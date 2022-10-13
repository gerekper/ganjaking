
<form method="post" action="" id="address_form">

	<?php

	// set the address fields
	foreach ( $addresses as $x => $addr ) {
		if ( empty( $addr ) )
			continue;

		$address_fields = WC()->countries->get_address_fields( $addr['shipping_country'], 'shipping_' );

		$address = array();
		$formatted_address = false;

		foreach ( $address_fields as $field_name => $field ) {
			$addr_key = str_replace('shipping_', '', $field_name);
			$address[$addr_key] = ( isset($addr[$field_name]) ) ? $addr[$field_name] : '';
		}

		if (! empty($address) ) {
			$formatted_address = wcms_get_formatted_address( $address );
			$json_address      = wp_json_encode( $address );
			$json_address      = function_exists( 'wc_esc_json' ) ? wc_esc_json( $json_address ) : _wp_specialchars( $json_address, ENT_QUOTES, 'UTF-8', true );
		}

		if ( ! $formatted_address )
			continue;
		?>
		<div style="display: none;">
			<?php
			do_action('woocommerce_after_checkout_shipping_form', $checkout);
			?>
		<input type="hidden" name="addresses[]" value="<?php echo $x; ?>" />
		<textarea style="display:none;"><?php echo $json_address; ?></textarea>
		</div>
		<?php
	}
	$ms_settings = get_option( 'woocommerce_multiple_shipping_settings', array() );

	$add_url = add_query_arg( 'address-form', '1' );
	?>

	<div>
		<a class="h2-link" href="<?php echo $add_url; ?>"><?php _e('Add a new shipping address', 'wc_shipping_multiple_address'); ?></a>

		<?php
		if ( isset($ms_settings['cart_duplication']) && $ms_settings['cart_duplication'] != 'no' ):
			$dupe_url = add_query_arg( array(
				'duplicate-form' => '1',
				'_wcmsnonce'     => wp_create_nonce( 'wcms-duplicate-cart' ),
			), get_permalink( wc_get_page_id( 'multiple_addresses' ) ) );
		?>
			<div style="float: right;">
				<a class="h2-link" href="<?php echo esc_url( $dupe_url ); ?>"><?php _e( 'Duplicate Cart', 'wc_shipping_multiple_address' ); ?></a>
				<img class="help_tip" title="<?php _e( 'Duplicating your cart will allow you to ship the exact same cart contents to multiple locations. This will also increase the price of your purchase.', 'wc_shipping_multiple_address' ); ?>" src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16">
			</div>
		<?php
		endif;
		?>
	</div>

	<table class="wc-shipping-multiple-addresses shop_table cart" cellspacing="0">
		<thead>
			<tr>
				<th class="product-name" width="20%"><?php _e( 'Product', 'wc_shipping_multiple_address' ); ?></th>
				<th class="product-quantity" width="20%"><?php _e( 'Quantity', 'wc_shipping_multiple_address' ); ?></th>
				<?php do_action('wc_ms_address_table_head'); ?>
				<th class="shipping-address" width="30%"><?php _e( 'Shipping Address', 'wc_shipping_multiple_address' ); ?></th>
				<th class="remove-item" width="20%">&nbsp;</th>
			</tr>
		</thead>
		<tbody>
		<?php

		foreach ($contents as $key => $value):
			$_product   = $value['data'];
			$pid        = $value['product_id'];

			if (! $_product->needs_shipping() ) continue;

			for ( $x = 0; $x < $value['quantity']; $x++ ):
		?>
			<tr>
				<td>
				<?php
				echo apply_filters( 'wcms_product_title', get_the_title($value['product_id']), $value );

				if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.0.0', '>' ) ) {
					if ( is_array( $value['variation'] ) && ! empty( $value['variation'] ) ) {
						echo ' (' . implode( ',',  array_values( $value['variation'] ) ) . ') ';
					}
				} else {
					echo WC_MS_Compatibility::get_item_data( $value );
				}
				?>
				</td>
				<td>
					<?php

					//$qty = array_count_values($relations[$x]);
					$product_quantity = woocommerce_quantity_input( array(
							'input_name'  => "items[{$key}][qty][]",
							'input_value' => 1,
							'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
						), $_product, false );
					echo $product_quantity;
					?>
				</td>
				<?php
				$address_key = "";
				$option_selected = false;
				foreach ( $addresses as $addr_key => $address ) {
					if ( !$option_selected && isset($relations[ $addr_key ]) ) {
						$rel_key = array_search( $key, $relations[ $addr_key ] );
						if ( $rel_key !== false ) {
							$option_selected = true;
							$address_key = $addr_key;
							unset( $relations[ $addr_key ][ $rel_key ] );
						}
					}
				}
				do_action( 'wc_ms_multiple_address_table_row', $key, $value, $address_key );
				?>
				<td>
					<select name="items[<?php echo $key; ?>][address][]" class="address-select">
					<?php

					foreach ( $addresses as $addr_key => $address ) {
						$formatted = $address['shipping_first_name'] .' '. $address['shipping_last_name'] .',';
						$formatted .= ' '. $address['shipping_address_1'] .' '. $address['shipping_address_2'] .',';
						$formatted .= ' '. $address['shipping_city'] .', '. $address['shipping_state'];

						echo '<option value="'. $addr_key .'" '. selected( $address_key, $addr_key ) .'>'. $formatted .'</option>';
						$selected = '';
					}
					?>
					</select>

				</td>
				<td><input type="submit" name="delete_line" class="button delete-line-item" data-key="<?php echo $key; ?>" data-index="<?php echo $x; ?>" value="<?php _e('Delete', 'wc_shipping_multiple_address'); ?>" /></td>
			</tr>
		<?php
			endfor;
		endforeach;
		?>
		</tbody>
	</table>

	<div class="form-row">
		<?php wp_nonce_field( 'shipping_address_action' ); ?>
		<input type="hidden" name="delete[index]" id="delete_index" value="" />
		<input type="hidden" name="delete[key]" id="delete_key" value="" />
		<input type="hidden" name="shipping_type" value="item" />
		<input type="hidden" name="shipping_address_action" value="save" />

		<div class="update-shipping-addresses">
			<input type="submit" name="update_quantities" class="button" value="<?php _e('Update', 'wc_shipping_multiple_address'); ?>" />
		</div>

		<div class="set-shipping-addresses">
			<input class="button alt" type="submit" name="set_addresses" value="<?php echo __('Save Addresses and Continue', 'wc_shipping_multiple_address'); ?>" />
		</div>

	</div>

	<div class="clear"></div>

	<small>
		<?php _e('Please note: To send a single item to more than one person, you must change the quantity of that item to match the number of people you\'re sending it to, then click the Update button.', 'wc_shipping_multiple_address'); ?>
	</small>

</form>
