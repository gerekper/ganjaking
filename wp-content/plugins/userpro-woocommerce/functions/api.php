<?php
if( !class_exists( 'UPWoocmmerceApi' ) ){

class UPWoocmmerceApi{

	function __construct(){
		
	}
	
	/* gets all orders of a particular user */

	function upw_get_customer_orders( $user_id = null, $order_status = null , $count = false){
		
		$order_status = !isset( $order_status)||empty($order_status)?array_keys( wc_get_order_statuses() ):'wc-'.$order_status;
		$user_id = isset( $user_id )?$user_id:get_current_user_id();
		$params = array(
			'meta_key'    => '_customer_user',
			'meta_value'  => $user_id,
			'post_type'   => wc_get_order_types( 'view-orders' ),
			'post_status' => $order_status
		);
		if( !$count ){
			$params['numberposts'] = 10;
		}
		$customer_orders = get_posts( $params );
		return $customer_orders;
	}
	
	/* gets all product of a user */
	function upw_get_products( $user_id = null ){
		
		global $post, $product, $userpro;
		$purchased_products = array();
		$user_id = isset( $user_id )?$user_id:get_current_user_id();
		$args = array( 'post_type' => 'product', 'posts_per_page' => -1 );
		$loop = new WP_Query( $args );
		if ( $loop->found_posts ) {
			$user = get_userdata( $user_id );
			$user_email = $user->user_email;
			while ( $loop->have_posts() ) : $loop->the_post();
				if ( !wc_customer_bought_product( $user_email, $user_id, get_the_ID() ) ) continue;
				$purchased_products[] = wc_get_product( get_the_ID() );
			endwhile;
			wp_reset_postdata();
		}

		if( count( $purchased_products ) > 0){
			return $purchased_products;	
		}
		else{
			return false;
		}
	}

	/* gets order amount */

	function upw_get_order_amount( $customer_orders ){
		$total = 0;
		$currency = get_woocommerce_currency_symbol();
		foreach ( $customer_orders as $customer_order ) {
			$order      = new WC_Order();
			$order->populate( $customer_order );
			$total+=$order->get_total();
		}
	
		return $currency.$total;
	}
	
}

	$upw_api = new UPWoocmmerceApi();
}
