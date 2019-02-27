function trace(a, b) {
    UniteAdminRev.trace(a, b)
}
function debug(a) {
    UniteAdminRev.debug(a)
}
var UniteAdminRev = new function () {
    var a = this, b = null, c = null, d = null, e = null, f = [], g = null, h = null, i = null, j = !1;
    a.showInfo = function (a) {
        var b = '<i class="eg-icon-info"></i>';
        "warning" == a.type && (b = '<i class="eg-icon-cancel"></i>'), "success" == a.type && (b = '<i class="eg-icon-ok"></i>'), a.showdelay = void 0 != a.showdelay ? a.showdelay : 0, a.hidedelay = void 0 != a.hidedelay ? a.hidedelay : 0, 0 == jQuery("#eg-toolbox-wrapper").length && jQuery("#viewWrapper").append('<div id="eg-toolbox-wrapper"></div>'), jQuery("#eg-toolbox-wrapper").append('<div class="eg-toolbox newadded">' + b + a.content + "</div>");
        var c = jQuery("#eg-toolbox-wrapper").find(".eg-toolbox.newadded");
        c.removeClass("newadded"), punchgs.TweenLite.fromTo(c, .5, {
            y: -50,
            autoAlpha: 0,
            transformOrigin: "50% 50%",
            transformPerspective: 900,
            rotationX: -90
        }, {
            autoAlpha: 1,
            y: 0,
            rotationX: 0,
            ease: punchgs.Back.easeOut,
            delay: a.showdelay
        }), "event" != a.hideon ? (c.click(function () {
            punchgs.TweenLite.to(c, .3, {
                x: 200, ease: punchgs.Power3.easeInOut, autoAlpha: 0, onComplete: function () {
                    c.remove()
                }
            })
        }), 0 != a.hidedelay && "click" != a.hideon && punchgs.TweenLite.to(c, .3, {
            x: 200,
            ease: punchgs.Power3.easeInOut,
            autoAlpha: 0,
            delay: a.hidedelay + a.showdelay,
            onComplete: function () {
                c.remove()
            }
        })) : jQuery("#eg-toolbox-wrapper").on(a.event, function () {
            punchgs.TweenLite.to(c, .3, {
                x: 200, ease: punchgs.Power3.easeInOut, autoAlpha: 0, onComplete: function () {
                    c.remove()
                }
            })
        })
    }, a.htmlspecialchars = function (a) {
        return a.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;")
    }, a.getAbsolutePos = function (a) {
        var b = curtop = 0;
        if (a.offsetParent)for (b = a.offsetLeft, curtop = a.offsetTop; a = a.offsetParent;)b += a.offsetLeft, curtop += a.offsetTop;
        return [b, curtop]
    }, a.stripslashes = function (a) {
        return (a + "").replace(/\\(.?)/g, function (a, b) {
            switch (b) {
                case"\\":
                    return "\\";
                case"0":
                    return "\0";
                case"":
                    return "";
                default:
                    return b
            }
        })
    }, a.strToBool = function (a) {
        if (void 0 == a)return !1;
        if ("string" != typeof a)return !1;
        a = a.toLowerCase();
        var b = "true" == a;
        return b
    }, a.setColorPickerCallback = function (a) {
        i = a
    }, a.onColorPickerMoveEvent = function (a) {
        "function" == typeof i && i(a)
    }, a.stripTags = function (a, b) {
        b = (((b || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join("");
        var c = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi, d = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
        return a.replace(d, "").replace(c, function (a, c) {
            return b.indexOf("<" + c.toLowerCase() + ">") > -1 ? a : ""
        })
    }, a.rgb2hex = function (a) {
        function b(a) {
            return ("0" + parseInt(a).toString(16)).slice(-2)
        }

        if (a.search("rgb") == -1 || "" == jQuery.trim(a))return a;
        if (a.indexOf("-moz") > -1) {
            var c = a.split(" ");
            delete c[0], a = jQuery.trim(c.join(" "))
        }
        if (a.split(")").length > 2) {
            for (var d = "", e = a.split(")"), f = 0; f < e.length - 1; f++) {
                e[f] += ")";
                var c = e[f].split(",");
                4 == c.length ? (a = c[0] + "," + c[1] + "," + c[2], a += ")") : a = e[f], a = jQuery.trim(a), a = a.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+))?\)$/), d += "#" + b(a[1]) + b(a[2]) + b(a[3]) + " "
            }
            return d
        }
        var c = a.split(",");
        return 4 == c.length && (a = c[0] + "," + c[1] + "," + c[2], a += ")"), a = a.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+))?\)$/), "#" + b(a[1]) + b(a[2]) + b(a[3])
    }, a.getTransparencyFromRgba = function (a, b) {
        var c = a.split(",");
        return 4 == c.length && (b = "undefined" == typeof b || b, b ? c[3].replace(/[^\d.]/g, "") : 100 * c[3].replace(/[^\d.]/g, ""))
    }, a.debug = function (a) {
        jQuery("#div_debug").show().html(a)
    }, a.trace = function (a, b) {
        b && 1 == b && console.clear(), console.log(a)
    }, a.showErrorMessage = function (b) {
        a.showInfo({content: b, type: "warning", showdelay: 0, hidedelay: 3, hideon: "", event: ""}), p()
    };
    var k = function () {
        null !== b ? (jQuery("#" + b).hide(), b = null) : jQuery("#error_message").hide()
    };
    a.setErrorMessageID = function (a) {
        b = a
    }, a.setSuccessMessageID = function (a) {
        c = a
    };
    var l = function (b) {
        a.showInfo({content: b, type: "success", showdelay: 0, hidedelay: 1, hideon: "", event: ""}), p()
    };
    this.hideSuccessMessage = function () {
        c ? (jQuery("#" + c).hide(), c = null) : jQuery("#success_message").slideUp("slow").fadeOut("slow"), p()
    }, this.setAjaxLoaderID = function (a) {
        d = a
    };
    var m = function () {
        d && jQuery("#" + d).show()
    }, n = function () {
        d && (jQuery("#" + d).hide(), d = null)
    };
    this.setAjaxHideButtonID = function (a) {
        e = a
    };
    var o = function () {
        if (e) {
            var a = e.split(",");
            if (a.length > 1)for (var b = 0; b < a.length; b++)jQuery("#" + a[b]).hide(); else jQuery("#" + e).hide()
        }
    }, p = function () {
        if (e) {
            var a = e.split(",");
            if (a.length > 1)for (var b = 0; b < a.length; b++)jQuery("#" + a[b]).show(); else jQuery("#" + e).show();
            e = null
        }
    };
    a.ajaxRequest = function (b, c, d, e, f) {
        var g = {action: g_uniteDirPlugin + "_ajax_action", client_action: b, nonce: g_revNonce, data: c};
        k(), m(), o(), void 0 === e && showWaitAMinute({
            fadeIn: 300,
            text: rev_lang.please_wait_a_moment
        }), jQuery.ajax({
            type: "post", url: ajaxurl, dataType: "json", data: g, success: function (c) {
                if (void 0 !== e || c.is_redirect || showWaitAMinute({fadeOut: 300}), n(), !c)return a.showErrorMessage("Empty ajax response!"), !1;
                if (c == -1)return a.showErrorMessage("ajax error!!!"), !1;
                if (0 == c)return a.showErrorMessage("ajax error, action: <b>" + b + "</b> not found"), !1;
                if (void 0 == c.success)return a.showErrorMessage("The 'success' param is a must!"), !1;
                if (0 == c.success) {
                    if (void 0 === f)return a.showErrorMessage(c.message), !1;
                    "function" == typeof d && d(c)
                } else"function" == typeof d && d(c), c.message && l(c.message), c.is_redirect && (location.href = c.redirect_url)
            }, error: function (b, c, d) {
                void 0 === e && showWaitAMinute({fadeOut: 300}), n(), "parsererror" == c && a.debug(b.responseText), a.showErrorMessage("Ajax Error!!! " + c)
            }
        })
    };
    var q = function (a, b, c) {
        void 0 == c && (c = !1);
        var d = wp.media({title: a, multiple: c, library: {type: "image"}, button: {text: "Insert"}});
        d.on("select", function () {
            var a = d.state().get("selection").first().toJSON(), e = d.state().get("selection"), f = [];
            1 == c ? (e.map(function (a) {
                var b = a.toJSON(), c = {};
                c.url = b.url, c.id = b.id, c.width = b.width, c.height = b.height, f.push(c)
            }), b(f)) : b(a.url, a.id, a.width, a.height)
        }), d.open()
    }, r = function (a, b) {
        var c = "type=image&post_id=0&TB_iframe=true";
        c = encodeURI(c), tb_show(a, "media-upload.php?" + c), window.send_to_editor = function (a) {
            tb_remove();
            var c = jQuery(a).attr("src");
            if (!c || void 0 == c || "" == c)var c = jQuery("img", a).attr("src");
            b(c, "")
            console.log('a',a);
        }
    },r1 = function (a,b) {
        RevSliderMedia.show({select:'images'});
        window.send_to_editor = function (data) {
            b(data.url, data.id, data.width, data.height);
            RevSliderMedia.hide();
        }
    }, s = function (a, b, c) {
        void 0 == c && (c = !1);
        var d = wp.media({title: a, multiple: c, library: {}, button: {text: "Insert"}});
        d.on("select", function () {
            var a = d.state().get("selection").first().toJSON(), e = d.state().get("selection"), f = [];
            1 == c ? (e.map(function (a) {
                var b = a.toJSON(), c = {};
                c.url = b.url, c.id = b.id, c.width = b.width, c.height = b.height, f.push(c)
            }), b(f)) : b(a.url, a.id, a.width, a.height)
        }), d.open()
    },s1 = function (a,b) {
        RevSliderMedia.show({select:'videos'});
        window.send_to_editor = function (data) {
            b(data.url, data.id, data.width, data.height);
            RevSliderMedia.hide();
        }
    };
    a.openAddImageDialog = function (a, b, c) {
        a || (a = rev_lang.select_image), "undefined" != typeof wp && "undefined" != typeof wp.media ? q(a, b, c) : r1(a,b);//r(a, b)
    }, a.openAddVideoDialog = function (a, b, c) {
        a || (a = rev_lang.select_image), ("undefined" != typeof wp && "undefined" != typeof wp.media) ?  s(a, b, c) : s1(a,b);
    }, a.loadCssFile = function (a, b) {
        var c = Math.floor(1e5 * Math.random() + 1);
        a += "?rand=" + c, b && jQuery("#" + b).remove(), jQuery("head").append("<link>");
        var d = jQuery("head").children(":last");
        d.attr({rel: "stylesheet", type: "text/css", href: a}), b && d.attr({id: b})
    }, a.getUrlShowImage = function (a, b, c, d) {
        a = parseInt(a, 0);
        var e = g_urlAjaxShowImage + "&img=" + a;
        return b && (e += "&w=" + b), c && (e += "&h=" + c), d && 1 == d && (e += "&t=exact"), e
    };
    var t = function (a) {
        if (!a)return jQuery("#video_content").html(""), !1;
        var b = a.thumb_medium, c = "" != jQuery.trim(jQuery("#input_video_preview").val()) ? jQuery("#input_video_preview").val() : b.url, d = '<div class="video-thumbnail-all-wrapper">';
        d += '<div class="video-thumbnail-preview-wrapper"><div id="video-thumbnail-preview" style="background-size:cover; background-position:center center; background-image:url(' + c + "); width:" + b.width + "px; height:" + b.height + 'px;display:inline-block;vertical-align:bottom"></div></div>', d += '<div class="video-content-description">', d += '<div class="video-content-title">' + a.title + "</div>", "undefined" != typeof a.desc_small && (d += a.desc_small), d += "</div>", d += "</div>", jQuery("#video_content").html(d)
    }, u = function (a) {
        if (jQuery("#button-audio-add").text(jQuery("#button-audio-add").data("textadd")), jQuery("#video_radio_audio").attr("checked", !0), jQuery("#video_type_chooser").hide(), jQuery("#video_block_youtube").hide(), jQuery("#video_block_vimeo").hide(), jQuery("#video_block_html5").hide(), jQuery("#video_block_audio").show(), jQuery(".rs-hide-on-audio").hide(), jQuery(".rs-show-on-audio").show(), !a)return jQuery("#video_content").html(""), !1;
        var b = a.thumb_medium, c = "" != jQuery.trim(jQuery("#input_video_preview").val()) ? jQuery("#input_video_preview").val() : b.url, d = '<div class="video-thumbnail-all-wrapper">';
        d += '<div class="video-thumbnail-preview-wrapper"><div id="video-thumbnail-preview" style="background-size:cover; background-position:center center; background-image:url(' + c + "); width:" + b.width + "px; height:" + b.height + 'px;display:inline-block;vertical-align:bottom"></div></div>', d += '<div class="video-content-description">', d += '<div class="video-content-title">' + a.title + "</div>", "undefined" != typeof a.desc_small && (d += a.desc_small), d += "</div>", d += "</div>", jQuery("#video_content").html(d)
    }, v = function (a) {
        a = jQuery.trim(a);
        var b = a.split("v=")[1];
        if (b) {
            var c = b.indexOf("&");
            c != -1 && (b = b.substring(0, c))
        } else {
            var d = a.split("/")[3];
            b = d ? d : a
        }
        return b
    }, w = function (a) {
        a = jQuery.trim(a);
        var b = a.replace(/[^0-9]+/g, "");
        return b = jQuery.trim(b)
    };
    a.onYoutubeCallback = function (a) {
        jQuery("#youtube_loader").hide();
        var b = 200, c = {};
        c.id = jQuery("#youtube_id").val(), c.id = jQuery.trim(c.id), c.video_type = "youtube", a[0].width <= 170 || a[0].height <= 140 ? c.title = "YouTube: " + rev_lang.maybe_wrong_yt_id : c.title = "YouTube", c.author = "YouTube", c.link = "", c.description = "", c.desc_small = "", c.description.length > b && (c.desc_small = c.description.slice(0, b) + "..."), c.thumb_small = {
            url: a[0].src,
            width: 200,
            height: 150
        }, c.thumb_medium = {url: a[0].src, width: 320, height: 240}, c.thumb_big = {
            url: a[0].src,
            width: a[0].width,
            height: a[0].height
        }, c.thumb_very_big = {
            url: a[0].src.replace("sddefault.jpg", "maxresdefault.jpg"),
            width: a[0].width,
            height: a[0].height
        }, c.video_width = a[0].width, c.video_height = a[0].height, t(c);
        var d = jQuery("#input_video_arguments");
        "" == d.val() && d.val(d.data("youtube")), g = c, jQuery("#video_dialog_tabs").removeClass("disabled"), j = !0, jQuery("#button-video-add").show()
    }, a.initVideoDef = function () {
        jQuery(".button-image-select-video-default").click(function () {
            if ("undefined" == typeof g)return !1;
            switch (g.video_type) {
                case"vimeo":
                    jQuery("#input_video_preview").val(g.thumb_medium.url), jQuery("#video-thumbnail-preview").css({backgroundImage: "url(" + g.thumb_medium.url + ")"});
                    break;
                case"youtube":
                    jQuery("#input_video_preview").val(g.thumb_big.url), jQuery("#video-thumbnail-preview").css({backgroundImage: "url(" + g.thumb_big.url + ")"})
            }
        }), jQuery(".button-image-select-video-max").click(function () {
            if ("undefined" == typeof g)return !1;
            switch (g.video_type) {
                case"vimeo":
                    jQuery("#input_video_preview").val(g.thumb_large.url), jQuery("#video-thumbnail-preview").css({backgroundImage: "url(" + g.thumb_large.url + ")"});
                    break;
                case"youtube":
                    jQuery("#input_video_preview").val(g.thumb_very_big.url), jQuery("#video-thumbnail-preview").css({backgroundImage: "url(" + g.thumb_very_big.url + ")"})
            }
        })
    }, a.onVimeoCallback = function (a) {
        jQuery("#vimeo_loader").hide();
        var b = 200;
        a = a[0];
        var c = {};
        c.video_type = "vimeo", c.id = a.id, c.id = jQuery.trim(c.id), c.title = a.title, c.link = a.url, c.author = a.user_name, c.description = a.description, c.description.length > b && (c.desc_small = c.description.slice(0, b) + "..."), c.thumb_large = {
            url: a.thumbnail_large,
            width: 640,
            height: 360
        }, c.thumb_medium = {url: a.thumbnail_medium, width: 200, height: 150}, c.thumb_small = {
            url: a.thumbnail_small,
            width: 100,
            height: 75
        }, c.video_with = 640, c.video_height = 360, t(c);
        var d = jQuery("#input_video_arguments");
        "" == d.val() && d.val(d.data("vimeo")), g = c, jQuery("#video_dialog_tabs").removeClass("disabled"), j = !0, jQuery("#button-video-add").show()
    }, a.videoDialogOnError = function () {
        if (1 == j)return jQuery("#video_radio_audio").is(":checked") ? jQuery("#button-audio-add").show() : jQuery("#button-video-add").show(), j = !1, !1;
        jQuery("#button-video-add").hide(), jQuery("#button-audio-add").hide(), jQuery("#youtube_loader").hide(), jQuery("#vimeo_loader").hide();
        var a = "<div class='video-content-error'>" + rev_lang.video_not_found + "</div>";
        jQuery("#video_content").html(a)
    };
    var x = function () {
        var a = jQuery("#input_video_fullwidth").is(":checked");
        1 == a ? jQuery("#video_full_screen_settings").show() : (jQuery("#input_video_cover").prop("checked", !1), jQuery("#video_full_screen_settings").hide());
        var b = jQuery("#input_video_cover").is(":checked");
        1 == b ? jQuery("#input_video_ratio_lbl, #input_video_ratio, #input_video_dotted_overlay_lbl, #input_video_dotted_overlay").show() : jQuery("#input_video_ratio_lbl, #input_video_ratio, #input_video_dotted_overlay_lbl, #input_video_dotted_overlay").hide(), RevSliderSettings.onoffStatus(jQuery("#input_video_fullwidth")), RevSliderSettings.onoffStatus(jQuery("#input_video_cover"))
    };
    a.openVideoDialog = function (a, b, c) {
        h = a;
        var d = jQuery("#dialog_video"), e = {
            Close: function () {
                d.dialog("close")
            }
        };
        "audio" == c ? u(!1) : (t(!1), jQuery("#video_block_youtube").show(), jQuery("#video_block_audio").hide(), jQuery("#video_radio_youtube").attr("checked", !0), jQuery(".rs-hide-on-audio").show(), jQuery(".rs-show-on-audio").hide(), jQuery("#video_type_chooser").show()), jQuery("#youtube_id,#vimeo_id").prop("disabled", "").removeClass("input-disabled"), jQuery("#html5_url_poster").val(""), jQuery("#html5_url_mp4").val(""), jQuery("#html5_url_webm").val(""), jQuery("#html5_url_ogv").val(""), jQuery("#html5_url_audio").val(""), jQuery("#input_video_arguments").val(""), jQuery("#select_video_autoplay option[value='false']").attr("selected", !0), jQuery("#input_video_nextslide").prop("checked", ""), jQuery("#input_video_force_rewind").prop("checked", ""), jQuery("#input_video_fullwidth").prop("checked", ""), jQuery("#input_video_control").prop("checked", ""), jQuery("#input_video_mute").prop("checked", ""), jQuery("#input_disable_on_mobile").prop("checked", ""), jQuery("#input_video_show_cover_pause").prop("checked", ""), jQuery("#input_video_large_controls").prop("checked", !0), jQuery("#input_video_leave_fs_on_pause").prop("checked", !0), jQuery("#input_video_cover").prop("checked", ""), jQuery("#input_video_stopallvideo").prop("checked", ""), jQuery("#input_video_allowfullscreen").prop("checked", ""), jQuery("#input_video_dotted_overlay option[value='none']").attr("selected", !0), jQuery("#input_video_ratio option[value='16:9']").attr("selected", !0), jQuery('#input_video_preload option[value="auto"]').attr("selected", !0), jQuery('#input_video_preload_wait option[value="5"]').attr("selected", !0), jQuery('#input_video_speed option[value="1"]').attr("selected", !0), jQuery('#input_video_loop option[value="none"]').attr("selected", !0), jQuery("#input_video_preview").val(""), jQuery("#input_use_poster_on_mobile").prop("checked", ""), jQuery("#input_video_show_visibility").prop("checked", ""), jQuery("#input_video_play_inline").prop("checked", ""), jQuery("#input_video_start_at").val(""), jQuery("#input_video_end_at").val(""), jQuery("#input_video_volume").val("100"), RevSliderSettings.onoffStatus(jQuery("#input_video_nextslide")), RevSliderSettings.onoffStatus(jQuery("#input_video_force_rewind")), RevSliderSettings.onoffStatus(jQuery("#input_video_fullwidth")), RevSliderSettings.onoffStatus(jQuery("#input_video_control")), RevSliderSettings.onoffStatus(jQuery("#input_video_mute")), RevSliderSettings.onoffStatus(jQuery("#input_disable_on_mobile")), RevSliderSettings.onoffStatus(jQuery("#input_video_cover")), RevSliderSettings.onoffStatus(jQuery("#input_video_stopallvideo")), RevSliderSettings.onoffStatus(jQuery("#input_video_allowfullscreen")), RevSliderSettings.onoffStatus(jQuery("#input_use_poster_on_mobile")), RevSliderSettings.onoffStatus(jQuery("#input_video_show_cover_pause")), RevSliderSettings.onoffStatus(jQuery("#input_video_large_controls")), RevSliderSettings.onoffStatus(jQuery("#input_video_leave_fs_on_pause")), RevSliderSettings.onoffStatus(jQuery("#input_video_show_visibility")), RevSliderSettings.onoffStatus(jQuery("#input_video_play_inline")), jQuery("#button-video-add").hide(), jQuery("#button-audio-add").hide(), jQuery("#video_dialog_tabs").hasClass("disabled") || jQuery("#video_dialog_tabs").addClass("disabled"), jQuery("#youtube_id").val(""), jQuery("#vimeo_id").val(""), jQuery("#fullscreenvideofun").hide();
        var f = jQuery("#button-video-add");
        f.text(f.data("textadd"));
        var g = jQuery("#button-audio-add");
        g.text(g.data("textadd")), d.dialog({
            buttons: e,
            minWidth: 830,
            minHeight: 500,
            modal: !0,
            dialogClass: "tpdialogs",
            create: function (a) {
                jQuery(a.target).parent().find(".ui-dialog-titlebar").addClass("tp-slider-new-dialog-title")
            }
        }), b && y(b), x()
    };
    var y = function (a) {
        switch (a.id = jQuery.trim(a.id), a.video_type) {
            case"youtube":
                jQuery("#video-dialog-wrap").removeClass("html5select"), jQuery("#video_radio_youtube").trigger("click"), jQuery("#youtube_id").val(a.id), jQuery("#fullscreenvideofun").hide();
                break;
            case"vimeo":
                jQuery("#video-dialog-wrap").removeClass("html5select"), jQuery("#video_radio_vimeo").trigger("click"), jQuery("#vimeo_id").val(a.id), jQuery("#fullscreenvideofun").hide();
                break;
            case"html5":
                jQuery("#video-dialog-wrap").addClass("html5select"), jQuery("#html5_url_poster").val(a.urlPoster), jQuery("#html5_url_mp4").val(a.urlMp4), jQuery("#html5_url_webm").val(a.urlWebm), jQuery("#html5_url_ogv").val(a.urlOgv), jQuery("#video_radio_html5").trigger("click"), jQuery("#fullscreenvideofun").show();
                break;
            case"streamvimeo":
                jQuery("#video-dialog-wrap").removeClass("html5select"), jQuery("#video_radio_streamvimeo").trigger("click");
                break;
            case"streamyoutube":
                jQuery("#video-dialog-wrap").removeClass("html5select"), jQuery("#video_radio_streamyoutube").trigger("click");
                break;
            case"streaminstagram":
                jQuery("#video-dialog-wrap").removeClass("html5select"), jQuery("#video_radio_streaminstagram").trigger("click");
                break;
            case"audio":
                jQuery("#html5_url_audio").val(a.urlAudio), jQuery("#rev-html5-options").hide()
        }
        if (jQuery("#input_video_arguments").val(a.args), jQuery("#input_video_preview").val(a.previewimage), a.use_poster_on_mobile && 1 == a.use_poster_on_mobile ? jQuery("#input_use_poster_on_mobile").attr("checked","checked").change() : jQuery("#input_use_poster_on_mobile").removeAttr("checked").change(), a.video_show_visibility && 1 == a.video_show_visibility ? jQuery("#input_video_show_visibility").attr("checked","checked").change() : jQuery("#input_video_show_visibility").removeAttr("checked").change(), a.video_play_inline && 1 == a.video_play_inline ? jQuery("#input_video_play_inline").attr("checked","checked").change() : jQuery("#input_video_play_inline").removeAttr("checked").change(), a.autoplayonlyfirsttime && 1 == a.autoplayonlyfirsttime && (a.autoplay = "1sttime"), a.autoplay)switch (a.autoplay) {
            case"true":
            case!0:
                jQuery('#select_video_autoplay option[value="true"]').attr("selected", !0);
                break;
            case!1:
            case"false":
                jQuery('#select_video_autoplay option[value="false"]').attr("selected", !0);
                break;
            default:
                jQuery('#select_video_autoplay option[value="' + a.autoplay + '"]').attr("selected", !0)
        }
        a.nextslide && 1 == a.nextslide ? jQuery("#input_video_nextslide").attr("checked","checked").change() : jQuery("#input_video_nextslide").removeAttr("checked").change(), a.forcerewind && 1 == a.forcerewind ? jQuery("#input_video_force_rewind").attr("checked","checked").change() : jQuery("#input_video_force_rewind").removeAttr("checked").change(), a.fullwidth && 1 == a.fullwidth ? jQuery("#input_video_fullwidth").attr("checked","checked").change() : jQuery("#input_video_fullwidth").removeAttr("checked").change(), a.controls && 1 == a.controls ? jQuery("#input_video_control").attr("checked","checked").change() : jQuery("#input_video_control").removeAttr("checked").change(), a.mute && 1 == a.mute ? jQuery("#input_video_mute").attr("checked","checked").change() : jQuery("#input_video_mute").removeAttr("checked").change(), a.disable_on_mobile && 1 == a.disable_on_mobile ? jQuery("#input_disable_on_mobile").attr("checked","checked").change() : jQuery("#input_disable_on_mobile").removeAttr("checked").change(), a.cover && 1 == a.cover ? jQuery("#input_video_cover").attr("checked","checked").change() : jQuery("#input_video_cover").removeAttr("checked").change(), a.stopallvideo && 1 == a.stopallvideo ? jQuery("#input_video_stopallvideo").attr("checked","checked").change() : jQuery("#input_video_stopallvideo").removeAttr("checked").change(), a.allowfullscreen && 1 == a.allowfullscreen ? jQuery("#input_video_allowfullscreen").attr("checked","checked").change() : jQuery("#input_video_allowfullscreen").removeAttr("checked").change(), a.preload && jQuery("#input_video_preload option").each(function () {
            jQuery(this).val() == a.preload && jQuery(this).attr("selected", !0)
        }), a.preload_audio && jQuery("#input_audio_preload option").each(function () {
            jQuery(this).val() == a.preload_audio && jQuery(this).attr("selected", !0)
        }), a.preload_wait && jQuery("#input_video_preload_wait option").each(function () {
            jQuery(this).val() == a.preload_wait && jQuery(this).attr("selected", !0)
        }), a.videospeed && jQuery("#input_video_speed option").each(function () {
            jQuery(this).val() == a.videospeed && jQuery(this).attr("selected", !0)
        }), a.dotted && jQuery("#input_video_dotted_overlay option").each(function () {
            jQuery(this).val() == a.dotted && jQuery(this).attr("selected", !0)
        }), a.ratio && jQuery("#input_video_ratio option").each(function () {
            jQuery(this).val() == a.ratio && jQuery(this).attr("selected", !0)
        }), a.videoloop && (1 == a.videoloop ? jQuery('#input_video_loop option[value="loop"]').attr("selected", !0) : jQuery("#input_video_loop option").each(function () {
            jQuery(this).val() == a.videoloop && jQuery(this).attr("selected", !0)
        }));
        var b = jQuery("#button-video-add");
        b.text(b.data("textupdate"));
        var c = jQuery("#button-audio-add");
        switch (c.text(c.data("textupdate")), a.video_type) {
            case"youtube":
                jQuery("#button_youtube_search").trigger("click");
                break;
            case"vimeo":
                jQuery("#button_vimeo_search").trigger("click")
        }
        a.show_cover_pause && 1 == a.show_cover_pause ? jQuery("#input_video_show_cover_pause").attr("checked","checked").change() : jQuery("#input_video_show_cover_pause").removeAttr("checked").change(), "undefined" != typeof a.large_controls && 0 == a.large_controls ? jQuery("#input_video_large_controls").removeAttr("checked").change() : jQuery("#input_video_large_controls").attr("checked","checked").change(), "undefined" != typeof a.leave_on_pause && 0 == a.leave_on_pause ? jQuery("#input_video_leave_fs_on_pause").removeAttr("checked").change() : jQuery("#input_video_leave_fs_on_pause").attr("checked","checked").change(), jQuery("#input_video_start_at").val(a.start_at), jQuery("#input_video_end_at").val(a.end_at), jQuery("#input_video_volume").val(a.volume), RevSliderSettings.onoffStatus(jQuery("#input_video_nextslide")), RevSliderSettings.onoffStatus(jQuery("#input_video_force_rewind")), RevSliderSettings.onoffStatus(jQuery("#input_video_fullwidth")), RevSliderSettings.onoffStatus(jQuery("#input_video_control")), RevSliderSettings.onoffStatus(jQuery("#input_video_mute")), RevSliderSettings.onoffStatus(jQuery("#input_disable_on_mobile")), RevSliderSettings.onoffStatus(jQuery("#input_video_cover")), RevSliderSettings.onoffStatus(jQuery("#input_video_stopallvideo")), RevSliderSettings.onoffStatus(jQuery("#input_video_allowfullscreen")), RevSliderSettings.onoffStatus(jQuery("#input_use_poster_on_mobile")), RevSliderSettings.onoffStatus(jQuery("#input_video_show_cover_pause")), RevSliderSettings.onoffStatus(jQuery("#input_video_large_controls")), RevSliderSettings.onoffStatus(jQuery("#input_video_leave_fs_on_pause")), RevSliderSettings.onoffStatus(jQuery("#input_video_show_visibility")), RevSliderSettings.onoffStatus(jQuery("#input_video_play_inline")), "audio" === a.video_type ? (jQuery("#button-video-add").hide(), jQuery("#button-audio-add").show()) : (jQuery("#button-video-add").show(), jQuery("#button-audio-add").hide()), jQuery("#video_dialog_tabs").removeClass("disabled"), jQuery("#reset_video_dialog_tab").click()
    }, z = function (a) {
        return a.args = jQuery("#input_video_arguments").val(), a.previewimage = jQuery("#input_video_preview").val(), a.autoplay = jQuery("#select_video_autoplay option:selected").val(), a.use_poster_on_mobile = jQuery("#input_use_poster_on_mobile").attr("checked") === "checked", a.video_show_visibility = jQuery("#input_video_show_visibility").attr("checked") === "checked", a.video_play_inline = jQuery("#input_video_play_inline").attr("checked") === "checked", a.nextslide = jQuery("#input_video_nextslide").attr("checked") === "checked", a.forcerewind = jQuery("#input_video_force_rewind").attr("checked") === "checked", a.fullwidth = jQuery("#input_video_fullwidth").attr("checked") === "checked", a.controls = jQuery("#input_video_control").attr("checked") === "checked", a.mute = jQuery("#input_video_mute").attr("checked") === "checked", a.disable_on_mobile = jQuery("#input_disable_on_mobile").attr("checked") === "checked", a.cover = jQuery("#input_video_cover").attr("checked") === "checked", a.stopallvideo = jQuery("#input_video_stopallvideo").attr("checked") === "checked", a.allowfullscreen = jQuery("#input_video_allowfullscreen").attr("checked") === "checked", a.dotted = jQuery("#input_video_dotted_overlay option:selected").val(), a.preload = jQuery("#input_video_preload option:selected").val(), a.preload_audio = jQuery("#input_audio_preload option:selected").val(), a.preload_wait = jQuery("#input_video_preload_wait option:selected").val(), a.videospeed = jQuery("#input_video_speed option:selected").val(), a.ratio = jQuery("#input_video_ratio option:selected").val(), a.videoloop = jQuery("#input_video_loop option:selected").val(), a.show_cover_pause = jQuery("#input_video_show_cover_pause").attr("checked") === "checked", a.start_at = jQuery("#input_video_start_at").val(), a.end_at = jQuery("#input_video_end_at").val(), a.volume = jQuery("#input_video_volume").val(), a.large_controls = jQuery("#input_video_large_controls").attr("checked") === "checked", a.leave_on_pause = jQuery("#input_video_leave_fs_on_pause").attr("checked") === "checked", a
    }, A = function () {
        jQuery("#video_radio_youtube").prop("checked", !0), jQuery("#video_radio_vimeo").click(function () {
            jQuery("#video_block_youtube").hide(), jQuery("#video_block_html5").hide(), jQuery("#rev-html5-options").hide(), jQuery("#rev-youtube-options").hide(), jQuery("#video_block_vimeo").show(), jQuery("#preview-image-video-wrap").show(), jQuery("#video-dialog-wrap").removeClass("html5select"), jQuery("#fullscreenvideofun").hide(), jQuery(".video-volume").show(), jQuery(".hide-for-vimeo").hide()
        }), jQuery("#video_radio_youtube").click(function () {
            jQuery("#video_block_vimeo").hide(), jQuery("#video_block_html5").hide(), jQuery("#rev-html5-options").hide(), jQuery("#rev-youtube-options").show(), jQuery("#video_block_youtube").show(), jQuery("#preview-image-video-wrap").show(), jQuery("#video-dialog-wrap").removeClass("html5select"), jQuery("#fullscreenvideofun").hide(), jQuery(".video-volume").show(), jQuery(".hide-for-vimeo").show()
        }), jQuery("#video_radio_html5").click(function () {
            jQuery("#video_block_vimeo").hide(), jQuery("#video_block_youtube").hide(), jQuery("#video_block_html5").show(), jQuery("#rev-youtube-options").hide(), jQuery("#rev-html5-options").show(), jQuery("#video_content").hide(), jQuery("#preview-image-video-wrap").hide(), jQuery("#video-dialog-wrap").addClass("html5select"), jQuery("#fullscreenvideofun").show(), jQuery(".video-volume").show(), jQuery(".hide-for-vimeo").show()
        }), jQuery("#video_radio_streamyoutube").click(function () {
            jQuery("#video_block_youtube").hide(), jQuery("#video_block_vimeo").hide(), jQuery("#video_block_html5").hide(), jQuery("#rev-html5-options").hide(), jQuery("#rev-youtube-options").hide(), jQuery("#preview-image-video-wrap").show(), jQuery("#video-dialog-wrap").removeClass("html5select"), jQuery("#fullscreenvideofun").hide(), jQuery("#video_dialog_tabs").removeClass("disabled"), jQuery("#button-video-add").show(), jQuery(".video-volume").show(), jQuery(".hide-for-vimeo").show()
        }), jQuery("#video_radio_streamvimeo").click(function () {
            jQuery("#video_block_youtube").hide(), jQuery("#video_block_vimeo").hide(), jQuery("#video_block_html5").hide(), jQuery("#rev-html5-options").hide(), jQuery("#rev-youtube-options").show(), jQuery("#preview-image-video-wrap").show(), jQuery("#video-dialog-wrap").removeClass("html5select"), jQuery("#fullscreenvideofun").hide(), jQuery("#video_dialog_tabs").removeClass("disabled"), jQuery("#button-video-add").show(), jQuery(".video-volume").show(), jQuery(".hide-for-vimeo").hide()
        }), jQuery("#video_radio_streaminstagram").click(function () {
            jQuery("#video_block_youtube").hide(), jQuery("#video_block_vimeo").hide(), jQuery("#video_block_html5").hide(), jQuery("#rev-html5-options").hide(), jQuery("#rev-youtube-options").hide(), jQuery("#preview-image-video-wrap").show(), jQuery("#video-dialog-wrap").removeClass("html5select"), jQuery("#fullscreenvideofun").hide(), jQuery("#video_dialog_tabs").removeClass("disabled"), jQuery("#button-video-add").show(), jQuery(".video-volume").hide(), jQuery(".hide-for-vimeo").show()
        }), jQuery("#input_video_cover").change(function () {
            jQuery(this).is(":checked") && (jQuery("#input_video_fullwidth").is(":checked") || jQuery("#input_video_fullwidth").prop("checked", !0), RevSliderSettings.onoffStatus(jQuery("#input_video_fullwidth"))), x()
        }), jQuery("#input_video_fullwidth").change(function () {
            jQuery(this).is(":checked") ? jQuery("#video_full_screen_settings").show() : jQuery("#video_full_screen_settings").hide()
        }), jQuery("#button_youtube_search").click(function () {
            t(!1), jQuery("#youtube_loader").show();
            var a = jQuery("#youtube_id").val();
            a = jQuery.trim(a), a = v(a), jQuery("#youtube_id").val(a);
            var b = new Image;
            b.onload = function () {
                var a = jQuery(this);
                UniteAdminRev.onYoutubeCallback(a)
            }, b.src = "https://img.youtube.com/vi/" + a + "/sddefault.jpg", jQuery("#video_content").show(), setTimeout("UniteAdminRev.videoDialogOnError()", 2e3)
        }), jQuery("#button-video-add, #button-audio-add").click(function () {
            var a = jQuery("#video_radio_html5").prop("checked"), b = jQuery("#video_radio_audio").prop("checked"), c = jQuery("#video_radio_streamyoutube").prop("checked"), d = jQuery("#video_radio_streamvimeo").prop("checked"), e = jQuery("#video_radio_streaminstagram").prop("checked");
            if (jQuery("#video_content").hide(), a) {
                var f = {};
                f.video_type = "html5", f.urlPoster = jQuery("#html5_url_poster").val(), f.urlMp4 = jQuery("#html5_url_mp4").val(), f.urlWebm = jQuery("#html5_url_webm").val(), f.urlOgv = jQuery("#html5_url_ogv").val(), f.video_width = 480, f.video_height = 360, f = z(f), "function" == typeof h && h(f), jQuery("#dialog_video").dialog("close")
            } else if (c) {
                var f = {};
                f.video_type = "streamyoutube", f.video_width = 480, f.video_height = 360, f = z(f), "function" == typeof h && h(f), jQuery("#dialog_video").dialog("close")
            } else if (d) {
                var f = {};
                f.video_type = "streamvimeo", f.video_width = 480, f.video_height = 360, f = z(f), "function" == typeof h && h(f), jQuery("#dialog_video").dialog("close")
            } else if (e) {
                var f = {};
                f.video_type = "streaminstagram", f.video_width = 480, f.video_height = 360, f = z(f), "function" == typeof h && h(f), jQuery("#dialog_video").dialog("close")
            } else if (b) {
                var f = {};
                f.video_type = "audio", f.urlAudio = jQuery("#html5_url_audio").val(), f.video_width = 200, f.video_height = 34, f = z(f), "function" == typeof h && h(f), jQuery("#dialog_video").dialog("close")
            } else {
                if (!g)return !1;
                g = z(g), "function" == typeof h && h(g), jQuery("#dialog_video").dialog("close")
            }
            try {
                UniteLayersRev.setLayerSelected(selectedLayerSerial, !0)
            } catch (a) {
            }
        }), jQuery("#html5_url_audio, #html5_url_ogv, #html5_url_webm, #html5_url_mp4").on("change", function () {
            jQuery("#video_dialog_tabs").removeClass("disabled"), "html5_url_audio" == jQuery(this).attr("id") ? (jQuery("#button-video-add").hide(), jQuery("#button-audio-add").show()) : (jQuery("#button-video-add").show(), jQuery("#button-audio-add").hide())
        }), jQuery("#button_vimeo_search").click(function () {
            t(!1), jQuery("#vimeo_loader").show(), jQuery("#video_content").show();
            var a = jQuery("#vimeo_id").val();
            a = jQuery.trim(a), a = w(a);
            var b = "//www.vimeo.com/api/v2/video/" + a + ".json?callback=UniteAdminRev.onVimeoCallback";
            jQuery.getScript(b), setTimeout("UniteAdminRev.videoDialogOnError()", 2e3)
        }), jQuery("#input_video_fullwidth").change(x)
    }, B = function () {
        jQuery("#button_general_settings").click(function () {
            jQuery("#loader_general_settings").hide(), jQuery("#dialog_general_settings").dialog({
                minWidth: 800,
                minHeight: 500,
                modal: !0,
                dialogClass: "tpdialogs",
                create: function (a) {
                    jQuery(a.target).parent().find(".ui-dialog-titlebar").addClass("tp-slider-new-dialog-title")
                }
            })
        }), jQuery("#button_save_general_settings").click(function () {
            var a = RevSliderSettings.getSettingsObject("form_general_settings");
            UniteAdminRev.ajaxRequest("update_general_settings", a, function (a) {
            })
        }), jQuery("#trigger_database_creation").click(function () {
            UniteAdminRev.ajaxRequest("fix_database_issues", {}, function (a) {
            })
        })
    }, C = function () {
        jQuery("#rs-validation-activate").click(function () {
            var b = {code: jQuery('input[name="rs-validation-token"]').val()};
            UniteAdminRev.ajaxRequest("activate_purchase_code", b, function (b) {
                0 == b.success ? jQuery("#register-wrong-purchase-code").click() : void 0 !== b.error && "exist" == b.error && a.showErrorMessage(b.msg)
            }, void 0, !0)
        }), jQuery("#rs-validation-deactivate").click(function () {
            UniteAdminRev.ajaxRequest("deactivate_purchase_code", "");
        })
    };
    jQuery(document).ready(function () {
        A(), B(), D(), C()
    }), a.setMultipleTextKey = function (a, b) {
        f[a] = b
    }, a.getMultipleTextKey = function (a) {
        return f[a]
    };
    var D = function () {
        jQuery("body").on("click", ".remove_multiple_text", function () {
            jQuery("#" + jQuery(this).data("remove")).remove(), jQuery(this).parent().remove()
        }), jQuery(".multiple_text_add").click(function () {
            var b = jQuery(this).data("name"), c = a.getMultipleTextKey(b) + 1, d = jQuery("." + b + "_TEMPLATE").html();
            d = d.replace(/##ID##/gi, b + "_" + c).replace(/##NAME##/gi, b), jQuery("#" + b + "_row .setting_input").append(d), a.setMultipleTextKey(b, c)
        })
    };
    a.parseCssMultiAttribute = function (a) {
        if ("" == a)return !1;
        var b = a.split(" "), c = [];
        switch (b.length) {
            case 1:
                c[0] = b[0], c[1] = b[0], c[2] = b[0], c[3] = b[0];
                break;
            case 2:
                c[0] = b[0], c[1] = b[1], c[2] = b[0], c[3] = b[1];
                break;
            case 3:
                c[0] = b[0], c[1] = b[1], c[2] = b[2], c[3] = b[1];
                break;
            case 4:
                c[0] = b[0], c[1] = b[1], c[2] = b[2], c[3] = b[3];
                break;
            case 0:
            default:
                return !1
        }
        return c
    }, a.convertHexToRGB = function (a) {
        var a = parseInt(a.indexOf("#") > -1 ? a.substring(1) : a, 16);
        return [a >> 16, (65280 & a) >> 8, 255 & a]
    }, a.initGoogleFonts = function () {
        jQuery("#eg-font-setting-change").click(function () {
            UniteAdminRev.ajaxRequest("change_google_fonts_settings", {setting: jQuery('input[name="load_fonts_place"]:checked').val()}, function (a) {
            })
        }), jQuery("#eg-font-add").click(function () {
            jQuery("#font-dialog-wrap").dialog({
                modal: !0,
                draggable: !1,
                resizable: !1,
                width: 470,
                height: 320,
                closeOnEscape: !0,
                dialogClass: "wp-dialog",
                create: function (a) {
                    jQuery(a.target).parent().find(".ui-dialog-titlebar").addClass("tp-slider-new-dialog-title")
                },
                buttons: [{
                    text: "Add Font", click: function () {
                        var b = {};
                        return b.handle = a.sanitize_input(jQuery('input[name="eg-font-handle"]').val()), b.url = jQuery('input[name="eg-font-url"]').val(), b.handle.length < 3 || b.url.length < 3 ? (alert(rev_lang.handle_at_least_three_chars), !1) : void UniteAdminRev.ajaxRequest("add_google_fonts", b, function (a) {
                        })
                    }
                }]
            })
        }), jQuery("body").on("click", ".eg-font-edit", function () {
            if (confirm(rev_lang.really_change_font_sett)) {
                var a = {}, b = jQuery(this);
                a.handle = b.closest(".inside").find('input[name="esg-font-handle[]"]').val(), a.url = b.closest(".inside").find('input[name="esg-font-url[]"]').val(), UniteAdminRev.ajaxRequest("edit_google_fonts", a, function (a) {
                })
            }
        }), jQuery("body").on("click", ".eg-font-delete", function () {
            if (confirm(rev_lang.really_delete_font)) {
                var a = {}, b = jQuery(this);
                a.handle = b.closest(".inside").find('input[name="esg-font-handle[]"]').val(), UniteAdminRev.ajaxRequest("remove_google_fonts", a, function (a) {
                    1 == a.success && b.closest(".postbox.eg-postbox").remove()
                })
            }
        })
    }, a.sanitize_input = function (a) {
        return a.replace(/ /g, "-").replace(/[^-0-9a-zA-Z_-]/g, "")
    }, a.sanitize_input_lc = function (a) {
        return a.replace(/ /g, "-").replace(/[^-0-9a-z_-]/g, "")
    }, a.initAccordion = function () {
        jQuery(".postbox-arrow").each(function (a) {
            jQuery(this).closest("h3").click(function () {
                var a = jQuery(this);
                a.hasClass("box-closed") ? (jQuery(".postbox-arrow").each(function () {
                    var a = jQuery(this).closest("h3");
                    a.closest(".postbox").find(".inside").slideUp("fast"), a.addClass("box-closed")
                }), a.closest(".postbox").find(".inside").slideDown("fast"), a.removeClass("box-closed")) : (a.closest(".postbox").find(".inside").slideUp("fast"), a.addClass("box-closed"))
            })
        })
    }, a.return_ajaxurl_param = function () {
        return ajaxurl.indexOf("?") === -1 ? "?" : "&"
    }
};