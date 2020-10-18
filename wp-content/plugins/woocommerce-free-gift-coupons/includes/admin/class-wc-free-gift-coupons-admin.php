<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( class_exists( 'WC_Free_Gift_Coupons_Admin' ) ) {
	WC_Free_Gift_Coupons_Admin::init(); // Run if class exists.
}

/**
 * Main Admin Class
 *
 * @class WC_Free_Gift_Coupons_Admin
 * @version	2.0.0
 */
class WC_Free_Gift_Coupons_Admin {

	/**
	 * The plugin version
	 * 
	 * @var string
	 */
	public static $version = '3.0.0';

	/** 
	 * Coupon Product ids list
	 * 
	 * @var array
	 * @since 3.0.0 
	 */
	protected static $coupon_product_ids = array();

	/**
	 * Initialize
	 *
	 * @return WC_Free_Gift_Coupons_Admin
	 * @since 1.0
	 */
	public static function init() {

		// Admin scripts.
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_scripts' ) );
		
		// Add and save coupon meta.
		add_action( 'woocommerce_coupon_options', array( __CLASS__, 'coupon_options' ), 10, 2 );
		add_action( 'woocommerce_before_data_object_save', array( __CLASS__, 'process_shop_coupon_meta' ), 20, 2 );

		// Show row meta on the plugin screen.
		add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );

	}

	/**
	 * Load admin script
	 *
	 * @return void
	 * @since 1.0
	 */
	public static function admin_scripts() {

		// Coupon styles.
		wp_register_style( 'woocommerce_free_gift_coupon_meta', plugins_url( '../assets/css/free-gift-coupons-meta-box.css' , __DIR__ ), array(), WC_Free_Gift_Coupons::$version );
			
		// Coupon script.
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_register_script( 'woocommerce_free_gift_coupon_meta', plugins_url( '../assets/js/free-gift-coupons-meta-box' . $suffix . '.js' , __DIR__ ), array( 'jquery', 'backbone', 'underscore', 'wp-util', 'jquery-ui-sortable', 'wc-enhanced-select' ), WC_Free_Gift_Coupons::$version, true );

	}

	/**
	 * Load admin script
	 *
	 * @param  int $coupon_id
	 * @return void
	 * @since 1.0
	 */
	public static function load_scripts( $coupon_id = '' ) {

		// Coupon styles.
		wp_enqueue_style( 'woocommerce_free_gift_coupon_meta' );
			
		// Coupon script.
		wp_enqueue_script( 'woocommerce_free_gift_coupon_meta' );

		$translation_array = array(
				'coupon_types' => wp_json_encode( WC_Free_Gift_Coupons::get_gift_coupon_types() ),
				'free_gifts' => array_values( WC_Free_Gift_Coupons::get_gift_data( intval( $coupon_id ) ) )
			);

		wp_localize_script( 'woocommerce_free_gift_coupon_meta', 'woocommerce_free_gift_coupon_meta_i18n', $translation_array );		

		// Backbone template.
		add_action( 'admin_print_footer_scripts', array( __CLASS__, 'print_templates' ) );

	}

	/**
	 * Output the new Coupon metabox fields
	 *
	 * @return HTML
	 * @since 1.0
	 */
	public static function coupon_options( $coupon_id, $coupon ) {
		self::load_scripts( $coupon_id );
		?>

		<p class="form-field show_if_free_gift">

			<label for="free_gift_ids"><?php esc_html_e( 'Free Gifts', 'wc_free_gift_coupons' ) ?></label>

			<span class="description"><?php esc_html_e( 'These are the products you are giving away with this coupon. They will automatically be added to the cart.', 'wc_free_gift_coupons' ); ?></span>

		</p>

		<p class="form-field show_if_free_gift">

			<span class="add_prompt dashicons-before dashicons-plus"></span>

			<select id="free_gift_ids" style="width:90%;" class="wc-product-search" name="free_gift_ids" multiple="multiple" data-sortable="sortable" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'wc_free_gift_coupons' ); ?>" data-action="woocommerce_json_search_products_and_variations">
				<option></option>
			</select>			

		</p>

		<div id="wc-free-gift-container" class="show_if_free_gift">
		<table id="wc-free-gift-table"></table>
			<noscript><?php esc_html_e( 'Javascript is required for product selection to work.', 'wc_free_gift_coupons' );?></noscript>
		</div>

		<!-- Sync Quantities -->
		<p class="form-field show_if_free_gift">

			<label for="_wc_fgc_product_sync_ids"><?php esc_html_e( 'Sync Quantities', 'wc_free_gift_coupons' ) ?></label>

			<span class="description"><?php esc_html_e( 'Sync the gift products\' quantities to the quantity of a product in the cart.', 'wc_free_gift_coupons' ); ?></span>

		</p>

		<p class="form-field show_if_free_gift">

			<select id="_wc_fgc_product_sync_ids" style="width:80%;" class="wc-product-search" name="_wc_fgc_product_sync_ids" data-sortable="sortable" data-allow-clear="tru" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'wc_free_gift_coupons' ); ?>" data-action="woocommerce_json_search_products_and_variations">
				<option></option>
				<?php
				$product_ids = $coupon->get_meta( '_wc_fgc_product_sync_ids', true, 'edit' );

				foreach ( $product_ids as $row => $product_id ) {
					$product = wc_get_product( $product_id );
					if ( is_object( $product ) ) {
						echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . htmlspecialchars( wp_kses_post( $product->get_formatted_name() ) ) . '</option>';
					}
				}
				?>
			</select>			
			<?php
			echo wc_help_tip(
				__( 'This adds the Synced Product to the required Product List, if it is not already added. If changed, the previously synced product remains in your required products list.', 'wc_free_gift_coupons' )
			);
			?>
		</p>

		<?php
		// Free shipping for free gift.
		if ( function_exists( 'wc_shipping_enabled' ) && wc_shipping_enabled() ) {
			woocommerce_wp_checkbox( array(
				'id'          => 'wc_free_gift_coupon_free_shipping',
				'value'		  => $coupon->get_meta( '_wc_free_gift_coupon_free_shipping', true, 'edit' ),
				'label'       => __( 'Free shipping for gift(s)', 'wc_free_gift_coupons' ),
				'description' => __( 'Check this box if the free gift(s) should not incur a shipping cost. A free shipping method must be enabled.', 'wc_free_gift_coupons' ),
				'wrapper_class' => 'show_if_free_gift'
			) );
		}

	}

	/**
	 * Prints the templates used in the coupon options metabox
	 *
	 * @since 2.0.0
	 */
	public static function print_templates() {
		
		/**
		 * Backbone Templates
		 * This file contains all of the HTML used in our application
		 *
		 * Each template is wrapped in a script block ( note the type is set to "text/html" ) and given an ID prefixed with
		 * 'tmpl'. The wp.template method retrieves the contents of the script block and converts these blocks into compiled
		 * templates to be used and reused in your application.
		 */

		/**
		 * The Table Header View
		 */
		?>
		<script type="text/template" id="tmpl-wc-free-gift-products-table-header">	
			<thead>
				<tr>
					<th><?php esc_html_e( 'Product', 'wc_free_gift_coupons' );?></th>
					<th><?php esc_html_e( 'Quantity', 'wc_free_gift_coupons' );?></th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody></tbody>
			
		</script>
		
		<?php
		/**
		 * The Singular List View
		 */
		?>
		<script type="text/template" id="tmpl-wc-free-gift-product">
			
				<td class="product-title">{{{ data.title }}}</td>
				<td class="product-quantity">
					<input type="number" name="wc_free_gift_coupons_data[{{{ data.gift_id }}}][quantity]" value="{{{ data.quantity }}}" />
				</td>
				<td class="product-remove">
					<button class="delete-product dashicons-before dashicons-no" href="#" title="<?php esc_attr_e( 'Click to remove', 'wc_free_gift_coupons' );?>"></button>
				</td>
		
		</script>
		
		<?php
	}

	/**
	 * Save the new coupon metabox field data.
	 * 
	 * This hooking was adjusted in v3.0.0
	 *
	 * @param WC_Data          $coupon The object being saved.
	 * @param WC_Data_Store_WP $data_store The data store persisting the data.
	 * @return void
	 * @since 1.0
	 */
	public static function process_shop_coupon_meta( $coupon, $data_store ) {
		// Run only when it's coupon.
		if ( ! ( $coupon instanceof WC_Coupon ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce check handled by WooCommerce core.
		if ( isset( $_POST['discount_type'] ) && in_array( $_POST['discount_type'], WC_Free_Gift_Coupons::get_gift_coupon_types(), true ) ) { 

			// Sanitize gift products.
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Missing -- Nonce check handled by WooCommerce core.
			$gift_data = isset( $_POST['wc_free_gift_coupons_data'] ) ? self::sanitize_free_gift_meta( $_POST['wc_free_gift_coupons_data'] ) : array();
		
			// If discount type is free gift, free gift is important.
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce check handled by WooCommerce core.
			if ( 'free_gift' === $_POST['discount_type'] ) {

				// Do not allow, a free gift is a must.
				if ( empty( $gift_data ) ) {
					$notice = __( 'Please select at least one product to give away with this coupon.', 'wc_free_gift_coupons' );
					WC_Free_Gift_Coupons_Admin_Notices::add_notice( $notice, 'error', true );
					return;
				}

			}
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Missing -- Nonce check handled by WooCommerce core.
			$synced_products       = isset( $_POST['_wc_fgc_product_sync_ids'] ) ? array_filter( array_map( 'intval', (array) $_POST['_wc_fgc_product_sync_ids'] ) ) : array();
			$clear_old_synced_prod = false;
			$save_coupon           = false; // $coupon->save() gives issue, so leave as false for now, till further notice.

			// Add the synced product to list of required products. Stealth way of making sure the sync doesn't malfunction!
			self::sort_synced_products_for_coupon( $coupon, $synced_products, $clear_old_synced_prod, $save_coupon );

			// Sanitize free shipping option.
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce check handled by WooCommerce core.
			$free_gift_shipping = isset( $_POST['wc_free_gift_coupon_free_shipping'] ) ? 'yes' : 'no';

			// Save.
			$coupon->update_meta_data( '_wc_free_gift_coupon_data', $gift_data );
			$coupon->update_meta_data( '_wc_free_gift_coupon_free_shipping', $free_gift_shipping );
			$coupon->update_meta_data( '_wc_fgc_product_sync_ids', $synced_products );
			$coupon->save_meta_data();
		}

	}

	/**
	 * Sorts/Clears old Synced products from Coupon's product id list.
	 *
	 * Clears the old synced data is $clear_old_synced_products is true,
	 * And adds the new set.
	 *
	 * @param  WC_Coupon  $coupon The coupon object.
	 * @param  array      $new_synced_products New synced product to be added to product_ids list.
	 * @param  bool       $clear_old_synced_products Removes old synced products if true, default is true.
	 * @param  bool       $save If true, runs the save method, default is false. 
	 * @return array The flushed array of product_ids
	 */
	public static function sort_synced_products_for_coupon( $coupon, $new_synced_products, $clear_old_synced_products = true, $save = false ) {
		// First get list of products already added, if any.
		self::$coupon_product_ids = $coupon->get_product_ids();

		// Get old synced product data.
		$old_synced_products = $coupon->get_meta( '_wc_fgc_product_sync_ids' );

		// Should we clear old data? if no, let's save stress.
		if ( true === $clear_old_synced_products ) {
			// Check if old synced products are in product ids data.
			foreach ( $old_synced_products as $old_data ) {
				if ( in_array( $old_data, self::$coupon_product_ids, true ) ) {
					// Remove old synced products from product_ids, incase there's a change.
					unset( self::$coupon_product_ids[ array_search( $old_data, self::$coupon_product_ids, true ) ] );
				}
			}
				
		} else {
			// Show notice only when new synced product is different from old.
			if ( ! empty( $old_synced_products ) && $old_synced_products !== $new_synced_products ) {
				// Show notice
				$notice = __( 'The previously synced product might still be in your required product list, if it has not been removed by you already.', 'wc_free_gift_coupons' );
				WC_Free_Gift_Coupons_Admin_Notices::add_notice(
					$notice,
					array(
						'type'          => 'info',
						'dismiss_class' => 'yes',
					),
					true
				);
			}
		}

		// Set new, and no need to check for duplicates, WC does that by default :).
		$merged = array_merge( self::$coupon_product_ids, $new_synced_products );

		$coupon->set_product_ids( $merged );

		if ( true === $save ) {
			// This gives an issue, which is from WC Core, so till it's fixed :).
			$coupon->save();
		}
		return $coupon->get_product_ids();
	}

	/**
	 * Sanitize separately so we can re-use this method for compatibility reasons.
	 *
	 * @param array $wc_free_gift_coupons_data - The posted data.
	 * @return array
	 * @since 2.1.0
	 */
	public static function sanitize_free_gift_meta( $wc_free_gift_coupons_data ) {

		$gift_data = array();

		if ( is_array( $wc_free_gift_coupons_data ) ) {

			foreach ( $wc_free_gift_coupons_data as $gift_id => $data ) {

				$gift_id = intval( $gift_id );

				$_product = wc_get_product( $gift_id );
				
				if ( $_product ) {
					$gift_data[$gift_id] = 
						array( 
							'product_id' => $_product->get_parent_id() > 0 ? $_product->get_parent_id() : $gift_id,
							'variation_id' => $_product->get_parent_id() > 0 ? $gift_id : 0,
							'quantity' => isset( $data['quantity'] ) ? intval( $data['quantity'] ) : 1
						);
				}
			}
		}

		return $gift_data;
	}

	/**
	 * Show row meta on the plugin screen.
	 *
	 * @param	mixed  $links
	 * @param	mixed  $file
	 * @return	array
	 */
	public static function plugin_row_meta( $links, $file ) {

		if ( $file === WC_FGC_PLUGIN_NAME ) {
			$row_meta = array(
				'docs'    => '<a target="_blank" href="https://docs.woocommerce.com/document/free-gift-coupons/">' . __( 'Documentation', 'wc_free_gift_coupons' ) . '</a>',
				'support' => '<a target="_blank" href="' . esc_url( 'https://woocommerce.com/my-account/marketplace-ticket-form/' ) . '">' . __( 'Support', 'wc_free_gift_coupons' ) . '</a>',
			);

			return array_merge( $links, $row_meta );
		}

		return $links;
	}

} // End class.

WC_Free_Gift_Coupons_Admin::init();
