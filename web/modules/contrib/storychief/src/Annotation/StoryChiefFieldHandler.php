<?php

namespace Drupal\storychief\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a StoryChief field handler item annotation object.
 *
 * @see \Drupal\storychief\Plugin\StorychiefFieldHandlerManager
 * @see plugin_api
 *
 * @Annotation
 */
class StoryChiefFieldHandler extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The label of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

  /**
   * The name of the Drupal field to use with this plugin.
   *
   * @var string
   */
  public $drupalFieldName;

}
