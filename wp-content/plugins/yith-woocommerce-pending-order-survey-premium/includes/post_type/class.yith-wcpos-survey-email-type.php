<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_WCOP_Survey_Email_Post_Type' ) ) {

	class YITH_WCOP_Survey_Email_Post_Type {
		protected static $instance;
		public $post_type_name;

		public function __construct() {

			$this->post_type_name = 'ywcpos_survey_email';
			//register post type and add metabox
			add_action( 'init', array( $this, 'register_post_type' ) );
			add_action( 'admin_init', array( $this, 'add_metaboxes' ) );
			add_action( 'admin_init', array( $this, 'add_capabilities' ) );

			add_action( 'wp_ajax_ajax_send_pending_email', array( $this, 'ajax_send_pending_email' ) );
			add_action( 'wp_ajax_nopriv_send_pending_email', array( $this, 'ajax_send_pending_email' ) );
			//Custom Pending Order Survey Message
			add_filter( 'post_updated_messages', array( $this, 'custom_pending_order_survey_email_messages' ) );

			add_filter( 'yit_fw_metaboxes_type_args', array( $this, 'add_custom_email_metaboxes' ) );


			add_filter( 'ywcpos_send_email', array( $this, 'check_if_can_send_email' ), 10, 2 );

		}


		/**
		 * return single instance
		 * @author YIThemes
		 * @since 1.0.0
		 * @return YITH_WCOP_Survey_Post_Type
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * add metabox to email post type
		 * @author YIThemes
		 * @since 1.0.0
		 */
		public function add_metaboxes() {

			/**
			 * @var $metaboxes array metabox_id, metabox_opt
			 */
			$metaboxes = array(
				'yit-pending-order-survey-email-metaboxes'         => 'pending-survey-email-metaboxes-options.php',
				'yit-pending-order-survey-coupon-email-metaboxes'  => 'pending-survey-coupon-email-metaboxes-options.php',
				'yit-pending-order-survey-reports-email-metaboxes' => 'pending-survey-email-report-metaboxes-options.php'

			);

			if ( ! function_exists( 'YIT_Metabox' ) ) {
				require_once( YITH_WCPO_SURVEY_DIR . 'plugin-fw/yit-plugin.php' );
			}

			foreach ( $metaboxes as $key => $metabox ) {
				$args = require_once( YITH_WCPO_SURVEY_TEMPLATE_PATH . '/metaboxes/' . $metabox );
				$box  = YIT_Metabox( $key );
				$box->init( $args );
			}
		}

		/**
		 * register post type
		 * @author YIThemes
		 * @since 1.0.0
		 */
		public function register_post_type() {

			$args = apply_filters( 'yith_pending_order_survey_email_args_post_type', array(
					'label'               => $this->post_type_name,
					'description'         => '',
					'labels'              => $this->get_pending_order_survey_taxonomy_label(),
					'supports'            => array( 'title', 'editor' ),
					'hierarchical'        => false,
					'public'              => false,
					'show_ui'             => true,
					'show_in_menu'        => false,
					'show_in_nav_menus'   => false,
					'show_in_admin_bar'   => false,
					'can_export'          => true,
					'has_archive'         => false,
					'exclude_from_search' => true,
					'publicly_queryable'  => false,
					'capabilities'        => $this->get_capabilities(),
				)
			);


			register_post_type( $this->post_type_name, $args );

		}

		/**
		 * get pending order survey capabilities
		 * @author YIThemes
		 * @since 1.0.0
		 * @return array
		 */
		public function get_capabilities() {

			$capability_type = 'pending_order_survey';
			$caps            = array(
				'edit_post'              => "edit_{$capability_type}",
				'read_post'              => "read_{$capability_type}",
				'delete_post'            => "delete_{$capability_type}",
				'edit_posts'             => "edit_{$capability_type}s",
				'edit_others_posts'      => "edit_others_{$capability_type}s",
				'publish_posts'          => "publish_{$capability_type}s",
				'read_private_posts'     => "read_private_{$capability_type}s",
				'read'                   => "read",
				'delete_posts'           => "delete_{$capability_type}s",
				'delete_private_posts'   => "delete_private_{$capability_type}s",
				'delete_published_posts' => "delete_published_{$capability_type}s",
				'delete_others_posts'    => "delete_others_{$capability_type}s",
				'edit_private_posts'     => "edit_private_{$capability_type}s",
				'edit_published_posts'   => "edit_published_{$capability_type}s",
				'create_posts'           => "edit_{$capability_type}s",
				'manage_posts'           => "manage_{$capability_type}s",
			);

			return $caps;
		}

		/**
		 * add capabilities for administrato and for shop_manager
		 * @author YIThemes
		 * @since 1.0.0
		 */
		public function add_capabilities() {

			$admin        = get_role( 'administrator' );
			$shop_manager = get_role( 'shop_manager' );

			$caps = $this->get_capabilities();

			foreach ( $caps as $key => $cap ) {

				$admin->add_cap( $cap );
				$shop_manager->add_cap( $cap );
			}
		}

		/**
		 * Get the tab taxonomy label
		 *
		 * @param   $arg string The string to return. Defaul empty. If is empty return all taxonomy labels
		 *
		 * @author YIThemes
		 * @since  1.0.0
		 * @return Array taxonomy label
		 *
		 */
		protected function get_pending_order_survey_taxonomy_label( $arg = '' ) {

			$labels = array(
				'name'               => _x( 'Email Templates', 'Post Type General Name', 'yith-woocommerce-pending-order-survey' ),
				'singular_name'      => _x( 'Email Template', 'Post Type Singular Name', 'yith-woocommerce-pending-order-survey' ),
				'menu_name'          => __( 'Email Template', 'yith-woocommerce-pending-order-survey' ),
				'parent_item_colon'  => __( 'Parent Item:', 'yith-woocommerce-pending-order-survey' ),
				'all_items'          => __( 'All Email Templates', 'yith-woocommerce-pending-order-survey' ),
				'view_item'          => __( 'View Email Templates', 'yith-woocommerce-pending-order-survey' ),
				'add_new_item'       => __( 'Add New Email Template', 'yith-woocommerce-pending-order-survey' ),
				'add_new'            => __( 'Add New Email Template', 'yith-woocommerce-pending-order-survey' ),
				'edit_item'          => __( 'Edit Email Template', 'yith-woocommerce-pending-order-survey' ),
				'update_item'        => __( 'Update Email Template', 'yith-woocommerce-pending-order-survey' ),
				'search_items'       => __( 'Search Email Template', 'yith-woocommerce-pending-order-survey' ),
				'not_found'          => __( 'Not found', 'yith-woocommerce-pending-order-survey' ),
				'not_found_in_trash' => __( 'Not found in Trash', 'yith-woocommerce-pending-order-survey' ),
			);

			return ! empty( $arg ) ? $labels[ $arg ] : $labels;
		}

		/**
		 * Customize the messages for Pending Order Survey
		 *
		 * @param $messages
		 *
		 * @author Yithemes
		 *
		 * @return array
		 * @fire post_updated_messages filter
		 */
		public function custom_pending_order_survey_email_messages( $messages ) {

			$singular_name                     = $this->get_pending_order_survey_taxonomy_label( 'singular_name' );
			$messages[ $this->post_type_name ] = array(

				0  => '',
				1  => sprintf( __( '%s updated', 'yith-woocommerce-pending-order-survey' ), $singular_name ),
				2  => __( 'Custom field updated', 'yith-woocommerce-pending-order-survey' ),
				3  => __( 'Custom field deleted', 'yith-woocommerce-pending-order-survey' ),
				4  => sprintf( __( '%s updated', 'yith-woocommerce-pending-order-survey' ), $singular_name ),
				5  => isset( $_GET['revision'] ) ? sprintf( __( 'Email template restored to version %s', 'yith-woocommerce-pending-order-survey'
				), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
				6  => sprintf( __( '%s published', 'yith-woocommerce-pending-order-survey' ), $singular_name ),
				7  => sprintf( __( '%s saved', 'yith-woocommerce-pending-order-survey' ), $singular_name ),
				8  => sprintf( __( '%s submitted', 'yith-woocommerce-pending-order-survey' ), $singular_name ),
				9  => sprintf( __( '%s', 'yith-woocommerce-pending-order-survey' ), $singular_name ),
				10 => sprintf( __( '%s draft updated', 'yith-woocommerce-pending-order-survey' ), $singular_name )
			);

			return $messages;
		}

		/**
		 * send email via ajax
		 * @author YIThemes
		 * @since 1.0.0
		 */
		public function ajax_send_pending_email() {

			if ( ! isset( $_REQUEST['ywcpos_order_id'] ) && ! isset( $_REQUEST['ywcpos_template'] ) ) {
				return;
			}

			$email_id = $_REQUEST['ywcpos_template'];
			$lang     = get_post_meta( $_REQUEST['ywcpos_order_id'], 'ywcpos_language', true );

			if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
				$email_id = ( function_exists( 'wpml_object_id_filter' ) ) ? wpml_object_id_filter( $email_id, 'ywcpos_survey_email', true, $lang ) : icl_object_id( $email_id, 'ywcpos_survey_email', true, $lang );
			}

			$email_template = get_post( $email_id );

			if ( ! empty( $email_template ) && apply_filters( 'ywcpos_send_email', true, $_REQUEST['ywcpos_order_id'] ) ) {
				$result = $this->send_pending_email( $_REQUEST['ywcpos_order_id'], $email_template, $lang );
			} else {
				$result = 'no';
			}


			wp_send_json( $result );

		}

		/**
		 * send all email
		 * @author YIThemes
		 * @since 1.0.0
		 */
		public function send_pending_email_cron() {

			if ( ! apply_filters( 'ywcpos_send_email_manually', false ) ) {

				$args      = array(
					'posts_per_page'  => - 1,
					'post_type'       => 'ywcpos_survey_email',
					'post_status'     => 'publish',
					'suppress_filter' => false,
					'meta_query'      => array(
						array(
							'key'     => '_ywcpos_enable_email',
							'value'   => '1',
							'compare' => '='
						)
					)
				);
				$all_email = get_posts( $args );

				if ( ! empty( $all_email ) ) {

					global $wpdb;
					foreach ( $all_email as $email ) {

						$time      = get_post_meta( $email->ID, '_ywcpos_send_after', true );
						$order_ids = get_post_meta( $email->ID, '_ywcpos_order_email_send', true );

						$order_ids = empty( $order_ids ) ? array() : $order_ids;

						if ( $time == '' ) {
							continue;
						}


						$date = date( "Y-m-d H:i:s", strtotime( '-' . absint( $time ) . ' MINUTES', current_time( 'timestamp' ) ) );


						$pending_orders = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT posts.ID
                                                                      FROM {$wpdb->posts} AS posts INNER JOIN {$wpdb->postmeta} AS meta ON posts.ID = meta.post_id
                                                                      WHERE 	posts.post_type = 'shop_order'
                                                                      AND 	posts.post_status = 'wc-pending'
                                                                      AND 	posts.post_modified < %s
                                                                      AND   posts.post_parent = 0
                                                                      AND ( meta.meta_key= %s AND meta.meta_value= %s ) ", $date, '_ywcpos_is_pending', 'yes' ) );

						if ( $pending_orders ) {

							foreach ( $pending_orders as $order_id ) {

								if ( ! in_array( $order_id, $order_ids ) && apply_filters( 'ywcpos_send_email', true, $order_id ) ) {
									$this->send_pending_email( $order_id, $email, '' );
								}
							}
						}


					}
				}
			}
		}

		/**
		 * send single email
		 * @author YIThemes
		 * @since 1.0.0
		 *
		 * @param $order_id
		 * @param $template
		 * @param $lang
		 *
		 * @return array
		 */
		public function send_pending_email( $order_id, $template, $lang ) {

			$order           = wc_get_order( $order_id );

			$user_first_name = $order->get_billing_first_name();
			$user_last_name  = $order->get_billing_last_name();
			$user_email      = $order->get_billing_email();


			$order_content = ywcpos_get_email_template_order_content( $order );


			$email_sender_name = get_option( 'ywcpos_user_sender_name' );
			$email_sender      = get_option( 'ywcpos_user_email_sender' );
			$email_reply_to    = get_option( 'ywcpos_user_email_reply' );
			$email_subject     = get_post_meta( $template->ID, '_ywcpos_email_subject', true );
			$template_content  = nl2br( $template->post_content );

			$order_link = ( wp_nonce_url( $order->get_checkout_payment_url(), 'ywcpos_pay_for_order', '_ywcpos_order' ) );
			$order_link = esc_url( add_query_arg( array( 'email_id' => $template->ID ), $order_link ) );

			$template_content = str_replace( '{{ywcpos_firstname}}', $user_first_name, $template_content );
			$template_content = str_replace( '{{ywcpos_lastname}}', $user_last_name, $template_content );
			$template_content = str_replace( '{{ywcpos_fullname}}', $user_first_name . ' ' . $user_last_name, $template_content );
			$template_content = str_replace( '{{ywcpos_useremail}}', $user_email, $template_content );
			$template_content = str_replace( '{{ywcpos_order}}', $order_content, $template_content );
			$template_content = str_replace( '{{ywcpos_link}}', $order_link, $template_content );

			$reg = '/{{ywcpos_pending_survey=\d+}}/';

			preg_match( $reg, $template_content, $match );
			$survey_id = '';
			if ( $match ) {

				$survey_placeholder = $match[0];

				$reg_survey = '/\d+/';

				preg_match( $reg_survey, $survey_placeholder, $survey_id );

				if ( $survey_id ) {
					$survey_id = $survey_id[0];

					$survey_title = get_the_title( $survey_id );
					$survey_url   = get_the_permalink( $survey_id );
					$url_params   = array(
						'email_id' => $template->ID,
						'order_id' => $order_id
					);

					$survey_url  = esc_url( add_query_arg( $url_params, $survey_url ) );
					$survey_link = sprintf( '<a href="%s" target="_blank">%s</a>', $survey_url, $survey_title );

					$template_content = str_replace( $survey_placeholder, $survey_link, $template_content );
				}
			}


			//check if a coupon must be send with the email
			$pos = strpos( $template_content, '{{ywcpos_coupon}}' );
			if ( $pos !== false ) {
				$coupon_code = $this->create_coupon( $template->ID );
				if ( $coupon_code ) {
					$template_content = str_replace( '{{ywcpos_coupon}}', $coupon_code, $template_content );
					update_post_meta( $order_id, '_ywcpos_coupon_code', $coupon_code );
				} else {
					$template_content = str_replace( '{{ywcpos_coupon}}', '', $template_content );
				}
			}

			$args = array(
				'order_id'       => $order_id,
				'email_id'       => $template->ID,
				'email_name'     => $template->post_title,
				'survey_id'      => $survey_id,
				'user_email'     => $user_email,
				'email_content'  => $template_content,
				'email_heading'  => $email_sender_name,
				'email_sender'   => $email_sender,
				'email_reply_to' => $email_reply_to,
				'email_subject'  => $email_subject,
			);

			do_action( 'send_wcpos_mail', $args );

			$result = get_post_meta( $order_id, '_ywcpos_email_sent', true );

			return $result;
		}

		/**
		 * create a coupon for current email
		 * @autho YIThemes
		 * @since 1.0.0
		 *
		 * @param $email_id
		 *
		 * @return bool|string
		 */
		public function create_coupon( $email_id ) {

			$amount = get_post_meta( $email_id, '_ywcpos_coupon_value', true );

			if ( empty( $amount ) || $amount == 0 ) {
				return false;
			}

			$prefix        = get_option( 'ywcpos_coupon_prefix' );
			$coupon_code   = uniqid( strtolower( $prefix ) . '_' ); // Code
			$discount_type = get_post_meta( $email_id, '_ywcpos_coupon_type', true );
			$expiry_time   = current_time( 'timestamp' ) + get_post_meta( $email_id, '_ywcpos_coupon_validity', true ) * 24 * 3600;

			$coupon = array(
				'post_title'   => $coupon_code,
				'post_content' => '',
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_type'    => 'shop_coupon'
			);

			$new_coupon_id = wp_insert_post( $coupon );

			update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
			update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
			update_post_meta( $new_coupon_id, 'individual_use', 'no' );
			update_post_meta( $new_coupon_id, 'product_ids', '' );
			update_post_meta( $new_coupon_id, 'exclude_product_ids', '' );
			update_post_meta( $new_coupon_id, 'usage_limit', '1' );
			update_post_meta( $new_coupon_id, 'expiry_date', date( 'Y-m-d', $expiry_time ) );
			update_post_meta( $new_coupon_id, 'apply_before_tax', 'yes' );
			update_post_meta( $new_coupon_id, 'free_shipping', 'no' );

			return $coupon_code;
		}

		public function add_custom_email_metaboxes( $args ) {
			global $post;

			if ( isset( $post ) && $this->post_type_name === $post->post_type ) {
				if ( 'pending_survey_email_report' == $args['type'] ) {
					$args['basename'] = YITH_WCPO_SURVEY_DIR;
					$args['path']     = 'metaboxes/types/';
				}

			}

			return $args;
		}

		/**
		 * @param bool $send_email
		 * @param int $order_id
		 */
		public function check_if_can_send_email( $send_email, $order_id ) {

			if ( 'yes' == get_option( 'ywcpos_user_privacy', 'no' ) ) {

				$not_send_email = get_post_meta( $order_id, '_ywcpos_not_send', true );

				if ( $not_send_email === 'yes' ) {

					$send_email = false;
				}
			}

			return $send_email;
		}

	}
}
/** return Pending Order Survey Email PostType
 * @author YIThemes
 * @since 1.0.0
 * @return YITH_WCOP_Survey_Email_Post_Type
 */
function YITH_Pending_Email_Type() {

	return YITH_WCOP_Survey_Email_Post_Type::get_instance();
}