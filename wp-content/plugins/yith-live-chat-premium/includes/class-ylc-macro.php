<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'YLC_Macro' ) ) {

	/**
	 * Macro class
	 *
	 * @class   YLC_Macro
	 * @package Yithemes
	 * @since   1.1.3
	 * @author  Your Inspiration Themes
	 *
	 */
	class YLC_Macro {

		/**
		 * @var $post_type string post type name
		 */
		protected $post_type = 'ylc-macro';

		/**
		 * Constructor
		 *
		 * @since   1.1.3
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			add_action( 'admin_init', array( $this, 'add_capabilities' ) );
			add_action( 'init', array( $this, 'add_ylc_macro_post_type' ) );
			add_filter( "manage_{$this->post_type}_posts_columns", array( $this, 'set_custom_columns' ) );
			add_action( "manage_{$this->post_type}_posts_custom_column", array( $this, 'render_custom_columns' ) );
			add_filter( 'ylc_macro_options', array( $this, 'get_macros' ) );
			add_filter( 'tiny_mce_before_init', array( $this, 'customize_tinymce' ) );
			add_filter( 'wp_editor_settings', array( $this, 'customize_editor' ) );
			add_filter( 'quicktags_settings', array( $this, 'customize_quicktags' ) );
			add_filter( 'post_row_actions', array( $this, 'row_actions' ), 100, 2 );

		}

		/**
		 * Add ylc-macro post type
		 *
		 * @since   1.1.3
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function add_ylc_macro_post_type() {

			$labels = array(
				'name'               => _x( 'Chat Macros', 'Post Type General Name', 'yith-live-chat' ),
				'singular_name'      => _x( 'Chat Macro', 'Post Type Singular Name', 'yith-live-chat' ),
				'add_new_item'       => esc_html__( 'Add New Chat Macro', 'yith-live-chat' ),
				'add_new'            => esc_html__( 'Add Chat Macro', 'yith-live-chat' ),
				'new_item'           => esc_html__( 'New Chat Macro', 'yith-live-chat' ),
				'edit_item'          => esc_html__( 'Edit Chat Macro', 'yith-live-chat' ),
				'view_item'          => esc_html__( 'View Chat Macro', 'yith-live-chat' ),
				'search_items'       => esc_html__( 'Search Chat Macro', 'yith-live-chat' ),
				'not_found'          => esc_html__( 'Not found', 'yith-live-chat' ),
				'not_found_in_trash' => esc_html__( 'Not found in Trash', 'yith-live-chat' ),
			);

			$args = array(
				'labels'              => $labels,
				'supports'            => array( 'title', 'editor' ),
				'hierarchical'        => false,
				'public'              => false,
				'show_ui'             => true,
				'menu_position'       => 10,
				'show_in_menu'        => false,
				'show_in_nav_menus'   => false,
				'show_in_admin_bar'   => false,
				'has_archive'         => true,
				'exclude_from_search' => true,
				'menu_icon'           => 'dashicons-awards',
				'capability_type'     => 'ylc-macro',
				'map_meta_cap'        => true,
				'rewrite'             => false,
				'publicly_queryable'  => false,
				'query_var'           => false,
			);

			register_post_type( $this->post_type, $args );
		}

		/**
		 * Add management capabilities to Admin and Shop Manager
		 *
		 * @since   1.1.3
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function add_capabilities() {

			global $wp_roles;

			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}

			$caps  = $this->get_capabilities();
			$roles = $wp_roles->get_names();

			foreach ( $roles as $role_slug => $rolename ) {
				$role = get_role( $role_slug );

				foreach ( $caps as $key => $cap ) {
					$role->remove_cap( $cap );
				}

				if ( $role->has_cap( 'answer_chat' ) ) {
					foreach ( $caps as $key => $cap ) {
						$role->add_cap( $cap );
					}
				}

			}

		}

		/**
		 * Get capabilities for custom post type
		 *
		 * @since   1.1.3
		 * @return  array
		 * @author  Alberto Ruggiero
		 */
		public function get_capabilities() {

			$capability_type = 'ylc-macro';

			return array(
				'edit_post'              => "edit_{$capability_type}",
				'read_post'              => "read_{$capability_type}",
				'delete_post'            => "delete_{$capability_type}",
				'edit_posts'             => "edit_{$capability_type}s",
				'edit_others_posts'      => "edit_others_{$capability_type}s",
				'publish_posts'          => "publish_{$capability_type}s",
				'read_private_posts'     => "read_private_{$capability_type}s",
				'delete_posts'           => "delete_{$capability_type}s",
				'delete_private_posts'   => "delete_private_{$capability_type}s",
				'delete_published_posts' => "delete_published_{$capability_type}s",
				'delete_others_posts'    => "delete_others_{$capability_type}s",
				'edit_private_posts'     => "edit_private_{$capability_type}s",
				'edit_published_posts'   => "edit_published_{$capability_type}s",
				'create_posts'           => "edit_{$capability_type}s",
				'manage_posts'           => "manage_{$capability_type}s",
			);

		}

		/**
		 * Set custom columns
		 *
		 * @since   1.1.3
		 *
		 * @param   $columns array
		 *
		 * @return  array
		 * @author  Alberto Ruggiero
		 */
		public function set_custom_columns( $columns ) {

			$columns['name']        = esc_html__( 'Name', 'yith-live-chat' );
			$columns['description'] = esc_html__( 'Description', 'yith-live-chat' );
			unset( $columns['title'] );
			unset( $columns['date'] );

			return $columns;

		}

		/**
		 * Render custom columns
		 *
		 * @since   1.1.3
		 *
		 * @param   $column string
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function render_custom_columns( $column ) {

			global $post;

			switch ( $column ) {

				case 'description':
					echo get_post_field( 'post_content', $post->ID );
					break;
				case 'name':

					$edit_link = get_edit_post_link( $post->ID );
					$title     = get_the_title( $post->ID );

					echo '<strong><a class="row-title" href="' . esc_url( $edit_link ) . '">' . esc_html( $title ) . '</a>';
					_post_states( $post );
					echo '</strong>';

					break;
			}

		}

		/**
		 * Customize row actions
		 *
		 * @since   1.3.0
		 *
		 * @param   $actions array
		 * @param   $post    WP_Post
		 *
		 * @return  array
		 * @author  Alberto Ruggiero
		 */
		public function row_actions( $actions, $post ) {

			if ( $this->post_type === $post->post_type ) {

				if ( isset( $actions['inline hide-if-no-js'] ) ) {
					unset( $actions['inline hide-if-no-js'] );
				}

			}

			return $actions;
		}

		/**
		 * Get macros
		 *
		 * @since   1.1.3
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function get_macros() {

			$opts = array();

			$args = array(
				'post_type'      => $this->post_type,
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
			);

			$query = new WP_Query( $args );

			if ( $query->have_posts() ) {

				while ( $query->have_posts() ) {

					$query->the_post();

					$opts[] = '<option value="' . esc_attr( $query->post->post_content ) . '">' . $query->post->post_title . '</option>';

				}

			}

			wp_reset_query();
			wp_reset_postdata();

			if ( ! empty( $opts ) ) {
				return implode( '', $opts );
			} else {
				return '';
			}


		}

		/**
		 * Customize Macro Editor
		 *
		 * @since   1.2.5
		 *
		 * @param   $settings array
		 *
		 * @return  array
		 * @author  Alberto Ruggiero
		 */
		public function customize_editor( $settings ) {

			$screen = null;

			if ( function_exists( 'get_current_screen' ) ) {
				$screen = get_current_screen();
			}

			if ( $screen && $screen->id == $this->post_type ) {

				$settings = array(
					'media_buttons' => false,
					'quicktags'     => false,
					'tinymce'       => false
				);

			}

			return $settings;

		}

		/**
		 * Customize TinyMCE
		 *
		 * @since   1.1.3
		 *
		 * @param   $in array
		 *
		 * @return  array
		 * @author  Alberto Ruggiero
		 */
		public function customize_tinymce( $in ) {

			$screen = null;

			if ( function_exists( 'get_current_screen' ) ) {
				$screen = get_current_screen();
			}

			if ( $screen && $screen->id == $this->post_type ) {

				//$in['toolbar1'] = 'bold,italic,strikethrough,bullist,numlist,blockquote,hr,alignleft,aligncenter,alignright,link,unlink,wp_more,spellchecker,wp_fullscreen,wp_adv ';
				//$in['toolbar1'] = 'bold,italic,strikethrough,wp_fullscreen ';
				$in['toolbar1'] = 'wp_fullscreen';
				$in['toolbar2'] = '';

			}

			return $in;

		}

		/**
		 * Customize Quicktags
		 *
		 * @since   1.1.3
		 *
		 * @param   $qtInit array
		 *
		 * @return  array
		 * @author  Alberto Ruggiero
		 */
		public function customize_quicktags( $qtInit ) {

			$screen = null;

			if ( function_exists( 'get_current_screen' ) ) {
				$screen = get_current_screen();
			}

			if ( $screen && $screen->id == $this->post_type ) {

				//$qtInit['buttons'] = 'strong,em,link,block,del,ins,img,ul,ol,li,code,more,close,dfw';
				//$qtInit['buttons'] = 'strong,em,del,close';
				$qtInit['buttons'] = 'dfw';

			}

			return $qtInit;

		}

	}

	new YLC_Macro();

}

