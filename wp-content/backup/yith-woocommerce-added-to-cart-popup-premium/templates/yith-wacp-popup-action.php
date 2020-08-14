<?php
/**
 * Popup actions template
 *
 * @author  YITH
 * @package YITH WooCommerce Added to Cart Popup
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WACP' ) ) {
	exit;
}

?>

<div class="popup-actions">
	<?php
	do_action( 'yith_wacp_before_action_buttons' );
	if ( $cart ) :
		?>
		<a class="<?php echo esc_attr( apply_filters( 'yith_wacp_go_cart_class', 'button go-cart' ) ); ?>" href="<?php echo esc_url( $cart_url ); ?>">
			<?php echo wp_kses_post( get_option( 'yith-wacp-text-go-cart', '' ) ); ?>
		</a>
		<?php
	endif;

	if ( $checkout ) :
		?>
		<a class="<?php echo esc_attr( apply_filters( 'yith_wacp_go_checkout_class', 'button go-checkout' ) ); ?>" href="<?php echo esc_url( $checkout_url ); ?>">
			<?php echo wp_kses_post( get_option( 'yith-wacp-text-go-checkout', '' ) ); ?>
		</a>
		<?php
	endif;

	if ( $continue ) :
		?>
		<a class="<?php echo esc_attr( apply_filters( 'yith_wacp_continue_shopping_class', 'button continue-shopping' ) ); ?>" href="<?php echo esc_url( $continue_shopping_url ); ?>">
			<?php echo wp_kses_post( get_option( 'yith-wacp-text-continue-shopping', '' ) ); ?>
		</a>
		<?php
	endif;

	do_action( 'yith_wacp_after_action_buttons' );
	?>
</div>
