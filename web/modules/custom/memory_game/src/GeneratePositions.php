<?php

namespace Drupal\memory_game;

use Drupal\file\Entity\File;

/**
 * Class GeneratePositions.
 */
class GeneratePositions {

  /**
   * add logic to generate cards positions.
   */
  public function generatePositions(array $trhidPartySettings) {

    $memoryImages = $trhidPartySettings['forms']['game_settings']['game_images'];

    // duplicate imagenes
    foreach ($memoryImages as $key => $value) {
      array_push($memoryImages, $value);
    }

    // Get cards collection.
    $tempPositionsStore = \Drupal::service('tempstore.private')->get('memory_game.cards');
    $tempPositionsStore->set('completed', 0);

    $imagesShuffled = $this->arrayShuffle($memoryImages);
    // renew images positions.
    foreach ($imagesShuffled as $key => $value) {
      $tempPositionsStore->delete($key);
      $tempPositionsStore->set($key, $value);
    }

  }

  /**
   * Set game settings.
   */
  public function setGameSettings($trhidPartySettings, $webformSubmissionId, $webformSubmissionUuid, $webformId) {

    $gameTime = intval($trhidPartySettings['forms']['game_settings']['game_time']);
    $memoryImages = $trhidPartySettings['forms']['game_settings']['game_images'];
    $winnerRedirectPath = $trhidPartySettings['forms']['game_settings']['redirect_path'];
    $noWinnerRedirectPath = $trhidPartySettings['forms']['game_settings']['no_winner_redirect_path'];

    $cardsNumber = (count($memoryImages) * 2);

    $tempPositionsStore = \Drupal::service('tempstore.private')->get('memory_game.settings');
    $tempPositionsStore->set('game_time', $gameTime);
    $tempPositionsStore->set('game_max_time', ($gameTime*60) );
    $tempPositionsStore->set('cards_number', $cardsNumber);
    $tempPositionsStore->set('game_status', 'unstarted');
    $tempPositionsStore->set('game_type', 'memory');
    $tempPositionsStore->set('game_start_time', 0);
    $tempPositionsStore->set('game_limit_time', 0);
    $tempPositionsStore->set('webform_id', $webformId);
    $tempPositionsStore->set('winner_redirect_path', $winnerRedirectPath);
    $tempPositionsStore->set('no_winner_redirect_path', $noWinnerRedirectPath);
    $tempPositionsStore->set('webform_submission_id', $webformSubmissionId);
    $tempPositionsStore->set('webform_submission_uuid', $webformSubmissionUuid);
    $backgroundFid = $trhidPartySettings['forms']['game_settings']['card_background'];
    $backgroundFile = File::load($backgroundFid[0]);
    $backgroundUri = $backgroundFile->getFileUri();
    $tempPositionsStore->set('card_background', $backgroundUri);

  }

  /**
   * Get game settings.
   */
  public function getGameSettings(): array {

    $tempPositionsStore = \Drupal::service('tempstore.private')->get('memory_game.settings');

    $settings = [];

    $settings['game_time'] = $tempPositionsStore->get('game_time');
    $settings['game_max_time'] = $tempPositionsStore->get('game_max_time');
    $settings['cards_number'] = $tempPositionsStore->get('cards_number');
    $settings['game_status'] = $tempPositionsStore->get('game_status');
    $settings['game_type'] = $tempPositionsStore->get('game_type');
    $settings['game_start_time'] = $tempPositionsStore->get('game_start_time');
    $settings['game_limit_time'] = $tempPositionsStore->get('game_limit_time');
    $settings['webform_id'] = $tempPositionsStore->get('webform_id');
    $settings['winner_redirect_path'] = $tempPositionsStore->get('winner_redirect_path');
    $settings['no_winner_redirect_path'] = $tempPositionsStore->get('no_winner_redirect_path');
    $settings['webform_submission_id'] = $tempPositionsStore->get('webform_submission_id');
    $settings['webform_submission_uuid'] = $tempPositionsStore->get('webform_submission_uuid');
    $settings['card_background'] = $tempPositionsStore->get('card_background');

    return $settings;

  }

  /**
   * function to start game memory.
   */
  public function startMemoryGame() {

    $gameSettings = \Drupal::service('tempstore.private')->get('memory_game.settings');
    $gameStatus = $gameSettings->get('game_status');
    $gameTime = $gameSettings->get('game_time');

    if ($gameStatus == 'unstarted') {

      $gameStatus = 'started';
      $gameStartTime = \Drupal::time()->getCurrentTime();
      $gameLimitTime = $gameStartTime + ($gameTime * 60);

      $gameSettings->set('game_status', $gameStatus);
      $gameSettings->set('game_start_time', $gameStartTime);
      $gameSettings->set('game_limit_time', $gameLimitTime);
    }

  }

  /**
   * function to resolve if request is in time.
   */
  public function inTime() {

    $gameSettings = \Drupal::service('tempstore.private')->get('memory_game.settings');
    $gameStartTime = $gameSettings->get('game_start_time');
    $gameLimitTime = $gameSettings->get('game_limit_time');
    $gameStatus = $gameSettings->get('game_status');

    if ($gameStatus == 'started') {
      $requestTime = \Drupal::time()->getCurrentTime();

      if ($requestTime > $gameStartTime && $requestTime <= $gameLimitTime) {
        return true;
      }
    }

    return false;

  }

  /**
   * function to get the game time record.
   */
  public function getTimeRecord(int $requestTime) {

    $gameSettings = \Drupal::service('tempstore.private')->get('memory_game.settings');
    $gameStartTime = $gameSettings->get('game_start_time');
    $gameTimeRecord = $requestTime - $gameStartTime;

    return $gameTimeRecord;
  }

  /**
   * function to flip first card.
   */
  public function flipFirstCard(array $positions) {
    $cards = [];

    $tempPositionsStore = \Drupal::service('tempstore.private')->get('memory_game.cards');
    $gameSettings = \Drupal::service('tempstore.private')->get('memory_game.settings');
    $gameStatus = $gameSettings->get('game_status');
    $cards['game_status'] = $gameStatus;

    if ($this->inTime()) {
      $cardsNumber = $gameSettings->get('cards_number');

      foreach ($positions as $key) {
        $fid = $tempPositionsStore->get($key);
        $file = File::load($fid);
        $cards[$key] = $file->getFileUri();
      }
    }

    return $cards;

  }

  /**
   * function to flip game cards.
   */
  public function flipCard(array $positions) {
    $cards = [];
    $requestTime = $requestTime = \Drupal::time()->getCurrentTime();
    $tempPositionsStore = \Drupal::service('tempstore.private')->get('memory_game.cards');
    $gameSettings = \Drupal::service('tempstore.private')->get('memory_game.settings');
    $cardsNumber = $gameSettings->get('cards_number');
    $gameStatus = $gameSettings->get('game_status');

    if ($this->inTime()) {
      foreach ($positions as $key) {
        $fid = $tempPositionsStore->get($key);
        $file = File::load($fid);
        $cards[$key] = $file->getFileUri();
      }
      // mark if is pair;
      $isPair = $this->validateCards($cards);
      $cards['isPair'] = $isPair;

      // Validate remaining cards
      $cards['completed'] = 0;
      $cards['cards_remaining'] = $cardsNumber;

      if ($isPair) {
        $cardsNumber = $cardsNumber - 2;
        $gameSettings->set('cards_number', $cardsNumber);
        $cards['cards_remaining'] = $cardsNumber;
        $cards['game_status'] = $gameStatus;
      }

      if ($cardsNumber == 0) {
        $tempPositionsStore->set('completed', 1);
        $tempPositionsStore->set('game_time_record', $this->getTimeRecord($requestTime));
        $completed = $tempPositionsStore->get('completed');
        $gameSettings->set('game_status', 'game_over');
        $cards['completed'] = $completed;
        $cards['game_status'] = $gameSettings->get('game_status');
        $cards['game_time_record'] = $tempPositionsStore->get('game_time_record');
        // Save game over record.
        $this->setGameOverStatus(1, $cards['game_time_record']);
        $this->saveGameOverRecord($cards, 'possible_winner');
      }
    }

    return $cards;
  }

  /**
   * validate if cards are pair.
   */
  public function validateCards(array $cards) {
    $cardsFlipped = [];
    foreach ($cards as $key => $value) {
      $cardsFlipped[] = $value;
    }

    if ($cardsFlipped[0] == $cardsFlipped[1]) {
      return TRUE;
    }

    return FALSE;

  }

  /**
   * Game Over by time actions.
   */
  public function gameOverByTime(bool $completed) {
    $gameSettings = \Drupal::service('tempstore.private')->get('memory_game.settings');
    $gameSettings->set('game_status', 'game_over');

    $cards = [];

    if ($completed == 0) {
      $cards['game_time_record'] = 0;
      $cards['completed'] = $completed;
      $this->saveGameOverRecord($cards, 'no_winner');
      $this->setGameOverStatus($completed, $cards['game_time_record']);
    }

    return $cards;

  }

  /**
   * function to save game record.
   */
  public function saveGameOverRecord(array $cards, $game_status) {

    $gameSettings = \Drupal::service('tempstore.private')->get('memory_game.settings');

    $webformSubmissionEntity = \Drupal::entityTypeManager()
      ->getStorage('webform_submission')
      ->load($this->getGameSettings()['webform_submission_id']);

    $webformSubmissionData = $webformSubmissionEntity->getData();
    $webformSubmissionData['game_status'] = $game_status;
    $webformSubmissionData['game_type'] = $gameSettings->get('game_type');
    $webformSubmissionData['game_time'] = $cards['game_time_record'];
    $webformSubmissionData['game_completed'] = $cards['completed'];
    $webformSubmissionEntity->setData($webformSubmissionData);
    $webformSubmissionEntity->save();
    // $this->destroyMemory();

  }

  /**
   * set Game Over status.
   */
  public function setGameOverStatus(int $completed, string $gameTime) {
    $status = \Drupal::service('tempstore.private')->get('memory_game.status');
    // delete previus state
    $status->delete('completed');
    $status->delete('game_time');
    // write a new state
    $status->set('completed', $completed);
    $status->set('game_time', $gameTime);
  }

  /**
   * get Game Over status.
   */
  public function getGameOverStatus() {
    $status = \Drupal::service('tempstore.private')->get('memory_game.status');
    $gameOverStatus = [];
    $gameOverStatus['game_status'] = $status->get('completed');
    $gameOverStatus['game_time'] = $status->get('game_time');

    return $gameOverStatus;
  }

  /**
   * function to shuffle array cards.
   */
  private function arrayShuffle(array $memoryImages) {

    $keys = array_keys($memoryImages);
    $temp = $memoryImages;
    shuffle($temp);
    $imagesSuffled = [];

    foreach ($temp as $key => $value) {
      $imagesSuffled[$keys[$key]] = $value;
    }

    return $imagesSuffled;
  }

  public function destroySettings(): void {
    /** @var \Drupal\Core\TempStore\PrivateTempStore $tempPositionsStore */
    $tempPositionsStore = \Drupal::service('tempstore.private')->get('memory_game.settings');

    foreach ($this->getGameSettings() as $key => $value) {
      $tempPositionsStore->delete($key);
    }
  }

}
