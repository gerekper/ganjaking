<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


$value = get_option( $option['id'] );
?>
    <tr valign="top">
        <th scope="row" class="titledesc"><label for="ywcwat_add_new"><?php echo $option['name']; ?></label></th>
        <td class="frominp forminp-button">
            <input type="button" class="button button-secondary" id="<?php echo esc_attr( $option['id'] ); ?>"
                   value="<?php _e( 'Add Watermark', 'yith-woocommerce-watermark' ); ?>">
        </td>
    </tr>

    <script type="text/javascript">
        jQuery(document).ready(function ($) {

            var create_uniqueId = function () {

                var string_id = '';
                do {

                    string_id = 'id-' + Math.random().toString(36).substr(2, 16);
                    var field = $('input:hidden[id^="ywcwat_id"][value="' + string_id + '"]');

                } while (field.size() > 0);

                return string_id;
            };

            $(document).on('click', '#<?php echo $option['id'];?>', function () {

                var size = $('table.ywcwat_row').size(),
                    list_section = $('.ywcwat_listsection').parent();

                var data = {
                    ywcwat_addnewwat: size,
                    ywcwat_unique_id: create_uniqueId(),
                    action: 'add_new_watermark_admin'
                };
                $.ajax({

                    type: 'POST',
                    url: ywcwat_params.ajax_url,
                    data: data,
                    dataType: 'json',
                    success: function (response) {

                        if (size > 0) {
                            var last_table = $('table.ywcwat_row:last');

                            last_table.after(response.result);
                        } else
                            $(response.result).insertAfter( list_section.find('table') );


                        $('body').trigger('ywcwat-init-admin-fields');
                        $(document.body).trigger('yith-framework-enhanced-select-init');
						$(document.body).trigger('wc-enhanced-select-init');

                    }

                });

            });
        });
    </script>
<?php
