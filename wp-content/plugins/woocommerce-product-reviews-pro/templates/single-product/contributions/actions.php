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
 * Display a contribution's actions.
 *
 * @type \WC_Contribution $contribution The contributions actions are for.
 *
 * @since 1.2.0
 * @version 1.7.0
 */
?>

<p class="contribution-actions">

	<a href="<?php echo esc_url( $contribution->get_vote_url( 'positive' ) ); ?>" class="vote vote-up js-tip <?php if ( 'positive' == $contribution->get_user_vote() ) : ?>done<?php endif; ?>" rel="nofollow" data-comment-id="<?php echo esc_attr( $contribution->get_id() ); ?>" title="<?php esc_attr_e( 'Upvote if this was helpful', 'woocommerce-product-reviews-pro' ); ?>"></a>
	<span class="vote-count vote-count-positive">
		(<span><?php echo absint( $contribution->get_positive_votes() ); ?></span>)
	</span>
	<a href="<?php echo esc_url( $contribution->get_vote_url( 'negative' ) ); ?>" class="vote vote-down js-tip <?php if ( 'negative' == $contribution->get_user_vote() ) : ?>done<?php endif; ?>" rel="nofollow" data-comment-id="<?php echo esc_attr( $contribution->get_id() ); ?>" title="<?php esc_attr_e( 'Downvote if this was not helpful', 'woocommerce-product-reviews-pro' ); ?>"></a>
	<span class="vote-count vote-count-negative">
		(<span><?php echo absint( $contribution->get_negative_votes() ); ?></span>)
	</span>

	<?php if ( ( (int) $contribution->parent === 0 ) && wc_product_reviews_pro_comment_notification_enabled() ) : ?>

		<a href="#" class="notifications subscribe js-tip" rel="nofollow" title="<?php esc_attr_e( 'Receive email notifications when there are replies', 'woocommerce-product-reviews-pro' ); ?>" data-comment-id="<?php echo esc_attr( $contribution->get_id() ); ?>" style="<?php if ( is_user_logged_in() && 'subscribe' !== $notifications ) { echo 'display: none;'; } ?>">
			<small><?php echo esc_html_x( 'Watch', 'Subscribe to contribution thread', 'woocommerce-product-reviews-pro' ); ?></small>
		</a>
		<a href="#" class="notifications unsubscribe js-tip" rel="nofollow" title="<?php esc_attr_e( 'Stop receiving email notifications when there are replies', 'woocommerce-product-reviews-pro' ); ?>" data-comment-id="<?php echo esc_attr( $contribution->get_id() ); ?>" style="<?php if ( 'unsubscribe' !== $notifications ) { echo 'display: none;'; } ?>">
			<small><?php echo esc_html_x( 'Unwatch', 'Unsubscribe from contribution thread', 'woocommerce-product-reviews-pro' ); ?></small>
		</a>

	<?php endif; ?>

	<?php if ( $contribution->is_editable() ) : ?>
		<a href="#" class="edit-comment js-tip" rel="nofollow" data-comment-id="<?php echo esc_attr( $contribution->get_id() ); ?>" title="<?php esc_attr_e( 'Edit your comment', 'woocommerce-product-reviews-pro' ); ?>">
			<span class="edit-icon dashicons dashicons-edit"></span>
			<small><?php echo esc_html_x( 'Edit', 'Edit your comment', 'woocommerce-product-reviews-pro' ); ?></small>
		</a>
	<?php endif; ?>

	<span class="feedback"></span>

	<a href="#flag-contribution-<?php echo esc_url( $contribution->get_id() ); ?>" class="flag js-toggle-flag-form js-tip <?php if ( $contribution->has_user_flagged() ) : ?>done<?php endif; ?>" data-comment-id="<?php echo esc_attr( $contribution->get_id() ) ?>" title="<?php _e( 'Flag for removal', 'woocommerce-product-reviews-pro' ); ?>"></a>

</p>
