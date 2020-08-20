<?php

/**
 * Class CT_Ultimate_GDPR_Service_Order_Delivery_Date_For_Woocommerce
 */
class CT_Ultimate_GDPR_Service_Order_Delivery_Date_For_Woocommerce extends CT_Ultimate_GDPR_Service_Abstract {

	/**
	 * @return void
	 */
	public function init() {
		add_filter( 'ct_ultimate_gdpr_controller_plugins_compatible_order-delivery-date-for-woocommerce/order_delivery_date.php', '__return_true' );
		add_filter( 'ct_ultimate_gdpr_controller_plugins_collects_data_order-delivery-date-for-woocommerce/order_delivery_date.php', '__return_false' );
	}

	/**
	 * @return $this
	 */
	public function collect() {
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function get_name() {
		return apply_filters( "ct_ultimate_gdpr_service_{$this->get_id()}_name", 'Order Delivery Date for WooCommerce Lite' );
	}

	/**
	 * @return bool
	 */
	public function is_active() {
		return class_exists( 'order_delivery_date_lite' );
	}

	/**
	 * @return bool
	 */
	public function is_forgettable() {
		return false;
	}

	/**
	 * @return bool
	 */
	public function is_subscribeable() {
		return false;
	}



	/**
	 * @throws Exception
	 * @return void
	 */
	public function forget() {

	}

	/**
	 * @return mixed
	 */
	public function add_option_fields() {

	}

	/**
	 *
	 */
	public function render_field_breach_services() {

	}

	/**
	 * @return mixed
	 */
	public function front_action() {

	}
}