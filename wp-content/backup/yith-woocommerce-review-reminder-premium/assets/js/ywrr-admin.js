jQuery(function ($) {

    $('body')
        .on('click', '.ywrr-send-test-email', function () {

            var container = $(this).parent(),
                email = $('#ywrr_email_test').attr('value'),
                template = $('#ywrr_mail_template').val() || 'base',
                re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

            container
                .find('.ywrr-ajax-result')
                .remove();

            container.append('<div class="ywrr-ajax-result"></div>');

            if (!re.test(email)) {

                container
                    .find('.ywrr-ajax-result')
                    .addClass('fail')
                    .html(ywrr_admin.mail_wrong);

            } else {

                var data = {
                    action  : 'ywrr_send_test_mail',
                    email   : email,
                    template: template
                };

                container
                    .find('.ywrr-ajax-result')
                    .addClass('progress')
                    .html(ywrr_admin.before_send_test_email);

                $.post(ywrr_admin.ajax_url, data, function (response) {

                    container
                        .find('.ywrr-ajax-result')
                        .removeClass('progress')
                        .addClass(response.success === true ? 'success' : 'fail')
                        .html(response.message);

                });

            }

        })
        .on('click', '.ywrr-add-blocklist', function () {
            var container = $(this).parent(),
                email = $('#add_to_blocklist'),
                re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

            container
                .find('.ywrr-ajax-result')
                .remove();

            container.append('<div class="ywrr-ajax-result"></div>');

            if (!re.test(email.attr('value'))) {

                container
                    .find('.ywrr-ajax-result')
                    .addClass('fail')
                    .html(ywrr_admin.mail_wrong);

            } else {

                var data = {
                    action: 'ywrr_add_to_blocklist',
                    email : email.attr('value')
                };

                container
                    .find('.ywrr-ajax-result')
                    .addClass('progress')
                    .html(ywrr_admin.please_wait);

                $.post(ywrr_admin.ajax_url, data, function (response) {

                    container
                        .find('.ywrr-ajax-result')
                        .removeClass('progress')
                        .addClass(response.success === true ? 'success' : 'fail')
                        .html(response.message);

                    if (response.success === true) {
                        email.attr('value', '');
                        $.post(document.location.href, function (data) {
                            if (data !== '') {
                                var temp_content = $("<div></div>").html(data),
                                    content = temp_content.find('#custom-table');
                                $('#custom-table').html(content.html());
                            }
                        });
                    }

                });

            }
        });

});

