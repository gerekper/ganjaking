<?php
/**
 * WC_CSP_Restrict_Shipping_Methods class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Restrict Shipping Methods.
 *
 * @class    WC_CSP_Restrict_Shipping_Methods
 * @version  1.7.7
 */
class WC_CSP_Restrict_Shipping_Methods extends WC_CSP_Restriction implements WC_CSP_Checkout_Restriction {

	public function __construct() {

		$this->id                       = 'shipping_methods';
		$this->title                    = __( 'Shipping Methods', 'woocommerce-conditional-shipping-and-payments' );
		$this->description              = __( 'Restrict the available shipping methods based on product-related constraints.', 'woocommerce-conditional-shipping-and-payments' );
		$this->validation_types         = array( 'checkout' );
		$this->has_admin_product_fields = true;
		$this->supports_multiple        = true;

		$this->has_admin_global_fields  = true;
		$this->method_title             = __( 'Shipping Method Restrictions', 'woocommerce-conditional-shipping-and-payments' );
		$this->restricted_key           = 'methods';

		// Add required variables to shipping packages.
		add_action( 'woocommerce_checkout_update_order_review', array( $this, 'add_variables_to_packages_on_order_review' ), 10 );

		// Remove shipping methods from packages.
		add_action( 'woocommerce_package_rates', array( $this, 'exclude_package_shipping_methods' ), 10, 2 );

		// Save global settings.
		add_action( 'woocommerce_update_options_restrictions_' . $this->id, array( $this, 'update_global_restriction_data' ) );

		// Initialize global settings.
		$this->init_form_fields();

		// Display shipping method options.
		add_action( 'woocommerce_csp_admin_shipping_method_option', array( $this, 'shipping_method_option' ), 10, 3 );

		// Shows a woocommerce error on the 'woocommerce_review_order_before_cart_contents' hook when shipping method restrictions apply.
		add_action( 'woocommerce_review_order_before_cart_contents', array( $this, 'excluded_shipping_methods_notice' ) );

		// Update checkout fields/totals on changing the State.
		add_filter( 'woocommerce_default_address_fields', array( $this, 'update_totals_on_state_change' ) );

		// Display notice after each excluded shipping rate.
		add_action( 'woocommerce_after_shipping_rate', array( $this, 'add_notice_after_excluded_shipping_rate' ), 100, 2 );
	}

	/**
	 * Filters shipping packages when refreshing order details to include checkout variables that are conditionally used to exclude rates.
	 * For example, the billing-email condition only kicks in at checkout and must be added to shipping package data to modify their hash.
	 *
	 * @since  1.4.0
	 */
	public function add_variables_to_packages_on_order_review() {
		add_filter( 'woocommerce_cart_shipping_packages', array( $this, 'add_variables_to_packages' ), 100 );
	}

	/**
	 * Adds extra variables to shipping packages.
	 *
	 * @since  1.4.0
	 * @param  array  $packages
	 */
	public function add_variables_to_packages( $packages ) {

		$variables = [];

		// Add billing email.
		if ( isset( $_POST[ 'post_data' ] ) ) {
			parse_str( $_POST[ 'post_data' ], $billing_data );
			if ( is_array( $billing_data ) && isset( $billing_data[ 'billing_email' ] ) ) {
				$variables[ 'billing_email' ] = wc_clean( $billing_data[ 'billing_email' ] );
			}
		}

		foreach ( $packages as $package_key => $package ) {
			foreach ( $variables as $key => $value ) {
				WC_CSP_Restriction::add_extra_package_variable( $packages[ $package_key ], $key, $value );
			}
		}

		return $packages;
	}

	/**
	 * Update checkout fields/totals on changing the State field.
	 *
	 * @param  array  $fields
	 * @return array
	 */
	public function update_totals_on_state_change( $fields ) {

		if ( isset( $fields[ 'state' ][ 'class' ] ) ) {
			if ( false === array_search( 'update_totals_on_change', $fields[ 'state' ][ 'class' ] ) ) {
				$fields[ 'state' ][ 'class' ][] = 'update_totals_on_change';
			}
		}

		return $fields;
	}

	/**
	 * Render notice after excluded shipping rates.
	 *
	 * @since  1.7.0
	 * @param  WC_Shipping_Rate  $rate
	 * @param  int               $index
	 */
	public function add_notice_after_excluded_shipping_rate( $rate, $index ) {

		$rate_id           = WC_CSP_Core_Compatibility::is_wc_version_gte( '3.2' ) ? $rate->get_id() : $rate->id;
		$canonical_rate_id = $rate_id;

		if ( WC_CSP_Core_Compatibility::is_wc_version_gte( '3.2' ) ) {

			$method_id   = $rate->get_method_id();
			$instance_id = $rate->get_instance_id();

			if ( $method_id && $instance_id ) {
				$canonical_rate_id = $method_id . ':' . $instance_id;
			}
		}

		$result = $this->validate_checkout( array(
			'check_package_index' => $index,
			'check_rate'          => $rate_id
		) );

		// Try again if the canonical rate id is not the same as the rate id.
		if ( ! $result->has_messages() && $rate_id !== $canonical_rate_id ) {

			$result = $this->validate_checkout( array(
				'check_package_index' => $index,
				'check_rate'          => $canonical_rate_id
			) );
		}

		if ( $result->has_messages() ) {

			/**
			 * 'woocommerce_csp_shipping_notice_classes' filter.
			 *
			 * @since  1.7.0
			 *
			 * @param  array             $classes
			 * @param  WC_Shipping_Rate  $rate
			 * @param  int               $index
			 */
			$classes = apply_filters( 'woocommerce_csp_restricted_shipping_rate_notice_classes', array( 'woocommerce-info', 'csp-shipping-rate-notice' ), $rate, $index );

			foreach ( $result->get_messages() as $message ) {
				echo '<div class="' . implode( ' ', $classes ) . '" style="margin: 1em 0;">' . $message[ 'text' ] . '</div>';
			}
		}
	}

	/**
	 * Display shipping method options.
	 *
	 * @param  string              $method_id
	 * @param  WC_Shipping_Method  $method
	 * @param  array               $selected_methods
	 * @return void
	 */
	public function shipping_method_option( $method_id, $method, $selected_methods ) {

		global $wpdb;

		if ( $method->supports( 'shipping-zones' ) ) {

			echo '<optgroup label="' . $method->get_method_title() . '">';
			echo '<option value="' . esc_attr( $method_id ) . '" ' . selected( in_array( $method_id, $selected_methods ), true, false ) . '>' . sprintf( __( 'All &quot;%s&quot; Method Instances', 'woocommerce-conditional-shipping-and-payments' ), $method->get_method_title() ) . '</option>';

			$zones = WC_Shipping_Zones::get_zones();

			if ( ! isset( $zones[ 0 ] ) ) {
				$rest_of_world = WC_Shipping_Zones::get_zone_by();
				$zones[ 0 ]                       = $rest_of_world->get_data();
				$zones[ 0 ][ 'shipping_methods' ] = $rest_of_world->get_shipping_methods();
			}

			foreach ( $zones as $zone ) {

				if ( ! empty( $zone[ 'shipping_methods' ] ) ) {

					$zone_name = $zone[ 'zone_name' ];

					foreach ( $zone[ 'shipping_methods' ] as $instance_id => $method_instance ) {

						if ( $method_instance->id !== $method->id ) {
							continue;
						}

						$option_id    = $method_instance->get_rate_id();
						$method_title = sprintf( __( '&quot;%1$s&quot; (Instance ID: %2$s)', 'woocommerce-conditional-shipping-and-payments' ), $method_instance->get_title(), $instance_id );
						$option_name  = sprintf( __( '%1$s &ndash; %2$s', 'woocommerce-conditional-shipping-and-payments' ), $zone_name, $method_title );

						echo '<option value="' . $option_id . '" ' . selected( in_array( $option_id, $selected_methods ), true, false ) . '>' . $option_name . '</option>';
					}
				}
			}

			echo '</optgroup>';

		} else {

			if ( $method_id === 'legacy_flat_rate' ) {

				echo '<optgroup label="' . __( 'Flat Rates (Legacy)', 'woocommerce-conditional-shipping-and-payments' ) . '">';
				echo '<option value="' . esc_attr( $method_id ) . '" ' . selected( in_array( $method_id, $selected_methods ), true, false ) . '>' . $method->get_title() . __( ' (Legacy)', 'woocommerce-conditional-shipping-and-payments' ) . '</option>';
				$this->additional_legacy_flat_rate_options( $method, $selected_methods );
				echo '</optgroup>';

			} else {

				$is_legacy = ( 0 === strpos( $method_id, 'legacy_' ) );
				$option    = '<option value="' . esc_attr( $method_id ) . '" ' . selected( in_array( $method_id, $selected_methods ), true, false ) . '>' . $method->get_title() . ( $is_legacy ? __( ' (Legacy)', 'woocommerce-conditional-shipping-and-payments' ) : '' ) . '</option>';
				echo apply_filters( 'woocommerce_csp_admin_shipping_method_option_default', $option, $method_id, $method, $selected_methods );
			}
		}
	}

	/**
	 * Append additional legacy flat rate options.
	 *
	 * @param  WC_Shipping_Method  $method
	 * @param  array               $selected_methods
	 * @return void
	 */
	private function additional_legacy_flat_rate_options( $method, $selected_methods ) {

		$additional_flat_rate_options = (array) explode( "\n", $method->get_option( 'options' ) );

		foreach ( $additional_flat_rate_options as $option ) {

			$this_option = array_map( 'trim', explode( WC_DELIMITER, $option ) );

			if ( sizeof( $this_option ) !== 3 ) {
				continue;
			}

			$option_id = 'legacy_flat_rate:' . urldecode( sanitize_title( $this_option[0] ) );

			echo '<option value="' . esc_attr( $option_id ) . '" ' . selected( in_array( $option_id, $selected_methods ), true, false ) . '>' . $this_option[0] . __( ' (Legacy)', 'woocommerce-conditional-shipping-and-payments' ) . '</option>';
		}
	}

	/**
	 * Append additional flat rate options.
	 *
	 * @param  WC_Shipping_Method  $method
	 * @param  array               $selected_methods
	 * @return void
	 */
	private function additional_flat_rate_options( $method, $selected_methods ) {

		$additional_flat_rate_options = (array) explode( "\n", $method->get_option( 'options' ) );

		foreach ( $additional_flat_rate_options as $option ) {

			$this_option = array_map( 'trim', explode( WC_DELIMITER, $option ) );

			if ( sizeof( $this_option ) !== 3 ) {
				continue;
			}

			$option_id = 'flat_rate:' . urldecode( sanitize_title( $this_option[0] ) );

			echo '<option value="' . esc_attr( $option_id ) . '" ' . selected( in_array( $option_id, $selected_methods ), true, false ) . '>' . $this_option[0] . '</option>';
		}
	}

	/**
	 * Declare 'admin_global_fields' type, generated by 'generate_admin_global_fields_html'.
	 *
	 * @return void
	 */
	function init_form_fields() {

		$this->form_fields = array(
			'admin_global_fields' => array(
				'type' => 'admin_global_fields'
				)
			);
	}

	/**
	 * Generates the 'admin_global_fields' field type, which is based on metaboxes.
	 *
	 * @return string
	 */
	function generate_admin_global_fields_html() {
		?><p>
			<?php echo __( 'Restrict the shipping methods available at checkout. Complex rules can be created by adding multiple restrictions. Each individual restriction becomes active when all defined conditions match.', 'woocommerce-conditional-shipping-and-payments' ); ?>
		</p><?php

		$this->get_admin_global_metaboxes_html();
	}

	/**
	 * Display options on the product Restrictions write-panel.
	 *
	 * All fields placed inside an indexed 'restriction[ $index ]' array will be passed to the 'process_admin_product_fields' function for validation.
	 *
	 * @param  int     $index
	 * @param  array   $options
	 * @param  string  $field_type
	 * @return string
	 */
	public function get_admin_fields_html( $index, $options = array(), $field_type = 'global' ) {

		$description           = '';
		$methods               = array();
		$custom_rates_input    = '';
		$message               = '';
		$show_excluded         = false;
		$show_excluded_notices = false;

		if ( isset( $options[ 'description' ] ) ) {
			$description = $options[ 'description' ];
		}

		if ( isset( $options[ 'methods' ] ) ) {
			$methods = $options[ 'methods' ];
		}

		if ( ! empty( $options[ 'message' ] ) ) {
			$message = $options[ 'message' ];
		}

		if ( isset( $options[ 'custom_rates' ] ) ) {
			$custom_rates = $options[ 'custom_rates' ];

			if ( is_array( $custom_rates ) ) {

				// Escape delimiter before impoding.
				foreach ( $custom_rates as $i => $rate_id ) {
					if ( strpos( $rate_id, WC_DELIMITER ) ) {
						$custom_rates[ $i ] = str_replace( WC_DELIMITER, '%' . WC_DELIMITER . '%', $rate_id );
					}
				}

				$custom_rates_input = esc_attr( implode( ' ' . WC_DELIMITER . ' ', $custom_rates ) );
			}
		}

		if ( isset( $options[ 'show_excluded' ] ) && $options[ 'show_excluded' ] === 'yes' ) {
			$show_excluded = true;
		}

		if ( isset( $options[ 'show_excluded_notices' ] ) && $options[ 'show_excluded_notices' ] === 'yes' ) {
			$show_excluded_notices = true;
		}

		$shipping_methods = WC()->shipping->load_shipping_methods();

		?>
		<div class="woocommerce_restriction_form">
			<div class="sw-form-field">
				<label for="short_description">
					<?php _e( 'Short Description', 'woocommerce-conditional-shipping-and-payments' ); ?>
				</label>
				<div class="sw-form-content">
					<input class="short_description" name="restriction[<?php echo $index; ?>][description]" id="restriction_<?php echo $index; ?>_message" placeholder="<?php _e( 'Optional short description for this rule&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?>" value="<?php echo $description; ?>" />
				</div>
			</div>
			<div class="sw-form-field">
				<label><?php _e( 'Exclude Methods', 'woocommerce-conditional-shipping-and-payments' ); ?></label>
				<div class="sw-form-content">
					<select name="restriction[<?php echo $index; ?>][methods][]" class="multiselect sw-select2" data-wrap="yes" multiple="multiple" data-placeholder="<?php _e( 'Select Shipping Methods&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?>">
						<?php
							foreach ( $shipping_methods as $key => $val ) {
								do_action( 'woocommerce_csp_admin_shipping_method_option', $key, $val, $methods );
							}
						?>
					</select>
				</div>
			</div>
			<div class="sw-form-field">
				<label><?php _e( 'Exclude Rate IDs', 'woocommerce-conditional-shipping-and-payments' ); ?></label>
				<div class="sw-form-content">
					<input type="text" name="restriction[<?php echo $index; ?>][custom_rates]" placeholder="<?php _e( 'Shipping rate IDs to exclude, separated by &quot;|&quot;&hellip;', 'woocommerce-conditional-shipping-and-payments' ); ?>" value="<?php echo $custom_rates_input; ?>"/>
					<?php echo WC_CSP_Core_Compatibility::wc_help_tip( __( 'Manually enter shipping rate IDs to exclude, separated by "|". If a rate includes the "|" character, replace it with "%|%". Useful if you are working with shipping methods that retrieve real-time rates, or if you simply require more granular control of the shipping options available during checkout. <strong>Important</strong>: Real-time rate IDs may change over time &ndash; use at your own risk!', 'woocommerce-conditional-shipping-and-payments' ) ); ?>
				</div>
			</div>
			<div class="sw-form-field sw-form-field--checkbox">
				<label>
					<?php _e( 'Show Excluded', 'woocommerce-conditional-shipping-and-payments' ); ?>
				</label>
				<div class="sw-form-content">
					<input type="checkbox" class="checkbox show_excluded_in_checkout" name="restriction[<?php echo $index; ?>][show_excluded]" <?php echo $show_excluded ? 'checked="checked"' : ''; ?>>
					<?php echo WC_CSP_Core_Compatibility::wc_help_tip( __( 'By default, excluded shipping methods are removed from the list of methods available during checkout. Select this option if you prefer to show excluded shipping methods in the checkout options and display a restriction notice when customers attempt to complete an order using an excluded shipping method.', 'woocommerce-conditional-shipping-and-payments' ) ); ?>
				</div>
			</div>
			<div class="sw-form-field show-excluded-checked" style="<?php echo false === $show_excluded ? 'display:none;' : ''; ?>">
				<label>
					<?php _e( 'Custom Notice', 'woocommerce-conditional-shipping-and-payments' ); ?>
					<?php

						if ( $field_type === 'global' ) {
							$tiptip = __( 'Defaults to:<br/>&quot;Unfortunately, your order cannot be shipped via {excluded_method}. To complete your order, please select an alternative shipping method.&quot;<br/>When conditions are defined, resolution instructions are added to the default message.', 'woocommerce-conditional-shipping-and-payments' );
						} else {
							$tiptip = __( 'Defaults to:<br/>&quot;Unfortunately, {product} is not eligible for shipping via {excluded_method}. To complete your order, please select an alternative shipping method, or remove {product} from your cart.&quot;<br/>When conditions are defined, resolution instructions are added to the default message.', 'woocommerce-conditional-shipping-and-payments' );
						}
					?>
				</label>
				<div class="sw-form-content">
					<textarea class="custom_message" name="restriction[<?php echo $index; ?>][message]" id="restriction_<?php echo $index; ?>_message" placeholder="" rows="2" cols="20"><?php echo $message; ?></textarea>
					<?php
						echo WC_CSP_Core_Compatibility::wc_help_tip( $tiptip );

						if ( $field_type === 'global' ) {
							$tip = __( 'Custom notice to display when attempting to place an order while this restriction is active. You may include <code>{excluded_method}</code> and have it substituted by the selected shipping method title.', 'woocommerce-conditional-shipping-and-payments' );
						} else {
							$tip = __( 'Custom notice to display when attempting to place an order while this restriction is active. You may include <code>{product}</code> and <code>{excluded_method}</code> and have them substituted by the actual product title and the selected shipping method title.', 'woocommerce-conditional-shipping-and-payments' );
						}

						echo '<span class="description">' . $tip . '</span>';
					?>
				</div>
			</div>
			<div class="sw-form-field sw-form-field--checkbox show-excluded-checked" style="<?php echo false === $show_excluded ? 'display:none;' : ''; ?>">
				<label>
					<?php _e( 'Show Static Notices', 'woocommerce-conditional-shipping-and-payments' ); ?>
				</label>
				<div class="sw-form-content">
					<input type="checkbox" class="checkbox show_excluded_notices_in_checkout" name="restriction[<?php echo $index; ?>][show_excluded_notices]" <?php echo $show_excluded_notices ? 'checked="checked"' : ''; ?>>
					<?php echo WC_CSP_Core_Compatibility::wc_help_tip( __( 'By default, when <strong>Show Excluded</strong> is enabled, a notice is displayed when customers attempt to place an order using a restricted shipping method. Select this option if you also want to display a static notice under each restricted shipping method.', 'woocommerce-conditional-shipping-and-payments' ) ); ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Display a short summary of the restriction's settings.
	 *
	 * @param  array  $options
	 * @return string
	 */
	public function get_options_description( $options ) {

		if ( ! empty( $options[ 'description' ] ) ) {
			return $options[ 'description' ];
		}

		$method_descriptions = array();
		$methods             = array();

		if ( isset( $options[ 'methods' ] ) ) {
			$methods = $options[ 'methods' ];
		}

		$shipping_methods = WC()->shipping->load_shipping_methods();

		foreach ( $shipping_methods as $key => $val ) {

			if ( in_array( $key, $methods ) ) {
				$method_descriptions[] = $val->get_method_title();
			} else {
				foreach ( $methods as $restricted_method ) {
					if ( 0 === strpos( $restricted_method, $key ) ) {
						$method_descriptions[] = $val->get_method_title() . ' (' . $restricted_method . ')';
					}
				}
			}

			if ( $key === 'legacy_flat_rate' ) {

				$additional_flat_rate_options = (array) explode( "\n", $val->get_option( 'options' ) );

				foreach ( $additional_flat_rate_options as $option ) {

					$this_option = array_map( 'trim', explode( WC_DELIMITER, $option ) );

					if ( sizeof( $this_option ) !== 3 ) {
						continue;
					}

					$option_id = 'legacy_flat_rate:' . urldecode( sanitize_title( $this_option[0] ) );

					if ( in_array( $option_id, $methods ) ) {
						$method_descriptions[] = $this_option[0];
					}
				}
			}
		}

		return trim( implode( ', ', $method_descriptions ), ', ' );
	}

	/**
	 * Display options on the global Restrictions write-panel.
	 *
	 * @param  int     $index    restriction fields array index
	 * @param  string  $options  metabox options
	 * @return string
	 */
	function get_admin_global_fields_html( $index, $options = array() ) {

		$this->get_admin_fields_html( $index, $options, 'global' );
	}

	/**
	 * Display options on the product Restrictions write-panel.
	 *
	 * @param  int     $index    restriction fields array index
	 * @param  string  $options  metabox options
	 * @return string
	 */
	function get_admin_product_fields_html( $index, $options = array() ) {
		?><div class="restriction-description">
			<?php echo __( 'Restrict the available shipping methods when a shipping package contains this product.', 'woocommerce-conditional-shipping-and-payments' ); ?>
		</div><?php

		$this->get_admin_fields_html( $index, $options, 'product' );
	}

	/**
	 * Validate, process and return product options.
	 *
	 * @see get_admin_product_fields_html
	 *
	 * @param  array  $posted_data
	 * @return array
	 */
	public function process_admin_fields( $posted_data ) {

		$processed_data = array();

		$processed_data[ 'methods' ]      = array();
		$processed_data[ 'custom_rates' ] = array();

		if ( ! empty( $posted_data[ 'custom_rates' ] ) ) {

			// If there's a delimiter escape it for the explode to work.
			if ( strpos( $posted_data[ 'custom_rates' ], '%' . WC_DELIMITER . '%' ) ) {
				$posted_data[ 'custom_rates' ] = str_replace( '%' . WC_DELIMITER . '%', '%d%', $posted_data[ 'custom_rates' ] );
			}

			// Explode.
			$processed_data[ 'custom_rates' ] = array_unique( array_map( 'wc_clean', explode( WC_DELIMITER, $posted_data[ 'custom_rates' ] ) ) );

			// Revert delimiter value.
			foreach ( $processed_data[ 'custom_rates' ] as $i => $rate_id ) {
				if ( strpos( $rate_id, '%d%' ) ) {
					$processed_data[ 'custom_rates' ][ $i ] = str_replace( '%d%', WC_DELIMITER, $rate_id );
				}
			}

			$processed_data[ 'methods' ] = $processed_data[ 'custom_rates' ];
		}

		if ( ! empty( $posted_data[ 'methods' ] ) ) {
			$processed_data[ 'methods' ] = array_unique( array_merge( $processed_data[ 'methods' ], array_map( 'stripslashes', $posted_data[ 'methods' ] ) ) );
		} else {
			if ( empty( $processed_data[ 'methods' ] ) ) {
				return false;
			}
		}

		if ( isset( $posted_data[ 'show_excluded' ] ) ) {
			$processed_data[ 'show_excluded' ] = 'yes';
		}

		if ( isset( $posted_data[ 'show_excluded_notices' ] ) ) {
			$processed_data[ 'show_excluded_notices' ] = 'yes';
		}

		if ( ! empty( $posted_data[ 'message' ] ) ) {
			$processed_data[ 'message' ] = wp_kses_post( stripslashes( $posted_data[ 'message' ] ) );
		}

		if ( ! empty( $posted_data[ 'description' ] ) ) {
			$processed_data[ 'description' ] = strip_tags( stripslashes( $posted_data[ 'description' ] ) );
		}

		return $processed_data;
	}

	/**
	 * Validate, process and return product metabox options.
	 *
	 * @param  array  $posted_data
	 * @return array
	 */
	public function process_admin_product_fields( $posted_data ) {

		$processed_data = $this->process_admin_fields( $posted_data );

		if ( ! $processed_data ) {

			WC_Admin_Meta_Boxes::add_error( sprintf( __( 'Restriction #%s was not saved. Before saving a &quot;Shipping Method&quot; restriction, remember to add at least one shipping method to the exclusions list.', 'woocommerce-conditional-shipping-and-payments' ), $posted_data[ 'index' ] ) );
			return false;
		}

		return $processed_data;
	}

	/**
	 * Validate, process and return global settings.
	 *
	 * @param  array  $posted_data
	 * @return array
	 */
	public function process_admin_global_fields( $posted_data ) {

		$processed_data = $this->process_admin_fields( $posted_data );

		if ( ! $processed_data ) {

			WC_CSP_Admin_Notices::add_notice( sprintf( __( 'Restriction #%s was not saved. Before saving a &quot;Shipping Method&quot; restriction, remember to add at least one shipping method to the exclusions list.', 'woocommerce-conditional-shipping-and-payments' ), $posted_data[ 'index' ] ), 'error', true );
			return false;
		}

		return $processed_data;
	}

	/**
	 * Shows a woocommerce error on the 'woocommerce_review_order_before_cart_contents' hook when shipping method restrictions apply.
	 *
	 * @return void
	 */
	public function excluded_shipping_methods_notice() {

		if ( ! defined( 'WOOCOMMERCE_CHECKOUT' ) ) {
			return;
		}

		if ( ! apply_filters( 'woocommerce_csp_validate_shipping_method_on_update_order_review', false ) ) {
			return;
		}

		$result = $this->validate_checkout( array() );

		if ( $result->has_messages() ) {
			foreach ( $result->get_messages() as $message ) {
				wc_add_notice( $message[ 'text' ], $message[ 'type' ] );
			}
		}
	}

	/**
	 * Return excluded rate IDs.
	 *
	 * @param  array  $rates
	 * @param  array  $excluded_rates
	 * @return array
	 */
	private function get_restricted_rates( $rates, $excluded_rates ) {

		$restricted_rate_ids = array();

		if ( ! empty( $rates ) && ! empty( $excluded_rates ) ) {

			foreach ( $rates as $rate_id => $rate ) {
				if ( $this->is_restricted( $rate_id, $excluded_rates, $rate ) ) {
					$restricted_rate_ids[] = $rate_id;
				}
			}
		}

		return $restricted_rate_ids;
	}

	/**
	 * True if a rate is excluded.
	 *
	 * @param  string                  $rate_id
	 * @param  array                   $excluded_rates
	 * @param  WC_Shipping_Rate|false  $rate
	 * @return bool
	 */
	private function is_restricted( $rate_id, $excluded_rates, $rate = false ) {

		$is_restricted              = false;
		$legacy_flat_rate_method_id = 'legacy_flat_rate';

		foreach ( $excluded_rates as $excluded_rate_id ) {

			if ( (string) $rate_id === (string) $excluded_rate_id ) {

				$is_restricted = true;
				break;

			} elseif ( $excluded_rate_id !== $legacy_flat_rate_method_id && 0 === strpos( $rate_id, $excluded_rate_id ) && in_array( substr( $rate_id, strlen( $excluded_rate_id ), 1 ), array( ':', '-' ) ) ) {

				$is_restricted = true;
				break;

			} elseif ( is_object( $rate ) && WC_CSP_Core_Compatibility::is_wc_version_gte( '3.2' ) ) {

				$method_id   = $rate->get_method_id();
				$instance_id = $rate->get_instance_id();

				// When a rate is mapped to a known method ID and instance ID (attached to specific Shipping Zones), attempt to construct & evaluate its canonical rate ID.
				if ( $method_id && $instance_id ) {

					$canonical_rate_id = $method_id . ':' . $instance_id;

					if ( self::is_restricted( $canonical_rate_id, $excluded_rates ) ) {
						$is_restricted = true;
						break;
					}
				}
			}
		}

		return $is_restricted;
	}

	/**
	 * Remove shipping methods from packages.
	 *
	 * @return bool
	 */
	public function exclude_package_shipping_methods( $rates, $package ) {

		// Initialize parameters.
		$args = array(
			'package' => $package
		);
		$maps = array();

		/* ----------------------------------------------------------------- */
		/* Product Restrictions
		/* ----------------------------------------------------------------- */

		// Loop package contents.
		if ( ! empty( $package[ 'contents' ] ) ) {
			foreach ( $package[ 'contents' ] as $cart_item_key => $cart_item_data ) {

				$product = $cart_item_data[ 'data' ];

				$product_restriction_data = $this->get_product_restriction_data( $product );
				$map                      = $this->get_matching_rules_map( $product_restriction_data, $rates, array_merge( $args, array( 'cart_item_data' => $cart_item_data ) ) );

				if ( ! empty( $map ) ) {
					$maps[] = $map;
				}
			}
		}

		/* ----------------------------------------------------------------- */
		/* Global Restrictions
		/* ----------------------------------------------------------------- */

		$global_restriction_data = $this->get_global_restriction_data();
		$maps[]                  = $this->get_matching_rules_map( $global_restriction_data, $rates, $args );

		// Unset gateways.
		$ids_to_exclude = $this->get_unique_exclusion_ids( $maps );

		foreach ( $ids_to_exclude as $id ) {
			unset( $rates[ $id ] );
		}

		return $rates;
	}

	/**
	 * Generate map data for each active rule.
	 *
	 * @since  1.4.0
	 *
	 * @param  array  $payload
	 * @param  array  $restriction
	 * @param  bool   $include_data
	 * @return array
	 */
	protected function generate_rules_map_data( $payload, $restriction, $include_data ) {

		$data = array();

		if ( $include_data ) {
			$data = $this->get_restricted_rates( $payload, $restriction[ $this->restricted_key ] );
		}

		return $data;
	}

	/**
	 * Validate order checkout and return WC_CSP_Check_Result object.
	 *
	 * @param  array  $posted
	 * @return WC_CSP_Check_Result
	 */
	public function validate_checkout( $posted ) {

		$result = new WC_CSP_Check_Result();
		$args   = array(
			'context'      => 'validation',
			'include_data' => true
		);

		/**
		 * 'woocommerce_csp_shipping_packages' filter.
		 *
		 * Alters the shipping packages seen by this validation routine.
		 *
		 * @since  1.4.0
		 * @param  array  $packages
		 */
		$shipping_packages      = apply_filters( 'woocommerce_csp_shipping_packages', WC()->shipping->get_packages() );
		$chosen_methods         = WC()->session->get( 'chosen_shipping_methods' );
		$shipping_package_index = 0;

		if ( isset( $posted[ 'check_package_index' ], $posted[ 'check_rate' ] ) && isset( $shipping_packages[ $posted[ 'check_package_index' ] ] ) ) {

			$shipping_packages = array( $shipping_packages[ $posted[ 'check_package_index' ] ] );
			$chosen_methods    = array( $posted[ 'check_rate' ] );

			$args[ 'context' ]    = 'check';
			$args[ 'check_rate' ] = $posted[ 'check_rate' ];

			unset( $args[ 'include_data' ] );
		}

		$args[ 'package_count' ] = sizeof( $shipping_packages );

		if ( ! empty( $shipping_packages ) ) {
			foreach ( $shipping_packages as $i => $package ) {

				$shipping_package_index++;

				// Add extra args.
				$args[ 'package' ]       = $package;
				$args[ 'package_key' ]   = $i;
				$args[ 'package_index' ] = $shipping_package_index;

				if ( empty( $chosen_methods[ $i ] ) || empty( $package[ 'rates' ] ) ) {
					continue;
				}

				$chosen_rate = ! empty( $package[ 'rates' ][ $chosen_methods[ $i ] ] ) ? $package[ 'rates' ][ $chosen_methods[ $i ] ] : false;

				foreach ( $package[ 'contents' ] as $cart_item_key => $cart_item_data ) {

					/* ----------------------------------------------------------------- */
					/* Product Restrictions
					/* ----------------------------------------------------------------- */

					$product = $cart_item_data[ 'data' ];

					$product_restriction_data = $this->get_product_restriction_data( $product );
					$product_rules_map        = $this->get_matching_rules_map( $product_restriction_data, array( $chosen_methods[ $i ] => $chosen_rate ), array_merge( $args, array( 'cart_item_data' => $cart_item_data ) ) );

					foreach ( $product_rules_map as $rule_index => $excluded_rate_ids ) {

						if ( ! empty( $excluded_rate_ids ) ) {
							$result->add( 'shipping_method_excluded_by_product_restriction', $this->get_resolution_message( $product_restriction_data[ $rule_index ], 'product', array_merge( $args, array( 'cart_item_data' => $cart_item_data ) ) ) );
						}
					}
				}

				/* ----------------------------------------------------------------- */
				/* Global Restrictions
				/* ----------------------------------------------------------------- */

				// Grab global restrictions.
				$global_restriction_data = $this->get_global_restriction_data();
				$global_rules_map        = $this->get_matching_rules_map( $global_restriction_data, array( $chosen_methods[ $i ] => $chosen_rate ), $args );

				foreach ( $global_rules_map as $rule_index => $excluded_rate_ids ) {

					if ( ! empty( $excluded_rate_ids ) ) {
						$result->add( 'shipping_method_excluded_by_global_restriction', $this->get_resolution_message( $global_restriction_data[ $rule_index ], 'global', $args ) );
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Generate resolution message.
	 *
	 * @since  1.7.7
	 *
	 * @param  array   $restriction
	 * @param  string  $context
	 * @param  array   $args
	 * @return string
	 */
	protected function get_resolution_message_content( $restriction, $context, $args = array() ) {

		$chosen_methods  = isset( $args[ 'check_rate' ] ) ? array( $args[ 'check_rate' ] ) : WC()->session->get( 'chosen_shipping_methods' );
		$message_context = isset( $args[ 'context' ] ) && 'check' === $args[ 'context' ] ? 'check' : 'validation';

		$message         = '';
		$package         = $args[ 'package' ];
		$package_key     = $args[ 'package_key' ];
		$package_count   = $args[ 'package_count' ];
		$package_index   = $args[ 'package_index' ];

		if ( 'product' === $context ) {

			$product = $args[ 'cart_item_data' ][ 'data' ];

			if ( ! empty( $restriction[ 'message' ] ) ) {

				$message 	= str_replace( array( '{product}', '{excluded_method}', '{excluded_package_index}' ), array( '&quot;%1$s&quot;', '%2$s', '%4$s' ), $restriction[ 'message' ] );
				$resolution = '';

			} else {

				$conditions_resolution = $this->get_conditions_resolution( $restriction, $args );

				if ( $conditions_resolution ) {

					if ( 'check' === $message_context ) {

						if ( $package_count === 1 ) {
							$resolution = sprintf( __( 'To choose &quot;%2$s&quot;, please %3$s. Otherwise, please remove &quot;%1$s&quot; from your cart.', 'woocommerce-conditional-shipping-and-payments' ), $product->get_title(), $package[ 'rates' ][ $chosen_methods[ $package_key ] ]->label, $conditions_resolution );
						} else {
							$resolution = sprintf( __( 'To choose &quot;%2$s&quot;, please %3$s. Otherwise, please remove &quot;%1$s&quot; from this package.', 'woocommerce-conditional-shipping-and-payments' ), $product->get_title(), $package[ 'rates' ][ $chosen_methods[ $package_key ] ]->label, $conditions_resolution, $package_index );
						}

					} else {

						if ( $package_count === 1 ) {
							$resolution = sprintf( __( 'To have &quot;%1$s&quot; shipped via &quot;%2$s&quot;, please %3$s. Otherwise, select an alternative shipping method, or remove &quot;%1$s&quot; from your cart.', 'woocommerce-conditional-shipping-and-payments' ), $product->get_title(), $package[ 'rates' ][ $chosen_methods[ $package_key ] ]->label, $conditions_resolution );
						} else {
							$resolution = sprintf( __( 'To have &quot;%1$s&quot; shipped via &quot;%2$s&quot;, please %3$s. Otherwise, select an alternative shipping method, or remove &quot;%1$s&quot; from package #%4$s.', 'woocommerce-conditional-shipping-and-payments' ), $product->get_title(), $package[ 'rates' ][ $chosen_methods[ $package_key ] ]->label, $conditions_resolution, $package_index );
						}
					}

				} else {

					if ( 'check' === $message_context ) {

						$resolution = '';

					} else {

						if ( $package_count === 1 ) {
							$resolution = sprintf( __( 'To complete your order, please select an alternative shipping method, or remove &quot;%1$s&quot; from your cart.', 'woocommerce-conditional-shipping-and-payments' ), $product->get_title() );
						} else {
							$resolution = sprintf( __( 'To complete your order, please select an alternative shipping method, or remove &quot;%1$s&quot; from package #%2$s.', 'woocommerce-conditional-shipping-and-payments' ), $product->get_title(), $package_index );
						}
					}
				}

				if ( 'check' === $message_context ) {
					$message = __( '&quot;%1$s&quot; is not eligible for shipping via &quot;%2$s&quot;. %3$s', 'woocommerce-conditional-shipping-and-payments' );
				} else {
					$message = __( 'Unfortunately, &quot;%1$s&quot; is not eligible for shipping via &quot;%2$s&quot;. %3$s', 'woocommerce-conditional-shipping-and-payments' );
				}
			}

			$message = sprintf( $message, $product->get_title(), $package[ 'rates' ][ $chosen_methods[ $package_key ] ]->label, $resolution, $package_index );

		} elseif ( 'global' === $context ) {

			if ( ! empty( $restriction[ 'message' ] ) ) {

				$message 	= str_replace( array( '{excluded_method}', '{excluded_package_index}' ), array( '%1$s', '%3$s' ), $restriction[ 'message' ] );
				$resolution = '';

			} else {

				$conditions_resolution = $this->get_conditions_resolution( $restriction, $args );

				if ( $conditions_resolution ) {

					if ( 'check' === $message_context ) {

						if ( $package_count === 1 ) {
							$resolution = sprintf( __( 'To have this order shipped via &quot;%1$s&quot;, please %2$s.', 'woocommerce-conditional-shipping-and-payments' ), $package[ 'rates' ][ $chosen_methods[ $package_key ] ]->label, $conditions_resolution );
						} else {
							$resolution = sprintf( __( 'To have this package shipped via &quot;%1$s&quot;, please %2$s.', 'woocommerce-conditional-shipping-and-payments' ), $package[ 'rates' ][ $chosen_methods[ $package_key ] ]->label, $conditions_resolution, $package_index );
						}

					} else {

						if ( $package_count === 1 ) {
							$resolution = sprintf( __( 'To have your order shipped via &quot;%1$s&quot;, please %2$s. Otherwise, choose an alternative shipping method.', 'woocommerce-conditional-shipping-and-payments' ), $package[ 'rates' ][ $chosen_methods[ $package_key ] ]->label, $conditions_resolution );
						} else {
							$resolution = sprintf( __( 'To have package #%3$s shipped via &quot;%1$s&quot;, please %2$s. Otherwise, choose an alternative shipping method.', 'woocommerce-conditional-shipping-and-payments' ), $package[ 'rates' ][ $chosen_methods[ $package_key ] ]->label, $conditions_resolution, $package_index );
						}
					}

				} else {

					if ( 'check' === $message_context ) {
						$resolution = '';
					} else {
						$resolution = __( 'To complete your order, please select an alternative shipping method.', 'woocommerce-conditional-shipping-and-payments' );
					}
				}

				if ( 'check' === $message_context ) {

					$message = __( 'This order cannot be shipped via &quot;%1$s&quot;. %2$s', 'woocommerce-conditional-shipping-and-payments' );

				} else {

					if ( $package_count === 1 ) {
						$message = __( 'Unfortunately, your order cannot be shipped via &quot;%1$s&quot;. %2$s', 'woocommerce-conditional-shipping-and-payments' );
					} else {
						$message = __( 'Unfortunately, package #%3$s cannot be shipped via &quot;%1$s&quot;. %2$s', 'woocommerce-conditional-shipping-and-payments' );
					}
				}
			}

			$message = sprintf( $message, $package[ 'rates' ][ $chosen_methods[ $package_key ] ]->label, $resolution, $package_index );
		}

		return $message;
	}
}
