<?php

namespace Drupal\storychief\Plugin\FieldHandlerType;

use Drupal\paragraphs\ParagraphInterface;

/**
 * Base class for paragraph fields.
 *
 * @package Drupal\storychief\Plugin\StorychiefFieldHandler
 */
abstract class ParagraphFieldHandlerType extends BaseFieldHandlerType {

  /**
   * Get the paragraph to process.
   *
   * Can be an new paragraph, an existing one or a translation.
   *
   * @param string $paragraph_type
   *   The type of paragraph to get.
   *
   * @return \Drupal\paragraphs\ParagraphInterface
   *   The paragraph to use to map data.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getParagraph(string $paragraph_type) {
    $entity = $this->getEntity();

    /** @var \Drupal\Core\Entity\Sql\SqlContentEntityStorage $storage */
    $storage = $this->entityTypeManager->getStorage('paragraph');

    // If there is already a paragraph attached, use it.
    if (!$entity->get($this->getDrupalFieldName())->isEmpty()) {
      /** @var \Drupal\paragraphs\ParagraphInterface $paragraph */
      $paragraphs = $entity->get($this->getDrupalFieldName())
        ->referencedEntities();
      $paragraph = reset($paragraphs);

      // If the main entity being processed is a new translation, returns a new
      // translation of this paragraph.
      $entity_langcode = $entity->language()->getId();
      $paragraph = $paragraph->hasTranslation($entity_langcode)
        ? $paragraph->getTranslation($entity_langcode)
        : $paragraph->addTranslation($entity_langcode);

      if (!$paragraph->isNewTranslation()) {
        $paragraph->setNewRevision();
      }

      return $paragraph;
    }

    // Otherwise, we create a new paragraph in the source language of the entity
    // being processed.
    /** @var \Drupal\paragraphs\ParagraphInterface $paragraph */
    $paragraph = $storage->create([
      'type' => $paragraph_type,
      'langcode' => $entity->language()->getId(),
    ]);

    return $paragraph;
  }

  /**
   * Save and attach the paragraph the processed entity.
   *
   * @param \Drupal\paragraphs\ParagraphInterface $paragraph
   *   The paragraph holding the imported data.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function setParagraph(ParagraphInterface $paragraph) {
    $paragraph->save();

    $this->getEntity()->set($this->getDrupalFieldName(), [
      'target_id' => $paragraph->id(),
      'target_revision_id' => $paragraph->getRevisionId(),
    ]);
  }

  /**
   * Deletes a paragraph or its translation.
   *
   * @param \Drupal\paragraphs\ParagraphInterface $paragraph
   *   The paragraph to delete.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function deleteParagraph(ParagraphInterface $paragraph) {
    // If the main entity is not the source one, delete the paragraph
    // translation only.
    if (!$this->getEntity()->isDefaultTranslation()) {
      $paragraph->removeTranslation($this->getEntity()->language()->getId());
      return;
    }

    // Delete the paragraph, and empty the field referencing it.
    $paragraph->delete();
    $this->getEntity()->set($this->getDrupalFieldName(), NULL);
  }

}
