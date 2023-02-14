<?php

namespace Drupal\node_sitemap;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ConfigFactoryOverrideInterface;
use Drupal\Core\Config\StorageInterface;

/**
 * Class MyModuleConfig.
 */
class NodeSitemapConfig implements ConfigFactoryOverrideInterface {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a new MyModuleConfig object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * Returns the default configuration for the module.
   *
   * @return array
   *   An array of default configuration values.
   */
  public function defaultConfiguration() {
    return [
      'limit' => 100,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function loadOverrides($names) {
    $overrides = [];
    $config = $this->configFactory->get('mymodule.settings');

    if (in_array('mymodule.settings', $names)) {
      $overrides['mymodule.settings'] = [
        'limit' => $config->get('limit'),
      ];
    }

    return $overrides;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheSuffix() {
    return 'MyModuleConfig';
  }

  /**
   * {@inheritdoc}
   */
  public function createConfigObject($name, $collection = StorageInterface::DEFAULT_COLLECTION) {
    return NULL;
  }

}
