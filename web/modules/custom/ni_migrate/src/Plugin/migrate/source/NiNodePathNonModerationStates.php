<?php

namespace Drupal\ni_migrate\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\node\Plugin\migrate\source\d7\Node;

/**
 * Node entities with path from the d7 database.
 *
 * @MigrateSource(
 *   id = "ni_node_path_non_moderation_states",
 *   source_module = "node"
 * )
 */
class NiNodePathNonModerationStates extends Node {

  /**
   * {@inheritdoc}
   *
   * @return array
   *   Array of new fields for the row.
   */
  public function fields() {
    $fields = ['moderation_state' => $this->t('Moderation state')] + ['alias' => $this->t('Path alias')];

    return $fields + parent::fields();
  }

  /**
   * {@inheritdoc}
   *
   * @return array
   *   prepared row with new alias and moderation state fields.
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
    // There is no moderation_state at source.
    // Assigned based on node status.
    if ($row->getSourceProperty('status') == TRUE) {
      $row->setSourceProperty('moderation_state', 'published');
    }
    else {
      $row->setSourceProperty('moderation_state', 'archive');
    }

    return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   *
   * @return mixed
   *   Altered query to exclude nodes not needed for the migration.
   */
  public function query() {
    // Nodes created as feeds using content default.
    $feeds = [
      231,
      456,
      461,
      407981,
      416596,
      409486,
      409461,
      409466,
      409471,
      409481,
    ];
    $query = parent::query();
    $query->condition('n.nid', $feeds, 'NOT IN');

    return $query;
  }

}
