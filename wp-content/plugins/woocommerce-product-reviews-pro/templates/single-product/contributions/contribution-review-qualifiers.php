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
 * Display the review qualifiers
 *
 * @type \WC_Contribution $contribution
 *
 * @since 1.0.0
 * @version 1.7.0
 */

global $product;

$review_qualifiers = wp_get_post_terms( $product->get_id(), 'product_review_qualifier' );
?>

<?php if ( ! empty( $review_qualifiers ) ) :  ?>

<div class="contribution-review-qualifiers">
	<?php foreach ( $review_qualifiers as $review_qualifier ) : ?>

		<?php if ( $value = get_comment_meta( $contribution->get_id(), 'wc_product_reviews_pro_review_qualifier_' . $review_qualifier->term_id, true ) ) : ?>
			<p>
				<strong class="review-qualifier-title"><?php echo $review_qualifier->name; ?></strong>
				<span class="review-qualifier-value"><?php echo $value; ?></span>
			</p>
		<?php endif; ?>

	<?php endforeach; ?>
</div>

<?php endif;
