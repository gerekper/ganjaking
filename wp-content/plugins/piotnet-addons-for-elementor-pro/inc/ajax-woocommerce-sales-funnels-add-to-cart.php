<?php
	add_action( 'wp_ajax_pafe_ajax_woocommerce_sales_funnels_add_to_cart', 'pafe_ajax_woocommerce_sales_funnels_add_to_cart' );
	add_action( 'wp_ajax_nopriv_pafe_ajax_woocommerce_sales_funnels_add_to_cart', 'pafe_ajax_woocommerce_sales_funnels_add_to_cart' );

	function pafe_ajax_woocommerce_sales_funnels_add_to_cart() {
		if ( !empty($_POST['options']) ) {
			$options = $_POST['options'];
			$product_id = $options['product_id'];
			$quantity = $options['quantity'];
			$variation_id = $options['variation_id'];

			if (empty($variation_id)) {
				$variation_id = 0;
			}

			$status = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, array() );
			$status_return = array();

			if ($status) {
				$status_return = array(
					'status' => 1,
					'message' => $options['message_success'],
				);
			} else {
				$status_return = array(
					'status' => 0,
					'message' => $options['message_out_of_stock'],
				);
			}

			echo json_encode($status_return);
		}

		wp_die();
	}
?>