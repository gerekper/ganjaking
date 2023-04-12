<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Product_Topup
 */
class WC_Product_Topup extends WC_Product {

	/**
	 * Constructor
	 */
	public function __construct( $product ) {
		parent::__construct( $product );
		$this->product_type = 'topup';
	}

	/**
	 * Auto-load in-accessible properties on demand.
	 *
	 * @since 2.8.0
	 *
	 * @param mixed $key Key name.
	 * @return mixed
	 */
	public function __get( $key ) {
		if ( 'tax_status' === $key ) {
			wc_doing_it_wrong( 'WC_Product_Topup->tax_status', 'Accessing this property is deprecated. Use WC_Product_Topup->get_tax_status() instead', '2.8.0' );
			return $this->get_tax_status();
		}
	}

	/** Exists */
	public function exists() {
		return true;
	}

	/** Purchasable */
	public function is_purchasable() {
		return true;
	}

	/**
	 * Product type.
	 *
	 * @since 2.1.3
	 *
	 * @version 2.1.3
	 *
	 * @return string Type of product.
	 */
	public function get_type() {
		return 'topup';
	}

	/** Title */
	public function get_title() {
		return sprintf(
			/* translators: %s: funds name */
			__( '%s Top-up', 'woocommerce-account-funds' ),
			wc_get_account_funds_name()
		);
	}

	/**
	 * Returns the tax status.
	 *
	 * @param string $context What the value is for. Valid values are view and edit.
	 * @return string
	 */
	public function get_tax_status( $context = 'view' ) {
		/**
		 * Filters the tax status of a Top-up product.
		 *
		 * @since 2.1.2
		 *
		 * @param string $status The tax status.
		 * @param string $context What the value is for. Valid values are view and edit.
		 */
		return apply_filters( 'woocommerce_account_funds_topup_get_tax_status', 'none', $context );
	}

	/**
	 * Not a visible product
	 *
	 * @return boolean
	 */
	public function is_visible() {
		return false;
	}

	/**
	 * Does not need shipping
	 *
	 * @return bool
	 */
	public function is_virtual() {
		return true;
	}

	/**
	 * Make sure topup is sold individually (no quantities).
	 *
	 * @return bool
	 */
	public function is_sold_individually() {
		return true;
	}
}
