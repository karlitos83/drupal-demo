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
 *   id = "memory_flip_first_card_resource",
 *   label = @Translation("Memory Flip First Card"),
 *   uri_paths = {
 *     "create" = "/memory-flip-first-card"
 *   }
 * )
 */
class MemoryFlipFirstCardResource extends ResourceBase {

  /**
   * Responds to  GET requests.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The incoming request.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The Response Message
   */
  public function post(Request $request): ResourceResponse {

    $data = json_decode($request->getContent());

    if (!isset($data->card1)) {
      throw new BadRequestHttpException("the field card1 is required");
    }

    $service = \Drupal::service('memory_game.generate_positions');
    $cardFlipped = $service->flipFirstCard([$data->card1]);

    $response = new ResourceResponse($cardFlipped);
    $response->addCacheableDependency($cardFlipped);

    return $response;
  }
}
