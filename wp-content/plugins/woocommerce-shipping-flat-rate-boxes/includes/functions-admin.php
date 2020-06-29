<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * wc_box_shipping_admin_rows function.
 *
 * @access public
 * @param mixed $table_rate_shipping
 */
function wc_box_shipping_admin_rows( $method ) {
	wp_enqueue_script( 'woocommerce_shipping_flat_rate_box_rows' );
	?>
	<table id="flat_rate_boxes" class="shippingrows widefat" cellspacing="0" style="position:relative;">
		<thead>
			<tr>
				<th class="check-column"><input type="checkbox"></th>
				<th><?php _e( 'Length', 'woocommerce-shipping-flat-rate-boxes' ); ?> (<?php echo esc_html( get_option( 'woocommerce_dimension_unit' ) ); ?>)</th>
				<th><?php _e( 'Width', 'woocommerce-shipping-flat-rate-boxes' ); ?> (<?php echo esc_html( get_option( 'woocommerce_dimension_unit' ) ); ?>)</th>
				<th><?php _e( 'Height', 'woocommerce-shipping-flat-rate-boxes' ); ?> (<?php echo esc_html( get_option( 'woocommerce_dimension_unit' ) ); ?>)</th>
				<th><?php _e( 'Weight', 'woocommerce-shipping-flat-rate-boxes' ); ?> (<?php echo esc_html( get_option( 'woocommerce_weight_unit' ) ); ?>)</th>
				<th><?php _e( 'Cost', 'woocommerce-shipping-flat-rate-boxes' ); ?>&nbsp;<a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'Cost for shipping the box (excl. tax)', 'woocommerce-shipping-flat-rate-boxes' ) ); ?>">[?]</a></th>
				<th><?php echo __( 'Cost per', 'woocommerce-shipping-flat-rate-boxes' ) . ' ' . esc_html( get_option( 'woocommerce_weight_unit' ) ); ?>&nbsp;<a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'Cost per weight unit (excl. tax)', 'woocommerce-shipping-flat-rate-boxes' ) ); ?>">[?]</a></th>
				<th><?php _e( 'Cost %', 'woocommerce-shipping-flat-rate-boxes' ); ?>&nbsp;<a class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'Charge a percentage based on the cost of the items packed into the box.', 'woocommerce-shipping-flat-rate-boxes' ) ); ?>">[?]</a></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th colspan="10"><a href="#" class="add-box button button-primary"><?php _e( 'Add box', 'woocommerce-shipping-flat-rate-boxes' ); ?></a> <a href="#" class="remove button"><?php _e( 'Delete selected', 'woocommerce-shipping-flat-rate-boxes' ); ?></a></th>
			</tr>
		</tfoot>
		<tbody class="flat_rate_boxes" data-boxes="<?php echo _wp_specialchars( wp_json_encode( $method->get_boxes() ),  ENT_QUOTES, 'UTF-8', true ); // Same as wc_esc_json but it's only in WC 3.5.5. ?>"></tbody>
	</table>
	<script type="text/template" id="tmpl-flat-rate-box-row-template">
		<tr class="flat_rate_box">
			<td class="check-column">
				<input type="checkbox" name="select" />
				<input type="hidden" class="box_id" name="box_id[{{{ data.index }}}]" value="{{{ data.box.box_id }}}" />
			</td>
			<td><input type="text" class="text" name="box_length[{{{ data.index }}}]" placeholder="<?php _e( '0', 'woocommerce-shipping-flat-rate-boxes' ); ?>" size="4" value="{{{ data.box.box_length }}}" /></td>
			<td><input type="text" class="text" name="box_width[{{{ data.index }}}]" placeholder="<?php _e( '0', 'woocommerce-shipping-flat-rate-boxes' ); ?>" size="4" value="{{{ data.box.box_width }}}" /></td>
			<td><input type="text" class="text" name="box_height[{{{ data.index }}}]" placeholder="<?php _e( '0', 'woocommerce-shipping-flat-rate-boxes' ); ?>" size="4" value="{{{ data.box.box_height }}}" /></td>
			<td><input type="text" class="text" name="box_max_weight[{{{ data.index }}}]" placeholder="<?php _e( '0', 'woocommerce-shipping-flat-rate-boxes' ); ?>" size="4" value="{{{ data.box.box_max_weight }}}" /></td>
			  <td><input type="text" class="text" name="box_cost[{{{ data.index }}}]" placeholder="<?php _e( '0', 'woocommerce-shipping-flat-rate-boxes' ); ?>" size="4" value="{{{ data.box.box_cost }}}" /></td>
			  <td><input type="text" class="text" name="box_cost_per_weight_unit[{{{ data.index }}}]" placeholder="<?php _e( '0', 'woocommerce-shipping-flat-rate-boxes' ); ?>" size="4" value="{{{ data.box.box_cost_per_weight_unit }}}" /></td>
			  <td><input type="text" class="text" name="box_cost_percent[{{{ data.index }}}]" placeholder="<?php _e( '0', 'woocommerce-shipping-flat-rate-boxes' ); ?>" size="4" value="{{{ data.box.box_cost_percent }}}" /></td>
		</tr>
	</script>
	<?php
}


/**
 * wc_box_shipping_admin_rows_process function.
 *
 * @access public
 * @return void
 */
function wc_box_shipping_admin_rows_process( $shipping_method_id ) {
	global $wpdb;

	// Clear cache
	$wpdb->query( "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_wc_ship_%')" );

	// Save rates
	$box_ids                   = isset( $_POST['box_id'] ) ? array_map( 'intval', $_POST['box_id'] ) : array();
	$box_lengths               = isset( $_POST['box_length'] ) ? array_map( 'wc_clean', $_POST['box_length'] ) : array();
	$box_widths                = isset( $_POST['box_width'] ) ? array_map( 'wc_clean', $_POST['box_width'] ) : array();
	$box_heights               = isset( $_POST['box_height'] ) ? array_map( 'wc_clean', $_POST['box_height'] ) : array();
	$box_max_weights           = isset( $_POST['box_max_weight'] ) ? array_map( 'wc_clean', $_POST['box_max_weight'] ) : array();
	$box_costs                 = isset( $_POST['box_cost'] ) ? array_map( 'wc_clean', $_POST['box_cost'] ) : array();
	$box_cost_per_weight_units = isset( $_POST['box_cost_per_weight_unit'] ) ? array_map( 'wc_clean', $_POST['box_cost_per_weight_unit'] ) : array();
	$box_cost_percents         = isset( $_POST['box_cost_percent'] ) ? array_map( 'wc_clean', $_POST['box_cost_percent'] ) : array();

	// Get max key
	$max_key = ( $box_ids ) ? max( array_keys( $box_ids ) ) : 0;

	for ( $i = 0; $i <= $max_key; $i++ ) {

		if ( ! isset( $box_ids[ $i ] ) ) {
			continue;
		}

		$box_id                   = $box_ids[ $i ];
		$box_length               = $box_lengths[ $i ];
		$box_width                = $box_widths[ $i ];
		$box_height               = $box_heights[ $i ];
		$box_max_weight           = $box_max_weights[ $i ];
		$box_cost                 = rtrim( rtrim( number_format( (double) $box_costs[ $i ], 4, '.', '' ), '0' ), '.' );
		$box_cost_per_weight_unit = rtrim( rtrim( number_format( (double) $box_cost_per_weight_units[ $i ], 4, '.', '' ), '0' ), '.' );
		$box_cost_percent         = rtrim( rtrim( number_format( (double) $box_cost_percents[ $i ], 4, '.', '' ), '0' ), '.' );

		if ( $box_id > 0 ) {

			// Update row
			$wpdb->update(
				$wpdb->prefix . 'woocommerce_shipping_flat_rate_boxes',
				array(
					'box_length'               => $box_length,
					'box_width'                => $box_width,
					'box_height'               => $box_height,
					'box_max_weight'           => $box_max_weight,
					'box_cost'                 => $box_cost,
					'box_cost_per_weight_unit' => $box_cost_per_weight_unit,
					'box_cost_percent'         => $box_cost_percent,
					'shipping_method_id'       => $shipping_method_id
				),
				array(
					'box_id' => $box_id
				),
				array(
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%d',
				),
				array(
					'%d'
				)
			);

		} else {

			// Insert row
			$result = $wpdb->insert(
				$wpdb->prefix . 'woocommerce_shipping_flat_rate_boxes',
				array(
					'box_length'               => $box_length,
					'box_width'                => $box_width,
					'box_height'               => $box_height,
					'box_max_weight'           => $box_max_weight,
					'box_cost'                 => $box_cost,
					'box_cost_per_weight_unit' => $box_cost_per_weight_unit,
					'box_cost_percent'         => $box_cost_percent,
					'shipping_method_id'       => $shipping_method_id
				),
				array(
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%d',
				)
			);
		}
	}
}
