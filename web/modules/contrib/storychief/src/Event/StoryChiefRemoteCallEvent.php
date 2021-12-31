<?php

namespace Drupal\storychief\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class StoryChiefEvent.
 *
 * Event dispatched to the subscribers.
 *
 * @package Drupal\storychief\Event
 */
class StoryChiefRemoteCallEvent extends Event {

  /**
   * The json decoded payload received from StoryChief.
   *
   * @var array
   */
  public $payload;

  /**
   * The response to return to StoryChief.
   *
   * @var \Symfony\Component\HttpFoundation\JsonResponse
   */
  protected $response;

  /**
   * The json serializer service.
   *
   * @var \Drupal\Component\Serialization\Json
   */
  protected $jsonSerializer;

  /**
   * A configuration object containing StoryChief's configuration.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * StoryChiefEvents constructor.
   *
   * @param array $payload
   *   The json decoded payload received from StoryChief.
   */
  public function __construct(array $payload) {
    $this->payload = $payload;
    $this->jsonSerializer = \Drupal::service('serialization.json');
    $this->config = \Drupal::config('storychief.settings');
  }

  /**
   * Gets the response needed by StoryChief.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The response object.
   */
  public function getResponse() {
    return $this->response;
  }

  /**
   * Attach a response to the subscribed event.
   *
   * StoryChief needs a response to acknowledge that a request has been
   * properly processed or failed.
   *
   * @param array|null $data
   *   Array of data to set as response body or null if nothing to return.
   * @param int $status
   *   The status code of the response.
   */
  public function setResponse($data, $status) {
    // Append an mac to the the response based on the content of $data.
    $data['mac'] = hash_hmac('sha256', json_encode($data), $this->config->get('api_key'));
    $this->response = new JsonResponse($data, $status);
  }

}
