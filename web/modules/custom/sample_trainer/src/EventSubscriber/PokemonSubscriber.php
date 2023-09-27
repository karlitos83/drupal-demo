<?php

namespace Drupal\sample_trainer\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\webform\WebformSubmissionInterface;
use Drupal\core_event_dispatcher\Event\Entity\EntityInsertEvent;
use Drupal\hook_event_dispatcher\HookEventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\user\Entity\User;

/**
 * Class to generate user token automatically.
 */
class PokemonSubscriber implements EventSubscriberInterface {

  /**
   * The entity_type storage.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager - Entity Type Manager.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * Method to duplicate webform data in Spreadsheet
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityInsertEvent $event
   *
   * @return void
   */
  public function onInsert(EntityInsertEvent $event) {
    $webform = $event->getEntity();
    $entityTypeId = $webform->getEntityTypeId();

    if ($entityTypeId != 'webform_submission') {
      return;
    }

    $sid = $webform->id();

    /** @var \Drupal\webform\WebformSubmissionInterface $webform_entity */
    $webform_entity = $this->getWebformSubmissionEntity($sid);
    if (!empty($webform_entity->get('webform_id')->target_id)) {
      $webform_id = $webform_entity->get('webform_id')->target_id;
      if ($webform_id == 'pokemon_summons') {
          $data = $webform_entity->getData();
          $this->updatePokedex($data);
      }
    }
  }

  /**
   * Method to alter webform_submission data.
   */
  private function updatePokedex(array $data) {

    $trainer = \Drupal\user\Entity\User::load($data['user_id']);
    $pokedex = $trainer->get('field_pokedex');
    $target_ids = [];
    //checking if the pokemon is already in the pokedex
    $pokemon_exists = false;
    foreach ($pokedex as $pokemon) {
      $target_id = $pokemon->target_id;
      $target_ids[] = $target_id;
      if ($target_id == $data['pokemon_id']) {
        $pokemon_exists = true;
        break;
      }
    }

    if (!$pokemon_exists) {
      $target_ids[] = $data['pokemon_id'];
      $trainer->set('field_pokedex', $target_ids);
      $trainer->save();
    }

  }

  /**
   * Method to get the webform_submission entity.
   */
  private function getWebformSubmissionEntity($sid): ?\Drupal\Core\Entity\EntityInterface
  {
    return $this->entityTypeManager->getStorage('webform_submission')->load($sid);
  }

  /**
   * Subscribe events.
   */
  public static function getSubscribedEvents() {
    return [
      HookEventDispatcherInterface::ENTITY_INSERT => 'onInsert',
    ];
  }

}
