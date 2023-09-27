<?php

namespace Drupal\sample_trainer\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use http\Message;

/**
 * Class BuyPokeballForm.
 */
class BuyPokeballForm extends FormBase
{

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'buy_pokeball_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $form['pokeballs_number'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Number of pokeballs'),
      '#maxlength' => 4,
      '#size' => 12,
      '#weight' => '0',
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Buy pokeball'),
      '#attributes' => ['class' => ['field--name-field-boton']]
    ];

    return $form;
  }

  /**
   * Valida código.
   */
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    $pokeball_number = $form_state->getValue('pokeballs_number');

    if ($pokeball_number) {
      $uid = \Drupal::currentUser()->id();
      $trainer_amount = \Drupal::service('sample_trainer.balance_service')
          ->getAmount($uid);
      $pokeballs_amount = (int)$pokeball_number * 50;
      if ($trainer_amount < $pokeballs_amount) {
        $form_state->setErrorByName('pokeballs_number', 'Not enough pokedollars.');
      }
      elseif (!is_int((int)$pokeball_number)) {
        $form_state->setErrorByName('pokeballs_number', 'Insert a numberic value.');
      }
    } else{
      $form_state->setErrorByName('pokeballs_number', 'Insert a pokeballs number.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $uid = \Drupal::currentUser()->id();
    $trainer = \Drupal\user\Entity\User::load($uid);
    $trainer_pokeballs= $trainer->get('field_pokeballs')->value;
    $new_pokeballs = $form_state->getValue('pokeballs_number');
    $pb = $trainer_pokeballs + $new_pokeballs;
    $trainer->set('field_pokeballs', $pb);
    $trainer->save();
    \Drupal::messenger()->addStatus("You have bought $new_pokeballs pokeballs.");
  }

}
