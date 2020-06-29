<?php
/**
 * Header functions
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

/**
 * Page title
 */

if (! function_exists('mfn_title')) {
	function mfn_title($title)
	{
		if (mfn_opts_get('mfn-seo') && mfn_ID()) {
			if ($seo_title = trim(get_post_meta(mfn_ID(), 'mfn-meta-seo-title', true))) {
				$title = esc_html($seo_title);
			}
		}

		return $title;
	}
}
add_filter('pre_get_document_title', 'mfn_title');

/**
 * Header meta tags
 */

if (! function_exists('mfn_meta')) {
	function mfn_meta()
	{
		// disable auto-formatting for telephone numbers

		echo '<meta name="format-detection" content="telephone=no">'."\n";

		// viewport

		if (mfn_opts_get('responsive')) {
			if (mfn_opts_get('responsive-zoom')) {
				echo '<meta name="viewport" content="width=device-width, initial-scale=1" />'."\n";
			} else {
				echo '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />'."\n";
			}
		}

		// favicon

		if( mfn_opts_get('favicon-img' ) || ( ! has_site_icon() ) ){
			echo '<link rel="shortcut icon" href="'. esc_url(mfn_opts_get('favicon-img', get_theme_file_uri('/images/favicon.ico'))) .'" type="image/x-icon" />'."\n";
		}

		// apple touch icon

		if( mfn_opts_get('apple-touch-icon') ){
			echo '<link rel="apple-touch-icon" href="'. esc_url(mfn_opts_get('apple-touch-icon')) .'" />'."\n";
		}

	}
}
add_action('wp_head', 'mfn_meta', 1);

/**
 * Built-in SEO Fields
 */

if (! function_exists('mfn_seo')) {
	function mfn_seo()
	{
		$mfn_ID = mfn_ID();

		if (mfn_opts_get('mfn-seo')) {

			if( mfn_ID() ){

				// description

				if (get_post_meta($mfn_ID, 'mfn-meta-seo-description', true)) {
					echo '<meta name="description" content="'. esc_attr(get_post_meta($mfn_ID, 'mfn-meta-seo-description', true)) .'"/>'."\n";
				} elseif (mfn_opts_get('meta-description')) {
					echo '<meta name="description" content="'. esc_attr(mfn_opts_get('meta-description')) .'"/>'."\n";
				}

				// keywords

				if (get_post_meta($mfn_ID, 'mfn-meta-seo-keywords', true)) {
					echo '<meta name="keywords" content="'. esc_attr(get_post_meta($mfn_ID, 'mfn-meta-seo-keywords', true)) .'"/>'."\n";
				} elseif (mfn_opts_get('meta-keywords')) {
					echo '<meta name="keywords" content="'. esc_attr(mfn_opts_get('meta-keywords')) .'"/>'."\n";
				}

				// og:image

				if (get_post_meta($mfn_ID, 'mfn-meta-seo-og-image', true)) {
					echo '<meta property="og:image" content="'. esc_attr(get_post_meta($mfn_ID, 'mfn-meta-seo-og-image', true)) .'"/>'."\n";
				} elseif (mfn_opts_get('mfn-seo-og-image')) {
					echo '<meta property="og:image" content="'. esc_attr(mfn_opts_get('mfn-seo-og-image')) .'"/>'."\n";
				} elseif( is_single($mfn_ID) ){
					if (has_post_thumbnail($mfn_ID)) {
						echo '<meta property="og:image" content="'. esc_attr(get_the_post_thumbnail_url($mfn_ID,'full')) .'"/>'."\n";
					}
				}

				// og:url, og:type, og:title, og:description, fb:app_id

				if( is_single($mfn_ID) ){

					echo '<meta property="og:url" content="'. esc_url(mfn_current_URL()) .'"/>'."\n";
					echo '<meta property="og:type" content="article"/>'."\n";
					echo '<meta property="og:title" content="'. esc_html(get_the_title($mfn_ID)) .'"/>'."\n";
					echo '<meta property="og:description" content="'. wp_strip_all_tags(get_the_excerpt($mfn_ID)) .'"/>'."\n";

					$fb_app_id = mfn_opts_get('seo-fb-app-id');
					if( $fb_app_id ){
						echo '<meta property="fb:app_id" content="'. esc_attr($fb_app_id) .'"/>'."\n";
					}

				}

			}

			// hreflang | only if WMPL is not active

			if (! function_exists('icl_object_id')) {
				$format_locale = str_replace('_', '-', get_locale());
				echo '<link rel="alternate" hreflang="'. esc_attr($format_locale) .'" href="'. esc_url(mfn_current_URL()) .'"/>'."\n";
			}
		}

		// google analytics

		if (mfn_opts_get('google-analytics')) {
			echo mfn_opts_get('google-analytics');
		}

		// facebook pixel

		if (mfn_opts_get('facebook-pixel')) {
			echo "\n";
			echo mfn_opts_get('facebook-pixel');
		}

	}
}
add_action('wp_head', 'mfn_seo', 0);

/**
 * Google Remarketing Code
 */

if (! function_exists('mfn_google_remarketing')) {
	function mfn_google_remarketing()
	{
		// google remarketing
		if (mfn_opts_get('google-remarketing')) {
			echo mfn_opts_get('google-remarketing');
		}
	}
}
add_action('wp_footer', 'mfn_google_remarketing', 100);

/**
 * Fonts selected in Theme Options
 */

if (! function_exists('mfn_fonts_selected')) {
	function mfn_fonts_selected()
	{
		$fonts = array(
			'content' => mfn_opts_get('font-content', 'Roboto'),
			'menu' => mfn_opts_get('font-menu', 'Roboto'),
			'title' => mfn_opts_get('font-title', 'Patua One'),
			'headings' => mfn_opts_get('font-headings', 'Patua One'),
			'headingsSmall' => mfn_opts_get('font-headings-small', 'Roboto'),
			'blockquote' => mfn_opts_get('font-blockquote', 'Patua One'),
			'decorative' => mfn_opts_get('font-decorative', 'Patua One'),
		);

		return $fonts;
	}
}

/**
 * Styles
 */

if (! function_exists('mfn_styles')) {
	function mfn_styles()
	{
		$theme_disable = mfn_opts_get('theme-disable');

		// wp_enqueue_style

		wp_enqueue_style('style', get_stylesheet_uri(), false, MFN_THEME_VERSION);

		wp_enqueue_style('mfn-base', get_theme_file_uri('/css/base.css'), false, MFN_THEME_VERSION);
		wp_enqueue_style('mfn-layout', get_theme_file_uri('/css/layout.css'), false, MFN_THEME_VERSION);
		wp_enqueue_style('mfn-shortcodes', get_theme_file_uri('/css/shortcodes.css'), false, MFN_THEME_VERSION);

		// plugins

		if (! isset($theme_disable[ 'entrance-animations' ])) {
			wp_enqueue_style('mfn-animations', get_theme_file_uri('/assets/animations/animations.min.css'), false, MFN_THEME_VERSION);
		}

		wp_enqueue_style('mfn-jquery-ui', get_theme_file_uri('/assets/ui/jquery.ui.all.css'), false, MFN_THEME_VERSION);
		wp_enqueue_style('mfn-jplayer', get_theme_file_uri('/assets/jplayer/css/jplayer.blue.monday.css'), false, MFN_THEME_VERSION);

		// responsive

		if (mfn_opts_get('responsive')) {
			wp_enqueue_style('mfn-responsive', get_theme_file_uri('/css/responsive.css'), false, MFN_THEME_VERSION);
		} else {
			wp_enqueue_style('mfn-responsive-off', get_theme_file_uri('/css/responsive-off.css'), false, MFN_THEME_VERSION);
		}

		// custom Theme Options styles

		if( ! mfn_opts_get( 'static-css' ) ){

			// predefined skins

			if ($layoutID = mfn_layout_ID()) {
				$skin = get_post_meta($layoutID, 'mfn-post-skin', true);
			} else {
				$skin = mfn_opts_get('skin', 'custom');
			}

			if( ( 'custom' != $skin ) && ( 'one' != $skin ) ){
				wp_enqueue_style('mfn-skin-'. $skin, get_theme_file_uri('/css/skins/'. $skin .'/style.css'), false, MFN_THEME_VERSION);
			}

		}

		// Google Fonts

		$fonts = mfn_fonts_selected();
		$google_fonts = mfn_fonts('all');
		$google_array = array();

		// subset

		if ($subset = mfn_opts_get('font-subset')) {
			$subset = '&subset='. str_replace(' ', '', $subset);
		}

		// style & weight

		if ($weight = mfn_opts_get('font-weight')) {
			$weight = ':'. implode(',', $weight);
		}

		foreach ($fonts as $font) {
			if (in_array($font, $google_fonts)) {
				$font_slug = str_replace(' ', '+', $font);
				$google_array[$font_slug] = $font_slug . $weight;
			}
		}

		if ($google_array) {
			$google_array = implode('|', $google_array);
			wp_enqueue_style('mfn-fonts', 'https://fonts.googleapis.com/css?family='. $google_array . $subset);
		}

	}
}
add_action('wp_enqueue_scripts', 'mfn_styles');

/*
 * RTL Tester
 */
 
if (! function_exists('mfn_rtl_tester')) {
	function mfn_rtl_tester() {
		global $wp_locale, $wp_styles;

		$wp_locale->text_direction = 'rtl';
		if ( ! is_a( $wp_styles, 'WP_Styles' ) ) {
			$wp_styles = new WP_Styles();
		}
		$wp_styles->text_direction = 'rtl';

	}
}

if( isset( $_GET['mfn-rtl'] ) ){
	add_action( 'init', 'mfn_rtl_tester' );
}

/*
 * Styles | Static
 */

if (! function_exists('mfn_styles_static')) {
	function mfn_styles_static()
	{
		if( mfn_opts_get( 'static-css' ) ){

			$upload_dir = wp_upload_dir();
			$url = $upload_dir['baseurl'] .'/betheme/css/static.css';
			wp_enqueue_style('mfn-static', $url, false, MFN_THEME_VERSION);

		}
	}
}
add_action('wp_enqueue_scripts', 'mfn_styles_static', 11);

/**
 * Styles | Inline HTML styles
 */

if (! function_exists('mfn_styles_html')) {
	function mfn_styles_html()
	{
		$css = '';

		// form submit buttons hidden

		$css .= 'form input.display-none{display:none!important}';

		// subheader

		if (mfn_opts_get('subheader-padding')) {
			$css .= '#Subheader{padding:'. esc_attr(mfn_opts_get('subheader-padding')) .'}';
		}

		// footer

		if (mfn_opts_get('footer-padding')) {
			$css .= '#Footer .widgets_wrapper{padding:'. esc_attr(mfn_opts_get('footer-padding')) .'}';
		}

		return $css;
	}
}

/**
 * Styles | Inline wp_head dynamic styles
 */

if (! function_exists('mfn_styles_inline')) {
	function mfn_styles_inline()
	{
		wp_register_style( 'mfn-dynamic', false );
		wp_enqueue_style( 'mfn-dynamic' );

		// custom fonts
		wp_add_inline_style( 'mfn-dynamic', mfn_styles_custom_font() );

		// backgrounds
		wp_add_inline_style( 'mfn-dynamic', mfn_styles_background() );

		// dynamic .php styles
		if( ! mfn_opts_get('static-css')){
			wp_add_inline_style( 'mfn-dynamic', mfn_styles_dynamic() );
		}

		// html inline styles
		wp_add_inline_style( 'mfn-dynamic', mfn_styles_html() );

		// builder styles

		// $mfn_builder_styles = new Mfn_Builder_Styles();
		// wp_add_inline_style( 'mfn-dynamic', $mfn_builder_styles->get_styles() );

	}
}
add_action('wp_enqueue_scripts', 'mfn_styles_inline', 100);

/**
 * Styles | Custom Font
 */

if (! function_exists('mfn_styles_custom_font')) {
	function mfn_styles_custom_font()
	{
		$output = '';
		$fonts = array();

		if( $font1 = mfn_opts_get('font-custom') ){
			$fonts[$font1] = array(
				'woff' => mfn_opts_get('font-custom-woff'),
				'ttf' => mfn_opts_get('font-custom-ttf'),
			);
		}

		if( $font2 = mfn_opts_get('font-custom2') ){
			$fonts[$font2] = array(
				'woff' => mfn_opts_get('font-custom2-woff'),
				'ttf' => mfn_opts_get('font-custom2-ttf'),
			);
		}

		foreach( $fonts as $font_k => $font ){
			$output .= '@font-face{';
				$output .= 'font-family:"'. esc_attr($font_k) .'";';
					$output .= 'src:';
					if ($font['woff']) {
						$output .= 'url("'. esc_url($font['woff']) .'") format("woff")';
					}
					if ($font['woff'] && $font['ttf']) {
						$output .= ',';
					}
					if ($font['ttf']) {
						$output .= 'url("'. esc_url($font['ttf']) .'") format("truetype")';
					}
					$output .= ';';
				$output .= 'font-weight:normal;';
				$output .= 'font-style:normal';
			$output .= '}';
		}

		return $output;
	}
}

/**
 * Styles | Background
 */

if (! function_exists('mfn_styles_background')) {
	function mfn_styles_background()
	{
		$output = $output_ultrawide = '';

		// HTML

		if ($layoutID = mfn_layout_ID()) {
			$htmlB = get_post_meta($layoutID, 'mfn-post-bg', true);
			$htmlP = get_post_meta($layoutID, 'mfn-post-bg-pos', true);
		} else {
			$htmlB = mfn_opts_get('img-page-bg');
			$htmlP = mfn_opts_get('position-page-bg');
		}

		if ($htmlB) {
			$aBg = array();
			$aBg[] = 'background-image:url('. esc_url($htmlB) .')';

			if ($htmlP) {
				$background_attr = explode(';', $htmlP);
				if ($background_attr[0]) {
					$aBg[] = 'background-repeat:'. esc_attr($background_attr[0]);
				}
				if ($background_attr[1]) {
					$aBg[] = 'background-position:'. esc_attr($background_attr[1]);
				}
				if ($background_attr[2]) {
					$aBg[] = 'background-attachment:'. esc_attr($background_attr[2]);
				}
				if ($background_attr[3]) {
					$aBg[] = 'background-size:'. esc_attr($background_attr[3]);
				} elseif (mfn_opts_get('size-page-bg')) {
					if (in_array(mfn_opts_get('size-page-bg'), array( 'cover', 'contain' ))) {
						$aBg[] = 'background-size:'. esc_attr(mfn_opts_get('size-page-bg'));
					} elseif (mfn_opts_get('size-page-bg') == 'cover-ultrawide') {
						$output_ultrawide .= 'html{background-size:cover}';
					}
				}
			}
			$background = implode(';', $aBg);

			$output .= 'html{'. $background. '}';
		}

		// Header wrapper

		$headerB = false;

		if (mfn_opts_get('img-subheader-bg')) {
			$headerB = mfn_opts_get('img-subheader-bg');
		}

		if (mfn_ID() && ! is_search()) {
			if (((mfn_ID() == get_option('page_for_posts')) || (get_post_type(mfn_ID()) == 'page')) && has_post_thumbnail(mfn_ID())) {

				// Pages & Blog Page ---
				$headerB = wp_get_attachment_image_src(get_post_thumbnail_id(mfn_ID()), 'full');
				$headerB = $headerB[0];

			} elseif (get_post_meta(mfn_ID(), 'mfn-post-header-bg', true)) {

				// Single Post ---
				$headerB = get_post_meta(mfn_ID(), 'mfn-post-header-bg', true);

			}
		}

		$headerP = mfn_opts_get('img-subheader-attachment');

		if ($headerB) {
			$aBg = array();
			$aBg[] = 'background-image:url('. esc_url($headerB) .')';

			if ($headerP == "fixed") {
				$aBg[] = 'background-attachment:fixed';
			} elseif ($headerP == "parallax") {
				// do nothing
			} elseif ($headerP) {
				$background_attr = explode(';', $headerP);
				if ($background_attr[0]) {
					$aBg[] = 'background-repeat:'. esc_attr($background_attr[0]);
				}
				if ($background_attr[1]) {
					$aBg[] = 'background-position:'. esc_attr($background_attr[1]);
				}
				if ($background_attr[2]) {
					$aBg[] = 'background-attachment:'. esc_attr($background_attr[2]);
				}
				if ($background_attr[3]) {
					$aBg[] = 'background-size:'. esc_attr($background_attr[3]);
				} elseif (mfn_opts_get('size-subheader-bg')) {
					if (in_array(mfn_opts_get('size-subheader-bg'), array( 'cover', 'contain' ))) {
						$aBg[] = 'background-size:'. esc_attr(mfn_opts_get('size-subheader-bg'));
					} elseif (mfn_opts_get('size-subheader-bg') == 'cover-ultrawide') {
						$output_ultrawide .= 'body:not(.template-slider) #Header_wrapper{background-size:cover}';
					}
				}
			}

			$background = implode(';', $aBg);

			$output .= 'body:not(.template-slider) #Header_wrapper{'. $background. '}';
		}

		// Top Bar

		$topbarB = mfn_opts_get('top-bar-bg-img');
		$topbarP = mfn_opts_get('top-bar-bg-position');

		if ($topbarB) {
			$aBg = array();
			$aBg[] = 'background-image:url('. esc_url($topbarB) .')';

			if ($topbarP) {
				$background_attr = explode(';', $topbarP);
				if ($background_attr[0]) {
					$aBg[] = 'background-repeat:'. esc_attr($background_attr[0]);
				}
				if ($background_attr[1]) {
					$aBg[] = 'background-position:'. esc_attr($background_attr[1]);
				}
				if ($background_attr[2]) {
					$aBg[] = 'background-attachment:'. esc_attr($background_attr[2]);
				}
				if ($background_attr[3]) {
					$aBg[] = 'background-size:'. esc_attr($background_attr[3]);
				} elseif (mfn_opts_get('topbar-bg-img-size')) {
					if (in_array(mfn_opts_get('topbar-bg-img-size'), array( 'cover', 'contain' ))) {
						$aBg[] = 'background-size:'. esc_attr(mfn_opts_get('topbar-bg-img-size'));
					}
				}
			}

			$background = implode(';', $aBg);

			$output .= '#Top_bar,#Header_creative{'. $background. '}';
		}

		// Subheader

		if (get_post_meta(mfn_ID(), 'mfn-post-subheader-image', true)) {
			$subheaderB = get_post_meta(mfn_ID(), 'mfn-post-subheader-image', true);
		} else {
			$subheaderB = mfn_opts_get('subheader-image');
		}

		$subheaderP = mfn_opts_get('subheader-position');

		if ($subheaderB) {
			$aBg = array();
			$aBg[] = 'background-image:url('. esc_url($subheaderB) .')';

			if ($subheaderP) {
				$background_attr = explode(';', $subheaderP);
				if ($background_attr[0]) {
					$aBg[] = 'background-repeat:'. esc_attr($background_attr[0]);
				}
				if ($background_attr[1]) {
					$aBg[] = 'background-position:'. esc_attr($background_attr[1]);
				}
				if ($background_attr[2]) {
					$aBg[] = 'background-attachment:'. esc_attr($background_attr[2]);
				}
				if ($background_attr[3]) {
					$aBg[] = 'background-size:'. esc_attr($background_attr[3]);
				} elseif (mfn_opts_get('subheader-size')) {
					if (in_array(mfn_opts_get('subheader-size'), array( 'cover', 'contain' ))) {
						$aBg[] = 'background-size:'. esc_attr(mfn_opts_get('subheader-size'));
					} elseif (mfn_opts_get('subheader-size') == 'cover-ultrawide') {
						$output_ultrawide .= '#Subheader{background-size:cover}';
					}
				}
			}

			$background = implode(';', $aBg);

			$output .= '#Subheader{'. $background. '}';
		}

		// Footer

		$footerB = mfn_opts_get('footer-bg-img');
		$footerP = mfn_opts_get('footer-bg-img-position');

		if ($footerB) {
			$aBg = array();
			$aBg[] = 'background-image:url('. esc_url($footerB) .')';

			if ($footerP) {
				$background_attr = explode(';', $footerP);
				if ($background_attr[0]) {
					$aBg[] = 'background-repeat:'. esc_attr($background_attr[0]);
				}
				if ($background_attr[1]) {
					$aBg[] = 'background-position:'. esc_attr($background_attr[1]);
				}
				if ($background_attr[2]) {
					$aBg[] = 'background-attachment:'. esc_attr($background_attr[2]);
				}
				if ($background_attr[3]) {
					$aBg[] = 'background-size:'. esc_attr($background_attr[3]);
				} elseif (mfn_opts_get('footer-bg-img-size')) {
					if (in_array(mfn_opts_get('footer-bg-img-size'), array( 'cover', 'contain' ))) {
						$aBg[] = 'background-size:'. esc_attr(mfn_opts_get('footer-bg-img-size'));
					} elseif (mfn_opts_get('footer-bg-img-size') == 'cover-ultrawide') {
						$output_ultrawide .= '#Footer{background-size:cover}';
					}
				}
			}

			$background = implode(';', $aBg);

			$output .= '#Footer{'. $background. '}';
		}

		// output -----

		if ($output_ultrawide) {
			$output .= '@media only screen and (min-width: 1921px){'. $output_ultrawide .'}';
		}

		return $output;
	}
}

/**
 * Styles | Minify
 */

if (! function_exists('mfn_styles_minify')) {
	function mfn_styles_minify($css)
	{
		// remove comments
		$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);

		// remove whitespace
		$css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);

		return $css;
	}
}

/**
 * Styles | Dynamic
 */

if (! function_exists('mfn_styles_dynamic')) {
	function mfn_styles_dynamic()
	{
		ob_start();

		// responsive

		if (mfn_opts_get('responsive')) {
			include_once get_theme_file_path('/style-responsive.php');
		}

		// colors

		if ($layoutID = mfn_layout_ID()) {
			$skin = get_post_meta($layoutID, 'mfn-post-skin', true);
		} else {
			$skin = mfn_opts_get('skin', 'custom');
		}

		if ('custom' == $skin) {
			include_once get_theme_file_path('/style-colors.php');
		} elseif ('one' == $skin) {
			include_once get_theme_file_path('/style-one.php');
		}

		// style PHP

		include_once get_theme_file_path('/style.php');

		$css = ob_get_contents();

		ob_get_clean();

		return mfn_styles_minify($css);
	}
}

/**
 * Styles | Custom Styles
 */

if (! function_exists('mfn_styles_custom')) {
	function mfn_styles_custom()
	{
		// Theme Options | Custom CSS

		$css = mfn_opts_get('custom-css');

		// Page Options | Custom CSS

		$css .= get_post_meta(mfn_ID(), 'mfn-post-css', true);

		// Layouts | Custom colors

		if ($layoutID = mfn_layout_ID()){

			$layout_css = '';

			if (get_post_meta($layoutID, 'mfn-post-background-subheader', true)) {

				$layout_css .= '#Subheader{background-color:'. get_post_meta($layoutID, 'mfn-post-background-subheader', true) .'}';

			}

			if (get_post_meta($layoutID, 'mfn-post-color-subheader', true)) {

				$layout_css .= '#Subheader .title{color:'. get_post_meta($layoutID, 'mfn-post-color-subheader', true) .'}';
				$layout_css .= '#Subheader ul.breadcrumbs li, #Subheader ul.breadcrumbs li a{color:'. mfn_hex2rgba(get_post_meta($layoutID, 'mfn-post-color-subheader', true), .6) .'}';

			}

			$css .= $layout_css;
		}

		wp_register_style( 'mfn-custom', false );
		wp_enqueue_style( 'mfn-custom' );

		wp_add_inline_style( 'mfn-custom', $css );
	}
}
add_action('wp_enqueue_scripts', 'mfn_styles_custom', 101);

/**
 * Scripts
 */

if (! function_exists('mfn_scripts')) {
	function mfn_scripts()
	{
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-widget');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('jquery-ui-accordion');

		wp_enqueue_script('mfn-plugins', get_theme_file_uri('/js/plugins.js'), array('jquery'), MFN_THEME_VERSION, true);
		wp_enqueue_script('mfn-menu', get_theme_file_uri('/js/menu.js'), array('jquery'), MFN_THEME_VERSION, true);

		wp_enqueue_script('mfn-animations', get_theme_file_uri('/assets/animations/animations.min.js'), array('jquery'), MFN_THEME_VERSION, true);
		wp_enqueue_script('mfn-jplayer', get_theme_file_uri('/assets/jplayer/jplayer.min.js'), array('jquery'), MFN_THEME_VERSION, true);

		$parallax = mfn_parallax_plugin();
		if ($parallax == 'translate3d') {
			wp_enqueue_script('mfn-parallax', get_theme_file_uri('/js/parallax/translate3d.js'), array('jquery'), MFN_THEME_VERSION, true);
		} elseif ($parallax == 'stellar') {
			wp_enqueue_script('mfn-stellar', get_theme_file_uri('/js/parallax/stellar.js'), array('jquery'), MFN_THEME_VERSION, true);
		}

		wp_enqueue_script('mfn-scripts', get_theme_file_uri('/js/scripts.js'), array('jquery'), MFN_THEME_VERSION, true);

		// single post | reply comment

		if (is_singular() && comments_open() && get_option('thread_comments')) {
			wp_enqueue_script('comment-reply');
		}

		// scripts config

		$lightboxOptions = mfn_opts_get('prettyphoto-options');

		$config = array(
			'mobileInit' => mfn_opts_get('mobile-menu-initial', 1240),
			'parallax' => mfn_parallax_plugin(),
			'responsive' => intval(mfn_opts_get('responsive', 0)),
			'sidebarSticky' => mfn_opts_get('sidebar-sticky') ? true : false,
			'lightbox' => array(
				'disable' => isset($lightboxOptions['disable']) ? true : false,
				'disableMobile' => isset($lightboxOptions['disable-mobile']) ? true : false,
				'title' => isset($lightboxOptions['title']) ? true : false,
			),
			'slider' => array(
				'blog' => intval(mfn_opts_get('slider-blog-timeout', 0)),
				'clients' => intval(mfn_opts_get('slider-clients-timeout', 0)),
				'offer' => intval(mfn_opts_get('slider-offer-timeout', 0)),
				'portfolio' => intval(mfn_opts_get('slider-portfolio-timeout', 0)),
				'shop' => intval(mfn_opts_get('slider-shop-timeout', 0)),
				'slider' => intval(mfn_opts_get('slider-slider-timeout', 0)),
				'testimonials' => intval(mfn_opts_get('slider-testimonials-timeout', 0)),
			),
		);

		if (mfn_opts_get('love')) {
			$config['ajax'] = admin_url('admin-ajax.php');
		}

		wp_localize_script('mfn-plugins', 'mfn', $config);
	}
}
add_action('wp_enqueue_scripts', 'mfn_scripts');

/**
 * Scripts | Custom JS
 */

function mfn_scripts_custom() {
	if ($custom_js = mfn_opts_get('custom-js')) {
		wp_add_inline_script('mfn-scripts', $custom_js);
	}
}
add_action('wp_enqueue_scripts', 'mfn_scripts_custom');

/**
 * Body classes | Header
 * Adds classes to the array of body classes.
 */

if (! function_exists('mfn_header_style')) {
	function mfn_header_style($firstPartOnly = false)
	{
		$header_layout = false;

		// plugin: Muffin Header Builder

		if (class_exists('Mfn_HB_Front') && get_site_option('mfn_header_builder')) {
			return 'mhb';
		}

		// header styles

		if ($_GET && key_exists('mfn-h', $_GET)) {
			$header_layout = esc_html($_GET['mfn-h']); // demo
		} elseif ($layoutID = mfn_layout_ID()) {
			$header_layout = get_post_meta($layoutID, 'mfn-post-header-style', true);
		} elseif (mfn_opts_get('header-style')) {
			$header_layout =  mfn_opts_get('header-style');
		}

		if (strpos($header_layout, ',')) {

			// multiple header parameters

			$a_header_layout = explode(',', $header_layout);

			// return ONLY first parameter

			if ($firstPartOnly) {
				return 'header-'.$a_header_layout[0];
			}

			foreach ((array)$a_header_layout as $key => $val) {
				$a_header_layout[$key] = 'header-'. $val;
			}
			$header = implode(' ', $a_header_layout);

		} else {

			// one parameter
			$header = 'header-'. $header_layout;
		}

		return $header;
	}
}

/**
 * Convert sidebar name
 */

if (! function_exists('mfn_sidebar_convert_name')) {
	function mfn_sidebar_convert_name($sidebar_name){
		return 'sidebar-'. str_replace('+', '-', urlencode(strtolower(trim($sidebar_name))));
	}
}

/**
 * Converts sidebar ID to name (slug)
 */

if (! function_exists('mfn_sidebar_id_name')) {
	function mfn_sidebar_id_name($sidebar_id){

		$sidebar_name = false;

		if ($sidebar_id >= 0) {

			$dynamic_sidebars = mfn_opts_get('sidebars');

			if (isset($dynamic_sidebars[$sidebar_id])) {
				$sidebar_name = mfn_sidebar_convert_name($dynamic_sidebars[$sidebar_id]);
			}

		}

		return $sidebar_name;
	}
}

/**
 * Get sidebar data for single or both sidebars
 */

if (! function_exists('mfn_sidebar_one_or_both')) {
	function mfn_sidebar_one_or_both($layout, $sidebar, $sidebar2 = false){

		$result = false;

		if( 'no-sidebar' == $layout ){
			return false;
		}

		if( 'both-sidebars' == $layout ){

			// both sidebars

			if ($sidebar && is_active_sidebar($sidebar)) {
				$result['layout'] = $layout;
				$result['sidebar']['first'] = $sidebar;
			}

			if ($sidebar2 && is_active_sidebar($sidebar2)) {
				if( !$result['layout'] ){
					$result['layout'] = 'right-sidebar';
				}
				$result['sidebar']['second'] = $sidebar2;
			} elseif($result['layout']) {
				$result['layout'] = 'left-sidebar';
			}

		} else {

			// single sidebar

			if ($sidebar && is_active_sidebar($sidebar)) {
				$result['layout'] = $layout;
				$result['sidebar']['first'] = $sidebar;
			}

		}

		return $result;

	}
}

/**
 * Sidebar
 * Get full sidebar data (layout + sidebar(s)) for current page
 */

if (! function_exists('mfn_sidebar')) {
	function mfn_sidebar(){

		// plugin related sidebars -----

		// WooCommerce: disable sidebar for single product in Theme Options

		if ( function_exists('is_product') && is_product() && ('shop' == mfn_opts_get('shop-sidebar')) ) {

			return false;

		}

		// WooCommerce: shop & categories

		if ( function_exists('is_woocommerce') && is_woocommerce() ) {

			$layout = get_post_meta(mfn_ID(), 'mfn-post-layout', true);

			if ( (! $layout) || ('both-sidebars' == $layout) ) {
				$layout = 'right-sidebar';
			}

			$sidebar = 'shop';
			if( is_search() ){
				$sidebar = 'mfn-search';
			}

			return mfn_sidebar_one_or_both($layout, $sidebar, false);

		}

		// bbPress

		if ( function_exists('is_bbpress') && is_bbpress() ) {

			return mfn_sidebar_one_or_both('right-sidebar', 'forum', false);

		}

		// BuddyPress

		if ( function_exists('is_buddypress') && is_buddypress() ) {

			return mfn_sidebar_one_or_both('right-sidebar', 'buddy', false);

		}

		// Easy Digital Downloads

		if ( 'download' == get_post_type() ) {

			return mfn_sidebar_one_or_both('right-sidebar', 'edd', false);

		}

		// Events Calendar

		if ( function_exists('tribe_is_month') ) {
			if ( tribe_is_month() || tribe_is_day() || tribe_is_event() || tribe_is_event_query() || tribe_is_venue() ) {

				return mfn_sidebar_one_or_both('right-sidebar', 'events', false);

			}
		}

		// theme related sidebars -----

		// template blank & under construction

		if ( is_page_template('template-blank.php') || is_page_template('under-construction.php') ) {

			return false;

		}

		// search page

		if ( is_search() ) {

			$layout = mfn_opts_get('search-layout', 'right-sidebar');

			return mfn_sidebar_one_or_both($layout, 'mfn-search', false);

		}

		// exit if page has no ID

		if( ! mfn_ID() ){
			return false;
		}

		// blog category

		if ( is_category() ){

			$blog_page_id = mfn_get_blog_ID();

			if( ! $blog_page_id ){
				return false;
			}

			$layout = get_post_meta($blog_page_id, 'mfn-post-layout', true);

			$category = get_category(get_query_var('cat'));
			$sidebar = 'blog-cat-'. $category->slug;

			if (! is_active_sidebar($sidebar)) {

				$sidebar_id = get_post_meta($blog_page_id, 'mfn-post-sidebar', true);
				$sidebar = mfn_sidebar_id_name($sidebar_id);
			}

			$sidebar2_id = get_post_meta($blog_page_id, 'mfn-post-sidebar2', true);
			$sidebar2 = mfn_sidebar_id_name($sidebar2_id);

			return mfn_sidebar_one_or_both($layout, $sidebar, $sidebar2);

		}

		// portfolio taxonomy

		if( is_tax() ){

			$portfolio_page_id = mfn_opts_get('portfolio-page');

			if( ! $portfolio_page_id ){
				return false;
			}

			$layout = get_post_meta($portfolio_page_id, 'mfn-post-layout', true);
			$sidebar = 'portfolio-cat-'. get_query_var('portfolio-types');

			if (! is_active_sidebar($sidebar)) {

				$sidebar_id = get_post_meta($portfolio_page_id, 'mfn-post-sidebar', true);
				$sidebar = mfn_sidebar_id_name($sidebar_id);
			}

			return mfn_sidebar_one_or_both($layout, $sidebar, false);
		}

		// sidebar set in post meta or forced in theme options

		if ( ('page' == get_post_type()) && ($layout = mfn_opts_get('single-page-layout')) ) {

			// theme options | force sidebar for single page

			$sidebar = mfn_sidebar_convert_name(mfn_opts_get('single-page-sidebar'));
			$sidebar2 = mfn_sidebar_convert_name(mfn_opts_get('single-page-sidebar2'));

			return mfn_sidebar_one_or_both($layout, $sidebar, $sidebar2);

		} elseif ( ('post' == get_post_type()) && is_single() && ($layout = mfn_opts_get('single-layout')) ) {

			// theme options | force sidebar for single post

			$sidebar = mfn_sidebar_convert_name(mfn_opts_get('single-sidebar'));
			$sidebar2 = mfn_sidebar_convert_name(mfn_opts_get('single-sidebar2'));

			return mfn_sidebar_one_or_both($layout, $sidebar, $sidebar2);

		} elseif ( ('portfolio' == get_post_type()) && is_single() && ($layout = mfn_opts_get('single-portfolio-layout'))  ) {

			// theme options | force sidebar for single portfolio

			$sidebar = mfn_sidebar_convert_name(mfn_opts_get('single-portfolio-sidebar'));
			$sidebar2 = mfn_sidebar_convert_name(mfn_opts_get('single-portfolio-sidebar2'));

			return mfn_sidebar_one_or_both($layout, $sidebar, $sidebar2);

		} elseif ($layout = get_post_meta(mfn_ID(), 'mfn-post-layout', true) ) {

			// post meta

			$sidebar_id = get_post_meta(mfn_ID(), 'mfn-post-sidebar', true);
			$sidebar = mfn_sidebar_id_name($sidebar_id);

			$sidebar2_id = get_post_meta(mfn_ID(), 'mfn-post-sidebar2', true);
			$sidebar2 = mfn_sidebar_id_name($sidebar2_id);

			return mfn_sidebar_one_or_both($layout, $sidebar, $sidebar2);

		}

		return false;

	}
}

/**
 * Converts sidebar layout to body classes
 */

if (! function_exists('mfn_sidebar_class')) {
	function mfn_sidebar_class($sidebars){

		if(empty($sidebars['layout'])){
			return false;
		}

		$layout = $sidebars['layout'];

		switch ($layout) {

			case 'left-sidebar':
				$classes = 'with_aside aside_left';
				break;

			case 'right-sidebar':
				$classes = 'with_aside aside_right';
				break;

			case 'both-sidebars':
				$classes = 'with_aside aside_both';
				break;

			default:
				$classes = false;

		}

		return $classes;
	}
}

/**
 * Body classes
 * Adds classes to the array of body classes.
 */

if (! function_exists('mfn_body_classes')) {
	function mfn_body_classes($classes)
	{
		$layoutID = mfn_layout_ID();

		// slider

		if (mfn_slider_isset()) {
			if (function_exists('is_woocommerce') && is_woocommerce()) {
				// do nothing
			} else {
				$classes[] = 'template-slider';
			}
		}

		// sidebar

		$classes[] = mfn_sidebar_class(mfn_sidebar());

		// skin

		if ($layoutID) {
			$classes[] = 'color-'. get_post_meta($layoutID, 'mfn-post-skin', true);
		} else {
			$classes[] = 'color-'. mfn_opts_get('skin', 'custom');
		}

		// style: default & simple

		if ($_GET && key_exists('mfn-style', $_GET)) {
			$classes[] = 'style-'. esc_html($_GET['mfn-style']); // demo
		} else {
			$classes[] = 'style-'. mfn_opts_get('style', 'default');
		}

		// button style

		if ($_GET && key_exists('mfn-btn', $_GET)) {
			$classes[] = 'button-'. esc_html($_GET['mfn-btn']); // demo
		} else {
			$classes[] = 'button-'. mfn_opts_get('button-style', 'default');
		}

		// layout: full width & boxed

		if ($_GET && key_exists('mfn-box', $_GET)) {
			$classes[] = 'layout-boxed'; // demo
		} elseif ($layoutID) {
			$classes[] = 'layout-'. get_post_meta($layoutID, 'mfn-post-layout', true);
		} else {
			$classes[] = 'layout-'. mfn_opts_get('layout', 'full-width');
		}

		// one page

		if (get_post_meta(mfn_ID(), 'mfn-post-one-page', true)) {
			$classes[] = 'one-page';
		}

		// image frame: style

		if ($_GET && key_exists('mfn-if', $_GET)) {
			$classes[] = 'if-'. esc_html($_GET['mfn-if']); // demo
		} elseif (mfn_opts_get('image-frame-style')) {
			$classes[] = 'if-'. mfn_opts_get('image-frame-style');
		}

		// image frame: border

		if (mfn_opts_get('image-frame-border')) {
			$classes[] = 'if-border-'. mfn_opts_get('image-frame-border');
		}

		// image frame: caption

		if (mfn_opts_get('image-frame-caption')) {
			$classes[] = 'if-caption-on';
		}

		// content padding

		if (mfn_opts_get('content-remove-padding')) {
			$classes[] = 'no-content-padding';
		} elseif (get_post_meta(mfn_ID(), 'mfn-post-remove-padding', true)) {
			$classes[] = 'no-content-padding';
		}

		// single template

		if (get_post_meta(mfn_ID(), 'mfn-post-template', true)) {
			$classes[] = 'single-template-'. get_post_meta(mfn_ID(), 'mfn-post-template', true);
		}

		// love

		if (! mfn_opts_get('love')) {
			$classes[] = 'hide-love';
		}

		// table: hover

		if (mfn_opts_get('table-hover')) {
			$classes[] = 'table-'. mfn_opts_get('table-hover');
		}

		// plugin: Contact Form 7: form error

		if (mfn_opts_get('cf7-error')) {
			$classes[] = 'cf7p-'. mfn_opts_get('cf7-error');
		}

		// advanced | other

		$layout_options = mfn_opts_get('layout-options');
		if (is_array($layout_options)) {
			if (isset($layout_options['no-shadows'])) {
				$classes[] = 'no-shadows';
			}
			if (isset($layout_options['boxed-no-margin'])) {
				$classes[] = 'boxed-no-margin';
			}
		}

		// header -----

		$header_options = mfn_opts_get('header-fw') ? mfn_opts_get('header-fw') : false;

		// haeder | layout

		$classes[] = mfn_header_style();

		// header | full width

		if ($_GET && key_exists('mfn-hfw', $_GET)) {
			$classes[] = 'header-fw'; // demo
		} elseif (isset($header_options['full-width'])) {
			$classes[] = 'header-fw';
		}

		// header | boxed

		if (is_array($header_options) && isset($header_options['header-boxed'])) {
			$classes[] = 'header-boxed';
		}

		// header | minimalist

		if ($_GET && key_exists('mfn-min', $_GET)) {
			$classes[] = 'minimalist-header'; // demo
		} elseif ($layoutID) {
			if (get_post_meta($layoutID, 'mfn-post-minimalist-header', true) == 'no') {
				$classes[] = 'minimalist-header-no';
			} elseif (get_post_meta($layoutID, 'mfn-post-minimalist-header', true)) {
				$classes[] = 'minimalist-header';
			}
		} elseif (mfn_opts_get('minimalist-header') == 'no') {
			$classes[] = 'minimalist-header-no';
		} elseif (mfn_opts_get('minimalist-header')) {
			$classes[] = 'minimalist-header';
		}

		// header | sticky

		if ($layoutID) {
			if (get_post_meta($layoutID, 'mfn-post-sticky-header', true)) {
				$classes[] = 'sticky-header';
			}
		} elseif (mfn_opts_get('sticky-header')) {
			$classes[] = 'sticky-header';
		}

		// header | sticky: style

		if ($_GET && key_exists('mfn-ss', $_GET)) {
			$classes[] = 'sticky-'. esc_html($_GET['mfn-ss']); // demo
		} elseif ($layoutID) {
			$classes[] = 'sticky-'. get_post_meta($layoutID, 'mfn-post-sticky-header-style', true);
		} else {
			$classes[] = 'sticky-'. mfn_opts_get('sticky-header-style', 'white');
		}

		// action bar

		$action_bar = mfn_opts_get('action-bar');
		if ('1' === $action_bar) {
			// BeTheme < 21.3.3 compatibility
			$classes[] = 'ab-show';
		} elseif( isset($action_bar['show']) ) {
			$classes[] = 'ab-show';
		} else {
			$classes[] = 'ab-hide';
		}

		// subheader | transparent

		$skin = mfn_opts_get('skin', 'custom');
		if ($_GET && key_exists('mfn-subtr', $_GET)) {
			$classes[] = 'subheader-transparent'; // demo
		} elseif (! in_array($skin, array('custom','one'))) {
			if (mfn_opts_get('subheader-transparent') != 100) {
				$classes[] = 'subheader-transparent';
			}
		}

		// subheader | style

		if ($_GET && key_exists('mfn-sh', $_GET)) {
			$classes[] = 'subheader-'. esc_html($_GET['mfn-sh']); // demo
		} else {
			$classes[] = 'subheader-'. mfn_opts_get('subheader-style', 'title-left');
		}

		// menu | style

		if ($_GET && key_exists('mfn-m', $_GET)) {
			$classes[] = 'menu-'. esc_html($_GET['mfn-m']); // demo
		} elseif (mfn_opts_get('menu-style')) {
			$classes[] = 'menu-'. mfn_opts_get('menu-style');
		}

		// menu | options

		$menu_options = mfn_opts_get('menu-options');
		if (is_array($menu_options) && isset($menu_options['align-right'])) {
			$classes[] = 'menuo-right';
		}
		if (is_array($menu_options) && isset($menu_options['menu-arrows'])) {
			$classes[] = 'menuo-arrows';
		}
		if (is_array($menu_options) && isset($menu_options['hide-borders'])) {
			$classes[] = 'menuo-no-borders';
		}
		if (is_array($menu_options) && isset($menu_options['submenu-active'])) {
			$classes[] = 'menuo-sub-active';
		}
		if (is_array($menu_options) && isset($menu_options['submenu-limit'])) {
			$classes[] = 'menuo-sub-limit';
		}
		if (is_array($menu_options) && isset($menu_options['last'])) {
			$classes[] = 'menuo-last';
		}

		// megamenu: style

		if (mfn_opts_get('menu-mega-style')) {
			$classes[] = 'mm-'. mfn_opts_get('menu-mega-style');
		}

		// logo

		if (mfn_opts_get('logo-vertical-align')) {
			$classes[] = 'logo-valign-'. mfn_opts_get('logo-vertical-align');
		}

		$logo_options = mfn_opts_get('logo-advanced');
		if (is_array($logo_options) && isset($logo_options['no-margin'])) {
			$classes[] = 'logo-no-margin';
		}
		if (is_array($logo_options) && isset($logo_options['overflow'])) {
			$classes[] = 'logo-overflow';
		}
		if (is_array($logo_options) && isset($logo_options['no-sticky-padding'])) {
			$classes[] = 'logo-no-sticky-padding';
		}

		// footer -----

		// footer | style

		if ($_GET && key_exists('mfn-ftr', $_GET)) {
			$classes[] = 'footer-'. esc_html($_GET['mfn-ftr']); // demo
		} elseif (mfn_opts_get('footer-style')) {
			$classes[] = 'footer-'. mfn_opts_get('footer-style');
		}

		// footer | copy & social

		if (mfn_opts_get('footer-hide') == 'center') {
			$classes[] = 'footer-copy-center';
		}

		// responsive -----

		if (! mfn_opts_get('responsive')) {
			$classes[] = 'responsive-off';
		}
		if (mfn_opts_get('responsive-boxed2fw')) {
			$classes[] = 'boxed2fw';
		}
		if (mfn_opts_get('no-hover')) {
			$classes[] = 'no-hover-'. mfn_opts_get('no-hover');
		}
		if (mfn_opts_get('no-section-bg')) {
			$classes[] = 'no-section-bg-'. mfn_opts_get('no-section-bg');
		}
		if (mfn_opts_get('responsive-top-bar')) {
			$classes[] = 'mobile-tb-'. mfn_opts_get('responsive-top-bar');
		}
		if (mfn_opts_get('responsive-mobile-menu')) {
			$classes[] = 'mobile-'. mfn_opts_get('responsive-mobile-menu');
		}
		if (mfn_opts_get('mobile-menu')) {
			$classes[] = 'mobile-menu';
		}

		if( 'no-tablet' == mfn_opts_get('builder-section-padding') ) {
			$classes[] = 'no-sec-padding';
		}
		if( 'no-mobile' == mfn_opts_get('builder-section-padding') ) {
			$classes[] = 'no-sec-padding-mob';
		}

		$classes[] = 'mobile-mini-'. mfn_opts_get('responsive-header-minimal', 'mr-ll');

		// responsive | tablet | options

		$responsive_header_mob = mfn_opts_get('responsive-header-tablet');
		if (is_array($responsive_header_mob)) {
			if (isset($responsive_header_mob['sticky'])) {
				$classes[] = 'tablet-sticky';
			}
		}

		// responsive | mobile | options

		$responsive_header_mob = mfn_opts_get('responsive-header-mobile');
		if (is_array($responsive_header_mob)) {
			if (isset($responsive_header_mob['minimal'])) {
				$classes[] = 'mobile-header-mini';
			}
			if (isset($responsive_header_mob['sticky'])) {
				$classes[] = 'mobile-sticky';
			}
			if (isset($responsive_header_mob['transparent'])) {
				$classes[] = 'mobile-tr-header';
			}
		}

		// transparent -----

		$transparent_options = mfn_opts_get('transparent');
		if (is_array($transparent_options)) {
			if (isset($transparent_options['header'])) {
				$classes[] = 'tr-header';
			}
			if (isset($transparent_options['menu'])) {
				$classes[] = 'tr-menu';
			}
			if (isset($transparent_options['content'])) {
				$classes[] = 'tr-content';
			}
			if (isset($transparent_options['footer'])) {
				$classes[] = 'tr-footer';
			}
		}

		// demo / debug

		if ($layoutID) {
			$classes[] = 'dbg-lay-id-'. $layoutID;
		}

		$reg = mfn_is_registered() ? 'reg-' : '';
		$classes[] = 'be-'. $reg . str_replace('.', '', MFN_THEME_VERSION);

		return $classes;
	}
}
add_filter('body_class', 'mfn_body_classes');


/**
 * Annoying styles remover
 */
if (! function_exists('mfn_remove_recent_comments_style')) {
	function mfn_remove_recent_comments_style()
	{
		global $wp_widget_factory;
		if (isset($wp_widget_factory->widgets['WP_Widget_Recent_Comments'])) {
			remove_action('wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ));
		}
	}
}
add_action('widgets_init', 'mfn_remove_recent_comments_style');
