<?php

namespace Drupal\storychief\Plugin\StoryChiefFieldHandler;

use Drupal\storychief\Plugin\FieldHandlerType\BaseFieldHandlerType;

/**
 * Class AuthorStoryChiefFieldHandler.
 *
 * Set the author as taxonomy terms based on its email.
 *
 * @StoryChiefFieldHandler(
 *   id = "author",
 *   label = @Translation("Handle the author field."),
 *   drupal_field_name = "uid",
 * )
 */
class AuthorStoryChiefFieldHandler extends BaseFieldHandlerType {

  /**
   * {@inheritdoc}
   */
  public function getValue() {
    $original_value = parent::getValue();

    /** @var \Drupal\user\Entity\User $user */
    if ($user = user_load_by_mail($original_value['data']['email'])) {
      return $user->id();
    }

    // If worst comes to worst, default the author as the root user.
    return $this->config->get('default_owner') ?? 1;
  }

}
