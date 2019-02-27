<?php

/**
 * @file
 * The theme hooks.
 */

use Drupal\system\Entity\Menu;

/**
 * Implements hook_theme_suggestions_HOOK_alter().
 */
function megamenu_theme_suggestions_menu_alter(array &$suggestions, array $variables) {
  if (isset($variables['menu_name'])) {
    //Get drupal menu and menu options
    if($menu = Menu::load($variables['menu_name'])) {
      if ($megamenu_options = $menu->getThirdPartySettings('megamenu')) {
        // Don't render menu through megamenu if it's disabled.
        if (empty($megamenu_options['megamenu_status'])) {
          return;
        }

        // Hide on mobile if option is set.
        if ($megamenu_options['disable_mobile'] && megamenu_is_mobile()) {
          return;
        }

        // Set new theme suggestion.
        $suggestions[] = 'menu__megamenu';
        $hook = str_replace('-', '_', $variables['menu_name']);
        $suggestions[] = 'menu__megamenu__' . $hook;
      }
    }
  }
}

/**
 * Implements hook_theme().
 */
function megamenu_theme() {
  return [
    // @see http://drupal.stackexchange.com/questions/200602/override-theme-template-from-module-without-implementing-a-theme
    'menu__megamenu' => [
      'template' => 'menu--megamenu',
      'base hook' => 'menu',
    ],
  ];
}

/**
 * Implements hook_megamenu_skins_info().
 */
function megamenu_megamenu_skins_info() {
  $skins = [];

  // Base
  $skins['base'] = [
    'name' => 'base',
    'title' => t('Base'),
    'library' => 'megamenu/skins.base',
    'classes' => '',
  ];
  // Bartik Blue Bar
  $skins['bartik_blue_bar'] = [
    'name' => 'bartik_blue_bar',
    'title' => t('Bartik Blue Bar'),
    'library' => 'megamenu/skins.bartik_blue_bar',
    'classes' => '',
  ];
  // Dark Bar
  $skins['dark_bar'] = [
    'name' => 'dark_bar',
    'title' => t('Dark Bar'),
    'library' => 'megamenu/skins.dark_bar',
    'classes' => '',
  ];
  // Blue Bar
  $skins['blue_bar'] = [
    'name' => 'blue_bar',
    'title' => t('Blue Bar'),
    'library' => 'megamenu/skins.blue_bar',
    'classes' => '',
  ];
  // Light Bar
  $skins['light_bar'] = [
    'name' => 'light_bar',
    'title' => t('Light Bar'),
    'library' => 'megamenu/skins.light_bar',
    'classes' => '',
  ];

  return $skins;
}
