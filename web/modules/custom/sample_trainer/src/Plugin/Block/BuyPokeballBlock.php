<?php

namespace Drupal\sample_trainer\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\sample_trainer\Form\BuyPokeballForm;

/**
 * Provides a 'BuyPokeballBlock' block.
 *
 * @Block(
 *  id = "buy_pokeball_block",
 *  admin_label = @Translation("Buy Pokeball"),
 * )
 */
class BuyPokeballBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\sample_trainer\Form\BuyPokeballForm');

    return [
      '#markup' => \Drupal::service('renderer')->render($form),
      '#cache' => ['max-age' => 0],
    ];

  }

}
