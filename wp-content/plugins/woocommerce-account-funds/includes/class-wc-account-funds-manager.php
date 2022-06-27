<?php
/**
 * Class for handling the account funds.
 *
 * @package WC_Account_Funds
 * @since   2.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Account funds manager class.
 */
class WC_Account_Funds_Manager {

	/**
	 * Gets the user's funds.
	 *
	 * @since 2.7.0
	 *
	 * @param int $user_id User ID.
	 * @return float
	 */
	public static function get_user_funds( $user_id ) {
		return floatval( get_user_meta( $user_id, 'account_funds', true ) );
	}

	/**
	 * Sets the user's funds to the specified amount.
	 *
	 * @since 2.7.0
	 *
	 * @param int   $user_id User ID.
	 * @param float $funds   Funds amount.
	 */
	public static function set_user_funds( $user_id, $funds ) {
		return (bool) update_user_meta( $user_id, 'account_funds', wc_format_decimal( $funds ) );
	}

	/**
	 * Increases the user's funds.
	 *
	 * @since 2.7.0
	 *
	 * @param int   $user_id User ID.
	 * @param float $amount  Funds amount.
	 */
	public static function increase_user_funds( $user_id, $amount ) {
		$funds  = self::get_user_funds( $user_id );
		$amount = (float) wc_format_decimal( $amount );

		/**
		 * Filters the amount of funds to add to the user.
		 *
		 * @since 2.1.11
		 *
		 * @param float $funds   Funds amount after the increment.
		 * @param int   $user_id User ID.
		 * @param float $amount  Amount of funds to add.
		 */
		$funds = apply_filters( 'woocommerce_account_funds_add_funds', $funds + $amount, $user_id, $amount );

		self::set_user_funds( $user_id, $funds );
	}

	/**
	 * Decreases the user's funds.
	 *
	 * @since 2.7.0
	 *
	 * @param int   $user_id User ID.
	 * @param float $amount  Funds amount.
	 */
	public static function decrease_user_funds( $user_id, $amount ) {
		$funds  = self::get_user_funds( $user_id );
		$amount = (float) wc_format_decimal( $amount );

		$funds = max( 0, $funds - $amount );

		/**
		 * Filters the amount of funds to remove from the user.
		 *
		 * @since 2.1.11
		 *
		 * @param float $funds   Funds amount after the decrement.
		 * @param int   $user_id User ID.
		 * @param float $amount  Amount of funds to remove.
		 */
		$funds = apply_filters( 'woocommerce_account_funds_remove_funds', $funds, $user_id, $amount );

		self::set_user_funds( $user_id, $funds );
	}
}
