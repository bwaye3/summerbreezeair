<?php

namespace Drupal\storychief\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\storychief\Event\StoryChiefRemoteCallEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class StoryChiefWebhookController.
 *
 * This is the main entry point for data coming from StoryChief. Based on the
 * StoryChief event received, an event is dispatched and intercepted by the
 * right subscriber.
 */
class StoryChiefWebhookController extends ControllerBase {

  /**
   * The request stack service.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * The Json serializer service.
   *
   * @var \Drupal\Component\Serialization\Json
   */
  protected $jsonSerializer;

  /**
   * The event dispatcher service.
   *
   * @var \Drupal\Component\EventDispatcher\ContainerAwareEventDispatcher
   */
  protected $eventDispatcher;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->requestStack = $container->get('request_stack');
    $instance->jsonSerializer = $container->get('serialization.json');
    $instance->eventDispatcher = $container->get('event_dispatcher');

    return $instance;
  }

  /**
   * StoryChief endpoint controller method.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   A Json response containing the success payload or an error message.
   */
  public function handle() {
    // Get the content of the payload and decode it.
    $payload = $this->requestStack->getCurrentRequest()->getContent();
    $data = $this->jsonSerializer->decode($payload);

    // Retrieve the event and get the right event to dispatch.
    $storychief_event_name = strtoupper($data['meta']['event']);
    $event_name = constant("Drupal\storychief\Event\StoryChiefEvents::$storychief_event_name");

    // If no event exists for the sent payload, return an error.
    if (!$event_name) {
      $message = sprintf('The %s event is not supported', $data['meta']['event']);
      return new JsonResponse(['message' => $message], 400);
    }

    // Allow modules to alter the payload.
    \Drupal::moduleHandler()->alter('storychief_payload', $data);

    // If we found one, dispatch it.
    $event = new StoryChiefRemoteCallEvent($data);

    $this->eventDispatcher->dispatch($event_name, $event);

    // And return the response (success or error).
    return $event->getResponse();
  }

}
