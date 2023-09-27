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
 *   id = "memory_start_game_resource",
 *   label = @Translation("Memory Start Game"),
 *   uri_paths = {
 *     "create" = "/memory-start-game"
 *   }
 * )
 */
class MemoryStartGameResource extends ResourceBase {

  /**
   * Responds to  POST requests.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The incoming request.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The Response Message
   */
  public function post(Request $request): ResourceResponse {


    $data = json_decode($request->getContent());

    if (gettype($data->start) == "boolean") {

      if ($data->start != TRUE) {
        throw new BadRequestHttpException("To start game the field start must be TRUE");
      }
      else {
        $service = \Drupal::service('memory_game.generate_positions');
        $service->startMemoryGame();
        $settings = $service->getGameSettings();
  
        $response = new ResourceResponse($settings);
        $response->addCacheableDependency($settings);
      }
    }

    return $response;
  }
}
