<?php
/**
 * Class RTWWDPD_Advance_Bogo to calculate discount according to Product rule.
 *
 * @since    1.0.0
 */
class RTWWDPD_Advance_Bogo extends RTWWDPD_Advance_Base {
	/**
	 * variable to set instance of bogo module.
	 *
	 * @since    1.0.0
	 */
	private static $rtwwdpd_instance;
	/**
	 * function to set instance of bogo module.
	 *
	 * @since    1.0.0
	 */
	public static function rtwwdpd_instance() {
		if ( self::$rtwwdpd_instance == null ) {
			self::$rtwwdpd_instance = new RTWWDPD_Advance_Bogo( 'advanced_bogo' );
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
	 * Function to perform discounting rules.
	 *
	 * @since    1.0.0
	 */
	public function rtwwdpd_adjust_cart( $rtwwdpd_temp_cart ) 
	{
		 
		global $woocommerce;
		
		if (!empty($woocommerce->cart->applied_coupons))
		{
			$active = get_site_option('rtwwdpd_coupon_with_discount', 'yes');
		
			if($active == 'no')
			{
				return;
			}
		}
		$sabcd = 'verification_done';
		$rtwwdpd_verification_done = get_site_option( 'rtwbma_'.$sabcd, array() );
		if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) ) 
		{ 

					global $woocommerce;
					$rtwwdpd_product_ids = array();
					$rtwwdpd_purchased_quantity = 0;
				
					foreach ( $rtwwdpd_temp_cart as $cart_item_key => $values ) {
						$rtwwdpd_temp_cart[ $cart_item_key ]                       = $values;
						$rtwwdpd_temp_cart[ $cart_item_key ]['available_quantity'] = $values['quantity'];
						$rtwwdpd_product_ids[] = $values['data']->get_id();
						$rtwwdpd_product_ids[] = $values['data']->get_parent_id();
					}
					$rtwwdpd_cart_total = $woocommerce->cart->get_subtotal();
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
					$rtwwdpd_numorders_cancelled = 0;
					$rtwwdpd_numorders_cancelled = count( wc_get_orders( $rtwwdpd_args ) );
					$rtwwdpd_no_oforders = $rtwwdpd_no_oforders - $rtwwdpd_numorders_cancelled;
					$rtwwdpd_ordrtotal = wc_get_customer_total_spent(get_current_user_id());
					$sabcd = 'fication_done';
					if( is_array( $rtwwdpd_setting_pri ) && !empty( $rtwwdpd_setting_pri ) && is_array( $rtwwdpd_temp_cart ) && !empty( $rtwwdpd_temp_cart ) )
					{
						if($rtwwdpd_setting_pri['rtw_offer_select'] == 'rtw_all_mtch')
						{
							if(isset($rtwwdpd_setting_pri['bogo_rule']) && $rtwwdpd_setting_pri['bogo_rule'] == 1 )
							{
								$rtwwdpd_pro_rul = get_option('rtwwdpd_bogo_rule');
								$rtwwdpd_free_pro_array = array();
								if( is_array($rtwwdpd_pro_rul) && !empty($rtwwdpd_pro_rul) )
								{
									$active_dayss = get_site_option('rtwwdpd_discount_on_selected_days', 'no');
									foreach ( $rtwwdpd_pro_rul as $pro => $rul )
									{  
										
										if($active_dayss == 'yes')
										{
											$active_days = isset($rul['rtwwwdpd_bogo_day']) ? $rul['rtwwwdpd_bogo_day'] : array();
											$current_day = date('N');
										
											if(!in_array($current_day, $active_days))
											{
												continue;
											}
										}

										$rtwwdpd_matched = true;
										if( $rul['rtwwdpd_bogo_from_date'] > $rtwwdpd_today_date || $rul['rtwwdpd_bogo_to_date'] < $rtwwdpd_today_date )
										{
											continue;
										}
										
										$rtwwdpd_pro_idss = array();
										$rtwwdpd_p_c 	= array();
										$rtwwdpd_temp_pro_ids = array();
										$pur_quantity = 0;
									
										if(isset( $rul['combi_quant'] ) && is_array( $rul['combi_quant'] ) && !empty( $rul['combi_quant']) )
										{
											foreach ( $rul['combi_quant'] as $ke => $valu ) {
												$rtwwdpd_p_c[] = $valu;
												$pur_quantity += (int) $valu;
										
											}
										}
										
										if(isset( $rul['product_id'] ) && is_array( $rul['product_id'] ) && !empty( $rul['product_id']) )
										{
											foreach($rul['product_id'] as $proids => $proid)
											{
												$rtwwdpd_pro_idss[] = $proid;
											}
										}
										
										foreach ( $rtwwdpd_temp_cart as $cart_item )
										{
											$rtwwdpd_temp_pro_ids[] = $cart_item['data']->get_id();
											if( isset( $rul['product_id'] ) && !empty( $rul['product_id']) && (in_array($cart_item['data']->get_id(), $rul['product_id']) || in_array($cart_item['data']->get_parent_id(), $rul['product_id'])) )
											{
												$rtwwdpd_purchased_quantity += $cart_item['quantity'];
											}
										}
										
										$rtwwdpd_user_role = $rul['rtwwdpd_select_roles'];
										$rtwwdpd_role_matched = false;
										if(is_array($rtwwdpd_user_role) && !empty($rtwwdpd_user_role))
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
										elseif(empty($rtwwdpd_user_role))
										{
											$rtwwdpd_role_matched = true;
										}
										if($rtwwdpd_role_matched == false)
										{
											continue;
										}

										$rtw_curnt_dayname = date("N");
										$rtwwdpd_day_waise_rule = false;
										if(isset($rul['rtwwdpd_enable_day_bogo']) && $rul['rtwwdpd_enable_day_bogo'] == 'yes')
										{
											
											if(isset($rul['rtwwdpd_select_day_bogo']) && !empty($rul['rtwwdpd_select_day_bogo']))
											{
												if($rul['rtwwdpd_select_day_bogo'] == $rtw_curnt_dayname)
												{
													$rtwwdpd_day_waise_rule = true;
												}
											}
											if($rtwwdpd_day_waise_rule == false)
											{
												continue;
											}
										}
										if($rtwwdpd_cart_total < $rul['rtwwdpd_bogo_min_spend'])
										{
											continue;
										}

										$rtwwdpd_restricted_mails = isset( $rul['rtwwdpd_select_emails'] ) ? $rul['rtwwdpd_select_emails'] : array();

										$rtwwdpd_cur_user_mail = get_current_user_id();
										
										if(in_array($rtwwdpd_cur_user_mail, $rtwwdpd_restricted_mails))
										{
											continue 1;
										}

										$iiiiii = 0;
										$rtwwdpd_verification_done = get_site_option( 'rtwbma_veri'.$sabcd, array() );
										if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) ) 
										{ 
											foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item ) 
											{
												
												$iiiiii++;
												$rtwwdpd_product = $cart_item['data'];
												if ( ! $this->rtwwdpd_is_cumulative( $cart_item, $cart_item_key ) ) 
												{

													if ( $this->rtwwdpd_is_item_discounted( $cart_item, $cart_item_key ) && apply_filters( 'rtwwdpd_stack_order_totals', false ) === false ) {
														continue;
													}
												}

												$rtwwdpd_discounted = isset( WC()->cart->cart_contents[ $cart_item_key ]['discounts'] );
												if ($rtwwdpd_discounted){
													$rtwwdpd_d = WC()->cart->cart_contents[ $cart_item_key ]['discounts'];
													
													if (in_array('advanced_bogo', $rtwwdpd_d['by'])) {
														continue;
													}
												}
												if(isset( $rul['product_id'] ) && is_array( $rul['product_id'] ) && !empty( $rul['product_id']) )
												{
													$rtwwdpd_p_id = isset( $rul['product_id'][0] ) ? $rul['product_id'][0] : '';

													$result_array = array_diff( $rul['product_id'], $rtwwdpd_product_ids );
												}
												else
												{
													$result_array = array();
												}
												$cart = $woocommerce->cart;
												
												// $rtwwdpd_rule_on = apply_filters('rtwwdpd_rule_applied_on_bogo', $pro );

												// if($rtwwdpd_rule_on == $pro)
												// {
												// 	$rtwwdpd_rule_on = 'product';
												// }
												$rtwwdpd_rule_on = isset($rul['rtwwdpd_bogo_rule_on']) ? $rul['rtwwdpd_bogo_rule_on'] : 'product';

											/////////////////////checking addon plugin is active////////////                           
											if( !in_array('bogo-add-on/bogo_addon.php', apply_filters('active_plugins', get_option('active_plugins') ) ) )
											{			
												/////////////////////checking addon plugin is active/////////////////
												if( empty($result_array) && $rtwwdpd_rule_on == 'product')
												{	
													$free_pro_array = array();
													if( isset($rul['product_id']) && (in_array($cart_item['data']->get_id(), $rul['product_id']) || in_array($cart_item['data']->get_parent_id(), $rul['product_id'])) && $pur_quantity <= $rtwwdpd_purchased_quantity )
													{
														$i =0;
														$free_i = 0;
														if( is_array( $rul['rtwbogo'] ) && !empty( $rul['rtwbogo'] ))
														{
															foreach ($rul['rtwbogo'] as $k => $val) 
															{
																if( stripos($cart_item_key , 'rtw_free_prod') === false )
																{
																	
																	$rtwwdpd_free_qunt = 0;
																	$rtwwdpd_f_quant = 0;
																	if( !array_key_exists( $val, $rtwwdpd_free_pro_array ) )
																	{
																		$rtwwdpd_f_quant = floor( $cart_item['quantity'] / $pur_quantity );
																		if( $rtwwdpd_f_quant >= 2 )
																		{
																			$rtwwdpd_free_qunt = (( isset( $rul['bogo_quant_free'][$k] ) ? $rul['bogo_quant_free'][$k] : 0 )* $rtwwdpd_f_quant );
																		}
																		else 
																		{
																			$rtwwdpd_free_qunt = isset( $rul['bogo_quant_free'][$k] ) ? $rul['bogo_quant_free'][$k] : 0;
																		}
																		
																		$rtwwdpd_free_pro_array[$val] = $rtwwdpd_free_qunt;
																	}
																	elseif( array_key_exists( $val, $rtwwdpd_free_pro_array ) )
																	{
																		
																		$rtwwdpd_f_quant = floor( $cart_item['quantity'] / $pur_quantity );
																		
																		if( $rtwwdpd_f_quant >= 2 )
																		{
																			$rtwwdpd_free_qunt = ( $rtwwdpd_free_pro_array[$val] + ( (isset( $rul['bogo_quant_free'][$k] ) ? $rul['bogo_quant_free'][$k] : 0 )* $rtwwdpd_f_quant ) );
																		
																		}
																		else 
																		{
																			$rtwwdpd_free_qunt = ( $rtwwdpd_free_pro_array[$val] + ( (isset( $rul['bogo_quant_free'][$k] ) ? $rul['bogo_quant_free'][$k] : 0 )* $rtwwdpd_f_quant ) );
																		}
																		$rtwwdpd_free_pro_array[$val] = $rtwwdpd_free_qunt;
																	}
																}
					
																$rtwwdpd_free_p_id = $val;
																$rtwwdpd_product_data = wc_get_product($rtwwdpd_free_p_id);
                                                               
																$rtwwdpd_prod_cont = $rul['bogo_quant_free'][$k];
																// {
																	if($rtwwdpd_setting_pri['rtw_auto_add_bogo'] == 'rtw_yes')
																	{
																		$found 		= false;
																		
																		if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
																			foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
																				$_product = $values['data'];
																			
																				if ( $_product->get_id() == 'rtw_free_prod' . $rtwwdpd_free_p_id )
																				{
																					// $found = true;
																				}
																			}
																			if ( ! $found )
																			{
																				$cart_item_key = 'rtw_free_prod'  . $rtwwdpd_free_p_id;
																				$cart->cart_contents[$cart_item_key] = array(
																					'product_id' => $rtwwdpd_free_p_id,
																					'variation_id' => 0,
																					'variation' => array(),
																					'quantity' => isset($rtwwdpd_free_pro_array[$rtwwdpd_free_p_id]),
																					'data' => $rtwwdpd_product_data,
																					// 'line_total' => 0
																				);
																			}
																		}         
																	}
																// }
																$free_i++;
															}
														}
														$free_i =0;
														$i++;
													}
												
													elseif($rtwwdpd_rule_on == 'min_purchase')
													{
														if($rtwwdpd_cart_total < $rul['rtwwdpd_min_purchase'] )
														{
															continue;
														}
														$i =0;
														$free_i = 0;
														if( is_array( $rul['rtwbogo'] ) && !empty( $rul['rtwbogo'] ))
														{
															foreach ($rul['rtwbogo'] as $k => $val) 
															{
																$rtwwdpd_free_p_id = $val;
																$rtwwdpd_product_data = wc_get_product($rtwwdpd_free_p_id);

																$rtwwdpd_prod_cont = $rul['bogo_quant_free'][$k];
																
																{
																	if($rtwwdpd_setting_pri['rtw_auto_add_bogo'] == 'rtw_yes')
																	{
																		$found 		= false;
																		if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
																			foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) 
																			{
																				$_product = $values['data'];
																				if ( $_product->get_id() == 'rtw_free_prod' . $rtwwdpd_free_p_id )
																				{
																					// $found = true;
																				}
																			}
																			if ( ! $found )
																			{
																					
																				$cart_item_key = 'rtw_free_prod'  . $rtwwdpd_free_p_id;
																				$cart->cart_contents[$cart_item_key] = array(
																					'product_id' => $rtwwdpd_free_p_id,
																					'variation_id' => 0,
																					'variation' => array(),
																					'quantity' => $rtwwdpd_prod_cont,
																					'data' => $rtwwdpd_product_data,
																					'line_total' => 0
																				);
																			}
																		}         
																	}
																}
																$free_i++;
															}
														}
														$free_i =0;
														$i++;
													}
												}
							
											}
										///////////////////// start extra addition in bogo rule for discount on free product  
											else
											{ 
												if(isset($value['rtwwdpd_dscnt_cat_val']) && !empty($value['rtwwdpd_dscnt_cat_val']))
												{
													$value['rtwwdpd_dscnt_cat_val'];
												} 
												else
												{
													$value['rtwwdpd_dscnt_cat_val']=100;	
												}
													if( empty($result_array) && $rtwwdpd_rule_on == 'product')
													{	
														$free_pro_array = array();
														if( isset($rul['product_id']) && (in_array($cart_item['data']->get_id(), $rul['product_id']) || in_array($cart_item['data']->get_parent_id(), $rul['product_id'])) && $pur_quantity <= $rtwwdpd_purchased_quantity )
														{
															$i =0;
															$free_i = 0;
															if( is_array( $rul['rtwbogo'] ) && !empty( $rul['rtwbogo'] ))
															{
																foreach ($rul['rtwbogo'] as $k => $val) 
																{
																	if( stripos($cart_item_key , 'rtw_free_prod') === false ) 
																	{  
																		
																		$rtwwdpd_free_qunt = 0;
																		$rtwwdpd_f_quant = 0;
																		
																		if( !array_key_exists( $val, $rtwwdpd_free_pro_array ) )
																		{
																			
																			$rtwwdpd_f_quant = floor( $cart_item['quantity'] / $pur_quantity );
																			if( $rtwwdpd_f_quant >= 2 )
																			{
																				$rtwwdpd_free_qunt = (( isset( $rul['bogo_quant_free'][$k] ) ? $rul['bogo_quant_free'][$k] : 0 )* $rtwwdpd_f_quant );
																			}
																			else 
																			{
																				$rtwwdpd_free_qunt = isset( $rul['bogo_quant_free'][$k] ) ? $rul['bogo_quant_free'][$k] : 0;
																			}
																			$rtwwdpd_free_pro_array[$val] = $rtwwdpd_free_qunt;
																		}
																		elseif( array_key_exists( $val, $rtwwdpd_free_pro_array ) )
																		{
																			
																			$rtwwdpd_f_quant = floor( $cart_item['quantity'] / $pur_quantity );
																			
																			if( $rtwwdpd_f_quant >= 2 )
																			{
																				$rtwwdpd_free_qunt = ( $rtwwdpd_free_pro_array[$val] + ( (isset( $rul['bogo_quant_free'][$k] ) ? $rul['bogo_quant_free'][$k] : 0 )* $rtwwdpd_f_quant ) );
																			}
																			else 
																			{
																				$rtwwdpd_free_qunt = ( $rtwwdpd_free_pro_array[$val] + ( (isset( $rul['bogo_quant_free'][$k] ) ? $rul['bogo_quant_free'][$k] : 0 )* $rtwwdpd_f_quant ) );
																			}
																			$rtwwdpd_free_pro_array[$val] = $rtwwdpd_free_qunt;
																		}
																	}
																	$rtwwdpd_free_p_id = $val;
																	$rtwwdpd_product_data = wc_get_product($rtwwdpd_free_p_id);
																$rtwwdpd_prod_cont = $rul['bogo_quant_free'][$k];					$rtwwdpd_free_p_qunt = wc_get_product($rtwwdpd_free_p_id);		
																					
																	{
																		if($rtwwdpd_setting_pri['rtw_auto_add_bogo'] == 'rtw_yes')
																		{
																			$found 		= false;
																			if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
																				foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
																					$_product = $values['data'];
																					
																					if ( $_product->get_id() == 'rtw_free_prod' . $rtwwdpd_free_p_id )
																					{
																						// $found = true;
																					}
																				}
																				if ( ! $found )
																				{
																					
																					$cart_item_key = 'rtw_free_prod'  . $rtwwdpd_free_p_id;
																					$cart->cart_contents[$cart_item_key] = array(
																						'product_id' => $rtwwdpd_free_p_id,
																						'variation_id' => 0,
																						'variation' => array(),
																						'quantity' => isset($rtwwdpd_free_pro_array[$rtwwdpd_free_p_id]),
																						'data' => $rtwwdpd_product_data
																						
																					);
																				
																				}
																			}         
																		}
																		else
																		{
																			global $woocommerce;
																			$items = $woocommerce->cart->get_cart();
																		}
																		
																	}
																	$free_i++;
																}
															}
															$free_i =0;
															$i++;
														}
													
														elseif($rtwwdpd_rule_on == 'min_purchase')
														{
															if($rtwwdpd_cart_total < $rul['rtwwdpd_min_purchase'] )
															{
															continue;
															}
															$i =0;
															$free_i = 0;
															if( is_array( $rul['rtwbogo'] ) && !empty( $rul['rtwbogo'] ))
															{
																foreach ($rul['rtwbogo'] as $k => $val) 
																{
																	$rtwwdpd_free_p_id = $val;
																	$rtwwdpd_product_data = wc_get_product($rtwwdpd_free_p_id);

																	$rtwwdpd_prod_cont = $rul['bogo_quant_free'][$k];
																	
																	{
																		if($rtwwdpd_setting_pri['rtw_auto_add_bogo'] == 'rtw_yes')
																		{
																			$found 		= false;
																			if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
																				foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
																					$_product = $values['data'];
																					if ( $_product->get_id() == 'rtw_free_prod' . $rtwwdpd_free_p_id )
																					{
																						// $found = true;
																					}
																				}
																				if ( ! $found )
																				{

																					$cart_item_key = 'rtw_free_prod'  . $rtwwdpd_free_p_id;
																					$cart->cart_contents[$cart_item_key] = array(
																						'product_id' => $rtwwdpd_free_p_id,
																						'variation_id' => 0,
																						'variation' => array(),
																						'quantity' => $rtwwdpd_prod_cont,
																						'data' => $rtwwdpd_product_data,
																						'line_total' => 0
																					);
																				}
																			}         
																		}
																	}
																	$free_i++;
																}
															}
															$free_i =0;
															$i++;
														}
													
													}

										
												}
											///////////////////// End extra addition in bogo rule for discount on free product  
						
										}
									}
								}
							}
						}
						
						if( isset($rtwwdpd_setting_pri['bogo_cat_rule']) && $rtwwdpd_setting_pri['bogo_cat_rule'] == 1 )
						{	
							
							$rtwwdpd_pro_rul = get_option('rtwwdpd_bogo_cat_rule');
							
							if( !is_array($rtwwdpd_pro_rul) || empty($rtwwdpd_pro_rul) )
							{
								return;
							}
							
							foreach ($rtwwdpd_pro_rul as $pro => $rul) {
								
								
								$rtwwdpd_matched = true;
								if($rul['rtwwdpd_bogo_from_date'] > $rtwwdpd_today_date || $rul['rtwwdpd_bogo_to_date'] < $rtwwdpd_today_date)
								{
									continue 1;
								}

								$rtwwdpd_pro_idss = array();
								$rtwwdpd_p_c 	= array();
								$rtwwdpd_temp_pro_ids = array();
								$quant_to_purchased = 0;
								$category_to_purchase = array();
								$category_in_cart = array();
								$quantity_in_cart = 0;
								
								foreach ($rul['combi_quant'] as $ke => $valu) {
									$rtwwdpd_p_c[] = $valu;
									$quant_to_purchased += $valu;
								}

								foreach($rul['category_id'] as $pro => $proid)
								{
									$rtwwdpd_pro_idss[] = $proid;
									$category_to_purchase[] = $proid;
								}
					
								foreach ( $rtwwdpd_temp_cart as $items => $item )
								{
									
									if( stripos( $items , '_free') !== false )
									{
									}
									else
									{

										if( isset($item['variation_id']) && !empty($item['variation_id']) )
										{
											$rtwwdpd_catids = wp_get_post_terms( $item['data']->get_parent_id(), 'product_cat', array( 'fields' => 'ids' ) );

		
											if( is_array($rtwwdpd_catids) && !empty($rtwwdpd_catids) )
											{
												foreach ($rtwwdpd_catids as $cc => $c) {
													$category_in_cart[] = $c;
												}
											}
											else
											{
												$categorys = $cart_item['data']->get_category_ids();
												foreach ($categorys as $cc => $c) {
													$category_in_cart[] = $c;
												}
											}
											
											if(array_intersect($rtwwdpd_catids, $category_to_purchase))
											{
												$quantity_in_cart += $item['quantity'];
												
											}
										}
										else{
											$categorys = $item['data']->get_category_ids();
											if( is_array($categorys) && !empty($categorys) )
											{
												foreach ($categorys as $cc => $c) {
													$category_in_cart[] = $c;
												}
											}
											if(array_intersect($categorys, $category_to_purchase))
											{
												// if( is_array($rul['product_exe_id']) && !empty($rul['product_exe_id']) && !in_array( $item['data']->get_id(), $rul['product_exe_id']))
												{
													$quantity_in_cart += $item['quantity'];
												}
											}
										}
									}
									
								
								}
								$intersect_array = array_intersect($category_in_cart, $category_to_purchase );
								if( empty( $intersect_array ) )
								{
									continue 1;
								}
								if( $quantity_in_cart < $quant_to_purchased )
								{
									continue 1;
								}
								
								$rtwwdpd_result = array_diff($rtwwdpd_pro_idss, $rtwwdpd_temp_pro_ids);

								$rtwwdpd_user_role = $rul['rtwwdpd_select_roles_com'] ;
								$rtwwdpd_role_matched = false;
								if(is_array($rtwwdpd_user_role) && !empty($rtwwdpd_user_role))
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
							
								foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item ) 
								{
									$rtwwdpd_f_quant = 0;
									$rtwwdpd_free_qunt = 0;
									$rtwwdpd_f_quant = floor( $quantity_in_cart / $rul['combi_quant'][0] );
								
									if( $rtwwdpd_f_quant >= 2 )
									{
										$rtwwdpd_free_qunt = ((isset( $rul['bogo_quant_free'][0] ) ? $rul['bogo_quant_free'][0] : 0 )* $rtwwdpd_f_quant);
										
									}
									else {
										$rtwwdpd_free_qunt = isset( $rul['bogo_quant_free'][0] ) ? $rul['bogo_quant_free'][0] : 0;
									}
									
									if( isset($rul['product_exe_id']) && $rul['product_exe_id'] == $cart_item['data']->get_id() )
									{
										continue 1;
									}

									$rtwwdpd_product = $cart_item['data'];

									if ( ! $this->rtwwdpd_is_cumulative( $cart_item, $cart_item_key ) ) {

										if ( $this->rtwwdpd_is_item_discounted( $cart_item, $cart_item_key ) && apply_filters( 'rtwwdpd_stack_order_totals', false ) === false ) {
											continue;
										}
									}

									$rtwwdpd_discounted = isset( WC()->cart->cart_contents[ $cart_item_key ]['discounts'] );

									if ($rtwwdpd_discounted){
										$rtwwdpd_d = WC()->cart->cart_contents[ $cart_item_key ]['discounts'];
										if (in_array('advanced_bogo_cat', $rtwwdpd_d['by'])) {
											continue;
										}
									}
								
									// if( !in_array('bogo-add-on/bogo_addon.php', apply_filters('active_plugins', get_option('active_plugins') ) ) )
									// 	{	

									if(isset($value['rtwwdpd_dscnt_cat_val']) && !empty($value['rtwwdpd_dscnt_cat_val']))
									{
										$value['rtwwdpd_dscnt_cat_val'];
									} 
									else
									{
										$value['rtwwdpd_dscnt_cat_val']=100;	
									}
									$rtwwdpd_original_price = $this->rtw_get_price_to_discount( $cart_item, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ), 'advanced_bogo_cat' );
								

									if ( $rtwwdpd_original_price ) 
									{
										$cart = $woocommerce->cart;
										
										if(isset($rul['category_id']))
										{				
											$rtwwdpd_catids = $cart_item['data']->get_category_ids();

											// if(in_array($rul['category_id'][0], $rtwwdpd_catids))
											{
												
												$i =0;
												$free_i = 0;
												// if((isset($rul['rtwwdpd_select_free_product']) && $rul['rtwwdpd_select_free_product'] == 'diff') || !isset($rul['rtwwdpd_select_free_product']))
												// {
													if( is_array($rul['rtwbogo']) && !empty($rul['rtwbogo']))
													{
														foreach ($rul['rtwbogo'] as $k => $val) {
															$rtwwdpd_free_p_id = $val;
															$rtwwdpd_product_data = wc_get_product($rtwwdpd_free_p_id);

															$rtwwdpd_free_qunt = $rul['bogo_quant_free'][$k];
															$rtwwdpd_prod_cont = isset($rul['combi_quant'][$k]) ?  $rul['combi_quant'][$k] : '';
															
															
															if($rtwwdpd_setting_pri['rtw_auto_add_bogo'] == 'rtw_yes')
															{
															
																$found 		= false;
																//check if product already in cart
																if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
																	foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
																		$_product = $values['data'];
																		if ( $_product->get_id() == 'rtw_free_prod_bogoc' .$rtwwdpd_free_p_id )
																			$found = true;
																	}
																// if product not found, add it
																	if ( ! $found )
																	{
																		
																		$cart_item_key = 'rtw_free_prod_bogoc' . $rtwwdpd_free_p_id;
																		$cart->cart_contents[$cart_item_key] = array(
																			'product_id' => $rtwwdpd_free_p_id,
																			'variation_id' => 0,
																			'variation' => array(),
																			'quantity' => $rtwwdpd_free_qunt,
																			'data' => $rtwwdpd_product_data,
																			// 'line_total' => 0
																		);
																		// return;
																	}
																}         
															}
															$free_i++;
														}
													}
													// }elseif(isset($rul['rtwwdpd_select_free_product']) && $rul['rtwwdpd_select_free_product'] == 'same'){

													// }
													$free_i =0;
													$i++;
											}
										}
								}
								// }

							}

							}

						
						}
						//// bogo tag rule start

						if( isset($rtwwdpd_setting_pri['bogo_tag_rule']) && $rtwwdpd_setting_pri['bogo_tag_rule'] == 1 )
						{	
							
							$rtwwdpd_pro_rul = get_option('rtwwdpd_bogo_tag_rule');
							
							if( !is_array($rtwwdpd_pro_rul) || empty($rtwwdpd_pro_rul) )
							{
								return;
							}
							
							foreach ($rtwwdpd_pro_rul as $pro => $rul) {
								
								
								$rtwwdpd_matched = true;
								if($rul['rtwwdpd_bogo_from_date'] > $rtwwdpd_today_date || $rul['rtwwdpd_bogo_to_date'] < $rtwwdpd_today_date)
								{
									continue 1;
								}
								
								$rtwwdpd_pro_idss = array();
								$rtwwdpd_p_c 	= array();
								$rtwwdpd_temp_pro_ids = array();
								$quant_to_purchased = 0;
								$tag_to_purchase = array();
								$tags_in_cart = array();
								$quantity_in_cart = 0;
								
								foreach ($rul['combi_quant'] as $ke => $valu) {
									$rtwwdpd_p_c[] = $valu;
									$quant_to_purchased += $valu;
								}

								foreach($rul['tag_id'] as $pro => $proid)
								{
									$rtwwdpd_pro_idss[] = $proid;
									$tag_to_purchase[] = $proid;
								}
					
								foreach ( $rtwwdpd_temp_cart as $items => $item )
								{
									
									if( stripos( $items , '_free') !== false )
									{
									}
									else
									{

										if( isset($item['variation_id']) && !empty($item['variation_id']) )
										{
											$rtwwdpd_catids = wp_get_post_terms( $item['data']->get_parent_id(), 'product_tag', array( 'fields' => 'ids' ) );

		
											if( is_array($rtwwdpd_catids) && !empty($rtwwdpd_catids) )
											{
												foreach ($rtwwdpd_catids as $cc => $c) {
													$tags_in_cart[] = $c;
												}
											}
											else
											{
												$tags = $cart_item['data']->get_tag_ids();
												foreach ($tags as $cc => $c) {
													$tags_in_cart[] = $c;
												}
											}
											
											if(array_intersect($rtwwdpd_catids, $tag_to_purchase))
											{
												$quantity_in_cart += $item['quantity'];
												
											}
										}
										else{
											$tags = $item['data']->get_tag_ids();
											if( is_array($tags) && !empty($tags) )
											{
												foreach ($tags as $cc => $c) {
													$tags_in_cart[] = $c;
												}
											}
											if(array_intersect($tags, $tag_to_purchase))
											{
												// if( is_array($rul['product_exe_id']) && !empty($rul['product_exe_id']) && !in_array( $item['data']->get_id(), $rul['product_exe_id']))
												{
													$quantity_in_cart += $item['quantity'];
												}
											}
										}
									}
									
								
								}
								
								$intersect_array = array_intersect($tags_in_cart, $tag_to_purchase );
								if( empty( $intersect_array ) )
								{
									continue 1;
								}
								if( $quantity_in_cart < $quant_to_purchased )
								{
									continue 1;
								}
								
								$rtwwdpd_result = array_diff($rtwwdpd_pro_idss, $rtwwdpd_temp_pro_ids);
								
								$rtwwdpd_user_role = $rul['rtwwdpd_select_roles_com'] ;
								$rtwwdpd_role_matched = false;
								if(is_array($rtwwdpd_user_role) && !empty($rtwwdpd_user_role))
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
							
								foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item ) 
								{
									$rtwwdpd_f_quant = 0;
									$rtwwdpd_free_qunt = 0;
									$rtwwdpd_f_quant = floor( $quantity_in_cart / $rul['combi_quant'][0] );
								
									if( $rtwwdpd_f_quant >= 2 )
									{
										$rtwwdpd_free_qunt = ((isset( $rul['bogo_quant_free'][0] ) ? $rul['bogo_quant_free'][0] : 0 )* $rtwwdpd_f_quant);
										
									}
									else {
										$rtwwdpd_free_qunt = isset( $rul['bogo_quant_free'][0] ) ? $rul['bogo_quant_free'][0] : 0;
									}
									
									if( isset($rul['product_exe_id']) && $rul['product_exe_id'] == $cart_item['data']->get_id() )
									{
										continue 1;
									}

									$rtwwdpd_product = $cart_item['data'];

									if ( ! $this->rtwwdpd_is_cumulative( $cart_item, $cart_item_key ) ) {

										if ( $this->rtwwdpd_is_item_discounted( $cart_item, $cart_item_key ) && apply_filters( 'rtwwdpd_stack_order_totals', false ) === false ) {
											continue;
										}
									}

									$rtwwdpd_discounted = isset( WC()->cart->cart_contents[ $cart_item_key ]['discounts'] );

									if ($rtwwdpd_discounted){
										$rtwwdpd_d = WC()->cart->cart_contents[ $cart_item_key ]['discounts'];
										if (in_array('advanced_bogo_cat', $rtwwdpd_d['by'])) {
											continue;
										}
									}	
									$rtwwdpd_original_price = $this->rtw_get_price_to_discount( $cart_item, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ), 'advanced_bogo_cat' );
								

									if ( $rtwwdpd_original_price ) 
									{
										$cart = $woocommerce->cart;
										
										if(isset($rul['tag_id']))
										{				
											$rtwwdpd_catids = $cart_item['data']->get_category_ids();

											// if(in_array($rul['category_id'][0], $rtwwdpd_catids))
											{
												
												$i =0;
												$free_i = 0;

													if( is_array($rul['rtwbogo']) && !empty($rul['rtwbogo']))
													{
														foreach ($rul['rtwbogo'] as $k => $val) {
															$rtwwdpd_free_p_id = $val;
															$rtwwdpd_product_data = wc_get_product($rtwwdpd_free_p_id);

															$rtwwdpd_free_qunt = $rul['bogo_quant_free'][$k];
															$rtwwdpd_prod_cont = isset($rul['combi_quant'][$k]) ?  $rul['combi_quant'][$k] : '';
															
															
															if($rtwwdpd_setting_pri['rtw_auto_add_bogo'] == 'rtw_yes')
															{
															
																$found 		= false;
																//check if product already in cart
																if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
																	foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
																		$_product = $values['data'];
																		if ( $_product->get_id() == 'rtw_free_prod_bogot' .$rtwwdpd_free_p_id )
																			$found = true;
																	}
																// if product not found, add it
																	if ( ! $found )
																	{
																		$cart_item_key = 'rtw_free_prod_bogot' . $rtwwdpd_free_p_id;
																		$cart->cart_contents[$cart_item_key] = array(
																			'product_id' => $rtwwdpd_free_p_id,
																			'variation_id' => 0,
																			'variation' => array(),
																			'quantity' => $rtwwdpd_free_qunt,
																			'data' => $rtwwdpd_product_data,
																			'line_total' => 0
																		);
																		// return;
																	}
																}         
															}
															$free_i++;
														}
													}
													$free_i =0;
													$i++;
											}
										}
								}
								// }

							}

							}

						
						}
						//// bogo tag rule
					}
				}
			}
	}

	/**
	 * Function to get discounting rules.
	 *
	 * @since    1.0.0
	 */

	protected function rtwwdpd_get_pricing_rule_sets( $rtwwdpd_cart_item ) {
		
		$rtwwdpd_product = wc_get_product( $rtwwdpd_cart_item['product_id'] );
		
		if ( empty( $rtwwdpd_product ) ) {
			return false;
		}
		
		$rtwwdpd_pricing_rule_sets = apply_filters( 'rtwwdpd_get_product_pricing_rule_sets', $this->rtwwdpd_get_product_meta( $rtwwdpd_product, '_pricing_rules' ), $rtwwdpd_product->get_id(), $this );
		
		$rtwwdpd_pricing_rule_sets = apply_filters( 'rtwwdpd_get_cart_item_pricing_rule_sets', $rtwwdpd_pricing_rule_sets, $rtwwdpd_cart_item );
		
		$rtwwdpd_sets              = array();
		if ( is_array($rtwwdpd_pricing_rule_sets) && !empty($rtwwdpd_pricing_rule_sets) ) {
			foreach ( $rtwwdpd_pricing_rule_sets as $set_id => $set_data ) {
				$rtwwdpd_sets[ $set_id ] = new RTWWDPD_Adjustment_Set_Product( $set_id, $set_data );
			}
		}
		
		return $rtwwdpd_sets;
	}

	/**
	 * Function to get product details.
	 *
	 * @since    1.0.0
	 */
	public static function rtwwdpd_get_product_meta( $rtwwdpd_product, $rtwwdpd_key, $rtwwdpd_context = 'view' ) {
		if ( empty( $rtwwdpd_product ) ) {
			return false;
		}

		return get_post_meta( $rtwwdpd_product->get_id(), $rtwwdpd_key, true);
	}

	/**
	 * Function to get product ids.
	 *
	 * @since    1.0.0
	 */
	public static function rtwwdpd_get_product_ids( $rtwwdpd_product ) {

		if ( empty( $rtwwdpd_product ) ) {
			return array();
		}

		$rtwwdpd_id    = isset( $rtwwdpd_product->variation_id ) ? $rtwwdpd_product->get_parent_id() : $rtwwdpd_product->get_id();

		return $rtwwdpd_id;
	}
	
}
