<?php
/**
 * The Template for list saved cards on checkout
 */

?>

<div id="yith_wcauthnet_credit_card_form">
	<div class="cards">
		<h6>
			<?php esc_html_e( 'Your credit cards', 'yith-woocommerce-authorizenet-payment-gateway' ); ?>
		</h6>

		<?php
		foreach ( $payment_profiles as $payment_profile ) {
			?>
			<div class="card<?php echo $payment_profile['default'] ? ' selected' : ''; ?>">
				<input type="radio" class="payment-profile-radio" value="<?php echo esc_attr( $payment_profile['profile_id'] ); ?>" id="<?php echo esc_attr( $payment_profile['profile_id'] ); ?>"
					   name="authorize_net_payment_profile" <?php checked( $payment_profile['default'], true ); ?> />
				<label for="<?php echo esc_attr( $payment_profile['profile_id'] ); ?>">
					<?php
					echo sprintf(
						'<span class="card-number">%s</span> <span class="card-expire">(%s)</span>',
						esc_html( $payment_profile['account_num'] ),
						esc_html( implode( '/', str_split( $payment_profile['expiration_date'], 2 ) ) )
					);
					?>
				</label>
			</div>
			<?php
		}
		?>

		<div class="card new-profile">
			<input type="radio" class="payment-profile-radio" value="" name="authorize_net_payment_profile" id="yith_wcauthnet_payment_profile_new"/>
			<label for="yith_wcauthnet_payment_profile_new"><?php esc_html_e( 'New card', 'yith-woocommerce-authorizenet-payment-gateway' ); ?></label>

			<div class="new-profile-form" style="display: none;">
				<?php $this->credit_card_form(); ?>
			</div>
		</div>

		<div class="clear"></div>

	</div>
</div>