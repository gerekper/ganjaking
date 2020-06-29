<?php

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_WooCommerce_Product_Shipping_Premium' ) ) {

	/**
	 * YITH WooCommerce Product Shipping Premium
	 *
	 * @since 1.0.0
	 */
	class YITH_WooCommerce_Product_Shipping_Premium extends YITH_WooCommerce_Product_Shipping {

		protected static $_instance = null;

		/**
		 * Construct
		 */
		public function __construct() {
		
			add_action( 'yith_wcps_admin_settings', array( $this, 'admin_settings' ) );
			add_action( 'yith_wcps_admin_table_rows', array( $this, 'admin_table_rows' ) );
			add_action( 'yith_wcps_admin_table_cols', array( $this, 'admin_table_cols' ) );

			/**
			 * Variations Panel
			 */
			if ( isset( WC()->shipping->get_shipping_methods()['yith_wc_product_shipping_method']->settings['variations'] ) &&
					WC()->shipping->get_shipping_methods()['yith_wc_product_shipping_method']->settings['variations'] == 'yes' ) {
				add_action( 'woocommerce_variation_options', array( $this, 'variation_options' ), 10, 3 );
				add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'product_after_variable_attributes' ), 10, 3 );
			}

		}

		/**
		 * Admin Settings
		 */
		function admin_settings() {
			return array(
				'enabled' => array(
					'title'         => __( 'Product Shipping', 'yith-product-shipping-for-woocommerce' ),
					'type'          => 'checkbox',
					'label'         => __( 'Enable', 'yith-product-shipping-for-woocommerce' ),
					'default'       => 'yes'
				),
				'variations' => array(
					'title'         => __( 'Product Variations', 'yith-product-shipping-for-woocommerce' ),
					'type'          => 'checkbox',
					'label'         => __( 'Enable', 'yith-product-shipping-for-woocommerce' ) . ' ' . __( '(this will allow you to enable rules for each product variation, but it will increase the loading time)', 'yith-product-shipping-for-woocommerce' ),
					'default'       => 'no'
				),
				'taxonomies' => array(
					'title'         => __( 'Taxonomies Loading', 'yith-product-shipping-for-woocommerce' ),
					'type'          => 'checkbox',
					'label'         => __( 'Disable', 'yith-product-shipping-for-woocommerce' ) . ' ' . __( '(improves performance)', 'yith-product-shipping-for-woocommerce' ),
					'default'       => 'no'
				),
				'availability' => array(
					'title'         => __( 'Availability', 'yith-product-shipping-for-woocommerce' ),
					'type'          => 'select',
					'default'       => 'all',
					'class'         => 'availability',
					'options'       => array(
						'all'       => __('All allowed countries', 'yith-product-shipping-for-woocommerce'),
						'specific'  => __('Specific Countries', 'yith-product-shipping-for-woocommerce')
					)
				),
				'countries' => array(
					'title'         => __( 'Countries', 'yith-product-shipping-for-woocommerce' ),
					'type'          => 'multiselect',
					'class'         => 'chosen_select',
					'css'           => 'width: 450px;',
					'default'       => '',
					'options'       => empty( WC()->countries ) ? '' : WC()->countries->get_allowed_countries()
				),
				'title' => array(
					'title'         => __( 'Cart Method Name', 'yith-product-shipping-for-woocommerce' ),
					'type'          => 'text',
					'description'   => __( 'Shipping method title on the frontend.', 'yith-product-shipping-for-woocommerce' ),
					'default'       => __( 'Product Shipping', 'yith-product-shipping-for-woocommerce' ),
					'desc_tip'      => true
				),
				'message' => array(
					'title'         => __( 'Message for Global Products', 'yith-product-shipping-for-woocommerce' ),
					'type'          => 'text',
					'description'   => __( 'Inform customers about shipping costs.', 'yith-product-shipping-for-woocommerce' ),
					'default'       => '',
					'desc_tip'      => true
				),
				'message_position' => array(
					'title'         => __( 'Message Position', 'yith-product-shipping-for-woocommerce' ),
					'type'          => 'select',
					'description'   => '',
					'default'       => 'before',
					'options'       => array(
						'before'   	=> __( 'Before "Add to cart" button', 'yith-product-shipping-for-woocommerce' ),
						'after'		=> __( 'After "Add to cart" button', 'yith-product-shipping-for-woocommerce' ),
					),
				),
				'tax_status' => array(
					'title'         => __( 'Taxable', 'yith-product-shipping-for-woocommerce' ),
					'type'          => 'select',
					'default'       => 'taxable',
					'options'       => array(
						'taxable'   => __( 'Yes', 'yith-product-shipping-for-woocommerce' ),
						'none'      => __( 'No', 'yith-product-shipping-for-woocommerce' ),
					),
				),
				/*
				'col_price' => array(
					'title'			=> __( 'Table columns', 'yith-product-shipping-for-woocommerce' ),
					'type'			=> 'checkbox',
					'label'			=> __( 'Price', 'yith-product-shipping-for-woocommerce' ),
					'default'		=> 'yes'
				),
				'col_qty' => array(
					'title'			=> '',
					'type'			=> 'checkbox',
					'label'			=> __( 'Quantity', 'yith-product-shipping-for-woocommerce' ),
					'default'		=> 'yes'
				),
				'col_weight' => array(
					'title'			=> '',
					'type'			=> 'checkbox',
					'label'			=> __( 'Weight', 'yith-product-shipping-for-woocommerce' ),
					'default'		=> 'yes'
				),
				'col_tax' => array(
					'title'			=> '',
					'type'			=> 'checkbox',
					'label'			=> __( 'Taxonomies', 'yith-product-shipping-for-woocommerce' ),
					'default'		=> 'yes'
				),
				'col_geo' => array(
					'title'			=> '',
					'type'			=> 'checkbox',
					'label'			=> __( 'Geolocation', 'yith-product-shipping-for-woocommerce' ),
					'default'		=> 'yes'
				),
				*/
				'table_rows' => array(
					'title'         => __( 'Table rows', 'yith-product-shipping-for-woocommerce' ),
					'type'          => 'select',
					'default'       => '10',
					'options'       => array(
						'10'		=> '10',
						'20'		=> '20',
						'50'		=> '50',
						'100'		=> '100',
					),
				),
				'table_cols' => array(
					'title'			=> 'Table columns',
					'type'			=> 'multiselect',
					'description'   => __( 'Select columns to show in Shipping Costs table.', 'yith-product-shipping-for-woocommerce' ),
					'css'           => 'max-width: 200px; height: 115px;',
					'options'		=> array(
										'role'		=> __( 'Role', 'yith-product-shipping-for-woocommerce' ),
										'price'		=> __( 'Price', 'yith-product-shipping-for-woocommerce' ),
										'qty'		=> __( 'Quantity', 'yith-product-shipping-for-woocommerce' ),
										'weight'	=> __( 'Weight', 'yith-product-shipping-for-woocommerce' ),
										'taxy'		=> __( 'Taxonomies', 'yith-product-shipping-for-woocommerce' ),
										'geo'		=> __( 'Geolocation', 'yith-product-shipping-for-woocommerce' ),
									),
					'default'		=> array( 'price', 'qty', 'weight', 'taxy' ),
				),
			);
		}

		/**
		 * Admin table columns
		 */
		function admin_table_rows() {
			return WC()->shipping->get_shipping_methods()['yith_wc_product_shipping_method']->settings['table_rows'];
		}

		/**
		 * Admin table columns
		 */
		function admin_table_cols() {
			return WC()->shipping->get_shipping_methods()['yith_wc_product_shipping_method']->settings['table_cols'];
		}

		/**
		 * Variations Enable Option
		 *
		 * @since 1.0.0
		 */
		public function variation_options( $loop, $variation_data, $variation ) { ?>
			<label class="tips">
				<?php echo __( 'Custom Shipping Costs', 'yith-product-shipping-for-woocommerce' ); ?>
				<input type="checkbox" class="checkbox enable_yith_product_shipping" name="_yith_product_shipping_variation[<?php echo $variation->ID; ?>]" <?php checked( get_post_meta( $variation->ID, '_yith_product_shipping', true ), "yes" ); ?> />
			</label>
			<?php
		}

		/**
		 * Shipping Table in Variations
		 *
		 * @since 1.0.0
		 */
		public function product_after_variable_attributes( $loop, $variation_data, $variation ) {
			global $yith_wc_product_shipping_admin;
			if ( get_post_meta( $variation->ID, '_yith_product_shipping')[0] == 'yes' ) {
				$yith_wc_product_shipping_admin->shipping_table( $variation->ID );
			}
		}

		/**
		 * Plugin Istance
		 */
		public static function instance() {
			if ( is_null( YITH_WooCommerce_Product_Shipping_Premium::$_instance ) ) {
				YITH_WooCommerce_Product_Shipping_Premium::$_instance = new YITH_WooCommerce_Product_Shipping_Premium();
			}
			return YITH_WooCommerce_Product_Shipping_Premium::$_instance;
		}

	}

}
