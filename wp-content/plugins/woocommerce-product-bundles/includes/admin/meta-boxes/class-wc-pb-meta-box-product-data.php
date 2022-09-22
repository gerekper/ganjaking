<?php
/**
 * WC_PB_Meta_Box_Product_Data class
 *
 * @package  WooCommerce Product Bundles
 * @since    5.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product meta-box data for the 'Bundle' type.
 *
 * @class    WC_PB_Meta_Box_Product_Data
 * @version  6.16.0
 */
class WC_PB_Meta_Box_Product_Data {

	/**
	 * Hook in.
	 */
	public static function init() {

		// Creates the "Bundled Products" tab.
		add_action( 'woocommerce_product_data_tabs', array( __CLASS__, 'product_data_tabs' ) );

		// Creates the panel for selecting bundled product options.
		add_action( 'woocommerce_product_data_panels', array( __CLASS__, 'product_data_panel' ) );

		// Adds a tooltip to the Manage Stock option.
		add_action( 'woocommerce_product_options_stock', array( __CLASS__, 'stock_note' ) );

		// Add type-specific options.
		add_filter( 'product_type_options', array( __CLASS__, 'bundle_type_options' ) );

		// Add Shipping type image select.
		add_action( 'woocommerce_product_options_shipping', array( __CLASS__, 'bundle_shipping_type_admin_html' ), 10000 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'js_handle_container_classes' ) );

		// Processes and saves type-specific data.
		add_action( 'woocommerce_admin_process_product_object', array( __CLASS__, 'process_bundle_data' ) );

		// Basic bundled product admin config options.
		add_action( 'woocommerce_bundled_product_admin_config_html', array( __CLASS__, 'bundled_product_admin_config_html' ), 10, 4 );

		// Advanced bundled product admin config options.
		add_action( 'woocommerce_bundled_product_admin_advanced_html', array( __CLASS__, 'bundled_product_admin_advanced_html' ), 10, 4 );
		add_action( 'woocommerce_bundled_product_admin_advanced_html', array( __CLASS__, 'bundled_product_admin_advanced_item_id_html' ), 100, 4 );

		// Bundle tab settings.
		add_action( 'woocommerce_bundled_products_admin_config', array( __CLASS__, 'bundled_products_admin_config_layout' ), 5 );
		add_action( 'woocommerce_bundled_products_admin_config', array( __CLASS__, 'bundled_products_admin_config_form_location' ), 10 );
		add_action( 'woocommerce_bundled_products_admin_config', array( __CLASS__, 'bundled_products_admin_config_group_mode' ), 15 );
		add_action( 'woocommerce_bundled_products_admin_config', array( __CLASS__, 'bundled_products_admin_config_edit_in_cart' ), 20 );
		add_action( 'woocommerce_bundled_products_admin_contents', array( __CLASS__, 'bundled_products_admin_contents' ), 20 );

		// Extended "Sold Individually" option.
		add_action( 'woocommerce_product_options_sold_individually', array( __CLASS__, 'sold_individually_option' ) );

		/*
		 * Support.
		 */

		// Add a notice if prices not set.
		add_action( 'admin_notices', array( __CLASS__, 'maybe_add_non_purchasable_notice' ), 0 );
	}

	/**
	 * Adds a notice if prices not set.
	 *
	 * @return void
	 */
	public static function maybe_add_non_purchasable_notice() {

		global $post_id;

		// Get admin screen ID.
		$screen    = get_current_screen();
		$screen_id = $screen ? $screen->id : '';

		if ( 'product' !== $screen_id ) {
			return;
		}

		$product_type = WC_Product_Factory::get_product_type( $post_id );

		if ( 'bundle' !== $product_type ) {
			return;
		}

		$product = wc_get_product( $post_id );

		if ( ! $product ) {
			return;
		}

		if ( false === $product->contains( 'priced_individually' ) && '' === $product->get_price( 'edit' ) ) {
			/* translators: %1$s: Product title, %2$s: Pricing options docs */
			$notice = sprintf( __( '&quot;%1$s&quot; is not purchasable just yet. But, fear not &ndash; setting up <a href="%2$s" target="_blank">pricing options</a> only takes a minute! <ul class="pb_notice_list"><li>To give &quot;%1$s&quot; a static base price, navigate to <strong>Product Data > General</strong> and fill in the <strong>Regular Price</strong> field.</li><li>To preserve the prices and taxes of individual bundled products, go to <strong>Product Data > Bundled Products</strong> and enable <strong>Priced Individually</strong> for each bundled product whose price must be preserved.</li></ul> Then, save your changes.', 'woocommerce-product-bundles' ), $product->get_title(), WC_PB()->get_resource_url( 'pricing-options' ) );
			WC_PB_Admin_Notices::add_notice( $notice, 'warning' );
		}
	}

	/**
	 * Renders extended "Sold Individually" option.
	 *
	 * @return void
	 */
	public static function sold_individually_option() {

		global $product_bundle_object;

		$sold_individually         = $product_bundle_object->get_sold_individually( 'edit' );
		$sold_individually_context = $product_bundle_object->get_sold_individually_context( 'edit' );

		$value = 'no';

		if ( $sold_individually ) {
			if ( ! in_array( $sold_individually_context, array( 'configuration', 'product' ) ) ) {
				$value = 'product';
			} else {
				$value = $sold_individually_context;
			}
		}

		// Provide context to the "Sold Individually" option.
		woocommerce_wp_select( array(
			'id'            => '_wc_pb_sold_individually',
			'wrapper_class' => 'show_if_bundle',
			'label'         => __( 'Sold individually', 'woocommerce' ),
			'options'       => array(
				'no'            => __( 'No', 'woocommerce-product-bundles' ),
				'product'       => __( 'Yes', 'woocommerce-product-bundles' ),
				'configuration' => __( 'Matching configurations only', 'woocommerce-product-bundles' )
			),
			'value'         => $value,
			'desc_tip'      => 'true',
			'description'   => __( 'Allow only one of this bundle to be bought in a single order. Choose the <strong>Matching configurations only</strong> option to only prevent <strong>identically configured</strong> bundles from being purchased together.', 'woocommerce-product-bundles' )
		) );
	}

	/**
	 * Add the "Bundled Products" panel tab.
	 */
	public static function product_data_tabs( $tabs ) {

		$tabs[ 'bundled_products' ] = array(
			'label'    => __( 'Bundled Products', 'woocommerce-product-bundles' ),
			'target'   => 'bundled_product_data',
			'class'    => array( 'show_if_bundle', 'wc_gte_26', 'bundled_product_options', 'bundled_product_tab' ),
			'priority' => 49
		);

		$tabs[ 'inventory' ][ 'class' ][] = 'show_if_bundle';

		return $tabs;
	}

	/**
	 * Data panels for Product Bundles.
	 */
	public static function product_data_panel() {

		global $product_bundle_object;

		?><div id="bundled_product_data" class="panel woocommerce_options_panel wc_gte_30 <?php echo $product_bundle_object->is_virtual_bundle() ? 'bundle_virtual' : ''; ?>" style="display:none">
			<div class="options_group_general">
				<?php
				/**
				 * 'woocommerce_bundled_products_admin_config' action.
				 *
				 * @param  WC_Product_Bundle  $product_bundle_object
				 */
				do_action( 'woocommerce_bundled_products_admin_config', $product_bundle_object );
				?>
			</div>
			<div class="options_group_contents">
				<?php
				/**
				 * 'woocommerce_bundled_products_admin_contents' action.
				 *
				 * @since  5.8.0
				 *
				 * @param  WC_Product_Bundle  $product_bundle_object
				 */
				do_action( 'woocommerce_bundled_products_admin_contents', $product_bundle_object );
				?>
			</div>
		</div><?php
	}

	/**
	 * Add Bundled Products stock note.
	 */
	public static function stock_note() {

		global $post;

		?><span class="bundle_stock_msg show_if_bundle">
				<?php echo wc_help_tip( __( 'By default, the sale of a product within a bundle has the same effect on its stock as an individual sale. There are no separate inventory settings for bundled items. However, managing stock at bundle level can be very useful for allocating bundle stock quota, or for keeping track of bundled item sales.', 'woocommerce-product-bundles' ) ); ?>
		</span><?php
	}

	/**
	 * Product bundle type-specific options.
	 *
	 * @param  array  $options
	 *
	 * @return array
	 */
	public static function bundle_type_options( $options ) {

		global $post, $product_object, $product_bundle_object;

		/*
		 * Create a global bundle-type object to use for populating fields.
		 */

		$post_id = $post->ID;

		if ( empty( $product_object ) || false === $product_object->is_type( 'bundle' ) ) {
			$product_bundle_object = $post_id ? new WC_Product_Bundle( $post_id ) : new WC_Product_Bundle();
		} else {
			$product_bundle_object = $product_object;
		}

		$options[ 'downloadable' ][ 'wrapper_class' ] .= ' show_if_bundle';
		$options[ 'virtual' ][ 'wrapper_class' ]      .= ' hide_if_bundle';

		/*
		 * Instead of adding this, another approach here would be to use the vanilla 'Virtual' box to set the 'virtual_bundle' prop for Bundles.
		 * However, we would need to make sure that we initialize it based on the 'virtual_bundle' prop value, instead of the 'virtual' prop, probably in JS.
		 * See 'js_handle_container_classes'.
		 */
		$options = array_merge( array(
			'wc_pb_virtual_bundle' => array(
				'id'            => '_virtual_bundle',
				'wrapper_class' => 'show_if_bundle',
				'label'         => __( 'Virtual', 'woocommerce' ),
				'description'   => __( 'Virtual bundles are intangible and are not shipped. When this option is enabled, any physical products added to this bundle will be treated as virtual.', 'woocommerce-product-bundles' ),
				'default'       => 'no',
			)
		), $options );

		return $options;
	}

	/**
	 * Shipping type image select html.
	 *
	 * @since 6.0.0
	 *
	 * @return void
	 */
	public static function bundle_shipping_type_admin_html() {
		global $product_bundle_object, $pagenow;

		$is_new_bundle = $pagenow === 'post-new.php';

		$bundle_type_options = array(
			array(
				'title'       => __( 'Unassembled', 'woocommerce-product-bundles' ),
				'description' => __( 'Bundled products preserve their individual dimensions, weight and shipping classes. A virtual container item keeps them grouped together in the cart.', 'woocommerce-product-bundles' ),
				'value'       => 'unassembled',
				'checked'     => $is_new_bundle || $product_bundle_object->is_virtual() ? ' checked="checked"' : ''
			),
			array(
				'title'       => __( 'Assembled', 'woocommerce-product-bundles' ),
				'description' => __( 'Bundled products are assembled and shipped in a new physical container with the specified dimensions, weight and shipping class. The entire bundle appears as a single physical item.</br></br>To ship a bundled product outside this container, navigate to the <strong>Bundled Products</strong> tab, expand its settings and enable <strong>Shipped Individually</strong>. Bundled products that are <strong>Shipped Individually</strong> preserve their own dimensions, weight and shipping classes.', 'woocommerce-product-bundles' ),
				'value'       => 'assembled',
				'checked'     => ! $is_new_bundle && ! $product_bundle_object->is_virtual() ? ' checked="checked"' : ''
			)
		);

		?>
		</div>
		<div class="options_group bundle_type show_if_bundle">
			<div class="form-field">
				<label><?php _e( 'Bundle type', 'woocommerce-product-bundles' ); ?></label>
				<ul class="bundle_type_options">
					<?php
					foreach ( $bundle_type_options as $type ) {
						$classes = array( $type[ 'value' ] );
						if ( ! empty( $type[ 'checked' ] ) ) {
							$classes[] = 'selected';
						}
						?>
						<li class="<?php echo implode( ' ', $classes ); ?>" >
							<input type="radio"<?php echo $type[ 'checked' ] ?> name="_bundle_type" class="bundle_type_option" value="<?php echo $type[ 'value' ] ?>">
							<?php echo wc_help_tip( '<strong>' . $type[ 'title' ] . '</strong> &ndash; ' . $type[ 'description' ] ); ?>
						</li>
						<?php
					}
					?>
				</ul>
			</div>
			<div class="wp-clearfix"></div>
			<div id="message" class="inline notice">
				<p>
					<span class="assembled_notice_title"><?php _e( 'What happened to the shipping options?', 'woocommerce-product-bundles' ); ?></span>

					<?php
						/* translators: Unassambled bundle documentation link */
						echo sprintf( __( 'The contents of this bundle preserve their dimensions, weight and shipping classes. <a href="%s" target="_blank">Unassembled</a> bundles do not have any shipping options to configure.', 'woocommerce-product-bundles' ), WC_PB()->get_resource_url( 'shipping-options' ) );
					?>
				</p>
			</div>
		<?php

		if ( wc_product_weight_enabled() ) {

			woocommerce_wp_select( array(
				'id'            => '_wc_pb_aggregate_weight',
				'wrapper_class' => 'bundle_aggregate_weight_field show_if_bundle',
				'value'         => $product_bundle_object->get_aggregate_weight( 'edit' ) ? 'preserve' : 'ignore',
				'label'         => __( 'Assembled weight', 'woocommerce-product-bundles' ),
				'description'   => __( 'Controls whether to ignore or preserve the weight of assembled bundled items.</br></br> <strong>Ignore</strong> &ndash; The specified Weight is the total weight of the entire bundle.</br></br> <strong>Preserve</strong> &ndash; The specified Weight is treated as a container weight. The total weight of the bundle is the sum of: i) the container weight, and ii) the weight of all assembled bundled items.', 'woocommerce-product-bundles' ),
				'desc_tip'      => true,
				'options'       => array(
					'ignore'        => __( 'Ignore', 'woocommerce-product-bundles' ),
					'preserve'      => __( 'Preserve', 'woocommerce-product-bundles' ),
				)
			) );
		}
	}

	/**
	 * Renders inline JS to handle product_data container classes.
	 *
	 * @since 6.0.0
	 *
	 * @return void
	 */
	public static function js_handle_container_classes() {

		$js = "
		( function( $ ) {
			$( function() {

				var shipping_product_data = $( '.product_data #shipping_product_data' ),
					bundled_product_data  = $( '.product_data #bundled_product_data' );

				$( 'select#product-type' ).on( 'change', function() {

					if ( 'bundle' !== $( this ).val() ) {

						// Clear container classes to make Shipping contents visible.
						// If we don't do this at this early point, WC will hide the Shipping tab even if the Virtual option is unchecked.

						shipping_product_data.removeClass( 'bundle_unassembled' );
						bundled_product_data.removeClass( 'bundle_unassembled' );
					}

				} );
			} );

		} )( jQuery );
		";

		// Append right after woocommerce_admin script.
		wp_add_inline_script( 'wc-admin-product-meta-boxes', $js, true );
	}

	/**
	 * Process, verify and save bundle type product data.
	 *
	 * @param  WC_Product  $product
	 *
	 * @return void
	 */
	public static function process_bundle_data( $product ) {

		if ( $product->is_type( 'bundle' ) ) {

			/*
			 * Test if 'max_input_vars' limit may have been exceeded.
			 */
			if ( isset( $_POST[ 'pb_post_control_var' ] ) && ! isset( $_POST[ 'pb_post_test_var' ] ) ) {
					/* translators: %1$s: Increase max input vars link, %2$s: Max input vars value */
				$notice = sprintf( __( 'Product Bundles has detected that your server may have failed to process and save some of the data on this page. Please get in touch with your server\'s host or administrator and (kindly) ask them to <a href="%1$s" target="_blank">increase the number of variables</a> that PHP scripts can post and process%2$s.', 'woocommerce-product-bundles' ), WC_PB()->get_resource_url( 'max-input-vars' ), function_exists( 'ini_get' ) && ini_get( 'max_input_vars' ) ? sprintf( __( ' (currently %s)', 'woocommerce-product-bundles' ), ini_get( 'max_input_vars' ) ) : '' );
				self::add_admin_notice( $notice, 'warning' );
			}

			$props = array(
				'layout'                    => 'default',
				'group_mode'                => 'parent',
				'editable_in_cart'          => false,
				'aggregate_weight'          => false,
				'sold_individually'         => false,
				'sold_individually_context' => 'product'
			);

			/*
			 * Layout.
			 */

			if ( ! empty( $_POST[ '_wc_pb_layout_style' ] ) ) {
				$props[ 'layout' ] = wc_clean( $_POST[ '_wc_pb_layout_style' ] );
			}

			/*
			 * Item grouping option.
			 */

			$group_mode_pre = $product->get_group_mode( 'edit' );

			if ( ! empty( $_POST[ '_wc_pb_group_mode' ] ) ) {
				$props[ 'group_mode' ] = wc_clean( $_POST[ '_wc_pb_group_mode' ] );
			}

			/*
			 * Cart editing option.
			 */

			if ( ! empty( $_POST[ '_wc_pb_edit_in_cart' ] ) ) {
				$props[ 'editable_in_cart' ] = true;
			}

			/*
			 * Base weight option.
			 */

			if ( ! empty( $_POST[ '_wc_pb_aggregate_weight' ] ) ) {
				$props[ 'aggregate_weight' ] = 'preserve' === $_POST[ '_wc_pb_aggregate_weight' ];
			}

			/*
			 * Extended "Sold Individually" option.
			 */

			if ( ! empty( $_POST[ '_wc_pb_sold_individually' ] ) ) {

				$sold_individually_context = wc_clean( $_POST[ '_wc_pb_sold_individually' ] );

				if ( in_array( $sold_individually_context, array( 'product', 'configuration' ) ) ) {
					$props[ 'sold_individually' ]         = true;
					$props[ 'sold_individually_context' ] = $sold_individually_context;
				}
			}

			/*
			 * "Form location" option.
			 */

			if ( ! empty( $_POST[ '_wc_pb_add_to_cart_form_location' ] ) ) {

				$form_location = wc_clean( $_POST[ '_wc_pb_add_to_cart_form_location' ] );

				if ( in_array( $form_location, array_keys( WC_Product_Bundle::get_add_to_cart_form_location_options() ) ) ) {
					$props[ 'add_to_cart_form_location' ] = $form_location;
				}
			}

			/*
			 * Virtual bundle.
			 */

			$props[ 'virtual_bundle' ] = isset( $_POST[ '_virtual_bundle' ] );

			/*
			 * Bundle shipping type.
			 */

			if ( ! empty( $_POST[ '_bundle_type' ] ) ) {
				$props[ 'virtual' ] = 'unassembled' === $_POST[ '_bundle_type' ] || isset( $_POST[ '_virtual_bundle' ] ) ? true : false;
			}

			if ( ! defined( 'WC_PB_UPDATING' ) ) {

				$posted_bundle_data    = isset( $_POST[ 'bundle_data' ] ) ? $_POST[ 'bundle_data' ] : false; // @phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$processed_bundle_data = self::process_posted_bundle_data( $posted_bundle_data, $product->get_id() );

				if ( empty( $processed_bundle_data ) ) {

					self::add_admin_error( __( 'Please add at least one product to the bundle before publishing. To add products, click on the <strong>Bundled Products</strong> tab.', 'woocommerce-product-bundles' ) );
					$props[ 'bundled_data_items' ] = array();

				} else {

					foreach ( $processed_bundle_data as $key => $data ) {
						$processed_bundle_data[ $key ] = array(
							'bundled_item_id' => $data[ 'item_id' ],
							'bundle_id'       => $product->get_id(),
							'product_id'      => $data[ 'product_id' ],
							'menu_order'      => $data[ 'menu_order' ],
							'meta_data'       => array_diff_key( $data, array( 'item_id' => 1, 'product_id' => 1, 'menu_order' => 1 ) )
						);
					}

					$props[ 'bundled_data_items' ] = $processed_bundle_data;
				}

				$product->set( $props );

			} else {
				self::add_admin_error( __( 'Your changes have not been saved &ndash; please wait for the <strong>WooCommerce Product Bundles Data Update</strong> routine to complete before creating new bundles or making changes to existing ones.', 'woocommerce-product-bundles' ) );
			}

			/*
			 * Show invalid group mode selection notice.
			 */

			if ( false === $product->validate_group_mode() ) {

				$product->set_group_mode( $group_mode_pre );

				$group_mode_options         = WC_Product_Bundle::get_group_mode_options( true );
				$group_modes_without_parent = array();

				foreach ( $group_mode_options as $group_mode_key => $group_mode_title ) {
					if ( false === WC_Product_Bundle::group_mode_has( $group_mode_key, 'parent_item' ) ) {
						$group_modes_without_parent[] = '<strong>' . $group_mode_title . '</strong>';
					}
				}

				/* translators: %1$s: Item Grouping option name, %2$s: Unassembled bundle docs URL, %3$s: Pricing URL link */
				$group_modes_without_parent_msg = sprintf( _n( '%1$s is only supported by <a href="%2$s" target="_blank">unassembled</a> bundles with an empty <a href="%3$s" target="_blank">base price</a>.', '%1$s are only supported by <a href="%2$s" target="_blank">unassembled</a> bundles with an empty <a href="%3$s" target="_blank">base price</a>.', count( $group_modes_without_parent ), 'woocommerce-product-bundles' ), WC_PB_Helpers::format_list_of_items( $group_modes_without_parent ), WC_PB()->get_resource_url( 'shipping-options' ), WC_PB()->get_resource_url( 'pricing-options' ) );
				/* translators: Reason */
				self::add_admin_error( sprintf( __( 'The chosen <strong>Item Grouping</strong> option is invalid. %s', 'woocommerce-product-bundles' ), $group_modes_without_parent_msg ) );

			}

			/*
			 * Show non-mandatory bundle notice.
			 */
			if ( 'none' !== $product->get_group_mode( 'edit' ) && $product->get_bundled_items() && ! $product->contains( 'mandatory' ) ) {

				$notice = __( 'This bundle does not contain any mandatory items. To control the minimum and/or maximum number of items that customers must choose in this bundle, use the <strong>Min Bundle Size</strong> and <strong>Max Bundle Size</strong> fields under <strong>Product Data > Bundled Products</strong>.', 'woocommerce-product-bundles' );

				self::add_admin_notice( $notice, array( 'dismiss_class' => 'process_data_min_max', 'type' => 'info' ) );
			}

			// Clear dismissible welcome notice.
			WC_PB_Admin_Notices::remove_dismissible_notice( 'welcome' );
		}
	}

	/**
	 * Sort by menu order callback.
	 *
	 * @param  array  $a
	 * @param  array  $b
	 *
	 * @return int
	 */
	public static function menu_order_sort( $a, $b ) {
		if ( isset( $a[ 'menu_order' ] ) && isset( $b[ 'menu_order' ] ) ) {
			return $a[ 'menu_order' ] - $b[ 'menu_order' ];
		} else {
			return isset( $a[ 'menu_order' ] ) ? 1 : -1;
		}
	}

	/**
	 * Process posted bundled item data.
	 *
	 * @param  array  $posted_bundle_data
	 * @param  mixed  $post_id
	 *
	 * @return mixed
	 */
	public static function process_posted_bundle_data( $posted_bundle_data, $post_id ) {

		$bundle_data = array();

		if ( ! empty( $posted_bundle_data ) ) {

			$sold_individually_notices = array();
			$times                     = array();
			$loop                      = 0;

			// Sort posted data by menu order.
			usort( $posted_bundle_data, array( __CLASS__, 'menu_order_sort' ) );

			foreach ( $posted_bundle_data as $data ) {

				$product_id = isset( $data[ 'product_id' ] ) ? absint( $data[ 'product_id' ] ) : false;
				$item_id    = isset( $data[ 'item_id' ] ) ? absint( $data[ 'item_id' ] ) : false;

				$product = wc_get_product( $product_id );

				if ( ! $product ) {
					continue;
				}

				$product_type    = $product->get_type();
				$product_title   = $product->get_title();
				$is_subscription = in_array( $product_type, array( 'subscription', 'variable-subscription' ) );

				if ( in_array( $product_type, array( 'simple', 'variable', 'subscription', 'variable-subscription' ) ) && ( $post_id != $product_id ) && ! isset( $sold_individually_notices[ $product_id ] ) ) {

					// Bundling subscription products requires Subs v2.0+.
					if ( $is_subscription ) {
						if ( ( ! class_exists( 'WC_Subscriptions' ) && ! class_exists( 'WC_Subscriptions_Core_Plugin' ) ) || ( class_exists( 'WC_Subscriptions' ) && version_compare( WC_Subscriptions::$version, '2.0.0', '<' ) ) ) {
							/* translators: Bundled product name */
							self::add_admin_error( sprintf( __( '<strong>%s</strong> was not saved. WooCommerce Subscriptions version 2.0 or higher is required in order to bundle Subscription products.', 'woocommerce-product-bundles' ), $product_title ) );
							continue;
						}
					}

					// Only allow bundling multiple instances of non-sold-individually items.
					if ( ! isset( $times[ $product_id ] ) ) {
						$times[ $product_id ] = 1;
					} else {
						if ( $product->is_sold_individually() ) {
							/* translators: Bundled product name */
							self::add_admin_error( sprintf( __( '<strong>%s</strong> is sold individually and cannot be bundled more than once.', 'woocommerce-product-bundles' ), $product_title ) );
							// Make sure we only display the notice once for every id.
							$sold_individually_notices[ $product_id ] = 'yes';
							continue;
						}
						$times[ $product_id ] += 1;
					}

					// Now start processing the posted data.
					$loop++;

					$item_data  = array();
					$item_title = $product_title;

					$item_data[ 'product_id' ] = $product_id;
					$item_data[ 'item_id' ]    = $item_id;
					$item_data[ 'title' ]      = $product_title;


					// Save thumbnail preferences first.
					if ( isset( $data[ 'hide_thumbnail' ] ) ) {
						$item_data[ 'hide_thumbnail' ] = 'yes';
					} else {
						$item_data[ 'hide_thumbnail' ] = 'no';
					}

					// Save title preferences.
					if ( isset( $data[ 'override_title' ] ) ) {
						$item_data[ 'override_title' ] = 'yes';
						$item_data[ 'title' ]          = isset( $data[ 'title' ] ) ? stripslashes( $data[ 'title' ] ) : '';
					} else {
						$item_data[ 'override_title' ] = 'no';
					}

					// Save description preferences.
					if ( isset( $data[ 'override_description' ] ) ) {
						$item_data[ 'override_description' ] = 'yes';
						$item_data[ 'description' ] = isset( $data[ 'description' ] ) ? wp_kses_post( stripslashes( $data[ 'description' ] ) ) : '';
					} else {
						$item_data[ 'override_description' ] = 'no';
					}

					// Save optional.
					if ( isset( $data[ 'optional' ] ) ) {
						$item_data[ 'optional' ] = 'yes';
					} else {
						$item_data[ 'optional' ] = 'no';
					}

					// Save item pricing scheme.
					if ( isset( $data[ 'priced_individually' ] ) ) {
						$item_data[ 'priced_individually' ] = 'yes';
					} else {
						$item_data[ 'priced_individually' ] = 'no';
					}

					// Save item shipping scheme.
					if ( isset( $data[ 'shipped_individually' ] ) || $product->is_virtual() || $is_subscription ) {
						$item_data[ 'shipped_individually' ] = 'yes';
					} else {
						$item_data[ 'shipped_individually' ] = 'no';
					}

					// Save min quantity.
					if ( isset( $data[ 'quantity_min' ] ) ) {

						if ( is_numeric( $data[ 'quantity_min' ] ) ) {

							$quantity = absint( $data[ 'quantity_min' ] );

							if ( $quantity >= 0 && $data[ 'quantity_min' ] - $quantity == 0 ) {

								if ( $quantity > 1 && $product->is_sold_individually() ) {
									/* translators: Bundled product name */
									self::add_admin_error( sprintf( __( '<strong>%s</strong> is sold individually &ndash; its <strong>Min Quantity</strong> cannot be higher than 1.', 'woocommerce-product-bundles' ), $item_title ) );
									$item_data[ 'quantity_min' ] = 1;
								} else {
									$item_data[ 'quantity_min' ] = $quantity;
								}

							} else {
								/* translators: Bundled product name */
								self::add_admin_error( sprintf( __( 'The minimum quantity of <strong>%s</strong> was not valid and has been reset. Please enter a non-negative integer <strong>Min Quantity</strong> value.', 'woocommerce-product-bundles' ), $item_title ) );
								$item_data[ 'quantity_min' ] = 1;
							}
						}

					} else {
						$item_data[ 'quantity_min' ] = 1;
					}

					$quantity_min = $item_data[ 'quantity_min' ];

					// Save max quantity.
					if ( isset( $data[ 'quantity_max' ] ) && ( is_numeric( $data[ 'quantity_max' ] ) || '' === $data[ 'quantity_max' ] ) ) {

						$quantity = '' !== $data[ 'quantity_max' ] ? absint( $data[ 'quantity_max' ] ) : '';

						if ( '' === $quantity || ( $quantity > 0 && $quantity >= $quantity_min && $data[ 'quantity_max' ] - $quantity == 0 ) ) {

							if ( $quantity !== 1 && $product->is_sold_individually() ) {
								/* translators: Bundled product name */
								self::add_admin_error( sprintf( __( '<strong>%s</strong> is sold individually &ndash; <strong>Max Quantity</strong> cannot be higher than 1.', 'woocommerce-product-bundles' ), $item_title ) );
								$item_data[ 'quantity_max' ] = 1;
							} else {
								$item_data[ 'quantity_max' ] = $quantity;
							}

						} else {

							/* translators: Bundled product name */
							self::add_admin_error( sprintf( __( 'The maximum quantity of <strong>%s</strong> was not valid and has been reset. Please enter a positive integer equal to or higher than <strong>Min Quantity</strong>, or leave the <strong>Max Quantity</strong> field empty for an unlimited maximum quantity.', 'woocommerce-product-bundles' ), $item_title ) );

							if ( 0 === $quantity_min ) {
								$item_data[ 'quantity_max' ] = 1;
							} else {
								$item_data[ 'quantity_max' ] = $quantity_min;
							}
						}

					} else {
						$item_data[ 'quantity_max' ] = max( $quantity_min, 1 );
					}

					$quantity_max = $item_data[ 'quantity_max' ];

					// Save default quantity.
					if ( isset( $data[ 'quantity_default' ] ) && is_numeric( $data[ 'quantity_default' ] ) ) {

						$quantity = absint( $data[ 'quantity_default' ] );

						if ( $quantity >= $quantity_min && ( $quantity <= $quantity_max || '' === $quantity_max ) ) {
							$item_data[ 'quantity_default' ] = $quantity;
						} else {
							/* translators: Bundled product name */
							self::add_admin_error( sprintf( __( 'The default quantity of <strong>%s</strong> was not valid and has been reset. Please enter an integer between the <strong>Min Quantity</strong> and <strong>Max Quantity</strong>.', 'woocommerce-product-bundles' ), $item_title ) );
							$item_data[ 'quantity_default' ] = $quantity_min;
						}

					} else {
						$item_data[ 'quantity_default' ] = $quantity_min;
					}

					// Save discount data. 0% discounts are skipped.
					if ( isset( $data[ 'discount' ] ) && ! empty( $data[ 'discount' ] ) &&  'yes' === $item_data[ 'priced_individually' ] ) {

						// wc_format_decimal returns an empty string if a string input is given.
						// Cast result to float to check that the discount value is between 0-100.
						// Casting empty strings to float returns 0.
						$discount = (float) wc_format_decimal( $data[ 'discount' ] );

						// Throw error if discount is not within the 0-100 range or if a string was passed to wc_format_decimal.
						if ( empty( $discount ) || $discount < 0 || $discount > 100 ) {
							/* translators: Bundled product name */
							self::add_admin_error( sprintf( __( 'The <strong>Discount</strong> of <strong>%s</strong> was not valid and has been reset. Please enter a positive number between 0-100.', 'woocommerce-product-bundles' ), $item_title ) );
							$item_data[ 'discount' ] = '';
						} else {
							$item_data[ 'discount' ] = $discount;
						}
					} else {
						$item_data[ 'discount' ] = '';
					}

					// Save data related to variable items.
					if ( in_array( $product_type, array( 'variable', 'variable-subscription' ) ) ) {

						$allowed_variations = array();

						// Save variation filtering options.
						if ( isset( $data[ 'override_variations' ] ) ) {

							if ( isset( $data[ 'allowed_variations' ] ) ) {

								if ( is_array( $data[ 'allowed_variations' ] ) ) {
									$allowed_variations = array_map( 'intval', $data[ 'allowed_variations' ] );
								} else {
									$allowed_variations = array_filter( array_map( 'intval', explode( ',', $data[ 'allowed_variations' ] ) ) );
								}

								if ( count( $allowed_variations ) > 0 ) {

									$item_data[ 'override_variations' ] = 'yes';

									$item_data[ 'allowed_variations' ] = $allowed_variations;

									if ( isset( $data[ 'hide_filtered_variations' ] ) ) {
										$item_data[ 'hide_filtered_variations' ] = 'yes';
									} else {
										$item_data[ 'hide_filtered_variations' ] = 'no';
									}
								}
							} else {
								$item_data[ 'override_variations' ] = 'no';
								/* translators: Bundled product name */
								self::add_admin_error( sprintf( __( 'Failed to save <strong>Filter Variations</strong> for <strong>%s</strong>. Please choose at least one variation.', 'woocommerce-product-bundles' ), $item_title ) );
							}
						} else {
							$item_data[ 'override_variations' ] = 'no';
						}

						// Save defaults.
						if ( isset( $data[ 'override_default_variation_attributes' ] ) ) {

							if ( isset( $data[ 'default_variation_attributes' ] ) ) {

								// If filters are set, check that the selections are valid.
								if ( isset( $data[ 'override_variations' ] ) && ! empty( $allowed_variations ) ) {

									// The array to store all valid attribute options of the iterated product.
									$filtered_attributes = array();

									// Populate array with valid attributes.
									foreach ( $allowed_variations as $variation ) {

										$variation_data = array();

										// Get variation attributes.
										$variation_data = wc_get_product_variation_attributes( $variation );

										foreach ( $variation_data as $name => $value ) {

											$attribute_name  = substr( $name, strlen( 'attribute_' ) );
											$attribute_value = $value;

											// Populate array.
											if ( ! isset( $filtered_attributes[ $attribute_name ] ) ) {
												$filtered_attributes[ $attribute_name ][] = $attribute_value;
											} elseif ( ! in_array( $attribute_value, $filtered_attributes[ $attribute_name ] ) ) {
												$filtered_attributes[ $attribute_name ][] = $attribute_value;
											}
										}
									}

									// Check validity.
									foreach ( $data[ 'default_variation_attributes' ] as $name => $value ) {

										if ( '' === $value ) {
											continue;
										}

										if ( ! in_array( stripslashes( $value ), $filtered_attributes[ $name ] ) && ! in_array( '', $filtered_attributes[ $name ] ) ) {
											// Set option to "Any".
											$data[ 'default_variation_attributes' ][ $name ] = '';
											// Show an error.
											/* translators: Bundled product name */
											self::add_admin_error( sprintf( __( 'The default variation attribute values of <strong>%s</strong> are inconsistent with the set of active variations and have been reset.', 'woocommerce-product-bundles' ), $item_title ) );
											continue;
										}
									}
								}

								// Save.
								foreach ( $data[ 'default_variation_attributes' ] as $name => $value ) {
									$item_data[ 'default_variation_attributes' ][ $name ] = stripslashes( $value );
								}

								$item_data[ 'override_default_variation_attributes' ] = 'yes';
							}

						} else {
							$item_data[ 'override_default_variation_attributes' ] = 'no';
						}
					}

					// Save item visibility preferences.
					$visibility = array(
						'product' => isset( $data[ 'single_product_visibility' ] ) ? 'visible' : 'hidden',
						'cart'    => isset( $data[ 'cart_visibility' ] ) ? 'visible' : 'hidden',
						'order'   => isset( $data[ 'order_visibility' ] ) ? 'visible' : 'hidden'
					);

					if ( 'hidden' === $visibility[ 'product' ] ) {

						if ( in_array( $product_type, array( 'variable', 'variable-subscription' ) ) ) {

							if ( 'yes' === $item_data[ 'override_default_variation_attributes' ] ) {

								if ( ! empty( $data[ 'default_variation_attributes' ] ) ) {

									foreach ( $data[ 'default_variation_attributes' ] as $default_name => $default_value ) {
										if ( '' === $default_value ) {
											$visibility[ 'product' ] = 'visible';
											/* translators: Bundled product name */
											self::add_admin_error( sprintf( __( 'To hide <strong>%s</strong> from the single-product template, please enable the <strong>Override Default Selections</strong> option and choose default variation attribute values.', 'woocommerce-product-bundles' ), $item_title ) );
											break;
										}
									}

								} else {
									$visibility[ 'product' ] = 'visible';
								}

							} else {
								/* translators: Bundled product name */
								self::add_admin_error( sprintf( __( 'To hide <strong>%s</strong> from the single-product template, please enable the <strong>Override Default Selections</strong> option and choose default variation attribute values.', 'woocommerce-product-bundles' ), $item_title ) );
								$visibility[ 'product' ] = 'visible';
							}
						}
					}

					$item_data[ 'single_product_visibility' ] = $visibility[ 'product' ];
					$item_data[ 'cart_visibility' ]           = $visibility[ 'cart' ];
					$item_data[ 'order_visibility' ]          = $visibility[ 'order' ];

					// Save price visibility preferences.

					$item_data[ 'single_product_price_visibility' ] = isset( $data[ 'single_product_price_visibility' ] ) ? 'visible' : 'hidden';
					$item_data[ 'cart_price_visibility' ]           = isset( $data[ 'cart_price_visibility' ] ) ? 'visible' : 'hidden';
					$item_data[ 'order_price_visibility' ]          = isset( $data[ 'order_price_visibility' ] ) ? 'visible' : 'hidden';

					// Save position data.
					$item_data[ 'menu_order' ] = absint( $data[ 'menu_order' ] );

					/**
					 * Filter processed data before saving/updating WC_Bundled_Item_Data objects.
					 *
					 * @param  array  $item_data
					 * @param  array  $data
					 * @param  mixed  $item_id
					 * @param  mixed  $post_id
					 */
					$bundle_data[] = apply_filters( 'woocommerce_bundles_process_bundled_item_admin_data', $item_data, $data, $item_id, $post_id );
				}
			}
		}

		return $bundle_data;
	}

	/**
	 * Add bundled product "Basic" tab content.
	 *
	 * @param  int    $loop
	 * @param  int    $product_id
	 * @param  array  $item_data
	 * @param  int    $post_id
	 *
	 * @return void
	 */
	public static function bundled_product_admin_config_html( $loop, $product_id, $item_data, $post_id ) {

		$bundled_product = isset( $item_data[ 'bundled_item' ] ) ? $item_data[ 'bundled_item' ]->product : wc_get_product( $product_id );
		$is_subscription = $bundled_product->is_type( array( 'subscription', 'variable-subscription' ) );

		if ( in_array( $bundled_product->get_type(), array( 'variable', 'variable-subscription' ) ) ) {

			$allowed_variations  = isset( $item_data[ 'allowed_variations' ] ) ? $item_data[ 'allowed_variations' ] : '';
			$default_attributes  = isset( $item_data[ 'default_variation_attributes' ] ) ? $item_data[ 'default_variation_attributes' ] : '';

			$override_variations = isset( $item_data[ 'override_variations' ] ) && 'yes' === $item_data[ 'override_variations' ] ? 'yes' : '';
			$override_defaults   = isset( $item_data[ 'override_default_variation_attributes' ] ) && 'yes' === $item_data[ 'override_default_variation_attributes' ] ? 'yes' : '';

			?><div class="override_variations">
				<div class="form-field">
					<label for="override_variations">
						<?php echo __( 'Filter Variations', 'woocommerce-product-bundles' ); ?>
					</label>
					<input type="checkbox" class="checkbox"<?php echo ( 'yes' === $override_variations ? ' checked="checked"' : '' ); ?> name="bundle_data[<?php echo $loop; ?>][override_variations]" <?php echo ( 'yes' === $override_variations ? 'value="1"' : '' ); ?>/>
					<?php echo wc_help_tip( __( 'Check to enable only a subset of the available variations.', 'woocommerce-product-bundles' ) ); ?>
				</div>
			</div>


			<div class="allowed_variations" <?php echo 'yes' === $override_variations ? '' : 'style="display:none;"'; ?>>
				<div class="form-field"><?php

					$variations = $bundled_product->get_children();
					$attributes = $bundled_product->get_attributes();

					if ( count( $variations ) < 50 ) {

						?><select multiple="multiple" name="bundle_data[<?php echo $loop; ?>][allowed_variations][]" style="width: 95%;" data-placeholder="<?php _e( 'Choose variations&hellip;', 'woocommerce-product-bundles' ); ?>" class="sw-select2"> <?php

							foreach ( $variations as $variation_id ) {

								if ( is_array( $allowed_variations ) && in_array( $variation_id, $allowed_variations ) ) {
									$selected = 'selected="selected"';
								} else {
									$selected = '';
								}

								$variation_description = WC_PB_Helpers::get_product_variation_title( $variation_id, 'flat' );

								if ( ! $variation_description ) {
									continue;
								}

								echo '<option value="' . $variation_id . '" ' . $selected . '>' . $variation_description . '</option>';
							}

						?></select><?php

					} else {

						$allowed_variations_descriptions = array();

						if ( ! empty( $allowed_variations ) ) {

							foreach ( $allowed_variations as $allowed_variation_id ) {

								$variation_description = WC_PB_Helpers::get_product_variation_title( $allowed_variation_id, 'flat' );

								if ( ! $variation_description ) {
									continue;
								}

								$allowed_variations_descriptions[ $allowed_variation_id ] = $variation_description;
							}
						}

						?><select class="sw-select2-search--products" multiple="multiple" style="width: 95%;" name="bundle_data[<?php echo $loop; ?>][allowed_variations][]" data-placeholder="<?php _e( 'Search for variations&hellip;', 'woocommerce-product-bundles' ); ?>" data-action="woocommerce_search_bundled_variations" data-limit="500" data-include="<?php echo $product_id; ?>"><?php
							foreach ( $allowed_variations_descriptions as $allowed_variation_id => $allowed_variation_description ) {
								echo '<option value="' . esc_attr( $allowed_variation_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $allowed_variation_description ) . '</option>';
							}
						?></select><?php
					}

				?></div>
			</div>

			<div class="override_default_variation_attributes">
				<div class="form-field">
					<label for="override_default_variation_attributes"><?php echo __( 'Override Default Selections', 'woocommerce-product-bundles' ) ?></label>
					<input type="checkbox" class="checkbox"<?php echo ( 'yes' === $override_defaults ? ' checked="checked"' : '' ); ?> name="bundle_data[<?php echo $loop; ?>][override_default_variation_attributes]" <?php echo ( 'yes' === $override_defaults ? 'value="1"' : '' ); ?>/>
					<?php echo wc_help_tip( __( 'In effect for this bundle only. When <strong>Filter Variations</strong> is enabled, double-check your selections to make sure they correspond to an active variation.', 'woocommerce-product-bundles' ) ); ?>
				</div>
			</div>

			<div class="default_variation_attributes" <?php echo 'yes' === $override_defaults ? '' : 'style="display:none;"'; ?>>
				<div class="form-field"><?php

					foreach ( $attributes as $attribute ) {

						if ( ! $attribute->get_variation() ) {
							continue;
						}

						$selected_value = isset( $default_attributes[ sanitize_title( $attribute->get_name() ) ] ) ? $default_attributes[ sanitize_title( $attribute->get_name() ) ] : '';

						?><select name="bundle_data[<?php echo $loop; ?>][default_variation_attributes][<?php echo sanitize_title( $attribute->get_name() ); ?>]" data-current="<?php echo esc_attr( $selected_value ); ?>">

							<option value=""><?php echo esc_html( sprintf( __( 'No default %s&hellip;', 'woocommerce' ), wc_attribute_label( $attribute->get_name() ) ) ); ?></option><?php

							if ( $attribute->is_taxonomy() ) {
								foreach ( $attribute->get_terms() as $option ) {
									?><option <?php selected( $selected_value, $option->slug ); ?> value="<?php echo esc_attr( $option->slug ); ?>"><?php echo esc_html( apply_filters( 'woocommerce_variation_option_name', $option->name ) ); ?></option><?php
								}
							} else {
								foreach ( $attribute->get_options() as $option ) {
									?><option <?php selected( $selected_value, $option ); ?> value="<?php echo esc_attr( $option ); ?>"><?php echo esc_html( apply_filters( 'woocommerce_variation_option_name', $option ) ); ?></option><?php
								}
							}

						?></select><?php
					}

				?></div>
			</div><?php
		}

		$item_quantity         = isset( $item_data[ 'quantity_min' ] ) ? absint( $item_data[ 'quantity_min' ] ) : 1;
		$item_quantity_max     = $item_quantity;
		$item_quantity_default = $item_quantity;

		if ( isset( $item_data[ 'quantity_max' ] ) ) {
			if ( '' !== $item_data[ 'quantity_max' ] ) {
				$item_quantity_max = absint( $item_data[ 'quantity_max' ] );
			} else {
				$item_quantity_max = '';
			}
		}

		if ( isset( $item_data[ 'quantity_default' ] ) ) {
			$item_quantity_default = absint( $item_data[ 'quantity_default' ] ) ;
		}

		$is_priced_individually  = isset( $item_data[ 'priced_individually' ] ) && 'yes' === $item_data[ 'priced_individually' ] ? 'yes' : '';
		$is_shipped_individually = isset( $item_data[ 'shipped_individually' ] ) && 'yes' === $item_data[ 'shipped_individually' ] ? 'yes' : '';
		$item_discount           = isset( $item_data[ 'discount' ] ) && (float) $item_data[ 'discount' ] > 0 ? wc_format_localized_decimal( $item_data[ 'discount' ] )  : '';
		$is_optional             = isset( $item_data[ 'optional' ] ) ? $item_data[ 'optional' ] : '';
		$step                    = isset( $item_data[ 'step' ] ) ? $item_data[ 'step' ] : 'any';

		// When adding a subscription-type product for the first time, enable "Priced Individually" by default.
		if ( did_action( 'wp_ajax_woocommerce_add_bundled_product' ) && $is_subscription && ! isset( $item_data[ 'priced_individually' ] ) ) {
			$is_priced_individually = 'yes';
		}

		?><div class="quantity_min">
			<div class="form-field">
				<label for="item_quantity_min_<?php echo $loop; ?>"><?php echo __( 'Min Quantity', 'woocommerce-product-bundles' ); ?></label>
				<input id="item_quantity_min_<?php echo $loop; ?>" type="number" class="item_quantity item_quantity_min" size="6" name="bundle_data[<?php echo $loop; ?>][quantity_min]" value="<?php echo $item_quantity; ?>" step="<?php echo $step; ?>" min="0" />
				<?php echo wc_help_tip( __( 'The minimum quantity of this bundled product.', 'woocommerce-product-bundles' ) ); ?>
			</div>
		</div>

		<div class="quantity_max">
			<div class="form-field">
				<label for="item_quantity_max_<?php echo $loop; ?>"><?php echo __( 'Max Quantity', 'woocommerce-product-bundles' ); ?></label>
				<input id="item_quantity_max_<?php echo $loop; ?>" type="number" class="item_quantity item_quantity_max" size="6" name="bundle_data[<?php echo $loop; ?>][quantity_max]" value="<?php echo $item_quantity_max; ?>" step="<?php echo $step; ?>" min="<?php echo $item_quantity; ?>" />
				<?php echo wc_help_tip( __( 'The maximum quantity of this bundled product. Leave the field empty for an unlimited maximum quantity.', 'woocommerce-product-bundles' ) ); ?>
			</div>
		</div>

		<div class="quantity_default">
			<div class="form-field">
				<label for="item_quantity_default_<?php echo $loop; ?>"><?php echo __( 'Default Quantity', 'woocommerce-product-bundles' ); ?></label>
				<input id="item_quantity_default_<?php echo $loop; ?>" type="number" class="item_quantity item_quantity_default" size="6" name="bundle_data[<?php echo $loop; ?>][quantity_default]" value="<?php echo $item_quantity_default; ?>" step="<?php echo $step; ?>" min="<?php echo $item_quantity; ?>" max="<?php echo $item_quantity_max; ?>" />
				<?php echo wc_help_tip( __( 'The default quantity of this bundled product.', 'woocommerce-product-bundles' ) ); ?>
			</div>
		</div>

		<div class="optional" data-is_optional_qty_zero="<?php echo ( 'yes' === $is_optional && 0 === $item_quantity ? 'yes' : 'no' ); ?>" <?php echo ( $item_quantity === 0 && 'yes' !== $is_optional ? 'style="display:none;"' : '' ); ?>">
			<div class="form-field">
				<label for="optional_<?php echo $loop; ?>"><?php echo __( 'Optional', 'woocommerce-product-bundles' ) ?></label>
				<input id="optional_<?php echo $loop; ?>" type="checkbox" class="checkbox"<?php echo ( 'yes' === $is_optional ? ' checked="checked"' : '' ); ?> name="bundle_data[<?php echo $loop; ?>][optional]" <?php echo ( 'yes' === $is_optional ? 'value="1"' : '' ); ?>/>
				<?php echo wc_help_tip( __( 'Check this option to mark the bundled product as optional.', 'woocommerce-product-bundles' ) ); ?>
			</div>
		</div>

		<?php if ( $bundled_product->needs_shipping() && ! $is_subscription ) : ?>

			<div class="shipped_individually">
				<div class="form-field">
					<label for="shipped_individually_<?php echo $loop; ?>"><?php echo __( 'Shipped Individually', 'woocommerce-product-bundles' ); ?></label>
					<input id="shipped_individually_<?php echo $loop; ?>" type="checkbox" class="checkbox"<?php echo ( 'yes' === $is_shipped_individually ? ' checked="checked"' : '' ); ?> name="bundle_data[<?php echo $loop; ?>][shipped_individually]" <?php echo ( 'yes' === $is_shipped_individually ? 'value="1"' : '' ); ?>/>
					<?php echo wc_help_tip( __( 'Check this option if this bundled item is shipped separately from the bundle.', 'woocommerce-product-bundles' ) ); ?>
				</div>
			</div>

		<?php endif; ?>

		<div class="priced_individually">
			<div class="form-field">
				<label for="priced_individually_<?php echo $loop; ?>"><?php echo __( 'Priced Individually', 'woocommerce-product-bundles' ); ?></label>
				<input id="priced_individually_<?php echo $loop; ?>" type="checkbox" class="checkbox"<?php echo ( 'yes' === $is_priced_individually ? ' checked="checked"' : '' ); ?> name="bundle_data[<?php echo $loop; ?>][priced_individually]" <?php echo ( 'yes' === $is_priced_individually ? 'value="1"' : '' ); ?>/>
				<?php echo wc_help_tip( __( 'Check this option to have the price of this bundled item added to the base price of the bundle.', 'woocommerce-product-bundles' ) ); ?>
			</div>
		</div>

		<div class="discount" <?php echo 'yes' === $is_priced_individually ? '' : 'style="display:none;"'; ?>>
			<div class="form-field">
				<label for="discount_<?php echo $loop; ?>"><?php echo __( 'Discount %', 'woocommerce-product-bundles' ); ?></label>
				<input id="discount_<?php echo $loop; ?>" type="text" class="input-text item_discount wc_input_decimal" size="5" name="bundle_data[<?php echo $loop; ?>][discount]" value="<?php echo $item_discount; ?>" />
				<?php echo wc_help_tip( __( 'Discount applied to the price of this bundled product when Priced Individually is checked. If the bundled product has a Sale Price, the discount is applied on top of the Sale Price.', 'woocommerce-product-bundles' ) ); ?>
			</div>
		</div><?php
	}

	/**
	 * Add bundled product "Advanced" tab content.
	 *
	 * @param  int    $loop
	 * @param  int    $product_id
	 * @param  array  $item_data
	 * @param  int    $post_id
	 *
	 * @return void
	 */
	public static function bundled_product_admin_advanced_html( $loop, $product_id, $item_data, $post_id ) {

		$is_priced_individually = isset( $item_data[ 'priced_individually' ] ) && 'yes' === $item_data[ 'priced_individually' ];
		$hide_thumbnail         = isset( $item_data[ 'hide_thumbnail' ] ) ? $item_data[ 'hide_thumbnail' ] : '';
		$override_title         = isset( $item_data[ 'override_title' ] ) ? $item_data[ 'override_title' ] : '';
		$override_description   = isset( $item_data[ 'override_description' ] ) ? $item_data[ 'override_description' ] : '';
		$visibility             = array(
			'product' => ! empty( $item_data[ 'single_product_visibility' ] ) && 'hidden' === $item_data[ 'single_product_visibility' ] ? 'hidden' : 'visible',
			'cart'    => ! empty( $item_data[ 'cart_visibility' ] ) && 'hidden' === $item_data[ 'cart_visibility' ] ? 'hidden' : 'visible',
			'order'   => ! empty( $item_data[ 'order_visibility' ] ) && 'hidden' === $item_data[ 'order_visibility' ] ? 'hidden' : 'visible',
		);
		$price_visibility       = array(
			'product' => ! empty( $item_data[ 'single_product_price_visibility' ] ) && 'hidden' === $item_data[ 'single_product_price_visibility' ] ? 'hidden' : 'visible',
			'cart'    => ! empty( $item_data[ 'cart_price_visibility' ] ) && 'hidden' === $item_data[ 'cart_price_visibility' ] ? 'hidden' : 'visible',
			'order'   => ! empty( $item_data[ 'order_price_visibility' ] ) && 'hidden' === $item_data[ 'order_price_visibility' ] ? 'hidden' : 'visible',
		);

		?><div class="item_visibility">
			<div class="form-field">
				<label><?php _e( 'Visibility', 'woocommerce-product-bundles' ); ?></label>
				<div>
					<input type="checkbox" class="checkbox visibility_product"<?php echo ( 'visible' === $visibility[ 'product' ] ? ' checked="checked"' : '' ); ?> name="bundle_data[<?php echo $loop; ?>][single_product_visibility]" <?php echo ( 'visible' === $visibility[ 'product' ] ? 'value="1"' : '' ); ?>/>
					<span class="labelspan"><?php _e( 'Product details', 'woocommerce-product-bundles' ); ?></span>
					<?php echo wc_help_tip( __( 'Controls the visibility of the bundled item in the single-product template of this bundle.', 'woocommerce-product-bundles' ) ); ?>
				</div>
				<div>
					<input type="checkbox" class="checkbox visibility_cart"<?php echo ( 'visible' === $visibility[ 'cart' ] ? ' checked="checked"' : '' ); ?> name="bundle_data[<?php echo $loop; ?>][cart_visibility]" <?php echo ( 'visible' === $visibility[ 'cart' ] ? 'value="1"' : '' ); ?>/>
					<span class="labelspan"><?php _e( 'Cart/checkout', 'woocommerce-product-bundles' ); ?></span>
					<?php echo wc_help_tip( __( 'Controls the visibility of the bundled item in cart/checkout templates.', 'woocommerce-product-bundles' ) ); ?>
				</div>
				<div>
					<input type="checkbox" class="checkbox visibility_order"<?php echo ( 'visible' === $visibility[ 'order' ] ? ' checked="checked"' : '' ); ?> name="bundle_data[<?php echo $loop; ?>][order_visibility]" <?php echo ( 'visible' === $visibility[ 'order' ] ? 'value="1"' : '' ); ?>/>
					<span class="labelspan"><?php _e( 'Order details', 'woocommerce-product-bundles' ); ?></span>
					<?php echo wc_help_tip( __( 'Controls the visibility of the bundled item in order-details and e-mail templates.', 'woocommerce-product-bundles' ) ); ?>
				</div>
			</div>
		</div>
		<div class="price_visibility" <?php echo $is_priced_individually ? '' : 'style="display:none;"'; ?>>
			<div class="form-field">
				<label><?php _e( 'Price Visibility', 'woocommerce-product-bundles' ); ?></label>
				<div class="price_visibility_product_wrapper">
					<input type="checkbox" class="checkbox price_visibility_product"<?php echo ( 'visible' === $price_visibility[ 'product' ] ? ' checked="checked"' : '' ); ?> name="bundle_data[<?php echo $loop; ?>][single_product_price_visibility]" <?php echo ( 'visible' === $price_visibility[ 'product' ] ? 'value="1"' : '' ); ?>/>
					<span class="labelspan"><?php _e( 'Product details', 'woocommerce-product-bundles' ); ?></span>
					<?php echo wc_help_tip( __( 'Controls the visibility of the bundled-item price in the single-product template of this bundle.', 'woocommerce-product-bundles' ) ); ?>
				</div>
				<div class="price_visibility_cart_wrapper">
					<input type="checkbox" class="checkbox price_visibility_cart"<?php echo ( 'visible' === $price_visibility[ 'cart' ] ? ' checked="checked"' : '' ); ?> name="bundle_data[<?php echo $loop; ?>][cart_price_visibility]" <?php echo ( 'visible' === $price_visibility[ 'cart' ] ? 'value="1"' : '' ); ?>/>
					<span class="labelspan"><?php _e( 'Cart/checkout', 'woocommerce-product-bundles' ); ?></span>
					<?php echo wc_help_tip( __( 'Controls the visibility of the bundled-item price in cart/checkout templates.', 'woocommerce-product-bundles' ) ); ?>
				</div>
				<div class="price_visibility_order_wrapper">
					<input type="checkbox" class="checkbox price_visibility_order"<?php echo ( 'visible' === $price_visibility[ 'order' ] ? ' checked="checked"' : '' ); ?> name="bundle_data[<?php echo $loop; ?>][order_price_visibility]" <?php echo ( 'visible' === $price_visibility[ 'order' ] ? 'value="1"' : '' ); ?>/>
					<span class="labelspan"><?php _e( 'Order details', 'woocommerce-product-bundles' ); ?></span>
					<?php echo wc_help_tip( __( 'Controls the visibility of the bundled-item price in order-details and e-mail templates.', 'woocommerce-product-bundles' ) ); ?>
				</div>
			</div>
		</div>
		<div class="override_title">
			<div class="form-field override_title">
				<label for="override_title_<?php echo $loop; ?>"><?php echo __( 'Override Title', 'woocommerce-product-bundles' ) ?></label>
				<input id="override_title_<?php echo $loop; ?>" type="checkbox" class="checkbox"<?php echo ( 'yes' === $override_title ? ' checked="checked"' : '' ); ?> name="bundle_data[<?php echo $loop; ?>][override_title]" <?php echo ( 'yes' === $override_title ? 'value="1"' : '' ); ?>/>
				<?php echo wc_help_tip( __( 'Check this option to override the default product title.', 'woocommerce-product-bundles' ) ); ?>
			</div>
		</div>
		<div class="custom_title">
			<div class="form-field item_title"><?php

				$title = isset( $item_data[ 'title' ] ) ? $item_data[ 'title' ] : '';

				?><textarea name="bundle_data[<?php echo $loop; ?>][title]" placeholder="" rows="2" cols="20"><?php echo esc_textarea( $title ); ?></textarea>
			</div>
		</div>
		<div class="override_description">
			<div class="form-field">
				<label for="override_description_<?php echo $loop; ?>"><?php echo __( 'Override Short Description', 'woocommerce-product-bundles' ) ?></label>
				<input id="override_description_<?php echo $loop; ?>" type="checkbox" class="checkbox"<?php echo ( 'yes' === $override_description ? ' checked="checked"' : '' ); ?> name="bundle_data[<?php echo $loop; ?>][override_description]" <?php echo ( 'yes' === $override_description ? 'value="1"' : '' ); ?>/>
				<?php echo wc_help_tip( __( 'Check this option to override the default short product description.', 'woocommerce-product-bundles' ) ); ?>
			</div>
		</div>
		<div class="custom_description">
			<div class="form-field item_description"><?php

				$description = isset( $item_data[ 'description' ] ) ? $item_data[ 'description' ] : '';

				?><textarea name="bundle_data[<?php echo $loop; ?>][description]" placeholder="" rows="2" cols="20"><?php echo esc_textarea( $description ); ?></textarea>
			</div>
		</div>
		<div class="hide_thumbnail">
			<div class="form-field">
				<label for="hide_thumbnail_<?php echo $loop; ?>"><?php echo __( 'Hide Thumbnail', 'woocommerce-product-bundles' ) ?></label>
				<input id="hide_thumbnail_<?php echo $loop; ?>" type="checkbox" class="checkbox"<?php echo ( 'yes' === $hide_thumbnail ? ' checked="checked"' : '' ); ?> name="bundle_data[<?php echo $loop; ?>][hide_thumbnail]" <?php echo ( 'yes' === $hide_thumbnail ? 'value="1"' : '' ); ?>/>
				<?php echo wc_help_tip( __( 'Check this option to hide the thumbnail image of this bundled product.', 'woocommerce-product-bundles' ) ); ?>
			</div>
		</div><?php
	}

	/**
	 * Add bundled item id in "Advanced" tab content.
	 *
	 * @since  5.9.0
	 *
	 * @param  int    $loop
	 * @param  int    $product_id
	 * @param  array  $item_data
	 * @param  int    $post_id
	 *
	 * @return void
	 */
	public static function bundled_product_admin_advanced_item_id_html( $loop, $product_id, $item_data, $post_id ) {

		if ( ! empty( $item_data[ 'bundled_item' ] ) ) {

			?><span class="item-id">
				<?php
				/* translators: Bundled item ID */
				echo sprintf( _x( 'Item ID: %s', 'bundled product id', 'woocommerce-product-bundles' ), $item_data[ 'bundled_item' ]->get_id() ); ?>
			</span><?php
		}
	}

	/**
	 * Render "Layout" option on 'woocommerce_bundled_products_admin_config'.
	 *
	 * @param  WC_Product_Bundle  $product_bundle_object
	 */
	public static function bundled_products_admin_config_layout( $product_bundle_object ) {

		woocommerce_wp_select( array(
			'id'            => '_wc_pb_layout_style',
			'wrapper_class' => 'bundled_product_data_field',
			'value'         => $product_bundle_object->get_layout( 'edit' ),
			'label'         => __( 'Layout', 'woocommerce-product-bundles' ),
			'description'   => __( 'Select the <strong>Tabular</strong> option to have the thumbnails, descriptions and quantities of bundled products arranged in a table. Recommended for displaying multiple bundled products with configurable quantities.', 'woocommerce-product-bundles' ),
			'desc_tip'      => true,
			'options'       => WC_Product_Bundle::get_layout_options()
		) );
	}

	/**
	 * Displays the "Form Location" option.
	 *
	 * @since  5.8.0
	 *
	 * @param  WC_Product_Bundle  $product_bundle_object
	 */
	public static function bundled_products_admin_config_form_location( $product_bundle_object ) {

		$options  = WC_Product_Bundle::get_add_to_cart_form_location_options();
		$help_tip = '';
		$loop     = 0;

		foreach ( $options as $option_key => $option ) {

			$help_tip .= '<strong>' . $option[ 'title' ] . '</strong> &ndash; ' . $option[ 'description' ];

			if ( $loop < count( $options ) - 1 ) {
				$help_tip .= '</br></br>';
			}

			$loop++;
		}

		woocommerce_wp_select( array(
			'id'            => '_wc_pb_add_to_cart_form_location',
			'wrapper_class' => 'bundled_product_data_field',
			'label'         => __( 'Form Location', 'woocommerce-product-bundles' ),
			'options'       => array_combine( array_keys( $options ), wp_list_pluck( $options, 'title' ) ),
			'value'         => $product_bundle_object->get_add_to_cart_form_location( 'edit' ),
			'description'   => $help_tip,
			'desc_tip'      => 'true'
		) );
	}

	/**
	 * Render "Item grouping" option on 'woocommerce_bundled_products_admin_config'.
	 *
	 * @param  WC_Product_Bundle  $product_bundle_object
	 */
	public static function bundled_products_admin_config_group_mode( $product_bundle_object ) {

		$group_mode_options = WC_Product_Bundle::get_group_mode_options( true );

		$group_modes_without_parent = array();

		foreach ( $group_mode_options as $group_mode_key => $group_mode_title ) {
			if ( false === WC_Product_Bundle::group_mode_has( $group_mode_key, 'parent_item' ) ) {
				$group_modes_without_parent[] = '<strong>' . $group_mode_title . '</strong>';
			}
		}

		woocommerce_wp_select( array(
			'id'            => '_wc_pb_group_mode',
			'wrapper_class' => 'bundle_group_mode bundled_product_data_field',
			'value'         => $product_bundle_object->get_group_mode( 'edit' ),
			'label'         => __( 'Item Grouping', 'woocommerce-product-bundles' ),
			'description'   => __( 'Controls the grouping of parent/child line items in cart/order templates.', 'woocommerce-product-bundles' ),
			'options'       => $group_mode_options,
			'desc_tip'      => true
		) );
	}

	/**
	 * Render "Edit in Cart" option on 'woocommerce_bundled_products_admin_config'.
	 *
	 * @param  WC_Product_Bundle  $product_bundle_object
	 */
	public static function bundled_products_admin_config_edit_in_cart( $product_bundle_object ) {

		woocommerce_wp_checkbox( array(
			'id'            => '_wc_pb_edit_in_cart',
			'wrapper_class' => 'bundled_product_data_field',
			'label'         => __( 'Edit in Cart', 'woocommerce-product-bundles' ),
			'value'         => $product_bundle_object->get_editable_in_cart( 'edit' ) ? 'yes' : 'no',
			'description'   => __( 'Enable this option to allow changing the configuration of this bundle in the cart. Applicable to bundles with configurable attributes and/or quantities.', 'woocommerce-product-bundles' ),
			'desc_tip'      => true
		) );
	}

	/**
	 * Render bundled product settings on 'woocommerce_bundled_products_admin_config'.
	 *
	 * @since  5.8.0
	 *
	 * @param  WC_Product_Bundle  $product_bundle_object
	 */
	public static function bundled_products_admin_contents( $product_bundle_object ) {

		$post_id = $product_bundle_object->get_id();

		/*
		 * Bundled products options.
		 */

		$bundled_items = $product_bundle_object->get_bundled_items( 'edit' );
		$tabs          = self::get_bundled_product_tabs();
		$toggle        = 'closed';

		?><div class="hr-section hr-section-components"><?php echo __( 'Bundled Products', 'woocommerce-composite-products' ); ?></div>
		<div class="wc-metaboxes-wrapper wc-bundle-metaboxes-wrapper <?php echo empty( $bundled_items ) ? 'wc-bundle-metaboxes-wrapper--boarding' : ''; ?>">

			<div id="wc-bundle-metaboxes-wrapper-inner">

				<p class="toolbar">
					<a href="#" class="close_all"><?php _e( 'Close all', 'woocommerce' ); ?></a>
					<a href="#" class="expand_all"><?php _e( 'Expand all', 'woocommerce' ); ?></a>
				</p>

				<div class="wc-bundled-items wc-metaboxes"><?php

					if ( ! empty( $bundled_items ) ) {

						$loop = 0;

						foreach ( $bundled_items as $item_id => $item ) {

							$item_availability = '';

							/**
							 * 'woocommerce_add_bundled_product_item_data' filter.
							 *
							 * Use this filter to modify the bundled item data when the product is being edited.
							 *
							 * @param  $item_data   array
							 * @param  $context     string
							 * @param  $product_id  int
							 */
							$item_data = apply_filters( 'woocommerce_add_bundled_product_item_data', $item->get_data(), 'edit', $item->get_product_id() );

							// Pass the bundled item downstream.
							$item_data[ 'bundled_item' ] = $item;

							$product            = $item->get_product();
							$stock_status       = $item->get_stock_status();
							$stock_status_label = '';

							if ( 'out_of_stock' === $stock_status ) {

								$stock_status_label = __( 'Out of stock', 'woocommerce' );

								if ( $item->get_product()->is_in_stock() ) {
									$stock_status       = 'insufficient_stock';
									$stock_status_label = __( 'Insufficient stock', 'woocommerce-product-bundles' );
								}

							} elseif ( 'in_stock' === $stock_status ) {

								if ( '' !== $item->get_max_stock() && $item->get_max_stock() <= wc_get_low_stock_amount( $item->get_product() ) ) {
									$stock_status       = 'low_stock';
									$stock_status_label = __( 'Low stock', 'woocommerce-product-bundles' );
								}

							} elseif ( 'on_backorder' === $stock_status ) {
								$stock_status_label = __( 'On backorder', 'woocommerce' );
							}

							include( WC_PB_ABSPATH . 'includes/admin/meta-boxes/views/html-bundled-product.php' );

							$loop++;
						}

					} else {

						?><div class="wc-bundled-items__boarding">
							<div class="wc-bundled-items__boarding__message">
								<h3><?php _e( 'Bundled Products', 'woocommerce-product-bundles' ); ?></h3>
								<p><?php _e( 'You have not added any products to this bundle.', 'woocommerce-product-bundles' ); ?>
								<br/><?php _e( 'Add some now?', 'woocommerce-product-bundles' ); ?>
								</p>
							</div>
						</div><?php
					}

				?></div>
			</div>
			<div class="add_bundled_product form-field">
				<?php
				/**
				 * 'woocommerce_bundled_item_legacy_add_input' filter.
				 *
				 * Filter to include the legacy select2 input instead of the new expanding button.
				 *
				 */
				if ( apply_filters( 'woocommerce_bundled_item_legacy_add_input', false ) ) { ?>

					<select class="sw-select2-search--products" id="bundled_product" style="width: 250px;" name="bundled_product" data-placeholder="<?php _e( 'Add a bundled product&hellip;', 'woocommerce-product-bundles' ); ?>" data-action="woocommerce_json_search_products" multiple="multiple" data-limit="500">
						<option></option>
					</select>

				<?php } else { ?>

					<div class="sw-expanding-button sw-expanding-button--large">
						<span class="sw-title"><?php echo _x( 'Add Product', 'new bundled product button', 'woocommerce-product-bundles' ); ?></span>
						<select class="sw-select2-search--products" id="bundled_product" name="bundled_product" data-placeholder="<?php _e( 'Search for a product&hellip;', 'woocommerce-product-bundles' ); ?>" data-action="woocommerce_json_search_products" multiple="multiple" data-limit="500">
							<option></option>
						</select>
					</div>

				<?php } ?>

			</div>
		</div><?php
	}

	/**
	 * Handles getting bundled product meta box tabs - @see bundled_product_admin_html.
	 *
	 * @return array
	 */
	public static function get_bundled_product_tabs() {

		/**
		 * 'woocommerce_bundled_product_admin_html_tabs' filter.
		 * Use this to add bundled product admin settings tabs
		 *
		 * @param  array  $tab_data
		 */
		return apply_filters( 'woocommerce_bundled_product_admin_html_tabs', array(
			array(
				'id'    => 'config',
				'title' => __( 'Basic Settings', 'woocommerce-product-bundles' ),
			),
			array(
				'id'    => 'advanced',
				'title' => __( 'Advanced Settings', 'woocommerce-product-bundles' ),
			)
		) );
	}

	/**
	 * Add admin notices.
	 *
	 * @param  string  $content
	 * @param  mixed   $args
	 */
	public static function add_admin_notice( $content, $args ) {
		if ( is_array( $args ) && ! empty( $args[ 'dismiss_class' ] ) ) {
			$args[ 'save_notice' ] = true;
			WC_PB_Admin_Notices::add_dismissible_notice( $content, $args );
		} else {
			WC_PB_Admin_Notices::add_notice( $content, $args, true );
		}
	}

	/**
	 * Add admin errors.
	 *
	 * @param  string  $error
	 *
	 * @return string
	 */
	public static function add_admin_error( $error ) {
		self::add_admin_notice( $error, 'error' );
	}

	/*
	|--------------------------------------------------------------------------
	| Deprecated methods.
	|--------------------------------------------------------------------------
	*/

	public static function bundled_products_admin_config( $product_bundle_object ) {
		_deprecated_function( __METHOD__ . '()', '6.4.0' );
	}
	public static function form_location_option( $product_bundle_object ) {
		_deprecated_function( __METHOD__ . '()', '5.8.0', __CLASS__ . '::bundled_products_admin_config_form_location()' );
		global $product_bundle_object;
		return self::bundled_products_admin_config_form_location( $product_bundle_object );
	}
	public static function build_bundle_config( $post_id, $posted_bundle_data ) {
		_deprecated_function( __METHOD__ . '()', '4.11.7', __CLASS__ . '::process_posted_bundle_data()' );
		return self::process_posted_bundle_data( $posted_bundle_data, $post_id );
	}
}

WC_PB_Meta_Box_Product_Data::init();
