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

    <p class="meta">

        <?php wc_product_reviews_pro_author_badge( $comment ); ?>

        <strong itemprop="author" itemscope itemtype="http://schema.org/Person">
            <span itemprop="name"><?php comment_author(); ?></span>
        </strong>
        <?php

        if (    'yes' === get_option( 'woocommerce_review_rating_verification_label' )
             && wc_customer_bought_product( $comment->comment_author_email, $comment->user_id, $comment->comment_post_ID ) ) {

            echo '<em class="verified">(' . esc_html__( 'verified owner', 'woocommerce-product-reviews-pro' ) . ')</em> ';
        }
        ?>&ndash; <time itemprop="dateCreated" datetime="<?php echo esc_attr( get_comment_date( 'c' ) ); ?>"><?php echo esc_html( date_i18n( wc_date_format(), get_comment_date( 'U' ) ) ); ?></time>
    </p>

<?php endif;
