<?php

namespace Drupal\megamenu;

use Drupal\views\Plugin\Menu\ViewsMenuLink;

/**
 * Defines menu links provided by views.
 */
class MegaMenuViewsMenuLink extends ViewsMenuLink {

  /**
   * {@inheritdoc}
   */
  protected $overrideAllowed = [
    'menu_name' => 1,
    'parent' => 1,
    'weight' => 1,
    'expanded' => 1,
    'enabled' => 1,
    'title' => 1,
    'description' => 1,
    // MegaMenu: Allow override of this variable.
    'options' => 1,
  ];

}
