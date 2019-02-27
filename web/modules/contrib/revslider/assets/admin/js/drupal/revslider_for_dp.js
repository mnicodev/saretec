
if(typeof (drupalSettings) != 'undefined')
{
    var RS_DEMO = drupalSettings.revslider.RS_DEMO;
    var ajaxurl = drupalSettings.revslider.ajaxurl;
    var g_revNonce = drupalSettings.revslider.g_revNonce;
    var g_uniteDirPlugin = drupalSettings.revslider.g_uniteDirPlugin;
    var g_urlContent = drupalSettings.revslider.g_urlContent;
    var g_urlAjaxShowImage = drupalSettings.revslider.g_urlAjaxShowImage;
    var g_urlAjaxActions = drupalSettings.revslider.g_urlAjaxActions;
    var g_revslider_url = drupalSettings.revslider.g_revslider_url;
    var g_settingsObj = drupalSettings.revslider.g_settingsObj;
    var rs_pack_page_creation = drupalSettings.revslider.rs_pack_page_creation;
    var rs_single_page_creation = drupalSettings.revslider.rs_single_page_creation;
    var tp_color_picker_presets = drupalSettings.revslider.tp_color_picker_presets;
    var global_grid_sizes = drupalSettings.revslider.global_grid_sizes;
    var wpColorPickerL10n = drupalSettings.revslider.wpColorPickerL10n;
    var thickboxL10n = drupalSettings.revslider.thickboxL10n;
    if(typeof(rev_lang) == 'undefined'){
        rev_lang = drupalSettings.revslider.javascript_multilanguage ;
    }
}
jQuery.fn.bindFirst = function(name, fn) {
    var elem, handlers, i, _len;
    this.bind(name, fn);
    for (i = 0, _len = this.length; i < _len; i++) {
        elem = this[i];
        handlers = jQuery._data(elem).events[name.split('.')[0]];
        handlers.unshift(handlers.pop());
    }
};

//function
jQuery('body').on('click','.rs-preview-device_selector_prev', function() {
    var btn = jQuery(this);
    jQuery('.rs-preview-device_selector_prev.selected').removeClass("selected");
    btn.addClass("selected");

    var w = parseInt(global_grid_sizes[btn.data("type")],0);
    if (w>1450) w = 1450;
    jQuery('#rs-preview-wrapper-inner').css({maxWidth:w+"px"});

});

jQuery(window).resize(function() {
    var ww = jQuery(window).width();
    if (global_grid_sizes)
        jQuery.each(global_grid_sizes,function(key,val) {
            if (ww<=parseInt(val,0)) {
                jQuery('.rs-preview-device_selector_prev.selected').removeClass("selected");
                jQuery('.rs-preview-device_selector_prev[data-type="'+key+'"]').addClass("selected");
            }
        })
});

/* SHOW A WAIT FOR PROGRESS */
function showWaitAMinute(obj) {
    var wm = jQuery('#waitaminute');

    // CHANGE TEXT
    if (obj.text != undefined) {
        switch (obj.text) {
            case "progress1":

                break;
            default:

                wm.html('<div class="waitaminute-message"><i class="eg-icon-emo-coffee"></i><br>'+obj.text+'</div>');
                break;
        }
    }


    if (obj.delay!=undefined) {
        punchgs.TweenLite.to(wm,0.3,{autoAlpha:1,ease:punchgs.Power3.easeInOut});
        punchgs.TweenLite.set(wm,{display:"block"});

        setTimeout(function() {
            punchgs.TweenLite.to(wm,0.3,{autoAlpha:0,ease:punchgs.Power3.easeInOut,onComplete:function() {
                punchgs.TweenLite.set(wm,{display:"block"});
            }});
        },obj.delay)
    }

    // SHOW IT
    if (obj.fadeIn !== undefined) {
        punchgs.TweenLite.to(wm,obj.fadeIn/1000,{autoAlpha:1,ease:punchgs.Power3.easeInOut});
        punchgs.TweenLite.set(wm,{display:"block"});
    }

    // HIDE IT
    if (obj.fadeOut !== undefined) {
        punchgs.TweenLite.to(wm,obj.fadeOut/1000,{autoAlpha:0,ease:punchgs.Power3.easeInOut,onComplete:function() {
            punchgs.TweenLite.set(wm,{display:"block"});
        }});
    }
}


jQuery(document).ready(function(){
    setTimeout(function () {
        jQuery('#viewWrapper input[type="checkbox"]').bindFirst('click',function () {
            if(this.checked)
                jQuery(this).attr('checked','checked');
            else
                jQuery(this).removeAttr('checked');
        });
    },200);
    jQuery('#licence_obect_library, #regsiter-to-access-update-none, #regsiter-to-access-store-none, #register-wrong-purchase-code').click(function(){
        var clicked = jQuery(this).attr('id');
        show_premium_dialog(clicked);
    });

    jQuery('.license_obj_library_cats_filter').click(function() {
        var t = jQuery(this);
        jQuery('.license_obj_library_cats_filter').removeClass("selected");
        t.addClass("selected");
        jQuery('.license_deep_content').hide();
        jQuery("#"+t.data('id')).show();
    });

});
