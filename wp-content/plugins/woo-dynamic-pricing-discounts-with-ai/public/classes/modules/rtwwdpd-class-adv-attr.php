<?php
/**
 * Class RTWWDPD_Advance_Attribute to calculate discount according to Product Attribute rule.
 *
 * @since    1.0.0
 */
class RTWWDPD_Advance_Attribute extends RTWWDPD_Advance_Base {

	/**
	 * Variable to set instance of module attribute rule in varable $rtwwdpd_instance.
	 *
	 * @since    1.0.0
	 */
	private static $rtwwdpd_instance;

	/**
	 * Function to get instance of module attribute rule.
	 *
	 * @since    1.0.0
	 */
	public static function rtwwdpd_instance() {
		if ( self::$rtwwdpd_instance == null ) {
			self::$rtwwdpd_instance = new RTWWDPD_Advance_Attribute( 'advanced_attribute' );
		}
		return self::$rtwwdpd_instance;
	}

	/**
	 * Varileble to get check applied rules.
	 *
	 * @since    1.0.0
	 */
	private $rtwwdpd_used_rules = array();

	/**
	 * Main function.
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
		$sabcd = 'verification_done';
		$rtwwdpd_verification_done = get_site_option( 'rtwbma_'.$sabcd, array() );
		if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) ) { 
			
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
		$rtwwdpd_set_id = 'rtwwdpd_aattr';
		if(is_array($rtwwdpd_setting_pri) && !empty($rtwwdpd_setting_pri))
		{
			if($rtwwdpd_setting_pri['rtw_offer_select'] == 'rtw_all_mtch')
			{
				if(isset($rtwwdpd_setting_pri['attr_rule']) && $rtwwdpd_setting_pri['attr_rule']==1)
				{
					$rtwwdpd_pro_rul = get_option('rtwwdpd_att_rule');
					
					if(is_array($rtwwdpd_pro_rul) && !empty($rtwwdpd_pro_rul))
					{
						foreach ($rtwwdpd_pro_rul as $pro => $rul) {

							$rtwwdpd_matched = true;
							if($rul['rtwwdpd_att_from_date'] > $rtwwdpd_today_date || $rul['rtwwdpd_att_to_date'] < $rtwwdpd_today_date)
							{
								continue 1;
							}

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
								continue 1;
							}
							$max_quant = 0;
							$this_price = 0;
							$max_price = 0;
							$max_weight = 0;
							$total_weight = 0;
							
							
							if( is_array($rtwwdpd_temp_cart) && !empty($rtwwdpd_temp_cart) )
							{
								$ai = 1;
								foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item ) {
									$rtwwdpd_product = $cart_item['data'];

									$rtwwdpd_attr = array();
									if( !empty(wc_get_product($cart_item['data']->get_parent_id()) ))
									{
										$rtwwdpd_attr = wc_get_product($cart_item['data']->get_parent_id())->get_attributes();
									}
									else{
										$rtwwdpd_attr = $cart_item['data']->get_attributes();
											
									}

									$attr_ids = array();
									foreach ($rtwwdpd_attr as $attrr => $att) {
										if(is_object($att))
										{
											foreach ($att->get_options() as $kopt => $opt) {
												$attr_ids[] = $opt;
											}
										}
									}
									if( isset($rul['rtwwdpd_max']) && !empty($rul['rtwwdpd_max']) )
									{
										$product_id = isset($rul['rtwwdpd_attribute_val'])? $rul['rtwwdpd_attribute_val'] : '';
										foreach($rul['rtwwdpd_attribute_val'] as $key => $val)								
										{
											if(in_array($val,$attr_ids))
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
									}

									$attribut_val = isset($rul['rtwwdpd_attribute_val']) ? $rul['rtwwdpd_attribute_val'] : array();

									$rtwwdpd_arr = array_intersect( $attr_ids, $attribut_val );
									if(is_array($rtwwdpd_arr) && empty($rtwwdpd_arr))
									{
										continue 1;
									}
									
									if(isset($rul['product_exe_id']) && (in_array($cart_item['data']->get_id(),$rul['product_exe_id']) ||  in_array($cart_item['data']->get_parent_id(),$rul['product_exe_id'])))
									{
										continue 1;
									}
									
									if ( ! $this->rtwwdpd_is_cumulative( $cart_item, $cart_item_key ) ) {

										if ( $this->rtwwdpd_is_item_discounted( $cart_item, $cart_item_key ) && apply_filters( 'rtwwdpd_stack_order_totals', false ) === false ) {
											continue 1;
										}
									}
									

									if(isset($rul['rtwwdpd_att_exclude_sale']) && $rul['rtwwdpd_att_exclude_sale'] == 'yes' )
									{
										if( $cart_item['data']->is_on_sale() )
										{
											continue 1;
										}
									}
									
									$rtwwdpd_discounted = isset( WC()->cart->cart_contents[ $cart_item_key ]['discounts'] );

									if ($rtwwdpd_discounted){
										$d = WC()->cart->cart_contents[ $cart_item_key ]['discounts'];
										if (in_array('advanced_attribute', $d['by'])) {
											// continue 1;
										}
									}

									$rtwwdpd_original_price = $this->rtw_get_price_to_discount( $cart_item, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false  ), 'advanced_attribute'.$pro );

									if ( $rtwwdpd_original_price ) {
										$rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $rul['rtwwdpd_att_discount_value'], 'adv_attr', $cart_item, $this );

										$set_data = array();

										if($rul['rtwwdpd_att_discount_type'] == 'rtwwdpd_discount_percentage')
										{
											if($rul['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
											{	
												
												if( $max_quant < $rul['rtwwdpd_min'] )
												{
													continue 1;
												}
												if( isset($rul['rtwwdpd_max']) && $rul['rtwwdpd_max'] != '' && $max_quant > $rul['rtwwdpd_max'] )
												{
													continue 1;
												}
											}
											elseif($rul['rtwwdpd_check_for'] == 'rtwwdpd_price')
											{
												if( $max_price < $rul['rtwwdpd_min'] )
												{
													continue 1;
												}
												$total_cost = ( $cart_item['data']->get_price() * $cart_item['quantity'] );
												if( isset($rul['rtwwdpd_max']) && !empty($rul['rtwwdpd_max']) && $total_cost > $rul['rtwwdpd_max'] )
												{
													continue 1;
												}
											}
											elseif($rul['rtwwdpd_check_for'] == 'rtwwdpd_weight')
											{
												
												if( ($cart_item['quantity']*$cart_item['data']->get_weight()) < $rul['rtwwdpd_min'] )
												{
													continue 1;
												}
												if( isset($rul['rtwwdpd_max']) && $rul['rtwwdpd_max'] != '' && ($cart_item['quantity']*$cart_item['data']->get_weight()) > $rul['rtwwdpd_max'] )
												{
													continue 1;
											
												}
											}
											$rtwwdpd_amount = $rtwwdpd_amount / 100;
											$rtwwdpd_discount_pr = ( floatval( $rtwwdpd_amount ) * $rtwwdpd_original_price );

											if($rtwwdpd_discount_pr > $rul['rtwwdpd_att_max_discount'])
											{
												$rtwwdpd_discount_pr = $rul['rtwwdpd_att_max_discount'];
											}

											$rtwwdpd_price_adjusted = ( floatval( $rtwwdpd_original_price ) - $rtwwdpd_discount_pr );

											$ids=0;
											
													
											$rtwwdpd_size = strtolower($rtwwdpd_product->get_attribute( 'pa_size' ));

											if(isset($rul['rtwwdpd_att_exclude_sale']))
											{
												if( !$cart_item['data']->is_on_sale() )
												{
													Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $rtwwdpd_set_id );
												}
											}
											else{
												Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $rtwwdpd_set_id );
											}
												
										}
										elseif($rul['rtwwdpd_att_discount_type'] == 'rtwwdpd_fixed_price')
										{
											if($rtwwdpd_amount > $rul['rtwwdpd_att_max_discount'])
											{
												$rtwwdpd_amount = $rul['rtwwdpd_att_max_discount'];
											}
											$rtwwdpd_amount = ( $rtwwdpd_amount / $cart_item['quantity'] );
											
											$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount );
											
											$ids=0;
											
											if(isset($rul['rtwwdpd_att_exclude_sale']))
											{
												if( !$cart_item['data']->is_on_sale() )
												{
													Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $rtwwdpd_set_id );
												}
											}
											else{
												Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $rtwwdpd_set_id );
											}
										}
												
										else
										{
											if($rtwwdpd_amount > $rul['rtwwdpd_att_max_discount'])
											{
												$rtwwdpd_amount = $rul['rtwwdpd_att_max_discount'];
											}

											$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount );
											
											$ids=0;
											
											if(isset($rul['rtwwdpd_att_exclude_sale']))
											{
												if( !$cart_item['data']->is_on_sale() )
												{
													Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $rtwwdpd_set_id );
												}
											}
											else{
												Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $rtwwdpd_set_id );
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
	 * Function to get disocunting rules.
	 *
	 * @since    1.0.0
	 */
	protected function rtw_get_pricing_rule_sets( $cart_item ) {

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
