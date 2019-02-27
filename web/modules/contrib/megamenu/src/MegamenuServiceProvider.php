<?php

namespace Drupal\megamenu;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Symfony\Component\DependencyInjection\Reference;

/**
 * MegaMenu service provider implementation.
 *
 * Modifies the menu link manager service.
 *
 * The class and file are intentionally prefixed with Megamenu instead of MegaMenu
 * because current Drupal logic use camelcase for modules which names contains
 * underscores (for example, mega_menu would be prefixed with MegaMenu).
 */
class MegamenuServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    // Overrides plugin.manager.menu.link class so that we can save the menu item options.
    $definition = $container->getDefinition('plugin.manager.menu.link');
    $definition->setClass('Drupal\megamenu\MegaMenuMenuLinkManager');

    // Overrides menu_link.static.overrides service so that we can save options statically.
    $definition = $container->getDefinition('menu_link.static.overrides');
    $definition->setClass('Drupal\megamenu\MegaMenuStaticMenuLinkOverrides');
  }

}
