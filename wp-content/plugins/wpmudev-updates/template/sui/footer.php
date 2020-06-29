<?php
$membership_type = WPMUDEV_Dashboard::$api->get_membership_type( $project_id );
$hide_footer     = false;
$footer_text     = sprintf( __( 'Made with %s by WPMU DEV', 'wpmudev' ), ' <i class="sui-icon-heart"></i>' );
if ( 'full' === $membership_type ) {
	$whitelabel_settings = WPMUDEV_Dashboard::$site->get_whitelabel_settings();
	$hide_footer         = $whitelabel_settings['footer_enabled'];
	$footer_text         = apply_filters( 'wpmudev_branding_footer_text', $footer_text );
}

$footer_nav_links = array(
	array(
		'href' => 'https://premium.wpmudev.org/hub/',
		'name' => __( 'The Hub', 'wpmudev' ),
	),
	array(
		'href' => 'https://premium.wpmudev.org/projects/category/plugins/',
		'name' => __( 'Plugins', 'wpmudev' ),
	),
	array(
		'href' => 'https://premium.wpmudev.org/roadmap/',
		'name' => __( 'Roadmap', 'wpmudev' ),
	),
	array(
		'href' => 'https://premium.wpmudev.org/hub/support',
		'name' => __( 'Support', 'wpmudev' ),
	),
	array(
		'href' => 'https://premium.wpmudev.org/docs/',
		'name' => __( 'Docs', 'wpmudev' ),
	),
	array(
		'href' => 'https://premium.wpmudev.org/hub/community/',
		'name' => __( 'Community', 'wpmudev' ),
	),
	array(
		'href' => 'https://premium.wpmudev.org/academy/',
		'name' => __( 'Academy', 'wpmudev' ),
	),
);

if ( 'free' === $membership_type ) {
	$footer_nav_links = array(
		array(
			'href' => 'https://profiles.wordpress.org/wpmudev#content-plugins',
			'name' => __( 'Free Plugins', 'wpmudev' ),
		),
		array(
			'href' => 'https://premium.wpmudev.org/features/',
			'name' => __( 'Membership', 'wpmudev' ),
		),
		array(
			'href' => 'https://premium.wpmudev.org/roadmap/',
			'name' => __( 'Roadmap', 'wpmudev' ),
		),
		array(
			'href' => 'https://premium.wpmudev.org/docs/',
			'name' => __( 'Docs', 'wpmudev' ),
		),
		array(
			'href' => 'https://premium.wpmudev.org/hub-welcome/',
			'name' => __( 'The Hub', 'wpmudev' ),
		),

	);
}

$footer_nav_links[] = array(
	'href' => 'https://premium.wpmudev.org/terms-of-service/',
	'name' => __( 'Terms of Service', 'wpmudev' ),
);
$footer_nav_links[] = array(
	'href' => 'https://incsub.com/privacy-policy/',
	'name' => __( 'Privacy Policy', 'wpmudev' ),
);
?>
<div class="sui-footer"><?php echo $footer_text ?></div>

<?php if ( ! $hide_footer ) : ?>
	<ul class="sui-footer-nav">
		<?php foreach ( $footer_nav_links as $footer_nav_link ) : ?>
			<li><a href="<?php echo esc_url( $footer_nav_link['href'] ); ?>" target="_blank"><?php echo esc_html( $footer_nav_link['name'] ); ?></a></li>
		<?php endforeach; ?>
	</ul>
	<ul class="sui-footer-social">
		<li><a href="https://www.facebook.com/wpmudev" target="_blank">
				<i class="sui-icon-social-facebook" aria-hidden="true"></i>
				<span class="sui-screen-reader-text"><?php _e( 'Facebook', 'wpmudev' ); ?></span>
			</a></li>
		<li><a href="https://twitter.com/wpmudev" target="_blank">
				<i class="sui-icon-social-twitter" aria-hidden="true"></i></a>
			<span class="sui-screen-reader-text"><?php _e( 'Twitter', 'wpmudev' ); ?></span>
		</li>
		<li><a href="https://www.instagram.com/wpmu_dev/" target="_blank">
				<i class="sui-icon-instagram" aria-hidden="true"></i>
				<span class="sui-screen-reader-text"><?php _e( 'Instagram', 'wpmudev' ); ?></span>
			</a>
		</li>
	</ul>
<?php endif; ?>
