<?php

namespace Drupal\storychief\Plugin\StoryChiefFieldHandler;

use Drupal\storychief\Exceptions\UndefinedFieldTypeException;
use Drupal\storychief\Plugin\FieldHandlerType\BaseFieldHandlerType;
use Drupal\storychief\Plugin\FieldHandlerType\EntityReferenceFieldHandlerType;
use Drupal\storychief\Plugin\FieldHandlerType\ImageFieldHandlerType;
use Drupal\storychief\Plugin\FieldHandlerType\ListFieldHandlerType;
use Drupal\storychief\Plugin\FieldHandlerType\TaxonomyTermFieldHandlerType;

/**
 * Class CustomFieldStoryChiefFieldHandler.
 *
 * Handle the array of custom fields.
 *
 * @StoryChiefFieldHandler(
 *   id = "custom_fields",
 *   label = @Translation("Handle the custom fields."),
 *   drupal_field_name = null,
 * )
 */
class CustomFieldStoryChiefFieldHandler extends BaseFieldHandlerType {

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\storychief\Exceptions\UndefinedFieldTypeException
   */
  public function upsert() {
    foreach ($this->getValue() as $custom_field_data) {
      if ($field_handler = $this->getFieldHandler($custom_field_data)) {

        // Some plugins shouldn't run for translations, skip them.
        if (!$field_handler->getFieldDefinition()->isTranslatable() && !$this->getEntity()->isDefaultTranslation()) {
          continue;
        }

        $field_handler->setValue($custom_field_data['value']);

        // Set or update field.
        $field_handler->upsert();
      }
    }
  }

  /**
   * Retrieve correct FileHandler from the mapping type.
   *
   * @param array $custom_field_data
   *   Array holding the custom fields data.
   *
   * @return \Drupal\storychief\Plugin\FieldHandlerType\BaseFieldHandlerType|null
   *   The field handler
   *
   * @throws \Drupal\storychief\Exceptions\UndefinedFieldTypeException
   */
  protected function getFieldHandler(array $custom_field_data) {
    $drupal_field_key = $this->config->get('custom_field_mapping')[$custom_field_data['key']]['field'] ?? '';
    if (!$field_definition = $this->getEntity()->getFieldDefinitions()[$drupal_field_key] ?? NULL) {
      return NULL;
    }

    $container = \Drupal::getContainer();

    switch ($field_definition->getType()) {
      case 'string':
      case 'string_long':
      case 'text':
      case 'text_long':
      case 'text_with_summary':
        $field_handler = BaseFieldHandlerType::create($container, $this->configuration, NULL, NULL);
        break;

      case 'list':
      case 'list_string':
      case 'list_float':
      case 'list_integer':
        $field_handler = ListFieldHandlerType::create($container, $this->configuration, NULL, NULL);
        break;

      case 'image':
        $field_handler = ImageFieldHandlerType::create($container, $this->configuration, NULL, NULL);
        break;

      case 'entity_reference':
        if ($field_definition->getSetting('target_type') === 'taxonomy_term') {
          $field_handler = TaxonomyTermFieldHandlerType::create($container, $this->configuration, NULL, NULL);
          break;
        }
        $field_handler = EntityReferenceFieldHandlerType::create($container, $this->configuration, NULL, NULL);
        break;

      default:
        throw new UndefinedFieldTypeException("No mapping exists for field type {$field_definition->getType()}");
    }

    $field_handler->setDrupalFieldName($drupal_field_key);

    return $field_handler;
  }

}
