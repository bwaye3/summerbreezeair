<?php

namespace Drupal\Tests\entity_clone\Functional;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Tests\BrowserTestBase;
use Drupal\views\Entity\View;

/**
 * Create a view and test a clone.
 *
 * @group entity_clone
 */
class EntityCloneViewTest extends BrowserTestBase {

  use StringTranslationTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['entity_clone', 'views', 'views_ui'];

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
    'clone view entity',
    'administer views',
  ];

  /**
   * An administrative user with permission to configure views settings.
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
   * Test view entity clone.
   */
  public function testViewEntityClone() {
    $edit = [
      'id' => 'test_view_cloned',
      'label' => 'Test view cloned',
    ];
    $this->drupalGet('entity_clone/view/who_s_new');
    $this->submitForm($edit, 'Clone');

    $views = \Drupal::entityTypeManager()
      ->getStorage('view')
      ->loadByProperties([
        'id' => $edit['id'],
      ]);
    $view = reset($views);
    $this->assertInstanceOf(View::class, $view, 'Test default view cloned found in database.');
  }

}
