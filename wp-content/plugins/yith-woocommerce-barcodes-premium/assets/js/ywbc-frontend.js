(function ($) {
    $(document).ready(function () {

        /** shortcode script */
        var timeout;
        var $element = null;

        $( 'form[name="yith-barcodes-form"]' ).submit(function( event ) {
            event.preventDefault();

        });
        $('input[name="yith-barcode-value"]').on('keyup', function (e) {
            e.preventDefault();

            if (timeout) {
                clearTimeout(timeout);
            }
            $element = $(this);
            timeout = setTimeout(search_barcode, 1000);
        });

        function search_barcode() {

            $('.ywbc-action-results').empty();

            var form = $element.closest('form[name="yith-barcodes-form"]');
            var barcode_value = form.find('input[name="yith-barcode-value"]').val();

            if (!barcode_value) {
                return;
            }

            var barcode_type = form.data('barcode-type');
            var data = {
                'action'         : 'barcode_actions_search_' + barcode_type,
                'text'           : form.find('input[name="yith-barcode-value"]').val(),
                'barcode-actions': form.data('barcode-actions'),
            };

            form.block({
                message   : null,
                overlayCSS: {
                    background: "#fff url(" + ywbc_data.loader + ") no-repeat center",
                    opacity   : .6
                }
            });

            $.post(ywbc_data.ajax_url, data, function (response) {
                if (1 == response.code) {
                    form.append('<div class="ywbc-action-results">' + response.items + '</div>');
                }
                else if (-1 == response.code) {
                    form.append('<div class="ywbc-action-results error">' + response.items + '</div>');
                    console.log( 'Error -> ' + response.items );
                }
                form.unblock();
            });
        }

        $(document).on('click', 'button.ywbc-action', function (e) {
            e.preventDefault();

            // $('.ywbc-action-results').empty();

            var $container = $(this).closest('.yith-barcode-actions');
            var $form = $element.closest('form[name="yith-barcodes-form"]');
            var $current_result = $(this).closest('tr.ywbc-search-row');

            var status = $(this).data('status');
            var type = $(this).data('type');
            var barcode_actions = $form.data('barcode-actions');
            var id = $(this).data('id');

            var data = {
                'action'         : 'apply_barcode_action',
                'status'         : status,
                'type'           : type,
                'barcode-actions': barcode_actions,
                'id'             : id
            };

            $container.block({
                message   : null,
                overlayCSS: {
                    background: "#fff url(" + ywbc_data.loader + ") no-repeat center",
                    opacity   : .6
                }
            });

            $.post(ywbc_data.ajax_url, data, function (response) {

                if (1 == response.code) {
                    $current_result.replaceWith(response.items);
                }

                $container.unblock();
            });

        });
        /** shortcode script end */

        /** Product page */
        var $product_barcode_img = $("#ywbc_barcode_value img.ywbc-barcode-image").length ? $("#ywbc_barcode_value img.ywbc-barcode-image").attr('src') : '';
        var $product_barcode_value = $("#ywbc_barcode_value .ywbc-barcode-display-value").length ? $("#ywbc_barcode_value .ywbc-barcode-display-value").text() : '';

        $(document).on('found_variation', 'form.variations_form', function (event, variation) {

            $variation_barcode_img = variation.barcode_img ? variation.barcode_img : $product_barcode_img;
            $variation_barcode_value = variation.barcode_img ? variation.barcode_value : $product_barcode_value;

            if ('' != $variation_barcode_img) {
                if (!$("img.ywbc-barcode-image").length) {
                    $("#ywbc_barcode_value").append('<img class="ywbc-barcode-image" src="' + $variation_barcode_img + '"><div class="ywbc-barcode-display-container"><span class="ywbc-barcode-display-value">' + $variation_barcode_value + '</span></div>');
                }
                else {
                    $("#ywbc_barcode_value img.ywbc-barcode-image").attr('src', $variation_barcode_img);
                    $("#ywbc_barcode_value .ywbc-barcode-display-value").text($variation_barcode_value);
                }
            }

        }).on('reset_image', function (event) {
            $("#ywbc_barcode_value img.ywbc-barcode-image").attr('src', $product_barcode_img);
            $("#ywbc_barcode_value .ywbc-barcode-display-value").text($product_barcode_value);
        });

        /** Product page end */
    });
})(jQuery);