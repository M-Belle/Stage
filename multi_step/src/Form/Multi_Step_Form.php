<?php

namespace Drupal\multi_step\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class DefaultForm.
 *
 * @package Drupal\multi_step\Form
 */
class Multi_Step_Form extends ConfigFormBase {


	protected $step = 1;
  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {}

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'default_form';
  }

  /**
   * {@inheritdoc}
   */
public function buildForm(array $form, FormStateInterface $form_state) {
	$form = parent::buildForm($form, $form_state);
	
	$config = $this->config('multi_step.multi_step_form_config');

	if($this->step == 1){
	
		$form['statut'] = array(
			'#type' => 'radios',
			'#title' => t('Statut'),
			'#options' => array(1 => $this->t('Particulier'), 2 => $this->t('Professionnel')),
			'#default_value' => $config->get('statut'),
			'#required' => TRUE,
		);
	}
	$statut = $form_state->getValue('statut');
	
	if($this->step == 2 && $statut == 1){
		
		$form['particulier'] = array(
			'#type' => 'fieldset',
			'#title' => t('Particulier'),
		);
		$form['particulier']['nom'] = array(
			'#type' => 'textfield',
			'#title' => t('Nom :'),
			'#default_value' => $config->get('nom'),
		);
		$form['particulier']['prenom'] = array(
			'#type' => 'textfield',
			'#title' => t('Prenom :'),
			'#default_value' => $config->get('prenom'),
		);
	
	}else if($this->step == 2 && $statut == 2){
	
		$form['professionnel'] = array(
			'#type' => 'fieldset',
			'#title' => t('Professionnel'),
		);
	
		$form['professionnel']['raison_sociale'] = array(
			'#type' => 'textfield',
			'#title' => t('Raison Sociale :'),
			'#default_value' => $config->get('raison_sociale'),
		);
	}
	
	if($this->step == 3){
		
		$form['email'] = array(
			'#type' => 'email',
			'#title' => t('Email :'),
			'#default_value' => $config->get('email'),
		);
	}
	
	if($this->step > 1){
		$form['back'] = array(
			'#type' => 'button',
			'#value' => 'Back',
			'#limit_validation_errors' => array(),
			'#attributes' => array(
			'onclick' => 'window.history.go(-1);return false;',        
			)	       
		);
	}
	
	if($this->step < 3) {
		$form['actions']['submit']['#value'] = $this->t('Suivant');
	}
	else {
		$form['actions']['submit']['#value'] = $this->t('Valider');
	}

return $form;
}

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    
	if($this->step < 3) {
		$form_state->setRebuild();
		$this->step++;
	}
	else {
		parent::submitForm($form, $form_state);
		
		$nom = $form_state->getValue('nom');
		$prenom = $form_state->getValue('prenom');
		$raison_sociale = $form_state->getValue('raison_sociale');
		$email = $form_state->getValue('email');
	
		$query = \Drupal::database()->insert('multi_step');
		$query->fields([
			'statut',
			'nom',
			'prenom',
			'raison_sociale',
			'email'
		]);
		$query->values([
			$statut,
			$nom,
			$prenom,
			$raison_sociale,
			$email,
		]);
	$query->execute();
	drupal_set_message(t('Votre formulaire a été enregistré'));
	}
  }
}
