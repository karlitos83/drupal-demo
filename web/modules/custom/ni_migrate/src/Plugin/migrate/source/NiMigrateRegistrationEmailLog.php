<?php

namespace Drupal\ni_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;

/**
 * Registration email log migration from the d7 database.
 *
 * @MigrateSource(
 *   id = "ni_migrate_registration_email_log"
 * )
 */
class NiMigrateRegistrationEmailLog extends SqlBase {

  /**
   * {@inheritdoc}
   *
   * @return mixed
   *   New query to select from ni_registration_email_log table.
   */
  public function query() {
    // Source data is queried from 'ni_registration_email_log' table.
    $query = $this->select('ni_registration_email_log', 'nrel')
      ->fields('nrel', [
        'log_id',
        'nid',
        'date_sent',
        'scheduling_type',
        'subject',
        'num_of_recipients',
        'recipients',
        'sender',
      ]);
    return $query;
  }

  /**
   * {@inheritdoc}
   *
   * @return array
   *   Query fields array.
   */
  public function fields() {
    $fields = [
      'log_id'             => $this->t('log_id'),
      'nid'                => $this->t('nid'),
      'date_sent'          => $this->t('date_sent'),
      'scheduling_type'    => $this->t('scheduling_type'),
      'subject'            => $this->t('subject'),
      'num_of_recipients'  => $this->t('num_of_recipients'),
      'recipients'         => $this->t('recipients'),
      'sender'             => $this->t('sender'),
    ];
    return $fields;
  }

  /**
   * {@inheritdoc}
   *
   * @return array
   *   Array of Id field.
   */
  public function getIds() {
    return [
      'log_id' => [
        'type' => 'integer',
        'alias' => 'nrel',
      ],
    ];
  }

}
