<?php
/**
 * Booking Search Form Single Result Add to Cart Template
 * shows the single result product
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/booking/search-form/results/single/thumbnails.php.
 *
 * @var WC_Product_Booking $product
 * @var array              $booking_data
 *
 * @package YITH\Booking\Modules\SearchForms\Templates
 */

defined( 'YITH_WCBK' ) || exit;

global $post, $product, $woocommerce;

$attachment_ids = $product->get_gallery_image_ids();
$thumb_id       = get_post_thumbnail_id( $product->get_id() );
$first_set      = false;

if ( $thumb_id ) {
	$attachment_ids = array_merge( array( $thumb_id ), $attachment_ids );
}

if ( $attachment_ids ) : ?>
	<?php foreach ( $attachment_ids as $attachment_id ) : ?>
		<?php
		$classes    = array( '' );
		$image_link = wp_get_attachment_url( $attachment_id );

		if ( ! $image_link ) {
			continue;
		}

		if ( ! $first_set ) {
			$classes[] = 'current';
			$first_set = true;
		}

		$image_class = implode( ' ', $classes );
		?>
		<div class="yith-wcbk-thumb <?php echo esc_attr( $image_class ); ?>" style="background-image: url(<?php echo esc_url( $image_link ); ?>)"></div>
	<?php endforeach; ?>
<?php else : ?>
	<div class="yith-wcbk-thumb current" style="background-image: url(<?php echo esc_url( wc_placeholder_img_src() ); ?>)"></div>
<?php endif; ?>

<?php if ( count( $attachment_ids ) > 1 ) : ?>
	<div id="yith-wcbk-search-form-result-product-thumb-actions-<?php echo esc_attr( $product->get_id() ); ?>" class="yith-wcbk-search-form-result-product-thumb-actions">
		<span class="yith-wcbk-search-form-result-product-thumb-action-prev dashicons dashicons-arrow-left-alt2"></span>
		<span class="yith-wcbk-search-form-result-product-thumb-action-next dashicons dashicons-arrow-right-alt2"></span>
	</div>
<?php endif; ?>
