<?php

namespace Drupal\megamenu\menuitems;

use Drupal\system\Entity\Menu;
use Drupal\Component\Utility\Html;
use Drupal\megamenu\MegaMenuMenu;

/**
 * Class MegaMenuItemBase.
 *
 * Base class for all menu items.
 *
 * @package Drupal\megamenu
 */
class MegaMenuItemBase {

  protected $type = 'unknown';
  protected $depth = 0;
  protected $args;
  protected $id;
  protected $settings;
  protected $menu;
  protected $megamenuMenu;
  protected $parentItem;
  protected $menuLink;
  protected $itemClasses = [0 => 'megamenu-item'];
  protected $itemAttributes = [];
  protected $hasChildren = false;
  protected $isSubmenuAdvanced;
  protected $submenuTag = 'ul';
  protected $submenuClasses = [];
  protected $submenuType = false;
  protected $isDropSubmenu = false;
  protected $isClosableSubmenu = false;

  /**
   * Constructor.
   *
   * @param $megamenu_menu
   * @param $menu_link
   *  - item from menu tree
   * @param int $depth
   * @param array $args
   * @param array $parent_item
   */
  function __construct($megamenu_menu, $menu_link, $depth = 0, $args = [], $parent_item = null) {
    $this->depth = $depth;
    $this->args = $args;
    $this->parentItem = $parent_item;
    $this->menuLink = $menu_link;
    $this->megamenuMenu = $megamenu_menu;
    $this->menu = $megamenu_menu->getMenu();
    $menu_name = $this->menu->id(); // Menu name from menu.

    // Setup settings
    $this->initOptions();

    if ($this->menuLink) {
      $original_link = $this->menuLink['original_link'];
      $menu_name = $this->menuLink['original_link']->getMenuName(); // Menu name from menu link.

      // Menu item id.
      if ($original_link_definition = $this->menuLink['original_link']->getPluginDefinition()) {
        $this->Id = $original_link_definition['id'];
      }

      $this->hasChildren = empty($this->menuLink['below']) ? false : true;

      // Only check if necessary (advanced submenu set to auto)
      if ($this->getOption('submenu_advanced') == 'auto') {
        if (isset($this->menuLink['classes']) && is_array($this->menuLink['classes'])) {
          if (in_array('advanced-sub', $this->menuLink['classes'])) {
            $this->isSubmenuAdvanced = true;
            $this->submenuTag = 'div';
          }
        }
      }
      elseif ($this->getOption('submenu_advanced') == 'enabled') {
        $this->isSubmenuAdvanced = true;
        $this->submenuTag = 'div';
      }
    }

    // Allows subclasses to hook in.
    $this->init();
  }

  /**
   * Init.
   *
   * Allows subclasses to hook in.
   */
  function init() {
  }

  /**
   * Get Id.
   *
   * @return int
   */
  function getId() {
    return $this->Id;
  }

  /**
   * Get menu link.
   *
   * @return mixed
   */
  function getMenuLink() {
    return $this->menuLink;
  }

  /**
   * Get parent item.
   */
  function getParentItem() {
    return $this->parentItem;
  }

  /**
   * Get submenu tag.
   *
   * @return string
   */
  function getSubmenuTag() {
    return $this->submenuTag;
  }

  /**
   * Get type.
   *
   * @return string
   */
  function getType() {
    return $this->type;
  }

  /**
   * Get item depth.
   *
   * @return mixed
   */
  function getDepth() {
    return $this->depth;
  }

  /**
   * Get URL.
   *
   * @return mixed
   */
  function getUrl() {
    return $this->menuLink['url']->toString();
  }

  /**
   * Get submenu type.
   *
   * @param bool $submenu_type
   *
   * @return bool|null|string
   */
  function getSubmenuType($submenu_type = false) {
    if (!$this->hasChildren) {
      return false;
    }

    // If already cached, don't reprocess.
    if ($this->submenuType) {
      return $this->submenuType;
    }

    if (!$submenu_type) {
      $submenu_type = $this->getOption('submenu_type');
    }

    // Determine submenu type for auto.
    if ($submenu_type == 'auto') {
      if ($this->depth == 0) {
        $submenu_type = 'mega';
      }
      elseif ($this->depth >= 1) {
        $parent = $this->getParentItem();
        if ($parent && $parent->type == 'row') {
          $parent = $this->getParentItem()->getParentItem();
        }

        $parent_submenu = $parent->getOption('submenu_type_calc');

        switch ($parent_submenu) {
          case 'mega':
          case 'block':
          case 'tabs-pane':
          case 'toggles-pane':
            $submenu_type = 'stack';
            break;
          case 'flyout':
            $submenu_type = 'flyout';
            break;
          // Inherit parent, by default.
          default:
            $submenu_type = $parent_submenu;
            break;
        }
      }
    }

    $this->submenuType = $submenu_type;

    return $submenu_type;
  }

  /**
   * Generate start element.
   *
   * @return string
   */
  function generateStartElement() {
    $output = "<li>";
    return $output;
  }

  /**
   * Generate end element.
   *
   * @return string
   */
  function generateEndElement() {
    $output = "</li>";
    return $output;
  }

  /**
   * Generate submenu wrapper start.
   *
   * @return string
   */
  function generateSubmenuWrapperStart() {
    $output = '';
    $classes = $this->submenuClasses;
    $classes[] = 'megamenu-submenu';
    $classes[] = 'megamenu-submenu--' . Html::cleanCssIdentifier($this->getId(), megamenu_get_clean_css_filter());

    // Submenu type.
    $submenu_type = $this->getOption('submenu_type');

    if ($submenu_type == 'auto') {
      $classes[] = 'megamenu-submenu--type-auto';
      $submenu_type = $this->getSubmenuType($submenu_type);
    }
    $this->settings['submenu_type_calc'] = $submenu_type;

    $classes[] = 'megamenu-submenu--type-' . $submenu_type;

    if (in_array($submenu_type, ['mega', 'flyout'])) {
      $this->isDropSubmenu = true;
      $classes[] = 'megamenu-submenu--drop';
    }

    if ($this->isDropSubmenu || $submenu_type == 'tabs-pane') {
      $this->isClosableSubmenu = true;
    }

    // Mega menu submenu alignment.
    if ($submenu_type == 'mega') {
      $classes[] = 'megamenu-submenu--align-' . $this->getOption('submenu_position');
    }
    elseif ($submenu_type == 'flyout') {
      $classes[] = 'megamenu-submenu--align-' . $this->getOption('flyout_submenu_position');
    }

    // Menu menu submenu content alignment.
    $submenu_content_align = $this->getOption('submenu_content_align');
    if ($submenu_content_align && $submenu_content_align != 'default') {
      $classes[] = 'megamenu-submenu--content-align-' . $submenu_content_align;
    }

    // Autoclear.
    $submenu_col_default = $this->getOption('submenu_column_default');
    if ($this->getOption('submenu_column_autoclear') && $submenu_col_default != 'auto' && $submenu_col_default != 'natural') {
      $classes[] = 'megamenu-autoclear';
    }

    // Padding.
    if ($this->getOption('submenu_padded')) {
      $classes[] = 'megamenu-submenu--padded';
    }

    // Background image.
    if ($this->getOption('submenu_background_image')) {
      $classes[] = 'megamenu-submenu--background-image';
    }

    // Submenu grid.
    if ($this->getOption('submenu_grid')) {
      $classes[] = 'megamenu-submenu--grid';
    }

    // Indent.
    if ($this->getOption('submenu_indent')) {
      $classes[] = 'megamenu-submenu--indent';
    }

    // Retractors.
    $retractor_top = $this->isClosableSubmenu && $this->checkDisplayRetractors() && $this->megamenuMenu->getOption('display_retractor_top');
    if ($retractor_top) {
      $classes[] = 'megamenu-submenu--retractor-top';
    }

    // Close button.
    $close_button = $this->isClosableSubmenu && $this->megamenuMenu->getOption('display_submenu_close_button');
    if ($close_button) {
      if ($retractor_top) {
        $classes[] = 'megamenu-submenu--retractor-top-2';
      }
      else {
        $classes[] = 'megamenu-submenu--retractor-top';
      }
    }

    $class = 'class="' . implode(' ', $classes) . '"';

    // Inline styles.
    $_styles = [];
    $styles = '';
    if (count($_styles) > 0) {
      $styles .= 'style="';
      foreach ($_styles as $property => $val) {
        $styles .= "$property:$val;";
      }
      $styles .= '"';
    }

    // Open submenu tag,
    $output = '<' . $this->submenuTag . ' ' . $class . ' ' . $styles . '>';

    // Retractor top.
    if ($retractor_top) {
      $retractor_tag = $this->submenuTag == 'ul' ? 'li' : 'div';

      $retractor_label = $this->megamenuMenu->getOption('retractor_label');
      if (!$retractor_label) {
        $retractor_label = t('Close');
      }

      $output .= '
        <' . $retractor_tag . ' class="megamenu-retractor megamenu-retractor--mobile">
          <i class="fa fa-times"></i> ' . $retractor_label . '
        </' . $retractor_tag . '>
      ';
    }

    // Close button.
    if ($close_button) {
      $retractor_tag = $this->submenuTag == 'ul' ? 'li' : 'div';
      $output .= '
        <' . $retractor_tag . ' class="megamenu-retractor megamenu-retractor--desktop">
          <i class="fa fa-times"></i>
        </' . $retractor_tag . '>
      ';
    }

    return $output;
  }

  /**
   * Generate submenu wrapper end.
   *
   * @return string
   */
  function generateSubmenuWrapperEnd() {
    $output = '';

    // Footer Content
    $footer_content = $this->getOption('submenu_footer_content');
    $footer_content_format = $this->getOption('submenu_footer_content_format');
    if ($footer_content) {
      $fc_tag = 'li';

      if ($this->submenuTag != 'ul') {
        $fc_tag = 'div';
      }

      $output .= '
        <' . $fc_tag . ' class="megamenu-submenu__footer megamenu-submenu__footer--' . Html::cleanCssIdentifier($this->getId(), megamenu_get_clean_css_filter()) . '">
          ' . check_markup($footer_content, $footer_content_format) . '
        </' . $fc_tag . '>
      ';
    }

    // Retractor Bottom
    if ($this->isClosableSubmenu && $this->checkDisplayRetractors() && $this->megamenuMenu->getOption('display_retractor_bottom')) {
      $retractor_tag = $this->submenuTag == 'ul' ? 'li' : 'div';

      $retractor_label = $this->megamenuMenu->getOption('retractor_label');
      if (!$retractor_label) {
        $retractor_label = t('Close');
      }

      $output .= '
        <' . $retractor_tag . ' class="megamenu-retractor megamenu-retractor--mobile">
          <i class="fa fa-times"></i>' . $retractor_label . '
        </' . $retractor_tag . '>
      ';
    }

    // Close submenu tag
    $output .= '</' . $this->submenuTag . '>';

    return $output;
  }

  /**
   * Build anchor attributes (including class, title, target, rel, href)
   *
   * @return array
   */
  function buildAnchorAtts() {
    $atts = [];

    $atts['class'] = 'megamenu-anchor';
    $atts['title'] = $this->getOption('anchor_attr_title');
    $atts['target'] = !empty($this->getOption('anchor_link_target')) ? '_blank' : '';
    $atts['rel'] = $this->getOption('anchor_xfn');
    $atts['href'] = !empty($this->menuLink['url']->toString()) ? $this->menuLink['url']->toString() : '';

    if ($this->depth == 0) {
      $atts['tabindex'] = 0;
    }

    return $atts;
  }

  /**
   * Generate anchor and its content.
   *
   * @param array $atts
   *  An array of attributes to add to the anchor
   *
   * @return string
   */
  function generateAnchor($atts) {
    // Divider.
    if ($this->menuLink['title'] == '--divide--') {
      return '<div class="megamenu-divider"><hr/></div>';
    }

    $a = '';
    $tag = 'a';

    // Highlight.
    if ($this->getOption('highlight')) {
      $atts['class'] .= ' megamenu-anchor--highlight';
    }

    // Image.
    $image = $this->generateImage();
    if ($image) {
      $atts['class'] .= ' megamenu-anchor--has-image';
    }

    // Icon.
    $icon = $this->getOption('icon');
    $icon_classes = isset($this->settings['icon_custom_class']) ? $this->settings['icon_custom_class'] : '';
    if ($icon_classes) {
      $atts['class'] .= ' megamenu-anchor--has-icon';
      $icon_tag = $this->megamenuMenu->getOption('icon_tag');
      if (!$icon_tag) {
        $icon_tag = 'i';
      }
      $icon = '<' . $icon_tag . ' class="megamenu-icon ' . ' fa ' . $icon_classes . '"></' . $icon_tag . '>';
    }
    else {
      $icon = '';
    }

    // Layout.
    $layout = $this->getOption('item_layout');
    $atts['class'] .= ' megamenu-anchor--layout-' . $layout;

    // Content align.
    $content_align = $this->getOption('content_alignment');
    if ($content_align != 'default') {
      $atts['class'] .= ' megamenu-anchor--content-align-' . $content_align;
    }

    if ($layout == 'default') {
      if ($image) {
        $layout = 'image_left';
      }
      elseif ($icon) {
        $layout = 'icon_left';
      }
      else {
        $layout = 'text_only';
      }

      $atts['class'] .= ' megamenu-anchor--layout-' . $layout;
    }

    $layout_order = megamenu_get_item_layouts($layout);

    // No wrap.
    if ($this->getOption('no_wrap')) {
      $atts['class'] .= ' megamenu-anchor--no-wrap';
    }

    // Disabled link (change tag).
    $disable_link = false;
    if ($this->getOption('disable_link')) {
      $tag = 'span';
      $disable_link = true;
      unset($atts['href']);
    }

    // Disable submenu indicator.
    $disable_submenu_indicator = false;
    if ($this->getOption('disable_submenu_indicator')) {
      $atts['class'] .= ' megamenu-anchor--no-indicator';
    }

    // ScrollTo.
    $scrollTo = $this->getOption('scrollto');
    if ($scrollTo) {
      $atts['data-megamenu-scrollto-anchor'] = $scrollTo;
    }

    // Anchor id.
    $anchor_id = $this->getOption('anchor_id');
    if ($anchor_id) {
      $atts['id'] = $anchor_id;
    }

    // Anchor class.
    $anchor_class = $this->getOption('anchor_class');
    if ($anchor_class) {
      $atts['class'] .= ' ' . $anchor_class;
    }

    // Title.
    $title = '';
    if (!$this->getOption('disable_text')) {
      $_title = $this->menuLink['title'];
      $title .= '
        <span class="megamenu-anchor__title megamenu-anchor__text">
          ' . $_title . '
        </span>
      ';
    }
    // Flag items with disabled text.
    else {
      $atts['class'] .= ' megamenu-anchor--no-text';
    }

    // Description.
    $description = '';
    if ($this->menuLink['original_link']->getDescription()) {
      $descriptions_top_level = $this->depth == 0 && $this->megamenuMenu->getOption('descriptions_top_level');
      $descriptions_headers = $this->depth > 0 && $this->getOption('item_display_calc') == 'header' && $this->megamenuMenu->getOption('descriptions_headers');
      $descriptions_normal = $this->depth > 0 && $this->getOption('item_display_calc') == 'normal' && $this->megamenuMenu->getOption('descriptions_normal');
      $descriptions_tab = $this->depth > 0 && $this->type == 'tab' && $this->megamenuMenu->getOption('descriptions_tab');

      if ($descriptions_top_level || $descriptions_headers || $descriptions_normal || $descriptions_tab) {
        $_desc = $this->menuLink['original_link']->getDescription();

        // Divider.
        $divider = $this->megamenuMenu->getOption('anchor_divider');
        if ($title && $divider) {
          $description .= '<span class="megamenu-anchor__divider">' . $divider . '</span>';
        }

        $description .= '
          <span class="megamenu-anchor__description megamenu-anchor__text">
            ' . $_desc . '
          </span>
        ';
      }
    }

    // Attributes.
    $attributes = '';
    foreach ($atts as $attr => $value) {
      if (!empty($value) || $value === 0) {
        if ($attr === 'href' && ($custom_url = $this->getOption('custom_url'))) {
          $value = $custom_url;
        }

        $attributes .= ' ' . $attr . '="' . $value . '"';
      }
    }

    // Everything is empty.
    if (!$title && !$description && !$image && !$icon) {
      return '';
    }

    // Put all the pieces in the layout order into an array.
    $layout_pieces = compact($layout_order);

    // Output anchor.
    if (isset($this->args['before'])) {
      $a .= $this->args['before'];
    }
    $a .= '<' . $tag . $attributes . '>';
    if (isset($this->args['link_before'])) {
      $a .= $this->args['link_before'];
    }

    // Add pieces based on layout order.
    foreach ($layout_pieces as $piece) {
      $a .= $piece;
    }

    if (isset($this->args['link_after'])) {
      $a .= $this->args['link_after'];
    }
    $a .= '</' . $tag . '>';
    if (isset($this->args['after'])) {
      $a .= $this->args['after'];
    }

    return $a;
  }

  /**
   * Generate image.
   *
   * @return string img HTML
   *
   * @see http://www.vdmi.nl/blog/drupal-8-rendering-image-programmatically
   */
  function generateImage() {
    $img = '';
    $img_src = '';
    $img_srcset = '';
    $img_sizes = '';
    $atts = [];

    // Disable on mobile.
    if ($this->megamenuMenu->getOption('disable_images_mobile') && megamenu_is_mobile()) {
      return '';
    }

    $image_id = $this->getOption('item_image');
    if (!$image_id) {
      return '';
    }

    if (is_array($image_id)) {
      $image_id = $image_id[0];
    }
    $file = \Drupal\file\Entity\File::load($image_id);

    if (!$file) {
      return '';
    }

    // The image.factory service can check if image is valid ($image->isValid()).
    $image = \Drupal::service('image.factory')->get($file->getFileUri());

    $atts['class'] = 'megamenu-image';

    // Determine size of image.
    $img_size = $this->getOption('image_size');
    if ($img_size == 'inherit') {
      $img_size = $this->megamenuMenu->getOption('image_size');
    }
    $atts['class'] .= ' megamenu-image--size-' . $img_size;

    // Use original file for full size.
    if ($img_size == 'full') {
      $img_src = file_create_url($file->getFileUri());
    }
    // Generate image using selected style. If style doesn't exist, use original file.
    // @see https://www.webomelette.com/how-render-your-images-image-styles-drupal-8
    else {
      $image_style = \Drupal::entityTypeManager()->getStorage('image_style')->load($img_size);
      if ($image_style) {
        $img_src = $image_style->buildUrl($file->getFileUri());
      }
      else {
        $img_src = file_create_url($file->getFileUri());
      }
    }

    // Lazy load.
    if ($this->depth > 0 && $this->megamenuMenu->getOption('lazy_load_images')) {
      $atts['class'] .= ' megamenu-image--lazy-load';
      $atts['data-src'] = $img_src;
      if ($img_srcset) {
        $atts['data-srcset'] = $img_srcset;
        if ($img_sizes) {
          $atts['data-sizes'] = $img_sizes;
        }
      }
    }
    // Normal load
    else {
      $atts['src'] = $img_src;
      if ($img_srcset) {
        $atts['srcset'] = $img_srcset;
        if ($img_sizes) {
          $atts['sizes'] = $img_sizes;
        }
      }
    }

    // Dimensions.
    $image_width = '';
    $image_height = '';
    $dimensions = $this->getOption('image_dimensions');

    // Custom dimensions - use menu item settings.
    if ($dimensions == 'custom') {
      $image_width = $this->getOption('image_width_custom');
      $image_height = $this->getOption('image_height_custom');
    }
    // Inherit settings from main menu settings.
    elseif ($dimensions == 'inherit') {
      $image_width = $this->megamenuMenu->getOption('image_width');
      $image_height = $this->megamenuMenu->getOption('image_height');
    }
    // Add width and height atts for natural width.
    elseif ($dimensions == 'natural') {
      // Apply natural dimensions if not already set.
      if ($this->megamenuMenu->getOption('image_set_dimensions')) {
        if ($image_width == '' && $image_height == '') {
          $image_width = $image->getWidth();
          $image_height = $image->getHeight();
        }
      }
    }

    // Add dimensions as attributes (with pixel units if missing).
    if ($image_width) {
      $atts['width'] = $image_width;
    }
    if ($image_height) {
      $atts['height'] = $image_height;
    }

    // Alt and title attributes.
    $alt = $this->getOption('image_alt');
    $title = $this->getOption('image_title');

    if (empty($alt)) {
      $alt = !empty($title) ? $title : $file->getFilename();
    }
    $atts['alt'] = $alt;

    if ($this->megamenuMenu->getOption('image_title_attribute')) {
      if (empty($title)) {
        $title = $file->getFilename();
      }
      $atts['title'] = $title;
    }

    // Attributes string.
    $attributes = '';
    foreach ($atts as $name => $val) {
      $attributes .= $name . '="' . $val . '" ';
    }

    $img = "<img $attributes />";

    return $img;
  }

  /**
   * Generate custom content.
   *
   * @return string
   */
  function generateCustomContent() {
    $output = '';

    $custom_content = $this->getOption('custom_content');
    $custom_content_format = $this->getOption('custom_content_format');
    if ($custom_content) {
      $pad_custom_content = $this->getOption('pad_custom_content') ? 'megamenu-custom-content--padded' : '';
      $output .= '
        <div class="megamenu-block megamenu-block--content megamenu-custom-content ' . $pad_custom_content . '">
          ' . check_markup($custom_content, $custom_content_format) . '
        </div>
      ';
    }

    return $output;
  }

  /**
   * Determine whether to display on mobile and desktop.
   *
   * @return bool
   */
  function checkDisplayOn() {
    if ($this->getOption('disable_on_mobile') && megamenu_is_mobile()) {
      return false;
    }

    if ($this->getOption('disable_on_desktop') && !megamenu_is_mobile()) {
      return false;
    }

    return true;
  }

  /**
   * Determine whether to display retractors.
   *
   * @return bool
   */
  function checkDisplayRetractors() {
    $display_retractors = true;
    $retractor_display_strategy = \Drupal::service('megamenu')->getOption('retractor_display_strategy');

    switch ($retractor_display_strategy) {
      case 'responsive':
        $display_retractors = true;
        break;
      case 'mobile':
        $display_retractors = megamenu_is_mobile();
        break;
      case 'touch':
        $display_retractors = true;
        break;
      default:
        $display_retractors = true;
    }

    return $display_retractors;
  }

  /**
   * Setup trigger.
   */
  function setupTrigger() {
    $trigger = $this->getOption('item_trigger');
    if ($trigger && $trigger != 'auto') {
      $this->itemAttributes['data-megamenu-trigger'] = $trigger;
    }
  }

  //---------------------------------------------------------------------------
  // CLASSES
  //---------------------------------------------------------------------------

  /**
   * Setup class item defaults.
   */
  function setupClassItemDefaults() {
    $classes = [];

    $current_path = \Drupal::request()->getRequestUri();
    $menu_link_url = $this->menuLink['url']->toString();
    if ($current_path == $menu_link_url) {
      $classes[] = 'megamenu-item--current';
    }

    if (!empty($this->menuLink['is_expanded'])) {
      $classes[] = 'megamenu-item--expanded';
    }
    if (!empty($this->menuLink['is_collapsed'])) {
      $classes[] = 'megamenu-item--collapsed';
    }
    if (!empty($this->menuLink['in_active_trail'])) {
      $classes[] = 'megamenu-item--active-trail';
    }

    $this->itemClasses = array_merge($this->itemClasses, $classes);

    if ($this->getOption('disable_current')) {
      $remove_current = ['megamenu-item--current', 'megamenu-item--active-trail',];
      foreach ($this->itemClasses as $k => $c) {
        if (in_array($c, $remove_current)) {
          unset($this->itemClasses[$k]);
        }
      }
      $this->itemClasses[] = 'megamenu-item--no-current';
    }
  }

  /**
   * Setup class id.
   */
  function setupClassId() {
    $this->itemClasses[] = 'megamenu-item--' . Html::cleanCssIdentifier($this->getId(), megamenu_get_clean_css_filter());
  }

  /**
   * Setup class item display.
   */
  function setupClassItemDisplay() {
    $this->settings['item_display_calc'] = '';

    // Item display.
    if ($this->depth > 0) {
      $item_display = $this->getOption('item_display');
      $this->itemClasses[] = 'megamenu-item--display-' . $item_display;

      // Determine auto.
      if ($item_display == 'auto') {
        $parent_type = $this->getParentItem()->getType();

        if ($item_display == 'auto') {
          $in_sub = $this->getParentItem()->getOption('submenu_type_calc');

          switch($in_sub) {
            case 'mega' :
              if ($this->depth == 1) {
                $item_display = 'header';
              }
              elseif ($this->depth > 1 && $this->getParentItem()->getDepth() == 1) {
                $item_display = 'header';
              }
              elseif ($this->getParentItem()->getType() == 'row') {
                $item_display = 'header';
              }
              elseif ($this->depth > 1 && $this->getParentItem()->getParentItem()->getOption('submenu_type_calc') == 'flyout') {
                $item_display = 'header';
              }
              else {
                // For items that are in the submenu but yet undetermined.
                if ($this->depth > 1) {
                  // If it's parent wasn't a header, but the sub of the parent was a mega, this should probably be a header.
                  if ($this->getParentItem()->getOption('item_display_calc') != 'header') {
                    $item_display = 'header';
                  }
                }
                else $item_display = 'normal';
              }
              break;
            case 'flyout':
              $item_display = 'normal';
              break;
            case 'stack':
              $item_display = 'normal';
              break;
            case 'block':
              $item_display = 'header';
              break;
            case 'tabs-group':
            case 'toggles-group':
              $item_display = '';
              break;
            case 'tabs-pane':
            case 'toggles-pane':
              $item_display = 'header';
              break;
            default:
              $item_display = 'unknown-[' . $in_sub . ']';
              break;
          }
        }

        if ($item_display) {
          $this->itemClasses[] = 'megamenu-item--display-' . $item_display;
        }
      }

      $this->settings['item_display_calc'] = $item_display;
    }
  }

  /**
   * Setup class level.
   */
  function setupClassLevel() {
    $this->itemClasses[] = 'megamenu-item--level-' . $this->getDepth();
  }

  /**
   * Setup class layout columns.
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
    if ($this->depth > 0) {
      // If auto, apply submenu column default from parent item.
      if ($cols == 'auto') {
        $cols = $this->getParentItem()->getOption('submenu_column_default');
      }
    }
    $this->itemClasses[] = 'megamenu-column megamenu-column--' . $cols;

    // New row.
    if ($this->getOption('clear_row')) {
      $this->itemClasses[] = 'megamenu-clear-row';
    }
  }

  /**
   * Setup class alignment.
   */
  function setupClassAlignment() {
    $align = $this->getOption('item_align');
    if ($align && $align != 'auto') {
      $this->itemClasses[] = 'megamenu-item--align-' . $align;
    }
  }

  /**
   * Setup class mini item.
   */
  function setupClassMiniItem() {
    if ($this->getOption('mini_item')) {
      $this->itemClasses[] = 'megamenu-item--mini';
    }
  }

  /**
   * Setup class rtl submenu.
   */
  function setupClassRtlSubmenu() {
    if (($this->getSubmenuType() == 'mega') && ($this->getOption('submenu_position') == 'right_edge_item')) {
      $this->itemClasses[] = 'megamenu-submenu--rtl';
    }
    elseif (($this->getSubmenuType() == 'flyout') && ($this->getOption('flyout_submenu_position') == 'right_edge_item')) {
      $this->itemClasses[] = 'megamenu-submenu--rtl';
      $this->itemClasses[] = 'megamenu-submenu--reverse';
    }
  }

  /**
   * Setup class responsive.
   */
  function setupClassResponsive() {
    if ($this->getOption('hide_on_mobile')) {
      $this->itemClasses[] = 'megamenu-item--hide-mobile';
    }
    if ($this->getOption('hide_on_desktop')) {
      $this->itemClasses[] = 'megamenu-item--hide-desktop';
    }
  }

  /**
   * Setup class submenu.
   */
  function setupClassSubmenu() {
    $submenu_type = $this->getSubmenuType();

    if ($submenu_type) {
      if (in_array($submenu_type, ['mega', 'flyout'])) {
        $this->itemClasses[] = 'megamenu-item--has-submenu-drop';

        // Show current.
        $is_active_trail = in_array('megamenu-item--active-trail', $this->itemClasses);
        $is_current_item = in_array('megamenu-item--current', $this->itemClasses);
        if ($this->getOption('show_current') && ($is_active_trail || $is_current_item)) {
          $this->itemClasses[] = 'megamenu-active';
        }

        // Show default.
        if ($this->getOption('show_default')) {
          $this->itemClasses[] = 'megamenu-active';
        }
      }
      $this->itemClasses[] = 'megamenu-item--has-submenu-' . $submenu_type;
    }

    if (($this->getOption('submenu_position') == 'vertical_parent_item') || ($this->getOption('flyout_submenu_position') == 'vertical_parent_item')) {
      $this->itemClasses[] = 'megamenu-item--relative';
    }

    if ($submenu_type == 'flyout' && $this->getOption('flyout_submenu_position') == 'vertical_full_height') {
      $this->itemClasses[] = 'megamenu-item--flyout-full-height';
    }
  }

  /**
   * Setup class disable padding.
   */
  function setupClassDisablePadding() {
    $disable_padding = $this->getOption('disable_padding');
    if ($disable_padding) {
      $this->itemClasses[] = 'megamenu-item--disable-padding';
    }
  }

  /**
   * Setup class item type.
   */
  function setupClassItemType() {
    if ($this->type) {
      $this->itemClasses[] = 'megamenu-item--type-' . $this->type;
    }
  }

  /**
   * Filter item classes.
   *
   * @return string
   */
  function filterItemClasses() {
    $class_names = join(' ', array_filter($this->itemClasses));
    $class_names = $class_names ? ' class="' . $class_names . '"' : '';

    return $class_names;
  }

  /**
   * Filter item id.
   *
   * @return string
   */
  function filterItemId() {
    $id = 'megamenu-item--' . Html::cleanCssIdentifier($this->getId(), megamenu_get_clean_css_filter());
    $id = $id ? ' id="' . $id . '"' : '';

    return $id;
  }

  //---------------------------------------------------------------------------
  // OPTIONS
  //---------------------------------------------------------------------------

  /**
   * Init options.
   *
   * For views menu links options are saved for every view display separately.
   */
  public function initOptions() {
    if ($this->menuLink) {
      $menu_link_options = $this->menuLink['url']->getOptions();

      // Override menu link options for views menu links.
      if (isset($this->menuLink['original_link'])){
        if ($this->menuLink['original_link'] instanceof \Drupal\megamenu\MegaMenuViewsMenuLink) {
          $menu_link_options = $this->getViewsMenuLinkOptions();
        }
      }

      if (isset($menu_link_options['megamenu'])) {
        $this->settings = $menu_link_options['megamenu'];
      }
    }
    else {
      $this->settings = $this->getDefaultOptions();
    }
  }

  /**
   * Get views menu link options.
   *
   * For views menu links options are saved for every view display separately.
   *
   * @return array
   */
  function getViewsMenuLinkOptions() {
    $options = [];

    $view = $this->menuLink['original_link']->loadView();
    $display = &$view->storage->getDisplay($view->current_display);

    if (isset($display['display_options']['menu']['options'])) {
      $options = $display['display_options']['menu']['options'];
    }

    return $options;
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
   * Get option.
   *
   * @param $option
   * @param $default
   *
   * @return null
   */
  function getOption($option, $default = null) {
    // Value from settings.
    if (isset($this->settings[$option])) {
      $menu_option = $this->settings[$option];
    }
    // Default fallback.
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
   * Get default option value.
   *
   * @param $option
   *
   * @return mixed|string
   */
  public function getDefaultOption($option) {
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
      'item_type' => 'normal',
      // General
      'item_display' => 'auto',
      'disable_link' => 0,
      'disable_text' => 0,
      'highlight' => 0,
      'mini_item' => 0,
      'item_align' => 'auto',
      'custom_url' => '',
      'scrollto' => '',
      'no_wrap' => 0,
      'item_trigger' => 'auto',
      'disable_submenu_indicator' => 0,
      'disable_current' => 0,
      'anchor_class' => '',
      'anchor_id' => '',
      'anchor_link_target' => 0,
      'anchor_attr_title' => '',
      'anchor_xfn' => '',
      // Row
      'row_padding' => '',
      'grid_row' => 0,
      // Custom content
      'custom_content' => '',
      'custom_content_format' => 'full_html',
      'pad_custom_content' => 0,
      // Tabs
      'tab_layout' => 'left',
      'tab_block_columns' => 'full',
      'tabs_group_layout' => 'auto',
      'panels_group_layout' => 'auto',
      'panels_grid' => 0,
      'panels_padding' => '',
      'show_default_panel' => 1,
      'tabs_trigger' => 'mouseover',
      // Drupal Block
      'drupal_block_id' => '',
      'pad_drupal_block' => 0,
      // Layout
      'item_layout' => 'default',
      'content_alignment' => 'default',
      // Responsive
      'hide_on_mobile' => 0,
      'hide_on_desktop' => 0,
      'disable_on_mobile' => 0,
      'disable_on_desktop' => 0,
      'disable_submenu_on_mobile' => 0,
      // Submenu
      'submenu_type' => 'auto',
      'submenu_position' => 'full_width',
      'flyout_submenu_position' => 'left_edge_item',
      'submenu_width' => '',
      'submenu_min_width' => '',
      'submenu_min_height' => '',
      'submenu_content_align' => 'default',
      'submenu_column_dividers' => '',
      'submenu_column_min_height' => '',
      'submenu_grid' => 0,
      'submenu_padded' => 0,
      'submenu_indent' => 0,
      'show_current' => 0,
      'show_default' => 0,
      'submenu_background_image' => '',
      'submenu_background_image_repeat' => 'no-repeat',
      'submenu_background_position' => 'bottom right',
      'submenu_background_size' => 'auto',
      'submenu_padding' => '',
      'submenu_footer_content' => '',
      'submenu_footer_content_format' => 'full_html',
      'submenu_advanced' => 'auto',
      // Image
      'image_title' => '',
      'image_alt' => '',
      'item_image' => '',
      'image_size' => 'inherit',
      'image_dimensions' => 'inherit',
      'image_width_custom' => '',
      'image_height_custom' => '',
      'image_text_top_padding' => '',
      'disable_padding' => 0,
      // Icon
      'icon_custom_class' => '',
      // Common
      'submenu_column_default' => 'auto', // Row
      'submenu_column_autoclear' => 1, // Row, Column Layout, Submenu
      'submenu_column_default' => 'auto', // Tabs, Column Layout, Submenu
      'columns' => 'auto', // Layout, Column Layout
      'clear_row' => 0, // Layout, Column Layout
    ];

    return $default_options;
  }

}
