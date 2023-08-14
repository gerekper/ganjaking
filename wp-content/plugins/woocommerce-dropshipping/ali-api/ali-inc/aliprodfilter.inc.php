<?php

class Ali_Product_Filter {

	function __construct() {
		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'ali_product_panel' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'ali_product_panel_save' ) );
	}

	public function ali_product_panel() {
		echo '<div class="options_group">';
		woocommerce_wp_checkbox(
			array(
				'id' => '_is_ali_product',
				'label' => __( 'Aliexpress Product', 'woocommerce-dropshipping' ),
				'description' => __(
					'Enable this option if this product is from Aliexpress',
					'woocommerce-dropshipping'
				),
			)
		);
		echo '</div>';
	}

	public function ali_product_panel_save( $post_id ) {
		if ( isset( $_POST['_is_ali_product'] ) ) {
			update_post_meta( $post_id, '_is_ali_product', 'yes' );
		} else {
			update_post_meta( $post_id, '_is_ali_product', 'no' );
		}
	}

}
