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
 * Display single product reviews (comments).
 *
 * @type string $type Contribution type.
 * @type \WC_Product $product Product object.
 * @type \WP_Comment[] $comments Array of comment objects.
 *
 * @version 1.13.0
 * @since 1.0.0
 */

global $product;

if ( ! comments_open() ) {
	return;
}

$contribution_types = wc_product_reviews_pro_get_enabled_contribution_types();
$ratings            = array( 5, 4, 3, 2, 1 );
$total_rating_count = $product->get_rating_count();

?>
<div id="reviews">

	<h2 class="contributions-title"><?php esc_html_e( 'Share your thoughts!', 'woocommerce-product-reviews-pro' ); ?></h2>

	<?php // Product ratings ?>
	<?php if ( wc_review_ratings_enabled() && $product->get_rating_count() ) : ?>

		<div class="product-rating">
			<div class="product-rating-summary">
				<?php

				/**
				 * Filters the product average rating.
				 *
				 * @since 1.10.0
				 *
				 * @param float $average_rating average rating
				 * @param \WC_Product $product the product object
				 */
				$average_rating = max( 0, (float) apply_filters( 'wc_product_reviews_pro_product_rating_average', $product->get_average_rating(), $product ) );

				$reviews_count  = max( 0, wc_product_reviews_pro_get_contributions_count( $product, 'review' ) );

				/* translators: Placeholders: %s - average rating stars count (float casted as string to avoid trailing zeroes), %d - 5 stars total (integer) -- (e.g "4.2 out of 5 stars") */
				$reviews_label  = sprintf( __( '%s out of %d stars', 'woocommerce-product-reviews-pro' ), $average_rating, 5 );

				?>
				<h3><?php echo esc_html( $reviews_label ); ?></h3>
				<p><?php printf( _nx( '%d review', '%d reviews', $reviews_count, 'noun', 'woocommerce-product-reviews-pro' ), $reviews_count ); ?></p>
			</div>
			<div class="product-rating-details">
				<table>

					<?php foreach ( $ratings as $rating ) : ?>

						<?php

						/**
						 * Filters the rating count of a product.
						 *
						 * @since 1.10.0
						 *
						 * @param int $count the rating count
						 * @param \WC_Product $product the current product
						 * @param int $rating rating
						 */
						$count      = (int) apply_filters( 'wc_product_reviews_pro_product_rating_count', $product->get_rating_count( $rating ), $product, $rating );
						$percentage = $count / $total_rating_count * 100;

						$url    = remove_query_arg( 'comment_filter', "//$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]" );
						$filter = "comment_type=review&rating={$rating}";
						$url    = add_query_arg( 'comments_filter', urlencode( $filter ), $url );

						?>
						<tr>
							<td class="rating-number">
								<a href="<?php echo esc_url( $url ); ?>#comments"><?php echo $rating; ?> <span class="rating-star"></span></a>
							</td>
							<td class="rating-graph">
								<a href="<?php echo esc_url( $url ); ?>#comments" class="bar" style="width: <?php echo $percentage; ?>%;" title="<?php printf( '%s%%', $percentage ); ?>"></a>
							</td>
							<td class="rating-count">
								<a href="<?php echo esc_url( $url ); ?>#comments"><?php echo $count; ?></a>
							</td>
						</tr>

					<?php endforeach; ?>

				</table>
			</div>
		</div>

	<?php endif; ?>

	<?php

	/**
     * Fires before contribution list and title
     *
     * @since 1.0.1
	 */
	do_action( 'wc_product_reviews_pro_before_contributions' );

	?>
	<h3 class="contributions-form-title"><?php esc_html_e( 'Let us know what you think...', 'woocommerce-product-reviews-pro' ); ?></h3>

	<div class="contribution-type-selector">
		<?php $key = 0; ?>
		<?php foreach ( $contribution_types as $type ) : ?>

			<?php if ( 'contribution_comment' !== $type ) : $key++; ?>

				<?php $contribution_type = wc_product_reviews_pro_get_contribution_type( $type ); ?>
				<a href="#share-<?php echo esc_attr( $type ); ?>" class="js-switch-contribution-type <?php if ( $key === 1 ) : ?>active<?php endif; ?>"><?php echo $contribution_type->get_call_to_action(); ?></a>

			<?php endif; ?>

		<?php endforeach; ?>
	</div>

	<?php $key = 0; ?>
	<?php foreach ( $contribution_types as $type ) : ?>

		<?php if ( 'contribution_comment' !== $type ) : $key++; ?>

			<div id="<?php echo esc_attr( $type ); ?>_form_wrapper" class="contribution-form-wrapper <?php if ( $key === 1 ) : ?>active<?php endif; ?>">
				<?php wc_get_template( 'single-product/form-contribution.php', array( 'type' => $type ) ); ?>
			</div>

		<?php endif; ?>

	<?php endforeach; ?>

	<?php if ( ! is_user_logged_in() && get_option('comment_registration') ) : ?>

		<noscript>
			<style type="text/css">#reviews .contribution-form-wrapper { display: none; }</style>
			<p class="must-log-in"><?php printf( __( 'You must be <a href="%s">logged in</a> to join the discussion.', 'woocommerce-product-reviews-pro' ), esc_url( add_query_arg( 'redirect_to', urlencode( get_permalink( get_the_ID() ) ), wc_get_page_permalink( 'myaccount' ) . '#tab-reviews' ) ) ); ?></p>
		</noscript>

	<?php endif; ?>

	<?php // Comments list ?>
	<div id="comments">

		<form method="get" action="#comments" class="contributions-filter">
			<?php

			// Filter options
			$options = array(
				'' => __( 'Show everything', 'woocommerce-product-reviews-pro' ),
			);

			// Add option for each contribution type
			foreach ( $contribution_types as $type ) {

				if ( 'contribution_comment' === $type ) {
					continue;
				}

				$contribution_type = wc_product_reviews_pro_get_contribution_type( $type );
				$options[ "comment_type={$type}" ] = $contribution_type->get_filter_title();
			}

			// adding user options for each contribution type if logged in
			if ( is_user_logged_in() ) {

			    foreach ( $contribution_types as $type ) {

					if ( 'contribution_comment' === $type ) {
						continue;
					}

					$contribution_type = wc_product_reviews_pro_get_contribution_type( $type );
					$options[ "comment_type={$type}&user_type=me" ] = $contribution_type->get_user_filter_title();
				}
			}

			// Review qualifier options
			$review_qualifiers = wp_get_post_terms( $product->get_id(), 'product_review_qualifier' );

			foreach ( $review_qualifiers as $review_qualifier ) {

				$qualifier_options = array_filter( explode( "\n", get_term_meta( $review_qualifier->term_id, 'options', true ) ) );

				foreach ( $qualifier_options as $option ) {
					$options[ 'comment_type=review&review_qualifier=' . $review_qualifier->term_id . ':' . $option ] = sprintf( __( 'Show all reviews that said %s is "%s"', 'woocommerce-product-reviews-pro' ), $review_qualifier->name, $option );
				}

			}

			// Special options
			$options[ 'comment_type=review&classification=positive&helpful=1' ] = __( 'Show helpful positive reviews', 'woocommerce-product-reviews-pro' );
			$options[ 'comment_type=review&classification=negative&helpful=1' ] = __( 'Show helpful negative reviews', 'woocommerce-product-reviews-pro' );
			$options[ 'comment_type=question&unanswered=1' ] = __( 'Show unanswered questions', 'woocommerce-product-reviews-pro' );

			/**
			 * Filter the filter options.
			 *
			 * @since 1.0.0
			 * @param array $options The filter options.
			 */
			$options = apply_filters( 'wc_product_reviews_pro_contribution_filter_options', $options );

			// Other field args
			$args = array(
				'type'    => 'select',
				'options' => $options,
			);

			$comments_filter = isset( $_REQUEST['comments_filter'] ) ? $_REQUEST['comments_filter'] : null;

			?>

			<a href="<?php the_permalink(); ?>" class="js-clear-filters" style="display:none;" title="<?php _e( 'Click to clear filters', 'woocommerce-product-reviews-pro' ); ?>"><?php _e( '(clear)', 'woocommerce-product-reviews-pro' ); ?></a>

			<?php woocommerce_form_field( 'comments_filter', $args, $comments_filter ); ?>

			<noscript><button type="submit" class="button"><?php _e( 'Go', 'woocommerce-product-reviews-pro' ); ?></button></noscript>
		</form>

		<div id="contributions-list">
			<?php wc_get_template( 'single-product/contributions-list.php', array( 'comments' => $comments ) ); ?>
		</div>
	</div>

	<div class="clear"></div>

	<?php if ( ! is_user_logged_in() ) : ?>

		<div id="wc-product-reviews-pro-modal-container">
			<div id="wc-product-reviews-pro-modal">

				<a href="#" class="close">&times;</a>

				<?php wc_get_template( 'myaccount/form-login.php' ); ?>

				<?php wc_get_template( 'myaccount/form-guest.php' ); ?>

				<?php if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>

					<div class="switcher">
						<p class="login"><?php /* translators: Placeholders: %1$s - opening <a> link tag, %2$s - closing </a> link tag */
							printf(	__( 'Already have an account? %1$sLog In%2$s', 'woocommerce-product-reviews-pro' ), '<a href="#">', '</a>' ); ?></p>

						<p class="register"><?php /* translators: Placeholders: %1$s - opening <a> link tag, %2$s - closing </a> link tag */
							printf( __( 'Don\'t have an account? %1$sSign Up%2$s', 'woocommerce-product-reviews-pro' ), '<a href="#">', '</a>' ); ?></p>
					</div>

				<?php endif; ?>

				<div class="guest-switcher" data-target="">
					<p class="guest"><?php /* translators: Placeholders: %1$s - opening <a> link tag, %2$s - closing </a> link tag */
					printf( __( '%1$sContinue as a Guest%2$s', 'woocommerce-product-reviews-pro' ), '<a class="guest-link" href="#">', '</a>' ); ?></p>
				</div>

			</div>
			<div id="wc-product-reviews-pro-modal-overlay"></div>
		</div>

	<?php endif; ?>

	<?php /* display all forms when no JS */ ?>
	<noscript>
		<style type="text/css">
			.contribution-form-wrapper { display: block; }
		</style>
	</noscript>

</div>
