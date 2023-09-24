<?php
/**
 * Custom Drush commands
 * Drush command to consume data from https://pokeapi.co/
 * php version 8.0.28
 *
 * @file
 * @category Category
 * @package Drupal\sample_content\Services
 * @author   Juan Carlos Pérez <karlitoskillyou@hotmail.com>
 * @license  MIT
 * @link
 *
 * @file
 */

namespace Drupal\sample_content\Command;

use Drush\Commands\DrushCommands;

/**
 * Extending drush commands.
 */
class PokeAPIRequest extends DrushCommands
{

    /**
     * Drush command that displays the given text.
     *
     * @command pokeapi:request
     * @aliases pokeapi-request
     *
     * @usage pokeapi:request
     */
    function getFirstGenPokemons()
    {
        $url_pokemon = "https://pokeapi.co/api/v2/pokemon/";
        $url_species = "https://pokeapi.co/api/v2/pokemon-species/";
        for ($id = 1; $id <= 10; $id++) {
            $pokemon = \Drupal::service('sample_content.pokeapi_service')
                ->getPokemon($url_pokemon, $id);
            $specie = \Drupal::service('sample_content.pokeapi_service')
                ->getPokemon($url_species, $id);
            \Drupal::service('sample_content.pokeapi_service')
                ->savePokemon($pokemon, $specie, $id);
        }
    }

}