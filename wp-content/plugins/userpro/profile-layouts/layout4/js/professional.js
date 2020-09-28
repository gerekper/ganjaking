(function ($) {

    var follow = $('.up-ajax-btn');

    follow.on('click', function () {
        // clicked button
        var up_btn = $(this);

        up_btn.addClass('loading');

        var action = $(this).data('profile-action');
        var current_user_id = $(this).parent().data('up-from-id');
        var profile_user_id = $(this).parent().data('up-user-id');

        var data = {};

        switch (action) {
            case 'follow':
                data.to = profile_user_id;
                data.action = 'userpro_followAction';
                data.follow_action = 'follow';
                data.security = up_social_ajax.security;
                break;

            case 'unfollow':
                data.to = profile_user_id;
                data.action = 'userpro_followAction';
                data.follow_action = 'unfollow';
                data.security = up_social_ajax.security;

                break;

            case 'connect' :
                data.user_id = profile_user_id;
                data.action = 'userpro_connect_user';

                break;

        }


        $.ajax({
            url: userpro_ajax_url,
            data: data,
            dataType: 'JSON',
            type: 'POST',
            success: function (data) {
                up_btn.removeClass('loading');

                switch (data.data.action) {
                    case 'follow':

                        up_changeValue(up_btn, data);

                        break;

                    case 'unfollow':

                        up_changeValue(up_btn, data);

                        break;

                    case 'connect':

                        upNotification(data.data.modal_msg, 'success');
                        up_btn.fadeOut();
                        break;
                }

            },
            error: function () {
                up_btn.removeClass('loading');
                upNotification('Something went wrong, sorry.', 'error');
            }
        });
    });

    // Change following button text,icon and counter.
    function up_changeValue(selector, data) {
        // Followers counter
        var followers = $('.up-followers');
        selector.data('profile-action', data.data.action);
        selector.find('p').text(data.data.text);
        selector.find('i').removeClass().addClass(data.data.icon);
        followers.find('span').text(data.data.count);
    }


    function upNotification(message, status) {

        var html = '';
        var notification = jQuery('.up-notification');
        var holder = jQuery('body');

        notification.remove();

        html += "<div class=\"up-notification up-notification--" + status + "\">\n" +
            "<div class=\"up-notification__icon\">\n";

        switch (status) {

            case 'success':

                html += '<i class=\"fas fa-check\"></i>';

                break;

            case 'error':

                html += '<i class=\"fas fa-times\"></i>';

                break;

            case 'warning':

                html += '<i class=\"fas fa-exclamation\"></i>';

                break;

        }

        html += "</div>\n" +
            "<div class=\"up-notification__body\">\n" +
            "<p> " + message + "</p>\n" +
            "</div>\n" +
            "<button class=\"up-notification__close\"><i class=\"fa fa-times\"></i></button>\n" +
            "</div>";


        jQuery(document).ready(function () {

            holder.append(html);

            setTimeout(function () {
                jQuery('.up-notification').addClass('up-notification__slide-out');
            }, 4000);

        });

    }

    $('.up-pagination li').on('click', function () {
        $('.up-pagination li.active').removeClass('active');
        $(this).addClass('active');
        $('.up-posts').addClass('loading');

        var data = {
            'action': 'userpro_get_user_posts',
            'page': $(this).data('page'),
            'user_id': $(this).parent().data('user-id')
        };
        $.ajax({
            url: userpro_ajax_url,
            data: data,
            dataType: 'JSON',
            type: 'POST',
            success: function (data) {
                $('.up-posts-container').html(data.data);
                $('.up-posts').removeClass('loading');
            },
            error: function (request, status, error) {
                $('.up-posts').removeClass('loading');
                upNotification(request.responseText, 'error');
            }
        });

    });

    // Professional layout tabs switcher


    $('.up-tab-container').css('height', $('.up-profile-information--visible').outerHeight());

    $('.up-profile-nav li').on('click', function (e) {

        e.preventDefault();

        var tab_id = $(this).find('a').attr('href');

        $( this ).parent().find( 'li.active' ).removeClass( 'active' );
        $( this ).addClass( 'active' );


        // Get Container height
        var containerHeight =  $(tab_id).outerHeight();

        // Slide out container

        $('.up-tab-container').css('height', containerHeight);

        $('.up-profile-information').removeClass('up-profile-information--visible');
        $(tab_id).addClass('up-profile-information--visible');


    });

})(jQuery);