<?php
/**
 * Created by FsFlex.
 * User: VH
 * Date: 8/1/2017
 * Time: 10:20 AM
 */

namespace Drupal\revslider\Controller\RawTemplates;


use Drupal\revslider\Helper\RevSliderFunctions;
use Drupal\revslider\Helper\RevSliderGlobals;
use Drupal\revslider\Helper\RevSliderOptions;
use Drupal\revslider\Model\Operations;
use Drupal\revslider\Model\Slider;

class SliderOverview
{
    public function getTemplate(array $args = array())
    {
        ob_start();

        $orders = false;

        $slider = new Slider();
        $operations = new Operations();
        $arrSliders = $slider->getArrSliders($orders);
        $glob_vals = $operations->getGeneralSettingsValues();

        $addNewLink = RevSliderFunctions::getViewUrl(RevSliderGlobals::VIEW_SLIDER);


        $fav = RevSliderOptions::getOptions('rev_fav_slider', array());
        if ($orders == false) { //sort the favs to top
            if (!empty($fav) && !empty($arrSliders)) {
                $fav_sort = array();
                foreach ($arrSliders as $skey => $sort_slider) {
                    if (in_array($sort_slider->getID(), $fav)) {
                        $fav_sort[] = $arrSliders[$skey];
                        unset($arrSliders[$skey]);
                    }
                }
                if (!empty($fav_sort)) {
                    //revert order of favs
                    krsort($fav_sort);
                    foreach ($fav_sort as $fav_arr) {
                        array_unshift($arrSliders, $fav_arr);
                    }
                }
            }
        }

        $exampleID = '"slider1"';
        if (!empty($arrSliders))
            $exampleID = '"' . $arrSliders[0]->getAlias() . '"';

        $latest_version = RevSliderOptions::getOptions('revslider-latest-version', RevSliderGlobals::SLIDER_REVISION);
        $stable_version = RevSliderOptions::getOptions('revslider-stable-version', '4.1');

        ?>

        <div class='wrap'>
            <div class="clear_both"></div>
            <div class="title_line" style="margin-bottom:10px">
                <div class="icon32" id="icon-options-general"></div>
                <a href="<?php echo RevSliderGlobals::LINK_HELP_SLIDERS; ?>"
                   class="button-secondary float_right mtop_10 mleft_10" target="_blank"><?php echo t("Help") ?></a>
            </div>

            <div class="clear_both"></div>

            <div class="title_line nobgnopd" style="height:auto; min-height:50px">
                <div class="view_title">
                    <?php echo t("Revolution Sliders") ?>
                </div>
                <div class="slider-sortandfilter">
                    <span class="slider-listviews slider-lg-views" data-type="rs-listview"><i
                                class="eg-icon-align-justify"></i></span>
                    <span class="slider-gridviews slider-lg-views active" data-type="rs-gridview"><i
                                class="eg-icon-th"></i></span>
                    <span class="slider-sort-drop"><?php echo t("Sort By:") ?></span>
                    <select id="sort-sliders" name="sort-sliders" style="max-width: 105px;" class="withlabel">
                        <option value="id" selected="selected"><?php echo t("By ID") ?></option>
                        <option value="name"><?php echo t("By Name") ?></option>
                        <option value="type"><?php echo t("By Type") ?></option>
                        <option value="favorit"><?php echo t("By Favorit") ?></option>
                    </select>

                    <span class="slider-filter-drop"><?php echo t("Filter By:") ?></span>

                    <select id="filter-sliders" name="filter-sliders" style="max-width: 105px;" class="withlabel">
                        <option value="all" selected="selected"><?php echo t("All") ?></option>
                        <option value="posts"><?php echo t("Posts") ?></option>
                        <option value="gallery"><?php echo t("Gallery") ?></option>
                        <option value="vimeo"><?php echo t("Vimeo") ?></option>
                        <option value="youtube"><?php echo t("YouTube") ?></option>
                        <option value="twitter"><?php echo t("Twitter") ?></option>
                        <option value="facebook"><?php echo t("Facebook") ?></option>
                        <option value="instagram"><?php echo t("Instagram") ?></option>
                        <option value="flickr"><?php echo t("Flickr") ?></option>
                    </select>
                </div>
                <div style="width:100%;height:1px;float:none;clear:both"></div>
            </div>

            <?php
            $no_sliders = false;
            if (empty($arrSliders)) {
                ?>
                <span style="display:block;margin-top:15px;margin-bottom:15px;">
                <?php echo t("No Sliders Found") ?>
                </span>
                <?php
                $no_sliders = true;
            }
            $template_seg = new SlidersList();
            echo $template_seg->getTemplate(array(
                'no_sliders' =>$no_sliders,
                'arrSliders'=>$arrSliders,
                'addNewLink'=>$addNewLink,
            ));

            ?>
            <!--
            THE INFO ABOUT EMBEDING OF THE SLIDER
            -->
            <div class="rs-dialog-embed-slider" title="<?php echo t("Embed Slider") ?>" style="display: none;">
                <div class="revyellow"
                     style="background: none repeat scroll 0% 0% #F1C40F; left:0px;top:55px;position:absolute;height:205px;padding:20px 10px;">
                    <i style="color:#fff;font-size:25px" class="revicon-arrows-ccw"></i></div>
                <div style="margin:5px 0px; padding-left: 55px;">
                    <div style="font-size:14px;margin-bottom:10px;">
                        <strong><?php echo t("Standard Embeding") ?></strong></div>
                    <?php echo t("In <b>Block layout</b> editor : Place Block with <b>Category</b> = `<b>RevSlider block</b>` and <b>Block</b> = `<b><code
                            class=\"rs-example-title\"></code></b>`") ?>
                </div>
            </div>
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    jQuery('#advanced-emeding').click(function () {
                        jQuery('#advanced-accord').toggle(200);
                    });
                });
            </script>


            <div style="width:100%;height:40px"></div>

            <!-- THE UPDATE HISTORY OF SLIDER REVOLUTION -->
            <div style="width:100%;height:40px"></div>
            <div class="rs-update-history-wrapper">
                <div class="rs-dash-title-wrap">
                    <div class="rs-dash-title"><?php echo t("Update History") ?></div>
                </div>
                <div class="rs-update-history"><?php include (RevSliderFunctions::changelog_path()); ?></div>
            </div>

        </div>

        <!-- Import slider dialog -->
        <div id="dialog_import_slider" title="<?php echo t("Import Slider") ?>" class="dialog_import_slider"
             style="display:none">
            <form action="<?php echo RevSliderFunctions::ajax_url() ?>" enctype="multipart/form-data" method="post"
                  id="form-import-slider-local">
                <br>
                <input type="hidden" name="action" value="revslider_ajax_action">
                <input type="hidden" name="client_action" value="import_slider_slidersview">
                <input type="hidden" name="nonce" value="<?php echo  RevSliderFunctions::create_nonce(); ?>">
                <?php echo t("Choose the import file") ?>:
                <br>
                <input type="file" size="60" name="import_file" class="input_import_slider">
                <br><br>
                <span style="font-weight: 700;"><?php echo t("Note: styles templates will be updated if they exist!") ?></span><br><br>
                <table>
                    <tr>
                        <td><?php echo t("Custom Animations:") ?></td>
                        <td><input type="radio" name="update_animations" value="true"
                                   checked="checked"> <?php echo t("Overwrite") ?></td>
                        <td><input type="radio" name="update_animations" value="false"> <?php echo t("Append") ?></td>
                    </tr>
                    <tr>
                        <td><?php echo t("Custom Navigations:") ?></td>
                        <td><input type="radio" name="update_navigations" value="true"
                                   checked="checked"> <?php echo t("Overwrite") ?></td>
                        <td><input type="radio" name="update_navigations" value="false"> <?php echo t("Append") ?></td>
                    </tr>
                    <?php
                    $single_page_creation = RevSliderFunctions::getVal($glob_vals, "single_page_creation", "off");
                    ?>
                    <tr style="<?php echo ($single_page_creation == 'on') ? '' : 'display: none;'; ?>">
                        <td><?php echo t('Create Blank Page:') ?></td>
                        <td><input type="radio" name="page-creation" value="true"> <?php echo t('Yes') ?></td>
                        <td><input type="radio" name="page-creation" value="false"
                                   checked="checked"> <?php echo t('No') ?></td>
                    </tr>
                </table>
                <br>
                <input type="submit" class="button-primary revblue tp-be-button rev-import-slider-button"
                       style="display: none;" value="<?php echo t("Import Slider") ?>">
            </form>
        </div>

        <div id="dialog_duplicate_slider" class="dialog_duplicate_layer" title="<?php echo t('Duplicate') ?>"
             style="display:none;">
            <div style="margin-top:14px">
                <span style="margin-right:15px"><?php echo t('Title:') ?></span><input id="rs-duplicate-animation"
                                                                                       type="text"
                                                                                       name="rs-duplicate-animation"
                                                                                       value=""/>
            </div>
        </div>

        <div id="dialog_duplicate_slider_package" class="dialog_duplicate_layer" title="<?php echo t('Duplicate') ?>"
             style="display:none;">
            <div style="margin-top:14px">
                <span style="margin-right:15px"><?php echo t('Prefix:') ?></span><input id="rs-duplicate-prefix"
                                                                                        type="text"
                                                                                        name="rs-duplicate-prefix"
                                                                                        value=""/>
            </div>
        </div>

        <script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function () {
                RevSliderAdmin.initSlidersListView();
                RevSliderAdmin.initNewsletterRoutine();

                jQuery('#benefitsbutton').hover(function () {
                    jQuery('#benefitscontent').slideDown(200);
                }, function () {
                    jQuery('#benefitscontent').slideUp(200);
                });

                jQuery('#why-subscribe').hover(function () {
                    jQuery('#why-subscribe-wrapper').slideDown(200);
                }, function () {
                    jQuery('#why-subscribe-wrapper').slideUp(200);
                });

                jQuery('#tp-validation-box').click(function () {
                    jQuery(this).css({cursor: "default"});
                    if (jQuery('#rs-validation-wrapper').css('display') == "none") {
                        jQuery('#tp-before-validation').hide();
                        jQuery('#rs-validation-wrapper').slideDown(200);
                    }
                });

                jQuery('body').on('click', '.rs-dash-more-info', function () {
                    var btn = jQuery(this),
                        p = btn.closest('.rs-dash-widget-inner'),
                        tmb = btn.data('takemeback'),
                        btxt = '';

                    btxt = btxt + '<div class="rs-dash-widget-warning-panel">';
                    btxt = btxt + '	<i class="eg-icon-cancel rs-dash-widget-wp-cancel"></i>';
                    btxt = btxt + '	<div class="rs-dash-strong-content">' + btn.data("title") + '</div>';
                    btxt = btxt + '	<div class="rs-dash-content-space"></div>';
                    btxt = btxt + '	<div>' + btn.data("content") + '</div>';

                    if (tmb !== "false" && tmb !== false) {
                        btxt = btxt + '	<div class="rs-dash-content-space"></div>';
                        btxt = btxt + '	<span class="rs-dash-invers-button-gray rs-dash-close-panel"><?php echo t('Thanks! Take me back') ?></span>';
                    }
                    btxt = btxt + '</div>';

                    p.append(btxt);
                    var panel = p.find('.rs-dash-widget-warning-panel');

                    punchgs.TweenLite.fromTo(panel, 0.3, {y: -10, autoAlpha: 0}, {
                        autoAlpha: 1,
                        y: 0,
                        ease: punchgs.Power3.easeInOut
                    });
                    panel.find('.rs-dash-widget-wp-cancel, .rs-dash-close-panel').click(function () {
                        punchgs.TweenLite.to(panel, 0.3, {y: -10, autoAlpha: 0, ease: punchgs.Power3.easeInOut});
                        setTimeout(function () {
                            panel.remove();
                        }, 300)
                    })
                });
            });
        </script>
        <?php
        $template_seg = new SliderSelector();
        echo   $template_seg->getTemplate();
        //require self::getPathTemplate('template-slider-selector');

        return ob_get_clean();
    }
}