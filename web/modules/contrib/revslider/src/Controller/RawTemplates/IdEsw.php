<?php
/**
 * Created by FsFlex.
 * User: VH
 * Date: 7/25/2017
 * Time: 2:24 PM
 */

namespace Drupal\revslider\Controller\RawTemplates;


use Drupal\revslider\Helper\RevSliderFunctions;

class IdEsw
{
    public function getTemplate(array $args )
    {
        extract($args);
        //
        $slide_stage = new SlideStage();
        $slide_stage_html = $slide_stage->getTemplate($args_for_slide_stage);
        ob_start();
        ?>
    <div id="id-esw" class="<?php echo ($slide->isStaticSlide()) ? ' rev_static_layers' : ''; ?>">
        <?php
        echo $slide_stage_html;
//        require self::getPathTemplate('slide-stage');
        ?>
        <div style="width:100%;clear:both;height:20px"></div>

        <div id="dialog_insert_icon" class="dialog_insert_icon" title="Insert Icon" style="display:none;"></div>

        <div id="dialog_template_insert" class="dialog_template_help" title="<?php echo t('Insert Meta') ?>"
             style="display:none;">
            <ul class="rs-template-settings-tabs">
                <?php
                switch ($slider_type) {
                    case 'posts':
                    case 'specific_posts':
                    case 'current_post':
                    case 'flickr':
                        ?>
                        <li data-content="#slide-flickr-template-entry" class="selected"><i style="height:45px"
                                                                                            class="rs-mini-layer-icon eg-icon-flickr rs-toolbar-icon"></i><span><?php echo t('Flickr') ?></span>
                        </li>
                        <?php
                        break;
                    case 'instagram':
                        ?>
                        <li data-content="#slide-instagram-template-entry" class="selected"><i style="height:45px"
                                                                                               class="rs-mini-layer-icon eg-icon-info rs-toolbar-icon"></i><span><?php echo t('Instagram') ?></span>
                        </li>
                        <?php
                        break;
                    case 'twitter':
                        ?>
                        <li data-content="#slide-twitter-template-entry" class="selected"><i style="height:45px"
                                                                                             class="rs-mini-layer-icon eg-icon-twitter rs-toolbar-icon"></i><span><?php echo t('Twitter') ?></span>
                        </li>
                        <?php
                        break;
                    case 'facebook':
                        ?>
                        <li data-content="#slide-facebook-template-entry" class="selected"><i style="height:45px"
                                                                                              class="rs-mini-layer-icon eg-icon-facebook rs-toolbar-icon"></i><span><?php echo t('Facebook') ?></span>
                        </li>
                        <?php
                        break;
                    case 'youtube':
                        ?>
                        <li data-content="#slide-youtube-template-entry" class="selected"><i style="height:45px"
                                                                                             class="rs-mini-layer-icon eg-icon-youtube rs-toolbar-icon"></i><span><?php echo t('YouTube') ?></span>
                        </li>
                        <?php
                        break;
                    case 'vimeo':
                        ?>
                        <li data-content="#slide-vimeo-template-entry" class="selected"><i style="height:45px"
                                                                                           class="rs-mini-layer-icon eg-icon-vimeo rs-toolbar-icon"></i><span><?php echo t('Vimeo') ?></span>
                        </li>
                        <?php
                        break;
                    case 'gallery':
                        ?>
                        <li data-content="#slide-gallery-template-entry" class="selected"><i style="height:45px"
                                                                                             class="rs-mini-layer-icon eg-icon-picture rs-toolbar-icon"></i><span><?php echo t('General') ?></span>
                        </li>
                        <?php
                        break;
                }
                // Apply Filters for Tabs from Add-Ons
                //                do_action('rev_slider_insert_meta_tabs', array(
                //                        'dummy'       => '<li data-content="#slide-INSERT_TAB_SLUG-template-entry" class="selected"><i style="height:45px" class="rs-mini-layer-icon INSERT_ICON_CLASS rs-toolbar-icon"></i><span>INSERT_TAB_NAME</span></li>',
                //                        'slider_type' => $slider_type
                //                    )
                //                );
                if ($slider_type != "gallery") {
                    ?>
                    <li data-content="#slide-images-template-entry" class="selected">
                        <i style="height:45px"
                           class="rs-mini-layer-icon eg-icon-picture-1 rs-toolbar-icon"></i><span><?php echo t('Images') ?></span>
                    </li>
                <?php } ?>
            </ul>
            <div style="clear: both;"></div>
            <?php
            switch ($slider_type) {
                case 'posts':
                case 'specific_posts':
                case 'current_post':
                case 'flickr':
                    ?>
                    <table class="table_template_help" id="slide-flickr-template-entry" style="display: none;">
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('title')">{{title}}</a></td>
                            <td><?php echo t("Post Title") ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('content')">{{content}}</a></td>
                            <td><?php echo t("Post content") ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('content:words:10')">{{content:words:10}}</a>
                            </td>
                            <td><?php echo t("Post content limit by words") ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('content:chars:10')">{{content:chars:10}}</a>
                            </td>
                            <td><?php echo t("Post content limit by chars") ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('link')">{{link}}</a></td>
                            <td><?php echo t("The link to the post") ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('date')">{{date}}</a></td>
                            <td><?php echo t("Date created") ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('author_name')">{{author_name}}</a>
                            </td>
                            <td><?php echo t('Username') ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('views')">{{views}}</a></td>
                            <td><?php echo t('Views') ?></td>
                        </tr>
                    </table>
                    <table class="table_template_help" id="slide-images-template-entry" style="display: none;">
                        <?php
                        foreach ($img_sizes as $img_handle => $img_name) {
                            ?>
                            <tr>
                                <td>
                                    <a href="javascript:UniteLayersRev.insertTemplate('image_url_<?php echo RevSliderFunctions::sanitize_title($img_handle); ?>')">
                                        {{image_url_<?php echo RevSliderFunctions::sanitize_title($img_handle); ?>
                                        }}</a></td>
                                <td><?php echo t("Image URL") ?>
                                    <?php echo ' ' . $img_name; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="javascript:UniteLayersRev.insertTemplate('image_<?php echo RevSliderFunctions::sanitize_title($img_handle); ?>')">
                                        {{image_<?php echo RevSliderFunctions::sanitize_title($img_handle); ?> }}</a>
                                </td>
                                <td><?php echo t("Image &lt;img /&gt;") ?>
                                    <?php echo ' ' . $img_name; ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                    <?php
                    break;
                case 'instagram':
                    ?>
                    <table class="table_template_help" id="slide-instagram-template-entry" style="display: none;">
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('title')">{{title}}</a></td>
                            <td><?php echo t("Title") ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('content')">{{content}}</a></td>
                            <td><?php echo t("Content") ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('content:words:10')">{{content:words:10}}</a>
                            </td>
                            <td><?php echo t("Post content limit by words") ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('content:chars:10')">{{content:chars:10}}</a>
                            </td>
                            <td><?php echo t("Post content limit by chars") ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('link')">{{link}}</a></td>
                            <td><?php echo t("Link") ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('date')">{{date}}</a></td>
                            <td><?php echo t("Date created") ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('author_name')">{{author_name}}</a>
                            </td>
                            <td><?php echo t('Username') ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('likes')">{{likes}}</a></td>
                            <td><?php echo t('Number of Likes') ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('num_comments')">{{num_comments}}</a>
                            </td>
                            <td><?php echo t('Number of Comments') ?></td>
                        </tr>
                    </table>
                    <table class="table_template_help" id="slide-images-template-entry" style="display: none;">
                        <?php
                        foreach ($img_sizes as $img_handle => $img_name) {
                            ?>
                            <tr>
                                <td>
                                    <a href="javascript:UniteLayersRev.insertTemplate('image_url_<?php echo RevSliderFunctions::sanitize_title($img_handle); ?>')">
                                        {{image_url_<?php echo RevSliderFunctions::sanitize_title($img_handle); ?>
                                        }}</a></td>
                                <td><?php echo t("Image URL") ?>
                                    <?php echo ' ' . $img_name; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="javascript:UniteLayersRev.insertTemplate('image_<?php echo RevSliderFunctions::sanitize_title($img_handle); ?>')">
                                        {{image_<?php echo RevSliderFunctions::sanitize_title($img_handle); ?> }}</a>
                                </td>
                                <td><?php echo t("Image &lt;img /&gt;") ?>
                                    <?php echo ' ' . $img_name; ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                    <?php
                    break;
                case 'twitter':
                    ?>
                    <table class="table_template_help" id="slide-twitter-template-entry" style="display: none;">
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('title')">{{title}}</a></td>
                            <td><?php echo t('Title') ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('content')">{{content}}</a></td>
                            <td><?php echo t('Content') ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('content:words:10')">{{content:words:10}}</a>
                            </td>
                            <td><?php echo t("Post content limit by words") ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('content:chars:10')">{{content:chars:10}}</a>
                            </td>
                            <td><?php echo t("Post content limit by chars") ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('link')">{{link}}</a></td>
                            <td><?php echo t("Link") ?></td>
                        </tr>
                        <tr>
                            <td>
                                <a href="javascript:UniteLayersRev.insertTemplate('date_published')">{{date_published}}</a>
                            </td>
                            <td><?php echo t('Pulbishing Date') ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('author_name')">{{author_name}}</a>
                            </td>
                            <td><?php echo t('Username') ?></td>
                        </tr>
                        <tr>
                            <td>
                                <a href="javascript:UniteLayersRev.insertTemplate('retweet_count')">{{retweet_count}}</a>
                            </td>
                            <td><?php echo t('Retweet Count') ?></td>
                        </tr>
                        <tr>
                            <td>
                                <a href="javascript:UniteLayersRev.insertTemplate('favorite_count')">{{favorite_count}}</a>
                            </td>
                            <td><?php echo t('Favorite Count') ?></td>
                        </tr>
                    </table>
                    <table class="table_template_help" id="slide-images-template-entry" style="display: none;">
                        <?php
                        foreach ($img_sizes as $img_handle => $img_name) {
                            ?>
                            <tr>
                                <td>
                                    <a href="javascript:UniteLayersRev.insertTemplate('image_url_<?php echo RevSliderFunctions::sanitize_title($img_handle); ?>')">
                                        {{image_url_<?php echo RevSliderFunctions::sanitize_title($img_handle); ?>
                                        }}</a></td>
                                <td><?php echo t("Image URL") ?>
                                    <?php echo ' ' . $img_name; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="javascript:UniteLayersRev.insertTemplate('image_<?php echo RevSliderFunctions::sanitize_title($img_handle); ?>')">
                                        {{image_<?php echo RevSliderFunctions::sanitize_title($img_handle); ?> }}</a>
                                </td>
                                <td><?php echo t("Image &lt;img /&gt;") ?>
                                    <?php echo ' ' . $img_name; ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                    <?php
                    break;
                case 'facebook':
                    ?>
                    <table class="table_template_help" id="slide-facebook-template-entry" style="display: none;">
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('title')">{{title}}</a></td>
                            <td><?php echo t('Title') ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('content')">{{content}}</a></td>
                            <td><?php echo t('Content') ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('content:words:10')">{{content:words:10}}</a>
                            </td>
                            <td><?php echo t("Post content limit by words") ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('content:chars:10')">{{content:chars:10}}</a>
                            </td>
                            <td><?php echo t("Post content limit by chars") ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('link')">{{link}}</a></td>
                            <td><?php echo t('Link') ?></td>
                        </tr>
                        <tr>
                            <td>
                                <a href="javascript:UniteLayersRev.insertTemplate('date_published')">{{date_published}}</a>
                            </td>
                            <td><?php echo t('Pulbishing Date') ?></td>
                        </tr>
                        <tr>
                            <td>
                                <a href="javascript:UniteLayersRev.insertTemplate('date_published')">{{date_modified}}</a>
                            </td>
                            <td><?php echo t('Last Modify Date') ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('author_name')">{{author_name}}</a>
                            </td>
                            <td><?php echo t('Username') ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('likes')">{{likes}}</a></td>
                            <td><?php echo t('Number of Likes') ?></td>
                        </tr>
                    </table>
                    <table class="table_template_help" id="slide-images-template-entry" style="display: none;">
                        <?php
                        foreach ($img_sizes as $img_handle => $img_name) {
                            ?>
                            <tr>
                                <td>
                                    <a href="javascript:UniteLayersRev.insertTemplate('image_url_<?php echo RevSliderFunctions::sanitize_title($img_handle); ?>')">{{image_url_<?php echo RevSliderFunctions::sanitize_title($img_handle); ?>
                                        }}</a></td>
                                <td><?php echo t("Image URL") ?>
                                    <?php echo ' ' . $img_name; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="javascript:UniteLayersRev.insertTemplate('image_<?php echo RevSliderFunctions::sanitize_title($img_handle); ?>')">{{image_<?php echo RevSliderFunctions::sanitize_title($img_handle); ?>
                                        }}</a></td>
                                <td><?php echo t("Image &lt;img /&gt;") ?>
                                    <?php echo ' ' . $img_name; ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                    <?php
                    break;
                case 'youtube':
                    ?>
                    <table class="table_template_help" id="slide-youtube-template-entry" style="display: none;">
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('title')">{{title}}</a></td>
                            <td><?php echo t('Title') ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('excerpt')">{{excerpt}}</a></td>
                            <td><?php echo t('Excerpt') ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('content')">{{content}}</a></td>
                            <td><?php echo t('Content') ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('content:words:10')">{{content:words:10}}</a>
                            </td>
                            <td><?php echo t("Post content limit by words") ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('content:chars:10')">{{content:chars:10}}</a>
                            </td>
                            <td><?php echo t("Post content limit by chars") ?></td>
                        </tr>
                        <tr>
                            <td>
                                <a href="javascript:UniteLayersRev.insertTemplate('date_published')">{{date_published}}</a>
                            </td>
                            <td><?php echo t('Pulbishing Date') ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('link')">{{link}}</a></td>
                            <td><?php echo t('Link') ?></td>
                        </tr>
                    </table>
                    <table class="table_template_help" id="slide-images-template-entry" style="display: none;">
                        <?php
                        foreach ($img_sizes as $img_handle => $img_name) {
                            ?>
                            <tr>
                                <td>
                                    <a href="javascript:UniteLayersRev.insertTemplate('image_url_<?php echo RevSliderFunctions::sanitize_title($img_handle); ?>')">{{image_url_<?php echo RevSliderFunctions::sanitize_title($img_handle); ?>
                                        }}</a></td>
                                <td><?php echo t("Image URL") ?>
                                    <?php echo ' ' . $img_name; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="javascript:UniteLayersRev.insertTemplate('image_<?php echo RevSliderFunctions::sanitize_title($img_handle); ?>')">{{image_<?php echo RevSliderFunctions::sanitize_title($img_handle); ?>
                                        }}</a></td>
                                <td><?php echo t("Image &lt;img /&gt;") ?>
                                    <?php echo ' ' . $img_name; ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                    <?php
                    break;
                case 'gallery':
                    ?>
                    <table class="table_template_help" id="slide-gallery-template-entry" style="display: none;">
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('current_page_link')">{{current_page_link}}</a>
                            </td>
                            <td><?php echo t('Link to current page') ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('home_url')">{{home_url}}</a></td>
                            <td><?php echo t('Link to Home Page') ?></td>
                        </tr>
                        <?php // do_action('rev_slider_insert_gallery_meta_row');
                        ?>
                    </table>
                    <?php
                    break;
                case 'vimeo':
                    ?>
                    <table class="table_template_help" id="slide-vimeo-template-entry" style="display: none;">
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('title')">{{title}}</a></td>
                            <td><?php echo t('Title') ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('excerpt')">{{excerpt}}</a></td>
                            <td><?php echo t('Excerpt') ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('content')">{{content}}</a></td>
                            <td><?php echo t('Content') ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('content:words:10')">{{content:words:10}}</a>
                            </td>
                            <td><?php echo t("Post content limit by words") ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('content:chars:10')">{{content:chars:10}}</a>
                            </td>
                            <td><?php echo t("Post content limit by chars") ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('link')">{{link}}</a></td>
                            <td><?php echo t('The link to the post') ?></td>
                        </tr>
                        <tr>
                            <td>
                                <a href="javascript:UniteLayersRev.insertTemplate('date_published')">{{date_published}}</a>
                            </td>
                            <td><?php echo t('Pulbishing Date') ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('author_name')">{{author_name}}</a>
                            </td>
                            <td><?php echo t('Username') ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('likes')">{{likes}}</a></td>
                            <td><?php echo t('Number of Likes') ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('views')">{{views}}</a></td>
                            <td><?php echo t('Number of Views') ?></td>
                        </tr>
                        <tr>
                            <td><a href="javascript:UniteLayersRev.insertTemplate('num_comments')">{{num_comments}}</a>
                            </td>
                            <td><?php echo t('Number of Comments') ?></td>
                        </tr>
                    </table>
                    <table class="table_template_help" id="slide-images-template-entry" style="display: none;">
                        <?php
                        foreach ($img_sizes as $img_handle => $img_name) {
                            ?>
                            <tr>
                                <td>
                                    <a href="javascript:UniteLayersRev.insertTemplate('image_url_<?php echo RevSliderFunctions::sanitize_title($img_handle); ?>')">{{image_url_<?php echo RevSliderFunctions::sanitize_title($img_handle); ?>
                                        }}</a></td>
                                <td><?php echo t("Image URL") ?>
                                    <?php echo ' ' . $img_name; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="javascript:UniteLayersRev.insertTemplate('image_<?php echo RevSliderFunctions::sanitize_title($img_handle); ?>')">{{image_<?php echo RevSliderFunctions::sanitize_title($img_handle); ?>
                                        }}</a></td>
                                <td><?php echo t("Image &lt;img /&gt;") ?>
                                    <?php echo ' ' . $img_name; ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                    <?php
                    break;
            }
            // Apply Filters for Tab Content from Add-Ons
            //            do_action('rev_slider_insert_meta_tab_content', array(
            //                    'tab_head'    => '<table class="table_template_help" id="slide-INSERT_TAB_SLUG-template-entry" style="display: none;">',
            //                    'tab_row'     => '<tr><td><a href="javascript:UniteLayersRev.insertTemplate(\'INSERT_META_SLUG\')">{{INSERT_META_SLUG}}</a></td><td>INSERT_META_NAME</td></tr>',
            //                    'tab_foot'    => '</table>',
            //                    'slider_type' => $slider_type
            //                )
            //            );
            ?>
            <script type="text/javascript">
                document.addEventListener("DOMContentLoaded", function () {
                    jQuery('.rs-template-settings-tabs li').click(function () {
                        var tw = jQuery('.rs-template-settings-tabs .selected'),
                            tn = jQuery(this);
                        jQuery(tw.data('content')).hide(0);
                        tw.removeClass("selected");
                        tn.addClass("selected");
                        jQuery(tn.data('content')).show(0);
                    });
                    jQuery('.rs-template-settings-tabs li:first-child').click();
                });
            </script>
        </div>

        <div id="dialog_advanced_css" class="dialog_advanced_css" title="<?php echo t('Advanced CSS') ?>"
             style="display:none;">
            <div style="display: none;"><span id="rev-example-style-layer">example</span></div>
            <div id="change_acea_wrappers">
                <div id="change_acea_toidle" class="revblue button-primary"><?php echo t('Edit Idle') ?></div>
                <div id="change_acea_tohover"
                     class="revblue button-primary"><?php echo t('Edit Hover') ?></div>
            </div>
            <div class="first-css-area">
                <span class="cbi-title"><?php echo t('Style from options') ?><span
                            class="acsa_idle_or_hover"></span><span
                            style="font-size:11px;font-style:italic;display:block;line-height:13px">(<?php echo t('Editable via Option Fields, Saved in the Class:') ?>
                        <span class="current-advance-edited-class"></span>)</span></span>
                <textarea id="textarea_template_css_editor_uneditable" rows="20" cols="81"
                          disabled="disabled"></textarea>
            </div>
            <div class="second-css-area">
                <span class="cbi-title"><?php echo t('Additional Custom Styling') ?><span
                            class="acsa_idle_or_hover"></span><span
                            style="font-size:11px;font-style:italic;display:block;line-height:13px">(<?php echo t('Appended in the Class:') ?>
                        <span class="current-advance-edited-class"></span>)</span></span>
                <textarea id="textarea_advanced_css_editor" rows="20" cols="81"></textarea>
            </div>
        </div>

        <div id="dialog_save_as_css" class="dialog_save_as_css" title="<?php echo t('Save As') ?>"
             style="display:none;">
            <div style="margin-top:14px">
                <span style="margin-right:15px"><?php echo t('Save As:') ?></span><input id="rs-save-as-css"
                                                                                         type="text"
                                                                                         name="rs-save-as-css"
                                                                                         value=""/>
            </div>
        </div>

        <div id="dialog_rename_css" class="dialog_rename_css" title="<?php echo t('Rename CSS') ?>"
             style="display:none;">
            <div style="margin-top:14px">
                <span style="margin-right:15px"><?php echo t('Rename to:') ?></span><input id="rs-rename-css"
                                                                                           type="text"
                                                                                           name="rs-rename-css"
                                                                                           value=""/>
            </div>
        </div>

        <div id="dialog_advanced_layer_css" class="dialog_advanced_layer_css"
             title="<?php echo t('Layer Inline CSS') ?>" style="display:none;">
            <div id="change_ace_wrappers">
                <div id="change_ace_toidle" class="revblue button-primary"><?php echo t('Edit Idle') ?></div>
                <div id="change_ace_tohover"
                     class="revblue button-primary"><?php echo t('Edit Hover') ?></div>
            </div>
            <div class="first-css-area">
                <span class="cbi-title"><?php echo t('Advanced Custom Styling') ?><span
                            id="acs_idle_or_hover"></span><span
                            style="font-size:11px;font-style:italic;display:block;line-height:13px">(<?php echo t('Appended Inline to the Layer Markup') ?>
                        )</span></span>
                <textarea id="textarea_template_css_editor_layer" name="textarea_template_css_editor_layer"></textarea>
            </div>
        </div>

        <div id="dialog_save_as_animation" class="dialog_save_as_animation" title="<?php echo t('Save As') ?>"
             style="display:none;">
            <div style="margin-top:14px">
                <span style="margin-right:15px"><?php echo t('Save As:') ?></span><input
                        id="rs-save-as-animation" type="text" name="rs-save-as-animation" value=""/>
            </div>
        </div>

        <div id="dialog_save_animation" class="dialog_save_animation" title="<?php echo t('Save Under') ?>"
             style="display:none;">
            <div style="margin-top:14px">
                <span style="margin-right:15px"><?php echo t('Save Under:') ?></span><input
                        id="rs-save-under-animation" type="text" name="rs-save-under-animation" value=""/>
            </div>
        </div>

        <script type="text/javascript">
            <?php
            $icon_sets = RevSliderFunctions::get_icon_sets();
            $sets = '';
            if (!empty($icon_sets)) {
                $sets = implode("','", $icon_sets);
            }
            ?>

            var rs_icon_sets = new Array('<?php echo $sets; ?>');


            document.addEventListener("DOMContentLoaded", function () {

                UniteLayersRev.addPreventLeave();

                <?php if(!empty($jsonLayers)){ ?>
                //set init layers object
                UniteLayersRev.setInitLayersJson(<?php echo $jsonLayers; ?>);
                <?php } ?>


                <?php
                if($slide->isStaticSlide()){
                $arrayDemoLayers = array();
                $arrayDemoSettings = array();
                if (!empty($all_slides) && is_array($all_slides)) {
                    foreach ($all_slides as $cSlide) {
                        $arrayDemoLayers[$cSlide->getID()] = $cSlide->getLayers();
                        $arrayDemoSettings[$cSlide->getID()] = $cSlide->getParams();
                    }
                }
                $jsonDemoLayers = RevSliderFunctions::jsonEncodeForClientSide($arrayDemoLayers);
                $jsonDemoSettings = RevSliderFunctions::jsonEncodeForClientSide($arrayDemoSettings);
                ?>
                //set init demo layers object
                UniteLayersRev.setInitDemoLayersJson(<?php echo $jsonDemoLayers; ?>);
                UniteLayersRev.setInitDemoSettingsJson(<?php echo $jsonDemoSettings; ?>);
                <?php
                } ?>
                <?php if(!empty($jsonStaticLayers)){ ?>
                UniteLayersRev.setInitStaticLayersJson(<?php echo $jsonStaticLayers; ?>);
                <?php } ?>

                <?php if(!empty($jsonCaptions)){ ?>
                UniteLayersRev.setInitCaptionClasses(<?php echo $jsonCaptions; ?>);
                <?php } ?>

                <?php if(!empty($arrCustomAnim)){ ?>
                UniteLayersRev.setInitLayerAnim(<?php echo $arrCustomAnim; ?>);
                <?php } ?>

                <?php if(!empty($arrCustomAnimDefault)){ ?>
                UniteLayersRev.setInitLayerAnimsDefault(<?php echo $arrCustomAnimDefault; ?>);
                <?php } ?>

                <?php if(!empty($jsonFontFamilys)){ ?>
                UniteLayersRev.setInitFontTypes(<?php echo $jsonFontFamilys; ?>);
                <?php } ?>

                <?php if(!empty($arrCssStyles)){ ?>
                UniteCssEditorRev.setInitCssStyles(<?php echo $arrCssStyles; ?>);
                <?php } ?>

                <?php
                $trans_sizes = RevSliderFunctions::jsonEncodeForClientSide($slide->translateIntoSizes());
                ?>
                UniteLayersRev.setInitTransSetting(<?php echo $trans_sizes; ?>);
                setTimeout(function () {
                    UniteLayersRev.init("<?php echo $slideDelay; ?>");
                },100);


                UniteCssEditorRev.init();


                RevSliderAdmin.initGlobalStyles();

                RevSliderAdmin.initLayerPreview();

                RevSliderAdmin.setStaticCssCaptionsUrl('<?php echo RevSliderFunctions::asset('public/assets/css/static-captions.css'); ?>');


                <?php if($kenburn_effect == 'on'){ ?>
                jQuery('input[name="kenburn_effect"]:checked').change();
                <?php } ?>


                // DRAW  HORIZONTAL AND VERTICAL LINEAR
                var horl = jQuery('#hor-css-linear .linear-texts'),
                    verl = jQuery('#ver-css-linear .linear-texts'),
                    maintimer = jQuery('#mastertimer-linear .linear-texts'),
                    mw = "<?php echo $tempwidth_jq; ?>";
                mw = parseInt(mw.split(":")[1], 0);

                for (var i = -600; i < mw; i = i + 100) {
                    if (mw - i < 100)
                        horl.append('<li style="width:' + (mw - i) + 'px"><span>' + i + '</span></li>');
                    else
                        horl.append('<li><span>' + i + '</span></li>');
                }

                for (var i = 0; i < 2000; i = i + 100) {
                    verl.append('<li><span>' + i + '</span></li>');
                }

                for (var i = 0; i < 160; i = i + 1) {
                    var txt = i + "s";

                    maintimer.append('<li><span>' + txt + '</span></li>');
                }

                // SHIFT RULERS and TEXTS and HELP LINES//
                function horRuler() {
                    var dl = jQuery('#divLayers'),
                        l = parseInt(dl.offset().left, 0) - parseInt(jQuery('#thelayer-editor-wrapper').offset().left, 0);
                    jQuery('#hor-css-linear').css({backgroundPosition: (l) + "px 50%"});
                    jQuery('#hor-css-linear .linear-texts').css({left: (l - 595) + "px"});
                    jQuery('#hor-css-linear .helplines-offsetcontainer').css({left: (l) + "px"});

                    jQuery('#ver-css-linear .helplines').css({left: "-15px"}).width(jQuery('#thelayer-editor-wrapper').outerWidth(true) - 35);
                    jQuery('#hor-css-linear .helplines').css({top: "-15px"}).height(jQuery('#thelayer-editor-wrapper').outerHeight(true) - 41);
                }

                horRuler();


                jQuery('.my-color-field').tpColorPicker({
                    defaultValue: '#FFFFFF',
                    mode: 'full',
                    wrapper: '<span class="rev-colorpickerspan"></span>',
                    cancel: function () {
                        jQuery('#style_form_wrapper').trigger("colorchanged");
                    },

                    onEdit: function (inputElement, color, gradientObj) {
                        switch (inputElement.attr('name')) {

                            case "adbutton-color-1":
                            case "adbutton-color-2":
                            case "adbutton-border-color":
                                setExampleButtons();
                                break;

                            case "adshape-color-1":
                            case "adshape-color-2":
                            case "adshape-border-color":
                                setExampleShape();
                                break;
                            case "bg_color":
                                if (color.length > 7) {
                                    jQuery("#divbgholder").css("background", color);
                                    jQuery('.slotholder .tp-bgimg.defaultimg').css({background: color});
                                    jQuery('#slide_selector .list_slide_links li.selected .slide-media-container ').css({background: color});
                                } else {
                                    jQuery("#divbgholder").css("background-color", color);
                                    jQuery('.slotholder .tp-bgimg.defaultimg').css({backgroundColor: color});
                                    jQuery('#slide_selector .list_slide_links li.selected .slide-media-container ').css({backgroundColor: color});
                                }

                                break;
                        }


                        var layer = jQuery('.layer_selected.slide_layer');
                        if (layer.length > 0) {
                            switch (inputElement.attr('name')) {
                                case "color_static":
                                case "hover_color_static":
                                    if (layer.hasClass("slide_layer_type_text"))
                                        punchgs.TweenLite.set(layer.find('>.tp-caption'), {color: color});
                                    else if (layer.hasClass("slide_layer_type_svg"))
                                        punchgs.TweenLite.set(layer.find('>.tp-caption>svg, >.tp-caption>svg path'), {fill: color});
                                    break;
                                case "css_svgstroke-color-show":
                                case "css_svgstroke-hover-color-show":
                                    if (layer.hasClass("slide_layer_type_svg"))
                                        punchgs.TweenLite.set(layer.find('>.tp-caption>svg'), {stroke: color});
                                    break;
                                case "css_background-color":
                                case "hover_css_background-color":
                                    jQuery('#style_form_wrapper').trigger("colorchanged");
                                    if (color.indexOf('gradient') >= 0)
                                        punchgs.TweenLite.set(layer.find('>.tp-caption'), {background: color});
                                    else
                                        punchgs.TweenLite.set(layer.find('>.tp-caption'), {backgroundColor: color});
                                    break;
                                case "css_border-color-show":
                                case "hover_css_border-color-show":
                                    punchgs.TweenLite.set(layer.find('>.tp-caption'), {borderColor: color});
                                    break;
                            }
                        }

                    },

                    change: function (inputElement, color, gradientObj) {

                        switch (inputElement.attr('name')) {
                            case "adbutton-color-1":
                            case "adbutton-color-2":
                            case "adbutton-border-color":
                                setExampleButtons();
                                break;

                            case "adshape-color-1":
                            case "adshape-color-2":
                            case "adshape-border-color":
                                setExampleShape();
                                break;
                            case "bg_color":
                                var bgColor = jQuery("#slide_bg_color").val();
                                if (bgColor.length > 7) {
                                    jQuery("#divbgholder").css("background", bgColor);
                                    jQuery('.slotholder .tp-bgimg.defaultimg').css({background: bgColor});
                                    jQuery('#slide_selector .list_slide_links li.selected .slide-media-container ').css({background: bgColor});
                                } else {
                                    jQuery("#divbgholder").css("background-color", bgColor);
                                    jQuery('.slotholder .tp-bgimg.defaultimg').css({backgroundColor: bgColor});
                                    jQuery('#slide_selector .list_slide_links li.selected .slide-media-container ').css({backgroundColor: bgColor});
                                }

                                break;
                        }
                        jQuery('#style_form_wrapper').trigger("colorchanged");

                    }
                });


                jQuery('.adb-input').on("change blur focus", setExampleButtons);
                jQuery('.ads-input, input[name="shape_fullwidth"], input[name="shape_fullheight"]').on("change blur focus", setExampleShape);
                jQuery('.ui-autocomplete').on('click', setExampleButtons);

                jQuery('.wp-color-result').on("click", function () {

                    if (jQuery(this).hasClass("wp-picker-open"))
                        jQuery(this).closest('.wp-picker-container').addClass("pickerisopen");
                    else
                        jQuery(this).closest('.wp-picker-container').removeClass("pickerisopen");
                });

                jQuery("body").click(function (event) {
                    jQuery('.wp-picker-container.pickerisopen').removeClass("pickerisopen");
                })

                // WINDOW RESIZE AND SCROLL EVENT SHOULD REDRAW RULERS
                jQuery(window).resize(horRuler);
                jQuery('#divLayers-wrapper').on('scroll', horRuler);


                jQuery('#toggle-idle-hover .icon-stylehover').click(function () {
                    var bt = jQuery('#toggle-idle-hover');
                    bt.removeClass("idleisselected").addClass("hoverisselected");
                    jQuery('#tp-idle-state-advanced-style').hide();
                    jQuery('#tp-hover-state-advanced-style').show();
                });

                jQuery('#toggle-idle-hover .icon-styleidle').click(function () {
                    var bt = jQuery('#toggle-idle-hover');
                    bt.addClass("idleisselected").removeClass("hoverisselected");
                    jQuery('#tp-idle-state-advanced-style').show();
                    jQuery('#tp-hover-state-advanced-style').hide();
                });


                jQuery('input[name="hover_allow"]').on("change", function () {
                    if (jQuery(this).attr("checked") == "checked") {
                        jQuery('#idle-hover-swapper').show();
                    } else {
                        jQuery('#idle-hover-swapper').hide();
                    }
                });


                // HIDE /SHOW  INNER SAVE,SAVE AS ETC..
                jQuery('.clicktoshowmoresub').click(function () {
                    jQuery(this).find('.clicktoshowmoresub_inner').show();
                });

                jQuery('.clicktoshowmoresub').on('mouseleave', function () {
                    jQuery(this).find('.clicktoshowmoresub_inner').hide();
                });

                //arrowRepeater();
                function arrowRepeater() {
                    var tw = new punchgs.TimelineLite();
                    tw.add(punchgs.TweenLite.from(jQuery('.animatemyarrow'), 0.5, {x: -10, opacity: 0}), 0);
                    tw.add(punchgs.TweenLite.to(jQuery('.animatemyarrow'), 0.5, {x: 10, opacity: 0}), 0.5);

                    tw.play(0);
                    tw.eventCallback("onComplete", function () {
                        tw.restart();
                    })
                }

                RevSliderSettings.createModernOnOff();

            });


        </script>

        </div><?php

        return ob_get_clean();
    }
}