<?php

namespace Drupal\megamenu;

use Drupal\Core\Menu\MenuLinkManager;
use Drupal\Component\Utility\NestedArray;

/**
 * Manages discovery, instantiation, and tree building of menu link plugins.
 *
 * This manager finds plugins that are rendered as menu links.
 */
class MegaMenuMenuLinkManager extends MenuLinkManager {

  /**
   * {@inheritdoc}
   */
  protected function processDefinition(array &$definition, $plugin_id) {
    // MegaMenu: use the megamenu link class override.
    $this->defaults['class'] = 'Drupal\megamenu\MegaMenuMenuLinkDefault';

    parent::processDefinition($definition, $plugin_id);
  }

}
