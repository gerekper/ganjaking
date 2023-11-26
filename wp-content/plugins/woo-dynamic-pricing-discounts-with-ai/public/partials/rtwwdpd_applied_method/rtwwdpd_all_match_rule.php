<?php


global $woocommerce;
$rtwwdpd_get_content = get_option('rtwwdpd_setting_priority');
$rtwwdpd_text_to_show = '';
$rtwqwdpd_nth_text_to_show = '';
$rtwwdpd_least_amount_text = '';
$rtwwdpd_bogo_text = '';
$rtwwdpd_tier_text_show = '';
$rtwwdpd_symbol = get_woocommerce_currency_symbol();
$rtwwdpd_categories = get_terms('product_cat', 'orderby=name&hide_empty=0');
$rtwwdpd_cats = array();
$rtwwdpd_user = wp_get_current_user();
$rtwwdpd_weight_unit = get_option('woocommerce_weight_unit');
if (is_array($rtwwdpd_categories) && !empty($rtwwdpd_categories)) {
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
else
{
	$rtwwdpd_bogo_text = $rtwwdpd_get_content['rtwwdpd_bogo_text'];
}
if(is_array($rtwwdpd_priority) && !empty($rtwwdpd_priority))
{
	$rtwwdpd_match = false;
	foreach ($rtwwdpd_priority as $rule => $rule_name) 
	{

		if($rule_name == 'bogo_cat_rule_row')
		{
			if(isset($rtwwdpd_offers['bogo_cat_rule']))
			{
				$rtwwdpd_rule_name = get_option('rtwwdpd_bogo_cat_rule');
				
				if( is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
				{
					foreach ($rtwwdpd_rule_name as $keys => $name) 
					{	
						$rtwwdpd_date = $name['rtwwdpd_bogo_to_date'];
						if($rtwwdpd_date >= $rtwwdpd_today_date && $rtwwdpd_today_date >= $name['rtwwdpd_bogo_from_date']){
							if(isset($name['category_id']) && is_array($name['category_id']) && !empty($name['category_id']))
							{
								foreach ($name['category_id'] as $no => $cat)
								{
									$category_in_cart = array();
									////////////////////
									if( $rtwwdpd_product->get_type() == 'variable' || $rtwwdpd_product->get_type() == 'variation' )
									{
										$categorys = $rtwwdpd_product->get_category_ids();
										if( is_array($categorys) && !empty($categorys) )
										{
											foreach ($categorys as $cc => $c) {
												$category_in_cart[] = $c;
											}
										}
									}
									else{
										$categorys = $rtwwdpd_product->get_category_ids();
										if( is_array($categorys) && !empty($categorys) )
										{
											foreach ($categorys as $cc => $c) {
												$category_in_cart[] = $c;
											}
										}
									}
									////////////////////
									if( ( in_array($cat, $category_in_cart) || $cat == $rtwwdpd_product_cat_id) && $rtwwdpd_match == false){

										$rtwwdpd_bogo_text = str_replace('[quantity1]', isset($name['combi_quant'][$no]) ? $name['combi_quant'][$no] : '', $rtwwdpd_bogo_text);
									
										$rtwwdpd_bogo_text = str_replace('[quantity2]', isset($name['bogo_quant_free'][$no]) ? $name['bogo_quant_free'][$no] : '', $rtwwdpd_bogo_text);

										$rtwwdpd_bogo_text = str_replace('[the-product]', isset($rtwwdpd_cats[$cat]) ? $rtwwdpd_cats[$cat] : '', $rtwwdpd_bogo_text);

										$rtwwdpd_bogo_text = str_replace('[free-product]', isset($name['rtwbogo'][$no]) ? get_the_title( $name['rtwbogo'][$no]) : '', $rtwwdpd_bogo_text);

										echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_bogo_text).'</span></div>';
										$rtwwdpd_match = true;
										break 2;
									}
								}
							}
						}
					}
				}
			}
		}
		elseif($rule_name == 'bogo_tag_rule_row')
		{
			if(isset($rtwwdpd_offers['bogo_tag_rule']))
			{
				$rtwwdpd_rule_name = get_option('rtwwdpd_bogo_tag_rule');

				if( is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
				{
					foreach ($rtwwdpd_rule_name as $keys => $name) 
					{	
						$rtwwdpd_date = $name['rtwwdpd_bogo_to_date'];
						if($rtwwdpd_date >= $rtwwdpd_today_date && $rtwwdpd_today_date >= $name['rtwwdpd_bogo_from_date']){
							if(isset($name['tag_id']) && is_array($name['tag_id']) && !empty($name['tag_id']))
							{
								foreach ($name['tag_id'] as $no => $cat)
								{
									$category_in_cart = array();
									////////////////////
									if( $rtwwdpd_product->get_type() == 'variable' || $rtwwdpd_product->get_type() == 'variation' )
									{
										$categorys = $rtwwdpd_product->get_tag_ids();
										if( is_array($categorys) && !empty($categorys) )
										{
											foreach ($categorys as $cc => $c) {
												$category_in_cart[] = $c;
											}
										}
									}
									else{
										$categorys = $rtwwdpd_product->get_tag_ids();
										if( is_array($categorys) && !empty($categorys) )
										{
											foreach ($categorys as $cc => $c) {
												$category_in_cart[] = $c;
											}
										}
									}
									////////////////////
									if( ( in_array($cat, $category_in_cart) || $cat == $rtwwdpd_product_cat_id) && $rtwwdpd_match == false){

										$rtwwdpd_bogo_text = str_replace('[quantity1]', isset($name['combi_quant'][$no]) ? $name['combi_quant'][$no] : '', $rtwwdpd_bogo_text);
									
										$rtwwdpd_bogo_text = str_replace('[quantity2]', isset($name['bogo_quant_free'][$no]) ? $name['bogo_quant_free'][$no] : '', $rtwwdpd_bogo_text);

										$rtwwdpd_bogo_text = str_replace('[the-product]', isset($rtwwdpd_cats[$cat]) ? $rtwwdpd_cats[$cat] : '', $rtwwdpd_bogo_text);

										$rtwwdpd_bogo_text = str_replace('[free-product]', isset($name['rtwbogo'][$no]) ? get_the_title( $name['rtwbogo'][$no]) : '', $rtwwdpd_bogo_text);

										echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_bogo_text).'</span></div>';
										$rtwwdpd_match = true;
										break 2;
									}
								}
							}
						}
					}
				}
			}
		}
		elseif($rule_name == 'cat_rule_row')
		{
			if(isset($rtwwdpd_offers['cat_rule']))
			{
				$rtwwdpd_rule_name = get_option('rtwwdpd_single_cat_rule');
				if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
				{
					$active_dayss = get_site_option('rtwwdpd_discount_on_selected_days', 'no');

					foreach ( $rtwwdpd_rule_name as $name) {

						if($active_dayss == 'yes')
						{
							$active_days = isset($name['rtwwwdpd_cat_day']) ? $name['rtwwwdpd_cat_day'] : array();
							$current_day = date('N');

							if(!in_array($current_day, $active_days))
							{
								continue;
							}
						}

						$rtwwdpd_text_to_show_cs = get_option( 'rtwwdpd_category_offer_msg','Get [discount_value] off on purchase of [from] to [to] on [category_name]' );

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

						if( isset( $name['rtw_exe_product_tags'] ) && is_array( $name['rtw_exe_product_tags'] ) && !empty( $name['rtw_exe_product_tags'] ) )
						{
							$rtw_matched = array_intersect( $name['rtw_exe_product_tags'], $rtwwdpd_product->get_tag_ids());
								
							if( !empty( $rtw_matched ) )
							{
								continue 1;
							}
						}
						
						if(isset($name['rtwwdpd_exclude_sale']))
						{
							if( !empty($rtwwdpd_product) && $rtwwdpd_product->is_on_sale() )
							{
								continue;
							}
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

						if(!empty($rtwwdpd_product))
						{
							$rtwwdpd_terms = get_the_terms( $rtwwdpd_product->get_id(), 'product_cat' );
						}
						else{
							$rtwwdpd_terms = array();
						}
						$rtwwdpd_product_cat_id = array();
					
						// $original_price = $rtwwdpd_product->get_regular_price();
						// if( !isset( $original_price ) || empty($original_price) )
						// {
						// 	$original_price = $rtwwdpd_product->get_price();
						// }
					
						
						if(is_array($rtwwdpd_terms) && !empty($rtwwdpd_terms))
						{
							foreach ($rtwwdpd_terms  as $term  ) {
								$rtwwdpd_product_cat_id[] = $term->term_id;
							}
						}
						

						if($name['rtwwdpd_to_date'] >= $rtwwdpd_today_date && $rtwwdpd_today_date >= $name['rtwwdpd_from_date'] )
						{
							if(isset($name['category_id']))
							{
								$cat = $name['category_id'];
								if(in_array($cat, $rtwwdpd_product_cat_id) && $rtwwdpd_match == false)
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
									$rtwwdpd_match = true;

									break 2;
								}
							}
							if(isset($name['multiple_cat_ids']))
							{
								$rtwwdpd_mul_comn_id = array_intersect($rtwwdpd_product_cat_id,$name['multiple_cat_ids']);
							
								if($rtwwdpd_mul_comn_id && $rtwwdpd_match == false)
								{
									
									foreach($rtwwdpd_mul_comn_id as $key => $val)
									{
									
										if($name['rtwwdpd_dscnt_cat_type'] == 'rtwwdpd_discount_percentage')
										{
											$rtwwdpd_text_to_show_cs = str_replace('[discount_value]', isset($name["rtwwdpd_dscnt_cat_val"]) ? $name["rtwwdpd_dscnt_cat_val"].'%' : '', $rtwwdpd_text_to_show_cs);

											$rtwwdpd_text_to_show_cs = str_replace('[category_name]', isset($rtwwdpd_cats[$val]) ? $rtwwdpd_cats[$val] : '', $rtwwdpd_text_to_show_cs);

											echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_text_to_show_cs).'</span></div>';
										}
										else
										{
											$rtwwdpd_text_to_show_cs = str_replace('[discount_value]', isset($name["rtwwdpd_dscnt_cat_val"]) ? get_woocommerce_currency_symbol().$name["rtwwdpd_dscnt_cat_val"] : '', $rtwwdpd_text_to_show_cs);

											$rtwwdpd_text_to_show_cs = str_replace('[category_name]', isset($rtwwdpd_cats[$val]) ? $rtwwdpd_cats[$val] : '', $rtwwdpd_text_to_show_cs);

											echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_text_to_show_cs).'</span></div>';
										}	
										$rtwwdpd_match = true;
										break 2;
									}
								
								}
							}
						}
					}
				}
			}
		}
		elseif($rule_name == 'cat_com_rule_row')
		{
			if(isset($rtwwdpd_offers['cat_com_rule']))
			{
				$rtwwdpd_rule_name = get_option('rtwwdpd_combi_cat_rule');
				if( is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
				{
					foreach ($rtwwdpd_rule_name as $name) {
						$rtwwdpd_text_to_show_cs = get_option( 'rtwwdpd_category_offer_msg','Get [discount_value] off on purchase of [from] to [to] on [category_name]' );
						if( isset( $name['rtw_exe_product_tags'] ) && is_array( $name['rtw_exe_product_tags'] ) && !empty( $name['rtw_exe_product_tags'] ) )
						{
							$rtw_matched = array_intersect( $name['rtw_exe_product_tags'], $rtwwdpd_product->get_tag_ids());
								
							if( !empty( $rtw_matched ) )
							{
								continue 1;
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
						
						if( $rtwwdpd_role_matched == false)
						{
							continue 1;
						}

						$rtwwdpd_restricted_mails = isset( $name['rtwwdpd_select_com_emails'] ) ? $name['rtwwdpd_select_com_emails'] : array();

						$rtwwdpd_cur_user_mail = get_current_user_id();
						
						if(in_array($rtwwdpd_cur_user_mail, $rtwwdpd_restricted_mails))
						{
							continue 1;
						}
						
						if(isset($name['rtwwdpd_exclude_sale']))
						{
							if( !empty($rtwwdpd_product) && $rtwwdpd_product->is_on_sale() )
							{
								continue;
							}
						}
						$rtwwdpd_text_to_show_cs = str_replace('[from]', isset($name["combi_quant"][0]) ? $name["combi_quant"][0] : '', $rtwwdpd_text_to_show_cs);

						$rtwwdpd_text_to_show_cs = str_replace('[to]', $name["combi_quant"][0].'+', $rtwwdpd_text_to_show_cs);

						$rtwwdpd_date = $name['rtwwdpd_combi_to_date'];
						if($rtwwdpd_date >= $rtwwdpd_today_date && $rtwwdpd_today_date >= $name['rtwwdpd_combi_from_date'] )
						{
							if(isset($name['category_id']) && is_array($name['category_id']) && !empty($name['category_id']))
							{
								foreach ($name['category_id'] as $keys => $val) {
									if($val == $rtwwdpd_product_cat_id && $rtwwdpd_match == false)
									{	
										if( $name['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage' )
										{

											$rtwwdpd_text_to_show_cs = str_replace('[discount_value]', isset($name["rtwwdpd_discount_value"]) ? $name["rtwwdpd_discount_value"].'%' : '', $rtwwdpd_text_to_show_cs);

											$rtwwdpd_text_to_show_cs = str_replace('[category_name]', isset($rtwwdpd_cats[$name['category_id'][0]]) ? $rtwwdpd_cats[$name['category_id'][0]] : '', $rtwwdpd_text_to_show_cs);

											echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_text_to_show_cs).'</span></div>';
										}
										else
										{
											$rtwwdpd_text_to_show_cs = str_replace('[discount_value]', isset($name["rtwwdpd_discount_value"]) ? $name["rtwwdpd_discount_value"].'%' : '', $rtwwdpd_text_to_show_cs);

											$rtwwdpd_text_to_show_cs = str_replace('[category_name]', isset($rtwwdpd_cats[$name['category_id'][0]]) ? $rtwwdpd_cats[$name['category_id'][0]] : '', $rtwwdpd_text_to_show_cs);

											echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_text_to_show_cs).'</span></div>';
										}
										$rtwwdpd_match = true;
										break 2;

									}
								}
							}
						}
					}
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
					$it = 1;
					$active_dayss = get_site_option('rtwwdpd_discount_on_selected_days', 'no');

					foreach ($rtwwdpd_rule_name as $kkk => $name) {

						if($active_dayss == 'yes')
						{
							$active_days = isset($name['rtwwwdpd_prod_day']) ? $name['rtwwwdpd_prod_day'] : array();
							$current_day = date('N');

							if(!in_array($current_day, $active_days))
							{
								continue;
							}
						}

						$rtwwdpd_text_to_show_pr = apply_filters('rtwwdpd_all_match_offer_for_product_rule', $kkk);

						if(empty($rtwwdpd_text_to_show_pr) || $rtwwdpd_text_to_show_pr == $kkk)
						{
							$rtwwdpd_text_to_show_pr = get_option( 'rtwwdpd_product_offer_msg', '');
							
						}

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
						
						$rtw_curnt_dayname = date("N");
						$rtwwdpd_day_waise_rule = false;
						if(isset($name['rtwwdpd_enable_day']) && $name['rtwwdpd_enable_day'] == 'yes')
						{
							if(isset($name['rtwwdpd_select_day']) && !empty($name['rtwwdpd_select_day']))
							{
								if($name['rtwwdpd_select_day'] == $rtw_curnt_dayname)
								{
									$rtwwdpd_day_waise_rule = true;
								}
							}
							if($rtwwdpd_day_waise_rule == false)
							{
								
								continue;
							}
						}

						$rtwwdpd_restricted_mails = isset( $name['rtwwdpd_select_emails'] ) ? $name['rtwwdpd_select_emails'] : array();

						$rtwwdpd_cur_user_mail = get_current_user_id();
						
						if(in_array($rtwwdpd_cur_user_mail, $rtwwdpd_restricted_mails))
						{
							continue 1;
						}

						if(isset($name['rtwwdpd_exclude_sale']))
						{
							if( !empty($rtwwdpd_product) && $rtwwdpd_product->is_on_sale() )
							{
								continue;
							}
						}

						$rtwwdpd_date = $name['rtwwdpd_single_from_date'];
						if($name['rtwwdpd_single_to_date'] >= $rtwwdpd_today_date && $rtwwdpd_today_date >= $name['rtwwdpd_single_from_date'] )
						{        
                            if(isset($name['rtwwdpd_rule_on']) && ($name['rtwwdpd_rule_on'] == 'rtwwdpd_products' || $name['rtwwdpd_rule_on'] == 'rtwwdpd_multiple_products'))
							{
								$checked = false;
								if(!empty($rtwwdpd_product) && !empty($rtwwdpd_product->get_children()))
								{
									$variations = $rtwwdpd_product->get_children();
								}else{
									$variations = '';
								}

								if( $name['rtwwdpd_rule_on'] == 'rtwwdpd_products' )
								{
									$rtwwdpd_id = $name['product_id'];
									if( $rtwwdpd_id == $rtwwdpd_prod_id  )
									{
										$checked = true;
									}
	
									if( !empty($variations) && in_array($rtwwdpd_id, $variations))
									{
										$checked = true;
									}
								}
								elseif( $name['rtwwdpd_rule_on'] == 'rtwwdpd_multiple_products' )
								{
									if(in_array($rtwwdpd_prod_id, $name['multiple_product_ids']))
									{
										$checked = true;
									}

									if( !empty($variations) && array_intersect($name['multiple_product_ids'], $variations))
									{
										$checked = true;
									}
								}

								if($name['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
								{
									$rtwwdpd_text_to_show_pr = str_replace('[from]', isset($name["rtwwdpd_min"]) ? $name["rtwwdpd_min"] : '', $rtwwdpd_text_to_show_pr);

									$rtwwdpd_text_to_show_pr = str_replace('[to]', isset($name["rtwwdpd_max"]) ? $name["rtwwdpd_max"] : '', $rtwwdpd_text_to_show_pr);

								}
								elseif($name['rtwwdpd_check_for'] == 'rtwwdpd_price')
								{
									$rtwwdpd_text_to_show_pr = str_replace('[from]', isset($name["rtwwdpd_min"]) ? get_woocommerce_currency_symbol().$name["rtwwdpd_min"] : '', $rtwwdpd_text_to_show_pr);

									$rtwwdpd_text_to_show_pr = str_replace('[to]', isset($name["rtwwdpd_max"]) ? get_woocommerce_currency_symbol().$name["rtwwdpd_max"] : '', $rtwwdpd_text_to_show_pr);
								}else{
									$rtwwdpd_text_to_show_pr = str_replace('[from]', isset($name["rtwwdpd_min"]) ? get_option('woocommerce_weight_unit').$name["rtwwdpd_min"] : '', $rtwwdpd_text_to_show_pr);

									$rtwwdpd_text_to_show_pr = str_replace('[to]', isset($name["rtwwdpd_max"]) ? get_option('woocommerce_weight_unit').$name["rtwwdpd_max"] : '', $rtwwdpd_text_to_show_pr);
								}
								$rtwwdpd_text_to_show_pr = str_replace('[minimum_spend]', $name["rtwwdpd_min_spend"], $rtwwdpd_text_to_show_pr);
								
								if( $checked == true && $rtwwdpd_match == false)
								{
									if($name['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
									{
										$rtwwdpd_text_to_show_pr = str_replace('[discount_value]', isset($name["rtwwdpd_discount_value"]) ? $name["rtwwdpd_discount_value"].'%' : '', $rtwwdpd_text_to_show_pr);
										
										$rtwwdpd_text_to_show_pr = str_replace('[product_name]', isset($rtwwdpd_prod_id) ? get_the_title( $rtwwdpd_prod_id) : '', $rtwwdpd_text_to_show_pr);

										echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_text_to_show_pr).'</span></div>';
									}
									else
									{   
										$rtwwdpd_text_to_show_pr = str_replace('[discount_value]', isset($name["rtwwdpd_discount_value"]) ? get_woocommerce_currency_symbol().$name["rtwwdpd_discount_value"] : '', $rtwwdpd_text_to_show_pr);

										$rtwwdpd_text_to_show_pr = str_replace('[product_name]', isset($rtwwdpd_prod_id) ? get_the_title( $rtwwdpd_prod_id) : '', $rtwwdpd_text_to_show_pr);

										$rtwwdpd_text_to_show_pr = str_replace('[discounted]', isset($name["rtwwdpd_discount_value"]) ? $rtwwdpd_symbol . $name["rtwwdpd_discount_value"] : '', $rtwwdpd_text_to_show_pr);

										echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_text_to_show_pr).'</span></div>';
									}
									$rtwwdpd_match = true;
									break 2;
								}
							}
							else
							{
								if($name['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
								{
									$rtwwdpd_text_to_show_pr = str_replace('[from]', isset($name["rtwwdpd_min"]) ? $name["rtwwdpd_min"] : '', $rtwwdpd_text_to_show_pr);

									$rtwwdpd_text_to_show_pr = str_replace('[to]', isset($name["rtwwdpd_max"]) ? $name["rtwwdpd_max"] : '', $rtwwdpd_text_to_show_pr);

								}
								elseif($name['rtwwdpd_check_for'] == 'rtwwdpd_price')
								{
									$rtwwdpd_text_to_show_pr = str_replace('[from]', isset($name["rtwwdpd_min"]) ? get_woocommerce_currency_symbol().$name["rtwwdpd_min"] : '', $rtwwdpd_text_to_show_pr);

									$rtwwdpd_text_to_show_pr = str_replace('[to]', isset($name["rtwwdpd_max"]) ? get_woocommerce_currency_symbol().$name["rtwwdpd_max"] : '', $rtwwdpd_text_to_show_pr);
								}else{
									$rtwwdpd_text_to_show_pr = str_replace('[from]', isset($name["rtwwdpd_min"]) ? $name["rtwwdpd_min"].get_option('woocommerce_weight_unit') : '', $rtwwdpd_text_to_show_pr);

									$rtwwdpd_text_to_show_pr = str_replace('[to]', isset($name["rtwwdpd_max"]) ? $name["rtwwdpd_max"].get_option('woocommerce_weight_unit') : '', $rtwwdpd_text_to_show_pr);
								}

								if($name['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
								{
									$rtwwdpd_text_to_show_pr = str_replace('[discount_value]', isset($name["rtwwdpd_discount_value"]) ? $name["rtwwdpd_discount_value"].'%' : '', $rtwwdpd_text_to_show_pr);

									$rtwwdpd_text_to_show_pr = str_replace('[product_name]', isset($rtwwdpd_prod_id) ? get_the_title( $rtwwdpd_prod_id) : '', $rtwwdpd_text_to_show_pr);


									echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_text_to_show_pr).'</span></div>';
								}
								else
								{
									$rtwwdpd_text_to_show_pr = str_replace('[discount_value]', isset($name["rtwwdpd_discount_value"]) ? get_woocommerce_currency_symbol().$name["rtwwdpd_discount_value"] : '', $rtwwdpd_text_to_show_pr);

									$rtwwdpd_text_to_show_pr = str_replace('[product_name]', isset($rtwwdpd_prod_id) ? get_the_title( $rtwwdpd_prod_id) : '', $rtwwdpd_text_to_show_pr);

									$rtwwdpd_text_to_show_pr = str_replace('[discounted]', isset($name["rtwwdpd_discount_value"]) ? $rtwwdpd_symbol . $name["rtwwdpd_discount_value"] : '', $rtwwdpd_text_to_show_pr);

									echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_text_to_show_pr).'</span></div>';
								}
								$rtwwdpd_match = true;
								break 2;
							}
						}
					}
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
						$rtwwdpd_user_role = $name['rtwwdpd_select_roles'];
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
						if(isset($name['rtwwdpd_enable_day_bogo']) && $name['rtwwdpd_enable_day_bogo'] == 'yes')
						{
							
							if(isset($name['rtwwdpd_select_day_bogo']) && !empty($name['rtwwdpd_select_day_bogo']))
							{
								if($name['rtwwdpd_select_day_bogo'] == $rtw_curnt_dayname)
								{
									$rtwwdpd_day_waise_rule = true;
								}
							}
							
							if($rtwwdpd_day_waise_rule == false)
							{
								continue;
							}
						}
						
						$rtwwdpd_date = $name['rtwwdpd_bogo_to_date'];
						if($rtwwdpd_date >= $rtwwdpd_today_date && $rtwwdpd_today_date >= $name['rtwwdpd_bogo_from_date'])
						{
							
							if(isset($name['product_id']) && is_array($name['product_id']) && !empty($name['product_id']))
							{
								
								foreach ($name['product_id'] as $no => $ids) {
									$free_products = '';
									if( is_array($name['rtwbogo']) && !empty($name['rtwbogo']))
									{
										foreach( $name['rtwbogo'] as $pro )
										{
											$free_products .= get_the_title($pro) .' , ';
										}
									}
									$this_product = wc_get_product($rtwwdpd_prod_id);
									$this_children_ids = array();
									if( !empty($this_product) && $this_product->has_child() && !empty($this_product->get_children()))
									{
										$this_children_ids = $this_product->get_children();
									}
									
									if(($ids == $rtwwdpd_prod_id || in_array($ids, $this_children_ids) )&& $rtwwdpd_match == false)
									{
										$rtwwdpd_bogo_text = str_replace('[quantity1]', isset($name['combi_quant'][$no]) ? $name['combi_quant'][$no] : '', $rtwwdpd_bogo_text);
									
										$rtwwdpd_bogo_text = str_replace('[quantity2]', isset($name['bogo_quant_free'][$no]) ? $name['bogo_quant_free'][$no] : '', $rtwwdpd_bogo_text);
										$this_product = wc_get_product($ids);
										$rtwwdpd_bogo_text = str_replace('[the-product]', $this_product->get_name(), $rtwwdpd_bogo_text);
										$rtwwdpd_free_product = isset($name['rtwbogo'][$no]) ? wc_get_product($name['rtwbogo'][$no]) : '';
										$rtwwdpd_bogo_text = str_replace('[free-product]', $rtwwdpd_free_product->get_name(), $rtwwdpd_bogo_text);
										
										echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_bogo_text).' Free</span></div>';
										$rtwwdpd_match = true;
										break 2;
									}
								}
							}
						}
					}
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
						$rtwwdpd_text_to_show_pc = get_option( 'rtwwdpd_product_offer_msg', '' );
						$rtwwdpd_date = $name['rtwwdpd_combi_to_date'];

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

						$rtwwdpd_restricted_mails = isset( $name['rtwwdpd_select_com_emails'] ) ? $name['rtwwdpd_select_com_emails'] : array();

						$rtwwdpd_cur_user_mail = get_current_user_id();
						
						if(in_array($rtwwdpd_cur_user_mail, $rtwwdpd_restricted_mails))
						{
							continue 1;
						}

						if(isset($name['rtwwdpd_combi_exclude_sale']))
						{
							if( !empty($rtwwdpd_product) && $rtwwdpd_product->is_on_sale() )
							{
								continue;
							}
						}

						if($rtwwdpd_date >= $rtwwdpd_today_date && $rtwwdpd_today_date >= $name['rtwwdpd_combi_from_date'] )
						{
							if(isset($name['product_id']) && is_array($name['product_id']) && !empty($name['product_id']))
							{
								foreach ($name['product_id'] as $keys => $val) {
									if($val == $rtwwdpd_prod_id && $rtwwdpd_match == false)
									{
										$rtwwdpd_text_to_show_pc = str_replace('[from]', isset($name["combi_quant"][0]) ? $name["combi_quant"][0] : '', $rtwwdpd_text_to_show_pc);

										$rtwwdpd_text_to_show_pc = str_replace('[to]', isset($name["combi_quant"][0]) ? $name["combi_quant"][0].'+' : '', $rtwwdpd_text_to_show_pc);

										if($name['rtwwdpd_combi_discount_type'] == 'rtwwdpd_discount_percentage')
										{
											$rtwwdpd_text_to_show_pc = str_replace('[discount_value]', isset($name["rtwwdpd_combi_discount_value"]) ? $name["rtwwdpd_combi_discount_value"].'%' : '', $rtwwdpd_text_to_show_pc);
										
											$rtwwdpd_text_to_show_pc = str_replace('[product_name]', isset($name['product_id'][0]) ? get_the_title( $name['product_id'][0]) : '', $rtwwdpd_text_to_show_pc);

											echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_text_to_show_pc).'</span></div>';
										}
										else
										{
											$rtwwdpd_text_to_show_pc = str_replace('[discount_value]', isset($name["rtwwdpd_combi_discount_value"]) ? get_woocommerce_currency_symbol().$name["rtwwdpd_combi_discount_value"] : '', $rtwwdpd_text_to_show_pc);
										
											$rtwwdpd_text_to_show_pc = str_replace('[product_name]', isset($name['product_id'][0]) ? get_the_title( $name['product_id'][0]) : '', $rtwwdpd_text_to_show_pc);

											echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_text_to_show_pc).'</span></div>';
										}
										$rtwwdpd_match = true;
										break 2;
									}
								}
							}
						}
					}
				}
			}
		}
		elseif( $rule_name == 'tier_rule_row' )
		{
			
			if( isset( $rtwwdpd_offers['tier_rule'] ) )
			{
				$rtwwdpd_rule_name = get_option('rtwwdpd_tiered_rule');
				if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
				{
					$temp_cart = $woocommerce->cart->cart_contents;
					$prods_quant = 0;
					$rtwwdpd_total_weig = 0;

					// $variations1 = $rtwwdpd_product->get_children();
					$rtwwdpd_cart_total = $woocommerce->cart->get_subtotal();
					foreach ( $temp_cart as $cart_item_key => $cart_item ) {
						$prods_quant += $cart_item['quantity'];
						if( $cart_item['data']->get_weight() != '' )
						{
							$rtwwdpd_total_weig += $cart_item['data']->get_weight();
						}
					}
					foreach ( $rtwwdpd_rule_name as $name ) 
					{
						
						
						if( $name['rtwwdpd_to_date'] >= $rtwwdpd_today_date && $rtwwdpd_today_date >= $name['rtwwdpd_from_date'] ){

							$rtwwdpd_user_role = $name['rtwwdpd_select_roles'] ;
							
							$rtwwdpd_role_matched = false;
							if(isset($rtwwdpd_user_role) && !empty($rtwwdpd_user_role))
							{
								foreach ($rtwwdpd_user_role as $rol => $role) 
								{
									if($role == 'all')
									{
										$rtwwdpd_role_matched = true;
									}
									if (in_array( $role, (array) $rtwwdpd_user->roles ) ) 
									{
										$rtwwdpd_role_matched = true;
									}
								}
							}

							if($rtwwdpd_role_matched == false)
							{
								continue;
							}

							if( isset($name['products'] ) && is_array( $name['products'] ) && !empty( $name['products'] ) )
							{
								foreach ( $name['products'] as $keys => $vals )
								{
							
									$variations_id = $rtwwdpd_product->get_children();
									$productsss = wc_get_product( $vals );
									$pname = $productsss->get_name();
									
									if( $vals == $rtwwdpd_prod_id)
									{

										if($name['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
										{
											if($name['rtwwdpd_check_for'] == 'rtwwdpd_price')
											{
												$table_html = '<table id="tier_offer_table" class="tier_offer_table percent">
												<tr><th class="rtwwdpd_tbl_pro_name rtwwdpd_custom_css" colspan="'.(count($name['quant_min']) +1).'">'.esc_html__($pname, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th></tr><tr><th class="rtwwdpd_same_th">'.esc_html__('Price', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
												foreach ($name['quant_min'] as $k => $va) {
													
													$table_html .= '<td class="rtwwdpd_same_td">'.wc_price($va) .'-'.wc_price($name['quant_max'][$k]).'</td>';
												}
												$table_html .= '</tr><tr><th class="rtwwdpd_same_th">'.esc_html__('Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
												foreach ($name['quant_min'] as $k => $va) {
													
													$table_html .= '<td class="rtwwdpd_same_td">'.$name['discount_val'][$k].'%</td>';
													
												}
												$table_html .= '</tr></table>';
												echo $table_html;
											}
											elseif($name['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
											{
												
												
												$table_html = '<table id="tier_offer_table" class="tier_offer_table percent"><tr ><th class="rtwwdpd_custom_css rtwwdpd_tbl_pro_name" colspan="'.(count($name['quant_min']) +1).'">'.esc_html__($pname, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th></tr>
												<tr><th class="rtwwdpd_same_th">'.esc_html__('Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
												foreach ($name['quant_min'] as $k => $va) {
													
													$table_html .= '<td class="rtwwdpd_same_td">'.$va .'-'.$name['quant_max'][$k].'</td>';
												}
												$table_html .= '</tr><tr><th class="rtwwdpd_same_th">'.esc_html__('Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
												foreach ($name['quant_min'] as $k => $va) {
													
													$table_html .= '<td class="rtwwdpd_same_td">'.$name['discount_val'][$k].'%</td>';
													
												}
												$table_html .= '</tr></table>';
												echo $table_html;
											}
											elseif($name['rtwwdpd_check_for'] == 'rtwwdpd_weight')
											{
												$table_html = '<table id="tier_offer_table" class="tier_offer_table percent">
												<tr><th class="rtwwdpd_tbl_pro_name rtwwdpd_custom_css" colspan="'.(count($name['quant_min']) +1).'">'.esc_html__($pname, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th></tr><tr><th class="rtwwdpd_same_th">'.esc_html__('Weight (kg)', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
												foreach ($name['quant_min'] as $k => $va) {
													
													$table_html .= '<td class="rtwwdpd_same_td">'.$va .'-'.$name['quant_max'][$k].'</td>';
												}
												$table_html .= '</tr><tr><th class="rtwwdpd_same_th">'.esc_html__('Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
												foreach ($name['quant_min'] as $k => $va) {
													
													$table_html .= '<td class="rtwwdpd_same_td">'.$name['discount_val'][$k].'%</td>';
													
												}
												$table_html .= '</tr></table>';
												echo $table_html;
											}
											// break 2;
										}
										else
										{
											if($name['rtwwdpd_check_for'] == 'rtwwdpd_price')
											{
												$table_html = '<table id="tier_offer_table" class="tier_offer_table percent">
												<tr><th class="rtwwdpd_tbl_pro_name rtwwdpd_custom_css" colspan="'.(count($name['quant_min']) +1).'">'.esc_html__($pname, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th></tr><tr><th class="rtwwdpd_same_th">'.esc_html__('Price', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
												foreach ($name['quant_min'] as $k => $va) {
													
													$table_html .= '<td class="rtwwdpd_same_td">'.wc_price($va) .'-'.wc_price($name['quant_max'][$k]).'</td>';
												}
												$table_html .= '</tr><tr><th class="rtwwdpd_same_th">'.esc_html__('Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
												foreach ($name['quant_min'] as $k => $va) {
													
													$table_html .= '<td class="rtwwdpd_same_td">'.wc_price($name['discount_val'][$k]).'</td>';
													
												}
												echo $table_html;
											}
											elseif($name['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
											{

												$table_html = '<table id="tier_offer_table" class="tier_offer_table percent"><tr ><th class="rtwwdpd_custom_css rtwwdpd_tbl_pro_name" colspan="'.(count($name['quant_min']) +1).'">'.esc_html__($pname, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th></tr>
												<tr><th class="rtwwdpd_same_th">'.esc_html__('Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
												foreach ($name['quant_min'] as $k => $va) {
													
													$table_html .= '<td class="rtwwdpd_same_td">'.$va .'-'.$name['quant_max'][$k].'</td>';
												}
												$table_html .= '</tr><tr><th class="rtwwdpd_same_th">'.esc_html__('Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
												foreach ($name['quant_min'] as $k => $va) {
													
													$table_html .= '<td class="rtwwdpd_same_td">'.wc_price($name['discount_val'][$k]).'</td>';
													
												}
												$table_html .= '</tr></table>';
												echo $table_html;
											}
											elseif($name['rtwwdpd_check_for'] == 'rtwwdpd_weight')
											{
												$table_html = '<table id="tier_offer_table" class="tier_offer_table percent">
												<tr><th class="rtwwdpd_tbl_pro_name rtwwdpd_custom_css" colspan="'.(count($name['quant_min']) +1).'">'.esc_html__($pname, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th></tr><tr><th class="rtwwdpd_same_th">'.esc_html__('Weight (kg)', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
												foreach ($name['quant_min'] as $k => $va) {
													
													$table_html .= '<td class="rtwwdpd_same_td">'.$va .'-'.$name['quant_max'][$k].'</td>';
												}
												$table_html .= '</tr><tr><th class="rtwwdpd_same_th">'.esc_html__('Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
												foreach ($name['quant_min'] as $k => $va) {
													
													$table_html .= '<td class="rtwwdpd_same_td">'.wc_price($name['discount_val'][$k]).'</td>';
													
												}
												$table_html .= '</tr></table>';
												echo $table_html;
											}
											$rtwwdpd_match = true;
											// break 3;
										}
									}
								}
							}
						}
					}
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
					foreach ( $rtwwdpd_rule_name as $ke => $name ) 
					{
						if(isset($name['category_id']) && is_array($name['category_id']) && !empty($name['category_id']))
						{
							foreach ($name['category_id'] as $keys => $vals) 
							{
								if($vals == $rtwwdpd_product_cat_id )
								{
									if($name['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
									{
										if($name['rtwwdpd_check_for'] == 'rtwwdpd_price')
										{
											$table_html = '<table id="tier_offer_table" class="tier_offer_table percent">
												<tr><th class="rtwwdpd_heading_color">'.esc_html__('Price', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
											foreach ($name['quant_min'] as $k => $va) 
											{
												
												$table_html .= '<td class="rtwwdpd_column_color">'.wc_price($va) .'-'.wc_price($name['quant_max'][$k]).'</td>';
											}
											$table_html .= '</tr><tr><th class="rtwwdpd_heading_color">'.esc_html__('Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
											foreach ($name['quant_min'] as $k => $va) {
												
												$table_html .= '<td class="rtwwdpd_column_color">'.($name['discount_val'][$k]).'%</td>';
											}
											$table_html .= '</tr></table>';
											echo $table_html;
										}
										else if($name['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
										{
											$table_html = '<table id="tier_offer_table" class="tier_offer_table percent">
												<tr><th class="rtwwdpd_heading_color">'.esc_html__('Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
											foreach ($name['quant_min'] as $k => $va) {
												
												$table_html .= '<td class="rtwwdpd_column_color">'.($va) .'-'.($name['quant_max'][$k]).'</td>';
											}
											$table_html .= '</tr><tr><th class="rtwwdpd_heading_color">'.esc_html__('Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
											foreach ($name['quant_min'] as $k => $va) {
												
												$table_html .= '<td class="rtwwdpd_column_color">'.($name['discount_val'][$k]).'%</td>';
												
											}
											$table_html .= '</tr></table>';
											echo $table_html;
										}
										else if($name['rtwwdpd_check_for'] == 'rtwwdpd_weight')
										{
											$table_html = '<table id="tier_offer_table" class="tier_offer_table percent">
												<tr><th class="rtwwdpd_heading_color">'.esc_html__('Weight', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
											foreach ($name['quant_min'] as $k => $va) {
												
												$table_html .= '<td class="rtwwdpd_column_color">'.($va) .'-'.($name['quant_max'][$k]).'</td>';
											}
											$table_html .= '</tr><tr><th class="rtwwdpd_heading_color">'.esc_html__('Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
											foreach ($name['quant_min'] as $k => $va) {
												
												$table_html .= '<td class="rtwwdpd_column_color">'.($name['discount_val'][$k]).'%</td>';
											}
											$table_html .= '</tr></table>';
											echo $table_html;
										}
										$rtwwdpd_match = true;
										// break 2;
									}
									else 
									{
										if($name['rtwwdpd_check_for'] == 'rtwwdpd_price')
										{
											$table_html = '<table id="tier_offer_table" class="tier_offer_table fixed">
												<tr><th class="rtwwdpd_heading_color">'.esc_html__('Price', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
											foreach ($name['quant_min'] as $k => $va) {
												
												$table_html .= '<td class="rtwwdpd_column_color" >'.wc_price($va) .'-'.wc_price($name['quant_max'][$k]).'</td>';
											}
											$table_html .= '</tr><tr><th class="rtwwdpd_heading_color">'.esc_html__('Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
											foreach ($name['quant_min'] as $k => $va) {
												
												$table_html .= '<td class="rtwwdpd_column_color">'.($name['discount_val'][$k]).'</td>';

											}
											$table_html .= '</tr></table>';
											echo $table_html;
										}
										else if($name['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
										{
											$table_html = '<table id="tier_offer_table" class="tier_offer_table fixed">
												<tr><th class="rtwwdpd_heading_color">'.esc_html__('Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
											foreach ($name['quant_min'] as $k => $va) 
											{
												$table_html .= '<td class="rtwwdpd_column_color">'.($va) .'-'.($name['quant_max'][$k]).'</td>';
											}
											$table_html .= '</tr><tr><th class="rtwwdpd_heading_color">'.esc_html__('Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
											foreach ($name['quant_min'] as $k => $va) 
											{
												$table_html .= '<td class="rtwwdpd_column_color">'.($name['discount_val'][$k]).'</td>';
											}
											$table_html .= '</tr></table>';
											echo $table_html;
										}
										else if($name['rtwwdpd_check_for'] == 'rtwwdpd_weight')
										{
											$table_html = '<table id="tier_offer_table" class="tier_offer_table fixed">
												<tr><th class="rtwwdpd_heading_color">'.esc_html__('Weight', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
											foreach ($name['quant_min'] as $k => $va) {
												
												$table_html .= '<td class="rtwwdpd_column_color">'.($va) .'-'.($name['quant_max'][$k]).'</td>';
											}
											$table_html .= '</tr><tr><th class="rtwwdpd_heading_color">'.esc_html__('Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
											foreach ($name['quant_min'] as $k => $va) {
												
												$table_html .= '<td class="rtwwdpd_column_color">'.($name['discount_val'][$k]).'</td>';
											}
											$table_html .= '</tr></table>';
											echo $table_html;
										}
										$rtwwdpd_match = true;
										// break 2;
									}
								}
							}
						}
					}
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

						if($name['rtwwdpd_att_to_date'] >= $rtwwdpd_today_date && $rtwwdpd_today_date >= $name['rtwwdpd_att_from_date'] )
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
								if( !empty($rtwwdpd_product) && $rtwwdpd_product->is_on_sale() )
								{
									continue 1;
								}
							}

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
							$rtwwdpd_match = true;
							break 1;
						}
					}
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
							if(isset($name['rtwwdpd_tag_exclude_sale']))
							{
								if( !empty($rtwwdpd_product) && $rtwwdpd_product->is_on_sale() )
								{
									continue;
								}
							}
							if($name['rtwwdpd_tag_to_date'] >= $rtwwdpd_today_date && $rtwwdpd_today_date >= $name['rtwwdpd_tag_from_date'] )
							{
								if(isset($name['rtw_product_tags']) && is_array($name['rtw_product_tags']) && !empty($name['rtw_product_tags']))
								{
									foreach ($name['rtw_product_tags'] as $tag => $tags) 
									{	
										if(in_array($tags, array_column($rtwwdpd_tag, 'term_id')) && $rtwwdpd_match == false)
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
											$rtwwdpd_match = true;
											break 2;
										}
									}
								}
							}
						}
					}
				}
			}
		}

		$rtwwdpd_nth_order_option = array();
		$rtwwdpd_nth_enable = get_option('rtwwdpd_enable_nth_order');
		
		if( isset( $rtwwdpd_nth_enable ) && $rtwwdpd_nth_enable == 'enable' )
		{
			$rtwwdpd_nth_order_option = get_option( 'rtwwdpd_nth_order' );
		}
		if( is_array( $rtwwdpd_nth_order_option ) && !empty( $rtwwdpd_nth_order_option ) )
		{
			if($rtwwdpd_nth_enable == 'enable')
			{
				foreach ($rtwwdpd_nth_order_option as $key => $value)
				{	
					
					$rtwwdpd_user_role = $value['rtwwdpd_select_roles'] ;
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
						continue 1;
					}
					
					if($value['rtwwdpd_from_date'] > $rtwwdpd_today_date || $value['rtwwdpd_to_date'] < $rtwwdpd_today_date)
					{
						continue 1;
					}

					$rtwwdpd_dsnt_val = isset($value['rtwwdpd_dscnt_val']) ? $value['rtwwdpd_dscnt_val'] : '';

					$rtwwdpd_order_no = isset($value['rtwwdpd_order_no']) ? $value['rtwwdpd_order_no'] : '';
					$rtwqwdpd_nth_text_to_show;
					if(!empty($rtwwdpd_product))
					{
						$rtwwdpd_terms = get_the_terms( $rtwwdpd_product->get_id(), 'product_cat' );
					}
					else{
						$rtwwdpd_terms = array();
					}
					
					$rtwwdpd_product_cat_id = array();
					if(is_array($rtwwdpd_terms) && !empty($rtwwdpd_terms))
					{
						foreach ($rtwwdpd_terms  as $term  ) {
							$rtwwdpd_product_cat_id[] = $term->term_id;
						}
					}
					if(isset($value['rtwwdpd_rule_for_plus']) && !empty($value['rtwwdpd_rule_for_plus']))
					{
						if($value['rtwwdpd_rule_for_plus'] == 'rtwwdpd_category')
						{
							if(isset($value['category_ids']) && !empty($value['category_ids']))
							{
								
								$cat = $value['category_ids'];
								foreach($cat as $cat_key => $cat_val)
								{
									if(in_array($cat_val, $rtwwdpd_product_cat_id) && $rtwwdpd_match == false)
									{

										$rtwqwdpd_nth_text_to_show ='Get '.$rtwwdpd_dsnt_val.'% off on  '.$rtwwdpd_order_no.' order';
										echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwqwdpd_nth_text_to_show).'</span></div>';	
									}
								}
								$rtwwdpd_match = true;
								break 1;
							}
						}
						if($value['rtwwdpd_rule_for_plus'] == 'rtwwdpd_product')
						{
							// $prod_id_arr = $value['product_ids'];
							$checked = false;
							if(!empty($rtwwdpd_product) && !empty($rtwwdpd_product->get_children()))
							{
								$variations = $rtwwdpd_product->get_children();
							}else{
								$variations = '';
							}
							$rtwwdpd_id =  $value['product_ids'];
							if( in_array($rtwwdpd_prod_id,$rtwwdpd_id ) )
							{
								$checked = true;
							}
							if( !empty($variations) && in_array($rtwwdpd_id, $variations))
							{
								$checked = true;
							}
							if($checked == true && $rtwwdpd_match == false)
							{

								$rtwqwdpd_nth_text_to_show ='Get '.$rtwwdpd_dsnt_val.' off on  '.$rtwwdpd_order_no.' order';
								echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwqwdpd_nth_text_to_show).'</span></div>';	
							}
							$rtwwdpd_match = true;
							break 1;
						}
					}
				}
			}
		}

		  // ////////////////// Least amount product discount ////////////////////
		  $rtwwdpd_least_option = array();
		  $rtwwdpd_temp_cart = WC()->cart->get_cart();
		  
		  $rtwwdpd_least_enable = get_option( 'rtwwdpd_enable_least_free' );
		  
		  if( isset( $rtwwdpd_least_enable ) && $rtwwdpd_least_enable == 'enable' )
		  {
			  $rtwwdpd_least_option = get_option( 'rtwwdpd_get_least_free' );
			  
		  }
  
		  if( isset($rtwwdpd_least_option) && is_array($rtwwdpd_least_option) && !empty($rtwwdpd_least_option) )
		  {
			$i = 0;
			$rtwwdpd_user = wp_get_current_user();
			$rtwwdpd_num_decimals = apply_filters( 'rtwwdpd_get_decimals', (int) get_option( 'woocommerce_price_num_decimals' ) );
			$rtwwdpd_no_oforders = wc_get_customer_order_count( get_current_user_id() );
			$rtwwdpd_today_date = current_time('Y-m-d');
			$rtwwdpd_ordrtotal = wc_get_customer_total_spent(get_current_user_id());
			$set_id = 1;
			
			foreach ($rtwwdpd_least_option as $catt => $pro_rul)
			{

			if ( is_page( 'cart' ) || is_cart() ) {
				wc_clear_notices();
				wc_add_notice( ( isset( $pro_rul['rtwwdpd_offer_msg'] ) ? $pro_rul['rtwwdpd_offer_msg'] : '' ), 'notice');
			}
			
			$rtwwdpd_total_quantity = 0;
			$least_price_pro_id = '';
			$least_price = 9999999;
			$rtwwdpd_catids = '';
			
			$rtwwdpd_terms = get_the_terms( $rtwwdpd_product->get_id(), 'product_cat' );
							
			if( isset( $pro_rul['product_exe_id'] ) && !empty( $pro_rul['product_exe_id'] ) )
			{
				if( !empty( $least_price_pro_id ) && in_array( $least_price_pro_id ,$pro_rul['product_exe_id']) )
				{
					continue 1;
				}
			}
			
			$rtwwdpd_matched = true;
			if( $pro_rul['rtwwdpd_from_date'] > $rtwwdpd_today_date || $pro_rul['rtwwdpd_to_date'] < $rtwwdpd_today_date )
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
					if ( in_array( $role, (array) $rtwwdpd_user->roles ) ) {
						$rtwwdpd_role_matched = true;
					}
				}
			}
			if($rtwwdpd_role_matched == false)
			{
				continue 1;
			}
			
			
			$sabcd = 'fication_done';
			$rtwwdpd_verification_done = get_site_option( 'rtwbma_veri'.$sabcd, array() );
				if( !empty( $rtwwdpd_verification_done ) && $rtwwdpd_verification_done['status'] == true && !empty($rtwwdpd_verification_done['purchase_code']) ) 
				{ 
					if(isset($pro_rul['rtwwdpd_discount_on']) && $pro_rul['rtwwdpd_discount_on'] == 2 )
					{
						if(!empty($rtwwdpd_product))
						{
							$rtwwdpd_terms = get_the_terms( $rtwwdpd_product->get_id(), 'product_cat' );
						}
						else
						{
							$rtwwdpd_terms = array();
						}
						$rtwwdpd_product_cat_id = array();
						if(is_array($rtwwdpd_terms) && !empty($rtwwdpd_terms))
						{
							foreach ($rtwwdpd_terms  as $term  ) {
								$rtwwdpd_product_cat_id[] = $term->term_id;
							}
						}
						$rtwwdpd_min_purchase_quant = '';
						if(isset($pro_rul['category_id']) && !empty($pro_rul['category_id']))
						{
							
							$cat = $pro_rul['category_id'];
							$rtwwdpd_dscnt_val = isset($pro_rul['rtwwdpd_dscnt_cat_val']) ? $pro_rul['rtwwdpd_dscnt_cat_val'] : '';
							if(isset($pro_rul['rtwwdpd_min_cat']) && !empty($pro_rul['rtwwdpd_min_cat']))
							{
								$rtwwdpd_min_purchase_quant = $pro_rul['rtwwdpd_min_cat'];
							}
							else
							{
								$rtwwdpd_min_purchase_quant = 1;
							}
							
							$rtwwdpd_dcnt = $rtwwdpd_dscnt_val /(int)$rtwwdpd_min_purchase_quant;
							$rtwwdpd_dis = floatval ($rtwwdpd_dcnt );
							if (preg_match('/\.\d{3,}/', $rtwwdpd_dis)) {
								$rtwwdpd_d = sprintf ("%.2f", $rtwwdpd_dcnt );
							} 
							else 
							{
								$rtwwdpd_d = $rtwwdpd_dis;
							}
							
							foreach($cat as $cat_key => $cat_val)
							{
								if(in_array($cat_val, $rtwwdpd_product_cat_id) && $rtwwdpd_match == false)
								{

									$rtwwdpd_least_amount_text ='Get '.$rtwwdpd_d.'%  off on each quantity on  Minimum purchase of '.$rtwwdpd_min_purchase_quant;
									echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_least_amount_text).'</span></div>';	
								}
							}
							$rtwwdpd_match = true;
							break 1;
						}
						
						
					}
					elseif(isset($pro_rul['rtwwdpd_discount_on']) && $pro_rul['rtwwdpd_discount_on'] == 1 )
					{
						
						if(!isset($pro_rul['category_id']))
						{
							$rtwwdpd_dscnt_val = isset($pro_rul['rtwwdpd_dscnt_cat_val']) ? $pro_rul['rtwwdpd_dscnt_cat_val'] : '';
							if(isset($pro_rul['rtwwdpd_min_cat']) && !empty($pro_rul['rtwwdpd_min_cat']))
							{
								$rtwwdpd_min_purchase_quant = $pro_rul['rtwwdpd_min_cat'];
							}
							else
							{
								$rtwwdpd_min_purchase_quant = 1;
							}

							$rtwwdpd_dcnt = $rtwwdpd_dscnt_val /(int)$rtwwdpd_min_purchase_quant;
							
							$rtwwdpd_dis = floatval ( $rtwwdpd_dcnt );
							if (preg_match('/\.\d{3,}/', $rtwwdpd_dis)) {
								$rtwwdpd_d = sprintf ("%.2f", $rtwwdpd_dcnt );
							} 
							else 
							{
								$rtwwdpd_d = $rtwwdpd_dis;
							}
							$rtwwdpd_least_amount_text ='Get '.$rtwwdpd_d.'%  off on each quantity on  Minimum purchase of '.$rtwwdpd_min_purchase_quant;
							echo '<div class="rtwwdpd_show_offer"><span>'.esc_html($rtwwdpd_least_amount_text).'</span></div>';
						}
						$rtwwdpd_match = true;
						break 2;
					}
					
				}
				  
			}
		  }
	}
}
echo '<div class="rtwwdpd_apply_on_variation_'.$rtwwdpd_prod_id.'"></div>';
