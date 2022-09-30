<?php
/**
 * Handle meta-boxes.
 *
 * @author  YITH
 * @package YITH WooCommerce Membership
 * @version 1.0.0
 */

! defined( 'YITH_WCMBS' ) && exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMBS_Admin_Meta_Boxes' ) ) {
	/**
	 * Meta-boxes class.
	 * The class handles admin meta-boxes.
	 *
	 * @since    1.4.0
	 * @author   Leanza Francesco <leanzafrancesco@gmail.com>
	 */
	class YITH_WCMBS_Admin_Meta_Boxes {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCMBS_Admin_Meta_Boxes
		 */
		protected static $instance;

		/**
		 * Is meta boxes saved once?
		 *
		 * @var boolean
		 */
		private static $saved_meta_boxes = false;

		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WCMBS_Admin_Meta_Boxes
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * YITH_WCMBS_Admin_Meta_Boxes constructor.
		 */
		protected function __construct() {
			add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
			add_action( 'save_post', array( $this, 'save_meta_boxes' ), 10, 2 );
			add_action( 'edit_attachment', array( $this, 'save_meta_boxes' ), 10, 1 );
			add_action( 'edit_attachment', array( $this, 'save_restrict_access_meta_box' ) );
			add_action( 'admin_menu', array( $this, 'remove_publish_box' ) );

			add_action( 'yith_wcbm_metaboxes_print_custom_field', array( $this, 'print_custom_field' ) );
		}

		/**
		 * Retrieve all meta-boxes
		 */
		public function get_meta_boxes() {
			$meta_boxes = array(
				'membership-options' => array(
					'id'         => 'yith-wcmbs-membership-options',
					'title'      => __( 'Membership options', 'yith-woocommerce-membership' ),
					'context'    => 'normal',
					'priority'   => 'default',
					'post_types' => YITH_WCMBS_Manager()->post_types,
				),
				'plan-options'       => array(
					'id'         => 'yith-wcmbs-plan-options',
					'title'      => __( 'Plan Options', 'yith-woocommerce-membership' ),
					'context'    => 'normal',
					'priority'   => 'high',
					'post_types' => YITH_WCMBS_Post_Types::$plan,
				),
				'membership-info'    => array(
					'id'         => 'yith-wcmbs-membership-info',
					'title'      => __( 'Membership Info', 'yith-woocommerce-membership' ),
					'context'    => 'normal',
					'priority'   => 'high',
					'post_types' => YITH_WCMBS_Post_Types::$membership,
				),
				'membership-actions' => array(
					'id'         => 'yith-wcmbs-membership-actions',
					'title'      => __( 'Membership Actions', 'yith-woocommerce-membership' ),
					'context'    => 'side',
					'priority'   => 'high',
					'post_types' => YITH_WCMBS_Post_Types::$membership,
				),
			);

			return $meta_boxes;
		}

		/**
		 * Add meta boxes
		 *
		 * @param string $post_type Post type.
		 */
		public function add_meta_boxes( $post_type ) {
			$meta_boxes = $this->get_meta_boxes();

			if ( empty( $meta_boxes ) || ! is_array( $meta_boxes ) ) {
				return;
			}

			foreach ( $meta_boxes as $meta_box ) {
				$post_types = isset( $meta_box['post_types'] ) ? (array) $meta_box['post_types'] : null;
				if ( is_null( $post_types ) || in_array( $post_type, $post_types ) ) {
					add_meta_box(
						$meta_box['id'],
						$meta_box['title'],
						array( $this, 'render_meta_box' ),
						$post_types,
						$meta_box['context'],
						$meta_box['priority']
					);
				}
			}
		}


		/**
		 * Render Meta-box
		 *
		 * @param WP_Post $post Post object.
		 * @param array   $meta_box
		 */
		public function render_meta_box( $post, $meta_box ) {

			if ( ! isset( $meta_box['id'] ) ) {
				return;
			}

			switch ( $meta_box['id'] ) {
				case 'yith-wcmbs-plan-options':
					$plan = yith_wcmbs_get_plan( $post->ID );
					yith_wcmbs_get_view( '/metaboxes/plan-options.php', compact( 'plan' ) );
					break;
				case 'yith-wcmbs-membership-options':
					$post_id = $post->ID;
					yith_wcmbs_get_view( '/metaboxes/membership-options.php', compact( 'post', 'post_id' ) );
					break;

				case 'yith-wcmbs-membership-info':
					$membership = yith_wcmbs_get_membership( $post->ID );
					yith_wcmbs_get_view( '/metaboxes/membership_info_content.php', compact( 'membership' ) );
					break;
				case 'yith-wcmbs-membership-actions':
					$membership = yith_wcmbs_get_membership( $post->ID );
					yith_wcmbs_get_view( '/metaboxes/membership_actions.php', compact( 'membership' ) );
					break;
			}
		}

		/**
		 * Remove the 'Publish' meta-box
		 */
		public function remove_publish_box() {
			remove_meta_box( 'submitdiv', YITH_WCMBS_Post_Types::$membership, 'side' );
		}

		/**
		 * Save Meta-boxes
		 *
		 * @param int          $post_id The post ID.
		 * @param WP_Post|bool $post
		 */
		public function save_meta_boxes( $post_id, $post = false ) {
			// $post_id and $post are required
			if ( empty( $post_id ) || self::$saved_meta_boxes ) {
				return;
			}

			// Dont' save meta boxes for revisions or auto-saves.
			if ( $post ) {
				if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
					return;
				}

				// Check the post being saved == the $post_id to prevent triggering this call for other save_post events.
				if ( empty( $_POST['post_ID'] ) || absint( $_POST['post_ID'] ) !== $post_id ) {
					return;
				}

				// Check user has permission to edit.
				if ( ! current_user_can( 'edit_post', $post_id ) ) {
					return;
				}
			}

			self::$saved_meta_boxes = true;

			$this->save_plan_options_meta_box( $post_id );
			$this->save_membership( $post_id );
			$this->save_membership_options( $post_id );
		}

		/**
		 * Save Restrict Access Meta-box for Media
		 *
		 * @param int $post_id The post ID.
		 */
		public function save_restrict_access_meta_box( $post_id ) {
			if ( isset( $_POST['_yith_wcmbs_restrict_access_edit_post'] ) ) {
				if ( ! empty( $_POST['_yith_wcmbs_restrict_access_plan'] ) ) {
					$restrict_access_plan_meta  = ! empty( $_POST['_yith_wcmbs_restrict_access_plan'] ) ? $_POST['_yith_wcmbs_restrict_access_plan'] : array();
					$restrict_access_plan_delay = ! empty( $_POST['_yith_wcmbs_plan_delay'] ) ? $_POST['_yith_wcmbs_plan_delay'] : array();

					yith_wcmbs_update_plans_meta_for_post( $post_id, $restrict_access_plan_meta );
					update_post_meta( $post_id, '_yith_wcmbs_plan_delay', $restrict_access_plan_delay );
				} else {
					yith_wcmbs_update_plans_meta_for_post( $post_id, array() );
					delete_post_meta( $post_id, '_yith_wcmbs_plan_delay' );
				}
			}
		}

		/**
		 * Save Plan Options Meta-box
		 *
		 * @param int $post_id The post ID.
		 */
		private function save_plan_options_meta_box( $post_id ) {
			if ( YITH_WCMBS_Post_Types::$plan === get_post_type( $post_id ) ) {
				$plan = yith_wcmbs_get_plan( $post_id );
				if ( $plan ) {
					$props = $plan->get_props_by_request();
					$props = array_merge( $plan->get_default_data(), $props );
					$plan->set_props( $props );
					$plan->save();

					/**
					 * Set extra data after saving the plan.
					 */
					do_action( 'yith_wcmbs_process_plan_meta', $plan );
				}
			}
		}

		/**
		 * Save Membership Meta-box
		 *
		 * @param int $post_id The post ID.
		 */
		private function save_membership( $post_id ) {
			if ( YITH_WCMBS_Post_Types::$membership === get_post_type( $post_id ) ) {
				if ( ! empty( $_POST['yith_wcmbs_membership_actions'] ) ) {
					$action = $_POST['yith_wcmbs_membership_actions'];

					if ( in_array( $action, array_keys( yith_wcmbs_get_membership_statuses() ) ) ) {
						$membership = yith_wcmbs_get_membership( $post_id );
						$membership->update_status( $action );
					}
				}

				if ( ! empty( $_POST['_yith_wcmbs_membership_user_id'] ) ) {
					$user_id = $_POST['_yith_wcmbs_membership_user_id'];

					$membership = yith_wcmbs_get_membership( $post_id );
					$membership->set( 'user_id', $user_id );
				}
			}
		}

		/**
		 * Save "Membership Options" Meta-box
		 *
		 * @param int $post_id The post ID.
		 */
		private function save_membership_options( $post_id ) {
			if ( isset( $_POST['yith-wcmbs-membership-options'], $_POST['yith-wcmbs-membership-options']['is_saving'] ) ) {
				$options = $_POST['yith-wcmbs-membership-options'];
				unset( $options['is_saving'] );

				foreach ( $options as $key => $value ) {
					switch ( $key ) {
						case '_yith_wcmbs_restrict_access_plan':
							yith_wcmbs_update_plans_meta_for_post( $post_id, $value );
							break;
						case '_yith_wcmbs_protected_links':
							$value = yith_wcmbs_sanitize_protected_links( $value );
							update_post_meta( $post_id, $key, $value );
							break;
						default:
							update_post_meta( $post_id, $key, $value );
					}
				}

				if ( empty( $options['_yith_wcmbs_restrict_access_plan'] ) ) {
					yith_wcmbs_update_plans_meta_for_post( $post_id, false );
				}

				if ( empty( $options['_yith_wcmbs_plan_delay'] ) ) {
					delete_post_meta( $post_id, '_yith_wcmbs_plan_delay' );
				}
			}
		}

		/**
		 * @param array $field The field
		 */
		public function print_custom_field( $field ) {
			$type      = $field['yith-wcbms-type'];
			$file_path = YITH_WCMBS_VIEWS_PATH . "/metaboxes/fields/{$type}.php";
			if ( file_exists( $file_path ) ) {
				include $file_path;
			}
		}

	}
}