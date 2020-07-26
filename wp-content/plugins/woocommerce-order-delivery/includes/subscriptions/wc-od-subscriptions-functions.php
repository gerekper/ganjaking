<?php
/**
 * Useful functions for the integration with the WooCommerce Subscriptions extension.
 *
 * @package WC_OD
 * @since   1.3.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets the subscription instance.
 *
 * @since 1.5.4
 *
 * @param mixed $the_subscription Subscription object or ID.
 * @return false|WC_Subscription The subscription object. False on failure.
 */
function wc_od_get_subscription( $the_subscription ) {
	return ( $the_subscription instanceof WC_Subscription ? $the_subscription : wcs_get_subscription( $the_subscription ) );
}

/**
 * Gets the edit-delivery endpoint for the specified subscription.
 *
 * @since 1.3.0
 *
 * @param int $subscription_id The subscription Id.
 * @return string The edit-delivery endpoint for the subscription.
 */
function wc_od_edit_delivery_endpoint( $subscription_id ) {
	return wc_get_endpoint_url( 'edit-delivery', $subscription_id, wc_get_page_permalink( 'myaccount' ) );
}

/**
 * Gets if the current page is the edit-delivery endpoint or not.
 *
 * @since 1.3.0
 *
 * @global WP_Query $wp_query The WP_Query instance.
 *
 * @return bool True if the current page is the edit-delivery endpoint. False otherwise.
 */
function wc_od_is_edit_delivery_endpoint() {
	global $wp_query;

	return ( is_account_page() && isset( $wp_query->query_vars['edit-delivery'] ) );
}

/**
 * Gets the current subscription ID.
 *
 * @since 1.3.0
 * @since 1.5.0 Also checks the posted data.
 *
 * @global WP_Query $wp_query The WP_Query instance.
 *
 * @return int|false The subscription ID. False on failure.
 */
function wc_od_get_current_subscription_id() {
	global $wp_query;

	$subscription_id = false;

	if ( is_ajax() ) {
		$subscription_id = wc_od_get_posted_data( 'subscription_id', false );
	} elseif ( wc_od_is_edit_delivery_endpoint() ) {
		$subscription_id = (int) $wp_query->query_vars['edit-delivery'];
	} elseif ( wcs_is_view_subscription_page() ) {
		$subscription_id = (int) $wp_query->query_vars['view-subscription'];
	}

	return $subscription_id;
}

/**
 * Gets if the user has the capabilities to work with the subscription delivery or not.
 *
 * @since 1.3.0
 *
 * @param mixed   $the_subscription Post object or post ID of the subscription.
 * @param WP_User $user             Optional. The user to check. Current by default.
 * @return bool True if the user has the capabilities to work with the subscription delivery. False otherwise.
 */
function wc_od_user_has_subscription_delivery_caps( $the_subscription, $user = null ) {
	$subscription = wc_od_get_subscription( $the_subscription );

	if ( ! $subscription ) {
		return false;
	}

	if ( ! $user ) {
		$user = wp_get_current_user();
	}

	$has_caps = user_can( $user, 'view_order', $subscription->get_id() );

	/**
	 * Filter if the user has the capabilities to work with the subscription delivery.
	 *
	 * @since 1.3.0
	 *
	 * @param bool            $has_caps     True if the user has subscription delivery caps. False otherwise.
	 * @param WC_Subscription $subscription The subscription instance.
	 * @param WP_User         $user         Optional. The user to check.
	 */
	return apply_filters( 'wc_od_user_has_subscription_delivery_caps', $has_caps, $subscription, $user );
}

/**
 * Gets if the subscription needs the delivery details or not.
 *
 * @since 1.3.0
 *
 * @param mixed $the_subscription Post object or post ID of the subscription.
 * @return bool True if the subscription needs the delivery details. False otherwise.
 */
function wc_od_subscription_needs_delivery_details( $the_subscription ) {
	$subscription = wc_od_get_subscription( $the_subscription );

	if ( ! $subscription ) {
		return false;
	}

	// On-hold subscriptions still return the _schedule_next_payment date, even if it has expired.
	// The delivery details are disabled until the customer renew the subscription manually.
	$next_payment = $subscription->get_time( 'next_payment', 'site' );

	$needs_details = (
		$subscription->has_status( array( 'active', 'pending' ) ) &&
		$next_payment && $next_payment >= current_time( 'timestamp' )
	);

	if ( $needs_details ) {
		$shipping_method = wc_od_get_order_shipping_method( $subscription );

		if ( 0 === strpos( $shipping_method, 'local_pickup' ) ) {
			$needs_details = wc_string_to_bool( WC_OD()->settings()->get_setting( 'enable_local_pickup' ) );
		} else {
			$needs_details = $subscription->needs_shipping_address();
		}
	}

	/**
	 * Filter if the subscription needs the delivery details.
	 *
	 * @since 1.3.0
	 *
	 * @param bool            $needs_details True if the subscription needs the delivery details. False otherwise.
	 * @param WC_Subscription $subscription  The subscription instance.
	 */
	return apply_filters( 'wc_od_subscription_needs_delivery_details', $needs_details, $subscription );
}

/**
 * Gets if the subscription needs a delivery date or not.
 *
 * @since 1.3.0
 *
 * @param mixed $the_subscription Post object or post ID of the subscription.
 * @return bool True if the subscription needs a delivery date. False otherwise.
 */
function wc_od_subscription_needs_delivery_date( $the_subscription ) {
	$subscription = wc_od_get_subscription( $the_subscription );

	if ( ! $subscription ) {
		return false;
	}

	$needs_date = (
		wc_od_subscription_needs_delivery_details( $subscription ) &&
		'calendar' === WC_OD()->settings()->get_setting( 'checkout_delivery_option' )
	);

	/**
	 * Filter if the subscription needs a delivery date.
	 *
	 * @since 1.3.0
	 *
	 * @param bool            $needs_date   True if the subscription needs a delivery date. False otherwise.
	 * @param WC_Subscription $subscription The subscription instance.
	 */
	return apply_filters( 'wc_od_subscription_needs_delivery_date', $needs_date, $subscription );
}

/**
 * Gets if the subscription has delivery preferences or not.
 *
 * @since 1.3.0
 *
 * @param mixed $the_subscription Post object or post ID of the subscription.
 * @return bool True if the subscription has delivery preferences. False otherwise.
 */
function wc_od_subscription_has_delivery_preferences( $the_subscription ) {
	$subscription = wc_od_get_subscription( $the_subscription );

	if ( ! $subscription ) {
		return false;
	}

	$has_preferences = (
		wc_od_subscription_needs_delivery_date( $subscription ) &&
		'day' !== $subscription->get_billing_period() &&
		$subscription->get_meta( '_delivery_date' ) // Disabled if there is no delivery date.
	);

	/**
	 * Filter if the subscription has delivery preferences.
	 *
	 * @since 1.3.0
	 *
	 * @param bool            $has_preferences True if the subscription has delivery preferences. False otherwise.
	 * @param WC_Subscription $subscription    The subscription instance.
	 */
	return apply_filters( 'wc_od_subscription_has_delivery_preferences', $has_preferences, $subscription );
}

/**
 * Gets the delivery days for a subscription.
 *
 * Returns the available delivery days based on the subscription parameters like the shipping method.
 *
 * @since 1.3.0
 * @since 1.5.0 Returns the subscription delivery days instead of the preferred days by the customer.
 * @since 1.6.0 Returns a WC_OD_Collection_Delivery_Days object.
 *
 * @param mixed $the_subscription Post object or post ID of the subscription.
 * @return WC_OD_Collection_Delivery_Days|false The subscription delivery days. False on failure.
 */
function wc_od_get_subscription_delivery_days( $the_subscription ) {
	$subscription = wc_od_get_subscription( $the_subscription );

	if ( ! $subscription ) {
		return false;
	}

	$shipping_method = wc_od_get_order_shipping_method( $subscription );
	$delivery_days   = wc_od_get_delivery_days();

	foreach ( $delivery_days as $index => $delivery_day ) {
		// Updates the status of the delivery day.
		$status = wc_od_get_delivery_day_status(
			$delivery_day,
			array(
				'subscription'    => $subscription,
				'shipping_method' => $shipping_method,
			),
			'subscription'
		);

		$delivery_day->set_enabled( $status );

		// Updates the allowed time frames of the delivery day.
		$time_frames = wc_od_get_time_frames_for_delivery_day(
			$delivery_day,
			array(
				'subscription'    => $subscription,
				'shipping_method' => $shipping_method,
			),
			'subscription'
		);

		$delivery_day->set_time_frames( $time_frames );
	}

	/**
	 * Filters the subscription delivery days.
	 *
	 * @since 1.3.0
	 * @since 1.6.0 The parameter `$delivery_days` is a WC_OD_Collection_Delivery_Days object.
	 *
	 * @param WC_OD_Collection_Delivery_Days $delivery_days The delivery days.
	 * @param WC_Subscription                $subscription  The subscription instance.
	 */
	return apply_filters( 'wc_od_get_subscription_delivery_days', $delivery_days, $subscription );
}

/**
 * Gets the preferred delivery days for a subscription.
 *
 * @since 1.5.0
 * @since 1.6.0 Returns a WC_OD_Collection_Delivery_Days object.
 *
 * @param mixed $the_subscription Post object or post ID of the subscription.
 * @return WC_OD_Collection_Delivery_Days|false An array with the subscription delivery days. False on failure.
 */
function wc_od_get_subscription_preferred_delivery_days( $the_subscription ) {
	$subscription = wc_od_get_subscription( $the_subscription );

	if ( ! $subscription ) {
		return false;
	}

	$delivery_days  = wc_od_get_subscription_delivery_days( $subscription );
	$preferred_days = $subscription->get_meta( '_delivery_days' );

	if ( ! empty( $preferred_days ) ) {
		// Merge the subscription preferred days with the global delivery days.
		foreach ( $preferred_days as $index => $preferred_day ) {
			$delivery_day = $delivery_days->get( $index );

			// Set the delivery day status only if the global value is enabled.
			if ( $delivery_day->is_enabled() ) {
				$delivery_day->set_enabled( $preferred_day['enabled'] );
			}

			// Restrict the time frames to the preferred by the customer.
			if ( ! empty( $preferred_day['time_frame'] ) ) {
				$time_frame_id = wc_od_parse_time_frame_id( $preferred_day['time_frame'] );

				if ( false !== $time_frame_id ) {
					$time_frames = $delivery_day->get_time_frames()->intersect_keys( array_flip( array( $time_frame_id ) ) );

					$delivery_day->set_time_frames( $time_frames );
				}
			}
		}
	}

	/**
	 * Filters the subscription preferred delivery days.
	 *
	 * @since 1.5.0
	 * @since 1.6.0 The parameter `$delivery_days` is a WC_OD_Collection_Delivery_Days object.
	 *
	 * @param WC_OD_Collection_Delivery_Days $delivery_days The delivery days.
	 * @param WC_Subscription                $subscription  The subscription instance.
	 */
	return apply_filters( 'wc_od_get_subscription_preferred_delivery_days', $delivery_days, $subscription );
}

/**
 * Gets the maximum delivery date for the next order of the specified subscription.
 *
 * The returned date is included in the range.
 *
 * @since 1.5.4
 *
 * @param mixed $the_subscription Subscription object or ID.
 * @return int A timestamp representing the maximum delivery date.
 */
function wc_od_get_subscription_max_delivery_date( $the_subscription ) {
	$subscription = wc_od_get_subscription( $the_subscription );

	$billing_interval = $subscription->get_billing_interval();
	$billing_period   = $subscription->get_billing_period();
	$next_payment     = $subscription->get_time( 'next_payment', 'site' );

	/*
	 * By default, the delivery is restricted to the subscription billing interval.
	 * So, the maximum date is the next payment date + the billing interval. Just the day before renewing the subscription again.
	 */
	$timestamp = strtotime( "+ {$billing_interval} {$billing_period}", $next_payment );

	$max_delivery_days      = WC_OD()->settings()->get_setting( 'max_delivery_days' );
	$max_delivery_timestamp = strtotime( "{$max_delivery_days} days", $next_payment );

	/*
	 * Use the 'max_delivery_days' setting if the subscription is not restricted to its billing interval or if the interval
	 * is greater than the maximum allowed. E.g. A yearly interval against a maximum of 30 days for delivery.
	 */
	if (
		$max_delivery_timestamp < $timestamp ||
		! wc_string_to_bool( WC_OD()->settings()->get_setting( 'subscriptions_limit_to_billing_interval' ) )
	) {
		$timestamp = $max_delivery_timestamp;
	}

	/**
	 * Filters the maximum delivery date for the next order of the specified subscription.
	 *
	 * @since 1.5.4
	 *
	 * @param int             $max_delivery_date The timestamp representing the maximum delivery date.
	 * @param WC_Subscription $subscription      The subscription object.
	 */
	return apply_filters( 'wc_od_get_subscription_max_delivery_date', $timestamp, $subscription );
}

/**
 * Gets the arguments used to calculate the subscription delivery date.
 *
 * @since 1.3.0
 *
 * @param mixed $the_subscription Post object or post ID of the subscription.
 * @return array|false An array with the arguments. False on failure.
 */
function wc_od_get_subscription_delivery_date_args( $the_subscription ) {
	$subscription = wc_od_get_subscription( $the_subscription );

	if ( ! $subscription ) {
		return false;
	}

	$start_timestamp = wc_od_get_subscription_first_delivery_date( $subscription );
	$end_timestamp   = ( wc_od_get_subscription_max_delivery_date( $subscription ) + DAY_IN_SECONDS ); // Non-inclusive.
	$delivery_days   = wc_od_get_subscription_delivery_days( $subscription );

	/**
	 * Filter the arguments used to calculate the subscription delivery date.
	 *
	 * @since 1.3.0
	 * @since 1.5.0 Added `shipping_method` parameter.
	 *
	 * @param array           $args         The arguments
	 * @param WC_Subscription $subscription The subscription instance.
	 */
	return apply_filters(
		'wc_od_subscription_delivery_date_args',
		array(
			'subscription'       => $subscription, // Useful for developers.
			'shipping_method'    => wc_od_get_order_shipping_method( $subscription ),
			'start_date'         => $start_timestamp,
			'end_date'           => $end_timestamp,
			'delivery_days'      => $delivery_days->to_array(),
			'disabled_days_args' => array(
				'subscription' => $subscription, // Useful for developers.
				'type'         => 'delivery',
				'start'        => date( 'Y-m-d', $start_timestamp ),
				'end'          => date( 'Y-m-d', $end_timestamp ),
				'country'      => $subscription->get_shipping_country(),
				'state'        => $subscription->get_shipping_state(),
			),
		),
		$subscription
	);
}

/**
 * Gets if the specified delivery date is valid for the subscription or not.
 *
 * @since 1.3.0
 *
 * @param mixed      $the_subscription Post object or post ID of the subscription.
 * @param string|int $date             The delivery date string or timestamp.
 * @return bool True if the delivery date is a valid date. False otherwise.
 */
function wc_od_validate_subscription_delivery_date( $the_subscription, $date ) {
	$subscription = wc_od_get_subscription( $the_subscription );

	if ( ! $subscription ) {
		return false;
	}

	// Fetch the subscription delivery date arguments.
	$args = wc_od_get_subscription_delivery_date_args( $subscription );

	return wc_od_validate_delivery_date( $date, $args, 'subscription' );
}

/**
 * Gets the first date to ship the order of a subscription.
 *
 * @since 1.3.0
 *
 * @param mixed $the_subscription Post object or post ID of the subscription.
 * @return false|int A timestamp representing the first shipping date. False on failure.
 */
function wc_od_get_subscription_first_shipping_date( $the_subscription ) {
	$subscription = wc_od_get_subscription( $the_subscription );

	if ( ! $subscription ) {
		return false;
	}

	$first_shipping_date = wc_od_get_first_shipping_date(
		array(
			'subscription' => $subscription, // Useful for developers.
			'start_date'   => $subscription->get_time( 'next_payment', 'site' ),
		),
		'subscription'
	);

	return $first_shipping_date;
}

/**
 * Gets the first date to deliver the order of a subscription.
 *
 * @since 1.3.0
 *
 * @param mixed $the_subscription Post object or post ID of the subscription.
 * @return false|int A timestamp representing the delivery date. False on failure.
 */
function wc_od_get_subscription_first_delivery_date( $the_subscription ) {
	$first_shipping_date = wc_od_get_subscription_first_shipping_date( $the_subscription );

	if ( ! $first_shipping_date ) {
		return false;
	}

	$subscription    = wc_od_get_subscription( $the_subscription );
	$shipping_method = wc_od_get_order_shipping_method( $subscription );
	$delivery_days   = wc_od_get_subscription_delivery_days( $subscription );
	$end_timestamp   = ( wc_od_get_subscription_max_delivery_date( $subscription ) + DAY_IN_SECONDS ); // Non-inclusive.

	return wc_od_get_first_delivery_date(
		array(
			'subscription'    => $subscription, // Useful for developers.
			'shipping_date'   => $first_shipping_date,
			'shipping_method' => $shipping_method,
			'delivery_days'   => $delivery_days->to_array(),
			'end_date'        => $end_timestamp,
		),
		'subscription'
	);
}

/**
 * Setups the subscription's delivery preferences.
 *
 * @since 1.5.5
 *
 * @param mixed $the_subscription Subscription object or ID.
 */
function wc_od_setup_subscription_delivery_preferences( $the_subscription ) {
	$subscription = wc_od_get_subscription( $the_subscription );
	$time_frame   = $subscription->get_meta( '_delivery_time_frame' );

	if ( ! $time_frame ) {
		return;
	}

	$search_params  = array_intersect_key( $time_frame, array_flip( array( 'time_from', 'time_to' ) ) );
	$delivery_days  = wc_od_get_subscription_delivery_days( $subscription );
	$preferred_days = array();

	foreach ( $delivery_days as $index => $delivery_day ) {
		$time_frame_id = wc_od_search_time_frame( $delivery_day->get_time_frames(), $search_params );

		$preferred_days[ $index ] = array(
			'enabled'    => $delivery_day->get_enabled(),
			'time_frame' => ( false === $time_frame_id ? '' : 'time_frame:' . $time_frame_id ),
		);
	}

	// Setup the 'delivery_days' based on the order time frame.
	wc_od_update_order_meta( $subscription, '_delivery_days', $preferred_days, true );
	wc_od_delete_order_meta( $subscription, '_delivery_time_frame', true );
}

/**
 * Updates the delivery date for new and renewed subscriptions.
 *
 * @since 1.3.0
 *
 * @param mixed $the_subscription Post object or post ID of the subscription.
 */
function wc_od_update_subscription_delivery_date( $the_subscription ) {
	$subscription = wc_od_get_subscription( $the_subscription );

	if ( ! $subscription ) {
		return;
	}

	$delivery_date = false;

	// Assign a delivery date automatically.
	if ( wc_od_subscription_needs_delivery_date( $subscription ) ) {
		$delivery_timestamp = wc_od_get_subscription_first_delivery_date( $subscription );

		if ( $delivery_timestamp ) {
			// Try to adapt the date to the customer preferences.
			if ( wc_od_subscription_has_delivery_preferences( $subscription ) ) {
				$delivery_days = wc_od_get_subscription_preferred_delivery_days( $subscription );
				$delivery_day  = $delivery_days->get( date( 'w', $delivery_timestamp ) );

				// The first delivery date is not an enabled day by the customer. Try with the next.
				if ( $delivery_day && ! $delivery_day->is_enabled() ) {
					$shipping_method = wc_od_get_order_shipping_method( $subscription );
					$end_timestamp   = ( wc_od_get_subscription_max_delivery_date( $subscription ) + DAY_IN_SECONDS ); // Non-inclusive.

					$next_delivery_date = wc_od_get_next_delivery_date(
						array(
							'subscription'       => $subscription, // Useful for developers.
							'shipping_method'    => $shipping_method,
							'delivery_days'      => $delivery_days->to_array(),
							'delivery_date'      => $delivery_timestamp,
							'end_date'           => $end_timestamp,
							'disabled_days_args' => array(
								'type'    => 'delivery',
								'country' => $subscription->get_shipping_country(),
								'state'   => $subscription->get_shipping_state(),
							),
						),
						'subscription'
					);

					if ( $next_delivery_date ) {
						$delivery_timestamp = $next_delivery_date;
					}
				}
			}

			/**
			 * Filter the delivery date of a subscription.
			 *
			 * @since 1.3.0
			 *
			 * @param int             $delivery_timestamp A timestamp representing the subscription delivery date.
			 * @param WC_Subscription $subscription       The subscription instance.
			 */
			$delivery_timestamp = apply_filters( 'wc_od_update_subscription_delivery_date', $delivery_timestamp, $subscription );

			if ( $delivery_timestamp ) {
				$delivery_date = wc_od_localize_date( $delivery_timestamp, 'Y-m-d' );

				if ( wc_od_update_order_meta( $subscription, '_delivery_date', $delivery_date, true ) ) {
					/**
					 * Fires immediately after updating the delivery date of a subscription.
					 *
					 * @since 1.3.0
					 *
					 * @param int             $delivery_timestamp A timestamp representing the subscription delivery date.
					 * @param WC_Subscription $subscription       The subscription instance.
					 */
					do_action( 'wc_od_subscription_delivery_date_updated', $delivery_timestamp, $subscription );
				}
			} else {
				/**
				 * Fires if there is no delivery date for the subscription.
				 *
				 * @since 1.3.0
				 *
				 * @param WC_Subscription $subscription The subscription instance.
				 */
				do_action( 'wc_od_subscription_delivery_date_not_found', $subscription );
			}
		}
	}

	if ( ! $delivery_date ) {
		// Delete if exists the _delivery_date meta cloned from the previous order.
		wc_od_delete_order_meta( $subscription, '_delivery_date', true );
	}
}

/**
 * Updates the delivery time frame for new and renewed subscriptions.
 *
 * @since 1.5.0
 *
 * @param mixed $the_subscription Post object or post ID of the subscription.
 */
function wc_od_update_subscription_delivery_time_frame( $the_subscription ) {
	$subscription = wc_od_get_subscription( $the_subscription );

	if ( ! $subscription ) {
		return;
	}

	$delivery_date = $subscription->get_meta( '_delivery_date' );
	$time_frame    = false;

	if ( $delivery_date ) {
		// Gets the available time frames for the delivery date.
		$time_frames = wc_od_get_time_frames_for_date(
			$delivery_date,
			array(
				'shipping_method' => wc_od_get_order_shipping_method( $subscription ),
			),
			'subscription'
		);

		// Assign the best delivery time frame for the delivery date.
		if ( ! $time_frames->is_empty() ) {
			$delivery_days = $subscription->get_meta( '_delivery_days' );
			$wday          = date( 'w', wc_od_get_timestamp( $delivery_date ) );

			// Use the preferred delivery time frame.
			if ( $delivery_days && isset( $delivery_days[ $wday ] ) && ! empty( $delivery_days[ $wday ]['time_frame'] ) ) {
				$time_frame = $delivery_days[ $wday ]['time_frame'];
			} else {
				$time_frame = 'time_frame:' . $time_frames->first()->get_id();
			}
		}
	}

	if ( $time_frame ) {
		wc_od_update_order_meta( $subscription, '_delivery_time_frame', $time_frame, true );
	} else {
		wc_od_delete_order_meta( $subscription, '_delivery_time_frame', true );
	}
}

/**
 * Gets the fields for the delivery form of a subscription.
 *
 * @since 1.3.0
 * @since 1.5.0 The `value` parameter in the field arguments is deprecated.
 *
 * @param mixed $the_subscription Post object or post ID of the subscription.
 * @return array|false An array with the delivery fields. False on failure.
 */
function wc_od_get_subscription_delivery_fields( $the_subscription ) {
	$subscription = wc_od_get_subscription( $the_subscription );

	if ( ! $subscription || ! wc_od_subscription_has_delivery_preferences( $subscription ) ) {
		return false;
	}

	$delivery_date = wc_od_get_subscription_delivery_field_value( $subscription, 'delivery_date' );

	$fields = array(
		'next_order_start' => array(
			'type'        => 'wc_od_subscription_section_start',
			'title'       => __( 'Next order', 'woocommerce-order-delivery' ),
			'description' => __( 'This will be the delivery details for the next order.', 'woocommerce-order-delivery' ),
			'class'       => array( 'wc-od-subscription-next-order' ),
		),
		'delivery_date'    => wc_od_get_delivery_date_field_args(
			array(
				'value' => $delivery_date, // Deprecated.
			),
			'subscription'
		),
	);

	if ( $delivery_date ) {
		$choices = wc_od_get_time_frames_choices_for_date(
			$delivery_date,
			array(
				'subscription'    => $subscription,
				'shipping_method' => wc_od_get_order_shipping_method( $subscription ),
			),
			'subscription'
		);

		if ( ! empty( $choices ) ) {
			$delivery_time_frame = wc_od_get_subscription_delivery_field_value( $subscription, 'delivery_time_frame' );

			$fields['delivery_time_frame'] = array(
				'label'    => _x( 'Time frame', 'checkout field label', 'woocommerce-order-delivery' ),
				'type'     => 'select',
				'class'    => array( 'form-row-wide' ),
				'required' => ( 'required' === WC_OD()->settings()->get_setting( 'delivery_fields_option' ) ),
				'options'  => $choices,
				'value'    => $delivery_time_frame, // Deprecated.
			);
		}
	}

	$fields = $fields + array(
		'next_order_end'             => array(
			'type' => 'wc_od_subscription_section_end',
		),
		'delivery_preferences_start' => array(
			'type'        => 'wc_od_subscription_section_start',
			'title'       => __( 'Delivery preferences', 'woocommerce-order-delivery' ),
			'description' => __( 'We will try to adapt the delivery details of the future orders to your preferences.', 'woocommerce-order-delivery' ),
			'class'       => array( 'wc-od-subscription-delivery-preferences' ),
		),
		'delivery_days'              => array(
			'subscription_id' => $subscription->get_id(),
			'type'            => 'wc_od_subscription_delivery_days',
			'label'           => __( 'Delivery days', 'woocommerce-order-delivery' ),
			'value'           => wc_od_get_subscription_delivery_field_value( $subscription, 'delivery_days' ), // Deprecated.
			'required'        => true,
		),
		'delivery_preferences_end'   => array(
			'type' => 'wc_od_subscription_section_end',
		),
	);

	/**
	 * Filters the delivery fields of the subscription.
	 *
	 * @since 1.3.0
	 * @since 1.5.0 Replaced `value` parameter by `default`.
	 *
	 * @param array           $fields       The form fields.
	 * @param WC_Subscription $subscription The subscription instance.
	 */
	return apply_filters( 'wc_od_get_subscription_delivery_fields', $fields, $subscription );
}

/**
 * Gets the delivery field value for the specified subscription.
 *
 * Checks the posted data and the metadata as the last option.
 *
 * @since 1.5.0
 *
 * @param mixed  $the_subscription Post object or post ID of the subscription.
 * @param string $input Input key.
 * @return mixed
 */
function wc_od_get_subscription_delivery_field_value( $the_subscription, $input ) {
	$subscription = wc_od_get_subscription( $the_subscription );

	if ( ! $subscription ) {
		return null;
	}

	$value = wc_od_get_posted_data( $input );

	if ( is_null( $value ) ) {
		$value = wc_od_get_order_meta( $subscription->get_id(), "_{$input}" );
		$value = ( '' === $value ? null : $value );
	}

	/**
	 * Filters the delivery field value for the specified subscription.
	 *
	 * @since 1.5.0
	 *
	 * @param mixed           $value        The input value.
	 * @param WC_Subscription $subscription The subscription instance.
	 * @param string          $input        The input key.
	 */
	return apply_filters( 'wc_od_get_subscription_delivery_field_value', $value, $subscription, $input );
}

/**
 * Gets the HTML content for the subscription section end field.
 *
 * @since 1.5.0
 *
 * @param string $field The field HTML content.
 * @param string $key   The field key.
 * @param array  $args  The field arguments.
 * @return string
 */
function wc_od_subscription_section_start_field( $field, $key, $args ) {
	ob_start();
	echo '<section class="' . esc_attr( implode( ' ', $args['class'] ) ) . '">';

	if ( ! empty( $args['title'] ) ) :
		echo '<h2>' . esc_html( $args['title'] ) . '</h2>';
	endif;

	if ( ! empty( $args['description'] ) ) :
		echo '<p>' . esc_html( $args['description'] ) . '</p>';
	endif;

	return ob_get_clean();
}

/**
 * Gets the HTML content for the subscription section end field.
 *
 * @since 1.5.0
 *
 * @return string
 */
function wc_od_subscription_section_end_field() {
	return '</section>';
}

/**
 * Gets the HTML content for the subscription delivery_days field.
 *
 * @since 1.3.0
 *
 * @param string $field The field HTML content.
 * @param string $key   The field key.
 * @param array  $args  The field arguments.
 * @param mixed  $value The field value.
 * @return string The field content.
 */
function wc_od_subscription_delivery_days_field( $field, $key, $args, $value ) {
	$delivery_days = wc_od_get_subscription_delivery_days( $args['subscription_id'] );
	$week_days     = wc_od_get_week_days();
	$required      = '';

	if ( $args['required'] ) {
		$args['class'][] = 'validate-required';
		$required        = ' <abbr class="required" title="' . esc_attr__( 'required', 'woocommerce-order-delivery' ) . '">*</abbr>';
	}

	$class = array(
		'base'   => 'wc-od-subscription-delivery-days',
		'header' => 'wc-od-subscription-delivery-days__header wc-od-subscription-delivery-days__header',
		'row'    => 'wc-od-subscription-delivery-days__row',
		'cell'   => 'wc-od-subscription-delivery-days__cell wc-od-subscription-delivery-days__cell',
	);

	$columns = array(
		'delivery_day' => _x( 'Delivery day', 'Subscription delivery: column name', 'woocommerce-order-delivery' ),
		'enabled'      => _x( 'Enabled', 'Subscription delivery: column name', 'woocommerce-order-delivery' ),
		'time_frame'   => _x( 'Time frame', 'Subscription delivery: column name', 'woocommerce-order-delivery' ),
	);

	ob_start();
	?>
	<div class="form-row <?php echo esc_attr( implode( ' ', $args['class'] ) ); ?>" id="<?php echo esc_attr( $key ); ?>">
		<label><?php echo esc_html( $args['label'] ) . $required; // WPCS: XSS ok. ?></label>
		<table class="<?php echo esc_attr( $class['base'] ); ?> shop_table shop_table_responsive">
			<thead>
				<tr>
					<th class="<?php echo esc_attr( "{$class['header']}-delivery-day" ); ?>"><?php echo esc_html( $columns['delivery_day'] ); ?></th>
					<th class="<?php echo esc_attr( "{$class['header']}-enabled" ); ?>"><?php echo esc_html( $columns['enabled'] ); ?></th>
					<th class="<?php echo esc_attr( "{$class['header']}-time-frame" ); ?>"><?php echo esc_html( $columns['time_frame'] ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
			foreach ( $delivery_days as $index => $delivery_day ) :
				$enabled = $delivery_day->is_enabled();
				?>
				<tr class="<?php echo esc_attr( $class['row'] ); ?>">
					<td class="<?php echo esc_attr( "{$class['cell']}-delivery-day" ); ?>" data-title="<?php echo esc_attr( $columns['delivery_day'] ); ?>">
						<?php echo esc_html( $week_days[ $index ] ); ?>
					</td>

					<td class="<?php echo esc_attr( "{$class['cell']}-enabled" ); ?>" data-title="<?php echo esc_attr( $columns['enabled'] ); ?>">
						<?php
						$checked = ( $enabled && ( ! isset( $value[ $index ] ) || wc_string_to_bool( $value[ $index ]['enabled'] ) ) );

						printf(
							'<input type="checkbox" name="%1$s" value="1" %2$s %3$s />',
							esc_attr( "{$key}[{$index}][enabled]" ),
							checked( $checked, true, false ),
							disabled( $enabled, false, false )
						);
						?>
					</td>

					<td class="<?php echo esc_attr( "{$class['cell']}-time-frame" ); ?>" data-title="<?php echo esc_attr( $columns['time_frame'] ); ?>">
						<?php
						if ( ! $enabled || ! $delivery_day->has_time_frames() ) :
							echo '-';
						else :
							$choices  = wc_od_get_time_frames_choices( $delivery_day->get_time_frames(), 'subscription' );
							$selected = ( isset( $value[ $index ] ) && isset( $value[ $index ]['time_frame'] ) ? $value[ $index ]['time_frame'] : '' );

							echo '<select class="select" name="' . esc_attr( "{$key}[{$index}][time_frame]" ) . '">';
							foreach ( $choices as $choice => $label ) :
								printf(
									'<option value="%1$s"%2$s>%3$s</option>',
									esc_attr( $choice ),
									selected( $choice, $selected, false ),
									esc_html( $label )
								);
							endforeach;
							echo '</select>';
						endif;
						?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
	$field = ob_get_clean();

	return $field;
}

/**
 * Gets the minimum subscription period in the cart.

 * @since 1.3.0
 *
 * @return array|false An array with the subscription period. False otherwise.
 */
function wc_od_get_min_subscription_period_in_cart() {
	$items = WC()->cart->get_cart();

	$min_period     = false;
	$min_timestamp  = false;
	$timestamp_base = time();

	foreach ( $items as $item ) {
		$product = $item['data'];

		if ( WC_Subscriptions_Product::is_subscription( $product ) && $product->needs_shipping() && ! WC_Subscriptions_Product::needs_one_time_shipping( $product ) ) {
			$period   = WC_Subscriptions_Product::get_period( $product );
			$interval = WC_Subscriptions_Product::get_interval( $product );

			$timestamp = strtotime( "+ {$interval} {$period}", $timestamp_base );
			if ( $timestamp && ( ! $min_timestamp || $min_timestamp > $timestamp ) ) {
				$min_timestamp = $timestamp;
				$min_period    = array(
					'period'   => $period,
					'interval' => $interval,
				);
			}
		}
	}

	return $min_period;
}

/**
 * Prints the delivery preferences in the admin subscription details.
 *
 * @since 1.3.0
 * @since 1.5.0 Updated HTML markup.
 *
 * @param WC_Subscription $subscription The subscription instance.
 */
function wc_od_admin_subscription_delivery_preferences( $subscription ) {
	$week_days      = wc_od_get_week_days();
	$preferred_days = wc_od_get_subscription_preferred_delivery_days( $subscription );

	$columns = array(
		'delivery_day' => _x( 'Delivery day', 'Subscription delivery: column name', 'woocommerce-order-delivery' ),
		'time_frame'   => _x( 'Time frame', 'Subscription delivery: column name', 'woocommerce-order-delivery' ),
	);
	?>
	<table class="wc-od-subscription-delivery-days widefat">
		<thead>
			<tr>
				<th class="delivery-day"><?php echo esc_html( $columns['delivery_day'] ); ?></th>
				<th class="time-frame"><?php echo esc_html( $columns['time_frame'] ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $preferred_days as $index => $delivery_day ) : ?>
				<tr>
					<td class="delivery-day">
						<?php echo esc_html( $week_days[ $index ] ); ?>
					</td>
					<td class="time-frame">
						<?php
						if ( ! $delivery_day->is_enabled() || ! $delivery_day->has_time_frames() ) :
							echo '<span class="na">–</span>';
						else :
							echo wp_kses_post( join( '<br>', array_map( 'wc_od_time_frame_to_string', $delivery_day->get_time_frames()->all() ) ) );
						endif;
						?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php
}
