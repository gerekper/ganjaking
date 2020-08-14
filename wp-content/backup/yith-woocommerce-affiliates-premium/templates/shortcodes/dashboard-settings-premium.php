<?php
/**
 * Affiliate Dashboard Settings
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.5
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly
?>

<div class="yith-wcaf yith-wcaf-settings woocommerce">

	<?php
	if ( function_exists( 'wc_print_notices' ) ) {
		wc_print_notices();
	}
	?>

	<?php do_action( 'yith_wcaf_before_dashboard_section', 'settings' ); ?>

	<div class="left-column <?php echo ( ! $show_right_column ) ? 'full-width' : ''; ?>">

		<form method="post">

			<?php do_action( 'yith_wcaf_settings_form_start' ); ?>

			<?php if ( apply_filters( 'yith_wcaf_show_additional_fields', 'yes' === $show_additional_fields, 'settings' ) ) : ?>

				<?php if ( 'yes' === $show_name_field ) : ?>
					<p class="form form-row">
						<label for="first_name"><?php esc_html_e( 'First name', 'yith-woocommerce-affiliates' ); ?></label>
						<input type="text" name="first_name" id="first_name" value="<?php echo esc_attr( $affiliate_name ); ?>" />
						<small><?php esc_html_e( '(First name for your account)', 'yith-woocommerce-affiliates' ); ?></small>
					</p>
				<?php endif; ?>

				<?php if ( 'yes' === $show_surname_field ) : ?>
					<p class="form form-row">
						<label for="last_name"><?php esc_html_e( 'Last name', 'yith-woocommerce-affiliates' ); ?></label>
						<input type="text" name="last_name" id="first_name" value="<?php echo esc_attr( $affiliate_surname ); ?>" />
						<small><?php esc_html_e( '(Last name for your account)', 'yith-woocommerce-affiliates' ); ?></small>
					</p>
				<?php endif; ?>

			<?php endif; ?>

			<?php if ( apply_filters( 'yith_wcaf_payment_email_required', true ) ) : ?>
			<p class="form form-row">
				<label for="payment_email"><?php esc_html_e( 'Payment email', 'yith-woocommerce-affiliates' ); ?></label>
				<input type="email" name="payment_email" id="payment_email" value="<?php echo esc_attr( $payment_email ); ?>" />
				<small><?php esc_html_e( '(Email address where you want to receive PayPal payments for commissions)', 'yith-woocommerce-affiliates' ); ?></small>
			</p>
			<?php endif; ?>

			<?php do_action( 'yith_wcaf_settings_form_after_payment_email' ); ?>

			<?php if ( apply_filters( 'yith_wcaf_show_additional_fields', 'yes' === $show_additional_fields, 'settings' ) ) : ?>

				<?php if ( 'yes' === $show_website_field ) : ?>
					<p class="form form-row">
						<label for="website"><?php echo esc_html( apply_filters( 'yith_wcaf_website_label', __( 'Website', 'yith-woocommerce-affiliates' ) ) ); ?></label>
						<input type="<?php echo esc_attr( apply_filters( 'yith_wcaf_website_type', 'url' ) ); ?>" name="website" id="website" value="<?php echo esc_attr( $affiliate_website ); ?>" />
						<small><?php echo esc_html( apply_filters( 'yith_wcaf_website_description', __( '(Your main website, or the one where you plan to promote our products)', 'yith-woocommerce-affiliates' ) ) ); ?></small>
					</p>
				<?php endif; ?>

				<?php if ( 'yes' === $show_promotional_methods_field ) : ?>
					<p class="form form-row">
						<label for="how_promote"><?php echo esc_html( apply_filters( 'yith_wcaf_promotional_methods_label', __( 'How will you promote our site?', 'yith-woocommerce-affiliates' ) ) ); ?></label>
						<select name="how_promote" id="how_promote">
							<?php
							$how_promote_options = yith_wcaf_get_promote_methods();

							if ( ! empty( $how_promote_options ) ) :
								foreach ( $how_promote_options as $id => $value ) :
									?>
									<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $promotional_method, $id ); ?>><?php echo esc_html( $value ); ?></option>
									<?php
								endforeach;
							endif;
							?>
						</select>
						<small><?php echo esc_html( apply_filters( 'yith_wcaf_promotional_methods_description', __( '(How do you plan to promote our products? Are you going to use your site, or other means?)', 'yith-woocommerce-affiliates' ) ) ); ?></small>
					</p>

					<p class="form form-row">
						<label for="custom_promote"><?php echo esc_html( apply_filters( 'yith_wcaf_custom_promote_label', __( 'Specify how will you promote our site', 'yith-woocommerce-affiliates' ) ) ); ?></label>
						<textarea name="custom_promote" id="custom_promote"><?php echo esc_html( $custom_promotional_method ); ?></textarea>
						<small><?php echo esc_html( apply_filters( 'yith_wcaf_custom_promote_description', __( '(Please, specify the method you\'re going to use to promote our products)', 'yith-woocommerce-affiliates' ) ) ); ?></small>
					</p>
				<?php endif; ?>

			<?php endif; ?>

			<p class="form form-row">
				<label for="notify_pending_commissions">
					<input type="checkbox" name="notify_pending_commissions" id="notify_pending_commissions" value="yes" <?php checked( $notify_pending_commissions, 'yes' ); ?> />
					<?php esc_html_e( 'Notify on new commissions', 'yith-woocommerce-affiliates' ); ?>
				</label>
				<small><?php esc_html_e( '(Select this option if you want to be emailed each time a commission status switches to pending)', 'yith-woocommerce-affiliates' ); ?></small>
			</p>

			<p class="form form-row">
				<label for="notify_paid_commissions">
					<input type="checkbox" name="notify_paid_commissions" id="notify_paid_commissions" value="yes" <?php checked( $notify_paid_commissions, 'yes' ); ?> />
					<?php esc_html_e( 'Notify on commission paid', 'yith-woocommerce-affiliates' ); ?>
				</label>
				<small><?php esc_html_e( '(Select this option if you want to be emailed each time a commission is paid)', 'yith-woocommerce-affiliates' ); ?></small>
			</p>

			<?php do_action( 'yith_wcaf_settings_form' ); ?>

			<input type="submit" name="settings_submit" value="<?php esc_attr_e( 'Submit', 'yith-woocommerce-affiliates' ); ?>" />

		</form>

	</div>

	<!--NAVIGATION MENU-->
	<?php
	$atts = array(
		'show_right_column'    => $show_right_column,
		'show_left_column'     => true,
		'show_dashboard_links' => $show_dashboard_links,
		'dashboard_links'      => $dashboard_links,
	);
	yith_wcaf_get_template( 'navigation-menu.php', $atts, 'shortcodes' );
	?>

	<?php do_action( 'yith_wcaf_after_dashboard_section', 'settings' ); ?>

</div>
