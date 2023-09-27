<?php

namespace Drupal\memory_game\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Provides a Resource to Update the System Status.
 *
 * @RestResource(
 *   id = "memory_resource",
 *   label = @Translation("Memory Game settings"),
 *   uri_paths = {
 *     "canonical" = "/memory-game-settings"
 *   }
 * )
 */
class MemorySettingsResource extends ResourceBase {

  /**
   * Responds to  GET requests.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The incoming request.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The Response Message
   */
  public function get(Request $request): ResourceResponse {

    $service = \Drupal::service('memory_game.generate_positions');
    $settings = $service->getGameSettings();
    $response = new ResourceResponse($settings);
    $response->addCacheableDependency($settings);

    return $response;
  }
}