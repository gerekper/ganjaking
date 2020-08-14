<?php
/**
 * Request Quote Message
 *
 * @package YITH Woocommerce Request A Quote
 * @since   1.0.0
 * @version 2.2.7
 * @author  YITH
 *
 * @var string $raq_nonce
 * @var int $order_id
 */

/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
?>
<div class="ywraq-question-message">
	<?php
	if ( isset( $message ) && '' !== $message ) :
		?>
		<p><?php echo wp_kses_post( $message ); ?></p>
		<?php
	elseif ( isset( $confirm ) && 'no' === $confirm ) :
		?>
		<p><?php printf( esc_html( __( 'Are you sure you want to reject quote No. %d?', 'yith-woocommerce-request-a-quote' ) ), esc_html( $order_id ) ); ?></p>
		<form type="post">
			<input type="hidden" name="status" value="rejected"/>
			<input type="hidden" name="raq_nonce" value="<?php echo esc_attr( $raq_nonce ); ?>"/>
			<input type="hidden" name="request_quote" value="<?php echo esc_attr( $order_id ); ?>"/>
			<input type="hidden" name="confirm" value="yes"/>
			<?php $label_return_to_shop = apply_filters( 'yith_ywraq_return_to_shop_label', get_option( 'ywraq_return_to_shop_label' ) ); ?>
			<p>
				<label
					for="reason"><?php echo wp_kses_post( apply_filters( 'yith_ywraq_rejected_reason_label', __( 'Please, feel free to send us your feedback/reasons:', 'yith-woocommerce-request-a-quote' ) ) ); ?> </label>
				<textarea name="reason" id="reason" cols="10" rows="3"></textarea>
			</p>
			<input type="submit" class="ywraq-button button"
				   value="<?php echo esc_html( apply_filters( 'ywraq_reject_quote_button_text', __( 'Yes, I want to reject the quote', 'yith-woocommerce-request-a-quote' ) ) ); ?>"/>
		</form>

		<?php
		if ( get_option( 'ywraq_show_return_to_shop' ) === 'yes' ) :
			$shop_url             = apply_filters( 'yith_ywraq_return_to_shop_url', get_option( 'ywraq_return_to_shop_url' ) );
			$label_return_to_shop = apply_filters( 'yith_ywraq_return_to_shop_label', get_option( 'ywraq_return_to_shop_label' ) );
			?>
			<p>
				<a class="ywraq-button button"
				   href="<?php echo esc_url( apply_filters( 'yith_ywraq_return_to_shop_url', $shop_url ) ); ?>"><?php echo esc_html( $label_return_to_shop ); ?></a>
			</p>
		<?php endif ?>
	<?php endif ?>
</div>
