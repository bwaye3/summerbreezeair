<?php

namespace Drupal\storychief\EventSubscribers;

use Drupal\storychief\Event\StoryChiefEvents;
use Drupal\storychief\Event\StoryChiefRemoteCallEvent;

/**
 * Event subscriber handling the "update" event class definition.
 *
 * @package Drupal\storychief\EventSubscribers
 */
class StoryChiefRemoteCallUpdateEventSubscriber extends StoryChiefRemoteCallEventSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      StoryChiefEvents::UPDATE => 'onUpdate',
    ];
  }

  /**
   * Updates an entity or a translation.
   *
   * @param \Drupal\storychief\Event\StoryChiefRemoteCallEvent $event
   *   The event being dispatched.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityMalformedException
   * @throws \Drupal\Core\Entity\EntityStorageException
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function onUpdate(StoryChiefRemoteCallEvent $event) {
    /** @var \Drupal\node\NodeStorage $storage */
    $storage = $this->entityTypeManager->getStorage('node');

    // Filter out empty values.
    $this->setPayload(array_filter($event->payload['data']));

    /** @var \Drupal\node\NodeInterface $entity */
    $entity = $storage->load($this->payload['external_id']);

    if (!$entity) {
      $event->setResponse(['message' => 'Story not found. It may already have been deleted, or was never imported.'],
        404);
      return;
    }

    // If source is set, than we're dealing with a translation.
    if (!empty($this->getPayload()['source'])) {
      $entity = $entity->getTranslation($this->getPayload()['language']);
    }
    $entity->setPublished();
    $entity->setOwnerId($this->config->get('default_owner') ?? 1);
    $entity->setNewRevision();

    $this->setEntity($entity);
    $this->processStory($event);
  }

}
