<?php
/**
 * Created by FsFlex.
 * User: VH
 * Date: 7/29/2017
 * Time: 10:19 AM
 */

namespace Drupal\revslider\Controller\RawTemplates;


use Drupal\revslider\Controller\RawTemplates\SliderMainOptions\SettingsPanelLeft;
use Drupal\revslider\Controller\RawTemplates\SliderMainOptions\SettingsPanelRight;
use Drupal\revslider\Helper\RevSliderFunctions;
use Drupal\revslider\Helper\RevSliderGlobals;
use Drupal\revslider\Model\Navigation;
use Drupal\revslider\Model\Operations;

class SliderMainOptions
{
    public function getTemplate(array $args)
    {
        extract($args);

        ob_start();
        ?>

        <?php
        /**
         * @author    ThemePunch <info@themepunch.com>
         * @link      http://www.themepunch.com/
         * @copyright 2015 ThemePunch
         */
        $settings_wrapper_class ='';
        $operations = new Operations();
        $rs_nav = new Navigation();

        $arrValues = $operations->getGeneralSettingsValues();
        $arr_navigations = $rs_nav->get_all_navigations();

        $transitions = $operations->getArrTransition();

        $_width = (isset($arrValues['width'])) ? $arrValues['width'] : 1240;
        $_width_notebook = (isset($arrValues['width_notebook'])) ? $arrValues['width_notebook'] : 1024;
        $_width_tablet = (isset($arrValues['width_tablet'])) ? $arrValues['width_tablet'] : 778;
        $_width_mobile = (isset($arrValues['width_mobile'])) ? $arrValues['width_mobile'] : 480;


        if (!isset($is_edit)) $is_edit = false;
        if (!isset($linksEditSlides)) $linksEditSlides = '';
        ?>
        <div class="wrap settings_wrap">
            <div class="clear_both"></div>

            <div class="title_line" style="margin-bottom:0px !important">
                <div class="icon32" id="icon-options-general"></div>
                <div class="view_title"><?php echo ($is_edit) ? t("Edit Slider") : t("New Slider"); ?></div>
                <a href="<?php echo RevSliderGlobals::LINK_HELP_SLIDER; ?>"
                   class="button-secondary float_right mtop_10 mleft_10"
                   target="_blank"><?php echo t("Help") ?></a>
                <div class="tp-clearfix"></div>
            </div>

            <div class="rs_breadcrumbs">
                <div class="rs-breadcrumbs-wrapper">
                    <a class='breadcrumb-button' href='<?php echo RevSliderFunctions::getViewUrl("sliders"); ?>'><i
                                class="eg-icon-th-large"></i><?php echo t("All Sliders") ?></a>
                    <a class='breadcrumb-button selected' href="#"><i
                                class="eg-icon-cog"></i><?php echo t('Slider Settings') ?></a>
                    <a class='breadcrumb-button' href="<?php echo $linksEditSlides; ?>"><i
                                class="eg-icon-pencil-2"></i><?php echo t('Slide Editor') ?></a>
                    <div class="tp-clearfix"></div>
                </div>
                <div class="tp-clearfix"></div>
                <div class="rs-mini-toolbar">
                    <div class="rs-mini-toolbar-button rs-toolbar-savebtn">
                        <a class='button-primary revgreen' href='javascript:void(0)' id="button_save_slider_t"><i
                                    class="rs-icon-save-light"
                                    style="display: inline-block;vertical-align: middle;width: 18px;height: 20px;background-repeat: no-repeat;margin-right:10px;margin-left:2px;"></i><span
                                    class="mini-toolbar-text"><?php echo t("Save Settings") ?></span></a>
                        <span id="loader_update_t" class="loader_round"
                              style="display:none;background-color:#27AE60 !important; color:#fff;padding: 5px 5px 6px 25px;margin-right: 5px;"><?php echo t("updating...") ?> </span>
                        <span id="update_slider_success_t" class="success_message"></span>
                    </div>

                    <?php
                    if (isset($linksEditSlides)) {
                        ?>
                        <div class="rs-mini-toolbar-button rs-toolbar-slides">
                            <a class="button-primary revblue" href="<?php echo $linksEditSlides; ?>"
                               id="link_edit_slides_t"><i class="revicon-pencil-1"></i><span
                                        class="mini-toolbar-text"><?php echo t("Edit Slides") ?></span></a>
                        </div>
                        <?php
                    }
                    ?>
                    <div class="rs-mini-toolbar-button  rs-toolbar-preview">
                        <a class="button-primary revgray" href="javascript:void(0)" id="button_preview_slider_t"><i
                                    class="revicon-search-1"></i><span
                                    class="mini-toolbar-text"><?php echo t("Preview") ?></span></a>
                    </div>
                    <div class="rs-mini-toolbar-button  rs-toolbar-delete">
                        <a class='button-primary revred' id="button_delete_slider_t" href='javascript:void(0)'><i
                                    class="revicon-trash"></i><span
                                    class="mini-toolbar-text"><?php echo t("Delete Slider") ?></span></a>
                    </div>
                    <div class="rs-mini-toolbar-button  rs-toolbar-close">
                        <a class='button-primary revyellow' id="button_close_slider_edit_t"
                           href='<?php echo RevSliderFunctions::getViewUrl("sliders"); ?>'><i class="eg-icon-th-large"></i><span
                                    class="mini-toolbar-text"><?php echo t("All Sliders") ?></span></a>
                    </div>
                </div>
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    jQuery('.rs-mini-toolbar-button').hover(function () {
                        var btn = jQuery(this),
                            txt = btn.find('.mini-toolbar-text');
                        punchgs.TweenLite.to(txt, 0.2, {
                            width: "100px",
                            ease: punchgs.Linear.easeNone,
                            overwrite: "all"
                        });
                        punchgs.TweenLite.to(txt, 0.1, {
                            autoAlpha: 1,
                            ease: punchgs.Linear.easeNone,
                            delay: 0.1,
                            overwrite: "opacity"
                        });
                    }, function () {
                        var btn = jQuery(this),
                            txt = btn.find('.mini-toolbar-text');
                        punchgs.TweenLite.to(txt, 0.2, {
                            autoAlpha: 0,
                            width: "0px",
                            ease: punchgs.Linear.easeNone,
                            overwrite: "all"
                        });
                    });
                    var mtb = jQuery('.rs-mini-toolbar'),
                        mtbo = mtb.offset().top;
                    jQuery(document).on("scroll", function () {

                        if (mtbo - jQuery(window).scrollTop() < 35)
                            mtb.addClass("sticky");
                        else
                            mtb.removeClass("sticky");

                    });
                    jQuery(document).on('keydown', function (event) {
                        if (event.ctrlKey || event.metaKey) {
                            switch (String.fromCharCode(event.which).toLowerCase()) {
                                case 's':
                                    event.preventDefault();
                                    jQuery('#button_save_slider_t').click();
                                    break;
                            }
                        }
                    });
                });
            </script>
            <div class="settings_panel">

                <?php
                    $template_seg = new SettingsPanelLeft();
                    echo $template_seg->getTemplate(array(
                        "settings_wrapper_class" => $settings_wrapper_class ,
                        "arrFieldsParams" => $arrFieldsParams ,
                        "uslider" => $uslider ,
                        "_width_notebook" => $_width_notebook ,
                        "_width" => $_width ,
                        "_width_tablet" => $_width_tablet ,
                        "_width_mobile" => $_width_mobile ,
                        "is_edit" => $is_edit,
                        'sliderID'=>$sliderID
                        ));
                ?>

                <?php
                    $template_seg = new SettingsPanelRight();
                    echo $template_seg->getTemplate(array(
                        "transitions" => $transitions ,
                        "arrFieldsParams" => $arrFieldsParams ,
                        "operations" => $operations ,
                        "is_edit" => $is_edit ,
                        "sliderID" => $sliderID ,
                        "slider" => $slider ,
                        'arr_navigations'=>$arr_navigations
                    ));
                ?>


                <div class="clear"></div>
                <script type="text/javascript">


                    <?php
                    $ph_presets_full = array();
                    foreach ($arr_navigations as $nav) {
                        if (isset($nav['settings']) && isset($nav['settings']['presets']) && !empty($nav['settings']['presets'])) {
                            foreach ($nav['settings']['presets'] as $prsst) {
                                if (!isset($ph_presets_full[$nav['handle']][$prsst['type']])) {
                                    $ph_presets_full[$nav['handle']][$prsst['type']] = array();
                                }
                                if (!isset($ph_presets_full[$nav['handle']][$prsst['type']][$prsst['handle']])) {
                                    $ph_presets_full[$nav['handle']][$prsst['type']][$prsst['handle']] = $prsst;
                                }
                            }
                        }
                    }
                    ?>

                    var phpfullresets ;

                    if (phpfullresets == null) phpfullresets = [];

                    var fullresetsstay = false;
                    // Some Inline Script for Right Side Panel Actions.
                    document.addEventListener("DOMContentLoaded", function() {
                        phpfullresets = jQuery.parseJSON(<?php echo RevSliderFunctions::jsonEncodeForClientSide($ph_presets_full); ?>);
                        RevSliderSettings.createModernOnOff();

                        jQuery(".tp-moderncheckbox").each(function () {
                            RevSliderSettings.onoffStatus(jQuery(this));
                        });


                        jQuery('#def-background_fit').change(function () {
                            if (jQuery(this).val() == 'percentage') {
                                jQuery('input[name="def-bg_fit_x"]').show();
                                jQuery('input[name="def-bg_fit_y"]').show();
                            } else {
                                jQuery('input[name="def-bg_fit_x"]').hide();
                                jQuery('input[name="def-bg_fit_y"]').hide();
                            }
                        });

                        jQuery('#slide_bg_position').change(function () {
                            if (jQuery(this).val() == 'percentage') {
                                jQuery('input[name="def-bg_position_x"]').show();
                                jQuery('input[name="def-bg_position_y"]').show();
                            } else {
                                jQuery('input[name="def-bg_position_x"]').hide();
                                jQuery('input[name="def-bg_position_y"]').hide();
                            }
                        });

                        jQuery('#slide_bg_end_position').change(function () {
                            if (jQuery(this).val() == 'percentage') {
                                jQuery('input[name="def-bg_end_position_x"]').show();
                                jQuery('input[name="def-bg_end_position_y"]').show();
                            } else {
                                jQuery('input[name="def-bg_end_position_x"]').hide();
                                jQuery('input[name="def-bg_end_position_y"]').hide();
                            }
                        });

                        jQuery('input[name="def-kenburn_effect"]').change(function () {
                            if (jQuery(this).attr('checked') == 'checked') {
                                jQuery('#def-kenburns-wrapper').show();
                            } else {
                                jQuery('#def-kenburns-wrapper').hide();
                            }
                        });


                        // Accordion
                        jQuery('.settings_wrapper').find('.setting_box h3').each(function () {
                            jQuery(this).click(function () {
                                var btn = jQuery(this),
                                    sb = jQuery(this).closest('.setting_box'),
                                    toclose = btn.hasClass("box_closed") ? true : false;

                                if (btn.closest('.settings_wrapper').hasClass("closeallothers"))
                                    btn.closest('.settings_wrapper').find('.setting_box').each(function () {
                                        var sb = jQuery(this);
                                        sb.find('h3').addClass("box_closed");
                                        sb.find('.inside').slideUp(200);
                                    });
                                else {
                                    sb.find('h3').addClass("box_closed");
                                    sb.find('.inside').slideUp(200);
                                }

                                if (toclose) {
                                    btn.removeClass("box_closed");
                                    sb.find('.inside').slideDown(200);
                                }
                            });
                        });

                        function rs_trigger_color_picker() {
                            jQuery('.my-color-field').not('.rev-cpicker-component').tpColorPicker({
                                mode: "full",
                                defaultValue: '#FFFFFF',
                                wrapper: '<span class="rev-m-colorpickerspan"></span>',
                                change: function (inputElement, color, gradientObj) {
                                    drawToolBarPreview();
                                    try {
                                        inputElement.closest('.placeholder-single-wrapper').find('.placeholder-checkbox').attr('checked', 'checked');
                                    } catch (e) {

                                    }
                                }
                            });


                            jQuery('.alpha-color-field').not('.rev-cpicker-component').tpColorPicker({
                                mode: "single",
                                wrapper: '<span class="rev-m-colorpickerspan"></span>',
                                change: function (inputElement, color, gradientObj) {
                                    drawToolBarPreview();
                                    try {
                                        inputElement.closest('.placeholder-single-wrapper').find('.placeholder-checkbox').attr('checked', 'checked');
                                    } catch (e) {

                                    }
                                }
                            });


                        }


                        rs_trigger_color_picker();

                        jQuery('.wp-color-result').on("click", function () {
                            if (jQuery(this).hasClass("wp-picker-open"))
                                jQuery(this).closest('.wp-picker-container').addClass("pickerisopen");
                            else
                                jQuery(this).closest('.wp-picker-container').removeClass("pickerisopen");
                        });

                        jQuery("body").click(function (event) {
                            jQuery('.wp-picker-container.pickerisopen').removeClass("pickerisopen");
                        })

                        // PREPARE ON/OFF BUTTON
                        jQuery('.tp-onoffbutton .withlabel').each(function () {
                            var wl = jQuery(this),
                                tpo = wl.closest('.tp-onoffbutton');
                            tpo.attr('label', wl.attr('id'));
                            tpo.addClass("withlabel");
                        })

                        jQuery('.wp-picker-container .withlabel').each(function () {
                            var wl = jQuery(this),
                                tpo = wl.closest('.wp-picker-container');
                            tpo.attr('label', wl.attr('id'));
                            tpo.addClass("withlabel");
                        });

                        //----------------------------------------------------
                        // 		DRAW PREVIEW OF NAVIGATION ELEMENTS
                        //----------------------------------------------------

                        function convertAnytoRGBorRGBA(a, c) {
                            if (a === "color" || a === "color-rgba") {
                                if (c.indexOf("rgb") >= 0) {
                                    c = c.split('(')[1].split(")")[0];
                                }
                                else {
                                    c = UniteAdminRev.convertHexToRGB(c);
                                    c = c[0] + "," + c[1] + "," + c[2];
                                }

                                if (a === "color-rgba" && c.split(",").length < 4) c = c + ",1";

                            }
                            return c;
                        }

                        var previewNav = function (sbut, mclass, the_css, the_markup, settings) {

                            var ap = jQuery('#preview-nav-wrapper .rs-arrows-preview'),
                                bp = jQuery('#preview-nav-wrapper .rs-bullets-preview'),
                                tabp = jQuery('#preview-nav-wrapper .rs-tabs-preview'),
                                thumbp = jQuery('#preview-nav-wrapper .rs-thumbs-preview'),
                                sizer = jQuery('#preview-nav-wrapper .little-sizes'),
                                navpre = jQuery('#preview-nav-wrapper');

                            navpre.css({height: 200});


                            ap.html("");
                            bp.html("");
                            tabp.html("");
                            thumbp.html("");

                            ap.hide();
                            bp.hide();
                            tabp.hide();
                            thumbp.hide();
                            sizer.hide();


                            var pattern = new RegExp(":hover", 'g'),
                                parts = the_css.split('##'),
                                sfor = [],
                                counter = 0,
                                ph = "";
                            for (var i = 0; i < parts.length; i++) {
                                if (counter == 1) {
                                    ph = parts[i];
                                    counter = 0;
                                    sfor.push(ph);
                                } else {
                                    counter++;
                                }
                            }


                            if (sbut == "arrows") {
                                ap.show();
                                jQuery.each(sfor, function (i, sf) {

                                    jQuery.each(settings.placeholders, function (i, o) {
                                        if (sf === o.handle && o["nav-type"] === "arrows") {
                                            var rwith = ""
                                            if (jQuery('input[name="ph-' + mclass + '-arrows-' + o.handle + '-' + o.type + '-def"]').attr("checked") === "checked")
                                                rwith = jQuery('input[name="ph-' + mclass + '-arrows-' + o.handle + '-' + o.type + '"]').val();
                                            else
                                                rwith = o.data[o.type];

                                            rwith = convertAnytoRGBorRGBA(o.type, rwith);
                                            the_css = the_css.replace('##' + sf + '##', rwith);
                                        }
                                    });
                                });


                                var t = '<style>' + the_css + '</style>';
                                t = t + '<div class="' + mclass + ' tparrows tp-leftarrow">' + the_markup + '</div>';
                                t = t + '<div class="' + mclass + ' tparrows tp-rightarrow">' + the_markup + '</div>';
                                ap.html(t);

                                var arh = ap.find('.tparrows').first().height() + 40,
                                    pph = navpre.height();


                                if (pph < arh)
                                    navpre.css({height: arh});


                            } else if (sbut == "bullets") {
                                bp.show();

                                jQuery.each(sfor, function (i, sf) {
                                    jQuery.each(settings.placeholders, function (i, o) {
                                        if (sf === o.handle && o["nav-type"] === "bullets") {
                                            var rwith = "";
                                            if (jQuery('input[name="ph-' + mclass + '-bullets-' + o.handle + '-' + o.type + '-def"]').attr("checked") === "checked")
                                                rwith = jQuery('input[name="ph-' + mclass + '-bullets-' + o.handle + '-' + o.type + '"]').val()
                                            else
                                                rwith = o.data[o.type]

                                            rwith = convertAnytoRGBorRGBA(o.type, rwith);

                                            the_css = the_css.replace('##' + sf + '##', rwith);

                                        }
                                    });
                                });


                                var t = '<style>' + the_css + '</style>';
                                t = t + '<div class="' + mclass + ' tp-bullets">'
                                for (var i = 0; i < 5; i++) {
                                    t = t + '<div class="tp-bullet">' + the_markup + '</div>';
                                }
                                t = t + '</div>';
                                bp.html(t);
                                var b = bp.find('.tp-bullet').first(),
                                    bw = jQuery('#bullets_direction option:selected').attr("value") == "horizontal" ? b.outerWidth(true) : b.outerHeight(true),
                                    bh = jQuery('#bullets_direction option:selected').attr("value") == "vertical" ? b.outerWidth(true) : b.outerHeight(true),
                                    mw = 0;
                                bp.find('.tp-bullet').each(function (i) {
                                    var e = jQuery(this);
                                    if (i == 0)
                                        setTimeout(function () {
                                            try {
                                                e.addClass("selected");
                                            } catch (e) {
                                            }
                                        }, 150);


                                    var np = i * bw + i * 10;
                                    if (jQuery('#bullets_direction option:selected').attr("value") == "horizontal") {
                                        e.css({left: np + "px"});
                                    } else {
                                        e.css({top: np + "px"});
                                    }

                                    mw = mw + bw + 10;
                                })
                                mw = mw - 10;
                                if (jQuery('#bullets_direction option:selected').attr("value") == "horizontal") {
                                    bp.find('.tp-bullets').css({width: mw, height: bh});
                                } else {
                                    bp.find('.tp-bullets').css({height: mw, width: bh});
                                }

                                bp.find('.tp-bullets').addClass("nav-pos-ver-" + jQuery('#bullets_align_vert').val()).addClass("nav-pos-hor-" + jQuery('#bullets_align_hor').val()).addClass("nav-dir-" + jQuery('#bullets_direction').val());

                            } else if (sbut == "tabs") {
                                tabp.show();
                                jQuery.each(sfor, function (i, sf) {
                                    jQuery.each(settings.placeholders, function (i, o) {
                                        if (sf === o.handle && o["nav-type"] === "tabs") {
                                            var rwith = "";
                                            if (jQuery('input[name="ph-' + mclass + '-tabs-' + o.handle + '-' + o.type + '-def"]').attr("checked") === "checked")
                                                rwith = jQuery('input[name="ph-' + mclass + '-tabs-' + o.handle + '-' + o.type + '"]').val();
                                            else
                                                rwith = o.data[o.type];
                                            rwith = convertAnytoRGBorRGBA(o.type, rwith);
                                            the_css = the_css.replace('##' + sf + '##', rwith);
                                        }
                                    });
                                });

                                var t = '<style>' + the_css + '</style>';
                                t = t + '<div class="' + mclass + '"><div class="tp-tab">' + the_markup + '</div></div>';
                                tabp.html(t);
                                var s = new Object();
                                s.w = 160,
                                    s.h = 160;
                                if (settings != "" && settings != undefined) {
                                    if (settings.width != undefined && settings.width.tabs != undefined)
                                        s.w = settings.width.tabs;
                                    if (settings.height != undefined && settings.height.tabs != undefined)
                                        s.h = settings.height.tabs;
                                }
                                tabp.find('.tp-tab').each(function () {
                                    jQuery(this).css({width: s.w + "px", height: s.h + "px"});
                                });
                                var tabc = tabp.find('.tp-tab');
                                tabc.addClass("nav-pos-ver-" + jQuery('#tabs_align_vert').val()).addClass("nav-pos-hor-" + jQuery('#tabs_align_hor').val()).addClass("nav-dir-" + jQuery('#tabs_direction').val());

                                var arh = tabc.height() + 40,
                                    pph = navpre.height();


                                if (pph < arh)
                                    navpre.css({height: arh});

                                return s;

                            } else if (sbut == "thumbs") {
                                thumbp.show();
                                jQuery.each(sfor, function (i, sf) {
                                    jQuery.each(settings.placeholders, function (i, o) {
                                        if (sf === o.handle && o["nav-type"] === "thumbs") {
                                            var rwith = "";
                                            if (jQuery('input[name="ph-' + mclass + '-thumbs-' + o.handle + '-' + o.type + '-def"]').attr("checked") === "checked")
                                                rwith = jQuery('input[name="ph-' + mclass + '-thumbs-' + o.handle + '-' + o.type + '"]').val();
                                            else
                                                rwith = o.data[o.type];
                                            rwith = convertAnytoRGBorRGBA(o.type, rwith);
                                            the_css = the_css.replace('##' + sf + '##', rwith);
                                        }
                                    });
                                });

                                var t = '<style>' + the_css + '</style>';
                                t = t + '<div class="' + mclass + '"><div class="tp-thumb">' + the_markup + '</div></div>';
                                thumbp.html(t);
                                var s = new Object();
                                s.w = 160,
                                    s.h = 160;
                                if (settings != "" && settings != undefined) {
                                    if (settings.width != undefined && settings.width.thumbs != undefined)
                                        s.w = settings.width.thumbs;
                                    if (settings.height != undefined && settings.height.thumbs != undefined)
                                        s.h = settings.height.thumbs;
                                }
                                thumbp.find('.tp-thumb').each(function () {
                                    jQuery(this).css({width: s.w + "px", height: s.h + "px"});
                                });

                                var thumbsc = thumbp.find('.tp-thumb').parent();
                                thumbsc.addClass("nav-pos-ver-" + jQuery('#thumbs_align_vert').val()).addClass("nav-pos-hor-" + jQuery('#thumbs_align_hor').val()).addClass("nav-dir-" + jQuery('#thumbs_direction').val());
                                return s;
                            }

                        }


                        fillNavStylePlaceholderOnInit();

                        jQuery('.toggle-custom-navigation-style').click(function () {
                            var p = jQuery(this).closest('.toggle-custom-navigation-style-wrapper'),
                                c = p.find('.toggle-custom-navigation-styletarget');

                            if (c.hasClass("opened")) {
                                c.removeClass("opened");
                                c.slideUp(200);
                            } else {
                                c.slideDown(200);
                                c.addClass("opened");
                            }
                        });


                        function fillNavStylePlaceholderOnInit() {
                            <?php
                            $ph_types = array('navigation_arrow_style' => 'arrows', 'navigation_bullets_style' => 'bullets', 'tabs_style' => 'tabs', 'thumbnails_style' => 'thumbs');
                            foreach($ph_types as $phname => $pht){

                            $ph_arr_type = RevSliderFunctions::getVal($arrFieldsParams, $phname, '');

                            $ph_init = array();
                            $ph_presets = array();
                            foreach ($arr_navigations as $nav) {
                                if ($nav['handle'] == $ph_arr_type) { //check for settings, placeholders
                                    if (isset($nav['settings']) && isset($nav['settings']['placeholders'])) {
                                        foreach ($nav['settings']['placeholders'] as $placeholder) {
                                            if (empty($placeholder)) continue;

                                            $ph_vals = array();
                                            $ph_vals_def = array();

                                            //$placeholder['type']
                                            foreach ($placeholder['data'] as $k => $d) {
                                                $ph_vals[$k] = stripslashes(RevSliderFunctions::getVal($arrFieldsParams, 'ph-' . $ph_arr_type . '-' . $pht . '-' . $placeholder['handle'] . '-' . $k, $d));
                                                $ph_vals_def[$k] = stripslashes(RevSliderFunctions::getVal($arrFieldsParams, 'ph-' . $ph_arr_type . '-' . $pht . '-' . $placeholder['handle'] . '-' . $k . '-def', 'off'));
                                            }

                                            $ph_init[] = array('nav-type' => @$placeholder['nav-type'], 'title' => @$placeholder['title'], 'handle' => $placeholder['handle'], 'type' => $placeholder['type'], 'data' => $ph_vals, 'default' => $ph_vals_def);
                                        }
                                        if (!empty($ph_vals) && isset($nav['settings']['presets']) && !empty($nav['settings']['presets'])) {
                                            $ph_presets[$nav['handle']] = $nav['settings']['presets'];
                                        }
                                    }
                                    break;
                                }
                            }

                            if(!empty($ph_init)){
                            ?>
                            var phvals = jQuery.parseJSON(<?php echo RevSliderFunctions::jsonEncodeForClientSide($ph_init); ?>);
                            var phpresets = jQuery.parseJSON(<?php echo RevSliderFunctions::jsonEncodeForClientSide($ph_presets); ?>);

                            //check for values
                            showNavStylePlaceholder('<?php echo $pht; ?>', phvals, phpresets);
                            <?php
                            }
                            }
                            ?>
                        }


                        function showNavStylePlaceholder(navtype, vals, phpresets) {

                            var cur_edit_type;
                            var cur_edit = {};
                            var ph_div;
                            var ph_preset_sel;
                            switch (navtype) {
                                case 'arrows':
                                    cur_edit_type = jQuery('#navigation_arrow_style option:selected').attr('value');
                                    ph_div = jQuery('.navigation_arrow_placeholder');
                                    ph_preset_sel = jQuery('#navigation_arrows_preset');
                                    break;
                                case 'bullets':
                                    cur_edit_type = jQuery('#navigation_bullets_style option:selected').attr('value');
                                    ph_div = jQuery('.navigation_bullets_placeholder');
                                    ph_preset_sel = jQuery('#navigation_bullets_preset');
                                    break;
                                case 'tabs':
                                    cur_edit_type = jQuery('#tabs_style option:selected').attr('value');
                                    ph_div = jQuery('.navigation_tabs_placeholder');
                                    ph_preset_sel = jQuery('#navigation_tabs_preset');
                                    break;
                                case 'thumbs':
                                    cur_edit_type = jQuery('#thumbnails_style option:selected').attr('value');
                                    ph_div = jQuery('.navigation_thumbs_placeholder');
                                    ph_preset_sel = jQuery('#navigation_thumbs_preset');
                                    break;
                                default:
                                    return false;
                                    break;
                            }


                            var current_value = ph_preset_sel.val();

                            ph_div.html('');
                            ph_preset_sel.find('option').each(function () {
                                if (!jQuery(this).hasClass('never')) jQuery(this).remove();
                            });


                            if (vals == undefined) {
                                for (var key in rs_navigations) {
                                    if (rs_navigations[key]['handle'] == cur_edit_type) {
                                        cur_edit = jQuery.extend(true, {}, rs_navigations[key]);
                                        break;
                                    }
                                }
                            } else {
                                cur_edit = {'settings': {'placeholders': vals}};
                            }

                            var tcnsw = ph_div.closest('.toggle-custom-navigation-style-wrapper'),
                                holder_type = tcnsw.data('navtype');

                            if (cur_edit['settings'] == undefined || cur_edit['settings']['placeholders'] == undefined) {
                                tcnsw.find('.toggle-custom-navigation-style').removeClass("visible");
                                return false;
                            }

                            var count = 0;
                            for (var key in cur_edit['settings']['placeholders']) {

                                var m = cur_edit['settings']['placeholders'][key];

                                if (holder_type == m['nav-type']) count++;

                                if (m['default'] == undefined) m['default'] = {};

                                if (jQuery.isEmptyObject(m)) continue;

                                var deftype = (m["type"] == 'font-family') ? 'font_family' : m["type"];

                                var isfor = 'ph-' + cur_edit_type + '-' + navtype + '-' + m['handle'] + '-' + deftype,
                                    ph_title = (m['title'] == undefined) ? '##' + m['handle'] + '##' : m['title'],
                                    ph_html = '<div class="placeholder-single-wrapper ' + m["nav-type"] + '"><input type="checkbox" name="ph-' + cur_edit_type + '-' + navtype + '-' + m['handle'] + '-' + deftype + '-def" data-isfor="' + isfor + '" class="tp-moderncheckbox placeholder-checkbox custom-preset-val triggernavstyle" data-unchecked="off" ';

                                ph_html += (m['default'][deftype] == undefined || m['default'][deftype] == 'off') ? '>' : ' checked="checked">';
                                ph_html += '<span class="label placeholder-label triggernavstyle">' + ph_title + '</span>';

                                switch (m['type']) {
                                    case 'color':
                                        ph_html += '<input type="text" name="ph-' + cur_edit_type + '-' + navtype + '-' + m['handle'] + '-color" class="triggernavstyle alpha-color-field custom-preset-val" id="' + isfor + '" value="' + m['data']['color'] + '">';
                                        break;
                                    case 'color-rgba':
                                        ph_html += '<input type="text" name="ph-' + cur_edit_type + '-' + navtype + '-' + m['handle'] + '-color-rgba" class="triggernavstyle alpha-color-field custom-preset-val" id="' + isfor + '" value="' + m['data']['color-rgba'] + '">';
                                        break;
                                    case 'font-family':
                                        ph_html += '<select style="width:112px" name="ph-' + cur_edit_type + '-' + navtype + '-' + m['handle'] + '-font_family" class="triggernavstyle custom-preset-val" id="' + isfor + '">';
                                    <?php
                                    $font_families = $operations->getArrFontFamilys();
                                    foreach($font_families as $handle => $name){
                                    if ($name['label'] == 'Dont Show Me') continue;
                                    ?>
                                        ph_html += '<option value="<?php echo RevSliderFunctions::esc_attr($name['label']); ?>"';
                                        if (m['data']['font_family'] == '<?php echo RevSliderFunctions::esc_attr($name['label']); ?>') {
                                            ph_html += ' selected="selected"';
                                        }
                                        ph_html += '><?php echo RevSliderFunctions::esc_attr($name['label']); ?></option>';
                                    <?php
                                    }
                                    ?>
                                        ph_html += '</select>';

                                        break;
                                    case 'custom':
                                        ph_html += '<input type="text" name="ph-' + cur_edit_type + '-' + navtype + '-' + m['handle'] + '-custom" value="' + m['data']['custom'] + '" class="triggernavstyle custom-preset-val" id="' + isfor + '">';
                                        break;

                                }

                                ph_html += '</div><div class="clear"></div>';
                                if (holder_type == m['nav-type']) ph_div.prepend(ph_html);
                            }


                            if (phpresets !== undefined) {
                                //add presets in the box
                                for (var key in phpresets) {
                                    if (key == cur_edit_type) {
                                        for (var kkey in phpresets[key]) {
                                            if (phpresets[key][kkey]['type'] == navtype)
                                                ph_preset_sel.append('<option value="' + phpresets[key][kkey]['handle'] + '">' + phpresets[key][kkey]['name'] + '</option>');
                                        }
                                    }
                                }

                                //select the correct preset in select box
                                ph_preset_sel.find('option[value="' + ph_preset_sel.data('startvalue') + '"]').attr('selected', true);

                            } else {
                                //phpfullresets
                                //fill the field, as we have changed the nav type, and set the select to default
                                for (var key in phpfullresets) {
                                    if (key == cur_edit_type) {
                                        if (phpfullresets[key][navtype] !== undefined) {
                                            for (var kkey in phpfullresets[key][navtype]) {
                                                ph_preset_sel.append('<option value="' + phpfullresets[key][navtype][kkey]['handle'] + '">' + phpfullresets[key][navtype][kkey]['name'] + '</option>');
                                            }
                                        }
                                    }
                                }

                                if (ph_preset_sel.find('option[value="' + current_value + '"]').length > 0) {
                                    ph_preset_sel.find('option[value="' + current_value + '"]').attr('selected', true);
                                } else {
                                    ph_preset_sel.find('option[value="default"]').attr('selected', true);
                                }

                                if (fullresetsstay !== false) {
                                    if (ph_preset_sel.find('option[value="' + fullresetsstay + '"]').length > 0) {
                                        ph_preset_sel.find('option[value="' + fullresetsstay + '"]').attr('selected', true);
                                    }
                                }


                                ph_preset_sel.change();
                            }
                            fullresetsstay = false;


                            if (count > 0) {
                                tcnsw.find('.toggle-custom-navigation-style').addClass("visible");
                                ph_div.append('<span class="overwrite-arrow"></span><span class="placeholder-description"><?php echo t('Check to use Custom') ?></span>');
                            } else {
                                tcnsw.find('.toggle-custom-navigation-style').removeClass("visible");
                            }
                            ph_div.append('<div class="tp-clear"></div><div class="save-navigation-style-as-preset"><?php echo t("Save as Preset") ?></div><div class="delete-navigation-style-as-preset"><?php echo t("Delete Preset") ?></div>');

                            rs_trigger_color_picker();
                        }


                        /**
                         * If changed, set parent to Custom!
                         **/
                        jQuery('body').on('change', '.custom-preset-val', function () {
                            var navtype = jQuery(this).closest('.toggle-custom-navigation-style-wrapper').data('navtype');

                            switch (navtype) {
                                case 'arrows':
                                    jQuery('#navigation_arrows_preset option[value="custom"]').attr('selected', true);
                                    break;
                                case 'bullets':
                                    jQuery('#navigation_bullets_preset option[value="custom"]').attr('selected', true);
                                    break;
                                case 'thumbs':
                                    jQuery('#navigation_thumbs_preset option[value="custom"]').attr('selected', true);
                                    break;
                                case 'tabs':
                                    jQuery('#navigation_tabs_preset option[value="custom"]').attr('selected', true);
                                    break;
                            }
                        });


                        jQuery('body').on('change', '#navigation_arrows_preset, #navigation_bullets_preset, #navigation_tabs_preset, #navigation_thumbs_preset', function () {
                            var myv = jQuery(this).val();

                            var type = 'arrows';
                            var sel_value = '';
                            switch (jQuery(this).attr('id')) {
                                case 'navigation_arrows_preset':
                                    type = 'arrows';
                                    sel_value = jQuery('#navigation_arrow_style option:selected').val();
                                    break;
                                case 'navigation_bullets_preset':
                                    type = 'bullets';
                                    sel_value = jQuery('#navigation_bullets_style option:selected').val();
                                    break;
                                case 'navigation_tabs_preset':
                                    type = 'tabs';
                                    sel_value = jQuery('#tabs_style option:selected').val();
                                    break;
                                case 'navigation_thumbs_preset':
                                    type = 'thumbs';
                                    sel_value = jQuery('#thumbnails_style option:selected').val();
                                    break;
                            }

                            if (myv == 'default' || myv !== 'custom') {
                                //uncheck all checkboxes, set the values to default values
                                for (var key in rs_navigations) {
                                    if (rs_navigations[key]['handle'] == sel_value) {
                                        if (rs_navigations[key]['settings'] !== undefined && rs_navigations[key]['settings']['placeholders'] !== undefined) {
                                            for (var kkey in rs_navigations[key]['settings']['placeholders']) {
                                                var m = rs_navigations[key]['settings']['placeholders'][kkey];
                                                switch (m['type']) {
                                                    case 'color':
                                                        jQuery('input[name="ph-' + sel_value + '-' + type + '-' + m['handle'] + '-color"]').val(m['data']['color']);
                                                        jQuery('input[name="ph-' + sel_value + '-' + type + '-' + m['handle'] + '-color-def"]').attr('checked', false);
                                                        break;
                                                    case 'color-rgba':
                                                        jQuery('input[name="ph-' + sel_value + '-' + type + '-' + m['handle'] + '-color"]').val(m['data']['color-rgba']);
                                                        jQuery('input[name="ph-' + sel_value + '-' + type + '-' + m['handle'] + '-color-def"]').attr('checked', false);
                                                        break;
                                                    case 'font_family':
                                                        jQuery('select name=["ph-' + sel_value + '-' + type + '-' + m['handle'] + '-font_family"] option[value="' + m['data']['font_family'] + '"]').attr('selected', true);
                                                        jQuery('input[name="ph-' + sel_value + '-' + type + '-' + m['handle'] + '-font-family-def"]').attr('checked', false);
                                                        break;
                                                    case 'custom':
                                                        jQuery('input[name="ph-' + sel_value + '-' + type + '-' + m['handle'] + '-custom"]').val(m['data']['custom']);
                                                        jQuery('input[name="ph-' + sel_value + '-' + type + '-' + m['handle'] + '-custom-def"]').attr('checked', false);
                                                        break;
                                                }
                                            }
                                        }
                                        break;
                                    }
                                }
                            }

                            if (myv == 'custom') {
                                //leave all as it is
                            } else {
                                //default values were set before, now set the values of the preset
                                if (phpfullresets !== null && phpfullresets[sel_value] !== undefined && phpfullresets[sel_value][type] !== undefined && phpfullresets[sel_value][type][myv] !== undefined) {
                                    if (phpfullresets[sel_value][type][myv]['values'] !== undefined) {
                                        for (var key in phpfullresets[sel_value][type][myv]['values']) {
                                            jQuery('#' + key).val(phpfullresets[sel_value][type][myv]['values'][key]);
                                            jQuery('input[name="' + key + '-def"]').attr('checked', true);
                                        }
                                    }
                                }
                            }
                        });


                        jQuery('body').on('click', '.save-navigation-style-as-preset', function () {
                            //what type am I
                            var ce = jQuery(this);
                            var nav_type = ce.closest('.toggle-custom-navigation-style-wrapper').data('navtype');

                            //set a name
                            jQuery('.rs-dialog-save-nav-preset').dialog({
                                modal: true,
                                resizable: false,
                                minWidth: 400,
                                minHeight: 300,
                                closeOnEscape: true,
                                buttons: {
                                    '<?php echo t('Save As') ?>': function () {

                                        var preset_name = jQuery('input[name="preset-name"]').val();
                                        var preset_handle = UniteAdminRev.sanitize_input(jQuery('input[name="preset-name"]').val());
                                        var preset_navigation = nav_type;
                                        var preset_values = {};
                                        var preset_nav_handle = '';

                                        switch (nav_type) {
                                            case 'arrows':
                                                var ph_class = '.navigation_arrow_placeholder';
                                                preset_nav_handle = jQuery('#navigation_arrow_style option:selected').val();
                                                break;
                                            case 'bullets':
                                                var ph_class = '.navigation_bullets_placeholder';
                                                preset_nav_handle = jQuery('#navigation_bullets_style option:selected').val();
                                                break;
                                            case 'thumbs':
                                                var ph_class = '.navigation_thumbs_placeholder';
                                                preset_nav_handle = jQuery('#thumbnails_style option:selected').val();
                                                break;
                                            case 'tabs':
                                                var ph_class = '.navigation_tabs_placeholder';
                                                preset_nav_handle = jQuery('#tabs_style option:selected').val();
                                                break;
                                            default:
                                                return false;
                                                break;
                                        }

                                        var overwrite = false;
                                        //check if preset handle is already existing!
                                        if (typeof(phpfullresets[preset_nav_handle]) !== 'undefined' && typeof(phpfullresets[preset_nav_handle][nav_type]) !== 'undefined') {
                                            if (typeof(phpfullresets[preset_nav_handle][nav_type][preset_handle]) !== 'undefined') {
                                                //handle already exists, change it
                                                if (!confirm('<?php echo t('Handle already exists, overwrite settings for this preset?') ?>')) {
                                                    return false;
                                                }
                                                overwrite = true;
                                            }
                                        }

                                        //get values where checkbox is selected
                                        jQuery(ph_class + ' input[type="checkbox"]').each(function () {
                                            if (jQuery(this).is(':checked')) {
                                                var jqrobj = jQuery('#' + jQuery(this).data('isfor'));

                                                preset_values[jqrobj.attr('name')] = jqrobj.val();
                                            }
                                        });

                                        if (!jQuery.isEmptyObject(preset_values)) {

                                            var data = {
                                                navigation: preset_nav_handle,
                                                name: preset_name,
                                                handle: preset_handle,
                                                type: preset_navigation,
                                                do_overwrite: overwrite,
                                                values: preset_values
                                            }

                                            UniteAdminRev.ajaxRequest('create_navigation_preset', data, function (response) {
                                                reload_navigation_preset_fields(response.navs, preset_handle);

                                                jQuery('.rs-dialog-save-nav-preset').dialog('close');
                                            });
                                        } else {
                                            alert('<?php echo t('No fields are checked, please check at least one field to save a preset') ?>');
                                        }

                                    }
                                }
                            });


                            //if exists, overwrite existing
                        });

                        jQuery('body').on('click', '.delete-navigation-style-as-preset', function () {
                            //check if we are default or custom
                            //if not, ask to really delete

                            var nav_type = jQuery(this).closest('.toggle-custom-navigation-style-wrapper').data('navtype');

                            var cur_preset = jQuery('#navigation_' + nav_type + '_preset option:selected').val();

                            if (cur_preset !== 'default' && cur_preset !== 'custom' && cur_preset !== undefined) { //default and custom can not be deleted
                                if (confirm('<?php echo t('Delete this Navigation Preset?') ?>')) {
                                    var style_handle = '';
                                    switch (nav_type) {
                                        case 'arrows':
                                            style_handle = jQuery('#navigation_arrow_style option:selected').val();
                                            break;
                                        case 'bullets':
                                            style_handle = jQuery('#navigation_bullets_style option:selected').val();
                                            break;
                                        case 'tabs':
                                            style_handle = jQuery('#tabs_style option:selected').val();
                                            break;
                                        case 'thumbs':
                                            style_handle = jQuery('#thumbnails_style option:selected').val();
                                            break;
                                    }

                                    //delete that entry
                                    var data = {
                                        style_handle: style_handle,
                                        handle: cur_preset,
                                        type: nav_type
                                    }


                                    UniteAdminRev.ajaxRequest('delete_navigation_preset', data, function (response) {

                                        reload_navigation_preset_fields(response.navs);

                                    });

                                }
                            }
                        });


                        function reload_navigation_preset_fields(navs, stay_at_current) {

                            //reload select boxes!
                            for (var key in navs) {
                                if (navs[key]['settings'] !== null && navs[key]['settings'] !== undefined && navs[key]['settings']['presets'] !== undefined) {
                                    phpfullresets[navs[key]['handle']] = {};

                                    for (var kkey in navs[key]['settings']['presets']) { //push values into phpfullresets
                                        var m = navs[key]['settings']['presets'][kkey];

                                        if (phpfullresets[navs[key]['handle']][m['type']] == undefined) phpfullresets[navs[key]['handle']][m['type']] = {};

                                        phpfullresets[navs[key]['handle']][m['type']][m['handle']] = m;
                                    }
                                }
                            }

                            //select the new created preset
                            //and select the things on other elements as they have changed to default

                            //now reset all fields with the new phpfullresets
                            if (stay_at_current !== undefined) {
                                fullresetsstay = stay_at_current;
                            }
                            showNavStylePlaceholder('arrows');
                            showNavStylePlaceholder('bullets');
                            showNavStylePlaceholder('tabs');
                            showNavStylePlaceholder('thumbs');

                        }


                        function changeNavStyle(navtype) {
                            var cur_edit = {},
                                cur_edit_type,
                                navtype,
                                nav_id,
                                mclass = "";

                            if (navtype == "arrows")
                                cur_edit_type = jQuery('#navigation_arrow_style option:selected').attr('value');
                            else if (navtype == 'bullets')
                                cur_edit_type = jQuery('#navigation_bullets_style option:selected').attr('value');
                            else if (navtype == 'tabs')
                                cur_edit_type = jQuery('#tabs_style option:selected').attr('value');
                            else if (navtype == 'thumbs')
                                cur_edit_type = jQuery('#thumbnails_style option:selected').attr('value');

                            for (var key in rs_navigations) {
                                if (rs_navigations[key]['handle'] == cur_edit_type) {
                                    cur_edit = jQuery.extend(true, {}, rs_navigations[key]);
                                    break;
                                }
                            }

                            var the_css = (typeof(cur_edit['css']) !== 'undefined' && cur_edit['css'] !== null && typeof(cur_edit['css'][navtype]) !== 'undefined') ? cur_edit['css'][navtype] : '',
                                the_markup = (typeof(cur_edit['markup']) !== 'undefined' && cur_edit['markup'] !== null && typeof(cur_edit['markup'][navtype]) !== 'undefined') ? cur_edit['markup'][navtype] : "",
                                settings = (typeof(cur_edit['settings']) !== 'undefined' && cur_edit['settings'] !== null) ? cur_edit['settings'] : "";

                            if (cur_edit["name"] == undefined) return false;
                            var mclass = UniteAdminRev.sanitize_input(cur_edit["name"].toLowerCase());


                            if (cur_edit['css'] == null) return false;
                            if (cur_edit['markup'] == null) return false;


                            return previewNav(navtype, mclass, the_css, the_markup, settings);

                        }

                        function callChangeNavStyle(e) {
                            prev.hide();
                            navpre.show();
                            punchgs.TweenLite.set(navpre, {autoAlpha: 1});

                            if (e.closest('#nav_arrows_subs').length > 0) {
                                navtype = 'arrows';
                                title.html("Arrow Styling");
                            }
                            else if (e.closest('#nav_bullets_subs').length > 0) {
                                navtype = 'bullets';
                                title.html("Bullet Styling");
                            }
                            else if (e.closest('#nav_tabs_subs').length > 0) {
                                navtype = 'tabs';
                                title.html("Tabs Styling");
                            }
                            else if (e.closest('#nav_thumbnails_subs').length > 0) {
                                navtype = 'thumbs';
                                title.html("Thumbnails Styling");
                            }

                            var s = changeNavStyle(navtype, jQuery(this));
                            if (s != undefined)
                                cont.html("Suggested Size:" + s.w + " x " + s.h + "px");
                            else cont.hide();

                        }


                        jQuery('body').on('mouseenter', '.label, .withlabel, .triggernavstyle, #form_toolbar', function () {
                            drawToolBarPreview();

                            var lbl = jQuery(this).hasClass("withlabel") ? (jQuery(this).attr('id') === undefined ? jQuery("#label_" + jQuery(this).attr("label")) : jQuery("#label_" + jQuery(this).attr("id"))) : jQuery(this),
                                ft = jQuery('#form_slider_params').offset().top;
                            tb = jQuery('#form_toolbar'),
                                title = tb.find('.toolbar-title'),
                                cont = tb.find('.toolbar-content'),
                                med = tb.find('.toolbar-media'),
                                prev = tb.find('.toolbar-sliderpreview'),
                                shads = tb.find('.toolbar-shadows'),
                                img = tb.find('.toolbar-slider-image'),
                                exti = tb.find('.toolbar-extended-info'),
                                navpre = jQuery('#preview-nav-wrapper');


                            if (jQuery(this).attr('id') !== "form_toolbar") {
                                if (!jQuery(this).hasClass("triggernavstyle")) {
                                    navpre.css({height: 200});
                                    title.html(lbl.html());
                                    cont.html(lbl.attr('origtitle'));
                                }


                                /*	if (lbl.attr('extendedinfo')!=undefined)
                                 exti.html(lbl.attr('extendedinfo'));

                                 /*if (lbl.attr('origmedia')===undefined) {
                                 prev.slideUp(150);
                                 shads.slideUp(150);
                                 }*/

                                if (lbl.attr('origmedia') == "show" || lbl.attr('origmedia') == "showbg") {
                                    prev.slideDown(150);
                                    shads.slideDown(150);
                                }

                                if (lbl.attr('origmedia') == "showbg")
                                    img.addClass('shownowbg');
                                else
                                    img.removeClass('shownowbg');

                                var topp = (lbl.offset().top - ft - 14),
                                    hh = tb.outerHeight(),
                                    so = jQuery(document).scrollTop(),
                                    foff = jQuery('#form_slider_params').offset().top,
                                    wh = jQuery(window).height(),
                                    diff = (so + wh - foff) - (topp + hh);

                                if (diff < 0) topp = topp + diff;


                                if (lbl.hasClass("triggernavstyle")) {
                                    callChangeNavStyle(jQuery(this));
                                } else {
                                    cont.show();
                                    prev.show();
                                    navpre.hide();
                                }
                                punchgs.TweenLite.to(tb, 0.5, {
                                    autoAlpha: 1,
                                    right: "100%",
                                    top: topp,
                                    ease: punchgs.Power3.easeOut,
                                    overwrite: "all"
                                });
                            } else {
                                punchgs.TweenLite.to(tb, 0.5, {autoAlpha: 1, overwrite: "all"});
                            }

                        });

                        jQuery('body').on('mouseleave', '.label, .withlabel, .triggernavstyle, #form_toolbar', function () {
                            punchgs.TweenLite.to(jQuery('#form_toolbar'), 0.2, {
                                autoAlpha: 0,
                                ease: punchgs.Power3.easeOut,
                                delay: 0.2
                            });
                        });


                        jQuery('body').on('click', '.placeholder-single-wrapper input.custom-preset-val', function () {
                            if (!jQuery(this).is(":checkbox"))
                                jQuery(this).closest('.placeholder-single-wrapper').find('.placeholder-checkbox').attr('checked', 'checked');
                            return true;
                        });

                        jQuery('body').on('keyup', '.placeholder-single-wrapper input', function () {
                            callChangeNavStyle(jQuery(this));
                        });

                        jQuery('body').on('mousemove', '.toggle-custom-navigation-styletarget', function () {
                            callChangeNavStyle(jQuery(this));
                            punchgs.TweenLite.to(jQuery('#form_toolbar'), 0.2, {
                                autoAlpha: 1,
                                ease: punchgs.Power3.easeOut,
                                overwrite: "auto"
                            });
                        });


                        jQuery('#navigation_arrow_style, #navigation_bullets_style, #tabs_style, #thumbnails_style').on("change", function () {
                            var e = jQuery(this),
                                tb = jQuery('#form_toolbar'),
                                title = tb.find('.toolbar-title'),
                                cont = tb.find('.toolbar-content');

                            if (e.closest('#nav_arrows_subs').length > 0)
                                navtype = 'arrows';
                            else if (e.closest('#nav_bullets_subs').length > 0)
                                navtype = 'bullets';
                            else if (e.closest('#nav_tabs_subs').length > 0)
                                navtype = 'tabs';
                            else if (e.closest('#nav_thumbnails_subs').length > 0)
                                navtype = 'thumbs';

                            var s = changeNavStyle(navtype, jQuery(this));

                            showNavStylePlaceholder(navtype);

                            if (s != undefined)
                                cont.html("<?php echo t('Suggested Size:') ?>" + s.w + " x " + s.h + "px");
                            else cont.hide();
                        });
                        var rs_navigations = jQuery.parseJSON(<?php echo RevSliderFunctions::jsonEncodeForClientSide($arr_navigations); ?>);

                        jQuery(".button-image-select-bg-img").click(function () {
                            UniteAdminRev.openAddImageDialog("Choose Image", function (urlImage, imageID) {
                                //update input:
                                jQuery("#background_image").val(urlImage);
                            });
                        });
                        jQuery(".button-image-select-background-img").click(function () {
                            UniteAdminRev.openAddImageDialog("Choose Image", function (urlImage, imageID) {
                                //update input:
                                jQuery("#show_alternate_image").val(urlImage);
                            });
                        });

                    });
                </script>
            </div>

        </div>


        <!-- PRESET SAVING DIALOG -->
        <div id="dialog-rs-add-new-setting-presets" title="<?php echo t('Save Settings as Preset') ?>"
             style="display:none;">
            <div class="settings_wrapper unite_settings_wide">
                <p><label><?php echo t('Preset Name') ?></label> <input type="text" name="rs-preset-name"/>
                </p>
                <p><label><?php echo t('Select Image') ?></label> <input type="button"
                                                                                   value="<?php echo t('Select'); ?>"
                                                                                   name="rs-button-select-img"/></p>
                <input type="hidden" name="rs-preset-image-id" value=""/>
                <div id="rs-preset-img-wrapper">

                </div>
            </div>
        </div>

        <?php
        $exampleID = '"slider1"';
        ?>
        <!--
THE INFO ABOUT EMBEDING OF THE SLIDER
-->
        <div class="rs-dialog-embed-slider" style="display: none;">
            <div class="revyellow"
                 style="background: none repeat scroll 0% 0% #F1C40F; left:0px;top:36px;position:absolute;height:224px;padding:20px 10px;">
                <i style="color:#fff;font-size:25px" class="revicon-arrows-ccw"></i></div>
            <div style="margin:5px 0px; padding-left: 55px;">
                <div style="font-size:14px;margin-bottom:10px;">
                    <strong><?php echo t("Standard Embeding") ?></strong></div>
                <?php echo t("In <b>Block layout</b> editor : Place Block with <b>Category</b> = `<b>RevSlider block</b>` and <b>Block</b> = `<b><code
                            class=\"rs-example-title\"></code></b>`") ?>
            </div>
        </div>



        <div class="rs-dialog-save-nav-preset" title="<?php echo t('Add Navigation Preset'); ?>" style="display: none;">
            <p><label><?php echo t('Preset Name:'); ?></label><input type="text" name="preset-name" value=""/></p>
        </div>


        <script type="text/html" id="tmpl-rs-preset-container">
            <span class="rs-preset-selector rs-preset-entry {{ data['type'] }} {{ data['class'] }} "
                  id="rs-preset-{{ data['key'] }}">
		<span class="rs-preset-image"<# if( data['img'] !== '' ){ #> style="background-image: url({{ data['img'] }});"<# } #>>
		<# if( data['custom'] == true ){ #><span class="rev-update-preset"><i class="revicon-pencil-1"></i></span><span
                    class="rev-remove-preset"><i class="revicon-cancel"></i></span><# } #>
		</span>
            <span class="rs-preset-label">{{ data['name'] }}</span>
            </span>
        </script>
        <?php
        return ob_get_clean();
    }
}