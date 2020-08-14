/**
 * Created by Your Inspiration on 05/04/2016.
 */
jQuery(document).ready(function ($) {
    if( ywf_admin.is_customize_active ){

        var table = $('#yith_funds_panel_endpoints-settings .yit-admin-panel-content-wrap');

        table.css({
            'pointer-events': 'none',
            'opacity': '0.3'
        });
    }

    var radio_field = $(document).find('#ywf_redeeming_gateway');

    if( radio_field.length ) {
      var  disabled_radio_value = radio_field.data('disabled'),
           disabled_radio_value = disabled_radio_value.split(',');

        if (!radio_field.hasClass('yith-disabled')) {
            $.each(disabled_radio_value, function (index, value) {

                var field = $('#ywf_redeeming_gateway-' + value);

                if (field.length) {

                    field.parent().addClass('yith-disabled');
                }
            });
        }
    }

    var redeem_email_type = $(document).find('#ywf_redeem_email_type');

    if( redeem_email_type.length && redeem_email_type.hasClass('yith-disabled') ){

        redeem_email_type.parent().find('span.select2').addClass('yith-disabled');
    }

    var redeem_option = $(document).find('#ywf_vendor_can_redeem');

    if( redeem_option.length ){

        redeem_option.on('change',function (e) {
            if( redeem_option.is(':checked')){
                redeem_option.parents('tr').nextAll('tr.yith-plugin-fw-panel-wc-row').removeClass('yith-disabled');

            }else{
                redeem_option.parents('tr').nextAll('tr.yith-plugin-fw-panel-wc-row').addClass('yith-disabled');
            }
        }).trigger('change');
    }

});