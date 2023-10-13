<?php

namespace WCML\Compatibility\WcSubscriptions;

use WC_Cart;

class SharedHooks implements \IWPML_Action {

	/** @var WC_Cart[]|null $recurring_carts */
	private $recurring_carts;

	public function add_hooks() {
		add_action( 'init', [ $this, 'init' ], 9 );
	}

	public function init() {
		if ( ! is_admin() ) {
			add_action( 'woocommerce_before_calculate_totals', [ $this, 'maybe_backup_recurring_carts' ], 1 );
			add_action( 'woocommerce_after_calculate_totals', [ $this, 'maybe_restore_recurring_carts' ], 200 );
		}
	}

	/**
	 * @param WC_Cart $cart
	 */
	public function maybe_backup_recurring_carts( $cart ) {
		if ( ! empty( $cart->recurring_carts ) ) {
			$this->recurring_carts = $cart->recurring_carts;
		}
	}

	/**
	 * @param WC_Cart $cart
	 */
	public function maybe_restore_recurring_carts( $cart ) {
		if ( ! empty( $this->recurring_carts ) ) {
			/* @phpstan-ignore-next-line */
			$cart->recurring_carts = $this->recurring_carts;
			$this->recurring_carts = null;
		}
	}
}
