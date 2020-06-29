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
	public static $version = '2.4.6';

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
		add_action( 'woocommerce_coupon_options_save', array( __CLASS__, 'process_shop_coupon_meta' ), 20, 2 );

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
				'free_gifts' => array_values( WC_Free_Gift_Coupons::get_gift_data( intval( $coupon_id ), true ) )
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
	 * Save the new coupon metabox field data
	 *
	 * @param integer $post_id
	 * @param object WC_Coupon $coupon
	 * @return void
	 * @since 1.0
	 */
	public static function process_shop_coupon_meta( $post_id, $coupon ) {

		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce check handled by WooCommerce core.
		if ( isset( $_POST['discount_type'] ) && in_array( $_POST['discount_type'], WC_Free_Gift_Coupons::get_gift_coupon_types(), true ) ) { 

			// Sanitize gift products.
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Missing -- Nonce check handled by WooCommerce core.
			$gift_data = isset( $_POST['wc_free_gift_coupons_data'] ) ? self::sanitize_free_gift_meta( $_POST['wc_free_gift_coupons_data'] ) : array(); 

			// Sanitize free shipping option.
			// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce check handled by WooCommerce core.
			$free_gift_shipping = isset( $_POST['wc_free_gift_coupon_free_shipping'] ) ? 'yes' : 'no';

			// Save.
			$coupon->update_meta_data( '_wc_free_gift_coupon_data', $gift_data );
			$coupon->update_meta_data( '_wc_free_gift_coupon_free_shipping', $free_gift_shipping );
			$coupon->save_meta_data();
		}

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
