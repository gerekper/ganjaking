<?php
/**
 * WooCommerce Twilio SMS Notifications
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Twilio SMS Notifications to newer
 * versions in the future. If you wish to customize WooCommerce Twilio SMS Notifications for your
 * needs please refer to http://docs.woocommerce.com/document/twilio-sms-notifications/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Twilio SMS Response class
 *
 * Handles all Response actions
 *
 * @since 1.3.0
 */
class WC_Twilio_SMS_Response {


	/**
	 * Adds required Response hooks
	 *
	 * @since 1.3.0
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'create_sms_return_message' ) );
	}

	/**
	 * Create return SMS message
	 *
	 * If 'wc_twilio_sms_response' is present in the url arguements, and the enable
	 * sms option is setup to 'yes' then generate a Twilio SMS response message in
	 * XML format and prevent any further loading.
	 *
	 * @since 1.3.0
	 */
	public function create_sms_return_message() {

		if ( 'yes' === get_option( 'wc_twilio_sms_enable_return_message', 'no' ) ) {

			// allow any actions before we start out response (saving information to db, etc)
			do_action( 'wc_twilio_sms_response_before_output' );

			$message = get_option( 'wc_twilio_sms_return_message' );

			// allow modification of message before variable replace (add additional variables, etc)
			$message = apply_filters( 'wc_twilio_sms_response_before_variable_replace', $message );

			$message = $this->replace_message_variables( $message );

			// allow modification of message after variable replace (add additional variables, etc)
			$message = apply_filters( 'wc_twilio_sms_response_after_variable_replace', $message );

			header( 'content-type: text/xml' );
			echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";

			?>
			<Response>
				<?php if ( $message ) : ?>
					<Message><?php echo $message; ?></Message>
				<?php endif; ?>
			</Response>
			<?php

			die();
		}
	}

	/**
	 * Replaces template variables in SMS response
	 *
	 * @since 1.3.0
	 * @param string $message raw SMS response to replace with variable info
	 * @return string message with variables replaced with indicated values
	 */
	private function replace_message_variables( $message ) {

		$replacements = array(
			'%shop_name%'    => Framework\SV_WC_Helper::get_site_name(),
			'%site_url%'     => home_url()
		);

		return str_replace( array_keys( $replacements ), $replacements, $message );
	}


}
