<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );

/**
 * Class GroovyMenuMegaMenuPostType
 */
class GroovyMenuMegaMenuPostType {

	public function __construct() {
		add_action( 'init', array( $this, 'addMegaMenuPostType' ) );
		add_action( 'template_redirect', array( $this, 'pages_redirect' ), 1 );
		add_filter( 'post_updated_messages', array( $this, 'remove_view_link' ) );
		add_filter( 'post_row_actions', array( $this, 'remove_post_row_view_link' ) );
		add_filter( 'custom_menu_order', '__return_true' );
		add_filter( 'menu_order', array( $this, 'custom_menu_order' ) );
		add_filter( 'gutenberg_can_edit_post_type', array( $this, 'gutenberg_can_edit_post_type' ), 10, 2 );
	}

	/**
	 * Register Mega Menu post type
	 */
	public function addMegaMenuPostType() {
		register_post_type( 'gm_menu_block', array(
				'labels'              => array(
					'name'          => __( 'Menu blocks', 'groovy-menu' ),
					'singular_name' => __( 'Menu block', 'groovy-menu' ),
					'add_new'       => __( 'Add New Menu block', 'groovy-menu' ),
					'add_new_item'  => __( 'Add New Menu block', 'groovy-menu' ),
					'edit_item'     => __( 'Edit Menu block', 'groovy-menu' ),
				),
				'public'              => true,
				'show_in_menu'        => current_user_can( 'administrator' ) ? 'groovy_menu_settings' : false,
				'show_in_admin_bar'   => false,
				'show_in_nav_menus'   => true,
				'supports'            => array(
					'title',
					'editor',
					'revisions'
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
			)
		);
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
		if ( ! is_preview() && 'gm_menu_block' === get_post_type() ) {
			wp_safe_redirect( esc_url_raw( home_url() ), 301 );
			exit();
		}
	}

	public function remove_view_link( $messages ) {

		if ( 'gm_menu_block' === get_post_type() && is_array( $messages ) ) {

			foreach ( $messages as $post_type => $post_data ) {

				foreach ( $post_data as $key => $data ) {
					preg_match( '# ?<a(.+)crane_footer=(.+)<\/a>#im', $data, $matches );
					if ( ! empty( $matches[0] ) ) {
						$messages[ $post_type ][ $key ] = str_replace( $matches[0], '', $messages[ $post_type ][ $key ] );
					}
				}

			}

		}

		return $messages;

	}

	public function remove_post_row_view_link( $actions, $post = 0 ) {
		if ( 'gm_menu_block' === get_post_type() ) {
			if ( isset( $actions['view'] ) ) {
				unset( $actions['view'] );
			}
		}

		return $actions;
	}

	public function custom_menu_order( $menu_ord ) {
		if ( ! $menu_ord ) {
			return true;
		}

		if ( in_array( 'groovy_menu_settings', $menu_ord, true ) ) {
			global $submenu;
			if ( isset( $submenu['groovy_menu_settings'] ) ) {
				$old_order = $submenu['groovy_menu_settings'];
				$new_order = array();
				foreach ( $old_order as $position => $item ) {
					if ( isset( $item[2] ) && 'edit.php?post_type=gm_menu_block' === $item[2] ) {
						$new_order[99] = $item;
					} else {
						$new_order[ $position ] = $item;
					}
				}

				ksort( $new_order );

				$submenu['groovy_menu_settings'] = $new_order;

			}
		}

		return $menu_ord;
	}

}


add_action( 'wp_footer', 'groovy_menu_add_vc_custom_css' );

if ( ! function_exists( 'groovy_menu_add_vc_custom_css' ) ) {
	/**
	 * Output custom styles for vc.
	 *
	 * @param null|int $new_id for collect post ids.
	 */
	function groovy_menu_add_vc_custom_css( $new_id = null ) {

		static $post_ids = array();

		if ( ! empty( $new_id ) ) {
			$post_ids[ $new_id ] = $new_id;

			return;
		}

		if ( empty( $post_ids ) ) {
			return;
		}

		foreach ( $post_ids as $post_id ) {
			$post_custom_css = get_post_meta( $post_id, '_wpb_post_custom_css', true );
			if ( ! empty( $post_custom_css ) ) {
				$post_custom_css = strip_tags( $post_custom_css );
				echo '<style type="text/css" data-type="vc_custom-css">';
				echo $post_custom_css;
				echo '</style>';
			}

			$shortcodes_custom_css = get_post_meta( $post_id, '_wpb_shortcodes_custom_css', true );
			if ( ! empty( $shortcodes_custom_css ) ) {
				$shortcodes_custom_css = strip_tags( $shortcodes_custom_css );
				echo '<style type="text/css" data-type="vc_shortcodes-custom-css">';
				echo $shortcodes_custom_css;
				echo '</style>';
			}
		}

	}
}
