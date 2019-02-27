<?php

namespace Drupal\megamenu\menuitems;

use Drupal\Component\Utility\Html;

/**
 * Class MegaMenuItemTabs.
 *
 * @package Drupal\megamenu
 */
class MegaMenuItemTabs extends MegaMenuItemBase {

  protected $type = 'tabs';
  protected $manual_content = '';

  /**
   * {@inheritdoc}
   */
  function init() {
    $this->submenuType = 'tabs-group';
    $this->submenuClasses[] = 'megamenu-tabs__group';

    $tabs_group_layout = $this->getOption('tabs_group_layout');
    if ($tabs_group_layout == 'auto') {
      $tab_layout = $this->getOption('tab_layout');
      switch ($tab_layout) {
        case 'top':
        case 'bottom':
          $tabs_group_layout = 'full';
          break;
        case 'right':
        case 'left':
          $tabs_group_layout = '1-4';
          break;
      }
    }

    $this->submenuClasses[] = 'megamenu-column';
    $this->submenuClasses[] = 'megamenu-column--' . $tabs_group_layout;
  }

  /**
   * {@inheritdoc}
   */
  function generateStartElement() {
    $this->settings['submenu_type_calc'] = 'toggles-group';

    $item_output = '';
    $class_names = '';

    // Tabs class.
    $this->itemClasses[] = 'megamenu-tabs';

    // Setup classes.
    $this->setupClassId();
    $this->setupClassLevel();
    $this->setupClassLayoutColumns();
    $this->setupClassResponsive();
    $this->setupClassItemType();

    // Tabs layout.
    $tab_layout = $this->getOption('tab_layout');
    $tab_layout_class = 'megamenu-tabs--layout-' . $tab_layout;

    $tabs_group_layout = $this->getOption('tabs_group_layout');
    $panels_group_layout = $this->getOption('panels_group_layout');

    if ($tab_layout == 'right' || $tab_layout == 'left') {
      if ($tabs_group_layout == 'auto') {
        $tabs_group_layout = '1-4';
      }
      if ($panels_group_layout == 'auto') {
        $panels_group_layout = '3-4';
      }
    }
    else {
      if ($tabs_group_layout == 'auto') {
        $tabs_group_layout = 'full';
      }
      if ($panels_group_layout == 'auto') {
        $panels_group_layout = 'full';
      }
    }

    $this->itemClasses[] = $tab_layout_class;

    // Show default.
    if ($this->getOption('show_default_panel')) {
      $this->itemClasses[] = 'megamenu-tabs--show-default';
    }

    $class_names = $this->filterItemClasses();

    // Setup id.
    $id = $this->filterItemId();

    // Comment.
    $item_output .= "<!-- begin Tabs: " . $this->menuLink['title'] . $this->getId() . "-->";

    // Item LI.
    $item_output .= '<li' . $id . $class_names . '>';

    // Manual content (for example, admin notices).
    $item_output .= $this->manual_content;

    // Custom content.
    $item_output .= $this->generateCustomContent();

    return $item_output;
  }

  /**
   * {@inheritdoc}
   */
  function generateEndElement() {
    $html = '</li>';
    $html .= "<!-- end Tabs: " . $this->menuLink['title'] . $this->getId() . "-->";
    return $html;
  }

  /**
   * Setup tab.
   *
   * @param $umitem
   */
  function setupTab($umitem) {
    $panels_group_layout = $this->getOption('panels_group_layout');

    if ($panels_group_layout == 'auto') {
      $tab_layout = $this->getOption('tab_layout');

      switch ($tab_layout) {
        case 'top':
        case 'bottom':
          $panels_group_layout = 'full';
          break;
        case 'left':
        case 'right':
          $tabs_group_layout = $this->getOption('tabs_group_layout');
          if ($tabs_group_layout == 'auto') {
            $panels_group_layout = '3-4';
          }
          else {
            $panels_group_layout = megamenu_get_column_complement($tabs_group_layout);
          }
          break;
      }
    }

    $umitem->submenuType = 'tabs-pane';
    $umitem->submenuClasses[] = "megamenu-tabs__pane";
    $umitem->submenuClasses[] = "megamenu-column";
    $umitem->submenuClasses[] = "megamenu-column--" . $panels_group_layout;

    // Grid panel.
    if ($this->getOption('panels_grid')) {
      $umitem->submenuClasses[] = 'megamenu-submenu--grid';
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

    // Columns setting ID for tabs.
    $cols = $this->getOption('tab_block_columns');
    if ($this->depth > 0 && $cols == 'auto') {
      $cols = $this->getParentItem()->getOption('submenu_column_default');
    }

    if ($cols == 'auto') {
      $cols = 'full';
    }

    $this->itemClasses[] = 'megamenu-column megamenu-column--' . $cols;
  }

}
