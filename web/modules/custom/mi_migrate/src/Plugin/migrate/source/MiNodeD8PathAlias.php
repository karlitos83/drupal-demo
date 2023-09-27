<?php

namespace Drupal\mi_migrate\Plugin\migrate\source;

use Drupal\migrate\Row;

/**
 * Node entities with path alias from D8 database.
 *
 * @MigrateSource(
 *   id = "mi_node_d8_path_alias"
 * )
 */
class MiaxNodeD8PathAlias extends MiaxNodeD8 {

  /**
   * {@inheritdoc}
   *
   * @return array
   *   Array with alias field.
   */
  public function fields() {
    return ['alias' => $this->t('Path alias')] + parent::fields();
  }

  /**
   * {@inheritdoc}
   *
   * @return array
   *   Array of the prepared row with new alias field.
   */
  public function prepareRow(Row $row) {

    // Include path alias.
    $nid = $row->getSourceProperty('nid');
    $query = $this->select('url_alias', 'ua')
      ->fields('ua', ['alias']);
    $query->condition('ua.source', '/node/' . $nid);
    $alias = $query->execute()->fetchField();
    if (!empty($alias)) {
      $row->setSourceProperty('alias', $alias);
    }
    return parent::prepareRow($row);
  }

}
