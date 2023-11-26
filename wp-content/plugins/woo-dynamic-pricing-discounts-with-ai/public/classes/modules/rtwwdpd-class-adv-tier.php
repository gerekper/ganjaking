<?php
/**
 * Class RTWWDPD_Advance_Tier to calculate discount according to Product rule.
 *
 * @since    1.0.0
 */
class RTWWDPD_Advance_Tier extends RTWWDPD_Advance_Base {
	/**
	 * variable to set instance of tier module.
	 *
	 * @since    1.0.0
	 */
	private static $rtwwdpd_instance;

	/**
	 * function to set instance of tier module.
	 *
	 * @since    1.0.0
	 */
	public static function rtwwdpd_instance() {
		if ( self::$rtwwdpd_instance == null ) {
			self::$rtwwdpd_instance = new RTWWDPD_Advance_Tier( 'advanced_tier' );
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

			if( !is_user_logged_in() )
			{
				$rtwwdpd_no_oforders = 0;
			}

			$rtwwdpd_ordrtotal = wc_get_customer_total_spent(get_current_user_id());
			$rtwwdpd_num_decimals = apply_filters( 'rtwwdpd_get_decimals', (int) get_option( 'woocommerce_price_num_decimals' ) );
			$set_id = 'tier';
			if( is_array($rtwwdpd_setting_pri) && !empty($rtwwdpd_temp_cart) )
			{
				if($rtwwdpd_setting_pri['rtw_offer_select'] == 'rtw_all_mtch')
				{
					
					if( isset( $rtwwdpd_setting_pri['tier_rule'] ) && $rtwwdpd_setting_pri['tier_rule'] == 1 )
					{	
					
						$rtwwdpd_pro_rul = get_option('rtwwdpd_tiered_rule');
						
						if( is_array( $rtwwdpd_pro_rul ) &&  !empty( $rtwwdpd_pro_rul ) )
						{
							
							$sabcd = 'fication_done';
							$rtwwdpd_verification_done = get_site_option( 'rtwbma_veri'.$sabcd, array() );
							if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) ) { 
								foreach ($rtwwdpd_pro_rul as $pro => $rul) {

									if($rul['rtwwdpd_from_date'] > $rtwwdpd_today_date || $rul['rtwwdpd_to_date'] < $rtwwdpd_today_date)
									{
										continue;
									}
									$rtwwdpd_user_role = $rul['rtwwdpd_select_roles'] ;

									$rtwwdpd_role_matched = false;
									if(isset($rtwwdpd_user_role) && !empty($rtwwdpd_user_role))
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

									if(isset($rul['rtwwdpd_min_orders']) && $rul['rtwwdpd_min_orders'] > $rtwwdpd_no_oforders)
									{
										continue;
									}
									if(isset($rul['rtwwdpd_min_spend']) && $rul['rtwwdpd_min_spend'] > $rtwwdpd_ordrtotal)
									{
										continue;
									}


									$rtwwdpd_matched = true;
									foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item ) {

										$rtwwdpd_product = $cart_item['data'];
										$rtwwdpd_prod_id = $cart_item['data']->get_id();

										foreach ($rtwwdpd_pro_rul as $id => $id_val) {

											if($id_val['products'][0] == $rtwwdpd_prod_id){
												$i = $id;
											}
										}

										if ( ! $this->rtwwdpd_is_cumulative( $cart_item, $cart_item_key ) ) {

											if ( $this->rtwwdpd_is_item_discounted( $cart_item, $cart_item_key ) && apply_filters( 'rtwwdpd_stack_order_totals', false ) === false ) {
												// continue;
											}
										}

										$rtwwdpd_discounted = isset( WC()->cart->cart_contents[ $cart_item_key ]['discounts'] );

										if ($rtwwdpd_discounted){
											$d = WC()->cart->cart_contents[ $cart_item_key ]['discounts'];
											if (in_array('advanced_tier', $d['by'])) {
												continue;
											}
										}

										$rtwwdpd_original_price = $this->rtw_get_price_to_discount( $cart_item, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ), 'tier_rule'.$pro );
										if ( $rtwwdpd_original_price ) {
	
											$pp = 0;
											$rtwwdpd_amount = 0;
											if(isset($rul['discount_val']) && !empty($rul['discount_val']))
											{
												foreach ($rul['discount_val'] as $dis => $disval) {

													$rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $disval, $rul, $cart_item, $this );
												
													if( $rul['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
													{ 
														$rtwwdpd_amount = $rtwwdpd_amount / 100;

														$rtwwdpd_discount_pr = ( floatval( $rtwwdpd_amount ) * $rtwwdpd_original_price );

														if( $rtwwdpd_discount_pr > $rul['rtwwdpd_max_discount'] )
														{
															$rtwwdpd_discount_pr = $rul['rtwwdpd_max_discount'];
														}

														$rtwwdpd_price_adjusted = ( floatval( $rtwwdpd_original_price ) - $rtwwdpd_discount_pr );
														if($rul['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
														{
															if( $rul['quant_min'][$dis] <= $cart_item['quantity'] && $rul['quant_max'][$dis] >= $cart_item['quantity'])
															{	
																foreach ($rul['products'] as $p => $pid) {

																if( $pid == $cart_item['data']->get_id() )
																{		
																	if(isset($rul['rtwwdpd_exclude_sale']))
																	{
																		if( !$cart_item['data']->is_on_sale() )
																		{
																			Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier'.$pro, $set_id );
																			continue 2;
																		}
																	}
																	else{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier'.$pro, $set_id );
																		continue 2;
																	}
																}}
															}
														}
														elseif($rul['rtwwdpd_check_for'] == 'rtwwdpd_price')
														{
															if( $rul['quant_min'][$dis] <= $cart_item['data']->get_price() && $rul['quant_max'][$dis] >= $cart_item['data']->get_price())
															{	
																foreach ($rul['products'] as $p => $pid) {
																if( $pid == $cart_item['data']->get_id())
																{				
																	if(isset($rul['rtwwdpd_exclude_sale']))
																	{
																		if( !$cart_item['data']->is_on_sale() )
																		{
																			Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier'.$pro, $set_id );
																			continue 2;
																		}
																	}
																	else{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier'.$pro, $set_id );
																		continue 2;
																	}
																}}
															}
														}
														else
														{
															if( $rul['quant_min'][$dis] <= $cart_item['data']->get_weight() && $rul['quant_max'][$dis] >= $cart_item['data']->get_weight())
															{	
																foreach ($rul['products'] as $p => $pid) {
																if( $pid == $cart_item['data']->get_id())
																{				
																	if(isset($rul['rtwwdpd_exclude_sale']))
																	{
																		if( !$cart_item['data']->is_on_sale() )
																		{
																			Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier'.$pro, $set_id );
																			continue 2;
																		}
																	}
																	else{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier'.$pro, $set_id );
																		continue 2;
																	}
																}}
															}
														}
													}
													elseif($rul['rtwwdpd_discount_type'] == 'rtwwdpd_flat_discount_amount')
													{
														
														if($rtwwdpd_amount > $rul['rtwwdpd_max_discount'])
														{
															$rtwwdpd_amount = $rul['rtwwdpd_max_discount'];
														}

														if($rtwwdpd_amount > $cart_item['data']->get_price())
														{
															$rtwwdpd_amount = $cart_item['data']->get_price();
														}
														
														$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount);

														if($rul['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
														{
															if( $rul['quant_min'][$dis] <= $cart_item['quantity'] && $rul['quant_max'][$dis] >= $cart_item['quantity'])
															{	
													
																foreach ($rul['products'] as $p => $pid) {
																if( $pid == $cart_item['data']->get_id())
																{					
																	if(isset($rul['rtwwdpd_exclude_sale']))
																	{
																		if( !$cart_item['data']->is_on_sale() )
																		{
																			Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier'.$pro, $set_id );
																			continue 2;
																		}
																	}
																	else{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier'.$pro, $set_id );
																		continue 2;
																	}
																}}
															}
														}
														elseif($rul['rtwwdpd_check_for'] == 'rtwwdpd_price')
														{
															if( $rul['quant_min'][$dis] <= $cart_item['data']->get_price() && $rul['quant_max'][$dis] >= $cart_item['data']->get_price())
															{	
																foreach ($rul['products'] as $p => $pid) {
																if( $pid == $cart_item['data']->get_id())
																{				
																	if(isset($rul['rtwwdpd_exclude_sale']))
																	{
																		if( !$cart_item['data']->is_on_sale() )
																		{
																			Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier'.$pro, $set_id );
																			continue 2;
																		}
																	}
																	else{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier'.$pro, $set_id );
																		continue 2;
																	}
																}}
															}
														}
														else
														{

															if( $rul['quant_min'][$dis] <= ($cart_item['data']->get_weight()* $cart_item['quantity']) && $rul['quant_max'][$dis] >= ($cart_item['data']->get_weight()* $cart_item['quantity']))
															{	
																foreach ($rul['products'] as $p => $pid) {
																if( $pid == $cart_item['data']->get_id())
																{				
																	if(isset($rul['rtwwdpd_exclude_sale']))
																	{
																		if( !$cart_item['data']->is_on_sale() )
																		{
																			Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier'.$pro, $set_id );
																			continue 2;
																		}
																	}
																	else{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier'.$pro, $set_id );
																		continue 2;
																	}
																}}
															}
														}
													}
													else
													{
														if($rtwwdpd_amount > $rul['rtwwdpd_max_discount'])
														{
															$rtwwdpd_amount = $rul['rtwwdpd_max_discount'];
														}
														$rtwwdpd_amount = ( $rtwwdpd_amount / $cart_item['quantity'] );
														$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount);
														
														if($rul['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
														{
															if( $rul['quant_min'][$dis] <= $cart_item['quantity'] && $rul['quant_max'][$dis] >= $cart_item['quantity'])
															{	
													
																foreach ($rul['products'] as $p => $pid) {
																if( $pid == $cart_item['data']->get_id())
																{					
																	if(isset($rul['rtwwdpd_exclude_sale']))
																	{
																		if( !$cart_item['data']->is_on_sale() )
																		{
																			Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier'.$pro, $set_id );
																			continue 2;
																		}
																	}
																	else{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier'.$pro, $set_id );
																		continue 2;
																	}
																}}
															}
														}
														elseif($rul['rtwwdpd_check_for'] == 'rtwwdpd_price')
														{
															if( $rul['quant_min'][$dis] <= $cart_item['data']->get_price() && $rul['quant_max'][$dis] >= $cart_item['data']->get_price())
															{	
																foreach ($rul['products'] as $p => $pid) {
																if( $pid == $cart_item['data']->get_id())
																{				
																	if(isset($rul['rtwwdpd_exclude_sale']))
																	{
																		if( !$cart_item['data']->is_on_sale() )
																		{
																			Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier'.$pro, $set_id );
																			continue 2;
																		}
																	}
																	else{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier'.$pro, $set_id );
																		continue 2;
																	}
																}}
															}
														}
														else
														{
															if( $rul['quant_min'][$dis] <= ($cart_item['data']->get_weight()* $cart_item['quantity']) && $rul['quant_max'][$dis] >= ($cart_item['data']->get_weight()* $cart_item['quantity']))
															{	
																foreach ($rul['products'] as $p => $pid) {
																if( $pid == $cart_item['data']->get_id())
																{				
																	if(isset($rul['rtwwdpd_exclude_sale']))
																	{
																		if( !$cart_item['data']->is_on_sale() )
																		{
																			Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier'.$pro, $set_id );
																			continue 2;
																		}
																	}
																	else{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier'.$pro, $set_id );
																		continue 2;
																	}
																}}
															}
														}
													}
													$pp++;
												}
											}
										}
									}
								}
							}
						}
					}
					if( isset( $rtwwdpd_setting_pri['tier_cat_rule'] ) && $rtwwdpd_setting_pri['tier_cat_rule'] == 1 )
					{	
						$rtwwdpd_pro_rul = get_option( 'rtwwdpd_tiered_cat' );
						
						if( is_array($rtwwdpd_pro_rul) && !empty($rtwwdpd_pro_rul))
						{
							
							$sabcd = 'fication_done';
							$rtwwdpd_verification_done = get_site_option( 'rtwbma_veri'.$sabcd, array() );
							if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) ) { 
								foreach ($rtwwdpd_pro_rul as $pro => $rul) {

									$rtwwdpd_total_weight = 0;
									$rtwwdpd_total_price = 0;
									$rtwwdpd_total_quantity = 0;

									foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item ) {
										if( isset($cart_item['variation_id']) && !empty($cart_item['variation_id']) )
										{
											$rtwwdpd_catid = wp_get_post_terms( $cart_item['data']->get_parent_id(), 'product_cat', array( 'fields' => 'ids' ) );
										}else{
											$rtwwdpd_catid = wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) );
										}

										// $rtwwdpd_catid = wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) );

										if( is_array($rtwwdpd_catid) && !empty($rtwwdpd_catid) && isset($rul['category_id']) && in_array($rul['category_id'][0], $rtwwdpd_catid))
										{
											if( $cart_item['data']->get_weight() != '')
											{
												$rtwwdpd_total_weight += $cart_item['quantity'] * $cart_item['data']->get_weight();
											}

											$rtwwdpd_total_price += $cart_item['quantity'] * $cart_item['data']->get_price();

											$rtwwdpd_total_quantity += $cart_item['quantity'];
										}
									}


									if( $rul['rtwwdpd_frm_date_c'] > $rtwwdpd_today_date || $rul['rtwwdpd_to_date_c'] < $rtwwdpd_today_date )
									{
										continue 1;
									}
									$rtwwdpd_user_role = $rul['rtwwdpd_select_roles'] ;
									$rtwwdpd_role_matched = false;
									if(isset($rtwwdpd_user_role) && !empty($rtwwdpd_user_role))
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

									if(isset($rul['rtwwdpd_min_orders']) && $rul['rtwwdpd_min_orders'] > $rtwwdpd_no_oforders)
									{
										continue 1;
									}
									if(isset($rul['rtwwdpd_min_spend']) && $rul['rtwwdpd_min_spend'] > $rtwwdpd_ordrtotal)
									{
										continue 1;
									}

									$rtwwdpd_matched = true;
									foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item ) {

										if(isset($rul['product_exe_id']) && $rul['product_exe_id'] == $cart_item['data']->get_id())
										{
											continue 1;
										}

										$rtwwdpd_product = $cart_item['data'];

										$cat_id = $this->rtwwdpd_get_prod_cat_ids( $rtwwdpd_product );

										foreach ($rtwwdpd_pro_rul as $id => $id_val) {

											if($id_val['category_id'][0] == $cat_id){
												$i = $id;
											}
										}

										if ( ! $this->rtwwdpd_is_cumulative( $cart_item, $cart_item_key ) ) {

											if ( $this->rtwwdpd_is_item_discounted( $cart_item, $cart_item_key ) && apply_filters( 'rtwwdpd_stack_order_totals', false ) === false ) {
												continue;
											}
										}

										$rtwwdpd_discounted = isset( WC()->cart->cart_contents[ $cart_item_key ]['discounts'] );
										if ($rtwwdpd_discounted){
											$d = WC()->cart->cart_contents[ $cart_item_key ]['discounts'];
											if (in_array('advanced_tier_c', $d['by'])) {
												continue;
											}
										}

										$rtwwdpd_original_price = $this->rtw_get_price_to_discount( $cart_item, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ) );

										if ( $rtwwdpd_original_price ) {

											$pp = 0;
											$rtwwdpd_amount = 0;
											if(isset($rul['discount_val']) && !empty($rul['discount_val']))
											{
												foreach ($rul['discount_val'] as $dis => $disval) {

													if(is_array($rul['quant_min']) && !empty($rul['quant_min'])){
															$rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $disval, $rul, $cart_item, $this );
													}

													if( $rul['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage' )
													{
														$rtwwdpd_amount = $rtwwdpd_amount / 100;

														$rtwwdpd_discount_pr = ( floatval( $rtwwdpd_amount ) * $rtwwdpd_original_price );

														if( $rtwwdpd_discount_pr > $rul['rtwwdpd_max_discount'] )
														{
															$rtwwdpd_discount_pr = $rul['rtwwdpd_max_discount'];
														}

														$rtwwdpd_price_adjusted = ( floatval( $rtwwdpd_original_price ) - $rtwwdpd_discount_pr);

														if($rul['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
														{
															if( $rul['quant_min'][$dis] <= $rtwwdpd_total_quantity && $rul['quant_max'][$dis] >= $rtwwdpd_total_quantity)
															{	
																if( isset($cart_item['variation_id']) && !empty($cart_item['variation_id']) )
																{
																	$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_parent_id(), 'product_cat', array( 'fields' => 'ids' ) );
																}else{
																	$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) );
																}
																// $rtwwdpd_catids = $cart_item['data']->get_category_ids();

																if(in_array($rul['category_id'][0], $rtwwdpd_catids))
																{		
																	if(isset($rul['rtwwdpd_exclude_sale']))
																	{
																		if( !$cart_item['data']->is_on_sale() )
																		{
																			Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier_cat'.$pro, $set_id );
																			continue 2;
																		}
																	}
																	else{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier_cat'.$pro, $set_id );
																		continue 2;
																	}
																}
															}
														}
														elseif($rul['rtwwdpd_check_for'] == 'rtwwdpd_price')
														{
															if( $rul['quant_min'][$dis] <= $rtwwdpd_total_price && $rul['quant_max'][$dis] >= $rtwwdpd_total_price)
															{	
																$rtwwdpd_catids = $cart_item['data']->get_category_ids();

																if( isset($cart_item['variation_id']) && !empty($cart_item['variation_id']) )
																{
																	$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_parent_id(), 'product_cat', array( 'fields' => 'ids' ) );
																}else{
																	$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) );
																}

																if(in_array($rul['category_id'][0], $rtwwdpd_catids))
																{			
																	if(isset($rul['rtwwdpd_exclude_sale']))
																	{
																		if( !$cart_item['data']->is_on_sale() )
																		{
																			Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier_cat'.$pro, $set_id );
																			continue 2;
																		}
																	}
																	else{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier_cat'.$pro, $set_id );
																		continue 2;
																	}
																}
															}
														}
														else
														{
															if( $rul['quant_min'][$dis] <= $rtwwdpd_total_weight && $rul['quant_max'][$dis] >= $rtwwdpd_total_weight)
															{	
																$rtwwdpd_catids = $cart_item['data']->get_category_ids();

																if( isset($cart_item['variation_id']) && !empty($cart_item['variation_id']) )
																{
																	$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_parent_id(), 'product_cat', array( 'fields' => 'ids' ) );
																}else{
																	$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) );
																}

																if(in_array($rul['category_id'][0], $rtwwdpd_catids))
																{				
																	if(isset($rul['rtwwdpd_exclude_sale']))
																	{
																		if( !$cart_item['data']->is_on_sale() )
																		{
																			Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier_cat'.$pro, $set_id );
																			continue 2;
																		}
																	}
																	else{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier_cat'.$pro, $set_id );
																		continue 2;
																	}
																}
															}
														}
													}
													elseif( $rul['rtwwdpd_discount_type'] == 'rtwwdpd_flat_discount_amount' )
													{
														
														if( $rtwwdpd_amount > $rul['rtwwdpd_max_discount'] )
														{
															$rtwwdpd_amount = $rul['rtwwdpd_max_discount'];
														}

														$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount);

														if($rul['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
														{ 
															if( $rul['quant_min'][$dis] <= $rtwwdpd_total_quantity && $rul['quant_max'][$dis] >= $rtwwdpd_total_quantity)
															{	
																$rtwwdpd_catids = $cart_item['data']->get_category_ids();

																if( isset($cart_item['variation_id']) && !empty($cart_item['variation_id']) )
																{
																	$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_parent_id(), 'product_cat', array( 'fields' => 'ids' ) );
																}else{
																	$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) );
																}

																if(in_array($rul['category_id'][0], $rtwwdpd_catids))
																{		
																	if(isset($rul['rtwwdpd_exclude_sale']))
																	{
																		if( !$cart_item['data']->is_on_sale() )
																		{
																			Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier_cat'.$pro, $set_id );
																			continue 2;
																		}
																	}
																	else{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier_cat'.$pro, $set_id );
																		continue 2;
																	}
																}
															}
														}
														elseif($rul['rtwwdpd_check_for'] == 'rtwwdpd_price')
														{
															if( $rul['quant_min'][$dis] <= $rtwwdpd_total_price && $rul['quant_max'][$dis] >= $rtwwdpd_total_price)
															{	
																$rtwwdpd_catids = $cart_item['data']->get_category_ids();

																if( isset($cart_item['variation_id']) && !empty($cart_item['variation_id']) )
																{
																	$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_parent_id(), 'product_cat', array( 'fields' => 'ids' ) );
																}else{
																	$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) );
																}

																if(in_array($rul['category_id'][0], $rtwwdpd_catids))
																{			
																	if(isset($rul['rtwwdpd_exclude_sale']))
																	{
																		if( !$cart_item['data']->is_on_sale() )
																		{
																			Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier_cat'.$pro, $set_id );
																			continue 2;
																		}
																	}
																	else{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier_cat'.$pro, $set_id );
																		continue 2;
																	}
																}
															}
														}
														else
														{
															if( $rul['quant_min'][$dis] <= $rtwwdpd_total_weight && $rul['quant_max'][$dis] >= $rtwwdpd_total_weight)
															{	
																$rtwwdpd_catids = $cart_item['data']->get_category_ids();

																if( isset($cart_item['variation_id']) && !empty($cart_item['variation_id']) )
																{
																	$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_parent_id(), 'product_cat', array( 'fields' => 'ids' ) );
																}else{
																	$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) );
																}

																if(in_array($rul['category_id'][0], $rtwwdpd_catids))
																{			
																	if(isset($rul['rtwwdpd_exclude_sale']))
																	{
																		if( !$cart_item['data']->is_on_sale() )
																		{
																			Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier_cat'.$pro, $set_id );
																			continue 2;
																		}
																	}
																	else{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier_cat'.$pro, $set_id );
																		continue 2;
																	}
																}
															}
														}
													}
													else
													{
														
														if( $rtwwdpd_amount > $rul['rtwwdpd_max_discount'] )
														{
															$rtwwdpd_amount = $rul['rtwwdpd_max_discount'];
														}
														$rtwwdpd_amount = ( $rtwwdpd_amount / $rtwwdpd_total_quantity);
														
														$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount );

														if($rul['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
														{ 
															if( $rul['quant_min'][$dis] <= $rtwwdpd_total_quantity && $rul['quant_max'][$dis] >= $rtwwdpd_total_quantity)
															{	
																$rtwwdpd_catids = $cart_item['data']->get_category_ids();

																if( isset($cart_item['variation_id']) && !empty($cart_item['variation_id']) )
																{
																	$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_parent_id(), 'product_cat', array( 'fields' => 'ids' ) );
																}else{
																	$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) );
																}

																if(in_array($rul['category_id'][0], $rtwwdpd_catids))
																{		
																	if(isset($rul['rtwwdpd_exclude_sale']))
																	{
																		if( !$cart_item['data']->is_on_sale() )
																		{
																			Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier_cat'.$pro, $set_id );
																			continue 2;
																		}
																	}
																	else{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier_cat'.$pro, $set_id );
																		continue 2;
																	}
																}
															}
														}
														elseif($rul['rtwwdpd_check_for'] == 'rtwwdpd_price')
														{
															if( $rul['quant_min'][$dis] <= $rtwwdpd_total_price && $rul['quant_max'][$dis] >= $rtwwdpd_total_price)
															{	
																$rtwwdpd_catids = $cart_item['data']->get_category_ids();

																if( isset($cart_item['variation_id']) && !empty($cart_item['variation_id']) )
																{
																	$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_parent_id(), 'product_cat', array( 'fields' => 'ids' ) );
																}else{
																	$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) );
																}

																if(in_array($rul['category_id'][0], $rtwwdpd_catids))
																{			
																	if(isset($rul['rtwwdpd_exclude_sale']))
																	{
																		if( !$cart_item['data']->is_on_sale() )
																		{
																			Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier_cat'.$pro, $set_id );
																			continue 2;
																		}
																	}
																	else{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier_cat'.$pro, $set_id );
																		continue 2;
																	}
																}
															}
														}
														else
														{
															if( $rul['quant_min'][$dis] <= $rtwwdpd_total_weight && $rul['quant_max'][$dis] >= $rtwwdpd_total_weight)
															{	
																$rtwwdpd_catids = $cart_item['data']->get_category_ids();

																if( isset($cart_item['variation_id']) && !empty($cart_item['variation_id']) )
																{
																	$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_parent_id(), 'product_cat', array( 'fields' => 'ids' ) );
																}else{
																	$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) );
																}

																if(in_array($rul['category_id'][0], $rtwwdpd_catids))
																{			
																	if(isset($rul['rtwwdpd_exclude_sale']))
																	{
																		if( !$cart_item['data']->is_on_sale() )
																		{
																			Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier_cat'.$pro, $set_id );
																			continue 2;
																		}
																	}
																	else{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, 'advanced_tier_cat'.$pro, $set_id );
																		continue 2;
																	}
																}
															}
														}
													}
													$pp++;
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
	 * Function to get discounting rules.
	 *
	 * @since    1.0.0
	 */
	protected function rtw_get_pricing_rule_sets( $rtwwdpd_cart_item ) {
		
		$rtwwdpd_product = wc_get_product( $rtwwdpd_cart_item['data']->get_id() );
		
		if ( empty( $rtwwdpd_product ) ) {
			return false;
		}
		
		$rtwwdpd_pricing_rule_sets = apply_filters( 'rtwwdpd_get_product_pricing_rule_sets', $this->rtwwdpd_get_product_meta( $rtwwdpd_product, '_pricing_rules' ), $rtwwdpd_product->get_id(), $this );
		
		$rtwwdpd_pricing_rule_sets = apply_filters( 'rtwwdpd_get_cart_item_pricing_rule_sets', $rtwwdpd_pricing_rule_sets, $rtwwdpd_cart_item );
		
		$rtwwdpd_sets              = array();
		if ( $rtwwdpd_pricing_rule_sets ) {
			$sabcd = 'fication_done';
			$rtwwdpd_verification_done = get_site_option( 'rtwbma_veri'.$sabcd, array() );
			if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) ) { 
				foreach ( $rtwwdpd_pricing_rule_sets as $set_id => $set_data ) {
					$rtwwdpd_sets[ $set_id ] = new RTWWDPD_Adjustment_Set_Product( $set_id, $set_data );
				}
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
	 * Function to get product id.
	 *
	 * @since    1.0.0
	 */
	public static function rtwwdpd_get_product_ids( $rtwwdpd_product ) {

		if ( empty( $rtwwdpd_product ) ) {
			return array();
		}

		$rtwwdpd_id    = isset( $rtwwdpd_product->variation_id ) ? $rtwwdpd_product->get_id() : $rtwwdpd_product->get_id();

		return $rtwwdpd_id;
	}

	/**
	 * Function to get product category ids.
	 *
	 * @since    1.0.0
	 */
	public static function rtwwdpd_get_prod_cat_ids( $rtwwdpd_product ) {

		if ( empty( $rtwwdpd_product ) ) {
			return array();
		}

		if(isset( $rtwwdpd_product->variation_id ))
		{
			$rtwwdpd_cat = get_the_terms( $rtwwdpd_product->get_parent_id(), 'product_cat' );
			
			$rtwwdpd_cat_id = '';
			foreach ( $rtwwdpd_cat as $categoria ) {
				if($categoria->parent == 0){
				}
				$rtwwdpd_cat_id = $categoria->term_id;
			}
		}

		$rtwwdpd_id    = isset( $rtwwdpd_product->variation_id ) ? $rtwwdpd_cat_id : $rtwwdpd_product->get_category_ids();

		return $rtwwdpd_id;
	}
}
