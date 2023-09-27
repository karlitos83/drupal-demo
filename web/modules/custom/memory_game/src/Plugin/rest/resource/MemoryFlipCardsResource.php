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
 *   id = "memory_flip_cards_resource",
 *   label = @Translation("Memory Flip Cards"),
 *   uri_paths = {
 *     "create" = "/memory-flip-cards"
 *   }
 * )
 */
class MemoryFlipCardsResource extends ResourceBase {

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

    if (!isset($data->card2)) {
      throw new BadRequestHttpException("the field card2 is required");
    }
    $service = \Drupal::service('memory_game.generate_positions');
    $cardsFlipped = $service->flipCard([$data->card1,$data->card2]);

    $response = new ResourceResponse($cardsFlipped);
    $response->addCacheableDependency($cardsFlipped);

    return $response;
  }
}
