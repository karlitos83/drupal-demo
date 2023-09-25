<?php

namespace Drupal\miax_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;

/**
 * Get data fields from the d8 database.
 *
 * @MigrateSource(
 *   id = "miax_migrate_webform_d8"
 * )
 */
class MiaxMigrateWebformD8 extends SqlBase {

  /**
   * {@inheritdoc}
   *
   * @return mixed
   *   Query to select D8 subscription fields.
   */
  public function query() {
    // $sql = '(@n := @n +1) sid, msut.token, msut.email, msut.date FROM (select @n:=0) initvars, miax_subscription_update_tokens msut';

    $query = $this->select('miax_subscription_update_tokens', 'msut');
    $query->fields('msut', [
      'token',
      'email',
      'date',
    ]);
    $query->addExpression('@n := @n +1', 'sid');

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
      'token' => $this->t('token'),
      'email' => $this->t('email'),
      'date' => $this->t('date'),
      'sid' => $this->t('sid'),
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'sid' => [
        'type' => 'integer',
        'alias' => 'sid',
      ],
    ];
  }

}
