<?php
/**
 * WooCommerce Local Pickup Plus
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Local Pickup Plus to newer
 * versions in the future. If you wish to customize WooCommerce Local Pickup Plus for your
 * needs please refer to http://docs.woocommerce.com/document/local-pickup-plus/
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2012-2023, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Local_Pickup_Plus;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\Local_Pickup_Plus\Data_Store\Package_Pickup_Data;
use SkyVerge\WooCommerce\Local_Pickup_Plus\Fields\Cart_Item_Handling_Toggle;
use SkyVerge\WooCommerce\Local_Pickup_Plus\Fields\Cart_Item_Pickup_Location_Field;
use SkyVerge\WooCommerce\Local_Pickup_Plus\Fields\Package_Pickup_Appointment_Field;
use SkyVerge\WooCommerce\Local_Pickup_Plus\Fields\Package_Pickup_Items_Field;
use SkyVerge\WooCommerce\Local_Pickup_Plus\Fields\Package_Pickup_Location_Field;
use SkyVerge\WooCommerce\PluginFramework\v5_11_0 as Framework;

/**
 * Checkout form shipping handler.
 *
 * @since 2.0.0
 */
class Checkout {


	/** @var array memoization helper to prevent duplicate HTML output in checkout form */
	private static $pickup_package_form_output = [];

	/** @var bool flag if packages have been counted yet */
	private static $packages_count_output = false;


	/**
	 * Checkout hooks.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// to output the checkout item pickup location selector we need a different hook than the one used in cart page
		add_filter( 'woocommerce_checkout_cart_item_quantity', [ $this, 'add_checkout_item_pickup_location_field' ], 999, 3 );

		// add pickup location information and a pickup appointment field to each package meant for pickup
		add_action( 'woocommerce_after_shipping_rate', [ $this, 'output_pickup_package_form' ], 999, 2 );

		// workaround to avoid WooCommerce displaying pickup item details in wrong places in the checkout form
		add_filter( 'woocommerce_shipping_package_details_array', [ $this, 'maybe_hide_pickup_package_item_details' ], 10, 2 );

		// output hidden counters for packages by handling type for JS use
		add_action( 'woocommerce_review_order_after_cart_contents', [ $this, 'packages_count' ], 40 );

		// ensure cash on delivery is available as it deactivates itself if there are multiple packages
		add_filter( 'woocommerce_available_payment_gateways', [ $this, 'enable_cash_on_delivery' ], 9 );

		// if there are any chosen pickup locations that warrant a discount, apply the total discount as a negative fee
		add_action( 'woocommerce_cart_calculate_fees', [ $this, 'apply_pickup_discount' ] );

		// handle checkout validation upon submission
		add_action( 'woocommerce_after_checkout_validation', [ $this, 'validate_checkout' ], 999 );

		// add tags to the first and last packages of each handling type (Shipping or Local Pickup)
		add_filter( 'woocommerce_shipping_packages', [ $this, 'add_template_variables_to_packages' ] );

		// add a new table row for each handling type (Shipping or Local Pickup)
		add_action( 'woocommerce_before_template_part', [ $this, 'add_package_group_row_start' ], 10, 4 );
		add_action( 'woocommerce_after_template_part', [ $this, 'add_package_group_row_end' ], 10, 4 );

		// group the shipping method label for LPP for all packages where it is the only method available
		add_filter( 'wc_get_template', [ $this, 'group_lpp_method_label' ], 10, 5 );
	}


	/**
	 * Adds template variables to the packages array:
	 * - tags to the first and last packages of each handling type (Shipping or Local Pickup) to group them in the UI
	 * - total cost of all pickup-only packages to the first pickup-only package
	 *
	 * @internal
	 *
	 * @since 2.7.0
	 *
	 * @param array $packages shipping packages array
	 * @return array
	 */
	public function add_template_variables_to_packages( $packages ) {

		$pickup_only_packages = [];
		$pickup_packages      = [];
		$shipping_packages    = [];

		$pickup_only_packages_total_cost     = 0;
		$pickup_only_packages_total_discount = 0;

		$local_pickup_plus = wc_local_pickup_plus_shipping_method_id();

		foreach ( $packages as $key => $package ) {

			$chosen_shipping_method = wc_get_chosen_shipping_method_for_package( $key, $package );

			// shipping
			if ( $chosen_shipping_method !== $local_pickup_plus ) {

				$shipping_packages[] = $package;

			// pickup-only
			} elseif ( ! empty( $package['rates'] ) && 1 === count( $package['rates'] ) && $local_pickup_plus === key( $package['rates'] ) ) {

				$pickup_only_packages[]           = $package;
				$shipping_method                  = $package['rates'][ $local_pickup_plus ];
				$pickup_only_packages_total_cost += $shipping_method->cost;

				// get discount (not persisted in the shipping method)
				if ( empty( (float) $shipping_method->cost ) ) {

					$pickup_location  = wc_local_pickup_plus()->get_packages_instance()->get_package_pickup_location( $package );
					$price_adjustment = $pickup_location ? $pickup_location->get_price_adjustment() : null;

					if ( $price_adjustment ) {

						$base = ! empty( $package['contents_cost'] ) ? $package['contents_cost'] : 0;
						$cost = $price_adjustment->get_relative_amount( $base );

						if ( $cost < 0 ) {
							$pickup_only_packages_total_discount += $cost;
						}
					}
				}

			// pickup or ship
			} else {

				$pickup_packages[] = $package;
			}
		}

		if ( ! empty( $pickup_only_packages ) ) {
			$pickup_only_packages[0]['total_lpp_only_cost'] = $pickup_only_packages_total_cost + $pickup_only_packages_total_discount;
		}

		$pickup_packages = array_merge( $pickup_only_packages, $pickup_packages );

		if ( ! empty( $pickup_packages ) ) {

			$pickup_packages[0]['first_pickup_package'] = true;

			$pickup_packages[ count( $pickup_packages ) - 1 ]['last_pickup_package'] = true;
		}

		if ( ! empty( $shipping_packages ) ) {

			$shipping_packages[0]['first_shipping_package'] = true;

			$shipping_packages[ count( $shipping_packages ) - 1 ]['last_shipping_package'] = true;
		}

		return array_merge( $pickup_packages, $shipping_packages );
	}


	/**
	 * Opens a new table row for each handling type (Shipping or Local Pickup).
	 *
	 * @internal
	 *
	 * @since 2.7.0
	 *
	 * @param string $template_name the template name
	 * @param string $template_path the template path
	 * @param string $located the template file
	 * @param array $args the args
	 */
	public function add_package_group_row_start( $template_name, $template_path, $located, $args ) {

		if ( 'cart/cart-shipping.php' === $template_name && ! empty( $args['package'] ) ) {

			$package = $args['package'];

			if ( ! empty( $package['first_pickup_package'] ) ) {

				echo '<tr class="woocommerce-shipping-total shipping"><th>' . esc_html( wc_local_pickup_plus_shipping_method()->get_method_title() ) . '</th><td>';
				echo $this->add_package_wrapper_start();
			}

			if ( ! empty( $package['first_shipping_package'] ) ) {

				echo '<tr class="woocommerce-shipping-total shipping"><th>' . esc_html__( 'Shipping', 'woocommerce-shipping-local-pickup-plus' ) . '</th><td>';
				echo $this->add_package_wrapper_start();
			}
		}
	}


	/**
	 * Closes the new table row for each handling type (Shipping or Local Pickup).
	 *
	 * @internal
	 *
	 * @since 2.7.0
	 *
	 * @param string $template_name the template name
	 * @param string $template_path the template path
	 * @param string $located the template file
	 * @param array $args the args
	 */
	public function add_package_group_row_end( $template_name, $template_path, $located, $args ) {

		if ( 'cart/cart-shipping.php' === $template_name ) {

			if ( ! empty( $args ) && ! empty( $package = $args['package'] ) ) {

				if ( ! empty( $package['last_pickup_package'] )
				     || ! empty( $package['last_shipping_package'] ) ) {

					echo $this->add_package_wrapper_end() . '</td></tr>';
				}
			}
		}
	}


	/**
	 * Opens the new table wrapping the shipping package.
	 *
	 * @since 2.7.0
	 *
	 * @return string
	 */
	private function add_package_wrapper_start() {

		return '<table class="lpp-shipping-package-wrapper">';
	}


	/**
	 * Closes the new table wrapping the shipping package.
	 *
	 * @since 2.7.0
	 *
	 * @return string
	 */
	private function add_package_wrapper_end() {

		return '</table>';
	}


	/**
	 * Groups the shipping method label for LPP for all packages where it is the only method available.
	 *
	 * @internal
	 *
	 * @since 2.7.0
	 *
	 * @param string $located the template file
	 * @param string $template_name the template name
	 * @param array $args the args
	 * @param string $template_path the template path
	 * @param string $default_path the default path
	 * @return string
	 */
	function group_lpp_method_label( $located, $template_name, $args, $template_path, $default_path ) {

		if ( 'cart/cart-shipping.php' === $template_name ) {

			// check if LPP is the only method available
			if ( ! empty( $args )
			     && ! empty( $args['available_methods'] )
			     && 1 === count( $args['available_methods'] )
			     && wc_local_pickup_plus_shipping_method_id() === key( $args['available_methods'] )
			     && ! empty( $package = $args['package'] ) ) {

				if ( ! empty( $package['first_pickup_package'] ) ) {

					$shipping_method = $args['available_methods'][ wc_local_pickup_plus_shipping_method_id() ];

					if ( isset( $package['total_lpp_only_cost'] ) ) {

						$total_cost = $package['total_lpp_only_cost'];

						if ( $total_cost >= 0 ) {

							// update cost to display the total cost for LPP-only packages
							$shipping_method->cost = $package['total_lpp_only_cost'];

						} else {

							// update label to display the total discount for LPP-only packages
							// we need to display the discount in the label as WooCommerce does not handle negative values in the 'cost' property of a shipping rate

							/* translators: Placeholder: %s - local pickup discount amount */
							$discount = sprintf( __( '%s (discount!)', 'woocommerce-shipping-local-pickup-plus' ), wc_price( $package['total_lpp_only_cost'] ) );

							if ( ! is_rtl() ) {
								$label = trim( $shipping_method->label ) . ': ' . $discount;
							} else {
								$label = $discount . ' :' . trim( $shipping_method->label );
							}

							$shipping_method->label = $label;
						}
					}

				} else {

					// unset label and cost
					$shipping_method        = $args['available_methods'][ wc_local_pickup_plus_shipping_method_id() ];
					$shipping_method->label = '';
					$shipping_method->cost  = '';
				}
			}
		}

		return $located;
	}


	/**
	 * Render the pickup location selection box on the checkout items summary.
	 *
	 * @see Cart::add_cart_item_pickup_location_field()
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string $product_qty_html HTML intended to output the item quantity to be ordered
	 * @param array $cart_item the cart item object as array
	 * @param string $cart_item_key the cart item identifier
	 * @return string HTML
	 */
	public function add_checkout_item_pickup_location_field( $product_qty_html, $cart_item, $cart_item_key ) {

		if ( is_checkout() ) {

			$local_pickup_plus = wc_local_pickup_plus_shipping_method();

			if ( $local_pickup_plus->is_available() ) {

				$product_field     = new Cart_Item_Pickup_Location_Field( $cart_item_key );
				$product_qty_html .= $product_field->get_html();

				$handling_toggle   = new Cart_Item_Handling_Toggle( $cart_item['cart_item_key'] );
				$product_qty_html .= $handling_toggle->get_html();
			}
		}

		return $product_qty_html;
	}


	/**
	 * Outputs the pickup location information and appointments box next to pickup packages in checkout form.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param string|\WC_Shipping_Rate $shipping_rate the chosen shipping method for the package
	 * @param int|string $package_index the current package index
	 */
	public function output_pickup_package_form( $shipping_rate, $package_index ) {

		$local_pickup_plus    = wc_local_pickup_plus_shipping_method();
		$local_pickup_plus_id = $local_pickup_plus && $local_pickup_plus->is_available() ? $local_pickup_plus->get_method_id() : null;
		$package              = wc_local_pickup_plus()->get_packages_instance()->get_shipping_package( $package_index );
		$is_local_pickup      = $shipping_rate === $local_pickup_plus_id || ( $shipping_rate instanceof \WC_Shipping_Rate && $shipping_rate->method_id === $local_pickup_plus_id );

		if ( $is_local_pickup && ! array_key_exists( $package_index, self::$pickup_package_form_output ) ) {

			// record that the current package has been evaluated for the current thread
			self::$pickup_package_form_output[ $package_index ] = true;

			if ( $this->should_output_pickup_form( $package_index, $package ) ) {

				$pickup_location_field = new Package_Pickup_Location_Field( $package_index );
				$appointment_field     = new Package_Pickup_Appointment_Field( $package_index );
				$pickup_items_field    = new Package_Pickup_Items_Field( $package_index );

				$pickup_location_field->output_html();
				$appointment_field->output_html();
				$pickup_items_field->output_html();
			}
		}

		// maybe display additional disclaimer if an address was not provided yet and only Local Pickup Plus is available
		if ( $this->should_display_address_additional_disclaimer( $local_pickup_plus_id, $package ) ) {

			echo '<p>' . __( 'Enter your address to see all available shipping options.', 'woocommerce-shipping-local-pickup-plus' ) . '</p>';
		}
	}


	/**
	 * Determines if the pickup form should be output.
	 *
	 * @see Checkout::output_pickup_package_form()
	 *
	 * @since 2.8.0
	 *
	 * @param int|string $package_index package index
	 * @param array $package package data
	 * @return bool
	 */
	private function should_output_pickup_form( $package_index, $package ) {

		$do_output = false;

		$local_pickup_plus    = wc_local_pickup_plus();
		$local_pickup_plus_id = wc_local_pickup_plus_shipping_method_id();
		$chosen_methods       = WC()->session->get( 'chosen_shipping_methods', [] );
		$available_methods    = ! empty( $package['rates'] ) ? count( $package['rates'] ) : 0;

		// 1. The current package has a selected method matching Local Pickup Plus stored in session:
		if ( isset( $chosen_methods[ $package_index ] ) && $chosen_methods[ $package_index ] === $local_pickup_plus_id ) {

			$do_output = true;

		// 2. There is only one shipping method available for the current package and it matches Local Pickup Plus:
		} elseif ( 1 === $available_methods && $local_pickup_plus_id === key( $package['rates'] ) ) {

			$do_output = true;

		// 3. There's at least an item that mandates pickup but no items that require shipping (there shouldn't be other options than Local Pickup Plus to choose from):
		} elseif ( $local_pickup_plus->get_packages_instance()->is_pickup_required() && ! $local_pickup_plus->get_packages_instance()->are_shipping_and_pickup_required() ) {

			$do_output = true;

		// 4. Shipping costs are hidden until an address is entered and one pickup location per order is enabled:
		} elseif ( 'yes' === get_option( 'woocommerce_shipping_cost_requires_address' ) && $local_pickup_plus->get_shipping_method_instance()->is_per_order_selection_enabled() ) {

			$do_output =
				// if a guest user hasn't provided yet an address, show the location fields if prompted by WooCommerce, since Local Pickup Plus shouldn't be bound to shipping zones
				( 0 === get_current_user_id() && ! WC()->customer->has_calculated_shipping() )
				// if there's only one available shipping method for a pickup-enabled package, we can safely assume it should be Local Pickup Plus since it's not bound to shipping zones and it's always available
				|| ( 1 === $available_methods && $local_pickup_plus->get_packages_instance()->package_can_be_picked_up( $package ) );

			// however, if there is more than one shipping method available, and default is to ship, do not show the location fields just yet
			if ( $do_output && $available_methods > 1 && $local_pickup_plus->get_shipping_method_instance()->is_default_handling( 'ship' ) ) {
				$do_output = false;
			}
		}

		return $do_output;
	}


	/**
	 * Determines whether to show the address additional disclaimer or not.
	 *
	 * @since 2.9.3
	 *
	 * @param string $local_pickup_plus_id local pickup plus shipping method ID
	 * @param array $package shipping package
	 * @return bool true is the address disclaimer must be displayed
	 */
	private function should_display_address_additional_disclaimer( $local_pickup_plus_id, $package ) {

		return
			! empty( $package['rates'] )
			&& 1 === count( $package['rates'] )
			&& $local_pickup_plus_id === key( $package['rates'] )
			&& ! WC()->customer->has_calculated_shipping()
			&& ! wc_local_pickup_plus()->is_the_only_available_shipping_method()
			&& ! wc_local_pickup_plus()->get_packages_instance()->package_contains_must_pick_up_products_only( $package );
	}


	/**
	 * Workaround for a WC glitch which might display item details in wrong places while doing AJAX.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param array $item_details items in package meant for the current shipment
	 * @param array $package the current package array object
	 * @return array
	 */
	public function maybe_hide_pickup_package_item_details( $item_details, $package ) {

		if ( ! empty( $package['pickup_location_id'] ) || ( isset( $package['ship_via'] ) && wc_local_pickup_plus_shipping_method_id() ) ) {
			$item_details = [];
		}

		return $item_details;
	}


	/**
	 * Add a flag to mark the total number of packages meant for shipping, and the total number of packages meant for pickup.
	 *
	 * This can be useful to JS scripts that need to quickly grab the count, for example to toggle the visibility of the shipping address fields.
	 *
	 * @internal
	 *
	 * @since 2.1.1
	 */
	public function packages_count() {

		if (    true !== self::$packages_count_output
		     && is_checkout()
		     && $packages = WC()->shipping()->get_packages() ) {

			$shipping_method_id = wc_local_pickup_plus_shipping_method_id();
			$packages_to_ship   = 0;
			$packages_to_pickup = 0;

			foreach ( $packages as $package ) {
				if ( isset( $package['ship_via'] ) && in_array( $shipping_method_id, $package['ship_via'], true ) ) {
					$packages_to_pickup++;
				} else {
					$packages_to_ship++;
				}
			}

			?>
			<tr>
				<td>&nbsp;</td>
				<td>
					<input
						type="hidden"
						id="wc-local-pickup-plus-packages-to-ship"
						value="<?php echo $packages_to_ship; ?>"
					/>
					<input
						type="hidden"
						id="wc-local-pickup-plus-packages-to-pickup"
						value="<?php echo $packages_to_pickup; ?>"
					/>
				</td>
			</tr>
			<?php

			self::$packages_count_output = true;
		}
	}


	/**
	 * Ensure that cash on delivery stays enabled when there are multiple pickup packages.
	 *
	 * @internal
	 *
	 * @since 2.1.1
	 *
	 * @param array $available_gateways associative array
	 * @return array
	 */
	public function enable_cash_on_delivery( $available_gateways ) {

		// ensure we don't enable this for "add payment method" or other places we shouldn't
		// this will return true for checkout and the order pay page
		if ( is_checkout() ) {

			$local_pickup_plus = wc_local_pickup_plus_shipping_method();

			if ( ! array_key_exists( 'cod', $available_gateways ) && $local_pickup_plus && $local_pickup_plus->is_available() ) {

				/* @type \WC_Payment_Gateway $gateway */
				foreach ( WC()->payment_gateways()->payment_gateways as $gateway ) {

					if ( isset( $gateway->settings['enabled'] ) && 'yes' === $gateway->settings['enabled'] && 'WC_Gateway_COD' === get_class( $gateway ) ) {

						if ( empty( $gateway->settings['enable_for_methods'] ) || in_array( $local_pickup_plus->get_method_id(), $gateway->settings['enable_for_methods'], true ) ) {

							foreach ( WC()->shipping()->get_packages() as $key => $package ) {

								$chosen_shipping_method = wc_get_chosen_shipping_method_for_package( $key, $package );

								if ( ! empty( $package['rates'] )
								     && array_key_exists( $local_pickup_plus->get_method_id(), $package['rates'] )
								     && $chosen_shipping_method === $local_pickup_plus->get_method_id() ) {

									$available_gateways['cod'] = $gateway;
									break;
								}
							}
						}
					}
				}
			}
		}

		return $available_gateways;
	}


	/**
	 * Calculate any pickup location discounts when doing cart totals.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 */
	public function apply_pickup_discount() {

		$cart              = WC()->cart;
		$local_pickup_plus = wc_local_pickup_plus_shipping_method();

		if (      $cart->cart_contents_total > 0
		     && ! $cart->is_empty()
		     &&   $local_pickup_plus->is_available() ) {

			$packages       = WC()->shipping()->get_packages();
			$total_discount = 0;

			foreach ( $packages as $package_key => $package ) {

				$chosen_methods       = WC()->session->get( 'chosen_shipping_methods' );
				$local_pickup_plus_id = $local_pickup_plus->get_method_id();

				// skip packages not set for pickup
				if ( ! isset( $chosen_methods[ $package_key ] ) || $chosen_methods[ $package_key ] !== $local_pickup_plus_id ) {
					continue;
				}

				$chosen_location = wc_local_pickup_plus()->get_packages_instance()->get_package_pickup_location( $package );

				// address a situation where the user has a saved preferred location but this is not recorded yet to session for price adjustment calculation purposes
				if ( ! $chosen_location && isset( $package['rates'] ) && array_key_exists( $local_pickup_plus_id, $package['rates'] ) ) {

					$chosen_location = wc_local_pickup_plus_get_user_default_pickup_location();

					// sanity check: the default location cannot be used if item cannot be picked up at preferred location
					if ( $chosen_location && isset( $package['contents'] ) && is_array( $package['contents'] ) ) {

						foreach ( $package['contents'] as $item ) {

							$product = isset( $item['data'] ) ? $item['data'] : null;

							if ( ! $product || ! wc_local_pickup_plus()->get_products_instance()->product_can_be_picked_up( $product, $chosen_location ) ) {
								$chosen_location = null;
								break;
							}
						}
					}
				}

				if ( $chosen_location && isset( $package['contents_cost'] ) && $package['contents_cost'] > 0 ) {

					$package_costs    = $package['contents_cost'];
					$price_adjustment = $chosen_location->get_price_adjustment();

					if ( $price_adjustment && $price_adjustment->is_discount() ) {

						$discount_amount = $price_adjustment->get_amount( true );

						// if the discount is a percentage, then calculate over the package contents
						if ( $price_adjustment->is_percentage() ) {
							$discount_amount = $price_adjustment->get_relative_amount( $package_costs, true );
						}

						$total_discount += $discount_amount > 0 ? $discount_amount : 0;
					}
				}
			}

			if ( $total_discount > 0 ) {

				// sanity check: the total discount shouldn't amount to more than the total cart costs, although WooCommerce wouldn't let a new order to have a negative total
				$total_discount = $total_discount >= $cart->cart_contents_total ? $cart->cart_contents_total : $total_discount;

				// normalize discount as fee amount (necessary in case of tax inclusive shops)
				$total_discount = $this->get_pickup_discount_fee_amount( $total_discount );

				WC()->cart->add_fee(
					/* translators: Placeholder: %s - shipping method title (e.g. Local Pickup) */
					sprintf( __( '%s discount', 'woocommerce-shipping-local-pickup-plus' ), $local_pickup_plus->get_method_title() ),
					"-{$total_discount}",
					false // discounts shouldn't be taxable regardless of tax settings
				);
			}
		}
	}


	/**
	 * Normalizes a pickup discount amount as a fee amount to apply to the cart.
	 *
	 * If the prices are tax inclusive and we're displaying prices including taxes, we need some special handling, otherwise the discount will include these while applying the fee below.
	 * The trick is to pass to WooCommerce a number that will be turned into the desired discount after tax is applied to it.
	 * For this purpose, we will consider our discount fee tax inclusive and apply reverse taxes to remove it from the discount amount.
	 *
	 * @since 2.7.4
	 *
	 * @param int|float $pickup_discount discount amount
	 * @return int|float the same discount amount, which may be altered in case of tax inclusive settings
	 */
	private function get_pickup_discount_fee_amount( $pickup_discount ) {

		if ( wc_prices_include_tax() && 'incl' === get_option( 'woocommerce_tax_display_cart' ) ) {

			$cart_content_taxes = WC()->cart->get_cart_contents_taxes();

			if ( ! empty( $cart_content_taxes ) ) {

				$tax_rates  = [];
				$compound   = false;
				$percentage = 0;

				foreach ( array_keys( $cart_content_taxes ) as $tax_id ) {

					$tax_rate = \WC_Tax::_get_tax_rate( $tax_id );

					/** normalize taxes as expected by {@see \WC_Tax::calc_inclusive_tax()} */
					foreach ( (array) $tax_rate as $key => $data ) {

						if ( 'tax_rate' === $key ) {
							$key = 'rate';
						} else {
							$key = str_replace( 'tax_rate_', '', $key );
						}

						if ( in_array( $key, [ 'compound', 'shipping' ], true ) ) {

							$value = wc_bool_to_string( $data );

							// set a flag so we know there are compound rates used
							if ( ! $compound && 'compound' === $key && 'yes' === $value ) {
								$compound = true;
							}

						} else {

							$value = $data;

							if ( 'rate' === $key && $value && (float) $value > 0 ) {
								$percentage += (float) $value / 100;
							}
						}

						$tax_rates[ $tax_id ][ $key ] = $value;
					}
				}

				// when compound taxes are found, we use WooCommerce internals to calculate the taxes out of the tax-inclusive discount amount
				if ( $compound ) {

					// we need to apply a filter momentarily to avoid some rounding errors
					add_filter( 'woocommerce_tax_round', [ $this, 'round_pickup_discount' ], 1, 2 );

					$tax_amounts = \WC_Tax::calc_tax( $pickup_discount, $tax_rates, true );

					// remove our internal filter to restore normal behavior for tax rounding calculations
					remove_filter( 'woocommerce_tax_round', [ $this, 'round_pickup_discount' ], 1 );

					// this is just a sum of all the applied taxes subtracted from pickup discount: they will be applied again when the fee is added to cart below
					$pickup_discount -= \WC_Tax::get_tax_total( $tax_amounts );

				// when no compound taxes are found, the calculation is much simpler with a reverse percentage
				} else {

					$reverse_percentage = 1 + $percentage;

					$pickup_discount /= $reverse_percentage;
				}
			}
		}

		return $pickup_discount;
	}


	/**
	 * Adjusts the rounded pickup discount amount to fix rounding errors with tax inclusive settings.
	 *
	 * Taxes are rounded by default to four decimal points but this is not the same rounding as in cart calculations, where our pickup discount is applied.
	 * This method filters {@see \WC_Tax::round()} to use the normal decimal precision rather than the tax rounding precision.
	 * @see \WC_Tax::calc_inclusive_tax()
	 *
	 * @since 2.7.4
	 *
	 * @param int|float $pickup_discount the discount parsed by WooCommerce internals
	 * @param int|float $original_amount the original pickup discount passed to the WooCommerce tax rounding method
	 * @return float
	 */
	public function round_pickup_discount( $pickup_discount, $original_amount ) {

		return round( $original_amount, wc_get_price_decimals() );
	}


	/**
	 * Validate local pickup order upon checkout.
	 *
	 * The exceptions are converted into customer error notices by WooCommerce.
	 *
	 * @internal
	 *
	 * @since 2.0.0
	 *
	 * @param array $posted_data checkout data (does not include package data, see $_POST)
	 * @throws \Exception
	 */
	public function validate_checkout( $posted_data ) {

		$local_pickup_method = wc_local_pickup_plus_shipping_method();
		$shipping_methods    = isset( $posted_data['shipping_method'] ) ? (array) $posted_data['shipping_method'] : [];
		$exception_message   = '';

		// check if there are any packages meant for local pickup
		if ( $local_pickup_packages = ! empty( $shipping_methods ) ? array_keys( $shipping_methods, $local_pickup_method->get_method_id() ) : null ) {

			$pickup_location_ids = isset( $_POST['_shipping_method_pickup_location_id'] ) ? $_POST['_shipping_method_pickup_location_id'] : [];
			$pickup_dates        = isset( $_POST['_shipping_method_pickup_date'] )        ? $_POST['_shipping_method_pickup_date']        : [];
			$appointment_offsets = Framework\SV_WC_Helper::get_posted_value( '_shipping_method_pickup_appointment_offset', [] );

			foreach ( $local_pickup_packages as $package_id ) {

				$error_messages = [];

				// a pickup location has not been chosen:
				if ( empty( $pickup_location_ids[ $package_id ] ) ) {
					/* translators: Placeholder: %s - user assigned name for Local Pickup Plus shipping method */
					$error_messages['pickup_location_id'] = sprintf( __( 'Please select a pickup location if you intend to use %s as shipping method.', 'woocommerce-shipping-local-pickup-plus' ), $local_pickup_method->get_method_title() );
				}

				// a pickup date has not been set, but it's mandatory:
				if ( empty( $pickup_dates[ $package_id ] ) && 'required' === $local_pickup_method->pickup_appointments_mode() ) {
					/* translators: Placeholder: %s - user assigned name for Local Pickup Plus shipping method */
					$error_messages['pickup_date'] = sprintf( __( 'Please choose a date to schedule a pickup when using %s shipping method.', 'woocommerce-shipping-local-pickup-plus' ), $local_pickup_method->get_method_title() );
				}

				// the selected appointment time is no longer available:
				if ( ! empty( $pickup_location_ids[ $package_id ] ) && ! empty( $pickup_dates[ $package_id ] ) ) {

					$appointment_offset = ! empty( $appointment_offsets[ $package_id ] ) ? (int) $appointment_offsets[ $package_id ] : 0;

					try {

						$pickup_location = wc_local_pickup_plus_get_pickup_location( (int) $pickup_location_ids[ $package_id ] );

						$now        = new \DateTime();
						$start_date = new \DateTime( date( 'Y-m-d H:i:s', strtotime( $pickup_dates[ $package_id ] ) + $appointment_offset ), $pickup_location->get_address()->get_timezone() );

						$appointment_duration = $pickup_location->get_appointments()->get_appointment_duration( $start_date );
						$end_date             = ( clone $start_date )->modify( sprintf( "+ %d seconds", $appointment_duration ) );

						if ( ! wc_local_pickup_plus()->get_appointments_instance()->is_appointment_time_available( $now, $pickup_location, $appointment_duration, $start_date, $end_date ) ) {

							// remove selected pickup date and time so that only available times are shown
							$data_store = new Package_Pickup_Data( $package_id );

							$data_store->set_pickup_data( array_merge( $data_store->get_pickup_data(), [
								'pickup_date'        => '',
								'appointment_offset' => ''
							] ) );

							// force WooCommerce to refresh checkout totals and render the appointment field again
							WC()->session->set( 'refresh_totals', true );

							throw new Framework\SV_WC_Plugin_Exception( 'Appointment time not available' );
						}

					} catch ( \Exception $e ) {

						$error_messages['pickup_time'] = __( 'Oops! That appointment time is no longer available. Please select a new appointment.', 'woocommerce-shipping-local-pickup-plus' );
					}
				}

				/**
				 * Filter validation of pickup errors at checkout.
				 *
				 * @since 2.0.0
				 *
				 * @param array $errors associative array of errors and predefined messages - leave empty to pass validation
				 * @param int|string $package_key the current package key for the package being evaluated for pickup data
				 * @param array $posted_data posted data incoming from form submission
				 */
				$error_messages = apply_filters( 'wc_local_pickup_plus_validate_pickup_checkout', $error_messages, $package_id, $posted_data );

				if ( ! empty( $error_messages ) && is_array( $error_messages ) ) {
					$exception_message = implode( '<br />', $error_messages );
				}
			}

			// set the user preferred pickup location (we can choose only one)
			if ( ! empty( $pickup_location_ids ) && is_array( $pickup_location_ids ) ) {

				$pickup_location_id = current( $pickup_location_ids );

				if ( is_numeric( $pickup_location_id ) ) {
					wc_local_pickup_plus_set_user_default_pickup_location( $pickup_location_id );
				}
			}
		}

		if ( '' !== $exception_message ) {
			throw new \Exception( $exception_message );
		} elseif ( $session = wc_local_pickup_plus()->get_session_instance() ) {
			$session->delete_default_handling();
		}
	}


}

class_alias( Checkout::class, 'WC_Local_Pickup_Plus_Checkout' );
