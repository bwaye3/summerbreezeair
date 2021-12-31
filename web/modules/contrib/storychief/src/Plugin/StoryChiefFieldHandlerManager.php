<?php

namespace Drupal\storychief\Plugin;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

/**
 * Provides the StoryChief field handler plugin manager.
 */
class StoryChiefFieldHandlerManager extends DefaultPluginManager {

  /**
   * Constructs a new StoryChiefFieldHandlerManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/StoryChiefFieldHandler',
      $namespaces,
      $module_handler,
      'Drupal\storychief\Plugin\StoryChiefFieldHandlerInterface',
      'Drupal\storychief\Annotation\StoryChiefFieldHandler'
    );

    $this->alterInfo('storychief_field_handler_info');
    $this->setCacheBackend($cache_backend, 'storychief_field_handler_plugins');
  }

}
