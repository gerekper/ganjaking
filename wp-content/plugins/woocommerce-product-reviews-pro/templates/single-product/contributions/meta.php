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
 * Display a contribution's meta.
 *
 * @type \WP_Comment $comment The comment object.
 * @type \WC_Contribution $contribution The contribution object.
 *
 * @since 1.2.0
 * @version 1.7.0
 */
?>

<?php if ( $contribution->moderation == '0' ) : ?>

    <p class="meta"><em><?php esc_html_e( 'Your contribution is awaiting approval', 'woocommerce-product-reviews-pro' ); ?></em></p>

<?php else : ?>

    <?php do_action( 'woocommerce_review_meta', $comment );?>

<?php endif;


