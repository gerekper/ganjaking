<?php
/**
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * @package       YITH\yit-woocommerce-advanced-reviews\Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( isset( $_REQUEST ) && isset( $_REQUEST['page'] ) ) {//phpcs:ignore WordPress.Security.NonceVerification
	?>
<div class="wrap">

<h2><?php esc_html_e( 'Product reviews', 'yith-woocommerce-advanced-reviews' ); ?></h2>

	<?php $product_reviews->views(); ?>

	<form id="ywar-reviews" method="get">
		<input type="hidden" name="page" value="<?php echo esc_attr( sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) );//phpcs:ignore WordPress.Security.NonceVerification ?>"/>
		<?php $product_reviews->search_box( esc_html__( 'Search reviews', 'yith-woocommerce-advanced-reviews' ), 'yith-woocommerce-advanced-reviews' ); ?>
		<?php $product_reviews->display(); ?>
	</form>
</div>

	<?php
}
?>
