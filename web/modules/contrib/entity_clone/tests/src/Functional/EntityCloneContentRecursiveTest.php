<?php

namespace Drupal\Tests\entity_clone\Functional;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\Entity\Term;
use Drupal\Tests\node\Functional\NodeTestBase;

/**
 * Create a content and test a clone.
 *
 * @group entity_clone
 */
class EntityCloneContentRecursiveTest extends NodeTestBase {

  use StringTranslationTrait;
  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['entity_clone', 'block', 'node', 'datetime'];

  /**
   * Theme to enable by default.
   *
   * @var string
   */
  protected $defaultTheme = 'claro';

  /**
   * Profile to install.
   *
   * @var string
   */
  protected $profile = 'standard';

  /**
   * Permissions to grant admin user.
   *
   * @var array
   */
  protected $permissions = [
    'bypass node access',
    'administer nodes',
    'clone node entity',
  ];

  /**
   * A user with permission to bypass content access checks.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * Sets the test up.
   */
  protected function setUp(): void {
    parent::setUp();

    $this->adminUser = $this->drupalCreateUser($this->permissions);
    $this->drupalLogin($this->adminUser);
  }

  /**
   * Test clone a content entity with another entities attached.
   */
  public function testContentEntityClone() {

    $term_title = $this->randomMachineName(8);
    $term = Term::create([
      'vid' => 'tags',
      'name' => $term_title,
    ]);
    $term->save();

    $node_title = $this->randomMachineName(8);
    $node = Node::create([
      'type' => 'article',
      'title' => $node_title,
      'field_tags' => [
        'target_id' => $term->id(),
      ],
    ]);
    $node->save();

    $settings = [
      'taxonomy_term' => [
        'default_value' => 1,
        'disable' => 0,
        'hidden' => 0,
      ],
    ];
    \Drupal::service('config.factory')->getEditable('entity_clone.settings')->set('form_settings', $settings)->save();
    $this->drupalGet('entity_clone/node/' . $node->id());

    $this->submitForm([
      'recursive[node.article.field_tags][references][' . $term->id() . '][clone]' => 1,
    ], 'Clone');

    $nodes = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadByProperties([
        'title' => $node_title . ' - Cloned',
      ]);
    /** @var \Drupal\node\Entity\Node $node */
    $node = reset($nodes);
    $this->assertInstanceOf(Node::class, $node, 'Test node cloned found in database.');

    $terms = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties([
        'name' => $term_title . ' - Cloned',
      ]);
    /** @var \Drupal\taxonomy\Entity\Term $term */
    $term = reset($terms);
    $this->assertInstanceOf(Term::class, $term, 'Test term referenced by node cloned too found in database.');

    $node->delete();
    $term->delete();

    $nodes = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadByProperties([
        'title' => $node_title,
      ]);
    $node = reset($nodes);
    $this->assertInstanceOf(Node::class, $node, 'Test original node found in database.');

    $terms = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadByProperties([
        'name' => $term_title,
      ]);
    $term = reset($terms);
    $this->assertInstanceOf(Term::class, $term, 'Test original term found in database.');
  }

}
