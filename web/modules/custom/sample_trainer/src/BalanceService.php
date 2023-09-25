<?php
namespace Drupal\sample_trainer;

/**
 * Class CustomService
 *
 * @package Drupal\sample_content\Services
 */
class BalanceService
{

    /**
     * This function fetch the user balance.
     *
     * @param int $uid  The Pokemon ID.
     */
    public function getBalance(int $uid)
    {
        // TODO get trainer pokedollars amount
        $amount = 300;
        return $amount;

    }

}

