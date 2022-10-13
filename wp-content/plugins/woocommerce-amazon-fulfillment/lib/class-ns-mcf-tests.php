<?php
/**
 * Tests class for automating SP-API tests and other aspects to manually run before updates and verify stability.
 * TODO: 4.1.0 This class should have a test harness for every API call used by the plugin.
 * TODO: 4.1.0 It should basically dynamically call all functions in use by the plugin, which make the API calls...
 * TODO: 4.1.0 ...providing fixed / test data with known / expected outcomes, and then dump the results to...
 * TODO: 4.1.0 ...to log file(s). I envision being able to click a single button in settings (maybe only visible...
 * TODO: 4.1.0 ...when debug mode is ON) and then it automatically runs through every test and results in 1 log...
 * TODO: 4.1.0 ...file with all the results / response data... The goal is that this can be run after any major...
 * TODO: 4.1.0 ...changes happen to the plugin and quickly verify that the changes have not broken anything.
 *
 * @package NeverSettle\WooCommerce-Amazon-Fulfillment
 * @since 4.1.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'NS_MCF_Tests' ) ) {

	/**
	 * Tests class.
	 */
	class NS_MCF_Tests extends NS_MCF_Integration {

	} // End class.
}
