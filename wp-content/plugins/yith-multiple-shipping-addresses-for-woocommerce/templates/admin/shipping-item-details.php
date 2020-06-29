<?php
/**
 * Admin View: Exclusion Table Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$shipping_address = yith_wcmas_shipping_address_from_destination_array( $destination );
$is_local_pickup = 'local_pickup' == $item->get_method_id();

?>
    <div class="view">
		<?php if ( ! $is_local_pickup ) : ?>
            <div class="ywcmas_shipping_address">
                <a class="ywcmas_edit_shipping_address_button" data-shipping_id="<?php echo $item_id; ?>"
                   title="<?php esc_html_e( 'Edit this shipping address', 'yith-multiple-shipping-addresses-for-woocommerce' ); ?>"></a>
                <strong><?php printf( esc_html__( 'Shipping address: %s', 'yith-multiple-shipping-addresses-for-woocommerce' ), $shipping_address ); ?></strong>
            </div>
		<?php endif; ?>
        <div class="ywcmas_shipping_status">
            <select class="ywcmas_shipping_status_select" name="ywcmas_shipping_status[<?php echo $item_id; ?>]" style="width: 150px;">
				<?php yith_wcmas_print_shipping_statuses_select_options( $shipping_status, $is_local_pickup ); ?>
            </select>
        </div>
    </div>
<?php if ( ! $is_local_pickup ) : ?>
    <div class="ywcmas_edit_shipping_item_dialog" title="<?php esc_html_e( 'Edit address', 'yith-multiple-shipping-addresses-for-woocommerce' ); ?>"
         style="display: none;" data-shipping_id="<?php echo $item_id; ?>">
		<?php $fields = WC()->countries->get_address_fields( $destination['country'], 'shipping_' ); ?>
		<?php $customer_id = $item->get_order()->get_customer_id(); ?>
        <div class="edit_address">
            <table class="ywcmas_edit_shipping_item_fields_table">
                <tbody>
                <tr>
                    <td>
                        <span><?php esc_html_e( 'Load user address', 'yith-multiple-shipping-addresses-for-woocommerce' ); ?></span>
                    </td>
                    <td>
                        <select class="ywcmas_edit_shipping_item_addresses_select" style="width: 200px;"><?php yith_wcmas_print_addresses_select_options( '', $customer_id ); ?></select>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td style="padding-bottom: 20px;">
                    <span>
                        <a class="ywcmas_edit_shipping_item_load_button" href="#"><?php esc_html_e( 'Load', 'yith-multiple-shipping-addresses-for-woocommerce' ); ?></a>
                    </span>
                    </td>
                </tr>
				<?php foreach ( $fields as $field_id => $field ) : ?>
					<?php $field_name = 'shipping_address_1' == $field_id ? 'address' : substr( $field_id, 9 ); ?>
					<?php $value = ! empty( $destination[$field_name] ) ? $destination[$field_name] : ''; ?>
                    <tr>
						<?php if ( 'shipping_state' == $field_id ) : ?>
                            <td><label><?php esc_html_e( 'State / County', 'yith-multiple-shipping-addresses-for-woocommerce' ); ?></label></td>
                            <td>
                                <input type="text" class="js_field-state select short"
                                       id="state_for_item_<?php echo $item_id; ?>"
                                       name="state_for_item_<?php echo $item_id; ?>"
                                       value="<?php echo $value; ?>" placeholder="">
                            </td>
						<?php elseif ( 'shipping_country' == $field_id ) : ?>
                            <td><label><?php esc_html_e( 'Country', 'yith-multiple-shipping-addresses-for-woocommerce' ); ?></label></td>
                            <td>
                                <select class="ywcmas_destination_shipping_country js_field-country select short">
									<?php
									$options = array( '' => esc_html__( 'Select a country&hellip;', 'yith-multiple-shipping-addresses-for-woocommerce' ) ) + WC()->countries->get_allowed_countries();
									foreach ( $options as $country_key => $country_value ) {
										echo '<option value="' . esc_attr( $country_key ) . '" ' . selected( esc_attr( $value ), esc_attr( $country_key ), false ) . '>' . esc_html( $country_value ) . '</option>';
									}
									?>
                                </select>
                            </td>
						<?php else : ?>
                            <td><label><?php echo ! empty( $field['label'] ) ? $field['label'] : $field['placeholder']; ?></label></td>
                            <td><input class="ywcmas_destination_<?php echo $field_id; ?>" type="text" value="<?php echo $value; ?>"></td>
						<?php endif; ?>
                    </tr>
				<?php endforeach; ?>
                <tr>
                    <td colspan="2" style="padding-top: 20px;">
                        <a class="ywcmas_edit_shipping_item_revert_button" href="#"><?php esc_html_e( 'Revert changes', 'yith-multiple-shipping-addresses-for-woocommerce' ); ?></a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>