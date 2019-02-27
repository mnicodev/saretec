<?php

/**
 * @file
 * Util functions.
 */

use Drupal\Component\Utility\Html;

/**
 * Check if font awesome library exists.
 */
function megamenu_fontawesome_library_exists() {
  $doesExist = FALSE;

  $library = \Drupal::service('library.discovery')->getLibraryByName('megamenu', 'fontawesome');
  if ($library && is_array($library)) {
    if (!empty($library['css'] && !empty($library['css'][0] && !empty($library['css'][0]['data'])))) {
      $library_file = $library['css'][0]['data'];
      $doesExist = file_exists($library_file);
    }
  }

  return $doesExist;
}

/**
 * Build menu tree.
 *
 * @see https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Core%21Menu%21menu.api.php/group/menu/8.2.x
 */
function megamenu_build_menu_tree($menu_name) {
  if (!$menu_name) {
    return [];
  }

  $menu_tree = \Drupal::menuTree();
  // Build the typical default set of menu tree parameters.
  $parameters = $menu_tree->getCurrentRouteMenuTreeParameters($menu_name);
  // Load the tree based on this set of parameters.
  $tree = $menu_tree->load($menu_name, $parameters);
  // Transform the tree using the manipulators you want.
  $manipulators = [
    // Only show links that are accessible for the current user.
    ['callable' => 'menu.default_tree_manipulators:checkAccess'],
    // Use the default sorting of menu links.
    ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
  ];
  $tree = $menu_tree->transform($tree, $manipulators);
  // Finally, build a renderable array from the transformed tree.
  $menu = $menu_tree->build($tree);

  return $menu;
}

/**
 * Get menu item from menu tree.
 */
function megamenu_find_menu_item($menu_items, $id) {
  $result = null;

  foreach ($menu_items as $key => $menu_item) {
    if ($key == $id) {
      $result = $menu_item;
      return $result;
    }

    if (!empty($menu_item['below'])) {
      $result = megamenu_find_menu_item($menu_item['below'], $id);
      if ($result) {
        return $result;
      }
    }
  }

  return $result;
}

/**
 * Function creates option array that will be saved as variable and used in javascript.
 *
 * @param $megamenu_options
 */
function megamenu_set_js_options($megamenu_options = []) {
  $config = \Drupal::configFactory()->getEditable('megamenu.settings');

  if (!$config) {
    return;
  }

  foreach ($megamenu_options as $key => $value) {
    // All values need to be strings because of javascript logic
    $config->set($key, $value);
  }

  $config->save();
}

/**
 * Function creates option array that will be saved as variable and used in javascript.
 *
 * @param $menu_id
 * @param $op
 *  Operation on array member. Options: 'update', 'delete'
 * @param $megamenu_options
 */
function megamenu_set_js_instance_options($menu_id, $op = 'update', $megamenu_options = []) {
  if (!$menu_id) {
    return;
  }

  $menu_id = Html::cleanCssIdentifier($menu_id, megamenu_get_clean_css_filter());

  $menu_instances_config = \Drupal::config('megamenu.settings')->get('menu_instances_config');
  if (!$menu_instances_config) {
    $menu_instances_config = [];
  }

  if ($op == 'update') {
    $menu_instances_config[$menu_id] = $megamenu_options;
  }
  elseif ($op == 'delete') {
    if (isset($menu_instances_config[$menu_id])) {
      unset($menu_instances_config[$menu_id]);
    }
  }

  \Drupal::configFactory()->getEditable('megamenu.settings')->set('menu_instances_config', $menu_instances_config)->save();
}

/**
 * Test if the current browser runs on a mobile device (smart phone, tablet, etc.)
 *
 * @return bool
 */
function megamenu_is_mobile() {
  $is_mobile = false;

  if (!isset($_SERVER['HTTP_USER_AGENT'])) {
    $is_mobile = false;
  }
  else {
    // @see http://detectmobilebrowsers.com for updates
    $useragent = $_SERVER['HTTP_USER_AGENT'];
    if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))) {
      $is_mobile = true;
    }
  }

  return $is_mobile;
}

/**
 * Get item layouts
 *
 * @param int $layout_id
 *
 * @return array|bool
 */
function megamenu_get_item_layouts($layout_id = 0) {
  $layouts = [];

  $layouts['text_only'] = [
    'order'	=> [
      'title',
      'description',
    ],
  ];

  $layouts['image_left'] = [
    'order'	=> [
      'image',
      'title',
      'description',
    ],
  ];

  $layouts['image_above'] = [
    'order'	=> [
      'image',
      'title',
      'description',
    ],
  ];

  $layouts['image_right'] = [
    'order'	=> [
      'image',
      'title',
      'description',
    ],
  ];

  $layouts['image_below'] = [
    'order'	=> [
      'title',
      'description',
      'image',
    ],
  ];

  $layouts['image_only'] = [
    'order'	=> [
      'image',
    ],
  ];

  $layouts['icon_left'] = [
    'order'	=> [
      'icon',
      'title',
      'description',
    ],
  ];

  if ($layout_id) {
    if (isset( $layouts[$layout_id])) {
      return $layouts[$layout_id];
    }
    return false;
  }

  return $layouts;
}

/**
 * Get drupal blocks.
 *
 * @return array|bool
 */
function megamenu_get_drupal_blocks_list() {
  $blocks = [];

  $blocks[''] = t('None');

  // Only add blocks which work without any available context.
  $definitions = \Drupal::service('plugin.manager.block')->getDefinitions();
  // Order by category, and then by admin label.
  $definitions = \Drupal::service('plugin.manager.block')->getSortedDefinitions($definitions);
  foreach ($definitions as $key => $value) {
    $blocks[$key] = $value['admin_label'];
  }

  return $blocks;
}

/**
 * Get image styles.
 *
 * @return array
 */
function megamenu_get_image_styles_list() {
  $styles = [];

  $image_styles = \Drupal\image\Entity\ImageStyle::loadMultiple();

  foreach ($image_styles as $key => $value) {
    $styles[$key] = $value->get('label');
  }

  return $styles;
}

/**
 * Get skin classes.
 *
 * @param $skin_id
 *
 * @return string
 */
function megamenu_get_skin_classes($skin) {
  $skin_classes = '';
  $skins = \Drupal::moduleHandler()->invokeAll('megamenu_skins_info', []);
  \Drupal::moduleHandler()->alter('megamenu_skins_info', $skins);

  if (isset($skins[$skin]) && isset($skins[$skin]['classes'])) {
    $skin_classes = $skins[$skin]['classes'];
  }

  return $skin_classes;
}

/**
 * Get clean css filter.
 *
 * Replace characters with dash (-).
 *
 * @return array
 */
function megamenu_get_clean_css_filter() {
  $filter = [
    ' ' => '-',
    '_' => '-',
    '/' => '-',
    '[' => '-',
    ']' => '-',
    ':' => '-',
    '.' => '-',
  ];

  return $filter;
}

/**
 * Get clean id filter.
 *
 * Replace characters with underscore (_).
 *
 * @return array
 */
function megamenu_get_clean_id_filter() {
  $filter = [
    ' ' => '_',
    '-' => '_',
    '/' => '_',
    '[' => '_',
    ']' => '_',
    ':' => '_',
    '.' => '_',
  ];

  return $filter;
}

/**
 * Get column complement
 *
 * @param $columns
 *
 * @return string
 */
function megamenu_get_column_complement($columns) {
  // E.g. 6 - 11
  $columns_array = explode( '-' , $columns );

  if (count($columns_array) < 2) {
    return $columns;
  }

  // (11 - 6) - 11, i.e. 5 - 11
  return ($columns_array[1] - $columns_array[0]) . '-' . $columns_array[1];
}
