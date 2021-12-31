<?php

namespace Drupal\storychief\Plugin\StoryChiefFieldHandler;

use Drupal\storychief\Plugin\FieldHandlerType\TaxonomyTermFieldHandlerType;

/**
 * Class CategoriesTaxonomyTermStoryChiefFieldHandler.
 *
 * Set the tags as taxonomy terms.
 *
 * @StoryChiefFieldHandler(
 *   id = "category",
 *   label = @Translation("Handle the categories."),
 *   drupal_field_name = null,
 * )
 */
class CategoriesStoryChiefFieldHandler extends TaxonomyTermFieldHandlerType {

  /**
   * {@inheritdoc}
   */
  public function setValue($value) {
    // TODO: migrate legacy field ID category to categories.
    $term_names = array_column($this->getPayload()['categories']['data'], 'name');
    parent::setValue($term_names);
  }

}
