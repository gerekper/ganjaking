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

namespace SkyVerge\WooCommerce\Memberships\Blocks;

use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Non-member block.
 *
 * Creates a block to display content to non-members only.
 *
 * @since 1.15.0
 */
class Non_Member_Content extends Block implements Dynamic_Content_Block {


	/**
	 * Block constructor.
	 *
	 * @since 1.15.0
	 */
	public function __construct() {

		$this->block_type = 'non-member-content';

		parent::__construct();

		add_filter( 'wc_memberships_trimmed_restricted_excerpt', [ $this, 'remove_block_from_restricted_content_excerpt' ], 1, 4 );
	}


	/**
	 * Renders the block content.
	 *
	 * Displays content to non members.
	 *
	 * @since 1.15.0
	 *
	 * @param array $attributes block attributes
	 * @param string $content HTML content
	 * @return string HTML
	 */
	public function render( $attributes, $content ) {

		$is_non_member      = true;
		$user_id            = get_current_user_id();
		$membership_plans   = ! empty( $attributes['membershipPlans'] ) ? (array) $attributes['membershipPlans'] : [];

		if ( $user_id > 0 ) {

			// skip admins: they are treated as members
			if ( current_user_can( 'wc_memberships_access_all_restricted_content' ) ) {

				$is_non_member = false;

			// non-members of any plan
			} elseif ( empty( $membership_plans ) ) {

				$is_non_member = ! wc_memberships_is_user_active_member( $user_id );

			// non-members of specific plans
			} else {

				foreach ( $membership_plans as $membership_plan ) {

					if ( wc_memberships_is_user_active_member( $user_id, $membership_plan ) ) {

						$is_non_member = false;
						break;
					}
				}
			}
		}

		return $is_non_member ? $content : '';
	}


}
