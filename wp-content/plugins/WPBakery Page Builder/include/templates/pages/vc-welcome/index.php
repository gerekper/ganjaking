<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
preg_match( '/^(\d+)(\.\d+)?/', WPB_VC_VERSION, $matches );
$custom_tag = 'script'; // Update to wp_add_inline later
?>
<div class="wrap vc-page-welcome about-wrap">
	<h1><?php echo sprintf( esc_html__( 'Welcome to WPBakery Page Builder %s', 'js_composer' ), esc_html( isset( $matches[0] ) ? $matches[0] : WPB_VC_VERSION ) ); ?></h1>

	<div class="about-text">
		<?php esc_html_e( 'The leading no-code solution for building and managing WordPress sites.', 'js_composer' ); ?>
	</div>
	<div class="wp-badge vc-page-logo">
		<?php echo sprintf( esc_html__( 'Version %s', 'js_composer' ), esc_html( WPB_VC_VERSION ) ); ?>
	</div>
	<p class="vc-page-actions">
		<?php
		if ( vc_user_access()
				->wpAny( 'manage_options' )
				->part( 'settings' )
				->can( 'vc-general-tab' )
				->get() && ( ! is_multisite() || ! is_main_site() )
		) :
			?>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=vc-general' ) ); ?>"
			class="button button-primary"><?php esc_html_e( 'Settings', 'js_composer' ); ?></a><?php endif; ?>
		<a href="https://twitter.com/share" class="twitter-share-button"
			data-via="wpbakery"
			data-text="Take full control over your #WordPress site with WPBakery Page Builder page builder"
			data-url="https://wpbakery.com" data-size="large">Tweet</a>
		<<?php echo esc_attr( $custom_tag ); ?>>! function ( d, s, id ) {
				var js, fjs = d.getElementsByTagName( s )[ 0 ], p = /^http:/.test( d.location ) ? 'http' : 'https';
				if ( ! d.getElementById( id ) ) {
					js = d.createElement( s );
					js.id = id;
					js.src = p + '://platform.twitter.com/widgets.js';
					fjs.parentNode.insertBefore( js, fjs );
				}
			}( document, 'script', 'twitter-wjs' );</<?php echo esc_attr( $custom_tag ); ?>>
	</p>
	<?php
	vc_include_template( '/pages/partials/_tabs.php', array(
		'slug' => $page->getSlug(),
		'active_tab' => $active_page->getSlug(),
		'tabs' => $pages,
	) );
	?>
	<?php
	// @codingStandardsIgnoreLine
	print $active_page->render();
	?>
</div>
