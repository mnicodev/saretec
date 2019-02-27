/**
 * @file
 * JavaScript file to initialize the AOS.
 */

(function ($) {
  Drupal.behaviors.aosTheme = {
    attach: function (context) {
      AOS.init();
    }
  };
})(jQuery);
