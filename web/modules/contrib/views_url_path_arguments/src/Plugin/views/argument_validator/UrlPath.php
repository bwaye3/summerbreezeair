<?php

namespace Drupal\views_url_path_arguments\Plugin\views\argument_validator;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\views\Plugin\views\argument_validator\ArgumentValidatorPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Convert an entity id to its url path.
 *
 * @ViewsArgumentValidator(
 *   id = "views_url_path",
 *   title = @Translation("Entity ID from URL path alias")
 * )
 */
class UrlPath extends ArgumentValidatorPluginBase {

  /**
   * The route match.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Language manager for retrieving the default langcode when none is specified.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Constructs a new Tid instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $pluginId
   *   The plugin_id for the plugin instance.
   * @param mixed $pluginDefinition
   *   The plugin implementation definition.   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The route match.
   * @param \Drupal\Core\Language\LanguageManagerInterface $languageManager
   *   The language manager.
   */
  public function __construct(array $configuration, $pluginId, $pluginDefinition, RouteMatchInterface $routeMatch, LanguageManagerInterface $languageManager) {
    parent::__construct($configuration, $pluginId, $pluginDefinition);

    $this->routeMatch = $routeMatch;
    $this->languageManager = $languageManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $pluginId, $pluginDefinition) {
    return new static(
      $configuration,
      $pluginId,
      $pluginDefinition,
      $container->get('current_route_match'),
      $container->get('language_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['provide_static_segments'] = ['default' => TRUE];
    $options['segments'] = ['default' => ''];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $formState) {
    $form['provide_static_segments'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Provide a static url segment as the prefix to the alias?'),
      '#default_value' => $this->options['provide_static_segments'],
    ];
    $form['segments'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Segments'),
      '#description' => $this->t('Without leading and/or trailing slashes.'),
      '#default_value' => $this->options['segments'],
      '#states' => [
        'visible' => [
          ':input[name="options[argument_default][views_url_path][provide_static_segments]"]' => ['checked' => TRUE],
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function validateArgument($argument) {

    // Is it already the entity id?
    if (ctype_digit($argument)) {
      $this->argument->argument = $argument;
      return TRUE;
    }

    $alias = '/';
    if ($this->options['provide_static_segments']) {
      $alias .= $this->options['segments'] . '/';
    }

    $alias .= $argument;
    $langcode = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_URL)->getId();

    $canonicalPath = '';
    if (\Drupal::hasService('path_alias.repository')) {
      if ($alias = \Drupal::service('path_alias.repository')->lookupByAlias($alias, $langcode)) {
        $canonicalPath = $alias['path'];
      }
    }
    else {
      $canonicalPath = \Drupal::service('path.alias_storage')->lookupPathSource($alias, $langcode);
    }

    $entity_id = substr($canonicalPath, strrpos($canonicalPath, '/') + 1);
    if (ctype_digit($entity_id)) {
      $this->argument->argument = $entity_id;
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

}
