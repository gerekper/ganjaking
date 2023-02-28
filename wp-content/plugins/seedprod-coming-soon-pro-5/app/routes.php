<?php
/**
 * Postback Routes
 */


add_action( 'admin_init', 'seedprod_pro_export_subscribers' );



/**
 * Admin Menu Routes
 */


add_action( 'admin_menu', 'seedprod_pro_create_menus' );

/**
 * Create menus for plugin.
 */
function seedprod_pro_create_menus() {
	// get notifications count
	$notification        = '';
	$n                   = new SeedProd_Notifications();
	$notifications_count = $n->get_count();

	
	// check for invalid license
	$seedprod_a = get_option( 'seedprod_per' );
	if ( empty( $seedprod_a ) ) {
		$notifications_count = 1;
	}

	
	if ( ! empty( $notifications_count ) ) {
		$notification = '<div class="seedprod-menu-notification-counter"><span>' . $notifications_count . '</span></div>';
	}

	add_menu_page(
		'SeedProd',
		'SeedProd' . $notification,
		apply_filters( 'seedprod_main_menu_capability', 'edit_others_posts' ),
		'seedprod_pro',
		'seedprod_pro_dashboard_page',
		'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTI1IiBoZWlnaHQ9IjEzMiIgdmlld0JveD0iMCAwIDEyNSAxMzIiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZmlsbC1ydWxlPSJldmVub2RkIiBjbGlwLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0wIDBDMCAwIDIuOTE2NjQgMC4xOTc4OTQgNjIuODIxMiA4LjAyNjgzQzEyMi43MjYgMTUuODU1OCAxNDMuNDU5IDc2LjYwNjQgMTA2Ljc4MSAxMjkuNjI4QzExMi40NTQgODIuMjUyNyAxMDIuMDcgMzMuMTA2MiA2MC4zNjA1IDI3LjM2MDZDMTguNjUwNSAyMS42MTUxIDIyLjI4MzQgMjIuNDk1NCAyMi4yODM0IDIyLjQ5NTRDMjIuMjgzNCAyMi40OTU0IDIyLjk3NDUgMzIuOTI5OSAyNi44ODgzIDYwLjk3OTlDMzAuODAyMSA4OS4wMjk5IDUyLjcwMzUgMTAyLjc4NiA3MS44NzA0IDEwOS44NjhDNzEuODcwNCAxMDkuODY4IDcyLjk5NDUgNzcuMDQwMSA2Mi4zMDA3IDYyLjU5MDlDNTEuNjA2OSA0OC4xNDE4IDM4LjMwMjYgMzguNTQ2IDM4LjMwMjYgMzguNTQ2QzM4LjMwMjYgMzguNTQ2IDY5LjU2OCA0Mi4yOTYgODEuMzcyMiA2NC4xMDE5QzkzLjE3NjQgODUuOTA3OCA5Mi4wMjY1IDEzMiA5Mi4wMjY1IDEzMkw3OS4yOTI1IDEzMS4zNDFDNDUuMDI4NCAxMjcuMjI1IDEzLjAxNzIgMTA2LjU5MSA3LjU3NDIzIDYzLjNDMi4xMzEzIDIwLjAwODggMCAwIDAgMFoiIGZpbGw9ImJsYWNrIi8+PHBhdGggZmlsbC1ydWxlPSJldmVub2RkIiBjbGlwLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0wIDBDMCAwIDIuOTE2NjQgMC4xOTc4OTQgNjIuODIxMiA4LjAyNjgzQzEyMi43MjYgMTUuODU1OCAxNDMuNDU5IDc2LjYwNjQgMTA2Ljc4MSAxMjkuNjI4QzExMi40NTQgODIuMjUyNyAxMDIuMDcgMzMuMTA2MiA2MC4zNjA1IDI3LjM2MDZDMTguNjUwNSAyMS42MTUxIDIyLjI4MzQgMjIuNDk1NCAyMi4yODM0IDIyLjQ5NTRDMjIuMjgzNCAyMi40OTU0IDIyLjk3NDUgMzIuOTI5OSAyNi44ODgzIDYwLjk3OTlDMzAuODAyMSA4OS4wMjk5IDUyLjcwMzUgMTAyLjc4NiA3MS44NzA0IDEwOS44NjhDNzEuODcwNCAxMDkuODY4IDcyLjk5NDUgNzcuMDQwMSA2Mi4zMDA3IDYyLjU5MDlDNTEuNjA2OSA0OC4xNDE4IDM4LjMwMjYgMzguNTQ2IDM4LjMwMjYgMzguNTQ2QzM4LjMwMjYgMzguNTQ2IDY5LjU2OCA0Mi4yOTYgODEuMzcyMiA2NC4xMDE5QzkzLjE3NjQgODUuOTA3OCA5Mi4wMjY1IDEzMiA5Mi4wMjY1IDEzMkw3OS4yOTI1IDEzMS4zNDFDNDUuMDI4NCAxMjcuMjI1IDEzLjAxNzIgMTA2LjU5MSA3LjU3NDIzIDYzLjNDMi4xMzEzIDIwLjAwODggMCAwIDAgMFoiIGZpbGw9IndoaXRlIi8+PC9zdmc+',
		apply_filters( 'seedprod_top_level_menu_postion', 58 )
	);

	add_submenu_page(
		'seedprod_pro',
		__( 'SeedProd', 'seedprod-pro' ),
		__( 'Landing Pages', 'seedprod-pro' ),
		apply_filters( 'seedprod_dashboard_menu_capability', 'edit_others_posts' ),
		'seedprod_pro',
		'seedprod_pro_dashboard_page'
	);

	add_submenu_page(
		'seedprod_pro',
		__( 'Theme Builder', 'seedprod-pro' ),
		__( 'Theme Builder', 'seedprod-pro' ),
		apply_filters( 'seedprod_theme_templates_menu_capability', 'edit_others_posts' ),
		'seedprod_pro_theme_templates',
		'seedprod_pro_theme_templates_page'
	);

	add_submenu_page(
		'seedprod_pro',
		__('Setup', 'seedprod-pro'),
		__('Setup', 'seedprod-pro'),
		apply_filters('seedprod_setup_menu_capability', 'edit_others_posts'),
		'seedprod_pro_setup',
		'seedprod_pro_setup_page'
	);

	//if ( 'lite' === SEEDPROD_PRO_BUILD ) {
		// add_submenu_page(
		// 	'seedprod_pro',
		// 	__( 'Templates', 'seedprod-pro' ),
		// 	__( 'Templates', 'seedprod-pro' ),
		// 	apply_filters( 'seedprod_templates_menu_capability', 'edit_others_posts' ),
		// 	'seedprod_pro_templates',
		// 	'seedprod_pro_templates_page'
		// );
	//}

	add_submenu_page(
		'seedprod_pro',
		__( 'Subscribers', 'seedprod-pro' ),
		__( 'Subscribers', 'seedprod-pro' ),
		apply_filters( 'seedprod_subscribers_menu_capability', 'edit_others_posts' ),
		'seedprod_pro_subscribers',
		'seedprod_pro_subscribers_page'
	);

    //if ('lite' === SEEDPROD_PRO_BUILD) {
        add_submenu_page(
            'seedprod_pro',
            __('Pop-ups', 'seedprod-pro'),
            __('Pop-ups', 'seedprod-pro'),
            apply_filters('seedprod_popup_menu_capability', 'edit_others_posts'),
            'seedprod_pro_popup',
            'seedprod_pro_popup_page'
        );
    //}

	add_submenu_page(
		'seedprod_pro',
		__( 'Settings', 'seedprod-pro' ),
		__( 'Settings', 'seedprod-pro' ),
		apply_filters( 'seedprod_settings_menu_capability', 'edit_others_posts' ),
		'seedprod_pro_settings',
		'seedprod_pro_settings_page'
	);

	add_submenu_page(
		'seedprod_pro',
		__( 'Growth Tools', 'seedprod-pro' ),
		__( 'Growth Tools', 'seedprod-pro' ),
		apply_filters( 'seedprod_growthtools_menu_capability', 'edit_others_posts' ),
		'seedprod_pro_growth_tools',
		'seedprod_pro_growth_tools_page'
	);

	add_submenu_page(
		'seedprod_pro',
		__( 'About Us', 'seedprod-pro' ),
		__( 'About Us', 'seedprod-pro' ),
		apply_filters( 'seedprod_aboutus_menu_capability', 'edit_others_posts' ),
		'seedprod_pro_about_us',
		'seedprod_pro_about_us_page'
	);

	add_submenu_page(
		'seedprod_pro',
		__( 'Request a Feature', 'seedprod-pro' ),
		'<span id="sp-feature-request">' . __( 'Request a Feature', 'seedprod-pro' ) . '</span>',
		apply_filters( 'seedprod_featurerequest_menu_capability', 'edit_others_posts' ),
		'seedprod_pro_featurerequest',
		'seedprod_pro_featurerequest_page'
	);

	if ( 'pro' === SEEDPROD_PRO_BUILD ) {
		add_submenu_page(
			'seedprod_pro',
			__( 'Import / Export', 'seedprod-pro' ),
			__( 'Import / Export', 'seedprod-pro' ),
			apply_filters( 'seedprod_theme_templates_menu_capability', 'edit_others_posts' ),
			'seedprod_pro_export_import_tools',
			'seedprod_pro_export_import_tools_page'
		);
	}

	if ( 'lite' === SEEDPROD_PRO_BUILD ) {
		add_submenu_page(
			'seedprod_pro',
			__( 'Upgrade to Pro', 'seedprod-pro' ),
			'<span id="sp-lite-admin-menu__upgrade">' . __( 'Upgrade to Pro', 'seedprod-pro' ) . '</span>',
			apply_filters( 'seedprod_gopro_menu_capability', 'edit_others_posts' ),
			'seedprod_pro_get_pro',
			'seedprod_pro_get_pro_page'
		);
		// add class
		add_action( 'admin_footer', 'seedprod_pro_upgrade_link_class' );
		function seedprod_pro_upgrade_link_class() {
			echo "<script>jQuery(function($) { $('#sp-lite-admin-menu__upgrade').parent().parent().addClass('sp-lite-admin-menu__upgrade_wrapper')});</script>";
		}
	}

	add_submenu_page(
		'seedprod_pro',
		__( 'Templates', 'seedprod-pro' ),
		__( 'Templates', 'seedprod-pro' ),
		apply_filters( 'seedprod_templates_menu_capability', 'edit_others_posts' ),
		'seedprod_pro_template',
		'seedprod_pro_template_page'
	);

	add_submenu_page(
		'seedprod_pro',
		__( 'Builder', 'seedprod-pro' ),
		__( 'Builder', 'seedprod-pro' ),
		apply_filters( 'seedprod_builder_menu_capability', 'edit_others_posts' ),
		'seedprod_pro_builder',
		'seedprod_pro_builder_page'
	);

	add_submenu_page(
		'seedprod_pro',
		__( 'Import/Export', 'seedprod-pro' ),
		__( 'Import/Export', 'seedprod-pro' ),
		apply_filters( 'seedprod_exportimport_menu_capability', 'edit_others_posts' ),
		'sp_pro_importexport',
		'seedprod_pro_importexport_page'
	);

	add_submenu_page(
		'seedprod_pro',
		__( 'Debug', 'seedprod-pro' ),
		__( 'Debug', 'seedprod-pro' ),
		apply_filters( 'seedprod_debug_menu_capability', 'edit_others_posts' ),
		'sp_pro_debug',
		'seedprod_pro_debug_page'
	);

	add_submenu_page( 
		'themes.php',
		__( 'Theme Builder', 'seedprod-pro' ),
		__( 'Theme Builder', 'seedprod-pro' ),
		apply_filters( 'seedprod_theme_templates_menu_capability', 'edit_others_posts' ),
		'seedprod_pro_theme_templates',
		'seedprod_pro_theme_templates_page'
	);
}

add_action( 'admin_head', 'seedprod_pro_remove_menus' );

/**
 * Remove menus for plugin.
 */
function seedprod_pro_remove_menus() {
	remove_submenu_page( 'seedprod_pro', 'seedprod_pro_builder' );
	remove_submenu_page( 'seedprod_pro', 'seedprod_pro_template' );
	remove_submenu_page( 'seedprod_pro', 'sp_pro_importexport' );
	remove_submenu_page( 'seedprod_pro', 'sp_pro_debug' );
	$dimiss_setup = get_option( 'seedprod_dismiss_setup' );
    if ( !empty( $dimiss_setup ) ) {
		remove_submenu_page( 'seedprod_pro', 'seedprod_pro_setup' );
    }
}

/**
 * Import/Export page.
 */
function seedprod_pro_importexport_page() {
	require_once SEEDPROD_PRO_PLUGIN_PATH . 'resources/views/importexport.php';
}

/**
 * Debug page.
 */
function seedprod_pro_debug_page() {
	require_once SEEDPROD_PRO_PLUGIN_PATH . 'resources/views/debug.php';
}

/**
 * Dashboard page.
 */
function seedprod_pro_dashboard_page() {
	require_once SEEDPROD_PRO_PLUGIN_PATH . 'resources/views/dashboard.php';
}

/**
 * Builder page.
 */
function seedprod_pro_builder_page() {
	require_once SEEDPROD_PRO_PLUGIN_PATH . 'resources/views/builder.php';
}

/**
 * Template page.
 */
function seedprod_pro_template_page() {
	require_once SEEDPROD_PRO_PLUGIN_PATH . 'resources/views/builder.php';
}

// update selected page
add_action( 'admin_footer', 'seedprod_pro_update_selected_page_in_submenu' );

/**
 * Update menu for single page app.
 */
function seedprod_pro_update_selected_page_in_submenu() {
	?>
	<script>
	jQuery(document).ready(function($){
		if(location.search.indexOf('seedprod_') >= 0){
			// Theme Builder
			if(location.hash.indexOf('#/theme-templates') >= 0){
				jQuery( "a[href^='admin.php?page=seedprod_<?php echo esc_attr( SEEDPROD_PRO_BUILD ); ?>']" ).parent().removeClass('current');
				jQuery( "a[href^='admin.php?page=seedprod_<?php echo esc_attr( SEEDPROD_PRO_BUILD ); ?>_theme_templates']" ).parent().addClass('current');
			}
			// Theme Chooser
			if(location.hash.indexOf('#/theme-chooser') >= 0){
				jQuery( "a[href^='admin.php?page=seedprod_<?php echo esc_attr( SEEDPROD_PRO_BUILD ); ?>']" ).parent().removeClass('current');
				jQuery( "a[href^='admin.php?page=seedprod_<?php echo esc_attr( SEEDPROD_PRO_BUILD ); ?>_theme_templates']" ).parent().addClass('current');
			}
			// Popups
			if(location.hash.indexOf('#/popups') >= 0){
				jQuery( "a[href^='admin.php?page=seedprod_<?php echo esc_attr( SEEDPROD_PRO_BUILD ); ?>']" ).parent().removeClass('current');
				jQuery( "a[href^='admin.php?page=seedprod_<?php echo esc_attr( SEEDPROD_PRO_BUILD ); ?>_popup']" ).parent().addClass('current');
			}
			// Templates
			if(location.hash.indexOf('#/template') >= 0){
				jQuery( "a[href^='admin.php?page=seedprod_<?php echo esc_attr( SEEDPROD_PRO_BUILD ); ?>']" ).parent().removeClass('current');
				jQuery( "a[href^='admin.php?page=seedprod_<?php echo esc_attr( SEEDPROD_PRO_BUILD ); ?>_templates']" ).parent().addClass('current');
			}

			// EXport Import Templates
			if(location.hash.indexOf('#/exportimport-templates') >= 0){
				jQuery( "a[href^='admin.php?page=seedprod_<?php echo esc_attr( SEEDPROD_PRO_BUILD ); ?>']" ).parent().removeClass('current');
				jQuery( "a[href^='admin.php?page=seedprod_<?php echo esc_attr( SEEDPROD_PRO_BUILD ); ?>_export_import_tools']" ).parent().addClass('current');
			}

			// Subscribers
			if(location.hash.indexOf('#/subscribers') >= 0){
				jQuery( "a[href^='admin.php?page=seedprod_<?php echo esc_attr( SEEDPROD_PRO_BUILD ); ?>']" ).parent().removeClass('current');
				jQuery( "a[href^='admin.php?page=seedprod_<?php echo esc_attr( SEEDPROD_PRO_BUILD ); ?>_subscribers']" ).parent().addClass('current');
			}
			// Settings
			if(location.hash.indexOf('#/settings') >= 0){
				jQuery( "a[href^='admin.php?page=seedprod_<?php echo esc_attr( SEEDPROD_PRO_BUILD ); ?>']" ).parent().removeClass('current');
				jQuery( "a[href^='admin.php?page=seedprod_<?php echo esc_attr( SEEDPROD_PRO_BUILD ); ?>_settings']" ).parent().addClass('current');
			}
			// Growth Tools
			if(location.hash.indexOf('#/growth-tools') >= 0){
				jQuery( "a[href^='admin.php?page=seedprod_<?php echo esc_attr( SEEDPROD_PRO_BUILD ); ?>']" ).parent().removeClass('current');
				jQuery( "a[href^='admin.php?page=seedprod_<?php echo esc_attr( SEEDPROD_PRO_BUILD ); ?>_growth_tools']" ).parent().addClass('current');
			}
			// About Us
			if(location.hash.indexOf('#/aboutus') >= 0){
				jQuery( "a[href^='admin.php?page=seedprod_<?php echo esc_attr( SEEDPROD_PRO_BUILD ); ?>']" ).parent().removeClass('current');
				jQuery( "a[href^='admin.php?page=seedprod_<?php echo esc_attr( SEEDPROD_PRO_BUILD ); ?>_about_us']" ).parent().addClass('current');
			}
			// Setup
			if(location.hash.indexOf('#/setup') >= 0){
				jQuery( "a[href^='admin.php?page=seedprod_<?php echo esc_attr( SEEDPROD_PRO_BUILD ); ?>']" ).parent().removeClass('current');
				jQuery( "a[href^='admin.php?page=seedprod_<?php echo esc_attr( SEEDPROD_PRO_BUILD ); ?>_setup']" ).parent().addClass('current');
			}
		}
	});
	</script>
	<?php
}



/* Short circuit new request */

add_action( 'admin_init', 'seedprod_pro_new_lpage', 1 );


/* Redirect to SPA */

add_action( 'admin_init', 'seedprod_pro_redirect_to_site', 1 );

/**
 * Redirects for single page app.
 */
function seedprod_pro_redirect_to_site() {
	// settings page
	if ( isset( $_GET['page'] ) && 'seedprod_pro_settings' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wp_safe_redirect( 'admin.php?page=seedprod_pro#/settings' );
		exit();
	}

	// subscribers
	if ( isset( $_GET['page'] ) && 'seedprod_pro_templates' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wp_safe_redirect( 'admin.php?page=seedprod_pro_template&id=0&from=sidebar#/template' );
		exit();
	}

	// subscribers
	if ( isset( $_GET['page'] ) && 'seedprod_pro_subscribers' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wp_safe_redirect( 'admin.php?page=seedprod_pro#/subscribers/0' );
		exit();
	}

	// theme templates
	if ( isset( $_GET['page'] ) && 'seedprod_pro_theme_templates' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wp_safe_redirect( 'admin.php?page=seedprod_pro#/theme-templates' );
		exit();
	}

	// export /  import  templates
	if ( isset( $_GET['page'] ) && 'seedprod_pro_export_import_tools' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wp_safe_redirect( 'admin.php?page=seedprod_pro#/exportimport-templates' );
		exit();
	}

	// growth tools page
	if ( isset( $_GET['page'] ) && 'seedprod_pro_growth_tools' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wp_safe_redirect( 'admin.php?page=seedprod_pro#/growth-tools' );
		exit();
	}

	// about us page
	if ( isset( $_GET['page'] ) && 'seedprod_pro_about_us' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wp_safe_redirect( 'admin.php?page=seedprod_pro#/aboutus' );
		exit();
	}

	//  setup page
	if ( isset( $_GET['page'] ) && 'seedprod_pro_setup' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if( !empty( $_GET[ 'sp_setup_dismiss' ] ) ){
			update_option( 'seedprod_dismiss_setup', 1 );
		}

		$dimiss_setup = get_option( 'seedprod_dismiss_setup' );

		if( !empty( $dimiss_setup ) ){
			wp_safe_redirect( 'admin.php?page=seedprod_pro#/' );
			exit();
		}else{
			wp_safe_redirect( 'admin.php?page=seedprod_pro#/setup' );
			exit();
		}

	}

	//  popups
	if ( isset( $_GET['page'] ) && 'seedprod_pro_popup' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if( is_plugin_active( 'optinmonster/optin-monster-wp-api.php' ) ) {
			wp_safe_redirect( 'admin.php?page=optin-monster-dashboard' );
		}else{
			wp_safe_redirect( 'admin.php?page=seedprod_pro&sp_om=1#/popups' );
		}
		exit();
	}

	// feature request page
	if ( isset( $_GET['page'] ) && 'seedprod_pro_featurerequest' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wp_redirect( 'https://www.seedprod.com/suggest-a-feature/?utm_source=wordpress&utm_medium=plugin-sidebar&utm_campaign=suggest-a-feature' ); // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
		exit();
	}

	// getpro page
	if ( isset( $_GET['page'] ) && 'seedprod_pro_get_pro' === $_GET['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wp_redirect( seedprod_pro_upgrade_link( 'wp-sidebar-menu' ) ); // phpcs:ignore WordPress.Security.SafeRedirect.wp_redirect_wp_redirect
		exit();
	}
}

/**
 * Preview Shortcode
 */
function seedprod_pro_render_shortcode() {
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}
		if ( ! empty( $_POST['shortcode'] ) ) {
			$shortcode = sanitize_text_field( wp_unslash( $_POST['shortcode'] ) );

			do_action( 'wp_print_footer_scripts' );
			do_action( 'wp_footer' );
			$content = do_shortcode( $shortcode );
			// $content = do_shortcode( $content );
			echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		exit();
	}
	exit;
}


/**
 * Preview Template tag
 */
function seedprod_pro_render_templatetag() {
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}

		$content = '';
		if ( ! empty( $_POST['templatetag'] ) ) {
			$templatetag = sanitize_text_field( wp_unslash( $_POST['templatetag'] ) );

			$args      = array(
				'posts_per_page' => 1,
				'post_type'      => 'post',
			);
			$the_query = new WP_Query( $args );

			// The Loop
			if ( $the_query->have_posts() ) {
				while ( $the_query->have_posts() ) {
					$the_query->the_post();
					$content = do_shortcode( $templatetag );
				}
			}

			/* Restore original Post Data */
			wp_reset_postdata();

			if ( ! empty( $content ) ) {
				do_action( 'wp_print_footer_scripts' );
				do_action( 'wp_footer' );
			}
		}

		echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		exit();
	}
	exit;
}

/**
 * Preview WC Template Tags.
 */
function seedprod_pro_render_wc_template_tags() {
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			return;
		}

		// Check if the WC Instance exists.
		if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			return;
		}

		$content = '';
		if ( ! empty( $_POST['wc_template_tag'] ) ) {
			$wc_template_tag = sanitize_text_field( wp_unslash( $_POST['wc_template_tag'] ) );

			$args = array(
				'posts_per_page' => 1,
				'post_type'      => 'product',
			);

			// Updating current query.
			$the_query = new WP_Query( $args );

			// The Loop
			if ( $the_query->have_posts() ) {
				while ( $the_query->have_posts() ) {
					$the_query->the_post();

					$id      = get_the_ID();
					$product = wc_get_product( $id );

					if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
						return '';
					}

					$content = do_shortcode( $wc_template_tag );
				}
			}

			/* Restore original Post Data */
			wp_reset_postdata();

			if ( ! empty( $content ) ) {
				do_action( 'wp_print_footer_scripts' );
				do_action( 'wp_footer' );
			}
		}

		echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		exit();
	}
	exit;
}


if ( defined( 'DOING_AJAX' ) ) {
	
	add_action( 'wp_ajax_seedprod_pro_render_shortcode_wc_cart', 'seedprod_pro_render_shortcode_wc_cart' );
	add_action( 'wp_ajax_seedprod_pro_render_shortcode_wc_custom_products_grid', 'seedprod_pro_render_shortcode_wc_custom_products_grid' );
	add_action( 'wp_ajax_seedprod_pro_render_shortcode_wc_checkout', 'seedprod_pro_render_shortcode_wc_checkout' );
	add_action( 'wp_ajax_seedprod_pro_render_shortcode', 'seedprod_pro_render_shortcode' );
	add_action( 'wp_ajax_seedprod_pro_render_templatetag', 'seedprod_pro_render_templatetag' );
	add_action( 'wp_ajax_seedprod_pro_render_wc_template_tags', 'seedprod_pro_render_wc_template_tags' );
	

	add_action( 'wp_ajax_seedprod_pro_dismiss_settings_lite_cta', 'seedprod_pro_dismiss_settings_lite_cta' );

	add_action( 'wp_ajax_seedprod_pro_save_settings', 'seedprod_pro_save_settings' );
	add_action( 'wp_ajax_seedprod_pro_save_api_key', 'seedprod_pro_save_api_key' );

	add_action( 'wp_ajax_seedprod_pro_save_app_settings', 'seedprod_pro_save_app_settings' );

	
	add_action( 'wp_ajax_seedprod_pro_deactivate_api_key', 'seedprod_pro_deactivate_api_key' );
	

	add_action( 'wp_ajax_seedprod_pro_template_subscribe', 'seedprod_pro_template_subscribe' );
	add_action( 'wp_ajax_seedprod_pro_save_template', 'seedprod_pro_save_template' );
	add_action( 'wp_ajax_seedprod_pro_save_lpage', 'seedprod_pro_save_lpage' );
	add_action( 'wp_ajax_seedprod_pro_get_revisions', 'seedprod_pro_get_revisisons' );
	add_action( 'wp_ajax_seedprod_pro_get_utc_offset', 'seedprod_pro_get_utc_offset' );
	add_action( 'wp_ajax_seedprod_pro_get_namespaced_custom_css', 'seedprod_pro_get_namespaced_custom_css' );
	add_action( 'wp_ajax_seedprod_pro_get_stockimages', 'seedprod_pro_get_stockimages' );
	
	add_action( 'wp_ajax_seedprod_pro_backgrounds_sideload', 'seedprod_pro_backgrounds_sideload' );
	add_action( 'wp_ajax_seedprod_pro_backgrounds_download', 'seedprod_pro_backgrounds_download' );
	

	// Landing pages
	add_action( 'wp_ajax_seedprod_pro_slug_exists', 'seedprod_pro_slug_exists' );
	add_action( 'wp_ajax_seedprod_pro_lpage_datatable', 'seedprod_pro_lpage_datatable' );
	add_action( 'wp_ajax_seedprod_pro_duplicate_lpage', 'seedprod_pro_duplicate_lpage' );
	add_action( 'wp_ajax_seedprod_pro_get_lpage_list', 'seedprod_pro_get_lpage_list' );
	add_action( 'wp_ajax_seedprod_pro_archive_selected_lpages', 'seedprod_pro_archive_selected_lpages' );
	add_action( 'wp_ajax_seedprod_pro_unarchive_selected_lpages', 'seedprod_pro_unarchive_selected_lpages' );
	add_action( 'wp_ajax_seedprod_pro_delete_archived_lpages', 'seedprod_pro_delete_archived_lpages' );

	// Theme templates
	
	add_action( 'wp_ajax_seedprod_pro_themetemplate_datatable', 'seedprod_pro_themetemplate_datatable' );
	add_action( 'wp_ajax_seedprod_pro_duplicate_themetemplate', 'seedprod_pro_duplicate_themetemplate' );
	add_action( 'wp_ajax_seedprod_pro_archive_selected_themetemplates', 'seedprod_pro_archive_selected_themetemplates' );
	add_action( 'wp_ajax_seedprod_pro_unarchive_selected_themetemplates', 'seedprod_pro_unarchive_selected_themetemplates' );
	add_action( 'wp_ajax_seedprod_pro_delete_archived_themetemplates', 'seedprod_pro_delete_archived_themetemplates' );
	add_action( 'wp_ajax_seedprod_pro_temp_save_theme_template', 'seedprod_pro_temp_save_theme_template' );
	add_action( 'wp_ajax_seedprod_pro_update_theme_template_conditions', 'seedprod_pro_update_theme_template_conditions' );
	add_action( 'wp_ajax_seedprod_pro_update_theme_template_post_status', 'seedprod_pro_update_theme_template_post_status' );
	add_action( 'wp_ajax_seedprod_pro_update_theme_template_preview_mode', 'seedprod_pro_update_theme_template_preview_mode' );
	add_action( 'wp_ajax_seedprod_pro_update_seedprod_theme_enabled', 'seedprod_pro_update_seedprod_theme_enabled' );

	add_action( 'wp_ajax_seedprod_pro_import_theme_request', 'seedprod_pro_import_theme_request' );
	add_action( 'wp_ajax_seedprod_pro_create_blog_and_home_for_theme', 'seedprod_pro_create_blog_and_home_for_theme' );

	

	add_action( 'wp_ajax_seedprod_pro_update_subscriber_count', 'seedprod_pro_update_subscriber_count' );
	add_action( 'wp_ajax_seedprod_pro_subscribers_datatable', 'seedprod_pro_subscribers_datatable' );
	
	add_action( 'wp_ajax_seedprod_pro_delete_subscribers', 'seedprod_pro_delete_subscribers' );
	

	add_action( 'wp_ajax_seedprod_pro_install_addon_setup', 'seedprod_pro_install_addon_setup' );
	add_action( 'wp_ajax_seedprod_pro_complete_setup_wizard', 'seedprod_pro_complete_setup_wizard' );

	add_action( 'wp_ajax_seedprod_pro_get_plugins_list', 'seedprod_pro_get_plugins_list' );

	add_action( 'wp_ajax_seedprod_pro_install_addon', 'seedprod_pro_install_addon' );
	add_action( 'wp_ajax_seedprod_pro_activate_addon', 'seedprod_pro_activate_addon' );
	add_action( 'wp_ajax_seedprod_pro_deactivate_addon', 'seedprod_pro_deactivate_addon' );

	add_action( 'wp_ajax_seedprod_pro_install_addon', 'seedprod_pro_install_addon' );
	add_action( 'wp_ajax_seedprod_pro_deactivate_addon', 'seedprod_pro_deactivate_addon' );
	add_action( 'wp_ajax_seedprod_pro_activate_addon', 'seedprod_pro_activate_addon' );
	add_action( 'wp_ajax_seedprod_pro_plugin_nonce', 'seedprod_pro_plugin_nonce' );

	add_action( 'wp_ajax_nopriv_seedprod_pro_run_one_click_upgrade', 'seedprod_pro_run_one_click_upgrade' );
	add_action( 'wp_ajax_seedprod_pro_upgrade_license', 'seedprod_pro_upgrade_license' );

	add_action( 'wp_ajax_seedprod_pro_get_wpforms', 'seedprod_pro_get_wpforms' );
	add_action( 'wp_ajax_seedprod_pro_get_wpform', 'seedprod_pro_get_wpform' );
	add_action( 'wp_ajax_seedprod_pro_get_rafflepress', 'seedprod_pro_get_rafflepress' );
	add_action( 'wp_ajax_seedprod_pro_get_rafflepress_code', 'seedprod_pro_get_rafflepress_code' );

	add_action( 'wp_ajax_seedprod_pro_get_widget_wpforms', 'seedprod_pro_get_widget_wpforms' );
	add_action( 'wp_ajax_seedprod_pro_get_widget_wpresults', 'seedprod_pro_get_widget_wpresults' );


	add_action( 'wp_ajax_seedprod_pro_dismiss_upsell', 'seedprod_pro_dismiss_upsell' );

	// WooCommerce.
	add_action( 'wp_ajax_seedprod_pro_get_woocommerce_products', 'seedprod_pro_get_woocommerce_products' );
	add_action( 'wp_ajax_seedprod_pro_get_woocommerce_product_taxonomy', 'seedprod_pro_get_woocommerce_product_taxonomy' );
	add_action( 'wp_ajax_seedprod_pro_get_woocommerce_product_attributes', 'seedprod_pro_get_woocommerce_product_attributes' );
	add_action( 'wp_ajax_seedprod_pro_get_woocommerce_product_attribute_terms', 'seedprod_pro_get_woocommerce_product_attribute_terms' );

	
	// Subscribe Callback
	add_action( 'wp_ajax_seedprod_pro_subscribe_callback', 'seedprod_pro_subscribe_callback' );
	add_action( 'wp_ajax_nopriv_seedprod_pro_subscribe_callback', 'seedprod_pro_subscribe_callback' );
	

	
	add_action( 'wp_ajax_seedprod_pro_render_gallery_shortcode', 'seedprod_pro_render_gallery_shortcode' );
	add_action( 'wp_ajax_seedprod_pro_render_basic_gallery_shortcode', 'seedprod_pro_render_basic_gallery_shortcode' );
	

	
	add_action( 'wp_ajax_seedprod_pro_render_business_review_shortcode', 'seedprod_pro_render_business_review_shortcode' );
	

	
	add_action( 'wp_ajax_seedprod_pro_get_domain_mapping_domain', 'seedprod_pro_get_domain_mapping_domain' );
	


}







/*
 * Force License Recheck
 */
add_action( 'init', 'seedprod_pro_force_license_recheck' );

add_action( 'init', 'seedprod_pro_deactivate_license' );





/**
 * Return Widget Previews
 */
function seedprod_pro_get_widget_wpforms() {
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}
		$block_type  = filter_input( INPUT_GET, 'block_type' );
		$base_id     = filter_input( INPUT_GET, 'base_id' );
		$widget_name = str_replace( 'wpwidgetblock-', '', $block_type );

		global $wp_widget_factory;
		$inst = $wp_widget_factory->widgets[ $widget_name ];

		$component_json = file_get_contents( 'php://input' );
		$component      = json_decode( $component_json, true );
		$options        = $component['options'];

		$instance = array();
		if ( is_array( $options ) ) {
			$wp_options = array();
			foreach ( $options as $t => $value ) {
				$wp_options[ $t ] = sanitize_text_field( $value );
			}
			$instance = $wp_options;
		}

		echo '<div class="widget-inside media-widget-control"><div class="form wp-core-ui">';

		echo '<input type="hidden" class="id_base" value="' . esc_attr( $base_id ) . '">';
		$random_data = wp_rand( 1000, 9999 );
		echo '<input type="hidden" class="widget-id" value="widget-c' . esc_attr( $random_data ) . '" />';

		echo '<div class="widget-content">';

		$updated_instance = $inst->update( $instance, array() );
		$ins_form         = $inst->form( $updated_instance );
		echo $ins_form; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		echo '</div></div></div>';

		die( '' );

	}
}

/**
 * Return Widget
 */
function seedprod_pro_get_widget_wpresults() {

	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}
		// print_r($_REQUEST);
		$block_type = filter_input( INPUT_GET, 'block_type' );
		// print_r($block_type);

		$widget_name = str_replace( 'wpwidgetblock-', '', $block_type );

		global $wp_widget_factory;
		$inst = $wp_widget_factory->widgets[ $widget_name ];

		$component_json = file_get_contents( 'php://input' );
		$component      = json_decode( $component_json, true );
		$options        = $component['options'];

		$instance = array();
		if ( is_array( $options ) ) {
			$wp_options = array();
			foreach ( $options as $t => $value ) {
				if ( 'WP_Widget_Custom_HTML' === $widget_name && 'content' === $t ) {
					$wp_options[ $t ] = $value;
				} else {
					$wp_options[ $t ] = sanitize_text_field( $value );
				}
			}
			$instance = $wp_options;
		}
		$updated_instance = $inst->update( $instance, array() );
		the_widget( $widget_name, $instance );

		die( '' );
		// return $widget_name;

	}

}

// login redirect
add_action( 'login_head', 'seedprod_pro_redirect_login_page' );



// Make RafflePress Discoverable
if ( 'pro' === SEEDPROD_PRO_BUILD ) {
	add_filter( 'install_plugins_table_api_args_featured', 'seedprod_pro_featured_plugins_tab' );
}
/**
 * Helper function for adding plugins to featured list
 *
 * @return array
 */
function seedprod_pro_featured_plugins_tab( $args ) {
	add_filter( 'plugins_api_result', 'seedprod_pro_plugins_api_result', 10, 3 );

	return $args;
} // featured_plugins_tab


/**
 * Add plugins to featured plugins list
 *
 * @return object
 */
function seedprod_pro_plugins_api_result( $res, $action, $args ) {
	remove_filter( 'plugins_api_result', 'seedprod_pro_plugins_api_result', 10, 3 );

	$res = seedprod_pro_add_plugin_featured( 'rafflepress', $res );

	return $res;
} // plugins_api_result

/**
 * Add single plugin to featured list
 *
 * @return object
 */
function seedprod_pro_add_plugin_featured( $plugin_slug, $res ) {
	// check if plugin is already on the list
	if ( ! empty( $res->plugins ) && is_array( $res->plugins ) ) {
		foreach ( $res->plugins as $plugin ) {
			if ( is_object( $plugin ) && ! empty( $plugin->slug ) && $plugin->slug == $plugin_slug ) {
				return $res;
			}
		} // foreach
	}

if ($plugin_info = get_transient('seedprod-plugin-info-' . $plugin_slug)) {
	array_splice($res->plugins,4,0,array($plugin_info));
	//array_unshift($res->plugins, $plugin_info);
} else {
	$plugin_info = plugins_api('plugin_information', array(
	'slug'   => $plugin_slug,
	'is_ssl' => is_ssl(),
	'fields' => array(
		'banners'           => true,
		'reviews'           => true,
		'downloaded'        => true,
		'active_installs'   => true,
		'icons'             => true,
		'short_description' => true,
	)
	));
	if (!is_wp_error($plugin_info)) {
	$res->plugins = array_merge(array($plugin_info), $res->plugins);
	set_transient('seedprod-plugin-info-' . $plugin_slug, $plugin_info, DAY_IN_SECONDS * 7);
	}
}

return $res;
} // add_plugin_featured



add_filter( 'admin_body_class', 'seedprod_pro_admin_body_class' );

/**
 * Adds one or more classes to the body tag in the dashboard.
 *
 * @link https://wordpress.stackexchange.com/a/154951/17187
 * @param  String $classes Current body classes.
 * @return String          Altered body classes.
 */
function seedprod_pro_admin_body_class( $classes ) {
	if( !empty( $_GET['sp_om'] ) &&  $_GET['sp_om'] == 1){
		return "$classes sp_om";
	}
	return $classes;
}
