<?php
/**
 * Custom Drush commands
 * Drush command to consume data from https://pokeapi.co/
 * php version 8.0.28
 *
 * @file
 * @category Category
 * @package Drupal\sample_trainer\Services
 * @author   Juan Carlos Pérez <karlitoskillyou@hotmail.com>
 * @license  MIT
 * @link
 *
 * @file
 */

namespace Drupal\sample_trainer\Command;

use Drush\Commands\DrushCommands;

/**
 * Extending drush commands.
 */
class UpdateTrainer extends DrushCommands
{

    /**
     * Drush command that give pokedollars to trainers.
     *
     * @param int $amount
     * @command sample_trainer:pokedollars
     * @aliases stpok
     *
     * @usage stpok
     */
    function addPokedollars($amount = 100)
    {
        // Loading all nodes ids.
        $nodes = \Drupal::entityTypeManager()->getStorage('node');

        // filtering IDs by pokedollars_balance nodes
        $nids = $nodes->getQuery()
        ->condition('type', 'pokedollars_balance')
        ->execute();

        foreach ($nids as $nid) {
            /** @var \Drupal\node\NodeInterface $node */
            $node = $nodes->load($nid);
            $current_amount = $node->get('field_pokedollars')->value;
            $new_amount = $current_amount + $amount;
            $node->set('field_pokedollars', $new_amount);
            $node->set('field_operation_concept', "$amount pokedollars de regalo");
            $node->setNewRevision(TRUE);
            $node->revision_log = 'Created revision for node' . $nid;
            $node->save();
        }

        $this->output()->writeln("$amount pokedollars given to all trainers.");
    }

}