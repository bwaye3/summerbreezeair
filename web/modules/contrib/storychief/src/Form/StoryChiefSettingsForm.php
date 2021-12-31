<?php

namespace Drupal\storychief\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\field\FieldConfigInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure Mailchimp settings for this site.
 */
class StoryChiefSettingsForm extends ConfigFormBase {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * The entity field manager service.
   *
   * @var \Drupal\Core\Entity\EntityFieldManager
   */
  protected $entityFieldManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->entityFieldManager = $container->get('entity_field.manager');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'storychief_admin_settings';
  }

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $config = $this->config('storychief.settings');

    $form['connect'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Connect'),
      'api_key' => [
        '#type' => 'textfield',
        '#title' => $this->t('StoryChief Encryption Key'),
        '#required' => TRUE,
        '#default_value' => $config->get('api_key'),
        '#description' => $this->t('Your encryption key is given when you add a Drupal destination on StoryChief.'),
      ],
      'website_url' => [
        '#type' => 'textfield',
        '#title' => 'Website url',
        '#disabled' => TRUE,
        '#value' => $this->getRequest()->getSchemeAndHttpHost(),
        '#description' => $this->t('Paste this url in your Drupal destination configuration on StoryChief.'),
      ],
    ];

    $form['configure'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Configure'),
      'node_type' => [
        '#type' => 'select',
        '#title' => $this->t('Node type'),
        '#description' => $this->t('Choose a node type where StoryChief must save stories to.'),
        '#empty_option' => $this->t('- Choose a node type -'),
        '#default_value' => $config->get('node_type'),
        '#options' => $this->getEntityBundleDropdown('node_type'),
        '#required' => TRUE,
        '#ajax' => [
          'callback' => '::updateFieldMappingForm',
          'wrapper' => 'field-mapping-container',
          'event' => 'change',
        ],
      ],
      'default_owner' => [
        '#type' => 'entity_autocomplete',
        '#title' => $this->t('Default owner'),
        '#description' => $this->t('Set a default owner for stories. Will be used if the author mapping fails.'),
        '#target_type' => 'user',
        '#default_value' => $config->get('default_owner') ? $this->entityTypeManager->getStorage('user')->load($config->get('default_owner')) : FALSE,
      ],
      'save_unpublished' => [
        '#type' => 'checkbox',
        '#title' => $this->t('Save stories received from StoryChief as Unpublished.'),
        '#description' => $this->t("multichannel publishing won't be possible if checked."),
        '#default_value' => $config->get('save_unpublished'),
      ],
    ];

    $form['field_mapping'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Field mapping'),
      '#attributes' => [
        'id' => 'field-mapping-container',
      ],
      'table' => [
        '#type' => 'container',
        '#prefix' => '<p>' . $this->t("No node type set") . '</p>',
      ],
    ];

    if ($form_state->getValue('node_type') || $config->get('node_type')) {
      $node_type = $form_state->getValue('node_type') ?? $config->get('node_type');

      $fields_definitions = $this->getFieldsConfigFieldsDefinition('node', $node_type);

      $form['field_mapping'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Field mapping'),
        '#attributes' => [
          'id' => 'field-mapping-container',
        ],
        'table' => [
          '#type' => 'container',
          '#prefix' => '<p>Implement a <code>StoryChiefFieldHandler</code> Plugin to add your more field types. More info in <a target="_blank" rel="noopener noreferrer" href="https://help.storychief.io/en/articles/4875855-drupal-8-9-mapping-fields">documentation</a>.</p><table class="sticky-enabled tableheader-processed sticky-table"><thead></thead><tr><th>' . $this->t('Source: StoryChief story') . '</th><th width="50%">' . $this->t('Target: @type node',
              ['@type' => $node_type]) . '</th></tr>',
          '#suffix' => '</table>',
        ],
      ];

      $options = $this->getAvailableListOptions($fields_definitions, ['text_with_summary']);
      $form['field_mapping']['table']['content'] = [
        '#type' => 'container',
        '#prefix' => '<tr class="odd"><td><strong>Content & Excerpt</strong><br /><div class="description"> ' . $this->t('This field must be of type <i>@field</i>.<br />The excerpt will be inserted in the summary and can be used for teaser views.',
            ['@field' => $this->t('Long text and summary')]) . '</div></td><td>',
        'mapping|field_content' => [
          '#type' => 'select',
          '#empty_option' => $this->t('- Choose field -'),
          '#title' => $this->t('Content'),
          '#title_display' => 'attribute',
          '#default_value' => $config->get('mapping.field_content'),
          '#disabled' => empty($options),
          '#required' => FALSE,
          '#options' => $options,
          '#description' => empty($options) ? $this->t('No field of type <i>@type</i> found on <i>@node</i> node',
            [
              '@type' => 'text_with_summary',
              '@node' => $node_type,
            ]
          ) : '',
        ],
        '#suffix' => '</td><tr>',
      ];

      $options = $this->getAvailableListOptions($fields_definitions, ['image']);
      $form['field_mapping']['table']['image'] = [
        '#type' => 'container',
        '#prefix' => '<tr class="even"><td><strong>Cover image</strong><br /><div class="description"> ' . $this->t('The cover image for teaser views and/or header image.') . '</div></td><td>',
        'mapping|field_featured_image' => [
          '#type' => 'select',
          '#empty_option' => $this->t('- Choose field -'),
          '#title' => $this->t('Cover image'),
          '#title_display' => 'attribute',
          '#default_value' => $config->get('mapping.field_featured_image'),
          '#disabled' => empty($options),
          '#options' => $options,
          '#description' => empty($options) ? $this->t('No field of type <i>@type</i> found on <i>@node</i> node',
            [
              '@type' => 'image',
              '@node' => $node_type,
            ]
          ) : '',
        ],
        '#suffix' => '</td><tr>',
      ];

      $options = $this->getAvailableListOptions($fields_definitions, ['entity_reference']);
      $form['field_mapping']['table']['tags'] = [
        '#type' => 'container',
        '#prefix' => '<tr class="odd"><td><strong>Tags</strong><br /><div class="description"> ' . $this->t('Micro-categories for your story used to show related stories.') . '</div></td><td>',
        'mapping|field_tags' => [
          '#type' => 'select',
          '#empty_option' => $this->t('- Choose field -'),
          '#title' => $this->t('Tags'),
          '#title_display' => 'attribute',
          '#default_value' => $config->get('mapping.field_tags'),
          '#disabled' => empty($options),
          '#options' => $options,
          '#description' => empty($options) ? $this->t('No field of type <i>@type</i> found on <i>@node</i> node',
            [
              '@type' => 'entity_reference',
              '@node' => $node_type,
            ]
          ) : '',
        ],
        '#suffix' => '</td><tr>',
      ];

      $options = $this->getAvailableListOptions($fields_definitions, ['entity_reference']);
      $form['field_mapping']['table']['category'] = [
        '#type' => 'container',
        '#prefix' => '<tr class="even"><td><strong>Category</strong><br /><div class="description"> ' . $this->t('The general topic the story can be classified in.<br />Readers can browse specific categories to see all stories in the category.') . '</div></td><td>',
        'mapping|field_category' => [
          '#type' => 'select',
          '#empty_option' => $this->t('- Choose field -'),
          '#title' => $this->t('Category'),
          '#title_display' => 'attribute',
          '#default_value' => $config->get('mapping.field_category'),
          '#disabled' => empty($options),
          '#options' => $options,
          '#description' => empty($options) ? $this->t('No field of type <i>@type</i> found on <i>@node</i> node',
            [
              '@type' => 'entity_reference',
              '@node' => $node_type,
            ]
          ) : '',
        ],
        '#suffix' => '</td><tr>',
      ];

      $custom_fields = $config->get('custom_field_mapping') ?: [];
      if (!empty($custom_fields)) {
        $options = $this->getAvailableListOptions($fields_definitions, [
          'string',
          'string_long',
          'text',
          'text_long',
          'text_with_summary',
          'list',
          'list_string',
          'list_float',
          'list_integer',
          'image',
          'entity_reference',
        ]);
        foreach ($custom_fields as $field_name => $custom_field) {
          $form['field_mapping']['table'][$field_name] = [
            '#type' => 'container',
            '#prefix' => '<tr><td><strong>' . $custom_field['label'] . '</strong><br /><div class="description">key: ' . $field_name . '<br>type: ' . $custom_field['type'] . '</div></td><td>',
            'custom_field_mapping|' . $field_name . '|field' => [
              '#type' => 'select',
              '#empty_option' => $this->t('- Choose field -'),
              '#default_value' => $custom_field['field'],
              '#disabled' => empty($options),
              '#options' => $options,
            ],
            '#suffix' => '</td><tr>',
          ];
        }
      }
    }
    return $form;
  }

  /**
   * Get the entity bundles.
   *
   * @param string $entity_type
   *   The entity type string.
   *
   * @return \Drupal\Core\Entity\EntityInterface[]
   *   Array of entity types.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getEntityBundleDropdown(string $entity_type) {
    $entity_types = $this->entityTypeManager->getStorage($entity_type)
      ->loadMultiple();
    array_walk($entity_types, function (&$entity_type) {
      /** @var \Drupal\Core\Entity\EntityInterface $entity_type */
      $entity_type = $entity_type->label();
    });

    return $entity_types;
  }

  /**
   * Retrieve all possible fieldDefinitions.
   *
   * @param string $entity_type
   *   The entity type.
   * @param string $bundle
   *   The bundle name.
   *
   * @return array
   *   Field definitions.
   */
  private function getFieldsConfigFieldsDefinition(string $entity_type, string $bundle) {
    $fields_definition = $this->entityFieldManager->getFieldDefinitions($entity_type, $bundle);
    return $this->filterFieldConfigFields($fields_definition);
  }

  /**
   * Filter fields on specific types.
   *
   * @param array $fields_definition
   *   Array of fields definitions.
   * @param array $field_types
   *   Optional array of field types.
   *
   * @return array
   *   Filtered fields.
   */
  private function filterFieldConfigFields(array $fields_definition, array $field_types = []) {
    $fields_definition = array_filter($fields_definition, function ($field_definition) {
      return $field_definition instanceof FieldConfigInterface;
    });

    if (!empty($field_types)) {
      $fields_definition = array_filter($fields_definition, function ($field_definition) use ($field_types) {
        return in_array($field_definition->getType(), $field_types);
      });
    }

    return $fields_definition;
  }

  /**
   * Retrieve field options formatted as select options.
   *
   * @param array $fields_definition
   *   Array of fields definitions.
   * @param array $available_field_types
   *   Optional array of field types.
   *
   * @return string[]
   *   Field options formatted as select options.
   */
  private function getAvailableListOptions(array $fields_definition, array $available_field_types = []) {
    $fields_definition = $this->filterFieldConfigFields($fields_definition, $available_field_types);

    return array_map(function ($field_definition) {
      return "{$field_definition->getLabel()} (machine_name: {$field_definition->getName()})";
    }, $fields_definition);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    if (
      $form_state->getTriggeringElement()
      && strpos($form_state->getTriggeringElement()['#id'], 'edit-update-field-mapping') !== FALSE
    ) {
      $form_state->setRebuild();
      return;
    }

    $config = $this->config('storychief.settings');
    $config = $config
      ->set('api_key', $form_state->getValue('api_key'))
      ->set('node_type', $form_state->getValue('node_type'))
      ->set('save_unpublished', $form_state->getValue('save_unpublished'))
      ->set('default_owner', $form_state->getValue('default_owner'));

    foreach ($form_state->getValues() as $input_name => $input_value) {
      if (
        strpos($input_name, 'mapping|') === 0
        || strpos($input_name, 'custom_field_mapping|') === 0
      ) {
        $config = $config->set(str_replace('|', '.', $input_name), $input_value);
      }
    }

    $config->save();
    parent::submitForm($form, $form_state);
  }

  /**
   * Update the field mapping options on entity type change.
   *
   * @param array $form
   *   Array representation of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return mixed
   *   The field mapping options.
   */
  public function updateFieldMappingForm(array &$form, FormStateInterface $form_state) {
    return $form['field_mapping'];
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['storychief.settings'];
  }

}
