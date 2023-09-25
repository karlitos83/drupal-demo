<?php

namespace Drupal\ni_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Assign training start date if registration end date is empty.
 *
 * @MigrateProcessPlugin(
 *   id = "reg_end_date"
 * )
 */
class NiMigrateRegEndDate extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($v, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    $closeDate = $row->getSourceProperty('close');
    if (!$closeDate) {
      $trainingStartDate = $row->getSourceProperty('field_training_date');
      $closeDate = $trainingStartDate[0]['value'];
    }
    return $closeDate;
  }

}
