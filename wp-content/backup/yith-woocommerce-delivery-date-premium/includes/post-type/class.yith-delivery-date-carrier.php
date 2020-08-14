<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'YITH_Delivery_Date_Carrier' ) ) {

	class YITH_Delivery_Date_Carrier {

		protected static $_instance;
		protected $capability_name;

		public function __construct() {
			$this->capability_name = 'delivery_date_carrier';
			add_action( 'init', array( $this, 'register_post_type' ), 16 );
			add_action( 'admin_init', array( $this, 'add_capabilities' ) );
			add_action( 'admin_init', array( $this, 'add_meta_boxes' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'include_admin_scripts' ), 25 );
			add_filter( 'yit_fw_metaboxes_type_args', array( $this, 'add_custom_type_metaboxes' ) );
			add_action( 'save_post', array( $this, 'save_carrier_meta' ),1, 2 );

			add_action( 'wp_ajax_add_carrier_time_slot', array( $this, 'add_carrier_time_slot' ) );
			add_action( 'wp_ajax_enable_disable_time_slot', array( $this, 'enable_disable_time_slot' ) );
			add_action( 'wp_ajax_update_carrier_time_slot', array( $this, 'update_carrier_time_slot' ) );
			add_action( 'wp_ajax_delete_carrier_time_slot', array( $this, 'delete_carrier_time_slot' ) );
			add_action( 'edit_form_top', array( $this, 'add_return_to_list_button' ) );

		}

		/**
		 * @author YITHEMES
		 * @since 1.0.0
		 * @return YITH_Delivery_Date_Carrier
		 */
		public static function get_instance() {

			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * get post_type capabilities
		 * @author YITHEMES
		 * @since 1.0.0
		 * @return array
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

			return apply_filters( 'yith_delivery_date_carrier_capability', $caps );
		}

		/**
		 * register delivery date carrier post type
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function register_post_type() {

			$args = apply_filters( 'yith_delivery_date_carrier_post_type', array(
					'label'               => $this->get_taxonomy_label( 'name' ),
					'description'         => '',
					'labels'              => $this->get_taxonomy_label(),
					'supports'            => array( 'title' ),
					'hierarchical'        => false,
					'public'              => false,
					'show_ui'             => true,
					'show_in_menu'        => false,
					'menu_position'       => 57,
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

			register_post_type( 'yith_carrier', $args );
			flush_rewrite_rules();
		}

		/**
		 * Get the taxonomy label
		 *
		 * @param   $arg string The string to return. Default empty. If is empty return all taxonomy labels
		 *
		 * @author YITHEMES
		 * @since  1.0.0
		 * @return array taxonomy label
		 *
		 */
		public function get_taxonomy_label( $arg = '' ) {

			$label = apply_filters( 'yith_delivery_date_taxonomy_label', array(
					'name'               => _x( 'Carrier', 'post type general name', 'yith-woocommerce-delivery-date' ),
					'singular_name'      => _x( 'Carrier', 'post type singular name', 'yith-woocommerce-delivery-date' ),
					'menu_name'          => __( 'Carrier', 'yith-woocommerce-delivery-date' ),
					'parent_item_colon'  => __( 'Parent Item:', 'yith-woocommerce-delivery-date' ),
					'all_items'          => __( 'All carriers', 'yith-woocommerce-delivery-date' ),
					'view_item'          => __( 'View carrier', 'yith-woocommerce-delivery-date' ),
					'add_new_item'       => __( 'Add new carrier', 'yith-woocommerce-delivery-date' ),
					'add_new'            => __( 'Add new carrier', 'yith-woocommerce-delivery-date' ),
					'edit_item'          => __( 'Edit carrier', 'yith-woocommerce-delivery-date' ),
					'update_item'        => __( 'Update carrier', 'yith-woocommerce-delivery-date' ),
					'search_items'       => __( 'Search carrier', 'yith-woocommerce-delivery-date' ),
					'not_found'          => __( 'No carrier found', 'yith-woocommerce-delivery-date' ),
					'not_found_in_trash' => __( 'No carrier found in Trash', 'yith-woocommerce-delivery-date' ),
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
		 * add meta boxes for post type carrier
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function add_meta_boxes() {

			$post_id = isset( $_GET['post'] ) ? $_GET['post'] : false;
			$save_toggle_action = isset( $_REQUEST['action'] ) && 'yith_plugin_fw_save_toggle_element_metabox' == $_REQUEST['action'];
			if ( ! function_exists( 'YIT_Metabox' ) ) {
				require_once( YITH_DELIVERY_DATE_DIR . 'plugin-fw/yit-plugin.php' );
			}

			if ( ( $post_id && 'yith_carrier' === get_post_type( $post_id ) ) || ( isset( $_GET['post_type'] ) && 'yith_carrier' === $_GET['post_type'] )  ) {

				/**
				 * @var $metaboxes array metabox_id, metabox_opt
				 */
				$metaboxes = array(
					'yit-carrier-metaboxes'           => 'carrier-meta-boxes-options.php',
					'yit-carrier-time-slot-metaboxes' => 'carrier-time-slot-meta-boxes-options.php',
				);


				foreach ( $metaboxes as $key => $metabox ) {
					$args = require_once( YITH_DELIVERY_DATE_TEMPLATE_PATH . '/meta-boxes/' . $metabox );
					$box  = YIT_Metabox( $key );
					$box->init( $args );
				}
			}

			if( $save_toggle_action ){
				/**
				 * @var $metaboxes array metabox_id, metabox_opt
				 */
				$metaboxes = array(
					'yit-carrier-time-slot-metaboxes' => 'carrier-time-slot-meta-boxes-options.php',
				);

				foreach ( $metaboxes as $key => $metabox ) {
					$args = require_once( YITH_DELIVERY_DATE_TEMPLATE_PATH . '/meta-boxes/' . $metabox );
					$box  = YIT_Metabox( $key );
					$box->init( $args );
				}
			}
		}

		/**
		 * show custom metabox type
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param $args
		 *
		 * @return mixed
		 */
		public function add_custom_type_metaboxes( $args ) {
			global $post;


			if ( isset( $post ) && 'yith_carrier' === $post->post_type ) {

				$custom_types = array( 'dayrange', 'multiselectday', 'addtimeslot', 'list-timeslot', 'number-select' );
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


			if ( ( isset( $post ) && 'yith_carrier' === get_post_type( $post->ID ) ) || ( isset( $_GET['post_type'] ) && 'yith_carrier' === $_GET['post_type'] ) ) {

				wp_enqueue_script( 'ywcdd_timepicker' );
				wp_enqueue_style( 'ywcdd_timepicker_style' );
				wp_enqueue_script( 'yith_wcdd_carrier' );
				$params = array(
					'ajax_url'     => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
					'actions'      => array(
						'add_carrier_time_slot'    => 'add_carrier_time_slot',
						'enable_disable_time_slot' => 'enable_disable_time_slot',
						'update_carrier_time_slot' => 'update_carrier_time_slot',
						'delete_carrier_time_slot' => 'delete_carrier_time_slot',
					),
					'empty_row'    => sprintf( '<tr class="no-items"><td class="colspanchange" colspan="6">%s</td></tr>', __( 'No item found.', 'yith-woocommerce-delivery-date' ) ),
					'timeformat'   => 'H:i',
					'timestep'     => get_option( 'ywcdd_timeslot_step', 30 ),
					'dateformat'   => get_option( 'date_format' ),
					'plugin_nonce' => YITH_DELIVERY_DATE_SLUG,
					'disable_timeslot_metabox' => 'checkout' !== get_option( 'ywcdd_processing_type' , 'checkout' )

				);

				wp_localize_script( 'yith_wcdd_carrier', 'yith_delivery_parmas', $params );

				wp_enqueue_style( 'ywcdd_carrier_metaboxes' );


			}
		}

		/**
		 * @author YITHEMES
		 * @since 1.0.0
		 *
		 * @param $post_id
		 */
		public function save_carrier_meta( $post_id, $post ) {
			if ( empty( $post_id ) || empty( $post )  ) {
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

			if ( 'yith_carrier' === get_post_type( $post_id ) ) {

				$dayrange   = isset( $_POST['yit_metaboxes']['_ywcdd_dayrange'] ) ? $_POST['yit_metaboxes']['_ywcdd_dayrange'] : '';
				$workday    = isset( $_POST['yit_metaboxes']['_ywcdd_workday'] ) ? $_POST['yit_metaboxes']['_ywcdd_workday'] : '';
				$select_day = isset( $_POST['yit_metaboxes']['_ywcdd_max_selec_orders'] ) ? $_POST['yit_metaboxes']['_ywcdd_max_selec_orders'] : 30;


				update_post_meta( $post_id, '_ywcdd_dayrange', $dayrange );
				update_post_meta( $post_id, '_ywcdd_workday', $workday );
				update_post_meta( $post_id, '_ywcdd_max_selec_orders', $select_day );

				if ( isset( $_POST['_ywcdd_addtimeslot'] ) ) {

					update_post_meta( $post_id, '_ywcdd_addtimeslot', $_POST['_ywcdd_addtimeslot'] );

				}
			}
		}

		/**
		 * add new carrier time slot via ajax
		 * @author YITH
		 * @since 1.0.0
		 */
		public function add_carrier_time_slot() {

			if ( isset( $_POST['ywcdd_carrier_id'] ) ) {

				$carrier_id    = $_POST['ywcdd_carrier_id'];
				$metakey       = $_POST['ywcdd_metakey'];
				$slot_name     = $_POST['ywcdd_slot_name'];
				$timefrom      = $_POST['ywcdd_time_from'];
				$timeto        = $_POST['ywcdd_time_to'];
				$max_order     = $_POST['ywcdd_max_order'];
				$fee_name      = $_POST['ywcdd_fee_name'];
				$fee           = $_POST['ywcdd_fee'];
				$override_days = 'no';
				$enabled       = 'yes';
				$days          = array();

				$carrier_timeslot = get_post_meta( $carrier_id, $metakey, true );
				$carrier_timeslot = empty( $carrier_timeslot ) ? array() : $carrier_timeslot;
				$id               = uniqid( 'ywcdd_carrier_' . $carrier_id . '_timeslot_' );
				$newslot          = array(
					'enabled'       => $enabled,
					'slot_name'     => $slot_name,
					'timefrom'      => $timefrom,
					'timeto'        => $timeto,
					'max_order'     => $max_order,
					'fee_name'      => $fee_name,
					'fee'           => $fee,
					'override_days' => $override_days,
					'day_selected'  => $days
				);

				$carrier_timeslot[ $id ] = $newslot;
				update_post_meta( $carrier_id, $metakey, $carrier_timeslot );

				$template = '';

				$post_id = $carrier_id;

				ob_start();
				include( YITH_DELIVERY_DATE_TEMPLATE_PATH . '/meta-boxes/carrier-time-slot-view.php' );
				$template = ob_get_contents();
				ob_end_clean();
				wp_send_json( array( 'template' => $template ) );
			}
		}

		/**
		 * @author YITH
		 * @since 2.0.0
		 */
		public function update_carrier_time_slot() {

			if ( isset( $_POST['ywcdd_carrier_id'] ) ) {

				$carrier_id    = $_POST['ywcdd_carrier_id'];
				$enabled       = $_POST['ywcdd_enabled'];
				$slot_name     = $_POST['ywcdd_slot_name'];
				$time_from     = $_POST['ywcdd_time_from'];
				$time_to       = $_POST['ywcdd_time_to'];
				$max_order     = $_POST['ywcdd_max_order'];
				$fee_name      = $_POST['ywcdd_fee_name'];
				$fee           = $_POST['ywcdd_fee'];
				$item_id       = $_POST['ywcdd_item_id'];
				$override_days = $_POST['ywcdd_override_days'];
				$days          = !empty( $_POST['ywcdd_day'] ) ? $_POST['ywcdd_day'] : array();

				$time_slots = get_post_meta( $carrier_id, '_ywcdd_addtimeslot', true );

				if ( ! empty( $time_slots ) && isset( $time_slots[ $item_id ] ) ) {

					$single_slot                  = $time_slots[ $item_id ];
					$single_slot['enabled']       = $enabled;
					$single_slot['slot_name']     = $slot_name;
					$single_slot['timefrom']      = $time_from;
					$single_slot['timeto']        = $time_to;
					$single_slot['max_order']     = $max_order;
					$single_slot['fee_name']      = $fee_name;
					$single_slot['fee']           = $fee;
					$single_slot['override_days'] = $override_days;
					$single_slot['day_selected']  = $days;
					$time_slots[ $item_id ]       = $single_slot;

					update_post_meta( $carrier_id, '_ywcdd_addtimeslot', $time_slots );
					wp_send_json( array( 'result' => true ) );
				}




			}

		}

		public function enable_disable_time_slot() {
			if ( isset( $_POST['ywcdd_slot_id'] ) && isset( $_POST['ywcdd_carrier_id'] ) ) {
				$slot_id       = $_POST['ywcdd_slot_id'];
				$enabled       = $_POST['ywcdd_enable'];
				$carrier_id    = $_POST['ywcdd_carrier_id'];
				$all_time_slot = $this->get_time_slots( $carrier_id );

				if ( isset( $all_time_slot[ $slot_id ] ) ) {

					$all_time_slot[ $slot_id ]['enabled'] = $enabled;

					update_post_meta( $carrier_id, '_ywcdd_addtimeslot', $all_time_slot );

					wp_send_json( array( 'result' => true ) );
				}
			}
		}

		public function delete_carrier_time_slot() {

			if ( isset( $_POST['ywcdd_carrier_id'] ) && isset( $_POST['ywcdd_slot_id'] ) ) {

				$carrier_id = $_POST['ywcdd_carrier_id'];
				$item_id    = $_POST['ywcdd_slot_id'];
				$time_slots = $this->get_time_slots( $carrier_id );

				if ( ! empty( $time_slots ) && isset( $time_slots[ $item_id ] ) ) {
					unset( $time_slots[ $item_id ] );
					update_post_meta( $carrier_id, '_ywcdd_addtimeslot', $time_slots );
					wp_send_json( array( 'result' => true ) );
				}
			}
		}

		public function get_all_carrier() {

			$args = array(
				'post_type'      => 'yith_carrier',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'fields'         => 'ids'
			);

			$carriers = get_posts( $args );

			return $carriers;
		}

		public function get_all_formatted_carriers(){

			$carriers = $this->get_all_carrier();
			$formatted_carriers = array();
			foreach( $carriers as $carrier_id ){

				$formatted_carriers[$carrier_id] = get_the_title( $carrier_id );
			}

			return $formatted_carriers;
		}

		/**
		 * get
		 *
		 * @param int $id
		 *
		 * @return int
		 */
		public function get_min_working_day( $carrier_id ) {

			if ( - 1 == $carrier_id ) {
				$min_dd = get_option( 'yith_delivery_date_range_day', 1 );
			} else {
				$min_dd = get_post_meta( $carrier_id, '_ywcdd_dayrange', true );
			}

			if( is_array( $min_dd  ) ){

				$min_dd = $this->get_need_day_for_zone( $min_dd );

			}

			/**
			 * this filter is deprecated from version 2.0
			 */
			$min_dd = apply_filters( 'yith_delivery_date_base_carrier_day', $min_dd, $carrier_id );

			return apply_filters( 'ywcdd_get_delivery_working_day', $min_dd, $carrier_id );
		}

		/**
		 * @param array $days_zone
		 *
		 * @return mixed
		 */
		public function get_need_day_for_zone( $days_zone = array() ){

			$customer_zone = yith_delivery_date_get_customer_zone();

			foreach( $days_zone as $key => $day_zone ){

				if( $customer_zone == $day_zone['shipping_zone'] || 'all' == $day_zone['shipping_zone'] ) {

					return $day_zone['day'];
				}
			}

			return false;
		}

		public function get_work_days( $carrier_id ) {

			if ( $carrier_id == - 1 ) {
				$delivery_worksday = get_option( 'yith_delivery_date_workday', array() );
			} else {
				$delivery_worksday = get_post_meta( $carrier_id, '_ywcdd_workday', true );

			}
			$delivery_worksday = empty( $delivery_worksday ) ? array_keys( yith_get_worksday( false ) ) : $delivery_worksday;
			$works_day         = array();

			foreach ( $delivery_worksday as $day ) {

				$works_day[ $day ] = $day;
			}

			return apply_filters( 'ywcdd_get_carrier_work_days', $works_day, $carrier_id );
		}



		public function get_max_range( $carrier_id ) {

			if ( - 1 === $carrier_id ) {
				$max_selected_range = get_option( 'yith_delivery_date_max_range', 30 );
			} else {
				$max_selected_range = get_post_meta( $carrier_id, '_ywcdd_max_selec_orders', true );
			}

			return empty( $max_selected_range ) ? 30 : $max_selected_range;
		}

		public function get_time_slots( $carrier_id ) {
			if ( - 1 == $carrier_id ) {
				$all_slots = get_option( 'yith_delivery_date_time_slot', array() );
			} else {
				$all_slots = get_post_meta( $carrier_id, '_ywcdd_addtimeslot', true );
				$all_slots = empty( $all_slots ) ? array() : $all_slots;
				$all_slots = maybe_unserialize( $all_slots );
			}

			return $all_slots;
		}

		public function get_enabled_time_slots( $carrier_id ){
			if( 'checkout' === get_option( 'ywcdd_processing_type', 'checkout' ) ) {
				$all_slots = get_post_meta( $carrier_id, '_ywcdd_addtimeslot', true );
				$all_slots = maybe_unserialize( $all_slots );
				$all_slots = empty( $all_slots ) ? array() : $all_slots;

				foreach ( $all_slots as $slot_id => $slot ) {

					$enabled = isset( $slot['enabled'] ) ? $slot['enabled'] : 'yes';

					if ( ! yith_plugin_fw_is_true( $enabled ) ) {
						unset( $all_slots[ $slot_id ] );
					}
				}

				return $all_slots;
			}else{
				return array();
			}
		}

		public function get_time_slot_by_id( $carrier_id, $slot_id ) {
			$all_slots = $this->get_time_slots( $carrier_id );

			return isset( $all_slots[ $slot_id ] ) ? $all_slots[ $slot_id ] : false;
		}

		public function add_return_to_list_button() {

			global $post;

			if ( isset( $post ) && 'yith_carrier' === $post->post_type ) {
				$admin_url = admin_url( 'admin.php' );
				$params    = array(
					'page' => 'yith_delivery_date_panel',
					'tab'  => 'carrier-settings'
				);

				$list_url = apply_filters( 'ywcdd_carrier_back_link', esc_url( add_query_arg( $params, $admin_url ) ) );
				$button   = sprintf( '<a href="%1$s" title="%2$s" class="ywcdd_back_to">%2$s</a>', $list_url,
					__( 'Back to Carriers',
						'yith-woocommerce-delivery-date' ) );
				echo $button;
			}
		}
	}

}

if ( ! function_exists( 'YITH_Delivery_Date_Carrier' ) ) {

	function YITH_Delivery_Date_Carrier() {
		return YITH_Delivery_Date_Carrier::get_instance();
	}
}

YITH_Delivery_Date_Carrier();

