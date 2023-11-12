<?php
/**
 * UAEL Admin HTML.
 *
 * @package UAEL
 */

use UltimateElementor\Classes\UAEL_Helper;

$branding     = UAEL_Helper::get_white_labels();
$replace_logo = UAEL_Helper::is_replace_logo();
$hide_tagline = UAEL_Helper::is_hide_tagline();

?>
<div class="uael-menu-page-wrapper">
	<div id="uael-menu-page">
		<div class="uael-menu-page-header <?php echo esc_attr( implode( ' ', $uael_header_wrapper_class ) ); ?>">
			<div class="uael-container uael-flex">
				<div class="uael-title">
					<?php if ( '' !== $branding['plugin']['name'] && $replace_logo ) { ?>
						<span><?php echo wp_kses_post( $branding['plugin']['name'] ); ?></span>
					<?php } else { ?>
						<a href="<?php echo esc_url( $uael_visit_site_url ); ?>" target="_blank" rel="noopener" >
						<?php if ( $uael_icon ) { ?>
							<img src="<?php echo esc_url( UAEL_URL . 'admin/assets/images/uael_logo.svg' ); ?>" class="uael-header-icon" alt="<?php echo esc_attr( UAEL_PLUGIN_NAME ); ?> " >
						<?php } ?>
						<span class="uael-plugin-version"><?php echo esc_html( UAEL_VER ); ?></span>
						<?php do_action( 'uael_header_title' ); ?>
						</a>
					<?php } ?>
				</div>
				<div class="uael-top-links">
					<?php
					if ( false === $hide_tagline ) {
						esc_attr_e( 'Take Elementor to The Next Level!', 'uael' );
					}
					?>
					<?php if ( '' === $branding['agency']['author'] ) { ?>
						<?php echo '-'; ?>
						<a href='<?php echo esc_url( UAEL_DOMAIN ); ?>'target="_blank" rel="">View Demos</a>
					<?php } ?>
				</div>
			</div>
		</div>

		<?php
		if ( isset( $_REQUEST['uael_admin_nonce'] ) && wp_verify_nonce( sanitize_text_field( $_REQUEST['uael_admin_nonce'] ), 'uael_admin_nonce' ) ) {
			if ( isset( $_REQUEST['message'] ) && ( 'saved' === $_REQUEST['message'] || 'saved_ext' === $_REQUEST['message'] ) ) {
				?>
					<div id="message" class="notice notice-success is-dismissive uael-notice"><p> <?php esc_html_e( 'Settings saved successfully.', 'uael' ); ?> </p></div>
				<?php
			} elseif ( isset( $_REQUEST['message'] ) && 'error' === $_REQUEST['message'] ) {
				?>
				<div id="message" class="notice notice-error is-dismissive uael-notice"><p> <?php echo isset( $_REQUEST['error'] ) ? esc_html( sanitize_text_field( $_REQUEST['error'] ) ) : ''; ?> </p></div>
				<?php
			}
		}
		do_action( 'uael_render_admin_content' );
		?>
	</div>
</div>
