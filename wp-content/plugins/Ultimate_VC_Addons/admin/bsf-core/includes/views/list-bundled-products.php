<?php
/**
 * Plugin installer.
 *
 * @package bsf-core
 */

/**
 * Prevent direct access.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

$current_page = '';

if ( isset( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$current_page = sanitize_text_field( $_GET['page'] );  // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	$arr        = explode( 'bsf-extensions-', $current_page );
	$product_id = $arr[1];
}

$redirect_url = network_admin_url( 'admin.php?page=' . $current_page );

$extensions_installer_heading = apply_filters( "bsf_extinstaller_heading_{$product_id}", 'iMedica Extensions' );

$extensions_installer_subheading = apply_filters( "bsf_extinstaller_subheading_{$product_id}", 'iMedica is already very flexible & feature rich theme. It further aims to be all-in-one solution for your WordPress needs. Install any necessary extensions you like from below and take it on the steroids.' );

$reset_bundled_url = bsf_exension_installer_url( $product_id . '&remove-bundled-products&redirect=' . $redirect_url );

?>
<div class="clear"></div>
<div class="wrap about-wrap bsf-sp-screen bend <?php echo 'extension-installer-' . sanitize_html_class( $product_id ); ?>">

	<div class="bend-heading-section extension-about-header">

		<h1><?php echo esc_html( $extensions_installer_heading ); ?></h1>
		<h3><?php echo esc_html( $extensions_installer_subheading ); ?></h3>

		<div class="bend-head-logo">
			<div class="bend-product-ver"><?php esc_html_e( 'Extensions ', 'bsf' ); ?></div>
		</div>
	</div>  <!--heading section-->

	<div class="bend-content-wrap">
		<hr class="bsf-extensions-lists-separator">
		<h3 class="bf-ext-sub-title"><?php esc_html_e( 'Available Extensions', 'bsf' ); ?></h3>
		<?php $nonce = wp_create_nonce( 'bsf_activate_extension_nonce' ); ?>
		<input type="hidden" id="bsf_activate_extension_nonce" value="<?php echo esc_attr( $nonce ); ?>" >
		<ul class="bsf-extensions-list">
			<?php echo wp_kses_post( bsf_render_bundled_products( $product_id, false ) ); ?>
		</ul>

		<hr class="bsf-extensions-lists-separator">
		<h3 class="bf-ext-sub-title"><?php esc_html_e( 'Installed Extensions', 'bsf' ); ?></h3>
		<ul class="bsf-extensions-list">
			<?php echo wp_kses_post( bsf_render_bundled_products( $product_id, true ) ); ?>
		</ul>
	</div>
</div>
