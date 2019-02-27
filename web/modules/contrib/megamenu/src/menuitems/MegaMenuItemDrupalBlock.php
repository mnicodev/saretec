<?php

namespace Drupal\megamenu\menuitems;

use Drupal\Component\Utility\Html;

/**
 * Class MegaMenuItemDrupalBlock.
 *
 * @package Drupal\megamenu
 */
class MegaMenuItemDrupalBlock extends MegaMenuItemBase {

  var $type = 'drupal_block';

  /**
   * {@inheritdoc}
   */
  function generateStartElement() {
    $item_output = '';
    $class_names = $value = '';

    // Setup classes.
    $this->setupClassItemDefaults();
    $this->setupClassId();
    $this->setupClassItemDisplay();
    $this->setupClassLevel();
    $this->setupClassLayoutColumns();
    $this->setupClassAlignment();
    $this->setupClassMiniItem();
    $this->setupClassSubmenu();
    $this->setupClassRtlSubmenu();
    $this->setupClassDisablePadding();
    $this->setupClassResponsive();
    $this->setupClassItemType();

    $class_names = $this->filterItemClasses();

    // Setup id.
    $id = $this->filterItemId();

    // Setup trigger.
    $this->setupTrigger();

    // LI attributes.
    $atts = ' ';
    foreach ($this->itemAttributes as $att => $val) {
      $atts .= $att . '="' . $val . '" ';
    }

    // Item LI
    $item_output .= '<li' . $id . $value . $class_names . $atts . '>';

    // Anchor attributes.
    $atts = $this->buildAnchorAtts();
    // Anchor.
    $item_output .= $this->generateAnchor($atts);

    // Drupal block.
    $item_output .= $this->generateDrupalBlock();

    return $item_output;
  }

  /**
   * Get anchor.
   *
   * @param array $atts
   *
   * @return string
   */
  function generateAnchor($atts) {
    return '';
  }

  /**
   * Get drupal block.
   *
   * @return string
   */
  function generateDrupalBlock() {
    $output = '';

    $drupal_block_id = $this->getOption('drupal_block_id');
    if (!$drupal_block_id) {
      return '';
    }

    $pad_drupal_block = $this->getOption('pad_drupal_block') ? 'megamenu-block--custom--padded' : '';

    $plugin_block = \Drupal::service('plugin.manager.block')->createInstance($drupal_block_id, []);

    // Some blocks might implement access check.
    $access_result = $plugin_block->access(\Drupal::currentUser());
    if ($access_result) {
      // In some cases, you need to add the cache tags/context depending on the block implemention.
      // As it's possible to add the cache tags and contexts in the render method and in
      // ::getCacheTags and ::getCacheContexts methods.
      $render = $plugin_block->build();

      if ($plugin_block) {
        $output .= '
            <div class="megamenu-block megamenu-block--drupal-block megamenu-block--content ' . $pad_drupal_block . '">
              ' . render($render) . '
            </div>
          ';
      }
    }

    return $output;
  }

}
