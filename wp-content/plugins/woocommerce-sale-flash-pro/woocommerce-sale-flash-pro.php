<?php
/**
 * Plugin Name: WooCommerce Sale Flash Pro
 * Plugin URI: https://woocommerce.com/products/sale-flash-pro/
 * Description: Gives you global and per-product control over sale flash display and lets you show % or the amount off.
 * Version: 1.2.19
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * Text Domain: wc_sale_flash
 * Domain Path: /languages
 * WC tested up to: 4.5
 * WC requires at least: 2.6
 *
 * Requires at least: 3.1
 * Tested up to: 5.5
 *
 * Copyright: © 2020 WooCommerce
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Woo: 18591:7761d62beb597bea4f6fab56fef6739c
 */

/**
 * WooCommerce fallback notice.
 *
 * @since 1.2.16
 * @return void
 */
function woocommerce_sale_flash_pro_missing_wc_notice() {
	/* translators: %s WC download URL link. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Sale Flash Pro requires WooCommerce to be installed and active. You can download %s here.', 'wc_sale_flash' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

if ( ! class_exists( 'WC_Sale_Flash_Pro' ) ) :
	class WC_Sale_Flash_Pro {
		var $discount_display_type;
		var $settings;

		public function __construct() {
			$this->discount_display_type = get_option( 'woocommerce_sale_flash_type' );

			// Init settings
			$this->settings = array(
				array(
					'name'          => __( 'Sale flash type', 'wc_sale_flash' ),
					'desc'          => __( '<strong>25% off!</strong> vs <strong>&pound;20 off!</strong>. Can be overridden by editing products.', 'wc_sale_flash' ),
					'id'            => 'woocommerce_sale_flash_type',
					'css'           => 'min-width:175px;',
					'type'          => 'select',
					'options' => array(
						''  => __( 'Default', 'wc_sale_flash' ),
						'percent'  => __( 'Percent', 'wc_sale_flash' ),
						'amount' => __( 'Amount', 'wc_sale_flash' ),
					),
				),
				array(
					'name'		=> __( 'Sale flash price', 'wc_sale_flash' ),
					'desc' 		=> __( 'Show original price?', 'wc_sale_flash' ),
					'id' 		=> 'woocommerce_sale_flash_original_price',
					'type' 		=> 'checkbox',
					'std' 		=> 'no',
				),
				array(
					'name'		=> __( 'Enable for variations', 'wc_sale_flash' ),
					'desc' 		=> __( 'Show sale flashes for variable products e.g. Up to 40% off!', 'wc_sale_flash' ),
					'id' 		=> 'woocommerce_sale_flash_variations',
					'type' 		=> 'checkbox',
					'std' 		=> 'no',
				),
			);

			// Default options
			add_option( 'woocommerce_sale_flash_type', 'percent' );
			add_option( 'woocommerce_sale_flash_variations', 'no' );

			// Admin
			add_action( 'woocommerce_settings_pricing_options_end', array( $this, 'admin_settings' ) );
			add_action( 'woocommerce_update_options_catalog', array( $this, 'save_admin_settings' ) );

			/* 2.1 */
			add_action( 'woocommerce_update_options_general', array( $this, 'save_admin_settings' ) );

			// Filters
			if ( ! is_admin() ) {
				add_filter( 'woocommerce_sale_flash', array( $this, 'sale_flash' ), 1, 3 );
				add_filter( 'woocommerce_sale_price_html', array( $this, 'sale_price_html' ), 1, 2 );
			}

			add_action( 'woocommerce_product_options_pricing', array( $this, 'write_panel' ) );
			add_action( 'woocommerce_process_product_meta', array( $this, 'write_panel_save' ) );
		}

		/*-----------------------------------------------------------------------------------*/
		/* Class Functions */
		/*-----------------------------------------------------------------------------------*/

		public function sale_flash( $html, $post, $product ) {
			if ( 'yes' === get_post_meta( $post->ID, 'woocommerce_sale_flash_hide', true ) ) {
				return $html;
			}

			if ( $product->has_child() ) :

				if ( 'no' === get_option( 'woocommerce_sale_flash_variations' ) ) {
					return $html;
				}

				$sale_flash_type = get_post_meta( $product->get_id(), 'woocommerce_sale_flash_type', true );

				if ( ! $sale_flash_type ) {
					$sale_flash_type = $this->discount_display_type;
				}

				$discounts = array();

				foreach ( $product->get_children() as $child ) :

					$child_product = wc_get_product( $child );

					if ( ! $child_product->get_sale_price() ) {
						continue;
					}

					if ( 'percent' === $sale_flash_type ) :

						$discount = round( ( ( $child_product->get_regular_price() - $child_product->get_sale_price() ) * 100 ) / $child_product->get_regular_price() );

					else :

						$discount = $child_product->get_regular_price() - $child_product->get_sale_price();

					endif;

					$discounts[] = $discount;

				endforeach;

				if ( 0 == sizeof( $discounts ) ) {
					return $html;
				}

				// Get Max discount :)
				$discount = max( $discounts );

				if ( 'percent' === $sale_flash_type ) :

					/* translators: 1: discount price */
					$html = '<span class="onsale">' . sprintf( __( 'up to %s%% off!', 'wc_sale_flash' ), $discount ) . '</span>';

				elseif ( 'amount' === $sale_flash_type ) :

					if ( $discount > 0 ) :
						/* translators: 1: discount price */
						$html = '<span class="onsale">' . sprintf( __( 'up to %s off!', 'wc_sale_flash' ), wc_price( $discount ) ) . '</span>';
					endif;

				endif;

			elseif ( $product->get_regular_price() && $product->get_sale_price() ) :

				$sale_flash_type = get_post_meta( $product->get_id(), 'woocommerce_sale_flash_type', true );

				if ( ! $sale_flash_type ) {
					$sale_flash_type = $this->discount_display_type;
				}

				if ( 'percent' === $sale_flash_type ) :

					$discount = round( ( ( $product->get_regular_price() - $product->get_sale_price() ) * 100 ) / $product->get_regular_price() );

					/* translators: 1: discount price */
					$html = '<span class="onsale">' . sprintf( __( '%s%% off!', 'wc_sale_flash' ), $discount ) . '</span>';

				elseif ( 'amount' === $sale_flash_type ) :

					$discount = $product->get_regular_price() - $product->get_sale_price();

					if ( $discount > 0 ) :
						/* translators: 1: discount price */
						$html = '<span class="onsale">' . sprintf( __( '%s off!', 'wc_sale_flash' ), wc_price( $discount ) ) . '</span>';
					endif;

				endif;

			endif;

			return $html;
		}

		public function sale_price_html( $html, $product ) {
			if ( 'yes' === get_option( 'woocommerce_sale_flash_original_price' ) ) {
				return $html;
			}

			if ( is_single() ) {
				return $html;
			}

			$html = wc_price( $product->get_price() );

			return $html;
		}

		public function admin_settings() {
			woocommerce_admin_fields( $this->settings );
		}

		public function save_admin_settings() {
			woocommerce_update_options( $this->settings );
		}

	    public function write_panel() {
	    	woocommerce_wp_select( array(
	    		'id' => 'woocommerce_sale_flash_type',
	    		'label' => __( 'Sale flash type', 'wc_sale_flash' ),
	    		'description' => __( 'Choose a sale flash style for this product.', 'wc_sale_flash' ),
	    		'options' => array(
		    		''  => __( 'Default', 'wc_sale_flash' ),
					'percent'  => __( 'Percent', 'wc_sale_flash' ),
					'amount' => __( 'Amount', 'wc_sale_flash' ),
	    		),
	    	) );

	    	woocommerce_wp_checkbox( array(
	    		'id' => 'woocommerce_sale_flash_hide',
	    		'label' => __( 'Hide sale flash', 'wc_sale_flash' ),
	    		'description' => __( 'Hide the sale flash for this product.', 'wc_sale_flash' ),
	    	) );
	    }

	    public function write_panel_save( $post_id ) {
	    	$woocommerce_sale_flash_type = $_POST['woocommerce_sale_flash_type'];
	    	update_post_meta( $post_id, 'woocommerce_sale_flash_type', $woocommerce_sale_flash_type );

	    	$hide_sale_flash = isset( $_POST['woocommerce_sale_flash_hide'] ) ? 'yes' : 'no';
	    	update_post_meta( $post_id, 'woocommerce_sale_flash_hide', $hide_sale_flash );
	    }
	}
endif;

add_action( 'plugins_loaded', 'woocommerce_sale_flash_pro_init' );

/**
 * Initalizes the extension.
 *
 * @since 1.2.16
 * @return void
 */
function woocommerce_sale_flash_pro_init() {
	load_plugin_textdomain( 'wc_sale_flash', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woocommerce_sale_flash_pro_missing_wc_notice' );
		return;
	}

	$wc_sale_flash_pro = new WC_Sale_Flash_Pro();
}
