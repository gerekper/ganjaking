<?php
/**
 * WooCommerce Product Reviews Pro
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Reviews Pro to newer
 * versions in the future. If you wish to customize WooCommerce Product Reviews Pro for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-reviews-pro/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2015-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Display a contribution's upvotes and downvotes
 *
 * @type \WC_Contribution $contribution
 *
 * @since 1.2.0
 * @version 1.4.0
 */
?>

<small class="contribution-karma">
	<?php if ( $contribution->get_vote_count() ) : ?>
		<?php /* translators: Number of users that found a contibution useful - %1$d number of users, %2$d - total number of users that found the contribution useful */
		printf( _n( 'One person found this helpful', '%1$d out of %2$d people found this helpful', $contribution->get_vote_count(), 'woocommerce-product-reviews-pro' ), $contribution->get_positive_votes(), $contribution->get_vote_count() ); ?>
	<?php endif; ?>
</small>
