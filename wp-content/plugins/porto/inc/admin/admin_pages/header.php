<h2 class="porto-admin-nav">
<?php
	$items = array(
		'porto' => array( 'admin.php?page=porto', __( 'Dashboard', 'porto' ) ),
	);
	$items['page_layouts'] = array( 'admin.php?page=porto-page-layouts', __( 'Page Layouts', 'porto' ) );
	if ( get_theme_mod( 'theme_options_use_new_style', false ) ) {
		$items['theme_options']    = array( 'customize.php', __( 'Theme Options', 'porto' ) );
		$items['advanced_options'] = array( 'themes.php?page=porto_settings', __( 'Advanced', 'porto' ) );
	} else {
		$items['theme_options'] = array( 'themes.php?page=porto_settings', __( 'Theme Options', 'porto' ) );
	}
	$items['setup_wizard']    = array( 'admin.php?page=porto-setup-wizard', __( 'Setup Wizard', 'porto' ) );
	$items['optimize_wizard'] = array( 'admin.php?page=porto-speed-optimize-wizard', __( 'Speed Optimize Wizard', 'porto' ) );
	$items['tools']           = array( 'admin.php?page=porto-tools', __( 'Tools', 'porto' ) );
	if ( post_type_exists( 'porto_builder' ) ) {
		$items['builder'] = array( 'edit.php?post_type=porto_builder', __( 'Templates Builder', 'porto' ) );
	}

	foreach ( $items as $key => $item ) {
		printf( '<a href="%s"' . ( isset( $active_item ) && $active_item == $key ? ' class="active nolink"' : '' ) . '>%s</a>', esc_url( admin_url( $item[0] ) ), esc_html( $item[1] ) );
	}
	?>
</h2>
<div class="porto-admin-header">
	<div class="header-left">
		<h1><?php echo esc_html( $title ); ?></h1>
		<h6><?php echo esc_html( $subtitle ); ?></h6>
	</div>
	<div class="header-right">
		<?php /* translators: theme version */ ?>
		<div class="porto-logo"><img src="<?php echo PORTO_URI . '/images/logo/logo_white_small.png'; ?>" alt="Porto"><span class="version"><?php printf( __( 'version %s', 'porto' ), PORTO_VERSION ); ?></span></div>
	</div>
</div>
