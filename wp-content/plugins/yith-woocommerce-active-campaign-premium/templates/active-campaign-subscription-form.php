<?php
/**
 * Subscription form template (used in shortcode and widget)
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Active Campaign
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCAC' ) ) {
	exit;
} // Exit if accessed directly
?>

	<!-- BEFORE NEWSLETTER SUBSCRIPTION FORM -->
<?php
if ( isset( $before ) ) {
	echo $before; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
?>

<?php if ( $enable_style ) : ?>
	<style>
		<?php echo $style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</style>
<?php endif; ?>

	<div class="yith-wcac-subscription-form woocommerce" id="subscription_form_<?php echo esc_attr( $unique_id ); ?>">
		<?php if ( ! empty( $title ) ) : ?>
			<h3><?php echo esc_html( $title ); ?></h3>
		<?php endif; ?>

		<?php do_action( 'yith_wcac_after_subscription_form_title', $list ); ?>

		<?php if ( function_exists( 'wc_get_notices' ) ) : ?>
			<div class="subscription-notice">
				<?php
				$success = wc_get_notices( 'yith-wcac-success' );
				$errors  = wc_get_notices( 'yith-wcac-error' );
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
				wc_clear_notices();
				?>
			</div>
		<?php endif; ?>

		<?php
		do_action( 'yith_wcac_after_subscription_form_notice', $list );
		$hide_form = ! empty( $hide_form_after_registration ) ? $hide_form_after_registration ? 'yes' : 'no' : 'no';
		?>

		<form method="POST" data-hide="<?php echo esc_attr( $hide_form ); ?>">
			<?php
			$attributes = array(
				'list'               => $list,
				'fields'             => $fields,
				'use_placeholders'   => $use_placeholders,
				'fields_data'        => $fields_data,
				'context'            => $context,
				'show_tags'          => $show_tags,
				'tags_label'         => $tags_label,
				'status'             => $status,
				'success_message'    => $success_message,
				'show_privacy_field' => $show_privacy_field,
				'privacy_label'      => $privacy_label,
			);

			yith_wcac_get_template( 'active-campaign-subscription-form-content', $attributes );
			?>
			<input class="submit-form" type="submit" value="<?php echo esc_attr( $submit_label ); ?>"/>
		</form>
	</div>

	<!-- AFTER NEWSLETTER SUBSCRIPTION FORM -->
<?php
if ( isset( $after ) ) {
	echo $after; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

do_action( 'after_newsletter_subscription_form' );
