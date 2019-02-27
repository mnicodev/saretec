<?php
/**
 * Created by FsFlex.
 * User: VH
 * Date: 6/26/2017
 * Time: 8:57 AM
 */

namespace Drupal\revslider\Controller;


use Drupal\Core\Access\AccessResult;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Session\AccountInterface;
use Drupal\revslider\Helper\RevSliderCssParser;
use Drupal\revslider\Helper\RevSliderDB;
use Drupal\revslider\Helper\RevSliderFunctions;
use Drupal\revslider\Helper\RevSliderGlobals;
use Drupal\revslider\Helper\RevSliderMedia;
use Drupal\revslider\Helper\RevSliderOptions;
use Drupal\revslider\Helper\TPColorpicker;
use Drupal\revslider\Model\Navigation;
use Drupal\revslider\Model\ObjectLibrary;
use Drupal\revslider\Model\Operations;
use Drupal\revslider\Model\Slide;
use Drupal\revslider\Model\Slider;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RevSliderAjax extends RevSliderControllerBase
{

    public function request()
    {
        $route = RevSliderFunctions::getRequestVariable('route');
        switch ($route)
        {
            case 'media':
                $result = $this->ajaxMediaHandle();
                break;
            case 'show_image':
                $result = $this->showImageHandle();
                break;
            default:
                $result = $this->onAjaxAction();
                break;
        }
        return $result;
    }
    protected function showImageHandle()
    {
        $id = RevSliderFunctions::getRequestVariable('img');
        if(empty($id))
            die('fail request');
        $url = RevSliderFunctions::get_image_url_by_id($id);
        $redirect = new RedirectResponse($url);
        $redirect->send();
        return;
    }
    protected function ajaxMediaHandle()
    {
        $action = RevSliderFunctions::getRequestVariable('action');
        switch ($action)
        {
            case 'delete_media':
                $fid = RevSliderFunctions::getRequestVariable('fid');
                RevSliderMedia::removeFile($fid);
                break;
            case 'upload_media':
                RevSliderMedia::uploadMedia();
        }
        $result = RevSliderMedia::getMedia();
        return AjaxResponse::create($result);
    }

    protected function onAjaxAction()
    {

        $role = RevSliderFunctions::getMenuRole();//self::getMenuRole(); //add additional security check and allow for example import only for admin

        $slider = new Slider();
        $slide = new Slide();
        $operations = new Operations();

        $action = RevSliderFunctions::getPostGetVar("client_action");
        $data = RevSliderFunctions::getPostGetVar("data");
        if ($data == '') $data = array();
        $nonce = RevSliderFunctions::getPostGetVar("nonce");
        if (empty($nonce))
            $nonce = RevSliderFunctions::getPostGetVar("rs-nonce");

        $demo_actions = array(
            'import_slider_online_template_slidersview',
            'duplicate_slider',
            'preview_slider',
            'get_static_css',
            'get_dynamic_css',
            'preview_slide'
        );//these are all okay in demo mode
        $admin_actions = array(
            'change_specific_navigation',
            'change_navigations',
            'update_static_css',
            'add_new_preset',
            'update_preset',
            'import_slider',
            'import_slider_slidersview',
            'import_slider_template_slidersview',
            'import_slide_template_slidersview',
            'import_slider_online_template_slidersview_new',
            'fix_database_issues'
        );
     //   try {
            if (RevSliderFunctions::isRS_DEMO() && !in_array($action, $demo_actions)) {
                RevSliderFunctions::throwError(t('Function Not Available in Demo Mode'));
                exit();
            }
            if (!RevSliderFunctions::isAdminUser() && in_array($action,$admin_actions)) {
                RevSliderFunctions::throwError(t('Function Only Available for Adminstrators'));
                exit();
            }

            switch ($action) {
                case 'add_new_preset':

                    $result = $operations->add_preset_setting($data);

                    if ($result === true) {

                        $presets = $operations->get_preset_settings();

                        self::ajaxResponseSuccess(t('Preset created'), array('data' => $presets));
                    } else {
                        self::ajaxResponseError($result, false);
                    }

                    exit;
                    break;
                case 'update_preset':
                    $result = $operations->update_preset_setting($data);

                    if ($result === true) {

                        $presets = $operations->get_preset_settings();

                        self::ajaxResponseSuccess(t('Preset created'), array('data' => $presets));
                    } else {
                        self::ajaxResponseError($result, false);
                    }

                    exit;
                    break;
                case 'remove_preset':

                    $result = $operations->remove_preset_setting($data);

                    if ($result === true) {

                        $presets = $operations->get_preset_settings();

                        self::ajaxResponseSuccess(t('Preset deleted'), array('data' => $presets));
                    } else {
                        self::ajaxResponseError($result, false);
                    }

                    exit;
                    break;
                case "export_slider":
                    $sliderID = RevSliderFunctions::getRequestVariable("sliderid");
                    $dummy = RevSliderFunctions::getRequestVariable("dummy");
                    $slider->initByID($sliderID);
                    $slider->exportSlider($dummy);
                    break;
                case "import_slider":
                    $updateAnim = RevSliderFunctions::getPostGetVar("update_animations");
                    $updateNav = RevSliderFunctions::getPostGetVar("update_navigations");
                    //$updateStatic = self::getPostGetVar("update_static_captions");
                    $updateStatic = 'none';
                    self::importSliderHandle(null, $updateAnim, $updateStatic, $updateNav);
                    break;
                case "import_slider_slidersview":
                    $viewBack = '';//self::getViewUrl(self::VIEW_SLIDERS);
                    $updateAnim = RevSliderFunctions::getPostGetVar("update_animations");
                    $updateNav = RevSliderFunctions::getPostGetVar("update_navigations");
                    //$updateStatic = self::getPostGetVar("update_static_captions");
                    $updateStatic = 'none';
                    self::importSliderHandle($viewBack, $updateAnim, $updateStatic, $updateNav);
                    break;
                case "import_slider_online_template_slidersview":
                    self::ajaxResponseError(t('Nothing to do'));
                    $viewBack = self::getViewUrl(self::VIEW_SLIDERS);
                    //ob_start();
                    $data['uid'] = esc_attr(RevSliderFunctions::getPostVariable('uid'));
                    $data['page-creation'] = esc_attr(RevSliderFunctions::getPostVariable('page-creation'));
                    $data['package'] = esc_attr(RevSliderFunctions::getPostVariable('package'));

                    self::importSliderOnlineTemplateHandle($data, $viewBack, 'true', 'none');
                    /*$html = ob_get_contents();
                    ob_clean();
                    ob_end_clean();

                    self::ajaxResponseData($html);*/
                    break;
                case "import_slider_template_slidersview":
                    self::ajaxResponseError(t('Nothing to do'));
                    $viewBack = self::getViewUrl(self::VIEW_SLIDERS);
                    $updateAnim = self::getPostGetVar("update_animations");
                    //$updateStatic = self::getPostGetVar("update_static_captions");
                    $updateStatic = 'none';
                    self::importSliderTemplateHandle($viewBack, $updateAnim, $updateStatic);
                    break;
                case "import_slider_online_template_slidersview_new":
                    self::ajaxResponseError(t('Nothing to do'));
                    $viewBack = self::getViewUrl(self::VIEW_SLIDERS);
                    $response = self::importSliderOnlineTemplateHandleNew($data, $viewBack, 'true', 'none');
                    self::ajaxResponseData($response);
                    break;
                case 'create_draft_page':
                    self::ajaxResponseError(t('Nothing to do'));
                    $response = array('open' => false);

                    $page_id = $operations->create_slider_page($data['slider_ids']);
                    if ($page_id > 0) {
                        $response['open'] = get_permalink($page_id);
                    }
                    self::ajaxResponseData($response);
                    break;
                case "import_slide_online_template_slidersview":
                    self::ajaxResponseError(t('Nothing to do'));
                    $redirect_id = esc_attr(self::getPostGetVar("redirect_id"));
                    $viewBack = self::getViewUrl(self::VIEW_SLIDE, "id=$redirect_id");
                    $slidenum = intval(self::getPostGetVar("slidenum"));
                    $sliderid = intval(self::getPostGetVar("slider_id"));

                    $data['uid'] = esc_attr(RevSliderFunctions::getPostVariable('uid'));
                    $data['page-creation'] = esc_attr(RevSliderFunctions::getPostVariable('page-creation'));
                    $data['package'] = esc_attr(RevSliderFunctions::getPostVariable('package'));

                    self::importSliderOnlineTemplateHandle($data, $viewBack, 'true', 'none', array('slider_id' => $sliderid, 'slide_id' => $slidenum));
                    break;
                case "import_slide_template_slidersview":
                    self::ajaxResponseError(t('Nothing to do'));
                    $redirect_id = esc_attr(self::getPostGetVar("redirect_id"));
                    $viewBack = self::getViewUrl(self::VIEW_SLIDE, "id=$redirect_id");
                    $updateAnim = self::getPostGetVar("update_animations");
                    //$updateStatic = self::getPostGetVar("update_static_captions");
                    $updateStatic = 'none';
                    $slidenum = intval(self::getPostGetVar("slidenum"));
                    $sliderid = intval(self::getPostGetVar("slider_id"));

                    self::importSliderTemplateHandle($viewBack, $updateAnim, $updateStatic, array('slider_id' => $sliderid, 'slide_id' => $slidenum));
                    break;
                case "create_slider":
                    $data = $operations->modifyCustomSliderParams($data);
                    $newSliderID = $slider->createSliderFromOptions($data);
                    self::ajaxResponseSuccessRedirect(t("Slider created"),
                        RevSliderFunctions::getViewUrl(RevSliderGlobals::VIEW_SLIDE,array('id'=>"new",'slider'=>$newSliderID))); //redirect to slide now
                    break;
                case "update_slider":
                    $data = $operations->modifyCustomSliderParams($data);
                    $slider->updateSliderFromOptions($data);
                    self::ajaxResponseSuccess(t("Slider updated"));
                    break;
                case "delete_slider":
                case "delete_slider_stay":

                    $isDeleted = $slider->deleteSliderFromData($data);

                    if (is_array($isDeleted)) {
                        $isDeleted = implode(', ', $isDeleted);
                        self::ajaxResponseError(t("Template can't be deleted, it is still being used by the following Sliders: ") . $isDeleted);
                    } else {
                        if ($action == 'delete_slider_stay') {
                            self::ajaxResponseSuccess(t("Slider deleted"));
                        } else {
                            //self::ajaxResponseSuccessRedirect(t("Slider deleted"), self::getViewUrl(self::VIEW_SLIDERS));
                            self::ajaxResponseSuccessRedirect(t("Slider deleted"),'');
                        }
                    }
                    break;
                case "duplicate_slider":

                    $slider->duplicateSliderFromData($data);

                    self::ajaxResponseSuccessRedirect(t("Success! Refreshing page..."), RevSliderFunctions::getViewUrl(RevSliderGlobals::VIEW_SLIDERS));
                    break;
                case "duplicate_slider_package":
                    self::ajaxResponseError(t('Nothing to do'));
                    $ret = $slider->duplicateSliderPackageFromData($data);

                    if ($ret !== true) {
                        RevSliderFunctions::throwError($ret);
                    } else {
                        self::ajaxResponseSuccessRedirect(__("Success! Refreshing page...", 'revslider'), self::getViewUrl(self::VIEW_SLIDERS));
                    }
                    break;
                case "add_slide":
                case "add_bulk_slide":
                    $numSlides = $slider->createSlideFromData($data);
                    $sliderID = $data["sliderid"];

                    if ($numSlides == 1) {
                        $responseText = t("Slide Created");
                    } else {
                        $responseText = $numSlides . " " . t("Slides Created");
                    }

                    $urlRedirect = RevSliderFunctions::getViewUrl(RevSliderGlobals::VIEW_SLIDE,array('id'=>'new','slider'=>$sliderID));
                    self::ajaxResponseSuccessRedirect($responseText, $urlRedirect);

                    break;
                case "add_slide_fromslideview":
                    $slideID = $slider->createSlideFromData($data, true);
                    $urlRedirect = RevSliderFunctions::getViewUrl(RevSliderGlobals::VIEW_SLIDE,array('id'=>$slideID));
                    $responseText = t("Slide Created, redirecting...");
                    self::ajaxResponseSuccessRedirect($responseText, $urlRedirect);
                    break;
                case 'copy_slide_to_slider':
                    $slideID = (isset($data['redirect_id'])) ? $data['redirect_id'] : -1;

                    if ($slideID === -1) RevSliderFunctions::throwError(t('Missing redirect ID!'));

                    $return = $slider->copySlideToSlider($data);

                    if ($return !== true) RevSliderFunctions::throwError($return);

                    $urlRedirect = RevSliderFunctions::getViewUrl(RevSliderGlobals::VIEW_SLIDE,array('id'=>$slideID));
                    $responseText = t("Slide copied to current Slider, redirecting...");
                    self::ajaxResponseSuccessRedirect($responseText, $urlRedirect);
                    break;
                case 'update_slide':
                    if (isset($data['obj_favorites'])) {
                        $obj_favorites = $data['obj_favorites'];
                        unset($data['obj_favorites']);
                        //save object favourites
                        $objlib = new ObjectLibrary();
                        $objlib->save_favorites($obj_favorites);
                    }
                    $slide->updateSlideFromData($data);
                    self::ajaxResponseSuccess(t("Slide updated"));
                    break;
                case "update_static_slide":
                    if (isset($data['obj_favorites'])) {
                        $obj_favorites = $data['obj_favorites'];
                        unset($data['obj_favorites']);
                        //save object favourites
                        $objlib = new ObjectLibrary();
                        $objlib->save_favorites($obj_favorites);
                    }
                    $slide->updateStaticSlideFromData($data);
                    self::ajaxResponseSuccess(t("Static Global Layers updated"));
                    break;
                case "delete_slide":
                case "delete_slide_stay":
                    $isPost = $slide->deleteSlideFromData($data);
                    if ($isPost)
                        $message = t("Post deleted");
                    else
                        $message = t("Slide deleted");

                    $sliderID = RevSliderFunctions::getVal($data, "sliderID");
                    if ($action == 'delete_slide_stay') {
                        self::ajaxResponseSuccess($message);
                    } else {
                        self::ajaxResponseSuccessRedirect($message, RevSliderFunctions::getViewUrl(RevSliderGlobals::VIEW_SLIDE,array('id'=>'new','slider'=>$sliderID)));
                    }
                    break;
                case "duplicate_slide":
                case "duplicate_slide_stay":
                    $return = $slider->duplicateSlideFromData($data);
                    if ($action == 'duplicate_slide_stay') {
                        self::ajaxResponseSuccess(t("Slide duplicated"), array('id' => $return[1]));
                    } else {
                        self::ajaxResponseSuccessRedirect(t("Slide duplicated"),
                            RevSliderFunctions::getViewUrl(RevSliderGlobals::VIEW_SLIDE,array('id'=>'new','slider'=>$return[0])));
                    }
                    break;
                case "copy_move_slide":
                case "copy_move_slide_stay":
                    $sliderID = $slider->copyMoveSlideFromData($data);
                    if ($action == 'copy_move_slide_stay') {
                        self::ajaxResponseSuccess(t("Success!"));
                    } else {
                        self::ajaxResponseSuccessRedirect(t("Success! Refreshing page..."),
                            RevSliderFunctions::getViewUrl(RevSliderGlobals::VIEW_SLIDE,array('id'=>'new','slider'=>$sliderID)));
                    }
                    break;
                case "add_slide_to_template":
                    self::ajaxResponseError(t('Nothing to do'));
                    $template = new RevSliderTemplate();
                    if (!isset($data['slideID']) || intval($data['slideID']) == 0) {
                        RevSliderFunctions::throwError(__('No valid Slide ID given', 'revslider'));
                        exit;
                    }
                    if (!isset($data['title']) || strlen(trim($data['title'])) < 3) {
                        RevSliderFunctions::throwError(__('No valid title given', 'revslider'));
                        exit;
                    }
                    if (!isset($data['settings']) || !isset($data['settings']['width']) || !isset($data['settings']['height'])) {
                        RevSliderFunctions::throwError(__('No valid title given', 'revslider'));
                        exit;
                    }

                    $return = $template->copySlideToTemplates($data['slideID'], $data['title'], $data['settings']);

                    if ($return == false) {
                        RevSliderFunctions::throwError(__('Could not save Slide as Template', 'revslider'));
                        exit;
                    }

                    //get HTML for template section
                    ob_start();

                    $rs_disable_template_script = true; //disable the script output of template selector file

                    include(RS_PLUGIN_PATH . 'admin/views/templates/template-selector.php');

                    $html = ob_get_contents();

                    ob_clean();
                    ob_end_clean();

                    self::ajaxResponseSuccess(__('Slide added to Templates', 'revslider'), array('HTML' => $html));
                    exit;
                    break;
                case "get_slider_custom_css_js":
                    $slider_css = '';
                    $slider_js = '';
                    if (isset($data['slider_id']) && intval($data['slider_id']) > 0) {
                        $slider->initByID(intval($data['slider_id']));
                        $slider_css = stripslashes($slider->getParam('custom_css', ''));
                        $slider_js = stripslashes($slider->getParam('custom_javascript', ''));
                    }
                    self::ajaxResponseData(array('css' => $slider_css, 'js' => $slider_js));
                    break;
                case "update_slider_custom_css_js":
                    if (isset($data['slider_id']) && intval($data['slider_id']) > 0) {
                        $slider->initByID(intval($data['slider_id']));
                        $slider->updateParam(array('custom_css' => $data['css']));
                        $slider->updateParam(array('custom_javascript' => $data['js']));
                    }
                    self::ajaxResponseSuccess(t('Slider CSS saved'));
                    exit;
                    break;
                case "get_static_css":
                    $contentCSS = $operations->getStaticCss();
                    self::ajaxResponseData($contentCSS);
                    break;
                case "get_dynamic_css":
                    $contentCSS = $operations->getDynamicCss();
                    self::ajaxResponseData($contentCSS);
                    break;
                case "insert_captions_css":

                    $arrCaptions = $operations->insertCaptionsContentData($data);

                    if ($arrCaptions !== false) {
                        $styles = RevSliderDB::instance(array(
                            'table'=>RevSliderGlobals::$table_css
                        ))->get();
                        $styles = RevSliderCssParser::parseDbArrayToCss($styles, "\n");
                        $styles = RevSliderCssParser::compress_css($styles);
                        $custom_css = Operations::getStaticCss();
                        $custom_css = RevSliderCssParser::compress_css($custom_css);

                        $arrCSS = $operations->getCaptionsContentArray();
                        //$arrCssStyles = RevSliderFunctions::jsonEncodeForClientSide($arrCSS);
                        $arrCssStyles = $arrCSS;

                        self::ajaxResponseSuccess(t("CSS saved"), array("arrCaptions" => $arrCaptions, 'compressed_css' => $styles . $custom_css, 'initstyles' => $arrCssStyles));
                    }

                    RevSliderFunctions::throwError(t('CSS could not be saved'));
                    exit();
                    break;
                case "update_captions_css":
                    $arrCaptions = $operations->updateCaptionsContentData($data);

                    //now check all layers of all sliders and check if you need to change them (only if all values are default)


                    if ($arrCaptions !== false) {
                        $styles = RevSliderDB::instance(array(
                            'table'=>RevSliderGlobals::$table_css
                        ))->get();
                        $styles = RevSliderCssParser::parseDbArrayToCss($styles, "\n");
                        $styles = RevSliderCssParser::compress_css($styles);
                        $custom_css = Operations::getStaticCss();
                        $custom_css = RevSliderCssParser::compress_css($custom_css);

                        $arrCSS = $operations->getCaptionsContentArray();
                        $arrCssStyles = RevSliderFunctions::jsonEncodeForClientSide($arrCSS);
//                        $arrCssStyles = $arrCSS;

                        self::ajaxResponseSuccess(t("CSS saved"), array("arrCaptions" => $arrCaptions, 'compressed_css' => $styles . $custom_css, 'initstyles' => $arrCssStyles));
                    }

                    RevSliderFunctions::throwError(t('CSS could not be saved'));
                    exit();
                    break;
                case "update_captions_advanced_css":

                    $arrCaptions = $operations->updateAdvancedCssData($data);
                    if ($arrCaptions !== false) {
                        $styles = RevSliderDB::instance(array(
                            'table'=>RevSliderGlobals::$table_css
                        ))->get();
                        $styles = RevSliderCssParser::parseDbArrayToCss($styles, "\n");
                        $styles = RevSliderCssParser::compress_css($styles);
                        $custom_css = Operations::getStaticCss();
                        $custom_css = RevSliderCssParser::compress_css($custom_css);

                        $arrCSS = $operations->getCaptionsContentArray();
                        $arrCssStyles = RevSliderFunctions::jsonEncodeForClientSide($arrCSS);
//                        $arrCssStyles = $arrCSS;

                        self::ajaxResponseSuccess(t("CSS saved"), array("arrCaptions" => $arrCaptions, 'compressed_css' => $styles . $custom_css, 'initstyles' => $arrCssStyles));
                    }

                    RevSliderFunctions::throwError(t('CSS could not be saved'));
                    exit();
                    break;
                case "rename_captions_css":
                    //rename all captions in all sliders with new handle if success
                    $arrCaptions = $operations->renameCaption($data);
                    $styles = RevSliderDB::instance(array(
                        'table'=>RevSliderGlobals::$table_css
                    ))->get();
                    $styles = RevSliderCssParser::parseDbArrayToCss($styles, "\n");
                    $styles = RevSliderCssParser::compress_css($styles);
                    $custom_css = Operations::getStaticCss();
                    $custom_css = RevSliderCssParser::compress_css($custom_css);

                    $arrCSS = $operations->getCaptionsContentArray();
                    $arrCssStyles = RevSliderFunctions::jsonEncodeForClientSide($arrCSS);
//                    $arrCssStyles = $arrCSS;

                    self::ajaxResponseSuccess(t("Class name renamed"), array("arrCaptions" => $arrCaptions, 'compressed_css' => $styles . $custom_css, 'initstyles' => $arrCssStyles));
                    break;
                case "delete_captions_css":
                    $arrCaptions = $operations->deleteCaptionsContentData($data);

                    $styles =RevSliderDB::instance(array(
                        'table'=>RevSliderGlobals::$table_css
                    ))->get();
                    $styles = RevSliderCssParser::parseDbArrayToCss($styles, "\n");
                    $styles = RevSliderCssParser::compress_css($styles);
                    $custom_css = Operations::getStaticCss();
                    $custom_css = RevSliderCssParser::compress_css($custom_css);

                    $arrCSS = $operations->getCaptionsContentArray();
                    $arrCssStyles = RevSliderFunctions::jsonEncodeForClientSide($arrCSS);
//                    $arrCssStyles = $arrCSS;

                    self::ajaxResponseSuccess(t("Style deleted!"), array("arrCaptions" => $arrCaptions, 'compressed_css' => $styles . $custom_css, 'initstyles' => $arrCssStyles));
                    break;
                case "update_static_css":
                    $data = ''; //do not allow to add new global CSS anymore, instead, remove all!
                    $staticCss = $operations->updateStaticCss($data);
                    $styles = RevSliderDB::instance(array(
                        'table'=>RevSliderGlobals::$table_css
                    ))->get();
                    $styles = RevSliderCssParser::parseDbArrayToCss($styles, "\n");
                    $styles = RevSliderCssParser::compress_css($styles);
                    $custom_css = Operations::getStaticCss();
                    $custom_css = RevSliderCssParser::compress_css($custom_css);

                    self::ajaxResponseSuccess(t("CSS saved"), array("css" => $staticCss, 'compressed_css' => $styles . $custom_css));
                    break;
                case "insert_custom_anim":
                    $arrAnims = $operations->insertCustomAnim($data); //$arrCaptions =
                    self::ajaxResponseSuccess(t("Animation saved"), $arrAnims); //,array("arrCaptions"=>$arrCaptions)
                    break;
                case "update_custom_anim":
                    $arrAnims = $operations->updateCustomAnim($data);
                    self::ajaxResponseSuccess(t("Animation saved"), $arrAnims); //,array("arrCaptions"=>$arrCaptions)
                    break;
                case "update_custom_anim_name":
                    $arrAnims = $operations->updateCustomAnimName($data);
                    self::ajaxResponseSuccess(t("Animation saved"), $arrAnims); //,array("arrCaptions"=>$arrCaptions)
                    break;
                case "delete_custom_anim":
                    $arrAnims = $operations->deleteCustomAnim($data);
                    self::ajaxResponseSuccess(t("Animation deleted"), $arrAnims); //,array("arrCaptions"=>$arrCaptions)
                    break;
                case "update_slides_order":
                    $slider->updateSlidesOrderFromData($data);
                    self::ajaxResponseSuccess(t("Order updated"));
                    break;
                case "change_slide_title":
                    $slide->updateTitleByID($data);
                    self::ajaxResponseSuccess(t('Title updated'));
                    break;
                case "change_slide_image":
                    $slide->updateSlideImageFromData($data);
                    $sliderID = RevSliderFunctions::getVal($data, "slider_id");
                    self::ajaxResponseSuccessRedirect(t("Slide changed"),
                        RevSliderFunctions::getViewUrl(RevSliderGlobals::VIEW_SLIDE,array('id'=>'new','slider'=>$sliderID)));
                    break;
                case "preview_slide":
                    $operations->putSlidePreviewByData($data);
                    exit;
                    break;
                case "preview_slider":
                    $sliderID = RevSliderFunctions::getPostGetVar("sliderid");
                    $do_markup = 'false';// RevSliderFunctions::getPostGetVar("only_markup");
                    if ($do_markup == 'true'){
//                        $operations->previewOutputMarkup($sliderID);
                    }
                    else
                        $operations->previewOutput($sliderID);

                    exit;
                    break;
                case "get_import_slides_data":
                    $slides = array();
                    if (!is_array($data)) {
                        $slider->initByID(intval($data));

                        $full_slides = $slider->getSlides(); //static slide is missing

                        if (!empty($full_slides)) {
                            foreach ($full_slides as $slide_id => $mslide) {
                                $slides[$slide_id]['layers'] = $mslide->getLayers();
                                foreach ($slides[$slide_id]['layers'] as $k => $l) { //remove columns as they can not be imported
                                    if (isset($l['type']) && ($l['type'] == 'column' || $l['type'] == 'row' || $l['type'] == 'group')) unset($slides[$slide_id]['layers'][$k]);
                                }
                                $slides[$slide_id]['params'] = $mslide->getParams();
                            }
                        }

                        $staticID = $slide->getStaticSlideID($slider->getID());
                        if ($staticID !== false) {
                            $msl = new Slide();
                            if (strpos($staticID, 'static_') === false) {
                                $staticID = 'static_' . $slider->getID();
                            }
                            $msl->initByID($staticID);
                            if ($msl->getID() !== '') {
                                $slides[$msl->getID()]['layers'] = $msl->getLayers();
                                foreach ($slides[$msl->getID()]['layers'] as $k => $l) { //remove columns as they can not be imported
                                    if (isset($l['type']) && ($l['type'] == 'column' || $l['type'] == 'row' || $l['type'] == 'group')) unset($slides[$msl->getID()]['layers'][$k]);
                                }
                                $slides[$msl->getID()]['params'] = $msl->getParams();
                                $slides[$msl->getID()]['params']['title'] = t('Static Slide');
                            }
                        }
                    }
                    if (!empty($slides)) {
                        self::ajaxResponseData(array('slides' => $slides));
                    } else {
                        self::ajaxResponseData('');
                    }
                    break;
                case "create_navigation_preset":
                    $nav = new Navigation();

                    $return = $nav->add_preset($data);

                    if ($return === true) {
                        self::ajaxResponseSuccess(t('Navigation preset saved/updated'), array('navs' => $nav->get_all_navigations()));
                    } else {
                        if ($return === false) $return = t('Preset could not be saved/values are the same');
                        self::ajaxResponseError($return);
                    }
                    break;
                case "delete_navigation_preset":
                    $nav = new Navigation();

                    $return = $nav->delete_preset($data);

                    if ($return) {
                        self::ajaxResponseSuccess(t('Navigation preset deleted'), array('navs' => $nav->get_all_navigations()));
                    } else {
                        if ($return === false) $return = t('Preset not found');
                        self::ajaxResponseError($return);
                    }
                    break;
                case "toggle_slide_state":
                    $currentState = $slide->toggleSlideStatFromData($data);
                    self::ajaxResponseData(array("state" => $currentState));
                    break;
                case "toggle_hero_slide":
                    $currentHero = $slider->setHeroSlide($data);
                    self::ajaxResponseSuccess(t('Slide is now the new active Hero Slide'));
                    break;
                case "slide_lang_operation":
                    $responseData = 'wpml is not exist';//$slide->doSlideLangOperation($data);
                    self::ajaxResponseData($responseData);
                    break;
                case "update_general_settings":
                    $operations->updateGeneralSettings($data);
                    self::ajaxResponseSuccess(t("General settings updated"));
                    break;
                case "fix_database_issues":

//                    update_option('revslider_change_database', true);
//                    RevSliderFront::createDBTables();

                    self::ajaxResponseSuccess(t('Can\'t do anything'));
//                    self::ajaxResponseSuccess(t('Database structure creation/update done'));
                    break;
                case "update_posts_sortby":
                    $slider->updatePostsSortbyFromData($data);
                    self::ajaxResponseSuccess(t("Sortby updated"));
                    break;
                case "replace_image_urls":
                    $slider->replaceImageUrlsFromData($data);
                    self::ajaxResponseSuccess(t("All Urls replaced"));
                    break;
                case "reset_slide_settings":
                    $slider->resetSlideSettings($data);
                    self::ajaxResponseSuccess(t("Settings in all Slides changed"));
                    break;
                case "delete_template_slide":

                    $slideID = (isset($data['slide_id'])) ? $data['slide_id'] : -1;

                    if ($slideID === -1) RevSliderFunctions::throwError(t('Missing Slide ID!'));

                    $slide->initByID($slideID);
                    $slide->deleteSlide();

                    $responseText = t("Slide deleted");
                    self::ajaxResponseSuccess($responseText);
                    break;
                case "activate_purchase_code":
                    $result = false;
                    if (!empty($data['code'])) { // && !empty($data['email'])
                        $result = true;//$operations->checkPurchaseVerification($data);
                    } else {
                        RevSliderFunctions::throwError(t('The Purchase Code and the E-Mail address need to be set!'));
                        exit();
                    }

                    if ($result === true) {
                        self::ajaxResponseSuccessRedirect(t("Purchase Code Successfully Activated"),
                            RevSliderFunctions::getViewUrl(RevSliderGlobals::VIEW_SLIDERS));
                    } elseif ($result === false) {
                        RevSliderFunctions::throwError(t('Purchase Code is invalid'));
                    } else {
                        if ($result == 'temp') {
                            self::ajaxResponseSuccessRedirect(t("Purchase Code Temporary Activated"),
                                RevSliderFunctions::getViewUrl(RevSliderGlobals::VIEW_SLIDERS));
                        }
                        if ($result == 'exist') {
                            self::ajaxResponseData(array('error' => $result, 'msg' => t('Purchase Code already registered!')));
                        }
                        /*elseif($result == 'bad_email'){
                            RevSliderFunctions::throwError(__('Please add an valid E-Mail Address', 'revslider'));
                        }elseif($result == 'email_used'){
                            RevSliderFunctions::throwError(__('E-Mail already in use, please choose a different E-Mail', 'revslider'));
                        }*/
                        RevSliderFunctions::throwError(t('Purchase Code could not be validated'));
                    }
                    break;
                case "deactivate_purchase_code":
                    $result = true;//$operations->doPurchaseDeactivation($data);

                    if ($result) {
                        self::ajaxResponseSuccessRedirect(t("Successfully removed validation"),
                            RevSliderFunctions::getViewUrl(RevSliderGlobals::VIEW_SLIDERS));
                    } else {
                        RevSliderFunctions::throwError(t('Could not remove Validation!'));
                    }
                    break;
                case 'dismiss_notice':
                    RevSliderOptions::updateOption('revslider-valid-notice', 'false');
                    self::ajaxResponseSuccess(t("."));
                    break;
                case 'dismiss_dynamic_notice':
                    if (trim($data['id']) == 'DISCARD') {
                        RevSliderOptions::updateOption('revslider-deact-notice', false);
                    } elseif (trim($data['id']) == 'DISCARDTEMPACT') {
                        RevSliderOptions::updateOption('revslider-temp-active-notice', 'false');
                    } else {
                        $notices_discarded = RevSliderOptions::getOptions('revslider-notices-dc', array());
                        $notices_discarded[] = RevSliderFunctions::esc_attr(trim($data['id']));
                        RevSliderOptions::updateOption('revslider-notices-dc', $notices_discarded);
                    }

                    self::ajaxResponseSuccess(t("."));
                    break;
                case 'toggle_favorite':
                    if (isset($data['id']) && intval($data['id']) > 0) {
                        $return = $this->toggle_favorite_by_id($data['id']);
                        if ($return === true) {
                            self::ajaxResponseSuccess(t('Setting Changed!'));
                        } else {
                            $error = $return;
                        }
                    } else {
                        $error = t('No ID given');
                    }
                    self::ajaxResponseError($error);
                    break;
                case "subscribe_to_newsletter":
//                    if (isset($data['email']) && !empty($data['email'])) {
//                        $return = ThemePunch_Newsletter::subscribe($data['email']);
//
//                        if ($return !== false) {
//                            if (!isset($return['status']) || $return['status'] === 'error') {
//                                $error = (isset($return['message']) && !empty($return['message'])) ? $return['message'] : __('Invalid Email', 'revslider');
//                                self::ajaxResponseError($error);
//                            } else {
//                                self::ajaxResponseSuccess(__("Success! Please check your Emails to finish the subscription", 'revslider'), $return);
//                            }
//                        } else {
//                            self::ajaxResponseError(__('Invalid Email/Could not connect to the Newsletter server', 'revslider'));
//                        }
//                    } else {
//                        self::ajaxResponseError(__('No Email given', 'revslider'));
//                    }
                    self::ajaxResponseError(t('Nothing to do'));
                    break;
                case "unsubscribe_to_newsletter":
//                    if (isset($data['email']) && !empty($data['email'])) {
//                        $return = ThemePunch_Newsletter::unsubscribe($data['email']);
//
//                        if ($return !== false) {
//                            if (!isset($return['status']) || $return['status'] === 'error') {
//                                $error = (isset($return['message']) && !empty($return['message'])) ? $return['message'] : __('Invalid Email', 'revslider');
//                                self::ajaxResponseError($error);
//                            } else {
//                                self::ajaxResponseSuccess(__("Success! Please check your Emails to finish the process", 'revslider'), $return);
//                            }
//                        } else {
//                            self::ajaxResponseError(__('Invalid Email/Could not connect to the Newsletter server', 'revslider'));
//                        }
//                    } else {
//                        self::ajaxResponseError(__('No Email given', 'revslider'));
//                    }
                    self::ajaxResponseError(t('Nothing to do'));
                    break;
                case 'change_specific_navigation':
                    $nav = new Navigation();

                    $found = false;
                    $navigations = $nav->get_all_navigations();
                    foreach ($navigations as $navig) {
                        if ($data['id'] == $navig['id']) {
                            $found = true;
                            break;
                        }
                    }
                    if ($found) {
                        $nav->create_update_navigation($data, $data['id']);
                    } else {
                        $nav->create_update_navigation($data);
                    }

                    self::ajaxResponseSuccess(t('Navigation saved/updated'), array('navs' => $nav->get_all_navigations()));

                    break;
                case 'change_navigations':
                    $nav = new Navigation();

                    $nav->create_update_full_navigation($data);

                    self::ajaxResponseSuccess(t('Navigations updated'), array('navs' => $nav->get_all_navigations()));
                    break;
                case 'delete_navigation':
                    $nav = new Navigation();

                    if (isset($data) && intval($data) > 0) {
                        $return = $nav->delete_navigation($data);

                        if ($return !== true) {
                            self::ajaxResponseError($return);
                        } else {
                            self::ajaxResponseSuccess(t('Navigation deleted'), array('navs' => $nav->get_all_navigations()));
                        }
                    }

                    self::ajaxResponseError(t('Wrong ID given'));
                    break;
                case "get_facebook_photosets":
//                    if (!empty($data['url'])) {
//                        $facebook = new RevSliderFacebook();
//                        $return = $facebook->get_photo_set_photos_options($data['url'], $data['album'], $data['app_id'], $data['app_secret']);
//                        if (!empty($return)) {
//                            self::ajaxResponseSuccess(t('Successfully fetched Facebook albums'), array('html' => implode(' ', $return)));
//                        } else {
//                            $error = t('Could not fetch Facebook albums');
//                            self::ajaxResponseError($error);
//                        }
//                    } else {
//                        self::ajaxResponseSuccess(t('Cleared Albums'), array('html' => ''));
//                    }
                    self::ajaxResponseError(t('Nothing to do'));
                    break;
                case "get_flickr_photosets":
//                    if (!empty($data['url']) && !empty($data['key'])) {
//                        $flickr = new RevSliderFlickr($data['key']);
//                        $user_id = $flickr->get_user_from_url($data['url']);
//                        $return = $flickr->get_photo_sets($user_id, $data['count'], $data['set']);
//                        if (!empty($return)) {
//                            self::ajaxResponseSuccess(__('Successfully fetched flickr photosets', 'revslider'), array("data" => array('html' => implode(' ', $return))));
//                        } else {
//                            $error = __('Could not fetch flickr photosets', 'revslider');
//                            self::ajaxResponseError($error);
//                        }
//                    } else {
//                        if (empty($data['url']) && empty($data['key'])) {
//                            self::ajaxResponseSuccess(__('Cleared Photosets', 'revslider'), array('html' => implode(' ', $return)));
//                        } elseif (empty($data['url'])) {
//                            $error = __('No User URL - Could not fetch flickr photosets', 'revslider');
//                            self::ajaxResponseError($error);
//                        } else {
//                            $error = __('No API KEY - Could not fetch flickr photosets', 'revslider');
//                            self::ajaxResponseError($error);
//                        }
//                    }
                    self::ajaxResponseError(t('Nothing to do'));
                    break;
                case "get_youtube_playlists":
//                    if (!empty($data['id'])) {
//                        $youtube = new RevSliderYoutube(trim($data['api']), trim($data['id']));
//                        $return = $youtube->get_playlist_options($data['playlist']);
//                        self::ajaxResponseSuccess(__('Successfully fetched YouTube playlists', 'revslider'), array("data" => array('html' => implode(' ', $return))));
//                    } else {
//                        $error = __('Could not fetch YouTube playlists', 'revslider');
//                        self::ajaxResponseError($error);
//                    }
                    self::ajaxResponseError(t('Nothing to do'));
                    break;
                case 'rs_get_store_information':
//                    global $wp_version;
//
//                    $code = get_option('revslider-code', '');
//                    $shop_version = RevSliderTemplate::SHOP_VERSION;
//
//                    $validated = get_option('revslider-valid', 'false');
//                    if ($validated == 'false') {
//                        $api_key = '';
//                        $username = '';
//                        $code = '';
//                    }
//
//                    $rattr = array(
//                        'code'         => urlencode($code),
//                        'product'      => urlencode('revslider'),
//                        'shop_version' => urlencode($shop_version),
//                        'version'      => urlencode(RevSliderGlobals::SLIDER_REVISION)
//                    );
//
//                    $request = wp_remote_post('http://templates.themepunch.tools/revslider/store.php', array(
//                        'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url'),
//                        'body'       => $rattr
//                    ));
//
//                    $response = '';
//
//                    if (!is_wp_error($request)) {
//                        $response = json_decode(@$request['body'], true);
//                    }
//
//                    self::ajaxResponseData(array("data" => $response));
                    self::ajaxResponseError(t('Nothing to do'));
                    break;
                case 'load_library_object':
                    $obj_library = new ObjectLibrary();

                    $thumbhandle = $data['handle'];
                    $type = $data['type'];
                    if ($type == 'thumb') {
                        $thumb = $obj_library->_get_object_thumb($thumbhandle, 'thumb');
                    } elseif ($type == 'orig') {
                        $thumb = $obj_library->_get_object_thumb($thumbhandle, 'original');
                    }
                    if ($thumb['error']) {
                        self::ajaxResponseError(t('Object could not be loaded'));
                    } else {
                        self::ajaxResponseData(array('url' => $thumb['url'], 'width' => $thumb['width'], 'height' => $thumb['height']));
                    }
                    break;
                case 'load_template_store_sliders':
//                    $tmpl = new RevSliderTemplate();
//
//                    $tp_template_slider = $tmpl->getThemePunchTemplateSliders();
//
//                    ob_start();
//                    $tmpl->create_html_sliders($tp_template_slider);
//                    $html = ob_get_contents();
//                    ob_clean();
//                    ob_end_clean();
//
//                    self::ajaxResponseData(array('html' => $html));
                    self::ajaxResponseError(t('Nothing to do'));
                    break;
                case 'load_template_store_slides':
//                    $tmpl = new RevSliderTemplate();
//
//                    $templates = $tmpl->getTemplateSlides();
//                    $tp_template_slider = $tmpl->getThemePunchTemplateSliders();
//
//                    $tmp_slider = new RevSlider();
//                    $all_slider = apply_filters('revslider_slide_templates', $tmp_slider->getArrSliders());
//
//                    ob_start();
//                    $tmpl->create_html_slides($tp_template_slider, $all_slider, $templates);
//                    $html = ob_get_contents();
//                    ob_clean();
//                    ob_end_clean();
//
//                    self::ajaxResponseData(array('html' => $html));
                    self::ajaxResponseError(t('Nothing to do'));
                    break;
                case 'load_object_library':
                    $html = '';
                    $obj = new ObjectLibrary();
                    $mdata = $obj->retrieve_all_object_data();

                    self::ajaxResponseData(array('data' => $mdata));
                    break;
                case 'slide_editor_sticky_menu':
                    if (isset($data['set_sticky']) && $data['set_sticky'] == 'true') {
                        RevSliderOptions::updateOption('revslider_slide_editor_sticky', 'true');
                    } else {
                        RevSliderOptions::updateOption('revslider_slide_editor_sticky', 'false');
                    }
                    self::ajaxResponseData(array());
                    break;
                case 'save_color_preset':

                    $presets = TPColorpicker::save_color_presets($data['presets']);
                    self::ajaxResponseData(array('presets' => $presets));

                    break;
                default:
                    $return = false;
                    if ($return === false)
                        self::ajaxResponseError("wrong ajax action: " . ($action));

                    exit;
                    break;
            }

//        } catch (\Exception $e) {
//
//            $message = $e->getMessage();
//            if ($action == "preview_slide" || $action == "preview_slider") {
//                echo $message;
//                exit();
//            }
//
//            self::ajaxResponseError($message);
//        }

        //it's an ajax action, so exit
        self::ajaxResponseError("No response output on $action action. please check with the developer.");
        exit();
    }


    protected function ajaxResponse($success, $message, $arrData = null)
    {

        $response = array();
        $response["success"] = $success;
        $response["message"] = $message;

        if (!empty($arrData)) {

            if (gettype($arrData) == "string")
                $arrData = array("data" => $arrData);

            $response = array_merge($response, $arrData);
        }

        $json = json_encode($response);

        echo $json;
        exit();
    }

    /**
     *
     * echo json ajax response, without message, only data
     */
    protected function ajaxResponseData($arrData)
    {
        if (gettype($arrData) == "string")
            $arrData = array("data" => $arrData);

        self::ajaxResponse(true, "", $arrData);
    }


    /**
     *
     * echo json ajax response
     */
    protected function ajaxResponseError($message, $arrData = null)
    {

        self::ajaxResponse(false, $message, $arrData);
    }


    /**
     * echo ajax success response
     */
    protected function ajaxResponseSuccess($message, $arrData = null)
    {

        self::ajaxResponse(true, $message, $arrData);

    }


    /**
     * echo ajax success response
     */
    protected function ajaxResponseSuccessRedirect($message, $url)
    {
        $arrData = array("is_redirect" => true, "redirect_url" => $url);

        self::ajaxResponse(true, $message, $arrData);
    }


    protected function importSliderHandle($viewBack = null, $updateAnim = true, $updateStatic = true, $updateNavigation = true)
    {

        $slider = new Slider();
        $response = $slider->importSliderFromPost($updateAnim, $updateStatic, false, false, false, $updateNavigation);

        $sliderID = intval($response["sliderID"]);

        if(empty($viewBack)){
            $viewBack = RevSliderFunctions::getViewUrl(RevSliderGlobals::VIEW_SLIDER,array('id'=>$sliderID));
            if(empty($sliderID))
                $viewBack = RevSliderFunctions::getViewUrl(RevSliderGlobals::VIEW_SLIDERS);
        }

        //handle error this
        if ($response["success"] == false) {
            $message = $response["error"];
            Operations::import_failed_message($message, $viewBack);

        } else {    //handle success, js redirect.
            //check here to create a page or not
//            if(!empty($sliderID)){
//                $page_id = 0;
//                $page_creation = RevSliderFunctions::getPostVariable('page-creation');
//                if($page_creation === 'true'){
//                    $operations = new Operations();
//                    $page_id = $operations->create_slider_page((array)$sliderID);
//                }
//                if($page_id > 0){
//                    echo '<script>window.open("'.get_permalink($page_id).'", "_blank");</script>';
//                }
//            }

            echo "<script>location.href='" . $viewBack . "';</script>";
        }
        exit();
    }
    protected function toggle_favorite_by_id($id){
        $id = intval($id);
        if($id === 0) return false;

        //check if ID exists

        $slider = RevSliderDB::instance(array(
            'table'=>RevSliderGlobals::$table_sliders,
            'where'=>array('id',$id),
            'select'=>array('settings')
        ))->first();

        if(empty($slider))
            return t('Slider not found');

        $settings = json_decode($slider['settings'], true);

        if(!isset($settings['favorite']) || $settings['favorite'] == 'false' || $settings['favorite'] == false){
            $settings['favorite'] = 'true';
        }else{
            $settings['favorite'] = 'false';
        }
        $response = RevSliderDB::instance(array(
            'table'=>RevSliderGlobals::$table_sliders,
            'where'=>array('id',$id)
        ))->update(array(
            'settings' => json_encode($settings)
        ));

        if($response === 0 || $response === false)
            return t('Slider setting could not be changed');
        return true;
    }
    public function access(AccountInterface $account)
    {
        return AccessResult::allowed();
    }
}