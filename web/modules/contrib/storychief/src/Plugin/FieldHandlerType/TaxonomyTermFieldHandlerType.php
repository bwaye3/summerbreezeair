<?php

namespace Drupal\storychief\Plugin\FieldHandlerType;

/**
 * Base class to handle taxonomy terms.
 *
 * @package Drupal\storychief\Plugin\StorychiefFieldHandler
 */
class TaxonomyTermFieldHandlerType extends EntityReferenceFieldHandlerType {

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function getValue() {
    $terms = [];
    $term_names = parent::getValue();

    foreach ($term_names as $term_name) {
      $term = $this->loadTaxonomyTerm($term_name);

      if (!$term && $this->getFieldDefinition()->getSetting('handler_settings')['auto_create']) {
        $term = $this->createTaxonomyTerm($term_name);
      }

      array_push($terms, $term);
    }

    return $terms;
  }

  /**
   * Find a taxonomy term by its name.
   *
   * @param string $term_name
   *   The term name.
   *
   * @return \Drupal\Core\Entity\EntityInterface|mixed|null
   *   The taxonomy term or null if none was found.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function loadTaxonomyTerm(string $term_name) {
    $vocabularies = $this->getFieldDefinition()
      ->getSetting('handler_settings')['target_bundles'];

    $terms = $this->entityTypeManager
      ->getStorage('taxonomy_term')
      ->loadByProperties([
        'vid' => array_values($vocabularies),
        'name' => $term_name,
        'langcode' => $this->getEntity()->language()->getId(),
      ]);

    // A term was found.
    if (empty($terms)) {
      return NULL;
    }

    return array_values($terms)[0];
  }

  /**
   * Creates a new taxonomy term.
   *
   * @param string $term_name
   *   The term name.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   Newly created taxonomy term.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function createTaxonomyTerm(string $term_name) {
    $vocabularies = $this->getFieldDefinition()
      ->getSetting('handler_settings')['target_bundles'];
    $term = $this->entityTypeManager
      ->getStorage('taxonomy_term')
      ->create([
        'name' => $term_name,
        'vid' => array_values($vocabularies)[0],
        'langcode' => $this->getEntity()->language()->getId(),
      ]);

    $term->save();

    return $term;
  }

}
