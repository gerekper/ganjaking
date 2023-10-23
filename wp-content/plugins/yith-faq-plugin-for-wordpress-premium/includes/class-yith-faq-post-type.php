<?php
/**
 * FAQ Post Type class
 *
 * @package YITH\FAQPluginForWordPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'YITH_FAQ_Post_Type' ) ) {

	/**
	 * Main class
	 *
	 * @class   YITH_FAQ_Post_Type
	 * @since   1.0.0
	 * @author  YITH <plugins@yithemes.com>
	 * @package YITH\FAQPluginForWordPress
	 */
	class YITH_FAQ_Post_Type {

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'add_faq_post_type' ) );
			add_filter( 'views_edit-' . YITH_FWP_FAQ_POST_TYPE, array( $this, 'set_views' ), 10, 1 );
			add_filter( 'manage_' . YITH_FWP_FAQ_POST_TYPE . '_posts_columns', array( $this, 'set_custom_columns' ) );
			add_action( 'manage_' . YITH_FWP_FAQ_POST_TYPE . '_posts_custom_column', array( $this, 'render_custom_columns' ), 10, 2 );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 20 );
			add_action( 'wp_ajax_yfwp_enable_switch', array( $this, 'enable_faq' ) );
			add_action( 'wp_ajax_yfwp_order_faqs', array( $this, 'order_faqs' ) );
			add_action( 'admin_init', array( $this, 'refresh_order' ) );
			add_action( 'pre_get_posts', array( $this, 'order_faqs_backend' ) );
			add_filter( 'allowed_block_types_all', array( $this, 'allowed_block_types' ), 10, 2 );
			add_action( 'manage_posts_extra_tablenav', array( $this, 'maybe_render_blank_state' ) );
		}

		/**
		 * Add scripts and styles
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function admin_scripts() {

			$screen = null;

			if ( function_exists( 'get_current_screen' ) ) {
				$screen = get_current_screen();
			}

			if ( ! $screen || YITH_FWP_FAQ_POST_TYPE !== $screen->post_type ) {
				return;
			}

			if ( ! wp_script_is( 'jquery-blockui', 'enqueued' ) ) {
				wp_register_script( 'jquery-blockui', yit_load_css_file( YITH_FWP_ASSETS_URL . '/js/jquery-blockui/jquery.blockUI.js' ), array( 'jquery' ), '2.70', false );
			}

			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'jquery-blockui' );
			wp_enqueue_style( 'yith-plugin-fw-fields' );
			wp_enqueue_style( 'yith-faq-post-type', yit_load_css_file( YITH_FWP_ASSETS_URL . '/css/faq-post-type.css' ), array(), YITH_FWP_VERSION );
			wp_enqueue_script( 'yith-plugin-fw-fields' );
			wp_enqueue_script( 'yith-faq-post-type', yit_load_js_file( YITH_FWP_ASSETS_URL . '/js/faq-post-type.js' ), array( 'jquery', 'jquery-ui-sortable', 'jquery-blockui' ), YITH_FWP_VERSION, false );
			wp_enqueue_style( 'yith-faq-shortcode-panel', yit_load_css_file( YITH_FWP_ASSETS_URL . '/css/admin-panel.css' ), array( 'wp-jquery-ui-dialog' ), YITH_FWP_VERSION );

			$params = array(
				'ajax_url'    => admin_url( 'admin-ajax.php' ),
				'is_order_by' => isset( $_GET['orderby'] ), //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			);

			wp_localize_script( 'yith-faq-post-type', 'yith_faq_post_type', $params );

		}

		/**
		 * Add video post type
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function add_faq_post_type() {

			$labels = array(
				'name'               => esc_html__( 'FAQs', 'yith-faq-plugin-for-wordpress' ),
				'singular_name'      => esc_html__( 'FAQ', 'yith-faq-plugin-for-wordpress' ),
				'add_new_item'       => esc_html__( 'Add new FAQ', 'yith-faq-plugin-for-wordpress' ),
				'add_new'            => esc_html__( 'Add new', 'yith-faq-plugin-for-wordpress' ),
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
				/**
				 * APPLY_FILTERS: yith_faq_rewrite
				 *
				 * Change the slug for rewrite rules.
				 *
				 * @param string $value The slug value.
				 *
				 * @return string
				 */
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
				'show_in_rest'        => true,
			);

			register_post_type( YITH_FWP_FAQ_POST_TYPE, $args );

			/**
			 * APPLY_FILTERS: yith_faq_needs_flushing
			 *
			 * Enable flushing of rewrite rules.
			 *
			 * @param boolean $value Value to enable/disable the flushing.
			 *
			 * @return boolean
			 */
			if ( apply_filters( 'yith_faq_needs_flushing', false ) === true ) {
				flush_rewrite_rules();
			}

		}

		/**
		 * Set custom columns
		 *
		 * @return  array
		 * @since   1.0.0
		 */
		public function set_custom_columns() {

			$columns = array(
				'drag'                              => '',
				'cb'                                => '<input type="checkbox" />',
				'title'                             => esc_html__( 'Title', 'yith-faq-plugin-for-wordpress' ),
				'taxonomy-' . YITH_FWP_FAQ_TAXONOMY => esc_html__( 'Categories', 'yith-faq-plugin-for-wordpress' ),
				'enable'                            => esc_html__( 'Active', 'yith-faq-plugin-for-wordpress' ),
			);

			return $columns;

		}

		/**
		 * Render custom columns
		 *
		 * @param string  $column  Column name.
		 * @param integer $post_id Post ID.
		 *
		 * @return  void
		 * @since   1.0.0
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
				echo '<span class="yith-plugin-ui">';
				wp_nonce_field( 'enable-faq', 'nonce_enable_' . $post_id, false );
				yith_plugin_fw_get_field( $args, true );
				echo '</span>';
			} elseif ( 'drag' === $column ) {
				echo '<i class="yith-icon yith-icon-drag"></i>';
			}

		}

		/**
		 * Filters views in custom post type
		 *
		 * @param array $views Views array.
		 *
		 * @return  array
		 * @since   1.0.0
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
		 * @throws  Exception Wrong nonce exception.
		 * @since   1.0.0
		 */
		public function enable_faq() {

			try {

				if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'enable-faq' ) ) {
					throw new Exception( 'Wrong Nonce!' );
				}

				$faq_id = isset( $_POST['faq_id'] ) ? (int) $_POST['faq_id'] : false;
				$value  = isset( $_POST['enabled'] ) && 'no' !== $_POST['enabled'] ? 'publish' : 'draft';

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
		 */
		public function refresh_order() {
			global $wpdb;

			$result = $wpdb->get_results( //phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$wpdb->prepare(
					"
						SELECT COUNT(*) AS count,
						       MAX( menu_order ) AS max,
						       MIN( menu_order ) AS min
						FROM $wpdb->posts
						WHERE post_type = %s 
						  AND post_status IN( 'publish', 'pending', 'draft', 'private', 'future' )
						  ",
					YITH_FWP_FAQ_POST_TYPE
				)
			);

			if ( 0 === (int) $result[0]->count || $result[0]->count === $result[0]->max ) {
				return;
			}

			$results = $wpdb->get_results( //phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$wpdb->prepare(
					"
						SELECT ID
						FROM $wpdb->posts
						WHERE post_type = %s
						  AND post_status IN( 'publish', 'pending', 'draft', 'private', 'future' ) 
						ORDER BY menu_order ASC
						",
					YITH_FWP_FAQ_POST_TYPE
				)
			);

			foreach ( $results as $key => $result ) {
				$wpdb->update( $wpdb->posts, array( 'menu_order' => $key + 1 ), array( 'ID' => $result->ID ) ); //phpcs:ignore WordPress.DB.DirectDatabaseQuery
			}

		}

		/**
		 * Save FAQs ordering
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function order_faqs() {
			global $wpdb;
			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'bulk-posts' ) ) {
				return;
			}

			$order = isset( $_POST['order'] ) ? sanitize_text_field( wp_unslash( $_POST['order'] ) ) : '';
			parse_str( $order, $data );

			if ( ! is_array( $data ) ) {
				return;
			}

			$id_arr = array();
			foreach ( $data as $values ) {
				foreach ( $values as $id ) {
					$id_arr[] = $id;
				}
			}

			$menu_order_arr = array();
			foreach ( $id_arr as $id ) {
				$results = $wpdb->get_results( "SELECT menu_order FROM {$wpdb->prefix}posts WHERE ID = " . intval( $id ) ); //phpcs:ignore WordPress.DB.DirectDatabaseQuery
				foreach ( $results as $result ) {
					$menu_order_arr[] = $result->menu_order;
				}
			}

			sort( $menu_order_arr );

			foreach ( $data as $values ) {
				foreach ( $values as $position => $id ) {
					$wpdb->update( $wpdb->prefix . 'posts', array( 'menu_order' => $menu_order_arr[ $position ] ), array( 'ID' => intval( $id ) ) ); //phpcs:ignore WordPress.DB.DirectDatabaseQuery
				}
			}
		}

		/**
		 * Set FAQs ordering on backend
		 *
		 * @param WP_Query $wp_query FAQs query.
		 *
		 * @return  void
		 * @since   1.0.0
		 */
		public function order_faqs_backend( $wp_query ) {
			if ( is_admin() ) {
				if ( isset( $wp_query->query['post_type'] ) && ( YITH_FWP_FAQ_POST_TYPE === $wp_query->query['post_type'] ) && ! isset( $_GET['orderby'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$wp_query->set( 'orderby', 'menu_order' );
					$wp_query->set( 'order', 'ASC' );
				}
			}
		}

		/**
		 * Limit block types
		 *
		 * @param bool|array              $allowed_blocks       Array of block type slugs, or boolean to enable/disable all. Default true (all registered block types supported).
		 * @param WP_Block_Editor_Context $block_editor_context The current block editor context.
		 *
		 * @return bool|array
		 * @since  1.9.0
		 */
		public function allowed_block_types( $allowed_blocks, $block_editor_context ) {
			$post = $block_editor_context->post;
			if ( $post && YITH_FWP_FAQ_POST_TYPE === $post->post_type ) {
				$allowed_blocks = apply_filters(
					'yith_faq_allowed_blocks',
					array(
						'core/embed',
						'core/paragraph',
						'core/heading',
						'core/list',
						'core/quote',
						'core/audio',
						'core/video',
						'core/file',
						'core/table',
						'core/verse',
						'core/code',
						'core/preformatted',
						'core/pullquote',
						'core/buttons',
						'core/separator',
						'core/spacer',
						'core/columns',
						'core/image',
						'core/shortcode',
					)
				);
			}

			return $allowed_blocks;
		}

		/**
		 * Show empty template if no button is created
		 *
		 * @param string $which The context position.
		 *
		 * @return  void
		 * @since   2.0.0
		 */
		public function maybe_render_blank_state( $which ) {
			global $post_type;

			if ( YITH_FWP_FAQ_POST_TYPE === $post_type && 'bottom' === $which ) {

				$posts_args = array(
					'numberposts' => -1,
					'post_type'   => YITH_FWP_FAQ_POST_TYPE,
					'post_status' => array( 'publish', 'draft' ),
				);

				$posts = get_posts( $posts_args );

				if ( 0 === count( $posts ) ) {
					$attrs = array(
						'icon'            => YITH_FWP_ASSETS_URL . '/images/empty-preset.svg',
						'message'         => esc_html__( "You don't have any FAQs created yet.", 'yith-faq-plugin-for-wordpress' ),
						'submessage'      => esc_html__( "But don't worry, you can create the first one here!", 'yith-faq-plugin-for-wordpress' ),
						'cta_button_text' => esc_html__( 'Create FAQ', 'yith-faq-plugin-for-wordpress' ),
						'cta_button_href' => esc_url( add_query_arg( array( 'post_type' => YITH_FWP_FAQ_POST_TYPE ), admin_url( 'post-new.php' ) ) ),
					);
					?>
					<div class="yith-plugin-ui">
						<?php
						include YITH_FWP_DIR . 'includes/admin/views/list-table/list-table-blank-state.php';
						?>
					</div>
					<style type="text/css">
						#posts-filter .wp-list-table,
						#posts-filter .tablenav.top,
						#posts-filter .search-box,
						.tablenav.bottom .actions,
						.wrap .subsubsub,
						.wrap .page-title-action {
							display: none !important;
						}

						#posts-filter .tablenav.bottom {
							height: auto;
						}

						.yith-plugin-ui {
							border: 1px solid #ddd;
							background: #ffffff;
						}
					</style>
					<?php
				}
			}
		}

	}

	new YITH_FAQ_Post_Type();

}
