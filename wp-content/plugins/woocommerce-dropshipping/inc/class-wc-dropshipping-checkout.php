<?php
class WC_Dropshipping_Checkout {

	public $order_options;

	public function __construct() {
		$this->order_options = get_option( 'wc_dropship_manager' );
		$this->init();
	}

	public function init() {
    add_filter( 'woocommerce_checkout_fields' , array( $this, 'add_custom_checkout_fields' ) );
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'save_custom_checkout_fields' ) );
		add_action( 'woocommerce_admin_order_data_after_shipping_address', array($this, 'show_custom_checkout_order_page' ) );
    add_action( 'wp_enqueue_scripts', array( $this, 'add_custom_checkout_style' ) );
		add_filter( 'manage_edit-shop_order_columns', array( $this, 'show_order_number_header' ) );
		add_filter( 'manage_edit-shop_order_columns', array( $this, 'show_order_tracking_header' ) );
		add_filter( 'manage_edit-shop_order_columns', array( $this, 'show_pod_header' ) );
		add_filter( 'manage_dropshipper-order-list_columns', array( $this, 'show_pod_header' ), 1, 20 );
		add_action( 'manage_shop_order_posts_custom_column', array( $this, 'show_order_number_content' ) );
		add_action( 'manage_shop_order_posts_custom_column', array( $this, 'show_order_tracking_content' ) );
		add_action( 'manage_shop_order_posts_custom_column', array( $this, 'show_pod_content' ) );
	}

  public function add_custom_checkout_fields( $fields ) {
		if( array_key_exists('checkout_order_number', $this->order_options) ) {
			if( $this->order_options['checkout_order_number'] == 1 ) {
		    $fields[ 'order' ]['_wc_dropshipping_order_number' ][ 'label' ] = 'Order Number';
		    $fields[ 'order' ][ '_wc_dropshipping_order_number' ][ 'type' ] = 'text';
		    $fields[ 'order' ][ '_wc_dropshipping_order_number' ][ 'priority' ] = 1;
		    $fields[ 'order' ][ 'order_comments' ][ 'priority' ] = 2;
			}
		}
    return $fields;
  }

	public function  save_custom_checkout_fields( $order_id ) {
	  if ( ! empty( $_POST['_wc_dropshipping_order_number'] ) ) {
	      update_post_meta( $order_id, '_wc_dropshipping_order_number', sanitize_text_field( $_POST['_wc_dropshipping_order_number'] ) );
		}
	}

	public function show_custom_checkout_order_page($order){
		if ( array_key_exists( 'checkout_order_number', $this->order_options ) ) {
			if ( $this->order_options['checkout_order_number'] == 1 ){
				?> <h3>Order Number</h3> <?php
				echo get_post_meta( $order->get_id(), '_wc_dropshipping_order_number', true );
			}
		}
	}

  public function add_custom_checkout_style( ) {
    $base_name = explode( '/', plugin_basename( __FILE__ ) );
    wp_enqueue_style( 'wc_dropshipping_checkout_style', 	plugins_url(). '/' . $base_name[0] . '/assets/css/custom.css' );
  }

	public function show_order_number_header( $columns ) {
		$new_columns = array();
		foreach ( $columns as $column_name => $column_info ) {
				$new_columns[ $column_name ] = $column_info;
				if ( 'status_of_aliexpress' === $column_name ) {
						$new_columns['wc_dropshipping_order_number'] = __( 'Order Number', 'woocommerce-dropshipping' );
				}
		}
		return $new_columns;
	}

	public function show_order_number_content( $column ) {
		global $post;

    if ( 'wc_dropshipping_order_number' === $column ) {
 			$order    = wc_get_order( $post->ID );
			$order_id = is_callable( array( $order, 'get_id' ) ) ? $order->get_id() : $order->id;
			$order_number = get_post_meta( $order_id, '_wc_dropshipping_order_number', true );
      echo $order_number;
		}
	}

	public function show_order_tracking_header( $columns ) {
    $new_columns = array();
    foreach ( $columns as $column_name => $column_info ) {
        $new_columns[ $column_name ] = $column_info;
        if ( 'wc_dropshipping_order_number' === $column_name ) {
            $new_columns['tracking_number'] = __( 'Tracking Number', 'woocommerce-dropshipping' );
        }
    }
    return $new_columns;
	}

	public function show_order_tracking_content( $column ) {
		global $post;

    if ( 'tracking_number' === $column ) {
			$order = wc_get_order( $post->ID );
			$items = $order->get_items();
			$order_status = $order->get_status();
			if( isset( $order_status ) ){
				if( $order_status == 'processing' || $order_status == 'completed' ){
					foreach ( $items as $item_id => $item ) {
						$supplier_nickname = wc_get_order_item_meta($item_id,'supplier',true);
						$user = get_user_by( 'login', $supplier_nickname );
						if( is_object( $user ) ){
							$user_id = $user->ID;
							$tracking_number = get_post_meta($post->ID, 'dropshipper_shipping_info_'.$user_id, true);
							if ( isset( $tracking_number ) ){
								if( is_array( $tracking_number ) ){
									echo $supplier_nickname . ': ' . $tracking_number['tracking_number'] . '</br></br>';
									}
								}
							}
						}
					}
				}
			}

		}

		public function show_pod_header( $columns ) {
			$new_columns = array();
			foreach ( $columns as $column_name => $column_info ) {
					$new_columns[ $column_name ] = $column_info;
					if ( 'tracking_number' === $column_name ) {
							$new_columns['wc_dropshipping_pod'] = __( 'POD', 'woocommerce-dropshipping' );
					}
			}
			return $new_columns;
		}

		public function show_pod_content( $column ) {
			global $post;

			if ( 'wc_dropshipping_pod' === $column ) {
				$order = wc_get_order( $post->ID );
				$items = $order->get_items();
				$order_status = $order->get_status();
				if( isset( $order_status ) ){
					if( $order_status == 'processing' || $order_status == 'completed' ){
						foreach ( $items as $item_id => $item ) {
							$supplier_nickname = wc_get_order_item_meta($item_id,'supplier',true);
							$user = get_user_by( 'login', $supplier_nickname );
							if( is_object( $user ) ){
								$user_id = $user->ID;
								$supplier_pod_id = '_supplier_pod_' . $user_id;
								$pod_status = get_post_meta( $post->ID, $post->ID . '_' . $supplier_pod_id . '_status', true );
								if ( !empty( $pod_status ) ){
										echo $supplier_nickname . ': ' . $pod_status . '</br></br>';
									}
								}
							}
						}
					}
			}
		}

}
