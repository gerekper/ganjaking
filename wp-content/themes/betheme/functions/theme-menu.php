<?php
/**
 * Menu functions.
 *
 * @package Betheme
 * @author Muffin group
 * @link http://muffingroup.com
 */

/**
 * Registers a menu location to use with navigation menus.
 */

register_nav_menu( 'main-menu', __( 'Main Menu | depth 5 (Overlay | depth 1)', 'mfn-opts' ) );
register_nav_menu( 'secondary-menu', __( 'Secondary Menu | depth 2 (Split | depth 5)', 'mfn-opts' ) );
register_nav_menu( 'lang-menu', __( 'Languages Menu | depth 1', 'mfn-opts' ) );
register_nav_menu( 'social-menu', __( 'Social Menu Top | depth 1', 'mfn-opts' ) );
register_nav_menu( 'social-menu-bottom', __( 'Social Menu Bottom | depth 1', 'mfn-opts' ) );

/**
 * Main Menu
 */

if( ! function_exists( 'mfn_wp_nav_menu' ) )
{
	function mfn_wp_nav_menu()
	{
		$args = array(
			'container' => false,
			'menu_class' => 'menu menu-main',
			'link_before' => '<span>',
			'link_after' => '</span>',
			'depth' => 5,
			'fallback_cb' => 'mfn_wp_page_menu',
		);

		// split menu

		if( in_array(mfn_header_style(), array( 'header-split', 'header-split header-semi', 'header-below header-split' )) ){
			mfn_wp_split_menu();
			return true;
		}

		// mega menu | custom walker

		$theme_disable = mfn_opts_get( 'theme-disable' );
		if( ! isset( $theme_disable[ 'mega-menu' ] ) ){
			$args['walker']		= new Walker_Nav_Menu_Mfn;
		}

		// custom menu

		if( mfn_ID() && is_single() && get_post_type() == 'post' && $custom_menu = mfn_opts_get( 'blog-single-menu' ) ){

			// theme options | single posts
			$args['menu'] = $custom_menu;

		} elseif( mfn_ID() && is_single() && get_post_type() == 'portfolio' && $custom_menu = mfn_opts_get( 'portfolio-single-menu' ) ){

			// theme options | single portfolio
			$args['menu'] = $custom_menu;

		} elseif( $custom_menu = get_post_meta( mfn_ID(), 'mfn-post-menu', true ) ){

			// page options | page
			$args['menu'] = $custom_menu;

		} else {

			// default
			$args['theme_location'] = 'main-menu';

		}

		echo '<nav id="menu">';

			// main menu
			wp_nav_menu( $args );

			// custom mobile menu
			mfn_wp_mobile_menu();

		echo '</nav>';

	}
}

if( ! function_exists( 'mfn_wp_page_menu' ) )
{
	function mfn_wp_page_menu()
	{
		$args = array(
			'title_li' => false,
			'sort_column'	=> 'menu_order',
			'depth' => 5,
		);

		echo '<ul class="menu page-menu">';
			wp_list_pages( $args );
		echo '</ul>';
	}
}

/**
 * Mobile Menu
 */

if( ! function_exists( 'mfn_wp_mobile_menu' ) )
{
	function mfn_wp_mobile_menu()
	{
		if( $menu_ID = mfn_opts_get( 'mobile-menu' ) ){

			$args = array(
				'container' => false,
				'menu_class' => 'menu menu-mobile',
				'link_before'	=> '<span>',
				'link_after' => '</span>',
				'depth' => 5,

				'menu' => intval( $menu_ID ),
			);

			wp_nav_menu( $args );
		}
	}
}

/**
 * Split Menu
 */

if( ! function_exists( 'mfn_wp_split_menu' ) )
{
	function mfn_wp_split_menu()
	{
		echo '<nav id="menu">';

			// main menu left

			$args = array(
				'container' => false,
				'menu_class' => 'menu menu-main menu_left',
				'link_before' => '<span>',
				'link_after' => '</span>',
				'theme_location' => 'main-menu',
				'depth' => 5,
				'fallback_cb' => false,
			);

			// custom walker for mega menu

			$theme_disable = mfn_opts_get( 'theme-disable' );
			if( ! isset( $theme_disable[ 'mega-menu' ] ) ){
				$args['walker']	= new Walker_Nav_Menu_Mfn;
			}

			wp_nav_menu( $args );

			// main menu right

			$args = array(
				'container' => false,
				'menu_class' => 'menu menu-main menu_right',
				'link_before' => '<span>',
				'link_after' => '</span>',
				'theme_location' => 'secondary-menu',
				'depth' => 5,
				'fallback_cb' => false,
			);

			// custom walker for mega menu

			$theme_disable = mfn_opts_get( 'theme-disable' );
			if( ! isset( $theme_disable[ 'mega-menu' ] ) ){
				$args['walker']	= new Walker_Nav_Menu_Mfn;
			}

			wp_nav_menu( $args );

			// custom mobile menu

			mfn_wp_mobile_menu();

		echo '</nav>';
	}
}

/**
 * Overlay menu
 */

if( ! function_exists( 'mfn_wp_overlay_menu' ) )
{
	function mfn_wp_overlay_menu()
	{
		$args = array(
			'container' => false,
			'menu_class' => 'menu overlay-menu',
			'theme_location' => 'main-menu',
			'depth' => 1,
			'fallback_cb' => false,
		);

		// custom menu

		if( mfn_ID() && is_single() && get_post_type() == 'post' && $custom_menu = mfn_opts_get( 'blog-single-menu' ) ){

			// theme options | single posts
			$args['menu'] = $custom_menu;

		} elseif( mfn_ID() && is_single() && get_post_type() == 'portfolio' && $custom_menu = mfn_opts_get( 'portfolio-single-menu' ) ){

			// theme options | single portfolio
			$args['menu'] = $custom_menu;

		} elseif( $custom_menu = get_post_meta( mfn_ID(), 'mfn-post-menu', true ) ){

			// page options | page
			$args['menu'] = $custom_menu;

		} else {

			// default
			$args['theme_location'] = 'main-menu';

		}

		echo '<nav id="overlay-menu">';

			// main menu
			wp_nav_menu( $args );

			// custom mobile menu
			mfn_wp_mobile_menu();

		echo '</nav>';
	}
}

/**
 * Secondary menu
 */

if( ! function_exists( 'mfn_wp_secondary_menu' ) )
{
	function mfn_wp_secondary_menu()
	{
		$args = array(
			'container' => 'nav',
			'container_id' => 'secondary-menu',
			'menu_class' => 'secondary-menu',
			'theme_location' => 'secondary-menu',
			'depth' => 2,
			'fallback_cb' => false,
		);

		// split menu

		if( in_array(mfn_header_style(), array( 'header-split', 'header-split header-semi', 'header-below header-split' )) ){
			return false;
		}

		wp_nav_menu( $args );
	}
}

/**
 * Languages menu
 */

if( ! function_exists( 'mfn_wp_lang_menu' ) )
{
	function mfn_wp_lang_menu()
	{
		$args = array(
			'container' => false,
			'fallback_cb' => false,
			'menu_class' => 'wpml-lang-dropdown',
			'theme_location' => 'lang-menu',
			'depth' => 1,
		);
		wp_nav_menu( $args );
	}
}

/**
 * Social menu
 */

if( ! function_exists( 'mfn_wp_social_menu' ) )
{
	function mfn_wp_social_menu()
	{
		$args = array(
			'container' => 'nav',
			'container_id' => 'social-menu',
			'menu_class' => 'social-menu',
			'theme_location' => 'social-menu',
			'depth' => 1,
			'fallback_cb' => false,
		);
		wp_nav_menu( $args );
	}
}

if( ! function_exists( 'mfn_wp_social_menu_bottom' ) )
{
	function mfn_wp_social_menu_bottom()
	{
		$args = array(
			'container' => 'nav',
			'container_id' => 'social-menu',
			'menu_class' => 'social-menu',
			'theme_location' => 'social-menu-bottom',
			'depth' => 1,
			'fallback_cb' => false,
		);
		wp_nav_menu( $args );
	}
}
