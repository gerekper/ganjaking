<?php
/**
 * Item list templates
 *
 * @package YITH\ReviewReminder
 * @var $item_list
 * @var $customer_id
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$assets_url = untrailingslashit( YWRR_ASSETS_URL );

?>
<table class="ywrr-table" cellspacing="0" cellpadding="6" style="width: 100%;" border="1">
	<tbody>
	<?php foreach ( $item_list as $item ) : ?>

		<?php

		$image = '';

		if ( has_post_thumbnail( $item['id'] ) ) {

			$product_image = wp_get_attachment_image_src( get_post_thumbnail_id( $item['id'] ), 'ywrr_picture' );

			list( $src, $width, $height ) = $product_image;

			$image = $src;

		} elseif ( wc_placeholder_img_src() ) {

			$image = wc_placeholder_img_src();

		}

		/**
		 * APPLY_FILTERS: ywrr_product_permalink
		 *
		 * Product permalink.
		 *
		 * @param string $value The product permalink.
		 *
		 * @return string
		 */
		$permalink = apply_filters( 'ywrr_product_permalink', get_permalink( $item['id'] ), $customer_id, false );

		?>
		<tr>
			<td class="picture-column">
				<a href="<?php echo esc_url( $permalink ); ?>"><img src="<?php echo esc_url( $image ); ?>" height="135" width="135" /></a>
			</td>
			<td class="title-column">
				<br />
				<a href="<?php echo esc_url( $permalink ); ?>"><?php echo wp_kses_post( $item['name'] ); ?>
					<br />
					<span class="stars">
						<?php esc_html_e( 'Your rating', 'yith-woocommerce-review-reminder' ); ?>
						<br />
						<img src="<?php echo esc_url( $assets_url ); ?>/images/rating-stars.png">
					</span>
				</a>

			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
