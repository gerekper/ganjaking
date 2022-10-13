<?php
/**
 * Abstract MCF integration class.
 *
 * Handles base set up of MCF classes.
 *
 * @package NeverSettle\WooCommerce-Amazon-Fulfillment
 * @since 4.1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Abstract NS MCF integration
 *
 * Implemented by classes using the same pattern.
 */
abstract class NS_MCF_Integration {

	/**
	 * Singleton instance of NS_FBA
	 *
	 * @var NS_FBA $ns_fba
	 */
	protected $ns_fba;


	/**
	 * Constructor.
	 *
	 * @param NS_FBA $ns_fba The main NS_FBA object.
	 */
	public function __construct( NS_FBA $ns_fba ) {
		$this->ns_fba = $ns_fba;
		$this->init();
	}

	/**
	 * Initializes the module. Always executed even if the module is deactivated.
	 *
	 * Do not use __construct in subclasses, use init() instead.
	 */
	public function init() {}
}
