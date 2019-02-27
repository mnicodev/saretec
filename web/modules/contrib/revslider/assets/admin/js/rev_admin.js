var rs_install_slider = {}, rs_install_ids = [], rs_install_url = !1, RevSliderAdmin = new function () {
    function k() {
        var a = jQuery("#spinner_color").val(), b = jQuery("#use_spinner");
        0 != b.val() && 5 != b.val() || (a = "#ffffff");
        var c = jQuery("#spinner_preview .tp-loader.tp-demo");
        c.hasClass("spinner0") || c.hasClass("spinner1") || c.hasClass("spinner2") ? c.css({backgroundColor: a}) : c.find("div").css({backgroundColor: a})
    }

    function l(a, b) {
        var c = jQuery("#use_spinner");
        a.find(".tp-loader").remove(), a.append('<div class="tp-loader tp-demo"><div class="dot1"></div><div class="dot2"></div><div class="bounce1"></div><div class="bounce2"></div><div class="bounce3"></div></div>'), a.find(".tp-demo").addClass("spinner" + c.val()), "-1" == c.val() || 0 == c.val() || 5 == c.val() ? jQuery("#spinner_color_row").css({display: "none"}) : jQuery("#spinner_color_row").css({display: "block"}), k()
    }

    var a = this, b = null, c = function (a) {
        jQuery("#button_save_slider,#button_save_slider_t").click(function () {
            var b = {
                params: RevSliderSettings.getSettingsObject("form_slider_params"),
                main: RevSliderSettings.getSettingsObject("form_slider_main"),
                template: jQuery("#revslider_template").val()
            };
            "update_slider" == a && (b.sliderid = jQuery("#sliderid").val(), b.params.custom_css = rev_cm_custom_css.getValue(), b.params.custom_javascript = rev_cm_custom_js.getValue(), UniteAdminRev.setAjaxLoaderID("loader_update, #loader_update_t"), UniteAdminRev.setAjaxHideButtonID("button_save_slider,button_save_slider_t"), UniteAdminRev.setSuccessMessageID("update_slider_success,#update_slider_success_t")), UniteAdminRev.ajaxRequest(a, b)
        })
    };
    a.initLayerPreview = function () {
        jQuery("#button_preview_slider-tb").click(function () {
            var a = jQuery("#sliderid").val();
            o(a)
        })
    };
    var d = function () {
        var a = jQuery("#alias").val(), b = '[rev_slider alias="' + a + '"]';
        "" == a && (b = rev_lang.wrong_alias), jQuery("#shortcode").val(b)
    }, e = !1;
    a.load_slider_template_html = function () {
        return e ? (jQuery("#template_area").addClass("show"), jQuery("#template_area").trigger("showitnow")) : UniteAdminRev.ajaxRequest("load_template_store_sliders", {}, function (a) {
            a.success && (jQuery(".revolution-template-groups").html(a.html), jQuery("#template_area").addClass("show"), jQuery("#template_area").trigger("showitnow"), initTemplateSliders(), e = !0)
        }), !0
    };
    var f = !1;
    a.load_slide_template_html = function () {
        return f ? (jQuery("#template_area").addClass("show"), jQuery(".revolution-template-groups").perfectScrollbar(), scrollTA()) : UniteAdminRev.ajaxRequest("load_template_store_slides", {}, function (a) {
            a.success && (jQuery(".revolution-basic-templates").html(a.html), f = !0, templateSelectorHandling(), jQuery("#template_area").addClass("show"), jQuery(".revolution-template-groups").perfectScrollbar(), scrollTA())
        }), !0
    };
    var g = function (a, b, c, d) {
        jQuery('input[name="width"]').attr("disabled", !1), jQuery('input[name="height"]').attr("disabled", !1), "normal" != d && "full" != d || jQuery("#layout-preshow").removeClass("lp-fullscreenalign"), a ? (jQuery("#responsitive_row").removeClass("disabled"), jQuery("#responsitive_row input").prop("disabled", "")) : (jQuery("#responsitive_row").addClass("disabled"), jQuery("#responsitive_row input").prop("disabled", "disabled")), b ? (jQuery("#auto_height_row").removeClass("disabled"), jQuery("#layout-preshow").removeClass("lp-fullscreenalign")) : jQuery("#auto_height_row").addClass("disabled"), "normal" == d && 0 == a && 0 == b && 0 == c ? jQuery(".rs-hide-on-fixed").hide() : jQuery(".rs-hide-on-fixed").show(), c ? (jQuery(".rs-show-on-fullscreen").show(), jQuery(".rs-hide-on-fullscreen").hide(), "on" == jQuery('input[name="full_screen_align_force"]:checked').val() && jQuery("#layout-preshow").addClass("lp-fullscreenalign"), jQuery("#full_screen_align_force_row").removeClass("disabled"), jQuery("#fullscreen_offset_container_row").removeClass("disabled")) : (jQuery(".rs-show-on-fullscreen").hide(), jQuery(".rs-hide-on-fullscreen").show(), jQuery("#full_screen_align_force_row").addClass("disabled"), jQuery("#fullscreen_offset_container_row").addClass("disabled")), c || b ? jQuery("#force_full_width_row").removeClass("disabled") : jQuery("#force_full_width_row").addClass("disabled"), jQuery("#layout-preshow").removeClass("lp-fixed"), jQuery("#layout-preshow").removeClass("lp-custom"), jQuery("#layout-preshow").removeClass("lp-autoresponsive"), jQuery("#layout-preshow").removeClass("lp-fullscreen"), a ? jQuery("#layout-preshow").addClass("lp-custom") : b ? jQuery("#layout-preshow").addClass("lp-autoresponsive") : c ? jQuery("#layout-preshow").addClass("lp-fullscreen") : jQuery("#layout-preshow").addClass("lp-fixed")
    }, h = function () {
        jQuery("#slider_type_1").click(function () {
            g(!1, !1, !1, "normal")
        }), jQuery("#slider_type_2").click(function () {
            g(!0, !1, !1, "normal")
        }), jQuery("#slider_type_3").click(function () {
            g(!1, !0, !1, "full")
        }), jQuery("#slider_type_4").click(function () {
            g(!1, !1, !0, "screen")
        }), jQuery('input[name="full_screen_align_force"]').click(function () {
            "on" == jQuery(this).val() ? jQuery("#layout-preshow").addClass("lp-fullscreenalign") : jQuery("#layout-preshow").removeClass("lp-fullscreenalign")
        }), jQuery('input[name="auto_height"]').click(function () {
            "on" == jQuery(this).val() ? jQuery("#layout-preshow").addClass("lp-autoheight") : jQuery("#layout-preshow").removeClass("lp-autoheight")
        }), jQuery('input[name="force_full_width"]').click(function () {
            "on" == jQuery(this).val() ? jQuery("#layout-preshow").addClass("lp-fullwidth") : jQuery("#layout-preshow").removeClass("lp-fullwidth")
        }), jQuery('input[name="full_screen_align_force"]:checked').click(), jQuery('input[name="auto_height"]:checked').click()//, jQuery('input[name="force_full_width"]:checked').click()
    }, i = function (a, c) {
        jQuery("#" + a).change(function () {
            var a = jQuery(this).val(), d = [];
            jQuery("#" + c + " option").each(function () {
                1 == jQuery(this).prop("selected") && d.push(jQuery(this).val())
            }), jQuery("#" + c).empty(), jQuery(a).each(function (a, e) {
                var f = b[e];
                for (var g in f) {
                    var h = f[g], i = new Option(h, g);
                    0 == g.indexOf("option_disabled") ? jQuery(i).prop("disabled", "disabled") : jQuery.inArray(jQuery(i).val(), d) !== -1 && jQuery(i).prop("selected", "selected"), jQuery("#" + c).append(i)
                }
            })
        }), jQuery("#" + a).change()
    }, j = function () {
        m(), h(), b = jQuery.parseJSON(g_jsonTaxWithCats), i("post_types", "post_category"), i("product_types", "product_category"), jQuery("#fetch_type").change(function () {
            switch (jQuery(".rs-post-type-wrap").hide(), jQuery(".rs-post-order-setting").show(), jQuery("#post_sortby_row").show(), jQuery(this).val()) {
                case"cat_tag":
                    jQuery(".rs-post-type-wrap").show();
                    break;
                case"related":
                    break;
                case"popular":
                    jQuery(".rs-post-order-setting").hide();
                    break;
                case"recent":
                    jQuery(".rs-post-order-setting").hide();
                    break;
                case"next_prev":
                    jQuery("#post_sortby_row").hide()
            }
        }), jQuery("#fetch_type option:selected").change(), jQuery("input[name='source_type']").click(function () {
            "posts" == jQuery(this).val() ? (jQuery("#toolbox_wrapper").hide(), jQuery("#slider_type_row").hide(), jQuery("#slider_type_row").prev().hide(), jQuery("#fullscreen_offset_container_row").hide(), jQuery("#full_screen_align_force_row").hide(), jQuery("#slider_size_row").hide(), jQuery("#auto_height_row").hide(), jQuery("#force_full_width_row").hide(), jQuery("#responsitive_row").hide(), jQuery("#responsitive_row").next().hide()) : (jQuery(".settings_panel_right").show(), jQuery("#toolbox_wrapper").show(), jQuery("#slider_type_row").show(), jQuery("#slider_type_row").prev().show(), jQuery("#fullscreen_offset_container_row").show(), jQuery("#full_screen_align_force_row").show(), jQuery("#slider_size_row").show(), jQuery("#auto_height_row").show(), jQuery("#force_full_width_row").show(), jQuery("#responsitive_row").show(), jQuery("#responsitive_row").next().show(), jQuery("#layout-preshow").show()), "specific_posts" == jQuery(this).val() ? (jQuery('#fetch_type option[value="cat_tag"]').attr("selected", "selected"), jQuery("#fetch_type option:selected").change()) : jQuery("#fetch_type option:selected").change(), jQuery(".rs-settings-wrapper").hide(), "posts" == jQuery(this).val() || "specific_posts" == jQuery(this).val() || "woocommerce" == jQuery(this).val() ? (jQuery("#rs-post-settings-wrapper").show(), jQuery(".rs-specific-posts-wrap").hide(), jQuery(".rs-woocommerce-product-wrap").hide(), jQuery(".rs-post-types-wrapper").hide(), jQuery(".rs-show-for-wc").hide(), jQuery(".rs-hide-for-wc").show(), "posts" == jQuery(this).val() ? (jQuery(".rs-post-types-wrapper").show(), jQuery(".rs-specific-posts-wrap").hide()) : "specific_posts" == jQuery(this).val() ? (jQuery(".rs-post-types-wrapper").hide(), jQuery(".rs-specific-posts-wrap").show()) : "woocommerce" == jQuery(this).val() && (jQuery(".rs-woocommerce-product-wrap").show(), jQuery(".rs-show-for-wc").show(), jQuery(".rs-hide-for-wc").hide())) : (jQuery("#rs-post-settings-wrapper").hide(), jQuery("#rs-" + jQuery(this).val() + "-settings-wrapper").show(), "facebook" == jQuery(this).val() && jQuery('select[name="facebook-type-source"]').change(), "instagram" == jQuery(this).val() && jQuery('select[name="instagram-type"]').change(), "photosets" == jQuery("select[name=flickr-type]").val() && "flickr" == jQuery("input[name=source_type]:checked").val() && jQuery("input[name=flickr-user-url]").change(), "youtube" == jQuery("input[name=source_type]:checked").val() && "playlist" == jQuery("select[name='youtube-type-source']").val() && jQuery("input[name=youtube-channel-id]").change())
        }), jQuery(".rs-settings-wrapper").hide(), jQuery("#source_type_1").is(":checked") || jQuery("#source_type_2").is(":checked") ? (jQuery("#rs-post-settings-wrapper").show(), jQuery("#source_type_1").is(":checked") ? (jQuery(".rs-post-types-wrapper").show(), jQuery(".rs-specific-posts-wrap").hide()) : (jQuery(".rs-post-types-wrapper").hide(), jQuery(".rs-specific-posts-wrap").show())) : (jQuery("#rs-post-settings-wrapper").hide(), jQuery("input[name=source_type]:checked").click()), jQuery("#source_type_1").is(":checked") && (jQuery("#toolbox_wrapper").hide(), jQuery("#slider_type_row").hide(), jQuery("#slider_type_row").prev().hide(), jQuery("#fullscreen_offset_container_row").hide(), jQuery("#full_screen_align_force_row").hide(), jQuery("#slider_size_row").hide(), jQuery("#auto_height_row").hide(), jQuery("#force_full_width_row").hide(), jQuery("#responsitive_row").hide(), jQuery("#responsitive_row").next().hide()), jQuery(document).ready(function () {
            jQuery('input[name="slider_type"]:checked').click(), jQuery('select[name="navigation_style"]').change(function () {
                switch (jQuery(this).val()) {
                    case"preview1":
                    case"preview2":
                    case"preview3":
                    case"preview4":
                        jQuery("#leftarrow_align_hor_row").hide(), jQuery("#leftarrow_align_vert_row").hide(), jQuery("#leftarrow_offset_hor_row").hide(), jQuery("#leftarrow_offset_vert_row").hide(), jQuery("#rightarrow_align_hor_row").hide(), jQuery("#rightarrow_align_vert_row").hide(), jQuery("#rightarrow_offset_hor_row").hide(), jQuery("#rightarrow_offset_vert_row").hide(), jQuery("#navigation_arrows_row").hide();
                        break;
                    default:
                        jQuery("#navigaion_type").change(), jQuery("#navigation_arrows").change()
                }
            }), jQuery("#navigation_arrows").on("change", function () {
                switch (jQuery(this).val()) {
                    case"nexttobullets":
                    case"solo":
                        var a = jQuery('select[name="navigation_style"]').val();
                        "preview1" != a && "preview2" != a && "preview3" != a && "preview4" != a && (jQuery("#leftarrow_align_hor_row").show(), jQuery("#leftarrow_align_vert_row").show(), jQuery("#leftarrow_offset_hor_row").show(), jQuery("#leftarrow_offset_vert_row").show(), jQuery("#rightarrow_align_hor_row").show(), jQuery("#rightarrow_align_vert_row").show(), jQuery("#rightarrow_offset_hor_row").show(), jQuery("#rightarrow_offset_vert_row").show());
                        break;
                    default:
                        jQuery("#leftarrow_align_hor_row").hide(), jQuery("#leftarrow_align_vert_row").hide(), jQuery("#leftarrow_offset_hor_row").hide(), jQuery("#leftarrow_offset_vert_row").hide(), jQuery("#rightarrow_align_hor_row").hide(), jQuery("#rightarrow_align_vert_row").hide(), jQuery("#rightarrow_offset_hor_row").hide(), jQuery("#rightarrow_offset_vert_row").hide()
                }
            }), jQuery('select[name="navigation_style"] option:selected').change(), jQuery("#navigation_type").change(function () {
                switch (jQuery(this).val()) {
                    case"bullet":
                    case"thumb":
                        var a = jQuery('select[name="navigation_style"]').val();
                        "preview1" != a && "preview2" != a && "preview3" != a && "preview4" != a && jQuery("#navigation_arrows_row").show();
                        break;
                    default:
                        jQuery("#navigation_arrows_row").hide()
                }
            }), jQuery("#navigation_style").change()
        }), jQuery("body").on("change", 'select[name="facebook-type-source"]', function () {
            var a = jQuery(this).val();
            "timeline" == a ? (jQuery("#facebook-album-wrap").hide(), jQuery("#facebook-timeline-wrap").show()) : (jQuery("#facebook-timeline-wrap").hide(), jQuery("#facebook-album-wrap").show(), jQuery("input[name=facebook-page-url]").change()), jQuery("input[name=facebook-type-source]").val(a)
        }), "facebook" == jQuery("input[name=source_type]:checked").val() && jQuery('select[name="facebook-type-source"]').change(), jQuery("input[name=facebook-page-url]").change(function () {
            if ("album" == jQuery("select[name='facebook-type-source']").val()) {
                var a = {
                    url: jQuery("input[name=facebook-page-url]").val(),
                    album: jQuery("input[name=facebook-album]").val(),
                    app_id: jQuery("input[name=facebook-app-id]").val(),
                    app_secret: jQuery("input[name=facebook-app-secret]").val()
                };
                "" != jQuery("input[name=facebook-page-url]").val() ? UniteAdminRev.ajaxRequest("get_facebook_photosets", a, function (a) {
                    jQuery("select[name=facebook-album-select]").html(a.html), jQuery("select[name=facebook-album-select]").change()
                }) : (jQuery("select[name=facebook-album-select]").html(""), jQuery("select[name=facebook-album-select]").change())
            }
        }), "facebook" == jQuery("input[name=source_type]:checked").val() && jQuery("input[name=facebook-page-url]").change(), jQuery("select[name=facebook-album-select]").change(function () {
            jQuery("input[name=facebook-album]").val(jQuery("select[name=facebook-album-select]").val())
        }), jQuery("select[name=flickr-type]").change(function () {
            var a = jQuery(this).val();
            switch (a) {
                case"publicphotos":
                    jQuery("#flickr-photosets-wrap,#flickr-gallery-url-wrap,#flickr-group-url-wrap").hide(), jQuery("#flickr-publicphotos-url-wrap").show();
                    break;
                case"gallery":
                    jQuery("#flickr-publicphotos-url-wrap,#flickr-photosets-wrap,#flickr-group-url-wrap").hide(), jQuery("#flickr-gallery-url-wrap").show();
                    break;
                case"photosets":
                    jQuery("#flickr-gallery-url-wrap,#flickr-group-url-wrap").hide(), jQuery("#flickr-publicphotos-url-wrap,#flickr-photosets-wrap").show();
                    break;
                case"group":
                    jQuery("#flickr-publicphotos-url-wrap,#flickr-photosets-wrap,#flickr-gallery-url-wrap").hide(), jQuery("#flickr-group-url-wrap").show()
            }
        }), jQuery("select[name=flickr-type]").change(), jQuery("input[name=flickr-user-url],select[name=flickr-type]").change(function () {
            if ("photosets" == jQuery("select[name=flickr-type]").val())if ("" != jQuery("input[name=flickr-user-url]").val() && "" != jQuery("input[name=flickr-api-key]").val()) {
                var a = {
                    url: jQuery("input[name=flickr-user-url]").val(),
                    key: jQuery("input[name=flickr-api-key]").val(),
                    count: jQuery("input[name=flickr-count]").val(),
                    set: jQuery("input[name=flickr-photoset]").val()
                };
                UniteAdminRev.ajaxRequest("get_flickr_photosets", a, function (a) {
                    jQuery("select[name=flickr-photoset-select]").html(a.data.html), jQuery("select[name=flickr-photoset-select]").change()
                })
            } else jQuery("select[name=flickr-photoset-select]").html(response.data.html), jQuery("input[name=flickr-photoset]").val("")
        }), "photosets" == jQuery("select[name=flickr-type]").val() && "flickr" == jQuery("input[name=source_type]:checked").val() && jQuery("input[name=flickr-user-url]").change(), jQuery("select[name=flickr-photoset-select]").change(function () {
            jQuery("input[name=flickr-photoset]").val(jQuery("select[name=flickr-photoset-select]").val())
        }), jQuery("select[name=youtube-playlist-select]").change(function () {
            jQuery("input[name=youtube-playlist]").val(jQuery("select[name=youtube-playlist-select]").val())
        }), jQuery("input[name=youtube-channel-id]").change(function () {
            if ("playlist" == jQuery("select[name='youtube-type-source']").val()) {
                var a = {
                    api: jQuery("input[name=youtube-api]").val(),
                    id: jQuery("input[name=youtube-channel-id]").val(),
                    playlist: jQuery("input[name=youtube-playlist]").val()
                };
                "" != jQuery("input[name=youtube-channel-id]").val() ? UniteAdminRev.ajaxRequest("get_youtube_playlists", a, function (a) {
                    jQuery("select[name=youtube-playlist-select]").html(a.data.html), jQuery("select[name=youtube-playlist-select]").change()
                }) : jQuery("select[name=youtube-playlist-select]").html("")
            }
        }), jQuery("select[name=youtube-type-source]").change(function () {
            var a = jQuery(this).val();
            "playlist" != a ? jQuery("#youtube-playlist-wrap").hide() : (jQuery("#youtube-playlist-wrap").show(), "youtube" == jQuery("input[name=source_type]:checked").val() && "" != jQuery("input[name=youtube-channel-id]").val() && "playlist" == jQuery('select[name="youtube-type-source"]').val() && jQuery("input[name=youtube-channel-id]").change())
        }), jQuery("select[name=youtube-type-source]").change(), jQuery("select[name=vimeo-type-source]").change(function () {
            var a = jQuery(this).val();
            jQuery(".source-vimeo").hide(), jQuery("#vimeo-" + a + "-wrap").show()
        }), jQuery("select[name=vimeo-type-source]").change(), jQuery("body").on("change", 'select[name="instagram-type"]', function () {
            var a = jQuery(this).val();
            jQuery(this).parent().find("div").hide(), jQuery("#instagram_" + a).show()
        }), "instagram" == jQuery("input[name=source_type]:checked").val() && jQuery('select[name="instagram-type"]').change(), jQuery("body").on("click", 'input[name="loop_slide"]', function () {
            "noloop" == jQuery(this).val() && (jQuery('#navigaion_type option[value="none"]').attr("selected", !0), jQuery('#navigation_arrows option[value="none"]').attr("selected", !0), jQuery("#navigaion_type").change(), jQuery("#navigation_arrows").change(), UniteAdminRev.showInfo({
                type: "info",
                hideon: "",
                event: "",
                content: rev_lang.nav_bullet_arrows_to_none,
                hidedelay: 3
            }))
        }), jQuery("#enable_advanced_sizes").click(function () {
            jQuery(".rev-advanced-sizes-wrap").show(), jQuery("#rev-enable-advanced-sizes").hide(), jQuery('input[name="advanced-responsive-sizes"]').val("true"), jQuery(".rev-desktop-naming").html("Desktop")
        }), jQuery("#disable_advanced_sizes").click(function () {
            jQuery(".rev-advanced-sizes-wrap").hide(), jQuery("#rev-enable-advanced-sizes").show(), jQuery('input[name="advanced-responsive-sizes"]').val("false"), jQuery(".rev-desktop-naming").html("")
        })
    };
    this.initSpinnerAdmin = function () {
        jQuery("#use_spinner_row").parent().prepend('<div id="spinner_preview"></div>');
        var a = jQuery("#spinner_preview"), b = jQuery("#use_spinner"), c = jQuery("#spinner_color"), d = c.val();
        l(a), b.on("change", function () {
            l(a, !0)
        }), setInterval(function () {
            d != c.val() && (k(), oldocl = c.val())
        }, 300)
    }, this.initAddSliderView = function () {
        j(), jQuery("#title").focus(), c("create_slider"), g(!1, !1, !1, "normal"), this.initSpinnerAdmin()
    }, this.initEditSliderView = function () {
        j(), c("update_slider"), jQuery("#button_delete_slider, #button_delete_slider_t").click(function () {
            if (0 == confirm(rev_lang.really_want_to_delete + " '" + jQuery("#title").val() + "' ?"))return !0;
            var a = {sliderid: jQuery("#sliderid").val()};
            UniteAdminRev.ajaxRequest("delete_slider", a)
        }), jQuery("#api_wrapper .api-input").click(function () {
            jQuery(this).select().focus()
        }), jQuery("#link_show_api").click(function () {
            jQuery("#api_wrapper").show(), jQuery("#link_show_api").addClass("button-selected"), jQuery("#toolbox_wrapper").hide(), jQuery("#link_show_toolbox").removeClass("button-selected")
        }), jQuery("#link_show_toolbox").click(function () {
            jQuery("#toolbox_wrapper").show(), jQuery("#link_show_toolbox").addClass("button-selected"), jQuery("#api_wrapper").hide(), jQuery("#link_show_api").removeClass("button-selected")
        }), jQuery("#button_export_slider").click(function () {
            var a = jQuery("#sliderid").val(), b = jQuery('input[name="export_dummy_images"]').is(":checked"), c = ajaxurl + UniteAdminRev.return_ajaxurl_param() + "action=" + g_uniteDirPlugin + "_ajax_action&client_action=export_slider&dummy=" + b + "&nonce=" + g_revNonce;
            c += "&sliderid=" + a, location.href = c
        }), jQuery("#button_preview_slider, #button_preview_slider_t").click(function () {
            var a = jQuery("#sliderid").val();
            o(a)
        }), jQuery("#button_replace_url").click(function () {
            if (0 == confirm(rev_lang.sure_to_replace_urls))return !1;
            var a = {
                sliderid: jQuery("#sliderid").val(),
                url_from: jQuery("#replace_url_from").val(),
                url_to: jQuery("#replace_url_to").val()
            };
            UniteAdminRev.setAjaxLoaderID("loader_replace_url"), UniteAdminRev.setAjaxHideButtonID("button_replace_url"), UniteAdminRev.setSuccessMessageID("replace_url_success"), UniteAdminRev.ajaxRequest("replace_image_urls", a)
        }), jQuery('input[name="slider_type"]').each(function () {
            jQuery(this).is(":checked") && jQuery(this).click()
        }), jQuery("#reset_slide_button").click(function () {
            if ("0.5" == jQuery(this).css("opacity"))return !1;
            if (0 == confirm(rev_lang.set_settings_on_all_slider))return !1;
            var a = {sliderid: jQuery("#sliderid").val()};
            jQuery('input[name="reset-slide_transition"]').attr("checked") === "checked" && (a.slide_transition = jQuery('select[name="def-slide_transition"] option:selected').val()), jQuery('input[name="reset-transition_duration"]').attr("checked") === "checked" && (a.transition_duration = jQuery('input[name="def-transition_duration"]').val()), jQuery('input[name="reset-image_source_type"]').attr("checked") === "checked" && (a.image_source_type = jQuery('select[name="def-image_source_type"] option:selected').val()), jQuery('input[name="reset-background_fit"]').attr("checked") === "checked" && (a.bg_fit = jQuery("#def-background_fit option:selected").val(), "percentage" == a.bg_fit && (a.bg_fit_x = jQuery('input[name="def-bg_fit_x"]').val(), a.bg_fit_y = jQuery('input[name="def-bg_fit_y"]').val())), jQuery('input[name="reset-bg_position"]').attr("checked") === "checked" && (a.bg_position = jQuery('select[name="def-bg_position"] option:selected').val(), "percentage" == a.bg_position && (a.bg_position_x = jQuery('input[name="def-bg_position_x"]').val(), a.bg_position_y = jQuery('input[name="def-bg_position_y"]').val())), jQuery('input[name="reset-bg_repeat"]').attr("checked") === "checked" && (a.bg_repeat = jQuery('select[name="def-bg_repeat"] option:selected').val()), jQuery('input[name="reset-kenburn_effect"]').attr("checked") === "checked" && (a.kenburn_effect = jQuery('input[name="def-kenburn_effect"]').attr("checked") === "checked" ? "on" : "off"), jQuery('input[name="reset-kb_start_fit"]').attr("checked") === "checked" && (a.kb_start_fit = jQuery('input[name="def-kb_start_fit"]').val()), jQuery('input[name="reset-kb_easing"]').attr("checked") === "checked" && (a.kb_easing = jQuery('select[name="def-kb_easing"] option:selected').val()), jQuery('input[name="reset-kb_end_fit"]').attr("checked") === "checked" && (a.kb_end_fit = jQuery('input[name="def-kb_end_fit"]').val()), jQuery('input[name="reset-kb_duration"]').attr("checked") === "checked" && (a.kb_duration = jQuery('input[name="def-kb_duration"]').val()), jQuery('input[name="reset-kb_start_offset_x"]').attr("checked") === "checked" && (a.kb_start_offset_x = jQuery('input[name="def-kb_start_offset_x"]').val()), jQuery('input[name="reset-kb_start_offset_y"]').attr("checked") === "checked" && (a.kb_start_offset_y = jQuery('input[name="def-kb_start_offset_y"]').val()), jQuery('input[name="reset-kb_blur_start"]').attr("checked") === "checked" && (a.kb_blur_start = jQuery('input[name="def-kb_blur_start"]').val()), jQuery('input[name="reset-kb_blur_end"]').attr("checked") === "checked" && (a.kb_blur_end = jQuery('input[name="def-kb_blur_end"]').val()), jQuery('input[name="reset-kb_end_offset_x"]').attr("checked") === "checked" && (a.kb_end_offset_x = jQuery('input[name="def-kb_end_offset_x"]').val()), jQuery('input[name="reset-kb_end_offset_y"]').attr("checked") === "checked" && (a.kb_end_offset_y = jQuery('input[name="def-kb_end_offset_y"]').val()), jQuery('input[name="reset-kb_start_rotate"]').attr("checked") === "checked" && (a.kb_start_rotate = jQuery('input[name="def-kb_start_rotate"]').val()), jQuery('input[name="reset-kb_end_rotate"]').attr("checked") === "checked" && (a.kb_end_rotate = jQuery('input[name="def-kb_end_rotate"]').val()), UniteAdminRev.ajaxRequest("reset_slide_settings", a)
        }), jQuery(".rs-reset-slide-setting").change(function () {
            jQuery("#reset_slide_button").css("opacity", "0.5"), jQuery(".rs-reset-slide-setting").each(function () {
                if (jQuery(this).attr("checked") === "checked")return jQuery("#reset_slide_button").css("opacity", "1"), !0
            })
        }), jQuery(".rs-reset-slide-setting").change(), jQuery(".tp-moderncheckbox").each(function () {
            RevSliderSettings.onoffStatus(jQuery(this))
        }), this.initSpinnerAdmin()
    };
    var m = function () {
        jQuery("#shortcode").focus(function () {
            this.select()
        }), jQuery("#shortcode").click(function () {
            this.select()
        }), jQuery("#alias").change(function () {
            d()
        }), jQuery("#alias").keyup(function () {
            d()
        }), d()
    }, n = function (a) {
        var b = jQuery("#list_slides").sortable("toArray"), c = [], d = 0;
        jQuery(b).each(function (a, b) {
            var e = b.replace("slidelist_item_", "");
            c.push(e), d++, jQuery("#slidelist_item_" + e + " .order-text").text(d)
        });
        var e = {arrIDs: c, sliderID: a};
        jQuery("#saving_indicator").show(), UniteAdminRev.ajaxRequest("update_slides_order", e, function () {
            jQuery("#saving_indicator").hide()
        }), jQuery("#select_sortby").val("menu_order")
    };
    this.initNewsletterRoutine = function () {
        jQuery("#subscribe-to-newsletter").click(function () {
            var a = {email: jQuery('input[name="rs-email"]').val()};
            UniteAdminRev.ajaxRequest("subscribe_to_newsletter", a)
        }), jQuery("#activate-unsubscribe").click(function () {
            jQuery(".subscribe-newsletter-wrap").hide(), jQuery("#activate-unsubscribe").hide(), jQuery(".unsubscribe-newsletter-wrap").show(), jQuery("#unsubscribe-text").show(), jQuery("#subscribe-text").hide()
        }), jQuery("#cancel-unsubscribe").click(function () {
            jQuery(".subscribe-newsletter-wrap").show(), jQuery("#activate-unsubscribe").show(), jQuery(".unsubscribe-newsletter-wrap").hide(), jQuery("#unsubscribe-text").hide(), jQuery("#subscribe-text").show()
        }), jQuery("#unsubscribe-to-newsletter").click(function () {
            var a = {email: jQuery('input[name="rs-email"]').val()};
            UniteAdminRev.ajaxRequest("unsubscribe_to_newsletter", a)
        })
    }, this.initSlidersListView = function () {
        jQuery("body").on("click", ".rs-reload-shop", function () {
            showWaitAMinute({
                fadeIn: 300,
                text: rev_lang.please_wait_a_moment
            }), location.href = window.location.href + "&update_shop"
        }), jQuery("body").on("click", ".template_slider_item_reimport, .install_template_slider", function () {
            var a = jQuery(this);
            if (jQuery("#import_dialog_box").data("requested_slide", a.data("title")), a.hasClass("deny_download"))return alert(rev_lang.this_template_requires_version + " " + a.data("versionneed") + " " + rev_lang.of_slider_revolution), !1;
            var b = {};
            return b.zip = a.data("zipname"), b.uid = a.data("uid"), b.package = "false", jQuery("#dialog_import_template_slider_from").dialog({
                modal: !0,
                resizable: !1,
                create: function (a) {
                    jQuery(a.target).parent().find(".ui-dialog-titlebar").addClass("tp-slider-new-dialog-title")
                },
                buttons: {
                    Local: function () {
                        return RS_DEMO ? (alert(rev_lang.not_available_in_demo), !1) : (jQuery(".input_import_slider").val(""), jQuery(".rs-import-slider-button").hide(), jQuery(".rs-zip-name").text(b.zip), jQuery(".rs-uid").val(b.uid), jQuery(".rs-package").val(b.package), jQuery("#dialog_import_template_slider").dialog({
                            modal: !0,
                            resizable: !1,
                            width: 600,
                            height: 450,
                            closeOnEscape: !0,
                            dialogClass: "tpdialogs",
                            create: function (a) {
                                jQuery(a.target).parent().find(".ui-dialog-titlebar").addClass("tp-slider-new-dialog-title")
                            },
                            buttons: {
                                Close: function () {
                                    jQuery(this).dialog("close")
                                }
                            }
                        }), void jQuery(this).dialog("close"))
                    }, Online: function () {
                        rs_plugin_validated ? rs_single_page_creation ? jQuery("#dialog_import_template_slider_page_template").dialog({
                            modal: !0,
                            resizable: !1,
                            title: "Create Blank Demo Page",
                            width: 450,
                            height: 200,
                            closeOnEscape: !0,
                            dialogClass: "tpdialogs",
                            create: function (a) {
                                jQuery(a.target).parent().find(".ui-dialog-titlebar").addClass("tp-slider-new-dialog-title")
                            },
                            buttons: {
                                Yes: function () {
                                    c(b, "true"), jQuery(this).dialog("close"), jQuery("#dialog_import_template_slider_from").dialog("close")
                                }, No: function () {
                                    c(b, "false"), jQuery(this).dialog("close"), jQuery("#dialog_import_template_slider_from").dialog("close")
                                }
                            }
                        }) : (c(b, "false"), jQuery("#dialog_import_template_slider_from").dialog("close")) : jQuery("#regsiter-to-access-store-none").click()
                    }
                }
            }), jQuery("#close-template").click(), !1
        }), jQuery("body").on("click", ".template_slider_item_reimport_package, .install_template_slider_package", function () {
            if (jQuery(this).hasClass("deny_download"))return alert(rev_lang.this_template_requires_version + " " + jQuery(this).data("versionneed") + " " + rev_lang.of_slider_revolution), !1;
            var a = {};
            return a.zip = jQuery(this).data("zipname"), a.uid = jQuery(this).data("uid"), a.package = "true", rs_plugin_validated ? rs_pack_page_creation ? jQuery("#dialog_import_template_slider_page_template").dialog({
                modal: !0,
                resizable: !1,
                title: "Create Blank Demo Page",
                width: 450,
                height: 200,
                closeOnEscape: !0,
                dialogClass: "tpdialogs",
                create: function (a) {
                    jQuery(a.target).parent().find(".ui-dialog-titlebar").addClass("tp-slider-new-dialog-title")
                },
                buttons: {
                    Yes: function () {
                        c(a, "true"), jQuery(this).dialog("close")
                    }, No: function () {
                        c(a, "false"), jQuery(this).dialog("close")
                    }
                }
            }) : c(a, "false") : alert(rev_lang.this_feature_only_if_activated), jQuery("#close-template").click(), !1
        });
        var b, c = function (a, c) {
            if (jQuery("#import_dialog_box").html(""), jQuery("#dialog_import_template_slider_info").dialog({
                    modal: !0,
                    resizable: !1,
                    width: 450,
                    minHeight: 350,
                    open: function (a, c) {
                        var d = jQuery(a.target).parent(), e = d.find(".ui-dialog-titlebar");
                        d.attr("id", "tp_import_sliders"), e.attr("id", "tp_import_sliders_title"), b = jQuery(".ui-dialog-titlebar-close", c.dialog), jQuery(".ui-dialog-titlebar-close", c.dialog).hide()
                    }
                }), void 0 !== a.package && void 0 !== slider_package_uids[a.uid]) {
                a["page-creation"] = !1, delete a.package, rs_install_slider = {}, rs_install_ids = [];
                for (var d in slider_package_uids[a.uid])for (var e in slider_package_uids[a.uid][d])rs_install_slider[d] = {
                    uid: slider_package_uids[a.uid][d][e],
                    sid: e,
                    status: 0
                };
                var f = 0, g = 60, h = 30, i = setInterval(function () {
                    f++, f == h && jQuery("#import_dialog_box_action").append('<div class="import_failure">' + rev_lang.download_install_takes_longer + "</div>"), f == g && (jQuery("#import_dialog_box").append(rev_lang.download_failed_check_server), jQuery("#import_dialog_box").append("<div>" + rev_lang.aborting_import + "</div>"), jQuery("#import_dialog_box_action").html(""), b.show(), clearInterval(i));
                    var d = 0, e = 1, j = !0;
                    for (var k in rs_install_slider)1 === rs_install_slider[k].status && (d = 1), 2 !== rs_install_slider[k].status && (j = !1), 2 === rs_install_slider[k].status && e++;
                    if (e <= Object.keys(rs_install_slider).length ? jQuery("#install-slider-counter").css({width: e / Object.keys(rs_install_slider).length * 100 + "%"}) : jQuery("#install-slider-counter").width("100%"), 0 === d) {
                        f = 0;
                        for (var k in rs_install_slider)if (0 === rs_install_slider[k].status) {
                            a.uid = rs_install_slider[k].uid, jQuery("#import_dialog_box_action").html(slider_package_names[rs_install_slider[k].uid].name), rs_install_slider[k].status = 1, UniteAdminRev.ajaxRequest("import_slider_online_template_slidersview_new", a, function (a) {
                                if (rs_install_slider[k].status = 2, void 0 !== a.slider_id && a.slider_id > 0 && rs_install_ids.push(a.slider_id), a.view !== !1 && (rs_install_url = a.view), a.error.length > 0)for (var b in a.error)jQuery("#import_dialog_box").append("<div>" + a.error[b] + "</div>"); else for (var b in a.success);
                            }, !0);
                            break
                        }
                    }
                    j && (clearInterval(i), "true" === c ? (jQuery("#import_dialog_box").append("<div>" + rev_lang.create_draft + "</div>"), UniteAdminRev.ajaxRequest("create_draft_page", {slider_ids: rs_install_ids}, function (a) {
                        a.open !== !1 ? (jQuery("#import_dialog_box").append("<div>" + rev_lang.draft_created + "</div>"), window.open(a.open, "_blank")) : jQuery("#import_dialog_box").append("<div>" + rev_lang.draft_not_created + "</div>"), jQuery("#import_dialog_box").append("<div>" + rev_lang.slider_import_success_reload + "</div>"), rs_install_url !== !1 && (location.href = rs_install_url)
                    }, !0)) : (jQuery("#import_dialog_box").append("<div>" + rev_lang.slider_import_success_reload + "</div>"), rs_install_url !== !1 && (location.href = rs_install_url)))
                }, 1e3)
            } else {
                a["page-creation"] = c;
                var j = jQuery("#import_dialog_box").data("requested_slide");
                jQuery("#import_dialog_box").append("<div>" + j + "</div>"), UniteAdminRev.ajaxRequest("import_slider_online_template_slidersview_new", a, function (a) {
                    if (jQuery("#import_dialog_box_action").html(""), a.error.length > 0)for (var b in a.error)jQuery("#import_dialog_box").append("<div>" + a.error[b] + "</div>"); else {
                        for (var b in a.success)jQuery("#install-slider-counter").css({width: "100%"});
                        a.open !== !1 && (jQuery("#import_dialog_box").append("<div>" + rev_lang.draft_created + "</div>"), window.open(a.open, "_blank")), a.view !== !1 && (jQuery("#import_dialog_box").append("<div>" + rev_lang.slider_import_success_reload + "</div>"), location.href = a.view)
                    }
                }, !0)
            }
        };
        a.get_title_by_uid = function (a) {
            var b = jQuery('.template_slide_item_img[data-uid="' + a + '"]').data("title");
            return void 0 == b && (b = jQuery('.install_template_slider[data-uid="' + a + '"]').data("title")), void 0 == b && (b = jQuery('.template_slider_item_import[data-uid="' + a + '"]').data("title")),
            void 0 == b && (b = jQuery('.install_template_slider_package[data-uid="' + a + '"]').data("title")), b
        }, jQuery("body").on("click", ".add_template_slider_item", function () {
            var a = jQuery(this).data("sliderid");
            jQuery("#dialog_duplicate_slider").dialog({
                modal: !0,
                resizable: !1,
                title: "Import",
                width: 250,
                height: 200,
                closeOnEscape: !0,
                dialogClass: "tpdialogs",
                create: function (a) {
                    jQuery(a.target).parent().find(".ui-dialog-titlebar").addClass("tp-slider-new-dialog-title")
                },
                buttons: {
                    Close: function () {
                        jQuery(this).dialog("close")
                    }, Import: function () {
                        return "" != jQuery("#rs-duplicate-animation").val() && void UniteAdminRev.ajaxRequest("duplicate_slider", {
                                sliderid: a,
                                title: jQuery("#rs-duplicate-animation").val()
                            }, function (a) {
                                jQuery("#close-template").click()
                            })
                    }
                }
            })
        }), jQuery("body").on("click", ".add_template_slider_item_package", function () {
            var a = jQuery(this).data("uid");
            jQuery("#dialog_duplicate_slider_package").dialog({
                modal: !0,
                resizable: !1,
                title: "Import Package",
                width: 250,
                height: 200,
                closeOnEscape: !0,
                dialogClass: "tpdialogs",
                create: function (a) {
                    jQuery(a.target).parent().find(".ui-dialog-titlebar").addClass("tp-slider-new-dialog-title")
                },
                buttons: {
                    Close: function () {
                        jQuery(this).dialog("close")
                    }, "Import Package": function () {
                        return "" != jQuery("#rs-duplicate-prefix").val() && void UniteAdminRev.ajaxRequest("duplicate_slider_package", {
                                slideruid: a,
                                title: jQuery("#rs-duplicate-prefix").val()
                            }, function (a) {
                                jQuery("#close-template").click()
                            })
                    }
                }
            })
        }), jQuery(".input_import_slider").change(function () {
            "" !== jQuery(this).val() ? jQuery(".rev-import-slider-button").show() : jQuery(".rev-import-slider-button").hide()
        }), jQuery("#form-import-slider-local, #form-import-online-slider-local").submit(function (a) {
            var c = jQuery(this).find('input[name="import_file"]').val().split("\\").pop();
            void 0 !== jQuery("#dialog_import_slider").dialog("instance") && jQuery("#dialog_import_slider").dialog("close"), void 0 !== jQuery("#dialog_import_template_slider").dialog("instance") && jQuery("#dialog_import_template_slider").dialog("close"), jQuery("#dialog_import_template_slider_info").dialog({
                modal: !0,
                resizable: !1,
                width: 450,
                minHeight: 350,
                open: function (a, b) {
                    var c = jQuery(a.target).parent(), d = c.find(".ui-dialog-titlebar");
                    c.attr("id", "tp_import_sliders"), d.attr("id", "tp_import_sliders_title"), jQuery(".ui-dialog-titlebar-close", b.dialog).hide()
                }
            }), jQuery("#import_dialog_box").html(""), jQuery("#import_dialog_box").append("<div>" + c + "</div>");
            var d = 0, e = 60, f = 30, g = setInterval(function () {
                d++, d == f && jQuery("#import_dialog_box_action").append(rev_lang.download_install_takes_longer), d == e && (jQuery("#import_dialog_box").append(rev_lang.download_failed_check_server), jQuery("#import_dialog_box").append("<div>" + rev_lang.aborting_import + "</div>"), jQuery("#import_dialog_box_action").html(""), b.show(), clearInterval(g))
            }, 1e3)
        }), jQuery("#button_import_template_slider, #button_import_template_slider_b").click(function () {
            a.load_slider_template_html()
        }), jQuery("#button_import_slider").click(function () {
            return RS_DEMO ? (alert(rev_lang.not_available_in_demo), !1) : (jQuery(".rev-import-slider-button").hide(), jQuery(".input_import_slider").val(""), void jQuery("#dialog_import_slider").dialog({
                modal: !0,
                resizable: !1,
                width: 600,
                height: 400,
                closeOnEscape: !0,
                dialogClass: "tpdialogs",
                create: function (a) {
                    jQuery(a.target).parent().find(".ui-dialog-titlebar").addClass("tp-slider-new-dialog-title")
                },
                buttons: {
                    Close: function () {
                        jQuery(this).dialog("close")
                    }
                }
            }))
        }), jQuery(".button_delete_slider").click(function () {
            var a = this.id.replace("button_delete_", ""), b = jQuery("#slider_title_" + a).text();
            return 0 != confirm(rev_lang.really_want_to_delete + " '" + b + "' ?") && void UniteAdminRev.ajaxRequest("delete_slider_stay", {sliderid: a}, function () {
                    jQuery('li[data-id="' + a + '"]').remove()
                })
        }), jQuery(".button_duplicate_slider").click(function () {
            var a = this.id.replace("button_duplicate_", "");
            jQuery("#dialog_duplicate_slider").dialog({
                modal: !0,
                resizable: !1,
                width: 250,
                height: 200,
                closeOnEscape: !0,
                dialogClass: "tpdialogs",
                create: function (a) {
                    jQuery(a.target).parent().find(".ui-dialog-titlebar").addClass("tp-slider-new-dialog-title")
                },
                buttons: {
                    Close: function () {
                        jQuery(this).dialog("close")
                    }, Duplicate: function () {
                        return "" != jQuery("#rs-duplicate-animation").val() && void UniteAdminRev.ajaxRequest("duplicate_slider", {
                                sliderid: a,
                                title: jQuery("#rs-duplicate-animation").val()
                            }, function (a) {
                            })
                    }
                }
            })
        }), jQuery(".rev-toogle-fav").click(function () {
            var a = this.id.replace("reg-toggle-id-", "");
            UniteAdminRev.ajaxRequest("toggle_favorite", {id: a}, function () {
                var b = jQuery("#reg-toggle-id-" + a).find("i");
                b.hasClass("eg-icon-star-empty") ? (b.removeClass("eg-icon-star-empty"), b.addClass("eg-icon-star")) : (b.removeClass("eg-icon-star"), b.addClass("eg-icon-star-empty"))
            })
        }), jQuery(".button_slider_preview").click(function () {
            var a = this.id.replace("button_preview_", "");
            o(a)
        }), jQuery(".export_slider_overview").click(function () {
            var a = this.id.replace("export_slider_", ""), b = !1, c = ajaxurl + UniteAdminRev.return_ajaxurl_param() + "action=" + g_uniteDirPlugin + "_ajax_action&client_action=export_slider&dummy=" + b + "&nonce=" + g_revNonce;
            c += "&sliderid=" + a, location.href = c
        }), jQuery("body").on("click", ".rs-embed-slider", function () {
            var a = jQuery(this).closest("li.tls-slide").find(".tls-alias").text(),b=jQuery(this).closest("li.tls-slide").find(".tls-alias").prev().text();
            jQuery(".rs-dialog-embed-slider").find(".rs-example-alias").text(a),jQuery(".rs-dialog-embed-slider").find(".rs-example-title").text(b), jQuery(".rs-dialog-embed-slider").find(".rs-example-alias-1").text('[rev_slider alias="' + a + '"]'), jQuery(".rs-dialog-embed-slider").dialog({
                modal: !0,
                resizable: !1,
                minWidth: 850,
                minHeight: 300,
                closeOnEscape: !0,
                create: function (a) {
                    jQuery(a.target).parent().find(".ui-dialog-titlebar").addClass("tp-slider-new-dialog-title")
                }
            })
        }), jQuery(".export_slider_standalone").click(function () {
            var a = this.id.replace("export_slider_standalone_", ""), b = !1, c = ajaxurl + UniteAdminRev.return_ajaxurl_param() + "action=" + g_uniteDirPlugin + "_ajax_action&client_action=preview_slider&only_markup=true&dummy=" + b + "&nonce=" + g_revNonce;
            c += "&sliderid=" + a, location.href = c
        })
    };
    var o = function (a) {
        var b = jQuery("#rs-preview-form");
        jQuery("#rs-client-action").val("preview_slider"), jQuery("#preview_sliderid").val(a), jQuery("#preview_slider_markup").val("false"), b.submit(), jQuery("#rs-preview-wrapper").show(), jQuery(window).trigger("resize")
    }, p = function (a) {
        var b = [];
        return a.find(".icon_slide_lang").each(function () {
            var a = jQuery(this).data("lang");
            b.push(a)
        }), b
    }, q = function (a, b) {
        var c = p(a), d = 0;
        return jQuery("#langs_float_wrapper li.item_lang").each(function () {
            var a = jQuery(this), e = a.data("lang");
            jQuery.inArray(e, c);
            "add" != b && jQuery("#langs_float_wrapper li.operation_sap").hide(), jQuery.inArray(e, c) == -1 ? (d++, a.show(), "add" != b && jQuery("#langs_float_wrapper li.operation_sap").show()) : a.hide()
        }), d
    };
    a.initSlidesListViewPosts = function (a) {
        r(a), jQuery("#select_sortby").change(function () {
            jQuery("#slides_top_loader").show();
            var b = {};
            b.sliderID = a, b.sortby = jQuery(this).val(), UniteAdminRev.ajaxRequest("update_posts_sortby", b, function () {
                jQuery("#slides_top_loader").html("Updated, reloading page..."), location.reload(!0)
            })
        }), jQuery(".button_delete_slide").click(function () {
            var b = jQuery(this).data("slideid"), c = {slideID: b, sliderID: a};
            return 0 != confirm(g_messageDeleteSlide) && void UniteAdminRev.ajaxRequest("delete_slide", c)
        })
    };
    var r = function (a) {
        jQuery("#list_slides").sortable({
            axis: "y", handle: ".col-handle", update: function () {
                n(a)
            }
        }), jQuery("#list_slides .icon_state").click(function () {
            var b = jQuery(this), c = b.siblings(".state_loader"), d = b.data("slideid"), e = {
                slider_id: a,
                slide_id: d
            };
            b.hide(), c.show(), UniteAdminRev.ajaxRequest("toggle_slide_state", e, function (a) {
                b.show(), c.hide();
                var d = a.state;
                "published" == d ? b.removeClass("state_unpublished").addClass("state_published").prop("title", "Unpublish Slide") : b.removeClass("state_published").addClass("state_unpublished").prop("title", "Publish Slide")
            })
        }), jQuery(".col-image .slide_image").click(function () {
            var b = this.id.replace("slide_image_", "");
            UniteAdminRev.openAddImageDialog(g_messageChangeImage, function (c, d) {
                var e = {slider_id: a, slide_id: b, url_image: c, image_id: d};
                UniteAdminRev.ajaxRequest("change_slide_image", e)
            })
        }).tipsy({gravity: "s", delayIn: 70})
    };
    a.initSlidesListView = function (a) {
        r(a), jQuery("#button_new_slide, #button_new_slide_top").click(function () {
            var b = jQuery("#button_new_slide").data("dialogtitle");
            UniteAdminRev.openAddImageDialog(b, function (b) {
                var c = {sliderid: a, obj: b};
                UniteAdminRev.ajaxRequest("add_slide", c)
            }, !0)
        }), jQuery("#button_new_slide_transparent, #button_new_slide_transparent_top").click(function () {
            jQuery(this).hide(), jQuery(".new_trans_slide_loader").show();
            var b = {sliderid: a};
            UniteAdminRev.ajaxRequest("add_slide", b)
        }), jQuery(".button_duplicate_slide").click(function () {
            var b = this.id.replace("button_duplicate_slide_", ""), c = {slideID: b, sliderID: a};
            UniteAdminRev.ajaxRequest("duplicate_slide", c)
        }), jQuery(".button_copy_slide").click(function () {
            if (jQuery(this).hasClass("button-disabled"))return !1;
            var b = jQuery("#dialog_copy_move"), d = (b.data("textclose"), b.data("textupdate")), e = jQuery(this), f = {};
            f[d] = function () {
                var b = e.attr("id").replace("button_copy_slide_", ""), c = jQuery("#selectSliders").val(), d = "copy";
                "checked" == jQuery("#radio_move").prop("checked") && (d = "move");
                var f = {slideID: b, sliderID: a, targetSliderID: c, operation: d}, g = e.siblings(".loader_copy");
                e.hide(), g.show(), UniteAdminRev.ajaxRequest("copy_move_slide", f), jQuery(this).dialog("close")
            }, jQuery("#dialog_copy_move").dialog({
                modal: !0,
                resizable: !1,
                width: 400,
                height: 300,
                closeOnEscape: !0,
                dialogClass: "tpdialogs",
                create: function (a) {
                    jQuery(a.target).parent().find(".ui-dialog-titlebar").addClass("tp-slider-new-dialog-title")
                },
                buttons: f
            })
        }), jQuery(".button_delete_slide").click(function () {
            var b = jQuery(this).data("slideid"), c = {slideID: b, sliderID: a};
            if (0 == confirm("Delete this slide?"))return !1;
            var d = jQuery(this), e = d.siblings(".loader_delete");
            d.hide(), e.show(), UniteAdminRev.ajaxRequest("delete_slide", c)
        }), jQuery("#list_slides .icon_slide_preview").click(function () {
            var a = jQuery(this).data("slideid");
            s(a, !1)
        })
    }, a.saveEditSlide = function (a, b) {
        b || (b = !1), tpLayerTimelinesRev.updateZIndexByOrder(), UniteLayersRev.setRowZoneOrders(), tpLayerTimelinesRev.organiseGroupsAndLayer();
        var c = UniteLayersRev.getLayers(), d = {};
        for (key in c)c[key].layer_unavailable !== !0 && c[key].deleted !== !0 && (d[key] = jQuery.extend(!0, {}, c[key]), "undefined" != typeof d[key].layer_unavailable && delete d[key].layer_unavailable, "undefined" != typeof d[key].deleted && delete d[key].deleted, "undefined" != typeof d[key].references && delete d[key].references, "undefined" != typeof d[key].createdOnInit && delete d[key].createdOnInit);
        JSON && JSON.stringify && (d = JSON.stringify(d));
        var e = {slideid: a, layers: d};
        if (e.params = RevSliderSettings.getSettingsObject("form_slide_params"), !b) {
            e.params.slide_bg_color = jQuery("#slide_bg_color").val(), e.params.slide_bg_external = jQuery("#slide_bg_external").val(), e.params.bg_fit = jQuery("#slide_bg_fit").val(), e.params.bg_fit_x = jQuery("input[name='bg_fit_x']").val(), e.params.bg_fit_y = jQuery("input[name='bg_fit_y']").val(), e.params.bg_repeat = jQuery("#slide_bg_repeat").val(), e.params.bg_position = jQuery("#slide_bg_position").val(), e.params.bg_position_x = jQuery("input[name='bg_position_x']").val(), e.params.bg_position_y = jQuery("input[name='bg_position_y']").val(), e.params.bg_end_position_x = jQuery("input[name='bg_end_position_x']").val(), e.params.bg_end_position_y = jQuery("input[name='bg_end_position_y']").val();
            var f = u();
            if ("object" == typeof f && !jQuery.isEmptyObject(f))for (key in f)e.params[key] = f[key];
            e.params.kenburn_effect = jQuery("input[name='kenburn_effect']").attr("checked") === "checked" ? "on" : "off", e.params.kb_start_fit = jQuery("input[name='kb_start_fit']").val(), e.params.kb_end_fit = jQuery("input[name='kb_end_fit']").val(), e.params.bg_end_position = jQuery("select[name='bg_end_position']").val(), e.params.kb_duration = jQuery("input[name='kb_duration']").val(), e.params.kb_easing = jQuery("select[name='kb_easing']").val(), e.params.slide_transition = [], e.params.slot_amount = [], e.params.transition_rotation = [], e.params.transition_duration = [], e.params.transition_ease_in = [], e.params.transition_ease_out = [], jQuery(".slide-trans-cur-ul li").each(function () {
                e.params.slide_transition.push(jQuery(this).data("animval")), e.params.slot_amount.push(jQuery(this).data("slot")), e.params.transition_rotation.push(jQuery(this).data("rotation")), e.params.transition_duration.push(jQuery(this).data("duration")), e.params.transition_ease_in.push(jQuery(this).data("ease_in")), e.params.transition_ease_out.push(jQuery(this).data("ease_out"))
            });
            var g = jQuery(".bgsrcchanger:checked").val();
            if (("vimeo" == g || "html5" == g || "youtube" == g) && !("undefined" != typeof e.params.image_id && 0 != parseInt(e.params.image_id) && "" != e.params.image_id || "undefined" != typeof e.params.image_url && "" != e.params.image_url))return alert(rev_lang.cover_image_needs_to_be_set), !1;
            var h = jQuery('input[name="rs-gallery-type"]').val();
            switch (h) {
                case"gallery":
                    break;
                case"posts":
                case"woocommerce":
                case"facebook":
                case"twitter":
                case"instagram":
                case"flickr":
                case"youtube":
                case"vimeo":
                    e.params.image_url = ""
            }
        }
        e.settings = {};
        var i = [];
        jQuery("#hor-css-linear .helplines").each(function () {
            i.push(jQuery(this).css("left"))
        });
        var j = [];
        jQuery("#ver-css-linear .helplines").each(function () {
            j.push(jQuery(this).css("top"))
        }), e.settings.hor_lines = i, e.settings.ver_lines = j, jQuery.each(UniteLayersRev.addon_callbacks, function (a, b) {
            var c = b.callback, d = b.environment, f = b.function_position;
            "saveEditSlide" === d && "data" == f && (e = c(e))
        }), e.obj_favorites = favoriteObjectsList, b ? (UniteAdminRev.setAjaxHideButtonID("button_save_static_slide,button_save_static_slide-tb"), UniteAdminRev.setAjaxLoaderID("loader_update"), UniteAdminRev.setSuccessMessageID("update_slide_success"), UniteAdminRev.ajaxRequest("update_static_slide", e)) : (UniteAdminRev.setAjaxHideButtonID("button_save_slide,button_save_slide-tb"), UniteAdminRev.setAjaxLoaderID("loader_update"), UniteAdminRev.setSuccessMessageID("update_slide_success"), UniteAdminRev.ajaxRequest("update_slide", e))
    }, this.initEditSlideView = function (b, c, d) {
        jQuery("body").on("click", ".rs-reload-shop", function () {
            confirm(rev_lang.unsaved_data_will_be_lost_proceed) && (showWaitAMinute({
                fadeIn: 300,
                text: rev_lang.please_wait_a_moment
            }), location.href = window.location.href + "&update_shop")
        }), jQuery("body").on("click", ".install_template_slide", function () {
            var a = jQuery(this);
            return jQuery("#import_dialog_box").data("requested_slide", a.data("title")), a.hasClass("deny_download") ? (alert(rev_lang.this_template_requires_version + " " + a.data("versionneed") + " " + rev_lang.of_slider_revolution), !1) : void(confirm(rev_lang.unsaved_data_will_be_lost_proceed) && (jQuery(".rs-zip-name").text(a.data("zipname")), jQuery(".rs-uid").val(a.data("uid")), jQuery(".rs-slide-number").val(a.data("slidenumber")), jQuery(".rs-slider-id").val(c), d ? jQuery(".rs-slide-id").val("static_" + c) : jQuery(".rs-slide-id").val(b), jQuery("#dialog_import_template_slide_from").dialog({
                modal: !0,
                resizable: !1,
                create: function (a) {
                    jQuery(a.target).parent().find(".ui-dialog-titlebar").addClass("tp-slider-new-dialog-title")
                },
                buttons: {
                    Local: function () {
                        jQuery(".input_import_slider").val(""), jQuery(".rs-import-slider-button").hide(), jQuery("#dialog_import_template_slide").dialog({
                            modal: !0,
                            resizable: !1,
                            width: 600,
                            height: 350,
                            closeOnEscape: !0,
                            dialogClass: "tpdialogs",
                            create: function (a) {
                                jQuery(a.target).parent().find(".ui-dialog-titlebar").addClass("tp-slider-new-dialog-title")
                            },
                            buttons: {
                                Close: function () {
                                    jQuery(this).dialog("close")
                                }
                            }
                        })
                    }, Online: function () {
                        rs_plugin_validated ? (showWaitAMinute({
                            fadeIn: 300,
                            text: rev_lang.please_wait_a_moment
                        }), e(), jQuery("#rs-import-slide-template-from-server").submit(), jQuery(this).dialog("close")) : alert(rev_lang.this_feature_only_if_activated)
                    }
                }
            }), jQuery("#close-template").click()))
        }), jQuery("#dialog_import_template_slide").submit(function (a) {
            e()
        });
        var e = function () {
            jQuery("#import_dialog_box").html(""), jQuery("#dialog_import_template_slide_info").dialog({
                modal: !0,
                resizable: !1,
                width: 450,
                minHeight: 350,
                open: function (a, b) {
                    var c = jQuery(a.target).parent(), d = c.find(".ui-dialog-titlebar");
                    c.attr("id", "tp_import_sliders"), d.attr("id", "tp_import_sliders_title"), jQuery(".ui-dialog-titlebar-close", b.dialog).hide()
                }
            });
            var a = 0, b = 60, c = 90;
            jQuery("#import_dialog_box").append(jQuery("#import_dialog_box").data("requested_slide"));
            var d = setInterval(function () {
                a++, a == b && jQuery("#import_dialog_box_action").append(rev_lang.download_install_takes_longer), a == c && (jQuery("#import_dialog_box").append(rev_lang.download_failed_check_server), jQuery("#import_dialog_box").append("<div>" + rev_lang.aborting_import + "</div>"), jQuery("#import_dialog_box_action").html(""), clearInterval(d))
            }, 1e3)
        };
        jQuery(".tp-accordion").click(function () {
            var a = jQuery(this);
            a.hasClass("tpa-closed") ? (a.parent().parent().parent().find(".tp-closeifotheropen").each(function () {
                jQuery(this).slideUp(300), jQuery(this).parent().find(".tp-accordion").addClass("tpa-closed").addClass("box_closed").find(".postbox-arrow2").html("+")
            }), a.parent().find(".toggled-content").slideDown(300), a.removeClass("tpa-closed").removeClass("box_closed"), a.find(".postbox-arrow2").html("-")) : (a.parent().find(".toggled-content").slideUp(300), a.addClass("tpa-closed").addClass("box_closed"), a.find(".postbox-arrow2").html("+"))
        }), jQuery(".mw960").each(function () {
            var a = jQuery("#divLayers").width();
            a < 960 && (a = 960), jQuery(this).css({maxWidth: a + "px"})
        }), jQuery("#button_sort_depth").on("click", function () {
            jQuery(".layer_sortbox").addClass("depthselected"), jQuery(".layer_sortbox").removeClass("timeselected")
        }), jQuery("#button_sort_time").on("click", function () {
            jQuery(".layer_sortbox").removeClass("depthselected"), jQuery(".layer_sortbox").addClass("timeselected")
        }), jQuery("#link_add_slide").click(function () {
            var a = {sliderid: c};
            jQuery("#loader_add_slide").show(), UniteAdminRev.ajaxRequest("add_slide_fromslideview", a)
        }), jQuery("#link_add_bulk_slide").click(function () {
            UniteAdminRev.openAddImageDialog(rev_lang.add_bulk_slides, function (a) {
                var b = {sliderid: c, obj: a};
                UniteAdminRev.ajaxRequest("add_bulk_slide", b)
            }, !0)
        }), jQuery("body").on("click", ".add_template_slide_item, .add_user_template_slide_item", function () {
            if (confirm(rev_lang.unsaved_data_will_be_lost_proceed)) {
                var a = {slider_id: c};
                a.slide_id = jQuery(this).data("slideid"), d ? a.redirect_id = "static_" + c : a.redirect_id = b, UniteAdminRev.ajaxRequest("copy_slide_to_slider", a, function () {
                    jQuery("#close-template").click()
                })
            }
        }), jQuery("body").on("click", ".delete_user_template_slide_item", function () {
            if (confirm(rev_lang.delete_user_slide)) {
                var a = {slider_id: c};
                a.slide_id = jQuery(this).data("slideid"), jQuery(this).closest(".template_group_wrappers").remove(), UniteAdminRev.ajaxRequest("delete_template_slide", a, function () {
                })
            }
        }), jQuery("#button_save_slide").click(function () {
            a.saveEditSlide(b), UniteLayersRev.set_save_needed(!1)
        }), jQuery("#button_save_slide-tb").click(function () {
            a.saveEditSlide(b), UniteLayersRev.set_save_needed(!1)
        }), jQuery("#button_save_static_slide").click(function () {
            a.saveEditSlide(b, !0), UniteLayersRev.set_save_needed(!1)
        }), jQuery("#button_save_static_slide-tb").click(function () {
            a.saveEditSlide(b, !0), UniteLayersRev.set_save_needed(!1)
        }), jQuery("#button_change_image").click(function () {
            UniteAdminRev.openAddImageDialog(rev_lang.select_slide_img, function (a, b) {
                void 0 == b && (b = ""), jQuery("#divbgholder").css("background-image", "url(" + a + ")"), jQuery("#slide_selector .list_slide_links li.selected .slide-media-container ").css("background-image", "url(" + a + ")"), jQuery("#image_url").val(a), jQuery("#image_id").val(b), UniteLayersRev.changeSlotBGs(), jQuery(".bgsrcchanger:checked").click(), jQuery('input[name="kenburn_effect"]').attr("checked") === "checked" && jQuery('input[name="kb_start_fit"]').change()
            })
        }), jQuery(".button_change_video").click(function () {
            var a = jQuery(this).data("inptarget");
            UniteAdminRev.openAddVideoDialog(rev_lang.select_slide_video, function (b, c) {
                jQuery('input[name="' + a + '"]').val(b), jQuery("#html5_url_audio, #html5_url_ogv, #html5_url_webm, #html5_url_mp4").change()
            })
        }), jQuery("#link_hide_options").click(function () {
            1 == jQuery("#slide_params_holder").is(":visible") ? (jQuery("#slide_params_holder").hide("slow"), jQuery(this).text(rev_lang.show_slide_opt).addClass("link-selected")) : (jQuery("#slide_params_holder").show("slow"), jQuery(this).text(rev_lang.hide_slide_opt).removeClass("link-selected"))
        }), jQuery("#button_preview_slide").click(function () {
            s(b, !0)
        }), jQuery("#button_preview_slide-tb").click(function () {
            s(b, !0)
        }), jQuery("#radio_back_image, #radio_back_trans, #radio_back_solid, #radio_back_external, #radio_back_youtube, #radio_back_htmlvideo").click(function () {
            var a = jQuery("#background_type").val(), b = jQuery(this).data("bgtype");
            return a == b || ("image" == b ? jQuery("#button_change_image").removeClass("button-disabled") : jQuery("#button_change_external").addClass("button-disabled"), "solid" == b ? jQuery("#slide_bg_color").removeClass("disabled").prop("disabled", "") : jQuery("#slide_bg_color").addClass("disabled").prop("disabled", "disabled"), "external" == b ? (jQuery("#slide_bg_external").removeClass("disabled").prop("disabled", ""), jQuery("#button_change_image").removeClass("button-disabled"), jQuery("#button_change_external").removeClass("button-disabled")) : (jQuery("#slide_bg_external").addClass("disabled").prop("disabled", "disabled"), jQuery("#button_change_external").addClass("button-disabled")), jQuery("#background_type").val(b), void t(b))
        }), jQuery("#button_change_external").click(function () {
            var a = jQuery("#radio_back_external:checked").data("bgtype");
            "external" == a && (jQuery("#slide_bg_external").removeClass("disabled").prop("disabled", ""), jQuery("#button_change_image").removeClass("button-disabled"), t(a), jQuery('input[name="kenburn_effect"]').attr("checked") === "checked" && jQuery('input[name="kb_start_fit"]').change(), UniteLayersRev.changeSlotBGs())
        }), UniteAdminRev.setColorPickerCallback(function () {
            var a = jQuery("#background_type").val();
            if ("solid" == a) {
                var b = jQuery("#slide_bg_color").val();
                jQuery("#divbgholder").css("background-color", b), jQuery("#slide_selector .list_slide_links li.selected .slide-media-container ").css({backgroundColor: b})
            }
        }), jQuery("#title").on("input", function (a) {
            jQuery(".slide_title").text(jQuery("#title").val())
        }), jQuery(".list_slide_links").sortable({
            items: "li:not(.eg-drag-disabled)", update: function () {
                f(c)
            }
        });
        var f = function (a) {
            var b = jQuery(".list_slide_links").sortable("toArray"), c = [];
            jQuery(b).each(function (a, b) {
                var d = b.replace("slidelist_item_", "");
                c.push(d)
            });
            var d = {arrIDs: c, sliderID: a};
            jQuery("#loader_add_slide").show(), UniteAdminRev.ajaxRequest("update_slides_order", d, function () {
                jQuery("#loader_add_slide").hide()
            })
        };
        jQuery(".inputDatePicker").datepicker({dateFormat: "dd-mm-yy 00:00"}), jQuery("#button_delete_slide").click(function () {
            var a = {slideID: b, sliderID: c};
            return 0 != confirm(g_messageDeleteSlide) && void UniteAdminRev.ajaxRequest("delete_slide", a)
        }), "false" == jQuery('input[name="load_googlefont"]:checked').val() && (jQuery("#load_googlefont_row").siblings(".spanSettingsStaticText").remove(), jQuery("#load_googlefont_row").remove(), jQuery("#google_font_row").remove(), jQuery("#load_googlefont").closest(".postbox.unite-postbox").hide()), jQuery("body").on("click", ".slide-remove", function () {
            var a = jQuery(this).closest("li").attr("id").replace("slidelist_item_", ""), b = {slideID: a, sliderID: c};
            if (0 == confirm("Delete this slide?"))return !1;
            var d = jQuery(this), e = d.siblings(".loader_delete");
            d.hide(), e.show();
            var f = jQuery(this).closest("li"), g = curSlideID == a ? "delete_slide" : "delete_slide_stay";
            UniteAdminRev.ajaxRequest(g, b, function (a) {
                f.remove()
            })
        }), jQuery("body").on("click", ".slide-published.pubclickable, .slide-unpublished.pubclickable", function () {
            var a = jQuery(this).closest("li"), b = a.attr("id").replace("slidelist_item_", ""), d = {
                slider_id: c,
                slide_id: b
            };
            return objButton = jQuery(this), a.find(".slide-published").fadeOut(200), a.find(".slide-unpublished").fadeOut(200), UniteAdminRev.ajaxRequest("toggle_slide_state", d, function (c) {
                var d = c.state;
                "published" == d ? (a.find(".slide-published").removeClass("pubclickable").fadeIn(200), a.find(".slide-unpublished").addClass("pubclickable").fadeIn(200), curSlideID == b && jQuery('select[name="state"] option[value="published"]').attr("selected", !0)) : (a.find(".slide-published").addClass("pubclickable").fadeIn(200), a.find(".slide-unpublished").removeClass("pubclickable").fadeIn(200), curSlideID == b && jQuery('select[name="state"] option[value="unpublished"]').attr("selected", !0))
            }), !1
        }), jQuery("body").on("click", ".slide-hero-unpublished.pubclickable", function () {
            var a = jQuery(this).closest("li"), b = jQuery(this).closest("ul"), d = a.attr("id").replace("slidelist_item_", ""), e = {
                slider_id: c,
                slide_id: d
            };
            return objButton = jQuery(this), UniteAdminRev.ajaxRequest("toggle_hero_slide", e, function (c) {
                b.find(".slide-hero-published").removeClass("slide-hero-published").addClass("slide-hero-unpublished").addClass("pubclickable"), a.find(".slide-hero-unpublished").removeClass("pubclickable").removeClass("slide-hero-unpublished").addClass("slide-hero-published")
            }), !1
        }), jQuery("body").on("click", ".slide-duplicate", function () {
            var a = jQuery(this).closest("li"), b = a.attr("id").replace("slidelist_item_", ""), d = {
                slider_id: c,
                slide_id: b
            }, e = jQuery(this);
            e.hide();
            var d = {slideID: b, sliderID: c};
            UniteAdminRev.ajaxRequest("duplicate_slide_stay", d, function (c) {
                if (e.show(), 1 == c.success) {
                    var d = a.clone(!0).insertAfter(a);
                    d.attr("id", "slidelist_item_" + c.id), d.hasClass("selected") && (d.removeClass("selected"), jQuery('<a href="#" class="slide-link-toolbar-button slide-moveto"><span class=""><i class="eg-icon-forward"></i><span>' + rev_lang.copy_move + "</span></span></a>").insertAfter(d.find(".slide-remove")));
                    var f = d.find("a").first().attr("href");
                    f = "javascript:void(0)" == f ? "?page=revslider&view=slide&id=" + c.id : f.replace("id=" + b, "id=" + c.id), d.find("a").first().attr("href", f);
                    var g = 0;
                    jQuery(".list_slide_links li").each(function () {
                        jQuery(this).find(".slide-link-nr").text("#" + g), g++
                    })
                }
            })
        }), jQuery("body").on("click", ".slide-moveto", function () {
            var a = jQuery(this).closest("li"), e = jQuery(this), f = jQuery("#dialog_copy_move"), h = (f.data("textclose"), f.data("textupdate")), e = jQuery(this), i = {};
            i[h] = function () {
                var b = a.attr("id").replace("slidelist_item_", ""), d = jQuery("#selectSliders").val(), f = "copy";
                "checked" != jQuery("#radio_move").prop("checked") && 1 != jQuery("#radio_move").prop("checked") || (f = "move");
                var g = {slideID: b, sliderID: c, targetSliderID: d, operation: f};
                e.siblings(".loader_copy");
                e.hide(), UniteAdminRev.ajaxRequest("copy_move_slide_stay", g, function (b) {
                    e.show(), "move" == f && a.remove()
                }), jQuery(this).dialog("close")
            }, jQuery("#dialog_copy_move").dialog({
                modal: !0,
                resizable: !1,
                width: 400,
                height: 300,
                closeOnEscape: !0,
                dialogClass: "tpdialogs",
                create: function (a) {
                    jQuery(a.target).parent().find(".ui-dialog-titlebar").addClass("tp-slider-new-dialog-title")
                },
                buttons: i
            })
        }), jQuery("body").on("click", ".slide-add-as-template", function () {
            if (confirm(rev_lang.unsaved_changes_will_not_be_added)) {
                var a = jQuery(this).closest("li"), b = prompt(rev_lang.please_enter_a_slide_title, a.find(".slidetitleinput").val()), c = jQuery('input[name="rs-grid-width"]').val(), d = jQuery('input[name="rs-grid-height"]').val(), e = a.attr("id").replace("slidelist_item_", ""), f = {
                    width: c,
                    height: d
                };
                if (null != b) {
                    var g = jQuery(this);
                    g.hide();
                    var h = {slideID: e, title: b, settings: f};
                    UniteAdminRev.ajaxRequest("add_slide_to_template", h, function (a) {
                        g.show(), jQuery("#template_area").replaceWith(a.HTML), templateSelectorHandling()
                    })
                }
            }
        }), jQuery("#rev_lang_list").delegate(".icon_slide_lang, .icon_slide_lang_add", "click", function (a) {
            a.stopPropagation();
            var b = UniteAdminRev.getAbsolutePos(this), c = b[0] - 135, d = b[1] - 60, e = jQuery(this), f = e.data("operation"), g = e.data("isparent");
            "add" == f ? jQuery("#langs_float_wrapper .item_operation").hide() : (jQuery("#langs_float_wrapper .item_operation").show(), 1 == g && jQuery("#langs_float_wrapper .item_operation.operation_delete").hide());
            var h = e.parents(".list_slide_icons");
            q(h, f), jQuery("#langs_float_wrapper").show().css({
                left: c,
                top: d
            }), jQuery("#langs_float_wrapper").data("iconid", this.id)
        }), jQuery("body").click(function () {
            jQuery("#langs_float_wrapper").hide()
        }), jQuery("#slides_langs_float li a").click(function () {
            var a = jQuery(this), b = a.data("lang"), d = jQuery("#langs_float_wrapper").data("iconid");
            if (!d)return !0;
            var e = jQuery("#" + d), f = e.parents(".list_slide_icons"), g = a.data("operation");
            void 0 != g && g || (g = e.data("operation")), void 0 != g && g || (g = "update");
            var h = e.data("lang"), i = e.data("slideid"), j = e.data("origid");
            if (h == b)return !0;
            if ("preview" != g && (e.siblings(".icon_lang_loader").show(), e.hide()), "edit" == g) {
                var k = g_patternViewSlide.replace("[slideid]", i);
                return location.href = k, !0
            }
            if ("preview" == g)return o(c), !0;
            "delete" != g && "update" != g || (j = i);
            var l = {sliderid: c, slideid: j, lang: b, operation: g};
            UniteAdminRev.ajaxRequest("slide_lang_operation", l, function (a) {
                switch (e.siblings(".icon_lang_loader").hide(), a.operation) {
                    case"update":
                        e.attr("src", a.url_icon), e.attr("title", a.title), e.data("lang", b), e.show();
                        break;
                    case"add":
                        e.show(), e.parent().before(a.html), 1 == a.isAll && f.find(".icon_slide_lang_add").hide();
                        break;
                    case"delete":
                        e.parent().remove(), f.find(".icon_slide_lang_add").show()
                }
            })
        }), jQuery(".tp-moderncheckbox").each(function () {
            RevSliderSettings.onoffStatus(jQuery(this))
        }), RevSliderSettings.onoffStatus(jQuery('input[name="hideslideonmobile"]')), RevSliderSettings.onoffStatus(jQuery('input[name="stream_do_cover"]')), RevSliderSettings.onoffStatus(jQuery('input[name="stream_do_cover_both"]')), RevSliderSettings.onoffStatus(jQuery('input[name="kenburn_effect"]')), RevSliderSettings.onoffStatus(jQuery('input[name="save_performance"]')), RevSliderSettings.onoffStatus(jQuery('input[name="video_force_cover"]')), RevSliderSettings.onoffStatus(jQuery('input[name="video_nextslide"]')), RevSliderSettings.onoffStatus(jQuery('input[name="video_allowfullscreen"]')), RevSliderSettings.onoffStatus(jQuery('input[name="video_force_rewind"]')), RevSliderSettings.onoffStatus(jQuery('input[name="video_mute"]')), RevSliderSettings.onoffStatus(jQuery('input[name="thumb_for_admin"]')), jQuery("#video_force_cover").change(function () {
            jQuery(this).attr("checked") === "checked" ? jQuery("#video_dotted_overlay_wrap").show() : jQuery("#video_dotted_overlay_wrap").hide()
        }), jQuery("#video_force_cover").change()
    };
    var s = function (a, b) {
        void 0 === b && (b = !0);
        var c = jQuery("#rs-preview-form"), d = {slideid: a};
        if (1 == b) {
            d.params = RevSliderSettings.getSettingsObject("form_slide_params"), d.params.slide_bg_color = jQuery("#slide_bg_color").val(), d.params.slide_bg_external = jQuery("#slide_bg_external").val(), d.params.bg_fit = jQuery("#slide_bg_fit").val(), d.params.bg_fit_x = jQuery("input[name='bg_fit_x']").val(), d.params.bg_fit_y = jQuery("input[name='bg_fit_y']").val(), d.params.bg_repeat = jQuery("#slide_bg_repeat").val(), d.params.bg_position = jQuery("#slide_bg_position").val(), d.params.bg_position_x = jQuery("input[name='bg_position_x']").val(), d.params.bg_position_y = jQuery("input[name='bg_position_y']").val(), d.params.bg_end_position_x = jQuery("input[name='bg_end_position_x']").val(), d.params.bg_end_position_y = jQuery("input[name='bg_end_position_y']").val(), d.params.kenburn_effect = jQuery("input[name='kenburn_effect']").attr("checked") === "checked" ? "on" : "off", d.params.kb_start_fit = jQuery("input[name='kb_start_fit']").val(), d.params.kb_end_fit = jQuery("input[name='kb_end_fit']").val(), d.params.bg_end_position = jQuery("select[name='bg_end_position']").val(), d.params.kb_duration = jQuery("input[name='kb_duration']").val(), d.params.kb_easing = jQuery("select[name='kb_easing']").val();
            var e = UniteLayersRev.getLayers(), f = {};
            for (key in e)e[key].layer_unavailable !== !0 && e[key].deleted !== !0 && (f[key] = jQuery.extend(!0, {}, e[key]), "undefined" != typeof f[key].layer_unavailable && delete f[key].layer_unavailable, "undefined" != typeof f[key].deleted && delete f[key].deleted, "undefined" != typeof f[key].references && delete f[key].references, "undefined" != typeof f[key].createdOnInit && delete f[key].createdOnInit);
            d.layers = f
        }
        var g = JSON.stringify(d);
        jQuery("#preview-slide-data").val(g), jQuery("#rs-client-action").val("preview_slide"), c.submit(), jQuery("#rs-preview-wrapper").show(), jQuery(window).trigger("resize")
    }, t = function (a) {
        switch (a) {
            case"image":
                var b = jQuery("#image_url").val();
                jQuery("#divbgholder").css("background-image", "url('" + b + "')"), jQuery("#divbgholder").css("background-color", "transparent"), jQuery("#divbgholder").removeClass("trans_bg"), jQuery('input[name="kenburn_effect"]').attr("checked") === "checked" && jQuery('input[name="kb_start_fit"]').change();
                break;
            case"trans":
                jQuery("#divbgholder").css("background-image", "none"), jQuery("#divbgholder").css("background-color", "transparent"), jQuery("#divbgholder").addClass("trans_bg");
                break;
            case"solid":
                jQuery("#divbgholder").css("background-image", "none"), jQuery("#divbgholder").removeClass("trans_bg");
                var c = jQuery("#slide_bg_color").val();
                jQuery("#divbgholder").css("background-color", c), jQuery("#slide_selector .list_slide_links li.selected .slide-media-container ").css({backgroundColor: c});
                break;
            case"external":
                var b = jQuery("#slide_bg_external").val();
                jQuery("#divbgholder").css("background-image", "url('" + b + "')"), jQuery("#divbgholder").css("background-color", "transparent"), jQuery("#divbgholder").removeClass("trans_bg"), jQuery('input[name="kenburn_effect"]').attr("checked") === "checked" && jQuery('input[name="kb_start_fit"]').change();
                break;
            case"youtube":
                var b = jQuery("#image_url").val();
                jQuery("#divbgholder").css("background-image", "url('" + b + "')"), jQuery("#divbgholder").css("background-color", "transparent"), jQuery("#divbgholder").removeClass("trans_bg");
                break;
            case"html5":
                var b = jQuery("#image_url").val();
                jQuery("#divbgholder").css("background-image", "url('" + b + "')"), jQuery("#divbgholder").css("background-color", "transparent"), jQuery("#divbgholder").removeClass("trans_bg")
        }
    }, u = function () {
        var a = new Object;
        return a.bg_fit = jQuery("#slide_bg_fit").val(), "percentage" == a.bg_fit && (a.bg_fit_x = jQuery('input[name="bg_fit_x"]').val(), a.bg_fit_y = jQuery('input[name="bg_fit_y"]').val()), a.bg_position = jQuery("#slide_bg_position").val(), "percentage" == a.bg_position && (a.bg_position_x = jQuery('input[name="bg_position_x"]').val(), a.bg_position_y = jQuery('input[name="bg_position_y"]').val()), a.bg_end_position = jQuery("#slide_bg_end_position").val(), "percentage" == a.bg_end_position && (a.bg_end_position_x = jQuery('input[name="bg_end_position_x"]').val(), a.bg_end_position_y = jQuery('input[name="bg_end_position_y"]').val()), a.bg_repeat = jQuery("#slide_bg_repeat").val(), a
    }, v = null, w = null, x = null, y = null, A = null;
    a.setStaticCssCaptionsUrl = function (a) {
        A = a
    }, a.getUrlStaticCssCaptions = function () {
        return A
    }, a.initGlobalStyles = function () {
        B(), C()
    }, a.setCodeMirrorStaticEditor = function () {
        w = CodeMirror.fromTextArea(document.getElementById("textarea_edit_static"), {lineNumbers: !0})
    }, a.setCodeMirrorSliderEditor = function () {
        x = CodeMirror.fromTextArea(document.getElementById("textarea_show_slider_styles"), {lineNumbers: !0})
    }, a.setCodeMirrorSliderEditorJS = function () {
        y = CodeMirror.fromTextArea(document.getElementById("textarea_show_slider_javascript"), {lineNumbers: !0})
    }, a.setCodeMirrorDynamicEditor = function () {
        v = CodeMirror.fromTextArea(document.getElementById("textarea_show_dynamic_styles"), {
            lineNumbers: !0,
            readOnly: !0
        })
    };
    var B = function () {
        jQuery("#css-static-accordion").accordion({
            heightStyle: "content", activate: function (a, b) {
                null != x && x.refresh(), null != y && y.refresh(), null != w && w.refresh(), null != v && v.refresh()
            }
        })
    }, C = function () {
        jQuery("#button_edit_css_global").click(function () {
            jQuery("#css-static-accordion").accordion({active: 1}), UniteAdminRev.ajaxRequest("get_slider_custom_css_js", {slider_id: curSliderID}, function (a) {
                var b = a.css, c = a.js;
                null != x ? x.setValue(b) : (jQuery("#textarea_show_slider_styles").val(b), setTimeout("RevSliderAdmin.setCodeMirrorSliderEditor()", 500)), null != y ? y.setValue(c) : (jQuery("#textarea_show_slider_javascript").val(c), setTimeout("RevSliderAdmin.setCodeMirrorSliderEditorJS()", 500))
            }, !1), UniteAdminRev.ajaxRequest("get_static_css", "", function (a) {
                var b = a.data;
                null != w ? w.setValue(b) : (jQuery("#textarea_edit_static").val(b), setTimeout("RevSliderAdmin.setCodeMirrorStaticEditor()", 500))
            }), UniteAdminRev.ajaxRequest("get_dynamic_css", "", function (a) {
                var b = a.data;
                null != v ? v.setValue(b) : (jQuery("#textarea_show_dynamic_styles").val(b), setTimeout("RevSliderAdmin.setCodeMirrorDynamicEditor()", 500))
            }), jQuery("#css_slider_editor_wrap").dialog({
                modal: !0,
                resizable: !1,
                minWidth: 1024,
                minHeight: 500,
                closeOnEscape: !0,
                create: function (a) {
                },
                open: function () {
                    jQuery(this).closest(".ui-dialog").addClass("tp-css-editor-dialog")
                },
                buttons: {
                    Save: function () {
                        var a = {};
                        null != x ? a.css = x.getValue() : a.css = jQuery("#textarea_show_slider_styles").val(), null != y ? a.js = y.getValue() : a.js = jQuery("#textarea_show_slider_javascript").val(), a.slider_id = curSliderID, UniteAdminRev.ajaxRequest("update_slider_custom_css_js", a, function (a) {
                        }), jQuery(this).dialog("close")
                    }
                }
            })
        }), jQuery(".show_global_styles").click(function () {
            B(), jQuery("#css_static_editor_wrap").dialog({
                modal: !0,
                resizable: !1,
                minWidth: 1224,
                minHeight: 600,
                closeOnEscape: !0,
                create: function (a) {
                },
                open: function () {
                    jQuery(this).closest(".ui-dialog").addClass("tp-css-editor-dialog").addClass("tp-depricated-dialog")
                },
                buttons: {
                    "Empty Global Styles": function () {
                        return !!confirm(rev_lang.really_clear_global_styles) && (data = "", void UniteAdminRev.ajaxRequest("update_static_css", data, function (a) {
                                null != w ? w.setValue("") : jQuery("#textarea_edit_static").val("")
                            }))
                    }, Cancel: function () {
                        jQuery(this).dialog("close")
                    }
                }
            })
        })
    }
};