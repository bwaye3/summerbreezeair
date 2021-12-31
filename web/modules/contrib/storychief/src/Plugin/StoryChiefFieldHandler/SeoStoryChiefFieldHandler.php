<?php

namespace Drupal\storychief\Plugin\StoryChiefFieldHandler;

use Drupal\storychief\Exceptions\StoryChiefException;
use Drupal\storychief\Plugin\FieldHandlerType\BaseFieldHandlerType;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SeoStoryChiefFieldHandler.
 *
 * TODO: kill this monstrosity. Saving SEO fields should be done migrated to
 * mapping.
 *
 * @StoryChiefFieldHandler(
 *   id = "seo",
 *   label = @Translation("Handle the seo fields"),
 *   drupal_field_name = null,
 * )
 */
class SeoStoryChiefFieldHandler extends BaseFieldHandlerType {

  const STORYCHIEF_META_TABLE = 'storychief_meta_tags';

  /**
   * The Path Alias Service.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $databaseService;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->databaseService = $container->get('database');

    return $instance;
  }

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\storychief\Exceptions\StoryChiefException
   * @throws \Exception
   */
  public function set() {
    if (!$this->getEntity()->id()) {
      throw new StoryChiefException("Race condition error on SEO field mapping");
    }

    // AMP Meta tag.
    if ($amp_html = $this->getPayload()['amphtml'] ?? NULL) {
      $render_array = [
        '#tag' => 'link',
        '#attributes' => [
          'rel' => 'amphtml',
          'href' => $amp_html,
        ],
      ];
      $this->insertMetaTag('amphtml', $render_array);
    }

    // Title Meta tag.
    if ($title = $this->getPayload()['seo_title'] ?? NULL) {
      $render_array = [
        '#tag' => 'title',
        'content' => ['#plain_text' => $title],
      ];
      $this->insertMetaTag('title', $render_array);
    }

    // Description Meta tag.
    if ($description = $this->getPayload()['seo_description'] ?? NULL) {
      $render_array = [
        '#tag' => 'meta',
        '#attributes' => [
          'name' => 'description',
          'content' => $description,
        ],
      ];
      $this->insertMetaTag('description', $render_array);
    }
  }

  /**
   * Save meta tag in database.
   *
   * @param string $key
   *   The meta key.
   * @param array $render_array
   *   The render array.
   *
   * @throws \Exception
   */
  protected function insertMetaTag(string $key, array $render_array) {
    $this->databaseService
      ->insert(self::STORYCHIEF_META_TABLE)
      ->fields(
        ['nid', 'langcode', 'render_key', 'render_array'],
        [
          $this->getEntity()->id(),
          $this->getEntity()->language()->getId(),
          $key,
          json_encode($render_array),
        ]
      )
      ->execute();
  }

  /**
   * {@inheritdoc}
   */
  public function update() {
    // Delete previous meta info.
    $this->databaseService
      ->delete(self::STORYCHIEF_META_TABLE)
      ->condition('nid', $this->getEntity()->id())
      ->condition('langcode', $this->getEntity()->language()->getId())
      ->execute();

    parent::update();
  }

}
