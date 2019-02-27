<?php

namespace Drupal\megamenu\menuitems;

use Drupal\Component\Utility\Html;

/**
 * Class MegaMenuItemRow.
 *
 * @package Drupal\megamenu
 */
class MegaMenuItemRow extends MegaMenuItemBase {

  protected $type = 'row';

  /**
   * {@inheritdoc}
   */
  function init() {
    if ($this->getDepth() == 0) {
      return '<!-- Rows can only be used in submenus -->';
    }
  }

  /**
   * {@inheritdoc}
   */
  function generateStartElement() {
    $class_names = '';

    // If submenu column default is auto, inherit from parent.
    if ($this->getOption('submenu_column_default') == 'auto') {
      $this->settings['submenu_column_default'] = $this->getParentItem()->getOption('submenu_column_default');
    }

    // Row submenus should be like if they were in a mega submenu.
    $this->settings['submenu_type_calc'] = 'mega';

    $this->itemClasses[] = 'megamenu-item--type-' . $this->type;
    $this->itemClasses[] = 'megamenu-row';
    $this->itemClasses[] = 'megamenu-row--' . Html::cleanCssIdentifier($this->getId(), megamenu_get_clean_css_filter());
    $this->setupClassId();
    if ($this->getOption('grid_row')) {
      $this->itemClasses[] = 'megamenu-row--grid';
    }
    if ($this->getOption('submenu_column_autoclear')) {
      $this->itemClasses[] = 'megamenu-autoclear';
    }

    $class_names = $this->filterItemClasses();

    return '<ul' . $class_names . '">';
  }

  /**
   * {@inheritdoc}
   */
  function generateEndElement() {
    $item_output = '</ul>';
    return $item_output;
  }

  /**
   * {@inheritdoc}
   */
  function generateSubmenuWrapperStart() {
    return '';
  }

  /**
   * {@inheritdoc}
   */
  function generateSubmenuWrapperEnd() {
    return '';
  }

}
