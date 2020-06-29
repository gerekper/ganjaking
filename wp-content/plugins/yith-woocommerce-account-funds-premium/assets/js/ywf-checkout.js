jQuery(document).ready(function ($) {

    var current = $('input[name="payment_method"]:checked');

    $('#order_review').on('change', 'input[name="payment_method"]', function () {

        var current_radio = $(this),
            current_radio_id = current_radio.attr('id'),
            old_radio_id = current.attr('id');

        if (( old_radio_id != 'payment_method_yith_funds' && current_radio_id == 'payment_method_yith_funds' ) || (old_radio_id == 'payment_method_yith_funds' && current_radio_id != 'payment_method_yith_funds' )) {
            current = current_radio;
            $('body').trigger('update_checkout');

        }

    });
});
