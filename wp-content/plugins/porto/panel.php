<?php
global $porto_settings, $porto_settings_optimize;
$header_type = porto_get_header_type();

if ( 'overlay' == $porto_settings['menu-type'] ) {
	if ( empty( $header_type ) ) {
		global $porto_menu_wrap;
		if ( empty( $porto_menu_wrap ) ) {
			return;
		}
	} elseif ( ! in_array( (int) $header_type, array( 1, 4, 9, 13, 14, 17 ) ) ) {
		return;
	}
}
?>
<div class="panel-overlay"></div>
<div id="side-nav-panel" class="<?php echo ( isset( $porto_settings['mobile-panel-pos'] ) && $porto_settings['mobile-panel-pos'] ) ? $porto_settings['mobile-panel-pos'] : ''; ?>">
	<a href="#" aria-label="Mobile Close" class="side-nav-panel-close"><i class="fas fa-times"></i></a>
<?php if ( ( isset( $_POST['action'] ) && 'porto_lazyload_menu' == $_POST['action'] ) || empty( $porto_settings_optimize['lazyload_menu'] ) ) : ?>
	<?php
	if ( '7' == $header_type || '8' == $header_type || ( isset( $porto_settings['mobile-panel-add-switcher'] ) && $porto_settings['mobile-panel-add-switcher'] ) ) {
		// show currency and view switcher
		$switcher  = '';
		$switcher .= porto_mobile_currency_switcher();
		$switcher .= porto_mobile_view_switcher();

		if ( $switcher ) {
			echo '<div class="switcher-wrap">' . $switcher . '</div>';
		}
	}

	// show top navigation and mobile menu
	$menu = porto_mobile_menu( '19' == $header_type || empty( $header_type ) );

	if ( $menu ) {
		echo '<div class="menu-wrap">' . $menu . '</div>';
	}

	if ( ( ! porto_header_type_is_preset() || 1 == $header_type || 3 == $header_type || 4 == $header_type || 9 == $header_type || 13 == $header_type || 14 == $header_type ) && ! empty( $porto_settings['menu-block'] ) ) {
		echo '<div class="menu-custom-block">' . wp_kses_post( $porto_settings['menu-block'] ) . '</div>';
	}

	$menu = porto_mobile_top_navigation();

	if ( $menu ) {
		echo '<div class="menu-wrap">' . $menu . '</div>';
	}

	if ( isset( $porto_settings['mobile-panel-add-search'] ) && $porto_settings['mobile-panel-add-search'] ) {
		echo porto_search_form_content( true );
	}

	// show social links
	echo porto_header_socials();
	?>
<?php else : ?>
	<div class="skeleton-body porto-ajax-loading"><i class="porto-loading-icon"></i></div>
<?php endif; ?>
</div>
