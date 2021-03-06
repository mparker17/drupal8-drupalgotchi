<?php

namespace Drupal\drupalgotchi\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelInterface;

use Drupal\Core\KeyValueStore\KeyValueStoreInterface;
use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\StringTranslation\TranslationManager;

/**
 * Drupalgotchi request events.
 */
class DrupalgotchiSubscriber implements EventSubscriberInterface {

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
   * The translation system.
   *
   * @var \Drupal\Core\StringTranslation\TranslationManager
   */
  protected $translator;

  /**
   *
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   The configuration system.
   * @param \Drupal\Core\KeyValueStore\KeyValueStoreInterface $state
   *   The state storage system.
   * @param \Drupal\Core\StringTranslation\TranslationManager $translator
   *   The translation system.
   */
  public function __construct(ConfigFactory $config_factory, KeyValueStoreInterface $state, TranslationManager $translator) {
    $this->config = $config_factory->get('drupalgotchi.settings');
    $this->state = $state;
    $this->translator = $translator;
  }

  /**
   * Responds to kernel event to set happiness level.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   The system event.
   */
  public function onKernelRequestSetHappiness(GetResponseEvent $event) {
    if ($event->getRequestType() != KernelInterface::MASTER_REQUEST) {
      return;
    }

    $attention_quotient = $this->state->get('drupalgotchi.attention') ?: 0;

    if ($event->getRequest()->attributes->get('_account')->hasPermission('make drupalgotchi happy')) {
      $neediness = $this->config->get('needy');
      $change = 10 - $neediness;
      $attention_quotient += $change;
    }
    else {
      $attention_quotient -= 1;
    }

    $this->state->set('drupalgotchi.attention', $attention_quotient);
  }

  /**
   * Registers event subscribers.
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = array('onKernelRequestSetHappiness', 5);
    return $events;
  }

}

