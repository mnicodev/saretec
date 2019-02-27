<?php
/**
 * Created by FsFlex.
 * User: VH
 * Date: 7/6/2017
 * Time: 11:06 AM
 */

namespace Drupal\revslider\Helper;


class RevSliderFunctionsWP
{
    public static $urlSite;
    public static $urlAdmin;

    const SORTBY_NONE = "none";
    const SORTBY_ID = "ID";
    const SORTBY_AUTHOR = "author";
    const SORTBY_TITLE = "title";
    const SORTBY_SLUG = "name";
    const SORTBY_DATE = "date";
    const SORTBY_LAST_MODIFIED = "modified";
    const SORTBY_RAND = "rand";
    const SORTBY_COMMENT_COUNT = "comment_count";
    const SORTBY_MENU_ORDER = "menu_order";

    const ORDER_DIRECTION_ASC = "ASC";
    const ORDER_DIRECTION_DESC = "DESC";

    const THUMB_SMALL = "thumbnail";
    const THUMB_MEDIUM = "medium";
    const THUMB_LARGE = "large";
    const THUMB_FULL = "full";

    const STATE_PUBLISHED = "publish";
    const STATE_DRAFT = "draft";
    public static function getPostTypesAssoc($arrPutToTop = array()){
        return array();
    }
    public static function getImageUrlFromPath($pathImage){
        //protect from absolute url
        $pathLower = strtolower($pathImage);
        if(strpos($pathLower, "http://") !== false || strpos($pathLower, "https://") !== false || strpos($pathLower, "www.") === 0)
            return($pathImage);

        $urlImage = self::getUrlUploads().'/'.$pathImage;
        return($urlImage);
    }
    public static function getUrlUploads(){

        $info = RevSliderFile::uploadDirInfo();
        return $info['baseurl'];

    }
    public static function import_media($file_url, $folder_name) {

        $ul_dir = RevSliderFile::uploadDirInfo();
        //$attrDir= 'revslider';
        $artDir = '';

        //if the directory doesn't exist, create it
        if(!file_exists($ul_dir['basedir'].'/'.$artDir)) mkdir($ul_dir['basedir'].'/'.$artDir);
        if(!file_exists($ul_dir['basedir'].'/'.$artDir.$folder_name)) mkdir($ul_dir['basedir'].'/'.$artDir.$folder_name);

        //rename the file... alternatively, you could explode on "/" and keep the original file name

        $filename = basename($file_url);
        //$siteurl = get_option('siteurl');

        if(@fclose(@fopen($file_url, "r"))){ //make sure the file actually exists

            $saveDir = $ul_dir['basedir'].'/'.$artDir.$folder_name.$filename;

            $atc_id = self::get_image_id_by_url($artDir.$folder_name.$filename);

            if($atc_id == false || $atc_id == NULL){
//                copy($file_url, $saveDir);
                $attach_id = RevSliderFile::downloadUrl($file_url,array(
                    'path'=>dirname($saveDir),
                    'name'=>basename($saveDir),
                    'file_type'=>'any',
                    'return_type'=>'id'
                ));
//                $file_info = getimagesize($saveDir);
//
//                //create an array of attachment data to insert into wp_posts table
//                $artdata = array(
//                    'post_author' => 1,
//                    'post_date' => current_time('mysql'),
//                    'post_date_gmt' => current_time('mysql'),
//                    'post_title' => $filename,
//                    'post_status' => 'inherit',
//                    'comment_status' => 'closed',
//                    'ping_status' => 'closed',
//                    'post_name' => sanitize_title_with_dashes(str_replace("_", "-", $filename)),
//                    'post_modified' => current_time('mysql'),
//                    'post_modified_gmt' => current_time('mysql'),
//                    'post_parent' => '',
//                    'post_type' => 'attachment',
//                    'guid' => $ul_dir['baseurl'].'/'.$artDir.$folder_name.$filename,
//                    'post_mime_type' => $file_info['mime'],
//                    'post_excerpt' => '',
//                    'post_content' => ''
//                );
//                //insert the database record
//                $attach_id = wp_insert_attachment($artdata, $artDir.$folder_name.$filename);
            }else{
                $attach_id = $atc_id;
            }

            //generate metadata and thumbnails
//            if($attach_data = wp_generate_attachment_metadata($attach_id, $saveDir)) wp_update_attachment_metadata($attach_id, $attach_data);
//            if(!self::isMultisite()) $artDir = 'uploads/'.$artDir;
            return array("id" => $attach_id, "path" => $artDir.$folder_name.$filename);
        }else{
            return false;
        }
    }
    public static function get_image_id_by_url($image_url) {
        $attachment_id = RevSliderFile::getFileIdFromUrl($image_url,'image');
        return $attachment_id;
    }
    public static function getUrlAttachmentImage($thumbID,$size = self::THUMB_FULL){
//        $arrImage = wp_get_attachment_image_src($thumbID,$size);
//
//        if(empty($arrImage))
//            return(false);
//
//        $url = RevSliderFunctions::getVal($arrImage, 0);
//        return($url);
        return RevSliderFile::getFileUrlFromId($thumbID);
    }
    public static function getPathContent($path)
    {
        return RevSliderFile::wrapToPath($path);
    }
    public static function getImagePathFromURL($urlImage){

        $baseUrl = self::getUrlUploads();
        $pathImage = str_replace($baseUrl, "", $urlImage);

        return($pathImage);
    }
}