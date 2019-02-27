<?php

/**
 * Created by FsFlex.
 * User: VH
 * Date: 7/13/2017
 * Time: 6:50 PM
 */
namespace Drupal\revslider\Controller\Handle;

use Drupal\revslider\Helper\RevSliderCssParser;
use Drupal\revslider\Helper\RevSliderFunctions;
use Drupal\revslider\Helper\RevSliderGlobals;
use Drupal\revslider\Helper\RevSliderHtml;
use Drupal\revslider\Helper\RevSliderWpml;
use Drupal\revslider\Model\Navigation;
use Drupal\revslider\Model\Operations;
use Drupal\revslider\Model\Slide;
use Drupal\revslider\Model\Slider;

class SlideEditor
{
    public function getArrInitData()
    {
        $slideID = RevSliderFunctions::getRequestVariable("id");

        if ($slideID == 'new') { //add new transparent slide
            $sID = intval(RevSliderFunctions::getRequestVariable("slider"));
            if ($sID > 0) {
                $revs = new Slider();
                $revs->initByID($sID);
                //check if we already have slides, if yes, go to first
                $arrS = $revs->getSlides(false);
                if (empty($arrS)) {
                    $slideID = $revs->createSlideFromData(array('sliderid' => $sID), true);
                } else {
                    $slideID = key($arrS);
                }
            }
        }

        $patternViewSlide = RevSliderFunctions::getViewUrl("slide",array('id'=>"[slideid]"));

//init slide object
        $slide = new Slide();
        $slide->initByID($slideID);

        $slideParams = $slide->getParams();

        $operations = new Operations();

        $rs_nav = new Navigation();
        $arr_navigations = $rs_nav->get_all_navigations();

//init slider object
        $sliderID = $slide->getSliderID();
        $slider = new Slider();
        $slider->initByID($sliderID);
        $sliderParams = $slider->getParams();
        $arrSlideNames = $slider->getArrSlideNames();

        $arrSlides = $slider->getSlides(false);
        $arrSlidesWPML = $slider->getSlidesWPML(false, $slide);

        $arrSliders = $slider->getArrSlidersShort($sliderID);
        $arrSlidersFull = $slider->getArrSlidersShort();
        $selectSliders = RevSliderHtml::getHTMLSelect($arrSliders, "", "id='selectSliders'", true);

//check if slider is template
        $sliderTemplate = $slider->getParam("template", "false");

//set slide delay
        $sliderDelay = $slider->getParam("delay", "9000");
        $slideDelay = $slide->getParam("delay", "");
        if (empty($slideDelay))
            $slideDelay = $sliderDelay;

//add tools.min.js
//        wp_enqueue_script('tp-tools', RS_PLUGIN_URL .'public/assets/js/jquery.themepunch.tools.min.js', array(), RevSliderGlobals::SLIDER_REVISION );

        $arrLayers = $slide->getLayers();

//set Layer settings
        $cssContent = $operations->getCaptionsContent();

        $arrCaptionClasses = $operations->getArrCaptionClasses($cssContent);
//$arrCaptionClassesSorted = $operations->getArrCaptionClasses($cssContent);
        $arrCaptionClassesSorted = RevSliderCssParser::get_captions_sorted();

        $arrFontFamily = $operations->getArrFontFamilys($slider);
        $arrCSS = $operations->getCaptionsContentArray();

        $arrAnim = $operations->getFullCustomAnimations();
        $arrAnimDefaultIn = $operations->getArrAnimations(false);
        $arrAnimDefaultOut = $operations->getArrEndAnimations(false);

        $arrAnimDefault = array_merge($arrAnimDefaultIn, $arrAnimDefaultOut);

//set various parameters needed for the page
        $width = $sliderParams["width"];
        $height = $sliderParams["height"];
        $imageUrl = $slide->getImageUrl();
        $imageID = $slide->getImageID();

        $slider_type = $slider->getParam('source_type', 'gallery');

        /**
         * Get Slider params which will be used as default on Slides
         * @since: 5.0
         **/
        $def_background_fit = $slider->getParam('def-background_fit', 'cover');
        $def_image_source_type = $slider->getParam('def-image_source_type', 'full');
        $def_bg_fit_x = $slider->getParam('def-bg_fit_x', '100');
        $def_bg_fit_y = $slider->getParam('def-bg_fit_y', '100');
        $def_bg_position = $slider->getParam('def-bg_position', 'center center');
        $def_bg_position_x = $slider->getParam('def-bg_position_x', '0');
        $def_bg_position_y = $slider->getParam('def-bg_position_y', '0');
        $def_bg_repeat = $slider->getParam('def-bg_repeat', 'no-repeat');
        $def_kenburn_effect = $slider->getParam('def-kenburn_effect', 'off');
        $def_kb_start_fit = $slider->getParam('def-kb_start_fit', '100');
        $def_kb_easing = $slider->getParam('def-kb_easing', 'Linear.easeNone');
        $def_kb_end_fit = $slider->getParam('def-kb_end_fit', '100');
        $def_kb_duration = $slider->getParam('def-kb_duration', '10000');
        $def_transition = $slider->getParam('def-slide_transition', 'fade');
        $def_transition_duration = $slider->getParam('def-transition_duration', 'default');

        $def_use_parallax = $slider->getParam('use_parallax', 'on');

        /* NEW KEN BURN INPUTS */
        $def_kb_start_offset_x = $slider->getParam('def-kb_start_offset_x', '0');
        $def_kb_blur_start = $slider->getParam('def-kb_blur_start', '0');
        $def_kb_blur_end = $slider->getParam('def-kb_blur_end', '0');
        $def_kb_start_offset_y = $slider->getParam('def-kb_start_offset_y', '0');
        $def_kb_end_offset_x = $slider->getParam('def-kb_end_offset_x', '0');
        $def_kb_end_offset_y = $slider->getParam('def-kb_end_offset_y', '0');
        $def_kb_start_rotate = $slider->getParam('def-kb_start_rotate', '0');
        $def_kb_end_rotate = $slider->getParam('def-kb_end_rotate', '0');
        /* END OF NEW KEN BURN INPUTS */

        $imageFilename = $slide->getImageFilename();

        $style = "height:" . $height . "px;"; //

        $divLayersWidth = "width:" . $width . "px;";
        $divbgminwidth = "min-width:" . $width . "px;";
        $maxbgwidth = "max-width:" . $width . "px;";

//set iframe parameters
        $iframeWidth = $width + 60;
        $iframeHeight = $height + 50;

        $iframeStyle = "width:" . $iframeWidth . "px;height:" . $iframeHeight . "px;";

        $closeUrl = RevSliderFunctions::getViewUrl(RevSliderGlobals::VIEW_SLIDES,array('id'=>$sliderID));

        $jsonLayers = RevSliderFunctions::jsonEncodeForClientSide($arrLayers);
        $jsonFontFamilys = RevSliderFunctions::jsonEncodeForClientSide($arrFontFamily);
        $jsonCaptions = RevSliderFunctions::jsonEncodeForClientSide($arrCaptionClassesSorted);

        $arrCssStyles = RevSliderFunctions::jsonEncodeForClientSide($arrCSS);

        $arrCustomAnim = RevSliderFunctions::jsonEncodeForClientSide($arrAnim);
        $arrCustomAnimDefault = RevSliderFunctions::jsonEncodeForClientSide($arrAnimDefault);

//bg type params
        $bgType = RevSliderFunctions::getVal($slideParams, 'background_type', 'image');
        $slideBGColor = RevSliderFunctions::getVal($slideParams, 'slide_bg_color', '#E7E7E7');
        $divLayersClass = "slide_layers";

        $meta_handle = RevSliderFunctions::getVal($slideParams, 'meta_handle', '');

        $bgFit = RevSliderFunctions::getVal($slideParams, 'bg_fit', $def_background_fit);
        $bgFitX = intval(RevSliderFunctions::getVal($slideParams, 'bg_fit_x', $def_bg_fit_x));
        $bgFitY = intval(RevSliderFunctions::getVal($slideParams, 'bg_fit_y', $def_bg_fit_y));

        $bgPosition = RevSliderFunctions::getVal($slideParams, 'bg_position', $def_bg_position);
        $bgPositionX = intval(RevSliderFunctions::getVal($slideParams, 'bg_position_x', $def_bg_position_x));
        $bgPositionY = intval(RevSliderFunctions::getVal($slideParams, 'bg_position_y', $def_bg_position_y));

        $slide_parallax_level = RevSliderFunctions::getVal($slideParams, 'slide_parallax_level', '-');
        $kenburn_effect = RevSliderFunctions::getVal($slideParams, 'kenburn_effect', $def_kenburn_effect);
        $kb_duration = RevSliderFunctions::getVal($slideParams, 'kb_duration', $def_kb_duration);
        $kb_easing = RevSliderFunctions::getVal($slideParams, 'kb_easing', $def_kb_easing);
        $kb_start_fit = RevSliderFunctions::getVal($slideParams, 'kb_start_fit', $def_kb_start_fit);
        $kb_end_fit = RevSliderFunctions::getVal($slideParams, 'kb_end_fit', $def_kb_end_fit);

        $ext_width = RevSliderFunctions::getVal($slideParams, 'ext_width', '1920');
        $ext_height = RevSliderFunctions::getVal($slideParams, 'ext_height', '1080');
        $use_parallax = RevSliderFunctions::getVal($slideParams, 'use_parallax', $def_use_parallax);

        $mediafilter = RevSliderFunctions::getVal($slideParams, 'media-filter-type', 'none');

        $parallax_level[] = RevSliderFunctions::getVal($sliderParams, "parallax_level_1", "5");
        $parallax_level[] = RevSliderFunctions::getVal($sliderParams, "parallax_level_2", "10");
        $parallax_level[] = RevSliderFunctions::getVal($sliderParams, "parallax_level_3", "15");
        $parallax_level[] = RevSliderFunctions::getVal($sliderParams, "parallax_level_4", "20");
        $parallax_level[] = RevSliderFunctions::getVal($sliderParams, "parallax_level_5", "25");
        $parallax_level[] = RevSliderFunctions::getVal($sliderParams, "parallax_level_6", "30");
        $parallax_level[] = RevSliderFunctions::getVal($sliderParams, "parallax_level_7", "35");
        $parallax_level[] = RevSliderFunctions::getVal($sliderParams, "parallax_level_8", "40");
        $parallax_level[] = RevSliderFunctions::getVal($sliderParams, "parallax_level_9", "45");
        $parallax_level[] = RevSliderFunctions::getVal($sliderParams, "parallax_level_10", "45");
        $parallax_level[] = RevSliderFunctions::getVal($sliderParams, "parallax_level_11", "46");
        $parallax_level[] = RevSliderFunctions::getVal($sliderParams, "parallax_level_12", "47");
        $parallax_level[] = RevSliderFunctions::getVal($sliderParams, "parallax_level_13", "48");
        $parallax_level[] = RevSliderFunctions::getVal($sliderParams, "parallax_level_14", "49");
        $parallax_level[] = RevSliderFunctions::getVal($sliderParams, "parallax_level_15", "50");
        $parallax_level[] = RevSliderFunctions::getVal($sliderParams, "parallax_level_16", "55");

        $parallaxisddd = RevSliderFunctions::getVal($sliderParams, "ddd_parallax", "off");
        $parallaxbgfreeze = RevSliderFunctions::getVal($sliderParams, "ddd_parallax_bgfreeze", "off");


        $slideBGYoutube = RevSliderFunctions::getVal($slideParams, 'slide_bg_youtube', '');
        $slideBGVimeo = RevSliderFunctions::getVal($slideParams, 'slide_bg_vimeo', '');
        $slideBGhtmlmpeg = RevSliderFunctions::getVal($slideParams, 'slide_bg_html_mpeg', '');
        $slideBGhtmlwebm = RevSliderFunctions::getVal($slideParams, 'slide_bg_html_webm', '');
        $slideBGhtmlogv = RevSliderFunctions::getVal($slideParams, 'slide_bg_html_ogv', '');

        $stream_do_cover = RevSliderFunctions::getVal($slideParams, 'stream_do_cover', 'on');
        $stream_do_cover_both = RevSliderFunctions::getVal($slideParams, 'stream_do_cover_both', 'on');

        $video_force_cover = RevSliderFunctions::getVal($slideParams, 'video_force_cover', 'on');
        $video_dotted_overlay = RevSliderFunctions::getVal($slideParams, 'video_dotted_overlay', 'none');
        $video_ratio = RevSliderFunctions::getVal($slideParams, 'video_ratio', 'none');
        $video_loop = RevSliderFunctions::getVal($slideParams, 'video_loop', 'none');
        $video_nextslide = RevSliderFunctions::getVal($slideParams, 'video_nextslide', 'off');
        $video_allowfullscreen = RevSliderFunctions::getVal($slideParams, 'video_allowfullscreen', 'on');
        $video_force_rewind = RevSliderFunctions::getVal($slideParams, 'video_force_rewind', 'on');
        $video_speed = RevSliderFunctions::getVal($slideParams, 'video_speed', '1');
        $video_mute = RevSliderFunctions::getVal($slideParams, 'video_mute', 'on');
        $video_volume = RevSliderFunctions::getVal($slideParams, 'video_volume', '100');
        $video_start_at = RevSliderFunctions::getVal($slideParams, 'video_start_at', '');
        $video_end_at = RevSliderFunctions::getVal($slideParams, 'video_end_at', '');
        $video_arguments = RevSliderFunctions::getVal($slideParams, 'video_arguments', RevSliderGlobals::DEFAULT_YOUTUBE_ARGUMENTS);
        $video_arguments_vim = RevSliderFunctions::getVal($slideParams, 'video_arguments_vimeo', RevSliderGlobals::DEFAULT_VIMEO_ARGUMENTS);

        /* NEW KEN BURN INPUTS */
        $kbStartOffsetX = intval(RevSliderFunctions::getVal($slideParams, 'kb_start_offset_x', $def_kb_start_offset_x));
        $kbStartOffsetY = intval(RevSliderFunctions::getVal($slideParams, 'kb_start_offset_y', $def_kb_start_offset_y));
        $kbEndOffsetX = intval(RevSliderFunctions::getVal($slideParams, 'kb_end_offset_x', $def_kb_end_offset_x));
        $kbEndOffsetY = intval(RevSliderFunctions::getVal($slideParams, 'kb_end_offset_y', $def_kb_end_offset_y));
        $kbStartRotate = intval(RevSliderFunctions::getVal($slideParams, 'kb_start_rotate', $def_kb_start_rotate));
        $kbEndRotate = intval(RevSliderFunctions::getVal($slideParams, 'kb_end_rotate', $def_kb_end_rotate));
        $kbBlurStart = intval(RevSliderFunctions::getVal($slideParams, 'kb_blur_start', $def_kb_blur_start));
        $kbBlurEnd = intval(RevSliderFunctions::getVal($slideParams, 'kb_blur_end', $def_kb_blur_end));
        /* END OF NEW KEN BURN INPUTS*/

        $bgRepeat = RevSliderFunctions::getVal($slideParams, 'bg_repeat', $def_bg_repeat);

        $slideBGExternal = RevSliderFunctions::getVal($slideParams, "slide_bg_external", "");

        $img_sizes = RevSliderFunctions::get_all_image_sizes($slider_type);

        $bg_image_size = RevSliderFunctions::getVal($slideParams, 'image_source_type', $def_image_source_type);

        $style_wrapper = '';
        $class_wrapper = '';


        switch ($bgType) {
            case "trans":
                $divLayersClass = "slide_layers";
                $class_wrapper = "trans_bg";
                break;
            case "solid":
                $style_wrapper .= "background-color:" . $slideBGColor . ";";
                break;
            case "image":
                switch ($slider_type) {
                    case 'posts':
                        $imageUrl = RevSliderFunctions::asset('public/assets/sources/post.png');
                        break;
                    case 'woocommerce':
                        $imageUrl = RevSliderFunctions::asset('public/assets/sources/wc.png');
                        break;
                    case 'facebook':
                        $imageUrl = RevSliderFunctions::asset('public/assets/sources/fb.png');
                        break;
                    case 'twitter':
                        $imageUrl = RevSliderFunctions::asset('public/assets/sources/tw.png');
                        break;
                    case 'instagram':
                        $imageUrl = RevSliderFunctions::asset('public/assets/sources/ig.png');
                        break;
                    case 'flickr':
                        $imageUrl = RevSliderFunctions::asset('public/assets/sources/fr.png');
                        break;
                    case 'youtube':
                        $imageUrl = RevSliderFunctions::asset('public/assets/sources/yt.png');
                        break;
                    case 'vimeo':
                        $imageUrl = RevSliderFunctions::asset('public/assets/sources/vm.png');
                        break;
                }
                $style_wrapper .= "background-image:url('" . $imageUrl . "');";
                if ($bgFit == 'percentage') {
                    $style_wrapper .= "background-size: " . $bgFitX . '% ' . $bgFitY . '%;';
                } else {
                    $style_wrapper .= "background-size: " . $bgFit . ";";
                }
                if ($bgPosition == 'percentage') {
                    $style_wrapper .= "background-position: " . $bgPositionX . '% ' . $bgPositionY . '%;';
                } else {
                    $style_wrapper .= "background-position: " . $bgPosition . ";";
                }
                $style_wrapper .= "background-repeat: " . $bgRepeat . ";";
                break;
            case "external":
                $style_wrapper .= "background-image:url('" . $slideBGExternal . "');";
                if ($bgFit == 'percentage') {
                    $style_wrapper .= "background-size: " . $bgFitX . '% ' . $bgFitY . '%;';
                } else {
                    $style_wrapper .= "background-size: " . $bgFit . ";";
                }
                if ($bgPosition == 'percentage') {
                    $style_wrapper .= "background-position: " . $bgPositionX . '% ' . $bgPositionY . '%;';
                } else {
                    $style_wrapper .= "background-position: " . $bgPosition . ";";
                }
                $style_wrapper .= "background-repeat: " . $bgRepeat . ";";
                break;
        }

        $slideTitle = $slide->getParam("title", "Slide");
        $slideOrder = $slide->getOrder();

//treat multilanguage
        $isWpmlExists = RevSliderWpml::isWpmlExists();
        $useWpml = $slider->getParam("use_wpml", "off");
        $wpmlActive = false;

        $jsonStaticLayers = "";
        if (!$slide->isStaticSlide()) {
            if ($isWpmlExists && $useWpml == "on") {
//                $wpmlActive = true;
//                $parentSlide = $slide->getParentSlide();
//                $arrChildLangs = $parentSlide->getArrChildrenLangs();
            }

            //get static slide, check all layers and add them to the action list
            $static_slide_id = $slide->getStaticSlideID($sliderID);

            if ($static_slide_id !== false) {
                $static_slide = new Slide();
                $static_slide->initByStaticID($static_slide_id);
                $static_layers = $static_slide->getLayers();
                $jsonStaticLayers = RevSliderFunctions::jsonEncodeForClientSide($static_layers);
            }
        }

// Set params to views twig
        $boolean_query = array(
            'slide_isStaticSlide'      => $slide->isStaticSlide(),
            'slider_isSlidesFromPosts' => $slider->isSlidesFromPosts()
        );
        $objvar = array(
            'slide' => array(
                'id' => $slide->getID()
            )
        );
       //$a =$img_sizes;

        $settings = $slide->getSettings();
        $enable_custom_size_notebook = $slider->getParam('enable_custom_size_notebook','off');
        $enable_custom_size_tablet = $slider->getParam('enable_custom_size_tablet','off');
        $enable_custom_size_iphone = $slider->getParam('enable_custom_size_iphone','off');
        $adv_resp_sizes = ($enable_custom_size_notebook == 'on' || $enable_custom_size_tablet == 'on' || $enable_custom_size_iphone == 'on') ? true : false;
        $url = array();

        $mslide_list = array();
        if(!empty($arrSlidesWPML)){
            foreach($arrSlidesWPML as $arwmpl) {
                if($arwmpl['id'] == $slideID) continue;

                $mslide_list[] = array($arwmpl['id'] => $arwmpl['title']);
            }
        }
        $global = array(
            'slide'=>array(
                'id'=>$slide->getID(),
                'isStaticSlide'=>$slide->isStaticSlide(),
            ),
            'slider'=>array(
                'id'=>$slider->getID(),
            ),
            'slideID'=>$slideID,
            'sliderID'=>$sliderID,
            'patternViewSlide'=>$patternViewSlide,
            'mslide_list'=>RevSliderFunctions::jsonEncodeForClientSide($mslide_list)
        );
        $response = compact(array('url','global'));
        $response = RevSliderFunctions::array_merge_multi_level($response,SectionVariable::slide_editor_scripts_library(array(
            'slider'=>$slider
        )));
        $response = RevSliderFunctions::array_merge_multi_level($response,SectionVariable::dialog_copy_move(array(
            'selectSliders'=>$selectSliders
        )));
        $response = RevSliderFunctions::array_merge_multi_level($response,SectionVariable::breadcrumbs(array(
            'sliderTemplate'=>$sliderTemplate,
            'sliderID'=>$sliderID,
            'slider'=>$slider,
            'slide'=>$slide
        )));
        $response = RevSliderFunctions::array_merge_multi_level($response,SectionVariable::slide_selector(array(
            "slideID"=>$slideID ,
            "slider"=>$slider ,
            "slide"=>$slide ,
            "slider_type"=>$slider_type ,
            "arrSlides"=>$arrSlides ,
        )));
        $response = RevSliderFunctions::array_merge_multi_level($response,SectionVariable::slide_general_settings(array(
            "arr_navigations" => $arr_navigations,
            "operations" => $operations,
            "slide" => $slide,
            "slider" => $slider,
            "slider_type" => $slider_type,
            "bg_image_size" => $bg_image_size,
            "slideParams" => $slideParams,
            "ext_width" => $ext_width,
            "ext_height" => $ext_height,
            "video_force_cover" => $video_force_cover,
            "video_dotted_overlay" => $video_dotted_overlay,
            "video_ratio" => $video_ratio,
            "video_start_at" => $video_start_at,
            "video_end_at" => $video_end_at,
            "video_loop" => $video_loop,
            "video_nextslide" => $video_nextslide,
            "video_force_rewind" => $video_force_rewind,
            "video_mute" => $video_mute,
            "video_volume" => $video_volume,
            "video_speed" => $video_speed,
            "video_arguments" => $video_arguments,
            "video_arguments_vim" => $video_arguments_vim,
            "bgFit" => $bgFit,
            "bgFitX" => $bgFitX,
            "bgFitY" => $bgFitY,
            "bgPosition" => $bgPosition,
            "bgPositionX" => $bgPositionX,
            "bgPositionY" => $bgPositionY,
            "bgRepeat" => $bgRepeat,
            "slide_parallax_level" => $slide_parallax_level,
            "parallax_level" => $parallax_level,
            "parallaxbgfreeze" => $parallaxbgfreeze,
            "parallaxisddd" => $parallaxisddd,
            "use_parallax" => $use_parallax,
            "mediafilter" => $mediafilter,
            "kenburn_effect" => $kenburn_effect,
            "kb_start_fit" => $kb_start_fit,
            "kb_end_fit" => $kb_end_fit,
            "kbStartOffsetX" => $kbStartOffsetX,
            "kbEndOffsetX" => $kbEndOffsetX,
            "kbStartOffsetY" => $kbStartOffsetY,
            "kbEndOffsetY" => $kbEndOffsetY,
            "kbStartRotate" => $kbStartRotate,
            "kbEndRotate" => $kbEndRotate,
            "kbBlurStart" => $kbBlurStart,
            "kbBlurEnd" => $kbBlurEnd,
            "kb_easing" => $kb_easing,
            "kb_duration" => $kb_duration,
            "imageUrl" => $imageUrl,
            "imageID" => $imageID,
            "def_transition" => $def_transition,
            "def_transition_duration" => $def_transition_duration,
            "img_sizes" => $img_sizes,
            'bgType'=>$bgType,
            'stream_do_cover'=>$stream_do_cover,
            'stream_do_cover_both'=>$stream_do_cover_both,
            'slideBGExternal'=>$slideBGExternal,
            'slideBGColor'=>$slideBGColor,
            'slideBGYoutube'=>$slideBGYoutube,
            'slideBGVimeo'=>$slideBGVimeo,
            'slideBGhtmlmpeg'=>$slideBGhtmlmpeg,
            'slideBGhtmlwebm'=>$slideBGhtmlwebm,
            'slideBGhtmlogv'=>$slideBGhtmlogv,
        )));
        $response = RevSliderFunctions::array_merge_multi_level($response,SectionVariable::html_id_esw(array(
            'tempwidth_jq' =>$maxbgwidth ,
            'kenburn_effect'=>  $kenburn_effect ,
            'slideDelay'=>$slideDelay ,
            'img_sizes'=>$img_sizes ,
            'slider_type'=>$slider_type ,
            'slide'=>$slide ,
            "jsonLayers" => $jsonLayers,
            "all_slides" => (!empty($all_slides)) ? $all_slides : '',
            "jsonStaticLayers" => $jsonStaticLayers,
            "jsonCaptions" => $jsonCaptions,
            "arrCustomAnim" => $arrCustomAnim,
            "arrCustomAnimDefault" => $arrCustomAnimDefault,
            "jsonFontFamilys" => $jsonFontFamilys,
            "arrCssStyles" => $arrCssStyles,
            'args_for_slide_stage'=>array(
                "operations" => $operations,
                "slide" => $slide,
                "use_parallax" => $use_parallax,
                "parallaxisddd" => $parallaxisddd,
                "parallax_level" => $parallax_level,
                "img_sizes" => $img_sizes,
                "slider" => $slider,
                "adv_resp_sizes" => $adv_resp_sizes,
                "enable_custom_size_notebook" => $enable_custom_size_notebook,
                "enable_custom_size_tablet" => $enable_custom_size_tablet,
                "enable_custom_size_iphone" => $enable_custom_size_iphone,
                "style" => $style,
                "maxbgwidth" => $maxbgwidth,
                "style_wrapper" => $style_wrapper,
                "settings" => $settings,
                "divbgminwidth" => $divbgminwidth,
                "class_wrapper" => $class_wrapper,
                "divLayersClass" => $divLayersClass,
                "divLayersWidth" => $divLayersWidth,
                "arrSlidersFull"=>$arrSlidersFull,
                "arrSlideNames"=>$arrSlideNames,

            )
        )));

        return $response;
    }
}
































