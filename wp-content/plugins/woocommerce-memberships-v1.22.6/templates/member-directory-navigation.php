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

defined( 'ABSPATH' ) or exit;

/**
 * Renders the navigation for the members directory, if paginated.
 *
 * @var int $total_pages the total pages in the directory
 *
 * @version 1.21.0
 * @since 1.21.0
 */
?>

<div class="members-nav">
	<span class="next-members-links"><?php next_posts_link( __( 'Older members &raquo;', 'woocommerce-memberships' ), $total_pages ); ?></span>
	<span class="prev-members-links"><?php previous_posts_link( __( '&laquo; Newer members', 'woocommerce-memberships' ) ); ?></span>
</div>
