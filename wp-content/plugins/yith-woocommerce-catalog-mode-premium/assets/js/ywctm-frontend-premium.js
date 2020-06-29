jQuery(function ($) {

    $(document).ready(function () {
        "use strict";

        var id_field = '<input type="hidden" name="ywctm-product-id" value="' + ywctm.product_id + '" />';

        switch (ywctm.form_type) {

            case 'contact-form-7':
                $('div.wpcf7 > form').append(id_field);
                break;

            case 'gravity-forms':
                $('.gform_wrapper > form > .gform_footer').append(id_field);
                break;

        }

        set_variation_inquiry();

    });

    $(document).on('woocommerce_variation_has_changed', function () {

        set_variation_inquiry();

    });

    function set_variation_inquiry() {

        var variation_id = parseInt($('.single_variation_wrap .variation_id, .single_variation_wrap input[name="variation_id"], .woocommerce-variation-add-to-cart input[name="variation_id"]').val());

        if (!isNaN(variation_id) && variation_id !== 0) {

            $('input[name="ywctm-product-id"]').val(variation_id);

        } else {

            $('input[name="ywctm-product-id"]').val(ywctm.product_id);

        }

    }

});