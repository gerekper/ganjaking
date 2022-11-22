<?php

class WC_Dynamic_Pricing_Simple_Category extends WC_Dynamic_Pricing_Simple_Base {

	private static $instance;

	public static function instance() {
		if ( self::$instance == null ) {
			self::$instance = new WC_Dynamic_Pricing_Simple_Category( 'simple_category' );
		}

		return self::$instance;
	}

	public $available_advanced_rulesets = array();

	public function __construct( $module_id ) {
		parent::__construct( $module_id );
	}

	public function initialize_rules() {
		$pricing_rule_sets = get_option( '_s_category_pricing_rules', array() );

		if ( is_array( $pricing_rule_sets ) && sizeof( $pricing_rule_sets ) > 0 ) {
			foreach ( $pricing_rule_sets as $set_id => $pricing_rule_set ) {
				$execute_rules      = false;
				$conditions_met     = 0;
				$pricing_conditions = $pricing_rule_set['conditions'] ?? [];
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

				if ( $execute_rules && isset( $pricing_rule_set['collector']['args']['cats'][0] ) ) {
					$this->available_rulesets[ $set_id ] = $pricing_rule_set;
				}
			}
		}

		$pricing_rule_sets = get_option( '_a_category_pricing_rules', array() );
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

				if ( $execute_rules && ( isset( $pricing_rule_set['date_from'] ) || isset( $pricing_rule_set['date_to'] ) ) ) {
					$execute_rules = wc_dynamic_pricing_is_within_date_range( $pricing_rule_set['date_from'], $pricing_rule_set['date_to'] );
				}

				if ( $execute_rules && isset( $pricing_rule_set['collector']['args']['cats'][0] ) ) {
					$this->available_advanced_rulesets[ $set_id ] = $pricing_rule_set;
				}
			}
		}
	}

	public function adjust_cart( $cart ) {

		if ( $this->available_rulesets && count( $this->available_rulesets ) ) {

			foreach ( $cart as $cart_item_key => $cart_item ) {
				$process_discounts = apply_filters( 'woocommerce_dynamic_pricing_process_product_discounts', true, $cart_item['data'], 'simple_category', $this, $cart_item );
				if ( ! $process_discounts ) {
					continue;
				}

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
				$applied_rule_set_id = null;

				foreach ( $this->available_rulesets as $set_id => $pricing_rule_set ) {

					if ( ! $this->is_cumulative( $cart_item, $cart_item_key ) ) {
						if ( $this->is_item_discounted( $cart_item, $cart_item_key, $set_id ) ) {
							continue;
						}
					}

					if ( $this->is_applied_to_product( $_product, $pricing_rule_set['collector']['args']['cats'][0] ) ) {
						$rule = $pricing_rule_set['rules'][0];

						$temp = $this->get_adjusted_price( $cart_item, $rule, $original_price );

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

	public function is_applied_to_product( $_product, $cat_id = false ) {

		if ( is_admin() && ! wp_doing_ajax() && apply_filters( 'woocommerce_dynamic_pricing_skip_admin', true ) ) {
			return false;
		}


		$cat_id = is_array( $cat_id ) ? $cat_id : array( $cat_id );

		$process_discounts = false;
		if ( ( isset( $this->available_rulesets ) && count( $this->available_rulesets ) > 0 ) || isset( $this->available_advanced_rulesets ) && count( $this->available_advanced_rulesets ) ) {
			if ( $cat_id ) {
				$product_categories = $this->get_product_category_ids( $_product );
				$process_discounts  = count( array_intersect( $cat_id, $product_categories ) ) > 0;
			}
		}


		return apply_filters( 'woocommerce_dynamic_pricing_is_applied_to', $process_discounts, $_product, $this->module_id, $this, $cat_id );
	}

	private function get_adjusted_price( $cart_item, $rule, $price ) {
		$result = false;

		$amount = apply_filters( 'woocommerce_dynamic_pricing_get_rule_amount', trim( $rule['amount'] ), $rule, null, $this );
		if ( $amount === null || $amount === "" || ! is_numeric( $amount ) ) {
			return false;
		}
		$amount = floatval( $amount );

		$num_decimals = apply_filters( 'woocommerce_dynamic_pricing_get_decimals', (int) get_option( 'woocommerce_price_num_decimals' ) );

		switch ( $rule['type'] ) {
			case 'price_discount':
			case 'fixed_product':
				$adjusted = floatval( $price ) - $amount;
				$result   = $adjusted >= 0 ? $adjusted : 0;
				break;
			case 'percentage_discount':
			case 'percent_product':
				$amount = $amount / 100;
				if ( $amount <= 1 ) {
					$result = round( floatval( $price ) - ( floatval( $amount ) * $price ), (int) $num_decimals );
				} else {
					$result = round( ( floatval( $amount ) * $price ), (int) $num_decimals );
				}

				break;
			case 'fixed_price':
				if ( isset( $cart_item['_gform_total'] ) ) {
					$amount += floatval( $cart_item['_gform_total'] );
				}

				if ( isset( $cart_item['addons_price_before_calc'] ) ) {
					$addons_total = floatval( $price ) - floatval( $cart_item['addons_price_before_calc'] );
					$amount       += $addons_total;
				}

				$result = round( $amount, (int) $num_decimals );
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
					if ( $condition['args']['applies_to'] == 'everyone' ) {
						$result = 1;
					} elseif ( $condition['args']['applies_to'] == 'unauthenticated' ) {
						if ( ! is_user_logged_in() ) {
							$result = 1;
						}
					} elseif ( $condition['args']['applies_to'] == 'authenticated' ) {
						if ( is_user_logged_in() ) {
							$result = 1;
						}
					} elseif ( $condition['args']['applies_to'] == 'roles' && isset( $condition['args']['roles'] ) && is_array( $condition['args']['roles'] ) ) {
						if ( is_user_logged_in() ) {
							foreach ( $condition['args']['roles'] as $role ) {
								if ( current_user_can( $role ) ) {
									$result = 1;
									break;
								}
							}
						}
					} elseif ( $condition['args']['applies_to'] == 'groups' && isset( $condition['args']['groups'] ) && is_array( $condition['args']['groups'] ) ) {
						if ( is_user_logged_in() && class_exists( 'Groups_User' ) ) {
							$groups_user = new Groups_User( get_current_user_id() );
							foreach ( $condition['args']['groups'] as $group ) {
								$current_group = Groups_Group::read( $group );
								if ( $current_group ) {
									if ( Groups_User_Group::read( $groups_user->user->ID, $current_group->group_id ) ) {
										$result = 1;
										break;
									}
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

	public function get_discounted_price_for_shop( $_product, $working_price ) {
		$fake_cart_item   = array( 'data' => $_product );
		$price_adjusted   = false;
		$applied_rule     = false;
		$applied_rule_set = false;

		$rulesets = $this->available_rulesets + $this->available_advanced_rulesets;

		if ( $rulesets && count( $rulesets ) ) {
			foreach ( $rulesets as $set_id => $pricing_rule_set ) {
				if ( ! isset( $pricing_rule_set['mode'] ) || ( isset( $pricing_rule_set['mode'] ) && $pricing_rule_set['mode'] != 'block' ) ) {
					$process_discounts = apply_filters( 'woocommerce_dynamic_pricing_process_product_discounts', true, $_product, 'simple_category', $this, $fake_cart_item );
					if ( $process_discounts ) {

						//3.1.19 - make sure the item is in the collectors in addition to the targets.
						$collectors = array_map( 'intval', $pricing_rule_set['collector']['args']['cats'] );
						//Grab targets from advanced category discounts so we properly show 0 based discounts for targets, not for the collector category values.
						$cats_to_check           = isset( $pricing_rule_set['targets'] ) ? array_map( 'intval', $pricing_rule_set['targets'] ) : array_map( 'intval', $pricing_rule_set['collector']['args']['cats'] );
						$product_is_in_collector = count( array_intersect( $collectors, $cats_to_check ) ) > 0;
						if ( $product_is_in_collector && $this->is_applied_to_product( $_product, $cats_to_check ) ) {
							$rule = array_shift( $pricing_rule_set['rules'] );

							if ( ! isset( $rule['from'] ) || empty( $rule['from'] ) ) {
								$rule['from'] = 0;
							}

							$show_pricing_in_shop = apply_filters( 'woocommerce_dynamic_pricing_show_adjustments_in_shop', ( $rule['from'] == '0' || $rule['from'] == '1' ), $rule, $_product );
							if ( $show_pricing_in_shop ) {
								$temp = $this->get_adjusted_price( $fake_cart_item, $rule, $working_price );

								if ( ! $price_adjusted || $temp < $price_adjusted ) {
									$price_adjusted = $temp;

									//return $price_adjusted;//Only process first rule, @since 3.1.7
								}
							}
						}
					}
				}
			}

			if ( $price_adjusted !== false && floatval( $working_price ) != floatval( $price_adjusted ) ) {
				return $price_adjusted;
			}
		}

		return $working_price;
	}

}
