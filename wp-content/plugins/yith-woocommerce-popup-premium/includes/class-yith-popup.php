<?php
/**
 * Main class
 *
 * @author YITH
 * @package YITH WooCommerce Popup
 * @version 1.0.0
 */


if ( ! defined( 'YITH_YPOP_INIT' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_Popup' ) ) {
	/**
	 * YITH WooCommerce Popup main class
	 *
	 * @since 1.0.0
	 */
	class YITH_Popup {
		/**
		 * Single instance of the class
		 *
		 * @var \YITH WooCommerce Popup
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Array with accessible variables
		 */
		protected $_data = array();

		public $post_type_name = 'yith_popup';

		public $template_list = array();

		/**
		 * The name for the plugin options
		 *
		 * @access public
		 * @var string
		 * @since 1.0.0
		 */
		public $plugin_options = 'yit_ypop_options';

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH WooCommerce Popup
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			$this->set_templates();

			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			add_action( 'init', array( $this, 'create_post_type' ), 0 );
			add_action( 'admin_init', array( $this, 'add_metabox' ), 1 );
			add_action( 'admin_init', array( $this, 'flush_rewrite' ) );

			add_filter( 'manage_edit-' . $this->post_type_name . '_columns', array( $this, 'edit_columns' ) );
			add_action( 'manage_' . $this->post_type_name . '_posts_custom_column', array( $this, 'custom_columns' ), 10, 2 );
			add_filter(
				'yith_plugin_fw_icons_field_icons_' . YITH_YPOP_SLUG,
				array(
					$this,
					'yith_add_retina_to_icons',
				),
				10,
				2
			);

		}

		public function set_templates() {
			$this->template_list = array(
				'theme1' => __( 'Theme 1', 'yith-woocommerce-popup' ),
				'theme2' => __( 'Theme 2', 'yith-woocommerce-popup' ),
				'theme3' => __( 'Theme 3', 'yith-woocommerce-popup' ),
				'theme4' => __( 'Theme 4', 'yith-woocommerce-popup' ),
				'theme5' => __( 'Theme 5', 'yith-woocommerce-popup' ),
				'theme6' => __( 'Theme 6', 'yith-woocommerce-popup' ),
			);

			$_data['template_list'] = $this->template_list;
		}


		// Register Custom Post Type
		function create_post_type() {

			$labels = array(
				'name'               => _x( 'Yith Popup', 'Post Type General Name', 'yith-woocommerce-popup' ),
				'singular_name'      => _x( 'Yith Popup', 'Post Type Singular Name', 'yith-woocommerce-popup' ),
				'menu_name'          => __( 'Popup', 'yith-woocommerce-popup' ),
				'parent_item_colon'  => __( 'Parent Item:', 'yith-woocommerce-popup' ),
				'all_items'          => __( 'All Popups', 'yith-woocommerce-popup' ),
				'view_item'          => __( 'View Popup', 'yith-woocommerce-popup' ),
				'add_new_item'       => __( 'Add New Popup', 'yith-woocommerce-popup' ),
				'add_new'            => __( 'Add New Popup', 'yith-woocommerce-popup' ),
				'edit_item'          => __( 'Edit Popup', 'yith-woocommerce-popup' ),
				'update_item'        => __( 'Update Popup', 'yith-woocommerce-popup' ),
				'search_items'       => __( 'Search Popup', 'yith-woocommerce-popup' ),
				'not_found'          => __( 'Not found', 'yith-woocommerce-popup' ),
				'not_found_in_trash' => __( 'Not found in Trash', 'yith-woocommerce-popup' ),
			);
			$args   = array(
				'label'               => __( 'yith_popup', 'yith-woocommerce-popup' ),
				'description'         => __( 'Yith Popup Description', 'yith-woocommerce-popup' ),
				'labels'              => $labels,
				'supports'            => array( 'title' ),
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => false,
				'show_in_admin_bar'   => true,
				'menu_position'       => null,
				'can_export'          => true,
				'has_archive'         => true,
				'menu_icon'           => 'dashicons-feedback',
				'exclude_from_search' => true,
				'publicly_queryable'  => true,
				'capability_type'     => 'post',
			);

			register_post_type( $this->post_type_name, $args );

		}

		/**
		 * Flush rewrite rules when the plugin is installed
		 *
		 * @since   1.0.0
		 * @author  Emanuela Castorina <emanuela.castorina@yithemes.com>
		 * @return  mix
		 */
		public function flush_rewrite() {
			if ( ! get_option( 'ypop_flush_rewrite_done' ) ) {
				flush_rewrite_rules();
				update_option( 'ypop_flush_rewrite_done', 1 );
			}
		}

		/**
		 * Return a $property defined in this class
		 *
		 * @since   1.0.0
		 * @author  Emanuela Castorina <emanuela.castorina@yithemes.com>
		 *
		 * @param $property
		 *
		 * @return mix
		 */
		public function __get( $property ) {
			if ( isset( $this->_data[ $property ] ) ) {
				return $this->_data[ $property ];
			}
		}

		/**
		 * Load YIT Plugin Framework
		 *
		 * @since  1.0.0
		 * @return void
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once $plugin_fw_file;
				}
			}
		}

		/**
		 * Get options from db
		 *
		 * @access public
		 * @since 1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param $option string
		 * @return mixed
		 */
		public function get_option( $option ) {
			// get all options
			$options = get_option( $this->plugin_options );

			if ( isset( $options[ $option ] ) ) {
				return $options[ $option ];
			}

			return false;
		}

		/**
		 * Add metabox in popup page
		 *
		 * @since  1.0.0
		 * @return void
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function add_metabox() {

			if ( ! function_exists( 'YIT_Metabox' ) ) {
				require_once 'plugin-fw/yit-plugin.php';
			}

			$args             = require_once YITH_YPOP_DIR . '/plugin-options/metabox/ypop_template.php';
			$metabox_template = YIT_Metabox( 'yit-pop' );
			$metabox_template->init( $args );

			$args    = require_once YITH_YPOP_DIR . '/plugin-options/metabox/ypop_metabox.php';
			$metabox = YIT_Metabox( 'yit-pop-info' );
			$metabox->init( $args );

			$args    = require_once YITH_YPOP_DIR . '/plugin-options/metabox/ypop_cpt_metabox.php';
			$metabox = YIT_Metabox( 'yit-cpt-info' );
			$metabox->init( $args );

		}

		/**
		 * Get meta from Metabox Panel
		 *
		 * return the meta from database
		 *
		 * @param $meta
		 * @param $post_id
		 *
		 * @return mixed
		 * @since    1.0
		 * @author   Antonio La Rocca <antonio.larocca@yithemes.it>
		 */
		public function get_meta( $meta, $post_id ) {
			$meta_value = get_post_meta( $post_id, $meta, true );

			if ( isset( $meta_value ) ) {
				return $meta_value;
			} else {
				return '';
			}
		}

		public function get_popups_list() {
			$popups = get_posts( 'post_type=' . $this->post_type_name . '&posts_per_page=-1' );

			$array = array();
			if ( ! empty( $popups ) ) {
				foreach ( $popups as $popup ) {
					$array[ $popup->ID ] = $popup->post_title;
				}
			}

			return $array;
		}


		function edit_columns( $columns ) {
			$columns = array(
				'cb'       => '<input type="checkbox" />',
				'title'    => __( 'Title', 'yith-woocommerce-popup' ),
				'template' => __( 'Template', 'yith-woocommerce-popup' ),
				'content'  => __( 'Content Type', 'yith-woocommerce-popup' ),
				'active'   => __( 'Active', 'yith-woocommerce-popup' ),
			);

			return $columns;
		}

		public function custom_columns( $column, $post_id ) {
			$template = get_post_meta( $post_id, '_template_name', true );
			$enabled  = get_post_meta( $post_id, '_enable_popup', true );
			$enabled  = yith_plugin_fw_is_true( $enabled );
			$enabled  = $enabled == 1 ? 'yes' : 'no';
			switch ( $column ) {
				case 'template':
					echo $template; //phpcs:ignore
					break;
				case 'content':
					$content = get_post_meta( $post_id, '_' . $template . '_content_type', true );
					if ( is_string( $content ) ) {
						echo wp_kses_post( $content );
					}
					break;
				case 'active':
					?>
					<div class="yith-plugin-ui"><div class="yith-plugin-fw-onoff-container ">
							<input type="checkbox" id="enable <?php echo esc_attr( $post_id ); ?>" name="ypop_enable_popup" value="<?php echo esc_attr( $enabled ); ?>" class="on_off" data-std="yes" <?php checked( $enabled, 'yes' ); ?> data-id="<?php echo esc_attr( $post_id ); ?>" data-action="ypop_change_status">
							<span class="yith-plugin-fw-onoff"></span>
						</div>
					</div>

					<?php
					break;
			}
		}

		public function yith_add_retina_to_icons( $yit_icons ) {
			$font_json                    = YITH_YPOP_ASSETS_PATH . '/fonts/retinaicon-font/config.json';
			$yit_icons['retinaicon-font'] = json_decode( file_get_contents( $font_json ), true );

			return $yit_icons;
		}


	}

	/**
	 * Unique access to instance of YITH_Popup class
	 *
	 * @return \YITH_Popup
	 */
	function YITH_Popup() {
		return YITH_Popup::get_instance();
	}
}

