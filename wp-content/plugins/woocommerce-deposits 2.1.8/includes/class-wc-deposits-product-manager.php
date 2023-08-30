<?php
/**
 * Deposits plan product manager
 *
 * @package woocommerce-deposits
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Deposits_Product_Manager class.
 */
class WC_Deposits_Product_Manager {

	/**
	 * Are deposits enabled for a specific product.
	 *
	 * @param  int $product_id Product ID.
	 * @return bool
	 */
	public static function deposits_enabled( $product_id ) {
		$product = wc_get_product( $product_id );

		if ( ! $product || $product->is_type( array( 'grouped', 'external', 'bundle', 'composite' ) ) ) {
			return false;
		}

		$setting = WC_Deposits_Product_Meta::get_meta( $product_id, '_wc_deposit_enabled' );

		if ( empty( $setting ) ) {
			$setting = get_option( 'wc_deposits_default_enabled', 'no' );
		}

		if ( 'optional' === $setting || 'forced' === $setting ) {
			if ( 'plan' === self::get_deposit_type( $product_id ) && ! self::has_plans( $product_id ) ) {
				return false;
			}
			return true;
		}
		return false;
	}

	/**
	 * Are deposits forced for a specific product?
	 *
	 * @param  int $product_id Product ID.
	 * @return bool
	 */
	public static function deposits_forced( $product_id ) {
		$setting = WC_Deposits_Product_Meta::get_meta( $product_id, '_wc_deposit_enabled' );
		if ( empty( $setting ) ) {
			$setting = get_option( 'wc_deposits_default_enabled', 'no' );
		}
		return 'forced' === $setting;
	}

	/**
	 * Get deposit type.
	 *
	 * @param  int $product_id Product ID.
	 * @return string
	 */
	public static function get_deposit_type( $product_id ) {
		$setting = WC_Deposits_Product_Meta::get_meta( $product_id, '_wc_deposit_type' );
		if ( ! $setting ) {
			$setting = get_option( 'wc_deposits_default_type', 'percent' );
		}
		return $setting;
	}

	/**
	 * Get deposit selected type.
	 *
	 * @param  int $product_id Product ID.
	 * @return string
	 */
	public static function get_deposit_selected_type( $product_id ) {
		$setting = WC_Deposits_Product_Meta::get_meta( $product_id, '_wc_deposit_selected_type' );
		if ( ! $setting ) {
			$setting = get_option( 'wc_deposits_default_selected_type', 'deposit' );
		}
		return $setting;
	}

	/**
	 * Does the product have plans?
	 *
	 * @param  int $product_id Product ID.
	 * @return int
	 */
	public static function has_plans( $product_id ) {
		$plans = count( array_map( 'absint', array_filter( (array) WC_Deposits_Product_Meta::get_meta( $product_id, '_wc_deposit_payment_plans' ) ) ) );
		if ( $plans <= 0 ) {
			$default_payment_plans = get_option( 'wc_deposits_default_plans', array() );
			if ( empty( $default_payment_plans ) ) {
				return 0;
			}
			return count( $default_payment_plans );
		}
		return $plans;
	}

	/**
	 * Formatted deposit amount for a product based on fixed or %.
	 *
	 * @param  int $product_id Product ID.
	 * @return string
	 */
	public static function get_formatted_deposit_amount( $product_id ) {
		$product = wc_get_product( $product_id );

		$amount = self::get_deposit_amount_for_display( $product );

		if ( $amount ) {
			$type = self::get_deposit_type( $product_id );

			if ( $product->is_type( 'booking' ) && 'yes' === WC_Deposits_Product_Meta::get_meta( $product_id, '_wc_deposit_multiple_cost_by_booking_persons' ) ) {
				$item = __( 'person', 'woocommerce-deposits' );
			} else {
				$item = __( 'item', 'woocommerce-deposits' );
			}

			if ( 'percent' === $type ) {
				/* translators: percent per item/person */
				return sprintf( __( 'Pay a %1$s deposit per %2$s', 'woocommerce-deposits' ), '<span class="wc-deposits-amount">' . $amount . '</span>', $item );
			} else {
				/* translators: amount per item/person */
				return sprintf( __( 'Pay a deposit of %1$s per %2$s', 'woocommerce-deposits' ), '<span class="wc-deposits-amount">' . $amount . '</span>', $item );
			}
		}
		return '';
	}

	/**
	 * Formatted deposit amount for a product based on payment plan.
	 *
	 * @param  int $product_id Product ID.
	 * @param  int $plan_id    Payment Plan ID.
	 * @return string
	 */
	public static function get_formatted_deposit_payment_plan_amount( $product_id, $plan_id ) {
		$product = wc_get_product( $product_id );
		$amount  = self::get_deposit_amount_for_display( $product, $plan_id );
		if ( $amount ) {
			/* translators: %s is the deposit amount to be paid */
			return sprintf( __( 'Pay a %s deposit per item', 'woocommerce-deposits' ), '<span class="wc-deposits-amount">' . $amount . '</span>' );
		}
		return '';
	}

	/**
	 * Deposit amount for a product based on fixed or %.
	 *
	 * @param  WC_Product|int $product Product.
	 * @param  int            $plan_id Plan ID.
	 * @return float|bool
	 */
	public static function get_deposit_amount_for_display( $product, $plan_id = 0 ) {
		if ( is_numeric( $product ) ) {
			$product = wc_get_product( $product );
		}
		$type       = self::get_deposit_type( $product->get_id() );
		$percentage = false;

		if ( in_array( $type, array( 'fixed', 'percent' ), true ) ) {
			$amount = WC_Deposits_Product_Meta::get_meta( $product->get_id(), '_wc_deposit_amount' );

			if ( ! $amount ) {
				$amount = get_option( 'wc_deposits_default_amount' );
			}

			if ( ! $amount ) {
				return false;
			}

			if ( 'percent' === $type ) {
				$percentage = true;
			}
		} else {
			if ( ! $plan_id ) {
				return false;
			}

			$plan          = new WC_Deposits_Plan( $plan_id );
			$schedule      = $plan->get_schedule();
			$first_payment = current( $schedule );
			$amount        = $first_payment->amount;
			$percentage    = true;
		}

		if ( ! $percentage ) {
			/**
			 * Filters fixed amount deposit value.
			 * This filter is used by "WooCommerce Multi-Currency" plugin to convert deposit amount to specific currency.
			 *
			 * @param float      $amount  Fixed amount deposit value.
			 * @param WC_Product $product WC_Product object.
			 */
			$amount = apply_filters( 'woocommerce_deposits_fixed_deposit_amount', $amount, $product );
			return wc_price( self::get_price( $product, $amount ) );
		} else {
			return $amount . '%';
		}
	}

	/**
	 * Deposit amount for a product based on fixed or % using actual prices.
	 *
	 * @param  WC_Product|int $product Product.
	 * @param  int            $plan_id Plan ID.
	 * @param  string         $context of display Valid values display or order.
	 * @param  float          $product_price If the price differs from that set in the product.
	 * @return float|bool
	 */
	public static function get_deposit_amount( $product, $plan_id = 0, $context = 'display', $product_price = null ) {
		if ( is_numeric( $product ) ) {
			$product = wc_get_product( $product );
		}
		$type       = self::get_deposit_type( $product->get_id() );
		$percentage = false;

		if ( in_array( $type, array( 'fixed', 'percent' ), true ) ) {
			$amount = WC_Deposits_Product_Meta::get_meta( $product->get_id(), '_wc_deposit_amount' );

			if ( ! $amount ) {
				$amount = get_option( 'wc_deposits_default_amount' );
			}

			if ( ! $amount ) {
				return false;
			}

			if ( 'percent' === $type ) {
				$percentage = true;
			}
		} else {
			if ( ! $plan_id ) {
				return false;
			}

			$plan          = new WC_Deposits_Plan( $plan_id );
			$schedule      = $plan->get_schedule();
			$first_payment = current( $schedule );
			$amount        = $first_payment->amount;
			$percentage    = true;
		}

		if ( $percentage ) {
			$product_price = is_null( $product_price ) ? $product->get_price() : $product_price;
			$amount        = ( $product_price / 100 ) * $amount;
		} else {
			/**
			 * Filters fixed amount deposit value.
			 * This filter is used by "WooCommerce Multi-Currency" plugin to convert deposit amount to specific currency.
			 *
			 * @param float      $amount  Fixed amount deposit value.
			 * @param WC_Product $product WC_Product object.
			 */
			$amount = apply_filters( 'woocommerce_deposits_fixed_deposit_amount', $amount, $product );
		}

		$price = 'display' === $context ? self::get_price( $product, $amount ) : $amount;
		return wc_format_decimal( $price );
	}

	/**
	 * Get correct price/amount depending on tax mode.
	 *
	 * @since  1.2.0
	 * @param  WC_Product $product Product.
	 * @param  float      $amount Amount.
	 * @return float
	 */
	protected static function get_price( $product, $amount ) {
		return wc_get_price_to_display(
			$product,
			array(
				'qty'   => 1,
				'price' => $amount,
			)
		);
	}
}
