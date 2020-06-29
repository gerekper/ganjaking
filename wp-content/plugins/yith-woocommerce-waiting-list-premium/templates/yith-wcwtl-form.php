<?php
/**
 * Waitlist form on single product page
 *
 * @author        Yithemes
 * @package       YITH WooCommerce Waiting List
 * @version       1.1.1
 */

if ( ! defined( 'YITH_WCWTL' ) ) {
	exit; // Exit if accessed directly
}

$user     = wp_get_current_user();
$waitlist = yith_waitlist_get( $product_id );

/**
 * @type $product WC_Product
 */
// set query
$url = is_ajax() ? add_query_arg( '_yith_wcwtl_users_list', $product_id, $product->get_permalink() ) : add_query_arg( '_yith_wcwtl_users_list', $product_id );
$url = add_query_arg( '_yith_wcwtl_users_list-action', 'register', $url );

?>

<div id="yith-wcwtl-output">

	<?php if ( $message ) : ?>
		<p class="yith-wcwtl-msg"><?php echo esc_html( $message ); ?></p>
	<?php endif; ?>

	<?php if ( ! $product->is_type( 'variation' ) && ! $user->exists() ) : ?>

		<form method="post" action="<?php echo esc_url( $url ); ?>">
			<label for="yith-wcwtl-email">
                <?php echo esc_html( apply_filters( 'yith_wcwtl_email_address_label', __( 'Email Address', 'yith-woocommerce-waiting-list' ) ) )?>
				<input type="email" name="yith-wcwtl-email" id="yith-wcwtl-email"/>
			</label>
			<?php echo yith_waitlist_policy_checkbox(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<input type="submit" value="<?php echo esc_html( $label_button_add ); ?>" class="button alt"/>
		</form>

	<?php elseif ( ! $user->exists() ) : ?>
		<label for="yith-wcwtl-email">
			<?php echo esc_html( apply_filters( 'yith_wcwtl_email_address_label', __( 'Email Address', 'yith-woocommerce-waiting-list' ) ) )?>
			<input type="email" name="yith-wcwtl-email" id="yith-wcwtl-email" class="wcwtl-variation"/>
		</label>
		<?php echo yith_waitlist_policy_checkbox(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<a href="<?php echo esc_url( $url ); ?>" class="button alt"><?php echo esc_html( $label_button_add ) ?></a>

	<?php elseif ( is_array( $waitlist ) && isset( $user->user_email ) && yith_waitlist_user_is_register( $user->user_email, $waitlist ) ) : ?>

		<?php // change action arg
		$url = add_query_arg( '_yith_wcwtl_users_list-action', 'leave', $url ); ?>

		<a href="<?php echo esc_url( $url ); ?>"
			class="button button-leave alt"><?php echo esc_html( $label_button_leave ); ?></a>

	<?php else : ?>

		<?php echo yith_waitlist_policy_checkbox(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<a href="<?php echo esc_url( $url ); ?>" class="button alt"><?php echo esc_html( $label_button_add ); ?></a>

	<?php endif; ?>

</div>