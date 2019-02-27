<?php

/**
 * Created by FsFlex.
 * User: VH
 * Date: 6/23/2017
 * Time: 4:46 PM
 */
namespace Drupal\revslider\Helper;

class RevSliderInit
{
    protected static $is_inited = false;
    public static function start()
    {
        if(self::$is_inited)
            return;
       self::addFilter();
       self::setGlobalVar();
       self::$is_inited = true;
    }
    protected static function addFilter()
    {

    }
    protected static function setGlobalVar()
    {
        RevSliderGlobals::$table_sliders = RevSliderDB::TABLE_SLIDERS;
        RevSliderGlobals::$table_slides = RevSliderDB::TABLE_SLIDES;
        RevSliderGlobals::$table_static_slides = RevSliderDB::TABLE_STATIC_SLIDERS;
//        RevSliderGlobals::$table_settings = self::$table_prefix.RevSliderGlobals::TABLE_SETTINGS_NAME;
        RevSliderGlobals::$table_css = RevSliderDB::TABLE_CSS;
        RevSliderGlobals::$table_layer_anims = RevSliderDB::TABLE_LAYER_ANIMATIONS;
        RevSliderGlobals::$table_navigation = RevSliderDB::TABLE_NAVIGATIONS;


//        RevSliderGlobals::$filepath_backup = RS_PLUGIN_PATH.'backup/';
//        RevSliderGlobals::$filepath_captions = RS_PLUGIN_PATH.'public/assets/css/captions.css';
//        RevSliderGlobals::$urlCaptionsCSS = RS_PLUGIN_URL.'public/assets/css/captions.php';
//        RevSliderGlobals::$filepath_dynamic_captions = RS_PLUGIN_PATH.'public/assets/css/dynamic-captions.css';
//        RevSliderGlobals::$filepath_captions_original = RS_PLUGIN_PATH.'public/assets/css/captions-original.css';

        $wp_upload_dir = RevSliderFile::uploadDirInfo();
        $wp_upload_dir = $wp_upload_dir['basedir'].'/';
        $wp_upload_dir = RevSliderFile::wrapToPath($wp_upload_dir);
        RevSliderGlobals::$uploadsUrlExportZip = $wp_upload_dir.'export.zip';
    }
}