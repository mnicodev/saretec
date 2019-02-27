<?php
/**
 * Created by FsFlex.
 * User: VH
 * Date: 7/31/2017
 * Time: 4:51 PM
 */

namespace Drupal\revslider\Controller\RawTemplates\SliderMainOptions;


class ContentSourceSB
{
    public function getTemplate()
    {
        ob_start();?>
        <div id="content_source_sb" class="setting_box">
            <h3><span class="setting-step-number">1</span><span><?php echo t("Content Source") ?></span></h3>
            <div class="inside" style="padding:0px;">
                <div class="source-selector-wrapper">
                    <?php $source_type = RevSliderFunctions::getVal($arrFieldsParams, 'source_type', 'gallery'); ?>
                    <span class="rs-source-selector selected">
								<span class="rs-source-image rssi-default"></span>
								<input type="radio" id="source_type_3" value="gallery"
                                       name="source_type" <?php RevSliderFunctions::checked($source_type, 'gallery'); ?> />
								<span class="rs-source-label"><?php echo t('Default Slider') ?></span>
							</span>
                    <span class="rs-source-selector">
								<span class="rs-source-image rssi-post"></span>
								<input type="radio" id="source_type_1" value="posts"
                                       name="source_type" <?php RevSliderFunctions::checked($source_type, 'posts'); ?> />
								<span class="rs-source-label"><?php echo t('Post-Based Slider') ?></span>
							</span>
                    <span class="rs-source-selector">
								<span class="rs-source-image rssi-post"></span>
								<input type="radio" id="source_type_2" value="specific_posts"
                                       name="source_type" <?php RevSliderFunctions::checked($source_type, 'specific_posts'); ?> />
								<span class="rs-source-label"><?php echo t('Specific Posts') ?></span>
							</span>
                    <span class="rs-source-selector">
								<span class="rs-source-image rssi-post"></span>
								<input type="radio" id="source_type_10" value="current_post"
                                       name="source_type" <?php RevSliderFunctions::checked($source_type, 'current_post'); ?> />
								<span class="rs-source-label"><?php echo t('Current Post/Page') ?></span>
							</span>
                    <span class="rs-source-selector">
								<span class="rs-source-image rssi-flickr"></span>
								<input type="radio" id="source_type_3" value="flickr"
                                       name="source_type" <?php RevSliderFunctions::checked($source_type, 'flickr'); ?> />
								<span class="rs-source-label"><?php echo t('Flickr Stream') ?></span>
							</span>
                    <span class="rs-source-selector">
								<span class="rs-source-image rssi-instagram"></span>
								<input type="radio" id="source_type_4" value="instagram"
                                       name="source_type" <?php RevSliderFunctions::checked($source_type, 'instagram'); ?> />
								<span class="rs-source-label"><?php echo t('Instagram Stream') ?></span>
							</span>
                    <span class="rs-source-selector">
								<span class="rs-source-image rssi-woo"></span>
								<input type="radio" id="source_type_5" value="woocommerce"
                                       name="source_type" <?php RevSliderFunctions::checked($source_type, 'woocommerce'); ?> />
								<span class="rs-source-label"><?php echo t('Woo Commerce Slider') ?></span>
							</span>
                    <span class="rs-source-selector">
								<span class="rs-source-image rssi-twitter"></span>
								<input type="radio" id="source_type_6" value="twitter"
                                       name="source_type" <?php RevSliderFunctions::checked($source_type, 'twitter'); ?> />
								<span class="rs-source-label"><?php echo t('Twitter Stream') ?></span>
							</span>
                    <span class="rs-source-selector">
								<span class="rs-source-image rssi-facebook"></span>
								<input type="radio" id="source_type_7" value="facebook"
                                       name="source_type" <?php RevSliderFunctions::checked($source_type, 'facebook'); ?> />
								<span class="rs-source-label"><?php echo t('Facebook Stream') ?></span>
							</span>
                    <span class="rs-source-selector">
								<span class="rs-source-image rssi-youtube"></span>
								<input type="radio" id="source_type_8" value="youtube"
                                       name="source_type" <?php RevSliderFunctions::checked($source_type, 'youtube'); ?> />
								<span class="rs-source-label"><?php echo t('YouTube Stream') ?></span>
							</span>
                    <span class="rs-source-selector">
								<span class="rs-source-image rssi-vimeo"></span>
								<input type="radio" id="source_type_9" value="vimeo"
                                       name="source_type" <?php RevSliderFunctions::checked($source_type, 'vimeo'); ?> />
								<span class="rs-source-label"><?php echo t('Vimeo Stream') ?></span>
							</span>
                    <span class="tp-clearfix"></span>
                </div>


                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        function rsSelectorFun() {
                            jQuery('.rs-source-selector').removeClass("selected");
                            jQuery('.source-selector-wrapper input:checked').closest(".rs-source-selector").addClass("selected");
                        }

                        jQuery('.source-selector-wrapper input').change(rsSelectorFun);
                        rsSelectorFun();
                    })
                </script>
            </div>

            <div id="rs-instagram-settings-wrapper" class="rs-settings-wrapper">
                <div style="width:50%;display:block;float:left;">
                    <span class="rev-new-label"><?php echo t('Slides (max 20)') ?></span>
                    <input type="text"
                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'instagram-count', ''); ?>"
                           name="instagram-count"
                           title="<?php echo t('Display this number of photos') ?>">
                    <p>
                        <span class="rev-new-label"><?php echo t('Cache (sec)') ?></span>
                        <input type="text"
                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'instagram-transient', '1200'); ?>"
                               name="instagram-transient"
                               title="<?php echo t('Cache the result') ?>">
                    </p>
                </div>
                <div style="width:50%;display:block;float:left;">
                    <span class="rev-new-label"><?php echo t('Source') ?></span>
                    <select name="instagram-type">
                        <option value="user"
                                title="<?php echo t('Display a user\'s public photos') ?>"
                                selected> <?php echo t('User Public Photos') ?></option>
                    </select>
                    <div id="instagram_user">
                        <p>
                            <span class="rev-new-label"><?php echo t('Instagram User Name') ?></span>
                            <input type="text"
                                   value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'instagram-user-id', ''); ?>"
                                   name="instagram-user-id"
                                   title="<?php echo t('Put in the Instagram User Name') ?>">
                        </p>
                    </div>
                    <div id="instagram_hash">
                        <p>
                            <span class="rev-new-label"><?php echo t('Instagram Hashtag') ?></span>
                            <input type="text"
                                   value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'instagram-hash-tag', ''); ?>"
                                   name="instagram-hash-tag"
                                   title="<?php echo t('Put in one Instagram Hashtag') ?>">
                        </p>
                    </div>
                </div>
            </div>

            <div id="rs-flickr-settings-wrapper" class="rs-settings-wrapper">
                <div style="width:50%;display:block;float:left;">
                    <span class="rev-new-label"><?php echo t('Slides (max 500)') ?></span>
                    <input type="text"
                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'flickr-count', ''); ?>"
                           name="flickr-count"
                           title="<?php echo t('Display this number of photos') ?>">
                    <p>
                        <span class="rev-new-label"><?php echo t('Cache (sec)') ?></span>
                        <input type="text"
                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'flickr-transient', '1200'); ?>"
                               name="flickr-transient"
                               title="<?php echo t('Cache results for x seconds') ?>">
                    </p>
                    <p>
                        <span class="rev-new-label"><?php echo t('Flickr API Key') ?></span>
                        <input type="text"
                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'flickr-api-key', ''); ?>"
                               name="flickr-api-key"
                               title="<?php echo t('Put in your Flickr API Key') ?>">
                    </p>
                    <span class="description"><?php echo t('Read <a target="_blank" href="http://weblizar.com/get-flickr-api-key/">here</a> how to receive your Flickr API key') ?></span>
                </div>
                <div style="width:50%;display:block;float:left;">
                    <span class="rev-new-label"><?php echo t('Source') ?></span>
                    <select name="flickr-type">
                        <option value="publicphotos"
                                title="<?php echo t('Display a user\'s public photos') ?>" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'flickr-type', 'publicphotos'), 'publicphotos'); ?>> <?php echo t('User Public Photos') ?></option>
                        <option value="photosets"
                                title="<?php echo t('Display a certain photoset from a user') ?>"<?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'flickr-type', 'publicphotos'), 'photosets'); ?>> <?php echo t('User Photoset') ?></option>
                        <option value="gallery"
                                title="<?php echo t('Display a gallery') ?>"<?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'flickr-type', 'publicphotos'), 'gallery'); ?>> <?php echo t('Gallery') ?></option>
                        <option value="group"
                                title="<?php echo t('Display a group\'s photos') ?>"<?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'flickr-type', 'publicphotos'), 'group'); ?>> <?php echo t('Groups\' Photos') ?></option>
                    </select>
                    <div id="flickr-publicphotos-url-wrap">
                        <p>
                            <span class="rev-new-label"><?php echo t('Flickr User Url') ?></span>
                            <input type="text"
                                   value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'flickr-user-url'); ?>"
                                   name="flickr-user-url"
                                   title="<?php echo t('Put in the URL of the flickr User') ?>">
                        </p>
                    </div>
                    <div id="flickr-photosets-wrap">
                        <p>
                            <span class="rev-new-label"><?php echo t('Select Photoset') ?></span>
                            <input type="hidden" name="flickr-photoset"
                                   value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'flickr-photoset', ''); ?>">
                            <select name="flickr-photoset-select"
                                    title="<?php echo t('Select the photoset to pull the data from ') ?>">
                            </select>
                        </p>
                    </div>
                    <div id="flickr-gallery-url-wrap">
                        <p>
                            <span class="rev-new-label"><?php echo t('Flickr Gallery Url') ?></span>
                            <input type="text"
                                   value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'flickr-gallery-url'); ?>"
                                   name="flickr-gallery-url"
                                   title="<?php echo t('Put in the URL of the flickr Gallery') ?>">
                        </p>
                    </div>
                    <div id="flickr-group-url-wrap">
                        <p>
                            <span class="rev-new-label"><?php echo t('Flickr Group Url') ?></span>
                            <input type="text"
                                   value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'flickr-group-url'); ?>"
                                   name="flickr-group-url"
                                   title="<?php echo t('Put in the URL of the flickr Group') ?>">
                        </p>
                    </div>
                </div>
            </div>

            <div id="rs-facebook-settings-wrapper" class="rs-settings-wrapper">
                <div style="width:50%;display:block;float:left;">
                    <span class="rev-new-label"><?php echo t('Slides (max 25)') ?></span>
                    <input type="text"
                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'facebook-count', ''); ?>"
                           name="facebook-count"
                           title="<?php echo t('Display this number of posts') ?>">
                    <p>
                        <span class="rev-new-label"><?php echo t('Cache (sec)') ?></span>
                        <input type="text"
                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'facebook-transient', '1200'); ?>"
                               name="facebook-transient"
                               title="<?php echo t('Cache the stream for x seconds') ?>">
                    </p>
                    <p>
                        <?php $facebook_page_url = RevSliderFunctions::getVal($arrFieldsParams, 'facebook-page-url', ''); ?>
                        <span class="rev-new-label"><?php echo t('Facebook Page') ?></span>
                        <input type="text" value="<?php echo $facebook_page_url; ?>"
                               name="facebook-page-url" id="facebook-page-url"
                               title="<?php echo t('Put in the URL/ID of the Facebook page') ?>">
                    </p>
                </div>
                <div style="width:50%;display:block;float:left;">
                    <span class="rev-new-label"><?php echo t('Source') ?></span>
                    <select name="facebook-type-source">
                        <option value="album" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'facebook-type-source', 'album'), 'album'); ?>><?php echo t('Album') ?></option>
                        <option value="timeline" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'facebook-type-source', 'album'), 'timeline'); ?>><?php echo t('Timeline') ?></option>
                    </select>
                    <div id="facebook-album-wrap">
                        <p>
                            <?php $facebook_album = RevSliderFunctions::getVal($arrFieldsParams, 'facebook-album', ''); ?>
                            <span class="rev-new-label"><?php echo t('Select Album') ?></span>
                            <input type="hidden" name="facebook-album"
                                   value="<?php echo $facebook_album; ?>">
                            <select name="facebook-album-select" class="eg-tooltip-wrap"
                                    title="<?php echo t('Select the album to pull the data from ') ?>">
                            </select>
                        </p>
                    </div>
                    <p>
                        <span class="rev-new-label"><?php echo t('App ID') ?></span>
                        <input type="text"
                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'facebook-app-id', '') ?>"
                               name="facebook-app-id" class="eg-tooltip-wrap"
                               title="<?php echo t('Put in the Facebook App ID') ?>">
                    </p>
                    <p>
                        <span class="rev-new-label"><?php echo t('App Secret') ?></span>
                        <input type="text"
                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'facebook-app-secret', '') ?>"
                               name="facebook-app-secret" class="eg-tooltip-wrap"
                               title="<?php echo t('Put in the Facebook App secret') ?>">
                    </p>
                    <span class="description"><?php echo t('Please <a target="_blank" href="https://developers.facebook.com/docs/apps/register">register</a> your Website app with Facebook to get the values') ?></span>
                </div>
            </div>

            <div id="rs-twitter-settings-wrapper" class="rs-settings-wrapper">
                <div style="width:50%;display:block;float:left;">
                    <span class="rev-new-label"><?php echo t('Slides (max 500)') ?></span>
                    <input type="text"
                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'twitter-count', ''); ?>"
                           name="twitter-count"
                           title="<?php echo t('Display this number of tweets') ?>">
                    <p>
                        <span class="rev-new-label"><?php echo t('Cache (sec)') ?></span>
                        <input type="text"
                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'twitter-transient', '1200'); ?>"
                               name="twitter-transient"
                               title="<?php echo t('Cache the stream for x seconds') ?>">
                    </p>
                    <p>
                        <span class="rev-new-label"><?php echo t('Twitter Name @') ?></span>
                        <input type="text"
                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'twitter-user-id', ''); ?>"
                               name="twitter-user-id"
                               title="<?php echo t('Put in the Twitter Account to stream from') ?>">
                    </p>
                    <p>
                        <span class="rev-new-label"><?php echo t('Text Tweets') ?></span>
                        <input type="checkbox" class="tp-moderncheckbox withlabel"
                               id="twitter-image-only" name="twitter-image-only"
                               data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, 'twitter-image-only', 'off'), 'on'); ?> >
                    </p>
                    <p>
                        <span class="rev-new-label"><?php echo t('Retweets') ?></span>
                        <input type="checkbox" class="tp-moderncheckbox withlabel"
                               id="twitter-include-retweets" name="twitter-include-retweets"
                               data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, 'twitter-include-retweets', 'off'), 'on'); ?> >
                    </p>
                    <p>
                        <span class="rev-new-label"><?php echo t('Replies') ?></span>
                        <input type="checkbox" class="tp-moderncheckbox withlabel"
                               id="twitter-exclude-replies" name="twitter-exclude-replies"
                               data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, 'twitter-exclude-replies', 'off'), 'on'); ?> >
                    </p>
                </div>
                <div style="width:50%;display:block;float:left;">
                    <span class="rev-new-label"><?php echo t('Consumer Key') ?></span>
                    <input type="text"
                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'twitter-consumer-key', ''); ?>"
                           name="twitter-consumer-key"
                           title="<?php echo t('Put in your Twitter Consumer Key') ?>">
                    <p>
                        <span class="rev-new-label"><?php echo t('Consumer Secret') ?></span>
                        <input type="text"
                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'twitter-consumer-secret', ''); ?>"
                               name="twitter-consumer-secret"
                               title="<?php echo t('Put in your Twitter Consumer Secret') ?>">
                    </p>
                    <p>
                        <span class="rev-new-label"><?php echo t('Access Token') ?></span>
                        <input type="text"
                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'twitter-access-token', ''); ?>"
                               name="twitter-access-token"
                               title="<?php echo t('Put in your Twitter Access Token') ?>">
                    </p>
                    <p>
                        <span class="rev-new-label"><?php echo t('Access Secret') ?></span>
                        <input type="text"
                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'twitter-access-secret', ''); ?>"
                               name="twitter-access-secret"
                               title="<?php echo t('Put in your Twitter Access Secret') ?>">
                    </p>
                    <span class="description"><?php echo t('Please <a target="_blank" href="https://dev.twitter.com/apps">register</a> your application with Twitter to get the values') ?></span>
                </div>
            </div>

            <div id="rs-youtube-settings-wrapper" class="rs-settings-wrapper">
                <div class="rs-notice-wrap stream-notice"><?php echo t('The “YouTube Stream” content source is used to display a full stream of videos from a channel/playlist.<br> If you want to display a single youtube video, please select the content source “Default Slider” and add a video layer in the slide editor.') ?></div>
                <div style="width:50%;display:block;float:left;">
                    <span class="rev-new-label"><?php echo t('Slides (max 50)') ?></span>
                    <input type="text"
                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'youtube-count', ''); ?>"
                           name="youtube-count"
                           title="<?php echo t('Display this number of videos') ?>">
                    <p>
                        <span class="rev-new-label"><?php echo t('Cache (sec)') ?></span>
                        <input type="text"
                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'youtube-transient', '1200'); ?>"
                               name="youtube-transient"
                               title="<?php echo t('Cache results for x seconds') ?>">
                    </p>
                    <p>
                        <span class="rev-new-label"><?php echo t('Youtube API Key') ?></span>
                        <input type="text"
                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'youtube-api', ''); ?>"
                               name="youtube-api"
                               title="<?php echo t('Put in your YouTube API Key') ?>">
                    </p>

                    <span class="description"><?php echo t('Find information about the YouTube API key <a target="_blank" href="https://developers.google.com/youtube/v3/getting-started#before-you-start">here</a>') ?></span>
                </div>
                <div style="width:50%;display:block;float:left;">
                    <span class="rev-new-label"><?php echo t('Channel ID') ?></span>
                    <input type="text"
                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'youtube-channel-id', ''); ?>"
                           name="youtube-channel-id"
                           title="<?php echo t('Put in the ID of the YouTube channel') ?>">
                    <p>
                        <span class="rev-new-label"><?php echo t('Source') ?></span>
                        <select name="youtube-type-source">
                            <option value="channel"
                                    title="<?php echo t('Display the channel´s videos') ?>" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'youtube-type-source', 'channel'), 'channel'); ?> > <?php echo t('Channel') ?> </option>
                            <option value="playlist"
                                    title="<?php echo t('Display a playlist') ?>" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'youtube-type-source', 'channel'), 'playlist'); ?> > <?php echo t('Playlist') ?>    </option>
                        </select>
                    </p>
                    <div id="youtube-playlist-wrap">
                        <p>
                            <span class="rev-new-label"><?php echo t('Select Playlist') ?></span>
                            <input type="hidden" name="youtube-playlist"
                                   value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'youtube-playlist', '') ?>">
                            <select name="youtube-playlist-select">
                            </select>
                        </p>
                    </div>
                    <span class="description"><?php echo t('See how to find the Youtube channel ID <a target="_blank" href="https://support.google.com/youtube/answer/3250431?hl=en">here</a>') ?></span>
                </div>
            </div>

            <div id="rs-vimeo-settings-wrapper" class="rs-settings-wrapper">
                <div class="rs-notice-wrap stream-notice"><?php echo t('The “Vimeo Stream” content source is used to display a full stream of videos from a user/album/group/channel.<br> If you want to display a single vimeo video, please select the content source “Default Slider” and add a video layer in the slide editor.') ?></div>
                <div style="width:50%;display:block;float:left;">
                    <span class="rev-new-label"><?php echo t('Slides (max 60)') ?></span>
                    <input type="text"
                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'vimeo-count', ''); ?>"
                           name="vimeo-count"
                           title="<?php echo t('Display this number of videos') ?>">
                    <p>
                        <span class="rev-new-label"><?php echo t('Cache (sec)') ?></span>
                        <input type="text"
                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'vimeo-transient', '1200'); ?>"
                               name="vimeo-transient"
                               title="<?php echo t('Cache results for x seconds') ?>">
                    </p>
                </div>
                <div style="width:50%;display:block;float:left;">
                    <span class="rev-new-label"><?php echo t('Source') ?></span>
                    <select name="vimeo-type-source">
                        <option name="vimeo-type-source" value="user"
                                title="<?php echo t('Display the user\'s videos') ?>" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'vimeo-type-source', 'user'), 'user'); ?> > <?php echo t('User') ?></option>
                        <option name="vimeo-type-source" value="album"
                                title="<?php echo t('Display an album') ?>" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'vimeo-type-source', 'user'), 'album'); ?> > <?php echo t('Album') ?></option>
                        <option name="vimeo-type-source" value="group"
                                title="<?php echo t('Display a group\'s videos') ?>" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'vimeo-type-source', 'user'), 'group'); ?> > <?php echo t('Group') ?>    </option>
                        <option name="vimeo-type-source" value="channel"
                                title="<?php echo t('Display a channel\'s videos') ?>" <?php RevSliderFunctions::selected(RevSliderFunctions::getVal($arrFieldsParams, 'vimeo-type-source', 'user'), 'channel'); ?> > <?php echo t('Channel') ?>    </option>
                    </select>
                    <p>
                    <div id="vimeo-user-wrap" class="source-vimeo">
                        <span class="rev-new-label"><?php echo t('User') ?></span>
                        <input type="text"
                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'vimeo-username', ''); ?>"
                               name="vimeo-username"
                               title="<?php echo t('Either the shortcut URL or ID of the user') ?>">
                    </div>
                    <div id="vimeo-group-wrap" class="source-vimeo">
                        <span class="rev-new-label"><?php echo t('Group') ?></span>
                        <input type="text"
                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'vimeo-groupname', ''); ?>"
                               name="vimeo-groupname"
                               title="<?php echo t('Either the shortcut URL or ID of the group') ?>">
                    </div>
                    <div id="vimeo-album-wrap" class="source-vimeo">
                        <span class="rev-new-label"><?php echo t('Album ID') ?></span>
                        <input type="text"
                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'vimeo-albumid', ''); ?>"
                               name="vimeo-albumid"
                               title="<?php echo t('The ID of the album') ?>">
                    </div>
                    <div id="vimeo-channel-wrap" class="source-vimeo">
                        <span class="rev-new-label"><?php echo t('Channel') ?></span>
                        <input type="text"
                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'vimeo-channelname', ''); ?>"
                               name="vimeo-channelname"
                               title="<?php echo t('Either the shortcut URL of the channel') ?>">
                    </div>
                    </p>
                </div>
            </div>


            <div id="rs-post-settings-wrapper" class="rs-settings-wrapper">
                <div style="width:50%;display:block;float:left;">
                    <div class="rs-specific-posts-wrap">
                        <span class="rev-new-label"><?php echo t('Specific Posts List:') ?></span><!--
								--><input type="text" class='regular-text'
                                          placeholder="<?php echo t('coma separated | ex: 23,24,25') ?>"
                                          id="posts_list" name="posts_list"
                                          value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'posts_list', ''); ?>"
                                          style="width:255px"/>
                        <span class="tp-clearfix"></span>
                        <?php
                        $pop_posts = $uslider->getPostsFromPopular(15);
                        $rec_posts = $uslider->getPostsFromRecent(15);
                        $recent = array();
                        $popular = array();
                        if (!empty($pop_posts)) {
                            foreach ($pop_posts as $p_post) {
                                $popular[] = $p_post['ID'];
                            }
                        }
                        if (!empty($rec_posts)) {
                            foreach ($rec_posts as $r_post) {
                                $recent[] = $r_post['ID'];
                            }
                        }
                        ?>
                        <span class="rev-new-label"></span><a class="button-primary revblue"
                                                              id="rev-fetch-popular-posts"
                                                              style="width: 200px;"
                                                              href="javascript:jQuery('#posts_list').val(jQuery('#posts_list').val()+'<?php echo implode(',', $popular); ?>');void(0);"><i
                                class="eg-icon-plus-circled"
                                style="margin-right:10px"></i><?php echo t('Add Popular Posts') ?>
                        </a>
                        <span class="tp-clearfix"></span>
                        <span class="rev-new-label"></span><a class="button-primary revblue"
                                                              id="rev-fetch-recent-posts"
                                                              style="width: 200px;"
                                                              href="javascript:jQuery('#posts_list').val(jQuery('#posts_list').val()+'<?php echo implode(',', $recent); ?>');void(0);"><i
                                class="eg-icon-plus-circled"
                                style="margin-right:10px"></i><?php echo t('Add Recent Posts') ?>
                        </a>
                    </div>
                    <div class="rs-post-types-wrapper">
                        <?php $fetch_type = RevSliderFunctions::getVal($arrFieldsParams, 'fetch_type', 'cat_tag'); ?>
                        <span class="rev-new-label"><?php echo t('Fetch Posts By:') ?></span>
                        <select id="fetch_type" name="fetch_type"
                                style="width:181px; vertical-align: top; height:50px">
                            <option value="cat_tag" <?php RevSliderFunctions::selected($fetch_type, 'cat_tag'); ?>><?php echo t('Categories & Tags') ?></option>
                            <option value="related" <?php RevSliderFunctions::selected($fetch_type, 'related'); ?>><?php echo t('Related') ?></option>
                            <option value="popular" <?php RevSliderFunctions::selected($fetch_type, 'popular'); ?>><?php echo t('Popular') ?></option>
                            <option value="recent" <?php RevSliderFunctions::selected($fetch_type, 'recent'); ?>><?php echo t('Recent') ?></option>
                            <option value="next_prev" <?php RevSliderFunctions::selected($fetch_type, 'next_prev'); ?>><?php echo t('Next / Previous') ?></option>
                        </select>

                        <span class="tp-clearfix"></span>
                        <div class="rs-post-type-wrap">
                            <span class="rev-new-label"><?php echo t('Post Types:') ?></span>
                            <?php
                            $post_type = RevSliderFunctionsWP::getPostTypesAssoc();
                            $sel_post_types = RevSliderFunctions::getVal($arrFieldsParams, 'post_types', 'post');
                            $sel_post_types = explode(',', $sel_post_types);
                            if (empty($sel_post_types)) {
                                $sel_post_types = array('post');
                            }
                            //default
                            ?>
                            <select id="post_types" name="post_types" multiple size='7'
                                    style="width:181px; vertical-align: top; height:100px">
                                <?php
                                if (!empty($post_type)) {
                                    foreach ($post_type as $post_handle => $post_name) {
                                        $sel = (in_array($post_handle, $sel_post_types)) ? ' selected="selected"' : '';
                                        echo '<option value="' . $post_handle . '"' . $sel . '>' . $post_name . '</option>';
                                    }
                                }
                                ?>
                            </select>
                            <?php
                            $sel_post_cagetory = RevSliderFunctions::getVal($arrFieldsParams, 'post_category', '');
                            $sel_post_cagetory = explode(',', $sel_post_cagetory);

                            ?>
                            <span class="tp-clearfix"></span>
                            <span class="rev-new-label"><?php echo t('Post Categories:') ?></span>
                            <select id="post_category" name="post_category" multiple size='7'
                                    style="width:181px; vertical-align: top;">
                                <?php
                                if (!empty($postTypesWithCats)) {
                                    foreach ($postTypesWithCats as $post_type => $post_array) {
                                        if (!empty($post_array)) {
                                            foreach ($post_array as $cat_handle => $cat_name) {
                                                $sel = (in_array($cat_handle, $sel_post_cagetory)) ? ' selected="selected"' : '';
                                                $dis = (strpos($cat_handle, 'option_disabled') !== false) ? ' disabled="disabled"' : '';
                                                echo '<option value="' . $cat_handle . '"' . $dis . $sel . '>' . $cat_name . '</option>';
                                            }
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div style="width:50%;display:none;float:left;" class="rs-show-for-wc">
                    <?php $product_sortby = RevSliderFunctions::getVal($arrFieldsParams, 'product_sortby', 'ID'); ?>
                    <span class="rev-new-label"><?php echo t('Sort Products By:') ?></span>
                    <select id="product_sortby" name="product_sortby">
                        <option value="ID" <?php RevSliderFunctions::selected($product_sortby, 'ID'); ?>><?php echo t('Post ID') ?></option>
                        <option value="date" <?php RevSliderFunctions::selected($product_sortby, 'date'); ?>><?php echo t('Date') ?></option>
                        <option value="title" <?php RevSliderFunctions::selected($product_sortby, 'title'); ?>><?php echo t('Title') ?></option>
                        <option value="name" <?php RevSliderFunctions::selected($product_sortby, 'name'); ?>><?php echo t('Slug') ?></option>
                        <option value="author" <?php RevSliderFunctions::selected($product_sortby, 'author'); ?>><?php echo t('Author') ?></option>
                        <option value="modified" <?php RevSliderFunctions::selected($product_sortby, 'modified'); ?>><?php echo t('Last Modified') ?></option>
                        <option value="comment_count" <?php RevSliderFunctions::selected($product_sortby, 'comment_count'); ?>><?php echo t('Number Of Comments') ?></option>
                        <option value="rand" <?php RevSliderFunctions::selected($product_sortby, 'rand'); ?>><?php echo t('Random') ?></option>
                        <option value="none" <?php RevSliderFunctions::selected($product_sortby, 'none'); ?>><?php echo t('Unsorted') ?></option>
                        <option value="menu_order" <?php RevSliderFunctions::selected($product_sortby, 'menu_order'); ?>><?php echo t('Custom Order') ?></option>
                    </select>
                    <span class="tp-clearfix"></span>

                    <?php $product_sort_direction = RevSliderFunctions::getVal($arrFieldsParams, 'product_sort_direction', 'DESC'); ?>
                    <span class="rev-new-label"><?php echo t('Sort Direction:') ?></span>
                    <span>
								<input type="radio" id="product_sort_direction_1" value="DESC"
                                       name="product_sort_direction" <?php RevSliderFunctions::checked($product_sort_direction, 'DESC'); ?> />
								<label for="product_sort_direction_1"
                                       style="cursor:pointer;"><?php echo t('Descending') ?></label>
								<input type="radio" style="margin-left:20px;" id="product_sort_direction_2" value="ASC"
                                       name="product_sort_direction" <?php RevSliderFunctions::checked($product_sort_direction, 'ASC'); ?> />
								<label for="product_sort_direction_2"
                                       style="cursor:pointer;"><?php echo t('Ascending') ?></label>
							</span>
                    <span class="tp-clearfix"></span>

                    <span class="rev-new-label"><?php echo t('Max Products:') ?></span>
                    <input type="text" class='small-text' id="max_slider_products"
                           name="max_slider_products"
                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'max_slider_products', '30'); ?>"/>
                    <span class="tp-clearfix"></span>

                    <span class="rev-new-label"><?php echo t('Limit The Excerpt To:') ?></span>
                    <input type="text" class='small-text' id="excerpt_limit_product"
                           name="excerpt_limit_product"
                           value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'excerpt_limit_product', '55'); ?>"/>
                    <span class="tp-clearfix"></span>

                    <span class="rev-new-label"><?php echo t('Regular Price:') ?></span>
                    <span style="width: 201px; display: inline-block;"><?php echo t('From') ?>
                        <input type="text" class='small-text'
                               style="width: 50px !important; margin-right: 40px;" id="reg_price_from"
                               name="reg_price_from"
                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'reg_price_from', ''); ?>"/>
                        <?php echo t('To') ?> <input type="text" class='small-text'
                                                     style="width: 50px !important;"
                                                     id="reg_price_to" name="reg_price_to"
                                                     value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'reg_price_to', ''); ?>"/>
							</span><span class="tp-clearfix"></span>

                    <span class="rev-new-label"><?php echo t('Sale Price:') ?></span>
                    <span style="width: 201px; display: inline-block;"><?php echo t('From') ?>
                        <input type="text" class='small-text'
                               style="width: 50px !important; margin-right: 40px;" id="sale_price_from"
                               name="sale_price_from"
                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'sale_price_from', ''); ?>"/>
                        <?php echo t('To') ?> <input type="text" class='small-text'
                                                     style="width: 50px !important;"
                                                     id="sale_price_to" name="sale_price_to"
                                                     value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'sale_price_to', ''); ?>"/>
							</span><span class="tp-clearfix"></span>

                    <span class="rev-new-label"><?php echo t('In Stock Only:') ?></span>
                    <input type="checkbox" id="instock_only" name="instock_only"
                           class="tp-moderncheckbox"
                           data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, 'instock_only', 'off'), "on"); ?>>
                    <span class="tp-clearfix"></span>

                    <span class="rev-new-label"><?php echo t('Featured Only:') ?></span>
                    <input type="checkbox" id="featured_only" name="featured_only"
                           class="tp-moderncheckbox"
                           data-unchecked="off" <?php RevSliderFunctions::checked(RevSliderFunctions::getVal($arrFieldsParams, 'featured_only', 'off'), "on"); ?>>
                    <span class="tp-clearfix"></span>
                </div>

                <div style="width:50%;display:block;float:left;" class="rs-hide-for-wc">
                    <div id="post_sortby_row" valign="top">
                        <div class="rs-post-order-setting">
                            <?php $post_sortby = RevSliderFunctions::getVal($arrFieldsParams, 'post_sortby', 'ID'); ?>
                            <span class="rev-new-label"><?php echo t('Sort Posts By:') ?></span>
                            <select id="post_sortby" name="post_sortby">
                                <option value="ID" <?php RevSliderFunctions::selected($post_sortby, 'ID'); ?>><?php echo t('Post ID') ?></option>
                                <option value="date" <?php RevSliderFunctions::selected($post_sortby, 'date'); ?>><?php echo t('Date') ?></option>
                                <option value="title" <?php RevSliderFunctions::selected($post_sortby, 'title'); ?>><?php echo t('Title') ?></option>
                                <option value="name" <?php RevSliderFunctions::selected($post_sortby, 'name'); ?>><?php echo t('Slug') ?></option>
                                <option value="author" <?php RevSliderFunctions::selected($post_sortby, 'author'); ?>><?php echo t('Author') ?></option>
                                <option value="modified" <?php RevSliderFunctions::selected($post_sortby, 'modified'); ?>><?php echo t('Last Modified') ?></option>
                                <option value="comment_count" <?php RevSliderFunctions::selected($post_sortby, 'comment_count'); ?>><?php echo t('Number Of Comments') ?></option>
                                <option value="rand" <?php RevSliderFunctions::selected($post_sortby, 'rand'); ?>><?php echo t('Random') ?></option>
                                <option value="none" <?php RevSliderFunctions::selected($post_sortby, 'none'); ?>><?php echo t('Unsorted') ?></option>
                                <option value="menu_order" <?php RevSliderFunctions::selected($post_sortby, 'menu_order'); ?>><?php echo t('Custom Order') ?></option>
                            </select>
                            <span class="tp-clearfix"></span>

                            <?php $posts_sort_direction = RevSliderFunctions::getVal($arrFieldsParams, 'posts_sort_direction', 'DESC'); ?>
                            <span class="rev-new-label"><?php echo t('Sort Direction:') ?></span>
                            <span>
										<input type="radio" id="posts_sort_direction_1" value="DESC"
                                               name="posts_sort_direction" <?php RevSliderFunctions::checked($posts_sort_direction, 'DESC'); ?> />
										<label for="posts_sort_direction_1"
                                               style="cursor:pointer;"><?php echo t('Descending') ?></label>
										<input type="radio" style="margin-left:20px;" id="posts_sort_direction_2"
                                               value="ASC"
                                               name="posts_sort_direction" <?php RevSliderFunctions::checked($posts_sort_direction, 'ASC'); ?> />
										<label for="posts_sort_direction_2"
                                               style="cursor:pointer;"><?php echo t('Ascending') ?></label>
									</span>
                            <span class="tp-clearfix"></span>
                        </div>

                        <span class="rev-new-label"><?php echo t('Max Posts Per Slider:') ?></span>
                        <input type="text" class='small-text' id="max_slider_posts"
                               name="max_slider_posts"
                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'max_slider_posts', '30'); ?>"/>
                        <span class="tp-clearfix"></span>

                        <span class="rev-new-label"><?php echo t('Limit The Excerpt To:') ?></span>
                        <input type="text" class='small-text' id="excerpt_limit" name="excerpt_limit"
                               value="<?php echo RevSliderFunctions::getVal($arrFieldsParams, 'excerpt_limit', '55'); ?>"/>
                    </div>
                </div>
                <span class="tp-clearfix"></span>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}