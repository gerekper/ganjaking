<?php
/**
 * Avatar handler class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH WooCommerce Customize My Account Page
 * @version 1.0.0
 */

defined( 'YITH_WCMAP' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WCMAP_Avatar', false ) ) {
	/**
	 * YITH WooCommerce Customize My Account Page Avatar class
	 *
	 * @since 1.0.0
	 */
	class YITH_WCMAP_Avatar {

		/**
		 * Action upload avatar
		 *
		 * @since 3.0.0
		 * @const string
		 */
		const AVATAR_ACTION = 'ywcmap_avatar_action';

		/**
		 * Constructor
		 *
		 * @since  1.0.0
		 */
		public function __construct() {

			if ( self::can_upload_avatar() ) {
				// AJAX actions.
				add_action( 'wc_ajax_' . self::AVATAR_ACTION, array( $this, 'avatar_ajax_action' ) );
				// Add frontend modal.
				add_action( 'wp_footer', array( $this, 'add_avatar_modal' ) );
				// Enqueue scripts and styles.
				add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 20 );
				// Get custom avatar.
				add_filter( 'pre_get_avatar', array( $this, 'get_avatar' ), 100, 3 );
			}

			// GDPR compliance.
			add_filter( 'woocommerce_privacy_export_customer_personal_data', array( $this, 'export_avatar' ), 99, 2 );
			add_filter( 'woocommerce_privacy_erase_personal_data_customer', array( $this, 'erase_avatar' ), 99, 2 );
		}

		/**
		 * Check if users are able to upload their own avatar image
		 *
		 * @since 3.0.0
		 * @return boolean
		 */
		public static function can_upload_avatar() {
			$opts    = get_option( 'yith_wcmap_avatar', array() );
			$enabled = ! isset( $opts['custom'] ) || 'yes' === $opts['custom'];

			/**
			 * APPLY_FILTERS: yith_wcmap_users_can_upload_avatar
			 *
			 * Filters whether the users can upload a custom avatar.
			 *
			 * @param bool $users_can_upload_avatar Whether the users can upload a custom avatar or not.
			 *
			 * @return bool
			 */
			return apply_filters( 'yith_wcmap_users_can_upload_avatar', $enabled );
		}

		/**
		 * Check if users are able to upload their own avatar image
		 *
		 * @since 3.0.0
		 * @return boolean
		 */
		public static function get_avatar_default_size() {
			$opts = get_option( 'yith_wcmap_avatar', array() );
			$size = ! empty( $opts['avatar_size'] ) ? absint( $opts['avatar_size'] ) : 120;

			/**
			 * APPLY_FILTERS: yith_wcmap_filter_avatar_size
			 *
			 * Filters the avatar size.
			 *
			 * @param int $size Avatar size.
			 *
			 * @return int
			 */
			return apply_filters( 'yith_wcmap_filter_avatar_size', $size );
		}

		/**
		 * Add localized script for avatar
		 *
		 * @since 3.0.0
		 * @return void
		 */
		public function enqueue_scripts() {
			wp_localize_script(
				'ywcmap-frontend',
				'yith_wcmap',
				array(
					'ajaxUrl'     => WC_AJAX::get_endpoint( self::AVATAR_ACTION ),
					'actionNonce' => wp_create_nonce( self::AVATAR_ACTION ),
				)
			);
		}

		/**
		 * Handle avatar AJAX actions
		 *
		 * @since 3.0.0
		 * @return void
		 */
		public function avatar_ajax_action() {
			check_ajax_referer( self::AVATAR_ACTION, 'security' );

			$action = ! empty( $_REQUEST['action'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['action'] ) ) : false;

			if ( empty( $action ) || ! is_callable( array( $this, $action ) ) ) {
				wp_send_json_error();
			}

			/**
			 * DO_ACTION: yith_wcmap_before_avatar_ajax_action
			 *
			 * Allows to trigger some action before applying the action to manage the avatar.
			 *
			 * @param string $action Action to manage the avatar.
			 */
			do_action( 'yith_wcmap_before_avatar_ajax_action', $action );

			$this->$action();
			die();
		}

		/**
		 * Get user custom avatar ID
		 *
		 * @since 3.0.0
		 * @param integer $user The user ID.
		 * @return integer
		 */
		public function get_user_avatar_id( $user = 0 ) {
			if ( ! $user ) {
				$user = get_current_user_id();
			}

			return intval( get_user_meta( $user, 'yith-wcmap-avatar', true ) );
		}

		/**
		 * Upload custom avatar
		 *
		 * @since 3.0.0
		 * @return void
		 */
		protected function upload_avatar() {
			/**
			 * APPLY_FILTERS: ywcmap_prevent_upload_user_avatar
			 *
			 * Filters whether to prevent the user to upload a custom avatar.
			 *
			 * @param bool  $prevent_upload_user_avatar Whether to prevent the user to upload a custom avatar or not.
			 * @param array $avatar_data                Data of the uploaded avatar.
			 *
			 * @return bool
			 */
			if ( empty( $_FILES['ywcmap_custom_avatar'] ) || apply_filters( 'ywcmap_prevent_upload_user_avatar', false, $_FILES['ywcmap_custom_avatar'] ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
				wp_send_json_error();
			}

			// Required file.
			if ( ! function_exists( 'media_handle_upload' ) ) {
				require_once ABSPATH . 'wp-admin/includes/media.php';
			}
			if ( ! function_exists( 'wp_handle_upload' ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}
			if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
				require_once ABSPATH . 'wp-admin/includes/image.php';
			}

			$media_id = media_handle_upload( 'ywcmap_custom_avatar', 0 );
			if ( is_wp_error( $media_id ) ) {
				wp_send_json_error();
			}

			// Save media id for filter query in media library.
			$medias   = get_option( 'yith_wcmap_users_avatar_ids', array() );
			$medias[] = $media_id;
			// Then save.
			update_option( 'yith_wcmap_users_avatar_ids', $medias );

			// Save user meta.
			$user = get_current_user_id();
			update_user_meta( $user, 'yith_wcmap_avatar_temp', $media_id );

			// Maybe resize images.
			yith_wcmap_resize_avatar_url( $media_id, '150' );
			$src = yith_wcmap_generate_avatar_url( $media_id, '150' );

			wp_send_json_success(
				array(
					'html' => sprintf( "<img src='%s' height='150' width='150' />", esc_url( $src ) ),
				)
			);
		}

		/**
		 * Set custom avatar from temp value
		 *
		 * @since 3.0.0
		 * @return void
		 */
		protected function set_avatar() {
			// Get temp user avatar.
			$user      = get_current_user_id();
			$avatar_id = get_user_meta( $user, 'yith_wcmap_avatar_temp', true );

			if ( empty( $avatar_id ) ) {
				wp_send_json_error();
			}

			// Clear old avatar if any.
			$this->delete_avatar( 'yith-wcmap-avatar' );
			// Set the new one.
			delete_user_meta( $user, 'yith_wcmap_avatar_temp' );
			update_user_meta( $user, 'yith-wcmap-avatar', $avatar_id );

			wp_send_json_success();
		}

		/**
		 * Clear temp avatar
		 *
		 * @since 3.0.0
		 * @return void
		 */
		protected function clear_temp_avatar() {
			$this->delete_avatar( 'yith_wcmap_avatar_temp' );
			wp_send_json_success();
		}

		/**
		 * Reset avatar to default
		 *
		 * @since 3.0.0
		 * @return void
		 */
		protected function reset_avatar() {
			$this->delete_avatar( 'yith-wcmap-avatar' );
			wp_send_json_success();
		}

		/**
		 * Delete given customer avatar
		 *
		 * @since 3.0.0
		 * @param string $avatar_key The avatar key to delete.
		 * @return boolean
		 */
		protected function delete_avatar( $avatar_key ) {

			$user   = get_current_user_id();
			$avatar = get_user_meta( $user, $avatar_key, true );

			if ( empty( $avatar ) ) {
				return true;
			}

			$avatar_ids = get_option( 'yith_wcmap_users_avatar_ids', array() );
			$key        = array_search( intval( $avatar ), $avatar_ids, true );

			if ( false !== $key ) {
				unset( $avatar_ids[ $key ] );
				// Then save.
				update_option( 'yith_wcmap_users_avatar_ids', $avatar_ids );
			}

			// Then delete user meta.
			delete_user_meta( $user, $avatar_key );
			wp_delete_attachment( $avatar, true );

			return true;
		}

		/**
		 * Get avatar upload form
		 *
		 * @since  2.2.0
		 * @access public
		 * @return void
		 */
		public function add_avatar_modal() {

			if ( is_null( YITH_WCMAP()->frontend ) || ! YITH_WCMAP()->frontend->is_my_account() ) {
				return;
			}

			wc_get_template(
				'ywcmap-myaccount-avatar-modal.php',
				array(
					'has_custom_avatar' => ! empty( $this->get_user_avatar_id() ),
				),
				'',
				YITH_WCMAP_DIR . 'templates/'
			);
		}

		/**
		 * Get customer avatar for user
		 *
		 * @access public
		 * @since  1.0.0
		 * @param string $avatar Current customer avatar.
		 * @param mixed  $id_or_email The customer id or email.
		 * @param array  $args The avatar args.
		 * @return string
		 */
		public function get_avatar( $avatar, $id_or_email, $args ) {
			// Prevent filter.
			remove_all_filters( 'get_avatar' );

			$user = false;

			if ( is_string( $id_or_email ) && is_email( $id_or_email ) ) {
				$user = get_user_by( 'email', $id_or_email );
			} elseif ( $id_or_email instanceof WP_User ) {
				// User Object.
				$user = $id_or_email;
			} elseif ( $id_or_email instanceof WP_Post ) {
				// Post Object.
				$user = get_user_by( 'id', (int) $id_or_email->post_author );
			} elseif ( $id_or_email instanceof WP_Comment ) {

				if ( ! empty( $id_or_email->user_id ) ) {
					$user = get_user_by( 'id', (int) $id_or_email->user_id );
				}
				if ( ( ! $user || is_wp_error( $user ) ) && ! empty( $id_or_email->comment_author_email ) ) {
					$email = $id_or_email->comment_author_email;
					$user  = get_user_by( 'email', $email );
				}
			}

			// Get the user ID.
			$user_id = ! $user ? $id_or_email : $user->ID;
			// Get custom avatar and make sure file exists.
			$custom_avatar = $this->get_user_avatar_id( $user_id );
			if ( ! $custom_avatar || ! get_attached_file( $custom_avatar ) ) {
				return $avatar;
			}

			// Maybe resize image.
			$resized = yith_wcmap_resize_avatar_url( $custom_avatar, $args['size'] );
			// If error occurred return.
			if ( ! $resized ) {
				return $avatar;
			}

			$src   = yith_wcmap_generate_avatar_url( $custom_avatar, $args['size'] );
			$class = array( 'avatar', 'avatar-' . (int) $args['size'], 'photo' );

			$avatar = sprintf(
				"<img alt='%s' src='%s' class='%s' height='%d' width='%d' %s/>",
				esc_attr( $args['alt'] ),
				esc_url( $src ),
				esc_attr( join( ' ', $class ) ),
				(int) $args['height'],
				(int) $args['width'],
				$args['extra_attr']
			);

			return $avatar;
		}

		/**
		 * Filter default avatar url to get the custom one
		 *
		 * @since  3.0.0
		 * @param string $url         The URL of the avatar.
		 * @param mixed  $id_or_email The Gravatar to retrieve. Accepts a user ID, Gravatar MD5 hash,
		 *                            user email, WP_User object, WP_Post object, or WP_Comment object.
		 * @param array  $args        Arguments passed to get_avatar_data(), after processing.
		 * @return array
		 */
		public function default_avatar_url( $url, $id_or_email, $args ) {
			$opts = get_option( 'yith_wcmap_avatar', array() );
			if ( ! empty( $opts['default'] ) && ! empty( $opts['custom_default'] ) && 'custom' === $opts['default'] ) {
				$url = $opts['custom_default'];
			}

			return $url;
		}

		/**
		 * Add avatar to customer data for GDPR exporter
		 *
		 * @since  2.2.9
		 * @param array       $data The export data.
		 * @param WC_Customer $customer The customer.
		 * @return array
		 */
		public function export_avatar( $data, $customer ) {

			$avatar = $this->get_user_avatar_id( $customer->get_id() );
			if ( ! $avatar ) {
				return $data;
			}

			$src = wp_get_attachment_image_src( $avatar, 'full' );
			if ( $src ) {
				$data[] = array(
					'name'  => __( 'Custom Avatar', 'yith-woocommerce-customize-myaccount-page' ),
					'value' => '<a href="' . $src[0] . '">' . $src[0] . '</a>',
				);
			}

			return $data;
		}

		/**
		 * Erase custom avatar on GDPR request
		 *
		 * @since  2.2.9
		 * @param array       $response The response array.
		 * @param WC_Customer $customer The customer.
		 * @return array
		 */
		public function erase_avatar( $response, $customer ) {

			$avatar = $this->get_user_avatar_id( $customer->get_id() );
			if ( ! $avatar ) {
				return $response;
			}

			// Remove id from global list.
			$medias = get_option( 'yith_wcmap_users_avatar_ids', array() );
			foreach ( $medias as $key => $media ) {
				if ( $media === $avatar ) {
					unset( $medias[ $key ] );
					continue;
				}
			}

			// Then save.
			update_option( 'yith_wcmap_users_avatar_ids', $medias );
			// Then delete user meta.
			delete_user_meta( $customer->get_id(), 'yith-wcmap-avatar' );
			// Then delete media attachment.
			wp_delete_attachment( $avatar );

			$response['messages'][]    = __( 'Removed customer avatar', 'yith-woocommerce-customize-myaccount-page' );
			$response['items_removed'] = true;

			return $response;
		}
	}
}
