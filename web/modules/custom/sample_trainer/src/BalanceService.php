<?php
namespace Drupal\sample_trainer;
use Drupal\Core\Database\Connection;
use Drupal\user\Entity\User;

/**
 * Class CustomService
 *
 * @package Drupal\sample_content\Services
 */
class BalanceService
{
    /**
     * Database connection.
     *
     * @var \Drupal\Core\Database\Connection
     */
    protected $connection;

    /**
     * Constructs a new Connection object.
     *
     * @param \Drupal\Core\Database\Connection $connection
     *   Add database connection.
     */
    public function __construct(Connection $connection){
        $this->database = $connection;
    }

    /**
     * This function fetch the user pokedollars amount.
     *
     * @param int $uid  The trainer UID.
     */
    public function getAmount(int $uid)
    {
        $nid = $this->getBalanceNodeId($uid);

        if (!empty($nid)) {
            $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
            $pokedollars = $node->get('field_pokedollars')->value;
            return $pokedollars;
        }

        return NULL;

    }

    /**
     * This function fetch the user pokedollars amount.
     *
     * @param int $uid  The Pokemon ID.
     * @param int $substract pokedollars to substract
     */
    public function substractAmount(int $uid, int $substract, int $pokeballs)
    {
        $current_amount = $this->getAmount($uid);
        $nid = $this->getBalanceNodeId($uid);
        $new_amount = $current_amount - $substract;
        if (!empty($nid)) {
            $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
            $node->set('field_pokedollars', $new_amount);
            $node->set('field_operation_concept', "Pokeballs X$pokeballs -$substract pokedollars");
            $node->setNewRevision(TRUE);
            $node->save();
        }
    }

    /**
     * This function fetch balance nodes ids.
     *
     * @param int $uid  Trainer id.
     */
    private function getBalanceNodeId($uid) {
        $query = $this->database->select('node__field_trainer', 'nft');
        $query
        ->fields('nft', ['entity_id'])
        ->condition('nft.field_trainer_target_id', $uid);
        $query->range(0, 1);
        $nid = $query->execute()->fetchField();

        return $nid;
    }
}

