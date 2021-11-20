<?php
/**
 * WooCommerce Points and Rewards
 *
 * @package     WC-Points-Rewards/Classes
 * @author      WooThemes
 * @copyright   Copyright (c) 2013, WooThemes
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Cart / Checkout class
 *
 * Adds earn/redeem messages to the cart / checkout page and calculates the discounts available
 *
 * @since 1.0
 */
class WC_Points_Rewards_Cart_Checkout {
	/**
	 * Add cart/checkout related hooks / filters
	 *
	 * @since 1.0
	 */
	public function __construct() {

		// Coupon display
		add_filter( 'woocommerce_cart_totals_coupon_label', array( $this, 'coupon_label' ) );
		// Coupon loading
		add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'points_last' ) );

		// add earn points/redeem points message above cart / checkout
		add_action( 'woocommerce_before_cart', array( $this, 'render_earn_points_message' ), 15 );
		add_action( 'woocommerce_before_cart', array( $this, 'render_redeem_points_message' ), 16 );
		add_action( 'woocommerce_before_checkout_form', array( $this, 'render_earn_points_message' ), 5 );
		add_action( 'woocommerce_before_checkout_form', array( $this, 'render_redeem_points_message' ), 6 );

		// Add JavaScript used by apply discount button (when partial redeem is enabled).
		add_action( 'woocommerce_before_cart', array( $this, 'render_discount_javascript' ) );
		add_action( 'woocommerce_before_checkout_form', array( $this, 'render_discount_javascript' ) );

		// Maximum amount of redeemable points for the current cart contents.
		add_action( 'woocommerce_after_cart_totals', array( $this, 'cart_max_points' ) );
		add_action( 'woocommerce_review_order_after_order_total', array( $this, 'cart_max_points' ) );

		// add earned points message on the thank you / order received page
		add_action( 'woocommerce_thankyou', array( $this, 'render_thank_you_message' ) );

		// handle the apply discount submit on the cart page
		add_action( 'wp', array( $this, 'maybe_apply_discount' ) );

		// handle the apply discount AJAX submit on the checkout page
		add_action( 'wp_ajax_wc_points_rewards_apply_discount', array( $this, 'ajax_maybe_apply_discount' ) );

		// Reshow messages on checkout if coupon was applied or removed.
		add_action( 'woocommerce_applied_coupon', array( $this, 'discount_updated' ), 40 );
		add_action( 'woocommerce_removed_coupon', array( $this, 'discount_updated' ), 40 );
	}

	/**
	 * Make the label for the coupon look nicer
	 * @param  string $label
	 * @return string
	 */
	public function coupon_label( $label ) {
		if ( strstr( strtoupper( $label ), 'WC_POINTS_REDEMPTION' ) ) {
			$label = esc_html( __( 'Points redemption', 'woocommerce-points-and-rewards' ) );
		}

		return $label;
	}

	/**
	 * Ensure points are applied before tax, last
	 */
	public function points_last() {
		$ordered_coupons = array();
		$points = array();

		foreach ( WC()->cart->get_applied_coupons() as $code ) {
			if ( strstr( $code, 'wc_points_redemption_' ) ) {
				$points[] = $code;
			} else {
				$ordered_coupons[] = $code;
			}
		}

		WC()->cart->applied_coupons = array_merge( $ordered_coupons, $points );
	}

	/**
	 * Redisplays the redeem and earn messages after a discount has been updated.
	 *
	 * @param string $coupon_code Coupon code which is removed.
	 */
	public function discount_updated( $coupon_code ) {
		// Do not display messages on ajax requests from the checkout or cart page.
		if ( wp_is_json_request() || is_checkout() || is_cart() || wp_get_referer() === wc_get_cart_url() ) {
			return;
		}

		WC()->cart->calculate_totals();
		$this->render_earn_points_message();
		$this->render_redeem_points_message();
	}

	/**
	 * Redeem the available points by generating and applying a discount code on the cart page
	 *
	 * @since 1.0
	 */
	public function maybe_apply_discount() {
		// only apply on cart and from apply discount action
		if ( ! is_cart() || ! isset( $_POST['wc_points_rewards_apply_discount'] ) ) {
			return;
		}

		$existing_discount = WC_Points_Rewards_Discount::get_discount_code();

		// bail if the discount has already been applied
		if ( ! empty( $existing_discount ) && WC()->cart->has_discount( $existing_discount ) ) {
			return;
		}

		$discount_amount = ! empty( $_POST['wc_points_rewards_apply_discount_amount'] ) ? absint( $_POST['wc_points_rewards_apply_discount_amount'] ) : '';

		// Queue an error notice and bail if the discount doesn't reach the minimum
		if ( self::queue_below_minimum_discount_notice( $discount_amount ) ) {
			return;
		}

		// Get discount amount if set and store in session
		WC()->session->set( 'wc_points_rewards_discount_amount', $discount_amount );

		// generate and set unique discount code
		$discount_code = WC_Points_Rewards_Discount::generate_discount_code();

		// apply the discount
		WC()->cart->add_discount( $discount_code );
	}


	/**
	 * @return bool True if the discount has already been applied to the cart.
	 *
	 * @since 1.7.0
	 */
	public static function is_discount_applied() {
	 	// bail if the discount has already been applied
		$existing_discount = WC_Points_Rewards_Discount::get_discount_code();

		return ! empty( $existing_discount ) && WC()->cart->has_discount( $existing_discount );
	}

	/**
	 * Redeem the available points by generating and applying a discount code via AJAX on the checkout page
	 *
	 * @since 1.0
	 */
	public function ajax_maybe_apply_discount() {
		check_ajax_referer( 'apply-coupon', 'security' );

		// Bail if the discount has already been applied.
		if ( self::is_discount_applied() ) {
			die;
		}

		$discount_amount = ! empty( $_POST['discount_amount'] ) ? absint( $_POST['discount_amount'] ) : 0;

		// Queue an error notice and display earn/redeem messages if the discount doesn't reach the minimum.
		if ( self::queue_below_minimum_discount_notice( $discount_amount ) ) {
			$this->render_earn_points_message();
			$this->render_redeem_points_message();
		} else {
			// Get discount amount if set and store in session.
			WC()->session->set( 'wc_points_rewards_discount_amount', $discount_amount );

			// Generate and set unique discount code.
			$discount_code = WC_Points_Rewards_Discount::generate_discount_code();

			// Apply the discount.
			WC()->cart->add_discount( $discount_code );
		}

		wc_print_notices();
		die;
	}

	/**
	 * If the request is from the rest API, we shouldn't show the success messages in the Cart block.
	 * @param string $message The message that will be shown to the user if the points are applied outside of the REST API.
	 * @param integer $message_code The code indicating success/failure.
	 * @param \WC_Coupon $coupon The coupon that was added.
	 *
	 * @return string The message that will be shown to the user if the points are applied outside of the REST API.
	 *
	 * @since 1.7.0
	 */
	public static function hide_success_message_for_rest_requests( $message, $message_code, $coupon ) {
		if ( WC_Coupon::WC_COUPON_SUCCESS === $message_code && $coupon->get_code() === WC_Points_Rewards_Discount::get_discount_code() ) {
			return '';
		}

		return $message;
	}

	/**
	 * Applies a coupon indicating a points redemption. This method is used by WooCommerce Blocks, via the REST API.
	 *
	 * @param array $args The arguments containing the number of points the user wants to redeem.
	 *
	 * @return void
	 *
	 * @since 1.7.0
	 */
	public static function rest_apply_discount( $args ) {
		$discount_amount = isset( $args['discount_amount'] ) ? $args['discount_amount'] : 0;
		add_filter( 'woocommerce_coupon_message', [ self::class, 'hide_success_message_for_rest_requests' ], 10, 3 );
		$existing_discount = WC_Points_Rewards_Discount::get_discount_code();

		WC()->cart->remove_coupon( $existing_discount );

		// Get discount amount if set and store in session
		WC()->session->set( 'wc_points_rewards_discount_amount', ( ! empty( $discount_amount ) ? absint( $discount_amount ) : '' ) );

		// Check if this is above the minimum spend.
		$minimum_discount_message = self::generate_below_minimum_discount_message( $discount_amount );
		if( ! is_null( $minimum_discount_message ) ) {
			throw new Exception( $minimum_discount_message );
		}

		// generate and set unique discount code
		$discount_code = WC_Points_Rewards_Discount::generate_discount_code();

		// apply the discount
		WC()->cart->add_discount( $discount_code );
	}


	/**
	 * Generate the error message when the redeemed points don't reach the set minimum.
	 *
	 * @param int $discount_amount The points discount submitted.
	 *
	 * @return string|null Error message or null if no error.
	 *
	 * @since 1.7.3
	 */
	private static function generate_below_minimum_discount_message( $discount_amount ) {
		global $wc_points_rewards;

		$minimum_discount      = (float) get_option( 'wc_points_rewards_cart_min_discount', '' );
		$minimum_points_amount = WC_Points_Rewards_Manager::calculate_points_for_discount( $minimum_discount );
		if (
			$discount_amount < $minimum_points_amount &&
			'yes' === get_option( 'wc_points_rewards_partial_redemption_enabled' )
		) {
			return sprintf(
			/* translators: %s is the custom name for points, %d is the minimum number of points the user must spend */
				__(
					'The minimum number of %s you can redeem is %d.',
					'woocommerce-points-and-rewards'
				),
				$wc_points_rewards->get_points_label( $discount_amount ),
				$minimum_points_amount
			);
		}
		return null;
	}

	/**
	 * Adds the corresponding error notice if the discount amount doesn't reach the set minimum.
	 *
	 * @param int $discount_amount The points discount submitted.
	 *
	 * @return bool true if the notice was added, false otherwise.
	 *
	 * @since 1.7.3
	 */
	private static function queue_below_minimum_discount_notice( $discount_amount ) {

		// Check if this is above the minimum spend.
		$minimum_discount_message = self::generate_below_minimum_discount_message( $discount_amount );
		if ( is_null( $minimum_discount_message ) ) {
			return false;
		}
		// Queue the error message
		wc_add_notice( $minimum_discount_message, 'error' );

		return true;
	}



	/**
	 * Generate the message that will be displayed showing how many points the customer will receive for completing their purchase.
	 *
	 * @return string the message that will be displayed showing how many points the customer will receive for completing their purchase.
	 *
	 * @since 1.7.0
	 */
	public function generate_earn_points_message() {
		global $wc_points_rewards;

		// get the total points earned for this purchase
		$points_earned = $this->get_points_earned_for_purchase();

		$message = get_option( 'wc_points_rewards_earn_points_message' );

		// bail if no message set or no points will be earned for purchase
		if ( ! $message || ! $points_earned ) {
			return null;
		}

		// points earned
		$message = str_replace( '{points}', number_format_i18n( $points_earned ), $message );

		// points label
		$message = str_replace( '{points_label}', $wc_points_rewards->get_points_label( $points_earned ), $message );

		return $message;
	}

	/**
	 * Renders a message above the cart displaying how many points the customer will receive for completing their purchase
	 *
	 * @since 1.0
	 */
	public function render_earn_points_message() {
		$points_earned = $this->get_points_earned_for_purchase();
		$message = $this->generate_earn_points_message();

		// If message was null then return here, we don't need to continue.
		if( null === $message ) {
			return;
		}

		// Prepare for rendering by wrapping in div.
		$message = '<div class="woocommerce-info wc_points_rewards_earn_points">' . $message . '</div>';

		echo apply_filters( 'wc_points_rewards_earn_points_message', $message, $points_earned );

		if ( is_checkout() ) {
			wc_enqueue_js(
				'$(function() {
					var remove_earn_points_messages = function() {
						$( ".wc_points_rewards_earn_points" ).remove();
					}

					$( "form.checkout_coupon" ).submit( remove_earn_points_messages );
					$( document.body ).on( "click", ".woocommerce-remove-coupon", remove_earn_points_messages );

				} );'
			);
		}
	}

	/**
	 * Renders a message on the thank you / order received page that tells the customer how many points they earned and
	 * how many they have total
	 * @param int $order_id
	 */
	public function render_thank_you_message( $order_id ) {
		global $wc_points_rewards;

		$points = $this->get_points_earned_for_order_received( $order_id );
		$total_points = WC_Points_Rewards_Manager::get_users_points( get_current_user_id() );

		$message = get_option( 'wc_points_rewards_thank_you_message' );

		if ( ! $message || ! $points ) {
			return;
		}

		$message = str_replace( '{points}', number_format_i18n( $points ), $message );
		$message = str_replace( '{points_label}', $wc_points_rewards->get_points_label( $points ), $message );

		$message = str_replace( '{total_points}', number_format_i18n( $total_points ), $message );
		$message = str_replace( '{total_points_label}', $wc_points_rewards->get_points_label( $total_points ), $message );

		$message = '<p>' . $message . '</p>';

		echo apply_filters( 'wc_points_rewards_thank_you_message', $message, $points, $total_points );
	}

	/**
	 * Returns the number of exact points earned for an order
	 * @param  int $order_id
	 * @return int
	 */
	public function get_points_earned_for_order_received( $order_id ) {
		global $wc_points_rewards, $wpdb;

		$points = 0;
		$point_log = $wpdb->get_results( $wpdb->prepare( "SELECT points FROM {$wc_points_rewards->user_points_log_db_tablename} WHERE order_id = %d;", $order_id ) );

		if ( ! empty( $point_log ) && $point_log[0]->points > 0 ) {
			$points = $point_log[0]->points;
		} elseif ( ! empty( $point_log ) && isset( $point_log[1]->points ) && $point_log[1]->points > 0 ) {
			$points = $point_log[1]->points;
		}

		return $points;
	}

	/**
	 * Determines if the users cart is fully discounted.
	 *
	 * @since 1.6.10
	 *
	 * @return boolean
	 */
	public function is_fully_discounted() {

		// If cart includes tax AND cart total is 0
		if ( WC()->cart->prices_include_tax && 0 >= ( WC()->cart->cart_contents_total + WC()->cart->tax_total ) ) {
			return true;
		}

		// If cart excludes tax AND cart total is 0
		if ( ! WC()->cart->prices_include_tax && 0 >= WC()->cart->cart_contents_total ) {
			return true;
		}

		return false;

	}

	/**
	 * Generates the message to shoppers telling them how many points htey can redeem.
	 *
	 * @return string The message displayed to shoppers telling them how many points they can redeem.
	 * @since 1.7.0
	 */
	public function generate_redeem_points_message() {
		global $wc_points_rewards;
		// get the total discount available for redeeming points
		$discount_available = $this->get_discount_for_redeeming_points( false, null, true );
		$message = get_option( 'wc_points_rewards_redeem_points_message' );

		// bail if no message set or no points will be earned for purchase
		if ( ! $message || ! $discount_available ) {
			return null;
		}

		// points required to redeem for the discount available
		$points  = WC_Points_Rewards_Manager::calculate_points_for_discount( $discount_available );
		$message = str_replace( '{points}', number_format_i18n( $points ), $message );

		// the maximum discount available given how many points the customer has
		$message = str_replace( '{points_value}', wc_price( $discount_available ), $message );

		// points label
		return str_replace( '{points_label}', $wc_points_rewards->get_points_label( $points ), $message );
	}

	/**
	 * Renders a message and button above the cart displaying the points available to redeem for a discount
	 *
	 * @since 1.0
	 */
	public function render_redeem_points_message() {
		$existing_discount = WC_Points_Rewards_Discount::get_discount_code();

		/*
		 * Don't display a points message to the user if:
		 * The cart total is fully discounted OR
		 * Coupons are disabled OR
		 * Points have already been applied for a discount.
		 */
		if ( $this->is_fully_discounted() || ! wc_coupons_enabled() || ( ! empty( $existing_discount ) && WC()->cart->has_discount( $existing_discount ) ) ) {
			return;
		}

		// get the total discount available for redeeming points
		$discount_available = $this->get_discount_for_redeeming_points( false, null, true );

		$message = $this->generate_redeem_points_message();

		if ( null === $message ) {
			return;
		}

		// add 'Apply Discount' button
		$message .= '<form class="wc_points_rewards_apply_discount" action="' . esc_url( wc_get_cart_url() ) . '" method="post" style="display:inline">';
		$message .= '<input type="hidden" name="wc_points_rewards_apply_discount_amount" class="wc_points_rewards_apply_discount_amount" />';
		$message .= '<input type="submit" class="button wc_points_rewards_apply_discount" name="wc_points_rewards_apply_discount" value="' . __( 'Apply Discount', 'woocommerce-points-and-rewards' ) . '" /></form>';

		// wrap with info div
		$message = '<div class="woocommerce-info wc_points_redeem_earn_points">' . $message . '</div>';

		echo apply_filters( 'wc_points_rewards_redeem_points_message', $message, $discount_available );

		// add AJAX submit for applying the discount on the checkout page

		if ( is_checkout() ) {
			wc_enqueue_js( '
			/* Points & Rewards AJAX Apply Points Discount */
			var points_applied = false;

			$(function() {

				/**
				 * Get the order total.
				 *
				 * @since 1.6.10
				 * @return {float} Float of the total for the users order
				 */
				var get_order_total = function get_order_total() {

					var order_total_parts = jQuery( ".order-total .woocommerce-Price-amount" ).clone().children().remove().end().text().split( "" );

					// Order totals occasionally have unwanted characters we dont want when parsing to evaluate.
					var order_total_clean = order_total_parts.filter( function( item ) {
						return ( ! isNaN( parseInt( item, 10 ) ) || "." === item );
					});

					return parseFloat( order_total_clean.join("") );

				}

				/**
				 * Show all points and rewards checkout messages.
				 *
				 * @since 1.6.10
				 */
				var show_all_messages = function show_all_messages() {
					show_rewards_message();
					show_redeem_message();
				}

				/**
				 * Hide all points and rewards checkout messages.
				 *
				 * @since 1.6.10
				 */
				var hide_all_messages = function hide_all_messages() {
					hide_rewards_message();
					hide_redeem_message();
				}

				/**
				 * Show the rewards checkout message.
				 *
				 * @since 1.6.10
				 * @param {function} Callback to be called after message is shown to the user.
				 */
				var show_rewards_message = function show_rewards_message( callback ) {
					var el = $( ".wc_points_rewards_earn_points" );

					if ( 0 == el.length ) {
						callback && callback();
						return;
					}

					el.slideDown( function() {
						callback && callback();
					});
				}

				/**
				 * Hide the rewards checkout message.
				 *
				 * @since 1.6.10
				 * @param {function} Callback to be called after message is shown to the user.
				 */
				var hide_rewards_message = function hide_rewards_message( callback ) {
					var el = $( ".wc_points_rewards_earn_points" );

					if ( 0 == el.length ) {
						callback && callback();
						return;
					}

					el.slideUp( function() {
						callback && callback();
					});
				}

				/**
				 * Show the redeem checkout message.
				 *
				 * @since 1.6.10
				 */
				var show_redeem_message = function show_redeem_message() {
					if ( points_applied ) {
						return;
					}
					$( ".wc_points_redeem_earn_points" ).slideDown();
				}

				/**
				 * Hide the redeem checkout message.
				 *
				 * @since 1.6.10
				 */
				var hide_redeem_message = function hide_redeem_message() {
					$( ".wc_points_redeem_earn_points" ).slideUp();
				}

				$( document.body ).on( "updated_checkout", function() {
					if ( 0 >= get_order_total() ) {
						hide_all_messages();
					} else {
						show_all_messages();
					}

				});

				$( "body" ).on ( "click", ".woocommerce-remove-coupon", function( e ) {
					if ( 0 < get_order_total() ) {
						show_all_messages();
					}
					e.preventDefault();
				});

				$( "body" ).on( "submit", ".wc_points_rewards_apply_discount", function( e ) {

					var $section = $( "div.wc_points_redeem_earn_points" );

					if ( $section.is( ".processing" ) ) {
						return false;
					}

					$section.addClass( "processing" ).block({message: null, overlayCSS: {background: "#fff", opacity: 0.6}});

					hide_rewards_message( function() {

						var data = {
							action: "wc_points_rewards_apply_discount",
							discount_amount: $("input.wc_points_rewards_apply_discount_amount").val(),
							security: ( woocommerce_params.apply_coupon_nonce ? woocommerce_params.apply_coupon_nonce : wc_checkout_params.apply_coupon_nonce )
						};

						$.ajax({
							type:     "POST",
							url:      woocommerce_params.ajax_url,
							data:     data,
							success:  function( code ) {
								points_applied = true;

								$( ".woocommerce-error, .woocommerce-message, .wc_points_rewards_earn_points" ).remove();
								$section.removeClass( "processing" ).unblock();

								if ( code ) {
									$section.before( code );

									$section.remove();

									$( "body" ).trigger( "update_checkout" );
								}
							},
							dataType: "html"
						});

					});

					return false;

				});

				var remove_redeem_points_messages = function() {
					$( ".wc_points_redeem_earn_points" ).remove();
				}

				$( "form.checkout_coupon" ).submit( remove_redeem_points_messages );
				$( document.body ).on( "click", ".woocommerce-remove-coupon", remove_redeem_points_messages );

			});
			' );
		} // End if().
	}

	/**
	 * Add javascript to footer.
	 *
	 * @return void
	 */
	public function render_discount_javascript() {
		if ( 'yes' === get_option( 'wc_points_rewards_partial_redemption_enabled' ) ) {
			// Add code to prompt for points amount.
			wc_enqueue_js(
				'
				$( "body" ).on( "click", "input.wc_points_rewards_apply_discount", function( e ) {
					var max_points = $( "[name=wc-points-rewards-max-points]" ).val();
					var points = prompt( "' . esc_js( __( 'How many points would you like to apply?', 'woocommerce-points-and-rewards' ) ) . '", max_points );
					if ( null != points && 0 != points ) {
						$( "input.wc_points_rewards_apply_discount_amount" ).val( points );
						return true;
					}
					return false;
				});
				'
			);
		}
	}

	/**
	 * Add the maxiumum amount of redeemable points to the cart page.
	 */
	public function cart_max_points() {
		if ( 'yes' === get_option( 'wc_points_rewards_partial_redemption_enabled' ) ) {
			$points = $this->calculate_cart_max_points();
			?>
				<input type="hidden" name="wc-points-rewards-max-points" value="<?php echo esc_html( $points ); ?>">
			<?php
		}
	}

	/**
	 * Returns the max amount of points that can be applied to the cart.
	 *
	 * @returns integer The maximum number of points that can be applied to the cart.
	 * @since 1.7.0
	 */
	public function calculate_cart_max_points() {
		$discount_available = $this->get_discount_for_redeeming_points( false, null, true );
		return WC_Points_Rewards_Manager::calculate_points_for_discount( $discount_available );
	}

	/**
	 * Returns the amount of points earned for the purchase, calculated by getting the points earned for each individual
	 * product purchase multiplied by the quantity being ordered
	 *
	 * @since 1.0
	 */
	private function get_points_earned_for_purchase() {
		$points_earned = 0;

		foreach ( WC()->cart->get_cart() as $item_key => $item ) {
			$points_earned += apply_filters( 'woocommerce_points_earned_for_cart_item', WC_Points_Rewards_Product::get_points_earned_for_product_purchase( $item['data'] ), $item_key, $item ) * $item['quantity'];
		}

		/*
		 * Reduce by any discounts.  One minor drawback: if the discount includes a discount on tax and/or shipping
		 * it will cost the customer points, but this is a better solution than granting full points for discounted orders.
		 */
		if ( version_compare( WC_VERSION, '2.3', '<' ) ) {
			$discount = WC()->cart->discount_cart + WC()->cart->discount_total;
		} else {
			$discount = ( wc_prices_include_tax() ) ? WC()->cart->discount_cart + WC()->cart->discount_cart_tax : WC()->cart->discount_cart;
		}

		$discount_amount = min( WC_Points_Rewards_Manager::calculate_points( $discount ), $points_earned );

		// Apply a filter that will allow users to manipulate the way discounts affect points earned.
		$points_earned = apply_filters( 'wc_points_rewards_discount_points_modifier', $points_earned - $discount_amount, $points_earned, $discount_amount, $discount );

		// Check if applied coupons have a points modifier and use it to adjust the points earned.
		$coupons = WC()->cart->get_applied_coupons();

		$points_earned = WC_Points_Rewards_Manager::calculate_points_modification_from_coupons( $points_earned, $coupons );

		$points_earned = WC_Points_Rewards_Manager::round_the_points( $points_earned );
		return apply_filters( 'wc_points_rewards_points_earned_for_purchase', $points_earned, WC()->cart );
	}


	/**
	 * Returns the maximum possible discount available given the total amount of points the customer has
	 *
	 * @since 1.0.0
	 * @version 1.6.5
	 * @param bool   $applying                  To indicate if this method is called during application of the points.
	 * @param float  $existing_discount_amounts Total amount for existing discount for items in cart.
	 * @param bool   $for_display               Whether to generate discount amount for message display purposes or for the actual discount.
	 * @param string $code                      Coupon code, used to check if this coupon was already applied in the process.
	 */
	public static function get_discount_for_redeeming_points( $applying = false, $existing_discount_amounts = null, $for_display = false, $code = null ) {
		// get the value of the user's point balance
		$available_user_discount = WC_Points_Rewards_Manager::get_users_points_value( get_current_user_id() );

		// no discount
		if ( $available_user_discount <= 0 ) {
			return 0;
		}

		if ( $applying && 'yes' === get_option( 'wc_points_rewards_partial_redemption_enabled' ) && WC()->session->get( 'wc_points_rewards_discount_amount' ) ) {
			$requested_user_discount = WC_Points_Rewards_Manager::calculate_points_value( WC()->session->get( 'wc_points_rewards_discount_amount' ) );
			if ( $requested_user_discount > 0 && $requested_user_discount < $available_user_discount ) {
				$available_user_discount = $requested_user_discount;
			}
		}

		// Limit the discount available by the global minimum discount if set.
		$minimum_discount = get_option( 'wc_points_rewards_cart_min_discount', '' );
		if ( $minimum_discount > $available_user_discount ) {
			return 0;
		}

		$discount_applied = 0;

		if ( ! did_action( 'woocommerce_before_calculate_totals' ) ) {
			WC()->cart->calculate_totals();
		}

		/*
		 * Calculate the discount to be applied by iterating through each item in the cart and calculating the individual
		 * maximum discount available.
		 */
		foreach ( WC()->cart->get_cart() as $item ) {
			$discount = self::get_discount_for_cart_item( $item, $for_display );

			// if the discount available is greater than the max discount, apply the max discount
			$discount = ( $available_user_discount <= $discount ) ? $available_user_discount : $discount;

			// add the discount to the amount to be applied
			$discount_applied += $discount;

			// reduce the remaining discount available to be applied
			$available_user_discount -= $discount;
		}

		if ( is_null( $existing_discount_amounts ) ) {
			$existing_discount_amounts = version_compare( WC_VERSION, '3.0.0', '<' )
				? WC()->cart->discount_total
				: WC()->cart->get_cart_discount_total();
		}

		/*
		 * If during calculation process this discount was already applied then we need to remove its amount
		 * from the total discounts in cart to not obscure the calculations.
		 */
		if ( ! is_null( $code ) ) {
			$discount_from_the_coupon   = WC()->cart->get_coupon_discount_amount( $code );
			$existing_discount_amounts -= $discount_from_the_coupon;
		}

		// if the available discount is greater than the order total, make the discount equal to the order total less any other discounts
		if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
			if ( 'no' === get_option( 'woocommerce_prices_include_tax' ) ) {
				$discount_applied = max( 0, min( $discount_applied, WC()->cart->subtotal_ex_tax - $existing_discount_amounts ) );

			} else {
				$discount_applied = max( 0, min( $discount_applied, WC()->cart->subtotal - $existing_discount_amounts ) );

			}
		} else {
			if ( 'no' === get_option( 'woocommerce_prices_include_tax' ) ) {
				$discount_applied = max( 0, min( $discount_applied, WC()->cart->subtotal_ex_tax - $existing_discount_amounts ) );

			} else {
				$discount_applied = max( 0, min( $discount_applied, WC()->cart->subtotal - $existing_discount_amounts ) );
			}
		}

		// limit the discount available by the global maximum discount if set
		$max_discount = get_option( 'wc_points_rewards_cart_max_discount' );

		if ( false !== strpos( $max_discount, '%' ) ) {
			$max_discount = self::calculate_discount_modifier( $max_discount );
		}
		$max_discount = filter_var( $max_discount, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );

		if ( $max_discount && $max_discount < $discount_applied ) {
			$discount_applied = $max_discount;
		}

		return filter_var( $discount_applied, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
	}

	/**
	 * Calculate the discount for a single cart item.
	 *
	 * @since 1.6.41
	 *
	 * @param array $item        The cart item.
	 * @param bool  $for_display Whether to generate discount amount for message display purposes or for the actual discount.
	 *
	 * @return int
	 */
	private static function get_discount_for_cart_item( $item, $for_display ) {
		$product  = $item['data'];
		$quantity = $item['quantity'];

		if ( ! $product instanceof WC_Product ) {
			return 0;
		}

		$max_discount = WC_Points_Rewards_Product::get_maximum_points_discount_for_product( $product );

		if ( is_numeric( $max_discount ) ) {

			// multiple the product max discount by the quantity being ordered
			return $max_discount * $quantity;

			// Max should be product price. As this will be applied before tax, it will respect other coupons.
		} else {
			/*
			 * Only exclude taxes when configured to in settings and when generating a discount amount for displaying in
			 * the checkout message. This makes the actual discount money amount always tax inclusive.
			 */
			if ( 'exclusive' === get_option( 'wc_points_rewards_points_tax_application', wc_prices_include_tax() ? 'inclusive' : 'exclusive' ) && $for_display ) {
				if ( function_exists( 'wc_get_price_excluding_tax' ) ) {
					$max_discount = wc_get_price_excluding_tax( $product, array( 'qty' => $quantity ) );
				} elseif ( method_exists( $product, 'get_price_excluding_tax' ) ) {
					$max_discount = $product->get_price_excluding_tax( $quantity );
				} else {
					$max_discount = $product->get_price( 'edit' ) * $quantity;
				}
			} else {
				if ( function_exists( 'wc_get_price_including_tax' ) ) {
					$max_discount = wc_get_price_including_tax( $product, array( 'qty' => $quantity ) );
				} elseif ( method_exists( $product, 'get_price_including_tax' ) ) {
					$max_discount = $product->get_price_including_tax( $quantity );
				} else {
					$max_discount = $product->get_price( 'edit' ) * $quantity;
				}
			}

			return $max_discount;
		}
	}

	/**
	 * Calculate the maximum points discount when it's set to a percentage by multiplying the percentage times the cart's
	 * price
	 *
	 * @since 1.0
	 * @param string $percentage the percentage to multiply the price by
	 * @return float the maximum discount after adjusting for the percentage
	 */
	private static function calculate_discount_modifier( $percentage ) {

		$percentage = str_replace( '%', '', $percentage ) / 100;

		if ( 'no' === get_option( 'woocommerce_prices_include_tax' ) ) {
			$discount = WC()->cart->subtotal_ex_tax;

		} else {
			$discount = WC()->cart->subtotal;

		}

		return $percentage * $discount;
	}


} // end \WC_Points_Rewards_Cart_Checkout class
