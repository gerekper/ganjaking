<?php
global $woocommerce;
$rtwwdpd_rule_name = get_option('rtwwdpd_cart_rule');
$rtwwdpd_get_setting_priority = get_option('rtwwdpd_setting_priority');
$rtwwdpd_weight_unit = get_option('woocommerce_weight_unit');
$rtwwdpd_symbol = get_woocommerce_currency_symbol();
$rtwwdpd_cart_text = isset($rtwwdpd_get_setting_priority['rtwwdpd_cart_text_show']) ? $rtwwdpd_get_setting_priority['rtwwdpd_cart_text_show'] : 'Buy from [from_quant] to [to_quant] Get [discounted] Off';
$rtwwdpd_user = wp_get_current_user();
if( isset( $rtwwdpd_get_setting_priority['rtw_offer_on_cart'] ) && $rtwwdpd_get_setting_priority['rtw_offer_on_cart'] == 'rtw_price_yes')
{
	if( is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name) && isset($rtwwdpd_get_setting_priority['cart_rule']) )
	{
		foreach ($rtwwdpd_rule_name as $keys => $name) 
		{	
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
			if( $rtwwdpd_role_matched == false )
			{
				continue 1;
			}

			$rtwwdpd_cart_text = isset($rtwwdpd_get_setting_priority['rtwwdpd_cart_text_show']) ? $rtwwdpd_get_setting_priority['rtwwdpd_cart_text_show'] : 'Buy from [from_quant] to [to_quant] Get [discounted] Off';
			$rtwwdpd_date = $name['rtwwdpd_to_date'];
			if($rtwwdpd_date >= $rtwwdpd_today_date && $rtwwdpd_today_date >= $name['rtwwdpd_from_date'] ){
				//--------------------------//
				if(isset($name['rtwwdpd_select_product']) && is_array($name['rtwwdpd_select_product']) && !empty($name['rtwwdpd_select_product']))
				{
					$selected_cart_pro = $name['rtwwdpd_select_product'];
					$cart_pro = array();

					if(sizeof(WC()->cart->get_cart())>0)
					{
						foreach(WC()->cart->get_cart() as $cart_item_key => $value)
						{
							$_product = $value['data'];
							$cart_pro[] = $_product->get_id();
						}
					}
					if(!empty(array_diff($selected_cart_pro,$cart_pro)))
					{
						continue;
					}
				}
				//----------------------------------------//

				if($name['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
				{
					if($name['rtwwdpd_check_for']=='rtwwdpd_quantity')
					{ 
						$rtwwdpd_cart_text = str_replace('[from_quant]', isset($name['rtwwdpd_min']) ? $name['rtwwdpd_min'] : '', $rtwwdpd_cart_text);
						$rtwwdpd_cart_text = str_replace('[to_quant]', isset($name['rtwwdpd_max']) ? $name['rtwwdpd_max'] : '', $rtwwdpd_cart_text);
						$rtwwdpd_cart_text = str_replace('[discounted]', isset($name['rtwwdpd_discount_value']) ? $name['rtwwdpd_discount_value']. '%' : '', $rtwwdpd_cart_text);
						wc_print_notice( $rtwwdpd_cart_text ,'notice' );
					
					}
					elseif($name['rtwwdpd_check_for']=='rtwwdpd_cart_count')
					{ 

						$rtwwdpd_cart_text = str_replace('[from_quant]', isset($name['rtwwdpd_min']) ? $name['rtwwdpd_min'].' products' : '', $rtwwdpd_cart_text);
						$rtwwdpd_cart_text = str_replace('[to_quant]', isset($name['rtwwdpd_max']) ? $name['rtwwdpd_max'] : '', $rtwwdpd_cart_text);
						$rtwwdpd_cart_text = str_replace('[discounted]', isset($name['rtwwdpd_discount_value']) ? $name['rtwwdpd_discount_value']. '%' : '', $rtwwdpd_cart_text);
						wc_print_notice( $rtwwdpd_cart_text ,'notice' );
					
					}
					elseif($name['rtwwdpd_check_for']=='rtwwdpd_price')
					{

						$rtwwdpd_cart_text = str_replace('[from_quant]', isset($name['rtwwdpd_min']) ? $rtwwdpd_symbol . $name['rtwwdpd_min'] : '', $rtwwdpd_cart_text);
						$rtwwdpd_cart_text = str_replace('[to_quant]', isset($name['rtwwdpd_max']) ? $rtwwdpd_symbol . $name['rtwwdpd_max'] : '', $rtwwdpd_cart_text);
						$rtwwdpd_cart_text = str_replace('[discounted]', isset($name['rtwwdpd_discount_value']) ? $name['rtwwdpd_discount_value']. '%' : '', $rtwwdpd_cart_text);
						wc_print_notice( $rtwwdpd_cart_text ,'notice' );
					}
					elseif($name['rtwwdpd_check_for']=='rtwwdpd_weight')
					{
						$rtwwdpd_cart_text = str_replace('[from_quant]', isset($name['rtwwdpd_min']) ? $name['rtwwdpd_min'] . $rtwwdpd_weight_unit : '', $rtwwdpd_cart_text);
						$rtwwdpd_cart_text = str_replace('[to_quant]', isset($name['rtwwdpd_max']) ? $name['rtwwdpd_max'] . $rtwwdpd_weight_unit : '', $rtwwdpd_cart_text);
						$rtwwdpd_cart_text = str_replace('[discounted]', isset($name['rtwwdpd_discount_value']) ? $name['rtwwdpd_discount_value']. '%' : '', $rtwwdpd_cart_text);
						wc_print_notice( $rtwwdpd_cart_text ,'notice' );
					}
					elseif($name['rtwwdpd_check_for']=='rtwwdpd_total')
					{
						$rtwwdpd_cart_text = str_replace('[from_quant]', isset($name['rtwwdpd_min']) ? $rtwwdpd_symbol . $name['rtwwdpd_min'] : '', $rtwwdpd_cart_text);
						$rtwwdpd_cart_text = str_replace('[to_quant]', isset($name['rtwwdpd_max']) ? $rtwwdpd_symbol . $name['rtwwdpd_max'] : '', $rtwwdpd_cart_text);
						$rtwwdpd_cart_text = str_replace('[discounted]', isset($name['rtwwdpd_discount_value']) ? $name['rtwwdpd_discount_value']. '%' : '', $rtwwdpd_cart_text);
						wc_print_notice( $rtwwdpd_cart_text ,'notice' );
					}
					elseif($name['rtwwdpd_check_for']=='rtwwdpd_subtotal')
					{
						$rtwwdpd_cart_text = str_replace('[from_quant]', isset($name['rtwwdpd_min']) ? $rtwwdpd_symbol . $name['rtwwdpd_min'] : '', $rtwwdpd_cart_text);
						$rtwwdpd_cart_text = str_replace('[to_quant]', isset($name['rtwwdpd_max']) ? $rtwwdpd_symbol . $name['rtwwdpd_max'] : '', $rtwwdpd_cart_text);
						$rtwwdpd_cart_text = str_replace('[discounted]', isset($name['rtwwdpd_discount_value']) ? $name['rtwwdpd_discount_value']. '%' : '', $rtwwdpd_cart_text);
						wc_print_notice( $rtwwdpd_cart_text ,'notice' );
					}
				}
				else
				{
					if($name['rtwwdpd_check_for']=='rtwwdpd_quantity')
					{
						if($name['rtwwdpd_max_discount'] < $name['rtwwdpd_discount_value'])
						{
							$name['rtwwdpd_discount_value'] = $name['rtwwdpd_max_discount'];
						}

						$rtwwdpd_cart_text = str_replace('[from_quant]', isset($name['rtwwdpd_min']) ? $name['rtwwdpd_min'] : '', $rtwwdpd_cart_text);
						$rtwwdpd_cart_text = str_replace('[to_quant]', isset($name['rtwwdpd_max']) ? $name['rtwwdpd_max'] : '', $rtwwdpd_cart_text);
						$rtwwdpd_cart_text = str_replace('[discounted]', isset($name['rtwwdpd_discount_value']) ? $rtwwdpd_symbol . $name['rtwwdpd_discount_value'] : '', $rtwwdpd_cart_text);
						wc_print_notice( $rtwwdpd_cart_text ,'notice' );
					}
					elseif($name['rtwwdpd_check_for']=='rtwwdpd_price')
					{
						if($name['rtwwdpd_max_discount'] < $name['rtwwdpd_discount_value'])
						{
							$name['rtwwdpd_discount_value'] = $name['rtwwdpd_max_discount'];
						}

						$rtwwdpd_cart_text = str_replace('[from_quant]', isset($name['rtwwdpd_min']) ? $rtwwdpd_symbol . $name['rtwwdpd_min'] : '', $rtwwdpd_cart_text);
						$rtwwdpd_cart_text = str_replace('[to_quant]', isset($name['rtwwdpd_max']) ? $rtwwdpd_symbol . $name['rtwwdpd_max'] : '', $rtwwdpd_cart_text);
						$rtwwdpd_cart_text = str_replace('[discounted]', isset($name['rtwwdpd_discount_value']) ?  $rtwwdpd_symbol . $name['rtwwdpd_discount_value'] : '', $rtwwdpd_cart_text);
						wc_print_notice( $rtwwdpd_cart_text ,'notice' );
					}
					elseif($name['rtwwdpd_check_for']=='rtwwdpd_weight')
					{
						if($name['rtwwdpd_max_discount'] < $name['rtwwdpd_discount_value'])
						{
							$name['rtwwdpd_discount_value'] = $name['rtwwdpd_max_discount'];
						}

						$rtwwdpd_cart_text = str_replace('[from_quant]', isset($name['rtwwdpd_min']) ? $rtwwdpd_weight_unit . $name['rtwwdpd_min'] : '', $rtwwdpd_cart_text);
						$rtwwdpd_cart_text = str_replace('[to_quant]', isset($name['rtwwdpd_max']) ? $rtwwdpd_weight_unit . $name['rtwwdpd_max'] : '', $rtwwdpd_cart_text);
						$rtwwdpd_cart_text = str_replace('[discounted]', isset($name['rtwwdpd_discount_value']) ? $rtwwdpd_symbol . $name['rtwwdpd_discount_value'] : '', $rtwwdpd_cart_text);
						wc_print_notice( $rtwwdpd_cart_text ,'notice' );
					}
					elseif($name['rtwwdpd_check_for']=='rtwwdpd_total')
					{
						if($name['rtwwdpd_max_discount'] < $name['rtwwdpd_discount_value'])
						{
							$name['rtwwdpd_discount_value'] = $name['rtwwdpd_max_discount'];
						}

						$rtwwdpd_cart_text = str_replace('[from_quant]', isset($name['rtwwdpd_min']) ? $rtwwdpd_symbol . $name['rtwwdpd_min'] : '', $rtwwdpd_cart_text);
						$rtwwdpd_cart_text = str_replace('[to_quant]', isset($name['rtwwdpd_max']) ? $rtwwdpd_symbol . $name['rtwwdpd_max'] : '', $rtwwdpd_cart_text);
						$rtwwdpd_cart_text = str_replace('[discounted]', isset($name['rtwwdpd_discount_value']) ? $rtwwdpd_symbol . $name['rtwwdpd_discount_value'] : '', $rtwwdpd_cart_text);
						wc_print_notice( $rtwwdpd_cart_text ,'notice' );
					}
					elseif($name['rtwwdpd_check_for']=='rtwwdpd_subtotal')
					{
						$rtwwdpd_cart_text = str_replace('[from_quant]', isset($name['rtwwdpd_min']) ? $rtwwdpd_symbol . $name['rtwwdpd_min'] : '', $rtwwdpd_cart_text);
						$rtwwdpd_cart_text = str_replace('[to_quant]', isset($name['rtwwdpd_max']) ? $rtwwdpd_symbol . $name['rtwwdpd_max'] : '', $rtwwdpd_cart_text);
						$rtwwdpd_cart_text = str_replace('[discounted]', isset($name['rtwwdpd_discount_value']) ? $name['rtwwdpd_discount_value']. '%' : '', $rtwwdpd_cart_text);
						wc_print_notice( $rtwwdpd_cart_text ,'notice' );
					}
				}
			}
		}
	}
}

if( isset( $rtwwdpd_get_setting_priority['rtw_tier_offer_on_cart'] ) && $rtwwdpd_get_setting_priority['rtw_tier_offer_on_cart'] == 'rtw_price_yes' && isset( $rtwwdpd_get_setting_priority['tier_rule'] ) && $rtwwdpd_get_setting_priority['tier_rule'] == 1 )
{ 
	$rtwwdpd_rule_name = get_option('rtwwdpd_tiered_rule');

	if(is_array($rtwwdpd_rule_name) && !empty($rtwwdpd_rule_name))
	{
		$temp_cart = $woocommerce->cart->cart_contents;
		$prods_quant = 0;
		$rtwwdpd_total_weig = 0;
		$rtwwdpd_cart_total = $woocommerce->cart->get_subtotal();
		foreach ( $temp_cart as $cart_item_key => $cart_item ) {
			$prods_quant += $cart_item['quantity'];
			if( $cart_item['data']->get_weight() != '' )
			{
				$rtwwdpd_total_weig += $cart_item['data']->get_weight();
			}
		}
		foreach ( $rtwwdpd_rule_name as $name ) {
			$match = false;
			if( $name['rtwwdpd_to_date'] >= $rtwwdpd_today_date && $rtwwdpd_today_date >= $name['rtwwdpd_from_date'] ){
				if( isset($name['products'] ) && is_array( $name['products'] ) && !empty( $name['products'] ) )
				{
					foreach ( $name['products'] as $keys => $vals ) 
					{
						$productsss = wc_get_product( $vals );
						$pname = $productsss->get_name();
						
						foreach ( $temp_cart as $cart_item_key => $cart_item ) 
						{
							if( $vals == $cart_item['product_id'] || isset($cart_item['variation_id']) && $vals == $cart_item['variation_id'] )
							{
								$prods_quant = $cart_item['quantity'];
								$match = true;
							}
						}
						if( $match == false)
						{
							continue 2;
						}

						if( $name['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage' )
						{
							if( $name['rtwwdpd_check_for'] == 'rtwwdpd_price' )
							{
								$table_html = '<table id="tier_offer_table" class="tier_offer_table">
								<tr><th class="rtwwdpd_tbl_pro_name" colspan="'.(count($name['quant_min']) +1).'">'.esc_html__($pname, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th></tr><tr><th>'.esc_html__('Price', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
								foreach ($name['quant_min'] as $k => $va) {
									
									$table_html .= '<td>'.wc_price($va) .'-'.wc_price($name['quant_max'][$k]).'</td>';
								}
								$table_html .= '</tr><tr><th>'.esc_html__('Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
								foreach ($name['quant_min'] as $k => $va) {
									
									$table_html .= '<td>'.($name['discount_val'][$k]).'%</td>';
									
								}
								$table_html .= '</tr></table>';
								echo $table_html;
							}
							elseif( $name['rtwwdpd_check_for'] == 'rtwwdpd_quantity' )
							{
								$table_html = '<table id="tier_offer_table" class="tier_offer_table">
								<tr><th class="rtwwdpd_tbl_pro_name" colspan="'.(count($name['quant_min']) +1).'">'.esc_html__($pname, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th></tr><tr><th>'.esc_html__('Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
								foreach ($name['quant_min'] as $k => $va) {
									
									$table_html .= '<td>'.($va) .'-'.($name['quant_max'][$k]).'</td>';
								}
								$table_html .= '</tr><tr><th>'.esc_html__('Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
								foreach ($name['quant_min'] as $k => $va) {
									
									$table_html .= '<td>'.($name['discount_val'][$k]).'%</td>';
									
								}
								$table_html .= '</tr></table>';
								echo $table_html;
							}
							elseif($name['rtwwdpd_check_for'] == 'rtwwdpd_weight')
							{
								$table_html = '<table id="tier_offer_table" class="tier_offer_table">
								<tr><th class="rtwwdpd_tbl_pro_name" colspan="'.(count($name['quant_min']) +1).'">'.esc_html__($pname, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th></tr><tr><th>'.esc_html__('Weight', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
								foreach ($name['quant_min'] as $k => $va) {
									
									$table_html .= '<td>'.($va) .'-'.($name['quant_max'][$k]).'</td>';
								}
								$table_html .= '</tr><tr><th>'.esc_html__('Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
								foreach ($name['quant_min'] as $k => $va) {
									
									$table_html .= '<td>'.($name['discount_val'][$k]).'%</td>';
									
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
								$table_html = '<table id="tier_offer_table" class="tier_offer_table">
								<tr><th class="rtwwdpd_tbl_pro_name" colspan="'.(count($name['quant_min']) +1).'">'.esc_html__($pname, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th></tr><tr><th>'.esc_html__('Price', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
								foreach ($name['quant_min'] as $k => $va) {
									
									$table_html .= '<td>'.wc_price($va) .'-'.wc_price($name['quant_max'][$k]).'</td>';
								}
								$table_html .= '</tr><tr><th>'.esc_html__('Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
								foreach ($name['quant_min'] as $k => $va) {
									
									$table_html .= '<td>'.wc_price($name['discount_val'][$k]).'</td>';
									
								}
								$table_html .= '</tr></table>';
								echo $table_html;
							}
							elseif($name['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
							{
								$table_html = '<table id="tier_offer_table" class="tier_offer_table">
								<tr><th class="rtwwdpd_tbl_pro_name" colspan="'.(count($name['quant_min']) +1).'">'.esc_html__($pname, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th></tr><tr><th>'.esc_html__('Quantity', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
								foreach ($name['quant_min'] as $k => $va) {
									
									$table_html .= '<td>'.($va) .'-'.($name['quant_max'][$k]).'</td>';
								}
								$table_html .= '</tr><tr><th>'.esc_html__('Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
								foreach ($name['quant_min'] as $k => $va) {
									
									$table_html .= '<td>'.($name['discount_val'][$k]).'</td>';
									
								}
								$table_html .= '</tr></table>';
								echo $table_html;
							}
							elseif($name['rtwwdpd_check_for'] == 'rtwwdpd_weight')
							{
								$table_html = '<table id="tier_offer_table" class="tier_offer_table">
								<tr><th class="rtwwdpd_tbl_pro_name" colspan="'.(count($name['quant_min']) +1).'">'.esc_html__($pname, 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th></tr><tr><th>'.esc_html__('Weight', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
								foreach ($name['quant_min'] as $k => $va) {
									
									$table_html .= '<td>'.($va) .'-'.($name['quant_max'][$k]).'</td>';
								}
								$table_html .= '</tr><tr><th>'.esc_html__('Discount', 'rtwwdpd-woo-dynamic-pricing-discounts-with-ai').'</th>';
								foreach ($name['quant_min'] as $k => $va) {
									
									$table_html .= '<td>'.wc_price($name['discount_val'][$k]).'</td>';
									
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