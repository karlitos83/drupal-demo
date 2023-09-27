<?php

namespace Drupal\memory_game\EventSubscriber;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\core_event_dispatcher\Event\Entity\EntityInsertEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;


class WebformSettings implements EventSubscriberInterface {

  /**
   * The entity_type storage.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   -Entity Type Manager.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      EntityHookEvents::ENTITY_INSERT => 'onInsert',
    ];
  }

  /**
   * Method to save the game information in drupal memory.
   */
  public function onInsert(EntityInsertEvent $event) {
    /** @var \Drupal\webform\WebformInterface $webform */
    $webformSubmission = $event->getEntity();
    $entityTypeId = $webformSubmission->getEntityTypeId();

    if ($entityTypeId != 'webform_submission') {
      return;
    }

    $webformSubmissionId = $webformSubmission->id();

    /** @var \Drupal\webform\WebformSubmissionInterface $webformSubmissionEntity */
    $webformSubmissionEntity = $this->getWebformSubmissionEntity($webformSubmissionId);

    if (!empty($webformSubmissionEntity->get('uuid')->value)) {
      $webformSubmissionUuid = $webformSubmissionEntity->get('uuid')->value;
    }

    if (!empty($webformSubmissionEntity->get('webform_id')->target_id)) {
      $webformId = $webformSubmissionEntity->get('webform_id')->target_id;
    }

    $webformEntity = $this->getWebformEntity($webformId);

    if ($webformEntity) {
      $gameSettings = $webformEntity->get('third_party_settings');

      if ($gameSettings) {
        if ($gameSettings['forms']['game_settings']['mode'] == 'interactive') {
          if ($gameSettings['forms']['game_settings']['interactive_game'] == 'memory') {

            /** @var \Drupal\memory_game\GeneratePositions $service */
            $service = \Drupal::service('memory_game.generate_positions');
  
            $service->setGameSettings($gameSettings, $webformSubmissionId, $webformSubmissionUuid, $webformId);
            $service->generatePositions($gameSettings);
            $memoryGameSettings = $service->getGameSettings();
  
            // alter data in the webform_submission
            $this->alterDataWebformSubmission($webformSubmissionEntity, $memoryGameSettings);
  
            // redirect to webform memory.
            (new RedirectResponse("/dinamica/memorama"))->send();
            exit();
          }
        }
      }
    }
  }

  /**
   * Method to alter webform_submission data.
   */
  private function alterDataWebformSubmission($webformSubmissionEntity, $memoryGameSettings): void {

    $webformSubmissionData = $webformSubmissionEntity->getData();

    // need ask for more fields.
    $webformSubmissionData['game_status'] = 'no_winner';
    $webformSubmissionData['game_max_time'] = $memoryGameSettings['game_max_time'];
    $webformSubmissionData['game_type'] = $memoryGameSettings['game_type'];
    $webformSubmissionData['game_time'] = $memoryGameSettings['game_time'];
    $webformSubmissionData['game_completed'] = 0;

    $webformSubmissionEntity->setData($webformSubmissionData);
    $webformSubmissionEntity->save();
  }

  /**
   * Method to get the webform entity.
   */
  private function getWebformEntity($webformId): ?\Drupal\Core\Entity\EntityInterface
  {
    return $this->entityTypeManager->getStorage('webform')->load($webformId);
  }

  /**
   * Method to get the webform_submission entity.
   */
  private function getWebformSubmissionEntity($webformSubmissionId): ?\Drupal\Core\Entity\EntityInterface
  {
    return $this->entityTypeManager->getStorage('webform_submission')->load($webformSubmissionId);
  }
}
