<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_Delivery_Date_Processing_Method' ) ) {

	class YITH_Delivery_Date_Processing_Method {

		protected static $_instance;
		protected $capability_name;

		public function __construct() {
			$this->capability_name = 'delivery_date_processing_method';
			add_action( 'init', array( $this, 'register_post_type' ), 16 );
			add_action( 'admin_init', array( $this, 'add_capabilities' ) );
			add_action( 'admin_init', array( $this, 'add_meta_boxes' ) );
			add_filter( 'yit_fw_metaboxes_type_args', array( $this, 'add_custom_type_metaboxes' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'include_admin_scripts' ), 25 );
			add_action( 'save_post', array( $this, 'save_processing_meta' ), 1, 2 );
			add_action( 'edit_form_top', array( $this, 'add_return_to_list_button' ) );

			add_filter( 'ywcdd_processing_method_metaboxes', array( $this, 'remove_unnecessary_field' ), 10, 1 );


		}

		/**
		 * @return YITH_Delivery_Date_Processing_Method
		 * @since 1.0.0
		 * @author YITHEMES
		 */
		public static function get_instance() {

			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * get post_type capabilities
		 * @return array
		 * @since 1.0.0
		 * @author YITHEMES
		 */
		public function get_capability() {

			$caps = array(
				'edit_post'              => "edit_{$this->capability_name}",
				'read_post'              => "read_{$this->capability_name}",
				'delete_post'            => "delete_{$this->capability_name}",
				'edit_posts'             => "edit_{$this->capability_name}s",
				'edit_others_posts'      => "edit_others_{$this->capability_name}s",
				'publish_posts'          => "publish_{$this->capability_name}s",
				'read_private_posts'     => "read_private_{$this->capability_name}s",
				'read'                   => "read",
				'delete_posts'           => "delete_{$this->capability_name}s",
				'delete_private_posts'   => "delete_private_{$this->capability_name}s",
				'delete_published_posts' => "delete_published_{$this->capability_name}s",
				'delete_others_posts'    => "delete_others_{$this->capability_name}s",
				'edit_private_posts'     => "edit_private_{$this->capability_name}s",
				'edit_published_posts'   => "edit_published_{$this->capability_name}s",
				'create_posts'           => "edit_{$this->capability_name}s",
				'manage_posts'           => "manage_{$this->capability_name}s",
			);

			return apply_filters( 'yith_delivery_date_processing_method_capability', $caps );
		}

		/**
		 * register delivery date carrier post type
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function register_post_type() {

			$args = apply_filters( 'yith_delivery_date_processing_method_post_type', array(
					'label'               => $this->get_taxonomy_label( 'name' ),
					'description'         => '',
					'labels'              => $this->get_taxonomy_label(),
					'supports'            => array( 'title' ),
					'hierarchical'        => false,
					'public'              => false,
					'show_ui'             => true,
					'show_in_menu'        => false,
					'menu_position'       => 56,
					'show_in_nav_menus'   => false,
					'show_in_admin_bar'   => false,
					'can_export'          => false,
					'has_archive'         => false,
					'exclude_from_search' => true,
					'publicly_queryable'  => false,
					'capability_type'     => $this->capability_name,
					'capabilities'        => $this->get_capability(),
				)
			);

			register_post_type( 'yith_proc_method', $args );
			flush_rewrite_rules();
		}

		/**
		 * Get the taxonomy label
		 *
		 * @param   $arg string The string to return. Default empty. If is empty return all taxonomy labels
		 *
		 * @return array taxonomy label
		 *
		 * @since  1.0.0
		 * @author YITHEMES
		 */
		public function get_taxonomy_label( $arg = '' ) {

			$label = apply_filters( 'yith_delivery_date_processing_method_taxonomy_label', array(
					'name'               => _x( 'Processing Method', 'post type general name', 'yith-woocommerce-delivery-date' ),
					'singular_name'      => _x( 'Processing Method', 'post type singular name', 'yith-woocommerce-delivery-date' ),
					'menu_name'          => __( 'Processing Method', 'yith-woocommerce-delivery-date' ),
					'parent_item_colon'  => __( 'Parent Item:', 'yith-woocommerce-delivery-date' ),
					'all_items'          => __( 'All methods', 'yith-woocommerce-delivery-date' ),
					'view_item'          => __( 'View method', 'yith-woocommerce-delivery-date' ),
					'add_new_item'       => __( 'Add new Processing Method', 'yith-woocommerce-delivery-date' ),
					'add_new'            => __( 'Add new Processing Method', 'yith-woocommerce-delivery-date' ),
					'edit_item'          => __( 'Edit Processing Method', 'yith-woocommerce-delivery-date' ),
					'update_item'        => __( 'Update Processing Method', 'yith-woocommerce-delivery-date' ),
					'search_items'       => __( 'Search Processing Method', 'yith-woocommerce-delivery-date' ),
					'not_found'          => __( 'No method found', 'yith-woocommerce-delivery-date' ),
					'not_found_in_trash' => __( 'No method found in Trash', 'yith-woocommerce-delivery-date' ),
				)
			);

			return ! empty( $arg ) ? $label[ $arg ] : $label;
		}

		/**
		 * add capabilities
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function add_capabilities() {


			$caps = $this->get_capability();

			// gets the admin and shop_mamager roles
			$admin        = get_role( 'administrator' );
			$shop_manager = get_role( 'shop_manager' );

			foreach ( $caps as $key => $cap ) {

				$admin->add_cap( $cap );
				$shop_manager->add_cap( $cap );
			}
		}

		/**
		 * add meta boxes for post type processing method
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function add_meta_boxes() {

			$post_id = isset( $_GET['post'] ) ? $_GET['post'] : false;


			if ( ( $post_id && 'yith_proc_method' === get_post_type( $post_id ) ) || ( isset( $_GET['post_type'] ) && 'yith_proc_method' === $_GET['post_type'] ) ) {

				/**
				 * @var $metaboxes array metabox_id, metabox_opt
				 */
				$metaboxes = array(
					'yith-processing-method-metaboxes' => 'processing-method-meta-boxes-options.php',

				);

				if ( ! function_exists( 'YIT_Metabox' ) ) {
					require_once( YITH_DELIVERY_DATE_DIR . 'plugin-fw/yit-plugin.php' );
				}

				foreach ( $metaboxes as $key => $metabox ) {
					$args = require_once( YITH_DELIVERY_DATE_TEMPLATE_PATH . '/meta-boxes/' . $metabox );
					$box  = YIT_Metabox( $key );
					$box->init( $args );
				}
			}
		}

		/**
		 * show custom metabox type
		 *
		 * @param $args
		 *
		 * @return mixed
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function add_custom_type_metaboxes( $args ) {
			global $post;


			if ( isset( $post ) && 'yith_proc_method' === $post->post_type ) {

				$custom_types = array( 'select_carrier', 'check_list_day', 'select-shipping-method' );
				if ( in_array( $args['type'], $custom_types ) ) {
					$args['basename'] = YITH_DELIVERY_DATE_DIR;
					$args['path']     = 'meta-boxes/types/';
				}

			}

			return $args;
		}

		/**
		 * include admin scripts
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function include_admin_scripts() {

			global $post;


			if ( ( isset( $post ) && 'yith_proc_method' === get_post_type( $post->ID ) ) || ( isset( $_GET['post_type'] ) && 'yith_proc_method' === $_GET['post_type'] ) ) {

				wp_enqueue_script( 'ywcdd_timepicker' );
				wp_enqueue_style( 'ywcdd_timepicker_style' );
				wp_enqueue_script( 'yith_wcdd_processing_method' );
				wp_enqueue_style( 'ywcdd_processing_method_metaboxes' );

				$params = array(
					'ajax_url'     => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
					'timeformat'   => 'H:i',
					'timestep'     => get_option( 'ywcdd_timeslot_step', 30 ),
					'dateformat'   => get_option( 'date_format' ),
					'plugin_nonce' => YITH_DELIVERY_DATE_SLUG,

				);

				wp_localize_script( 'yith_wcdd_processing_method', 'yith_delivery_parmas', $params );
			}
		}

		public function save_processing_meta( $post_id, $post ) {

			if ( empty( $post_id ) || empty( $post ) ) {
				return;
			}
			// Dont' save meta boxes for revisions or autosaves
			if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
				return;
			}

			// Check the nonce
			if ( empty( $_POST['yit_metaboxes_nonce'] ) || ! wp_verify_nonce( $_POST['yit_metaboxes_nonce'], 'metaboxes-fields-nonce' ) ) {
				return;
			}

			// Check the post being saved == the $post_id to prevent triggering this call for other save_post events
			if ( empty( $_POST['post_ID'] ) || (int) $_POST['post_ID'] !== $post_id ) {
				return;
			}

			// Check user has permission to edit
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			if ( 'yith_proc_method' === get_post_type( $post_id ) ) {

				$min_days   = isset( $_POST['yit_metaboxes']['_ywcdd_minworkday'] ) ? $_POST['yit_metaboxes']['_ywcdd_minworkday'] : false;
				$enable_day = isset( $_POST['ywcdd_enable_day'] ) ? $_POST['ywcdd_enable_day'] : false;
				$timelimit  = isset( $_POST['ywcdd_timelimit'] ) ? $_POST['ywcdd_timelimit'] : false;


				update_post_meta( $post_id, '_ywcdd_minworkday', $min_days );


				if ( $enable_day && $timelimit ) {

					$select_day = array();

					foreach ( $enable_day as $key => $enable ) {

						$new_opt      = array( 'day' => $key, 'timelimit' => $timelimit[ $key ], 'enabled' => $enable );
						$select_day[] = $new_opt;
					}

					update_post_meta( $post_id, '_ywcdd_list_day', $select_day );
				}

				$all_day = isset( $_POST['ywcdd_all_day'] ) ? $_POST['ywcdd_all_day'] : 'no';
				update_post_meta( $post_id, '_ywcdd_all_day', $all_day );

			}

			$type   = get_post_meta( $post_id, '_ywcdd_type_checkout', true );
			$option = get_option( 'ywcdd_processing_type', 'checkout' );

			if ( 'checkout' == $option ) {

				$carrier_select = isset( $_POST['yit_metaboxes']['_ywcdd_carrier'] ) ? $_POST['yit_metaboxes']['_ywcdd_carrier'] : array();
				$carrier_select = array_map( "intval", $carrier_select );

				update_post_meta( $post_id, '_ywcdd_carrier', $carrier_select );
			}

			if ( empty( $type ) ) {


				$type = 'checkout' == $option ? 'yes' : 'no';

				update_post_meta( $post_id, '_ywcdd_type_checkout', $type );
			}
		}

		/**
		 * @param $post_id
		 *
		 * @return mixed
		 */
		public function get_carriers( $post_id ) {

			$carriers = get_post_meta( $post_id, '_ywcdd_carrier', true );

			return $carriers;
		}


		public function get_work_days( $processing_method_id ) {

			$works_day     = get_post_meta( $processing_method_id, '_ywcdd_list_day', true );
			$new_works_day = array();
			if ( ! empty( $works_day ) ) {
				foreach ( $works_day as $key => $wday ) {

					if ( $wday['enabled'] == 'yes' ) {
						$new_works_day[ $wday['day'] ] = $wday;
					}
				}
			}

			return apply_filters( 'ywcdd_get_processing_work_days', $new_works_day, $processing_method_id );
		}

		/**
		 * @param int $processing_method_id
		 *
		 * @return int
		 */
		public function get_min_working_day( $processing_method_id ) {

			$base_day = get_post_meta( $processing_method_id, '_ywcdd_minworkday', true );

			$base_day = empty( $base_day ) ? 0 : $base_day;
			/**
			 * this filter is deprecated from version 2.0
			 */
			$base_day = apply_filters( 'yith_delivery_date_base_shipping_day', $base_day, $processing_method_id );

			return apply_filters( 'ywcdd_get_processing_working_day', $base_day, $processing_method_id );
		}

		/**
		 * add a button in single post for back to list
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function add_return_to_list_button() {

			global $post;

			if ( isset( $post ) && 'yith_proc_method' === $post->post_type ) {
				$admin_url = admin_url( 'admin.php' );
				$params    = array(
					'page' => 'yith_delivery_date_panel',
					'tab'  => 'processing-method'
				);

				$list_url = apply_filters( 'ywcdd_processing_method_back_link', esc_url( add_query_arg( $params, $admin_url ) ) );
				$button   = sprintf( '<a href="%1$s" title="%2$s" class="ywcdd_back_to">%2$s</a>', $list_url,
					__( 'Back to Processing Methods',
						'yith-woocommerce-delivery-date' ) );
				echo $button;
			}
		}

		/**
		 * @param array $args
		 *get all processing methods
		 *
		 * @return array
		 */
		public function get_processing_method( $args = array() ) {

			$default = array(
				'suppress_filter' => false,
				'post_type'       => 'yith_proc_method',
				'post_status'     => 'publish',
				'posts_per_page'  => - 1
			);

			$default = wp_parse_args( $args, $default );

			return get_posts( $default );
		}

		public function get_formatted_processing_method() {

			$type = get_option( 'ywcdd_processing_type', 'checkout' );
			$type = 'checkout' == $type ? 'yes' : 'no';

			$query_args               = array(
				'fields' => 'ids'
			);
			$query_args['meta_query'] = array(
				array(
					'key'     => '_ywcdd_type_checkout',
					'value'   => $type,
					'compare' => '='
				)
			);

			$posts_id = $this->get_processing_method( $query_args );


			$results = array();

			foreach ( $posts_id as $post_id ) {
				$results[ $post_id ] = get_the_title( $post_id );
			}

			return $results;

		}

		/**
		 * @param array $metabox_options
		 *
		 * @return array
		 */
		public function remove_unnecessary_field( $metabox_options ) {

			$option = get_option( 'ywcdd_processing_type', 'checkout' );

			if ( 'product' == $option ) {

				if ( isset( $metabox_options['tabs']['processing_method_settings']['fields']['ywcdd_carrier'] ) ) {

					unset( $metabox_options['tabs']['processing_method_settings']['fields']['ywcdd_carrier'] );
				}

				if ( isset( $metabox_options['tabs']['processing_method_settings']['fields']['ywcdd_shipping_method'] ) ) {

					unset( $metabox_options['tabs']['processing_method_settings']['fields']['ywcdd_shipping_method'] );
				}
			}

			return $metabox_options;
		}
	}
}


if ( ! function_exists( 'YITH_Delivery_Date_Processing_Method' ) ) {

	function YITH_Delivery_Date_Processing_Method() {
		return YITH_Delivery_Date_Processing_Method::get_instance();
	}
}

YITH_Delivery_Date_Processing_Method();
