<?php

namespace Drupal\miax_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;

/**
 * Get Node fields from the d8 database.
 *
 * @MigrateSource(
 *   id = "miax_users"
 * )
 */
class MiaxUsers extends SqlBase {

  /**
   * {@inheritdoc}
   *
   * @return mixed
   *   Query to select D8 node_field_data fields.
   */
  public function query() {

    $uid = [
      0,
      1,
    ];

    $query = $this->select('users_field_data', 'ufd');
    $query->fields('ufd', [
      'uid',
      'langcode',
      'name',
      'pass',
      'mail',
      'timezone',
      'status',
      'created',
      'changed',
      'access',
      'login',
      'init',
    ]);
    $query->condition('ufd.uid', $uid, 'NOT IN');
    $query->leftJoin('user__roles', 'ur', 'ufd.uid = ur.entity_id',);
    $query->fields('ur', [
      'roles_target_id',
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
      'uid' => $this->t('uid'),
      'langcode' => $this->t('langcode'),
      'name' => $this->t('name'),
      'pass' => $this->t('pass'),
      'mail' => $this->t('mail'),
      'timezone' => $this->t('timezone'),
      'status' => $this->t('status'),
      'created' => $this->t('created'),
      'changed' => $this->t('changed'),
      'access' => $this->t('access'),
      'login'  => $this->t('login'),
      'init' => $this->t('init'),
      'roles_target_id' => $this->t('roles_target_id'),
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'uid' => [
        'type' => 'integer',
        'alias' => 'uid',
      ],
    ];
  }

}
