<?php

/**
 * Created by FsFlex.
 * User: VH
 * Date: 7/29/2017
 * Time: 10:28 AM
 */
namespace Drupal\revslider\Controller\RawTemplates\SliderMainOptions;

use Drupal\revslider\Helper\RevSliderFunctions;
use Drupal\revslider\Helper\RevSliderFunctionsWP;
use Drupal\revslider\Model\Operations;

class SettingsPanelLeft
{
    public function getTemplate(array $args)
    {
        extract($args);

        ob_start();
        ?>
        <div class="settings_panel_left settings_wrapper<?php echo $settings_wrapper_class; ?>">
            <form name="form_slider_main" id="form_slider_main" onkeypress="return event.keyCode != 13;">

                <input type="hidden" name="hero_active"
                       value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'hero_active', -1); ?>"/>

                <?php $index_content = 1; ?>
                <!-- SLIDER TITLE AND SHORTCODE -->
                <div id="slider_title_sb" class="setting_box" style="background:#fff">
                    <h3>
                        <span class="setting-step-number"><?php echo $index_content++ ?></span><span><?php echo t("Slider Title & Alias") ?></span>
                    </h3>
                    <div class="inside">
                        <div class="slidertitlebox">

							<span class="one-third-container">
								<input placeholder='<?php echo t("Enter your Slider Name here") ?>' type="text"
                                       class='regular-text' id="title" name="title"
                                       value="<?php echo RevSliderFunctions::esc_attr(stripslashes(RevSliderFunctions::getVal($arrFieldsParams, 'title', ''))); ?>"/>
								<i class="input-edit-icon"></i>
								<span class="description"><?php echo t("The title of the slider, example: Slider 1") ?></span>
							</span>

                            <span class="one-third-container">
								<input placeholder='<?php echo t("Enter your Slider Alias here") ?>'
                                       type="text" class='regular-text' id="alias" name="alias"
                                       value="<?php echo RevSliderFunctions::esc_attr(stripslashes(RevSliderFunctions::getVal($arrFieldsParams, 'alias', ''))); ?>"/>
								<i class="input-edit-icon"></i>
								<span class="description"><?php echo t("The alias for embedding your slider, example: slider1") ?></span>
							</span>

<!--                            <span class="one-third-container">-->
<!--								<input type="text" class='regular-text code' readonly='readonly' id="shortcode"-->
<!--                                       name="shortcode" value=""/>-->
<!--								<i class="input-shortcode-icon"></i>-->
<!--								<span class="description">--><?php //echo t("Place the shortcode where you want to show the slider") ?><!--</span>-->
<!--							</span>-->
                        </div>


                    </div>
                </div>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        jQuery('#button_save_slider_t').on('click',function (e) {
                            var main_form = jQuery('#form_slider_main');
                            if(!(main_form.find('#title').val() && main_form.find('#alias').val()))
                            {
                                confirm('<?php echo t("Slider Title & Alias is required") ?>');
                                e.preventDefault();
                            }
                        });
                    })
                </script>
                <!-- THE SLIDE TYPE CHOOSER -->
                <div id="slider_type_sb" class="setting_box">
                    <h3>
                        <span class="setting-step-number"><?php echo $index_content++ ?></span><span><?php echo t("Select a Slider Type") ?></span>
                    </h3>
                    <?php $slider_type = RevSliderFunctions::getVal($arrFieldsParams, 'slider-type', 'standard'); ?>
                    <div class="rs-slidetypeselector">

                        <div data-mode="standardpreset"
                             class="rs-slidertype<?php echo ($slider_type == 'standard') ? ' selected' : ''; ?>">
                            <span class="rs-preset-image standardslider"></span>
                            <span class="rs-preset-label"><?php echo t("Standard Slider") ?></span>
                            <input style="display: none;" type="radio" name="slider-type"
                                   value="standard" <?php RevSliderFunctions::checked($slider_type, 'standard'); ?> />
                        </div>
                        <div data-mode="heropreset"
                             class="rs-slidertype<?php echo ($slider_type == 'hero') ? ' selected' : ''; ?>">
                            <span class="rs-preset-image heroscene"></span>
                            <span class="rs-preset-label"><?php echo t("Hero Scene") ?></span>
                            <input style="display: none;" type="radio" name="slider-type"
                                   value="hero" <?php RevSliderFunctions::checked($slider_type, 'hero'); ?> />
                        </div>
                        <div data-mode="carouselpreset"
                             class="rs-slidertype<?php echo ($slider_type == 'carousel') ? ' selected' : ''; ?>">
                            <span class="rs-preset-image carouselslider"></span>
                            <span class="rs-preset-label"><?php echo t("Carousel Slider") ?></span>
                            <input style="display: none;" type="radio" name="slider-type"
                                   value="carousel" <?php RevSliderFunctions::checked($slider_type, 'carousel'); ?> />
                        </div>
                    </div>
                    <span class="preset-splitter readytoopen"><?php echo t("Load a Preset from this Slider Type") ?>
                        <i class="eg-icon-down-open"></i></span>
                    <div id="preset-selector-wrapper" class="preset-selector-wrapper" style="display:none">
                        <div id="preselect-horiz-wrapper" class="preselect-horiz-wrapper">
                            <?php $presets = Operations::get_preset_settings() ?>
                            <span class="rs-preset-selector rs-do-nothing standardpreset heropreset carouselpreset"
                                  id="rs-add-new-settings-preset">
                            <span class="rs-preset-image"
                                  style="background-image: url('<?php echo RevSliderFunctions::asset('/admin/images/mainoptions/add_preset.png') ?>')"></span>
                            <span class="rs-preset-label"><?php echo t('Save Current Settings as Preset') ?> </span>
                            </span>
                            <script type="text/javascript">
                                <?php $pjs = RevSliderFunctions::jsonEncodeForClientSide($presets);?>
                                var revslider_presets;
                                document.addEventListener("DOMContentLoaded", function () {
                                    revslider_presets = JSON.parse(<?php echo $pjs ?>);
                                })
                            </script>
                            <span class="tp-clearfix"></span>
                        </div>
                    </div>
                    <script type="text/javascript">
                        var preset_template_container;
                        var googlef_template_container;
                        function rs_reset_preset_html() {

                            jQuery('.rs-preset-entry').remove();

                            for (var key in revslider_presets) {

                                if (typeof(revslider_presets[key]['settings']['preset']) === 'undefined') {
                                    revslider_presets[key]['settings']['preset'] = 'standardpreset';
                                }

                                var data = {};
                                data['key'] = key;
                                data['name'] = revslider_presets[key]['settings']['name'];
                                data['type'] = revslider_presets[key]['settings']['preset'];
                                data['img'] = (typeof(revslider_presets[key]['settings']['image']) !== 'undefined') ? revslider_presets[key]['settings']['image'] : '';
                                data['class'] = (typeof(revslider_presets[key]['settings']['class']) !== 'undefined') ? revslider_presets[key]['settings']['class'] : '';
                                data['custom'] = (typeof(revslider_presets[key]['settings']['custom']) !== 'undefined' && revslider_presets[key]['settings']['custom'] == true) ? true : false;

                                var content = preset_template_container(data);

                                jQuery('#rs-add-new-settings-preset').before(content);
                            }

                            jQuery('.rs-slidertype.selected').click(); //show only for current active type
                        }
                        function updateSliderPresets() {
                            var bt = jQuery('.rs-slidertype.selected'),
                                sw = jQuery('#preselect-horiz-wrapper'),
                                swp = jQuery('#preset-selector-wrapper'),
                                mode = bt.data('mode'),
                                prewi = (swp.width() - 2) / 4;
                            if (prewi < 200) prewi = ((swp.width() - 1) / 3);

                            preitems = jQuery('.rs-preset-selector.' + mode);

                            jQuery('.rs-preset-selector').removeClass("selected").hide().css({width: prewi + "px"});
                            preitems.show();


                            //if (preitems.length<7) {
                            sw.css({position: "relative", height: "auto", width: "100%"});
                            swp.css({position: "relative", height: "auto"});


                            //} else {

                            //	sw.css({position:"absolute",height:"400px",width:(prewi*Math.ceil(preitems.length/2))+"px"});
                            //	swp.css({position:"relative",height:"400px"});
                            //}
                            jQuery('.preset-selector-wrapper').perfectScrollbar('update');

                            switch (mode) {
                                case "standardpreset":
                                    jQuery('.dontshowonhero').show();
                                    jQuery('.dontshowonstandard').hide();
                                    break;
                                case "carouselpreset":
                                    jQuery('.dontshowonhero').show();
                                    jQuery('.dontshowonstandard').show();
                                    break;
                                case "heropreset":
                                    jQuery('.dontshowonhero').hide();
                                    break;
                            }

                        }
                        function get_preset_params() {
                            var params = RevSliderSettings.getSettingsObject('form_slider_params');
                            delete params.action;
                            delete params['0'];

                            var ecsn = (jQuery('input[name="enable_custom_size_notebook"]').is(':checked')) ? 'on' : 'off';
                            var ecst = (jQuery('input[name="enable_custom_size_tablet"]').is(':checked')) ? 'on' : 'off';
                            var ecsi = (jQuery('input[name="enable_custom_size_iphone"]').is(':checked')) ? 'on' : 'off';
                            var mof = (jQuery('input[name="main_overflow_hidden"]').is(':checked')) ? 'on' : 'off';
                            var ah = (jQuery('input[name="auto_height"]').is(':checked')) ? 'on' : 'off';

                            var params2 = {
                                slider_type: jQuery('input[name="slider_type"]:checked').val(),
                                width: jQuery('input[name="width"]').val(),
                                width_notebook: jQuery('input[name="width_notebook"]').val(),
                                width_tablet: jQuery('input[name="width_tablet"]').val(),
                                width_mobile: jQuery('input[name="width_mobile"]').val(),
                                height: jQuery('input[name="height"]').val(),
                                height_notebook: jQuery('input[name="height_notebook"]').val(),
                                height_tablet: jQuery('input[name="height_tablet"]').val(),
                                height_mobile: jQuery('input[name="height_mobile"]').val(),
                                enable_custom_size_notebook: ecsn,
                                enable_custom_size_tablet: ecst,
                                enable_custom_size_iphone: ecsi,

                                main_overflow_hidden: mof,
                                auto_height: ah,
                                min_height: jQuery('input[name="min_height"]').val(),
                            };

                            if (typeof rev_cm_custom_js !== 'undefined')
                                params2.custom_javascript = rev_cm_custom_js.getValue();

                            if (typeof rev_cm_custom_css !== 'undefined')
                                params2.custom_css = rev_cm_custom_css.getValue();

                            jQuery.extend(params, params2);


                            return params;

                        }

                        document.addEventListener("DOMContentLoaded", function () {
                            jQuery('.preset-splitter').click(function () {
                                jQuery('#preset-selector-wrapper').slideDown(200);
                                jQuery(this).removeClass('readytoopen');
                            });
                            preset_template_container = wp.template("rs-preset-container");
                            rs_reset_preset_html();
                            jQuery('body').on("click", '.rs-slidertype', function () {
                                var bt = jQuery(this);
                                jQuery('.rs-slidertype').removeClass("selected");
                                bt.addClass("selected").find('input[name="slider-type"]').attr('checked', 'checked');

                                if (jQuery('input[name="slider-type"]:checked').val() !== "hero" || jQuery('#ddd_parallax').attr('checked') === "checked")
                                    jQuery('#fadeinoutparallax').hide();
                                else
                                    jQuery('#fadeinoutparallax').show();

                                updateSliderPresets();
                            });
                            jQuery('.preset-selector-wrapper').perfectScrollbar({});
                            updateSliderPresets();
                            jQuery(window).resize(updateSliderPresets);
                            jQuery('body').on('mouseover', '.rs-preset-selector', function () {
                                jQuery(this).find('.rev-remove-preset').show();
                                jQuery(this).find('.rev-update-preset').show();
                            });
                            jQuery('body').on('mouseleave', '.rs-preset-selector', function () {
                                jQuery(this).find('.rev-remove-preset').hide();
                                jQuery(this).find('.rev-update-preset').hide();
                            });
                            googlef_template_container = wp.template("rs-preset-googlefont");
                            jQuery('body').on('click', '.rs-preset-selector', function () {
                                if (typeof(jQuery(this).attr('id')) == 'undefined' || jQuery(this).hasClass('rs-do-nothing')) return false;
                                var preset_id = jQuery(this).attr('id').replace('rs-preset-', '');

                                showWaitAMinute({fadeIn: 300, text: rev_lang.preset_loaded});

                                if (typeof(revslider_presets[preset_id]) !== 'undefined') {

                                    for (var key in revslider_presets[preset_id]['values']) {
                                        var entry = jQuery('[name="' + key + '"]');

                                        if (key == 'google_font') {
                                            jQuery('#rs-google-fonts').html('');

                                            for (var gfk in revslider_presets[preset_id]['values'][key]) {
                                                jQuery('#rs-google-fonts').append(googlef_template_container({'value': revslider_presets[preset_id]['values'][key][gfk]}));
                                            }

                                        }

                                        if (entry.length == 0) continue;

                                        switch (entry.prop('tagName').toLowerCase()) {
                                            case 'input':
                                                switch (entry.attr('type')) {
                                                    case 'radio':
                                                        jQuery('[name="' + key + '"][value="' + revslider_presets[preset_id]['values'][key] + '"]').click();
                                                        break;
                                                    case 'checkbox':
                                                        if (revslider_presets[preset_id]['values'][key] == 'on')
                                                            entry.attr('checked', true);
                                                        else
                                                            entry.attr('checked', false);

                                                        RevSliderSettings.onoffStatus(entry);
                                                        break;
                                                    default:
                                                        entry.val(revslider_presets[preset_id]['values'][key]);
                                                        break;
                                                }
                                                break;
                                            case 'select':
                                                jQuery('[name="' + key + '"] option[value="' + revslider_presets[preset_id]['values'][key] + '"]').attr('selected', true);
                                                break;
                                            default:
                                                switch (key) {
                                                    case 'custom_css':
                                                        if (typeof rev_cm_custom_css !== 'undefined')
                                                            rev_cm_custom_css.setValue(UniteAdminRev.stripslashes(revslider_presets[preset_id]['values'][key]));
                                                        break;
                                                    case 'custom_javascript':
                                                        if (typeof rev_cm_custom_js !== 'undefined')
                                                            rev_cm_custom_js.setValue(UniteAdminRev.stripslashes(revslider_presets[preset_id]['values'][key]));
                                                        break;
                                                    default:
                                                        jQuery('[name="' + key + '"]').val(revslider_presets[preset_id]['values'][key]);
                                                        break;
                                                }
                                                break;
                                        }

                                        entry.change(); //trigger change call for elements to hide/show dependencies
                                    }

                                    if (typeof rev_cm_custom_css !== 'undefined') rev_cm_custom_css.refresh();
                                    if (typeof rev_cm_custom_js !== 'undefined') rev_cm_custom_js.refresh();
                                }

                                setTimeout('showWaitAMinute({fadeOut:300})', 400);
                            });
                            jQuery('body').on('click', '.rev-update-preset', function () {
                                if (confirm(rev_lang.update_preset)) {

                                    var pr_id = jQuery(this).closest('.rs-preset-entry').attr('id').replace('rs-preset-', '');

                                    if (typeof(revslider_presets[pr_id]) == 'undefined') alert(rev_lang.preset_not_found);

                                    var params = get_preset_params();

                                    var update_preset = {
                                        name: revslider_presets[pr_id]['settings']['name'],
                                        values: params
                                    };

                                    UniteAdminRev.ajaxRequest('update_preset', update_preset, function (response) {
                                        if (response.success == true) {
                                            //refresh presets
                                            revslider_presets = response.data;

                                            rs_reset_preset_html();
                                        }
                                    });
                                }

                                return false;
                            });
                            jQuery('#rs-add-new-settings-preset').click(function () {
                                jQuery('input[name="rs-preset-name"]').val('');
                                jQuery('input[name="rs-preset-image-id"]').val('');
                                jQuery('#rs-preset-img-wrapper').css('background-image', '');

                                jQuery('#dialog-rs-add-new-setting-presets').dialog({
                                    modal: true,
                                    resizable: false,
                                    minWidth: 400,
                                    minHeight: 300,
                                    closeOnEscape: true,
                                    buttons: {
                                        '<?php echo t('Save Settings') ?>': function () {
                                            var preset_name = UniteAdminRev.sanitize_input(jQuery('input[name="rs-preset-name"]').val());
                                            var preset_img = jQuery('input[name="rs-preset-image-id"]').val();
                                            jQuery('input[name="rs-preset-name"]').val(preset_name);

                                            if (preset_name == '') return false;

                                            for (var key in revslider_presets) {
                                                if (revslider_presets[key]['settings']['name'] == preset_name) {
                                                    alert(rev_lang.preset_name_already_exists);
                                                    return false;
                                                }
                                            }
                                            var c_type = jQuery('.rs-slidertype.selected').data('mode');

                                            var params = get_preset_params();

                                            var new_preset = {
                                                settings: {
                                                    'class': '',
                                                    image: preset_img,
                                                    name: preset_name,
                                                    preset: c_type
                                                },
                                                values: params
                                            };


                                            //add new preset to the list
                                            UniteAdminRev.ajaxRequest('add_new_preset', new_preset, function (response) {
                                                if (response.success == true) {
                                                    //refresh presets
                                                    revslider_presets = response.data;

                                                    rs_reset_preset_html();
                                                }

                                                jQuery('#dialog-rs-add-new-setting-presets').dialog('close');
                                            });

                                        }
                                    }
                                });


                            });
                            jQuery('input[name="rs-button-select-img"]').click(function () {
                                jQuery('#rs-preset-img-wrapper').css('background-image', '');
                                jQuery('input[name="rs-preset-image-id"]').val('');

                                UniteAdminRev.openAddImageDialog(rev_lang.select_image, function (urlImage, imageID, width, height) {
                                    var data = {
                                        url_image: urlImage,
                                        image_id: imageID,
                                        img_width: width,
                                        img_height: height
                                    };

                                    jQuery('input[name="rs-preset-image-id"]').val(data.image_id);

                                    jQuery('#rs-preset-img-wrapper').css('background-image', ' url(' + data.url_image + ')');

                                    var mw = 200;
                                    var mh = height / width * 200;

                                    jQuery('#rs-preset-img-wrapper').css('width', mw + 'px');
                                    jQuery('#rs-preset-img-wrapper').css('height', mh + 'px');
                                    jQuery('#rs-preset-img-wrapper').css('background-size', 'cover');
                                });

                            });
                            jQuery('body').on('click', '.rev-remove-preset', function () {
                                if (confirm(rev_lang.delete_preset)) {

                                    var pr_id = jQuery(this).closest('.rs-preset-entry').attr('id').replace('rs-preset-', '');

                                    if (typeof(revslider_presets[pr_id]) == 'undefined') alert(rev_lang.preset_not_found);

                                    UniteAdminRev.ajaxRequest('remove_preset', {name: revslider_presets[pr_id]['settings']['name']}, function (response) {
                                        revslider_presets = response.data;

                                        rs_reset_preset_html();
                                    });
                                }

                                return false;
                            });
                        });
                    </script>
                </div>

                <!-- SLIDE LAYOUT -->

                <?php
                $width_notebook = RevSliderFunctions::getVal($arrFieldsParams, "width_notebook", $_width_notebook);
                $height_notebook = RevSliderFunctions::getVal($arrFieldsParams, "height_notebook");
                if (intval($height_notebook) == 0) {
                    $height_notebook = 768;
                }

                $width = RevSliderFunctions::getVal($arrFieldsParams, "width", $_width);
                $height = RevSliderFunctions::getVal($arrFieldsParams, "height", 868);

                $width_tablet = RevSliderFunctions::getVal($arrFieldsParams, "width_tablet", $_width_tablet);
                if (intval($width_tablet) == 0) {
                    $width_tablet = $_width_tablet;
                }

                $height_tablet = RevSliderFunctions::getVal($arrFieldsParams, "height_tablet");
                if (intval($height_tablet) == 0) {
                    $height_tablet = 960;
                }
                $width_mobile = RevSliderFunctions::getVal($arrFieldsParams, "width_mobile", $_width_mobile);

                if (intval($width_mobile) == 0) {
                    $width_mobile = $_width_mobile;
                }

                $height_mobile = RevSliderFunctions::getVal($arrFieldsParams, "height_mobile");
                if (intval($height_mobile) == 0) {
                    $height_mobile = 720;
                }

                $advanced_sizes = RevSliderFunctions::getVal($arrFieldsParams, "advanced-responsive-sizes", 'false');
                $advanced_sizes = RevSliderFunctions::strToBool($advanced_sizes);

                $sliderType = RevSliderFunctions::getVal($arrFieldsParams, "slider_type");

                ?>

                <div class="setting_box" id="rs-slider-layout-cont">
                    <h3>
                        <span class="setting-step-number"><?php echo $index_content++ ?></span><span><?php echo t("Slide Layout") ?></span>
                    </h3>
                    <div class="inside" style="padding:0px">

                        <?php $slider_type = RevSliderFunctions::getVal($arrFieldsParams, 'slider_type', 'fullwidth'); ?>
                        <div id="rs_slider_layout_size_wrapper" style="background:#eee">
                            <div class="rs-slidesize-selector">

                                <div class="rs-slidersize">
                                    <span class="rs-size-image autosized"></span>
                                    <span class="rs-preset-label"><?php echo t('Auto') ?></span>
                                    <input type="radio" id="slider_type_1" value="auto"
                                           name="slider_type" <?php RevSliderFunctions::checked($slider_type, 'auto'); ?> />
                                </div>
                                <div class="rs-slidersize">
                                    <span class="rs-size-image fullwidthsized"></span>
                                    <span class="rs-preset-label"><?php echo t('Full-Width') ?></span>
                                    <input type="radio" id="slider_type_2" value="fullwidth"
                                           name="slider_type" <?php RevSliderFunctions::checked($slider_type, 'fullwidth'); ?> />
                                </div>
                                <div class="rs-slidersize selected">
                                    <span class="rs-size-image fullscreensized"></span>
                                    <span class="rs-preset-label"><?php echo t('Full-Screen') ?></span>
                                    <input type="radio" id="slider_type_3" style="margin-left:20px"
                                           value="fullscreen"
                                           name="slider_type" <?php RevSliderFunctions::checked($slider_type, 'fullscreen'); ?> />
                                </div>
                                <div style="clear:both;float:none"></div>
                            </div>
                        </div>

                        <div id="layout-preshow">
                            <div class="rsp-desktop-view rsp-view-cell">
                                <div class="rsp-view-header">
                                    <?php echo t('Desktop Large') ?> <span
                                            class="rsp-cell-dimension"><?php echo t('Max') ?></span>
                                </div>
                                <div class="rsp-present-area">
                                    <div class="rsp-device-imac">
                                        <div class="rsp-imac-topbar"></div>
                                        <div class="rsp-bg"></div>
                                        <div class="rsp-browser">
                                            <div class="rsp-slide-bg" data-width="140" data-height="70"
                                                 data-fullwidth="188" data-faheight="70" data-fixwidth="140"
                                                 data-fixheight="70" data-gmaxw="140" data-lscale="1">
                                                <div class="rsp-grid">
                                                    <div class="rsp-dotted-line-hr-left"></div>
                                                    <div class="rsp-dotted-line-hr-right"></div>
                                                    <div class="rsp-dotted-line-vr-top"></div>
                                                    <div class="rsp-dotted-line-vr-bottom"></div>
                                                    <div class="rsp-layer"><?php echo t('Layer') ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="rsp-device-imac-bg"></div>
                                </div>
                                <div class="slide-size-wrapper">
                                    <div id="desktop-dimension-fields">
                                        <span class="rs-preset-label"><?php echo t('Layer Grid Size') ?></span>
                                        <span class="relpos"><input id="width" name="width" type="text"
                                                                    style="width:120px" class="textbox-small"
                                                                    value="<?php echo $width; ?>"><span
                                                    class="pxfill">px</span></span>
                                        <span class="rs-preset-label label-multiple">x</span>
                                        <span class="relpos"><input id="height" name="height" type="text"
                                                                    style="width:120px" class="textbox-small"
                                                                    value="<?php echo $height; ?>"><span
                                                    class="pxfill">px</span></span>
                                        <span class="tp-clearfix" style="margin-bottom:15px"></span>
                                    </div>
                                    <span class="description"><?php echo t('Specify a layer grid size above') ?></span>
                                    <span class="description"
                                          style="padding:20px 20px 0px; box-sizing:border-box;-moz-box-sizing:border-box;"><?php echo t('Slider is always Linear Responsive till next Defined Grid size has been hit.') ?></span>
                                </div>
                            </div>
                            <div class="rsp-macbook-view rsp-view-cell">
                                <div class="rsp-view-header">
                                    <?php echo t('Notebook') ?> <span
                                            class="rsp-cell-dimension"><?php echo $_width_notebook; ?>px</span>
                                </div>
                                <div class="rsp-present-area">
                                    <div class="rsp-device-macbook">
                                        <div class="rsp-macbook-topbar"></div>
                                        <div class="rsp-bg"></div>
                                        <div class="rsp-browser">
                                            <div class="rsp-slide-bg" data-width="140" data-height="60"
                                                 data-fullwidth="160" data-faheight="60" data-fixwidth="140"
                                                 data-fixheight="60" data-gmaxw="140" data-lscale="0.8">
                                                <div class="rsp-grid">
                                                    <div class="rsp-dotted-line-hr-left"></div>
                                                    <div class="rsp-dotted-line-hr-right"></div>
                                                    <div class="rsp-dotted-line-vr-top"></div>
                                                    <div class="rsp-dotted-line-vr-bottom"></div>
                                                    <div class="rsp-layer"><?php echo t('Layer') ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="rsp-device-macbook-bg"></div>
                                </div>
                                <div class="slide-size-wrapper">
                                    <span class="rs-preset-label"><?php echo t('Layer Grid Size') ?></span>
                                    <span class="rs-width-height-wrapper">
										<span class="relpos"><input name="width_notebook" type="text"
                                                                    style="width:120px" class="textbox-small"
                                                                    value="<?php echo $width_notebook; ?>"><span
                                                    class="pxfill">px</span></span>
										<span class="rs-preset-label label-multiple">x</span>
										<span class="relpos"><input name="height_notebook" type="text"
                                                                    style="width:120px" class="textbox-small"
                                                                    value="<?php echo $height_notebook; ?>"><span
                                                    class="pxfill">px</span></span>
									</span>
                                    <span class="rs-width-height-alternative" style="display:none">
										<span class="rs-preset-label"><?php echo t('Auto Sizes') ?></span>
									</span>
                                    <span class="tp-clearfix" style="margin-bottom:15px"></span>
                                    <span class="rs-preset-label"
                                          style="display:inline-block; margin-right:15px;"><?php echo t('Custom Grid Size') ?></span>
                                    <span style="text-align:left">
										<input type="checkbox" class="tp-moderncheckbox"
                                               id="enable_custom_size_notebook" name="enable_custom_size_notebook"
                                               data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, 'enable_custom_size_notebook', 'off'), "on"); ?>>
									</span>
                                    <span class="description"
                                          style="padding:0px 20px 0px; box-sizing:border-box;-moz-box-sizing:border-box;"><?php echo t('<br>If not defined, the next bigger Layer Grid Size is the basic of Linear Responsive calculations.') ?></span>
                                </div>
                            </div>
                            <div class="rsp-tablet-view rsp-view-cell">
                                <div class="rsp-view-header">
                                    <?php echo t('Tablet') ?> <span
                                            class="rsp-cell-dimension"><?php echo $_width_tablet; ?>px</span>
                                </div>
                                <div class="rsp-present-area">
                                    <div class="rsp-device-ipad">
                                        <div class="rsp-bg"></div>
                                        <div class="rsp-browser">
                                            <div class="rsp-slide-bg" data-width="126" data-height="60"
                                                 data-fullwidth="138" data-faheight="130" data-fixwidth="140"
                                                 data-fixheight="70" data-gmaxw="126" data-lscale="0.7">
                                                <div class="rsp-grid">
                                                    <div class="rsp-dotted-line-hr-left"></div>
                                                    <div class="rsp-dotted-line-hr-right"></div>
                                                    <div class="rsp-dotted-line-vr-top"></div>
                                                    <div class="rsp-dotted-line-vr-bottom"></div>
                                                    <div class="rsp-layer"><?php echo t('Layer') ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="rsp-device-ipad-bg"></div>
                                </div>
                                <div class="slide-size-wrapper">
                                    <span class="rs-preset-label"><?php echo t('Layer Grid Size') ?></span>
                                    <span class="rs-width-height-wrapper">
										<span class="relpos"><input name="width_tablet" type="text" style="width:120px"
                                                                    class="textbox-small"
                                                                    value="<?php echo $width_tablet; ?>"><span
                                                    class="pxfill">px</span></span>
										<span class="rs-preset-label label-multiple">x</span>
										<span class="relpos"><input name="height_tablet" type="text" style="width:120px"
                                                                    class="textbox-small"
                                                                    value="<?php echo $height_tablet; ?>"><span
                                                    class="pxfill">px</span></span>
									</span>
                                    <span class="rs-width-height-alternative" style="display:none">
										<span class="rs-preset-label"><?php echo t('Auto Sizes') ?></span>
									</span>
                                    <span class="tp-clearfix" style="margin-bottom:15px"></span>
                                    <span class="rs-preset-label"
                                          style="display:inline-block; margin-right:15px;"><?php echo t('Custom Grid Size') ?></span>
                                    <span style="text-align:left">
										<input type="checkbox" class="tp-moderncheckbox" id="enable_custom_size_tablet"
                                               name="enable_custom_size_tablet"
                                               data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, 'enable_custom_size_tablet', 'off'), "on"); ?>>
									</span>
                                    <span class="description"
                                          style="padding:0px 20px 0px; box-sizing:border-box;-moz-box-sizing:border-box;"><?php echo t('<br>If not defined, the next bigger Layer Grid Size is the basic of Linear Responsive calculations.') ?></span>
                                </div>

                            </div>
                            <div class="rsp-mobile-view rsp-view-cell">
                                <div class="rsp-view-header">
                                    <?php echo t('Mobile') ?> <span
                                            class="rsp-cell-dimension"><?php echo $_width_mobile; ?>px</span>
                                </div>
                                <div class="rsp-present-area">
                                    <div class="rsp-device-iphone">
                                        <div class="rsp-bg"></div>
                                        <div class="rsp-browser">
                                            <div class="rsp-slide-bg" data-width="70" data-height="40"
                                                 data-fullwidth="80" data-faheight="100" data-fixwidth="140"
                                                 data-fixheight="70" data-gmaxw="70" data-lscale="0.4">
                                                <div class="rsp-grid">
                                                    <div class="rsp-dotted-line-hr-left"></div>
                                                    <div class="rsp-dotted-line-hr-right"></div>
                                                    <div class="rsp-dotted-line-vr-top"></div>
                                                    <div class="rsp-dotted-line-vr-bottom"></div>
                                                    <div class="rsp-layer"><?php echo t('Layer') ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="rsp-device-iphone-bg"></div>
                                </div>
                                <div class="slide-size-wrapper">
                                    <span class="rs-preset-label"><?php echo t('Layer Grid Size') ?></span>
                                    <span class="rs-width-height-wrapper">
										<span class="relpos"><input name="width_mobile" type="text" style="width:120px"
                                                                    class="textbox-small"
                                                                    value="<?php echo $width_mobile; ?>"><span
                                                    class="pxfill">px</span></span>
										<span class="rs-preset-label label-multiple">x</span>
										<span class="relpos"><input name="height_mobile" type="text" style="width:120px"
                                                                    class="textbox-small"
                                                                    value="<?php echo $height_mobile; ?>"><span
                                                    class="pxfill">px</span></span>
									</span>
                                    <span class="rs-width-height-alternative" style="display:none">
										<span class="rs-preset-label"><?php echo t('Auto Sizes') ?></span>
									</span>
                                    <span class="tp-clearfix" style="margin-bottom:15px"></span>
                                    <span class="rs-preset-label"
                                          style="display:inline-block; margin-right:15px;"><?php echo t('Custom Grid Size') ?></span>
                                    <span style="text-align:left">
										<input type="checkbox" class="tp-moderncheckbox" id="enable_custom_size_iphone"
                                               name="enable_custom_size_iphone"
                                               data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, 'enable_custom_size_iphone', 'off'), "on"); ?>>
									</span>
                                    <span class="description"
                                          style="padding:0px 20px 0px; box-sizing:border-box;-moz-box-sizing:border-box;"><?php echo t('<br>If not defined, the next bigger Layer Grid Size is the basic of Linear Responsive calculations.') ?></span>
                                </div>
                            </div>
                            <div style="clear:both;float:none"></div>
                        </div>
                        <div class="buttonarea" id="removethisbuttonarea">
                            <a class="button-primary revblue" id="show_advanced_navigation" original-title=""><i
                                        class="revicon-cog"></i><?php echo t("Show Advanced Size Options") ?>
                            </a>
                        </div>

                        <!-- VISUAL ADVANCED SIZING -->
                        <div class="inside" id="visual-sizing" style="display:none; padding:25px 20px;">
                            <div id="fullscreen-advanced-sizing">
								<span class="one-half-container" style="vertical-align:top">

									<span class="rs-preset-label noopacity "><?php echo t("Minimal Height of Slider (Optional)") ?></span>
									<span style="clear:both;float:none; height:25px;display:block"></span>

									<span style="text-align:left; display:none;">
										<span class="rs-preset-label noopacity "
                                              style="display:inline-block;margin-right:20px"><?php echo t("FullScreen Align Force") ?> </span>
										<input type="checkbox" class="tp-moderncheckbox withlabel"
                                               id="full_screen_align_force" name="full_screen_align_force"
                                               data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, 'full_screen_align_force', 'off'), "on"); ?>>
										<span class="description"><?php echo t("Layers align within the full slider instead of the layer grid.") ?></span>
									</span>

									<span class="slidertitlebox limitedtablebox">
										<span class="one-half-container">
											<input placeholder="<?php echo t("Min. Height") ?>" type="text"
                                                   class="text-sidebar" id="fullscreen_min_height"
                                                   name="fullscreen_min_height"
                                                   value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "fullscreen_min_height", ""); ?>">
											<i class="input-edit-icon"></i>
											<span class="description"><?php echo t("The minimum height of the Slider in FullScreen mode.") ?></span>
										</span>
									</span>
									<span style="clear:both;float:none; height:25px;display:block"></span>

									<span style="text-align:left; padding:0px 20px;">
										<span class="rs-preset-label noopacity"
                                              style="display:inline-block;margin-right:20px"><?php echo t("Disable Force FullWidth") ?> </span>
										<input type="checkbox" class="tp-moderncheckbox " id="autowidth_force"
                                               name="autowidth_force"
                                               data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, 'autowidth_force', 'off'), "on"); ?>>
										<span class="description"
                                              style="padding:0px 20px;"><?php echo t("Disable the FullWidth Force function, and allow to float the Fullheight slider horizontal.") ?></span>
									</span>

								</span>

                                <span class="one-half-container" style="vertical-align:top">
									<span class="rs-preset-label noopacity "><?php echo t("Increase/Decrease Fullscreen Height (Optional)") ?></span>
									<span style="clear:both;float:none; height:25px;display:block"></span>

									<span class="slidertitlebox limitedtablebox">
										<span class="one-full-container">
											<input placeholder="<?php echo t("Containers") ?>" type="text"
                                                   class="text-sidebar" id="fullscreen_offset_container"
                                                   name="fullscreen_offset_container"
                                                   value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "fullscreen_offset_container", ""); ?>">
											<i class="input-edit-icon"></i>
											<span class="description"><?php echo t("Example: #header or .header, .footer, #somecontainer | Height of Slider will be decreased with the height of these Containers to fit perfect in the screen.") ?></span>
										</span>
									</span>
									<span style="clear:both;float:none; height:25px;display:block"></span>
									<span class="slidertitlebox limitedtablebox">
										<span class="one-full-container">
											<input placeholder="<?php echo t("PX or %") ?>" type="text"
                                                   class="text-sidebar" id="fullscreen_offset_size"
                                                   name="fullscreen_offset_size"
                                                   value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "fullscreen_offset_size", ""); ?>">
											<i class="input-edit-icon"></i>
											<span class="description"><?php echo t("Decrease/Increase height of Slider. Can be used with px and %. Positive/Negative values allowed. Example: 40px or 10%") ?></span>
										</span>
									</span>
									<span style="clear:both;float:none; height:25px;display:block"></span>

								</span>
                            </div>
                            <div id="normal-advanced-sizing">

                                <div class="slidertitlebox limitedtablebox" style="width:100%; max-width:100%">
									<span class="one-third-container" style="vertical-align:top">
										<span class="rs-preset-label noopacity"
                                              style="margin-top:12px;display:inline-block;margin-right:20px"><?php echo t("Overflow Hidden") ?> </span>
										<input type="checkbox" class="tp-moderncheckbox" id="main_overflow_hidden"
                                               name="main_overflow_hidden"
                                               data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, 'main_overflow_hidden', 'off'), "on"); ?>>
										<div style="clear:both;float:none; height:25px"></div>
										<span class="description"><?php echo t("Adds overflow:hidden to the slider wrapping container which will hide / cut any overlapping elements. Mostly used in Carousel Sliders.") ?></span>
									</span>

                                    <span class="one-third-container" style="vertical-align:top">
										<span class="rs-preset-label noopacity"
                                              style="margin-top:12px;display:inline-block;margin-right:20px"><?php echo t("Respect Aspect Ratio") ?> </span>
										<input type="checkbox" class="tp-moderncheckbox" id="auto_height"
                                               name="auto_height"
                                               data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, 'auto_height', 'off'), "on"); ?>>
										<div style="clear:both;float:none; height:25px"></div>
										<span class="description"><?php echo t("It will keep aspect ratio and ignore max height of Layer Grid by upscaling. Layer Area will be vertical centered.") ?></span>
									</span>

                                    <span class="one-third-container" style="vertical-align:top">
										<input placeholder="<?php echo t("Min. Height (Optional)") ?>"
                                               type="text" class="text-sidebar"
                                               style="padding:11px 45px 11px 15px; line-height:26px" id="min_height"
                                               name="min_height"
                                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "min_height", ""); ?>">
										<i class="input-edit-icon"></i>
										<span class="description"><?php echo t("The minimum height of the Slider in FullWidth or Auto mode.") ?></span>
										<span class="rs-show-on-auto" style="display:inline-block; position:relative;">
											<input placeholder="<?php echo t("Max. Width (Optional)") ?>"
                                                   type="text" class="text-sidebar"
                                                   style="padding:11px 45px 11px 15px; margin-top: 20px; line-height:26px"
                                                   id="max_width" name="max_width"
                                                   value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, "max_width", ""); ?>">
											<i class="input-edit-icon" style="top: 23px; right:0px"></i>
											<span class="description"><?php echo t("The maximum width of the Slider in Auto mode.") ?></span>
										</span>
									</span>
                                </div>
                            </div>

                        </div>        <!-- / VISUAL ADVANCED SIZING -->

                        <script>

                            document.addEventListener("DOMContentLoaded", function() {

                                jQuery('#show_advanced_navigation').click(function() {
                                    var a = jQuery('#visual-sizing');
                                    a.slideDown(200);
                                    jQuery('#removethisbuttonarea').remove();
                                });

                                jQuery('#show_advanced_navigation').click();

                                jQuery('input[name="slider_type"]').on("change",function() {
                                    var s_fs = jQuery('#slider_type_3').is(":checked");
                                    if (s_fs) {
                                        jQuery('#normal-advanced-sizing').hide();
                                        jQuery('#fullscreen-advanced-sizing').show();
                                    } else {
                                        jQuery('#normal-advanced-sizing').show();
                                        jQuery('#fullscreen-advanced-sizing').hide();
                                    }

                                    var s_fs = jQuery('#slider_type_1').is(":checked");
                                    if (s_fs) {
                                        jQuery('.rs-show-on-auto').show();
                                    }else{
                                        jQuery('.rs-show-on-auto').hide();
                                    }
                                });

                                jQuery('#slider_type_3').change();

                                function get_preview_resp_sizes() {
                                    var s = {};

                                    s.n  = jQuery('#enable_custom_size_notebook').attr('checked')==="checked";
                                    s.t  = jQuery('#enable_custom_size_tablet').attr('checked')==="checked";
                                    s.m  = jQuery('#enable_custom_size_iphone').attr('checked')==="checked";



                                    s.w_d = jQuery('input[name="width"]');
                                    s.w_n = s.n ? jQuery('input[name="width_notebook"]') : s.w_d;
                                    s.w_t = s.t ? jQuery('input[name="width_tablet"]') : s.n ? s.w_n : s.w_d;
                                    s.w_m = s.m ? jQuery('input[name="width_mobile"]') : s.t ? s.w_t : s.n ? s.w_n : s.w_d;

                                    s.h_d = jQuery('input[name="height"]');
                                    s.h_n = s.n ? jQuery('input[name="height_notebook"]') : s.h_d;
                                    s.h_t = s.t ? jQuery('input[name="height_tablet"]') : s.n ? s.h_n : s.h_d;
                                    s.h_m = s.m ? jQuery('input[name="height_mobile"]') : s.t ? s.h_t : s.n ? s.h_n : s.h_d;
                                    return s;
                                }

                                function draw_responsive_previews(s) {
                                    var  d_gw = (s.w_d.val() / 1400) * 150;
                                    //jQuery('.rsp-desktop-view .rsp-grid').css({width})

                                }

                                function setLayoutDesign(element,bw,bh,sw,sh,bm,ww,mm,fh,tt) {
                                    var o = {};
                                    o.mtp = 1;

                                    if (bw>sw) {
                                        bh = bh *  sw/bw;
                                        o.mtp = sw/bw;
                                        bw = sw;
                                    }
                                    if (bh>sh) {
                                        bw = bw *  sh/bh;
                                        o.mtp = sh/bh;
                                        bh = sh;
                                    }

                                    o.tt = tt;

                                    o.left = o.right = (2 + ((1 - ( bw / sw))*(sw/10))/2) * bm,
                                        o.height =   (bh/10)*bm;
                                    o.mt = (((sh/10) - o.height)/20)*bm;

                                    if (fh===1)
                                        o.gridtop = (((sh*bm/10) - o.height)/2);
                                    else {
                                        o.gridtop = 0;

                                        if (jQuery('#auto_height').attr("checked")==="checked")
                                            o.gridtop = Math.abs((o.height - (o.height * (sw/bw))))/2;
                                    }

                                    punchgs.TweenLite.to(element.find('.rsp-grid'),0.3,{top:o.gridtop,height:o.height,left:mm,ease:punchgs.Power3.easeInOut});
                                    punchgs.TweenLite.to(element.find('.rsp-dotted-line-hr-left'),0.3,{left:o.left,ease:punchgs.Power3.easeInOut});
                                    punchgs.TweenLite.to(element.find('.rsp-dotted-line-hr-right'),0.3,{right:o.right,ease:punchgs.Power3.easeInOut});
                                    if (fh===1) {
                                        o.height = "100%";
                                        o.mt = 0;
                                        o.tt = 0;
                                    }
                                    else

                                    if (jQuery('#auto_height').attr("checked")==="checked")
                                        o.height = o.height * (sw/bw);


                                    punchgs.TweenLite.to(element.find('.rsp-slide-bg'),0.3,{top:o.tt,width:ww,left:0-mm,marginTop:o.mt, height:o.height,ease:punchgs.Power3.easeInOut});
                                    punchgs.TweenLite.to(element.find('.rsp-layer'),0.3,{fontSize:o.mtp*14, paddingTop:o.mtp*3, paddingBottom:o.mtp*3, paddingLeft:o.mtp*5,paddingRight:o.mtp*5, lineHeight:o.mtp*14+"px" ,ease:punchgs.Power3.easeInOut });

                                }
                                function readLayoutValues(goon) {
                                    var s = get_preview_resp_sizes(),
                                        o = {};
                                    if (goon===1)
                                        jQuery('.slide-size-wrapper .tp-moderncheckbox').change();
                                    if (jQuery('#slider_type_2').is(":checked")|| jQuery('#slider_type_3').is(":checked")) {
                                        o.dw = 187; o.dm = 23;
                                        o.nw = 160; o.nm = 10;
                                        o.tw = 140; o.tm = 7;
                                        o.mw = 80; o.mm = 5;
                                    } else {
                                        o.dw = 140; o.dm = 0;
                                        o.nw = 140; o.nm = 0;
                                        o.tw = 126; o.tm = 0;
                                        o.mw = 71; o.mm = 0;
                                    }

                                    if (jQuery('#slider_type_3').is(":checked")) {
                                        o.dh = 1;
                                        o.nh = 1;
                                        o.th = 1;
                                        o.mh = 1;
                                    } else {
                                        o.dh = 0;
                                        o.nh = 0;
                                        o.th = 0;
                                        o.mh = 0;
                                    }

                                    setLayoutDesign(jQuery('.rsp-device-imac'), s.w_d.val(), s.h_d.val(), 1400,900,1,o.dw,o.dm,o.dh,0);
                                    setLayoutDesign(jQuery('.rsp-device-macbook'), s.w_n.val(), s.h_n.val(), 1200,770,1.166,o.nw,o.nm,o.nh,0);
                                    setLayoutDesign(jQuery('.rsp-device-ipad'), s.w_t.val(), s.h_t.val(), 768,1024,1.78,o.tw,o.tm,o.th,6);
                                    setLayoutDesign(jQuery('.rsp-device-iphone'), s.w_m.val(), s.h_m.val(), 640,1136,1.25,o.mw,o.mm,o.mh,0);
                                }

                                jQuery('.slide-size-wrapper .tp-moderncheckbox').on("change",function() {
                                    var bt = jQuery(this),
                                        bp = bt.closest('.slide-size-wrapper'),
                                        cw = bp.find('.rs-width-height-alternative'),
                                        aw = bp.find('.rs-width-height-wrapper'),
                                        s = get_preview_resp_sizes();


                                    if (bt.attr('checked')==="checked") {
                                        if (bt.data('oldstatus')==="unchecked" || bt.data('oldstatus') ===undefined) {
                                            bp.removeClass("disabled").find('input[type="text"]').removeAttr("disabled");
                                            aw.show();
                                            cw.hide();
                                            bp.find('input[type="text"]').each(function() {
                                                var inp = jQuery(this);
                                                if (inp.data('oldval')!==undefined) inp.val(inp.data('oldval'));
                                            });

                                        }
                                        bt.data('oldstatus',"checked");
                                    } else {
                                        if (bt.data('oldstatus')==="checked" || bt.data('oldstatus') ===undefined) {
                                            bp.addClass("disabled").find('input[type="text"]').attr("disabled","disabled");
                                            aw.hide();
                                            cw.show();
                                            bp.find('input[type="text"]').each(function() {
                                                var inp = jQuery(this);
                                                inp.data('oldval',inp.val());
                                            });
                                        }
                                        bt.data('oldstatus',"unchecked");
                                    }

                                    // CHECK DISABLE VALUES AND INHERIT THEM
                                    /*if (!s.n) {
                                     s.w_n.val(s.w_d.val());
                                     s.h_n.val(s.h_d.val());
                                     }
                                     if (!s.t) {
                                     s.w_t.val(s.w_n.val());
                                     s.h_t.val(s.h_n.val());
                                     }
                                     if (!s.m) {
                                     s.w_m.val(s.w_t.val());
                                     s.h_m.val(s.h_t.val());
                                     }*/

                                    readLayoutValues(0);

                                });

                                jQuery('.slide-size-wrapper .tp-moderncheckbox').change();
                                readLayoutValues();
                                jQuery('input[name="slider_type"], #auto_height, #width, #height, input[name="width_notebook"], input[name="height_notebook"], input[name="width_tablet"], input[name="height_tablet"], input[name="width_mobile"], input[name="height_mobile"]').on("change",function() {
                                    readLayoutValues(1);
                                });
                            });
                        </script>
                        <!-- FALLBACK SETTINGS -->
                        <p style="display:none">
                            <?php $force_full_width = RevSliderFunctions::getVal($arrFieldsParams, 'force_full_width', 'off'); ?>
                            <span class="rev-new-label"><?php echo t('Force Full Width:') ?></span>
                            <input type="checkbox" class="tp-moderncheckbox " id="force_full_width"
                                   name="force_full_width"
                                   data-unchecked="off" <?php RevSliderFunctions::checked($force_full_width, 'on'); ?>>

                        </p>

                        <script>
                            function rsSelectorFun(firsttime) {

                                jQuery('.rs-slidersize').removeClass("selected");
                                jQuery('.rs-slidesize-selector input:checked').closest(".rs-slidersize").addClass("selected");


                                // IF AUTO IS SELECTED AND FULLSCREEN IS FORCED (FALL BACK) THAN SELECT FULLWIDTH !
                                if (firsttime === 1) {

                                    if (jQuery('#force_full_width').attr('checked') === "checked" && jQuery('#slider_type_1').is(':checked')) {
                                        jQuery('#slider_type_1').removeAttr('checked').change();
                                        jQuery('#slider_type_2').attr('checked', "checked").change();
                                    }

                                    if (jQuery('#force_full_width').attr('checked') !== "checked" && jQuery('#slider_type_2').is(':checked')) {
                                        jQuery('#slider_type_2').removeAttr('checked').change();
                                        jQuery('#slider_type_1').attr('checked', "checked").change();
                                    }
                                }

                                // FORCE FULLWIDTH ON FULLWIDTH AND FULLSCREEN
                                if (jQuery('#slider_type_2').is(':checked') || jQuery('#slider_type_3').is(':checked'))
                                    jQuery('#force_full_width').attr('checked', "checked").change();
                                else
                                    jQuery('#force_full_width').removeAttr('checked').change();


                            }
                            document.addEventListener("DOMContentLoaded", function () {
                                jQuery('.rs-slidesize-selector input').change(rsSelectorFun);
                                jQuery('#force_full_width').change();
                                rsSelectorFun(1);
                            })
                        </script>
                        <div style="float:none; clear:both"></div>
                    </div>
                </div>

                <div id="customize_build_implement_sb" class="setting_box">
                    <h3>
                        <span class="setting-step-number"><?php echo $index_content++ ?></span><span><?php echo t("Customize, Build & Implement") ?></span>
                    </h3>
                    <div class="inside" style="padding:35px 20px">
                        <div class="slidertitlebox breakdownonmobile">
							<span class="one-third-container" style="text-align:center">
								<img style="width:100%; max-width:325px;"
                                     src="<?php echo RevSliderFunctions::asset('/admin/images/mainoptions/mini-customizeslide.jpg') ?>">
								<span class="cbi-title"><?php echo t("Advanced Settings") ?></span>
								<span class="description"
                                      style="text-align:center;min-height:60px;"><?php echo t("Go for further customization using the advanced settings on the right of this configuration page.") ?></span>
								<div style="float:none; clear:both; height:20px;display:block;"></div>
								<a class="button-primary revblue" href="#form_slider_params"><i
                                            class="revicon-cog"></i><?php echo t("Scroll to Options") ?></a>
							</span>

                            <span class="one-third-container" style="text-align:center">
								<img style="width:100%;max-width:325px;"
                                     src="<?php echo RevSliderFunctions::asset('/admin/images/mainoptions/mini-editslide.jpg') ?>">
								<span class="cbi-title"><?php echo t("Start Building Slides") ?></span>
								<span class="description"
                                      style="text-align:center;min-height:60px;"><?php echo t("Our drag and drop editor will make creating slide content an absolut breeze. This is where the magic happens!") ?></span>
								<div style="float:none; clear:both; height:20px;"></div>
                                <?php
                                if (isset($linksEditSlides)) {
                                    ?>
                                    <a class="button-primary revblue" href="<?php echo $linksEditSlides; ?>"
                                       id="link_edit_slides"><i
                                                class="revicon-pencil-1"></i><?php echo t("Edit Slides") ?> </a>
                                    <?php
                                }
                                ?>
							</span>

                            <span class="one-third-container" style="text-align:center">
								<img id="impl_yr_sldr" style="width:100%;max-width:325px;"
                                     src="<?php echo RevSliderFunctions::asset('/admin/images/mainoptions/mini-implement.jpg') ?>"><span
                                        class="description"></span>
								<span class="cbi-title"><?php echo t("Implement your Slider") ?></span>
								<span class="description"
                                      style="text-align:center;min-height:60px;"><?php echo t("There are several ways to add your slider to your wordpress post / page / etc.") ?></span>
								<div style="float:none; clear:both; height:20px;"></div>

								<span class="button-primary revblue rs-embed-slider"><i
                                            class="eg-icon-plus-circled"></i><?php echo t("Embed Slider") ?> </span>

							</span>
                        </div>
                    </div>
                    <div class="buttonarea" style="background-color:#eee; text-align:center">
                        <a style="width:125px" class='button-primary revgreen' href='javascript:void(0)'
                           id="button_save_slider"><i class="rs-rp-accordion-icon rs-icon-save-light"
                                                      style="display: inline-block;vertical-align: middle;width: 18px;height: 20px;margin-right:5px;background-repeat: no-repeat;"></i><?php echo t("Save Settings") ?>
                        </a>
                        <span id="loader_update" class="loader_round"
                              style="display:none;background-color:#27AE60 !important; color:#fff;padding: 4px 5px 5px 25px;margin-right: 5px;"><?php echo t("updating...") ?> </span>
                        <span id="update_slider_success" class="success_message"></span>
                        <a style="width:125px" class='button-primary revred' id="button_delete_slider"
                           href='javascript:void(0)'><i
                                    class="revicon-trash"></i><?php echo t("Delete Slider") ?></a>
                        <a style="width:125px" class='button-primary revyellow' id="button_close_slider_edit"
                           href='<?php echo RevSliderFunctions::getViewUrl("sliders") ?>'><i
                                    class="eg-icon-th-large"></i><?php echo t("All Sliders") ?></a>
                        <a style="width:125px" class="button-primary revgray" href="javascript:void(0)"
                           id="button_preview_slider" title="<?php echo t("Preview Slider") ?>"><i
                                    class="revicon-search-1"></i><?php echo t("Preview") ?></a>
                    </div>
                </div>

                <!-- END OF THE HTML MARKUP FOR THE MAIN SETTINGS -->
            </form>

            <script>
                var use_alias;
                var use_title;
                document.addEventListener("DOMContentLoaded", function () {
                    jQuery('body').on('click', '.rs-embed-slider', function () {
                        use_alias = jQuery('#alias').val();
                        use_title = jQuery('#title').val();
                        console.log('alias',use_alias);
                        console.log('alias',use_title);
                        jQuery('.rs-dialog-embed-slider').find('.rs-example-title').text(use_title);
                        //jQuery('.rs-dialog-embed-slider').find('.rs-example-alias-1').text('[rev_slider alias="' + use_alias + '"]');

                        jQuery('.rs-dialog-embed-slider').dialog({
                            modal: true,
                            resizable: false,
                            minWidth: 750,
                            minHeight: 300,
                            closeOnEscape: true
                        });
                    });
                });
            </script>

            <div class="divide20"></div>

            <?php
            if ($is_edit) {
                $custom_css = '';
                $custom_js = '';
                if (!empty($sliderID)) {
                    $custom_css = @stripslashes($arrFieldsParams['custom_css']);
                    $custom_js = @stripslashes($arrFieldsParams['custom_javascript']);
                }

                $hidebox = '';
                if (!RevSliderFunctions::isAdminUser()) {
                    $hidebox = 'display: none;';
                }
                ?>

                <div id="customize_css_js_sb" class="setting_box" id="css-javascript-customs"
                     style="max-width:100%;position:relative;overflow:hidden;<?php echo $hidebox; ?>">
                    <h3>
                        <span class="setting-step-number"><?php echo $index_content++ ?></span><span><?php echo t("Custom CSS / Javascript") ?></span>
                    </h3>
                    <div class="inside" id="codemirror-wrapper">

                        <span class="cbi-title"><?php echo t("Custom CSS") ?></span>
                        <textarea name="custom_css" id="rs_custom_css"><?php echo $custom_css; ?></textarea>
                        <div class="divide20"></div>

                        <span class="cbi-title"><?php echo t("Custom JavaScript") ?></span>
                        <textarea name="custom_javascript"
                                  id="rs_custom_javascript"><?php echo $custom_js; ?></textarea>
                        <div class="divide20"></div>

                    </div>
                </div>

                <script type="text/javascript">
                    var rev_cm_custom_css = null;
                    var rev_cm_custom_js = null;
                    var hlLineC;
                    var hlLineJ;
                    document.addEventListener("DOMContentLoaded", function () {
                        rev_cm_custom_css = CodeMirror.fromTextArea(document.getElementById("rs_custom_css"), {
                            onChange: function () {
                            },
                            lineNumbers: true,
                            mode: 'css',
                            lineWrapping: true
                        });

                        rev_cm_custom_js = CodeMirror.fromTextArea(document.getElementById("rs_custom_javascript"), {
                            onChange: function () {
                            },
                            lineNumbers: true,
                            mode: 'text/html',
                            lineWrapping: true
                        });


                        jQuery('.rs-cm-refresh').click(function () {
                            rev_cm_custom_css.refresh();
                            rev_cm_custom_js.refresh();
                        });

                        hlLineC = rev_cm_custom_css.setLineClass(0, "activeline");
                        hlLineJ = rev_cm_custom_js.setLineClass(0, "activeline");


                    });
                </script>

                <?php
            }
            ?>
        </div>
        <?php
        return ob_get_clean();
    }

}