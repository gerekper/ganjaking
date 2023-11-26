<?php
/**
 * Class RTWWDPD_Advance_Product_Tag to calculate discount according to Product Tag rule.
 *
 * @since    1.0.0
 */
class RTWWDPD_Advance_Product_Tag extends RTWWDPD_Advance_Base {
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
			self::$rtwwdpd_instance = new RTWWDPD_Advance_Product_Tag( 'advanced_pro_tag' );
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
			
			$rtwwdpd_today_date = current_time('Y-m-d');
			$rtwwdpd_user = wp_get_current_user();
			$set_id = '';
			if( is_array($rtwwdpd_setting_pri) && !empty($rtwwdpd_temp_cart) )
			{
				if($rtwwdpd_setting_pri['rtw_offer_select'] == 'rtw_all_mtch')
				{
					if(isset($rtwwdpd_setting_pri['prod_tag_rule']) && $rtwwdpd_setting_pri['prod_tag_rule']==1)
					{	
						$rtwwdpd_pro_rul = get_option('rtwwdpd_tag_method');
						if( is_array($rtwwdpd_pro_rul) && !empty($rtwwdpd_pro_rul))
						{
							foreach ($rtwwdpd_pro_rul as $pro => $rul) {
								$rtwwdpd_matched = true;
								if($rul['rtwwdpd_tag_from_date'] > $rtwwdpd_today_date || $rul['rtwwdpd_tag_to_date'] < $rtwwdpd_today_date)
								{
									continue;
								}

								$rtwwdpd_user_role = $rul['rtwwdpd_select_roles'] ;
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
								$sabcd = 'fication_done';
								$rtwwdpd_verification_done = get_site_option( 'rtwbma_veri'.$sabcd, array() );
								if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) ) { 
									foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item ) {
										if(isset($rul['product_exe_id']) && $rul['product_exe_id'] == $cart_item['data']->get_id())
										{
											continue 1;
										}
										$product = $cart_item['data'];
										$rtwwdpd_prod_id = $cart_item['data']->get_id();

										if ( ! $this->rtwwdpd_is_cumulative( $cart_item, $cart_item_key ) ) {

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
										// Code modify because it was not implenting on cart page..
										// $abc = wc_get_product($cart_item['data']->get_parent_id());
										if(!empty($cart_item['data']))
										{
											$tags_arr = $cart_item['data']->get_tag_ids(); 
										}

										if( isset( $rul['rtw_product_tags'] ) && is_array( $rul['rtw_product_tags'] ) && !empty( $rul['rtw_product_tags'] ) )
										{
											if(!empty($rul['rtw_product_tags']) && !empty($tags_arr))
											{
												$rtw_matched = array_intersect( $rul['rtw_product_tags'], $tags_arr);
											}

											if( empty( $rtw_matched ) )
											{	
												continue 1;
											}
										}
										$rtwwdpd_original_price = $this->rtw_get_price_to_discount( $cart_item, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ), 'advanced_pro_tag' );
										
										// Code modify because it was not implenting on cart page..
										
										// $abc = wc_get_product($cart_item['data']->get_parent_id());

										// $tags_arr = $abc->get_tag_ids();
										// if( isset( $rul['rtw_product_tags'] ) && is_array( $rul['rtw_product_tags'] ) && !empty( $rul['rtw_product_tags'] ) )
										// {
										// 	$rtw_matched = array_intersect( $rul['rtw_product_tags'], $tags_arr);

										// 	if( empty( $rtw_matched ) )
										// 	{
										// 		continue 1;
										// 	}
										// }
                                        
										if ( $rtwwdpd_original_price ) {
											$rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $rul['rtwwdpd_tag_discount_value'], 'prod_tag', $cart_item, $this );
                                          
											$rtwwdpd_terms = get_terms( 'product_tag' );
											$rtwwdpd_term_array = array();
											if ( ! empty( $rtwwdpd_terms ) && ! is_wp_error( $rtwwdpd_terms ) ){
												foreach ( $rtwwdpd_terms as $term ) {
													$rtwwdpd_term_array[] = $term->term_id;
												}
											}
											if($rul['rtwwdpd_tag_discount_type'] == 'rtwwdpd_discount_percentage')
											{
												$rtwwdpd_amount = $rtwwdpd_amount / 100;
												$rtwwdpd_discnted_val = ( floatval( $rtwwdpd_amount ) * $rtwwdpd_original_price );
												if($rtwwdpd_discnted_val > $rul['rtwwdpd_tag_max_discount'])
												{
													$rtwwdpd_discnted_val = $rul['rtwwdpd_tag_max_discount'];
												}

												$rtwwdpd_price_adjusted = ( floatval( $rtwwdpd_original_price ) - $rtwwdpd_discnted_val );
												foreach ($rul['rtw_product_tags'] as $tagi => $tagid) {

													if(in_array($tagid, $rtwwdpd_term_array))
													{ 
														if(isset($rul['rtwwdpd_tag_exclude_sale']))
														{
															if( !$cart_item['data']->is_on_sale() )
															{
																Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_pro_tag', $set_id );
																break;
															}
														}
														else{
															Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_pro_tag', $set_id );
															break;
														}
													}
												}
											}
											elseif($rul['rtwwdpd_tag_discount_type'] == 'rtwwdpd_flat_discount_amount')
											{
												if($rtwwdpd_amount > $rul['rtwwdpd_tag_max_discount'])
												{
													$rtwwdpd_amount = $rul['rtwwdpd_tag_max_discount'];
												}
												$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount );

												if( is_array($rul['rtw_product_tags']) && !empty($rul['rtw_product_tags']))
												{
													foreach ($rul['rtw_product_tags'] as $tagi => $tagid) {
														if(in_array($tagid, $rtwwdpd_term_array))
														{ 	
															if(isset($rul['rtwwdpd_tag_exclude_sale']))
															{
																if( !$cart_item['data']->is_on_sale() )
																{
																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_pro_tag', $set_id );
																	break;
																}
															}
															else{
																Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_pro_tag', $set_id );
																break;
															}
														}
													}
												}
											}
											else
											{
												if($rtwwdpd_amount > $rul['rtwwdpd_tag_max_discount'])
												{
													$rtwwdpd_amount = $rul['rtwwdpd_tag_max_discount'];
												}
												$rtwwdpd_amount = ( $rtwwdpd_amount / $cart_item['quantity'] );
												$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount );

												if( is_array($rul['rtw_product_tags']) && !empty($rul['rtw_product_tags']))
												{
													foreach ($rul['rtw_product_tags'] as $tagi => $tagid) {
														if(in_array($tagid, $rtwwdpd_term_array))
														{ 	
															if(isset($rul['rtwwdpd_tag_exclude_sale']))
															{
																if( !$cart_item['data']->is_on_sale() )
																{
																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_pro_tag', $set_id );
																	break;
																}
															}
															else{
																Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_pro_tag', $set_id );
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
				}
			}
		}
	}
}
