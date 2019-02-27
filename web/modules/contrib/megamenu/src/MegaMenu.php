<?php

namespace Drupal\megamenu;

use Drupal\system\Entity\Menu;
use Drupal\Component\Utility\Html;

/**
 * Class MegaMenu
 *
 * @package Drupal\megamenu
 */
class MegaMenu {

  private $settings = [];

  /**
   * Constructor.
   */
  public function __construct() {
    $this->initOptions();
  }

  /**
   * Get menu by name.
   *
   * @param $menu_name
   *
   * @return \Drupal\Core\Entity\EntityInterface|null|static
   */
  public function getMenuByName($menu_name) {
    $menu = null;

    if (empty($menu_name)) {
      return null;
    }

    $menu = Menu::load($menu_name);
    if (!$menu) {
      return null;
    }

    return $menu;
  }

  /**
   * Get megamenu options by menu name.
   *
   * @param $menu_name
   *
   * @return array
   */
  public function getMegaMenuOptionsByMenuName($menu_name) {
    $megamenu_options = [];
    $menu = null;

    if (empty($menu_name)) {
      return [];
    }

    $menu = $this->getMenuByName($menu_name);
    if (!$menu) {
      return [];
    }

    // Get megamenu options
    $megamenu_options = $menu->getThirdPartySettings('megamenu');
    if (empty($megamenu_options) || empty($megamenu_options['megamenu_status'])) {
      return [];
    }

    return $megamenu_options;
  }

  //---------------------------------------------------------------------------
  // CUSTOM STYLES
  //---------------------------------------------------------------------------

  /**
   * Generate custom styles.
   *
   * @return string
   */
  function generateCustomStyles() {
    $output = '';
    $styles = [];
    $custom_css = '';
    $menu_styles = '';
    $item_styles = '';

    // Menu Styles
    $menu_styles = $this->generateMenusStyles();
    if ($menu_styles) {
      $menu_styles = "\n/*** MegaMenu Menu Styles ***/\n" . $menu_styles;
      $styles[20] = $menu_styles;
    }

    // Menu Item Styles
    $item_styles = $this->generateMenuItemsStyles();
    if ($item_styles) {
      $item_styles = "\n/*** MegaMenu Menu Item Styles ***/\n" . $item_styles;
      $styles[30] = $item_styles;
    }

    // Custom Styles - General
    $custom_styles = $this->getOption('custom_styles');
    if ($custom_styles) {
      $custom_styles =
        "\n/*** MegaMenu custom CSS styles ***/\n" .
        $custom_styles;
      $styles[50] = $custom_styles;
    }

    // Custom Styles - Mobile
    $custom_styles_mobile = $this->getOption('custom_styles_mobile');
    if ($custom_styles_mobile) {
      $max_width = $this->getOption('responsive_breakpoint');
      if (!$max_width) {
        $max_width = 959;
      }

      if (is_numeric($max_width)) {
        $max_width .= 'px';
      }

      $custom_styles_mobile =
        "\n/*** MegaMenu custom CSS styles - Mobile ***/\n" .
        "@media screen and (max-width:" . $max_width . ") {\n" .
        $custom_styles_mobile .
        "\n}";
      $styles[60] = $custom_styles_mobile;
    }

    // Custom Styles - Desktop
    $custom_styles_desktop = $this->getOption('custom_styles_desktop');
    if ($custom_styles_desktop) {
      $min_width = $this->getOption('responsive_breakpoint');
      if (!$min_width) {
        $min_width = 960;
      }
      else {
        $min_width = $min_width + 1;
      }

      if (is_numeric($min_width)) {
        $min_width .= 'px';
      }

      $custom_styles_desktop =
        "\n/*** MegaMenu custom CSS styles - Desktop ***/\n" .
        "@media screen and (min-width:".$min_width.") {\n" .
        $custom_styles_desktop .
        "\n}";
      $styles[100] = $custom_styles_desktop;
    }

    $custom_css = implode("\n" , $styles);
    if ($custom_css) {
      $output .= '<style id="megamenu-custom-generated-css">';
      $output .= $custom_css;
      $output .= "\n</style>";
    }

    return $output;
  }

  /**
   * Generate all menus styles.
   *
   * @return string
   */
  function generateMenusStyles() {
    $output = '';

    $menus_styles = $this->getOption('menus_styles');
    if (!$menus_styles) {
      return '';
    }

    foreach ($menus_styles as $menu_id => $styles) {
      if (empty($styles) || empty($styles['status']) || !isset($styles['menu_styles'])) {
        continue;
      }

      $spacing = 12;
      $delim = "/* $menu_id */";
      $remainder = $spacing - strlen($delim);
      if ($remainder < 0) {
        $remainder = 0;
      }
      $output .= $delim . str_repeat(' ', $remainder);

      $k = 0;
      foreach ($styles['menu_styles'] as $selector_key => $selector_value) {
        if ($k > 0) {
          $output .= str_repeat(' ', $spacing);
        }
        $k++;

        $selector = $selector_value['selector'];
        $output .= "$selector { ";
        foreach ($selector_value['properties'] as $property_key => $property_value) {
          $output .= "$property_key:$property_value; ";
        }
        $output .= "}\n";
      }
    }

    return $output;
  }

  /**
   * Generate all menu items styles.
   *
   * @return string
   */
  function generateMenuItemsStyles() {
    $output = '';

    $menus_styles = $this->getOption('menus_styles');
    if (!$menus_styles) {
      return '';
    }

    foreach ($menus_styles as $menu_id => $styles) {
      if (empty($styles) || empty($styles['status']) || empty($styles['items_styles'])) {
        continue;
      }

      foreach ($styles['items_styles'] as $item_id => $item_styles) {
        if (empty($item_styles) || empty($item_styles['item_styles'])) {
          continue;
        }

        $spacing = 12;
        $delim = "/* $menu_id : $item_id */";
        $remainder = $spacing - strlen($delim);
        if ($remainder < 0) {
          $remainder = 0;
        }
        $output .= $delim . str_repeat(' ', $remainder);

        $k = 0;
        foreach ($item_styles['item_styles'] as $selector_key => $selector_value) {
          if ($k > 0) {
            $output .= str_repeat(' ', $spacing);
          }
          $k++;

          $selector = $selector_value['selector'];
          $output .= "$selector { ";
          foreach ($selector_value['properties'] as $property_key => $property_value) {
            $output .= "$property_key:$property_value; ";
          }
          $output .= "}\n";
        }
      }
    }

    return $output;
  }

  /**
   * Set menu item styles.
   *
   * The methods performs both, setting and deleting styles. It deletes style if
   * property is empty. Than it checks cascading to up and delete all empty arrays.
   *
   * Saved array is in the following format:
   *
   * menus_styles
   *  test-menu
   *     menu_styles
   *       0
   *        selector
   *        properties
   *          margin-top
   *    items_styles
   *      0
   *      selector
   *      properties
   *        padding-top
   *
   * @param $menu_id
   * @param $selector
   * @param $property_map
   * @param $status
   */
  function setMenusStyles($menu_id, $selector, $property_map, $status = 1) {
    $menu_id = Html::cleanCssIdentifier($menu_id, megamenu_get_clean_css_filter());
    $menus_styles = \Drupal::service('megamenu')->getOption('menus_styles');
    $selector_exist = false;
    $existed_selector_key = null;
    $current_menu_styles = [];

    if (isset($menus_styles[$menu_id])) {
      $current_menu_styles = $menus_styles[$menu_id];
    }

    // Check if selector exists.
    if (isset($current_menu_styles['menu_styles'])) {
      foreach ($current_menu_styles['menu_styles'] as $selector_key => $selector_value) {
        if ($selector_value['selector'] == $selector) {
          $selector_exist = true;
          $existed_selector_key = $selector_key;
          break;
        }
      }
    }

    // Selector exists, update it.
    if ($selector_exist) {
      // Properties (e.g. 'color' => 'red')
      foreach ($property_map as $property_map_key => $property_map_value) {
        if (empty($property_map_value)) {
          unset($current_menu_styles['menu_styles'][$existed_selector_key]['properties'][$property_map_key]);
          if (empty($current_menu_styles['menu_styles'][$existed_selector_key]['properties'])) {
            unset($current_menu_styles['menu_styles'][$existed_selector_key]);
            if (empty($current_menu_styles['menu_styles'])) {
              unset($current_menu_styles['menu_styles']);
            }
          }
        }
        else {
          $current_menu_styles['menu_styles'][$existed_selector_key]['properties'][$property_map_key] = $property_map_value;
        }
      }
    }
    // Selector doesn't exist, create it.
    else {
      // Filter empty values.
      $properties = [];
      foreach ($property_map as $property_map_key => $property_map_value) {
        if (empty($property_map_value)) {
          continue;
        }
        $properties[$property_map_key] = $property_map_value;
      }
      // Create styles only if properties are not empty.
      if (!empty($properties)) {
        $current_menu_styles['menu_styles'][] = [
          'selector' => $selector,
          'properties' => $properties
        ];
      }
    }

    if (empty($current_menu_styles)) {
      unset($menus_styles[$menu_id]);
    }
    else {
      $menus_styles[$menu_id] = $current_menu_styles;
      $menus_styles[$menu_id]['status'] = $status;
    }

    \Drupal::service('megamenu')->setOption('menus_styles', $menus_styles);
  }

  /**
   * Delete menu styles.
   *
   * @param $menu_id
   */
  function deleteMenuStyles($menu_id) {
    $menu_id = Html::cleanCssIdentifier($menu_id, megamenu_get_clean_css_filter());
    $menus_styles = \Drupal::service('megamenu')->getOption('menus_styles');

    if (!isset($menus_styles[$menu_id])) {
      return;
    }

    unset($menus_styles[$menu_id]);

    \Drupal::service('megamenu')->setOption('menus_styles', $menus_styles);
  }

  /**
   * Set menu item styles.
   *
   * The methods performs both, setting and deleting styles. It deletes style if
   * property is empty. Than it checks cascading to up and delete all empty arrays.
   *
   * Saved array is in the following format:
   *
   * menus_styles
   *  test-menu
   *     menu_styles
   *       0
   *        selector
   *        properties
   *          margin-top
   *    items_styles
   *      0
   *        selector
   *        properties
   *          padding-top
   *
   * @param $menu_item_id
   * @param $selector
   * @param $property_map
   */
  function setMenuItemStyles($menu_id, $menu_item_id, $selector, $property_map, $status = 1) {
    $menu_id = Html::cleanCssIdentifier($menu_id, megamenu_get_clean_css_filter());
    $menu_item_id = Html::cleanCssIdentifier($menu_item_id, megamenu_get_clean_css_filter());
    $menus_styles = \Drupal::service('megamenu')->getOption('menus_styles');
    $selector_exist = false;
    $existed_selector_key = null;
    $current_menu_styles = [];

    if (isset($menus_styles[$menu_id])) {
      $current_menu_styles = $menus_styles[$menu_id];
    }

    // Check if selector exists.
    if (isset($current_menu_styles['items_styles']) && isset($current_menu_styles['items_styles'][$menu_item_id])) {
      if (isset($current_menu_styles['items_styles'][$menu_item_id]['item_styles'])) {
        foreach ($current_menu_styles['items_styles'][$menu_item_id]['item_styles'] as $selector_key => $selector_value) {
          if ($selector_value['selector'] == $selector) {
            $selector_exist = true;
            $existed_selector_key = $selector_key;
            break;
          }
        }
      }
    }

    // Selector exists, update it.
    if ($selector_exist) {
      // Properties (e.g. 'color' => 'red'
      foreach ($property_map as $property_map_key => $property_map_value) {
        if (empty($property_map_value)) {
          unset($current_menu_styles['items_styles'][$menu_item_id]['item_styles'][$existed_selector_key]['properties'][$property_map_key]);
          if (empty($current_menu_styles['items_styles'][$menu_item_id]['item_styles'][$existed_selector_key]['properties'])) {
            unset($current_menu_styles['items_styles'][$menu_item_id]['item_styles'][$existed_selector_key]);
            if (empty($current_menu_styles['items_styles'][$menu_item_id]['item_styles'])) {
              unset($current_menu_styles['items_styles'][$menu_item_id]['item_styles']);
              if (empty($current_menu_styles['items_styles'][$menu_item_id])) {
                unset($current_menu_styles['items_styles'][$menu_item_id]);
                if (empty($current_menu_styles['items_styles'])) {
                  unset($current_menu_styles['items_styles']);
                }
              }
            }
          }
        }
        else {
          $current_menu_styles['items_styles'][$menu_item_id]['item_styles'][$existed_selector_key]['properties'][$property_map_key] = $property_map_value;
        }
      }
    }
    // Selector doesn't exist, create it.
    else {
      // Filter empty values.
      $properties = [];
      foreach ($property_map as $property_map_key => $property_map_value) {
        if (empty($property_map_value)) {
          continue;
        }
        $properties[$property_map_key] = $property_map_value;
      }
      // Create styles only if properties are not empty.
      if (!empty($properties)) {
        $current_menu_styles['items_styles'][$menu_item_id]['item_styles'][] = [
          'selector' => $selector,
          'properties' => $property_map
        ];
      }
    }

    if (empty($current_menu_styles)) {
      unset($menus_styles[$menu_id]);
    }
    else {
      $menus_styles[$menu_id] = $current_menu_styles;
      $menus_styles[$menu_id]['status'] = $status;
    }

    \Drupal::service('megamenu')->setOption('menus_styles', $menus_styles);
  }

  /**
   * Delete menu styles.
   *
   * @param $menu_item_id
   */
  function deleteMenuItemStyles($menu_id, $menu_item_id) {
    $menu_id = Html::cleanCssIdentifier($menu_id, megamenu_get_clean_css_filter());
    $menu_item_id = Html::cleanCssIdentifier($menu_item_id, megamenu_get_clean_css_filter());
    $menus_styles = \Drupal::service('megamenu')->getOption('menus_styles');

    if (!isset($menus_styles[$menu_id])) {
      return;
    }

    $current_menu_styles = $menus_styles[$menu_id];

    if (isset($current_menu_styles['items_styles']) && isset($current_menu_styles['items_styles'][$menu_item_id])) {
      unset($current_menu_styles['items_styles'][$menu_item_id]);
      if (empty($current_menu_styles['items_styles'])) {
        unset($current_menu_styles['items_styles']);
      }
    }

    if (empty($current_menu_styles)) {
      unset($menus_styles[$menu_id]);
    }
    else {
      $menus_styles[$menu_id] = $current_menu_styles;
    }

    \Drupal::service('megamenu')->setOption('menus_styles', $menus_styles);
  }

  //---------------------------------------------------------------------------
  // OPTIONS
  //---------------------------------------------------------------------------

  /**
   * Init options.
   */
  public function initOptions() {
    $this->settings = \Drupal::config('megamenu.settings')->getRawData();
  }

  /**
   * Delete option.
   *
   * @return array
   */
  function deleteOption($option) {
    unset($this->settings[$option]);
    $config = \Drupal::configFactory()->getEditable('megamenu.settings');
    $config->clear($option);
    $config->save();
  }

  /**
   * Set options.
   *
   * @return array
   */
  function setOptions($options) {
    $this->settings = $options;
    $config = \Drupal::configFactory()->getEditable('megamenu.settings');
    foreach ($options as $key => $value) {
      $config->set($key, $value);
    }
    $config->save();
  }

  /**
   * Set option.
   *
   * @return array
   */
  function setOption($option, $value) {
    $this->settings[$option] = $value;
    $config = \Drupal::configFactory()->getEditable('megamenu.settings');
    $config->set($option, $value);
    $config->save();
  }

  /**
   * Get option.
   *
   * @param $option
   * @param null $default
   *
   * @return mixed|null|string
   */
  function getOption($option, $default = null) {
    // Value from settings
    if (isset($this->settings[$option])) {
      $menu_option = $this->settings[$option];
    }
    // Default fallback
    else {
      // Use passed default.
      if ($default) {
        $menu_option = $default;
      }
      // No default passed.
      else {
        $menu_option = $this->getDefaultOption($option);
      }
    }

    return $menu_option;
  }

  /**
   * Get options.
   *
   * @return array
   */
  function getOptions() {
    if (!isset($this->settings)) {
      $this->initOptions();
    }

    return $this->settings;
  }

  /**
   * Get default option.
   *
   * @param $option
   *
   * @return mixed|string
   */
  function getDefaultOption($option) {
    $default = '';
    $default_options = $this->getDefaultOptions();

    if (isset($default_options[$option])) {
      $default = $default_options[$option];
    }

    return $default;
  }

  /**
   * Get default options.
   */
  public static function getDefaultOptions() {
    $default_options = [
      // Responsive & Mobile
      'retractor_display_strategy' => 'responsive',
      'responsive_breakpoint' => 959,
      // Script Configuration
      'touch_off_close'	=> 1,
      'reposition_on_load' => 0,
      'intent_delay' => 300,
      'intent_interval' => 100,
      'intent_threshold' => 7,
      'scrollto_offset' => 50,
      'scrollto_duration' => 1000,
      'collapse_after_scroll' => 1,
      'remove_conflicts'	=> 1,
      // Custom Styles
      'custom_styles' => '',
      'custom_styles_mobile' => '',
      'custom_styles_desktop' => '',
      // Misc
      'accessible' => 1,
      'fontawesome_load' => 1,
      'fontawesome_load_admin_route' => 1,
      'fontawesome_use_cdn' => 0,
      'disable_megamenu_css' => 0,
      // Menu instances config
      'menu_instances_config' => [],
      'menus_styles' => [],
    ];

    return $default_options;
  }

}
