jQuery(function ($) {

    $(document).ready(function ($) {

        /**
         * Product edit page
         */
        function lock_unlock_product() {
            var variations_override = $('#_ywmmq_product_quantity_limit_variations_override'),
                product_override_enabled = $('#_ywmmq_product_quantity_limit_override').is(':checked'),
                variations_override_enabled = (variations_override.length > 0 ? variations_override.is(':checked') : false);

            if (product_override_enabled) {

                variations_override.removeAttr('disabled');

                if (!variations_override_enabled) {

                    $('#_ywmmq_product_minimum_quantity').removeAttr('disabled');
                    $('#_ywmmq_product_maximum_quantity').removeAttr('disabled');
                    $('#_ywmmq_product_step_quantity').removeAttr('disabled');

                }

            } else {

                variations_override.attr('disabled', 'disabled');

                $('#_ywmmq_product_minimum_quantity').attr('disabled', 'disabled');
                $('#_ywmmq_product_maximum_quantity').attr('disabled', 'disabled');
                $('#_ywmmq_product_step_quantity').attr('disabled', 'disabled');

            }
        }

        $('#_ywmmq_product_exclusion').change(function () {

            if ($(this).is(':checked')) {

                $('#_ywmmq_product_quantity_limit_override').attr('disabled', 'disabled');
                $('#_ywmmq_product_minimum_quantity').attr('disabled', 'disabled');
                $('#_ywmmq_product_maximum_quantity').attr('disabled', 'disabled');
                $('#_ywmmq_product_step_quantity').attr('disabled', 'disabled');
                $('#_ywmmq_product_quantity_limit_variations_override').attr('disabled', 'disabled');

            } else {

                $('#_ywmmq_product_quantity_limit_override').removeAttr('disabled');
                lock_unlock_product();

            }

        }).change();

        $('#_ywmmq_product_quantity_limit_override').change(function () {

            lock_unlock_product();

        }).change();

        $('#_ywmmq_product_quantity_limit_variations_override').change(function () {

            if ($('#_ywmmq_product_quantity_limit_override').is(':checked')) {

                if ($(this).is(':checked')) {

                    $('#_ywmmq_product_minimum_quantity').attr('disabled', 'disabled');
                    $('#_ywmmq_product_maximum_quantity').attr('disabled', 'disabled');
                    $('#_ywmmq_product_step_quantity').attr('disabled', 'disabled');
                    $('.ywmmq-variation-field').each(function () {

                        $(this).removeAttr('disabled');

                    });
                } else {

                    $('#_ywmmq_product_minimum_quantity').removeAttr('disabled');
                    $('#_ywmmq_product_maximum_quantity').removeAttr('disabled');
                    $('#_ywmmq_product_step_quantity').removeAttr('disabled');
                    $('.ywmmq-variation-field').each(function () {

                        $(this).attr('disabled', 'disabled');

                    });
                }

            }

        }).change();

        /**
         * Category edit page
         */
        function lock_unlock_taxonomy(override_check, taxonomy, type) {

            var minimum_value = $('#_ywmmq_' + taxonomy + '_minimum_' + type),
                maximum_value = $('#_ywmmq_' + taxonomy + '_maximum_' + type),
                step_value = $('#_ywmmq_' + taxonomy + '_step_' + type);

            if (override_check.is(':checked')) {

                minimum_value.removeAttr('disabled');
                maximum_value.removeAttr('disabled');
                step_value.removeAttr('disabled');

            } else {

                minimum_value.attr('disabled', 'disabled');
                maximum_value.attr('disabled', 'disabled');
                step_value.attr('disabled', 'disabled');

            }

        }

        $('#_ywmmq_category_exclusion, #_ywmmq_tag_exclusion').change(function () {

            var taxonomy = $(this).attr('id').replace('_ywmmq_', '').replace('_exclusion', ''),
                quantity_override = $('#_ywmmq_' + taxonomy + '_quantity_limit_override'),
                value_override = $('#_ywmmq_' + taxonomy + '_value_limit_override');


            if ($(this).is(':checked')) {
                quantity_override.attr('disabled', 'disabled');
                $('#_ywmmq_' + taxonomy + '_minimum_quantity').attr('disabled', 'disabled');
                $('#_ywmmq_' + taxonomy + '_maximum_quantity').attr('disabled', 'disabled');
                $('#_ywmmq_' + taxonomy + '_step_quantity').attr('disabled', 'disabled');

                value_override.attr('disabled', 'disabled');
                $('#_ywmmq_' + taxonomy + '_minimum_value').attr('disabled', 'disabled');
                $('#_ywmmq_' + taxonomy + '_maximum_value').attr('disabled', 'disabled');

            } else {

                quantity_override.removeAttr('disabled');
                lock_unlock_taxonomy(quantity_override, taxonomy, 'quantity');

                value_override.removeAttr('disabled');
                lock_unlock_taxonomy(value_override, taxonomy, 'value');

            }

        }).change();

        $('#_ywmmq_category_quantity_limit_override, #_ywmmq_tag_quantity_limit_override').change(function () {

            var taxonomy = $(this).attr('id').replace('_ywmmq_', '').replace('_quantity_limit_override', '');

            lock_unlock_taxonomy($(this), taxonomy, 'quantity')

        }).change();

        $('#_ywmmq_category_value_limit_override, #_ywmmq_tag_value_limit_override').change(function () {

            var taxonomy = $(this).attr('id').replace('_ywmmq_', '').replace('_value_limit_override', '');

            lock_unlock_taxonomy($(this), taxonomy, 'value')

        }).change();

    });

    $('#woocommerce-product-data').on('woocommerce_variations_loaded', function () {

        $('.ywmmq-variation-field').each(function () {

            if ($('#_ywmmq_product_quantity_limit_override').is(':checked') && $('#_ywmmq_product_quantity_limit_variations_override').is(':checked')) {

                $(this).removeAttr('disabled');

            } else {

                $(this).attr('disabled', 'disabled');

            }

        });

    })

});

