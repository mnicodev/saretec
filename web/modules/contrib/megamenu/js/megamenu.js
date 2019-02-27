/*
 *  @file
 * megamenu.js
 */

/**
 * Universal logic for debouncing events.
 *
 * @see https://www.paulirish.com/2009/throttled-smartresize-jquery-event-handler/
 */
(function($, sr) {
  /**
   * Debouncing function.
   *
   * Debouncing ensures that exactly one signal is sent for an event that may be
   * happening several times — or even several hundreds of times over an extended period.
   * As long as the events are occurring fast enough to happen at least once in
   * every detection period, the signal will not be sent!
   *
   * @see http://unscriptable.com/index.php/2009/03/20/debouncing-javascript-methods/
   */
  var debounce = function(func, threshold, execAsap) {
    var timeout;
    return function debounced() {
      var obj = this, args = arguments;
      function delayed () {
        if (!execAsap) {
          func.apply(obj, args);
        }
        timeout = null;
      };
      if (timeout) {
        clearTimeout(timeout);
      }
      else if (execAsap) {
        func.apply(obj, args);
      }
      timeout = setTimeout(delayed, threshold || 100);
    };
  }

  jQuery.fn[sr] = function(fn) {
    return fn ? this.bind('resize', debounce(fn)) : this.trigger(sr);
  };

})(jQuery, 'megaMenuSmartResize');

/**
 * Main MegaMenu logic.
 */
(function ($, Drupal, drupalSettings, window, document, undefined) {

  'use strict';

  /**
   * The main mega menu class that will be used as a jQuery plugin.
   *
   * @see https://developer.mozilla.org/en/docs/Web/JavaScript/Reference/Classes
   */
  function MegaMenu(element, options) {

    // Init variables.
    this.$megamenuElement = $(element);
    this.settings = [];
    this.megamenuData = [];
    this.orientation = 'horizontal';
    if (this.$megamenuElement.hasClass('megamenu--vertical')) {
      this.orientation = 'vertical';
    }
    this.windowWidth = 0;
    this.oldWindowWidth = 0;
    this.lastWidth = 0;
    this.eventsDisabled = false;
    this.allowTriggerOverrides = true;
    this.$currentFocus = false;
    this.suppressClicks = false;
    this.mouseDown = false;
    this.currentMousePosX = 0;
    this.currentMousePosY = 0;
    this.previousMousePosX = 0;
    this.previousMousePosY = 0;
    this.transitions = false;
    this.transitionEnd = '';
    this.touchEnabled = false;
    this.touchStart = 'touchstart';
    this.touchEnd = 'touchend';
    this.touchMove = 'touchmove';
    this.toggleEvent = this.touchEnd + ' click';
    this.touchOffCloseStart = 0;

    //--------------------------------------------------------------------------
    // HELPERS
    //--------------------------------------------------------------------------

    /**
     * Init MegaMenu data.
     *
     * @param plugin
     */
    this.initMegaMenuData = function (plugin) {
      plugin = plugin || this;

      if (typeof drupalSettings.megamenu !== 'undefined' && typeof drupalSettings.megamenu.megamenuData !== 'undefined') {
        this.megamenuData = drupalSettings.megamenu.megamenuData;
      }
    }

    /**
     * Init MegaMenu settings.
     *
     * @param options
     * @param plugin
     */
    this.initMegaMenuSettings = function (options, plugin) {
      plugin = plugin || this;

      plugin.settings = $.extend(
        {},
        {
          touch_events: true,
          mouse_events: true,
          retractors: true,
          move_threshold: 10,
          submenu_animation_duration: 500,
          breakpoint_secondary: 480,
          breakpoint_primary: plugin.getOption('responsive_breakpoint', {datatype: 'numeric'}, 959),
          remove_conflicts: plugin.getOption('remove_conflicts', {datatype: 'boolean'} , true),
          accessible: plugin.getOption('accessible', {datatype: 'boolean'}, true),
          touch_off_close: plugin.getOption('touch_off_close', {datatype: 'boolean'}, true),
          retractor_display_strategy: plugin.getOption('retractor_display_strategy', {datatype: 'string'}, 'responsive'),
          reposition_on_load: plugin.getOption('reposition_on_load', {datatype: 'boolean'}, false),
          intent_interval: plugin.getOption('intent_interval', {datatype: 'numeric'}, 100),
          intent_threshold: plugin.getOption('intent_threshold', {datatype: 'numeric'}, 300),
          intent_delay: plugin.getOption('intent_delay', {datatype: 'numeric'}, 300),
          collapse_after_scroll: plugin.getOption('collapse_after_scroll', {datatype:'boolean'}, true),
          scrollto_offset: plugin.getOption('scrollto_offset', {datatype: 'numeric'}, 0),
          scrollto_duration: plugin.getOption('scrollto_duration', {datatype: 'numeric'}, 1000),
        },
        options
      );
    }

    /**
     * Start MegaMenu.
     *
     * @param plugin
     */
    this.startMegaMenu = function (plugin) {
      plugin = plugin || this;

      plugin.$megamenuElement.removeClass('megamenu--no-js');
    }

    /**
     * Align menu items to right.
     *
     * @param plugin
     */
    this.alignMenuItemsRight = function (plugin) {
      plugin = plugin || this;
      plugin.lastWidth = window.innerWidth;
      var currentWidth = plugin.lastWidth;
      var $rightItems = plugin.$megamenuElement.find('.megamenu-item--level-0.megamenu-item--align-right');

      if ($rightItems.length) {
        $(window).megaMenuSmartResize(function () {
          currentWidth = window.innerWidth;
          if (plugin.lastWidth <= plugin.settings.breakpoint_primary && currentWidth >= plugin.settings.breakpoint_primary) {
            $rightItems.hide();
            $rightItems[0].offsetHeight;
            $rightItems.show();
          }
          plugin.lastWidth = currentWidth;
        });
      }
    }

    /**
     * Check if browser supports feature.
     *
     * @param prop
     *
     * @see https://github.com/burocratik/outdated-browser
     */
    this.checkBrowserSupport = function (prop) {
      var div = document.createElement('div');
      var vendors = 'Khtml Ms O Moz Webkit'.split(' ');

      var len = vendors.length;

      if (prop in div.style) {
        return true;
      }

      prop = prop.replace(/^[a-z]/, function (val) {
        return val.toUpperCase();
      });

      while (len--) {
        if (vendors[len] + prop in div.style) {
          return true;
        }
      }
      return false;
    };

    /**
     * Get option.
     *
     * @param id
     * @param args
     * @param defaultValue
     * @param plugin
     *
     * @returns {*}
     */
    this.getOption = function (id, args, defaultValue, plugin) {
      plugin = plugin || this;

      // Id not passed, return default value.
      if (!plugin.megamenuData || !plugin.megamenuData.hasOwnProperty(id)) {
        return defaultValue;
      }

      var value = plugin.megamenuData[id];

      if (args.hasOwnProperty('datatype')) {
        if (args['datatype'] == 'numeric') {
          value = parseInt(val);
          if (!val) {
            value = defaultValue;
          }
        }
        else if (args['datatype'] == 'boolean') {
          if (val == 1 || val == '1' || val == 'on') {
            value = true;
          }
          else {
            value = false;
          }
        }
      }

      return val;
    }

    /**
     * Remove conflicts with other JavaScript.
     *
     * @param plugin
     */
    this.removeJsConficts = function (plugin) {
      plugin = plugin || this;

      if (plugin.settings.remove_conflicts) {
        plugin.$megamenuElement
          .find('.megamenu-item, .megamenu-anchor, .megamenu-submenu')
          .add(plugin.$megamenuElement)
          .removeAttr('style')
          .unbind()
          .off();
      }
    }

    /**
     * Reset events.
     *
     * @param $target
     * @param plugin
     */
    this.resetEvents = function ($target, plugin) {
      plugin = plugin || this;
      var $item = $target.parent();

      // Remove events.
      $target.off(plugin.touchEnd);
      $target.off(plugin.touchMove);
      $item.off('mousemove.mouse_intent');
      $item.off('mouseleave.mouse_intent_none');
      // Reset data.
      $item.data('mouse_intent_timer', clearTimeout($item.data('mouse_intent_timer')));
      $item.data('mouse_intent_state', 0);
    }

    /**
     * Init active trails.
     *
     * @param plugin
     */
    this.initActiveTrails = function (plugin) {
      plugin = plugin || this;

      plugin.$megamenuElement
        .find('.megamenu-item--current')
        .first()
        .parents('.megamenu-item:not(.megamenu-item--no-current)')
        .addClass('megamenu-item--active-trail');
    }

    /**
     * Process link event.
     *
     * @param e
     * @param link
     * @param plugin
     * @param follow
     *
     * @returns {boolean}
     */
    this.processLinkEvent = function (e, link, plugin, follow) {
      plugin = plugin || this;
      follow = follow || false;

      var $link = $(link);
      // Links has to be a element.
      if (!$link.is('a')) {
        return;
      }
      var href = $link.attr('href');
      var scrollTarget = $link.data('megamenu-scrollto-anchor');

      // Scroll target exists.
      if (scrollTarget) {
        var $targetElement = $(scrollTarget).first();

        if ($targetElement.length > 0) {
          e.preventDefault();
          var $li = $link.parent('.megamenu-item');
          // Add current classes to clicked item.
          $li
            .addClass('megamenu-item--current')
            .addClass('megamenu-item--active-trail');
          // Remove current classes from other items.
          $li.siblings()
            .removeClass('megamenu-item--current')
            .removeClass('megamenu-item--active-trail');

          var animationFinished = false;
          $('html, body').animate({
              scrollTop: $targetElement.offset().top - plugin.settings.scrollto_offset
            }, plugin.settings.scrollto_duration, 'swing', function() {
              // Scroll is finished.
              if (!animationFinished) {
                animationFinished = true;
                plugin.closeSubmenu($link.closest('.megamenu-item--level-0'), plugin);
                if (plugin.settings.collapse_after_scroll && !plugin.$megamenuElement.hasClass('megamenu--responsive--no-collapse')) {
                  plugin.toggleMenuCollapse('toggle', false, plugin);
                }
              }
            }
          );

          // Don't follow links if there's scroll target.
          return false;
        }
        // Target doesn't exist, redirect with hash.
        else {
          if (href && href.indexOf('#') == -1) {
            if (scrollTarget.indexOf('#') == -1) {
              scrollTarget = '#' + scrollTarget;
            }
            window.location = href + scrollTarget;
            e.preventDefault();
          }
        }
      }

      if (href) {
        if (follow && e.isDefaultPrevented()) {
          if ($link.attr('target') == '_blank') {
            window.open(href, '_blank');
          }
          else {
            window.location = href;
          }
        }
      }
      else {
        e.preventDefault();
      }
    }

    //--------------------------------------------------------------------------
    // MOUSE
    //--------------------------------------------------------------------------

    /**
     * Process mouse click.
     *
     * @param e
     * @param target
     * @param plugin
     */
    this.processMouseClick = function (e, target, plugin) {
      plugin = plugin || this;
      var $target	= $(target);

      if ($target.data('megamenu-disable-click')) {
        return;
      }

      var $item = $target.parent('.megamenu-item');

      if ($item.length) {
        // Item is already opened.
        if ($item.hasClass('megamenu-active')) {
          if ($target.is('a')) {
            plugin.processLinkEvent(e, target, plugin);
          }

          if (!$item.hasClass('megamenu-tabs__tab')) {
            plugin.closeSubmenu($item);
          }
        }
        // Open item, close other submenus.
        else {
          if ($item.hasClass('megamenu-item--has-submenu-drop')) {
            e.preventDefault();
            plugin.closeSubmenuWithoutEffects($item.siblings('.megamenu-active'));
            plugin.openSubmenu($item, plugin);
          }
        }
      }
    }

    /**
     * Process mouse intent.
     *
     * @param e
     * @param item
     * @param plugin
     */
    this.processMouseIntent = function (e, item, plugin) {
      plugin = plugin || this;
      var $item = $(item);

      // Disable timer.
      if ($item.data('mouse_intent_timer')) {
        $item.data('mouse_intent_timer', clearTimeout($item.data('mouse_intent_timer')));
      }

      // Triggered by touch event, do nothing.
      var $anchor = $item.find('.megamenu-anchor');
      if ($anchor.data('megamenu-disable-hover')) {
        e.preventDefault();
        e.stopPropagation();
        return;
      }

      // Set variables, events and data.
      plugin.previousMousePosX = e.pageX;
      plugin.previousMousePosY = e.pageY;
      $item.on('mousemove.mouse_intent', plugin.traceMouse);
      $item.data('mouse_intent_timer', setTimeout(function() {
          plugin.compareMousePositions(e, $item, plugin.processMouseIntentSuccess, plugin);
        },
        plugin.settings.intent_interval
      ));

      $item.on('mouseleave.mouse_intent_none', function() {
        $(this).data('mouse_intent_timer', clearTimeout($(this).data('mouse_intent_timer')));
        $item.data('mouse_intent_state', 0);
        $item.off('mouseleave.mouse_intent_none');
        // Triggered by touch event, do nothing.
        if ($anchor.data('megamenu-disable-hover')) {
          e.preventDefault();
          e.stopPropagation();
          return;
        }
        // Close submenu.
        plugin.closeSubmenu($item, plugin);
      });
    }

    /**
     * Process mouse intent success.
     *
     * @param e
     * @param $item
     * @param plugin
     */
    this.processMouseIntentSuccess = function (e, $item, plugin) {
      plugin = plugin || this;

      // Disable quick leave event.
      $item.off('mouseleave.mouse_intent_none');

      // Triggered by touch event, do nothing.
      var $anchor = $item.find('.megamenu-anchor');
      if ($anchor.data('megamenu-disable-hover')) {
        e.preventDefault();
        e.stopPropagation();
        return;
      }

      // Clear event flag.
      $anchor.data('megamenu-disable-hover', false);
      // Trigger submenu.
      plugin.triggerSubmenu(e, $item, plugin);

      // Init mouseleave, except for tabs.
      if (!$item.hasClass('megamenu-tabs__tab')) {
        $item.on('mouseleave.mouse_intent', function(e) {
          plugin.processMouseIntentLeave(e, this, plugin);
        });
      }
    }

    /**
     * Process mouse intent leave.
     *
     * @param e
     * @param item
     * @param plugin
     */
    this.processMouseIntentLeave = function (e, item, plugin) {
      plugin = plugin || this;
      var $item = $(item);

      // Disable timer.
      if ($item.data('mouse_intent_timer')) {
        $item.data('mouse_intent_timer', clearTimeout($item.data('mouse_intent_timer')));
      }

      // Disable event.
      $item.off('mousemove.mouse_intent', plugin.traceMouse);

      if ($item.data('mouse_intent_state') == 1) {
        $item.data('mouse_intent_timer', setTimeout(
          function() {
            plugin.delayMouseLeave(e, $item, plugin.processMouseIntentLeaveSuccess, plugin);
          },
          plugin.settings.intent_delay
        ));
      }
    }

    /**
     * Process mouse intent leave success.
     *
     * @param e
     * @param $el
     * @param plugin
     */
    this.processMouseIntentLeaveSuccess = function (e, $el, plugin) {
      plugin = plugin || this;

      $el.off('mouseleave.mouse_intent');
      if ($el.find('> .megamenu-anchor').data('megamenu-disable-hover')) {
        return;
      }

      // Close submenu.
      plugin.closeSubmenu($el, plugin);
    }

    /**
     * Delay mouse leave.
     *
     * @param e
     * @param $el
     * @param func
     * @param plugin
     *
     * @returns {*}
     */
    this.delayMouseLeave = function (e, $el, func, plugin) {
      plugin = plugin || this;

      // Disable timer and event.
      $el.data('mouse_intent_timer', clearTimeout($el.data('mouse_intent_timer')));
      $el.data('mouse_intent_state', 0);

      return func.apply($el,[e, $el, plugin]);
    }

    /**
     * Trace mouse.
     *
     * @param e
     * @param plugin
     */
    this.traceMouse = function (e, plugin) {
      plugin = plugin || this;

      // Set positions.
      plugin.currentMousePosX = e.pageX;
      plugin.currentMousePosY = e.pageY;
    }

    /**
     * Compare mouse positions.
     *
     * @param e
     * @param $el
     * @param func
     * @param plugin
     *
     * @returns {*}
     */
    this.compareMousePositions = function (e, $el, func, plugin) {
      plugin = plugin || this;

      // Disable timer.
      $el.data('mouse_intent_timer', clearTimeout($el.data('mouse_intent_timer')));

      // Compare mouse positions to see if they've crossed the threshold
      var diffX = Math.abs(plugin.previousMousePosX - plugin.currentMousePosX);
      var diffY = Math.abs(plugin.previousMousePosY - plugin.currentMousePosY);
      var diffTotal = diffX + diffY;

      if (diffTotal < plugin.settings.intent_threshold) {
        $el.off('mousemove.mouse_intent', plugin.track);
        // hoverIntent state is set to true in order mouseOut is able to be called.
        $el.data('mouse_intent_state', 1);
        return func.apply($el, [e,$el,plugin]);
      }
      else {
        plugin.previousMousePosX = plugin.currentMousePosX;
        plugin.previousMousePosY = plugin.currentMousePosY;

        // Avoid JS timer bugs by using self-calling timeout.
        $el.data('mouse_intent_timer', setTimeout(function() {
            plugin.compareMousePositions(e, $el, func, plugin);
          }, plugin.settings.intent_interval
        ));
      }
    }

    /**
     * Process mouseover.
     *
     * @param e
     * @param target
     * @param plugin
     */
    this.processMouseOver = function (e, target, plugin) {
      plugin = plugin || this;
      if (plugin.eventsDisabled) {
        return;
      }

      var $target = $(target);
      $target.data('megamenu-disable-touch', true);
      setTimeout(function() {
        $target.data('megamenu-disable-touch', false);
      }, 1000);

      var $item = $target.parent('.megamenu-item');
      if ($item.length) {
        if (!$item.hasClass('megamenu-active')) {
          plugin.triggerSubmenu(e, $item, plugin);

          // Init mouseleave, except for tabs.
          if (!$item.hasClass('megamenu-tabs__tab')) {
            $item.on('mouseleave.megamenu-submenu-toggle', function(e) {
              plugin.processMouseLeave(e, this, plugin);
            });
          }
        }
      }
    }

    /**
     * Process mouse leave event.
     *
     * @param e
     * @param li
     * @param plugin
     */
    this.processMouseLeave = function (e, li, plugin) {
      plugin = plugin || this;

      // Unbind mouseout event, because it'll be rebind on next mouseover event.
      $(li).off('mouseleave.megamenu-submenu-toggle');
      // Close submenu.
      plugin.closeSubmenu($(li));
    }

    //--------------------------------------------------------------------------
    // TOUCH
    //--------------------------------------------------------------------------

    /**
     * Init touch.
     *
     * @param plugin
     */
    this.initTouch = function (plugin) {
      plugin = plugin || this;

      if (('ontouchstart' in window) || (navigator.maxTouchPoints > 0) || (navigator.msMaxTouchPoints > 0)) {
        plugin.touchEnabled = true;
        plugin.$megamenuElement.addClass('megamenu--touch');
      }
      else {
        plugin.touchEnabled = false;
        plugin.$megamenuElement.addClass('megamenu--no-touch');
      }

      if (window.navigator.pointerEnabled) {
        plugin.touchStart = 'pointerdown';
        plugin.touchEnd = 'pointerup';
        plugin.touchMove = 'pointermove';
        plugin.suppressClicks = true;
        plugin.toggleEvent = plugin.touchEnd;
      }
      else if (window.navigator.msPointerEnabled) {
        plugin.touchStart = 'MSPointerDown';
        plugin.touchEnd = 'MSPointerUp';
        plugin.touchMove = 'MSPointerMove';
        plugin.suppressClicks = true;
        plugin.toggleEvent = plugin.touchEnd;
      }
    }

    /**
     * Init touch off close.
     *
     * @param plugin
     */
    this.initTouchOffClose = function (plugin) {
      plugin = plugin || this;

      if (!plugin.settings.touch_off_close) {
        return;
      }

      $(document).on(plugin.touchStart + '.megamenu_touchoff', function(e) {
        plugin.processTouchOffCloseStart(e, plugin);
      });
      $(document).on(plugin.touchEnd + '.megamenu_touchoff', function(e) {
        plugin.processTouchOffClose(e, 'touch', plugin);
      });
      // Mouseup event is used for Firefox browser instead of click event.
      if (!plugin.suppressClicks) {
        $(document).on('mouseup.megamenu_clickoff', function(e) {
          plugin.processTouchOffClose(e, 'click', plugin);
        });
      }
    }

    /**
     * Process touch interactions.
     *
     * @param e
     * @param target
     * @param plugin
     */
    this.processTouchEvents = function (e, target, plugin) {
      plugin = plugin || this;

      e.stopPropagation();

      // Disable transition for Windows touch.
      if (e.type.indexOf('pointer') >= 0) {
        plugin.disableTransitionEffects();
      }

      var $target = $(target);
      // Disable hover intent out for touch.
      $target.parent().off('mouseleave.mouse_intent_none');

      // Set touch events.
      $target.on(plugin.touchEnd, function(e) {
        plugin.processTap(e, this, plugin);
      });
      $target.on(plugin.touchMove, function(e) {
        plugin.disableInteractionOnScroll(e, this, plugin);
      });

      // Standard event points
      if (e.originalEvent.touches) {
        $target.data('megamenu-startX', e.originalEvent.touches[0].clientX);
        $target.data('megamenu-startY', e.originalEvent.touches[0].clientY);
      }
      // Microsoft event points
      else if (e.originalEvent.clientY) {
        $target.data('megamenu-startX', e.originalEvent.clientX);
        $target.data('megamenu-startY', e.originalEvent.clientY);
      }
    }

    /**
     * Process touchoff close start.
     *
     * @param e
     * @param plugin
     */
    this.processTouchOffCloseStart = function (e, plugin) {
      plugin = plugin || this;

      plugin.touchOffCloseStart = $(window).scrollTop();
    }

    /**
     * Process touchoff close.
     *
     * @param e
     * @param eventType
     * @param plugin
     */
    this.processTouchOffClose = function (e, eventType, plugin) {
      plugin = plugin || this;

      // Touch event happened outside the menu.
      if (!$(e.target).closest('.megamenu').length) {
        // Event is click or touch without scroll.
        if (eventType == 'click' || plugin.touchOffCloseStart == $(window).scrollTop()) {
          // Close all submenus.
          var result = plugin.closeAllSubmenus();
          if (result) {
            // Hovering is disabled temporarily because mouseover event can fire and reopen menu.
            plugin.eventsDisabled = true;
            window.setTimeout(function() {
                plugin.eventsDisabled = false;
              },
              plugin.settings.submenu_animation_duration
            );
            // Calling e.preventDefault() will disable firing mouseover events.
          }
        }
      }
    }

    /**
     * Disable interaction on scroll.
     *
     * @param e
     * @param target
     * @param plugin
     */
    this.disableInteractionOnScroll = function (e, target, plugin) {
      plugin = plugin || this;
      var $target = $(target);
      var diffX = 0;
      var diffY = 0;

      // Touch points are close enough so handle as a tap.
      if (e.originalEvent.touches) {
        diffX = Math.abs(e.originalEvent.touches[0].clientX - $target.data('megamenu-startX'));
        diffY = Math.abs(e.originalEvent.touches[0].clientY - $target.data('megamenu-startY'));
        if (diffX > plugin.settings.move_threshold || diffY > plugin.settings.move_threshold) {
          plugin.resetEvents($target, plugin);
        }
      }
      // Touch points are not so close so handle as scroll or move.
      else if (e.originalEvent.clientY) {
        diffX = Math.abs(e.originalEvent.clientX - $target.data('megamenu-startX'));
        diffY = Math.abs(e.originalEvent.clientY - $target.data('megamenu-startY'));
        if (diffX > plugin.settings.move_threshold || diffY > plugin.settings.move_threshold) {
          plugin.resetEvents($target, plugin);
        }
      }
    }

    /**
     * Process tap (a completed touch event).
     *
     * @param e
     * @param target
     * @param plugin
     */
    this.processTap = function (e, target, plugin) {
      e.preventDefault();
      e.stopPropagation();

      plugin = plugin || this;
      var $target = $(target);

      // Quit if this was triggered after regular hover (which happens on IE)
      // Disable any touches flagged by mouseenter
      if ($target.data('megamenu-disable-touch')) {
        e.preventDefault();
        e.stopPropagation();
      }
      else {
        var $li = $target.parent();

        // Disable normal handling of click and hover events.
        $target.data('megamenu-disable-click', true);
        $target.data('megamenu-disable-hover', true);
        setTimeout(function() {
          $target.data('megamenu-disable-click', false).data('megamenu-disable-hover', false);
        }, 1000);

        // Close other submenus first.
        plugin.closeSubmenuWithoutEffects($li.siblings('.megamenu-active'));

        // Toggle submenus if link has it.
        if ($li.hasClass('megamenu-item--has-submenu-drop')) {
          // Submenu is already open.
          if ($li.hasClass('megamenu-active')) {
            // Close tabs only on mobile.
            if (!$li.hasClass('megamenu-tabs__tab') || window.innerWidth <= plugin.settings.breakpoint_primary) {
              plugin.closeSubmenu($li, plugin);
            }
            plugin.processLinkEvent(e, target, plugin, true);
          }
          // Submenu is closed.
          else {
            // Open the submenu and don't follow link.
            plugin.openSubmenu($li, plugin);
          }
        }
        // Link doesn't have submenu.
        else {
          plugin.processLinkEvent(e, target, plugin, true);
        }
      }

      // Reset events.
      $target.data('megamenu-disable-touch', false);
      plugin.resetEvents($target, plugin);
    }

    //--------------------------------------------------------------------------
    // MENU
    //--------------------------------------------------------------------------

    /**
     * Toggle menu collapse.
     *
     * @param action
     * @param toggle
     * @param plugin
     */
    this.toggleMenuCollapse = function (action, toggle, plugin) {
      plugin = plugin || this;
      action = action || 'toggle';
      toggle = toggle || '.megamenu-resposive-toggle';
      var $toggle;

      // Object.
      if (typeof toggle == 'object') {
        $toggle = $(toggle);
      }
      // Selector string.
      else {
        $toggle = $(toggle + '[data-megamenu-anchor="' + plugin.$megamenuElement.attr('id') + '"]');
      }

      // Toggle.
      if (action == 'toggle') {
        if (plugin.$megamenuElement.hasClass('megamenu--responsive--collapse')) {
          action = 'open';
        }
        else {
          action = 'close';
        }
      }
      // Open/close
      if (action == 'open') {
        plugin.$megamenuElement.removeClass('megamenu--responsive--collapse');
        $toggle.toggleClass('megamenu-responsive-toggle--open');
      }
      else {
        plugin.$megamenuElement.addClass('megamenu--responsive--collapse');
        $toggle.toggleClass('megamenu-responsive-toggle--open');
      }

      if (plugin.transitions && !plugin.$megamenuElement.hasClass('megamenu--responsive--no-collapse')) {
        plugin.$megamenuElement.addClass('megamenu--in-transition');
        plugin.$megamenuElement.on(plugin.transitionEnd + '_toggle_megamenu', function() {
          plugin.$megamenuElement.removeClass('megamenu--in-transition');
          plugin.$megamenuElement.off(plugin.transitionEnd  + '_toggle_megamenu');
        });
      }
    }

    //--------------------------------------------------------------------------
    // SUBMENU
    //--------------------------------------------------------------------------

    /**
     * Init submenu positioning.
     *
     * @param plugin
     */
    this.initSubmenuPosition = function (plugin) {
      plugin = plugin || this;

      plugin.positionSubmenus();
      $(window).megaMenuSmartResize(function() {
        plugin.positionSubmenus();
      });

      if (plugin.settings.reposition_on_load) {
        $(window).on('load', function() {
          plugin.positionSubmenus();
        });
      }
    }

    /**
     * Init submenu toggle touch events.
     *
     * @param plugin
     */
    this.initSubmenuToggleTouchEvents = function (plugin) {
      plugin = plugin || this;

      // Touch events are disabled, do nothing.
      if (!plugin.settings.touch_events) {
        return;
      }

      plugin.$megamenuElement.on(plugin.touchStart, '.megamenu-anchor:not(.shiftnav-toggle)', function(e) {
        plugin.processTouchEvents(e, this, plugin);
      });

      // Disable clicks by touch.
      plugin.$megamenuElement.on('click', '.megamenu-item--has-submenu-drop > .megamenu-anchor, .megamenu-tabs__tab.megamenu-item--has-children > .megamenu-anchor', function(e) {
        if ($(this).data('megamenu-disable-click')) {
          e.preventDefault();
        }
      });
    }

    /**
     * Init submenu toggle mouse events.
     *
     * @param plugin
     */
    this.initSubmenuToggleMouseEvents = function (plugin) {
      plugin = plugin || this;

      // Mouse events are disabled, do nothing.
      if (!plugin.settings.mouse_events) {
        return;
      }

      var event = '';
      var trigger = 'hover';

      // Determine trigger type.
      if (plugin.$megamenuElement.hasClass('megamenu--trigger-click')) {
        trigger = 'click';
      }
      else if (plugin.$megamenuElement.hasClass('megamenu--trigger-hover_intent')) {
        trigger = 'hover_intent';
      }

      // Click trigger.
      if (trigger == 'click') {
        if (!plugin.suppressClicks) {
          event = 'click.megamenu-submenu-toggle';
          plugin.$megamenuElement.on(event, '.megamenu-item.megamenu-item--has-submenu-drop:not([data-megamenu-trigger]) > .megamenu-anchor', function(e) {
            plugin.processMouseClick(e , this , plugin);
          });
          plugin.$megamenuElement.on('click.megamenu-click-anchor', '.megamenu-item:not(.megamenu-item--has-submenu-drop):not([data-megamenu-trigger]) > .megamenu-anchor', function(e) {
            plugin.processLinkEvent(e, this, plugin);
          });
        }
      }
      // Hover intent trigger.
      else if (trigger == 'hover_intent') {
        event = 'mouseenter.mouse_intent';
        plugin.$megamenuElement.on(event, '.megamenu-item.megamenu-item--has-submenu-drop:not([data-megamenu-trigger])', function(e) {
          plugin.processMouseIntent(e, this, plugin);
        });
        plugin.$megamenuElement.on('click.megamenu-click-anchor', '.megamenu-item:not([data-megamenu-trigger]) > .megamenu-anchor', function(e) {
          plugin.processLinkEvent(e, this, plugin);
        });
      }
      // Hover trigger.
      else {
        event = 'mouseenter.megamenu-submenu-toggle';
        plugin.$megamenuElement.on(event, '.megamenu-item.megamenu-item--has-submenu-drop:not([data-megamenu-trigger]) > .megamenu-anchor', function(e) {
          plugin.processMouseOver(e, this, plugin);
        });
        plugin.$megamenuElement.on('click.megamenu-click-anchor', '.megamenu-item:not([data-megamenu-trigger]) > .megamenu-anchor', function(e) {
          plugin.processLinkEvent(e, this, plugin);
        });
      }

      // Trigger overrides are allowed.
      if (plugin.allowTriggerOverrides) {
        plugin.$megamenuElement.find('.megamenu-item[data-megamenu-trigger]').each(function() {
          var $li = $(this);
          trigger = $li.data('megamenu-trigger');

          // Click trigger.
          if (trigger == 'click') {
            if (!plugin.suppressClicks) {
              $li.on('click.megamenu-submenu-toggle', '.megamenu-anchor', function(e) {
                plugin.processMouseClick(e, this, plugin);
              });
            }
          }
          // Hover intent trigger.
          else if (trigger == 'hover_intent') {
            $li.on('mouseenter.mouse_intent', function(e) {
              plugin.processMouseIntent(e, this, plugin);
            });
          }
          // Hover trigger.
          else {
            $li.on('mouseenter.megamenu-submenu-toggle', '.megamenu-anchor', function(e) {
              plugin.processMouseOver(e, this, plugin);
            });
          }
        });
      }
      // Trigger overrides aren't allowed, click default tabs.
      else {
        plugin.$megamenuElement.find('.megamenu-tabs__tab').on('click.megamenu-submenu-toggle', '.megamenu-anchor', function(e) {
          plugin.processMouseClick(e, this, plugin);
        });
      }
    }

    /**
     * Position submenus.
     */
    this.positionSubmenus = function (plugin) {
      plugin = plugin || this;

      // Only for horizontal menu.
      if (plugin.orientation == 'horizontal') {
        plugin.$megamenuElement.find('.megamenu-submenu--drop.megamenu-submenu--align-center').each(function() {
          var $parent = $(this).parent('.megamenu-item');
          var parentWidth = $parent.outerWidth();
          var parentLeftEdge = $parent.offset().left;
          var $sub = $(this);
          var subWidth = $sub.outerWidth();
          var $container;

          if (plugin.$megamenuElement.hasClass('megamenu--bound')) {
            // Main menu bar
            $container = $parent.closest('.megamenu , .megamenu-submenu');
          }
          else if (plugin.$megamenuElement.hasClass('megamenu--bound--inner')) {
            // Inner menu bar
            $container = $parent.closest('.megamenu-nav , .megamenu-submenu');
          }
          else {
            var $parentSub = $parent.closest('.megamenu-submenu');

            // The closest relatively positioned element.
            if ($parentSub.length === 0) {
              $container = plugin.$megamenuElement.offsetParent();
            }
            else {
              $container = $parentSub;
            }
          }

          var containerWidth = $container.width();
          var containerLeftEdge = $container.offset().left;
          var centerLeft = (parentLeftEdge + (parentWidth / 2)) - (containerLeftEdge + (subWidth / 2));
          var left = 0;

          // Align left if submenu is left of container edge.
          if (centerLeft > 0) {
            left = centerLeft;
          }

          // Center to container/menu bar if submenu width is larger than container width.
          if (subWidth > containerWidth) {
            left = (subWidth - containerWidth) / -2;
          }
          // Align right if submenu is right of container edge.
          else if (left + subWidth > containerWidth) {
            $sub.css({'right' : 0, 'left' : 'auto'});
            left = false;
          }

          if (left !== false) {
            $sub.css('left', left);
          }
        });
      }
    }

    /**
     * Open submenu.
     *
     * @param $li
     * @param plugin
     */
    this.openSubmenu = function ($li, plugin) {
      plugin = plugin || this;

      if (!$li.hasClass('megamenu-active')) {
        $li.addClass('megamenu-active');

        if (plugin.transitions) {
          // Set transition flag.
          $li.addClass('megamenu-item--in-transition');

          $li.find('> .megamenu-submenu').on(plugin.transitionEnd + '_opensubmenu', function() {
            $li.removeClass('megamenu-item--in-transition');
            $(this).off(plugin.transitionEnd  + '_opensubmenu');
          });
        }
        // Fire all attached triggers.
        $li.trigger('megamenuOpen');
      }
    }

    /**
     * Close submenu.
     *
     * @param $li
     * @param plugin
     */
    this.closeSubmenu = function ($li, plugin) {
      plugin = plugin || this;

      // Menu is active and has submenu.
      if ($li.hasClass('megamenu-active') && $li.hasClass('megamenu-item--has-children')) {
        if (plugin.transitions) {
          // Set transition flag.
          $li.addClass('megamenu-item--in-transition');
        }

        $li.each(function() {
          var _$li = $(this);
          var _$ul = _$li.find('> ul');

          // When transition is finished, delete transition flag.
          if (plugin.transitions) {
            _$ul.on(plugin.transitionEnd + '_closesubmenu', function() {
              _$li.removeClass('megamenu-item--in-transition');
              _$ul.off(plugin.transitionEnd  + '_closesubmenu');
            });
          }
        });
      }

      // Close submenu by removing active class.
      $li.removeClass('megamenu-active');
      // Fire all triggers attached on.
      $li.trigger('megamenuClose');
    }

    /**
     * Close subs immediately (no transition).
     *
     * @param $li
     * @param plugin
     */
    this.closeSubmenuWithoutEffects = function ($li, plugin) {
      plugin = plugin || this;
      if ($li.length === 0) {
        return;
      }

      $li.addClass('megamenu-item--no-transition');
      // Remove transition flag.
      $li.removeClass('megamenu-active').removeClass('megamenu-item--in-transition');
      // Trigger realign.
      $li[0].offsetHeight;
      $li.removeClass('megamenu-item--no-transition');
      // Fire all triggers attached on.
      $li.trigger('megamenuClose');
    }

    /**
     * Close all submenus starting from the top level.
     *
     * @param plugin
     */
    this.closeAllSubmenus = function (plugin) {
      plugin = plugin || this;

      var $actives = plugin.$megamenuElement.find('.megamenu-item--level-0.megamenu-active');
      if ($actives.length) {
        plugin.closeSubmenuWithoutEffects($actives);
      }

      return $actives.length;
    }

    /**
     * Trigger submenu.
     *
     * @param e
     * @param $item
     * @param plugin
     */
    this.triggerSubmenu = function (e, $item, plugin) {
      plugin = plugin || this;

      plugin.closeSubmenuWithoutEffects($item.siblings('.megamenu-active, .megamenu-item--in-transition'));
      plugin.openSubmenu($item, plugin);
    }

    //--------------------------------------------------------------------------
    // RETRACTORS
    //--------------------------------------------------------------------------

    /**
     * Init retractors.
     *
     * @param plugin
     */
    this.initRetractors = function (plugin) {
      plugin = plugin || this;

      // Retractors are disabled, do nothing.
      if (!plugin.settings.retractors) {
        return;
      }

      plugin.$megamenuElement.on('click', '.megamenu-retractor', function(e) {
        plugin.processSubmenuRetractorEnd(e, this, plugin);
      });

      if (plugin.settings.touch_events) {
        plugin.$megamenuElement.on(plugin.touchStart, '.megamenu-retractor', function(e) {
          plugin.processSubmenuRetractorStart(e, this, plugin);
        });
      }

      // Remove mobile retractors if device doesn't support touch.
      if (plugin.settings.retractor_display_strategy == 'touch' && !plugin.touchEnabled) {
        plugin.$megamenuElement.find('.megamenu-retractor--mobile').remove();
        plugin.$megamenuElement.find('.megamenu-submenu--retractor-top')
          .removeClass('megamenu-submenu--retractor-top')
          .removeClass('megamenu-submenu--retractor-top-2');
      }
    }

    /**
     * Process submenu retractor start.
     *
     * @param e
     * @param li
     * @param plugin
     */
    this.processSubmenuRetractorStart = function (e, li, plugin) {
      e.preventDefault();
      e.stopPropagation();

      plugin = plugin || this;

      $(li).on(plugin.touchEnd, function(e) {
        plugin.processSubmenuRetractorEnd(e, this, plugin);
      });
    }

    /**
     * Process submenu retractor end.
     *
     * @param e
     * @param li
     * @param plugin
     *
     * @returns {boolean}
     */
    this.processSubmenuRetractorEnd = function (e, li, plugin) {
      e.preventDefault();
      e.stopPropagation();

      plugin = plugin || this;
      var $parentItem = $(li).closest('.megamenu-item');

      plugin.closeSubmenu($parentItem);
      $(li).off(plugin.touchEnd);

      return false;
    }

    //--------------------------------------------------------------------------
    // RESPONSIVE
    //--------------------------------------------------------------------------

    /**
     * Init responsive.
     *
     * @param plugin
     */
    this.initResponsive = function (plugin) {
      plugin = plugin || this;

      plugin.settings.responsive = plugin.$megamenuElement.hasClass('megamenu--responsive') ? true : false;

      plugin.setResponsiveClasses();
      $(window).megaMenuSmartResize(function() {
        plugin.setResponsiveClasses();
      });
    }

    /**
     * Set breakpoint classes.
     *
     * @param plugin
     */
    this.setResponsiveClasses = function (plugin) {
      plugin = plugin || this;
      var responsive_primary = false;
      var responsive_above_primary = false;
      var responsive_secondary = false;
      var $responsiveToggle = $('.megamenu-responsive-toggle');

        // Clear breakpoint classes.
      plugin.$megamenuElement.removeClass('megamenu--responsive--above-primary');
      plugin.$megamenuElement.removeClass('megamenu--responsive--primary');
      plugin.$megamenuElement.removeClass('megamenu--responsive--secondary');
      $responsiveToggle.removeClass('megamenu-responsive-toggle--above-primary');
      $responsiveToggle.removeClass('megamenu-responsive-toggle--primary');
      $responsiveToggle.removeClass('megamenu-responsive-toggle--secondary');

      // Determine breakpoint.
      if (plugin.settings.responsive && (window.innerWidth > plugin.settings.breakpoint_primary)) {
        responsive_above_primary = true;
      }
      if (plugin.settings.responsive && (window.innerWidth <= plugin.settings.breakpoint_primary)) {
        responsive_primary = true;
      }
      if (plugin.settings.responsive && (window.innerWidth <= plugin.settings.breakpoint_secondary)) {
        responsive_secondary = true;
      }

      // Set classes based on breakpoint.
      if (responsive_above_primary) {
        plugin.$megamenuElement.addClass('megamenu--responsive--above-primary');
        $responsiveToggle.addClass('megamenu-responsive-toggle--above-primary');
      }
      else if (responsive_primary) {
        plugin.$megamenuElement.addClass('megamenu--responsive--primary');
        $responsiveToggle.addClass('megamenu-responsive-toggle--primary');
        if (responsive_secondary) {
          plugin.$megamenuElement.addClass('megamenu--responsive--secondary');
          $responsiveToggle.addClass('megamenu-responsive-toggle--secondary');
        }
      }
    }

    /**
     * Init responsive toggle.
     *
     * @param plugin
     */
    this.initResponsiveToggle = function (plugin) {
      plugin = plugin || this;
      var toggleSelector = '.megamenu-responsive-toggle[data-megamenu-anchor=' + plugin.$megamenuElement.attr('id') + '], .megamenu-responsive-toggle[data-megamenu-anchor=_any_]';

      $(document).on(plugin.toggleEvent, toggleSelector, function(e) {
        plugin.processResponsiveToggle(e, this, plugin);
      });
    }

    /**
     * Process responsive toggle.
     *
     * @param e
     * @param toggle
     * @param plugin
     */
    this.processResponsiveToggle = function (e, toggle, plugin) {
      e.preventDefault();
      e.stopPropagation();

      plugin = plugin || this;

      // For some browsers both click and touch events are fired, so disable click
      // in order to prevent double fired events.
      if (e.type == 'touchend') {
        plugin.$megamenuElement.data('megamenu-disable-click', true);
        setTimeout(function() {
          plugin.$megamenuElement.data('megamenu-disable-click', false);
        }, 500);
      }
      else if (e.type == 'click' && plugin.$megamenuElement.data('megamenu-disable-click')) {
        plugin.$megamenuElement.data('megamenu-disable-click', false);
        return;
      }

      plugin.toggleMenuCollapse('toggle', toggle, plugin);
    }

    //--------------------------------------------------------------------------
    // TRANSITIONS
    //--------------------------------------------------------------------------

    /**
     * Init transitions.
     *
     * @param plugin
     */
    this.initTransitions = function (plugin) {
      plugin = plugin || this;
      plugin.transitionEnd = 'transitionend.megamenu webkitTransitionEnd.megamenu msTransitionEnd.megamenu oTransitionEnd.megamenu';

      // Transition is enabled.
      if (plugin.checkBrowserSupport('transition') && !plugin.$megamenuElement.hasClass('megamenu--transition-none')) {
        plugin.transitions = true;
      }
      // Transition is disabled.
      else {
        plugin.transitions = false;
        plugin.$megamenuElement.addClass('megamenu--no-transitions');
      }

      var userAgent = navigator.userAgent.toLowerCase();
      var windowsMobile = plugin.settings.windows_mobile = /iemobile/.test(userAgent);
      var android = plugin.settings.android = /android/.test(userAgent);
      var safari5 = (!/chrome/.test(userAgent)) && (/safari/.test(userAgent)) && (/version\/5/.test(userAgent));

      // Disable transition for android, windows and ios browsers.
      if (windowsMobile || android) {
        if ((windowsMobile) || (android && !(/chrome/.test(userAgent) || /firefox/.test(userAgent) || /opera/.test(userAgent)))) {
          plugin.settings.touch_off_close = false;
          plugin.disableTransitionEffects();

          // Touchend is not fired properly in android.
          if (!windowsMobile && android) {
            plugin.$megamenuElement
              .removeClass('megamenu--trigger-hover_intent')
              .removeClass('megamenu--trigger-hover')
              .addClass('megamenu--trigger-click');
            plugin.settings.touch_events = false;
            plugin.allowTriggerOverrides = false;
          }
        }
      }
      if (windowsMobile) {
        plugin.settings.touch_off_close = false;
        plugin.settings.accessible = false;
        plugin.settings.mouse_events = false;
      }
      if (safari5) {
        plugin.disableTransitionEffects();
      }
    }

    /**
     * Disable transitions.
     *
     * @param plugin
     */
    this.disableTransitionEffects = function (plugin) {
      plugin = plugin || this;
      plugin.transitions = false;

      plugin.$megamenuElement
        .removeClass('megamenu--transition-slide')
        .removeClass('megamenu--transition-fade')
        .removeClass('megamenu--transition-shift')
        .addClass('megamenu--no-transitions')
        .addClass('megamenu--transition-none');
    }

    //--------------------------------------------------------------------------
    // ACCESSIBILITY
    //--------------------------------------------------------------------------

    /**
     * Init accessibility.
     *
     * @param plugin
     */
    this.initAccessibility = function (plugin) {
      plugin = plugin || this;
      // Selects elements which the user can focus via tabbing.
      var tabbableElements = '.megamenu-anchor, a, input, select, textarea';

      plugin.$megamenuElement.addClass('megamenu--accessible');
      plugin.$currentFocus = false;
      plugin.mouseDown = false;

      // Focus event.
      plugin.$megamenuElement.on('focus', tabbableElements, function() {
        if (!plugin.mouseDown) {
          var $target = $(this);

          plugin.$currentFocus = $target;
          // li parent of a element.
          var $item = $target.parent('.megamenu-item');

          if ($item.length) {
            // Close submenus for top level items.
            if ($item.is('.megamenu-item--level-0')) {
              plugin.closeAllSubmenus();
            }

            // Open submenu for items with submenus.
            if ($item.is('.megamenu-item--has-submenu-drop')) {
              // Delay a little bit in order to use tab easier.
              setTimeout(function() {
                if (!$target.is(':focus')) {
                  return;
                }

                // Close submenus of all siblings.
                $item.siblings('.megamenu-item--has-submenu-drop').each(function() {
                  plugin.closeSubmenu($(this), plugin);
                });

                // Open submenu of this menu item.
                plugin.openSubmenu($item, plugin);
              }, 500);
            }

            $target.on('blur.megamenu', tabbableElements, function(e) {
              if (!plugin.mouseDown) {
                plugin.$currentFocus = false;
                $(this).off('blur.megamenu');

                // Assume the menu focus is left if new focus isn't set for 500ms.
                setTimeout(function() {
                  if (!plugin.$currentFocus) {
                    plugin.closeAllSubmenus();
                  }
                }, 500);
              }
              plugin.mouseDown = false;
            });
          }
        }

        plugin.mouseDown = false;
      });

      // Mousedown event (focus event above will be ignored if focus is result of mouse).
      plugin.$megamenuElement.on('mousedown', function(e) {
        plugin.mouseDown = true;
        setTimeout(function() {
          plugin.mouseDown = false;
        }, 100);
      });
    }

    /**
     * Init tab accessibility.
     *
     * @param plugin
     */
    this.initTabAccessibility = function (plugin) {
      plugin = plugin || this;
      if (!plugin.settings.accessible) {
        return;
      }

      // Initialize
      $('body').on('keydown.megamenu', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode == 9) {
          $('body').off('keydown.megamenu');
          plugin.initializeAccessibility();
        }
      });
    }

    //--------------------------------------------------------------------------
    // TABS
    //--------------------------------------------------------------------------

    /**
     * Init tabs.
     *
     * @param plugin
     */
    this.initTabs = function (plugin) {
      plugin = plugin || this;
      var responsive = plugin.settings.responsive && (window.innerWidth <= plugin.settings.breakpoint_primary) ? true : false;

      plugin.$tabBlocks = plugin.$megamenuElement.find('.megamenu-tabs');

      plugin.$tabBlocks = $(plugin.$tabBlocks.get().reverse());

      // Handle tabs after all images are loaded.
      $(window).on('load', function() {
        plugin.setTabsSize();
      });

      // Handle tabs after window is resized.
      plugin.windowWidth = window.innerWidth;
      $(window).megaMenuSmartResize(function() {
        plugin.oldWindowWidth = plugin.windowWidth;
        plugin.windowWidth = window.innerWidth;
        // Check if width is changed.
        if (plugin.windowWidth != plugin.oldWindowWidth) {
          plugin.removeTabSizes(plugin);
          plugin.setTabsSize();
          plugin.processActiveTabs(plugin);
        }
      });

      // Handle tabs after submenu is opened for the first time.
      plugin.$megamenuElement.find('.megamenu-item--level-0.megamenu-item--has-submenu-drop').on('megamenuOpen.sizeTabs', function() {
        $(this).off('megamenuOpen.sizeTabs');
        plugin.setTabsSize();
      });

      if (!responsive) {
        plugin.initActiveTab(plugin);
      }
    }

    /**
     * Process active tabs.
     *
     * @param plugin
     */
    this.processActiveTabs = function (plugin) {
      plugin = plugin || this;

      if (window.innerWidth <= plugin.settings.breakpoint_primary) {
        // Close all tabs.
        plugin.$tabBlocks.find('.megamenu-tabs__tab.megamenu-active').removeClass('megamenu-active');
      }
      else {
        plugin.initActiveTab(plugin);
      }
    }

    /**
     * Init active tab.
     *
     * @param plugin
     */
    this.initActiveTab = function (plugin) {
      plugin = plugin || this;

      plugin.$megamenuElement.find('.megamenu-tabs--show-default > .megamenu-tabs__group').each(function() {
        // Activate the first tab in case there are no any active.
        if ($(this).find('> .megamenu-tabs__tab.megamenu-active').length === 0) {
          plugin.openSubmenu($(this).find('> .megamenu-tabs__tab').first(), plugin);
        }
      });
    }

    /**
     * Remove tab sizes.
     *
     * @param plugin
     */
    this.removeTabSizes = function (plugin) {
      plugin = plugin || this;

      // Remove tab size.
      plugin.$megamenuElement
        .find('.megamenu-submenu , .megamenu-tabs , .megamenu-tabs__pane , .megamenu-tabs__group')
        .css('min-height', '');
    }

    /**
     * Set tabs size.
     *
     * @param plugin
     */
    this.setTabsSize = function (plugin) {
      plugin = plugin || this;

      var responsive = plugin.settings.responsive && (window.innerWidth <= plugin.settings.breakpoint_primary) ? true : false;

      // Don't size tabs on responsive.
      if (responsive) {
        return;
      }

      plugin.initActiveTab(plugin);

      plugin.$tabBlocks.each(function() {
        var isStacked = false;
        var maxHeight = 0;
        var $parentsTree;
        var $tabPanels;
        var $tabsGroup;

        if (!responsive && ($(this).hasClass('megamenu-tabs--layout-top') || $(this).hasClass('megamenu-tabs--layout-bottom'))) {
          isStacked = true;
        }

        if (responsive) {
          $parentsTree = $(this).parentsUntil('.megamenu').add($(this).parents('.megamenu'));
        }
        else {
          $parentsTree = $(this).parentsUntil('.megamenu-item--level-0');
        }
        $parentsTree.addClass('megamenu-test-dimensions');

        $tabPanels = $(this).find(' > .megamenu-tabs__group > .megamenu-tabs__tab > .megamenu-tabs__pane');
        // Get maximum panel height for this tab group (immediate panels only, not nested)
        $tabPanels.each(function() {
          $(this).addClass('megamenu-test-dimensions');
          if ($(this).outerHeight() > maxHeight) {
            maxHeight = $(this).outerHeight();
          }
          $(this).removeClass('megamenu-test-dimensions');
        });

        $tabsGroup = $(this).find('> .megamenu-tabs__group');

        // Left or right layout - set min-height on tabs group.
        if (!isStacked) {
          if ($tabsGroup.outerHeight() > maxHeight) {
            maxHeight = $(this).outerHeight();
          }
          $tabsGroup.css('min-height', maxHeight);
        }
        // Top or bottom layout - set height for entire block.
        else {
          $(this).css('min-height', maxHeight + $tabsGroup.outerHeight());
        }

        // Responsive - set submenu.
        if (responsive) {
          $(this).closest('.megamenu-submenu--drop').css('min-height', maxHeight);
          $tabPanels.css('min-height', false);
        }
        // Desktop - set all panels to max height.
        else {
          var $sub = $(this).closest('.megamenu-submenu--drop');
          $sub.css('min-height', false);
          $tabPanels.css('min-height', maxHeight);
        }

        $parentsTree.removeClass('megamenu-test-dimensions');
      });
    }

    //--------------------------------------------------------------------------
    // IMAGES
    //--------------------------------------------------------------------------

    /**
     * Init image lazy load.
     *
     * @param plugin
     */
    this.initImageLazyLoad = function (plugin) {
      plugin = plugin || this;

      $('.megamenu-item--level-0').one('megamenuOpen', function() {
        $(this).find('.megamenu-image--lazy-load').each(function() {
          // Add srcset and sizes for responsive images.
          if ($(this).data('srcset')) {
            $(this)
              .attr('srcset', $(this).data('srcset'))
              .attr('sizes', $(this).data('sizes'))
          }
          // Set src attribute for all images.
          $(this)
            .attr('src', $(this).data('src'))
            .removeClass('megamenu-image--lazy-load');
        });
        setTimeout(function() {
          plugin.removeTabSizes();
          plugin.setTabsSize();
        }, 300);
      });
    }

    //--------------------------------------------------------------------------
    // INIT
    //--------------------------------------------------------------------------

    this.initMegaMenuData();
    this.initMegaMenuSettings(options);
    this.initResponsive();
    this.initTouch();
    this.initTransitions();
    this.alignMenuItemsRight();
    this.startMegaMenu();
    this.removeJsConficts();
    this.initSubmenuToggleTouchEvents();
    this.initSubmenuToggleMouseEvents();
    this.initRetractors();
    this.initResponsiveToggle();
    this.initTouchOffClose();
    this.initTabs();
    this.initSubmenuPosition();
    this.initActiveTrails();
    this.initTabAccessibility();
    this.initImageLazyLoad();

  }

  /**
   * Main function to be called for initialization megamenu over an element.
   *
   * @param options
   *
   * @returns {*}
   */
  $.fn.megamenu = function(options) {
    var args = arguments;

    if (options === undefined || typeof options === 'object') {
      return this.each(function() {
        if (!$.data(this, "plugin_megamenu")) {
          $.data(this, "plugin_megamenu" , new MegaMenu(this, options));
        }
      });
    }
    else if (typeof options === 'string' && options !== 'init' && options[0] !== '_') {
      var result;

      this.each(function() {
        var pluginInstance = $.data(this, 'plugin_megamenu');

        // Check if there's already an instance of plugin.
        if (pluginInstance instanceof MegaMenu && typeof instance[options] === 'function') {
          // Call and cache method of plugin instance with arguments.
          result = pluginInstance[options].apply(pluginInstance, Array.prototype.slice.call(args, 1));
        }

        // Plugin instances can be destroyed using destroy method.
        if (options === 'destroy') {
          $.data(this, 'plugin_megamenu', null);
        }
      });

      // Return cached value or this for the sake of chain logic.
      return result !== undefined ? result : this;
    }
  };

})(jQuery, Drupal, drupalSettings, window, document);
