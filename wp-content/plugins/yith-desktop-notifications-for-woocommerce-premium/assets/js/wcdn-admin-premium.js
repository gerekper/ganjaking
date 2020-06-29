/**
 * admin.js
 *
 * @author Your Inspiration Themes
 * @package YITH Desktop Notifications for WooCommerce
 * @version 1.0.0
 */
 
jQuery(document).ready( function($) {

    //Multivendor active delete rol vendor
    if (yith_wcdn_admin.vendor_active) {
        $("#_yith_desktop_notifications_role_user option[value='yith_vendor']").remove();
    }

    var select_notification;
    
    $('#yith-wcdn-add-section-button').on( 'click', function(e) {
        e.preventDefault();
        if ($('#yit_wcdn_options_id-container').is(':visible')){
            $('.wcdn-add-notifications').hide();
        }else {
            $('.wcdn-add-notifications').show();
            showplaceholder($('#yith-wcdn-notification-type').val());
        }
    });

    //specific status
    $("#yith-wcdn-notification-type").on('change',function(e) {


        var notification = $(this).val();

        if (notification == 'status_changed') {
            $('#yith-wcdn-id-status').show();
        } else {
            $('#yith-wcdn-id-status').hide();
        }

        if (notification == 'sold') {
            $('#yith-wcdn-id-product-sold').show();
        } else {
            $('#yith-wcdn-id-product-sold').hide();
        }

        if (yith_wcdn_admin.vendor_active) {
            if ((notification == 'placed' || notification ==  'status_changed')) {
                if ( $("#_yith_desktop_notifications_role_user option[value='yith_vendor']").length <= 0 ) {
                    $('#_yith_desktop_notifications_role_user').append('<option value="yith_vendor">Vendor</option>');
                }

            } else {
                $("#_yith_desktop_notifications_role_user option[value='yith_vendor']").remove();
                $("#_yith_desktop_notifications_role_user").trigger('change');
            }
        }

        showplaceholder(notification);

    });

    function showplaceholder(type){
        var type_notification = 'yith-wcdn-' +type;
        $('.yith-wcdn-placeholder-available li').each(function(){
            if($(this).hasClass(type_notification)) {
                $(this).show();
            }else{
                $(this).hide();
            }
        })
    }

    $('#_yith_wcdn_save_notification').on('click',function() {
        $('.wcdn-add-notifications').block({message:null, overlayCSS:{background:"#fff",opacity:.6}});

        var post_data = {
            key : $('#wcdn-key').val(),
            notification : $('#yith-wcdn-notification-type').val(),
            title : $('#_yith_desktop_notifications_title').val(),
            description : $('#_yith_desktop_notifications_description').val(),
            role_user : $('#_yith_desktop_notifications_role_user').val(),
            image:  $('#_yith_desktop_notifications_icon').val(),
            sound: $('#_yith_desktop_notifications_sound').val(),
            time_notification: $('#_yith_desktop_notifications_length').val(),
            //security: object.search_post_nonce,
            action: 'yith_wcdn_save_notifications',
            specific_status: $('#yith-wcdn-id-status').is(":visible") ? $('#yith-wcdn-specific-status').val(): undefined,
            products: $('#yith-wcdn-id-product-sold').is(":visible") ? $('#yith-wcdn-select-products').val() : undefined
        };

        $.ajax({
            type    : "POST",
            data    : post_data,
            url     : yith_wcdn_admin.ajaxurl,
            success : function ( response ) {
                $('.wcdn-add-notifications').unblock();
                window.location.reload();
                // On Success
            },
            complete: function () {
            }
        });
    });

    //New notification
    var icon = $("#_yith_desktop_notifications_icon").val();
    $("#_yith_new_notification_logo").attr("src",icon);

    $("#_yith_desktop_notifications_icon").on('change',function(e){
        var icon = $(this).val();
        $("#_yith_new_notification_logo").attr("src",icon);
    });
    $("#yith_click_new_audio_preview").on( 'click', function(e) {
        $("#yith_new_audio_preview").attr("src",$("#_yith_desktop_notifications_sound").val());
        $("#yith_new_audio_preview").get(0).play();
    });
    // preview notification in New notification
    $("#_yith_wcdn_demo_notification").on('click', function(e) {
        var theTitle = $("#_yith_desktop_notifications_title").val();
        var options = {
            body: $("#_yith_desktop_notifications_description").val(),
            icon: $("#_yith_desktop_notifications_icon").val(),
            sound: $("#_yith_desktop_notifications_sound").val()
        }
        var n = new Notification(theTitle,options);
        //add audio notify
        $("<audio id='yith_wcdn_preview_notification'></audio>").attr({
            'src': $("#_yith_desktop_notifications_sound").val(),
            'autoplay':'autoplay'
        }).appendTo("body");
        //set time to notify is show
        var time_notify = parseInt($('#_yith_desktop_notifications_length').val());
        if (time_notify > 0) {
            time_notify = time_notify * 1000;
            setTimeout(n.close.bind(n), time_notify);
        }
        //tab <blink> in

        var original_title = document.title;
        var b =  $('#yith-wcdn-notification-type').val();
        var c = $('#yith-wcdn-notification-type option:selected').text();
        var tabblink = function(){
            if(b == original_title){
                $('title').text(c);
                b = $('#yith-wcdn-notification-type').val();
            } else {
                $('title').text(original_title);
                b = original_title
            }
        }
        setInterval(tabblink,1000);
        n.onclose = function (event) {
            event.preventDefault();
            $('#yith_wcdn_preview_notification').remove();
        };
    });

    //***Active notifications***

    $('.form-notifications-rules').find('.section-head').on( 'click', function() {
        var t            = $(this);
        t.parents( '.wcdn-section' ).toggleClass( 'open' );
        t.next( '.section-body' ).slideToggle();

        $type_notification = t.next('.section-body').find('select._yith-wcdn-update-notification-type-select').val();

        showplaceholderupdate($type_notification, t.next('.section-body').find('.yith-wcdn-update-placeholder-available li'));


        if ($type_notification != 'status_changed') {
            t.next('.section-body').find('#yith-wcdn-update-specific-status').hide();
        }

        if($type_notification != 'sold') {
            t.next('.section-body').find('#yith-wcdn-id-product-sold-update').hide();
        }
        
        if (yith_wcdn_admin.vendor_active) {

            if (($type_notification == 'placed' || $type_notification == 'status_changed')) {
                if(t.next('.section-body').find("#_yith_desktop_update_notifications_role_user option[value='yith_vendor']").length <= 0) {
                    t.next('.section-body').find('#_yith_desktop_update_notifications_role_user').append('<option value="yith_vendor">Vendor</option>');
                }
            }else {
                t.next('.section-body').find("#_yith_desktop_update_notifications_role_user option[value='yith_vendor']").remove();
            }
        }

        t.next('.section-body').find('._yith-wcdn-update-notification-type-select').on('change',function(e){
            var notification = $(this).val();
            if ( notification == 'status_changed' ) {

                t.next('.section-body').find('#yith-wcdn-update-specific-status').show();

            } else {
                t.next('.section-body').find('#yith-wcdn-update-specific-status').hide();
            }

            if ( notification == 'sold' ) {
                t.next('.section-body').find('#yith-wcdn-id-product-sold-update').show();
            } else {
                t.next('.section-body').find('#yith-wcdn-id-product-sold-update').hide();
            }

            if (yith_wcdn_admin.vendor_active) {
                if (notification == 'placed' || notification ==  'status_changed') {
                    if (t.next('.section-body').find("#_yith_desktop_update_notifications_role_user option[value='yith_vendor']").length <= 0) {
                        t.next('.section-body').find('#_yith_desktop_update_notifications_role_user').append('<option value="yith_vendor">Vendor</option>');
                    }
                } else {
                    t.next('.section-body').find("#_yith_desktop_update_notifications_role_user option[value='yith_vendor']").remove();
                    t.next('.section-body').find("#_yith_desktop_update_notifications_role_user").trigger('change');
                }
            }

            showplaceholderupdate(notification, t.next('.section-body').find('.yith-wcdn-update-placeholder-available li'));
        });
    });


    $(document ).on('click', "._yith_wcdn_update_notification", function(event){

        var $target = $(event.target); // this is your target button ;-)
        var $form_pricing_rules = $target.closest('.form-notifications-rules');
        $form_pricing_rules.block({message: null, overlayCSS: {background: "#fff", opacity: .6}});
        var post_data = {
            key: $form_pricing_rules.find('.wcdn-update-key').val(),
            notification: $form_pricing_rules.find('select._yith-wcdn-update-notification-type-select').val(),
            title: $form_pricing_rules.find('._yith-desktop-update-notifications-title').val(),
            description: $form_pricing_rules.find('._yith-desktop-update-notifications-description').val(),
            role_user: $form_pricing_rules.find('select._yith-desktop-update-notifications-role-user').val(),
            image: $form_pricing_rules.find('select._yith_list_desktop_notifications_icon').val(),
            sound: $form_pricing_rules.find('select._yith_list_desktop_notifications_sound').val(),
            time_notification: $form_pricing_rules.find('._yith_desktop_update_notifications_length').val(),
            //security: object.search_post_nonce,
            action: 'yith_wcdn_update_notifications',
            specific_status: $form_pricing_rules.find('#yith-wcdn-update-specific-status').is(":visible") ? $form_pricing_rules.find('select._yith-wcdn-update-specific-status-select').val() : undefined,
            products: $form_pricing_rules.find('#yith-wcdn-id-product-sold-update').is(":visible") ? $form_pricing_rules.find('#yith-wcdn-select-products').val() : undefined,

        };

            $.ajax({
            type: "POST",
            data: post_data,
            url: yith_wcdn_admin.ajaxurl,
            success: function (response) {
                $form_pricing_rules.unblock();
                $form_pricing_rules.find('.wcdn-active').html($form_pricing_rules.find('._yith-desktop-update-notifications-title').val());
                //window.location.reload();
                // On Success
            },
            complete: function () {
            }
        });
    });

    $(document ).on('click', "._yith_wcdn_delete_notification", function(event){
        var $target = $(event.target);
        var $form_pricing_rules = $target.closest('.form-notifications-rules');
        $form_pricing_rules.block({message:null, overlayCSS:{background:"#fff",opacity:.6}});
        var post_data = {
            key : $form_pricing_rules.find('.wcdn-update-key').val(),
            action: 'yith_wcdn_delete_notifications'
        };

        $.ajax({
            type    : "POST",
            data    : post_data,
            url     : yith_wcdn_admin.ajaxurl,
            success : function ( response ) {
                $form_pricing_rules.unblock();
                $form_pricing_rules.hide();
                //On Success
            },
            complete: function () {
            }
        });
    });
    //Preview audio in upload tab
    $(".yith_click_audio_preview").on( 'click', function(e) {
        $(this).next().get(0).play();
    });




    //List of notifications actives
        //Img
     $("._yith_list_notification_logo").each(function(index){
         var img_list = $(this).closest('.form-notifications-rules').find('._yith_list_desktop_notifications_icon').val();
         $(this).attr('src',img_list);
     });

    $("._yith_list_desktop_notifications_icon").on('change',function(e) {
       var icon = $(this).val();
        $(this).closest('.form-notifications-rules').find('._yith_list_notification_logo').attr('src',icon);
    });
        //Audio
    $(document ).on('click', ".yith_list_click_audio_preview", function(event){

        var $target = $(event.target); // this is your target button ;-)
        var $form = $target.closest('.form-notifications-rules');
        var sound = $form.find('select._yith_list_desktop_notifications_sound').val();
        $form.find('.yith_list_audio_preview').attr("src",sound);
        $form.find('.yith_list_audio_preview').get(0).play();
    });


    //Type selected 2
    $(".yith_wcdn_multiple_role").select2();


    function showplaceholderupdate(type,list){
        var type_notification = 'yith-wcdn-' +type;
        $(list).each(function() {
            if($(this).hasClass(type_notification)) {
                $(this).show();
            }else{
                $(this).hide();
            }
        })
    }

    //Preview notification in Active notification
    $(document ).on('click', ".yith_wcdn_preview_notification", function(event){
        var $target = $(event.target); // this is your target button ;-)
        var $form_pricing_rules = $target.closest('.form-notifications-rules');

        var theTitle = $form_pricing_rules.find('._yith-desktop-update-notifications-title').val();
        var options = {
            body: $form_pricing_rules.find('._yith-desktop-update-notifications-description').val(),
            icon: $form_pricing_rules.find('select._yith_list_desktop_notifications_icon').val(),
            sound: $form_pricing_rules.find('select._yith_list_desktop_notifications_sound').val(),
        }
        var n = new Notification(theTitle,options);
        //add audio notify
        $("<audio id='yith_wcdn_preview_notification'></audio>").attr({
            'src': $form_pricing_rules.find('select._yith_list_desktop_notifications_sound').val(),
            'autoplay':'autoplay'
        }).appendTo("body");
        //set time to notify is show
        var time_notify = parseInt($('#_yith_desktop_notifications_length').val());
        if (time_notify > 0) {
            time_notify = time_notify * 1000;
            setTimeout(n.close.bind(n), time_notify);
        }
        n.onclose = function (event) {
            event.preventDefault();
            $('#yith_wcdn_preview_notification').remove();
        };

    });

});


