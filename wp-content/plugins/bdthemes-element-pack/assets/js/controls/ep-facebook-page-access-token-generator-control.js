(function ($) {
    $(document).ready(function () {

        $(document).on('click', 'input.ep-facebook-page-access-token-generator-button', function (e) {
            e.preventDefault();
            var errorSelector = $(this).parent().find('p.ep-error-notice');
            var access_tokenSelector = $(this).parent().find('input.ep-facebook-page-access-token-field');
                errorSelector.text('');

            var page_scope = $(this).data('permisson');
            var pageIdField = $(this).data('page_id_field');
            var pageId = $(this).parents('#elementor-controls').find('input[data-setting='+pageIdField+']').val();

            if(!pageId){
                errorSelector.text('Facebook Page ID is missing!');
                return false;
            }

            FB.login(function (response) {
                if (response.authResponse) {
                    // Get and display the user profile data.
                    FB.api('/'+pageId, {fields: 'access_token'},
                        function (response) {
                            if (response && !response.error) {
                                var access_token = response.access_token;
                                access_tokenSelector.val(access_token);
                                access_tokenSelector.trigger('input');
                            }
                        }
                    );
                } else {
                    errorSelector.text('Fail: Please try again!');
                }
            }, {scope: page_scope});
        });
    });
})(jQuery);