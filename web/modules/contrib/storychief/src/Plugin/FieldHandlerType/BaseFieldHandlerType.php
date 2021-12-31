<?php

namespace Drupal\storychief\Plugin\FieldHandlerType;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\node\NodeInterface;
use Drupal\storychief\Plugin\StoryChiefFieldHandlerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for the StoryChiefFieldHandler plugin type.
 *
 * @package Drupal\storychief\Plugin
 */
class BaseFieldHandlerType extends PluginBase implements StorychiefFieldHandlerInterface, ContainerFactoryPluginInterface {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The language manager service.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * The database connection service.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Configuration object for the StoryChief module.
   *
   * @var \Drupal\Core\Config\Config|\Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * The entity being processed.
   *
   * @var \Drupal\node\Entity\Node
   */
  private $entity;

  /**
   * Json decoded payload of the entire request made by StoryChief.
   *
   * @var array
   */
  private $payload;

  /**
   * The value to process by the plugin.
   *
   * @var mixed
   */
  private $value = NULL;

  /**
   * The field name to map to.
   *
   * @var mixed
   */
  private $drupalFieldName = NULL;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->setEntity($configuration['entity']);
    $this->setPayload($configuration['payload']);
    $this->setValue($configuration['payload'][$this->getPluginId()] ?? NULL);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );

    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->languageManager = $container->get('language_manager');
    $instance->database = $container->get('database');
    $instance->config = $container->get('config.factory')
      ->get('storychief.settings');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldDefinition() {
    if (!$this->getDrupalFieldName()) {
      return NULL;
    }

    return $this->getEntity()
      ->getFieldDefinitions()[$this->getDrupalFieldName()];
  }

  /**
   * {@inheritdoc}
   */
  public function getDrupalFieldName() {
    if ($this->drupalFieldName) {
      return $this->drupalFieldName;
    }

    if ($definedDrupalFieldName = $this->getPluginDefinition()['drupal_field_name'] ?? NULL) {
      return $definedDrupalFieldName;
    }

    // No mapping defined in annotation.
    if ($mapped_field = $this->config->get('mapping')["field_{$this->getPluginId()}"] ?? NULL) {
      return !empty($mapped_field) ? $mapped_field : NULL;
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function setDrupalFieldName(string $value) {
    $this->drupalFieldName = $value;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntity() {
    return $this->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setEntity(NodeInterface $entity) {
    $this->entity = $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getPayload() {
    return $this->payload;
  }

  /**
   * {@inheritdoc}
   */
  public function setPayload(array $payload) {
    $this->payload = $payload;
  }

  /**
   * Update or save fields depending on the entity state (new or update).
   */
  public function upsert() {
    // If creating an entity or a translation, call the set() method.
    if ($this->getEntity()->isNew() || $this->getEntity()->isNewTranslation()) {
      $this->set();
    }
    // Otherwise call the update() method.
    else {
      $this->update();
    }
  }

  /**
   * Setter for basic fields.
   *
   * Do not not requires further processing that assigning directly the content
   * of a StoryChief field to a Drupal field.
   */
  public function set() {
    $drupalFieldName = $this->getDrupalFieldName();

    // Field hasn't been mapped.
    if (!$drupalFieldName) {
      return;
    }

    $this->getEntity()->set($drupalFieldName, $this->getValue());
  }

  /**
   * {@inheritdoc}
   */
  public function getValue() {
    return $this->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setValue($value) {
    $this->value = $value;
  }

  /**
   * Updates basic fields.
   *
   * Identical to set() by default, but can be overridden if needed. If nothing
   * should happen on update, an overridden empty method should be added in the
   * plugin.
   */
  public function update() {
    $this->set();
  }

}
