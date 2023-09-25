<?php

namespace Drupal\nihl_migrate;

use Drupal\Core\Database\Database;
use Drupal\migrate\Row;
use Drupal\migrate_media_handler\MediaMaker as MediaMakerBase;

/**
 * MediaMaker class extends MediaMaker class of migrate_media_maker.
 */
class MediaMaker extends MediaMakerBase {

  /**
   * Assign file_id to 'mid' to the media object creation.
   *
   * @return bool
   *   Return False if make Image Entity fails or there is not a image
   *   field to create media.
   */
  public function makeImageEntity(int $file_id, Row $row, array $configuration) {
    // $media = parent::mediaImageEntity($file_id, $row, $configuration);
    $media = FALSE;
    // Load file entity.
    $file = $this->entityManager->getStorage('file')->load($file_id);
    // If that's successful, carry on.
    if ($file) {
      // Hash file for overlap lookup.
      $hash = sha1_file($file->getFileUri());

      // Lookup media entity by file hash.
      $media = $this->findExistingMediaByHash($hash);

      // If it wasn't found, make it!
      if (!$media) {
        $field_source = $row->getSourceProperty($configuration['source_field']);
        $database = Database::getConnection('default', 'migrate');
        $query = $database->select('field_data_field_file_image_alt_text', 'fdffiat');
        $query->fields('fdffiat', ['field_file_image_alt_text_value']);
        $query->condition('fdffiat.entity_id', $file_id, '=');
        $alt = $query->execute()->fetchField();

        $query = $database->select('field_data_field_file_image_title_text', 'fdffitt');
        $query->fields('fdffitt', ['field_file_image_title_text_value']);
        $query->condition('fdffitt.entity_id', $file_id, '=');
        $title = $query->execute()->fetchField();
        // Create media entity with saved file.
        // Please note accessibility concerns around empty alt & title.
        $media = $this->entityManager->getStorage('media')->create([
          'mid' => $file_id,
          'bundle' => 'image',
          'field_original_ref' => $hash,
          $this->config->get('image_field_name') => [
            'target_id' => $file_id,
            'alt' => $alt,
            'title' => $title,
          ],
          'langcode' => 'en',
        ]);

        $owner = $file->getOwnerId();
        $filename = $file->getFilename();
        $media->setOwnerId($owner);
        ($title) ? $media->setName($title) : $media->setName($filename);

        $media->save();
      }
    }
    return $media;
  }

  /**
   * Make a document media entity out of a file.
   *
   * @return bool
   *   Return False if make Document Entity fails or there is not
   *   a field to create media.
   */
  public function makeDocumentEntity($file_id, $hash = '', $display = '', $description = '') {
    $media = FALSE;
    // Load file entity.
    $file = $this->entityManager->getStorage('file')->load($file_id);
    // If that's successful, carry on.
    if ($file) {
      // If no file hash was specified, hash it.
      if (!$hash) {
        $hash = sha1_file($file->getFileUri());
      }
      // Lookup media entity by file hash.
      $media = $this->findExistingMediaByHash($hash);

      // If it wasn't found, make it!
      if (!$media) {
        // Create media entity with saved file.
        $media = $this->entityManager->getStorage('media')->create([
          'mid' => $file_id,
          'bundle' => 'document',
          'field_original_ref' => $hash,
          $this->config->get('document_field_name') => [
            'target_id' => $file_id,
            'display' => $display,
            'description' => $description,
          ],
          'langcode' => 'en',
        ]);
        // Get additional media properties.
        $owner = $file->getOwnerId();
        $filename = $file->getFilename();
        // Set additional media properties.
        $media->setOwnerId($owner);
        $media->setName($filename);

        $media->save();

      }
    }
    return $media;
  }

}
