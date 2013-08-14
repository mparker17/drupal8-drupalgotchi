<?php

/**
 * @file
 * Contains \Drupal\drupalgotchi\Plugin\Block\DrupalgotchiBlock.
 */

namespace Drupal\drupalgotchi\Plugin\Block;

use Drupal\block\BlockBase;
use Drupal\Component\Annotation\Plugin;
use Drupal\Core\Annotation\Translation;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Action\ActionManager;
use Drupal\Core\StringTranslation\TranslationManager;
use Drupal\Core\Config\Config;

use Drupal\drupalgotchi\Form\ResetForm;

/**
 * Provides a Drupalgotchi reset form.
 *
 * @Plugin(
 *  id = "drupalgotchi_reset",
 *  admin_label = @Translation("Reset Drupalgotchi"),
 *  module = "drupalgotchi"
 * )
 */
class ResetBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The actions system.
   *
   * @var \Drupal\Core\Action\ActionManager
   */
  protected $actionsManager;

  /**
   * The translation system.
   *
   * @var \Drupal\Core\Translation\TranslationManager
   */
  protected $translation;

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
      $container->get('config.factory')->get('drupalgotchi.settings'),
      $container->get('string_translation'),
      $container->get('plugin.manager.action')
    );
  }

  public function __construct(array $configuration, $plugin_id, array $plugin_definition, KeyValueStoreInterface $state, Config $config, TranslationManager $translation_manager, ActionManager $action_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->state = $state;
    $this->config = $config;
    $this->translation = $translation_manager;
    $this->actionsManager = $action_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form_to_display = new ResetForm($this->actionsManager, $this->translation, $this->config);
    return drupal_get_form($form_to_display);
  }
}
