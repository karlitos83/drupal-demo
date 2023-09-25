<?php

namespace Drupal\sample_trainer\EventSubscriber;

use Drupal\core_event_dispatcher\Event\Entity\EntityPresaveEvent;
use Drupal\hook_event_dispatcher\HookEventDispatcherInterface;
use Drupal\user_event_dispatcher\Event\User\UserLoginEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;

/**
 * Class to generate user token automatically.
 */
class UserRegistrationSubscriber implements EventSubscriberInterface {

  /**
   * On preSave assign user token reference.
   */
  public function preSave(EntityPresaveEvent $event) {
    $user = $event->getEntity();
    if ($user instanceof User && $user->isNew()) {
      $user->set('field_pokeballs', 3);
    }
  }

  public function createPokedollarsBalance(UserLoginEvent $event){
    $user = $event->getAccount();

    if(empty($user->getLastAccessedTime())){
        // $pokedollars_settings = \Drupal::config('sample_trainer.registration_settings');

        $user = $event->getAccount();
        $uid = $user->get('uid')->value;
        $base_pokedollars = 300;
        $pokedollars_description = 'Base pokedollars amount.';

        // Creating balance node.
        $node = Node::create(
            [
                'type' => 'pokedollars_balance',
                'title' => $uid . " Balance",
                'field_pokedollars' => $base_pokedollars,
                'field_operation_concept' => $pokedollars_description,
                'field_trainer' => $uid,
            ]
        );
        $node->save();

    }

  }


  /**
   * Subscribe events.
   */
  public static function getSubscribedEvents() {
    return [
      HookEventDispatcherInterface::ENTITY_PRE_SAVE => 'preSave',
      HookEventDispatcherInterface::USER_LOGIN => 'createPokedollarsBalance',
    ];
  }

}
