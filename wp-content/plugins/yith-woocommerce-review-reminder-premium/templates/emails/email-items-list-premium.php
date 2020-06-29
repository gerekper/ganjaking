<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

$site_url   = get_option( 'siteurl' );
$assets_url = untrailingslashit( YWRR_ASSETS_URL );

if ( strpos( $assets_url, $site_url ) === false ) {
	$assets_url = $site_url . $assets_url;
}

?>
<table class="ywrr-table" cellspacing="0" cellpadding="6" style="width: 100%;" border="1">
    <tbody>
	<?php foreach ( $item_list as $item ): ?>

		<?php

		$image = '';

		if ( has_post_thumbnail( $item['id'] ) ) {

			$product_image = wp_get_attachment_image_src( get_post_thumbnail_id( $item['id'] ), 'ywrr_picture' );
			list( $src, $width, $height ) = $product_image;

			$image = $src;

		} elseif ( wc_placeholder_img_src() ) {

			$image = wc_placeholder_img_src();

		}

		//APPLY_FILTER: ywrr_product_permalink: product permalink
		$permalink = apply_filters( 'ywrr_product_permalink', get_permalink( $item['id'] ), $customer_id, false );

		?>
        <tr>
            <td class="picture-column">
                <a href="<?php echo $permalink ?>"><img src="<?php echo $image ?>" height="135" width="135" /></a>
            </td>
            <td class="title-column">
                <br />
                <a href="<?php echo $permalink ?>"><?php echo $item['name'] ?><br />
                    <span class="stars"><?php esc_html_e( 'Your rating', 'yith-woocommerce-review-reminder' ) ?><br />
                        <img src="<?php echo $assets_url ?>/images/rating-stars.png">
                    </span>
                </a>

            </td>
        </tr>

	<?php endforeach; ?>
    </tbody>
</table>
