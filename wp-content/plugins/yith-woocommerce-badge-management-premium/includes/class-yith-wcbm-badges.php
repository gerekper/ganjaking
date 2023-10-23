<?php
/**
 * Badges Class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\BadgeManagement\Classes
 * @since   2.0
 */

if ( ! class_exists( 'YITH_WCBM_Badges' ) ) {
	/**
	 * Class YITH_WCBM_Badges
	 */
	class YITH_WCBM_Badges {
		/**
		 * Class instance
		 *
		 * @var YITH_WCBM_Badges|YITH_WCBM_Badges_Premium
		 */
		protected static $instance;

		/**
		 * Badge IDs transient key
		 *
		 * @var string
		 */
		protected $badge_ids_transient = 'yith_wcbm_badge_ids';

		/**
		 * Return the class instance
		 *
		 * @return YITH_WCBM_Badges|YITH_WCBM_Badge_Premium
		 */
		public static function get_instance() {
			$self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

			return ! is_null( $self::$instance ) ? $self::$instance : $self::$instance = new $self();
		}

		/**
		 * YITH_WCBM_Badges constructor.
		 */
		public function __construct() {

			add_filter( 'wp_insert_post_data', array( $this, 'check_badge_title_to_prevent_duplicate' ), 10, 2 );

			add_filter( 'post_updated_messages', array( $this, 'badge_updated_messages' ) );
			add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_badge_updated_messages' ), 10, 2 );

			// Register data store.
			add_filter( 'woocommerce_data_stores', array( $this, 'register_data_stores' ) );

			add_action( 'save_post_' . YITH_WCBM_Post_Types::$badge, array( $this, 'save_badge' ) );
			add_action( 'admin_action_yith_wcbm_clone_badge', array( $this, 'clone_badge' ) );

			add_filter( 'yith_plugin_fw_metabox_yith-wcbm-metabox_field_pre_get_value', array( $this, 'initialize_value_in_metabox_field' ), 10, 4 );

			add_action( 'publish_' . YITH_WCBM_Post_Types::$badge, array( $this, 'delete_badge_ids_transient' ) );
			add_action( 'wp_insert_post', array( $this, 'maybe_delete_badge_ids_transient' ), 10, 3 );
			add_action( 'delete_post', array( $this, 'maybe_delete_badge_ids_transient' ), 10, 3 );
		}

		/**
		 * Check post title to prevent duplicate
		 *
		 * @param array $data    Post Data.
		 * @param array $postarr Post Array.
		 *
		 * @return array
		 */
		public function check_badge_title_to_prevent_duplicate( $data, $postarr ) {
			if ( YITH_WCBM_Post_Types::$badge === $data['post_type'] ) {
				$data['post_title'] = yith_wcbm_get_unique_post_title( $data['post_title'], $postarr['ID'], $postarr['post_type'] );
			}

			return $data;
		}

		/**
		 * Change post default messages for Badge post type
		 *
		 * @param array $messages The Badge messages.
		 *
		 * @return array
		 */
		public function badge_updated_messages( $messages ) {
			global $post_type;
			if ( YITH_WCBM_Post_Types::$badge === $post_type ) {
				$messages['post'][1] = __( 'Badge saved.', 'yith-woocommerce-badges-management' );
				$messages['post'][4] = __( 'Badge saved.', 'yith-woocommerce-badges-management' );
				$messages['post'][6] = __( 'Badge created.', 'yith-woocommerce-badges-management' );
				$messages['post'][7] = __( 'Badge saved.', 'yith-woocommerce-badges-management' );
				$messages['post'][8] = __( 'Badge saved.', 'yith-woocommerce-badges-management' );
			}

			return $messages;
		}

		/**
		 * Change bulk post default messages for Badge post type
		 *
		 * @param array $messages    The badge messages.
		 * @param array $bulk_counts The bulk badge counts.
		 *
		 * @return array
		 */
		public function bulk_badge_updated_messages( $messages, $bulk_counts ) {
			global $post_type;
			if ( YITH_WCBM_Post_Types::$badge === $post_type ) {
				// translators: %s is the deleted badges number.
				$messages['post']['deleted'] = _n( '%s badge deleted.', '%s badges deleted.', $bulk_counts['deleted'], 'yith-woocommerce-badges-management' );
			}

			return $messages;
		}

		/**
		 * Add Badge rule Data Store to WC ones.
		 *
		 * @param array $data_stores WC Data Stores.
		 *
		 * @return array
		 */
		public function register_data_stores( $data_stores ) {
			$data_stores['badge'] = 'YITH_WCBM_Badge_Data_Store_CPT';

			return $data_stores;
		}

		/**
		 * Handle Badge saving
		 *
		 * @param int $post_id Badge ID.
		 */
		public function save_badge( $post_id ) {
			global $post_type;

			$is_bulk = wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ?? false ) ), 'bulk-posts' ) && YITH_WCBM_Post_Types::$badge === $post_type;

			if ( $is_bulk || ( isset( $_POST['yith_wcbm_badge_security'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['yith_wcbm_badge_security'] ) ), 'yith_wcbm_save_badge' ) && get_post_type( $post_id ) === YITH_WCBM_Post_Types::$badge ) ) {
				$badge = yith_wcbm_get_badge_object( $post_id );

				if ( $badge ) {
					$props = $badge->get_internal_props_from_request();
					$badge->set_props( $props );
					$badge->save();
				}
			}
			YITH_WCBM_Frontend::delete_badges_inline_css_transient();
		}

		/**
		 * Clone badge action handler
		 */
		public function clone_badge() {
			if ( isset( $_REQUEST['post'], $_REQUEST['security'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['security'] ) ), 'yith_wcbm_clone_badge' ) ) {
				$id = isset( $_REQUEST['post'] ) ? absint( $_REQUEST['post'] ) : '';

				$badge = get_post( $id );

				if ( ! $badge || YITH_WCBM_Post_Types::$badge !== $badge->post_type ) {
					/* translators: %s: Badge ID. */
					wp_die( esc_html( sprintf( __( 'Error while duplicating badge: badge #%s not found', 'yith-woocommerce-badges-management' ), $id ) ) );
				}

				$badge = yith_wcbm_get_badge_object( $badge );
				if ( $badge ) {
					$clone = clone $badge;
					$clone->set_id( 0 );
					$clone->set_title( yith_wcbm_get_unique_post_title( $clone->get_title(), 0, YITH_WCBM_Post_Types::$badge ) );
					$clone->save();
				}
			}

			wp_safe_redirect( add_query_arg( array( 'post_type' => YITH_WCBM_Post_Types::$badge ), admin_url( 'edit.php' ) ) );
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
							$value = in_array( $_GET['badge-type'], array( 'text', 'image' ), true ) ? sanitize_text_field( wp_unslash( $_GET['badge-type'] ) ) : 'text';
						}
						break;
				}
			}

			return $value;
		}

		/**
		 * Delete Badge IDs transient if the post id is a Badge type.
		 *
		 * @param int|WP_Post $post_id The post ID (or just the post).
		 * @param WP_Post     $post    The post object.
		 * @param bool        $update  If the post is updated.
		 *
		 * @return void
		 * @since 2.6.0
		 */
		public function maybe_delete_badge_ids_transient( $post_id, WP_Post $post, bool $update = false ): void {
			if ( ! $update && get_post_type( $post_id ) === YITH_WCBM_Post_Types::$badge ) {
				$this->delete_badge_ids_transient();
			}
		}

		/**
		 * Set Badge IDs transient
		 *
		 * @param array $badge_ids The Badge IDs.
		 *
		 * @return void
		 * @since 2.6.0
		 */
		public function set_badge_ids_transient( $badge_ids ) {
			set_transient( $this->badge_ids_transient, $badge_ids );
		}

		/**
		 * Delete Badge IDs transient
		 *
		 * @return void
		 * @since 2.6.0
		 */
		public function delete_badge_ids_transient() {
			delete_transient( $this->badge_ids_transient );
		}

		/**
		 * Get Badge IDs
		 *
		 * @return int[]
		 * @since 2.6.0
		 */
		public function get_badge_ids(): array {
			$badge_ids = get_transient( $this->badge_ids_transient );
			if ( ! $badge_ids ) {
				$args = array(
					'posts_per_page' => -1,
					'orderby'        => 'title',
					'order'          => 'ASC',
					'post_status'    => 'publish',
					'fields'         => 'ids',
					'post_type'      => YITH_WCBM_Post_Types::$badge,
				);

				$badge_ids = get_posts( $args );
				$this->set_badge_ids_transient( $badge_ids );
			}

			return $badge_ids;
		}
	}
}

if ( ! function_exists( 'yith_wcbm_badges' ) ) {
	/**
	 * Get the class instance
	 *
	 * @return YITH_WCBM_Badges|YITH_WCBM_Badge_Premium
	 */
	function yith_wcbm_badges() {
		return YITH_WCBM_Badges::get_instance();
	}
}
