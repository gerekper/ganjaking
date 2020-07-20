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
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_FAQ_Post_Type' ) ) {

	/**
	 * Main class
	 *
	 * @class   YITH_FAQ_Post_Type
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 * @package Yithemes
	 */
	class YITH_FAQ_Post_Type {

		/**
		 * @var $post_type string post type name
		 */
		private $post_type = null;

		/**
		 * @var $taxonomy string taxonomy name
		 */
		private $taxonomy = null;

		/**
		 * Constructor
		 *
		 * @param   $post_type string
		 * @param   $taxonomy  string
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct( $post_type, $taxonomy ) {

			$this->post_type = $post_type;
			$this->taxonomy  = $taxonomy;

			add_action( 'init', array( $this, 'add_faq_post_type' ) );
			add_filter( "views_edit-{$this->post_type}", array( $this, 'set_views' ), 10, 1 );
			add_filter( "manage_{$this->post_type}_posts_columns", array( $this, 'set_custom_columns' ) );
			add_action( "manage_{$this->post_type}_posts_custom_column", array( $this, 'render_custom_columns' ), 10, 2 );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 20 );
			add_action( 'wp_ajax_yfwp_enable_switch', array( $this, 'enable_faq' ) );
			add_action( 'wp_ajax_yfwp_order_faqs', array( $this, 'order_faqs' ) );
			add_action( 'admin_init', array( $this, 'refresh_order' ) );
			add_action( 'pre_get_posts', array( $this, 'order_faqs_backend' ) );

		}

		/**
		 * Add scripts and styles
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function admin_scripts() {

			$screen = null;

			if ( function_exists( 'get_current_screen' ) ) {
				$screen = get_current_screen();
			}

			if ( ! $screen || $screen->post_type !== $this->post_type ) {
				return;
			}

			wp_enqueue_style( 'yith-plugin-fw-fields' );
			wp_enqueue_style( 'yith-faq-post-type', yit_load_css_file( YITH_FWP_ASSETS_URL . '/css/faq-post-type.css' ), array(), YITH_FWP_VERSION );
			wp_enqueue_script( 'yith-plugin-fw-fields' );
			wp_enqueue_script( 'yith-faq-post-type', yit_load_js_file( YITH_FWP_ASSETS_URL . '/js/faq-post-type.js' ), array( 'jquery', 'jquery-ui-sortable', 'jquery-blockui' ), YITH_FWP_VERSION );

			$params = array(
				'ajax_url'    => admin_url( 'admin-ajax.php' ),
				'is_order_by' => isset( $_GET['orderby'] ),
			);

			wp_localize_script( 'yith-faq-post-type', 'yith_faq_post_type', $params );

		}

		/**
		 * Add video post type
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_faq_post_type() {

			$labels = array(
				'name'               => esc_html_x( 'FAQs', 'Post Type General Name', 'yith-faq-plugin-for-wordpress' ),
				'singular_name'      => esc_html_x( 'FAQ', 'Post Type Singular Name', 'yith-faq-plugin-for-wordpress' ),
				'add_new_item'       => esc_html__( 'Add New FAQ', 'yith-faq-plugin-for-wordpress' ),
				'add_new'            => esc_html__( 'Add New', 'yith-faq-plugin-for-wordpress' ),
				'new_item'           => esc_html__( 'New FAQ', 'yith-faq-plugin-for-wordpress' ),
				'edit_item'          => esc_html__( 'Edit FAQ', 'yith-faq-plugin-for-wordpress' ),
				'view_item'          => esc_html__( 'View FAQ', 'yith-faq-plugin-for-wordpress' ),
				'search_items'       => esc_html__( 'Search FAQ', 'yith-faq-plugin-for-wordpress' ),
				'not_found'          => esc_html__( 'Not found', 'yith-faq-plugin-for-wordpress' ),
				'not_found_in_trash' => esc_html__( 'Not found in Trash', 'yith-faq-plugin-for-wordpress' ),
			);

			$args = array(
				'public'              => false,
				'publicly_queryable'  => false,
				'show_ui'             => true,
				'query_var'           => false,
				//APPLY_FILTER: yith_faq_rewrite: change the slug for rewrite rules
				'rewrite'             => array( 'slug' => apply_filters( 'yith_faq_rewrite', 'yith_faq' ) ),
				'capability_type'     => 'post',
				'menu_icon'           => 'dashicons-list-view',
				'has_archive'         => true,
				'hierarchical'        => false,
				'menu_position'       => 10,
				'supports'            => array( 'title', 'editor' ),
				'labels'              => $labels,
				'show_in_nav_menus'   => false,
				'exclude_from_search' => true,
			);

			register_post_type( $this->post_type, $args );

			//APPLY_FILTER: yith_faq_needs_flushing: enable flushing of rewrite rules
			if ( apply_filters( 'yith_faq_needs_flushing', false ) === true ) {
				flush_rewrite_rules();
			}

		}

		/**
		 * Set custom columns
		 *
		 * @return  array
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function set_custom_columns() {

			$columns = array(
				'drag'                  => '',
				'cb'                    => '<input type="checkbox" />',
				'title'                 => esc_html__( 'Title', 'yith-faq-plugin-for-wordpress' ),
				'taxonomy-yith_faq_cat' => esc_html__( 'Categories', 'yith-faq-plugin-for-wordpress' ),
				'enable'                => esc_html__( 'Off/On', 'yith-faq-plugin-for-wordpress' ),
				'date'                  => esc_html__( 'Date', 'yith-faq-plugin-for-wordpress' ),
			);

			return $columns;

		}

		/**
		 * Render custom columns
		 *
		 * @param   $column  string
		 * @param   $post_id integer
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function render_custom_columns( $column, $post_id ) {

			if ( 'enable' === $column ) {

				global $post;

				$enabled = 'draft' !== $post->post_status ? 'yes' : 'no';

				$args = array(
					'id'    => 'enable_' . $post_id,
					'name'  => '_enabled_faq',
					'type'  => 'onoff',
					'value' => $enabled,
				);

				yith_plugin_fw_get_field( $args, true );
			} elseif ( 'drag' === $column ) {
				echo '<i class="dashicons-before dashicons-menu-alt2"></i>';
			}

		}

		/**
		 * Filters views in custom post type
		 *
		 * @param   $views array
		 *
		 * @return  array
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function set_views( $views ) {

			if ( isset( $views['mine'] ) ) {

				unset( $views['mine'] );

			}

			return $views;

		}

		/**
		 * Enable/disable faq from post page
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function enable_faq() {

			try {

				$faq_id = $_POST['faq_id'];
				$value  = 'no' !== $_POST['enabled'] ? 'publish' : 'draft';

				wp_update_post(
					array(
						'ID'          => $faq_id,
						'post_status' => $value,
					)
				);

				wp_send_json( array( 'success' => true ) );

			} catch ( Exception $e ) {

				wp_send_json(
					array(
						'success' => false,
						'error'   => $e->getMessage(),
					)
				);

			}

		}

		/**
		 * Refresh FAQs ordering
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function refresh_order() {
			global $wpdb;

			// phpcs:disable
			$result = $wpdb->get_results(
				"SELECT COUNT(*) AS count, 
					MAX(menu_order) AS max,
					MIN(menu_order) AS min 
					FROM $wpdb->posts 
					WHERE post_type = '" . $this->post_type . "' 
					AND post_status IN ('publish', 'pending', 'draft', 'private', 'future')"
			);
			// phpcs:enable

			if ( 0 === (int) $result[0]->count || $result[0]->count === $result[0]->max ) {
				return;
			}

			// phpcs:disable
			$results = $wpdb->get_results(
				"SELECT ID 
					FROM $wpdb->posts 
					WHERE post_type = '" . $this->post_type . "' 
					AND post_status IN ('publish', 'pending', 'draft', 'private', 'future') 
					ORDER BY menu_order ASC"
			);

			// phpcs:enable
			foreach ( $results as $key => $result ) {
				$wpdb->update( $wpdb->posts, array( 'menu_order' => $key + 1 ), array( 'ID' => $result->ID ) );
			}

		}

		/**
		 * Save FAQs ordering
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function order_faqs() {
			global $wpdb;

			parse_str( $_POST['order'], $data );

			if ( ! is_array( $data ) ) {
				return;
			}

			$id_arr = array();
			foreach ( $data as $key => $values ) {
				foreach ( $values as $position => $id ) {
					$id_arr[] = $id;
				}
			}

			$menu_order_arr = array();
			foreach ( $id_arr as $key => $id ) {
				$results = $wpdb->get_results( "SELECT menu_order FROM {$wpdb->prefix}posts WHERE ID = " . intval( $id ) );
				foreach ( $results as $result ) {
					$menu_order_arr[] = $result->menu_order;
				}
			}

			sort( $menu_order_arr );

			foreach ( $data as $key => $values ) {
				foreach ( $values as $position => $id ) {
					$wpdb->update( $wpdb->prefix . 'posts', array( 'menu_order' => $menu_order_arr[ $position ] ), array( 'ID' => intval( $id ) ) );
				}
			}
		}

		/**
		 * Set FAQs ordering on backend
		 *
		 * @param   $wp_query WP_Query
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function order_faqs_backend( $wp_query ) {

			if ( is_admin() ) {

				if ( isset( $wp_query->query['post_type'] ) && ( $wp_query->query['post_type'] === $this->post_type ) && ! isset( $_GET['orderby'] ) ) {
					$wp_query->set( 'orderby', 'menu_order' );
					$wp_query->set( 'order', 'ASC' );
				}
			}

		}

	}

}
