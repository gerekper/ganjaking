<?php

/**
 * Get variation string.
 *
 * @param WC_Order                    $order WC_Order.
 * @param WC_Order_Item_Product|array $item WC_Order_Item_Product.
 * @return string
 */
function warranty_get_variation_string( $order, $item ) {
	if ( is_callable( array( $item, 'get_product' ) ) ) {
		$product = $item->get_product();
	} else {
		$product = $order->get_product_from_item( $item );
	}

	$formatted_meta = array();
	if ( ! empty( $item['item_meta_array'] ) ) {
		foreach ( $item['item_meta_array'] as $meta_id => $meta ) {
			if ( '' === $meta->value || is_serialized( $meta->value ) || '_' === substr( $meta->key, 0, 1 ) ) {
				continue;
			}

			$attribute_key = urldecode( str_replace( 'attribute_', '', $meta->key ) );

			// If this is a term slug, get the term's nice name.
			if ( taxonomy_exists( $attribute_key ) ) {
				$term = get_term_by( 'slug', $meta->value, $attribute_key );
				if ( ! is_wp_error( $term ) && is_object( $term ) && $term->name ) {
					$meta->value = $term->name;
				}
			}

			$formatted_meta[ $meta_id ] = array(
				'key'   => $meta->key,
				'label' => wc_attribute_label( $attribute_key, $product ),
				'value' => apply_filters( 'woocommerce_order_item_display_meta_value', $meta->value, $meta, $item ),
			);
		}
	}

	$output = '';
	if ( ! empty( $formatted_meta ) ) {
		$meta_list = array();

		foreach ( $formatted_meta as $meta ) {
			$meta_list[] = wp_kses_post( $meta['label'] . ': ' . $meta['value'] );
		}

		if ( ! empty( $meta_list ) ) {
			$output .= implode( '<br/>', $meta_list );
		}
	}

	return $output;
}

/**
 * Render Return Shipping Tracking Code Form.
 *
 * @param array $request Data from "Return Shipping Tracking Code Form".
 *
 * @return string
 */
function warranty_return_shipping_tracking_code_form( $request ) {
	ob_start();
	?>
	<div class="warranty-tracking-container">

		<?php
		if ( 'y' === $request['request_tracking_code'] ) {
			if ( ! empty( $request['tracking_code'] ) ) {
				// Tracking code provided - show tracking link.
				?>
				<div class="customer-tracking-code-container">
					<b><?php esc_html_e( 'Track Your Package', 'wc_warranty' ); ?></b>
					<?php
					if ( ! empty( $request['tracking_provider'] ) ) {
						$all_providers  = array();
						$providers_name = array();

						foreach ( WooCommerce_Warranty::get_providers() as $providers ) {
							foreach ( $providers as $provider => $format ) {
								$all_providers[ sanitize_title( $provider ) ]  = $format;
								$providers_name[ sanitize_title( $provider ) ] = $provider;
							}
						}

						$provider = $request['tracking_provider'];
						$link     = $all_providers[ $provider ];
						$link     = str_replace( '%1$s', $request['tracking_code'], $link );
						$link     = str_replace( '%2$s', '', $link );

						echo '<ul class="warranty-data">';
						echo '<li>' . esc_html__( 'Shipped via', 'wc_warranty' ) . ' ' . esc_html( $providers_name[ $provider ] ) . '</li>';
						echo '<li>' . esc_html__( 'Tracking Code:', 'wc_warranty' ) . ' ' . esc_html( $request['tracking_code'] ) . ' <br/> (<a href="' . esc_url( $link ) . '" target="_blank">' . esc_html__( 'Track Shipment', 'wc_warranty' ) . '</a>)</li>';
						echo '</ul>';
					} else {
						echo '<p>' . esc_html__( 'Tracking Number:', 'wc_warranty' ) . ' ' . esc_html( $request['tracking_code'] ) . '</p>';
					}
					?>
				</div>
				<?php
			}
		}

		if ( ! empty( $request['return_tracking_code'] ) ) {
			if ( ! empty( $request['return_tracking_provider'] ) ) {
				?>
				<div class="store-tracking-code-container">
					<b><?php esc_html_e( 'Shipment Tracking Code', 'wc_warranty' ); ?></b>
					<?php
					$all_providers  = array();
					$providers_name = array();

					foreach ( WooCommerce_Warranty::$providers as $providers ) {
						foreach ( $providers as $provider => $format ) {
							$all_providers[ sanitize_title( $provider ) ]  = $format;
							$providers_name[ sanitize_title( $provider ) ] = $provider;
						}
					}

					$provider = $request['return_tracking_provider'];
					$link     = $all_providers[ $provider ];
					$link     = str_replace( '%1$s', $request['return_tracking_code'], $link );
					$link     = str_replace( '%2$s', '', $link );
					?>
					<ul class="warranty-data">
						<?php // translators: %s is a provider name. ?>
						<li><?php printf( esc_html__( 'Shipped via %s', 'wc_warranty' ), esc_html( $providers_name[ $provider ] ) ); ?></li>
						<?php // translators: %s is a return tracking code. ?>
						<li><?php printf( esc_html__( 'Tracking Code: %1$s &mdash; %1$s', 'wc_warranty' ), esc_html( $request['return_tracking_code'] ), '<a href="' . esc_url( $link ) . '" target="_blank">' . esc_html__( 'Track Return Shipment', 'wc_warranty' ) . '</a>' ); ?></li>
					</ul>
				</div>
				<?php
			} else {
				echo '<p>' . esc_html__( 'Your Tracking Number is:', 'wc_warranty' ) . ' ' . esc_html( $request['return_tracking_code'] ) . '</p>';
			}
		}
		?>
	</div>
	<?php

	return ob_get_clean();
}

/**
 * Render Request Shipping Tracking Code Form.
 *
 * @param array $request Data from "Request Shipping Tracking Code Form".
 *
 * @return string
 */
function warranty_request_shipping_tracking_code_form( $request ) {
	ob_start();
	?>
	<div class="warranty-tracking-code-container">
		<strong><?php esc_html_e( 'Shipping Tracking Code', 'wc_warranty' ); ?></strong>

		<p class="form-field">
			<label class="form-label" for="shipping_provider_<?php echo esc_attr( $request['ID'] ); ?>">
				<?php esc_html_e( 'Shipping Provider', 'wc_warranty' ); ?>
			</label>
			<select class="tracking_provider" name="tracking_provider" id="shipping_provider_<?php echo esc_attr( $request['ID'] ); ?>" data-request_id="<?php echo esc_attr( $request['ID'] ); ?>">
				<?php
				foreach ( WooCommerce_Warranty::get_providers() as $provider_group => $providers ) {
					echo '<optgroup label="' . esc_attr( $provider_group ) . '">';
					foreach ( $providers as $provider => $url ) {
						$selected = ( sanitize_title( $provider ) === $request['tracking_provider'] ) ? 'selected' : '';
						echo '<option value="' . esc_attr( sanitize_title( $provider ) ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $provider ) . '</option>';
					}
					echo '</optgroup>';
				}
				?>
			</select>
		</p>

		<p class="form-field">
			<label class="form-label" for="tracking_code_<?php echo esc_attr( $request['ID'] ); ?>">
				<?php esc_html_e( 'Tracking Code', 'wc_warranty' ); ?>
			</label>
			<br/>
			<input type="text" class="tracking_code" name="tracking_code" id="tracking_code_<?php echo esc_attr( $request['ID'] ); ?>" value="<?php echo esc_attr( $request['tracking_code'] ); ?>" data-request_id="<?php echo esc_attr( $request['ID'] ); ?>" />
		</p>

		<input type="hidden" name="action" value="set_tracking_code" />
		<input type="hidden" name="request_id" value="<?php echo esc_attr( $request['ID'] ); ?>" />
		<input type="submit" name="send" value="<?php esc_html_e( 'Add Tracking Code', 'wc_warranty' ); ?>" class="button" />
	</div>
	<?php
	return ob_get_clean();
}

/**
 * Remove slashes from text.
 *
 * @param string $value String with slashes.
 * @return string
 */
function warranty_stripslashes( $value ) {
	return stripslashes( $value );
}

/**
 * Return warranty from order item.
 *
 * @param array $item Item data.
 * @return array
 */
function warranty_get_order_item_warranty( $item ) {
	$meta     = $item['item_meta'];
	$warranty = array( 'type' => 'no_warranty' );

	foreach ( $meta as $key => $value ) {
		$value = version_compare( WC_VERSION, '3.0', '<' ) ? stripslashes( $value[0] ) : $value;

		( is_array( $value ) ) ? array_walk_recursive( $value, 'warranty_stripslashes' ) : $value = stripslashes( $value );

		if ( '_item_warranty' === $key ) {
			$warranty = array_merge( $warranty, (array) maybe_unserialize( $value ) );
		} elseif ( '_item_warranty_selected' === $key ) {
			$warranty['warranty_idx'] = $value;
		}
	}

	return $warranty;
}

/**
 * Get warranty duration.
 *
 * @param array    $warranty Warranty.
 * @param WC_Order $order WC_Order.
 * @return string
 */
function warranty_get_warranty_duration_string( $warranty, $order ) {
	$completed = $order->get_date_completed() ? $order->get_date_completed()->date( 'Y-m-d H:i:s' ) : false;

	if ( empty( $completed ) ) {
		$completed = false;
	}

	/*
	 * If there is a default warranty set, and we're instructed to
	 * override with the default, lets use the default warranty.
	 */
	$maybe_override   = get_option( 'warranty_override_all' );
	$default_warranty = warranty_get_default_warranty();
	if (
		'yes' === $maybe_override &&
		'no_warranty' === $warranty['type'] &&
		'no_warranty' !== $default_warranty['type']
	) {
		$warranty = $default_warranty;
	}

	if ( 'no_warranty' === $warranty['type'] ) {
		$warranty_string = __( 'Product has no warranty', 'wc_warranty' );
	} elseif ( 'included_warranty' === $warranty['type'] ) {
		if ( 'lifetime' === $warranty['length'] ) {
			$warranty_string = __( 'Lifetime', 'wc_warranty' );
		} else {
			$order_date      = ( $completed ) ? $completed : ( $order instanceof WC_Order ? $order->get_date_created()->date( 'Y-m-d H:i:s' ) : '-' );
			$warranty_string = __( 'Expiry Date: ', 'wc_warranty' ) . warranty_get_date( $order_date, $warranty['value'], $warranty['duration'] );
		}
	} elseif ( 'addon_warranty' === $warranty['type'] ) {
		$idx             = $warranty['warranty_idx'];
		$warranty_string = '';

		if ( isset( $warranty['addons'][ $idx ] ) ) {
			$addon           = $warranty['addons'][ $idx ];
			$order_date      = ( $completed ) ? $completed : ( $order instanceof WC_Order ? $order->get_date_created()->date( 'Y-m-d H:i:s' ) : '-' );
			$warranty_string = __( 'Expiry Date: ', 'wc_warranty' ) . warranty_get_date( $order_date, $addon['value'], $addon['duration'] );
		}
	}

	return $warranty_string;
}

/**
 * Get a product's warranty details
 *
 * @param int  $product_id Product or variation ID.
 * @param bool $maybe_use_parent false to force loading variation's warranty.
 *
 * @return array
 */
function warranty_get_product_warranty( $product_id, $maybe_use_parent = true ) {
	$product          = wc_get_product( $product_id );
	$warranty         = $product->get_meta( '_warranty' );
	$warranty_control = '';

	if ( $maybe_use_parent && $product && $product->is_type( 'variation' ) ) {
		$parent_product_id = $product->get_parent_id();
		$parent_product    = wc_get_product( $parent_product_id );

		$warranty_control = $parent_product->get_meta( '_warranty_control' );

		if ( 'parent' === $warranty_control ) {
			$warranty = $parent_product->get_meta( '_warranty' );
		}
	}

	if ( ! $warranty ) {
		$category_warranties = get_option( 'wc_warranty_categories', array() );
		$categories          = wp_get_object_terms( $product_id, 'product_cat' );

		if ( ! is_wp_error( $categories ) ) {
			foreach ( $categories as $category ) {

				if ( ! empty( $category_warranties[ $category->term_id ] ) ) {
					$warranty = $category_warranties[ $category->term_id ];
					break;
				}
			}
		}
	}

	if ( ! $warranty ) {
		$warranty = warranty_get_default_warranty();
	}

	if ( empty( $warranty ) ) {
		$warranty = array(
			'type' => 'no_warranty',
		);
	}

	if ( empty( $warranty['label'] ) ) {
		$warranty['label'] = ( ! empty( $parent_product ) && 'parent' === $warranty_control ) ? $parent_product->get_meta( '_warranty_label' ) : $product->get_meta( '_warranty_label' );
	}

	$warranty['length']   = ( ! empty( $warranty['length'] ) ) ? $warranty['length'] : '';
	$warranty['duration'] = ( ! empty( $warranty['duration'] ) ) ? $warranty['duration'] : '';

	return apply_filters( 'get_product_warranty', $warranty, $product_id );
}

/**
 * Get the formatted warranty string of the given product
 *
 * @param int   $product_id WC_Product ID.
 * @param array $warranty Warranty data.
 * @return string
 */
function warranty_get_warranty_string( $product_id = 0, $warranty = null ) {
	if ( $product_id ) {
		$warranty = warranty_get_product_warranty( $product_id );
	}

	if ( empty( $warranty ) || 'no_warranty' === $warranty['type'] ) {
		$string = __( 'No warranty', 'wc_warranty' );
	} elseif ( 'included_warranty' === $warranty['type'] ) {
		if ( 'lifetime' === $warranty['length'] ) {
			$string = __( 'Lifetime Warranty Included', 'wc_warranty' );
		} else {
			$duration = warranty_duration_i18n( $warranty['duration'], $warranty['value'] );
			// translators: %1$d: warranty, %2$s: duration.
			$string = sprintf( esc_html__( 'Warranty Included (%1$d %2$s)', 'wc_warranty' ), $warranty['value'], $duration );
		}
	} else {
		$string = 'Add-on Warranty: ';
		foreach ( (array) $warranty['addons'] as $addon ) {
			$duration = warranty_duration_i18n( $addon['duration'], $addon['value'] );
			$amount   = wp_strip_all_tags( wc_price( floatval( $addon['amount'] ) ) );
			// translators: %1$d: addon, %2$s: duration, %3$s: amount.
			$string .= sprintf( esc_html__( '%1$d %2$s for %3$s; ', 'wc_warranty' ), $addon['value'], $duration, $amount );
		}
	}

	return apply_filters( 'get_warranty_string', $string, $product_id, $warranty );
}

/**
 * Switch the duration from plural to singular form depending on the passed $value
 *
 * @param string $duration (e.g. weeks, months).
 * @param int    $value Number of $duration.
 *
 * @return string
 */
function warranty_trim_duration( $duration, $value ) {
	if ( 1 === intval( $value ) ) {
		$duration = rtrim( $duration, 's' );
	}

	return $duration;
}

/**
 * Get the singular or plural form of the given duration.
 *
 * @param string $duration (e.g. weeks, months).
 * @param int    $value Number of $duration.
 * @return string
 */
function warranty_duration_i18n( $duration, $value = 0 ) {
	$units_i18n = array(
		'day'    => __( 'Day', 'wc_warranty' ),
		'days'   => __( 'Days', 'wc_warranty' ),
		'week'   => __( 'Week', 'wc_warranty' ),
		'weeks'  => __( 'Weeks', 'wc_warranty' ),
		'month'  => __( 'Month', 'wc_warranty' ),
		'months' => __( 'Months', 'wc_warranty' ),
		'year'   => __( 'Year', 'wc_warranty' ),
		'years'  => __( 'Years', 'wc_warranty' ),
	);

	$duration = warranty_trim_duration( $duration, $value );

	return isset( $units_i18n[ $duration ] ) ? $units_i18n[ $duration ] : $duration;
}

/**
 * Get the default warranty settings
 *
 * @return array
 */
function warranty_get_default_warranty() {
	$warranty = array(
		'type'               => get_option( 'warranty_default_type', 'no_warranty' ),
		'label'              => get_option( 'warranty_default_label', '' ),
		'length'             => get_option( 'warranty_default_length', 'lifetime' ),
		'value'              => get_option( 'warranty_default_length_value', 0 ),
		'duration'           => get_option( 'warranty_default_length_duration', 'days' ),
		'no_warranty_option' => get_option( 'warranty_default_addon_no_warranty', 'no' ),
		'addons'             => get_option( 'warranty_default_addons', array() ),
		'default'            => true,
	);

	return apply_filters( 'get_default_warranty', $warranty );
}

/**
 * Get the line total order item meta
 *
 * @param int $warranty_id Warranty ID.
 *
 * @return float
 */
function warranty_get_item_amount( $warranty_id ) {
	$item_indexes = array_column( warranty_get_request_items( $warranty_id ), 'order_item_index' );
	$total        = 0;

	if ( ! empty( $item_indexes ) ) {
		foreach ( $item_indexes as $item_index ) {

			try {
				$line_total = wc_format_decimal( wc_get_order_item_meta( $item_index, '_line_total', true ) );
				$total     += floatval( $line_total );
			} catch ( Exception $e ) {
				error_log( "Exception caught in warranty_get_item_amount. {$e->getMessage()}." );
			}
		}
	} else {
		$order_id = get_post_meta( $warranty_id, '_order_id', true );
		$order    = wc_get_order( $order_id );
		if ( $order ) {
			$total = $order->get_total();
		}
	}

	return apply_filters( 'warranty_get_item_amount', $total, $warranty_id );
}

/**
 * Get the number of available requests left for the given product in an order.
 *
 * @param int $order_id WC_Order ID.
 * @param int $product_id WC_Product ID.
 * @param int $idx Index.
 *
 * @return int
 */
function warranty_get_quantity_remaining( $order_id, $product_id, $idx ) {
	$order = wc_get_order( $order_id );
	$items = $order->get_items();
	$qty   = 0;

	if ( isset( $items[ $idx ] ) ) {
		$qty        = $items[ $idx ]['qty'];
		$warranties = warranty_search( $order_id, $product_id, $idx );

		if ( $warranties ) {
			$used = 0;
			foreach ( $warranties as $warranty ) {
				$warranty = warranty_load( $warranty->ID );

				foreach ( $warranty['products'] as $warranty_product ) {
					if ( (int) $warranty_product['order_item_index'] === (int) $idx ) {
						$used += $warranty_product['quantity'];
					}
				}
			}

			$qty -= $used;
		}
	}

	return $qty;
}

/**
 * Opposite to warranty_get_quantity_remaining()
 *
 * @param int $order_id WC_Order ID.
 * @param int $product_id WC_Product ID.
 * @param int $idx Index.
 * @return int
 */
function warranty_count_quantity_used( $order_id, $product_id, $idx ) {
	$order = wc_get_order( $order_id );
	$items = $order->get_items();
	$used  = 0;

	if ( isset( $items[ $idx ] ) ) {
		$warranties = warranty_search( $order_id, $product_id, $idx );

		if ( $warranties ) {
			foreach ( $warranties as $warranty ) {
				$warranty = warranty_load( $warranty->ID );

				foreach ( $warranty['products'] as $warranty_product ) {
					if ( (int) $warranty_product['order_item_index'] === (int) $idx ) {
						$used += $warranty_product['quantity'];
					}
				}
			}
		}
	}

	return $used;
}

/**
 * Searches for warranty requests for a particular item in an order
 *
 * @param int $order_id WC_Order ID.
 * @param int $product_id WC_Product ID.
 * @param int $idx The index in the WC_Order::get_items results.
 *
 * @return array|bool Array of warranties or false if none are found
 */
function warranty_search( $order_id, $product_id = null, $idx = null ) {

	$args = array(
		'post_type'  => 'warranty_request',
		'meta_query' => array(
			array(
				'key'     => '_order_id',
				'value'   => $order_id,
				'compare' => '=',
			),
		),
	);

	$q = new WP_Query( $args );

	$q->query_vars['order_id'] = $order_id;

	if ( $product_id ) {
		$q->query_vars['product_id'] = $product_id;
	}

	if ( $idx ) {
		$q->query_vars['item_index'] = $idx;
	}

	$results = $q->get_posts();

	if ( ! $results ) {
		return false;
	}

	return $results;
}

/**
 * Get all warranty statuses and allow other plugins to add their own
 *
 * @return array
 */
function warranty_get_statuses() {
	$defaults    = WooCommerce_Warranty::$default_statuses;
	$statuses    = get_terms( 'shop_warranty_status', array( 'hide_empty' => false ) );
	$orders      = get_option( 'wc_warranty_status_order', array() );
	$orig_orders = $orders;
	$terms       = array();

	// make sure all statuses are inside $order.
	foreach ( $statuses as $status ) {
		if ( ! in_array( $status->slug, $orders, true ) ) {
			$orders[] = $status->slug;
		}
	}

	if ( $orig_orders !== $orders ) {
		update_option( 'wc_warranty_status_order', $orders );
	}

	foreach ( $orders as $slug ) {
		$term = get_term_by( 'slug', $slug, 'shop_warranty_status' );

		if ( ! $term ) {
			continue;
		}

		$terms[] = $term;
	}

	return apply_filters( 'warranty_statuses', $terms );
}

/**
 * Reset the statuses to the default value
 */
function warranty_reset_statuses() {
	$terms = get_terms(
		'shop_warranty_status',
		array(
			'fields'     => 'ids',
			'hide_empty' => false,
		)
	);
	foreach ( $terms as $value ) {
		wp_delete_term( $value, 'shop_warranty_status' );
	}

	$defaults = WooCommerce_Warranty::$default_statuses;
	$orders   = array();
	$terms    = array();

	foreach ( $defaults as $status ) {
		if ( ! get_term_by( 'name', $status, 'shop_warranty_status' ) ) {
			wp_insert_term( $status, 'shop_warranty_status' );

			$term    = get_term_by( 'name', $status, 'shop_warranty_status' );
			$terms[] = $term->slug;
		}
	}

	update_option( 'wc_warranty_status_order', $terms );
}

/**
 * Update a warranty's status
 *
 * @param int    $warranty_id Warranty ID.
 * @param string $new_status Warranty Status.
 */
function warranty_update_status( $warranty_id, $new_status ) {
	$status_term = get_the_terms( $warranty_id, 'shop_warranty_status' );
	$prev_status = ( ! empty( $status_term[0] ) ) ? $status_term[0]->slug : '';
	wp_set_post_terms( $warranty_id, $new_status, 'shop_warranty_status', false );

	$update = array(
		'ID'            => $warranty_id,
		'post_modified' => current_time( 'mysql' ),
	);
	wp_update_post( $update );

	do_action( 'wc_warranty_status_updated', $warranty_id, $new_status, $prev_status );

	warranty_send_emails( $warranty_id, $new_status, $prev_status );
	warranty_add_order_note( $warranty_id );
}

/**
 * Return Warranty Completed Status object.
 *
 * @return WP_Term|false
 */
function warranty_get_completed_status() {
	foreach ( warranty_get_statuses() as $status ) {
		if ( 'Completed' === $status->name ) {
			return $status;
		}
	}

	return false;
}

/**
 * Get the warranty validity date based on the order date and warranty duration
 *
 * @param string $order_date Order date.
 * @param int    $warranty_duration Warranty duration.
 * @param string $warranty_unit Duration unit (e.g. weeks, months).
 *
 * @return string
 */
function warranty_get_date( $order_date, $warranty_duration, $warranty_unit ) {
	$order_time   = strtotime( $order_date );
	$expired_time = false;

	$order_date = array(
		'month' => gmdate( 'n', $order_time ),
		'day'   => gmdate( 'j', $order_time ),
		'year'  => gmdate( 'Y', $order_time ),
	);

	if ( 'days' === $warranty_unit ) {
		$expired_time = $order_time + $warranty_duration * 86400;
		$expired_date = gmdate( 'Y-m-d', $expired_time ) . ' 23:59:59';
		$expired_time = strtotime( $expired_date );
	} elseif ( 'weeks' === $warranty_unit ) {
		$add          = ( 86400 * 7 ) * $warranty_duration;
		$expired_time = $order_time + $add;
		$expired_date = gmdate( 'Y-m-d', $expired_time ) . ' 23:59:59';
		$expired_time = strtotime( $expired_date );
	} elseif ( 'months' === $warranty_unit ) {
		$warranty_day   = $order_date['day'];
		$warranty_month = $order_date['month'] + $warranty_duration;
		$warranty_year  = $order_date['year'] + ( $warranty_month / 12 );
		$warranty_month = $warranty_month % 12;

		if ( ( 2 === intval( $warranty_month ) ) && ( $warranty_day > 28 ) ) {
			$warranty_day = 29;
		}

		if ( checkdate( $warranty_month, $warranty_day, $warranty_year ) ) {
			$expired_time = mktime( 23, 59, 59, $warranty_month, $warranty_day, $warranty_year );
		} else {
			$expired_time = mktime( 23, 59, 59, $warranty_month, ( $warranty_day - 1 ), $warranty_year );
		}
	} elseif ( 'years' === $warranty_unit ) {
		$warranty_year = $order_date['year'] + $warranty_duration;

		if ( checkdate( $order_date['month'], $order_date['day'], $warranty_year ) ) {
			$expired_time = mktime( 23, 59, 59, $order_date['month'], $order_date['day'], $warranty_year );
		} else {
			$expired_time = mktime( 23, 59, 59, $order_date['month'], ( $order_date['day'] - 1 ), $warranty_year );
		}
	}

	if ( $expired_time ) {
		return date_i18n( wc_date_format(), $expired_time );
	}

	return '-';
}

/**
 * Add item to Warranty Request
 *
 * @param string|int $request_id Warranty Request ID.
 * @param string|int $product_id WC_Product ID.
 * @param string|int $order_item_index Item index.
 * @param string|int $quantity Item quantity.
 *
 * @return int
 */
function warranty_add_request_item( $request_id, $product_id, $order_item_index = '', $quantity = 1 ) {

	/**
	 * WordPress database access abstraction class.
	 *
	 * @var wpdb $wpdb
	 */
	global $wpdb;

	$item = array(
		'request_id'       => $request_id,
		'product_id'       => $product_id,
		'order_item_index' => $order_item_index,
		'quantity'         => $quantity,
	);

	$wpdb->insert( $wpdb->prefix . 'wc_warranty_products', $item );

	return $wpdb->insert_id;
}

/**
 * Get Warranty Request items.
 *
 * @param int $request_id Warranty Request ID.
 * @return array|null
 */
function warranty_get_request_items( $request_id ) {
	/**
	 * WordPress database access abstraction class.
	 *
	 * @var wpdb $wpdb
	 */
	global $wpdb;

	$items = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT *
		FROM {$wpdb->prefix}wc_warranty_products
		WHERE request_id = %d",
			$request_id
		),
		ARRAY_A
	);

	return $items;
}

/**
 * Process a request for warranty
 *
 * @deprecated
 * @param int    $order_id WC_Order ID.
 * @param int    $product_id WC_Product ID.
 * @param int    $idx Item Index.
 * @param string $request_type Request type.
 * @return int $request_id
 */
function warranty_process_request( $order_id, $product_id, $idx = 0, $request_type = 'replacement' ) {
	_deprecated_function( 'warranty_process_request', '1.7', 'warranty_create_request' );

	$data = warranty_request_post_data();

	return warranty_create_request(
		array(
			'type'       => $request_type,
			'order_id'   => $order_id,
			'product_id' => $product_id,
			'index'      => $idx,
			'qty'        => isset( $data['warranty_qty'] ) ? $data['warranty_qty'] : 1,
		)
	);
}

/**
 * Create a new warranty request
 *
 * @param array $args Warranty request data.
 * @return int|false|WP_Error
 */
function warranty_create_request( $args = array() ) {
	$default = array(
		'type'       => 'replacement',
		'order_id'   => 0,
		'product_id' => 0,
		'index'      => '',
		'qty'        => 1,
	);

	$args  = wp_parse_args( $args, $default );
	$order = wc_get_order( $args['order_id'] );

	if ( ! warranty_user_has_access( wp_get_current_user(), $order ) ) {
		return false;
	}

	$warranty = apply_filters(
		'wc_warranty_post_data',
		array(
			'post_content' => '',
			'post_name'    => __( 'RMA Request for Order #', 'wc_warranty' ) . $args['order_id'],
			'post_status'  => 'publish',
			'post_author'  => 1,
			'post_type'    => 'warranty_request',
		),
		$args
	);

	$request_id = wp_insert_post( $warranty );

	if ( is_wp_error( $request_id ) ) {
		return $request_id;
	}

	$metas = array(
		'order_id'     => $args['order_id'],
		'code'         => warranty_generate_rma_code(),
		'request_type' => $args['type'],
	);

	foreach ( $metas as $key => $value ) {
		add_post_meta( $request_id, '_' . $key, $value, true );
	}

	if ( ! is_array( $args['product_id'] ) ) {
		$args['product_id'] = array( $args['product_id'] );
	}

	if ( ! is_array( $args['qty'] ) ) {
		$args['qty'] = array( $args['qty'] );
	}

	if ( ! is_array( $args['index'] ) ) {
		$args['index'] = array( $args['index'] );
	}

	foreach ( $args['product_id'] as $loop => $product_id ) {
		$index = isset( $args['index'][ $loop ] ) ? $args['index'][ $loop ] : '';
		$qty   = isset( $args['qty'][ $index ] ) ? $args['qty'][ $index ] : 1;
		warranty_add_request_item( $request_id, $product_id, $index, $qty );
	}

	do_action( 'wc_warranty_created', $request_id );

	return $request_id;
}

/**
 * Update a warranty request.
 *
 * @param int   $request_id Warranty Request ID.
 * @param array $data Array of data to update.
 * @return bool
 */
function warranty_update_request( $request_id, $data ) {
	if ( ! warranty_load( $request_id ) ) {
		return false;
	}

	if ( isset( $data['status'] ) ) {
		// update the status.
		warranty_update_status( $request_id, $data['status'] );

		if ( get_option( 'warranty_returned_status' ) === $data['status'] ) {
			// Item has already been returned.
			do_action( 'wc_warranty_item_returned', $request_id, $data['status'] );
		}

		unset( $data['status'] );
	}

	$post_fields = array( 'post_content', 'post_name' );
	$post_data   = array();

	foreach ( $post_fields as $post_field ) {
		if ( isset( $data[ $post_field ] ) ) {
			$post_data[ $post_field ] = $data[ $post_field ];
			unset( $data[ $post_field ] );
		}
	}

	if ( ! empty( $post_data ) ) {
		$post_data['ID'] = $request_id;
		wp_update_post( $post_data );
	}

	foreach ( $data as $field => $value ) {
		update_post_meta( $request_id, '_' . $field, $value );
	}

	return true;
}

/**
 * Delete Warranty Request.
 *
 * @param int $request_id Warranty Request ID.
 * @return bool
 */
function warranty_delete_request( $request_id ) {
	global $wpdb;

	$id = absint( $request_id );

	if ( ! warranty_user_has_access( wp_get_current_user(), false, $id ) ) {
		return false;
	}

	wp_delete_post( $id, true );

	// delete from the wc_warranty_products table.
	$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wc_warranty_products WHERE request_id = %d", $id ) );

	return true;
}

/**
 * Loads a warranty request, along with its metadata
 *
 * @param int $request_id Warranty Request ID.
 * @return array|false
 */
function warranty_load( $request_id ) {

	$order    = false;
	$warranty = get_post( $request_id, ARRAY_A );

	if ( 'warranty_request' !== $warranty['post_type'] ) {
		return false;
	}

	$defaults = array(
		'code'                     => '',
		'tracking_code'            => '',
		'tracking_provider'        => '',
		'return_tracking_code'     => '',
		'return_tracking_provider' => '',
		'request_tracking_code'    => '',
		'first_name'               => '',
		'last_name'                => '',
		'email'                    => '',
		'customer_id'              => '',
		'request_type'             => 'replacement',
		'order_id'                 => '',
		'product_id'               => '',
		'product_name'             => '',
	);

	$warranty           = wp_parse_args( $warranty, $defaults );
	$term               = get_the_terms( $request_id, 'shop_warranty_status' );
	$status             = ( ! empty( $term[0] ) ) ? $term[0]->slug : '';
	$warranty['status'] = $status;

	$custom = get_post_custom( $request_id );

	foreach ( $custom as $key => $value ) {
		$clean              = ltrim( $key, '_' );
		$warranty[ $clean ] = $value[0];
	}

	if ( empty( $warranty['first_name'] ) || empty( $warranty['last_name'] ) ) {
		$order = wc_get_order( $warranty['order_id'] );

		if ( $order instanceof WC_Order ) {
			$warranty['first_name']  = $order->get_billing_first_name();
			$warranty['last_name']   = $order->get_billing_last_name();
			$warranty['email']       = $order->get_billing_email();
			$warranty['customer_id'] = $order->get_customer_id();
		}
	}

	if ( empty( $warranty['product_name'] ) && $warranty['product_id'] ) {
		$warranty['product_name'] = warranty_get_product_title( $warranty['product_id'] );
	}

	// warranty products.
	$warranty['products'] = warranty_get_request_items( $request_id );

	if ( isset( $warranty['products'][0]['product_id'] ) ) {
		$warranty['product_id']   = (int) $warranty['products'][0]['product_id'];
		$warranty['product_name'] = get_the_title( $warranty['product_id'] );
	} else {
		$warranty['product_id'] = 0;
	}

	if ( ! warranty_user_has_access( wp_get_current_user(), $order, $request_id ) ) {
		return false;
	}

	return apply_filters( 'warranty_load', $warranty, $request_id );
}

/**
 * Check if current user can access warranty.
 *
 * @param WP_user             $user  WP User.
 * @param WC_Order|false|null $order WC_Order object, false or null.
 * @param int                 $request_id Warranty ID.
 *
 * @return bool
 */
function warranty_user_has_access( $user, $order = false, $request_id = 0 ) {

	$allowed_roles = array( 'administrator', 'shop_manager' );

	if ( array_intersect( $allowed_roles, $user->roles ) ) {
		return true;
	}

	if ( $request_id ) {
		$items = warranty_get_request_items( $request_id );

		foreach ( $items as $item ) {
			if ( isset( $item['order_item_index'] ) && class_exists( 'WC_Product_Vendors_Utils' ) && WC_Product_Vendors_Utils::can_vendor_manage_order_item( $item['order_item_index'], WC_Product_Vendors_Utils::get_user_active_vendor( $user->ID ) ) ) {
				return true;
			}
		}
	}

	if ( ! $order instanceof WC_Order ) {
		return false;
	}

	if ( class_exists( 'WC_Product_Vendors_Utils' ) && ( ! $request_id && WC_Product_Vendors_Utils::can_vendor_access_order( $order->get_id(), WC_Product_Vendors_Utils::get_user_active_vendor( $user->ID ) ) ) ) {
		return true;
	}

	if ( $user->ID === $order->get_customer_id() ) {
		return true;
	}

	return false;
}

/**
 * Get product title.
 *
 * @param int $product_id WC_Product ID.
 * @return string
 */
function warranty_get_product_title( $product_id ) {
	$product = wc_get_product( $product_id );

	if ( ! $product instanceof WC_Product ) {
		return '';
	}

	return $product->get_title();
}

/**
 * Send emails based on status change
 *
 * @uses warranty_variable_replacements()
 * @param int    $request_id Warranty Request ID.
 * @param string $status Status Slug.
 * @param string $prev_status Previous status.
 * @return void
 */
function warranty_send_emails( $request_id, $status, $prev_status = '' ) {
	$emails  = get_option( 'warranty_emails', array() );
	$request = get_post( $request_id );

	if ( empty( $emails ) || ! $request ) {
		return;
	}

	if ( ! isset( $emails[ $status ] ) ) {
		return;
	}

	$mailer      = WC()->mailer();
	$order       = wc_get_order( get_post_meta( $request_id, '_order_id', true ) );
	$admin_email = get_option( 'admin_email' );

	if ( $order ) {
		$customer_email = $order->get_billing_email();
	} else {
		$customer_email = sanitize_email( get_post_meta( $request_id, '_email', true ) );
	}

	if ( class_exists( 'WC_Product_Vendors_Utils' ) ) {
		$products = warranty_get_request_items( $request_id );

		foreach ( $products as $product ) {
			$vendor_id = WC_Product_Vendors_Utils::get_vendor_id_from_product( $product['product_id'] );
			$vendor    = WC_Product_Vendors_Utils::get_vendor_data_by_id( $vendor_id );

			if ( $vendor && ! empty( $vendor['email'] ) ) {
				$admin_email .= $vendor['email'] . ';';
			}
		}
	}

	foreach ( $emails[ $status ] as $email ) {
		if ( empty( $email['from_status'] ) ) {
			$email['from_status'] = 'any';
		}

		$from_status = $email['from_status'];
		$subject     = $email['subject'];
		$message     = $email['message'];

		if ( 'any' !== $from_status && ! empty( $prev_status ) && $from_status !== $prev_status ) {
			continue;
		}

		// Variable replacements.
		$subject = warranty_variable_replacements( $subject, $request_id );
		$message = warranty_variable_replacements( $message, $request_id );

		// wrap message into the template.
		$message          = $mailer->wrap_message( $subject, $message );
		$admin_recipients = empty( $email['admin_recipients'] ) ? $admin_email : $email['admin_recipients'];

		if ( 'customer' === $email['recipient'] ) {
			$mailer->send( $customer_email, $subject, $message );
		} elseif ( 'admin' === $email['recipient'] ) {
			$mailer->send( $admin_recipients, $subject, $message );
		} else {
			// both.
			$mailer->send( $customer_email, $subject, $message );
			$mailer->send( $admin_recipients, $subject, $message );
		}
	}
}

/**
 * Send a tracking request to the customer
 *
 * @param int $request_id Warranty Request ID.
 * @return void
 */
function warranty_send_tracking_request( $request_id ) {
	warranty_update_request( $request_id, array( 'request_tracking_code' => 'y' ) );
	warranty_send_emails( $request_id, 'request_tracking' );
}

/**
 * Return inventory if the store or product's manage inventory is enabled
 *
 * @param int $request_id Warranty Request ID.
 * @return bool|void
 */
function warranty_return_product_stock( $request_id ) {
	$request = warranty_load( $request_id );

	if ( ! $request ) {
		return false;
	}

	$product      = wc_get_product( $request['product_id'] );
	$manage_stock = get_post_meta( $request['product_id'], '_manage_stock', true );

	if ( $product && $product->is_type( 'variation' ) ) {
		$stock = get_post_meta( $request['product_id'], '_stock', true );

		if ( $stock > 0 ) {
			$manage_stock = 'yes';
		}
	}

	if ( 'yes' === $manage_stock ) {
		wc_update_product_stock( $product, $request['qty'], 'increase' );
		return true;
	}
}

/**
 * Give a full or partial refund on the request's product
 *
 * @param int   $request_id Warranty Request ID.
 * @param float $amount If left empty, it will refund the full line item price.
 * @return bool|WP_Error
 */
function warranty_refund_item( $request_id, $amount = 0 ) {
	$request = warranty_load( $request_id );

	if ( ! $request ) {
		return false;
	}

	$order_id = $request['order_id'];
	$order    = wc_get_order( $order_id );

	if ( ! $order ) {
		return false;
	}

	$refund_reason   = __( 'Item Returned', 'wc_warranty' );
	$refunded        = false;
	$refunded_amount = empty( $request['refund_amount'] ) ? 0 : $request['refund_amount'];

	$refund_items = array();
	foreach ( $request['products'] as $product ) {
		$refund_items[ $product['order_item_index'] ] = $product;
	}

	// attempt to process the refund.
	$line_items  = array();
	$order_items = $order->get_items();

	foreach ( $order_items as $line_item_key => $line_item ) {
		if ( ! in_array( $line_item_key, array_keys( $refund_items ) ) ) {
			continue;
		}

		if ( ! $amount ) {
			$amount += $line_item['line_total'];
		}

		$qty = $refund_items[ $line_item_key ]['quantity'];

		$line_items[ $line_item_key ] = array(
			'qty'          => $qty,
			'refund_total' => 0,
			'refund_tax'   => array( $line_item['line_tax'] ),
		);
	}

	$refunded_amount += $amount;

	$refund = wc_create_refund(
		array(
			'amount'     => $amount,
			'reason'     => $refund_reason,
			'order_id'   => $order_id,
			'line_items' => $line_items,
		)
	);

	// attempt to refund automatically through the payment gateway.
	$error        = '';
	$api_refunded = false;
	if ( WC()->payment_gateways() ) {
		$payment_gateways = WC()->payment_gateways->payment_gateways();
	}
	$payment_method = $order->get_payment_method();
	if ( isset( $payment_gateways[ $payment_method ] ) && $payment_gateways[ $payment_method ]->supports( 'refunds' ) ) {
		$api_refunded = true;
		$result       = $payment_gateways[ $payment_method ]->process_refund( $order_id, $amount, $refund_reason );

		if ( is_wp_error( $result ) ) {
			$error = $result;
		} elseif ( ! $result ) {
			$error = new WP_Error( 'wc_refund_error', __( 'Refund failed', 'wc_warranty' ) );
		}
	}

	// Clear transients.
	wc_delete_shop_order_transients( $order_id );

	if ( $api_refunded ) {
		if ( ! is_wp_error( $error ) ) {
			$refunded = true;
		} else {
			if ( $refund && is_a( $refund, 'WC_Order_Refund' ) ) {
				wp_delete_post( $refund->get_id(), true );
			}

			return $error;
		}
	} else {
		$refunded = true;
	}

	if ( $refunded ) {
		$data = array(
			'refunded'      => 'yes',
			'refund_amount' => $refunded_amount,
			'refund_date'   => current_time( 'mysql' ),
		);
		warranty_update_request( $request_id, $data );

		warranty_send_emails( $request_id, 'item_refunded' );
	}

	return $refunded;
}

/**
 * Find and replace variable holders. Used by warranty_send_emails()
 *
 * @param string $input String with placeholders to process.
 * @param int    $request_id Warranty Request ID.
 * @return string|false Parsed $input or false when can't load Warranty Request
 */
function warranty_variable_replacements( $input, $request_id ) {
	$request = warranty_load( $request_id );

	if ( ! $request ) {
		return false;
	}

	$status_term   = get_the_terms( $request_id, 'shop_warranty_status' );
	$status        = ( $status_term ) ? $status_term[0]->name : 'new';
	$order         = wc_get_order( $request['order_id'] );
	$store_url     = home_url();
	$request_url   = esc_url( add_query_arg( 'order', $request['order_id'], get_permalink( get_option( 'woocommerce_warranty_page_id' ) ) ) );
	$form_fields   = get_option( 'warranty_form' );
	$form_inputs   = json_decode( $form_fields['inputs'] );
	$email         = get_post_meta( $request_id, '_email', true );
	$first_name    = get_post_meta( $request_id, '_first_name', true );
	$last_name     = get_post_meta( $request_id, '_last_name', true );
	$coupon_code   = get_post_meta( $request_id, '_coupon_code', true );
	$refund_amount = get_post_meta( $request_id, '_refund_amount', true );

	if ( $order ) {
		$email      = $order->get_billing_email();
		$first_name = $order->get_billing_first_name();
		$last_name  = $order->get_billing_last_name();
	}

	$order_number = $request['order_id'];

	if ( $order ) {
		$order_number = $order->get_order_number();
	}

	// get the products in the request.
	$items         = warranty_get_request_items( $request_id );
	$product_ids   = array();
	$product_names = array();
	foreach ( $items as $item ) {
		$product = wc_get_product( $item['product_id'] );

		$product_ids[] = $product->get_id();

		if ( $product->is_type( 'variation' ) ) {
			$product_names[] = esc_html( wp_strip_all_tags( $product->get_formatted_name() ) );
		} else {
			$product_names[] = $product->get_title();
		}
	}
	$product_names = implode( ', ', $product_names );
	$product_ids   = implode( ', ', $product_ids );

	/**
	 * Deprecated variable : {shipping_code}. Deprecated as it has the same with {customer_shipping_code}.
	 */
	$vars = array(
		'{order_id}',
		'{rma_code}',
		'{shipping_code}',
		'{product_id}',
		'{product_name}',
		'{warranty_status}',
		'{customer_email}',
		'{customer_name}',
		'{customer_shipping_code}',
		'{store_shipping_code}',
		'{warranty_request_url}',
		'{store_url}',
		'{coupon_code}',
		'{refund_amount}',
	);
	$reps = array(
		$order_number,
		$request['code'],
		$request['tracking_code'],
		$product_ids,
		$product_names,
		$status,
		$email,
		$first_name . ' ' . $last_name,
		$request['tracking_code'],
		$request['return_tracking_code'],
		$request_url,
		$store_url,
		$coupon_code,
		$refund_amount,
	);

	$reason_injected = false;
	foreach ( $form_inputs as $form_input ) {
		if ( 'paragraph' === $form_input->type ) {
			continue;
		}

		$key   = $form_input->key;
		$type  = $form_input->type;
		$field = $form_fields['fields'][ $key ];

		$vars[] = '{' . $key . '}';

		$value = get_post_meta( $request_id, '_field_' . $key, true );

		if ( is_array( $value ) ) {
			$value = implode( ',<br/>', $value );
		}

		if ( 'file' === $type && ! empty( $value ) ) {
			$value = WooCommerce_Warranty::get_uploaded_file_anchor_tag( $value, 'customer' );
		}

		if ( empty( $value ) && ! empty( $item['reason'] ) && ! $reason_injected ) {
			$value           = $item['reason'];
			$reason_injected = true;
		}

		if ( ! $value ) {
			$value = '';
		}

		$reps[] = $value;
	}

	$defaults = array(
		'fields' => array(),
		'inputs' => '',
	);
	$form     = get_option( 'warranty_form', $defaults );

	$inputs = array();
	if ( ! empty( $form['inputs'] ) ) {
		$inputs = json_decode( $form['inputs'] );
	}

	$custom_vars = array();
	foreach ( $inputs as $input_field ) {
		$key = $input_field->key;

		if ( empty( $form['fields'][ $key ]['name'] ) ) {
			continue;
		}

		$var           = $form['fields'][ $key ]['name'];
		$sanitized_key = str_replace( '-', '_', sanitize_title( strtolower( $var ) ) );
		$value         = get_post_meta( $request_id, '_field_' . $key, true );

		if ( ! $value ) {
			$value = '';
		}

		if ( is_array( $value ) ) {
			$value = implode( ', ', $value );
		}

		if ( 'file' === $input_field->type && ! empty( $value ) ) {
			$value = WooCommerce_Warranty::get_uploaded_file_anchor_tag( $value, 'customer' );
		}

		$vars[] = '{' . $sanitized_key . '}';
		$reps[] = $value;

	}

	$input = str_replace( $vars, $reps, $input );

	return apply_filters( 'warranty_variable_replacements', $input, $request_id );
}

/**
 * Generate a unique RMA code
 *
 * @return string $code
 */
function warranty_generate_rma_code() {
	// RMA Code Format.
	$rma_start = absint( get_option( 'warranty_rma_start', 0 ) );
	$last      = get_option( 'warranty_last_rma', 1 );
	$length    = get_option( 'warranty_rma_length', 1 );
	$prefix    = get_option( 'warranty_rma_prefix', '' );
	$suffix    = get_option( 'warranty_rma_suffix', '' );

	if ( $rma_start > $last ) {
		$last = $rma_start - 1;
	}

	$vars = array( '{DD}', '{MM}', '{YYYY}', '{YY}' );
	$reps = array( gmdate( 'd' ), gmdate( 'm' ), gmdate( 'Y' ), gmdate( 'Y' ) );

	if ( ! empty( $prefix ) ) {
		$prefix = str_replace( $vars, $reps, $prefix );
	}

	if ( ! empty( $suffix ) ) {
		$suffix = str_replace( $vars, $reps, $suffix );
	}

	$code = (int) $last + 1;

	update_option( 'warranty_last_rma', $code );

	if ( $length > strlen( $code ) ) {
		$pad  = $length - strlen( $code );
		$code = str_repeat( '0', $pad ) . '' . $code;
	}

	$code = $prefix . $code . $suffix;

	return $code;
}

/**
 * Add an order note
 *
 * @param int $warranty_id Warranty Request ID.
 * @return bool
 */
function warranty_add_order_note( $warranty_id ) {
	$warranty_statuses       = warranty_get_statuses();
	$warranty_status_options = array();

	$term   = wp_get_post_terms( $warranty_id, 'shop_warranty_status' );
	$status = $term[0];

	$order_id = get_post_meta( $warranty_id, '_order_id', true );

	if ( ! $order_id ) {
		return false;
	}

	foreach ( $warranty_statuses as $warranty_status ) {
		$warranty_status_options[] = $warranty_status->slug;
	}

	$order_status_triggers = get_option( 'warranty_request_order_note_statuses', $warranty_status_options );

	if ( ! in_array( $status->slug, $order_status_triggers ) ) {
		return false;
	}

	$order = wc_get_order( $order_id );

	if ( ! $order ) {
		return false;
	}

	$rma_url = admin_url( 'admin.php?page=warranties&s=' . get_post_meta( $warranty_id, '_code', true ) );
	// translators: 1: warranty url, 2: warranty id, 3: status.
	$note = sprintf( __( '<a href="%1$s">RMA #%2$d</a> status changed to %3$s', 'wc_warranty' ), $rma_url, $warranty_id, $status->name );

	$order->add_order_note( $note );
	return true;
}

/**
 * Get tracking data.
 *
 * @param int $request_id Warranty Request ID.
 * @return array|false
 */
function warranty_get_tracking_data( $request_id ) {
	$tracking = array();
	$request  = warranty_load( $request_id );

	if ( ! $request ) {
		return false;
	}

	if ( ! empty( $request['tracking_code'] ) && ! empty( $request['tracking_provider'] ) ) {
		$all_providers = array();

		foreach ( WooCommerce_Warranty::$providers as $providers ) {
			foreach ( $providers as $provider => $format ) {
				$all_providers[ sanitize_title( $provider ) ] = $format;
			}
		}

		$provider             = $request['tracking_provider'];
		$link                 = $all_providers[ $provider ];
		$link                 = str_replace( '%1$s', $request['tracking_code'], $link );
		$link                 = str_replace( '%2$s', '', $link );
		$tracking['customer'] = '<a href="' . $link . '" target="_blank">' . __( 'Track Shipment', 'wc_warranty' ) . '</a>';

	}

	if ( ! empty( $request['return_tracking_code'] ) && ! empty( $request['return_tracking_provider'] ) ) {
		$all_providers = array();

		foreach ( WooCommerce_Warranty::$providers as $providers ) {
			foreach ( $providers as $provider => $format ) {
				$all_providers[ sanitize_title( $provider ) ] = $format;
			}
		}

		$provider          = $request['return_tracking_provider'];
		$link              = $all_providers[ $provider ];
		$link              = str_replace( '%1$s', $request['return_tracking_code'], $link );
		$link              = str_replace( '%2$s', '', $link );
		$tracking['store'] = '<a href="' . $link . '" target="_blank">' . __( 'Track Shipment', 'wc_warranty' ) . '</a>';

	} else {
		if ( ! empty( $request['tracking_code'] ) ) {
			$tracking['customer'] = $request['tracking_code'];
		}

		if ( ! empty( $request['return_tracking_code'] ) ) {
			$tracking['store'] = $request['return_tracking_code'];
		}
	}

	return $tracking;
}

/**
 * Check if the store allows refund requests
 *
 * @return bool
 */
function warranty_refund_requests_enabled() {
	$enabled = get_option( 'warranty_enable_refund_requests', 'no' );

	return ( 'yes' === $enabled );
}

/**
 * Check if the store allows coupon requests
 *
 * @return bool
 */
function warranty_coupon_requests_enabled() {
	$enabled = get_option( 'warranty_enable_coupon_requests', 'no' );

	return ( 'yes' === $enabled );
}

/**
 * Check if the given order has any warranty requests
 *
 * @param int $order_id WC_Order ID.
 * @return bool
 */
function warranty_order_has_warranty_requests( $order_id ) {
	$query = new WP_Query(
		array(
			'post_type'  => 'warranty_request',
			'meta_query' => array(
				array(
					'key'   => '_order_id',
					'value' => $order_id,
				),
			),
		)
	);

	return $query->have_posts();
}

/**
 * Get sanitized $_REQUEST data.
 *
 * @return array
 */
function warranty_request_data() {
	static $data;

	if ( ! $data ) {
		$data = wc_clean( wp_unslash( $_REQUEST ) );
	}

	return $data;
}

/**
 * Sanitize post data.
 *
 * @param Array $posts Post data.
 *
 * @return Array.
 */
function warranty_post_data_cleaner( $posts ) {
	$unslashed_posts = wp_unslash( $posts );

	foreach ( $unslashed_posts as $post_key => $post_data ) {
		// Sanitize the form builder fields differently.
		if ( 'fb_field' === $post_key && 'form' === $unslashed_posts['tab'] ) {
			foreach ( $post_data as $field_key => $field_data ) {
				array_walk(
					$unslashed_posts[ $post_key ][ $field_key ],
					function( &$value, $key ) {
						if ( 'options' === $key || 'text' === $key ) {
							$value = sanitize_textarea_field( $value );
						} else {
							$value = sanitize_text_field( $value );
						}
					}
				);
			}

			// Sanitize the message in the emails settings differently.
		} elseif ( 'message' === $post_key && 'emails' === $unslashed_posts['tab'] ) {
			$unslashed_posts[ $post_key ] = array_map(
				function( $value ) {
					return sanitize_textarea_field( $value );
				},
				$post_data
			);
		} else {
			$unslashed_posts[ $post_key ] = wc_clean( $post_data );
		}
	}

	return $unslashed_posts;
}

/**
 * Get sanitized $_POST data.
 *
 * @return array
 */
function warranty_request_post_data() {
	static $data;

	if ( ! $data ) {
		$data = warranty_post_data_cleaner( $_POST );
	}

	return $data;
}

/**
 * Get sanitized $_GET data.
 *
 * @return array
 */
function warranty_request_get_data() {
	static $data;

	if ( ! $data ) {
		$data = wc_clean( wp_unslash( $_GET ) );
	}

	return $data;
}
