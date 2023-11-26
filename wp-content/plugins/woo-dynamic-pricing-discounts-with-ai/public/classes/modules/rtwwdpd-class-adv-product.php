<?php
/**
 * Class RTWWDPD_Advance_Product to calculate discount according to Product rule.
 *
 * @since    1.0.0
 */
class RTWWDPD_Advance_Product extends RTWWDPD_Advance_Base {
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
			self::$rtwwdpd_instance = new RTWWDPD_Advance_Product( 'advanced_product' );
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
		$cart_obj = $woocommerce->cart;
		
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
			
			if( is_array($rtwwdpd_setting_pri) && !empty($rtwwdpd_temp_cart) )
			{
				if($rtwwdpd_setting_pri['rtw_offer_select'] == 'rtw_all_mtch')
				{
					if(isset($rtwwdpd_setting_pri['pro_rule']) && $rtwwdpd_setting_pri['pro_rule']==1)
					{
						$rtwwdpd_get_pro_option = get_option('rtwwdpd_single_prod_rule');
						$a = date(1);
						
						$i = 0;
						$rtwwdpd_user = wp_get_current_user();
						$rtwwdpd_num_decimals = apply_filters( 'rtwwdpd_get_decimals', (int) get_option( 'woocommerce_price_num_decimals' ) );
						$rtwwdpd_no_oforders = wc_get_customer_order_count( get_current_user_id());
						$rtwwdpd_today_date = current_time('Y-m-d');
						$rtwwdpd_ordrtotal = wc_get_customer_total_spent(get_current_user_id());
						$set_id = 1;
						if( is_array($rtwwdpd_get_pro_option) && !empty($rtwwdpd_get_pro_option) )
						{
							$sabcd = 'fication_done';
							$rtwwdpd_verification_done = get_site_option( 'rtwbma_veri'.$sabcd, array() );
							
							if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) ) { 
								
								$active_dayss = get_site_option('rtwwdpd_discount_on_selected_days', 'no');

								foreach ($rtwwdpd_get_pro_option as $prod => $pro_rul) {
								
									if($active_dayss == 'yes')
									{
										$active_days = isset($pro_rul['rtwwwdpd_prod_day']) ? $pro_rul['rtwwwdpd_prod_day'] : array();
										$current_day = date('N');

										if(!in_array($current_day, $active_days))
										{
											continue;
										}
									}

									$max_quant = 0;
									$this_price = 0;
									$max_price = 0;
									$max_weight = 0;
									$total_weight = 0;
									
									if( isset($pro_rul['rtwwdpd_max']) && !empty($pro_rul['rtwwdpd_max']) )
									{
										
										$ai = 1;
										foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item )
										{
											$product_id = isset($pro_rul['product_id'])? $pro_rul['product_id'] : '';
											if($product_id == $cart_item['data']->get_id() || $product_id == $cart_item['product_id'])
											{
												if($ai == 1)
												{
													$this_price = $cart_item['data']->get_price();
													$ai++;
												}
												$max_price += $cart_item['quantity']*$cart_item['data']->get_price();
												$max_quant += $cart_item['quantity'];
												
												if( !empty($cart_item['data']->get_weight()) )
												{
													$max_weight += $cart_item['quantity']*$cart_item['data']->get_weight();
												}
											}
										}
										if( $max_quant > $pro_rul['rtwwdpd_max'] )
										{	
											// continue;
										}
									}	
									
									$all_ids = array();
									$total_quantities = array();
									$total_prices = array();
									$total_weightss = array();
									if($pro_rul['rtwwdpd_rule_on'] == 'rtwwdpd_multiple_products')
									{
										if($pro_rul['rtwwdpd_condition'] == 'rtwwdpd_and')
										{
											foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item )
											{
												if(!in_array($cart_item['data']->get_id(), $all_ids))
												{
													$all_ids[] = $cart_item['data']->get_id();
												}
												
												if(!empty($cart_item['data']->get_parent_id()) && !in_array($cart_item['data']->get_parent_id(), $all_ids))
												{
													$all_ids[] = $cart_item['data']->get_parent_id();
												}

												if(in_array($cart_item['data']->get_id(), $pro_rul['multiple_product_ids']))
												{
													if(!array_key_exists($cart_item['data']->get_id(), $total_quantities))
													{
														$total_quantities[$cart_item['data']->get_id()] = $cart_item['quantity'];
														
														$total_prices[$cart_item['data']->get_id()] = ( $cart_item['quantity'] * $cart_item['data']->get_price());
														
														$total_weightss[$cart_item['data']->get_id()] = ( (int)$cart_item['quantity'] * (int)$cart_item['data']->get_weight());
														
													}
													else
													{
														$total_quantities[$cart_item['data']->get_id()] = $total_quantities[$cart_item['data']->get_id()] + $cart_item['quantity'];
														
														$total_prices[$cart_item['data']->get_id()] = $total_prices[$cart_item['data']->get_id()] + ( $cart_item['quantity'] * $cart_item['data']->get_price());
														
														$total_weightss[$cart_item['data']->get_id()] = $total_weightss[$cart_item['data']->get_id()]+ ( $cart_item['quantity'] * $cart_item['data']->get_weight());
														
													}
												}

												if(in_array($cart_item['data']->get_parent_id(), $pro_rul['multiple_product_ids']))
												{
													if(array_key_exists($cart_item['data']->get_parent_id(), $total_quantities))
													{
														$total_quantities[$cart_item['data']->get_parent_id()] = $total_quantities[$cart_item['data']->get_parent_id()] + $cart_item['quantity'];

														$total_prices[$cart_item['data']->get_parent_id()] = $total_prices[$cart_item['data']->get_parent_id()] + ( $cart_item['quantity'] * $cart_item['data']->get_price());

														$total_weightss[$cart_item['data']->get_parent_id()] = $total_weightss[$cart_item['data']->get_parent_id()] + ( $cart_item['quantity'] * $cart_item['data']->get_weight());
														
													}else{
														$total_quantities[$cart_item['data']->get_parent_id()] = $cart_item['quantity'];

														$total_prices[$cart_item['data']->get_parent_id()] = ($cart_item['quantity']* $cart_item['data']->get_price() );
														
														$total_weightss[$cart_item['data']->get_parent_id()] = ($cart_item['quantity'] * $cart_item['data']->get_weight());

													}
												}
											}
											
											$reslt = array_diff($pro_rul['multiple_product_ids'], $all_ids);

											if(!empty($reslt))
											{
												continue;
											}
										}
									}
									if($pro_rul['rtwwdpd_single_from_date'] > $rtwwdpd_today_date || $pro_rul['rtwwdpd_single_to_date'] < $rtwwdpd_today_date)
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


									$rtw_curnt_dayname = date("N");
									$rtwwdpd_day_waise_rule = false;
									if(isset($pro_rul['rtwwdpd_enable_day']) && $pro_rul['rtwwdpd_enable_day'] == 'yes')
									{
										
										if(isset($pro_rul['rtwwdpd_select_day']) && !empty($pro_rul['rtwwdpd_select_day']))
										{
											if($pro_rul['rtwwdpd_select_day'] == $rtw_curnt_dayname)
											{
												$rtwwdpd_day_waise_rule = true;
											}
										}
										if($rtwwdpd_day_waise_rule == false)
										{
											
											continue;
										}
									}
									

									$rtwwdpd_restricted_mails = isset( $pro_rul['rtwwdpd_select_emails'] ) ? $pro_rul['rtwwdpd_select_emails'] : array();

									$rtwwdpd_cur_user_mail = get_current_user_id();
									
									if(in_array($rtwwdpd_cur_user_mail, $rtwwdpd_restricted_mails))
									{
										continue 1;
									}
								
									if(isset($pro_rul['rtwwdpd_min_orders']) && $pro_rul['rtwwdpd_min_orders'] > $rtwwdpd_no_oforders)
									{
										continue;
									}
									if(isset($pro_rul['rtwwdpd_min_spend']) && $pro_rul['rtwwdpd_min_spend'] > $rtwwdpd_ordrtotal)
									{
										continue;
									}
										
									foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item ){
										$rtwwdpd_original_price = $this->rtw_get_price_to_discount( $cart_item, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ), 'advanced_product', $set_id  );
										
										if ( $rtwwdpd_original_price ) {
											$rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $pro_rul['rtwwdpd_discount_value'], $pro_rul, $cart_item, $this );
											
											if($pro_rul['rtwwdpd_rule_on'] == 'rtwwdpd_products' || $pro_rul['rtwwdpd_rule_on'] == 'rtwwdpd_cart')
											{
											
												if($pro_rul['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
												{	
													
													if( $max_quant < $pro_rul['rtwwdpd_min'] )
													{	
														continue 1;
													}
													if( isset($pro_rul['rtwwdpd_max']) && $pro_rul['rtwwdpd_max'] != '' && $max_quant > $pro_rul['rtwwdpd_max'] )
													{	
														continue 1;
													}
												}
												elseif($pro_rul['rtwwdpd_check_for'] == 'rtwwdpd_price')
												{
													if( $max_price < $pro_rul['rtwwdpd_min'] )
													{
														continue 1;
													}
													$total_cost = ( $cart_item['data']->get_price() * $cart_item['quantity'] );
													if( isset($pro_rul['rtwwdpd_max']) && !empty($pro_rul['rtwwdpd_max']) && $total_cost > $pro_rul['rtwwdpd_max'] )
													{
														continue 1;
													}
												}
												elseif($pro_rul['rtwwdpd_check_for'] == 'rtwwdpd_weight')
												{
													
													if( ($cart_item['quantity']*$cart_item['data']->get_weight()) < $pro_rul['rtwwdpd_min'] )
													{
														continue 1;
													}
													if( isset($pro_rul['rtwwdpd_max']) && $pro_rul['rtwwdpd_max'] != '' && ($cart_item['quantity']*$cart_item['data']->get_weight()) > $pro_rul['rtwwdpd_max'] )
													{
														continue 1;
												
													}
												}
											}
											elseif($pro_rul['rtwwdpd_rule_on'] == 'rtwwdpd_multiple_products')
											{
												if($pro_rul['rtwwdpd_condition'] == 'rtwwdpd_and')
												{
												
													// $total_quantities = array();
													// $total_prices = array();
													// $total_weightss = array();
													if($pro_rul['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
													{
														$total_quant = 0;
														if(is_array($total_quantities) && !empty($total_quantities))
														{
															foreach ($total_quantities as $q => $qnt) {
																$total_quant += $qnt;
															}
														}
														if(isset($total_quant) && $total_quant < $pro_rul['rtwwdpd_min'] )
														{
															continue 1;
														}
													}
													elseif($pro_rul['rtwwdpd_check_for'] == 'rtwwdpd_price')
													{
														$total_prz = 0;
														if(is_array($total_prices) && !empty($total_prices))
														{
															foreach ($total_prices as $q => $pri) {
																$total_prz += $pri;
															}
														}

														if($total_prz < $pro_rul['rtwwdpd_min'] )
														{
															continue 1;
														}

														if( isset($pro_rul['rtwwdpd_max']) && !empty($pro_rul['rtwwdpd_max']) && $total_prz > $pro_rul['rtwwdpd_max'] )
														{
															continue 1;
														}
													}
													elseif($pro_rul['rtwwdpd_check_for'] == 'rtwwdpd_weight')
													{
														$total_weigh = 0;
														if(is_array($total_weightss) && !empty($total_weightss))
														{
															foreach ($total_weightss as $q => $we) {
																$total_weigh += $we;
															}
															
														}

														if( $total_weigh < $pro_rul['rtwwdpd_min'] )
														{
															continue 1;
														}
														if( isset($pro_rul['rtwwdpd_max']) && $pro_rul['rtwwdpd_max'] != '' && $total_weigh > $pro_rul['rtwwdpd_max'] )
														{
															continue 1;
														}
													}
												}else{
													if($pro_rul['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
													{
														if( $cart_item['quantity'] < $pro_rul['rtwwdpd_min'] )
														{
															continue 1;
														}
														if( isset($pro_rul['rtwwdpd_max']) && $pro_rul['rtwwdpd_max'] != '' && $cart_item['quantity'] > $pro_rul['rtwwdpd_max'] )
														{
															continue 1;
														}
													}
													elseif($pro_rul['rtwwdpd_check_for'] == 'rtwwdpd_price')
													{
														if($cart_item['data']->get_price() < $pro_rul['rtwwdpd_min'] )
														{
															continue 1;
														}
														$total_cost = ( $cart_item['data']->get_price() * $cart_item['quantity'] );
														if( isset($pro_rul['rtwwdpd_max']) && !empty($pro_rul['rtwwdpd_max']) && $total_cost > $pro_rul['rtwwdpd_max'] )
														{
															continue 1;
														}
													}
													elseif($pro_rul['rtwwdpd_check_for'] == 'rtwwdpd_weight')
													{
													
														if( $cart_item['data']->get_weight() < $pro_rul['rtwwdpd_min'] )
														{
															continue 1;
															
														}
														if( isset($pro_rul['rtwwdpd_max']) && $pro_rul['rtwwdpd_max'] != '' && $cart_item['data']->get_weight() > $pro_rul['rtwwdpd_max'] )
														{
															continue 1;
														}
													}
												}
											}
											
											if($pro_rul['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
											{
												$rtwwdpd_amount = $rtwwdpd_amount / 100;
												$rtwwdpd_dscnted_val = ( floatval( $rtwwdpd_amount ) * $rtwwdpd_original_price );
												
												if($rtwwdpd_dscnted_val > $pro_rul['rtwwdpd_max_discount'])
												{
													$rtwwdpd_dscnted_val = $pro_rul['rtwwdpd_max_discount'];
												}
												$rtwwdpd_price_adjusted = ( floatval( $rtwwdpd_original_price ) - $rtwwdpd_dscnted_val );
												$rtwwdpd_parent_id = 0;
												
												if( !empty(wc_get_product($cart_item['data']->get_parent_id()) ))
												{
													$rtwwdpd_parent_id = $cart_item['data']->get_parent_id();
												}
												
												if($pro_rul['rtwwdpd_rule_on'] == 'rtwwdpd_products' && isset($pro_rul['product_id']))
												{			
															
													if($pro_rul['product_id'] == $cart_item['data']->get_id() || $pro_rul['product_id'] == $cart_item['data']->get_parent_id())
													{
														if(isset($pro_rul['rtwwdpd_exclude_sale']))
														{
															if( !$cart_item['data']->is_on_sale() )
															{
																if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) 
																{
																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_product', $set_id );
																	$set_id++;
																}
															}
														}
														else
														{
															if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
																Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_product', $set_id );
																$set_id++;
															}
														}
													}
												}
												elseif($pro_rul['rtwwdpd_rule_on'] == 'rtwwdpd_multiple_products')
												{
													if(in_array( $cart_item['data']->get_id(), $pro_rul['multiple_product_ids'] ) || in_array( $cart_item['data']->get_parent_id(), $pro_rul['multiple_product_ids'] ))
													{
														if(isset($pro_rul['rtwwdpd_exclude_sale']))
														{
															if( !$cart_item['data']->is_on_sale() )
															{
																if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_product', $set_id );
																	$set_id++;
																}
															}
														}
														else{
															if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
																Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_product', $set_id );
																$set_id++;
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
																Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_product', $set_id );
																$set_id++;
															}
														}
													}
													else{
														if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
															Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_product', $set_id );
															$set_id++;
														}
													}
												}
											}
											elseif($pro_rul['rtwwdpd_discount_type'] == 'rtwwdpd_fixed_price')
											{
												if($rtwwdpd_amount > $pro_rul['rtwwdpd_max_discount'])
												{
													$rtwwdpd_amount = $pro_rul['rtwwdpd_max_discount'];
												}
												$rtwwdpd_amount = ( $rtwwdpd_amount / $cart_item['quantity'] );

												$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount );
												if($pro_rul['rtwwdpd_rule_on'] == 'rtwwdpd_products' && ( isset($pro_rul['product_id']) || $pro_rul['product_id'] == $cart_item['data']->get_parent_id() ))
												{												
													if($pro_rul['product_id'] == $cart_item['data']->get_id() || $pro_rul['product_id'] == $cart_item['product_id'])
													{
														if(isset($pro_rul['rtwwdpd_exclude_sale']))
														{
															if( !$cart_item['data']->is_on_sale() )
															{
																if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_product', $set_id );
																	$set_id++;
																}
															}
														}
														else{
															if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
																Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_product', $set_id );
																$set_id++;
															}
														}
													}
												}
												elseif($pro_rul['rtwwdpd_rule_on'] == 'rtwwdpd_multiple_products')
												{
													if(in_array( $cart_item['data']->get_id(), $pro_rul['multiple_product_ids'] ) || in_array( $cart_item['data']->get_parent_id(), $pro_rul['multiple_product_ids'] ))
													{
														if(isset($pro_rul['rtwwdpd_exclude_sale']))
														{
															if( !$cart_item['data']->is_on_sale() )
															{
																if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_product', $set_id );
																	$set_id++;
																}
															}
														}
														else{
															if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
																Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_product', $set_id );
																$set_id++;
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
																Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_product', $set_id );
																$set_id++;
															}
														}
													}
													else{
														if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
															Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_product', $set_id );
															$set_id++;
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

												$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount );
												if(isset($pro_rul['product_id']))
												{												
													if($pro_rul['product_id'] == $cart_item['data']->get_id() || $pro_rul['product_id'] == $cart_item['data']->get_parent_id())
													{
														if(isset($pro_rul['rtwwdpd_exclude_sale']))
														{
															if( !$cart_item['data']->is_on_sale() )
															{
																if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_product', $set_id );
																	$set_id++;
																}
															}
														}
														else{
															if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
																Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_product', $set_id );
																$set_id++;
															}
														}
													}
												}
												elseif($pro_rul['rtwwdpd_rule_on'] == 'rtwwdpd_multiple_products')
												{
													if(in_array( $cart_item['data']->get_id(), $pro_rul['multiple_product_ids'] ) || in_array( $cart_item['data']->get_parent_id(), $pro_rul['multiple_product_ids'] ))
													{
														if(isset($pro_rul['rtwwdpd_exclude_sale']))
														{
															if( !$cart_item['data']->is_on_sale() )
															{
																if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_product', $set_id );
																	$set_id++;
																}
															}
														}
														else{
															if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
																Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_product', $set_id );
																$set_id++;
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
																Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_product', $set_id );
																$set_id++;
															}
														}
													}
													else{
														if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
															Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_product', $set_id );
															$set_id++;
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
					if( isset( $rtwwdpd_setting_pri['pro_com_rule'] ) && $rtwwdpd_setting_pri['pro_com_rule'] == 1 )
					{
						$rtwwdpd_get_pro_option = get_option('rtwwdpd_combi_prod_rule');
						$i = 0;
						$rtwwdpd_user = wp_get_current_user();
						$rtwwdpd_num_decimals = apply_filters( 'rtwwdpd_get_decimals', (int) get_option( 'woocommerce_price_num_decimals' ) );
						$rtwwdpd_no_oforders = wc_get_customer_order_count( get_current_user_id());
						$rtwwdpd_today_date = current_time('Y-m-d');
						$rtwwdpd_ordrtotal = wc_get_customer_total_spent(get_current_user_id());

						$set_id = 1;
						if ( is_array($rtwwdpd_get_pro_option) && !empty($rtwwdpd_get_pro_option) ) {

							foreach ( $rtwwdpd_get_pro_option as $prod => $pro_rul ) {

								if($pro_rul['rtwwdpd_combi_from_date'] > $rtwwdpd_today_date || $pro_rul['rtwwdpd_combi_to_date'] < $rtwwdpd_today_date)
								{
									continue 1;
								}
								$rtwwdpd_user_role = $pro_rul['rtwwdpd_select_roles_com'] ;
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
									continue 1;
								}

								$rtwwdpd_restricted_mails = isset( $pro_rul['rtwwdpd_select_com_emails'] ) ? $pro_rul['rtwwdpd_select_com_emails'] : array();

								$rtwwdpd_cur_user_mail = get_current_user_id();
								
								if(in_array($rtwwdpd_cur_user_mail, $rtwwdpd_restricted_mails))
								{
									continue 1;
								}

								if(isset($pro_rul['rtwwdpd_combi_min_orders']) && $pro_rul['rtwwdpd_combi_min_orders'] > $rtwwdpd_no_oforders)
								{
									continue 1;
								}
								if(isset($pro_rul['rtwwdpd_combi_min_spend']) && $pro_rul['rtwwdpd_combi_min_spend'] > $rtwwdpd_ordrtotal)
								{
									continue 1;
								}

								$both_quantity = 0;
								$both_ids 	=	array();

								$ids_quantity_in_cart = array();
								$ids_quantity_in_rule = array();
								if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
									foreach ( $rtwwdpd_temp_cart as $cart_item_k => $valid ) {
										foreach($pro_rul['product_id'] as $na => $kid )
										{ 
											if($kid == $valid['data']->get_parent_id() || $kid == $valid['data']->get_id())
											{
												if( $valid['data']->get_parent_id() != 0 )
												{
													$both_ids[] = $valid['data']->get_parent_id();
												}
												else{
													$both_ids[] = $valid['data']->get_id();
												}
												
												$both_quantity += $valid['quantity'];
												$ids_quantity_in_cart[$kid] = $valid['quantity'];
											}
										}
									}
								}

								$givn_quanty = 0;
								foreach ($pro_rul['combi_quant'] as $quants) {
									$givn_quanty += $quants;
								}
								
								$rslt = array();
			
								$rslt = array_diff($pro_rul['product_id'], $both_ids );
								if( !empty($rslt) )
								{
									continue 1;
								}

								if( $givn_quanty > $both_quantity )
								{
									continue 1;
								}

								///////////////////////////////////////
								foreach($pro_rul['product_id'] as $na => $kid )
								{
									$ids_quantity_in_rule[$kid] = $pro_rul['combi_quant'][$na];
								} 

								foreach($ids_quantity_in_rule as $na => $kid )
								{
									if($ids_quantity_in_cart[$na] < $kid )
									{
										continue 2;
									}
								} 

								///////////////////////////////////////
								$sabcd = 'fication_done';
								$rtwwdpd_verification_done = get_site_option( 'rtwbma_veri'.$sabcd, array() );
								if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) ) { 
									foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item ) {

										$rtwwdpd_original_price = $this->rtw_get_price_to_discount( $cart_item, $cart_item_key, apply_filters( 'rtwwdpd_pro_com_totals', false ), 'advanced_product_c', $set_id );

										if ( $rtwwdpd_original_price ) {
											$rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $pro_rul['rtwwdpd_combi_discount_value'], $pro_rul, $cart_item, $this );

											if($pro_rul['rtwwdpd_combi_discount_type'] == 'rtwwdpd_discount_percentage')
											{
												$rtwwdpd_amount = $rtwwdpd_amount / 100;
												$rtwwdpd_dscnted_val = ( floatval( $rtwwdpd_amount ) * $rtwwdpd_original_price );
												if($rtwwdpd_dscnted_val > $pro_rul['rtwwdpd_combi_max_discount'])
												{
													$rtwwdpd_dscnted_val = $pro_rul['rtwwdpd_combi_max_discount'];
												}

												$rtwwdpd_price_adjusted = ( floatval( $rtwwdpd_original_price ) - $rtwwdpd_dscnted_val );

												foreach ($pro_rul['product_id'] as $k => $v) {

													if($v == $cart_item['data']->get_id() || $v == $cart_item['data']->get_parent_id())
													{
														if(isset($pro_rul['rtwwdpd_combi_exclude_sale']))
														{
															if( !$cart_item['data']->is_on_sale() )
															{
																if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_product_c', $set_id );
																	$set_id++;
																	
																}
															}
														}
														else{

															if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
																Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_product_c', $set_id );
																$set_id++;
																
															}
														}
													}
												}
											}
											elseif($pro_rul['rtwwdpd_combi_discount_type'] == 'rtwwdpd_flat_discount_amount')
											{
												if($rtwwdpd_amount > $pro_rul['rtwwdpd_combi_max_discount'])
												{
													$rtwwdpd_amount = $pro_rul['rtwwdpd_combi_max_discount'];
												}
												$rtwwdpd_amount = ( $rtwwdpd_amount / $cart_item['quantity'] );
												$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount );

												if(isset($pro_rul['product_id']) && is_array($pro_rul['product_id']))
												{
													foreach ($pro_rul['product_id'] as $k => $v) {
														if($v == $cart_item['data']->get_id() || $v == $cart_item['data']->get_parent_id())
														{
															if(isset($pro_rul['rtwwdpd_combi_exclude_sale']))
															{
																if( !$cart_item['data']->is_on_sale() )
																{
																	if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_product_c', $set_id );
																		$set_id++;
																		
																	}
																}
															}
															else{
																if ( $rtwwdpd_price_adjusted !== false && floatval( $rtwwdpd_original_price ) != floatval( $rtwwdpd_price_adjusted ) ) {
																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_product_c', $set_id );
																	$set_id++;
																	
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
		$sabcd = 'fication_done';
		$rtwwdpd_verification_done = get_site_option( 'rtwbma_veri'.$sabcd, array() );
		if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) ) { 

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
