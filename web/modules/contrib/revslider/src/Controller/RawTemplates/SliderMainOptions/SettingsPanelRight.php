<?php
/**
 * Created by FsFlex.
 * User: VH
 * Date: 7/31/2017
 * Time: 9:02 AM
 */

namespace Drupal\revslider\Controller\RawTemplates\SliderMainOptions;


use Drupal\revslider\Helper\RevSliderFunctions;
use Drupal\revslider\Helper\RevSliderGlobals;
use Drupal\revslider\Helper\TPColorpicker;
use Drupal\revslider\Model\Operations;

class SettingsPanelRight
{
    public function getTemplate(array $args)
    {
        extract($args);
        ob_start(); ?>
        <div class="settings_panel_right">
            <script type="text/javascript">
                function drawToolBarPreview() {

                    var tslideprev = jQuery('.toolbar-sliderpreview'),
                        tslider = jQuery('.toolbar-slider'),
                        tslider_image = jQuery('.toolbar-slider-image'),
                        tprogress = jQuery('.toolbar-progressbar'),
                        tdot = jQuery('.toolbar-dottedoverlay'),
                        tthumbs = jQuery('.toolbar-navigation-thumbs'),
                        ttabs = jQuery('.toolbar-navigation-tabs'),
                        tbuls = jQuery('.toolbar-navigation-bullets'),
                        tla = jQuery('.toolbar-navigation-left'),
                        tra = jQuery('.toolbar-navigation-right');


                    // DRAW SHADOWS
                    jQuery('.shadowTypes').css({display: "none"});
                    tslideprev.removeClass("tp-shadow1").removeClass("tp-shadow2").removeClass("tp-shadow3").removeClass("tp-shadow4").removeClass("tp-shadow5").removeClass("tp-shadow6");

                    // MAKE ddd_IF NEEDED
                    if (jQuery('#ddd_parallax').attr('checked') && jQuery('#use_parallax').attr('checked')) {
                        punchgs.TweenLite.to(tslideprev, 0.5, {
                            transformPerspective: 800,
                            rotationY: 30,
                            rotationX: 10,
                            scale: 0.8
                        });
                        if (jQuery('#ddd_parallax_shadow').attr('checked')) {
                            tslideprev.css({boxShadow: "0 45px 100px rgba(0, 0, 0, 0.4)"})
                        } else {
                            tslideprev.css({boxShadow: "none"})
                        }
                    } else {
                        punchgs.TweenLite.to(tslideprev, 0.5, {
                            transformPerspective: 800,
                            rotationY: 0,
                            rotationX: 0,
                            scale: 1
                        });
                        tslideprev.addClass('tp-shadow' + jQuery('#shadow_type').val());
                        tslideprev.css({boxShadow: "none"})
                    }


                    // DRAW PADDING
                    tslideprev.css({padding: jQuery('#padding').val() + "px"});

                    // DRAWING BACKGROUND IMAGE OR COLOR
                    if (jQuery('#show_background_image').attr("checked") === "checked")	// DRAWING BACKGROUND IMAGE
                        tslider.css({
                            background: "url(" + jQuery('#background_image').val() + ")",
                            backgroundSize: jQuery('#bg_fit').val(),
                            backgroundPosition: jQuery('#bg_repeat').val(),
                            backgroundRepeat: jQuery('#bg_position').val(),
                        });
                    else
                        tslider.css({background: window.RevColor.get(jQuery('#background_color').val())});	// DRAW BACKGROUND COLOR


                    // DRAWING PROGRESS BAR
                    var progope = parseInt(jQuery('#progress_opa').val(), 0),
                        progheight = parseInt(jQuery('#progress_height').val(), 0);

                    progope = jQuery.isNumeric(progope) ? progope / 100 : 0.15;
                    progheight = jQuery.isNumeric(progheight) ? progheight : 5;

                    switch (jQuery('#show_timerbar').val()) {
                        case "top":
                            punchgs.TweenLite.set(tprogress, {
                                background: window.RevColor.get(jQuery('#progressbar_color').val()),
                                top: "0px",
                                bottom: "auto",
                                height: progheight + "px",
                                opacity: progope
                            });
                            break;
                        case "bottom":
                            punchgs.TweenLite.set(tprogress, {
                                background: window.RevColor.get(jQuery('#progressbar_color').val()),
                                bottom: "0px",
                                top: "auto",
                                height: progheight + "px",
                                opacity: progope
                            });
                            break;
                    }
                    if (jQuery('#enable_progressbar').attr('checked') === "checked")
                        punchgs.TweenLite.set(tprogress, {display: "block"});
                    else
                        punchgs.TweenLite.set(tprogress, {display: "none"});


                    function removeClasses(obj, cs) {
                        var classes = cs.split(",");
                        if (classes)
                            jQuery.each(classes, function (index, c) {
                                obj.removeClass("tbn-" + c);
                            });
                    }

                    jQuery('.toolbar-sliderpreview').removeClass("outer-left").removeClass("outer-right").removeClass("outer-top").removeClass("outer-bottom").removeClass("inner");

                    // SHOW / HIDE ARROWS
                    if (jQuery('#enable_arrows').attr("checked") !== "checked") {
                        tla.hide();
                        tra.hide();
                    } else {
                        tla.show();
                        tra.show();
                        removeClasses(tla, "left,right,center,top,bottom,middle");
                        removeClasses(tra, "left,right,center,top,bottom,middle");

                        // LEFT ARROW
                        var hor = jQuery('#leftarrow_align_hor option:selected').val(),
                            ver = jQuery('#leftarrow_align_vert option:selected').val();
                        ver = ver === "center" ? "middle" : ver;
                        tla.addClass("tbn-" + hor);
                        tla.addClass("tbn-" + ver);
                        var ml = Math.ceil(parseInt(jQuery('#leftarrow_offset_hor').val(), 0) / 4),
                            mt = Math.ceil(parseInt(jQuery('#leftarrow_offset_vert').val(), 0) / 4);

                        if (hor === "right")
                            tla.css({marginRight: ml + "px", marginLeft: "0px"});
                        else
                            tla.css({marginRight: "0px", marginLeft: ml + "px"});

                        if (ver === "bottom")
                            tla.css({marginBottom: mt + "px", marginTop: "0px"});
                        else
                            tla.css({marginBottom: "0px", marginTop: mt + "px"});


                        // RIGHT ARROW
                        hor = jQuery('#rightarrow_align_hor option:selected').val();
                        ver = jQuery('#rightarrow_align_vert option:selected').val();
                        ver = ver === "center" ? "middle" : ver;
                        tra.addClass("tbn-" + hor);
                        tra.addClass("tbn-" + ver);
                        ml = Math.ceil(parseInt(jQuery('#rightarrow_offset_hor').val(), 0) / 4),
                            mt = Math.ceil(parseInt(jQuery('#rightarrow_offset_vert').val(), 0) / 4);
                        if (hor === "right")
                            tra.css({marginRight: ml + "px", marginLeft: "0px"});
                        else
                            tra.css({marginRight: "0px", marginLeft: ml + "px"});

                        if (ver === "bottom")
                            tra.css({marginBottom: mt + "px", marginTop: "0px"});
                        else
                            tra.css({marginBottom: "0px", marginTop: mt + "px"});

                    }


                    // SHOW HIDE BULLETS
                    if (jQuery('#enable_bullets').attr("checked") !== "checked") {
                        tbuls.hide();
                    } else {
                        tbuls.show();
                        removeClasses(tbuls, "left,right,center,top,bottom,middle,vertical,horizontal,inner,outer-left,outer-right,outer-top,outer-bottom");

                        hor = jQuery('#bullets_align_hor option:selected').val();
                        ver = jQuery('#bullets_align_vert option:selected').val();
                        ver = ver === "center" ? "middle" : ver;
                        tbuls.addClass("tbn-" + hor);
                        tbuls.addClass("tbn-" + ver);

                        ml = Math.ceil(parseInt(jQuery('#bullets_offset_hor').val(), 0) / 4),
                            mt = Math.ceil(parseInt(jQuery('#bullets_offset_vert').val(), 0) / 4);
                        if (hor === "right")
                            tbuls.css({marginRight: ml + "px", marginLeft: "0px"});
                        else
                            tbuls.css({marginRight: "0px", marginLeft: ml + "px"});

                        if (ver === "bottom")
                            tbuls.css({marginBottom: mt + "px", marginTop: "0px"});
                        else
                            tbuls.css({marginBottom: "0px", marginTop: mt + "px"});

                        tbuls.addClass("tbn-" + jQuery('#bullets_direction option:selected').val());
                    }

                    // SHOW HIDE THUMBNAILS
                    if (jQuery('#enable_thumbnails').attr("checked") !== "checked") {
                        tthumbs.hide();
                    } else {
                        tthumbs.show();
                        removeClasses(tthumbs, "left,right,center,top,bottom,middle,vertical,horizontal,inner,outer,spanned,outer-left,outer-right,outer-top,outer-bottom");
                        tthumbs.addClass("tbn-" + jQuery('#thumbnails_align_hor option:selected').val());
                        var v = jQuery('#thumbnails_align_vert option:selected').val() === "center" ? "middle" : jQuery('#thumbnails_align_vert option:selected').val();
                        tthumbs.addClass("tbn-" + v);
                        tthumbs.addClass("tbn-" + jQuery('#thumbnail_direction option:selected').val());
                        if (jQuery('#span_thumbnails_wrapper').attr("checked") === "checked")
                            tthumbs.addClass("tbn-spanned");
                        jQuery('.toolbar-navigation-thumbs-bg').css({background: window.RevColor.get(jQuery('#thumbnails_wrapper_color').val())});

                        jQuery('.toolbar-sliderpreview').addClass(jQuery('#thumbnails_inner_outer option:selected').val())
                        tthumbs.addClass("tbn-" + jQuery('#thumbnails_inner_outer option:selected').val())

                    }

                    // SHOW HIDE TABS
                    if (jQuery('#enable_tabs').attr("checked") !== "checked") {
                        ttabs.hide();
                    } else {
                        ttabs.show();
                        removeClasses(ttabs, "left,right,center,top,bottom,middle,vertical,horizontal,inner,outer,spanned,outer-left,outer-right,outer-top,outer-bottom");
                        ttabs.addClass("tbn-" + jQuery('#tabs_align_hor option:selected').val());
                        var v = jQuery('#tabs_align_vert option:selected').val() === "center" ? "middle" : jQuery('#tabs_align_vert option:selected').val();
                        ttabs.addClass("tbn-" + v);
                        ttabs.addClass("tbn-" + jQuery('#tabs_direction option:selected').val());
                        if (jQuery('#span_tabs_wrapper').attr("checked") === "checked")
                            ttabs.addClass("tbn-spanned");
                        jQuery('.toolbar-navigation-tabs-bg').css({background: window.RevColor.get(jQuery('#tabs_wrapper_color').val())});
                        jQuery('.toolbar-sliderpreview').addClass(jQuery('#tabs_inner_outer option:selected').val());
                        ttabs.addClass("tbn-" + jQuery('#tabs_inner_outer option:selected').val());
                    }

                    // DRAWING DOTTED OVERLAY
                    tdot.removeClass("twoxtwo").removeClass("twoxtwowhite").removeClass("threexthree").removeClass("threexthreewhite");
                    tdot.addClass(jQuery('#background_dotted_overlay').val());
                }
                document.addEventListener("DOMContentLoaded", function () {
                    RevSliderAdmin.initEditSlideView();
                });
            </script>


            <!-- ALL SETTINGS -->
            <div class="settings_wrapper closeallothers" id="form_slider_params_wrap">
                <form name="form_slider_params" id="form_slider_params"
                      onkeypress="return event.keyCode != 13;">

                    <!-- GENERAL SETTINGS -->

                    <div class="setting_box">
                        <h3 class="box_closed"><i class="rs-rp-accordion-icon eg-icon-cog-alt"></i>
                            <div class="setting_box-arrow"></div>
                            <span><?php echo t("General Settings") ?></span>
                        </h3>

                        <div class="inside" style="display:none;">

                            <ul class="main-options-small-tabs" style="display:inline-block; ">
                                <li id="gs_mp_1" data-content="#general-slideshow"
                                    class="selected"><?php echo t('Slideshow') ?></li>
                                <li id="gs_mp_2" data-content="#general-defaults"
                                    class=""><?php echo t('Defaults') ?></li>
                                <li id="gs_mp_3" data-content="#general-progressbar"
                                    class="dontshowonhero"><?php echo t('Progress Bar') ?></li>
                                <li id="gs_mp_4" data-content="#general-firstslide"
                                    class="dontshowonhero"><?php echo t('1st Slide') ?></li>
                                <li id="gs_mp_5"
                                    data-content="#general-misc"><?php echo t('Misc.') ?></li>

                            </ul>

                            <!-- GENERAL MISC. -->
                            <div id="general-misc" style="display:none">

                                <div class="dontshowonhero">
                                    <!-- NEXT SLIDE ON FOCUS -->
                                    <span id="label_next_slide_on_window_focus" class="label"
                                          origtitle="<?php echo t("Call next slide when inactive browser tab is focused again. Use this for avoid dissorted layers and broken timeouts after bluring the browser tab.") ?>"><?php echo t("Next Slide on Focus") ?> </span>
                                    <input type="checkbox" class="tp-moderncheckbox withlabel"
                                           id="next_slide_on_window_focus" name="next_slide_on_window_focus"
                                           data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, 'next_slide_on_window_focus', 'off'), "on"); ?>>
                                    <div class="clearfix"></div>
                                </div>

                                <div>
                                    <!-- BLUR ON FOCUS -->
                                    <span id="label_disable_focus_listener" class="label"
                                          origtitle="<?php echo t("This will disable the blur/focus behavior of the browser.") ?>"><?php echo t("Disable Blur/Focus behavior") ?> </span>
                                    <input type="checkbox" class="tp-moderncheckbox withlabel"
                                           id="disable_focus_listener" name="disable_focus_listener"
                                           data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, 'disable_focus_listener', 'off'), "on"); ?>>
                                    <div class="clearfix"></div>
                                </div>
                            </div><!-- end of GENERAL MISC -->

                            <!-- GENERAL DEFAULTS -->
                            <div id="general-defaults" style="display:none">
                                <!-- DEFAULT LAYER SELECTION -->
                                <?php $layer_selection = RevSliderFunctions::getVal($arrFieldsParams, 'def-layer_selection', 'off'); ?>
                                <span id="label_def-layer_selection"
                                      origtitle="<?php echo t("Default Layer Selection on Frontend enabled or disabled") ?>"
                                      class="label"><?php echo t('Layers Selectable:') ?></span>
                                <input type="checkbox" class="tp-moderncheckbox withlabel"
                                       id="def-layer_selection" name="def-layer_selection"
                                       data-unchecked="off" <?php RevSliderFunctions::checked($layer_selection, 'on'); ?>>

                                <!-- SLIDER ID -->
                                <span class="label" id="label_slider_id"
                                      origtitle="<?php echo t("Set a specific ID to the Slider, if empty, there will be a default one written") ?>"><?php echo t("Slider ID") ?> </span>
                                <input type="text" class="text-sidebar withlabel" id="slider_id"
                                       name="slider_id"
                                       value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'slider_id', ''); ?>">
                                <div class="clearfix"></div>

                                <!-- DELAY -->
                                <span class="label" id="label_delay"
                                      origtitle="<?php echo t("The time one slide stays on the screen in Milliseconds. This is a Default Global value. Can be adjusted slide to slide also in the slide editor.") ?>"><?php echo t("Default Slide Duration") ?> </span>
                                <input type="text" class="text-sidebar withlabel" id="delay" name="delay"
                                       value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'delay', '9000'); ?>">
                                <span><?php echo t("ms") ?></span>
                                <div class="clearfix"></div>

                                <!-- Initialisation Delay -->
                                <span id="label_start_js_after_delay" class="label"
                                      origtitle="<?php echo t("Sets a delay before the Slider gets initialized") ?>"><?php echo t("Initialization Delay") ?> </span>
                                <input type="text" class="text-sidebar withlabel" id="start_js_after_delay"
                                       name="start_js_after_delay"
                                       value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'start_js_after_delay', '0'); ?>">
                                <span><?php echo t("ms") ?></span>
                                <div class="clear"></div>
                                <div id="reset-to-default-inputs">
                                    <input type="checkbox" class="rs-ingore-save rs-reset-slide-setting"
                                           name="reset-slide_transition"/> <span id="label_def-slide_transition"
                                                                                 origtitle="<?php echo t("Default transition by creating a new slide.") ?>"
                                                                                 class="label"><?php echo t('Transitions') ?></span>
                                    <select id="def-slide_transition" name="def-slide_transition"
                                            style="max-width:105px" class="withlabel">
                                        <?php
                                        $def_trans = RevSliderFunctions::getVal($arrFieldsParams, 'def-slide_transition', 'fade');
                                        foreach ($transitions as $handle => $name) :
                                            $not = (strpos($handle, 'notselectable') !== false) ? ' disabled="disabled"' : '';
                                            $sel = ($def_trans == $handle) ? ' selected="selected"' : '';
                                            ?>
                                            <option
                                            value="<?php echo $handle ?>" <?php echo $not . $sel ?>><?php echo $name ?></option>
                                        <?php endforeach ?>
                                    </select>
                                    <div class="clear"></div>

                                    <?php $def_trans_dur = RevSliderFunctions::getVal($arrFieldsParams, 'def-transition_duration', '300'); ?>
                                    <input type="checkbox" class="rs-ingore-save rs-reset-slide-setting"
                                           name="reset-transition_duration"/> <span
                                            id="label_def-transition_duration"
                                            origtitle="<?php echo t("Default transition duration by creating a new slide.") ?>"
                                            class="label"
                                            origtitle=""><?php echo t('Animation Duration') ?></span>
                                    <input type="text" class="text-sidebar withlabel"
                                           id="def-transition_duration" name="def-transition_duration"
                                           value="<?php echo $def_trans_dur; ?>">
                                    <span><?php echo t('ms') ?></span>
                                    <div class="clear"></div>

                                    <?php
                                    $img_sizes = RevSliderFunctions::get_all_image_sizes();
                                    $bg_image_size = RevSliderFunctions::getVal($arrFieldsParams, 'def-image_source_type', 'full');
                                    ?>
                                    <input type="checkbox" class="rs-ingore-save rs-reset-slide-setting"
                                           name="reset-image_source_type"/> <span
                                            id="label_def-image_source_type" class="label"
                                            origtitle="<?php echo t("Default main image source size by creating a new slide.") ?>"><?php echo t('Image Source Size') ?></span>
                                    <select name="def-image_source_type">
                                        <?php
                                        foreach ($img_sizes as $imghandle => $imgSize) {
                                            $sel = ($bg_image_size == $imghandle) ? ' selected="selected"' : '';
                                            echo '<option value="' . $imghandle . '"' . $sel . '>' . $imgSize . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <div class="clear"></div>

                                    <?php
                                    $bgFit = RevSliderFunctions::getVal($arrFieldsParams, 'def-background_fit', 'cover');
                                    $bgFitX = RevSliderFunctions::getVal($arrFieldsParams, 'def-bg_fit_x', '100');
                                    $bgFitY = RevSliderFunctions::getVal($arrFieldsParams, 'def-bg_fit_y', '100');
                                    ?>
                                    <input type="checkbox" class="rs-ingore-save rs-reset-slide-setting"
                                           name="reset-background_fit"/> <span id="label_def-background_fit"
                                                                               origtitle="<?php echo t("Default background size by creating a new slide.") ?>"
                                                                               class="label"><?php echo t('Background Fit') ?></span>
                                    <select id="def-background_fit" name="def-background_fit"
                                            style="max-width: 105px;" class="withlabel">
                                        <option value="cover"<?php RevSliderFunctions::selected($bgFit, 'cover'); ?>>
                                            cover
                                        </option>
                                        <option value="contain"<?php RevSliderFunctions::selected($bgFit, 'contain'); ?>>
                                            contain
                                        </option>
                                        <option value="percentage"<?php RevSliderFunctions::selected($bgFit, 'percentage'); ?>>
                                            (%,
                                            %)
                                        </option>
                                        <option value="normal"<?php RevSliderFunctions::selected($bgFit, 'normal'); ?>>
                                            normal
                                        </option>
                                    </select>
                                    <input type="text" name="def-bg_fit_x"
                                           style="<?php if ($bgFit != 'percentage') {
                                               echo 'display: none; ';
                                           }
                                           ?> width:60px;margin-right:10px" value="<?php echo $bgFitX; ?>"/>
                                    <input type="text" name="def-bg_fit_y"
                                           style="<?php if ($bgFit != 'percentage') {
                                               echo 'display: none; ';
                                           }
                                           ?> width:60px;margin-right:10px" value="<?php echo $bgFitY; ?>"/>
                                    <div class="clear"></div>

                                    <?php
                                    $bgPosition = RevSliderFunctions::getVal($arrFieldsParams, 'def-bg_position', 'center center');
                                    $bgPositionX = RevSliderFunctions::getVal($arrFieldsParams, 'def-bg_position_x', '0');
                                    $bgPositionY = RevSliderFunctions::getVal($arrFieldsParams, 'def-bg_position_y', '0');
                                    ?>
                                    <input type="checkbox" class="rs-ingore-save rs-reset-slide-setting"
                                           name="reset-bg_position"/> <span id="label_slide_bg_position"
                                                                            origtitle="<?php echo t("Default background position by creating a new slide.") ?>"
                                                                            class="label"><?php echo t('Background Position') ?></span>
                                    <select name="def-bg_position" id="slide_bg_position" class="withlabel">
                                        <option value="center top"<?php RevSliderFunctions::selected($bgPosition, 'center top'); ?>>
                                            center top
                                        </option>
                                        <option value="center right"<?php RevSliderFunctions::selected($bgPosition, 'center right'); ?>>
                                            center right
                                        </option>
                                        <option value="center bottom"<?php RevSliderFunctions::selected($bgPosition, 'center bottom'); ?>>
                                            center bottom
                                        </option>
                                        <option value="center center"<?php RevSliderFunctions::selected($bgPosition, 'center center'); ?>>
                                            center center
                                        </option>
                                        <option value="left top"<?php RevSliderFunctions::selected($bgPosition, 'left top'); ?>>
                                            left
                                            top
                                        </option>
                                        <option value="left center"<?php RevSliderFunctions::selected($bgPosition, 'left center'); ?>>
                                            left center
                                        </option>
                                        <option value="left bottom"<?php RevSliderFunctions::selected($bgPosition, 'left bottom'); ?>>
                                            left bottom
                                        </option>
                                        <option value="right top"<?php RevSliderFunctions::selected($bgPosition, 'right top'); ?>>
                                            right top
                                        </option>
                                        <option value="right center"<?php RevSliderFunctions::selected($bgPosition, 'right center'); ?>>
                                            right center
                                        </option>
                                        <option value="right bottom"<?php RevSliderFunctions::selected($bgPosition, 'right bottom'); ?>>
                                            right bottom
                                        </option>
                                    </select>
                                    <input type="text" name="def-bg_position_x"
                                           style="<?php if ($bgPosition != 'percentage') {
                                               echo 'display: none;';
                                           }
                                           ?>width:60px;margin-right:10px" value="<?php echo $bgPositionX; ?>"/>
                                    <input type="text" name="def-bg_position_y"
                                           style="<?php if ($bgPosition != 'percentage') {
                                               echo 'display: none;';
                                           }
                                           ?>width:60px;margin-right:10px" value="<?php echo $bgPositionY; ?>"/>
                                    <div class="clear"></div>
                                    <?php
                                    $bgRepeat = RevSliderFunctions::getVal($arrFieldsParams, 'def-bg_repeat', 'no-repeat');
                                    ?>
                                    <input type="checkbox" class="rs-ingore-save rs-reset-slide-setting"
                                           name="reset-bg_repeat"/>
                                    <span id="slide_bg_repeat"
                                          origtitle="<?php echo t("Default background repeat by creating a new slide.") ?>"
                                          class="label"><?php echo t('Background Repeat') ?></span>
                                    <select name="def-bg_repeat" id="slide_bg_repeat" style="margin-right:20px">
                                        <option value="no-repeat"<?php RevSliderFunctions::selected($bgRepeat, 'no-repeat'); ?>>
                                            no-repeat
                                        </option>
                                        <option value="repeat"<?php RevSliderFunctions::selected($bgRepeat, 'repeat'); ?>>
                                            repeat
                                        </option>
                                        <option value="repeat-x"<?php RevSliderFunctions::selected($bgRepeat, 'repeat-x'); ?>>
                                            repeat-x
                                        </option>
                                        <option value="repeat-y"<?php RevSliderFunctions::selected($bgRepeat, 'repeat-y'); ?>>
                                            repeat-y
                                        </option>
                                    </select>
                                    <div class="clear"></div>

                                    <?php $kenburn_effect = RevSliderFunctions::getVal($arrFieldsParams, 'def-kenburn_effect', 'off'); ?>
                                    <input type="checkbox" class="rs-ingore-save rs-reset-slide-setting"
                                           name="reset-kenburn_effect"/>
                                    <span id="label_def-kenburn_effect"
                                          origtitle="<?php echo t("Default Ken/Burn setting by creating a new slide.") ?>"
                                          class="label"><?php echo t('Ken Burns / Pan Zoom:') ?></span>
                                    <input type="checkbox" class="tp-moderncheckbox withlabel"
                                           id="def-kenburn_effect" name="def-kenburn_effect"
                                           data-unchecked="off" <?php RevSliderFunctions::checked($kenburn_effect, 'on'); ?>>

                                    <div class="clear"></div>
                                    <div id="def-kenburns-wrapper" <?php if ($kenburn_effect == 'off') {
                                        echo 'style="display: none;"';
                                    }
                                    ?>>

                                        <?php $kb_start_fit = RevSliderFunctions::getVal($arrFieldsParams, 'def-kb_start_fit', '100'); ?>
                                        <input type="checkbox" class="rs-ingore-save rs-reset-slide-setting"
                                               name="reset-kb_start_fit"/>
                                        <span id="label_kb_start_fit"
                                              class="label"><?php echo t('Start Fit: (in %):') ?></span>
                                        <input type="text" name="def-kb_start_fit"
                                               value="<?php echo intval($kb_start_fit); ?>"/>
                                        <div class="clear"></div>

                                        <?php $kb_easing = RevSliderFunctions::getVal($arrFieldsParams, 'def-kb_easing', 'Linear.easeNone'); ?>
                                        <input type="checkbox" class="rs-ingore-save rs-reset-slide-setting"
                                               name="reset-kb_easing"/> <span id="label_kb_easing"
                                                                              class="label"><?php echo t('Easing:') ?></span>
                                        <select name="def-kb_easing">
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Linear.easeNone'); ?>
                                                    value="Linear.easeNone">Linear.easeNone
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Power0.easeIn'); ?>
                                                    value="Power0.easeIn">Power0.easeIn (linear)
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Power0.easeInOut'); ?>
                                                    value="Power0.easeInOut">Power0.easeInOut (linear)
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Power0.easeOut'); ?>
                                                    value="Power0.easeOut">Power0.easeOut (linear)
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Power1.easeIn'); ?>
                                                    value="Power1.easeIn">Power1.easeIn
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Power1.easeInOut'); ?>
                                                    value="Power1.easeInOut">Power1.easeInOut
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Power1.easeOut'); ?>
                                                    value="Power1.easeOut">Power1.easeOut
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Power2.easeIn'); ?>
                                                    value="Power2.easeIn">Power2.easeIn
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Power2.easeInOut'); ?>
                                                    value="Power2.easeInOut">Power2.easeInOut
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Power2.easeOut'); ?>
                                                    value="Power2.easeOut">Power2.easeOut
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Power3.easeIn'); ?>
                                                    value="Power3.easeIn">Power3.easeIn
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Power3.easeInOut'); ?>
                                                    value="Power3.easeInOut">Power3.easeInOut
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Power3.easeOut'); ?>
                                                    value="Power3.easeOut">Power3.easeOut
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Power4.easeIn'); ?>
                                                    value="Power4.easeIn">Power4.easeIn
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Power4.easeInOut'); ?>
                                                    value="Power4.easeInOut">Power4.easeInOut
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Power4.easeOut'); ?>
                                                    value="Power4.easeOut">Power4.easeOut
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Back.easeIn'); ?>
                                                    value="Back.easeIn">Back.easeIn
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Back.easeInOut'); ?>
                                                    value="Back.easeInOut">Back.easeInOut
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Back.easeOut'); ?>
                                                    value="Back.easeOut">Back.easeOut
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Bounce.easeIn'); ?>
                                                    value="Bounce.easeIn">Bounce.easeIn
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Bounce.easeInOut'); ?>
                                                    value="Bounce.easeInOut">Bounce.easeInOut
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Bounce.easeOut'); ?>
                                                    value="Bounce.easeOut">Bounce.easeOut
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Circ.easeIn'); ?>
                                                    value="Circ.easeIn">Circ.easeIn
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Circ.easeInOut'); ?>
                                                    value="Circ.easeInOut">Circ.easeInOut
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Circ.easeOut'); ?>
                                                    value="Circ.easeOut">Circ.easeOut
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Elastic.easeIn'); ?>
                                                    value="Elastic.easeIn">Elastic.easeIn
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Elastic.easeInOut'); ?>
                                                    value="Elastic.easeInOut">Elastic.easeInOut
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Elastic.easeOut'); ?>
                                                    value="Elastic.easeOut">Elastic.easeOut
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Expo.easeIn'); ?>
                                                    value="Expo.easeIn">Expo.easeIn
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Expo.easeInOut'); ?>
                                                    value="Expo.easeInOut">Expo.easeInOut
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Expo.easeOut'); ?>
                                                    value="Expo.easeOut">Expo.easeOut
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Sine.easeIn'); ?>
                                                    value="Sine.easeIn">Sine.easeIn
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Sine.easeInOut'); ?>
                                                    value="Sine.easeInOut">Sine.easeInOut
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'Sine.easeOut'); ?>
                                                    value="Sine.easeOut">Sine.easeOut
                                            </option>
                                            <option <?php RevSliderFunctions::selected($kb_easing, 'SlowMo.ease'); ?>
                                                    value="SlowMo.ease">SlowMo.ease
                                            </option>
                                        </select>
                                        <div class="clear"></div>


                                        <?php $kb_end_fit = RevSliderFunctions::getVal($arrFieldsParams, 'def-kb_end_fit', '100'); ?>
                                        <input type="checkbox" class="rs-ingore-save rs-reset-slide-setting"
                                               name="reset-kb_end_fit"/> <span id="label_kb_end_fit"
                                                                               class="label"><?php echo t('End Fit: (in %):') ?></span>
                                        <input type="text" name="def-kb_end_fit"
                                               value="<?php echo intval($kb_end_fit); ?>"/>
                                        <div class="clear"></div>

                                        <?php $kb_start_offset_x = RevSliderFunctions::getVal($arrFieldsParams, 'def-kb_start_offset_x', '0'); ?>
                                        <input type="checkbox" class="rs-ingore-save rs-reset-slide-setting"
                                               name="reset-kb_start_offset_x"/> <span id="label_kb_end_fit"
                                                                                      class="label"><?php echo t('Start Offset X:') ?></span>
                                        <input type="text" name="def-kb_start_offset_x"
                                               value="<?php echo intval($kb_start_offset_x); ?>"/>
                                        <div class="clear"></div>

                                        <?php $kb_start_offset_y = RevSliderFunctions::getVal($arrFieldsParams, 'def-kb_start_offset_y', '0'); ?>
                                        <input type="checkbox" class="rs-ingore-save rs-reset-slide-setting"
                                               name="reset-kb_start_offset_y"/> <span id="label_kb_end_fit"
                                                                                      class="label"><?php echo t('Start Offset Y:') ?></span>
                                        <input type="text" name="def-kb_start_offset_y"
                                               value="<?php echo intval($kb_start_offset_y); ?>"/>
                                        <div class="clear"></div>

                                        <?php $kb_end_offset_x = RevSliderFunctions::getVal($arrFieldsParams, 'def-kb_end_offset_x', '0'); ?>
                                        <input type="checkbox" class="rs-ingore-save rs-reset-slide-setting"
                                               name="reset-kb_end_offset_x"/> <span id="label_kb_end_fit"
                                                                                    class="label"><?php echo t('End Offset X:') ?></span>
                                        <input type="text" name="def-kb_end_offset_x"
                                               value="<?php echo intval($kb_end_offset_x); ?>"/>
                                        <div class="clear"></div>

                                        <?php $kb_end_offset_y = RevSliderFunctions::getVal($arrFieldsParams, 'def-kb_end_offset_y', '0'); ?>
                                        <input type="checkbox" class="rs-ingore-save rs-reset-slide-setting"
                                               name="reset-kb_end_offset_y"/> <span id="label_kb_end_fit"
                                                                                    class="label"><?php echo t('End Offset Y:') ?></span>
                                        <input type="text" name="def-kb_end_offset_y"
                                               value="<?php echo intval($kb_end_offset_y); ?>"/>
                                        <div class="clear"></div>

                                        <?php $kb_start_rotate = RevSliderFunctions::getVal($arrFieldsParams, 'def-kb_start_rotate', '0'); ?>
                                        <input type="checkbox" class="rs-ingore-save rs-reset-slide-setting"
                                               name="reset-kb_start_rotate"/> <span id="label_kb_end_fit"
                                                                                    class="label"><?php echo t('Start Rotate:') ?></span>
                                        <input type="text" name="def-kb_start_rotate"
                                               value="<?php echo intval($kb_start_rotate); ?>"/>
                                        <div class="clear"></div>

                                        <?php $kb_end_rotate = RevSliderFunctions::getVal($arrFieldsParams, 'def-kb_end_rotate', '0'); ?>
                                        <input type="checkbox" class="rs-ingore-save rs-reset-slide-setting"
                                               name="reset-kb_end_rotate"/> <span id="label_kb_end_fit"
                                                                                  class="label"><?php echo t('End Rotate:') ?></span>
                                        <input type="text" name="def-kb_end_rotate"
                                               value="<?php echo intval($kb_end_rotate); ?>"/>
                                        <div class="clear"></div>

                                        <?php $kb_blur_start = RevSliderFunctions::getVal($arrFieldsParams, 'def-kb_blur_start', '0'); ?>
                                        <input type="checkbox" class="rs-ingore-save rs-reset-slide-setting"
                                               name="reset-kb_blur_start"/> <span id="label_kb_end_fit"
                                                                                  class="label"><?php echo t('Blur Start:') ?></span>
                                        <input type="text" name="def-kb_blur_start"
                                               value="<?php echo intval($kb_blur_start); ?>"/>
                                        <div class="clear"></div>

                                        <?php $kb_blur_end = RevSliderFunctions::getVal($arrFieldsParams, 'def-kb_blur_end', '0'); ?>
                                        <input type="checkbox" class="rs-ingore-save rs-reset-slide-setting"
                                               name="reset-kb_blur_end"/> <span id="label_kb_end_fit"
                                                                                class="label"><?php echo t('Blur End:') ?></span>
                                        <input type="text" name="def-kb_blur_end"
                                               value="<?php echo intval($kb_blur_end); ?>"/>
                                        <div class="clear"></div>

                                        <?php $kb_duration = RevSliderFunctions::getVal($arrFieldsParams, 'def-kb_duration', '10000'); ?>
                                        <input type="checkbox" class="rs-ingore-save rs-reset-slide-setting"
                                               name="reset-kb_duration"/> <span id="label_kb_duration"
                                                                                class="label"><?php echo t('Duration (in ms):') ?></span>
                                        <input type="text" name="def-kb_duration"
                                               value="<?php echo intval($kb_duration); ?>"/>
                                        <div class="clear"></div>
                                    </div>
                                    <span class="overwrite-arrow"></span>
                                    <input type="button" id="reset_slide_button"
                                           value="<?php echo t('Overwrite Selected Settings on all Slides') ?>"
                                           class="button-primary revblue" origtitle="">
                                    <div class="clear"></div>
                                </div>


                            </div> <!-- END OF GENERAL DEFAULTS -->

                            <!-- GENERAL FIRST SLIDE -->
                            <div id="general-firstslide" style="display:none">
                                        <span id="label_start_with_slide_enable" class="label"
                                              origtitle="<?php echo t("Activate Alternative 1st Slide.") ?>"><?php echo t("Activate Alt. 1st Slide") ?></span>
                                <input type="checkbox" class="tp-moderncheckbox withlabel"
                                       id="start_with_slide_enable" name="start_with_slide_enable"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "start_with_slide_enable", "off"), "on"); ?>>
                                <div class="clear"></div>

                                <?php $start_with_slide = intval(RevSliderFunctions::getVal($arrFieldsParams, 'start_with_slide', '1')); ?>
                                <div id="start_with_slide_row">
                                            <span id="label_start_with_slide" class="label"
                                                  origtitle="<?php echo t("Start from a different slide instead of the first slide. I.e. good for preview / edit mode.") ?>"><?php echo t("Alternative 1st Slide") ?> </span>
                                    <input type="text" class="text-sidebar withlabel" id="start_with_slide"
                                           name="start_with_slide" value="<?php echo $start_with_slide; ?>">
                                    <div class="clear"></div>
                                </div>

                                <span id="label_first_transition_active" class="label"
                                      origtitle="<?php echo t("If active, it will overwrite the first slide transition. Use it to get special transition for the first slide.") ?>"><?php echo t("First Transition Active") ?> </span>
                                <input type="checkbox" class="tp-moderncheckbox withlabel"
                                       id="first_transition_active" name="first_transition_active"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "first_transition_active", "off"), "on"); ?>>
                                <div class="clear"></div>

                                <div id="first_transition_row" class="withsublabels">
                                            <span id="label_first_transition_type" class="label"
                                                  origtitle="<?php echo t("First slide transition type") ?>"><?php echo t("Transition Type") ?> </span>
                                    <select id="first_transition_type" name="first_transition_type"
                                            style="max-width:100px" class="withlabel">
                                        <?php
                                        $transitions = $operations->getArrTransition();
                                        $ftt = RevSliderFunctions::getVal($arrFieldsParams, 'first_transition_type', 'fade');
                                        foreach ($transitions as $handle => $name) {
                                            $not = (strpos($handle, 'notselectable') !== false) ? ' disabled="disabled"' : '';
                                            $sel = ($handle == $ftt) ? ' selected="selected"' : '';
                                            echo '<option value="' . $handle . '"' . $not . $sel . '>' . $name . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <div class="clear"></div>

                                    <span id="label_first_transition_duration" class="label"
                                          origtitle="<?php echo t("First slide transition duration.") ?>"><?php echo t("Transition Duration") ?> </span>
                                    <input type="text" class="text-sidebar withlabel"
                                           id="first_transition_duration" name="first_transition_duration"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "first_transition_duration", "300"); ?>">
                                    <span><?php echo t("ms") ?></span>
                                    <div class="clear"></div>


                                    <span id="label_first_transition_slot_amount" class="label"
                                          origtitle="<?php echo t("The number of slots or boxes the slide is divided into.") ?>"><?php echo t("Transition Slot Amount") ?> </span>
                                    <input type="text" class="text-sidebar withlabel"
                                           id="first_transition_slot_amount" name="first_transition_slot_amount"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "first_transition_slot_amount", "7"); ?>">
                                    <span><?php echo t("ms") ?></span>
                                    <div class="clear"></div>
                                </div>
                            </div><!-- END OF GENERAL FIRST SLIDE -->

                            <!-- GENERAL SLIDE SHOW -->
                            <div id="general-slideshow" style="display:block;">
                                <div class="dontshowonhero">
                                    <!-- Stop Slider on Hover -->
                                    <span id="label_stop_on_hover" class="label"
                                          origtitle="<?php echo t("Stops the Timer when mouse is hovering the slider.") ?>"><?php echo t("Stop Slide On Hover") ?></span>
                                    <input type="checkbox" class="tp-moderncheckbox withlabel"
                                           id="stop_on_hover" name="stop_on_hover"
                                           data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, 'stop_on_hover', 'off'), "on"); ?>>
                                    <div class="clear"></div>

                                    <!-- Stop Slider -->
                                    <span class="label label-with-subsection" id="label_stop_slider"
                                          origtitle="<?php echo t("Stops the slideshow after the predefined loop amount at the predefined slide.") ?>"><?php echo t("Stop Slider After ...") ?> </span>
                                    <input type="checkbox" class="tp-moderncheckbox withlabel" id="stop_slider"
                                           name="stop_slider"
                                           data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, 'stop_slider', 'off'), 'on'); ?>>
                                    <div class="clear"></div>

                                    <div id="stopoptionsofslider" class="withsublabels">
                                        <!-- Stop After loops -->
                                        <span class="label " id="label_stop_after_loops"
                                              origtitle="<?php echo t("Stops the slider after certain amount of loops. 0 related to the first loop.") ?>"><?php echo t("Amount of Loops") ?> </span>
                                        <input type="text" class="text-sidebar withlabel" id="stop_after_loops"
                                               name="stop_after_loops"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'stop_after_loops', '0'); ?>">
                                        <div class="clear"></div>

                                        <!-- Stop At Slide -->
                                        <span class="label" id="label_stop_at_slide"
                                              origtitle="<?php echo t("Stops the slider at the given slide") ?>"><?php echo t("At Slide") ?> </span>
                                        <input type="text" class="text-sidebar withlabel" id="stop_at_slide"
                                               name="stop_at_slide"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'stop_at_slide', '2'); ?>">
                                        <div class="clear"></div>
                                    </div>

                                    <!-- SHUFFLE -->
                                    <span id="label_shuffle" class="label"
                                          origtitle="<?php echo t("Randomize the order of the slides at every Page reload.") ?>"><?php echo t("Shuffle / Random Mode") ?> </span>
                                    <input type="checkbox" class="tp-moderncheckbox withlabel" id="shuffle"
                                           name="shuffle"
                                           data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, 'shuffle', 'off'), "on"); ?>>
                                    <div class="clearfix"></div>

                                    <!-- Loop Single Slide -->
                                    <span class="label" id="label_loop_slide"
                                          origtitle="<?php echo t("If only one Slide is in the Slider, you can choose wether the Slide should loop or if it should stop. If only one Slide exist, slide will be duplicated !") ?>"><?php echo t("Loop Single Slide") ?> </span>
                                    <input type="checkbox" class="tp-moderncheckbox withlabel" id="loop_slide"
                                           name="loop_slide"
                                           data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, 'loop_slide', 'off'), "on"); ?>>
                                    <div class="clear"></div>
                                </div>

                                <!-- ViewPort Slider -->
                                <span id="label_label_viewport" class="label label-with-subsection"
                                      origtitle="<?php echo t("Allow to stop the Slider out of viewport.") ?>"><?php echo t("Stop Slider Out of ViewPort") ?> </span>
                                <input type="checkbox" class="tp-moderncheckbox withlabel" id="label_viewport"
                                       name="label_viewport"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, 'label_viewport', 'off'), 'on'); ?>>
                                <div class="clear"></div>

                                <div id="viewportoptionsofslider"
                                     class="withsublabels" <?php if (RevSliderFunctions::getVal($arrFieldsParams, 'label_viewport', 'off') == 'off') echo 'style="display: none;"'; ?>>
                                    <span class="label"><?php echo t('Out Of ViewPort:') ?></span>
                                    <select name="viewport_start">
                                        <option value="wait" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'viewport_start', 'wait'), "wait"); ?>><?php echo t("Wait") ?></option>
                                        <option value="pause" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'viewport_start', 'wait'), "pause"); ?>><?php echo t("Pause") ?></option>
                                    </select>
                                    <div class="clear"></div>

                                    <span class="label" origmedia="show"
                                          origtitle="<?php echo t("Min. Size of Slider must be in Viewport before slide starts again.") ?>"><?php echo t("Area out of ViewPort:") ?> </span>
                                    <input type="text" class="text-sidebar withlabel" id="viewport_area"
                                           name="viewport_area"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'viewport_area', '80'); ?>">
                                    <span><?php echo t("%") ?></span>

                                    <span class="label label-with-subsection"
                                          origtitle="<?php echo t("Precalculate the Height of the Slider to support Inline Links") ?>"><?php echo t("Preset Slider Height") ?> </span>
                                    <input type="checkbox" class="tp-moderncheckbox withlabel"
                                           id="label_presetheight" name="label_presetheight"
                                           data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, 'label_presetheight', 'off'), 'on'); ?>>
                                    <div class="clear"></div>

                                </div>

                                <!-- wait for revstart -->
                                <span id="label_waitforinit" class="label label-with-subsection text-selectable"
                                      origtitle="<?php echo t("Wait for the revstart method to be called before playing.") ?>"><?php echo t("Wait for ") ?>
                                    revapi<?php echo ($is_edit) ? $sliderID : ''; ?>.revstart() </span>
                                <input type="checkbox" class="tp-moderncheckbox withlabel" id="waitforinit"
                                       name="waitforinit"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, 'waitforinit', 'off'), 'on'); ?>>
                                <div class="clear"></div>
                            </div><!-- END OF GENERAL SLIDE SHOW -->

                            <!-- GENERAL PROGRESSBAR -->
                            <div id="general-progressbar" style="display:none">

                                        <span class="label" id="label_enable_progressbar" origmedia='show'
                                              origtitle="<?php echo t("Enable / disable progress var") ?>"><?php echo t("Progress Bar Active") ?> </span>
                                <input type="checkbox" class="tp-moderncheckbox withlabel"
                                       id="enable_progressbar" name="enable_progressbar"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "enable_progressbar", "off"), "on"); ?>>
                                <div id="progressbar_settings">
                                    <!-- Show Progressbar -->
                                    <span class="label" id="label_show_timerbar" origmedia="show"
                                          origtitle="<?php echo t("Position of the progress bar.") ?>"><?php echo t("Progress Bar Position") ?> </span>
                                    <select id="show_timerbar" name="show_timerbar" class="withlabel">
                                        <option value="top" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'show_timerbar', 'top'), "top"); ?>><?php echo t("Top") ?></option>
                                        <option value="bottom" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'show_timerbar', 'top'), "bottom"); ?>><?php echo t("Bottom") ?></option>
                                    </select>
                                    <div class="clear"></div>

                                    <!-- Progress Bar Height -->
                                    <span class="label" id="label_progress_height" origmedia="show"
                                          origtitle="<?php echo t("The height of the progress bar") ?>"><?php echo t("Progress Bar Heigth") ?> </span>
                                    <input type="text" class="text-sidebar withlabel" id="progress_height"
                                           name="progress_height"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'progress_height', '5'); ?>">
                                    <span><?php echo t("px") ?></span>
                                    <div class="clear"></div>

                                    <!-- Progress Bar Color -->
                                    <span class="label" id="label_progressbar_color" origmedia="show"
                                          origtitle="<?php echo t("Color of the progress bar.") ?>"><?php echo t("Progress Bar Color") ?> </span>
                                    <input type="text"
                                           class="my-color-field rs-layer-input-field tipsy_enabled_top withlabel"
                                           id="progressbar_color" data-editing="Progress Bar Color"
                                           name="progressbar_color"
                                           value="<?php echo TPColorpicker::convert(RevSliderFunctions::getVal($arrFieldsParams, 'progressbar_color', 'rgba(0,0,0,0.15)'), RevSliderFunctions::getVal($arrFieldsParams, 'progress_opa', 'false')); ?>"/>
                                    <script>
                                        document.addEventListener("DOMContentLoaded", function () {
                                            var v = jQuery('#progressbar_color').val();
                                            if (v.indexOf("false") >= 0 && v.indexOf("rgba") >= 0)
                                                jQuery('#progressbar_color').val(window.RevColor.get(jQuery('#progressbar_color').val()));
                                        });
                                    </script>
                                    <div class="clear"></div>
                                </div>
                            </div><!-- END OF GENERAL PROGRESSBAR -->
                        </div>

                        <script>
                            document.addEventListener("DOMContentLoaded", function () {
                                jQuery('#stop_slider').on("change", function () {
                                    var sbi = jQuery(this);
                                    if (sbi.attr("checked") === "checked") {
                                        jQuery('#stopoptionsofslider').show();
                                    } else {
                                        jQuery('#stopoptionsofslider').hide();
                                    }
                                    drawToolBarPreview();
                                });

                                jQuery('#label_viewport').on("change", function () {
                                    var sbi = jQuery(this);
                                    if (sbi.attr("checked") === "checked") {
                                        jQuery('#viewportoptionsofslider').show();
                                    } else {
                                        jQuery('#viewportoptionsofslider').hide();
                                    }
                                    drawToolBarPreview();
                                });


                                jQuery('#enable_progressbar').on("change", function () {
                                    var sbi = jQuery(this);
                                    if (sbi.attr('checked') !== "checked")
                                        jQuery('#progressbar_settings').hide();
                                    else
                                        jQuery('#progressbar_settings').show();
                                    drawToolBarPreview();
                                });

                                jQuery('#progress_height').on("keyup", drawToolBarPreview);
                                jQuery('#progress_opa').on("keyup", drawToolBarPreview);

                                jQuery('#enable_progressbar').change();
                                jQuery('#stop_slider').change();
                                jQuery('#show_timerbar').change();

                                // ALTERNATIVE FIRST SLIDE
                                jQuery('#first_transition_active').on("change", function () {
                                    var sbi = jQuery(this);

                                    if (sbi.attr("checked") === "checked") {
                                        jQuery('#first_transition_row').show();

                                    } else {
                                        jQuery('#first_transition_row').hide();
                                    }
                                });
                                jQuery('#first_transition_active').change();
                            });
                        </script>
                    </div><!-- END OF GENERAL SETTINGS -->

                    <!-- LAYOUT VISAL SETTINGS -->
                    <div class="setting_box">
                        <h3 class="box_closed"><i class="rs-rp-accordion-icon eg-icon-droplet"></i>
                            <div class="setting_box-arrow"></div>
                            <span><?php echo t("Layout & Visual") ?></span>
                        </h3>

                        <div class="inside" style="display:none">
                            <ul class="main-options-small-tabs" style="display:inline-block; ">
                                <li id="lv_mp_1" data-content="#visual-appearance"
                                    class="selected"><?php echo t('Appearance') ?></li>
                                <!--<li data-content="#visual-sizing"><?php echo t('Sizing') ?></li>									-->
                                <li id="lv_mp_2"
                                    data-content="#visual-spinner"><?php echo t('Spinner') ?></li>
                                <li id="lv_mp_3"
                                    data-content="#visual-mobile"><?php echo t('Mobile') ?></li>
                                <li id="lv_mp_4"
                                    data-content="#visual-position"><?php echo t('Position') ?></li>
                            </ul>

                            <!-- VISUAL Mobile -->
                            <div id="visual-mobile" style="display:none">
                                        <span class="label" id="label_disable_on_mobile"
                                              origtitle="<?php echo t("If this is enabled, the slider will not be loaded on mobile devices.") ?>"><?php echo t("Disable Slider on Mobile") ?></span>
                                <input type="checkbox" class="tp-moderncheckbox withlabel"
                                       id="disable_on_mobile" name="disable_on_mobile"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "disable_on_mobile", "off"), "on"); ?>>
                                <div class="clear"></div>


                                <span class="label" id="label_disable_kenburns_on_mobile"
                                      origtitle="<?php echo t("This will disable KenBurns on mobile devices to save performance") ?>"><?php echo t("Disable KenBurn On Mobile") ?></span>
                                <input type="checkbox" class="tp-moderncheckbox withlabel"
                                       id="disable_kenburns_on_mobile" name="disable_kenburns_on_mobile"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "disable_kenburns_on_mobile", "off"), "on"); ?>>
                                <div class="clear"></div>

                                <h4><?php echo t("Hide Element Under Width:") ?></h4>

                                <span class="label" id="label_hide_slider_under"
                                      origtitle="<?php echo t("Hide the slider under the defined slider width. Value 0 will disable the function.") ?>"><?php echo t("Slider") ?></span>
                                <input type="text" class="text-sidebar withlabel" id="hide_slider_under"
                                       name="hide_slider_under"
                                       value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "hide_slider_under", "0"); ?>">
                                <span><?php echo t("px") ?></span>
                                <div class="clear"></div>


                                <span class="label" id="label_hide_defined_layers_under" style="font-size:12px"
                                      origtitle="<?php echo t("Hide the selected layers (set layers hide under in slide editor) under the defined slider width. Value 0 will disable the function.") ?>"><?php echo t("Predefined Layers") ?></span>
                                <input type="text" class="text-sidebar withlabel" id="hide_defined_layers_under"
                                       name="hide_defined_layers_under"
                                       value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "hide_defined_layers_under", "0"); ?>">
                                <span><?php echo t("px") ?></span>
                                <div class="clear"></div>

                                <span class="label" id="label_hide_all_layers_under"
                                      origtitle="<?php echo t("Hide all layers under the defined slider width. Value 0 will disable the function.") ?>"><?php echo t("All Layers") ?></span>
                                <input type="text" class="text-sidebar withlabel" id="hide_all_layers_under"
                                       name="hide_all_layers_under"
                                       value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "hide_all_layers_under", "0"); ?>">
                                <span><?php echo t("px") ?></span>
                                <div class="clear"></div>

                            </div><!-- VISUAL MOBILE -->


                            <!-- VISUAL APPEARANCE -->
                            <div id="visual-appearance" style="display:block">
                                <div class="hide_on_ddd_parallax">
                                            <span class="label " id="label_shadow_type" origmedia='show'
                                                  origtitle="<?php echo t("The Shadow display underneath the banner.") ?>"><?php echo t("Shadow Type") ?> </span>
                                    <select id="shadow_type" class="withlabel" name="shadow_type">
                                        <option value="0" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'shadow_type', '0'), "0"); ?>><?php echo t("No Shadow") ?></option>
                                        <option value="1" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'shadow_type', '0'), "1"); ?>>
                                            1
                                        </option>
                                        <option value="2" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'shadow_type', '0'), "2"); ?>>
                                            2
                                        </option>
                                        <option value="3" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'shadow_type', '0'), "3"); ?>>
                                            3
                                        </option>
                                        <option value="4" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'shadow_type', '0'), "4"); ?>>
                                            4
                                        </option>
                                        <option value="5" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'shadow_type', '0'), "5"); ?>>
                                            5
                                        </option>
                                        <option value="6" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'shadow_type', '0'), "6"); ?>>
                                            6
                                        </option>
                                    </select>
                                    <div class="clear"></div>
                                </div>
                                <span class="label" id="label_background_dotted_overlay" origmedia="show"
                                      origtitle="<?php echo t("Show a dotted overlay over the slides.") ?>"><?php echo t("Dotted Overlay Size") ?> </span>
                                <select id="background_dotted_overlay" name="background_dotted_overlay"
                                        class="withlabel">
                                    <option value="none" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'background_dotted_overlay', 'none'), "none"); ?>><?php echo t("none") ?></option>
                                    <option value="twoxtwo" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'background_dotted_overlay', 'none'), "twoxtwo"); ?>><?php echo t("2 x 2 Black") ?></option>
                                    <option value="twoxtwowhite" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'background_dotted_overlay', 'none'), "twoxtwowhite"); ?>><?php echo t("2 x 2 White") ?></option>
                                    <option value="threexthree" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'background_dotted_overlay', 'none'), "threexthree"); ?>><?php echo t("3 x 3 Black") ?></option>
                                    <option value="threexthreewhite" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'background_dotted_overlay', 'none'), "threexthreewhite"); ?>><?php echo t("3 x 3 White") ?></option>
                                </select>
                                <div class="clear"></div>

                                <h4><?php echo t("Slider Background") ?></h4>
                                <span class="label" id="label_background_color" origmedia="showbg"
                                      origtitle="<?php echo t("General background color for slider. Clear value to get transparent slider container.") ?>"><?php echo t("Background color") ?> </span>
                                <input type="text"
                                       class="my-color-field rs-layer-input-field tipsy_enabled_top withlabel"
                                       title="<?php echo t("Font Color") ?>" id="background_color"
                                       name="background_color"
                                       value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'background_color', 'transparent'); ?>"/>
                                <div class="clear"></div>

                                <span class="label" id="label_padding" origmedia="showbg"
                                      origtitle="<?php echo t("Padding around the slider. Together with background color shows as slider border.") ?>"><?php echo t("Padding as Border") ?> </span>
                                <input type="text" class="text-sidebar withlabel" id="padding" name="padding"
                                       value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'padding', '0'); ?>">
                                <div class="clear"></div>

                                <span class="label" id="label_show_background_image" origmedia="showbg"
                                      origtitle="<?php echo t("Use a general background image instead of general background color.") ?>"><?php echo t("Show Background Image") ?> </span>
                                <input type="checkbox" class="tp-moderncheckbox withlabel"
                                       id="show_background_image" name="show_background_image"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, 'show_background_image', 'off'), "on"); ?>>
                                <div class="clear"></div>

                                <div id="background_settings" class="withsublabels">
                                            <span class="label" id="label_background_image" origmedia="showbg"
                                                  origtitle="<?php echo t("The source of the general background image.") ?>"><?php echo t("Background Image Url") ?> </span>
                                    <input type="text" class="text-sidebar-long withlabel" style="width: 104px;"
                                           id="background_image" name="background_image"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'background_image', ''); ?>">
                                    <a href="javascript:void(0)"
                                       class="button-image-select-bg-img button-primary revblue"><?php echo t('Set') ?></a>
                                    <div class="clear"></div>

                                    <span class="label" id="label_bg_fit" origmedia="showbg"
                                          origtitle="<?php echo t("General background image size. Cover - always fill the container, cuts overlays. Contain- always fits image into slider.") ?>"><?php echo t("Background Fit") ?> </span>
                                    <select id="bg_fit" name="bg_fit" class="withlabel">
                                        <option value="cover" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'bg_fit', 'cover'), "cover"); ?>><?php echo t("cover") ?></option>
                                        <option value="contain" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'bg_fit', 'cover'), "contain"); ?>><?php echo t("contain") ?></option>
                                        <option value="normal" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'bg_fit', 'cover'), "normal"); ?>><?php echo t("normal") ?></option>
                                    </select>
                                    <div class="clear"></div>

                                    <span class="label" id="label_bg_repeat" origmedia="showbg"
                                          origtitle="<?php echo t("General background image repeat attitude. Used for tiled images.") ?>"><?php echo t("Background Repeat") ?> </span>
                                    <select id="bg_repeat" name="bg_repeat" class="withlabel">
                                        <option value="no-repeat" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'bg_repeat', 'no-repeat'), "no-repeat"); ?>><?php echo t("no-repeat") ?></option>
                                        <option value="repeat" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'bg_repeat', 'no-repeat'), "repeat"); ?>><?php echo t("repeat") ?></option>
                                        <option value="repeat-x" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'bg_repeat', 'no-repeat'), "repeat-x"); ?>><?php echo t("repeat-x") ?></option>
                                        <option value="repeat-y" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'bg_repeat', 'no-repeat'), "repeat-y"); ?>><?php echo t("repeat-y") ?></option>
                                    </select>
                                    <div class="clear"></div>

                                    <span class="label" id="label_bg_position" origmedia="showbg"
                                          origtitle="<?php echo t("General background image position.  i.e. center center to always center vertical and horizontal the image in the slider background.") ?>"><?php echo t("Background Position") ?> </span>
                                    <select id="bg_position" name="bg_position" class="withlabel">
                                        <option value="center top" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'bg_position', 'center center'), "center top"); ?>><?php echo t("center top") ?></option>
                                        <option value="center right" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'bg_position', 'center center'), "center right"); ?>><?php echo t("center right") ?></option>
                                        <option value="center bottom" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'bg_position', 'center center'), "center bottom"); ?>><?php echo t("center bottom") ?></option>
                                        <option value="center center" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'bg_position', 'center center'), "center center"); ?>><?php echo t("center center") ?></option>
                                        <option value="left top" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'bg_position', 'center center'), "left top"); ?>><?php echo t("left top") ?></option>
                                        <option value="left center" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'bg_position', 'center center'), "left center"); ?>><?php echo t("left center") ?></option>
                                        <option value="left bottom" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'bg_position', 'center center'), "left bottom"); ?>><?php echo t("left bottom") ?></option>
                                        <option value="right top" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'bg_position', 'center center'), "right top"); ?>><?php echo t("right top") ?></option>
                                        <option value="right center" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'bg_position', 'center center'), "right center"); ?>><?php echo t("right center") ?></option>
                                        <option value="right bottom" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'bg_position', 'center center'), "right bottom"); ?>><?php echo t("right bottom") ?></option>
                                    </select>
                                    <div class="clear"></div>
                                </div>
                            </div>    <!-- / VISUAL APPEARANCE -->


                            <!-- VISUAL POSITION -->
                            <div id="visual-position" style="display:none;">
                                        <span class="label" id="label_position"
                                              origtitle="<?php echo t("The position of the slider within the parrent container. (float:left or float:right or with margin:0px auto;). We recomment do use always CENTER, since the slider will auto fill and grow with the wrapping container. Set any border,padding, floating etc. to the wrapping container where the slider embeded instead of using left/right here !") ?>"><?php echo t("Position on the page") ?> </span>
                                <select id="position" class="withlabel" name="position">
                                    <option value="left" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'position', 'center'), "left"); ?>><?php echo t("Left") ?></option>
                                    <option value="center" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'position', 'center'), "center"); ?>><?php echo t("Center") ?></option>
                                    <option value="right" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'position', 'center'), "right"); ?>><?php echo t("Right") ?></option>
                                </select>
                                <div class="clear"></div>


                                <span class="label" id="label_margin_top"
                                      origtitle="<?php echo t("The top margin of the slider wrapper div") ?>"><?php echo t("Margin Top") ?> </span>
                                <input type="text" class="text-sidebar withlabel" id="margin_top"
                                       name="margin_top"
                                       value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'margin_top', '0'); ?>">
                                <span><?php echo t("px") ?></span>
                                <div class="clear"></div>


                                <span class="label" id="label_margin_bottom"
                                      origtitle="<?php echo t("The bottom margin of the slider wrapper div") ?>"><?php echo t("Margin Bottom") ?> </span>
                                <input type="text" class="text-sidebar withlabel" id="margin_bottom"
                                       name="margin_bottom"
                                       value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'margin_bottom', '0'); ?>">
                                <span><?php echo t("px") ?></span>
                                <div class="clear"></div>

                                <div id="leftrightmargins">
                                            <span class="label" id="label_margin_left"
                                                  origtitle="<?php echo t("The left margin of the slider wrapper div") ?>"><?php echo t("Margin Left") ?> </span>
                                    <input type="text" class="text-sidebar withlabel" id="margin_left"
                                           name="margin_left"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'margin_left', '0'); ?>">
                                    <span><?php echo t("px") ?></span>
                                    <div class="clear"></div>

                                    <span class="label" id="label_margin_right"
                                          origtitle="<?php echo t("The right margin of the slider wrapper div") ?>"><?php echo t("Margin Right") ?> </span>
                                    <input type="text" class="text-sidebar withlabel" id="margin_right"
                                           name="margin_right"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'margin_right', '0'); ?>">
                                    <span><?php echo t("px") ?></span>
                                    <div class="clear"></div>
                                </div>
                            </div> <!-- / VISUAL POSITION -->

                            <!-- VISUAL SPINNER -->
                            <div id="visual-spinner" style="display:none;">
                                <div id="spinner_preview">
                                    <div class="tp-loader tp-demo spinner2"
                                         style="background-color: rgb(255, 255, 255);">
                                        <div class="dot1"></div>
                                        <div class="dot2"></div>
                                        <div class="bounce1"></div>
                                        <div class="bounce2"></div>
                                        <div class="bounce3"></div>
                                    </div>
                                </div>
                                <div style="height:15px;width:100%;"></div>
                                <span class="label" id="label_use_spinner"
                                      origtitle="<?php echo t("Select a Spinner for your Slider") ?>"><?php echo t("Choose Spinner") ?> </span>
                                <select id="use_spinner" name="use_spinner" class="withlabel">
                                    <option value="-1" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "use_spinner", "0"), "-1"); ?>><?php echo t("Off") ?></option>
                                    <option value="0" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "use_spinner", "0"), "0"); ?>><?php echo t("0") ?></option>
                                    <option value="1" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "use_spinner", "0"), "1"); ?>><?php echo t("1") ?></option>
                                    <option value="2" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "use_spinner", "0"), "2"); ?>><?php echo t("2") ?></option>
                                    <option value="3" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "use_spinner", "0"), "3"); ?>><?php echo t("3") ?></option>
                                    <option value="4" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "use_spinner", "0"), "4"); ?>><?php echo t("4") ?></option>
                                    <option value="5" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "use_spinner", "0"), "5"); ?>><?php echo t("5") ?></option>
                                </select>
                                <div class="clear"></div>
                                <div id="spinner_color_row">
                                            <span id="label_spinner_color" class="label"
                                                  origtitle="<?php echo t("The Color the Spinner will be shown in") ?>"><?php echo t("Spinner Color") ?> </span>
                                    <input type="text"
                                           class="my-color-field rs-layer-input-field tipsy_enabled_top withlabel"
                                           title="<?php echo t("Font Color") ?>" id="spinner_color"
                                           name="spinner_color"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "spinner_color", "#FFFFFF"); ?>"/>
                                </div>
                                <div class="clear"></div>
                            </div>    <!-- / VISUAL SPINNER -->

                        </div>

                        <script type="text/javascript">
                            document.addEventListener("DOMContentLoaded", function () {
                                /**
                                 * set shadow type
                                 */
                                // SHADOW TYPES
                                jQuery("#shadow_type").change(function () {
                                    var sel = jQuery(this).val();

                                    drawToolBarPreview();
                                });

                                // BACKGROUND IMAGE SCRIPT
                                jQuery('#show_background_image').on("change", function () {
                                    var sbi = jQuery(this);
                                    if (sbi.attr("checked") === "checked") {
                                        jQuery('#background_settings').show();
                                    } else {
                                        jQuery('#background_settings').hide();
                                    }
                                });
                                jQuery('#show_background_image').change();
                                jQuery('#padding').change(drawToolBarPreview).on("keyup", drawToolBarPreview);
                                jQuery('#background_dotted_overlay').change(drawToolBarPreview);

                                // POSITION SCRIPT
                                jQuery('#position').on("change", function () {
                                    var sbi = jQuery(this);
                                    switch (jQuery(this).val()) {
                                        case "left":
                                        case "right":
                                            jQuery('#leftrightmargins').show();
                                            break;
                                        case "center":
                                            jQuery('#leftrightmargins').hide();
                                            break;
                                    }
                                    drawToolBarPreview();
                                });
                                jQuery('#position').change();

                                // SPINNER SCRIPT
                                jQuery('#use_spinner').on("change", function () {
                                    switch (jQuery(this).val()) {
                                        case "-1":
                                        case "0":
                                        case "5":
                                            jQuery('#spinner_color_row').hide();
                                            break;
                                        default:
                                            jQuery('#spinner_color_row').show();
                                            break;
                                    }
                                });
                                jQuery('#use_spinner').change();

                                // TAB CHANGES
                                jQuery('.main-options-small-tabs').find('li').click(function () {
                                    var li = jQuery(this),
                                        ul = li.closest('.main-options-small-tabs'),
                                        ref = li.data('content');

                                    jQuery(ul.find('.selected').data('content')).hide();
                                    ul.find('.selected').removeClass("selected");

                                    jQuery(ref).show();
                                    li.addClass("selected");

                                    if (ref == '#navigation-arrows' || ref == '#navigation-bullets' || ref == '#navigation-tabs' || ref == '#navigation-thumbnails')
                                        jQuery('#navigation-miniimagedimensions').show();
                                    else if (!jQuery('#navigation-settings-wrapper>h3').hasClass("box_closed"))
                                        jQuery('#navigation-miniimagedimensions').hide();

                                })
                            });
                        </script>
                    </div> <!-- END OF LAYOUT VISUAL SETTINGS -->

                    <!-- NAVIGATION SETTINGS -->
                    <div class="setting_box dontshowonhero" id="navigation-settings-wrapper">
                        <h3 class="box_closed"><i class="rs-rp-accordion-icon eg-icon-flickr"></i>
                            <div class="setting_box-arrow"></div>
                            <span><?php echo t('Navigation') ?></span>
                        </h3>

                        <div class="inside" style="display:none;">
                            <ul class="main-options-small-tabs" style="display:inline-block; ">
                                <li id="nav_mp_1" data-content="#navigation-arrows"
                                    class="selected"><?php echo t('Arrows') ?></li>
                                <li id="nav_mp_2"
                                    data-content="#navigation-bullets"><?php echo t('Bullets') ?></li>
                                <li id="nav_mp_3"
                                    data-content="#navigation-tabs"><?php echo t('Tabs') ?></li>
                                <li id="nav_mp_4"
                                    data-content="#navigation-thumbnails"><?php echo t('Thumbs') ?></li>
                                <li id="nav_mp_5"
                                    data-content="#navigation-touch"><?php echo t('Touch') ?></li>
                                <li id="nav_mp_6"
                                    data-content="#navigation-keyboard"><?php echo t('Misc.') ?></li>
                            </ul>

                            <!-- NAVIGATION ARROWS -->
                            <div id="navigation-arrows">
                                        <span class="label" id="label_enable_arrows"
                                              origtitle="<?php echo t("Enable / Disable Arrows") ?>"><?php echo t("Enable Arrows") ?> </span>
                                <input type="checkbox" class="tp-moderncheckbox withlabel" id="enable_arrows"
                                       name="enable_arrows"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "enable_arrows", "off"), "on"); ?>>

                                <div id="nav_arrows_subs">
                                            <span class="label" id="label_rtl_arrows"
                                                  origtitle="<?php echo t("Change Direction of Arrow Functions for RTL Support") ?>"><?php echo t("RTL Direction") ?> </span>
                                    <input type="checkbox" class="tp-moderncheckbox withlabel" id="rtl_arrows"
                                           name="rtl_arrows"
                                           data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "rtl_arrows", "off"), "on"); ?>>
                                    <span class="label triggernavstyle" id="label_navigation_arrow_style"
                                          origtitle="<?php echo t("Look of the navigation Arrows") ?>"><?php echo t("Arrows Style") ?></span>
                                    <select id="navigation_arrow_style" name="navigation_arrow_style"
                                            class=" withlabel triggernavstyle">
                                        <option value="" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'navigation_arrow_style', 'round'), ''); ?>><?php echo t('No Style') ?></option>
                                        <?php
                                        if (!empty($arr_navigations)) {
                                            $mav = RevSliderFunctions::getVal($arrFieldsParams, 'navigation_arrow_style', 'round');
                                            foreach ($arr_navigations as $cur_nav) {
                                                if (isset($cur_nav['markup']['arrows'])) {
                                                    ?>
                                                    <option value="<?php echo RevSliderFunctions::esc_attr($cur_nav['handle']); ?>" <?php RevSliderFunctions::selected($mav, RevSliderFunctions::esc_attr($cur_nav['handle'])); ?>><?php echo RevSliderFunctions::esc_attr($cur_nav['name']); ?></option>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div class="clear"></div>
                                    <span class="label triggernavstyle" id="label_navigation_arrows_preset"
                                          origtitle="<?php echo t("Preset") ?>"><?php echo t("Preset") ?></span>
                                    <select id="navigation_arrows_preset" name="navigation_arrows_preset"
                                            class="withlabel"
                                            data-startvalue="<?php echo RevSliderFunctions::esc_attr(RevSliderFunctions::getVal($arrFieldsParams, 'navigation_arrows_preset', 'default')); ?>">
                                        <option class="never" value="default"
                                                selected="selected"><?php echo t('Default') ?></option>
                                        <option class="never"
                                                value="custom"><?php echo t('Custom') ?></option>
                                    </select>
                                    <div class="clear"></div>

                                    <div data-navtype="arrows" class="toggle-custom-navigation-style-wrapper">
                                        <div class="toggle-custom-navigation-style triggernavstyle"><?php echo t("Toggle Custom Navigation Styles") ?></div>
                                        <div class="toggle-custom-navigation-styletarget navigation_arrow_placeholder triggernavstyle"
                                             style="display:none">
                                        </div>
                                    </div>
                                    <div class="clear"></div>

                                    <h4><?php echo t("Visibility") ?></h4>
                                    <span class="label" id="label_arrows_always_on"
                                          origtitle="<?php echo t("Enable to make arrows always visible. Disable to hide arrows after the defined time.") ?>"><?php echo t("Always Show ") ?></span>
                                    <select id="arrows_always_on" name="arrows_always_on"
                                            class=" withlabel showhidewhat_truefalse"
                                            data-showhidetarget="hide_after_arrow">
                                        <option value="false" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'arrows_always_on', 'false'), "false"); ?>><?php echo t("Yes") ?></option>
                                        <option value="true" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'arrows_always_on', 'false'), "true"); ?>><?php echo t("No") ?></option>
                                    </select>
                                    <div class="clear"></div>

                                    <div id="hide_after_arrow">
                                                <span class="label" id="label_hide_arrows"
                                                      origtitle="<?php echo t("Time after the Arrows will be hidden(Default: 200 ms)") ?>"><?php echo t("Hide After") ?></span>
                                        <input type="text" class="text-sidebar withlabel" id="hide_arrows"
                                               name="hide_arrows"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'hide_arrows', '200'); ?>">
                                        <span><?php echo t("ms") ?></span>
                                        <div class="clear"></div>

                                        <span class="label" id="label_hide_arrows_mobile"
                                              origtitle="<?php echo t("Time after the Arrows will be hidden on Mobile(Default: 1200 ms)") ?>"><?php echo t("Hide After on Mobile") ?></span>
                                        <input type="text" class="text-sidebar withlabel"
                                               id="hide_arrows_mobile" name="hide_arrows_mobile"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'hide_arrows_mobile', '1200'); ?>">
                                        <span><?php echo t("ms") ?></span>
                                        <div class="clear"></div>
                                    </div>

                                    <span class="label" id="label_hide_arrows_on_mobile"
                                          origtitle="<?php echo t("Force Hide Navigation Arrows under width") ?>"><?php echo t("Hide Under") ?> </span>
                                    <input type="checkbox"
                                           class="tp-moderncheckbox withlabel showhidewhat_truefalse"
                                           data-showhidetarget="hide_under_arrow" id="hide_arrows_on_mobile"
                                           name="hide_arrows_on_mobile"
                                           data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "hide_arrows_on_mobile", "off"), "on"); ?>>
                                    <div class="clear"></div>

                                    <div id="hide_under_arrow" class="withsublabels">
                                                <span id="label_arrows_under_hidden" class="label"
                                                      origtitle="<?php echo t("If browser size goes below this value, then Navigation Arrows are hidden.") ?>"><?php echo t("Width") ?> </span>
                                        <input type="text" class="text-sidebar withlabel"
                                               id="arrows_under_hidden" name="arrows_under_hidden"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'arrows_under_hidden', '0'); ?>">
                                        <span><?php echo t("px") ?></span>
                                        <div class="clear"></div>
                                    </div>

                                    <span class="label" id="label_hide_arrows_over"
                                          origtitle="<?php echo t("Force Hide Navigation over width") ?>"><?php echo t("Hide Over") ?> </span>
                                    <input type="checkbox"
                                           class="tp-moderncheckbox withlabel showhidewhat_truefalse"
                                           data-showhidetarget="hide_over_arrow" id="hide_arrows_over"
                                           name="hide_arrows_over"
                                           data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "hide_arrows_over", "off"), "on"); ?>>
                                    <div class="clear"></div>

                                    <div id="hide_over_arrow" class="withsublabels">
                                                <span id="label_arrows_over_hidden" class="label"
                                                      origtitle="<?php echo t("If browser size goes over this value, then Navigation Arrows are hidden.") ?>"><?php echo t("Width") ?> </span>
                                        <input type="text" class="text-sidebar withlabel"
                                               id="arrows_over_hidden" name="arrows_over_hidden"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'arrows_over_hidden', '0'); ?>">
                                        <span><?php echo t("px") ?></span>
                                        <div class="clear"></div>
                                    </div>

                                    <h4><?php echo t("Left Arrow Position") ?></h4>

                                    <span class="label" id="label_leftarrow_align_hor"
                                          origtitle="<?php echo t("Horizontal position of the left arrow.") ?>"><?php echo t("Horizontal Align") ?></span>
                                    <select id="leftarrow_align_hor" name="leftarrow_align_hor"
                                            class="withlabel">
                                        <option value="left" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "leftarrow_align_hor", "left"), "left"); ?>><?php echo t("Left") ?></option>
                                        <option value="center" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "leftarrow_align_hor", "left"), "center"); ?>><?php echo t("Center") ?></option>
                                        <option value="right" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "leftarrow_align_hor", "left"), "right"); ?>><?php echo t("Right") ?></option>
                                    </select>
                                    <div class="clear"></div>


                                    <span class="label" id="label_leftarrow_align_vert"
                                          origtitle="<?php echo t("Vertical position of the left arrow.") ?>"><?php echo t("Vertical Align") ?> </span>
                                    <select id="leftarrow_align_vert" name="leftarrow_align_vert"
                                            class="withlabel">
                                        <option value="top" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "leftarrow_align_vert", "center"), "top"); ?>><?php echo t("Top") ?></option>
                                        <option value="center" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "leftarrow_align_vert", "center"), "center"); ?>><?php echo t("Center") ?></option>
                                        <option value="bottom" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "leftarrow_align_vert", "center"), "bottom"); ?>><?php echo t("Bottom") ?></option>
                                    </select>
                                    <div class="clear"></div>

                                    <span id="label_leftarrow_offset_hor" class="label"
                                          origtitle="<?php echo t("Offset from current horizontal position of of left arrow.") ?>"><?php echo t("Horizontal Offset") ?> </span>
                                    <input type="text" class="text-sidebar withlabel" id="leftarrow_offset_hor"
                                           name="leftarrow_offset_hor"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'leftarrow_offset_hor', '20'); ?>">
                                    <span><?php echo t("px") ?></span>
                                    <div class="clear"></div>

                                    <span id="label_leftarrow_offset_vert" class="label"
                                          origtitle="<?php echo t("Offset from current vertical position of of left arrow.") ?>"><?php echo t("Vertical Offset") ?> </span>
                                    <input type="text" class="text-sidebar withlabel" id="leftarrow_offset_vert"
                                           name="leftarrow_offset_vert"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "leftarrow_offset_vert", "0"); ?>">
                                    <span><?php echo t("px") ?></span>
                                    <div class="clear"></div>

                                    <span class="label" id="label_leftarrow_position"
                                          origtitle="<?php echo t("Position the Left Arrow to Slider or Layer Grid") ?>"><?php echo t("Aligned by") ?></span>
                                    <select id="leftarrow_position" name="leftarrow_position" class="withlabel">
                                        <option value="slider" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "leftarrow_position", "slider"), "slider"); ?>><?php echo t("Slider") ?></option>
                                        <option value="grid" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "leftarrow_position", "slider"), "grid"); ?>><?php echo t("Layer Grid") ?></option>
                                    </select>
                                    <div class="clear"></div>

                                    <h4><?php echo t("Right Arrow Position") ?></h4>
                                    <span class="label" id="label_rightarrow_align_hor"
                                          origtitle="<?php echo t("Horizontal position of the right arrow.") ?>"><?php echo t("Horizontal Align") ?> </span>
                                    <select id="rightarrow_align_hor" name="rightarrow_align_hor"
                                            class="withlabel">
                                        <option value="left" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "rightarrow_align_hor", "right"), "left"); ?>><?php echo t("Left") ?></option>
                                        <option value="center" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "rightarrow_align_hor", "right"), "center"); ?>><?php echo t("Center") ?></option>
                                        <option value="right" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "rightarrow_align_hor", "right"), "right"); ?>><?php echo t("Right") ?></option>
                                    </select>

                                    <div class="clear"></div>


                                    <span id="label_rightarrow_align_vert" class="label"
                                          origtitle="<?php echo t("Vertical position of the right arrow.") ?>"><?php echo t("Vertical Align") ?> </span>
                                    <select id="rightarrow_align_vert" name="rightarrow_align_vert"
                                            class="withlabel">
                                        <option value="top" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "rightarrow_align_vert", "center"), "top"); ?>><?php echo t("Top") ?></option>
                                        <option value="center" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "rightarrow_align_vert", "center"), "center"); ?>><?php echo t("Center") ?></option>
                                        <option value="bottom" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "rightarrow_align_vert", "center"), "bottom"); ?>><?php echo t("Bottom") ?></option>
                                    </select>

                                    <div class="clear"></div>


                                    <span id="label_rightarrow_offset_hor" class="label"
                                          origtitle="<?php echo t("Offset from current horizontal position of of right arrow.") ?>"><?php echo t("Horizontal Offset") ?> </span>
                                    <input type="text" class="text-sidebar withlabel" id="rightarrow_offset_hor"
                                           name="rightarrow_offset_hor"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "rightarrow_offset_hor", "20"); ?>">
                                    <span><?php echo t("px") ?></span>
                                    <div class="clear"></div>


                                    <span id="label_rightarrow_offset_vert" class="label"
                                          origtitle="<?php echo t("Offset from current vertical position of of right arrow.") ?>"><?php echo t("Vertical Offset") ?></span>
                                    <input type="text" class="text-sidebar withlabel"
                                           id="rightarrow_offset_vert" name="rightarrow_offset_vert"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "rightarrow_offset_vert", "0"); ?>">
                                    <span><?php echo t("px") ?></span>
                                    <div class="clear"></div>

                                    <span class="label" id="label_rightarrow_position"
                                          origtitle="<?php echo t("Position the Right Arrow to Slider or Layer Grid") ?>"><?php echo t("Aligned by") ?></span>
                                    <select id="rightarrow_position" name="rightarrow_position"
                                            class="withlabel">
                                        <option value="slider" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "rightarrow_position", "slider"), "slider"); ?>><?php echo t("Slider") ?></option>
                                        <option value="grid" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "rightarrow_position", "slider"), "grid"); ?>><?php echo t("Layer Grid") ?></option>
                                    </select>
                                    <div class="clear"></div>
                                </div>
                            </div><!-- END OF NAVIGATION ARROWS -->

                            <!-- NAVIGATION BULLETS -->
                            <div id="navigation-bullets" style="display:none;">

                                        <span class="label" id="label_enable_bullets"
                                              origtitle="<?php echo t("Enable / Disable Bullets") ?>"><?php echo t("Enable Bullets") ?> </span>
                                <input type="checkbox" class="tp-moderncheckbox withlabel" id="enable_bullets"
                                       name="enable_bullets"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "enable_bullets", "off"), "on"); ?>>

                                <div id="nav_bullets_subs">
                                            <span class="label" id="label_rtl_bullets"
                                                  origtitle="<?php echo t("Change Direction of Bullet Functions for RTL Support") ?>"><?php echo t("RTL Direction") ?> </span>
                                    <input type="checkbox" class="tp-moderncheckbox withlabel" id="rtl_bullets"
                                           name="rtl_bullets"
                                           data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "rtl_bullets", "off"), "on"); ?>>

                                    <span class="label triggernavstyle" id="label_navigation_bullets_style"
                                          origtitle="<?php echo t("Look of the Bullets") ?>"><?php echo t("Bullet Style") ?></span>
                                    <select id="navigation_bullets_style" name="navigation_bullets_style"
                                            class="triggernavstyle withlabel">
                                        <option value="" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'navigation_bullets_style', 'round'), ''); ?>><?php echo t('No Style') ?></option>
                                        <?php
                                        if (!empty($arr_navigations)) {
                                            foreach ($arr_navigations as $cur_nav) {
                                                if (isset($cur_nav['markup']['bullets'])) {
                                                    ?>
                                                    <option value="<?php echo RevSliderFunctions::esc_attr($cur_nav['handle']); ?>" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'navigation_bullets_style', 'round'), RevSliderFunctions::esc_attr($cur_nav['handle'])); ?>><?php echo RevSliderFunctions::esc_attr($cur_nav['name']); ?></option>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div class="clear"></div>
                                    <span class="label triggernavstyle" id="label_navigation_bullets_preset"
                                          origtitle="<?php echo t("Preset") ?>"><?php echo t("Preset") ?></span>
                                    <select id="navigation_bullets_preset" name="navigation_bullets_preset"
                                            class="triggernavstyle withlabel"
                                            data-startvalue="<?php echo RevSliderFunctions::esc_attr(RevSliderFunctions::getVal($arrFieldsParams, 'navigation_bullets_preset', 'default')); ?>">
                                        <option class="never" value="default"
                                                selected="selected"><?php echo t('Default') ?></option>
                                        <option class="never"
                                                value="custom"><?php echo t('Custom') ?></option>
                                    </select>
                                    <div class="clear"></div>
                                    <div data-navtype="bullets" class="toggle-custom-navigation-style-wrapper">
                                        <div class="toggle-custom-navigation-style"><?php echo t("Toggle Custom Navigation Styles") ?></div>
                                        <div class="toggle-custom-navigation-styletarget navigation_bullets_placeholder">
                                        </div>
                                    </div>
                                    <div class="clear"></div>

                                    <span class="label" id="label_bullets_space"
                                          origtitle="<?php echo t("Space between the bullets.") ?>"><?php echo t("Space") ?></span>
                                    <input type="text" class="text-sidebar withlabel" id="bullets_space"
                                           name="bullets_space"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'bullets_space', '5'); ?>">
                                    <span><?php echo t("px") ?></span>
                                    <div class="clear"></div>


                                    <span class="label" id="label_bullets_direction"
                                          origtitle="<?php echo t("Direction of the Bullets. Vertical or Horizontal.") ?>"><?php echo t("Direction") ?></span>
                                    <select id="bullets_direction" name="bullets_direction" class=" withlabel">
                                        <option value="horizontal" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'bullets_direction', 'horizontal'), "horizontal"); ?>><?php echo t("Horizontal") ?></option>
                                        <option value="vertical" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'bullets_direction', 'horizontal'), "vertical"); ?>><?php echo t("Vertical") ?></option>
                                    </select>
                                    <div class="clear"></div>


                                    <h4><?php echo t("Visibility") ?></h4>

                                    <span class="label" id="label_bullets_always_on"
                                          origtitle="<?php echo t("Enable to make bullets always visible. Disable to hide bullets after the defined time.") ?>"><?php echo t("Always Show") ?></span>
                                    <select id="bullets_always_on" name="bullets_always_on"
                                            class=" withlabel showhidewhat_truefalse"
                                            data-showhidetarget="hide_after_bullets">
                                        <option value="false" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'bullets_always_on', 'false'), "false"); ?>><?php echo t("Yes") ?></option>
                                        <option value="true" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'bullets_always_on', 'false'), "true"); ?>><?php echo t("No") ?></option>
                                    </select>
                                    <div class="clear"></div>
                                    <div id="hide_after_bullets">
                                                <span class="label" id="label_hide_bullets"
                                                      origtitle="<?php echo t("Time after that the bullets will be hidden(Default: 200 ms)") ?>"><?php echo t("Hide After") ?></span>
                                        <input type="text" class="text-sidebar withlabel" id="hide_bullets"
                                               name="hide_bullets"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'hide_bullets', '200'); ?>">
                                        <span><?php echo t("ms") ?></span>
                                        <div class="clear"></div>

                                        <span class="label" id="label_hide_bullets_mobile"
                                              origtitle="<?php echo t("Time after the bullets will be hidden on Mobile (Default: 1200 ms)") ?>"><?php echo t("Hide After on Mobile") ?></span>
                                        <input type="text" class="text-sidebar withlabel"
                                               id="hide_bullets_mobile" name="hide_bullets_mobile"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'hide_bullets_mobile', '1200'); ?>">
                                        <span><?php echo t("ms") ?></span>
                                        <div class="clear"></div>
                                    </div>

                                    <span class="label" id="label_hide_bullets_on_mobile"
                                          origtitle="<?php echo t("Force Hide Navigation Bullets under width") ?>"><?php echo t("Hide under Width") ?> </span>
                                    <input type="checkbox"
                                           class="tp-moderncheckbox withlabel showhidewhat_truefalse"
                                           data-showhidetarget="hide_under_bullet" id="hide_bullets_on_mobile"
                                           name="hide_bullets_on_mobile"
                                           data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "hide_bullets_on_mobile", "off"), "on"); ?>>
                                    <div class="clear"></div>

                                    <div id="hide_under_bullet" class="withsublabels">
                                                <span id="label_bullets_under_hidden" class="label"
                                                      origtitle="<?php echo t("If browser size goes below this value, then Navigation bullets are hidden.") ?>"><?php echo t("Width") ?> </span>
                                        <input type="text" class="text-sidebar withlabel"
                                               id="bullets_under_hidden" name="bullets_under_hidden"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'bullets_under_hidden', '0'); ?>">
                                        <span><?php echo t("px") ?></span>
                                        <div class="clear"></div>
                                    </div>

                                    <span class="label" id="label_hide_bullets_over"
                                          origtitle="<?php echo t("Force Hide Navigation Bullets over width") ?>"><?php echo t("Hide over Width") ?> </span>
                                    <input type="checkbox"
                                           class="tp-moderncheckbox withlabel showhidewhat_truefalse"
                                           data-showhidetarget="hide_over_bullet" id="hide_bullets_over"
                                           name="hide_bullets_over"
                                           data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "hide_bullets_over", "off"), "on"); ?>>
                                    <div class="clear"></div>

                                    <div id="hide_over_bullet" class="withsublabels">
                                                <span id="label_bullets_over_hidden" class="label"
                                                      origtitle="<?php echo t("If browser size goes below this value, then Navigation bullets are hidden.") ?>"><?php echo t("Width") ?> </span>
                                        <input type="text" class="text-sidebar withlabel"
                                               id="bullets_over_hidden" name="bullets_over_hidden"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'bullets_over_hidden', '0'); ?>">
                                        <span><?php echo t("px") ?></span>
                                        <div class="clear"></div>
                                    </div>

                                    <h4><?php echo t("Position") ?></h4>
                                    <span class="label" id="label_bullets_align_hor"
                                          origtitle="<?php echo t("Horizontal position of bullets "); ?>"><?php echo t("Horizontal Align") ?></span>
                                    <select id="bullets_align_hor" name="bullets_align_hor" class=" withlabel">
                                        <option value="left" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'bullets_align_hor', 'center'), "left"); ?>><?php echo t("Left") ?></option>
                                        <option value="center" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'bullets_align_hor', 'center'), "center"); ?>><?php echo t("Center") ?></option>
                                        <option value="right" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'bullets_align_hor', 'center'), "right"); ?>><?php echo t("Right") ?></option>
                                    </select>
                                    <div class="clear"></div>

                                    <span class="label" id="label_bullets_align_vert"
                                          origtitle="<?php echo t("Vertical positions of bullets ") ?>"><?php echo t("Vertical Align") ?></span>
                                    <select id="bullets_align_vert" name="bullets_align_vert" class="withlabel">
                                        <option value="top" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'bullets_align_vert', 'bottom'), "top"); ?>><?php echo t("Top") ?></option>
                                        <option value="center" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'bullets_align_vert', 'bottom'), "center"); ?>><?php echo t("Center") ?></option>
                                        <option value="bottom" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'bullets_align_vert', 'bottom'), "bottom"); ?>><?php echo t("Bottom") ?></option>
                                    </select>
                                    <div class="clear"></div>

                                    <span class="label" id="label_bullets_offset_hor"
                                          origtitle="<?php echo t("Offset from current horizontal position.") ?>"><?php echo t("Horizontal Offset") ?></span>
                                    <input type="text" class="text-sidebar withlabel" id="bullets_offset_hor"
                                           name="bullets_offset_hor"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'bullets_offset_hor', '0'); ?>">
                                    <span><?php echo t("px") ?></span>
                                    <div class="clear"></div>


                                    <span class="label" id="label_bullets_offset_vert"
                                          origtitle="<?php echo t("Offset from current Vertical  position.") ?>"><?php echo t("Vertical Offset") ?></span>
                                    <input type="text" class="text-sidebar withlabel" id="bullets_offset_vert"
                                           name="bullets_offset_vert"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'bullets_offset_vert', '20'); ?>">
                                    <span><?php echo t("px") ?></span>
                                    <div class="clear"></div>

                                    <span class="label" id="label_bullets_position"
                                          origtitle="<?php echo t("Position the Bullets to Slider or Layer Grid") ?>"><?php echo t("Aligned by") ?></span>
                                    <select id="bullets_position" name="bullets_position" class="withlabel">
                                        <option value="slider" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "bullets_position", "slider"), "slider"); ?>><?php echo t("Slider") ?></option>
                                        <option value="grid" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "bullets_position", "slider"), "grid"); ?>><?php echo t("Layer Grid") ?></option>
                                    </select>
                                    <div class="clear"></div>

                                </div>
                            </div><!-- END OF NAVIGATION BULLETS -->


                            <!-- NAVIGATION THUMBNAILS -->
                            <div id="navigation-thumbnails" style="display:none;">
                                        <span class="label" id="label_enable_thumbnails"
                                              origtitle="<?php echo t("Enable / Disable Thumbnails") ?>"><?php echo t("Enable Thumbnails") ?> </span>
                                <input type="checkbox" class="tp-moderncheckbox withlabel"
                                       id="enable_thumbnails" name="enable_thumbnails"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "enable_thumbnails", "off"), "on"); ?>>

                                <div id="nav_thumbnails_subs">
                                            <span class="label" id="label_rtl_thumbnails"
                                                  origtitle="<?php echo t("Change Direction of thumbnail Functions for RTL Support") ?>"><?php echo t("RTL Direction") ?> </span>
                                    <input type="checkbox" class="tp-moderncheckbox withlabel"
                                           id="rtl_thumbnails" name="rtl_thumbnails"
                                           data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "rtl_thumbnails", "off"), "on"); ?>>

                                    <h4><?php echo t("Wrapper Container") ?></h4>

                                    <span class="label" id="label_thumbnails_padding"
                                          origtitle="<?php echo t("The wrapper div padding of thumbnails") ?>"><?php echo t("Wrapper Padding") ?> </span>
                                    <input type="text" class="text-sidebar withlabel" id="thumbnails_padding"
                                           name="thumbnails_padding"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'thumbnails_padding', '5'); ?>">
                                    <div class="clear"></div>

                                    <span class="label" id="label_span_thumbnails_wrapper"
                                          origtitle="<?php echo t("Span wrapper to full width or full height based on the direction selected") ?>"><?php echo t("Span Wrapper") ?> </span>
                                    <input type="checkbox" class="tp-moderncheckbox withlabel"
                                           id="span_thumbnails_wrapper" name="span_thumbnails_wrapper"
                                           data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "span_thumbnails_wrapper", "off"), "on"); ?>>


                                    <span class="label" id="label_thumbnails_wrapper_color"
                                          origtitle="<?php echo t("Thumbnails wrapper background color. For transparent leave empty.") ?>"><?php echo t("Wrapper color") ?> </span>
                                    <input type="text"
                                           class="my-color-field rs-layer-input-field tipsy_enabled_top withlabel"
                                           title="<?php echo t("Wrapper Color") ?>"
                                           id="thumbnails_wrapper_color"
                                           data-editing="Thumbnails Wrapper BG Color"
                                           name="thumbnails_wrapper_color"
                                           value="<?php echo TPColorpicker::convert(RevSliderFunctions::getVal($arrFieldsParams, 'thumbnails_wrapper_color', 'transparent'), RevSliderFunctions::getVal($arrFieldsParams, 'thumbnails_wrapper_opacity', false)); ?>"/>
                                    <div class="clear"></div>

                                    <h4><?php echo t("Thumbnails") ?></h4>

                                    <span class="label triggernavstyle" id="label_thumbnails_style"
                                          origtitle="<?php echo t("Style of the thumbnails.") ?>"><?php echo t("Thumbnails Style") ?></span>
                                    <select id="thumbnails_style" name="thumbnails_style"
                                            class="triggernavstyle withlabel">
                                        <option value="" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'thumbnails_style', 'round'), ''); ?>><?php echo t('No Style') ?></option>
                                        <?php
                                        if (!empty($arr_navigations)) {
                                            foreach ($arr_navigations as $cur_nav) {
                                                if (isset($cur_nav['markup']['thumbs'])) {
                                                    ?>
                                                    <option value="<?php echo RevSliderFunctions::esc_attr($cur_nav['handle']); ?>" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'thumbnails_style', 'round'), RevSliderFunctions::esc_attr($cur_nav['handle'])); ?>><?php echo RevSliderFunctions::esc_attr($cur_nav['name']); ?></option>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div class="clear"></div>
                                    <span class="label triggernavstyle" id="label_navigation_thumbs_preset"
                                          origtitle="<?php echo t("Preset") ?>"><?php echo t("Preset") ?></span>
                                    <select id="navigation_thumbs_preset" name="navigation_thumbs_preset"
                                            class="withlabel triggernavstyle"
                                            data-startvalue="<?php echo RevSliderFunctions::esc_attr(RevSliderFunctions::getVal($arrFieldsParams, 'navigation_thumbs_preset', 'default')); ?>">
                                        <option class="never" value="default"
                                                selected="selected"><?php echo t('Default') ?></option>
                                        <option class="never"
                                                value="custom"><?php echo t('Custom') ?></option>
                                    </select>
                                    <div class="clear"></div>
                                    <div data-navtype="thumbs" class="toggle-custom-navigation-style-wrapper">
                                        <div class="toggle-custom-navigation-style"><?php echo t("Toggle Custom Navigation Styles") ?></div>
                                        <div class="toggle-custom-navigation-styletarget navigation_thumbs_placeholder">
                                        </div>
                                    </div>
                                    <div class="clear"></div>

                                    <span id="label_thumb_amount" class="label"
                                          origtitle="<?php echo t("The amount of max visible Thumbnails in the same time. ") ?>"><?php echo t("Visible Thumbs Amount") ?></span>
                                    <input type="text" class="text-sidebar withlabel" id="thumb_amount"
                                           name="thumb_amount"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "thumb_amount", "5"); ?>">
                                    <div class="clear"></div>

                                    <span class="label" id="label_thumbnails_space"
                                          origtitle="<?php echo t("Space between the thumbnails.") ?>"><?php echo t("Space") ?></span>
                                    <input type="text" class="text-sidebar withlabel" id="thumbnails_space"
                                           name="thumbnails_space"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'thumbnails_space', '5'); ?>">
                                    <span><?php echo t("px") ?></span>
                                    <div class="clear"></div>

                                    <span class="label" id="label_thumbnail_direction"
                                          origtitle="<?php echo t("Direction of the Thumbnails. Vertical or Horizontal.") ?>"><?php echo t("Direction") ?></span>
                                    <select id="thumbnail_direction" name="thumbnail_direction"
                                            class=" withlabel">
                                        <option value="horizontal" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'thumbnail_direction', 'horizontal'), "horizontal"); ?>><?php echo t("Horizontal") ?></option>
                                        <option value="vertical" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'thumbnail_direction', 'horizontal'), "vertical"); ?>><?php echo t("Vertical") ?></option>
                                    </select>
                                    <div class="clear"></div>

                                    <h4><?php echo t("Thumbnail Container Size") ?></h4>

                                    <span id="label_thumb_width" class="label"
                                          origtitle="<?php echo t("The basic Width of one Thumbnail Container.") ?>"><?php echo t("Container Width") ?></span>
                                    <input type="text" class="text-sidebar withlabel" id="thumb_width"
                                           name="thumb_width"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "thumb_width", "100"); ?>">
                                    <span><?php echo t("px") ?></span>
                                    <div class="clear"></div>

                                    <span id="label_thumb_height" class="label"
                                          origtitle="<?php echo t("The basic Height of one Thumbnail.") ?>"><?php echo t("Container Height") ?></span>
                                    <input type="text" class="text-sidebar withlabel" id="thumb_height"
                                           name="thumb_height"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "thumb_height", "50"); ?>">
                                    <span><?php echo t("px") ?></span>
                                    <div class="clear"></div>

                                    <span id="label_thumb_width_min" class="label"
                                          origtitle="<?php echo t("The minimum width of the auto resized thumbs. Between Max and Min width the sizes are auto calculated).") ?>"><?php echo t("Min Container Width") ?></span>
                                    <input type="text" class="text-sidebar withlabel" id="thumb_width_min"
                                           name="thumb_width_min"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "thumb_width_min", "100"); ?>">
                                    <span><?php echo t("px") ?></span>
                                    <div class="clear"></div>


                                    <h4><?php echo t("Visibility") ?></h4>

                                    <span class="label" id="label_thumbs_always_on"
                                          origtitle="<?php echo t("Enable to make thumbnails always visible. Disable to hide thumbnails after the defined time.") ?>"><?php echo t("Always Show ") ?></span>
                                    <select id="thumbs_always_on" name="thumbs_always_on"
                                            class=" withlabel showhidewhat_truefalse"
                                            data-showhidetarget="hide_after_thumbs">
                                        <option value="false" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'thumbs_always_on', 'false'), "false"); ?>><?php echo t("Yes") ?></option>
                                        <option value="true" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'thumbs_always_on', 'false'), "true"); ?>><?php echo t("No") ?></option>
                                    </select>
                                    <div class="clear"></div>

                                    <div id="hide_after_thumbs">
                                                <span class="label" id="label_hide_thumbs"
                                                      origtitle="<?php echo t("Time after that the thumbnails will be hidden(Default: 200 ms)") ?>"><?php echo t("Hide After") ?></span>
                                        <input type="text" class="text-sidebar withlabel" id="hide_thumbs"
                                               name="hide_thumbs"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'hide_thumbs', '200'); ?>">
                                        <span><?php echo t("ms") ?></span>
                                        <div class="clear"></div>

                                        <span class="label" id="label_hide_thumbs_mobile"
                                              origtitle="<?php echo t("Time after that the thumbnails will be hidden on Mobile (Default: 1200 ms)") ?>"><?php echo t("Hide After on Mobile") ?></span>
                                        <input type="text" class="text-sidebar withlabel"
                                               id="hide_thumbs_mobile" name="hide_thumbs_mobile"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'hide_thumbs_mobile', '1200'); ?>">
                                        <span><?php echo t("ms") ?></span>
                                        <div class="clear"></div>
                                    </div>

                                    <span class="label" id="label_hide_thumbs_on_mobile"
                                          origtitle="<?php echo t("Force Hide Navigation Thumbnails under width") ?>"><?php echo t("Hide under Width") ?> </span>
                                    <input type="checkbox"
                                           class="tp-moderncheckbox withlabel showhidewhat_truefalse"
                                           data-showhidetarget="hide_under_thumb" id="hide_thumbs_on_mobile"
                                           name="hide_thumbs_on_mobile"
                                           data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "hide_thumbs_on_mobile", "off"), "on"); ?>>
                                    <div class="clear"></div>

                                    <div id="hide_under_thumb" class="withsublabels">
                                                <span id="label_thumbs_under_hidden" class="label"
                                                      origtitle="<?php echo t("If browser size goes below this value, then Navigation thumbs are hidden.") ?>"><?php echo t("Width") ?> </span>
                                        <input type="text" class="text-sidebar withlabel"
                                               id="thumbs_under_hidden" name="thumbs_under_hidden"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'thumbs_under_hidden', '0'); ?>">
                                        <span><?php echo t("px") ?></span>
                                        <div class="clear"></div>
                                    </div>

                                    <span class="label" id="label_hide_thumbs_over"
                                          origtitle="<?php echo t("Force Hide Navigation Thumbnails under width") ?>"><?php echo t("Hide over Width") ?> </span>
                                    <input type="checkbox"
                                           class="tp-moderncheckbox withlabel showhidewhat_truefalse"
                                           data-showhidetarget="hide_over_thumb" id="hide_thumbs_over"
                                           name="hide_thumbs_over"
                                           data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "hide_thumbs_over", "off"), "on"); ?>>
                                    <div class="clear"></div>

                                    <div id="hide_over_thumb" class="withsublabels">
                                                <span id="label_thumbs_over_hidden" class="label"
                                                      origtitle="<?php echo t("If browser size goes below this value, then Navigation thumbs are hidden.") ?>"><?php echo t("Width") ?> </span>
                                        <input type="text" class="text-sidebar withlabel"
                                               id="thumbs_over_hidden" name="thumbs_over_hidden"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'thumbs_over_hidden', '0'); ?>">
                                        <span><?php echo t("px") ?></span>
                                        <div class="clear"></div>
                                    </div>

                                    <h4><?php echo t("Position") ?></h4>
                                    <span class="label" id="label_thumbnails_inner_outer"
                                          origtitle="<?php echo t("Put the thumbnails inside or outside of the slider container. Outside added thumbnails will decrease the size of the slider."); ?>"><?php echo t("Inner / outer") ?></span>
                                    <select id="thumbnails_inner_outer" name="thumbnails_inner_outer"
                                            class=" withlabel">
                                        <option value="inner" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'thumbnails_inner_outer', 'inner'), "inner"); ?>><?php echo t("Inner Slider") ?></option>
                                        <option value="outer-left" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'thumbnails_inner_outer', 'inner'), "outer-left"); ?>><?php echo t("Outer Left") ?></option>
                                        <option value="outer-right" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'thumbnails_inner_outer', 'inner'), "outer-right"); ?>><?php echo t("Outer Right") ?></option>
                                        <option value="outer-top" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'thumbnails_inner_outer', 'inner'), "outer-top"); ?>><?php echo t("Outer Top") ?></option>
                                        <option value="outer-bottom" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'thumbnails_inner_outer', 'inner'), "outer-bottom"); ?>><?php echo t("Outer Bottom") ?></option>

                                    </select>
                                    <div class="clear"></div>

                                    <span class="label" id="label_thumbnails_align_hor"
                                          origtitle="<?php echo t("Horizontal position of thumbnails"); ?>"><?php echo t("Horizontal Align") ?></span>
                                    <select id="thumbnails_align_hor" name="thumbnails_align_hor"
                                            class=" withlabel">
                                        <option value="left" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'thumbnails_align_hor', 'center'), "left"); ?>><?php echo t("Left") ?></option>
                                        <option value="center" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'thumbnails_align_hor', 'center'), "center"); ?>><?php echo t("Center") ?></option>
                                        <option value="right" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'thumbnails_align_hor', 'center'), "right"); ?>><?php echo t("Right") ?></option>
                                    </select>
                                    <div class="clear"></div>

                                    <span class="label" id="label_thumbnails_align_vert"
                                          origtitle="<?php echo t("Vertical position of thumbnails") ?>"><?php echo t("Vertical Align") ?></span>
                                    <select id="thumbnails_align_vert" name="thumbnails_align_vert"
                                            class="withlabel">
                                        <option value="top" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'thumbnails_align_vert', 'bottom'), "top"); ?>><?php echo t("Top") ?></option>
                                        <option value="center" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'thumbnails_align_vert', 'bottom'), "center"); ?>><?php echo t("Center") ?></option>
                                        <option value="bottom" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'thumbnails_align_vert', 'bottom'), "bottom"); ?>><?php echo t("Bottom") ?></option>
                                    </select>
                                    <div class="clear"></div>

                                    <span class="label" id="label_thumbnails_offset_hor"
                                          origtitle="<?php echo t("Offset from current Horizontal position.") ?>"><?php echo t("Horizontal Offset") ?></span>
                                    <input type="text" class="text-sidebar withlabel" id="thumbnails_offset_hor"
                                           name="thumbnails_offset_hor"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'thumbnails_offset_hor', '0'); ?>">
                                    <span><?php echo t("px") ?></span>
                                    <div class="clear"></div>


                                    <span class="label" id="label_thumbnails_offset_vert"
                                          origtitle="<?php echo t("Offset from current Vertical position.") ?>"><?php echo t("Vertical Offset") ?></span>
                                    <input type="text" class="text-sidebar withlabel"
                                           id="thumbnails_offset_vert" name="thumbnails_offset_vert"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'thumbnails_offset_vert', '20'); ?>">
                                    <span><?php echo t("px") ?></span>
                                    <div class="clear"></div>

                                    <span class="label" id="label_thumbnails_position"
                                          origtitle="<?php echo t("Position the Thumbnails to Slider or Layer Grid") ?>"><?php echo t("Aligned by") ?></span>
                                    <select id="thumbnails_position" name="thumbnails_position"
                                            class="withlabel">
                                        <option value="slider" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "thumbnails_position", "slider"), "slider"); ?>><?php echo t("Slider") ?></option>
                                        <option value="grid" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "thumbnails_position", "slider"), "grid"); ?>><?php echo t("Layer Grid") ?></option>
                                    </select>
                                    <div class="clear"></div>
                                </div>
                            </div>
                            <!-- END OF NAVIGATION THUMBNAILS -->

                            <!-- NAVIGATION TABS-->
                            <div id="navigation-tabs" style="display:none;">

                                        <span class="label" id="label_enable_tabs"
                                              origtitle="<?php echo t("Enable / Disable navigation tabs.") ?>"><?php echo t("Enable Tabs") ?> </span>
                                <input type="checkbox" class="tp-moderncheckbox withlabel" id="enable_tabs"
                                       name="enable_tabs"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "enable_tabs", "off"), "on"); ?>>

                                <div id="nav_tabs_subs">

                                            <span class="label" id="label_rtl_tabs"
                                                  origtitle="<?php echo t("Change Direction of tab Functions for RTL Support") ?>"><?php echo t("RTL Direction") ?> </span>
                                    <input type="checkbox" class="tp-moderncheckbox withlabel" id="rtl_tabs"
                                           name="rtl_tabs"
                                           data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "rtl_tabs", "off"), "on"); ?>>

                                    <h4><?php echo t("Wrapper Container") ?></h4>

                                    <span class="label" id="label_tabs_padding"
                                          origtitle="<?php echo t("The wrapper div padding of tabs.") ?>"><?php echo t("Wrapper Padding") ?> </span>
                                    <input type="text" class="text-sidebar withlabel" id="tabs_padding"
                                           name="tabs_padding"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'tabs_padding', '5'); ?>">
                                    <div class="clear"></div>

                                    <span class="label" id="label_span_tabs_wrapper"
                                          origtitle="<?php echo t("Span wrapper to full width or full height based on the direction selected.") ?>"><?php echo t("Span Wrapper") ?> </span>
                                    <input type="checkbox" class="tp-moderncheckbox withlabel"
                                           id="span_tabs_wrapper" name="span_tabs_wrapper"
                                           data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "span_tabs_wrapper", "off"), "on"); ?>>


                                    <span class="label" id="label_tabs_wrapper_color"
                                          origtitle="<?php echo t("Tabs wrapper background color. For transparent leave empty.") ?>"><?php echo t("Wrapper Color") ?> </span>
                                    <input type="text"
                                           class="my-color-field rs-layer-input-field tipsy_enabled_top withlabel"
                                           title="<?php echo t("Wrapper Color") ?>"
                                           id="tabs_wrapper_color" name="tabs_wrapper_color"
                                           data-editing="Tabs Wrapped BG Color"
                                           value="<?php echo TPColorpicker::convert(RevSliderFunctions::getVal($arrFieldsParams, 'tabs_wrapper_color', 'transparent'), RevSliderFunctions::getVal($arrFieldsParams, 'tabs_wrapper_opacity', false)); ?>"/>
                                    <div class="clear"></div>

                                    <h4><?php echo t("Tabs") ?></h4>

                                    <span class="triggernavstyle label" id="label_tabs_style"
                                          origtitle="<?php echo t("Style of the tabs.") ?>"><?php echo t("Tabs Style") ?></span>
                                    <select id="tabs_style" name="tabs_style" class="triggernavstyle withlabel">
                                        <option value="" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'tabs_style', 'round'), ''); ?>><?php echo t('No Style') ?></option>
                                        <?php
                                        if (!empty($arr_navigations)) {
                                            foreach ($arr_navigations as $cur_nav) {
                                                if (isset($cur_nav['markup']['tabs'])) {
                                                    ?>
                                                    <option value="<?php echo RevSliderFunctions::esc_attr($cur_nav['handle']); ?>" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'tabs_style', 'round'), RevSliderFunctions::esc_attr($cur_nav['handle'])); ?>><?php echo RevSliderFunctions::esc_attr($cur_nav['name']); ?></option>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                    <script>
                                        document.addEventListener("DOMContentLoaded", function () {


                                            function checkMetis() {
                                                var v = jQuery('#tabs_style').val().toLowerCase();
                                                if (v.indexOf('metis') >= 0) {
                                                    jQuery('#tabs_padding').val(0).attr('disabled', 'disabled');

                                                    jQuery('#tabs_width_min').val(0).attr('disabled', 'disabled');
                                                    jQuery('#tabs_direction').val('vertical').attr('disabled', 'disabled');
                                                    jQuery('#tabs_align_hor').val('left').attr('disabled', 'disabled');
                                                    jQuery('#span_tabs_wrapper').attr('checked', 'checked');
                                                    RevSliderSettings.onoffStatus(jQuery('#span_tabs_wrapper'));
                                                } else {
                                                    jQuery('#tabs_padding').removeAttr('disabled');

                                                    jQuery('#tabs_direction').removeAttr('disabled');
                                                    jQuery('#tabs_width_min').removeAttr('disabled');
                                                    jQuery('#tabs_align_hor').removeAttr('disabled');
                                                }

                                            }

                                            checkMetis();
                                            jQuery('#tabs_style').change(checkMetis);
                                        });
                                    </script>
                                    <div class="clear"></div>
                                    <span class="label triggernavstyle" id="label_navigation_tabs_preset"
                                          origtitle="<?php echo t("Preset") ?>"><?php echo t("Preset") ?></span>
                                    <select id="navigation_tabs_preset" name="navigation_tabs_preset"
                                            class="withlabel triggernavstyle"
                                            data-startvalue="<?php echo RevSliderFunctions::esc_attr(RevSliderFunctions::getVal($arrFieldsParams, 'navigation_tabs_preset', 'default')); ?>">
                                        <option class="never" value="default"
                                                selected="selected"><?php echo t('Default') ?></option>
                                        <option class="never"
                                                value="custom"><?php echo t('Custom') ?></option>
                                    </select>
                                    <div class="clear"></div>
                                    <div data-navtype="tabs" class="toggle-custom-navigation-style-wrapper">
                                        <div class="toggle-custom-navigation-style"><?php echo t("Toggle Custom Navigation Styles") ?></div>
                                        <div class="toggle-custom-navigation-styletarget navigation_tabs_placeholder">
                                        </div>
                                    </div>
                                    <div class="clear"></div>

                                    <span id="label_tabs_amount" class="label"
                                          origtitle="<?php echo t("The amount of max visible tabs in same time.") ?>"><?php echo t("Visible Tabs Amount") ?></span>
                                    <input type="text" class="text-sidebar withlabel" id="tabs_amount"
                                           name="tabs_amount"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "tabs_amount", "5"); ?>">
                                    <div class="clear"></div>

                                    <span class="label" id="label_tabs_space"
                                          origtitle="<?php echo t("Space between the tabs.") ?>"><?php echo t("Space") ?></span>
                                    <input type="text" class="text-sidebar withlabel" id="tabs_space"
                                           name="tabs_space"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'tabs_space', '5'); ?>">
                                    <span><?php echo t("px") ?></span>
                                    <div class="clear"></div>

                                    <span class="label" id="label_tabs_direction"
                                          origtitle="<?php echo t("Direction of the Tabs. Vertical or Horizontal.") ?>"><?php echo t("Direction") ?></span>
                                    <select id="tabs_direction" name="tabs_direction" class=" withlabel">
                                        <option value="horizontal" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'tabs_direction', 'horizontal'), "horizontal"); ?>><?php echo t("Horizontal") ?></option>
                                        <option value="vertical" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'tabs_direction', 'horizontal'), "vertical"); ?>><?php echo t("Vertical") ?></option>
                                    </select>
                                    <div class="clear"></div>

                                    <h4><?php echo t("Tab Sizes") ?></h4>

                                    <span id="label_tabs_width" class="label"
                                          origtitle="<?php echo t("The basic width of one tab.") ?>"><?php echo t("Tabs Width") ?></span>
                                    <input type="text" class="text-sidebar withlabel" id="tabs_width"
                                           name="tabs_width"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "tabs_width", "100"); ?>">
                                    <span><?php echo t("px") ?></span>
                                    <div class="clear"></div>

                                    <span id="label_tabs_height" class="label"
                                          origtitle="<?php echo t("the basic height of one tab.") ?>"><?php echo t("Tabs Height") ?></span>
                                    <input type="text" class="text-sidebar withlabel" id="tabs_height"
                                           name="tabs_height"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "tabs_height", "50"); ?>">
                                    <span><?php echo t("px") ?></span>
                                    <div class="clear"></div>

                                    <span id="label_tabs_width_min" class="label"
                                          origtitle="<?php echo t("The minimum width of the auto resized Tabs. Between Max and Min width the sizes are auto calculated).") ?>"><?php echo t("Min. Tab Width") ?></span>
                                    <input type="text" class="text-sidebar withlabel" id="tabs_width_min"
                                           name="tabs_width_min"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "tabs_width_min", "100"); ?>">
                                    <span><?php echo t("px") ?></span>
                                    <div class="clear"></div>

                                    <h4><?php echo t("Visibility") ?></h4>

                                    <span class="label" id="label_tabs_always_on"
                                          origtitle="<?php echo t("Enable to make tabs always visible. Disable to hide tabs after the defined time.") ?>"><?php echo t("Always Show ") ?></span>
                                    <select id="tabs_always_on" name="tabs_always_on"
                                            class=" withlabel showhidewhat_truefalse"
                                            data-showhidetarget="hide_after_tabs">
                                        <option value="false" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'tabs_always_on', 'false'), "false"); ?>><?php echo t("Yes") ?></option>
                                        <option value="true" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'tabs_always_on', 'false'), "true"); ?>><?php echo t("No") ?></option>
                                    </select>
                                    <div class="clear"></div>

                                    <div id="hide_after_tabs">
                                                <span class="label" id="label_hide_tabs"
                                                      origtitle="<?php echo t("Time after that the tabs will be hidden(Default: 200 ms)") ?>"><?php echo t("Hide  After") ?></span>
                                        <input type="text" class="text-sidebar withlabel" id="hide_tabs"
                                               name="hide_tabs"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'hide_tabs', '200'); ?>">
                                        <span><?php echo t("ms") ?></span>
                                        <div class="clear"></div>
                                        <span class="label" id="label_hide_tabs_mobile"
                                              origtitle="<?php echo t("Time after that the tabs will be hidden on Mobile (Default: 1200 ms)") ?>"><?php echo t("Hide  After on Mobile") ?></span>
                                        <input type="text" class="text-sidebar withlabel" id="hide_tabs_mobile"
                                               name="hide_tabs_mobile"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'hide_tabs_mobile', '1200'); ?>">
                                        <span><?php echo t("ms") ?></span>
                                        <div class="clear"></div>
                                    </div>

                                    <span class="label" id="label_hide_tabs_on_mobile"
                                          origtitle="<?php echo t("Force Hide Navigation tabs under width") ?>"><?php echo t("Hide under Width") ?> </span>
                                    <input type="checkbox"
                                           class="tp-moderncheckbox withlabel showhidewhat_truefalse"
                                           data-showhidetarget="hide_under_tab" id="hide_tabs_on_mobile"
                                           name="hide_tabs_on_mobile"
                                           data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "hide_tabs_on_mobile", "off"), "on"); ?>>
                                    <div class="clear"></div>

                                    <div id="hide_under_tab" class="withsublabels">
                                                <span id="label_tabs_under_hidden" class="label"
                                                      origtitle="<?php echo t("If browser size goes below this value, then Navigation tabs are hidden.") ?>"><?php echo t("Width") ?> </span>
                                        <input type="text" class="text-sidebar withlabel" id="tabs_under_hidden"
                                               name="tabs_under_hidden"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'tabs_under_hidden', '0'); ?>">
                                        <span><?php echo t("px") ?></span>
                                        <div class="clear"></div>
                                    </div>

                                    <span class="label" id="label_hide_tabs_over"
                                          origtitle="<?php echo t("Force Hide Navigation tabs under width") ?>"><?php echo t("Hide over Width") ?> </span>
                                    <input type="checkbox"
                                           class="tp-moderncheckbox withlabel showhidewhat_truefalse"
                                           data-showhidetarget="hide_over_tab" id="hide_tabs_over"
                                           name="hide_tabs_over"
                                           data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "hide_tabs_over", "off"), "on"); ?>>
                                    <div class="clear"></div>

                                    <div id="hide_over_tab" class="withsublabels">
                                                <span id="label_tabs_over_hidden" class="label"
                                                      origtitle="<?php echo t("If browser size goes below this value, then Navigation tabs are hidden.") ?>"><?php echo t("Width") ?> </span>
                                        <input type="text" class="text-sidebar withlabel" id="tabs_over_hidden"
                                               name="tabs_over_hidden"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'tabs_over_hidden', '0'); ?>">
                                        <span><?php echo t("px") ?></span>
                                        <div class="clear"></div>
                                    </div>

                                    <h4><?php echo t("Position") ?></h4>

                                    <span class="label" id="label_tabs_inner_outer"
                                          origtitle="<?php echo t("Put the tabs inside or outside of the slider container. Outside added tabs will decrease the size of the slider."); ?>"><?php echo t("Inner / outer") ?></span>
                                    <select id="tabs_inner_outer" name="tabs_inner_outer" class=" withlabel">
                                        <option value="inner" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'tabs_inner_outer', 'inner'), "inner"); ?>><?php echo t("Inner Slider") ?></option>
                                        <option value="outer-left" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'tabs_inner_outer', 'inner'), "outer-left"); ?>><?php echo t("Outer Left") ?></option>
                                        <option value="outer-right" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'tabs_inner_outer', 'inner'), "outer-right"); ?>><?php echo t("Outer Right") ?></option>
                                        <option value="outer-top" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'tabs_inner_outer', 'inner'), "outer-top"); ?>><?php echo t("Outer Top") ?></option>
                                        <option value="outer-bottom" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'tabs_inner_outer', 'inner'), "outer-bottom"); ?>><?php echo t("Outer Bottom") ?></option>

                                    </select>
                                    <div class="clear"></div>


                                    <span class="label" id="label_tabs_align_hor"
                                          origtitle="<?php echo t("Horizontal position of tabs."); ?>"><?php echo t("Horizontal Align") ?></span>
                                    <select id="tabs_align_hor" name="tabs_align_hor" class=" withlabel">
                                        <option value="left" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'tabs_align_hor', 'center'), "left"); ?>><?php echo t("Left") ?></option>
                                        <option value="center" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'tabs_align_hor', 'center'), "center"); ?>><?php echo t("Center") ?></option>
                                        <option value="right" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'tabs_align_hor', 'center'), "right"); ?>><?php echo t("Right") ?></option>
                                    </select>
                                    <div class="clear"></div>

                                    <span class="label" id="label_tabs_align_vert"
                                          origtitle="<?php echo t("Vertical position of tabs.") ?>"><?php echo t("Vertical Align") ?></span>
                                    <select id="tabs_align_vert" name="tabs_align_vert" class="withlabel">
                                        <option value="top" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'tabs_align_vert', 'bottom'), "top"); ?>><?php echo t("Top") ?></option>
                                        <option value="center" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'tabs_align_vert', 'bottom'), "center"); ?>><?php echo t("Center") ?></option>
                                        <option value="bottom" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'tabs_align_vert', 'bottom'), "bottom"); ?>><?php echo t("Bottom") ?></option>
                                    </select>
                                    <div class="clear"></div>

                                    <span class="label" id="label_tabs_offset_hor"
                                          origtitle="<?php echo t("Offset from current horizontal position of tabs.") ?>"><?php echo t("Horizontal Offset") ?></span>
                                    <input type="text" class="text-sidebar withlabel" id="tabs_offset_hor"
                                           name="tabs_offset_hor"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'tabs_offset_hor', '0'); ?>">
                                    <span><?php echo t("px") ?></span>
                                    <div class="clear"></div>


                                    <span class="label" id="label_tabs_offset_vert"
                                          origtitle="<?php echo t("Offset from current vertical position of tabs.") ?>"><?php echo t("Vertical Offset") ?></span>
                                    <input type="text" class="text-sidebar withlabel" id="tabs_offset_vert"
                                           name="tabs_offset_vert"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'tabs_offset_vert', '20'); ?>">
                                    <span><?php echo t("px") ?></span>
                                    <div class="clear"></div>

                                    <div id="rs-tabs_position_wrapper">
                                                <span class="label" id="label_tabs_position"
                                                      origtitle="<?php echo t("Position the Tabs to Slider or Layer Grid") ?>"><?php echo t("Aligned by") ?></span>
                                        <select id="tabs_position" name="tabs_position" class="withlabel">
                                            <option value="slider" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "tabs_position", "slider"), "slider"); ?>><?php echo t("Slider") ?></option>
                                            <option value="grid" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "tabs_position", "slider"), "grid"); ?>><?php echo t("Layer Grid") ?></option>
                                        </select>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </div>
                            <!-- END OF NAVIGATION TABS-->

                            <!-- TOUCH NAVIGATION -->
                            <div id="navigation-touch" style="display:none;">

                                        <span class="label" id="label_touchenabled"
                                              origtitle="<?php echo t("Enable Swipe Function on touch devices") ?>"><?php echo t("Touch Enabled") ?> </span>
                                <input type="checkbox" class="tp-moderncheckbox withlabel" id="touchenabled"
                                       name="touchenabled"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "touchenabled", "off"), "on"); ?>>
                                <div class="clear"></div>

                                <span class="label" id="label_touchenabled_desktop"
                                      origtitle="<?php echo t("Enable Swipe Function on touch devices") ?>"><?php echo t("Touch Enabled on Desktop") ?> </span>
                                <input type="checkbox" class="tp-moderncheckbox withlabel"
                                       id="touchenabled_desktop" name="touchenabled_desktop"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "touchenabled_desktop", "off"), "on"); ?>>
                                <div class="clear"></div>

                                <span class="label" id="label_drag_block_vertical"
                                      origtitle="<?php echo t("Scroll below slider on vertical swipe") ?>"><?php echo t("Drag Block Vertical") ?> </span>
                                <input type="checkbox" class="tp-moderncheckbox withlabel"
                                       id="drag_block_vertical" name="drag_block_vertical"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "drag_block_vertical", "off"), "on"); ?>>
                                <div class="clear"></div>

                                <span class="label" id="label_swipe_velocity"
                                      origtitle="<?php echo t("Defines the sensibility of gestures. Smaller values mean a higher sensibility") ?>"><?php echo t("Swipe Treshhold (0 - 200)") ?> </span>
                                <input type="text" class="text-sidebar withlabel" id="swipe_velocity"
                                       name="swipe_velocity"
                                       value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "swipe_velocity", "75"); ?>">
                                <div class="clear"></div>

                                <span class="label" id="label_swipe_min_touches"
                                      origtitle="<?php echo t("Defines how many fingers are needed minimum for swiping") ?>"><?php echo t("Swipe Min Finger") ?> </span>
                                <input type="text" class="text-sidebar withlabel" id="swipe_min_touches"
                                       name="swipe_min_touches"
                                       value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "swipe_min_touches", "1"); ?>">
                                <div class="clear"></div>

                                <span class="label" id="label_swipe_direction"
                                      origtitle="<?php echo t("Swipe Direction to swap slides?") ?>"><?php echo t("Swipe Direction") ?></span>
                                <select id="swipe_direction" name="swipe_direction" class="withlabel">
                                    <option value="horizontal" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'swipe_direction', 'horizontal'), "horizontal"); ?>><?php echo t("Horizontal") ?></option>
                                    <option value="vertical" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'swipe_direction', 'horizontal'), "vertical"); ?>><?php echo t("Vertical") ?></option>
                                </select>
                                <div class="clear"></div>

                            </div> <!-- END TOUCH NAVIGATION -->

                            <!-- KEYBOARD NAVIGATION -->
                            <div id="navigation-keyboard" style="display:none;">
                                        <span class="label" id="label_keyboard_navigation"
                                              origtitle="<?php echo t("Allow/disallow to navigate the slider with keyboard.") ?>"><?php echo t("Keyboard Navigation") ?></span>
                                <input type="checkbox" class="tp-moderncheckbox withlabel"
                                       id="keyboard_navigation" name="keyboard_navigation"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, 'keyboard_navigation', 'off'), "on"); ?>>
                                <div class="clear"></div>

                                <span class="label" id="label_keyboard_direction"
                                      origtitle="<?php echo t("Keyboard Direction to swap slides (horizontal - left/right arrow, vertical - up/down arrow)?") ?>"><?php echo t("Key Direction") ?></span>
                                <select id="keyboard_direction" name="keyboard_direction" class="withlabel">
                                    <option value="horizontal" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'keyboard_direction', 'horizontal'), "horizontal"); ?>><?php echo t("Horizontal") ?></option>
                                    <option value="vertical" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'keyboard_direction', 'horizontal'), "vertical"); ?>><?php echo t("Vertical") ?></option>
                                </select>
                                <div class="clear"></div>

                                <span class="label" id="label_mousescroll_navigation"
                                      origtitle="<?php echo t("Allow/disallow to navigate the slider with Mouse Scroll.") ?>"><?php echo t("Mouse Scroll Navigation") ?></span>
                                <select id="mousescroll_navigation" name="mousescroll_navigation"
                                        class="withlabel">
                                    <option value="on" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'mousescroll_navigation', 'off'), "on"); ?>><?php echo t("On") ?></option>
                                    <option value="off" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'mousescroll_navigation', 'off'), "off"); ?>><?php echo t("Off") ?></option>
                                    <option value="carousel" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'mousescroll_navigation', 'off'), "carousel"); ?>><?php echo t("Carousel") ?></option>
                                </select>

                                <span class="label" id="label_mousescroll_navigation_reverse"
                                      origtitle="<?php echo t("Reverse the functionality of the Mouse Scroll Navigation") ?>"><?php echo t("Reverse Mouse Scroll") ?></span>
                                <select id="mousescroll_navigation_reverse"
                                        name="mousescroll_navigation_reverse" class="withlabel">
                                    <option value="reverse" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'mousescroll_navigation_reverse', 'default'), "reverse"); ?>><?php echo t("Reverse") ?></option>
                                    <option value="default" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'mousescroll_navigation_reverse', 'default'), "default"); ?>><?php echo t("Default") ?></option>

                                </select>

                                <div class="clear"></div>

                            </div><!-- END KEYBOARD NAVIGATION -->

                            <!-- PREVIEW IMAGE SIZES -->
                            <div id="navigation-miniimagedimensions"
                                 style="border-top:1px solid #f1f1f1; margin:20px -20px 0px; padding:0px 20px">
                                <h4><?php echo t("Preview Image Size") ?></h4>

                                <span id="label_previewimage_width" class="label"
                                      origtitle="<?php echo t("The basic Width of one Preview Image.") ?>"><?php echo t("Preview Image Width") ?></span>
                                <input type="text" class="text-sidebar withlabel" id="previewimage_width"
                                       name="previewimage_width"
                                       value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "previewimage_width", RevSliderFunctions::getVal($arrFieldsParams, "thumb_width", 100)); ?>">
                                <span><?php echo t("px") ?></span>
                                <div class="clear"></div>

                                <span id="label_previewimage_height" class="label"
                                      origtitle="<?php echo t("The basic Height of one Preview Image.") ?>"><?php echo t("Preview Image Height") ?></span>
                                <input type="text" class="text-sidebar withlabel" id="previewimage_height"
                                       name="previewimage_height"
                                       value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "previewimage_height", RevSliderFunctions::getVal($arrFieldsParams, "thumb_height", 50)); ?>">
                                <span><?php echo t("px") ?></span>
                                <div class="clear"></div>

                            </div>
                        </div>
                        <script>
                            document.addEventListener("DOMContentLoaded", function () {
                                jQuery('#navigation-settings-wrapper input, #navigation-settings-wrapper select').on("change", drawToolBarPreview);

                                // NOT NICE, BUT SURELY UNBREAKABLE LATER :)
                                jQuery('#enable_arrows').on("change", function () {
                                    var sbi = jQuery(this);
                                    if (sbi.attr("checked") === "checked") {
                                        jQuery('#nav_arrows_subs').show();
                                    } else {
                                        jQuery('#nav_arrows_subs').hide();
                                    }
                                    drawToolBarPreview();
                                });


                                jQuery('#enable_bullets').on("change", function () {
                                    var sbi = jQuery(this);
                                    if (sbi.attr("checked") === "checked") {
                                        jQuery('#nav_bullets_subs').show();
                                    } else {
                                        jQuery('#nav_bullets_subs').hide();
                                    }
                                    drawToolBarPreview();
                                });


                                jQuery('#enable_thumbnails').on("change", function () {
                                    var sbi = jQuery(this);
                                    if (sbi.attr("checked") === "checked") {
                                        jQuery('#nav_thumbnails_subs').show();
                                    } else {
                                        jQuery('#nav_thumbnails_subs').hide();
                                    }
                                    drawToolBarPreview();
                                });


                                jQuery('#enable_tabs').on("change", function () {
                                    var sbi = jQuery(this);
                                    if (sbi.attr("checked") === "checked") {
                                        jQuery('#nav_tabs_subs').show();
                                    } else {
                                        jQuery('#nav_tabs_subs').hide();
                                    }
                                    drawToolBarPreview();
                                });

                                jQuery('.showhidewhat_truefalse').on("change", function () {
                                    var sbi = jQuery(this);
                                    if (sbi.val() === true || sbi.val() === "true" || sbi.attr("checked")) {
                                        jQuery("#" + sbi.data("showhidetarget")).show();
                                    } else {
                                        jQuery("#" + sbi.data("showhidetarget")).hide();
                                    }
                                });


                                jQuery('#thumbnails_inner_outer').on('change', function () {
                                    var sbi = jQuery(this),
                                        v = sbi.find('option:selected').val();
                                    if (v === "outer-top" || v === "outer-bottom") {
                                        if (v === "outer-top") jQuery('#thumbnails_align_vert').val("top");
                                        if (v === "outer-bottom") jQuery('#thumbnails_align_vert').val("bottom");
                                        jQuery('#thumbnails_align_vert').attr("disabled", "disabled");
                                        jQuery('#thumbnail_direction').val("horizontal");
                                        jQuery('#thumbnail_direction').change();
                                    }
                                    else
                                        jQuery('#thumbnails_align_vert').removeAttr("disabled");


                                    if (v === "outer-left" || v === "outer-right") {
                                        if (v === "outer-left") jQuery('#thumbnails_align_hor').val("left");
                                        if (v === "outer-right") jQuery('#thumbnails_align_hor').val("right");
                                        jQuery('#thumbnails_align_hor').attr("disabled", "disabled");
                                        jQuery('#thumbnail_direction').val("vertical");
                                        jQuery('#thumbnail_direction').change();
                                    }
                                    else
                                        jQuery('#thumbnails_align_hor').removeAttr("disabled");


                                    if (v === "outer-left" || v === "outer-right" || v === "outer-top" || v === "outer-bottom")
                                        jQuery('#thumbnail_direction').attr("disabled", "disabled");
                                    else
                                        jQuery('#thumbnail_direction').removeAttr("disabled");

                                });

                                jQuery('#tabs_inner_outer').on('change', function () {
                                    var sbi = jQuery(this),
                                        v = sbi.find('option:selected').val();
                                    if (v === "outer-top" || v === "outer-bottom") {
                                        if (v === "outer-top") jQuery('#tabs_align_vert').val("top");
                                        if (v === "outer-bottom") jQuery('#tabs_align_vert').val("bottom");
                                        jQuery('#tabs_align_vert').attr("disabled", "disabled");
                                        jQuery('#tabs_direction').val("horizontal");
                                        jQuery('#tabs_direction').change();
                                        jQuery('#tabs_direction').attr("disabled", "disabled");
                                    }
                                    else
                                        jQuery('#tabs_align_vert').removeAttr("disabled");

                                    if (v === "outer-left" || v === "outer-right") {
                                        if (v === "outer-left") jQuery('#tabs_align_hor').val("left");
                                        if (v === "outer-right") jQuery('#tabs_align_hor').val("right");
                                        jQuery('#tabs_align_hor').attr("disabled", "disabled");
                                        jQuery('#tabs_direction').val("vertical");
                                        jQuery('#tabs_direction').change();
                                        jQuery('#tabs_direction').attr("disabled", "disabled");
                                    }
                                    else
                                        jQuery('#tabs_align_hor').removeAttr("disabled");

                                    if (v === "outer-left" || v === "outer-right" || v === "outer-top" || v === "outer-bottom")
                                        jQuery('#tabs_direction').removeAttr("disabled", "disabled");
                                });


                                jQuery('.showhidewhat_truefalse').change();
                                jQuery('#thumbnails_inner_outer').change();
                                jQuery('#tabs_inner_outer').change();
                                jQuery('#enable_arrows').change();
                                jQuery('#enable_thumbnails').change();
                                jQuery('#enable_bullets').change();
                                jQuery('#enable_tabs').change();
                            });
                        </script>
                    </div><!-- END OF NAVIGATION SETTINGS -->


                    <!-- CAROUSEL SETTINGS -->
                    <div class="setting_box dontshowonhero dontshowonstandard">
                        <h3 class="box_closed"><i class="rs-rp-accordion-icon eg-icon-ccw"></i>
                            <div class="setting_box-arrow"></div>
                            <span><?php echo t("Carousel Settings") ?></span>
                        </h3>

                        <div class="inside" style="display: none;">

                            <ul class="main-options-small-tabs" style="display:inline-block; ">
                                <li data-content="#carousel-basics"
                                    class="selected"><?php echo t('Basics') ?></li>
                                <li data-content="#carousel-trans"><?php echo t('Transformations') ?></li>
                                <li data-content="#carousel-aligns"><?php echo t('Aligns') ?></li>
                            </ul>
                            <div id="carousel-basics">
                                <!-- Infinity -->
                                <span class="label" id="label_carousel_infinity"
                                      origtitle="<?php echo t("Infinity Carousel Scroll. No Endpoints exists at first and last slide if valuse is set to ON.") ?>"><?php echo t("Infinity Scroll") ?> </span>
                                <input type="checkbox" class="tp-moderncheckbox withlabel"
                                       id="carousel_infinity" name="carousel_infinity"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, 'carousel_infinity', 'off'), 'on'); ?>>
                                <div class="clearfix"></div>

                                <!-- Carousel Spaces -->
                                <span class="label" id="label_carousel_space"
                                      origtitle="<?php echo t("The horizontal gap/space between the slides") ?>"><?php echo t("Space between slides") ?> </span>
                                <input type="text" class="text-sidebar withlabel" id="carousel_space"
                                       name="carousel_space"
                                       value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'carousel_space', '0'); ?>">
                                <span><?php echo t("px") ?></span>
                                <div class="clear"></div>

                                <!-- Border Radius -->
                                <span class="label" id="label_carousel_borderr"
                                      origtitle="<?php echo t("The border radius of slides") ?>"><?php echo t("Border Radius") ?> </span>
                                <input style="width:60px;min-width:60px;max-width:60px;" type="text"
                                       class="text-sidebar withlabel" id="carousel_borderr"
                                       name="carousel_borderr"
                                       value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'carousel_borderr', '0'); ?>">

                                <!-- Border Radius Unit -->
                                <select style="width:45px;min-width:45px;max-width:45px;"
                                        id="carousel_borderr_unit" name="carousel_borderr_unit">
                                    <option value="px" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'carousel_borderr_unit', 'px'), "px"); ?>><?php echo t("px") ?></option>
                                    <option value="%" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'carousel_borderr_unit', 'px'), "%"); ?>><?php echo t("%") ?></option>
                                </select>
                                <div class="clear"></div>

                                <!-- Padding -->
                                <span class="label" id="label_carousel_padding_top"
                                      origtitle="<?php echo t("The padding top of slides") ?>"><?php echo t("Padding Top") ?> </span>
                                <input type="text" class="text-sidebar withlabel" id="carousel_padding_top"
                                       name="carousel_padding_top"
                                       value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'carousel_padding_top', '0'); ?>"> <?php echo t('px') ?>

                                <span class="label" id="label_carousel_padding_bottom"
                                      origtitle="<?php echo t("The padding bottom of slides") ?>"><?php echo t("Padding Bottom") ?> </span>
                                <input type="text" class="text-sidebar withlabel" id="carousel_padding_bottom"
                                       name="carousel_padding_bottom"
                                       value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'carousel_padding_bottom', '0'); ?>"> <?php echo t('px') ?>

                                <!-- Carousel Max Visible Items -->
                                <span class="label" id="label_carousel_maxitems"
                                      origtitle="<?php echo t("The maximum visible items in same time.") ?>"><?php echo t("Max. Visible Items") ?> </span>
                                <select id="carousel_maxitems" class="withlabel" name="carousel_maxitems">
                                    <option value="1" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'carousel_maxitems', '3'), "1"); ?>><?php echo t("1") ?></option>
                                    <option value="3" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'carousel_maxitems', '3'), "3"); ?>><?php echo t("3") ?></option>
                                    <option value="5" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'carousel_maxitems', '3'), "5"); ?>><?php echo t("5") ?></option>
                                    <option value="7" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'carousel_maxitems', '3'), "7"); ?>><?php echo t("7") ?></option>
                                    <option value="9" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'carousel_maxitems', '3'), "9"); ?>><?php echo t("9") ?></option>
                                    <option value="11" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'carousel_maxitems', '3'), "11"); ?>><?php echo t("11") ?></option>
                                    <option value="13" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'carousel_maxitems', '3'), "13"); ?>><?php echo t("13") ?></option>
                                    <option value="15" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'carousel_maxitems', '3'), "15"); ?>><?php echo t("15") ?></option>
                                    <option value="17" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'carousel_maxitems', '3'), "17"); ?>><?php echo t("17") ?></option>
                                    <option value="19" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'carousel_maxitems', '3'), "19"); ?>><?php echo t("19") ?></option>
                                </select>

                                <!-- Carousel Stretch Out -->
                                <span class="label" id="label_carousel_stretch"
                                      origtitle="<?php echo t("Stretch carousel element width to the wrapping container width.  Using this you can see only 1 item in same time.") ?>"><?php echo t("Stretch Element") ?> </span>
                                <input type="checkbox" class="tp-moderncheckbox withlabel" id="carousel_stretch"
                                       name="carousel_stretch"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, 'carousel_stretch', 'off'), 'on'); ?>>
                                <div class="clearfix"></div>

                                <!-- Carousel Show All Lyers -->
                                <span class="label" id="label_showalllayers_carousel"
                                      origtitle="<?php echo t("Show All Layers for all the Time with one Start Animation only.") ?>"><?php echo t("Show All Layers 1 Time") ?> </span>
                                <input type="checkbox" class="tp-moderncheckbox withlabel"
                                       id="showalllayers_carousel" name="showalllayers_carousel"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, 'showalllayers_carousel', 'off'), 'on'); ?>>
                                <div class="clearfix"></div>

                                <div class="clear"></div>
                            </div>

                            <!-- Carousel Easing -->
                            <?php
                            $car_easing = RevSliderFunctions::getVal($arrFieldsParams, 'carousel_easing', 'Power3.easeInOut');
                            ?>
                            <span class="label" id="label_carousel_easing"
                                  origtitle="<?php echo t("The carousel easing") ?>"><?php echo t("Easing") ?> </span>
                            <select id="carousel_easing" class="withlabel" name="carousel_easing"
                                    style="width: 106px;">
                                <option <?php RevSliderFunctions::selected($car_easing, 'Linear.easeNone'); ?>
                                        value="Linear.easeNone">Linear.easeNone
                                </option>
                                <option <?php RevSliderFunctions::selected($car_easing, 'Power0.easeIn'); ?>
                                        value="Power0.easeIn">
                                    Power0.easeIn (linear)
                                </option>
                                <option <?php RevSliderFunctions::selected($car_easing, 'Power0.easeInOut'); ?>
                                        value="Power0.easeInOut">Power0.easeInOut (linear)
                                </option>
                                <option
                                    <?php RevSliderFunctions::selected($car_easing, 'Power0.easeOut'); ?>value="Power0.easeOut">
                                    Power0.easeOut (linear)
                                </option>
                                <option <?php RevSliderFunctions::selected($car_easing, 'Power1.easeIn'); ?>
                                        value="Power1.easeIn">
                                    Power1.easeIn
                                </option>
                                <option <?php RevSliderFunctions::selected($car_easing, 'Power1.easeInOut'); ?>
                                        value="Power1.easeInOut">Power1.easeInOut
                                </option>
                                <option
                                    <?php RevSliderFunctions::selected($car_easing, 'Power1.easeOut'); ?>value="Power1.easeOut">
                                    Power1.easeOut
                                </option>
                                <option <?php RevSliderFunctions::selected($car_easing, 'Power2.easeIn'); ?>
                                        value="Power2.easeIn">
                                    Power2.easeIn
                                </option>
                                <option <?php RevSliderFunctions::selected($car_easing, 'Power2.easeInOut'); ?>
                                        value="Power2.easeInOut">Power2.easeInOut
                                </option>
                                <option
                                    <?php RevSliderFunctions::selected($car_easing, 'Power2.easeOut'); ?>value="Power2.easeOut">
                                    Power2.easeOut
                                </option>
                                <option <?php RevSliderFunctions::selected($car_easing, 'Power3.easeIn'); ?>
                                        value="Power3.easeIn">
                                    Power3.easeIn
                                </option>
                                <option <?php RevSliderFunctions::selected($car_easing, 'Power3.easeInOut'); ?>
                                        value="Power3.easeInOut">Power3.easeInOut
                                </option>
                                <option
                                    <?php RevSliderFunctions::selected($car_easing, 'Power3.easeOut'); ?>value="Power3.easeOut">
                                    Power3.easeOut
                                </option>
                                <option <?php RevSliderFunctions::selected($car_easing, 'Power4.easeIn'); ?>
                                        value="Power4.easeIn">
                                    Power4.easeIn
                                </option>
                                <option <?php RevSliderFunctions::selected($car_easing, 'Power4.easeInOut'); ?>
                                        value="Power4.easeInOut">Power4.easeInOut
                                </option>
                                <option
                                    <?php RevSliderFunctions::selected($car_easing, 'Power4.easeOut'); ?>value="Power4.easeOut">
                                    Power4.easeOut
                                </option>
                                <option <?php RevSliderFunctions::selected($car_easing, 'Back.easeIn'); ?>
                                        value="Back.easeIn">
                                    Back.easeIn
                                </option>
                                <option
                                    <?php RevSliderFunctions::selected($car_easing, 'Back.easeInOut'); ?>value="Back.easeInOut">
                                    Back.easeInOut
                                </option>
                                <option <?php RevSliderFunctions::selected($car_easing, 'Back.easeOut'); ?>
                                        value="Back.easeOut">
                                    Back.easeOut
                                </option>
                                <option <?php RevSliderFunctions::selected($car_easing, 'Bounce.easeIn'); ?>
                                        value="Bounce.easeIn">
                                    Bounce.easeIn
                                </option>
                                <option <?php RevSliderFunctions::selected($car_easing, 'Bounce.easeInOut'); ?>
                                        value="Bounce.easeInOut">Bounce.easeInOut
                                </option>
                                <option
                                    <?php RevSliderFunctions::selected($car_easing, 'Bounce.easeOut'); ?>value="Bounce.easeOut">
                                    Bounce.easeOut
                                </option>
                                <option <?php RevSliderFunctions::selected($car_easing, 'Circ.easeIn'); ?>
                                        value="Circ.easeIn">
                                    Circ.easeIn
                                </option>
                                <option
                                    <?php RevSliderFunctions::selected($car_easing, 'Circ.easeInOut'); ?>value="Circ.easeInOut">
                                    Circ.easeInOut
                                </option>
                                <option <?php RevSliderFunctions::selected($car_easing, 'Circ.easeOut'); ?>
                                        value="Circ.easeOut">
                                    Circ.easeOut
                                </option>
                                <option
                                    <?php RevSliderFunctions::selected($car_easing, 'Elastic.easeIn'); ?>value="Elastic.easeIn">
                                    Elastic.easeIn
                                </option>
                                <option <?php RevSliderFunctions::selected($car_easing, 'Elastic.easeInOut'); ?>
                                        value="Elastic.easeInOut">Elastic.easeInOut
                                </option>
                                <option <?php RevSliderFunctions::selected($car_easing, 'Elastic.easeOut'); ?>
                                        value="Elastic.easeOut">Elastic.easeOut
                                </option>
                                <option <?php RevSliderFunctions::selected($car_easing, 'Expo.easeIn'); ?>
                                        value="Expo.easeIn">
                                    Expo.easeIn
                                </option>
                                <option
                                    <?php RevSliderFunctions::selected($car_easing, 'Expo.easeInOut'); ?>value="Expo.easeInOut">
                                    Expo.easeInOut
                                </option>
                                <option <?php RevSliderFunctions::selected($car_easing, 'Expo.easeOut'); ?>
                                        value="Expo.easeOut">
                                    Expo.easeOut
                                </option>
                                <option <?php RevSliderFunctions::selected($car_easing, 'Sine.easeIn'); ?>
                                        value="Sine.easeIn">
                                    Sine.easeIn
                                </option>
                                <option
                                    <?php RevSliderFunctions::selected($car_easing, 'Sine.easeInOut'); ?>value="Sine.easeInOut">
                                    Sine.easeInOut
                                </option>
                                <option <?php RevSliderFunctions::selected($car_easing, 'Sine.easeOut'); ?>
                                        value="Sine.easeOut">
                                    Sine.easeOut
                                </option>
                                <option <?php RevSliderFunctions::selected($car_easing, 'SlowMo.ease'); ?>
                                        value="SlowMo.ease">
                                    SlowMo.ease
                                </option>
                            </select>
                            <div class="clear"></div>

                            <!-- Carousel Easing Speed -->
                            <span class="label" id="label_carousel_speed"
                                  origtitle="<?php echo t("The easing speed") ?>"><?php echo t("Easing Speed") ?> </span>
                            <input type="text" class="text-sidebar withlabel" id="carousel_speed"
                                   name="carousel_speed"
                                   value="<?php echo intval(RevSliderFunctions::getVal($arrFieldsParams, 'carousel_speed', '800')); ?>">
                            <span><?php echo t("ms") ?></span>
                            <div class="clear"></div>

                            <div id="carousel-trans" style="display:none">
                                <!-- Carousel Fade Out -->
                                <span class="label" id="label_carousel_fadeout"
                                      origtitle="<?php echo t("All elements out of focus will get some Opacity value based on the Distance to the current focused element, or only the coming/leaving elements.") ?>"><?php echo t("Fade All Elements") ?> </span>
                                <input type="checkbox" class="tp-moderncheckbox withlabel" id="carousel_fadeout"
                                       name="carousel_fadeout"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, 'carousel_fadeout', 'on'), "on"); ?>>
                                <div class="clearfix"></div>

                                <div id="carousel-fade-row" class="withsublabels">
                                    <!-- Carousel Rotation Varying Out -->
                                    <span class="label" id="label_carousel_varyfade"
                                          origtitle="<?php echo t("Fade is varying based on the distance to the focused element.") ?>"><?php echo t("Varying Fade") ?> </span>
                                    <input type="checkbox" class="tp-moderncheckbox withlabel"
                                           id="carousel_varyfade" name="carousel_varyfade"
                                           data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, 'carousel_varyfade', 'off'), "on"); ?>>
                                    <div class="clearfix"></div>
                                </div>

                                <!-- Carousel Rotation  -->
                                <span class="label label-with-subsection" id="label_carousel_rotation"
                                      origtitle="<?php echo t("Rotation enabled/disabled for not focused elements.") ?>"><?php echo t("Rotation") ?> </span>
                                <input type="checkbox" class="tp-moderncheckbox withlabel"
                                       id="carousel_rotation" name="carousel_rotation"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, 'carousel_rotation', 'off'), "on"); ?>>
                                <div class="clearfix"></div>

                                <div id="carousel-rotation-row" class="withsublabels">

                                    <!-- Carousel Rotation Varying Out -->
                                    <span class="label" id="label_carousel_varyrotate"
                                          origtitle="<?php echo t("Rotation is varying based on the distance to the focused element.") ?>"><?php echo t("Varying Rotation") ?> </span>
                                    <input type="checkbox" class="tp-moderncheckbox withlabel"
                                           id="carousel_varyrotate" name="carousel_varyrotate"
                                           data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, 'carousel_varyrotate', 'off'), "on"); ?>>
                                    <div class="clearfix"></div>

                                    <!-- Carousel Max Rotation -->
                                    <span class="label" id="label_carousel_maxrotation"
                                          origtitle="<?php echo t("The maximum rotation of the Side elements. Rotation will depend on the element distance to the current focused element. 0 will turn off the Rotation") ?>"><?php echo t("Max. Rotation") ?> </span>
                                    <input type="text" class="text-sidebar withlabel" id="carousel_maxrotation"
                                           name="carousel_maxrotation"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'carousel_maxrotation', '0'); ?>">
                                    <span><?php echo t("deg") ?></span>
                                    <div class="clear"></div>
                                </div>

                                <!-- Carousel Scale -->
                                <span class="label label-with-subsection" id="label_carousel_scale"
                                      origtitle="<?php echo t("Scale enabled/disabled for not focused elements.") ?>"><?php echo t("Scale") ?> </span>
                                <input type="checkbox" class="tp-moderncheckbox withlabel" id="carousel_scale"
                                       name="carousel_scale"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, 'carousel_scale', 'off'), "on"); ?>>
                                <div class="clearfix"></div>


                                <div id="carousel-scale-row" class="withsublabels">

                                    <!-- Carousel Scale Varying Out -->
                                    <span class="label" id="label_carousel_varyscale"
                                          origtitle="<?php echo t("Scale is varying based on the distance to the focused element.") ?>"><?php echo t("Varying Scale") ?> </span>
                                    <input type="checkbox" class="tp-moderncheckbox withlabel"
                                           id="carousel_varyscale" name="carousel_varyscale"
                                           data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, 'carousel_varyscale', 'off'), "on"); ?>>
                                    <div class="clearfix"></div>

                                    <!-- Carousel Min Scale Down -->
                                    <span class="label" id="label_carousel_scaledown"
                                          origtitle="<?php echo t("The maximum scale down of the Side elements. Scale will depend on the element distance to the current focused element. Min value is 0 and max value is 100.") ?>"><?php echo t("Max. Scaledown") ?> </span>
                                    <input type="text" class="text-sidebar withlabel" id="carousel_scaledown"
                                           name="carousel_scaledown"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'carousel_scaledown', '50'); ?>">
                                    <span><?php echo t("%") ?></span>
                                    <div class="clear"></div>
                                </div>
                            </div>

                            <div id="carousel-aligns" style="display:none">

                                <!-- Align of Carousel -->
                                <span class="label" id="label_carousel_hposition"
                                      origtitle="<?php echo t("Horizontal Align of the Carousel.") ?>"><?php echo t("Horizontal Aligns") ?> </span>
                                <select id="carousel_hposition" class="withlabel" name="carousel_hposition">
                                    <option value="left" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'carousel_hposition', 'center'), "left"); ?>><?php echo t("Left") ?></option>
                                    <option value="center" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'carousel_hposition', 'center'), "center"); ?>><?php echo t("Center") ?></option>
                                    <option value="right" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'carousel_hposition', 'center'), "right"); ?>><?php echo t("Right") ?></option>
                                </select>
                                <div class="clear"></div>

                                <span class="label" id="label_carousel_vposition"
                                      origtitle="<?php echo t("Vertical Align of the Carousel.") ?>"><?php echo t("Vertical Aligns") ?> </span>
                                <select id="carousel_vposition" class="withlabel" name="carousel_vposition">
                                    <option value="top" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'carousel_vposition', 'center'), "top"); ?>><?php echo t("Top") ?></option>
                                    <option value="center" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'carousel_vposition', 'center'), "center"); ?>><?php echo t("Center") ?></option>
                                    <option value="bottom" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'carousel_vposition', 'center'), "bottom"); ?>><?php echo t("Bottom") ?></option>
                                </select>
                                <div class="clear"></div>
                            </div>


                            <script>
                                document.addEventListener("DOMContentLoaded", function () {
                                    jQuery('#carousel_stretch').on("change", function () {
                                        var sbi = jQuery(this);
                                        if (sbi.attr("checked") === "checked" && jQuery('#carousel_maxitems option[value="1"]').attr("selected") === undefined) {
                                            jQuery('#carousel_maxitems option:selected').removeAttr('selected');
                                            jQuery('#carousel_maxitems option[value="1"]').attr("selected", "selected");
                                        }
                                    });

                                    jQuery('#carousel_maxitems').on("change", function () {
                                        if (jQuery('#carousel_stretch').attr("checked") === "checked" && jQuery('#carousel_maxitems option[value="1"]').attr("selected") === undefined) {
                                            jQuery('#carousel_stretch').removeAttr("checked");
                                            jQuery('#carousel_stretch').change();
                                        }
                                    });

                                    jQuery('#carousel_fadeout').on("change", function () {
                                        var sbi = jQuery(this);

                                        if (sbi.attr("checked") === "checked") {
                                            jQuery('#carousel-fade-row').show();

                                        } else {
                                            jQuery('#carousel-fade-row').hide();
                                        }
                                    });

                                    jQuery('#carousel_rotation').on("change", function () {
                                        var sbi = jQuery(this);

                                        if (sbi.attr("checked") === "checked") {
                                            jQuery('#carousel-rotation-row').show();

                                        } else {
                                            jQuery('#carousel-rotation-row').hide();
                                        }
                                    });

                                    jQuery('#carousel_scale').on("change", function () {
                                        var sbi = jQuery(this);

                                        if (sbi.attr("checked") === "checked") {
                                            jQuery('#carousel-scale-row').show();

                                        } else {
                                            jQuery('#carousel-scale-row').hide();
                                        }
                                    });
                                    jQuery('#carousel_scale').change();
                                    jQuery('#carousel_fadeout').change();
                                    jQuery('#carousel_rotation').change();
                                    jQuery('#first_transition_active').change();
                                });
                            </script>

                        </div>
                    </div> <!-- END OF CAROUSEL SETTINGS -->

                    <!-- Parallax Level -->
                    <div class="setting_box">
                        <h3 class="box_closed"><i class="rs-rp-accordion-icon eg-icon-camera-alt"></i>

                            <div class="setting_box-arrow"></div>

                            <span><?php echo t('Parallax & 3D') ?></span>
                        </h3>

                        <div class="inside" style="display:none">
                                    <span class="label" id="label_use_parallax"
                                          origtitle="<?php echo t("Enabling this, will give you new options in the slides to create a unique parallax effect") ?>"><?php echo t("Enable Parallax / 3D") ?> </span>
                            <input type="checkbox" class="tp-moderncheckbox withlabel" id="use_parallax"
                                   name="use_parallax"
                                   data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "use_parallax", "off"), "on"); ?>>
                            <div class="clear"></div>

                            <div id="parallax_settings_row">

                                        <span id="label_disable_parallax_mobile" class="label"
                                              origtitle="<?php echo t("If set to on, parallax will be disabled on mobile devices to save performance") ?>"><?php echo t("Disable on Mobile") ?> </span>
                                <input type="checkbox" class="tp-moderncheckbox withlabel"
                                       id="disable_parallax_mobile" name="disable_parallax_mobile"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "disable_parallax_mobile", "off"), "on"); ?>>
                                <div class="clear"></div>

                                <span class="label" id="label_ddd_parallax"
                                      origtitle="<?php echo t("Enabling this, will build a ddd_Rotating World of your Slides.") ?>"><?php echo t("3D") ?> </span>
                                <input type="checkbox" class="tp-moderncheckbox withlabel" id="ddd_parallax"
                                       name="ddd_parallax"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "ddd_parallax", "off"), "on"); ?>>
                                <div class="clear"></div>


                                <div class="show_on_ddd_parallax">
                                    <h4><?php echo t("3D Settings") ?></h4>
                                    <div class="withsublabels">
                                                <span class="label" id="label_ddd_parallax_shadow"
                                                      origtitle="<?php echo t("Enabling 3D Shadow") ?>"><?php echo t("3D Shadow") ?> </span>
                                        <input type="checkbox" class="tp-moderncheckbox withlabel"
                                               id="ddd_parallax_shadow" name="ddd_parallax_shadow"
                                               data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "ddd_parallax_shadow", "off"), "on"); ?>>
                                        <div class="clear"></div>

                                        <span class="label" id="label_ddd_parallax_bgfreeze"
                                              origtitle="<?php echo t("BG 3D Disabled") ?>"><?php echo t("3D Background Disabled") ?> </span>
                                        <input type="checkbox" class="tp-moderncheckbox withlabel"
                                               id="ddd_parallax_bgfreeze" name="ddd_parallax_bgfreeze"
                                               data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "ddd_parallax_bgfreeze", "off"), "on"); ?>>
                                        <div class="clear"></div>

                                        <span class="label" id="label_ddd_parallax_overflow"
                                              origtitle="<?php echo t("If option is enabled, all slides and Layers are cropped by the Slider sides.") ?>"><?php echo t("Slider Overflow Hidden") ?> </span>
                                        <input type="checkbox" class="tp-moderncheckbox withlabel"
                                               id="ddd_parallax_overflow" name="ddd_parallax_overflow"
                                               data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "ddd_parallax_overflow", "off"), "on"); ?>>
                                        <div class="clear"></div>

                                        <span class="label" id="label_ddd_parallax_layer_overflow"
                                              origtitle="<?php echo t("If option enabled, Layers are cropped by the Grid Layer Dimensions to avoid Floated 3d Texts and hide Elements outside of the Slider.") ?>"><?php echo t("Layers Overflow Hidden") ?> </span>
                                        <input type="checkbox" class="tp-moderncheckbox withlabel"
                                               id="ddd_parallax_layer_overflow"
                                               name="ddd_parallax_layer_overflow"
                                               data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "ddd_parallax_layer_overflow", "off"), "on"); ?>>
                                        <div class="clear"></div>


                                        <!--<span class="label" id="label_ddd_parallax_path" origtitle="<?php echo t("Select the Events which should trigger the 3D Animation.  Mouse - Mouse Movements will rotate the Slider, Static Paths will set Single or Animated 3d Rotations per Slides (Edit these paths via the Slide Editor), and both will allow you to use both in the same Time.") ?>"><?php echo t("3D Path") ?> </span>
											<select id="ddd_parallax_path" class="withlabel"  name="ddd_parallax_path" style="max-width:110px">
												<option value="mouse" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'ddd_parallax_path', 'mouse'), "mouse"); ?>><?php echo t("Mouse Based") ?></option>
												<option value="static" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'ddd_parallax_path', 'mouse'), "static"); ?>><?php echo t("Static Path (Set Slide by Slide)") ?></option>
												<option value="both" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'ddd_parallax_path', 'mouse'), "both"); ?>><?php echo t("Both") ?></option>
											</select>
											<div class="clear"></div>-->

                                        <span class="label" id="label_ddd_parallax_zcorrection"
                                              origtitle="<?php echo t("Solves issues in Safari Browser. It will move layers along z-axis if BG Freeze enabled to avoid 3d Rendering issues") ?>"><?php echo t("3D Crop Fix (z)") ?> </span>
                                        <input type="text" class="text-sidebar withlabel"
                                               id="ddd_parallax_zcorrection" name="ddd_parallax_zcorrection"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "ddd_parallax_zcorrection", "65"); ?>">
                                        <span><?php echo t("px") ?></span>
                                        <div class="clear"></div>

                                    </div>
                                </div>


                                <div id="p3_ms_wrap">
                                    <h4><?php echo t("Mouse Sensibility") ?></h4>
                                    <div class="withsublabels">
                                        <div class="hide_on_ddd_parallax">
                                                    <span id="label_parallax_type" class="label"
                                                          origtitle="<?php echo t("Defines on what event type the parallax should react to") ?>"><?php echo t("Event") ?></span>
                                            <select id="parallax_type" name="parallax_type" class="withlabel"
                                                    style="width:140px">
                                                <option value="mouse" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "parallax_type", "mouse"), "mouse"); ?>><?php echo t("Mouse Move") ?></option>
                                                <option value="scroll" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "parallax_type", "mouse"), "scroll"); ?>><?php echo t("Scroll Position") ?></option>
                                                <option value="mouse+scroll" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "parallax_type", "mouse"), "mouse+scroll"); ?>><?php echo t("Move and Scroll") ?></option>
                                            </select>
                                            <div class="clear"></div>

                                            <span id="label_parallax_origo" class="label"
                                                  origtitle="<?php echo t("Mouse Based parallax calculation Origo") ?>"><?php echo t("Parallax Origo") ?></span>
                                            <select id="parallax_origo" name="parallax_origo" class="withlabel"
                                                    style="width:140px">
                                                <option value="enterpoint" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "parallax_origo", "enterpoint"), "enterpoint"); ?>><?php echo t("Mouse Enter Point") ?></option>
                                                <option value="slidercenter" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "parallax_origo", "enterpoint"), "slidercenter"); ?>><?php echo t("Slider Center") ?></option>
                                            </select>
                                            <div class="clear"></div>
                                        </div>

                                        <div id="px_m_b_speed" style="display:none">
                                                    <span class="label" id="label_parallax_speed"
                                                          origtitle="<?php echo t("Parallax Speed for Mouse movements.") ?>"><?php echo t("Mouse based Speed") ?> </span>
                                            <input type="text" class="text-sidebar withlabel"
                                                   id="parallax_speed" name="parallax_speed"
                                                   value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "parallax_speed", "400"); ?>">
                                            <span><?php echo t("ms") ?></span>
                                            <div class="clear"></div>
                                        </div>

                                        <div id="px_s_b_speed" style="display:none">
                                                    <span class="label" id="label_parallax_bg_speed"
                                                          origtitle="<?php echo t("Parallax Speed for Background movements.") ?>"><?php echo t("Background Speed") ?> </span>
                                            <input type="text" class="text-sidebar withlabel"
                                                   id="parallax_bg_speed" name="parallax_bg_speed"
                                                   value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "parallax_bg_speed", "0"); ?>">
                                            <span><?php echo t("ms") ?></span>
                                            <div class="clear"></div>

                                            <span class="label" id="label_parallax_ls_speed"
                                                  origtitle="<?php echo t("Parallax Speed for Layer movements.") ?>"><?php echo t("Layers Speed") ?> </span>
                                            <input type="text" class="text-sidebar withlabel"
                                                   id="parallax_ls_speed" name="parallax_ls_speed"
                                                   value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "parallax_ls_speed", "0"); ?>">
                                            <span><?php echo t("ms") ?></span>
                                            <div class="clear"></div>
                                        </div>
                                    </div>

                                    <h4 class="hide_on_ddd_parallax"><?php echo t("Parallax Levels") ?></h4>
                                    <h4 class="show_on_ddd_parallax"><?php echo t("3D Depth Levels") ?></h4>

                                    <div class="withsublabels">
											<span class="show_on_ddd_parallax">
												<span class="label" id="label_parallax_level_16"
                                                      origtitle="<?php echo t("Defines the Strength of the 3D Rotation on the Background and Layer Groups.  The Higher the Value the stronger the effect.  All other Depth will offset this default value !") ?>"><span><?php echo t("Default 3D Depth") ?></span> </span>
												<input type="text" class="text-sidebar withlabel" id="parallax_level_16"
                                                       name="parallax_level_16"
                                                       value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "parallax_level_16", "55"); ?>">
												<span class="clear"></span>
											</span>

                                        <span class="label" id="label_parallax_level_1"
                                              origtitle="<?php echo t("Defines the strength of the effect. The higher the value, the stronger the effect. In 3D World the smaller Value comes to the front, and the Higher Value goes to the Background. Set for BG in 3D World the highest value always. Elements with higher z-index should get smaller values to make the effect perfect.") ?>"><span
                                                    class="hide_on_ddd_parallax"><?php echo t("Level Depth 1") ?></span><span
                                                    class="show_on_ddd_parallax"><?php echo t("Depth 1") ?></span></span>
                                        <input type="text" class="text-sidebar withlabel" id="parallax_level_1"
                                               name="parallax_level_1"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "parallax_level_1", "5"); ?>">
                                        <div class="clear"></div>

                                        <span class="label" id="label_parallax_level_2"
                                              origtitle="<?php echo t("Defines the strength of the effect. The higher the value, the stronger the effect. In 3D World the smaller Value comes to the front, and the Higher Value goes to the Background. Set for BG in 3D World the highest value always. Elements with higher z-index should get smaller values to make the effect perfect.") ?>"><span
                                                    class="hide_on_ddd_parallax"><?php echo t("Level Depth 2") ?></span><span
                                                    class="show_on_ddd_parallax"><?php echo t("Depth 2") ?></span> </span>
                                        <input type="text" class="text-sidebar withlabel" id="parallax_level_2"
                                               name="parallax_level_2"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "parallax_level_2", "10"); ?>">
                                        <div class="clear"></div>

                                        <span class="label" id="label_parallax_level_3"
                                              origtitle="<?php echo t("Defines the strength of the effect. The higher the value, the stronger the effect. In 3D World the smaller Value comes to the front, and the Higher Value goes to the Background. Set for BG in 3D World the highest value always. Elements with higher z-index should get smaller values to make the effect perfect.") ?>"><span
                                                    class="hide_on_ddd_parallax"><?php echo t("Level Depth 3") ?></span><span
                                                    class="show_on_ddd_parallax"><?php echo t("Depth 3") ?></span> </span>
                                        <input type="text" class="text-sidebar withlabel " id="parallax_level_3"
                                               name="parallax_level_3"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "parallax_level_3", "15"); ?>">
                                        <div class="clear"></div>

                                        <span class="label" id="label_parallax_level_4"
                                              origtitle="<?php echo t("Defines the strength of the effect. The higher the value, the stronger the effect. In 3D World the smaller Value comes to the front, and the Higher Value goes to the Background. Set for BG in 3D World the highest value always. Elements with higher z-index should get smaller values to make the effect perfect.") ?>"><span
                                                    class="hide_on_ddd_parallax"><?php echo t("Level Depth 4") ?></span><span
                                                    class="show_on_ddd_parallax"><?php echo t("Depth 4") ?></span> </span>
                                        <input type="text" class="text-sidebar withlabel" id="parallax_level_4"
                                               name="parallax_level_4"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "parallax_level_4", "20"); ?>">
                                        <div class="clear"></div>

                                        <span class="label" id="label_parallax_level_5"
                                              origtitle="<?php echo t("Defines the strength of the effect. The higher the value, the stronger the effect. In 3D World the smaller Value comes to the front, and the Higher Value goes to the Background. Set for BG in 3D World the highest value always. Elements with higher z-index should get smaller values to make the effect perfect.") ?>"><span
                                                    class="hide_on_ddd_parallax"><?php echo t("Level Depth 5") ?></span><span
                                                    class="show_on_ddd_parallax"><?php echo t("Depth 5") ?></span> </span>
                                        <input type="text" class="text-sidebar withlabel" id="parallax_level_5"
                                               name="parallax_level_5"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "parallax_level_5", "25"); ?>">
                                        <div class="clear"></div>

                                        <span class="label" id="label_parallax_level_6"
                                              origtitle="<?php echo t("Defines the strength of the effect. The higher the value, the stronger the effect. In 3D World the smaller Value comes to the front, and the Higher Value goes to the Background. Set for BG in 3D World the highest value always. Elements with higher z-index should get smaller values to make the effect perfect.") ?>"><span
                                                    class="hide_on_ddd_parallax"><?php echo t("Level Depth 6") ?></span><span
                                                    class="show_on_ddd_parallax"><?php echo t("Depth 6") ?></span> </span>
                                        <input type="text" class="text-sidebar withlabel" id="parallax_level_6"
                                               name="parallax_level_6"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "parallax_level_6", "30"); ?>">
                                        <div class="clear"></div>

                                        <span class="label" id="label_parallax_level_7"
                                              origtitle="<?php echo t("Defines the strength of the effect. The higher the value, the stronger the effect. In 3D World the smaller Value comes to the front, and the Higher Value goes to the Background. Set for BG in 3D World the highest value always. Elements with higher z-index should get smaller values to make the effect perfect.") ?>"><span
                                                    class="hide_on_ddd_parallax"><?php echo t("Level Depth 7") ?></span><span
                                                    class="show_on_ddd_parallax"><?php echo t("Depth 7") ?></span> </span>
                                        <input type="text" class="text-sidebar withlabel" id="parallax_level_7"
                                               name="parallax_level_7"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "parallax_level_7", "35"); ?>">
                                        <div class="clear"></div>

                                        <span class="label" id="label_parallax_level_8"
                                              origtitle="<?php echo t("Defines the strength of the effect. The higher the value, the stronger the effect. In 3D World the smaller Value comes to the front, and the Higher Value goes to the Background. Set for BG in 3D World the highest value always. Elements with higher z-index should get smaller values to make the effect perfect.") ?>"><span
                                                    class="hide_on_ddd_parallax"><?php echo t("Level Depth 8") ?></span><span
                                                    class="show_on_ddd_parallax"><?php echo t("Depth 8") ?></span> </span>
                                        <input type="text" class="text-sidebar withlabel" id="parallax_level_8"
                                               name="parallax_level_8"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "parallax_level_8", "40"); ?>">
                                        <div class="clear"></div>

                                        <span class="label" id="label_parallax_level_9"
                                              origtitle="<?php echo t("Defines the strength of the effect. The higher the value, the stronger the effect. In 3D World the smaller Value comes to the front, and the Higher Value goes to the Background. Set for BG in 3D World the highest value always. Elements with higher z-index should get smaller values to make the effect perfect.") ?>"><span
                                                    class="hide_on_ddd_parallax"><?php echo t("Level Depth 9") ?></span><span
                                                    class="show_on_ddd_parallax"><?php echo t("Depth 9") ?></span> </span>
                                        <input type="text" class="text-sidebar withlabel" id="parallax_level_9"
                                               name="parallax_level_9"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "parallax_level_9", "45"); ?>">
                                        <div class="clear"></div>

                                        <span class="label" id="label_parallax_level_10"
                                              origtitle="<?php echo t("Defines the strength of the effect. The higher the value, the stronger the effect. In 3D World the smaller Value comes to the front, and the Higher Value goes to the Background. Set for BG in 3D World the highest value always. Elements with higher z-index should get smaller values to make the effect perfect.") ?>"><span
                                                    class="hide_on_ddd_parallax"><?php echo t("Level Depth 10") ?></span><span
                                                    class="show_on_ddd_parallax"><?php echo t("Depth 10") ?></span> </span>
                                        <input type="text" class="text-sidebar withlabel" id="parallax_level_10"
                                               name="parallax_level_10"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "parallax_level_10", "46"); ?>">
                                        <div class="clear"></div>

                                        <span class="label" id="label_parallax_level_11"
                                              origtitle="<?php echo t("Defines the strength of the effect. The higher the value, the stronger the effect. In 3D World the smaller Value comes to the front, and the Higher Value goes to the Background. Set for BG in 3D World the highest value always. Elements with higher z-index should get smaller values to make the effect perfect.") ?>"><span
                                                    class="hide_on_ddd_parallax"><?php echo t("Level Depth 11") ?></span><span
                                                    class="show_on_ddd_parallax"><?php echo t("Depth 11") ?></span> </span>
                                        <input type="text" class="text-sidebar withlabel" id="parallax_level_11"
                                               name="parallax_level_11"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "parallax_level_11", "47"); ?>">
                                        <div class="clear"></div>

                                        <span class="label" id="label_parallax_level_12"
                                              origtitle="<?php echo t("Defines the strength of the effect. The higher the value, the stronger the effect. In 3D World the smaller Value comes to the front, and the Higher Value goes to the Background. Set for BG in 3D World the highest value always. Elements with higher z-index should get smaller values to make the effect perfect.") ?>"><span
                                                    class="hide_on_ddd_parallax"><?php echo t("Level Depth 12") ?></span><span
                                                    class="show_on_ddd_parallax"><?php echo t("Depth 12") ?></span> </span>
                                        <input type="text" class="text-sidebar withlabel" id="parallax_level_12"
                                               name="parallax_level_12"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "parallax_level_12", "48"); ?>">
                                        <div class="clear"></div>

                                        <span class="label" id="label_parallax_level_13"
                                              origtitle="<?php echo t("Defines the strength of the effect. The higher the value, the stronger the effect. In 3D World the smaller Value comes to the front, and the Higher Value goes to the Background. Set for BG in 3D World the highest value always. Elements with higher z-index should get smaller values to make the effect perfect.") ?>"><span
                                                    class="hide_on_ddd_parallax"><?php echo t("Level Depth 13") ?></span><span
                                                    class="show_on_ddd_parallax"><?php echo t("Depth 13") ?></span> </span>
                                        <input type="text" class="text-sidebar withlabel" id="parallax_level_13"
                                               name="parallax_level_13"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "parallax_level_13", "49"); ?>">
                                        <div class="clear"></div>

                                        <span class="label" id="label_parallax_level_14"
                                              origtitle="<?php echo t("Defines the strength of the effect. The higher the value, the stronger the effect. In 3D World the smaller Value comes to the front, and the Higher Value goes to the Background. Set for BG in 3D World the highest value always. Elements with higher z-index should get smaller values to make the effect perfect.") ?>"><span
                                                    class="hide_on_ddd_parallax"><?php echo t("Level Depth 14") ?></span><span
                                                    class="show_on_ddd_parallax"><?php echo t("Depth 14") ?></span> </span>
                                        <input type="text" class="text-sidebar withlabel" id="parallax_level_14"
                                               name="parallax_level_14"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "parallax_level_14", "50"); ?>">
                                        <div class="clear"></div>

                                        <span class="label" id="label_parallax_level_15"
                                              origtitle="<?php echo t("Defines the strength of the effect. The higher the value, the stronger the effect. In 3D World the smaller Value comes to the front, and the Higher Value goes to the Background. Set for BG in 3D World the highest value always. Elements with higher z-index should get smaller values to make the effect perfect.") ?>"><span
                                                    class="hide_on_ddd_parallax"><?php echo t("Level Depth 15") ?></span><span
                                                    class="show_on_ddd_parallax"><?php echo t("Depth 15") ?></span> </span>
                                        <input type="text" class="text-sidebar withlabel" id="parallax_level_15"
                                               name="parallax_level_15"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "parallax_level_15", "51"); ?>">
                                        <div class="clear"></div>


                                    </div>
                                </div>

                            </div>
                        </div>

                        <script>
                            function showHidePXBaseds() {
                                var v = jQuery('#parallax_type').val();
                                switch (v) {
                                    case "scroll":
                                        jQuery('#px_m_b_speed').hide();
                                        jQuery('#px_s_b_speed').show();
                                        break;
                                    case "mouse":
                                        jQuery('#px_m_b_speed').show();
                                        jQuery('#px_s_b_speed').hide();
                                        break;
                                    case "mouse+scroll":
                                        jQuery('#px_m_b_speed').show();
                                        jQuery('#px_s_b_speed').show();
                                        break;
                                }
                            }
                            document.addEventListener("DOMContentLoaded", function () {
                                jQuery('#use_parallax').on("change", function () {
                                    var sbi = jQuery(this);
                                    drawToolBarPreview();
                                    if (sbi.attr("checked") === "checked") {
                                        jQuery('#parallax_settings_row').show();
                                        jQuery('#ddd_parallax').change();
                                    } else {
                                        jQuery('#parallax_settings_row').hide();
                                        jQuery('.hide_on_ddd_parallax').show();
                                        jQuery('.show_on_ddd_parallax').hide();
                                    }
                                });


                                jQuery('#parallax_type').on("change", showHidePXBaseds);
                                showHidePXBaseds();

                                jQuery('#ddd_parallax').on("change", function () {
                                    drawToolBarPreview();
                                    var sbi = jQuery(this);
                                    if (sbi.attr("checked") === "checked" && jQuery('#use_parallax').attr("checked") === "checked") {
                                        jQuery('.hide_on_ddd_parallax').hide();
                                        jQuery('.show_on_ddd_parallax').show();
                                        jQuery('#fadeinoutparallax').hide();
                                    } else {
                                        jQuery('.hide_on_ddd_parallax').show();
                                        jQuery('.show_on_ddd_parallax').hide();
                                        if (jQuery('input[name="slider-type"]:checked').val() === "hero") jQuery('#fadeinoutparallax').show();
                                    }
                                });

                                jQuery('#ddd_parallax_shadow').on("change", drawToolBarPreview);

                                jQuery('#use_parallax').change();
                                jQuery('#ddd_parallax').change();
                            });
                        </script>

                    </div>


                    <!-- Scroll Effects -->
                    <div class="setting_box" id="fadeinoutparallax">
                        <h3 class="box_closed"><i class="rs-rp-accordion-icon eg-icon-camera-alt"></i>

                            <div class="setting_box-arrow"></div>

                            <span><?php echo t('Scroll Effects') ?></span>
                        </h3>
                        <div class="inside" style="display:none">
                            <!-- FADE EFFECT ON SCROLL -->
                            <span class="label" id="label_fade_scrolleffect"
                                  origtitle="<?php echo t("Endable / Disable Fade Effect on Scroll:") ?>"><?php echo t("Fade Effect") ?> </span>
                            <input type="checkbox" class="tp-moderncheckbox withlabel" id="fade_scrolleffect"
                                   name="fade_scrolleffect"
                                   data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "fade_scrolleffect", "off"), "on"); ?>>
                            <div class="clear"></div>


                            <!-- BLUR EFFECT ON SCROLL -->
                            <span class="label" id="label_blur_scrolleffect"
                                  origtitle="<?php echo t("Endable / Disable Blur Effect on Scroll") ?>"><?php echo t("Blur Effect") ?> </span>
                            <input type="checkbox" class="tp-moderncheckbox withlabel" id="blur_scrolleffect"
                                   name="blur_scrolleffect"
                                   data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "blur_scrolleffect", "off"), "on"); ?>>
                            <div class="clear"></div>

                            <!-- GRAYSCALE EFFECT ON SCROLL -->
                            <span class="label" id="label_grayscale_scrolleffect"
                                  origtitle="<?php echo t("Enable / Disable grayscale Effect on Scroll") ?>"><?php echo t("Grayscale Effect") ?> </span>
                            <input type="checkbox" class="tp-moderncheckbox withlabel"
                                   id="grayscale_scrolleffect" name="grayscale_scrolleffect"
                                   data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "grayscale_scrolleffect", "off"), "on"); ?>>
                            <div class="clear"></div>

                            <h4><?php echo t("Effect Levels:") ?></h4>
                            <div class="withsublabels">

                                <!-- MAX BLUR EFFECT -->
                                <span class="label" id="label_scrolleffect_maxblur"
                                      origtitle="<?php echo t("Maximum Blur Level on Elements by Maximal Scroll. Optimal Values between 0-100") ?>"><?php echo t("Max. Blur Effect") ?> </span>
                                <input type="text" class="text-sidebar withlabel" id="scrolleffect_maxblur"
                                       name="scrolleffect_maxblur"
                                       value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "scrolleffect_maxblur", "10"); ?>">
                                <span><?php echo t("px") ?></span>
                                <div class="clear"></div>

                            </div>

                            <h4><?php echo t("Effects on Elements:") ?></h4>
                            <div class="withsublabels">
                                        <span class="label" id="label_scrolleffect_bg"
                                              origtitle="<?php echo t("Effects enabled on Slide BG") ?>"><?php echo t("On Slider BG's") ?> </span>
                                <input type="checkbox" class="tp-moderncheckbox withlabel" id="scrolleffect_bg"
                                       name="scrolleffect_bg"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "scrolleffect_bg", "off"), "on"); ?>>
                                <div class="clear"></div>

                                <span class="label" id="label_scrolleffect_layers"
                                      origtitle="<?php echo t("Effects on Layers without Parallax Effect") ?>"><?php echo t("None Parallax Layers") ?> </span>
                                <input type="checkbox" class="tp-moderncheckbox withlabel"
                                       id="scrolleffect_layers" name="scrolleffect_layers"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "scrolleffect_layers", "off"), "on"); ?>>
                                <div class="clear"></div>

                                <span class="label" id="label_scrolleffect_parallax_layers"
                                      origtitle="<?php echo t("Effects on Layers with Parallax Effect") ?>"><?php echo t("Parallax Layers") ?> </span>
                                <input type="checkbox" class="tp-moderncheckbox withlabel"
                                       id="scrolleffect_parallax_layers" name="scrolleffect_parallax_layers"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "scrolleffect_parallax_layers", "off"), "on"); ?>>
                                <div class="clear"></div>

                                <span class="label" id="label_scrolleffect_static_layers"
                                      origtitle="<?php echo t("Effects on Static Layers without Parallax Effect") ?>"><?php echo t("None Parallax Static L.") ?> </span>
                                <input type="checkbox" class="tp-moderncheckbox withlabel"
                                       id="scrolleffect_static_layers" name="scrolleffect_static_layers"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "scrolleffect_static_layers", "off"), "on"); ?>>
                                <div class="clear"></div>

                                <span class="label" id="label_scrolleffect_static_parallax_layers"
                                      origtitle="<?php echo t("Effects on Static Layers with Parallax Effect") ?>"><?php echo t("Parallax Static Layers") ?> </span>
                                <input type="checkbox" class="tp-moderncheckbox withlabel"
                                       id="scrolleffect_static_parallax_layers"
                                       name="scrolleffect_static_parallax_layers"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "scrolleffect_static_parallax_layers", "off"), "on"); ?>>
                                <div class="clear"></div>

                            </div>
                            <h4><?php echo t("Scroll Dependencies:") ?></h4>
                            <div class="withsublabels">
                                        <span class="label" id="label_scrolleffect_direction"
                                              origtitle="<?php echo t("Select the Direction where the Elements should be Fade In/Out from/to") ?>"><?php echo t("Effect Directions") ?> </span>
                                <select id="scrolleffect_direction" class="withlabel"
                                        name="scrolleffect_direction" style="max-width:110px">
                                    <option value="top" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'scrolleffect_direction', 'both'), "top"); ?>><?php echo t("Top Direction") ?></option>
                                    <option value="bottom" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'scrolleffect_direction', 'both'), "bottom"); ?>><?php echo t("Bottom Direction") ?></option>
                                    <option value="both" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'scrolleffect_direction', 'both'), "both"); ?>><?php echo t("Both Direction") ?></option>
                                </select>
                                <div class="clear"></div>

                                <span class="label" id="label_scrolleffect_tilt"
                                      origtitle="<?php echo t("Offset the Effect with % of Screen. Best Values between 0% and 100%") ?>"><?php echo t("Offset Effect") ?> </span>
                                <input type="text" class="text-sidebar withlabel" id="scrolleffect_tilt"
                                       name="scrolleffect_tilt"
                                       value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "scrolleffect_tilt", "30"); ?>">
                                <span><?php echo t("%") ?></span>
                                <div class="clear"></div>

                                <span class="label" id="label_scrolleffect_multiplicator"
                                      origtitle="<?php echo t("Parallax Speed Multiplicator For Background. Best Values between 0.2 and 2") ?>"><?php echo t("Effect Factor BG") ?> </span>
                                <input type="text" class="text-sidebar withlabel"
                                       id="scrolleffect_multiplicator" name="scrolleffect_multiplicator"
                                       value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "scrolleffect_multiplicator", "1.3"); ?>">
                                <div class="clear"></div>

                                <span class="label" id="label_scrolleffect_multiplicator_layers"
                                      origtitle="<?php echo t("Parallax Speed Multiplicator For Layers.  Best Values between 0.2 and 2") ?>"><?php echo t("Effect Factor Layers") ?> </span>
                                <input type="text" class="text-sidebar withlabel"
                                       id="scrolleffect_multiplicator_layers"
                                       name="scrolleffect_multiplicator_layers"
                                       value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "scrolleffect_multiplicator_layers", "1.3"); ?>">
                                <div class="clear"></div>

                                <span class="label" id="label_scrolleffect_off_mobile"
                                      origtitle="<?php echo t("Disable Fade Out Effect On Mobile Devices") ?>"><?php echo t("Disable on Mobile") ?> </span>
                                <input type="checkbox" class="tp-moderncheckbox withlabel"
                                       id="scrolleffect_off_mobile" name="scrolleffect_off_mobile"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "scrolleffect_off_mobile", "on"), "on"); ?>>
                                <div class="clear"></div>

                            </div>
                        </div>
                    </div><!-- END OF SCROLL EFFECTS-->


                    <?php
                    if (!empty($sliderID)) {
                        ?>
                        <!-- SPEED MONITOR -->
                        <div class="setting_box" id="v_sp_mo">
                            <h3 class="box_closed"><i class="rs-rp-accordion-icon eg-icon-cog-alt"></i>
                                <div class="setting_box-arrow"></div>
                                <span><?php echo t("Performance and SEO Optimization") ?></span>
                            </h3>

                            <div class="inside" style="display:none;">

                                <!-- LAZY LOAD -->
                                <?php
                                $llt = RevSliderFunctions::getVal($arrFieldsParams, 'lazy_load_type', false);
                                if ($llt === false) {
                                    //do fallback checks to removed lazy_load value since version 5.0 and replaced with an enhanced version
                                    $old_ll = RevSliderFunctions::getVal($arrFieldsParams, 'lazy_load', 'off');
                                    $llt = ($old_ll == 'on') ? 'all' : 'none';
                                }
                                ?>
                                <span id="label_lazy_load_type" class="label"
                                      origtitle="<?php echo t("How to load/preload the images. <br><br><strong>All</strong> - Load all image element in a sequence at the initialisation. This will boost up the loading of your page, and will preload all images to have a smooth and breakless run already in the first loop.  <br><br><strong>Smart</strong> - It will load the page as quick as possible, and load only the current and neighbour slide elements. If slide is called which not loaded yet, will be loaded on demand with minimal delays.   <br><br><strong>Single</strong> - It will load only the the start slide. Any other slides will be loaded on demand.") ?>"><?php echo t("Lazy Load") ?> </span>
                                <select id="lazy_load_type" name="lazy_load_type" class="withlabel">
                                    <option value="all" <?php RevSliderFunctions::selected($llt, 'all'); ?>><?php echo t("All") ?></option>
                                    <option value="smart" <?php RevSliderFunctions::selected($llt, 'smart'); ?>><?php echo t("Smart") ?></option>
                                    <option value="single" <?php RevSliderFunctions::selected($llt, 'single'); ?>><?php echo t("Single") ?></option>
                                    <option value="none" <?php RevSliderFunctions::selected($llt, 'none'); ?>><?php echo t("No Lazy Loading") ?></option>
                                </select>
                                <div class="clearfix"></div>


                                <!-- MONITORING PART -->
                                <?php //list all images and speed here ?>
                                <?php
                                //Operations::get_slider_speed($sliderID);
                                ?>
                            </div><!-- END OF INSIDE-->
                        </div>
                        <script>
                            document.addEventListener("DOMContentLoaded", function () {
                                jQuery('#lazy_load_type').on("change", function () {
                                    switch (jQuery('#lazy_load_type option:selected').val()) {
                                        case "all":
                                        case "none":
                                            jQuery('.tp-monitor-single-speed').hide();
                                            jQuery('.tp-monitor-smart-speed').hide();
                                            jQuery('.tp-monitor-all-speed').show();
                                            break;
                                        case "smart":
                                            jQuery('.tp-monitor-single-speed').hide();
                                            jQuery('.tp-monitor-smart-speed').show();
                                            jQuery('.tp-monitor-all-speed').hide();
                                            break;
                                        case "single":
                                            jQuery('.tp-monitor-single-speed').show();
                                            jQuery('.tp-monitor-smart-speed').hide();
                                            jQuery('.tp-monitor-all-speed').hide();
                                            break;
                                    }
                                });
                                jQuery('#lazy_load_type').change();
                            })
                        </script>

                        <?php
                    }
                    ?>


                    <!-- FALLBACKS -->
                    <div class="setting_box" id="phandlings">
                        <h3 class="box_closed"><i class="rs-rp-accordion-icon eg-icon-medkit"></i>

                            <div class="setting_box-arrow"></div>

                            <span class="phandlingstitle"><?php echo t('Problem Handlings') ?></span>
                        </h3>

                        <div class="inside" style="display:none;">
                            <ul class="main-options-small-tabs" style="display:inline-block; ">
                                <li data-content="#problem-fallback"
                                    class="selected"><?php echo t('Fallbacks') ?></li>
                                <li id="phandling_menu" data-content="#problem-troubleshooting"
                                    class=""><?php echo t('Troubleshooting') ?></li>
                            </ul>
                            <div id="problem-fallback">
                                        <span id="label_simplify_ie8_ios4" class="label"
                                              origtitle="<?php echo t("Simplyfies the Slider on IOS4 and IE8") ?>"><?php echo t("Simplify on IOS4/IE8") ?> </span>
                                <input type="checkbox" class="tp-moderncheckbox withlabel"
                                       id="simplify_ie8_ios4" name="simplify_ie8_ios4"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "simplify_ie8_ios4", "off"), "on"); ?>>
                                <div class="clear"></div>

                                <div id="label_show_alternative_type" class="label"
                                     origtitle="<?php echo t("Disables the Slider and load an alternative image instead") ?>"><?php echo t("Use Alternative Image") ?> </div>
                                <select id="show_alternative_type" name="show_alternative_type"
                                        class="withlabel">
                                    <option value="off" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "show_alternative_type", "off"), "off"); ?>><?php echo t("Off") ?></option>
                                    <option value="mobile" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "show_alternative_type", "off"), "mobile"); ?>><?php echo t("On Mobile") ?></option>
                                    <option value="ie8" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "show_alternative_type", "off"), "ie8"); ?>><?php echo t("On IE8") ?></option>
                                    <option value="mobile-ie8" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "show_alternative_type", "off"), "mobile-ie8"); ?>><?php echo t("On Mobile and IE8") ?></option>
                                </select>
                                <div class="clear"></div>

                                <span id="label_allow_android_html5_autoplay" class="label"
                                      origtitle="<?php echo t("HTML5 autoplay on mobile devices") ?>"><?php echo t("HTML5 Autoplay on Mobiles") ?> </span>
                                <input type="checkbox" class="tp-moderncheckbox withlabel"
                                       id="allow_android_html5_autoplay" name="allow_android_html5_autoplay"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "allow_android_html5_autoplay", "on"), "on"); ?>>
                                <div class="clear"></div>

                                <div class="enable_alternative_image">
                                    <div id="label_show_alternate_image" class="label"
                                         origtitle="<?php echo t("The image that will be loaded instead of the slider.") ?>"><?php echo t("Alternate Image") ?> </div>
                                    <input type="text" style="width: 104px;" class="text-sidebar-long withlabel"
                                           id="show_alternate_image" name="show_alternate_image"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "show_alternate_image", ""); ?>">
                                    <a original-title="" href="javascript:void(0)"
                                       class="button-image-select-background-img button-primary revblue"><?php echo t('Set') ?></a>
                                    <div class="clear"></div>
                                </div>

                                <div class="rs-show-on-auto">
                                    <div id="label_ignore_height_changes" class="label"
                                         origtitle="<?php echo t("Prevents jumping of background image for Android devices for example") ?>"><?php echo t("Ignore Height Changes") ?> </div>
                                    <select id="ignore_height_changes" name="ignore_height_changes"
                                            class="withlabel">
                                        <option value="off" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "ignore_height_changes", "off"), "off"); ?>><?php echo t("Off") ?></option>
                                        <option value="mobile" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "ignore_height_changes", "off"), "mobile"); ?>><?php echo t("On Mobile") ?></option>
                                        <option value="always" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "ignore_height_changes", "off"), "always"); ?>><?php echo t("Always") ?></option>
                                    </select>
                                    <div class="clear"></div>

                                    <span class="label" id="label_ignore_height_changes_px"
                                          origtitle="<?php echo t("Ignores the Ignore Height Changes feature under a certain amount of pixels.") ?>"><?php echo t("Ignore Height Changes Under") ?></span>
                                    <input type="text" class="text-sidebar withlabel"
                                           id="ignore_height_changes_px" name="ignore_height_changes_px"
                                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'ignore_height_changes_px', '0'); ?>">
                                    <span><?php echo t("px") ?></span>
                                    <div class="clear"></div>
                                </div>
                            </div>
                            <div id="problem-troubleshooting" style="display:none;">
                                <div id="label_jquery_noconflict" class="label"
                                     origtitle="<?php echo t("Turns on / off jquery noconflict mode. Try to enable this option if javascript conflicts exist on the page.") ?>"><?php echo t("JQuery No Conflict Mode") ?> </div>
                                <input type="checkbox" class="tp-moderncheckbox withlabel"
                                       id="jquery_noconflict" name="jquery_noconflict"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "jquery_noconflict", "off"), "on"); ?>>
                                <div class="clear"></div>

                                <span id="label_js_to_body" class="label"
                                      origtitle="<?php echo t("Try this to fix some javascript conflicts of type: TypeError: tpj('#rev_slider_1_1').show().revolution is not a function") ?>"><?php echo t("Put JS Includes To Body") ?> </span>
                                <span id="js_to_body" class="withlabel">
									<input type="radio" id="js_to_body_1" value="true"
                                           name="js_to_body" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "js_to_body", "false"), "true"); ?>>
									<label for="js_to_body_1"
                                           style="cursor:pointer;margin-right:15px"><?php echo t('On') ?></label>
									<input type="radio" id="js_to_body_2" value="false"
                                           name="js_to_body" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "js_to_body", "false"), "false"); ?>>
									<label for="js_to_body_2"
                                           style="cursor:pointer;"><?php echo t('Off') ?></label>
								</span>
                                <div class="clear"></div>

                                <div id="label_output_type" class="label"
                                     origtitle="<?php echo t("Activate a protection against wordpress output filters that adds html blocks to the shortcode output like P and BR.") ?>"><?php echo t("Output Filters Protection") ?> </div>
                                <select id="output_type" name="output_type" style="max-width:105px"
                                        class="withlabel">
                                    <option value="none" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "output_type", "none"), "none"); ?>><?php echo t("None") ?></option>
                                    <option value="compress" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "output_type", "none"), "compress"); ?>><?php echo t("By Compressing Output") ?></option>
                                    <option value="echo" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, "output_type", "none"), "echo"); ?>><?php echo t("By Echo Output") ?></option>
                                </select>
                                <div class="clear"></div>

                                <div id="label_jquery_debugmode" class="label phandlingstitle"
                                     origtitle="<?php echo t("Turns on / off visible Debug Mode on Front End.") ?>"><?php echo t("Debug Mode") ?> </div>
                                <input type="checkbox" class="tp-moderncheckbox withlabel" id="jquery_debugmode"
                                       name="jquery_debugmode"
                                       data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, "jquery_debugmode", "off"), "on"); ?>>
                                <div class="clear"></div>

                                <div style="margin-top:15px"><a
                                            href="http://spyropress.com/docs/revolution/"
                                            target="_blank"><?php echo t("Follow FAQ for Troubleshooting") ?></a>
                                </div>
                            </div>
                        </div>
                        <script>
                            document.addEventListener("DOMContentLoaded", function () {
                                jQuery('#show_alternative_type').on("change", function () {
                                    var sbi = jQuery(this);
                                    switch (sbi.val()) {
                                        case "off":
                                            jQuery('.enable_alternative_image').hide();
                                            break;
                                        default:
                                            jQuery('.enable_alternative_image').show();
                                            break;
                                    }
                                });

                                jQuery('#jquery_debugmode').on("change", function () {
                                    if (jQuery(this).attr("checked") === "checked")
                                        jQuery('#phandlings').addClass("debugmodeon");
                                    else
                                        jQuery('#phandlings').removeClass("debugmodeon");
                                });

                                if (jQuery('#jquery_debugmode').attr("checked") === "checked")
                                    jQuery('#phandlings').addClass("debugmodeon");
                                else
                                    jQuery('#phandlings').removeClass("debugmodeon");

                                jQuery('#show_alternative_type').change();
                            });
                        </script>
                    </div>

                    <div class="setting_box rs-cm-refresh" id="v_goo_fo">
                        <h3 class="box_closed"><i class="rs-rp-accordion-icon eg-icon-font"></i>
                            <div class="setting_box-arrow"></div>
                            <span><?php echo t('Google Fonts') ?></span>
                        </h3>

                        <div class="inside" style="display:none">

                            <div class="rs-gf-listing">
                                <?php
                                $subsets = RevSliderFunctions::getVal($arrFieldsParams, 'subsets', array());

                                $gf = array();
                                if ($is_edit) {
                                    if (!empty($sliderID)) {
                                        $gf = $slider->getUsedFonts();
                                    }
                                }
                                if (!empty($gf)) : ?>
                                    <h4 style="margin-top:0px;margin-bottom:8px"><?php echo t('Dynamically Registered Google Fonts') ?></h4>

                                    <?php foreach ($gf as $mgf => $mgv) : ?>
                                        <div class="single-google-font-item">
                                        <span class="label font-name-label"><?php echo $mgf ?> :
                                            <?php if (!empty($mgv['variants'])) {
                                                $mgfirst = true;
                                                foreach ($mgv['variants'] as $mgvk => $mgvv) { ?>
                                                    <?php if (!$mgfirst): ?>
                                                        ,
                                                    <?php endif ?>
                                                    <?php echo $mgvk;
                                                    $mgfirst = false;
                                                }
                                            } ?>
                                        </span>
                                            <div class="single-font-setting-wrapper">
                                                <?php if (!empty($mgv['slide'])) : ?>
                                                    <span class="label"><?php echo t('Used in Slide') ?>:</span>
                                                    <select class="google-font-slide-link-list">
                                                        <option value="blank"><?php echo t('Edit Slide(s)') ?></option>
                                                        <?php foreach ($mgv['slide'] as $mgskey => $mgsval) : ?>
                                                            <option value="<?php echo RevSliderFunctions::getViewUrl(RevSliderGlobals::VIEW_SLIDE, array('id' => $mgsval['id'], 'slider' => intval($sliderID))) ?>">
                                                                <?php echo t('Edit:') ?><?php echo RevSliderFunctions::esc_attr($mgsval['title']) ?></option>
                                                        <?php endforeach ?>
                                                    </select>

                                                <?php endif ?>

                                                <?php if (!empty($mgv['subsets'])) : ?>
                                                    <div class="clear"></div>
                                                    <?php foreach ($mgv['subsets'] as $ssk => $ssv) : ?>
                                                        <span class="label subsetlabel"><?php echo $ssv ?></span>
                                                        <input class="tp-moderncheckbox" type="checkbox"
                                                               data-useval="true"
                                                               value="<?php echo RevSliderFunctions::esc_attr($mgf . '+' . $ssv)?>"
                                                               name="subsets[]"
                                                            <?php if (array_search(RevSliderFunctions::esc_attr($mgf . '+' . $ssv), $subsets) !== false) : ?>
                                                                checked="checked"
                                                            <?php endif ?>
                                                        />
                                                    <?php endforeach ?>
                                                <?php endif ?>
                                            </div>
                                        </div>
                                    <?php endforeach ?>
                                <?php else : ?>
                                    <h4 style="margin-top:0px;"><?php echo t('No dynamic fonts registered') ?></h4>
                                <?php endif ?>
                            </div>
                            <script>
                                document.addEventListener("DOMContentLoaded", function () {
                                    jQuery('.google-font-slide-link-list').on('change', function () {
                                        var t = jQuery(this),
                                            v = t.find('option:selected').val();

                                        if (v != "blank") {
                                            var win = window.open(v, '_blank');
                                            if (win) {
                                                win.focus();
                                            } else {
                                                alert('<?php echo t('Link to Slide Editor is Blocked ! Please Allow Pop Ups for this Site !') ?>');
                                            }
                                        }
                                        t.val("blank");

                                    });
                                });
                            </script>
                            <!--h4><?php echo t("Deprecated Google Font Import") ?></h4>
							<div id="rs-google-fonts">

							</div-->
                        </div>
                    </div>
                </form>

                <!-- IMPORT / EXPORT SETTINGS -->
                <?php /*
                if (!RevSliderFunctions::isRS_DEMO()) {
                    if ($is_edit) {
                        ?>
                        <div class="setting_box" id="v_imp_exp">
                            <h3 class="box_closed"><i class="rs-rp-accordion-icon eg-icon-upload"></i>
                                <div class="setting_box-arrow"></div>
                                <span><?php echo t('Import / Export / Replace') ?></span>
                            </h3>
                            <div class="inside" style="display:none">
                                <ul class="main-options-small-tabs" style="display:inline-block; ">
                                    <li data-content="#import-import"
                                        class="selected"><?php echo t('Import') ?></li>
                                    <li data-content="#import-export"
                                        class=""><?php echo t('Export') ?></li>
                                    <li data-content="#import-replace"
                                        class=""><?php echo t('Replace URL') ?></li>
                                </ul>

                                <div id="import-import">
                                    <?php
                                    if (!RevSliderFunctions::isAdminUser()) {
                                        echo t('Import only available for Administrators');
                                    } else {
                                        ?>
                                        <form name="import_slider_form" id="rs_import_slider_form"
                                              action="<?php echo RevSliderFunctions::ajax_url(); ?>"
                                              enctype="multipart/form-data" method="post">
                                            <input type="hidden" name="action" value="revslider_ajax_action">
                                            <input type="hidden" name="client_action" value="import_slider">
                                            <input type="hidden" name="sliderid"
                                                   value="<?php echo $sliderID; ?>">
                                            <!--                                            <input type="hidden" name="nonce"-->
                                            <!--                                                   value="-->
                                            <?php //echo wp_create_nonce("revslider_actions"); ?><!--">-->
                                            <input type="file" name="import_file" class="input_import_slider"
                                                   style="width:100%; font-size:12px;">
                                            <div style="width:100%;height:25px"></div>

                                            <span class="label label-with-subsection"
                                                  id="label_update_animations"
                                                  origtitle="<?php echo t("Overwrite or append the custom animations due the new imported values ?") ?>"><?php echo t("Custom Animations") ?> </span>
                                            <input class="withlabel" type="radio" name="update_animations"
                                                   value="true"
                                                   checked="checked"> <?php echo t("overwrite") ?>
                                            <input class="withlabel" type="radio" name="update_animations"
                                                   value="false"> <?php echo t("append") ?>
                                            <div class="tp-clearfix"></div>


                                            <div class="divide5"></div>
                                            <input type="submit" style="width:100%"
                                                   class="button-primary revgreen" id="rs-submit-import-form"
                                                   value="<?php echo t('Import Slider') ?>">
                                        </form>
                                        <div class="divide20"></div>
                                        <div class="revred api-desc"
                                             style="padding:8px;color:#fff;font-weight:600;font-size:12px"><?php echo t("Note! Style templates will be updated if they exist. Importing slider, will delete all the current slider settings and slides and replacing it with the imported content.") ?></div>
                                        <?php
                                    }
                                    ?>
                                </div>

                                <div id="import-export" style="display:none">
                                    <a id="button_export_slider" class='button-primary revgreen'
                                       href='javascript:void(0)'
                                       style="width:100%;text-align:center;"><?php echo t("Export Slider") ?></a>
                                    <div style="display: none;"><input type="checkbox"
                                                                       name="export_dummy_images"> <?php echo t("Export with Dummy Images") ?>
                                    </div>
                                </div>

                                <div id="import-replace" style="display:none">

                                            <span class="label label-with-subsection" id="label_replace_url_from"
                                                  origtitle="<?php echo t("Replace all layer and backgorund image url's. example - replace from: http://localhost") ?>"><?php echo t("Replace From") ?> </span>
                                    <input type="text" class="text-sidebar-link withlabel"
                                           id="replace_url_from">
                                    <div class="tp-clearfix"></div>

                                    <span class="label label-with-subsection" id="label_replace_url_to"
                                          origtitle="<?php echo t("Replace all layer and backgorund image url's. example - replace to: http://yoursite.com") ?>"><?php echo t("Replace To") ?> </span>
                                    <input type="text" class="text-sidebar-link withlabel" id="replace_url_to">
                                    <div class="tp-clearfix"></div>


                                    <div style="width:100%;height:15px;display:block"></div>

                                    <a id="button_replace_url" class='button-primary revgreen'
                                       href='javascript:void(0)'
                                       style="width:100%; text-align:center;"><?php echo t("Replace URL's") ?></a>
                                    <div id="loader_replace_url" class="loader_round"
                                         style="display:none;"><?php echo t("Replacing...") ?> </div>
                                    <div id="replace_url_success" class="success_message"
                                         class="display:none;"></div>
                                    <div class="divide20"></div>
                                    <div class="revred api-desc"
                                         style="padding:8px;color:#fff;font-weight:600;font-size:12px"><?php echo t("Note! The replace process is not reversible !") ?></div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                }
                */?> <!-- END OF IMPORT EXPORT SETTINGS -->

                <!-- API SETTINGS -->
                <?php
                if ($is_edit) {
                    $api = "revapi" . $sliderID;
                    ?>

                    <div class="setting_box rs-cm-refresh" id="v_api_se">
                        <h3 class="box_closed"><i class="rs-rp-accordion-icon eg-icon-magic"></i>
                            <div class="setting_box-arrow"></div>
                            <span><?php echo t('API Functions') ?></span>
                        </h3>
                        <div class="inside" style="display:none">
                            <ul class="main-options-small-tabs" style="display:inline-block; ">
                                <li data-content="#api-method"
                                    class="selected"><?php echo t('Methods') ?></li>
                                <li data-content="#api-events" class=""><?php echo t('Events') ?></li>
                            </ul>
                            <div id="api-method">
                                        <span class="label" id="label_apiapi1" style="min-width:130px"
                                              origtitle="<?php echo t("Call this function to start the slider.") ?>"><?php echo t("Start Slider") ?>
                                            :</span>
                                <input type="text" style="width:180px" readonly class="api-input withlabel"
                                       id="apiapi0" value="<?php echo $api ?>.revstart();"></span>
                                <div class="tp-clearfix"></div>

                                <span class="label" id="label_apiapi1" style="min-width:130px"
                                      origtitle="<?php echo t("Call this function to pause the slider.") ?>"><?php echo t("Pause Slider") ?>
                                    :</span>
                                <input type="text" style="width:180px" readonly class="api-input withlabel"
                                       id="apiapi1" value="<?php echo $api ?>.revpause();"></span>
                                <div class="tp-clearfix"></div>

                                <span class="label" id="label_apiapi2" style="min-width:130px"
                                      origtitle="<?php echo t("Call this function to play the slider if it is paused.") ?>"><?php echo t("Resume Slider") ?>
                                    :</span>
                                <input type="text" style="width:180px" readonly class="api-input withlabel"
                                       id="apiapi2" value="<?php echo $api ?>.revresume();"></span>
                                <div class="tp-clearfix"></div>

                                <span class="label" id="label_apiapi3" style="min-width:130px"
                                      origtitle="<?php echo t("Switch slider to previous slide.") ?>"><?php echo t("Previous Slide") ?>
                                    :</span>
                                <input type="text" style="width:180px" readonly class="api-input withlabel"
                                       id="apiapi3" value="<?php echo $api ?>.revprev();"></span>
                                <div class="tp-clearfix"></div>

                                <span class="label" id="label_apiapi4" style="min-width:130px"
                                      origtitle="<?php echo t("Switch slider to next slide.") ?>"><?php echo t("Next Slide") ?>
                                    :</span>
                                <input type="text" style="width:180px" readonly class="api-input withlabel"
                                       id="apiapi4" value="<?php echo $api ?>.revnext();"></span>
                                <div class="tp-clearfix"></div>

                                <span class="label" id="label_apiapi5" style="min-width:130px"
                                      origtitle="<?php echo t("Switch to the slide which is defined as parameter.") ?>"><?php echo t("Go To Slide") ?>
                                    :</span>
                                <input type="text" style="width:180px" readonly class="api-input withlabel"
                                       id="apiapi5" value="<?php echo $api ?>.revshowslide(2);"></span>
                                <div class="tp-clearfix"></div>

                                <span class="label" id="label_apiapi5" style="min-width:130px"
                                      origtitle="<?php echo t("Switch to the slide which is defined as parameter.") ?>"><?php echo t("Go To Slide with ID") ?>
                                    :</span>
                                <input type="text" style="width:180px" readonly class="api-input withlabel"
                                       id="apiapi5"
                                       value="<?php echo $api ?>.revcallslidewithid('rs-1007');"></span>
                                <div class="tp-clearfix"></div>

                                <span class="label" id="label_apiapi6" style="min-width:130px"
                                      origtitle="<?php echo t("Get the amount of existing slides in the slider.") ?>"><?php echo t("Max Slides") ?>
                                    :</span>
                                <input type="text" style="width:180px" readonly class="api-input withlabel"
                                       id="apiapi6" value="<?php echo $api ?>.revmaxslide();"></span>
                                <div class="tp-clearfix"></div>

                                <span class="label" id="label_apiapi7" style="min-width:130px"
                                      origtitle="<?php echo t("Get the current focused slide index.") ?>"><?php echo t("Current Slide") ?>
                                    :</span>
                                <input type="text" style="width:180px" readonly class="api-input withlabel"
                                       id="apiapi7" value="<?php echo $api ?>.revcurrentslide();"></span>
                                <div class="tp-clearfix"></div>

                                <span class="label" id="label_apiapi8" style="min-width:130px"
                                      origtitle="<?php echo t("Get the previously played slide.") ?>"><?php echo t("Last Slide") ?>
                                    :</span>
                                <input type="text" style="width:180px" readonly class="api-input withlabel"
                                       id="apiapi8" value="<?php echo $api ?>.revlastslide();"></span>
                                <div class="tp-clearfix"></div>

                                <span class="label" id="label_apiapi9" style="min-width:130px"
                                      origtitle="<?php echo t("Scroll page under the slider.") ?>"><?php echo t("External Scroll") ?>
                                    :</span>
                                <input type="text" style="width:180px" readonly class="api-input withlabel"
                                       id="apiapi9" value="<?php echo $api ?>.revscroll(offset);"></span>
                                <div class="tp-clearfix"></div>

                                <span class="label" id="label_apiapi10" style="min-width:130px"
                                      origtitle="<?php echo t("Recalculate all positions, sizing etc in the slider.  This should be called i.e. if Slider was invisible and becomes visible without any window resize event.") ?>"><?php echo t("Redraw Slider") ?>
                                    :</span>
                                <input type="text" style="width:180px" readonly class="api-input withlabel"
                                       id="apiapi10" value="<?php echo $api ?>.revredraw();"></span>
                                <div class="tp-clearfix"></div>

                                <span class="label" id="label_apiapi12" style="min-width:130px"
                                      origtitle="<?php echo t("Remove One Slide with Slide Index from the Slider. Index starts with 0 which will remove the first slide.") ?>"><?php echo t("Remove Slide") ?>
                                    :</span>
                                <input type="text" style="width:180px" readonly class="api-input withlabel"
                                       id="apiapi12"
                                       value="<?php echo $api ?>.revremoveslide(slideindex);"></span>
                                <div class="tp-clearfix"></div>


                                <span class="label" id="label_apiapi11" style="min-width:130px"
                                      origtitle="<?php echo t("Unbind all listeners, remove current animations and delete containers. Ready for Garbage collection.") ?>"><?php echo t("Kill Slider") ?>
                                    :</span>
                                <input type="text" style="width:180px" readonly class="api-input withlabel"
                                       id="apiapi11" value="<?php echo $api ?>.revkill();"></span>
                                <div class="tp-clearfix"></div>
                            </div>
                            <div id="api-events" style="display:none">
                                <h4 style="margin-top:0px"><?php echo t("Slider Loaded") ?></h4>
                                <textarea class="api_area" style="height:80px;" readonly>
<?php echo $api ?>.bind("revolution.slide.onloaded",function (e) {
	console.log("slider loaded");
});</textarea>
                                <h4 style="margin-top:15px"><?php echo t("Slider swapped to an other slide") ?></h4>
                                <textarea class="api_area" style=" height:100px;" readonly>
<?php echo $api ?>.bind("revolution.slide.onchange",function (e,data) {
	console.log("slide changed to: "+data.slideIndex);
	console.log("current slide <li> Index: "+data.slideLIIndex);
	//data.currentslide - <?php echo t('Current  Slide as jQuery Object') ?>

                                        //data.prevslide - <?php echo t('Previous Slide as jQuery Object') ?>
                                        });</textarea>
                                <h4 style="margin-top:15px"><?php echo t("Slider paused") ?></h4>
                                <textarea class="api_area" style=" height:80px;" readonly>
<?php echo $api ?>.bind("revolution.slide.onpause",function (e,data) {
	console.log("timer paused");
});</textarea>
                                <h4 style="margin-top:15px"><?php echo t("Slider is Playing after pause") ?></h4>
                                <textarea class="api_area" style=" height:80px;" readonly>
<?php echo $api ?>.bind("revolution.slide.onresume",function (e,data) {
	console.log("timer resume");
});</textarea>
                                <h4 style="margin-top:15px"><?php echo t("Video is playing in slider") ?></h4>
                                <textarea class="api_area" style=" height:130px;" readonly>
<?php echo $api ?>.bind("revolution.slide.onvideoplay",function (e,data) {
	console.log("video play");
	//data.video - <?php echo t('The Video API to Manage Video functions') ?>

                                    //data.videotype - <?php echo t('youtube, vimeo, html5') ?>

                                    //data.settings - <?php echo t('Video Settings') ?>
                                    });</textarea>
                                <h4 style="margin-top:15px"><?php echo t("Video stopped in slider") ?></h4>
                                <textarea class="api_area" style=" height:130px;" readonly>
<?php echo $api ?>.bind("revolution.slide.onvideostop",function (e,data) {
	console.log("video stop");
	//data.video - <?php echo t('The Video API to Manage Video functions') ?>

                                    //data.videotype - <?php echo t('youtube, vimeo, html5') ?>

                                    //data.settings - <?php echo t('Video Settings') ?>
                                    });</textarea>
                                <h4 style="margin-top:15px"><?php echo t("Slider reached the 'stop at' slide") ?></h4>
                                <textarea class="api_area" style=" height:80px;" readonly>
<?php echo $api ?>.bind("revolution.slide.onstop",function (e,data) {
	console.log("slider stopped");
});</textarea>
                                <h4 style="margin-top:15px"><?php echo t("Prepared for slide change") ?></h4>
                                <textarea class="api_area" style=" height:100px;" readonly>
<?php echo $api ?>.bind("revolution.slide.onbeforeswap",function (e) {
	console.log("Slider Before Swap");
	//data.currentslide - <?php echo t('Current Slide as jQuery Object') ?>

                                    //data.nextslide - <?php echo t('Coming Slide as jQuery Object') ?>
                                    });</textarea>
                                <h4 style="margin-top:15px"><?php echo t("Finnished with slide change") ?></h4>
                                <textarea class="api_area" style=" height:100px;" readonly>
<?php echo $api ?>.bind("revolution.slide.onafterswap",function (e) {
	console.log("Slider After Swap");
	//data.currentslide - <?php echo t('Current Slide as jQuery Object') ?>

                                    //data.prevslide - <?php echo t('Previous Slide as jQuery Object') ?>
                                    });</textarea>
                                <h4 style="margin-top:15px"><?php echo t("Last slide starts") ?></h4>
                                <textarea class="api_area" style=" height:80px;" readonly>
<?php echo $api ?>.bind("revolution.slide.slideatend",function (e) {
	console.log("slide at end");
});</textarea>
                                <h4 style="margin-top:15px"><?php echo t("Layer Events") ?></h4>
                                <textarea class="api_area" style=" height:130px;" readonly>
<?php echo $api ?>.bind("revolution.slide.layeraction",function (e) {
	//data.eventtype - <?php echo t('Layer Action (enterstage, enteredstage, leavestage,leftstage)') ?>

                                    //data.layertype - <?php echo t('Layer Type (image,video,html)') ?>

                                    //data.layersettings - <?php echo t('Default Settings for Layer') ?>

                                    //data.layer - <?php echo t('Layer as jQuery Object') ?>

                                    });</textarea>
                            </div>
                        </div>
                    </div>
                    <?php
                } ?> <!-- END OF API SETTINGS -->
            </div>

            <!-- THE TOOLBAR FUN -->
            <div id="form_toolbar">
                <div class="toolbar-title"></div>
                <div class="toolbar-content"></div>
                <!--<div class="toolbar-title-a">Schematic</div>				-->
                <!--<div class="toolbar-media"></div>-->
                <div class="toolbar-sliderpreview">
                    <div class="toolbar-slider">
                        <div class="toolbar-slider-image"></div>
                        <div class="toolbar-progressbar"></div>
                        <div class="toolbar-dottedoverlay"></div>
                        <div class="toolbar-navigation-right"></div>
                        <div class="toolbar-navigation-left"></div>
                        <div class="toolbar-navigation-bullets">
                            <div class="toolbar-navigation-bullet"></div>
                            <div class="toolbar-navigation-bullet"></div>
                            <div class="toolbar-navigation-bullet" style="margin:0px !important"></div>
                            <span class="tp-clearfix"></span>
                        </div>

                        <div class="toolbar-navigation-thumbs">
                            <div class="toolbar-navigation-thumb"></div>
                            <div style="border-color:#fff" class="toolbar-navigation-thumb"></div>
                            <div class="toolbar-navigation-thumb" style="margin:0px !important"></div>
                            <span class="toolbar-navigation-thumbs-bg tntb"></span>
                            <span class="tp-clearfix"></span>
                        </div>

                        <div class="toolbar-navigation-tabs">
                            <div class="toolbar-navigation-tab"><span class="long-lorem-ipsum"></span><span
                                        class="short-lorem-ipsum"></span></div>
                            <div style="border-color:#fff" class="toolbar-navigation-tab"><span
                                        class="long-lorem-ipsum"></span><span class="short-lorem-ipsum"></span>
                            </div>
                            <div class="toolbar-navigation-tab" style="margin:0px !important"><span
                                        class="long-lorem-ipsum"></span><span class="short-lorem-ipsum"></span>
                            </div>
                            <span class="toolbar-navigation-tabs-bg tntb"></span>
                            <span class="tp-clearfix"></span>
                        </div>
                    </div>
                </div>
                <div id="preview-nav-wrapper">
                    <div class="rs-editing-preview-overlay"></div>
                    <div class="rs-arrows-preview">
                        <div class="tp-arrows tp-leftarrow"></div>
                        <div class="tp-arrows tp-rightarrow"></div>
                    </div>
                    <div class="rs-bullets-preview"></div>
                    <div class="rs-thumbs-preview"></div>
                    <div class="rs-tabs-preview"></div>
                </div>
                <div class="toolbar-extended-info">
                    <i><?php echo t('*Only Illustration, most changes are not visible.') ?></i></div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}