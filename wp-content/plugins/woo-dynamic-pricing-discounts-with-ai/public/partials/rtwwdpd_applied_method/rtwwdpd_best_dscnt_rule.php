<?php
$rtwwdpd_compared_price = 0;
$rtwwdpd_best_dis_arr = array();
$rtwwdpd_best_dis_arr['name'] = '';
$rtwwdpd_best_dis_arr['dscnt_value'] = '';
$rtwwdpd_best_dis_arr['original_price'] = '';
$rtwwdpd_best_dis_arr['discounted_price'] = '';
$rtwwdpd_best_dis_arr['discount_type'] = '';
if(is_array($rtwwdpd_priority) && !empty($rtwwdpd_priority))
{
	foreach ($rtwwdpd_priority as $rule => $rule_name) {

		if($rule_name == 'bogo_rule_row')
		{
			if(isset($rtwwdpd_offers['bogo_rule']))
			{
		
				$rtwwdpd_product_price = $rtwwdpd_product->get_price();
				$rtwwdpd_rule_name = get_option('rtwwdpd_bogo_rule');
				if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
				{
					foreach ($rtwwdpd_rule_name as $ke => $name) {
						$rtw_date = $name['rtwwdpd_bogo_to_date'];
						if($rtw_date >= $rtwwdpd_today_date && $rtwwdpd_today_date >= $name['rtwwdpd_bogo_from_date'] )
						{
							if(isset($name['product_id']))
							{
								foreach ($name['product_id'] as $no => $ids) {
									if($ids == $rtwwdpd_prod_id)
									{
										if( isset( $name['rtwbogo'][$no] ) )
										{
											$rtw_pro = wc_get_product( $name['rtwbogo'][$no] );
											$rtw_b_price = $rtw_pro->get_price();

											if($rtw_b_price > $rtwwdpd_compared_price)
											{
												$rtwwdpd_compared_price = $rtw_b_price;
												$rtwwdpd_best_dis_arr['name'] = $rule_name;
												$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
												$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
												$rtwwdpd_best_dis_arr['discount_type'] = 'bogo';
												$rtwwdpd_best_dis_arr['dscnt_value'] = ''; 
												$rtwwdpd_best_dis_arr['buyprod'] = $ids;
												$rtwwdpd_best_dis_arr['freeprod'] = $name['rtwbogo'][$no];
												$rtwwdpd_best_dis_arr['buyquant'] = $name['combi_quant'][$no];
												$rtwwdpd_best_dis_arr['freequant'] = $name['bogo_quant_free'][$no];
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
			$rtwwdpd_product_price = $rtwwdpd_product->get_price();
			if(isset($rtwwdpd_offers['bogo_cat_rule']))
			{
				$rtwwdpd_rule_name = get_option('rtwwdpd_bogo_cat_rule');
				
				if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
				{
					foreach ($rtwwdpd_rule_name as $keys => $name) 
					{	
						$rtwwdpd_date = $name['rtwwdpd_bogo_to_date'];
						if($rtwwdpd_date >= $rtwwdpd_today_date && $rtwwdpd_today_date >= $name['rtwwdpd_bogo_from_date']){
							if(isset($name['category_id']) && is_array($name['category_id']) && !empty($name['category_id']))
							{
								foreach ($name['category_id'] as $no => $cat)
								{
									if($cat == $rtwwdpd_product_cat_id){
										if( isset( $name['rtwbogo'][$no] ) )
										{
											$rtw_pro = wc_get_product($name['rtwbogo'][$no] );
											$rtw_b_price = $rtw_pro->get_price();

											if($rtw_b_price > $rtwwdpd_compared_price)
											{
												$rtwwdpd_compared_price = $rtw_b_price;
												$rtwwdpd_best_dis_arr['name'] = $rule_name;
												$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
												$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
												$rtwwdpd_best_dis_arr['discount_type'] = 'bogocat';
												$rtwwdpd_best_dis_arr['dscnt_value'] = '';
												$rtwwdpd_best_dis_arr['buyprod'] = $cat;
												$rtwwdpd_best_dis_arr['freeprod'] = $name['rtwbogo'][$no];
												$rtwwdpd_best_dis_arr['buyquant'] = $name['combi_quant'][$no];
												$rtwwdpd_best_dis_arr['freequant'] = $name['bogo_quant_free'][$no];
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
				$rtwwdpd_product_price = $rtwwdpd_product->get_price();
				$rtwwdpd_rule_name = get_option('rtwwdpd_single_cat_rule');
				if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
				{
					
					foreach ($rtwwdpd_rule_name as $name) {

						if( isset( $name['rtw_exe_product_tags'] ) && is_array( $name['rtw_exe_product_tags'] ) && !empty( $name['rtw_exe_product_tags'] ) )
						{
							
							$rtw_matched = array_intersect( $name['rtw_exe_product_tags'], $rtwwdpd_product->get_tag_ids());
								
							if( !empty( $rtw_matched ) )
							{
								continue 1;
							}
						}

						$rtwwdpd_date = $name['rtwwdpd_to_date'];
						if($rtwwdpd_date >= $rtwwdpd_today_date && $rtwwdpd_today_date >= $name['rtwwdpd_from_date'] )
						{
							if(isset($name['category_id']))
							{
								$cat = $name['category_id'];
								if($cat == $rtwwdpd_product_cat_id)
								{
									if($name['rtwwdpd_dscnt_cat_type'] == 'rtwwdpd_discount_percentage')
									{
										$rtwwdpd_dscnt_price = $name['rtwwdpd_dscnt_cat_val'];
										$rtwwdpd_new_price = $rtwwdpd_product_price * ($rtwwdpd_dscnt_price/100);
										if($rtwwdpd_new_price > $name['rtwwdpd_max_discount']){
											$rtwwdpd_new_price = $name['rtwwdpd_max_discount'];
										}
										if($rtwwdpd_new_price > $rtwwdpd_compared_price)
										{
											$rtwwdpd_compared_price = $rtwwdpd_new_price;
											$rtwwdpd_best_dis_arr['name'] = $rule_name;
											$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
											$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
											$rtwwdpd_best_dis_arr['discount_type'] = 'percent';
											$rtwwdpd_best_dis_arr['dscnt_value'] = $rtwwdpd_dscnt_price;
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
											$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
											$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
											$rtwwdpd_best_dis_arr['discount_type'] = 'flat';
											$rtwwdpd_best_dis_arr['dscnt_value'] = $rtwwdpd_flat_price;
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
				$rtwwdpd_product_price = $rtwwdpd_product->get_price();
				$rtwwdpd_rule_name = get_option('rtwwdpd_combi_cat_rule');
				if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
				{
					foreach ($rtwwdpd_rule_name as $name) {

						if( isset( $name['rtw_exe_product_tags'] ) && is_array( $name['rtw_exe_product_tags'] ) && !empty( $name['rtw_exe_product_tags'] ) )
						{
							
							$rtw_matched = array_intersect( $name['rtw_exe_product_tags'], $rtwwdpd_product->get_tag_ids());
								
							if( !empty( $rtw_matched ) )
							{
								continue 1;
							}
						}

						$rtwwdpd_date = $name['rtwwdpd_combi_to_date'];
						if($rtwwdpd_date >= $rtwwdpd_today_date && $rtwwdpd_today_date >= $name['rtwwdpd_combi_from_date'] )
						{
							if( $rtwwdpd_product->is_in_stock()) {
								if(isset($name['category_id']) && is_array($name['category_id']) && !empty($name['category_id']))
								{
									foreach ($name['category_id'] as $keys => $val) {
										if($val == $rtwwdpd_product_cat_id)
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
													$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
													$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
													$rtwwdpd_best_dis_arr['discount_type'] = 'percent';
													$rtwwdpd_best_dis_arr['dscnt_value'] = $rtwwdpd_dscnt_price;
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
													$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
													$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
													$rtwwdpd_best_dis_arr['discount_type'] = 'flat';
													$rtwwdpd_best_dis_arr['dscnt_value'] = $rtwwdpd_flat_price;
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
			if(isset($rtwwdpd_offers['pro_rule']))
			{
				$rtwwdpd_product_price = $rtwwdpd_product->get_price();
				$rtwwdpd_rule_name = get_option('rtwwdpd_single_prod_rule');
				if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
				{
					foreach ($rtwwdpd_rule_name as $name) 
					{
						$rtwwdpd_date = $name['rtwwdpd_single_to_date'];
						if($rtwwdpd_date >= $rtwwdpd_today_date && $rtwwdpd_today_date >= $name['rtwwdpd_single_from_date'])
						{
							if($name['rtwwdpd_rule_on'] == 'rtwwdpd_products')
							{
								$rtw_id = $name['product_id'];
								if($rtw_id == $rtwwdpd_prod_id)
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
											$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
											$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
											$rtwwdpd_best_dis_arr['discount_type'] = 'percent';
											$rtwwdpd_best_dis_arr['dscnt_value'] = $rtwwdpd_dscnt_price;
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
											$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
											$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
											$rtwwdpd_best_dis_arr['discount_type'] = 'flat';
											$rtwwdpd_best_dis_arr['dscnt_value'] = $rtwwdpd_flat_price;
										}
									}
								}
							}
							elseif($name['rtwwdpd_rule_on'] == 'rtwwdpd_multiple_products')
							{
							}
							else
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
										$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
										$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
										$rtwwdpd_best_dis_arr['discount_type'] = 'percent';
										$rtwwdpd_best_dis_arr['dscnt_value'] = $rtwwdpd_dscnt_price;
									}
								}
								elseif($name['rtwwdpd_discount_type'] == 'rtwwdpd_flat_discount_amount')
								{
									$rtwwdpd_flat_price = $name['rtwwdpd_discount_value'];
									if($rtwwdpd_flat_price > $name['rtwwdpd_max_discount']){
										$rtwwdpd_flat_price = $name['rtwwdpd_max_discount'];
									}
									if($rtwwdpd_flat_price > $rtwwdpd_compared_price)
									{
										$rtwwdpd_compared_price = $rtwwdpd_flat_price;
										$rtwwdpd_best_dis_arr['name'] = $rule_name;
										$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
										$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
										$rtwwdpd_best_dis_arr['discount_type'] = 'flat';
										$rtwwdpd_best_dis_arr['dscnt_value'] = $rtwwdpd_flat_price;
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
				$rtwwdpd_product_price = $rtwwdpd_product->get_price();
				$rtwwdpd_rule_name = get_option('rtwwdpd_combi_prod_rule');
				if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
				{
					foreach ($rtwwdpd_rule_name as $name) {
						$rtw_date = $name['rtwwdpd_combi_to_date'];
						if($rtw_date >= $rtwwdpd_today_date && $rtwwdpd_today_date >= $name['rtwwdpd_combi_from_date'])
						{
							if(isset($name['product_id']) && is_array($name['product_id']) && !empty($name['product_id']))
							{
								foreach ($name['product_id'] as $keys => $val) {
									if($val == $rtwwdpd_prod_id)
									{
										if($name['rtwwdpd_combi_discount_type'] == 'rtwwdpd_discount_percentage')
										{
											$rtwwdpd_dscnt_price = $name['rtwwdpd_combi_discount_value'];
											$rtwwdpd_new_price = $rtwwdpd_product_price - ($rtwwdpd_product_price * ($rtwwdpd_dscnt_price/100));
											if($rtwwdpd_new_price > $name['rtwwdpd_combi_max_discount']){
												$rtwwdpd_new_price = $name['rtwwdpd_combi_max_discount'];
											}
											if($rtwwdpd_new_price > $rtwwdpd_compared_price)
											{
												$rtwwdpd_compared_price = $rtwwdpd_new_price;
												$rtwwdpd_best_dis_arr['name'] = $rule_name;
												$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
												$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
												$rtwwdpd_best_dis_arr['discount_type'] = 'percent';
												$rtwwdpd_best_dis_arr['dscnt_value'] = $rtwwdpd_dscnt_price;
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
												$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
												$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
												$rtwwdpd_best_dis_arr['discount_type'] = 'flat';
												$rtwwdpd_best_dis_arr['dscnt_value'] = $rtwwdpd_flat_price;
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
				$rtwwdpd_product_price = $rtwwdpd_product->get_price();
				$rtwwdpd_rule_name = get_option('rtwwdpd_tiered_rule');
				if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
				{
					foreach ($rtwwdpd_rule_name as $name) {
						if($name['rtwwdpd_to_date'] >= $rtwwdpd_today_date && $rtwwdpd_today_date >= $name['rtwwdpd_from_date'] ){
							if(isset($name['products']) && is_array($name['products']) && !empty($name['products']))
							{
								foreach ($name['products'] as $keys => $vals) 
								{
									if($vals == $rtwwdpd_prod_id)
									{
										if($name['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
										{
											foreach ($name['quant_min'] as $k => $va) {
												
												$rtwwdpd_dscnt_price = $name['discount_val'][$k];

												$rtwwdpd_new_price = $rtwwdpd_product_price * ($rtwwdpd_dscnt_price/100);

												if($rtwwdpd_new_price > $name['rtwwdpd_max_discount']){
													$rtwwdpd_new_price = $name['rtwwdpd_max_discount'];
												}

												if($rtwwdpd_new_price > $rtwwdpd_compared_price)
												{
													$rtwwdpd_compared_price = $rtwwdpd_new_price;
													$rtwwdpd_best_dis_arr['name'] = $rule_name;
													$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
													$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
													$rtwwdpd_best_dis_arr['discount_type'] = 'tier_percent';
													$rtwwdpd_best_dis_arr['dscnt_value'] = $rtwwdpd_dscnt_price;
													$rtwwdpd_best_dis_arr['from'] = $va;
													$rtwwdpd_best_dis_arr['to'] = $name['quant_max'][$k];
													$rtwwdpd_best_dis_arr['product'] = $vals;

												}
											}
										}
										else
										{
											foreach ($name['quant_min'] as $k => $va) {

												$rtwwdpd_flat_price = $name['discount_val'][$k];

												if($rtwwdpd_flat_price > $name['rtwwdpd_max_discount']){
													$rtwwdpd_flat_price = $name['rtwwdpd_max_discount'];
												}

												if($rtwwdpd_flat_price > $rtwwdpd_compared_price)
												{
													$rtwwdpd_compared_price = $rtwwdpd_flat_price;
													$rtwwdpd_best_dis_arr['name'] = $rule_name;
													$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
													$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
													$rtwwdpd_best_dis_arr['discount_type'] = 'tier_flat';
													$rtwwdpd_best_dis_arr['from'] = $va;
													$rtwwdpd_best_dis_arr['to'] = $name['quant_max'][$k];
													$rtwwdpd_best_dis_arr['dscnt_value'] = $rtwwdpd_flat_price;
													$rtwwdpd_best_dis_arr['product'] = $vals;

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
				$rtwwdpd_product_price = $rtwwdpd_product->get_price();
				$rtwwdpd_rule_name = get_option('rtwwdpd_tiered_cat');
				if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
				{
					foreach ($rtwwdpd_rule_name as $ke => $name) 
					{
						if(isset($name['category_id']) && is_array($name['category_id']) && !empty($name['category_id']) && is_array($name['quant_min']) && !empty($name['quant_min']))
						{
							foreach ($name['category_id'] as $keys => $vals) 
							{
								if($vals == $rtwwdpd_product_cat_id)
								{
									if($name['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
									{
										$rtwwdpd_dscnt_price = 0;
										foreach ($name['quant_min'] as $k => $va) {
											$rtwwdpd_dscnt_price = $name['discount_val'][$k];
										}
										
										if( !empty( $rtwwdpd_product_price ))
										{
											$rtwwdpd_new_price = $rtwwdpd_product_price * ( $rtwwdpd_dscnt_price/100 );

											if($rtwwdpd_new_price > $name['rtwwdpd_max_discount']){
												$rtwwdpd_new_price = $name['rtwwdpd_max_discount'];
											}
											if($rtwwdpd_new_price > $rtwwdpd_compared_price)
											{
												$rtwwdpd_compared_price = $rtwwdpd_new_price;
												$rtwwdpd_best_dis_arr['name'] = $rule_name;
												$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
												$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
												$rtwwdpd_best_dis_arr['discount_type'] = 'tierc_percent';
												$rtwwdpd_best_dis_arr['dscnt_value'] = $rtwwdpd_dscnt_price;
												$rtwwdpd_best_dis_arr['from'] = $va;
												$rtwwdpd_best_dis_arr['to'] = $name['quant_max'][$k];
											}
										}
									}
									else
									{
										foreach ($name['quant_min'] as $k => $va) {

											$rtwwdpd_flat_price = $name['discount_val'][$k];
										}
										if($rtwwdpd_flat_price > $name['rtwwdpd_max_discount']){
											$rtwwdpd_flat_price = $name['rtwwdpd_max_discount'];
										}
										if($rtwwdpd_flat_price > $rtwwdpd_compared_price)
										{
											$rtwwdpd_compared_price = $rtwwdpd_flat_price;
											$rtwwdpd_best_dis_arr['name'] = $rule_name;
											$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
											$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
											$rtwwdpd_best_dis_arr['discount_type'] = 'tierc_flat';
											$rtwwdpd_best_dis_arr['dscnt_value'] = $rtwwdpd_flat_price;
											$rtwwdpd_best_dis_arr['from'] = $va;
											$rtwwdpd_best_dis_arr['to'] = $name['quant_max'][$k];
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
			$rtwwdpd_colors = get_the_terms($post, 'pa_color');
			$rtwwdpd_sizes = get_the_terms($post, 'pa_size');
			
			$rtwwdpd_product_price = $rtwwdpd_product->get_price();
			if(isset($rtwwdpd_offers['attr_rule']))
			{
				$rtwwdpd_rule_name = get_option('rtwwdpd_att_rule');
				if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
				{	
					foreach ($rtwwdpd_rule_name as $ke => $name) 
					{
						if($name['rtwwdpd_att_to_date'] >= $rtwwdpd_today_date && $rtwwdpd_today_date >= $name['rtwwdpd_att_from_date'])
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
									$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
									$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
									$rtwwdpd_best_dis_arr['discount_type'] = 'percent';
									$rtwwdpd_best_dis_arr['dscnt_value'] = $rtwwdpd_dscnt_price;
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
									$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
									$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
									$rtwwdpd_best_dis_arr['discount_type'] = 'flat';
									$rtwwdpd_best_dis_arr['dscnt_value'] = $rtwwdpd_flat_price;
								}
							}
						}
					}
				}
			}
		}
		if($rule_name == 'prod_tag_rule_row')
		{
			$rtw_tag = wp_get_post_terms( get_the_id(), 'product_tag' );
			$rtwwdpd_product_price = $rtwwdpd_product->get_price();
			if(!empty($rtw_tag))
			{
				if(isset($rtwwdpd_offers['prod_tag_rule']))
				{
					$rtwwdpd_rule_name = get_option('rtwwdpd_tag_method');
					if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
					{
						foreach ($rtwwdpd_rule_name as $ke => $name) 
						{
							if($name['rtwwdpd_tag_to_date'] >= $rtwwdpd_today_date && $rtwwdpd_today_date >= $name['rtwwdpd_tag_from_date'])
							{
								if(isset($name['rtw_product_tags']) && is_array($name['rtw_product_tags']) && !empty($name['rtw_product_tags']))
								{	
									if( isset( $name['rtw_product_tags'] ) && is_array( $name['rtw_product_tags'] ) && !empty( $name['rtw_product_tags'] ) )
									{
										$rtw_matched = array_intersect( $name['rtw_product_tags'], $rtwwdpd_product->get_tag_ids());

										if( empty( $rtw_matched ) )
										{
											continue 1;
										}
									}

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
											$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
											$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
											$rtwwdpd_best_dis_arr['discount_type'] = 'percent';
											$rtwwdpd_best_dis_arr['dscnt_value'] = $rtwwdpd_dscnt_price;
										}
									}
									else
									{
										$rtwwdpd_flat_price = $name['rtwwdpd_tag_discount_value'];
										if($rtwwdpd_flat_price > $name['rtwwdpd_tag_max_discount']){
											$rtwwdpd_flat_price = $name['rtwwdpd_tag_max_discount'];
										}
										if($rtwwdpd_flat_price > $rtwwdpd_compared_price)
										{
											$rtwwdpd_compared_price = $rtwwdpd_flat_price;
											$rtwwdpd_best_dis_arr['name'] = $rule_name;
											$rtwwdpd_best_dis_arr['original_price'] = $rtwwdpd_product_price;
											$rtwwdpd_best_dis_arr['discounted_price'] = $rtwwdpd_compared_price;
											$rtwwdpd_best_dis_arr['discount_type'] = 'flat';
											$rtwwdpd_best_dis_arr['dscnt_value'] = $rtwwdpd_flat_price;
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

$rtwwdpd_dscnt_value = $rtwwdpd_best_dis_arr['dscnt_value'];
$rtwwdpd_get_content = get_option('rtwwdpd_setting_priority');
$rtwwdpd_text_to_show = '';
$rtwwdpd_bogo_text = '';
$rtwwdpd_tier_text_show = '';
$rtwwdpd_symbol = get_woocommerce_currency_symbol();
$rtwwdpd_categories = get_terms('product_cat', 'orderby=name&hide_empty=0');
$rtwwdpd_cats = array();
$rtwwdpd_weight_unit = get_option('woocommerce_weight_unit');
if (is_array($rtwwdpd_categories) && !empty($rtwwdpd_categories))  {
	foreach ($rtwwdpd_categories as $cat) {
		$rtwwdpd_cats[$cat->term_id] = $cat->name;
	}
}

if(!isset($rtwwdpd_get_content['rtwwdpd_text_to_show']) || $rtwwdpd_get_content['rtwwdpd_text_to_show'] == '')
{
	$rtwwdpd_text_to_show = 'Get [discounted] Off';
}
else{
	$rtwwdpd_text_to_show = $rtwwdpd_get_content['rtwwdpd_text_to_show'];
}

if(!isset($rtwwdpd_get_content['rtwwdpd_bogo_text']) || $rtwwdpd_get_content['rtwwdpd_bogo_text'] == '')
{
	$rtwwdpd_bogo_text = 'Buy [quantity1] [the-product] Get [quantity2] [free-product]';
}
else{
	$rtwwdpd_bogo_text = $rtwwdpd_get_content['rtwwdpd_bogo_text'];
}

if($rtwwdpd_best_dis_arr['discount_type'] == 'flat')
{
	if($rtwwdpd_compared_price != 0)
	{
		$rtwwdpd_text_to_show = str_replace('[discounted]', $rtwwdpd_symbol . $rtwwdpd_dscnt_value, $rtwwdpd_text_to_show);

		echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_text_to_show).'</span></div>';
	}
}
elseif($rtwwdpd_best_dis_arr['discount_type'] == 'percent'){
	if($rtwwdpd_compared_price != 0)
	{
		$rtwwdpd_text_to_show = str_replace('[discounted]', $rtwwdpd_dscnt_value.'%', $rtwwdpd_text_to_show);

		echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_text_to_show).'</span></div>';
	}
}
elseif ($rtwwdpd_best_dis_arr['discount_type'] == 'bogo') {

	$rtwwdpd_bogo_text = str_replace('[quantity1]', $rtwwdpd_best_dis_arr['buyquant'], $rtwwdpd_bogo_text);
	$rtwwdpd_bogo_text = str_replace('[quantity2]', $rtwwdpd_best_dis_arr['freequant'], $rtwwdpd_bogo_text);
	$rtwwdpd_bogo_text = str_replace('[the-product]', isset($rtwwdpd_best_dis_arr['buyprod']) ? get_the_title( $rtwwdpd_best_dis_arr['buyprod']) : '', $rtwwdpd_bogo_text);
	$rtwwdpd_bogo_text = str_replace('[free-product]', isset($rtwwdpd_best_dis_arr['freeprod']) ? get_the_title( $rtwwdpd_best_dis_arr['freeprod']) : '', $rtwwdpd_bogo_text);
	echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_bogo_text).'</span></div>';
}
elseif ($rtwwdpd_best_dis_arr['discount_type'] == 'bogocat') {

	$rtwwdpd_bogo_text = str_replace('[quantity1]', $rtwwdpd_best_dis_arr['buyquant'], $rtwwdpd_bogo_text);
	$rtwwdpd_bogo_text = str_replace('[quantity2]', $rtwwdpd_best_dis_arr['freequant'], $rtwwdpd_bogo_text);
	$rtwwdpd_bogo_text = str_replace('[the-product]', isset($rtwwdpd_best_dis_arr['buyprod']) ? $rtwwdpd_cats[$rtwwdpd_best_dis_arr['buyprod']] : '', $rtwwdpd_bogo_text);
	$rtwwdpd_bogo_text = str_replace('[free-product]', isset($rtwwdpd_best_dis_arr['freeprod']) ? get_the_title( $rtwwdpd_best_dis_arr['freeprod']) : '', $rtwwdpd_bogo_text);
	echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_bogo_text).'</span></div>';
}
elseif( $rtwwdpd_best_dis_arr['discount_type'] == 'tier_percent' )
{
	$rtwwdpd_tier_text_show = 'Buy [this_product] from [from_quant] to [to_quant] Get [discounted] Off';
	if( isset($rtwwdpd_get_content['rtwwdpd_tier_text_show']) )
	{
		$rtwwdpd_tier_text_show = $rtwwdpd_get_content['rtwwdpd_tier_text_show'];
	}
	$rtwwdpd_tier_text_show = str_replace('[discounted]', isset($rtwwdpd_best_dis_arr['dscnt_value']) ? $rtwwdpd_best_dis_arr['dscnt_value'].'%' : '', $rtwwdpd_tier_text_show);
													
	$rtwwdpd_tier_text_show = str_replace('[this_product]', isset($rtwwdpd_best_dis_arr['product']) ? get_the_title($rtwwdpd_best_dis_arr['product']) : '', $rtwwdpd_tier_text_show);

	$rtwwdpd_tier_text_show =	str_replace('[from_quant]', isset($rtwwdpd_best_dis_arr['from']) ? $rtwwdpd_best_dis_arr['from'] : '', $rtwwdpd_tier_text_show);

	$rtwwdpd_tier_text_show =	str_replace('[to_quant]', isset($rtwwdpd_best_dis_arr['to']) ? $rtwwdpd_best_dis_arr['to'] : '', $rtwwdpd_tier_text_show);

	echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_tier_text_show).'</span></div>';
}
elseif( $rtwwdpd_best_dis_arr['discount_type'] == 'tier_flat' )
{
	$rtwwdpd_tier_text_show = 'Buy [this_product] from [from_quant] to [to_quant] Get [discounted] Off';
	if( isset($rtwwdpd_get_content['rtwwdpd_tier_text_show']) )
	{
		$rtwwdpd_tier_text_show = $rtwwdpd_get_content['rtwwdpd_tier_text_show'];
	}
	$rtwwdpd_tier_text_show = str_replace('[discounted]', isset($rtwwdpd_best_dis_arr['dscnt_value']) ? $rtwwdpd_best_dis_arr['dscnt_value'].'%' : '', $rtwwdpd_tier_text_show);
													
	$rtwwdpd_tier_text_show = str_replace('[this_product]', isset($rtwwdpd_best_dis_arr['product']) ? get_the_title($rtwwdpd_best_dis_arr['product']) : '', $rtwwdpd_tier_text_show);

	$rtwwdpd_tier_text_show =	str_replace('[from_quant]', isset($rtwwdpd_best_dis_arr['from']) ? $rtwwdpd_best_dis_arr['from'] : '', $rtwwdpd_tier_text_show);

	$rtwwdpd_tier_text_show =	str_replace('[to_quant]', isset($rtwwdpd_best_dis_arr['to']) ? $rtwwdpd_best_dis_arr['to'] : '', $rtwwdpd_tier_text_show);

	echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_tier_text_show).'</span></div>';
}