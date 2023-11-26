<?php
/**
 * Class RTWWDPD_Advance_Attribute to calculate discount according to Product Attribute rule.
 *
 * @since    1.0.0
 */
class RTWWDPD_Advance_Payment extends RTWWDPD_Advance_Base {
	/**
	 * variable to set instance of payment module.
	 *
	 * @since    1.0.0
	 */
	private static $rtwwdpd_instance;

	/**
	 * function to set instance of payment module.
	 *
	 * @since    1.0.0
	 */
	public static function rtwwdpd_instance() {
		if ( self::$rtwwdpd_instance == null ) {
			self::$rtwwdpd_instance = new RTWWDPD_Advance_Payment( 'advanced_payment' );
		}
		return self::$rtwwdpd_instance;
	}

	/**
	 * variable to check applied modules.
	 *
	 * @since    1.0.0
	 */
	private $rtwwdpd_used_rules = array();

	/**
	 * construct function.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $rtwwdpd_module_id ) {
		parent::__construct( $rtwwdpd_module_id );
	}

	/**
	 * function to apply discount on cart items.
	 *
	 * @since    1.0.0
	 */
	public function rtwwdpd_adjust_cart( $rtwwdpd_temp_cart ) {
		global $woocommerce;
		if (!empty($woocommerce->cart->applied_coupons))
		{
			$active = get_site_option('rtwwdpd_coupon_with_discount', 'yes');
			if($active == 'no')
			{
				return;
			}
		}
		$sabcd = 'fication_done';
		$rtwwdpd_verification_done = get_site_option( 'rtwbma_veri'.$sabcd, array() );
		if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) ) {
		global $woocommerce;
		$rtwwdpd_num_decimals = apply_filters( 'rtwwdpd_get_decimals', (int) get_option( 'woocommerce_price_num_decimals' ) );
		$rtwwdpd_setting_pri = get_option('rtwwdpd_setting_priority');
		$rtwwdpd_today_date = current_time('Y-m-d');
		$rtwwdpd_user = wp_get_current_user();

		$rtwwdpd_no_oforders = wc_get_customer_order_count( get_current_user_id());
		$rtwwdpd_args = array(
			'customer_id' => get_current_user_id(),
			'post_status' => 'cancelled',
			'post_type' => 'shop_order',
			'return' => 'ids',
		);
		$rtwwdpd_numordr_cancld = 0;
		$rtwwdpd_numordr_cancld = count( wc_get_orders( $rtwwdpd_args ) );
		$rtwwdpd_no_oforders = $rtwwdpd_no_oforders - $rtwwdpd_numordr_cancld;
		$rtwwdpd_ordrtotal = wc_get_customer_total_spent(get_current_user_id());
		$rtwwdpd_set_id = 'rtwwdpd_pymnt';
		$rtwwdpd_cart_prod_count = $woocommerce->cart->cart_contents;
		$rtwwdpd_prod_count = 0;
		$rtwwdpd_cart_total = $woocommerce->cart->get_subtotal();
		$set_id = '';
		if( is_array($rtwwdpd_cart_prod_count) && !empty($rtwwdpd_cart_prod_count) )
		{
			foreach ($rtwwdpd_cart_prod_count as $key => $value) {
				$rtwwdpd_prod_count += $value['quantity'];
			}
		}
		if(is_array($rtwwdpd_setting_pri) && !empty($rtwwdpd_setting_pri))
		{
			if($rtwwdpd_setting_pri['rtw_offer_select'] == 'rtw_all_mtch')
			{
				if( isset( $rtwwdpd_setting_pri['pay_rule'] ) && $rtwwdpd_setting_pri['pay_rule'] == 1 )
				{
					$rtwwdpd_pay_rul = get_option('rtwwdpd_pay_method');

					if(!is_array($rtwwdpd_pay_rul) || empty($rtwwdpd_pay_rul))
					{
						return;
					}
					foreach ($rtwwdpd_pay_rul as $pay => $rul) {

						$rtwwdpd_user_role = $rul['rtwwdpd_select_roles'] ;
						$rtwwdpd_role_matched = false;
						foreach ($rtwwdpd_user_role as $rol => $role) {
							if($role == 'all'){
								$rtwwdpd_role_matched = true;
							}
							if (in_array( $role, (array) $rtwwdpd_user->roles ) ) {
								$rtwwdpd_role_matched = true;
							}
						}
						if($rtwwdpd_role_matched == false)
						{
							continue;
						}

						if(isset($rul['rtwwdpd_min_prod_cont']) && $rul['rtwwdpd_min_prod_cont'] > $rtwwdpd_prod_count)
						{
							continue;
						}
						if(isset($rul['rtwwdpd_min_spend']) && $rul['rtwwdpd_min_spend'] > $rtwwdpd_cart_total)
						{
							continue;
						}

						$rtwwdpd_matched = true;
						if($rul['rtwwdpd_pay_from_date'] > $rtwwdpd_today_date || $rul['rtwwdpd_pay_to_date'] < $rtwwdpd_today_date)
						{
							continue;
						}

						if( !is_array($rtwwdpd_temp_cart) || empty($rtwwdpd_temp_cart))
						{
							return;
						}
						foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item ) {
							$rtwwdpd_process_discounts = apply_filters( 'rtwwdpd_process_product_discounts', true, $cart_item['data'], 'advanced_payment', $this, $cart_item );

							if ( ! $rtwwdpd_process_discounts ) {
								continue;
							}

							if ( ! $this->rtwwdpd_is_cumulative( $cart_item, $cart_item_key ) ) {


								if ( $this->rtwwdpd_is_item_discounted( $cart_item, $cart_item_key ) && apply_filters( 'rtwwdpd_stack_order_totals', false ) === false ) {
									// continue;
								}
							}

							$rtwwdpd_discounted = isset( WC()->cart->cart_contents[ $cart_item_key ]['discounts'] );

							if ($rtwwdpd_discounted){
								$d = WC()->cart->cart_contents[ $cart_item_key ]['discounts'];
								if (in_array('advanced_payment', $d['by'])) {
									continue;
								}
							}

							$rtwwdpd_original_price = $this->rtw_get_price_to_discount( $cart_item, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ), 'advanced_payment' );

							if ( $rtwwdpd_original_price ) { 
								$rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $rul['rtwwdpd_pay_discount_value'], 'adv_pay', $cart_item, $this );

								$rtwwdpd_cart_prod_count = count( WC()->cart->get_cart());
								$rtwwdpd_cart_total = $woocommerce->cart->get_subtotal();
								$rtwwdpd_chosen_gateway = WC()->session->chosen_payment_method;
								$rtwwdpd_dscnt_on = $rul['allowed_payment_methods'];

								if($rtwwdpd_chosen_gateway == $rtwwdpd_dscnt_on)
								{
									if($rul['rtwwdpd_min_prod_cont'] <= $rtwwdpd_cart_prod_count && $rul['rtwwdpd_min_spend'] <= $rtwwdpd_cart_total)
									{
										if($rul['rtwwdpd_pay_discount_type'] == 'rtwwdpd_discount_percentage')
										{
											$rtwwdpd_amount = $rtwwdpd_amount / 100;
											$rtwwdpd_dscnted_val = ( floatval( $rtwwdpd_amount ) * $rtwwdpd_original_price );
											if( $rtwwdpd_dscnted_val > $rul['rtwwdpd_pay_max_discount'])
											{
												$rtwwdpd_dscnted_val = $rul['rtwwdpd_pay_max_discount'];
											}

											$rtwwdpd_price_adjusted = ( floatval( $rtwwdpd_original_price ) - $rtwwdpd_dscnted_val );

											if(isset($rul['rtwwdpd_pay_exclude_sale']))
											{
												if( !$cart_item['data']->is_on_sale() )
												{
													Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
													continue;
												}
											}
											else{
												Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
												continue;
											}
										}
										else{
											if($rtwwdpd_amount > $rul['rtwwdpd_pay_max_discount'])
											{
												$rtwwdpd_amount = $rul['rtwwdpd_pay_max_discount'];
											}
											$rtwwdpd_new_price = $rtwwdpd_amount/$rtwwdpd_prod_count;

											$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_new_price );

											if(isset($rul['rtwwdpd_pay_exclude_sale']))
											{

												if( !$cart_item['data']->is_on_sale() )
												{
													Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
													continue;
												}
											}
											else{
												Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
												continue;
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}}
	}

	/**
	 * Function to get disocunting rules.
	 *
	 * @since    1.0.0
	 */
	protected function rtw_get_pricing_rule_sets( $cart_item ) {
		$sabcd = 'fication_done';
		$rtwwdpd_verification_done = get_site_option( 'rtwbma_veri'.$sabcd, array() );
		if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) ) { 

			$rtwwdpd_product = wc_get_product( $cart_item['data']->get_id() );

			if ( empty( $rtwwdpd_product ) ) {
				return false;
			}

			$rtwwdpd_pricing_rule_sets = apply_filters( 'rtwwdpd_get_product_pricing_rule_sets', $this->rtwwdpd_get_product_meta( $rtwwdpd_product, '_pricing_rules' ), $rtwwdpd_product->get_id(), $this );

			$rtwwdpd_pricing_rule_sets = apply_filters( 'rtwwdpd_get_cart_item_pricing_rule_sets', $rtwwdpd_pricing_rule_sets, $cart_item );

			$rtwwdpd_sets              = array();
			if ( $rtwwdpd_pricing_rule_sets ) {
				foreach ( $rtwwdpd_pricing_rule_sets as $set_id => $set_data ) {
					$rtwwdpd_sets[ $set_id ] = new RTWWDPD_Adjustment_Set_Product( $set_id, $set_data );
				}
			}

			return $rtwwdpd_sets;
		}
	}

	/**
	 * Function to get product detail.
	 *
	 * @since    1.0.0
	 */
	public static function rtwwdpd_get_product_meta( $rtwwdpd_product, $key, $context = 'view' ) {
		if ( empty( $rtwwdpd_product ) ) {
			return false;
		}
		$sabcd = 'fication_done';
		$rtwwdpd_verification_done = get_site_option( 'rtwbma_veri'.$sabcd, array() );
		if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) ) { 
			return get_post_meta( $rtwwdpd_product->get_id(), $key, true);
		}
	}

}
