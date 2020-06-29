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

if ( ! class_exists( 'YITH_WooCommerce_Product_Shipping_Method' ) ) {

	/**
	 * YITH WooCommerce Product Shipping Method
	 *
	 * @since 1.0.0
	 */
	class YITH_WooCommerce_Product_Shipping_Method extends WC_Shipping_Method {

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			global $yith_wc_product_shipping_admin;

			$this->id					= 'yith_wc_product_shipping_method';
			$this->method_title			= __( 'YITH Product Shipping', 'yith-product-shipping-for-woocommerce' );
			$this->method_description	= __( 'Description of your custom shipping method.', 'yith-product-shipping-for-woocommerce' );

			$this->init_form_fields();
			$this->init_settings();

			$this->enabled		= isset( $this->settings['enabled'] )		? $this->settings['enabled']		: '';
			$this->taxonomies	= isset( $this->settings['taxonomies'] )	? $this->settings['taxonomies']		: '';
			$this->title		= isset( $this->settings['title'] )			? $this->settings['title']			: 'Shipping';
			$this->availability	= isset( $this->settings['availability'] )	? $this->settings['availability']	: '';
			$this->countries	= isset( $this->settings['countries'] )		? $this->settings['countries']		: '';
			$this->tax_status	= isset( $this->settings['tax_status'] )	? $this->settings['tax_status']		: '';

			add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $yith_wc_product_shipping_admin, 'update_shippings' ) );
			add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );

		}

		/**
		 * Admin Options
		 *
		 * @since 1.0.0
		 */
		function admin_options() {
			global $wpdb, $yith_wc_product_shipping_admin; ?>

			<h2><?php echo $this->method_title; ?></h2>
			<p><?php echo $this->method_description; ?></p>
			<table class="form-table" style="margin-bottom: 50px;">
				<?php $this->generate_settings_html(); ?>
			</table>
			
			<h2><?php echo __( 'Global Shipping Costs', 'yith-product-shipping-for-woocommerce' ); ?></h2>
			<?php $yith_wc_product_shipping_admin->shipping_table(); ?>

			<?php if ( false ) : // Used for debug ?>
				<h2>
					<?php echo __( 'Single Products Summary', 'yith-product-shipping-for-woocommerce' ); ?>
					<small>(<?php echo __( 'not editable', 'yith-product-shipping-for-woocommerce' ); ?>)</small>
				</h2>
				<?php $yith_wc_product_shipping_admin->shipping_table('not-editable'); ?>
			<?php endif; ?>

			<?php
		}

		/**
		 * Plugin Settings
		 *
		 * @since 1.0.0
		 */
		public function init_form_fields() {

			$this->form_fields = apply_filters( 'yith_wcps_admin_settings', array(
				'enabled' => array(
					'title'         => __( 'Product Shipping', 'yith-product-shipping-for-woocommerce' ),
					'type'          => 'checkbox',
					'label'         => __( 'Enable', 'yith-product-shipping-for-woocommerce' ),
					'default'       => 'yes'
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
			));

		}

		/**
		 * Calculate Shipping
		 *
		 * @since 1.0.0
		 */
		public function calculate_shipping( $package = array() ) {

			$_tax   			= new WC_Tax();
			$taxes  			= array();
			$info_cart			= array('0' => 'ok');
			$_product_id		= 0;
			$shipping_cost  	= 0;
			$cart_unique_cost	= 0;
			$single_unique_cost = true;

			$cart_weight		= 0;
			$per_order_costs	= array();

			if ( sizeof( $package['contents'] ) > 0 ) {
				foreach ( $package['contents'] as $item_id => $values ) {
					if ( $values['quantity'] > 0 ) {
						if ( $values['data']->needs_shipping() ) {
							$values['weight'] = isset( $values['weight'] ) ? $values['weight'] : 0;
							// Check if variation
							if ( $values['variation_id'] ) {
								$_product = wc_get_product( $values['variation_id'] );
								$weight = ( $_product->get_weight() > 0 ? $_product->get_weight() : 0 ) * ( $values['quantity'] > 0 ? $values['quantity'] : 0 );
								$cart_weight += $weight;
							} else {
								$_product = wc_get_product( $values['product_id'] );
								$weight = ( $_product->get_weight() > 0 ? $_product->get_weight() : 0 ) * ( $values['quantity'] > 0 ? $values['quantity'] : 0 );
								$cart_weight += $weight;
							}
						}
					}
				}
			}

			if ( sizeof( $package['contents'] ) > 0 ) {
				$min_unique_cost = 0;
				$max_unique_cost = 0;
				$cost_per_vendor = array( 0 => 0 );
				$peoc_per_vendor = array();
				$mauc_per_vendor = array();
				$miuc_per_vendor = array();
				foreach ( $package['contents'] as $item_id => $values ) {
					if ( $values['quantity'] > 0 ) {
						$is_not_sold_individually = isset( $values['yith_wapo_sold_individually'] ) && $values['yith_wapo_sold_individually'] ? false : true;
						if ( $values['data']->needs_shipping() && $is_not_sold_individually ) {
							$shipping_row = false;
							$item_shipping_cost = 0;

							/**
							 * Vendor
							 */
							$vendor_id = 0;
							if ( function_exists( 'yith_get_vendor' ) ) {
								$vendor = yith_get_vendor( $_product, 'product' );
								if ( $vendor->id > 0 ) {
									$vendor_id = $vendor->id;
								}
							}

							/**
							 * Weight check
							 */
							$values['weight'] = isset( $values['weight'] ) ? $values['weight'] : 0;

							/**
							 * Get Shipping Row
							 */
							if ( $values['variation_id'] ) {
								$_product_id = $values['variation_id'];
								$_product = wc_get_product( $_product_id );
								$weight = ( $_product->get_weight() > 0 ? $_product->get_weight() : 0 ) * ( $values['quantity'] > 0 ? $values['quantity'] : 0 );
								$shipping_row = yith_wc_product_shipping_row( $values['variation_id'], $package, $values['quantity'], $weight, $cart_weight );
							}
							if ( $shipping_row === false ) {
								$_product_id = $values['product_id'];
								$_product = wc_get_product( $_product_id );
								$weight = ( $_product->get_weight() > 0 ? $_product->get_weight() : 0 ) * ( $values['quantity'] > 0 ? $values['quantity'] : 0 );
								$shipping_row = yith_wc_product_shipping_row( $values['product_id'], $package, $values['quantity'], $weight, $cart_weight );
							}

							/**
							 * Cost Calculation
							 */
							if ( $shipping_row ) {

								$shipping_row->shipping_cost = $shipping_row->shipping_cost > 0 ? $shipping_row->shipping_cost : 0;
								$shipping_row->product_cost = $shipping_row->product_cost > 0 ? $shipping_row->product_cost : 0;
								$shipping_row->unique_cost = $shipping_row->unique_cost > 0 ? $shipping_row->unique_cost : 0;
								$values['quantity'] = $values['quantity'] > 0 ? $values['quantity'] : 0;

								$item_shipping_cost += $shipping_row->shipping_cost + ( $shipping_row->product_cost * $values['quantity'] );
								$per_order_costs[ $shipping_row->ord ] = $shipping_row->unique_cost;

								$info_cart[ $_product_id ]['shipping_cost'] = $shipping_row->shipping_cost;
								$info_cart[ $_product_id ]['product_cost'] = $shipping_row->product_cost;
								$info_cart[ $_product_id ]['unique_cost'] = $shipping_row->unique_cost;

								$max_unique_cost = $shipping_row->unique_cost > $max_unique_cost ? $shipping_row->unique_cost : $max_unique_cost;
								$min_unique_cost = ( $min_unique_cost == 0 || $shipping_row->unique_cost < $min_unique_cost ) ? $shipping_row->unique_cost : $min_unique_cost;

								$peoc_per_vendor[ $vendor_id ] = $per_order_costs;
								$mauc_per_vendor[ $vendor_id ] = $max_unique_cost;
								$miuc_per_vendor[ $vendor_id ] = $min_unique_cost;

							} else { return; }

							$cost_per_vendor[ $vendor_id ] += $item_shipping_cost;
							$shipping_cost += $item_shipping_cost;

						}

					}

				}

			}

			/*
			 *	Add "Per Order" cost based on max price
			 */
			if ( apply_filters( 'yith_wcps_enable_max_unique_cost', false ) ) {
				$shipping_cost += $max_unique_cost;
			/*
			 *	Add "Per Order" cost based on min price
			 */
			} else if ( apply_filters( 'yith_wcps_enable_min_unique_cost', false ) ) {
				$shipping_cost += $min_unique_cost;
			/*
			 *	Add "Per Order" cost based on priority
			 */
			} else if ( count( $per_order_costs ) > 0 ) {
				foreach ( $peoc_per_vendor as $key => $value ) {
					$costs = $peoc_per_vendor[ $key ];
					$shipping_cost += $costs[ min( array_keys( $costs ) ) ];
				}
				// $shipping_cost += $per_order_costs[ min( array_keys( $per_order_costs ) ) ];
			}

			/**
			 * Taxes
			 */
			if ( get_option( 'woocommerce_calc_taxes' ) == 'yes' && $this->tax_status == 'taxable' ) {
				
				$rates      = ! empty( $values['data'] ) ? $_tax->get_shipping_tax_rates( $values['data']->get_tax_class() ) : 0;
				$item_taxes = $_tax->calc_shipping_tax( $shipping_cost, $rates );

				foreach ( array_keys( $taxes + $item_taxes ) as $key ) {
					$item_tax 	= isset( $item_taxes[ $key ] ) ? $item_taxes[ $key ] : 0;
					$tax 		= isset( $taxes[ $key ] ) ? $taxes[ $key ] : 0;
					$taxes[ $key ] = round( $item_tax + $tax, 2, PHP_ROUND_HALF_UP );

					if ( get_option( 'woocommerce_tax_display_cart' ) == 'incl' ) {

						$current_tax = round( $taxes[ $key ], 2, PHP_ROUND_HALF_UP ); // 10
						$rate = $shipping_cost > 0 ? ( ( 100 / $shipping_cost ) * $current_tax ) : 0; // %

						$new_item_shipping_cost = $shipping_cost / ( 1 + ( $rate / 100 ) );
						$new_item_shipping_tax = $shipping_cost - $new_item_shipping_cost;

						$taxes[ $key ] = round( $new_item_shipping_tax, 2, PHP_ROUND_HALF_UP );
						$shipping_cost -= round( $taxes[ $key ], 2, PHP_ROUND_HALF_UP );

					}

				}

			}

			/**
			 * Cart Info
			 */
			$_SESSION['yith_wcps_info_cart'] = $info_cart;

			/**
			 * Add Rate
			 */
			if ( $shipping_cost == 0 ) {
				$this->title = yit_wpml_string_translate( YITH_WCPS_WPML_CONTEXT, 'yith_wcps_free_shipping_title', $this->title );
				$this->title = apply_filters( 'yith_wcps_free_shipping_title', $this->title );
			}
			$this->add_rate( array(
				'id'    => $this->id,
				'label' => $this->title,
				'cost'  => $shipping_cost,
				'taxes' => $taxes,
			) );

		}

	}

}
