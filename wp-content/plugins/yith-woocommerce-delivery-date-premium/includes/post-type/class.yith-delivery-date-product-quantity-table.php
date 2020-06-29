<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_Delivery_Product_Quantity_Table' ) ) {

	class YITH_Delivery_Product_Quantity_Table {

		protected static $_instance;
		protected $post_type_name;
		protected $capability_name;

		public function __construct() {
			$this->post_type_name  = 'yith_product_table';
			$this->capability_name = 'delivery_date_product_table';
			add_action( 'init', array( $this, 'register_post_type' ), 16 );
			add_action( 'admin_init', array( $this, 'add_capabilities' ) );
			add_action( 'admin_init', array( $this, 'add_meta_boxes' ) );
			add_filter( 'yit_fw_metaboxes_type_args', array( $this, 'add_custom_type_metaboxes' ) );
			add_action( 'save_post', array( $this, 'save_post_meta' ) );
			add_action( 'edit_form_top', array( $this, 'add_return_to_list_button' ) );
		}

		/**
		 * @return YITH_Delivery_Product_Quantity_Table
		 * @since 2.1.0
		 * @author YITH
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
		 * @since 2.1.0
		 * @author YITH
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
		 * Get the taxonomy label
		 *
		 * @param   $arg string The string to return. Default empty. If is empty return all taxonomy labels
		 *
		 * @return array taxonomy label
		 *
		 * @since  2.1.0
		 * @author YITH
		 */
		public function get_taxonomy_label( $arg = '' ) {

			$label = apply_filters( 'yith_delivery_date_product_table_taxonomy_label', array(
					'name'               => _x( 'Quantity Table', 'post type general name', 'yith-woocommerce-delivery-date' ),
					'singular_name'      => _x( 'Quantity Table', 'post type singular name', 'yith-woocommerce-delivery-date' ),
					'menu_name'          => __( 'Quantity Table', 'yith-woocommerce-delivery-date' ),
					'parent_item_colon'  => __( 'Parent Item:', 'yith-woocommerce-delivery-date' ),
					'all_items'          => __( 'All Quantity Tables', 'yith-woocommerce-delivery-date' ),
					'view_item'          => __( 'View Quantity Table', 'yith-woocommerce-delivery-date' ),
					'add_new_item'       => __( 'Add Quantity Table', 'yith-woocommerce-delivery-date' ),
					'add_new'            => __( 'Add Quantity Table', 'yith-woocommerce-delivery-date' ),
					'edit_item'          => __( 'Edit Quantity Table', 'yith-woocommerce-delivery-date' ),
					'update_item'        => __( 'Update Quantity Table', 'yith-woocommerce-delivery-date' ),
					'search_items'       => __( 'Search Quantity Table', 'yith-woocommerce-delivery-date' ),
					'not_found'          => __( 'No Quantity Table found', 'yith-woocommerce-delivery-date' ),
					'not_found_in_trash' => __( 'No Quantity Table found in Trash', 'yith-woocommerce-delivery-date' ),
				)
			);

			return ! empty( $arg ) ? $label[ $arg ] : $label;
		}

		/**
		 * add capabilities
		 * @author YITH
		 * @since 2.1.0
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

		public function register_post_type() {

			$args = apply_filters( 'yith_delivery_date_product_quantity_table_post_type', array(
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


			register_post_type( $this->post_type_name, $args );
			flush_rewrite_rules();
		}

		/**
		 * add meta boxes for post type carrier
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function add_meta_boxes() {

			$post_id = isset( $_GET['post'] ) ? $_GET['post'] : false;


			if ( ( $post_id && 'yith_product_table' === get_post_type( $post_id ) ) || ( isset( $_GET['post_type'] ) && 'yith_product_table' === $_GET['post_type'] ) ) {

				/**
				 * @var $metaboxes array metabox_id, metabox_opt
				 */
				$metaboxes = array(
					'yit-delivery-table-metaboxes' => 'delivery-table-meta-boxes-options.php',
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
		 * @param array $args
		 *
		 * @return array
		 * @author YITH
		 * @since 2.1.0
		 */
		public function add_custom_type_metaboxes( $args ) {
			global $post;


			if ( isset( $post ) && 'yith_product_table' === $post->post_type ) {

				$custom_types = array( 'quantity-table' );
				if ( in_array( $args['type'], $custom_types ) ) {
					$args['basename'] = YITH_DELIVERY_DATE_DIR;
					$args['path']     = 'meta-boxes/types/';
				}

			}

			return $args;
		}

		/**
		 * save the post meta
		 *
		 * @param $post_id
		 */
		public function save_post_meta( $post_id ) {

			if ( $this->post_type_name == get_post_type( $post_id ) ) {

				$qty_table       = isset( $_POST['yit_metaboxes']['ywcdd_qty_product_table'] ) ? $_POST['yit_metaboxes']['ywcdd_qty_product_table'] : array();
				$enable_table    = isset( $_POST['yit_metaboxes']['ywcdd_enable_quantity_rule_table'] ) ? $_POST['yit_metaboxes']['ywcdd_enable_quantity_rule_table'] : 'no';
				$how_apply_table = isset( $_POST['yit_metaboxes']['ywcdd_table_how_set_table'] ) ? $_POST['yit_metaboxes']['ywcdd_table_how_set_table'] : 'product';
				$product         = isset( $_POST['yit_metaboxes']['ywcdd_table_select_product'] ) ? $_POST['yit_metaboxes']['ywcdd_table_select_product'] : '';
				$category        = isset( $_POST['yit_metaboxes']['ywcdd_table_select_product_cat'] ) ? $_POST['yit_metaboxes']['ywcdd_table_select_product_cat'] : '';
				$carrier         = isset( $_POST['yit_metaboxes']['ywcdd_table_select_carrier'] ) ? $_POST['yit_metaboxes']['ywcdd_table_select_carrier'] : '';
				$need_days       = isset( $_POST['yit_metaboxes']['ywcdd_table_need_days'] ) ? $_POST['yit_metaboxes']['ywcdd_table_need_days'] : 0;
				update_post_meta( $post_id, 'ywcdd_qty_product_table', $qty_table );
				update_post_meta( $post_id, 'ywcdd_enable_quantity_rule_table', $enable_table );
				update_post_meta( $post_id, 'ywcdd_table_how_set_table', $how_apply_table );
				update_post_meta( $post_id, 'ywcdd_table_select_product', $product );
				update_post_meta( $post_id, 'ywcdd_table_select_product_cat', $category );
				update_post_meta( $post_id, 'ywcdd_table_select_carrier', $carrier );
				update_post_meta( $post_id, 'ywcdd_table_need_days', $need_days );
			}
		}

		/**
		 * @param array $args
		 *
		 * @return  array
		 */
		public function get_quantity_tables( $args = array() ) {

			$default_args = array(
				'post_type'      => $this->post_type_name,
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
			);

			$default_args = wp_parse_args( $args, $default_args );

			$posts = get_posts( $default_args );

			return $posts;
		}

		/**
		 * return only enabled tables
		 *
		 * @param array $args
		 *
		 * @return array
		 * @author YITH
		 * @since 2.1.0
		 */
		public function get_enabled_quantity_tables( $args = array() ) {

			$default_args = array(
				'meta_query' => array(
					array(
						'key'     => 'ywcdd_enable_quantity_rule_table',
						'value'   => 'yes',
						'compare' => '='
					)
				)
			);

			$default_args = wp_parse_args( $args, $default_args );

			$posts = $this->get_quantity_tables( $default_args );

			global $wpdb;


			return $posts;
		}

		public function get_tables_by_product_ids( $product_ids = array() ) {

			$default_args = array(
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key'     => 'ywcdd_enable_quantity_rule_table',
						'value'   => 'yes',
						'compare' => '='
					),
					array(
						'key'     => 'ywcdd_table_how_set_table',
						'value'   => 'product',
						'compare' => '='
					)
				),
				'fields'     => 'ids',
			);


			$posts = $this->get_quantity_tables( $default_args );


			foreach ( $posts as $i => $post_id ) {
				$post_product_ids = get_post_meta( $post_id, 'ywcdd_table_select_product', true );
				$find = array();
				if (  is_array( $post_product_ids ) ) {
					$find = array_intersect( $product_ids, $post_product_ids );
				}

				if ( count( $find ) == 0 ) {
					unset( $posts[ $i ] );
				}

			}

			return $posts;
		}

		public function get_tables_by_product_category_ids( $category_ids = array() ) {

			$default_args = array(
				'meta_query' => array(
					'relation' => 'AND',
					array(
						'key'     => 'ywcdd_enable_quantity_rule_table',
						'value'   => 'yes',
						'compare' => '='
					),
					array(
						'key'     => 'ywcdd_table_how_set_table',
						'value'   => 'product_cat',
						'compare' => '='
					)
				),
				'fields'     => 'ids'
			);


			$posts = $this->get_quantity_tables( $default_args );

			foreach ( $posts as $i => $post_id ) {
				$post_product_cat_ids = get_post_meta( $post_id, 'ywcdd_table_select_product_cat', true );

				$find = array_intersect( $category_ids, $post_product_cat_ids );

				if ( count( $find ) == 0 ) {
					unset( $posts[ $i ] );
				}
			}

			return $posts;
		}

		/**
		 * add a button in single post for back to list
		 * @author YITHEMES
		 * @since 1.0.0
		 */
		public function add_return_to_list_button() {

			global $post;

			if ( isset( $post ) && $this->post_type_name === $post->post_type ) {
				$admin_url = admin_url( 'admin.php' );
				$params    = array(
					'page' => 'yith_delivery_date_panel',
					'tab'  => 'delivery-table'
				);

				$list_url = apply_filters( 'ywcdd_quantity_tables_back_link', esc_url( add_query_arg( $params, $admin_url ) ) );
				$button   = sprintf( '<a href="%1$s" title="%2$s" class="ywcdd_back_to">%2$s</a>', $list_url,
					__( 'Back to Quantity Tables',
						'yith-woocommerce-delivery-date' ) );
				echo $button;
			}
		}

	}
}

if ( ! function_exists( 'YITH_Delivery_Product_Quantity_Table' ) ) {
	/**
	 * @return YITH_Delivery_Product_Quantity_Table
	 */
	function YITH_Delivery_Product_Quantity_Table() {

		return YITH_Delivery_Product_Quantity_Table::get_instance();
	}
}

YITH_Delivery_Product_Quantity_Table();