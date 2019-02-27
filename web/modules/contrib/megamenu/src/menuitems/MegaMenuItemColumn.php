<?php

namespace Drupal\megamenu\menuitems;

use Drupal\Component\Utility\Html;

/**
 * Class MegaMenuItemColumn.
 *
 * @package Drupal\megamenu
 */
class MegaMenuItemColumn extends MegaMenuItemBase {

  protected $type = 'column';

  /**
   * {@inheritdoc}
   */
  function generateStartElement() {
    // Setup classes.
    $this->setupClassItemDefaults();
    $this->setupClassId();
    $this->setupClassLevel();
    $this->setupClassLayoutColumns();
    $this->setupClassAlignment();
    $this->setupClassSubmenu();
    $this->setupClassDisablePadding();
    $this->setupClassResponsive();
    $this->setupClassItemType();

    $this->itemClasses[] = 'megamenu-column--'. Html::cleanCssIdentifier($this->getId(), megamenu_get_clean_css_filter());

    $submenu_column_default = $this->getOption('submenu_column_default');
    switch ($submenu_column_default) {
      case 'auto':
      case 'natural':
      case 'full':
        $this->settings['submenu_type'] = 'stack';
        break;
      default:
        $this->settings['submenu_type'] = 'block';
        break;
    }

    return '<li class="' . implode(' ', $this->itemClasses) . '">';
  }

}
