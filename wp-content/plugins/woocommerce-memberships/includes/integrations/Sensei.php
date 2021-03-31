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
 * @copyright Copyright (c) 2014-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships\Integrations;

use SkyVerge\WooCommerce\PluginFramework\v5_10_6 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Integration class for Sensei LMS.
 *
 * @since 1.21.0
 */
class Sensei {


	/**
	 * Sensei integration constructor.
	 *
	 * @since 1.21.0
	 */
	public function __construct() {

		// remove Sensei posts from "My Content"
		add_filter( 'wc_memberships_get_restricted_posts_query_args', [ $this, 'adjust_members_area_my_content_query' ], 10, 2 );

		// add custom content sections for Sensei lessons and courses
		add_filter( 'wc_membership_plan_members_area_sections', [ $this, 'add_sensei_members_area_section' ], 20 );
	}


	/**
	 * Adjusts the "My content" posts query.
	 *
	 * @internal
	 *
	 * @since 1.21.0
	 *
	 * @param string[] $query_args args for retrieving membership content
	 * @param string $type Type of request: 'content_restriction', 'product_restriction', 'purchasing_discount'
	 * @return string[] updated query args
	 */
	public function adjust_members_area_my_content_query( array $query_args, string $type ) : array {

		// only adjust this if we're looking at "My Content"
		if ( 'content_restriction' === $type ) {
			unset( $query_args['post_type']['course'], $query_args['post_type']['lesson'] );
		}

		return $query_args;
	}


	/**
	 * Adds a new content section for Sensei lessons and courses.
	 *
	 * @internal
	 *
	 * @since 1.21.0
	 *
	 * @param string[] $sections the member area sections
	 * @return string[] the updated sections
	 */
	public function add_sensei_members_area_section( array $sections ) : array {

		$new_sections = [];

		// add the new section after "My Content"
		foreach ( $sections as $key => $section ) {

			$new_sections[ $key ] = $section;

			if ( 'my-membership-content'  === $key ) {
				$new_sections['my-membership-sensei'] = __( 'Courses & Lessons', 'woocommerce-memberships' );
			}
		}

		return $new_sections;
	}


}
