<?php
/**
 * @file
 * A settings form for the Drupalgotchi.
 */

use Drupal\Core\Config\Config;
use Drupal\system\SystemConfigFormBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SettingsForm extends SystemConfigFormBase {
  protected $config;

  public function __construct(Config $config) {
    $this->config = $config;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory')->get('drupalgotchi.settings')
    );
  }

  public function getFormID() {
    return 'drupalgotchi_settings';
  }

  public function buildForm(array $form, array &$form_state) {
    $form['name'] = array(
      '#type' => 'textfield',
      '#title' => t('Name'),
      '#description' => t("The name of your site's persona."),
      '#required' => TRUE,
      '#default_value' => $this->config->get('name'),
    );

    $form['needy'] = array(
      '#type' => 'range',
      '#title' => t('Neediness'),
      '#description' => t('How needy your site is'),
      '#required' => TRUE,
      '#default_value' => $this->config->get('needy'),
    );

    return parent::buildForm($form, $form_state);
  }
}
