(function ($) {
    $(window).on('load', function () {
        $('#mailoptin-send-mail').on('click', function (e) {
            e.preventDefault();
            $('#mailoptin-spinner').fadeIn();
            $.post(
                ajaxurl,
                {
                    action: 'mailoptin_send_test_email',
                    email_campaign_id: mailoptin_email_campaign_id,
                    security: $('#mailoptin-send-test-email-nonce').val()
                },
                function () {
                    $('#mailoptin-spinner').fadeOut();
                    $('#mailoptin-success').fadeIn().delay(3000).fadeOut();
                }, "json");
        });
    });
})(jQuery);