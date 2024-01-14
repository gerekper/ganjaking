<?php
/**
 * This template is used for display Cashback Form.
 *
 * This template can be overridden by copying it to yourtheme/rewardsystem/cashback-form.php
 *
 * To maintain compatibility, Reward System will update the template files and you have to copy the updated files to your theme.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<form id="encashing_form" class="encashing_form" method="post" enctype="multipart/form-data">
	<div class="rs_current_points_value">
		<p>
			<label><?php echo esc_html( get_option( 'rs_total_points_for_cashback_request' ) ); ?></label>
		</p>
		<p>
			<input type="text" id="rs_available_points" name="rs_available_points" value="<?php echo esc_attr( $PointsToDisplay ); ?>" readonly="readonly">
		</p>
	</div>
	<div class="rs_encash_points_value">
		<p>
			<label><?php echo esc_html( get_option( 'rs_encashing_points_label' ) ); ?></label>
		</p>
		<p>
			<input type="number" 
				   step="any" min="1" id="rs_encash_points_value" 
				   name="rs_encash_points_value" 
				   <?php
					if ( '2' == get_option( 'rs_allow_user_to_request_cashback' ) ) {
						?>
					   readonly="readonly"<?php } ?> value="
				   <?php
					if ( '2' == get_option( 'rs_allow_user_to_request_cashback' ) ) {
						echo esc_html( $AvailablePoints );
					}
					?>
				   "/>
		</p>
	</div>
	<div class="error" id="points_empty_error">
		<?php echo esc_html( get_option( 'rs_error_message_points_empty_encash' ) ); ?>
	</div>
	<div class="error" id="points_number_error">
		<?php echo esc_html( get_option( 'rs_error_message_points_number_val_encash' ) ); ?>
	</div>
	<div class="error" id ="points_greater_than_earnpoints_error">
		<?php echo esc_html( get_option( 'rs_error_message_points_greater_than_earnpoints' ) ); ?>
	</div>
	<div class="error" id ="points_lesser_than_minpoints_error">
		<?php echo esc_html( $ReplacedErrMsg ); ?>
	</div>
	<?php
	$user      = wp_get_current_user();
	$roles     = is_object( $user ) ? $user->roles : '';
	$user_role = isset( $roles[ 0 ] ) ? $roles[ 0 ] : '';
	if ( get_option( 'rs_cashback_' . $user_role . '_for_redeem_percentage', 100 ) ) {
		?>
		<div class="rs_encash_currency_value">
			<p>
				<label><?php echo esc_html( get_option( 'rs_encashing_currency_label', 'Currency Value' ) ); ?></label>
			</p>
			<p>                                    
				<input type="text" id="rs_encash_currency_value" class ="rs_encash_currency_value" readonly="readonly"/>
			</p>
		</div>
	<?php } ?>
	<div class="rs_encash_points_reason">
		<p>
			<label><?php echo esc_html( get_option( 'rs_encashing_reason_label' ) ); ?></label>
		</p>
		<p>
			<textarea name ="rs_encash_points_reason" id="rs_encash_points_reason" rows= "3" cols= "50"></textarea>
		</p>
	</div>
	<div class="error" id ="reason_empty_error">
		<?php echo esc_html( get_option( 'rs_error_message_reason_encash_empty' ) ); ?>
	</div>
	<?php if ( '3' == get_option( 'rs_select_payment_method' ) ) { ?>
		<div class ="rs_encash_payment_method">
			<p>
				<label><?php echo esc_html( get_option( 'rs_encashing_payment_method_label' ) ); ?></label>
			</p>
			<p>
				<select id= "rs_encash_payment_method">
					<option value="encash_through_paypal_method" 
					<?php
					if ( 'yes' == $AllowToSavePaymentMethod && 'encash_through_paypal_method' == get_user_meta( get_current_user_id(), 'rs_cashback_previous_payment_method', true ) ) {
						?>
								selected="selected"<?php } ?>><?php esc_html_e( 'PayPal', 'rewardsystem' ); ?></option>
					<option value="encash_through_custom_payment" 
					<?php
					if ( 'yes' == $AllowToSavePaymentMethod && 'encash_through_custom_payment' == get_user_meta( get_current_user_id(), 'rs_cashback_previous_payment_method', true ) ) {
						?>
								selected="selected"<?php } ?>><?php esc_html_e( 'Custom Payment', 'rewardsystem' ); ?></option>
							<?php
							if ( check_whether_hoicker_is_active() ) {
								$WalletLabel = ! empty( get_option( 'rs_encashing_wallet_menu_label' ) ) ? get_option( 'rs_encashing_wallet_menu_label' ) : esc_html__( 'Hoicker Wallet', 'rewardsystem' );
								?>
						<option value="<?php echo esc_attr( $WalletLabel ); ?>"><?php echo esc_html( $WalletLabel ); ?></option>
					<?php } ?>
				</select>
			</p>
		</div>
		<?php
	}
	if ( check_whether_hoicker_is_active() ) {
		$WalletLabel = ! empty( get_option( 'rs_encashing_wallet_menu_label' ) ) ? get_option( 'rs_encashing_wallet_menu_label' ) : __( 'Hoicker Wallet', 'rewardsystem' );
		?>
		<input type="hidden" value="<?php echo esc_attr( $WalletLabel ); ?>" id="is_walletia_selected" class="is_walletia_selected" name="is_walletia_selected">
		<div class ="rs_encash_wallet">
			<p>
				<label><?php echo esc_html( get_option( 'rs_encashing_wallet_label' ) ); ?></label>
			</p>
		</div>
		<?php
	}
	if ( '1' == get_option( 'rs_select_payment_method' ) || '3' == get_option( 'rs_select_payment_method' ) ) {
		?>
		<div class ="rs_encash_paypal_address">
			<p>
				<label><?php echo esc_html( get_option( 'rs_encashing_payment_paypal_label' ) ); ?></label>
			</p>
			<p>
				<input type = "text" id = "rs_encash_paypal_address" name = "rs_encash_paypal_address" value="
				<?php
				if ( 'yes' == $AllowToSavePaymentMethod && '' != get_user_meta( get_current_user_id(), 'rs_paypal_payment_details', true ) ) {
					echo esc_attr( get_user_meta( get_current_user_id(), 'rs_paypal_payment_details', true ) );
				}
				?>
					   "/>
			</p>
		</div>
		<div class="error" id ="paypal_email_empty_error">
			<?php echo esc_html( get_option( 'rs_error_message_paypal_email_empty' ) ); ?>
		</div>
		<div class="error" id ="paypal_email_format_error">
			<?php echo esc_html( get_option( 'rs_error_message_paypal_email_wrong' ) ); ?>
		</div>
		<?php
	}
	if ( '2' == get_option( 'rs_select_payment_method' ) || '3' == get_option( 'rs_select_payment_method' ) ) {
		?>
		<div class ="rs_encash_custom_payment_option_value">
			<p>
				<label><?php echo esc_html( get_option( 'rs_encashing_payment_custom_label' ) ); ?></label>
			</p>
			<p>
				<textarea name ="rs_encash_custom_payment_option_value" id="rs_encash_custom_payment_option_value" rows= "3" cols= "50">
					<?php
					if ( 'yes' == $AllowToSavePaymentMethod && '' != get_user_meta( get_current_user_id(), 'rs_custom_payment_details', true ) ) {
						echo esc_html( get_user_meta( get_current_user_id(), 'rs_custom_payment_details', true ) );
					}
					?>
				</textarea>
			</p>
		</div>
		<div class="error" id="paypal_custom_option_empty_error" style="display:none;">
			<?php echo esc_html( get_option( 'rs_error_custom_payment_field_empty' ) ); ?>
		</div>
		<?php
	}
	if ( 'yes' == get_option( 'rs_enable_recaptcha_to_display' ) && '' != get_option( 'rs_google_recaptcha_site_key' ) ) {
		?>
		<div class="rs_enable_recaptcha_to_display">
			<p>
				<label>
					<?php echo esc_html( get_option( 'rs_google_recaptcha_label' ) ); ?>
				</label>
			</p>
			<p>
			<div name="rs_encash_recaptcha" class="g-recaptcha" data-sitekey="<?php echo esc_attr( get_option( 'rs_google_recaptcha_site_key' ) ); ?>"></div>
			</p>
		</div>
		<div class="error" id="recaptcha_empty_error">
			<?php echo esc_html( get_option( 'rs_error_recaptcha_field_empty' ) ); ?>
		</div>
		<?php
	}
	?>
	<div class ="rs_encash_submit">
		<input type="submit" value="<?php echo esc_attr( get_option( 'rs_encashing_submit_button_label' ) ); ?>" id="submit_cashback"/>
	</div>
	<div class="success_info" id ="encash_form_success_info">
		<b><?php echo esc_html( get_option( 'rs_message_encashing_request_submitted' ) ); ?></b>
	</div>
</form>
<?php
