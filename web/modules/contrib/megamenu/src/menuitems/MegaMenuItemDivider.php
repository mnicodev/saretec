<?php

namespace Drupal\megamenu\menuitems;

use Drupal\Component\Utility\Html;

/**
 * Class MegaMenuItemDivider (a horizontal rule).
 *
 * @package Drupal\megamenu
 */
class MegaMenuItemDivider extends MegaMenuItemBase {

  /**
   * {@inheritdoc}
   */
  function generateStartElement() {
    return '<li class="megamenu-divider"><hr/>';
  }

}
