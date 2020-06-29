<?php
/**
 * WooCommerce Intuit Payments
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Intuit Payments to newer
 * versions in the future. If you wish to customize WooCommerce Intuit Payments for your
 * needs please refer to https://docs.woocommerce.com/document/intuit-qbms/
 *
 * @package   WC-Intuit-Payments/Gateway
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

/**
 * The eCheck gateway class.
 *
 * @since 2.0.0
 */
class WC_Gateway_Inuit_Payments_eCheck extends WC_Gateway_Inuit_Payments {


	/**
	 * Constructs the gateway.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		parent::__construct(
			WC_Intuit_Payments::ECHECK_ID,
			array(
				'method_title' => __( 'Intuit Payments eCheck', 'woocommerce-gateway-intuit-payments' ),
				'supports'     => array(
					self::FEATURE_TOKENIZATION,
					self::FEATURE_ADD_PAYMENT_METHOD,
					self::FEATURE_TOKEN_EDITOR,
				),
				'payment_type' => self::PAYMENT_TYPE_ECHECK,
			)
		);
	}


	/**
	 * Gets the echeck test case options.
	 *
	 * @since 2.0.0
	 * @return array
	 */
	protected function get_test_case_options() {

		return array(
			'1.11' => __( 'Pending', 'woocommerce-gateway-intuit-payments' ),
			'3.33' => __( 'Declined', 'woocommerce-gateway-intuit-payments' ),
			'5.55' => __( 'Succeeded', 'woocommerce-gateway-intuit-payments' ),
		);
	}


	/**
	 * Renders hidden inputs on the payment form for the JS token & last four.
	 *
	 * @since 2.0.0
	 */
	public function render_hidden_inputs() {

		parent::render_hidden_inputs();

		// If adding a new payment method, add the billing phone field
		if ( is_add_payment_method_page() ) {

			$user = get_userdata( get_current_user_id() );

			// last name
			printf( '<input type="hidden" id="%1$s" name="%1$s" value="%2$s" />', 'billing_phone', $user->billing_phone );
		}
	}


	/**
	 * Gets the payment form field defaults.
	 *
	 * @since 2.0.0
	 * @return array
	 */
	public function get_payment_method_defaults() {

		$defaults = parent::get_payment_method_defaults();

		if ( $this->is_test_environment() ) {
			$defaults['account-number'] = '11000000333456781';
			$defaults['routing-number'] = '322079353';
		}

		return $defaults;
	}


	/**
	 * Removes the input names for the account number and routing number fields
	 * so they're not POSTed to the server.
	 *
	 * @since 2.0.0
	 * @param array $fields the payment form fields
	 * @return array
	 */
	public function remove_payment_form_field_input_names( $fields ) {

		$fields['routing-number']['name'] = $fields['account-number']['name'] = '';

		return $fields;
	}


	/**
	 * Validate the provided eCheck fields.
	 *
	 * @since 2.0.0
	 * @see Framework\SV_WC_Payment_Gateway_Direct::validate_credit_card_fields()
	 * @param bool $is_valid whether the fields are valid
	 * @return bool whether the fields are valid
	 */
	protected function validate_check_fields( $is_valid ) {

		return $is_valid;
	}


}
