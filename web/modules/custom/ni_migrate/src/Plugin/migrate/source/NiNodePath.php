<?php

namespace Drupal\ni_migrate\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\node\Plugin\migrate\source\d7\Node;

/**
 * Node entities with path from the d7 database.
 *
 * @MigrateSource(
 *   id = "ni_node_path",
 *   source_module = "node"
 * )
 */
class NiNodePath extends Node {

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
    $query->condition('ua.source', 'node/' . $nid);
    $alias = $query->execute()->fetchField();
    if (!empty($alias)) {
      $row->setSourceProperty('alias', '/' . $alias);
    }
    return parent::prepareRow($row);
  }

}
