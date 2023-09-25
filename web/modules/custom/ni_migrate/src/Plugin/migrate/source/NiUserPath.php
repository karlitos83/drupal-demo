<?php

namespace Drupal\ni_migrate\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\user\Plugin\migrate\source\d7\User;

/**
 * User entities with Path from the d7 database.
 *
 * @MigrateSource(
 *   id = "ni_user_path",
 *   source_module = "user"
 * )
 */
class NiUserPath extends User {

  /**
   * {@inheritdoc}
   *
   * @return array
   *   array with new added alias field to existing fields.
   */
  public function fields() {
    return ['alias' => $this->t('Path alias')] + parent::fields();
  }

  /**
   * {@inheritdoc}
   *
   * @return array
   *   Prepared row with alias field.
   */
  public function prepareRow(Row $row) {
    // Include path alias.
    $uid = $row->getSourceProperty('uid');
    $query = $this->select('url_alias', 'ua')
      ->fields('ua', ['alias']);
    $query->condition('ua.source', 'user/' . $uid);
    $alias = $query->execute()->fetchField();
    if (!empty($alias)) {
      $row->setSourceProperty('alias', '/' . $alias);
    }
    return parent::prepareRow($row);
  }

}
