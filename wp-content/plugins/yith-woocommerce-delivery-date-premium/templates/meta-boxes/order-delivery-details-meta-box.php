<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

$order_id = $post->ID;

$order           = wc_get_order( $order_id );
$carrier_label   = $order->get_meta( 'ywcdd_order_carrier' );
$shipping_date   = $order->get_meta( 'ywcdd_order_shipping_date' );
$delivery_date   = $order->get_meta( 'ywcdd_order_delivery_date' );
$time_from       = $order->get_meta( 'ywcdd_order_slot_from' );
$time_to         = $order->get_meta( 'ywcdd_order_slot_to' );
$date_format     = get_option( 'date_format' );
$carrier_id      = $order->get_meta( 'ywcdd_order_carrier_id' );
$method_id       = $order->get_meta( 'ywcdd_order_processing_method' );
$order_has_child = apply_filters( 'yith_delivery_date_order_has_child', false, $order_id );
$disable_option  = $order_has_child ? 'disabled' : '';
$fields          = array(
	'carrier'       => array(
		'label' => __( 'Carrier', 'yith-woocommerce-delivery-date' ),
		'value' => $carrier_label
	),
	'shipping_date' => array(
		'label' => __( 'Shipping Date', 'yith-woocommerce-delivery-date' ),
		'value' => $shipping_date
	),
	'delivery_date' => array(
		'label' => __( 'Delivery Date', 'yith-woocommerce-delivery-date' ),
		'value' => $delivery_date,
	),
	'timeslot'      => array(
		'label' => __( 'Time Slot', 'yith-woocommerce-delivery-date' ),
		'value' => ( empty( $time_from ) || empty( $time_to ) ) ? '' : sprintf( '%s - %s', ywcdd_display_timeslot( $time_from ), ywcdd_display_timeslot( $time_to ) )
	)
);

$order_shipped      = get_post_meta( $order_id, 'ywcdd_order_shipped', true );
$order_shipped      = empty( $order_shipped ) ? 'no' : $order_shipped;
$no_available_label = __( 'Not Available', 'yith-woocommerce-delivery-date' );

$disable_tooltip = '';

$send_email = get_post_meta( $order_id, '_ywcdd_not_send', true );

if ( $send_email == 'yes' ) {


	$disable_tooltip = sprintf( '<span class="woocommerce-help-tip" data-tip="%s"></span>', __( 'No email will be sent since customer denied the consent during the checkout', 'yith-woocommerce-delivery-date' ) );
}

$option = get_option( 'ywcdd_processing_type', 'checkout' );
if ( ( 'product' == $option && ! empty( $shipping_date ) ) || 'checkout' == $option ) {


	?>
    <div id="ywcdd_delivery_order_metabox">
        <h4>
			<?php _e( 'Processing and Delivery date', 'yith-woocommerce-delivery-date' ); ?>
			<?php if ( apply_filters( 'ywcdd_edit_order_date_details', !in_array( $order->get_status(), array( 'processing', 'completed', 'refunded' ) ), $order ) ) { ?>
                <a href="#" class="edit_address"><?php _e( 'Edit', 'yith-woocommerce-delivery-date' ); ?></a>
				<?php
			}
			?>
        </h4>
        <div id="processing_delivery_date_container">
            <div class="processing_delivery_date">
				<?php foreach ( $fields as $key => $field ) {

					$formatted_value = empty( $field['value'] ) ? false : $field['value'];


					if ( ( 'shipping_date' === $key || 'delivery_date' === $key ) && $formatted_value ) {

						$formatted_value = wc_format_datetime( new WC_DateTime( $formatted_value, new DateTimeZone( 'UTC' ) ) );

					}

					if ( 'shipping_date' === $key ) {

						if ( $formatted_value ) {
							$timezone_format  = 'Y-m-d H:i:s';
							$now              = strtotime( date_i18n( $timezone_format ) );
							$now              = strtotime( 'midnight', $now );
							$to               = strtotime( $formatted_value );
							$days             = intval( ( $to - $now ) / DAY_IN_SECONDS );
							$shipping_message = '';
							$color_class      = '';
							$message          = '';


							if ( 'no' === $order_shipped ) {

								if ( $days >= 0 ) {

									$color_class = 'ywcdd_advise';
									$message     = __( 'Please, ship this order to the carrier within this date', 'yith-woocommerce-delivery-date' );
								} else {
									$color_class = 'ywcdd_error';
									$message     = __( 'You haven\'t shipped the order in time!', 'yith-woocommerce-delivery-date' );
								}


							} else {
								$color_class = 'ywcdd_shipped';
								$message     = __( 'Shipped to carrier', 'yith-woocommerce-delivery-date' );
							}

							$shipping_message = sprintf( '<span class="woocommerce-help-tip ywcdd-icon-warning %s" data-tip="%s"></span>', $color_class, $message );

							echo sprintf( '<p class="%s"><strong>%s:</strong>%s<br/>%s</p>', $key, $field['label'], $shipping_message, $formatted_value );
						} else {
							echo sprintf( '<p class="%s"><strong>%s:</strong>%s<br/></p>', $key, $field['label'], $no_available_label );
						}
					} else {

						if ( $formatted_value ) {
							echo sprintf( '<p class="%s"><strong>%s:</strong><br/>%s</p>', $key, $field['label'], $formatted_value );
						} else {
							echo sprintf( '<p class="%s"><strong>%s:</strong><br/>%s</p>', $key, $field['label'], $no_available_label );
						}
					}
				}
				?>
                <p>
                    <label
                            for="ywcdd_order_shipped"><strong><?php _e( 'Shipped to carrier', 'yith-woocommerce-delivery-date' ); ?></strong></label>
                    <input type="checkbox" id="ywcdd_order_shipped" name="ywcdd_order_shipped"
                           value="1" <?php checked( 'yes', $order_shipped ); ?> <?php esc_attr_e( $disable_option ); ?>/>
					<?php echo $disable_tooltip; ?>
                </p>
            </div>
            <div class="edit_processing_delivery_date">
                <p class="edit_processing_method">
                    <label for="ywcdd_edit_processing_method"><?php _e( 'Processing Method', 'yith-woocommerce-delivery-date' ); ?></label>
					<?php
					$all_processing_method = YITH_Delivery_Date_Processing_Method()->get_processing_method( array( 'fields' => 'ids' ) );
					?>
                    <select id="ywcdd_edit_processing_method">
                        <option value="" <?php selected( '', $method_id ); ?>><?php _e( 'Select a Processing Method', 'yith-woocommerce-delivery-date' ); ?></option>
						<?php foreach ( $all_processing_method as $processing_method_id ): ?>
                            <option value="<?php esc_attr_e( $processing_method_id ); ?>" <?php selected( $processing_method_id, $method_id ); ?>><?php echo get_the_title( $processing_method_id ); ?></option>
						<?php endforeach; ?>
                    </select>
                </p>
                <p class="edit_carrier">
                    <label for="ywcdd_edit_carrier">
						<?php _e( 'Carrier', 'yith-woocommerce-delivery-date' ); ?>
                    </label>
					<?php
					$all_carrier = YITH_Delivery_Date_Carrier()->get_all_formatted_carriers();
					?>
                    <select id="ywcdd_edit_carrier">
                        <option value="" <?php selected( '', $carrier_id ); ?>><?php _e( 'Select a Carrier', 'yith-woocommerce-delivery-date' ); ?></option>
						<?php foreach ( $all_carrier as $id => $carrier_name ): ?>
                            <option value="<?php esc_attr_e( $id ); ?>" <?php selected( $carrier_id, $id ); ?>><?php echo $carrier_name; ?></option>
						<?php endforeach; ?>
                    </select>
                </p>
                <p class="edit_processing_date">
                    <label for="ywcdd_edit_processing_date">
						<?php _e( 'Processing date', 'yith-woocommerce-delivery-date' ); ?>
                    </label>
                    <input id="ywcdd_edit_processing_date" type="text" class="ywcdd_datepicker"
                           value="<?php echo $shipping_date; ?>">
                </p>
                <p class="edit_delivery_date">
                    <label for="ywcdd_edit_delivery_date">
						<?php _e( 'Delivery date', 'yith-woocommerce-delivery-date' ); ?>
                    </label>
                    <input id="ywcdd_edit_delivery_date" type="text" class="ywcdd_datepicker"
                           value="<?php echo $delivery_date; ?>">
                </p>
                <p class="edit_time_from">
                    <label for="ywcdd_edit_time_from">
						<?php _e( 'Time From', 'yith-woocommerce-delivery-date' ); ?>
                    </label>
                    <input id="ywcdd_edit_time_from" type="text" class="ywcdd_timepicker"
                           value="<?php echo $time_from; ?>">
                </p>
                <p class="edit_time_to">
                    <label for="ywcdd_edit_time_to">
						<?php _e( 'Time To', 'yith-woocommerce-delivery-date' ); ?>
                    </label>
                    <input id="ywcdd_edit_time_to" type="text" class="ywcdd_timepicker" value="<?php echo $time_to; ?>">
                </p>
                <button class="button-secondary ywcdd_update_date"><?php _e( 'Update delivery details', 'yith-woocommerce-delivery-date' ); ?></button>
            </div>
        </div>
    </div>
	<?php
}