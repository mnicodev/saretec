<?php
/**
 * Created by FsFlex.
 * User: VH
 * Date: 7/11/2017
 * Time: 10:40 AM
 */

namespace Drupal\revslider\Model;


use Drupal\revslider\Helper\GoogleFonts;
use Drupal\revslider\Helper\RevSliderCssParser;
use Drupal\revslider\Helper\RevSliderDB;
use Drupal\revslider\Helper\RevSliderFunctions;
use Drupal\revslider\Helper\RevSliderGlobals;
use Drupal\revslider\Helper\RevSliderOptions;

class Operations extends RevSliderElementsBase
{
    private static $animations;
    private static $css;


    public static function add_preset_setting($data)
    {

        if (!isset($data['settings']) || !isset($data['values']))
            return t('Missing values to add preset');
        $customer_presets = RevSliderOptions::getOptions('revslider_presets', array());

        $data['settings']['custom'] = true;

        $customer_presets[] = array(
            'settings' => $data['settings'],
            'values'   => $data['values']
        );
        RevSliderOptions::updateOption('revslider_presets', $customer_presets, 'off');

        return true;
    }
    /**
     *
     * get animation params by id
     */
    public static function getFullCustomAnimationByID($id){
        if(empty(self::$animations)){
            self::fillAnimations();
        }

        foreach(self::$animations as $key => $value){
            if($value['id'] == $id){
                $customAnimations = array();
                $customAnimations['id'] = $value['id'];
                $customAnimations['handle'] = $value['handle'];
                $customAnimations['params'] = json_decode(str_replace("'", '"', $value['params']), true);
                return $customAnimations;
            }
        }

        return false;
    }
    public function getArrFontFamilys($slider = false)
    {

        //Web Safe Fonts
        $fonts = array(
            // GOOGLE Loaded Fonts
            array('type' => 'websafe', 'version' => t('Loaded Google Fonts'), 'label' => 'Dont Show Me'),

            //Serif Fonts
            array('type' => 'websafe', 'version' => t('Serif Fonts'), 'label' => 'Georgia, serif'),
            array('type' => 'websafe', 'version' => t('Serif Fonts'), 'label' => '"Palatino Linotype", "Book Antiqua", Palatino, serif'),
            array('type' => 'websafe', 'version' => t('Serif Fonts'), 'label' => '"Times New Roman", Times, serif'),

            //Sans-Serif Fonts
            array('type' => 'websafe', 'version' => t('Sans-Serif Fonts'), 'label' => 'Arial, Helvetica, sans-serif'),
            array('type' => 'websafe', 'version' => t('Sans-Serif Fonts'), 'label' => '"Arial Black", Gadget, sans-serif'),
            array('type' => 'websafe', 'version' => t('Sans-Serif Fonts'), 'label' => '"Comic Sans MS", cursive, sans-serif'),
            array('type' => 'websafe', 'version' => t('Sans-Serif Fonts'), 'label' => 'Impact, Charcoal, sans-serif'),
            array('type' => 'websafe', 'version' => t('Sans-Serif Fonts'), 'label' => '"Lucida Sans Unicode", "Lucida Grande", sans-serif'),
            array('type' => 'websafe', 'version' => t('Sans-Serif Fonts'), 'label' => 'Tahoma, Geneva, sans-serif'),
            array('type' => 'websafe', 'version' => t('Sans-Serif Fonts'), 'label' => '"Trebuchet MS", Helvetica, sans-serif'),
            array('type' => 'websafe', 'version' => t('Sans-Serif Fonts'), 'label' => 'Verdana, Geneva, sans-serif'),

            //Monospace Fonts
            array('type' => 'websafe', 'version' => t('Monospace Fonts'), 'label' => '"Courier New", Courier, monospace'),
            array('type' => 'websafe', 'version' => t('Monospace Fonts'), 'label' => '"Lucida Console", Monaco, monospace')
        );

        /*if($slider !== false){
            $font_custom = $slider->getParam("google_font","");

            if(!is_array($font_custom)) $font_custom = array($font_custom); //backwards compability

            if(is_array($font_custom)){
                foreach($font_custom as $key => $curFont){
                    $font = $this->cleanFontStyle(stripslashes($curFont));

                    if($font != false)
                        $font_custom[$key] = array('version' => t('Depricated Google Fonts', 'revslider'), 'label' => $font);
                    else
                        unset($font_custom[$key]);
                }
                $fonts = array_merge($font_custom, $fonts);
            }
        }*/

        $googlefonts = GoogleFonts::getFont();

        foreach ($googlefonts as $f => $val) {
            $fonts[] = array('type' => 'googlefont', 'version' => t('Google Fonts'), 'label' => $f, 'variants' => $val['variants'], 'subsets' => $val['subsets']);
        }

//        return apply_filters('revslider_operations_getArrFontFamilys', $fonts);
        return $fonts;
    }

    public function deleteCaptionsContentData($handle)
    {

        RevSliderDB::instance(array(
            'table' => RevSliderGlobals::$table_css,
            'where' => array('handle', ".tp-caption." . $handle)
        ))->delete();
        //$this->updateDynamicCaptions();

        //output captions array
        $arrCaptions = RevSliderCssParser::get_captions_sorted();

        return ($arrCaptions);
    }

    public function updateGeneralSettings($data)
    {

        $strSettings = serialize($data);
        RevSliderOptions::updateOption('revslider-global-settings', $data);

    }

    public static function update_preset_setting($data)
    {

        if (!isset($data['name']) || !isset($data['values']))
            return t('Missing values to update preset');

        $customer_presets = RevSliderOptions::getOptions('revslider_presets', array());

        if (!empty($customer_presets)) {
            foreach ($customer_presets as $key => $preset) {
                if ($preset['settings']['name'] == $data['name']) {
                    $customer_presets[$key]['values'] = $data['values'];
                    break;
                }
            }
        }

        RevSliderOptions::updateOption('revslider_presets', $customer_presets, 'off');

        return true;
    }

    public function renameCaption($content)
    {
        if (isset($content['old_name']) && isset($content['new_name'])) {

            $handle = $content['old_name'];

            $arrUpdate = array();
            $arrUpdate["handle"] = '.tp-caption.' . $content['new_name'];
            $result = RevSliderDB::instance(array(
                'table' => RevSliderGlobals::$table_css,
                'where' => array('handle', '.tp-caption.' . $handle)
            ))->update($arrUpdate);
            if ($result !== false) { //rename all layers in all Sliders that use this old name with the new name
                $slider = new Slider();
                $arrSliders = $slider->getArrSliders();
                if (!empty($arrSliders)) {
                    foreach ($arrSliders as $slider) {
                        $arrSildes = $slider->getSlides();
                        foreach ($arrSildes as $slide) {
                            $slide->replaceCssClass($content['old_name'], $content['new_name']);
                        }
                    }
                }
            }
        }

        //output captions array
        $arrCaptions = RevSliderCssParser::get_captions_sorted();
        return ($arrCaptions);
    }

    public static function remove_preset_setting($data)
    {

        if (!isset($data['name']))
            return t('Missing values to remove preset');

        $customer_presets = RevSliderOptions::getOptions('revslider_presets', array());

        if (!empty($customer_presets)) {
            foreach ($customer_presets as $key => $preset) {
                if ($preset['settings']['name'] == $data['name']) {
                    unset($customer_presets[$key]);
                    break;
                }
            }
        }

        RevSliderOptions::updateOption('revslider_presets', $customer_presets, 'off');

        return true;
    }

    public function updateAdvancedCssData($data)
    {
        if (!isset($data['handle']) || !isset($data['styles']) || !isset($data['type'])) return false;
        if ($data['type'] !== 'idle' && $data['type'] !== 'hover') return false;


        //get current styles
        $styles = RevSliderDB::instance(array(
            'table' => RevSliderGlobals::$table_css,
            'where' => array('handle', $data['handle'])
        ))->first();

        if (!empty($styles)) {
            if (!isset($styles['advanced'])) $styles['advanced'] = '';

            $adv = json_decode(str_replace("'", '"', $styles['advanced']), true);

            if (!isset($adv['idle'])) $adv['idle'] = array();
            if (!isset($adv['hover'])) $adv['hover'] = array();

            $adv[$data['type']] = $data['styles'];


            $arrUpdate = array();

            $arrUpdate['advanced'] = json_encode(str_replace("'", '"', $adv));

            $result = RevSliderDB::instance(array(
                'table' => RevSliderGlobals::$table_css,
                'where' => array('handle', $data['handle'])
            ))->update($arrUpdate);
            //output captions array
            $arrCaptions = RevSliderCssParser::get_captions_sorted();

            return ($arrCaptions);

        } else {
            return false;
        }

    }

    public function updateCaptionsContentData($content)
    {

        if (!isset($content['handle']) || !isset($content['idle']) || !isset($content['hover'])) return false; // || !isset($content['advanced'])

        //first get single entry to merge settings

        $styles = RevSliderDB::instance(array(
            'table' => RevSliderGlobals::$table_css,
            'where' => array('handle', '.tp-caption.' . $content['handle'])
        ))->first();

        if (empty($styles)) return false;

        $settings = json_decode(str_replace("'", '"', $styles['settings']), true);
        if (isset($content['settings']) && !empty($content['settings'])) {
            foreach ($content['settings'] as $key => $value) {
                $settings[$key] = $value;
            }
            //$settings = array_merge($content['settings'], $settings);
        }

        $handle = $content['handle'];

        if (!isset($content['idle'])) $content['idle'] = '';
        if (!isset($content['hover'])) $content['hover'] = '';
        if (!isset($content['advanced'])) $content['advanced'] = array();
        if (!isset($content['advanced']['idle'])) $content['advanced']['idle'] = array();
        if (!isset($content['advanced']['hover'])) $content['advanced']['hover'] = array();

        $arrUpdate = array();
        $arrUpdate["params"] = stripslashes(json_encode(str_replace("'", '"', $content['idle'])));
        $arrUpdate["hover"] = stripslashes(json_encode(str_replace("'", '"', $content['hover'])));
        $arrUpdate["settings"] = stripslashes(json_encode(str_replace("'", '"', $settings)));

        $arrUpdate["advanced"] = array();
        $arrUpdate["advanced"]['idle'] = $content['advanced']['idle'];
        $arrUpdate["advanced"]['hover'] = $content['advanced']['hover'];
        $arrUpdate["advanced"] = stripslashes(json_encode(str_replace("'", '"', $arrUpdate["advanced"])));

        $result = RevSliderDB::instance(array(
            'table' => RevSliderGlobals::$table_css,
            'where' => array('handle', '.tp-caption.' . $handle)
        ))->update($arrUpdate);
        //output captions array
        $arrCaptions = RevSliderCssParser::get_captions_sorted();

        return ($arrCaptions);
    }

    public function insertCaptionsContentData($content)
    {

        if (!isset($content['handle']) || !isset($content['idle']) || !isset($content['hover'])) return false; // || !isset($content['advanced'])

        $handle = $content['handle'];

        if (!isset($content['hover'])) $content['hover'] = '';
        if (!isset($content['advanced'])) $content['advanced'] = array();
        if (!isset($content['advanced']['idle'])) $content['advanced']['idle'] = array();
        if (!isset($content['advanced']['hover'])) $content['advanced']['hover'] = array();

        $arrInsert = array();
        $arrInsert["handle"] = '.tp-caption.' . $handle;
        $arrInsert["params"] = stripslashes(json_encode(str_replace("'", '"', $content['idle'])));
        $arrInsert["hover"] = stripslashes(json_encode(str_replace("'", '"', $content['hover'])));

        if (!isset($content['settings'])) $content['settings'] = array();
        $content['settings']['version'] = 'custom';
        $content['settings']['translated'] = '5'; // translated to version 5 currently
        $arrInsert["settings"] = stripslashes(json_encode(str_replace("'", '"', $content['settings'])));

        $arrInsert["advanced"] = array();
        $arrInsert["advanced"]['idle'] = $content['advanced']['idle'];
        $arrInsert["advanced"]['hover'] = $content['advanced']['hover'];
        $arrInsert["advanced"] = stripslashes(json_encode(str_replace("'", '"', $arrInsert["advanced"])));

        $result = RevSliderDB::instance(array(
            'table' => RevSliderGlobals::$table_css
        ))->insert($arrInsert);
        //output captions array
        $arrCaptions = RevSliderCssParser::get_captions_sorted();

        return ($arrCaptions);
    }

    public function getCaptionsContent()
    {

        if (empty(self::$css)) {
            self::fillCSS();
        }

        $result = self::$css;
        $contentCSS = RevSliderCssParser::parseDbArrayToCss($result);
        return ($contentCSS);
    }

    public function getArrEasing()
    { //true

        $arrEasing = array(
            "Linear.easeNone"   => "Linear.easeNone",
            "Power0.easeIn"     => "Power0.easeIn  (linear)",
            "Power0.easeInOut"  => "Power0.easeInOut  (linear)",
            "Power0.easeOut"    => "Power0.easeOut  (linear)",
            "Power1.easeIn"     => "Power1.easeIn",
            "Power1.easeInOut"  => "Power1.easeInOut",
            "Power1.easeOut"    => "Power1.easeOut",
            "Power2.easeIn"     => "Power2.easeIn",
            "Power2.easeInOut"  => "Power2.easeInOut",
            "Power2.easeOut"    => "Power2.easeOut",
            "Power3.easeIn"     => "Power3.easeIn",
            "Power3.easeInOut"  => "Power3.easeInOut",
            "Power3.easeOut"    => "Power3.easeOut",
            "Power4.easeIn"     => "Power4.easeIn",
            "Power4.easeInOut"  => "Power4.easeInOut",
            "Power4.easeOut"    => "Power4.easeOut",
            "Quad.easeIn"       => "Quad.easeIn  (same as Power1.easeIn)",
            "Quad.easeInOut"    => "Quad.easeInOut  (same as Power1.easeInOut)",
            "Quad.easeOut"      => "Quad.easeOut  (same as Power1.easeOut)",
            "Cubic.easeIn"      => "Cubic.easeIn  (same as Power2.easeIn)",
            "Cubic.easeInOut"   => "Cubic.easeInOut  (same as Power2.easeInOut)",
            "Cubic.easeOut"     => "Cubic.easeOut  (same as Power2.easeOut)",
            "Quart.easeIn"      => "Quart.easeIn  (same as Power3.easeIn)",
            "Quart.easeInOut"   => "Quart.easeInOut  (same as Power3.easeInOut)",
            "Quart.easeOut"     => "Quart.easeOut  (same as Power3.easeOut)",
            "Quint.easeIn"      => "Quint.easeIn  (same as Power4.easeIn)",
            "Quint.easeInOut"   => "Quint.easeInOut  (same as Power4.easeInOut)",
            "Quint.easeOut"     => "Quint.easeOut  (same as Power4.easeOut)",
            "Strong.easeIn"     => "Strong.easeIn  (same as Power4.easeIn)",
            "Strong.easeInOut"  => "Strong.easeInOut  (same as Power4.easeInOut)",
            "Strong.easeOut"    => "Strong.easeOut  (same as Power4.easeOut)",
            "Back.easeIn"       => "Back.easeIn",
            "Back.easeInOut"    => "Back.easeInOut",
            "Back.easeOut"      => "Back.easeOut",
            "Bounce.easeIn"     => "Bounce.easeIn",
            "Bounce.easeInOut"  => "Bounce.easeInOut",
            "Bounce.easeOut"    => "Bounce.easeOut",
            "Circ.easeIn"       => "Circ.easeIn",
            "Circ.easeInOut"    => "Circ.easeInOut",
            "Circ.easeOut"      => "Circ.easeOut",
            "Elastic.easeIn"    => "Elastic.easeIn",
            "Elastic.easeInOut" => "Elastic.easeInOut",
            "Elastic.easeOut"   => "Elastic.easeOut",
            "Expo.easeIn"       => "Expo.easeIn",
            "Expo.easeInOut"    => "Expo.easeInOut",
            "Expo.easeOut"      => "Expo.easeOut",
            "Sine.easeIn"       => "Sine.easeIn",
            "Sine.easeInOut"    => "Sine.easeInOut",
            "Sine.easeOut"      => "Sine.easeOut",
            "SlowMo.ease"       => "SlowMo.ease",
            //add old easings //From here on display none
            "easeOutBack"       => "easeOutBack",
            "easeInQuad"        => "easeInQuad",
            "easeOutQuad"       => "easeOutQuad",
            "easeInOutQuad"     => "easeInOutQuad",
            "easeInCubic"       => "easeInCubic",
            "easeOutCubic"      => "easeOutCubic",
            "easeInOutCubic"    => "easeInOutCubic",
            "easeInQuart"       => "easeInQuart",
            "easeOutQuart"      => "easeOutQuart",
            "easeInOutQuart"    => "easeInOutQuart",
            "easeInQuint"       => "easeInQuint",
            "easeOutQuint"      => "easeOutQuint",
            "easeInOutQuint"    => "easeInOutQuint",
            "easeInSine"        => "easeInSine",
            "easeOutSine"       => "easeOutSine",
            "easeInOutSine"     => "easeInOutSine",
            "easeInExpo"        => "easeInExpo",
            "easeOutExpo"       => "easeOutExpo",
            "easeInOutExpo"     => "easeInOutExpo",
            "easeInCirc"        => "easeInCirc",
            "easeOutCirc"       => "easeOutCirc",
            "easeInOutCirc"     => "easeInOutCirc",
            "easeInElastic"     => "easeInElastic",
            "easeOutElastic"    => "easeOutElastic",
            "easeInOutElastic"  => "easeInOutElastic",
            "easeInBack"        => "easeInBack",
            "easeInOutBack"     => "easeInOutBack",
            "easeInBounce"      => "easeInBounce",
            "easeOutBounce"     => "easeOutBounce",
            "easeInOutBounce"   => "easeInOutBounce",
        );

        return ($arrEasing);
    }

    public function getArrEndEasing()
    {
        $arrEasing = $this->getArrEasing();
        $arrEasing = array_merge(array("nothing" => "No Change"), $arrEasing);

        return ($arrEasing);
    }

    public function modifyCustomSliderParams($data)
    {

        $arrNames = array("width", "height",
            "responsitive_w1", "responsitive_sw1",
            "responsitive_w2", "responsitive_sw2",
            "responsitive_w3", "responsitive_sw3",
            "responsitive_w4", "responsitive_sw4",
            "responsitive_w5", "responsitive_sw5",
            "responsitive_w6", "responsitive_sw6");

        $arrMain = $data["main"];
        foreach ($arrNames as $name) {
            if (array_key_exists($name, $arrMain)) {
                $arrMain[$name] = floatval($arrMain[$name]);
                if (!is_numeric($arrMain[$name])) $arrMain[$name] = 0;
            }
        }

        if (!isset($arrMain["fullscreen_offset_container"]))
            $arrMain["fullscreen_offset_container"] = '';
        $data["main"] = $arrMain;

        return ($data);
    }

    public function getArrCaptionClasses($contentCSS)
    {
        //parse css captions file
        $parser = new RevSliderCssParser();
        $parser->initContent($contentCSS);
        $arrCaptionClasses = $parser->getArrClasses('', '', true);

        return ($arrCaptionClasses);
    }

    public function getArrTransition()
    {

        $arrTransition = array(
            "notselectable1"         => "BASICS",
            "notransition"           => "No Transition",
            "fade"                   => "Fade",
            "crossfade"              => "Fade Cross",
            "fadethroughdark"        => "Fade Through Black",
            "fadethroughlight"       => "Fade Through Light",
            "fadethroughtransparent" => "Fade Through Transparent",

            "notselectable2"  => "SLIDE SIMPLE",
            "slideup"         => "Slide To Top",
            "slidedown"       => "Slide To Bottom",
            "slideright"      => "Slide To Right",
            "slideleft"       => "Slide To Left",
            "slidehorizontal" => "Slide Horizontal (Next/Previous)",
            "slidevertical"   => "Slide Vertical (Next/Previous)",

            "notselectable21"     => "SLIDE OVER",
            "slideoverup"         => "Slide Over To Top",
            "slideoverdown"       => "Slide Over To Bottom",
            "slideoverright"      => "Slide Over To Right",
            "slideoverleft"       => "Slide Over To Left",
            "slideoverhorizontal" => "Slide Over Horizontal (Next/Previous)",
            "slideoververtical"   => "Slide Over Vertical (Next/Previous)",

            "notselectable22"       => "SLIDE REMOVE",
            "slideremoveup"         => "Slide Remove To Top",
            "slideremovedown"       => "Slide Remove To Bottom",
            "slideremoveright"      => "Slide Remove To Right",
            "slideremoveleft"       => "Slide Remove To Left",
            "slideremovehorizontal" => "Slide Remove Horizontal (Next/Previous)",
            "slideremovevertical"   => "Slide Remove Vertical (Next/Previous)",

            "notselectable26"          => "SLIDING OVERLAYS",
            "slidingoverlayup"         => "Sliding Overlays To Top",
            "slidingoverlaydown"       => "Sliding Overlays To Bottom",
            "slidingoverlayright"      => "Sliding Overlays To Right",
            "slidingoverlayleft"       => "Sliding Overlays To Left",
            "slidingoverlayhorizontal" => "Sliding Overlays Horizontal (Next/Previous)",
            "slidingoverlayvertical"   => "Sliding Overlays Vertical (Next/Previous)",

            "notselectable23"      => "SLOTS AND BOXES",
            "boxslide"             => "Slide Boxes",
            "slotslide-horizontal" => "Slide Slots Horizontal",
            "slotslide-vertical"   => "Slide Slots Vertical",
            "boxfade"              => "Fade Boxes",
            "slotfade-horizontal"  => "Fade Slots Horizontal",
            "slotfade-vertical"    => "Fade Slots Vertical",

            "notselectable31"         => "FADE & SLIDE",
            "fadefromright"           => "Fade and Slide from Right",
            "fadefromleft"            => "Fade and Slide from Left",
            "fadefromtop"             => "Fade and Slide from Top",
            "fadefrombottom"          => "Fade and Slide from Bottom",
            "fadetoleftfadefromright" => "To Left From Right",
            "fadetorightfadefromleft" => "To Right From Left",
            "fadetotopfadefrombottom" => "To Top From Bottom",
            "fadetobottomfadefromtop" => "To Bottom From Top",

            "notselectable4"     => "PARALLAX",
            "parallaxtoright"    => "Parallax to Right",
            "parallaxtoleft"     => "Parallax to Left",
            "parallaxtotop"      => "Parallax to Top",
            "parallaxtobottom"   => "Parallax to Bottom",
            "parallaxhorizontal" => "Parallax Horizontal",
            "parallaxvertical"   => "Parallax Vertical",

            "notselectable5"      => "ZOOM TRANSITIONS",
            "scaledownfromright"  => "Zoom Out and Fade From Right",
            "scaledownfromleft"   => "Zoom Out and Fade From Left",
            "scaledownfromtop"    => "Zoom Out and Fade From Top",
            "scaledownfrombottom" => "Zoom Out and Fade From Bottom",
            "zoomout"             => "ZoomOut",
            "zoomin"              => "ZoomIn",
            "slotzoom-horizontal" => "Zoom Slots Horizontal",
            "slotzoom-vertical"   => "Zoom Slots Vertical",

            "notselectable6" => "CURTAIN TRANSITIONS",
            "curtain-1"      => "Curtain from Left",
            "curtain-2"      => "Curtain from Right",
            "curtain-3"      => "Curtain from Middle",

            "notselectable8"  => "FILTER TRANSITIONS",
            "grayscale"       => "Grayscale Transition",
            "grayscalecross"  => "Grayscale Cross Transition",
            "brightness"      => "Brightness Transition",
            "brightnesscross" => "Brightness Cross Transition",
            "blurlight"       => "Blur Light Transition",
            "blurlightcross"  => "Blur Light Cross Transition",
            "blurstrong"      => "Blur Strong Transition",
            "blurstrongcross" => "Blur Strong Cross Transition",


            "notselectable7"       => "PREMIUM TRANSITIONS",
            "3dcurtain-horizontal" => "3D Curtain Horizontal",
            "3dcurtain-vertical"   => "3D Curtain Vertical",
            "cube"                 => "Cube Vertical",
            "cube-horizontal"      => "Cube Horizontal",
            "incube"               => "In Cube Vertical",
            "incube-horizontal"    => "In Cube Horizontal",
            "turnoff"              => "TurnOff Horizontal",
            "turnoff-vertical"     => "TurnOff Vertical",
            "papercut"             => "Paper Cut",
            "flyin"                => "Fly In",

            "notselectable1a" => "RANDOM",
            "random-selected" => "Random of Selected",
            "random-static"   => "Random Flat",
            "random-premium"  => "Random Premium",
            "random"          => "Random Flat and Premium"
        );

        return ($arrTransition);
    }

    public static function get_preset_settings()
    {
        $presets = array();

        //ThemePunch default presets are added here directly

        //preset -> standardpreset || heropreset || carouselpreset

        $presets[] = array(
            'settings' => array('class' => '', 'image' => RevSliderFunctions::asset('') . 'admin/images/sliderpresets/slideshow_auto_layout.png', 'name' => 'Slideshow-Auto', 'preset' => 'standardpreset'),
            'values'   =>
                array(
                    'next_slide_on_window_focus'  => 'off',
                    'delay'                       => '9000',
                    'start_js_after_delay'        => '0',
                    'image_source_type'           => 'full',
                    0                             => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                    'start_with_slide'            => '1',
                    'stop_on_hover'               => 'on',
                    'stop_slider'                 => 'off',
                    'stop_after_loops'            => '0',
                    'stop_at_slide'               => '1',
                    'shuffle'                     => 'off',
                    'viewport_start'              => 'wait',
                    'viewport_area'               => '80',
                    'enable_progressbar'          => 'on',
                    'background_dotted_overlay'   => 'none',
                    'background_color'            => 'transparent',
                    'padding'                     => '0',
                    'show_background_image'       => 'off',
                    'background_image'            => '',
                    'bg_fit'                      => 'cover',
                    'bg_repeat'                   => 'no-repeat',
                    'bg_position'                 => 'center center',
                    'position'                    => 'center',
                    'use_spinner'                 => '-1',
                    'spinner_color'               => '#FFFFFF',
                    'enable_arrows'               => 'on',
                    'navigation_arrow_style'      => 'round',
                    'arrows_always_on'            => 'true',
                    'hide_arrows'                 => '200',
                    'hide_arrows_mobile'          => '1200',
                    'hide_arrows_on_mobile'       => 'on',
                    'arrows_under_hidden'         => '600',
                    'hide_arrows_over'            => 'off',
                    'arrows_over_hidden'          => '0',
                    'leftarrow_align_hor'         => 'left',
                    'leftarrow_align_vert'        => 'center',
                    'leftarrow_offset_hor'        => '30',
                    'leftarrow_offset_vert'       => '0',
                    'rightarrow_align_hor'        => 'right',
                    'rightarrow_align_vert'       => 'center',
                    'rightarrow_offset_hor'       => '30',
                    'rightarrow_offset_vert'      => '0',
                    'enable_bullets'              => 'on',
                    'navigation_bullets_style'    => 'round-old',
                    'bullets_space'               => '5',
                    'bullets_direction'           => 'horizontal',
                    'bullets_always_on'           => 'true',
                    'hide_bullets'                => '200',
                    'hide_bullets_mobile'         => '1200',
                    'hide_bullets_on_mobile'      => 'on',
                    'bullets_under_hidden'        => '600',
                    'hide_bullets_over'           => 'off',
                    'bullets_over_hidden'         => '0',
                    'bullets_align_hor'           => 'center',
                    'bullets_align_vert'          => 'bottom',
                    'bullets_offset_hor'          => '0',
                    'bullets_offset_vert'         => '30',
                    'enable_thumbnails'           => 'off',
                    'thumbnails_padding'          => '5',
                    'span_thumbnails_wrapper'     => 'off',
                    'thumbnails_wrapper_color'    => 'transparent',
                    'thumbnails_wrapper_opacity'  => '100',
                    'thumbnails_style'            => 'round',
                    'thumb_amount'                => '5',
                    'thumbnails_space'            => '5',
                    'thumbnail_direction'         => 'horizontal',
                    'thumb_width'                 => '100',
                    'thumb_height'                => '50',
                    'thumb_width_min'             => '100',
                    'thumbs_always_on'            => 'false',
                    'hide_thumbs'                 => '200',
                    'hide_thumbs_mobile'          => '1200',
                    'hide_thumbs_on_mobile'       => 'off',
                    'thumbs_under_hidden'         => '0',
                    'hide_thumbs_over'            => 'off',
                    'thumbs_over_hidden'          => '0',
                    'thumbnails_inner_outer'      => 'inner',
                    'thumbnails_align_hor'        => 'center',
                    'thumbnails_align_vert'       => 'bottom',
                    'thumbnails_offset_hor'       => '0',
                    'thumbnails_offset_vert'      => '20',
                    'enable_tabs'                 => 'off',
                    'tabs_padding'                => '5',
                    'span_tabs_wrapper'           => 'off',
                    'tabs_wrapper_color'          => 'transparent',
                    'tabs_wrapper_opacity'        => '5',
                    'tabs_style'                  => '',
                    'tabs_amount'                 => '5',
                    'tabs_space'                  => '5',
                    'tabs_direction'              => 'horizontal',
                    'tabs_width'                  => '100',
                    'tabs_height'                 => '50',
                    'tabs_width_min'              => '100',
                    'tabs_always_on'              => 'false',
                    'hide_tabs'                   => '200',
                    'hide_tabs_mobile'            => '1200',
                    'hide_tabs_on_mobile'         => 'off',
                    'tabs_under_hidden'           => '0',
                    'hide_tabs_over'              => 'off',
                    'tabs_over_hidden'            => '0',
                    'tabs_inner_outer'            => 'inner',
                    'tabs_align_hor'              => 'center',
                    'tabs_align_vert'             => 'bottom',
                    'tabs_offset_hor'             => '0',
                    'tabs_offset_vert'            => '20',
                    'touchenabled'                => 'on',
                    'drag_block_vertical'         => 'off',
                    'swipe_velocity'              => '75',
                    'swipe_min_touches'           => '50',
                    'swipe_direction'             => 'horizontal',
                    'keyboard_navigation'         => 'off',
                    'keyboard_direction'          => 'horizontal',
                    'mousescroll_navigation'      => 'off',
                    'carousel_infinity'           => 'off',
                    'carousel_space'              => '0',
                    'carousel_borderr'            => '0',
                    'carousel_borderr_unit'       => 'px',
                    'carousel_padding_top'        => '0',
                    'carousel_padding_bottom'     => '0',
                    'carousel_maxitems'           => '3',
                    'carousel_stretch'            => 'off',
                    'carousel_fadeout'            => 'on',
                    'carousel_varyfade'           => 'off',
                    'carousel_rotation'           => 'off',
                    'carousel_varyrotate'         => 'off',
                    'carousel_maxrotation'        => '0',
                    'carousel_scale'              => 'off',
                    'carousel_varyscale'          => 'off',
                    'carousel_scaledown'          => '50',
                    'carousel_hposition'          => 'center',
                    'carousel_vposition'          => 'center',
                    'use_parallax'                => 'on',
                    'disable_parallax_mobile'     => 'off',
                    'parallax_type'               => 'mouse',
                    'parallax_origo'              => 'slidercenter',
                    'parallax_speed'              => '2000',
                    'parallax_level_1'            => '2',
                    'parallax_level_2'            => '3',
                    'parallax_level_3'            => '4',
                    'parallax_level_4'            => '5',
                    'parallax_level_5'            => '6',
                    'parallax_level_6'            => '7',
                    'parallax_level_7'            => '12',
                    'parallax_level_8'            => '16',
                    'parallax_level_9'            => '10',
                    'parallax_level_10'           => '50',
                    'lazy_load_type'              => 'smart',
                    'seo_optimization'            => 'none',
                    'simplify_ie8_ios4'           => 'off',
                    'show_alternative_type'       => 'off',
                    'show_alternate_image'        => '',
                    'jquery_noconflict'           => 'off',
                    'js_to_body'                  => 'false',
                    'output_type'                 => 'none',
                    'jquery_debugmode'            => 'off',
                    'slider_type'                 => 'auto',
                    'width'                       => '1240',
                    'width_notebook'              => '1024',
                    'width_tablet'                => '778',
                    'width_mobile'                => '480',
                    'height'                      => '600',
                    'height_notebook'             => '600',
                    'height_tablet'               => '500',
                    'height_mobile'               => '400',
                    'enable_custom_size_notebook' => 'on',
                    'enable_custom_size_tablet'   => 'on',
                    'enable_custom_size_iphone'   => 'on',
                    'main_overflow_hidden'        => 'off',
                    'auto_height'                 => 'off',
                    'min_height'                  => '',
                    'custom_javascript'           => '',
                    'custom_css'                  => '',
                ),
        );

        $presets[] = array(
            'settings' =>
                array('class' => '', 'image' => RevSliderFunctions::asset('') . 'admin/images/sliderpresets/slideshow_auto_layout.png', 'name' => 'Slideshow-Full-Width', 'preset' => 'standardpreset'),
            'values'   =>
                array(
                    'next_slide_on_window_focus'  => 'off',
                    'delay'                       => '9000',
                    'start_js_after_delay'        => '0',
                    'image_source_type'           => 'full',
                    0                             => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                    'start_with_slide'            => '1',
                    'stop_on_hover'               => 'on',
                    'stop_slider'                 => 'off',
                    'stop_after_loops'            => '0',
                    'stop_at_slide'               => '1',
                    'shuffle'                     => 'off',
                    'viewport_start'              => 'wait',
                    'viewport_area'               => '80',
                    'enable_progressbar'          => 'on',
                    'background_dotted_overlay'   => 'none',
                    'background_color'            => 'transparent',
                    'padding'                     => '0',
                    'show_background_image'       => 'off',
                    'background_image'            => '',
                    'bg_fit'                      => 'cover',
                    'bg_repeat'                   => 'no-repeat',
                    'bg_position'                 => 'center center',
                    'position'                    => 'center',
                    'use_spinner'                 => '-1',
                    'spinner_color'               => '#FFFFFF',
                    'enable_arrows'               => 'on',
                    'navigation_arrow_style'      => 'round',
                    'arrows_always_on'            => 'true',
                    'hide_arrows'                 => '200',
                    'hide_arrows_mobile'          => '1200',
                    'hide_arrows_on_mobile'       => 'on',
                    'arrows_under_hidden'         => '600',
                    'hide_arrows_over'            => 'off',
                    'arrows_over_hidden'          => '0',
                    'leftarrow_align_hor'         => 'left',
                    'leftarrow_align_vert'        => 'center',
                    'leftarrow_offset_hor'        => '30',
                    'leftarrow_offset_vert'       => '0',
                    'rightarrow_align_hor'        => 'right',
                    'rightarrow_align_vert'       => 'center',
                    'rightarrow_offset_hor'       => '30',
                    'rightarrow_offset_vert'      => '0',
                    'enable_bullets'              => 'on',
                    'navigation_bullets_style'    => 'round-old',
                    'bullets_space'               => '5',
                    'bullets_direction'           => 'horizontal',
                    'bullets_always_on'           => 'true',
                    'hide_bullets'                => '200',
                    'hide_bullets_mobile'         => '1200',
                    'hide_bullets_on_mobile'      => 'on',
                    'bullets_under_hidden'        => '600',
                    'hide_bullets_over'           => 'off',
                    'bullets_over_hidden'         => '0',
                    'bullets_align_hor'           => 'center',
                    'bullets_align_vert'          => 'bottom',
                    'bullets_offset_hor'          => '0',
                    'bullets_offset_vert'         => '30',
                    'enable_thumbnails'           => 'off',
                    'thumbnails_padding'          => '5',
                    'span_thumbnails_wrapper'     => 'off',
                    'thumbnails_wrapper_color'    => 'transparent',
                    'thumbnails_wrapper_opacity'  => '100',
                    'thumbnails_style'            => 'round',
                    'thumb_amount'                => '5',
                    'thumbnails_space'            => '5',
                    'thumbnail_direction'         => 'horizontal',
                    'thumb_width'                 => '100',
                    'thumb_height'                => '50',
                    'thumb_width_min'             => '100',
                    'thumbs_always_on'            => 'false',
                    'hide_thumbs'                 => '200',
                    'hide_thumbs_mobile'          => '1200',
                    'hide_thumbs_on_mobile'       => 'off',
                    'thumbs_under_hidden'         => '0',
                    'hide_thumbs_over'            => 'off',
                    'thumbs_over_hidden'          => '0',
                    'thumbnails_inner_outer'      => 'inner',
                    'thumbnails_align_hor'        => 'center',
                    'thumbnails_align_vert'       => 'bottom',
                    'thumbnails_offset_hor'       => '0',
                    'thumbnails_offset_vert'      => '20',
                    'enable_tabs'                 => 'off',
                    'tabs_padding'                => '5',
                    'span_tabs_wrapper'           => 'off',
                    'tabs_wrapper_color'          => 'transparent',
                    'tabs_wrapper_opacity'        => '5',
                    'tabs_style'                  => '',
                    'tabs_amount'                 => '5',
                    'tabs_space'                  => '5',
                    'tabs_direction'              => 'horizontal',
                    'tabs_width'                  => '100',
                    'tabs_height'                 => '50',
                    'tabs_width_min'              => '100',
                    'tabs_always_on'              => 'false',
                    'hide_tabs'                   => '200',
                    'hide_tabs_mobile'            => '1200',
                    'hide_tabs_on_mobile'         => 'off',
                    'tabs_under_hidden'           => '0',
                    'hide_tabs_over'              => 'off',
                    'tabs_over_hidden'            => '0',
                    'tabs_inner_outer'            => 'inner',
                    'tabs_align_hor'              => 'center',
                    'tabs_align_vert'             => 'bottom',
                    'tabs_offset_hor'             => '0',
                    'tabs_offset_vert'            => '20',
                    'touchenabled'                => 'on',
                    'drag_block_vertical'         => 'off',
                    'swipe_velocity'              => '75',
                    'swipe_min_touches'           => '50',
                    'swipe_direction'             => 'horizontal',
                    'keyboard_navigation'         => 'off',
                    'keyboard_direction'          => 'horizontal',
                    'mousescroll_navigation'      => 'off',
                    'carousel_infinity'           => 'off',
                    'carousel_space'              => '0',
                    'carousel_borderr'            => '0',
                    'carousel_borderr_unit'       => 'px',
                    'carousel_padding_top'        => '0',
                    'carousel_padding_bottom'     => '0',
                    'carousel_maxitems'           => '3',
                    'carousel_stretch'            => 'off',
                    'carousel_fadeout'            => 'on',
                    'carousel_varyfade'           => 'off',
                    'carousel_rotation'           => 'off',
                    'carousel_varyrotate'         => 'off',
                    'carousel_maxrotation'        => '0',
                    'carousel_scale'              => 'off',
                    'carousel_varyscale'          => 'off',
                    'carousel_scaledown'          => '50',
                    'carousel_hposition'          => 'center',
                    'carousel_vposition'          => 'center',
                    'use_parallax'                => 'on',
                    'disable_parallax_mobile'     => 'off',
                    'parallax_type'               => 'mouse',
                    'parallax_origo'              => 'slidercenter',
                    'parallax_speed'              => '2000',
                    'parallax_level_1'            => '2',
                    'parallax_level_2'            => '3',
                    'parallax_level_3'            => '4',
                    'parallax_level_4'            => '5',
                    'parallax_level_5'            => '6',
                    'parallax_level_6'            => '7',
                    'parallax_level_7'            => '12',
                    'parallax_level_8'            => '16',
                    'parallax_level_9'            => '10',
                    'parallax_level_10'           => '50',
                    'lazy_load_type'              => 'smart',
                    'seo_optimization'            => 'none',
                    'simplify_ie8_ios4'           => 'off',
                    'show_alternative_type'       => 'off',
                    'show_alternate_image'        => '',
                    'jquery_noconflict'           => 'off',
                    'js_to_body'                  => 'false',
                    'output_type'                 => 'none',
                    'jquery_debugmode'            => 'off',
                    'slider_type'                 => 'fullwidth',
                    'width'                       => '1240',
                    'width_notebook'              => '1024',
                    'width_tablet'                => '778',
                    'width_mobile'                => '480',
                    'height'                      => '600',
                    'height_notebook'             => '600',
                    'height_tablet'               => '500',
                    'height_mobile'               => '400',
                    'enable_custom_size_notebook' => 'on',
                    'enable_custom_size_tablet'   => 'on',
                    'enable_custom_size_iphone'   => 'on',
                    'main_overflow_hidden'        => 'off',
                    'auto_height'                 => 'off',
                    'min_height'                  => '',
                    'custom_javascript'           => '',
                    'custom_css'                  => '',
                ),
        );

        $presets[] = array(
            'settings' =>
                array('class' => '', 'image' => RevSliderFunctions::asset('') . 'admin/images/sliderpresets/slideshow_auto_layout.png', 'name' => 'Slideshow-Full-Screen', 'preset' => 'standardpreset'),
            'values'   =>
                array(
                    'next_slide_on_window_focus'  => 'off',
                    'delay'                       => '9000',
                    'start_js_after_delay'        => '0',
                    'image_source_type'           => 'full',
                    0                             => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                    'start_with_slide'            => '1',
                    'stop_on_hover'               => 'on',
                    'stop_slider'                 => 'off',
                    'stop_after_loops'            => '0',
                    'stop_at_slide'               => '1',
                    'shuffle'                     => 'off',
                    'viewport_start'              => 'wait',
                    'viewport_area'               => '80',
                    'enable_progressbar'          => 'on',
                    'background_dotted_overlay'   => 'none',
                    'background_color'            => 'transparent',
                    'padding'                     => '0',
                    'show_background_image'       => 'off',
                    'background_image'            => '',
                    'bg_fit'                      => 'cover',
                    'bg_repeat'                   => 'no-repeat',
                    'bg_position'                 => 'center center',
                    'position'                    => 'center',
                    'use_spinner'                 => '-1',
                    'spinner_color'               => '#FFFFFF',
                    'enable_arrows'               => 'on',
                    'navigation_arrow_style'      => 'round',
                    'arrows_always_on'            => 'true',
                    'hide_arrows'                 => '200',
                    'hide_arrows_mobile'          => '1200',
                    'hide_arrows_on_mobile'       => 'on',
                    'arrows_under_hidden'         => '600',
                    'hide_arrows_over'            => 'off',
                    'arrows_over_hidden'          => '0',
                    'leftarrow_align_hor'         => 'left',
                    'leftarrow_align_vert'        => 'center',
                    'leftarrow_offset_hor'        => '30',
                    'leftarrow_offset_vert'       => '0',
                    'rightarrow_align_hor'        => 'right',
                    'rightarrow_align_vert'       => 'center',
                    'rightarrow_offset_hor'       => '30',
                    'rightarrow_offset_vert'      => '0',
                    'enable_bullets'              => 'on',
                    'navigation_bullets_style'    => 'round-old',
                    'bullets_space'               => '5',
                    'bullets_direction'           => 'horizontal',
                    'bullets_always_on'           => 'true',
                    'hide_bullets'                => '200',
                    'hide_bullets_mobile'         => '1200',
                    'hide_bullets_on_mobile'      => 'on',
                    'bullets_under_hidden'        => '600',
                    'hide_bullets_over'           => 'off',
                    'bullets_over_hidden'         => '0',
                    'bullets_align_hor'           => 'center',
                    'bullets_align_vert'          => 'bottom',
                    'bullets_offset_hor'          => '0',
                    'bullets_offset_vert'         => '30',
                    'enable_thumbnails'           => 'off',
                    'thumbnails_padding'          => '5',
                    'span_thumbnails_wrapper'     => 'off',
                    'thumbnails_wrapper_color'    => 'transparent',
                    'thumbnails_wrapper_opacity'  => '100',
                    'thumbnails_style'            => 'round',
                    'thumb_amount'                => '5',
                    'thumbnails_space'            => '5',
                    'thumbnail_direction'         => 'horizontal',
                    'thumb_width'                 => '100',
                    'thumb_height'                => '50',
                    'thumb_width_min'             => '100',
                    'thumbs_always_on'            => 'false',
                    'hide_thumbs'                 => '200',
                    'hide_thumbs_mobile'          => '1200',
                    'hide_thumbs_on_mobile'       => 'off',
                    'thumbs_under_hidden'         => '0',
                    'hide_thumbs_over'            => 'off',
                    'thumbs_over_hidden'          => '0',
                    'thumbnails_inner_outer'      => 'inner',
                    'thumbnails_align_hor'        => 'center',
                    'thumbnails_align_vert'       => 'bottom',
                    'thumbnails_offset_hor'       => '0',
                    'thumbnails_offset_vert'      => '20',
                    'enable_tabs'                 => 'off',
                    'tabs_padding'                => '5',
                    'span_tabs_wrapper'           => 'off',
                    'tabs_wrapper_color'          => 'transparent',
                    'tabs_wrapper_opacity'        => '5',
                    'tabs_style'                  => '',
                    'tabs_amount'                 => '5',
                    'tabs_space'                  => '5',
                    'tabs_direction'              => 'horizontal',
                    'tabs_width'                  => '100',
                    'tabs_height'                 => '50',
                    'tabs_width_min'              => '100',
                    'tabs_always_on'              => 'false',
                    'hide_tabs'                   => '200',
                    'hide_tabs_mobile'            => '1200',
                    'hide_tabs_on_mobile'         => 'off',
                    'tabs_under_hidden'           => '0',
                    'hide_tabs_over'              => 'off',
                    'tabs_over_hidden'            => '0',
                    'tabs_inner_outer'            => 'inner',
                    'tabs_align_hor'              => 'center',
                    'tabs_align_vert'             => 'bottom',
                    'tabs_offset_hor'             => '0',
                    'tabs_offset_vert'            => '20',
                    'touchenabled'                => 'on',
                    'drag_block_vertical'         => 'off',
                    'swipe_velocity'              => '75',
                    'swipe_min_touches'           => '50',
                    'swipe_direction'             => 'horizontal',
                    'keyboard_navigation'         => 'off',
                    'keyboard_direction'          => 'horizontal',
                    'mousescroll_navigation'      => 'off',
                    'carousel_infinity'           => 'off',
                    'carousel_space'              => '0',
                    'carousel_borderr'            => '0',
                    'carousel_borderr_unit'       => 'px',
                    'carousel_padding_top'        => '0',
                    'carousel_padding_bottom'     => '0',
                    'carousel_maxitems'           => '3',
                    'carousel_stretch'            => 'off',
                    'carousel_fadeout'            => 'on',
                    'carousel_varyfade'           => 'off',
                    'carousel_rotation'           => 'off',
                    'carousel_varyrotate'         => 'off',
                    'carousel_maxrotation'        => '0',
                    'carousel_scale'              => 'off',
                    'carousel_varyscale'          => 'off',
                    'carousel_scaledown'          => '50',
                    'carousel_hposition'          => 'center',
                    'carousel_vposition'          => 'center',
                    'use_parallax'                => 'on',
                    'disable_parallax_mobile'     => 'off',
                    'parallax_type'               => 'mouse',
                    'parallax_origo'              => 'slidercenter',
                    'parallax_speed'              => '2000',
                    'parallax_level_1'            => '2',
                    'parallax_level_2'            => '3',
                    'parallax_level_3'            => '4',
                    'parallax_level_4'            => '5',
                    'parallax_level_5'            => '6',
                    'parallax_level_6'            => '7',
                    'parallax_level_7'            => '12',
                    'parallax_level_8'            => '16',
                    'parallax_level_9'            => '10',
                    'parallax_level_10'           => '50',
                    'lazy_load_type'              => 'smart',
                    'seo_optimization'            => 'none',
                    'simplify_ie8_ios4'           => 'off',
                    'show_alternative_type'       => 'off',
                    'show_alternate_image'        => '',
                    'jquery_noconflict'           => 'off',
                    'js_to_body'                  => 'false',
                    'output_type'                 => 'none',
                    'jquery_debugmode'            => 'off',
                    'slider_type'                 => 'fullscreen',
                    'width'                       => '1240',
                    'width_notebook'              => '1024',
                    'width_tablet'                => '778',
                    'width_mobile'                => '480',
                    'height'                      => '600',
                    'height_notebook'             => '600',
                    'height_tablet'               => '500',
                    'height_mobile'               => '400',
                    'enable_custom_size_notebook' => 'on',
                    'enable_custom_size_tablet'   => 'on',
                    'enable_custom_size_iphone'   => 'on',
                    'main_overflow_hidden'        => 'off',
                    'auto_height'                 => 'off',
                    'min_height'                  => '',
                    'custom_javascript'           => '',
                    'custom_css'                  => '',
                ),
        );

        $presets[] = array(
            'settings' =>
                array('class' => '', 'image' => RevSliderFunctions::asset('') . 'admin/images/sliderpresets/thumb_auto1.png', 'name' => 'Thumbs-Bottom-Auto', 'preset' => 'standardpreset'),
            'values'   =>
                array(
                    'next_slide_on_window_focus'  => 'off',
                    'delay'                       => '9000',
                    'start_js_after_delay'        => '0',
                    'image_source_type'           => 'full',
                    0                             => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                    'start_with_slide'            => '1',
                    'stop_on_hover'               => 'off',
                    'stop_slider'                 => 'on',
                    'stop_after_loops'            => '0',
                    'stop_at_slide'               => '1',
                    'shuffle'                     => 'off',
                    'viewport_start'              => 'wait',
                    'viewport_area'               => '80',
                    'enable_progressbar'          => 'off',
                    'background_dotted_overlay'   => 'none',
                    'background_color'            => 'transparent',
                    'padding'                     => '0',
                    'show_background_image'       => 'off',
                    'background_image'            => '',
                    'bg_fit'                      => 'cover',
                    'bg_repeat'                   => 'no-repeat',
                    'bg_position'                 => 'center center',
                    'position'                    => 'center',
                    'use_spinner'                 => '-1',
                    'spinner_color'               => '#FFFFFF',
                    'enable_arrows'               => 'on',
                    'navigation_arrow_style'      => 'navbar',
                    'arrows_always_on'            => 'false',
                    'hide_arrows'                 => '200',
                    'hide_arrows_mobile'          => '1200',
                    'hide_arrows_on_mobile'       => 'on',
                    'arrows_under_hidden'         => '600',
                    'hide_arrows_over'            => 'off',
                    'arrows_over_hidden'          => '0',
                    'leftarrow_align_hor'         => 'left',
                    'leftarrow_align_vert'        => 'center',
                    'leftarrow_offset_hor'        => '30',
                    'leftarrow_offset_vert'       => '0',
                    'rightarrow_align_hor'        => 'right',
                    'rightarrow_align_vert'       => 'center',
                    'rightarrow_offset_hor'       => '30',
                    'rightarrow_offset_vert'      => '0',
                    'enable_bullets'              => 'off',
                    'navigation_bullets_style'    => 'round-old',
                    'bullets_space'               => '5',
                    'bullets_direction'           => 'horizontal',
                    'bullets_always_on'           => 'true',
                    'hide_bullets'                => '200',
                    'hide_bullets_mobile'         => '1200',
                    'hide_bullets_on_mobile'      => 'on',
                    'bullets_under_hidden'        => '600',
                    'hide_bullets_over'           => 'off',
                    'bullets_over_hidden'         => '0',
                    'bullets_align_hor'           => 'center',
                    'bullets_align_vert'          => 'bottom',
                    'bullets_offset_hor'          => '0',
                    'bullets_offset_vert'         => '30',
                    'enable_thumbnails'           => 'on',
                    'thumbnails_padding'          => '5',
                    'span_thumbnails_wrapper'     => 'off',
                    'thumbnails_wrapper_color'    => 'transparent',
                    'thumbnails_wrapper_opacity'  => '100',
                    'thumbnails_style'            => 'navbar',
                    'thumb_amount'                => '5',
                    'thumbnails_space'            => '5',
                    'thumbnail_direction'         => 'horizontal',
                    'thumb_width'                 => '50',
                    'thumb_height'                => '50',
                    'thumb_width_min'             => '50',
                    'thumbs_always_on'            => 'false',
                    'hide_thumbs'                 => '200',
                    'hide_thumbs_mobile'          => '1200',
                    'hide_thumbs_on_mobile'       => 'off',
                    'thumbs_under_hidden'         => '0',
                    'hide_thumbs_over'            => 'off',
                    'thumbs_over_hidden'          => '0',
                    'thumbnails_inner_outer'      => 'inner',
                    'thumbnails_align_hor'        => 'center',
                    'thumbnails_align_vert'       => 'bottom',
                    'thumbnails_offset_hor'       => '0',
                    'thumbnails_offset_vert'      => '20',
                    'enable_tabs'                 => 'off',
                    'tabs_padding'                => '5',
                    'span_tabs_wrapper'           => 'off',
                    'tabs_wrapper_color'          => 'transparent',
                    'tabs_wrapper_opacity'        => '5',
                    'tabs_style'                  => '',
                    'tabs_amount'                 => '5',
                    'tabs_space'                  => '5',
                    'tabs_direction'              => 'horizontal',
                    'tabs_width'                  => '100',
                    'tabs_height'                 => '50',
                    'tabs_width_min'              => '100',
                    'tabs_always_on'              => 'false',
                    'hide_tabs'                   => '200',
                    'hide_tabs_mobile'            => '1200',
                    'hide_tabs_on_mobile'         => 'off',
                    'tabs_under_hidden'           => '0',
                    'hide_tabs_over'              => 'off',
                    'tabs_over_hidden'            => '0',
                    'tabs_inner_outer'            => 'inner',
                    'tabs_align_hor'              => 'center',
                    'tabs_align_vert'             => 'bottom',
                    'tabs_offset_hor'             => '0',
                    'tabs_offset_vert'            => '20',
                    'touchenabled'                => 'on',
                    'drag_block_vertical'         => 'off',
                    'swipe_velocity'              => '75',
                    'swipe_min_touches'           => '50',
                    'swipe_direction'             => 'horizontal',
                    'keyboard_navigation'         => 'off',
                    'keyboard_direction'          => 'horizontal',
                    'mousescroll_navigation'      => 'off',
                    'carousel_infinity'           => 'off',
                    'carousel_space'              => '0',
                    'carousel_borderr'            => '0',
                    'carousel_borderr_unit'       => 'px',
                    'carousel_padding_top'        => '0',
                    'carousel_padding_bottom'     => '0',
                    'carousel_maxitems'           => '3',
                    'carousel_stretch'            => 'off',
                    'carousel_fadeout'            => 'on',
                    'carousel_varyfade'           => 'off',
                    'carousel_rotation'           => 'off',
                    'carousel_varyrotate'         => 'off',
                    'carousel_maxrotation'        => '0',
                    'carousel_scale'              => 'off',
                    'carousel_varyscale'          => 'off',
                    'carousel_scaledown'          => '50',
                    'carousel_hposition'          => 'center',
                    'carousel_vposition'          => 'center',
                    'use_parallax'                => 'on',
                    'disable_parallax_mobile'     => 'off',
                    'parallax_type'               => 'mouse',
                    'parallax_origo'              => 'slidercenter',
                    'parallax_speed'              => '2000',
                    'parallax_level_1'            => '2',
                    'parallax_level_2'            => '3',
                    'parallax_level_3'            => '4',
                    'parallax_level_4'            => '5',
                    'parallax_level_5'            => '6',
                    'parallax_level_6'            => '7',
                    'parallax_level_7'            => '12',
                    'parallax_level_8'            => '16',
                    'parallax_level_9'            => '10',
                    'parallax_level_10'           => '50',
                    'lazy_load_type'              => 'smart',
                    'seo_optimization'            => 'none',
                    'simplify_ie8_ios4'           => 'off',
                    'show_alternative_type'       => 'off',
                    'show_alternate_image'        => '',
                    'jquery_noconflict'           => 'off',
                    'js_to_body'                  => 'false',
                    'output_type'                 => 'none',
                    'jquery_debugmode'            => 'off',
                    'slider_type'                 => 'auto',
                    'width'                       => '1240',
                    'width_notebook'              => '1024',
                    'width_tablet'                => '778',
                    'width_mobile'                => '480',
                    'height'                      => '600',
                    'height_notebook'             => '600',
                    'height_tablet'               => '500',
                    'height_mobile'               => '400',
                    'enable_custom_size_notebook' => 'on',
                    'enable_custom_size_tablet'   => 'on',
                    'enable_custom_size_iphone'   => 'on',
                    'main_overflow_hidden'        => 'off',
                    'auto_height'                 => 'off',
                    'min_height'                  => '',
                    'custom_javascript'           => '',
                    'custom_css'                  => '',
                ),
        );

        $presets[] = array(
            'settings' =>
                array('class' => '', 'image' => RevSliderFunctions::asset('') . 'admin/images/sliderpresets/thumbs_left_auto.png', 'name' => 'Thumbs-Left-Auto', 'preset' => 'standardpreset'),
            'values'   =>
                array(
                    'next_slide_on_window_focus'  => 'off',
                    'delay'                       => '9000',
                    'start_js_after_delay'        => '0',
                    'image_source_type'           => 'full',
                    0                             => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                    'start_with_slide'            => '1',
                    'stop_on_hover'               => 'off',
                    'stop_slider'                 => 'on',
                    'stop_after_loops'            => '0',
                    'stop_at_slide'               => '1',
                    'shuffle'                     => 'off',
                    'viewport_start'              => 'wait',
                    'viewport_area'               => '80',
                    'enable_progressbar'          => 'off',
                    'background_dotted_overlay'   => 'none',
                    'background_color'            => 'transparent',
                    'padding'                     => '0',
                    'show_background_image'       => 'off',
                    'background_image'            => '',
                    'bg_fit'                      => 'cover',
                    'bg_repeat'                   => 'no-repeat',
                    'bg_position'                 => 'center center',
                    'position'                    => 'center',
                    'use_spinner'                 => '-1',
                    'spinner_color'               => '#FFFFFF',
                    'enable_arrows'               => 'on',
                    'navigation_arrow_style'      => 'navbar',
                    'arrows_always_on'            => 'false',
                    'hide_arrows'                 => '200',
                    'hide_arrows_mobile'          => '1200',
                    'hide_arrows_on_mobile'       => 'off',
                    'arrows_under_hidden'         => '600',
                    'hide_arrows_over'            => 'off',
                    'arrows_over_hidden'          => '0',
                    'leftarrow_align_hor'         => 'right',
                    'leftarrow_align_vert'        => 'bottom',
                    'leftarrow_offset_hor'        => '40',
                    'leftarrow_offset_vert'       => '0',
                    'rightarrow_align_hor'        => 'right',
                    'rightarrow_align_vert'       => 'bottom',
                    'rightarrow_offset_hor'       => '0',
                    'rightarrow_offset_vert'      => '0',
                    'enable_bullets'              => 'off',
                    'navigation_bullets_style'    => 'round-old',
                    'bullets_space'               => '5',
                    'bullets_direction'           => 'horizontal',
                    'bullets_always_on'           => 'true',
                    'hide_bullets'                => '200',
                    'hide_bullets_mobile'         => '1200',
                    'hide_bullets_on_mobile'      => 'on',
                    'bullets_under_hidden'        => '600',
                    'hide_bullets_over'           => 'off',
                    'bullets_over_hidden'         => '0',
                    'bullets_align_hor'           => 'center',
                    'bullets_align_vert'          => 'bottom',
                    'bullets_offset_hor'          => '0',
                    'bullets_offset_vert'         => '30',
                    'enable_thumbnails'           => 'on',
                    'thumbnails_padding'          => '5',
                    'span_thumbnails_wrapper'     => 'off',
                    'thumbnails_wrapper_color'    => 'transparent',
                    'thumbnails_wrapper_opacity'  => '100',
                    'thumbnails_style'            => 'navbar',
                    'thumb_amount'                => '5',
                    'thumbnails_space'            => '5',
                    'thumbnail_direction'         => 'vertical',
                    'thumb_width'                 => '50',
                    'thumb_height'                => '50',
                    'thumb_width_min'             => '50',
                    'thumbs_always_on'            => 'false',
                    'hide_thumbs'                 => '200',
                    'hide_thumbs_mobile'          => '1200',
                    'hide_thumbs_on_mobile'       => 'on',
                    'thumbs_under_hidden'         => '778',
                    'hide_thumbs_over'            => 'off',
                    'thumbs_over_hidden'          => '0',
                    'thumbnails_inner_outer'      => 'inner',
                    'thumbnails_align_hor'        => 'left',
                    'thumbnails_align_vert'       => 'center',
                    'thumbnails_offset_hor'       => '20',
                    'thumbnails_offset_vert'      => '0',
                    'enable_tabs'                 => 'off',
                    'tabs_padding'                => '5',
                    'span_tabs_wrapper'           => 'off',
                    'tabs_wrapper_color'          => 'transparent',
                    'tabs_wrapper_opacity'        => '5',
                    'tabs_style'                  => '',
                    'tabs_amount'                 => '5',
                    'tabs_space'                  => '5',
                    'tabs_direction'              => 'horizontal',
                    'tabs_width'                  => '100',
                    'tabs_height'                 => '50',
                    'tabs_width_min'              => '100',
                    'tabs_always_on'              => 'false',
                    'hide_tabs'                   => '200',
                    'hide_tabs_mobile'            => '1200',
                    'hide_tabs_on_mobile'         => 'off',
                    'tabs_under_hidden'           => '0',
                    'hide_tabs_over'              => 'off',
                    'tabs_over_hidden'            => '0',
                    'tabs_inner_outer'            => 'inner',
                    'tabs_align_hor'              => 'center',
                    'tabs_align_vert'             => 'bottom',
                    'tabs_offset_hor'             => '0',
                    'tabs_offset_vert'            => '20',
                    'touchenabled'                => 'on',
                    'drag_block_vertical'         => 'off',
                    'swipe_velocity'              => '75',
                    'swipe_min_touches'           => '50',
                    'swipe_direction'             => 'horizontal',
                    'keyboard_navigation'         => 'off',
                    'keyboard_direction'          => 'horizontal',
                    'mousescroll_navigation'      => 'off',
                    'carousel_infinity'           => 'off',
                    'carousel_space'              => '0',
                    'carousel_borderr'            => '0',
                    'carousel_borderr_unit'       => 'px',
                    'carousel_padding_top'        => '0',
                    'carousel_padding_bottom'     => '0',
                    'carousel_maxitems'           => '3',
                    'carousel_stretch'            => 'off',
                    'carousel_fadeout'            => 'on',
                    'carousel_varyfade'           => 'off',
                    'carousel_rotation'           => 'off',
                    'carousel_varyrotate'         => 'off',
                    'carousel_maxrotation'        => '0',
                    'carousel_scale'              => 'off',
                    'carousel_varyscale'          => 'off',
                    'carousel_scaledown'          => '50',
                    'carousel_hposition'          => 'center',
                    'carousel_vposition'          => 'center',
                    'use_parallax'                => 'on',
                    'disable_parallax_mobile'     => 'off',
                    'parallax_type'               => 'mouse',
                    'parallax_origo'              => 'slidercenter',
                    'parallax_speed'              => '2000',
                    'parallax_level_1'            => '2',
                    'parallax_level_2'            => '3',
                    'parallax_level_3'            => '4',
                    'parallax_level_4'            => '5',
                    'parallax_level_5'            => '6',
                    'parallax_level_6'            => '7',
                    'parallax_level_7'            => '12',
                    'parallax_level_8'            => '16',
                    'parallax_level_9'            => '10',
                    'parallax_level_10'           => '50',
                    'lazy_load_type'              => 'smart',
                    'seo_optimization'            => 'none',
                    'simplify_ie8_ios4'           => 'off',
                    'show_alternative_type'       => 'off',
                    'show_alternate_image'        => '',
                    'jquery_noconflict'           => 'off',
                    'js_to_body'                  => 'false',
                    'output_type'                 => 'none',
                    'jquery_debugmode'            => 'off',
                    'slider_type'                 => 'auto',
                    'width'                       => '1240',
                    'width_notebook'              => '1024',
                    'width_tablet'                => '778',
                    'width_mobile'                => '480',
                    'height'                      => '600',
                    'height_notebook'             => '600',
                    'height_tablet'               => '500',
                    'height_mobile'               => '400',
                    'enable_custom_size_notebook' => 'on',
                    'enable_custom_size_tablet'   => 'on',
                    'enable_custom_size_iphone'   => 'on',
                    'main_overflow_hidden'        => 'off',
                    'auto_height'                 => 'off',
                    'min_height'                  => '',
                    'custom_javascript'           => '',
                    'custom_css'                  => '',
                ),
        );

        $presets[] = array(
            'settings' => array('class' => '', 'image' => RevSliderFunctions::asset('') . 'admin/images/sliderpresets/thumbs_right_auto.png', 'name' => 'Thumbs-Right-Auto', 'preset' => 'standardpreset'),
            'values'   =>
                array(
                    'next_slide_on_window_focus'  => 'off',
                    'delay'                       => '9000',
                    'start_js_after_delay'        => '0',
                    'image_source_type'           => 'full',
                    0                             => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                    'start_with_slide'            => '1',
                    'stop_on_hover'               => 'off',
                    'stop_slider'                 => 'on',
                    'stop_after_loops'            => '0',
                    'stop_at_slide'               => '1',
                    'shuffle'                     => 'off',
                    'viewport_start'              => 'wait',
                    'viewport_area'               => '80',
                    'enable_progressbar'          => 'off',
                    'background_dotted_overlay'   => 'none',
                    'background_color'            => 'transparent',
                    'padding'                     => '0',
                    'show_background_image'       => 'off',
                    'background_image'            => '',
                    'bg_fit'                      => 'cover',
                    'bg_repeat'                   => 'no-repeat',
                    'bg_position'                 => 'center center',
                    'position'                    => 'center',
                    'use_spinner'                 => '-1',
                    'spinner_color'               => '#FFFFFF',
                    'enable_arrows'               => 'on',
                    'navigation_arrow_style'      => 'navbar',
                    'arrows_always_on'            => 'false',
                    'hide_arrows'                 => '200',
                    'hide_arrows_mobile'          => '1200',
                    'hide_arrows_on_mobile'       => 'off',
                    'arrows_under_hidden'         => '600',
                    'hide_arrows_over'            => 'off',
                    'arrows_over_hidden'          => '0',
                    'leftarrow_align_hor'         => 'left',
                    'leftarrow_align_vert'        => 'bottom',
                    'leftarrow_offset_hor'        => '0',
                    'leftarrow_offset_vert'       => '0',
                    'rightarrow_align_hor'        => 'left',
                    'rightarrow_align_vert'       => 'bottom',
                    'rightarrow_offset_hor'       => '40',
                    'rightarrow_offset_vert'      => '0',
                    'enable_bullets'              => 'off',
                    'navigation_bullets_style'    => 'round-old',
                    'bullets_space'               => '5',
                    'bullets_direction'           => 'horizontal',
                    'bullets_always_on'           => 'true',
                    'hide_bullets'                => '200',
                    'hide_bullets_mobile'         => '1200',
                    'hide_bullets_on_mobile'      => 'on',
                    'bullets_under_hidden'        => '600',
                    'hide_bullets_over'           => 'off',
                    'bullets_over_hidden'         => '0',
                    'bullets_align_hor'           => 'center',
                    'bullets_align_vert'          => 'bottom',
                    'bullets_offset_hor'          => '0',
                    'bullets_offset_vert'         => '30',
                    'enable_thumbnails'           => 'on',
                    'thumbnails_padding'          => '5',
                    'span_thumbnails_wrapper'     => 'off',
                    'thumbnails_wrapper_color'    => 'transparent',
                    'thumbnails_wrapper_opacity'  => '100',
                    'thumbnails_style'            => 'navbar',
                    'thumb_amount'                => '5',
                    'thumbnails_space'            => '5',
                    'thumbnail_direction'         => 'vertical',
                    'thumb_width'                 => '50',
                    'thumb_height'                => '50',
                    'thumb_width_min'             => '50',
                    'thumbs_always_on'            => 'false',
                    'hide_thumbs'                 => '200',
                    'hide_thumbs_mobile'          => '1200',
                    'hide_thumbs_on_mobile'       => 'on',
                    'thumbs_under_hidden'         => '778',
                    'hide_thumbs_over'            => 'off',
                    'thumbs_over_hidden'          => '0',
                    'thumbnails_inner_outer'      => 'inner',
                    'thumbnails_align_hor'        => 'right',
                    'thumbnails_align_vert'       => 'center',
                    'thumbnails_offset_hor'       => '20',
                    'thumbnails_offset_vert'      => '0',
                    'enable_tabs'                 => 'off',
                    'tabs_padding'                => '5',
                    'span_tabs_wrapper'           => 'off',
                    'tabs_wrapper_color'          => 'transparent',
                    'tabs_wrapper_opacity'        => '5',
                    'tabs_style'                  => '',
                    'tabs_amount'                 => '5',
                    'tabs_space'                  => '5',
                    'tabs_direction'              => 'horizontal',
                    'tabs_width'                  => '100',
                    'tabs_height'                 => '50',
                    'tabs_width_min'              => '100',
                    'tabs_always_on'              => 'false',
                    'hide_tabs'                   => '200',
                    'hide_tabs_mobile'            => '1200',
                    'hide_tabs_on_mobile'         => 'off',
                    'tabs_under_hidden'           => '0',
                    'hide_tabs_over'              => 'off',
                    'tabs_over_hidden'            => '0',
                    'tabs_inner_outer'            => 'inner',
                    'tabs_align_hor'              => 'center',
                    'tabs_align_vert'             => 'bottom',
                    'tabs_offset_hor'             => '0',
                    'tabs_offset_vert'            => '20',
                    'touchenabled'                => 'on',
                    'drag_block_vertical'         => 'off',
                    'swipe_velocity'              => '75',
                    'swipe_min_touches'           => '50',
                    'swipe_direction'             => 'horizontal',
                    'keyboard_navigation'         => 'off',
                    'keyboard_direction'          => 'horizontal',
                    'mousescroll_navigation'      => 'off',
                    'carousel_infinity'           => 'off',
                    'carousel_space'              => '0',
                    'carousel_borderr'            => '0',
                    'carousel_borderr_unit'       => 'px',
                    'carousel_padding_top'        => '0',
                    'carousel_padding_bottom'     => '0',
                    'carousel_maxitems'           => '3',
                    'carousel_stretch'            => 'off',
                    'carousel_fadeout'            => 'on',
                    'carousel_varyfade'           => 'off',
                    'carousel_rotation'           => 'off',
                    'carousel_varyrotate'         => 'off',
                    'carousel_maxrotation'        => '0',
                    'carousel_scale'              => 'off',
                    'carousel_varyscale'          => 'off',
                    'carousel_scaledown'          => '50',
                    'carousel_hposition'          => 'center',
                    'carousel_vposition'          => 'center',
                    'use_parallax'                => 'on',
                    'disable_parallax_mobile'     => 'off',
                    'parallax_type'               => 'mouse',
                    'parallax_origo'              => 'slidercenter',
                    'parallax_speed'              => '2000',
                    'parallax_level_1'            => '2',
                    'parallax_level_2'            => '3',
                    'parallax_level_3'            => '4',
                    'parallax_level_4'            => '5',
                    'parallax_level_5'            => '6',
                    'parallax_level_6'            => '7',
                    'parallax_level_7'            => '12',
                    'parallax_level_8'            => '16',
                    'parallax_level_9'            => '10',
                    'parallax_level_10'           => '50',
                    'lazy_load_type'              => 'smart',
                    'seo_optimization'            => 'none',
                    'simplify_ie8_ios4'           => 'off',
                    'show_alternative_type'       => 'off',
                    'show_alternate_image'        => '',
                    'jquery_noconflict'           => 'off',
                    'js_to_body'                  => 'false',
                    'output_type'                 => 'none',
                    'jquery_debugmode'            => 'off',
                    'slider_type'                 => 'auto',
                    'width'                       => '1240',
                    'width_notebook'              => '1024',
                    'width_tablet'                => '778',
                    'width_mobile'                => '480',
                    'height'                      => '600',
                    'height_notebook'             => '600',
                    'height_tablet'               => '500',
                    'height_mobile'               => '400',
                    'enable_custom_size_notebook' => 'on',
                    'enable_custom_size_tablet'   => 'on',
                    'enable_custom_size_iphone'   => 'on',
                    'main_overflow_hidden'        => 'off',
                    'auto_height'                 => 'off',
                    'min_height'                  => '',
                    'custom_javascript'           => '',
                    'custom_css'                  => '',
                ),
        );
        $presets[] = array(
            'settings' =>
                array('class' => '', 'image' => RevSliderFunctions::asset('') . 'admin/images/sliderpresets/scroll_fullscreen.png', 'name' => 'Vertical-Bullet-Full-Screen', 'preset' => 'standardpreset'),
            'values'   =>
                array(
                    'next_slide_on_window_focus'  => 'off',
                    'delay'                       => '9000',
                    'start_js_after_delay'        => '0',
                    'image_source_type'           => 'full',
                    0                             => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                    'start_with_slide'            => '1',
                    'stop_on_hover'               => 'off',
                    'stop_slider'                 => 'on',
                    'stop_after_loops'            => '0',
                    'stop_at_slide'               => '1',
                    'shuffle'                     => 'off',
                    'viewport_start'              => 'wait',
                    'viewport_area'               => '80',
                    'enable_progressbar'          => 'off',
                    'background_dotted_overlay'   => 'none',
                    'background_color'            => 'transparent',
                    'padding'                     => '0',
                    'show_background_image'       => 'off',
                    'background_image'            => '',
                    'bg_fit'                      => 'cover',
                    'bg_repeat'                   => 'no-repeat',
                    'bg_position'                 => 'center center',
                    'position'                    => 'center',
                    'use_spinner'                 => '-1',
                    'spinner_color'               => '#FFFFFF',
                    'enable_arrows'               => 'off',
                    'navigation_arrow_style'      => 'navbar',
                    'arrows_always_on'            => 'false',
                    'hide_arrows'                 => '200',
                    'hide_arrows_mobile'          => '1200',
                    'hide_arrows_on_mobile'       => 'off',
                    'arrows_under_hidden'         => '600',
                    'hide_arrows_over'            => 'off',
                    'arrows_over_hidden'          => '0',
                    'leftarrow_align_hor'         => 'left',
                    'leftarrow_align_vert'        => 'bottom',
                    'leftarrow_offset_hor'        => '0',
                    'leftarrow_offset_vert'       => '0',
                    'rightarrow_align_hor'        => 'left',
                    'rightarrow_align_vert'       => 'bottom',
                    'rightarrow_offset_hor'       => '40',
                    'rightarrow_offset_vert'      => '0',
                    'enable_bullets'              => 'on',
                    'navigation_bullets_style'    => 'round-old',
                    'bullets_space'               => '5',
                    'bullets_direction'           => 'vertical',
                    'bullets_always_on'           => 'false',
                    'hide_bullets'                => '200',
                    'hide_bullets_mobile'         => '1200',
                    'hide_bullets_on_mobile'      => 'on',
                    'bullets_under_hidden'        => '600',
                    'hide_bullets_over'           => 'off',
                    'bullets_over_hidden'         => '0',
                    'bullets_align_hor'           => 'right',
                    'bullets_align_vert'          => 'center',
                    'bullets_offset_hor'          => '30',
                    'bullets_offset_vert'         => '0',
                    'enable_thumbnails'           => 'off',
                    'thumbnails_padding'          => '5',
                    'span_thumbnails_wrapper'     => 'off',
                    'thumbnails_wrapper_color'    => 'transparent',
                    'thumbnails_wrapper_opacity'  => '100',
                    'thumbnails_style'            => 'navbar',
                    'thumb_amount'                => '5',
                    'thumbnails_space'            => '5',
                    'thumbnail_direction'         => 'vertical',
                    'thumb_width'                 => '50',
                    'thumb_height'                => '50',
                    'thumb_width_min'             => '50',
                    'thumbs_always_on'            => 'false',
                    'hide_thumbs'                 => '200',
                    'hide_thumbs_mobile'          => '1200',
                    'hide_thumbs_on_mobile'       => 'on',
                    'thumbs_under_hidden'         => '778',
                    'hide_thumbs_over'            => 'off',
                    'thumbs_over_hidden'          => '0',
                    'thumbnails_inner_outer'      => 'inner',
                    'thumbnails_align_hor'        => 'right',
                    'thumbnails_align_vert'       => 'center',
                    'thumbnails_offset_hor'       => '20',
                    'thumbnails_offset_vert'      => '0',
                    'enable_tabs'                 => 'off',
                    'tabs_padding'                => '5',
                    'span_tabs_wrapper'           => 'off',
                    'tabs_wrapper_color'          => 'transparent',
                    'tabs_wrapper_opacity'        => '5',
                    'tabs_style'                  => '',
                    'tabs_amount'                 => '5',
                    'tabs_space'                  => '5',
                    'tabs_direction'              => 'horizontal',
                    'tabs_width'                  => '100',
                    'tabs_height'                 => '50',
                    'tabs_width_min'              => '100',
                    'tabs_always_on'              => 'false',
                    'hide_tabs'                   => '200',
                    'hide_tabs_mobile'            => '1200',
                    'hide_tabs_on_mobile'         => 'off',
                    'tabs_under_hidden'           => '0',
                    'hide_tabs_over'              => 'off',
                    'tabs_over_hidden'            => '0',
                    'tabs_inner_outer'            => 'inner',
                    'tabs_align_hor'              => 'center',
                    'tabs_align_vert'             => 'bottom',
                    'tabs_offset_hor'             => '0',
                    'tabs_offset_vert'            => '20',
                    'touchenabled'                => 'on',
                    'drag_block_vertical'         => 'off',
                    'swipe_velocity'              => '75',
                    'swipe_min_touches'           => '50',
                    'swipe_direction'             => 'horizontal',
                    'keyboard_navigation'         => 'off',
                    'keyboard_direction'          => 'horizontal',
                    'mousescroll_navigation'      => 'off',
                    'carousel_infinity'           => 'off',
                    'carousel_space'              => '0',
                    'carousel_borderr'            => '0',
                    'carousel_borderr_unit'       => 'px',
                    'carousel_padding_top'        => '0',
                    'carousel_padding_bottom'     => '0',
                    'carousel_maxitems'           => '3',
                    'carousel_stretch'            => 'off',
                    'carousel_fadeout'            => 'on',
                    'carousel_varyfade'           => 'off',
                    'carousel_rotation'           => 'off',
                    'carousel_varyrotate'         => 'off',
                    'carousel_maxrotation'        => '0',
                    'carousel_scale'              => 'off',
                    'carousel_varyscale'          => 'off',
                    'carousel_scaledown'          => '50',
                    'carousel_hposition'          => 'center',
                    'carousel_vposition'          => 'center',
                    'use_parallax'                => 'on',
                    'disable_parallax_mobile'     => 'off',
                    'parallax_type'               => 'mouse',
                    'parallax_origo'              => 'slidercenter',
                    'parallax_speed'              => '2000',
                    'parallax_level_1'            => '2',
                    'parallax_level_2'            => '3',
                    'parallax_level_3'            => '4',
                    'parallax_level_4'            => '5',
                    'parallax_level_5'            => '6',
                    'parallax_level_6'            => '7',
                    'parallax_level_7'            => '12',
                    'parallax_level_8'            => '16',
                    'parallax_level_9'            => '10',
                    'parallax_level_10'           => '50',
                    'lazy_load_type'              => 'smart',
                    'seo_optimization'            => 'none',
                    'simplify_ie8_ios4'           => 'off',
                    'show_alternative_type'       => 'off',
                    'show_alternate_image'        => '',
                    'jquery_noconflict'           => 'off',
                    'js_to_body'                  => 'false',
                    'output_type'                 => 'none',
                    'jquery_debugmode'            => 'off',
                    'slider_type'                 => 'fullscreen',
                    'width'                       => '1240',
                    'width_notebook'              => '1024',
                    'width_tablet'                => '778',
                    'width_mobile'                => '480',
                    'height'                      => '600',
                    'height_notebook'             => '600',
                    'height_tablet'               => '500',
                    'height_mobile'               => '400',
                    'enable_custom_size_notebook' => 'on',
                    'enable_custom_size_tablet'   => 'on',
                    'enable_custom_size_iphone'   => 'on',
                    'main_overflow_hidden'        => 'off',
                    'auto_height'                 => 'off',
                    'min_height'                  => '',
                    'custom_javascript'           => '',
                    'custom_css'                  => '',
                ),
        );
        $presets[] = array(
            'settings' =>
                array('class' => '', 'image' => RevSliderFunctions::asset('') . 'admin/images/sliderpresets/wide_fullscreen.png', 'name' => 'Wide-Full-Screen', 'preset' => 'heropreset'),
            'values'   =>
                array(
                    'next_slide_on_window_focus'  => 'off',
                    'delay'                       => '9000',
                    'start_js_after_delay'        => '0',
                    'image_source_type'           => 'full',
                    0                             => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                    'start_with_slide'            => '1',
                    'stop_on_hover'               => 'off',
                    'stop_slider'                 => 'on',
                    'stop_after_loops'            => '0',
                    'stop_at_slide'               => '1',
                    'shuffle'                     => 'off',
                    'viewport_start'              => 'wait',
                    'viewport_area'               => '80',
                    'enable_progressbar'          => 'off',
                    'background_dotted_overlay'   => 'none',
                    'background_color'            => 'transparent',
                    'padding'                     => '0',
                    'show_background_image'       => 'off',
                    'background_image'            => '',
                    'bg_fit'                      => 'cover',
                    'bg_repeat'                   => 'no-repeat',
                    'bg_position'                 => 'center center',
                    'position'                    => 'center',
                    'use_spinner'                 => '-1',
                    'spinner_color'               => '#FFFFFF',
                    'enable_arrows'               => 'off',
                    'navigation_arrow_style'      => 'navbar',
                    'arrows_always_on'            => 'false',
                    'hide_arrows'                 => '200',
                    'hide_arrows_mobile'          => '1200',
                    'hide_arrows_on_mobile'       => 'off',
                    'arrows_under_hidden'         => '600',
                    'hide_arrows_over'            => 'off',
                    'arrows_over_hidden'          => '0',
                    'leftarrow_align_hor'         => 'left',
                    'leftarrow_align_vert'        => 'bottom',
                    'leftarrow_offset_hor'        => '0',
                    'leftarrow_offset_vert'       => '0',
                    'rightarrow_align_hor'        => 'left',
                    'rightarrow_align_vert'       => 'bottom',
                    'rightarrow_offset_hor'       => '40',
                    'rightarrow_offset_vert'      => '0',
                    'enable_bullets'              => 'on',
                    'navigation_bullets_style'    => 'round-old',
                    'bullets_space'               => '5',
                    'bullets_direction'           => 'vertical',
                    'bullets_always_on'           => 'true',
                    'hide_bullets'                => '200',
                    'hide_bullets_mobile'         => '1200',
                    'hide_bullets_on_mobile'      => 'on',
                    'bullets_under_hidden'        => '600',
                    'hide_bullets_over'           => 'off',
                    'bullets_over_hidden'         => '0',
                    'bullets_align_hor'           => 'right',
                    'bullets_align_vert'          => 'center',
                    'bullets_offset_hor'          => '30',
                    'bullets_offset_vert'         => '0',
                    'enable_thumbnails'           => 'off',
                    'thumbnails_padding'          => '5',
                    'span_thumbnails_wrapper'     => 'off',
                    'thumbnails_wrapper_color'    => 'transparent',
                    'thumbnails_wrapper_opacity'  => '100',
                    'thumbnails_style'            => 'navbar',
                    'thumb_amount'                => '5',
                    'thumbnails_space'            => '5',
                    'thumbnail_direction'         => 'vertical',
                    'thumb_width'                 => '50',
                    'thumb_height'                => '50',
                    'thumb_width_min'             => '50',
                    'thumbs_always_on'            => 'false',
                    'hide_thumbs'                 => '200',
                    'hide_thumbs_mobile'          => '1200',
                    'hide_thumbs_on_mobile'       => 'on',
                    'thumbs_under_hidden'         => '778',
                    'hide_thumbs_over'            => 'off',
                    'thumbs_over_hidden'          => '0',
                    'thumbnails_inner_outer'      => 'inner',
                    'thumbnails_align_hor'        => 'right',
                    'thumbnails_align_vert'       => 'center',
                    'thumbnails_offset_hor'       => '20',
                    'thumbnails_offset_vert'      => '0',
                    'enable_tabs'                 => 'off',
                    'tabs_padding'                => '5',
                    'span_tabs_wrapper'           => 'off',
                    'tabs_wrapper_color'          => 'transparent',
                    'tabs_wrapper_opacity'        => '5',
                    'tabs_style'                  => '',
                    'tabs_amount'                 => '5',
                    'tabs_space'                  => '5',
                    'tabs_direction'              => 'horizontal',
                    'tabs_width'                  => '100',
                    'tabs_height'                 => '50',
                    'tabs_width_min'              => '100',
                    'tabs_always_on'              => 'false',
                    'hide_tabs'                   => '200',
                    'hide_tabs_mobile'            => '1200',
                    'hide_tabs_on_mobile'         => 'off',
                    'tabs_under_hidden'           => '0',
                    'hide_tabs_over'              => 'off',
                    'tabs_over_hidden'            => '0',
                    'tabs_inner_outer'            => 'inner',
                    'tabs_align_hor'              => 'center',
                    'tabs_align_vert'             => 'bottom',
                    'tabs_offset_hor'             => '0',
                    'tabs_offset_vert'            => '20',
                    'touchenabled'                => 'on',
                    'drag_block_vertical'         => 'off',
                    'swipe_velocity'              => '75',
                    'swipe_min_touches'           => '50',
                    'swipe_direction'             => 'horizontal',
                    'keyboard_navigation'         => 'off',
                    'keyboard_direction'          => 'horizontal',
                    'mousescroll_navigation'      => 'off',
                    'carousel_infinity'           => 'off',
                    'carousel_space'              => '0',
                    'carousel_borderr'            => '0',
                    'carousel_borderr_unit'       => 'px',
                    'carousel_padding_top'        => '0',
                    'carousel_padding_bottom'     => '0',
                    'carousel_maxitems'           => '3',
                    'carousel_stretch'            => 'off',
                    'carousel_fadeout'            => 'on',
                    'carousel_varyfade'           => 'off',
                    'carousel_rotation'           => 'off',
                    'carousel_varyrotate'         => 'off',
                    'carousel_maxrotation'        => '0',
                    'carousel_scale'              => 'off',
                    'carousel_varyscale'          => 'off',
                    'carousel_scaledown'          => '50',
                    'carousel_hposition'          => 'center',
                    'carousel_vposition'          => 'center',
                    'use_parallax'                => 'on',
                    'disable_parallax_mobile'     => 'off',
                    'parallax_type'               => 'mouse',
                    'parallax_origo'              => 'slidercenter',
                    'parallax_speed'              => '2000',
                    'parallax_level_1'            => '2',
                    'parallax_level_2'            => '3',
                    'parallax_level_3'            => '4',
                    'parallax_level_4'            => '5',
                    'parallax_level_5'            => '6',
                    'parallax_level_6'            => '7',
                    'parallax_level_7'            => '12',
                    'parallax_level_8'            => '16',
                    'parallax_level_9'            => '10',
                    'parallax_level_10'           => '50',
                    'lazy_load_type'              => 'smart',
                    'seo_optimization'            => 'none',
                    'simplify_ie8_ios4'           => 'off',
                    'show_alternative_type'       => 'off',
                    'show_alternate_image'        => '',
                    'jquery_noconflict'           => 'off',
                    'js_to_body'                  => 'false',
                    'output_type'                 => 'none',
                    'jquery_debugmode'            => 'off',
                    'slider_type'                 => 'fullscreen',
                    'width'                       => '1400',
                    'width_notebook'              => '1240',
                    'width_tablet'                => '778',
                    'width_mobile'                => '480',
                    'height'                      => '868',
                    'height_notebook'             => '768',
                    'height_tablet'               => '960',
                    'height_mobile'               => '720',
                    'enable_custom_size_notebook' => 'on',
                    'enable_custom_size_tablet'   => 'on',
                    'enable_custom_size_iphone'   => 'on',
                    'main_overflow_hidden'        => 'off',
                    'auto_height'                 => 'off',
                    'min_height'                  => '',
                    'custom_javascript'           => '',
                    'custom_css'                  => '',
                ),
        );
        $presets[] = array(
            'settings' =>
                array('class' => '', 'image' => RevSliderFunctions::asset('') . 'admin/images/sliderpresets/wide_fullscreen.png', 'name' => 'Wide-Full-Width', 'preset' => 'heropreset'),
            'values'   =>
                array(
                    'next_slide_on_window_focus'  => 'off',
                    'delay'                       => '9000',
                    'start_js_after_delay'        => '0',
                    'image_source_type'           => 'full',
                    0                             => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                    'start_with_slide'            => '1',
                    'stop_on_hover'               => 'off',
                    'stop_slider'                 => 'on',
                    'stop_after_loops'            => '0',
                    'stop_at_slide'               => '1',
                    'shuffle'                     => 'off',
                    'viewport_start'              => 'wait',
                    'viewport_area'               => '80',
                    'enable_progressbar'          => 'off',
                    'background_dotted_overlay'   => 'none',
                    'background_color'            => 'transparent',
                    'padding'                     => '0',
                    'show_background_image'       => 'off',
                    'background_image'            => '',
                    'bg_fit'                      => 'cover',
                    'bg_repeat'                   => 'no-repeat',
                    'bg_position'                 => 'center center',
                    'position'                    => 'center',
                    'use_spinner'                 => '-1',
                    'spinner_color'               => '#FFFFFF',
                    'enable_arrows'               => 'off',
                    'navigation_arrow_style'      => 'navbar',
                    'arrows_always_on'            => 'false',
                    'hide_arrows'                 => '200',
                    'hide_arrows_mobile'          => '1200',
                    'hide_arrows_on_mobile'       => 'off',
                    'arrows_under_hidden'         => '600',
                    'hide_arrows_over'            => 'off',
                    'arrows_over_hidden'          => '0',
                    'leftarrow_align_hor'         => 'left',
                    'leftarrow_align_vert'        => 'bottom',
                    'leftarrow_offset_hor'        => '0',
                    'leftarrow_offset_vert'       => '0',
                    'rightarrow_align_hor'        => 'left',
                    'rightarrow_align_vert'       => 'bottom',
                    'rightarrow_offset_hor'       => '40',
                    'rightarrow_offset_vert'      => '0',
                    'enable_bullets'              => 'on',
                    'navigation_bullets_style'    => 'round-old',
                    'bullets_space'               => '5',
                    'bullets_direction'           => 'vertical',
                    'bullets_always_on'           => 'true',
                    'hide_bullets'                => '200',
                    'hide_bullets_mobile'         => '1200',
                    'hide_bullets_on_mobile'      => 'on',
                    'bullets_under_hidden'        => '600',
                    'hide_bullets_over'           => 'off',
                    'bullets_over_hidden'         => '0',
                    'bullets_align_hor'           => 'right',
                    'bullets_align_vert'          => 'center',
                    'bullets_offset_hor'          => '30',
                    'bullets_offset_vert'         => '0',
                    'enable_thumbnails'           => 'off',
                    'thumbnails_padding'          => '5',
                    'span_thumbnails_wrapper'     => 'off',
                    'thumbnails_wrapper_color'    => 'transparent',
                    'thumbnails_wrapper_opacity'  => '100',
                    'thumbnails_style'            => 'navbar',
                    'thumb_amount'                => '5',
                    'thumbnails_space'            => '5',
                    'thumbnail_direction'         => 'vertical',
                    'thumb_width'                 => '50',
                    'thumb_height'                => '50',
                    'thumb_width_min'             => '50',
                    'thumbs_always_on'            => 'false',
                    'hide_thumbs'                 => '200',
                    'hide_thumbs_mobile'          => '1200',
                    'hide_thumbs_on_mobile'       => 'on',
                    'thumbs_under_hidden'         => '778',
                    'hide_thumbs_over'            => 'off',
                    'thumbs_over_hidden'          => '0',
                    'thumbnails_inner_outer'      => 'inner',
                    'thumbnails_align_hor'        => 'right',
                    'thumbnails_align_vert'       => 'center',
                    'thumbnails_offset_hor'       => '20',
                    'thumbnails_offset_vert'      => '0',
                    'enable_tabs'                 => 'off',
                    'tabs_padding'                => '5',
                    'span_tabs_wrapper'           => 'off',
                    'tabs_wrapper_color'          => 'transparent',
                    'tabs_wrapper_opacity'        => '5',
                    'tabs_style'                  => '',
                    'tabs_amount'                 => '5',
                    'tabs_space'                  => '5',
                    'tabs_direction'              => 'horizontal',
                    'tabs_width'                  => '100',
                    'tabs_height'                 => '50',
                    'tabs_width_min'              => '100',
                    'tabs_always_on'              => 'false',
                    'hide_tabs'                   => '200',
                    'hide_tabs_mobile'            => '1200',
                    'hide_tabs_on_mobile'         => 'off',
                    'tabs_under_hidden'           => '0',
                    'hide_tabs_over'              => 'off',
                    'tabs_over_hidden'            => '0',
                    'tabs_inner_outer'            => 'inner',
                    'tabs_align_hor'              => 'center',
                    'tabs_align_vert'             => 'bottom',
                    'tabs_offset_hor'             => '0',
                    'tabs_offset_vert'            => '20',
                    'touchenabled'                => 'on',
                    'drag_block_vertical'         => 'off',
                    'swipe_velocity'              => '75',
                    'swipe_min_touches'           => '50',
                    'swipe_direction'             => 'horizontal',
                    'keyboard_navigation'         => 'off',
                    'keyboard_direction'          => 'horizontal',
                    'mousescroll_navigation'      => 'off',
                    'carousel_infinity'           => 'off',
                    'carousel_space'              => '0',
                    'carousel_borderr'            => '0',
                    'carousel_borderr_unit'       => 'px',
                    'carousel_padding_top'        => '0',
                    'carousel_padding_bottom'     => '0',
                    'carousel_maxitems'           => '3',
                    'carousel_stretch'            => 'off',
                    'carousel_fadeout'            => 'on',
                    'carousel_varyfade'           => 'off',
                    'carousel_rotation'           => 'off',
                    'carousel_varyrotate'         => 'off',
                    'carousel_maxrotation'        => '0',
                    'carousel_scale'              => 'off',
                    'carousel_varyscale'          => 'off',
                    'carousel_scaledown'          => '50',
                    'carousel_hposition'          => 'center',
                    'carousel_vposition'          => 'center',
                    'use_parallax'                => 'on',
                    'disable_parallax_mobile'     => 'off',
                    'parallax_type'               => 'mouse',
                    'parallax_origo'              => 'slidercenter',
                    'parallax_speed'              => '2000',
                    'parallax_level_1'            => '2',
                    'parallax_level_2'            => '3',
                    'parallax_level_3'            => '4',
                    'parallax_level_4'            => '5',
                    'parallax_level_5'            => '6',
                    'parallax_level_6'            => '7',
                    'parallax_level_7'            => '12',
                    'parallax_level_8'            => '16',
                    'parallax_level_9'            => '10',
                    'parallax_level_10'           => '50',
                    'lazy_load_type'              => 'smart',
                    'seo_optimization'            => 'none',
                    'simplify_ie8_ios4'           => 'off',
                    'show_alternative_type'       => 'off',
                    'show_alternate_image'        => '',
                    'jquery_noconflict'           => 'off',
                    'js_to_body'                  => 'false',
                    'output_type'                 => 'none',
                    'jquery_debugmode'            => 'off',
                    'slider_type'                 => 'fullwidth',
                    'width'                       => '1400',
                    'width_notebook'              => '1240',
                    'width_tablet'                => '778',
                    'width_mobile'                => '480',
                    'height'                      => '600',
                    'height_notebook'             => '500',
                    'height_tablet'               => '400',
                    'height_mobile'               => '400',
                    'enable_custom_size_notebook' => 'on',
                    'enable_custom_size_tablet'   => 'on',
                    'enable_custom_size_iphone'   => 'on',
                    'main_overflow_hidden'        => 'off',
                    'auto_height'                 => 'off',
                    'min_height'                  => '',
                    'custom_javascript'           => '',
                    'custom_css'                  => '',
                ),
        );
        $presets[] = array(
            'settings' =>
                array('class' => '', 'image' => RevSliderFunctions::asset('') . 'admin/images/sliderpresets/wide_fullscreen.png', 'name' => 'Regular-Full-Screen', 'preset' => 'heropreset'),
            'values'   =>
                array(
                    'next_slide_on_window_focus'  => 'off',
                    'delay'                       => '9000',
                    'start_js_after_delay'        => '0',
                    'image_source_type'           => 'full',
                    0                             => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                    'start_with_slide'            => '1',
                    'stop_on_hover'               => 'off',
                    'stop_slider'                 => 'on',
                    'stop_after_loops'            => '0',
                    'stop_at_slide'               => '1',
                    'shuffle'                     => 'off',
                    'viewport_start'              => 'wait',
                    'viewport_area'               => '80',
                    'enable_progressbar'          => 'off',
                    'background_dotted_overlay'   => 'none',
                    'background_color'            => 'transparent',
                    'padding'                     => '0',
                    'show_background_image'       => 'off',
                    'background_image'            => '',
                    'bg_fit'                      => 'cover',
                    'bg_repeat'                   => 'no-repeat',
                    'bg_position'                 => 'center center',
                    'position'                    => 'center',
                    'use_spinner'                 => '-1',
                    'spinner_color'               => '#FFFFFF',
                    'enable_arrows'               => 'off',
                    'navigation_arrow_style'      => 'navbar',
                    'arrows_always_on'            => 'false',
                    'hide_arrows'                 => '200',
                    'hide_arrows_mobile'          => '1200',
                    'hide_arrows_on_mobile'       => 'off',
                    'arrows_under_hidden'         => '600',
                    'hide_arrows_over'            => 'off',
                    'arrows_over_hidden'          => '0',
                    'leftarrow_align_hor'         => 'left',
                    'leftarrow_align_vert'        => 'bottom',
                    'leftarrow_offset_hor'        => '0',
                    'leftarrow_offset_vert'       => '0',
                    'rightarrow_align_hor'        => 'left',
                    'rightarrow_align_vert'       => 'bottom',
                    'rightarrow_offset_hor'       => '40',
                    'rightarrow_offset_vert'      => '0',
                    'enable_bullets'              => 'on',
                    'navigation_bullets_style'    => 'round-old',
                    'bullets_space'               => '5',
                    'bullets_direction'           => 'vertical',
                    'bullets_always_on'           => 'true',
                    'hide_bullets'                => '200',
                    'hide_bullets_mobile'         => '1200',
                    'hide_bullets_on_mobile'      => 'on',
                    'bullets_under_hidden'        => '600',
                    'hide_bullets_over'           => 'off',
                    'bullets_over_hidden'         => '0',
                    'bullets_align_hor'           => 'right',
                    'bullets_align_vert'          => 'center',
                    'bullets_offset_hor'          => '30',
                    'bullets_offset_vert'         => '0',
                    'enable_thumbnails'           => 'off',
                    'thumbnails_padding'          => '5',
                    'span_thumbnails_wrapper'     => 'off',
                    'thumbnails_wrapper_color'    => 'transparent',
                    'thumbnails_wrapper_opacity'  => '100',
                    'thumbnails_style'            => 'navbar',
                    'thumb_amount'                => '5',
                    'thumbnails_space'            => '5',
                    'thumbnail_direction'         => 'vertical',
                    'thumb_width'                 => '50',
                    'thumb_height'                => '50',
                    'thumb_width_min'             => '50',
                    'thumbs_always_on'            => 'false',
                    'hide_thumbs'                 => '200',
                    'hide_thumbs_mobile'          => '1200',
                    'hide_thumbs_on_mobile'       => 'on',
                    'thumbs_under_hidden'         => '778',
                    'hide_thumbs_over'            => 'off',
                    'thumbs_over_hidden'          => '0',
                    'thumbnails_inner_outer'      => 'inner',
                    'thumbnails_align_hor'        => 'right',
                    'thumbnails_align_vert'       => 'center',
                    'thumbnails_offset_hor'       => '20',
                    'thumbnails_offset_vert'      => '0',
                    'enable_tabs'                 => 'off',
                    'tabs_padding'                => '5',
                    'span_tabs_wrapper'           => 'off',
                    'tabs_wrapper_color'          => 'transparent',
                    'tabs_wrapper_opacity'        => '5',
                    'tabs_style'                  => '',
                    'tabs_amount'                 => '5',
                    'tabs_space'                  => '5',
                    'tabs_direction'              => 'horizontal',
                    'tabs_width'                  => '100',
                    'tabs_height'                 => '50',
                    'tabs_width_min'              => '100',
                    'tabs_always_on'              => 'false',
                    'hide_tabs'                   => '200',
                    'hide_tabs_mobile'            => '1200',
                    'hide_tabs_on_mobile'         => 'off',
                    'tabs_under_hidden'           => '0',
                    'hide_tabs_over'              => 'off',
                    'tabs_over_hidden'            => '0',
                    'tabs_inner_outer'            => 'inner',
                    'tabs_align_hor'              => 'center',
                    'tabs_align_vert'             => 'bottom',
                    'tabs_offset_hor'             => '0',
                    'tabs_offset_vert'            => '20',
                    'touchenabled'                => 'on',
                    'drag_block_vertical'         => 'off',
                    'swipe_velocity'              => '75',
                    'swipe_min_touches'           => '50',
                    'swipe_direction'             => 'horizontal',
                    'keyboard_navigation'         => 'off',
                    'keyboard_direction'          => 'horizontal',
                    'mousescroll_navigation'      => 'off',
                    'carousel_infinity'           => 'off',
                    'carousel_space'              => '0',
                    'carousel_borderr'            => '0',
                    'carousel_borderr_unit'       => 'px',
                    'carousel_padding_top'        => '0',
                    'carousel_padding_bottom'     => '0',
                    'carousel_maxitems'           => '3',
                    'carousel_stretch'            => 'off',
                    'carousel_fadeout'            => 'on',
                    'carousel_varyfade'           => 'off',
                    'carousel_rotation'           => 'off',
                    'carousel_varyrotate'         => 'off',
                    'carousel_maxrotation'        => '0',
                    'carousel_scale'              => 'off',
                    'carousel_varyscale'          => 'off',
                    'carousel_scaledown'          => '50',
                    'carousel_hposition'          => 'center',
                    'carousel_vposition'          => 'center',
                    'use_parallax'                => 'on',
                    'disable_parallax_mobile'     => 'off',
                    'parallax_type'               => 'mouse',
                    'parallax_origo'              => 'slidercenter',
                    'parallax_speed'              => '2000',
                    'parallax_level_1'            => '2',
                    'parallax_level_2'            => '3',
                    'parallax_level_3'            => '4',
                    'parallax_level_4'            => '5',
                    'parallax_level_5'            => '6',
                    'parallax_level_6'            => '7',
                    'parallax_level_7'            => '12',
                    'parallax_level_8'            => '16',
                    'parallax_level_9'            => '10',
                    'parallax_level_10'           => '50',
                    'lazy_load_type'              => 'smart',
                    'seo_optimization'            => 'none',
                    'simplify_ie8_ios4'           => 'off',
                    'show_alternative_type'       => 'off',
                    'show_alternate_image'        => '',
                    'jquery_noconflict'           => 'off',
                    'js_to_body'                  => 'false',
                    'output_type'                 => 'none',
                    'jquery_debugmode'            => 'off',
                    'slider_type'                 => 'fullscreen',
                    'width'                       => '1240',
                    'width_notebook'              => '1024',
                    'width_tablet'                => '778',
                    'width_mobile'                => '480',
                    'height'                      => '868',
                    'height_notebook'             => '768',
                    'height_tablet'               => '960',
                    'height_mobile'               => '720',
                    'enable_custom_size_notebook' => 'on',
                    'enable_custom_size_tablet'   => 'on',
                    'enable_custom_size_iphone'   => 'on',
                    'main_overflow_hidden'        => 'off',
                    'auto_height'                 => 'off',
                    'min_height'                  => '',
                    'custom_javascript'           => '',
                    'custom_css'                  => '',
                ),
        );
        $presets[] = array(
            'settings' =>
                array('class' => '', 'image' => RevSliderFunctions::asset('') . 'admin/images/sliderpresets/wide_fullscreen.png', 'name' => 'Regular-Full-Width', 'preset' => 'heropreset'),
            'values'   =>
                array(
                    'next_slide_on_window_focus'  => 'off',
                    'delay'                       => '9000',
                    'start_js_after_delay'        => '0',
                    'image_source_type'           => 'full',
                    0                             => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                    'start_with_slide'            => '1',
                    'stop_on_hover'               => 'off',
                    'stop_slider'                 => 'on',
                    'stop_after_loops'            => '0',
                    'stop_at_slide'               => '1',
                    'shuffle'                     => 'off',
                    'viewport_start'              => 'wait',
                    'viewport_area'               => '80',
                    'enable_progressbar'          => 'off',
                    'background_dotted_overlay'   => 'none',
                    'background_color'            => 'transparent',
                    'padding'                     => '0',
                    'show_background_image'       => 'off',
                    'background_image'            => '',
                    'bg_fit'                      => 'cover',
                    'bg_repeat'                   => 'no-repeat',
                    'bg_position'                 => 'center center',
                    'position'                    => 'center',
                    'use_spinner'                 => '-1',
                    'spinner_color'               => '#FFFFFF',
                    'enable_arrows'               => 'off',
                    'navigation_arrow_style'      => 'navbar',
                    'arrows_always_on'            => 'false',
                    'hide_arrows'                 => '200',
                    'hide_arrows_mobile'          => '1200',
                    'hide_arrows_on_mobile'       => 'off',
                    'arrows_under_hidden'         => '600',
                    'hide_arrows_over'            => 'off',
                    'arrows_over_hidden'          => '0',
                    'leftarrow_align_hor'         => 'left',
                    'leftarrow_align_vert'        => 'bottom',
                    'leftarrow_offset_hor'        => '0',
                    'leftarrow_offset_vert'       => '0',
                    'rightarrow_align_hor'        => 'left',
                    'rightarrow_align_vert'       => 'bottom',
                    'rightarrow_offset_hor'       => '40',
                    'rightarrow_offset_vert'      => '0',
                    'enable_bullets'              => 'on',
                    'navigation_bullets_style'    => 'round-old',
                    'bullets_space'               => '5',
                    'bullets_direction'           => 'vertical',
                    'bullets_always_on'           => 'true',
                    'hide_bullets'                => '200',
                    'hide_bullets_mobile'         => '1200',
                    'hide_bullets_on_mobile'      => 'on',
                    'bullets_under_hidden'        => '600',
                    'hide_bullets_over'           => 'off',
                    'bullets_over_hidden'         => '0',
                    'bullets_align_hor'           => 'right',
                    'bullets_align_vert'          => 'center',
                    'bullets_offset_hor'          => '30',
                    'bullets_offset_vert'         => '0',
                    'enable_thumbnails'           => 'off',
                    'thumbnails_padding'          => '5',
                    'span_thumbnails_wrapper'     => 'off',
                    'thumbnails_wrapper_color'    => 'transparent',
                    'thumbnails_wrapper_opacity'  => '100',
                    'thumbnails_style'            => 'navbar',
                    'thumb_amount'                => '5',
                    'thumbnails_space'            => '5',
                    'thumbnail_direction'         => 'vertical',
                    'thumb_width'                 => '50',
                    'thumb_height'                => '50',
                    'thumb_width_min'             => '50',
                    'thumbs_always_on'            => 'false',
                    'hide_thumbs'                 => '200',
                    'hide_thumbs_mobile'          => '1200',
                    'hide_thumbs_on_mobile'       => 'on',
                    'thumbs_under_hidden'         => '778',
                    'hide_thumbs_over'            => 'off',
                    'thumbs_over_hidden'          => '0',
                    'thumbnails_inner_outer'      => 'inner',
                    'thumbnails_align_hor'        => 'right',
                    'thumbnails_align_vert'       => 'center',
                    'thumbnails_offset_hor'       => '20',
                    'thumbnails_offset_vert'      => '0',
                    'enable_tabs'                 => 'off',
                    'tabs_padding'                => '5',
                    'span_tabs_wrapper'           => 'off',
                    'tabs_wrapper_color'          => 'transparent',
                    'tabs_wrapper_opacity'        => '5',
                    'tabs_style'                  => '',
                    'tabs_amount'                 => '5',
                    'tabs_space'                  => '5',
                    'tabs_direction'              => 'horizontal',
                    'tabs_width'                  => '100',
                    'tabs_height'                 => '50',
                    'tabs_width_min'              => '100',
                    'tabs_always_on'              => 'false',
                    'hide_tabs'                   => '200',
                    'hide_tabs_mobile'            => '1200',
                    'hide_tabs_on_mobile'         => 'off',
                    'tabs_under_hidden'           => '0',
                    'hide_tabs_over'              => 'off',
                    'tabs_over_hidden'            => '0',
                    'tabs_inner_outer'            => 'inner',
                    'tabs_align_hor'              => 'center',
                    'tabs_align_vert'             => 'bottom',
                    'tabs_offset_hor'             => '0',
                    'tabs_offset_vert'            => '20',
                    'touchenabled'                => 'on',
                    'drag_block_vertical'         => 'off',
                    'swipe_velocity'              => '75',
                    'swipe_min_touches'           => '50',
                    'swipe_direction'             => 'horizontal',
                    'keyboard_navigation'         => 'off',
                    'keyboard_direction'          => 'horizontal',
                    'mousescroll_navigation'      => 'off',
                    'carousel_infinity'           => 'off',
                    'carousel_space'              => '0',
                    'carousel_borderr'            => '0',
                    'carousel_borderr_unit'       => 'px',
                    'carousel_padding_top'        => '0',
                    'carousel_padding_bottom'     => '0',
                    'carousel_maxitems'           => '3',
                    'carousel_stretch'            => 'off',
                    'carousel_fadeout'            => 'on',
                    'carousel_varyfade'           => 'off',
                    'carousel_rotation'           => 'off',
                    'carousel_varyrotate'         => 'off',
                    'carousel_maxrotation'        => '0',
                    'carousel_scale'              => 'off',
                    'carousel_varyscale'          => 'off',
                    'carousel_scaledown'          => '50',
                    'carousel_hposition'          => 'center',
                    'carousel_vposition'          => 'center',
                    'use_parallax'                => 'on',
                    'disable_parallax_mobile'     => 'off',
                    'parallax_type'               => 'mouse',
                    'parallax_origo'              => 'slidercenter',
                    'parallax_speed'              => '2000',
                    'parallax_level_1'            => '2',
                    'parallax_level_2'            => '3',
                    'parallax_level_3'            => '4',
                    'parallax_level_4'            => '5',
                    'parallax_level_5'            => '6',
                    'parallax_level_6'            => '7',
                    'parallax_level_7'            => '12',
                    'parallax_level_8'            => '16',
                    'parallax_level_9'            => '10',
                    'parallax_level_10'           => '50',
                    'lazy_load_type'              => 'smart',
                    'seo_optimization'            => 'none',
                    'simplify_ie8_ios4'           => 'off',
                    'show_alternative_type'       => 'off',
                    'show_alternate_image'        => '',
                    'jquery_noconflict'           => 'off',
                    'js_to_body'                  => 'false',
                    'output_type'                 => 'none',
                    'jquery_debugmode'            => 'off',
                    'slider_type'                 => 'fullwidth',
                    'width'                       => '1240',
                    'width_notebook'              => '1024',
                    'width_tablet'                => '778',
                    'width_mobile'                => '480',
                    'height'                      => '600',
                    'height_notebook'             => '500',
                    'height_tablet'               => '400',
                    'height_mobile'               => '300',
                    'enable_custom_size_notebook' => 'on',
                    'enable_custom_size_tablet'   => 'on',
                    'enable_custom_size_iphone'   => 'on',
                    'main_overflow_hidden'        => 'off',
                    'auto_height'                 => 'off',
                    'min_height'                  => '',
                    'custom_javascript'           => '',
                    'custom_css'                  => '',
                ),
        );
        $presets[] = array(
            'settings' =>
                array('class' => '', 'image' => RevSliderFunctions::asset('') . 'admin/images/sliderpresets/cover_carousel_thumbs.png', 'name' => 'Cover-Flow-Thumbs', 'preset' => 'carouselpreset'),
            'values'   =>
                array(
                    'next_slide_on_window_focus'  => 'off',
                    'delay'                       => '9000',
                    'start_js_after_delay'        => '0',
                    'image_source_type'           => 'full',
                    0                             => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                    'start_with_slide'            => '1',
                    'stop_on_hover'               => 'off',
                    'stop_slider'                 => 'on',
                    'stop_after_loops'            => '0',
                    'stop_at_slide'               => '1',
                    'shuffle'                     => 'off',
                    'viewport_start'              => 'wait',
                    'viewport_area'               => '80',
                    'enable_progressbar'          => 'on',
                    'background_dotted_overlay'   => 'none',
                    'background_color'            => 'transparent',
                    'padding'                     => '0',
                    'show_background_image'       => 'off',
                    'background_image'            => '',
                    'bg_fit'                      => 'cover',
                    'bg_repeat'                   => 'no-repeat',
                    'bg_position'                 => 'center center',
                    'position'                    => 'center',
                    'use_spinner'                 => '-1',
                    'spinner_color'               => '#FFFFFF',
                    'enable_arrows'               => 'on',
                    'navigation_arrow_style'      => 'navbar-old',
                    'arrows_always_on'            => 'false',
                    'hide_arrows'                 => '200',
                    'hide_arrows_mobile'          => '1200',
                    'hide_arrows_on_mobile'       => 'off',
                    'arrows_under_hidden'         => '600',
                    'hide_arrows_over'            => 'off',
                    'arrows_over_hidden'          => '0',
                    'leftarrow_align_hor'         => 'left',
                    'leftarrow_align_vert'        => 'center',
                    'leftarrow_offset_hor'        => '30',
                    'leftarrow_offset_vert'       => '0',
                    'rightarrow_align_hor'        => 'right',
                    'rightarrow_align_vert'       => 'center',
                    'rightarrow_offset_hor'       => '30',
                    'rightarrow_offset_vert'      => '0',
                    'enable_bullets'              => 'off',
                    'navigation_bullets_style'    => 'round-old',
                    'bullets_space'               => '5',
                    'bullets_direction'           => 'horizontal',
                    'bullets_always_on'           => 'true',
                    'hide_bullets'                => '200',
                    'hide_bullets_mobile'         => '1200',
                    'hide_bullets_on_mobile'      => 'on',
                    'bullets_under_hidden'        => '600',
                    'hide_bullets_over'           => 'off',
                    'bullets_over_hidden'         => '0',
                    'bullets_align_hor'           => 'center',
                    'bullets_align_vert'          => 'bottom',
                    'bullets_offset_hor'          => '0',
                    'bullets_offset_vert'         => '30',
                    'enable_thumbnails'           => 'on',
                    'thumbnails_padding'          => '20',
                    'span_thumbnails_wrapper'     => 'on',
                    'thumbnails_wrapper_color'    => '#000000',
                    'thumbnails_wrapper_opacity'  => '15',
                    'thumbnails_style'            => 'navbar',
                    'thumb_amount'                => '9',
                    'thumbnails_space'            => '10',
                    'thumbnail_direction'         => 'horizontal',
                    'thumb_width'                 => '60',
                    'thumb_height'                => '60',
                    'thumb_width_min'             => '60',
                    'thumbs_always_on'            => 'false',
                    'hide_thumbs'                 => '200',
                    'hide_thumbs_mobile'          => '1200',
                    'hide_thumbs_on_mobile'       => 'off',
                    'thumbs_under_hidden'         => '0',
                    'hide_thumbs_over'            => 'off',
                    'thumbs_over_hidden'          => '0',
                    'thumbnails_inner_outer'      => 'outer-bottom',
                    'thumbnails_align_hor'        => 'center',
                    'thumbnails_align_vert'       => 'bottom',
                    'thumbnails_offset_hor'       => '0',
                    'thumbnails_offset_vert'      => '0',
                    'enable_tabs'                 => 'off',
                    'tabs_padding'                => '5',
                    'span_tabs_wrapper'           => 'off',
                    'tabs_wrapper_color'          => 'transparent',
                    'tabs_wrapper_opacity'        => '5',
                    'tabs_style'                  => '',
                    'tabs_amount'                 => '5',
                    'tabs_space'                  => '5',
                    'tabs_direction'              => 'horizontal',
                    'tabs_width'                  => '100',
                    'tabs_height'                 => '50',
                    'tabs_width_min'              => '100',
                    'tabs_always_on'              => 'false',
                    'hide_tabs'                   => '200',
                    'hide_tabs_mobile'            => '1200',
                    'hide_tabs_on_mobile'         => 'off',
                    'tabs_under_hidden'           => '0',
                    'hide_tabs_over'              => 'off',
                    'tabs_over_hidden'            => '0',
                    'tabs_inner_outer'            => 'inner',
                    'tabs_align_hor'              => 'center',
                    'tabs_align_vert'             => 'bottom',
                    'tabs_offset_hor'             => '0',
                    'tabs_offset_vert'            => '20',
                    'touchenabled'                => 'on',
                    'drag_block_vertical'         => 'off',
                    'swipe_velocity'              => '75',
                    'swipe_min_touches'           => '50',
                    'swipe_direction'             => 'horizontal',
                    'keyboard_navigation'         => 'off',
                    'keyboard_direction'          => 'horizontal',
                    'mousescroll_navigation'      => 'off',
                    'carousel_infinity'           => 'off',
                    'carousel_space'              => '-150',
                    'carousel_borderr'            => '0',
                    'carousel_borderr_unit'       => 'px',
                    'carousel_padding_top'        => '0',
                    'carousel_padding_bottom'     => '0',
                    'carousel_maxitems'           => '5',
                    'carousel_stretch'            => 'off',
                    'carousel_fadeout'            => 'on',
                    'carousel_varyfade'           => 'on',
                    'carousel_rotation'           => 'on',
                    'carousel_varyrotate'         => 'on',
                    'carousel_maxrotation'        => '65',
                    'carousel_scale'              => 'on',
                    'carousel_varyscale'          => 'off',
                    'carousel_scaledown'          => '55',
                    'carousel_hposition'          => 'center',
                    'carousel_vposition'          => 'center',
                    'use_parallax'                => 'on',
                    'disable_parallax_mobile'     => 'off',
                    'parallax_type'               => 'mouse',
                    'parallax_origo'              => 'slidercenter',
                    'parallax_speed'              => '2000',
                    'parallax_level_1'            => '2',
                    'parallax_level_2'            => '3',
                    'parallax_level_3'            => '4',
                    'parallax_level_4'            => '5',
                    'parallax_level_5'            => '6',
                    'parallax_level_6'            => '7',
                    'parallax_level_7'            => '12',
                    'parallax_level_8'            => '16',
                    'parallax_level_9'            => '10',
                    'parallax_level_10'           => '50',
                    'lazy_load_type'              => 'smart',
                    'seo_optimization'            => 'none',
                    'simplify_ie8_ios4'           => 'off',
                    'show_alternative_type'       => 'off',
                    'show_alternate_image'        => '',
                    'jquery_noconflict'           => 'off',
                    'js_to_body'                  => 'false',
                    'output_type'                 => 'none',
                    'jquery_debugmode'            => 'off',
                    'slider_type'                 => 'fullwidth',
                    'width'                       => '600',
                    'width_notebook'              => '600',
                    'width_tablet'                => '600',
                    'width_mobile'                => '600',
                    'height'                      => '600',
                    'height_notebook'             => '600',
                    'height_tablet'               => '600',
                    'height_mobile'               => '600',
                    'enable_custom_size_notebook' => 'off',
                    'enable_custom_size_tablet'   => 'off',
                    'enable_custom_size_iphone'   => 'off',
                    'main_overflow_hidden'        => 'off',
                    'auto_height'                 => 'off',
                    'min_height'                  => '',
                    'custom_css'                  => '',
                    'custom_javascript'           => '',
                ),
        );
        $presets[] = array(
            'settings' =>
                array('class' => '', 'image' => RevSliderFunctions::asset('') . 'admin/images/sliderpresets/cover_carousel_endless.png', 'name' => 'Cover-Flow-Infinite', 'preset' => 'carouselpreset'),
            'values'   =>
                array(
                    'next_slide_on_window_focus'  => 'off',
                    'delay'                       => '9000',
                    'start_js_after_delay'        => '0',
                    'image_source_type'           => 'full',
                    0                             => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                    'start_with_slide'            => '1',
                    'stop_on_hover'               => 'off',
                    'stop_slider'                 => 'on',
                    'stop_after_loops'            => '0',
                    'stop_at_slide'               => '1',
                    'shuffle'                     => 'off',
                    'viewport_start'              => 'wait',
                    'viewport_area'               => '80',
                    'enable_progressbar'          => 'on',
                    'background_dotted_overlay'   => 'none',
                    'background_color'            => 'transparent',
                    'padding'                     => '0',
                    'show_background_image'       => 'off',
                    'background_image'            => '',
                    'bg_fit'                      => 'cover',
                    'bg_repeat'                   => 'no-repeat',
                    'bg_position'                 => 'center center',
                    'position'                    => 'center',
                    'use_spinner'                 => '-1',
                    'spinner_color'               => '#FFFFFF',
                    'enable_arrows'               => 'on',
                    'navigation_arrow_style'      => 'round',
                    'arrows_always_on'            => 'false',
                    'hide_arrows'                 => '200',
                    'hide_arrows_mobile'          => '1200',
                    'hide_arrows_on_mobile'       => 'off',
                    'arrows_under_hidden'         => '600',
                    'hide_arrows_over'            => 'off',
                    'arrows_over_hidden'          => '0',
                    'leftarrow_align_hor'         => 'left',
                    'leftarrow_align_vert'        => 'center',
                    'leftarrow_offset_hor'        => '30',
                    'leftarrow_offset_vert'       => '0',
                    'rightarrow_align_hor'        => 'right',
                    'rightarrow_align_vert'       => 'center',
                    'rightarrow_offset_hor'       => '30',
                    'rightarrow_offset_vert'      => '0',
                    'enable_bullets'              => 'off',
                    'navigation_bullets_style'    => 'round-old',
                    'bullets_space'               => '5',
                    'bullets_direction'           => 'horizontal',
                    'bullets_always_on'           => 'true',
                    'hide_bullets'                => '200',
                    'hide_bullets_mobile'         => '1200',
                    'hide_bullets_on_mobile'      => 'on',
                    'bullets_under_hidden'        => '600',
                    'hide_bullets_over'           => 'off',
                    'bullets_over_hidden'         => '0',
                    'bullets_align_hor'           => 'center',
                    'bullets_align_vert'          => 'bottom',
                    'bullets_offset_hor'          => '0',
                    'bullets_offset_vert'         => '30',
                    'enable_thumbnails'           => 'off',
                    'thumbnails_padding'          => '20',
                    'span_thumbnails_wrapper'     => 'on',
                    'thumbnails_wrapper_color'    => '#000000',
                    'thumbnails_wrapper_opacity'  => '15',
                    'thumbnails_style'            => 'navbar',
                    'thumb_amount'                => '9',
                    'thumbnails_space'            => '10',
                    'thumbnail_direction'         => 'horizontal',
                    'thumb_width'                 => '60',
                    'thumb_height'                => '60',
                    'thumb_width_min'             => '60',
                    'thumbs_always_on'            => 'false',
                    'hide_thumbs'                 => '200',
                    'hide_thumbs_mobile'          => '1200',
                    'hide_thumbs_on_mobile'       => 'off',
                    'thumbs_under_hidden'         => '0',
                    'hide_thumbs_over'            => 'off',
                    'thumbs_over_hidden'          => '0',
                    'thumbnails_inner_outer'      => 'outer-bottom',
                    'thumbnails_align_hor'        => 'center',
                    'thumbnails_align_vert'       => 'bottom',
                    'thumbnails_offset_hor'       => '0',
                    'thumbnails_offset_vert'      => '0',
                    'enable_tabs'                 => 'off',
                    'tabs_padding'                => '5',
                    'span_tabs_wrapper'           => 'off',
                    'tabs_wrapper_color'          => 'transparent',
                    'tabs_wrapper_opacity'        => '5',
                    'tabs_style'                  => '',
                    'tabs_amount'                 => '5',
                    'tabs_space'                  => '5',
                    'tabs_direction'              => 'horizontal',
                    'tabs_width'                  => '100',
                    'tabs_height'                 => '50',
                    'tabs_width_min'              => '100',
                    'tabs_always_on'              => 'false',
                    'hide_tabs'                   => '200',
                    'hide_tabs_mobile'            => '1200',
                    'hide_tabs_on_mobile'         => 'off',
                    'tabs_under_hidden'           => '0',
                    'hide_tabs_over'              => 'off',
                    'tabs_over_hidden'            => '0',
                    'tabs_inner_outer'            => 'inner',
                    'tabs_align_hor'              => 'center',
                    'tabs_align_vert'             => 'bottom',
                    'tabs_offset_hor'             => '0',
                    'tabs_offset_vert'            => '20',
                    'touchenabled'                => 'on',
                    'drag_block_vertical'         => 'off',
                    'swipe_velocity'              => '75',
                    'swipe_min_touches'           => '50',
                    'swipe_direction'             => 'horizontal',
                    'keyboard_navigation'         => 'off',
                    'keyboard_direction'          => 'horizontal',
                    'mousescroll_navigation'      => 'off',
                    'carousel_infinity'           => 'on',
                    'carousel_space'              => '-150',
                    'carousel_borderr'            => '0',
                    'carousel_borderr_unit'       => 'px',
                    'carousel_padding_top'        => '0',
                    'carousel_padding_bottom'     => '0',
                    'carousel_maxitems'           => '3',
                    'carousel_stretch'            => 'off',
                    'carousel_fadeout'            => 'on',
                    'carousel_varyfade'           => 'on',
                    'carousel_rotation'           => 'on',
                    'carousel_varyrotate'         => 'on',
                    'carousel_maxrotation'        => '65',
                    'carousel_scale'              => 'on',
                    'carousel_varyscale'          => 'off',
                    'carousel_scaledown'          => '55',
                    'carousel_hposition'          => 'center',
                    'carousel_vposition'          => 'center',
                    'use_parallax'                => 'on',
                    'disable_parallax_mobile'     => 'off',
                    'parallax_type'               => 'mouse',
                    'parallax_origo'              => 'slidercenter',
                    'parallax_speed'              => '2000',
                    'parallax_level_1'            => '2',
                    'parallax_level_2'            => '3',
                    'parallax_level_3'            => '4',
                    'parallax_level_4'            => '5',
                    'parallax_level_5'            => '6',
                    'parallax_level_6'            => '7',
                    'parallax_level_7'            => '12',
                    'parallax_level_8'            => '16',
                    'parallax_level_9'            => '10',
                    'parallax_level_10'           => '50',
                    'lazy_load_type'              => 'smart',
                    'seo_optimization'            => 'none',
                    'simplify_ie8_ios4'           => 'off',
                    'show_alternative_type'       => 'off',
                    'show_alternate_image'        => '',
                    'jquery_noconflict'           => 'off',
                    'js_to_body'                  => 'false',
                    'output_type'                 => 'none',
                    'jquery_debugmode'            => 'off',
                    'slider_type'                 => 'fullwidth',
                    'width'                       => '600',
                    'width_notebook'              => '600',
                    'width_tablet'                => '600',
                    'width_mobile'                => '600',
                    'height'                      => '600',
                    'height_notebook'             => '600',
                    'height_tablet'               => '600',
                    'height_mobile'               => '600',
                    'enable_custom_size_notebook' => 'off',
                    'enable_custom_size_tablet'   => 'off',
                    'enable_custom_size_iphone'   => 'off',
                    'main_overflow_hidden'        => 'off',
                    'auto_height'                 => 'off',
                    'min_height'                  => '',
                    'custom_css'                  => '',
                    'custom_javascript'           => '',
                ),
        );
        $presets[] = array(
            'settings' =>
                array('class' => '', 'image' => RevSliderFunctions::asset('') . 'admin/images/sliderpresets/flat_carousel_thumbs.png', 'name' => 'Flat-Infinite-Thumbs', 'preset' => 'carouselpreset'),
            'values'   =>
                array(
                    'next_slide_on_window_focus'  => 'off',
                    'delay'                       => '9000',
                    'start_js_after_delay'        => '0',
                    'image_source_type'           => 'full',
                    0                             => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                    'start_with_slide'            => '1',
                    'stop_on_hover'               => 'off',
                    'stop_slider'                 => 'on',
                    'stop_after_loops'            => '0',
                    'stop_at_slide'               => '1',
                    'shuffle'                     => 'off',
                    'viewport_start'              => 'wait',
                    'viewport_area'               => '80',
                    'enable_progressbar'          => 'on',
                    'background_dotted_overlay'   => 'none',
                    'background_color'            => '#111111',
                    'padding'                     => '0',
                    'show_background_image'       => 'off',
                    'background_image'            => '',
                    'bg_fit'                      => 'cover',
                    'bg_repeat'                   => 'no-repeat',
                    'bg_position'                 => 'center center',
                    'position'                    => 'center',
                    'use_spinner'                 => '-1',
                    'spinner_color'               => '#FFFFFF',
                    'enable_arrows'               => 'on',
                    'navigation_arrow_style'      => 'navbar',
                    'arrows_always_on'            => 'false',
                    'hide_arrows'                 => '200',
                    'hide_arrows_mobile'          => '1200',
                    'hide_arrows_on_mobile'       => 'off',
                    'arrows_under_hidden'         => '600',
                    'hide_arrows_over'            => 'off',
                    'arrows_over_hidden'          => '0',
                    'leftarrow_align_hor'         => 'left',
                    'leftarrow_align_vert'        => 'center',
                    'leftarrow_offset_hor'        => '30',
                    'leftarrow_offset_vert'       => '0',
                    'rightarrow_align_hor'        => 'right',
                    'rightarrow_align_vert'       => 'center',
                    'rightarrow_offset_hor'       => '30',
                    'rightarrow_offset_vert'      => '0',
                    'enable_bullets'              => 'off',
                    'navigation_bullets_style'    => 'round-old',
                    'bullets_space'               => '5',
                    'bullets_direction'           => 'horizontal',
                    'bullets_always_on'           => 'true',
                    'hide_bullets'                => '200',
                    'hide_bullets_mobile'         => '1200',
                    'hide_bullets_on_mobile'      => 'on',
                    'bullets_under_hidden'        => '600',
                    'hide_bullets_over'           => 'off',
                    'bullets_over_hidden'         => '0',
                    'bullets_align_hor'           => 'center',
                    'bullets_align_vert'          => 'bottom',
                    'bullets_offset_hor'          => '0',
                    'bullets_offset_vert'         => '30',
                    'enable_thumbnails'           => 'on',
                    'thumbnails_padding'          => '20',
                    'span_thumbnails_wrapper'     => 'on',
                    'thumbnails_wrapper_color'    => '#222222',
                    'thumbnails_wrapper_opacity'  => '100',
                    'thumbnails_style'            => 'navbar',
                    'thumb_amount'                => '9',
                    'thumbnails_space'            => '10',
                    'thumbnail_direction'         => 'horizontal',
                    'thumb_width'                 => '60',
                    'thumb_height'                => '60',
                    'thumb_width_min'             => '60',
                    'thumbs_always_on'            => 'false',
                    'hide_thumbs'                 => '200',
                    'hide_thumbs_mobile'          => '1200',
                    'hide_thumbs_on_mobile'       => 'off',
                    'thumbs_under_hidden'         => '0',
                    'hide_thumbs_over'            => 'off',
                    'thumbs_over_hidden'          => '0',
                    'thumbnails_inner_outer'      => 'outer-bottom',
                    'thumbnails_align_hor'        => 'center',
                    'thumbnails_align_vert'       => 'bottom',
                    'thumbnails_offset_hor'       => '0',
                    'thumbnails_offset_vert'      => '0',
                    'enable_tabs'                 => 'off',
                    'tabs_padding'                => '5',
                    'span_tabs_wrapper'           => 'off',
                    'tabs_wrapper_color'          => 'transparent',
                    'tabs_wrapper_opacity'        => '5',
                    'tabs_style'                  => '',
                    'tabs_amount'                 => '5',
                    'tabs_space'                  => '5',
                    'tabs_direction'              => 'horizontal',
                    'tabs_width'                  => '100',
                    'tabs_height'                 => '50',
                    'tabs_width_min'              => '100',
                    'tabs_always_on'              => 'false',
                    'hide_tabs'                   => '200',
                    'hide_tabs_mobile'            => '1200',
                    'hide_tabs_on_mobile'         => 'off',
                    'tabs_under_hidden'           => '0',
                    'hide_tabs_over'              => 'off',
                    'tabs_over_hidden'            => '0',
                    'tabs_inner_outer'            => 'inner',
                    'tabs_align_hor'              => 'center',
                    'tabs_align_vert'             => 'bottom',
                    'tabs_offset_hor'             => '0',
                    'tabs_offset_vert'            => '20',
                    'touchenabled'                => 'on',
                    'drag_block_vertical'         => 'off',
                    'swipe_velocity'              => '75',
                    'swipe_min_touches'           => '50',
                    'swipe_direction'             => 'horizontal',
                    'keyboard_navigation'         => 'off',
                    'keyboard_direction'          => 'horizontal',
                    'mousescroll_navigation'      => 'off',
                    'carousel_infinity'           => 'on',
                    'carousel_space'              => '0',
                    'carousel_borderr'            => '0',
                    'carousel_borderr_unit'       => 'px',
                    'carousel_padding_top'        => '0',
                    'carousel_padding_bottom'     => '0',
                    'carousel_maxitems'           => '3',
                    'carousel_stretch'            => 'off',
                    'carousel_fadeout'            => 'on',
                    'carousel_varyfade'           => 'on',
                    'carousel_rotation'           => 'off',
                    'carousel_varyrotate'         => 'on',
                    'carousel_maxrotation'        => '65',
                    'carousel_scale'              => 'off',
                    'carousel_varyscale'          => 'off',
                    'carousel_scaledown'          => '55',
                    'carousel_hposition'          => 'center',
                    'carousel_vposition'          => 'center',
                    'use_parallax'                => 'on',
                    'disable_parallax_mobile'     => 'off',
                    'parallax_type'               => 'mouse',
                    'parallax_origo'              => 'slidercenter',
                    'parallax_speed'              => '2000',
                    'parallax_level_1'            => '2',
                    'parallax_level_2'            => '3',
                    'parallax_level_3'            => '4',
                    'parallax_level_4'            => '5',
                    'parallax_level_5'            => '6',
                    'parallax_level_6'            => '7',
                    'parallax_level_7'            => '12',
                    'parallax_level_8'            => '16',
                    'parallax_level_9'            => '10',
                    'parallax_level_10'           => '50',
                    'lazy_load_type'              => 'smart',
                    'seo_optimization'            => 'none',
                    'simplify_ie8_ios4'           => 'off',
                    'show_alternative_type'       => 'off',
                    'show_alternate_image'        => '',
                    'jquery_noconflict'           => 'off',
                    'js_to_body'                  => 'false',
                    'output_type'                 => 'none',
                    'jquery_debugmode'            => 'off',
                    'slider_type'                 => 'fullwidth',
                    'width'                       => '720',
                    'width_notebook'              => '720',
                    'width_tablet'                => '720',
                    'width_mobile'                => '720',
                    'height'                      => '405',
                    'height_notebook'             => '405',
                    'height_tablet'               => '405',
                    'height_mobile'               => '405',
                    'enable_custom_size_notebook' => 'off',
                    'enable_custom_size_tablet'   => 'off',
                    'enable_custom_size_iphone'   => 'off',
                    'main_overflow_hidden'        => 'off',
                    'auto_height'                 => 'off',
                    'min_height'                  => '',
                    'custom_css'                  => '',
                    'custom_javascript'           => '',
                ),
        );
        $presets[] = array(
            'settings' =>
                array('class' => '', 'image' => RevSliderFunctions::asset('') . 'admin/images/sliderpresets/flat_carousel.png', 'name' => 'Flat-Infinite', 'preset' => 'carouselpreset'),
            'values'   =>
                array(
                    'next_slide_on_window_focus'  => 'off',
                    'delay'                       => '9000',
                    'start_js_after_delay'        => '0',
                    'image_source_type'           => 'full',
                    0                             => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                    'start_with_slide'            => '1',
                    'stop_on_hover'               => 'off',
                    'stop_slider'                 => 'on',
                    'stop_after_loops'            => '0',
                    'stop_at_slide'               => '1',
                    'shuffle'                     => 'off',
                    'viewport_start'              => 'wait',
                    'viewport_area'               => '80',
                    'enable_progressbar'          => 'on',
                    'background_dotted_overlay'   => 'none',
                    'background_color'            => '#111111',
                    'padding'                     => '0',
                    'show_background_image'       => 'off',
                    'background_image'            => '',
                    'bg_fit'                      => 'cover',
                    'bg_repeat'                   => 'no-repeat',
                    'bg_position'                 => 'center center',
                    'position'                    => 'center',
                    'use_spinner'                 => '-1',
                    'spinner_color'               => '#FFFFFF',
                    'enable_arrows'               => 'on',
                    'navigation_arrow_style'      => 'uranus',
                    'arrows_always_on'            => 'false',
                    'hide_arrows'                 => '200',
                    'hide_arrows_mobile'          => '1200',
                    'hide_arrows_on_mobile'       => 'off',
                    'arrows_under_hidden'         => '600',
                    'hide_arrows_over'            => 'off',
                    'arrows_over_hidden'          => '0',
                    'leftarrow_align_hor'         => 'left',
                    'leftarrow_align_vert'        => 'center',
                    'leftarrow_offset_hor'        => '30',
                    'leftarrow_offset_vert'       => '0',
                    'rightarrow_align_hor'        => 'right',
                    'rightarrow_align_vert'       => 'center',
                    'rightarrow_offset_hor'       => '30',
                    'rightarrow_offset_vert'      => '0',
                    'enable_bullets'              => 'off',
                    'navigation_bullets_style'    => 'round-old',
                    'bullets_space'               => '5',
                    'bullets_direction'           => 'horizontal',
                    'bullets_always_on'           => 'true',
                    'hide_bullets'                => '200',
                    'hide_bullets_mobile'         => '1200',
                    'hide_bullets_on_mobile'      => 'on',
                    'bullets_under_hidden'        => '600',
                    'hide_bullets_over'           => 'off',
                    'bullets_over_hidden'         => '0',
                    'bullets_align_hor'           => 'center',
                    'bullets_align_vert'          => 'bottom',
                    'bullets_offset_hor'          => '0',
                    'bullets_offset_vert'         => '30',
                    'enable_thumbnails'           => 'off',
                    'thumbnails_padding'          => '20',
                    'span_thumbnails_wrapper'     => 'on',
                    'thumbnails_wrapper_color'    => '#222222',
                    'thumbnails_wrapper_opacity'  => '100',
                    'thumbnails_style'            => 'navbar',
                    'thumb_amount'                => '9',
                    'thumbnails_space'            => '10',
                    'thumbnail_direction'         => 'horizontal',
                    'thumb_width'                 => '60',
                    'thumb_height'                => '60',
                    'thumb_width_min'             => '60',
                    'thumbs_always_on'            => 'false',
                    'hide_thumbs'                 => '200',
                    'hide_thumbs_mobile'          => '1200',
                    'hide_thumbs_on_mobile'       => 'off',
                    'thumbs_under_hidden'         => '0',
                    'hide_thumbs_over'            => 'off',
                    'thumbs_over_hidden'          => '0',
                    'thumbnails_inner_outer'      => 'outer-bottom',
                    'thumbnails_align_hor'        => 'center',
                    'thumbnails_align_vert'       => 'bottom',
                    'thumbnails_offset_hor'       => '0',
                    'thumbnails_offset_vert'      => '0',
                    'enable_tabs'                 => 'off',
                    'tabs_padding'                => '5',
                    'span_tabs_wrapper'           => 'off',
                    'tabs_wrapper_color'          => 'transparent',
                    'tabs_wrapper_opacity'        => '5',
                    'tabs_style'                  => '',
                    'tabs_amount'                 => '5',
                    'tabs_space'                  => '5',
                    'tabs_direction'              => 'horizontal',
                    'tabs_width'                  => '100',
                    'tabs_height'                 => '50',
                    'tabs_width_min'              => '100',
                    'tabs_always_on'              => 'false',
                    'hide_tabs'                   => '200',
                    'hide_tabs_mobile'            => '1200',
                    'hide_tabs_on_mobile'         => 'off',
                    'tabs_under_hidden'           => '0',
                    'hide_tabs_over'              => 'off',
                    'tabs_over_hidden'            => '0',
                    'tabs_inner_outer'            => 'inner',
                    'tabs_align_hor'              => 'center',
                    'tabs_align_vert'             => 'bottom',
                    'tabs_offset_hor'             => '0',
                    'tabs_offset_vert'            => '20',
                    'touchenabled'                => 'on',
                    'drag_block_vertical'         => 'off',
                    'swipe_velocity'              => '75',
                    'swipe_min_touches'           => '50',
                    'swipe_direction'             => 'horizontal',
                    'keyboard_navigation'         => 'off',
                    'keyboard_direction'          => 'horizontal',
                    'mousescroll_navigation'      => 'off',
                    'carousel_infinity'           => 'on',
                    'carousel_space'              => '0',
                    'carousel_borderr'            => '0',
                    'carousel_borderr_unit'       => 'px',
                    'carousel_padding_top'        => '0',
                    'carousel_padding_bottom'     => '0',
                    'carousel_maxitems'           => '3',
                    'carousel_stretch'            => 'off',
                    'carousel_fadeout'            => 'on',
                    'carousel_varyfade'           => 'on',
                    'carousel_rotation'           => 'off',
                    'carousel_varyrotate'         => 'on',
                    'carousel_maxrotation'        => '65',
                    'carousel_scale'              => 'off',
                    'carousel_varyscale'          => 'off',
                    'carousel_scaledown'          => '55',
                    'carousel_hposition'          => 'center',
                    'carousel_vposition'          => 'center',
                    'use_parallax'                => 'on',
                    'disable_parallax_mobile'     => 'off',
                    'parallax_type'               => 'mouse',
                    'parallax_origo'              => 'slidercenter',
                    'parallax_speed'              => '2000',
                    'parallax_level_1'            => '2',
                    'parallax_level_2'            => '3',
                    'parallax_level_3'            => '4',
                    'parallax_level_4'            => '5',
                    'parallax_level_5'            => '6',
                    'parallax_level_6'            => '7',
                    'parallax_level_7'            => '12',
                    'parallax_level_8'            => '16',
                    'parallax_level_9'            => '10',
                    'parallax_level_10'           => '50',
                    'lazy_load_type'              => 'smart',
                    'seo_optimization'            => 'none',
                    'simplify_ie8_ios4'           => 'off',
                    'show_alternative_type'       => 'off',
                    'show_alternate_image'        => '',
                    'jquery_noconflict'           => 'off',
                    'js_to_body'                  => 'false',
                    'output_type'                 => 'none',
                    'jquery_debugmode'            => 'off',
                    'slider_type'                 => 'fullwidth',
                    'width'                       => '720',
                    'width_notebook'              => '720',
                    'width_tablet'                => '720',
                    'width_mobile'                => '720',
                    'height'                      => '405',
                    'height_notebook'             => '405',
                    'height_tablet'               => '405',
                    'height_mobile'               => '405',
                    'enable_custom_size_notebook' => 'off',
                    'enable_custom_size_tablet'   => 'off',
                    'enable_custom_size_iphone'   => 'off',
                    'main_overflow_hidden'        => 'off',
                    'auto_height'                 => 'off',
                    'min_height'                  => '',
                    'custom_css'                  => '',
                    'custom_javascript'           => '',
                ),
        );
        $presets[] = array(
            'settings' =>
                array('class' => '', 'image' => RevSliderFunctions::asset('') . 'admin/images/sliderpresets/flat_carousel_thumbs_left.png', 'name' => 'Flat-Thumbs-Left', 'preset' => 'carouselpreset'),
            'values'   =>
                array(
                    'next_slide_on_window_focus'  => 'off',
                    'delay'                       => '9000',
                    'start_js_after_delay'        => '0',
                    'image_source_type'           => 'full',
                    0                             => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                    'start_with_slide'            => '1',
                    'stop_on_hover'               => 'off',
                    'stop_slider'                 => 'on',
                    'stop_after_loops'            => '0',
                    'stop_at_slide'               => '1',
                    'shuffle'                     => 'off',
                    'viewport_start'              => 'wait',
                    'viewport_area'               => '80',
                    'enable_progressbar'          => 'on',
                    'background_dotted_overlay'   => 'none',
                    'background_color'            => '#111111',
                    'padding'                     => '0',
                    'show_background_image'       => 'off',
                    'background_image'            => '',
                    'bg_fit'                      => 'cover',
                    'bg_repeat'                   => 'no-repeat',
                    'bg_position'                 => 'center center',
                    'position'                    => 'center',
                    'use_spinner'                 => '-1',
                    'spinner_color'               => '#FFFFFF',
                    'enable_arrows'               => 'on',
                    'navigation_arrow_style'      => 'uranus',
                    'arrows_always_on'            => 'false',
                    'hide_arrows'                 => '200',
                    'hide_arrows_mobile'          => '1200',
                    'hide_arrows_on_mobile'       => 'off',
                    'arrows_under_hidden'         => '600',
                    'hide_arrows_over'            => 'off',
                    'arrows_over_hidden'          => '0',
                    'leftarrow_align_hor'         => 'left',
                    'leftarrow_align_vert'        => 'center',
                    'leftarrow_offset_hor'        => '30',
                    'leftarrow_offset_vert'       => '0',
                    'rightarrow_align_hor'        => 'right',
                    'rightarrow_align_vert'       => 'center',
                    'rightarrow_offset_hor'       => '30',
                    'rightarrow_offset_vert'      => '0',
                    'enable_bullets'              => 'off',
                    'navigation_bullets_style'    => 'round-old',
                    'bullets_space'               => '5',
                    'bullets_direction'           => 'horizontal',
                    'bullets_always_on'           => 'true',
                    'hide_bullets'                => '200',
                    'hide_bullets_mobile'         => '1200',
                    'hide_bullets_on_mobile'      => 'on',
                    'bullets_under_hidden'        => '600',
                    'hide_bullets_over'           => 'off',
                    'bullets_over_hidden'         => '0',
                    'bullets_align_hor'           => 'center',
                    'bullets_align_vert'          => 'bottom',
                    'bullets_offset_hor'          => '0',
                    'bullets_offset_vert'         => '30',
                    'enable_thumbnails'           => 'on',
                    'thumbnails_padding'          => '20',
                    'span_thumbnails_wrapper'     => 'on',
                    'thumbnails_wrapper_color'    => '#222222',
                    'thumbnails_wrapper_opacity'  => '100',
                    'thumbnails_style'            => 'navbar',
                    'thumb_amount'                => '9',
                    'thumbnails_space'            => '10',
                    'thumbnail_direction'         => 'vertical',
                    'thumb_width'                 => '60',
                    'thumb_height'                => '60',
                    'thumb_width_min'             => '60',
                    'thumbs_always_on'            => 'false',
                    'hide_thumbs'                 => '200',
                    'hide_thumbs_mobile'          => '1200',
                    'hide_thumbs_on_mobile'       => 'off',
                    'thumbs_under_hidden'         => '0',
                    'hide_thumbs_over'            => 'off',
                    'thumbs_over_hidden'          => '0',
                    'thumbnails_inner_outer'      => 'outer-left',
                    'thumbnails_align_hor'        => 'left',
                    'thumbnails_align_vert'       => 'top',
                    'thumbnails_offset_hor'       => '0',
                    'thumbnails_offset_vert'      => '0',
                    'enable_tabs'                 => 'off',
                    'tabs_padding'                => '5',
                    'span_tabs_wrapper'           => 'off',
                    'tabs_wrapper_color'          => 'transparent',
                    'tabs_wrapper_opacity'        => '5',
                    'tabs_style'                  => '',
                    'tabs_amount'                 => '5',
                    'tabs_space'                  => '5',
                    'tabs_direction'              => 'horizontal',
                    'tabs_width'                  => '100',
                    'tabs_height'                 => '50',
                    'tabs_width_min'              => '100',
                    'tabs_always_on'              => 'false',
                    'hide_tabs'                   => '200',
                    'hide_tabs_mobile'            => '1200',
                    'hide_tabs_on_mobile'         => 'off',
                    'tabs_under_hidden'           => '0',
                    'hide_tabs_over'              => 'off',
                    'tabs_over_hidden'            => '0',
                    'tabs_inner_outer'            => 'inner',
                    'tabs_align_hor'              => 'center',
                    'tabs_align_vert'             => 'bottom',
                    'tabs_offset_hor'             => '0',
                    'tabs_offset_vert'            => '20',
                    'touchenabled'                => 'on',
                    'drag_block_vertical'         => 'off',
                    'swipe_velocity'              => '75',
                    'swipe_min_touches'           => '50',
                    'swipe_direction'             => 'horizontal',
                    'keyboard_navigation'         => 'off',
                    'keyboard_direction'          => 'horizontal',
                    'mousescroll_navigation'      => 'off',
                    'carousel_infinity'           => 'on',
                    'carousel_space'              => '0',
                    'carousel_borderr'            => '0',
                    'carousel_borderr_unit'       => 'px',
                    'carousel_padding_top'        => '0',
                    'carousel_padding_bottom'     => '0',
                    'carousel_maxitems'           => '3',
                    'carousel_stretch'            => 'off',
                    'carousel_fadeout'            => 'on',
                    'carousel_varyfade'           => 'on',
                    'carousel_rotation'           => 'off',
                    'carousel_varyrotate'         => 'on',
                    'carousel_maxrotation'        => '65',
                    'carousel_scale'              => 'off',
                    'carousel_varyscale'          => 'off',
                    'carousel_scaledown'          => '55',
                    'carousel_hposition'          => 'center',
                    'carousel_vposition'          => 'center',
                    'use_parallax'                => 'on',
                    'disable_parallax_mobile'     => 'off',
                    'parallax_type'               => 'mouse',
                    'parallax_origo'              => 'slidercenter',
                    'parallax_speed'              => '2000',
                    'parallax_level_1'            => '2',
                    'parallax_level_2'            => '3',
                    'parallax_level_3'            => '4',
                    'parallax_level_4'            => '5',
                    'parallax_level_5'            => '6',
                    'parallax_level_6'            => '7',
                    'parallax_level_7'            => '12',
                    'parallax_level_8'            => '16',
                    'parallax_level_9'            => '10',
                    'parallax_level_10'           => '50',
                    'lazy_load_type'              => 'smart',
                    'seo_optimization'            => 'none',
                    'simplify_ie8_ios4'           => 'off',
                    'show_alternative_type'       => 'off',
                    'show_alternate_image'        => '',
                    'jquery_noconflict'           => 'off',
                    'js_to_body'                  => 'false',
                    'output_type'                 => 'none',
                    'jquery_debugmode'            => 'off',
                    'slider_type'                 => 'fullwidth',
                    'width'                       => '720',
                    'width_notebook'              => '720',
                    'width_tablet'                => '720',
                    'width_mobile'                => '720',
                    'height'                      => '405',
                    'height_notebook'             => '405',
                    'height_tablet'               => '405',
                    'height_mobile'               => '405',
                    'enable_custom_size_notebook' => 'off',
                    'enable_custom_size_tablet'   => 'off',
                    'enable_custom_size_iphone'   => 'off',
                    'main_overflow_hidden'        => 'off',
                    'auto_height'                 => 'off',
                    'min_height'                  => '',
                    'custom_css'                  => '',
                    'custom_javascript'           => '',
                ),
        );
        $presets[] = array(
            'settings' =>
                array('class' => '', 'image' => RevSliderFunctions::asset('') . 'admin/images/sliderpresets/carousel_thumbs_right_fullscreen.png', 'name' => 'Full-Screen-Thumbs-Right', 'preset' => 'carouselpreset'),
            'values'   =>
                array(
                    'next_slide_on_window_focus'  => 'off',
                    'delay'                       => '9000',
                    'start_js_after_delay'        => '0',
                    'image_source_type'           => 'full',
                    0                             => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                    'start_with_slide'            => '1',
                    'stop_on_hover'               => 'off',
                    'stop_slider'                 => 'on',
                    'stop_after_loops'            => '0',
                    'stop_at_slide'               => '1',
                    'shuffle'                     => 'off',
                    'viewport_start'              => 'wait',
                    'viewport_area'               => '80',
                    'enable_progressbar'          => 'on',
                    'background_dotted_overlay'   => 'none',
                    'background_color'            => '#111111',
                    'padding'                     => '0',
                    'show_background_image'       => 'off',
                    'background_image'            => '',
                    'bg_fit'                      => 'cover',
                    'bg_repeat'                   => 'no-repeat',
                    'bg_position'                 => 'center center',
                    'position'                    => 'center',
                    'use_spinner'                 => '-1',
                    'spinner_color'               => '#FFFFFF',
                    'enable_arrows'               => 'on',
                    'navigation_arrow_style'      => 'uranus',
                    'arrows_always_on'            => 'false',
                    'hide_arrows'                 => '200',
                    'hide_arrows_mobile'          => '1200',
                    'hide_arrows_on_mobile'       => 'off',
                    'arrows_under_hidden'         => '600',
                    'hide_arrows_over'            => 'off',
                    'arrows_over_hidden'          => '0',
                    'leftarrow_align_hor'         => 'left',
                    'leftarrow_align_vert'        => 'center',
                    'leftarrow_offset_hor'        => '30',
                    'leftarrow_offset_vert'       => '0',
                    'rightarrow_align_hor'        => 'right',
                    'rightarrow_align_vert'       => 'center',
                    'rightarrow_offset_hor'       => '30',
                    'rightarrow_offset_vert'      => '0',
                    'enable_bullets'              => 'off',
                    'navigation_bullets_style'    => 'round-old',
                    'bullets_space'               => '5',
                    'bullets_direction'           => 'horizontal',
                    'bullets_always_on'           => 'true',
                    'hide_bullets'                => '200',
                    'hide_bullets_mobile'         => '1200',
                    'hide_bullets_on_mobile'      => 'on',
                    'bullets_under_hidden'        => '600',
                    'hide_bullets_over'           => 'off',
                    'bullets_over_hidden'         => '0',
                    'bullets_align_hor'           => 'center',
                    'bullets_align_vert'          => 'bottom',
                    'bullets_offset_hor'          => '0',
                    'bullets_offset_vert'         => '30',
                    'enable_thumbnails'           => 'on',
                    'thumbnails_padding'          => '20',
                    'span_thumbnails_wrapper'     => 'on',
                    'thumbnails_wrapper_color'    => '#222222',
                    'thumbnails_wrapper_opacity'  => '100',
                    'thumbnails_style'            => 'navbar',
                    'thumb_amount'                => '9',
                    'thumbnails_space'            => '10',
                    'thumbnail_direction'         => 'vertical',
                    'thumb_width'                 => '60',
                    'thumb_height'                => '60',
                    'thumb_width_min'             => '60',
                    'thumbs_always_on'            => 'false',
                    'hide_thumbs'                 => '200',
                    'hide_thumbs_mobile'          => '1200',
                    'hide_thumbs_on_mobile'       => 'off',
                    'thumbs_under_hidden'         => '0',
                    'hide_thumbs_over'            => 'off',
                    'thumbs_over_hidden'          => '0',
                    'thumbnails_inner_outer'      => 'outer-right',
                    'thumbnails_align_hor'        => 'right',
                    'thumbnails_align_vert'       => 'top',
                    'thumbnails_offset_hor'       => '0',
                    'thumbnails_offset_vert'      => '0',
                    'enable_tabs'                 => 'off',
                    'tabs_padding'                => '5',
                    'span_tabs_wrapper'           => 'off',
                    'tabs_wrapper_color'          => 'transparent',
                    'tabs_wrapper_opacity'        => '5',
                    'tabs_style'                  => '',
                    'tabs_amount'                 => '5',
                    'tabs_space'                  => '5',
                    'tabs_direction'              => 'horizontal',
                    'tabs_width'                  => '100',
                    'tabs_height'                 => '50',
                    'tabs_width_min'              => '100',
                    'tabs_always_on'              => 'false',
                    'hide_tabs'                   => '200',
                    'hide_tabs_mobile'            => '1200',
                    'hide_tabs_on_mobile'         => 'off',
                    'tabs_under_hidden'           => '0',
                    'hide_tabs_over'              => 'off',
                    'tabs_over_hidden'            => '0',
                    'tabs_inner_outer'            => 'inner',
                    'tabs_align_hor'              => 'center',
                    'tabs_align_vert'             => 'bottom',
                    'tabs_offset_hor'             => '0',
                    'tabs_offset_vert'            => '20',
                    'touchenabled'                => 'on',
                    'drag_block_vertical'         => 'off',
                    'swipe_velocity'              => '75',
                    'swipe_min_touches'           => '50',
                    'swipe_direction'             => 'horizontal',
                    'keyboard_navigation'         => 'off',
                    'keyboard_direction'          => 'horizontal',
                    'mousescroll_navigation'      => 'off',
                    'carousel_infinity'           => 'on',
                    'carousel_space'              => '0',
                    'carousel_borderr'            => '0',
                    'carousel_borderr_unit'       => 'px',
                    'carousel_padding_top'        => '0',
                    'carousel_padding_bottom'     => '0',
                    'carousel_maxitems'           => '3',
                    'carousel_stretch'            => 'off',
                    'carousel_fadeout'            => 'on',
                    'carousel_varyfade'           => 'on',
                    'carousel_rotation'           => 'off',
                    'carousel_varyrotate'         => 'on',
                    'carousel_maxrotation'        => '65',
                    'carousel_scale'              => 'off',
                    'carousel_varyscale'          => 'off',
                    'carousel_scaledown'          => '55',
                    'carousel_hposition'          => 'center',
                    'carousel_vposition'          => 'center',
                    'use_parallax'                => 'on',
                    'disable_parallax_mobile'     => 'off',
                    'parallax_type'               => 'mouse',
                    'parallax_origo'              => 'slidercenter',
                    'parallax_speed'              => '2000',
                    'parallax_level_1'            => '2',
                    'parallax_level_2'            => '3',
                    'parallax_level_3'            => '4',
                    'parallax_level_4'            => '5',
                    'parallax_level_5'            => '6',
                    'parallax_level_6'            => '7',
                    'parallax_level_7'            => '12',
                    'parallax_level_8'            => '16',
                    'parallax_level_9'            => '10',
                    'parallax_level_10'           => '50',
                    'lazy_load_type'              => 'smart',
                    'seo_optimization'            => 'none',
                    'simplify_ie8_ios4'           => 'off',
                    'show_alternative_type'       => 'off',
                    'show_alternate_image'        => '',
                    'jquery_noconflict'           => 'off',
                    'js_to_body'                  => 'false',
                    'output_type'                 => 'none',
                    'jquery_debugmode'            => 'off',
                    'slider_type'                 => 'fullscreen',
                    'width'                       => '900',
                    'width_notebook'              => '720',
                    'width_tablet'                => '720',
                    'width_mobile'                => '720',
                    'height'                      => '720',
                    'height_notebook'             => '405',
                    'height_tablet'               => '405',
                    'height_mobile'               => '405',
                    'enable_custom_size_notebook' => 'off',
                    'enable_custom_size_tablet'   => 'off',
                    'enable_custom_size_iphone'   => 'off',
                    'main_overflow_hidden'        => 'off',
                    'auto_height'                 => 'off',
                    'min_height'                  => '',
                    'custom_css'                  => '',
                    'custom_javascript'           => '',
                ),
        );
        $presets[] = array(
            'settings' =>
                array('class' => '', 'image' => RevSliderFunctions::asset('') . 'admin/images/sliderpresets/cover_carousel_thumbs.png', 'name' => 'Cover-Flow-Full-Screen', 'preset' => 'carouselpreset'),
            'values'   =>
                array(
                    'next_slide_on_window_focus'   => 'off',
                    'delay'                        => '9000',
                    'start_js_after_delay'         => '0',
                    'image_source_type'            => 'full',
                    0                              => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                    'start_with_slide'             => '1',
                    'first_transition_active'      => 'on',
                    'first_transition_type'        => 'fade',
                    'first_transition_duration'    => '1500',
                    'first_transition_slot_amount' => '7',
                    'stop_on_hover'                => 'off',
                    'stop_slider'                  => 'on',
                    'stop_after_loops'             => '0',
                    'stop_at_slide'                => '1',
                    'shuffle'                      => 'off',
                    'viewport_start'               => 'wait',
                    'viewport_area'                => '80',
                    'enable_progressbar'           => 'on',
                    'background_dotted_overlay'    => 'none',
                    'background_color'             => 'transparent',
                    'padding'                      => '0',
                    'show_background_image'        => 'off',
                    'background_image'             => '',
                    'bg_fit'                       => 'cover',
                    'bg_repeat'                    => 'no-repeat',
                    'bg_position'                  => 'center center',
                    'position'                     => 'center',
                    'use_spinner'                  => '-1',
                    'spinner_color'                => '#FFFFFF',
                    'enable_arrows'                => 'on',
                    'navigation_arrow_style'       => 'navbar-old',
                    'arrows_always_on'             => 'false',
                    'hide_arrows'                  => '200',
                    'hide_arrows_mobile'           => '1200',
                    'hide_arrows_on_mobile'        => 'off',
                    'arrows_under_hidden'          => '600',
                    'hide_arrows_over'             => 'off',
                    'arrows_over_hidden'           => '0',
                    'leftarrow_align_hor'          => 'left',
                    'leftarrow_align_vert'         => 'center',
                    'leftarrow_offset_hor'         => '30',
                    'leftarrow_offset_vert'        => '0',
                    'rightarrow_align_hor'         => 'right',
                    'rightarrow_align_vert'        => 'center',
                    'rightarrow_offset_hor'        => '30',
                    'rightarrow_offset_vert'       => '0',
                    'enable_bullets'               => 'off',
                    'navigation_bullets_style'     => 'round-old',
                    'bullets_space'                => '5',
                    'bullets_direction'            => 'horizontal',
                    'bullets_always_on'            => 'true',
                    'hide_bullets'                 => '200',
                    'hide_bullets_mobile'          => '1200',
                    'hide_bullets_on_mobile'       => 'on',
                    'bullets_under_hidden'         => '600',
                    'hide_bullets_over'            => 'off',
                    'bullets_over_hidden'          => '0',
                    'bullets_align_hor'            => 'center',
                    'bullets_align_vert'           => 'bottom',
                    'bullets_offset_hor'           => '0',
                    'bullets_offset_vert'          => '30',
                    'enable_thumbnails'            => 'on',
                    'thumbnails_padding'           => '20',
                    'span_thumbnails_wrapper'      => 'on',
                    'thumbnails_wrapper_color'     => '#000000',
                    'thumbnails_wrapper_opacity'   => '15',
                    'thumbnails_style'             => 'navbar',
                    'thumb_amount'                 => '9',
                    'thumbnails_space'             => '10',
                    'thumbnail_direction'          => 'horizontal',
                    'thumb_width'                  => '60',
                    'thumb_height'                 => '60',
                    'thumb_width_min'              => '60',
                    'thumbs_always_on'             => 'false',
                    'hide_thumbs'                  => '200',
                    'hide_thumbs_mobile'           => '1200',
                    'hide_thumbs_on_mobile'        => 'off',
                    'thumbs_under_hidden'          => '0',
                    'hide_thumbs_over'             => 'off',
                    'thumbs_over_hidden'           => '0',
                    'thumbnails_inner_outer'       => 'inner',
                    'thumbnails_align_hor'         => 'center',
                    'thumbnails_align_vert'        => 'bottom',
                    'thumbnails_offset_hor'        => '0',
                    'thumbnails_offset_vert'       => '0',
                    'enable_tabs'                  => 'off',
                    'tabs_padding'                 => '5',
                    'span_tabs_wrapper'            => 'off',
                    'tabs_wrapper_color'           => 'transparent',
                    'tabs_wrapper_opacity'         => '5',
                    'tabs_style'                   => '',
                    'tabs_amount'                  => '5',
                    'tabs_space'                   => '5',
                    'tabs_direction'               => 'horizontal',
                    'tabs_width'                   => '100',
                    'tabs_height'                  => '50',
                    'tabs_width_min'               => '100',
                    'tabs_always_on'               => 'false',
                    'hide_tabs'                    => '200',
                    'hide_tabs_mobile'             => '1200',
                    'hide_tabs_on_mobile'          => 'off',
                    'tabs_under_hidden'            => '0',
                    'hide_tabs_over'               => 'off',
                    'tabs_over_hidden'             => '0',
                    'tabs_inner_outer'             => 'inner',
                    'tabs_align_hor'               => 'center',
                    'tabs_align_vert'              => 'bottom',
                    'tabs_offset_hor'              => '0',
                    'tabs_offset_vert'             => '20',
                    'touchenabled'                 => 'on',
                    'drag_block_vertical'          => 'off',
                    'swipe_velocity'               => '75',
                    'swipe_min_touches'            => '50',
                    'swipe_direction'              => 'horizontal',
                    'keyboard_navigation'          => 'off',
                    'keyboard_direction'           => 'horizontal',
                    'mousescroll_navigation'       => 'off',
                    'carousel_infinity'            => 'on',
                    'carousel_space'               => '-150',
                    'carousel_borderr'             => '0',
                    'carousel_borderr_unit'        => '%',
                    'carousel_padding_top'         => '0',
                    'carousel_padding_bottom'      => '0',
                    'carousel_maxitems'            => '5',
                    'carousel_stretch'             => 'off',
                    'carousel_fadeout'             => 'on',
                    'carousel_varyfade'            => 'on',
                    'carousel_rotation'            => 'on',
                    'carousel_varyrotate'          => 'on',
                    'carousel_maxrotation'         => '65',
                    'carousel_scale'               => 'on',
                    'carousel_varyscale'           => 'off',
                    'carousel_scaledown'           => '55',
                    'carousel_hposition'           => 'center',
                    'carousel_vposition'           => 'center',
                    'use_parallax'                 => 'on',
                    'disable_parallax_mobile'      => 'off',
                    'parallax_type'                => 'mouse',
                    'parallax_origo'               => 'slidercenter',
                    'parallax_speed'               => '2000',
                    'parallax_level_1'             => '2',
                    'parallax_level_2'             => '3',
                    'parallax_level_3'             => '4',
                    'parallax_level_4'             => '5',
                    'parallax_level_5'             => '6',
                    'parallax_level_6'             => '7',
                    'parallax_level_7'             => '12',
                    'parallax_level_8'             => '16',
                    'parallax_level_9'             => '10',
                    'parallax_level_10'            => '50',
                    'lazy_load_type'               => 'smart',
                    'seo_optimization'             => 'none',
                    'simplify_ie8_ios4'            => 'off',
                    'show_alternative_type'        => 'off',
                    'show_alternate_image'         => '',
                    'jquery_noconflict'            => 'off',
                    'js_to_body'                   => 'false',
                    'output_type'                  => 'none',
                    'jquery_debugmode'             => 'off',
                    'slider_type'                  => 'fullscreen',
                    'width'                        => '800',
                    'width_notebook'               => '600',
                    'width_tablet'                 => '600',
                    'width_mobile'                 => '600',
                    'height'                       => '800',
                    'height_notebook'              => '600',
                    'height_tablet'                => '600',
                    'height_mobile'                => '600',
                    'enable_custom_size_notebook'  => 'off',
                    'enable_custom_size_tablet'    => 'off',
                    'enable_custom_size_iphone'    => 'off',
                    'main_overflow_hidden'         => 'off',
                    'auto_height'                  => 'off',
                    'min_height'                   => '',
                    'custom_css'                   => '',
                    'custom_javascript'            => '',
                ),
        );
        $presets[] = array(
            'settings' =>
                array('class' => '', 'image' => RevSliderFunctions::asset('') . 'admin/images/sliderpresets/carousel_full_rounded.png', 'name' => 'Cover-Flow-Rounded', 'preset' => 'carouselpreset'),
            'values'   =>
                array(
                    'next_slide_on_window_focus'   => 'off',
                    'delay'                        => '9000',
                    'start_js_after_delay'         => '0',
                    'image_source_type'            => 'full',
                    0                              => 'revapi39.bind(\\"revolution.slide.layeraction\\",function (e) {
	//data.eventtype - Layer Action (enterstage, enteredstage, leavestage,leftstage)
	//data.layertype - Layer Type (image,video,html)
	//data.layersettings - Default Settings for Layer
	//data.layer - Layer as jQuery Object
});',
                    'start_with_slide'             => '1',
                    'first_transition_active'      => 'on',
                    'first_transition_type'        => 'fade',
                    'first_transition_duration'    => '1500',
                    'first_transition_slot_amount' => '7',
                    'stop_on_hover'                => 'off',
                    'stop_slider'                  => 'on',
                    'stop_after_loops'             => '0',
                    'stop_at_slide'                => '1',
                    'shuffle'                      => 'off',
                    'viewport_start'               => 'wait',
                    'viewport_area'                => '80',
                    'enable_progressbar'           => 'on',
                    'background_dotted_overlay'    => 'none',
                    'background_color'             => 'transparent',
                    'padding'                      => '0',
                    'show_background_image'        => 'off',
                    'background_image'             => '',
                    'bg_fit'                       => 'cover',
                    'bg_repeat'                    => 'no-repeat',
                    'bg_position'                  => 'center center',
                    'position'                     => 'center',
                    'use_spinner'                  => '-1',
                    'spinner_color'                => '#FFFFFF',
                    'enable_arrows'                => 'on',
                    'navigation_arrow_style'       => 'round',
                    'arrows_always_on'             => 'false',
                    'hide_arrows'                  => '200',
                    'hide_arrows_mobile'           => '1200',
                    'hide_arrows_on_mobile'        => 'off',
                    'arrows_under_hidden'          => '600',
                    'hide_arrows_over'             => 'off',
                    'arrows_over_hidden'           => '0',
                    'leftarrow_align_hor'          => 'left',
                    'leftarrow_align_vert'         => 'center',
                    'leftarrow_offset_hor'         => '30',
                    'leftarrow_offset_vert'        => '0',
                    'rightarrow_align_hor'         => 'right',
                    'rightarrow_align_vert'        => 'center',
                    'rightarrow_offset_hor'        => '30',
                    'rightarrow_offset_vert'       => '0',
                    'enable_bullets'               => 'off',
                    'navigation_bullets_style'     => 'round-old',
                    'bullets_space'                => '5',
                    'bullets_direction'            => 'horizontal',
                    'bullets_always_on'            => 'true',
                    'hide_bullets'                 => '200',
                    'hide_bullets_mobile'          => '1200',
                    'hide_bullets_on_mobile'       => 'on',
                    'bullets_under_hidden'         => '600',
                    'hide_bullets_over'            => 'off',
                    'bullets_over_hidden'          => '0',
                    'bullets_align_hor'            => 'center',
                    'bullets_align_vert'           => 'bottom',
                    'bullets_offset_hor'           => '0',
                    'bullets_offset_vert'          => '30',
                    'enable_thumbnails'            => 'on',
                    'thumbnails_padding'           => '20',
                    'span_thumbnails_wrapper'      => 'on',
                    'thumbnails_wrapper_color'     => '#000000',
                    'thumbnails_wrapper_opacity'   => '0',
                    'thumbnails_style'             => 'preview1',
                    'thumb_amount'                 => '9',
                    'thumbnails_space'             => '10',
                    'thumbnail_direction'          => 'horizontal',
                    'thumb_width'                  => '60',
                    'thumb_height'                 => '60',
                    'thumb_width_min'              => '60',
                    'thumbs_always_on'             => 'false',
                    'hide_thumbs'                  => '200',
                    'hide_thumbs_mobile'           => '1200',
                    'hide_thumbs_on_mobile'        => 'off',
                    'thumbs_under_hidden'          => '0',
                    'hide_thumbs_over'             => 'off',
                    'thumbs_over_hidden'           => '0',
                    'thumbnails_inner_outer'       => 'inner',
                    'thumbnails_align_hor'         => 'center',
                    'thumbnails_align_vert'        => 'bottom',
                    'thumbnails_offset_hor'        => '0',
                    'thumbnails_offset_vert'       => '0',
                    'enable_tabs'                  => 'off',
                    'tabs_padding'                 => '5',
                    'span_tabs_wrapper'            => 'off',
                    'tabs_wrapper_color'           => 'transparent',
                    'tabs_wrapper_opacity'         => '5',
                    'tabs_style'                   => '',
                    'tabs_amount'                  => '5',
                    'tabs_space'                   => '5',
                    'tabs_direction'               => 'horizontal',
                    'tabs_width'                   => '100',
                    'tabs_height'                  => '50',
                    'tabs_width_min'               => '100',
                    'tabs_always_on'               => 'false',
                    'hide_tabs'                    => '200',
                    'hide_tabs_mobile'             => '1200',
                    'hide_tabs_on_mobile'          => 'off',
                    'tabs_under_hidden'            => '0',
                    'hide_tabs_over'               => 'off',
                    'tabs_over_hidden'             => '0',
                    'tabs_inner_outer'             => 'inner',
                    'tabs_align_hor'               => 'center',
                    'tabs_align_vert'              => 'bottom',
                    'tabs_offset_hor'              => '0',
                    'tabs_offset_vert'             => '20',
                    'touchenabled'                 => 'on',
                    'drag_block_vertical'          => 'off',
                    'swipe_velocity'               => '75',
                    'swipe_min_touches'            => '50',
                    'swipe_direction'              => 'horizontal',
                    'keyboard_navigation'          => 'off',
                    'keyboard_direction'           => 'horizontal',
                    'mousescroll_navigation'       => 'off',
                    'carousel_infinity'            => 'on',
                    'carousel_space'               => '-150',
                    'carousel_borderr'             => '50',
                    'carousel_borderr_unit'        => '%',
                    'carousel_padding_top'         => '0',
                    'carousel_padding_bottom'      => '0',
                    'carousel_maxitems'            => '5',
                    'carousel_stretch'             => 'off',
                    'carousel_fadeout'             => 'on',
                    'carousel_varyfade'            => 'on',
                    'carousel_rotation'            => 'off',
                    'carousel_varyrotate'          => 'on',
                    'carousel_maxrotation'         => '65',
                    'carousel_scale'               => 'on',
                    'carousel_varyscale'           => 'off',
                    'carousel_scaledown'           => '55',
                    'carousel_hposition'           => 'center',
                    'carousel_vposition'           => 'center',
                    'use_parallax'                 => 'on',
                    'disable_parallax_mobile'      => 'off',
                    'parallax_type'                => 'mouse',
                    'parallax_origo'               => 'slidercenter',
                    'parallax_speed'               => '2000',
                    'parallax_level_1'             => '2',
                    'parallax_level_2'             => '3',
                    'parallax_level_3'             => '4',
                    'parallax_level_4'             => '5',
                    'parallax_level_5'             => '6',
                    'parallax_level_6'             => '7',
                    'parallax_level_7'             => '12',
                    'parallax_level_8'             => '16',
                    'parallax_level_9'             => '10',
                    'parallax_level_10'            => '50',
                    'lazy_load_type'               => 'smart',
                    'seo_optimization'             => 'none',
                    'simplify_ie8_ios4'            => 'off',
                    'show_alternative_type'        => 'off',
                    'show_alternate_image'         => '',
                    'jquery_noconflict'            => 'off',
                    'js_to_body'                   => 'false',
                    'output_type'                  => 'none',
                    'jquery_debugmode'             => 'off',
                    'slider_type'                  => 'fullwidth',
                    'width'                        => '800',
                    'width_notebook'               => '600',
                    'width_tablet'                 => '600',
                    'width_mobile'                 => '600',
                    'height'                       => '800',
                    'height_notebook'              => '600',
                    'height_tablet'                => '600',
                    'height_mobile'                => '600',
                    'enable_custom_size_notebook'  => 'off',
                    'enable_custom_size_tablet'    => 'off',
                    'enable_custom_size_iphone'    => 'off',
                    'main_overflow_hidden'         => 'off',
                    'auto_height'                  => 'off',
                    'min_height'                   => '',
                    'custom_css'                   => '',
                    'custom_javascript'            => '',
                ),
        );

        //add the presets made from customers
        $customer_presets = RevSliderOptions::getOptions('revslider_presets', array());


        $presets = array_merge($presets, $customer_presets);

        //$presets = apply_filters('revslider_slider_presets', $presets);

        foreach ($presets as $key => $preset) {
            if (intval($preset['settings']['image']) > 0) {
                $img = RevSliderFunctions::get_image_url_by_id($preset['settings']['image'], 'medium');
                $presets[$key]['settings']['image'] = ($img !== false) ? $img['0'] : '';
            }
        }

        return $presets;

    }

    public static function getGeneralSettingsValues()
    {

        $arrValues = RevSliderOptions::getOptions('revslider-global-settings', '');

        return ($arrValues);
    }

    public static function import_failed_message($message, $link = false)
    {

        $bg_image_url = RevSliderFunctions::asset('admin/assets/images/errorbg.jpg');
        echo '<div style="font-family:arial; width:100%;height:100%;position:absolute;top:0px;left:0px;background-image:url(' . $bg_image_url . '); background-position:center center; background-size:cover;">';
        echo '<div style="width:100%;height:250px;text-align:center; line-height:25px; position:absolute;top:50%;left:0;padding:40px;box-sizing:border-box;margin-top:-165px;">';
        echo '<div style="font-size:30px; font-weight:600; line-height:50px; white-space:nowrap;margin-bottom:10px">Error: ' . $message . '</div>';
        if ($link !== false) {
            echo '<a style="padding:10px 25px; color:#fff; border-radius:4px; text-decoration:none !important; background:#2980b9; font-weight:400; font-size:14px; line-height:30px; vertical-align:middle;" href="' . $link . '">Go Back</a>';
        }
        echo '</div>';
        echo '</div>';

    }

    public static function getStaticCss()
    {
        if (!RevSliderOptions::getOptions('revslider-static-css')) {
            if (file_exists($path = RevSliderFunctions::asset_dir('public/css/static-captions.css'))) {
                $contentCSS = @file_get_contents($path);
                self::updateStaticCss($contentCSS);
            }
        }
        $contentCSS = RevSliderOptions::getOptions('revslider-static-css', '');

        return ($contentCSS);
    }

    public static function updateStaticCss($content)
    {
        $content = str_replace(array("\'", '\"', '\\\\'), array("'", '"', '\\'), trim($content));
        RevSliderOptions::getOptions('revslider-static-css', '');
        RevSliderOptions::updateOption('revslider-static-css', $content, 'off');
        return $content;
    }

    public function getDynamicCss()
    {
        if (empty(self::$css)) {
            self::fillCSS();
        }

        $styles = self::$css;
        $styles = RevSliderCssParser::parseDbArrayToCss($styles, "\n");

        return $styles;
    }

    public static function fillCSS()
    {
        if (empty(self::$css)) {
            $customCss = array();

            $result = RevSliderDB::instance(array(
                'table' => RevSliderGlobals::$table_css
            ))->get();
            if (!empty($result)) {
                $customCss = $result;
            }
            self::$css = $customCss;
        }
    }

    public static function insertCustomAnim($anim)
    {
        if (isset($anim['handle'])) {

            $arrInsert = array();
            $arrInsert["handle"] = $anim['handle'];
            unset($anim['handle']);

            $arrInsert["params"] = stripslashes(json_encode(str_replace("'", '"', $anim['params'])));
            $arrInsert["settings"] = json_encode(array('version' => 'custom'));
            RevSliderDB::instance(array(
                'table' => RevSliderGlobals::$table_layer_anims
            ))->insert($arrInsert);
        }

        $arrAnims['customin'] = self::getCustomAnimations();
        $arrAnims['customout'] = self::getCustomAnimations('customout');
        $arrAnims['customfull'] = self::getFullCustomAnimations();

        return $arrAnims;
    }

    public static function getCustomAnimations($pre = 'customin')
    {

        if (empty(self::$animations)) {
            self::fillAnimations();
        }

        $customAnimations = self::$animations;

        $ret_array = array();

        foreach ($customAnimations as $key => $value) {
            $params = json_decode($value['params'], true);
            if (!isset($params['type']) || $params['type'] == $pre) {
                $ret_array[$pre . '-' . $value['id']] = $value['handle'];
            }
        }

        asort($ret_array);

        return $ret_array;
    }

    public static function fillAnimations()
    {
        if (empty(self::$animations)) {

            $customAnimations = array();

            $result = RevSliderDB::instance(array(
                'table' => RevSliderGlobals::$table_layer_anims
            ))->get();
            if (!empty($result)) {
                $customAnimations = $result;
            }

            self::$animations = $customAnimations;
        }
    }

    public static function getFullCustomAnimations()
    {

        if (empty(self::$animations)) {
            self::fillAnimations();
        }

        $customAnimations = self::$animations;

        $ret_anims = array();

        foreach ($customAnimations as $key => $value) {
            $ret_anims[$key]['id'] = $value['id'];
            $ret_anims[$key]['handle'] = $value['handle'];
            $ret_anims[$key]['params'] = json_decode(str_replace("'", '"', $value['params']), true);
        }

        return $ret_anims;
    }

    public static function updateCustomAnim($anim)
    {

        if (isset($anim['handle'])) {
            $handle = $anim['handle'];
            unset($anim['handle']);

            $id = str_replace(array('customin-', 'customout-'), array('', ''), $handle);

            $arrUpdate = array();
            $arrUpdate['params'] = stripslashes(json_encode(str_replace("'", '"', $anim['params'])));
            //$arrUpdate["settings"] = json_encode(array('version' => 'custom'));
            RevSliderDB::instance(array(
                'table' => RevSliderGlobals::$table_layer_anims,
                'where' => array('id', $id)
            ))->update($arrUpdate);
        }

        $arrAnims['customin'] = self::getCustomAnimations();
        $arrAnims['customout'] = self::getCustomAnimations('customout');
        $arrAnims['customfull'] = self::getFullCustomAnimations();

        return $arrAnims;
    }

    public static function updateCustomAnimName($anim)
    {
        if (isset($anim['handle'])) {
            $id = $anim['id'];
            unset($anim['id']);
            RevSliderDB::instance(array(
                'table' => RevSliderGlobals::$table_layer_anims,
                'where' => array('id', $id)
            ))->update($anim);
        }

        $arrAnims['customin'] = self::getCustomAnimations();
        $arrAnims['customout'] = self::getCustomAnimations('customout');
        $arrAnims['customfull'] = self::getFullCustomAnimations();

        return $arrAnims;
    }

    public static function deleteCustomAnim($rawID)
    {

        if (trim($rawID) != '') {
            $id = str_replace(array('customin-', 'customout-'), array('', ''), $rawID);
            RevSliderDB::instance(array(
                'table' => RevSliderGlobals::$table_layer_anims,
                'where' => array('id', $id)
            ))->delete();
        }

        $arrAnims['customin'] = self::getCustomAnimations();
        $arrAnims['customout'] = self::getCustomAnimations('customout');
        $arrAnims['customfull'] = self::getFullCustomAnimations();

        return $arrAnims;
    }

    public function putSlidePreviewByData($data)
    {

        if ($data == "empty_output") {
            $this->loadingMessageOutput();
            exit();
        }

        $data = RevSliderFunctions::jsonDecodeFromClientSide($data);
        $slideID = $data["slideid"];
        $slide = new Slide();
        $slide->initByID($slideID);
        $sliderID = $slide->getSliderID();

        $output = new Output();
        $output->setOneSlideMode($data);

        $this->previewOutput($sliderID, $output);
    }

    public function loadingMessageOutput()
    {
        ?>
        <div class="message_loading_preview"><?php echo t("Loading Preview...") ?></div>
        <?php
    }

    public function previewOutput($sliderID, $output = null)
    {

        if ($sliderID == "empty_output") {
            $this->loadingMessageOutput();
            exit();
        }

        if ($output == null)
            $output = new Output();

        $slider = new Slider();
        $slider->initByID($sliderID);
        $isWpmlExists = false;//RevSliderWpml::isWpmlExists();
        $useWpml = $slider->getParam("use_wpml", "off");
        $wpmlActive = false;
//        if($isWpmlExists && $useWpml == "on"){
//            $wpmlActive = true;
//            $arrLanguages = RevSliderWpml::getArrLanguages(false);
//
//            //set current lang to output
//            $currentLang = RevSliderFunctions::getPostGetVariable("lang");
//
//            if(empty($currentLang))
//                $currentLang = RevSliderWpml::getCurrentLang();
//
//            if(empty($currentLang))
//                $currentLang = $arrLanguages[0];
//
//            $output->setLang($currentLang);
//
//            $selectLangChoose = RevSliderFunctions::getHTMLSelect($arrLanguages,$currentLang,"id='select_langs'",true);
//        }


        $output->setPreviewMode();

        //put the output html
        $urlPlugin = RevSliderFunctions::asset('public/');
        $urlPreviewPattern = RevSliderFunctions::url_ajax_action(array(
            'client_action' => 'preview_slider',
            'sliderid'      => $sliderID,
            'lang'          => '[lang]',
            'nonce'         => '[nonce]'
        ));
//        $nonce = wp_create_nonce("revslider_actions");
//
        $setBase = '//';//(is_ssl()) ? "https://" : "http://";

        ?>
        <html>
        <head>
            <link rel='stylesheet'
                  href='<?php echo $urlPlugin; ?>css/settings.css?rev=<?php echo RevSliderGlobals::SLIDER_REVISION; ?>'
                  type='text/css' media='all'/>
            <link rel='stylesheet'
                  href='<?php echo $urlPlugin; ?>fonts/font-awesome/css/font-awesome.css?rev=<?php echo RevSliderGlobals::SLIDER_REVISION; ?>'
                  type='text/css' media='all'/>
            <link rel='stylesheet'
                  href='<?php echo $urlPlugin; ?>fonts/pe-icon-7-stroke/css/pe-icon-7-stroke.css?rev=<?php echo RevSliderGlobals::SLIDER_REVISION; ?>'
                  type='text/css' media='all'/>
            <?php

            if (empty(self::$css)) {
                self::fillCSS();
            }

            $styles = self::$css;
            $styles = RevSliderCssParser::parseDbArrayToCss($styles, "\n");
            $styles = RevSliderCssParser::compress_css($styles);

            echo '<style type="text/css">' . $styles . '</style>'; //.$stylesinnerlayers

            //            $http = (is_ssl()) ? 'https' : 'http';

            $operations = new Operations();
            $arrValues = $operations->getGeneralSettingsValues();

            $set_diff_font = RevSliderFunctions::getVal($arrValues, "change_font_loading", '');
            //            if($set_diff_font !== ''){
            //                $font_url = $set_diff_font;
            //            }else{
            //                $font_url = $http.'://fonts.googleapis.com/css?family=';
            //            }

            $custom_css = Operations::getStaticCss();
            echo '<style type="text/css">' . RevSliderCssParser::compress_css($custom_css) . '</style>';

            $gfonts = '';
            $googleFont = $slider->getParam("google_font");
            if (is_array($googleFont)) {
                foreach ($googleFont as $key => $font) {

                }
            } else {
                Operations::setCleanFontImport($googleFont);
                //$gfonts .= RevSliderOperations::getCleanFontImport($googleFont);
            }
            //add all google fonts of layers
            $gfsubsets = $slider->getParam("subsets", array());
            $gf = $slider->getUsedFonts(false);

            foreach ($gf as $gfk => $gfv) {
                $variants = array();
                if (!empty($gfv['variants'])) {
                    foreach ($gfv['variants'] as $mgvk => $mgvv) {
                        $variants[] = $mgvk;
                    }
                }

                $subsets = array();
                if (!empty($gfv['subsets'])) {
                    foreach ($gfv['subsets'] as $ssk => $ssv) {
                        if (array_search(RevsliderFunctions::esc_attr($gfk . '+' . $ssv), $gfsubsets) !== false) {
                            $subsets[] = $ssv;
                        }
                    }
                }
                Operations::setCleanFontImport($gfk, '', '', $variants, $subsets);
                //$gfonts .= RevSliderOperations::getCleanFontImport($gfk, '', '', $variants, $subsets);
            }

            echo Operations::printCleanFontImport();
            //echo $gfonts;
            ?>

            <script type='text/javascript' src='<?php echo $setBase; ?>code.jquery.com/jquery-latest.min.js'></script>

            <script type='text/javascript'
                    src='<?php echo $urlPlugin; ?>js/jquery.themepunch.tools.min.js?rev=<?php echo RevSliderGlobals::SLIDER_REVISION; ?>'></script>
            <script type='text/javascript'
                    src='<?php echo $urlPlugin; ?>js/jquery.themepunch.revolution.min.js?rev=<?php echo RevSliderGlobals::SLIDER_REVISION; ?>'></script>
            <?php
            $setRevStartSize = Front::add_setREVStartSize();
            echo $setRevStartSize;
            ?>
            <?php
            //            do_action('revslider_preview_slider_head');
            ?>
        </head>
        <body style="padding:0px;margin:0px;width:100%;height:100%;position:relative;">
        <?php
        if ($wpmlActive == true) {
            ?>
            <div style="margin-bottom:10px;text-align:center;">
                <?php t("Choose language"); ?>: <?php echo $selectLangChoose; ?>
            </div>

            <script type="text/javascript">
                var g_previewPattern = '<?php echo $urlPreviewPattern; ?>';
                jQuery("#select_langs").change(function () {
                    var lang = this.value;
                    var nonce = "<?php echo $nonce; ?>";
                    var pattern = g_previewPattern;
                    var urlPreview = pattern.replace("[lang]", lang).replace("[nonce]", nonce);
                    location.href = urlPreview;
                });
            </script>
            <?php
        }
        ?>

        <?php
        $output->putSliderBase($sliderID);
        //do_action('revslider_preview_slider_footer', $slider, $output);
        ?>
        </body>
        </html>
        <?php
    }

    public static function setCleanFontImport($font, $class = '', $url = '', $variants = array(), $subsets = array())
    {
        $revslider_fonts = RevSliderGlobals::$revslider_fonts;

        $ret = '';

        if (!isset($revslider_fonts)) $revslider_fonts = array(); //if this is called without revslider.php beeing loaded

        $do_print = false;
        $tcf = '';
        if (!empty($variants) || !empty($subsets)) {
            if (!isset($revslider_fonts[$font])) $revslider_fonts[$font] = array();
            if (!isset($revslider_fonts[$font]['variants'])) $revslider_fonts[$font]['variants'] = array();
            if (!isset($revslider_fonts[$font]['subsets'])) $revslider_fonts[$font]['subsets'] = array();

            if (!empty($variants)) {
                foreach ($variants as $k => $v) {
                    if (!in_array($v, $revslider_fonts[$font]['variants'])) {
                        $revslider_fonts[$font]['variants'][] = $v;
                    } else { //already included somewhere, so do not call it anymore
                        unset($variants[$k]);
                    }
                }
            }
            if (!empty($subsets)) {
                foreach ($subsets as $k => $v) {
                    if (!in_array($v, $revslider_fonts[$font]['subsets'])) {
                        $revslider_fonts[$font]['subsets'][] = $v;
                    } else { //already included somewhere, so do not call it anymore
                        unset($subsets[$k]);
                    }
                }
            }
            /*
            if(!empty($variants)){
                $mgfirst = true;
                foreach($variants as $mgvk => $mgvv){
                    if(!$mgfirst) $tcf .= ',';
                    $tcf .= $mgvv;
                    $mgfirst = false;
                }
            }

            if(!empty($subsets)){

                $mgfirst = true;
                foreach($subsets as $ssk => $ssv){
                    if($mgfirst) $tcf .= '&subset=';
                    if(!$mgfirst) $tcf .= ',';
                    $tcf .= $ssv;
                    $mgfirst = false;
                }
            }

            if($tcf !== ''){
                $tcf = ':'.$tcf;
                $do_print = true;
            }*/
        } else {
            /*if(in_array($font, $revslider_fonts)){
                $ret = '';
                $do_print = false;
            }else{
                $do_print = true;
            }*/
        }


        /*if($do_print){
            $setBase = (is_ssl()) ? "https://" : "http://";

            if($class !== '') $class = ' class="'.$class.'"';

            if(!isset($revslider_fonts[$font])){
                $revslider_fonts[$font] = array();
            }
            if(strpos($font, "href=") === false){ //fallback for old versions
                $url = RevSliderFront::modify_punch_url($setBase . 'fonts.googleapis.com/css?family=');
                $ret = '<link href="'.$url.urlencode($font.$tcf).'"'.$class.' rel="stylesheet" property="stylesheet" type="text/css" media="all">'; //id="rev-google-font"
            }else{
                $font = str_replace(array('http://', 'https://'), array($setBase, $setBase), $font);
                $ret = html_entity_decode(stripslashes($font));
            }
        }


        return apply_filters('revslider_getCleanFontImport', $ret, $font, $class, $url, $variants, $subsets);*/

        RevSliderGlobals::$revslider_fonts = $revslider_fonts;
    }

    public static function printCleanFontImport()
    {
        $revslider_fonts = RevSliderGlobals::$revslider_fonts;

        $do_print = false;
        $font_first = true;
        $ret = '';
        $tcf = '';
        $tcf2 = '';
//        $setBase = (is_ssl()) ? "https://" : "http://";
        $setBase = "//";

        if (!empty($revslider_fonts)) {
            foreach ($revslider_fonts as $f_n => $f_s) {
                if ($f_n !== '') {
                    if (isset($f_s['variants']) && !empty($f_s['variants']) || isset($f_s['subsets']) && !empty($f_s['subsets'])) {
                        if (strpos($f_n, "href=") === false) {
                            if ($font_first == false) $tcf .= '%7C'; //'|';
                            $tcf .= urlencode($f_n) . ':';

                            if (isset($f_s['variants']) && !empty($f_s['variants'])) {
                                $mgfirst = true;
                                foreach ($f_s['variants'] as $mgvk => $mgvv) {
                                    if (!$mgfirst) $tcf .= urlencode(',');
                                    $tcf .= urlencode($mgvv);
                                    $mgfirst = false;
                                }
                            }

                            if (isset($f_s['subsets']) && !empty($f_s['subsets'])) {
                                $mgfirst = true;
                                foreach ($f_s['subsets'] as $ssk => $ssv) {
                                    if ($mgfirst) $tcf .= urlencode('&subset=');
                                    if (!$mgfirst) $tcf .= urlencode(',');
                                    $tcf .= urlencode($ssv);
                                    $mgfirst = false;
                                }
                            }

                        } else {
                            $f_n = str_replace(array('http://', 'https://'), array($setBase, $setBase), $f_n);
                            $tcf2 .= html_entity_decode(stripslashes($f_n));
                        }
                    }
                    $font_first = false;
                }
            }
        }

        $url = Front::modify_punch_url($setBase . 'fonts.googleapis.com/css?family=');
        if ($tcf !== '') {
            $ret .= '<link href="' . $url . $tcf . '" rel="stylesheet" property="stylesheet" type="text/css" media="all">'; //id="rev-google-font"
        }
        if ($tcf2 !== '') {
            $ret .= html_entity_decode(stripslashes($tcf2));
        }

        RevSliderGlobals::$revslider_fonts = $revslider_fonts;
        return $ret;
//        return apply_filters('revslider_printCleanFontImport', $ret);
    }

    public static function getArrAnimations($all = true)
    {
        $arrAnimations = array();

        $arrAnimations['custom'] = array('handle' => t('## Custom Animation ##'));
        $arrAnimations['vSFXs'] = array('handle' => '-----------------------------------');
        $arrAnimations['vSFX'] = array('handle' => t('- SPECIAL EFFECTS -'));
        $arrAnimations['vSFXe'] = array('handle' => '-----------------------------------');

        $arrAnimations['blockfromleft'] = array('handle' => 'Block-From-Left', 'params' => '{"movex":"inherit","movey":"inherit","movez":"0","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0px","mask_y":"0px","easing":"Power4.easeInOut","speed":"2000","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.3'));
        $arrAnimations['blockfromright'] = array('handle' => 'Block-From-Right', 'params' => '{"movex":"inherit","movey":"inherit","movez":"0","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0px","mask_y":"0px","easing":"Power4.easeInOut","speed":"2000","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.3'));
        $arrAnimations['blockfromtop'] = array('handle' => 'Block-From-Top', 'params' => '{"movex":"inherit","movey":"inherit","movez":"0","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0px","mask_y":"0px","easing":"Power4.easeInOut","speed":"2000","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.3'));
        $arrAnimations['blockfrombottom'] = array('handle' => 'Block-From-Bottom', 'params' => '{"movex":"inherit","movey":"inherit","movez":"0","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0px","mask_y":"0px","easing":"Power4.easeInOut","speed":"2000","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.3'));

        $arrAnimations['v5s'] = array('handle' => '-----------------------------------');
        $arrAnimations['v5'] = array('handle' => t('- VERSION 5.0 ANIMATIONS -'));
        $arrAnimations['v5e'] = array('handle' => '-----------------------------------');

        $arrAnimations['LettersFlyInFromBottom'] = array('handle' => 'LettersFlyInFromBottom', 'params' => '{"movex":"inherit","movey":"[100%]","movez":"0","rotationx":"inherit","rotationy":"inherit","rotationz":"-35deg","scalex":"1","scaley":"1","skewx":"0","skewy":"0","captionopacity":"inherit","mask":"true","mask_x":"0px","mask_y":"0px","easing":"Power4.easeInOut","speed":"2000","split":"chars","splitdelay":"5"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['LettersFlyInFromLeft'] = array('handle' => 'LettersFlyInFromLeft', 'params' => '{"movex":"[-105%]","movey":"inherit","movez":"0","rotationx":"0deg","rotationy":"0deg","rotationz":"-90deg","scalex":"1","scaley":"1","skewx":"0","skewy":"0","captionopacity":"inherit","mask":"true","mask_x":"0px","mask_y":"0px","easing":"Power4.easeInOut","speed":"2000","split":"chars","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['LettersFlyInFromRight'] = array('handle' => 'LettersFlyInFromRight', 'params' => '{"movex":"[105%]","movey":"inherit","movez":"0","rotationx":"45deg","rotationy":"0deg","rotationz":"90deg","scalex":"1","scaley":"1","skewx":"0","skewy":"0","captionopacity":"inherit","mask":"true","mask_x":"0px","mask_y":"0px","easing":"Power4.easeInOut","speed":"2000","split":"chars","splitdelay":"5"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['LettersFlyInFromTop'] = array('handle' => 'LettersFlyInFromTop', 'params' => '{"movex":"inherit","movey":"[-100%]","movez":"0","rotationx":"inherit","rotationy":"inherit","rotationz":"35deg","scalex":"1","scaley":"1","skewx":"0","skewy":"0","captionopacity":"inherit","mask":"true","mask_x":"0px","mask_y":"0px","easing":"Power4.easeInOut","speed":"2000","split":"chars","splitdelay":"5"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['MaskedZoomOut'] = array('handle' => 'MaskedZoomOut', 'params' => '{"movex":"inherit","movey":"inherit","movez":"0","rotationx":"0deg","rotationy":"0","rotationz":"0","scalex":"2","scaley":"2","skewx":"0","skewy":"0","captionopacity":"0","mask":"true","mask_x":"0px","mask_y":"0px","easing":"Power2.easeOut","speed":"1000","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['PopUpSmooth'] = array('handle' => 'PopUpSmooth', 'params' => '{"movex":"inherit","movey":"inherit","movez":"0","rotationx":"0","rotationy":"0","rotationz":"0","scalex":"0.9","scaley":"0.9","skewx":"0","skewy":"0","captionopacity":"0","mask":"false","mask_x":"0px","mask_y":"top","easing":"Power3.easeInOut","speed":"1500","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['RotateInFromBottom'] = array('handle' => 'RotateInFromBottom', 'params' => '{"movex":"inherit","movey":"bottom","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"90deg","scalex":"2","scaley":"2","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","easing":"Power3.easeInOut","speed":"1500","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['RotateInFormZero'] = array('handle' => 'RotateInFormZero', 'params' => '{"movex":"inherit","movey":"bottom","movez":"inherit","rotationx":"-20deg","rotationy":"-20deg","rotationz":"0deg","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","easing":"Power3.easeOut","speed":"1500","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SlideMaskFromBottom'] = array('handle' => 'SlideMaskFromBottom', 'params' => '{"movex":"inherit","movey":"[100%]","movez":"0","rotationx":"0deg","rotationy":"0","rotationz":"0","scalex":"1","scaley":"1","skewx":"0","skewy":"0","captionopacity":"0","mask":"true","mask_x":"0px","mask_y":"[100%]","easing":"Power2.easeInOut","speed":"2000","split":"none","splitdelay":"5"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SlideMaskFromLeft'] = array('handle' => 'SlideMaskFromLeft', 'params' => '{"movex":"[-100%]","movey":"inherit","movez":"0","rotationx":"0deg","rotationy":"0","rotationz":"0","scalex":"1","scaley":"1","skewx":"0","skewy":"0","captionopacity":"inherit","mask":"true","mask_x":"0px","mask_y":"0px","easing":"Power3.easeInOut","speed":"1500","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SlideMaskFromRight'] = array('handle' => 'SlideMaskFromRight', 'params' => '{"movex":"[100%]","movey":"inherit","movez":"0","rotationx":"0deg","rotationy":"0","rotationz":"0","scalex":"1","scaley":"1","skewx":"0","skewy":"0","captionopacity":"inherit","mask":"true","mask_x":"0px","mask_y":"0px","easing":"Power3.easeInOut","speed":"1500","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SlideMaskFromTop'] = array('handle' => 'SlideMaskFromTop', 'params' => '{"movex":"inherit","movey":"[-100%]","movez":"0","rotationx":"0deg","rotationy":"0","rotationz":"0","scalex":"1","scaley":"1","skewx":"0","skewy":"0","captionopacity":"inherit","mask":"true","mask_x":"0px","mask_y":"0px","easing":"Power3.easeInOut","speed":"1500","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SmoothPopUp_One'] = array('handle' => 'SmoothPopUp_One', 'params' => '{"movex":"inherit","movey":"inherit","movez":"0","rotationx":"0","rotationy":"0","rotationz":"0","scalex":"0.8","scaley":"0.8","skewx":"0","skewy":"0","captionopacity":"0","mask":"false","mask_x":"0px","mask_y":"top","easing":"Power4.easeOut","speed":"1500","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SmoothPopUp_Two'] = array('handle' => 'SmoothPopUp_Two', 'params' => '{"movex":"inherit","movey":"inherit","movez":"0","rotationx":"0","rotationy":"0","rotationz":"0","scalex":"0.9","scaley":"0.9","skewx":"0","skewy":"0","captionopacity":"0","mask":"false","mask_x":"0px","mask_y":"top","easing":"Power2.easeOut","speed":"1000","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SmoothMaskFromRight'] = array('handle' => 'SmoothMaskFromRight', 'params' => '{"movex":"[-175%]","movey":"0px","movez":"0","rotationx":"0","rotationy":"0","rotationz":"0","scalex":"1","scaley":"1","skewx":"0","skewy":"0","captionopacity":"1","mask":"true","mask_x":"[100%]","mask_y":"0","easing":"Power3.easeOut","speed":"1500","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SmoothMaskFromLeft'] = array('handle' => 'SmoothMaskFromLeft', 'params' => '{"movex":"[175%]","movey":"0px","movez":"0","rotationx":"0","rotationy":"0","rotationz":"0","scalex":"1","scaley":"1","skewx":"0","skewy":"0","captionopacity":"1","mask":"true","mask_x":"[-100%]","mask_y":"0","easing":"Power3.easeOut","speed":"1500","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SmoothSlideFromBottom'] = array('handle' => 'SmoothSlideFromBottom', 'params' => '{"movex":"inherit","movey":"[100%]","movez":"0","rotationx":"0deg","rotationy":"0","rotationz":"0","scalex":"1","scaley":"1","skewx":"0","skewy":"0","captionopacity":"0","mask":"false","mask_x":"0px","mask_y":"[100%]","easing":"Power4.easeInOut","speed":"2000","split":"none","splitdelay":"5"}', 'settings' => array('version' => '5.0'));

        $arrAnimations['v4s'] = array('handle' => '-----------------------------------');
        $arrAnimations['v4'] = array('handle' => t('- VERSION 4.0 ANIMATIONS -'));
        $arrAnimations['v4e'] = array('handle' => '-----------------------------------');
        $arrAnimations['noanim'] = array('handle' => 'No-Animation', 'params' => '{"movex":"inherit","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['tp-fade'] = array('handle' => 'Fade-In', 'params' => '{"movex":"inherit","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"0"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['sft'] = array('handle' => 'Short-from-Top', 'params' => '{"movex":"inherit","movey":"-50px","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['sfb'] = array('handle' => 'Short-from-Bottom', 'params' => '{"movex":"inherit","movey":"50px","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['sfl'] = array('handle' => 'Short-From-Left', 'params' => '{"movex":"-50px","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['sfr'] = array('handle' => 'Short-From-Right', 'params' => '{"movex":"50px","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['lfr'] = array('handle' => 'Long-From-Right', 'params' => '{"movex":"right","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['lfl'] = array('handle' => 'Long-From-Left', 'params' => '{"movex":"left","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['lft'] = array('handle' => 'Long-From-Top', 'params' => '{"movex":"inherit","movey":"top","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['lfb'] = array('handle' => 'Long-From-Bottom', 'params' => '{"movex":"inherit","movey":"bottom","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['skewfromleft'] = array('handle' => 'Skew-From-Long-Left', 'params' => '{"movex":"left","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"45px","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['skewfromright'] = array('handle' => 'Skew-From-Long-Right', 'params' => '{"movex":"right","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"-85px","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['skewfromleftshort'] = array('handle' => 'Skew-From-Short-Left', 'params' => '{"movex":"-200px","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"85px","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['skewfromrightshort'] = array('handle' => 'Skew-From-Short-Right', 'params' => '{"movex":"200px","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"-85px","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['randomrotate'] = array('handle' => 'Random-Rotate-and-Scale', 'params' => '{"movex":"{-250,250}","movey":"{-150,150}","movez":"inherit","rotationx":"{-90,90}","rotationy":"{-90,90}","rotationz":"{-360,360}","scalex":"{0,1}","scaley":"{0,1}","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));


        if ($all) {
            $arrAnimations['vss'] = array('handle' => '--------------------------------------');
            $arrAnimations['vs'] = array('handle' => t('- SAVED CUSTOM ANIMATIONS -'));
            $arrAnimations['vse'] = array('handle' => '--------------------------------------');

            //$custom = RevSliderOperations::getCustomAnimations('customin');
            $custom = Operations::getCustomAnimationsFullPre('customin');

            $arrAnimations = array_merge($arrAnimations, $custom);
        }

        foreach ($arrAnimations as $key => $value) {
            if (!isset($value['params'])) continue;

            $t = json_decode(str_replace("'", '"', $value['params']), true);
            if (!empty($t))
                $arrAnimations[$key]['params'] = $t;
        }

        return ($arrAnimations);
    }

    public static function getCustomAnimationsFullPre($pre = 'customin')
    {

        if (empty(self::$animations)) {
            self::fillAnimations();
        }

        $customAnimations = array();
        $customTemp = array();
        $sort = array();

        foreach (self::$animations as $key => $value) {
            $params = json_decode($value['params'], true);
            if (!isset($params['type']) || $params['type'] == $pre) {
                $customTemp[$pre . '-' . $value['id']] = $value;
                $sort[$pre . '-' . $value['id']] = $value['handle'];
            }
        }
        if (!empty($sort)) {
            asort($sort);
            foreach ($sort as $k => $v) {
                $customAnimations[$k] = $customTemp[$k];
            }
        }

        return $customAnimations;
    }


    public static function parseCustomAnimationByArray($animArray, $is = 'start', $frame_val)
    {
        $retString = '';

        $reverse = (isset($animArray['x_' . $is . '_reverse']) && $animArray['x_' . $is . '_reverse'] == true) ? '(R)' : ''; //movex reverse
        if (isset($animArray['x_' . $is]) && $animArray['x_' . $is] !== '' && $animArray['x_' . $is] !== 'inherit') $retString .= 'x:' . $animArray['x_' . $is] . $reverse . ';'; //movex
        $reverse = (isset($animArray['y_' . $is . '_reverse']) && $animArray['y_' . $is . '_reverse'] == true) ? '(R)' : ''; //movey reverse
        if (isset($animArray['y_' . $is]) && $animArray['y_' . $is] !== '' && $animArray['y_' . $is] !== 'inherit') $retString .= 'y:' . $animArray['y_' . $is] . $reverse . ';'; //movey
        if (isset($animArray['z_' . $is]) && $animArray['z_' . $is] !== '' && $animArray['z_' . $is] !== 'inherit') $retString .= 'z:' . $animArray['z_' . $is] . ';'; //movez

        $reverse = (isset($animArray['x_rotate_' . $is . '_reverse']) && $animArray['x_rotate_' . $is . '_reverse'] == true) ? '(R)' : ''; //rotationx reverse
        if (isset($animArray['x_rotate_' . $is]) && $animArray['x_rotate_' . $is] !== '' && $animArray['x_rotate_' . $is] !== 'inherit') $retString .= 'rX:' . $animArray['x_rotate_' . $is] . $reverse . ';'; //rotationx
        $reverse = (isset($animArray['y_rotate_' . $is . '_reverse']) && $animArray['y_rotate_' . $is . '_reverse'] == true) ? '(R)' : ''; //rotationy reverse
        if (isset($animArray['y_rotate_' . $is]) && $animArray['y_rotate_' . $is] !== '' && $animArray['y_rotate_' . $is] !== 'inherit') $retString .= 'rY:' . $animArray['y_rotate_' . $is] . $reverse . ';'; //rotationy
        $reverse = (isset($animArray['z_rotate_' . $is . '_reverse']) && $animArray['z_rotate_' . $is . '_reverse'] == true) ? '(R)' : ''; //rotationz reverse
        if (isset($animArray['z_rotate_' . $is]) && $animArray['z_rotate_' . $is] !== '' && $animArray['z_rotate_' . $is] !== 'inherit') $retString .= 'rZ:' . $animArray['z_rotate_' . $is] . $reverse . ';'; //rotationz

        if (isset($animArray['scale_x_' . $is]) && $animArray['scale_x_' . $is] !== '' && $animArray['scale_x_' . $is] !== 'inherit') { //scalex
            $reverse = (isset($animArray['scale_x_' . $is . '_reverse']) && $animArray['scale_x_' . $is . '_reverse'] == true) ? '(R)' : ''; //scalex reverse
            $retString .= 'sX:';
            $retString .= ($animArray['scale_x_' . $is] == 0) ? 0 : $animArray['scale_x_' . $is];
            $retString .= $reverse;
            $retString .= ';';
        }
        if (isset($animArray['scale_y_' . $is]) && $animArray['scale_y_' . $is] !== '' && $animArray['scale_y_' . $is] !== 'inherit') { //scaley
            $reverse = (isset($animArray['scale_y_' . $is . '_reverse']) && $animArray['scale_y_' . $is . '_reverse'] == true) ? '(R)' : ''; //scaley reverse
            $retString .= 'sY:';
            $retString .= ($animArray['scale_y_' . $is] == 0) ? 0 : $animArray['scale_y_' . $is];
            $retString .= $reverse;
            $retString .= ';';
        }

        $reverse = (isset($animArray['skew_x_' . $is . '_reverse']) && $animArray['skew_x_' . $is . '_reverse'] == true) ? '(R)' : ''; //skewx reverse
        if (isset($animArray['skew_x_' . $is]) && $animArray['skew_x_' . $is] !== '' && $animArray['skew_x_' . $is] !== 'inherit') $retString .= 'skX:' . $animArray['skew_x_' . $is] . $reverse . ';'; //skewx
        $reverse = (isset($animArray['skew_y_' . $is . '_reverse']) && $animArray['skew_y_' . $is . '_reverse'] == true) ? '(R)' : ''; //skewy reverse
        if (isset($animArray['skew_y_' . $is]) && $animArray['skew_y_' . $is] !== '' && $animArray['skew_y_' . $is] !== 'inherit') $retString .= 'skY:' . $animArray['skew_y_' . $is] . $reverse . ';'; //skewy

        if (isset($animArray['opacity_' . $is]) && $animArray['opacity_' . $is] !== '' && $animArray['opacity_' . $is] !== 'inherit') { //captionopacity
            $retString .= 'opacity:';
            $opa = (intval($animArray['opacity_' . $is]) > 1) ? $animArray['opacity_' . $is] / 100 : $animArray['opacity_' . $is];
            $retString .= $opa;
            //$retString.= ($is == 'start' && ($opa == '0' || $opa == 0)) ? '0.0001' : $opa;
            $retString .= ';';
        }

        if ($retString == '') { //we do not have animations set, so set them here

        }

        return $retString;
    }

    public static function parseCustomMaskByArray($animArray, $is = 'start')
    {
        $retString = '';
        $reverse = (isset($animArray['mask_x_' . $is . '_reverse']) && $animArray['mask_x_' . $is . '_reverse'] == true) ? '(R)' : '';
        if (isset($animArray['mask_x_' . $is]) && $animArray['mask_x_' . $is] !== '') $retString .= 'x:' . $animArray['mask_x_' . $is] . $reverse . ';';
        $reverse = (isset($animArray['mask_y_' . $is . '_reverse']) && $animArray['mask_y_' . $is . '_reverse'] == true) ? '(R)' : '';
        if (isset($animArray['mask_y_' . $is]) && $animArray['mask_y_' . $is] !== '') $retString .= 'y:' . $animArray['mask_y_' . $is] . $reverse . ';';
        if (isset($animArray['mask_speed_' . $is]) && $animArray['mask_speed_' . $is] !== '') $retString .= 's:' . $animArray['mask_speed_' . $is] . ';';
        if (isset($animArray['mask_ease_' . $is]) && $animArray['mask_ease_' . $is] !== '') $retString .= 'e:' . $animArray['mask_ease_' . $is] . ';';

        return $retString;
    }

    public static function getCaptionsContentArray($handle = false)
    {
        if (empty(self::$css)) {
            self::fillCSS();
        }

        $result = self::$css;

        $contentCSS = RevSliderCssParser::parseDbArrayToArray($result, $handle);
        return ($contentCSS);
    }

    public static function getArrEndAnimations($all = true)
    {
        $arrAnimations = array();
        $arrAnimations['custom'] = array('handle' => t('## Custom Animation ##'));
        $arrAnimations['auto'] = array('handle' => t('Automatic Reverse'));
        $arrAnimations['vSFXs'] = array('handle' => '-----------------------------------');
        $arrAnimations['vSFX'] = array('handle' => t('- SPECIAL EFFECTS -'));
        $arrAnimations['vSFXe'] = array('handle' => '-----------------------------------');

        $arrAnimations['blocktoleft'] = array('handle' => 'Block-To-Left', 'params' => '{"movex":"inherit","movey":"inherit","movez":"0","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0px","mask_y":"0px","easing":"Power4.easeInOut","speed":"2000","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.3'));
        $arrAnimations['blocktoright'] = array('handle' => 'Block-To-Right', 'params' => '{"movex":"inherit","movey":"inherit","movez":"0","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0px","mask_y":"0px","easing":"Power4.easeInOut","speed":"2000","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.3'));
        $arrAnimations['blocktotop'] = array('handle' => 'Block-To-Top', 'params' => '{"movex":"inherit","movey":"inherit","movez":"0","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0px","mask_y":"0px","easing":"Power4.easeInOut","speed":"2000","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.3'));
        $arrAnimations['blocktobottom'] = array('handle' => 'Block-To-Bottom', 'params' => '{"movex":"inherit","movey":"inherit","movez":"0","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0px","mask_y":"0px","easing":"Power4.easeInOut","speed":"2000","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.3'));


        $arrAnimations['v5s'] = array('handle' => '-----------------------------------');
        $arrAnimations['v5'] = array('handle' => t('- VERSION 5.0 ANIMATIONS -'));
        $arrAnimations['v5e'] = array('handle' => '-----------------------------------');

        $arrAnimations['BounceOut'] = array('handle' => 'BounceOut', 'params' => '{"movex":"inherit","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"0deg","scalex":"0.7","scaley":"0.7","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"true","mask_x":"0","mask_y":"0","easing":"Back.easeIn","speed":"500","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['Fade-Out-Long'] = array('handle' => 'Fade-Out-Long', 'params' => '{"movex":"inherit","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","easing":"Power2.easeIn","speed":"1000","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SlideMaskToBottom'] = array('handle' => 'SlideMaskToBottom', 'params' => '{"movex":"inherit","movey":"[100%]","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"true","mask_x":"inherit","mask_y":"inherit","easing":"nothing","speed":"300","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SlideMaskToLeft'] = array('handle' => 'SlideMaskToLeft', 'params' => '{"movex":"[-100%]","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"true","mask_x":"inherit","mask_y":"inherit","easing":"Power3.easeInOut","speed":"1000","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SlideMaskToRight'] = array('handle' => 'SlideMaskToRight', 'params' => '{"movex":"[100%]","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"true","mask_x":"inherit","mask_y":"inherit","easing":"Power3.easeInOut","speed":"1000","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SlideMaskToTop'] = array('handle' => 'SlideMaskToTop', 'params' => '{"movex":"inherit","movey":"[-100%]","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"true","mask_x":"inherit","mask_y":"inherit","easing":"nothing","speed":"300","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SlurpOut'] = array('handle' => 'SlurpOut', 'params' => '{"movex":"inherit","movey":"[100%]","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"0deg","scalex":"0.7","scaley":"0.7","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"true","mask_x":"0","mask_y":"0","easing":"Power3.easeInOut","speed":"1000","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['SmoothCropToBottom'] = array('handle' => 'SmoothCropToBottom', 'params' => '{"movex":"inherit","movey":"[175%]","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"true","mask_x":"inherit","mask_y":"inherit","easing":"Power2.easeInOut","speed":"1000","split":"none","splitdelay":"10"}', 'settings' => array('version' => '5.0'));

        $arrAnimations['v4s'] = array('handle' => '-----------------------------------');
        $arrAnimations['v4'] = array('handle' => t('- VERSION 4.0 ANIMATIONS -'));
        $arrAnimations['v4e'] = array('handle' => '-----------------------------------');
        $arrAnimations['noanimout'] = array('handle' => 'No-Out-Animation', 'params' => '{"movex":"inherit","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['fadeout'] = array('handle' => 'Fade-Out', 'params' => '{"movex":"inherit","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"0"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['stt'] = array('handle' => 'Short-To-Top', 'params' => '{"movex":"inherit","movey":"-50px","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['stb'] = array('handle' => 'Short-To-Bottom', 'params' => '{"movex":"inherit","movey":"50px","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['stl'] = array('handle' => 'Short-To-Left', 'params' => '{"movex":"-50px","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['str'] = array('handle' => 'Short-To-Right', 'params' => '{"movex":"50px","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['ltr'] = array('handle' => 'Long-To-Right', 'params' => '{"movex":"right","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['ltl'] = array('handle' => 'Long-To-Left', 'params' => '{"movex":"left","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['ltt'] = array('handle' => 'Long-To-Top', 'params' => '{"movex":"inherit","movey":"top","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['ltb'] = array('handle' => 'Long-To-Bottom', 'params' => '{"movex":"inherit","movey":"bottom","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"inherit","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['skewtoleft'] = array('handle' => 'Skew-To-Long-Left', 'params' => '{"movex":"left","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"45px","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['skewtoright'] = array('handle' => 'Skew-To-Long-Right', 'params' => '{"movex":"right","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"-85px","skewy":"inherit","captionopacity":"inherit","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['skewtorightshort'] = array('handle' => 'Skew-To-Short-Right', 'params' => '{"movex":"200px","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"-85px","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['skewtoleftshort'] = array('handle' => 'Skew-To-Short-Left', 'params' => '{"movex":"-200px","movey":"inherit","movez":"inherit","rotationx":"inherit","rotationy":"inherit","rotationz":"inherit","scalex":"inherit","scaley":"inherit","skewx":"85px","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));
        $arrAnimations['randomrotateout'] = array('handle' => 'Random-Rotate-Out', 'params' => '{"movex":"{-250,250}","movey":"{-150,150}","movez":"inherit","rotationx":"{-90,90}","rotationy":"{-90,90}","rotationz":"{-360,360}","scalex":"{0,1}","scaley":"{0,1}","skewx":"inherit","skewy":"inherit","captionopacity":"0","mask":"false","mask_x":"0","mask_y":"0","mask_speed":"500"}', 'settings' => array('version' => '5.0'));

        if ($all) {
            $arrAnimations['vss'] = array('handle' => '--------------------------------------');
            $arrAnimations['vs'] = array('handle' => t('- SAVED CUSTOM ANIMATIONS -'));
            $arrAnimations['vse'] = array('handle' => '--------------------------------------');
            //$custom = RevSliderOperations::getCustomAnimations('customout');
            $custom = Operations::getCustomAnimationsFullPre('customout');

            $arrAnimations = array_merge($arrAnimations, $custom);
        }

        foreach ($arrAnimations as $key => $value) {
            if (!isset($value['params'])) continue;

            $t = json_decode(str_replace("'", '"', $value['params']), true);
            if (!empty($t))
                $arrAnimations[$key]['params'] = $t;
        }
        return ($arrAnimations);
    }

}