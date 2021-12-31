<?php

namespace Drupal\storychief\Access;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Routing\Access\AccessInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class StoryChiefAccessCheck.
 *
 * Validates the hmac received with the request.
 */
class StoryChiefAccessCheck implements AccessInterface {

  /**
   * The json serializer service.
   *
   * @var \Drupal\Component\Serialization\Json
   */
  protected $jsonSerializer;

  /**
   * A configuration object containing story chief's config.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * StorychiefAccessCheck constructor.
   *
   * @param \Drupal\Component\Serialization\Json $json
   *   The json serializer service.
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   The config factory service.
   */
  public function __construct(Json $json, ConfigFactory $config_factory) {
    $this->jsonSerializer = $json;
    $this->config = $config_factory->get('storychief.settings');
  }

  /**
   * Allows access only if the the sent hmac matches the calculated one.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   A request object.
   *
   * @return \Drupal\Core\Access\AccessResult
   *   Whether or not the access is granted.
   */
  public function access(Request $request) {
    $payload = $request->getContent();
    if (!$payload) {
      return AccessResult::forbidden('Empty request.');
    }

    // To verify if the payload comes from an authorized provider, we compared
    // the "mac" value in the payload, with one we calculate ourselves.
    $data = $this->jsonSerializer->decode($payload);

    // Forbid access if either event or mac is missing.
    if (!isset($data['meta']['event'], $data['meta']['mac'])) {
      return AccessResult::forbidden('Missing event or mac field.');
    }

    $request_hmac = $data['meta']['mac'];
    unset($data['meta']['mac']);
    // Do not use the JSON serializer's encode method, as escaping some
    // characters makes the validation fail.
    $hmac = hash_hmac('sha256', json_encode($data), $this->config->get('api_key'));

    if (!hash_equals($request_hmac, $hmac)) {
      return AccessResult::forbidden('Hmac validation failed');
    }

    return AccessResult::allowed();
  }

}
