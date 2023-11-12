<?php
/**
 * Class RTWWDPD_Advance_Category to calculate discount according to all discount rule.
 *
 * @since    1.0.0
 */
class RTWWDPD_Advance_Total extends RTWWDPD_Advance_Base {
	/**
	 * variable to set instance of all modules.
	 *
	 * @since    1.0.0
	 */
	private static $instance;
	/**
	 * function to set instance of all modules.
	 *
	 * @since    1.0.0
	 */
	public static function rtwwdpd_instance() {
		if ( self::$instance == null ) {
			self::$instance = new RTWWDPD_Advance_Total( 'advanced_totals' );
		}

		return self::$instance;
	}

	/**
	 * variable to check rules priority.
	 *
	 * @since    1.0.0
	 */
	public $adjustment_sets;

	/**
	 * variable to check rules priority.
	 *
	 * @since    1.3.1
	 */
	public $priority_array;

	/**
	 * construct function.
	 *
	 * @since    1.0.0
	 */
	public function __construct( $module_id ) {
		parent::__construct( $module_id );

		$rtwwdpd_get_settings = get_option('rtwwdpd_setting_priority');

		$rtwwdpd_i = 0;
		$rtwwdpd_priority = array();
		if(is_array($rtwwdpd_get_settings) && !empty($rtwwdpd_get_settings)){
			foreach ($rtwwdpd_get_settings as $key => $value) {
				if($key == 'cart_rule_row')
				{
					if(isset($rtwwdpd_get_settings['cart_rule']) && $rtwwdpd_get_settings['cart_rule'] == 1)
					{
						$rtwwdpd_priority[$rtwwdpd_i] = $key;
						$rtwwdpd_i++;
					}
				}
				elseif($key == 'pro_rule_row')
				{
					if(isset($rtwwdpd_get_settings['pro_rule']) && $rtwwdpd_get_settings['pro_rule'] == 1)
					{
						$rtwwdpd_priority[$rtwwdpd_i] = $key;
						$rtwwdpd_i++;
					}
				}
				elseif($key == 'bogo_rule_row')
				{
					if(isset($rtwwdpd_get_settings['bogo_rule']) && $rtwwdpd_get_settings['bogo_rule'] == 1)
					{
						$rtwwdpd_priority[$rtwwdpd_i] = $key;
						$rtwwdpd_i++;
					}
				}
				elseif($key == 'tier_rule_row')
				{
					if(isset($rtwwdpd_get_settings['tier_rule']) && $rtwwdpd_get_settings['tier_rule'] == 1)
					{
						$rtwwdpd_priority[$rtwwdpd_i] = $key;
						$rtwwdpd_i++;
					}
				}
				elseif($key == 'pro_com_rule_row')
				{
					if(isset($rtwwdpd_get_settings['pro_com_rule']) && $rtwwdpd_get_settings['pro_com_rule'] == 1)
					{
						$rtwwdpd_priority[$rtwwdpd_i] = $key;
						$rtwwdpd_i++;
					}
				}
				elseif($key == 'cat_com_rule_row')
				{
					if(isset($rtwwdpd_get_settings['cat_com_rule']) && $rtwwdpd_get_settings['cat_com_rule'] == 1)
					{
						$rtwwdpd_priority[$rtwwdpd_i] = $key;
						$rtwwdpd_i++;
					}
				}
				elseif($key == 'tier_cat_rule_row')
				{
					if(isset($rtwwdpd_get_settings['tier_cat_rule']) && $rtwwdpd_get_settings['tier_cat_rule'] == 1)
					{
						$rtwwdpd_priority[$rtwwdpd_i] = $key;
						$rtwwdpd_i++;
					}
				}	
				elseif($key == 'var_rule_row')
				{
					if(isset( $rtwwdpd_get_settings['var_rule']) && $rtwwdpd_get_settings['var_rule'] == 1 )
					{
						$rtwwdpd_priority[$rtwwdpd_i] = $key;
						$rtwwdpd_i++;
					}
				}
				elseif($key == 'cat_rule_row')
				{
					if(isset($rtwwdpd_get_settings['cat_rule']) && $rtwwdpd_get_settings['cat_rule'] == 1)
					{
						$rtwwdpd_priority[$rtwwdpd_i] = $key;
						$rtwwdpd_i++;
					}
				}
				elseif($key == 'bogo_cat_rule_row')
				{
					if(isset($rtwwdpd_get_settings['bogo_cat_rule']) && $rtwwdpd_get_settings['bogo_cat_rule'] == 1)
					{
						$rtwwdpd_priority[$rtwwdpd_i] = $key;
						$rtwwdpd_i++;
					}
				}
				elseif($key == 'attr_rule_row')
				{
					if(isset($rtwwdpd_get_settings['attr_rule']) && $rtwwdpd_get_settings['attr_rule'] == 1)
					{
						$rtwwdpd_priority[$rtwwdpd_i] = $key;
						$rtwwdpd_i++;
					}
				}
				elseif($key == 'prod_tag_rule_row')
				{
					if(isset($rtwwdpd_get_settings['prod_tag_rule']) && $rtwwdpd_get_settings['prod_tag_rule'] == 1)
					{
						$rtwwdpd_priority[$rtwwdpd_i] = $key;
						$rtwwdpd_i++;
					}
				}
				elseif($key == 'pay_rule_row')
				{
					if(isset($rtwwdpd_get_settings['pay_rule']) && $rtwwdpd_get_settings['pay_rule'] == 1)
					{
						$rtwwdpd_priority[$rtwwdpd_i] = $key;
						$rtwwdpd_i++;
					}
				}
				elseif($key == 'ship_rule_row')
				{
					if(isset($rtwwdpd_get_settings['ship_rule']) && $rtwwdpd_get_settings['ship_rule'] == 1)
					{
						$rtwwdpd_priority[$rtwwdpd_i] = $key;
						$rtwwdpd_i++;
					}
				}
				elseif($key == 'var_rule_row')
				{
					if(isset($rtwwdpd_get_settings['var_rule']) && $rtwwdpd_get_settings['var_rule'] == 1)
					{
						$rtwwdpd_priority[$rtwwdpd_i] = $key;
						$rtwwdpd_i++;
					}
				}
			}
		}

		if ( is_array( $rtwwdpd_priority ) && !empty( $rtwwdpd_priority ) ) 
		{
			$i = 0;
			$id = 1;

			$rtwwdpd_nam = array();

			foreach ($rtwwdpd_priority as $key => $value) {
				$rtwwdpd_set_data = array();
				if($value == 'pro_rule_row')
				{	
					$rtwwdpd_nam[] = $value;
					$rtwwdpd_rule = get_option('rtwwdpd_single_prod_rule');
					$iin = 0;
					$this->priority_array[] = $value;
					if(isset($rtwwdpd_rule) && !empty($rtwwdpd_rule))
					{
						foreach ($rtwwdpd_rule as $rul => $rule_no) 
						{
							$rtwwdpd_set_data['rules'][0]['from'] = $rtwwdpd_rule[0]['rtwwdpd_min'];
							$rtwwdpd_set_data['rules'][0]['to'] = '';
							$rtwwdpd_set_data['rules'][0]['type'] = $rtwwdpd_rule[0]['rtwwdpd_discount_type'];
							$rtwwdpd_set_data['rules'][0]['amount'] = $rtwwdpd_rule[0]['rtwwdpd_discount_value'];
							
							$i++;
							$rtwwdpd_obj = new RTWWDPD_Adjustment_Set_Totals($rtwwdpd_set_data , $rtwwdpd_rule, $rtwwdpd_nam);
							$this->adjustment_sets[$id] = $rtwwdpd_obj;
							$id++;
							$iin++;
						}
					}
				}
				elseif($value == 'pro_com_rule_row')
				{
					$rtwwdpd_nam[] = $value;
					$this->priority_array[] = $value;
					$iin = 0;
					$rtwwdpd_rule = get_option('rtwwdpd_combi_prod_rule');
					if(isset($rtwwdpd_rule) && !empty($rtwwdpd_rule))
					{
						foreach ($rtwwdpd_rule as $rul => $rule_no) {

							$rtwwdpd_set_data['rules'][0]['from'] = $rtwwdpd_rule[0]['combi_quant'];
							$rtwwdpd_set_data['rules'][0]['to'] = '';
							$rtwwdpd_set_data['rules'][0]['type'] = $rtwwdpd_rule[0]['rtwwdpd_combi_discount_type'];
							$rtwwdpd_set_data['rules'][0]['amount'] = $rtwwdpd_rule[0]['rtwwdpd_combi_discount_value'];
							$rtwwdpd_set_data['rules'][0]['prod_id'] = $rtwwdpd_rule[0]['product_id'];
							
							$i++;
							$rtwwdpd_obj = new RTWWDPD_Adjustment_Set_Totals($rtwwdpd_set_data , $rtwwdpd_rule, $rtwwdpd_nam);
							$this->adjustment_sets[$id] = $rtwwdpd_obj;
							$iin++;
						}
					}
				}
				elseif($value == 'cart_rule_row')
				{
					$rtwwdpd_nam[] = $value;
					$this->priority_array[] = $value;
					$rtwwdpd_rule = get_option('rtwwdpd_cart_rule');
					$iin = 0;
					if(isset($rtwwdpd_rule) && !empty($rtwwdpd_rule))
					{
						foreach ($rtwwdpd_rule as $rul => $rule_no) {

							$rtwwdpd_set_data['rules'][0]['from'] = $rtwwdpd_rule[0]['rtwwdpd_min'];
							$rtwwdpd_set_data['rules'][0]['to'] =  $rtwwdpd_rule[0]['rtwwdpd_max'];
							$rtwwdpd_set_data['rules'][0]['type'] = $rtwwdpd_rule[0]['rtwwdpd_discount_type'];
							$rtwwdpd_set_data['rules'][$i]['amount'] = $rtwwdpd_rule[$iin]['rtwwdpd_discount_value'];
							
							$rtwwdpd_obj = new RTWWDPD_Adjustment_Set_Totals($rtwwdpd_set_data , $rtwwdpd_rule, $rtwwdpd_nam);
							$this->adjustment_sets[$id] = $rtwwdpd_obj;
						}
					}
				}
				elseif($value == 'cat_rule_row')
				{
					$rtwwdpd_nam[] = $value;
					$this->priority_array[] = $value;
					$iin = 0;
					$rtwwdpd_rule = get_option('rtwwdpd_single_cat_rule');
					if(isset($rtwwdpd_rule) && !empty($rtwwdpd_rule))
					{
						foreach ($rtwwdpd_rule as $rul => $rule_no) {

							$rtwwdpd_set_data['rules'][$i]['from'] = $rtwwdpd_rule[$iin]['rtwwdpd_min_cat'];
							$rtwwdpd_set_data['rules'][$i]['to'] =  $rtwwdpd_rule[$iin]['rtwwdpd_max_cat'];
							$rtwwdpd_set_data['rules'][$i]['type'] = $rtwwdpd_rule[$iin]['rtwwdpd_dscnt_cat_type'];
							$rtwwdpd_set_data['rules'][$i]['amount'] = $rtwwdpd_rule[$iin]['rtwwdpd_dscnt_cat_val'];
							$rtwwdpd_set_data['rules'][$i]['cat_id'] = isset($rtwwdpd_rule[$iin]['category_id']) ? $rtwwdpd_rule[$iin]['category_id'] : '';

							$rtwwdpd_obj = new RTWWDPD_Adjustment_Set_Totals($rtwwdpd_set_data , $rtwwdpd_rule, $rtwwdpd_nam);
							$this->adjustment_sets[$id] = $rtwwdpd_obj;
						}
					}
				}
				elseif($value == 'cat_com_rule_row')
				{
					$rtwwdpd_nam[] = $value;
					$this->priority_array[] = $value;
					$rtwwdpd_rule = get_option('rtwwdpd_combi_cat_rule');
					$iin = 0;
					$ids = 0;
					if(isset($rtwwdpd_rule) && !empty($rtwwdpd_rule))
					{
						foreach ($rtwwdpd_rule as $rul => $rule_no) {

							$rtwwdpd_set_data['rules'][$i]['from'] = $rtwwdpd_rule[$iin]['combi_quant'][$iin];
							$rtwwdpd_set_data['rules'][$i]['to'] =  '';
							$rtwwdpd_set_data['rules'][$i]['type'] = $rtwwdpd_rule[$iin]['rtwwdpd_discount_type'];
							$rtwwdpd_set_data['rules'][$i]['amount'] = $rtwwdpd_rule[$iin]['rtwwdpd_discount_value'];
							foreach ($rule_no['category_id'] as $ke) {
								$rtwwdpd_set_data['rules'][$i]['cat_id'][] = $ke;
								$ids++;
							}
							
							$rtwwdpd_obj = new RTWWDPD_Adjustment_Set_Totals($rtwwdpd_set_data , $rtwwdpd_rule, $rtwwdpd_nam);
							$this->adjustment_sets[$id] = $rtwwdpd_obj;
						}
					}
				}
				elseif($value == 'attr_rule_row')
				{	
					$rtwwdpd_nam[] = $value;
					$this->priority_array[] = $value;
					$rtwwdpd_rule = get_option('rtwwdpd_att_rule');
					$iin = 0;
					if(isset($rtwwdpd_rule) && !empty($rtwwdpd_rule))
					{
						foreach ($rtwwdpd_rule as $rul => $rule_no) {

							$rtwwdpd_set_data['rules'][$i]['from'] = 1;
							$rtwwdpd_set_data['rules'][$i]['to'] = '';
							$rtwwdpd_set_data['rules'][$i]['type'] = $rtwwdpd_rule[$iin]['rtwwdpd_att_discount_type'];
							$rtwwdpd_set_data['rules'][$i]['amount'] = $rtwwdpd_rule[$iin]['rtwwdpd_att_discount_value'];

							$rtwwdpd_obj = new RTWWDPD_Adjustment_Set_Totals($rtwwdpd_set_data , $rtwwdpd_rule, $rtwwdpd_nam);
							$this->adjustment_sets[$id] = $rtwwdpd_obj;
						}
					}
				}
				elseif($value == 'prod_tag_rule_row')
				{	
					$rtwwdpd_nam[] = $value;
					$this->priority_array[] = $value;
					$iin = 0;
					$rtwwdpd_rule = get_option('rtwwdpd_tag_method');
					
					if(isset($rtwwdpd_rule) && !empty($rtwwdpd_rule))
					{
						foreach ($rtwwdpd_rule as $rul => $rule_no) 
						{
							$rtwwdpd_set_data['rules'][$i]['from'] = 1;
							$rtwwdpd_set_data['rules'][$i]['to'] = '';
							$rtwwdpd_set_data['rules'][$i]['type'] = $rtwwdpd_rule[$iin]['rtwwdpd_tag_discount_type'];
							$rtwwdpd_set_data['rules'][$i]['amount'] = $rtwwdpd_rule[$iin]['rtwwdpd_tag_discount_value'];
							
							$rtwwdpd_obj = new RTWWDPD_Adjustment_Set_Totals($rtwwdpd_set_data , $rtwwdpd_rule, $rtwwdpd_nam);
							$this->adjustment_sets[$id] = $rtwwdpd_obj;
						}
					}
				}
				elseif($value == 'tier_rule_row')
				{	
					$rtwwdpd_nam[] = $value;
					$this->priority_array[] = $value;
					$iin = 0;
					$rtwwdpd_rule = get_option('rtwwdpd_tiered_rule');

					if(isset($rtwwdpd_rule) && !empty($rtwwdpd_rule))
					{
						$rtwwdpd_set_data = array();
						foreach ($rtwwdpd_rule as $rul => $rule_no) 
						{
							$rtwwdpd_set_data['rules'][$i]['from'] = $rtwwdpd_rule[$iin]['quant_min'];
							$rtwwdpd_set_data['rules'][$i]['to'] = $rtwwdpd_rule[$iin]['quant_max'];
							$rtwwdpd_set_data['rules'][$i]['type'] = $rtwwdpd_rule[$iin]['rtwwdpd_discount_type'];
							$rtwwdpd_set_data['rules'][$i]['discount_val'] = $rtwwdpd_rule[$iin]['discount_val'];
							
							$rtwwdpd_set_data['rules'][$i]['prod_id'] = $rtwwdpd_rule[$iin]['products'];

							$rtwwdpd_obj = new RTWWDPD_Adjustment_Set_Totals($rtwwdpd_set_data , $rtwwdpd_rule, $rtwwdpd_nam);
							$this->adjustment_sets[$id] = $rtwwdpd_obj;
						}
					}
				}
				elseif($value == 'tier_cat_rule_row')
				{	
					$rtwwdpd_nam[] = $value;
					$this->priority_array[] = $value;
					$iin = 0;
					$rtwwdpd_rule = get_option('rtwwdpd_tiered_cat');
					
					if(isset($rtwwdpd_rule) && !empty($rtwwdpd_rule))
					{
						$rtwwdpd_set_data = array();
						foreach ($rtwwdpd_rule as $rul => $rule_no) 
						{
							$rtwwdpd_set_data['rules'][$i]['from'] = $rtwwdpd_rule[$iin]['quant_min'];
							$rtwwdpd_set_data['rules'][$i]['to'] = $rtwwdpd_rule[$iin]['quant_max'];
							$rtwwdpd_set_data['rules'][$i]['type'] = $rtwwdpd_rule[$iin]['rtwwdpd_discount_type'];
							$rtwwdpd_set_data['rules'][$i]['discount_val'] = $rtwwdpd_rule[$iin]['discount_val'];
							
							// $rtwwdpd_set_data['rules'][$i]['cat_id'] = $rtwwdpd_rule[$iin]['category_id'];

							$rtwwdpd_obj = new RTWWDPD_Adjustment_Set_Totals($rtwwdpd_set_data , $rtwwdpd_rule, $rtwwdpd_nam);
							$this->adjustment_sets[$id] = $rtwwdpd_obj;
						}
					}
				}
				elseif($value == 'bogo_rule_row')
				{	
					$rtwwdpd_nam[] = $value;
					
					$this->priority_array[] = $value;
					$iin = 0;
					$rtwwdpd_rule = get_option('rtwwdpd_bogo_rule'); 
					
					if(isset($rtwwdpd_rule) && !empty($rtwwdpd_rule))
					{
						foreach ($rtwwdpd_rule as $rul => $rule_no) 
						{
							$rtwwdpd_set_data['rules'][$i]['from'] = '';
							$rtwwdpd_set_data['rules'][$i]['to'] = '';
							$rtwwdpd_set_data['rules'][$i]['type'] = '';
							$rtwwdpd_set_data['rules'][$i]['amount'] = '';
							// $rtwwdpd_set_data['rules'][$i]['prod_id'] = $rtwwdpd_rule[$iin]['product_id'];
							$rtwwdpd_set_data['rules'][$i]['free_prod_id'] = $rtwwdpd_rule[$iin]['rtwbogo'];
							
							$rtwwdpd_obj = new RTWWDPD_Adjustment_Set_Totals($rtwwdpd_set_data , $rtwwdpd_rule, $rtwwdpd_nam);
							$this->adjustment_sets[$id] = $rtwwdpd_obj;
							
						}
					}
				}
				elseif($value == 'bogo_cat_rule_row')
				{	
					$rtwwdpd_nam[] = $value;
					$this->priority_array[] = $value;
					$iin = 0;
					$rtwwdpd_rule = get_option('rtwwdpd_bogo_cat_rule');
					
					if(isset($rtwwdpd_rule) && !empty($rtwwdpd_rule))
					{
						foreach ($rtwwdpd_rule as $rul => $rule_no) 
						{
							$rtwwdpd_set_data['rules'][$i]['from'] = '';
							$rtwwdpd_set_data['rules'][$i]['to'] = '';
							$rtwwdpd_set_data['rules'][$i]['type'] = '';
							$rtwwdpd_set_data['rules'][$i]['amount'] = '';
							// $rtwwdpd_set_data['rules'][$i]['cat_id'] = $rtwwdpd_rule[$iin]['category_id'];
							if(isset($rtwwdpd_rule[$iin]['rtwbogo']))
							{
								$rtwwdpd_set_data['rules'][$i]['free_prod_id'] = $rtwwdpd_rule[$iin]['rtwbogo'];
							}
							
							$rtwwdpd_obj = new RTWWDPD_Adjustment_Set_Totals($rtwwdpd_set_data , $rtwwdpd_rule, $rtwwdpd_nam);
							$this->adjustment_sets[$id] = $rtwwdpd_obj;
						}
					}
				}
				elseif($value == 'pay_rule_row')
				{
					$rtwwdpd_nam[] = $value;
					$this->priority_array[] = $value;
					$iin = 0;
					$rtwwdpd_rule = get_option('rtwwdpd_pay_method');

					$i=0;
					if(isset($rtwwdpd_rule) && !empty($rtwwdpd_rule))
					{
						foreach ($rtwwdpd_rule as $rul => $rule_no) {

							$rtwwdpd_set_data['rules'][$i]['type'] = $rtwwdpd_rule[$iin]['rtwwdpd_pay_discount_type'];
							$rtwwdpd_set_data['rules'][$i]['amount'] = $rtwwdpd_rule[$iin]['rtwwdpd_pay_discount_value'];
							
							$rtwwdpd_obj = new RTWWDPD_Adjustment_Set_Totals($rtwwdpd_set_data , $rtwwdpd_rule, $rtwwdpd_nam);
							$this->adjustment_sets[$id] = $rtwwdpd_obj;
						}
					}
				}
				elseif($value == 'ship_rule_row')
				{
					$rtwwdpd_nam[] = $value;
					$this->priority_array[] = $value;
					$iin = 0;
					$rtwwdpd_rule = get_option('rtwwdpd_ship_method');
					
					if(isset($rtwwdpd_rule) && !empty($rtwwdpd_rule))
					{
						foreach ($rtwwdpd_rule as $rul => $rule_no) {

							$rtwwdpd_set_data['rules'][$i]['type'] = $rtwwdpd_rule[$iin]['rtwwdpd_ship_discount_type'];
							$rtwwdpd_set_data['rules'][$i]['amount'] = $rtwwdpd_rule[$iin]['rtwwdpd_ship_discount_value'];
							
							$rtwwdpd_obj = new RTWWDPD_Adjustment_Set_Totals($rtwwdpd_set_data , $rtwwdpd_rule, $rtwwdpd_nam);
							$this->adjustment_sets[$id] = $rtwwdpd_obj;
						}
					}
				}
				elseif($value == 'var_rule_row')
				{
					$rtwwdpd_rule = get_option('rtwwdpd_variation_rule');
					$rtwwdpd_nam[] = $value;
					$this->priority_array[] = $value;
					$iin = 0;
					if(isset($rtwwdpd_rule) && !empty($rtwwdpd_rule))
					{
						foreach ($rtwwdpd_rule as $rul => $rule_no) {
							$rtwwdpd_set_data['rules'][$i]['type'] = $rtwwdpd_rule[$iin]['rtwwdpd_discount_type'];
							$rtwwdpd_set_data['rules'][$i]['amount'] = $rtwwdpd_rule[$iin]['rtwwdpd_discount_value'];
							
							$rtwwdpd_obj = new RTWWDPD_Adjustment_Set_Totals($rtwwdpd_set_data , $rtwwdpd_rule, $rtwwdpd_nam);
							$this->adjustment_sets[$id] = $rtwwdpd_obj;
						}
					}
				}
			}
		}
	}

	/**
	 * Function to perform discounting rules on cart items.
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
		global $product;
		if( !is_array( $rtwwdpd_temp_cart ) || empty( $rtwwdpd_temp_cart ) )
		{
			return;
		}
		$rtwwdpd_setting_pri = get_option('rtwwdpd_setting_priority');
		
		$rtwwdpd_cart_total = $woocommerce->cart->get_subtotal();
		$rtwwdpd_cart_prod_count = $woocommerce->cart->cart_contents;
		$rtwwdpd_prod_count = 0;
		if( is_array($rtwwdpd_cart_prod_count) && !empty($rtwwdpd_cart_prod_count) )
		{
			foreach ($rtwwdpd_cart_prod_count as $key => $value) {
				$rtwwdpd_prod_count += $value['quantity'];
			}
		}

		if( is_array($rtwwdpd_setting_pri) && !empty($rtwwdpd_setting_pri) ) 
		{
			$rtwwdpd_num_decimals = apply_filters( 'rtwwdpd_get_decimals', (int) get_option( 'woocommerce_price_num_decimals' ) );
			$rtwwdpd_pricing_rules = 0;
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

			if ( $this->adjustment_sets && count( $this->adjustment_sets ) ) {

				foreach ( $this->adjustment_sets as $set_id => $set ) 
				{
					
					$rtwwdpd_matched        = false;
					$rtwwdpd_pricing_rules  = $set->rtwwdpd_pricing_rules;
					
					if ( is_array( $rtwwdpd_pricing_rules ) && sizeof( $rtwwdpd_pricing_rules ) > 0 ) {
						$i=0;
						foreach ( $rtwwdpd_pricing_rules as $rule ) {

							if( $rtwwdpd_setting_pri['rtw_offer_select'] == 'rtw_first_match' )
							{
								if( is_array( $set->rtwwdpd_rule_name ) && !empty( $set->rtwwdpd_rule_name ) )
								{
								foreach ( $this->priority_array as $kval ) 
								{
									if( $kval == 'pro_rule_row' )
									{	
										$rtwwdpd_pro_rul = get_option('rtwwdpd_single_prod_rule');
										if(!is_array($rtwwdpd_pro_rul) || empty($rtwwdpd_pro_rul))
										{
											continue 1;
										}
										$active_dayss = get_site_option('rtwwdpd_discount_on_selected_days', 'no');
										foreach ($rtwwdpd_pro_rul as $pro => $rul) {

											if($active_dayss == 'yes')
											{
												$active_days = isset($rul['rtwwwdpd_prod_day']) ? $rul['rtwwwdpd_prod_day'] : array();
												$current_day = date('N');

												if(!in_array($current_day, $active_days))
												{
													continue;
												}
											}

											$discount_lggya = false;
											$rtwwdpd_matched = true;
											if($rul['rtwwdpd_single_from_date'] > $rtwwdpd_today_date || $rul['rtwwdpd_single_to_date'] < $rtwwdpd_today_date)
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
											
											$rtwwdpd_restricted_mails = isset( $rul['rtwwdpd_select_emails'] ) ? $rul['rtwwdpd_select_emails'] : array();

											$rtwwdpd_cur_user_mail = get_current_user_id();
											
											if(in_array($rtwwdpd_cur_user_mail, $rtwwdpd_restricted_mails))
											{
												continue 1;
											}

											if(isset($rul['rtwwdpd_min_orders']) && $rul['rtwwdpd_min_orders'] > $rtwwdpd_no_oforders)
											{
												continue 1;
											}
											if(isset($rul['rtwwdpd_min_spend']) && $rul['rtwwdpd_min_spend'] > $rtwwdpd_cart_total)
											{
												continue 1;
											}
											////////////////////////////////
											$all_ids = array();
											$total_quantities = array();
											$total_prices = array();
											$total_weightss = array();
											if($rul['rtwwdpd_rule_on'] == 'rtwwdpd_multiple_products')
											{
												if($rul['rtwwdpd_condition'] == 'rtwwdpd_and')
												{
													foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item )
													{
														if(!in_array($cart_item['data']->get_id(), $all_ids))
														{
															$all_ids[] = $cart_item['data']->get_id();
														}
														
														if(!empty($cart_item['data']->get_parent_id()) && !in_array($cart_item['data']->get_parent_id(), $all_ids))
														{
															$all_ids[] = $cart_item['data']->get_parent_id();
														}

														if(in_array($cart_item['data']->get_id(), $rul['multiple_product_ids']))
														{
															if(!array_key_exists($cart_item['data']->get_id(), $total_quantities))
															{
																$total_quantities[$cart_item['data']->get_id()] = $cart_item['quantity'];
																
																$total_prices[$cart_item['data']->get_id()] = ( $cart_item['quantity'] * $cart_item['data']->get_price());
																$total_weightss[$cart_item['data']->get_id()] = ( $cart_item['quantity'] * $cart_item['data']->get_weight());
															}
															else{
																$total_quantities[$cart_item['data']->get_id()] = $total_quantities[$cart_item['data']->get_id()] + $cart_item['quantity'];
																
																$total_prices[$cart_item['data']->get_id()] = $total_prices[$cart_item['data']->get_id()] + ( $cart_item['quantity'] * $cart_item['data']->get_price());
																$total_weightss[$cart_item['data']->get_id()] = $total_weightss[$cart_item['data']->get_id()]+ ( $cart_item['quantity'] * $cart_item['data']->get_weight());
															}
														}

														if(in_array($cart_item['data']->get_parent_id(), $rul['multiple_product_ids']))
														{
															if(array_key_exists($cart_item['data']->get_parent_id(), $total_quantities))
															{
																$total_quantities[$cart_item['data']->get_parent_id()] = $total_quantities[$cart_item['data']->get_parent_id()] + $cart_item['quantity'];

																$total_prices[$cart_item['data']->get_parent_id()] = $total_prices[$cart_item['data']->get_parent_id()] + ( $cart_item['quantity'] * $cart_item['data']->get_price());

																$total_weightss[$cart_item['data']->get_parent_id()] = $total_weightss[$cart_item['data']->get_parent_id()] + ( $cart_item['quantity'] * $cart_item['data']->get_weight());
																
															}else{
																$total_quantities[$cart_item['data']->get_parent_id()] = $cart_item['quantity'];

																$total_prices[$cart_item['data']->get_parent_id()] = ($cart_item['quantity']* $cart_item['data']->get_price() );
																
																$total_weightss[$cart_item['data']->get_parent_id()] = ($cart_item['quantity'] * $cart_item['data']->get_weight());

															}
														}
													}
													
													$reslt = array_diff($rul['multiple_product_ids'], $all_ids);

													if(!empty($reslt))
													{
														continue;
													}
												}
											}

											////////////////////////////////

											foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item ) {
												$product = $cart_item['data'];

												if ( ! $this->rtwwdpd_is_cumulative( $cart_item, $cart_item_key ) ) {

													if ( $this->rtwwdpd_is_item_discounted( $cart_item, $cart_item_key ) && apply_filters( 'rtwwdpd_stack_order_totals', false ) === false ) {
														continue 1;
													}
												}

												$rtwwdpd_discounted = isset( WC()->cart->cart_contents[ $cart_item_key ]['discounts'] );

												if ($rtwwdpd_discounted){
													$rtwwdpd_d = WC()->cart->cart_contents[ $cart_item_key ]['discounts'];
													if (in_array('advanced_totals', $rtwwdpd_d['by'])) {
														continue 1;
													}
												}

												$rtwwdpd_original_price = $this->rtw_get_price_to_discount( $cart_item, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ) );

												if ( $rtwwdpd_original_price ) {
													$rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $rul['rtwwdpd_discount_value'], $rule, $cart_item, $this );

													if($rul['rtwwdpd_rule_on'] == 'rtwwdpd_products' || $rul['rtwwdpd_rule_on'] == 'rtwwdpd_cart')
													{
														if($rul['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
														{
															if($cart_item['quantity'] < $rul['rtwwdpd_min'])
															{
																continue 1;
															}
															if( isset($rul['rtwwdpd_max']) && $rul['rtwwdpd_max'] != '' && $cart_item['quantity'] > $rul['rtwwdpd_max'] )
															{
																continue 1;
															}
														}
														elseif($rul['rtwwdpd_check_for'] == 'rtwwdpd_price')
														{
															if($cart_item['data']->get_price() < $rul['rtwwdpd_min'])
															{
																continue 1;
															}
															$total_cost = ( $cart_item['data']->get_price() * $cart_item['quantity'] );
															if( isset($rul['rtwwdpd_max']) && $rul['rtwwdpd_max'] != '' && $total_cost > $rul['rtwwdpd_max'] )
															{
																continue 1;
															}
														}
														elseif($rul['rtwwdpd_check_for'] == 'rtwwdpd_weight')
														{
															if($cart_item['data']->get_weight() < $rul['rtwwdpd_min'] )
															{
																continue 1;
															}
															if( isset($rul['rtwwdpd_max']) && $rul['rtwwdpd_max'] != '' && $cart_item['data']->get_weight() > $rul['rtwwdpd_max'] )
															{
																continue 1;
															}
														}
													}
													elseif($rul['rtwwdpd_rule_on'] == 'rtwwdpd_multiple_products')
													{
														if( $rul['rtwwdpd_condition'] == 'rtwwdpd_and' )
														{
														
															// $total_quantities = array();
															// $total_prices = array();
															// $total_weightss = array();
															if($rul['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
															{
																$total_quant = 0;
																if(is_array($total_quantities) && !empty($total_quantities))
																{
																	foreach ($total_quantities as $q => $qnt) {
																		$total_quant += $qnt;
																	}
																}
																if(isset($total_quant) && $total_quant < $rul['rtwwdpd_min'] )
																{
																	continue 1;
																}
															}
															elseif($rul['rtwwdpd_check_for'] == 'rtwwdpd_price')
															{
																$total_prz = 0;
																if(is_array($total_prices) && !empty($total_prices))
																{
																	foreach ($total_prices as $q => $pri) {
																		$total_prz += $pri;
																	}
																}

																if($total_prz < $rul['rtwwdpd_min'] )
																{
																	continue 1;
																}

																if( isset($rul['rtwwdpd_max']) && !empty($rul['rtwwdpd_max']) && $total_prz > $rul['rtwwdpd_max'] )
																{
																	continue 1;
																}
															}
															elseif($rul['rtwwdpd_check_for'] == 'rtwwdpd_weight')
															{
																$total_weigh = 0;
																if(is_array($total_weightss) && !empty($total_weightss))
																{
																	foreach ($total_weightss as $q => $we) {
																		$total_weigh += $we;
																	}
																}

																if( $total_weigh < $rul['rtwwdpd_min'] )
																{
																	continue 1;
																}
																if( isset($rul['rtwwdpd_max']) && $rul['rtwwdpd_max'] != '' && $total_weigh > $rul['rtwwdpd_max'] )
																{
																	continue 1;
																}
															}
														}else{
															if($rul['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
															{
																if( $cart_item['quantity'] < $rul['rtwwdpd_min'] )
																{
																	continue 1;
																}
																if( isset($rul['rtwwdpd_max']) && $rul['rtwwdpd_max'] != '' && $cart_item['quantity'] > $rul['rtwwdpd_max'] )
																{
																	continue 1;
																}
															}
															elseif($rul['rtwwdpd_check_for'] == 'rtwwdpd_price')
															{
																if($cart_item['data']->get_price() < $rul['rtwwdpd_min'] )
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
																if( $cart_item['data']->get_weight() < $rul['rtwwdpd_min'] )
																{
																	continue 1;
																}
																if( isset($rul['rtwwdpd_max']) && $rul['rtwwdpd_max'] != '' && $cart_item['data']->get_weight() > $rul['rtwwdpd_max'] )
																{
																	continue 1;
																}
															}
														}
													}
													
													$rtwwdpd_parent_id = $cart_item['data']->get_parent_id();

													if($rul['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
													{
														$rtwwdpd_amount = $rtwwdpd_amount / 100;
														$rtwwdpd_discnted_val = ( floatval( $rtwwdpd_amount ) * $rtwwdpd_original_price );

														if(isset($rul['rtwwdpd_max_discount']) && !empty($rul['rtwwdpd_max_discount']))
														{
															if($rtwwdpd_discnted_val > $rul['rtwwdpd_max_discount'])
															{
																$rtwwdpd_discnted_val = $rul['rtwwdpd_max_discount'];
															}
														}
														$rtwwdpd_price_adjusted = ( floatval( $rtwwdpd_original_price ) - $rtwwdpd_discnted_val );

														if($rul['rtwwdpd_rule_on'] == 'rtwwdpd_products' && isset($rul['product_id']))
														{	
															if( $rul['product_id'] == $cart_item['data']->get_id() || $rtwwdpd_parent_id == $rul['product_id'] )
															{
																if(isset($rul['rtwwdpd_exclude_sale']))
																{
																	if( !$cart_item['data']->is_on_sale() )
																	{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																		return;
																	}
																}
																else{
																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																	return;
																}
															}
														}
														elseif($rul['rtwwdpd_rule_on'] == 'rtwwdpd_cart')
														{
															if(isset($rul['rtwwdpd_exclude_sale']))
															{
																if( !$cart_item['data']->is_on_sale() )
																{
																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																	return;
																	
																}
															}
															else{
																Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																return;
																
															}
														}
														elseif($rul['rtwwdpd_rule_on'] == 'rtwwdpd_multiple_products')
														{
															if( in_array($cart_item['data']->get_id(), $rul['multiple_product_ids']) || in_array($cart_item['data']->get_parent_id(), $rul['multiple_product_ids']) )
															{
																if(isset($rul['rtwwdpd_exclude_sale']))
																{
																	if( !$cart_item['data']->is_on_sale() )
																	{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																		$discount_lggya = true;
																		// return;
																	}
																}
																else{
																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																	$discount_lggya = true;
																	// return;
																}
															}
														}
													}
													elseif( $rul['rtwwdpd_discount_type'] == 'rtwwdpd_fixed_price' )
													{
														if(isset($rul['rtwwdpd_max_discount']) && !empty($rul['rtwwdpd_max_discount']))
														{
															if( $rtwwdpd_amount > $rul['rtwwdpd_max_discount'] )
															{
																$rtwwdpd_amount = $rul['rtwwdpd_max_discount'];
															}
														}
														$totl_qunt = 0;
														if(is_array($total_quantities) && !empty($total_quantities))
														{
															foreach ($total_quantities as $qnt => $qt) {
																// $totl_qunt += $qt;
															}
														}
														
														$rtwwdpd_amount = ( $rtwwdpd_amount / $cart_item['quantity'] );
														$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount);
														if($rul['rtwwdpd_rule_on'] == 'rtwwdpd_products' && isset($rul['product_id']))
														{			
															if( $rul['product_id'] == $cart_item['data']->get_id()  || $rtwwdpd_parent_id == $rul['product_id'] )
															{
																if(isset($rul['rtwwdpd_exclude_sale']))
																{
																	if( !$cart_item['data']->is_on_sale() )
																	{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																		return;
																		
																	}
																}
																else{
																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																	return;
																	
																}
															}
														}
														elseif($rul['rtwwdpd_rule_on'] == 'rtwwdpd_cart')
														{
															if(isset($rul['rtwwdpd_exclude_sale']))
															{
																if( !$cart_item['data']->is_on_sale() )
																{
																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																	return;
																	
																}
															}
															else{
																Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																return;
																
															}
														}
														elseif($rul['rtwwdpd_rule_on'] == 'rtwwdpd_multiple_products')
														{
															if( in_array($cart_item['data']->get_id(), $rul['multiple_product_ids']) || in_array($cart_item['data']->get_parent_id(), $rul['multiple_product_ids']) )
															{
																if(isset($rul['rtwwdpd_exclude_sale']))
																{
																	if( !$cart_item['data']->is_on_sale() )
																	{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																		$discount_lggya = true;
																		// return;
																	}
																}
																else{
																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																	$discount_lggya = true;
																	// return;
																}
															}
														}
													}
													else
													{
														if(isset($rul['rtwwdpd_max_discount']) && !empty($rul['rtwwdpd_max_discount']))
														{
															if($rtwwdpd_amount > $rul['rtwwdpd_max_discount'])
															{
																$rtwwdpd_amount = $rul['rtwwdpd_max_discount'];
															}
														}
														$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount);
														if($rul['rtwwdpd_rule_on'] == 'rtwwdpd_products' && isset($rul['product_id']))
														{		
															if( $rul['product_id'] == $cart_item['data']->get_id() || $rtwwdpd_parent_id == $rul['product_id'] )
															{
																if(isset($rul['rtwwdpd_exclude_sale']))
																{
																	if( !$cart_item['data']->is_on_sale() )
																	{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																		return;
																		
																	}
																}
																else{
																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																	return;
																	
																}
															}
														}
														elseif($rul['rtwwdpd_rule_on'] == 'rtwwdpd_cart')
														{
															if(isset($rul['rtwwdpd_exclude_sale']))
															{
																if( !$cart_item['data']->is_on_sale() )
																{
																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																	return;
																	
																}
															}
															else{
																Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																return;
															}
														}
														elseif($rul['rtwwdpd_rule_on'] == 'rtwwdpd_multiple_products')
														{
															if( in_array($cart_item['data']->get_id(), $rul['multiple_product_ids']) || in_array($cart_item['data']->get_parent_id(), $rul['multiple_product_ids']) )
															{
																if(isset($rul['rtwwdpd_exclude_sale']))
																{
																	if( !$cart_item['data']->is_on_sale() )
																	{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																		$discount_lggya = true;
																		// return;
																	}
																}
																else{
																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																	$discount_lggya = true;
																	// return;
																}
															}
														}
													}
												}
											}
											if($discount_lggya)
											{
												return;
											}
										}
										continue 1;
									}
									elseif($kval == 'pro_com_rule_row')
									{
										$rtwwdpd_pro_com = get_option('rtwwdpd_combi_prod_rule');
										
										if(!is_array($rtwwdpd_pro_com) || empty($rtwwdpd_pro_com))
										{
											continue 1;
										}
										foreach ($rtwwdpd_pro_com as $pro => $com) {

											$rtwwdpd_matched = true;
											if($com['rtwwdpd_combi_from_date'] > $rtwwdpd_today_date || $com['rtwwdpd_combi_to_date'] < $rtwwdpd_today_date)
											{
												continue;
											} 

											$rtwwdpd_user_role = $com['rtwwdpd_select_roles_com'] ;

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

											$rtwwdpd_restricted_mails = isset( $com['rtwwdpd_select_com_emails'] ) ? $com['rtwwdpd_select_com_emails'] : array();

											$rtwwdpd_cur_user_mail = get_current_user_id();
											
											if(in_array($rtwwdpd_cur_user_mail, $rtwwdpd_restricted_mails))
											{
												continue 1;
											}

											if(isset($com['rtwwdpd_combi_min_orders']) && $com['rtwwdpd_combi_min_orders'] > $rtwwdpd_no_oforders)
											{
												continue;
											}
											if(isset($com['rtwwdpd_combi_min_spend']) && $com['rtwwdpd_combi_min_spend'] > $rtwwdpd_ordrtotal)
											{
												continue;
											}

											$both_quantity = 0;
											$both_ids 	=	array();

											if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
												foreach ( WC()->cart->get_cart() as $cart_item_k => $valid ) {
													foreach($com['product_id'] as $na => $kid )
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
											foreach ($com['combi_quant'] as $quants) {
												$givn_quanty += $quants;
											}

											$rslt = array();
											$rslt = array_diff($com['product_id'], $both_ids );
											
											if( !empty($rslt) )
											{
												continue 1;
											}
											if( $givn_quanty > $both_quantity )
											{
												continue 1;
											}

											foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item ) {
												$product = $cart_item['data'];

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

												$rtwwdpd_original_price = $this->rtw_get_price_to_discount( $cart_item, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ) );

												if ( $rtwwdpd_original_price ) {
													$rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $com['rtwwdpd_combi_discount_value'], $rule, $cart_item, $this );

													if($com['rtwwdpd_combi_discount_type'] == 'rtwwdpd_discount_percentage')
													{
														$rtwwdpd_amount = $rtwwdpd_amount / 100;
														$rtwwdpd_discnted_val = ($rtwwdpd_amount  * $rtwwdpd_original_price );
														if(isset($com['rtwwdpd_combi_max_discount']) && !empty($com['rtwwdpd_combi_max_discount']))
														{
															if($rtwwdpd_discnted_val > $com['rtwwdpd_combi_max_discount'])
															{
																$rtwwdpd_discnted_val = $com['rtwwdpd_combi_max_discount'];
															}
														}
														$rtwwdpd_price_adjusted =  ($rtwwdpd_original_price  - $rtwwdpd_discnted_val);
														if( is_array($com['product_id']) && !empty($com['product_id']) )
														{
															foreach ($com['product_id'] as $k => $v) {
																if($v == $cart_item['data']->get_id())
																{
																	if(isset($com['rtwwdpd_combi_exclude_sale']))
																	{
																		if( !$cart_item['data']->is_on_sale() )
																		{
																			Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																			return;
																		}
																	}
																	else{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																		return;
																	}
																}
															}
														}
													}
													elseif($com['rtwwdpd_combi_discount_type'] == 'rtwwdpd_flat_discount_amount')
													{
														if(isset($com['rtwwdpd_combi_max_discount']) && !empty($com['rtwwdpd_combi_max_discount']))
														{
															if($rtwwdpd_amount > $com['rtwwdpd_combi_max_discount'])
															{
																$rtwwdpd_amount = $com['rtwwdpd_combi_max_discount'];
															}
														}
														$rtwwdpd_amount = ( $rtwwdpd_amount / $cart_item['quantity'] ); 
														$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount);

														if(is_array($com['product_id']) && !empty($com['product_id']))
														{
															foreach ($com['product_id'] as $k => $v) {
																
																if($v == $cart_item['data']->get_id())
																{
																	if(isset($com['rtwwdpd_combi_exclude_sale']))
																	{
																		if( !$cart_item['data']->is_on_sale() )
																		{
																			Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																			return;
																		}
																	}
																	else{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																		return;
																	}
																}
															}
														}
													}
												}
											}
										}
										continue 1;
									}
									elseif($kval == 'cat_rule_row')
									{	
										$check = 0;
										$rtwwdpd_pro_rul = get_option('rtwwdpd_single_cat_rule');
										if(!is_array($rtwwdpd_pro_rul) || empty($rtwwdpd_pro_rul))
										{
											continue 1;
										}

										$active_dayss = get_site_option('rtwwdpd_discount_on_selected_days', 'no');

										foreach ($rtwwdpd_pro_rul as $pro => $rul) {
											
											if($active_dayss == 'yes')
											{
												$active_days = isset($rul['rtwwwdpd_cat_day']) ? $rul['rtwwwdpd_cat_day'] : array();
												$current_day = date('N');

												if(!in_array($current_day, $active_days))
												{
													continue;
												}
											}

											$rtwwdpd_matched = true;
											if(!isset($rul['rtwwdpd_from_date']))
											{
												continue 1;
											}
											if(!isset($rul['rtwwdpd_to_date']))
											{
												continue 1;
											}
											if($rul['rtwwdpd_from_date'] > $rtwwdpd_today_date || $rul['rtwwdpd_to_date'] < $rtwwdpd_today_date)
											{
												continue 1;
											}

											$rtwwdpd_total_weight = 0;
											$rtwwdpd_total_price = 0;
											$rtwwdpd_total_quantity = 0;

											foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item ) {

												if( isset($cart_item['variation_id']) && !empty($cart_item['variation_id']) )
												{
													$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_parent_id(), 'product_cat', array( 'fields' => 'ids' ) );
												}else{
													$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) );
												}

												if( is_array($rtwwdpd_catids) && !empty($rtwwdpd_catids) && in_array($rul['category_id'], $rtwwdpd_catids))
												{
													$weight = $cart_item['data']->get_weight();
													if(empty($weight))
													{
														$weight = 1;
													}

													$rtwwdpd_total_weight += $cart_item['quantity'] * $weight;

													$rtwwdpd_total_price += $cart_item['quantity'] * $cart_item['data']->get_price();

													$rtwwdpd_total_quantity += $cart_item['quantity'];
												}
											}

											$rtwwdpd_user_role = $rul['rtwwdpd_select_roles'] ;
											
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

											$rtwwdpd_restricted_mails = isset( $rul['rtwwdpd_select_emails'] ) ? $rul['rtwwdpd_select_emails'] : array();

											$rtwwdpd_cur_user_mail = get_current_user_id();
											
											if(in_array($rtwwdpd_cur_user_mail, $rtwwdpd_restricted_mails))
											{
												continue 1;
											}
											
											if(isset($rul['rtwwdpd_min_orders']) && $rul['rtwwdpd_min_orders'] != 0 && $rul['rtwwdpd_min_orders'] > $rtwwdpd_no_oforders)
											{
												continue;
											}
											if(isset($rul['rtwwdpd_min_spend']) && $rul['rtwwdpd_min_spend'] != 0 && $rul['rtwwdpd_min_spend'] > $rtwwdpd_ordrtotal)
											{
												continue;
											}

											foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item ) {

												if(isset($rul['product_exe_id']) && $rul['product_exe_id'] == $cart_item['data']->get_id())
												{
													continue 1;
												}

												
												if(isset($rul['product_exe_id']) && is_array($rul['product_exe_id']))
												{
													if( in_array($cart_item['data']->get_id(), $rul['product_exe_id'] ) )
													{
														continue 1;
													}
												}

												if( isset( $rul['rtw_exe_product_tags'] ) && is_array( $rul['rtw_exe_product_tags'] ) && !empty( $rul['rtw_exe_product_tags'] ) )
												{
													$rtw_matched = array_intersect( $rul['rtw_exe_product_tags'], $cart_item['data']->get_tag_ids());

													if( !empty( $rtw_matched ) )
													{
														continue 1;
													}
												}

												$product = $cart_item['data'];
												$rtwwdpd_prod_id = $this->rtwwdpd_get_prod_cat_ids( $product );

												if($rul['rtwwdpd_check_for_cat'] == 'rtwwdpd_quantity')
												{
													if($rtwwdpd_total_quantity < $rul['rtwwdpd_min_cat'])
													{
														continue;
													}

													if(isset($rul['rtwwdpd_max_cat']) && $rul['rtwwdpd_max_cat'] != '')
													{
														if($rul['rtwwdpd_max_cat'] < $rtwwdpd_total_quantity)
														{
															continue;
														}
													}
												}
												elseif($rul['rtwwdpd_check_for_cat'] == 'rtwwdpd_price')
												{
													if($rtwwdpd_total_price < $rul['rtwwdpd_min_cat'])
													{
														continue;
													}
													if(isset($rul['rtwwdpd_max_cat']) && $rul['rtwwdpd_max_cat'] != '')
													{
														if($rul['rtwwdpd_max_cat'] < $rtwwdpd_total_price)
														{
															continue;
														}
													}
												}
												else{
													if( $rtwwdpd_total_weight < $rul['rtwwdpd_min_cat'] )
													{
														continue;
													}
													if(isset($rul['rtwwdpd_max_cat']) && $rul['rtwwdpd_max_cat'] != '')
													{
														if($rul['rtwwdpd_max_cat'] < $rtwwdpd_total_weight)
														{
															continue;
														}
													}
												}

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

												$rtwwdpd_original_price = $this->rtw_get_price_to_discount( $cart_item, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ) );

												if ( $rtwwdpd_original_price ) {
													$rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $rul['rtwwdpd_dscnt_cat_val'], $rule, $cart_item, $this );

													if($rul['rtwwdpd_dscnt_cat_type'] == 'rtwwdpd_discount_percentage')
													{
														$rtwwdpd_amount = $rtwwdpd_amount / 100;
														$rtwwdpd_discnted_val = ( floatval( $rtwwdpd_amount ) * $rtwwdpd_original_price );

														if(isset($rul['rtwwdpd_max_discount']) && !empty($rul['rtwwdpd_max_discount']))
														{
															if($rtwwdpd_discnted_val > $rul['rtwwdpd_max_discount'])
															{
																$rtwwdpd_discnted_val = $rul['rtwwdpd_max_discount'];
															}
														}
														$rtwwdpd_price_adjusted = ( floatval( $rtwwdpd_original_price ) - $rtwwdpd_discnted_val );

														if(isset($rul['category_id']))
														{	
															$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) );

															if( is_array($rtwwdpd_catids) && !empty($rtwwdpd_catids) && in_array($rul['category_id'], $rtwwdpd_catids))
															{
																if(isset($rul['rtwwdpd_exclude_sale']))
																{
																	if( !$cart_item['data']->is_on_sale() )
																	{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																		$check = 1;
																	}
																}
																else{
																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																	$check = 1;
																}
															}
														}
													}
													elseif($rul['rtwwdpd_dscnt_cat_type'] == 'rtwwdpd_fixed_price')
													{
														if(isset($rul['rtwwdpd_max_discount']) && !empty($rul['rtwwdpd_max_discount']))
														{
															if($rtwwdpd_amount > $rul['rtwwdpd_max_discount'])
															{
																$rtwwdpd_amount = $rul['rtwwdpd_max_discount'];
															}
														}
														$rtwwdpd_amount = ( $rtwwdpd_amount / $cart_item['quantity'] );
														$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount);
														if( isset($rul['category_id']))
														{
															$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) );

															if(  is_array($rtwwdpd_catids) && !empty($rtwwdpd_catids) && in_array($rul['category_id'], $rtwwdpd_catids))
															{
																if(isset($rul['rtwwdpd_exclude_sale']))
																{
																	if( !$cart_item['data']->is_on_sale() )
																	{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																		$check = 1;
																	}
																}
																else{
																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																	$check = 1;
																}
															}
														}
													}
													else
													{
														if(isset($rul['rtwwdpd_max_discount']) && !empty($rul['rtwwdpd_max_discount']))
														{
															if($rtwwdpd_amount > $rul['rtwwdpd_max_discount'])
															{
																$rtwwdpd_amount = $rul['rtwwdpd_max_discount'];
															}
														}
														$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount);
														if( isset($rul['category_id']))
														{
															$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) );

															if(  is_array($rtwwdpd_catids) && !empty($rtwwdpd_catids) && in_array($rul['category_id'], $rtwwdpd_catids))
															{
																if(isset($rul['rtwwdpd_exclude_sale']))
																{
																	if( !$cart_item['data']->is_on_sale() )
																	{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																		$check = 1;
																	}
																}
																else{
																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																	$check = 1;
																}
															}
														}
													}
												}
											}
										}
										if($check == 1)
										{
											return;
										}
									}
									elseif($kval == 'cat_com_rule_row')
									{	
										$rtwwdpd_pro_rul = get_option('rtwwdpd_combi_cat_rule');
										
										if(!is_array($rtwwdpd_pro_rul) || empty($rtwwdpd_pro_rul))
										{
											continue 1;
										}
										foreach ($rtwwdpd_pro_rul as $pro => $rul) {
											
											$rtwwdpd_total_quantity = 0;
											$rtwwdpd_total_quant_in_rul = 0;
											$rtwwdpd_cat_idss = array();
											$rtwwdpd_temp_cat_ids = array();
											if( is_array( $rul['category_id'] ) && !empty($rul['category_id']))
											{
												foreach( $rul['category_id'] as $cati => $catid )
												{
													$rtwwdpd_cat_idss[] = $catid;
													$rtwwdpd_total_quant_in_rul += $rul['combi_quant'][$cati];
												}
											}
											
											foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item )
											{
												foreach ((wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) )) as $key => $value)
												{
													$rtwwdpd_temp_cat_ids[] = $value;
												}
											}
											
											foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item )
											{
												$arr = wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) );

												if( array_intersect( $arr, $rtwwdpd_cat_idss ) )
												{
													$rtwwdpd_total_quantity += $cart_item['quantity'];				
												}
											}

											$rtw_result = array_diff($rtwwdpd_cat_idss, $rtwwdpd_temp_cat_ids);

											if(!empty($rtw_result)){
												continue;
											}

											$rtwwdpd_matched = true;
											if($rul['rtwwdpd_combi_from_date'] > $rtwwdpd_today_date || $rul['rtwwdpd_combi_to_date'] < $rtwwdpd_today_date)
											{
												continue;
											}

											$rtwwdpd_user_role = $rul['rtwwdpd_select_roles_com'] ;

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

											$rtwwdpd_restricted_mails = isset( $rul['rtwwdpd_select_com_emails'] ) ? $rul['rtwwdpd_select_com_emails'] : array();

											$rtwwdpd_cur_user_mail = get_current_user_id();
											if(in_array($rtwwdpd_cur_user_mail, $rtwwdpd_restricted_mails))
											{
												continue 1;
											}

											if(isset($rul['rtwwdpd_min_orders']) && $rul['rtwwdpd_min_orders'] > $rtwwdpd_no_oforders)
											{
												continue;
											}

											if(isset($rul['rtwwdpd_min_spend']) && $rul['rtwwdpd_min_spend'] > $rtwwdpd_ordrtotal)
											{
												continue;
											}

											foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item ) {
												if(isset($rul['product_exe_id']) && $rul['product_exe_id'] == $cart_item['data']->get_id())
												{
													continue 1;
												}

												if( isset( $rul['rtw_exe_product_tags'] ) && is_array( $rul['rtw_exe_product_tags'] ) && !empty( $rul['rtw_exe_product_tags'] ) )
												{
													$rtw_matched = array_intersect( $rul['rtw_exe_product_tags'], $cart_item['data']->get_tag_ids());

													if( !empty( $rtw_matched ) )
													{
														continue 1;
													}
												}

												$product = $cart_item['data'];

												$rtwwdpd_prod_id = $this->rtwwdpd_get_prod_cat_ids( $product );

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

												$rtwwdpd_original_price = $this->rtw_get_price_to_discount( $cart_item, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ) );

												if ( $rtwwdpd_original_price ) {
													$rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $rul['rtwwdpd_discount_value'], $rule, $cart_item, $this );
												
													if( $rtwwdpd_total_quant_in_rul <= $rtwwdpd_total_quantity )
													{
														if($rul['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
														{
															$rtwwdpd_amount = $rtwwdpd_amount / 100;
															$rtwwdpd_discnted_val = ( floatval( $rtwwdpd_amount ) * $rtwwdpd_original_price );

															if(isset($rul['rtwwdpd_max_discount']) && !empty($rul['rtwwdpd_max_discount']))
															{
																if($rtwwdpd_discnted_val > $rul['rtwwdpd_max_discount'])
																{
																	$rtwwdpd_discnted_val = $rul['rtwwdpd_max_discount'];
																}
															}
															$rtwwdpd_price_adjusted = ( floatval( $rtwwdpd_original_price ) - $rtwwdpd_discnted_val );

															if(isset($rul['category_id']) && is_array($rul['category_id']) && !empty($rul['category_id']))
															{	
																foreach ($rul['category_id'] as $cati => $catid)
																{
																	$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) );

																	if( in_array($catid, $rtwwdpd_catids))
																	{	
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																		return;
																	}
																}
															}
														}
														elseif($rul['rtwwdpd_discount_type'] == 'rtwwdpd_fixed_price')
														{
															if(isset($rul['rtwwdpd_max_discount']) && !empty($rul['rtwwdpd_max_discount']))
															{
																if($rtwwdpd_amount > $rul['rtwwdpd_max_discount'])
																{
																	$rtwwdpd_amount = $rul['rtwwdpd_max_discount'];
																}
															}
															$rtwwdpd_amount = ( $rtwwdpd_amount / $cart_item['quantity'] );

															$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount );
															if( isset( $rul['category_id'] ) && is_array( $rul['category_id'] ) && !empty( $rul['category_id'] ) )
															{
																foreach ( $rul['category_id'] as $cati => $catid )
																{
																	$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) );

																	if(in_array($catid, $rtwwdpd_catids))
																	{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																		return;
																	}
																}
															}
														}
														else
														{
															if(isset($rul['rtwwdpd_max_discount']) && !empty($rul['rtwwdpd_max_discount']))
															{
																if($rtwwdpd_amount > $rul['rtwwdpd_max_discount'])
																{
																	$rtwwdpd_amount = $rul['rtwwdpd_max_discount'];
																}
															}
															$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount );
															if( isset( $rul['category_id'] ) && is_array( $rul['category_id'] ) && !empty( $rul['category_id'] ) )
															{
																foreach ( $rul['category_id'] as $cati => $catid )
																{
																	$rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) );

																	if(in_array($catid, $rtwwdpd_catids))
																	{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																		return;
																	}
																}
															}
														}
													}
												}
											}
										}
										continue 1;
									}
									elseif($kval == 'attr_rule_row')
									{	
										$rtwwdpd_pro_rul = get_option('rtwwdpd_att_rule');
										
										if(!is_array($rtwwdpd_pro_rul) || empty($rtwwdpd_pro_rul))
										{
											continue 1;
										}
										foreach ($rtwwdpd_pro_rul as $pro => $rul) {
											
											$rtwwdpd_matched = true;
											if($rul['rtwwdpd_att_from_date'] > $rtwwdpd_today_date || $rul['rtwwdpd_att_to_date'] < $rtwwdpd_today_date)
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

											foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item ) {
												$product = $cart_item['data'];

												$rtwwdpd_attr = $cart_item['data']->get_attributes();
												$attr_ids = array();
												if(is_array($rtwwdpd_attr) && !empty($rtwwdpd_attr))
												{
													foreach ($rtwwdpd_attr as $attrr => $att) {
														if(is_object($att))
														{
															foreach ($att->get_options() as $kopt => $opt) {
																$attr_ids[] = $opt;
															}
														}
													}
												}

												$rtwwdpd_arr = array_intersect($attr_ids, $rul['rtwwdpd_attribute_val']);
											
												if(is_array($rtwwdpd_arr) && empty($rtwwdpd_arr))
												{
													continue 1;
												}

												if(isset($rul['product_exe_id']) && $rul['product_exe_id'] == $cart_item['data']->get_id())
												{
													continue 1;
												}

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

												$rtwwdpd_original_price = $this->rtw_get_price_to_discount( $cart_item, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ) );

												if ( $rtwwdpd_original_price ) {
													$rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $rul['rtwwdpd_att_discount_value'], $rule, $cart_item, $this );

													if($rul['rtwwdpd_att_discount_type'] == 'rtwwdpd_discount_percentage')
													{
														$rtwwdpd_amount = $rtwwdpd_amount / 100;
														$rtwwdpd_discnted_val = ( floatval( $rtwwdpd_amount ) * $rtwwdpd_original_price );
														if(isset($rul['rtwwdpd_att_max_discount']) && !empty($rul['rtwwdpd_att_max_discount']))
														{
															if($rtwwdpd_discnted_val > $rul['rtwwdpd_att_max_discount'])
															{
																$rtwwdpd_discnted_val = $rul['rtwwdpd_att_max_discount'];
															}
														}

														$rtwwdpd_price_adjusted = ( floatval( $rtwwdpd_original_price ) - $rtwwdpd_discnted_val );

														$ids=0;
														
														if(isset($rul['rtwwdpd_att_exclude_sale']))
														{
															if( !$cart_item['data']->is_on_sale() )
															{
																Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																return;
															}
														}
														else{
															Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
															return;
														}		
													}
													elseif($rul['rtwwdpd_att_discount_type'] == 'rtwwdpd_fixed_price')
													{
														if(isset($rul['rtwwdpd_att_max_discount']) && !empty($rul['rtwwdpd_att_max_discount']))
														{
															if($rtwwdpd_amount > $rul['rtwwdpd_att_max_discount'])
															{
																$rtwwdpd_amount = $rul['rtwwdpd_att_max_discount'];
															}
														}
														$rtwwdpd_amount = ( $rtwwdpd_amount / $cart_item['quantity'] );
														$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount );

														$ids=0;
														
														if(isset($rul['rtwwdpd_att_exclude_sale']))
														{
															if( !$cart_item['data']->is_on_sale() )
															{
																Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																return;
															}
														}
														else{
															Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
															return;
														}	
													}
													else
													{
														if(isset($rul['rtwwdpd_att_max_discount']) && !empty($rul['rtwwdpd_att_max_discount']))
														{
															if($rtwwdpd_amount > $rul['rtwwdpd_att_max_discount'])
															{
																$rtwwdpd_amount = $rul['rtwwdpd_att_max_discount'];
															}
														}
														$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount );

														$ids=0;
														
														if(isset($rul['rtwwdpd_att_exclude_sale']))
														{
															if( !$cart_item['data']->is_on_sale() )
															{
																Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																return;
															}
														}
														else{
															Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
															return;
														}
													}
												}
											}
										}
										continue 1;
									}
									elseif($kval == 'prod_tag_rule_row')
									{	
										$rtwwdpd_pro_rul = get_option('rtwwdpd_tag_method');
										
										if(!is_array($rtwwdpd_pro_rul) || empty($rtwwdpd_pro_rul))
										{
											continue 1;
										}
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

												$rtwwdpd_original_price = $cart_item['data']->get_price();
												// $this->rtw_get_price_to_discount( $cart_item, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ) );

												if ( $rtwwdpd_original_price ) {
													$rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $rul['rtwwdpd_tag_discount_value'], 'adv_totl', $cart_item, $this );

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

														if( isset($rul['rtwwdpd_tag_max_discount']) && !empty($rul['rtwwdpd_tag_max_discount']) && $rtwwdpd_discnted_val > $rul['rtwwdpd_tag_max_discount'])
														{
															$rtwwdpd_discnted_val = $rul['rtwwdpd_tag_max_discount'];
														}
														$rtwwdpd_price_adjusted = ( floatval( $rtwwdpd_original_price ) - $rtwwdpd_discnted_val );

														if( isset( $rul['rtw_product_tags'] ) && is_array( $rul['rtw_product_tags'] ) && !empty( $rul['rtw_product_tags'] ) )
														{
															$rtw_matched = array_intersect( $rul['rtw_product_tags'], $cart_item['data']->get_tag_ids());

															if( empty( $rtw_matched ) )
															{
																continue 1;
															}
														}

														if(isset($rul['rtwwdpd_tag_exclude_sale']))
														{
															if( !$cart_item['data']->is_on_sale() )
															{
																Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																return;
															}
														}
														else{
															Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
															return;
														}
													}
													elseif($rul['rtwwdpd_tag_discount_type'] == 'rtwwdpd_flat_discount_amount')
													{
														if( isset($rul['rtwwdpd_tag_max_discount']) && !empty($rul['rtwwdpd_tag_max_discount']) && $rtwwdpd_discnted_val > $rul['rtwwdpd_tag_max_discount'])
														{
															if($rtwwdpd_amount > $rul['rtwwdpd_tag_max_discount'])
															{
																$rtwwdpd_amount = $rul['rtwwdpd_tag_max_discount'];
															}
														}
														$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount );

														if( isset( $rul['rtw_product_tags'] ) && is_array( $rul['rtw_product_tags'] ) && !empty( $rul['rtw_product_tags'] ) )
														{
															$rtw_matched = array_intersect( $rul['rtw_product_tags'], $cart_item['data']->get_tag_ids());

															if( empty( $rtw_matched ) )
															{
																continue 1;
															}
														}

														if(isset($rul['rtwwdpd_tag_exclude_sale']))
														{
															if( !$cart_item['data']->is_on_sale() )
															{
																Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																return;
															}
														}
														else{
															Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
															return;
														}
													}
													else
													{
														if( isset($rul['rtwwdpd_tag_max_discount']) && !empty($rul['rtwwdpd_tag_max_discount']) && $rtwwdpd_discnted_val > $rul['rtwwdpd_tag_max_discount'])
														{
															if($rtwwdpd_amount > $rul['rtwwdpd_tag_max_discount'])
															{
																$rtwwdpd_amount = $rul['rtwwdpd_tag_max_discount'];
															}
														}
														$rtwwdpd_amount = ( $rtwwdpd_amount / $cart_item['quantity'] );

														$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount );

														if( isset( $rul['rtw_product_tags'] ) && is_array( $rul['rtw_product_tags'] ) && !empty( $rul['rtw_product_tags'] ) )
														{
															$rtw_matched = array_intersect( $rul['rtw_product_tags'], $cart_item['data']->get_tag_ids());

															if( empty( $rtw_matched ) )
															{
																continue 1;
															}
														}

														if(isset($rul['rtwwdpd_tag_exclude_sale']))
														{
															if( !$cart_item['data']->is_on_sale() )
															{
																Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																return;
															}
														}
														else{
															Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
															return;
														}
													}
												}
											}
										}
										continue 1;
									}
									elseif($kval == 'tier_rule_row')
									{	
										$rtwwdpd_pro_rul = get_option('rtwwdpd_tiered_rule');
										
										if(!is_array($rtwwdpd_pro_rul) || empty($rtwwdpd_pro_rul))
										{
											continue 1;
										}
										foreach ($rtwwdpd_pro_rul as $pro => $rul) {

											if($rul['rtwwdpd_from_date'] > $rtwwdpd_today_date || $rul['rtwwdpd_to_date'] < $rtwwdpd_today_date)
											{
												continue;
											}

											$rtwwdpd_user_role = $rul['rtwwdpd_select_roles'] ;
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

												$product = $cart_item['data'];
												$rtwwdpd_prod_id = $cart_item['data']->get_id();

												foreach ($rtwwdpd_pro_rul as $id => $id_val) {

													if($id_val['products'][0] == $rtwwdpd_prod_id){
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
													$rtwwdpd_d = WC()->cart->cart_contents[ $cart_item_key ]['discounts'];
													if (in_array('advanced_totals', $rtwwdpd_d['by'])) {
														continue;
													}
												}

												$rtwwdpd_original_price = $this->rtw_get_price_to_discount( $cart_item, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ) );

												if ( $rtwwdpd_original_price ) {

													$pp = 0;
													$rtwwdpd_amount = 0;
													if( is_array($rul['discount_val']) && !empty($rul['discount_val']) )
													{
														foreach ($rul['discount_val'] as $dis => $disval) {
															
																$rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $rul['discount_val'][$dis], $rule, $cart_item, $this );

															if($rul['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
															{
																$rtwwdpd_amount = $rtwwdpd_amount / 100;

																$rtwwdpd_discnted_val = ( floatval( $rtwwdpd_amount ) * $rtwwdpd_original_price );

																if( isset($rul['rtwwdpd_max_discount']) && !empty($rul['rtwwdpd_max_discount']) && $rtwwdpd_discnted_val > $rul['rtwwdpd_max_discount'])
																{
																	$rtwwdpd_discnted_val = $rul['rtwwdpd_max_discount'];
																	
																}

																$rtwwdpd_price_adjusted = ( floatval( $rtwwdpd_original_price ) - $rtwwdpd_discnted_val );

																if($rul['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
																{
																	if( $rul['quant_min'][$dis] <= $cart_item['quantity'] && $rul['quant_max'][$dis] >= $cart_item['quantity'])
																	{	
																		foreach ($rul['products'] as $p => $pid) 
																		{
																		if($pid == $cart_item['data']->get_id())
																		{				
																			if(isset($rul['rtwwdpd_exclude_sale']))
																			{
																				if( !$cart_item['data']->is_on_sale() )
																				{
																					Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																					return;
																				}
																			}
																			else{
																				Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																				return;
																			}
																		}
																	}
																	}
																}
																elseif($rul['rtwwdpd_check_for'] == 'rtwwdpd_price')
																{
																	if( $rul['quant_min'][$dis] <= $cart_item['data']->get_price() && $rul['quant_max'][$dis] >= $cart_item['data']->get_price())
																	{	
																		foreach ($rul['products'] as $p => $pid) 
																		{
																		if($pid == $cart_item['data']->get_id())
																		{				
																			if(isset($rul['rtwwdpd_exclude_sale']))
																			{
																				if( !$cart_item['data']->is_on_sale() )
																				{
																					Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																					return;
																				}
																			}
																			else{
																				Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																				return;
																			}
																		}
																	}
																	}
																}
																else
																{
																	if( $rul['quant_min'][$dis] <= $cart_item['data']->get_weight() && $rul['quant_max'][$dis] >= $cart_item['data']->get_weight())
																	{	
																		foreach ($rul['products'] as $p => $pid) 
																		{
																		if($pid == $cart_item['data']->get_id())
																		{				
																			if(isset($rul['rtwwdpd_exclude_sale']))
																			{
																				if( !$cart_item['data']->is_on_sale() )
																				{
																					Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																					return;
																				}
																			}
																			else{
																				Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																				return;
																			}
																		}
																	}
																	}
																}
															}
															elseif($rul['rtwwdpd_discount_type'] == 'rtwwdpd_flat_discount_amount')
															{ 
																if( isset($rul['rtwwdpd_max_discount']) && !empty($rul['rtwwdpd_max_discount']))
																{
																	if($rtwwdpd_amount > $rul['rtwwdpd_max_discount'])
																	{
																		$rtwwdpd_amount = $rul['rtwwdpd_max_discount'];
																	}
																}
																$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount);

																if($rul['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
																{
																	if( $rul['quant_min'][$dis] <= $cart_item['quantity'] && $rul['quant_max'][$dis] >= $cart_item['quantity'])
																	{	
																		foreach ($rul['products'] as $p => $pid) 
																		{
																		if($pid == $cart_item['data']->get_id())
																		{				
																			if(isset($rul['rtwwdpd_exclude_sale']))
																			{ 
																				if( !$cart_item['data']->is_on_sale() )
																				{
																					Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																					return;
																				}
																			}
																			else{
																				Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																				return;
																			}
																		}
																	}
																	}
																}
																elseif($rul['rtwwdpd_check_for'] == 'rtwwdpd_price')
																{
																	if( $rul['quant_min'][$dis] <= $cart_item['data']->get_price() && $rul['quant_max'][$dis] >= $cart_item['data']->get_price())
																	{	
																		foreach ($rul['products'] as $p => $pid) 
																		{
																		if($pid == $cart_item['data']->get_id())
																		{				
																			if(isset($rul['rtwwdpd_exclude_sale']))
																			{
																				if( !$cart_item['data']->is_on_sale() )
																				{
																					Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																					return;
																				}
																			}
																			else{
																				Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																				return;
																			}
																		}
																	}
																	}
																}
																else
																{
																	if( $rul['quant_min'][$dis] <= $cart_item['data']->get_weight() && $rul['quant_max'][$dis] >= $cart_item['data']->get_weight())
																	{	
																		foreach ($rul['products'] as $p => $pid) 
																		{
																		if($pid == $cart_item['data']->get_id())
																		{				
																			if(isset($rul['rtwwdpd_exclude_sale']))
																			{
																				if( !$cart_item['data']->is_on_sale() )
																				{
																					Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																					return;
																				}
																			}
																			else{
																				Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																				return;
																			}
																		}
																	}
																	}
																}
															}
															else
															{ 
																if( isset($rul['rtwwdpd_max_discount']) && !empty($rul['rtwwdpd_max_discount']))
																{
																	if($rtwwdpd_amount > $rul['rtwwdpd_max_discount'])
																	{
																		$rtwwdpd_amount = $rul['rtwwdpd_max_discount'];
																	}
																}
																$rtwwdpd_amount = ( $rtwwdpd_amount / $cart_item['quantity'] );
																$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount );

																if($rul['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
																{
																	if( $rul['quant_min'][$dis] <= $cart_item['quantity'] && $rul['quant_max'][$dis] >= $cart_item['quantity'])
																	{	
																		foreach ($rul['products'] as $p => $pid) 
																		{
																		if($pid == $cart_item['data']->get_id())
																		{				
																			if(isset($rul['rtwwdpd_exclude_sale']))
																			{ 
																				if( !$cart_item['data']->is_on_sale() )
																				{
																					Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																					return;
																				}
																			}
																			else{
																				Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																				return;
																			}
																		}
																	}
																	}
																}
																elseif($rul['rtwwdpd_check_for'] == 'rtwwdpd_price')
																{
																	if( $rul['quant_min'][$dis] <= ($cart_item['data']->get_price() * $cart_item['quantity']) && $rul['quant_max'][$dis] >= ($cart_item['data']->get_price() * $cart_item['quantity']))
																	{		
																		foreach ($rul['products'] as $p => $pid) 
																		{
																		if($pid == $cart_item['data']->get_id())
																		{				
																			if(isset($rul['rtwwdpd_exclude_sale']))
																			{
																				if( !$cart_item['data']->is_on_sale() )
																				{
																					Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																					return;
																				}
																			}
																			else{
																				Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																				return;
																			}
																		}
																	}
																	}
																}
																else
																{
																	if( $rul['quant_min'][$dis] <= $cart_item['data']->get_weight() && $rul['quant_max'][$dis] >= $cart_item['data']->get_weight())
																	{	
																		foreach ($rul['products'] as $p => $pid) 
																		{
																		if($pid == $cart_item['data']->get_id())
																		{				
																			if(isset($rul['rtwwdpd_exclude_sale']))
																			{
																				if( !$cart_item['data']->is_on_sale() )
																				{
																					Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																					return;
																				}
																			}
																			else{
																				Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																				return;
																			}
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
										continue 1;
									}
									elseif($kval == 'tier_cat_rule_row')
									{	
										$rtwwdpd_pro_rul = get_option('rtwwdpd_tiered_cat');
										
										if(!is_array($rtwwdpd_pro_rul) || empty($rtwwdpd_pro_rul))
										{
											continue 1;
										}
										
										foreach ($rtwwdpd_pro_rul as $pro => $rul) {
											
											if($rul['rtwwdpd_frm_date_c'] > $rtwwdpd_today_date || $rul['rtwwdpd_to_date_c'] < $rtwwdpd_today_date)
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

												if(isset($rul['product_exe_id']) && $rul['product_exe_id'] == $cart_item['data']->get_id())
												{
													continue 1;
												}

												$product = $cart_item['data'];

												$rtwwdpd_cat_id = $this->rtwwdpd_get_prod_cat_ids( $product );
												foreach ($rtwwdpd_pro_rul as $id => $id_val) {

													if($id_val['category_id'][0] == $rtwwdpd_cat_id){
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
													if (in_array('advanced_totals', $d['by'])) {
														continue;
													}
												}

												$rtwwdpd_original_price = $this->rtw_get_price_to_discount( $cart_item, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ) );

												if ( $rtwwdpd_original_price ) {

													$pp = 0;
													$rtwwdpd_amount = 0;
													if( is_array($rul['discount_val']) && !empty($rul['discount_val']) )
													{
														foreach ($rul['discount_val'] as $dis => $disval) {
															if(is_array($rul['quant_min']) && !empty($rul['quant_min'])){
																foreach ($rul['quant_min'] as $f => $fro) {

																	if($cart_item['quantity'] >= $fro && $cart_item['quantity'] <= $rul['quant_max'][$f])
																	{
																		$rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $rul['discount_val'][$f], $rule, $cart_item, $this );
																	}
																}
															}
											
                              if( isset($cart_item['variation_id']) && !empty($cart_item['variation_id']) )
                              {
                                $rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_parent_id(), 'product_cat', array( 'fields' => 'ids' ) );
                              }else{
                                $rtwwdpd_catids = wp_get_post_terms( $cart_item['data']->get_id(), 'product_cat', array( 'fields' => 'ids' ) );
                              }

															if($rul['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
															{
																$rtwwdpd_amount = $rtwwdpd_amount / 100;
																$rtwwdpd_discnted_val = ( floatval( $rtwwdpd_amount ) * $rtwwdpd_original_price );
																if( isset($rul['rtwwdpd_max_discount']) && !empty($rul['rtwwdpd_max_discount']) && $rtwwdpd_discnted_val > $rul['rtwwdpd_max_discount'])
																{
																	$rtwwdpd_discnted_val = $rul['rtwwdpd_max_discount'];
																}

																$rtwwdpd_price_adjusted = ( floatval( $rtwwdpd_original_price ) - $rtwwdpd_discnted_val );
												
																if($rul['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
																{
																	if( $rul['quant_min'][$dis] <= $cart_item['quantity'] && $rul['quant_max'][$dis] >= $cart_item['quantity'] )
																	{	
																		// $rtwwdpd_catids = $cart_item['data']->get_category_ids();

																		if(in_array($rul['category_id'][0], $rtwwdpd_catids))
																		{			
																			if(isset($rul['rtwwdpd_exclude_sale']))
																			{
																				if( !$cart_item['data']->is_on_sale() )
																				{
																					Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																					return;
																				}
																			}
																			else{
																				Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																				return;
																			}
																		}
																	}
																}
																elseif($rul['rtwwdpd_check_for'] == 'rtwwdpd_price')
																{
																	if( $rul['quant_min'][$pp] <= $cart_item['data']->get_price() && $rul['quant_max'][$pp] >= $cart_item['data']->get_price())
																	{	
																		// $rtwwdpd_catids = $cart_item['data']->get_category_ids();

																		if(in_array($rul['category_id'][0], $rtwwdpd_catids))
																		{			
																			if(isset($rul['rtwwdpd_exclude_sale']))
																			{
																				if( !$cart_item['data']->is_on_sale() )
																				{
																					Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																					return;
																				}
																			}
																			else{
																				Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																				return;
																			}
																		}
																	}
																}
																else
																{
																	if( $rule['quant_min'][$pp] <= $cart_item['data']->get_weight() && $rule['quant_max'][$pp] >= $cart_item['data']->get_weight())
																	{	
																		// $rtwwdpd_catids = $cart_item['data']->get_category_ids();

																		if(in_array($rul['category_id'][0], $rtwwdpd_catids))
																		{				
																			if(isset($rul['rtwwdpd_exclude_sale']))
																			{
																				if( !$cart_item['data']->is_on_sale() )
																				{
																					Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																					return;
																				}
																			}
																			else{
																				Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																				return;
																			}
																		}
																	}
																}
															}
															elseif($rul['rtwwdpd_discount_type'] == 'rtwwdpd_flat_discount_amount')
															{

																if( isset($rul['rtwwdpd_max_discount']) && !empty($rul['rtwwdpd_max_discount']) && $rtwwdpd_amount > $rul['rtwwdpd_max_discount'])
																{
																	$rtwwdpd_amount = $rul['rtwwdpd_max_discount'];
																}
																$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount);

																if($rul['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
																{
																	if( $rul['quant_min'][$pp] <= $cart_item['quantity'] && $rul['quant_max'][$pp] >= $cart_item['quantity'])
																	{	
																		// $rtwwdpd_catids = $cart_item['data']->get_category_ids();

																		if( in_array($rul['category_id'][0], $rtwwdpd_catids) )
																		{			
																			if(isset($rul['rtwwdpd_exclude_sale']))
																			{
																				if( !$cart_item['data']->is_on_sale() )
																				{
																					Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																					return;
																				}
																			}
																			else{
																				Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																				return;
																			}
																		}
																	}
																}
																elseif($rul['rtwwdpd_check_for'] == 'rtwwdpd_price')
																{
																	if( $rul['quant_min'][$pp] <= $cart_item['data']->get_price() && $rul['quant_max'][$pp] >= $cart_item['data']->get_price())
																	{	
																		// $rtwwdpd_catids = $cart_item['data']->get_category_ids();

																		if(in_array($rul['category_id'][0], $rtwwdpd_catids))
																		{			
																			if(isset($rul['rtwwdpd_exclude_sale']))
																			{
																				if( !$cart_item['data']->is_on_sale() )
																				{
																					Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																					return;
																				}
																			}
																			else{
																				Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																				return;
																			}
																		}
																	}
																}
																else
																{
																	if( $rul['quant_min'][$pp] <= $cart_item['data']->get_weight() && $rul['quant_max'][$pp] >= $cart_item['data']->get_weight())
																	{	
																		// $rtwwdpd_catids = $cart_item['data']->get_category_ids();

																		if( in_array($rul['category_id'][0], $rtwwdpd_catids) )
																		{			
																			if(isset($rul['rtwwdpd_exclude_sale']))
																			{
																				if( !$cart_item['data']->is_on_sale() )
																				{
																					Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																					return;
																				}
																			}
																			else{
																				Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																				return;
																			}
																		}
																	}
																}
															}
															else
															{
																if( isset($rul['rtwwdpd_max_discount']) && !empty($rul['rtwwdpd_max_discount']) && $rtwwdpd_amount > $rul['rtwwdpd_max_discount'])
																{
																	$rtwwdpd_amount = $rul['rtwwdpd_max_discount'];
																}
																$rtwwdpd_amount = ( $rtwwdpd_amount / $cart_item['quantity'] );

																$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_amount);

																if($rul['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
																{
																	if( $rul['quant_min'][$pp] <= $cart_item['quantity'] && $rul['quant_max'][$pp] >= $cart_item['quantity'])
																	{	
																		// $rtwwdpd_catids = $cart_item['data']->get_category_ids();

																		if( in_array($rul['category_id'][0], $rtwwdpd_catids) )
																		{			
																			if(isset($rul['rtwwdpd_exclude_sale']))
																			{
																				if( !$cart_item['data']->is_on_sale() )
																				{
																					Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																					return;
																				}
																			}
																			else{
																				Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																				return;
																			}
																		}
																	}
																}
																elseif($rul['rtwwdpd_check_for'] == 'rtwwdpd_price')
																{
																	if( $rul['quant_min'][$pp] <= $cart_item['data']->get_price() && $rul['quant_max'][$pp] >= $cart_item['data']->get_price())
																	{	
																		// $rtwwdpd_catids = $cart_item['data']->get_category_ids();

																		if(in_array($rul['category_id'][0], $rtwwdpd_catids))
																		{			
																			if(isset($rul['rtwwdpd_exclude_sale']))
																			{
																				if( !$cart_item['data']->is_on_sale() )
																				{
																					Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																					return;
																				}
																			}
																			else{
																				Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																				return;
																			}
																		}
																	}
																}
																else
																{
																	if( $rul['quant_min'][$pp] <= $cart_item['data']->get_weight() && $rul['quant_max'][$pp] >= $cart_item['data']->get_weight())
																	{	
																		// $rtwwdpd_catids = $cart_item['data']->get_category_ids();

																		if( in_array($rul['category_id'][0], $rtwwdpd_catids) )
																		{			
																			if(isset($rul['rtwwdpd_exclude_sale']))
																			{
																				if( !$cart_item['data']->is_on_sale() )
																				{
																					Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																					return;
																				}
																			}
																			else{
																				Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																				return;
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
										continue 1;
									}
									elseif($kval == 'bogo_rule_row')
									{	
										$rtwwdpd_pro_rul = get_option('rtwwdpd_bogo_rule');

										if(!is_array($rtwwdpd_pro_rul) || empty($rtwwdpd_pro_rul))
										{
											continue 1;
										}
										$active_dayss = get_site_option('rtwwdpd_discount_on_selected_days', 'no');
										
										foreach ($rtwwdpd_pro_rul as $pro => $rul) {
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
											if($rul['rtwwdpd_bogo_from_date'] > $rtwwdpd_today_date || $rul['rtwwdpd_bogo_to_date'] < $rtwwdpd_today_date)
											{
												continue;
											}

											$rtw_pro_idss = array();
											$rtw_p_c 	= array();
											$temp_pro_ids = array();
											foreach ($rtwwdpd_pro_rul as $ky => $va) {
												foreach ($va['combi_quant'] as $ke => $valu) {
													$rtw_p_c[] = $valu;
												}
											}
											if(!empty($rul['product_id']))
											{
												foreach($rul['product_id'] as $pro => $proid)
												{
													$rtw_pro_idss[] = $proid;
												}
											}

											foreach ( $rtwwdpd_temp_cart as $cart_item )
											{
												$temp_pro_ids[] = $cart_item['data']->get_id();
												$temp_pro_ids[] = $cart_item['data']->get_parent_id();
											}
											$rtw_result = array_diff($rtw_pro_idss, $temp_pro_ids);

											if( !empty( $rtw_result ) ){
												continue;
											}
											if($rtwwdpd_cart_total < $rul['rtwwdpd_bogo_min_spend'])
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

											$rtwwdpd_restricted_mails = isset( $rul['rtwwdpd_select_emails'] ) ? $rul['rtwwdpd_select_emails'] : array();

											$rtwwdpd_cur_user_mail = get_current_user_id();
											
											if(in_array($rtwwdpd_cur_user_mail, $rtwwdpd_restricted_mails))
											{
												continue 1;
											}

											foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item ) {

												$product = $cart_item['data'];
												$rtwwdpd_prod_id = $cart_item['data']->get_id();


												if ( ! $this->rtwwdpd_is_cumulative( $cart_item, $cart_item_key ) ) {

													if ( $this->rtwwdpd_is_item_discounted( $cart_item, $cart_item_key ) && apply_filters( 'rtwwdpd_stack_order_totals', false ) === false ) {
														continue;
													}
												}

												$rtwwdpd_discounted = isset( WC()->cart->cart_contents[ $cart_item_key ]['discounts'] );

												if ($rtwwdpd_discounted){
													$d = WC()->cart->cart_contents[ $cart_item_key ]['discounts'];
													if (in_array('advanced_totals', $d['by'])) {
														continue;
													}
												}
												$rtwwdpd_free_prod = $rul['rtwbogo'];

												if(!empty($rul['product_id'][0]))
												{
													$rtwwdpd_p_id = $rul['product_id'][0];
												}else{
													$rtwwdpd_p_id = '';
												}
												
												$cart = $woocommerce->cart;

												$rtwwdpd_rule_on = apply_filters('rtwwdpd_rule_applied_on_bogo', $pro );

												if( $rtwwdpd_rule_on === $pro )
												{
													$rtwwdpd_rule_on = 'product';
												}
												if($rtwwdpd_rule_on == 'product' && isset($rul['product_id'][0]))
												{			
													$rtw_free_qunt = 0;
													$rtwwdpd_f_quant = 0;
													if($rul['product_id'][0] == $cart_item['data']->get_id() || $rul['product_id'][0] == $cart_item['data']->get_parent_id() )
													{
													if( $rul['combi_quant'][0] <= $cart_item['quantity'])
													{
														$rtwwdpd_f_quant = floor( $cart_item['quantity'] / $rul['combi_quant'][0] );
														if( $rtwwdpd_f_quant >= 2 )
														{
															$rtw_free_qunt = ((isset( $rul['bogo_quant_free'][0] ) ? $rul['bogo_quant_free'][0] : 0 )* $rtwwdpd_f_quant);

														}
														else {
															$rtw_free_qunt = isset( $rul['bogo_quant_free'][0] ) ? $rul['bogo_quant_free'][0] : 0;
														}

														$rtwwdpd_get_settings = get_option('rtwwdpd_setting_priority');
														$i =0;
														$free_i = 0;

														if( is_array($rul['rtwbogo']) && !empty($rul['rtwbogo']) ) 
														{
															foreach ($rul['rtwbogo'] as $k => $val) {
																$rtw_free_p_id = $val;
																$product_data = wc_get_product($rtw_free_p_id);

																$rtw_prod_cont = $rul['combi_quant'][$k];

																if( !empty($rul['product_id']) && $rul['product_id'][$free_i] == $rtwwdpd_p_id)
																{

																	if($rtwwdpd_get_settings['rtw_auto_add_bogo'] == 'rtw_yes')
																	{
																		$found 		= false;
														        //check if product already in cart
																		if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
																			foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
																				$_product = $values['data'];
																				if ( $_product->get_id() == 'rtw_free_prod' . $rtw_free_p_id )
																					$found = true;
																			}

														            // if product not found, add it
																			if ( ! $found )
																			{
																				$cart_item_key = 'rtw_free_prod' . $rtw_free_p_id;
																				$cart->cart_contents[$cart_item_key] = array(
																					'product_id' => $rtw_free_p_id,
																					'variation_id' => 0,
																					'variation' => array(),
																					'quantity' => $rtw_free_qunt,
																					'data' => $product_data,
																					'line_total' => 0
																				);
																				return;
																			}
																		}         
																	}
																}
																else{
																	if($rtwwdpd_get_settings['rtw_auto_add_bogo'] == 'rtw_yes')
																	{
																		$found 		= false;
																		
																		if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
																			foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
																				$_product = $values['data'];
																				if ( $_product->get_id() == 'rtw_free_prod' . $rtw_free_p_id )
																					$found = true;

																				if ( $found && $cart_item_key == ('rtw_free_prod' . $rtw_free_p_id) )
																				{
																					$cart_item_key = 'rtw_free_prod' . $rtw_free_p_id;
																					$cart->cart_contents[$cart_item_key]['quantity'] = 1;
																				}
																				elseif( !$found ) 
																				{
																					$cart_item_key = 'rtw_free_prod' . $rtw_free_p_id;
																					$cart->cart_contents[$cart_item_key] = array(
																						'product_id' => $rtw_free_p_id,
																						'variation_id' => 0,
																						'variation' => array(),
																						'quantity' => $rtw_free_qunt,
																						'data' => $product_data,
																						'line_total' => 0
																					);
																					return;
																				}
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
												}elseif($rul['rtwwdpd_bogo_rule_on'] == 'min_purchase')
												{
													if( $rtwwdpd_cart_total < $rul['rtwwdpd_min_purchase'] )
													{
														continue;
													}
													if( is_array($rul['rtwbogo']) && !empty($rul['rtwbogo']) ) 
													{
														
														$rtwwdpd_get_settings = get_option('rtwwdpd_setting_priority');

														foreach ($rul['rtwbogo'] as $k => $val) {
															$rtw_free_p_id = $val;
															$product_data = wc_get_product($rtw_free_p_id);
															$rtw_free_qunt = $rul['bogo_quant_free'][$k];

															$rtw_prod_cont = $rul['combi_quant'][$k];

															
															if($rtwwdpd_get_settings['rtw_auto_add_bogo'] == 'rtw_yes')
															{
																$found 		= false;
														
																if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
																	foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
																		$_product = $values['data'];
																		if ( $_product->get_id() == 'rtw_free_prod' . $rtw_free_p_id )
																			$found = true;

																		if ( $found && $cart_item_key == ('rtw_free_prod' . $rtw_free_p_id) )
																		{
																			$cart_item_key = 'rtw_free_prod' . $rtw_free_p_id;
																			$cart->cart_contents[$cart_item_key]['quantity'] = 1;
																		}
																		elseif( !$found ) 
																		{
																			$cart_item_key = 'rtw_free_prod' . $rtw_free_p_id;
																			$cart->cart_contents[$cart_item_key] = array(
																				'product_id' => $rtw_free_p_id,
																				'variation_id' => 0,
																				'variation' => array(),
																				'quantity' => $rtw_free_qunt,
																				'data' => $product_data,
																				'line_total' => 0
																			);
																			return;
																		}
																	}
																}         
															}
															
															$free_i++;
														}
													}
												}
											}
										}
									}
									elseif($kval == 'bogo_cat_rule_row')
									{	
										$rtwwdpd_pro_rul = get_option('rtwwdpd_bogo_cat_rule');
										if( !is_array($rtwwdpd_pro_rul) || empty($rtwwdpd_pro_rul) )
										{
											continue 1;
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
												
												if( $item['data']->get_type() == 'variable' || $item['data']->get_type() == 'variation' )
												{
													$__product = wc_get_product($item['data']->get_parent_id());
													$categorys = $__product->get_category_ids();
													if( is_array($categorys) && !empty($categorys) )
													{
														foreach ($categorys as $cc => $c) {
															$category_in_cart[] = $c;
														}
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
												}
												$quantity_in_cart += $item['quantity'];
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

										foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item ){
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

											if(isset($rul['product_exe_id']) && $rul['product_exe_id'] == $cart_item['data']->get_id())
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

											$rtwwdpd_original_price = $this->rtw_get_price_to_discount( $cart_item, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ) );

											if ( $rtwwdpd_original_price ) {
											$cart = $woocommerce->cart;
											if(isset($rul['category_id']))
											{		
														
												$rtwwdpd_catids = array();

												if( $item['data']->get_type() == 'variable' || $item['data']->get_type() == 'variation' )
												{
													$__product = wc_get_product($item['data']->get_parent_id());
													$categorys = $__product->get_category_ids();
													if( is_array($categorys) && !empty($categorys) )
													{
														foreach ($categorys as $cc => $c) {
															$rtwwdpd_catids[] = $c;
														}
													}
												}
												else{
													$categorys = $item['data']->get_category_ids();
													if( is_array($categorys) && !empty($categorys) )
													{
													foreach ($categorys as $cc => $c) {
														$rtwwdpd_catids[] = $c;
													}
													}
												}

												if(in_array($rul['category_id'][0], $rtwwdpd_catids))
												{
													
												$i =0;
												$free_i = 0;
												if( is_array($rul['rtwbogo']) && !empty($rul['rtwbogo']))
												{
													foreach ($rul['rtwbogo'] as $k => $val) {
													$rtwwdpd_free_p_id = $val;
													$rtwwdpd_product_data = wc_get_product($rtwwdpd_free_p_id);

													// $rtwwdpd_free_qunt = $rul['bogo_quant_free'][$k];
													$rtwwdpd_prod_cont = $rul['combi_quant'][$k];
													
													
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
															'line_total' => 0
															);
															return;
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
										}
					          			}
									}
									elseif($kval == 'pay_rule_row')
									{
										$rtwwdpd_pay_rul = get_option('rtwwdpd_pay_method');

										if(!is_array($rtwwdpd_pay_rul) || empty($rtwwdpd_pay_rul))
										{
											continue 1;
										}
										foreach ($rtwwdpd_pay_rul as $pay => $rul) {
											
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
												continue;
											}

											if(isset($rul['rtwwdpd_min_prod_cont']) && $rul['rtwwdpd_min_prod_cont'] > $rtwwdpd_prod_count)
											{
												continue;
											}
											if(isset($rul['rtwwdpd_min_spend']) && $rul['rtwwdpd_min_spend'] > $rtwwdpd_cart_total)
											{
												continue;
											}

											$rtwwdpd_matched = true;
											if($rul['rtwwdpd_pay_from_date'] > $rtwwdpd_today_date || $rul['rtwwdpd_pay_to_date'] < $rtwwdpd_today_date)
											{
												continue;
											}

											foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item ) {
												$product = $cart_item['data'];

												$rtwwdpd_process_discounts = apply_filters( 'rtwwdpd_process_product_discounts', true, $cart_item['data'], 'advanced_totals', $this, $cart_item );

												if ( ! $rtwwdpd_process_discounts ) {
													continue;
												}

												if ( ! $this->rtwwdpd_is_cumulative( $cart_item, $cart_item_key ) ) {


													if ( $this->rtwwdpd_is_item_discounted( $cart_item, $cart_item_key ) && apply_filters( 'rtwwdpd_stack_order_totals', false ) === false ) {
														continue;
													}
												}

												$rtwwdpd_discounted = isset( WC()->cart->cart_contents[ $cart_item_key ]['discounts'] );

												if ($rtwwdpd_discounted){
													$d = WC()->cart->cart_contents[ $cart_item_key ]['discounts'];
													if (in_array('advanced_totals', $d['by'])) {
														continue;
													}
												}

												$rtwwdpd_original_price = $this->rtw_get_price_to_discount( $cart_item, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ) );

												if ( $rtwwdpd_original_price ) {
													$rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $rul['rtwwdpd_pay_discount_value'], $rule, $cart_item, $this );

													$rtwwdpd_cart_prod_count = count( WC()->cart->get_cart());
													$rtwwdpd_cart_total = $woocommerce->cart->get_subtotal();
													$rtwwdpd_chosen_gateway = WC()->session->chosen_payment_method;
													$rtwwdpd_dscnt_on = $rul['allowed_payment_methods'];
												
													if($rtwwdpd_chosen_gateway == $rtwwdpd_dscnt_on)
													{
														if($rul['rtwwdpd_min_prod_cont'] <= $rtwwdpd_cart_prod_count && $rul['rtwwdpd_min_spend'] <= $rtwwdpd_cart_total)
														{
															if($rul['rtwwdpd_pay_discount_type'] == 'rtwwdpd_discount_percentage')
															{
																$rtwwdpd_amount = $rtwwdpd_amount / 100;
																$rtwwdpd_discnted_val = ( floatval( $rtwwdpd_amount ) * $rtwwdpd_original_price );
																if( $rtwwdpd_discnted_val > $rul['rtwwdpd_pay_max_discount'])
																{
																	$rtwwdpd_discnted_val = $rul['rtwwdpd_pay_max_discount'];
																}

																$rtwwdpd_price_adjusted = ( floatval( $rtwwdpd_original_price ) - $rtwwdpd_discnted_val );

																if(isset($rul['rtwwdpd_pay_exclude_sale']))
																{
																	if( !$cart_item['data']->is_on_sale() )
																	{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																		return;
																	}
																}
																else{
																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																	return;
																}
															}
															else{

																if($rtwwdpd_amount > $rul['rtwwdpd_pay_max_discount'])
																{
																	$rtwwdpd_amount = $rul['rtwwdpd_pay_max_discount'];
																}
																$rtwwdpd_new_price = $rtwwdpd_amount/$rtwwdpd_prod_count;

																$rtwwdpd_price_adjusted = ( $rtwwdpd_original_price - $rtwwdpd_new_price );

																if(isset($rul['rtwwdpd_pay_exclude_sale']))
																{

																	if( !$cart_item['data']->is_on_sale() )
																	{
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																		return;
																	}
																}
																else{
																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																	return;
																}
															}
														}
													}
												}
											}
										}
										continue 1;
									}
									elseif($kval == 'var_rule_row')
									{
										$rtwwdpd_var_rul = get_option('rtwwdpd_variation_rule');
										
										if(!is_array($rtwwdpd_var_rul) || empty($rtwwdpd_var_rul))
										{
											continue 1;
										}

										$rtwwdpd_variation_arr = array();
										foreach ($rtwwdpd_var_rul as $key => $value) {
											$rtwwdpd_variation_arr[$key] = $value['rtwwdpd_offer_name'];
										}
										$rtwwdpd_variation_arr 	= array_merge( array( '0' => 'Select Offer' ), $rtwwdpd_variation_arr );

										$it = 1;
										foreach ($rtwwdpd_var_rul as $var => $rul) {
											if($rul['rtwwdpd_from_date'] > $rtwwdpd_today_date || $rul['rtwwdpd_to_date'] < $rtwwdpd_today_date)
											{
												continue 2;
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
												continue 2;
											}

											if(isset($rul['rtwwdpd_min_orders']) && $rul['rtwwdpd_min_orders'] > $rtwwdpd_no_oforders)
											{
												continue 2;
											}
											if(isset($rul['rtwwdpd_min_spend']) && $rul['rtwwdpd_min_spend'] > $rtwwdpd_ordrtotal)
											{
												continue 2;
											}

											foreach ($rtwwdpd_temp_cart as $cart_item_key => $cart_item) {

												if(isset($rul['product_exe_id']) && $rul['product_exe_id'] == $cart_item['data']->get_id())
												{
													continue 1;
												}

												if($cart_item['variation_id'] > 0)
												{
													$product = wc_get_product($cart_item['variation_id']);
													$rtwwdpd_var_offer = get_post_meta($cart_item['variation_id'], 'rtwwdpd_variation');
													
													$rtwwdpd_rules = $rtwwdpd_var_offer[0];

													if( stripos($rtwwdpd_rules, $rul['rtwwdpd_offer_name']) )
													{
														if($rul['rtwwdpd_check_for'] == 'rtwwdpd_quantity')
														{
															if($cart_item['quantity'] >= $rul['rtwwdpd_min'])
															{ 		
																if( isset($rul['rtwwdpd_max']) && $rul['rtwwdpd_max'] < $cart_item['quantity'] )
																{
																	continue;
																}
																if ( ! $this->rtwwdpd_is_cumulative( $cart_item, $cart_item_key ) ) 
																{
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

																$rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $rule['amount'], $rule, $cart_item, $this );

																$rtwwdpd_original_price = $this->rtw_get_price_to_discount( $cart_item, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ) );

																if($rul['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
																{
																	$rtwwdpd_price = $rul['rtwwdpd_discount_value'];
																	$rtwwdpd_max = $rul['rtwwdpd_max_discount'];

																	$price_adjust = $rtwwdpd_original_price * ($rtwwdpd_price / 100);

																	if($price_adjust > $rtwwdpd_max)
																	{
																		$price_adjust = $rtwwdpd_max;
																	}
																	$rtwwdpd_price_adjusted = $rtwwdpd_original_price - $price_adjust;

																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																	return;
																}
																else{
																	$rtwwdpd_price = $rul['rtwwdpd_discount_value'];
																	$rtwwdpd_max = $rul['rtwwdpd_max_discount'];

																	if($rtwwdpd_price <= $rtwwdpd_max)
																	{
																		$rtwwdpd_price_adjusted = $rtwwdpd_original_price - $rtwwdpd_price;
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																		return;
																	}
																	else{
																		$rtwwdpd_price = $rtwwdpd_max;
																		$rtwwdpd_price_adjusted = $rtwwdpd_original_price - $rtwwdpd_price;
																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																		return;
																	}
																}
															}
														}
														elseif($rul['rtwwdpd_check_for'] == 'rtwwdpd_price')
														{
															if( $rtwwdpd_cart_total >= $rul['rtwwdpd_min'] )
															{
																if( isset($rul['rtwwdpd_max']) && $rul['rtwwdpd_max'] < $rtwwdpd_cart_total )
																{
																	continue;
																}
																if ( ! $this->rtwwdpd_is_cumulative( $cart_item, $cart_item_key ) ) 
																{
																	if ( $this->rtwwdpd_is_item_discounted( $cart_item, $cart_item_key ) && apply_filters( 'rtwwdpd_stack_order_totals', false ) === false ) {
																		continue;
																	}
																}
																$rtwwdpd_discounted = isset( WC()->cart->cart_contents[ $cart_item_key ]['discounts'] );

																if ($rtwwdpd_discounted){
																	$d = WC()->cart->cart_contents[ $cart_item_key ]['discounts'];
																	if (in_array('advanced_totals', $d['by'])) {
																		continue;
																	}
																}
																$rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $rule['amount'], $rule, $cart_item, $this );

																$rtwwdpd_original_price = $this->rtw_get_price_to_discount( $cart_item, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ) );

																if($rul['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
																{
																	$rtwwdpd_price = $rul['rtwwdpd_discount_value'];
																	$rtwwdpd_max = $rul['rtwwdpd_max_discount'];
																	$price_adjust = $rtwwdpd_original_price * ($rtwwdpd_price / 100);

																	if($price_adjust > $rtwwdpd_max)
																	{
																		$price_adjust = $rtwwdpd_max;
																	}

																	$rtwwdpd_price_adjusted = $rtwwdpd_original_price - $price_adjust;

																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																	return;
																}
																else{
																	$rtwwdpd_price = $rul['rtwwdpd_discount_value'];
																	$rtwwdpd_max = $rul['rtwwdpd_max_discount'];

																	if($rtwwdpd_price <= $rtwwdpd_max)
																	{
																		$rtwwdpd_price_adjusted = $rtwwdpd_original_price - $rtwwdpd_price;

																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																		return;
																	}
																}
															}
														}
														else{
															$rtwwdpd_weight = $cart_item['data']->get_weight();
															if(isset($rtwwdpd_weight) && $cart_item['data']->get_weight() >= $rul['rtwwdpd_min'])
															{
																if( isset($rul['rtwwdpd_max']) && $rul['rtwwdpd_max'] < $cart_item['data']->get_weight() )
																{
																	continue;
																}
																if ( ! $this->rtwwdpd_is_cumulative( $cart_item, $cart_item_key ) ) 
																{
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
																$rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $rul['rtwwdpd_discount_value'], $rule, $cart_item, $this );

																$rtwwdpd_original_price = $this->rtw_get_price_to_discount( $cart_item, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ) );
																if($rul['rtwwdpd_discount_type'] == 'rtwwdpd_discount_percentage')
																{
																	$rtwwdpd_price = $rul['rtwwdpd_discount_value'];
																	$rtwwdpd_max = $rul['rtwwdpd_max_discount'];

																	$price_adjust = $rtwwdpd_original_price * ($rtwwdpd_price / 100);
																	if($price_adjust > $rtwwdpd_max)
																	{
																		$price_adjust = $rtwwdpd_max;
																	}

																	$rtwwdpd_price_adjusted = $rtwwdpd_original_price - $price_adjust;

																	Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																	return;
																}
																else{
																	$rtwwdpd_price = $rul['rtwwdpd_discount_value'];
																	$rtwwdpd_max = $rul['rtwwdpd_max_discount'];

																	if($rtwwdpd_price <= $rtwwdpd_max)
																	{
																		$rtwwdpd_price_adjusted = $rtwwdpd_original_price - $rtwwdpd_price;

																		Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_product_rule_adj( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
																		return;
																	}
																}
															}
														}
													}
												}
											}
											$it++;
										}
									}
									$i++;
								}
							}
							}

							//////////////// best matched disount ///////////////
							elseif ($rtwwdpd_setting_pri['rtw_offer_select'] == 'rtw_best_discount') {
								include( RTWWDPD_DIR.'public/partials/rtwwdpd_applied_method/rtwwdpd_best_match.php');
							}
						}
					}

					//Only process the first matched rule set
					if ( $rtwwdpd_matched && apply_filters( 'rtwwdpd_stack_order_totals', false ) === false ) {
						return;
					}
					// $rtwwdpd_pricing_rules++;
				}
			}
		}
	}

	/**
	 * Function to get cart total payable amount.
	 *
	 * @since    1.0.0
	 */
	private function rtwwdpd_get_cart_total( $rtwwdpd_set ) {
		global $woocommerce;
		$rtwwdpd_quantity  = 0;
		if( is_array(WC()->cart->cart_contents) && !empty(WC()->cart->cart_contents))
		{
			foreach ( WC()->cart->cart_contents as $cart_item ) {
				$product = $cart_item['data'];
				if ( $collector['type'] == 'cat' ) {

					if ( ! isset( $collector['args'] ) ) {
						return 0;
					}

					$rtwwdpd_terms = $this->rtwwdpd_get_product_category_ids( $product );
					if ( count( array_intersect( $collector['args']['cats'], $rtwwdpd_terms ) ) > 0 ) {

						$rtwwdpd_q = $cart_item['quantity'] ? $cart_item['quantity'] : 1;

						if ( isset( $cart_item['discounts'] ) && isset( $cart_item['discounts']['by'] ) && $cart_item['discounts']['by'][0] == $this->module_id ) {
							$rtwwdpd_quantity += floatval( $cart_item['discounts']['price_base'] ) * $rtwwdpd_q;
						} else {
							$rtwwdpd_quantity += $cart_item['data']->get_price() * $rtwwdpd_q;
						}
					}
				} else {
					$rtwwdpd_process_discounts = apply_filters( 'rtwwdpd_process_product_discounts', true, $cart_item['data'], 'advanced_totals', $this, $cart_item );


					if ( $rtwwdpd_process_discounts ) {
						$rtwwdpd_q = $cart_item['quantity'] ? $cart_item['quantity'] : 1;

						if ( isset( $cart_item['discounts'] ) && isset( $cart_item['discounts']['by'] ) && $cart_item['discounts']['by'] == $this->module_id ) {
							$rtwwdpd_quantity += floatval( $cart_item['discounts']['price_base'] ) * $rtwwdpd_q;
						} else {
							$rtwwdpd_quantity += $cart_item['data']->get_price() * $rtwwdpd_q;
						}
					}
				}
			}
		}

		return $rtwwdpd_quantity;
	}

	/**
	 * Function to get product category ids.
	 *
	 * @since    1.0.0
	 */
	public static function rtwwdpd_get_product_category_ids( $rtwwdpd_product ) {
		if ( empty( $rtwwdpd_product ) ) {
			return array();
		}

		$rtwwdpd_id    = isset( $rtwwdpd_product->variation_id ) ? $rtwwdpd_product->parent->get_id() : $rtwwdpd_product->get_id();
		$rtwwdpd_terms = wp_get_post_terms( $rtwwdpd_id, 'product_cat', array( 'fields' => 'ids' ) );

		return $rtwwdpd_terms;
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

	/**
	 * Function to perform shipping discounting rules.
	 *
	 * @since    1.0.0
	 */
	function rtwwdpd_shipping_method( $rtwwdpd_temp_cart ){
		global $woocommerce;
		$i = 0;
		$rtwwdpd_today_date = current_time('Y-m-d');
		$rtwwdpd_pay_rul = get_option('rtwwdpd_ship_method');

		$rtwwdpd_matched = true;
		if($rtwwdpd_pay_rul[$i]['rtwwdpd_ship_from_date'] > $rtwwdpd_today_date || $rtwwdpd_pay_rul[$i]['rtwwdpd_ship_to_date'] < $rtwwdpd_today_date)
		{
			return false;
		}
		if( is_array($rtwwdpd_temp_cart) && !empty($rtwwdpd_temp_cart) )
		{
			foreach ( $rtwwdpd_temp_cart as $cart_item_key => $cart_item ) {
				$rtwwdpd_product = $cart_item['data'];
				if ( $collector['type'] == 'cat' ) {
					$rtwwdpd_process_discounts = false;
					$rtwwdpd_terms             = $this->rtwwdpd_get_product_category_ids( $rtwwdpd_product );
					if ( count( array_intersect( $targets, $rtwwdpd_terms ) ) > 0 ) {
						$rtwwdpd_process_discounts = apply_filters( 'rtwwdpd_process_product_discounts', true, $cart_item['data'], 'advanced_totals', $this, $cart_item );
					}
				} else {
					$rtwwdpd_process_discounts = apply_filters( 'rtwwdpd_process_product_discounts', true, $cart_item['data'], 'advanced_totals', $this, $cart_item );
				}

				if ( ! $rtwwdpd_process_discounts ) {
					return false;
				}

				if ( ! $this->rtwwdpd_is_cumulative( $cart_item, $cart_item_key ) ) {

					if ( $this->rtwwdpd_is_item_discounted( $cart_item, $cart_item_key ) && apply_filters( 'rtwwdpd_stack_order_totals', false ) === false ) {
						return false;
					}
				}

				$rtwwdpd_discounted = isset( WC()->cart->cart_contents[ $cart_item_key ]['discounts'] );

				if ($rtwwdpd_discounted){
					$rtwwdpd_d = WC()->cart->cart_contents[ $cart_item_key ]['discounts'];
					if (in_array('advanced_totals', $rtwwdpd_d['by'])) {
						return false;
					}
				}

				$rtwwdpd_original_price = $this->rtw_get_price_to_discount( $cart_item, $cart_item_key, apply_filters( 'rtwwdpd_stack_order_totals', false ) );
				if ( $rtwwdpd_original_price ) {
					$rtwwdpd_amount = apply_filters( 'rtwwdpd_get_rule_amount', $rule['amount'], $rule, $cart_item, $this );

					$rtwwdpd_cart_prod_count = count( WC()->cart->get_cart());
					$rtwwdpd_cart_total = $woocommerce->cart->get_subtotal();

					$rtwwdpd_ship_chosen_method = WC()->session->get( 'chosen_shipping_methods' );
					$rtwwdpd_dscnt_on = $rtwwdpd_pay_rul[$i]['allowed_shipping_methods'][0];

					$pos = stripos($rtwwdpd_ship_chosen_method[0], $rtwwdpd_dscnt_on);

					if($pos !== false)
					{
						if($rtwwdpd_pay_rul[$i]['rtwwdpd_min_prod_cont'] <= $rtwwdpd_cart_prod_count && $rtwwdpd_pay_rul[$i]['rtwwdpd_min_spend'] <= $rtwwdpd_cart_total)
						{
							if($set->pricing_rules[$i]['type'] == 'rtwwdpd_discount_percentage')
							{
								$rtwwdpd_amount = $rtwwdpd_amount / 100;

								$rtwwdpd_price_adjusted = round( floatval( $rtwwdpd_original_price ) - ( floatval( $rtwwdpd_amount ) * $rtwwdpd_original_price ), (int) $rtwwdpd_num_decimals );
                   
								Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
							}
							else{

								$rtwwdpd_new_price = $rtwwdpd_amount/$rtwwdpd_cart_prod_count;

								$rtwwdpd_price_adjusted = $rtwwdpd_original_price - $rtwwdpd_new_price;
                     
								Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_Public::rtw_apply_cart_item_adjustment( $cart_item_key, $rtwwdpd_original_price, $rtwwdpd_price_adjusted, $this->module_id, $set_id );
							}
						}
					}
				}
			}
		}
	}
}