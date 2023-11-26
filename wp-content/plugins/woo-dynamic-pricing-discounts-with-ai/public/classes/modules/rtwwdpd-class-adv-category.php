<?php
/**
 * Class RTWWDPD_Advance_Category to calculate discount according to Product Category rule.
 *
 * @since    1.0.0
 */
class RTWWDPD_Advance_Category extends RTWWDPD_Advance_Base {

	/**
	 * variable to set instance of category module.
	 *
	 * @since    1.0.0
	 */
	private static $rtwwdpd_instance;

	/**
	 * function to set instance of category module.
	 *
	 * @since    1.0.0
	 */
	public static function rtwwdpd_instance() {
		if ( self::$rtwwdpd_instance == null ) {
			self::$rtwwdpd_instance = new RTWWDPD_Advance_Category( 'advanced_category' );
		}

		return self::$rtwwdpd_instance;
	}

	/**
	 * construct function.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $rtwwdpd_module_id ) {
		parent::__construct( $rtwwdpd_module_id );
		
	}

	/**
	 * Function to apply discount on cart items.
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
		$rtwwdpd_setting_pri = get_option('rtwwdpd_setting_priority');
		
		if( is_array( $rtwwdpd_setting_pri ) && !empty($rtwwdpd_setting_pri) ) 
		{
			if($rtwwdpd_setting_pri['rtw_offer_select'] == 'rtw_all_mtch')
			{
				$i = 0;
				$rtwwdpd_user = wp_get_current_user();
				$rtwwdpd_num_decimals = apply_filters( 'rtwwdpd_get_decimals', (int) get_option( 'woocommerce_price_num_decimals' ) );
				$rtwwdpd_no_oforders = wc_get_customer_order_count( get_current_user_id());
				$rtwwdpd_today_date = current_time('Y-m-d');
				$rtwwdpd_ordrtotal = wc_get_customer_total_spent(get_current_user_id());
				$set_id = 1;
				if( isset($rtwwdpd_setting_pri['cat_rule']) && $rtwwdpd_setting_pri['cat_rule']==1 )
				{
					$rtwwdpd_get_cat_option = get_option('rtwwdpd_single_cat_rule');		
					if(isset($rtwwdpd_get_cat_option) && !empty($rtwwdpd_get_cat_option))
					{
						$active_dayss = get_site_option('rtwwdpd_discount_on_selected_days', 'no');
						foreach ($rtwwdpd_get_cat_option as $catt => $pro_rul)
						{
							if($active_dayss == 'yes')
							{
								$active_days = isset($pro_rul['rtwwwdpd_cat_day']) ? $pro_rul['rtwwwdpd_cat_day'] : array();
								$current_day = date('N');

								if(!in_array($current_day, $active_days))
								{
									continue;
								}
							}

							if(isset($pro_rul['rtwwdpd_category_on_update']) && $pro_rul['rtwwdpd_category_on_update']== 'rtwwdpd_category_update')
							{
								
								$rtwwdpd_total_weight = 0;
								$rtwwdpd_total_price = 0;
								$rtwwdpd_total_quantity = 0;
								foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item ) 
								{
									if( isset($cart_item['variation_id']) && !empty($cart_item['variation_id']) )
									{
										$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_parent_id(), 'product_cat', array( 'fields' => 'ids' ) );
									}
									else
									{
										$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) );	
									}
									if( is_array($rtwwdpd_catids) && !empty($rtwwdpd_catids) && in_array($pro_rul['category_id'], $rtwwdpd_catids))
									{
										if( $cart_item['data']->get_weight() != '')
										{
											$rtwwdpd_total_weight += $cart_item['quantity'] * $cart_item['data']->get_weight();
										}
										$rtwwdpd_total_price += $cart_item['quantity'] * $cart_item['data']->get_price();
								
										$rtwwdpd_total_quantity += $cart_item['quantity'];
									}
								}
								$rtwwdpd_matched = true;
								if($pro_rul['rtwwdpd_from_date'] > $rtwwdpd_today_date || $pro_rul['rtwwdpd_to_date'] < $rtwwdpd_today_date)
								{
									continue 1;
								}
								$rtwwdpd_user_role = $pro_rul['rtwwdpd_select_roles'] ;
								$rtwwdpd_role_matched = false;

								if(isset($rtwwdpd_user_role) && is_array($rtwwdpd_user_role) && !empty($rtwwdpd_user_role))
								{
									foreach ($rtwwdpd_user_role as $rol => $role) 
									{
										if($role == 'all')
										{
											$rtwwdpd_role_matched = true;
										}
										if($role == 'guest')
										{
											if(!is_user_logged_in())
											{
												$rtwwdpd_role_matched = true;
											}
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
								$rtwwdpd_restricted_mails = isset( $pro_rul['rtwwdpd_select_emails'] ) ? $pro_rul['rtwwdpd_select_emails'] : array();

								$rtwwdpd_cur_user_mail = get_current_user_id();
								
								if(in_array($rtwwdpd_cur_user_mail, $rtwwdpd_restricted_mails))
								{
									continue 1;
								}
								if(isset($pro_rul['rtwwdpd_min_orders']) && $pro_rul['rtwwdpd_min_orders'] > $rtwwdpd_no_oforders)
								{
									continue 1;
								}
								if(isset($pro_rul['rtwwdpd_min_spend']) && $pro_rul['rtwwdpd_min_spend'] > $rtwwdpd_ordrtotal)
								{
									continue 1;
								}
								$sabcd = 'fication_done';
								$rtwwdpd_verification_done = get_site_option( 'rtwbma_veri'.$sabcd, array() );
								if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) ) { 
									if( is_array($rtwwdpd_temp_cart) && !empty($rtwwdpd_temp_cart) )
									{
										foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item ) 
										{	
											if( isset( $pro_rul['rtw_exe_product_tags'] ) && is_array( $pro_rul['rtw_exe_product_tags'] ) && !empty( $pro_rul['rtw_exe_product_tags'] ) )
											{
												$rtwwdpd_tags = array();
												if( isset($cart_item['variation_id']) && !empty($cart_item['variation_id']) )
												{
													$rtwwdpd_tags = wp_get_post_terms( $cart_item['data']->get_parent_id(), 'product_tag', array( 'fields' => 'ids' ) );
												}else{
													$rtwwdpd_tags = $cart_item['data']->get_tag_ids();
												}

												$rtw_matched = array_intersect( $pro_rul['rtw_exe_product_tags'], $rtwwdpd_tags);

												if( !empty( $rtw_matched ) )
												{
													continue 1;
												}
											}

											
											if(isset($pro_rul['product_exe_id']) && is_array($pro_rul['product_exe_id']))
											{
												if( in_array($cart_item['data']->get_id(), $pro_rul['product_exe_id'] ) )
												{
													continue 1;
												}
											}

											$rtwwdpd_category_ids = $cart_item['data']->get_category_ids();
											if( is_array($rtwwdpd_category_ids) && !empty($rtwwdpd_category_ids) )
											{
												foreach ($rtwwdpd_category_ids as $key => $value) {
													$rtwwdpd_category_id = $value;
												}
											}
											
											if($pro_rul['rtwwdpd_check_for_cat'] == 'rtwwdpd_quantity')
											{	
												$rtwwdpd_on_single_or_whole = 'whole';
												$rtwwdpd_on_single_or_whole = apply_filters('rtwwdpd_on_single_or_whole', $rtwwdpd_on_single_or_whole);
												
												if($rtwwdpd_on_single_or_whole == 'whole')
												{
													if($rtwwdpd_total_quantity < $pro_rul['rtwwdpd_min_cat'])
													{	
														continue 1;
													}
												}
												elseif($rtwwdpd_on_single_or_whole == 'single')
												{	
													if($cart_item['quantity'] < $pro_rul['rtwwdpd_min_cat'])
													{
														continue 1;
													}
												}
												if(isset($pro_rul['rtwwdpd_max_cat']) && $pro_rul['rtwwdpd_max_cat'] != '')
												{
													if($rtwwdpd_on_single_or_whole == 'whole')
													{
														if($pro_rul['rtwwdpd_max_cat'] < $rtwwdpd_total_quantity)
														{
															continue 1;
														}
													}elseif($rtwwdpd_on_single_or_whole == 'single')
													{	
														if($pro_rul['rtwwdpd_max_cat'] < $cart_item['quantity'])
														{
															continue 1;
														}
													}
												}
											}
											elseif($pro_rul['rtwwdpd_check_for_cat'] == 'rtwwdpd_price')
											{
												
												if($rtwwdpd_total_price < $pro_rul['rtwwdpd_min_cat'])
												{
													continue 1;
												}
												if(isset($pro_rul['rtwwdpd_max_cat']) && $pro_rul['rtwwdpd_max_cat'] != '')
												{
													if($pro_rul['rtwwdpd_max_cat'] < $rtwwdpd_total_price)
													{
														continue 1;
													}
												}
											}
											else
											{ 	
												if($rtwwdpd_total_weight < $pro_rul['rtwwdpd_min_cat'])
												{
													continue 1;
												}
												if(isset($pro_rul['rtwwdpd_max_cat']) && $pro_rul['rtwwdpd_max_cat'] != '')
												{
													if($pro_rul['rtwwdpd_max_cat'] < $rtwwdpd_total_weight)
													{
														continue 1;
													}
												}
											}
											$rtwwdpd_original_price = $this->rtw_get_price_to_discount( $cart_item, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ),'advanced_category'.$catt );
											if ( $rtwwdpd_original_price ) 
											{
												$rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $pro_rul['rtwwdpd_dscnt_cat_val'], $pro_rul, $cart_item, $this );
												$rtwwdpd_catids = '';
												if( isset($cart_item['variation_id']) && !empty($cart_item['variation_id']) )
												{
													$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_parent_id(), 'product_cat', array( 'fields' => 'ids' ) );
													
												}
												else
												{
													$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) );
												
												}
											
												if( isset( $pro_rul['category_id'] ) && in_array( $pro_rul['category_id'], $rtwwdpd_catids ) )
												{
													if( $pro_rul['rtwwdpd_dscnt_cat_type'] == 'rtwwdpd_discount_percentage' )
													{
														
														$rtwwdpd_amount = $rtwwdpd_amount / 100;
														
														$rtwwdpd_dscnted_val = ( floatval( $rtwwdpd_amount ) * $rtwwdpd_original_price );
														if($rtwwdpd_dscnted_val > $pro_rul['rtwwdpd_max_discount'])
														{
											
															$rtwwdpd_dscnted_val = $pro_rul['rtwwdpd_max_discount'];
															
														}
														$rtwwdpd_price_adjusted = ( floatval( $rtwwdpd_original_price ) - $rtwwdpd_dscnted_val );


														if(isset($pro_rul['category_id']))
														{	
																if(isset($pro_rul['rtwwdpd_exclude_sale']))
																{
																	if( !$cart_item['data']->is_on_sale() )
																	{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_category', $set_id );
																	}
																}
																else
																{
																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_category', $set_id );
																}
														}
													}
													elseif( $pro_rul['rtwwdpd_dscnt_cat_type'] == 'rtwwdpd_fixed_price' )
													{
														if( $rtwwdpd_amount > $pro_rul['rtwwdpd_max_discount'] )
														{
															$rtwwdpd_amount = $pro_rul['rtwwdpd_max_discount'];
														}
														$rtwwdpd_amount = ( $rtwwdpd_amount / $rtwwdpd_total_quantity );
														$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount );
														if(isset($pro_rul['rtwwdpd_exclude_sale']))
														{
															if( !$cart_item['data']->is_on_sale() )
															{
																Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_category', $set_id );
															}
														}
														else{
															Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_category', $set_id );
														}
													}
													else
													{
														if(isset($pro_rul['rtwwdpd_max_discount']) && !empty($pro_rul['rtwwdpd_max_discount']) && $rtwwdpd_amount > $pro_rul['rtwwdpd_max_discount'] )
														{
															$rtwwdpd_amount = $pro_rul['rtwwdpd_max_discount'];
											
														}
														$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount );
														if(isset($pro_rul['rtwwdpd_exclude_sale']))
														{
															if( !$cart_item['data']->is_on_sale() )
															{
																Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_category', $set_id );
															}
														}
														else{
															Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_category', $set_id );
														}
													}
												}
											}
										}
									}
								}
					    	}
						/////////////////////// start  add multiple category rule ////////////
							else
							{
								if(isset($pro_rul['rtwwdpd_category_on_update']) && $pro_rul['rtwwdpd_category_on_update']== 'rtwwdpd_multiple_cat_update')
								{
									$multi_cat_quant=array();
									$multi_cat_price=array();
									$multi_cat_weight=array();
									foreach($pro_rul['multiple_cat_ids'] as $mul_key=>$mul_val)
									{
										foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item ) 
										{						
											if( isset($cart_item['variation_id']) && !empty($cart_item['variation_id']) )
											{
												$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_parent_id(), 'product_cat', array( 'fields' => 'ids' ) );
											}
											else
											{
												$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) );
											}
											
											if( is_array($rtwwdpd_catids) && !empty($rtwwdpd_catids) && array_intersect($pro_rul['multiple_cat_ids'] , $rtwwdpd_catids))
											{
												if( $cart_item['data']->get_weight() != '')
												{
													if(in_array($mul_val,$rtwwdpd_catids))
													{
														if(isset($multi_cat_weight[$mul_val]))
														{
															$multi_cat_weight[$mul_val] += $cart_item['quantity'] * $cart_item['data']->get_weight();
														}
														else
														{
															$multi_cat_weight[$mul_val] = $cart_item['quantity'] * $cart_item['data']->get_weight();
														}
													}
												}
											
												if(in_array($mul_val,$rtwwdpd_catids))
												{
													if(isset($multi_cat_price[$mul_val]))
													{
														$multi_cat_price[$mul_val] += ($cart_item['quantity'] * $cart_item['data']->get_price());
													}
													else
													{
														$multi_cat_price[$mul_val] = $cart_item['quantity'] * $cart_item['data']->get_price();
													}
												}
												if(in_array($mul_val,$rtwwdpd_catids))
												{
													if(isset($multi_cat_quant[$mul_val]))
													{
														$multi_cat_quant[$mul_val] += $cart_item['quantity'];
													}
													else
													{
														$multi_cat_quant[$mul_val] = $cart_item['quantity'];
													}
												}
												
											}	
										}
										
										$rtwwdpd_matched = true;
										
										if($pro_rul['rtwwdpd_from_date'] > $rtwwdpd_today_date || $pro_rul['rtwwdpd_to_date'] < $rtwwdpd_today_date)
										{
											continue 1;
										}
										$rtwwdpd_user_role = $pro_rul['rtwwdpd_select_roles'] ;
										$rtwwdpd_role_matched = false;
										if(isset($rtwwdpd_user_role) && is_array($rtwwdpd_user_role) && !empty($rtwwdpd_user_role))
										{
											foreach ($rtwwdpd_user_role as $rol => $role) 
											{
												if($role == 'all'){
													$rtwwdpd_role_matched = true;
												}
												if($role == 'guest')
												{
													if(!is_user_logged_in())
													{
														$rtwwdpd_role_matched = true;
													}
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
						
										$rtwwdpd_restricted_mails = isset( $pro_rul['rtwwdpd_select_emails'] ) ? $pro_rul['rtwwdpd_select_emails'] : array();

										$rtwwdpd_cur_user_mail = get_current_user_id();
										
										if(in_array($rtwwdpd_cur_user_mail, $rtwwdpd_restricted_mails))
										{
											continue 1;
										}
										if(isset($pro_rul['rtwwdpd_min_orders']) && $pro_rul['rtwwdpd_min_orders'] > $rtwwdpd_no_oforders)
										{
											continue 1;
										}
										if(isset($pro_rul['rtwwdpd_min_spend']) && $pro_rul['rtwwdpd_min_spend'] > $rtwwdpd_ordrtotal)
										{
											continue 1;
										}
										$sabcd = 'fication_done';
										$rtwwdpd_verification_done = get_site_option( 'rtwbma_veri'.$sabcd, array() );
										if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) ) { 
											if( is_array($rtwwdpd_temp_cart) && !empty($rtwwdpd_temp_cart) )
											{
												foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item ) {
													
													if( isset( $pro_rul['rtw_exe_product_tags'] ) && is_array( $pro_rul['rtw_exe_product_tags'] ) && !empty( $pro_rul['rtw_exe_product_tags'] ) )
													{
														$rtwwdpd_tags = array();
														if( isset($cart_item['variation_id']) && !empty($cart_item['variation_id']) )
														{
															$rtwwdpd_tags = wp_get_post_terms( $cart_item['data']->get_parent_id(), 'product_tag', array( 'fields' => 'ids' ) );
														}else{
															$rtwwdpd_tags = $cart_item['data']->get_tag_ids();
														}

														$rtw_matched = array_intersect( $pro_rul['rtw_exe_product_tags'], $rtwwdpd_tags);

														if( !empty( $rtw_matched ) )
														{
															continue 1;
														}
													}
													if(isset($pro_rul['product_exe_id']) && is_array($pro_rul['product_exe_id']))
													{
														if( in_array($cart_item['data']->get_id(), $pro_rul['product_exe_id'] ) )
														{
															continue 1;
														}
													}

													$rtwwdpd_category_ids = $cart_item['data']->get_category_ids();

													if( is_array($rtwwdpd_category_ids) && !empty($rtwwdpd_category_ids) )
													{
														foreach ($rtwwdpd_category_ids as $key => $value) {
															$rtwwdpd_category_id = $value;
														}
													}
													if($pro_rul['rtwwdpd_check_for_cat'] == 'rtwwdpd_quantity')
													{
														
														$rtwwdpd_on_single_or_whole = 'whole';
														$rtwwdpd_on_single_or_whole = apply_filters('rtwwdpd_on_single_or_whole', $rtwwdpd_on_single_or_whole);
														
														if($rtwwdpd_on_single_or_whole == 'whole')
														{
															if(isset($pro_rul['rtwwdpd_min_cat']) && !empty($pro_rul['rtwwdpd_min_cat']))
															{
																if($multi_cat_quant[$mul_val] < $pro_rul['rtwwdpd_min_cat'])
																{
																	continue 1;
																}
															}
														}
														elseif($rtwwdpd_on_single_or_whole == 'single')
														{
															if($cart_item['quantity'] < $pro_rul['rtwwdpd_min_cat'])
															{
																continue 1;
															}
														}

														if(isset($pro_rul['rtwwdpd_max_cat']) && $pro_rul['rtwwdpd_max_cat'] != '')
														{
															if($rtwwdpd_on_single_or_whole == 'whole')
															{
																if($pro_rul['rtwwdpd_max_cat'] < $multi_cat_quant[$mul_val])
																{
																	continue 1;
																}
															}elseif($rtwwdpd_on_single_or_whole == 'single')
															{	
																if($pro_rul['rtwwdpd_max_cat'] < $cart_item['quantity'])
																{
																	continue 1;
																}
															}
														}
													}
													elseif($pro_rul['rtwwdpd_check_for_cat'] == 'rtwwdpd_price')
													{
														
														if(isset($multi_cat_price[$mul_val]) && $multi_cat_price[$mul_val] < $pro_rul['rtwwdpd_min_cat'])
														{
															continue 1;
														}
														if(isset($pro_rul['rtwwdpd_max_cat']) && $pro_rul['rtwwdpd_max_cat'] != '')
														{
															if( isset($multi_cat_price[$mul_val]) && $pro_rul['rtwwdpd_max_cat'] < $multi_cat_price[$mul_val])
															{
																continue 1;
															}
														}
													}
													else{
														if(isset($multi_cat_weight[$mul_val]) && $multi_cat_weight[$mul_val] < $pro_rul['rtwwdpd_min_cat'])
														{
															continue 1;
														}
														if(isset($pro_rul['rtwwdpd_max_cat']) && $pro_rul['rtwwdpd_max_cat'] != '')
														{
															if(isset($multi_cat_weight[$mul_val]) && $pro_rul['rtwwdpd_max_cat'] < $multi_cat_weight[$mul_val])
															{
																continue 1;
															}
														}
													}

													$rtwwdpd_original_price = $this->rtw_get_price_to_discount( $cart_item, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ),'advanced_category'.$catt );
												
													if ( $rtwwdpd_original_price ) {
														$rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $pro_rul['rtwwdpd_dscnt_cat_val'], $pro_rul, $cart_item, $this );
													
														$rtwwdpd_catids = '';
														if( isset($cart_item['variation_id']) && !empty($cart_item['variation_id']) )
														{
															$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_parent_id(), 'product_cat', array( 'fields' => 'ids' ) );
															
														}
														else
														{
															$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) );
															
														}
														
														if( isset($mul_val) && in_array( $mul_val, $rtwwdpd_catids ) )
														{
															if( $pro_rul['rtwwdpd_dscnt_cat_type'] == 'rtwwdpd_discount_percentage' )
															{
																$rtwwdpd_amount = $rtwwdpd_amount / 100;
																$rtwwdpd_dscnted_val = ( floatval( $rtwwdpd_amount ) * $rtwwdpd_original_price );
															
																if($rtwwdpd_dscnted_val > $pro_rul['rtwwdpd_max_discount'])
																{								
																$rtwwdpd_dscnted_val = $pro_rul['rtwwdpd_max_discount'];		
																}
																$rtwwdpd_price_adjusted = ( floatval( $rtwwdpd_original_price ) - $rtwwdpd_dscnted_val );
																if(isset($mul_val))
																{	
																		if(isset($pro_rul['rtwwdpd_exclude_sale']))
																		{
																			if( !$cart_item['data']->is_on_sale() )
																			{
																				Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_category', $set_id );
																			}
																		}
																		else{
																			Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_category', $set_id );
																		}
																}
															}
															elseif( $pro_rul['rtwwdpd_dscnt_cat_type'] == 'rtwwdpd_fixed_price' )
															{
																if( $rtwwdpd_amount > $pro_rul['rtwwdpd_max_discount'] )
																{
																	$rtwwdpd_amount = $pro_rul['rtwwdpd_max_discount'];
																}
																$rtwwdpd_amount = ( $rtwwdpd_amount / $multi_cat_quant);
																$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount );
																if(isset($pro_rul['rtwwdpd_exclude_sale']))
																{
																	if( !$cart_item['data']->is_on_sale() )
																	{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_category', $set_id );
																	}
																}
																else{
																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_category', $set_id );
																}
															}
															else
															{
																if(isset($pro_rul['rtwwdpd_max_discount']) && !empty($pro_rul['rtwwdpd_max_discount']) && $rtwwdpd_amount > $pro_rul['rtwwdpd_max_discount'] )
																{
																	$rtwwdpd_amount = $pro_rul['rtwwdpd_max_discount'];
													
																}
																$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount );
																if(isset($pro_rul['rtwwdpd_exclude_sale']))
																{
																	if( !$cart_item['data']->is_on_sale() )
																	{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_category', $set_id );
																	}
																}
																else{
																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_category', $set_id );
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
			/////////////////////// End  add multiple category rule ////////////

						}
					}
				}
				if(isset($rtwwdpd_setting_pri['cat_com_rule']) && $rtwwdpd_setting_pri['cat_com_rule']==1)
				{
					$rtwwdpd_get_cat_option = get_option('rtwwdpd_combi_cat_rule');
					
					$rtwwdpd_cat_idss = array();
					$rtwwdpd_temp_cat_ids = array();
					if( isset( $rtwwdpd_get_cat_option ) && !empty($rtwwdpd_get_cat_option ) )
					{

						foreach ( $rtwwdpd_get_cat_option as $catt => $pro_rul ) {
							$rtwwdpd_total_weight = 0;
							$rtwwdpd_total_price = 0;
							$rtwwdpd_total_quantity = 0;
							$rtwwdpd_total_quant_in_rul = 0;

							if( is_array($pro_rul['category_id']) && !empty($pro_rul['category_id']) )
							{
								foreach($pro_rul['category_id'] as $cati => $catid)
								{
									$rtwwdpd_cat_idss[] = $catid;
									$rtwwdpd_total_quant_in_rul += $pro_rul['combi_quant'][$cati];
								}
							}
							
							if( is_array($rtwwdpd_temp_cart) && !empty($rtwwdpd_temp_cart) )
							{
								foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item )
								{
									foreach ((wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) )) as $key => $value) {
										$rtwwdpd_temp_cat_ids[] = $value;
									}
								}

								foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item )
								{
									$arr = wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) ); 
                          
									if( array_intersect( $arr, $rtwwdpd_cat_idss ) )
									{
										$rtwwdpd_total_quantity += $cart_item['quantity'];
									}
								}
							}

							$rtwwdpd_result = array_diff( $rtwwdpd_cat_idss, $rtwwdpd_temp_cat_ids );
							
							if( !empty ($rtwwdpd_result ) ){
								continue 1;
							}

							$rtwwdpd_matched = true;
							if( $pro_rul['rtwwdpd_combi_from_date'] > $rtwwdpd_today_date || $pro_rul['rtwwdpd_combi_to_date'] < $rtwwdpd_today_date )
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
							
							if( $rtwwdpd_role_matched == false)
							{
								continue 1;
							}

							$rtwwdpd_restricted_mails = isset( $pro_rul['rtwwdpd_select_com_emails'] ) ? $pro_rul['rtwwdpd_select_com_emails'] : array();

							$rtwwdpd_cur_user_mail = get_current_user_id();
							
							if(in_array($rtwwdpd_cur_user_mail, $rtwwdpd_restricted_mails))
							{
								continue 1;
							}

							if( isset($pro_rul['rtwwdpd_min_orders']) && $pro_rul['rtwwdpd_min_orders'] > $rtwwdpd_no_oforders)
							{
								continue 1;
							}
							if(isset($pro_rul['rtwwdpd_min_spend']) && $pro_rul['rtwwdpd_min_spend'] > $rtwwdpd_ordrtotal)
							{
								continue 1;
							}
							$sabcd = 'fication_done';
							$rtwwdpd_verification_done = get_site_option( 'rtwbma_veri'.$sabcd, array() );
							if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) ) { 
								if( is_array( $rtwwdpd_temp_cart ) && !empty( $rtwwdpd_temp_cart ) )
								{
									foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item ) {

										if( isset( $pro_rul['rtw_exe_product_tags'] ) && is_array( $pro_rul['rtw_exe_product_tags'] ) && !empty( $pro_rul['rtw_exe_product_tags'] ) )
										{
											$rtw_matched = array_intersect( $pro_rul['rtw_exe_product_tags'], $cart_item['data']->get_tag_ids());

											if( !empty( $rtw_matched ) )
											{
												continue 1;
											}
										}

										$rtwwdpd_category_id = wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) );

										$rtwwdpd_total_price = $cart_item['quantity'] * $cart_item['data']->get_price();

										$rtwwdpd_original_price = $this->rtw_get_price_to_discount( $cart_item, $cart_item_key, apply_filters( 'rtwwdpd_category_com_totals', false ),'advanced_category_c'.$catt );

										if ( $rtwwdpd_original_price ) {
											$rtwwdpd_amount = apply_filters( 'rtwwdpd_cat_com_amount', $pro_rul['rtwwdpd_discount_value'], $pro_rul, $cart_item, $this );
					
											if( $rtwwdpd_total_quant_in_rul <= $rtwwdpd_total_quantity )
											{
												if($pro_rul['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
												{
													$rtwwdpd_amount = $rtwwdpd_amount / 100;
													$rtwwdpd_dscnted_val = ( floatval( $rtwwdpd_amount ) * $rtwwdpd_original_price );
													if( $rtwwdpd_dscnted_val > $pro_rul['rtwwdpd_max_discount'] )
													{
														$rtwwdpd_dscnted_val = $pro_rul['rtwwdpd_max_discount'];
													}
													$rtwwdpd_price_adjusted = ( floatval( $rtwwdpd_original_price ) - $rtwwdpd_dscnted_val );


													if(isset($pro_rul['category_id']))
													{	
														$match = array_intersect($pro_rul['category_id'], $rtwwdpd_category_id);

														if( !empty( $match ) )
														{	
															if(isset($pro_rul['rtwwdpd_exclude_sale']))
															{
																if( !$cart_item['data']->is_on_sale() )
																{
																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_category_c', $set_id );
																}
															}
															else{
																Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_category_c', $set_id );
															}
														}
													}
												}
												elseif($pro_rul['rtwwdpd_discount_type'] == 'rtwwdpd_fixed_price')
												{
													// if($rtwwdpd_amount > $pro_rul['rtwwdpd_max_discount'])
													// {
													// 	$rtwwdpd_amount = $pro_rul['rtwwdpd_max_discount'];
													// }
													$rtwwdpd_amount = ( $rtwwdpd_amount / $rtwwdpd_total_quantity );
													$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount );

													if(isset($pro_rul['category_id']))
													{
														$match = array_intersect($pro_rul['category_id'], $rtwwdpd_category_id);

														if( !empty( $match ) )
														{
															if(isset($pro_rul['rtwwdpd_exclude_sale']))
															{
																if( !$cart_item['data']->is_on_sale() )
																{
																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_category_c', $set_id );
																}
															}
															else{
																Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_category_c', $set_id );
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

													if(isset($pro_rul['category_id']))
													{
														$match = array_intersect($pro_rul['category_id'], $rtwwdpd_category_id);

														if( !empty( $match ) )
														{
															if(isset($pro_rul['rtwwdpd_exclude_sale']))
															{
																if( !$cart_item['data']->is_on_sale() )
																{
																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_category_c', $set_id );
																}
															}
															else{
																Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_category_c', $set_id );
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