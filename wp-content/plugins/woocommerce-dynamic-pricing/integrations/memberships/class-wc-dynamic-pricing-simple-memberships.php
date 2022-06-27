<?php

class WC_Dynamic_Pricing_Simple_Membership extends WC_Dynamic_Pricing_Simple_Base {

	private static $instance;

	public static function instance() {
		if ( self::$instance == null ) {
			self::$instance = new WC_Dynamic_Pricing_Simple_Group( 'simple_membership' );
		}

		return self::$instance;
	}

	public function __construct( $module_id ) {
		parent::__construct( $module_id );
	}

	public function initialize_rules() {
		$pricing_rule_sets = get_option( '_s_group_pricing_rules', array() );

		if ( is_array( $pricing_rule_sets ) && sizeof( $pricing_rule_sets ) > 0 ) {
			foreach ( $pricing_rule_sets as $set_id => $pricing_rule_set ) {
				$execute_rules      = false;
				$conditions_met     = 0;
				$pricing_conditions = $pricing_rule_set['conditions'];
				if ( is_array( $pricing_conditions ) && sizeof( $pricing_conditions ) > 0 ) {
					foreach ( $pricing_conditions as $condition ) {
						$conditions_met += $this->handle_condition( $condition );
					}
					if ( $pricing_rule_set['conditions_type'] == 'all' ) {
						$execute_rules = $conditions_met == count( $pricing_conditions );
					} elseif ( $pricing_rule_set['conditions_type'] == 'any' ) {
						$execute_rules = $conditions_met > 0;
					}
				} else {
					//empty conditions - default match, process price adjustment rules
					$execute_rules = true;
				}

				if ( $execute_rules ) {
					$this->available_rulesets[ $set_id ] = $pricing_rule_set['rules'][0];
				}
			}
		}
	}

	public function adjust_cart( $cart ) {

		if ( $this->available_rulesets && count( $this->available_rulesets ) ) {


			foreach ( $cart as $cart_item_key => $cart_item ) {

				$is_applied        = apply_filters( 'woocommerce_dynamic_pricing_is_applied_to_product', $this->is_applied_to_product( $cart_item['data'] ), $this->module_id, $this );
				$process_discounts = apply_filters( 'woocommerce_dynamic_pricing_process_product_discounts', true, $cart_item['data'], 'groups', $this, $cart_item );
				if ( ! $process_discounts ) {
					continue;
				}

				if ( $is_applied ) {
					if ( ! $this->is_cumulative( $cart_item, $cart_item_key ) ) {

						if ( $this->is_item_discounted( $cart_item, $cart_item_key ) ) {
							continue;
						}
					}

					$original_price = $this->get_price_to_discount( $cart_item, $cart_item_key );

					$_product            = $cart_item['data'];
					$price_adjusted      = false;
					$applied_rule        = false;
					$applied_rule_set    = false;
					$applied_rule_set_id = 0;

					foreach ( $this->available_rulesets as $set_id => $pricing_rule_set ) {

						if ( $this->is_applied_to_product( $_product ) ) {
							$rule = $pricing_rule_set;

							$temp = $this->get_adjusted_price( $rule, $original_price );

							if ( ! $price_adjusted || $temp < $price_adjusted ) {
								$price_adjusted      = $temp;
								$applied_rule        = $rule;
								$applied_rule_set    = $pricing_rule_set;
								$applied_rule_set_id = $set_id;
							}
						}
					}

					if ( $price_adjusted !== false && floatval( $original_price ) != floatval( $price_adjusted ) ) {
						WC_Dynamic_Pricing::apply_cart_item_adjustment( $cart_item_key, $original_price, $price_adjusted, $this->module_id, $applied_rule_set_id );
					}
				}
			}
		}
	}

	public function is_applied_to_product( $_product ) {
		if ( is_admin() && ! wp_doing_ajax() ) {
			return false;
		}

		return true; //all products are eligibile for the discount.  Only eligibile rulesets for this user have been loaded.
	}

	private function get_adjusted_price( $rule, $price ) {
		$num_decimals = apply_filters( 'woocommerce_dynamic_pricing_get_decimals', (int) get_option( 'woocommerce_price_num_decimals' ) );

		switch ( $rule['type'] ) {
			case 'fixed_product':
				$adjusted = floatval( $price ) - floatval( $rule['amount'] );
				$result   = $adjusted >= 0 ? $adjusted : 0;
				break;
			case 'percent_product':
				if ( $rule['amount'] > 1 ) {
					$rule['amount'] = $rule['amount'] / 100;
				}
				$result = round( floatval( $price ) - ( floatval( $rule['amount'] ) * $price ), (int) $num_decimals );
				break;
			case 'fixed_price':
				$result = round( $rule['amount'], (int) $num_decimals );
				break;
			default:
				$result = false;
				break;
		}

		return $result;
	}

	private function handle_condition( $condition ) {
		$result = 0;

		switch ( $condition['type'] ) {
			case 'apply_to':
				if ( is_array( $condition['args'] ) && isset( $condition['args']['applies_to'] ) ) {
					if ( $condition['args']['applies_to'] == 'membership' && isset( $condition['args']['memberships'] ) && is_array( $condition['args']['memberships'] ) ) {
						if ( is_user_logged_in() ) {
							foreach ( $condition['args']['memberships'] as $plan_id ) {
								if ( wc_memberships_is_user_active_member( get_current_user_id(), $plan_id ) ) {
									$result = 1;
									break;
								}
							}
						}
					}
				}
				break;
			default:
				break;
		}

		return $result;
	}


	//Gets the price for the shop
	public function get_discounted_price_for_shop( $_product, $working_price ) {

		$fake_cart_item  = array( 'data' => $_product );
		$a_working_price = apply_filters( 'woocommerce_dyanmic_pricing_working_price', $working_price, 'advanced_product', $fake_cart_item );

		$lowest_price         = false;
		$applied_rule         = null;
		$applied_to_variation = false;

		$process_discounts = apply_filters( 'woocommerce_dynamic_pricing_process_product_discounts', true, $fake_cart_item['data'], 'memberships', $this, $fake_cart_item );
		if ( $process_discounts ) {
			if ( ! $this->is_cumulative( $fake_cart_item, false ) ) {

				if ( get_class( $_product ) == 'WC_Product' && $_product->is_type( 'variable' ) && $lowest_price ) {
					return $lowest_price;
				} elseif ( $applied_rule ) {
					return $this->get_adjusted_price_by_product_rule( $applied_rule, $a_working_price, $_product );
				} elseif ( $this->available_rulesets && count( $this->available_rulesets ) ) {
					$available_rule = reset( $this->available_rulesets );

					$s_working_price = apply_filters( 'woocommerce_dyanmic_pricing_working_price', $working_price, 'memberships', $fake_cart_item );

					return $this->get_adjusted_price( $available_rule, $s_working_price );
				}
			} else {

				$discounted_price = null;
				if ( get_class( $_product ) == 'WC_Product' && $_product->is_type( 'variable' ) && $lowest_price ) {
					$discounted_price = $lowest_price;
				} elseif ( $applied_rule ) {
					$discounted_price = $this->get_adjusted_price_by_product_rule( $applied_rule, $a_working_price, $_product );
				}

				if ( $this->available_rulesets && count( $this->available_rulesets ) ) {
					$available_rule = reset( $this->available_rulesets );

					$s_working_price = apply_filters( 'woocommerce_dyanmic_pricing_working_price', $discounted_price, ' memberships', $fake_cart_item );

					return $this->get_adjusted_price( $available_rule, $s_working_price );
				} else {
					return $discounted_price;
				}
			}
		}

		return $working_price;
	}

	private function get_adjusted_price_by_product_rule( $rule, $price, $_product ) {
		$result = false;

		$amount       = apply_filters( 'woocommerce_dynamic_pricing_get_rule_amount', $rule['amount'], $rule, null, $this );
		$num_decimals = apply_filters( 'woocommerce_dynamic_pricing_get_decimals', (int) get_option( 'woocommerce_price_num_decimals' ) );

		$q = 0;

		if ( $rule['from'] == '*' ) {
			$rule['from'] = 0;
		}

		if ( $rule['to'] == '*' ) {
			$rule['to'] = $q;
		}

		if ( $q >= $rule['from'] && $q <= $rule['to'] ) {
			$this->discount_data['rule'] = $rule;

			switch ( $rule['type'] ) {
				case 'price_discount':
					$adjusted = floatval( $price ) - floatval( $amount );
					$result   = $adjusted >= 0 ? $adjusted : 0;
					break;
				case 'percentage_discount':
					$amount = $amount / 100;

					$result = round( floatval( $price ) - ( floatval( $amount ) * $price ), (int) $num_decimals );
					break;
				case 'fixed_price':

					$tax_display_mode = get_option( 'woocommerce_tax_display_shop' );
					$fixed_price      = round( $amount, (int) $num_decimals );
					$result           = $tax_display_mode == 'incl' ? wc_get_price_including_tax( $_product, array( 'price' => $fixed_price ) ) : wc_get_price_excluding_tax( $_product, array( 'price' => $fixed_price ) );

					break;
				default:
					$result = false;
					break;
			}
		}

		return $result;
	}

}
