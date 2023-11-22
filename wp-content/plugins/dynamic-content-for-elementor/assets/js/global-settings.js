(function ($) {
	var is_animsition = false;
    var structure_wrapper = '';
    var structure_header = '';
    var settings_global = null;

    // TrackerHeader
    var headroom = null;
    var trackerHeader_element = null;
    var is_overlay = '';

    // smoothTransition
    var loading_mode = '';
    var loading_style = '';

    var initWrapping = function () {
        if (!$('.animsition').length) {
            $('#main').wrapInner('<div class="animsition" data-animsition-in-class="fade-in" data-animsition-in-duration="1000" data-animsition-out-class="fade-out" data-animsition-out-duration="800"></div>');
        }
    };
    var trackerHeaderHandler = function () {
        structure_header = settings_global.selector_header || '';
        is_overlay = Boolean(settings_global.trackerheader_overlay) || false;

        trackerHeader_element = $(structure_header);
        if (trackerHeader_element.length > 1) {
            if ($('#trackerheader-wrap').length) {
				trackerHeader_element.unwrap();
			}
            trackerHeader_element.wrapAll("<div id='trackerheader-wrap' />");
            trackerHeader_element = $('#trackerheader-wrap');
        }
        if (trackerHeader_element !== null && trackerHeader_element.length) {

            headroom = new Headroom(trackerHeader_element[0], {
                tolerance: {
                    down: 5,
                    up: 10
                },
                offset: 10,
                classes: {
                    // when element is initialized
                    initial: "trackerheader",
                    // when scrolling up
                    pinned: "trackerheader--pinned",
                    // when scrolling down
                    unpinned: "trackerheader--unpinned",
                    // when above offset
                    top: "trackerheader--top",
                    // when below offset
                    notTop: "trackerheader--not-top",
                    // when at bottom of scroll area
                    bottom: "trackerheader--bottom",
                    // when not at bottom of scroll area
                    notBottom: "trackerheader--not-bottom",
                    // when frozen method has been called
                    frozen: "trackerheader--frozen"
                },
            });
            headroom.init();
        }
        if (is_overlay) {
            generate_overlay();
        } else {
            remove_overlay();
        }
    };
    var trackerHeaderHandler_remove = function () {
        if (trackerHeader_element !== null && trackerHeader_element.length) {
            headroom.destroy();
            generate_overlay();
        }
    };
    var resizeHandle = function () {
        var headHeight = trackerHeader_element.outerHeight();
        if ($('body.admin-bar').length) {
            trackerHeader_element.css('top', 32);
        }
        $('body').css('padding-top', headHeight);
    };
    var remove_overlay = function ( ) {
        is_overlay = false;
        resizeHandle();
        $(window).on('resize', resizeHandle);
    };
    var generate_overlay = function ( ) {
        is_overlay = true;
        $(window).off('resize', resizeHandle);
        $('body').css('padding-top', 0);
    };
    var smoothTransitionHandler = function ( ) {

        structure_wrapper = settings_global.selector_wrapper || '#wrap';

        var speed_in = Number(settings_global.smoothtransition_speed_in.size) || 500;
        var speed_out = Number(settings_global.smoothtransition_speed_out.zize) || 500;

        var smoothtransition_loading_mode = settings_global.smoothtransition_loading_mode || 'circle';
        var smoothtransition_loading_style = settings_global.smoothtransition_loading_style || 'fade';

        var a_class = settings_global.a_class || 'a:not([target="_blank"]):not([href=""]):not([href^="uploads"]):not([href^="#"]):not([href^="mailto"]):not([href^="tel"]):not(.no-transition):not(.gallery-lightbox):not(.elementor-clickable):not(.oceanwp-lightbox):not(.is-lightbox):not(.elementor-icon):not(.download-link):not([href^="elementor-action"])';
        if (typeof elementorFrontend !== "undefined" && elementorFrontend.isEditMode()) {
            a_class = 'a.smoothtransition-enable';
        }

        // for enable or disable The Loading spin...
        enable_loading = true;
        if (smoothtransition_loading_mode == 'none') {
            enable_loading = false;
        }

        loading_mode = 'loading-mode-' + smoothtransition_loading_mode || 'circle';
        if (smoothtransition_loading_mode == 'image') {
            loading_style = 'loading-style-' + smoothtransition_loading_style || 'loading-style-rotate';
        }
        if (Boolean(settings_global.smoothtransition_enable_overlay)) {
            if (!$('body.smoothtransition-overlay').length)
                $('body').addClass('smoothtransition-overlay');
        }

        jQuery(a_class).each(function () {
            jQuery(this).addClass('animsition-link');
        });

        jQuery(structure_wrapper).animsition({
            inClass: 'dce-anim-style-in',
            outClass: 'dce-anim-style-out',
            inDuration: speed_in,
            outDuration: speed_out,
            linkElement: '.animsition-link',
            loading: true,
            loadingParentElement: 'body', //animsition wrapper element
            loadingClass: 'animsition-loading',
            loadingInner: '',
            timeout: false,
            timeoutCountdown: 5000,
            onLoadEvent: true,
            browser: ['animation-duration', '-webkit-animation-duration'],
            // "browser" option allows you to disable the "animsition" in case the css property in the array is not supported by your browser.
            // The default setting is to disable the "animsition" in a browser that does not support "animation-duration".
            overlay: false,
            overlayClass: 'animsition-overlay',
            overlayParentElement: 'body',
            transition: function (url) {
                window.location.href = url;
            }
        });
        is_animsition = true;

        jQuery(structure_wrapper).on('animsition.outStart', function () {
            jQuery('html,body').addClass('dce-modal-open');
            jQuery('body.smoothtransition-overlay').addClass('overlay-out');
        });
        jQuery(structure_wrapper).on('animsition.inEnd', function () {
            jQuery('html,body').removeClass('dce-modal-open');
        });
        jQuery(structure_wrapper).on('animsition.inStart', function () {

        });
    };
    function handleLoadingMode(newValue) {

        if (newValue) {

            loading_style = 'loading-style-' + elementor.settings.dynamicooo.model.attributes.smoothtransition_loading_style;

            $('.animsition-loading').removeClass(loading_mode);
            $('.animsition-loading').removeClass(loading_style);
            // reset to default

            if (newValue == 'image') {
                $('.animsition-loading').addClass(loading_style);
            }

            loading_mode = 'loading-mode-' + newValue;
            $('.animsition-loading').addClass(loading_mode);

        }
    }
    function handleOverlay(newValue) {

        if (newValue) {
            // SI
            if (!$('body.smoothtransition-overlay').length) {
				$('body').addClass('smoothtransition-overlay');
			}
        } else {
            // NO
            $('body').removeClass('smoothtransition-overlay');
        }
    }
    function handleLoadingStyle(newValue) {
        if (newValue) {
            // SI
            $('.animsition-loading').removeClass(loading_style);
            loading_style = 'loading-style-' + newValue;
            $('.animsition-loading').addClass(loading_style);
		}
    }
    function handleAnimsition(newValue) {
        $('body').toggleClass('dce-smoothtransition');

        if (newValue) {
            // SI

            if (! is_animsition) {
                settings_global = elementor.settings.dynamicooo.model.attributes;
            }

            var smoothtransitionClassController = settings_global.dce_smoothtransition_class_controller || '';
            if (smoothtransitionClassController) {
				smoothTransitionHandler();
			}

            // ...se lo spinner animsition-loading non esiste lo genero per poter controllare l'anteprima..
            if( !$('.animsition-loading').length ){
                var mainpagewrap = 'body';
                if('#outer-wrap'){
                    mainpagewrap = '#outer-wrap';
                }
                $(mainpagewrap).append('<div class="animsition-loading loading-mode-circle"></div>');

            }
        } else {
            // NO
            is_animsition = false;
            loading_mode = '';
            loading_style = '';
            $(structure_wrapper).animsition('destroy');
            $('.animsition-loading').remove();
            $('body').removeClass('smoothtransition-overlay');
        }
    }
    function handleHeaderTracker(newValue) {

        settings_global = elementor.settings.dynamicooo.model.attributes;

        if (newValue) {
            if (!$('body.dce-trackerheader').length)
                $('body').addClass('dce-trackerheader');
            trackerHeaderHandler();
        } else {
            if ($('body.dce-trackerheader').length)
                $('body').removeClass('dce-trackerheader');
            trackerHeaderHandler_remove();
        }

    }
    function handleTrackerHeader_Overlay(newValue) {
        if (newValue) {
            // SI
            generate_overlay();
        } else {
            // NO
            remove_overlay();
        }
    }
    // Make sure you run this code under Elementor..
    $(window).on('elementor/frontend/init', function () {

        // per il rendering della preview in EditMode
        if (elementorFrontend.isEditMode()) {

            if (elementor.settings.dynamicooo) {
                elementor.settings.dynamicooo.addChangeCallback('enable_smoothtransition', handleAnimsition);
                elementor.settings.dynamicooo.addChangeCallback('dce_smoothtransition_class_controller', handleAnimsition);
                elementor.settings.dynamicooo.addChangeCallback('smoothtransition_enable_overlay', handleOverlay);
                elementor.settings.dynamicooo.addChangeCallback('smoothtransition_loading_mode', handleLoadingMode);
                elementor.settings.dynamicooo.addChangeCallback('smoothtransition_loading_style', handleLoadingStyle);
                elementor.settings.dynamicooo.addChangeCallback('enable_trackerheader', handleHeaderTracker);
                elementor.settings.dynamicooo.addChangeCallback('dce_trackerheader_class_controller', handleHeaderTracker);
                elementor.settings.dynamicooo.addChangeCallback('trackerheader_overlay', handleTrackerHeader_Overlay);
            }
        }

    });
    $(function () {

        if (typeof elementorFrontendConfig !== "undefined") {
            settings_global = elementorFrontendConfig.settings.dynamicooo;
        } else {
            settings_global = dceGlobalSettings;
        }

        if (settings_global) {
            var responsive_smoothtransition = settings_global.responsive_smoothtransition || ['desktop', 'tablet', 'mobile'];
            var enableSmoothtransition = settings_global.enable_smoothtransition || '';
            var smoothtransitionClassController = settings_global.dce_smoothtransition_class_controller || '';
            var responsive_trackerheader = settings_global.responsive_trackerheader || ['desktop', 'tablet', 'mobile'];
            var enableTrackerHeader = settings_global.enable_trackerheader || '';
            var trackerheaderClassController = settings_global.dce_trackerheader_class_controller || '';
            var deviceMode = $('body').attr('data-elementor-device-mode') || 'desktop';

            if (enableSmoothtransition && $.inArray(deviceMode, responsive_smoothtransition) >= 0 && smoothtransitionClassController) {
				smoothTransitionHandler();
			}
                
            if (enableTrackerHeader && $.inArray(deviceMode, responsive_trackerheader) >= 0 && trackerheaderClassController) {
				$('body').addClass('dce-trackerheader');
				trackerHeaderHandler();
			}
               
        }
    });
})(jQuery);
