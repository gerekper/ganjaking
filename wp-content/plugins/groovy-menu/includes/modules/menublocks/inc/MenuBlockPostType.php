<?php

namespace GroovyMenu;

use \GroovyMenuRoleCapabilities as GroovyMenuRoleCapabilities;
use \GroovyMenuUtils as GroovyMenuUtils;

defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Class MenuBlockPostType
 */
class MenuBlockPostType {

	public function __construct() {
		add_action( 'init', array( $this, 'add_menu_block_post_type' ) );
		add_action( 'template_redirect', array( $this, 'pages_redirect' ), 1 );
		add_filter( 'post_row_actions', array( $this, 'remove_post_row_view_link' ) );
		add_filter( 'custom_menu_order', '__return_true' );
		add_filter( 'gutenberg_can_edit_post_type', array( $this, 'gutenberg_can_edit_post_type' ), 10, 2 );
		add_action( 'admin_menu', array( $this, 'change_admin_menu_order' ), 999 );
		add_filter( 'vc_show_button_fe', array( $this, 'remove_vc_edit_button' ), 100, 3 );
	}


	/**
	 * Register gm_menu_block post type
	 */
	public function add_menu_block_post_type() {
		$show_in_menu = false;
		$admin_opt    = is_admin() ? GroovyMenuUtils::check_apr() : false;
		if ( current_user_can( 'administrator' ) ) {
			$show_in_menu = 'groovy_menu_welcome';
		} elseif ( function_exists( 'GroovyMenuRoleCapabilities' ) && GroovyMenuRoleCapabilities::blockRead( true ) ) {
			$show_in_menu = 'groovy_menu_welcome';
		}

		$lic_opt = get_option( GROOVY_MENU_DB_VER_OPTION . '__lic' );
		if ( ! $lic_opt ) {
			$show_in_menu = false;
		}

		$capabilities = array(
			'edit_post'           => 'groovy_menu_edit_block',
			'read_post'           => 'groovy_menu_read_block',
			'delete_post'         => 'groovy_menu_delete_block',
			'delete_posts'        => 'groovy_menu_delete_blocks',
			'delete_others_posts' => 'groovy_menu_delete_others_blocks',
			'edit_posts'          => 'groovy_menu_edit_blocks',
			'edit_others_posts'   => 'groovy_menu_edit_others_blocks',
			'publish_posts'       => 'groovy_menu_publish_blocks',
			'read_private_posts'  => 'groovy_menu_read_private_blocks',
			'create_posts'        => 'groovy_menu_create_block',
		);

		$args = array(
			'labels'              => array(
				'name'          => __( 'Menu blocks', 'groovy-menu' ),
				'singular_name' => __( 'Menu block', 'groovy-menu' ),
				'add_new'       => __( 'Add New Menu block', 'groovy-menu' ),
				'add_new_item'  => __( 'Add New Menu block', 'groovy-menu' ),
				'edit_item'     => __( 'Edit Menu block', 'groovy-menu' ),
			),
			'public'              => true,
			'show_in_menu'        => $show_in_menu,
			'show_in_admin_bar'   => false,
			'show_in_nav_menus'   => true,
			'supports'            => array(
				'title',
				'editor',
				'revisions',
			),
			'publicly_queryable'  => true,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_rest'        => true,
			'rest_base'           => null,
			'menu_position'       => 100,
			'menu_icon'           => null,
			'hierarchical'        => false,
			'taxonomies'          => array(),
			'has_archive'         => false,
			'rewrite'             => false,
			'query_var'           => true,
			'capability_type'     => 'gm_menu_block',
			'capabilities'        => $capabilities,
		);

		register_post_type( 'gm_menu_block', $args );

	}


	/**
	 * Enable the Gutenberg editor for gm_menu_block post type.
	 *
	 * @param bool   $can_edit  Whether to use the Gutenberg editor.
	 * @param string $post_type Name of WordPress post type.
	 *
	 * @return bool  $can_edit
	 */
	public function gutenberg_can_edit_post_type( $can_edit, $post_type ) {
		return 'gm_menu_block' === $post_type ? true : $can_edit;
	}


	public function pages_redirect() {
		$do_redirect = false;

		if ( ! empty( $_GET['gm_menu_block'] ) && ! GroovyMenuUtils::check_wp_builders() ) { // @codingStandardsIgnoreLine
			$do_redirect = true;
		}

		if ( ! is_preview() && 'gm_menu_block' === get_post_type() && ! GroovyMenuUtils::check_wp_builders() ) {
			$do_redirect = true;
		}

		if ( is_preview() ) {
			$do_redirect = false;
		}

		if ( $do_redirect ) {
			wp_safe_redirect( esc_url_raw( home_url() ), 301 );
			exit();
		}
	}

	public function remove_post_row_view_link( $actions, $post = 0 ) {
		if ( 'gm_menu_block' === get_post_type() ) {
			if ( isset( $actions['view'] ) ) {
				unset( $actions['view'] );
			}
		}

		return $actions;
	}

	/**
	 * Custom order for sub items of groovy_menu_welcome
	 *
	 */
	public function change_admin_menu_order() {
		global $menu;
		global $submenu;

		// check empty.
		if ( empty( $submenu['groovy_menu_welcome'] ) || ! is_array( $submenu['groovy_menu_welcome'] ) ) {
			return;
		}

		$gm_submenus = $submenu['groovy_menu_welcome'];

		$correct_order = array(
			'groovy_menu_welcome'              => array(),
			'groovy_menu_settings'             => array(),
			'edit.php?post_type=gm_menu_block' => array(),
			'groovy_menu_menus'                => array(),
		);

		// do new order.
		foreach ( $correct_order as $item_slug => $item_order ) {
			foreach ( $gm_submenus as $index => $gm_submenu ) {
				if ( ! empty( $correct_order[ $item_slug ] ) ) {
					continue;
				}
				if ( ! empty( $gm_submenu[2] ) && $item_slug === $gm_submenu[2] ) {
					$correct_order[ $item_slug ] = $gm_submenu;
				}
			}
		}

		// add missing items.
		foreach ( $gm_submenus as $index => $gm_submenu ) {
			if ( ! empty( $gm_submenu[2] ) && empty( $correct_order[ $gm_submenu[2] ] ) ) {
				$correct_order[ $gm_submenu[2] ] = $gm_submenu;
			}
		}

		// clear empty items.
		foreach ( $correct_order as $index => $item ) {
			if ( empty( $item ) ) {
				unset( $correct_order[ $index ] );
			}
		}


		$submenu['groovy_menu_welcome'] = $correct_order;
	}


	/**
	 * Remove link to WPBakery Frontend Editor
	 *
	 * @param $result
	 * @param $post_id
	 * @param $type
	 *
	 * @return bool
	 */
	public function remove_vc_edit_button( $result, $post_id, $type ) {

		if ( ! empty( $type ) && 'gm_menu_block' === $type ) {
			$result = false;
		}

		return $result;
	}

}
