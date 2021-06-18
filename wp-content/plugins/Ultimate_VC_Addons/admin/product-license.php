<?php
/**
 * Product License Page
 *
 *  @package Product License Page
 */

if ( isset( $_GET['author'] ) ) { // PHPCS:ignore:WordPress.Security.NonceVerification.Recommended
	$author = true;
} else {
	$author = false;
}
	$author_extend = '';
if ( $author ) {
	$author_extend = '&author';
}
?>

<div class="wrap about-wrap bsf-page-wrapper ultimate-smooth-scroll bend">
<div class="wrap-container">
	<div class="bend-heading-section ultimate-header">
	<h1><?php esc_html_e( 'Product License', 'ultimate_vc' ); ?></h1>
	<h3><?php esc_html_e( "Let's activate your license of Ultimate Addons for WPBakery Page Builder that enable you automatic updates, direct support and many other benefits.", 'ultimate_vc' ); ?></h3>
	<div class="bend-head-logo">
		<div class="bend-product-ver">
			<?php
			esc_html_e( 'Version', 'ultimate_vc' );
			echo ' ' . ULTIMATE_VERSION; // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</div>
	</div>
	</div><!-- bend-heading section -->

	<div id="msg"></div>
	<div id="bsf-message"></div>

	<div class="bend-content-wrap">
	<div class="smile-settings-wrapper">
			<h2 class="nav-tab-wrapper">
				<a href="<?php echo admin_url( 'admin.php?page=about-ultimate' . $author_extend ); // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped ?>" data-tab="about-ultimate" class="nav-tab"> <?php echo esc_html__( 'About', 'ultimate_vc' ); ?> </a>
				<a href="<?php echo admin_url( 'admin.php?page=ultimate-dashboard' . $author_extend ); // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped ?>" data-tab="ultimate-modules" class="nav-tab"> <?php echo esc_html__( 'Elements', 'ultimate_vc' ); ?> </a>
				<a href="<?php echo admin_url( 'admin.php?page=ultimate-smoothscroll' . $author_extend ); // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped ?>" data-tab="css-settings" class="nav-tab"> <?php echo esc_html__( 'Smooth Scroll', 'ultimate_vc' ); ?> </a>
				<a href="<?php echo admin_url( 'admin.php?page=ultimate-scripts-and-styles' . $author_extend ); // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped ?>" data-tab="css-settings" class="nav-tab"> <?php echo esc_html__( 'Scripts and Styles', 'ultimate_vc' ); ?> </a>
				<?php if ( $author ) : ?>
					<a href="<?php echo admin_url( 'admin.php?page=ultimate-debug-settings' ); // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped ?>" data-tab="ultimate-debug" class="nav-tab"> Debug </a>
				<?php endif; ?>
			</h2>
	</div><!-- smile-settings-wrapper -->

	</hr>

	<div class="container ultimate-content">
		<div class="col-md-12">			
			<?php

			$product_id = bsf_extract_product_id( __ULTIMATE_ROOT__ );

			$args = array(
				'product_id'          => $product_id,
				'submit_button_class' => 'uavc_submit_license button-primary button-hero',
			);

			echo bsf_envato_register( $args ); // PHPCS:ignore:WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</div> <!--col-md-12 -->
	</div> <!-- ultimate-content -->
	</div> <!-- bend-content-wrap -->
</div> <!-- .wrap-container -->
</div> <!-- .bend -->

<style type="text/css">

.bsf-license-key-registration {
	max-width: 450px;
	margin-top: 30px;
}

.license-error-heading.bsf-license-not-active-6892199 {
	display: block;
	padding: 10px 20px;
	background: #d54e21;
	color: #fff;
	font-size: 13px;
	font-weight: normal;
}

.bsf-license-not-active-6892199 span {
	color: #ae5842;
	font-style: italic;
}

/* Bad but temorary CSS */
/*.bsf-license-key-registration input.button.uavc_submit_license.button-primary {
	display: inline-block !important;
	text-decoration: none !important;
	font-size: 13px !important;
	line-height: 26px !important;
	height: 28px !important;
	margin: 0 !important;
	padding: 0 10px 1px !important;
	cursor: pointer !important;
	border-width: 1px !important;
	border-style: solid !important;
	-webkit-appearance: none !important;
	-webkit-border-radius: 3px !important;
	border-radius: 3px !important;
	white-space: nowrap !important;
	-webkit-box-sizing: border-box !important;
	-moz-box-sizing: border-box !important;
	box-sizing: border-box !important;
	margin-top: 20px !important;
}*/

.bsf-license-key-registration h2,
.bsf-license-key-registration h3 {
	color: #23282d;
	font-size: 1.3em;
}

.bsf-license-active-6892199  span {
	color: #3cb341;
	font-style: italic;
}

.bsf-license-message {
	margin-top: 20px;
	display: inline-block;
	background: white;
	padding: 10px;
	font-size: 14px;
}

.license-success {
	border-left: 5px solid green;
}

.license-error {
	border-left: 5px solid red;
}

.bsf-current-license-error-6892199,
.bsf-current-license-error-6892199 h2,
.bsf-current-license-success-6892199 {
	text-align: left;
	margin: 20px 0;
	display: block;
}

.bsf-current-license-error-6892199 {    
	color: #cc0808;
}

.bsf-license-key-registration .bsf-license-form-6892199.bsf-license-not-active-6892199 input.button.uavc_submit_license.button-primary {
	margin-top: 0 !important;
}

</style>

