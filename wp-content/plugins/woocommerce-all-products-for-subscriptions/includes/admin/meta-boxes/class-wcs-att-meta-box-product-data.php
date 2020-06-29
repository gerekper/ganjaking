<?php
/**
 * WCS_ATT_Meta_Box_Product_Data class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce All Products For Subscriptions
 * @since    2.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product meta-box data for SATT-enabled product types.
 *
 * @class    WCS_ATT_Meta_Box_Product_Data
 * @version  3.0.0
 */
class WCS_ATT_Meta_Box_Product_Data {

	/**
	 * Initialize.
	 */
	public static function init() {
		self::add_hooks();
	}

	/**
	 * Add hooks.
	 */
	private static function add_hooks() {

		// Create the SATT Subscriptions tab.
		add_action( 'woocommerce_product_data_tabs', array( __CLASS__, 'satt_product_data_tab' ) );

		// Create the SATT Subscriptions tab panel.
		add_action( 'woocommerce_product_data_panels', array( __CLASS__, 'product_data_panel' ) );

		// Subscription scheme markup added on the 'wcsatt_subscription_scheme' action.
		add_action( 'wcsatt_subscription_scheme', array( __CLASS__, 'subscription_scheme' ), 10, 3 );

		// Subscription scheme options displayed on the 'wcsatt_subscription_scheme_content' action.
		add_action( 'wcsatt_subscription_scheme_content', array( __CLASS__, 'subscription_scheme_content' ), 10, 3 );

		// Product-specific subscription scheme options displayed on the 'wcsatt_subscription_scheme_content' action.
		add_action( 'wcsatt_subscription_scheme_content', array( __CLASS__, 'subscription_scheme_product_content_display' ), 100, 3 );

		// Product-specific subscription scheme options content.
		add_action( 'wcsatt_subscription_scheme_product_content', array( __CLASS__, 'subscription_scheme_product_content' ), 10, 3 );

		// Cart subscription scheme options content.
		add_action( 'wcsatt_subscription_scheme_global_content', array( __CLASS__, 'subscription_scheme_global_content' ), 10, 2 );

		// Process and save the necessary meta.
		add_action( 'woocommerce_admin_process_product_object', array( __CLASS__, 'save_subscription_data' ), 10, 1 );
	}

	/**
	 * Add Subscriptions tab.
	 *
	 * @param  array  $tabs
	 * @return void
	 */
	public static function satt_product_data_tab( $tabs ) {

		$tabs[ 'satt' ] = array(
			'label'    => __( 'Subscriptions', 'woocommerce-all-products-for-subscriptions' ),
			'target'   => 'wcsatt_data',
			'priority' => 100,
			'class'    => array( 'cart_subscription_options', 'cart_subscriptions_tab', 'show_if_simple', 'show_if_variable', 'show_if_bundle', 'hide_if_subscription', 'hide_if_variable-subscription' )
		);

		return $tabs;
	}

	/**
	 * Product writepanel for Subscriptions.
	 *
	 * @return void
	 */
	public static function product_data_panel() {

		global $post, $product_object;

		$subscription_schemes = $product_object->get_meta( '_wcsatt_schemes', true );

		?><div id="wcsatt_data" class="panel woocommerce_options_panel wc-metaboxes-wrapper <?php echo empty( $subscription_schemes ) ? 'planless onboarding' : ''; ?>">
			<div class="options_group general_scheme_options"><?php

				// Subscription Status.
				woocommerce_wp_checkbox( array(
					'id'          => '_wcsatt_allow_one_off',
					'label'       => __( 'One-time purchase', 'woocommerce-all-products-for-subscriptions' ),
					'value'       => 'yes' === $product_object->get_meta( '_wcsatt_force_subscription', true ) ? 'no' : 'yes',
					'desc_tip'    => true,
					'description' => __( 'Enable this option to allow one-time purchases of this product. Applicable when at least one Subscription Plan has been added below.', 'woocommerce-all-products-for-subscriptions' )
				) );

				// Default Status.
				woocommerce_wp_select( array(
					'id'            => '_wcsatt_default_status',
					'wrapper_class' => 'wcsatt_default_status',
					'label'         => __( 'Default to', 'woocommerce-all-products-for-subscriptions' ),
					'description'   => '',
					'options'       => array(
						'one-time'     => __( 'One-time purchase', 'woocommerce-all-products-for-subscriptions' ),
						'subscription' => __( 'Subscription plan', 'woocommerce-all-products-for-subscriptions' ),
					)
				) );

				// Subscription Prompt.
				woocommerce_wp_textarea_input( array(
					'id'          => '_wcsatt_subscription_prompt',
					'label'       => __( 'Title', 'woocommerce-all-products-for-subscriptions' ),
					'description' => __( 'Text/html to display above the available purchase plan options. Supports html and shortcodes. Applicable when at least one Subscription Plan has been added below.', 'woocommerce-all-products-for-subscriptions' ),
					'placeholder' => __( 'e.g. "Choose a subscription plan"', 'woocommerce-all-products-for-subscriptions' ),
					'desc_tip'    => true
				) );

				// Plans layout.
				$current_layout = $product_object->get_meta( '_wcsatt_layout', true );
				$current_layout = in_array( $current_layout, array( 'flat', 'grouped' ) ) ? $current_layout : 'flat';

				// Available layouts.
				$layouts = array(
					'flat'           => array(
						'title'       => 'Flat',
						'description' => 'Renders all options as radio buttons.',
						'value'       => 'flat',
						'class'       => 'flat',
						'checked'     => 'flat' === $current_layout
					),
					'grouped'        => array(
						'title'       => 'Grouped',
						'description' => 'Renders a pair of radio buttons to prompt users to subscribe, or make a one-time purchase. Groups the available Subscription Plans in a drop-down menu.',
						'value'       => 'grouped',
						'class'       => 'grouped',
						'checked'     => 'grouped' === $current_layout
					)
				);

				?>
				<div class="wcsatt_default_layout form-field _wcsatt_layout_field">
					<label for="_wcsatt_layout">Layout</label>
					<ul class="wcsatt_image_select__container">
						<?php
						foreach ( $layouts as $layout ) {
							$classes = array( $layout[ 'class' ] );
							if ( ! empty( $layout[ 'checked' ] ) ) {
								$classes[] = 'selected';
							}
						?>
						<li class="<?php echo implode( ' ', $classes ); ?>" >
							<input type="radio"<?php echo $layout[ 'checked' ] ? ' checked' : '' ?> name="_wcsatt_layout" id="_wcsatt_layout" value="<?php echo $layout[ 'value' ] ?>">
							<?php echo wc_help_tip( '<strong>' . $layout[ 'title' ] . '</strong> &ndash; ' . $layout[ 'description' ] ); ?>
						</li>
						<?php } ?>
					</ul>

				</div>
				<div class="wp-clearfix"></div>
				<?php

				if ( isset( $_GET[ 'wcsatt_onboarding' ] ) ) {
					woocommerce_wp_hidden_input( array(
						'id'    => '_wcsatt_onboarding',
						'value' => 1
					) );
				}

			?></div>
			<div class="hr-section hr-section-schemes"><?php echo __( 'Subscription Plans', 'woocommerce-all-products-for-subscriptions' ); ?></div>
			<div class="options_group subscription_schemes wc-metaboxes ui-sortable" data-count=""><?php

				if ( ! empty ( $subscription_schemes ) ) {

					$i = 0;

					foreach ( $subscription_schemes as $subscription_scheme ) {
						do_action( 'wcsatt_subscription_scheme', $i, $subscription_scheme, $post->ID );
						$i++;
					}

				} else {

					?>
					<div class="apfs_boarding__schemes">
						<div class="apfs_boarding__schemes__message">
							<h3><?php _e( 'Subscription Plans', 'woocommerce-all-products-for-subscriptions' ); ?></h3>
							<p><?php _e( 'Want to make this product available on subscription?', 'woocommerce-all-products-for-subscriptions' ); ?>
							<br/><?php _e( 'Add some subscription plans to get started!', 'woocommerce-all-products-for-subscriptions' ); ?>
							</p>
						</div>
					</div>
					<?php
				}

				?><div class="apfs_no_schemes__message">
					<p><?php _e( 'No subscription plans found. Add one now?', 'woocommerce-all-products-for-subscriptions' ); ?></p>
				</div>

			</div>
			<p class="subscription_schemes_add_wrapper">
				<button type="button" class="button add_subscription_scheme"><?php _e( 'Add Plan', 'woocommerce-all-products-for-subscriptions' ); ?></button>
			</p>
		</div><?php
	}


	/**
	 * Subscription scheme markup adeed on the 'wcsatt_subscription_scheme' action.
	 *
	 * @param  int     $index
	 * @param  array   $scheme_data
	 * @param  int     $post_id
	 * @return void
	 */
	public static function subscription_scheme( $index, $scheme_data, $post_id ) {
		include( 'views/subscription-scheme.php' );
	}

	/**
	 * Subscription scheme options displayed on the 'wcsatt_subscription_scheme_content' action.
	 *
	 * @param  int     $index
	 * @param  array   $scheme_data
	 * @param  int     $post_id
	 * @return void
	 */
	public static function subscription_scheme_content( $index, $scheme_data, $post_id ) {

		global $thepostid;

		if ( empty( $thepostid ) ) {
			$thepostid = '-1';
		}

		if ( ! empty( $scheme_data ) ) {
			$subscription_period          = $scheme_data[ 'subscription_period' ];
			$subscription_period_interval = $scheme_data[ 'subscription_period_interval' ];
			$subscription_length          = $scheme_data[ 'subscription_length' ];
		} else {
			$subscription_period          = 'month';
			$subscription_period_interval = '';
			$subscription_length          = '';
		}

		// Subscription Price, Interval and Period.
		?><div class="satt_subscription_details">
			<p class="form-field _satt_subscription_details_<?php echo $index; ?>">
				<label for="_satt_subscription_details_<?php echo $index; ?>"><?php esc_html_e( 'Interval', 'woocommerce-all-products-for-subscriptions' ); ?></label>
				<span class="wrap">
					<label for="_satt_subscription_period_interval_<?php echo $index; ?>" class="wcs_hidden_label"><?php esc_html_e( 'Subscription interval', 'woocommerce-subscriptions' ); ?></label>
					<select id="_satt_subscription_period_interval_<?php echo $index; ?>" name="wcsatt_schemes[<?php echo $index; ?>][subscription_period_interval]" class="wc_input_subscription_period_interval">
					<?php foreach ( wcs_get_subscription_period_interval_strings() as $value => $label ) { ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $subscription_period_interval, true ) ?>><?php echo esc_html( $label ); ?></option>
					<?php } ?>
					</select>
					<label for="_satt_subscription_period_<?php echo $index; ?>" class="wcs_hidden_label"><?php esc_html_e( 'Subscription period', 'woocommerce-subscriptions' ); ?></label>
					<select id="_satt_subscription_period_<?php echo $index; ?>" name="wcsatt_schemes[<?php echo $index; ?>][subscription_period]" class="wc_input_subscription_period last" >
					<?php foreach ( wcs_get_subscription_period_strings() as $value => $label ) { ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $subscription_period, true ) ?>><?php echo esc_html( $label ); ?></option>
					<?php } ?>
					</select>
				</span>
				<?php echo wc_help_tip( __( 'Choose the subscription billing interval and period.', 'woocommerce-all-products-for-subscriptions' ) ); ?>
			</p>
		</div>
		<div class="satt_subscription_length"><?php

			// Subscription Length.
			woocommerce_wp_select( array(
				'id'          => '_satt_subscription_length_' . $index,
				'class'       => 'wc_input_subscription_length short',
				'label'       => __( 'Length', 'woocommerce-all-products-for-subscriptions' ),
				'value'       => $subscription_length,
				'options'     => wcs_get_subscription_ranges( $subscription_period ),
				'name'        => 'wcsatt_schemes[' . $index . '][subscription_length]',
				'description' => __( 'Choose the subscription billing length.', 'woocommerce-all-products-for-subscriptions' ),
				'desc_tip'    => true
			) );

		?></div><?php
	}

	/**
	 * Show product-specific subscription scheme options on the 'wcsatt_subscription_scheme_content' action.
	 *
	 * @param  int     $index
	 * @param  array   $scheme_data
	 * @param  int     $post_id
	 * @return void
	 */
	public static function subscription_scheme_product_content_display( $index, $scheme_data, $post_id ) {

		if ( $post_id > 0 ) {
			?><div class="subscription_scheme_product_data"><?php
				do_action( 'wcsatt_subscription_scheme_product_content', $index, $scheme_data, $post_id );
			?></div><?php
		} else {
			?><div class="subscription_scheme_global_data"><?php
				do_action( 'wcsatt_subscription_scheme_global_content', $index, $scheme_data );
			?></div><?php
		}
	}

	/**
	 * Product-specific subscription scheme options.
	 *
	 * @param  int     $index
	 * @param  array   $scheme_data
	 * @param  int     $post_id
	 * @return void
	 */
	public static function subscription_scheme_product_content( $index, $scheme_data, $post_id ) {

		$subscription_pricing_method = '';
		$subscription_regular_price  = '';
		$subscription_sale_price     = '';
		$subscription_discount       = '';

		if ( ! empty( $scheme_data ) ) {
			$subscription_pricing_method = ! empty( $scheme_data[ 'subscription_pricing_method' ] ) ? $scheme_data[ 'subscription_pricing_method' ] : 'inherit';
			$subscription_regular_price  = isset( $scheme_data[ 'subscription_regular_price' ] ) ? $scheme_data[ 'subscription_regular_price' ] : '';
			$subscription_sale_price     = isset( $scheme_data[ 'subscription_sale_price' ] ) ? $scheme_data[ 'subscription_sale_price' ] : '';
			$subscription_discount       = isset( $scheme_data[ 'subscription_discount' ] ) ? $scheme_data[ 'subscription_discount' ] : '';
		}

		// Subscription Price Override Method.
		woocommerce_wp_select( array(
			'id'            => '_subscription_pricing_method_input',
			'class'         => 'subscription_pricing_method_input short',
			'wrapper_class' => 'subscription_pricing_method_select',
			'label'         => __( 'Price', 'woocommerce-all-products-for-subscriptions' ),
			'value'         => $subscription_pricing_method,
			'options'       => array(
					'inherit'  => __( 'Inherit from product', 'woocommerce-all-products-for-subscriptions' ),
					'override' => __( 'Override product', 'woocommerce-all-products-for-subscriptions' ),
				),
			'name'          => 'wcsatt_schemes[' . $index . '][subscription_pricing_method]'
			)
		);

		?><div class="subscription_pricing_method subscription_pricing_method_override"><?php

			// Price.
			woocommerce_wp_text_input( array(
				'id'            => '_override_subscription_regular_price',
				'name'          => 'wcsatt_schemes[' . $index . '][subscription_regular_price]',
				'value'         => $subscription_regular_price,
				'wrapper_class' => 'override_subscription_regular_price',
				'label'         => __( 'Regular Price', 'woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . ')',
				'data_type'     => 'price'
			) );

			// Sale Price.
			woocommerce_wp_text_input( array(
				'id'            => '_override_subscription_sale_price',
				'name'          => 'wcsatt_schemes[' . $index . '][subscription_sale_price]',
				'value'         => $subscription_sale_price,
				'wrapper_class' => 'override_subscription_sale_price',
				'label'         => __( 'Sale Price', 'woocommerce' ) . ' (' . get_woocommerce_currency_symbol() . ')',
				'data_type'     => 'price'
			) );

		?></div>
		<div class="subscription_pricing_method subscription_pricing_method_inherit"><?php

			// Discount.
			woocommerce_wp_text_input( array(
				'id'            => '_subscription_price_discount',
				'name'          => 'wcsatt_schemes[' . $index . '][subscription_discount]',
				'value'         => $subscription_discount,
				'wrapper_class' => 'subscription_price_discount',
				'label'         => __( 'Discount %', 'woocommerce-all-products-for-subscriptions' ),
				'description'   => __( 'Discount applied on the <strong>Regular Price</strong> of the product.', 'woocommerce-all-products-for-subscriptions' ),
				'desc_tip'      => true,
				'data_type'     => 'decimal'
			) );

		?></div>
		<?php
	}

	/**
	 * Cart subscription scheme options.
	 *
	 * @since  2.2.0
	 *
	 * @param  int     $index
	 * @param  array   $scheme_data
	 * @return void
	 */
	public static function subscription_scheme_global_content( $index, $scheme_data ) {

		$subscription_discount = ! empty( $scheme_data ) && isset( $scheme_data[ 'subscription_discount' ] ) ? $scheme_data[ 'subscription_discount' ] : '';

		?><div class="subscription_pricing_method subscription_pricing_method_inherit"><?php

			// Discount.
			woocommerce_wp_text_input( array(
				'id'            => '_subscription_price_discount',
				'name'          => 'wcsatt_schemes[' . $index . '][subscription_discount]',
				'value'         => $subscription_discount,
				'wrapper_class' => 'subscription_price_discount',
				'label'         => __( 'Discount %', 'woocommerce-all-products-for-subscriptions' ),
				'description'   => __( 'Discount applied on the <strong>Regular Price</strong> of the product.', 'woocommerce-all-products-for-subscriptions' ),
				'desc_tip'      => true,
				'data_type'     => 'decimal'
			) );

		?></div>
		<?php
	}

	/**
	 * Save subscription options.
	 *
	 * @param  WC_Product  $product
	 * @return void
	 */
	public static function save_subscription_data( $product ) {

		if ( WCS_ATT_Product::supports_feature( $product, 'subscription_schemes' ) ) {

			$schemes = array();

			// Process scheme options.
			if ( isset( $_POST[ 'wcsatt_schemes' ] ) ) {

				$posted_schemes = stripslashes_deep( $_POST[ 'wcsatt_schemes' ] );

				foreach ( $posted_schemes as $posted_scheme ) {

					// Format subscription prices.
					if ( isset( $posted_scheme[ 'subscription_regular_price' ] ) ) {
						$posted_scheme[ 'subscription_regular_price' ] = ( $posted_scheme[ 'subscription_regular_price'] === '' ) ? '' : wc_format_decimal( $posted_scheme[ 'subscription_regular_price' ] );
					}

					if ( isset( $posted_scheme[ 'subscription_sale_price' ] ) ) {
						$posted_scheme[ 'subscription_sale_price' ] = ( $posted_scheme[ 'subscription_sale_price'] === '' ) ? '' : wc_format_decimal( $posted_scheme[ 'subscription_sale_price' ] );
					}

					if ( '' !== $posted_scheme[ 'subscription_sale_price' ] ) {
						$posted_scheme[ 'subscription_price' ] = $posted_scheme[ 'subscription_sale_price' ];
					} else {
						$posted_scheme[ 'subscription_price' ] = ( $posted_scheme[ 'subscription_regular_price' ] === '' ) ? '' : $posted_scheme[ 'subscription_regular_price' ];
					}

					// Format subscription discount.
					if ( isset( $posted_scheme[ 'subscription_discount' ] ) ) {

						if ( is_numeric( $posted_scheme[ 'subscription_discount' ] ) ) {

							$discount = (float) wc_format_decimal( $posted_scheme[ 'subscription_discount' ] );

							if ( $discount < 0 || $discount > 100 ) {

								WC_Admin_Meta_Boxes::add_error( __( 'Please enter positive subscription discount values, between 0-100.', 'woocommerce-all-products-for-subscriptions' ) );
								$posted_scheme[ 'subscription_discount' ] = '';

							} else {
								$posted_scheme[ 'subscription_discount' ] = $discount;
							}
						} else {
							$posted_scheme[ 'subscription_discount' ] = '';
						}
					} else {
						$posted_scheme[ 'subscription_discount' ] = '';
					}

					// Validate price override method.
					if ( isset( $posted_scheme[ 'subscription_pricing_method' ] ) && $posted_scheme[ 'subscription_pricing_method' ] === 'override' ) {
						if ( $posted_scheme[ 'subscription_price' ] === '' && $posted_scheme[ 'subscription_regular_price' ] === '' ) {
							$posted_scheme[ 'subscription_pricing_method' ] = 'inherit';
						}
					} else {
						$posted_scheme[ 'subscription_pricing_method' ] = 'inherit';
					}

					/**
					 * Allow third parties to add custom data to schemes.
					 *
					 * @since  2.1.0
					 *
					 * @param  array       $posted_scheme
					 * @param  WC_Product  $product
					 */
					$posted_scheme = apply_filters( 'wcsatt_processed_scheme_data', $posted_scheme, $product );

					// Don't store multiple schemes with the same billing schedule.
					$scheme_key = $posted_scheme[ 'subscription_period_interval' ] . '_' . $posted_scheme[ 'subscription_period' ] . '_' . $posted_scheme[ 'subscription_length' ];

					if ( isset( $schemes[ $scheme_key ] ) ) {
						continue;
					}

					$schemes[ $scheme_key ] = $posted_scheme;
				}

				if ( isset( $_POST[ '_wcsatt_onboarding' ] ) ) {

					// Clear onboarding "welcome" notice.
	 				WCS_ATT_Admin_Notices::remove_dismissible_notice( 'welcome' );

	 				if ( ! empty( $schemes ) ) {
	 					// Add onboarding "cart-plans" notice (one-time).
	 					WCS_ATT_Core_Compatibility::is_wc_admin_enabled() ? WCS_ATT_Admin_Notices::add_cart_plans_onboarding_admin_note() : WCS_ATT_Admin_Notices::add_cart_plans_onboarding_notice();
	 				}
	 			}
			}

			// Process one-time shipping option.
			$one_time_shipping = isset( $_POST[ '_subscription_one_time_shipping' ] ) ? 'yes' : 'no';

			// Process default status option.
			$default_status = ! empty( $schemes ) && isset( $_POST[ '_wcsatt_default_status' ] ) ? stripslashes( $_POST[ '_wcsatt_default_status' ] ) : 'one-time';

			// Process force-sub status.
			$force_subscription = ! empty( $schemes ) && ! isset( $_POST[ '_wcsatt_allow_one_off' ] ) ? 'yes' : 'no';

			// Process prompt text.
			$prompt = ! empty( $schemes ) && ! empty( $_POST[ '_wcsatt_subscription_prompt' ] ) ? wp_kses_post( stripslashes( $_POST[ '_wcsatt_subscription_prompt' ] ) ) : false;

			// Process layout.
			$layout = isset( $_POST[ '_wcsatt_layout' ] ) ? stripslashes( $_POST[ '_wcsatt_layout' ] ) : 'flat';

			/*
			 * Add/update meta.
			 */

			// Save scheme options.
			if ( ! empty( $schemes ) ) {

				$product->update_meta_data( '_wcsatt_schemes', array_values( $schemes ) );

				// Set regular price to zero should the shop owner forget.
				if ( 'yes' === $force_subscription && empty( $_POST[ '_regular_price' ] ) ) {
					$product->set_regular_price( 0 );
					$product->set_price( 0 );
				}

			} else {
				$product->delete_meta_data( '_wcsatt_schemes' );
			}

			// Save one-time shipping option.
			$product->update_meta_data( '_subscription_one_time_shipping', $one_time_shipping );

			// Save default status.
			$product->update_meta_data( '_wcsatt_default_status', $default_status );

			// Save force-sub status.
			$product->update_meta_data( '_wcsatt_force_subscription', $force_subscription );

			// Save layout.
			$product->update_meta_data( '_wcsatt_layout', $layout );

			// Save prompt.
			if ( false === $prompt ) {
				$product->delete_meta_data( '_wcsatt_subscription_prompt' );
			} else {
				$product->update_meta_data( '_wcsatt_subscription_prompt', $prompt );
			}

		} else {

			$product->delete_meta_data( '_wcsatt_schemes' );
			$product->delete_meta_data( '_wcsatt_force_subscription' );
			$product->delete_meta_data( '_wcsatt_default_status' );
			$product->delete_meta_data( '_wcsatt_subscription_prompt' );
			$product->delete_meta_data( '_wcsatt_layout' );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Deprecated
	|--------------------------------------------------------------------------
	*/

	/**
	 * WC 2.X way of saving product data.
	 *
	 * @deprecated  2.1.0   No longer used internally.
	 *
	 * @param  int  $post_id
	 * @return void
	 */
	public static function process_product_meta( $post_id ) {
		_deprecated_function( __METHOD__ . '()', '2.1.0', 'WCS_ATT_Meta_Box_Product_Data::save_subscription_data()' );
		$product = wc_get_product( $post_id );
		if ( is_a( $product, 'WC_Product' ) ) {
			self::save_subscription_data( $product );
		}
	}
}

WCS_ATT_Meta_Box_Product_Data::init();
