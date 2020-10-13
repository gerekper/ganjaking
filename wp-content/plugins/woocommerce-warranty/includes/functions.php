<?php

function warranty_get_variation_string( $order, $item ) {
	$product = $order->get_product_from_item( $item );
	$formatted_meta = array();
	if ( ! empty( $item['item_meta_array'] ) ) {
		foreach ( $item['item_meta_array'] as $meta_id => $meta ) {
			if ( "" === $meta->value || is_serialized( $meta->value ) || substr( $meta->key, 0, 1 ) == '_' ) {
				continue;
			}

			$attribute_key = urldecode( str_replace( 'attribute_', '', $meta->key ) );

			// If this is a term slug, get the term's nice name
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
			$output .= implode( "<br/>", $meta_list );
		}
	}

	return $output;
}

function warranty_return_shipping_tracking_code_form( $request ) {
	ob_start();
	?>
	<div class="warranty-tracking-container">

		<?php
		if ( $request['request_tracking_code'] == 'y' ) {
			if (! empty($request['tracking_code']) ) {
				// Tracking code provided - show tracking link
				?>
				<div class="customer-tracking-code-container">
					<b><?php _e('Track Your Package', 'wc_warranty'); ?></b>
					<?php
					if ( !empty($request['tracking_provider']) ) {
						$all_providers  = array();
						$providers_name = array();

						foreach ( WooCommerce_Warranty::get_providers() as $providers ) {
							foreach ( $providers as $provider => $format ) {
								$all_providers[sanitize_title( $provider )] = $format;
								$providers_name[sanitize_title( $provider )] = $provider;
							}
						}

						$provider   = $request['tracking_provider'];
						$link       = $all_providers[$provider];
						$link       = str_replace('%1$s', $request['tracking_code'], $link);
						$link       = str_replace('%2$s', '', $link);

						echo '<ul class="warranty-data">';
						echo '<li>'. __('Shipped via', 'wc_warranty') .' '. $providers_name[$provider] .'</li>';
						echo '<li>'. __('Tracking Code:', 'wc_warranty') .' '. $request['tracking_code'] .' <br/> (<a href="'. $link .'" target="_blank">'. __('Track Shipment', 'wc_warranty') .'</a>)</li>';
						echo '</ul>';
					} else {
						echo '<p>'. __('Tracking Number:', 'wc_warranty') .' '. $request['tracking_code'] .'</p>';
					}
					?>
				</div>
				<?php
			}
		}

		if (! empty($request['return_tracking_code']) ) {
			if ( !empty($request['return_tracking_provider']) ) {
			?>
			<div class="store-tracking-code-container">
				<b><?php _e('Shipment Tracking Code', 'wc_warranty'); ?></b>
				<?php
				$all_providers  = array();
				$providers_name = array();

				foreach ( WooCommerce_Warranty::$providers as $providers ) {
					foreach ( $providers as $provider => $format ) {
						$all_providers[sanitize_title( $provider )] = $format;
						$providers_name[sanitize_title( $provider )] = $provider;
					}
				}

				$provider   = $request['return_tracking_provider'];
				$link       = $all_providers[$provider];
				$link       = str_replace('%1$s', $request['return_tracking_code'], $link);
				$link       = str_replace('%2$s', '', $link);
				?>
				<ul class="warranty-data">
					<li><?php printf( __('Shipped via %s', 'wc_warranty'), $providers_name[$provider] ); ?></li>
					<li><?php printf( __('Tracking Code: %s &mdash; %s', 'wc_warranty'), $request['return_tracking_code'], '<a href="'. $link .'" target="_blank">'. __('Track Return Shipment', 'wc_warranty') .'</a>'); ?></li>
				</ul>
			</div>
				<?php
		} else {
			echo '<p>'. __('Your Tracking Number is:', 'wc_warranty') .' '. $request['return_tracking_code'] .'</p>';
		}
	}
	?>
	</div>
	<?php

	return ob_get_clean();
}

function warranty_request_shipping_tracking_code_form( $request ) {
	ob_start();
	?>
	<div class="warranty-tracking-code-container">
		<strong><?php _e('Shipping Tracking Code', 'wc_warranty'); ?></strong>

		<p class="form-field">
			<label class="form-label" for="shipping_provider_<?php echo $request['ID']; ?>">
				<?php _e('Shipping Provider', 'wc_warranty'); ?>
			</label>
			<select class="tracking_provider" name="tracking_provider" id="shipping_provider_<?php echo $request['ID']; ?>" data-request_id="<?php echo $request['ID']; ?>">
				<?php
				foreach ( WooCommerce_Warranty::get_providers() as $provider_group => $providers ) {
					echo '<optgroup label="' . $provider_group . '">';
					foreach ( $providers as $provider => $url ) {
						$selected = (sanitize_title($provider) == $request['tracking_provider']) ? 'selected' : '';
						echo '<option value="' . sanitize_title( $provider ) . '" '. $selected .'>' . $provider . '</option>';
					}
					echo '</optgroup>';
				}
				?>
			</select>
		</p>

		<p class="form-field">
			<label class="form-label" for="tracking_code_<?php echo $request['ID']; ?>">
				<?php _e('Tracking Code', 'wc_warranty'); ?>
			</label>
			<br/>
			<input type="text" class="tracking_code" name="tracking_code" id="tracking_code_<?php echo $request['ID']; ?>" value="<?php echo $request['tracking_code']; ?>" data-request_id="<?php echo $request['ID']; ?>" />
		</p>

		<input type="hidden" name="action" value="set_tracking_code" />
		<input type="hidden" name="request_id" value="<?php echo $request['ID']; ?>" />
		<input type="submit" name="send" value="<?php _e('Add Tracking Code', 'wc_warranty'); ?>" class="button" />
	</div>
	<?php
	return ob_get_clean();
}

function warranty_stripslashes( $value ) {
	return stripslashes( $value );
}

function warranty_get_order_item_warranty( $item ) {
	$meta       = $item['item_meta'];
	$warranty   = array('type' => 'no_warranty');

	foreach ( $meta as $key => $value ) {
		$value = version_compare( WC_VERSION, '3.0', '<' ) ? stripslashes( $value[0] ) : $value;

		( is_array( $value ) ) ? array_walk_recursive( $value, 'warranty_stripslashes' ) : $value = stripslashes( $value );

		if ( $key == '_item_warranty' ) {
			$warranty = array_merge( $warranty, (array) maybe_unserialize( $value ) );
		} elseif ( $key == '_item_warranty_selected' ) {
			$warranty['warranty_idx'] = $value;
		}
	}

	return $warranty;
}

function warranty_get_warranty_duration_string( $warranty, $order ) {
	if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
		$completed = get_post_meta( $order->id, '_completed_date', true);
	} else {
		$completed = $order->get_date_completed() ? $order->get_date_completed()->date( 'Y-m-d H:i:s' ) : false;
	}

	if ( empty($completed) ) {
		$completed = false;
	}

	/*
	 * If there is a default warranty set, and we're instructed to
	 * override with the default, lets use the default warranty.
	 */
	$maybe_override = get_option( 'warranty_override_all' );
	$default_warranty = warranty_get_default_warranty();
	if (
		'yes' == $maybe_override &&
		'no_warranty' == $warranty['type'] &&
		'no_warranty' != $default_warranty['type']
	) {
		$warranty = $default_warranty;
	}

	if ( $warranty['type'] == 'no_warranty' ) {
		$warranty_string = __('Product has no warranty', 'wc_warranty');
	} elseif ( $warranty['type'] == 'included_warranty' ) {
		if ( $warranty['length'] == 'lifetime' ) {
			$warranty_string = __('Lifetime', 'wc_warranty');
		} else {
			$order_date         = ($completed) ? $completed : WC_Warranty_Compatibility::get_order_prop( $order, 'order_date' );
			$warranty_string    = __('Expiry Date: ', 'wc_warranty') . warranty_get_date($order_date, $warranty['value'], $warranty['duration']);
		}
	} elseif ( $warranty['type'] == 'addon_warranty' ) {
		$idx                = $warranty['warranty_idx'];
		$warranty_string    = '';

		if ( isset( $warranty['addons'][ $idx ] ) ) {
			$addon              = $warranty['addons'][ $idx ];
			$order_date         = ($completed) ? $completed : WC_Warranty_Compatibility::get_order_prop( $order, 'order_date' );
			$warranty_string    = __('Expiry Date: ', 'wc_warranty') . warranty_get_date($order_date, $addon['value'], $addon['duration']);
		}
	}

	return $warranty_string;
}

/**
 * Get a product's warranty details
 *
 * @param int $product_id Product or variation ID
 * @return array
 */
function warranty_get_product_warranty( $product_id ) {
	$product = wc_get_product( $product_id );

	if ( $product && $product->is_type( 'variation' ) ) {
		if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
			$parent_product_id = $product->parent->id;
		} else {
			$parent_product_id = $product->get_parent_id();
		}

		if ( 'parent' == get_post_meta( $parent_product_id, '_warranty_control', true ) ) {
			$warranty = get_post_meta( $parent_product_id, '_warranty', true );
		} else {
			$warranty = get_post_meta( $product_id, '_warranty', true );
		}
	} else {
		$warranty = get_post_meta( $product_id, '_warranty', true );
	}

	if ( !$warranty ) {
		$category_warranties = get_option( 'wc_warranty_categories', array() );
		$categories = wp_get_object_terms( $product_id, 'product_cat' );

		if ( !is_wp_error( $categories ) ) {
			foreach ( $categories as $category ) {

				if ( !empty( $category_warranties[ $category->term_id ] ) ) {
					$warranty = $category_warranties[ $category->term_id ];
					break;
				}

			}
		}
	}

	if ( !$warranty ) {
		$warranty   = warranty_get_default_warranty();
	}

	if ( empty( $warranty ) ) {
		$warranty = array(
			'type'  => 'no_warranty'
		);
	}

	if ( empty( $warranty['label'] ) ) {
		$warranty['label'] = get_post_meta( $product_id, '_warranty_label', true );
	}

	return apply_filters( 'get_product_warranty', $warranty, $product_id );
}

/**
 * Get the formatted warranty string of the given product
 *
 * @param int   $product_id
 * @param array $warranty
 * @return string
 */
function warranty_get_warranty_string( $product_id = 0, $warranty = null ) {
	if ( $product_id ) {
		$warranty = warranty_get_product_warranty( $product_id );
	}

	if ( empty( $warranty ) || $warranty['type'] == 'no_warranty' ) {
		$string = __('No warranty', 'wc_warranty');
	} elseif ( $warranty['type'] == 'included_warranty' ) {
		if ( $warranty['length'] == 'lifetime' ) {
			$string = __('Lifetime Warranty Included', 'wc_warranty');
		} else {
			$duration = warranty_duration_i18n( $warranty['duration'], $warranty['value'] );

			$string = sprintf( __('Warranty Included (%d %s)', 'wc_warranty'), $warranty['value'], $duration );
		}
	} else {
		$string = 'Add-on Warranty: ';
		foreach ( (array)$warranty['addons'] as $addon ) {
			$duration   = warranty_duration_i18n( $addon['duration'], $addon['value'] );
			$amount     = wc_price( $addon['amount'] );
			$string     .= sprintf( __('%d %s for %s; ', 'wc_warranty'), $addon['value'], $duration, $amount );
		}
	}

	return apply_filters( 'get_warranty_string', $string, $product_id, $warranty );
}

/**
 * Switch the duration from plural to singular form depending on the passed $value
 *
 * @param string $duration (e.g. weeks, months)
 * @param int $value
 *
 * @return string
 */
function warranty_trim_duration( $duration, $value ) {
	if ( 1 == $value ) {
		$duration = rtrim( $duration, 's' );
	}

	return $duration;
}

/**
 * Get the singular or plural form of the given duration.
 *
 * @param string $duration
 * @param int $value
 * @return string
 */
function warranty_duration_i18n( $duration, $value = 0 ) {
	$units_i18n = array(
		'day'      => __('Day', 'wc_warranty'),
		'days'     => __('Days', 'wc_warranty'),
		'week'     => __('Week', 'wc_warranty'),
		'weeks'    => __('Weeks', 'wc_warranty'),
		'month'    => __('Month', 'wc_warranty'),
		'months'   => __('Months', 'wc_warranty'),
		'year'     => __('Year', 'wc_warranty'),
		'years'    => __('Years', 'wc_warranty')
	);

	$duration = warranty_trim_duration( $duration, $value );

	return isset( $units_i18n[ $duration ] ) ? $units_i18n[ $duration ] : $duration;
}

/**
 * Get the default warranty settings
 * @return array
 */
function warranty_get_default_warranty() {
	$warranty = array(
		'type'              => get_option( 'warranty_default_type', 'no_warranty' ),
		'label'             => get_option( 'warranty_default_label', '' ),
		'length'            => get_option( 'warranty_default_length', 'lifetime' ),
		'value'             => get_option( 'warranty_default_length_value', 0 ),
		'duration'          => get_option( 'warranty_default_length_duration', 'days' ),
		'no_warranty_option'=> get_option( 'warranty_default_addon_no_warranty', 'no' ),
		'addons'            => get_option( 'warranty_default_addons', array() ),
		'default'           => true
	);

	return apply_filters( 'get_default_warranty', $warranty );
}

/**
 * Get the line total order item meta
 *
 * @param int $warranty_id
 * @return float
 */
function warranty_get_item_amount( $warranty_id ) {
	$order_item_key = get_post_meta( $warranty_id, '_index', true );
	$total = 0;

	if ( $order_item_key ) {
		$total =  wc_get_order_item_meta( $order_item_key, '_line_total', true );
	} else {
		$order_id = get_post_meta( $warranty_id, '_order_id', true );
		$order    = wc_get_order( $order_id );
		if ( $order ) {
			$total    = $order->get_total();
		}
	}

	return apply_filters( 'warranty_get_item_amount', $total, $warranty_id );
}

/**
 * Get the number of available requests left for the given product in an order.
 *
 * @param int $order_id
 * @param int $product_id
 * @param int $idx
 *
 * @return int
 */
function warranty_get_quantity_remaining( $order_id, $product_id, $idx ) {
	$order  = wc_get_order( $order_id );
	$items  = $order->get_items();
	$qty    = 0;

	if ( isset($items[$idx]) ) {
		$qty        = $items[$idx]['qty'];
		$warranties = warranty_search( $order_id, $product_id, $idx );

		if ( $warranties ) {
			$used = 0;
			foreach ( $warranties as $warranty ) {
				$warranty = warranty_load( $warranty->ID );

				foreach ( $warranty['products'] as $warranty_product ) {
					if ( $warranty_product['order_item_index'] == $idx ) {
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
 * @param int $order_id
 * @param int $product_id
 * @param int $idx
 * @return int
 */
function warranty_count_quantity_used( $order_id, $product_id, $idx ) {
	$order  = wc_get_order( $order_id );
	$items  = $order->get_items();
	$used   = 0;

	if ( isset($items[$idx]) ) {
		$warranties = warranty_search( $order_id, $product_id, $idx );

		if ( $warranties ) {
			foreach ( $warranties as $warranty ) {
				$warranty = warranty_load( $warranty->ID );

				foreach ( $warranty['products'] as $warranty_product ) {
					if ( $warranty_product['order_item_index'] == $idx ) {
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
 * @param int $order_id
 * @param int $product_id
 * @param int $idx The index in the WC_Order::get_items results
 * @return array|bool Array of warranties or false if none are found
 */
function warranty_search( $order_id, $product_id = null, $idx = null ) {

	$args = array(
		'post_type'     => 'warranty_request',
		'meta_query'    => array(
			array(
				'key'       => '_order_id',
				'value'     => $order_id,
				'compare'   => '='
			)
		)
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
	$defaults   = WooCommerce_Warranty::$default_statuses;
	$statuses   = get_terms( 'shop_warranty_status', array('hide_empty' => false) );
	$orders     = get_option( 'wc_warranty_status_order', array() );
	$orig_orders= $orders;
	$terms      = array();

	// make sure all statuses are inside $order
	foreach ( $statuses as $status ) {
		if (! in_array($status->slug, $orders) ) {
			$orders[] = $status->slug;
		}
	}

	if ( $orig_orders != $orders ) {
		update_option( 'wc_warranty_status_order', $orders );
	}

	foreach ( $orders as $slug ) {
		$term = get_term_by( 'slug', $slug, 'shop_warranty_status' );

		if (! $term ) continue;

		$terms[] = $term;
	}

	return apply_filters( 'warranty_statuses', $terms );
}

/**
 * Reset the statuses to the default value
 */
function warranty_reset_statuses() {
	$terms = get_terms( 'shop_warranty_status', array( 'fields' => 'ids', 'hide_empty' => false ) );
	foreach ( $terms as $value ) {
		wp_delete_term( $value, 'shop_warranty_status' );
	}

	$defaults   = WooCommerce_Warranty::$default_statuses;
	$orders     = array();
	$terms      = array();

	foreach ( $defaults as $status ) {
		if ( ! get_term_by( 'name', $status, 'shop_warranty_status' ) ) {
			wp_insert_term( $status, 'shop_warranty_status' );

			$term = get_term_by( 'name', $status, 'shop_warranty_status' );
			$terms[] = $term->slug;
		}
	}

	update_option( 'wc_warranty_status_order', $terms );
}

/**
 * Update a warranty's status
 * @param int $warranty_id
 * @param string $new_status
 */
function warranty_update_status( $warranty_id, $new_status ) {
	$status_term    = get_the_terms( $warranty_id, 'shop_warranty_status' );
	$prev_status    = (!empty($status_term[0])) ? $status_term[0]->slug : '';
	wp_set_post_terms( $warranty_id, $new_status, 'shop_warranty_status', false );

	$update = array(
		'ID'            => $warranty_id,
		'post_modified' => current_time( 'mysql' )
	);
	wp_update_post( $update );

	do_action( 'wc_warranty_status_updated', $warranty_id, $new_status, $prev_status );

	warranty_send_emails( $warranty_id, $new_status, $prev_status);
	warranty_add_order_note( $warranty_id );
}

function warranty_get_completed_status() {
	foreach ( warranty_get_statuses() as $status ) {
		if ( $status->name == 'Completed' )
			return $status;
	}

	return false;
}

/**
 * Get the warranty validity date based on the order date and warranty duration
 *
 * @param string $order_date
 * @param int $warranty_duration
 * @param string $warranty_unit
 * @return string
 */
function warranty_get_date($order_date, $warranty_duration, $warranty_unit) {
	$order_time     = strtotime($order_date);
	$expired_date   = false;

	$order_date = array(
		'month'     => date('n', $order_time),
		'day'       => date('j', $order_time),
		'year'      => date('Y', $order_time)
	);

	if ($warranty_unit == 'days') {
		$expired_time = $order_time + $warranty_duration*86400;
		$expired_date = date( 'Y-m-d', $expired_time )." 23:59:59";
		$expired_time = strtotime($expired_date);
	} elseif ( $warranty_unit == 'weeks' ) {
		$add = (86400 * 7) * $warranty_duration;
		$expired_time = $order_time + $add;
		$expired_date = date( 'Y-m-d', $expired_time )." 23:59:59";
		$expired_time = strtotime($expired_date);
	} elseif ( $warranty_unit == 'months' ) {
		$warranty_day   = $order_date['day'];
		$warranty_month = $order_date['month'] + $warranty_duration;
		$warranty_year  = $order_date['year'] + ($warranty_month / 12);
		$warranty_month = $warranty_month % 12;

		if (($warranty_month == 2) && ($warranty_day > 28)) $warranty_day = 29;

		if (checkdate($warranty_month, $warranty_day, $warranty_year) ) {
			$expired_time = mktime(23, 59, 59, $warranty_month, $warranty_day, $warranty_year);
		} else {
			$expired_time = mktime(23, 59, 59, $warranty_month, ($warranty_day - 1) , $warranty_year);
		}
	} elseif ( $warranty_unit == 'years' ) {
		$warranty_year = $order_date['year'] + $warranty_duration;

		if (checkdate($order_date['month'], $order_date['day'], $warranty_year) ) {
			$expired_time = mktime(23, 59, 59, $order_date['month'], $order_date['day'], $warranty_year);
		} else {
			$expired_time = mktime(23, 59, 59, $order_date['month'], ($order_date['day'] - 1) , $warranty_year);
		}
	}

	if ( $expired_time) {
		return date_i18n( get_option('date_format'), $expired_time);
	}

	return '-';
}

function warranty_add_request_item( $request_id, $product_id, $order_item_index = '', $quantity = 1 ) {
	global $wpdb;

	$item = array(
		'request_id'        => $request_id,
		'product_id'        => $product_id,
		'order_item_index'  => $order_item_index,
		'quantity'          => $quantity
	);

	$wpdb->insert( $wpdb->prefix .'wc_warranty_products', $item );

	return $wpdb->insert_id;
}

function warranty_get_request_items( $request_id ) {
	global $wpdb;

	$items = $wpdb->get_results( $wpdb->prepare(
		"SELECT *
		FROM {$wpdb->prefix}wc_warranty_products
		WHERE request_id = %d",
		$request_id
	), ARRAY_A );

	return $items;
}

/**
 * Process a request for warranty
 *
 * @deprecated
 * @param int $order_id
 * @param int $product_id
 * @param int $idx
 * @param string $request_type
 * @return int $request_id
 */
function warranty_process_request( $order_id, $product_id, $idx = 0, $request_type = 'replacement' ) {
	_deprecated_function( 'warranty_process_request', '1.7', 'warranty_create_request' );

	return warranty_create_request( array(
		'type'          => $request_type,
		'order_id'      => $order_id,
		'product_id'    => $product_id,
		'index'         => $idx,
		'qty'           => isset($_POST['warranty_qty']) ? $_POST['warranty_qty'] : 1
	) );
}

/**
 * Create a new warranty request
 *
 * @param array $args
 * @return int
 */
function warranty_create_request( $args = array() ) {
	$default = array(
		'type'          => 'replacement',
		'order_id'      => 0,
		'product_id'    => 0,
		'index'         => '',
		'qty'           => 1,
	);

	$args = wp_parse_args( $args, $default );

	$warranty = apply_filters( 'wc_warranty_post_data', array(
		'post_content'  => '',
		'post_name'     => __('RMA Request for Order #', 'wc_warranty') . $args['order_id'],
		'post_status'   => 'publish',
		'post_author'   => 1,
		'post_type'     => 'warranty_request'
	), $args );

	$request_id = wp_insert_post( $warranty );

	$metas = array(
		'order_id'      => $args['order_id'],
		'code'          => warranty_generate_rma_code(),
		'request_type'  => $args['type']
	);

	foreach ( $metas as $key => $value ) {
		add_post_meta( $request_id, '_'.$key, $value, true );
	}

	if ( !is_array( $args['product_id'] ) ) {
		$args['product_id'] = array( $args['product_id'] );
	}

	if ( !is_array( $args['qty'] ) ) {
		$args['qty'] = array( $args['qty'] );
	}

	if ( !is_array( $args['index'] ) ) {
		$args['index'] = array( $args['index'] );
	}

	foreach ( $args['product_id'] as $loop => $product_id ) {
		$index = isset( $args['index'][ $loop ] ) ? $args['index'][ $loop ] : '';
		$qty = isset( $args['qty'][ $index ] ) ? $args['qty'][ $index ] : 1;
		warranty_add_request_item( $request_id, $product_id, $index, $qty );
	}

	do_action( 'wc_warranty_created', $request_id );

	return $request_id;
}

/**
 * Update a warranty request
 *
 * @param int   $request_id
 * @param array $data Array of data to update
 */
function warranty_update_request( $request_id, $data ) {
	if ( isset( $data['status'] ) ) {
		// update the status
		warranty_update_status( $request_id, $data['status'] );

		if ( $data['status'] == get_option('warranty_returned_status') ) {
			// Item has already been returned
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

	if ( !empty( $post_data ) ) {
		$post_data['ID'] = $request_id;
		wp_update_post( $post_data );
	}

	foreach ( $data as $field => $value ) {
		update_post_meta( $request_id, '_'.$field, $value );
	}
}

function warranty_delete_request( $request_id ) {
	global $wpdb;

	$id = absint($request_id);

	wp_delete_post( $id, true );

	// delete from the wc_warranty_products table
	$wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}wc_warranty_products WHERE request_id = %d", $id) );

	return true;
}

/**
 * Loads a warranty request, along with its metadata
 *
 * @param int $request_id
 * @return array
 */
function warranty_load( $request_id ) {

	$warranty = get_post( $request_id, ARRAY_A );

	if ( $warranty['post_type'] != 'warranty_request' ) {
		return false;
	}

	$defaults = array(
		'code'                      => '',
		'tracking_code'             => '',
		'tracking_provider'         => '',
		'return_tracking_code'      => '',
		'return_tracking_provider'  => '',
		'request_tracking_code'     => '',
		'first_name'                => '',
		'last_name'                 => '',
		'email'                     => '',
		'request_type'              => 'replacement',
		'order_id'                  => '',
		'product_id'                => ''
	);

	$warranty = wp_parse_args( $warranty, $defaults );

	if ( $warranty ) {
		$term   = get_the_terms( $request_id, 'shop_warranty_status' );
		$status = (!empty($term[0])) ? $term[0]->slug : '';
		$warranty['status'] = $status;

		$custom = get_post_custom( $request_id );

		foreach ( $custom as $key => $value ) {
			$clean = ltrim($key, '_');
			$warranty[$clean] = $value[0];
		}

		if ( empty($warranty['first_name']) || empty($warranty['last_name']) ) {
			$order = wc_get_order( $warranty['order_id'] );

			if ( $order ) {
				$warranty['first_name'] = WC_Warranty_Compatibility::get_order_prop( $order, 'billing_first_name' );
				$warranty['last_name']  = WC_Warranty_Compatibility::get_order_prop( $order, 'billing_last_name' );
				$warranty['email']      = WC_Warranty_Compatibility::get_order_prop( $order, 'billing_email' );
			}
		}

		if ( empty( $warranty['product_name'] ) && $warranty['product_id'] ) {
			$warranty['product_name'] = warranty_get_product_title( $warranty['product_id'] );
		}

		// warranty products
		$warranty['products'] = warranty_get_request_items( $request_id );
		$warranty['product_id'] = $warranty['products'][0]['product_id'];
		$warranty['product_name'] = get_the_title( $warranty['product_id'] );
	}

	return apply_filters( 'warranty_load', $warranty, $request_id );
}

function warranty_get_product_title( $product_id ) {
	$product    = wc_get_product( $product_id );
	$title      = get_the_title( $product_id );

	if ( $product && $product->is_type( 'variation' ) ) {
		$title = $product->get_title();
	}

	return $title;
}

/**
 * Send emails based on status change
 *
 * @uses warranty_variable_replacements()
 * @param int $request_id
 * @param string $status Status Slug
 * @param string $prev_status
 */
function warranty_send_emails( $request_id, $status, $prev_status = '' ) {
	global $wpdb, $woocommerce;

	$emails     = get_option( 'warranty_emails', array() );
	$request    = get_post($request_id);

	if ( empty($emails) || !$request ) {
		return;
	}

	if (! isset($emails[$status]) ) {
		return;
	}

	$mailer         = $woocommerce->mailer();
	$order          = wc_get_order( get_post_meta( $request_id, '_order_id', true ) );
	$admin_email    = get_option('admin_email');

	if ( $order ) {
		$customer_email = WC_Warranty_Compatibility::get_order_prop( $order, 'billing_email' );
	} else {
		$customer_email = sanitize_email( get_post_meta( $request_id, '_email', true ) );
	}

	if ( class_exists( 'WC_Product_Vendors_Utils' ) ) {
		$products   = warranty_get_request_items( $request_id );

		foreach ( $products as $product ) {
			$vendor_id  = WC_Product_Vendors_Utils::get_vendor_id_from_product( $product['product_id'] );
			$vendor     = WC_Product_Vendors_Utils::get_vendor_data_by_id( $vendor_id );

			if ( $vendor && !empty( $vendor['email'] ) ) {
				$admin_email .= $vendor['email'] .';';
			}
		}
	}

	foreach ( $emails[$status] as $email ) {
		if ( empty( $email['from_status'] ) ) {
			$email['from_status'] = 'any';
		}

		$from_status= $email['from_status'];
		$subject    = $email['subject'];
		$message    = $email['message'];

		if ( $from_status != 'any' && !empty( $prev_status ) && $from_status != $prev_status ) {
			continue;
		}

		// Variable replacements
		$subject    = warranty_variable_replacements( $subject, $request_id );
		$message    = warranty_variable_replacements( $message, $request_id );

		// wrap message into the template
		$message = $mailer->wrap_message( $subject, $message );
		$admin_recipients = empty( $email['admin_recipients'] ) ? $admin_email : $email['admin_recipients'];

		if ( $email['recipient'] == 'customer' ) {
			$mailer->send( $customer_email, $subject, $message);
		} elseif ( $email['recipient'] == 'admin' ) {
			$mailer->send( $admin_recipients, $subject, $message);
		} else {
			// both
			$mailer->send( $customer_email, $subject, $message);
			$mailer->send( $admin_recipients, $subject, $message);
		}
	}
}

/**
 * Send a tracking request to the customer
 *
 * @param int $request_id
 */
function warranty_send_tracking_request( $request_id ) {
	warranty_update_request( $request_id, array('request_tracking_code' => 'y') );
	warranty_send_emails( $request_id, 'request_tracking' );
}

/**
 * Return inventory if the store or product's manage inventory is enabled
 * @param int $request_id
 */
function warranty_return_product_stock( $request_id ) {
	$request        = warranty_load( $request_id );
	$product        = wc_get_product( $request['product_id'] );
	$manage_stock   = get_post_meta( $request['product_id'], '_manage_stock', true );

	if ( $product && $product->is_type('variation') ) {
		$stock = get_post_meta( $request['product_id'], '_stock', true );

		if ( $stock > 0 )
			$manage_stock = 'yes';
	}

	if ( $manage_stock == 'yes' ) {
		wc_update_product_stock( $product, $request['qty'], 'increase' );
	}
}

/**
 * Give a full or partial refund on the request's product
 * @param int   $request_id
 * @param float $amount If left empty, it will refund the full line item price
 * @return bool|WP_Error
 */
function warranty_refund_item( $request_id, $amount = null ) {
	$request        = warranty_load( $request_id );
	$product_id     = $request['product_id'];
	$qty            = $request['qty'];
	$order_item_key = $request['index'];
	$order_id       = $request['order_id'];
	$product        = wc_get_product( $product_id );
	$order          = wc_get_order( $order_id );
	$refund_reason  = __('Item Returned', 'wc_warranty');
	$refunded       = false;
	$add_notice     = isset( $_REQUEST['add_notice'] ) ? (bool)$_REQUEST['add_notice'] : false;
	$refunded_amount = empty( $request['refund_amount'] ) ? 0 : $request['refund_amount'];

	// attempt to process the refund
	$line_items = array();
	$order_items = $order->get_items();

	foreach ( $order_items as $line_item_key => $line_item ) {
		if ( $order_item_key != $line_item_key )
			continue;

		if ( !$amount ) {
			$amount = $line_item['line_total'];
		}

		$refunded_amount += $amount;

		// Set the qty to 0 if this is only a partial refund
		if ( $refunded_amount < $line_item['line_total'] ) {
			$qty = 0;
		}

		$line_items[ $line_item_key ] = array(
			'qty'           => $qty,
			'refund_total'  => $amount,
			'refund_tax'    => array($line_item['line_tax'])
		);

		break;
	}

	$refund = wc_create_refund( array(
		'amount'     => $amount,
		'reason'     => $refund_reason,
		'order_id'   => $order_id,
		'line_items' => $line_items
	) );

	// attempt to refund automatically through the payment gateway
	$error = '';
	$api_refunded = false;
	if ( WC()->payment_gateways() ) {
		$payment_gateways = WC()->payment_gateways->payment_gateways();
	}
	$payment_method = WC_Warranty_Compatibility::get_order_prop( $order, 'payment_method' );
	if ( isset( $payment_gateways[ $payment_method ] ) && $payment_gateways[ $payment_method ]->supports( 'refunds' ) ) {
		$api_refunded = true;
		$result = $payment_gateways[ $payment_method ]->process_refund( $order_id, $amount, $refund_reason );

		if ( is_wp_error( $result ) ) {
			$error = $result;
		} elseif ( ! $result ) {
			$error = new WP_Error( 'wc_refund_error', __('Refund failed', 'wc_warranty') );
		}
	}

	// Clear transients
	wc_delete_shop_order_transients( $order_id );

	if ( $api_refunded ) {
		if ( !is_wp_error( $error ) ) {
			$refunded = true;
		} else {
			if ( $refund && is_a( $refund, 'WC_Order_Refund' ) ) {
				wp_delete_post( $refund->id, true );
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
			'refund_date'   => current_time('mysql')
		);
		warranty_update_request( $request_id, $data );

		warranty_send_emails( $request_id, 'item_refunded' );
	}
}

/**
 * Find and replace variable holders. Used by warranty_send_emails()
 *
 * @param string $input
 * @param int $request_id
 * @return string Parsed $input
 */
function warranty_variable_replacements( $input, $request_id ) {
	global $wpdb, $woocommerce;

	$request    = warranty_load( $request_id );
	$status_term= get_the_terms( $request_id, 'shop_warranty_status' );
	$status     = ( $status_term ) ? $status_term[0]->name : 'new';
	$order      = wc_get_order( $request['order_id'] );
	$store_url      = home_url();
	$request_url    = esc_url( add_query_arg( 'order', $request['order_id'], get_permalink( get_option("woocommerce_warranty_page_id") ) ) );
	$form_fields    = get_option( 'warranty_form' );
	$form_inputs    = json_decode($form_fields['inputs']);
	$email          = get_post_meta( $request_id, '_email', true );
	$first_name     = get_post_meta( $request_id, '_first_name', true );
	$last_name      = get_post_meta( $request_id, '_last_name', true );
	$coupon_code    = get_post_meta( $request_id, '_coupon_code', true );
	$refund_amount  = get_post_meta( $request_id, '_refund_amount', true );

	if ( $order ) {
		$email      = WC_Warranty_Compatibility::get_order_prop( $order, 'billing_email' );
		$first_name = WC_Warranty_Compatibility::get_order_prop( $order, 'billing_first_name' );
		$last_name  = WC_Warranty_Compatibility::get_order_prop( $order, 'billing_last_name' );
	}

	$order_number = $request['order_id'];

	if ( $order ) {
		$order_number = $order->get_order_number();
	}

	// get the products in the request
	$items = warranty_get_request_items( $request_id );
	$product_ids    = array();
	$product_names  = array();
	foreach ( $items as $item ) {
		$product = wc_get_product( $item['product_id'] );

		$product_ids[] = $product->get_id();

		if ( $product->is_type( 'variation' ) ) {
			$product_names[] = $product->get_formatted_name();
		} else {
			$product_names[] = $product->get_title();
		}
	}
	$product_names = implode( ', ', $product_names );
	$product_ids   = implode( ', ', $product_ids );

	$vars = array(
		'{order_id}', '{rma_code}', '{shipping_code}', '{product_id}', '{product_name}',
		'{warranty_status}',
		'{customer_email}', '{customer_name}', '{customer_shipping_code}', '{store_shipping_code}',
		'{warranty_request_url}', '{store_url}', '{coupon_code}', '{refund_amount}'
	);
	$reps = array(
		$order_number, $request['code'], $request['tracking_code'], $product_ids, $product_names,
		$status, $email, $first_name .' '. $last_name,
		$request['tracking_code'], $request['return_tracking_code'], $request_url, $store_url,
		$coupon_code, $refund_amount
	);

	$reason_injected = false;
	foreach ( $form_inputs as $form_input ) {
		if ( $form_input->type == 'paragraph') continue;

		$key    = $form_input->key;
		$type   = $form_input->type;
		$field = $form_fields['fields'][$key];

		$vars[] = '{'. $key .'}';

		$value = get_post_meta( $request_id, '_field_'.$key, true );

		if ( is_array($value) )
			$value = implode( ',<br/>', $value );

		if ($type == 'file' && !empty($value)) {
			$wp_uploads = wp_upload_dir();
			$value = '<a href="'. $wp_uploads['baseurl'] . $value .'">'. basename($value) .'</a>';
		}

		if ( empty( $value ) && !empty( $item['reason'] ) && !$reason_injected ) {
			$value = $item['reason'];
			$reason_injected = true;
		}

		if (! $value )
			$value = '';

		$reps[] = $value;
	}

	$defaults = array(
		'fields'    => array(),
		'inputs'    => ''
	);
	$form = get_option( 'warranty_form', $defaults );

	$inputs = array();
	if (! empty($form['inputs']) ) {
		$inputs = json_decode($form['inputs']);
	}

	$custom_vars = array();
	foreach ( $inputs as $input_field ) {
		$key = $input_field->key;

		if ( empty( $form['fields'][ $key ]['name'] ) ) {
			continue;
		}

		$var = $form['fields'][$key]['name'];
		$sanitized_key  = str_replace( '-', '_', sanitize_title( strtolower($var) ) );
		$value          = get_post_meta( $request_id, '_field_'. $key, true );

		if (! $value )
			$value = '';

		if ( is_array($value) )
			$value = implode( ', ', $value );

		if ( $input_field->type == 'file' && !empty($value) ) {
			$uploads    = wp_upload_dir();
			$value      = $uploads['baseurl'] . $value;
		}

		$vars[] = '{'. $sanitized_key .'}';
		$reps[] = $value;

	}

	$input = str_replace($vars, $reps, $input);

	return apply_filters( 'warranty_variable_replacements', $input, $request_id );
}

/**
 * Generate a unique RMA code
 *
 * @return string $code
 */
function warranty_generate_rma_code() {
	// RMA Code Format
	$rma_start = absint( get_option( 'warranty_rma_start', 0 ) );
	$last   = get_option( 'warranty_last_rma', 1 );
	$length = get_option( 'warranty_rma_length', 1 );
	$prefix = get_option( 'warranty_rma_prefix', '' );
	$suffix = get_option( 'warranty_rma_suffix', '' );

	if ( $rma_start > $last ) {
		$last = $rma_start - 1;
	}

	$vars   = array( '{DD}', '{MM}', '{YYYY}', '{YY}' );
	$reps   = array( date('d'), date('m'), date('Y'), date('Y') );

	if (! empty($prefix) ) {
		$prefix = str_replace( $vars, $reps, $prefix );
	}

	if (! empty($suffix) ) {
		$suffix = str_replace( $vars, $reps, $suffix );
	}

	$code = (int)$last + 1;

	update_option( 'warranty_last_rma', $code );

	if ( $length > strlen($code) ) {
		$pad    = $length - strlen($code);
		$code   = str_repeat('0', $pad) .''. $code;
	}

	$code = $prefix . $code . $suffix;

	return $code;
}

/**
 * Add an order note
 * @param int $warranty_id
 * @return bool
 */
function warranty_add_order_note( $warranty_id ) {
	$warranty_statuses      = warranty_get_statuses();
	$warranty_status_options= array();

	$term       = wp_get_post_terms( $warranty_id, 'shop_warranty_status' );
	$status     = $term[0];

	$order_id   = get_post_meta( $warranty_id, '_order_id', true );

	if ( !$order_id ) {
		return false;
	}

	foreach ( $warranty_statuses as $warranty_status ) {
		$warranty_status_options[] = $warranty_status->slug;
	}

	$order_status_triggers = get_option( 'warranty_request_order_note_statuses', $warranty_status_options );

	if (! in_array( $status->slug, $order_status_triggers ) ) {
		return false;
	}

	$order = wc_get_order( $order_id );

	if ( !$order ) {
		return false;
	}

	$rma_url = admin_url( 'admin.php?page=warranties&s='. get_post_meta( $warranty_id, '_code', true ) );
	$note = sprintf( __('<a href="%s">RMA #%d</a> status changed to %s', 'wc_warranty'), $rma_url, $warranty_id, $status->name );

	$order->add_order_note( $note );
	return true;
}

function warranty_get_tracking_data( $request_id ) {
	$tracking = array();
	$request  = warranty_load( $request_id );

	if ( ! empty( $request['tracking_code'] ) && ! empty( $request['tracking_provider'] ) ) {
		$all_providers = array();

		foreach ( WooCommerce_Warranty::$providers as $providers ) {
			foreach ( $providers as $provider => $format ) {
				$all_providers[ sanitize_title( $provider ) ] = $format;
			}
		}

		$provider = $request['tracking_provider'];
		$link     = $all_providers[ $provider ];
		$link     = str_replace( '%1$s', $request['tracking_code'], $link );
		$link     = str_replace( '%2$s', '', $link );
		$tracking['customer'] = '<a href="' . $link . '" target="_blank">' . __( 'Track Shipment', 'wc_warranty' ) . '</a>';

	}

	if (! empty($request['return_tracking_code']) && ! empty($request['return_tracking_provider']) ) {
		$all_providers = array();

		foreach ( WooCommerce_Warranty::$providers as $providers ) {
			foreach ( $providers as $provider => $format ) {
				$all_providers[sanitize_title( $provider )] = $format;
			}
		}

		$provider   = $request['return_tracking_provider'];
		$link       = $all_providers[$provider];
		$link       = str_replace('%1$s', $request['return_tracking_code'], $link);
		$link       = str_replace('%2$s', '', $link);
		$tracking['store'] = '<a href="'. $link .'" target="_blank">'. __('Track Shipment', 'wc_warranty') .'</a>';

	} else {
		if (! empty($request['tracking_code']) ) {
			$tracking['customer'] = $request['tracking_code'];
		}

		if (! empty($request['return_tracking_code']) ) {
			$tracking['store'] = $request['return_tracking_code'];
		}
	}

	return $tracking;
}

/**
 * Check if the store allows refund requests
 * @return bool
 */
function warranty_refund_requests_enabled() {
	$enabled = get_option( 'warranty_enable_refund_requests', 'no' );

	return ($enabled == 'yes');
}

/**
 * Check if the store allows coupon requests
 * @return bool
 */
function warranty_coupon_requests_enabled() {
	$enabled = get_option( 'warranty_enable_coupon_requests', 'no' );

	return ($enabled == 'yes');
}

/**
 * Check if the given order has any warranty requests
 *
 * @param int $order_id
 * @return bool
 */
function warranty_order_has_warranty_requests( $order_id ) {
	$query = new WP_Query(array(
		'post_type' => 'warranty_request',
		'meta_query'    => array(
			array(
				'key'   => '_order_id',
				'value' => $order_id
			)
		)
	));

	return $query->have_posts();
}
