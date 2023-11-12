<?php
/**
 * Class RTWWDPD_Advance_Product to calculate discount according to Product rule.
 *
 * @since    1.0.0
 */
class RTWWDPD_Advance_Comming_Sale extends RTWWDPD_Advance_Base {
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
			self::$rtwwdpd_instance = new RTWWDPD_Advance_Comming_Sale( 'advanced_coming_sale' );
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
			
			foreach ( $rtwwdpd_temp_cart as $cart_item_key => $values ) {
				$rtwwdpd_temp_cart[ $cart_item_key ]                       = $values;
				$rtwwdpd_temp_cart[ $cart_item_key ]['available_quantity'] = $values['quantity'];
			}
			$rtwwdpd_setting_pri = get_option('rtwwdpd_setting_priority');
			
			if( is_array($rtwwdpd_setting_pri) && !empty($rtwwdpd_temp_cart) )
			{
				$rtwwdpd_get_pro_option = get_option('rtwwdpd_coming_sale');
				$i = 0;
				$rtwwdpd_user = wp_get_current_user();
				$rtwwdpd_num_decimals = apply_filters( 'rtwwdpd_get_decimals', (int) get_option( 'woocommerce_price_num_decimals' ) );
				$rtwwdpd_no_oforders = wc_get_customer_order_count( get_current_user_id());
				$rtwwdpd_today_date = current_time('Y-m-d');
				$rtwwdpd_ordrtotal = wc_get_customer_total_spent(get_current_user_id());
				$set_id = 1;
				if( is_array($rtwwdpd_get_pro_option) && !empty($rtwwdpd_get_pro_option) )
				{
					foreach ($rtwwdpd_get_pro_option as $prod => $pro_rul) {

						if($pro_rul['rtwwdpd_sale_from_date'] > $rtwwdpd_today_date || $pro_rul['rtwwdpd_sale_to_date'] < $rtwwdpd_today_date)
						{
							continue;
						}

						$rtwwdpd_user_role = $pro_rul['rtwwdpd_select_roles'] ;
						$rtwwdpd_role_matched = false;
						if( is_array($rtwwdpd_user_role) && !empty($rtwwdpd_user_role) )
						{
							foreach ($rtwwdpd_user_role as $rol => $role) {
								if($role == 'all'){
									$rtwwdpd_role_matched = true;
								}
								if (in_array( $role, (array) $rtwwdpd_user->roles ) ) {
									$rtwwdpd_role_matched = true;
								}
							}
						}

						if($rtwwdpd_role_matched == false)
						{
							continue;
						}
						if(isset($pro_rul['rtwwdpd_sale_min_orders']) && $pro_rul['rtwwdpd_sale_min_orders'] > $rtwwdpd_no_oforders)
						{
							continue;
						}
						if(isset($pro_rul['rtwwdpd_sale_min_spend']) && $pro_rul['rtwwdpd_sale_min_spend'] > $rtwwdpd_ordrtotal)
						{
							continue;
						}

						foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item ) {

							$rtwwdpd_original_price = $this->rtw_get_price_to_discount( $cart_item, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ) );

							if ( $rtwwdpd_original_price ) {
								$rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $pro_rul['rtwwdpd_discount_value'], $pro_rul, $cart_item, $this );

								if($pro_rul['rtwwdpd_sale_check_for'] == 'rtwwdpd_quantity')
								{
									if($cart_item['quantity'] < $pro_rul['quant_pro'] )
									{
										continue 1;
									}
								}
								elseif($pro_rul['rtwwdpd_sale_check_for'] == 'rtwwdpd_price')
								{
									if($cart_item['data']->price < $pro_rul['quant_pro'] )
									{
										continue 1;
									}
								}
								elseif($pro_rul['rtwwdpd_sale_check_for'] == 'rtwwdpd_weight')
								{
									if($cart_item['data']->weight < $pro_rul['quant_pro'] )
									{
										continue 1;
									}
								}

								if($pro_rul['rtwwdpd_sale_discount_type'] == 'rtwwdpd_discount_percentage')
								{
									$rtwwdpd_amount = $rtwwdpd_amount / 100;
									$rtwwdpd_dscnted_val = ( floatval( $rtwwdpd_amount ) * $rtwwdpd_original_price );
									if($rtwwdpd_dscnted_val > $pro_rul['rtwwdpd_sale_max_discount'])
									{
										$rtwwdpd_dscnted_val = $pro_rul['rtwwdpd_sale_max_discount'];
									}
									$rtwwdpd_price_adjusted = ( floatval( $rtwwdpd_original_price ) - $rtwwdpd_dscnted_val );


									if(isset($pro_rul['product_id']))
									{								
										if($pro_rul['product_id'] == $cart_item['data']->get_id())
										{
											if(isset($pro_rul['rtwwdpd_exclude_sale']))
											{
												if( !$cart_item['data']->is_on_sale() )
												{
													if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
														Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_coming_sale', $set_id );
														$set_id++;
														break;
													}
												}
											}
											else{
												if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
													Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_coming_sale', $set_id );
													$set_id++;
													break;
												}
											}
										}
									}
									elseif($pro_rul['rtwwdpd_rule_on'] == 'rtwwdpd_cart')
									{
										if(isset($pro_rul['rtwwdpd_exclude_sale']))
										{
											if( !$cart_item['data']->is_on_sale() )
											{
												if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
													Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_coming_sale', $set_id );
													$set_id++;
													break;
												}
											}
										}
										else{
											if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
												Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_coming_sale', $set_id );
												$set_id++;
												break;
											}
										}
									}
								}
								elseif($pro_rul['rtwwdpd_sale_discount_type'] == 'rtwwdpd_flat_discount_amount')
								{
									if($rtwwdpd_amount > $pro_rul['rtwwdpd_max_discount'])
									{
										$rtwwdpd_amount = $pro_rul['rtwwdpd_max_discount'];
									}

									$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount);
									if(isset($pro_rul['prod_id']))
									{												
										if($set->pricing_rules['prod_id'] == $cart_item['data']->get_id())
										{
											if(isset($pro_rul['rtwwdpd_exclude_sale']))
											{
												if( !$cart_item['data']->is_on_sale() )
												{
													if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
														Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_coming_sale', $set_id );
														$set_id++;
														break;
													}
												}
											}
											else{
												if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
													Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_coming_sale', $set_id );
													$set_id++;
													break;
												}
											}
										}
									}
									elseif($pro_rul['rtwwdpd_rule_on'] == 'rtwwdpd_cart')
									{
										if(isset($pro_rul['rtwwdpd_exclude_sale']))
										{
											if( !$cart_item['data']->is_on_sale() )
											{
												if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
													Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_coming_sale', $set_id );
													$set_id++;
													break;
												}
											}
										}
										else{
											if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
												Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_coming_sale', $set_id );
												$set_id++;
												break;
											}
										}
									}
								}
								else
								{
									if($rtwwdpd_amount > $pro_rul['rtwwdpd_max_discount'])
									{
										$rtwwdpd_amount = $pro_rul['rtwwdpd_max_discount'];
									}
									$rtwwdpd_amount = ( $rtwwdpd_amount / $cart_item['quantity'] );
									$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount);
									if(isset($pro_rul['prod_id']))
									{												
										if($set->pricing_rules['prod_id'] == $cart_item['data']->get_id())
										{
											if(isset($pro_rul['rtwwdpd_exclude_sale']))
											{
												if( !$cart_item['data']->is_on_sale() )
												{
													if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
														Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_coming_sale', $set_id );
														$set_id++;
														break;
													}
												}
											}
											else{
												if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
													Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_coming_sale', $set_id );
													$set_id++;
													break;
												}
											}
										}
									}
									elseif($pro_rul['rtwwdpd_rule_on'] == 'rtwwdpd_cart')
									{
										if(isset($pro_rul['rtwwdpd_exclude_sale']))
										{
											if( !$cart_item['data']->is_on_sale() )
											{
												if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
													Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_coming_sale', $set_id );
													$set_id++;
													break;
												}
											}
										}
										else{
											if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
												Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_coming_sale', $set_id );
												$set_id++;
												break;
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

	/**
	 * Function to get the discounting rules.
	 *
	 * @since    1.0.0
	 */
	protected function rtwwdpd_get_pricing_rule_sets( $rtwwdpd_cart_item ) {
		
		$rtwwdpd_product = wc_get_product( $rtwwdpd_cart_item['data']->get_id() );
		
		if ( empty( $rtwwdpd_product ) ) {
			return false;
		}
		
		$rtwwdpd_pricing_rule_sets = apply_filters( 'rtwwdpd_get_product_pricing_rule_sets', $this->rtwwdpd_get_product_meta( $rtwwdpd_product, '_pricing_rules' ), $rtwwdpd_product->get_id(), $this );
		
		$rtwwdpd_pricing_rule_sets = apply_filters( 'rtwwdpd_get_cart_item_pricing_rule_sets', $rtwwdpd_pricing_rule_sets, $rtwwdpd_cart_item );
		
		$rtwwdpd_sets              = array();
		if ( $rtwwdpd_pricing_rule_sets ) {
			foreach ( $rtwwdpd_pricing_rule_sets as $set_id => $set_data ) {
				$rtwwdpd_sets[ $set_id ] = new RTWWDPD_Adjustment_Set_Product( $set_id, $set_data );
			}
		}
		
		return $rtwwdpd_sets;
	}

	/**
	 * Function to get the product detail.
	 *
	 * @since    1.0.0
	 */
	public static function rtwwdpd_get_product_meta( $rtwwdpd_product, $key, $context = 'view' ) {
		if ( empty( $rtwwdpd_product ) ) {
			return false;
		}

		return get_post_meta( $rtwwdpd_product->get_id(), $key, true);
	}

}
