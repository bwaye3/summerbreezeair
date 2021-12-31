<?php

/**
 * @file
 * Hooks for the entity_usage module.
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Alter the node type machine name to be created.
 *
 * @param string $node_type
 *   The node type to create.
 * @param array $payload
 *   The json decoded payload received from StoryChief.
 */
function hook_storychief_node_type_alter(string &$node_type, array $payload) {
}

/**
 * Alter the StoryChief payload.
 *
 * @param array $payload
 *   The json decoded payload received from StoryChief.
 */
function hook_storychief_payload_alter(array &$payload) {
}

/**
 * @} End of "addtogroup hooks".
 */
