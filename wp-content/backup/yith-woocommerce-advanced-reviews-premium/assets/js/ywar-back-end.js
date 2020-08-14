jQuery(document).ready(function ($) {

    $("input[name='ywar-user-data']").on("change", function(e) {
        if (! $(this).is(':checked')) {
            $(".ywar-author-info input[type='text']").prop("disabled", "");
        }
        else {
            $(".ywar-author-info input[type='text']").prop("disabled", "disabled");
        }
     });

    $(document).on('click', "a.featured-review", (function (e) {
        e.preventDefault();

        var data = {
            'action': 'change_featured_status',
            'featured_status': $(this).attr('data-featured-status'),
            'review_id': $(this).attr('data-review-id')
        };

        var clicked_item = $(this);

        var parent = $(this).parent();

                parent.block({
                    message: null,
                    overlayCSS: {
                        background: "#fff url(" + ywar.loader + ") no-repeat center",
                        opacity: .6
                    }
                });

        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        $.post(ywar.ajax_url, data, function (response) {
            //  retrieve new status and set "selected" CSS class
            if (1 == response.value) {
                clicked_item.addClass("selected");
                clicked_item.attr("data-featured-status", 1);
            } else if (0 == response.value) {
                clicked_item.removeClass("selected");
                clicked_item.attr("data-featured-status", 0);
            }
            parent.unblock();
        });
    }))

    $(document).on('click', "a.reply-status", (function (e) {
        e.preventDefault();

        var data = {
            'action': 'change_reply_status',
            'stop_reply_status': $(this).attr('data-stop-reply-status'),
            'review_id': $(this).attr('data-review-id')
        };

        var clicked_item = $(this);

        var parent = $(this).parent();

        parent.block({
            message: null,
            overlayCSS: {
                background: "#fff url(" + ywar.loader + ") no-repeat center",
                opacity: .6
            }
        });

        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        $.post(ywar.ajax_url, data, function (response) {
            //  retrieve new status and set "selected" CSS class
            if (1 == response.value) {
                clicked_item.addClass("closed");
                clicked_item.attr("data-stop-reply-status", 1);
            } else if (0 == response.value) {
                clicked_item.removeClass("closed");
                clicked_item.attr("data-stop-reply-status", 0);
            }
            parent.unblock();
        });
    }))

    $(document).on('click', "a.convert-reviews", (function (e) {
        e.preventDefault();

        var data = {
            'action': 'convert_reviews'
        };

        var clicked_item = $(this);

        clicked_item.block({
            message: null,
            overlayCSS: {
                background: "#fff url(" + ywar.loader + ") no-repeat center",
                opacity: .6
            }
        });

        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        $.post(ywar.ajax_url, data, function (response) {
            $("span.converted-items").remove();
            $("div.convert-reviews").append('<div class="converted-items"><span class="converted-items">' + response.value + '</span></div>');

            clicked_item.unblock();
        });
    }))
});