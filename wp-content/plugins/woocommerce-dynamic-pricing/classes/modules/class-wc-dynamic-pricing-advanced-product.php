<?php

class WC_Dynamic_Pricing_Advanced_Product extends WC_Dynamic_Pricing_Advanced_Base {

	private static $instance;

	public static function instance() {
		if ( self::$instance == null ) {
			self::$instance = new WC_Dynamic_Pricing_Advanced_Product( 'advanced_product' );
		}

		return self::$instance;
	}

	private $used_rules = array();

	public function __construct( $module_id ) {
		parent::__construct( $module_id );
	}

	public function adjust_cart( $temp_cart ) {
		foreach ( $temp_cart as $cart_item_key => $values ) {
			$temp_cart[ $cart_item_key ]                       = $values;
			$temp_cart[ $cart_item_key ]['available_quantity'] = $values['quantity'];
			$temp_cart[ $cart_item_key ]['available_quantity'] = $values['quantity'];
		}


		foreach ( $temp_cart as $cart_item_key => $cart_item ) {
			$process_discounts = apply_filters( 'woocommerce_dynamic_pricing_process_product_discounts', true, $cart_item['data'], 'advanced_product', $this, $cart_item );
			if ( ! $process_discounts ) {
				continue;
			}

			if ( ! $this->is_cumulative( $cart_item, $cart_item_key ) ) {
				if ( $this->is_item_discounted( $cart_item, $cart_item_key ) ) {
					continue;
				}
			}

			$product_adjustment_sets = $this->get_pricing_rule_sets( $cart_item );
			if ( $product_adjustment_sets && count( $product_adjustment_sets ) ) {

				foreach ( $product_adjustment_sets as $set_id => $set ) {

					if ( $this->is_item_discounted( $cart_item, $cart_item_key, $set_id ) ) {
						continue;
					}

					if ( $set->target_variations && isset( $cart_item['variation_id'] ) && ! in_array( $cart_item['variation_id'], $set->target_variations ) ) {
						continue;
					}

					//check if this set is valid for the current user;
					$is_valid_for_user = $set->is_valid_for_user();

					if ( ! ( $is_valid_for_user ) ) {
						continue;
					}

					$original_price = $this->get_price_to_discount( $cart_item, $cart_item_key );
					if ( $original_price ) {
						$price_adjusted = false;
						if ( $set->mode == 'block' ) {
							$price_adjusted = $this->get_block_adjusted_price( $set, $original_price, $cart_item );
						} elseif ( $set->mode == 'bulk' ) {
							$price_adjusted = $this->get_adjusted_price( $set, $original_price, $cart_item );
						}

						if ( $price_adjusted !== false && floatval( $original_price ) != floatval( $price_adjusted ) ) {
							WC_Dynamic_Pricing::apply_cart_item_adjustment( $cart_item_key, $original_price, $price_adjusted, 'advanced_product', $set_id );
							//if (!apply_filters( 'woocommerce_dynamic_pricing_is_cumulative', false, $this->module_id, $cart_item, $cart_item_key )) {
							break;
							//}
						}
					}
				}
			}
		}
	}

	protected function get_pricing_rule_sets( $cart_item ) {

		$product = wc_get_product( $cart_item['product_id'] );

		if ( empty( $product ) ) {
			return false;
		}

		$pricing_rule_sets = apply_filters( 'wc_dynamic_pricing_get_product_pricing_rule_sets', WC_Dynamic_Pricing_Compatibility::get_product_meta( $product, '_pricing_rules' ), $product->get_id(), $this );
		$pricing_rule_sets = apply_filters( 'wc_dynamic_pricing_get_cart_item_pricing_rule_sets', $pricing_rule_sets, $cart_item );
		$sets              = array();
		if ( $pricing_rule_sets && is_array( $pricing_rule_sets ) ) {
			foreach ( $pricing_rule_sets as $set_id => $set_data ) {
				$sets[ $set_id ] = new WC_Dynamic_Pricing_Adjustment_Set_Product( $set_id, $set_data );
			}
		}

		return $sets;
	}

	protected function get_adjusted_price( $set, $price, $cart_item ) {
		$result = false;

		$pricing_rules = $set->pricing_rules;
		$collector     = $set->get_collector();
		$rule_set_id   = $set->set_id;

		if ( is_array( $pricing_rules ) && sizeof( $pricing_rules ) > 0 ) {
			foreach ( $pricing_rules as $rule ) {

				$q = $this->get_quantity_to_compare( $cart_item, $collector, $set );

				if ( $rule['from'] == '*' ) {
					$rule['from'] = 0;
				}

				if ( empty( $rule['to'] ) || $rule['to'] == '*' ) {
					$rule['to'] = $q;
				}

				$rule['from']   = floatval( $rule['from'] );
				$rule['to']     = floatval( $rule['to'] );
				$amount       = apply_filters( 'woocommerce_dynamic_pricing_get_rule_amount', $rule['amount'], $rule, $cart_item, $this );
				$amount       = floatval( $amount );

				if ( $q >= $rule['from'] && $q <= $rule['to'] ) {
					$this->discount_data['rule'] = $rule;

					$num_decimals = apply_filters( 'woocommerce_dynamic_pricing_get_decimals', (int) get_option( 'woocommerce_price_num_decimals' ) );
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

					break; //break out here only the first matched pricing rule will be evaluated.
				}
			}
		}

		return $result;
	}

	protected function get_block_adjusted_price( $set, $price, $cart_item ) {
		$result = false;

		$pricing_rules = $set->pricing_rules;
		$collector     = $set->get_collector();
		$rule_set_id   = $set->set_id;

		if ( is_array( $pricing_rules ) && sizeof( $pricing_rules ) > 0 ) {
			foreach ( $pricing_rules as &$rule ) {

				$q  = $this->get_quantity_to_compare( $cart_item, $collector, $set );
				$rq = 0; //required quantity to trigger the calculations

				$rule['to']     = floatval( $rule['to'] ?? 0 );
				$rule['from']   = floatval( $rule['from'] ?? 0 );
				$rule['adjust'] = floatval( $rule['adjust'] ?? 0 );
				$rule['amount'] = floatval( $rule['amount'] ?? 0 );

				if ( $collector['type'] == 'cart_item' && $q <= $rule['from'] ) {
					continue;
				}

				if ( $rule['repeating'] == 'yes' ) {
					switch ( $collector['type'] ) {
						case 'cart_item':
							$b         = 0;
							$remaining = $q;

							for ( $i = 0; $i < $q; $i ++ ) {
								if ( $q > $rule['from'] && $remaining > $rule['from'] ) {
									$b ++;
								}

								$remaining -= ( $rule['from'] + $rule['adjust'] );
								$remaining = max( 0, $remaining );

								if ( $remaining <= $rule['from'] ) {
									break;
								}
							}

							if ( $b ) {
								$f = $b * $rule['from'];
								$a = $q - $f - $remaining;
							} else {
								$f = $rule['from'];
								$a = 0;
							}
							break;
						case 'cat' :

							$terms = $this->get_product_category_ids( $cart_item['data'] );

							if ( count( array_intersect( $collector['args']['cats'], $terms ) ) > 0 ) {
								//How many blocks are available.
								$b = floor( $q / ( $rule['from'] + $rule['adjust'] ) );
								$a = $b * $rule['adjust'];
								$f = $b * $rule['from'];
							} else {
								$f = $rule['from'];
								//How many times does the categoy trigger the rule to be applied?
								$b = floor( $q / $rule['from'] );
								//How many blocks * the amount to adjust per block
								$a = $rule['adjust'] * $b;
							}

							if ( isset( $this->used_rules[ $rule_set_id ] ) ) {
								$a = $a - $this->used_rules[ $rule_set_id ];
							}

							break;
						case 'product':

							if ( $q > $cart_item['quantity'] ) {
								//The quantity of the Product in the cart is greater than this line item quanity.  An extension must be allowing the product to be
								//in the cart more than once ( product-addons, gravity forms, etc.. ) or this is a variation.
								//Special handling needed in this situation.
								$b = floor( $q / ( $rule['from'] + $rule['adjust'] ) );
								$a = $b * $rule['adjust'];
								$f = $b * $rule['from'];

								if ( isset( $this->used_rules[ $rule_set_id ] ) ) {
									$a = $a - $this->used_rules[ $rule_set_id ];
								}
							} else {
								$b         = 0;
								$remaining = $q;

								for ( $i = 0; $i < $q; $i ++ ) {
									if ( $q > $rule['from'] && $remaining > $rule['from'] ) {
										$b ++;
									}

									$remaining -= ( $rule['from'] + $rule['adjust'] );
									$remaining = max( 0, $remaining );

									if ( $remaining <= $rule['from'] ) {
										break;
									}
								}

								if ( $b ) {
									$f = $b * $rule['from'];
									$a = $q - $f - $remaining;
								} else {
									$f = $rule['from'];
									$a = 0;
								}
							}
							break;
						case 'variation':
							if ( $q > $cart_item['quantity'] ) {
								//The quantity of the variation in the cart is greater than this line item quantity.  More than one variation is in the cart
								//for this product.  Handle the same as if the product is in the cart more than once.
								$b = floor( $q / ( $rule['from'] + $rule['adjust'] ) );
								$a = $b * $rule['adjust'];
								$f = $b * $rule['from'];

								if ( isset( $this->used_rules[ $rule_set_id ] ) ) {
									$a = $a - $this->used_rules[ $rule_set_id ];
								}
							} else {

								//How many blocks are available.
								$b = floor( $q / ( $rule['from'] + $rule['adjust'] ) );
								$a = $b * $rule['adjust'];
								$f = $b * $rule['from'];
							}
							break;
					}
					$rq = $f; //required quantity equals the amount of fixed price items we need to have.
				} else {

					switch ( $collector['type'] ) {
						case 'cart_item':
							$f  = $rule['from'];
							$a  = max( 0, min( $rule['adjust'], $cart_item['quantity'] - $f ) );
							$rq = $f + min( $a, $cart_item['quantity'] - $f );
							break;
						case 'cat':
							$f = $rule['from'];
							$a = $rule['adjust'];

							if ( isset( $this->used_rules[ $rule_set_id ] ) ) {
								$a = $a - $this->used_rules[ $rule_set_id ];
							}

							$terms = $this->get_product_category_ids( $cart_item['data'] );
							if ( count( array_intersect( $collector['args']['cats'], $terms ) ) > 0 ) {
								$rq = $f + $a;
							} else {
								$rq = $f;
							}

							break;
						case 'product':

							$f = $rule['from'];
							$a = min( $rule['adjust'], max( 0, $q - $f ) );

							if ( isset( $this->used_rules[ $rule_set_id ] ) ) {
								$a = $a - $this->used_rules[ $rule_set_id ];
							}

							$rq = $f + $a; //required quantity is the amount of fixed price items + the amount that will be adjusted.
							break;
						case 'variation':
							$f = $rule['from'];
							$a = min( $rule['adjust'], max( 0, $q - $f ) );

							if ( isset( $this->used_rules[ $rule_set_id ] ) ) {
								$a = $a - $this->used_rules[ $rule_set_id ];
							}

							$rq = $f + $a; //required quantity is the amount of fixed price items + the amount that will be adjusted.
							break;
					}
				}

				if ( $a > $cart_item['quantity'] ) {
					$a = $cart_item['quantity'];
				}

				if ( $q >= $rq ) {

					$amount       = apply_filters( 'woocommerce_dynamic_pricing_get_rule_amount', $rule['amount'], $rule, $cart_item, $this );
					$num_decimals = apply_filters( 'woocommerce_dynamic_pricing_get_decimals', (int) get_option( 'woocommerce_price_num_decimals' ) );

					switch ( $rule['type'] ) {
						case 'fixed_adjustment':
							$adjusted            = floatval( $price ) - floatval( $amount );
							$adjusted            = max( $adjusted, 0 );
							$full_price_quantity = $cart_item['quantity'] - $a;
							$discount_quantity   = $a;
							$line_total          = ( $discount_quantity * $adjusted ) + ( $full_price_quantity * $price );
							$result              = $line_total / $cart_item['quantity'];
							$result              = max( $result, 0 );

							$this->used_rules[ $rule_set_id ] = isset( $this->used_rules[ $rule_set_id ] ) ? $this->used_rules[ $rule_set_id ] + $a : $a;

							break;
						case 'percent_adjustment':
							$amount = floatval( $amount ) / 100;

							$adjusted   = round( floatval( $price ) - ( floatval( $amount ) * $price ), (int) $num_decimals );
							$line_total = 0;

							$full_price_quantity = $cart_item['quantity'] - $a;
							$discount_quantity   = $a;

							$line_total = ( $discount_quantity * $adjusted ) + ( $full_price_quantity * $price );
							$result     = $line_total / $cart_item['quantity'];

							$this->used_rules[ $rule_set_id ] = isset( $this->used_rules[ $rule_set_id ] ) ? $this->used_rules[ $rule_set_id ] + $a : $a;

							break;
						case 'fixed_price':
							$adjusted            = round( floatval( $amount ), (int) $num_decimals );
							$line_total          = 0;
							$full_price_quantity = $cart_item['quantity'] - $a;
							$discount_quantity   = $a;
							$line_total          = ( $discount_quantity * $adjusted ) + ( $full_price_quantity * $price );
							$result              = $line_total / $cart_item['quantity'];
							$result              = max( $result, 0 );

							$this->used_rules[ $rule_set_id ] = isset( $this->used_rules[ $rule_set_id ] ) ? $this->used_rules[ $rule_set_id ] + $a : $a;

							break;
						default:
							$result = false;
							break;
					}
				}
			}
		}

		return $result;
	}

	protected function get_quantity_to_compare( $cart_item, $collector, $set = null ) {
		global $woocommerce_pricing, $woocommerce;
		$quantity = 0;

		switch ( $collector['type'] ) {
			case 'cart_item':
				$quantity = $cart_item['quantity'];
				break;
			case 'cat' :
				if ( isset( $collector['args'] ) && isset( $collector['args']['cats'] ) && is_array( $collector['args']['cats'] ) ) {
					$quantity = 0;
					if ( isset( $collector['args'] ) && isset( $collector['args']['cats'] ) && is_array( $collector['args']['cats'] ) ) {
						$temp_cart = WC_Dynamic_Pricing_Compatibility::WC()->cart->cart_contents;
						foreach ( $temp_cart as $lck => $check_cart_item ) {
							if ( is_object_in_term( $check_cart_item['product_id'], 'product_cat', $collector['args']['cats'] ) ) {
								if ( apply_filters( 'woocommerce_dynamic_pricing_count_categories_for_cart_item', true, $cart_item, $lck ) ) {
									$quantity += (int) $check_cart_item['quantity'];
								}
							}
						}
					}
				}
				break;
			case 'product':
				if ( WC_Dynamic_Pricing_Counter::get_product_count( $cart_item['product_id'] ) ) {
					$quantity += WC_Dynamic_Pricing_Counter::get_product_count( $cart_item['product_id'] );
				}
				break;
			case 'variation':
				if ( WC_Dynamic_Pricing_Counter::get_variation_count( $cart_item['variation_id'] ) ) {
					$quantity += WC_Dynamic_Pricing_Counter::get_variation_count( $cart_item['variation_id'] );
				}
				break;
		}

		return apply_filters( 'woocommerce_dynamic_pricing_get_quantity_for_cart_item', $quantity, $cart_item, $collector, $set );
	}

}
