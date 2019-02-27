<?php
/**
 * Created by FsFlex.
 * User: VH
 * Date: 7/20/2017
 * Time: 10:32 AM
 */

namespace Drupal\revslider\Controller\Handle;


use Drupal\revslider\Controller\RawTemplates\IdEsw;
use Drupal\revslider\Helper\RevSliderFunctions;
use Drupal\revslider\Helper\RevSliderGlobals;
use Drupal\revslider\Helper\RevSliderPluginUpdate;
use Drupal\revslider\Helper\TPColorpicker;
use Drupal\revslider\Model\Slide;
use Drupal\revslider\Model\Slider;

class SectionVariable
{
    public static function dialog_video(array $args = array())
    {
        extract($args);
        $url = array(
            'gif_vimeo_loader'   => RevSliderFunctions::asset('/admin/images/loader.gif'),
            'gif_youtube_loader' => RevSliderFunctions::asset('/admin/images/loader.gif'),
        );
        $global = array(
            'dialog_video' => array(
                'youtube_video_example'     => RevSliderGlobals::YOUTUBE_EXAMPLE_ID,
                'default_youtube_arguments' => RevSliderGlobals::DEFAULT_YOUTUBE_ARGUMENTS,
                'default_vimeo_arguments'   => RevSliderGlobals::DEFAULT_VIMEO_ARGUMENTS
            )
        );
        return compact(array('url', 'global'));
    }

    public static function dialog_copy_move(array $args)
    {
        extract($args);
        $url = array();
        $global = array(
            'dialog_copy_move' => array(
                'selectSliders' => $selectSliders,
            )
        );
        return compact(array('url', 'global'));
    }

    public static function slide_editor_scripts_library(array $args)
    {
        extract($args);
        $url = array();
        $global = array(
            'slide_editor_scripts_library' => array(
                'sgfamilies' => array(),
            )
        );
        $raw_sgfamilies = array();
        $googleFont = $slider->getParam("google_font",array());
        if (!empty($googleFont)) {
            if (is_array($googleFont)) {
                foreach ($googleFont as $key => $font) {
                    $raw_sgfamilies[] = $font;
                }
            } else {
                $raw_sgfamilies[] = $googleFont;
            }
        }

        //add here all new google fonts of the layers, with full variants and subsets
        $gfsubsets = $slider->getParam("subsets", array());
        $gf = $slider->getUsedFonts(true);
        foreach ($gf as $gfk => $gfv) {
            $tcf = $gfk . ':';
            if (!empty($gfv['variants'])) {
                $mgfirst = true;
                foreach ($gfv['variants'] as $mgvk => $mgvv) {
                    if (!$mgfirst) $tcf .= ',';
                    $tcf .= $mgvk;
                    $mgfirst = false;
                }
            }

            if (!empty($gfv['subsets'])) {

                $mgfirst = true;
                foreach ($gfv['subsets'] as $ssk => $ssv) {
                    if ($mgfirst) $tcf .= '&subset=';
                    if (!$mgfirst) $tcf .= ',';
                    $tcf .= $ssv;
                    $mgfirst = false;
                }
            }
            $raw_sgfamilies[] = $tcf;
        }
        $global['slide_editor_scripts_library']['sgfamilies'] = $raw_sgfamilies;
        return compact(array('url', 'global'));
    }

    public static function breadcrumbs(array $args)
    {
        extract($args);
        $slider_url = ($sliderTemplate == 'true') ? RevSliderGlobals::VIEW_SLIDER_TEMPLATE : RevSliderGlobals::VIEW_SLIDER;
        $url = array(
            'view' => array(
                'sliders'    => RevSliderFunctions::getViewUrl(RevSliderGlobals::VIEW_SLIDERS),
                'slider'     => RevSliderFunctions::getViewUrl(RevSliderGlobals::VIEW_SLIDER, array('id' => $sliderID)),
                'slider_url' => RevSliderFunctions::getViewUrl($slider_url, array('id' => $sliderID))
            )
        );

        $global = array(
            'breadcrumbs' => array(
                'savebtnid'    => 'button_save_static_slide-tb',
                'prevbtn'      => "button_preview_slider-tb",
                'slider_title' => $slider->getParam("title", "")
            )
        );
        if (!$slide->isStaticSlide()) {
            $global['breadcrumbs']['savebtnid'] = "button_save_slide-tb";
            $global['breadcrumbs']['prevbtn'] = "button_preview_slide-tb";
            if ($slider->isSlidesFromPosts()) {
                $global['breadcrumbs']['prevbtn'] = "button_preview_slider-tb";
            }
        }
        return compact(array('url', 'global'));
    }

    public static function slide_selector(array $args)
    {
        extract($args);
        //
        $the_slidertype = $slider->getParam('slider-type', 'standard');
        $active_slide = -1;
        if ($the_slidertype == 'hero') {
            $active_slide = $slider->getParam('hero_active', -1);
            //check if this id is still existing
            $exists = Slide::isSlideByID($active_slide);
            if (empty($exists)) {
                $active_slide = -1;
            }
        }

        $sID = $slider->getID();
        $url = array(
            'view' => array(
                'static_slide' => RevSliderFunctions::getViewUrl(RevSliderGlobals::VIEW_SLIDE, array('id' => 'static_' . $sID))
            )
        );
        $global = array(
            'slide_selector' => array(
                '_width'      => $slider->getParam('width', 1280),
                '_height'     => $slider->getParam('height', 868),
                'staticclass' => '',
                'slide'       => array(
                    'isStaticSlide' => $slide->isStaticSlide()
                ),
                'arrSlides'   => array(),
                'slideID'     => $slideID
            )
        );

        if ($slide->isStaticSlide()) {
            $global['slide_selector']['staticclass'] = 'statictabselected';
            $all_slides = $slider->getSlides(true);
            $global['slide_selector']['all_slides'] = array();
            foreach ($all_slides as $c_slide) {
                $c_params = $c_slide->getParams();
                $c_slide_params_use = array();
                $c_slide_params_use['id'] = $c_slide->getID();
                $c_slide_params_use['option_label'] = stripslashes(RevSliderFunctions::getVal($c_params, 'title', 'Slide')) . ' (ID: ' . $c_slide->getID() . ')';
                $global['slide_selector']['all_slides'][] = $c_slide_params_use;
            }
        }
        $slidecounter = 0;
        foreach ($arrSlides as $t_slide) {
            $slidelistID = $t_slide->getID();
            /* BACKGROUND SETTINGS */
            $c_bgType = $t_slide->getParam('background_type', 'transparent');
            $c_bgColor = $t_slide->getParam('slide_bg_color', 'transparent');

            $c_bgFit = $t_slide->getParam('bg_fit', 'cover');
            $c_bgFitX = intval($t_slide->getParam('bg_fit_x', '100'));
            $c_bgFitY = intval($t_slide->getParam('bg_fit_y', '100'));

            $c_bgPosition = $t_slide->getParam('bg_position', 'center center');
            $c_bgPositionX = intval($t_slide->getParam('bg_position_x', '0'));
            $c_bgPositionY = intval($t_slide->getParam('bg_position_y', '0'));

            $c_bgRepeat = $t_slide->getParam('bg_repeat', 'no-repeat');

            $c_isvisible = $t_slide->getParam('state', 'published');

            $c_thumb_for_admin = $t_slide->getParam('thumb_for_admin', 'off');
            $c_real_thumbURL = $t_slide->getParam('slide_thumb', '');

            $c_bgStyle = ' ';
            if ($c_bgFit == 'percentage') {
                $c_bgStyle .= "background-size: " . $c_bgFitX . '% ' . $c_bgFitY . '%;';
            } else {
                $c_bgStyle .= "background-size: " . $c_bgFit . ";";
            }
            if ($c_bgPosition == 'percentage') {
                $c_bgStyle .= "background-position: " . $c_bgPositionX . '% ' . $c_bgPositionY . '%;';
            } else {
                $c_bgStyle .= "background-position: " . $c_bgPosition . ";";
            }
            $c_bgStyle .= "background-repeat: " . $c_bgRepeat . ";";
            $c_urlImageForView = $t_slide->getThumbUrl();

            $c_bg_fullstyle = '';
            $c_bg_extraClass = '';

            if ($c_bgType == 'image' || $c_bgType == 'streamvimeo' || $c_bgType == 'streamyoutube' || $c_bgType == 'streaminstagram') {
                switch ($slider_type) {
                    case 'posts':
                        $c_urlImageForView = RevSliderFunctions::asset('public/assets/sources/post.png');
                        break;
                    case 'woocommerce':
                        $c_urlImageForView = RevSliderFunctions::asset('public/assets/sources/wc.png');
                        break;
                    case 'facebook':
                        $c_urlImageForView = RevSliderFunctions::asset('public/assets/sources/fb.png');
                        break;
                    case 'twitter':
                        $c_urlImageForView = RevSliderFunctions::asset('public/assets/sources/tw.png');
                        break;
                    case 'instagram':
                        $c_urlImageForView = RevSliderFunctions::asset('public/assets/sources/ig.png');
                        break;
                    case 'flickr':
                        $c_urlImageForView = RevSliderFunctions::asset('public/assets/sources/fr.png');
                        break;
                    case 'youtube':
                        $c_urlImageForView = RevSliderFunctions::asset('public/assets/sources/yt.png');
                        break;
                    case 'vimeo':
                        $c_urlImageForView = RevSliderFunctions::asset('public/assets/sources/vm.png');
                        break;
                }
            }

            if ($c_bgType == 'image' || $c_bgType == 'vimeo' || $c_bgType == 'youtube' || $c_bgType == 'html5' || $c_bgType == 'streamvimeo' || $c_bgType == 'streamyoutube' || $c_bgType == 'streaminstagram')
                $c_bg_fullstyle = ' style="background-image:url(' . $c_urlImageForView . ');' . $c_bgStyle . '" ';

            if ($c_bgType == 'solid')
                $c_bg_fullstyle = ' style="background:' . TPColorpicker::get($c_bgColor) . ';" ';
            if ($c_bgType == 'trans')
                $c_bg_extraClass = 'mini-transparent';

            if ($c_thumb_for_admin == "on")
                $c_bg_fullstyle = ' style="background-image:url(' . $c_real_thumbURL . ');background-size:cover;background-position:center center" ';

            /* END OF BG SETTINGS */
            $slidecounter++;
            $title = $t_slide->getParam('title', 'Slide');
            $slideName = $title;
            $arrChildrenIDs = $t_slide->getArrChildrenIDs();

            $class = 'tipsy_enabled_top';
            $titleclass = '';
            $c_topclass = '';
            $urlEditSlide = RevSliderFunctions::getViewUrl(RevSliderGlobals::VIEW_SLIDE, array('id' => $slidelistID));
            if ($slideID == $slidelistID || in_array($slideID, $arrChildrenIDs)) {
                $class .= ' selected';
                $c_topclass = ' selected';
                $titleclass = ' ';
                $urlEditSlide = 'javascript:void(0)';
            }

            $addParams = "class='" . $class . "'";
            $slideName = str_replace("'", "", $slideName);
            $title = RevSliderFunctions::esc_attr(stripslashes($title));
            $is_slide_id_not_in_children_ids = !in_array($slideID, $arrChildrenIDs);
            $t_slide_params_use = array(
                'slidelistID'                     => $slidelistID,
                'c_topclass'                      => $c_topclass,
                'urlEditSlide'                    => $urlEditSlide,
                'addParams'                       => $addParams,
                'c_bg_extraClass'                 => $c_bg_extraClass,
                'c_bg_fullstyle'                  => $c_bg_fullstyle,
                'c_isvisible'                     => $c_isvisible,
                'active_slide'                    => $active_slide,
                'slidecounter'                    => $slidecounter,
                'title'                           => $title,
                'arrChildrenIDs'                  => $arrChildrenIDs,
                'the_slidertype'                  => $the_slidertype,
                'is_slide_id_not_in_children_ids' => $is_slide_id_not_in_children_ids
            );
            $global['slide_selector']['arrSlides'][] = $t_slide_params_use;
        }

        return $response = compact(array('url', 'global'));
    }

    public static function slide_general_settings(array $args)
    {
        extract($args);

        $url = array(
            'view' => array()
        );
        $global = array(
            'slide_general_settings' => array(
                'slide'                               => array(
                    'isStaticSlide' => $slide->isStaticSlide(),
                ),
                'slider_type'                         => $slider_type,
                'img_sizes'                           => array(),
                'bg_image_size'                       => $bg_image_size,
                'alt_option'                          => RevSliderFunctions::getVal($slideParams, 'alt_option', 'media_library'),
                'alt_attr'                            => RevSliderFunctions::getVal($slideParams, 'alt_attr', ''),
                'title_option'                        => RevSliderFunctions::getVal($slideParams, 'title_option', 'media_library'),
                'ext_width'                           => $ext_width,
                'ext_height'                          => $ext_height,
                'title_attr'                          => RevSliderFunctions::getVal($slideParams, 'title_attr', ''),
                'video_force_cover'                   => $video_force_cover,
                'video_dotted_overlay'                => $video_dotted_overlay,
                'video_ratio'                         => $video_ratio,
                'video_start_at'                      => $video_start_at,
                'video_end_at'                        => $video_end_at,
                'video_loop'                          => $video_loop,
                'video_nextslide'                     => $video_nextslide,
                'video_force_rewind'                  => $video_force_rewind,
                'video_mute'                          => $video_mute,
                'video_volume'                        => $video_volume,
                'video_speed'                         => $video_speed,
                'video_arguments'                     => $video_arguments,
                'video_arguments_vim'                 => $video_arguments_vim,
                'bgFit'                               => $bgFit,
                'bgFitX'                              => $bgFitX,
                'bgFitY'                              => $bgFitY,
                'bgPosition'                          => $bgPosition,
                'bgPositionX'                         => $bgPositionX,
                'bgPositionY'                         => $bgPositionY,
                'bgRepeat'                            => $bgRepeat,
                'slide_parallax_level'                => $slide_parallax_level,
                'parallax_level'                      => $parallax_level,
                'parallaxbgfreeze'                    => $parallaxbgfreeze,
                'parallaxisddd'                       => $parallaxisddd,
                'use_parallax'                        => $use_parallax,
                'mediafilter'                         => $mediafilter,
                'kenburn_effect'                      => $kenburn_effect,
                'kb_start_fit'                        => intval($kb_start_fit),
                'kb_end_fit'                          => intval($kb_end_fit),
                'kbStartOffsetX'                      => $kbStartOffsetX,
                'kbEndOffsetX'                        => $kbEndOffsetX,
                'kbStartOffsetY'                      => $kbStartOffsetY,
                'kbEndOffsetY'                        => $kbEndOffsetY,
                'kbStartRotate'                       => $kbStartRotate,
                'kbEndRotate'                         => $kbEndRotate,
                'kbBlurStart'                         => $kbBlurStart,
                'kbBlurEnd'                           => $kbBlurEnd,
                'kb_easing'                           => $kb_easing,
                'kb_duration'                         => intval($kb_duration),
                'imageUrl'                            => $imageUrl,
                'imageID'                             => $imageID,
                'staticoverflow'                      => RevSliderFunctions::getVal($slideParams, 'staticoverflow', 'published'),
                'title'                               => RevSliderFunctions::esc_attr(stripslashes(RevSliderFunctions::getVal($slideParams, 'title', 'Slide'))),
                'delay'                               => intval(RevSliderFunctions::getVal($slideParams, 'delay', '')),
                'stoponpurpose'                       => RevSliderFunctions::getVal($slideParams, 'stoponpurpose', 'published'),
                'invisibleslide'                      => RevSliderFunctions::getVal($slideParams, 'invisibleslide', 'published'),
                'state'                               => RevSliderFunctions::getVal($slideParams, 'state', 'published'),
                'hideslideafter'                      => RevSliderFunctions::getVal($slideParams, 'hideslideafter', 0),
                'hideslideonmobile'                   => RevSliderFunctions::getVal($slideParams, 'hideslideonmobile', 'off'),
                'date_from'                           => RevSliderFunctions::getVal($slideParams, 'date_from', ''),
                'date_to'                             => RevSliderFunctions::getVal($slideParams, 'date_to', ''),
                'save_performance'                    => RevSliderFunctions::getVal($slideParams, 'save_performance', 'off'),
                'slide_thumb'                         => ($slide_thumb = RevSliderFunctions::getVal($slideParams, 'slide_thumb', '')),
                'slide_thumb_url'                     => '',
                'thumb_dimension'                     => RevSliderFunctions::getVal($slideParams, 'thumb_dimension', 'slider'),
                'thumb_for_admin'                     => RevSliderFunctions::getVal($slideParams, 'thumb_for_admin', 'off'),
                'js_choosen_slide_transition'         => '',
                'js_transition_settings'              => array(
                    'slot'     => '',
                    'rotation' => '',
                    'duration' => '',
                    'ease_in'  => '',
                    'ease_out' => ''
                ),
                'slide_general_addon'                 => array(),
                'html_navigation_placeholder_wrapper' => self::get_html_navigation_placeholder_wrapper(array(
                    'slider'          => $slider,
                    'slideParams'     => $slideParams,
                    'arr_navigations' => $arr_navigations,
                    'operations'      => $operations,
                )),
                'slide_info_settings'                 => array(),
                'slide_description'                   => stripslashes(RevSliderFunctions::getVal($slideParams, 'slide_description', '')),
                'class_attr'                          => RevSliderFunctions::getVal($slideParams, 'class_attr', ''),
                'id_attr'                             => RevSliderFunctions::getVal($slideParams, 'id_attr', ''),
                'data_attr'                           => stripslashes(RevSliderFunctions::getVal($slideParams, 'data_attr', '')),
                'enable_link'                         => RevSliderFunctions::getVal($slideParams, 'enable_link', 'false'),
                'enable_link_2'                       => RevSliderFunctions::getVal($slideParams, 'link_type', 'regular'),
                'val_link'                            => RevSliderFunctions::getVal($slideParams, 'link', ''),
                'link_open_in'                        => RevSliderFunctions::getVal($slideParams, 'link_open_in', 'same'),
                'link_pos'                            => RevSliderFunctions::getVal($slideParams, 'link_pos', 'front'),
                'arrSlideLinkLayers'                  => array(),
                'slide_link'                          => RevSliderFunctions::getVal($slideParams, 'slide_link', 'nothing'),
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
            )
        );
//        $slide_link = RevSliderFunctions::getVal($slideParams, 'slide_link', 'nothing');
        //num_slide_link
        $arrSlideLink = array();
        $arrSlideLink["nothing"] = t("-- Not Chosen --");
        $arrSlideLink["next"] = t("-- Next Slide --");
        $arrSlideLink["prev"] = t("-- Previous Slide --");

        $arrSlideLinkLayers = $arrSlideLink;
        $arrSlideLinkLayers["scroll_under"] = t("-- Scroll Below Slider --");
        $arrSlideNames = array();
        if (isset($slider) && $slider->isInited())
            $arrSlideNames = $slider->getArrSlideNames();
        if (!empty($arrSlideNames) && is_array($arrSlideNames)) {
            foreach ($arrSlideNames as $slideNameID => $arr) {
                $slideName = RevSliderFunctions::esc_attr(stripslashes($arr["title"]));
                $arrSlideLink[$slideNameID] = $slideName;
                $arrSlideLinkLayers[$slideNameID] = $slideName;
            }
            $global['slide_general_settings']['arrSlideLinkLayers'] = $arrSlideLinkLayers;
        }

        $slide_info_settings = array();
        for ($i = 1; $i < 11; $i++) {
            $setting_info_use = array();
            $setting_info_use['index'] = $i;
            $setting_info_use['value'] = stripslashes(RevSliderFunctions::esc_attr(RevSliderFunctions::getVal($slideParams, 'params_' . $i, '')));
            $setting_info_use['max_chars'] = RevSliderFunctions::getVal($slideParams, 'params_' . $i . '_chars', 10);
            $slide_info_settings[] = $setting_info_use;
        }
        $global['slide_general_settings']['slide_info_settings'] = $slide_info_settings;
        if (!empty($slide_general_addon))
            $global['slide_general_settings']['slide_general_addon'] = $slide_general_addon;
        //

        $js_raw_choosen_slide_transition = array();
        $js_raw_transition_settings = array(
            'slot'     => array(),
            'rotation' => array(),
            'duration' => array(),
            'ease_in'  => array(),
            'ease_out' => array()
        );
        $slide_transition = RevSliderFunctions::getVal($slideParams, 'slide_transition', $def_transition);
        if (!is_array($slide_transition))
            $slide_transition = explode(',', $slide_transition);

        if (!is_array($slide_transition))
            $slide_transition = array($slide_transition);
        $slot_amount = (array)RevSliderFunctions::getVal($slideParams, 'slot_amount', 'default');
        $transition_rotation = (array)RevSliderFunctions::getVal($slideParams, 'transition_rotation', '0');
        $transition_duration = (array)RevSliderFunctions::getVal($slideParams, 'transition_duration', $def_transition_duration);
        $transition_ease_in = (array)RevSliderFunctions::getVal($slideParams, 'transition_ease_in', 'default');
        $transition_ease_out = (array)RevSliderFunctions::getVal($slideParams, 'transition_ease_out', 'default');

        $tr_count = count($slide_transition);
        foreach ($slide_transition as $tr) {
            $js_raw_choosen_slide_transition[] = $tr;
        }


        foreach ($slot_amount as $sa) {
            $js_raw_transition_settings['slot'][] = $sa;
        }
        $sac = count($slot_amount);
        if ($sac < $tr_count) {
            while ($sac < $tr_count) {
                $sac++;
                $js_raw_transition_settings['slot'][] = $slot_amount[0];
            }
        }

        foreach ($transition_rotation as $sa) {
            $js_raw_transition_settings['rotation'][] = $sa;
        }
        $sac = count($transition_rotation);
        if ($sac < $tr_count) {
            while ($sac < $tr_count) {
                $sac++;
                $js_raw_transition_settings['rotation'][] = $transition_rotation[0];
            }
        }

        foreach ($transition_duration as $sa) {
            $js_raw_transition_settings['duration'][] = $sa;
        }
        $sac = count($transition_duration);
        if ($sac < $tr_count) {
            while ($sac < $tr_count) {
                $sac++;
                $js_raw_transition_settings['duration'][] = $transition_duration[0];
            }
        }

        foreach ($transition_ease_in as $sa) {
            $js_raw_transition_settings['ease_in'][] = $sa;
        }
        $sac = count($transition_ease_in);
        if ($sac < $tr_count) {
            while ($sac < $tr_count) {
                $sac++;
                $js_raw_transition_settings['ease_in'][] = $transition_ease_in[0];
            }
        }

        foreach ($transition_ease_out as $sa) {
            $js_raw_transition_settings['ease_out'][] = $sa;
        }
        $sac = count($transition_ease_out);
        if ($sac < $tr_count) {
            while ($sac < $tr_count) {
                $sac++;
                $js_raw_transition_settings['ease_out'] = $transition_ease_out[0];
            }
        }
        $global['slide_general_settings']['js_choosen_slide_transition'] = (empty($js_raw_choosen_slide_transition)) ? '' : '"' . implode('","', $js_raw_choosen_slide_transition) . '"';
        $global['slide_general_settings']['js_transition_settings'] = array(
            'slot'     => (empty($js_raw_transition_settings['slot'])) ? '' : '"' . implode('","', $js_raw_transition_settings['slot']) . '"',
            'rotation' => (empty($js_raw_transition_settings['rotation'])) ? '' : '"' . implode('","', $js_raw_transition_settings['rotation']) . '"',
            'duration' => (empty($js_raw_transition_settings['duration'])) ? '' : '"' . implode('","', $js_raw_transition_settings['duration']) . '"',
            'ease_in'  => (empty($js_raw_transition_settings['ease_in'])) ? '' : '"' . implode('","', $js_raw_transition_settings['ease_in']) . '"',
            'ease_out' => (empty($js_raw_transition_settings['ease_out'])) ? '' : '"' . implode('","', $js_raw_transition_settings['ease_out']) . '"'
        );
        $global['slide_general_settings']['transmenu'] = '';
        $global['slide_general_settings']['listoftrans'] = '';
        $transitions = $operations->getArrTransition();
        if (!empty($transitions) && is_array($transitions)) {
            $transmenu = '<ul class="slide-trans-menu">';
            $lastclass = '';
            $transchecks = '';
            $listoftrans = '<div class="slide-trans-lists">';

            foreach ($transitions as $tran_handle => $tran_name) {

                $sel = (in_array($tran_handle, $slide_transition)) ? ' checked="checked"' : '';

                if (strpos($tran_handle, 'notselectable') !== false) {
                    $listoftrans = $listoftrans . $transchecks;
                    $lastclass = "slide-trans-" . $tran_handle;
                    $transmenu = $transmenu . '<li class="slide-trans-menu-element" data-reference="' . $lastclass . '">' . $tran_name . '</li>';
                    $transchecks = '';

                } else
                    $transchecks = $transchecks . '<div class="slide-trans-checkelement ' . $lastclass . '"><input name="slide_transition[]" type="checkbox" data-useval="true" value="' . $tran_handle . '"' . $sel . '>' . $tran_name . '</div>';
            }

            $listoftrans = $listoftrans . $transchecks;
            $transmenu = $transmenu . "</ul>";
            $listoftrans = $listoftrans . "</div>";
            $global['slide_general_settings']['transmenu'] = $transmenu;
            $global['slide_general_settings']['listoftrans'] = $listoftrans;
        }
        $global['slide_general_settings']['slide_transition'] = $slide_transition;
        $global['slide_general_settings']['slot_amount'] = $slot_amount;
        $global['slide_general_settings']['transition_rotation'] = $transition_rotation;
        $global['slide_general_settings']['transition_duration'] = $transition_duration;
        $global['slide_general_settings']['transition_ease_in'] = $transition_ease_in;
        $global['slide_general_settings']['transition_ease_out'] = $transition_ease_out;
        //

        if (intval($slide_thumb) > 0) {
            $global['slide_general_settings']['slide_thumb_url'] = RevSliderFunctions::ajax_url(array(
                'action' => 'revslider_show_image',
                'img'    => $slide_thumb,
                'w'      => '100',
                'h'      => '70',
                't'      => 'exact'
            ));
        } elseif ($slide_thumb !== '') {
            $global['slide_general_settings']['slide_thumb_url'] = $slide_thumb;
        }
        foreach ($img_sizes as $imghandle => $imgSize) {
            $img_sizes_use_params = array(
                'imghandle'     => $imghandle,
                'imghandle_val' => RevSliderFunctions::sanitize_title($imghandle),
                'imgSize'       => $imgSize,
            );
            $global['slide_general_settings']['img_sizes'][] = $img_sizes_use_params;
        }

        return compact(array('url', 'global'));
    }

    protected static function get_html_navigation_placeholder_wrapper(array $args)
    {
        extract($args);

        ob_start();
        $ph_types = array('navigation_arrow_style' => 'arrows', 'navigation_bullets_style' => 'bullets', 'tabs_style' => 'tabs', 'thumbnails_style' => 'thumbs');
        foreach ($ph_types as $phname => $pht) {

            $ph_arr_type = $slider->getParam($phname, '');

            $ph_init = array();
            foreach ($arr_navigations as $nav) {
                if ($nav['handle'] == $ph_arr_type) { //check for settings, placeholders
                    if (isset($nav['settings']) && isset($nav['settings']['placeholders'])) {
                        foreach ($nav['settings']['placeholders'] as $placeholder) {
                            if (empty($placeholder)) continue;

                            $ph_vals = array();

                            //$placeholder['type']
                            foreach ($placeholder['data'] as $k => $d) {
                                $get_from = RevSliderFunctions::getVal($slideParams, 'ph-' . $ph_arr_type . '-' . $pht . '-' . $placeholder['handle'] . '-' . $k . '-slide', 'off');
                                if ($get_from == 'on') { //get from Slide
                                    $ph_vals[$k] = stripslashes(RevSliderFunctions::getVal($slideParams, 'ph-' . $ph_arr_type . '-' . $pht . '-' . $placeholder['handle'] . '-' . $k, $d));
                                } else { ////get from Slider
                                    $ph_vals[$k] = stripslashes($slider->getParam('ph-' . $ph_arr_type . '-' . $pht . '-' . $placeholder['handle'] . '-' . $k, $d));
                                }
                            }
                            ?>
                            <?php if ($placeholder['nav-type'] === $pht) { ?>
                                <li class="custom-nav-types nav-type-<?php echo $placeholder['nav-type']; ?>">
                                    <?php
                                    switch ($placeholder['type']) {
                                        case 'color':
                                            $get_from = RevSliderFunctions::getVal($slideParams, 'ph-' . $ph_arr_type . '-' . $pht . '-' . $placeholder['handle'] . '-color-slide', 'off');
                                            ?>
                                            <label><?php echo $placeholder['title']; ?></label>
                                            <input type="checkbox" class="tp-moderncheckbox"
                                                   id="<?php echo 'ph-' . $ph_arr_type . '-' . $pht . '-' . $placeholder['handle'] . '-color-slide'; ?>"
                                                   name="<?php echo 'ph-' . $ph_arr_type . '-' . $pht . '-' . $placeholder['handle'] . '-color-slide'; ?>"
                                                   data-unchecked="off" <?php RevSliderFunctions::checked($get_from, 'on'); ?>>
                                            <input type="text"
                                                   name="<?php echo 'ph-' . $ph_arr_type . '-' . $pht . '-' . $placeholder['handle'] . '-color'; ?>"
                                                   class="my-alphacolor-field"
                                                   value="<?php echo $ph_vals['color']; ?>">
                                            <?php
                                            break;

                                        case 'color-rgba':
                                            $get_from = RevSliderFunctions::getVal($slideParams, 'ph-' . $ph_arr_type . '-' . $pht . '-' . $placeholder['handle'] . '-color-rgba-slide', 'off');
                                            ?>
                                            <label><?php echo $placeholder['title']; ?></label>
                                            <input type="checkbox" class="tp-moderncheckbox"
                                                   id="<?php echo 'ph-' . $ph_arr_type . '-' . $pht . '-' . $placeholder['handle'] . '-color-rgba-slide'; ?>"
                                                   name="<?php echo 'ph-' . $ph_arr_type . '-' . $pht . '-' . $placeholder['handle'] . '-color-rgba-slide'; ?>"
                                                   data-unchecked="off" <?php RevSliderFunctions::checked($get_from, 'on'); ?>>
                                            <input type="text"
                                                   name="<?php echo 'ph-' . $ph_arr_type . '-' . $pht . '-' . $placeholder['handle'] . '-color-rgba'; ?>"
                                                   class="my-alphacolor-field"
                                                   value="<?php echo $ph_vals['color-rgba']; ?>">
                                            <?php
                                            break;
                                        case 'font-family':
                                            $get_from_font_family = RevSliderFunctions::getVal($slideParams, 'ph-' . $ph_arr_type . '-' . $pht . '-' . $placeholder['handle'] . '-font_family-slide', 'off');
                                            ?>
                                            <label><?php echo $placeholder['title']; ?></label>
                                            <input type="checkbox" class="tp-moderncheckbox"
                                                   id="<?php echo 'ph-' . $ph_arr_type . '-' . $pht . '-' . $placeholder['handle'] . '-font_family-slide'; ?>"
                                                   name="<?php echo 'ph-' . $ph_arr_type . '-' . $pht . '-' . $placeholder['handle'] . '-font_family-slide'; ?>"
                                                   data-unchecked="off" <?php RevSliderFunctions::checked($get_from_font_family, 'on'); ?>>
                                            <select style="width: 140px;"
                                                    name="<?php echo 'ph-' . $ph_arr_type . '-' . $pht . '-' . $placeholder['handle'] . '-font_family'; ?>">
                                                <?php
                                                $font_families = $operations->getArrFontFamilys();
                                                foreach ($font_families as $handle => $name) {
                                                    if ($name['label'] == 'Dont Show Me') continue;

                                                    echo '<option value="' . RevSliderFunctions::esc_attr($name['label']) . '"';
                                                    if ($ph_vals['font_family'] == RevSliderFunctions::esc_attr($name['label'])) {
                                                        echo ' selected="selected"';
                                                    }
                                                    echo '>' . RevSliderFunctions::esc_attr($name['label']) . '</option>';
                                                }
                                                ?>
                                            </select>
                                            <?php
                                            break;
                                        case 'custom':
                                            $get_from_custom = RevSliderFunctions::getVal($slideParams, 'ph-' . $ph_arr_type . '-' . $pht . '-' . $placeholder['handle'] . '-custom-slide', 'off');
                                            ?>
                                            <label><?php echo $placeholder['title']; ?></label>
                                            <input type="checkbox" class="tp-moderncheckbox"
                                                   id="<?php echo 'ph-' . $ph_arr_type . '-' . $pht . '-' . $placeholder['handle'] . '-custom-slide'; ?>"
                                                   name="<?php echo 'ph-' . $ph_arr_type . '-' . $pht . '-' . $placeholder['handle'] . '-custom-slide'; ?>"
                                                   data-unchecked="off" <?php RevSliderFunctions::checked($get_from_custom, 'on'); ?>>
                                            <input type="text"
                                                   name="<?php echo 'ph-' . $ph_arr_type . '-' . $pht . '-' . $placeholder['handle'] . '-custom'; ?>"
                                                   value="<?php echo $ph_vals['custom']; ?>">
                                            <?php
                                            break;
                                    }
                                    ?>
                                </li>
                                <?php
                            }
                            ?>
                            <?php
                        }
                    }
                    break;
                }
            }
        }
        return ob_get_clean();
    }

    public static function html_id_esw(array $args)
    {
        $id_esw = new IdEsw();
        $url = array();
        $global = array(
            'html_idesw' => $id_esw->getTemplate($args)
        );
        return compact(array('url', 'global'));
    }
}

































