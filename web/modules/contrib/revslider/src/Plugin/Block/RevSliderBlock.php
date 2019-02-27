<?php

/**
 * Created by FsFlex.
 * User: VH
 * Date: 7/28/2017
 * Time: 2:10 PM
 */
namespace Drupal\revslider\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\revslider\Helper\RevSliderFunctions;
use Drupal\revslider\Helper\RevSliderInit;
use Drupal\revslider\Model\Output;

/**
 * Provides a 'RevSlider' Block.
 *
 * @Block(
 *   id = "revslider_block",
 *   admin_label = @Translation("RevSlider block"),
 *   category = @Translation("RevSlider block"),
 *   deriver = "Drupal\revslider\Plugin\Derivative\RevSliderBlock",
 * )
 */
class RevSliderBlock extends BlockBase
{
    public function build()
    {
        RevSliderInit::start();
        //test
        $args = array();
        $settings ='';
        $order = '';
        $slider_name = $this->getDerivativeId();
//        var_dump($this->getMachineNameSuggestion());
//        var_dump($slider_name);
//        $entry = array(
//            'machine_name' => $slider_name
//        );
        $alias = $slider_name;
        //
        if($settings !== '') $settings = json_decode(str_replace(array('({', '})', "'"), array('[', ']', '"'), $settings) ,true);
        if($order !== '') $order = explode(',', $order);

        $sliderAlias = ($alias != '') ? $alias : RevSliderFunctions::getVal($args,0);

        $gal_ids = false;//RevSliderFunctionsWP::check_for_shortcodes($mid_content); //check for example on gallery shortcode and do stuff

        ob_start();
        $this->get_js_lib_required();
        if(!empty($gal_ids)){ //add a gallery based slider
            $slider = Output::putSlider($sliderAlias, '', $gal_ids);
        }else{
            $slider = Output::putSlider($sliderAlias, '', array(), $settings, $order);
        }
        $content = ob_get_clean();

        return array(
            '#theme' => 'revslider_front_view',
            '#attached' => array(
                'library'        => array('revslider/front-view'),
            ),
            '#data' => $content,
        );
    }
    protected function get_js_lib_required()
    {
        ?>
        <script type="text/javascript">
            var setREVStartSize;
            document.addEventListener("DOMContentLoaded", function () {
                setREVStartSize = function (e) {
                    try {
                        var i = jQuery(window).width(), t = 9999, r = 0, n = 0, l = 0, f = 0, s = 0, h = 0;
                        if (e.responsiveLevels && (jQuery.each(e.responsiveLevels, function (e, f) {
                                f > i && (t = r = f, l = e), i > f && f > r && (r = f, n = e)
                            }), t > r && (l = n)), f = e.gridheight[l] || e.gridheight[0] || e.gridheight, s = e.gridwidth[l] || e.gridwidth[0] || e.gridwidth, h = i / s, h = h > 1 ? 1 : h, f = Math.round(h * f), "fullscreen" == e.sliderLayout) {
                            var u = (e.c.width(), jQuery(window).height());
                            if (void 0 != e.fullScreenOffsetContainer) {
                                var c = e.fullScreenOffsetContainer.split(",");
                                if (c) jQuery.each(c, function (e, i) {
                                    u = jQuery(i).length > 0 ? u - jQuery(i).outerHeight(!0) : u
                                }), e.fullScreenOffset.split("%").length > 1 && void 0 != e.fullScreenOffset && e.fullScreenOffset.length > 0 ? u -= jQuery(window).height() * parseInt(e.fullScreenOffset, 0) / 100 : void 0 != e.fullScreenOffset && e.fullScreenOffset.length > 0 && (u -= parseInt(e.fullScreenOffset, 0))
                            }
                            f = u
                        } else void 0 != e.minHeight && f < e.minHeight && (f = e.minHeight);
                        e.c.closest(".rev_slider_wrapper").css({height: f})
                    } catch (d) {
                        console.log("Failure at Presize of Slider:" + d)
                    }
                };
            })
        </script>

        <?php
    }
    public function getCacheTags()
    {
        $tag = $this->getDerivativeId();
        $tag = 'revslider_'.$tag;
        return Cache::mergeTags(parent::getCacheTags(),array($tag));
    }
}