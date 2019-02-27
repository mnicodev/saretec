<?php
/**
 * Created by FsFlex.
 * User: VH
 * Date: 7/5/2017
 * Time: 6:23 PM
 */

namespace Drupal\revslider\Model;


use Drupal\revslider\Helper\RevSliderDB;
use Drupal\revslider\Helper\RevSliderGlobals;
use Drupal\revslider\Helper\RevSliderOptions;

class Template extends RevSliderElementsBase
{
    private $templates_url		= 'https://templates.themepunch.tools/';
    private $templates_list		= 'revslider/get-list.php';
    private $templates_download	= 'revslider/download.php';

    private $templates_server_path	= '/revslider/images/';
    private $templates_path			= '/revslider/templates/';
    private $templates_path_plugin	= 'admin/assets/imports/';

    private $curl_check	= null;

    const SHOP_VERSION				= '1.2.1';
    public function getDefaultTemplateSliders(){

        $sliders = array();
        $check = array();

        $table_name = RevSliderGlobals::$table_sliders;

        //add themepunch default Sliders here

        $check = RevSliderDB::instance(array(
            'table'=>$table_name,
            'where'=>array('type','template')
        ))->get();

//        $sliders = apply_filters('revslider_set_template_sliders', $sliders);

        /**
         * Example
        $sliders['Slider Pack Name'] = array(
        array('title' => 'PJ Slider 1', 'alias' => 'pjslider1', 'width' => 1400, 'height' => 868, 'zip' => 'exwebproduct.zip', 'uid' => 'bde6d50c2f73f8086708878cf227c82b', 'installed' => false, 'img' => RS_PLUGIN_URL .'admin/assets/imports/exwebproduct.jpg'),
        array('title' => 'PJ Classic Slider', 'alias' => 'pjclassicslider', 'width' => 1240, 'height' => 600, 'zip' => 'classicslider.zip', 'uid' => 'a0d6a9248c9066b404ba0f1cdadc5cf2', 'installed' => false, 'img' => RS_PLUGIN_URL .'admin/assets/imports/classicslider.jpg')
        );
         **/

        if(!empty($check) && !empty($sliders)){
            foreach($sliders as $key => $the_sliders){
                foreach($the_sliders as $skey => $slider){
                    foreach($check as $ikey => $installed){
                        if($installed['alias'] == $slider['alias']){
                            $img = $slider['img'];
                            $sliders[$key][$skey] = $installed;

                            $sliders[$key][$skey]['img'] = $img;

                            $sliders[$key]['version'] = (isset($slider['version'])) ? $slider['version'] : '';
                            if(isset($slider['is_new'])) $sliders[$key]['is_new'] = true;

                            $preview = (isset($slider['preview'])) ? $slider['preview'] : false;
                            if($preview !== false) $sliders[$key]['preview'] = $preview;

                            break;
                        }
                    }
                }
            }
        }

        return $sliders;
    }
    public function getThemePunchTemplateDefaultSlides($slider_alias){

        $templates = RevSliderOptions::getOptions('rs-templates', array());
        $slides = (isset($templates['slides']) && !empty($templates['slides'])) ? $templates['slides'] : array();

        return (isset($slides[$slider_alias])) ? $slides[$slider_alias] : array();
    }
    public function getThemePunchTemplateSlides($sliders = false){

        $templates = array();

        $slide_defaults = array();//

        if($sliders == false){
            $sliders = $this->getThemePunchTemplateSliders();
        }
        $table_name = RevSliderGlobals::$table_slides;

        if(!empty($sliders)){
            foreach($sliders as $slider){
                $slides = $this->getThemePunchTemplateDefaultSlides($slider['alias']);

                if(!isset($slider['installed'])){
                    $add_templates = RevSliderDB::instance(array(
                        'table'=>$table_name,
                        'where'=>array('slider_id',$slider['id'])
                    ))->get();
                    $templates = array_merge($templates, $add_templates);
                }else{
                    $templates = array_merge($templates, $slides);
                }
                if(!empty($templates)){
                    foreach($templates as $key => $tmpl){
                        if(isset($slides[$key])) $templates[$key]['img'] = $slides[$key]['img'];
                    }
                }

                /*else{
                    $templates = array_merge($templates, array($slide_defaults[$slider['alias']]));
                }*/
            }
        }

        if(!empty($templates)){
            foreach($templates as $key => $template){
                if(!isset($template['installed'])){
                    $template['params'] = (isset($template['params'])) ? $template['params'] : '';
                    $template['layers'] = (isset($template['layers'])) ? $template['layers'] : '';
                    $template['settings'] = (isset($template['settings'])) ? $template['settings'] : '';

                    $templates[$key]['params'] = json_decode($template['params'], true);
                    //$templates[$key]['layers'] = json_decode($template['layers'], true);
                    $templates[$key]['settings'] = json_decode($template['settings'], true);

                    //$templates[$key][]
                    //add missing uid and zipname
                }
            }
        }

        return $templates;
    }
    public function getThemePunchTemplateSliders(){

        $table_name = RevSliderGlobals::$table_sliders;

        //add themepunch default Sliders here
        $sliders = RevSliderDB::instance(array(
            'table'=>$table_name,
            'where'=>array('type','template')
        ))->get();
        $defaults = RevSliderOptions::getOptions('rs-templates', array());
        $defaults = (isset($defaults['slider'])) ? $defaults['slider'] : array();

        if(!empty($sliders)){

            if(!empty($defaults)){
                foreach($defaults as $key => $slider){
                    foreach($sliders as $ikey => $installed){
                        if($installed['alias'] == $slider['alias']){
                            //check if $sliders has slides, if not, set for redownload by deleting Template Slider in table
                            $c_slides = $this->getThemePunchTemplateSlides(array($installed));
                            if(empty($c_slides)){
                                //delete slider in table
                                RevSliderDB::instance(array(
                                    'table'=>$table_name,
                                    'where'=>array('id',$installed['id'])
                                ))->delete();
                                break;
                            }

                            $img = $slider['img'];
                            $preview = (isset($slider['preview'])) ? $slider['preview'] : false;
                            $defaults[$key] = array_merge($defaults[$key], $installed);

                            unset($defaults[$key]['installed']);

                            $defaults[$key]['img'] = $img;
                            $defaults[$key]['version'] = $slider['version'];
                            $defaults[$key]['cat'] = $slider['cat'];
                            $defaults[$key]['filter'] = $slider['filter'];

                            if(isset($slider['is_new'])){
                                $defaults[$key]['is_new'] = true;
                                $defaults[$key]['width'] = $slider['width'];
                                $defaults[$key]['height'] = $slider['height'];
                            }
                            $defaults[$key]['zip'] = $slider['zip'];
                            $defaults[$key]['uid'] = $slider['uid'];

                            if(isset($slider['new_slider'])) $defaults[$key]['new_slider'] = $slider['new_slider'];

                            if($preview !== false) $defaults[$key]['preview'] = $preview;
                            break;
                        }
                    }
                }
                foreach($defaults as $dk => $di){ //check here if package parent needs to be set to installed, as all others
                    if(isset($di['package_parent']) && $di['package_parent'] == 'true'){
                        $full_installed = true;
                        foreach($defaults as $k => $ps){
                            if($dk !== $k && isset($ps['package_id']) && $ps['package_id'] === $di['package_id']){ //ignore comparing of the same, as it can never be installed
                                if(isset($ps['installed'])){
                                    $full_installed = false;
                                    break;
                                }
                            }
                        }

                        if($full_installed){
                            if(isset($defaults[$dk]['installed'])){
                                unset($defaults[$dk]['installed']);
                            }
                        }
                    }
                }
            }
        }

        krsort($defaults);

        return $defaults;
    }

}