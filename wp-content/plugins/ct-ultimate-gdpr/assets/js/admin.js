jQuery(document).ready(function ($) {

    // set color picker options
    $().wpColorPicker && $('.ct-color-field').wpColorPicker();

    // select all checkboxes
    $("input[name=services-select-all]").on('click', function () {
        if ($(this).prop('checked')) {
            $(this).parent().parent().find("input[type=checkbox]").prop('checked', true);
        } else {
            $(this).parent().parent().find("input[type=checkbox]").prop('checked', false);
        }
    });

    // hide nag notices
    $("#wpbody-content > div.updated:not(.ct-ultimate-gdpr), #wpbody-content > div.notice:not(.ct-ultimate-gdpr), #wpbody-content > div.error:not(.ct-ultimate-gdpr)").hide();


    // ajax buttons
    $("input[name=ct-ultimate-gdpr-cookie-content-language]").on('click', function () {
        $.post(
            ajaxurl,
            {
                'action': 'ct_ultimate_gdpr_cookie_get_option_text',
                'language': $('#cookie_content_language').val()
            },
            function (response) {
                if (!response) return;
                for( var key in response) {
                    var val = response[ key ];

                    // switch text inputs values to chosen language content
                    $('#' + key).val(val);

                    // mce editors
                    $('#ct-ultimate-gdpr-cookie_' + key).val(val);
                    $('#ct-ultimate-gdpr-cookie_' + key + '_ifr').contents().find('body').html(val);

                };
            }
        );
    });

    $('.ct-iconpicker .ct-ip-holder .ct-ip-icon').on('click', function(){
        var iconPickerPopup = $('.ct-iconpicker .ct-ip-popup');

        iconPickerPopup.slideToggle();
    });
    $('.ct-iconpicker .ct-ip-popup').on('click', 'a', function (e) {
        e.preventDefault();
        var iconClass = $(this).data('icon'),
            inputField = $('.ct-icon-value'),
            iconHolder = $('.ct-ip-icon i');
        iconHolder.attr('class', '');
        iconHolder.addClass(iconClass);
        inputField.val(iconClass);
    });

    $('.ct-iconpicker .ct-ip-popup').on('change keyup paste', 'input.ct-ip-search-input', function (e) {
        var iconPickerPopup = $('.ct-iconpicker .ct-ip-popup ul'),
            searchVal = $(this).val();

        if( _.isEmpty(searchVal) ){
            iconPickerPopup.find('li').removeClass('hidden');
        } else {
            iconPickerPopup.find('li').addClass('hidden');

            var found = iconPickerPopup.find('li a[data-icon*="'+searchVal+'"]');
            found.parent('li').removeClass('hidden');
        }


    });

    $(document).mouseup(function (e){
        var iconPicker = $('.ct-iconpicker'),
            iconPickerPopup = $('.ct-iconpicker .ct-ip-popup');

        if ( ( ! iconPicker.is(e.target) && iconPicker.has(e.target).length === 0 ) ){
            iconPickerPopup.slideUp();
        }
    });


    //enabled/disabled change checkbox
    if ( $( '[class*="ultimate-gdpr_page_ct-ultimate-gdpr-"]' ).length ) {
        var $this = $( '[class*="ultimate-gdpr_page_ct-ultimate-gdpr-"]' );
        $this.find( '.form-table input[type="checkbox"]' ).each( function() {
            var $this = $( this );
            $this.css( 'display', 'none' );
            var $html = '<div class="ct-ultimate-gdpr-checkbox-switch"><span class="on label">'+ct_ultimate_gdpr_admin_translations.enable+'</span><span class="off label">'+ct_ultimate_gdpr_admin_translations.disable+'</span><span class="switch">\'+ct_ultimate_gdpr_admin_translations.enable+\'</span></div>';
            $( $html ).insertAfter( $this );

            if ( $this[0].hasAttribute( 'checked' )  ) {
                $this.next().find( '.switch' ).css( 'left', '0' ).text( ct_ultimate_gdpr_admin_translations.enabled );
            } else {
                $this.next().find( '.switch' ).css( 'left', '50%' ).text( ct_ultimate_gdpr_admin_translations.disabled );
            }
        } );
        $( '.ct-ultimate-gdpr-checkbox-switch .off' ).on( 'click', function () {
            var $this = $( this );
            $this.parent().prev().removeAttr( 'checked' );
            $this.next().css( 'left', '50%' ).text( ct_ultimate_gdpr_admin_translations.disabled );
        });
        $( '.ct-ultimate-gdpr-checkbox-switch .on' ).on( 'click', function () {
            var $this = $( this );
            $this.parent().prev().attr( 'checked', 'checked' );
            $this.next().next().css( 'left', '0' ).text( ct_ultimate_gdpr_admin_translations.enabled );
        });
    }

    //message notices
    // ON DOCUMENT READY
    if ( jQuery( '.ct-ultimate-gdpr-message' ).length ) {
        jQuery( '.ct-ultimate-gdpr-message' ).each( function() {
            if ( jQuery( this ).hasClass( 'warning' ) ) {
                var msgVariety = ' warning';
            } else if ( jQuery( this ).hasClass( 'caution' ) ) {
                var msgVariety = ' caution';
            } else {
                var msgVariety = '';
            }
            var $this = jQuery( this );
            var msgType = $this.find( 'th' ).text();
            var msg = $this.find( 'td' ).html();
            var jsMsg = '<div class="container-fluid"><div class="row ct-ultimate-gdpr-width ct-ultimate-gdpr-msg-clone' + msgVariety + ' ct-ultimate-gdpr-inner-wrap"><div class="col-12"><strong class="msg-type">' + msgType + '</strong><p class="msg">' + msg + '</p></div></div></div>';
            jQuery( jsMsg ).insertBefore( 'table' );
        } );
    }

    //cookie consent tabs
    $(".ct-tab-2").css("display","none");
    $(".ct-tab-3").css("display","none");

    $("#cookie-popup").click(function(){
        $(this).addClass( "nav-tab-active" );
        $("#cookie-preference").removeClass("nav-tab-active");
        $("#cookie-advanced").removeClass("nav-tab-active");

        $(".ct-tab-1").css("display","block");
        $(".ct-tab-2").css("display","none");
        $(".ct-tab-3").css("display","none");
    });
    $("#cookie-preference").click(function(){
        $(this).addClass( "nav-tab-active" );
        $("#cookie-popup").removeClass("nav-tab-active");
        $("#cookie-advanced").removeClass("nav-tab-active");

        $(".ct-tab-1").css("display","none");
        $(".ct-tab-2").css("display","block");
        $(".ct-tab-3").css("display","none");
    });
    $("#cookie-advanced").click(function(){
        $(this).addClass( "nav-tab-active" );
        $("#cookie-popup").removeClass("nav-tab-active");
        $("#cookie-preference").removeClass("nav-tab-active");

        $(".ct-tab-1").css("display","none");
        $(".ct-tab-2").css("display","none");
        $(".ct-tab-3").css("display","block");
    });

    //cookie advanced settings - fullwidth
    // MOVE FIRST 2 ROWS IN COOKIE ADVANCED SETTINGS SECTION 1 TO THEAD
    if ( $( '.ultimate-gdpr_page_ct-ultimate-gdpr-cookie' ).length ) {
        $('.ct-tab-3 .section-1').prepend('<thead></thead>');
        $('.ct-tab-3 .section-1 tbody tr:first-child()').appendTo('.ct-tab-3 .section-1 thead');
        $('.ct-tab-3 .section-1 tbody tr:first-child()').appendTo('.ct-tab-3 .section-1 thead');
    }

    //SERVICES PREVENT DEFAULT OF PLUGIN NAME LOAD
    $('.btn.btn-link').on('click', function(e){e.preventDefault();})


    // COOKIE ACCORDION FUNCTION
    if ( $( '.ct-ultimate-gdpr-wrap.ct-tab-3 .form-table.section-3 th' ).length ) {
        var accHead = $( '.ct-ultimate-gdpr-wrap.ct-tab-3 .form-table.section-3 th' );
        accHead.next().slideUp();
        accHead.on( 'click', function() {
            accHead.next().slideUp();
            accHead.removeClass( 'ct-acc-active' );
            $( this ).addClass( 'ct-acc-active' );
            if ( $( this ).next().is( ':visible' ) ) {
                $( this ).next().slideUp();
            } else {
                $( this ).next().slideDown();
            }
        } );
    }
    //accordion services
    $( ".ultimate-gdpr_page_ct-ultimate-gdpr-services #accordion .card:nth-child(2) .card-header" ).addClass('active');
    $('.btn-link').on('click', function() {
        if ($(this).hasClass('collapsed')) {
            $(this).parent().parent().addClass('active');
            $('.btn-link').not(this).parent().parent().removeClass('active');
        } else {
            $(this).parent().parent().removeClass('active');
        }
    });

    // COOKIE SCANNER MODAL
    var $firstRequest, $secondRequest, $failed;
    $('input[name=ct-ultimate-gdpr-check-cookies]').on('click', function (e) {
        var modal = $("#ct-ultimate-gdpr-cookies-scanner");
        modal.show();
        e.preventDefault();

        $firstRequest = $.post(
            ajaxurl,
            { 'action': 'ct_ultimate_gdpr_cookie_prepare_scan_cookies' },
            process_first_request
        );

        // AJAX-related functions

        function process_first_request(response){
            $secondRequest = [];
            $failed = [];
            prepare_for_second_request(response.length);
            request_second(response, 0, []);

        }

        function request_second(response, key, failed) {
            update_currently_scanning(response, key);
            $secondRequest[key] = $.post(ajaxurl, {
                    'action': 'ct_ultimate_gdpr_cookie_scan_cookies',
                    'url': response[key].url
                },
                function (successResponse) {
                    // todo maybe display added cookies
                }
            ).fail(function(response){
            }).always( function (alwaysResponse) {
                show_response_notification();
                if(alwaysResponse.success == false){
                    failed.push(response[key]);
                    $failed = failed;
                    show_failed_notification(failed.length, response[key].label);
                    update_scan_response(alwaysResponse.message, false);
                }else{
                    update_scan_response(alwaysResponse.message, true);
                }

                if ( ++key < response.length ) {
                    if( !((typeof $secondRequest[key] != 'undefined') && $secondRequest[key].aborted) ){
                        // create next request
                        request_second(response, key, failed);
                    }
                } else {
                    if(!failed.length){
                        // last request
                        $('.ct-ultimate-gdpr-cookies-scanner__Close').trigger('click');
                        window.location.replace(window.location.href + '&notice=scanner-success');
                    }
                    update_scan_ended();
                }
            });
        }

        function stop_operations(){
            try {
                $firstRequest.onreadystatechange = null;
                $firstRequest.abort();
                for( var i in $secondRequest ) {
                    $secondRequest.aborted = true;
                    $secondRequest[i].onreadystatechange = null;
                    $secondRequest[i].abort();
                }
            } catch (e) {
                console.log('Not aborted: ' + e);
            }
        }


        //DOM Manipulations

        function update_currently_scanning(urls, key){
            var labelTrimmed = urls[key].label.substring(0, 15) + '...' + urls[key].label.substring(urls[key].label.length - 15);

            $('.ct-ultimate-gdpr-cookies-scanner-content__scanned').text(key + 1);
            $('.ct-ultimate-gdpr-cookies-scanner-content__currentUrl').text(labelTrimmed);
        }

        function update_scan_response(message, success){
            $('.cookies-scanner-content__details-bottom .cookies-scanner-content__response').html(message);
            $('.cookies-scanner-content__details-bottom .cookies-scanner-content__response').removeClass('warning success');
            if(success){
                $('.cookies-scanner-content__details-bottom .cookies-scanner-content__response').addClass('success');
            }else{
                $('.cookies-scanner-content__details-bottom .cookies-scanner-content__response').addClass('warning');
            }
        }

        function update_scan_ended(){
            $('.cookies-scanner-content__response-notice-continue').addClass('hidden');
            $('.cookies-scanner-content__response-notice-ended').removeClass('hidden');
            $('.ct-ultimate-gdpr-cookies-scanner-content__show-failed').removeClass('hidden');
            $('.ct-ultimate-gdpr-cookies-scanner-content__retry').removeClass('hidden');
        }

        function prepare_for_second_request(total){
            //update top notice
            $('.ct-ultimate-gdpr-cookies-scanner-content__scanTotal').text(total);
            $('.ct-ultimate-gdpr-cookies-scanner__Pages').text(total);

            //hide failed scans in bottom section
            $('.ct-ultimate-gdpr-cookies-scanner-content__scan.failed').addClass('hidden');
            $('.ct-ultimate-gdpr-cookies-scanner-content__failed').text(0);

            //remove failed scans details
            $('.ct-ultimate-gdpr-cookies-scanner-content__scan.failed-urls').addClass('hidden')
            $('.ct-ultimate-gdpr-cookies-scanner-content__scan.failed-urls .urls ul').html('');

            //hide bottom notifications
            $('.cookies-scanner-content__response-notice-0').removeClass('hidden');
            $('.cookies-scanner-content__response-notice-1').addClass('hidden');
            $('.cookies-scanner-content__response-notice-continue').addClass('hidden');
            $('.cookies-scanner-content__response-notice-ended').addClass('hidden');
            $('.ct-ultimate-gdpr-cookies-scanner-content__show-failed').addClass('hidden');
            $('.ct-ultimate-gdpr-cookies-scanner-content__retry').addClass('hidden');
            update_scan_response('');
        }

        function show_response_notification(){
            //showing bottom notification messages
            $('.cookies-scanner-content__response-notice-0').addClass('hidden');
            $('.cookies-scanner-content__response-notice-1').removeClass('hidden');
            $('.cookies-scanner-content__response-notice-2').removeClass('hidden');
            $('.cookies-scanner-content__response-notice-continue').removeClass('hidden');
        }

        function show_failed_notification(num_failed, response_label){
            //showing failed urls
            $('.ct-ultimate-gdpr-cookies-scanner-content__scan.failed').removeClass('hidden');
            $('span.ct-ultimate-gdpr-cookies-scanner-content__failed').text(num_failed);
            $('.ct-ultimate-gdpr-cookies-scanner-content__scan.failed-urls .urls ul').append('<li><small>' + response_label + '</small></li>');

        }


        //DOM Triggers
        $(document).on('click', '.ct-ultimate-gdpr-cookies-scanner__Close', function () {
            modal.hide();
            stop_operations();
            $(this).off('click');
        });

        $(document).on('click', '.ct-ultimate-gdpr-cookies-scanner-content__show-failed', function(e){
            e.preventDefault();
            $('.ct-ultimate-gdpr-cookies-scanner-content__scan.failed-urls').toggleClass('hidden');
        });

        $(document).on('click', '.ct-ultimate-gdpr-cookies-scanner-content__retry', function(e){
            e.preventDefault();
            if(confirm($("#ct-ultimate-gdpr-cookies-scanner-content__retry-message").text())){
                stop_operations();
                process_first_request( $failed );
            }
        });

    });

    // for adminpanel tabs
    $('.ct-ultimate-gdpr-wrap a.nav-tab').on('click', function(){
        $('.ct-ultimate-gdpr-InputForTab').attr('value',($(this).attr('id')));
    });
        var $tab = $('.ct-ultimate-gdpr-InputForTab');

        if ($tab.length > 0){
            $tabValue = $tab.attr('value');
            $('#' + $tabValue).trigger('click');
        }


      // hints for admin options
      tippy('.ct-ultimate-gdpr-hint', {
         allowTitleHTML: true,
         placement: 'bottom',
         trigger: 'click',
         hideOnClick: true,
         arrow: true,
         distance: 0,
         animateFill: false,
         animation: 'fade', // 'shift-toward', 'fade', 'scale', 'perspective'
      })

});
