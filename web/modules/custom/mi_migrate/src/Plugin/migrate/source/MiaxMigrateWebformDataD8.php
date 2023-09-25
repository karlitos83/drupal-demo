<?php

namespace Drupal\miax_migrate\Plugin\migrate\source;

use Drupal\migrate\Row;

/**
 * Get data fields from the d8 database.
 *
 * @MigrateSource(
 *   id = "miax_migrate_webform_data_d8"
 * )
 */
class MiaxMigrateWebformDataD8 extends MiaxMigrateWebformD8 {

  /**
   * {@inheritdoc}
   *
   * @return array
   *   Array with webform submission data.
   */
  public function fields() {
    return ['sid' => $this->t('sid'), 'date' => $this->t('date'), 'webform_data' => $this->t('webform_data')] + parent::fields();
  }

  /**
   * {@inheritdoc}
   *
   * @return array
   *   Array of the prepared row with webform submission data.
   */
  public function prepareRow(Row $row) {
    $submittedData = []; // Array for webform_data migration element.

    $connection = \Drupal::database();
    $query = $connection->select('webform_submission', 'ws');
    $query->addExpression('COUNT(sid)', 'sid');
    $items = $query->execute()->fetchField();
    // Create a fake sid to get a migration items counter.
    if ($items) {
      $sid = $items + 1;
    }
    else {
      $sid = 1;
    }
    $row->setSourceProperty('sid', $sid);
    // Change creation date to timestamp.
    $date = $row->getSourceProperty('date');
    $created = strtotime($date);
    $row->setSourceProperty('date', $created);

    $email = $row->getSourceProperty('email');
    $submittedData['email'] = $email;

    $userData = $this->getSubscriptionUserData($email);
    $submittedData['first_name'] = $userData->first_name;
    $submittedData['last_name'] = $userData->last_name;
    $submittedData['company_name'] = $userData->company_name;

    $subscriptionOptions = $this->getSubscriptionOptions($email);
    foreach ($subscriptionOptions as $option) {
      $miaxSubscription = $this->assignSubscriptionOptionsToSubmit($option);
      if ($miaxSubscription) {
        $submittedData[$miaxSubscription][] = $option;
      }
    }

    $row->setSourceProperty('webform_data', $submittedData);

    return parent::prepareRow($row);
  }

  /**
   * Get user subscriber data.
   *
   * @return mixed
   *   Object with the user subscribed data.
   *   Array empty array when user data not found.
   */
  public function getSubscriptionUserData(string $email) {
    /** @var \Drupal\miax_subscription\ConstantContact\Services\ContactService $contactService */
    $contactService = \Drupal::service('miax_subscription.contact.service');
    // Apply a delay of one second to avoid more than 4 request peer second.
    sleep(1);
    if ($contactService->contactEmailExists($email)) {
      return $contactService->getContactByEmail($email);
    }
    return [];
  }

  /**
   * Use contact service to get Subscription options.
   *
   * @return array
   *   Array with the subscription options.
   */
  public function getSubscriptionOptions(string $email) {
    /** @var \Drupal\miax_subscription\ConstantContact\Services\ContactService $contactService */
    $contactService = \Drupal::service('miax_subscription.contact.service');
    // Apply a delay of one second to avoid more than 4 request peer second.
    sleep(1);
    if ($contactService->contactEmailExists($email)) {
      return $contactService->getArrayListsIdsByEmailContact($email);
    }
    return [];
  }

  /**
   * Assign value to array key when subscription options match.
   *
   * @return array
   *   Array of option that match with the subscription options.
   */
  private function assignSubscriptionOptionsToSubmit(string $option) {

    $options = [
      "1917833164" => 'miax_pearl_options', //MIAX PEARL Options Trading Alerts
      "1815171307" => 'miax_pearl_options', //MIAX PEARL Options Technical Alerts
      "1480556594" => 'miax_pearl_options', //MIAX PEARL Options Regulatory Alerts
      "1996168573" => 'miax_pearl_options', //MIAX PEARL Options News Alerts
      "2040542947" => 'miax_pearl_options', //MIAX PEARL Options Fee Change Alerts
      "1250708629" => 'miax_pearl_options', //MIAX PEARL Options Listing Alerts
      "1170203629" => 'miax_emerald', //MIAX Emerald Options Listing Alerts
      "1540883187" => 'miax_emerald', //MIAX Emerald Options Fee Change Alerts
      "1913305393" => 'miax_emerald', //MIAX Emerald Options Regulatory Alerts
      "1805344008" => 'miax_emerald', //MIAX Emerald Options News Alerts
      "1772663819" => 'miax_emerald', //MIAX Emerald Options Technical Alerts
      "1045051221" => 'miax_emerald', //MIAX Emerald Options Trading Alerts
      "1701936956" => 'miax_options', //MIAX Options Product Alerts
      "2" => 'miax_options', //MIAX Options Trading Alerts
      "3" => 'miax_options', //MIAX Options Regulatory Alerts
      "4" => 'miax_options', //MIAX Options Technical Alerts
      "5" => 'miax_options', //MIAX Options Listing Alerts
      "2095483568" => 'miax_options', //MIAX Options News Alerts
      "1683747673" => 'miax_options', //MIAX Options Fee Change Alerts
      "1955080632" => 'miax_pearl_equities', //MIAX PEARL Equities Fee Change Alerts
      "1119087600" => 'miax_pearl_equities', //MIAX PEARL Equities Listing Alerts
      "1803901185" => 'miax_pearl_equities', //MIAX PEARL Equities News Alerts
      "1950843516" => 'miax_pearl_equities', //MIAX PEARL Equities Regulatory Alerts
      "1682757790" => 'miax_pearl_equities', //MIAX PEARL Equities Technical Alerts
      "1532975774" => 'miax_pearl_equities', //MIAX PEARL Equities Trading Alerts
    ];

    if (array_key_exists($option, $options)) {
      return $options[$option];
    }

  }

}
