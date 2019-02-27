/**
 * @file
 * megamenu.init.js
 */

(function ($, Drupal, drupalSettings) {

  'use strict';

  var isMegaMenuInitialized = false;
  var megamenuData;

  // Check if variable exists
  if (typeof drupalSettings.megamenu !== 'undefined' && typeof drupalSettings.megamenu.megamenu_data !== 'undefined') {
    megamenuData = drupalSettings.megamenu.megamenu_data;
  }

  // Document ready event.
  jQuery(function($) {
    initMegaMenuInstances('document.ready');
  });

  // Window load event.
  $(window).on('load', function() {
    initMegaMenuInstances('window.load');
  });

  /**
   * Init all megamenu instances.
   */
  function initMegaMenuInstances(initPoint) {
    if (isMegaMenuInitialized) {
      return;
    }
    isMegaMenuInitialized = true;

    if ((typeof console != "undefined") && initPoint == 'window.load') {
      console.log('Warning: MegaMenu is initialized via event: ' + initPoint + '. There is some unrelated error preventing it from loading via the normal document ready event.');
    }

    // Hash without ID.
    if (window.location.hash.substring(1, 2) == '.') {
      var $scrollTarget = $(window.location.hash.substring(1));
      if ($scrollTarget.length) {
        window.scrollTo(0, $scrollTarget.offset().top - megamenuData.scrollto_offset);
      }
    }
    else if (window.location.hash.length) {
      setTimeout(function() {
        try {
          var $scrollTarget = $(window.location.hash);
          if ($scrollTarget.length) {
            window.scrollTo(0, $scrollTarget.offset().top - megamenuData.scrollto_offset);
          }
        }
        catch(error) {
          // Don't scroll if not an element identifier.
        }
      }, 100);
    }

    // Init every instance of megamenu located in menu instances config.
    $.each(megamenuData.menu_instances_config, function (megamenu_id, megamenu_options) {
      $('#megamenu--' + megamenu_id).megamenu();
    });
  }

})(jQuery, Drupal, drupalSettings);
