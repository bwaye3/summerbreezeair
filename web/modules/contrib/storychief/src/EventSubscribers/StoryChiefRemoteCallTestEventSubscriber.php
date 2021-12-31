<?php

namespace Drupal\storychief\EventSubscribers;

use Drupal\storychief\Event\StoryChiefEvents;
use Drupal\storychief\Event\StoryChiefRemoteCallEvent;

/**
 * Event subscriber handling the "test" event class definition.
 *
 * The event is used as a ping, and to inform of changes in custom fields
 * definition.
 *
 * @package Drupal\storychief\EventSubscribers
 */
class StoryChiefRemoteCallTestEventSubscriber extends StoryChiefRemoteCallEventSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      StoryChiefEvents::TEST => 'onTest',
    ];
  }

  /**
   * Respond to the "test" event.
   *
   * @param \Drupal\storychief\Event\StoryChiefRemoteCallEvent $event
   *   The dispatched even.
   */
  public function onTest(StoryChiefRemoteCallEvent $event) {
    $this->setPayload(array_filter($event->payload['data']));

    if ($this->getPayload()['custom_fields']) {
      $custom_field_mapping = $this->config->get('custom_field_mapping');
      $mapping = [];
      foreach ($this->getPayload()['custom_fields']['data'] as $custom_field) {
        $mapping[$custom_field['name']] = [
          'label' => $custom_field['label'],
          'type' => $custom_field['type'],
          'field' => $custom_field_mapping[$custom_field['name']]['field'] ?? '',
        ];
      }

      $this->config->set('custom_field_mapping', $mapping)->save();
    }

    $event->setResponse(NULL, 200);
  }

}
