<?php
/**
 * Post type admin class for "extra costs".
 *
 * Handles the "extra cost" post type on admin side.
 *
 * @class   YITH_WCBK_Booking_Extra_Cost_Post_Type_Admin
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCBK_Booking_Extra_Cost_Post_Type_Admin' ) ) {
	/**
	 * Class YITH_WCBK_Booking_Extra_Cost_Post_Type_Admin
	 *
	 * @author YITH <plugins@yithemes.com>
	 */
	class YITH_WCBK_Booking_Extra_Cost_Post_Type_Admin extends YITH_WCBK_Post_Type_Admin {
		/**
		 * The post type.
		 *
		 * @var string
		 */
		protected $post_type = YITH_WCBK_Post_Types::EXTRA_COST;

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
		 * Return the post_type settings placeholder.
		 *
		 * @return array Array of settings: title_placeholder, title_description, updated_messages.
		 */
		protected function get_post_type_settings() {
			return array(
				'title_placeholder'            => __( 'Extra cost name', 'yith-booking-for-woocommerce' ),
				'title_description'            => __( 'Enter a name to identify this extra cost.', 'yith-booking-for-woocommerce' ),
				'updated_messages'             => array(
					1  => __( 'Extra cost updated.', 'yith-booking-for-woocommerce' ),
					4  => __( 'Extra cost updated.', 'yith-booking-for-woocommerce' ),
					6  => __( 'Extra cost created.', 'yith-booking-for-woocommerce' ),
					7  => __( 'Extra cost saved.', 'yith-booking-for-woocommerce' ),
					8  => __( 'Extra cost submitted.', 'yith-booking-for-woocommerce' ),
					10 => __( 'Extra cost draft updated.', 'yith-booking-for-woocommerce' ),
				),
				'hide_views'                   => true,
				'hide_new_post_button_in_list' => true,
			);
		}

		/**
		 * Render add post form.
		 */
		public function render_add_post_form() {
			global $post_type, $post_type_object;

			$fields = array(
				'name'        => array(
					'label' => __( 'Name', 'yith-booking-for-woocommerce' ),
					'name'  => 'name',
					'type'  => 'text',
					'desc'  => __( 'Enter a name to identify the extra cost.', 'yith-booking-for-woocommerce' ),
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
				'icon'    => 'bank',
				'message' => __( 'You have no cost yet!', 'yith-booking-for-woocommerce' ),
			);
		}
	}
}

return YITH_WCBK_Booking_Extra_Cost_Post_Type_Admin::instance();
