<?php
/**
 * WooCommerce Social Login
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Social Login to newer
 * versions in the future. If you wish to customize WooCommerce Social Login for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-social-login/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Social_Login;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_12 as Framework;

/**
 * Social Login lifecycle handler.
 *
 * @since 2.6.0
 *
 * @method \WC_Social_Login get_plugin()
 */
class Lifecycle extends Framework\Plugin\Lifecycle {


	/**
	 * Lifecycle constructor.
	 *
	 * @since 2.6.3
	 *
	 * @param \WC_Social_Login $plugin
	 */
	public function __construct( $plugin ) {

		parent::__construct( $plugin );

		$this->upgrade_versions = [
			'1.1.0',
			'2.0.0',
			'2.3.0',
			'2.5.0',
			'2.6.2',
			'2.6.4',
			'2.7.0',
			'2.8.4',
			'2.9.0',
		];
	}


	/**
	 * Installs default settings.
	 *
	 * @since 2.6.0
	 */
	protected function install() {

		// Settings page defaults.
		// Unfortunately we can't dynamically pull these because the requisite core WC classes aren't loaded.
		// A better solution may be to set any defaults within the save method of the social provider settings classes.
		add_option( 'wc_social_login_display',           [ 'checkout', 'my_account' ] );
		add_option( 'wc_social_login_text',              __( 'For faster checkout, login or register using your social account.', 'woocommerce-social-login' ) );
		add_option( 'wc_social_login_text_non_checkout', __( 'Use a social account for faster login or easy registration.', 'woocommerce-social-login' ) );
	}


	/**
	 * Updates to v1.1.0
	 *
	 * @since 2.6.3
	 */
	protected function upgrade_to_1_1_0() {

		$social_login_display = get_option( 'wc_social_login_display', '' );

		if ( is_string( $social_login_display ) ) {

			// display option is now a multiselect
			update_option( 'wc_social_login_display', explode( ',', $social_login_display ) );
		}
	}


	/**
	 * Updates to v2.0.0
	 *
	 * @since 2.6.3
	 */
	protected function upgrade_to_2_0_0() {
		global $wpdb;

		// this install has been upgraded from an opauth-based version,
		// set the callback URL format to legacy to give users time to upgrade
		add_option( 'wc_social_login_upgraded_from_opauth', true );
		add_option( 'wc_social_login_callback_url_format', 'legacy' );

		// vk is now vkontakte
		update_option( 'wc_social_login_vkontakte_settings', get_option( 'wc_social_login_vk_settings' ) );
		delete_option( 'wc_social_login_vk_settings' );

		// Social provider uid and full_profile have been renamed in user meta.
		// Also, profile fields have been readjusted.
		foreach ( array_keys( $this->get_plugin()->get_providers() ) as $provider_id ) {

			$provider_id = esc_attr( $provider_id );
			$old_id      = 'vkontakte' === $provider_id ? 'vk' : $provider_id;

			// remove old profiles
			$wpdb->query( "
				DELETE FROM $wpdb->usermeta
				WHERE meta_key = '_wc_social_login_{$old_id}_profile_full'
			" );

			// rename uid => identifier
			$wpdb->query( "
				UPDATE $wpdb->usermeta
				SET meta_key = '_wc_social_login_{$provider_id}_identifier'
				WHERE meta_key = '_wc_social_login_{$old_id}_uid'
			" );

			// for vkontakte, also update the profile_image meta
			if ( 'vkontakte' === $provider_id ) {

				// options that need to be renamed/updated
				$vk_options = [ 'profile', 'profile_image', 'login_timestamp', 'login_timestamp_gmt' ];

				foreach ( $vk_options as $option_name ) {

					$wpdb->query( "
						UPDATE $wpdb->usermeta
						SET meta_key = '_wc_social_login_{$provider_id}_{$option_name}'
						WHERE meta_key = '_wc_social_login_{$old_id}_{$option_name}'
					" );
				}
			}

			// restructure profiles
			// TODO: this can potentially timeout on large customer bases, perhaps refactor? {IT 2016-10-08}
			$results = $wpdb->get_results( "
				SELECT user_id, meta_value
				FROM $wpdb->usermeta
				WHERE meta_key = '_wc_social_login_{$provider_id}_profile'
			" );

			if ( ! empty( $results ) ) {

				foreach ( $results as $row ) {

					$profile = maybe_unserialize( $row->meta_value );

					if ( isset( $profile['nickname'] ) ) {

						$profile['display_name'] = $profile['nickname'];

						unset( $profile['nickname'] );
					}

					if ( isset( $profile['location'] ) ) {

						$profile['city'] = $profile['location'];

						unset( $profile['location'] );
					}

					if ( isset( $profile['image'] ) ) {

						$profile['photo_url'] = $profile['image'];

						unset( $profile['image'] );
					}

					if ( isset( $profile['urls'] ) ) {

						if ( isset( $profile['urls']['website'] ) ) {
							$profile['web_site_url'] = $profile['urls']['website'];
						}

						if ( isset( $profile['urls'][ $provider_id ] ) ) {
							$profile['profile_url'] = $profile['urls'][ $provider_id ];
						}

						unset( $profile['urls'] );
					}

					unset( $profile['provider'] );

					update_user_meta( $row->user_id, '_wc_social_login_' . $provider_id . '_profile', $profile );
				}
			}
		}
	}


	/**
	 * Updates to v2.3.0
	 *
	 * @since 2.6.3
	 */
	protected function upgrade_to_2_3_0() {

		// add new option to display login text on non-checkout pages
		add_option( 'wc_social_login_text_non_checkout', __( 'Use a social account for faster login or easy registration.', 'woocommerce-social-login' ) );
	}


	/**
	 * Updates to v2.5.0
	 *
	 * @since 2.6.3
	 */
	protected function upgrade_to_2_5_0() {

		// add new option that allows appending Social Login buttons to Memberships restriction messages
		add_option( 'wc_social_login_append_buttons_memberships_restriction_messages', 'yes' );
	}


	/**
	 * Updates to v2.6.2
	 *
	 * Handles PayPal API updated scope.
	 *
	 * @since 2.6.3
	 */
	protected function upgrade_to_2_6_2() {

		$paypal_provider = wc_social_login()->get_provider( 'paypal' );
		$paypal_settings = get_option( 'wc_social_login_paypal_settings', [] );

		// existing installations that use PayPal will preserve its scope attribute mappings which otherwise will default to `profile` only
		if ( $paypal_provider && ! empty( $paypal_settings ) && is_array( $paypal_settings ) && $paypal_provider->is_available() ) {

			$paypal_settings['scope'] = 'profile email address phone';

			update_option( 'wc_social_login_paypal_settings', $paypal_settings );
		}
	}


	/**
	 * Updates to v2.6.4
	 *
	 * Handles LinkedIn API v2.
	 *
	 * @since 2.6.4
	 */
	protected function upgrade_to_2_6_4() {

		$linkedin_provider = wc_social_login()->get_provider( 'linkedin' );
		$linkedin_settings = get_option( 'wc_social_login_linkedin_settings', [] );

		// for existing installations, assume API v1 based client if LinkedIn has been configured and is currently available
		if ( $linkedin_provider && ! empty( $linkedin_settings ) && is_array( $linkedin_settings ) && $linkedin_provider->is_available() ) {

			$linkedin_settings['api_version'] = 'v1';

			update_option( 'wc_social_login_linkedin_settings', $linkedin_settings );

			update_option( 'wc_social_login_upgraded_linkedin', 'yes' );
		}
	}


	/**
	 * Updates to v2.7.0
	 *
	 * @since 2.7.0
	 */
	protected function upgrade_to_2_7_0() {

		// skips the wizard if not a new installation
		if ( $wizard = $this->get_plugin()->get_setup_wizard_handler() ) {

			$wizard->complete_setup();
		}
	}


	/**
	 * Updates to v2.8.4
	 *
	 * Flags Instagram to be hidden on installations where Instagram is not currently used due to API deprecation.
	 * If Instagram is found to be enabled (even if misconfigured), then the flag will tell Social Login to keep the service enabled and the merchant warned.
	 * @see Lifecycle::upgrade_to_2_9_0()
	 *
	 * @since 2.8.4
	 */
	protected function upgrade_to_2_8_4() {

		$instagram_settings = get_option( 'wc_social_login_instagram_settings', [] );
		$keep_instagram     = isset( $instagram_settings['enabled'] ) && 'yes' === $instagram_settings['enabled'] ? 'yes' : 'no';

		// if existing installs are not using Instagram, we can remove this provider as its login API is deprecated
		update_option( 'wc_social_login_allow_instagram', $keep_instagram );
	}


	/**
	 * Updates to v2.9.0
	 *
	 * Removes Instagram support.
	 *
	 * @since 2.9.0
	 */
	protected function upgrade_to_2_9_0() {

		$instagram_settings = get_option( 'wc_social_login_instagram_settings',  [] );

		// we can't access Instagram via PHP handlers since they've been removed
		if ( isset( $instagram_settings['enabled'] ) && 'yes' === $instagram_settings['enabled'] ) {
			update_option( 'wc_social_login_instagram_removed', 'yes' );
		}

		// completely remove settings and previous options
		delete_option( 'wc_social_login_instagram_settings' );
		delete_option( 'wc_social_login_allow_instagram' );
	}


}
