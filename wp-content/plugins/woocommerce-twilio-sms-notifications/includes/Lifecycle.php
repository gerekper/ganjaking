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

namespace SkyVerge\WooCommerce\Twilio_SMS;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Plugin lifecycle handler.
 *
 * @since 1.12.0
 *
 * @method \WC_Twilio_SMS get_plugin()
 */
class Lifecycle extends Framework\Plugin\Lifecycle {


	/**
	 * Lifecycle constructor.
	 *
	 * @since 1.12.1
	 *
	 * @param \WC_Twilio_SMS $plugin
	 */
	public function __construct( $plugin ) {

		parent::__construct( $plugin );

		$this->upgrade_versions = [
			'1.1.2',
			'1.7.1',
			'1.11.0',
			'1.12.2',
		];
	}


	/**
	 * Runs every time.
	 *
	 * Used since the activation hook is not executed when updating a plugin.
	 *
	 * @since 1.12.0
	 */
	protected function install() {

		require_once( $this->get_plugin()->get_plugin_path() . '/includes/admin/class-wc-twilio-sms-admin.php' );

		// install default settings
		foreach ( \WC_Twilio_SMS_Admin::get_settings() as $setting ) {

			if ( isset( $setting['default'] ) ) {
				add_option( $setting['id'], $setting['default'] );
			}
		}
	}


	/**
	 * Performs plugin version upgrade tasks.
	 *
	 * @since 1.12.0
	 *
	 * @param string $installed_version
	 */
	protected function upgrade( $installed_version ) {

		// overrides option value so logging is enforced during upgrade
		add_filter( 'pre_option_wc_twilio_sms_log_errors', [ $this, 'enable_logging' ] );

		parent::upgrade( $installed_version );

		// restores normal logging behavior
		remove_filter( 'pre_option_wc_twilio_sms_log_errors', [ $this, 'enable_logging' ] );
	}


	/**
	 * Enables logging while performing upgrade routines.
	 *
	 * @internal
	 *
	 * @since 1.12.1
	 *
	 * @return string
	 */
	public function enable_logging() {

		return 'yes';
	}


	/**
	 * Updates to v1.1.2
	 *
	 * @since 1.12.1
	 */
	protected function upgrade_to_1_1_2() {

		delete_option( 'wc_twilio_sms_is_installed' );
	}


	/**
	 * Updates to v1.7.1
	 *
	 * @since 1.12.1
	 */
	protected function upgrade_to_1_7_1() {

		$sms_order_statuses = [];

		foreach ( wc_get_order_statuses() as $slug => $label ) {

			$old_slug = 0 === strpos( $slug, 'wc-' ) ? substr( $slug, 3 ) : $slug;

			if ( 'yes' === get_option( 'wc_twilio_sms_send_sms_' . $old_slug, 'yes' ) ) {
				$sms_order_statuses[] = $slug;
			}

			delete_option( 'wc_twilio_sms_send_sms_' . $old_slug );
		}

		update_option( 'wc_twilio_sms_send_sms_order_statuses', $sms_order_statuses );
	}


	/**
	 * Updates to v1.11.0
	 *
	 * @since 1.12.1
	 */
	protected function upgrade_to_1_11_0() {

		$legacy_api_key_option = 'wc_twilio_sms_shortener_api_key';

		update_option( 'wc_twilio_sms_google_url_shortener_api_key', get_option( $legacy_api_key_option, '' ) );
		update_option( 'wc_twilio_sms_firebase_dynamic_links_api_key', '' );
		update_option( 'wc_twilio_sms_firebase_dynamic_links_domain', '' );

		delete_option( $legacy_api_key_option );
	}


	/**
	 * Updates to version 1.12.2
	 *
	 * @since 1.12.2
	 */
	protected function upgrade_to_1_12_2() {

		// TODO remove this option in a future version as Google URL Shortener support is dropped {FN 2019-06-12}
		update_option( 'wc_twilio_sms_keep_google_url_shortener_service', 'yes' );

		update_option( 'wc_twilio_sms_allow_concatenate_messages', 'no' );
	}


}
