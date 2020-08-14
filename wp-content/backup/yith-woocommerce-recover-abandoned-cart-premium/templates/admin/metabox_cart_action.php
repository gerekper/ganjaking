<?php
/**
 * YITH WooCommerce Recover Abandoned Cart Content metabox template
 *
 * @package YITH WooCommerce Recover Abandoned Cart
 * @since   1.0.0
 * @author YITH
 */

$email_templates = YITH_WC_Recover_Abandoned_Cart_Email()->get_email_templates( 'cart', true );
if ( ! empty( $email_templates ) ) :
	?>
<table class="yith-ywrac-info-cart" cellspacing="20">
	<tbody>
		<tr>
			<th colspan="2"><?php esc_html_e( 'Send Email Manually:', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>

		</tr>
		<tr>

			<td class="ywrac_email_status">
				<select id="ywrac-email-template" name="ywrac-email-template">
					<?php foreach ( $email_templates as $et ) : ?>
						<option value="<?php echo esc_attr( $et->ID ); ?>"><?php echo esc_html( $et->post_title ); ?></option>
					<?php endforeach ?>
				</select>
				<p><?php echo '<input type="button" id="sendemail" class="ywrac_send_email button action"  value="' . esc_html( __( 'Send email', 'yith-woocommerce-recover-abandoned-cart' ) ) . '" data-id="' . esc_attr( $cart_id ) . '" data-type="cart">'; ?></p>
			</td>
		</tr>

	</tbody>
</table>
<?php endif; ?>
