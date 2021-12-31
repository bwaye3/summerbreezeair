<?php

namespace Drupal\storychief\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\node\NodeInterface;

/**
 * Defines an interface for StoryChief field handler plugins.
 */
interface StoryChiefFieldHandlerInterface extends PluginInspectionInterface {

  /**
   * Retrieve the node being processed.
   *
   * @return \Drupal\node\NodeInterface
   *   The node being processed.
   */
  public function getEntity();

  /**
   * Set the node that is going to be processed.
   *
   * @param \Drupal\node\NodeInterface $entity
   *   The node to process.
   */
  public function setEntity(NodeInterface $entity);

  /**
   * Get the value of the main field to process for this plugin.
   *
   * @return string|array
   *   The value to process.
   */
  public function getValue();

  /**
   * Set the value sent by StoryChief to process.
   *
   * @param string|array $value
   *   The value to process.
   */
  public function setValue($value);

  /**
   * Get the entire payload.
   *
   * @return array
   *   Array representing the json decoded payload.
   */
  public function getPayload();

  /**
   * Set the payload to process.
   *
   * @param array $payload
   *   Array representing the json decoded payload.
   */
  public function setPayload(array $payload);

  /**
   * Get the name of the Drupal field to map to.
   *
   * @return string
   *   The Drupal field name.
   */
  public function getDrupalFieldName();

  /**
   * Set the name of the Drupal field to map to.
   *
   * @param string $value
   *   The Drupal field name.
   */
  public function setDrupalFieldName(string $value);

  /**
   * Get the field definition.
   *
   * @return \Drupal\field\Entity\FieldConfig|null
   *   The configuration object of the field.
   */
  public function getFieldDefinition();

  /**
   * Set a value from StoryChief to Drupal.
   */
  public function set();

  /**
   * Update the content of a field.
   */
  public function update();

}
