<?php

namespace Drupal\storychief\Plugin\FieldHandlerType;

use Drupal\Component\Render\PlainTextOutput;
use Drupal\Core\File\FileSystemInterface;
use Drupal\storychief\Exceptions\ImageStoryChiefException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class to handle taxonomy terms.
 *
 * @package Drupal\storychief\Plugin\StorychiefFieldHandler
 */
class ImageFieldHandlerType extends BaseFieldHandlerType {

  /**
   * The FileSystem service.
   *
   * @var \Drupal\Core\File\FileSystem
   */
  protected $fileSystem;

  /**
   * The token services.
   *
   * @var \Drupal\Core\Utility\Token
   */
  protected $token;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->fileSystem = $container->get('file_system');
    $instance->token = $container->get('token');

    return $instance;
  }

  /**
   * {@inheritdoc}
   *
   * @return \Drupal\file\FileInterface|null
   *   The loaded image file.
   *
   * @throws \Drupal\storychief\Exceptions\ImageStoryChiefException
   */
  public function getValue() {
    $value = parent::getValue();

    if (empty($value)) {
      return NULL;
    }

    return $this->retrieveFile($value, basename($value));
  }

  /**
   * Retrieve a remote file, copy it into the right file system.
   *
   * @param string $source_url
   *   Url to target to retrieve the file.
   * @param string $image_name
   *   Name of the image to download.
   *
   * @return \Drupal\file\FileInterface
   *   A file entity.
   *
   * @throws \Drupal\storychief\Exceptions\ImageStorychiefException
   */
  protected function retrieveFile(string $source_url, string $image_name) {
    // Determine the scheme and destination directory based on the image field's
    // configuration.
    $settings = $this->getFieldDefinition()->getSettings();
    $destination_directory = trim($settings['file_directory'], '/');
    $destination_directory = PlainTextOutput::renderFromHtml($this->token->replace($destination_directory));
    $destination_directory = $settings['uri_scheme'] . '://' . $destination_directory;
    // Final destination name of the image.
    $destination = $destination_directory . '/' . $image_name;

    // Make sure we can write into the directory.
    if (!$this->fileSystem->prepareDirectory($destination_directory, FileSystemInterface:: CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS)) {
      throw new ImageStoryChiefException("Could not write to directory '$destination_directory'");
    }

    // Retrieve the file and make a file entity out of it.
    /** @var \Drupal\file\FileInterface $file */
    $file = system_retrieve_file($source_url, $destination, TRUE, FileSystemInterface::EXISTS_REPLACE);

    if (!$file) {
      throw new ImageStoryChiefException("Encountered a problem while loading image '$source_url'");
    }

    return $file;
  }

}
