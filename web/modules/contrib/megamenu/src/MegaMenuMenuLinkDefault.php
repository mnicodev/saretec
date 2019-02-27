<?php

namespace Drupal\megamenu;

use Drupal\Core\Menu\MenuLinkDefault;

/**
 * Provides a default implementation for menu link plugins.
 */
class MegaMenuMenuLinkDefault extends MenuLinkDefault {

  /**
   * {@inheritdoc}
   */
  protected $overrideAllowed = [
    'menu_name' => 1,
    'parent' => 1,
    'weight' => 1,
    'expanded' => 1,
    'enabled' => 1,
    // MegaMenu: Allow override of this variable.
    'options' => 1,
  ];

}
