<?php

namespace Drupal\ni_migrate\Plugin\migrate\source;

use Drupal\node\Plugin\migrate\source\d7\Node;

/**
 * Publications node fields to paragraph from the d7 database.
 *
 * @MigrateSource(
 *   id = "ni_migrate_nodes_publications",
 *   source_module = "node"
 * )
 */
class NiMigrateNodesPublications extends Node {

  /**
   * {@inheritdoc}
   *
   * @return mixed
   *   Altered query to filter by publication types.
   */
  public function query() {

    $query = parent::query();
    // Publication type Ids from migration script parameters.
    $publicationType = $this->configuration['publication_types'];
    // Inner Join with field publication type table and Where condition.
    $query->innerJoin('field_data_field_publication_type', 'fdfpt', 'n.nid = fdfpt.entity_id');
    $query->condition('fdfpt.field_publication_type_tid', $publicationType, 'IN');

    return $query;
  }

}
