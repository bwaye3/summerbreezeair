<?php

namespace Drupal\storychief\Plugin\StoryChiefFieldHandler;

use Drupal\storychief\Exceptions\UsedAliasException;
use Drupal\storychief\Plugin\FieldHandlerType\BaseFieldHandlerType;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SlugStoryChiefFieldHandler.
 *
 * Create a path alias from the seo_slug field.
 *
 * @StoryChiefFieldHandler(
 *   id = "seo_slug",
 *   label = @Translation("Handle the path alias"),
 *   drupal_field_name = "path",
 * )
 */
class SlugStoryChiefFieldHandler extends BaseFieldHandlerType {

  /**
   * The Path Alias Service.
   *
   * @var \Drupal\path_alias\AliasManager
   */
  protected $pathAliasService;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->pathAliasService = $container->get('path_alias.manager');

    return $instance;
  }

  /**
   * Set the node alias.
   *
   * @throws \Drupal\storychief\Exceptions\UsedAliasException
   */
  public function set() {
    $alias = null;

    if (!empty($this->getValue())) {
      // TODO: unused path_auto_prefix configuration atm.
      $prefix = $this->config->get('path_auto_prefix') ?? '';
      $alias = $prefix . '/' . $this->getValue();

      if (!$this->isAliasAvailable($alias)) {
        throw new UsedAliasException("A node already exists with alias '$alias'");
      }
    }

    $this->setValue([
      'alias' => $alias,
      'pathauto' => $alias ? 0 : 1,
    ]);

    parent::set();
  }

  /**
   * Check availability of an alias.
   *
   * @param string $alias
   *   The alias to check.
   *
   * @return bool
   *   Whether or not it is available to use.
   */
  protected function isAliasAvailable(string $alias) {
    $node_path = $this->pathAliasService->getPathByAlias($alias, $this->getEntity()
      ->language()
      ->getId());
    return (
      $alias === $node_path
      || $node_path === '/node/' . $this->getEntity()->id()
    );
  }

}
