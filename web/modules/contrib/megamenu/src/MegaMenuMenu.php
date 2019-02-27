<?php

namespace Drupal\megamenu;

use Drupal\system\Entity\Menu;
use Drupal\Component\Utility\Html;

/**
 * Class MegaMenuMenu
 *
 * @package Drupal\megamenu
 */
class MegaMenuMenu {

  protected $id = 0;
  protected $menu = null;
  protected $settings = [];

  /**
   * Constructor.
   */
  function __construct($menu_name) {
    $this->menu = \Drupal::service('megamenu')->getMenuByName($menu_name);
    $this->id = $this->menu ? $this->menu->Id() : 0;
    $this->initOptions();
  }

  /**
   * Get id.
   */
  public function getId() {
    return $this->id;
  }

  /**
   * Get menu.
   *
   * @return $menu
   */
  function getMenu() {
    return $this->menu;
  }

  /**
   * Load skin library.
   *
   * @param $skin_name
   *
   * @return string
   */
  function loadSkinLibrary($skin_name) {
    $output = '';

    $skins = \Drupal::moduleHandler()->invokeAll('megamenu_skins_info', []);
    if ($skins) {
      \Drupal::moduleHandler()->alter('megamenu_skins_info', $skins);
    }

    if (isset($skins[$skin_name]) && isset($skins[$skin_name]['library'])) {
      $skin_library = [
        '#attached' => [
          'library' => [
            $skins[$skin_name]['library'],
          ]
        ]
      ];

      $output .= \Drupal::service('renderer')->render($skin_library);
    }

    return $output;
  }

  /**
   * Generate content before menu.
   *
   * @return string
   */
  function generateContentBeforeMenu() {
    $output = '';

    $before_content = $this->getOption('content_before_nav');
    $before_content_format = $this->getOption('content_before_nav_format');
    if ($before_content) {
      $output .= '<!-- MegaMenu content before -->';
      $output .= check_markup($before_content, $before_content_format);
      $output .= '<!-- End MegaMenu content before -->';
    }

    return $output;
  }

  /**
   * Generate start element.
   *
   * @return string
   */
  function generateStartElement() {
    $output = '';

    // General.
    $container_classes = 'megamenu megamenu--no-js';
    $menu_class = 'megamenu-nav';
    $container = $this->getOption('container_tag');

    // Menu ID.
    $container_id = 'megamenu--' . Html::cleanCssIdentifier($this->getId(), megamenu_get_clean_css_filter());
    $container_classes .= ' megamenu--' . Html::cleanCssIdentifier($this->getId(), megamenu_get_clean_css_filter());

    // Responsive.
    if ($this->getOption('responsive')) {
      $container_classes .= ' megamenu--responsive';

      if ($this->getOption('responsive_columns') == 1) {
        $container_classes .= ' megamenu--responsive--single-column';
      }

      $breakpoint = \Drupal::service('megamenu')->getOption('responsive_breakpoint');
      if (!$breakpoint) {
        $breakpoint = 'default';
      }
      else {
        $breakpoint = intval($breakpoint);
      }
      $container_classes .= ' megamenu--responsive--' . $breakpoint;
    }

    // Responsive (no collapse).
    if ($this->getOption('responsive_collapse')) {
      $container_classes .= ' megamenu--responsive--collapse';
    }
    else {
      $container_classes .= ' megamenu--responsive--no-collapse';
    }

    // Orientation.
    $orientation = $this->getOption('orientation') == 'vertical' ? 'vertical' : 'horizontal';
    $container_classes .= ' megamenu--' . $orientation;

    // Transition.
    $transition = $this->getOption('transition');
    $container_classes .= ' megamenu--transition-' . $transition;

    // Trigger.
    $trigger = $this->getOption('trigger');
    $container_classes .= ' megamenu--trigger-' . $trigger;

    // Skin.
    $skin = $this->getOption('skin');
    if ($skin) {
      $container_classes .= ' megamenu--skin-' . $skin;
      $container_classes .= ' ' . megamenu_get_skin_classes($skin);
    }

    // Border class.
    if (!$this->getOption('disable_border_classes')) {
      $container_classes .= ' megamenu--has-border';
    }

    // Menu bar alignment.
    $bar_align = $this->getOption('bar_align');
    $container_classes .= ' megamenu--bar-align-' . $bar_align;

    // Menu item alignment.
    $items_align = $this->getOption('items_align');
    $container_classes .= ' megamenu--items-align-' . $items_align;

    // Inner menu center.
    if ($this->getOption('bar_inner_center')) {
      $container_classes .= ' megamenu--bar-inner-center';
    }

    // Submenu bound.
    if ($this->getOption('bound_submenus') == 'on' || $this->getOption('orientation') == 'vertical') {
      $container_classes .= ' megamenu--bound';
    }
    elseif ($this->getOption('bound_submenus') == 'inner') {
      $container_classes .= ' megamenu--bound--inner';
    }

    // Submenu scrolling.
    if ($this->getOption('transition') != 'slide' && !$this->getOption('submenu_scrolling')) {
      $container_classes .= ' megamenu--disable-submenu-scroll';
    }

    // Force current submenus.
    if ($this->getOption('force_current_submenus')) {
      $container_classes .= ' megamenu--force-current-submenu';
    }

    // Invert submenus.
    if ($this->getOption('invert_submenus')) {
      $container_classes .= ' megamenu--invert';
    }

    // Submenu background image hiding.
    if ($this->getOption('submenu_background_image_reponsive_hide')) {
      $container_classes .= ' megamenu--hide-background';
    }

    // Submenu indicators.
    if ($this->getOption('display_submenu_indicators')) {
      $container_classes .= ' megamenu--sub-indicators';
    }

    // Retractors.
    if (\Drupal::service('megamenu')->getOption('retractor_display_strategy') == 'responsive') {
      $container_classes .= ' megamenu--retractors-responsive';
    }

    // Icon Display
    if ($this->getOption('icon_display') == 'inline') {
      $container_classes .= ' megamenu--icons-inline';
    }

    // Custom CSS classes.
    if ($this->getOption('custom_classes')) {
      $container_classes .= ' ' . $this->getOption('custom_classes');
    }

    $output .= '
      <' . $container . ' id="' . $container_id . '" class="' . $container_classes . '">
        <ul id="megamenu-nav-' . Html::cleanCssIdentifier($this->getId(), megamenu_get_clean_css_filter()) . '" class="' . $menu_class . '">
    ';

    return $output;
  }

  /**
   * Generate end element.
   *
   * @return string
   */
  function generateEndElement() {
    $output = '';

    $container = $this->getOption('container_tag');

    $output .= '
        </ul>
      </' . $container . '>
    ';

    return $output;
  }

  /**
   * Get responsive toggle (item with menu title above all menu items).
   *
   * @return string
   */
  function getResponsiveToggle() {
    $output = '';

    if (!$this->getOption('responsive') || !$this->getOption('responsive_toggle')) {
      return $output;
    }

    $toggle_content = $this->getOption('responsive_toggle_content');
    $toggle_tag = $this->getOption('responsive_toggle_tag');
    $toggle_content_align = $this->getOption('responsive_toggle_content_alignment');
    $toggle_align = $this->getOption('responsive_toggle_alignment');
    $toggle_classes = !$this->getOption('responsive_collapse') ? 'megamenu-responsive-toggle--open' : '';

    $toggle_args = [
      'toggle_content' => $toggle_content,
      'tag' => $toggle_tag,
      'content_align' => $toggle_content_align,
      'align' => $toggle_align,
      'classes' => $toggle_classes,
    ];

    $megamenu_toggle = $this->generateResponsiveToggle($toggle_args);

    $output .= $megamenu_toggle;

    return $output;
  }

  /**
   * Generate responsive toggle.
   *
   * @param array $args
   *
   * @return string
   */
  function generateResponsiveToggle($args = []) {
    $output = '';
    $container_id = 'megamenu--' . Html::cleanCssIdentifier($this->getId(), megamenu_get_clean_css_filter());
    $skin = isset($args['skin']) ? $args['skin'] : '';
    $toggle_content = isset($args['toggle_content']) ? $args['toggle_content'] : t('Menu');
    $icon_class = isset($args['icon_class']) ? $args['icon_class'] : 'bars';
    $tag = isset($args['tag']) ? $args['tag'] : 'a';
    $toggle_id = isset($args['toggle_id']) ? $args['toggle_id'] : '';
    $content_align = isset($args['content_align']) ? $args['content_align'] : 'left';
    $align = isset($args['align']) ? $args['align'] : 'full';
    $classes = isset($args['classes']) ? $args['classes'] : '';

    if (!$skin) {
      $skin = $this->getOption('skin');
    }

    $class = 'megamenu-responsive-toggle';
    $class .= ' megamenu-responsive-toggle--' . Html::cleanCssIdentifier($this->getId(), megamenu_get_clean_css_filter());

    if ($skin) {
      $class .= ' megamenu--skin-' . $skin;
    }

    $class .= ' megamenu-responsive-toggle--content-align-' . $content_align;
    $class .= ' megamenu-responsive-toggle--align-' . $align;
    if (!$toggle_content) {
      $class .= ' megamenu-responsive-toggle--icon-only';
    }

    $id = '';
    if ($toggle_id) {
      $id = ' id="' . $toggle_id . '" ';
    }

    $class .= ' ' . $classes;

    $output .= '<' . $tag . $id . ' class="' . $class . '" data-megamenu-anchor="' . $container_id . '">';
    if ($icon_class) {
      $output .= '<i class="fa fa-'.$icon_class.'"></i>';
    }
    if ($toggle_content) {
      $output .= $toggle_content;
    }
    $output .= '</'.$tag.'>';

    return $output;
  }

  //---------------------------------------------------------------------------
  // OPTIONS
  //---------------------------------------------------------------------------

  /**
   * Init options.
   */
  public function initOptions() {
    if ($this->menu) {
      // Get megamenu options from menu (empty array if there's no megamenu settings).
      $this->settings = $this->menu->getThirdPartySettings('megamenu');
    }
    else {
      $this->settings = $this->getDefaultOptions();
    }
  }

  /**
   * Get menu option.
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
    // Default Fallback
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
   * Get default option value.
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
   *
   * @return array
   */
  public static function getDefaultOptions() {
    $default_options = [
      'megamenu_status' => 0,
      // General
      'skin' => 'bartik_blue_bar',
      'orientation' => 'horizontal',
      'vertical_submenu_width' => '',
      'trigger' => 'hover_intent',
      'transition' => 'shift',
      'transition_duration' => '',
      'custom_classes' => '',
      'disable_border_classes' => 0,
      // Position & layout
      'bar_align' => 'full',
      'bar_width' => '',
      'bar_margin_top' => '',
      'bar_margin_bottom' => '',
      'items_align' => 'left',
      'bar_inner_center' => 0,
      'bar_inner_width' => '',
      // Responsive & mobile
      'disable_mobile' => '',
      'responsive' => 1,
      'responsive_columns' => 2,
      'responsive_toggle' => 1,
      'responsive_toggle_tag' => 'a',
      'responsive_toggle_content' => 'Menu',
      'responsive_toggle_content_alignment' => 'left',
      'responsive_toggle_alignment' => 'full',
      'responsive_collapse' => 1,
      'responsive_max_height' => '',
      'display_retractor_top' => 0,
      'display_retractor_bottom' => 1,
      'retractor_label' => '',
      // Submenus
      'container_tag' => 'nav',
      'content_before_nav' => '',
      'content_before_nav_format' => 'full_html',
      'bound_submenus' => 'on',
      'submenu_inner_width'	=> '',
      'submenu_scrolling' => 0,
      'submenu_max_height' => '',
      'display_submenu_indicators' => 1,
      'display_submenu_close_button' => 0,
      // Descriptions
      'descriptions_top_level' => 1,
      'descriptions_headers' => 1,
      'descriptions_normal' => 1,
      'descriptions_tab' => 1,
      'anchor_divider' => ' - ',
      // Images
      'image_size' => 'full',
      'image_width' => '',
      'image_height' => '',
      'image_set_dimensions' => 1,
      'image_title_attribute' => 0,
      'disable_images_mobile' => 0,
      'lazy_load_images' => 0,
      'image_text_top_padding' => '',
      'submenu_background_image_reponsive_hide' => 0,
      // Icons
      'icon_width' => '',
      'icon_tag' => 'i',
      'icon_display' => 'block',
      // Fonts
      'google_font' => 'none',
      'google_font_style' => 'none',
      'custom_font_family' => '',
      'custom_font_property' => '',
    ];

    return $default_options;
  }

}
