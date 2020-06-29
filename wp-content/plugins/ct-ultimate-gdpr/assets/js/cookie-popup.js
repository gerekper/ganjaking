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
                $('#ct-ultimate-gdpr-cookie-accept, #ct-ultimate-gdpr-cookie-change-settings').css({
                    'height': btnHeight + 'px',
                    'line-height': btnHeight + 'px'
                });
            } else {
                $('#ct-ultimate-gdpr-cookie-accept, #ct-ultimate-gdpr-cookie-change-settings').css({
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
        $(this).parents('#ct-ultimate-gdpr-cookie-modal').hide();
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
        // return !! ( window.ct_ultimate_gdpr_cookie_shortcode_popup && ct_ultimate_gdpr_cookie_shortcode_popup.always_visible );
    }

    function hidePopup() {

        if (isModalAlwaysVisible()) return;

        jQuery('#ct-ultimate-gdpr-cookie-popup').hide();
        jQuery('.ct-ultimate-gdpr-cookie-fullPanel-overlay').hide();
        jQuery('#ct-ultimate-gdpr-cookie-open').show();
    }

    function showPopup() {
        jQuery('#ct-ultimate-gdpr-cookie-popup').show();
    }

    function hideModal() {

        if (isModalAlwaysVisible()) return;

        jQuery('#ct-ultimate-gdpr-cookie-modal').hide();
        jQuery('#ct-ultimate-gdpr-cookie-open').show();
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

    function isConsentValid() {

        // on some setups php is unable to grab cookie in backend, then check in js below
        if (ct_ultimate_gdpr_cookie.consent) {
            return true;
        }

        var cookieValue = getCookie('ct-ultimate-gdpr-cookie');

        //fix for old version of the plugin
        if(cookieValue){
            var myCookieIsFromOldVersion = (cookieValue.indexOf("consent") !== -1);
            if(myCookieIsFromOldVersion){
                document.cookie = "ct-ultimate-gdpr-cookie" + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
                return  false;
            }
        }

        var cookieObject = cookieValue ? JSON.parse(atob(decodeURIComponent(cookieValue))) : {};

        //checking for latest/legacy expire_time js implementation
        if (cookieObject.consent_expire_time) {
            return cookieObject.consent_expire_time > +new Date / 1000;
        } else if (cookieObject.expire_time) {
            return cookieObject.expire_time > +new Date / 1000;
        } else {
            return false;
        }

    }

    // hide popup and show small gear icon if user already given consent
    if (isConsentValid()) {
        hidePopup();
    } else {
        showPopup();
        $('body').removeClass("ct-ultimate-gdpr-cookie-bottomPanel-padding");
        $('body').removeClass("ct-ultimate-gdpr-cookie-topPanel-padding");
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

    function onSave(e) {

        e.preventDefault();
        var level = $('.ct-ultimate-gdpr-cookie-modal-slider-item--active input').val();
        var protectLevel = $(".ct-ultimate-gdpr-shortcode-protection").attr('data-level');

        document.cookie = 'ct-ultimate-gdpr-cookie=;expires=Thu, 01 Jan 1970 00:00:01 GMT;';

        jQuery.post(ct_ultimate_gdpr_cookie.ajaxurl, {
            "action": "ct_ultimate_gdpr_cookie_consent_give",
            "level": level
        }, function () {
            if (ct_ultimate_gdpr_cookie.reload) {
                window.location.reload(true);
            }
        }).fail(function () {

            jQuery.post(ct_ultimate_gdpr_cookie.ajaxurl, {
                "skip_cookies": true,
                "action": "ct_ultimate_gdpr_cookie_consent_give",
                "level": level
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

    $('#ct-ultimate-gdpr-cookie-accept').bind('click', onAccept);
    $('#ct-ultimate-cookie-close-modal').bind('click', onCloseModal);
    $('#ct-ultimate-gdpr-cookie-read-more').bind('click', onRead);
    $('.ct-ultimate-gdpr-cookie-modal-btn.save').bind('click', onSave);


    //MODAL
    $('#ct-ultimate-gdpr-cookie-open,#ct-ultimate-gdpr-cookie-change-settings,.ct-ultimate-triggler-modal-sc').on('click', function (e) {
        var modalbody = $("body");
        var modal = $("#ct-ultimate-gdpr-cookie-modal");
        modal.show();
        $('.ct-ultimate-gdpr-cookie-modal-slider-item.ct-ultimate-gdpr-cookie-modal-slider-item--active').trigger('click');
        modalbody.addClass("cookie-modal-open");
        $("html").addClass("cookie-modal-open");
        e.stopPropagation();

        var slider_class = $("#ct-ultimate-gdpr-cookie-modal-slider-form").attr("class");
        var level = slider_class.substr(slider_class.length - 1);
        $(".ct-ultimate-gdpr-cookie-modal-slider li:nth-child(" + level + ")").addClass('ct-ultimate-gdpr-cookie-modal-slider-item--active');

    });

    //Close modal on x button
    $('#ct-ultimate-gdpr-cookie-modal-close,#ct-ultimate-gdpr-cookie-modal-compact-close').on('click', function () {

        if (isModalAlwaysVisible()) return;

        var modalbody = $("body");
        var modal = $("#ct-ultimate-gdpr-cookie-modal");
        modal.hide();
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

});