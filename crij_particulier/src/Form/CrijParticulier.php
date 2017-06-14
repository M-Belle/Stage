<?php

namespace Drupal\crij_particulier\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\Core\Extension\ModuleHandler;

/**
 * Class CrijParticulier.
 *
 * @package Drupal\crij_particulier\Form
 */
class CrijParticulier extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'crij_particulier.crijparticulier',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'crij_particulier';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
	$form['nom'] = array(
		'#type' => 'textfield',
		'#title' => $this->t('Nom:'),
		'#attributes' => ['class' => ['uppercase']],
    );
	
	$form['prenom'] = array(
		'#type' => 'textfield',
		'#title' => $this->t('Prenom:'),
		'#attributes' => ['class' => ['uppercase']],
    );

	$form['mail'] = array(
		'#type' => 'email',
		'#title' => $this->t('E-mail:'),
		'#required' => TRUE,
	);
	$form['login'] = array(
		'#type' => 'textfield',
		'#title' => $this->t('Identifiant (e-mail ou login de votre choix):'),
		'#required' => TRUE,
	);
	
	$form['password'] = array(
		'#type' => 'password_confirm',
		'#description' => t('Entrer le même mot de passe dans les champs'),
		'#required' => TRUE,
	);
		
	$form['checkbox'] = array(
		'#type' => 'checkbox',
		'#title' => $this->t('En cochant cette case, je reconnais avoir pris connaissance et accepte les conditions d\'utilisation et de diffusion des offres.'),
		'#default_value' => 0,
	);
	
	$form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Valider'),
      '#button_type' => 'primary',
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state-> getValue('checkbox') != 1) {
		$form_state->setErrorByName('checkbox', t('Veuillez cocher les conditions d\'utilisation et de diffusion des offres.'));
	}
	
	$count = \Drupal::state()->get('data_login');
	if ($count > 0){
		$form_state->setErrorByName('login', t('Ce login est déjà utilisé'));
	}
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
	$mail = $form_state->getValue('mail');
	\Drupal::moduleHandler()->invoke('crij_particulier','checkmail', array($mail));
	
	$count = \Drupal::state()->get('data_mail');
	if($count > 0){
		$redirect_link = Link::fromTextAndUrl(t('cliquer ici'), Url::fromUri('http://localhost/Site/user/login'))->toString();
		drupal_set_message(t('Cette adresse mail est déjà utilisé, @link pour vous connecter', array('@link' => $redirect_link)), 'error');
	}else{
		$user = \Drupal\user\Entity\User::create();
		$user->set("field_statut", isset($content['statut']) ? $content['statut'] : 'Particulier');
		$user->set("field_nom", isset($content['nom']) ? $content['nom'] : $form_state->getValue('nom'));
		$user->set("field_prenom", isset($content['prenom']) ? $content['prenom'] : $form_state->getValue('prenom'));
		$user->setPassword($form_state->getValue('password'));
		$user->enforceIsNew();
		$user->setEmail($form_state->getValue('mail'));
		$user->setUsername($form_state->getValue('login'));
		$user->addRole('annonceur');
		
		$user->activate();
		
		$result = $user->save();
		
		drupal_set_message(t('Votre espace personnel a été créé'));
	}
  }

}
