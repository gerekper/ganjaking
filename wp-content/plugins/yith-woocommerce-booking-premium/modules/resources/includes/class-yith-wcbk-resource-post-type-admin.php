<?php
/**
 * Class YITH_WCBK_Resource_Post_Type_Admin
 * Handles the Resource post type on admin side.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Resources
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Resource_Post_Type_Admin' ) ) {
	/**
	 * Class YITH_WCBK_Resource_Post_Type_Admin
	 */
	class YITH_WCBK_Resource_Post_Type_Admin extends YITH_WCBK_Post_Type_Admin {
		/**
		 * The post type.
		 *
		 * @var string
		 */
		protected $post_type = YITH_WCBK_Post_Types::RESOURCE;

		/**
		 * The resource object.
		 *
		 * @var YITH_WCBK_Resource
		 */
		protected $object;

		/**
		 * YITH_WCBK_Post_Type_Admin constructor.
		 */
		protected function __construct() {
			parent::__construct();

			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
			add_action( 'yith_wcbk_post_process_resource_meta', array( $this, 'save_meta' ), 10, 1 );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 20 );
		}

		/**
		 * Return true if you want to use the object. False otherwise.
		 *
		 * @return bool
		 */
		protected function use_object() {
			return true;
		}

		/**
		 * Pre-fetch any data for the row each column has access to it, by loading $this->object.
		 *
		 * @param int $post_id Post ID being shown.
		 */
		protected function prepare_row_data( $post_id ) {
			$this->object = yith_wcbk_get_resource( $post_id );
		}

		/**
		 * Define which columns to show on this screen.
		 *
		 * @param array $columns Existing columns.
		 *
		 * @return array
		 */
		public function define_columns( $columns ) {
			if ( isset( $columns['title'] ) ) {
				unset( $columns['title'] );
			}
			$columns['name'] = __( 'Resource', 'yith-booking-for-woocommerce' );

			return parent::define_columns( $columns );
		}

		/**
		 * Define which columns are sortable.
		 *
		 * @param array $columns Existing columns.
		 *
		 * @return array
		 */
		public function define_sortable_columns( $columns ) {
			$custom = array(
				'name' => 'title',
			);

			return wp_parse_args( $custom, $columns );
		}

		/**
		 * Return the post_type settings placeholder.
		 *
		 * @return array Array of settings: title_placeholder, title_description, updated_messages.
		 */
		protected function get_post_type_settings() {
			return array(
				'title_placeholder' => __( 'Resource name', 'yith-booking-for-woocommerce' ),
				'title_description' => __( 'Enter a name to identify this resource.', 'yith-booking-for-woocommerce' ),
				'updated_messages'  => array(
					1  => __( 'Resource updated.', 'yith-booking-for-woocommerce' ),
					4  => __( 'Resource updated.', 'yith-booking-for-woocommerce' ),
					6  => __( 'Resource created.', 'yith-booking-for-woocommerce' ),
					7  => __( 'Resource saved.', 'yith-booking-for-woocommerce' ),
					8  => __( 'Resource saved.', 'yith-booking-for-woocommerce' ),
					10 => __( 'Resource updated.', 'yith-booking-for-woocommerce' ),
				),
				'hide_views'        => true,
			);
		}

		/**
		 * Retrieve an array of parameters for blank state.
		 *
		 * @return array{
		 * @type string $icon_url The icon URL.
		 * @type string $message  The message to be shown.
		 * @type string $cta      The call-to-action button title.
		 * @type string $cta_icon The call-to-action button icon.
		 * @type string $cta_url  The call-to-action button URL.
		 *                        }
		 */
		protected function get_blank_state_params() {
			return array(
				'icon_url' => YITH_WCBK_ASSETS_URL . '/images/empty-calendar.svg',
				'message'  => __( 'You have no resources yet!', 'yith-booking-for-woocommerce' ),
				'cta'      => array(
					'title' => _x( 'Create resource', 'Button text', 'yith-booking-for-woocommerce' ),
					'url'   => add_query_arg( array( 'post_type' => $this->post_type ), admin_url( 'post-new.php' ) ),
				),
			);
		}

		/**
		 * Add meta boxes.
		 *
		 * @param string $post_type Post type.
		 */
		public function add_meta_boxes( $post_type ) {
			if ( $this->post_type !== $post_type ) {
				return;
			}

			add_meta_box(
				'yith-wcbk-resource-data',
				__( 'Resource data', 'yith-booking-for-woocommerce' ),
				array( $this, 'meta_box_print' ),
				$this->post_type,
				'normal',
				'high'
			);
		}

		/**
		 * Print meta_boxes content
		 *
		 * @param WP_Post $post Post object.
		 */
		public function meta_box_print( $post ) {
			$resource = yith_wcbk_get_resource( $post );

			if ( ! $resource ) {
				return;
			}

			yith_wcbk_get_module_view( 'resources', 'meta-boxes/resource-data.php', compact( 'resource' ) );
		}

		/**
		 * Save meta.
		 *
		 * @param int $post_id The post ID.
		 */
		public function save_meta( $post_id ) {
			// Disable nonce verification notice, since the nonce is already checked!
			// phpcs:disable WordPress.Security.NonceVerification.Missing
			$resource = yith_wcbk_get_resource( $post_id );

			if ( ! $resource ) {
				return;
			}

			$resource->set_props(
				array(
					'available_quantity'   => isset( $_POST['available_quantity'] ) ? absint( $_POST['available_quantity'] ) : null,
					'default_availability' => wc_clean( wp_unslash( $_POST['default_availability'] ?? array() ) ),
					'availability_rules'   => wc_clean( wp_unslash( $_POST['availability_rules'] ?? array() ) ),
					'image_id'             => absint( $_POST['image_id'] ?? 0 ),
				)
			);

			$resource->save();

			// phpcs:enable
		}

		/**
		 * Enqueue scripts.
		 */
		public function enqueue_scripts() {
			if ( $this->is_post_type_edit() ) {
				wp_enqueue_script( 'yith-wcbk-admin-booking-availability-rules' );
				wp_enqueue_script( 'yith-wcbk-admin-booking-settings-sections' );

				wp_enqueue_media();
			}
		}

		/**
		 * Render image column
		 */
		protected function render_name_column() {
			$resource  = $this->object;
			$edit_link = get_edit_post_link( $this->object->get_id() );
			$title     = _draft_or_post_title();

			echo sprintf(
				'<a href="%s">%s <strong>%s</strong></a>',
				esc_url( $edit_link ),
				wp_kses_post( $resource->get_image( 'thumbnail', array(), true ) ),
				esc_html( $title )
			);
		}

		/**
		 * Render Actions column
		 */
		protected function render_actions_column() {
			$resource = $this->object;
			$actions  = yith_plugin_fw_get_default_post_actions( $this->post_id, array( 'delete-directly' => true ) );
			if ( $resource ) {
				$actions = array_merge(
					array(
						'view-calendar' => array(
							'type'   => 'action-button',
							'action' => 'view-calendar',
							'title'  => __( 'View resource calendar', 'yith-booking-for-woocommerce' ),
							'icon'   => 'calendar',
							'url'    => $resource->get_admin_calendar_url(),
						),
					),
					$actions
				);
			}
			yith_plugin_fw_get_action_buttons( $actions, true );
		}
	}
}

return YITH_WCBK_Resource_Post_Type_Admin::instance();
