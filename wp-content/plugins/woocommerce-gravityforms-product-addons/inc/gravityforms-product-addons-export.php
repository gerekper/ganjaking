<?php


//Export management
class WC_GFPA_Export {

	private static $instance;

	public static function register() {
		if ( self::$instance == null ) {

			if ( apply_filters( 'woocommerce_gravityforms_create_entries', true ) ) {
				self::$instance = new WC_GFPA_Export();
			}
		}
	}

	private function __construct() {
		add_filter( 'gform_export_fields', array( $this, 'add_wc_order_fields' ), 10, 1 );
		add_filter( 'gform_export_field_value', array( $this, 'export_wc_order_fields' ), 10, 4 );
	}


	public function add_wc_order_fields( $form ) {
		$form['fields'][] = array(
			'id'    => 'woocommerce_order_number',
			'label' => __( 'WooCommerce Order Number', 'wc_gf_addons' )
		);

		$form['fields'][] = array(
			'id'    => 'woocommerce_order_status',
			'label' => __( 'WooCommerce Order Status', 'wc_gf_addons' )
		);

		$form['fields'][] = array(
			'id'    => 'woocommerce_order_item_number',
			'label' => __( 'WooCommerce Order Item Line Number', 'wc_gf_addons' )
		);

		$form['fields'][] = array(
			'id'    => 'woocommerce_order_item_product_name',
			'label' => __( 'WooCommerce Order Item Product Name', 'wc_gf_addons' )
		);

		$form['fields'][] = array(
			'id'    => 'woocommerce_order_item_product_id',
			'label' => __( 'WooCommerce Order Item Product ID', 'wc_gf_addons' )
		);

		$form['fields'][] = array(
			'id'    => 'woocommerce_order_item_product_sku',
			'label' => __( 'WooCommerce Order Item Product SKU', 'wc_gf_addons' )
		);

		$form['fields'][] = array(
			'id'    => 'woocommerce_order_item_product_quantity',
			'label' => __( 'WooCommerce Order Item Quantity', 'wc_gf_addons' )
		);

		return $form;
	}


	public function export_wc_order_fields( $value, $form_id, $field_id, $entry ) {
		switch ( $field_id ) {
			case 'woocommerce_order_number' :
				$order_id = gform_get_meta( $entry['id'], 'woocommerce_order_number' );
				if ( ! empty( $order_id ) && $the_order = wc_get_order( $order_id ) ) {
					$value     = $the_order->get_order_number();
				} else {
					$value = '';
				}
				break;
			case 'woocommerce_order_item_number' :
				$order_item_id = gform_get_meta( $entry['id'], 'woocommerce_order_item_number' );
				$value         = empty( $order_item_id ) ? '' : $order_item_id;
				break;
			case 'woocommerce_order_item_product_name' :
				$value         = '';
				$order_id      = gform_get_meta( $entry['id'], 'woocommerce_order_number' );
				$order_item_id = gform_get_meta( $entry['id'], 'woocommerce_order_item_number' );
				$the_order     = wc_get_order( $order_id );
				if ( $the_order ) {
					$order_items = $the_order->get_items();
					if ( isset( $order_items[ $order_item_id ] ) ) {
						$value = $order_items[ $order_item_id ]['name'];
					}
				}
				break;
			case 'woocommerce_order_item_product_id' :
				$value         = '';
				$order_id      = gform_get_meta( $entry['id'], 'woocommerce_order_number' );
				$order_item_id = gform_get_meta( $entry['id'], 'woocommerce_order_item_number' );
				$the_order     = wc_get_order( $order_id );
				if ( $the_order ) {
					$order_items = $the_order->get_items();
					if ( isset( $order_items[ $order_item_id ] ) ) {
						$value = $order_items[ $order_item_id ]['product_id'];
					}
				}
				break;
			case 'woocommerce_order_item_product_quantity' :
				$order_id      = gform_get_meta( $entry['id'], 'woocommerce_order_number' );
				$order_item_id = gform_get_meta( $entry['id'], 'woocommerce_order_item_number' );
				$the_order     = wc_get_order( $order_id );
				if ( $the_order ) {
					$order_items = $the_order->get_items();
					if ( isset( $order_items[ $order_item_id ] ) ) {
						$value = $order_items[ $order_item_id ]['qty'];
					}
				}
				break;
			case 'woocommerce_order_item_product_sku' :
				$value         = '';
				$order_id      = gform_get_meta( $entry['id'], 'woocommerce_order_number' );
				$order_item_id = gform_get_meta( $entry['id'], 'woocommerce_order_item_number' );
				$the_order     = wc_get_order( $order_id );
				if ( $the_order ) {
					$order_items = $the_order->get_items();
					if ( isset( $order_items[ $order_item_id ] ) ) {
						$order_item_product = $order_items[ $order_item_id ];
						// if $order_item_product is type of WC_Order_Item_Product, proceed to get the information.
						if ( $order_item_product instanceof WC_Order_Item_Product ) {
							$product = $order_item_product->get_product();
							$value   = $product->get_sku();
						}
					}
				}
				break;
			case 'woocommerce_order_status' :
				$order_id = gform_get_meta( $entry['id'], 'woocommerce_order_number' );
				if ( $order_id ) {
					$order = wc_get_order( $order_id );
					if ( $order ) {
						$value = $order->get_status();
					}
				}

				break;
		}

		return $value;
	}
}
