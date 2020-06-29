jQuery(function ($) {

    // Show dynamic region description.
    $(document).on('change', '#woocommerce_fba_ns_fba_service_url', function () {
        var amazon_region_url = $(this).val();
        if (amazon_region_url) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                dateType: 'json',
                cache: false,
                data: {
                    action: 'ns_fba_update_amazon_region_description',
                    amazon_region_url: amazon_region_url
                },
                success: function (result) {
                    var region_desc_table = $('.amazon-region-desc');
                    region_desc_table.find("tr:not(.static-desc-text)").remove();
                    region_desc_table.append(result.data);
                }
            });
        }
    });

    // AJAX action buttons in settings.
    $('[name=ns_fba_test_api], [name=ns_fba_test_inventory], [name=ns_fba_sync_inventory_manually], [name=ns_fba_clean_logs_now]').click(function(e){
        var button = $(this);
        button.nextAll('.ns-fba-success-label, .ns-fba-error-label').remove();
        button.after('<div class="spinner is-active"/>');
        $.post(
            ajaxurl,
            {
                action  : $(this).attr('name'),
                nonce   : ns_fba.nonce,
                options : $('#mainform').serialize(),
            },
            function (response){
                button.next('.spinner').remove();
                var message_class = response.success ? 'ns-fba-success-label' : 'ns-fba-error-label';
                var message_text  = response.data ? response.data : 'Error, no response received.';
                button.after('<span class="' + message_class + '">' + message_text + ' <u>Dismiss</u></span>');
            }
        );
        e.preventDefault();
    });

    // Dismiss ajax button messages.
    $(document).on('click', '.ns-fba-success-label u, .ns-fba-error-label u', function(){
       $(this).parent().fadeOut();
    });

});