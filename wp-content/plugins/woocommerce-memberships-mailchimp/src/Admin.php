<?php
/**
 * MailChimp for WooCommerce Memberships
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
 * Do not edit or add to this file if you wish to upgrade MailChimp for WooCommerce Memberships to newer
 * versions in the future. If you wish to customize MailChimp for WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/mailchimp-for-woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2017-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships\MailChimp;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Main admin handler.
 *
 * @since 1.0.0
 */
class Admin {


	/** @var Admin\Settings instance */
	private $settings;

	/** @var array color codes to indicate success/failure used across the plugin admin */
	private $status_color_codes;

	/** @var Admin\Membership_Plans instance */
	private $membership_plans;

	/** @var Admin\User_Memberships instance */
	private $user_memberships;


	/**
	 * Hook in admin screens and events.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->status_color_codes = [
			'default' => '#333333', // light black
			'success' => '#008000', // green
			'failure' => '#DC3232', // red
		];

		// load admin components
		if ( Framework\SV_WC_Plugin_Compatibility::is_wc_version_gte( '3.7' ) ) {
			$this->init_settings();
			add_action( 'current_screen', [ $this, 'includes' ] );
		} else {
			add_action( 'admin_init',     [ $this, 'init_settings' ] );
			add_action( 'current_screen', [ $this, 'includes' ] );
		}

		// display admin messages
		add_action( 'admin_notices', array( $this, 'show_admin_messages' ) );

		// enqueue admin scripts & styles
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_and_styles' ) );

		// WordPress would automatically convert some HTML entities into emoji in the settings page
		add_action( 'init', array( $this, 'disable_settings_wp_emoji' ) );
	}


	/**
	 * Returns a color hexadecimal value.
	 *
	 * @since 1.0.0
	 *
	 * @param $status
	 * @return string an hex color code, default light black
	 */
	public function get_status_color_code( $status ) {

		return isset( $this->status_color_codes[ $status ] ) ? $this->status_color_codes[ $status ] : $this->status_color_codes['default'];
	}


	/**
	 * Initializes the plugin settings admin.
	 *
	 * @internal
	 *
	 * @since 1.1.0
	 */
	public function init_settings() {

		$this->settings = new Admin\Settings();
	}


	/**
	 * Loads admin includes.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function includes() {
		global $current_screen;

		if ( $current_screen ) {

			switch ( $current_screen->id ) {

				case 'wc_membership_plan' :
				case 'edit-wc_membership_plan' :
					$this->membership_plans = new Admin\Membership_Plans();
				break;

				case 'wc_user_membership' :
				case 'edit-wc_user_membership' :
					$this->user_memberships = new Admin\User_Memberships();
				break;
			}
		}
	}


	/**
	 * Returns the settings instance.
	 *
	 * @since 1.0.0
	 *
	 * @return null|Admin\Settings
	 */
	public function get_settings_instance() {

		// allow use of settings in AJAX
		if ( null === $this->settings && is_ajax() ) {
			$this->init_settings();
		}

		return $this->settings;
	}


	/**
	 * Returns the membership plans admin handler instance.
	 *
	 * @since 1.0.0
	 *
	 * @return null|Admin\Membership_Plans
	 */
	public function get_membership_plans_instance() {

		return $this->membership_plans;
	}


	/**
	 * Returns the user memberships admin handler instance.
	 *
	 * @since 1.0.0
	 *
	 * @return null|Admin\User_Memberships
	 */
	public function get_user_memberships_instance() {

		return $this->user_memberships;
	}


	/**
	 * Displays admin messages.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function show_admin_messages() {

		$plugin  = wc_memberships_mailchimp();
		$screen  = get_current_screen();
		$screens = array(
			'plugins',
			'wc_user_membership',
			'edit-wc_user_membership',
			'wc_membership_plan',
			'edit-wc_membership_plan'
		);

		if ( ( $screen && in_array( $screen->id, $screens, true ) ) || $plugin->is_plugin_settings() ) {

			if ( wc_memberships_mailchimp()->is_connected() ) {

				// when updating from 1.0.0 to 1.0.1, remind to sync members to set default opt in preference
				if ( ! $plugin->is_plugin_settings() && ( $sync_members_opt_in = get_option( '_wc_memberships_mailchimp_sync_needs_members_sync' ) ) ) {

					/* translators: Placeholders: %1$s - opening HTML <a> link tag, %2$s - plugin name, %3$s - closing HTML </a> link tag, %4$s - "Sync Now" button name */
					$message = sprintf( __( '%1$s has been upgraded! Please visit the %2$ssettings page%3$s and click on the %4$s button to complete the upgrade.', 'woocommerce-memberships-mailchimp' ),
						wc_memberships_mailchimp()->get_plugin_name(),
						'<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=memberships&section=mailchimp-sync#wc-memberships-mailchimp-sync-sync-members' ) ) . '">',
						'</a>',
						'<strong>' . __( 'Sync Now', 'woocommerce-memberships-mailchimp' ) . '</strong>'
					);

					wc_memberships_mailchimp()->get_admin_notice_handler()->add_admin_notice( $message, 'upgrade-to-1-0-1', array( 'always_show_on_settings' => false ) );
				}

				// first check if there are any plans to work with
				if (    ! in_array( $screen->id, array( 'wc_membership_plan', 'edit-wc_membership_plan' ), true )
				     &&   0 === wc_memberships()->get_plans_instance()->get_membership_plans_count() ) {

					/* translators: Placeholders: %1$s - opening <a> link tag, %2$s - closing </a> link tag */
					$message = sprintf( __( 'It looks like you have no membership plan published in your Memberships setup. MailChimp Sync requires at least one plan to work. %1$sAdd a Membership Plan%2$s.', 'woocommerce-memberships-mailchimp' ), '<a href="' . admin_url( 'edit.php?post_type=wc_membership_plan' ) . '">', '</a>' );

					wc_memberships_mailchimp()->get_message_handler()->add_error( $message );
				}

				// check if any audience lists are available
				$lists = $plugin->get_api_instance()->get_lists();

				if ( empty( $lists ) ) {

					$message = __( 'It looks like you do not have any MailChimp audience available. MailChimp for WooCommerce Memberships needs at least one audience to work.', 'woocommerce-memberships-mailchimp' );

					wc_memberships_mailchimp()->get_message_handler()->add_error( $message );
				}
			}

			// show messages, including those added by other admin classes in MailChimp Sync admin screens
			$plugin->get_message_handler()->show_messages();
		}
	}


	/**
	 * Enqueues admin scripts and styles.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts_and_styles() {

		$plugin             = wc_memberships_mailchimp();
		$screen             = get_current_screen();
		$is_settings_screen = $plugin->is_plugin_settings();
		$is_members_screens = $screen && in_array( $screen->id, array( 'wc_user_membership', 'edit-wc_user_membership' ), true );
		$is_plans_screens   = $screen && in_array( $screen->id, array( 'wc_membership_plan', 'edit-wc_membership_plan' ), true );

		// MailChimp Sync screens are user memberships, membership plans, memberships settings admin screens
		if ( $is_settings_screen || $is_members_screens || $is_plans_screens ) {

			$path    = $plugin->get_plugin_url();
			$version = $plugin->get_version();

			wp_enqueue_style( 'wc-memberships-mailchimp-sync-admin',  $path . '/assets/css/admin/wc-memberships-mailchimp-sync-admin.min.css', array(), $version );

			if ( $is_settings_screen || $is_members_screens ) {

				if ( $sync_job = wc_memberships_mailchimp()->get_background_sync_instance()->get_job() ) {
					$existing_sync_id = $sync_job->id;
				} else {
					$existing_sync_id = false;
				}

				wp_enqueue_script(  'wc-memberships-mailchimp-sync-admin', $path . '/assets/js/admin/wc-memberships-mailchimp-sync-admin.min.js', array( 'jquery', 'backbone' ), $version );
				wp_localize_script( 'wc-memberships-mailchimp-sync-admin', 'wc_memberships_mailchimp_sync_admin', array(

					'has_api_key'             => wc_memberships_mailchimp()->has_api_key(),
					'is_valid_api_key_nonce'  => wp_create_nonce( 'mailchimp-sync-is-valid-api-key' ),
					'update_list_settings'    => wp_create_nonce( 'mailchimp-sync-update-list-settings' ),
					'sync_member'             => wp_create_nonce( 'mailchimp-sync-update-member' ),
					'start_members_sync'      => wp_create_nonce( 'mailchimp-sync-start-members-sync' ),
					'get_members_sync_status' => wp_create_nonce( 'mailchimp-sync-get-members-sync-status' ),
					'existing_sync_id'        => $existing_sync_id,

					// normalized status colors used in admin
					'status_color'            => array(
						'default'  => $this->get_status_color_code( 'default' ),
						'failure'  => $this->get_status_color_code( 'failure' ),
						'success'  => $this->get_status_color_code( 'success' ),
					),

					// localization strings
					'i18n' => array(
						'sync_error'       => __( 'Oops! Something went wrong. Please try again.', 'woocommerce-memberships-mailchimp' ),
						'sync_success'     => __( 'Member data synced.', 'woocommerce-memberships-mailchimp' ),
						'sync_in_progress' => __( 'Syncing...', 'woocommerce-memberships-mailchimp' ),
					),
				) );
			}
		}
	}


	/**
	 * Prevents the conversion of some HTML entities used in the plugin settings page into emojis.
	 *
	 * This bothers for example the check mark or the cross mark shown next to the API key field.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function disable_settings_wp_emoji() {

		if ( wc_memberships_mailchimp()->is_plugin_settings() ) {

			remove_action( 'admin_print_styles',  'print_emoji_styles' );
			remove_action( 'wp_head',             'print_emoji_detection_script', 7 );
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		}
	}


}
