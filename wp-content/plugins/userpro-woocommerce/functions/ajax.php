<?php

class UPWAjax{

	function __construct(){
		
		add_action( 'wp_ajax_upw_get_order_details', array( $this, 'get_order_details' ) );
		add_action( 'wp_ajax_upw_get_more_products', array( $this, 'get_more_products' ) );
	}

	function get_order_details(){
			global $userpro;
			$order_id = $_POST['order_id'];	
			$customer_order = get_post( $_POST['order_id'] );
			$order = wc_get_order( $_POST['order_id'] );
			$order->populate( $customer_order );
			$user_id = get_current_user_id();
			ob_start();
			include_once UPWPATH.'templates/upw-order-detail.php';
			$output = ob_get_contents();
			ob_end_clean();
			$output = json_encode( array('html'=>$output) );
			echo $output;
			die();
	
	}

	function get_more_products(){
			if( empty( $upw_default_options ) ){
				$upw_default_options = new UPWDefaultOptions();
			}
			$upw_total_products = $upw_default_options->userpro_woocommerce_get_option( 'upw_total_products_show' );
			$post_per_page = $upw_default_options->userpro_woocommerce_get_option( 'upw_products_per_page' );
			$paged = esc_attr($_POST['paged']);
			if($_POST['shown_products']>=$upw_total_products){
				$output = 0;
				$no_more_text = __("No more products to load");
				
			}
			else{
			$no_more_text = __('Load more products');
			$params = array( 'post_type' => 'product', 'posts_per_page' => $post_per_page, 'paged' => $paged );
			$loop = new WP_Query( $params );
			if ( $loop->found_posts ) {
				$user = get_userdata( get_current_user_id() );
				ob_start();
				require_once UPWPATH . 'templates/upw-purchases.php';
				$output = ob_get_contents();
				ob_end_clean();
			}
			else{
				$output = 0;
				$no_more_text = __("No more products to load");
			}
			}
			$output = json_encode( array('html'=>$output, 'no_more_text'=>$no_more_text) );
			echo $output;
			die();
			
	}
}

new UPWAjax();
