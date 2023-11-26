<?php
/**
 * Class RTWWDPD_Advance_Product_Tag to calculate discount according to Product Tag rule.
 *
 * @since    1.0.0
 */
class RTWWDPD_Advance_Product_Variation extends RTWWDPD_Advance_Base {
	/**
	 * variable to set instance of product module.
	 *
	 * @since    1.0.0
	 */
	private static $rtwwdpd_instance;
	
	/**
	 * function to set instance of product module.
	 *
	 * @since    1.0.0
	 */
	public static function rtwwdpd_instance() {
		if ( self::$rtwwdpd_instance == null ) {
			self::$rtwwdpd_instance = new RTWWDPD_Advance_Product_Variation( 'advanced_pro_var' );
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
		if( is_array($rtwwdpd_temp_cart) && !empty($rtwwdpd_temp_cart) )
		{
			$rtwwdpd_cart_prod_count = $woocommerce->cart->cart_contents;
			$rtwwdpd_prod_count = 0;
			if( is_array($rtwwdpd_cart_prod_count) && !empty($rtwwdpd_cart_prod_count) )
			{
				foreach ($rtwwdpd_cart_prod_count as $key => $value) {
					$rtwwdpd_prod_count += $value['quantity'];
				}
			}
			
			foreach ( $rtwwdpd_temp_cart as $cart_item_key => $values ) {
				$rtwwdpd_temp_cart[ $cart_item_key ]                       = $values;
				$rtwwdpd_temp_cart[ $cart_item_key ]['available_quantity'] = $values['quantity'];
			}
			$rtwwdpd_setting_pri = get_option('rtwwdpd_setting_priority');
			$rtwwdpd_user = wp_get_current_user();
			$rtwwdpd_num_decimals = apply_filters( 'rtwwdpd_get_decimals', (int) get_option( 'woocommerce_price_num_decimals' ) );
			$rtwwdpd_no_oforders = wc_get_customer_order_count( get_current_user_id());
			$rtwwdpd_today_date = current_time('Y-m-d');
			$rtwwdpd_ordrtotal = wc_get_customer_total_spent(get_current_user_id());
			$set_id = 1;
			if( is_array($rtwwdpd_setting_pri) && !empty($rtwwdpd_temp_cart) )
			{
				if($rtwwdpd_setting_pri['rtw_offer_select'] == 'rtw_all_mtch')
				{
					if( isset( $rtwwdpd_setting_pri['var_rule'] ) && $rtwwdpd_setting_pri['var_rule'] == 1 )
					{	
						$rtwwdpd_var_rul = get_option('rtwwdpd_variation_rule'); 
						if( is_array( $rtwwdpd_var_rul ) && !empty( $rtwwdpd_var_rul ) )
						{
							$rtwwdpd_variation_arr = array();
							foreach ( $rtwwdpd_var_rul as $key => $value ) {
								$rtwwdpd_variation_arr[$key] = $value['rtwwdpd_offer_name'];
							}
							$rtwwdpd_variation_arr 	= array_merge( array( '0' => 'Select Offer' ), $rtwwdpd_variation_arr );
							$sabcd = 'fication_done';
							$rtwwdpd_verification_done = get_site_option( 'rtwbma_veri'.$sabcd, array() );
							if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) ) { 
								foreach ( $rtwwdpd_var_rul as $var => $rul ) {
									
									if( $rul['rtwwdpd_from_date'] > $rtwwdpd_today_date || $rul['rtwwdpd_to_date'] < $rtwwdpd_today_date )
									{
										continue 1;
									}

									$rtwwdpd_user_role = $rul['rtwwdpd_select_roles'] ;
									$rtwwdpd_role_matched = false;
									if( is_array( $rtwwdpd_user_role ) && !empty( $rtwwdpd_user_role ) )
									{
										foreach ($rtwwdpd_user_role as $rol => $role) {
											if( $role == 'all' ){
												$rtwwdpd_role_matched = true;
											}
											if ( in_array( $role, (array) $rtwwdpd_user->roles ) ) {
												$rtwwdpd_role_matched = true;
											}
										}
									}
									
									if($rtwwdpd_role_matched == false)
									{
										continue 1;
									}

									if(isset($rul['rtwwdpd_min_orders']) && $rul['rtwwdpd_min_orders'] > $rtwwdpd_no_oforders)
									{
										continue 1;
									}
									if(isset($rul['rtwwdpd_min_spend']) && $rul['rtwwdpd_min_spend'] > $rtwwdpd_ordrtotal)
									{
										continue 1;
									}
								
									foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item ) {
										
										if(isset($rul['product_exe_id']) && $rul['product_exe_id'] == $cart_item['data']->get_id())
										{
											continue 1;
										}

										if( $cart_item['variation_id'] > 0 )
										{	
											$product = wc_get_product( $cart_item['data']->get_id() ); 
											$rtwwdpd_var_offer = get_post_meta($cart_item['data']->get_id(), 'rtwwdpd_variation');
											
											if( $rtwwdpd_var_offer != 0 )
											{ 
												$rtwwdpd_rules = $rtwwdpd_var_offer[0];

												if( stripos($rtwwdpd_rules, $rul['rtwwdpd_offer_name']) )
												{	
													if($rul['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
													{
														if($cart_item['quantity'] >= $rul['rtwwdpd_min'] )
														{ 		
															if(isset($rul['rtwwdpd_max']) && !empty($rul['rtwwdpd_max']))
															{
																if($cart_item['quantity'] > $rul['rtwwdpd_max'])
																{
																	continue;
																}
															}
																	
															if ( ! $this->rtwwdpd_is_cumulative( $cart_item, $cart_item_key ) ) 
															{
																if ( $this->rtwwdpd_is_item_discounted( $cart_item, $cart_item_key ) && apply_filters( 'rtwwdpd_stack_order_totals', false ) === false ) {
																	continue;
																}
															}

															$rtwwdpd_discounted = isset( WC()->cart->cart_contents[ $cart_item_key ]['discounts'] );
															
															if ($rtwwdpd_discounted){
																$rtwwdpd_d = WC()->cart->cart_contents[ $cart_item_key ]['discounts'];
																if (in_array('advanced_totals', $rtwwdpd_d['by'])) {
																	continue;
																}
															}
															// if(isset($rul['amount']) && !empty($rul['amount']))
															// {																
															//  	$rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $rul['amount'], $rul, $cart_item, $this );															
															// }
															
															$rtwwdpd_original_price = $this->rtw_get_price_to_discount( $cart_item, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ), 'advanced_pro_var' );

															if($rul['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
															{
																$rtwwdpd_price = $rul['rtwwdpd_discount_value'];
																$rtwwdpd_max = $rul['rtwwdpd_max_discount'];

																$price_adjust = $rtwwdpd_original_price * ($rtwwdpd_price / 100);

																if($price_adjust > $rtwwdpd_max)
																{
																	$price_adjust = $rtwwdpd_max;
																}
																$rtwwdpd_price_adjusted = $rtwwdpd_original_price - $price_adjust;

																Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_pro_var', $set_id );
																
															}
															else{
																$rtwwdpd_price = $rul['rtwwdpd_discount_value'];
																$rtwwdpd_max = $rul['rtwwdpd_max_discount'];

																if($rtwwdpd_price <= $rtwwdpd_max)
																{
																	$rtwwdpd_price_adjusted = $rtwwdpd_original_price - $rtwwdpd_price;
																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_pro_var', $set_id );
																}
																else{
																	$rtwwdpd_price = $rtwwdpd_max;
																	$rtwwdpd_price_adjusted = $rtwwdpd_original_price - $rtwwdpd_price;
																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_pro_var', $set_id );
																}
															}
														}
													}
													elseif($rul['rtwwdpd_check_for'] == 'rtwwdpd_price')
													{
														if($rtwwdpd_cart_total >= $rul['rtwwdpd_min'] && $rtwwdpd_cart_total <= $rul['rtwwdpd_max'])
														{
															if ( ! $this->rtwwdpd_is_cumulative( $cart_item, $cart_item_key ) ) 
															{
																if ( $this->rtwwdpd_is_item_discounted( $cart_item, $cart_item_key ) && apply_filters( 'rtwwdpd_stack_order_totals', false ) === false ) {
																	continue;
																}
															}
															$rtwwdpd_discounted = isset( WC()->cart->cart_contents[ $cart_item_key ]['discounts'] );

															if ($rtwwdpd_discounted){
																$d = WC()->cart->cart_contents[ $cart_item_key ]['discounts'];
																if (in_array('advanced_totals', $d['by'])) {
																	continue;
																}
															}
															$rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $rule['amount'], $rule, $cart_item, $this );

															$rtwwdpd_original_price = $this->rtw_get_price_to_discount( $cart_item, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ) );

															if($rul['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
															{
																$rtwwdpd_price = $rul['rtwwdpd_discount_value'];
																$rtwwdpd_max = $rul['rtwwdpd_max_discount'];
																$price_adjust = $rtwwdpd_original_price * ($rtwwdpd_price / 100);

																if($price_adjust > $rtwwdpd_max)
																{
																	$price_adjust = $rtwwdpd_max;
																}

																$rtwwdpd_price_adjusted = $rtwwdpd_original_price - $price_adjust;

																Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_pro_var', $set_id );
																
															}
															else{
																$rtwwdpd_price = $rul['rtwwdpd_discount_value'];
																$rtwwdpd_max = $rul['rtwwdpd_max_discount'];

																if($rtwwdpd_price <= $rtwwdpd_max)
																{
																	$rtwwdpd_price_adjusted = $rtwwdpd_original_price - $rtwwdpd_price;

																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_pro_var', $set_id );
																}
															}
														}
													}
													else{
														$rtwwdpd_weight = $cart_item['data']->get_weight();
														if(isset($rtwwdpd_weight) && $cart_item['data']->get_weight() >= $rul['rtwwdpd_min'] && $cart_item['data']->get_weight() <= $rul['rtwwdpd_max'])
														{
															if ( ! $this->rtwwdpd_is_cumulative( $cart_item, $cart_item_key ) ) 
															{
																if ( $this->rtwwdpd_is_item_discounted( $cart_item, $cart_item_key ) && apply_filters( 'rtwwdpd_stack_order_totals', false ) === false ) {
																	continue;
																}
															}
															$rtwwdpd_discounted = isset( WC()->cart->cart_contents[ $cart_item_key ]['discounts'] );

															if ($rtwwdpd_discounted){
																$rtwwdpd_d = WC()->cart->cart_contents[ $cart_item_key ]['discounts'];
																if (in_array('advanced_totals', $rtwwdpd_d['by'])) {
																	continue;
																}
															}
															$rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $rul['rtwwdpd_discount_value'], $rule, $cart_item, $this );

															$rtwwdpd_original_price = $this->rtw_get_price_to_discount( $cart_item, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ) );
															if($rul['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
															{
																$rtwwdpd_price = $rul['rtwwdpd_discount_value'];
																$rtwwdpd_max = $rul['rtwwdpd_max_discount'];

																$price_adjust = $rtwwdpd_original_price * ($rtwwdpd_price / 100);
																if($price_adjust > $rtwwdpd_max)
																{
																	$price_adjust = $rtwwdpd_max;
																}

																$rtwwdpd_price_adjusted = $rtwwdpd_original_price - $price_adjust;

																Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_pro_var', $set_id );
																
															}
															else{
																$rtwwdpd_price = $rul['rtwwdpd_discount_value'];
																$rtwwdpd_max = $rul['rtwwdpd_max_discount'];

																if($rtwwdpd_price <= $rtwwdpd_max)
																{
																	$rtwwdpd_price_adjusted = $rtwwdpd_original_price - $rtwwdpd_price;

																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_pro_var', $set_id );
																}
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}
}
