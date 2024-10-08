<?php

namespace Drupal\Tests\entity_clone\Functional;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\system\Entity\Action;
use Drupal\Tests\BrowserTestBase;

/**
 * Create an action and test a clone.
 *
 * @group entity_clone
 */
class EntityCloneActionTest extends BrowserTestBase {

  use StringTranslationTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['entity_clone', 'action'];

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
    'administer actions',
    'clone action entity',
  ];

  /**
   * An administrative user with permission to configure actions settings.
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
   * Test action entity clone.
   */
  public function testActionEntityClone() {
    foreach (\Drupal::service('plugin.manager.action')->getDefinitions() as $id => $definition) {
      if (is_subclass_of($definition['class'], '\Drupal\Core\Plugin\PluginFormInterface') && $definition['label'] == 'Send email') {
        $action_key = $id;
        break;
      }
    }

    $edit = [
      'label' => 'Test send email action for clone',
      'id' => 'test_send_email_for_clone',
      'recipient' => 'test@recipient.com',
      'subject' => 'test subject',
      'message' => 'test message',
    ];
    $this->drupalGet("admin/config/system/actions/add/$action_key");
    $this->submitForm($edit, 'Save');

    $actions = \Drupal::entityTypeManager()
      ->getStorage('action')
      ->loadByProperties([
        'id' => $edit['id'],
      ]);
    $action = reset($actions);

    $edit = [
      'label' => 'Test send email action cloned',
      'id' => 'test_send_email_cloned',
    ];
    $this->drupalGet('entity_clone/action/' . $action->id());
    $this->submitForm($edit, 'Clone');

    $actions = \Drupal::entityTypeManager()
      ->getStorage('action')
      ->loadByProperties([
        'id' => $edit['id'],
      ]);
    $action = reset($actions);
    $this->assertInstanceOf(Action::class, $action, 'Test action cloned found in database.');
  }

}
