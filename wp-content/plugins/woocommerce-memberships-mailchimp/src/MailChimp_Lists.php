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
 * MailChimp audience lists handler.
 *
 * @see MailChimp_List
 *
 * @since 1.0.0
 */
class MailChimp_Lists {


	/** @var MailChimp_List[] array of audience objects indexed by their ID */
	private static $lists = array();

	/** @var string settings option key */
	private static $settings_option = 'wc_memberships_mailchimp_sync_list_settings';

	/** @var array list of saved preferences for available MailChimp audience lists */
	private static $settings = array();

	/** @var string the default audience option key */
	private static $default_list_option_key = 'wc_memberships_mailchimp_sync_default_list';

	/** @var string audience lists names transient key name */
	public static $names_transient = 'wc_memberships_mailchimp_sync_list_name';

	/** @var string audience lists interest categories transient key name */
	public static $interest_categories_transient = 'wc_memberships_mailchimp_sync_interest_categories';

	/** @var string audience lists interests transient key name */
	public static $interests_transient = 'wc_memberships_mailchimp_sync_interests';

	/** @var string audience lists merge fields transient key name */
	public static $merge_fields_transient = 'wc_memberships_mailchimp_sync_merge_fields';

	/** @var string plans merge fields transient key name */
	public static $plans_merge_tags_transient = 'wc_memberships_mailchimp_sync_plans_merge_tags';


	/**
	 * Returns the default audience ID.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public static function get_current_list_id() {

		return (string) get_option( self::$default_list_option_key, '' );
	}


	/**
	 * Returns the settings option key for MailChimp audience lists preferences.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_list_settings_option() {

		return self::$settings_option;
	}


	/**
	 * Returns the saved preferences for available MailChimp audience lists.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public static function get_lists_settings() {

		if ( empty( self::$settings ) ) {
			self::$settings = get_option( self::$settings_option, array() );
		}

		return self::$settings;
	}


	/**
	 * Removes the saved preferences for available MailChimp audience lists.
	 *
	 * @since 1.0.0
	 */
	public static function delete_lists_settings()  {

		delete_option( self::$settings_option );

		self::$settings = array();
	}


	/**
	 * Updates the preferences for a MailChimp audience list.
	 *
	 * @since 1.0.0
	 *
	 * @param string $list_id the audience ID
	 * @param array $data array of data to update
	 */
	public static function update_list_settings( $list_id, array $data ) {

		$settings             = self::get_lists_settings();
		$settings[ $list_id ] = $data;

		update_option( self::$settings_option, $settings );

		self::$settings = $settings;
	}


	/**
	 * Returns available MailChimp audience lists.
	 *
	 * @since 1.0.0
	 *
	 * @return MailChimp_List[]
	 */
	public static function get_lists() {

		if ( empty( self::$lists ) ) {

			$lists = wc_memberships_mailchimp()->get_api_instance()->get_lists();

			self::$lists = array();

			if ( ! empty( $lists ) ) {

				foreach ( $lists as $list ) {

					if ( isset( $list->id ) ) {
						self::$lists[ $list->id ] = new MailChimp_List( $list->id );
					}
				}
			}
		}

		return self::$lists;
	}


	/**
	 * Returns a MailChimp audience list object by its ID.
	 *
	 * @since 1.0.0
	 *
	 * @param string|null $list_id optional ID of the audience to retrieve, defaults to the current audience to use
	 * @return null|MailChimp_List
	 */
	public static function get_list( $list_id = null ) {

		$list_id = null === $list_id ? self::get_current_list_id() : $list_id;

		if ( empty( self::$lists ) ) {
			self::$lists = self::get_lists();
		}

		return ! empty( $list_id ) && isset( self::$lists[ $list_id ] ) ? self::$lists[ $list_id ] : null;
	}


	/**
	 * Parses a string to normalize a merge tag.
	 *
	 * - Removes spaces and non alphanumeric characters
	 * - Trims the string to maximum 10 characters (MailChimp limit)
	 * - Changes text to uppercase
	 *
	 * e.g. `my-1st-plan-slug` becomes `MY1STPLANS`
	 *
	 * TODO improve the human handling of this: for example, assume spaces, dashes and underscores to separate words and perhaps cut to less than 10 characters to keep the resulting output more meaningful, `MY1STPLAN` in the example above {FN 2018-01-11}
	 *
	 * @since 1.0.0
	 *
	 * @param string $tag merge tag
	 * @return string
	 */
	public static function parse_merge_tag( $tag ) {

		$merge_tag = '';

		if ( '' !== $tag && is_string( $tag ) ) {
			$merge_tag = strtoupper( substr( preg_replace('/[^\da-z]/i', '', $tag ), 0, 10 ) );
		}

		return $merge_tag;
	}


	/**
	 * Returns the default is_active tag to segment active members.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public static function get_default_is_active_tag() {

		return self::parse_merge_tag(
			_x( 'ISACTIVE', 'The default merge tag to be used to segment active members: this should be an uppercase alphanumeric string of maximum 10 characters without spaces or dashes.', 'woocommerce-memberships-mailchimp' )
		);
	}


	/**
	 * Returns the default member data used when syncing a member to an audience.
	 *
	 * This is normally combined with audience settings and user memberships data.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_User $member user object
	 * @return array
	 */
	public static function get_list_default_member_data( \WP_User $member ) {

		$country_code = get_user_meta( $member->ID, 'billing_country', true );
		$member_data  = array(
			'email_address' => $member->user_email,
			'language'      => strtolower( substr( get_locale(), 0, 2 ) ),
			'location'      => array(
				'latitude'     => 0,
				'longitude'    => 0,
				'gmtoff'       => 0,
				'dstoff'       => 0,
				'country_code' => is_string( $country_code ) ? $country_code : '',
				'timezone'     => wc_timezone_string(),
			),
			'merge_fields'  => array(
				'FNAME' => $member->first_name,
				'LNAME' => $member->last_name,
			),
		);

		/**
		 * Filters the default member data that is used to send member information to an audience.
		 *
		 * @since 1.0.0
		 *
		 * @param array $member_data associative array of basic member data
		 * @param \WP_User $member the member user the data is being built for
		 */
		return (array) apply_filters( 'wc_memberships_mailchimp_list_default_member_data', $member_data, $member );
	}


}
