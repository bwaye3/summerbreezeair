<?php

namespace Drupal\storychief\Plugin\FieldHandlerType;

/**
 * Base class to handle taxonomy terms.
 *
 * @package Drupal\storychief\Plugin\StorychiefFieldHandler
 */
class ListFieldHandlerType extends BaseFieldHandlerType {

  /**
   * {@inheritdoc}
   */
  public function setValue($value) {
    if (!is_array($value)) {
      $value = explode(',', $value);
    }

    parent::setValue($value);
  }

}
