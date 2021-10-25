<?php
	namespace MasterAddons\Inc\Templates\Types;

	/**
	 * Author Name: Liton Arefin
	 * Author URL: https://jeweltheme.com
	 * Date: 9/8/19
	 */

	if ( ! defined('ABSPATH') ) exit; // No access of directly access

	if ( ! class_exists( 'Master_Addons_Templates_Types' ) ) {

		class Master_Addons_Templates_Types {

			private $types = null;

			public function __construct() {

				$this->register_types();

			}

			public function register_types() {

				$base_path = MELA_PLUGIN_PATH . '/inc/templates/types/';

				require $base_path . 'base.php';

				$temp_types = array(
					__NAMESPACE__ . '\Master_Addons_Structure_Section' => $base_path . 'section.php',
				);

				array_walk( $temp_types, function( $file, $class ) {

					require $file;

					$this->register_type( $class );

				} );

				do_action( 'master-addons-templates/types/register', $this );

			}


			public function register_type( $class ) {

				$instance = new $class;

				$this->types[ $instance->get_id() ] = $instance;

				if ( true === $instance->is_location() ) {

					register_structure()->locations->register_location( $instance->location_name(), $instance );

				}

			}

			public function get_types() {

				return $this->types;

			}

			public function get_type( $id ) {

				return isset( $this->types[ $id ] ) ? $this->types[ $id ] : false;

			}

			public function get_types_for_popup() {

//				$result = array();
				$result = array(
					'master_pages' =>array(
						'title' => __('Ready Pages', MELA_TD),
						'data' =>[],
						'sources' => array( 'master-addons','master-api' ),
						'settings' =>array(
							'show_title' =>true,
							'show_keywords' =>true
						)
					),
//					'master_popups' =>array(
//						'title' => __('Popups', MELA_TD) ,
//						'data' =>[],
//						'sources' => array( 'master-addons','master-api' ),
//						'settings' =>array(
//							'show_title' =>true,
//							'show_keywords' =>true
//						)
//					),
					'master_headers' =>array(
						'title' => __('Headers', MELA_TD) ,
						'data' =>[],
						'sources' => array( 'master-addons','master-api' ),
						'settings' =>array(
							'show_title' =>true,
							'show_keywords' =>true
						)
					),
					'master_footers' =>array(
						'title' => __('Footers', MELA_TD) ,
						'data' =>[],
						'sources' => array( 'master-addons','master-api' ),
						'settings' =>array(
							'show_title' =>true,
							'show_keywords' =>true
						)
					),

//					'master_woocommerce' =>array(
//						'title' => __('WooCommerce', MELA_TD) ,
//						'data' =>[],
//						'sources' => array( 'master-addons','master-api' ),
//						'settings' =>array(
//							'show_title' =>true,
//							'show_keywords' =>true
//						)
//					),


				);

				foreach ( $this->types as $id => $structure ) {
					$result[ $id ] = array(
						'title'    => $structure->get_plural_label(),
						'data'     => array(),
						'sources'  => $structure->get_sources(),
						'settings' => $structure->library_settings(),
					);
				}
				return $result;

			}

		}

	}
