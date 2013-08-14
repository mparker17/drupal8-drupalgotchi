<?php

/**
 * @file
 * Contains \Drupal\drupalgotchi\Plugin\Block\DrupalgotchiBlock.
 */

namespace Drupal\drupalgotchi\Plugin\Block;

use Drupal\block\BlockBase;
use Drupal\Component\Annotation\Plugin;
use Drupal\Core\Annotation\Translation;

use Drupal\Core\KeyValueStore\KeyValueStoreInterface;
use Drupal\Core\Config\Config;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Drupalgotchi status block.
 *
 * @Plugin(
 *  id = "drupalgotchi_status",
 *  admin_label = @Translation("Drupalgotchi status"),
 *  module = "drupalgotchi"
 * )
 */
class DrupalgotchiBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The state system.
   *
   * @var \Drupal\Core\KeyValueStore\KeyValueStoreInterface
   */
  protected $state;

  /**
   * The configuration object.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, array $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('state'),
      $container->get('config.factory')->get('drupalgotchi.settings')
    );
  }

  public function __construct(array $configuration, $plugin_id, array $plugin_definition, KeyValueStoreInterface $state, Config $config) {
    $this->state = $state;
    $this->config = $config;
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return array(
      '#theme' => 'drupalgotchi_status_block',
      '#happiness' => $this->state->get('drupalgotchi.attention'),
      '#name' => $this->config->get('name'),
    );
  }
}
