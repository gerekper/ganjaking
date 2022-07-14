<?php
if( !class_exists( 'UPWHookActions' ) ){

class UPWHookActions{
	var $upw_api;
	function __construct(){
		$this->upw_api = new UPWoocmmerceApi();
		if(!isset($upw_default_options)){
			$upw_default_options = new UPWDefaultOptions();
		}
		if( $upw_default_options->userpro_woocommerce_get_option( 'upw_hide_orders' ) == 'n' ){
			add_action( 'userpro_after_fields', array( $this, 'upw_add_orders_tab' ) );
		}
		if( $upw_default_options->userpro_woocommerce_get_option( 'upw_hide_purchases' ) == 'n'){
			add_action( 'userpro_after_fields', array( $this, 'upw_add_purchases_tab' ) );
		}
		if( $upw_default_options->userpro_woocommerce_get_option( 'upw_userprologin' ) == 'y'){
			add_filter('wc_get_template',array($this,'UserPrologin'),10,5);
		}
		if( $upw_default_options->userpro_woocommerce_get_option( 'upw_show_wishlist' ) == 'y'){
			add_action( 'userpro_after_fields', array( $this, 'upw_add_wishlist_tab' ) );
		}	
		add_action( 'after_dashboard_side', array( $this, 'upw_add_orders_tab_in_dashboard' ));
		add_action( 'userpro_after_fields', array( $this, 'upw_add_billing_shipping_tab' ) );
		add_action( 'after_dashboard_profile_content', array( $this, 'upw_add_orders_dashboard' ),10,3);
		add_filter('updb_default_options_array',array($this,'upw_widget_in_dashboard'),'10','1');
	}

	function UserPrologin($located, $template_name, $args, $template_path, $default_path)
	{
		if($template_name == 'global/form-login.php'){
			$located = UPWPATH . "templates/login.php" ; 
			return $located;
		}
		else
			return $located;
	}


	function upw_add_orders_tab_in_dashboard()
	{
?>
		
					<div class="uploadPic dashboard-side" data-id ="upw-recent-order">
					<a href="#" class="uploadPic-box">
						<span>
							<i class="fa fa-bars"></i>
						</span>
						<?php _e('Orders', 'userpro-dashboard');?>
					</a>
					</div>

					<div class="uploadPic dashboard-side" data-id ="upw-billing-address">
					<a href="#" class="uploadPic-box">
						<span>
							<i class="fa fa-bars"></i>
						</span>
						<?php _e('Billing Address', 'userpro-dashboard');?>
					</a>
					</div>

					<div class="uploadPic dashboard-side" data-id ="upw-shipping-address">
					<a href="#" class="uploadPic-box">
						<span>
							<i class="fa fa-bars"></i>
						</span>
						<?php _e('Shipping Address', 'userpro-dashboard');?>
					</a>
					</div>
				
	<?php } 


	/* Show order tab on profile page */

	function upw_add_orders_dashboard($arg, $edit_fields, $unique_id)
	{
		require_once UPWPATH . 'templates/upw-orders.php';
		require_once UPWPATH . 'templates/dashboard-billing-address.php';
		require_once UPWPATH . 'templates/dashboard-shipping-address.php';

	}

	function upw_add_orders_tab( $arg ){
		if( $arg['template'] == 'view' && $arg['user_id'] == get_current_user_id()){
			require_once UPWPATH . 'templates/upw-orders.php';
		}
	}
        function upw_add_wishlist_tab( $arg ){
		if( $arg['template'] == 'view' && $arg['user_id'] == get_current_user_id()){
			require_once UPWPATH . 'templates/upw-wishlist.php';
		}
	}
	/*show billing address and shipping address tab in dashboard*/
	function upw_add_billing_shipping_tab( $arg ){
		if( $arg['template'] == 'view' && $arg['user_id'] == get_current_user_id() && is_page("dashboard")){
				require_once UPWPATH . 'templates/dashboard-billing-address.php';
				require_once UPWPATH . 'templates/dashboard-shipping-address.php';
		}
	}


	/* Show purchase tab on profile page */

	function upw_add_purchases_tab( $arg ){
		global $wpdb;
		if( $arg['template'] == 'view' && $arg['user_id'] == get_current_user_id()){
			if( empty( $upw_default_options ) ){
				$upw_default_options = new UPWDefaultOptions();
			}
			$user = get_userdata( get_current_user_id() );
			$customer_data = array( get_current_user_id() );
			$customer_data[] = $user->user_email;
			$customer_data = array_map( 'esc_sql', array_filter( array_unique( $customer_data ) ) );
			$total_products = $upw_default_options->userpro_woocommerce_get_option( 'upw_total_products_show' );
			$result = $wpdb->get_col( "
								SELECT distinct(im.meta_value) FROM {$wpdb->posts} AS p
								INNER JOIN {$wpdb->postmeta} AS pm ON p.ID = pm.post_id
								INNER JOIN {$wpdb->prefix}woocommerce_order_items AS i ON p.ID = i.order_id
								INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS im ON i.order_item_id = im.order_item_id
								WHERE p.post_status IN ( 'wc-completed', 'wc-processing' )
								AND pm.meta_key IN ( '_billing_email', '_customer_user' )
								AND im.meta_key IN ( '_product_id', '_variation_id' )
								AND im.meta_value != 0
								AND pm.meta_value IN ( '" . implode( "','", $customer_data ) . " ' ) limit $total_products" );
			if ( $result ) {
				
				require_once UPWPATH . 'templates/upw-purchase-header.php';
				require_once UPWPATH . 'templates/upw-purchases.php';
				require_once UPWPATH . 'templates/upw-purchase-footer.php';
			}
			
		}
	}

	function show_orders_under_profile( $user_id ){
		$customer_orders = $this->upw_api->upw_get_customer_orders( $user_id, 'completed', true );
		$order_count = count( $customer_orders  );
		$order_total = $this->upw_api->upw_get_order_amount( $customer_orders );
		?>
		<script type=text/javascript>
			var order_count = "<?php echo $order_count;?>";
			var order_total = "<?php echo $order_total;?>";
			jQuery('.userpro-sc-bar>.userpro-sc-right').append('<div class="upw-order-snap"><a href="#upw-recent-order">'+order_count+' Orders | '+order_total+'</a></div>');
		</script>
<?php 
	}

	function show_billing_address_under_profile( $user_id ){
		$customer_billing_address = $this->upw_api->get_customer_billing_address( $user_id, 'completed', true );
    $billing_address = apply_filters( 'woocommerce_api_customer_billing_address', array(
      'first_name',
      'last_name',
      'company',
      'address_1',
      'address_2',
      'city',
      'state',
      'postcode',
      'country',
      'email',
      'phone',
    ) );

		//$order_count = count( $customer_orders  );
		//$order_total = $this->upw_api->upw_get_order_amount( $customer_orders );
		?>
		<!--script type=text/javascript>
			var order_count = "<?php echo $order_count;?>";
			var order_total = "<?php echo $order_total;?>";
			jQuery('.userpro-sc-bar>.userpro-sc-right').append('<div class="upw-order-snap"><a href="#upw-recent-order">'+order_count+' Orders | '+order_total+'</a></div>');
		</script-->
<?php 
	}
	
	
	function upw_widget_in_dashboard($array)
	{
		$template_path= UPWPATH.'templates/';
		$olddata=$array['updb_available_widgets'];
		$newdata= array ('wcorders'=>array('title'=>'Orders', 'template_path'=>$template_path ) , 'wcpurchases'=>array('title'=>'Purchases', 'template_path'=>$template_path) );
		$array['updb_available_widgets']=   array_merge($olddata,$newdata);
		$oldunsetwidgets=$array['updb_unused_widgets'];
		$newunsetwidgets= array( 'wcorders', 'wcpurchases' );
		$array['updb_unused_widgets']= array_merge($oldunsetwidgets,$newunsetwidgets);
	
		return $array;
	}
	
	
}

	new UPWHookActions();
}
?>
