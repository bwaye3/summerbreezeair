<?php

namespace Drupal\storychief\EventSubscribers;

use Drupal\storychief\Event\StoryChiefEvents;
use Drupal\storychief\Event\StoryChiefRemoteCallEvent;

/**
 * Event subscriber handling the "publish" event class definition.
 *
 * @package Drupal\storychief\EventSubscribers
 */
class StoryChiefRemoteCallPublishEventSubscriber extends StoryChiefRemoteCallEventSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      StoryChiefEvents::PUBLISH => 'onPublish',
    ];
  }

  /**
   * Creates an entity or a translation.
   *
   * @param \Drupal\storychief\Event\StoryChiefRemoteCallEvent $event
   *   The entity being dispatched.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityMalformedException
   * @throws \Drupal\Core\Entity\EntityStorageException
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function onPublish(StoryChiefRemoteCallEvent $event) {
    /** @var \Drupal\node\NodeStorage $storage */
    $storage = $this->entityTypeManager->getStorage('node');

    // Filter out empty values.
    $this->setPayload(array_filter($event->payload['data']));

    // If source is empty, then we are creating a source entity.
    if (empty($this->getPayload()['source'])) {
      /** @var \Drupal\node\NodeInterface $entity */
      $node_type = $this->config->get('node_type');

      // Allow modules to alter the node type.
      \Drupal::moduleHandler()->alter('storychief_node_type', $node_type, $event->payload);

      $entity = $storage->create(['type' => $node_type]);
    }

    // Otherwise, we are creating a translation.
    else {
      /** @var \Drupal\node\NodeInterface $entity */
      $entity = $storage->load($this->getPayload()['source']['data']['external_id']);
      // Make sure a translation do not already exists.
      $entity = $entity->hasTranslation($this->getPayload()['language'])
        ? $entity->getTranslation($this->getPayload()['language'])
        : $entity->addTranslation($this->getPayload()['language']);
    }

    $this->setEntity($entity);
    $this->processStory($event);
  }

}
