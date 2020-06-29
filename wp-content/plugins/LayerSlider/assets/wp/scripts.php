<?php

// Prevent direct file access
defined( 'LS_ROOT_FILE' ) || exit;

$lsPriority = (int) get_option('ls_scripts_priority', 3);
$lsPriority = ! empty($lsPriority) ? $lsPriority : 3;


if( get_option('ls_gutenberg_block', true ) ) {
	add_action('enqueue_block_editor_assets', 'ls_enqueue_slider_library');
	add_action('init', 'layerslider_register_gutenberg_block');
}

add_action('wp_enqueue_scripts', 'layerslider_enqueue_content_res', $lsPriority);
add_action('wp_footer', 'layerslider_footer_scripts', ($lsPriority+1));

add_action('admin_enqueue_scripts', 'layerslider_enqueue_admin_res', $lsPriority);
add_action('admin_enqueue_scripts', 'ls_load_google_fonts', $lsPriority);
add_action('wp_enqueue_scripts', 'ls_load_google_fonts', ($lsPriority+1));
add_action('wp_head', 'ls_meta_generator', 9);

// Fix for CloudFlare's Rocket Loader
add_filter('script_loader_tag', 'layerslider_script_attributes', 10, 3);
function layerslider_script_attributes( $tag, $handle, $src ) {


	if(
		$handle === 'layerslider' ||
		$handle === 'layerslider-utils' ||
		$handle === 'layerslider-transitions' ||
		$handle === 'layerslider-origami' ||
		$handle === 'layerslider-popup' ||
		$handle === 'ls-user-transitions'
	) {

		if( get_option('ls_rocketscript_ignore', false ) ) {
			$tag =  str_replace( "type='text/javascript' src=", 'data-cfasync="false" src=', $tag );
		}

		if( get_option('ls_defer_scripts', false ) ) {
			$tag = str_replace( '></script>', ' defer></script>', $tag);
		}
	}


	return $tag;
}


function ls_enqueue_slider_library() {

	// Dependencies: LS Utils & Kreatura Modal Window
	wp_enqueue_script('layerslider-utils', LS_ROOT_URL.'/static/layerslider/js/layerslider.utils.js', array('jquery'), LS_PLUGIN_VERSION );
	wp_enqueue_style('kreatura-modal-window', LS_ROOT_URL.'/static/admin/css/kmw.css', false, LS_PLUGIN_VERSION );
	wp_enqueue_script('kreatura-modal-window', LS_ROOT_URL.'/static/admin/js/kmw.js', array('jquery'), LS_PLUGIN_VERSION );

	// Slider Library files
	wp_enqueue_style('layerslider-slider-library', LS_ROOT_URL.'/static/admin/css/slider-library.css', false, LS_PLUGIN_VERSION );
	wp_enqueue_script('layerslider-slider-library', LS_ROOT_URL.'/static/admin/js/slider-library.js', array('jquery'), LS_PLUGIN_VERSION );

	// Slider Library Localization
	include LS_ROOT_PATH.'/wp/slider_library_l10n.php';
	wp_localize_script('layerslider-slider-library', 'LS_SLibrary_l10n', $l10n_ls_slider_library);
}


function layerslider_register_gutenberg_block() {

	if( function_exists('register_block_type') ) {

		include LS_ROOT_PATH.'/wp/gutenberg_l10n.php';
		wp_register_script('layerslider-gutenberg', LS_ROOT_URL.'/static/admin/js/gutenberg.js', array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-editor' ), LS_PLUGIN_VERSION );
		wp_localize_script('layerslider-gutenberg', 'LS_GB_l10n', $l10n_ls_gutenberg);

		wp_register_style('layerslider-gutenberg', LS_ROOT_URL.'/static/admin/css/gutenberg.css', false, LS_PLUGIN_VERSION );


		register_block_type('kreatura/layerslider', array(
			'editor_style' => array(
				'layerslider-gutenberg',
				'kreatura-modal-window'
			),
			'editor_script' => array(
				'layerslider-gutenberg',
				'layerslider-utils',
				'kreatura-modal-window'
			),
			'render_callback' => 'layerslider_render_gutenberg_block'
		));
	}
}


function layerslider_render_gutenberg_block( $attributes )  {

	if( ! empty( $attributes['id'] ) ) {
		return LS_Shortcode::handleShortcode( $attributes );
	}
}


function layerslider_enqueue_content_res() {

	// Include in the footer?
	$condsc = get_option( 'ls_conditional_script_loading', false );
	$condsc = apply_filters( 'ls_conditional_script_loading', $condsc );

	$always = get_option( 'ls_load_all_js_files', false );
	$always = apply_filters( 'ls_load_all_js_files', $always );

	$footer = get_option( 'ls_include_at_footer', false );
	$footer = apply_filters( 'ls_include_at_footer', $footer );

	$footer = $condsc ? true : $footer;

	// Use Gogole CDN version of jQuery
	if(get_option('ls_use_custom_jquery', false)) {
		wp_deregister_script('jquery');
		wp_enqueue_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js', array(), '1.8.3');
	}

	// Enqueue admin front-end assets
	if( current_user_can(get_option('layerslider_custom_capability', 'manage_options')) ) {
		wp_enqueue_style('layerslider-front', LS_ROOT_URL.'/static/public/front.css', false, LS_PLUGIN_VERSION );
	}

	// Register LayerSlider resources
	wp_register_script('layerslider-utils', LS_ROOT_URL.'/static/layerslider/js/layerslider.utils.js', array('jquery'), LS_PLUGIN_VERSION, $footer );
	wp_register_script('layerslider', LS_ROOT_URL.'/static/layerslider/js/layerslider.kreaturamedia.jquery.js', array('jquery'), LS_PLUGIN_VERSION, $footer );
	wp_register_script('layerslider-transitions', LS_ROOT_URL.'/static/layerslider/js/layerslider.transitions.js', false, LS_PLUGIN_VERSION, $footer );
	wp_enqueue_style('layerslider', LS_ROOT_URL.'/static/layerslider/css/layerslider.css', false, LS_PLUGIN_VERSION );

	// LayerSlider Origami plugin
	wp_register_script('layerslider-origami', LS_ROOT_URL.'/static/layerslider/plugins/origami/layerslider.origami.js', array('jquery'), LS_PLUGIN_VERSION, $footer );
	wp_register_style('layerslider-origami', LS_ROOT_URL.'/static/layerslider/plugins/origami/layerslider.origami.css', false, LS_PLUGIN_VERSION );

	// LayerSlider Popup plugin
	wp_register_script('layerslider-popup', LS_ROOT_URL.'/static/layerslider/plugins/popup/layerslider.popup.js', array('jquery'), LS_PLUGIN_VERSION, $footer );
	wp_register_style('layerslider-popup', LS_ROOT_URL.'/static/layerslider/plugins/popup/layerslider.popup.css', false, LS_PLUGIN_VERSION );

	// 3rd-party: Font Awesome
	wp_register_style('layerslider-font-awesome', LS_ROOT_URL.'/static/font-awesome/css/font-awesome.min.css', false, LS_PLUGIN_VERSION );

	// Build LS_Meta object
	$LS_Meta = array();

	if( ! get_option('ls_suppress_debug_info', false ) ) {
		$LS_Meta['v'] = LS_PLUGIN_VERSION;
	}

	if( get_option('ls_gsap_sandboxing', true ) ) {
		$LS_Meta['fixGSAP'] = true;
	}

	// Print LS_Meta object
	if( ! empty( $LS_Meta ) ) {
		wp_localize_script('layerslider-utils', 'LS_Meta', $LS_Meta);
	}

	// User resources
	$uploads = wp_upload_dir();
	$uploads['baseurl'] = set_url_scheme( $uploads['baseurl'] );

	if(file_exists($uploads['basedir'].'/layerslider.custom.transitions.js')) {
		wp_register_script('ls-user-transitions', $uploads['baseurl'].'/layerslider.custom.transitions.js', false, LS_PLUGIN_VERSION, $footer );
	}

	if(file_exists($uploads['basedir'].'/layerslider.custom.css')) {
		wp_enqueue_style('ls-user', $uploads['baseurl'].'/layerslider.custom.css', false, LS_PLUGIN_VERSION );
	}

	if( ! $footer || $always ) {
		wp_enqueue_script('layerslider-utils');
		wp_enqueue_script('layerslider');
		wp_enqueue_script('layerslider-transitions');
		wp_enqueue_script('ls-user-transitions');
	}

	// If the "Always load all JS files" option is enabled
	// load all LayerSlider plugin files as well.
	if( $always ) {
		wp_enqueue_style( 'layerslider-origami' );
		wp_enqueue_script( 'layerslider-origami' );

		wp_enqueue_style( 'layerslider-popup' );
		wp_enqueue_script( 'layerslider-popup' );
	}
}



function layerslider_footer_scripts() {

	$condsc = get_option( 'ls_conditional_script_loading', false );
	$condsc = apply_filters( 'ls_conditional_script_loading', $condsc );

	$always = get_option( 'ls_load_all_js_files', false );
	$always = apply_filters( 'ls_load_all_js_files', $always );

	if( ! $condsc || ! empty( $GLOBALS['lsSliderInit'] ) || $always ) {

		// Enqueue scripts
		wp_enqueue_script('layerslider-utils');
		wp_enqueue_script('layerslider');
		wp_enqueue_script('layerslider-transitions');

		if( wp_script_is('ls-user-transitions', 'registered') ) {
			wp_enqueue_script('ls-user-transitions');
		}
	}

	// Conditionally load LayerSlider plugins
	if( ! empty( $GLOBALS['lsLoadPlugins'] ) ) {

		// Filter out duplicates
		$GLOBALS['lsLoadPlugins'] = array_unique($GLOBALS['lsLoadPlugins']);

		// Load plugins
		foreach( $GLOBALS['lsLoadPlugins'] as $item ) {
			wp_enqueue_script('layerslider-'.$item);
			wp_enqueue_style('layerslider-'.$item);
		}
	}

	// If the "Always load all JS files" option is enabled
	// load all LayerSlider plugin files as well.
	if( $always ) {
		wp_enqueue_style( 'layerslider-origami' );
		wp_enqueue_script( 'layerslider-origami' );

		wp_enqueue_style( 'layerslider-popup' );
		wp_enqueue_script( 'layerslider-popup' );
	}


	// Always load Font Awesome in Elementor Preview:
	// Elementor loads modules individually and we can't
	// gather information about the fonts being used in
	// embedded sliders.
	if( ! empty( $_GET['elementor-preview'] ) ) {
		$GLOBALS['lsLoadFonts'] = array('font-awesome');
	}


	// Load used fonts
	if( ! empty( $GLOBALS['lsLoadFonts'] ) ) {

		// Filter out duplicates
		$GLOBALS['lsLoadFonts'] = array_unique($GLOBALS['lsLoadFonts']);

		// Load fonts
		foreach( $GLOBALS['lsLoadFonts'] as $item ) {
			wp_enqueue_style('layerslider-'.$item);
		}
	}

	if( ! empty( $GLOBALS['lsSliderInit'] ) ) {
		wp_add_inline_script( 'layerslider', implode('', $GLOBALS['lsSliderInit']) );
	}

}



function layerslider_enqueue_admin_res() {

	// Load global LayerSlider CSS
	wp_enqueue_style('layerslider-global', LS_ROOT_URL.'/static/admin/css/global.css', false, LS_PLUGIN_VERSION );

	// Load global LayerSlider JS
	include LS_ROOT_PATH.'/wp/tinymce_l10n.php';
	wp_enqueue_script('layerslider-global', LS_ROOT_URL.'/static/admin/js/ls-admin-global.js', false, LS_PLUGIN_VERSION );
	wp_localize_script('layerslider-global', 'LS_MCE_l10n', $l10n_ls_mce);


	// Embed CSS. Hides the admin menu bar and the sidebar.
	if( ! empty( $_GET['ls-embed'] ) ) {
		wp_enqueue_style('layerslider-embed', LS_ROOT_URL.'/static/admin/css/embed.css', false, LS_PLUGIN_VERSION);
	}

	// Use Google CDN version of jQuery
	if( get_option( 'ls_use_custom_jquery', false ) ) {
		wp_deregister_script('jquery');
		wp_enqueue_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js', array(), '1.8.3');
	}

	// Load LayerSlider-only resources
	$screen = get_current_screen();

	if( strpos( $screen->base, 'layerslider' ) !== false ) {

		// New Media Library
		if( function_exists( 'wp_enqueue_media' ) ) {
			wp_enqueue_media();
		}

		// Load some bundled WP resources
		wp_enqueue_script('wp-pointer');
		wp_enqueue_style('wp-pointer');

		wp_enqueue_script('jquery-ui-droppable');

		// Dashicons
		if( version_compare( get_bloginfo('version'), '3.8', '<') ) {
			wp_enqueue_style('dashicons', LS_ROOT_URL.'/static/dashicons/dashicons.css', false, LS_PLUGIN_VERSION );
		}

		// Global scripts & stylesheets
		wp_enqueue_script('layerslider-utils', LS_ROOT_URL.'/static/layerslider/js/layerslider.utils.js', array('jquery'), LS_PLUGIN_VERSION );
		wp_enqueue_script('kreaturamedia-ui', LS_ROOT_URL.'/static/admin/js/km-ui.js', array('jquery'), LS_PLUGIN_VERSION );
		wp_enqueue_script('kreatura-modal-window', LS_ROOT_URL.'/static/admin/js/kmw.js', array('jquery'), LS_PLUGIN_VERSION );
		wp_enqueue_script('ls-admin-global', LS_ROOT_URL.'/static/admin/js/ls-admin-common.js', array('jquery'), LS_PLUGIN_VERSION );

		wp_enqueue_style('kreatura-modal-window', LS_ROOT_URL.'/static/admin/css/kmw.css', false, LS_PLUGIN_VERSION );
		wp_enqueue_style('layerslider-admin', LS_ROOT_URL.'/static/admin/css/admin.css', false, LS_PLUGIN_VERSION );
		wp_enqueue_style('layerslider-admin-new', LS_ROOT_URL.'/static/admin/css/admin_new.css', false, LS_PLUGIN_VERSION );
		wp_enqueue_style('kreaturamedia-ui', LS_ROOT_URL.'/static/admin/css/km-ui.css', false, LS_PLUGIN_VERSION );

		// Check if Google Fonts is enabled as per the new privacy
		// settings introduced in version 6.7.6
		if( get_option('layerslider-google-fonts-enabled', true ) ) {
			wp_enqueue_style('ls-admin-google-fonts', LS_ROOT_URL.'/static/admin/css/google-fonts.css', false, LS_PLUGIN_VERSION );
		}

		// 3rd-party: Font Awesome
		wp_enqueue_style('layerslider-font-awesome', LS_ROOT_URL.'/static/font-awesome/css/font-awesome.min.css', false, LS_PLUGIN_VERSION );

		// 3rd-party: CodeMirror
		wp_enqueue_style('codemirror', LS_ROOT_URL.'/static/codemirror/lib/codemirror.css', false, LS_PLUGIN_VERSION );
		wp_enqueue_script('codemirror', LS_ROOT_URL.'/static/codemirror/lib/codemirror.js', array('jquery'), LS_PLUGIN_VERSION );
		wp_enqueue_style('codemirror-solarized', LS_ROOT_URL.'/static/codemirror/theme/solarized.mod.css', false, LS_PLUGIN_VERSION );
		wp_enqueue_script('codemirror-syntax-css', LS_ROOT_URL.'/static/codemirror/mode/css/css.js', array('jquery'), LS_PLUGIN_VERSION );
		wp_enqueue_script('codemirror-syntax-javascript', LS_ROOT_URL.'/static/codemirror/mode/javascript/javascript.js', array('jquery'), LS_PLUGIN_VERSION );
		wp_enqueue_script('codemirror-foldcode', LS_ROOT_URL.'/static/codemirror/addon/fold/foldcode.js', array('jquery'), LS_PLUGIN_VERSION );
		wp_enqueue_script('codemirror-foldgutter', LS_ROOT_URL.'/static/codemirror/addon/fold/foldgutter.js', array('jquery'), LS_PLUGIN_VERSION );
		wp_enqueue_script('codemirror-brace-fold', LS_ROOT_URL.'/static/codemirror/addon/fold/brace-fold.js', array('jquery'), LS_PLUGIN_VERSION );
		wp_enqueue_script('codemirror-active-line', LS_ROOT_URL.'/static/codemirror/addon/selection/active-line.js', array('jquery'), LS_PLUGIN_VERSION );

		// Localize admin scripts
		include LS_ROOT_PATH.'/wp/scripts_l10n.php';
		wp_localize_script('ls-admin-global', 'LS_l10n', $l10n_ls);


		// Settings Page
		if( strpos( $screen->base, 'layerslider-options' ) !== false ) {

			// Avoid PHP undef notice
			$section = ! empty( $_GET['section'] ) ? $_GET['section'] : false;

			switch( $section ) {

				case 'about':
					wp_enqueue_style('ls-about-page', LS_ROOT_URL.'/static/admin/css/about.css', false, LS_PLUGIN_VERSION );
					wp_enqueue_script('ls-about-page', LS_ROOT_URL.'/static/admin/js/about.js', array('jquery'), LS_PLUGIN_VERSION );
					break;

				case 'skin-editor':
				case 'css-editor':
					wp_enqueue_style('ls-skin-editor', LS_ROOT_URL.'/static/admin/css/skin.editor.css', false, LS_PLUGIN_VERSION );
					break;


				case 'transition-builder':
					ls_require_builder_assets();
					wp_enqueue_script('layerslider_tr_builder', LS_ROOT_URL.'/static/admin/js/ls-admin-transition-builder.js', array('jquery'), LS_PLUGIN_VERSION );
					break;

				default:
					wp_enqueue_script('layerslider-settings', LS_ROOT_URL.'/static/admin/js/ls-admin-settings.js', array('jquery'), LS_PLUGIN_VERSION );
					wp_enqueue_style('layerslider-settings', LS_ROOT_URL.'/static/admin/css/plugin_settings.css', false, LS_PLUGIN_VERSION );
					break;
			}



		// Add-Ons Page
		} elseif( strpos( $screen->base, 'layerslider-addons' ) !== false ) {
			wp_enqueue_script('layerslider-addons', LS_ROOT_URL.'/static/admin/js/ls-admin-addons.js', array('jquery'), LS_PLUGIN_VERSION );
			wp_enqueue_style('layerslider-addons', LS_ROOT_URL.'/static/admin/css/addons.css', false, LS_PLUGIN_VERSION );

			wp_enqueue_style('ls-revisions', LS_ROOT_URL.'/static/admin/css/revisions.css', false, LS_PLUGIN_VERSION );
			wp_enqueue_script('ls-revisions', LS_ROOT_URL.'/static/admin/js/ls-admin-revisions.js', array('jquery'), LS_PLUGIN_VERSION );

			if( ! empty( $_GET['section'] ) && $_GET['section'] === 'revisions' ) {
				ls_require_builder_assets();
			}

		// Sliders list page
		} elseif( empty( $_GET['action'] ) ) {

			wp_enqueue_script('ls-admin-sliders', LS_ROOT_URL.'/static/admin/js/ls-admin-sliders.js', array('jquery'), LS_PLUGIN_VERSION );
			wp_enqueue_script('ls-shuffle', LS_ROOT_URL.'/static/shuffle/shuffle.min.js', array('jquery'), LS_PLUGIN_VERSION );

			wp_enqueue_style('ls-font-awesome-latest', LS_ROOT_URL.'/static/font-awesome-latest/css/all.min.css', false, LS_PLUGIN_VERSION );

			wp_enqueue_script('layerslider', LS_ROOT_URL.'/static/layerslider/js/layerslider.kreaturamedia.jquery.js', array('jquery'), LS_PLUGIN_VERSION );
			wp_enqueue_style('layerslider', LS_ROOT_URL.'/static/layerslider/css/layerslider.css', false, LS_PLUGIN_VERSION );

		// Slider & Transition Builder
		} else {
			ls_require_builder_assets();
		}
	}
}


function ls_require_builder_assets() {

	// Load some bundled WP resources
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script('jquery-ui-selectable');
	wp_enqueue_script('jquery-ui-draggable');
	wp_enqueue_script('jquery-ui-resizable');
	wp_enqueue_script('jquery-ui-slider');

	wp_register_script('layerslider-admin', LS_ROOT_URL.'/static/admin/js/ls-admin-slider-builder.js', array('jquery', 'json2'), LS_PLUGIN_VERSION );

	//  Don't load automatically the Slider Builder JS file other than the Slider Builder itself.
	if( empty( $_GET['section'] ) ) {
		wp_enqueue_script('layerslider-admin');
	}

	// LayerSlider includes for preview
	wp_enqueue_script('layerslider', LS_ROOT_URL.'/static/layerslider/js/layerslider.kreaturamedia.jquery.js', array('jquery'), LS_PLUGIN_VERSION );
	wp_enqueue_script('layerslider-transitions', LS_ROOT_URL.'/static/layerslider/js/layerslider.transitions.js', false, LS_PLUGIN_VERSION );
	wp_enqueue_script('layerslider-tr-gallery', LS_ROOT_URL.'/static/admin/js/layerslider.transition.gallery.js', array('jquery'), LS_PLUGIN_VERSION );
	wp_enqueue_style('layerslider', LS_ROOT_URL.'/static/layerslider/css/layerslider.css', false, LS_PLUGIN_VERSION );
	wp_enqueue_style('layerslider-tr-gallery', LS_ROOT_URL.'/static/admin/css/layerslider.transitiongallery.css', false, LS_PLUGIN_VERSION );

	// LayerSlider Timeline plugin
	wp_enqueue_script('layerslider-timeline', LS_ROOT_URL.'/static/layerslider/plugins/timeline/layerslider.timeline.js', array('jquery'), LS_PLUGIN_VERSION );
	wp_enqueue_style('layerslider-timeline', LS_ROOT_URL.'/static/layerslider/plugins/timeline/layerslider.timeline.css', false, LS_PLUGIN_VERSION );

	// LayerSlider Origami plugin
	wp_enqueue_script('layerslider-origami', LS_ROOT_URL.'/static/layerslider/plugins/origami/layerslider.origami.js', array('jquery'), LS_PLUGIN_VERSION );
	wp_enqueue_style('layerslider-origami', LS_ROOT_URL.'/static/layerslider/plugins/origami/layerslider.origami.css', false, LS_PLUGIN_VERSION );

	// LayerSlider Popup plugin
	wp_enqueue_script('layerslider-popup', LS_ROOT_URL.'/static/layerslider/plugins/popup/layerslider.popup.js', array('jquery'), LS_PLUGIN_VERSION );
	wp_enqueue_style('layerslider-popup', LS_ROOT_URL.'/static/layerslider/plugins/popup/layerslider.popup.css', false, LS_PLUGIN_VERSION );

	// 3rd-party: MiniColor
	wp_enqueue_script('minicolor', LS_ROOT_URL.'/static/minicolors/jquery.minicolors.js', array('jquery'), LS_PLUGIN_VERSION );
	wp_enqueue_style('minicolor', LS_ROOT_URL.'/static/minicolors/jquery.minicolors.css', false, LS_PLUGIN_VERSION );

	// 3rd-party: Air Datepicker
	wp_enqueue_style('air-datepicker', LS_ROOT_URL.'/static/air-datepicker/datepicker.min.css', false, '2.1.0' );
	wp_enqueue_script('air-datepicker', LS_ROOT_URL.'/static/air-datepicker/datepicker.min.js', array('jquery'), '2.1.0' );
	wp_enqueue_script('air-datepicker-en', LS_ROOT_URL.'/static/air-datepicker/i18n/datepicker.en.js', array('jquery'), '2.1.0' );

	// 3rd party: html2canvas
	wp_enqueue_script('html2canvas', LS_ROOT_URL.'/static/html2canvas/html2canvas.min.js', array('jquery'), '1.0.0a9' );

	// User CSS
	$uploads = wp_upload_dir();
	$uploads['baseurl'] = set_url_scheme( $uploads['baseurl'] );

	if(file_exists($uploads['basedir'].'/layerslider.custom.transitions.js')) {
		wp_enqueue_script('ls-user-transitions', $uploads['baseurl'].'/layerslider.custom.transitions.js', false, LS_PLUGIN_VERSION );
	}

	// User transitions
	if(file_exists($uploads['basedir'].'/layerslider.custom.css')) {
		wp_enqueue_style('ls-user', $uploads['baseurl'].'/layerslider.custom.css', false, LS_PLUGIN_VERSION );
	}
}



function ls_load_google_fonts() {

	// Check if Google Fonts is enabled as per the new privacy
	// settings introduced in version 6.7.6
	if( ! get_option('layerslider-google-fonts-enabled', true ) ) {
		return;
	}

	// Get font list
	$fonts = get_option('ls-google-fonts', array());
	$scripts = get_option('ls-google-font-scripts', array('latin', 'latin-ext'));

	// Check fonts if any
	if(!empty($fonts) && is_array($fonts)) {
		$lsFonts = array();
		foreach($fonts as $item) {
			if( is_admin() || !$item['admin'] ) {
				$lsFonts[] = htmlspecialchars($item['param']);
			}
		}

		if(!empty($lsFonts)) {
			$lsFonts = implode('%7C', $lsFonts);
			$protocol = is_ssl() ? 'https' : 'http';
			$query_args = array(
				'family' => $lsFonts,
				'subset' => implode('%2C', $scripts),
			);

			wp_enqueue_style('ls-google-fonts',
				add_query_arg($query_args, "$protocol://fonts.googleapis.com/css" ),
				array(), null
			);
		}
	}
}

function ls_meta_generator() {

	if( get_option('ls_suppress_debug_info', false ) ) {
		return;
	}


	$str = '<meta name="generator" content="Powered by LayerSlider '.LS_PLUGIN_VERSION.' - Multi-Purpose, Responsive, Parallax, Mobile-Friendly Slider Plugin for WordPress." />' . NL;
	$str.= '<!-- LayerSlider updates and docs at: https://layerslider.kreaturamedia.com -->' . NL;

	echo apply_filters('ls_meta_generator', $str);
}
