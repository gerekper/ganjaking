<?php
global $woocommerce;
$rtwwdpd_offers = get_option('rtwwdpd_setting_priority');
$rtwwdpd_priority = array();
$rtwwdpd_i = 0;
if( is_array($rtwwdpd_offers) && !empty($rtwwdpd_offers) )
{
	foreach ($rtwwdpd_offers as $key => $value) {
		if($key == 'cart_rule_row')
		{
			$rtwwdpd_priority[$rtwwdpd_i] = $key;
			$rtwwdpd_i++;
		}
		if($key == 'pro_rule_row')
		{
			$rtwwdpd_priority[$rtwwdpd_i] = $key;
			$rtwwdpd_i++;
		}
		elseif($key == 'bogo_rule_row')
		{
			$rtwwdpd_priority[$rtwwdpd_i] = $key;
			$rtwwdpd_i++;
		}
		elseif($key == 'tier_rule_row')
		{
			$rtwwdpd_priority[$rtwwdpd_i] = $key;
			$rtwwdpd_i++;
		}
		elseif($key == 'pro_com_rule_row')
		{
			$rtwwdpd_priority[$rtwwdpd_i] = $key;
			$rtwwdpd_i++;
		}
		elseif($key == 'cat_com_rule_row')
		{
			$rtwwdpd_priority[$rtwwdpd_i] = $key;
			$rtwwdpd_i++;
		}
		elseif($key == 'tier_cat_rule_row')
		{
			$rtwwdpd_priority[$rtwwdpd_i] = $key;
			$rtwwdpd_i++;
		}	
		elseif($key == 'var_rule_row')
		{
			$rtwwdpd_priority[$rtwwdpd_i] = $key;
			$rtwwdpd_i++;
		}
		elseif($key == 'cat_rule_row')
		{
			$rtwwdpd_priority[$rtwwdpd_i] = $key;
			$rtwwdpd_i++;
		}
		elseif($key == 'bogo_cat_rule_row')
		{
			$rtwwdpd_priority[$rtwwdpd_i] = $key;
			$rtwwdpd_i++;
		}
		elseif($key == 'attr_rule_row')
		{
			$rtwwdpd_priority[$rtwwdpd_i] = $key;
			$rtwwdpd_i++;
		}
		elseif($key == 'prod_tag_rule_row')
		{
			$rtwwdpd_priority[$rtwwdpd_i] = $key;
			$rtwwdpd_i++;
		}
	}
}

if( is_array($rtwwdpd_temp_cart) && !empty($rtwwdpd_temp_cart) )
{
	$rtwwdpd_cat_array = array();
	$match_quant = false;
	$rtwwdpd_product_ids = array();
	foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_itemm ) {
		foreach ( $cart_itemm['data']->get_category_ids() as $cat => $cat_id ) {
			$rtwwdpd_cat_array[] = $cat_id;
		}
		$rtwwdpd_product_ids[] = $cart_itemm['data']->get_id();
	}
	foreach ($rtwwdpd_temp_cart as $cart_item_key => $cart_itemm) {

		$rtwwdpd_compared_price = 0;
		$rtwwdpd_best_dis_arr = array();
		foreach ($rtwwdpd_priority as $rule => $rule_name) {

			if($rule_name == 'bogo_rule_row')
			{
				if(isset($rtwwdpd_offers['bogo_rule']))
				{
					$rtwwdpd_prod_id = $cart_itemm['data']->get_id();
					$rtwwdpd_product_price = $this->rtw_get_price_to_discount( $cart_itemm, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ) );
					$rtwwdpd_rule_name = get_option('rtwwdpd_bogo_rule');
					if( is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
					{
						$rtwwdpd_free_pro_array = array();
						
						$active_dayss = get_site_option('rtwwdpd_discount_on_selected_days', 'no');
						foreach ($rtwwdpd_rule_name as $ke => $name) {
						
							if($active_dayss == 'yes')
							{
								$active_days = isset($name['rtwwwdpd_bogo_day']) ? $name['rtwwwdpd_bogo_day'] : array();
								$current_day = date('N');

								if(!in_array($current_day, $active_days))
								{
									continue;
								}
							}

							$pur_quantity = 0;
							foreach ($name['combi_quant'] as $ke => $valu) {
								$rtwwdpd_p_c[] = $valu;
								$pur_quantity += $valu;
							}
							$rtwwdpd_date = $name['rtwwdpd_bogo_to_date'];
							$rtwwdpd_free_p = isset( $name['rtwbogo'] ) ? $name['rtwbogo'] : '';
							$rtwwdpd_pro_idss = array();
							foreach($name['product_id'] as $pro => $proid)
							{
								$rtwwdpd_pro_idss[] = $proid;
							}
							$result_array = array_diff($rtwwdpd_pro_idss, $rtwwdpd_product_ids );

							$rtwwdpd_user_role = $name['rtwwdpd_select_roles'] ;
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
							if(empty($result_array))
							{
								if($rtwwdpd_date >= $rtwwdpd_today_date && $rtwwdpd_today_date >= $name['rtwwdpd_bogo_from_date'])
								{
									if(isset($name['rtwbogo']) && is_array($name['rtwbogo']) && !empty($name['rtwbogo']))
									{
										{ 
											$rtwwdpd_free_quant = 0;
											$rtwwdpd_f_quant = 0;
											foreach ($name['rtwbogo'] as $no => $ids) {
												if( !array_key_exists( $ids, $rtwwdpd_free_pro_array ) )
												{
													$rtwwdpd_f_quant = floor( $cart_itemm['quantity'] / $pur_quantity );
													
													if( $rtwwdpd_f_quant >= 2 )
													{
														$rtwwdpd_free_qunt = (( isset( $name['bogo_quant_free'][$no] ) ? $name['bogo_quant_free'][$no] : 0 )* $rtwwdpd_f_quant );
													}
													else {
														$rtwwdpd_free_qunt = isset( $name['bogo_quant_free'][$no] ) ? $name['bogo_quant_free'][$no] : 0;
													}

													$rtwwdpd_free_pro_array[$ids] = $rtwwdpd_free_qunt;
												}
												elseif( array_key_exists( $ids, $rtwwdpd_free_pro_array ) )
												{
													
													$rtwwdpd_f_quant = floor( $cart_itemm['quantity'] / $pur_quantity );
													
													if( $rtwwdpd_f_quant >= 2 )
													{
														$rtwwdpd_free_qunt = ( $rtwwdpd_free_pro_array[$ids] + ( (isset( $name['bogo_quant_free'][$no] ) ? $name['bogo_quant_free'][$no] : 0 )* $rtwwdpd_f_quant ) );
													}
													else {
														$rtwwdpd_free_qunt = ( $rtwwdpd_free_pro_array[$ids] + isset( $name['bogo_quant_free'][$no] ) ? $name['bogo_quant_free'][$no] : 0 );
													}

													$rtwwdpd_free_pro_array[$ids] = $rtwwdpd_free_qunt;
												}

												if(in_array($cart_itemm['data']->get_id(), $name['product_id']))
												{	
													$rtwwdpd_pro = wc_get_product( isset( $name['rtwbogo'][0] ) ? $name['rtwbogo'][0] : '' );
													$rtwwdpd_b_price = $rtwwdpd_pro->get_price();

													if($rtwwdpd_b_price > $rtwwdpd_compared_price)
													{

													}
													if( $rtwwdpd_offers['rtw_auto_add_bogo'] == 'rtw_yes')
													{
														// if( is_array($rtwwdpd_free_p) && !empty($rtwwdpd_free_p) )
														{
															foreach ($rtwwdpd_free_p as $free => $id) 
															{
																$rtwwdpd_free_p_id = $id;
																$rtwwdpd_found 	= false;
																$rtwwdpd_product_data = wc_get_product($rtwwdpd_free_p_id);

																if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
																	foreach ( WC()->cart->get_cart() as $cart_item_key => $values ){
																		$_product = $values['data'];
																		if ( $_product->get_id() == 'rtw_free_prod'. $ke .$rtwwdpd_free_p_id )
																			$rtwwdpd_found = false;
																	}
																	if ( ! $rtwwdpd_found )
																	{

																		$cart_item_ke = 'rtw_free_prod'. $ke . $rtwwdpd_free_p_id;
																		WC()->cart->cart_contents[$cart_item_ke] = array(
																			'product_id' => $rtwwdpd_free_p_id,
																			'variation_id' => 0,
																			'variation' => array(),
																			'quantity' => $rtwwdpd_free_qunt,
																			'data' => $rtwwdpd_product_data,
																			'line_total' => 0
																		);
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
			if($rule_name == 'bogo_cat_rule_row')
			{
				if(isset($rtwwdpd_offers['bogo_cat_rule']))
				{
					$rtwwdpd_product_price = $this->rtw_get_price_to_discount( $cart_itemm, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ) );
					$rtwwdpd_rule_name = get_option('rtwwdpd_bogo_cat_rule');
					if( is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
					{
						foreach ($rtwwdpd_rule_name as $ke => $name) {
							$rtwwdpd_date = $name['rtwwdpd_bogo_to_date'];
							$rtwwdpd_free_p = isset( $name['rtwbogo'] ) ? $name['rtwbogo'] : '';

							$rtwwdpd_user_role = $name['rtwwdpd_select_roles_com'] ;
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
							
							if(is_array($rtwwdpd_free_p) && !empty($rtwwdpd_free_p) &&  $rtwwdpd_date >= $rtwwdpd_today_date && $rtwwdpd_today_date >= $name['rtwwdpd_bogo_from_date'])
							{
								if(is_array($name['category_id']) && !empty($name['category_id']))
								{
									foreach ($name['category_id'] as $no => $ids) 
									{
										if(in_array($ids, $rtwwdpd_cat_array))
										{	
											if( isset( $name['rtwbogo'][0] ) )
											{
												$rtwwdpd_pro = wc_get_product( isset( $name['rtwbogo'][0] ) ? $name['rtwbogo'][0] : '' );
												$rtwwdpd_b_price = $rtwwdpd_pro->get_price();

											}
											if($rtwwdpd_offers['rtw_auto_add_bogo'] == 'rtw_yes')
											{
												foreach ($rtwwdpd_free_p as $free => $id)
												{
													$rtwwdpd_free_quant = $name['bogo_quant_free'][$free];
													$rtwwdpd_free_p_id = $id;
													$rtwwdpd_found 	= false;
													$rtwwdpd_product_data = wc_get_product($rtwwdpd_free_p_id);
													if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
														foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
															$_product = $values['data'];
															if ( $_product->get_id() == 'rtw_free_prod_bogoc'.$rtwwdpd_free_p_id )
																$rtwwdpd_found = true;
														}
														if ( ! $rtwwdpd_found )
														{
															$cart_item_ke = 'rtw_free_prod_bogoc' . $rtwwdpd_free_p_id;
															
															WC()->cart->cart_contents[$cart_item_ke] = array(
																'product_id' => $rtwwdpd_free_p_id,
																'variation_id' => 0,
																'variation' => array(),
																'line_total' => 0,
																'quantity' => $rtwwdpd_free_quant,
																'data' => $rtwwdpd_product_data
															);
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
			if($rule_name == 'cat_rule_row')
			{
				if(isset($rtwwdpd_offers['cat_rule']))
				{
					$rtwwdpd_prod_id = $cart_itemm['data']->get_id();
					$rtwwdpd_product_price = $this->rtw_get_price_to_discount( $cart_itemm, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ) );
					$rtwwdpd_rule_name = get_option('rtwwdpd_single_cat_rule');

					$rtwwdpd_cat_id = wp_get_post_terms( $rtwwdpd_prod_id, 'product_cat', array( 'fields' => 'ids' ) );

					if( is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
					{
						$active_dayss = get_site_option('rtwwdpd_discount_on_selected_days', 'no');
						foreach ($rtwwdpd_rule_name as $name) {

							if($active_dayss == 'yes')
							{
								$active_days = isset($name['rtwwwdpd_cat_day']) ? $name['rtwwwdpd_cat_day'] : array();
								$current_day = date('N');

								if(!in_array($current_day, $active_days))
								{
									continue;
								}
							}

							if( isset( $name['rtw_exe_product_tags'] ) && is_array( $name['rtw_exe_product_tags'] ) && !empty( $name['rtw_exe_product_tags'] ) )
							{
								$rtw_matched = array_intersect( $name['rtw_exe_product_tags'], $cart_itemm['data']->get_tag_ids());

								if( !empty( $rtw_matched ) )
								{
									continue 1;
								}
							}
							$rtwwdpd_total_weight = 0;
							$rtwwdpd_total_price = 0;
							$rtwwdpd_total_quantity = 0;

							foreach ( $rtwwdpd_temp_cart as $cart_item_keys => $cart_items ) {

								if( isset($cart_items['variation_id']) && !empty($cart_items['variation_id']) )
								{
									$rtwwdpd_catids = wp_get_post_terms( $cart_items['data']->get_parent_id(), 'product_cat', array( 'fields' => 'ids' ) );
								}else{
									$rtwwdpd_catids = wp_get_post_terms( $cart_items['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) );
								}

								if( is_array($rtwwdpd_catids) && !empty($rtwwdpd_catids) && in_array($name['category_id'], $rtwwdpd_catids))
								{
									$weight = $cart_items['data']->get_weight();
									if(!isset($weight) || empty($weight))
									{
										$weight = 1;
									}

									$rtwwdpd_total_weight += $cart_items['quantity'] * $weight;

									$rtwwdpd_total_price += $cart_items['quantity'] * $cart_items['data']->get_price();

									$rtwwdpd_total_quantity += $cart_items['quantity'];
								}
							}
							$rtwwdpd_user_role = $name['rtwwdpd_select_roles'] ;
							$rtwwdpd_role_matched = false;
							if( is_array($rtwwdpd_user_role) && !empty($rtwwdpd_user_role))
							{
								foreach ($rtwwdpd_user_role as $rol => $role) {
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
								continue;
							}

							if($name['rtwwdpd_check_for_cat'] == 'rtwwdpd_quantity')
							{
								if($rtwwdpd_total_quantity < $name['rtwwdpd_min_cat'])
								{
									continue 1;
								}
								if(isset($name['rtwwdpd_max_cat']) && $name['rtwwdpd_max_cat'] != '')
								{
									if( $name['rtwwdpd_max_cat'] < $rtwwdpd_total_quantity)
									{
										continue 1;
									}
								}
							}
							elseif($name['rtwwdpd_check_for_cat'] == 'rtwwdpd_price')
							{
								if($rtwwdpd_total_price < $name['rtwwdpd_min_cat'])
								{
									continue 1;
								}
								if(isset($name['rtwwdpd_max_cat']) && $name['rtwwdpd_max_cat'] != '')
								{
									if($name['rtwwdpd_max_cat'] < $rtwwdpd_total_price)
									{
										continue 1;
									}
								}
							}
							else{
								if($rtwwdpd_total_weight < $name['rtwwdpd_min_cat'])
								{
									continue 1;
								}
								if(isset($name['rtwwdpd_max_cat']) && $name['rtwwdpd_max_cat'] != '')
								{
									if($name['rtwwdpd_max_cat'] < $rtwwdpd_total_weight)
									{
										continue 1;
									}
								}
							}

							$rtwwdpd_date = $name['rtwwdpd_to_date'];
							if($rtwwdpd_date >= $rtwwdpd_today_date && $rtwwdpd_today_date >= $name['rtwwdpd_from_date'])
							{
								if( isset( $name['category_id'] ) )
								{
									$cat = $name['category_id'];

									$rtwwdpd_catids = '';
									if( isset($cart_itemm['variation_id']) && !empty($cart_itemm['variation_id']) )
									{
										$rtwwdpd_catids = wp_get_post_terms( $cart_itemm['data']->get_parent_id(), 'product_cat', array( 'fields' => 'ids' ) );
									}else{
										$rtwwdpd_catids = wp_get_post_terms( $cart_itemm['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) );
									}

									if( in_array($cat, $rtwwdpd_catids) )
									{
										if($name['rtwwdpd_dscnt_cat_type'] == 'rtwwdpd_discount_percentage')
										{
											
											$rtwwdpd_dscnt_price = $name['rtwwdpd_dscnt_cat_val'];
											$rtwwdpd_new_price = $rtwwdpd_product_price * ($rtwwdpd_dscnt_price/100);
											if( $rtwwdpd_new_price > $name['rtwwdpd_max_discount'] ){
												$rtwwdpd_new_price = $name['rtwwdpd_max_discount'];
											}
											if($rtwwdpd_new_price > $rtwwdpd_compared_price)
											{
												$rtwwdpd_compared_price = $rtwwdpd_new_price;
												$rtwwdpd_best_dis_arr['name'] = $rule_name;
												$rtwwdpd_best_dis_arr['item_key'] = $cart_item_key;
												$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
												$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
											}
										}
										elseif($name['rtwwdpd_dscnt_cat_type'] == 'rtwwdpd_fixed_price')
										{
											$rtwwdpd_flat_price = ( $name['rtwwdpd_dscnt_cat_val'] / $cart_itemm['quantity'] );
											if($rtwwdpd_flat_price > $name['rtwwdpd_max_discount']){
												$rtwwdpd_flat_price = $name['rtwwdpd_max_discount'];
											}
											if($rtwwdpd_flat_price > $rtwwdpd_compared_price)
											{
												$rtwwdpd_compared_price = $rtwwdpd_flat_price;
												$rtwwdpd_best_dis_arr['name'] = $rule_name;
												$rtwwdpd_best_dis_arr['item_key'] = $cart_item_key;
												$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
												$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
											}
										}
										else
										{
											$rtwwdpd_flat_price = $name['rtwwdpd_dscnt_cat_val'];
											if($rtwwdpd_flat_price > $name['rtwwdpd_max_discount']){
												$rtwwdpd_flat_price = $name['rtwwdpd_max_discount'];
											}
											if($rtwwdpd_flat_price > $rtwwdpd_compared_price)
											{
												$rtwwdpd_compared_price = $rtwwdpd_flat_price;
												$rtwwdpd_best_dis_arr['name'] = $rule_name;
												$rtwwdpd_best_dis_arr['item_key'] = $cart_item_key;
												$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
												$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
											}
										}
									}
								}
							}
						}
					}
				}
			}
			if($rule_name == 'cat_com_rule_row')
			{
				if(isset($rtwwdpd_offers['cat_com_rule']))
				{

					$rtwwdpd_prod_id = $cart_itemm['data']->get_id();
					$rtwwdpd_product_price = $this->rtw_get_price_to_discount( $cart_itemm, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_tot als', false ) );
					$rtwwdpd_rule_name = get_option('rtwwdpd_combi_cat_rule');

					$rtwwdpd_cat_id = wp_get_post_terms( $rtwwdpd_prod_id, 'product_cat', array( 'fields' => 'ids' ) );
					
					if( is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
					{

						foreach ($rtwwdpd_rule_name as $name) {
							$rtwwdpd_total_quantity = 0;
							$rtwwdpd_total_quant_in_rul = 0;

							if( is_array($name['category_id']) && !empty($name['category_id']) )
							{
								foreach($name['category_id'] as $cati => $catid)
								{
									$rtwwdpd_cat_idss[] = $catid;
									$rtwwdpd_total_quant_in_rul += $name['combi_quant'][$cati];
								}
							}
							$rtwwdpd_user_role = $name['rtwwdpd_select_roles_com'] ;

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

							if( is_array($rtwwdpd_temp_cart) && !empty($rtwwdpd_temp_cart) )
							{
								foreach ( $rtwwdpd_temp_cart as $cart_item_keys => $cart_items )
								{
									foreach ((wp_get_post_terms( $cart_items['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) )) as $key => $value) {
										$rtwwdpd_temp_cat_ids[] = $value;
									}
								}

								foreach ( $rtwwdpd_temp_cart as $cart_item_keys => $cart_items )
								{
									$arr = wp_get_post_terms( $cart_items['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) );
									if( array_intersect( $arr, $rtwwdpd_cat_idss ) )
									{

										$rtwwdpd_total_quantity += $cart_items['quantity'];
										
									}
								}
							}

							if( isset( $name['rtw_exe_product_tags'] ) && is_array( $name['rtw_exe_product_tags'] ) && !empty( $name['rtw_exe_product_tags'] ) )
							{
								$rtw_matched = array_intersect( $name['rtw_exe_product_tags'], $cart_itemm['data']->get_tag_ids());

								if( !empty( $rtw_matched ) )
								{
									continue 1;
								}
							}

							$rtwwdpd_date = $name['rtwwdpd_combi_to_date'];
							if($rtwwdpd_date >= $rtwwdpd_today_date && $rtwwdpd_today_date >= $name['rtwwdpd_combi_from_date']) 
							{
								if(is_array($name['category_id']) && !empty($name['category_id']))
								{
									foreach ($name['category_id'] as $keys => $val) {
										if( in_array( $val , $rtwwdpd_cat_id ))
										{	
											if( $rtwwdpd_total_quant_in_rul <= $rtwwdpd_total_quantity )
											{
												if($name['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
												{
													$rtwwdpd_dscnt_price = $name['rtwwdpd_discount_value'];

													$rtwwdpd_new_price = ( $rtwwdpd_product_price * ($rtwwdpd_dscnt_price/100) );

													if($rtwwdpd_new_price > $name['rtwwdpd_max_discount']){
														$rtwwdpd_new_price = $name['rtwwdpd_max_discount'];
													}

													if($rtwwdpd_new_price > $rtwwdpd_compared_price)
													{
														$rtwwdpd_compared_price = $rtwwdpd_new_price;
														$rtwwdpd_best_dis_arr['name'] = $rule_name;
														$rtwwdpd_best_dis_arr['item_key'] = $cart_item_key;
														$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
														$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
													}
												}
												elseif($name['rtwwdpd_discount_type'] == 'rtwwdpd_fixed_price')
												{
													$rtwwdpd_flat_price = ( $name['rtwwdpd_discount_value'] / $cart_itemm['quantity'] );
													if($rtwwdpd_flat_price > $name['rtwwdpd_max_discount']){
														$rtwwdpd_flat_price = $name['rtwwdpd_max_discount'];
													}
													if($rtwwdpd_flat_price > $rtwwdpd_compared_price)
													{
														$rtwwdpd_compared_price = $rtwwdpd_flat_price;
														$rtwwdpd_best_dis_arr['name'] = $rule_name;
														$rtwwdpd_best_dis_arr['item_key'] = $cart_item_key;
														$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
														$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
													}
												}
												else
												{
													$rtwwdpd_flat_price = $name['rtwwdpd_discount_value'];
													if($rtwwdpd_flat_price > $name['rtwwdpd_max_discount']){
														$rtwwdpd_flat_price = $name['rtwwdpd_max_discount'];
													}
													if($rtwwdpd_flat_price > $rtwwdpd_compared_price)
													{
														$rtwwdpd_compared_price = $rtwwdpd_flat_price;
														$rtwwdpd_best_dis_arr['name'] = $rule_name;
														$rtwwdpd_best_dis_arr['item_key'] = $cart_item_key;
														$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
														$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
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
			if($rule_name == 'pro_rule_row')
			{
				if( isset($rtwwdpd_offers['pro_rule']))
				{
					$rtwwdpd_cart_prod_count = $woocommerce->cart->cart_contents;
					$rtwwdpd_prod_count = 0;
					if( is_array($rtwwdpd_cart_prod_count) && !empty($rtwwdpd_cart_prod_count) )
					{
						foreach ($rtwwdpd_cart_prod_count as $key => $value) {
							$rtwwdpd_prod_count += $value['quantity'];
						}
					}
					$rtwwdpd_prod_id = $cart_itemm['data']->get_id();
					$rtwwdpd_product_price = $this->rtw_get_price_to_discount( $cart_itemm, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ) );
					$rtwwdpd_rule_name = get_option('rtwwdpd_single_prod_rule');
					if( is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
					{
						$active_dayss = get_site_option('rtwwdpd_discount_on_selected_days', 'no');

						foreach ($rtwwdpd_rule_name as $name) {

							if($active_dayss == 'yes')
							{
								
								$active_days = isset($name['rtwwwdpd_prod_day']) ? $name['rtwwwdpd_prod_day'] : array();
								$current_day = date('N');

								if(!in_array($current_day, $active_days))
								{
									
									continue;
								}
							}

							$rtwwdpd_user_role = $name['rtwwdpd_select_roles'] ;
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
							$rtwwdpd_date = $name['rtwwdpd_single_to_date'];
							if($rtwwdpd_date >= $rtwwdpd_today_date && $rtwwdpd_today_date >= $name['rtwwdpd_single_from_date'])
							{
								if($name['rtwwdpd_rule_on'] == 'rtwwdpd_products')
								{
								   
									$rtwwdpd_id = $name['product_id'];
									if($rtwwdpd_id == $rtwwdpd_prod_id)
									{
										if($name['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
										{
											$rtwwdpd_dscnt_price = $name['rtwwdpd_discount_value'];
											
											$rtwwdpd_new_price = $rtwwdpd_product_price * ($rtwwdpd_dscnt_price/100);
											if($rtwwdpd_new_price > $name['rtwwdpd_max_discount']){
												$rtwwdpd_new_price = $name['rtwwdpd_max_discount'];
											}
											if($rtwwdpd_new_price > $rtwwdpd_compared_price)
											{
												$rtwwdpd_compared_price = $rtwwdpd_new_price;
												$rtwwdpd_best_dis_arr['name'] = $rule_name;
												$rtwwdpd_best_dis_arr['item_key'] = $cart_item_key;
												$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
												$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
											}
										}
										elseif($name['rtwwdpd_discount_type'] == 'rtwwdpd_fixed_price')
										{
											$rtwwdpd_flat_price = ( $name['rtwwdpd_discount_value'] / $cart_itemm['quantity'] );
											if($rtwwdpd_flat_price > $name['rtwwdpd_max_discount']){
												$rtwwdpd_flat_price = $name['rtwwdpd_max_discount'];
											}
											if($rtwwdpd_flat_price > $rtwwdpd_compared_price)
											{
												$rtwwdpd_compared_price = $rtwwdpd_flat_price;
												$rtwwdpd_best_dis_arr['name'] = $rule_name;
												$rtwwdpd_best_dis_arr['item_key'] = $cart_item_key;
												$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
												$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
											}
										}
										else
										{
											$rtwwdpd_flat_price = $name['rtwwdpd_discount_value'];
											if($rtwwdpd_flat_price > $name['rtwwdpd_max_discount']){
												$rtwwdpd_flat_price = $name['rtwwdpd_max_discount'];
											}
											if($rtwwdpd_flat_price > $rtwwdpd_compared_price)
											{
												$rtwwdpd_compared_price = $rtwwdpd_flat_price;
												$rtwwdpd_best_dis_arr['name'] = $rule_name;
												$rtwwdpd_best_dis_arr['item_key'] = $cart_item_key;
												$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
												$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
											}
										}
									}
								}
								elseif($name['rtwwdpd_rule_on'] == 'rtwwdpd_multiple_products')
								{

								}
								elseif($name['rtwwdpd_rule_on'] == 'rtwwdpd_cart')
								{
									if($name['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
									{
										$rtwwdpd_dscnt_price = $name['rtwwdpd_discount_value'];
										$rtwwdpd_new_price = $rtwwdpd_product_price * ($rtwwdpd_dscnt_price/100);
										if($rtwwdpd_new_price > $name['rtwwdpd_max_discount']){
											$rtwwdpd_new_price = $name['rtwwdpd_max_discount'];
										}
										if($rtwwdpd_new_price > $rtwwdpd_compared_price)
										{
											$rtwwdpd_compared_price = $rtwwdpd_new_price;
											$rtwwdpd_best_dis_arr['name'] = $rule_name;
											$rtwwdpd_best_dis_arr['item_key'] = $cart_item_key;
											$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
											$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
										}
									}
									elseif($name['rtwwdpd_discount_type'] == 'rtwwdpd_fixed_price')
									{
										$rtwwdpd_flat_price = ( $name['rtwwdpd_discount_value'] / $cart_itemm['quantity'] );

										if($rtwwdpd_flat_price > $name['rtwwdpd_max_discount']){
											$rtwwdpd_flat_price = $name['rtwwdpd_max_discount'];
										}
										$rtwwdpd_flat_price = ( $rtwwdpd_flat_price / $rtwwdpd_prod_count );

										if($rtwwdpd_flat_price > $rtwwdpd_compared_price)
										{
											$rtwwdpd_compared_price = $rtwwdpd_flat_price;
											$rtwwdpd_best_dis_arr['name'] = $rule_name;
											$rtwwdpd_best_dis_arr['item_key'] = $cart_item_key;
											$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
											$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
										}
									}
									else
									{
										$rtwwdpd_flat_price = $name['rtwwdpd_discount_value'];

										if($rtwwdpd_flat_price > $name['rtwwdpd_max_discount']){
											$rtwwdpd_flat_price = $name['rtwwdpd_max_discount'];
										}
										$rtwwdpd_flat_price = ( $rtwwdpd_flat_price / $rtwwdpd_prod_count );

										if($rtwwdpd_flat_price > $rtwwdpd_compared_price)
										{
											$rtwwdpd_compared_price = $rtwwdpd_flat_price;
											$rtwwdpd_best_dis_arr['name'] = $rule_name;
											$rtwwdpd_best_dis_arr['item_key'] = $cart_item_key;
											$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
											$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
										}
									}
								}
							}
						}
					}
				}
			}
			if($rule_name == 'pro_com_rule_row')
			{
				if(isset($rtwwdpd_offers['pro_com_rule']))
				{
					$rtwwdpd_prod_id = $cart_itemm['data']->get_id();

					$rtwwdpd_product_price = $this->rtw_get_price_to_discount( $cart_itemm, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ) );
					$rtwwdpd_rule_name = get_option('rtwwdpd_combi_prod_rule');
					
					if( is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name) )
					{
						foreach ($rtwwdpd_rule_name as $name) {

							$rtwwdpd_user_role = $name['rtwwdpd_select_roles_com'] ;

							$rtwwdpd_role_matched = false;
							if( is_array($rtwwdpd_user_role) && !empty($rtwwdpd_user_role))
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

							$rtwwdpd_date = $name['rtwwdpd_combi_to_date'];
							if( $rtwwdpd_date >= $rtwwdpd_today_date && $rtwwdpd_today_date >= $name['rtwwdpd_combi_from_date'] )
							{
								if( is_array($name['product_id']) && !empty($name['product_id']) )
								{
									$both_quantity = 0;
									$both_ids 	=	array();

									if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
										foreach ( WC()->cart->get_cart() as $cart_item_k => $valid ) {
											foreach($name['product_id'] as $na => $kid )
											{ 
												if($kid == $valid['data']->get_id())
												{
													$both_ids[] = $valid['data']->get_id();
													$both_quantity += $valid['quantity'];
												}

											}
										}
									}
									$givn_quanty = 0;
									foreach ($name['combi_quant'] as $quants) {
										$givn_quanty += $quants;
									}

									$rslt = array();
									$rslt = array_diff($name['product_id'], $both_ids );
									
									if( !empty($rslt) )
									{
										continue 1;
									}
									if( $givn_quanty > $both_quantity )
									{
										continue 1;
									}

									foreach ($name['product_id'] as $keys => $val) {
										if($val == $rtwwdpd_prod_id)
										{
											if($name['rtwwdpd_combi_discount_type'] == 'rtwwdpd_discount_percentage')
											{
												$rtwwdpd_dscnt_price = $name['rtwwdpd_combi_discount_value'];
												$rtwwdpd_new_price = $rtwwdpd_product_price * ($rtwwdpd_dscnt_price/100);
												if($rtwwdpd_new_price > $name['rtwwdpd_combi_max_discount']){
													$rtwwdpd_new_price = $name['rtwwdpd_combi_max_discount'];
												}
												if($rtwwdpd_new_price > $rtwwdpd_compared_price)
												{
													$rtwwdpd_compared_price = $rtwwdpd_new_price;
													$rtwwdpd_best_dis_arr['name'] = $rule_name;
													$rtwwdpd_best_dis_arr['item_key'] = $cart_item_key;
													$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
													$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
												}
											}
											elseif($name['rtwwdpd_combi_discount_type'] == 'rtwwdpd_fixed_price')
											{
												$rtwwdpd_flat_price = ( $name['rtwwdpd_combi_discount_value'] / $cart_itemm['quantity'] );
												if($rtwwdpd_flat_price > $name['rtwwdpd_combi_max_discount']){
													$rtwwdpd_flat_price = $name['rtwwdpd_combi_max_discount'];
												}
												if($rtwwdpd_flat_price > $rtwwdpd_compared_price)
												{
													$rtwwdpd_compared_price = $rtwwdpd_flat_price;
													$rtwwdpd_best_dis_arr['name'] = $rule_name;
													$rtwwdpd_best_dis_arr['item_key'] = $cart_item_key;
													$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
													$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
												}
											}
											else
											{
												$rtwwdpd_flat_price = $name['rtwwdpd_combi_discount_value'];
												if($rtwwdpd_flat_price > $name['rtwwdpd_combi_max_discount']){
													$rtwwdpd_flat_price = $name['rtwwdpd_combi_max_discount'];
												}
												if($rtwwdpd_flat_price > $rtwwdpd_compared_price)
												{
													$rtwwdpd_compared_price = $rtwwdpd_flat_price;
													$rtwwdpd_best_dis_arr['name'] = $rule_name;
													$rtwwdpd_best_dis_arr['item_key'] = $cart_item_key;
													$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
													$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
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
			if($rule_name == 'tier_rule_row')
			{
				if(isset($rtwwdpd_offers['tier_rule']))
				{
					$rtwwdpd_prod_id = $cart_itemm['data']->get_id();
					$rtwwdpd_product_price = $this->rtw_get_price_to_discount( $cart_itemm, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ) );
					$rtwwdpd_rule_name = get_option('rtwwdpd_tiered_rule');
					if( is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
					{
						foreach ($rtwwdpd_rule_name as $name) {
							$rtwwdpd_user_role = $name['rtwwdpd_select_roles'] ;
							$rtwwdpd_role_matched = false;
							if( is_array($rtwwdpd_user_role) && !empty($rtwwdpd_user_role))
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
							if($name['rtwwdpd_to_date'] >= $rtwwdpd_today_date && $rtwwdpd_today_date >= $name['rtwwdpd_from_date']){
								if( is_array($name['products']) && !empty($name['products']))
								{
									foreach ($name['products'] as $keys => $vals) 
									{
										if($vals == $rtwwdpd_prod_id)
										{
											if($name['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
											{
												foreach ($name['quant_min'] as $k => $va) {
													if( $va <= $cart_itemm['quantity'])
													{
														$rtwwdpd_dscnt_price = $name['discount_val'][$k];
														$rtwwdpd_new_price = $rtwwdpd_product_price * ($rtwwdpd_dscnt_price/100);
														if($rtwwdpd_new_price > $name['rtwwdpd_max_discount']){
															$rtwwdpd_new_price = $name['rtwwdpd_max_discount'];
														}
														if($rtwwdpd_new_price > $rtwwdpd_compared_price)
														{
															$rtwwdpd_compared_price = $rtwwdpd_new_price;
															$rtwwdpd_best_dis_arr['name'] = $rule_name;
															$rtwwdpd_best_dis_arr['item_key'] = $cart_item_key;
															$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
															$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
														}
													}
												}
											}
											elseif($name['rtwwdpd_discount_type'] == 'rtwwdpd_flat_discount_amount')
											{
												foreach ($name['quant_min'] as $k => $va) {
													if( $va <= $cart_itemm['quantity'])
													{
														$rtwwdpd_flat_price = $name['discount_val'][$k];
														if($rtwwdpd_flat_price > $name['rtwwdpd_max_discount']){
															$rtwwdpd_flat_price = $name['rtwwdpd_max_discount'];
														}
														if($rtwwdpd_flat_price > $rtwwdpd_compared_price)
														{
															$rtwwdpd_compared_price = $rtwwdpd_flat_price;
															$rtwwdpd_best_dis_arr['name'] = $rule_name;
															$rtwwdpd_best_dis_arr['item_key'] = $cart_item_key;
															$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
															$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
														}
													}
												}
											}
											else
											{
												foreach ($name['quant_min'] as $k => $va) {
													if( $va <= $cart_itemm['quantity'])
													{
														$rtwwdpd_flat_price = ( $name['discount_val'][$k] / $cart_itemm['quantity'] );
														if($rtwwdpd_flat_price > $name['rtwwdpd_max_discount']){
															$rtwwdpd_flat_price = $name['rtwwdpd_max_discount'];
														}
														if($rtwwdpd_flat_price > $rtwwdpd_compared_price)
														{
															$rtwwdpd_compared_price = $rtwwdpd_flat_price;
															$rtwwdpd_best_dis_arr['name'] = $rule_name;
															$rtwwdpd_best_dis_arr['item_key'] = $cart_item_key;
															$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
															$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
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
			if($rule_name == 'tier_cat_rule_row')
			{
				if(isset($rtwwdpd_offers['tier_cat_rule']))
				{
					$rtwwdpd_prod_id = $cart_itemm['data']->get_id();
					$rtwwdpd_product_price = $this->rtw_get_price_to_discount( $cart_itemm, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ) );
					$rtwwdpd_rule_name = get_option('rtwwdpd_tiered_cat');
					if( isset($cart_itemm['variation_id']) && !empty($cart_itemm['variation_id']) )
					{
						$rtwwdpd_cat_id = wp_get_post_terms( $cart_itemm['data']->get_parent_id(), 'product_cat', array( 'fields' => 'ids' ) );
					}else{
						$rtwwdpd_cat_id = wp_get_post_terms( $cart_itemm['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) );
					}
					// $rtwwdpd_cat_id = $cart_itemm['data']->get_category_ids();
					$rtwwdpd_product_cat_id = '';
					foreach ($rtwwdpd_cat_id as $k => $cid) {
						$rtwwdpd_product_cat_id = $cid;
					}

					if( is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
					{
						foreach ($rtwwdpd_rule_name as $ke => $name) 
						{
							$rtwwdpd_user_role = $name['rtwwdpd_select_roles'] ;
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
							
							if(is_array($name['category_id']) && !empty($name['category_id']))
							{
								foreach ($name['category_id'] as $keys => $vals) 
								{
									if(in_array($vals, $rtwwdpd_cat_id))
									{
										if($name['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
										{
											foreach ($name['quant_min'] as $k => $va) {
												$rtwwdpd_dscnt_price = $name['discount_val'][$k];
												if( $va <= $cart_itemm['quantity'])
												{
													$rtwwdpd_new_price = $rtwwdpd_product_price * ($rtwwdpd_dscnt_price/100);
													if($rtwwdpd_new_price > $name['rtwwdpd_max_discount']){
														$rtwwdpd_new_price = $name['rtwwdpd_max_discount'];
													}
													if($rtwwdpd_new_price > $rtwwdpd_compared_price)
													{
														$rtwwdpd_compared_price = $rtwwdpd_new_price;
														$rtwwdpd_best_dis_arr['name'] = $rule_name;
														$rtwwdpd_best_dis_arr['item_key'] = $cart_item_key;
														$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
														$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
													}
												}
											}
										}
										elseif($name['rtwwdpd_discount_type'] == 'rtwwdpd_flat_discount_amount')
										{
											foreach ($name['quant_min'] as $k => $va) {
												if( $va <= $cart_itemm['quantity'])
												{
													$rtwwdpd_flat_price = $name['discount_val'][$k];
												}
												if($rtwwdpd_flat_price > $name['rtwwdpd_max_discount']){
													$rtwwdpd_flat_price = $name['rtwwdpd_max_discount'];
												}
												if($rtwwdpd_flat_price > $rtwwdpd_compared_price)
												{
													$rtwwdpd_compared_price = $rtwwdpd_flat_price;
													$rtwwdpd_best_dis_arr['name'] = $rule_name;
													$rtwwdpd_best_dis_arr['item_key'] = $cart_item_key;
													$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
													$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
												}
											}
										}
										else
										{
											foreach ($name['quant_min'] as $k => $va) {
												if( $va <= $cart_itemm['quantity'])
												{
													$rtwwdpd_flat_price = ( $name['discount_val'][$k] / $cart_itemm['quantity'] );
												}
												if($rtwwdpd_flat_price > $name['rtwwdpd_max_discount']){
													$rtwwdpd_flat_price = $name['rtwwdpd_max_discount'];
												}
												if($rtwwdpd_flat_price > $rtwwdpd_compared_price)
												{
													$rtwwdpd_compared_price = $rtwwdpd_flat_price;
													$rtwwdpd_best_dis_arr['name'] = $rule_name;
													$rtwwdpd_best_dis_arr['item_key'] = $cart_item_key;
													$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
													$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
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
			if($rule_name == 'attr_rule_row')
			{
				global $post;

				$rtwwdpd_prod_id = $cart_itemm['data']->get_id();
				$rtwwdpd_product = $cart_itemm['data'];
				
				$rtwwdpd_product_price = $this->rtw_get_price_to_discount( $cart_itemm, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ) );

				if(isset($rtwwdpd_offers['attr_rule']))
				{
					$rtwwdpd_rule_name = get_option('rtwwdpd_att_rule');
					if( is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
					{	
						foreach ($rtwwdpd_rule_name as $ke => $name) 
						{
							$rtwwdpd_attr = array();
							if( !empty($rtwwdpd_product->get_parent_id()) )
							{
								$rtwwdpd_attr = wc_get_product($rtwwdpd_product->get_parent_id())->get_attributes();
							}
							else{
								$rtwwdpd_attr = $rtwwdpd_product->get_attributes();
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
							$attribut_val = isset($name['rtwwdpd_attribute_val']) ? $name['rtwwdpd_attribute_val'] : array();
							$rtwwdpd_arr = array_intersect( $attr_ids, $attribut_val );

							if(is_array($rtwwdpd_arr) && empty($rtwwdpd_arr))
							{
								continue 1;
							}
							if(isset($name['product_exe_id']) && $name['product_exe_id'] == $rtwwdpd_product->get_id())
							{
								continue 1;
							}
							if(isset($name['rtwwdpd_att_exclude_sale']) && $name['rtwwdpd_att_exclude_sale'] == 'yes' )
							{
								if( $rtwwdpd_product->is_on_sale() )
								{
									continue 1;
								}
							}
				
							if($name['rtwwdpd_att_to_date'] >= $rtwwdpd_today_date && $rtwwdpd_today_date >= $name['rtwwdpd_att_from_date'])
							{
								
								$i = 0;
								if($name['rtwwdpd_att_discount_type'] == 'rtwwdpd_discount_percentage')
								{	
									$rtwwdpd_dscnt_price = $name['rtwwdpd_att_discount_value'];

									$rtwwdpd_new_price = $rtwwdpd_product_price * ($rtwwdpd_dscnt_price/100);
									if($rtwwdpd_new_price > $name['rtwwdpd_att_max_discount']){
										$rtwwdpd_new_price = $name['rtwwdpd_att_max_discount'];
									}
									if($rtwwdpd_new_price > $rtwwdpd_compared_price)
									{
										$rtwwdpd_compared_price = $rtwwdpd_new_price;
										$rtwwdpd_best_dis_arr['name'] = $rule_name;
										$rtwwdpd_best_dis_arr['item_key'] = $cart_item_key;
										$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
										$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
									}
								}
								elseif($name['rtwwdpd_att_discount_type'] == 'rtwwdpd_fixed_price')
								{
									$rtwwdpd_flat_price = ( $name['rtwwdpd_att_discount_value'] / $cart_itemm['quantity'] );

									if($rtwwdpd_flat_price > $name['rtwwdpd_att_max_discount']){
										$rtwwdpd_flat_price = $name['rtwwdpd_att_max_discount'];
									}
									if($rtwwdpd_flat_price > $rtwwdpd_compared_price)
									{
										$rtwwdpd_compared_price = $rtwwdpd_flat_price;
										$rtwwdpd_best_dis_arr['name'] = $rule_name;
										$rtwwdpd_best_dis_arr['item_key'] = $cart_item_key;
										$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
										$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
									}
								}
								else
								{
									$rtwwdpd_flat_price = $name['rtwwdpd_att_discount_value'];
									if($rtwwdpd_flat_price > $name['rtwwdpd_att_max_discount']){
										$rtwwdpd_flat_price = $name['rtwwdpd_att_max_discount'];
									}
									if($rtwwdpd_flat_price > $rtwwdpd_compared_price)
									{
										$rtwwdpd_compared_price = $rtwwdpd_flat_price;
										$rtwwdpd_best_dis_arr['name'] = $rule_name;
										$rtwwdpd_best_dis_arr['item_key'] = $cart_item_key;
										$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
										$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
									}
								}
							}
						}
					}
				}
			}
			if($rule_name == 'prod_tag_rule_row')
			{	
				$rtwwdpd_product = wc_get_product();
				$rtwwdpd_prod_id = $cart_itemm['data']->get_id();
				$rtwwdpd_terms = get_terms( 'product_tag' );
				$rtwwdpd_term_array = array();
				$rtwwdpd_terms = get_the_terms( $cart_itemm['data']->get_id(), 'product_tag' );

				if ( ! empty( $rtwwdpd_terms ) && ! is_wp_error( $rtwwdpd_terms ) ){
					foreach ( $rtwwdpd_terms as $term ) {
						$rtwwdpd_term_array[] = $term->term_id;
					}
				}

				$rtwwdpd_product_price = $this->rtw_get_price_to_discount( $cart_itemm, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ) );

				if( is_array($rtwwdpd_term_array) && !empty($rtwwdpd_term_array))
				{
					if(isset($rtwwdpd_offers['prod_tag_rule']))
					{
						$rtwwdpd_rule_name = get_option('rtwwdpd_tag_method');
						if( is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
						{
							foreach ($rtwwdpd_rule_name as $ke => $name) 
							{
								$rtwwdpd_user_role = $name['rtwwdpd_select_roles'] ;
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
								if($name['rtwwdpd_tag_to_date'] >= $rtwwdpd_today_date && $rtwwdpd_today_date >= $name['rtwwdpd_tag_from_date'])
								{
									if( is_array($name['rtw_product_tags']) && !empty($name['rtw_product_tags']) )
									{
										foreach ($name['rtw_product_tags'] as $tag => $tags) 
										{	
											if( isset( $name['rtw_product_tags'] ) && is_array( $name['rtw_product_tags'] ) && !empty( $name['rtw_product_tags'] ) )
											{
												$rtw_matched = array_intersect( $name['rtw_product_tags'], $cart_itemm['data']->get_tag_ids() );

												if( empty( $rtw_matched ) )
												{
													continue 1;
												}
											}
											if(in_array($tags, $rtwwdpd_term_array))
											{ 
												if($name['rtwwdpd_tag_discount_type'] == 'rtwwdpd_discount_percentage')
												{
													$rtwwdpd_dscnt_price = $name['rtwwdpd_tag_discount_value'];

													$rtwwdpd_new_price = $rtwwdpd_product_price * ($rtwwdpd_dscnt_price/100);
													if($rtwwdpd_new_price > $name['rtwwdpd_tag_max_discount']){
														$rtwwdpd_new_price = $name['rtwwdpd_tag_max_discount'];
													}
													if($rtwwdpd_new_price > $rtwwdpd_compared_price)
													{
														$rtwwdpd_compared_price = $rtwwdpd_new_price;
														$rtwwdpd_best_dis_arr['name'] = $rule_name;
														$rtwwdpd_best_dis_arr['item_key'] = $cart_item_key;
														$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
														$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
													}
												}
												elseif($name['rtwwdpd_tag_discount_type'] == 'rtwwdpd_flat_discount_amount')
												{
													$rtwwdpd_flat_price = $name['rtwwdpd_tag_discount_value'];
													if($rtwwdpd_flat_price > $name['rtwwdpd_tag_max_discount']){
														$rtwwdpd_flat_price = $name['rtwwdpd_tag_max_discount'];
													}
													if($rtwwdpd_flat_price > $rtwwdpd_compared_price)
													{
														$rtwwdpd_compared_price = $rtwwdpd_flat_price;
														$rtwwdpd_best_dis_arr['item_key'] = $cart_item_key;
														$rtwwdpd_best_dis_arr['name'] = $rule_name;
														$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
														$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
													}
												}
												else
												{
													$rtwwdpd_flat_price = ( $name['rtwwdpd_tag_discount_value'] / $cart_itemm['quantity'] );
													if($rtwwdpd_flat_price > $name['rtwwdpd_tag_max_discount']){
														$rtwwdpd_flat_price = $name['rtwwdpd_tag_max_discount'];
													}
													if($rtwwdpd_flat_price > $rtwwdpd_compared_price)
													{
														$rtwwdpd_compared_price = $rtwwdpd_flat_price;
														$rtwwdpd_best_dis_arr['item_key'] = $cart_item_key;
														$rtwwdpd_best_dis_arr['name'] = $rule_name;
														$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
														$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
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
		$rtwwdpd_price_adjusted = $rtwwdpd_product_price - $rtwwdpd_compared_price;

		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_product_price, $rtwwdpd_price_adjusted, 'rtw_best_match', $set_id );
	}	
}