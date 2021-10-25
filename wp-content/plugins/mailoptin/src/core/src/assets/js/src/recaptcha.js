import $ from 'jquery';

export default function () {
    window.moFormRecaptchaLoadCallback = function () {
        $('.mo-g-recaptcha').each(function (index, el) {
            var $site_key = $(el).attr('data-sitekey');
            if ($(el).attr('data-type') === 'v3') {
                var $form = $(this).parents('form.mo-optin-form');

                $form.find('input.mo-optin-form-submit-button').on('click', function (e) {
                    e.preventDefault();
                    grecaptcha.ready(function () {
                        grecaptcha.execute($site_key, {action: 'form'}).then(function (token) {
                            $form.find('[name="g-recaptcha-response"]').remove();

                            $form.append($('<input>', {
                                type: 'hidden',
                                value: token,
                                name: 'g-recaptcha-response'
                            }));

                            $form.submit();
                        });
                    });
                });
            } else {
                grecaptcha.render(el, {
                    'sitekey': $site_key,
                    'theme': $(el).attr('data-theme'),
                    'size': $(el).attr('data-size')
                });
            }
        });
    }
}