<?php

namespace Drupal\storychief\EventSubscribers;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;
use Drupal\storychief\Event\StoryChiefRemoteCallEvent;
use Drupal\storychief\Plugin\FieldHandlerType\BaseFieldHandlerType;
use Drupal\storychief\Plugin\StorychiefFieldHandlerManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class StoryChiefRemoteCallEventSubscriberBase.
 *
 * Base class for event subscribers.
 *
 * @package Drupal\storychief\EventSubscribers
 */
abstract class StoryChiefRemoteCallEventSubscriberBase implements EventSubscriberInterface {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Configuration object for the StoryChief module.
   *
   * @var \Drupal\Core\Config\Config|\Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * The StoryChief field handler plugin manager.
   *
   * @var \Drupal\storychief\Plugin\StorychiefFieldHandlerManager
   */
  protected $storychiefManager;

  /**
   * The entity being processed.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected $entity;

  /**
   * An array containing the json decoded payload.
   *
   * @var array
   */
  protected $payload;

  /**
   * StoryChiefEventSubscriberBase constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   The config factory.
   * @param \Drupal\storychief\Plugin\StorychiefFieldHandlerManager $field_handler_manager
   *   The StoryChief field handler plugin manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ConfigFactory $config_factory, StorychiefFieldHandlerManager $field_handler_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->config = $config_factory->getEditable('storychief.settings');
    $this->storychiefManager = $field_handler_manager;
  }

  /**
   * Process the creation or update of a story.
   *
   * @param \Drupal\storychief\Event\StoryChiefRemoteCallEvent $event
   *   The event being dispatched.
   *
   * @return int|void
   *   0, SAVED_NEW or SAVED_UPDATED, depending on the operation performed.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   * @throws \Drupal\Core\Entity\EntityStorageException
   * @throws \Drupal\Core\Entity\EntityMalformedException
   */
  protected function processStory(StoryChiefRemoteCallEvent $event) {
    /** @var \Drupal\storychief\Plugin\FieldHandlerType\BaseFieldHandlerType $field_instance */

    $entity = $this->getEntity();

    $configuration = [
      'entity' => $entity,
      'payload' => $this->getPayload(),
    ];

    $definitions = $this->storychiefManager->getDefinitions();

    // Language and seo definitions are special cases.
    $language_plugin_definition = $definitions['language'] ?? NULL;
    $seo_plugin_definition = $definitions['seo'];

    unset($definitions['language']);
    unset($definitions['seo']);

    // Language has to run first as it is used in entity references plugins.
    if ($language_plugin_definition) {
      $field_instance = $this->storychiefManager->createInstance(
        $language_plugin_definition['id'],
        $configuration
      );
      $this->processField($field_instance);
    }

    // Set all fields. On failure set as response returned to StoryChief.
    try {
      foreach ($definitions as $definition) {
        $field_instance = $this->storychiefManager->createInstance($definition['id'], $configuration);
        $this->processField($field_instance);
      }
    }
    catch (\Exception $exception) {
      $event->setResponse(['message' => $exception->getMessage() ?: 'Unknown StoryChief Exception occurred'], 503);
      return 0;
    }

    if ($this->config->get('save_unpublished') == TRUE) {
      $entity->setUnpublished();
    }
    else {
      $entity->setPublished();
    }

    $status = $entity->save();

    // SEO needs to run after save because an entity ID is required.
    if ($seo_plugin_definition) {
      $field_instance = $this->storychiefManager->createInstance(
        $seo_plugin_definition['id'],
        $configuration
      );
      $this->processField($field_instance);
    }

    // Returns the node id, as external_id, and url if the node is published.
    $event->setResponse(
      [
        'id' => $entity->id(),
        'permalink' => $entity->isPublished() ? $entity->toUrl('canonical', ['absolute' => TRUE])
          ->toString() : NULL,
      ],
      200
    );

    return $status;
  }

  /**
   * Get the entity being processed.
   *
   * @return \Drupal\node\NodeInterface
   *   Entity being processed.
   */
  public function getEntity() {
    return $this->entity;
  }

  /**
   * Set the entity to process.
   *
   * @param \Drupal\node\NodeInterface $entity
   *   The entity to process.
   *
   * @return $this
   */
  public function setEntity(NodeInterface $entity) {
    $this->entity = $entity;
    return $this;
  }

  /**
   * Get the json decoded version of the payload.
   *
   * @return array
   *   Array containing the decoded json payload.
   */
  public function getPayload() {
    return $this->payload;
  }

  /**
   * Set the payload attribute.
   *
   * @param array $payload
   *   Array containing the decoded json payload.
   */
  public function setPayload(array $payload) {
    $this->payload = $payload;
  }

  /**
   * Set or update the field depending on the entity status.
   *
   * @param \Drupal\storychief\Plugin\FieldHandlerType\BaseFieldHandlerType $field_instance
   *   The field instance.
   */
  protected function processField(BaseFieldHandlerType $field_instance) {
    $field_definition = $field_instance->getFieldDefinition();

    // Some plugins shouldn't run for translations, skip them.
    if ($field_definition && !$field_definition->isTranslatable() && !$this->getEntity()->isDefaultTranslation()) {
      return;
    }

    $field_instance->upsert();
  }

}
