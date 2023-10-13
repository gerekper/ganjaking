<?php
/**
 * Class YITH_WCBK_Booking_Person_Type_Post_Type_Admin
 *
 * Handles the Booking post type on admin side.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBK_Booking_Person_Type_Post_Type_Admin' ) ) {
	/**
	 * Class YITH_WCBK_Booking_Person_Type_Post_Type_Admin
	 */
	class YITH_WCBK_Booking_Person_Type_Post_Type_Admin extends YITH_WCBK_Post_Type_Admin {
		/**
		 * The post type.
		 *
		 * @var string
		 */
		protected $post_type = YITH_WCBK_Post_Types::PERSON_TYPE;

		/**
		 * Return the post_type settings placeholder.
		 *
		 * @return array Array of settings: title_placeholder, title_description, updated_messages.
		 */
		protected function get_post_type_settings() {
			return array(
				'title_placeholder'            => __( 'Person type name', 'yith-booking-for-woocommerce' ),
				'title_description'            => __( 'Enter a name to identify this person type.', 'yith-booking-for-woocommerce' ),
				'updated_messages'             => array(
					1  => __( 'Person type updated.', 'yith-booking-for-woocommerce' ),
					4  => __( 'Person type updated.', 'yith-booking-for-woocommerce' ),
					6  => __( 'Person type created.', 'yith-booking-for-woocommerce' ),
					7  => __( 'Person type saved.', 'yith-booking-for-woocommerce' ),
					8  => __( 'Person type submitted.', 'yith-booking-for-woocommerce' ),
					10 => __( 'Person type draft updated.', 'yith-booking-for-woocommerce' ),
				),
				'hide_views'                   => true,
				'hide_new_post_button_in_list' => true,
			);
		}

		/**
		 * Initialize the WP List handlers.
		 */
		public function init_wp_list_handlers() {
			parent::init_wp_list_handlers();
			if ( $this->should_wp_list_handlers_be_loaded() ) {
				add_action( 'admin_footer', array( $this, 'render_add_post_form' ) );
			}
		}

		/**
		 * Render add post form.
		 */
		public function render_add_post_form() {
			global $post_type, $post_type_object;

			$fields = array(
				'name'        => array(
					'label'             => __( 'Name', 'yith-booking-for-woocommerce' ),
					'name'              => 'name',
					'type'              => 'text',
					'desc'              => __( 'Enter a name to identify the person type.', 'yith-booking-for-woocommerce' ),
					'custom_attributes' => array(
						'required' => 'required',
					),
				),
				'description' => array(
					'label' => __( 'Description', 'yith-booking-for-woocommerce' ),
					'name'  => 'description',
					'type'  => 'textarea',
					'desc'  => __( 'Enter a description.', 'yith-booking-for-woocommerce' ),
				),
			);

			$post_status = wc_clean( wp_unslash( $_REQUEST['post_status'] ?? 'any' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			if ( $post_type && $post_type_object && current_user_can( $post_type_object->cap->create_posts ) && in_array( $post_status, array( 'any', 'publish' ), true ) ) {
				yith_wcbk_get_view( 'post-types/new-post-form.php', compact( 'fields' ) );
			}
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
				'icon'    => 'people',
				'message' => __( 'You have no person yet!', 'yith-booking-for-woocommerce' ),
			);
		}

	}
}

return YITH_WCBK_Booking_Person_Type_Post_Type_Admin::instance();
