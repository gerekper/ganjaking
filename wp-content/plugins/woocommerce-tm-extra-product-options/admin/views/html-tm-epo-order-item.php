<?php
/**
 * Shows an order item
 *
 * @var object $item    The item being displayed
 * @var int    $item_id The id of the item being displayed
 *
 * @package Extra Product Options/Admin/Views
 * @version 6.0
 */

defined( 'ABSPATH' ) || exit;

$input_type = ( is_callable( [ $order, 'is_editable' ] ) && $order->is_editable() ) ? 'number' : 'text';

$product_link  = $_product ? admin_url( 'post.php?post=' . absint( themecomplete_get_id( $_product ) ) . '&action=edit' ) : '';
$thumbnail     = '';
$tax_data      = wc_tax_enabled() ? themecomplete_maybe_unserialize( isset( $item['line_tax_data'] ) ? $item['line_tax_data'] : '' ) : false;
$item_total    = ( isset( $item['line_total'] ) ) ? esc_attr( wc_format_localized_price( $item['line_total'] ) ) : '';
$item_subtotal = ( isset( $item['line_subtotal'] ) ) ? esc_attr( wc_format_localized_price( $item['line_subtotal'] ) ) : '';

$currency_arg             = [ 'currency' => ( is_callable( [ $order, 'get_currency' ] ) ? $order->get_currency() : $order->get_order_currency() ) ];
$epo_can_show_order_price = apply_filters( 'epo_can_show_order_price', true, $item_meta );
$row_class                = apply_filters( 'woocommerce_admin_html_order_item_class', isset( $class ) && ! empty( $class ) ? $class : '', $item, $order );
?>
<tr class="tm-order-line-option item <?php echo esc_attr( $row_class ); ?>" data-order_item_id="<?php echo esc_attr( $item_id ); ?>" data-tm_item_id="<?php echo esc_attr( $item_id ); ?>" data-tm_key_id="<?php echo esc_attr( $key ); ?>">
	<?php echo ( version_compare( WC()->version, '2.6', '>=' ) ) ? '' : '<td class="check-column">&nbsp;</td>'; ?>
	<td class="thumb">
		<?php
		echo '<div class="tc-epo-wc-order-item-thumbnail">' . apply_filters( 'wc_epo_kses', wp_kses_post( $thumbnail ), $thumbnail, false ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
	</td>
	<td class="tm-c name" data-sort-value="<?php echo esc_attr( $item['name'] ); ?>">
		<div class="tm-50">
			<?php
			echo apply_filters( 'wc_epo_kses', wp_kses_post( $epo_name ), $epo_name, false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</div>
		<div class="view">
			<div class="tm-50">
				<?php
				echo apply_filters( 'wc_epo_kses', wp_kses_post( $epo_value ), $epo_value, false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				?>
			</div>
		</div>
		<?php if ( $epo_edit_value ) { ?>
			<div class="edit">
				<div class="tm-50">
					<?php
					$textarea_value = '';
					if ( is_array( $epo['value'] ) ) {
						$edit_value     = THEMECOMPLETE_EPO_HELPER()->entity_decode( $epo['value'] );
						$edit_value     = THEMECOMPLETE_EPO_HELPER()->recursive_implode( $edit_value, THEMECOMPLETE_EPO()->tm_epo_multiple_separator_cart_text );
						$textarea_value = $edit_value;
					} else {
						$textarea_value = $epo['value'];
					}
					?>
					<textarea novalidate name="tm_epo[<?php echo esc_attr( $item_id ); ?>][<?php echo esc_attr( $key ); ?>][value]" class="value"><?php echo esc_textarea( $textarea_value ); ?></textarea>
				</div>
			</div>
		<?php } ?>
	</td>

	<?php

	do_action( 'woocommerce_admin_order_item_values', $_product, $item, 0 );

	?>

	<td class="item_cost" width="1%" data-sort-value="<?php echo esc_attr( $order->get_item_subtotal( $item, false, true ) ); ?>">
		<?php

		if ( $epo['quantity'] <= 0 ) {
			if ( $epo_can_show_order_price ) {
				echo '<div class="view">' . wc_price( 0, $currency_arg ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			if ( $epo_can_show_order_price && $epo_edit_cost ) {
				echo '<div class="edit"><input novalidate type="' . esc_attr( $input_type ) . '" name="tm_epo[' . esc_attr( $item_id ) . '][' . esc_attr( $key ) . '][price]" placeholder="0" value="0" data-qty="0" class="price"></div>';
			}
		} else {
			echo '<div class="view">';

			if ( $epo_can_show_order_price ) {
				if ( $epo_is_fee ) {
					echo wc_price( (float) $epo['price'], $currency_arg ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo wc_price(
						themecomplete_order_get_price_excluding_tax(
							$order,
							$item_id,
							[
								'qty'   => 1,
								'price' => $epo['price'],
							]
						) / $epo['quantity'],
						$currency_arg
					);
				}
			}

			echo '</div>';
			if ( $epo_can_show_order_price && $epo_edit_cost ) {
				echo '<div class="edit"><input novalidate type="' . esc_attr( $input_type ) . '" name="tm_epo[' . esc_attr( $item_id ) . '][' . esc_attr( $key ) . '][price]" placeholder="0" value="' .
				esc_attr(
					themecomplete_order_get_price_excluding_tax(
						$order,
						$item_id,
						[
							'qty'   => 1,
							'price' => $epo['price'],
						]
					) / $epo['quantity']
				) .
				'" data-qty="' .
				esc_attr(
					$epo['quantity']
				) .
				'" class="price"></div>';
			}
		}

		?>
	</td>
	<td class="quantity" width="1%">
		<div class="view">
			<?php
			echo apply_filters( 'wc_epo_kses', wp_kses_post( $epo_quantity ), $epo_quantity, false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</div>
		<?php if ( $epo_edit_quantity ) { ?>
			<div class="edit">
				<?php $item_qty = esc_attr( $item['qty'] ); ?>
				<input novalidate type="<?php echo esc_attr( $input_type ); ?>" step="1" min="0" autocomplete="off" name="tm_epo[<?php echo absint( $item_id ); ?>][<?php echo esc_attr( $key ); ?>][quantity]" placeholder="0" value="<?php echo esc_attr( $epo['quantity'] ); ?>" data-qty="<?php echo esc_attr( $epo['quantity'] ); ?>" class="quantity">
				<small>&times;<?php echo esc_html( (float) $item_meta['_qty'][0] ); ?></small>
			</div>
		<?php } ?>
	</td>
	<td class="line_cost" width="1%" data-sort-value="<?php echo esc_attr( isset( $item['line_total'] ) ? $item['line_total'] : '' ); ?>">
		<div class="view">
			<?php
			if ( $epo_can_show_order_price ) {
				echo '<span class="amount">';
				if ( $epo_is_fee ) {
					echo wc_price( (float) $epo['price'], $currency_arg ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo wc_price(
						(float) themecomplete_order_get_price_excluding_tax(
							$order,
							$item_id,
							[
								'qty'   => 1,
								'price' => $epo['price'],
							]
						) * (float) $item_meta['_qty'][0],
						$currency_arg
					);
				}
				echo '</span>';
			}
			?>
		</div>
	</td>

	<?php
	if ( ! empty( $tax_data ) ) {
		$tax_based_on = get_option( 'woocommerce_tax_based_on' );

		if ( 'billing' === $tax_based_on ) {
			$country  = $order->get_billing_country();
			$state    = $order->get_billing_state();
			$postcode = $order->get_billing_postcode();
			$city     = $order->get_billing_city();
		} elseif ( 'shipping' === $tax_based_on ) {
			$country  = $order->get_shipping_country();
			$state    = $order->get_shipping_state();
			$postcode = $order->get_shipping_postcode();
			$city     = $order->get_shipping_city();
		}

		// Default to base.
		if ( 'base' === $tax_based_on || empty( $country ) ) {
			$default  = wc_get_base_location();
			$country  = $default['country'];
			$state    = $default['state'];
			$postcode = '';
			$city     = '';
		}
		$tax_class = $item['tax_class'];
		$tax_rates = WC_Tax::find_rates(
			[
				'country'   => $country,
				'state'     => $state,
				'postcode'  => $postcode,
				'city'      => $city,
				'tax_class' => $tax_class,
			]
		);
		if ( $epo_is_fee ) {
			$epo_line_taxes = WC_Tax::calc_tax( (float) $epo['price'], $tax_rates, false );
		} else {
			$epo_line_taxes = WC_Tax::calc_tax( (float) $epo['price'] * (float) $item_meta['_qty'][0], $tax_rates, themecomplete_order_get_att( $order, 'prices_include_tax' ) );
		}


		foreach ( $order_taxes as $tax_item ) {
			$tax_item_id = $tax_item['rate_id'];
			if ( is_callable( [ $tax_item, 'get_rate_id' ] ) ) {
				$tax_item_id = $tax_item->get_rate_id();
			}
			?>
			<td class="line_tax" width="1%">
				<div class="view">
					<?php
					if ( isset( $epo_line_taxes[ $tax_item_id ] ) ) {
						$tax_price = $epo_line_taxes[ $tax_item_id ];
						echo wc_price( wc_round_tax_total( $tax_price ), $currency_arg ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					} else {
						echo '&ndash;';
					}
					?>
				</div>
			</td>
			<?php
		}
	}
	?>
	<td class="wc-order-edit-line-item" width="1%">
		<div class="wc-order-edit-line-item-actions">
			<?php if ( $edit_buttons && $order->is_editable() ) : ?>
				<a class="edit-order-item tips" href="#" data-tip="<?php esc_attr_e( 'Edit item', 'woocommerce' ); ?>"></a>
				<a class="tm-delete-order-item tips" href="#" data-tip="<?php esc_attr_e( 'Delete item', 'woocommerce' ); ?>"></a>
			<?php endif; ?>
		</div>
	</td>
</tr>
