<?php

class WC_Dynamic_Pricing_Simple_Membership extends WC_Dynamic_Pricing_Simple_Base {

	private static $instance;

	public static function instance() {
		if ( self::$instance == null ) {
			self::$instance = new WC_Dynamic_Pricing_Simple_Membership( 'simple_membership' );
		}

		return self::$instance;
	}

	private $_loaded_product_rules;//used to cache product rules

	public function __construct( $module_id ) {
		parent::__construct( $module_id );
	}

	public function initialize_rules() {
		$pricing_rule_sets = get_option( '_s_membership_pricing_rules', array() );

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
				$process_discounts = apply_filters( 'woocommerce_dynamic_pricing_process_product_discounts', true, $cart_item['data'], 'simple_membership', $this, $cart_item );

				if ( $is_applied && $process_discounts ) {
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
					$applied_rule_set_id = false;

					foreach ( $this->available_rulesets as $set_id => $pricing_rule_set ) {

						if ( ! $this->is_cumulative( $cart_item, $cart_item_key ) ) {
							if ( $this->is_item_discounted( $cart_item, $cart_item_key, $set_id ) ) {
								continue;
							}
						}

						if ( $this->is_applied_to_product( $_product ) ) {
							$rule = $pricing_rule_set;

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
	}

	public function is_applied_to_product( $_product ) {
		if ( is_admin() && ! wp_doing_ajax() && apply_filters( 'woocommerce_dynamic_pricing_skip_admin', true ) ) {
			return false;
		}


		return true; //all products are eligibile for the discount.  Only eligibile rulesets for this user have been loaded.
	}

	private function get_adjusted_price( $cart_item, $rule, $price ) {
		$result = $price;

		$amount       = apply_filters( 'woocommerce_dynamic_pricing_get_rule_amount', $rule['amount'], $rule, null, $this );
		$num_decimals = apply_filters( 'woocommerce_dynamic_pricing_get_decimals', (int) get_option( 'woocommerce_price_num_decimals' ) );

		switch ( $rule['type'] ) {
			case 'fixed_product':
				$adjusted = floatval( $price ) - floatval( $amount );
				$result   = $adjusted >= 0 ? $adjusted : 0;
				break;
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
					$addons_total = $price - $cart_item['addons_price_before_calc'];
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
					}
				}
				break;
			default:
				break;
		}

		$result = apply_filters( 'woocommerce_dynamic_pricing_is_rule_set_valid_for_user', $result, $condition, $this );

		return $result;
	}

	/**
	 * Gets the discounted price for the shop.
	 *
	 * @param WC_Product $_product
	 * @param float      $working_price
	 *
	 * @return bool|float|int|null
	 */
	public function get_discounted_price_for_shop( $_product, $working_price, $additional_price = false ) {

		$fake_cart_item  = array( 'data' => $_product );
		$a_working_price = apply_filters( 'woocommerce_dyanmic_pricing_working_price', $working_price, 'advanced_product', $fake_cart_item );

		$lowest_price         = false;
		$applied_rule         = null;
		$applied_to_variation = false;


		//Need to process product rules that might have a 0 based quantity.

		if ( $_product->get_type() == 'variation' || $_product->get_type() == 'subscription_variation' ) {
			if ( WC_Dynamic_Pricing_Compatibility::is_wc_version_gte_2_7() ) {
				if ( ! isset( $this->_loaded_product_rules[ $_product->get_parent_id() ] ) ) {
					$pricing_rule_sets                                         = apply_filters( 'dynamic_pricing_product_rules', WC_Dynamic_Pricing_Compatibility::get_product_meta( wc_get_product( $_product->get_parent_id() ), '_pricing_rules' ) );
					$this->_loaded_product_rules[ $_product->get_parent_id() ] = $pricing_rule_sets;
				}
				$pricing_rule_sets = $this->_loaded_product_rules[ $_product->get_parent_id() ];
			} else {
				$pricing_rule_sets = apply_filters( 'dynamic_pricing_product_rules', WC_Dynamic_Pricing_Compatibility::get_product_meta( wc_get_product( $_product->parent->id ), '_pricing_rules' ) );
			}
		} else {
			if ( ! isset( $this->_loaded_product_rules[ $_product->get_id() ] ) ) {
				$pricing_rule_sets                                  = apply_filters( 'dynamic_pricing_product_rules', WC_Dynamic_Pricing_Compatibility::get_product_meta( $_product, '_pricing_rules' ) );
				$this->_loaded_product_rules[ $_product->get_id() ] = $pricing_rule_sets;
			}

			$pricing_rule_sets = $this->_loaded_product_rules[ $_product->get_id() ];
		}


		if ( is_array( $pricing_rule_sets ) && sizeof( $pricing_rule_sets ) > 0 ) {
			foreach ( $pricing_rule_sets as $pricing_rule_set ) {
				$execute_rules        = false;
				$conditions_met       = 0;
				$variation_id         = 0;
				$variation_rules      = isset( $pricing_rule_set['variation_rules'] ) ? $pricing_rule_set['variation_rules'] : '';
				$applied_to_variation = $variation_rules && isset( $variation_rules['args']['type'] ) && $variation_rules['args']['type'] == 'variations';

				if ($variation_rules && isset( $variation_rules['args']['type'] ) &&  $variation_rules['args']['type'] == 'product') {
					$variation_rules = '';
					$applied_to_variation = false;
				}

				/** Commented out the is_single in 2.9.8 **/
				//if ( is_single() ) {
				if ( $applied_to_variation && ( $_product->is_type( 'variable' ) || $_product->is_type( 'variation' ) ) && $variation_rules ) {
					if ( isset( $variation_rules['args']['type'] ) && $variation_rules['args']['type'] == 'variations' && isset( $variation_rules['args']['variations'] ) && count( $variation_rules['args']['variations'] ) ) {
						if ( ! in_array( $_product->get_id(), $variation_rules['args']['variations'] ) ) {
							continue;
						} else {
							$variation_id = $_product->get_id();
						}
					}
				}
				//} else {
				//$applied_to_variation = false;
				//}

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

				if ($execute_rules) {
					$quantity = 0;
					if (isset($pricing_rule_set['collector']) && $pricing_rule_set['collector']['type'] == 'cat' ) {
						$collector = $pricing_rule_set['collector'];
						if ( isset( $collector['args'] ) && isset( $collector['args']['cats'] ) && is_array( $collector['args']['cats'] ) ) {

							if ( isset( $collector['args'] ) && isset( $collector['args']['cats'] ) && is_array( $collector['args']['cats'] ) ) {

								if ( is_object_in_term( $_product->get_id(), 'product_cat', $collector['args']['cats'] ) ) {
									$quantity += 1;
								}

								$temp_cart = WC_Dynamic_Pricing_Compatibility::WC()->cart->cart_contents;
								foreach ( $temp_cart as $lck => $check_cart_item ) {
									if ( is_object_in_term( $check_cart_item['product_id'], 'product_cat', $collector['args']['cats'] ) ) {
										$quantity += (int) $check_cart_item['quantity'];
									}
								}
							}
						}

						$execute_rules = $quantity > 0;
					}


				}

				if ( $execute_rules ) {
					$pricing_rules = $pricing_rule_set['rules'];
					if ( is_array( $pricing_rules ) && sizeof( $pricing_rules ) > 0 ) {
						foreach ( $pricing_rules as $rule ) {

							if ( ! isset( $rule['from'] ) || empty( $rule['from'] ) ) {
								$rule['from'] = 0;
							}

							$show_pricing_in_shop = apply_filters( 'woocommerce_dynamic_pricing_show_adjustments_in_shop', ( $rule['from'] == '0' || $rule['from']  == '1'), $rule, $_product );
							if ( $show_pricing_in_shop ) {

								//first rule matched takes precedence for the item.
								if ( ! $applied_rule ) {
									if ( $applied_to_variation && $variation_id ) {
										$applied_rule = $rule;
									} elseif ( ! $applied_to_variation ) {
										$applied_rule = $rule;
									}
								}

								//calcualte the lowest price for display
								$price = $this->get_adjusted_price_by_product_rule( $rule, $a_working_price, $_product, $additional_price );
								if ( ($price === 0.0 || $price ) && !$lowest_price ) {
									$lowest_price = $price;
								} elseif ( ($price === 0.0 || $price ) && $price < $lowest_price ) {
									$lowest_price = $price;
								}
							}
						}
					}
				}
			}
		}

		$process_discounts = apply_filters( 'woocommerce_dynamic_pricing_process_product_discounts', true, $fake_cart_item['data'], 'simple_product', $this, $fake_cart_item );
		if ( $process_discounts ) {
			if ( ! $this->is_cumulative( $fake_cart_item, false ) ) {

				//$product_class = get_class( $_product );
				if ( ($_product->is_type( 'variable' ) || $_product->is_type( 'variation' )) && ($lowest_price || $lowest_price === 0.0) ) {
					return $lowest_price;
				} elseif ( $applied_rule ) {
					return $this->get_adjusted_price_by_product_rule( $applied_rule, $a_working_price, $_product, $additional_price );
				} elseif ( $this->available_rulesets && count( $this->available_rulesets ) ) {
					$available_rule = reset( $this->available_rulesets );

					$s_working_price = apply_filters( 'woocommerce_dyanmic_pricing_working_price', $working_price, 'membership', $fake_cart_item );

					return $this->get_adjusted_price( $fake_cart_item, $available_rule, $s_working_price );
				}
			} else {

				$discounted_price = false;
				if ( get_class( $_product ) == 'WC_Product' && $_product->is_type( 'variable' ) && $lowest_price ) {
					$discounted_price = $lowest_price;
				} elseif ( $applied_rule ) {
					$discounted_price = $this->get_adjusted_price_by_product_rule( $applied_rule, $a_working_price, $_product, $additional_price );
				}

				if ( $this->available_rulesets && count( $this->available_rulesets ) ) {
					$available_rule = reset( $this->available_rulesets );

					$s_working_price = apply_filters( 'woocommerce_dyanmic_pricing_working_price', $discounted_price ? $discounted_price : $working_price, 'membership', $fake_cart_item );

					return $this->get_adjusted_price( $fake_cart_item, $available_rule, $s_working_price );
				} else {
					return $discounted_price;
				}
			}
		}

		return $working_price === null ? false : $working_price;
	}

	private function get_adjusted_price_by_product_rule( $rule, $price, $_product, $additional_price = false ) {
		$result = false;

		$amount       = apply_filters( 'woocommerce_dynamic_pricing_get_rule_amount', $rule['amount'], $rule, null, $this );
		$num_decimals = apply_filters( 'woocommerce_dynamic_pricing_get_decimals', (int) get_option( 'woocommerce_price_num_decimals' ) );


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
				if ( $additional_price ) {
					$amount += floatval( $additional_price );
				}
				$fixed_price = round( $amount, (int) $num_decimals );
				//$result      = $tax_display_mode == 'incl' ? wc_get_price_including_tax( $_product, array( 'price' => $fixed_price ) ) : wc_get_price_excluding_tax( $_product, array( 'price' => $fixed_price ) );
				$result = $fixed_price;
				break;
			default:
				$result = false;
				break;
		}

		return $result;
	}
}
