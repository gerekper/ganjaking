<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * General class to manage the template list table
 *
 * @class   YITH_YWPI_PDF_Template_List
 * @since   4.0.0
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\PDF_Invoice\PDF_Builder\Abstracts
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'YITH_YWPI_PDF_Template_List' ) ) {
	/**
	 * Abstract class
	 */
	abstract class YITH_YWPI_PDF_Template_List {

		/**
		 *  Post type name
		 *
		 * @var string
		 */
		public $post_type = '';

		/**
		 * Metabox is saved
		 *
		 * @var boolean
		 */
		protected $saved_meta_box = false;

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			/* list table */
			add_filter( "manage_{$this->post_type}_posts_columns", array( $this, 'set_custom_columns' ) );
			add_action( "manage_{$this->post_type}_posts_custom_column", array( $this, 'render_custom_columns' ), 10, 2 );
			add_filter( "manage_edit-{$this->post_type}_sortable_columns", array( $this, 'sortable_custom_columns' ) );
			add_filter( 'months_dropdown_results', array( $this, 'remove_date_drowpdown' ), 10, 2 );
			add_filter( "bulk_actions-edit-{$this->post_type}", array( $this, 'customize_bulk_actions' ), 1 );
			add_filter( 'post_row_actions', array( $this, 'customize_row_actions' ), 10, 2 );
			add_filter( 'handle_bulk_actions-edit-' . $this->post_type, array( $this, 'handle_bulk_actions' ), 10, 3 );

			/* post editor */
			add_filter( 'woocommerce_screen_ids', array( $this, 'add_screen_ids' ) );

			global $sitepress;

			if ( ! $sitepress ) {
				add_action( 'admin_menu', array( $this, 'remove_publish_box' ) );
			}

			add_filter( 'admin_body_class', array( $this, 'add_post_edit_body_class' ) );
			add_action( 'edit_form_top', array( $this, 'add_back_button' ) );
			add_filter( 'views_edit-' . $this->post_type, array( $this, 'remove_user_views' ) );

			add_filter( 'post_updated_messages', array( $this, 'change_post_update_message' ) );
			add_filter( 'bulk_post_updated_messages', array( $this, 'change_bulk_post_updated_messages' ), 10, 2 );
		}

		/**
		 * Change the bulk post message
		 *
		 * @param array $messages List of messages.
		 *
		 * @return array
		 */
		public function change_post_update_message( $messages ) {
			return $messages;
		}

		/**
		 * Remove the views form these custom post types.
		 *
		 * @param array $views Views.
		 *
		 * @return array|void
		 */
		public function remove_user_views( $views ) {
			global $post_type;

			if ( $this->post_type === $post_type ) {
				return array();
			}

			return $views;
		}

		/**
		 * Return custom sortable columns.
		 *
		 * @param array $sortables_columns List of columns to sort.
		 *
		 * @return array
		 */
		public function sortable_custom_columns( $sortables_columns ) {
			return $sortables_columns;
		}

		/**
		 * Add custom post type screen to WooCommerce list
		 *
		 * @param array $screen_ids Array of Screen IDs.
		 *
		 * @return  array
		 */
		public function add_screen_ids( $screen_ids ) {
			$screen_ids[] = 'edit-' . $this->post_type;
			$screen_ids[] = $this->post_type;

			return $screen_ids;
		}

		/**
		 * Set custom columns
		 *
		 * @param array $columns Existing columns.
		 *
		 * @return array
		 */
		public function set_custom_columns( $columns ) {
			return $columns;
		}

		/**
		 * Remove the publish metabox
		 *
		 * @return  void
		 */
		public function remove_publish_box() {
			remove_meta_box( 'submitdiv', $this->post_type, 'side' );
		}

		/**
		 * Manage custom columns
		 *
		 * @param string $column Current column.
		 * @param int    $post_id Post ID.
		 *
		 * @return  void
		 */
		public function render_custom_columns( $column, $post_id ) {
		}

		/**
		 * Add a back button at the top of the page
		 *
		 * @param WP_Post $post The Post Object.
		 *
		 * @return  void
		 */
		public function add_back_button( $post ) {
			$getted = $_GET; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			$back_to_list_label = $this->get_back_button_list_label();

			if ( ( isset( $getted['post_type'] ) && $this->post_type === $getted['post_type'] ) || ( $post && $post->post_type === $this->post_type ) ) {
				printf( '<a href="%1$s" class="ywpi_back_button" title="%2$s">< %2$s</a>', esc_url( esc_url( add_query_arg( array( 'post_type' => $this->post_type ), admin_url( 'edit.php' ) ) ) ), esc_html( $back_to_list_label ) );
			}
		}

		/**
		 * Return the back to list button label.
		 *
		 * @return string
		 */
		protected function get_back_button_list_label() {
			return '';
		}

		/**
		 * Add custom body class
		 *
		 * @param string $classes Classes.
		 *
		 * @return  string
		 */
		public function add_post_edit_body_class( $classes ) {
			if ( isset( $_GET['post'] ) && get_post_type( intval( $_GET['post'] ) ) === $this->post_type && isset( $_GET['action'] ) && 'edit' === sanitize_text_field( wp_unslash( $_GET['action'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$classes .= ' ' . $this->post_type . '-edit';
			} elseif ( isset( $_GET['post_type'] ) && sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) === $this->post_type && $this->is_empty_list() ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$classes .= ' yith-empty-list';
			}

			return $classes;
		}

		/**
		 * The function to be called to output the meta box in Earning Rule detail/edit page.
		 *
		 * @param WP_Post $post The Post object.
		 *
		 * @return  void
		 */
		public function option_metabox( $post ) {
		}

		/**
		 * Get the post settings values
		 *
		 * @param integer $post_id The Post ID.
		 *
		 * @return  array
		 */
		public function get_settings_values( $post_id ) {
			$settings = array();

			if ( get_post_type( $post_id ) === $this->post_type ) {
				if ( is_array( $this->options ) ) {
					foreach ( $this->options as $option ) {
						$settings[ $option['id'] ] = get_post_meta( $post_id, $option['id'], true );
					}
				}
			}

			return $settings;
		}

		/**
		 * Remove date dropdown inside the wp list table
		 *
		 * @param object[] $months Array of the months drop-down query results.
		 * @param string   $post_type The post type.
		 *
		 * @return  array
		 */
		public function remove_date_drowpdown( $months, $post_type ) {
			if ( $post_type === $this->post_type ) {
				$months = array();
			}

			return $months;
		}

		/**
		 * Customize the bulk action list.
		 *
		 * @return  array
		 */
		public function customize_bulk_actions() {
			return array();
		}

		/**
		 * Handle bulk actions.
		 *
		 * @param string $redirect_to URL to redirect to.
		 * @param string $action Action name.
		 * @param array  $ids List of ids.
		 *
		 * @return void
		 */
		public function handle_bulk_actions( $redirect_to, $action, $ids ) {
		}

		/**
		 * Customize row actions
		 *
		 * @param array   $actions List of actions.
		 * @param WP_Post $post Post.
		 *
		 * @return  array
		 */
		public function customize_row_actions( $actions, $post ) {
			if ( $post->post_type === $this->post_type ) {
				return array();
			}

			return $actions;
		}

		/**
		 * Change status to the post
		 *
		 * @param int    $post_id Post id.
		 * @param string $status Status ('yes' or 'not').
		 *
		 * @return void
		 */
		public function set_status( $post_id, $status ) {
			update_post_meta( $post_id, '_status', $status );
		}

		/**
		 * Add a metabox
		 *
		 * @return  void
		 */
		public function add_metabox() {
			remove_meta_box( 'slugdiv', $this->post_type, 'normal' );
			add_meta_box( 'yith-ywpi-' . $this->post_type . '-metabox', esc_html__( 'Options', 'yith-woocommerce-pdf-invoice' ), array( $this, 'option_metabox' ), $this->post_type, 'normal', 'default' );
		}

		/**
		 * Check if the table is empty
		 *
		 * @return boolean
		 */
		protected function is_empty_list() {
			$posts = get_posts(
				array(
					'post_type' => $this->post_type,
					'status'    => 'publish',
					'fields'    => 'ids',
				)
			);
			return empty( $posts );
		}

		/**
		 * Render blank state. Extend to add content.
		 */
		protected function render_blank_state() {
			$component         = $this->get_blank_state_params();
			$component['type'] = 'list-table-blank-state';

			yith_plugin_fw_get_component( $component, true );
		}
	}
}
