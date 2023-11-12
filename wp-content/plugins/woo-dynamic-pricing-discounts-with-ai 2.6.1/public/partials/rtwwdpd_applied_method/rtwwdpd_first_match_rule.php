<?php
$rtwwdpd_get_content = get_option('rtwwdpd_setting_priority');
$rtwwdpd_text_to_show = isset($rtwwdpd_get_content['rtwwdpd_text_to_show']) ? $rtwwdpd_get_content['rtwwdpd_text_to_show'] : 'Get [discounted] Off';
$rtwwdpd_bogo_text = isset($rtwwdpd_get_content['rtwwdpd_bogo_text']) ? $rtwwdpd_get_content['rtwwdpd_bogo_text'] : 'Buy from [from_quant] to [to_quant] Get [discounted] Off';
$rtwwdpd_symbol = get_woocommerce_currency_symbol();
$rtwwdpd_rule_name = array();

$rtwwdpd_categories = get_terms('product_cat', 'orderby=name&hide_empty=0');
$rtwwdpd_cats = array();
$rtwwdpd_user = wp_get_current_user();
$rtwwdpd_weight_unit = get_option('woocommerce_weight_unit');
if (is_array($rtwwdpd_categories) && !empty($rtwwdpd_categories)) {
	foreach ($rtwwdpd_categories as $cat) {
		$rtwwdpd_cats[$cat->term_id] = $cat->name;
	}
}

if(is_array($rtwwdpd_priority) && !empty($rtwwdpd_priority))
{
	foreach ($rtwwdpd_priority as $rule => $rule_name) 
	{
		if($rule_name == 'bogo_cat_rule_row')
		{
			if(isset($rtwwdpd_offers['bogo_cat_rule']))
			{
				$rtwwdpd_rule_name = get_option('rtwwdpd_bogo_cat_rule');
				if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
				{
					foreach ($rtwwdpd_rule_name as $keys => $name) 
					{	
						$rtwwdpd_date = $name['rtwwdpd_bogo_to_date'];
						if($rtwwdpd_date > $rtwwdpd_today_date)
						{
							if(isset($name['category_id']) && is_array($name['category_id']) && !empty($name['category_id']))
							{
								foreach ($name['category_id'] as $no => $rtwwdpd_cat) {
									if($rtwwdpd_cat == $rtwwdpd_product_cat_id){
										$rtwwdpd_bogo_text = str_replace('[quantity1]', isset($name['combi_quant'][$no]) ? $name['combi_quant'][$no] : '', $rtwwdpd_bogo_text);

										$rtwwdpd_bogo_text = str_replace('[quantity2]', isset($name['bogo_quant_free'][$no]) ? $name['bogo_quant_free'][$no] : '', $rtwwdpd_bogo_text);

										$rtwwdpd_bogo_text = str_replace('[the-product]', isset($products[$rtwwdpd_cat]) ? $products[$rtwwdpd_cat] : '', $rtwwdpd_bogo_text);

										$rtwwdpd_bogo_text = str_replace('[free-product]', isset($name['rtwbogo'][$no]) ? get_the_title( $name['rtwbogo'][$no]) : '', $rtwwdpd_bogo_text);

										echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_bogo_text).'</span></div>';
									}
								}
							}
						}
					}
					return;
				}
			}
		}
		elseif($rule_name == 'cat_rule_row')
		{
			if(isset($rtwwdpd_offers['cat_rule']))
			{
				$rtwwdpd_rule_name = get_option('rtwwdpd_single_cat_rule');
				$rtwwdpd_text_to_show_cs = get_option( 'rtwwdpd_category_offer_msg','Get [discount_value] off on purchase of [from] to [to] on [category_name]' );
				if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
				{
					foreach ($rtwwdpd_rule_name as $name) {

						$rtwwdpd_user_role = $name['rtwwdpd_select_roles'] ;
						$rtwwdpd_role_matched = false;

						if(isset($rtwwdpd_user_role) && is_array($rtwwdpd_user_role) && !empty($rtwwdpd_user_role))
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
							continue 1;
						}
		
						$rtwwdpd_restricted_mails = isset( $name['rtwwdpd_select_emails'] ) ? $name['rtwwdpd_select_emails'] : array();

						$rtwwdpd_cur_user_mail = get_current_user_id();
						
						if(in_array($rtwwdpd_cur_user_mail, $rtwwdpd_restricted_mails))
						{
							continue 1;
						}

						if($name['rtwwdpd_check_for_cat'] == 'rtwwdpd_quantity')
						{
							$rtwwdpd_text_to_show_cs = str_replace('[from]', isset($name["rtwwdpd_min_cat"]) ? $name["rtwwdpd_min_cat"] : '', $rtwwdpd_text_to_show_cs);

							$rtwwdpd_text_to_show_cs = str_replace('[to]', isset($name["rtwwdpd_max_cat"]) ? $name["rtwwdpd_max_cat"] : $name["rtwwdpd_min_cat"].'+', $rtwwdpd_text_to_show_cs);

						}
						elseif($name['rtwwdpd_check_for_cat'] == 'rtwwdpd_price')
						{
							$rtwwdpd_text_to_show_cs = str_replace('[from]', isset($name["rtwwdpd_min_cat"]) ? get_woocommerce_currency_symbol().$name["rtwwdpd_min_cat"] : '', $rtwwdpd_text_to_show_cs);

							$rtwwdpd_text_to_show_cs = str_replace('[to]', isset($name["rtwwdpd_max_cat"]) ? get_woocommerce_currency_symbol().$name["rtwwdpd_max_cat"] : $name["rtwwdpd_min_cat"].'+', $rtwwdpd_text_to_show_cs);
						}else{
							$rtwwdpd_text_to_show_cs = str_replace('[from]', isset($name["rtwwdpd_min_cat"]) ? get_option('woocommerce_weight_unit').$name["rtwwdpd_min_cat"] : '', $rtwwdpd_text_to_show_cs);

							$rtwwdpd_text_to_show_cs = str_replace('[to]', isset($name["rtwwdpd_max_cat"]) ? get_option('woocommerce_weight_unit').$name["rtwwdpd_max_cat"] : $name["rtwwdpd_min_cat"].'+', $rtwwdpd_text_to_show_cs);
						}
						
						$rtwwdpd_date = $name['rtwwdpd_to_date'];
						if($rtwwdpd_date > $rtwwdpd_today_date)
						{
							if(isset($name['category_id']))
							{
								$rtwwdpd_cat = $name['category_id'];
								if($rtwwdpd_cat == $rtwwdpd_product_cat_id)
								{
									if($name['rtwwdpd_dscnt_cat_type'] == 'rtwwdpd_discount_percentage')
									{

										$rtwwdpd_text_to_show_cs = str_replace('[discount_value]', isset($name["rtwwdpd_dscnt_cat_val"]) ? $name["rtwwdpd_dscnt_cat_val"].'%' : '', $rtwwdpd_text_to_show_cs);

										$rtwwdpd_text_to_show_cs = str_replace('[category_name]', isset($rtwwdpd_cats[$name['category_id']]) ? $rtwwdpd_cats[$name['category_id']] : '', $rtwwdpd_text_to_show_cs);

										echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_text_to_show_cs).'</span></div>';
									}
									else
									{
										$rtwwdpd_text_to_show_cs = str_replace('[discount_value]', isset($name["rtwwdpd_dscnt_cat_val"]) ? get_woocommerce_currency_symbol().$name["rtwwdpd_dscnt_cat_val"] : '', $rtwwdpd_text_to_show_cs);

										$rtwwdpd_text_to_show_cs = str_replace('[category_name]', isset($rtwwdpd_cats[$name['category_id']]) ? $rtwwdpd_cats[$name['category_id']] : '', $rtwwdpd_text_to_show_cs);

										echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_text_to_show_cs).'</span></div>';
									}
								}
							}
						}
					}
					return;
				}
			}
		}
		elseif($rule_name == 'cat_com_rule_row')
		{
			if(isset($rtwwdpd_offers['cat_com_rule']))
			{
				$rtwwdpd_rule_name = get_option('rtwwdpd_combi_cat_rule');
				if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
				{
					foreach ($rtwwdpd_rule_name as $name) {
						$rtwwdpd_date = $name['rtwwdpd_combi_to_date'];
						if($rtwwdpd_date > $rtwwdpd_today_date)
						{
							if(isset($name['category_id']) && is_array($name['category_id']) && !empty($name['category_id']))
								foreach ($name['category_id'] as $keys => $val) {
									if($val == $rtwwdpd_product_cat_id)
									{
										$rtwwdpd_cat = $val;
										if($rtwwdpd_cat == $rtwwdpd_product_cat_id)
										{	
											if($name['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
											{
												$rtwwdpd_text_to_show = str_replace('[discounted]', isset($name["rtwwdpd_discount_value"]) ? $name["rtwwdpd_discount_value"].'%' : '', $rtwwdpd_text_to_show);

												echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_text_to_show).'</span></div>';
											}
											else
											{
												$rtwwdpd_text_to_show = str_replace('[discounted]', isset($name["rtwwdpd_discount_value"]) ? $rtwwdpd_symbol . $name["rtwwdpd_discount_value"] : '', $rtwwdpd_text_to_show);

												echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_text_to_show).'</span></div>';
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
		elseif($rule_name == 'pro_rule_row')
		{
			if(isset($rtwwdpd_offers['pro_rule']))
			{
				$rtwwdpd_rule_name = get_option('rtwwdpd_single_prod_rule');
				if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
				{
					foreach ($rtwwdpd_rule_name as $name) {
						$rtwwdpd_date = $name['rtwwdpd_single_to_date'];
						if($rtwwdpd_date > $rtwwdpd_today_date)
						{
							if(isset($name['product_id']))
							{
								$rtw_id = $name['product_id'];
								if($rtw_id == $rtwwdpd_prod_id)
								{
									if($name['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
									{
										$rtwwdpd_text_to_show = str_replace('[discounted]', isset($name["rtwwdpd_discount_value"]) ? $name["rtwwdpd_discount_value"].'%' : '', $rtwwdpd_text_to_show);

										echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_text_to_show).'</span></div>';
									}
									else
									{
										$rtwwdpd_text_to_show = str_replace('[discounted]', isset($name["rtwwdpd_discount_value"]) ? $rtwwdpd_symbol . $name["rtwwdpd_discount_value"] : '', $rtwwdpd_text_to_show);

										echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_text_to_show).'</span></div>';
									}
								}
							}
							else
							{
								if($name['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
								{
									$rtwwdpd_text_to_show = str_replace('[discounted]', isset($name["rtwwdpd_discount_value"]) ? $name["rtwwdpd_discount_value"].'%' : '', $rtwwdpd_text_to_show);

									echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_text_to_show).'</span></div>';
								}
								else
								{
									$rtwwdpd_text_to_show = str_replace('[discounted]', isset($name["rtwwdpd_discount_value"]) ? $rtwwdpd_symbol . $name["rtwwdpd_discount_value"] : '', $rtwwdpd_text_to_show);

									echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_text_to_show).'</span></div>';
								}
							}
						}
					}
					return;
				}
			}
		}
		elseif($rule_name == 'bogo_rule_row')
		{
			if(isset($rtwwdpd_offers['bogo_rule']))
			{
				$rtwwdpd_rule_name = get_option('rtwwdpd_bogo_rule');
				if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
				{
					foreach ($rtwwdpd_rule_name as $ke => $name) {
						$rtwwdpd_date = $name['rtwwdpd_bogo_to_date'];
						if($rtwwdpd_date > $rtwwdpd_today_date)
						{
							if(isset($name['product_id']) && is_array($name['product_id']) && !empty($name['product_id']))
							{
								foreach ($name['product_id'] as $no => $ids) {
									if($ids == $rtwwdpd_prod_id)
									{
										$rtwwdpd_bogo_text = str_replace('[quantity1]', isset($name['combi_quant'][$no]) ? $name['combi_quant'][$no] : '', $rtwwdpd_bogo_text);

										$rtwwdpd_bogo_text = str_replace('[quantity2]', isset($name['bogo_quant_free'][$no]) ? $name['bogo_quant_free'][$no] : '', $rtwwdpd_bogo_text);

										$rtwwdpd_bogo_text = str_replace('[the-product]', $rtwwdpd_product->get_name(), $rtwwdpd_bogo_text);

										$rtwwdpd_bogo_text = str_replace('[free-product]', isset($name['rtwbogo'][$no]) ? get_the_title( $name['rtwbogo'][$no]) : '', $rtwwdpd_bogo_text);

										echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_bogo_text).'</span></div>';
									}
								}
							}
						}
					}
					return;
				}
			}
		}
		elseif($rule_name == 'pro_com_rule_row')
		{
			if(isset($rtwwdpd_offers['pro_com_rule']))
			{
				$rtwwdpd_rule_name = get_option('rtwwdpd_combi_prod_rule');
				if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
				{
					foreach ($rtwwdpd_rule_name as $name) {
						$rtwwdpd_date = $name['rtwwdpd_combi_to_date'];
						if($rtwwdpd_date > $rtwwdpd_today_date)
						{
							if(isset($name['product_id']) && is_array($name['product_id']) && !empty($name['product_id']))
							{
								foreach ($name['product_id'] as $keys => $val) {
									if($val == $rtwwdpd_prod_id)
									{
										if($name['rtwwdpd_combi_discount_type'] == 'rtwwdpd_discount_percentage')
										{
											$rtwwdpd_text_to_show = str_replace('[discounted]', isset($name["rtwwdpd_combi_discount_value"]) ? $name["rtwwdpd_combi_discount_value"].'%' : '', $rtwwdpd_text_to_show);

											echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_text_to_show).'</span></div>';
										}
										else
										{
											$rtwwdpd_text_to_show = str_replace('[discounted]', isset($name["rtwwdpd_combi_discount_value"]) ? $rtwwdpd_symbol . $name["rtwwdpd_combi_discount_value"] : '', $rtwwdpd_text_to_show);

											echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_text_to_show).'</span></div>';
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
		elseif($rule_name == 'tier_rule_row')
		{
			if(isset($rtwwdpd_offers['tier_rule']))
			{
				$rtwwdpd_rule_name = get_option('rtwwdpd_tiered_rule');
				if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
				{
					foreach ($rtwwdpd_rule_name as $name) {
						if($name['rtwwdpd_to_date'] > $rtwwdpd_today_date && isset($name['products']) && !empty($name['products'])){
							foreach ($name['products'] as $keys => $vals) 
							{
								if($vals == $rtwwdpd_prod_id && isset($name['quant_min']) && !empty($name['quant_min']))
								{
									if($name['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
									{
										foreach ($name['quant_min'] as $k => $va) {
											$rtwwdpd_text_to_show = str_replace('[discounted]', isset($name['discount_val'][$k]) ? $name['discount_val'][$k].'%' : '', $rtwwdpd_text_to_show);

											echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_text_to_show).'</span></div>';
										}
									}
									else
									{
										foreach ($name['quant_min'] as $k => $va) {
											$rtwwdpd_text_to_show = str_replace('[discounted]', isset($name["discount_val"][$k]) ? $rtwwdpd_symbol . $name["discount_val"][$k] : '', $rtwwdpd_text_to_show);

											echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_text_to_show).'</span></div>';
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
		elseif($rule_name == 'tier_cat_rule_row')
		{
			if(isset($rtwwdpd_offers['tier_cat_rule']))
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
								if($vals == $rtwwdpd_product_cat_id)
								{
									if($name['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
									{
										foreach ($name['quant_min'] as $k => $va) {
											$rtwwdpd_text_to_show = str_replace('[discounted]', isset($name['discount_val'][$k]) ? $name['discount_val'][$k].'%' : '', $rtwwdpd_text_to_show);

											echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_text_to_show).'</span></div>';
										}
									}
									else
									{
										foreach ($name['quant_min'] as $k => $va) 
										{
											$rtwwdpd_text_to_show = str_replace('[discounted]', isset($name['discount_val'][$k]) ? $rtwwdpd_symbol . $name['discount_val'][$k] : '', $rtwwdpd_text_to_show);

											echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_text_to_show).'</span></div>';
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
		elseif($rule_name == 'attr_rule_row')
		{
			global $post;
			$rtwwdpd_colors = get_the_terms($post, 'pa_color');
			$rtwwdpd_sizes = get_the_terms($post, 'pa_size');
			
			if(isset($rtwwdpd_offers['attr_rule']))
			{
				$rtwwdpd_rule_name = get_option('rtwwdpd_att_rule');
				if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
				{
					foreach ($rtwwdpd_rule_name as $ke => $name) 
					{
						if($name['rtwwdpd_att_to_date'] > $rtwwdpd_today_date)
						{
							if(is_array($rtwwdpd_colors) && !empty($rtwwdpd_colors))
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
													$rtwwdpd_text_to_show = str_replace('[discounted]', isset($name['rtwwdpd_att_discount_value']) ? $name['rtwwdpd_att_discount_value'].'%' : '', $rtwwdpd_text_to_show);

													echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_text_to_show).'</span></div>';
												}
												else
												{
													$rtwwdpd_text_to_show = str_replace('[discounted]', isset($name['rtwwdpd_att_discount_value']) ? $rtwwdpd_symbol . $name['rtwwdpd_att_discount_value'] : '', $rtwwdpd_text_to_show);

													echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_text_to_show).'</span></div>';
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
									if(isset($name['rtwwdpd_attribute_val_size']) && is_array($name['rtwwdpd_attribute_val_size']) && !empty($name['rtwwdpd_attribute_val_size']))
									{
										foreach ($name['rtwwdpd_attribute_val_size'] as $col => $size) 
										{	
											if(in_array($size, array_column($rtwwdpd_sizes, 'term_id'))){ 
												if($name['rtwwdpd_att_discount_type'] == 'rtwwdpd_discount_percentage')
												{
													$rtwwdpd_text_to_show = str_replace('[discounted]', isset($name['rtwwdpd_att_discount_value']) ? $name['rtwwdpd_att_discount_value'].'%' : '', $rtwwdpd_text_to_show);

													echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_text_to_show).'</span></div>';
												}
												else
												{
													$rtwwdpd_text_to_show = str_replace('[discounted]', isset($name['rtwwdpd_att_discount_value']) ? $rtwwdpd_symbol . $name['rtwwdpd_att_discount_value'] : '', $rtwwdpd_text_to_show);

													echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_text_to_show).'</span></div>';
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
		}
		elseif($rule_name == 'prod_tag_rule_row')
		{
			$rtwwdpd_tag = wp_get_post_terms( get_the_id(), 'product_tag' );
			if(!empty($rtwwdpd_tag))
			{
				if(isset($rtwwdpd_offers['prod_tag_rule']))
				{
					$rtwwdpd_rule_name = get_option('rtwwdpd_tag_method');
					if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
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
												$rtwwdpd_text_to_show = str_replace('[discounted]', isset($name['rtwwdpd_tag_discount_value']) ? $name['rtwwdpd_tag_discount_value'].'%' : '', $rtwwdpd_text_to_show);

												echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_text_to_show).'</span></div>';
											}
											else
											{
												$rtwwdpd_text_to_show = str_replace('[discounted]', isset($name['rtwwdpd_tag_discount_value']) ? $rtwwdpd_symbol . $name['rtwwdpd_tag_discount_value'] : '', $rtwwdpd_text_to_show);

												echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_text_to_show).'</span></div>';
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