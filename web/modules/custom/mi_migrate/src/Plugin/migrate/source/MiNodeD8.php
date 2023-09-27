<?php

namespace Drupal\mi_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;

/**
 * Get Node fields from the d8 database.
 *
 * @MigrateSource(
 *   id = "mi_node_d8"
 * )
 */
class MiaxNodeD8 extends SqlBase {

  /**
   * {@inheritdoc}
   *
   * @return mixed
   *   Query to select D8 node_field_data fields.
   */
  public function query() {
    // Content type and fields array.
    $contentType = $this->configuration['content_type'];
    $fieldName = $this->configuration['field_name'];
    $query = $this->select('node_field_data', 'nfd');
    $query->fields('nfd', [
      'nid',
      'vid',
      'type',
      'langcode',
      'title',
      'uid',
      'status',
      'created',
      'changed',
      'promote',
      'sticky',
      'revision_translation_affected',
      'default_langcode',
    ]);
    $query->fields('nb', [
      'body_value',
      'body_summary',
      'body_format',
    ]);
    $query->leftJoin('node__body', 'nb', 'nfd.nid = nb.entity_id');

    foreach ($fieldName as $fieldInfo) {
      $arrayField = explode('__', $fieldInfo);
      $tableName = $arrayField[0] . '__' . $arrayField[1];
      $field = $arrayField[1] . '_' . $arrayField[2];
      $query->leftJoin($tableName, $tableName, 'nfd.nid = ' . $tableName . '.entity_id');
      $query->fields($tableName, [$field]);
    }

    $query->condition('nfd.type', $contentType, '=');

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
      'nid' => $this->t('nid'),
      'vid' => $this->t('vid'),
      'type' => $this->t('type'),
      'langcode' => $this->t('langcode'),
      'title' => $this->t('title'),
      'uid' => $this->t('uid'),
      'status' => $this->t('status'),
      'created' => $this->t('created'),
      'changed' => $this->t('changed'),
      'promote' => $this->t('promote'),
      'sticky'  => $this->t('sticky'),
      'revision_translation_affected' => $this->t('revision_translation_affected'),
      'default_langcode' => $this->t('default_langcode'),
      'body_value' => $this->t('body_value'),
      'body_summary' => $this->t('body_summary'),
      'body_format' => $this->t('body_format'),
    ];

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'nid' => [
        'type' => 'integer',
        'alias' => 'nid',
      ],
    ];
  }

}
