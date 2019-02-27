<?php

namespace Drupal\megamenu;

use Drupal\system\Entity\Menu;
use Drupal\Component\Utility\Html;
use Drupal\megamenu\MegaMenuMenu;
use Drupal\megamenu\menuitems\MegaMenuItemBase;
use Drupal\megamenu\menuitems\MegaMenuItemNormal;
use Drupal\megamenu\menuitems\MegaMenuItemCustom;
use Drupal\megamenu\menuitems\MegaMenuItemColumn;
use Drupal\megamenu\menuitems\MegaMenuItemDivider;
use Drupal\megamenu\menuitems\MegaMenuItemRow;
use Drupal\megamenu\menuitems\MegaMenuItemTabs;
use Drupal\megamenu\menuitems\MegaMenuItemTab;
use Drupal\megamenu\menuitems\MegaMenuItemDrupalBlock;

/**
 * Class MegaMenuTwigExtension.
 *
 * @see https://www.openaccess.fr/la-veille-du-web/create-twig-extension-drupal-8
 * @see http://symfony.com/doc/current/templating/twig_extension.html
 * @see http://twig.sensiolabs.org/doc/2.x/advanced.html
 *
 * @package Drupal\megamenu
 */
class MegaMenuTwigExtension extends \Twig_Extension {

  /**
   * {@inheritdoc}
   *
   * This function must return the name of the extension. It must be unique.
   */
  public function getName() {
    return 'megamenu';
  }

  /**
   * Declare twig extension functions.
   */
  public function getFunctions() {
    return [
      new \Twig_SimpleFunction('megamenu_render_menu',
        [$this, 'megamenuRenderMenu'],
        ['is_safe' => ['html']]
      ),
    ];
  }

  /**
   * Render menu.
   *
   * @param $menu_name
   * @param $menu_links
   *
   * @return string
   */
  public function megamenuRenderMenu($menu_name, $menu_links) {
    $output = '';
    $menu_level = 0;

    if (empty($menu_name)) {
      return '';
    }
    $megamenu_menu = new MegaMenuMenu($menu_name);

    // Get drupal menu.
    $menu = $megamenu_menu->getMenu();
    if (!$menu) {
      return '';
    }
    // Get megamenu options.
    $megamenu_options = $megamenu_menu->getOptions();
    if (empty($megamenu_options['megamenu_status'])) {
      return '';
    }

    // Show/hide on mobile.
    if ($megamenu_options['disable_mobile'] && megamenu_is_mobile()) {
      return '';
    }

    // Load skin library.
    if ($megamenu_options['skin']) {
      $output .= $megamenu_menu->loadSkinLibrary($megamenu_options['skin']);
    }

    // Before menu content.
    $output .= $megamenu_menu->generateContentBeforeMenu();

    // Responsive toggle
    $output .= $megamenu_menu->getResponsiveToggle();

    // Menu opening (start element).
    $output .= $megamenu_menu->generateStartElement();

    // Menu items.
    $output .= $this->megamenuRenderItems($megamenu_menu, $menu_links, $menu_level);

    // Menu closing (end element).
    $output .= $megamenu_menu->generateEndElement();

    return $output;
  }

  /**
   * Render menu items.
   *
   * @param $megamenu_menu
   * @param $menu_links
   * @param int $menu_level
   * @param $parent_item
   *
   * @return string
   */
  private function megamenuRenderItems($megamenu_menu, $menu_links, $menu_level = 0, $parent_item = null) {
    $output = '';

    foreach ($menu_links as $menu_link) {
      $output .= $this->megamenuRenderItem($megamenu_menu, $menu_link, $menu_level, $parent_item);
    }

    return $output;
  }

  /**
   * Render menu item.
   *
   * @param $megamenu_menu
   * @param $menu_link
   * @param int $menu_level
   * @param null $parent_item
   *
   * @return string
   */
  private function megamenuRenderItem($megamenu_menu, $menu_link, $menu_level = 0, $parent_item = null) {
    $output = '';
    $args = [];

    // Load menu link options.
    $menu_link_options = $menu_link['url']->getOptions();
    $megamenu_link_options = isset($menu_link_options['megamenu']) ? $menu_link_options['megamenu'] : [];

    // Show/hide menu item on mobile.
    if (megamenu_is_mobile()) {
      if (!empty($megamenu_link_options['disable_on_mobile'])) {
        return '';
      }
    }
    // Show/hide menu item on desktop.
    else {
      if (!empty($megamenu_link_options['disable_on_desktop'])) {
        return '';
      }
    }

    // Determine menu item type and init menu item.
    if (!isset($megamenu_link_options['item_type'])) {
      $menu_item = new MegaMenuItemNormal($megamenu_menu, $menu_link, $menu_level, $args, $parent_item);
    }
    elseif ($parent_item && $parent_item->getType() == 'tabs') {
      $menu_item = new MegaMenuItemTab($megamenu_menu, $menu_link, $menu_level, $args, $parent_item);
    }
    elseif ($megamenu_link_options['item_type'] == 'normal') {
      $menu_item = new MegaMenuItemNormal($megamenu_menu, $menu_link, $menu_level, $args, $parent_item);
    }
    elseif ($megamenu_link_options['item_type'] == 'row') {
      $menu_item = new MegaMenuItemRow($megamenu_menu, $menu_link, $menu_level, $args, $parent_item);
    }
    elseif ($megamenu_link_options['item_type'] == 'column') {
      $menu_item = new MegaMenuItemColumn($megamenu_menu, $menu_link, $menu_level, $args, $parent_item);
    }
    elseif ($megamenu_link_options['item_type'] == 'divider') {
      $menu_item = new MegaMenuItemDivider($megamenu_menu, $menu_link, $menu_level, $args, $parent_item);
    }
    elseif ($megamenu_link_options['item_type'] == 'custom_content') {
      $menu_item = new MegaMenuItemCustom($megamenu_menu, $menu_link, $menu_level, $args, $parent_item);
    }
    elseif ($megamenu_link_options['item_type'] == 'tabs_block') {
      $menu_item = new MegaMenuItemTabs($megamenu_menu, $menu_link, $menu_level, $args, $parent_item);
    }
    elseif ($megamenu_link_options['item_type'] == 'tab') {
      $menu_item = new MegaMenuItemTab($megamenu_menu, $menu_link, $menu_level, $args, $parent_item);
    }
    elseif ($megamenu_link_options['item_type'] == 'drupal_block') {
      $menu_item = new MegaMenuItemDrupalBlock($megamenu_menu, $menu_link, $menu_level, $args, $parent_item);
    }
    // Unknown menu type.
    else {
      return '';
    }

    // Generate start element.
    $output .= $menu_item->generateStartElement();

    // Submenu.
    if (!empty($menu_link['below'])) {
      $show_submenu = true;

      // Show/hide submenu on mobile.
      if (!empty($megamenu_link_options['disable_submenu_on_mobile']) && megamenu_is_mobile()) {
        $show_submenu = false;
      }

      if ($show_submenu) {
        $output .= $menu_item->generateSubmenuWrapperStart();
        $output .= $this->megamenuRenderItems($megamenu_menu, $menu_link['below'], $menu_level + 1, $menu_item);
        $output .= $menu_item->generateSubmenuWrapperEnd();
      }
    }

    // Generate end element.
    $output .= $menu_item->generateEndElement();

    return $output;
  }

}
