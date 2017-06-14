<?php

namespace Drupal\crij_professionnel\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class CrijProfessionnel.
 *
 * @package Drupal\crij_professionnel\Form
 */
class CrijProfessionnel extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'crij_professionnel.crijprofessionnel',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'crij_professionnel';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
		$form['type_structure'] = array(
			'#type' => 'select',
			'#title' => $this->t('Type de structure:'),
			'#options' => [ 'Entreprise privée' => $this->t('Entreprise privée'), 'Association' => $this->t('Association'), 'Collectivité ou établissement public' => $this->t('Collectivité ou établissement public'), 'Agence intérimaire' => $this->t('Agence intérimaire'), 'Résidence étudiante, régie et agence immobilière' => $this->t('Résidence étudiante, régie et agence immobilière'),
			],
			'#empty_value' => '',
			'#empty_option' => t('-- Selectionner type de structure --'),
			'#attributes' => ['class' => ['uppercase']],
			'#required' => TRUE,
		);
		
		$form['nom_structure'] = array(
			'#type' => 'textfield',
			'#title' => $this->t('NOM structure:'),
			'#attributes' => ['class' => ['uppercase']],
			'#required' => TRUE,
		);
		
		$form['adresse'] = array(
			'#type' => 'textfield',
			'#title' => $this->t('Adresse:'),
			'#attributes' => ['class' => ['uppercase']],
			'#required' => TRUE,
		);
		
		$form['ville'] = array(
			'#type' => 'textfield',
			'#title' => $this->t('Ville:'),
			'#autocomplete_route_name' => 'crij_professionnel.autocomplete_cities',
			'#required' => TRUE,
		);
		
		$form['telephone'] = array(
			'#type' => 'tel',
			'#title' => t('Telephone:'),
			'#maxlength' => 10,
		);
	
		$form['site_web'] = array(
			'#type' => 'textfield',
			'#title' => t('Site web: http://'),
		);
	
		$form['mail'] = array(
			'#type' => 'email',
			'#title' => t('E-mail:'),
			'#required' => TRUE,
		);
	
		$form['login'] = array(
			'#type' => 'textfield',
			'#title' => $this->t('Identifiant (e-mail ou login de votre choix):'),
			'#required' => TRUE,
		);
	
		$form['password'] = array(
			'#type' => 'password_confirm',
			'#description' => t('Enter the same password in both fields'),
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
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $user = \Drupal\user\Entity\User::create();
		$user->set("field_statut", isset($content['statut']) ? $content['statut'] : 'Professionnel');
		$user->set("field_type_structure", isset($content['type_structure']) ? $content['type_structure'] : $form_state->getValue('type_structure'));
		$user->set("field_nom_structure", isset($content['nom_structure']) ? $content['nom_structure'] : $form_state->getValue('nom_structure'));
		$user->set("field_adresse", isset($content['adresse']) ? $content['adresse'] : $form_state->getValue('adresse'));
		$user->set("field_ville", isset($content['ville']) ? $content['ville'] : $form_state->getValue('ville'));
		$user->set("field_telephone", isset($content['telephone']) ? $content['telephone'] : $form_state->getValue('telephone'));
		$user->set("field_site", isset($content['site_web']) ? $content['site_web'] : $form_state->getValue('site_web'));
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
