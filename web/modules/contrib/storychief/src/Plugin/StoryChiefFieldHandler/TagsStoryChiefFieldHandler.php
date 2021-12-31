<?php

namespace Drupal\storychief\Plugin\StoryChiefFieldHandler;

use Drupal\storychief\Plugin\FieldHandlerType\TaxonomyTermFieldHandlerType;

/**
 * Class TagsStoryChiefFieldHandler.
 *
 * Set the tags as taxonomy terms.
 *
 * @StoryChiefFieldHandler(
 *   id = "tags",
 *   label = @Translation("Handle the tags."),
 *   drupal_field_name = null,
 * )
 */
class TagsStoryChiefFieldHandler extends TaxonomyTermFieldHandlerType {

  /**
   * {@inheritdoc}
   */
  public function setValue($value) {
    $term_names = array_column($value['data'], 'name');
    parent::setValue($term_names);
  }

}
