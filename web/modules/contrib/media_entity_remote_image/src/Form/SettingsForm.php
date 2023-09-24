<?php

namespace Drupal\media_entity_remote_image\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Media entity remote image settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'media_entity_remote_image_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['media_entity_remote_image.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['generate_thumbnails'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Generate Thumbnail Previews'),
      '#description' => $this->t('Whether thumbnails should be generated for the media listing page when media entities are saved. This may have licensing implications depending on your use case.'),
      '#default_value' => $this->config('media_entity_remote_image.settings')
        ->get('generate_thumbnails') ?? FALSE,
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('media_entity_remote_image.settings')
      ->set('generate_thumbnails', $form_state->getValue('generate_thumbnails'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
