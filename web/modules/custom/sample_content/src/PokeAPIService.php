<?php
namespace Drupal\sample_content;

use Drupal\node\Entity\Node;

/**
 * Class CustomService
 *
 * @package Drupal\sample_content\Services
 */
class PokeAPIService
{

    /**
     * This function performs requests to pokeapi.co.
     *
     * @param string $url The API URL.
     * @param int    $id  The Pokemon ID.
     */
    public function getPokemon(string $url, int $id)
    {
        $url = $url . $id;
        $headers = [];
        $headers[] = 'Content-Type:application/json';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $json_data = json_decode($response, true);

        return $json_data;

    }

    /**
     * This function performs ABC operation.
     *
     * @param array $pokemon    Pokemon data.
     * @param array $specie     Specie data.
     * @param int   $pokemon_id The ID of the Pokémon.
     */
    public function savePokemon(array $pokemon, array $specie, int $pokemon_id)
    {
        $tids = [];
        foreach ($pokemon['types'] as $type) {
            $term = \Drupal::entityTypeManager()
                ->getStorage('taxonomy_term')
                ->loadByProperties(['name' => $type['type']['name']]);

            if (!empty($term)) {
                // Getting the first tid that match name text.
                $tid = reset($term)->id();
                $tids[] = $tid;
            }
        }
        // Getting pokemon color tid.
        $color_term = \Drupal::entityTypeManager()
            ->getStorage('taxonomy_term')
            ->loadByProperties(['name' => $specie['color']['name']]);
        $color_tid = 0;
        if (!empty($term)) {
            // Getting the first tid that match name text.
            $color_tid = reset($color_term)->id();
        }

        foreach ($specie['flavor_text_entries'] as $flavor_text) {
            $pokemon_text = $flavor_text['flavor_text'];
            break;
        }

        // Creating pokemon node.
        $node = Node::create(
            [
                'type' => 'pokemon',
                'title' => $pokemon['name'],
                'body' => [
                    'value' => $pokemon_text,
                    'format' => 'plain_text',
                ],
                'field_capture_rate' => $specie['capture_rate'],
                'field_pokemon_type' => $tids,
                'field_pokemon_color' => $color_tid,
                'field_base_happiness' => $specie['base_happiness'],
                'field_pokemon_front_image' => [
                    'uri' => $pokemon['sprites']['front_default'],
                    'alt' => $pokemon['name'],
                ],
                'field_pokedex_id' => $pokemon_id,
            ]
        );

        $node->save();

    }

}

