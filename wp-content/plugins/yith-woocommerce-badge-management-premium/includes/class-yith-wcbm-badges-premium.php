<?php
/**
 * Badges Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagementPremium\Classes
 * @since   2.0
 */

if ( ! class_exists( 'YITH_WCBM_Badges_Premium' ) ) {
	/**
	 * Class YITH_WCBM_Badges
	 */
	class YITH_WCBM_Badges_Premium extends YITH_WCBM_Badges {

		/**
		 * YITH_WCBM_Badges constructor.
		 */
		public function __construct() {
			parent::__construct();

			add_action( 'wp_ajax_yith_wcbm_add_badge_to_library', array( $this, 'add_badge_to_library' ) );
		}

		/**
		 * Add Badge rule Data Store to WC ones.
		 *
		 * @param array $data_stores WC Data Stores.
		 *
		 * @return array
		 */
		public function register_data_stores( $data_stores ) {
			$data_stores['badge']         = 'YITH_WCBM_Badge_Data_Store_CPT';
			$data_stores['badge-premium'] = 'YITH_WCBM_Badge_Premium_Data_Store_CPT';

			return $data_stores;
		}

		/**
		 * Filter the value initialized in metabox fields
		 *
		 * @param null   $value      The value.
		 * @param int    $post_id    The post ID.
		 * @param string $field_name The field name.
		 * @param array  $field      The field.
		 *
		 * @return mixed
		 */
		public function initialize_value_in_metabox_field( $value, $post_id, $field_name, $field ) {
			static $badge = null;

			if ( is_null( $badge ) ) {
				$badge = yith_wcbm_get_badge_object( $post_id );
			}

			$prop   = preg_replace( '/yith_wcbm_badge|\[_|\]/m', '', $field['name'] );
			$getter = 'get_' . $prop;

			if ( method_exists( $badge, $getter ) ) {
				$value = $badge->$getter();

				switch ( $prop ) {
					case 'type':
						if ( ! $value && isset( $_GET['badge-type'], $_GET['security'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['security'] ) ), 'yith_wcbm_create_badge' ) ) {
							$value = in_array( $_GET['badge-type'], array( 'text', 'image', 'css', 'advanced' ), true ) ? sanitize_text_field( wp_unslash( $_GET['badge-type'] ) ) : 'text';
						}
						break;
					case 'image':
					case 'advanced':
					case 'css':
						$badge_list = function_exists( 'yith_wcbm_get_badges_list' ) ? yith_wcbm_get_badges_list( $prop ) : array();
						if ( ! in_array( $value, $badge_list, true ) && ! ( 'image' === $prop && 'upload' === $value && yith_wcbm_has_active_license() ) ) {
							$value = current( $badge_list );
						}
						break;
				}
			}

			return $value;
		}

		/**
		 * AJAX add badge to library
		 */
		public function add_badge_to_library() {
			$return = array( 'success' => false );
			if ( isset( $_POST['badge_id'], $_POST['badge_type'], $_POST['security'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'yith_wcbm_add_badge_to_library' ) && yith_wcbm_has_active_license() ) {
				$badge_id = sanitize_text_field( wp_unslash( $_POST['badge_id'] ) );
				$type     = sanitize_text_field( wp_unslash( $_POST['badge_type'] ) );

				$api_url       = 'https://plugins.yithemes.com/resources/yith-woocommerce-badge-management/badges/templates/' . $type . '/' . $badge_id;
				$api_call_args = array(
					'timeout'    => apply_filters( 'yith_wcbm_add_badge_to_library_timeout', 15 ),
					'user-agent' => 'YITH Badge Management Premium/' . YITH_WCBM_VERSION . '; ' . get_site_url(),
				);
				$response      = wp_remote_get( $api_url );

				if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
					$imported_badge = $response['body'] ?? '';

					/**
					 * WordPress Filesystem
					 *
					 * @var WP_Filesystem_Direct $wp_filesystem
					 */
					global $wp_filesystem;
					if ( empty( $wp_filesystem ) ) {
						require_once ABSPATH . '/wp-admin/includes/file.php';
						WP_Filesystem();
					}
					$dir = yith_wcbm_get_badge_library_dir_path( $type );
					wp_mkdir_p( $dir );
					if ( $wp_filesystem->put_contents( $dir . $badge_id, $imported_badge ) ) {
						$return['success']      = true;
						$args                   = array(
							'type'  => $type,
							'style' => $badge_id,
						);
						$return['badgeContent'] = yith_wcbm_get_badge_svg( $args, true );
					}
				}
			}
			wp_send_json( $return );
			exit();
		}

	}
}
