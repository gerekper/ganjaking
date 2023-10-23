<?php
/**
 * Display single product reviews for YITH WooCommerce Advanced Reviews
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/ywar-single-product-reviews.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see           http://docs.woothemes.com/document/template-structure/
 * @author        YITH <plugins@yithemes.com>
 * @package       YITH\yit-woocommerce-advanced-reviews\Templates
 * @version       1.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$product_id = yit_get_prop( $product, 'id' );

$total_reviews = isset( $review_stats['voted_reviews'] ) ? $review_stats['voted_reviews'] : $review_stats['total'];

$total_reviews = ( $total_reviews > 0 ) ? $total_reviews : 1;

?>
<div class="reviews_bar">

	<?php
	for ( $i = 5; $i >= 1; $i -- ) :
		$perc = ( '0' === $review_stats['total'] ) ? 0 : floor( $review_stats[ $i ] / $total_reviews * 100 );
		?>

		<div class="ywar_review_row">

			<?php
			/** DO_ACTION: ywar_summary_row_prepend
			 *
			 * Adds an action in the review bars template before main content.
			 *
			 * @param float $i          Cycle of the for.
			 * @param float $product_id ID of the product.
			 * @param $perc             Perc.
			 */
			do_action( 'ywar_summary_row_prepend', $i, $product_id, $perc );
			?>
			<?php /* translators: s: $i */ ?>
			<span class="ywar_stars_value" style="color:<?php echo esc_attr( get_option( 'ywar_summary_rating_label_color' ) ); ?>"> <?php printf( esc_attr( _n( '%s star', '%s stars', $i, 'yith-woocommerce-advanced-reviews' ) ), esc_attr( $i ) ); ?> </span>

			<span class="ywar_num_reviews" style="color:<?php echo esc_attr( get_option( 'ywar_summary_count_color' ) ); ?>"> <?php echo esc_attr( $review_stats[ $i ] ); ?> </span>

			<span class="ywar_rating_bar">

				<span style="background-color:<?php echo esc_attr( get_option( 'ywar_summary_bar_color' ) ); ?>" class="ywar_scala_rating">

					<span class="ywar_perc_rating" style="width: <?php echo esc_attr( $perc ); ?>%; background-color:<?php echo esc_attr( get_option( 'ywar_summary_percentage_bar_color' ) ); ?>">

						<?php if ( 'yes' === get_option( 'ywar_summary_percentage_value' ) ) : ?>

							<span style="color:<?php echo esc_attr( get_option( 'ywar_summary_percentage_value_color' ) ); ?>" class="ywar_perc_value"><?php printf( '%s %%', esc_attr( $perc ) ); ?> </span>

						<?php endif; ?>

					</span>

				</span>

			</span>

			<?php
			/** DO_ACTION: ywar_summary_row_append
			 *
			 * Adds an action in the review bars template after main content.
			 *
			 * @param float $i          Cycle of the for.
			 * @param float $product_id ID of the product.
			 * @param $perc             Perc.
			 */
			do_action( 'ywar_summary_row_append', $i, $product_id, $perc );
			?>

		</div>

	<?php endfor; ?>

</div>
