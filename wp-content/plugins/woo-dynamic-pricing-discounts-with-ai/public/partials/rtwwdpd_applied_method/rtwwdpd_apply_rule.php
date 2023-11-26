<?php
$rtwwdpd_arr_quant = array();

$i = 0;
if(is_array( $cart->cart_contents ) && !empty( $cart->cart_contents ))
{
	foreach ( $cart->cart_contents as $key => $value ) {

		$rtwwdpd_arr_quant[$i]['prod_id'] = $value['product_id'];
		$rtwwdpd_arr_quant[$i]['quant'] = $value['quantity'];
		$rtwwdpd_arr_quant[$i]['var_id'] = $value['variation_id'];
		$rtwwdpd_arr_quant[$i]['name'] = $value['data']->get_name();
		$rtwwdpd_arr_quant[$i]['cat_id'] = $value['data']->get_category_ids();
		$rtwwdpd_arr_quant[$i]['tag_id'] = $value['data']->get_tag_ids();
		$rtwwdpd_arr_quant[$i]['price'] = $value['data']->get_price();
		$rtwwdpd_arr_quant[$i]['reg_price'] = $value['data']->get_regular_price();
		$rtwwdpd_arr_quant[$i]['sale_price'] = $value['data']->get_sale_price();
		$rtwwdpd_arr_quant[$i]['weight'] = $value['data']->get_weight();
		$rtwwdpd_arr_quant[$i]['ship_class_id'] = $value['data']->get_shipping_class_id();
		$i++;
	}
}

$rtwwdpd_rule_name = array();
if( is_array($rtwwdpd_priority) && !empty($rtwwdpd_priority)){
	foreach ($rtwwdpd_priority as $rule => $rule_name) 
	{
		if($rule_name == 'bogo_cat_rule_row' && isset($rtwwdpd_get_settings['bogo_cat_rule']))
		{
			$rtwwdpd_rule_name = get_option('rtwwdpd_bogo_cat_rule');
			if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
			{
				foreach ($rtwwdpd_rule_name as $keys => $name) 
				{	
					$rtwwdpd_date = $name['rtwwdpd_bogo_to_date'];
					if($rtwwdpd_date > $rtwwdpd_today_date && isset($name['category_id']) && is_array($name['category_id']) && !empty($name['category_id']))
					{
						foreach ($name['category_id'] as $no => $cat) 
						{
							if(isset($rtwwdpd_arr_quant[$no]['cat_id'][$no]))
							{
								if($cat == $rtwwdpd_arr_quant[$no]['cat_id'][$no])
								{
									$product = wc_get_product( $rtwwdpd_arr_quant[$no]['prod_id'] );
									foreach ($cart->cart_contents as $key => $value)
									{
										$value['data']->set_price('20');
									}
								}
							}
						}
					}
				}
				return;
			}
		}
		elseif($rule_name == 'cat_rule_row' && isset($rtwwdpd_get_settings['cat_rule']))
		{
			$rtwwdpd_rule_name = get_option('rtwwdpd_single_cat_rule');
			if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
			{
				foreach ($rtwwdpd_rule_name as $name) 
				{
					if(isset($name['rtwwdpd_to_date']))
					{
						$rtwwdpd_date = $name['rtwwdpd_to_date'];

						if($rtwwdpd_date > $rtwwdpd_today_date && isset($name['category_id']))
						{
							$cat = $name['category_id'];
							if(is_array($rtwwdpd_cat_ids) && !empty($rtwwdpd_cat_ids))
							{
								foreach ($rtwwdpd_cat_ids as $kid => $cids) {
									if(in_array($cat, $cids))
									{

										if($name['rtwwdpd_dscnt_cat_type'] == 'rtwwdpd_discount_percentage')
										{
											esc_html_e('Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
											echo esc_html__($name['rtwwdpd_dscnt_cat_val'].'% Off use Coupan ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
											echo '\''.esc_html($name['rtwwdpd_offer_cat_name']).'\'<br>';
										}
										elseif($name['rtwwdpd_dscnt_cat_type'] == 'rtwwdpd_flat_discount_amount')
										{
											esc_html_e('Flat ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
											echo esc_html__($name['rtwwdpd_dscnt_cat_val'].' Off use Coupan ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
											echo '\''.esc_html($name['rtwwdpd_offer_cat_name']).'\'<br>';
										}
										elseif($name['rtwwdpd_dscnt_cat_type'] == 'rtwwdpd_fixed_price')
										{
											esc_html_e('Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
											echo esc_html__($name['rtwwdpd_dscnt_cat_val'].' Off use Coupan ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
											echo '\''.esc_html($name['rtwwdpd_offer_cat_name']).'\'<br>';
										}
									}
								}
							}
						}
					}
					return;
				}
			}
		}
		elseif($rule_name == 'cat_com_rule_row' && isset($rtwwdpd_get_settingss['cat_com_rule']))
		{
			$rtwwdpd_rule_name = get_option('rtwwdpd_combi_cat_rule');
			if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
			{
				foreach ($rtwwdpd_rule_name as $name) 
				{
					$rtwwdpd_date = $name['rtwwdpd_combi_to_date'];
					if($rtwwdpd_date > $rtwwdpd_today_date)
					{
						if(isset($name['category_id']) && is_array($name['category_id']) && !empty($name['category_id']))
						{
							foreach ($name['category_id'] as $keys => $val) 
							{
								foreach ($rtwwdpd_cat_ids as $kid => $cids) {
									if(in_array($val, $cids))
									{
										if($name['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
										{
											esc_html_e('Buy ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
											echo esc_html($name['combi_quant'][$keys]);
											esc_html_e(' Quantity & Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
											echo esc_html__($name['rtwwdpd_discount_value'].'% Off use Coupan ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
											echo '\''.esc_html($name['rtwwdpd_offer_name']).'\'<br>';
										}
										elseif($name['rtwwdpd_discount_type'] == 'rtwwdpd_flat_discount_amount')
										{
											esc_html_e('Buy ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
											echo esc_html($name['combi_quant'][$keys]);
											esc_html_e(' Quantity & Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
											echo esc_html__($name['rtwwdpd_discount_value'].' Off use Coupan ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
											echo '\''.esc_html($name['rtwwdpd_offer_name']).'\'<br>';
										}
										elseif($name['rtwwdpd_discount_type'] == 'rtwwdpd_fixed_price')
										{
											esc_html_e('Buy ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
											echo esc_html($name['combi_quant'][$keys]);
											esc_html_e(' Quantity & Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
											echo esc_html__($name['rtwwdpd_discount_value'].' Off use Coupan ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
											echo '\''.esc_html($name['rtwwdpd_offer_name']).'\'<br>';
										}
									}
								}
							}
						}
						return;
					}
				}
			}
		}
		elseif($rule_name == 'pro_rule_row' && isset($rtwwdpd_get_settingss['pro_rule']))
		{
			$rtwwdpd_rule_name = get_option('rtwwdpd_single_prod_rule');
			if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
			{
				foreach ($rtwwdpd_rule_name as $name) 
				{
					$rtwwdpd_date = $name['rtwwdpd_single_to_date'];
					if($rtwwdpd_date > $rtwwdpd_today_date && isset($name['product_id']))
					{
						$rtw_id = $name['product_id'];
						if($rtw_id == $rtw_prod_id)
						{
							if($name['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
							{
								esc_html_e('Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
								echo esc_html__($name['rtwwdpd_discount_value'].'% Off use Coupan ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
								echo '\''.esc_html($name['rtwwdpd_offer_name']).'\'<br>';
							}
							elseif($name['rtwwdpd_discount_type'] == 'rtwwdpd_flat_discount_amount')
							{
								esc_html_e('Flat ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
								echo esc_html__($name['rtwwdpd_discount_value'].' Off use Coupan ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
								echo '\''.esc_html($name['rtwwdpd_offer_name']).'\'<br>';
							}
							elseif($name['rtwwdpd_discount_type'] == 'rtwwdpd_fixed_price')
							{
								esc_html_e('Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
								echo esc_html__($name['rtwwdpd_discount_value'].' Off use Coupan ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
								echo '\''.esc_html($name['rtwwdpd_offer_name']).'\'<br>';
							}
						}
						else
						{
							if($name['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
							{
								esc_html_e('Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
								echo esc_html__($name['rtwwdpd_discount_value'].'% Off use Coupan ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
								echo '\''.esc_html($name['rtwwdpd_offer_name']).'\'<br>';
							}
							elseif($name['rtwwdpd_discount_type'] == 'rtwwdpd_flat_discount_amount')
							{
								esc_html_e('Flat ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
								echo esc_html__($name['rtwwdpd_discount_value'].' Off use Coupan ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
								echo '\''.esc_html($name['rtwwdpd_offer_name']).'\'<br>';
							}
							elseif($name['rtwwdpd_discount_type'] == 'rtwwdpd_fixed_price')
							{
								esc_html_e('Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
								echo esc_html__($name['rtwwdpd_discount_value'].' Off use Coupan ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
								echo '\''.esc_html($name['rtwwdpd_offer_name']).'\'<br>';
							}
						}
					}
				}
				return;
			}
		}
		elseif($rule_name == 'bogo_rule_row' && isset($rtwwdpd_get_settingss['bogo_rule']))
		{
			
			$rtwwdpd_rule_name = get_option('rtwwdpd_bogo_rule');
			if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
			{
				foreach ($rtwwdpd_rule_name as $ke => $name) 
				{
					$rtwwdpd_date = $name['rtwwdpd_bogo_to_date'];
					if($rtwwdpd_date > $rtwwdpd_today_date && isset($name['product_id']) && is_array($name['product_id']) && !empty($name['product_id']))
					{
						foreach ($name['product_id'] as $no => $ids) 
						{
							if($ids == $rtw_prod_id)
							{
								echo esc_html__('Buy ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
								if(isset($name['combi_quant'][$no]) && $name['combi_quant'][$no] != '')
								{
									echo esc_html($name['combi_quant'][$no]).' ';
								}
								else
								{
									$no = $no - 1;
									echo esc_html($name['combi_quant'][$no]).' ';
								}
								esc_html_e($rtw_product->get_name() .' Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');

								if(isset($name['rtwbogo']))
								{
									echo esc_html($name['bogo_quant_free'][$no]).' ';
									echo get_the_title( $name['rtwbogo'][$no]).'<br>';
								}
							}
						}
					}
				}
				return;
			}
		}
		elseif($rule_name == 'pro_com_rule_row' && isset($rtwwdpd_get_settingss['pro_com_rule']))
		{
			$rtwwdpd_rule_name = get_option('rtwwdpd_combi_prod_rule');
			if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
			{
				foreach ($rtwwdpd_rule_name as $name) 
				{
					$rtwwdpd_date = $name['rtwwdpd_combi_to_date'];
					if($rtwwdpd_date > $rtwwdpd_today_date && isset($name['product_id']) && is_array($name['product_id']) && !empty($name['product_id']))
					{
						foreach ($name['product_id'] as $keys => $val) 
						{
							if($val == $rtw_prod_id)
							{
								if($name['rtwwdpd_combi_discount_type'] == 'rtwwdpd_discount_percentage')
								{
									esc_html_e('Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
									echo esc_html__($name['rtwwdpd_combi_discount_value'].'% Off on purchase of ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
									echo esc_html($name['combi_quant'][$keys]).'<br>';
								}
								elseif($name['rtwwdpd_combi_discount_type'] == 'rtwwdpd_flat_discount_amount')
								{
									esc_html_e('Flat ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
									echo esc_html__($name['rtwwdpd_combi_discount_value'].' Off on purchase of ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
									echo esc_html($name['combi_quant'][$keys]).'<br>';
								}
								elseif($name['rtwwdpd_combi_discount_type'] == 'rtwwdpd_fixed_price')
								{
									esc_html_e('Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
									echo esc_html__($name['rtwwdpd_combi_discount_value'].' Off on purchase of ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
									echo esc_html($name['combi_quant'][$keys]).'<br>';
								}
							}
						}
					}
				}
				return;
			}
		}
		elseif($rule_name == 'tier_rule_row' && isset($rtwwdpd_get_settingss['tier_rule']))
		{
			$rtwwdpd_rule_name = get_option('rtwwdpd_tiered_rule');
			if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
			{
				foreach ($rtwwdpd_rule_name as $name) {
					if($name['rtwwdpd_to_date'] > $rtwwdpd_today_date)
					{
						if(isset($name['products']) && is_array($name['products']) && !empty($name['products']))
						{
							foreach ($name['products'] as $keys => $vals) 
							{
								if(isset($name['quant_min']) && is_array($name['quant_min']) && !empty($name['quant_min']))
								{
									if($vals == $rtw_prod_id)
									{
										if($name['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
										{
											if($name['rtwwdpd_check_for'] == 'rtwwdpd_price')
											{
												foreach ($name['quant_min'] as $k => $va) {
													esc_html_e('Buy for ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													esc_html_e($va.' to '.$name['quant_max'][$k].
														' Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													echo esc_html($name['discount_val'][$k]);
													esc_html_e('% Off ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													echo '<br>';
												}
											}
											elseif($name['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
											{
												foreach ($name['quant_min'] as $k => $va) {
													esc_html_e('Buy ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													esc_html_e($va.' to '.$name['quant_max'][$k].
														' Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													echo esc_html($name['discount_val'][$k]);
													esc_html_e('% Off ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													echo '<br>';
												}
											}
											elseif($name['rtwwdpd_check_for'] == 'rtwwdpd_weight')
											{
												foreach ($name['quant_min'] as $k => $va) {
													esc_html_e('Buy weight ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													esc_html_e($va.' to '.$name['quant_max'][$k].
														' Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													echo esc_html($name['discount_val'][$k]);
													esc_html_e('% Off ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													echo '<br>';
												}
											}
										}
										elseif($name['rtwwdpd_discount_type'] == 'rtwwdpd_flat_discount_amount')
										{
											if($name['rtwwdpd_check_for'] == 'rtwwdpd_price')
											{
												foreach ($name['quant_min'] as $k => $va) {
													esc_html_e('Buy for ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													esc_html_e($va.' to '.$name['quant_max'][$k].
														' Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													echo esc_html($name['discount_val'][$k]);
													esc_html_e(' Off ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													echo '<br>';
												}
											}
											elseif($name['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
											{
												foreach ($name['quant_min'] as $k => $va) {
													esc_html_e('Buy ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													esc_html_e($va.' to '.$name['quant_max'][$k].
														' Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													echo esc_html($name['discount_val'][$k]);
													esc_html_e(' Off ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													echo '<br>';
												}
											}
											elseif($name['rtwwdpd_check_for'] == 'rtwwdpd_weight')
											{
												foreach ($name['quant_min'] as $k => $va) {
													esc_html_e('Buy weight ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													esc_html_e($va.' to '.$name['quant_max'][$k].
														' Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													echo esc_html($name['discount_val'][$k]);
													esc_html_e(' Off ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													echo '<br>';
												}
											}
										}
										elseif($name['rtwwdpd_discount_type'] == 'rtwwdpd_fixed_price')
										{
											if($name['rtwwdpd_check_for'] == 'rtwwdpd_price')
											{
												foreach ($name['quant_min'] as $k => $va) {
													esc_html_e('Buy for ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													esc_html_e($va.' to '.$name['quant_max'][$k].
														' Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													echo esc_html($name['discount_val'][$k]);
													esc_html_e(' Off ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													echo '<br>';
												}
											}
											elseif($name['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
											{
												foreach ($name['quant_min'] as $k => $va) {
													esc_html_e('Buy ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													esc_html_e($va.' to '.$name['quant_max'][$k].
														' Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													echo esc_html($name['discount_val'][$k]);
													esc_html_e(' Off ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													echo '<br>';
												}
											}
											elseif($name['rtwwdpd_check_for'] == 'rtwwdpd_weight')
											{
												foreach ($name['quant_min'] as $k => $va) {
													esc_html_e('Buy weight ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													esc_html_e($va.' to '.$name['quant_max'][$k].
														' Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													echo esc_html($name['discount_val'][$k]);
													esc_html_e(' Off ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													echo '<br>';
												}
											}
										}
									}
								}
							}
						}
					}
				}
				return;
			}
		}
		elseif($rule_name == 'tier_cat_rule_row' && isset($rtwwdpd_get_settingss['tier_cat_rule']))
		{
			$rtwwdpd_rule_name = get_option('rtwwdpd_tiered_cat');
			if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
			{
				foreach ($rtwwdpd_rule_name as $ke => $name) 
				{
					if(isset($name['category_id']) && is_array($name['category_id']) && !empty($name['category_id']))
					{
						foreach ($name['category_id'] as $keys => $vals) 
						{
							foreach ($rtwwdpd_cat_ids as $kid => $cids) {
								if(in_array($vals, $cids))
								{
									if($name['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
									{
										if($name['rtwwdpd_check_for'] == 'rtwwdpd_price')
										{
											foreach ($name['quant_min'] as $k => $va) {
												esc_html_e('Buy for ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
												esc_html_e($va.' to '.$name['quant_max'][$k].
													' Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
												echo esc_html($name['discount_val'][$k]);
												esc_html_e('% Off ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
												echo '<br>';
											}
										}
										elseif($name['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
										{
											foreach ($name['quant_min'] as $k => $va) {
												esc_html_e('Buy ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
												esc_html_e($va.' to '.$name['quant_max'][$k].
													' Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
												echo esc_html($name['discount_val'][$k]);
												esc_html_e('% Off ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
												echo '<br>';
											}
										}
										elseif($name['rtwwdpd_check_for'] == 'rtwwdpd_weight')
										{
											foreach ($name['quant_min'] as $k => $va) {
												esc_html_e('Buy weight ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
												esc_html_e($va.' to '.$name['quant_max'][$k].
													' Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
												echo esc_html($name['discount_val'][$k]);
												esc_html_e('% Off ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
												echo '<br>';
											}
										}
									}
									elseif($name['rtwwdpd_discount_type'] == 'rtwwdpd_flat_discount_amount')
									{
										if($name['rtwwdpd_check_for'] == 'rtwwdpd_price')
										{
											foreach ($name['quant_min'] as $k => $va) {
												esc_html_e('Buy for ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
												esc_html_e($va.' to '.$name['quant_max'][$k].
													' Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
												echo esc_html($name['discount_val'][$k]);
												esc_html_e(' Off ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
												echo '<br>';
											}
										}
										elseif($name['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
										{
											foreach ($name['quant_min'] as $k => $va) {
												esc_html_e('Buy ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
												esc_html_e($va.' to '.$name['quant_max'][$k].
													' Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
												echo esc_html($name['discount_val'][$k]);
												esc_html_e(' Off ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
												echo '<br>';
											}
										}
										elseif($name['rtwwdpd_check_for'] == 'rtwwdpd_weight')
										{
											foreach ($name['quant_min'] as $k => $va) {
												esc_html_e('Buy weight ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
												esc_html_e($va.' to '.$name['quant_max'][$k].
													' Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
												echo esc_html($name['discount_val'][$k]);
												esc_html_e(' Off ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
												echo '<br>';
											}
										}
									}
									elseif($name['rtwwdpd_discount_type'] == 'rtwwdpd_fixed_price')
									{
										if($name['rtwwdpd_check_for'] == 'rtwwdpd_price')
										{
											foreach ($name['quant_min'] as $k => $va) {
												esc_html_e('Buy for ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
												esc_html_e($va.' to '.$name['quant_max'][$k].
													' Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
												echo esc_html($name['discount_val'][$k]);
												esc_html_e(' Off ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
												echo '<br>';
											}
										}
										elseif($name['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
										{
											foreach ($name['quant_min'] as $k => $va) {
												esc_html_e('Buy ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
												esc_html_e($va.' to '.$name['quant_max'][$k].
													' Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
												echo esc_html($name['discount_val'][$k]);
												esc_html_e(' Off ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
												echo '<br>';
											}
										}
										elseif($name['rtwwdpd_check_for'] == 'rtwwdpd_weight')
										{
											foreach ($name['quant_min'] as $k => $va) {
												esc_html_e('Buy weight ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
												esc_html_e($va.' to '.$name['quant_max'][$k].
													' Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
												echo esc_html($name['discount_val'][$k]);
												esc_html_e(' Off ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
												echo '<br>';
											}
										}
									}
								}
							}
							return;
						}
					}
				}
			}
			elseif($rule_name == 'attr_rule_row' && isset($rtwwdpd_get_settingss['attr_rule']))
			{
				global $post;
				$rtwwdpd_colors = get_the_terms($post, 'pa_color');
				$rtwwdpd_sizes = get_the_terms($post, 'pa_size');

				$rtwwdpd_rule_name = get_option('rtwwdpd_att_rule');
				if(!empty($rtwwdpd_rule_name))
				{
					foreach ($rtwwdpd_rule_name as $ke => $name) 
					{
						if($name['rtwwdpd_att_to_date'] > $rtwwdpd_today_date)
						{
							if(!empty($rtwwdpd_colors))
							{
								if(isset($name['rtwwdpd_attributes']) && $name['rtwwdpd_attributes'] == 'pa_color')
								{
									$i = 0;
									if(isset($name['rtwwdpd_attribute_val_col']) && is_array($name['rtwwdpd_attribute_val_col']) && !empty($name['rtwwdpd_attribute_val_col']))
									{
										foreach ($name['rtwwdpd_attribute_val_col'] as $col => $color) 
										{	
											if(in_array($color, array_column($rtwwdpd_colors, 'term_id')))
											{ 
												if($name['rtwwdpd_att_discount_type'] == 'rtwwdpd_discount_percentage')
												{
													esc_html_e('Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													echo esc_html__($name['rtwwdpd_att_discount_value'].'% Off use Coupan ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													echo '\''.esc_html__($name['rtwwdpd_att_offer_name'].'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'\'<br>';
												}
												elseif($name['rtwwdpd_att_discount_type'] == 'rtwwdpd_flat_discount_amount')
												{
													esc_html_e('Flat ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													echo esc_html__($name['rtwwdpd_att_discount_value'].' Off use Coupan ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													echo '\''.esc_html__($name['rtwwdpd_att_offer_name'],'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'\'<br>';
												}
												elseif($name['rtwwdpd_att_discount_type'] == 'rtwwdpd_fixed_price')
												{
													esc_html_e('Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													esc_html__( $name['rtwwdpd_att_discount_value'].' Off use Coupan ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													echo '\''.esc_html__($name['rtwwdpd_att_offer_name'],'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'\'<br>';
												}
											}
											$i++;
											if($i==1) break;
										}
									}
								}
							}
							if(!empty($rtwwdpd_sizes)){
								if(isset($name['rtwwdpd_attributes']) && $name['rtwwdpd_attributes'] == 'pa_size')
								{
									$i = 0;
									if(isset($name['rtwwdpd_attribute_val_size']) && !empty($name['rtwwdpd_attribute_val_size']))
									{
										foreach ($name['rtwwdpd_attribute_val_size'] as $col => $size) 
										{	
											if(in_array($size, array_column($rtwwdpd_sizes, 'term_id'))){ 
												if($name['rtwwdpd_att_discount_type'] == 'rtwwdpd_discount_percentage')
												{
													esc_html_e('Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													esc_html__($name['rtwwdpd_att_discount_value'].'% Off use Coupan ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													echo '\''.esc_html__($name['rtwwdpd_att_offer_name'],'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'\'<br>';
												}
												elseif($name['rtwwdpd_att_discount_type'] == 'rtwwdpd_flat_discount_amount')
												{
													esc_html_e('Flat ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													esc_html__($name['rtwwdpd_att_discount_value'].' Off use Coupan ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													echo '\''.esc_html__($name['rtwwdpd_att_offer_name'],'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'\'<br>';
												}
												elseif($name['rtwwdpd_att_discount_type'] == 'rtwwdpd_fixed_price')
												{
													esc_html_e('Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													esc_html__( $name['rtwwdpd_att_discount_value'].' Off use Coupan ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
													echo '\''.esc_html__($name['rtwwdpd_att_offer_name'],'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'\'<br>';
												}
											}
											$i++;
											if($i==1) break;
										}
									}
								}

							}
						}
					}
					return;
				}
			}
			elseif($rule_name == 'prod_tag_rule_row' && isset($rtwwdpd_get_settingss['prod_tag_rule']))
			{
				$rtwwdpd_tag = wp_get_post_terms( get_the_id(), 'product_tag' );
				if(!empty($rtwwdpd_tag))
				{
					$rtwwdpd_rule_name = get_option('rtwwdpd_tag_method');
					if(!empty($rtwwdpd_rule_name))
					{
						foreach ($rtwwdpd_rule_name as $ke => $name) 
						{
							if($name['rtwwdpd_tag_to_date'] > $rtwwdpd_today_date)
							{
								if(isset($name['rtw_product_tags']) && is_array($name['rtw_product_tags']) && !empty($name['rtw_product_tags']))
								{
									foreach ($name['rtw_product_tags'] as $tag => $tags) 
									{	
										if(in_array($tags, array_column($rtwwdpd_tag, 'term_id')))
										{ 
											if($name['rtwwdpd_tag_discount_type'] == 'rtwwdpd_discount_percentage')
											{
												esc_html_e('Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
												echo esc_html__($name['rtwwdpd_tag_discount_value'].'% Off use Coupan ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
												echo '\''.esc_html__($name['rtwwdpd_tag_offer_name'],'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'\'<br>';
											}
											elseif($name['rtwwdpd_tag_discount_type'] == 'rtwwdpd_flat_discount_amount')
											{
												esc_html_e('Flat ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
												echo esc_html__($name['rtwwdpd_tag_discount_value'].' Off use Coupan ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
												echo '\''.esc_html__($name['rtwwdpd_tag_offer_name'],'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'\'<br>';
											}
											elseif($name['rtwwdpd_tag_discount_type'] == 'rtwwdpd_fixed_price')
											{
												esc_html_e('Get ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
												esc_html__($name['rtwwdpd_tag_discount_value'].' Off use Coupan ','rtwwdpd-woo-dynamic-pricing-discounts-with-ai');
												echo '\''.esc_html__($name['rtwwdpd_tag_offer_name'],'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'\'<br>';
											}
										}
									}
								}
							}
						}
						return;
					}
				}
			}
		}
	}
}