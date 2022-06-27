<?php

class WC_Dynamic_Pricing_Advanced_Totals extends WC_Dynamic_Pricing_Advanced_Base {

	private static $instance;

	public static function instance() {
		if ( self::$instance == null ) {
			self::$instance = new WC_Dynamic_Pricing_Advanced_Totals( 'advanced_totals' );
		}

		return self::$instance;
	}

	public $adjustment_sets;

	public function __construct( $module_id ) {
		parent::__construct( $module_id );

		$sets = get_option( '_a_totals_pricing_rules' );
		if ( $sets && is_array( $sets ) && sizeof( $sets ) > 0 ) {
			foreach ( $sets as $id => $set_data ) {
				$obj_adjustment_set           = new WC_Dynamic_Pricing_Adjustment_Set_Totals( $id, $set_data );
				$this->adjustment_sets[ $id ] = $obj_adjustment_set;
			}
		}
	}

	public function adjust_cart( $temp_cart ) {
		$num_decimals = apply_filters( 'woocommerce_dynamic_pricing_get_decimals', (int) get_option( 'woocommerce_price_num_decimals' ) );

		if ( $this->adjustment_sets && count( $this->adjustment_sets ) ) {
			foreach ( $this->adjustment_sets as $set_id => $set ) {
				$q = $this->get_cart_total( $set );

				$matched           = false;
				$pricing_rules     = $set->pricing_rules;
				$is_valid_for_user = $set->is_valid_for_user();
				$collector         = $set->get_collector();
				$targets           = $set->targets;
				if ( $is_valid_for_user && is_array( $pricing_rules ) && sizeof( $pricing_rules ) > 0 ) {
					foreach ( $pricing_rules as $rule ) {
						if ( $rule['from'] == '*' ) {
							$rule['from'] = 0;
						}

						if ( empty( $rule['to'] ) || $rule['to'] == '*' ) {
							$rule['to'] = $q;
						}

						if ( $q >= $rule['from'] && $q <= $rule['to'] ) {

							$matched = true;

							//Adjust the cart items.
							foreach ( $temp_cart as $cart_item_key => $cart_item ) {
								$product = $cart_item['data'];
								if ( $collector['type'] == 'cat' ) {
									$process_discounts = false;
									if ( $this->is_applied_to_product( $product, $targets ) ) {
										$process_discounts = apply_filters( 'woocommerce_dynamic_pricing_process_product_discounts', true, $cart_item['data'], 'advanced_totals', $this, $cart_item );
									}
								} else {
									$process_discounts = apply_filters( 'woocommerce_dynamic_pricing_process_product_discounts', true, $cart_item['data'], 'advanced_totals', $this, $cart_item );
								}

								if ( ! $process_discounts ) {
									continue;
								}

								if ( ! $this->is_cumulative( $cart_item, $cart_item_key ) ) {
									if ( $this->is_item_discounted( $cart_item, $cart_item_key ) && apply_filters( 'wc_dynamic_pricing_stack_order_totals', false ) === false ) {
										continue;
									}
								}

								$discounted = isset( WC()->cart->cart_contents[ $cart_item_key ]['discounts'] );
								if ( $discounted ) {
									$d = WC()->cart->cart_contents[ $cart_item_key ]['discounts'];
									if ( in_array( 'advanced_totals', $d['by'] ) ) {
										continue;
									}
								}

								$original_price = $this->get_price_to_discount( $cart_item, $cart_item_key, apply_filters( 'wc_dynamic_pricing_stack_order_totals', false ) );

								if ( $original_price ) {
									$amount = apply_filters( 'woocommerce_dynamic_pricing_get_rule_amount', $rule['amount'], $rule, $cart_item, $this );
									$amount = $amount / 100;

									if ( $amount > 1 ) {
										$price_adjusted = round(floatval( $original_price )  + ( ( floatval( $amount ) * $original_price) - floatval( $original_price)), (int) $num_decimals );
									} else {
										$price_adjusted = round( floatval( $original_price ) - ( floatval( $amount ) * $original_price ), (int) $num_decimals );
									}

									WC_Dynamic_Pricing::apply_cart_item_adjustment( $cart_item_key, $original_price, $price_adjusted, $this->module_id, $set_id );
								}
							}
						}
					}
				}

				//Only process the first matched rule set
				if ( $matched && apply_filters( 'wc_dynamic_pricing_stack_order_totals', false ) === false ) {
					return;
				}
			}
		}
	}


	private function is_applied_to_product( $product, $targets ) {
		if ( is_admin() && ! wp_doing_ajax() ) {
			return false;
		}

		$terms = $this->get_product_category_ids( $product );

		$process_discounts = count( array_intersect( $targets, $terms ) ) > 0;

		return apply_filters( 'woocommerce_dynamic_pricing_is_applied_to', $process_discounts, $product, $this->module_id, $this, $targets );
	}

	private function get_cart_total( $set ) {
		global $woocommerce;
		$collector = $set->get_collector();
		$quantity  = 0;
		foreach ( WC()->cart->cart_contents as $cart_item ) {
			$product = $cart_item['data'];
			if ( $collector['type'] == 'cat' ) {

				if ( ! isset( $collector['args'] ) ) {
					return 0;
				}

				if ( $this->is_applied_to_product( $product, $collector['args']['cats'] ) ) {

					$q = $cart_item['quantity'] ? $cart_item['quantity'] : 1;

					if ( isset( $cart_item['discounts'] ) && isset( $cart_item['discounts']['by'] ) && $cart_item['discounts']['by'][0] == $this->module_id ) {
						$quantity += floatval( $cart_item['discounts']['price_base'] ) * $q;
					} else {
						$quantity += $cart_item['data']->get_price() * $q;
					}
				}
			} else {
				$process_discounts = apply_filters( 'woocommerce_dynamic_pricing_process_product_discounts', true, $cart_item['data'], 'advanced_totals', $this, $cart_item );
				if ( $process_discounts ) {
					$q = $cart_item['quantity'] ? $cart_item['quantity'] : 1;

					if ( isset( $cart_item['discounts'] ) && isset( $cart_item['discounts']['by'] ) && $cart_item['discounts']['by'] == $this->module_id ) {
						$quantity += floatval( $cart_item['discounts']['price_base'] ) * $q;
					} else {
						$quantity += $cart_item['data']->get_price() * $q;
					}
				}
			}
		}

		return $quantity;
	}

}
