<?php
/**
 * Class RTWWDPD_Module_Base to calculate discount according to Modules.
 *
 * @since    1.0.0
 */
abstract class RTWWDPD_Module_Base {
	/**
	 * variable to set module id.
	 *
	 * @since    1.0.0
	 */
	public $rtwwdpd_module_id;
	/**
	 * variable to set module type.
	 *
	 * @since    1.0.0
	 */
	public $rtwwdpd_module_type;

	public $module_id;
	public $module_type;
	/**
	 * construct function.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $rtwwdpd_module_id, $rtwwdpd_module_type ) {
		$this->module_id   = $rtwwdpd_module_id;
		$this->module_type = $rtwwdpd_module_type;
	}

	/**
	 * Function defined abstract.
	 *
	 * @since    1.0.0
	 */
	public abstract function rtwwdpd_adjust_cart( $rtwwdpd_cart );

	/**
	 * Function to get product price on which discount is applied.
	 *
	 * @since    1.0.0
	 */
	public function rtw_get_price_to_discount( $rtwwdpd_cart_item, $rtwwdpd_cart_item_key, $rtw_stack_rules = false, $rule_name='' ) {
		$sabcd = 'verification_done';
		$rtwwdpd_verification_done = get_site_option( 'rtwbma_'.$sabcd, array() );
		if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) ) { 
			global $woocommerce;
			$rtwwdpd_setting_pri = get_option('rtwwdpd_setting_priority');
			$rtwwdpd_result = false;
			do_action( 'rtwwdpd_memberships_discounts_disable_price_adjustments' );

			$rtwwdpd_filter_cart_item = $rtwwdpd_cart_item;
			
			if ( isset( WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ] ) ) {
				$rtwwdpd_filter_cart_item = WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ];
				if ( isset( WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ]['discounts'] ) ) {
					if ( $this->rtwwdpd_is_cumulative( $rtwwdpd_cart_item, $rtwwdpd_cart_item_key ) || $rtw_stack_rules ) {
						$rtwwdpd_result = WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ]['discounts']['price_adjusted'];
					} else {
						if(WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ]['discounts']['by'][0] != $rule_name)
						{
							$rtwwdpd_result = WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ]['discounts']['price_adjusted'];
						}
						else{

							$rtwwdpd_result = WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ]['discounts']['price_base'];
						}

					}
				} else {
					if( isset( $rtwwdpd_setting_pri['rtw_dscnt_on'] ) && $rtwwdpd_setting_pri['rtw_dscnt_on'] == 'rtw_sale_price')
					{
						if ( apply_filters( 'rtwwdpd_dynamic_pricing_get_use_sale_price', true, $rtwwdpd_filter_cart_item['data'] ) ) {
							$rtwwdpd_result = WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ]['data']->get_price('edit');
						} 
						else {
							$rtwwdpd_result = WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ]['data']->get_regular_price('edit');
						}
					}
					else{
						$rtwwdpd_result = WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ]['data']->get_regular_price('edit');
					}
				}
			}
			return $rtwwdpd_result;
		}
	}

	/**
	 * Function to check if a product is discounted.
	 *
	 * @since    1.0.0
	 */
	protected function rtwwdpd_is_item_discounted( $rtwwdpd_cart_item, $rtwwdpd_cart_item_key ) {
		global $woocommerce;
		
		return isset( WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ]['discounts'] );
	}

	/**
	 * Function to check if a product is already discounted by the same rule.
	 *
	 * @since    1.0.0
	 */
	protected function rtwwdpd_is_cumulative( $rtwwdpd_cart_item, $rtwwdpd_cart_item_key, $rtwwdpd_default = false ) {
		//Check to make sure the item has not already been discounted by this module.  This could happen if update_totals is called more than once in the cart. 
		$sabcd = 'verification_done';
		$rtwwdpd_verification_done = get_site_option( 'rtwbma_'.$sabcd, array() );
		if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) ) { 
			$rtwwdpd_cart = WC()->cart->get_cart();
			if ( isset( $rtwwdpd_cart ) && is_array( $rtwwdpd_cart ) && isset( $rtwwdpd_cart[ $rtwwdpd_cart_item_key ]['discounts'] ) && in_array( $this->module_id, WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ]['discounts']['by'] ) ) {
				
				return false;
			} else {
				return apply_filters( 'rtwwdpd_is_cumulative', $rtwwdpd_default, $this->module_id, $rtwwdpd_cart_item, $rtwwdpd_cart_item_key );
			}
		}
	}

	/**
	 * Function to reset cart items.
	 *
	 * @since    1.0.0
	 */
	protected function rtw_reset_cart_item( &$rtwwdpd_cart_item, $rtwwdpd_cart_item_key ) {

		if ( isset( WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ]['discounts'] ) && in_array( $this->module_id, WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ]['discounts']['by'] ) ) {
			foreach ( WC()->cart->cart_contents[ $rtwwdpd_cart_item_key ]['discounts'] as $module ) {

			}
		}
	}
}