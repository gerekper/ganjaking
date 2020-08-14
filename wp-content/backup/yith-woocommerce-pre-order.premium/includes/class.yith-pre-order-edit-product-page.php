<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCPO_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Pre_Order_Edit_Product_Page
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Mora <carlos.eugenio@yourinspiration.it>
 *
 */

if ( ! class_exists( 'YITH_Pre_Order_Edit_Product_Page' ) ) {
	/**
	 * Class YITH_Pre_Order_Edit_Product_Page
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 */
	class YITH_Pre_Order_Edit_Product_Page {

		/**
		 * Construct
		 *
		 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
		 * @since  1.0.0
		 */
		public function __construct() {
			add_filter( 'product_type_options', array( $this, 'pre_order_checkbox' ), 5 );
			add_action( 'woocommerce_process_product_meta', array( $this, 'update_settings' ) );
			add_action( 'woocommerce_variation_options', array( $this, 'add_pre_order_variable_checkbox' ), 10, 3 );
			add_action( 'woocommerce_save_product_variation', array( $this, 'save_variable_fields' ), 10, 2 );
			add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hide_preorder_itemmeta') );
		}


		/**
		 * Pre Order Checkbox
		 *
		 * Sets a product with pre order status
		 *
		 * @return   array product_type_options
		 * @since    1.0.0
		 * @author   Carlos Mora <carlos.eugenio@yourinspiration.it>
		 */
		public function pre_order_checkbox( $product_type_options ) {
			$product_type_options[ 'ywpo_preorder' ] =  array(
			        'id'            => '_ywpo_preorder',
                    'wrapper_class' => 'show_if_simple hide_if_bundle',
                    'label'         => esc_html_x( 'Pre-Order', 'Set the product as a Pre-Order product.','yith-pre-order-for-woocommerce' ),
                    'description'   => esc_html__( 'Set the Pre-Order status for this product.', 'yith-pre-order-for-woocommerce' ),
                    'default'       => 'no'
            );

			return $product_type_options;
		}


		public function update_settings( $post_id ) {

			$pre_order    = new YITH_Pre_Order_Product( $post_id );
			$is_pre_order = isset( $_POST['_ywpo_preorder'] ) && ! is_array( $_POST['_ywpo_preorder'] ) ? 'yes' : 'no';

			$pre_order->set_pre_order_status( $is_pre_order );

		}


		public function add_pre_order_variable_checkbox( $loop, $variation_data, $variation ) {
			$pre_order    = new YITH_Pre_Order_Product( $variation->ID );
			$is_preorder = $pre_order->get_pre_order_status();
			?>
			<label>
				<input type="checkbox" class="checkbox variable_is_preorder"
					   name="_ywpo_preorder[<?php echo $loop; ?>]"
					<?php checked( $is_preorder, esc_attr( 'yes' ) ); ?> />
				<?php _ex( 'Pre-Order', 'Set the variation to Pre-Order status.','yith-pre-order-for-woocommerce' ); ?>
				<?php echo wc_help_tip( esc_html__( 'Enable this option to set this variation to the Pre-Order status.', 'yith-pre-order-for-woocommerce' ) ); ?>
			</label>
			<?php
		}


		public function save_variable_fields( $post_id, $_i ) {
			$pre_order = new YITH_Pre_Order_Product( $post_id );

			$is_pre_order = isset( $_POST['_ywpo_preorder'][ $_i ] ) ? 'yes' : 'no';
			$pre_order->set_pre_order_status( $is_pre_order );

		}

		function hide_preorder_itemmeta( $array ) {
			$array[] = '_ywpo_item_preorder';
			$array[] = '_ywpo_item_for_sale_date';
			return $array;
		}
		

	}

}