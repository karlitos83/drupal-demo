<?php

namespace Drupal\ni_migrate;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceModifierInterface;

/**
 * Assign MakeMedia service as migrate_media_handler service.
 */
class NihlMigrateServiceProvider implements ServiceModifierInterface {

  /**
   * Set 'Drupal\ni_migrate\MakeMedia' class as migrate_media_handler service.
   */
  public function alter(ContainerBuilder $container) {
    if ($container->hasDefinition('migrate_media_handler.service')) {
      $definition = $container->getDefinition('migrate_media_handler.service');
      $definition->setClass('Drupal\ni_migrate\MakeMedia');
    }
  }

}
