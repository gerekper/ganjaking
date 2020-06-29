<?php

class WC_Dynamic_Pricing_Advanced_Category extends WC_Dynamic_Pricing_Advanced_Base {

	private static $instance;

	public static function instance() {
		if ( self::$instance == null ) {
			self::$instance = new WC_Dynamic_Pricing_Advanced_Category( 'advanced_category' );
		}

		return self::$instance;
	}

	public $adjustment_sets;

	public function __construct( $module_id ) {
		parent::__construct( $module_id );
		$sets = get_option( '_a_category_pricing_rules' );
		if ( $sets && is_array( $sets ) && sizeof( $sets ) > 0 ) {
			foreach ( $sets as $id => $set_data ) {
				$obj_adjustment_set           = new WC_Dynamic_Pricing_Adjustment_Set_Category( $id, $set_data );
				$this->adjustment_sets[ $id ] = $obj_adjustment_set;
			}
		}
	}

	public function adjust_cart( $temp_cart ) {

		if ( $this->adjustment_sets && count( $this->adjustment_sets ) ) {

			$valid_sets = wp_list_filter( $this->get_adjustment_sets(), array(
				'is_valid_rule'     => true,
				'is_valid_for_user' => true
			) );

			if ( empty( $valid_sets ) ) {
				return;
			}

			foreach ( $temp_cart as $cart_item_key => $values ) {
				$temp_cart[ $cart_item_key ]['available_quantity'] = $values['quantity'];
				$temp_cart[ $cart_item_key ]['has_special_offers'] = false;
			}

			//Process block discounts first
			foreach ( $valid_sets as $set_id => $set ) {

				if ( $set->mode != 'block' ) {
					continue;
				}

				//check if this set is valid for the current user;
				$is_valid_for_user = $set->is_valid_for_user();
				if ( ! ( $is_valid_for_user ) ) {
					continue;
				}


				//Lets actually process the rule.
				//Setup the matching quantity
				$targets = $set->targets;

				$collector = $set->get_collector();
				$q         = 0;
				if ( isset( $collector['args'] ) && isset( $collector['args']['cats'] ) && is_array( $collector['args']['cats'] ) ) {
					foreach ( $collector['args']['cats'] as $cat_id ) {
						$q += WC_Dynamic_Pricing_Counter::get_category_count( $cat_id );
					}
				} else {
					continue; //no categories
				}

				$rule = reset( $set->pricing_rules ); //block rules can only have one line item. 
				if ( $q < $rule['from'] ) {
					//continue;
				}
				if ( $rule['repeating'] == 'yes' ) {
					$b = floor( $q / ( $rule['from'] ) ); //blocks - this is how many times has the required amount been met. 
				} else {
					$b = 1;
				}

				$ct = 0; //clean targets
				$mt = 0;

				$cq = 0; //matched clean quantity;
				$mq = 0; //matched mixed quantity;

				foreach ( $temp_cart as $cart_item_key => &$cart_item ) {
					$product = $cart_item['data'];
					$terms   = $this->get_product_category_ids( $product );
					if ( count( array_intersect( $collector['args']['cats'], $terms ) ) > 0 ) {
						if ( count( array_intersect( $targets, $terms ) ) > 0 ) {
							$mq += $cart_item['available_quantity'];
						} else {
							$cq += $cart_item['available_quantity'];
						}
					}

					if ( count( array_intersect( $targets, $terms ) ) > 0 ) {
						if ( count( array_intersect( $collector['args']['cats'], $terms ) ) == 0 ) {
							$ct += $cart_item['quantity'];
						} else {
							$mt += $cart_item['quantity'];
						}
					}
				}

				$rt  = $ct + $mt; //remaining targets.
				$rcq = $cq; //remaining clean quantity
				$rmq = $mq; //remaining mixed quantity

				$tt = 0; //the total number of items we can discount. 
				//for each block reduce the amount of remaining items which can make up a discount by the amount required. 
				if ( $rcq || $rmq ) {
					for ( $x = 0; $x < $b; $x ++ ) {
						//If the remaining clean quantity minus what is required to make a block is greater than 0 there are more clean quantity items remaining. 
						//This means we don't have to eat into mixed quantities yet. 
						if ( $rcq - $rule['from'] >= 0 ) {
							$rcq -= $rule['from'];
							$tt  += $rule['adjust'];
							//If the total items that can be discounted is greater than the number of clean items to be discounted, reduce the
							//mixed quantity by the difference, because those items will be discounted and can not count towards making another discounted item. 
							if ( $tt > $ct ) {
								$rmq -= ( $tt - $ct );
							}

							if ( $tt > $mt + $ct ) {
								$tt = $mt + $ct;
							}

							$rt -= ( $ct + $mt ) - $tt;
						} else {
							//how many items left over from clean quantities.  if we have a buy two get one free, we may have one quantity of clean item, and two mixed items. 
							$l = $rcq ? $rule['from'] - $rcq : 0;
							if ( $rcq > 0 ) {
								//If the remaining mixed quantity minus the left overs trigger items is more than 0, we have another discount available
								if ( $rt - $l > 0 ) {
									$tt += min( $rt - $l, $rule['adjust'] );
								}

								$rt -= ( $ct + $mt ) - $tt;
							} elseif ( $rmq > 0 ) {
								$rt -= $rule['from'];
								//$rt -= ($ct + $mt) - $tt;
								if ( $rt > 0 ) {
									$tt  += min( $rt, $rule['adjust'] );
									$rt  -= min( $rt, $rule['adjust'] );
									$rmq = $rmq - $l - ( $rule['adjust'] + $rule['from'] );
								}
							}

							$rcq = 0;
						}
					}

					foreach ( $temp_cart as $cart_item_key => $ctitem ) {
						if ( isset( $ctitem['has_special_offers'] ) && $ctitem['has_special_offers'] ) {
							continue;
						}

						$product = $ctitem['data'];

						$price_adjusted = false;
						$original_price = $this->get_price_to_discount( $ctitem, $cart_item_key );

						//Check if the original price is free, we don't want to apply any of these discounts, or use any of them up by 
						//applying a discount to a free item. 
						$op_check = floatval( $original_price );
						if ( empty( $op_check ) && $rule['type'] != 'fixed_adjustment' ) {
							continue;
						}

						$process_discounts = apply_filters( 'woocommerce_dynamic_pricing_process_product_discounts', true, $ctitem['data'], 'advanced_category', $this, $ctitem );
						if ( ! $process_discounts ) {
							continue;
						}

						$terms = $this->get_product_category_ids( $product );
						if ( count( array_intersect( $targets, $terms ) ) > 0 ) {

							$price_adjusted = $this->get_block_adjusted_price( $ctitem, $original_price, $rule, $tt );

							if ( $tt > $ctitem['quantity'] ) {
								$tt                                                -= $ctitem['quantity'];
								$temp_cart[ $cart_item_key ]['available_quantity'] = 0;
							} else {
								$temp_cart[ $cart_item_key ]['available_quantity'] = $ctitem['quantity'] - $tt;
								$tt                                                = 0;
							}

							if ( $price_adjusted !== false && floatval( $original_price ) != floatval( $price_adjusted ) ) {
								$temp_cart[ $cart_item_key ]['has_special_offers'] = true;
								WC_Dynamic_Pricing::apply_cart_item_adjustment( $cart_item_key, $original_price, $price_adjusted, 'advanced_category', $set_id );
							}
						}
					}
				}
			}


			//Now process bulk rules

			foreach ( $temp_cart as $b_cart_item_key => $b_cart_item ) {
				$adjustment = $this->get_bulk_cart_item_adjusted_price( $b_cart_item, $b_cart_item_key );
				if ( $adjustment !== false ) {
					WC_Dynamic_Pricing::apply_cart_item_adjustment( $b_cart_item_key, $adjustment['original_price'], $adjustment['price_adjusted'], $this->module_id, $adjustment['set_id'] );
				}
			}
		}
	}


	/**
	 *
	 * Gets the bulk adjusted price for an item based on the cart item and rules which would apply to it.
	 *
	 * @param      $cart_item               The woocommerce cart item.
	 * @param      $cart_item_key           The woocommerce cart item key.
	 * @param bool $original_price_override An optional price to be sent to the adjustment calculation functions.
	 *
	 * @return array|bool
	 */
	public function get_bulk_cart_item_adjusted_price( $cart_item, $cart_item_key, $original_price_override = false ) {

		$valid_sets = $this->get_valid_adjustment_sets_for_cart_item( $cart_item, $cart_item_key );
		if ( $valid_sets === false ) {
			return false;
		}

		foreach ( $valid_sets as $set_id => $set ) {
			if ( $set->mode != 'bulk' ) {
				continue;
			}

			$result = $this->get_bulk_cart_item_adjusted_price_by_adjustment_set( $set, $cart_item, $cart_item_key, $original_price_override );
			if ( $result !== false ) {
				return $result;
			}
		}

		return false;
	}

	/**
	 *
	 * Gets the adjusted price for a cart item for a specific adjustment set.
	 * You should only pass valid adjustment sets for the cart item to this function.
	 *
	 * @param      $adjustment_set          WC_Dynamic_Pricing_Adjustment_Set_Category
	 * @param      $cart_item               array
	 * @param      $cart_item_key           string
	 * @param bool $original_price_override bool|float Optional price to pass to the adjustment calculation functions.
	 *
	 * @return array|bool
	 * @see WC_Dynamic_Pricing_Advanced_Category::get_valid_adjustment_sets_for_cart_item();
	 *
	 */
	public function get_bulk_cart_item_adjusted_price_by_adjustment_set( $adjustment_set, $cart_item, $cart_item_key, $original_price_override = false ) {
		if ( ! $adjustment_set->is_valid_for_user() ) {
			return false;
		}

		$product = $cart_item['data'];

		$process_discounts = apply_filters( 'woocommerce_dynamic_pricing_process_product_discounts', true, $cart_item['data'], 'advanced_category', $this, $cart_item );
		if ( ! $process_discounts ) {
			return false;
		}


		$price_adjusted = false;
		if ( $original_price_override === false ) {
			$original_price = $this->get_price_to_discount( $cart_item, $cart_item_key );
		} else {
			$original_price = $original_price_override;
		}


		if ( is_array( $adjustment_set->pricing_rules ) && sizeof( $adjustment_set->pricing_rules ) > 0 ) {

			//Get the quantity to match to pass in to the calculate_bulk_adjusted_price, where it's used to determine if the specific rule applies.
			$collector = $adjustment_set->get_collector_object();
			$q         = $collector->collect_quantity( $cart_item );

			foreach ( $adjustment_set->pricing_rules as $rule ) {
				$price_adjusted = $this->calculate_bulk_adjusted_price( $cart_item, $original_price, $rule, $q );
				if ( $price_adjusted !== false ) {
					break;
				}
			}
		}

		if ( $price_adjusted !== false && floatval( $original_price ) != floatval( $price_adjusted ) ) {
			return array(
				'set_id'         => $adjustment_set->set_id,
				'original_price' => $original_price,
				'price_adjusted' => $price_adjusted
			);

		}


		return false;
	}

	//calculate the block based price
	protected function get_block_adjusted_price( $cart_item, $price, $rule, $a ) {
		if ( $a > $cart_item['quantity'] ) {
			$a = $cart_item['quantity'];
		}

		$amount       = apply_filters( 'woocommerce_dynamic_pricing_get_rule_amount', $rule['amount'], $rule, $cart_item, $this );
		$num_decimals = apply_filters( 'woocommerce_dynamic_pricing_get_decimals', (int) get_option( 'woocommerce_price_num_decimals' ) );

		switch ( $rule['type'] ) {
			case 'fixed_adjustment':
				$adjusted            = floatval( $price ) - floatval( $amount );
				$adjusted            = $adjusted >= 0 ? $adjusted : 0;
				$line_total          = 0;
				$full_price_quantity = $cart_item['quantity'] - $a;

				$discount_quantity = $a;

				$line_total = ( $discount_quantity * $adjusted ) + ( $full_price_quantity * $price );
				$result     = $line_total / $cart_item['quantity'];
				$result     = $result >= 0 ? $result : 0;

				break;
			case 'percent_adjustment':
				$amount     = $amount / 100;
				$adjusted   = round( floatval( $price ) - ( floatval( $amount ) * $price ), (int) $num_decimals );
				$line_total = 0;

				$full_price_quantity = $cart_item['available_quantity'] - $a;
				$discount_quantity   = $a;

				$line_total = ( $discount_quantity * $adjusted ) + ( $full_price_quantity * $price );
				$result     = $line_total / $cart_item['quantity'];

				$result = $result >= 0 ? $result : 0;
				break;
			case 'fixed_price':
				$adjusted            = round( $amount, (int) $num_decimals );
				$line_total          = 0;
				$full_price_quantity = $cart_item['quantity'] - $a;
				$discount_quantity   = $a;
				$line_total          = ( $discount_quantity * $adjusted ) + ( $full_price_quantity * $price );
				$result              = $line_total / $cart_item['quantity'];
				$result              = $result >= 0 ? $result : 0;

				break;
			default:
				$result = false;
				break;
		}

		return $result;
	}

	protected function calculate_bulk_adjusted_price( $cart_item, $price, $rule, $q ) {
		if ( ! is_numeric( $price ) ) {
			return $price;
		}

		$result = false;

		$amount       = apply_filters( 'woocommerce_dynamic_pricing_get_rule_amount', $rule['amount'], $rule, $cart_item, $this );
		$num_decimals = apply_filters( 'woocommerce_dynamic_pricing_get_decimals', (int) get_option( 'woocommerce_price_num_decimals' ) );


		if ( $rule['from'] == '*' ) {
			$rule['from'] = 0;
		}

		if ( empty( $rule['to'] ) || $rule['to'] == '*' ) {
			$rule['to'] = $q;
		}

		if ( $q >= $rule['from'] && $q <= $rule['to'] ) {
			switch ( $rule['type'] ) {
				case 'price_discount':
					$adjusted = floatval( $price ) - floatval( $amount );
					$result   = $adjusted >= 0 ? $adjusted : 0;
					break;
				case 'percentage_discount':

					if ( $amount >= 1 ) {
						$amount = $amount / 100;
					}

					$result = round( floatval( $price ) - ( floatval( $amount ) * $price ), (int) $num_decimals );
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
		}


		return $result;
	}


	/**
	 *
	 * Gets the Advanced Category adjustment sets.  Does no validity checking for user or roles.
	 *
	 *
	 * @return bool|WC_Dynamic_Pricing_Adjustment_Set_Category[] The list of valid pricing adjustment sets.
	 */
	public function get_adjustment_sets() {
		return apply_filters( 'wc_dynamic_pricing_get_adjustment_sets', ( empty( $this->adjustment_sets ) ? false : $this->adjustment_sets ), 'advanced_category' );
	}


	/**
	 *
	 * Gets the adjustment sets for the cart item based on matching the target categories to the product.
	 *
	 * @param WC_Product $product
	 *
	 * @return bool|WC_Dynamic_Pricing_Adjustment_Set_Category[] The list of valid pricing adjustment sets.
	 */
	public function get_adjustment_sets_for_product( $product ) {

		if ( ! is_object( $product ) ) {
			return false;
		}

		$terms = $this->get_product_category_ids( $product );
		$sets  = array();

		$all_sets = $this->get_adjustment_sets();
		if ( $all_sets === false ) {
			return false;
		}

		$valid_sets = wp_list_filter( $this->get_adjustment_sets(), array(
			'is_valid_rule'     => true,
			'is_valid_for_user' => true
		) );


		foreach ( $valid_sets as $adjustment_set ) {
			if ( count( array_intersect( $adjustment_set->targets, $terms ) ) > 0 ) {
				$sets[ $adjustment_set->set_id ] = $adjustment_set;
			}
		}

		return apply_filters( 'wc_dynamic_pricing_get_adjustment_sets_for_product', ( empty( $sets ) ? false : $sets ), $product, 'advanced_category' );
	}


	/**
	 *
	 * Gets the valid Adjustment Sets which should be processed.  Checks for the cumulative and already discounted flags on the cart item.
	 * Also makes sure the item is in the correct terms for the adjustment set to apply.
	 *
	 * @param $cart_item     The woocommerce cart item
	 * @param $cart_item_key The woocommerce cart item key
	 *
	 *
	 * @return bool|WC_Dynamic_Pricing_Adjustment_Set_Category[] The list of valid pricing adjustment sets.
	 */
	public function get_valid_adjustment_sets_for_cart_item( $cart_item, $cart_item_key ) {

		if ( empty( $cart_item['data'] ) ) {
			return false;
		}

		$cart_item_sets = $this->get_adjustment_sets_for_product( $cart_item['data'] );
		if ( $cart_item_sets === false ) {
			return false;
		}

		$sets = array();
		foreach ( $cart_item_sets as $adjustment_set ) {
			if ( ! $this->is_cumulative( $cart_item, $cart_item_key ) ) {
				if ( ! $this->is_item_discounted( $cart_item, $cart_item_key ) ) {
					$sets[ $adjustment_set->set_id ] = $adjustment_set;
				}
			} else {
				if ( ! $this->is_item_discounted( $cart_item, $cart_item_key, $adjustment_set->set_id ) ) {
					$sets[ $adjustment_set->set_id ] = $adjustment_set;
				}
			}
		}

		return apply_filters( 'wc_dynamic_pricing_get_valid_adjustment_sets_for_cart_item', ( empty( $sets ) ? false : $sets ), $cart_item, 'advanced_category' );


	}


	public function get_adjusted_price( $cart_item_key, $cart_item ) {

	}

}
