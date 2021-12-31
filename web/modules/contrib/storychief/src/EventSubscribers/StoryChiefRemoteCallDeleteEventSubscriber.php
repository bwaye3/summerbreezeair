<?php

namespace Drupal\storychief\EventSubscribers;

use Drupal\Core\Database\Database;
use Drupal\storychief\Event\StoryChiefEvents;
use Drupal\storychief\Event\StoryChiefRemoteCallEvent;

/**
 * Event subscriber handling the "delete" event class definition.
 *
 * @package Drupal\storychief\EventSubscribers
 */
class StoryChiefRemoteCallDeleteEventSubscriber extends StoryChiefRemoteCallEventSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      StoryChiefEvents::DELETE => 'onDelete',
    ];
  }

  /**
   * Delete an entity or a translation.
   *
   * @param \Drupal\storychief\Event\StoryChiefRemoteCallEvent $event
   *   The event being dispatched.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function onDelete(StoryChiefRemoteCallEvent $event) {
    // Filter out empty values.
    $this->setPayload(array_filter($event->payload['data']));

    $nid = empty($this->getPayload()['source'])
      ? $this->getPayload()['external_id']
      : $this->getPayload()['source']['data']['external_id'];

    /** @var \Drupal\node\NodeStorage $storage */
    $storage = $this->entityTypeManager->getStorage('node');

    /** @var \Drupal\node\NodeInterface $entity */
    $entity = $storage->load($nid);
    if (!$entity) {
      $event->setResponse(['message' => 'Story not found. It may already have been deleted, or was never imported.'],
        404);
      return;
    }

    // If the source field is not empty, we are deleting a translation.
    if (!empty($this->getPayload()['source']) && $entity->hasTranslation($this->getPayload()['language'])) {
      $entity->removeTranslation($this->getPayload()['language']);
      $entity->save();
      $event->setResponse(NULL, 204);
      return;
    }

    try {
      $storage->delete([$entity]);

      Database::getConnection()
        ->delete('storychief_meta_tags')
        ->condition('nid', $nid)
        ->condition('langcode', $this->getPayload()['language'])
        ->execute();

      $event->setResponse(NULL, 204);
    }
    catch (\Exception $exception) {
      $event->setResponse(['message' => $exception->getMessage()], 404);
    }
  }

}
