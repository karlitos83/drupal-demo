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
     * @param int $uid  The Pokemon ID.
     */
    public function getAmount(int $uid)
    {

        $query = $this->database->select('node__field_trainer', 'nft');
        $query
        ->fields('nft', ['entity_id'])
        ->condition('nft.field_trainer_target_id', $uid);
        $query->range(0, 1);
        $nid = $query->execute()->fetchField();

        if (!empty($nid)) {
            $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
            $pokedollars = $node->get('field_pokedollars')->value;
            return $pokedollars;
        }

        return NULL;

    }

}

