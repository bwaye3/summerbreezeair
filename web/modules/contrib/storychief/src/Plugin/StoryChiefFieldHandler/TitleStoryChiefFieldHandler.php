<?php

namespace Drupal\storychief\Plugin\StoryChiefFieldHandler;

use Drupal\storychief\Plugin\FieldHandlerType\BaseFieldHandlerType;

/**
 * Class TitleStoryChiefFieldHandler.
 *
 * Map the StoryChief title to to drupal entity title.
 *
 * @StoryChiefFieldHandler(
 *   id = "title",
 *   label = @Translation("Handle the title field."),
 *   drupal_field_name = "title",
 * )
 */
class TitleStoryChiefFieldHandler extends BaseFieldHandlerType {

}
