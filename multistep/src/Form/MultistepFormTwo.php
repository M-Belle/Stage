<?php

namespace Drupal\multistep\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

class MultistepFormTwo extends MultistepFormBase {

	/**
	* {@inheritdoc}.
	*/
	public function getFormId() {
		return 'multistep_form_two';
	}

	/**
	* {@inheritdoc}.
	*/
	public function buildForm(array $form, FormStateInterface $form_state) {

		$form = parent::buildForm($form, $form_state);
		
		$form['login'] = array(
			'#type' => 'textfield',
			'#title' => $this->t('Identifiant (e-mail ou login de votre choix):'),
			'#default_value' => $this->store->get('login') ? $this->store->get('login') : '',
		);
	
		$form['password'] = array(
			'#type' => 'password_confirm',
			'#description' => t('Enter the same password in both fields'),
		);
		
		$form['checkbox'] = array(
			'#type' => 'checkbox',
			'#title' => $this->t('En cochant cette case, je reconnais avoir pris connaissance et accepte les conditions d\'utilisation et de diffusion des offres.'),
			'#default_value' => 0,
		);
	
		$form['actions']['previous'] = array(
			'#type' => 'link',
			'#title' => $this->t('Retour'),
			'#attributes' => array(
			'class' => array('button'),
			),
			'#weight' => 0,
			'#url' => Url::fromRoute('multistep.multistep_form_one'),
		);

		return $form;
	}
	
	public function validateForm(array &$form, FormStateInterface $form_state){
	
		if ($form_state-> getValue('checkbox') != 1) {
			$form_state->setErrorByName('checkbox', t('Veuillez cocher les conditions d\'utilisation et de diffusion des offres.'));
		}
	}

	/**
	* {@inheritdoc}
	*/
	public function submitForm(array &$form, FormStateInterface $form_state) {
		$this->store->set('login', $form_state->getValue('login'));
		$this->store->set('password', $form_state->getValue('password'));

    // Save the data
		parent::saveData();
	}
}