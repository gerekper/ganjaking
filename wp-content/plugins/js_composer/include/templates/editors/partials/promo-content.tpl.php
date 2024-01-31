<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/** @var bool $is_about_page */
?>
<img class="vc-featured-img" src="<?php echo esc_url( vc_asset_url( 'vc/wpb_introduce_seo.png' ) ); ?>"/>

<div class="vc-feature-text">
	<h3><?php esc_html_e( 'Introducing WPBakery SEO', 'js_composer' ); ?></h3>

	<p><?php esc_html_e( 'Streamline your search engine optimization efforts with WPBakery\'s built-in SEO toolkit. Access WPBakery\'s SEO tools directly from the editor to boost your website\'s visibility and attract a wider audience:', 'js_composer' ); ?></p>
	<ul>
		<li><?php esc_html_e( 'Get social media sharing previews', 'js_composer' ); ?></li>
		<li><?php esc_html_e( 'Gain insights from content analysis and receive suggestions for improvement', 'js_composer' ); ?></li>
		<li><?php esc_html_e( 'Track your site\'s SEO health with a color-coded status indicator', 'js_composer' ); ?></li>
	</ul>
	<?php
	$tabs = vc_settings()->getTabs();
	$is_license_tab_access = isset( $tabs['vc-updater'] ) && vc_user_access()->part( 'settings' )->can( 'vc-updater-tab' )->get();
	if ( $is_about_page && ! vc_license()->isActivated() && $is_license_tab_access ) : ?>
		<div class="vc-feature-activation-section">
			<?php $url = 'admin.php?page=vc-updater'; ?>
			<a href="<?php echo esc_attr( is_network_admin() ? network_admin_url( $url ) : admin_url( $url ) ); ?>" class="vc-feature-btn" id="vc_settings-updater-button" data-vc-action="activation"><?php esc_html_e( 'Activate License', 'js_composer' ); ?></a>
			<p class="vc-feature-info-text">
				<?php esc_html_e( 'Direct plugin activation only.', 'js_composer' ); ?>
				<a href="https://wpbakery.com/wpbakery-page-builder-license/?utm_source=wpb-welcome-page&utm_medium=wpb-whats-new-tab" target="_blank" rel="noreferrer noopener"><?php esc_html_e( 'Don\'t have a license?', 'js_composer' ); ?></a>
			</p>
		</div>
	<?php endif; ?>
</div>
