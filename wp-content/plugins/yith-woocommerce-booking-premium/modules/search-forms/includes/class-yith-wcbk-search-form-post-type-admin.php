<?php
/**
 * Class YITH_WCBK_Search_Form_Post_Type_Admin
 * Handle Search Form CPT.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\SearchForms
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Search_Form_Post_Type_Admin' ) ) {
	/**
	 * Class YITH_WCBK_Search_Form_Post_Type_Admin
	 */
	class YITH_WCBK_Search_Form_Post_Type_Admin extends YITH_WCBK_Post_Type_Admin {

		/**
		 * The post type.
		 *
		 * @var string
		 */
		protected $post_type = YITH_WCBK_Post_Types::SEARCH_FORM;

		/**
		 * Singleton implementation
		 *
		 * @return YITH_WCBK_Search_Form_Post_Type_Admin
		 * @deprecated 3.0.0 | use YITH_WCBK_Search_Form_Post_Type_Admin::instance instead.
		 */
		public static function get_instance() {
			return self::instance();
		}

		/**
		 * YITH_WCBK_Search_Form_Post_Type_Admin constructor.
		 */
		protected function __construct() {
			parent::__construct();

			add_action( 'init', array( $this, 'add_plugin_fw_meta_boxes' ) );
			add_action( 'yith_plugin_fw_metabox_before_render_yith-wcbk-search-form-options', array( $this, 'print_meta_box_title' ) );
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
			add_action( 'save_post', array( $this, 'save' ), 10, 1 );
		}

		/**
		 * Return the post_type settings placeholder.
		 *
		 * @return array Array of settings: title_placeholder, title_description, updated_messages.
		 */
		protected function get_post_type_settings() {
			return array(
				'title_placeholder' => __( 'Search form name', 'yith-booking-for-woocommerce' ),
				'title_description' => __( 'Enter a name to identify this search form.', 'yith-booking-for-woocommerce' ),
				'updated_messages'  => array(
					1  => __( 'Search form updated.', 'yith-booking-for-woocommerce' ),
					4  => __( 'Search form updated.', 'yith-booking-for-woocommerce' ),
					6  => __( 'Search form created.', 'yith-booking-for-woocommerce' ),
					7  => __( 'Search form saved.', 'yith-booking-for-woocommerce' ),
					8  => __( 'Search form submitted.', 'yith-booking-for-woocommerce' ),
					10 => __( 'Search form draft updated.', 'yith-booking-for-woocommerce' ),
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
		 *              }
		 */
		protected function get_blank_state_params() {
			return array(
				'icon'    => 'magnifier',
				'message' => __( 'You have no search form yet!', 'yith-booking-for-woocommerce' ),
				'cta'     => array(
					'title' => _x( 'Create search form', 'Button text', 'yith-booking-for-woocommerce' ),
					'url'   => add_query_arg( array( 'post_type' => YITH_WCBK_Post_Types::SEARCH_FORM ), admin_url( 'post-new.php' ) ),
				),
			);
		}

		/**
		 * Define which columns to show on this screen.
		 *
		 * @param array $columns Existing columns.
		 *
		 * @return array
		 */
		public function define_columns( $columns ) {
			if ( isset( $columns['date'] ) ) {
				unset( $columns['date'] );
			}

			$columns['title']     = __( 'Form name', 'yith-booking-for-woocommerce' );
			$columns['shortcode'] = __( 'Shortcode', 'yith-booking-for-woocommerce' );
			$columns['actions']   = __( 'Actions', 'yith-booking-for-woocommerce' );

			return $columns;
		}

		/**
		 * Render Actions column
		 */
		protected function render_shortcode_column() {
			$post_id   = $this->post_id;
			$shortcode = "[booking_search_form id={$post_id}]";
			yith_plugin_fw_copy_to_clipboard( $shortcode );
		}

		/**
		 * Add meta boxes to edit booking page
		 *
		 * @param string $post_type Post type.
		 *
		 * @since  1.0.0
		 */
		public function add_meta_boxes( $post_type ) {
			add_meta_box(
				'yith-wcbk-search-form-metabox',
				__( 'Search form', 'yith-booking-for-woocommerce' ),
				array( $this, 'print_search_form_metabox' ),
				YITH_WCBK_Post_Types::SEARCH_FORM,
				'normal',
				'high'
			);
		}

		/**
		 * Add the Style meta-box through Plugin-FW
		 *
		 * @deprecated 3.0.0 | use YITH_WCBK_Search_Form_Post_Type_Admin::add_plugin_fw_meta_boxes instead
		 */
		public function add_style_metabox() {
			$this->add_plugin_fw_meta_boxes();
		}

		/**
		 * Add the Style meta-box through Plugin-FW
		 */
		public function add_plugin_fw_meta_boxes() {
			$args = array(
				'label'    => __( 'Options', 'yith-booking-for-woocommerce' ),
				'pages'    => YITH_WCBK_Post_Types::SEARCH_FORM,
				'context'  => 'normal',
				'class'    => yith_set_wrapper_class(),
				'priority' => 'high',
				'tabs'     => include YITH_WCBK_DIR . '/plugin-options/meta-boxes/search-form-options.php',
			);

			$meta_box = YIT_Metabox( 'yith-wcbk-search-form-options' );
			$meta_box->init( $args );
		}

		/**
		 * Print the meta-box title
		 */
		public function print_meta_box_title() {
			echo '<h3 class="yith-wcbk-admin-search-form-section-title">' . esc_html__( 'Options', 'yith-booking-for-woocommerce' ) . '</h3>';
		}

		/**
		 * Render Search form meta-box
		 *
		 * @param WP_Post $post The Post.
		 */
		public function print_search_form_metabox( $post ) {

			$search_form = yith_wcbk_get_search_form( $post->ID );
			$fields      = $search_form->get_fields();

			include YITH_WCBK_VIEWS_PATH . 'metaboxes/html-search-form-metabox.php';
		}

		/**
		 * Save meta on save post
		 *
		 * @param int $post_id The post ID.
		 */
		public function save( $post_id ) {
			// phpcs:disable WordPress.Security.NonceVerification.Missing
			if ( YITH_WCBK_Post_Types::SEARCH_FORM === get_post_type( $post_id ) ) {
				if ( isset( $_POST['_yith_wcbk_admin_search_form_fields'] ) ) {
					$form_fields = wc_clean( wp_unslash( $_POST['_yith_wcbk_admin_search_form_fields'] ) );
					$form_fields = is_array( $form_fields ) ? $form_fields : array();
					update_post_meta( $post_id, '_yith_wcbk_admin_search_form_fields', $form_fields );
				}
			}
			// phpcs:enable
		}

		/**
		 * Render blank state. Extend to add content.
		 */
		protected function render_blank_state() {
			parent::render_blank_state();

			echo '<style>.page-title-action{ display: none !important; }</style>';
		}
	}
}

return YITH_WCBK_Search_Form_Post_Type_Admin::instance();
