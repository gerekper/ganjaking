<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<!--[if IE]><meta http-equiv='X-UA-Compatible' content='IE=edge,chrome=1'><![endif]-->
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
	<link rel="profile" href="https://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php
	global $porto_settings;
	wp_reset_postdata();
	$use_theme = false;
if ( have_posts() ) :
	the_post();
	$terms     = wp_get_post_terms( get_the_ID(), 'porto_builder_type', array( 'fields' => 'names' ) );
	$is_header = isset( $terms[0] ) && 'header' == $terms[0];
	$is_footer = isset( $terms[0] ) && 'footer' == $terms[0];
	$use_theme = isset( $terms[0] ) && ( 'product' == $terms[0] || 'shop' == $terms[0] || 'archive' == $terms[0] || 'single' == $terms[0] );
	$container = get_post_meta( get_the_ID(), 'container', true );
	if ( ! $container ) {
		if ( $is_header ) {
			$container = 'wide' == $porto_settings['header-wrapper'] ? 'fluid' : '';
		}
	}

	if ( ! empty( $terms[0] ) && 'shop' == $terms[0] ) {
		// fix style compatibility issues
		echo '<style>';
		echo '.main-content-wrap:not(.opened) .main-content { margin: 0 !important; }';
		echo '</style>';
		if ( ! empty( $porto_settings['product-archive-filter-layout'] ) && 'default' != $porto_settings['product-archive-filter-layout'] ) {
			wp_enqueue_style( 'porto-shop-filter', PORTO_CSS . '/theme/shop/shop-filter/' . $porto_settings['product-archive-filter-layout'] . ( is_rtl() ? '_rtl' : '' ) . '.css', false, PORTO_VERSION, 'all' );
		}
	}

	if ( $use_theme ) {
		get_template_part( 'header/header_before' );
	}

	if ( $is_header ) {
		$is_side = get_post_meta( get_the_ID(), 'header_type', true );
		echo '<header id="header" class="header-builder header-builder-p' . ( $is_side ? ' header-side' : '' ) . '">';
	} elseif ( $is_footer ) {
		echo '<footer id="footer" class="footer footer-builder">';
	}

	if ( $use_theme ) {
		the_content();
	} else {
		echo '<div class="page-wrapper">';
		if ( 'fluid' == $container ) {
			echo '<div class="container-fluid">';
		} elseif ( $container ) {
			echo '<div class="container">';
		}
		if ( ! empty( $terms[0] ) && 'type' == $terms[0] ) {
			$content_type       = get_post_meta( get_the_ID(), 'content_type', true );
			$content_type_value = $content_type ? get_post_meta( get_the_ID(), 'content_type_' . $content_type, true ) : '';
			$preview_width      = get_post_meta( get_the_ID(), 'preview_width', true );
			if ( ! $preview_width ) {
				$preview_width = 360;
			}
			echo '<div class="porto-tb-preview mx-auto" style="margin-top: 3rem; max-width: ' . absint( $preview_width ) . 'px">';

			if ( function_exists( 'porto_shortcode_template' ) && ( $template = porto_shortcode_template( 'porto_posts_grid' ) ) ) {
				$atts = array(
					'builder_id' => get_the_ID(),
					'count'      => 1,
					'columns'    => 1,
				);
				if ( 'term' == $content_type ) {
					$atts['source'] = 'terms';
					$atts['tax']    = $content_type_value;
				} else {
					$atts['post_type'] = $content_type;
					if ( $content_type_value ) {
						$atts['ids'] = array( (int) $content_type_value );
					}
				}
				include $template;
			}

			echo '</div>';
		} else {
			the_content();
		}
		if ( $container ) {
			echo '</div>';
		}
		if ( $is_header ) {
			echo '</header>';
		} elseif ( $is_footer ) {
			echo '</footer>';
		}
		echo '</div>';
	}
endif;

if ( $use_theme ) {
	get_footer();
} else {
	if ( $is_header ) {
		if ( isset( $porto_settings['mobile-panel-type'] ) && 'side' === $porto_settings['mobile-panel-type'] ) {
			// navigation panel
			get_template_part( 'panel' );
		}
	}
	wp_footer();
	echo '</body></html>';
}
