<?php

namespace Drupal\ni_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;

/**
 * Registration email log migration from the d7 database.
 *
 * @MigrateSource(
 *   id = "ni_migrate_registrations"
 * )
 */
class NiMigrateRegistrations extends SqlBase {

  /**
   * {@inheritdoc}
   *
   * @return mixed
   *   New query to select from d7.registration table.
   */
  public function query() {

    $firstNameSubquery = $this->select('field_data_field_first_name', 'fdffn');
    $firstNameSubquery->fields('fdffn', ['entity_id', 'field_first_name_value']);
    $firstNameSubquery->condition('fdffn.bundle', 'registration');

    $lastNameSubquery = $this->select('field_data_field_last_name', 'fdfln');
    $lastNameSubquery->fields('fdfln', ['entity_id', 'field_last_name_value']);
    $lastNameSubquery->condition('fdfln.bundle', 'registration');

    $alphanumericSubquery = $this->select('field_data_field_serial_alphanumeric', 'fdfsa');
    $alphanumericSubquery->fields('fdfsa',
    [
      'entity_id',
      'field_serial_alphanumeric_value',
    ]);
    $alphanumericSubquery->condition('fdfsa.bundle', 'registration');

    $phoneSubquery = $this->select('field_data_field_phone', 'fdfph');
    $phoneSubquery->fields('fdfph', ['entity_id', 'field_phone_value']);
    $phoneSubquery->condition('fdfph.bundle', 'registration');

    $methodSubquery = $this->select('field_data_field_class_method', 'fdfcm');
    $methodSubquery->fields('fdfcm', ['entity_id', 'field_class_method_value']);
    $methodSubquery->condition('fdfcm.bundle', 'registration');

    $organizationSubquery = $this->select('field_data_field_organization', 'fdfo');
    $organizationSubquery->fields('fdfo',
    [
      'entity_id',
      'field_organization_value',
    ]);
    $organizationSubquery->condition('fdfo.bundle', 'registration');

    $positionSubquery = $this->select('field_data_field_position', 'fdfp');
    $positionSubquery->fields('fdfp', ['entity_id', 'field_position_value']);
    $positionSubquery->condition('fdfp.bundle', 'registration');

    // Source data is queried from 'registration' table.
    $query = $this->select('registration', 'r');
    $query->leftJoin($firstNameSubquery, 'fdffn', 'r.registration_id = fdffn.entity_id');
    $query->leftJoin($lastNameSubquery, 'fdfln', 'r.registration_id = fdfln.entity_id');
    $query->leftJoin($alphanumericSubquery, 'fdfsa', 'r.registration_id = fdfsa.entity_id');
    $query->leftJoin($phoneSubquery, 'fdfph', 'r.registration_id = fdfph.entity_id');
    $query->leftJoin($methodSubquery, 'fdfcm', 'r.registration_id = fdfcm.entity_id');
    $query->leftJoin($organizationSubquery, 'fdfo', 'r.registration_id = fdfo.entity_id');
    $query->leftJoin($positionSubquery, 'fdfp', 'r.registration_id = fdfp.entity_id');

    $query
      ->fields('r', [
        'registration_id',
        'entity_id',
        'author_uid',
        'anon_mail',
        'count',
        'state',
        'created',
        'updated',
      ]);
    $query
      ->fields('fdffn', ['field_first_name_value']);
    $query
      ->fields('fdfln', ['field_last_name_value']);
    $query
      ->fields('fdfsa', ['field_serial_alphanumeric_value']);
    $query
      ->fields('fdfph', ['field_phone_value']);
    $query
      ->fields('fdfcm', ['field_class_method_value']);
    $query
      ->fields('fdfo', ['field_organization_value']);
    $query
      ->fields('fdfp', ['field_position_value']);

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
      'registration_id'    => $this->t('registration_id'),
      'entity_id'          => $this->t('entity_id'),
      'author_uid'          => $this->t('author_uid'),
      'anon_mail'          => $this->t('anon_mail'),
      // Spaces.
      'count'              => $this->t('count'),
      'state'              => $this->t('state'),
      'created'            => $this->t('created'),
      'updated'            => $this->t('updated'),
      // Fields from Joins.
      'field_first_name_value' => $this->t('field_first_name_value'),
      'field_last_name_value' => $this->t('field_last_name_value'),
      'field_class_method_value' => $this->t('field_class_method_value'),
      'field_organization_value' => $this->t('field_organization_value'),
      'field_position_value' => $this->t('field_position_value'),
      'field_phone_value' => $this->t('field_phone_value'),
      'field_serial_alphanumeric_value' => $this->t('field_serial_alphanumeric_value'),

    ];
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'registration_id' => [
        'type' => 'integer',
        'alias' => 'r',
      ],
    ];
  }

}
