<?php
/**
 * Created by FsFlex.
 * User: VH
 * Date: 7/6/2017
 * Time: 4:06 PM
 */

namespace Drupal\revslider\Helper;


use Drupal\revslider\Model\Operations;
use Drupal\revslider\Model\Slide;
use Drupal\revslider\Model\Slider;

class RevSliderPluginUpdate
{
    /**
     * change svg path of all layers from the upload folder if 5.2.5.3+ was installed
     * @since 5.2.5.5
     */
    public static function change_layers_svg_5_2_5_5($sliders = false){
        $sr = new Slider();
        $sl = new Slide();
        $upload_dir = RevSliderFile::uploadDirInfo();
        $path = $upload_dir['baseurl'].'/assets/svg/';

        //$operations = new RevSliderOperations();
        if($sliders === false){ //do it on all Sliders
            $sliders = $sr->getArrSliders(false);
        }else{
            $sliders = array($sliders);
        }

        if(!empty($sliders) && is_array($sliders)){
            foreach($sliders as $slider){
                $slides = $slider->getSlides();
                $staticID = $sl->getStaticSlideID($slider->getID());
                if($staticID !== false){
                    $msl = new Slide();
                    if(strpos($staticID, 'static_') === false){
                        $staticID = 'static_'.$slider->getID();
                    }
                    $msl->initByID($staticID);
                    if($msl->getID() !== ''){
                        $slides = array_merge($slides, array($msl));
                    }
                }

                if(!empty($slides) && is_array($slides)){
                    foreach($slides as $slide){
                        $layers = $slide->getLayers();
                        if(!empty($layers) && is_array($layers)){
                            foreach($layers as $lk => $layer){
                                if(isset($layer['type']) && $layer['type'] == 'svg'){
                                    if(isset($layer['svg']) && isset($layer['svg']->src)){
                                        //change newer path to older path
                                        if(strpos($layers[$lk]['svg']->src, $path) !== false){

                                            $layers[$lk]['svg']->src = str_replace($path, RevSliderFunctions::asset('public/assets/svg/'), $layers[$lk]['svg']->src);
                                        }
                                    }
                                }
                            }

                            $slide->setLayersRaw($layers);
                            $slide->saveLayers();
                        }
                    }
                }
            }
        }
    }
    public static function change_general_settings_5_0_7($sliders = false){
        //handle the new option for shuffle in combination with first alternative slide
        $sr = new Slider();
        $sl = new Slide();
        //$operations = new RevSliderOperations();
        if($sliders === false){ //do it on all Sliders
            $sliders = $sr->getArrSliders(false);
        }else{
            $sliders = array($sliders);
        }

        if(!empty($sliders) && is_array($sliders)){
            foreach($sliders as $slider){
                $settings = $slider->getSettings();

                if(!isset($settings['version']) || version_compare($settings['version'], '5.0.7', '<')){
                    $start_with_slide = $slider->getParam('start_with_slide', '1');

                    if($start_with_slide !== '1'){
                        $slider->updateParam(array('start_with_slide_enable' => 'on'));
                    }

                    $settings['version'] = '5.0.7';
                    $slider->updateSetting(array('version' => '5.0.7'));
                }

            }
        }
    }
    public static function add_general_settings($sliders = false){
        $sr = new Slider();
        $sl = new Slide();
        //$operations = new RevSliderOperations();
        if($sliders === false){ //do it on all Sliders
            $sliders = $sr->getArrSliders(false);
        }else{
            $sliders = array($sliders);
        }
        //$styles = $operations->getCaptionsContentArray();

        if(!empty($sliders) && is_array($sliders)){
            $fonts = RevSliderOptions::getOptions('tp-google-fonts', array());
            foreach($sliders as $slider){
                $settings = $slider->getSettings();
                $bg_freeze = $slider->getParam('parallax_bg_freeze', 'off');
                $google_fonts = $slider->getParam('google_font', array());
                if(!isset($settings['version']) || version_compare($settings['version'], 5.0, '<')){
                    if(empty($google_fonts) && !empty($fonts)){ //add all punchfonts to the Slider
                        foreach($fonts as $font){
                            $google_fonts[] = $font['url'];
                        }
                        $slider->updateParam(array('google_font' => $google_fonts));
                    }
                    $settings['version'] = 5.0;
                    $slider->updateSetting(array('version' => 5.0));
                }

                if($bg_freeze == 'on'){ //deprecated here, moved to slides so remove check here and add on to slides
                    $slider->updateParam(array('parallax_bg_freeze' => 'off'));
                }

                $slides = $slider->getSlides();
                $staticID = $sl->getStaticSlideID($slider->getID());
                if($staticID !== false){
                    $msl = new Slide();
                    if(strpos($staticID, 'static_') === false){
                        $staticID = 'static_'.$slider->getID();
                    }
                    $msl->initByID($staticID);
                    if($msl->getID() !== ''){
                        $slides = array_merge($slides, array($msl));
                    }
                }
                if(!empty($slides) && is_array($slides)){
                    foreach($slides as $slide){

                        if($bg_freeze == 'on'){ //set bg_freeze to on for slide settings
                            $slide->setParam('slide_parallax_level', '1');
                        }

                        $slide->saveParams();
                    }
                }

            }
        }
    }
    public static function change_settings_on_layers($sliders = false){
        $sr = new Slider();
        $sl = new Slide();
        if($sliders === false){ //do it on all Sliders
            $sliders = $sr->getArrSliders(false);
        }else{
            $sliders = array($sliders);
        }

        if(!empty($sliders) && is_array($sliders)){
            foreach($sliders as $slider){
                $slides = $slider->getSlides();
                $staticID = $sl->getStaticSlideID($slider->getID());
                if($staticID !== false){
                    $msl = new Slide();
                    if(strpos($staticID, 'static_') === false){
                        $staticID = 'static_'.$slider->getID();
                    }
                    $msl->initByID($staticID);
                    if($msl->getID() !== ''){
                        $slides = array_merge($slides, array($msl));
                    }
                }
                if(!empty($slides) && is_array($slides)){
                    foreach($slides as $slide){
                        $layers = $slide->getLayers();
                        if(!empty($layers) && is_array($layers)){
                            $do_save = false;
                            foreach($layers as $lk => $layer){
                                $link_slide = RevSliderFunctions::getVal($layer, 'link_slide', false);
                                if($link_slide != false && $link_slide !== 'nothing'){ //link to slide/scrollunder is set, move it to actions
                                    $layers[$lk]['layer_action'] = new \stdClass();
                                    switch($link_slide){
                                        case 'link':
                                            $link = RevSliderFunctions::getVal($layer, 'link');
                                            $link_open_in = RevSliderFunctions::getVal($layer, 'link_open_in');
                                            $layers[$lk]['layer_action']->action = array('a' => 'link');
                                            $layers[$lk]['layer_action']->link_type = array('a' => 'a');
                                            $layers[$lk]['layer_action']->image_link = array('a' => $link);
                                            $layers[$lk]['layer_action']->link_open_in = array('a' => $link_open_in);

                                            unset($layers[$lk]['link']);
                                            unset($layers[$lk]['link_open_in']);
                                        case 'next':
                                            $layers[$lk]['layer_action']->action = array('a' => 'next');
                                            break;
                                        case 'prev':
                                            $layers[$lk]['layer_action']->action = array('a' => 'prev');
                                            break;
                                        case 'scroll_under':
                                            $scrollunder_offset = RevSliderFunctions::getVal($layer, 'scrollunder_offset');
                                            $layers[$lk]['layer_action']->action = array('a' => 'scroll_under');
                                            $layers[$lk]['layer_action']->scrollunder_offset = array('a' => $scrollunder_offset);

                                            unset($layers[$lk]['scrollunder_offset']);
                                            break;
                                        default: //its an ID, so its a slide ID
                                            $layers[$lk]['layer_action']->action = array('a' => 'jumpto');
                                            $layers[$lk]['layer_action']->jump_to_slide = array('a' => $link_slide);
                                            break;

                                    }
                                    $layers[$lk]['layer_action']->tooltip_event = array('a' => 'click');

                                    unset($layers[$lk]['link_slide']);

                                    $do_save = true;
                                }
                            }

                            if($do_save){
                                $slide->setLayersRaw($layers);
                                $slide->saveLayers();
                            }
                        }
                    }
                }
            }
        }
    }
    public static function add_style_settings_to_layer($sliders = false){

        $sr = new Slider();
        $sl = new Slide();
        $operations = new Operations();
        if($sliders === false){ //do it on all Sliders
            $sliders = $sr->getArrSliders(false);
        }else{
            $sliders = array($sliders);
        }

        $styles = $operations->getCaptionsContentArray();

        if(!empty($sliders) && is_array($sliders)){
            foreach($sliders as $slider){
                $slides = $slider->getSlides();
                $staticID = $sl->getStaticSlideID($slider->getID());
                if($staticID !== false){
                    $msl = new Slide();
                    if(strpos($staticID, 'static_') === false){
                        $staticID = 'static_'.$slider->getID();
                    }
                    $msl->initByID($staticID);
                    if($msl->getID() !== ''){
                        $slides = array_merge($slides, array($msl));
                    }
                }
                if(!empty($slides) && is_array($slides)){
                    foreach($slides as $slide){
                        $layers = $slide->getLayers();
                        if(!empty($layers) && is_array($layers)){
                            foreach($layers as $lk => $layer){
                                $static_styles = (array) RevSliderFunctions::getVal($layer, 'static_styles', array());
                                $def_val = (array) RevSliderFunctions::getVal($layer, 'deformation', array());
                                $defh_val = (array) RevSliderFunctions::getVal($layer, 'deformation-hover', array());

                                if(empty($def_val)){

                                    //add parallax always!
                                    $def_val['parallax'] = RevSliderFunctions::getVal($layer, 'parallax_level', '-');
                                    $layers[$lk]['deformation'] = $def_val;

                                    //check for selected style in styles, then add all deformations to the layer
                                    $cur_style = RevSliderFunctions::getVal($layer, 'style', '');

                                    if(trim($cur_style) == '') continue;
                                    $wws = false;

                                    foreach($styles as $style){
                                        if($style['handle'] == '.tp-caption.'.$cur_style){
                                            $wws = $style;
                                            break;
                                        }
                                    }

                                    if($wws == false) continue;

                                    $css_idle = '';
                                    $css_hover = '';

                                    $wws['params'] = (array)$wws['params'];
                                    $wws['hover'] = (array)$wws['hover'];
                                    $wws['advanced'] = (array)$wws['advanced'];

                                    if(isset($wws['params']['font-family'])) $def_val['font-family'] = $wws['params']['font-family'];
                                    if(isset($wws['params']['padding'])){
                                        $raw_pad = $wws['params']['padding'];
                                        if(!is_array($raw_pad)) $raw_pad = explode(' ', $raw_pad);

                                        switch(count($raw_pad)){
                                            case 1:
                                                $raw_pad = array($raw_pad[0], $raw_pad[0], $raw_pad[0], $raw_pad[0]);
                                                break;
                                            case 2:
                                                $raw_pad = array($raw_pad[0], $raw_pad[1], $raw_pad[0], $raw_pad[1]);
                                                break;
                                            case 3:
                                                $raw_pad = array($raw_pad[0], $raw_pad[1], $raw_pad[2], $raw_pad[1]);
                                                break;
                                        }

                                        $def_val['padding'] = $raw_pad;
                                    }
                                    if(isset($wws['params']['font-style'])) $def_val['font-style'] = $wws['params']['font-style'];
                                    if(isset($wws['params']['text-decoration'])) $def_val['text-decoration'] = $wws['params']['text-decoration'];
                                    if(isset($wws['params']['background-color'])){
                                        if(RevSliderFunctions::isrgb($wws['params']['background-color'])){
                                            $def_val['background-color'] = RevSliderFunctions::rgba2hex($wws['params']['background-color']);
                                        }else{
                                            $def_val['background-color'] = $wws['params']['background-color'];
                                        }
                                    }
                                    if(isset($wws['params']['background-transparency'])){
                                        $def_val['background-transparency'] = $wws['params']['background-transparency'];
                                        if($def_val['background-transparency'] > 1) $def_val['background-transparency'] /= 100;
                                    }else{
                                        if(isset($wws['params']['background-color'])) $def_val['background-transparency'] = RevSliderFunctions::get_trans_from_rgba($wws['params']['background-color'], true);
                                    }

                                    if(isset($wws['params']['border-color'])){
                                        if(RevSliderFunctions::isrgb($wws['params']['border-color'])){
                                            $def_val['border-color'] = RevSliderFunctions::rgba2hex($wws['params']['border-color']);
                                        }else{
                                            $def_val['border-color'] = $wws['params']['border-color'];
                                        }
                                    }

                                    if(isset($wws['params']['border-style'])) $def_val['border-style'] = $wws['params']['border-style'];
                                    if(isset($wws['params']['border-width'])) $def_val['border-width'] = $wws['params']['border-width'];
                                    if(isset($wws['params']['border-radius'])){
                                        $raw_bor = $wws['params']['border-radius'];
                                        if(!is_array($raw_bor)) $raw_bor = explode(' ', $raw_bor);

                                        switch(count($raw_bor)){
                                            case 1:
                                                $raw_bor = array($raw_bor[0], $raw_bor[0], $raw_bor[0], $raw_bor[0]);
                                                break;
                                            case 2:
                                                $raw_bor = array($raw_bor[0], $raw_bor[1], $raw_bor[0], $raw_bor[1]);
                                                break;
                                            case 3:
                                                $raw_bor = array($raw_bor[0], $raw_bor[1], $raw_bor[2], $raw_bor[1]);
                                                break;
                                        }

                                        $def_val['border-radius'] = $raw_bor;
                                    }
                                    if(isset($wws['params']['x'])) $def_val['x'] = $wws['params']['x'];
                                    if(isset($wws['params']['y'])) $def_val['y'] = $wws['params']['y'];
                                    if(isset($wws['params']['z'])) $def_val['z'] = $wws['params']['z'];
                                    if(isset($wws['params']['skewx'])) $def_val['skewx'] = $wws['params']['skewx'];
                                    if(isset($wws['params']['skewy'])) $def_val['skewy'] = $wws['params']['skewy'];
                                    if(isset($wws['params']['scalex'])) $def_val['scalex'] = $wws['params']['scalex'];
                                    if(isset($wws['params']['scaley'])) $def_val['scaley'] = $wws['params']['scaley'];
                                    if(isset($wws['params']['opacity'])) $def_val['opacity'] = $wws['params']['opacity'];
                                    if(isset($wws['params']['xrotate'])) $def_val['xrotate'] = $wws['params']['xrotate'];
                                    if(isset($wws['params']['yrotate'])) $def_val['yrotate'] = $wws['params']['yrotate'];
                                    if(isset($wws['params']['2d_rotation'])) $def_val['2d_rotation'] = $wws['params']['2d_rotation'];
                                    if(isset($wws['params']['2d_origin_x'])) $def_val['2d_origin_x'] = $wws['params']['2d_origin_x'];
                                    if(isset($wws['params']['2d_origin_y'])) $def_val['2d_origin_y'] = $wws['params']['2d_origin_y'];
                                    if(isset($wws['params']['pers'])) $def_val['pers'] = $wws['params']['pers'];

                                    if(isset($wws['params']['color'])){
                                        if(RevSliderFunctions::isrgb($wws['params']['color'])){
                                            $static_styles['color'] = RevSliderFunctions::rgba2hex($wws['params']['color']);
                                        }else{
                                            $static_styles['color'] = $wws['params']['color'];
                                        }
                                    }

                                    if(isset($wws['params']['font-weight'])) $static_styles['font-weight'] = $wws['params']['font-weight'];
                                    if(isset($wws['params']['font-size'])) $static_styles['font-size'] = $wws['params']['font-size'];
                                    if(isset($wws['params']['line-height'])) $static_styles['line-height'] = $wws['params']['line-height'];
                                    if(isset($wws['params']['font-family'])) $static_styles['font-family'] = $wws['params']['font-family'];

                                    if(isset($wws['advanced']) && isset($wws['advanced']['idle']) && is_array($wws['advanced']['idle']) && !empty($wws['advanced']['idle'])){
                                        $css_idle = '{'."\n";
                                        foreach($wws['advanced']['idle'] as $handle => $value){
                                            $value = implode(' ', $value);
                                            if($value !== '')
                                                $css_idle .= '	'.$handle.': '.$value.';'."\n";

                                        }
                                        $css_idle .= '}'."\n";
                                    }

                                    if(isset($wws['hover']['color'])){
                                        if(RevSliderFunctions::isrgb($wws['hover']['color'])){
                                            $defh_val['color'] = RevSliderFunctions::rgba2hex($wws['hover']['color']);
                                        }else{
                                            $defh_val['color'] = $wws['hover']['color'];
                                        }
                                    }
                                    if(isset($wws['hover']['text-decoration'])) $defh_val['text-decoration'] = $wws['hover']['text-decoration'];
                                    if(isset($wws['hover']['background-color'])){
                                        if(RevSliderFunctions::isrgb($wws['hover']['background-color'])){
                                            $defh_val['background-color'] = RevSliderFunctions::rgba2hex($wws['hover']['background-color']);
                                        }else{
                                            $defh_val['background-color'] = $wws['hover']['background-color'];
                                        }
                                    }
                                    if(isset($wws['hover']['background-transparency'])){
                                        $defh_val['background-transparency'] = $wws['hover']['background-transparency'];
                                        if($defh_val['background-transparency'] > 1) $defh_val['background-transparency'] /= 100;
                                    }else{
                                        if(isset($wws['hover']['background-color'])) $defh_val['background-transparency'] = RevSliderFunctions::get_trans_from_rgba($wws['hover']['background-color'], true);
                                    }
                                    if(isset($wws['hover']['border-color'])){
                                        if(RevSliderFunctions::isrgb($wws['hover']['border-color'])){
                                            $defh_val['border-color'] = RevSliderFunctions::rgba2hex($wws['hover']['border-color']);
                                        }else{
                                            $defh_val['border-color'] = $wws['hover']['border-color'];
                                        }
                                    }
                                    if(isset($wws['hover']['border-style'])) $defh_val['border-style'] = $wws['hover']['border-style'];
                                    if(isset($wws['hover']['border-width'])) $defh_val['border-width'] = $wws['hover']['border-width'];
                                    if(isset($wws['hover']['border-radius'])){
                                        $raw_bor = $wws['hover']['border-radius'];
                                        if(!is_array($raw_bor)) $raw_bor = explode(' ', $raw_bor);

                                        switch(count($raw_bor)){
                                            case 1:
                                                $raw_bor = array($raw_bor[0], $raw_bor[0], $raw_bor[0], $raw_bor[0]);
                                                break;
                                            case 2:
                                                $raw_bor = array($raw_bor[0], $raw_bor[1], $raw_bor[0], $raw_bor[1]);
                                                break;
                                            case 3:
                                                $raw_bor = array($raw_bor[0], $raw_bor[1], $raw_bor[2], $raw_bor[1]);
                                                break;
                                        }

                                        $defh_val['border-radius'] = $raw_bor;
                                    }
                                    if(isset($wws['hover']['x'])) $defh_val['x'] = $wws['hover']['x'];
                                    if(isset($wws['hover']['y'])) $defh_val['y'] = $wws['hover']['y'];
                                    if(isset($wws['hover']['z'])) $defh_val['z'] = $wws['hover']['z'];
                                    if(isset($wws['hover']['skewx'])) $defh_val['skewx'] = $wws['hover']['skewx'];
                                    if(isset($wws['hover']['skewy'])) $defh_val['skewy'] = $wws['hover']['skewy'];
                                    if(isset($wws['hover']['scalex'])) $defh_val['scalex'] = $wws['hover']['scalex'];
                                    if(isset($wws['hover']['scaley'])) $defh_val['scaley'] = $wws['hover']['scaley'];
                                    if(isset($wws['hover']['opacity'])) $defh_val['opacity'] = $wws['hover']['opacity'];
                                    if(isset($wws['hover']['xrotate'])) $defh_val['xrotate'] = $wws['hover']['xrotate'];
                                    if(isset($wws['hover']['yrotate'])) $defh_val['yrotate'] = $wws['hover']['yrotate'];
                                    if(isset($wws['hover']['2d_rotation'])) $defh_val['2d_rotation'] = $wws['hover']['2d_rotation'];
                                    if(isset($wws['hover']['2d_origin_x'])) $defh_val['2d_origin_x'] = $wws['hover']['2d_origin_x'];
                                    if(isset($wws['hover']['2d_origin_y'])) $defh_val['2d_origin_y'] = $wws['hover']['2d_origin_y'];
                                    if(isset($wws['hover']['speed'])) $defh_val['speed'] = $wws['hover']['speed'];
                                    if(isset($wws['hover']['easing'])) $defh_val['easing'] = $wws['hover']['easing'];

                                    if(isset($wws['advanced']) && isset($wws['advanced']['hover']) && is_array($wws['advanced']['hover']) && !empty($wws['advanced']['hover'])){
                                        $css_hover = '{'."\n";
                                        foreach($wws['advanced']['hover'] as $handle => $value){
                                            $value = implode(' ', $value);
                                            if($value !== '')
                                                $css_hover .= '	'.$handle.': '.$value.';'."\n";

                                        }
                                        $css_hover .= '}'."\n";

                                    }

                                    if(!isset($layers[$lk]['inline'])) $layers[$lk]['inline'] = array();
                                    if($css_idle !== ''){
                                        $layers[$lk]['inline']['idle'] = $css_idle;
                                    }
                                    if($css_hover !== ''){
                                        $layers[$lk]['inline']['idle'] = $css_hover;
                                    }

                                    $layers[$lk]['deformation'] = $def_val;
                                    $layers[$lk]['deformation-hover'] = $defh_val;
                                    $layers[$lk]['static_styles'] = $static_styles;
                                }
                            }

                            $slide->setLayersRaw($layers);
                            $slide->saveLayers();
                        }
                    }
                }
            }
        }
    }
    public static function update_css_styles()
    {

        $css = new RevSliderCssParser();
        $styles = RevSliderDB::instance(array(
            'table' => RevSliderGlobals::$table_css
        ))->get();
        $default_classes = RevSliderCssParser::default_css_classes();

        $cs = array(
            'background-color' => 'backgroundColor', //rgb rgba and opacity
            'border-color'     => 'borderColor',
            'border-radius'    => 'borderRadius',
            'border-style'     => 'borderStyle',
            'border-width'     => 'borderWidth',
            'color'            => 'color',
            'font-family'      => 'fontFamily',
            'font-size'        => 'fontSize',
            'font-style'       => 'fontStyle',
            'font-weight'      => 'fontWeight',
            'line-height'      => 'lineHeight',
            'opacity'          => 'opacity',
            'padding'          => 'padding',
            'text-decoration'  => 'textDecoration',
            'text-align'       => 'textAlign'
        );

        $cs = array_merge($cs, RevSliderCssParser::get_deformation_css_tags());

        foreach ($styles as $key => $attr) {

            if (isset($attr['advanced'])) {
                $adv = json_decode($attr['advanced'], true); // = array('idle' => array(), 'hover' => '');
            } else {
                $adv = array('idle' => array(), 'hover' => '');
            }

            if (!isset($adv['idle'])) $adv['idle'] = array();
            if (!isset($adv['hover'])) $adv['hover'] = array();
            //only do this to styles prior 5.0
            $settings = json_decode($attr['settings'], true);
            if (!empty($settings) && isset($settings['translated'])) {
                if (version_compare($settings['translated'], 5.0, '>=')) continue;
            }

            $idle = json_decode($attr['params'], true);
            $hover = json_decode($attr['hover'], true);

            //check if in styles, there is type, then change the type text to something else
            $the_type = 'text';

            if (!empty($idle)) {
                foreach ($idle as $style => $value) {
                    if ($style == 'type') $the_type = $value;
                    if (!isset($cs[$style])) {
                        $adv['idle'][$style] = $value;
                        unset($idle[$style]);
                    }
                }
            }

            if (!empty($hover)) {
                foreach ($hover as $style => $value) {
                    if (!isset($cs[$style])) {
                        $adv['hover'][$style] = $value;
                        unset($hover[$style]);
                    }
                }
            }

            $settings['translated'] = 5.0; //set the style version to 5.0
            $settings['type'] = $the_type; //set the type version to text, since 5.0 we also have buttons and shapes, so we need to differentiate from now on


            if (!isset($settings['version'])) {
                if (isset($default_classes[$styles[$key]['handle']])) {
                    $settings['version'] = $default_classes[$styles[$key]['handle']];
                } else {
                    $settings['version'] = 'custom'; //set the version to custom as its not in the defaults
                }
            }
            $styles[$key]['params'] = (!empty($idle)) ? json_encode($idle) : '{}';
            $styles[$key]['hover'] = json_encode($hover);
            $styles[$key]['advanced'] = json_encode($adv);
            $styles[$key]['settings'] = json_encode($settings);
        }
        //save now all styles back to database
        foreach ($styles as $key => $attr) {
            $ret = RevSliderDB::instance(array(
                'table' => RevSliderGlobals::$table_css,
                'where' => array('id', $attr['id'])
            ))->update(array(
                'settings' => $styles[$key]['settings'],
                'params'   => (!empty($styles[$key]['params']) && $styles[$key]['params'] !== 'null') ?  $styles[$key]['params'] : '{}',
                'hover'    => $styles[$key]['hover'],
                'advanced' => $styles[$key]['advanced']
            ));
        }

    }
    public static function add_animation_settings_to_layer($sliders = false){
        $sr = new Slider();
        $sl = new Slide();

        if($sliders === false){ //do it on all Sliders
            $sliders = $sr->getArrSliders(false);
        }else{
            $sliders = array($sliders);
        }


        $inAnimations = Operations::getArrAnimations(true);
        $outAnimations = Operations::getArrEndAnimations(true);
        if(!empty($sliders) && is_array($sliders)){
            foreach($sliders as $slider){
                $slides = $slider->getSlides();
                $staticID = $sl->getStaticSlideID($slider->getID());
                if($staticID !== false){
                    $msl = new Slide();
                    if(strpos($staticID, 'static_') === false){
                        $staticID = 'static_'.$slider->getID();
                    }
                    $msl->initByID($staticID);
                    if($msl->getID() !== ''){
                        $slides = array_merge($slides, array($msl));
                    }
                }

                if(!empty($slides) && is_array($slides)){
                    foreach($slides as $slide){
                        $layers = $slide->getLayers();
                        if(!empty($layers) && is_array($layers)){
                            foreach($layers as $lk => $layer){
                                if(RevSliderFunctions::getVal($layer, 'x_start', false) === false){ //values are not set, set them now through
                                    $animation = RevSliderFunctions::getVal($layer, 'animation', 'tp-fade');
                                    $endanimation = RevSliderFunctions::getVal($layer, 'endanimation', 'tp-fade');
                                    if($animation == 'fade') $animation = 'tp-fade';
                                    if($endanimation == 'fade') $endanimation = 'tp-fade';

                                    $anim_values = array();
                                    foreach($inAnimations as $handle => $anim){
                                        if($handle == $animation){
                                            $anim_values = (isset($anim['params'])) ? $anim['params'] : '';
                                            if(!is_array($anim_values)) $anim_values = json_encode($anim_values);
                                            break;
                                        }
                                    }

                                    $anim_endvalues = array();
                                    foreach($outAnimations as $handle => $anim){
                                        if($handle == $endanimation){
                                            $anim_endvalues = (isset($anim['params'])) ? $anim['params'] : '';
                                            if(!is_array($anim_endvalues)) $anim_endvalues = json_encode($anim_endvalues);
                                            break;
                                        }
                                    }

                                    $layers[$lk]['x_start'] = RevSliderFunctions::getVal($anim_values, 'movex', 'inherit');
                                    $layers[$lk]['x_end'] = RevSliderFunctions::getVal($anim_endvalues, 'movex', 'inherit');
                                    $layers[$lk]['y_start'] = RevSliderFunctions::getVal($anim_values, 'movey', 'inherit');
                                    $layers[$lk]['y_end'] = RevSliderFunctions::getVal($anim_endvalues, 'movey', 'inherit');
                                    $layers[$lk]['z_start'] = RevSliderFunctions::getVal($anim_values, 'movez', 'inherit');
                                    $layers[$lk]['z_end'] = RevSliderFunctions::getVal($anim_endvalues, 'movez', 'inherit');

                                    $layers[$lk]['x_rotate_start'] = RevSliderFunctions::getVal($anim_values, 'rotationx', 'inherit');
                                    $layers[$lk]['x_rotate_end'] = RevSliderFunctions::getVal($anim_endvalues, 'rotationx', 'inherit');
                                    $layers[$lk]['y_rotate_start'] = RevSliderFunctions::getVal($anim_values, 'rotationy', 'inherit');
                                    $layers[$lk]['y_rotate_end'] = RevSliderFunctions::getVal($anim_endvalues, 'rotationy', 'inherit');
                                    $layers[$lk]['z_rotate_start'] = RevSliderFunctions::getVal($anim_values, 'rotationz', 'inherit');
                                    $layers[$lk]['z_rotate_end'] = RevSliderFunctions::getVal($anim_endvalues, 'rotationz', 'inherit');

                                    $layers[$lk]['scale_x_start'] = RevSliderFunctions::getVal($anim_values, 'scalex', 'inherit');
                                    if(intval($layers[$lk]['scale_x_start']) > 10) $layers[$lk]['scale_x_start'] /= 100;
                                    $layers[$lk]['scale_x_end'] = RevSliderFunctions::getVal($anim_endvalues, 'scalex', 'inherit');
                                    if(intval($layers[$lk]['scale_x_end']) > 10) $layers[$lk]['scale_x_end'] /= 100;
                                    $layers[$lk]['scale_y_start'] = RevSliderFunctions::getVal($anim_values, 'scaley', 'inherit');
                                    if(intval($layers[$lk]['scale_y_start']) > 10) $layers[$lk]['scale_y_start'] /= 100;
                                    $layers[$lk]['scale_y_end'] = RevSliderFunctions::getVal($anim_endvalues, 'scaley', 'inherit');
                                    if(intval($layers[$lk]['scale_y_end']) > 10) $layers[$lk]['scale_y_end'] /= 100;

                                    $layers[$lk]['skew_x_start'] = RevSliderFunctions::getVal($anim_values, 'skewx', 'inherit');
                                    $layers[$lk]['skew_x_end'] = RevSliderFunctions::getVal($anim_endvalues, 'skewx', 'inherit');
                                    $layers[$lk]['skew_y_start'] = RevSliderFunctions::getVal($anim_values, 'skewy', 'inherit');
                                    $layers[$lk]['skew_y_end'] = RevSliderFunctions::getVal($anim_endvalues, 'skewy', 'inherit');

                                    $layers[$lk]['opacity_start'] = RevSliderFunctions::getVal($anim_values, 'captionopacity', 'inherit');
                                    $layers[$lk]['opacity_end'] = RevSliderFunctions::getVal($anim_endvalues, 'captionopacity', 'inherit');

                                }
                            }
                            $slide->setLayersRaw($layers);
                            $slide->saveLayers();
                        }
                    }
                }
            }
        }
    }
}