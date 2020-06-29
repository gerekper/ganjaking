<?php
/**
 * WC_CSP_Condition_Cart_Recurring_Item class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.4.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Recurring in Cart Condition.
 *
 * @class    WC_CSP_Condition_Cart_Reccuring_Item
 * @version  1.6.0
 */
class WC_CSP_Condition_Cart_Recurring_Item extends WC_CSP_Condition {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id                            = 'recurring_item_in_cart';
		$this->title                         = __( 'Recurring Item', 'woocommerce-conditional-shipping-and-payments' );
		$this->supported_global_restrictions = array( 'payment_gateways' );
	}

	/**
	 * Return condition field-specific resolution message which is combined along with others into a single restriction "resolution message".
	 *
	 * @param  array  $data  Condition field data.
	 * @param  array  $args  Optional arguments passed by restriction.
	 * @return string|false
	 */
	public function get_condition_resolution( $data, $args ) {

		$cart_contents = WC()->cart->get_cart();

		if ( empty( $cart_contents ) ) {
			return false;
		}

		$message                    = false;
		$chosen_periods_placeholder = WC_CSP_Condition::merge_titles( $this->get_billing_period_adverb( $data[ 'value' ] ), array( 'rel' => 'or', 'quotes' => false ) );

		if ( $this->modifier_is( $data[ 'modifier' ], array( 'in' ) ) ) {

			if ( sizeof( $data[ 'value' ] ) === 4 ) {
				$message  = __( 'remove all subscription products from your cart', 'woocommerce-conditional-shipping-and-payments' );
			} else {
				$subjects = $this->get_billing_period_adverb( $this->get_condition_violation_subjects( $data, $args ) );
				$message  = sprintf( __( 'remove all %s subscription products from your cart', 'woocommerce-conditional-shipping-and-payments' ), WC_CSP_Condition::merge_titles( $subjects, array( 'rel' => 'and', 'quotes' => false ) ) );
			}

		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'not-in' ) ) ) {

			if ( sizeof( $data[ 'value' ] ) === 4 ) {
				$message = __( 'add some subscription products to your cart', 'woocommerce-conditional-shipping-and-payments' );
			} else {
				$message = sprintf( __( 'add some %s subscription products to your cart', 'woocommerce-conditional-shipping-and-payments' ), $chosen_periods_placeholder );
			}

		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'all-in' ) ) ) {

			if ( sizeof( $data[ 'value' ] ) === 4 ) {
				$message = __( 'make sure that your cart doesn\'t contain only subscription products', 'woocommerce-conditional-shipping-and-payments' );
			} else {
				$message = sprintf( __( 'make sure that your cart doesn\'t contain only %s subscription products', 'woocommerce-conditional-shipping-and-payments' ), $chosen_periods_placeholder );
			}

		} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'not-all-in' ) ) ) {

			if ( sizeof( $data[ 'value' ] ) === 4 ) {
				$message = __( 'make sure that your cart contains only subscription products', 'woocommerce-conditional-shipping-and-payments' );
			} else {
				$message = sprintf( __( 'make sure that your cart contains only %s subscription products', 'woocommerce-conditional-shipping-and-payments' ), $chosen_periods_placeholder );
			}
		}

		return $message;
	}

	/**
	 * Returns a readable form of the subcription periods.
	 *
	 * @since  1.4.0
	 *
	 * @param  array|String  $periods  Periods to format.
	 * @return array|String
	 */
	private function get_billing_period_adverb( $periods ) {

		$return_array = true;

		// Transform type if String is passed.
		if ( ! is_array( $periods ) ) {

			$return_array = false;
			$periods      = array( $periods );
		}

		$mapper = array(
			'day'   => __( 'daily', 'woocommerce-conditional-shipping-and-payments' ),
			'week'  => __( 'weekly', 'woocommerce-conditional-shipping-and-payments' ),
			'month' => __( 'monthly', 'woocommerce-conditional-shipping-and-payments' ),
			'year'  => __( 'yearly', 'woocommerce-conditional-shipping-and-payments' )
		);

		$formatted = array();

		foreach ( $periods as $period ) {
			if ( isset( $mapper[ $period ] ) ) {
				$formatted[] = $mapper[ $period ];
			} else {
				$formatted[] = $period;
			}
		}

		return $return_array ? $formatted : end( $formatted );
	}

	/**
	 * Returns condition violation subjects.
	 *
	 * @since  1.4.0
	 *
	 * @param  array  $data  Condition field data.
	 * @param  array  $args  Optional arguments passed by restriction.
	 * @return array
	 */
	public function get_condition_violation_subjects( $data, $args ) {

		$subjects = array();
		$renewal  = wcs_cart_contains_renewal();

		if ( $renewal ) {

			$subscription_id = (int) $renewal[ 'subscription_renewal' ][ 'subscription_id' ];
			$subscription    = wcs_get_subscription( $subscription_id );
			$billing_period  = $subscription->get_billing_period();

			if ( in_array( $billing_period, $data[ 'value' ] ) ) {
				$subjects[] = $billing_period;
			}

		} else {

			$recurring_carts = WC()->cart->recurring_carts;

			if ( ! empty( $recurring_carts ) ) {

				if ( $this->modifier_is( $data[ 'modifier' ], array( 'in' ) ) ) {

					foreach ( $recurring_carts as $cart_hash => $cart ) {

						$billing_period = wcs_cart_pluck( $cart, 'subscription_period' );

						if ( in_array( $billing_period, $data[ 'value' ] ) ) {
							$subjects[] = $billing_period;
						}
					}
				}
			}
		}

		return array_unique( $subjects );
	}

	/**
	 * Evaluate if the condition is in effect or not.
	 *
	 * @param  array  $data  Condition field data.
	 * @param  array  $args  Optional arguments passed by restriction.
	 * @return boolean
	 */
	public function check_condition( $data, $args ) {

		// Check for change payment action.
		if ( isset( $args[ 'order' ] ) ) {

			$order = $args[ 'order' ];

			if ( ! ( $order instanceof WC_Subscription ) ) {
				return false;
			}

			$billing_period       = $order->get_billing_period();
			$billing_period_match = in_array( $billing_period, $data[ 'value' ] );

			if ( $this->modifier_is( $data[ 'modifier' ], array( 'in', 'all-in' ) ) ) {

				if ( $billing_period_match ) {
					return true;
				}

			} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'not-in', 'not-all-in' ) ) ) {

				if ( ! $billing_period_match ) {
					return true;
				}
			}

		} else {

			$cart_contents = WC()->cart->get_cart();

			if ( empty( $cart_contents ) ) {
				return false;
			}

			// Search for Renewal items.
			// Note: A Renewal item can't co-exist with a Subcription item in the same cart.
			$renewal = wcs_cart_contains_renewal();

			if ( $renewal ) {

				$matching_item                   = false;
				$all_items_matching              = true;
				$billing_period_match            = false;
				$contains_non_subscription_items = false;

				// Fetch Subcription and renewal's billing period.
				$subscription_id = (int) $renewal[ 'subscription_renewal' ][ 'subscription_id' ];
				$subscription    = wcs_get_subscription( $subscription_id );
				$billing_period  = $subscription->get_billing_period();

				if ( in_array( $billing_period, $data[ 'value' ] ) ) {
					$billing_period_match = true;
				}

				foreach ( $cart_contents as $cart_item_key => $cart_item ) {

					// Check for subscription renewal context.
					if ( isset( $cart_item[ 'subscription_renewal' ] ) ) {

						if ( $this->modifier_is( $data[ 'modifier' ], array( 'in', 'not-in' ) ) ) {

							if ( $billing_period_match ) {
								$matching_item = true;
							}

						} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'all-in', 'not-all-in' ) ) ) {

							if ( ! $billing_period_match ) {
								$all_items_matching = false;
							}
						}

					} else {
						$contains_non_subscription_items = true;
					}
				}

				if ( $subscription && $contains_non_subscription_items && ( $this->modifier_is( $data[ 'modifier' ], array( 'all-in', 'not-all-in' ) ) ) ) {
					return $this->modifier_is( $data[ 'modifier' ], array( 'all-in' ) ) ? false : true;
				}

				if ( $this->modifier_is( $data[ 'modifier' ], array( 'in' ) ) && $matching_item ) {
					return true;
				} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'not-in' ) ) && ! $matching_item ) {
					return true;
				} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'all-in' ) ) && $all_items_matching ) {
					return true;
				} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'not-all-in' ) ) && ! $all_items_matching ) {
					return true;
				}
			}

			// Re-Init.
			$contains_non_subscription_items = false;

			// Search for non-subcription items of any kind.
			if ( $this->modifier_is( $data[ 'modifier' ], array( 'all-in', 'not-all-in' ) ) ) {

				foreach ( $cart_contents as $cart_item_key => $cart_item ) {
					if ( ! WC_Subscriptions_Product::is_subscription( $cart_item[ 'data' ] ) ) {
						$contains_non_subscription_items = true;
						break;
					}
				}

				if ( $contains_non_subscription_items ) {
					return $this->modifier_is( $data[ 'modifier' ], array( 'all-in' ) ) ? false : true;
				}
			}

			// Lastly, search for subcriptions.
			if ( WC_Subscriptions_Cart::cart_contains_subscription() ) {

				$recurring_carts    = WC()->cart->recurring_carts;
				$matching_item      = false;
				$all_items_matching = true;

				if ( ! empty( $recurring_carts ) ) {

					foreach ( $recurring_carts as $cart_hash => $cart ) {

						$billing_period  = wcs_cart_pluck( $cart, 'subscription_period' );

						if ( $this->modifier_is( $data[ 'modifier' ], array( 'in', 'not-in' ) ) ) {

							if ( in_array( $billing_period, $data[ 'value' ] ) ) {
								$matching_item = true;
								break;
							}

						} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'all-in', 'not-all-in' ) ) ) {

							if ( ! in_array( $billing_period, $data[ 'value' ] ) ) {
								$all_items_matching = false;
								break;
							}
						}
					}

					if ( $this->modifier_is( $data[ 'modifier' ], array( 'in' ) ) && $matching_item ) {
						return true;
					} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'not-in' ) ) && ! $matching_item ) {
						return true;
					} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'all-in' ) ) && $all_items_matching ) {
						return true;
					} elseif ( $this->modifier_is( $data[ 'modifier' ], array( 'not-all-in' ) ) && ! $all_items_matching ) {
						return true;
					}
				}

			} else {
				return $this->modifier_is( $data[ 'modifier' ], array( 'not-in', 'not-all-in' ) );
			}
		}

		return false;
	}

	/**
	 * Validate, process and return condition fields.
	 *
	 * @param  array  $posted_condition_data
	 * @return array
	 */
	public function process_admin_fields( $posted_condition_data ) {

		$processed_condition_data = array();

		if ( isset( $posted_condition_data[ 'value' ] ) ) {
			$processed_condition_data[ 'condition_id' ] = $this->id;
			$processed_condition_data[ 'modifier' ]     = stripslashes( $posted_condition_data[ 'modifier' ] );
			$processed_condition_data[ 'value' ]        = array_map( 'wc_clean', $posted_condition_data[ 'value' ] );
		}

		return $processed_condition_data;
	}

	/**
	 * Get backorders-in-cart condition content for global restrictions.
	 *
	 * @param  int    $index
	 * @param  int    $condition_index
	 * @param  array  $condition_data
	 * @return str
	 */
	public function get_admin_fields_html( $index, $condition_index, $condition_data ) {

		$modifier = '';

		if ( ! empty( $condition_data[ 'modifier' ] ) ) {
			$modifier = $condition_data[ 'modifier' ];
		}

		$periods          = wcs_get_subscription_period_strings();
		$selected_periods = isset( $condition_data[ 'value' ] ) ? $condition_data[ 'value' ] : array();

		?>
		<input type="hidden" name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][condition_id]" value="<?php echo $this->id; ?>" />
		<div class="condition_row_inner">
			<div class="condition_modifier">
				<div class="sw-enhanced-select">
					<select name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][modifier]">
						<option value="in" <?php selected( $modifier, 'in', true ) ?>><?php echo __( 'in cart', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="not-in" <?php selected( $modifier, 'not-in', true ) ?>><?php echo __( 'not in cart', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="all-in" <?php selected( $modifier, 'all-in', true ) ?>><?php echo __( 'all cart items', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
						<option value="not-all-in" <?php selected( $modifier, 'not-all-in', true ) ?>><?php echo __( 'not all cart items', 'woocommerce-conditional-shipping-and-payments' ); ?></option>
					</select>
				</div>
			</div>
			<div class="condition_value">
				<select name="restriction[<?php echo $index; ?>][conditions][<?php echo $condition_index; ?>][value][]" class="multiselect sw-select2" multiple="multiple" data-placeholder="<?php _e( 'Select billing period&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?>">

					<?php
						foreach ( $periods as $value => $label ) {
							echo '<option value="' . esc_attr( $value ) . '" ' . selected( in_array( $value, $selected_periods ), true, false ).'>' . esc_html( $this->get_billing_period_adverb( $value ) ) . '</option>';
						}
					?>

				</select>
			</div>
		</div><?php

	}
}
