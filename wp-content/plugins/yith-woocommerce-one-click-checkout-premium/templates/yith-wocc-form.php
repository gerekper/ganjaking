<?php
/**
 * One-Click Checkout Template
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * @type $product \WC_Product
 */

$product_id = $product->get_id();
$product_url = $product->get_permalink();

?>

<div class="clear"></div>
<div class="yith-wocc-wrapper">

	<?php do_action( 'yith_wocc_before_one_click_button', $product ); ?>

	<?php if( isset( $divider ) && $divider ) : ?>
		<div class="yith-wocc-divider">
			<span><?php esc_html_e( 'or', 'yith-woocommerce-one-click-checkout' ); ?></span>
		</div>
	<?php endif; ?>
	
	<div class="yith-wocc-button-container">
		<?php if( isset( $is_loop ) && $is_loop ) : ?>
			<a href="<?php echo esc_url_raw( add_query_arg( array( '_yith_wocc_one_click' => 'is_one_click', 'add-to-cart' => $product_id ), $product_url ) ) ?>"
			   class="yith-wocc-button button"><span class="button-label"><?php echo esc_html( $label ); ?></span></a>
		<?php else: ?>
				<input type="hidden" name="_yith_wocc_one_click" value />
				<?php if( $product->is_type( array( 'simple', 'subscription' ) ) ) : ?>
                    <button type="submit" class="yith-wocc-button button" name="add-to-cart" value="<?php echo esc_html( $product_id ); ?>">
                <?php else : ?>
                    <button type="submit" class="yith-wocc-button button">
                <?php endif; ?>
                        <span class="button-label"><?php echo esc_html( $label ); ?></span>
                    </button>
		<?php endif; ?>
	</div>

	<?php do_action( 'yith_wocc_after_one_click_button', $product ); ?>

</div>