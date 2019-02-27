<?php
/**
 * Created by FsFlex.
 * User: VH
 * Date: 7/5/2017
 * Time: 4:35 PM
 */

namespace Drupal\revslider\Helper;


use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RevSliderFunctions
{
    public static function changelog_path(){
        $file =  RevSliderFile::getPathLocalFile('changelog.html');
        return $file;
    }
    public static function current_link()
    {
        $relative_path = \Drupal::request()->getPathInfo();
        return self::home_url().$relative_path;
    }
    public static function home_url()
    {
        return $GLOBALS['base_url'];
    }
    public static function size_format( $bytes, $decimals = 0 ) {
        $quant = array(
            'TB' => 1024*1024*1024*1024,
            'GB' => 1024*1024*1024,
            'MB' => 1024*1024,
            'KB' => 1024,
            'B'  => 1,
        );

        if ( 0 === $bytes ) {
            return number_format( 0, $decimals ) . ' B';
        }

        foreach ( $quant as $unit => $mag ) {
            if ( doubleval( $bytes ) >= $mag ) {
                return number_format( $bytes / $mag, $decimals ) . ' ' . $unit;
            }
        }

        return false;
    }
    public static function is_ssl()
    {
        $request = \Drupal::request();
        return $request->getScheme() === 'http';
    }
    public static function rev_aq_resize( $url, $width = null, $height = null, $crop = null, $single = true, $upscale = false ) {
        $aq_resize = RevSliderAqResize::getInstance();
        return $aq_resize->process( $url, $width, $height, $crop, $single, $upscale );
    }
    public static function is_mobile()
    {
        if ( empty($_SERVER['HTTP_USER_AGENT']) ) {
            $is_mobile = false;
        } elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false // many mobile devices (all iPhone, iPad, etc.)
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
            || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false ) {
            $is_mobile = true;
        } else {
            $is_mobile = false;
        }

        return $is_mobile;
    }
    public static function array_merge_multi_level(array $arr1,array $arr2)
    {
        $keys = array_keys($arr1);
        $raw_result = array_merge($arr1,$arr2);
        foreach ($keys as $key)
        {
            if(is_array($arr1[$key]) && isset($arr2[$key]) && is_array($arr2[$key]))
            {
                $raw_result[$key] = self::array_merge_multi_level($arr1[$key],$arr2[$key]);
            }
        }
        return $raw_result;
    }
    public static function wp_get_attachment_image_src( $attachment_id, $size = 'thumbnail', $icon = false ) {
        // get a thumbnail or intermediate image if there is one
        $src =  self::get_image_url_by_id($attachment_id);
        $size = @getimagesize($src);
        $image = array($src);
        if(is_array($size))
        {
            $image[1] = $size[0];
            $image[2] = $size[1];
        }

//        return apply_filters( 'wp_get_attachment_image_src', $image, $attachment_id, $size, $icon );
        return  $image;
    }
    public static function cleanStdClassToArray($arr){
        $arr = (array)$arr;

        $arrNew = array();

        foreach($arr as $key=>$item){
            $arrNew[$key] = $item;
        }

        return($arrNew);
    }
    public static function stripslashes_deep($value){
        $value = is_array($value) ?
            array_map( array(self::class, 'stripslashes_deep'), $value) :
            stripslashes($value);

        return $value;
    }
    public static function wp_get_attachment_metadata( $post_id = 0, $unfiltered = false ) {

//        return apply_filters( 'wp_get_attachment_metadata', $data, $post->ID );
        return false;
    }
    public static function get_intermediate_image_sizes() {
        //$_wp_additional_image_sizes = wp_get_additional_image_sizes();
        $image_sizes = array('thumbnail', 'medium', 'medium_large', 'large'); // Standard sizes
//        if ( ! empty( $_wp_additional_image_sizes ) ) {
//            $image_sizes = array_merge( $image_sizes, array_keys( $_wp_additional_image_sizes ) );
//        }

        return $image_sizes;
    }
    public static function get_url_base()
    {
        $request = \Drupal::request();
        return $request->getScheme().'://';
    }
    public static function set_icon_sets($icon_sets){

        $icon_sets[] = 'fa-icon-';
        $icon_sets[] = 'pe-7s-';

        return $icon_sets;
    }
    public static function get_biggest_device_setting($obj, $enabled_devices){

        if($enabled_devices['desktop'] == 'on'){
            if(isset($obj->desktop) && $obj->desktop != ''){
                return $obj->desktop;
            }
        }

        if($enabled_devices['notebook'] == 'on'){
            if(isset($obj->notebook) && $obj->notebook != ''){
                return $obj->notebook;
            }
        }

        if($enabled_devices['tablet'] == 'on'){
            if(isset($obj->tablet) && $obj->tablet != ''){
                return $obj->tablet;
            }
        }

        if($enabled_devices['mobile'] == 'on'){
            if(isset($obj->mobile) && $obj->mobile != ''){
                return $obj->mobile;
            }
        }

        return '';
    }
    public static function normalize_device_settings($obj, $enabled_devices, $return = 'obj', $set_to_if = array()){ //array -> from -> to
        /*desktop
        notebook
        tablet
        mobile*/

        if(!empty($set_to_if)){
            foreach($obj as $key => $value) {
                foreach($set_to_if as $from => $to){
                    if(trim($value) == $from) $obj->$key = $to;
                }
            }
        }

        $inherit_size = self::get_biggest_device_setting($obj, $enabled_devices);
        if($enabled_devices['desktop'] == 'on'){
            if(!isset($obj->desktop) || $obj->desktop === ''){
                $obj->desktop = $inherit_size;
            }else{
                $inherit_size = $obj->desktop;
            }
        }else{
            $obj->desktop = $inherit_size;
        }

        if($enabled_devices['notebook'] == 'on'){
            if(!isset($obj->notebook) || $obj->notebook === ''){
                $obj->notebook = $inherit_size;
            }else{
                $inherit_size = $obj->notebook;
            }
        }else{
            $obj->notebook = $inherit_size;
        }

        if($enabled_devices['tablet'] == 'on'){
            if(!isset($obj->tablet) || $obj->tablet === ''){
                $obj->tablet = $inherit_size;
            }else{
                $inherit_size = $obj->tablet;
            }
        }else{
            $obj->tablet = $inherit_size;
        }

        if($enabled_devices['mobile'] == 'on'){
            if(!isset($obj->mobile) || $obj->mobile === ''){
                $obj->mobile = $inherit_size;
            }else{
                $inherit_size = $obj->mobile;
            }
        }else{
            $obj->mobile = $inherit_size;
        }

        switch($return){
            case 'obj':
                //order according to: desktop, notebook, tablet, mobile
                $new_obj = new \stdClass();
                $new_obj->desktop = $obj->desktop;
                $new_obj->notebook = $obj->notebook;
                $new_obj->tablet = $obj->tablet;
                $new_obj->mobile = $obj->mobile;
                return $new_obj;
                break;
            case 'html-array':
                if($obj->desktop === $obj->notebook && $obj->desktop === $obj->mobile && $obj->desktop === $obj->tablet){
                    return $obj->desktop;
                }else{
                    return "['".@$obj->desktop."','".@$obj->notebook."','".@$obj->tablet."','".@$obj->mobile."']";
                }
                break;
        }

        return $obj;
    }

    public static function boolToStr($bool){
        if(gettype($bool) == "string")
            return($bool);
        if($bool == true)
            return("true");
        else
            return("false");
    }
    public static function get_svg_sets_full(){

        $svg_sets = self::get_svg_sets_url();

        $svg = array();

        if(!empty($svg_sets)){
            foreach($svg_sets as $handle => $values){
                $svg[$handle] = array();

                if($dir = opendir($values['path'])) {
                    while(false !== ($file = readdir($dir))){
                        if ($file != "." && $file != "..") {
                            $filetype = pathinfo($file);

                            if(isset($filetype['extension']) && $filetype['extension'] == 'svg'){
                                $svg[$handle][$file] = $values['url'].$file;
                            }
                        }
                    }
                }
            }
        }

//        $svg = apply_filters('revslider_get_svg_sets_full', $svg);

        return $svg;
    }
    public static function get_svg_sets_url(){
        $svg_sets = array();

        $path = RevSliderFunctions::asset_dir('public/assets/svg/');
        $url = RevSliderFunctions::asset( 'public/assets/svg/');

        if(!file_exists($path.'action/ic_3d_rotation_24px.svg')){ //the path needs to be changed to the uploads folder then
            $upload_dir = RevSliderFile::uploadDirInfo();
            $path = $upload_dir['basedir'].'/revslider/assets/svg/';
            $url = $upload_dir['baseurl'].'/revslider/assets/svg/';
        }

        $svg_sets['Actions'] = array('path' => $path.'action/', 'url' => $url.'action/');
        $svg_sets['Alerts'] = array('path' => $path.'alert/', 'url' => $url.'alert/');
        $svg_sets['AV'] = array('path' => $path.'av/', 'url' => $url.'av/');
        $svg_sets['Communication'] = array('path' => $path.'communication/', 'url' => $url.'communication/');
        $svg_sets['Content'] = array('path' => $path.'content/', 'url' => $url.'content/');
        $svg_sets['Device'] = array('path' => $path.'device/', 'url' => $url.'device/');
        $svg_sets['Editor'] = array('path' => $path.'editor/', 'url' => $url.'editor/');
        $svg_sets['File'] = array('path' => $path.'file/', 'url' => $url.'file/');
        $svg_sets['Hardware'] = array('path' => $path.'hardware/', 'url' => $url.'hardware/');
        $svg_sets['Images'] = array('path' => $path.'image/', 'url' => $url.'image/');
        $svg_sets['Maps'] = array('path' => $path.'maps/', 'url' => $url.'maps/');
        $svg_sets['Navigation'] = array('path' => $path.'navigation/', 'url' => $url.'navigation/');
        $svg_sets['Notifications'] = array('path' => $path.'notification/', 'url' => $url.'notification/');
        $svg_sets['Places'] = array('path' => $path.'places/', 'url' => $url.'places/');
        $svg_sets['Social'] = array('path' => $path.'social/', 'url' => $url.'social/');
        $svg_sets['Toggle'] = array('path' => $path.'toggle/', 'url' => $url.'toggle/');


//        $svg_sets = apply_filters('revslider_get_svg_sets', $svg_sets);

        return $svg_sets;
    }
    public static function current_time($type ='')
    {
        switch ($type)
        {
            case 'timestamp' :
                return time();
                break;
            default:
                return time();
                break;
        }
    }
    public static function esc_url( $url) {
       return Html::escape(UrlHelper::stripDangerousProtocols($url));
    }
    public static function strToBool($str){
        if(is_bool($str))
            return($str);

        if(empty($str))
            return(false);

        if(is_numeric($str))
            return($str != 0);

        $str = strtolower($str);
        if($str == "true")
            return(true);

        return(false);
    }
    public static function is_super_admin()
    {
        return true;
    }
    public static function is_admin_bar_showing()
    {
        return true;
    }
    public static function current_user_can($method)
    {
        return true;
    }
    public static function assocToArray($assoc){
        $arr = array();
        foreach($assoc as $item)
            $arr[] = $item;

        return($arr);
    }
    public static function isHasParams(array $arr,$params)
    {
        $is_exists = true;
        if(is_string($params))
            $is_exists = !empty($arr[$params]);
        elseif(is_array($params))
            foreach ($params as $param)
                if(empty($arr[$param]))
                {
                    $is_exists = false;
                    break;
                }
        return $is_exists;
    }
    public static function normalizeTextareaContent($content){
        if(empty($content))
            return($content);
        $content = stripslashes($content);
        $content = trim($content);
        return($content);
    }
    public static function isRS_DEMO()
    {
        return false;
    }
    public static function isAdminUser()
    {
        return true;
    }
    public static function getPostVariable($var_name,$default = '')
    {
        return self::getPostGetVar($var_name,$default);
    }
    public static function get_all_image_sizes($type = 'gallery'){
    $custom_sizes = array();

    switch($type){
        case 'flickr':
            $custom_sizes = array(
                'original' => t('Original'),
                'large' => t('Large'),
                'large-square' => t('Large Square'),
                'medium' => t('Medium'),
                'medium-800' => t('Medium 800'),
                'medium-640' => t('Medium 640'),
                'small' => t('Small'),
                'small-320' => t('Small 320'),
                'thumbnail'=> t('Thumbnail'),
                'square' => t('Square')
            );
            break;
        case 'instagram':
            $custom_sizes = array(
                'standard_resolution' => t('Standard Resolution'),
                'thumbnail' => t('Thumbnail'),
                'low_resolution' => t('Low Resolution')
            );
            break;
        case 'twitter':
            $custom_sizes = array(
                'large' => t('Standard Resolution')
            );
            break;
        case 'facebook':
            $custom_sizes = array(
                'full' => t('Original Size'),
                'thumbnail' => t('Thumbnail')
            );
            break;
        case 'youtube':
            $custom_sizes = array(
                'default' => t('Default'),
                'medium' => t('Medium'),
                'high' => t('High'),
                'standard' => t('Standard'),
                'maxres' => t('Max. Res.')
            );
            break;
        case 'vimeo':
            $custom_sizes = array(
                'thumbnail_small' => t('Small'),
                'thumbnail_medium' => t('Medium'),
                'thumbnail_large' => t('Large'),
            );
            break;
        case 'gallery':
        default:
            $added_image_sizes = RevSliderFunctions::get_intermediate_image_sizes();
            if(!empty($added_image_sizes) && is_array($added_image_sizes)){
                foreach($added_image_sizes as $key => $img_size_handle){
                    $custom_sizes[$img_size_handle] = ucwords(str_replace('_', ' ', $img_size_handle));
                }
            }
            $img_orig_sources = array(
                'full' => t('Original Size'),
                'thumbnail' => t('Thumbnail'),
                'medium' => t('Medium'),
                'large' => t('Large')
            );
            $custom_sizes = array_merge($img_orig_sources, $custom_sizes);
            break;
    }

    return $custom_sizes;
}

    public static function getPostGetVar($key,$defaultValue = ""){

        return \Drupal::request()->get($key,$defaultValue);
    }
    public static function getRequestVariable($var_name,$default = '')
    {
        return \Drupal::request()->get($var_name,$default);
    }
    public static function isHasRequestVariable($list)
    {
        $list_check = array();
        if(is_string($list))
            $list_check[] = $list;
        elseif(is_array($list))
            $list_check = $list;
        else
            return false;
        foreach ($list_check as $key)
        {
            $var = self::getRequestVariable($key,null);
            if(is_null($var))
                return false;
        }
        return true;
    }
    public static function getFileUpload($field)
    {
        return \Drupal::request()->files->get($field);
    }
    public static function validateNumeric($val,$fieldName=""){
        self::validateNotEmpty($val,$fieldName);

        if(empty($fieldName))
            $fieldName = "Field";

        if(!is_numeric($val))
            self::throwError("$fieldName should be numeric ");
    }
    public static function validateNotEmpty($val,$fieldName=""){
        if(empty($fieldName))
            $fieldName = "Field";

        if(empty($val) && is_numeric($val) == false)
            self::throwError("Field <b>$fieldName</b> should not be empty");
    }
    public static function throwError($message,$code=null){
        if(!RevSliderGlobals::DEBUG)
        {
            ob_clean();
            echo new RedirectResponse(Url::fromRoute('revslider.admin')->toString());
            die();
        }

        if(!empty($code)){
            throw new \Exception($message,$code);
        }else{
            throw new \Exception($message);
        }
    }
    public static function unzipFile($file_path,$path_extract)
    {
        if(empty($file_path) || empty($path_extract))
            return false;
        $use_file_path = RevSliderFile::wrapToPath($file_path);
        $use_path_extract = RevSliderFile::wrapToPath($path_extract);
        $zip = new \ZipArchive();
        $res = $zip->open($use_file_path);
        if($res === true)
        {
            $zip->extractTo($use_path_extract);
            $zip->close();
        }
        else
        {
            self::throwError('Extract failed!!!');
            return false;
        }
        return true;
    }
    public static function toArray($obj)
    {
        return json_decode(json_encode($obj),true);
    }
    public static function create_nonce()
    {
        return '';
    }
    public static function translate_settings_to_v5($settings){

        if(isset($settings['navigaion_type'])){
            switch($settings['navigaion_type']){
                case 'none': // all is off, so leave the defaults
                    break;
                case 'bullet':
                    $settings['enable_bullets'] = 'on';
                    $settings['enable_thumbnails'] = 'off';
                    $settings['enable_tabs'] = 'off';

                    break;
                case 'thumb':
                    $settings['enable_bullets'] = 'off';
                    $settings['enable_thumbnails'] = 'on';
                    $settings['enable_tabs'] = 'off';
                    break;
            }
            unset($settings['navigaion_type']);
        }

        if(isset($settings['navigation_arrows'])){
            $settings['enable_arrows'] = ($settings['navigation_arrows'] == 'solo' || $settings['navigation_arrows'] == 'nexttobullets') ? 'on' : 'off';
            unset($settings['navigation_arrows']);
        }

        if(isset($settings['navigation_style'])){
            $settings['navigation_arrow_style'] = $settings['navigation_style'];
            $settings['navigation_bullets_style'] = $settings['navigation_style'];
            unset($settings['navigation_style']);
        }

        if(isset($settings['navigaion_always_on'])){
            $settings['arrows_always_on'] = $settings['navigaion_always_on'];
            $settings['bullets_always_on'] = $settings['navigaion_always_on'];
            $settings['thumbs_always_on'] = $settings['navigaion_always_on'];
            unset($settings['navigaion_always_on']);
        }

        if(isset($settings['hide_thumbs']) && !isset($settings['hide_arrows']) && !isset($settings['hide_bullets'])){ //as hide_thumbs is still existing, we need to check if the other two were already set and only translate this if they are not set yet
            $settings['hide_arrows'] = $settings['hide_thumbs'];
            $settings['hide_bullets'] = $settings['hide_thumbs'];
        }

        if(isset($settings['navigaion_align_vert'])){
            $settings['bullets_align_vert'] = $settings['navigaion_align_vert'];
            $settings['thumbnails_align_vert'] = $settings['navigaion_align_vert'];
            unset($settings['navigaion_align_vert']);
        }

        if(isset($settings['navigaion_align_hor'])){
            $settings['bullets_align_hor'] = $settings['navigaion_align_hor'];
            $settings['thumbnails_align_hor'] = $settings['navigaion_align_hor'];
            unset($settings['navigaion_align_hor']);
        }

        if(isset($settings['navigaion_offset_hor'])){
            $settings['bullets_offset_hor'] = $settings['navigaion_offset_hor'];
            $settings['thumbnails_offset_hor'] = $settings['navigaion_offset_hor'];
            unset($settings['navigaion_offset_hor']);
        }

        if(isset($settings['navigaion_offset_hor'])){
            $settings['bullets_offset_hor'] = $settings['navigaion_offset_hor'];
            $settings['thumbnails_offset_hor'] = $settings['navigaion_offset_hor'];
            unset($settings['navigaion_offset_hor']);
        }

        if(isset($settings['navigaion_offset_vert'])){
            $settings['bullets_offset_vert'] = $settings['navigaion_offset_vert'];
            $settings['thumbnails_offset_vert'] = $settings['navigaion_offset_vert'];
            unset($settings['navigaion_offset_vert']);
        }

        if(isset($settings['show_timerbar']) && !isset($settings['enable_progressbar'])){
            if($settings['show_timerbar'] == 'hide'){
                $settings['enable_progressbar'] = 'off';
                $settings['show_timerbar'] = 'top';
            }else{
                $settings['enable_progressbar'] = 'on';
            }
        }

        return $settings;
    }
    public static function hex2rgba($hex, $transparency = false, $raw = false, $do_rgb = false) {
        if($transparency !== false){
            $transparency = ($transparency > 0) ? number_format( ( $transparency / 100 ), 2, ".", "" ) : 0;
        }else{
            $transparency = 1;
        }

        $hex = str_replace("#", "", $hex);

        if(strlen($hex) == 3) {
            $r = hexdec(substr($hex,0,1).substr($hex,0,1));
            $g = hexdec(substr($hex,1,1).substr($hex,1,1));
            $b = hexdec(substr($hex,2,1).substr($hex,2,1));
        } else if(self::isrgb($hex)){
            return $hex;
        } else {
            $r = hexdec(substr($hex,0,2));
            $g = hexdec(substr($hex,2,2));
            $b = hexdec(substr($hex,4,2));
        }

        if($do_rgb){
            $ret = $r.', '.$g.', '.$b;
        }else{
            $ret = $r.', '.$g.', '.$b.', '.$transparency;
        }
        if($raw){
            return $ret;
        }else{
            return 'rgba('.$ret.')';
        }

    }
    public static function isrgb($rgba){
        if(strpos($rgba, 'rgb') !== false) return true;

        return false;
    }
    public static function add_missing_val($obj, $set_to = 'px'){

        if(is_array($obj)){
            foreach($obj as $key => $value){
                if(strpos($value, $set_to) === false){
                    $obj[$key] = $value.$set_to;
                }
            }
        }elseif(is_object($obj)){
            foreach($obj as $key => $value){
                if(strpos($value, $set_to) === false){
                    $obj->$key = $value.$set_to;
                }
            }
        }else{
            if(strpos($obj, $set_to) === false){
                $obj .= $set_to;
            }
        }

        return $obj;
    }
    public static function clear_error_in_string($m){
        return 's:'.strlen($m[2]).':"'.$m[2].'";';
    }
    public static function getVal($arr,$key,$altVal=""){
        if(is_array($arr)){
            if(isset($arr[$key])){
                return($arr[$key]);
            }
        }elseif(is_object($arr)){
            if(isset($arr->$key)){
                return($arr->$key);
            }
        }
        return($altVal);
    }
    public static function check_file_in_zip($d_path, $image, $alias, &$alreadyImported, $add_path = false){
        //  global $wp_filesystem;

        if(trim($image) !== ''){
            if(strpos($image, 'http') !== false){
            }else{
                $strip = false;
                $zimage =   RevSliderFile::fileExists( $d_path.'images/'.$image );
                if(!$zimage){
                    $zimage = RevSliderFile::fileExists( str_replace('//', '/', $d_path.'images/'.$image) );
                    $strip = true;
                }

                if(!$zimage){
                    echo $image.t(' not found!<br>');
                }else{
                    if(!isset($alreadyImported['images/'.$image])){
                        //check if we are object folder, if yes, do not import into media library but add it to the object folder
                        $uimg = ($strip == true) ? str_replace('//', '/', 'images/'.$image) : $image; //pclzip
//                        $object_library = (strpos($uimg, 'revslider/objects/') === 0) ? true : false;

//                        if($object_library === true){ //copy the image to the objects folder if false
//                            $objlib = new RevSliderObjectLibrary();
//                            $importImage = $objlib->_import_object($d_path.'images/'.$uimg);
//                        }else{
                        $importImage = RevSliderFunctionsWP::import_media($d_path.'images/'.$uimg, $alias.'/');
//                        }
                        if($importImage !== false){
                            $alreadyImported['images/'.$image] = $importImage['path'];

                            $image = $importImage['path'];
                        }
                    }else{
                        $image = $alreadyImported['images/'.$image];
                    }
                }
                if($add_path){
                    $upload_dir = RevSliderFile::uploadDirInfo();
                    $cont_url = $upload_dir['baseurl'];
                    $image = str_replace('uploads/uploads/', 'uploads/', $cont_url . '/' . $image);
                }
            }
        }

        return $image;
    }
    public static function convertStdClassToArray($arr){
        $arr = (array)$arr;

        $arrNew = array();

        foreach($arr as $key=>$item){
            $item = (array)$item;
            $arrNew[$key] = $item;
        }

        return($arrNew);
    }
    public static function getHtmlLink($link,$text,$id="",$class=""){

        if(!empty($class))
            $class = " class='$class'";

        if(!empty($id))
            $id = " id='$id'";

        $html = "<a href=\"$link\"".$id.$class.">$text</a>";
        return($html);
    }
    public static function getVar($arr,$key,$defaultValue = ""){
        $val = $defaultValue;
        if(isset($arr[$key])) $val = $arr[$key];
        return($val);
    }
    public static function get_image_id_by_url($thumb)
    {
        return RevSliderFile::getFileIdFromUrl($thumb);
    }
    public static function get_image_url_by_id($id,$size = 'any')
    {
        if(empty($id))
            return false;
        return RevSliderFile::getFileUrlFromId($id,'image');
    }
    public static function rgba2hex($rgba){
        if(strtolower($rgba) == 'transparent') return $rgba;

        $temp = explode(',', $rgba);
        $rgb = array();
        if(count($temp) == 4) unset($temp[3]);
        foreach($temp as $val){
            $t = dechex(preg_replace('/[^\d.]/', '', $val));
            if(strlen($t) < 2) $t = '0'.$t;
            $rgb[] = $t;
        }

        return '#'.implode('', $rgb);
    }
    public static function get_trans_from_rgba($rgba, $in_percent = false){
        if(strtolower($rgba) == 'transparent') return 100;

        $temp = explode(',', $rgba);
        if(count($temp) == 4){
            return ($in_percent) ? preg_replace('/[^\d.]/', '', $temp[3]) : preg_replace('/[^\d.]/', "", $temp[3]) * 100;
        }
        return 100;
    }
    public static function asset($path,$include_scheme = true)
    {
        if(empty($path))
            $path = '';
        if(strlen($path) > 0 && $path[0] === '/')
            $path= substr($path,1);
        $module_path = RevSliderFile::getUrlLocalFile();
        $request = \Drupal::request();
        $result = $request->getUriForPath($module_path.'/assets/'.$path);
        if(!$include_scheme)
            $result = '//'.explode('://',$result)[1];
        return $result;
    }
    public static function asset_dir($path)
    {
        $asset = RevSliderFile::getUrlLocalFile();
        $asset = str_replace('/',DIRECTORY_SEPARATOR,$asset);
        $asset = str_replace('\\',DIRECTORY_SEPARATOR,$asset);
        $dir = explode($asset,__DIR__)[0].$asset;
        return $dir.'/assets/'.$path;
    }
    public static function getMenuRole()
    {
        return 'admin';
    }
    public static function getViewUrl($viewName,$add_arams=array()){
        $params = array();
        $params = array_merge($add_arams,$params);
        $params = array_merge($params,array(
            'view'=>$viewName
        ));
        $link = Url::fromRoute('revslider.admin',array($params))->toString();
        return($link);
    }
    public static function getServerName()
    {
        return \Drupal::request()->getHost();
    }
    public static function trimArrayItems($arr){
        if(gettype($arr) != "array")
            RevSliderFunctions::throwError("trimArrayItems error: The type must be array");
        $keys = array_keys($arr);
        foreach ($keys as $key)
        {
            if(is_array($arr[$key]))
                self::trimArrayItems($arr[$key]);
            elseif (is_string($arr[$key]))
                trim($arr[$key]);;
        }
        return $arr;
    }
    public static function sanitize_text_fields( $str) {
        return Html::escape($str);
    }
    public static function esc_attr($str)
    {
        return Html::escape($str);
    }
    public static function sanitize_title($title)
    {
        $slug = trim($title); // trim the string
        $slug= preg_replace('/[^a-zA-Z0-9 -]/','',$slug ); // only take alphanumerical characters, but keep the spaces and dashes too...
        $slug= str_replace(' ','-', $slug); // replace spaces by dashes
        $slug= strtolower($slug);  // make it lowercase
        return $slug;
    }
    public static function jsonDecodeFromClientSide($data){
        $data = str_replace('</','<\/',$data);
//        ob_start();
//         var_dump($data);
//         $a = ob_get_clean();
//         echo htmlentities($data);
//         die();
        $data = str_replace('&#092;"','\"',$data);
//        $data = stripslashes($data);
        $data = json_decode($data);
        $data = (array)$data;
        return($data);
    }
    public static function is_plugin_validated()
    {
        return 'true';
       // $validated = RevSliderOptions::getOptions('revslider-valid', 'false');
    }
    public static function jsonEncodeForClientSide($arr){
        $json = "";
        if(!empty($arr)){
            $json = json_encode($arr);
            $json = addslashes($json);
        }

        if(empty($json)) $json = '{}';

        $json = "'".$json."'";

        return($json);
    }
    public static function url_ajax_action(array $args = array())
    {
        $params = array_merge($args,array(
           'route'=> 'actions'
        ));
        $params = array_merge($args,$params);
        return self::ajax_url($params);
    }
    public static function ajax_url(array $args = array())
    {
        $default_params=array();
        $params = array_merge($args,$default_params);
        return Url::fromRoute('revslider.ajax',$params)->toString();
    }
    public static function checked($var,$parse,$echo = true)
    {
        ob_start();
        if($var == $parse)
            echo ' checked="checked" ';
        $result = ob_get_clean();
        if($echo)
            echo $result;
        return $result;
    }
    public static function selected($var,$parse,$echo = true)
    {
        ob_start();
        if($var == $parse)
            echo ' selected="selected" ';
        $result = ob_get_clean();
        if($echo)
            echo $result;
        return $result;
    }
    public static function get_icon_sets(){
        $icon_sets = array("fa-icon-", "pe-7s-");

//        $icon_sets = apply_filters('revslider_mod_icon_sets', $icon_sets);

        return $icon_sets;
    }
}