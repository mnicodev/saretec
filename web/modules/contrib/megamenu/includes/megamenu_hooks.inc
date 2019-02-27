<?php

/**
 * @file
 * The primary file for the Mega Menu hooks.
 */

use Drupal\Core\Entity\EntityInterface;
use Drupal\system\Entity\Menu;
use Drupal\menu_link_content\Entity\MenuLinkContent;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Implements hook_help().
 */
function megamenu_help($route_name, RouteMatchInterface $route_match) {
  switch ($route_name) {
    case 'help.page.megamenu':
      $output = '';
      $output .= '<h3>' . t('About') . '</h3>';
      $output .= '<p>' . t('Mega Menu provides UI to create mega menus easily.') . '</p>';

      return $output;
  }
}

/**
 * Implements hook_menu_links_discovered_alter().
 */
function megamenu_menu_links_discovered_alter(&$links) {
  // Set custom class for login/logout menu link.
  $links['user.logout']['class'] = 'Drupal\megamenu\MegaMenuLoginLogoutMenuLink';

  foreach ($links as $key => $value) {
    // Set custom class for views menu link.
    if (!empty($value['provider']) && $value['provider'] == 'views') {
      if (strpos($key, 'views_view:') === 0) {
        $links[$key]['class'] = 'Drupal\megamenu\MegaMenuViewsMenuLink';
      }
    }
  }
}

/**
 * Implements hook_page_attachments().
 */
function megamenu_page_attachments(array &$attachments) {
  // Attach menu instances.
  $megamenu_data = \Drupal::config('megamenu.settings')->getRawData();
  $attachments['#attached']['drupalSettings']['megamenu']['megamenu_data'] = $megamenu_data;

  // Attach libraries.
  if (!\Drupal::service('megamenu')->getOption('disable_megamenu_css')) {
    $attachments['#attached']['library'][] = 'megamenu/core_css';
  }
  $attachments['#attached']['library'][] = 'megamenu/core_js';

  // Attach Font Awesome library.
  if (!empty($megamenu_data['fontawesome_load'])) {
    if (empty($megamenu_data['fontawesome_use_cdn'])) {
      if (function_exists('megamenu_fontawesome_library_exists')) {
        if (megamenu_fontawesome_library_exists()) {
          // Check whether to load FontAwesome in admin routes.
          if (\Drupal::service('router.admin_context')->isAdminRoute()) {
            if (!empty($megamenu_data['fontawesome_load_admin_route'])) {
              $attachments['#attached']['library'][] = 'megamenu/fontawesome';
            }
          }
          else {
            $attachments['#attached']['library'][] = 'megamenu/fontawesome';
          }
        }
        else {
          drupal_set_message(
            t('
              Font Awesome library could not be found.
              Please make sure that fontawesome was download & extracted at /libraries/fontawesome directory.
              Please check README.txt for more details.
            '),
            'warning'
          );
        }
      }
    }
    else {
      $attachments['#attached']['library'][] = 'megamenu/fontawesome.cdn';
    }
  }
}

/**
 * Implements hook_page_top().
 *
 * Drupal 8 doesn't support inserting inline CSS/JS into head at proper place.
 * Using page_top region for inline CSS/JS is just an ugly fix!
 *
 * @see https://www.drupal.org/node/2391025
 */
function megamenu_page_top(array &$page_top) {
  // Inject custom css styles.
  $page_top['megamenu'] = [
    '#type' => 'inline_template',
    '#template' => '{{ inline_css|raw }}',
    '#context' => [
      'inline_css' => \Drupal::service('megamenu')->generateCustomStyles(),
    ]
  ];
}

/**
 * Implement hook_entity_delete().
 */
function megamenu_entity_delete(EntityInterface $entity) {
  if ($entity instanceof Menu) {
    // Delete javascript options on menu delete.
    megamenu_set_js_instance_options($entity->id(), 'delete');
    // Delete menu styles on menu delete.
    \Drupal::service('megamenu')->deleteMenuStyles($entity->id());
  }

  if ($entity instanceof MenuLinkContent) {
    // Delete item custom styles on item delete.
    $menu_link_uuid = $entity->uuid();
    $menu_link_id = $entity->getEntityTypeId() . ':' . $menu_link_uuid;
    $menu_name = $entity->getMenuName();
    \Drupal::service('megamenu')->deleteMenuItemStyles($menu_name, $menu_link_id);
  }
}
