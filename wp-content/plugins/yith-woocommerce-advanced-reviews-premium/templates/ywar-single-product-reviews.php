<?php
/**
 * Display single product reviews for YITH WooCommerce Advanced Reviews
 *
 * @package       YITH\yit-woocommerce-advanced-reviews\Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$product_id = yit_get_prop( $product, 'id' );
?>

<div id="ywar_reviews">
	<div id="reviews_summary">
		<?php
		/** APPLY_FILTERS: ywar_reviews_summary_title
		 *
		 * Filter the default summary title.
		 *
		 * @param string $text    Default summary title.
		 * @param obj    $product Obj of the product.
		 */
		?>
		<h3><?php echo wp_kses( apply_filters( 'ywar_reviews_summary_title', esc_html__( 'Customer reviews', 'yith-woocommerce-advanced-reviews' ), $product ), 'post' ); ?></h3>

		<?php
		/** DO_ACTION: ywar_summary_prepend
		 *
		 * Adds an action in the single product review template before summary.
		 *
		 * @param obj   $product      Obj of the product.
		 * @param mixed $review_stats Review stats.
		 */
		do_action( 'ywar_summary_prepend', $product, $review_stats );
		?>

		<?php
		/** DO_ACTION: ywar_summary
		 *
		 * Adds an action in the single product review template in the main summary.
		 *
		 * @param obj   $product      Obj of the product.
		 * @param mixed $review_stats Review stats.
		 */
		do_action( 'ywar_summary', $product, $review_stats );
		?>

		<?php
		/** DO_ACTION: ywar_summary_append
		 *
		 * Adds an action in the single product review template after main summary.
		 *
		 * @param obj   $product      Obj of the product.
		 * @param mixed $review_stats Review stats.
		 */
		do_action( 'ywar_summary_append', $product, $review_stats );
		?>

		<?php if ( has_action( 'ywar_reviews_header' ) ) : ?>
			<div id="reviews_header">
				<?php
				/** DO_ACTION: ywar_reviews_header
				 *
				 * Adds an action in the single product review template in the header container.
				 *
				 * @param mixed $review_stats Review stats.
				 */
				do_action( 'ywar_reviews_header', $review_stats );
				?>
			</div>
		<?php endif; ?>
	</div>

	<?php
	/** DO_ACTION: ywar_after_summary
	 *
	 * Adds an action in the single product review template after summary.
	 *
	 * @param float $product_id   ID of the product.
	 * @param mixed $review_stats Review stats.
	 */
	do_action( 'ywar_after_summary', $product_id, $review_stats );
	?>

	<div id="reviews_dialog"></div>
</div>
