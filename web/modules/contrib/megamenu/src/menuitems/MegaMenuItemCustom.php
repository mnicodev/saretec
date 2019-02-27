<?php

namespace Drupal\megamenu\menuitems;

use Drupal\Component\Utility\Html;

/**
 * Class MegaMenuItemCustom.
 *
 * @package Drupal\megamenu
 */
class MegaMenuItemCustom extends MegaMenuItemBase {

  var $type = 'custom_content';

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

    // Item LI.
    $item_output .= '<li' . $id . $value . $class_names . $atts . '>';

    // Anchor attributes.
    $atts = $this->buildAnchorAtts();
    // Anchor.
    $item_output .= $this->generateAnchor($atts);

    // Custom content.
    $item_output .= $this->generateCustomContent();

    return $item_output;
  }

  /**
   * {@inheritdoc}
   */
  function generateAnchor($atts) {
    return '';
  }

}
