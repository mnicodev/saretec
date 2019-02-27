<?php

namespace Drupal\megamenu;

use Drupal\user\Plugin\Menu\LoginLogoutMenuLink;

/**
 * A menu link that shows "Log in" or "Log out" as appropriate.
 */
class MegaMenuLoginLogoutMenuLink extends LoginLogoutMenuLink {

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
