<?php

namespace Drupal\storychief\Event;

/**
 * Class StoryChiefEvents.
 *
 * Lists the events available for dispatch.
 *
 * @package Drupal\storychief\Event
 */
final class StoryChiefEvents {

  // Dispatch the test event.
  const TEST = 'storychief.webhook.test';

  // Dispatch the publish event.
  const PUBLISH = 'storychief.webhook.publish';

  // Dispatch the update event.
  const UPDATE = 'storychief.webhook.update';

  // Dispatch the delete event.
  const DELETE = 'storychief.webhook.delete';

}
