<?php

namespace Drupal\ni_migrate\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\node\Plugin\migrate\source\d7\Node;

/**
 * Node entities with path from the d7 database.
 *
 * @MigrateSource(
 *   id = "ni_node_path_moderation_states",
 *   source_module = "node"
 * )
 */
class NiNodePathModerationStates extends Node {

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

    $query = $this->select('workbench_moderation_node_history', 'wmnh')
      ->fields('wmnh', ['nid', 'state']);
    $query->addExpression('MAX(hid)', 'hid');
    $query->condition('wmnh.nid', $nid);
    $query->groupBy('wmnh.nid')->groupBy('wmnh.state')->groupBy('wmnh.hid')->orderBy('wmnh.hid', 'DESC');
    $moderationStateQuery = $query->execute();
    $moderationState = $moderationStateQuery->fetchField(1);

    if (!empty($alias)) {
      $row->setSourceProperty('alias', '/' . $alias);
    }

    if (!empty($moderationState)) {
      $row->setSourceProperty('moderation_state', $moderationState);
    }

    return parent::prepareRow($row);
  }

}
