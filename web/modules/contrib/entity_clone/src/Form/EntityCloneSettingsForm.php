<?php

namespace Drupal\entity_clone\Form;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provide the settings form for entity clone.
 */
class EntityCloneSettingsForm extends ConfigFormBase implements ContainerInjectionInterface {

  /**
   * The entity clone settings manager.
   *
   * @var \Drupal\entity_clone\EntityCloneSettingsManager
   */
  protected $entityCloneSettingsManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->entityCloneSettingsManager = $container->get('entity_clone.settings.manager');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getEditableConfigNames() {
    return ['entity_clone.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'entity_clone_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#tree'] = TRUE;

    $form['form_settings'] = [
      '#tree' => TRUE,
      '#type' => 'fieldset',
      '#title' => $this->t('Clone form settings'),
      '#description' => $this->t("
        For each type of child entity (the entity that's referenced by the entity being
        cloned), please set your cloning preferences. This will affect the clone form presented to users when they
        clone entities. Default behavior for whether or not the child entities should be cloned is specified in
        the left-most column.  To prevent users from altering behavior for each type when they're actually cloning
        (but still allowing them to see what will happen), use the middle column. The right-most column can be used
        to hide the form options from users completely. This will run the clone operation with the defaults set here
        (in the left-most column). See the clone form (by cloning an entity) for more information.
      "),
      '#open' => TRUE,
      '#collapsible' => FALSE,
    ];

    $form['form_settings']['table'] = [
      '#type' => 'table',
      '#header' => [
        'label' => $this->t('Label'),
        'default_value' => $this->t('Checkboxes default value'),
        'disable'  => $this->t('Disable checkboxes'),
        'hidden' => $this->t('Hide checkboxes'),
      ],
    ];

    foreach ($this->entityCloneSettingsManager->getContentEntityTypes() as $type_id => $type) {
      $form['form_settings']['table'][$type_id] = [
        'label' => [
          '#type' => 'label',
          '#title' => $this->t('@type', [
            '@type' => $type->getLabel(),
          ]),
        ],
        'default_value' => [
          '#type' => 'checkbox',
          '#default_value' => $this->entityCloneSettingsManager->getDefaultValue($type_id),
        ],
        'disable' => [
          '#type' => 'checkbox',
          '#default_value' => $this->entityCloneSettingsManager->getDisableValue($type_id),
        ],
        'hidden' => [
          '#type' => 'checkbox',
          '#default_value' => $this->entityCloneSettingsManager->getHiddenValue($type_id),
        ],
      ];
    }

    $form['take_ownership'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Take ownership'),
      '#description' => $this->t('Whether the "Take ownership" option should be checked by default on the entity clone form.'),
      '#default_value' => $this->entityCloneSettingsManager->getTakeOwnershipSetting(),
    ];

    $form['no_suffix'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Exclude Cloned'),
      '#description' => $this->t('Exclude " - Cloned" from title of cloned entity.'),
      '#default_value' => $this->entityCloneSettingsManager->getExcludeClonedSetting(),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->entityCloneSettingsManager->setFormSettings($form_state->getValue('form_settings'));
    $this->entityCloneSettingsManager->setTakeOwnershipSettings($form_state->getValue('take_ownership'));
    $this->entityCloneSettingsManager->setExcludeClonedSetting($form_state->getValue('no_suffix'));
    parent::submitForm($form, $form_state);
  }

}
