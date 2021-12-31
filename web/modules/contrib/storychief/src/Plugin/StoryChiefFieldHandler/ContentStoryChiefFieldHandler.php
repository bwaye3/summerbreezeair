<?php

namespace Drupal\storychief\Plugin\StoryChiefFieldHandler;

use Drupal\storychief\Plugin\FieldHandlerType\BaseFieldHandlerType;

/**
 * Class ContentStoryChiefFieldHandler.
 *
 * Map the storychief content field.
 *
 * @StoryChiefFieldHandler(
 *   id = "content",
 *   label = @Translation("Handle the content field."),
 *   default_drupal_field_name = NULL,
 * )
 */
class ContentStoryChiefFieldHandler extends BaseFieldHandlerType {

  /**
   * {@inheritdoc}
   */
  public function getValue() {
    return [
      'value' => parent::getValue(),
      'format' => 'full_html',
      'summary' => $this->getPayload()['excerpt'] ?? NULL,
    ];
  }

}
