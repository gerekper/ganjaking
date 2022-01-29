<?php
/**
 * Display the legacy free coming soon page
 */
if ( ! function_exists( 'seedprod_pro_csp4_render_comingsoon_page' ) ) {
	function seedprod_pro_csp4_render_comingsoon_page() {
		extract( seedprod_pro_seed_csp4_get_settings() );

		if ( ! isset( $status ) ) {
			$err = new WP_Error( 'error', __( 'Please enter your settings.', 'coming-soon' ) );
			echo $err->get_error_message();
			exit();
		}

		if ( empty( $_GET['cs_preview'] ) ) {
			$_GET['cs_preview'] = false;
		}

		// Check if Preview
		$is_preview = false;
		if ( ( isset( $_GET['cs_preview'] ) && $_GET['cs_preview'] == 'true' ) ) {
			$is_preview = true;
		}

		// Exit if a custom login page
		if ( empty( $disable_default_excluded_urls ) ) {
			if ( preg_match( '/login|admin|dashboard|account/i', $_SERVER['REQUEST_URI'] ) > 0 && $is_preview == false ) {
				return false;
			}
		}

		// Check if user is logged in.
		if ( $is_preview === false ) {
			if ( is_user_logged_in() ) {
				return false;
			}
		}

		// set headers
		if ( $status == '2' ) {
			header( 'HTTP/1.1 503 Service Temporarily Unavailable' );
			header( 'Status: 503 Service Temporarily Unavailable' );
			header( 'Retry-After: 86400' ); // retry in a day
			$csp4_maintenance_file = WP_CONTENT_DIR . '/maintenance.php';
			if ( ! empty( $enable_maintenance_php ) and file_exists( $csp4_maintenance_file ) ) {
				include_once $csp4_maintenance_file;
				exit();
			}
		}

		// Prevetn Plugins from caching
		// Disable caching plugins. This should take care of:
		//   - W3 Total Cache
		//   - WP Super Cache
		//   - ZenCache (Previously QuickCache)
		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}
		if ( ! defined( 'DONOTCDN' ) ) {
			define( 'DONOTCDN', true );
		}
		if ( ! defined( 'DONOTCACHEDB' ) ) {
			define( 'DONOTCACHEDB', true );
		}
		if ( ! defined( 'DONOTMINIFY' ) ) {
			define( 'DONOTMINIFY', true );
		}
		if ( ! defined( 'DONOTCACHEOBJECT' ) ) {
			define( 'DONOTCACHEOBJECT', true );
		}
		//ob_end_clean();
		nocache_headers();

		// render template tags
		if ( empty( $html ) ) {
			$template      = file_get_contents( SEEDPROD_PRO_PLUGIN_PATH . 'app/backwards/themes/default/index.php' );
			$template_tags = array(
				'{Title}'           => seedprod_pro_seed_csp4_title(),
				'{MetaDescription}' => seedprod_pro_seed_csp4_metadescription(),
				'{Privacy}'         => seedprod_pro_seed_csp4_privacy(),
				'{Favicon}'         => seedprod_pro_seed_csp4_favicon(),
				'{CustomCSS}'       => seedprod_pro_seed_csp4_customcss(),
				'{Head}'            => seedprod_pro_seed_csp4_head(),
				'{Footer}'          => seedprod_pro_seed_csp4_footer(),
				'{Logo}'            => seedprod_pro_seed_csp4_logo(),
				'{Headline}'        => seedprod_pro_seed_csp4_headline(),
				'{Description}'     => seedprod_pro_seed_csp4_description(),
				'{Credit}'          => seedprod_pro_seed_csp4_credit(),
				'{Append_HTML}'     => seed_csp4_append_html(),
			);
			echo strtr( $template, $template_tags );
		} else {
			echo $html;
		}
		exit();
	}
}

// Template Tags
if ( ! function_exists( 'seedprod_pro_seed_csp4_title' ) ) {
	function seedprod_pro_seed_csp4_title() {
		$o = seedprod_pro_seed_csp4_get_settings();
		extract( $o );

		$output = '';

		if ( ! empty( $seo_title ) ) {
			$output = esc_html( $seo_title );
		}
		return $output;
	}
}

if ( ! function_exists( 'seedprod_pro_seed_csp4_metadescription' ) ) {
	function seedprod_pro_seed_csp4_metadescription() {
		$o = seedprod_pro_seed_csp4_get_settings();
		extract( $o );

		$output = '';

		if ( ! empty( $seo_description ) ) {
			$output = '<meta name="description" content="' . esc_attr( $seo_description ) . '">';
		}

		return $output;
	}
}

if ( ! function_exists( 'seedprod_pro_seed_csp4_privacy' ) ) {
	function seedprod_pro_seed_csp4_privacy() {
		$output = '';

		if ( get_option( 'blog_public' ) == 0 ) {
			$output = "<meta name='robots' content='noindex,nofollow' />";
		}

		return $output;
	}
}

if ( ! function_exists( 'seedprod_pro_seed_csp4_favicon' ) ) {
	function seedprod_pro_seed_csp4_favicon() {
		$o = seedprod_pro_seed_csp4_get_settings();
		extract( $o );

		$output = '';

		if ( ! empty( $favicon ) ) {
			$output .= "<!-- Favicon -->\n";
			$output .= '<link href="' . esc_attr( $favicon ) . '" rel="shortcut icon" type="image/x-icon" />';
		}

		return $output;
	}
}

if ( ! function_exists( 'seedprod_pro_seed_csp4_customcss' ) ) {
	function seedprod_pro_seed_csp4_customcss() {
		$o = seedprod_pro_seed_csp4_get_settings();
		extract( $o );

		$output = '';

		if ( ! empty( $custom_css ) ) {
			$output = '<style type="text/css">' . esc_html( $custom_css ) . '</style>';
		}

		return $output;
	}
}

if ( ! function_exists( 'seed_csp4_head' ) ) {
	function seedprod_pro_seed_csp4_head() {
		$o = seedprod_pro_seed_csp4_get_settings();
		extract( $o );

		// CSS
		$output = '';

		$output .= "<!-- Bootstrap and default Style -->\n";
		$output .= '<link rel="stylesheet" href="' . SEEDPROD_PRO_PLUGIN_URL . 'app/backwards/themes/default/bootstrap/css/bootstrap.min.css">' . "\n";
		$output .= '<link rel="stylesheet" href="' . SEEDPROD_PRO_PLUGIN_URL . 'app/backwards/themes/default/style.css">' . "\n";
		if ( is_rtl() ) {
			$output .= '<link rel="stylesheet" href="' . SEEDPROD_PRO_PLUGIN_URL . 'app/backwards/themes/default/rtl.css">' . "\n";
		}
		$output .= '<style type="text/css">' . "\n";

		// Calculated Styles

		$output .= '/* calculated styles */' . "\n";
		ob_start(); ?>

	/* Background Style */
	html{
		<?php
		if ( ! empty( $bg_image ) ) :
			;
			?>
			<?php if ( isset( $bg_cover ) && in_array( '1', $bg_cover ) ) : ?>
				background: <?php echo $bg_color; ?> url('<?php echo $bg_image; ?>') no-repeat top center fixed;
				<?php if ( isset( $bg_size ) && $bg_size == 'contain' ) : ?>
				-webkit-background-size: contain;
				-moz-background-size: contain;
				-o-background-size: contain;
				background-size: contain;
				<?php else : ?>

				-webkit-background-size: cover;
				-moz-background-size: cover;
				-o-background-size: cover;
				background-size: cover;
				<?php endif ?>
			<?php else : ?>
				background: <?php echo $bg_color; ?> url('<?php echo $bg_image; ?>') <?php echo $bg_repeat; ?> <?php echo $bg_position; ?> <?php echo $bg_attahcment; ?>;
			<?php endif ?>
			<?php
		else :
			if ( ! empty( $bg_color ) ) :
				?>
			background: <?php echo $bg_color; ?>;
				<?php
		endif;
		endif;
		?>
	}
	.seed-csp4 body{
			<?php if ( ! empty( $bg_effect ) ) : ?>
				background: transparent url('<?php echo plugins_url( 'images/bg-' . $bg_effect . '.png', __FILE__ ); ?>') repeat;
			<?php else : ?>
				background: transparent;
			<?php endif; ?>
	}
		<?php
		if ( ! empty( $bg_overlay ) ) :
			;
			?>
		#seed-csp4-page{
			background-color: rgba(0,0,0,0.5);
		}
	<?php endif ?>

		<?php if ( ! empty( $max_width ) ) : ?>
	#seed-csp4-content{
		max-width: <?php echo intval( $max_width ); ?>px;
	}
	<?php endif; ?>

		<?php if ( ! empty( $enable_well ) ) : ?>
	#seed-csp4-content{
		min-height: 20px;
		padding: 19px;
		background-color: #f5f5f5;
		border: 1px solid #e3e3e3;
		border-radius: 4px;
	}
	<?php endif; ?>

	/* Text Styles */
		<?php if ( ! empty( $text_font ) ) : ?>
		.seed-csp4 body{
			font-family: <?php echo seedprod_pro_seed_csp4_get_font_family( $text_font ); ?>
		}

		.seed-csp4 h1, .seed-csp4 h2, .seed-csp4 h3, .seed-csp4 h4, .seed-csp4 h5, .seed-csp4 h6{
			font-family: <?php echo seedprod_pro_seed_csp4_get_font_family( $text_font ); ?>
		}
	<?php endif; ?>

		<?php if ( ! empty( $text_color ) ) { ?>
		.seed-csp4 body{
			color:<?php echo $text_color; ?>;
		}
	<?php } ?>

		<?php if ( ! empty( $link_color ) ) { ?>
			<?php
			if ( empty( $headline_color ) ) {
				$headline_color = $link_color;
			}
			?>
	<?php } ?>


		<?php if ( ! empty( $headline_color ) ) { ?>
		.seed-csp4 h1, .seed-csp4 h2, .seed-csp4 h3, .seed-csp4 h4, .seed-csp4 h5, .seed-csp4 h6{
			color:<?php echo $headline_color; ?>;
		}
	<?php } ?>


		<?php if ( ! empty( $link_color ) ) { ?>
		.seed-csp4 a, .seed-csp4 a:visited, .seed-csp4 a:hover, .seed-csp4 a:active, .seed-csp4 a:focus{
			color:<?php echo $link_color; ?>;
		}


	<?php } ?>


		<?php
		if ( ! empty( $bg_image ) ) :
			;
			?>
			<?php if ( isset( $bg_cover ) && in_array( '1', $bg_cover ) ) : ?>
	@supports (-webkit-overflow-scrolling: touch) {
		html {
		height: 100%;
		overflow: hidden;
		}
		body
		{
		height:100%;
		overflow: auto;
		-webkit-overflow-scrolling: touch;
		}
	}
		<?php endif; ?>
	<?php endif; ?>

		<?php

		$output .= ob_get_clean();

		$output .= '</style>' . "\n";

		// Javascript
		$output     .= "<!-- JS -->\n";
		$include_url = includes_url();
		$last        = $include_url[ strlen( $include_url ) - 1 ];
		if ( $last != '/' ) {
			$include_url = $include_url . '/';
		}
		if ( empty( $enable_wp_head_footer ) ) {
			$output .= '<script src="' . $include_url . 'js/jquery/jquery.js"></script>' . "\n";
		}
		$output .= '<script src="' . SEEDPROD_PRO_PLUGIN_URL . 'app/backwards/themes/default/bootstrap/js/bootstrap.min.js"></script>' . "\n";

		// Header Scripts
		if ( ! empty( $header_scripts ) ) {
			$output .= "<!-- Header Scripts -->\n";
			$output .= $header_scripts;
		}

		// Google Analytics
		if ( ! empty( $ga_analytics ) ) {
			$output .= "<!-- Google Analytics -->\n";
			$output .= $ga_analytics;
		}

		return $output;
	}
}

if ( ! function_exists( 'seedprod_pro_seed_csp4_get_font_family' ) ) {
	function seedprod_pro_seed_csp4_get_font_family( $font ) {
		$fonts                    = array();
		$fonts['_arial']          = 'Helvetica, Arial, sans-serif';
		$fonts['_arial_black']    = 'Arial Black, Arial Black, Gadget, sans-serif';
		$fonts['_georgia']        = 'Georgia,serif';
		$fonts['_helvetica_neue'] = '"Helvetica Neue", Helvetica, Arial, sans-serif';
		$fonts['_impact']         = 'Charcoal,Impact,sans-serif';
		$fonts['_lucida']         = 'Lucida Grande,Lucida Sans Unicode, sans-serif';
		$fonts['_palatino']       = 'Palatino,Palatino Linotype, Book Antiqua, serif';
		$fonts['_tahoma']         = 'Geneva,Tahoma,sans-serif';
		$fonts['_times']          = 'Times,Times New Roman, serif';
		$fonts['_trebuchet']      = 'Trebuchet MS, sans-serif';
		$fonts['_verdana']        = 'Verdana, Geneva, sans-serif';

		if ( ! empty( $fonts[ $font ] ) ) {
			$font_family = $fonts[ $font ];
		} else {
			$font_family = 'Helvetica Neue, Arial, sans-serif';
		}

		echo $font_family;
	}
}

if ( ! function_exists( 'seedprod_pro_seed_csp4_footer' ) ) {
	function seedprod_pro_seed_csp4_footer() {
		$o = seedprod_pro_seed_csp4_get_settings();
		extract( $o );

		$output = '';

		if ( ! empty( $bg_cover ) ) {
			$output .= '<!--[if lt IE 9]>
		<script>
		jQuery(document).ready(function($){';

			$output .= '$.supersized({';
			$output .= "slides:[ {image : '$bg_image'} ]";
			$output .= '});';

			$output .= '});
		</script>
		<![endif]-->';
		}

		if ( ! empty( $footer_scripts ) ) {
			$output .= "<!-- Footer Scripts -->\n";
			$output .= $footer_scripts;
		}

		return $output;
	}
}

if ( ! function_exists( 'seedprod_pro_seed_csp4_logo' ) ) {
	function seedprod_pro_seed_csp4_logo() {
		$o = seedprod_pro_seed_csp4_get_settings();
		extract( $o );

		$output = '';

		if ( ! empty( $logo ) ) {
			$output .= "<img id='seed-csp4-image' src='" . esc_attr( $logo ) . "'>";
		}

		return $output;
	}
}

if ( ! function_exists( 'seedprod_pro_seed_csp4_headline' ) ) {
	function seedprod_pro_seed_csp4_headline() {
		$o = seedprod_pro_seed_csp4_get_settings();
		extract( $o );

		$output = '';

		if ( ! empty( $headline ) ) {
			$output .= '<h1 id="seed-csp4-headline">' . wp_kses(
				$headline,
				array(
					'a'      => array(
						'href'  => array(),
						'title' => array(),
					),
					'br'     => array(),
					'em'     => array(),
					'strong' => array(),
				)
			) . '</h1>';
		}

		return $output;
	}
}

if ( ! function_exists( 'seedprod_pro_seed_csp4_description' ) ) {
	function seedprod_pro_seed_csp4_description() {
		$o = seedprod_pro_seed_csp4_get_settings();
		extract( $o );

		$output = '';

		if ( ! empty( $description ) ) {
			if ( has_shortcode( $description, 'rafflepress' ) ) {
				$output .= '<div id="seed-csp4-description">' . do_shortcode( shortcode_unautop( wpautop( convert_chars( wptexturize( $description ) ) ) ) ) . '</div>';
			} else {
				$output .= '<div id="seed-csp4-description">' . shortcode_unautop( wpautop( convert_chars( wptexturize( $description ) ) ) ) . '</div>';
			}
		}

		return $output;
	}
}

if ( ! function_exists( 'seed_csp4_append_html' ) ) {
	function seed_csp4_append_html() {
		$o = seedprod_pro_seed_csp4_get_settings();
		extract( $o );

		$output = '';

		if ( ! empty( $append_html ) ) {
			$output .= '<div id="coming-soon-custom-html">' . $append_html . '</div>';
		}

		return $output;
	}
}

if ( ! function_exists( 'seedprod_pro_seed_csp4_credit' ) ) {
	function seedprod_pro_seed_csp4_credit() {
		$o = seedprod_pro_seed_csp4_get_settings();
		extract( $o );

		$output = '';

		if ( ! empty( $footer_credit ) ) {
			$output  = '<div id="seed-csp4-credit">';
			$output .= '<a  target="_blank" href="http://www.seedprod.com/?utm_source=coming-soon-credit-link&utm_medium=banner&utm_campaign=coming-soon-plugin-credit-link"><img style="width:75px" src="' . SEEDPROD_PRO_PLUGIN_URL . 'public/svg/powered-by-logo.svg"></a>';
			$output .= '</div>';
		}

		return $output;
	}
}

if ( ! function_exists( 'seedprod_pro_seed_csp4_get_settings' ) ) {
	function seedprod_pro_seed_csp4_get_settings() {
		$s1 = get_option( 'seed_csp4_settings_content' );
		$s2 = get_option( 'seed_csp4_settings_design' );
		$s3 = get_option( 'seed_csp4_settings_advanced' );

		if ( empty( $s1 ) ) {
			$s1 = array();
		}

		if ( empty( $s2 ) ) {
			$s2 = array();
		}

		if ( empty( $s3 ) ) {
			$s3 = array();
		}

		$settings = $s1 + $s2 + $s3;

		return apply_filters( 'seedprod_pro_seed_csp4_get_settings', $settings );
	}
}


