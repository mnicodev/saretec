<?php
/**
 * Created by FsFlex.
 * User: VH
 * Date: 7/10/2017
 * Time: 10:54 AM
 */

namespace Drupal\revslider\Model;


use Drupal\revslider\Helper\RevSliderDB;
use Drupal\revslider\Helper\RevSliderFile;
use Drupal\revslider\Helper\RevSliderFunctions;
use Drupal\revslider\Helper\RevSliderFunctionsWP;
use Drupal\revslider\Helper\RevSliderGlobals;

class Slide extends RevSliderElementsBase
{
    private $id;
    private $sliderID;
    private $slideOrder;

    private $imageUrl;
    private $imageID;
    private $imageThumb;
    private $imageFilepath;
    private $imageFilename;

    private $params;
    private $arrLayers;
    private $settings;
    private $arrChildren = null;
    private $slider;

    private $static_slide = false;

    private $postData;
    public $templateID;

    public function __construct()
    {
        parent::__construct();
    }

    public function setID($id){
        $this->id = $id;
    }
    /**
     * get params for export
     */
    public function getParamsForExport(){
        $arrParams = $this->getParams();
        $urlImage = RevSliderFunctions::getVal($arrParams, "image");
        if(!empty($urlImage))
            $arrParams["image"] = RevSliderFunctionsWP::getImagePathFromURL($urlImage);

        //check if we are transparent or solid and remove unneeded image
        $bgtype = RevSliderFunctions::getVal($arrParams, "background_type", 'transparent');
        switch($bgtype){
            case 'transparent':
            case 'trans':
            case 'solid':
                $arrParams["image"] = '';
                break;
        }

        return $arrParams;
    }
    public function setLayersRaw($layers){
        $this->arrLayers = $layers;
    }
    public function saveLayers(){
        $this->validateInited();
        $table = ($this->static_slide) ? RevSliderGlobals::$table_static_slides : RevSliderGlobals::$table_slides;

//        $this->arrLayers = apply_filters('revslider_slide_saveLayers', $this->arrLayers, $this->static_slide, $this);
        RevSliderDB::instance(array(
            'table'=>$table,
            'where'=>array('id',$this->id)
        ))->update(array(
            'layers' => json_encode($this->arrLayers)
        ));
    }
    public function saveParams(){
        $this->validateInited();
        $table = ($this->static_slide) ? RevSliderGlobals::$table_static_slides : RevSliderGlobals::$table_slides;

//        $this->params = apply_filters('revslider_slide_saveParams', $this->params, $this->static_slide, $this);
        RevSliderDB::instance(array(
            'table'=>$table,
            'where'=>array('id',$this->id)
        ))->update(array(
            'params' => json_encode($this->params)
        ));
    }
    public function getLayersForExport($useDummy = false){
        $this->validateInited();
        $arrLayersNew = array();
        foreach($this->arrLayers as $key=>$layer){
            $imageUrl = RevSliderFunctions::getVal($layer, "image_url");
            if(!empty($imageUrl))
                $layer["image_url"] = RevSliderFunctionsWP::getImagePathFromURL($layer["image_url"]);

            $arrLayersNew[] = $layer;
        }

        return $arrLayersNew;
    }
    public static function isSlideByID($slideid){
        try{
            if(strpos($slideid, 'static_') !== false){

                $sliderID = str_replace('static_', '', $slideid);

                RevSliderFunctions::validateNumeric($sliderID,"Slider ID");

                $record = RevSliderDB::instance(array(
                    'table'=>RevSliderGlobals::$table_static_slides,
                    'where'=>array('slider_id',$sliderID)
                ))->first();

                if(empty($record))
                    return false;

                return true;

            }else{


                $record = RevSliderDB::instance(array(
                    'table'=>RevSliderGlobals::$table_slides,
                    'where'=>array('id',$slideid)
                ))->first();

                if(empty($record))
                    return false;

                return true;

            }
        }catch(\Exception $e){
            return false;
        }
    }
    public function getThumbUrl(){
        $thumbUrl = $this->imageUrl;
        if(!empty($this->imageThumb))
            $thumbUrl = $this->imageThumb;

        return($thumbUrl);
    }
    public function getSettings(){
        $this->validateInited();
//        return apply_filters('revslider_slide_getSettings', $this->settings, $this);
        return $this->settings;
    }
    public static function translateIntoSizes(){

        return array(
            'align_hor',
            'align_vert',
            'top',
            'left',
            'font-size',
            'line-height',
            'font-weight',
            'color',
            'max_width',
            'max_height',
            'whitespace',
            'video_height',
            'video_width',
            'scaleX',
            'scaleY',
            'margin',
            'padding',
            'text-align',
            'letter-spacing'
        );
    }
    public function getID()
    {
        return ($this->id);
    }
    public function toggleSlideStatFromData($data){

        $sliderID = RevSliderFunctions::getVal($data, "slider_id");
        $slider = new Slider();
        $slider->initByID($sliderID);

        $slideID = RevSliderFunctions::getVal($data, "slide_id");

        $this->initByID($slideID);

        $state = $this->getParam("state","published");
        $newState = ($state == "published")?"unpublished":"published";

        $arrUpdate = array();
        $arrUpdate["state"] = $newState;

//        $arrUpdate = apply_filters('revslider_slide_toggleSlideStatFromData', $arrUpdate, $data, $this);

        $this->updateParamsInDB($arrUpdate);

        return $newState;
    }
    public function initByData($record)
    {
        //$record = apply_filters('revslider_slide_initByData', $record);

        $this->id = $record["id"];
        $this->sliderID = $record["slider_id"];
        $this->slideOrder = (isset($record["slide_order"])) ? $record["slide_order"] : '';

        $params = $record["params"];
        $params = (array)json_decode($params);

        $layers = $record["layers"];
        $layers = (array)json_decode($layers);
        $layers = RevSliderFunctions::convertStdClassToArray($layers);

        $settings = $record["settings"];
        $settings = (array)json_decode($settings);

        //$layers = $this->translateLayerSizes($layers);

        $imageID = RevSliderFunctions::getVal($params, "image_id");

        $imgResolution = RevSliderFunctions::getVal($params, 'image_source_type', 'full');

        //get image url and thumb url
        if (!empty($imageID)) {
            $this->imageID = $imageID;
            $imageUrl = RevSliderFunctionsWP::getUrlAttachmentImage($imageID, $imgResolution);
            if (empty($imageUrl)) {
                $imageUrl = RevSliderFunctions::getVal($params, "image");


                $imgID = RevSliderFile::getFileIdFromUrl($imageUrl, 'image');
                if ($imgID !== false) {
                    $imageUrl = RevSliderFunctionsWP::getUrlAttachmentImage($imgID, $imgResolution);
                }
            }

            $this->imageThumb = RevSliderFunctionsWP::getUrlAttachmentImage($imageID, RevSliderFunctionsWP::THUMB_MEDIUM);

        } else {
            $imageUrl = RevSliderFunctions::getVal($params, "image");

            $imgID = RevSliderFile::getFileIdFromUrl($imageUrl, 'image');
            if ($imgID !== false && $imgID !== null) {
                $imageUrl = RevSliderFunctionsWP::getUrlAttachmentImage($imgID, $imgResolution);
//            }else{ //we may be from the object library
//                $objlib = new RevSliderObjectLibrary();
//                $imageUrl = $objlib->get_correct_size_url($imageUrl, $imgResolution); //check for size to be used
            }
        }

//        if(is_ssl()){
//            $imageUrl = str_replace("http://", "https://", $imageUrl);
//        }

        //set image path, file and url
        $this->imageUrl = $imageUrl;

        $this->imageFilepath = RevSliderFunctionsWP::getImagePathFromURL($this->imageUrl);
        $realPath = RevSliderFile::wrapToPath($this->imageUrl);

        if (RevSliderFile::fileExists($realPath) == false)
            $this->imageFilepath = "";

        $this->imageFilename = basename($this->imageUrl);

        $this->params = $params;
        $this->arrLayers = $layers;
        $this->settings = $settings;

    }

    public function getLayerID_by_unique_id($unique_id, $static_slide)
    {
        $this->validateInited();

        if (strpos($unique_id, 'static-') !== false) {
            $unique_id = str_replace('static-', '', $unique_id);
            $layers = $static_slide->getLayers();
            if (!empty($layers)) {
                foreach ($layers as $l) {
                    $uid = RevSliderFunctions::getVal($l, 'unique_id');
                    if ($uid == $unique_id) {
                        return RevSliderFunctions::getVal($l, 'attrID');
                    }
                }
            }
        } else {
            foreach ($this->arrLayers as $l) {

                $uid = RevSliderFunctions::getVal($l, 'unique_id');
                if ($uid == $unique_id) {
                    return RevSliderFunctions::getVal($l, 'attrID');
                }
            }
        }

        return '';
    }

    public function get_image_attributes($slider_type)
    {

        $params = $this->params;
        $bgType = RevSliderFunctions::getVar($params, "background_type", "transparent");
        $bgColor = RevSliderFunctions::getVar($params, "slide_bg_color", "transparent");

        $bgFit = RevSliderFunctions::getVar($params, "bg_fit", "cover");
        $bgFitX = intval(RevSliderFunctions::getVar($params, "bg_fit_x", "100"));
        $bgFitY = intval(RevSliderFunctions::getVar($params, "bg_fit_y", "100"));

        $bgPosition = RevSliderFunctions::getVar($params, "bg_position", "center top");
        $bgPositionX = intval(RevSliderFunctions::getVar($params, "bg_position_x", "0"));
        $bgPositionY = intval(RevSliderFunctions::getVar($params, "bg_position_y", "0"));

        $bgRepeat = RevSliderFunctions::getVar($params, "bg_repeat", "no-repeat");

        $bgStyle = ' ';
        if ($bgFit == 'percentage') {
            $bgStyle .= "background-size: " . $bgFitX . '% ' . $bgFitY . '%;';
        } else {
            $bgStyle .= "background-size: " . $bgFit . ";";
        }
        if ($bgPosition == 'percentage') {
            $bgStyle .= "background-position: " . $bgPositionX . '% ' . $bgPositionY . '%;';
        } else {
            $bgStyle .= "background-position: " . $bgPosition . ";";
        }
        $bgStyle .= "background-repeat: " . $bgRepeat . ";";

        $thumb = '';
        $thumb_on = RevSliderFunctions::getVar($params, "thumb_for_admin", 'off');
        switch ($slider_type) {
            case 'gallery':
                $imageID = RevSliderFunctions::getVar($params, "image_id");
                if (empty($imageID)) {
                    $thumb = RevSliderFunctions::getVar($params, "image");

                    $imgID = RevSliderFunctions::get_image_id_by_url($thumb);
                    if ($imgID !== false) {
                        $thumb = RevSliderFunctionsWP::getUrlAttachmentImage($imgID, RevSliderFunctionsWP::THUMB_MEDIUM);
                    }
                } else {
                    $thumb = RevSliderFunctionsWP::getUrlAttachmentImage($imageID, RevSliderFunctionsWP::THUMB_MEDIUM);
                }

                if ($thumb_on == 'on') {
                    $thumb = RevSliderFunctions::getVar($params, "slide_thumb", '');
                }
                break;
            case 'posts':

                $thumb = RevSliderFunctions::asset('public/assets/sources/post.png');
                $bgStyle = 'background-size: cover;';
                break;
            case 'woocommerce':
                $thumb = RevSliderFunctions::asset('public/assets/sources/wc.png');
                $bgStyle = 'background-size: cover;';
                break;
            case 'facebook':
                $thumb = RevSliderFunctions::asset('public/assets/sources/fb.png');
                $bgStyle = 'background-size: cover;';
                break;
            case 'twitter':
                $thumb = RevSliderFunctions::asset('public/assets/sources/tw.png');
                $bgStyle = 'background-size: cover;';
                break;
            case 'instagram':
                $thumb = RevSliderFunctions::asset('public/assets/sources/ig.png');
                $bgStyle = 'background-size: cover;';
                break;
            case 'flickr':
                $thumb = RevSliderFunctions::asset('public/assets/sources/fr.png');
                $bgStyle = 'background-size: cover;';
                break;
            case 'youtube':
                $thumb = RevSliderFunctions::asset('public/assets/sources/yt.png');
                $bgStyle = 'background-size: cover;';
                break;
            case 'vimeo':
                $thumb = RevSliderFunctions::asset('public/assets/sources/vm.png');
                $bgStyle = 'background-size: cover;';
                break;
        }

        if ($thumb == '') $thumb = RevSliderFunctions::getVar($params, "image");

        $bg_fullstyle = '';
        $bg_extraClass = '';
        $data_urlImageForView = '';

        //if($bgType=="image" || $bgType=="streamvimeo" || $bgType=="streamyoutube" || $bgType=="streaminstagram" || $bgType=="html5") {
        $data_urlImageForView = $thumb;
        $bg_fullstyle = $bgStyle;
        //}

        if ($bgType == "solid") {
            if ($thumb_on == 'off') {
                $bg_fullstyle = 'background-color:' . $bgColor . ';';
                $data_urlImageForView = '';
            } else {
                $bg_fullstyle = 'background-size: cover;';
            }
        }

        if ($bgType == "trans" || $bgType == "transparent") {
            $data_urlImageForView = '';
            $bg_extraClass = 'mini-transparent';
            $bg_fullstyle = 'background-size: inherit; background-repeat: repeat;';
        }
        return array(
            'url'   => $data_urlImageForView,
            'class' => $bg_extraClass,
            'style' => $bg_fullstyle
        );
//        return apply_filters('revslider_slide_get_image_attributes', array(
//            'url' => $data_urlImageForView,
//            'class' => $bg_extraClass,
//            'style' => $bg_fullstyle
//        ), $this);

    }

    public function getStaticSlideID($sliderID)
    {

        RevSliderFunctions::validateNumeric($sliderID, "Slider ID");
        $record = RevSliderDB::instance(array(
            'table' => RevSliderGlobals::$table_static_slides,
            'where' => array('slider_id', $sliderID)
        ))->first();

        if (empty($record)) {
            return false;
        } else {
            return $record['id'];
        }
    }

    public function getParams()
    {
        $this->validateInited();
        return $this->params;
//        return apply_filters('revslider_slide_getParams', $this->params, $this);
    }

    public function getLayers()
    {
        $this->validateInited();
        //return apply_filters('revslider_getLayers', $this->arrLayers, $this);
        return $this->arrLayers;
    }

    private function validateInited()
    {
        if (empty($this->id))
            RevSliderFunctions::throwError("The slide is not initialized!!!");
    }

    public function createSlide($sliderID, $obj = "", $static = false)
    {

        $imageID = null;

        if (is_array($obj)) {
            $urlImage = RevSliderFunctions::getVal($obj, "url");
            $imageID = RevSliderFunctions::getVal($obj, "id");
        } else {
            $urlImage = $obj;
        }

        //get max order
        $slider = new Slider();
        $slider->initByID($sliderID);
        $maxOrder = $slider->getMaxOrder();
        $order = $maxOrder + 1;

        $params = array();
        if (!empty($urlImage)) {
            $params["background_type"] = "image";
            $params["image"] = $urlImage;
            if (!empty($imageID))
                $params["image_id"] = $imageID;

        } else {    //create transparent slide

            $params["background_type"] = "trans";
        }

        $jsonParams = json_encode($params);


        $arrInsert = array(
            "params"    => $jsonParams,
            "slider_id" => $sliderID,
            "layers"    => "",
            "settings"  => ""
        );

        if (!$static)
            $arrInsert["slide_order"] = $order;

        //$arrInsert = apply_filters('revslider_slide_createSlide', $arrInsert, $sliderID, $static, $this);
        $table = ($static) ? RevSliderGlobals::$table_static_slides : RevSliderGlobals::$table_slides;
        $slideID = RevSliderDB::instance(array(
            'table' => $table
        ))->insert($arrInsert);

        return $slideID;
    }

    public function deleteSlideFromData($data)
    {

        $sliderID = RevSliderFunctions::getVal($data, "sliderID");
        $slider = new Slider();
        $slider->initByID($sliderID);

        //delete slide
        $slideID = RevSliderFunctions::getVal($data, "slideID");
        $this->initByID($slideID);
        $this->deleteChildren();
        $this->deleteSlide();

    }
    public function replaceImageUrls($urlFrom, $urlTo, $static = false){
        $this->validateInited();

        $isUpdated = false;

        $check = array('image', 'image_url', 'background_image', 'slide_thumb', 'show_alternate_image');

        if(isset($this->params['background_type']) && $this->params['background_type'] == 'html5'){
            $check[] = 'slide_bg_html_mpeg';
            $check[] = 'slide_bg_html_webm';
            $check[] = 'slide_bg_html_ogv';
        }

        foreach($check as $param){
            $urlImage = RevSliderFunctions::getVal($this->params, $param, '');
            if(strpos($urlImage, $urlFrom) !== false){
                $imageNew = str_replace($urlFrom, $urlTo, $urlImage);
                $this->params[$param] = $imageNew;
                $isUpdated = true;
            }
        }

        if($isUpdated == true){
            $this->updateParamsInDB(array(), $static);
        }


        // update image url in layers
        $isUpdated = false;
        foreach($this->arrLayers as $key=>$layer){
            $type =  RevSliderFunctions::getVal($layer, "type");

            $urlImage = RevSliderFunctions::getVal($layer, "image_url");
            if(strpos($urlImage, $urlFrom) !== false){
                $newUrlImage = str_replace($urlFrom, $urlTo, $urlImage);
                $this->arrLayers[$key]["image_url"] = $newUrlImage;
                $isUpdated = true;
            }

            if(isset($type) && ($type == 'video' || $type == 'audio')){
                $video_data = (isset($layer['video_data'])) ? (array) $layer['video_data'] : array();

                $check = array();

                if(!empty($video_data) && isset($video_data['video_type']) && $video_data['video_type'] == 'html5'){
                    $check[] = 'urlPoster';
                    $check[] = 'urlMp4';
                    $check[] = 'urlWebm';
                    $check[] = 'urlOgv';
                }elseif(!empty($video_data) && isset($video_data['video_type']) && $video_data['video_type'] != 'html5'){ //video cover image
                    if($video_data['video_type'] == 'audio'){
                        $check[] = 'urlAudio';
                    }else{
                        $check[] = 'previewimage';
                    }
                }

                if(!empty($check)){
                    foreach($check as $param){
                        $url = RevSliderFunctions::getVal($video_data, $param);
                        if(strpos($url, $urlFrom) !== false){
                            $newUrl = str_replace($urlFrom, $urlTo, $url);
                            $video_data[$param] = $newUrl;
                            $isUpdated = true;
                        }
                    }
                }

                $this->arrLayers[$key]['video_data'] = $video_data;
            }elseif(isset($type) && $type == 'svg'){
                $svg_val = RevSliderFunctions::getVal($layer, 'svg', false);
                if (!empty($svg_val) && sizeof($svg_val)>0) {
                    $svg_val->{'src'} = str_replace($urlFrom, $urlTo, $svg_val->{'src'});

                    $this->arrLayers[$key]['svg'] = $svg_val;
                    $isUpdated = true;
                }
            }

            if(isset($layer['layer_action'])){
                if(isset($layer['layer_action']->image_link) && !empty($layer['layer_action']->image_link)){
                    $layer['layer_action']->image_link = (array)$layer['layer_action']->image_link;
                    foreach($layer['layer_action']->image_link as $jtsk => $jtsval){
                        if(strpos($jtsval, $urlFrom) !== false){
                            $this->arrLayers[$key]['layer_action']->image_link[$jtsk] = str_replace($urlFrom, $urlTo, $jtsval);
                            $isUpdated = true;
                        }
                    }
                }
            }

        }

        if($isUpdated == true){
            $this->updateLayersInDB(null, $static);
        }

//        do_action('revslider_slide_replaceImageUrls', $this);
    }
    protected function updateLayersInDB($arrLayers = null, $static = false){
        $this->validateInited();

        if($arrLayers === null)
            $arrLayers = $this->arrLayers;


//        $arrLayers = apply_filters('revslider_slide_updateLayersInDB', $arrLayers, $this);

        $jsonLayers = json_encode($arrLayers);
        $arrDBUpdate = array("layers"=>$jsonLayers);
        $table = ($static === false) ? RevSliderGlobals::$table_slides : RevSliderGlobals::$table_static_slides;

        if($static === false){
            RevSliderDB::instance(array(
                'table'=>RevSliderGlobals::$table_slides,
                'where'=>array('id',$this->id)
            ))->update($arrDBUpdate);
        }else{
            RevSliderDB::instance(array(
                'table'=>RevSliderGlobals::$table_static_slides,
                'where'=>array('id',$static)
            ))->update($arrDBUpdate);
        }
    }
    public function initByID($slideid)
    {
        try {
            if (strpos($slideid, 'static_') !== false) {
                $this->static_slide = true;
                $sliderID = str_replace('static_', '', $slideid);

                RevSliderFunctions::validateNumeric($sliderID, "Slider ID");


                $record = RevSliderDB::instance(array(
                    'table' => RevSliderGlobals::$table_static_slides,
                    'where' => array('slider_id', $sliderID)
                ))->first();

                if (empty($record)) {
                    try {
                        //create a new static slide for the Slider and then use it
                        $slide_id = $this->createSlide($sliderID, "", true);
                        $record = RevSliderDB::instance(array(
                            'table' => RevSliderGlobals::$table_static_slides,
                            'where' => array('slider_id', $sliderID)
                        ))->first();
                        $this->initByData($record);
                    } catch (\Exception $e) {
                    }
                } else {
                    $this->initByData($record);
                }
            } else {
                RevSliderFunctions::validateNumeric($slideid, "Slide ID");

                $record = RevSliderDB::instance(array(
                    'table' => RevSliderGlobals::$table_slides,
                    'where' => array('id', $slideid)
                ))->first();

                $this->initByData($record);
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();
            echo $message;
            exit;
        }
    }
    public function replaceCssClass($css_from, $css_to){

        $this->validateInited();


        $isUpdated = false;

        if(!empty($this->arrLayers)){
            foreach($this->arrLayers as $key=>$layer){
                $caption = RevSliderFunctions::getVal($layer, 'style');
                if($caption == $css_from){
                    $this->arrLayers[$key]['style'] = $css_to;
                    $isUpdated = true;
                }
            }
        }

        if($isUpdated == true)
            $this->updateLayersInDB();

//        do_action('revslider_slide_replaceCssClass', $css_from, $css_to, $this);
    }
    public function deleteChildren()
    {
        $this->validateInited();
        $arrChildren = $this->getArrChildren();
        foreach ($arrChildren as $child)
            $child->deleteSlide();
    }
    public function updateSlideFromData($data){

        $slideID = RevSliderFunctions::getVal($data, "slideid");
        $this->initByID($slideID);

        //treat params
        $params = RevSliderFunctions::getVal($data, "params");
        $params = $this->normalizeParams($params);

        //preserve old data that not included in the given data
        $params = array_merge($this->params,$params);

        //treat layers
        $layers = RevSliderFunctions::getVal($data, "layers");

        if(gettype($layers) == "string"){
            $layersStrip = stripslashes($layers);
            $layersDecoded = json_decode($layersStrip);
            if(empty($layersDecoded))
                $layersDecoded = json_decode($layers);

            $layers = RevSliderFunctions::convertStdClassToArray($layersDecoded);
        }

        if(empty($layers) || gettype($layers) != "array")
            $layers = array();

        $layers = $this->normalizeLayers($layers);


        $settings = RevSliderFunctions::getVal($data, "settings");

        $arrUpdate = array();
        $arrUpdate["layers"] = json_encode($layers);
        $arrUpdate["params"] = json_encode($params);
        $arrUpdate["settings"] = json_encode($settings);

//        $arrUpdate = apply_filters('revslider_slide_updateSlideFromData_pre', $arrUpdate, $data, $this);
        RevSliderDB::instance(array(
            'table'=>RevSliderGlobals::$table_slides,
            'where'=>array('id',$this->id)
        ))->update($arrUpdate);

//        do_action('revslider_slide_updateSlideFromData_post', $arrUpdate, $data, $this);
        //RevSliderOperations::updateDynamicCaptions();
    }
    public function updateStaticSlideFromData($data){

        $slideID = RevSliderFunctions::getVal($data, "slideid");
        $this->initByStaticID($slideID);

        $params = RevSliderFunctions::getVal($data, "params");
        $params = $this->normalizeParams($params);

        //treat layers
        $layers = RevSliderFunctions::getVal($data, "layers");


        if(gettype($layers) == "string"){
            $layersStrip = stripslashes($layers);
            $layersDecoded = json_decode($layersStrip);
            if(empty($layersDecoded))
                $layersDecoded = json_decode($layers);

            $layers = RevSliderFunctions::convertStdClassToArray($layersDecoded);
        }

        if(empty($layers) || gettype($layers) != "array")
            $layers = array();

        $layers = $this->normalizeLayers($layers);

        $settings = RevSliderFunctions::getVal($data, "settings");


        $arrUpdate = array();
        $arrUpdate["layers"] = json_encode($layers);
        $arrUpdate["params"] = json_encode($params);
        $arrUpdate["settings"] = json_encode($settings);

//        $arrUpdate = apply_filters('revslider_slide_updateStaticSlideFromData', $arrUpdate, $data, $this);
        RevSliderDB::instance(array(
            'table'=>RevSliderGlobals::$table_static_slides,
            'where'=>array('id',$this->id)
        ))->update($arrUpdate);
//        $this->db->update(RevSliderGlobals::$table_static_slides,$arrUpdate,array("id"=>$this->id));

//        do_action('revslider_slide_updateStaticSlideFromData_post', $arrUpdate, $data, $this);
        //RevSliderOperations::updateDynamicCaptions();
    }
    public function isStaticSlide(){
        return $this->static_slide;
    }
    public function getArrChildren()
    {
        $this->validateInited();

        if ($this->arrChildren === null) {
            $slider = new Slider();
            $slider->initByID($this->sliderID);
            $this->arrChildren = $slider->getArrSlideChildren($this->id);
        }

//        return apply_filters('revslider_slide_getArrChildren', $this->arrChildren, $this);
        return $this->arrChildren;
    }

    function getParam($name, $default = null)
    {

        if ($default === null) {
            //if(!array_key_exists($name, $this->params))
            $default = '';
        }

        return RevSliderFunctions::getVal($this->params, $name, $default);
    }

    public function setArrChildren($arrChildren)
    {
        $this->arrChildren = $arrChildren;
    }
    public function getArrChildrenIDs(){
        $arrChildren = $this->getArrChildren();
        $arrChildrenIDs = array();
        foreach($arrChildren as $child){
            $childID = $child->getID();
            $arrChildrenIDs[] = $childID;
        }

        return($arrChildrenIDs);
    }
    public function deleteSlide()
    {
        $this->validateInited();
        RevSliderDB::instance(array(
            'table' => RevSliderGlobals::$table_slides,
            'where' => array('id', $this->id)
        ))->delete();

        // do_action('revslider_slide_deleteSlide', $this->id);
    }

    public function getOrder()
    {
        $this->validateInited();
        return $this->slideOrder;
    }

    public function updateParentSlideID($parentID)
    {
        $arrUpdate = array();
        $arrUpdate["parentid"] = $parentID;
        $this->updateParamsInDB($arrUpdate);
    }

    protected function updateParamsInDB($arrUpdate = array(), $static = false)
    {
        $this->validateInited();

//        $this->params = apply_filters('revslider_slide_updateParamsInDB', array_merge($this->params,$arrUpdate), $this);
        $this->params = array_merge($this->params, $arrUpdate);

        $jsonParams = json_encode($this->params);

        $arrDBUpdate = array("params" => $jsonParams);
        if ($static === false) {
            RevSliderDB::instance(array(
                'table' => RevSliderGlobals::$table_slides,
                'where' => array('id', $this->id)
            ))->update($arrDBUpdate);
        } else {
            RevSliderDB::instance(array(
                'table' => RevSliderGlobals::$table_static_slides,
                'where' => array('id', $static)
            ))->update($arrDBUpdate);
        }
    }

    public function updateTitleByID($data)
    {
        if (!isset($data['slideID']) || !isset($data['slideTitle'])) return false;

        $this->initByID($data['slideID']);

        $arrUpdate = array();
        $arrUpdate['title'] = $data['slideTitle'];

//        $arrUpdate = apply_filters('revslider_slide_updateTitleByID', $arrUpdate, $data, $this);

        $this->updateParamsInDB($arrUpdate);

    }

    public function updateSlideImageFromData($data)
    {

        $sliderID = RevSliderFunctions::getVal($data, "slider_id");
        $slider = new Slider();
        $slider->initByID($sliderID);

        $slideID = RevSliderFunctions::getVal($data, "slide_id");
        $urlImage = RevSliderFunctions::getVal($data, "url_image");
        RevSliderFunctions::validateNotEmpty($urlImage);
        $imageID = RevSliderFunctions::getVal($data, "image_id");
        if ($slider->isSlidesFromPosts()) {

//            if(!empty($imageID))
//                RevSliderFunctionsWP::updatePostThumbnail($slideID, $imageID);

        } elseif ($slider->isSlidesFromStream() !== false) {
            //do nothing
        } else {
            $this->initByID($slideID);

            $arrUpdate = array();
            $arrUpdate["image"] = $urlImage;
            $arrUpdate["image_id"] = $imageID;

            $this->updateParamsInDB($arrUpdate);
        }

        return $urlImage;
    }

    public function getSliderID()
    {
        return ($this->sliderID);
    }

    public function setParams($params)
    {
        $params = $this->normalizeParams($params);
        $this->params = $params;
    }

    private function normalizeParams($params)
    {

        $urlImage = RevSliderFunctions::getVal($params, "image_url");

        //init the id if absent
        $params["image_id"] = RevSliderFunctions::getVal($params, "image_id");

        $params["image"] = $urlImage;
        unset($params["image_url"]);

        if (isset($params["video_description"]))
            $params["video_description"] = RevSliderFunctions::normalizeTextareaContent($params["video_description"]);

        return $params;
    }

    public function setLayers($layers)
    {
        $layers = $this->normalizeLayers($layers);
        $this->arrLayers = $layers;
    }

    public function getLang()
    {
        $lang = $this->getParam("lang", "all");
        return ($lang);
    }
    private function sortLayersByOrder($layer1,$layer2){
        $layer1 = (array)$layer1;
        $layer2 = (array)$layer2;

        $order1 = RevSliderFunctions::getVal($layer1, "order",1);
        $order2 = RevSliderFunctions::getVal($layer2, "order",2);
        if($order1 == $order2)
            return(0);

        return($order1 > $order2);
    }
    private function normalizeLayers($arrLayers)
    {

        usort($arrLayers, array($this, "sortLayersByOrder"));

        $arrLayersNew = array();
        foreach ($arrLayers as $key => $layer) {

            $layer = (array)$layer;

            //set type
            $type = RevSliderFunctions::getVal($layer, "type", "text");
            $layer["type"] = $type;

            //normalize position:
            if (is_object($layer["left"])) {
                foreach ($layer["left"] as $key1 => $val) {
                    $layer["left"]->$key1 = round($val);
                }
            } else {
                $layer["left"] = round($layer["left"]);
            }
            if (is_object($layer["top"])) {
                foreach ($layer["top"] as $key1 => $val) {
                    $layer["top"]->$key1 = round($val);
                }
            } else {
                $layer["top"] = round($layer["top"]);
            }

            //unset order
            unset($layer["order"]);

            //modify text
            $layer["text"] = stripcslashes($layer["text"]);

            $arrLayersNew[] = $layer;
        }

        return $arrLayersNew;
    }

    public function setImageByID($imageID, $size = 'full')
    {
//        $a = apply_filters('revslider_slide_setImageByID', array('imageID' => $imageID, 'size' => $size), $this);
        $a = array('imageID' => $imageID, 'size' => $size);

        $imageUrl = RevSliderFunctionsWP::getUrlAttachmentImage($a['imageID'], $a['size']);
        if (!empty($imageUrl)) {
            $this->imageID = $a['imageID'];
            $this->imageUrl = $imageUrl;
            $this->imageThumb = RevSliderFunctionsWP::getUrlAttachmentImage($a['imageID'], RevSliderFunctionsWP::THUMB_MEDIUM);
            $this->imageFilename = basename($this->imageUrl);
            $this->imageFilepath = RevSliderFunctionsWP::getImagePathFromURL($this->imageUrl);
            $realPath = RevSliderFunctionsWP::getPathContent($this->imageFilepath);

            if (RevSliderFile::fileExists($realPath) == false)
                $this->imageFilepath = "";

            return true;
        }
        return false;
    }

    public function setBackgroundType($new_param)
    {
//        $new_param = apply_filters('revslider_slide_setBackgroundType', $new_param, $this);

        $this->params['background_type'] = $new_param;
    }

    public function setParam($name, $value)
    {

        $this->params[$name] = $value;

    }

    public function getParentSlideID()
    {
        $parentID = $this->getParam("parentid", "");

        return $parentID;
    }

    public function initByStaticID($slideid)
    {

        RevSliderFunctions::validateNumeric($slideid, "Slide ID");

        $record = RevSliderDB::instance(array(
            'table' => RevSliderGlobals::$table_static_slides,
            'where' => array('id', $slideid)
        ))->first();

        $this->initByData($record);
    }

    public function getImageUrl()
    {
        return ($this->imageUrl);
    }

    public function getImageID()
    {
        return ($this->imageID);
    }

    public function getImageFilename()
    {
        return ($this->imageFilename);
    }
}