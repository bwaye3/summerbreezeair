<?php

namespace Drupal\Tests\entity_clone\Functional;

use Drupal\comment\Entity\Comment;
use Drupal\comment\Tests\CommentTestTrait;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Tests\comment\Functional\CommentTestBase;

/**
 * Create a comment and test a clone.
 *
 * @group entity_clone
 */
class EntityCloneCommentTest extends CommentTestBase {

  use CommentTestTrait;
  use StringTranslationTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'entity_clone',
    'block',
    'comment',
    'node',
    'history',
    'field_ui',
    'datetime',
  ];

  /**
   * Theme to enable by default.
   *
   * @var string
   */
  protected $defaultTheme = 'claro';

  /**
   * Permissions to grant admin user.
   *
   * @var array
   */
  protected $permissions = [
    'administer content types',
    'administer comments',
    'administer comment types',
    'administer comment fields',
    'administer comment display',
    'skip comment approval',
    'post comments',
    'access comments',
    'access user profiles',
    'access content',
    'clone comment entity',
  ];

  /**
   * Sets the test up.
   */
  protected function setUp(): void {
    parent::setUp();

    $this->adminUser = $this->drupalCreateUser($this->permissions);
    $this->drupalLogin($this->adminUser);
  }

  /**
   * Test comment entity clone.
   */
  public function testCommentEntityClone() {
    $subject = 'Test comment for clone';
    $body = $this->randomMachineName();
    $comment = $this->postComment($this->node, $body, $subject, TRUE);
    $this->drupalGet('entity_clone/comment/' . $comment->id());

    $this->submitForm([], 'Clone');

    $comments = \Drupal::entityTypeManager()
      ->getStorage('comment')
      ->loadByProperties([
        'subject' => $subject . ' - Cloned',
        'comment_body' => $body,
      ]);
    $comments = reset($comments);
    $this->assertInstanceOf(Comment::class, $comments, 'Test comment cloned found in database.');
  }

}
