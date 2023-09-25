<?php

namespace Drupal\ni_migrate\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\node\Plugin\migrate\source\d7\Node;

/**
 * Node entities with path from the d7 database.
 *
 * @MigrateSource(
 *   id = "ni_migrate_training_settings",
 *   source_module = "node"
 * )
 */
class NiMigrateTrainingSettings extends Node {

  /**
   * {@inheritdoc}
   *
   * @return array
   *   New fields values from custom queries.
   */
  public function fields() {

    $fields = [
      'moderation_state' => $this->t('Moderation state'),
    ] + [
      'alias' => $this->t('Path alias'),
    ] + [
      'capacity' => $this->t('Capacity'),
    ] + [
      'capacity_online' => $this->t('Capacity online'),
    ] + [
      'open' => $this->t('Open'),
    ] + [
      'close' => $this->t('Close'),
    ] + [
      'maximum_spaces' => $this->t('Maximum spaces'),
    ] + [
      'multiple_registrations' => $this->t('Multiple registrations'),
    ] + [
      'from_address' => $this->t('From address'),
    ] + [
      'confirmation' => $this->t('Confirmation'),
    ] + [
      'confirmation_redirect' => $this->t('Confirmation redirect'),
    ] + [
      'status' => $this->t('Enable in person'),
    ] + [
      'enable_online' => $this->t('Enable online'),
    ] + [
      'registration_waitlist_enable' => $this->t('Registration waitlist enable'),
    ] + [
      'registration_waitlist_capacity' => $this->t('Registration waitlist capacity'),
    ] + [
      'registration_waitlist_message_enable' => $this->t('Registration waitlist message enable'),
    ] + [
      'registration_waitlist_message' => $this->t('Registration waitlist message'),
    ];

    return $fields + parent::fields();
  }

  /**
   * {@inheritdoc}
   *
   * @return array
   *   Array of the new row.
   */
  public function prepareRow(Row $row) {

    $nid = $row->getSourceProperty('nid');

    // Include path alias.
    $query = $this->select('url_alias', 'ua')
      ->fields('ua', ['alias']);
    $query->condition('ua.source', 'node/' . $nid);
    $alias = $query->execute()->fetchField();

    $query = $this->select('registration_entity', 're')
      ->fields('re', [
        'entity_id',
        'capacity',
        'open',
        'close',
        'settings',
        'status',
      ]);

    $query->condition('re.entity_id', $nid);
    $registrationEntityFields = $query->execute();

    $fields = $registrationEntityFields->fetchAssoc();
    $settings = unserialize($fields['settings']);

    $query = $this->select('workbench_moderation_node_history', 'wmnh')
      ->fields('wmnh', ['nid', 'state']);
    $query->addExpression('MAX(hid)', 'hid');
    $query->condition('wmnh.nid', $nid);
    $query->groupBy('wmnh.nid')->groupBy('wmnh.state')->groupBy('wmnh.hid')->orderBy('wmnh.hid', 'DESC');
    $moderationStateQuery = $query->execute();
    $moderationState = $moderationStateQuery->fetchField(1);

    if (!empty($moderationState)) {
      $row->setSourceProperty('moderation_state', $moderationState);
    }

    if (!empty($fields)) {
      foreach ($fields as $field => $value) {
        if ($field == 'open' && $value === NULL) {
          $createdDate = $row->getSourceProperty('created');
          $openRegistrationDate = \Drupal::service('date.formatter')->format($createdDate, 'U', 'Y-m-d H:i:s');
          $row->setSourceProperty($field, $openRegistrationDate);
        }
        else {
          $row->setSourceProperty($field, $value);
        }
      }
    }

    if (!empty($settings)) {
      foreach ($settings as $field => $value) {
        $row->setSourceProperty($field, $value);
      }
    }

    if (!empty($alias)) {
      $row->setSourceProperty('alias', '/' . $alias);
    }

    return parent::prepareRow($row);

  }

}
