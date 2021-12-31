<?php

namespace Drupal\storychief\Plugin\StoryChiefFieldHandler;

use Drupal\storychief\Exceptions\InvalidLanguageException;
use Drupal\storychief\Plugin\FieldHandlerType\BaseFieldHandlerType;

/**
 * Class LanguageStoryChiefFieldHandler.
 *
 * Check if the langcode exists and set it. Set to not translatable, as it is
 * handled on entity translation creation.
 *
 * @StoryChiefFieldHandler(
 *   id = "language",
 *   label = @Translation("Handle the language field"),
 *   drupal_field_name = "langcode",
 * )
 */
class LanguageStoryChiefFieldHandler extends BaseFieldHandlerType {

  /**
   * Fallback to Drupal's default language.
   *
   * {@inheritdoc}
   */
  public function getValue() {
    if (!$value = parent::getValue()) {
      $value = $this->languageManager->getDefaultLanguage()->getId();
    }
    return $value;
  }

  /**
   * Verify that the langcode is installed and set it.
   *
   * {@inheritdoc}
   *
   * @throws \Drupal\storychief\Exceptions\InvalidLanguageException
   */
  public function set() {
    if (in_array($this->getValue(), array_keys($this->languageManager->getLanguages()))) {
      parent::set();
    }
    else {
      $message = 'The language provided is not available on this website.';
      throw new InvalidLanguageException($message, 501);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function update() {
    // Don't do anything, langcode should not be changed once entity has been
    // created.
  }

}
