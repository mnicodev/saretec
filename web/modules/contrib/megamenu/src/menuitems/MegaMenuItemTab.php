<?php

namespace Drupal\megamenu\menuitems;

use Drupal\Component\Utility\Html;

/**
 * Class MegaMenuItemTab.
 *
 * @package Drupal\megamenu
 */
class MegaMenuItemTab extends MegaMenuItemBase {

  protected $type = 'tab';

  /**
   * {@inheritdoc}
   */
  function init() {
    if ($this->getParentItem() && $this->getParentItem()->getType() == 'tabs') {
      $this->getParentItem()->setupTab($this);
    }
  }

  /**
   * {@inheritdoc}
   */
  function generateStartElement() {
    $item_output = '';
    $class_names = $value = '';

    // Tab.
    $this->itemClasses[] = 'megamenu-tabs__tab';

    // Setup classes.
    $this->setupClassItemDefaults();
    $this->setupClassId();
    $this->setupClassItemDisplay();
    $this->setupClassLayoutColumns();
    $this->setupClassResponsive();
    $this->setupClassItemType();

    if (!($this->getOption('disable_submenu_on_mobile') && megamenu_is_mobile())) {
      $this->itemClasses[] = 'megamenu-item--has-submenu-drop';
    }

    $class_names = $this->filterItemClasses();

    // Setup id.
    $id = $this->filterItemId();

    // Setup trigger.
    $this->setupTrigger();

    // Attributes.
    $atts = ' ';
    foreach ($this->itemAttributes as $att => $val) {
      $atts .= $att . '="' . $val . '" ';
    }

    // Item LI.
    $item_output .= '<li' . $id . $class_names . $atts . '>';

    // Anchor.
    $atts = $this->buildAnchorAtts();
    $item_output .= $this->generateAnchor($atts);

    // Custom content.
    $item_output .= $this->generateCustomContent();

    return $item_output;
  }

  /**
   * {@inheritdoc}
   */
  function generateEndElement() {
    $item_output = '</li>';
    return $item_output;
  }

  /**
   * {@inheritdoc}
   */
  function setupTrigger() {
    // Item trigger.
    $trigger = $this->getOption('item_trigger');

    // If auto, get trigger from tabs group.
    if (!$trigger || $trigger == 'auto') {
      $trigger = $this->getParentItem()->getOption('tabs_trigger');
    }

    if ($trigger && $trigger != 'auto') {
      $this->itemAttributes['data-megamenu-trigger'] = $trigger;
    }
  }

  /**
   * {@inheritdoc}
   */
  function setupClassLayoutColumns() {
    if ($this->depth > 0) {
      $parent_submenu_type = $this->getParentItem()->getOption('submenu_type_calc');
      // No columns in flyouts.
      if ($parent_submenu_type == 'flyout') {
        return;
      }
    }

    $cols = $this->getOption('columns');

    if ($cols == 'auto') {
      $tab_layout = $this->getParentItem()->getOption('tab_layout');
      if ($tab_layout == 'right' || $tab_layout == 'left') {
        $cols = 'full';
      }
      elseif ($this->depth > 0 && $cols == 'auto') {
        $cols = $this->getParentItem()->getOption('submenu_column_default');
      }
    }

    $this->itemClasses[] = 'megamenu-column megamenu-column--' . $cols;

    if ($this->getOption('submenu_column_default') == 'auto') {
      $this->settings['submenu_column_default'] = $this->getParentItem()->getOption('submenu_column_default');
    }
  }

}
