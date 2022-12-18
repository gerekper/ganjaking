<?php
/**
 * Global
 * 
 * @since 6.5.1 WooCommerce booking plugin not working porperly in its archive page.
 */

global $porto_settings;
if ( ! defined( 'ELEMENTOR_VERSION' ) && ! defined( 'WPB_VC_VERSION' ) && ( ! empty( $porto_settings['enable-gfse'] ) && true == $porto_settings['enable-gfse'] ) ) {
	// Add block patterns
	require PORTO_LIB . '/block-patterns.php';

	global $pagenow;
	if ( is_admin() && isset( $pagenow ) && ( 'site-editor.php' == $pagenow || 'post.php' == $pagenow || 'post-new.php' == $pagenow ) ) {
		add_filter( 'block_editor_settings_all', 'porto_gfse_head_assets', 99, 2 );

		// Template Part Area
		add_filter( 'porto_js_admin_vars', 'porto_templatepart_area' );
	}
} else {
	// Disable Block Templates
	remove_theme_support( 'block-templates' );
	add_filter( 'get_block_templates', 'porto_remove_template_block', 99, 3 );

	if ( class_exists( 'WooCommerce' ) ) {
		add_filter( 'woocommerce_has_block_template', 'porto_remove_woocommerce_template', 20, 2 );
	}
	if ( ! defined( 'ELEMENTOR_VERSION' ) && ! defined( 'WPB_VC_VERSION' ) ) {
		add_filter( 'should_load_separate_core_block_assets', '__return_false' );
	}
	if ( is_admin() ) {
		add_filter( 'add_menu_classes', 'porto_remove_template_menu', 20 );
		add_action( 'admin_bar_menu', 'porto_remove_site_edit_menu', 50 );
	}
}

/**
 * Enqueue fonts in iframe head for site-editor.php
 *
 * @since 6.6.0
 */
function porto_gfse_head_assets( $editor_settings, $block_editor_context ) {

	global $porto_settings_optimize;
	$optimized_suffix = '';
	if ( isset( $porto_settings_optimize['optimize_fontawesome'] ) && $porto_settings_optimize['optimize_fontawesome'] ) {
		$optimized_suffix = '_optimized';
	}
	if ( is_rtl() ) {
		wp_register_style( 'porto-plugins', PORTO_URI . '/css/plugins_rtl' . $optimized_suffix . '.css', array(), PORTO_VERSION );
	} else {
		wp_register_style( 'porto-plugins', PORTO_URI . '/css/plugins' . $optimized_suffix . '.css', array(), PORTO_VERSION );
	}
	//Google Fonts
	porto_include_google_font();

	$style_handles = array(
		'porto-plugins',
		'porto-google-fonts',
	);

	// Styles
	ob_start();
	wp_styles()->do_items( $style_handles );
	$editor_settings['__unstableResolvedAssets']['styles'] .= ob_get_clean();

	// Scripts
	wp_register_script( 'porto-admin-gfse', PORTO_JS . '/admin/gutenberg-fse.js', array(), PORTO_VERSION, 'all' );
	ob_start();
	wp_scripts()->do_items( array( 'porto-admin-gfse' ) );
	$editor_settings['__unstableResolvedAssets']['scripts'] .= ob_get_clean();

	return $editor_settings;
}

/**
 * Add Template part type for editor
 *
 * @since 6.6.0
 */
function porto_templatepart_area( $admin_vars ) {
	$admin_vars['gfse_template_area'] = false;
	if ( isset( $_REQUEST['postId'] ) && ( isset( $_REQUEST['postType'] ) && 'wp_template_part' == $_REQUEST['postType'] ) ) {
		$post_slug = explode( '//', $_REQUEST['postId'] );
		if ( is_array( $post_slug ) && ! empty( $post_slug[1] ) && $post_slug[0] ) {
			$result     = porto_get_post_type_items(
				'wp_template_part',
				array(
					'post_name__in'  => array( $post_slug[1] ),
					'posts_per_page' => 1,
					'tax_query'      => array(
						array(
							'taxonomy' => 'wp_theme',
							'field'    => 'slug',
							'terms'    => $post_slug[0],
						),
					),
				),
				false
			);
			$type_terms = get_the_terms( array_key_first( $result ), 'wp_template_part_area' );
			if ( is_array( $type_terms ) ) {
				$admin_vars['gfse_template_area'] = $type_terms[0]->name;
			}
		}
	}
	return $admin_vars;
}

/**
 * Remove Porto block template for Gutenberg Full Site Editing
 *
 * @since 6.5.0
 */
function porto_remove_template_block( $query_result, $query, $template_type ) {
	foreach ( $query_result as $index => $query ) {
		if ( false !== strpos( $query->id, 'porto//' ) ) {
			unset( $query_result[ $index ] );
		}
	}
	return $query_result;
}

/**
 * Remove WooCommerce Html Templates for non Gutenberg Full Site Editing
 *
 * @since 6.5.0
 */
function porto_remove_woocommerce_template( $has_template, $template_name ) {
	if ( 'single-product' == $template_name || 'archive-product' == $template_name || 'taxonomy-product_cat' == $template_name || 'taxonomy-product_tag' == $template_name ) {
		return false;
	}
	return $has_template;
}

if ( is_admin() ) {
	/**
	 * Remove Submenu item - Appearance/Editor
	 *
	 * @since 6.5.0
	 */
	function porto_remove_template_menu( $menu ) {
		global $submenu;
		if ( ! empty( $submenu['themes.php'] ) && ! empty( $submenu['themes.php'][6] ) ) {
			if ( 'site-editor.php' == $submenu['themes.php'][6][2] ) {
				unset( $submenu['themes.php'][6] );
			}
		}
		return $menu;
	}

	/**
	 * Remove Admin Submenu - Edit Site
	 *
	 * @since 6.5.0
	 */
	function porto_remove_site_edit_menu( $wp_admin_bar ) {
		$wp_admin_bar->remove_node( 'site-editor' );
	}
}
