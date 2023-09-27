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
 *   id = "memory_game_over_by_time_resource",
 *   label = @Translation("Memory Game Over By Time"),
 *   uri_paths = {
 *     "create" = "/memory-game-over-by-time"
 *   }
 * )
 */
class MemoryGameOverByTimeResource extends ResourceBase {

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
    $service = \Drupal::service('memory_game.generate_positions');

    if ($data->completed == FALSE) {
      $status = $service->gameOverByTime(FALSE);
      $response = new ResourceResponse($status);
      $response->addCacheableDependency($status);
    }

    return $response;
  }
}
