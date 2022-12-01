<?php
/**
 * WooCommerce Memberships
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Memberships lifecycle upgrades.
 *
 * This class handles actions triggered upon plugin updates from an earlier to the current latest version.
 *
 * @since 1.6.2
 *
 * @method \WC_Memberships get_plugin()
 */
class WC_Memberships_Upgrade extends Framework\Plugin\Lifecycle {


	/**
	 * Lifecycle handler constructor.
	 *
	 * @since 1.11.0
	 *
	 * @param \WC_Memberships $wc_memberships
	 */
	public function __construct( $wc_memberships ) {

		parent::__construct( $wc_memberships );

		$this->upgrade_versions = [
			'1.1.0',
			'1.4.0',
			'1.7.0',
			'1.9.0',
			'1.9.2',
			'1.10.0',
			'1.10.5',
			'1.11.1',
			'1.13.2',
			'1.16.2',
			'1.19.0',
			'1.20.0',
			'1.21.0',
		];
	}


	/**
	 * Handles plugin activation.
	 *
	 * @internal
	 *
	 * @since 1.11.0
	 */
	public function activate() {

		$is_active = get_option( 'wc_memberships_is_active', false );

		if ( ! $is_active ) {

			update_option( 'wc_memberships_is_active', true );

			/**
			 * Runs when Memberships is activated.
			 *
			 * @since 1.0.0
			 */
			do_action( 'wc_memberships_activated' );

			$this->get_plugin()->add_rewrite_endpoints();

			flush_rewrite_rules();

			// flush caches
			$this->get_plugin()->get_restrictions_instance()->delete_public_content_cache();
			$this->get_plugin()->get_member_discounts_instance()->delete_excluded_member_discounts_products_cache();
		}
	}


	/**
	 * Handles plugin deactivation.
	 *
	 * @internal
	 *
	 * @since 1.11.0
	 */
	public function deactivate() {

		delete_option( 'wc_memberships_is_active' );

		/**
		 * Runs when Memberships is deactivated.
		 *
		 * @since 1.0.0
		 */
		do_action( 'wc_memberships_deactivated' );

		flush_rewrite_rules();
	}


	/**
	 * Installs default settings & pages.
	 *
	 * @since 1.11.0
	 */
	protected function install() {

		// install default "content restricted" page
		$title   = _x( 'Content restricted', 'Page title', 'woocommerce-memberships' );
		$slug    = _x( 'content-restricted', 'Page slug', 'woocommerce-memberships' );
		$content = '[wcm_content_restricted]';

		wc_create_page( esc_sql( $slug ), 'wc_memberships_redirect_page_id', $title, $content );

		// show a notice about restricted content to admin users as they get started
		update_option( 'wc_memberships_admin_restricted_content_notice', 'yes' );

		// default option to the my account members area endpoint
		update_option( 'woocommerce_myaccount_members_area_endpoint', 'members-area' );

		// default option to the my account profile fields area endpoint
		update_option( 'woocommerce_myaccount_profile_fields_area_endpoint', 'my-profile' );

		// load settings and install default values
		include_once( WC()->plugin_path() . '/includes/admin/settings/class-wc-settings-page.php' );

		/* @type \WC_Settings_Memberships $settings_page */
		$settings_page     = $this->get_plugin()->load_class( '/src/admin/class-wc-memberships-settings.php', 'WC_Settings_Memberships' );
		$settings_sections = array_keys( $settings_page->get_sections() );

		foreach ( $settings_sections as $section ) {

			$settings = $settings_page->get_settings( $section );

			// special handling for messages
			if ( 'messages' === $section ) {

				foreach ( $settings as $i => $settings_data ) {

					if (    isset( $settings_data['id'], $settings_data['default'] )
					     && Framework\SV_WC_Helper::str_ends_with( $settings_data['id'], ']' ) ) {

						unset( $settings[ $i ] );
					}
				}
			}

			$this->install_default_settings( $settings );
		}

		// wipe a membership plan ID that may have been created in the setup wizard in case a user chooses to start over
		if ( $wizard = $this->get_plugin()->get_setup_wizard_handler() ) {

			$wizard->delete_my_first_membership_plan_id();
		}

		// filesystem
		self::create_files();
	}


	/**
	 * Runs upgrade scripts.
	 *
	 * @since 1.11.0
	 *
	 * @param string $installed_version semver
	 */
	protected function upgrade( $installed_version ) {

		parent::upgrade( $installed_version );

		$this->get_plugin()->add_rewrite_endpoints();

		flush_rewrite_rules();

		// flush caches
		$this->get_plugin()->get_restrictions_instance()->delete_public_content_cache();
		$this->get_plugin()->get_member_discounts_instance()->delete_excluded_member_discounts_products_cache();
	}


	/**
	 * Creates files/directories.
	 *
	 * Based on WC_Install::create_files()
	 *
	 * @since 1.13.2
	 */
	private static function create_files() {

		self::create_access_protected_uploads_dir( 'memberships_csv_exports' );
		self::create_access_protected_uploads_dir( 'memberships_profile_fields' );
	}


	/**
	 * Creates a directory with access protection files in WordPress uploads.
	 *
	 * Adds files to loosely protect a directory from access:
	 * - empty "index.html"
	 * - .htaccess with "deny from all"
	 *
	 * Helper method, do not open to public.
	 *
	 * @since 1.19.0
	 *
	 * @param string $dir
	 */
	public static function create_access_protected_uploads_dir( $dir ) {

		// install files and folders for exported files and prevent hotlinking
		$upload_dir = wp_upload_dir();
		$directory  = trailingslashit( $upload_dir['basedir'] ) . $dir;

		$files = [
			[
				'base'    => $directory,
				'file'    => 'index.html',
				'content' => '',
			],
			[
				'base'    => $directory,
				'file'    => '.htaccess',
				'content' => 'deny from all',
			],
		];

		foreach ( $files as $file ) {

			if ( wp_mkdir_p( $file['base'] ) && ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {

				if ( $file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' ) ) {

					fwrite( $file_handle, $file['content'] );
					fclose( $file_handle );
				}
			}
		}
	}


	/**
	 * Updates to v1.1.0
	 *
	 * @since 1.13.0
	 */
	protected function upgrade_to_1_1_0() {

		$all_rules = array();

		// merge rules from different options into a single option
		foreach ( array( 'content_restriction', 'product_restriction', 'purchasing_discount' ) as $rule_type ) {

			$rules = get_option( "wc_memberships_{$rule_type}_rules" );

			if ( is_array( $rules ) && ! empty( $rules ) ) {

				foreach ( $rules as $rule ) {

					// skip empty/corrupt rules
					if ( empty( $rule ) || ( isset( $rule[0] ) && ! $rule[0] ) ) {
						continue;
					}

					$rule['rule_type'] = $rule_type;
					$all_rules[] = $rule;
				}
			}

			delete_option( "wc_memberships_{$rule_type}_rules" );
		}

		update_option( 'wc_memberships_rules', $all_rules );
	}


	/**
	 * Updates to v1.4.0
	 *
	 * @since 1.13.0
	 */
	protected function upgrade_to_1_4_0() {

		// product category custom restriction messages in settings options
		update_option( 'wc_memberships_product_category_viewing_restricted_message', __( 'This product category can only be viewed by members. To view this category, sign up by purchasing {products}.', 'woocommerce-memberships' ) );
		update_option( 'wc_memberships_product_category_viewing_restricted_message_no_products', __( 'Displays if viewing a product category is restricted to a membership that cannot be purchased.', 'woocommerce-memberships' ) );
	}


	/**
	 * Updates to v1.7.0
	 *
	 * This will transition legacy Memberships expiry events set on WP Cron to utilize the newer Action Scheduler.
	 *
	 * The update won't unschedule the memberships expiration events to prevent possible timeouts or out of memory errors on very large installs while the wp cron array in option has to be updated several times.
	 * However, such events won't have a callback attached anymore and thus gracefully disappear when they are naturally due.
	 *
	 * @since 1.13.0
	 */
	protected function upgrade_to_1_7_0() {

		// get all wp cron events to process the memberships expiry ones
		$cron_events = get_option( 'cron' );

		// this would hardly happen on a healthy install...
		if ( empty( $cron_events ) ) {
			return;
		}

		$this->get_plugin()->log( sprintf( 'Starting upgrade to 1.7.0 for %d events', count( $cron_events ) ) );

		// process 50 events at one time, so in case of timeouts
		// one can always resume the script by activating again...
		do {

			$key_offset   = (int) get_option( 'wc_memberships_cron_offset', 0 );
			$events_chunk = array_slice( $cron_events, $key_offset, 50, true );

			if ( empty( $events_chunk ) ) {
				break;
			}

			// process the chunk of events
			foreach ( $events_chunk as $timestamp => $scheduled ) {

				// convert memberships expiry events to use the Action Scheduler
				if ( is_array( $scheduled ) && 'wc_memberships_user_membership_expiry' === key( $scheduled ) ) {

					$expiration_event   = array_values( current( $scheduled ) );
					$user_membership_id = isset( $expiration_event[0]['args'][0] ) ? $expiration_event[0]['args'][0] : null;

					if ( is_numeric( $user_membership_id ) && $user_membership = wc_memberships_get_user_membership( $user_membership_id ) ) {

						// re-schedule events using the action scheduler
						$user_membership->schedule_expiration_events( (int) $timestamp );
					}
				}
			}

			// update offset to move the pointer 50 items forward in the next batch
			update_option( 'wc_memberships_cron_offset', $key_offset + 50 );

		} while ( count( $events_chunk ) === 50 );

		// once the while loop is complete we can delete the offset option
		delete_option( 'wc_memberships_cron_offset' );
	}


	/**
	 * Updates to 1.9.0
	 *
	 * - Move all user messages into a single option array and remove obsolete option keys.
	 * - Adds a "Details" members area section that became available in the new version.
	 * - Compacts rules for all plans to improve plan handling and general performance.
	 *
	 * @since 1.13.0
	 */
	protected function upgrade_to_1_9_0() {

		$new_messages    = array();
		$legacy_messages = array(
			'member_login_message',
			'content_restricted_message',
			'page_content_restricted_message',
			'post_content_restricted_message',
			'content_restricted_message_no_products',
			'page_content_restricted_message_no_products',
			'post_content_restricted_message_no_products',
			'content_delayed_message',
			'page_content_delayed_message',
			'post_content_delayed_message',
			'product_discount_message',
			'product_discount_message_no_products',
			'product_purchasing_delayed_message',
			'product_purchasing_restricted_message',
			'product_purchasing_restricted_message_no_products',
			'product_viewing_delayed_message',
			'product_viewing_restricted_message',
			'product_viewing_restricted_message_no_products',
		);
		$unused_options  = array(
			'memberships_options',
			'memberships_products_options',
			'memberships_messages',
			'memberships_other_messages',
			'memberships_page_restriction_messages',
			'memberships_post_restriction_messages',
			'memberships_product_messages',
			'memberships_restriction_messages',
			'product_category_viewing_delayed_message',
			'product_category_viewing_restricted_message',
			'product_category_viewing_restricted_message_no_products',
			'wc_memberships_subscriptions_version',
			'wc_memberships_product_category_delayed_message',
			'wc_memberships_product_category_restricted_message',
			'wc_memberships_product_category_restricted_message_no_products',
		);

		foreach ( $legacy_messages as $message_code ) {

			// we use one key for both product purchasing delayed and product viewing delayed cases
			if ( 'product_purchasing_delayed_message' === $message_code ) {
				$message_code = 'product_access_delayed_message';
			} elseif ( 'product_viewing_delayed_message' === $message_code ) {
				continue;
			}

			$option_key     = "wc_memberships_{$message_code}";
			$legacy_message = get_option( $option_key, \WC_Memberships_User_Messages::get_message( $message_code ) );

			$new_messages[ $message_code ] = $legacy_message;

			$unused_options[] = $option_key;
		}

		// update messages in a single array
		update_option( 'wc_memberships_messages', $new_messages );

		// delete legacy options
		foreach ( $unused_options as $legacy_option ) {
			delete_option( $legacy_option );
		}

		$this->get_plugin()->log( 'Moved all user messages into a single option' );

		// add the new "Manage" membership members area section to existing plans
		$plans = $this->get_plugin()->get_plans_instance()->get_membership_plans( array( 'post_status' => 'any' ) );

		foreach ( $plans as $plan ) {
			$plan->set_members_area_sections( array_merge( $plan->get_members_area_sections(), array( 'my-membership-details' ) ) );
		}

		$this->get_plugin()->log( 'Updated membership plans members area sections' );

		// optimize the plan rules using the new rules compacting feature
		$this->get_plugin()->get_rules_instance()->compact_rules();

		$this->get_plugin()->log( 'Compacted membership plans rules' );
	}


	/**
	 * Updates to 1.9.2
	 *
	 * Repair custom taxonomy product rules that may have been corrupted after saving in 1.9.0
	 *
	 * @since 1.13.0
	 */
	protected function upgrade_to_1_9_2() {

		$raw_rules = get_option( 'wc_memberships_rules' );

		// back up the rules, just in case (will be deleted in 1.10.0 upgrade path)
		update_option( 'wc_memberships_rules_backup', $raw_rules );

		// get all product rules
		// non-taxonomy rules are filtered out below
		$product_rules = $this->get_plugin()->get_rules_instance()->get_rules( array(
			'rule_type' => array(
				'product_restriction',
				'purchasing_discount',
			),
		) );

		foreach ( $product_rules as $rule_key => $rule ) {

			// sanity check, or if the rule has a taxonomy name already, there's nothing to repair
			if ( ! $rule instanceof \WC_Memberships_Membership_Plan_Rule || 'taxonomy' !== $rule->get_content_type() || $rule->get_content_type_name() ) {
				continue;
			}

			$term_ids = $rule->get_object_ids();

			// nothing we can do if there are no terms to check
			if ( empty( $term_ids ) ) {
				continue;
			}

			$term = get_term( current( $term_ids ) );

			if ( $term && ! empty( $term->taxonomy ) && ! is_wp_error( $term ) ) {

				$product_rules[ $rule_key ]->set_content_type_name( $term->taxonomy );
				continue;
			}
		}

		$this->get_plugin()->get_rules_instance()->update_rules( $product_rules );
	}


	/**
	 * Updates to 1.10.0
	 *
	 * @since 1.13.0
	 */
	protected function upgrade_to_1_10_0() {

		delete_option( 'wc_memberships_rules_backup' );
	}


	/**
	 * Updates to 1.10.5
	 *
	 * @since 1.13.0
	 */
	protected function upgrade_to_1_10_5() {

		delete_option( 'wc_memberships_product_category_viewing_restricted_message' );
		delete_option( 'wc_memberships_product_category_viewing_restricted_message_no_products' );
	}


	/**
	 * Updates to 1.11.0
	 *
	 * @since 1.13.0
	 */
	protected function upgrade_to_1_11_0() {

		// skips the wizard if not a new installation
		if ( $wizard = $this->get_plugin()->get_setup_wizard_handler() ) {

			$wizard->complete_setup();
		}
	}


	/**
	 * Updates to version 1.13.2
	 *
	 * - Creates .htaccess and index.php files in the exports directory.
	 * - Renames a WP Cron event and moves it into an Action Scheduler task.
	 *
	 * @since 1.13.2
	 */
	protected function upgrade_to_1_13_2() {

		self::create_files();

		$legacy_wp_cron_hook = 'wc_memberships_activate_delayed_user_memberships';
		$new_as_task_hook    = 'wc_memberships_activate_delayed_user_membership';

		if ( $next_scheduled = wp_next_scheduled( $legacy_wp_cron_hook ) ) {

			wp_unschedule_event( $next_scheduled, $legacy_wp_cron_hook );

			as_schedule_single_action(
				// schedules activation of all delayed memberships 10 minutes after the upgrade routine is complete (may be worth to give a little time in case the user is updating multiple plugins, etc.)
				max( 0, $next_scheduled, current_time( 'timestamp', true ) ) + ( 10 * MINUTE_IN_SECONDS ),
				$new_as_task_hook,
				[],
				'woocommerce-memberships'
			);
		}
	}


	/**
	 * Updates to version 1.16.2
	 *
	 * Logs whether the installation was found running Action Scheduler when 1.16.0 was deployed bundling AS 3.0.0-beta.
	 *
	 * TODO remove this upgrade script when requiring WooCommerce 4.0+ and delete the option "wc_memberships_use_as_3_0_0" {FN 2020-11-17}
	 *
	 * @since 1.16.2
	 *
	 * @param null|string $upgrading_from version installed
	 */
	protected function upgrade_to_1_16_2( $upgrading_from = null ) {

		if ( in_array( $upgrading_from, [ '1.16.0', '1.16.1' ], false ) && 'yes' === get_option( 'wc_memberships_use_as_3_0_0' ) ) {
			$this->get_plugin()->log( 'Action Scheduler 3.0.0 will be used after Memberships 1.16.0 update' );
		} else {
			update_option( 'wc_memberships_use_as_3_0_0', 'no' );
		}
	}


	/**
	 * Updates to version 1.19.0
	 *
	 * @since 1.19.0
	 */
	protected function upgrade_to_1_19_0() {

		self::create_access_protected_uploads_dir( 'memberships_profile_fields' );

		update_option( 'woocommerce_myaccount_profile_fields_area_endpoint', 'my-profile' );
	}


	/**
	 * Updates to version 1.20.0
	 *
	 * @since 1.20.0
	 */
	protected function upgrade_to_1_20_0() {

		// Jilt Promotions flags
		delete_option( 'wc_memberships_show_advanced_emails_notice' );
		delete_option( 'wc_memberships_show_jilt_cross_sell_notice' );
	}


	/**
	 * Updates to version 1.21.0
	 *
	 * @since 1.21.0
	 */
	protected function upgrade_to_1_21_0() {

		$found_plugins = [];
		$free_add_ons  = [
			'directory-shortcode',
			'excerpt-length',
			'role-handler',
			'sensei-member-area',
		];

		foreach ( $free_add_ons as $i => $free_add_on ) {

			$prefix   = 'woocommerce-memberships';
			$dirname  = "{$prefix}-{$free_add_on}";
			$filename = $dirname . '.php';

			if ( $this->get_plugin()->is_plugin_active( $filename ) ) {

				// deactivate the plugin if found active
				$found_plugins[] = $dirname . '/' . $filename;

				// special handling for role handler: it will be enabled for users that migrated
				if ( 'role-handler' === $free_add_on ) {
					update_option( 'wc_memberships_assign_user_roles_to_members', 'yes' );
				}

			} elseif ( ! $this->get_plugin()->is_plugin_installed( $filename ) ) {

				// do not track the plugin if not installed
				unset( $free_add_ons[ $i ] );
			}
		}

		if ( ! empty( $found_plugins ) ) {

			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

			deactivate_plugins( $found_plugins );
		}

		if ( ! empty( $free_add_ons ) ) {
			update_option( 'wc_memberships_installed_free_add_ons_migrated', $free_add_ons );
		}

		// migrates Memberships Role Handler settings
		update_option( 'wc_memberships_active_member_user_role',   get_option( 'wc_memberships_role_handler_member_role', 'customer' ) );
		update_option( 'wc_memberships_inactive_member_user_role', get_option( 'wc_memberships_role_handler_inactive_role', 'customer' ) );
		delete_option( 'wc_memberships_role_handler_member_role' );
		delete_option( 'wc_memberships_role_handler_inactive_role' );

		// cleanup: remove version options
		delete_option( 'wc_memberships_role_handler_version' );
		delete_option( 'wc_memberships_directory_shortcode_version' );
		delete_option( 'wc_memberships_sensei_member_area_version' );
		delete_option( 'wc_memberships_excerpt_length_version' );
	}


}
