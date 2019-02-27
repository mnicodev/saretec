<?php

/**
 * @file
 * The menu link form hooks.
 */

use Drupal\Core\Form\FormStateInterface;
use Drupal\system\Entity\Menu;
use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\Component\Utility\Html;
use Drupal\megamenu\MegaMenuMenu;
use Drupal\megamenu\menuitems\MegaMenuItemBase;

//---------------------------------------------------------------------------
// HOOKS
//---------------------------------------------------------------------------

/**
 * Implements hook_BASE_FORM_id_alter().
 *
 * Alter menu link edit form for special menu item type (e.g. Home).
 */
function megamenu_form_menu_link_edit_alter(&$form, &$form_state, $form_id) {
  $menu_link = null;
  $options = $form_state->getBuildInfo()['args'][0]->getOptions();
  $menu_link_definition = $form_state->getBuildInfo()['args'][0]->getPluginDefinition();
  $menu_name = $menu_link_definition['menu_name'];

  // Set menu link options only if mega menu is enabled for the menu.
  $menu_entity = \Drupal::entityManager()->getStorage('menu')->load($menu_name);
  $thirdPartySettings = $menu_entity->getThirdPartySettings('megamenu');
  if (empty($thirdPartySettings) || empty($thirdPartySettings['megamenu_status'])) {
    return;
  }

  $menu_tree = megamenu_build_menu_tree($menu_name);
  if (!empty($menu_tree['#items'])) {
    $menu_link = megamenu_find_menu_item($menu_tree['#items'], $menu_link_definition['id']);
  }
  $megamenu_menu = new MegaMenuMenu($menu_name);
  $megamenu_menu_item = new MegaMenuItemBase($megamenu_menu, $menu_link);

  $form = _megamenu_menu_link_form_options($form, $form_state, $megamenu_menu_item);

  $form['#submit'][] = '_megamenu_form_menu_link_edit_submit';
}

/**
 * Callback for menu_link_edit form submit.
 */
function _megamenu_form_menu_link_edit_submit(&$form, &$form_state) {
  $menu_link_manager = \Drupal::service('plugin.manager.menu.link');
  $menu_link_id = $form_state->getValue('menu_link_id');
  $menu_link_definition = $menu_link_manager->getDefinition($menu_link_id);
  $menu_name = $menu_link_definition['menu_name'];

  if ($form_state->getValue('megamenu')) {
    $options = $form_state->getBuildInfo()['args'][0]->getOptions();
    $megamenu_options = ['megamenu' => $form_state->getValue('megamenu')];
    $megamenu_options = _megamenu_menu_link_form_handle_options($megamenu_options);
    ksort($megamenu_options['megamenu']);

    $menu_link_manager->updateDefinition($menu_link_id, ['options' => $megamenu_options]);

    // Handle uploaded images.
    if (!empty($megamenu_options['megamenu']['item_image'])) {
      $fids = $megamenu_options['megamenu']['item_image'];
      foreach ($fids as $fid) {
        $file = file_load($fid);
        if ($file) {
          $file->status = FILE_STATUS_PERMANENT;
          $file->save();
          \Drupal::service('file.usage')->add($file, 'megamenu', 'menu_link', $menu_link_id);
        }
      }
    }
    if (!empty($megamenu_options['megamenu']['submenu_background_image'])) {
      $fids = $megamenu_options['megamenu']['submenu_background_image'];
      foreach ($fids as $fid) {
        $file = file_load($fid);
        if ($file) {
          $file->status = FILE_STATUS_PERMANENT;
          $file->save();
          \Drupal::service('file.usage')->add($file, 'megamenu', 'menu_link', $menu_link_id);
        }
      }
    }

    // Set menu item styles.
    _megamenu_set_item_styles($menu_name, $menu_link_id, $megamenu_options);
  }
}

/**
 * Implements hook_BASE_FORM_id_alter().
 *
 * Alter menu link edit form.
 */
function megamenu_form_menu_link_content_form_alter(&$form, &$form_state, $form_id) {
  $menu_link = null;

  $menu_link_entity = $form_state->getFormObject()->getEntity();
  $menu_link_uuid = $menu_link_entity->uuid();
  $menu_link_id = $menu_link_entity->getEntityTypeId() . ':' . $menu_link_uuid;
  $menu_link_options = $menu_link_entity->link->first()->options ?: [];

  // Set menu link options only if mega menu is enabled for the menu.
  $menu_entity = \Drupal::entityManager()->getStorage('menu')->load($menu_link_entity->getMenuName());
  $menu_name = $menu_entity->id();
  $thirdPartySettings = $menu_entity->getThirdPartySettings('megamenu');
  if (empty($thirdPartySettings) || empty($thirdPartySettings['megamenu_status'])) {
    return;
  }

  $menu_tree = megamenu_build_menu_tree($menu_entity->id());
  if (!empty($menu_tree['#items'])) {
    $menu_link = megamenu_find_menu_item($menu_tree['#items'], $menu_link_id);
  }
  $megamenu_menu = new MegaMenuMenu($menu_name);
  $megamenu_menu_item = new MegaMenuItemBase($megamenu_menu, $menu_link);

  $form = _megamenu_menu_link_form_options($form, $form_state, $megamenu_menu_item);

  $form['actions']['submit']['#submit'][] = '_megamenu_menu_link_content_form_submit';
}

/**
 * Callback for menu_link_content_menu_link_content_form form submit.
 */
function _megamenu_menu_link_content_form_submit(&$form, &$form_state) {
  $menu_link_entity = $form_state->getFormObject()->getEntity();
  $menu_link_uuid = $menu_link_entity->uuid();
  $menu_link_id = $menu_link_entity->getEntityTypeId() . ':' . $menu_link_uuid;
  $menu_link_options = $menu_link_entity->link->first()->options;
  $menu_name = $menu_link_entity->getMenuName();

  if ($form_state->getValue('megamenu')) {
    $megamenu_options = ['megamenu' => $form_state->getValue('megamenu')];
    $megamenu_options = _megamenu_menu_link_form_handle_options($megamenu_options);
    ksort($megamenu_options['megamenu']);

    $menu_link_entity->link->first()->options = array_merge($menu_link_options, $megamenu_options);
    $menu_link_entity->save();

    // Handle uploaded images.
    if (!empty($megamenu_options['megamenu']['item_image'])) {
      $fids = $megamenu_options['megamenu']['item_image'];
      foreach ($fids as $fid) {
        $file = file_load($fid);
        if ($file) {
          $file->status = FILE_STATUS_PERMANENT;
          $file->save();
          \Drupal::service('file.usage')->add($file, 'megamenu', 'menu_link', $menu_link_entity->id());
        }
      }
    }
    if (!empty($megamenu_options['megamenu']['submenu_background_image'])) {
      $fids = $megamenu_options['megamenu']['submenu_background_image'];
      foreach ($fids as $fid) {
        $file = file_load($fid);
        if ($file) {
          $file->status = FILE_STATUS_PERMANENT;
          $file->save();
          \Drupal::service('file.usage')->add($file, 'megamenu', 'menu_link', $menu_link_entity->id());
        }
      }
    }

    // Set menu item styles
    _megamenu_set_item_styles($menu_name, $menu_link_id, $megamenu_options);
  }
}

//---------------------------------------------------------------------------
// OPTIONS
//---------------------------------------------------------------------------

/**
 * Get options for menu form.
 */
function _megamenu_menu_link_form_options(array $form, FormStateInterface $form_state, $megamenu_menu_item) {
  $form['megamenu'] = [
    '#type' => 'container',
    '#weight' => 99,
  ];

  // Menu link type.
  $form['megamenu']['item_type'] = [
    '#type' => 'select',
    '#title' => t('Mega Menu item type'),
    '#options' => [
      'normal' => t('Normal link'),
      'custom_content' => t('Custom content'),
      'drupal_block' => t('Drupal block'),
      'divider' => t('Horizontal divider'),
      'row' => t('Row'),
      'column' => t('Column'),
      'tabs_block' => t('Tabs block'),
    ],
    '#default_value' => $megamenu_menu_item->getOption('item_type'),
    '#parents' => ['megamenu', 'item_type'],
  ];

  // Tabs
  $form['megamenu_tabs'] = [
    '#type' => 'details',
    '#title' => t('Mega Menu item setttings'),
    '#open' => TRUE,
    '#weight' => 99,
  ];

  // General
  $form = _megamenu_menu_link_form_options__general($form, $form_state, $megamenu_menu_item);
  // Row
  $form = _megamenu_menu_link_form_options__row($form, $form_state, $megamenu_menu_item);
  // Divider
  $form = _megamenu_menu_link_form_options__divider($form, $form_state, $megamenu_menu_item);
  // Custom content
  $form = _megamenu_menu_link_form_options__custom_content($form, $form_state, $megamenu_menu_item);
  // Tabs block
  $form = _megamenu_menu_link_form_options__tabs_block($form, $form_state, $megamenu_menu_item);
  // Drupal Block
  $form = _megamenu_menu_link_form_options__drupal_block($form, $form_state, $megamenu_menu_item);
  // Layout
  $form = _megamenu_menu_link_form_options__layout($form, $form_state, $megamenu_menu_item);
  // Column layout
  $form = _megamenu_menu_link_form_options__column_layout($form, $form_state, $megamenu_menu_item);
  // Responsive
  $form = _megamenu_menu_link_form_options__responsive_mobile($form, $form_state, $megamenu_menu_item);
  // Submenu
  $form = _megamenu_menu_link_form_options__submenu($form, $form_state, $megamenu_menu_item);
  // Image
  $form = _megamenu_menu_link_form_options__image($form, $form_state, $megamenu_menu_item);
  // Icon
  $form = _megamenu_menu_link_form_options__icon($form, $form_state, $megamenu_menu_item);

  return $form;
}

/**
 * General
 */
function _megamenu_menu_link_form_options__general(array $form, FormStateInterface $form_state, $megamenu_menu_item) {
  $form['megamenu_tabs']['general_options'] = [
    '#type' => 'details',
    '#title' => t('General'),
    '#group' => 'megamenu_tabs',
    '#states' => [
      'visible' => [
        [':input[name="megamenu[item_type]"]' => ['value' => 'normal']],
      ],
    ],
  ];
  $form['megamenu_tabs']['general_options']['item_display'] = [
    '#type' => 'radios',
    '#title' => t('Item display'),
    '#description' => t('Choose how to display the item. This option will have no effect on Top Level Items.'),
    '#options' => [
      'auto' => t('Automatic (automatically determine the appropriate display type)'),
      'header' => t('Header (display as a submenu column header)'),
      'normal' => t('Normal (display as a normal submenu item)'),
    ],
    '#default_value' => $megamenu_menu_item->getOption('item_display'),
    '#parents' => ['megamenu', 'item_display'],
  ];
  $form['megamenu_tabs']['general_options']['disable_link'] = [
    '#type' => 'checkbox',
    '#title' => t('Disable link'),
    '#description' => t('Use this option to remove the link from the item.'),
    '#default_value' => $megamenu_menu_item->getOption('disable_link'),
    '#parents' => ['megamenu', 'disable_link'],
  ];
  $form['megamenu_tabs']['general_options']['disable_text'] = [
    '#type' => 'checkbox',
    '#title' => t('Disable text'),
    '#description' => t('Use this option to disable the text for this item.'),
    '#default_value' => $megamenu_menu_item->getOption('disable_text'),
    '#parents' => ['megamenu', 'disable_text'],
  ];
  $form['megamenu_tabs']['general_options']['highlight'] = [
    '#type' => 'checkbox',
    '#title' => t('Highlight link'),
    '#description' => t('Use this option to highlight this menu item.'),
    '#default_value' => $megamenu_menu_item->getOption('highlight'),
    '#parents' => ['megamenu', 'highlight'],
  ];
  $form['megamenu_tabs']['general_options']['item_align'] = [
    '#type' => 'select',
    '#title' => t('Alignment'),
    '#description' => t('Choose alignment of this menu item within the menu bar.'),
    '#options' => [
      'auto' => t('Automatic'),
      'left' => t('Left'),
      'right' => t('Right'),
    ],
    '#default_value' => $megamenu_menu_item->getOption('item_align'),
    '#parents' => ['megamenu', 'item_align'],
  ];
  $form['megamenu_tabs']['general_options']['mini_item'] = [
    '#type' => 'checkbox',
    '#title' => t('Mini item'),
    '#description' => t('Use this option to make the item narrower padding-wise compared to other items. The option is useful for grouping icons.'),
    '#default_value' => $megamenu_menu_item->getOption('mini_item'),
    '#parents' => ['megamenu', 'mini_item'],
  ];
  $form['megamenu_tabs']['general_options']['custom_url'] = [
    '#type' => 'textfield',
    '#title' => t('Custom URL override'),
    '#description' => t('Use this option to override URL of this item.'),
    '#default_value' => $megamenu_menu_item->getOption('custom_url'),
    '#parents' => ['megamenu', 'custom_url'],
  ];
  $form['megamenu_tabs']['general_options']['scrollto'] = [
    '#type' => 'textfield',
    '#title' => t('Scroll to'),
    '#description' => t('Enter the selector for an item to scroll to when clicked (e.g. #section-1).'),
    '#default_value' => $megamenu_menu_item->getOption('scrollto'),
    '#parents' => ['megamenu', 'scrollto'],
  ];
  $form['megamenu_tabs']['general_options']['no_wrap'] = [
    '#type' => 'checkbox',
    '#title' => t('Don\'t wrap title/label text'),
    '#description' => t('Use this option to prevent the text from wrapping to a new line.'),
    '#default_value' => $megamenu_menu_item->getOption('no_wrap'),
    '#parents' => ['megamenu', 'no_wrap'],
  ];
  $form['megamenu_tabs']['general_options']['item_trigger'] = [
    '#type' => 'select',
    '#title' => t('Trigger override'),
    '#description' => t('Choose trigger override. The default Trigger for the menu can be configured in the menu settings. Set other option than "Automatic" to trigger submenu of this item differently from other items.'),
    '#options' => [
      'auto' => t('Automatic'),
      'hover' => t('Hover'),
      'hover_intent' => t('Hover Intent'),
      'click' => t('Click'),
    ],
    '#default_value' => $megamenu_menu_item->getOption('item_trigger'),
    '#parents' => ['megamenu', 'item_trigger'],
  ];
  $form['megamenu_tabs']['general_options']['disable_submenu_indicator'] = [
    '#type' => 'checkbox',
    '#title' => t('Disable submenu indicator'),
    '#description' => t('Use this option to disable submenu indicators (i.e. arrow).'),
    '#default_value' => $megamenu_menu_item->getOption('disable_submenu_indicator'),
    '#parents' => ['megamenu', 'disable_submenu_indicator'],
  ];
  $form['megamenu_tabs']['general_options']['disable_current'] = [
    '#type' => 'checkbox',
    '#title' => t('Disable current'),
    '#description' => t('Use this option to disable the current menu item classes.'),
    '#default_value' => $megamenu_menu_item->getOption('disable_current'),
    '#parents' => ['megamenu', 'disable_current'],
  ];
  $form['megamenu_tabs']['general_options']['anchor_class'] = [
    '#type' => 'textfield',
    '#title' => t('Anchor class'),
    '#description' => t('Space separated classes to be applied to the anchor element.'),
    '#default_value' => $megamenu_menu_item->getOption('anchor_class'),
    '#parents' => ['megamenu', 'anchor_class'],
  ];
  $form['megamenu_tabs']['general_options']['anchor_id'] = [
    '#type' => 'textfield',
    '#title' => t('Anchor ID'),
    '#description' => t('An ID to be applied to the anchor element.'),
    '#default_value' => $megamenu_menu_item->getOption('anchor_id'),
    '#parents' => ['megamenu', 'anchor_id'],
  ];

  $form['megamenu_tabs']['general_options']['anchor_link_target'] = [
    '#type' => 'checkbox',
    '#title' => t('Open link in new tab'),
    '#description' => 'Use this option to open link in new tab.',
    '#default_value' => $megamenu_menu_item->getOption('anchor_link_target'),
    '#parents' => ['megamenu', 'anchor_link_target'],
  ];
  $form['megamenu_tabs']['general_options']['anchor_attr_title'] = [
    '#type' => 'textfield',
    '#title' => t('Title attribute'),
    '#description' => t('Use this option to set title attribute.'),
    '#default_value' => $megamenu_menu_item->getOption('anchor_attr_title'),
    '#parents' => ['megamenu', 'anchor_attr_title'],
  ];
  $form['megamenu_tabs']['general_options']['anchor_xfn'] = [
    '#type' => 'textfield',
    '#title' => t('Link relationship (XFN)'),
    '#description' => t('Use this field to set link relationship.'),
    '#default_value' => $megamenu_menu_item->getOption('anchor_xfn'),
    '#parents' => ['megamenu', 'anchor_xfn'],
  ];

  return $form;
}

/**
 * Row
 */
function _megamenu_menu_link_form_options__row(array $form, FormStateInterface $form_state, $megamenu_menu_item) {
  $form['megamenu_tabs']['row_options'] = [
    '#type' => 'details',
    '#title' => t('Row'),
    '#group' => 'megamenu_tabs',
    '#states' => [
      'visible' => [
        [':input[name="megamenu[item_type]"]' => ['value' => 'row']],
      ],
    ],
  ];
  $form['megamenu_tabs']['row_options']['submenu_column_default__row'] = [
    '#type' => 'select',
    '#title' => t('Submenu column default'),
    '#description' => t('Select the number of columns per row that the submenu should be broken into by default. This value can be overridden on individual items.'),
    '#options' => [
      'Basic' => [
        'auto' => t('Automatic'),
        'natural' => t('Natural'),
      ],
      'Halves' => [
        '1-2' => t('1/2'),
      ],
      'Thirds' => [
        '1-3' => t('1/3'),
        '2-3' => t('2/3'),
      ],
      'Quarters' => [
        '1-4' => t('1/4'),
        '3-4' => t('3/4'),
      ],
      'Fifths' => [
        '1-5' => t('1/5'),
        '2-5' => t('2/5'),
        '3-5' => t('3/5'),
        '4-5' => t('4/5'),
      ],
      'Sixths' => [
        '1-6' => t('1/6'),
        '5-6' => t('1/6'),
      ],
      'Sevenths' => [
        '1-7' => t('1/7'),
        '2-7' => t('2/7'),
        '3-7' => t('3/7'),
        '4-7' => t('4/7'),
        '5-7' => t('5/7'),
        '6-7' => t('6/7'),
      ],
      'Eighths' => [
        '1-8' => t('1/8'),
        '3-8' => t('3/8'),
        '5-8' => t('5/8'),
        '7-8' => t('7/8'),
      ],
      'Ninths' => [
        '1-9' => t('1/9'),
        '2-9' => t('2/9'),
        '4-9' => t('4/9'),
        '5-9' => t('5/9'),
        '7-9' => t('7/9'),
        '8-9' => t('8/9'),
      ],
      'Tenths' => [
        '1-10' => t('1/10'),
        '3-10' => t('3/10'),
        '7-10' => t('7/10'),
        '9-10' => t('9/10'),
      ],
      'Elevenths' => [
        '1-11' => t('1/11'),
        '2-11' => t('2/11'),
        '3-11' => t('3/11'),
        '4-11' => t('4/11'),
        '5-11' => t('5/11'),
        '6-11' => t('6/11'),
        '7-11' => t('7/11'),
        '8-11' => t('8/11'),
        '9-11' => t('9/11'),
        '10-11' => t('10/11'),
      ],
      'Twelfths' => [
        '1-12' => t('1/12'),
        '5-12' => t('5/12'),
        '7-12' => t('7/12'),
        '11-12' => t('11/12'),
      ],
    ],
    '#default_value' => $megamenu_menu_item->getOption('submenu_column_default'),
    '#parents' => ['megamenu', 'submenu_column_default__row'],
  ];
  $form['megamenu_tabs']['row_options']['submenu_column_autoclear__row'] = [
    '#type' => 'checkbox',
    '#title' => t('Auto clear'),
    '#description' => t('Use this option to automatically start a new row of items every X items. For example, if you choose a Submenu column default of 1/4, the 5th item will start a new column automatically. Disable this option if you are adjusting item columns manually.'),
    '#default_value' => $megamenu_menu_item->getOption('submenu_column_autoclear'),
    '#parents' => ['megamenu', 'submenu_column_autoclear__row'],
  ];
  $form['megamenu_tabs']['row_options']['row_padding'] = [
    '#type' => 'textfield',
    '#title' => t('Row padding (experimental)'),
    '#description' => t('Enter the padding on this specific row. For example, 10px 0 10px 0 will set top and bottom padding to 10px.'),
    '#default_value' => $megamenu_menu_item->getOption('row_padding'),
    '#parents' => ['megamenu', 'row_padding'],
  ];
  $form['megamenu_tabs']['row_options']['grid_row'] = [
    '#type' => 'checkbox',
    '#title' => t('Grid row'),
    '#description' => t('Use this option to space this row as a grid with equal padding. The option is useful for image grids.'),
    '#default_value' => $megamenu_menu_item->getOption('grid_row'),
    '#parents' => ['megamenu', 'grid_row'],
  ];
  return $form;
}

/**
 * Divider
 */
function _megamenu_menu_link_form_options__divider(array $form, FormStateInterface $form_state, $megamenu_menu_item) {
  $form['megamenu_tabs']['divider_options'] = [
    '#type' => 'details',
    '#title' => t('Divider'),
    '#group' => 'megamenu_tabs',
    '#open' => TRUE,
    '#states' => [
      'visible' => [
        [':input[name="megamenu[item_type]"]' => ['value' => 'divider']],
      ],
    ],
  ];
  $form['megamenu_tabs']['divider_options']['divider_no_settings'] = [
    '#type' => 'markup',
    '#markup' => t('The divider menu item does not have custom settings.'),
  ];
  return $form;
}

/**
 * Custom content
 */
function _megamenu_menu_link_form_options__custom_content(array $form, FormStateInterface $form_state, $megamenu_menu_item) {
  $form['megamenu_tabs']['custom_content_options'] = [
    '#type' => 'details',
    '#title' => t('Custom content'),
    '#group' => 'megamenu_tabs',
    '#states' => [
      'visible' => [
        [':input[name="megamenu[item_type]"]' => ['value' => 'normal']],
        [':input[name="megamenu[item_type]"]' => ['value' => 'custom_content']],
      ],
    ],
  ];
  $form['megamenu_tabs']['custom_content_options']['custom_content'] = [
    '#type' => 'text_format',
    '#title' => t('Custom content'),
    '#rows' => 3,
    '#format' => $megamenu_menu_item->getOption('custom_content_format'),
    '#default_value' => $megamenu_menu_item->getOption('custom_content'),
    '#parents' => ['megamenu', 'custom_content'],
  ];
  $form['megamenu_tabs']['custom_content_options']['pad_custom_content'] = [
    '#type' => 'checkbox',
    '#title' => t('Pad custom content'),
    '#description' => t('Use this option to pad the content area so that it aligns similarly to other menu items.'),
    '#default_value' => $megamenu_menu_item->getOption('pad_custom_content'),
    '#parents' => ['megamenu', 'pad_custom_content'],
  ];
  return $form;
}

/**
 * Tabs
 */
function _megamenu_menu_link_form_options__tabs_block(array $form, FormStateInterface $form_state, $megamenu_menu_item) {
  $form['megamenu_tabs']['tabs_block_options'] = [
    '#type' => 'details',
    '#title' => t('Tabs block'),
    '#description' => t('A tabs block is just a wrapper item, it does not create any content on its own. You need to create child items in order to create the Tab Toggles. Also, you\'ll need to create child items to those toggles items to create the panes that will be shown when the toggles are activated.'),
    '#group' => 'megamenu_tabs',
    '#states' => [
      'visible' => [
        [':input[name="megamenu[item_type]"]' => ['value' => 'tabs_block']],
      ],
    ],
  ];
  $form['megamenu_tabs']['tabs_block_options']['tab_layout'] = [
    '#type' => 'radios',
    '#title' => t('Tab layout'),
    '#description' => t('Choose tab layout.'),
    '#options' => [
      'left' => t('Left (tabs on the left, content panels on the right)'),
      'right' => t('Right (tabs on the right, content panels on the left)'),
      'top' => t('Top (tabs on the top, content panels on the bottom)'),
      'bottom' => t('Bottom (tabs on the bottom, content panels on the top)'),
    ],
    '#default_value' => $megamenu_menu_item->getOption('tab_layout'),
    '#parents' => ['megamenu', 'tab_layout'],
  ];
  $form['megamenu_tabs']['tabs_block_options']['tab_block_columns'] = [
    '#type' => 'select',
    '#title' => t('Tab block width'),
    '#description' => t('Use this option to set the width of the whole tab block.'),
    '#options' => [
      'Basic' => [
        'auto' => t('Automatic'),
        'natural' => t('Natural'),
      ],
      'Halves' => [
        '1-2' => t('1/2'),
      ],
      'Thirds' => [
        '1-3' => t('1/3'),
        '2-3' => t('2/3'),
      ],
      'Quarters' => [
        '1-4' => t('1/4'),
        '3-4' => t('3/4'),
      ],
      'Fifths' => [
        '1-5' => t('1/5'),
        '2-5' => t('2/5'),
        '3-5' => t('3/5'),
        '4-5' => t('4/5'),
      ],
      'Sixths' => [
        '1-6' => t('1/6'),
        '5-6' => t('1/6'),
      ],
      'Sevenths' => [
        '1-7' => t('1/7'),
        '2-7' => t('2/7'),
        '3-7' => t('3/7'),
        '4-7' => t('4/7'),
        '5-7' => t('5/7'),
        '6-7' => t('6/7'),
      ],
      'Eighths' => [
        '1-8' => t('1/8'),
        '3-8' => t('3/8'),
        '5-8' => t('5/8'),
        '7-8' => t('7/8'),
      ],
      'Ninths' => [
        '1-9' => t('1/9'),
        '2-9' => t('2/9'),
        '4-9' => t('4/9'),
        '5-9' => t('5/9'),
        '7-9' => t('7/9'),
        '8-9' => t('8/9'),
      ],
      'Tenths' => [
        '1-10' => t('1/10'),
        '3-10' => t('3/10'),
        '7-10' => t('7/10'),
        '9-10' => t('9/10'),
      ],
      'Elevenths' => [
        '1-11' => t('1/11'),
        '2-11' => t('2/11'),
        '3-11' => t('3/11'),
        '4-11' => t('4/11'),
        '5-11' => t('5/11'),
        '6-11' => t('6/11'),
        '7-11' => t('7/11'),
        '8-11' => t('8/11'),
        '9-11' => t('9/11'),
        '10-11' => t('10/11'),
      ],
      'Twelfths' => [
        '1-12' => t('1/12'),
        '5-12' => t('5/12'),
        '7-12' => t('7/12'),
        '11-12' => t('11/12'),
      ],
    ],
    '#default_value' => $megamenu_menu_item->getOption('tab_block_columns'),
    '#parents' => ['megamenu', 'tab_block_columns'],
  ];
  $form['megamenu_tabs']['tabs_block_options']['tabs_group_layout'] = [
    '#type' => 'select',
    '#title' => t('Tabs toggles layout width'),
    '#description' => t('
      Represents the width of the tab toggles within the tab block.
      <br/>
      <b>Note:</b> If tab layout is set to left or right, you\'ll want this option to follow panels layout width. If tab layout is set to top or bottom, you\'ll want this to be set to full.
    '),
    '#options' => [
      'Basic' => [
        'auto' => t('Automatic'),
        'natural' => t('Natural'),
      ],
      'Halves' => [
        '1-2' => t('1/2'),
      ],
      'Thirds' => [
        '1-3' => t('1/3'),
        '2-3' => t('2/3'),
      ],
      'Quarters' => [
        '1-4' => t('1/4'),
        '3-4' => t('3/4'),
      ],
      'Fifths' => [
        '1-5' => t('1/5'),
        '2-5' => t('2/5'),
        '3-5' => t('3/5'),
        '4-5' => t('4/5'),
      ],
      'Sixths' => [
        '1-6' => t('1/6'),
        '5-6' => t('1/6'),
      ],
      'Sevenths' => [
        '1-7' => t('1/7'),
        '2-7' => t('2/7'),
        '3-7' => t('3/7'),
        '4-7' => t('4/7'),
        '5-7' => t('5/7'),
        '6-7' => t('6/7'),
      ],
      'Eighths' => [
        '1-8' => t('1/8'),
        '3-8' => t('3/8'),
        '5-8' => t('5/8'),
        '7-8' => t('7/8'),
      ],
      'Ninths' => [
        '1-9' => t('1/9'),
        '2-9' => t('2/9'),
        '4-9' => t('4/9'),
        '5-9' => t('5/9'),
        '7-9' => t('7/9'),
        '8-9' => t('8/9'),
      ],
      'Tenths' => [
        '1-10' => t('1/10'),
        '3-10' => t('3/10'),
        '7-10' => t('7/10'),
        '9-10' => t('9/10'),
      ],
      'Elevenths' => [
        '1-11' => t('1/11'),
        '2-11' => t('2/11'),
        '3-11' => t('3/11'),
        '4-11' => t('4/11'),
        '5-11' => t('5/11'),
        '6-11' => t('6/11'),
        '7-11' => t('7/11'),
        '8-11' => t('8/11'),
        '9-11' => t('9/11'),
        '10-11' => t('10/11'),
      ],
      'Twelfths' => [
        '1-12' => t('1/12'),
        '5-12' => t('5/12'),
        '7-12' => t('7/12'),
        '11-12' => t('11/12'),
      ],
    ],
    '#default_value' => $megamenu_menu_item->getOption('tabs_group_layout'),
    '#parents' => ['megamenu', 'tabs_group_layout'],
  ];
  $form['megamenu_tabs']['tabs_block_options']['panels_group_layout'] = [
    '#type' => 'select',
    '#title' => t('Panels layout width'),
    '#description' => t('
      Represents the width of the content panels within the tab block.
      <br/>
      <b>Note:</b> If tab layout is set to left or right, you\'ll want this option to follow tabs toggles layout width. If tab layout is set to top or bottom, you\'ll want this to be set to full.
    '),
    '#options' => [
      'Basic' => [
        'auto' => t('Automatic'),
        'natural' => t('Natural'),
      ],
      'Halves' => [
        '1-2' => t('1/2'),
      ],
      'Thirds' => [
        '1-3' => t('1/3'),
        '2-3' => t('2/3'),
      ],
      'Quarters' => [
        '1-4' => t('1/4'),
        '3-4' => t('3/4'),
      ],
      'Fifths' => [
        '1-5' => t('1/5'),
        '2-5' => t('2/5'),
        '3-5' => t('3/5'),
        '4-5' => t('4/5'),
      ],
      'Sixths' => [
        '1-6' => t('1/6'),
        '5-6' => t('1/6'),
      ],
      'Sevenths' => [
        '1-7' => t('1/7'),
        '2-7' => t('2/7'),
        '3-7' => t('3/7'),
        '4-7' => t('4/7'),
        '5-7' => t('5/7'),
        '6-7' => t('6/7'),
      ],
      'Eighths' => [
        '1-8' => t('1/8'),
        '3-8' => t('3/8'),
        '5-8' => t('5/8'),
        '7-8' => t('7/8'),
      ],
      'Ninths' => [
        '1-9' => t('1/9'),
        '2-9' => t('2/9'),
        '4-9' => t('4/9'),
        '5-9' => t('5/9'),
        '7-9' => t('7/9'),
        '8-9' => t('8/9'),
      ],
      'Tenths' => [
        '1-10' => t('1/10'),
        '3-10' => t('3/10'),
        '7-10' => t('7/10'),
        '9-10' => t('9/10'),
      ],
      'Elevenths' => [
        '1-11' => t('1/11'),
        '2-11' => t('2/11'),
        '3-11' => t('3/11'),
        '4-11' => t('4/11'),
        '5-11' => t('5/11'),
        '6-11' => t('6/11'),
        '7-11' => t('7/11'),
        '8-11' => t('8/11'),
        '9-11' => t('9/11'),
        '10-11' => t('10/11'),
      ],
      'Twelfths' => [
        '1-12' => t('1/12'),
        '5-12' => t('5/12'),
        '7-12' => t('7/12'),
        '11-12' => t('11/12'),
      ],
    ],
    '#default_value' => $megamenu_menu_item->getOption('panels_group_layout'),
    '#parents' => ['megamenu', 'panels_group_layout'],
  ];
  $form['megamenu_tabs']['tabs_block_options']['submenu_column_default__tabs_block'] = [
    '#type' => 'select',
    '#title' => t('Panels columns default'),
    '#description' => t('Represents the number of columns per row that the content panel should be broken into by default. This value can be overridden on individual items.'),
    '#options' => [
      'Basic' => [
        'auto' => t('Automatic'),
        'natural' => t('Natural'),
      ],
      'Halves' => [
        '1-2' => t('1/2'),
      ],
      'Thirds' => [
        '1-3' => t('1/3'),
        '2-3' => t('2/3'),
      ],
      'Quarters' => [
        '1-4' => t('1/4'),
        '3-4' => t('3/4'),
      ],
      'Fifths' => [
        '1-5' => t('1/5'),
        '2-5' => t('2/5'),
        '3-5' => t('3/5'),
        '4-5' => t('4/5'),
      ],
      'Sixths' => [
        '1-6' => t('1/6'),
        '5-6' => t('1/6'),
      ],
      'Sevenths' => [
        '1-7' => t('1/7'),
        '2-7' => t('2/7'),
        '3-7' => t('3/7'),
        '4-7' => t('4/7'),
        '5-7' => t('5/7'),
        '6-7' => t('6/7'),
      ],
      'Eighths' => [
        '1-8' => t('1/8'),
        '3-8' => t('3/8'),
        '5-8' => t('5/8'),
        '7-8' => t('7/8'),
      ],
      'Ninths' => [
        '1-9' => t('1/9'),
        '2-9' => t('2/9'),
        '4-9' => t('4/9'),
        '5-9' => t('5/9'),
        '7-9' => t('7/9'),
        '8-9' => t('8/9'),
      ],
      'Tenths' => [
        '1-10' => t('1/10'),
        '3-10' => t('3/10'),
        '7-10' => t('7/10'),
        '9-10' => t('9/10'),
      ],
      'Elevenths' => [
        '1-11' => t('1/11'),
        '2-11' => t('2/11'),
        '3-11' => t('3/11'),
        '4-11' => t('4/11'),
        '5-11' => t('5/11'),
        '6-11' => t('6/11'),
        '7-11' => t('7/11'),
        '8-11' => t('8/11'),
        '9-11' => t('9/11'),
        '10-11' => t('10/11'),
      ],
      'Twelfths' => [
        '1-12' => t('1/12'),
        '5-12' => t('5/12'),
        '7-12' => t('7/12'),
        '11-12' => t('11/12'),
      ],
    ],
    '#default_value' => $megamenu_menu_item->getOption('submenu_column_default'),
    '#parents' => ['megamenu', 'submenu_column_default__tabs_block'],
  ];
  $form['megamenu_tabs']['tabs_block_options']['panels_grid'] = [
    '#type' => 'checkbox',
    '#title' => t('Grid panels'),
    '#description' => t('Use this option to make the space between the targets equal to the space on the content panel edges. The option is useful for image grids in the Tab Content Panel.'),
    '#default_value' => $megamenu_menu_item->getOption('panels_grid'),
    '#parents' => ['megamenu', 'panels_grid'],
  ];
  $form['megamenu_tabs']['tabs_block_options']['panels_padding'] = [
    '#type' => 'textfield',
    '#title' => t('Panel padding (experimental)'),
    '#description' => t('Use this option to add padding to panels. The option is useful if you need to make the spacing at the edges of a row equal to that between the columns (by doubling it).'),
    '#default_value' => $megamenu_menu_item->getOption('panels_padding'),
    '#parents' => ['megamenu', 'panels_padding'],
  ];
  $form['megamenu_tabs']['tabs_block_options']['show_default_panel'] = [
    '#type' => 'checkbox',
    '#title' => t('Show default panel'),
    '#description' => t('Use this option to show the first tab\'s panel by default. Otherwise a blank area will be displayed.'),
    '#default_value' => $megamenu_menu_item->getOption('show_default_panel'),
    '#parents' => ['megamenu', 'show_default_panel'],
  ];
  $form['megamenu_tabs']['tabs_block_options']['tabs_trigger'] = [
    '#type' => 'select',
    '#title' => t('Trigger'),
    '#description' => t('Choose tabs trigger.'),
    '#options' => [
      'auto' => t('Automatic'),
      'mouseover' => t('Hover'),
      'click' => t('Click'),
    ],
    '#default_value' => $megamenu_menu_item->getOption('tabs_trigger'),
    '#parents' => ['megamenu', 'tabs_trigger'],
  ];
  return $form;
}

/**
 * Drupal block
 */
function _megamenu_menu_link_form_options__drupal_block(array $form, FormStateInterface $form_state, $megamenu_menu_item) {
  $form['megamenu_tabs']['drupal_block_options'] = [
    '#type' => 'details',
    '#title' => t('Drupal block'),
    '#group' => 'megamenu_tabs',
    '#states' => [
      'visible' => [
        [':input[name="megamenu[item_type]"]' => ['value' => 'drupal_block']],
      ],
    ],
  ];
  $form['megamenu_tabs']['drupal_block_options']['drupal_block_id'] = [
    '#type' => 'select',
    '#title' => t('Drupal block'),
    '#description' => t('Choose drupal block to be displayed.'),
    '#options' => megamenu_get_drupal_blocks_list(),
    '#default_value' => $megamenu_menu_item->getOption('drupal_block_id'),
    '#parents' => ['megamenu', 'drupal_block_id'],
  ];
  $form['megamenu_tabs']['drupal_block_options']['pad_drupal_block'] = [
    '#type' => 'checkbox',
    '#title' => t('Pad block'),
    '#description' => t('Use this option to pad the block area so that it aligns similarly to other menu items.'),
    '#default_value' => $megamenu_menu_item->getOption('pad_drupal_block'),
    '#parents' => ['megamenu', 'pad_drupal_block'],
  ];
  return $form;
}

/**
 * Layout
 */
function _megamenu_menu_link_form_options__layout(array $form, FormStateInterface $form_state, $megamenu_menu_item) {
  $form['megamenu_tabs']['layout_options'] = [
    '#type' => 'details',
    '#title' => t('Layout'),
    '#group' => 'megamenu_tabs',
    '#states' => [
      'visible' => [
        [':input[name="megamenu[item_type]"]' => ['value' => 'normal']],
        [':input[name="megamenu[item_type]"]' => ['value' => 'custom_content']],
        [':input[name="megamenu[item_type]"]' => ['value' => 'drupal_block']],
      ],
    ],
  ];
  $form['megamenu_tabs']['layout_options']['clear_row__layout'] = [
    '#type' => 'checkbox',
    '#title' => t('New row'),
    '#description' => t('Use this option to clear the previous row and start a new one with this item.'),
    '#default_value' => $megamenu_menu_item->getOption('clear_row'),
    '#parents' => ['megamenu', 'clear_row__layout'],
  ];
  $form['megamenu_tabs']['layout_options']['columns__layout'] = [
    '#type' => 'select',
    '#title' => t('Columns width'),
    '#description' => t('Represents the fraction of the submenu width that the item or column will occupy.<br/><b>Note:</b> If you set the columns to a fraction, the wrapper for this item must be either full width or have an explicit width set. For submenu items, that means setting an explicit or full width submenu. For top level items, that means setting an explicit width or full width menu bar.'),
    '#options' => [
      'Basic' => [
        'auto' => t('Automatic'),
        'natural' => t('Natural'),
      ],
      'Halves' => [
        '1-2' => t('1/2'),
      ],
      'Thirds' => [
        '1-3' => t('1/3'),
        '2-3' => t('2/3'),
      ],
      'Quarters' => [
        '1-4' => t('1/4'),
        '3-4' => t('3/4'),
      ],
      'Fifths' => [
        '1-5' => t('1/5'),
        '2-5' => t('2/5'),
        '3-5' => t('3/5'),
        '4-5' => t('4/5'),
      ],
      'Sixths' => [
        '1-6' => t('1/6'),
        '5-6' => t('1/6'),
      ],
      'Sevenths' => [
        '1-7' => t('1/7'),
        '2-7' => t('2/7'),
        '3-7' => t('3/7'),
        '4-7' => t('4/7'),
        '5-7' => t('5/7'),
        '6-7' => t('6/7'),
      ],
      'Eighths' => [
        '1-8' => t('1/8'),
        '3-8' => t('3/8'),
        '5-8' => t('5/8'),
        '7-8' => t('7/8'),
      ],
      'Ninths' => [
        '1-9' => t('1/9'),
        '2-9' => t('2/9'),
        '4-9' => t('4/9'),
        '5-9' => t('5/9'),
        '7-9' => t('7/9'),
        '8-9' => t('8/9'),
      ],
      'Tenths' => [
        '1-10' => t('1/10'),
        '3-10' => t('3/10'),
        '7-10' => t('7/10'),
        '9-10' => t('9/10'),
      ],
      'Elevenths' => [
        '1-11' => t('1/11'),
        '2-11' => t('2/11'),
        '3-11' => t('3/11'),
        '4-11' => t('4/11'),
        '5-11' => t('5/11'),
        '6-11' => t('6/11'),
        '7-11' => t('7/11'),
        '8-11' => t('8/11'),
        '9-11' => t('9/11'),
        '10-11' => t('10/11'),
      ],
      'Twelfths' => [
        '1-12' => t('1/12'),
        '5-12' => t('5/12'),
        '7-12' => t('7/12'),
        '11-12' => t('11/12'),
      ],
    ],
    '#default_value' => $megamenu_menu_item->getOption('columns'),
    '#parents' => ['megamenu', 'columns__layout'],
  ];
  $form['megamenu_tabs']['layout_options']['item_layout'] = [
    '#type' => 'select',
    '#title' => t('Item layout'),
    '#description' => t('Use this option to set item layout.'),
    '#options' => [
      'Core' => [
        'default' => t('Default (layout will be automatically determined)'),
        'text_only' => t('Text Only (no image or icon, just the text)'),
      ],
      'Icons' => [
        'icon_left' => t('Icon Left'),
      ],
      'images' => [
        'image_left' => t('Image Left'),
        'image_right' => t('Image Right'),
        'image_above' => t('Image Above'),
        'image_below' => t('Image Below'),
        'image_only' => t('Image Only'),
      ],
    ],
    '#default_value' => $megamenu_menu_item->getOption('item_layout'),
    '#parents' => ['megamenu', 'item_layout'],
  ];
  $form['megamenu_tabs']['layout_options']['content_alignment'] = [
    '#type' => 'select',
    '#title' => t('Item content alignment'),
    '#description' => t('Use this option to set content alignment. Choose "Center" to have a centered image stacked above the title when used in conjunction with the "Image Above" layout.'),
    '#options' => [
      'default' => t('Default'),
      'left' => t('Left'),
      'center' => t('Center'),
      'right' => t('Right'),
    ],
    '#default_value' => $megamenu_menu_item->getOption('content_alignment'),
    '#parents' => ['megamenu', 'content_alignment'],
  ];
  return $form;
}

/**
 * Column Layout
 */
function _megamenu_menu_link_form_options__column_layout(array $form, FormStateInterface $form_state, $megamenu_menu_item) {
  $form['megamenu_tabs']['column_layout_options'] = [
    '#type' => 'details',
    '#title' => t('Column layout'),
    '#group' => 'megamenu_tabs',
    '#states' => [
      'visible' => [
        [':input[name="megamenu[item_type]"]' => ['value' => 'column']],
      ],
    ],
  ];
  $form['megamenu_tabs']['column_layout_options']['columns__column_layout'] = [
    '#type' => 'select',
    '#title' => t('Columns width'),
    '#description' => t('Represents the fraction of the submenu width that the item or column will occupy.<br/><b>Note:</b> If you set the columns to a fraction, the wrapper for this item must be either full width or have an explicit width set. For submenu items, that means setting an explicit or full width submenu. For top level items, that means setting an explicit width or full width menu bar.'),
    '#options' => [
      'Basic' => [
        'auto' => t('Automatic'),
        'natural' => t('Natural'),
      ],
      'Halves' => [
        '1-2' => t('1/2'),
      ],
      'Thirds' => [
        '1-3' => t('1/3'),
        '2-3' => t('2/3'),
      ],
      'Quarters' => [
        '1-4' => t('1/4'),
        '3-4' => t('3/4'),
      ],
      'Fifths' => [
        '1-5' => t('1/5'),
        '2-5' => t('2/5'),
        '3-5' => t('3/5'),
        '4-5' => t('4/5'),
      ],
      'Sixths' => [
        '1-6' => t('1/6'),
        '5-6' => t('1/6'),
      ],
      'Sevenths' => [
        '1-7' => t('1/7'),
        '2-7' => t('2/7'),
        '3-7' => t('3/7'),
        '4-7' => t('4/7'),
        '5-7' => t('5/7'),
        '6-7' => t('6/7'),
      ],
      'Eighths' => [
        '1-8' => t('1/8'),
        '3-8' => t('3/8'),
        '5-8' => t('5/8'),
        '7-8' => t('7/8'),
      ],
      'Ninths' => [
        '1-9' => t('1/9'),
        '2-9' => t('2/9'),
        '4-9' => t('4/9'),
        '5-9' => t('5/9'),
        '7-9' => t('7/9'),
        '8-9' => t('8/9'),
      ],
      'Tenths' => [
        '1-10' => t('1/10'),
        '3-10' => t('3/10'),
        '7-10' => t('7/10'),
        '9-10' => t('9/10'),
      ],
      'Elevenths' => [
        '1-11' => t('1/11'),
        '2-11' => t('2/11'),
        '3-11' => t('3/11'),
        '4-11' => t('4/11'),
        '5-11' => t('5/11'),
        '6-11' => t('6/11'),
        '7-11' => t('7/11'),
        '8-11' => t('8/11'),
        '9-11' => t('9/11'),
        '10-11' => t('10/11'),
      ],
      'Twelfths' => [
        '1-12' => t('1/12'),
        '5-12' => t('5/12'),
        '7-12' => t('7/12'),
        '11-12' => t('11/12'),
      ],
    ],
    '#default_value' => $megamenu_menu_item->getOption('columns'),
    '#parents' => ['megamenu', 'columns__column_layout'],
  ];
  $form['megamenu_tabs']['column_layout_options']['clear_row__column_layout'] = [
    '#type' => 'checkbox',
    '#title' => t('New row'),
    '#description' => t('Use this option to clear the previous row and start a new one with this item.'),
    '#default_value' => $megamenu_menu_item->getOption('clear_row'),
    '#parents' => ['megamenu', 'clear_row__column_layout'],
  ];
  $form['megamenu_tabs']['column_layout_options']['submenu_column_default__column_layout'] = [
    '#type' => 'select',
    '#title' => t('Submenu column default'),
    '#description' => t('Represents the number of columns per row that the items within this column should be broken into by default. This value can be overridden on individual items.'),
    '#options' => [
      'Basic' => [
        'auto' => t('Automatic'),
        'natural' => t('Natural'),
      ],
      'Halves' => [
        '1-2' => t('1/2'),
      ],
      'Thirds' => [
        '1-3' => t('1/3'),
        '2-3' => t('2/3'),
      ],
      'Quarters' => [
        '1-4' => t('1/4'),
        '3-4' => t('3/4'),
      ],
      'Fifths' => [
        '1-5' => t('1/5'),
        '2-5' => t('2/5'),
        '3-5' => t('3/5'),
        '4-5' => t('4/5'),
      ],
      'Sixths' => [
        '1-6' => t('1/6'),
        '5-6' => t('1/6'),
      ],
      'Sevenths' => [
        '1-7' => t('1/7'),
        '2-7' => t('2/7'),
        '3-7' => t('3/7'),
        '4-7' => t('4/7'),
        '5-7' => t('5/7'),
        '6-7' => t('6/7'),
      ],
      'Eighths' => [
        '1-8' => t('1/8'),
        '3-8' => t('3/8'),
        '5-8' => t('5/8'),
        '7-8' => t('7/8'),
      ],
      'Ninths' => [
        '1-9' => t('1/9'),
        '2-9' => t('2/9'),
        '4-9' => t('4/9'),
        '5-9' => t('5/9'),
        '7-9' => t('7/9'),
        '8-9' => t('8/9'),
      ],
      'Tenths' => [
        '1-10' => t('1/10'),
        '3-10' => t('3/10'),
        '7-10' => t('7/10'),
        '9-10' => t('9/10'),
      ],
      'Elevenths' => [
        '1-11' => t('1/11'),
        '2-11' => t('2/11'),
        '3-11' => t('3/11'),
        '4-11' => t('4/11'),
        '5-11' => t('5/11'),
        '6-11' => t('6/11'),
        '7-11' => t('7/11'),
        '8-11' => t('8/11'),
        '9-11' => t('9/11'),
        '10-11' => t('10/11'),
      ],
      'Twelfths' => [
        '1-12' => t('1/12'),
        '5-12' => t('5/12'),
        '7-12' => t('7/12'),
        '11-12' => t('11/12'),
      ],
    ],
    '#default_value' => $megamenu_menu_item->getOption('submenu_column_default'),
    '#parents' => ['megamenu', 'submenu_column_default__column_layout'],
  ];
  $form['megamenu_tabs']['column_layout_options']['submenu_column_autoclear__column_layout'] = [
    '#type' => 'checkbox',
    '#title' => t('Auto row'),
    '#description' => t('Use this option to automatically start a new row every X items. For example, if you choose a Submenu column default of 1/4, the 5th item will start a new column automatically. Disable this option if you are adjusting item columns manually.'),
    '#default_value' => $megamenu_menu_item->getOption('submenu_column_autoclear'),
    '#parents' => ['megamenu', 'submenu_column_autoclear__column_layout'],
  ];
  return $form;
}

/**
 * Responsive
 */
function _megamenu_menu_link_form_options__responsive_mobile(array $form, FormStateInterface $form_state, $megamenu_menu_item) {
  $form['megamenu_tabs']['responsive_options'] = [
    '#type' => 'details',
    '#title' => t('Responsive'),
    '#group' => 'megamenu_tabs',
    '#states' => [
      'visible' => [
        [':input[name="megamenu[item_type]"]' => ['value' => 'normal']],
        [':input[name="megamenu[item_type]"]' => ['value' => 'custom_content']],
        [':input[name="megamenu[item_type]"]' => ['value' => 'row']],
        [':input[name="megamenu[item_type]"]' => ['value' => 'column']],
        [':input[name="megamenu[item_type]"]' => ['value' => 'tabs_block']],
        [':input[name="megamenu[item_type]"]' => ['value' => 'drupal_block']],
      ],
    ],
  ];
  $form['megamenu_tabs']['responsive_options']['hide_on_mobile'] = [
    '#type' => 'checkbox',
    '#title' => t('Hide below breakpoint'),
    '#description' => t('Use this option to hide the item below the responsive breakpoint via CSS.'),
    '#default_value' => $megamenu_menu_item->getOption('hide_on_mobile'),
    '#parents' => ['megamenu', 'hide_on_mobile'],
  ];
  $form['megamenu_tabs']['responsive_options']['hide_on_desktop'] = [
    '#type' => 'checkbox',
    '#title' => t('Hide above breakpoint'),
    '#description' => t('Use this option to hide the item above the responsive breakpoint via CSS.'),
    '#default_value' => $megamenu_menu_item->getOption('hide_on_desktop'),
    '#parents' => ['megamenu', 'hide_on_desktop'],
  ];
  $form['megamenu_tabs']['responsive_options']['disable_on_mobile'] = [
    '#type' => 'checkbox',
    '#title' => t('Disable on mobile'),
    '#description' => t('Use this option to disable the item when mobile device is detected via built-in megamenu_is_mobile() function.'),
    '#default_value' => $megamenu_menu_item->getOption('disable_on_mobile'),
    '#parents' => ['megamenu', 'disable_on_mobile'],
  ];
  $form['megamenu_tabs']['responsive_options']['disable_on_desktop'] = [
    '#type' => 'checkbox',
    '#title' => t('Disable on desktop'),
    '#description' => t('Use this option to disable the item when mobile device is NOT detected via built-in megamenu_is_mobile() function.'),
    '#default_value' => $megamenu_menu_item->getOption('disable_on_desktop'),
    '#parents' => ['megamenu', 'disable_on_desktop'],
  ];
  $form['megamenu_tabs']['responsive_options']['disable_submenu_on_mobile'] = [
    '#type' => 'checkbox',
    '#title' => t('Disable submenu on mobile'),
    '#description' => t('Use this option to disable the item\'s submenu when mobile device is detected via built-in megamenu_is_mobile() function.'),
    '#default_value' => $megamenu_menu_item->getOption('disable_submenu_on_mobile'),
    '#parents' => ['megamenu', 'disable_submenu_on_mobile'],
  ];
  return $form;
}

/**
 * Submenu
 */
function _megamenu_menu_link_form_options__submenu(array $form, FormStateInterface $form_state, $megamenu_menu_item) {
  $form['megamenu_tabs']['submenu_options'] = [
    '#type' => 'details',
    '#title' => t('Submenu'),
    '#group' => 'megamenu_tabs',
    '#states' => [
      'visible' => [
        [':input[name="megamenu[item_type]"]' => ['value' => 'normal']],
      ],
    ],
  ];
  $form['megamenu_tabs']['submenu_options']['submenu_type'] = [
    '#type' => 'radios',
    '#title' => t('Submenu type'),
    '#description' => t('Choose the submenu type.'),
    '#options' => [
      'auto' => t('Automatic (MegaMenu will attempt to automatically determine the best type of submenu for this item)'),
      'mega' => t('Mega Submenu (a mega submenu)'),
      'flyout' => t('Flyout Submenu (a standard Flyout submenu - not intended for use inside Mega submenus)'),
      'stack' => t('Stack (a stacked vertical submenu that is always visible)'),
    ],
    '#default_value' => $megamenu_menu_item->getOption('submenu_type'),
    '#parents' => ['megamenu', 'submenu_type'],
  ];
  $form['megamenu_tabs']['submenu_options']['submenu_position'] = [
    '#type' => 'radios',
    '#title' => t('Mega submenu position'),
    '#description' => t('Choose how the submenu should be positioned relative to this item.'),
    '#options' => [
      'full_width' => t('Full Width - the submenu will be the full width of the menu bar (or wider, if not bound by the menu bar)'),
      'center' => t('Center - align mega submenu centered below the parent menu item'),
      'left_edge_bar' => t('Left Edge of Menu Bar - align submenu to the left edge of the menu bar (or to the next relative wrapper if the submenus are not bound by the menu bar)'),
      'right_edge_bar' => t('Right Edge of Menu Bar - align submenu to the right edge of the menu bar (or to the next relative wrapper if the submenus are not bound by the menu bar)'),
      'left_edge_item' => t('Left Edge of Parent Item - align submenu to the left edge of the parent menu item'),
      'right_edge_item' => t('Right Edge of Parent Item - align submenu to the right edge of the parent menu item. Forces direction:rtl.'),
      'vertical_full_height' => t('Vertical - Full Height - the submenu will be the full height of the menu bar (at minimum), aligned to the top. (Vertically oriented menus only)'),
      'vertical_parent_item' => t('Vertical - Aligned to parent - the submenu will be a natural height and aligned to its parent item. (Vertically oriented menus only)'),
    ],
    '#default_value' => $megamenu_menu_item->getOption('submenu_position'),
    '#parents' => ['megamenu', 'submenu_position'],
  ];
  $form['megamenu_tabs']['submenu_options']['flyout_submenu_position'] = [
    '#type' => 'radios',
    '#title' => t('Flyout submenu position'),
    '#description' => t('Choose how the flyout submenu should be positioned relative to this item.'),
    '#options' => [
      'left_edge_item' => t('Fly Right - for the first submenu, align to the left edge of the parent menu item. For deeper submenus, fly to the right of the previous level.'),
      'right_edge_item' => t('Fly Left - For the first submenu, align to the right edge of the parent menu item. For deeper submenus, fly to the left of the previous level. Forces direction:rtl.'),
      'vertical_full_height' => t('Vertical - Full Height - the submenu will be the full height of the menu bar (at minimum), aligned to the top. (Vertically oriented menus only)'),
      'vertical_parent_item' => t('Vertical - Aligned to parent - the submenu will be a natural height and aligned to its parent item. (Vertically oriented menus only)'),
    ],
    '#default_value' => $megamenu_menu_item->getOption('flyout_submenu_position'),
    '#parents' => ['megamenu', 'flyout_submenu_position'],
  ];
  $form['megamenu_tabs']['submenu_options']['submenu_width'] = [
    '#type' => 'textfield',
    '#title' => t('Submenu width (experimental)'),
    '#description' => t('Use this option to set submenu with. Include the units together with value (e.g. px/em/%). Leave this option blank in order to size to contents. Note that some layouts will require an explicit width if you are not using the Full Width submenu layout.'),
    '#default_value' => $megamenu_menu_item->getOption('submenu_width'),
    '#parents' => ['megamenu', 'submenu_width'],
  ];
  $form['megamenu_tabs']['submenu_options']['submenu_min_width'] = [
    '#type' => 'textfield',
    '#title' => t('Submenu minimum width (experimental)'),
    '#description' => t('Use this option to set submenu minimum width. Include the units together with value (e.g. px/em/%). Mega submenu will be at least 50% width of the menu bar, by default. You can override here that behaviour.'),
    '#default_value' => $megamenu_menu_item->getOption('submenu_min_width'),
    '#parents' => ['megamenu', 'submenu_min_width'],
  ];
  $form['megamenu_tabs']['submenu_options']['submenu_min_height'] = [
    '#type' => 'textfield',
    '#title' => t('Submenu minimum height (experimental)'),
    '#description' => t('Use this option to set submenu minimum height. Submenus will be sized to their contents, by default. In majority of cases, this will not be needed. This setting is not compatible with the Slide Reveal submenu transition.'),
    '#default_value' => $megamenu_menu_item->getOption('submenu_min_height'),
    '#parents' => ['megamenu', 'submenu_min_height'],
  ];
  $form['megamenu_tabs']['submenu_options']['submenu_content_align'] = [
    '#type' => 'select',
    '#title' => t('Submenu content alignment'),
    '#description' => t('Use this option to explicitly align the submenu columns to the left, right, or center. If not sure, leave this set to default. This is intended to be used with the "Submenu Position: Full Width". If the submenu is not wider than the content, this option will not have much effect.'),
    '#options' => [
      'default' => t('Default'),
      'left' => t('Left'),
      'right' => t('Right'),
      'center' => t('Center'),
    ],
    '#default_value' => $megamenu_menu_item->getOption('submenu_content_align'),
    '#parents' => ['megamenu', 'submenu_content_align'],
  ];
  $form['megamenu_tabs']['submenu_options']['submenu_column_default__submenu'] = [
    '#type' => 'select',
    '#title' => t('Submenu column default'),
    '#description' => t('Choose the number of columns per row that the submenu should be broken into by default. This option can be overridden on individual items.'),
    '#options' => [
      'Basic' => [
        'auto' => t('Automatic'),
        'natural' => t('Natural'),
      ],
      'Halves' => [
        '1-2' => t('1/2'),
      ],
      'Thirds' => [
        '1-3' => t('1/3'),
        '2-3' => t('2/3'),
      ],
      'Quarters' => [
        '1-4' => t('1/4'),
        '3-4' => t('3/4'),
      ],
      'Fifths' => [
        '1-5' => t('1/5'),
        '2-5' => t('2/5'),
        '3-5' => t('3/5'),
        '4-5' => t('4/5'),
      ],
      'Sixths' => [
        '1-6' => t('1/6'),
        '5-6' => t('1/6'),
      ],
      'Sevenths' => [
        '1-7' => t('1/7'),
        '2-7' => t('2/7'),
        '3-7' => t('3/7'),
        '4-7' => t('4/7'),
        '5-7' => t('5/7'),
        '6-7' => t('6/7'),
      ],
      'Eighths' => [
        '1-8' => t('1/8'),
        '3-8' => t('3/8'),
        '5-8' => t('5/8'),
        '7-8' => t('7/8'),
      ],
      'Ninths' => [
        '1-9' => t('1/9'),
        '2-9' => t('2/9'),
        '4-9' => t('4/9'),
        '5-9' => t('5/9'),
        '7-9' => t('7/9'),
        '8-9' => t('8/9'),
      ],
      'Tenths' => [
        '1-10' => t('1/10'),
        '3-10' => t('3/10'),
        '7-10' => t('7/10'),
        '9-10' => t('9/10'),
      ],
      'Elevenths' => [
        '1-11' => t('1/11'),
        '2-11' => t('2/11'),
        '3-11' => t('3/11'),
        '4-11' => t('4/11'),
        '5-11' => t('5/11'),
        '6-11' => t('6/11'),
        '7-11' => t('7/11'),
        '8-11' => t('8/11'),
        '9-11' => t('9/11'),
        '10-11' => t('10/11'),
      ],
      'Twelfths' => [
        '1-12' => t('1/12'),
        '5-12' => t('5/12'),
        '7-12' => t('7/12'),
        '11-12' => t('11/12'),
      ],
    ],
    '#default_value' => $megamenu_menu_item->getOption('submenu_column_default'),
    '#parents' => ['megamenu', 'submenu_column_default__submenu'],
  ];
  $form['megamenu_tabs']['submenu_options']['submenu_column_autoclear__submenu'] = [
    '#type' => 'checkbox',
    '#title' => t('Auto row'),
    '#description' => t('Use this option to automatically start a new row every X items. For example, if you choose a Submenu column default of 1/4, the 5th item will start a new column automatically. Disable this option if you are adjusting item columns manually.'),
    '#default_value' => $megamenu_menu_item->getOption('submenu_column_autoclear'),
    '#parents' => ['megamenu', 'submenu_column_autoclear__submenu'],
  ];
  // @todo Next version - Create this option as color picker field.
  $form['megamenu_tabs']['submenu_options']['submenu_column_dividers'] = [
    '#type' => 'textfield',
    '#title' => t('Submenu columns dividers'),
    '#description' => t('Use this option to set the color of the border between each submenu column. This only affects columns that are children of this item, not further descendants.'),
    '#default_value' => $megamenu_menu_item->getOption('submenu_column_dividers'),
    '#parents' => ['megamenu', 'submenu_column_dividers'],
  ];
  $form['megamenu_tabs']['submenu_options']['submenu_column_min_height'] = [
    '#type' => 'textfield',
    '#title' => t('Submenu columns minimum height (experimental)'),
    '#description' => t('Use this option to set minimum height of submenu columns. This option is useful when using the Submenu Columns Dividers setting.'),
    '#default_value' => $megamenu_menu_item->getOption('submenu_column_min_height'),
    '#parents' => ['megamenu', 'submenu_column_min_height'],
  ];
  $form['megamenu_tabs']['submenu_options']['submenu_grid'] = [
    '#type' => 'checkbox',
    '#title' => t('Grid submenu'),
    '#description' => t('Use this option to make the space between the targets equal to the space on the submenu edges. The option is useful for image grids.'),
    '#default_value' => $megamenu_menu_item->getOption('submenu_grid'),
    '#parents' => ['megamenu', 'submenu_grid'],
  ];
  $form['megamenu_tabs']['submenu_options']['submenu_padded'] = [
    '#type' => 'checkbox',
    '#title' => t('Pad submenu'),
    '#description' => t('Use this option to add padding to submenus (doubles the edge gutters). The option is useful if you need to make the spacing at the edges of a row equal to that between the columns.'),
    '#default_value' => $megamenu_menu_item->getOption('submenu_padded'),
    '#parents' => ['megamenu', 'submenu_padded'],
  ];
  $form['megamenu_tabs']['submenu_options']['submenu_indent'] = [
    '#type' => 'checkbox',
    '#title' => t('Indent submenu'),
    '#description' => t('Use this option to indent submenu. The option is useful for vertically stacked content to create a visual hierarchy.'),
    '#default_value' => $megamenu_menu_item->getOption('submenu_indent'),
    '#parents' => ['megamenu', 'submenu_indent'],
  ];
  $form['megamenu_tabs']['submenu_options']['show_current'] = [
    '#type' => 'checkbox',
    '#title' => t('Show submenu when current'),
    '#description' => t('Use this option to show this submenu on page load by default when its parent is current. This option is relevant only for submenu types that are hidden by default.'),
    '#default_value' => $megamenu_menu_item->getOption('show_current'),
    '#parents' => ['megamenu', 'show_current'],
  ];
  $form['megamenu_tabs']['submenu_options']['show_default'] = [
    '#type' => 'checkbox',
    '#title' => t('Show submenu by default'),
    '#description' => t('Use this option to show this submenu on page load by default on every page. This option is relevant only for submenu types that are hidden by default.'),
    '#default_value' => $megamenu_menu_item->getOption('show_default'),
    '#parents' => ['megamenu', 'show_default'],
  ];
  // Submenu background image options are visible only to menu_link_content items
  // because of D8 bug with special items.
  // @see https://www.drupal.org/node/2898335
  $menu_link = $megamenu_menu_item->getMenuLink();
  if (isset($menu_link['original_link'])) {
    $original_link = $menu_link['original_link'];
    if ($original_link instanceof \Drupal\menu_link_content\Plugin\Menu\MenuLinkContent) {
      $form['megamenu_tabs']['submenu_options']['submenu_background_image'] = [
        '#type' => 'managed_file',
        '#title' => t('Submenu background image (experimental)'),
        '#description' => t('Allowed extensions: gif png jpg jpeg.'),
        '#upload_location' => 'public://megamenu/menu_items',
        '#upload_validators' => [
          'file_validate_extensions' => ['gif png jpg jpeg'],
          // Pass the maximum file size in bytes
          //'file_validate_size' => [10 * 3024 * 3024],
        ],
        '#default_value' => $megamenu_menu_item->getOption('submenu_background_image'),
        '#parents' => ['megamenu', 'submenu_background_image'],
      ];
      $form['megamenu_tabs']['submenu_options']['submenu_background_image_repeat'] = [
        '#type' => 'select',
        '#title' => t('Repeat background image (experimental)'),
        '#description' => t('Select how background image will be repeated.'),
        '#options' => [
          'no-repeat' => t('No Repeat'),
          'repeat' => t('Repeat'),
          'repeat-x' => t('Repeat X (Horizontal)'),
          'repeat-y' => t('Repeat Y (Vertical)'),
          'space' => t('Space'),
          'round' => t('Round'),
        ],
        '#default_value' => $megamenu_menu_item->getOption('submenu_background_image_repeat'),
        '#parents' => ['megamenu', 'submenu_background_image_repeat'],
      ];
      $form['megamenu_tabs']['submenu_options']['submenu_background_position'] = [
        '#type' => 'textfield',
        '#title' => t('Background position (experimental)'),
        '#description' => t('Use this option to set background image position (e.g. bottom right).'),
        '#default_value' => $megamenu_menu_item->getOption('submenu_background_position'),
        '#parents' => ['megamenu', 'submenu_background_position'],
      ];
      $form['megamenu_tabs']['submenu_options']['submenu_background_size'] = [
        '#type' => 'textfield',
        '#title' => t('Background size (experimental)'),
        '#description' => t('Use this option to set background image position (e.g. cover, to cover the entire menu).'),
        '#default_value' => $megamenu_menu_item->getOption('submenu_background_size'),
        '#parents' => ['megamenu', 'submenu_background_size'],
      ];
    }
  }
  $form['megamenu_tabs']['submenu_options']['submenu_padding'] = [
    '#type' => 'textfield',
    '#title' => t('Submenu padding (experimental)'),
    '#description' => t('Enter here value for submenu\'s padding property (e.g. 0 0 10px 0 for adding  10px of padding to the bottom of the submenu).'),
    '#default_value' => $megamenu_menu_item->getOption('submenu_padding'),
    '#parents' => ['megamenu', 'submenu_padding'],
  ];
  $form['megamenu_tabs']['submenu_options']['submenu_footer_content'] = [
    '#type' => 'text_format',
    '#title' => t('Submenu footer content'),
    '#rows' => 3,
    '#format' => $megamenu_menu_item->getOption('submenu_footer_content_format'),
    '#default_value' => $megamenu_menu_item->getOption('submenu_footer_content'),
    '#parents' => ['megamenu', 'submenu_footer_content'],
  ];
  return $form;
}

/**
 * Image.
 *
 * Only menu_item_content supports images uploading because of bug "Uploading file
 * using managed_file field added to module-defined menu link's edit form causes
 * ajax and database connection serialization errors"
 *
 * @see bug https://www.drupal.org/node/2898335
 */
function _megamenu_menu_link_form_options__image(array $form, FormStateInterface $form_state, $megamenu_menu_item) {
  $menu_link = $megamenu_menu_item->getMenuLink();
  if (!isset($menu_link['original_link'])) {
    return $form;
  }

  $original_link = $menu_link['original_link'];
  if (!$original_link instanceof \Drupal\menu_link_content\Plugin\Menu\MenuLinkContent) {
    return $form;
  }

  $form['megamenu_tabs']['image_options'] = [
    '#type' => 'details',
    '#title' => t('Image'),
    '#group' => 'megamenu_tabs',
    '#states' => [
      'visible' => [
        [':input[name="megamenu[item_type]"]' => ['value' => 'normal']],
      ],
    ],
  ];
  $form['megamenu_tabs']['image_options']['image_title'] = [
    '#type' => 'textfield',
    '#title' => t('Image title'),
    '#description' => t('Enter here title of the image.'),
    '#default_value' => $megamenu_menu_item->getOption('image_title'),
    '#parents' => ['megamenu', 'image_title'],
  ];
  $form['megamenu_tabs']['image_options']['image_alt'] = [
    '#type' => 'textfield',
    '#title' => t('Image alt'),
    '#description' => t('Enter text that will be used for image\'s alt tag.'),
    '#default_value' => $megamenu_menu_item->getOption('image_alt'),
    '#parents' => ['megamenu', 'image_alt'],
  ];
  $form['megamenu_tabs']['image_options']['item_image'] = [
    '#type' => 'managed_file',
    '#title' => t('Image'),
    '#description' => t('Allowed extensions: gif png jpg jpeg.'),
    '#upload_location' => 'public://megamenu/menu_items',
    '#upload_validators' => [
      'file_validate_extensions' => ['gif png jpg jpeg'],
      // Pass the maximum file size in bytes
      //'file_validate_size' => [10 * 3024 * 3024],
    ],
    '#default_value' => $megamenu_menu_item->getOption('item_image'),
    '#parents' => ['megamenu', 'item_image'],
  ];
  $image_size_options = [
    'inherit' => t('Inherit (inherit settings from the menu Configuration settings)'),
    'full' => t('Full (display image at natural dimensions)'),
  ];
  $image_styles = megamenu_get_image_styles_list();
  foreach ($image_styles as $key => $value) {
    $image_size_options[$key] = $value;
  }
  $form['megamenu_tabs']['image_options']['image_size'] = [
    '#type' => 'radios',
    '#title' => t('Image size'),
    '#description' => t('Represents the size of the actual file that will be served. You can set a default to be inherited in the menu settings form.'),
    '#options' => $image_size_options,
    '#default_value' => $megamenu_menu_item->getOption('image_size'),
    '#parents' => ['megamenu', 'image_size'],
  ];
  $form['megamenu_tabs']['image_options']['image_dimensions'] = [
    '#type' => 'radios',
    '#title' => t('Image dimensions (experimental)'),
    '#description' => t('Choose image dimensions.'),
    '#options' => [
      'inherit' => t('Inherit (inherit settings from the menu instance settings)'),
      'natural' => t('Natural (display image at natural dimensions)'),
      'custom' => t('Custom (use a custom size, defined below)'),
    ],
    '#default_value' => $megamenu_menu_item->getOption('image_dimensions'),
    '#parents' => ['megamenu', 'image_dimensions'],
  ];
  $form['megamenu_tabs']['image_options']['image_width_custom'] = [
    '#type' => 'textfield',
    '#title' => t('Custom image width'),
    '#description' => t('Represents the image width attribute in pixels (do not include units). It is valid only if "Image Dimensions" is set to "Custom" above.'),
    '#default_value' => $megamenu_menu_item->getOption('image_width_custom'),
    '#parents' => ['megamenu', 'image_width_custom'],
  ];
  $form['megamenu_tabs']['image_options']['image_height_custom'] = [
    '#type' => 'textfield',
    '#title' => t('Custom image height'),
    '#description' => t('Represents the image height attribute in pixels (do not include units). It is valid only if "Image Dimensions" is set to "Custom" above. Leave blank to maintain aspect ratio.'),
    '#default_value' => $megamenu_menu_item->getOption('image_height_custom'),
    '#parents' => ['megamenu', 'image_height_custom'],
  ];
  $form['megamenu_tabs']['image_options']['image_text_top_padding'] = [
    '#type' => 'textfield',
    '#title' => t('Image text top padding'),
    '#description' => t('Represents the top padding for the accompanying text when Image Left or Image Right layouts are displayed. This allows control over the vertical alignment of the text relative to the image.'),
    '#default_value' => $megamenu_menu_item->getOption('image_text_top_padding'),
    '#parents' => ['megamenu', 'image_text_top_padding'],
  ];
  $form['megamenu_tabs']['image_options']['disable_padding'] = [
    '#type' => 'checkbox',
    '#title' => t('Disable item padding'),
    '#description' => t('Use this option to disable the padding on this item. The option is useful for image-only menu items where the image should extend to the extents of the item.'),
    '#default_value' => $megamenu_menu_item->getOption('disable_padding'),
    '#parents' => ['megamenu', 'disable_padding'],
  ];
  return $form;
}

/**
 * Icon
 */
function _megamenu_menu_link_form_options__icon(array $form, FormStateInterface $form_state, $megamenu_menu_item) {
  $form['megamenu_tabs']['icon_options'] = [
    '#type' => 'details',
    '#title' => t('Icon'),
    '#group' => 'megamenu_tabs',
    '#states' => [
      'visible' => [
        [':input[name="megamenu[item_type]"]' => ['value' => 'normal']],
      ],
    ],
  ];
  $form['megamenu_tabs']['icon_options']['icon_custom_class'] = [
    '#type' => 'textfield',
    '#title' => t('Icon custom class'),
    '#description' => t('Use this option to set FontAwesome icon class (e.g. fa-image). Check all available options at <a target="_blank" href="http://fontawesome.io/">Font Awesome icons</a>.'),
    '#default_value' => $megamenu_menu_item->getOption('icon_custom_class'),
    '#parents' => ['megamenu', 'icon_custom_class'],
  ];
  return $form;
}

/**
 * Handle single value for options with the same names in different sections.
 */
function _megamenu_menu_link_form_handle_options(array $megamenu_options) {
  // Set defaults.
  $megamenu_options['megamenu']['columns'] = 'auto';
  $megamenu_options['megamenu']['clear_row'] = 0;
  $megamenu_options['megamenu']['submenu_column_default'] = 'auto';
  $megamenu_options['megamenu']['submenu_column_autoclear'] = 1;

  // Handle common options by item type.
  if ($megamenu_options['megamenu']['item_type'] == 'normal') {
    $megamenu_options['megamenu']['columns'] = $megamenu_options['megamenu']['columns__layout'];
    $megamenu_options['megamenu']['clear_row'] = $megamenu_options['megamenu']['clear_row__layout'];
    $megamenu_options['megamenu']['submenu_column_default'] = $megamenu_options['megamenu']['submenu_column_default__submenu'];
    $megamenu_options['megamenu']['submenu_column_autoclear'] = $megamenu_options['megamenu']['submenu_column_autoclear__submenu'];
  }
  elseif ($megamenu_options['megamenu']['item_type'] == 'row') {
    $megamenu_options['megamenu']['submenu_column_default'] = $megamenu_options['megamenu']['submenu_column_default__row'];
    $megamenu_options['megamenu']['submenu_column_autoclear'] = $megamenu_options['megamenu']['submenu_column_autoclear__row'];
  }
  elseif ($megamenu_options['megamenu']['item_type'] == 'column') {
    $megamenu_options['megamenu']['columns'] = $megamenu_options['megamenu']['columns__column_layout'];
    $megamenu_options['megamenu']['clear_row'] = $megamenu_options['megamenu']['clear_row__column_layout'];
    $megamenu_options['megamenu']['submenu_column_default'] = $megamenu_options['megamenu']['submenu_column_default__column_layout'];
    $megamenu_options['megamenu']['submenu_column_autoclear'] = $megamenu_options['megamenu']['submenu_column_autoclear__column_layout'];
  }
  elseif ($megamenu_options['megamenu']['item_type'] == 'divider') {
  }
  elseif ($megamenu_options['megamenu']['item_type'] == 'custom_content') {
    $megamenu_options['megamenu']['columns'] = $megamenu_options['megamenu']['columns__layout'];
    $megamenu_options['megamenu']['clear_row'] = $megamenu_options['megamenu']['clear_row__layout'];
  }
  elseif ($megamenu_options['megamenu']['item_type'] == 'tabs_block') {
    $megamenu_options['megamenu']['submenu_column_default'] = $megamenu_options['megamenu']['submenu_column_default__tabs_block'];
  }
  elseif ($megamenu_options['megamenu']['item_type'] == 'drupal_block') {
    $megamenu_options['megamenu']['columns'] = $megamenu_options['megamenu']['columns__layout'];
    $megamenu_options['megamenu']['clear_row'] = $megamenu_options['megamenu']['clear_row__layout'];
  }

  // Remove temporary common options.
  unset($megamenu_options['megamenu']['columns__layout']);
  unset($megamenu_options['megamenu']['columns__column_layout']);
  unset($megamenu_options['megamenu']['clear_row__layout']);
  unset($megamenu_options['megamenu']['clear_row__column_layout']);
  unset($megamenu_options['megamenu']['submenu_column_default__column_layout']);
  unset($megamenu_options['megamenu']['submenu_column_default__submenu']);
  unset($megamenu_options['megamenu']['submenu_column_default__row']);
  unset($megamenu_options['megamenu']['submenu_column_default__tabs_block']);
  unset($megamenu_options['megamenu']['submenu_column_autoclear__column_layout']);
  unset($megamenu_options['megamenu']['submenu_column_autoclear__submenu']);
  unset($megamenu_options['megamenu']['submenu_column_autoclear__row']);

  // Handle text_format fields.
  if (isset($megamenu_options['megamenu']['custom_content']['format'])) {
    $megamenu_options['megamenu']['custom_content_format'] = $megamenu_options['megamenu']['custom_content']['format'];
  }
  if (isset($megamenu_options['megamenu']['custom_content']['value'])) {
    $megamenu_options['megamenu']['custom_content'] = $megamenu_options['megamenu']['custom_content']['value'];
  }
  if (isset($megamenu_options['megamenu']['submenu_footer_content']['format'])) {
    $megamenu_options['megamenu']['submenu_footer_content_format'] = $megamenu_options['megamenu']['submenu_footer_content']['format'];
  }
  if (isset($megamenu_options['megamenu']['submenu_footer_content']['value'])) {
    $megamenu_options['megamenu']['submenu_footer_content'] = $megamenu_options['megamenu']['submenu_footer_content']['value'];
  }

  return $megamenu_options;
}

//---------------------------------------------------------------------------
// CUSTOM STYLES
//---------------------------------------------------------------------------

/**
 * Set item styles.
 *
 * @param $menu_id
 * @param $menu_item_id
 * @param $megamenu_options
 */
function _megamenu_set_item_styles($menu_id, $menu_item_id, $megamenu_options) {
  // Row padding.
  _megamenu_set_item_styles__row_padding($menu_id, $menu_item_id, $megamenu_options);
  // Panels padding.
  _megamenu_set_item_styles__panels_padding($menu_id, $menu_item_id, $megamenu_options);
  // Submenu width.
  _megamenu_set_item_styles__submenu_width($menu_id, $menu_item_id, $megamenu_options);
  // Submenu min width.
  _megamenu_set_item_styles__submenu_min_width($menu_id, $menu_item_id, $megamenu_options);
  // Submenu min height.
  _megamenu_set_item_styles__submenu_min_height($menu_id, $menu_item_id, $megamenu_options);
  // Submenu column min height.
  _megamenu_set_item_styles__submenu_column_min_height($menu_id, $menu_item_id, $megamenu_options);
  // Submenu background image.
  _megamenu_set_item_styles__submenu_background_image($menu_id, $menu_item_id, $megamenu_options);
  // Submenu padding.
  _megamenu_set_item_styles__submenu_padding($menu_id, $menu_item_id, $megamenu_options);
  // Image dimensions.
  _megamenu_set_item_styles__image_dimensions($menu_id, $menu_item_id, $megamenu_options);
}

/**
 * Set row padding.
 *
 * @param $menu_id
 * @param $menu_item_id
 * @param $megamenu_options
 */
function _megamenu_set_item_styles__row_padding($menu_id, $menu_item_id, $megamenu_options) {
  $menu_id = Html::cleanCssIdentifier($menu_id, megamenu_get_clean_css_filter());
  $menu_item_id = Html::cleanCssIdentifier($menu_item_id, megamenu_get_clean_css_filter());
  $selector = ".megamenu .megamenu-row--$menu_item_id";
  $property_map = [
    'padding' => ''
  ];

  // Option is not empty, create styles.
  if (!empty($megamenu_options['megamenu']['row_padding'])) {
    $row_padding = $megamenu_options['megamenu']['row_padding'];

    $property_map = [
      'padding' => $row_padding
    ];
  }

  \Drupal::service('megamenu')->setMenuItemStyles($menu_id, $menu_item_id, $selector, $property_map);
}

/**
 * Set panels padding.
 *
 * @param $menu_id
 * @param $menu_item_id
 * @param $megamenu_options
 */
function _megamenu_set_item_styles__panels_padding($menu_id, $menu_item_id, $megamenu_options) {
  $menu_id = Html::cleanCssIdentifier($menu_id, megamenu_get_clean_css_filter());
  $menu_item_id = Html::cleanCssIdentifier($menu_item_id, megamenu_get_clean_css_filter());
  $selector = ".megamenu .megamenu-tabs.megamenu-item--$menu_item_id > .megamenu-tabs__group > .megamenu-tabs__tab > .megamenu-tabs__pane";
  $property_map = [
    'padding' => ''
  ];

  // Option is not empty, create styles.
  if (!empty($megamenu_options['megamenu']['panels_padding'])) {
    $panels_padding = $megamenu_options['megamenu']['panels_padding'];

    $property_map = [
      'padding' => $panels_padding
    ];
  }

  \Drupal::service('megamenu')->setMenuItemStyles($menu_id, $menu_item_id, $selector, $property_map);
}

/**
 * Set submenu width.
 *
 * @param $menu_id
 * @param $menu_item_id
 * @param $megamenu_options
 */
function _megamenu_set_item_styles__submenu_width($menu_id, $menu_item_id, $megamenu_options) {
  $menu_id = Html::cleanCssIdentifier($menu_id, megamenu_get_clean_css_filter());
  $menu_item_id = Html::cleanCssIdentifier($menu_item_id, megamenu_get_clean_css_filter());
  $selector = ".megamenu .megamenu-submenu.megamenu-submenu--$menu_item_id";
  $property_map = [
    'width' => '',
    'min-width' => ''
  ];

  // Option is not empty, create styles.
  if (!empty($megamenu_options['megamenu']['submenu_width'])) {
    $submenu_width = $megamenu_options['megamenu']['submenu_width'];

    // If no units provided, assume pixels.
    if (is_numeric($submenu_width)) {
      $submenu_width .= 'px';
    }

    $property_map = [
      'width' => $submenu_width,
      'min-width' => $submenu_width,
    ];
  }

  \Drupal::service('megamenu')->setMenuItemStyles($menu_id, $menu_item_id, $selector, $property_map);
}

/**
 * Set submenu min width.
 *
 * @param $menu_id
 * @param $menu_item_id
 * @param $megamenu_options
 */
function _megamenu_set_item_styles__submenu_min_width($menu_id, $menu_item_id, $megamenu_options) {
  $menu_id = Html::cleanCssIdentifier($menu_id, megamenu_get_clean_css_filter());
  $menu_item_id = Html::cleanCssIdentifier($menu_item_id, megamenu_get_clean_css_filter());
  $selector = ".megamenu .megamenu-submenu.megamenu-submenu--$menu_item_id";
  $property_map = [
    'min-width' => ''
  ];

  // Option is not empty, create styles.
  if (!empty($megamenu_options['megamenu']['submenu_min_width'])) {
    $submenu_min_width = $megamenu_options['megamenu']['submenu_min_width'];

    // If no units provided, assume pixels.
    if (is_numeric($submenu_min_width)) {
      $submenu_min_width .= 'px';
    }

    $property_map = [
      'min-width' => $submenu_min_width,
    ];
  }

  \Drupal::service('megamenu')->setMenuItemStyles($menu_id, $menu_item_id, $selector, $property_map);
}

/**
 * Set submenu min height.
 *
 * @param $menu_id
 * @param $menu_item_id
 * @param $megamenu_options
 */
function _megamenu_set_item_styles__submenu_min_height($menu_id, $menu_item_id, $megamenu_options) {
  $menu_id = Html::cleanCssIdentifier($menu_id, megamenu_get_clean_css_filter());
  $menu_item_id = Html::cleanCssIdentifier($menu_item_id, megamenu_get_clean_css_filter());
  $selector = ".megamenu .megamenu-submenu.megamenu-submenu--$menu_item_id";
  $property_map = [
    'min-height' => ''
  ];

  // Option is not empty, create styles.
  if (!empty($megamenu_options['megamenu']['submenu_min_height'])) {
    $submenu_min_height = $megamenu_options['megamenu']['submenu_min_height'];

    // If no units provided, assume pixels.
    if (is_numeric($submenu_min_height)) {
      $submenu_min_height .= 'px';
    }

    $property_map = [
      'min-height' => $submenu_min_height,
    ];
  }

  \Drupal::service('megamenu')->setMenuItemStyles($menu_id, $menu_item_id, $selector, $property_map);
}

/**
 * Set submenu column min height.
 *
 * @param $menu_id
 * @param $menu_item_id
 * @param $megamenu_options
 */
function _megamenu_set_item_styles__submenu_column_min_height($menu_id, $menu_item_id, $megamenu_options) {
  $menu_id = Html::cleanCssIdentifier($menu_id, megamenu_get_clean_css_filter());
  $menu_item_id = Html::cleanCssIdentifier($menu_item_id, megamenu_get_clean_css_filter());
  $selector = ".megamenu .megamenu-submenu--$menu_item_id > .megamenu-column";
  $property_map = [
    'min-height' => ''
  ];

  // Option is not empty, create styles.
  if (!empty($megamenu_options['megamenu']['submenu_column_min_height'])) {
    $submenu_column_min_height = $megamenu_options['megamenu']['submenu_column_min_height'];

    // If no units provided, assume pixels.
    if (is_numeric($submenu_column_min_height)) {
      $submenu_column_min_height .= 'px';
    }

    $property_map = [
      'min-height' => $submenu_column_min_height,
    ];
  }

  \Drupal::service('megamenu')->setMenuItemStyles($menu_id, $menu_item_id, $selector, $property_map);
}

/**
 * Set submenu background image.
 *
 * @param $menu_id
 * @param $menu_item_id
 * @param $megamenu_options
 */
function _megamenu_set_item_styles__submenu_background_image($menu_id, $menu_item_id, $megamenu_options) {
  $menu_id = Html::cleanCssIdentifier($menu_id, megamenu_get_clean_css_filter());
  $menu_item_id = Html::cleanCssIdentifier($menu_item_id, megamenu_get_clean_css_filter());
  $selector = '.megamenu .megamenu-submenu.megamenu-submenu--' . $menu_item_id;
  $property_map = [
    'background-image' => '',
    'background-repeat'	=> '',
    'background-position' => '',
    'background-size'	=> '',
  ];

  // Option is not empty, create styles.
  if (!empty($megamenu_options['megamenu']['submenu_background_image'])) {
    $background_image_id = $megamenu_options['megamenu']['submenu_background_image'];
    if ($background_image_id) {
      if (is_array($background_image_id)) {
        $background_image_id = $background_image_id[0];
      }

      $file = \Drupal\file\Entity\File::load($background_image_id);
      if ($file) {
        $background_image = 'url(' . file_create_url($file->getFileUri()) . ')';
        $background_repeat = !empty($megamenu_options['megamenu']['submenu_background_image_repeat']) ? $megamenu_options['megamenu']['submenu_background_image_repeat'] : '';
        $background_position = !empty($megamenu_options['megamenu']['submenu_background_position']) ? $megamenu_options['megamenu']['submenu_background_position'] : '';
        $background_size = !empty($megamenu_options['megamenu']['submenu_background_size']) ? $megamenu_options['megamenu']['submenu_background_size'] : '';

        $property_map = [
          'background-image' => $background_image,
          'background-repeat' => $background_repeat,
          'background-position' => $background_position,
          'background-size' => $background_size,
        ];
      }
    }
  }

  \Drupal::service('megamenu')->setMenuItemStyles($menu_id, $menu_item_id, $selector, $property_map);
}

/**
 * Set submenu padding.
 *
 * @param $menu_id
 * @param $menu_item_id
 * @param $megamenu_options
 */
function _megamenu_set_item_styles__submenu_padding($menu_id, $menu_item_id, $megamenu_options) {
  $menu_id = Html::cleanCssIdentifier($menu_id, megamenu_get_clean_css_filter());
  $menu_item_id = Html::cleanCssIdentifier($menu_item_id, megamenu_get_clean_css_filter());
  $selector = '.megamenu .megamenu-active > .megamenu-submenu.megamenu-submenu--' . $menu_item_id;
  $property_map = [
    'padding' => ''
  ];

  // Option is not empty, create styles.
  if (!empty($megamenu_options['megamenu']['submenu_padding'])) {
    $padding = $megamenu_options['megamenu']['submenu_padding'];

    $property_map = [
      'padding' => $padding
    ];
  }

  \Drupal::service('megamenu')->setMenuItemStyles($menu_id, $menu_item_id, $selector, $property_map);
}

/**
 * Set image dimensions.
 *
 * @param $menu_id
 * @param $menu_item_id
 * @param $megamenu_options
 */
function _megamenu_set_item_styles__image_dimensions($menu_id, $menu_item_id, $megamenu_options) {
  $menu_id = Html::cleanCssIdentifier($menu_id, megamenu_get_clean_css_filter());
  $menu_item_id = Html::cleanCssIdentifier($menu_item_id, megamenu_get_clean_css_filter());
  $selector = '';
  $property_map = [
    'padding-left' => '',
    'padding-right' => ''
  ];

  // Option is not empty, create styles.
  if (!empty($megamenu_options['megamenu']['image_dimensions'])) {
    // Image is not empty.
    if (!empty($megamenu_options['megamenu']['item_image'])) {
      $image_id = $megamenu_options['megamenu']['item_image'];
      if ($image_id) {
        if (is_array($image_id)) {
          $image_id = $image_id[0];
        }

        $file = \Drupal\file\Entity\File::load($image_id);
        if ($file) {
          // The image.factory service can check if image is valid ($image->isValid()).
          $image = \Drupal::service('image.factory')->get($file->getFileUri());

          $image_dimensions = $megamenu_options['megamenu']['image_dimensions'];
          if (in_array($image_dimensions, ['natural', 'custom'])) {
            $layout = $megamenu_options['megamenu']['item_layout'];
            if ($layout == 'default') {
              $layout = 'image_left';
            }

            if (in_array($layout, ['image_left','image_right'])) {
              $selector = ".megamenu .megamenu-item--$menu_item_id > .megamenu-anchor.megamenu-anchor--layout-$layout > ";

              $image_width = '';

              // Natural dimensions.
              if ($image_dimensions == 'natural') {
                $image_width = $image->getWidth();
              }
              // Custom dimensions.
              elseif ($image_dimensions == 'custom') {
                $image_width = intval($megamenu_options['megamenu']['image_width_custom']);
              }

              if ($image_width) {
                $padding = $image_width + 10;
              }
              else {
                $padding = 0;
              }

              $property = '';
              switch ($layout) {
                // Add padding to the left of the text.
                case 'image_left':
                  $property = 'padding-left';
                  $selector .= '.megamenu-anchor__text';
                  break;
                // Add padding to the right of the text.
                case 'image_right':
                  $property = 'padding-right';
                  $selector .= '.megamenu-anchor__text';
                  break;
              }

              $image_size = $megamenu_options['megamenu']['image_size'];
              if ($image_size == 'inherit' || $padding == 0) {
                $property_map = [
                  'padding-left' => '',
                  'padding-right' => ''
                ];
              }
              else {
                $property_map = [
                  $property => $padding . 'px',
                ];
              }
            }
          }
        }
      }
    }
  }

  \Drupal::service('megamenu')->setMenuItemStyles($menu_id, $menu_item_id, $selector, $property_map);
}
