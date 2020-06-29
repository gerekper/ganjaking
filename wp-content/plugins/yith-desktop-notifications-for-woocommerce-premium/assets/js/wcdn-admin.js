/**
 * admin.js
 *
 * @author Your Inspiration Themes
 * @package YITH Desktop Notifications for WooCommerce
 * @version 1.0.0
 */
 
jQuery(document).ready( function($) {
    
    $(document ).on('click', "._yith_wcdn_save_update_notification", function(event){
        $('.wcdn-add-notifications').block({message:null, overlayCSS:{background:"#fff",opacity:.6}});

        var post_data = {
            title : $('#_yith_desktop_notifications_title').val(),
            description : $('#_yith_desktop_notifications_description').val(),
            image:  $('#_yith_desktop_notifications_icon').val(),
            sound: $('#_yith_desktop_notifications_sound').val(),
            time_notification: $('#_yith_desktop_notifications_length').val(),
            //security: object.search_post_nonce,
            action: 'yith_wcdn_save_notifications',
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

    //***Active notifications***

    $('.section-head').on( 'click', function() {
        var t            = $(this);
        t.parents( '.wcdn-section' ).toggleClass( 'open' );
        t.next( '.section-body' ).slideToggle();
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

    //Type selected 2
    $(".yith_wcdn_multiple_role").select2();


    //Preview notification in Active notification
    $(document ).on('click', ".yith_wcdn_preview_notification", function(event){
        var $target = $(event.target); // this is your target button ;-)

        var theTitle = $('#_yith_desktop_notifications_title').val();
        var options = {
            body: $('#_yith_desktop_notifications_description').val(),
            icon: $('#_yith_desktop_notifications_icon').val(),
            sound: $('#_yith_desktop_notifications_sound').val(),
        }
        var n = new Notification(theTitle,options);
        n.trigger('yith-wtbe-start-animation',theTitle);
        //add audio notify
        $("<audio id='yith_wcdn_preview_notification'></audio>").attr({
            'src': $('#_yith_desktop_notifications_sound').val() ,
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


