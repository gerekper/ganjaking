<?php

	namespace MasterAddons\Inc\Templates\Classes;

	use MasterAddons\Inc\Templates;

	/**
	 * Author Name: Liton Arefin
	 * Author URL: https://jeweltheme.com
	 * Date: 9/8/19
	 */


	if( ! defined( 'ABSPATH') ) exit;

	if ( ! class_exists( 'Master_Addons_Templates_Manager' ) ) {


		class Master_Addons_Templates_Manager {

			private static $instance = null;

			private $sources = array();


			public function __construct() {

				//Register AJAX hooks
				add_action( 'wp_ajax_ma_el_get_templates', array( $this, 'get_templates' ) );
				add_action( 'wp_ajax_nopriv_ma_el_get_templates', array( $this, 'get_templates' ) );

				add_action( 'wp_ajax_ma_el_inner_template', array( $this, 'insert_inner_template' ) );
				add_action( 'wp_ajax_nopriv_ma_el_inner_template', array( $this, 'insert_inner_template' ) );


				if ( defined( 'ELEMENTOR_VERSION' ) && version_compare( ELEMENTOR_VERSION, '2.2.8', '>' ) ) {
					add_action( 'elementor/ajax/register_actions', array( $this, 'register_ajax_actions' ), 20 );
				} else {
					add_action( 'wp_ajax_elementor_get_template_data', array( $this, 'get_template_data' ), -1 );
				}

				$this->register_sources();

				add_filter( 'master-addons-core/assets/editor/localize', array( $this, 'localize_tabs' ) );

			}


			public function localize_tabs( $data ) {

				$tabs    = $this->get_template_tabs();
				$ids     = array_keys( $tabs );
				$default = $ids[0];

				$data['tabs']       = $this->get_template_tabs();
				$data['defaultTab'] = $default;

				return $data;

			}


			public function register_sources() {

				require MELA_PLUGIN_PATH . '/inc/templates/sources/base.php';

				$namespace = str_replace( 'Classes', 'Sources' , __NAMESPACE__ );

				$sources = array(
					'master-api'   =>  $namespace . '\Master_Addons_Templates_Source_Api',
				);

				foreach ( $sources as $key => $class ) {

					require MELA_PLUGIN_PATH . '/inc/templates/sources/' . $key . '.php';

					$this->add_source( $key, $class );
				}

			}


			public function get_template_tabs() {

				$tabs = Templates\master_addons_templates()->types->get_types_for_popup();

				return $tabs;
			}


			public function add_source( $key, $class ) {
				$this->sources[ $key ] = new $class();
			}


			public function get_source( $slug = null ) {
				return isset( $this->sources[ $slug ] ) ? $this->sources[ $slug ] : false;
			}



			public function get_templates() {

				if ( ! current_user_can( 'edit_posts' ) ) {
					wp_send_json_error();
				}

				$tab     = $_GET['tab'];
				$tabs    = $this->get_template_tabs();
				$sources = $tabs[ $tab ]['sources'];

				$result = array(
//					'ready_pages'  => array(),
//					'ready_widgets'  => array(),
					'ready_headers'  => array(),
					'ready_footers'  => array(),
					'templates'  => array(),
					'categories' => array(),
					'keywords'   => array(),
				);

				foreach ( $sources as $source_slug ) {

					$source = isset( $this->sources[ $source_slug ] ) ? $this->sources[ $source_slug ] : false;

					if ( $source ) {
//						$result['ready_pages']  = array_merge( $result['ready_pages'], $source->get_items( $tab ) );
						$result['ready_headers']  = array_merge( $result['ready_headers'], $source->get_items( $tab ) );
						$result['ready_footers']  = array_merge( $result['ready_footers'], $source->get_items( $tab ) );
						$result['templates']  = array_merge( $result['templates'], $source->get_items( $tab ) );
						$result['categories'] = array_merge( $result['categories'], $source->get_categories( $tab ) );
						$result['keywords']   = array_merge( $result['keywords'], $source->get_keywords( $tab ) );
					}

				}


				$all_cats = array(
					array(
						'slug' => '',
						'title' => __( 'All Sections', MELA_TD ),
					),
				);

				if ( ! empty( $result['categories'] ) ) {
					$result['categories'] = array_merge( $all_cats, $result['categories'] );
				}

				wp_send_json_success( $result );

			}

			public function insert_inner_template() {



				if ( ! current_user_can( 'edit_posts' ) ) {
					wp_send_json_error();
				}

				$template = isset( $_REQUEST['template'] ) ? $_REQUEST['template'] : false;


				if ( ! $template ) {
					wp_send_json_error();
				}


				$template_id = isset( $template['template_id'] ) ? esc_attr( $template['template_id'] ) : false;

				$source_name = isset( $template['source'] ) ? esc_attr( $template['source'] ) : false;
				$source      = isset( $this->sources[ $source_name ] ) ? $this->sources[ $source_name ] : false;

				if ( ! $source || ! $template_id ) {
					wp_send_json_error();
				}

				$template_data = $source->get_item( $template_id );

				if ( ! empty( $template_data['content'] ) ) {
					wp_insert_post( array(
						'post_type'   => 'elementor_library',
						'post_title'  => $template['title'],
						'post_status' => 'publish',
						'meta_input'  => array(
							'_elementor_data'          => $template_data['content'],
							'_elementor_edit_mode'     => 'builder',
							'_elementor_template_type' => 'section',
						),
					) );
				}

				wp_send_json_success();

			}


			public function register_ajax_actions( $ajax_manager ) {

				if ( ! isset( $_POST['actions'] ) ) {
					return;
				}

				$actions     = json_decode( stripslashes( $_REQUEST['actions'] ), true );
				$data        = false;

				foreach ( $actions as $id => $action_data ) {
					if ( ! isset( $action_data['get_template_data'] ) ) {
						$data = $action_data;
					}
				}

				if ( ! $data ) {
					return;
				}

				if ( ! isset( $data['data'] ) ) {
					return;
				}

				if ( ! isset( $data['data']['source'] ) ) {
					return;
				}

				$source = $data['data']['source'];

				if ( ! isset( $this->sources[ $source ] ) ) {
					return;
				}

				$ajax_manager->register_ajax_action( 'get_template_data', function( $data ) {
					return $this->get_template_data_array( $data );
				} );

			}

			public function get_template_data_array( $data ) {

				if ( ! current_user_can( 'edit_posts' ) ) {
					return false;
				}

				if ( empty( $data['template_id'] ) ) {
					return false;
				}

				$source_name = isset( $data['source'] ) ? esc_attr( $data['source'] ) : '';


				if ( ! $source_name ) {
					return false;
				}

				$source = isset( $this->sources[ $source_name ] ) ? $this->sources[ $source_name ] : false;

				if ( ! $source ) {
					return false;
				}

				if ( empty( $data['tab'] ) ) {
					return false;
				}

				$template = $source->get_item( $data['template_id'], $data['tab'] );

				return $template;
			}


			public function get_template_data() {

				$template = $this->get_template_data_array( $_REQUEST );

				if ( ! $template ) {
					wp_send_json_error();
				}

				wp_send_json_success( $template );

			}


			public static function get_instance() {

				// If the single instance hasn't been set, set it now.
				if ( null == self::$instance ) {
					self::$instance = new self;
				}
				return self::$instance;
			}
		}

	}
