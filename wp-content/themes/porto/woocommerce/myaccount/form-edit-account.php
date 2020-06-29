<?php
/**
 * Edit account form
 *
 * @version     3.5.0
 */

defined( 'ABSPATH' ) || exit;

$porto_woo_version = porto_get_woo_version_number();
if ( version_compare( $porto_woo_version, '2.6', '<' ) ) :
	wc_print_notices();
	?>
	<div class="featured-box align-left">
		<div class="box-content">
<?php endif; ?>

<?php do_action( 'woocommerce_before_edit_account_form' ); ?>

<form action="" method="post" class="woocommerce-EditAccountForm edit-account" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?> >

	<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

	<p class="woocommerce-FormRow woocommerce-FormRow--first form-row form-row-first">
		<label for="account_first_name"><?php esc_html_e( 'First name', 'porto' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr( $user->first_name ); ?>" />
	</p>
	<p class="woocommerce-FormRow woocommerce-FormRow--last form-row form-row-last">
		<label for="account_last_name"><?php esc_html_e( 'Last name', 'porto' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr( $user->last_name ); ?>" />
	</p>
	<div class="clear"></div>

	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		<label for="account_display_name"><?php esc_html_e( 'Display name', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_display_name" id="account_display_name" value="<?php echo esc_attr( $user->display_name ); ?>" /> <span><em><?php esc_html_e( 'This will be how your name will be displayed in the account section and in reviews', 'woocommerce' ); ?></em></span>
	</p>
	<div class="clear"></div>

	<p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide">
		<label for="account_email"><?php esc_html_e( 'Email address', 'porto' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="email" class="woocommerce-Input woocommerce-Input--email input-text" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr( $user->user_email ); ?>" />
	</p>

	<fieldset class="m-t-lg">
		<legend><?php esc_html_e( 'Password Change', 'porto' ); ?></legend>

		<p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide">
			<label for="password_current"><?php esc_html_e( 'Current Password (leave blank to leave unchanged)', 'porto' ); ?></label>
			<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_current" id="password_current" autocomplete="off" />
		</p>

		<p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide">
			<label for="password_1"><?php esc_html_e( 'New Password (leave blank to leave unchanged)', 'porto' ); ?></label>
			<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_1" id="password_1" autocomplete="off" />
		</p>

		<p class="woocommerce-FormRow woocommerce-FormRow--wide form-row form-row-wide">
			<label for="password_2"><?php esc_html_e( 'Confirm New Password', 'porto' ); ?></label>
			<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_2" id="password_2" autocomplete="off" />
		</p>
	</fieldset>
	<div class="clear"></div>

	<?php do_action( 'woocommerce_edit_account_form' ); ?>

	<p class="clearfix">
		<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
		<button type="submit" class="woocommerce-Button button btn-lg pt-right" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'porto' ); ?>"><?php esc_html_e( 'Save changes', 'porto' ); ?></button>
		<input type="hidden" name="action" value="save_account_details" />
	</p>

	<?php do_action( 'woocommerce_edit_account_form_end' ); ?>

</form>

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>

<?php if ( version_compare( $porto_woo_version, '2.6', '<' ) ) : ?>
		</div>
	</div>
<?php endif; ?>
