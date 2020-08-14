<?php
/**
 * Referral Link Generator
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

<div class="yith-wcaf yith-wcaf-link-generator woocommerce">

	<?php
	if ( function_exists( 'wc_print_notices' ) ) {
		wc_print_notices();
	}
	?>

	<?php do_action( 'yith_wcaf_before_dashboard_section', 'link-generator' ); ?>
	<?php do_action( 'yith_wcaf_before_link_generator' ); ?>

	<div class="left-column <?php echo ( ! $show_right_column ) ? 'full-width' : ''; ?>">

		<?php if ( $affiliate_id ) : ?>
			<p>
				<?php
				// translators: 1. Affiliate token.
				echo wp_kses_post( sprintf( __( 'Your affiliate ID is: <strong>%s</strong>', 'yith-woocommerce-affiliates' ), $affiliate_token ) );
				?>
			</p>

			<p>
				<?php
				// translators: 1. Referral link.
				echo wp_kses_post( sprintf( __( 'Your referral URL is: <strong class="copy-target">%s</strong>', 'yith-woocommerce-affiliates' ), $referral_link ) );
				?>
				<?php echo sprintf( '<small>%s <span class="copy-trigger">%s</span> %s</small>', esc_html__( '(Now', 'yith-woocommerce-affiliates' ), esc_html__( 'copy', 'yith-woocommerce-affiliates' ), esc_html__( 'this referral link and share it anywhere)', 'yith-woocommerce-affiliates' ) ); ?>
			</p>
		<?php endif; ?>

		<p>
			<?php esc_html_e( 'Enter any URL from this site into the form below to generate your referral link to that page', 'yith-woocommerce-affiliates' ); ?>
		</p>

		<form method="post">
			<?php if ( ! is_user_logged_in() ) : ?>
				<p class="form form-row">
					<label for="username"><?php esc_html_e( 'Affiliate login', 'yith-woocommerce-affiliates' ); ?></label>
					<input type="text" name="username" id="username" value="<?php echo esc_attr( $username ); ?>" />
				</p>
			<?php endif; ?>

			<p class="form form-row">
				<label for="original_url"><?php esc_html_e( 'Page URL', 'yith-woocommerce-affiliates' ); ?></label>
				<input type="url" name="original_url" id="original_url" value="<?php echo esc_attr( $original_url ); ?>" />
			</p>

			<p class="form form-row">
				<label for="generated_url"><?php esc_html_e( 'Referral URL', 'yith-woocommerce-affiliates' ); ?></label>
				<input class="copy-target" readonly="readonly" type="url" name="generated_url" id="generated_url" value="<?php echo esc_attr( $generated_url ); ?>" />
				<?php echo ( ! empty( $generated_url ) ) ? sprintf( '<small>%s <span class="copy-trigger">%s</span> %s</small>', esc_html__( '(Now', 'yith-woocommerce-affiliates' ), esc_html__( 'copy', 'yith-woocommerce-affiliates' ), esc_html__( 'this referral link and share it anywhere)', 'yith-woocommerce-affiliates' ) ) : ''; ?>
			</p>

			<input type="submit" value="<?php esc_attr_e( 'Generate', 'yith-woocommerce-affiliates' ); ?>" />
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
	yith_wcaf_get_template( 'navigation-menu.php', $atts, 'shortcodes' )
	?>

	<?php do_action( 'yith_wcaf_after_link_generator' ); ?>

	<?php
	if ( apply_filters( 'yith_wcaf_share_conditions', true ) && $share_enabled ) {
		yith_wcaf_get_template( 'share.php', $share_atts, 'shortcodes' );
	}

	do_action( 'yith_wcaf_after_social_share_template' );
	?>

	<?php do_action( 'yith_wcaf_after_dashboard_section', 'link-generator' ); ?>
</div>
