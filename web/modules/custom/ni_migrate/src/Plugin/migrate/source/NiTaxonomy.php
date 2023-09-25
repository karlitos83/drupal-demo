<?php

namespace Drupal\ni_migrate\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\taxonomy\Plugin\migrate\source\d7\Term;

/**
 * Provides Taxonomy entities with path from the d7 database.
 *
 * @MigrateSource(
 *  id = "ni_taxonomy",
 *  source_module = "taxonomy"
 * )
 */
class NiTaxonomy extends Term {

  /**
   * {@inheritdoc}
   *
   * @return array
   *   prepared row with alias field.
   */
  public function prepareRow(Row $row) {

    $tid = $row->getSourceProperty('tid');

    $query = $this->select('url_alias', 'ua')
      ->fields('ua', ['alias']);
    $query->condition('ua.source', 'taxonomy/term/' . $tid);
    $alias = $query->execute()->fetchField();

    if (!empty($alias)) {
      $row->setSourceProperty('alias', '/' . $alias);
    }

    return parent::prepareRow($row);
  }

  /**
   * {@inheritdoc}
   *
   * @return array
   *   array with newly added alias field to existing fields.
   */
  public function fields() {
    $fields = ['alias' => $this->t('alias')] + parent::fields();
    return $fields;
  }

}
