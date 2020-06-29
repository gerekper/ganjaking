<?php
/**
 * Subscription form template (used in shortcode and widget)
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Mailchimp
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCMC' ) ) {
	exit;
} // Exit if accessed directly
?>

<!-- BEFORE NEWSLETTER SUBSCRIPTION FORM -->
<?php
do_action( 'before_newsletter_subscription_form' );

if ( isset( $before ) ) {
	echo $before; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
?>

<?php if ( $enable_style ) : ?>
	<style>
		<?php echo $style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</style>
<?php endif; ?>

<div class="yith-wcmc-subscription-form woocommerce" id="subscription_form_<?php echo esc_attr( $unique_id ); ?>">
	<?php if ( ! empty( $title ) ) : ?>
		<h3><?php echo esc_html( $title ); ?></h3>
	<?php endif; ?>

	<?php do_action( 'yith_wcmc_after_subscription_form_title', $list ); ?>

	<?php if ( function_exists( 'wc_get_notices' ) ) : ?>
	<div class="subscription-notice">
		<?php
		$success = wc_get_notices( 'yith-wcmc-success' );
		$errors  = wc_get_notices( 'yith-wcmc-error' );

		if ( ! empty( $success ) ) {
			foreach ( $success as $notice ) {
				wc_print_notice( $notice, 'success' );
			}
		}

		if ( ! empty( $errors ) ) {
			foreach ( $errors as $notice ) {
				wc_print_notice( $notice, 'error' );
			}
		}
	?>
	</div>
	<?php endif; ?>

	<?php do_action( 'yith_wcmc_after_subscription_form_notice', $list ); ?>

	<form method="POST" data-hide="<?php echo ! empty( $hide_form_after_registration ) ? esc_attr( $hide_form_after_registration ) : 'no'; ?>">

		<?php foreach ( $fields as $id => $field ) : ?>

			<?php if ( isset( $fields_data[ $id ] ) && $fields_data[ $id ]['public'] ) : ?>
				<p>
					<?php if ( ! empty( $field['name'] ) && ! $use_placeholders ) : ?>
						<label for="<?php echo esc_attr( $id ); ?>_<?php echo esc_attr( $unique_id ); ?>" ><?php echo function_exists( 'icl_t' ) ? esc_html( icl_t( 'admin_texts_plugin_yith-woocommerce-mailchimp-premium', "yith_wcmc_{$context}_custom_fields[$id]", $field['name'] ) ) : esc_html( $field['name'] ); ?></label><br/>
					<?php endif; ?>
					<?php YITH_WCMC_Premium()->print_field( $unique_id, $field, $fields_data[ $id ], $context, $id ); ?>
				</p>
			<?php endif; ?>

		<?php endforeach; ?>

		<?php if ( ! empty( $groups_data ) ) : ?>
			<?php foreach ( $groups_data as $group_id => $group_data ) : ?>
				<p>
					<?php if ( ! empty( $group_data['name'] ) && ! $use_placeholders ) : ?>
						<label for="group_<?php echo esc_attr( $group_id ); ?>_<?php echo esc_attr( $unique_id ); ?>" ><?php echo function_exists( 'icl_t' ) ? esc_html( icl_t( 'admin_texts_plugin_yith-woocommerce-mailchimp-premium', "yith_wcmc_{$context}_groups[$group_id]", $group_data['name'] ) ) : esc_html( $group_data['name'] ); ?></label><br/>
					<?php endif; ?>
					<?php YITH_WCMC_Premium()->print_groups( $unique_id, $group_data ); ?>
				</p>
			<?php endforeach; ?>
		<?php endif; ?>

		<?php if ( $show_privacy_field ) : ?>
			<label for="privacy_agreement">
				<input type="checkbox" value="yes" name="privacy_agreement" id="privacy_agreement" />
				<?php echo wp_kses_post( $privacy_label ); ?>
			</label>
		<?php endif; ?>

		<input type="hidden" name="email_type" value="<?php echo esc_attr( $email_type ); ?>" />
		<input type="hidden" name="double_optin" value="<?php echo esc_attr( $double_optin ); ?>" />
		<input type="hidden" name="update_existing" value="<?php echo esc_attr( $update_existing ); ?>" />
		<input type="hidden" name="list" value="<?php echo esc_attr( $list ); ?>" />
		<input type="hidden" name="groups" value="<?php echo esc_attr( $groups ); ?>" />
		<input type="hidden" name="success_message" value="<?php echo esc_attr( $success_message ); ?>" />
		<input type="hidden" name="show_privacy_field" value="<?php echo $show_privacy_field ? 'yes' : 'no'; ?>"/>

		<?php wp_nonce_field( 'yith_wcmc_subscribe', 'yith_wcmc_subscribe_nonce' ); ?>
		<input class="submit-form" type="submit" value="<?php echo esc_attr( $submit_label ); ?>" />
	</form>
</div>

<!-- AFTER NEWSLETTER SUBSCRIPTION FORM -->
<?php
if ( isset( $after ) ) {
	echo $after; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

do_action( 'after_newsletter_subscription_form' );
