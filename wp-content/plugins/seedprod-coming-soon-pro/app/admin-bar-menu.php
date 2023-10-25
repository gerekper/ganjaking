<?php

	/**
	 * Display admin bar when active
	 */
function seedprod_pro_admin_bar_menu( $wp_admin_bar ) {

	$ts                = get_option( 'seedprod_settings' );
	$seedprod_settings = json_decode( $ts, true );

	// get preview mode
	$theme_preview_mode = get_option( 'seedprod_theme_template_preview_mode' );
	if ( ! empty( $theme_preview_mode ) ) {
		$theme_preview_mode = true;
	} else {
		$theme_preview_mode = false;
	}

	// if (empty($seedprod_settings['enable_coming_soon_mode']) && empty($seedprod_settings['enable_maintenance_mode'])) {
	//     return false;
	// }

	// Disable if page line editor open
	$pl_edit = isset( $_GET['pl_edit'] ) ? sanitize_text_field( wp_unslash( $_GET['pl_edit'] ) ) : null; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( $pl_edit ) {
		return false;
	}

	$icon = '
        <span class="seedprod-mb-icon"><svg width="22" height="23" viewBox="0 0 22 23" fill="none" xmlns="http://www.w3.org/2000/svg">
        <g filter="url(#filter0_d)">
        <path fill-rule="evenodd" clip-rule="evenodd" d="M4 0C4 0 4.32666 0.022488 11.036 0.91214C17.7453 1.80179 20.0674 8.70527 15.9594 14.7304C16.5949 9.34689 15.4319 3.76206 10.7604 3.10916C6.08886 2.45626 6.49574 2.5563 6.49574 2.5563C6.49574 2.5563 6.57314 3.74204 7.01149 6.92954C7.44984 10.117 9.90279 11.6803 12.0495 12.485C12.0495 12.485 12.1754 8.75455 10.9777 7.1126C9.77997 5.47066 8.2899 4.38023 8.2899 4.38023C8.2899 4.38023 11.7916 4.80636 13.1137 7.28431C14.4358 9.76225 14.307 15 14.307 15L12.8808 14.9251C9.04318 14.4574 5.45792 12.1126 4.84831 7.19318C4.23871 2.27373 4 0 4 0Z" fill="black"/>
        <path fill-rule="evenodd" clip-rule="evenodd" d="M4 0C4 0 4.32666 0.022488 11.036 0.91214C17.7453 1.80179 20.0674 8.70527 15.9594 14.7304C16.5949 9.34689 15.4319 3.76206 10.7604 3.10916C6.08886 2.45626 6.49574 2.5563 6.49574 2.5563C6.49574 2.5563 6.57314 3.74204 7.01149 6.92954C7.44984 10.117 9.90279 11.6803 12.0495 12.485C12.0495 12.485 12.1754 8.75455 10.9777 7.1126C9.77997 5.47066 8.2899 4.38023 8.2899 4.38023C8.2899 4.38023 11.7916 4.80636 13.1137 7.28431C14.4358 9.76225 14.307 15 14.307 15L12.8808 14.9251C9.04318 14.4574 5.45792 12.1126 4.84831 7.19318C4.23871 2.27373 4 0 4 0Z" fill="white"/>
        </g>
        <defs>
        <filter id="filter0_d" x="0" y="0" width="22" height="23" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
        <feFlood flood-opacity="0" result="BackgroundImageFix"/>
        <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"/>
        <feOffset dy="4"/>
        <feGaussianBlur stdDeviation="2"/>
        <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/>
        <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow"/>
        <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow" result="shape"/>
        </filter>
        </defs>
        </svg></span>';
	$text = '<span>SeedProd</span>';
	if ( ! empty( $seedprod_settings['enable_coming_soon_mode'] ) ) {
		$text = '<span>' . __( 'Coming Soon Mode Active', 'seedprod-pro' ) . '</span>';
	} elseif ( ! empty( $seedprod_settings['enable_maintenance_mode'] ) ) {
		$text = '<span>' . __( 'Maintenance Mode Active', 'seedprod-pro' ) . '</span>';
	} elseif ( ! empty( $theme_preview_mode ) ) {
		$text = '<span>' . __( 'Theme Preview Mode Active', 'seedprod-pro' ) . '</span>';
	}

	$notification = '';

	//Add the main siteadmin menu item
	$wp_admin_bar->add_menu(
		array(
			'id'     => 'seedprod_admin_bar',
			'href'   => admin_url() . 'admin.php?page=seedprod_pro#/',
			'parent' => 'top-secondary',
			'title'  => $icon . $text . $notification,
			'meta'   => array( 'class' => 'seedprod-mode-active' ),
		)
	);

	// $args = array(
	//     'id'    => 'media_settings',
	//     'title' => 'Media Settings',
	//     'href'  => admin_url() . 'options-media.php',
	//     'parent' => 'seedprod_admin_bar'
	// );
	// $wp_admin_bar->add_node( $args );

}

// nonce covered by menu item capability.
