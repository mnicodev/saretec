<?php
/**
 * Created by FsFlex.
 * User: VH
 * Date: 6/20/2017
 * Time: 4:46 PM
 */
namespace Drupal\revslider\Controller;


use Drupal\Core\Url;
use Drupal\revslider\Controller\Handle\SectionVariable;
use Drupal\revslider\Controller\Handle\SlideEditor;
use Drupal\revslider\Controller\RawTemplates\SliderEditor;
use Drupal\revslider\Controller\RawTemplates\SliderOverview;
use Drupal\revslider\Helper\RevSliderFunctions;
use Drupal\revslider\Model\Operations;


class RevSliderAdmin extends RevSliderControllerBase
{
    public function index()
    {
        $view = RevSliderFunctions::getRequestVariable('view','');
        switch ($view)
        {
            case 'slider':
                $response = $this->slider_editor();
                $response['#data']['view'] = 'slider-editor';
                break;
            case 'slide':
                $response = $this->slide_editor();
                $response['#data']['view'] = 'slide-editor';
                break;
            default:
                $response =  $this->overview();
                $response['#data']['view'] = 'slider-overview';
                break;
        }
        $response = RevSliderFunctions::array_merge_multi_level($response,$this->get_requiment_js_var());
        $response = RevSliderFunctions::array_merge_multi_level(array(
            '#theme'    => 'revslider_admin_master_view',
            '#attached' => array(
                'library'        => array('revslider/admin-editor'),
            ),
            '#data'=>array(
                'url'=>array(
                    'ajax_url'=>RevSliderFunctions::ajax_url()
                ),
                'rs_plugin_validated'=> true
            )
        ),$response);
        $response['#data'] = RevSliderFunctions::array_merge_multi_level($response['#data'],SectionVariable::dialog_video());
        return $response;
    }
    protected function slider_editor()
    {
        $template_seg = new SliderEditor();
        $content = $template_seg->getTemplate();
        $response = array(
            '#data'     => array(
                'slider_editor'=> $content
            )
        );
        return $response;
    }
    protected function slide_editor()
    {

        $editor = new SlideEditor();
        $data = $editor->getArrInitData();
        $response = array(
            '#data'     => $data
        );
        return $response;
    }

    public function overview()
    {

        $template_seg = new SliderOverview();

        $response = array(
            '#data'     => array(
                'slider_overview'=> $template_seg->getTemplate()
            )
        );
        return $response;

    }
    protected function get_requiment_js_var()
    {
        $operations = new Operations();
        $glob_vals = $operations->getGeneralSettingsValues();
        $pack_page_creation = RevSliderFunctions::getVal($glob_vals, "pack_page_creation", "on");
        $single_page_creation = RevSliderFunctions::getVal($glob_vals, "single_page_creation", "off");
        //$tp_color_picker_presets = TPColorpicker::get_color_presets();


        $javascript_multilanguage = self::get_javascript_multilanguage();
        $ajaxurl = Url::fromRoute('revslider.ajax')->toString();;
        $g_revNonce = 'undefined';
        $g_uniteDirPlugin = "revslider";
        $g_urlContent = '/undefined';
        $g_urlAjaxShowImage = Url::fromRoute('revslider.ajax', array('route' => 'show_image'))->toString();
        $g_urlAjaxActions = Url::fromRoute('revslider.ajax', array('route' => 'actions'))->toString();;
        $g_revslider_url = Url::fromRoute('revslider.admin')->toString();;
        $g_settingsObj = new \stdClass();
        $rs_pack_page_creation = ($pack_page_creation == 'on') ? 'true' : 'false';
        $rs_single_page_creation = ($single_page_creation == 'on') ? 'true' : 'false';
        $tp_color_picker_presets = '';//RevSliderFunctions::jsonEncodeForClientSide($tp_color_picker_presets);;
        $global_grid_sizes = array(
            'desktop'  => RevSliderFunctions::getVar($glob_vals, 'width', 1230),
            'notebook' => RevSliderFunctions::getVar($glob_vals, 'width_notebook', 1230),
            'tablet'   => RevSliderFunctions::getVar($glob_vals, 'width_tablet', 992),
            'mobile'   => RevSliderFunctions::getVar($glob_vals, 'width_mobile', 480)
        );
        $thickboxL10n = array(
		'next' => t('Next &gt;'),
		'prev' => t('&lt; Prev'),
		'image' => t('Image'),
		'of' => t('of'),
		'close' => t('Close'),
		'noiframes' => t('This feature requires inline frames. You have iframes disabled or your browser does not support them.'),
		'loadingAnimation' =>  RevSliderFunctions::asset('/public/js/thickbox/loadingAnimation.gif')
        );
        $wpColorPickerL10n = array(
        'clear' => t( 'Clear' ),
        'defaultString' => t( 'Default' ),
        'pick' => t( 'Select Color' ),
        'current' => t( 'Current Color' )
        );
        $RS_DEMO = RevSliderFunctions::isRS_DEMO();
        //
        return  array(
            '#attached'=>array(
                'drupalSettings'=>array(
                    'revslider' => compact(array(
                        'javascript_multilanguage',
                        'ajaxurl',
                        'g_revNonce',
                        'g_uniteDirPlugin',
                        'g_urlContent',
                        'g_urlAjaxShowImage',
                        'g_urlAjaxActions',
                        'g_revslider_url',
                        'g_settingsObj',
                        'rs_pack_page_creation',
                        'rs_single_page_creation',
                        'tp_color_picker_presets',
                        'global_grid_sizes',
                        'thickboxL10n',
                        'wpColorPickerL10n',
                        'RS_DEMO'
                    ))
                )
            )
        );
    }

    protected static function get_javascript_multilanguage()
    {
        $lang = array(
            'wrong_alias'                                     => t('-- wrong alias -- '),
            'nav_bullet_arrows_to_none'                       => t('Navigation Bullets and Arrows are now set to none.'),
            'create_template'                                 => t('Create Template'),
            'really_want_to_delete'                           => t('Do you really want to delete'),
            'sure_to_replace_urls'                            => t('Are you sure to replace the urls?'),
            'set_settings_on_all_slider'                      => t('Set selected settings on all Slides of this Slider? (This will be saved immediately)'),
            'select_slide_img'                                => t('Select Slide Image'),
            'select_layer_img'                                => t('Select Layer Image'),
            'select_slide_video'                              => t('Select Slide Video'),
            'show_slide_opt'                                  => t('Show Slide Options'),
            'hide_slide_opt'                                  => t('Hide Slide Options'),
            'close'                                           => t('Close'),
            'really_update_global_styles'                     => t('Really update global styles?'),
            'really_clear_global_styles'                      => t('This will remove all Global Styles, continue?'),
            'global_styles_editor'                            => t('Global Styles Editor'),
            'select_image'                                    => t('Select Image'),
            'video_not_found'                                 => t('No Thumbnail Image Set on Video / Video Not Found / No Valid Video ID'),
            'handle_at_least_three_chars'                     => t('Handle has to be at least three character long'),
            'really_change_font_sett'                         => t('Really change font settings?'),
            'really_delete_font'                              => t('Really delete font?'),
            'class_exist_overwrite'                           => t('Class already exists, overwrite?'),
            'class_must_be_valid'                             => t('Class must be a valid CSS class name'),
            'really_overwrite_class'                          => t('Really overwrite Class?'),
            'relly_delete_class'                              => t('Really delete Class'),
            'class_this_cant_be_undone'                       => t('? This can\'t be undone!'),
            'this_class_does_not_exist'                       => t('This class does not exist.'),
            'making_changes_will_probably_overwrite_advanced' => t('Making changes to these settings will probably overwrite advanced settings. Continue?'),
            'select_static_layer_image'                       => t('Select Static Layer Image'),
            'select_layer_image'                              => t('Select Layer Image'),
            'really_want_to_delete_all_layer'                 => t('Do you really want to delete all the layers?'),
            'layer_animation_editor'                          => t('Layer Animation Editor'),
            'animation_exists_overwrite'                      => t('Animation already exists, overwrite?'),
            'really_overwrite_animation'                      => t('Really overwrite animation?'),
            'default_animations_cant_delete'                  => t('Default animations can\'t be deleted'),
            'must_be_greater_than_start_time'                 => t('Must be greater than start time'),
            'sel_layer_not_set'                               => t('Selected layer not set'),
            'edit_layer_start'                                => t('Edit Layer Start'),
            'edit_layer_end'                                  => t('Edit Layer End'),
            'default_animations_cant_rename'                  => t('Default Animations can\'t be renamed'),
            'anim_name_already_exists'                        => t('Animationname already existing'),
            'css_name_already_exists'                         => t('CSS classname already existing'),
            'css_orig_name_does_not_exists'                   => t('Original CSS classname not found'),
            'enter_correct_class_name'                        => t('Enter a correct class name'),
            'class_not_found'                                 => t('Class not found in database'),
            'css_name_does_not_exists'                        => t('CSS classname not found'),
            'delete_this_caption'                             => t('Delete this caption? This may affect other Slider'),
            'this_will_change_the_class'                      => t('This will update the Class with the current set Style settings, this may affect other Sliders. Proceed?'),
            'unsaved_changes_will_not_be_added'               => t('Template will have the state of the last save, proceed?'),
            'please_enter_a_slide_title'                      => t('Please enter a Slide title'),
            'please_wait_a_moment'                            => t('Please Wait a Moment'),
            'copy_move'                                       => t('Copy / Move'),
            'preset_loaded'                                   => t('Preset Loaded'),
            'add_bulk_slides'                                 => t('Add Bulk Slides'),
            'arrows'                                          => t('Arrows'),
            'bullets'                                         => t('Bullets'),
            'thumbnails'                                      => t('Thumbnails'),
            'tabs'                                            => t('Tabs'),
            'delete_navigation'                               => t('Delete this Navigation?'),
            'could_not_update_nav_name'                       => t('Navigation name could not be updated'),
            'name_too_short_sanitize_3'                       => t('Name too short, at least 3 letters between a-zA-z needed'),
            'nav_name_already_exists'                         => t('Navigation name already exists, please choose a different name'),
            'remove_nav_element'                              => t('Remove current element from Navigation?'),
            'create_this_nav_element'                         => t('This navigation element does not exist, create one?'),
            'overwrite_animation'                             => t('Overwrite current animation?'),
            'cant_modify_default_anims'                       => t('Default animations can\'t be changed'),
            'anim_with_handle_exists'                         => t('Animation already existing with given handle, please choose a different name.'),
            'really_delete_anim'                              => t('Really delete animation:'),
            'this_will_reset_navigation'                      => t('This will reset the navigation, continue?'),
            'preset_name_already_exists'                      => t('Preset name already exists, please choose a different name'),
            'delete_preset'                                   => t('Really delete this preset?'),
            'update_preset'                                   => t('This will update the preset with the current settings. Proceed?'),
            'maybe_wrong_yt_id'                               => t('No Thumbnail Image Set on Video / Video Not Found / No Valid Video ID'),
            'preset_not_found'                                => t('Preset not found'),
            'cover_image_needs_to_be_set'                     => t('Cover Image need to be set for videos'),
            'remove_this_action'                              => t('Really remove this action?'),
            'layer_action_by'                                 => t('Layer is triggered by '),
            'due_to_action'                                   => t(' due to action: '),
            'layer'                                           => t('layer:'),
            'start_layer_in'                                  => t('Start Layer "in" animation'),
            'start_layer_out'                                 => t('Start Layer "out" animation'),
            'start_video'                                     => t('Start Media'),
            'stop_video'                                      => t('Stop Media'),
            'mute_video'                                      => t('Mute Media'),
            'unmute_video'                                    => t('Unmute Media'),
            'toggle_layer_anim'                               => t('Toggle Layer Animation'),
            'toggle_video'                                    => t('Toggle Media'),
            'toggle_mute_video'                               => t('Toggle Mute Media'),
            'toggle_global_mute_video'                        => t('Toggle Mute All Media'),
            'last_slide'                                      => t('Last Slide'),
            'simulate_click'                                  => t('Simulate Click'),
            'togglefullscreen'                                => t('Toggle FullScreen'),
            'gofullscreen'                                    => t('Go FullScreen'),
            'exitfullscreen'                                  => t('Exit FullScreen'),
            'toggle_class'                                    => t('Toogle Class'),
            'copy_styles_to_hover_from_idle'                  => t('Copy hover styles to idle?'),
            'copy_styles_to_idle_from_hover'                  => t('Copy idle styles to hover?'),
            'select_at_least_one_device_type'                 => t('Please select at least one device type'),
            'please_select_first_an_existing_style'           => t('Please select an existing Style Template'),
            'cant_remove_last_transition'                     => t('Can not remove last transition!'),
            'name_is_default_animations_cant_be_changed'      => t('Given animation name is a default animation. These can not be changed.'),
            'override_animation'                              => t('Animation exists, override existing animation?'),
            'this_feature_only_if_activated'                  => t('This feature is only available if you activate Slider Revolution for this installation'),
            'unsaved_data_will_be_lost_proceed'               => t('Unsaved data will be lost, proceed?'),
            'delete_user_slide'                               => t('This will delete this Slide Template, proceed?'),
            'is_loading'                                      => t('is Loading...'),
            'google_fonts_loaded'                             => t('Google Fonts Loaded'),
            'delete_layer'                                    => t('Delete Layer?'),
            'this_template_requires_version'                  => t('This template requires at least version'),
            'of_slider_revolution'                            => t('of Slider Revolution to work.'),
            'slider_revolution_shortcode_creator'             => t('Slider Revolution Shortcode Creator'),
            'slider_informations_are_missing'                 => t('Slider informations are missing!'),
            'shortcode_generator'                             => t('Shortcode Generator'),
            'please_add_at_least_one_layer'                   => t('Please add at least one Layer.'),
            'choose_image'                                    => t('Choose Image'),
            'shortcode_parsing_successfull'                   => t('Shortcode parsing successfull. Items can be found in step 3'),
            'shortcode_could_not_be_correctly_parsed'         => t('Shortcode could not be parsed.'),
            'background_video'                                => t('Background Video'),
            'active_video'                                    => t('Video in Active Slide'),
            'empty_data_retrieved_for_slider'                 => t('Data could not be fetched for selected Slider'),
            'import_selected_layer'                           => t('Import Selected Layer?'),
            'import_all_layer_from_actions'                   => t('Layer Imported! The Layer has actions which include other Layers. Import all connected layers?'),
            'not_available_in_demo'                           => t('Not available in Demo Mode'),
            'leave_not_saved'                                 => t('By leaving now, all changes since the last saving will be lost. Really leave now?'),
            'static_layers'                                   => t('--- Static Layers ---'),
            'objects_only_available_if_activated'             => t('Only available if plugin is activated'),
            'download_install_takes_longer'                   => t('Download/Install takes longer than usual, please wait'),
            'download_failed_check_server'                    => t('<div class="import_failure">Download/Install seems to have failed.</div><br><br>'),
            'aborting_import'                                 => t('<b>Aborting Import...</b>'),
            'create_draft'                                    => t('Creating Draft Page...'),
            'draft_created'                                   => t('Draft Page created. Popup will open'),
            'draft_not_created'                               => t('Draft Page could not be created.'),
            'slider_import_success_reload'                    => t('Slider import successful'),
            'save_changes'                                    => t('Save Changes?')
        );
        return $lang;
    }
}