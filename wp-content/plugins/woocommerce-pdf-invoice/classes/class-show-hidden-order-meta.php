<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( !class_exists( 'PDF_Hidden_Order_Meta' ) ) {

	Class PDF_Hidden_Order_Meta {

		public function __construct() {

			$settings = get_option( 'woocommerce_pdf_invoice_settings' );

		  	if( isset( $settings["pdf_debug"] ) && $settings["pdf_debug"] == "true" ) {

				add_action( 'add_meta_boxes', array( $this,'hidden_meta' ), 10, 2 );

			}

		}

	    /**
	     * [hidden_meta description]
	     * @param  [type] $post_type [description]
	     * @param  [type] $post      [description]
	     * @return [type]            [description]
	     */
		function hidden_meta( $post_type,$post ) {
			if( isset( $post_type ) && $post_type === 'shop_order' ) {
				add_meta_box( 'woocommerce-hidden-order-meta', __('Order Meta', 'woocommerce-pdf-invoice'), array( $this,'woocommerce_hidden_order_meta_box' ), 'shop_order', 'advanced', 'low');
			}
		}
		
		/**
		 * [woocommerce_invoice_meta_box description]
		 * @param  [type] $post [description]
		 * @return [type]       [description]
		 */
		function woocommerce_hidden_order_meta_box( $post ) {

			$custom_fields = get_post_meta( $post->ID );
	?>
			<table>
				<thead>
					<tr>
						<th class="key-column"><?php _e( 'Key', 'woocommerce-pdf-invoice' ); ?></th>
						<th class="value-column"><?php _e( 'Value', 'woocommerce-pdf-invoice' ); ?></th>
					</tr>
				</thead>
				<tbody>
	<?php
			foreach( $custom_fields as $key => $values ) {
	?>
					<tr><th style="text-align:left"><?php echo $key; ?></th><td>
	<?php 
						foreach( $values as $value ) {
							var_export( $value );
						}
	?>
					</td></tr>
	<?php			
			}
	?>
				</tbody>
			</table>
	<?php
		}

	}

	// $GLOBALS['PDF_Hidden_Order_Meta'] = new PDF_Hidden_Order_Meta();

}