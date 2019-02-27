<?php
/**
 * Created by FsFlex.
 * User: VH
 * Date: 7/12/2017
 * Time: 5:16 PM
 */

namespace Drupal\revslider\Model;


use Drupal\revslider\Helper\RevSliderFunctions;

class Front extends RevSliderElementsBase
{
    public static function modify_punch_url($url){
        $operations = new Operations();
        $arrValues = $operations->getGeneralSettingsValues();

        $set_diff_font = RevSliderFunctions::getVal($arrValues, "change_font_loading",'');
        if($set_diff_font !== ''){
            return $set_diff_font;
        }else{
            return $url;
        }
    }
    public static function add_setREVStartSize(){
        $script = '<script type="text/javascript">';
        $script .= 'function setREVStartSize(e){
				try{ var i=jQuery(window).width(),t=9999,r=0,n=0,l=0,f=0,s=0,h=0;					
					if(e.responsiveLevels&&(jQuery.each(e.responsiveLevels,function(e,f){f>i&&(t=r=f,l=e),i>f&&f>r&&(r=f,n=e)}),t>r&&(l=n)),f=e.gridheight[l]||e.gridheight[0]||e.gridheight,s=e.gridwidth[l]||e.gridwidth[0]||e.gridwidth,h=i/s,h=h>1?1:h,f=Math.round(h*f),"fullscreen"==e.sliderLayout){var u=(e.c.width(),jQuery(window).height());if(void 0!=e.fullScreenOffsetContainer){var c=e.fullScreenOffsetContainer.split(",");if (c) jQuery.each(c,function(e,i){u=jQuery(i).length>0?u-jQuery(i).outerHeight(!0):u}),e.fullScreenOffset.split("%").length>1&&void 0!=e.fullScreenOffset&&e.fullScreenOffset.length>0?u-=jQuery(window).height()*parseInt(e.fullScreenOffset,0)/100:void 0!=e.fullScreenOffset&&e.fullScreenOffset.length>0&&(u-=parseInt(e.fullScreenOffset,0))}f=u}else void 0!=e.minHeight&&f<e.minHeight&&(f=e.minHeight);e.c.closest(".rev_slider_wrapper").css({height:f})					
				}catch(d){console.log("Failure at Presize of Slider:"+d)}
			};';
        $script .= '</script>'."\n";
//        echo apply_filters('revslider_add_setREVStartSize', $script);
        return ($script);
    }
}