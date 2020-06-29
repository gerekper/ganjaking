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
 * @copyright Copyright (c) 2014-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Integration class for bbPress plugin.
 *
 * TODO this isn't truly a full fledged integration yet {FN 2017-11-13}
 *
 * @since 1.8.5
 */
class WC_Memberships_Integration_Bbpress {


	/**
	 * Loads bbPress integration.
	 *
	 * @since 1.8.5
	 */
	public function __construct() {

		// handles a bbPress-induced bug: when hidden or private forums exists, no posts shows in the members area content
		add_action( 'init', array( $this, 'bbpress_init' ), 40 );

		// workaround for an issue where multiple plans might kept locked the main topic in a restricted discussion
		add_filter( 'user_has_cap', array( $this, 'member_can_view_topic' ), 10, 3 );
	}


	/**
	 * Allows members to view the initial topic in a discussion thread.
	 *
	 * When "Hide Completely" is used, and there are multiple plans restricting different forums, the initial topic of a discussion thread is not visible to a member that should have access.
	 * This is a workaround that extends 'wc_memberships_access_all_restricted_content' in a very specific context to the current user while viewing a bbPress topic.
	 *
	 * NOTE: This callback method will be removed once bbPress is fully integrated into Memberships.
	 *
	 * @internal
	 *
	 * @since 1.9.4
	 *
	 * @param array $all_caps all capabilities
	 * @param array $caps capabilities
	 * @param array $args additional arguments
	 * @return array
	 */
	public function member_can_view_topic( $all_caps, $caps, $args ) {
		global $post;

		if (      $post
		     &&  'topic' === $post->post_type
		     && ! empty( $caps )
		     &&   wc_memberships()->get_restrictions_instance()->is_restriction_mode( 'hide' ) ) {

			foreach ( $caps as $cap ) {

				if (    'wc_memberships_access_all_restricted_content' === $cap
				     && empty( $all_caps[ $cap ] ) ) {

					// for sanity remove our own filter
					remove_filter( 'user_has_cap', array( $this, 'member_can_view_topic' ), 10 );

					$can_view_topic = current_user_can( 'wc_memberships_view_restricted_post_content', $post->ID );

					// check for force public flag
					if ( ! $can_view_topic ) {
						$can_view_topic = wc_memberships()->get_restrictions_instance()->is_post_public( $post->ID );
					}

					$all_caps[ $cap ] = $can_view_topic;

					// re-add back the current filter
					add_filter( 'user_has_cap', array( $this, 'member_can_view_topic' ), 10, 3 );
				}
			}
		}

		return $all_caps;
	}


	/**
	 * When hidden or private forums exists, no posts shows in the members area content.
	 *
	 * This is due to a bug in bbPress where the meta_query it adds via pre_get_posts excludes Hidden or Private forums for all queries that include the forum post type.
	 * Memberships is affected as it checks for posts of all post types when querying for a plan's restricted content.
	 * Our workaround merely consists of suppressing bbPress pre_get_posts filtering.
	 *
	 * NOTE: This method may be removed once the workaround will no longer be needed.
	 *
	 * TODO version 2.6 of bbPress might fix this, making this workaround useful only in bbPress versions 2.5.x and earlier {FN 2017-05-19}
	 *
	 * @internal
	 *
	 * @since 1.8.5
	 */
	public function bbpress_init() {

		$bbpress = bbpress();

		if (      isset( $bbpress->version )
		     &&   version_compare( $bbpress->version, '2.6', '<' )
		     &&   $this->is_members_area()
		     && ! is_bbpress() ) {

			remove_action( 'pre_get_posts', 'bbp_pre_get_posts_normalize_forum_visibility', 4 );
		}
	}


	/**
	 * Checks if the current page is the members area.
	 *
	 * Note: this is not the best way to determine if we are on the members area, but bbPress itself with pre_get_posts filtering may prevent determining it via query vars.
	 * @see \WC_Memberships_Integration_Bbpress::bbpress_init()
	 * @see \WC_Memberships_Members_Area::is_members_area()
	 *
	 * @since 1.8.7
	 *
	 * @return bool
	 */
	private function is_members_area() {

		$is_endpoint_url = false;

		if ( isset( $_SERVER['REQUEST_URI'] ) && get_option( 'permalink_structure' ) ) {

			$members_area_sections = wc_memberships_get_members_area_sections();

			if ( ! empty( $members_area_sections ) ) {
				foreach ( array_keys( $members_area_sections ) as $members_area_section ) {
					if ( (bool) strpos( $_SERVER['REQUEST_URI'], $members_area_section ) ) {
						$is_endpoint_url = true;
						break;
					}
				}
			}

		} else {

			$query_var       = wc_memberships_get_members_area_query_var();
			$is_endpoint_url = ! empty( $_GET[ $query_var ] ) && is_numeric( $_GET[ $query_var ] );
		}

		return $is_endpoint_url;
	}


}
