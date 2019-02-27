<?php

/**
 * @file
 * The menu form hooks.
 */

use Drupal\Core\Form\FormStateInterface;
use Drupal\system\Entity\Menu;
use Drupal\megamenu\MegaMenuMenu;
use Drupal\Component\Utility\Html;

/**
 * Implements hook_BASE_FORM_id_alter().
 *
 * Alter menu edit form.
 */
function megamenu_form_menu_add_form_alter(&$form, &$form_state, $form_id) {
  $menu_entity = $form_state->getFormObject()->getEntity();
  $megamenu_options = $menu_entity->getThirdPartySettings('megamenu');
  $megamenu_menu = new MegaMenuMenu($menu_entity->id());

  $form = _megamenu_menu_form_options($form, $form_state, $megamenu_menu);

  $form['#entity_builders'][] = '_megamenu_form_menu_form_builder';
  $form['actions']['submit']['#submit'][] = '_megamenu_form_menu_form_submit';
}

/**
 * Implements hook_BASE_FORM_id_alter().
 *
 * Alter menu edit form.
 *
 * #parents arrays are used in order to organize values
 * @see https://www.drupal.org/node/279246#comment-2292322
 */
function megamenu_form_menu_edit_form_alter(&$form, &$form_state, $form_id) {
  $menu_entity = $form_state->getFormObject()->getEntity();
  $megamenu_options = $menu_entity->getThirdPartySettings('megamenu');
  $megamenu_menu = new MegaMenuMenu($menu_entity->id());

  // Extend menu link title in overview table if mega menu is enabled for the menu.
  if (!empty($megamenu_options['megamenu_status'])) {
    array_splice($form['links']['links']['#header'], 1, 0, [t('Item Type')]);

    foreach ($form['links']['links'] as $key => $value) {
      if (strpos($key, 'menu_plugin_id') === FALSE) {
        continue;
      }
      if (!isset($value['#item']) || !($value['#item'] instanceof \Drupal\Core\Menu\MenuLinkTreeElement)) {
        continue;
      }

      $item_type = '';
      $menu_link = $value['#item']->link;
      $menu_link_options = $menu_link->getOptions();
      $menu_link_megamenu_options = isset($menu_link_options['megamenu']) ? $menu_link_options['megamenu'] : [];

      // Determine link type
      if (empty($menu_link_megamenu_options['item_type'])) {
        $item_type = t('Normal link');
      }
      elseif ($menu_link_megamenu_options['item_type'] == 'normal') {
        $item_type = t('Normal link');
      }
      elseif ($menu_link_megamenu_options['item_type'] == 'row') {
        $item_type = t('Row');
      }
      elseif ($menu_link_megamenu_options['item_type'] == 'column') {
        $item_type = t('Column');
      }
      elseif ($menu_link_megamenu_options['item_type'] == 'divider') {
        $item_type = t('Horizontal divider');
      }
      elseif ($menu_link_megamenu_options['item_type'] == 'custom_content') {
        $item_type = t('Custom content');
      }
      elseif ($menu_link_megamenu_options['item_type'] == 'tabs_block') {
        $item_type = t('Tabs block');
      }
      elseif ($menu_link_megamenu_options['item_type'] == 'drupal_block') {
        $item_type = t('Drupal block');
      }
      else {
        $item_type = t('Normal link');
      }

      $pos = array_search('enabled', array_keys($form['links']['links'][$key]));
      $form['links']['links'][$key] = array_merge(
        array_slice($form['links']['links'][$key], 0, $pos),
        ['megamenu_type' => ['#markup' => $item_type]],
        array_slice($form['links']['links'][$key], $pos)
      );
    }
  }

  $form = _megamenu_menu_form_options($form, $form_state, $megamenu_menu);

  $form['#entity_builders'][] = '_megamenu_form_menu_form_builder';
  $form['actions']['submit']['#submit'][] = '_megamenu_form_menu_form_submit';
}

/**
 * Entity builder for the menu configuration entity.
 *
 * @see: https://www.webomelette.com/drupal-8-custom-data-configuration-entities-using-thirdpartysettingsinterface
 */
function _megamenu_form_menu_form_builder($entity_type, Menu $menu, array &$form, FormStateInterface $form_state) {
  // Set megamenu options.
  if ($form_state->getValue('megamenu')) {
    $megamenu_options = _megamenu_menu_form_handle_options($form_state->getValue('megamenu'));
    ksort($megamenu_options);

    // Update third party settings in menu itself.
    foreach ($megamenu_options as $key => $value) {
      $menu->setThirdPartySetting('megamenu', $key, $value);
    }

    // MegaMenu is disabled, delete javascript options.
    if (empty($megamenu_options['megamenu_status'])) {
      megamenu_set_js_instance_options($menu->id(), 'delete');
    }
    // MegaMenu is enabled, update javascript options.
    else {
      megamenu_set_js_instance_options($menu->id(), 'update', $megamenu_options);
    }

    // Set styles.
    _megamenu_set_menu_styles($menu->id(), $megamenu_options);
  }
  // No megamenu options, delete them from javascript, too.
  else {
    megamenu_set_js_instance_options($menu->id(), 'delete');
    \Drupal::service('megamenu')->deleteMenuStyles($menu->id());
  }
}

/**
 * Callback for menu_edit form submit.
 */
function _megamenu_form_menu_form_submit(&$form, &$form_state) {
}

/**
 * Get options for menu form.
 */
function _megamenu_menu_form_options(array $form, FormStateInterface $form_state, $megamenu_menu) {
  // Container
  $form['megamenu'] = [
    '#type' => 'container',
    '#weight' => 98,
  ];

  // MegaMenu status.
  $form['megamenu']['megamenu_status'] = [
    '#type' => 'select',
    '#title' => t('Mega Menu status'),
    '#options' => [
      0 => t('Disabled'),
      1 => t('Enabled'),
    ],
    '#default_value' => $megamenu_menu->getOption('megamenu_status'),
    '#parents' => ['megamenu', 'megamenu_status'],
  ];

  // Tabs
  $form['megamenu_tabs'] = [
    '#type' => 'details',
    '#title' => t('Mega Menu setttings'),
    '#open' => TRUE,
    '#states' => [
      'visible' => [
        ':input[name="megamenu[megamenu_status]"]' => ['value' => 1],
      ],
    ],
    '#weight' => 99,
  ];

  // General
  $form = _megamenu_menu_form_options__general($form, $form_state, $megamenu_menu);
  // Position & Layout
  $form = _megamenu_menu_form_options__position_layout($form, $form_state, $megamenu_menu);
  // Responsive & Mobile
  $form = _megamenu_menu_form_options__responsive_mobile($form, $form_state, $megamenu_menu);
  // Submenus
  $form = _megamenu_menu_form_options__submenus($form, $form_state, $megamenu_menu);
  // Descriptions
  $form = _megamenu_menu_form_options__descriptions($form, $form_state, $megamenu_menu);
  // Images
  $form = _megamenu_menu_form_options__images($form, $form_state, $megamenu_menu);
  // Icons
  $form = _megamenu_menu_form_options__icons($form, $form_state, $megamenu_menu);

  return $form;
}

/**
 * General Configuration
 */
function _megamenu_menu_form_options__general(array $form, FormStateInterface $form_state, $megamenu_menu) {
  $form['general_options'] = [
    '#type' => 'details',
    '#title' => t('General'),
    '#group' => 'megamenu_tabs',
    '#open' => TRUE,
  ];
  // General Configuration
  $skin_options = ['' => t('None (transparent)')];
  $skins = \Drupal::moduleHandler()->invokeAll('megamenu_skins_info', []);
  if ($skins) {
    \Drupal::moduleHandler()->alter('megamenu_skins_info', $skins);
  }
  foreach ($skins as $key => $value) {
    $skin_options[$key] = $value['title'];
  }
  $form['general_options']['skin'] = [
    '#type' => 'select',
    '#title' => t('Menu skin'),
    '#description' => t('Choose menu skin.'),
    '#options' => $skin_options,
    '#default_value' => $megamenu_menu->getOption('skin'),
    '#parents' => ['megamenu', 'skin'],
  ];
  $form['general_options']['orientation'] = [
    '#type' => 'select',
    '#title' => t('Menu orientation'),
    '#description' => t('Choose orientation for the menu.'),
    '#options' => [
      'horizontal' => t('Horizontal'),
      'vertical' => t('Vertical'),
    ],
    '#default_value' => $megamenu_menu->getOption('orientation'),
    '#parents' => ['megamenu', 'orientation'],
  ];
  $form['general_options']['vertical_submenu_width'] = [
    '#type' => 'textfield',
    '#title' => t('Vertical menu mega submenu width (experimental)'),
    '#description' => t('Use this option to set vertical submenu width. The value can be overridden for submenus in menu item configuration. Default value is 600px.'),
    '#default_value' => $megamenu_menu->getOption('vertical_submenu_width'),
    '#parents' => ['megamenu', 'vertical_submenu_width'],
  ];
  // Trigger
  $form['general_options']['trigger'] = [
    '#type' => 'select',
    '#title' => t('Submenu trigger'),
    '#description' => t('Set the submenu trigger. The submenu will be opened by this trigger.'),
    '#options' => [
      'hover' => t('Hover'),
      'hover_intent' => t('Hover Intent'),
      'click' => t('Click'),
    ],
    '#default_value' => $megamenu_menu->getOption('trigger'),
    '#parents' => ['megamenu', 'trigger'],
  ];
  // Dropdown Transitions
  $form['general_options']['transition'] = [
    '#type' => 'select',
    '#title' => t('Submenu transition'),
    '#description' => t('Choose submenu transition. Transitions are supported in Chrome, Firefox, IE10+, Safari.'),
    '#options' => [
      'none' => t('None'),
      'slide' => t('Slide Reveal'),
      'fade' => t('Fade'),
      'shift' => t('Shift Up'),
    ],
    '#default_value' => $megamenu_menu->getOption('transition'),
    '#parents' => ['megamenu', 'transition'],
  ];
  $form['general_options']['transition_duration'] = [
    '#type' => 'textfield',
    '#title' => t('Submenu transition duration (experimental)'),
    '#description' => t('Set submenu transition duration (ms or s). Default value is .3s (.5s is equivalent to 500ms).'),
    '#default_value' => $megamenu_menu->getOption('transition_duration'),
    '#parents' => ['megamenu', 'transition_duration'],
  ];
  // Custom classes
  $form['general_options']['custom_classes'] = [
    '#type' => 'textfield',
    '#title' => t('CSS class(es)'),
    '#description' => t('Space separated CSS classes for this menu (e.g. my-class-one my-class-two).'),
    '#default_value' => $megamenu_menu->getOption('custom_classes'),
    '#parents' => ['megamenu', 'custom_classes'],
  ];
  $form['general_options']['disable_border_classes'] = [
    '#type' => 'checkbox',
    '#title' => t('Disable built-in border classes'),
    '#description' => t('Disable the built-in border classes for this menu (e.g. megamenu--has-border).'),
    '#default_value' => $megamenu_menu->getOption('disable_border_classes'),
    '#parents' => ['megamenu', 'disable_border_classes'],
  ];
  return $form;
}

/**
 * Position & Layout
 */
function _megamenu_menu_form_options__position_layout(array $form, FormStateInterface $form_state, $megamenu_menu) {
  $form['position_layout_options'] = [
    '#type' => 'details',
    '#title' => t('Position & Layout'),
    '#group' => 'megamenu_tabs',
  ];
  // Menu Bar
  $form['position_layout_options']['bar_align'] = [
    '#type' => 'select',
    '#title' => t('Menu bar alignment'),
    '#description' => t('Use this option to set menu bar alignment. The alignment is relative to the theme container. The theme\'s container will limit the maximum width of the menu bar. If the theme\'s container element is 600px, the "Full Width" setting will make the menu 600px wide. If you choose "Center", you must set a menu bar width option below.'),
    '#options' => [
      'full' => t('Full Width (of theme container element)'),
      'left' => t('Left'),
      'right' => t('Right'),
      'center' => t('Center (requires Menu Bar Width)'),
    ],
    '#default_value' => $megamenu_menu->getOption('bar_align'),
    '#parents' => ['megamenu', 'bar_align'],
  ];
  $form['position_layout_options']['bar_width'] = [
    '#type' => 'textfield',
    '#title' => t('Menu bar width (experimental)'),
    '#description' => t('Set an explicit width for the menu bar. Generally not needed except for centering.'),
    '#default_value' => $megamenu_menu->getOption('bar_width'),
    '#parents' => ['megamenu', 'bar_width'],
  ];
  $form['position_layout_options']['bar_margin_top'] = [
    '#type' => 'textfield',
    '#title' => t('Menu bar margin top (experimental)'),
    '#description' => t('Set menu bar margin top. This option is useful for tweaking position.'),
    '#default_value' => $megamenu_menu->getOption('bar_margin_top'),
    '#parents' => ['megamenu', 'bar_margin_top'],
  ];
  $form['position_layout_options']['bar_margin_bottom'] = [
    '#type' => 'textfield',
    '#title' => t('Menu bar margin bottom (experimental)'),
    '#description' => t('Set menu bar margin bottom. This option is useful for spacing out elements.'),
    '#default_value' => $megamenu_menu->getOption('bar_margin_bottom'),
    '#parents' => ['megamenu', 'bar_margin_bottom'],
  ];
  // Menu Items
  $form['position_layout_options']['items_align'] = [
    '#type' => 'select',
    '#title' => t('Horizontal item alignment'),
    '#description' => t('Choose horizontal item alignment.'),
    '#options' => [
      'left' => t('Left'),
      'center' => t('Center'),
      'right' => t('Right'),
    ],
    '#default_value' => $megamenu_menu->getOption('items_align'),
    '#parents' => ['megamenu', 'items_align'],
  ];
  // Inner Menu Bar
  $form['position_layout_options']['bar_inner_center'] = [
    '#type' => 'checkbox',
    '#title' => t('Center inner menu bar'),
    '#description' => t('Use this option to center inner menu bar. It requires an "Inner menu bar width" option to be set below.'),
    '#default_value' => $megamenu_menu->getOption('bar_inner_center'),
    '#parents' => ['megamenu', 'bar_inner_center'],
  ];
  $form['position_layout_options']['bar_inner_width'] = [
    '#type' => 'textfield',
    '#title' => t('Inner menu bar width (experimental)'),
    '#description' => t('Set an explicit width for the inner menu bar. In majority of cases it is not needed, except for inner menu bar centering. You may also want to set the "Bound Submenu" option to Inner.'),
    '#default_value' => $megamenu_menu->getOption('bar_inner_width'),
    '#parents' => ['megamenu', 'bar_inner_width'],
  ];
  return $form;
}

/**
 * Responsive & Mobile
 */
function _megamenu_menu_form_options__responsive_mobile(array $form, FormStateInterface $form_state, $megamenu_menu) {
  $form['responsive_mobile_options'] = [
    '#type' => 'details',
    '#title' => t('Responsive & Mobile'),
    '#group' => 'megamenu_tabs',
  ];
  $form['responsive_mobile_options']['disable_mobile'] = [
    '#type' => 'checkbox',
    '#title' => t('Disable MegaMenu on mobile'),
    '#description' => t('Use this option to disable Mega Menu when a mobile device is detected via built-in megamenu_is_mobile() function. This will include tablets default.'),
    '#default_value' => $megamenu_menu->getOption('disable_mobile'),
    '#parents' => ['megamenu', 'disable_mobile'],
  ];
  $form['responsive_mobile_options']['responsive'] = [
    '#type' => 'checkbox',
    '#title' => t('Responsive menu'),
    '#description' => t('Uncheck this if you want to disable a responsive menu.'),
    '#default_value' => $megamenu_menu->getOption('responsive'),
    '#parents' => ['megamenu', 'responsive'],
  ];
  $form['responsive_mobile_options']['responsive_columns'] = [
    '#type' => 'select',
    '#title' => t('Responsive columns (tablet)'),
    '#description' => t('Choose number of responsive columns. This option only affects top level menu items. By default, the top level menu items will appear in two (2) columns from 480px to your defined breakpoint which is 960 by default (approximately tablet-size). If you want them to be displayed in a single column instead, change that here.'),
    '#options' => [
      2 => t('Two (2) Columns'),
      1 => t('One (1) Column'),
    ],
    '#default_value' => $megamenu_menu->getOption('responsive_columns'),
    '#parents' => ['megamenu', 'responsive_columns'],
  ];
  $form['responsive_mobile_options']['responsive_toggle'] = [
    '#type' => 'checkbox',
    '#title' => t('Responsive toggle'),
    '#description' => t('Use this option to display a responsive toggle for this menu.'),
    '#default_value' => $megamenu_menu->getOption('responsive_toggle'),
    '#parents' => ['megamenu', 'responsive_toggle'],
  ];
  $form['responsive_mobile_options']['responsive_toggle_tag'] = [
    '#type' => 'select',
    '#title' => t('Responsive toggle tag'),
    '#description' => t('Set toggle tag. Anchor is default value.'),
    '#options' => [
      'a' => t('&lt;a&gt;'),
      'div' => t('&lt;div&gt;'),
      'span' => t('&lt;span&gt;'),
      'button' => t('&lt;button&gt;'),
    ],
    '#default_value' => $megamenu_menu->getOption('responsive_toggle_tag'),
    '#parents' => ['megamenu', 'responsive_toggle_tag'],
  ];
  $form['responsive_mobile_options']['responsive_toggle_alignment'] = [
    '#type' => 'select',
    '#title' => t('Responsive toggle alignment'),
    '#description' => t('Use this option to set the toggle button alignment.'),
    '#options' => [
      'full' => t('Full Width'),
      'left' => t('Left'),
      'right' => t('Right'),
    ],
    '#default_value' => $megamenu_menu->getOption('responsive_toggle_alignment'),
    '#parents' => ['megamenu', 'responsive_toggle_alignment'],
  ];
  $form['responsive_mobile_options']['responsive_toggle_content'] = [
    '#type' => 'textfield',
    '#title' => t('Responsive toggle content'),
    '#description' => t('Enter text to be shown on the responsive toggle.'),
    '#default_value' => $megamenu_menu->getOption('responsive_toggle_content'),
    '#parents' => ['megamenu', 'responsive_toggle_content'],
  ];
  $form['responsive_mobile_options']['responsive_toggle_content_alignment'] = [
    '#type' => 'select',
    '#title' => t('Responsive toggle content alignment'),
    '#description' => t('Choose alignment of the content within the toggle.'),
    '#options' => [
      'left' => t('Left'),
      'center' => t('Center'),
      'right' => t('Right'),
    ],
    '#default_value' => $megamenu_menu->getOption('responsive_toggle_content_alignment'),
    '#parents' => ['megamenu', 'responsive_toggle_content_alignment'],
  ];
  $form['responsive_mobile_options']['responsive_collapse'] = [
    '#type' => 'checkbox',
    '#title' => t('Collapse by default'),
    '#description' => t('Uncheck this if you want to see all top level menu items on mobile without a responsive toggle.'),
    '#default_value' => $megamenu_menu->getOption('responsive_collapse'),
    '#parents' => ['megamenu', 'responsive_collapse'],
  ];
  $form['responsive_mobile_options']['display_retractor_top'] = [
    '#type' => 'checkbox',
    '#title' => t('Display submenu retractor top'),
    '#description' => t('Use this option to display a close button at the top of the submenu on mobile devices.'),
    '#default_value' => $megamenu_menu->getOption('display_retractor_top'),
    '#parents' => ['megamenu', 'display_retractor_top'],
  ];
  $form['responsive_mobile_options']['display_retractor_bottom'] = [
    '#type' => 'checkbox',
    '#title' => t('Display submenu retractor bottom'),
    '#description' => t('Use this option to display a close button at the bottom of the submenu on mobile devices.'),
    '#default_value' => $megamenu_menu->getOption('display_retractor_bottom'),
    '#parents' => ['megamenu', 'display_retractor_bottom'],
  ];
  $form['responsive_mobile_options']['retractor_label'] = [
    '#type' => 'textfield',
    '#title' => t('Submenu retractor text'),
    '#description' => t('Set text for submenu retractor. "Close" will be displayed by default.'),
    '#default_value' => $megamenu_menu->getOption('retractor_label'),
    '#parents' => ['megamenu', 'retractor_label'],
  ];
  return $form;
}

/**
 * Submenus
 */
function _megamenu_menu_form_options__submenus(array $form, FormStateInterface $form_state, $megamenu_menu) {
  $form['submenus_options'] = [
    '#type' => 'details',
    '#title' => t('Submenus'),
    '#group' => 'megamenu_tabs',
  ];
  $form['submenus_options']['container_tag'] = [
    '#type' => 'select',
    '#title' => t('Container tag'),
    '#description' => t('Choose the wrapper tag for the entire menu. Use "div" for non-HTML5 sites.'),
    '#options' => [
      'nav'	=> t('&lt;nav&gt;'),
      'div'	=> t('&lt;div&gt;'),
    ],
    '#default_value' => $megamenu_menu->getOption('container_tag'),
    '#parents' => ['megamenu', 'container_tag'],
  ];
  $form['submenus_options']['content_before_nav'] = [
    '#type' => 'text_format',
    '#title' => t('Content before menu'),
    '#description' => t('Enter the content to be inserted before the menu.'),
    '#rows' => 3,
    '#format' => $megamenu_menu->getOption('content_before_nav_format'),
    '#default_value' => $megamenu_menu->getOption('content_before_nav'),
    '#parents' => ['megamenu', 'content_before_nav'],
  ];
  $form['submenus_options']['bound_submenus'] = [
    '#type' => 'select',
    '#title' => t('Bound submenu to'),
    '#description' => t('Choose how to bound submenu. If you want a submenu wider than the menu bar choose "Unbounded" value. The submenu will be bound by the next relatively positioned ancestor element in a theme. This is only relevant for horizontal menus.'),
    '#options' => [
      'on' => t('Menu Bar'),
      'inner' => t('Inner menu bar width'),
      'off' => t('Unbounded'),
    ],
    '#default_value' => $megamenu_menu->getOption('bound_submenus'),
    '#parents' => ['megamenu', 'bound_submenus'],
  ];
  $form['submenus_options']['submenu_inner_width'] = [
    '#type' => 'textfield',
    '#title' => t('Submenu row width (experimental)'),
    '#description' => t('Set row width for submenu. You can center the contents at this width if there are rows within submenu.'),
    '#default_value' => $megamenu_menu->getOption('submenu_inner_width'),
    '#parents' => ['megamenu', 'submenu_inner_width'],
  ];
  $form['submenus_options']['submenu_max_height'] = [
    '#type' => 'textfield',
    '#title' => t('Mega submenu max height (experimental)'),
    '#description' => t('Enter here the maximum height of the submenu. Submenus that are taller than this value will get a vertical scrollbar if option "Submenu scrolling" is not disabled above. Default value is 600px.'),
    '#default_value' => $megamenu_menu->getOption('submenu_max_height'),
    '#parents' => ['megamenu', 'submenu_max_height'],
  ];
  $form['submenus_options']['submenu_scrolling'] = [
    '#type' => 'checkbox',
    '#title' => t('Allow submenu scrolling'),
    '#description' => t('Use this option to allow scrolling in the submenus. It is recommended to leave this disabled because of current Chrome browser bug that causes rendering issues in overflow scrolled elements. Disabling submenu scrolling will have no effect if the Slide transition is used, so it is recommended to use a different transition.'),
    '#default_value' => $megamenu_menu->getOption('submenu_scrolling'),
    '#parents' => ['megamenu', 'submenu_scrolling'],
  ];
  $form['submenus_options']['display_submenu_indicators'] = [
    '#type' => 'checkbox',
    '#title' => t('Display submenu indicators'),
    '#description' => t('Use this option to display an arrow indicator when a drop submenu exists.'),
    '#default_value' => $megamenu_menu->getOption('display_submenu_indicators'),
    '#parents' => ['megamenu', 'display_submenu_indicators'],
  ];
  $form['submenus_options']['display_submenu_close_button'] = [
    '#type' => 'checkbox',
    '#title' => t('Display submenu close button'),
    '#description' => t('Use this option to display an "X" button for closing the submenu (at all sizes). The option is useful for click trigger.'),
    '#default_value' => $megamenu_menu->getOption('display_submenu_close_button'),
    '#parents' => ['megamenu', 'display_submenu_close_button'],
  ];
  return $form;
}

/**
 * Descriptions
 */
function _megamenu_menu_form_options__descriptions(array $form, FormStateInterface $form_state, $megamenu_menu) {
  $form['descriptions_options'] = [
    '#type' => 'details',
    '#title' => t('Descriptions'),
    '#group' => 'megamenu_tabs',
  ];
  $form['descriptions_options']['descriptions_top_level'] = [
    '#type' => 'checkbox',
    '#title' => t('Top level descriptions'),
    '#description' => t('Display descriptions on top level menu items.'),
    '#default_value' => $megamenu_menu->getOption('descriptions_top_level'),
    '#parents' => ['megamenu', 'descriptions_top_level'],
  ];
  $form['descriptions_options']['descriptions_headers'] = [
    '#type' => 'checkbox',
    '#title' => t('Header item descriptions'),
    '#description' => t('Display descriptions on header menu items.'),
    '#default_value' => $megamenu_menu->getOption('descriptions_headers'),
    '#parents' => ['megamenu', 'descriptions_headers'],
  ];
  $form['descriptions_options']['descriptions_normal'] = [
    '#type' => 'checkbox',
    '#title' => t('Normal item descriptions'),
    '#description' => t('Display descriptions on normal menu items.'),
    '#default_value' => $megamenu_menu->getOption('descriptions_normal'),
    '#parents' => ['megamenu', 'descriptions_normal'],
  ];
  $form['descriptions_options']['descriptions_tab'] = [
    '#type' => 'checkbox',
    '#title' => t('Tab item descriptions'),
    '#description' => t('Display descriptions on tab toggle menu items.'),
    '#default_value' => $megamenu_menu->getOption('descriptions_tab'),
    '#parents' => ['megamenu', 'descriptions_tab'],
  ];
  $form['descriptions_options']['anchor_divider'] = [
    '#type' => 'textfield',
    '#title' => t('Anchor divider'),
    '#description' => t('Enter anchor divider. The string that will separate the title from the description. This value will not be visible, but it is useful for screen readers.'),
    '#default_value' => $megamenu_menu->getOption('anchor_divider'),
    '#parents' => ['megamenu', 'anchor_divider'],
  ];
  return $form;
}

/**
 * Images
 */
function _megamenu_menu_form_options__images(array $form, FormStateInterface $form_state, $megamenu_menu) {
  $form['images_options'] = [
    '#type' => 'details',
    '#title' => t('Images'),
    '#group' => 'megamenu_tabs',
  ];
  // Images
  $image_size_options = [
    'full' => t('Full (display image at natural dimensions)'),
  ];
  $image_styles = megamenu_get_image_styles_list();
  foreach ($image_styles as $key => $value) {
    $image_size_options[$key] = $value;
  }
  $form['images_options']['image_size'] = [
    '#type' => 'radios',
    '#title' => t('Image size'),
    '#description' => t('Represents the size of the actual file that will be served. Note that image sizes can be overridden on individual menu items.'),
    '#options' => $image_size_options,
    '#default_value' => $megamenu_menu->getOption('image_size'),
    '#parents' => ['megamenu', 'image_size'],
  ];
  $form['images_options']['image_width'] = [
    '#type' => 'textfield',
    '#title' => t('Image width attribute value'),
    '#description' => t('Represents the width attribute value for menu item images in pixels (do not include units). Leave the option blank to use actual dimensions.'),
    '#default_value' => $megamenu_menu->getOption('image_width'),
    '#parents' => ['megamenu', 'image_width'],
  ];
  $form['images_options']['image_height'] = [
    '#type' => 'textfield',
    '#title' => t('Image height attribute value'),
    '#description' => t('Represents the height attribute value for menu item images in pixels (do not include units). Leave the option blank to use actual dimensions.'),
    '#default_value' => $megamenu_menu->getOption('image_height'),
    '#parents' => ['megamenu', 'image_height'],
  ];
  $form['images_options']['image_set_dimensions'] = [
    '#type' => 'checkbox',
    '#title' => t('Set image dimensions'),
    '#description' => t('Use this option to set image dimensions. The actual width and height attributes on an image will be used if none are set manually.'),
    '#default_value' => $megamenu_menu->getOption('image_set_dimensions'),
    '#parents' => ['megamenu', 'image_set_dimensions'],
  ];
  $form['images_options']['image_title_attribute'] = [
    '#type' => 'checkbox',
    '#title' => t('Use image title attribute'),
    '#description' => t('Use this option to set image title attribute.'),
    '#default_value' => $megamenu_menu->getOption('image_title_attribute'),
    '#parents' => ['megamenu', 'image_title_attribute'],
  ];
  $form['images_options']['disable_images_mobile'] = [
    '#type' => 'checkbox',
    '#title' => t('Disable images on mobile'),
    '#description' => t('Use this option to disable images on mobile. Mobile devices are detected via built-in megamenu_is_mobile() function.'),
    '#default_value' => $megamenu_menu->getOption('disable_images_mobile'),
    '#parents' => ['megamenu', 'disable_images_mobile'],
  ];
  $form['images_options']['lazy_load_images'] = [
    '#type' => 'checkbox',
    '#title' => t('Lazy-load images'),
    '#description' => t('Use this option to load the images only when the submenu is opened. There could be a slight delay for users depending on the server speed, but more efficient bandwidth usage.'),
    '#default_value' => $megamenu_menu->getOption('lazy_load_images'),
    '#parents' => ['megamenu', 'lazy_load_images'],
  ];
  $form['images_options']['image_text_top_padding'] = [
    '#type' => 'textfield',
    '#title' => t('Image text top padding (experimental)'),
    '#description' => t('Represents the top padding for the accompanying text when Image Left or Image Right layouts are displayed. This allows control over the vertical alignment of the text relative to the image. Can be overriden on individual menu items.'),
    '#default_value' => $megamenu_menu->getOption('image_text_top_padding'),
    '#parents' => ['megamenu', 'image_text_top_padding'],
  ];
  // Submenu Background Images
  $form['images_options']['submenu_background_image_reponsive_hide'] = [
    '#type' => 'checkbox',
    '#title' => t('Hide background images on mobile'),
    '#description' => t('Use this option to hide background images on mobile.'),
    '#default_value' => $megamenu_menu->getOption('submenu_background_image_reponsive_hide'),
    '#parents' => ['megamenu', 'submenu_background_image_reponsive_hide'],
  ];
  return $form;
}

/**
 * Icons
 */
function _megamenu_menu_form_options__icons(array $form, FormStateInterface $form_state, $megamenu_menu) {
  $form['icons_options'] = [
    '#type' => 'details',
    '#title' => t('Icons'),
    '#group' => 'megamenu_tabs',
  ];
  $form['icons_options']['icon_tag'] = [
    '#type' => 'select',
    '#title' => t('Icon tag'),
    '#description' => t('Choose the HTML tag to be used for the icons.'),
    '#options' => [
      'i' => t('&lt;i&gt;'),
      'span' => t('&lt;span&gt;'),
    ],
    '#default_value' => $megamenu_menu->getOption('icon_tag'),
    '#parents' => ['megamenu', 'icon_tag'],
  ];
  $form['icons_options']['icon_display'] = [
    '#type' => 'select',
    '#title' => t('Display icons'),
    '#description' => t('Select the way the icons are displayed. Choose "Inline" if text is too long to fit on one line along with the icon.'),
    '#options' => [
      'block' => t('Block'),
      'inline' => t('Inline'),
    ],
    '#default_value' => $megamenu_menu->getOption('icon_display'),
    '#parents' => ['megamenu', 'icon_display'],
  ];
  $form['icons_options']['icon_width'] = [
    '#type' => 'textfield',
    '#title' => t('Icon width (experimental)'),
    '#description' => t('Use this option to set icon width. The icon will be centered within this width. Default value is 1.3em.'),
    '#default_value' => $megamenu_menu->getOption('icon_width'),
    '#parents' => ['megamenu', 'icon_width'],
  ];
  return $form;
}

/**
 * Handle single value for options with the same names in different sections.
 */
function _megamenu_menu_form_handle_options(array $megamenu_options) {
  // Handle text_format fields.
  if (isset($megamenu_options['content_before_nav']['format'])) {
    $megamenu_options['content_before_nav_format'] = $megamenu_options['content_before_nav']['format'];
  }
  if (isset($megamenu_options['content_before_nav']['value'])) {
    $megamenu_options['content_before_nav'] = $megamenu_options['content_before_nav']['value'];
  }

  return $megamenu_options;
}

//---------------------------------------------------------------------------
// CUSTOM STYLES
//---------------------------------------------------------------------------

/**
 * Set menu styles.
 *
 * @param $menu_id
 * @param $megamenu_options
 */
function _megamenu_set_menu_styles($menu_id, $megamenu_options) {
  // Vertical submenu width.
  _megamenu_set_menu_styles__vertical_submenu_width($menu_id, $megamenu_options);
  // Transition duration.
  _megamenu_set_menu_styles__transition_duration($menu_id, $megamenu_options);
  // Bar width.
  _megamenu_set_menu_styles__bar_width($menu_id, $megamenu_options);
  // Bar margin top.
  _megamenu_set_menu_styles__bar_margin_top($menu_id, $megamenu_options);
  // Bar margin bottom.
  _megamenu_set_menu_styles__bar_margin_bottom($menu_id, $megamenu_options);
  // Bar inner width.
  _megamenu_set_menu_styles__bar_inner_width($menu_id, $megamenu_options);
  // Submenu inner width.
  _megamenu_set_menu_styles__submenu_inner_width($menu_id, $megamenu_options);
  // Submenu max height.
  _megamenu_set_menu_styles__submenu_max_height($menu_id, $megamenu_options);
  // Image text top padding.
  _megamenu_set_menu_styles__image_text_top_padding($menu_id, $megamenu_options);
  // Icon width.
  _megamenu_set_menu_styles__icon_width($menu_id, $megamenu_options);
}

/**
 * Set vertical submenu width.
 *
 * @param $menu_id
 * @param $megamenu_options
 */
function _megamenu_set_menu_styles__vertical_submenu_width($menu_id, $megamenu_options) {
  $menu_id = Html::cleanCssIdentifier($menu_id, megamenu_get_clean_css_filter());
  $selector = ".megamenu--$menu_id.megamenu--vertical .megamenu-submenu--type-mega";
  $property_map = [
    'width' => ''
  ];

  // Option is not empty, create styles.
  if (!empty($megamenu_options['vertical_submenu_width'])) {
    $vertical_submenu_width = $megamenu_options['vertical_submenu_width'];

    // If no units provided, assume pixels.
    if (is_numeric($vertical_submenu_width)) {
      $vertical_submenu_width .= 'px';
    }

    $property_map = [
      'width' => $vertical_submenu_width
    ];
  }

  \Drupal::service('megamenu')->setMenusStyles($menu_id, $selector, $property_map, $megamenu_options['megamenu_status']);
}

/**
 * Transition duration.
 *
 * @param $menu_id
 * @param $megamenu_options
 */
function _megamenu_set_menu_styles__transition_duration($menu_id, $megamenu_options) {
  $menu_id = Html::cleanCssIdentifier($menu_id, megamenu_get_clean_css_filter());
  $selector = '.megamenu--' . $menu_id . ' .megamenu-item .megamenu-submenu--drop';
  $property_map = [
    '-webkit-transition-duration' => '',
    '-ms-transition-duration' => '',
    'transition-duration' => ''
  ];

  // Option is not empty, create styles.
  if (!empty($megamenu_options['transition_duration'])) {
    $transition_duration = $megamenu_options['transition_duration'];

    // If no units provided, assume seconds.
    if (is_numeric($transition_duration)) {
      $transition_duration .= 's';
    }

    $property_map = [
      '-webkit-transition-duration' => $transition_duration,
      '-ms-transition-duration' => $transition_duration,
      'transition-duration' => $transition_duration
    ];
  }

  \Drupal::service('megamenu')->setMenusStyles($menu_id, $selector, $property_map, $megamenu_options['megamenu_status']);
}

/**
 * Set bar width.
 *
 * @param $menu_id
 * @param $megamenu_options
 */
function _megamenu_set_menu_styles__bar_width($menu_id, $megamenu_options) {
  $menu_id = Html::cleanCssIdentifier($menu_id, megamenu_get_clean_css_filter());
  $selector = '.megamenu--' . $menu_id;
  $property_map = [
    'max-width' => ''
  ];

  // Option is not empty, create styles.
  if (!empty($megamenu_options['bar_width'])) {
    $bar_width = $megamenu_options['bar_width'];

    // If no units provided, assume pixels.
    if (is_numeric($bar_width)) {
      $bar_width .= 'px';
    }

    $property_map = [
      'max-width' => $bar_width
    ];
  }

  \Drupal::service('megamenu')->setMenusStyles($menu_id, $selector, $property_map, $megamenu_options['megamenu_status']);
}

/**
 * Set bar margin top.
 *
 * @param $menu_id
 * @param $megamenu_options
 */
function _megamenu_set_menu_styles__bar_margin_top($menu_id, $megamenu_options) {
  $menu_id = Html::cleanCssIdentifier($menu_id, megamenu_get_clean_css_filter());
  $selector = '.megamenu--' . $menu_id;
  $property_map = [
    'margin-top' => ''
  ];

  // Option is not empty, create styles.
  if (!empty($megamenu_options['bar_margin_top'])) {
    $bar_margin_top = $megamenu_options['bar_margin_top'];

    // If no units provided, assume pixels.
    if (is_numeric($bar_margin_top)) {
      $bar_margin_top .= 'px';
    }

    $property_map = [
      'margin-top' => $bar_margin_top
    ];
  }

  \Drupal::service('megamenu')->setMenusStyles($menu_id, $selector, $property_map, $megamenu_options['megamenu_status']);
}

/**
 * Set bar margin bottom.
 *
 * @param $menu_id
 * @param $megamenu_options
 */
function _megamenu_set_menu_styles__bar_margin_bottom($menu_id, $megamenu_options) {
  $menu_id = Html::cleanCssIdentifier($menu_id, megamenu_get_clean_css_filter());
  $selector = '.megamenu--' . $menu_id;
  $property_map = [
    'margin-bottom' => ''
  ];

  // Option is not empty, create styles.
  if (!empty($megamenu_options['bar_margin_bottom'])) {
    $bar_margin_bottom = $megamenu_options['bar_margin_bottom'];

    // If no units provided, assume pixels.
    if (is_numeric($bar_margin_bottom)) {
      $bar_margin_bottom .= 'px';
    }

    $property_map = [
      'margin-bottom' => $bar_margin_bottom
    ];
  }

  \Drupal::service('megamenu')->setMenusStyles($menu_id, $selector, $property_map, $megamenu_options['megamenu_status']);
}

/**
 * Set bar inner width.
 *
 * @param $menu_id
 * @param $megamenu_options
 */
function _megamenu_set_menu_styles__bar_inner_width($menu_id, $megamenu_options) {
  $menu_id = Html::cleanCssIdentifier($menu_id, megamenu_get_clean_css_filter());
  $selector = '.megamenu--' . $menu_id . ' .megamenu-nav';
  $property_map = [
    'max-width' => ''
  ];

  // Option is not empty, create styles.
  if (!empty($megamenu_options['bar_inner_width'])) {
    $bar_inner_width = $megamenu_options['bar_inner_width'];

    // If no units provided, assume pixels.
    if (is_numeric($bar_inner_width)) {
      $bar_inner_width .= 'px';
    }

    $property_map = [
      'max-width' => $bar_inner_width
    ];
  }

  \Drupal::service('megamenu')->setMenusStyles($menu_id, $selector, $property_map, $megamenu_options['megamenu_status']);
}

/**
 * Set submenu inner width.
 *
 * @param $menu_id
 * @param $megamenu_options
 */
function _megamenu_set_menu_styles__submenu_inner_width($menu_id, $megamenu_options) {
  $menu_id = Html::cleanCssIdentifier($menu_id, megamenu_get_clean_css_filter());
  $selector = '.megamenu--' . $menu_id . ' .megamenu-nav';
  $property_map = [
    'max-width' => '',
    'margin-left' => '',
    'margin-right' => ''
  ];

  // Option is not empty, create styles.
  if (!empty($megamenu_options['submenu_inner_width'])) {
    $submenu_inner_width = $megamenu_options['submenu_inner_width'];

    // If no units provided, assume pixels.
    if (is_numeric($submenu_inner_width)) {
      $submenu_inner_width .= 'px';
    }

    $property_map = [
      'max-width' => $submenu_inner_width,
      'margin-left' => 'auto',
      'margin-right' => 'auto'
    ];
  }

  \Drupal::service('megamenu')->setMenusStyles($menu_id, $selector, $property_map, $megamenu_options['megamenu_status']);
}

/**
 * Set submenu max height.
 *
 * @param $menu_id
 * @param $megamenu_options
 */
function _megamenu_set_menu_styles__submenu_max_height($menu_id, $megamenu_options) {
  $menu_id = Html::cleanCssIdentifier($menu_id, megamenu_get_clean_css_filter());
  $selector = ".megamenu--$menu_id.megamenu--transition-slide .megamenu-active > .megamenu-submenu.megamenu-submenu--type-mega, ";
  $selector .= ".megamenu--$menu_id:not(.megamenu--transition-slide) .megamenu-submenu.megamenu-submenu--type-mega";
  $property_map = [
    'max-height' => ''
  ];

  // Option is not empty, create styles.
  if (!empty($megamenu_options['submenu_max_height'])) {
    $submenu_max_height = $megamenu_options['submenu_max_height'];

    // If no units provided, assume pixels.
    if (is_numeric($submenu_max_height)) {
      $submenu_max_height .= 'px';
    }

    $property_map = [
      'max-height' => $submenu_max_height
    ];
  }

  \Drupal::service('megamenu')->setMenusStyles($menu_id, $selector, $property_map, $megamenu_options['megamenu_status']);
}

/**
 * Set image text top padding.
 *
 * @param $menu_id
 * @param $megamenu_options
 */
function _megamenu_set_menu_styles__image_text_top_padding($menu_id, $megamenu_options) {
  $menu_id = Html::cleanCssIdentifier($menu_id, megamenu_get_clean_css_filter());
  $selector = ".megamenu--$menu_id .megamenu-anchor--layout-image_left > .megamenu-anchor__title, ";
  $selector .= ".megamenu--$menu_id .megamenu-anchor--layout-image_right > .megamenu-anchor__title" ;
  $property_map = [
    'padding-top' => ''
  ];

  // Option is not empty, create styles.
  if (!empty($megamenu_options['image_text_top_padding'])) {
    $image_text_top_padding = $megamenu_options['image_text_top_padding'];

    // If no units provided, assume pixels.
    if (is_numeric($image_text_top_padding)) {
      $image_text_top_padding .= 'px';
    }

    $property_map = [
      'padding-top' => $image_text_top_padding
    ];
  }

  \Drupal::service('megamenu')->setMenusStyles($menu_id, $selector, $property_map, $megamenu_options['megamenu_status']);
}

/**
 * Set icon width.
 *
 * @param $menu_id
 * @param $megamenu_options
 */
function _megamenu_set_menu_styles__icon_width($menu_id, $megamenu_options) {
  $menu_id = Html::cleanCssIdentifier($menu_id, megamenu_get_clean_css_filter());
  $selector = ".megamenu--$menu_id .megamenu-icon";
  $property_map = [
    'width' => ''
  ];

  // Option is not empty, create styles.
  if (!empty($megamenu_options['icon_width'])) {
    $icon_width = $megamenu_options['icon_width'];

    // If no units provided, assume pixels.
    if (is_numeric($icon_width)) {
      $icon_width .= 'px';
    }

    $property_map = [
      'width' => $icon_width
    ];
  }

  \Drupal::service('megamenu')->setMenusStyles($menu_id, $selector, $property_map, $megamenu_options['megamenu_status']);
}
