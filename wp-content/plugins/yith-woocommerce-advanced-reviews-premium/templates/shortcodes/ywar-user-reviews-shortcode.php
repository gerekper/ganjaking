<?php
/**
 * Display single product reviews for YITH WooCommerce Advanced Reviews
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/ywar-product-reviews.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @author        YITH <plugins@yithemes.com>
 * @package       YITH\yit-woocommerce-advanced-reviews\Templates
 * @version       1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;// Exit if accessed directly.
}

?>

<div class="ywar-reviews-panel-title-container">
	<h2 style="float: left">
	<?php
		/** APPLY_FILTERS: yith_ywar_my_account_title
		 *
		 * Filter the default title label in the user-review shortcode.
		 */
		apply_filters( 'yith_ywar_my_account_title', esc_html_e( 'My Reviews', 'yith-woocommerce-advanced-reviews' ) );
	?>
	</h2>
</div>


<?php if ( empty( $reviews_by_user ) ) { ?>
	<span style="display: inline-block;"><p><?php echo esc_html__( 'You have not written any reviews yet, leave a review on the products you have purchased and they will appear in this section. ', 'yith-woocommerce-advanced-reviews' ); ?></p></span>

<?php } else { ?>
<table class="shop_table shop_table_responsive my_account_reviews">
	<thead>
		<th><?php echo esc_html__( 'Review', 'yith-woocommerce-advanced-reviews' ); ?></th>
		<th><?php echo esc_html__( 'Date', 'yith-woocommerce-advanced-reviews' ); ?></th>
		<th><?php echo esc_html__( 'Rate', 'yith-woocommerce-advanced-reviews' ); ?></th>
		<th><?php echo esc_html__( 'Product', 'yith-woocommerce-advanced-reviews' ); ?></th>
	</thead>
	<tbody>
	<?php

	foreach ( $reviews_by_user as $review ) {
		$review_id = $review->ID;

		$review->post_content = convert_smilies( $review->post_content );

		$product_id = get_post_meta( $review_id, YITH_YWAR_META_KEY_PRODUCT_ID, true );
		$product    = wc_get_product( $product_id );
		if ( ! $product ) {
			return;
		}
		$product_url = $product->get_permalink();

		$reviews_section_link = $product_url . '#reviews_summary';
		?>
		<tr>
			<td>
				<?php if ( ! empty( $review->post_title ) ) : ?>
					<a class="review-title row-title" href="<?php echo esc_html( $reviews_section_link ); ?>"><?php echo esc_html( $review->post_title ); ?></a><br>
				<?php endif; ?>

				<?php
				if ( ! empty( $review->post_content ) ) {
					/** APPLY_FILTERS: yith_ywar_review_content
					*
					* Filter the content of a comment in the backend table.
					*
					* @param string $post->post_content Default comment content for the backend table.
					* @param obj    $review             Obj of the post.
					*
					* @return string
					*/
					echo '<span class="review-content">' . esc_html( wp_strip_all_tags( apply_filters( 'yith_ywar_review_content', wc_trim_string( $review->post_content, 80 ), $review ) ) ) . '</span>';
				}
				?>
			</td>

			<td>
				<?php
				$t_time = get_the_time( __( 'Y/m/d g:i:s a' ) );
				$m_time = $review->post_date;
				$time   = get_post_time( 'G', true, $review );

				$time_diff = time() - $time;

				if ( $time_diff > 0 && $time_diff < DAY_IN_SECONDS ) {
					/* translators: %s: days ago */
					$h_time = sprintf( esc_html__( '%s ago' ), human_time_diff( $time ) );
				} else {
					$h_time = mysql2date( __( 'Y/m/d' ), $m_time );
				}

				echo '<abbr title="' . esc_html( $t_time ) . '">' . esc_html( $h_time ) . '</abbr>';
				?>
			</td>

			<td>
				<?php
				if ( 0 === $review->post_parent ) {
					$rating = get_post_meta( $review_id, YITH_YWAR_META_KEY_RATING, true );

					if ( $rating ) {
						?>
						<div class="woocommerce">
							<div class="star-rating"
							title="
								<?php
									/* translators: %d: rating of the user over 5 points */
									echo sprintf( esc_html__( 'Rated %d out of 5', 'yith-woocommerce-advanced-reviews' ), esc_html( $rating ) );
								?>
									">
								<span style="width:<?php echo ( ( esc_html( $rating ) / 5 ) * 100 ) . '%'; ?>"><strong><?php echo esc_html( $rating ); ?></strong><?php esc_html_e( ' out of 5', 'yith-woocommerce-advanced-reviews' ); ?></span>
							</div>
						</div>
						<?php
					}
				}
				?>
			</td>

			<td>
				<?php
				$product_id = get_post_meta( $review_id, YITH_YWAR_META_KEY_PRODUCT_ID, true );
				$product    = wc_get_product( $product_id );
				if ( ! $product ) {
					return;
				}
				$url = $product->get_permalink();
				?>
				<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $product->get_title() ) . ' '; ?></a>
			</td>
		</tr>

		<?php
	}

	?>
	</tbody>
</table>

	<?php
};
