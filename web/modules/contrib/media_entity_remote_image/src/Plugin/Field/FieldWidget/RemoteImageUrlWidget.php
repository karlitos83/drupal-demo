<?php

namespace Drupal\media_entity_remote_image\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\link\Plugin\Field\FieldWidget\LinkWidget;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Remote image URL field widget.
 *
 * @FieldWidget(
 *   id = "remote_image_url_widget",
 *   label = @Translation("Remote Image Url"),
 *   description = @Translation("A plaintext field for a remote image url plus fields for metadata."),
 *   field_types = {
 *     "remote_image_url"
 *   }
 * )
 */
class RemoteImageUrlWidget extends LinkWidget {

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  private $routeMatch;

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->routeMatch = $container->get('current_route_match');
    return $instance;
  }

  /**
   * {@inheritDoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    unset($element['title']);
    unset($element['attributes']);

    $element['alt'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Alt text'),
      // '#placeholder' => $this->getSetting('placeholder_title'),
      '#default_value' => $items[$delta]->alt ?? NULL,
      '#maxlength' => 255,
      // '#access' => $this->getFieldSetting('title') != DRUPAL_DISABLED,
      // '#required' => $field_settings['alt_field_required'] ?? 1,
      '#description' => $this->t('Short description of the image used by screen readers and displayed when the image is not loaded. This is important for accessibility.'),
      '#states' => [
        'required' => [
          ':input[name="settings[alt_field_required]"]' => ['checked' => TRUE],
        ],
        'optional' => [
          ':input[name="settings[alt_field_required]"]' => ['checked' => FALSE],
        ],
      ],
    ];

    if ($this->routeMatch->getRouteName() !== 'entity.field_config.media_field_edit_form') {
      $element['alt']['#required'] = $field_settings['alt_field_required'] ?? 1;
    }

    return $element;
  }

}
