<?php

namespace Drupal\multistep\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Class MultistepFormOne.
 *
 * @package Drupal\multistep\Form
 */
class MultistepFormOne extends MultistepFormBase {

  /**
   * {@inheritdoc}
   */
  /*protected function getEditableConfigNames() {
    return [
      'multistep.multistepformone',
    ];
  }*/

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'multistep_form_one';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
	
	$form['nom'] = array(
		'#type' => 'textfield',
		'#title' => $this->t('Nom:'),
		'#default_value' => $this->store->get('nom') ? $this->store->get('nom') : '',
    );
	
	$form['prenom'] = array(
		'#type' => 'textfield',
		'#title' => $this->t('Prenom:'),
		'#default_value' => $this->store->get('prenom') ? $this->store->get('prenom') : '',
    );

	$form['mail'] = array(
		'#type' => 'email',
		'#title' => $this->t('E-mail:'),
		'#default_value' => $this->store->get('mail') ? $this->store->get('mail') : '',
	);
	
    $form['actions']['submit']['#value'] = $this->t('Suivant');
    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->store->set('nom','prenom','mail', $form_state->getValue('nom','prenom','mail'));
    $form_state->setRedirect('multistep.multistep_form_two');
  }
}
