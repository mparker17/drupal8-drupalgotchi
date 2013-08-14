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
  private $config;
  private $state;
  private $translation_manager;

  public function __construct(ConfigFactory $config_factory, KeyValueStoreInterface $state, TranslationManager $translation_manager) {
    $this->config = $config_factory->get('drupalgotchi.settings');
    $this->state = $state;
    $this->translation_manager = $translation_manager;
  }

  /**
   * Responds to kernel event to set happiness level.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   The system event.
   */
  public function onKernelRequestSetHappiness(GetResponseEvent $event) {
    // Tell if we're the main request.
    if ($event->getRequestType() !== KernelInterface::MASTER_REQUEST) {
      return;
    }
    // Note: this doesn't work.
    if ($event->getRequest()->attributes->get('_controller') !== 'controller.page:content') {
      return;
    }

    $happiness = $this->state->get('drupalgotchi.happiness') ?: 0;

    // If the user can make Drupalgotchi happy, increase happiness.
    if ($event->getRequest()->attributes->get('_account')->hasPermission('make drupalgotchi happy')) {
      $happiness++;
    }
    // Otherwise, decrease happiness.
    else {
      $happiness--;
    }

    $this->state->set('drupalgotchi.happiness', $happiness);
  }

  /**
   * Responds to kernel event to display level.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   The system event.
   */
  public function onKernelRequestShowHappiness(GetResponseEvent $event) {
    $happiness = $this->state->get('drupalgotchi.happiness') ?: 0;

    // Show a message if the Drupalgotchi's happiness is <= 0.
    if ($happiness <= 0) {
      drupal_set_message($this->translation_manager->translate("@name misses it's owner.", array(
        '@name' => $this->config->get('name'),
      )));
    }

    drupal_set_message($this->translation_manager->translate("Currently happiness is %happiness.", array(
      '%happiness' => $happiness,
    )));
  }

  /**
   * Registers event subscribers.
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = array('onKernelRequestSetHappiness', 5);
    $events[KernelEvents::REQUEST][] = array('onKernelRequestShowHappiness', 2);
    return $events;
  }

}

