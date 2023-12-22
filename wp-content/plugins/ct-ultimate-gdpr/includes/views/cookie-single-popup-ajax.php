<?php

/**
 * The template for displaying cookie single popup on front via ajax call
 *
 * You can overwrite this template by copying it to yourtheme/ct-ultimate-gdpr folder
 *
 * @version 1.0
 *
 */

if (!defined('ABSPATH')) {
	exit;
}

/* MODAL VARIABLES */

//if( !empty( apply_filters('ct_ultimate_gdpr_controller_cookie_ajax_popup', 0) ) ) : 
?>

<div id="ajax-cookie-popup-js">
    
<script>
/**
 * cookie popup features
 * @var object ct_ultimate_gdpr_cookie - from wp_localize_script
 * @var object ct_ultimate_gdpr_cookie_shortcode_popup - from wp_localize_script
 *
* */

jQuery(document).ready(function ($) {

    // IN PANEL VIEW @ 1024px WINDOW WIDTH AND WIDER, ALLOWS OREO
    // SKIN BUTTONS TO HAVE 50% HEIGHT EVEN IF PARENT HAS DYNAMIC HEIGHT
    var oreoTopPanel = $('.ct-ultimate-gdpr-cookie-popup-oreo.ct-ultimate-gdpr-cookie-topPanel');
    var oreoBottomPanel = $('.ct-ultimate-gdpr-cookie-popup-oreo.ct-ultimate-gdpr-cookie-bottomPanel');
    if (oreoTopPanel.add(oreoBottomPanel).length) {
        function setBtnHeight() {
            if ($(window).width() >= 1024) {
                var boxHeight = jQuery('#ct-ultimate-gdpr-cookie-popup').outerHeight();
                var btnHeight = boxHeight;
                if ($('#ct-ultimate-gdpr-cookie-change-settings').length) {
                    var btnHeight = boxHeight / 2;
                }
                $('#ct-ultimate-gdpr-cookie-accept, #ct-ultimate-gdpr-cookie-change-settings, #ct_ultimate-gdpr-cookie-reject').css({
                    'height': btnHeight + 'px',
                    'line-height': btnHeight + 'px'
                });
            } else {
                $('#ct-ultimate-gdpr-cookie-accept, #ct-ultimate-gdpr-cookie-change-settings, #ct_ultimate-gdpr-cookie-reject').css({
                    'height': '52px',
                    'line-height': 'normal'
                });
            }
        }
        setBtnHeight();
        $(window).resize(function() {
            setBtnHeight();
        });
    }

    // FIX DOUBLE VERTICAL SCROLLBAR AFTER COOKIE MODAL POPUP SAVE BUTTON IS CLICKED
    $('.ct-ultimate-gdpr-cookie-modal-btn.save').on('click', function () {
        removerAjaxScript();
        $(this).parents('#ct-ultimate-gdpr-cookie-modal').hide();
    });

    // 1173
    $('input.ct-ultimate-gdpr-cookie-modal-single-item').each(function() {
        if ( $(this).is(":checked") ) {
            var checkboxParent = $(this).closest("li");

            $(this).addClass('ct-cookie-item-selected');
            checkboxParent.addClass('active');
            checkboxParent.find('path').css('fill', '#82aa3b');
        }
    });

    // TOGGLE "HIDE DETAILS" BUTTON, AND COOKIE WILL / WON'T LIST ACCORDINGLY
    var compactBlock = $('.ct-ultimate-gdpr-cookie-modal-compact #ct-ultimate-gdpr-cookie-modal-slider-item-block');
    compactBlock.on('click', function () {
        var cookieList = jQuery(this).parents('form').next();
        var hideDetails = jQuery('.hide-btn-wrapper');
        if (cookieList.is(':visible')) {
            cookieList.slideUp();
        }
        if (hideDetails.is(':visible')) {
            hideDetails.slideUp();
        }
    });
    var compactItem = $('.ct-ultimate-gdpr-cookie-modal-compact .ct-ultimate-gdpr-cookie-modal-slider-item');
    compactItem.not('#ct-ultimate-gdpr-cookie-modal-slider-item-block').on('click', function () {
        var cookieList = jQuery(this).parents('form').next();
        var hideDetails = jQuery('.hide-btn-wrapper');
        if (cookieList.is(':hidden')) {
            cookieList.slideDown();
        }
        if (hideDetails.is(':hidden')) {
            hideDetails.slideDown();
        }
    });

    function isModalAlwaysVisible() {
        return false;
    }

    function hidePopup() {

        if (isModalAlwaysVisible()) return;

        jQuery('#ct-ultimate-gdpr-cookie-popup').hide();
        jQuery('.ct-ultimate-gdpr-cookie-fullPanel-overlay').hide();
        jQuery('#ct-ultimate-gdpr-cookie-open').show(function(){
            $(this).css('display','flex');
        });
    }

    function showPopup() {
        jQuery('#ct-ultimate-gdpr-cookie-popup').show();
    }

    function hideModal() {

        if (isModalAlwaysVisible()) return;

        removerAjaxScript();
        jQuery('#ct-ultimate-gdpr-cookie-modal').hide();
        jQuery('#ct-ultimate-gdpr-cookie-open').show(function(){
            $(this).css('display','flex');
        });
    }

    function showModal() {
        jQuery('#ct-ultimate-gdpr-cookie-modal').show();
        jQuery('#ct-ultimate-gdpr-cookie-open').hide();
    }

    function getCookie(name) {
        var ctCookie = document.cookie;
        if (ctCookie) {
            var match = ctCookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
            if (match) return match[2];
        } else {
            return '';
        }
    }

    function dateToAge(date) {
        const msDiff = Date.now() - date.getTime();
        const diffDate = new Date(msDiff);

        return Math.abs(diffDate.getUTCFullYear() - 1970);
    }

    function shouldDisplayPopup() {

        // on some setups php is unable to grab cookie in backend, then check in js below
        if (ct_ultimate_gdpr_cookie.consent) {
            return false;
        }

        const consentCookieValue = getCookie('ct-ultimate-gdpr-cookie');

        //fix for old version of the plugin
        if(consentCookieValue){
            var myCookieIsFromOldVersion = (consentCookieValue.indexOf("consent") !== -1);
            if(myCookieIsFromOldVersion){
                document.cookie = "ct-ultimate-gdpr-cookie" + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
                return true;
            }
        }

        var cookieObject = consentCookieValue ? JSON.parse(atob(decodeURIComponent(consentCookieValue))) : {};

        //checking for latest/legacy expire_time js implementation
        if (cookieObject.consent_expire_time) {
            return cookieObject.consent_expire_time < +new Date / 1000;
        } else if (cookieObject.expire_time) {
            return cookieObject.expire_time < +new Date / 1000;
        } else {
            return true;
        }

    }

    function maybeShowPopup(e) {
        // hide popup and show small gear icon if user already given consent
        if (shouldDisplayPopup()) {
            showPopup();
            $('body').removeClass("ct-ultimate-gdpr-cookie-bottomPanel-padding");
            $('body').removeClass("ct-ultimate-gdpr-cookie-topPanel-padding");
            
        } else {
            hidePopup();
        }

    }

    function setJsCookie(consent_level) {

        try {

            var consent_expire_time = ct_ultimate_gdpr_cookie.consent_expire_time;
            var consent_time = ct_ultimate_gdpr_cookie.consent_time;
            var content = {
                'consent_level': consent_level,
                'consent_expire_time': consent_expire_time,
                'consent_time': consent_time,
                'consent_declined': false
            };

            content = btoa(JSON.stringify(content));
            var js_expire_time = new Date(1000 * consent_expire_time).toUTCString();
            document.cookie = "ct-ultimate-gdpr-cookie=" + content + "; expires=" + js_expire_time + "; path=/";

        } catch (e) {

        }

    }

    function onAccept() {
        acceptConcent();
        var level = ct_ultimate_gdpr_cookie.consent_accept_level;
        var protectLevel = $(".ct-ultimate-gdpr-shortcode-protection").attr('data-level');
        var $this = $(this);
        setJsCookie(level);

        // triggering click in proper modal input
        jQuery('.ct-ultimate-gdpr-cookie-modal-content input[data-count=' + level + ']').trigger('click');

        jQuery.post(ct_ultimate_gdpr_cookie.ajaxurl, {
                "action": "ct_ultimate_gdpr_cookie_consent_give",
                "level": level
            },
            function () {
                if( $this.attr("id") != "ct-ultimate-cookie-close-modal" ){
                    if (ct_ultimate_gdpr_cookie.reload) {
                        window.location.reload(true);
                    }
                }
            }
        ).fail(function () {


            jQuery.post(ct_ultimate_gdpr_cookie.ajaxurl, {
                "skip_cookies": true,
                "action": "ct_ultimate_gdpr_cookie_consent_give",
                "level": level
            }, function () {
                if( $this.attr("id") != "ct-ultimate-cookie-close-modal" ){
                    if (ct_ultimate_gdpr_cookie.reload) {
                        window.location.reload(true);
                    }
                }
            });

        });

        if (!ct_ultimate_gdpr_cookie.reload) {
            hidePopup()
            if (level >= protectLevel) {
                $(".ct-ultimate-gdpr-shortcode-protection").removeClass("blur")
                $("span.ct-ultimate-gdpr-shortcode-protection-label").remove();
                var content = $("div.ct-ultimate-gdpr-shortcode-protection").text();
                var result = $.base64.decode(content);
                $(".ct-ultimate-gdpr-shortcode-protection").html(result);
            }

        }

        $('body').removeClass("ct-ultimate-gdpr-cookie-bottomPanel-padding");
        $('body').removeClass("ct-ultimate-gdpr-cookie-topPanel-padding");


        if (level >= protectLevel) {
            $(".ct-ultimate-gdpr-shortcode-protection").removeClass("blur")
            $("span.ct-ultimate-gdpr-shortcode-protection-label").remove();
            var content = $("div.ct-ultimate-gdpr-shortcode-protection").text();
            var result = $.base64.decode(content);
            $(".ct-ultimate-gdpr-shortcode-protection").html(result);
        }
    }

    function onRead() {
        if (ct_ultimate_gdpr_cookie && ct_ultimate_gdpr_cookie.readurl) {
            if (ct_ultimate_gdpr_cookie.readurl_new_tab == "off"){
                window.location.href = ct_ultimate_gdpr_cookie.readurl;
            }else{
                window.open(ct_ultimate_gdpr_cookie.readurl, '_blank');
            }
        }
    }

    function onCloseModal(e) {
        e.preventDefault();
        acceptConcent();
        var level = ct_ultimate_gdpr_cookie.consent_default_level;

        jQuery.post( ct_ultimate_gdpr_cookie.ajaxurl, {
                "action"                        : "ct_ultimate_gdpr_cookie_consent_decline",
                "ct_ultimate_gdpr_button_close" : "1"
            },
            function () {
                if (ct_ultimate_gdpr_cookie.reload) {
                    window.location.reload(true);
                }
            });

        if (isModalAlwaysVisible()) return;

        jQuery('#ct-ultimate-gdpr-cookie-popup').hide();
        jQuery('.ct-ultimate-gdpr-cookie-fullPanel-overlay').hide();

    }
    // 1173
    function ASCValidate(array) {
    var length = array.length;
    return array.every(function(value, index) {
        var nextIndex = index + 1;
        return nextIndex < length ? value < array[nextIndex] : true;
    });
    }

    function onSave(e) {

        e.preventDefault();
        acceptConcent();

        var level          = $('.ct-ultimate-gdpr-cookie-modal-slider-item--active input').val(),
            protectLevel   = $(".ct-ultimate-gdpr-shortcode-protection").attr('data-level'),
            init_level_id  = $('li input.ct-ultimate-gdpr-cookie-modal-single-item.ct-cookie-item-selected').map(function(){ return this.value; }).get();

        let level_id = init_level_id; // 1173

        if(init_level_id.length > 0) {
            $('li input.ct-ultimate-gdpr-cookie-modal-single-item.ct-cookie-item-selected').each(function(e) {
                if( !ASCValidate(level_id) ) {
                    level_id = level_id.slice(0, -1); // delete last index until statement is true.
                }
                if( init_level_id[0] === '1') { // force block-all = 1.
                    level_id = ['1'];
                }
            });
        }
        
        document.cookie = 'ct-ultimate-gdpr-cookie=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';

        jQuery.post(ct_ultimate_gdpr_cookie.ajaxurl, {
            "action": "ct_ultimate_gdpr_cookie_consent_give",
            "level": level,
            "level_id": level_id
        }, function () {
            if (ct_ultimate_gdpr_cookie.reload) {
                window.location.reload(true);
            }
        }).fail(function () {

            jQuery.post(ct_ultimate_gdpr_cookie.ajaxurl, {
                "skip_cookies": true,
                "action": "ct_ultimate_gdpr_cookie_consent_give",
                "level": level,
                "level_id": level_id
            }, function () {
                setJsCookie(level);
                if (ct_ultimate_gdpr_cookie.reload) {
                    window.location.reload(true);
                }
            })

        });

        if (!ct_ultimate_gdpr_cookie.reload) {
            hideModal();
            hidePopup();
            if (level >= protectLevel) {
                $(".ct-ultimate-gdpr-shortcode-protection").removeClass("blur")
                $("span.ct-ultimate-gdpr-shortcode-protection-label").remove();
                var content = $("div.ct-ultimate-gdpr-shortcode-protection").text();
                var result = $.base64.decode(content);
                $(".ct-ultimate-gdpr-shortcode-protection").html(result);
            }
        }
        $('body').removeClass("ct-ultimate-gdpr-cookie-bottomPanel-padding");
        $('body').removeClass("ct-ultimate-gdpr-cookie-topPanel-padding");
        $('html').removeClass("cookie-modal-open");
        $('body').removeClass("cookie-modal-open");

        if (level >= protectLevel) {
            $(".ct-ultimate-gdpr-shortcode-protection").removeClass("blur")
            $("span.ct-ultimate-gdpr-shortcode-protection-label").remove();
            var content = $("div.ct-ultimate-gdpr-shortcode-protection").text();
            var result = $.base64.decode(content);
            $(".ct-ultimate-gdpr-shortcode-protection").html(result);
        }
    }

    function removerAjaxScript() {
        var ajaxScriptCount = $("div[id^=ajax-cookie-popup-js]").length;
        for (let i = 0; i < ajaxScriptCount; i++) {
            $('#ajax-cookie-popup-js').remove();
        }
    }

    function acceptConcent() {
        if (typeof ct_ultimate_gdpr_service_gtm === "undefined") {
            return;
        }
        if(ct_ultimate_gdpr_service_gtm.id) {
            let consent = {};
            $('body').find('li input.ct-ultimate-gdpr-cookie-modal-single-item.ct-cookie-item-selected:checked').each(function (e) {
                // 5 = essentials = personalization
                // 6 = functionality = functionality_storage
                // 7 = analytics = analytics_storage
                // 8 = advertising = ad_storage
                if ($(this).val() === '5') consent.personalization = true;
                if ($(this).val() === '6') consent.functionality_storage = true;
                if ($(this).val() === '7') consent.analytics_storage = ($(this).val() === '7');
                if ($(this).val() === '8') consent.ad_storage = ($(this).val() === '8');
            });

            setConsent(consent);
        }
    }

    $('#ct-ultimate-gdpr-cookie-accept').bind('click', onAccept);
    $('#ct-ultimate-cookie-close-modal').bind('click', onCloseModal);
    $('#ct-ultimate-gdpr-cookie-read-more').bind('click', onRead);
    $('.ct-ultimate-gdpr-cookie-modal-btn.save').bind('click', onSave);

    //Close modal on x button
    $('#ct-ultimate-gdpr-cookie-modal-close,#ct-ultimate-gdpr-cookie-modal-compact-close').on('click', function () {

        if (isModalAlwaysVisible()) return;

        var modalbody = $("body");
        var modal = $("#ct-ultimate-gdpr-cookie-modal");
        modal.hide();

        removerAjaxScript();
        modalbody.removeClass("cookie-modal-open");
        $("html").removeClass("cookie-modal-open");
    });

    //Close modal when clicking outside of modal area.
    $('#ct-ultimate-gdpr-cookie-modal').on("click", function (e) {

        if (isModalAlwaysVisible()) return;

        if (!($(e.target).closest('#ct-ultimate-gdpr-cookie-change-settings, .ct-ultimate-gdpr-cookie-modal-content').length)) {
            var modalbody = $("body");
            var modal = $("#ct-ultimate-gdpr-cookie-modal");
            modal.hide();

            removerAjaxScript();
            modalbody.removeClass("cookie-modal-open");
            $("html").removeClass("cookie-modal-open");
        }

        e.stopPropagation();
    });

    //SVG
    jQuery('img.ct-svg').each(function () {
        var $img = jQuery(this);
        var imgID = $img.attr('id');
        var imgClass = $img.attr('class');
        var imgURL = $img.attr('src');

        jQuery.get(imgURL, function (data) {
            // Get the SVG tag, ignore the rest
            var $svg = jQuery(data).find('svg');

            // Add replaced image's ID to the new SVG
            if (typeof imgID !== 'undefined') {
                $svg = $svg.attr('id', imgID);
            }
            // Add replaced image's classes to the new SVG
            if (typeof imgClass !== 'undefined') {
                $svg = $svg.attr('class', imgClass + ' replaced-svg');
            }

            // Remove any invalid XML tags as per http://validator.w3.org
            $svg = $svg.removeAttr('xmlns:a');

            // Check if the viewport is set, else we gonna set it if we can.
            if (!$svg.attr('viewBox') && $svg.attr('height') && $svg.attr('width')) {
                $svg.attr('viewBox', '0 0 ' + $svg.attr('height') + ' ' + $svg.attr('width'))
            }

            // Replace image with new SVG
            $img.replaceWith($svg);

        }, 'xml');
    });

    function alwaysDisplayCookie(){
    if(ct_ultimate_gdpr_cookie.display_cookie_always) {
        showPopup();
        jQuery('#ct-ultimate-gdpr-cookie-open').hide();
    } 
    }

    function deleteAllCookies(){
    var cookies = document.cookie.split(";");

        for (var i = 0; i < cookies.length; i++) {
            var cookie = cookies[i];
            var eqPos = cookie.indexOf("=");
            var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
            setCookie(name,'',-1);
        }
    }

        
    function setCookie(name,value,days) {
        var expires = "";
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days*24*60*60*1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "")  + expires + "; path=/";
    }

    function resetCookieConsent(){
        if(ct_ultimate_gdpr_cookie.cookie_reset_consent){
        deleteAllCookies();
        } else {
            // do nothing here
        }
    }

    $(window).on('load', function () {
        var selected = $('.ct-ultimate-gdpr-cookie-modal-slider-item--active');

        var checked = selected.find('input');
        var input_id = checked.attr('id');
        var $count = checked.attr('data-count');
        selected.find('path').css('fill', '#82aa3b');
        selected.prevUntil('#ct-ultimate-gdpr-cookie-modal-slider-item-block').addClass('ct-ultimate-gdpr-cookie-modal-slider-item--selected');
        checked.parent().prevUntil('#ct-ultimate-gdpr-cookie-modal-slider-item-block').find('path').css('fill', '#82aa3b');

        $('#ct-ultimate-gdpr-cookie-modal-slider-form').attr('class', 'ct-slider-cookie' + $count);
        $('.ct-ultimate-gdpr-cookie-modal-slider-info.' + 'cookie_' + $count).css('display', 'block');

        if( getCookie('ct-ultimate-gdpr-cookie-popup') == 1 ){
            jQuery('#ct-ultimate-gdpr-cookie-popup').hide();
            jQuery('.ct-ultimate-gdpr-cookie-fullPanel-overlay').hide();
        }    


    });

    $('.ct-ultimate-gdpr-cookie-modal-slider').each(function () {

        var $btns = $('.ct-ultimate-gdpr-cookie-modal-slider-item').click(function () {

            var $input = $(this).find('input').attr('id');

            $('.tab').removeClass('ct-ultimate-gdpr-cookie-modal-active-tab');
            $('.tab.' + $input).addClass('ct-ultimate-gdpr-cookie-modal-active-tab');

            var $el = $('.' + $input);
            $el.show();
            var form_class = $('#ct-ultimate-gdpr-cookie-modal-slider-form');
            var modalBody = $('div#ct-ultimate-gdpr-cookie-modal-body');

            var $count = $(this).find('input').attr('data-count');

            $('#ct-ultimate-gdpr-cookie-modal-slider-form').attr('class', 'ct-slider-cookie' + $count);
            $('.ct-ultimate-gdpr-cookie-modal-slider-wrap .ct-ultimate-gdpr-cookie-modal-slider-info').not($el).hide();

            $btns.removeClass('ct-ultimate-gdpr-cookie-modal-slider-item--active');
            $(this).addClass('ct-ultimate-gdpr-cookie-modal-slider-item--active');

            $(this).prevUntil('#ct-ultimate-gdpr-cookie-modal-slider-item-block').find('path').css('fill', '#82aa3b');
            $(this).prevUntil('#ct-ultimate-gdpr-cookie-modal-slider-item-block').addClass('ct-ultimate-gdpr-cookie-modal-slider-item--selected');
            $(this).find('path').css('fill', '#82aa3b');
            $(this).nextAll().find('path').css('fill', '#595959');
            $(this).removeClass('ct-ultimate-gdpr-cookie-modal-slider-item--selected');
            $(this).nextAll().removeClass('ct-ultimate-gdpr-cookie-modal-slider-item--selected');

            if ($(this).attr('id') === 'ct-ultimate-gdpr-cookie-modal-slider-item-block') {
                modalBody.addClass('ct-ultimate-gdpr-slider-block');
                modalBody.removeClass('ct-ultimate-gdpr-slider-not-block');
            } else {
                modalBody.removeClass('ct-ultimate-gdpr-slider-block');
                modalBody.addClass('ct-ultimate-gdpr-slider-not-block');
            }

        });

    });

    // 1173 granular cookie
    $('li input.ct-ultimate-gdpr-cookie-modal-single-item').click(function (elem) {
        
        $('input[type=radio]').on('change', function() {
            // $('.ct-ultimate-gdpr-cookie-modal-single-wrap').hide();
            var radioParent = $(this).closest("li");
            $("input[type=checkbox]").prop( "checked", false );

            if( $(this).prop("checked") ) {
                radioParent.addClass('active');
                $(this).addClass('ct-cookie-item-selected');
                $("#cookie5").closest("li").removeClass('active');
                $("#cookie6").closest("li").removeClass('active');
                $("#cookie7").closest("li").removeClass('active');
                $("#cookie8").closest("li").removeClass('active');
                $("#cookie5").closest("li").find('path').css('fill', '#595959');
                $("#cookie6").closest("li").find('path').css('fill', '#595959');
                $("#cookie7").closest("li").find('path').css('fill', '#595959');
                $("#cookie8").closest("li").find('path').css('fill', '#595959');
                radioParent.find('path').css('fill', '#82aa3b');

                

            }
            else {
                $(this).removeClass('ct-cookie-item-selected');
                $(this).removeAttr('checked');
            } 

        });

        $('input[type=checkbox]').on('change', function() {      
            $('.ct-ultimate-gdpr-cookie-modal-single-wrap').show();
            var checkboxParent = $(this).closest("li");
            $("#cookie0").prop( "checked", false );
            $("#cookie0").closest("li").removeClass('active');
            $("#cookie0").removeClass('ct-cookie-item-selected');
            $("#cookie0").closest("li").find('path').css('fill', '#111');

            if( $(this).prop("checked") ) {
                $(this).addClass('ct-cookie-item-selected');
                checkboxParent.addClass('active');
                checkboxParent.find('path').css('fill', '#82aa3b');
            }
            else {
                $(this).removeAttr('checked');
                $(this).removeClass('ct-cookie-item-selected');
                checkboxParent.removeClass('active');
                checkboxParent.find('path').css('fill', '#595959');
            }
        });

    });

    $('.ct-ultimate-gdpr-cookie-modal-single').on('change',function(){
        displayItalianDescription();
    });

    /* HIDE DETAILS BUTTON */
    $('.hide-btn').on('click', function () {

        var infoBox = $('.ct-ultimate-gdpr-cookie-modal-slider-wrap');
        var hideBtnI = jQuery(this).find('span');
        if (infoBox.is(':hidden')) {
            hideBtnI.removeClass('fa-chevron-down');
            hideBtnI.addClass('fa-chevron-up');
        } else {
            hideBtnI.removeClass('fa-chevron-up');
            hideBtnI.addClass('fa-chevron-down');
        }
        infoBox.slideToggle();
    });

    // MAKE LEFT PANEL OF COOKIE MODAL POPUP INFO BOX IN COMPACT SKINS CLICKABLE
    $('.cookie-modal-tab-wrapper li').on('click', function () {
        var $this = jQuery(this);
        var cookieType = '';
        if ($this.hasClass('cookie0')) {
            cookieType = 'cookie0';
        } else if ($this.hasClass('cookie1')) {
            cookieType = 'cookie1';
        } else if ($this.hasClass('cookie2')) {
            cookieType = 'cookie2';
        } else if ($this.hasClass('cookie3')) {
            cookieType = 'cookie3';
        } else if ($this.hasClass('cookie4')) {
            cookieType = 'cookie4';
        }
        var formCookieType = jQuery('#ct-ultimate-gdpr-cookie-modal-slider-form').find('#' + cookieType);
        formCookieType.parent().click();
    });

    if ($("#ct-ultimate-gdpr-cookie-popup").hasClass("ct-ultimate-gdpr-cookie-topPanel")) {
        if (!ct_ultimate_gdpr_cookie.consent) {
            $('body').addClass("ct-ultimate-gdpr-cookie-topPanel-padding");
        }
    }

    if ($("#ct-ultimate-gdpr-cookie-popup").hasClass("ct-ultimate-gdpr-cookie-bottomPanel")) {
        if (!ct_ultimate_gdpr_cookie.consent) {
            $('body').addClass("ct-ultimate-gdpr-cookie-bottomPanel-padding");
        }
    }

    if ($("#ct-ultimate-gdpr-cookie-popup").hasClass("ct-ultimate-gdpr-cookie-topPanel ct-ultimate-gdpr-cookie-popup-modern")) {
        $('body').addClass("popup-modern-style");
    }

    if ($("#ct-ultimate-gdpr-cookie-popup").hasClass("ct-ultimate-gdpr-cookie-bottomPanel ct-ultimate-gdpr-cookie-popup-modern")) {
        $('body').addClass("popup-modern-style");
    }

    $(window).on('load resize', function() {
        var cookiePopupHeight = $('#ct-ultimate-gdpr-cookie-popup').outerHeight();

        $('.ct-ultimate-gdpr-cookie-bottomPanel-padding').css('padding-bottom', cookiePopupHeight);
    });

    function displayItalianDescription(){
        var blockAll = $('#cookie0');
        var essentials = $('#cookie5');
        var functionality = $('#cookie6');
        var analytics = $('#cookie7');
        var advertising = $('#cookie8');
        var descriptions = $('.ct-ultimate-gdpr-cookie-modal-single-wrap__inner--info');

        if(essentials.prop('checked')) {
            descriptions.find('#desc-left li.essentials').show();
            descriptions.find('#desc-right li.essentials').hide();
            } else {
            descriptions.find('#desc-right li.essentials').show();
            descriptions.find('#desc-left li.essentials').hide();

            }
            if(functionality.prop('checked')) {
            descriptions.find('#desc-left li.functionality').show();
            descriptions.find('#desc-right li.functionality').hide();
        } else {
            descriptions.find('#desc-right li.functionality').show();
            descriptions.find('#desc-left li.functionality').hide();

        }

        if(analytics.prop('checked')) {
            descriptions.find('#desc-left li.analytics').show();
            descriptions.find('#desc-right li.analytics').hide();
        } else {
            descriptions.find('#desc-right li.analytics').show();
            descriptions.find('#desc-left li.analytics').hide();

        }

        if(advertising.prop('checked')) {
            descriptions.find('#desc-left li.advertising').show();
            descriptions.find('#desc-right li.advertising').hide();
        } else {
            descriptions.find('#desc-right li.advertising').show();
            descriptions.find('#desc-left li.advertising').hide();

        }

        if(blockAll.prop('checked')) {
            descriptions.find('#desc-left').find('li').hide();
            descriptions.find('#desc-right').find('li').show();
            descriptions.find('#desc-left li.block-all').show();
            descriptions.find('#desc-right li.block-all').hide();
        } else {
            descriptions.find('#desc-right li.block-all').show();
            descriptions.find('#desc-left li.block-all').hide();
        }
    }

    $(document).on('ct-age-clicked', maybeShowPopup);

    maybeShowPopup();
    displayItalianDescription();
    alwaysDisplayCookie();
    resetCookieConsent();

});

</script>

</div>

<?php
// class for number of groups
$number_of_groups = 0;
$number_of_groups = ( ! empty( $options['cookie_group_popup_hide_level_1'] ) ) ? ++ $number_of_groups : $number_of_groups;
$number_of_groups = ( ! empty( $options['cookie_group_popup_hide_level_2'] ) ) ? ++ $number_of_groups : $number_of_groups;
$number_of_groups = ( ! empty( $options['cookie_group_popup_hide_level_3'] ) ) ? ++ $number_of_groups : $number_of_groups;
$number_of_groups = ( ! empty( $options['cookie_group_popup_hide_level_4'] ) ) ? ++ $number_of_groups : $number_of_groups;
$number_of_groups = ( ! empty( $options['cookie_group_popup_hide_level_5'] ) ) ? ++ $number_of_groups : $number_of_groups;

$group_class = 'ct-ultimate-gdpr--Groups-' . ( 5 - $number_of_groups );
$group_class = ( empty( $options['cookie_group_popup_hide_level_1'] ) ) ? $group_class : $group_class . ' ct-ultimate-gdpr--NoBlockGroup';

if ( isset ( $options['cookie_trigger_modal_bg_shape'] ) ) :
	if ( $options['cookie_trigger_modal_bg_shape'] == 'round' ):
		$cookie_trigger_modal_bg_shape = 'ct-ultimate-gdpr-trigger-modal-round';
    elseif ( $options['cookie_trigger_modal_bg_shape'] == 'rounded' ) :
		$cookie_trigger_modal_bg_shape = 'ct-ultimate-gdpr-trigger-modal-rounded';
    elseif ( $options['cookie_trigger_modal_bg_shape'] == 'squared' ) :
		$cookie_trigger_modal_bg_shape = 'ct-ultimate-gdpr-trigger-modal-squared';
	endif;
else :
	$cookie_trigger_modal_bg_shape = '';
endif;

/*Modal Skin*/
if ( $options['cookie_modal_skin'] == 'style-one' ) :
	$cookie_modal_skin = esc_attr( 'ct-ultimate-gdpr-cookie-skin-one' );
	$block_icon = ct_ultimate_gdpr_url() . '/assets/css/images/block-all.svg';
	$ess_icon = ct_ultimate_gdpr_url() . '/assets/css/images/essential.svg';
	$func_icon = ct_ultimate_gdpr_url() . '/assets/css/images/skin1-func.svg';
	$ana_icon = ct_ultimate_gdpr_url() . '/assets/css/images/skin1-ana.svg';
	$adv_icon = ct_ultimate_gdpr_url() . '/assets/css/images/skin1-adv.svg';
elseif ( $options['cookie_modal_skin'] == 'style-two' ) :
	$cookie_modal_skin = esc_attr( 'ct-ultimate-gdpr-cookie-skin-two' );
	$block_icon = ct_ultimate_gdpr_url() . '/assets/css/images/block-all.svg';
	$ess_icon = ct_ultimate_gdpr_url() . '/assets/css/images/skin2-ess.svg';
	$func_icon = ct_ultimate_gdpr_url() . '/assets/css/images/skin2-func.svg';
	$ana_icon = ct_ultimate_gdpr_url() . '/assets/css/images/skin2-ana.svg';
	$adv_icon = ct_ultimate_gdpr_url() . '/assets/css/images/skin2-adv.svg';
elseif ( $options['cookie_modal_skin'] == 'default' ) :
	$cookie_modal_skin = "";
	$block_icon = ct_ultimate_gdpr_url() . '/assets/css/images/block-all.svg';
	$ess_icon = ct_ultimate_gdpr_url() . '/assets/css/images/essential.svg';
	$func_icon =  ct_ultimate_gdpr_url() . '/assets/css/images/functionality.svg';
	$ana_icon = ct_ultimate_gdpr_url() . '/assets/css/images/statistics.svg';
	$adv_icon = ct_ultimate_gdpr_url() . '/assets/css/images/targeting.svg';
else :
	$block_icon = $ess_icon = $func_icon = $ana_icon = $adv_icon = '';
	$cookie_modal_skin = $options['cookie_modal_skin'];
endif;

$cookie_modal_type = '';
if ( $cookie_modal_skin == 'compact-green' ) {
	$cookie_modal_type = ' ' . 'ct-ultimate-gdpr-cookie-modal-compact ct-ultimate-gdpr-cookie-modal-compact-green';
} elseif ( $cookie_modal_skin == 'compact-light-blue' ) {
	$cookie_modal_type = ' ' . 'ct-ultimate-gdpr-cookie-modal-compact ct-ultimate-gdpr-cookie-modal-compact-light-blue';
} elseif ( $cookie_modal_skin == 'compact-dark-blue' ) {
	$cookie_modal_type = ' ' . 'ct-ultimate-gdpr-cookie-modal-compact ct-ultimate-gdpr-cookie-modal-compact-dark-blue';
}

// SHORTCODE
if ( ! empty( $options['content'] ) ) : ?>
    <a href="#" class="ct-ultimate-triggler-modal-sc"><?php echo esc_html( $options['content'] ); ?></a>
<?php endif;


/* END MODAL VARIABLES */

/** @var array $options */

/*
*Check user agent
*Return false
 */

if ( empty( $options['cookie_modal_always_visible'] ) ) :

    $distance = isset( $options['cookie_position_distance'] ) ? $options['cookie_position_distance'] : 0;
	$skin_location_class = $box_style_class = $box_shape_class = $btn_shape_class = $btn_size_class = $top_panel_attr =
	$bottom_panel_attr = $card_attr = $needle = $replacement = $haystack = $popup_panel_open_tag =
	$popup_btn_wrap_open_tag = $close_tag = $cookie_box_bg = $box_css = $light_img = $content_style = $skin_name =
	$arrow = $btn_icon = $check = $left_cog = $right_cog = $close_tags = $accept_border_color = $accept_bg_color = $accept_color =
	$accept_btn_content = $is_10_set = $adv_set_border_color = $adv_set_bg_color = $adv_set_color =
	$adv_set_btn_content = $_10_set = $btn_wrapper = $btn_wrapper_end = $attachment_url = $attachment_image = '';
	$class_array = $attr_array = array();
	$accept_label = esc_html(
		ct_ultimate_gdpr_get_value(
			'cookie_popup_label_accept',
			$options,
			esc_html__( 'Accept', 'ct-ultimate-gdpr' ),
			false
		)
	);
	$adv_set_label = esc_html(
		ct_ultimate_gdpr_get_value(
			'cookie_popup_label_settings',
			$options,
			esc_html__( 'Change Settings', 'ct-ultimate-gdpr' ),
			false
		)
	);
	$cookie_read_page_custom = isset( $options['cookie_read_page_custom'] ) ? $options['cookie_read_page_custom'] : '';
	$cookie_read_page = isset( $options['cookie_read_page'] ) ? $options['cookie_read_page'] : '';

	if ( isset( $options['cookie_position'] ) ) :
		$ct_gdpr_is_panel_array = ct_gdpr_is_panel( $options['cookie_position'], $distance );
		$skin_location_class = $ct_gdpr_is_panel_array['skin_location_class'];
		$panel_attr = $ct_gdpr_is_panel_array['panel_attr'];
		$popup_panel_open_tag = $ct_gdpr_is_panel_array['popup_panel_open_tag'];
		$close_tags = $ct_gdpr_is_panel_array['close_tag'];
	endif;

	if ( isset( $options['cookie_box_style'] ) ) :
		$box_css = $options['cookie_box_style'];

		$ct_gdpr_set_btn_css_array = ct_gdpr_set_btn_css(
			$box_css,
			$options['cookie_position'],
			$options['cookie_button_bg_color'],
			$options['cookie_button_border_color'],
			$options['cookie_button_text_color']
		);
		$accept_border_color = $ct_gdpr_set_btn_css_array['accept_border_color'];
		$accept_bg_color = $ct_gdpr_set_btn_css_array['accept_bg_color'];
		$accept_color = $ct_gdpr_set_btn_css_array['accept_color'];

		$adv_set_border_color = $ct_gdpr_set_btn_css_array['adv_set_border_color'];
		$adv_set_bg_color = $ct_gdpr_set_btn_css_array['adv_set_bg_color'];
		$adv_set_color = $ct_gdpr_set_btn_css_array['adv_set_color'];
	endif;

	if ( isset( $box_css ) ) :
		$cookie_box_style_array = ct_gdpr_get_box_style_class_and_wrapper( $box_css );
		$box_style_class = $cookie_box_style_array['box_style_class'];
		$content_style = $cookie_box_style_array['content_style'];
		$popup_btn_wrap_open_tag = $cookie_box_style_array['popup_btn_wrap_open_tag'];
		$close_tag = $cookie_box_style_array['close_tag'];
		$skin_set = $cookie_box_style_array['skin_set'];

		$btn_wrapper = $skin_set == '1' ? '<div class="ct-ultimate-gdpr-cookie-popup-btn-wrapper">' : '' ;
		$btn_wrapper_end = $skin_set == '1' ? '</div>' : '' ;

		$skin_name = strtok( $box_css, '_' );
		if ( isset( $options['cookie_button_settings'] ) ) :
			$btn_settings = $options['cookie_button_settings'];
			$ct_gdpr_get_icon_array = ct_gdpr_get_icon( $btn_settings, $skin_name );
			$arrow = $ct_gdpr_get_icon_array['arrow'];
			$btn_icon = $ct_gdpr_get_icon_array['btn_icon'];
			$check = $ct_gdpr_get_icon_array['check'];
			$right_cog = $ct_gdpr_get_icon_array['right_cog'];
			$left_cog = $ct_gdpr_get_icon_array['left_cog'];
			$accept_btn_content = ct_gdpr_get_accept_content( $btn_settings, $skin_name, $check, $accept_label );
			$adv_set_btn_content = ct_gdpr_get_adv_set_content( $btn_settings, $adv_set_label, $left_cog, $right_cog );
			$read_more_10_set = ct_gdpr_get_10_set_read_more_content( $options, $arrow, $skin_name);
		endif;
	endif;

	if ( isset( $options['cookie_box_shape'] ) ) :
		if ( $options['cookie_box_shape'] == 'squared' ) :
			$box_shape_class = esc_attr( 'ct-ultimate-gdpr-cookie-popup-squared' );
		endif;
	endif;

	if ( isset( $options['cookie_button_shape'] ) ) :
		if ( $options['cookie_button_shape'] == 'rounded' ) :
			$btn_shape_class = esc_attr( 'ct-ultimate-gdpr-cookie-popup-button-rounded' );
		endif;
	endif;

	if ( isset( $options['cookie_button_size'] ) ) :
		if ( $options['cookie_button_size'] == 'large' ) :
			$btn_size_class = esc_attr( 'ct-ultimate-gdpr-cookie-popup-button-large' );
		endif;
	endif;

	$ct_gdpr_get_box_bg_array = ct_gdpr_get_box_bg( $options['cookie_background_image'], $box_css );
	$cookie_box_bg = $ct_gdpr_get_box_bg_array['img'];
	$light_img = $ct_gdpr_get_box_bg_array['light_img'];

	$class_array = array(
		$skin_location_class,
		$box_style_class,
		$box_shape_class,
		$btn_shape_class,
		$btn_size_class,
	);
	$attr_array = array(
		$panel_attr,
		$bottom_panel_attr,
		$ct_gdpr_is_panel_array['card_attr'],
		$cookie_box_bg,
	);

	$is_10_set = strtok( $box_style_class, ' ' );
	$_10_set = $is_10_set == 'ct-ultimate-gdpr-cookie-popup-10-set' ? true : false;
	?>

	<?php if ( $options['cookie_position'] == "full_layout_panel_" ) : ?>
        <div class="ct-ultimate-gdpr-cookie-fullPanel-overlay"></div>
	<?php endif; ?>

<?php endif; ?>

<div id="ct-ultimate-gdpr-cookie-modal" class="<?=esc_attr($group_class) . esc_attr( $cookie_modal_type ); ?>" style="display: block"> 
		
	<!-- Modal content -->
    <div class="ct-ultimate-gdpr-cookie-modal-content <?=esc_attr($cookie_modal_skin); ?> ct-ultimate-gdpr-cookie-modal-content-single">
		
		<?php
		if ( ! $cookie_modal_type == ' ct-ultimate-gdpr-cookie-modal-compact ct-ultimate-gdpr-cookie-modal-compact-light-blue'
			|| ! $cookie_modal_type == ' ct-ultimate-gdpr-cookie-modal-compact ct-ultimate-gdpr-cookie-modal-compact-dark-blue'
			|| ! $cookie_modal_type == ' ct-ultimate-gdpr-cookie-modal-compact ct-ultimate-gdpr-cookie-modal-compact-green') : ?>
            <div id="ct-ultimate-gdpr-cookie-modal-close"></div>
		<?php endif; ?>

        <div id="ct-ultimate-gdpr-cookie-modal-body" class="<?=(CT_Ultimate_GDPR_Model_Group::LEVEL_BLOCK_ALL == apply_filters('ct_ultimate_gdpr_controller_cookie_id', 0)) ? 'ct-ultimate-gdpr-slider-block' : 'ct-ultimate-gdpr-slider-not-block'; ?>">

			<?php
			if (
				$cookie_modal_type == ' ct-ultimate-gdpr-cookie-modal-compact ct-ultimate-gdpr-cookie-modal-compact-green'
				|| $cookie_modal_type == ' ct-ultimate-gdpr-cookie-modal-compact ct-ultimate-gdpr-cookie-modal-compact-light-blue'
				|| $cookie_modal_type == ' ct-ultimate-gdpr-cookie-modal-compact ct-ultimate-gdpr-cookie-modal-compact-dark-blue'
			) :
				?>
                <div id="ct-ultimate-gdpr-cookie-modal-compact-close"></div>
			<?php endif; ?>

			<?php
			if (!empty($options['cookie_group_popup_header_content'])) : ?>
                <div style="color:<?=esc_attr($options['cookie_modal_text_color'])?>"> <?=wp_kses_post($options['cookie_group_popup_header_content'])?> </div>
			<?php
			else:
				ct_ultimate_gdpr_locate_template('cookie-group-popup-header-content', true, $options);
			endif; ?>		

			<form action="#" class="ct-ultimate-gdpr-cookie-modal-single" >
				<ul>
					<?php if (empty($options['cookie_group_popup_hide_level_1'])) : ?>
						<li><label for="cookie0">
							<img class="ct-svg" src="<?php echo esc_url( $block_icon )?>" style="width: 60px;" alt="<?php echo esc_html(CT_Ultimate_GDPR_Model_Group::get_label(CT_Ultimate_GDPR_Model_Group::LEVEL_BLOCK_ALL)); ?>">
							</label>
							<span><?php echo esc_html(CT_Ultimate_GDPR_Model_Group::get_label(CT_Ultimate_GDPR_Model_Group::LEVEL_BLOCK_ALL)); ?></span>
							<input type="radio" name="radio-group" id="cookie0" value="1" class="ct-ultimate-gdpr-cookie-modal-single-item"  
								<?=CT_Ultimate_GDPR_Model_Group::is_level_checked( apply_filters('ct_ultimate_gdpr_controller_cookie_group_level', 0), 1)?>/></li>
					<?php endif; ?>
					<?php if (empty($options['cookie_group_popup_hide_level_2'])) : ?>
						<li><label for="cookie5">
							<img class="ct-svg" src="<?php echo esc_url( $ess_icon)?>" style="width: 60px;" alt="<?php echo esc_html(CT_Ultimate_GDPR_Model_Group::get_label(CT_Ultimate_GDPR_Model_Group::LEVEL_NECESSARY)); ?>">
							</label>
							<span><?php echo esc_html(CT_Ultimate_GDPR_Model_Group::get_label(CT_Ultimate_GDPR_Model_Group::LEVEL_NECESSARY)); ?></span>
							<input type="checkbox" name="radio-group" id="cookie5" value="5" class="ct-ultimate-gdpr-cookie-modal-single-item" 
							<?=CT_Ultimate_GDPR_Model_Group::is_level_checked( apply_filters('ct_ultimate_gdpr_controller_cookie_group_level', 0), 5)?> /></li>
					<?php endif; ?>
					<?php if (empty($options['cookie_group_popup_hide_level_3'])) : ?>
						<li><label for="cookie6">
							<img class="ct-svg" src="<?php echo esc_url( $func_icon)?>" alt="<?php echo esc_html(CT_Ultimate_GDPR_Model_Group::get_label(CT_Ultimate_GDPR_Model_Group::LEVEL_CONVENIENCE)); ?>">
							</label>
							<span><?php echo esc_html(CT_Ultimate_GDPR_Model_Group::get_label(CT_Ultimate_GDPR_Model_Group::LEVEL_CONVENIENCE)); ?></span>
							<input type="checkbox" name="radio-group"  id="cookie6" value="6" class="ct-ultimate-gdpr-cookie-modal-single-item"
							<?=CT_Ultimate_GDPR_Model_Group::is_level_checked( apply_filters('ct_ultimate_gdpr_controller_cookie_group_level', 0), 6)?>/></li>
					<?php endif; ?>
					<?php if (empty($options['cookie_group_popup_hide_level_4'])) : ?>
						<li><label for="cookie7">
							<img class="ct-svg" src="<?php echo esc_url( $ana_icon )?>" alt="<?php echo esc_html(CT_Ultimate_GDPR_Model_Group::get_label(CT_Ultimate_GDPR_Model_Group::LEVEL_STATISTICS)); ?>">
							</label>
							<span><?php echo esc_html(CT_Ultimate_GDPR_Model_Group::get_label(CT_Ultimate_GDPR_Model_Group::LEVEL_STATISTICS)); ?></span>
							<input type="checkbox" name="radio-group"  id="cookie7" value="7" class="ct-ultimate-gdpr-cookie-modal-single-item" 
							<?=CT_Ultimate_GDPR_Model_Group::is_level_checked( apply_filters('ct_ultimate_gdpr_controller_cookie_group_level', 0), 7)?>/></li>
					<?php endif; ?>
					<?php if (empty($options['cookie_group_popup_hide_level_5'])) : ?>
						<li><label for="cookie8">
							<img class="ct-svg" src="<?php echo esc_url( $adv_icon ) ?>" alt="<?php echo esc_html(CT_Ultimate_GDPR_Model_Group::get_label(CT_Ultimate_GDPR_Model_Group::LEVEL_TARGETTING)); ?>">
							</label>
							<span><?php echo esc_html(CT_Ultimate_GDPR_Model_Group::get_label(CT_Ultimate_GDPR_Model_Group::LEVEL_TARGETTING)); ?></span>
							<input type="checkbox" name="radio-group"  id="cookie8" value="8" class="ct-ultimate-gdpr-cookie-modal-single-item" 
							<?=CT_Ultimate_GDPR_Model_Group::is_level_checked( apply_filters('ct_ultimate_gdpr_controller_cookie_group_level', 0), 8)?>/></li>
					<?php endif; ?>
				</ul>
			</form>

			<div class="ct-ultimate-gdpr-cookie-modal-single-wrap">
				<div class="ct-ultimate-gdpr-cookie-modal-single-wrap__inner">
                   
					<div class="ct-ultimate-gdpr-cookie-modal-single-wrap__inner--title">
						<div class="title-block">			
							<h4 style="color: <?php echo esc_attr($options['cookie_modal_header_color']); ?>;"><?php echo esc_html(ct_ultimate_gdpr_get_value("cookie_group_popup_label_will", $options, __('This website will:', 'ct-ultimate-gdpr'))); ?></h4></div>
						
						<div class="title-block">			
							<h4 style="color: <?php echo esc_attr($options['cookie_modal_header_color']); ?>;"><?php echo esc_html(ct_ultimate_gdpr_get_value("cookie_group_popup_label_wont", $options, __("This website wont't:", 'ct-ultimate-gdpr'))); ?></h4>
						</div>
						<div class="ct-clearfix"></div>
					</div> <!-- //end title -->
					<?php

						$ess_option_string = ct_ultimate_gdpr_get_value("cookie_group_popup_features_available_group_2_individual", $options);
						$essentials = array_filter(array_map('trim', explode(';', $ess_option_string)));

						$func_option_string = ct_ultimate_gdpr_get_value("cookie_group_popup_features_available_group_3_individual", $options);
						$functionalities = array_filter(array_map('trim', explode(';', $func_option_string)));

						$ana_option_string = ct_ultimate_gdpr_get_value("cookie_group_popup_features_available_group_4_individual", $options);
						$analytics = array_filter(array_map('trim', explode(';', $ana_option_string)));

						$adv_option_string = ct_ultimate_gdpr_get_value("cookie_group_popup_features_available_group_5_individual", $options);
						$advertisings = array_filter(array_map('trim', explode(';', $adv_option_string)));

					?>
					<div class="ct-ultimate-gdpr-cookie-modal-single-wrap__inner--info">
						<div class="ct-ultimate-gdpr-cookie-modal-single__info--desc" id="desc-left">
							<ul class="ct-ultimate-gdpr-cookie-modal-slider-able" style="color: <?php echo esc_attr($options['cookie_modal_text_color']); ?>;">
                                <li class="block-all"> <?php echo _e('Remember which cookies group you accepted','ct-ultimate-gdpr'); ?></li>
								<?php if(!empty($essentials) && empty($options['cookie_group_popup_hide_level_2']) ) : ?>
									<?php foreach ($essentials as $essential) : ?>
										<li class="essentials"> <?php echo esc_html($essential); ?></li>
									<?php endforeach; ?>
								<?php endif; ?>

								<?php if(!empty($functionalities) && empty($options['cookie_group_popup_hide_level_3']) ) : ?>
									<?php foreach ($functionalities as $functionality) : ?>
										<li class="functionality"> <?php echo esc_html($functionality); ?></li>
									<?php endforeach; ?>
								<?php endif; ?>
								
								<?php if(!empty($analytics) && empty($options['cookie_group_popup_hide_level_4']) ) : ?>
									<?php foreach ($analytics as $analytic) : ?>
										<li class="analytics"> <?php echo esc_html($analytic); ?></li>
									<?php endforeach; ?>
								<?php endif; ?>

								<?php if(!empty($advertisings) && empty($options['cookie_group_popup_hide_level_5']) ) : ?>
									<?php foreach ($advertisings as $advertising) : ?>
										<li class="advertising"> <?php echo esc_html($advertising); ?></li>
									<?php endforeach; ?>
								<?php endif; ?>
							</ul>
						</div>
						<div class="ct-ultimate-gdpr-cookie-modal-single__info--desc" id="desc-right">
							<ul class="ct-ultimate-gdpr-cookie-modal-slider-not-able" style="color: <?php echo esc_attr($options['cookie_modal_text_color']); ?>;">

								<li><?php echo esc_html__('Remember your login details', 'ct-ultimate-gdpr'); ?></li>
								<?php if(!empty($essentials) && empty($options['cookie_group_popup_hide_level_2']) ) : ?>
									<?php foreach ($essentials as $essential) : ?>
										<li class="essentials"> <?php echo esc_html($essential); ?></li>
									<?php endforeach; ?>
								<?php endif; ?>

								<?php if(!empty($functionalities) && empty($options['cookie_group_popup_hide_level_3']) ) : ?>
									<?php foreach ($functionalities as $functionality) : ?>
										<li class="functionality"> <?php echo esc_html($functionality); ?></li>
									<?php endforeach; ?>
								<?php endif; ?>
								
								<?php if(!empty($analytics) && empty($options['cookie_group_popup_hide_level_4']) ) : ?>
									<?php foreach ($analytics as $analytic) : ?>
										<li class="analytics"> <?php echo esc_html($analytic); ?></li>
									<?php endforeach; ?>
								<?php endif; ?>

								<?php if(!empty($advertisings) && empty($options['cookie_group_popup_hide_level_5']) ) : ?>
									<?php foreach ($advertisings as $advertising) : ?>
										<li class="advertising"> <?php echo esc_html($advertising); ?></li>
									<?php endforeach; ?>
								<?php endif; ?>

						    </ul>
						</div>
						<div class="ct-clearfix"></div>
					</div>
				</div>	
			</div>
			
			
			<div class="ct-ultimate-gdpr-cookie-modal-btn save">
                <a href="#"><?php echo esc_html(ct_ultimate_gdpr_get_value('cookie_group_popup_label_save', $options, esc_html__('Save & Close', 'ct-ultimate-gdpr'), false)); ?></a>
            </div>

		</div>
	</div>
</div>

<?php //endif; ?>