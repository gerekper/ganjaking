<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct access forbidden.' );
}

if ( ! class_exists( 'YITH_Frontend_Manager_Section' ) ) {

	class YITH_Frontend_Manager_Section {

		/**
		 * Unique section ID
		 *
		 * @var string
		 * @since  1.0.0
		 * @access public
		 */
		public $slug = '';

		/**
		 * Section Name
		 *
		 * @var string
		 * @since  1.0.0
		 * @access protected
		 */
		protected $_name = '';

		/**
		 * Default Section Name
		 *
		 * @var string
		 * @since  1.0.0
		 * @access protected
		 */
		protected $_default_section_name = '';

		/**
		 * Section ID
		 *
		 * @var string
		 * @since  1.0.0
		 * @access protected
		 */
		public $id = '';

		/**
		 * Shortcodes prefix
		 *
		 * @var string
		 * @since  1.0.0
		 * @access protected
		 */
		protected $_shortcodes_prefix = 'yith_woocommerce_frontend_manager_';

		/**
		 * Subsection array
		 *
		 * [
		 *   [
		 *     'id' => string
		 *     'name' => string
		 *     'endpoint' => string
		 *     'visible' => bool
		 *   ],
		 *   [
		 *     'id' => string
		 *     'name' => string
		 *     'endpoint' => string
		 *     'visible' => bool
		 *   ],
		 *   ...
		 * ]
		 *
		 * @var mixed
		 * @since  1.0.0
		 * @access public
		 */
		public $_subsections = array();

		/**
		 * @var array Allowed query string for this section
		 */
		public $allowed_query_string = array();

		/**
		 * Constructor method
		 *
		 * @return \YITH_Frontend_Manager_Section
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->slug         = $this->get_option( 'slug', $this->id, $this->id );
			$this->_name        = $this->get_option( 'name', $this->id, $this->_default_section_name );

			/**
			 * APPLY_FILTERS: yith_wcfm_section_subsections
			 *
			 * Filters the subsection.
			 *
			 * @param array $subsection The subscections.
			 * @param YITH_Frontend_Manager_Section $class The class.
			 * @param string $slug The section id.
			 *
			 *  @return array
			 */
			$this->_subsections = apply_filters( 'yith_wcfm_section_subsections', $this->_subsections, __CLASS__, $this->slug );

			add_filter( 'woocommerce_account_menu_item_classes', array( $this, 'menu_item_classes' ), 10, 2 );
			add_filter( 'yith_wcfm_endpoints_options', array( $this, 'add_endpoints_settings' ) );

			/* Change Endpoint Page Title */
			add_filter( 'the_title', array( $this, 'page_endpoint_title' ) );

			/* Enqueue Styles and Scripts */
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

            /* Body Classes */
            add_filter( 'body_class', array( $this, 'body_classes' ), 20 );

            /* Print and clean woocommerce notice */
            add_action( 'yith_wcfm_before_section_template', 'wc_print_notices' );
            add_action( 'yith_wcfm_after_section_template',  'wc_clear_notices' );

			/**
			 * WPML Support
			 */
			$section_id = $this->get_id();
			YITH_Frontend_Manager()->register_string_wpml( "yith_wcfm_{$section_id}_section_name", $this->_default_section_name );

			/* === Query Filter Args === */
			add_filter( 'allowed_args_on_frontend_manager_query_string', array( $this, 'allowed_args_on_frontend_manager_query_string' ) );
		}

		/* === SECTION METHODS === */

		/**
		 * Print section
		 *
		 * To be extended on sub classes
		 *
		 * @author YITH <plugins@yithemes.com>
		 * @return void
		 * @since  1.0.0
		 */
		public function print_section( $subsection = '', $section = '', $atts = array() ) {
			if( ! is_user_logged_in() ){
				return false;
			}

			if( $this->is_enabled() ){
				$section_id = $this->get_id();
				/**
				 * APPLY_FILTERS: yith_wcfm_print_section_path
				 *
				 * Filters the section.
				 *
				 * @param string $section The section.
				 * @param string  $subsection The subsection.
				 * @param string $id The section id.
				 *
				 * @return string
				 */
                $section    = apply_filters( 'yith_wcfm_print_section_path', $section, $subsection, $this->get_id() );
				/**
				 * APPLY_FILTERS: yith_wcfm_print_subsection_path
				 *
				 * Filters the section.
				 *
				 * @param string $subsection The subsection.
				 * @param string  $section The section.
				 * @param string $id The section id.
				 * @return string
				 */
				$subsection = apply_filters( 'yith_wcfm_print_subsection_path', $subsection, $section, $this->get_id() );

				/**
				 * DO_ACTION: yith_wcmf_before_print_section
				 *
				 * Before print section.
				 *
				 * @param string $section_id The section id
				 * @param string $section The section
				 * @param string $subsection The subsection
				 * @param YITH_Frontend_Manager_Section $class the frontend manager section class
				 *
				 */
				do_action( 'yith_wcmf_before_print_section', $section_id, $section, $subsection, $this );

                yith_wcfm_get_template( $subsection, $atts, 'sections/' . $section );

                if( ! empty( $this->_subsections[ $subsection ][ 'add_delete_script' ] ) ) {
                    yith_wcfm_add_confirm_script_on_delete();
                }


                $action = "yith_wcmf_section_{$subsection}";
				/**
				 * DO_ACTION: yith_wcmf_section_{$subsection}
				 *
				 * Print subsection.
				 *
				 * @param string $section_id The section id
				 * @param string $section The section
				 * @param string $subsection The subsection
				 * @param YITH_Frontend_Manager_Section $class the frontend manager section class
				 *
				 */
				do_action( $action, $section_id, $section, $subsection, $this );
				/**
				 * DO_ACTION: yith_wcmf_after_print_section
				 *
				 * After print section.
				 *
				 * @param string $section_id The section id
				 * @param string $section The section
				 * @param string $subsection The subsection
				 * @param YITH_Frontend_Manager_Section $class the frontend manager section class
				 *
				 */
				do_action( 'yith_wcmf_after_print_section', $section_id, $section, $subsection, $this );
            }

            else {
				/**
				 * DO_ACTION: yith_wcfm_print_section_unauthorized
				 *
				 * Print unauthorized section.
				 *
				 * @param string $section_id The section id
				 *
				 */
                do_action( 'yith_wcfm_print_section_unauthorized', $this->id );
            }

		}

		/**
		 * Print shortcode function
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function print_shortcode( $atts = array(), $content = '', $tag = '' ) {
			trigger_error( sprintf( 'The %s() method must be overridden in child class', __METHOD__ ), E_USER_WARNING );
		}

		/* === HELPER METHODS === */

		/**
		 * Check if the current section has subsections
		 *
		 * @since 1.0
		 * @return int subsection number
		 */
		public function has_subsections() {
			return count( $this->_subsections );
		}

		/**
		 * Check if section is enabled
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function is_enabled() {
			if ( ! is_user_logged_in() ) {
				return false;
			}
			/**
			 * APPLY_FILTERS: yith_wcfm_section_is_enabled
			 *
			 * Return if section is enabled or not.
			 *
			 * @param bool $is_enabled True/false if section is enabled.
			 * @param string $id The section id.
			 *
			 * @return bool
			 */
			return apply_filters( 'yith_wcfm_section_is_enabled', yith_wcfm_is_section_enabled( $this ), $this->get_id() );
		}

		/**
		 * Check if current page prints this section
		 *
		 * @param $subsection string Slug of subsection to check; if no subsection is passed, main section is checked
		 *
		 * @return bool
		 * @since  1.0.0
		 */
		public function is_current( $subsection = '' ) {
			global $wp;
			$is_current = empty( $subsection ) ? isset( $wp->query_vars[ $this->slug ] ) : false;

			if ( $subsection && isset( $wp->query_vars[ $this->slug ] ) && isset( $this->_subsections[ $subsection ]['slug'] ) ) {
				$is_current = $this->_subsections[ $subsection ]['slug'] === $wp->query_vars[ $this->slug ];
			}
			/**
			 * APPLY_FILTERS: yith_wcfm_section_is_current
			 *
			 * Check if current page prints this section
			 *
			 * @param bool $is_current True/false if section is enabled.
			 * @param string $slug The section slug.
			 * @param string $subsection The subsection.
			 *
			 * @return bool
			 */
			return apply_filters( 'yith_wcfm_section_is_current', $is_current, $this->slug, $subsection );
		}

		/* === GETTER / SETTER METHODS === */

		/**
		 * Get url of the section
		 *
		 * @param $subsection string Slug of subsection to check; if no subsection is passed, main section is checked
		 *
		 * @return string
		 * @since  1.0.0
		 */
		public function get_url( $subsection = '', $permalink = '' ) {
			if( $subsection ){
				$subsection  = $this->get_option( 'slug', $this->id . '_' . $subsection, $subsection );
			}

			if( empty( $permalink ) ){
			    $permalink = yith_wcfm_get_main_page_url();
            }
			/**
			 * APPLY_FILTERS: yith_wcfm_section_is_current
			 *
			 * Get url of the section
			 *
			 * @param string $url The url section.
			 * @param string $slug The section slug.
			 * @param string $subsection The subsection.
			 * @param string $id The section id.
			 *
			 * @return string
			 */
			return apply_filters( 'yith_wcfm_section_url', wc_get_endpoint_url( $this->slug, $subsection, $permalink ), $this->slug, $subsection, $this->id );
		}

		/**
		 * Get current Subsection args
		 *
		 *
		 * @return string
		 * @since  1.0.0
		 */
		public function get_current_subsection( $single = false ){
			global $wp;
			$slug = $this->slug;
			$current_subsection = array();
			if( isset( $wp->query_vars[ $slug ] ) ){
				foreach( $this->_subsections as $subsection_id => $subsection_args ){
				    //TODO: Fix get current subsection with no default slug
					if( $wp->query_vars[ $slug ] == $subsection_args['slug'] ){
						$current_subsection = $single ? $subsection_id : array( $subsection_id => $subsection_args );
						break;
					}
				}
			}

			return $current_subsection;
		}

		/**
		 * Get section slug
		 *
		 * @return string
		 * @since  1.0.0
		 */
		public function get_slug() {
			return ! empty( $this->slug ) ? $this->slug : $this->get_option( 'slug', $this->id, $this->id );
		}

		/**
		 * Get section id
		 *
		 * @return string
		 * @since  1.0.0
		 */
		public function get_id() {
			return $this->id;
		}

		/**
		 * Get section name
		 *
		 * @return string
		 * @since  1.0.0
		 */
		public function get_name( $subsection = '' ) {
			$section = $this->id;
			$type    = 'name';

			if ( $subsection ) {
				$subsection_id   = is_array( $subsection ) && isset( $subsection['slug'] ) ? $subsection['slug'] : $subsection;
				$subsection_name = is_array( $subsection ) && isset( $subsection['name'] ) ? $subsection['name'] : $subsection_id;
				$section         = $this->id . '_' . $subsection_id;
				$subsection_name = $this->get_option( $type, $section, $subsection_name );
			}

			$section_name = YITH_Frontend_Manager()->get_string_wpml( "yith_wcfm_{$section}_section_{$type}", isset( $subsection_name ) ? $subsection_name : $this->_name );

			/**
			 * APPLY_FILTERS: yith_wcfm_section_name
			 *
			 * Filters the section name before print it.
			 *
			 * @param string $section_name The section name.
			 * @param array $subsection The subsection array.
			 * @param array $this The section object.
			 *
			 * @return array
			 */
			return apply_filters( 'yith_wcfm_section_name', $section_name, $subsection, $this );
		}

		/**
		 * Get section subsections
		 *
		 * @return array
		 * @since  1.0.0
		 */
		public function get_subsections() {
			return $this->_subsections;
		}

		/**
		 * get shortcodes prefix
		 *
		 * @return array
		 * @since  1.0.0
		 */
		public function get_shortcodes_prefix() {
			return $this->_shortcodes_prefix;
		}

		/**
		 * Add custom menu item classes to frontend manager navigation link
		 *
		 * @since  1.0.0
		 *
		 * @param $classes     HTML classes
		 * @param $endpoint    Current endpoint
		 *
		 * @return array The classes
		 */
		public function menu_item_classes( $classes, $endpoint ) {
			if ( ! empty(  YITH_Frontend_Manager()->gui ) && YITH_Frontend_Manager()->gui->is_main_page() ) {
			    $section_classes = array(
                    'yith-wcfm-navigation-link',
                    'yith-wcfm-navigation-link--' . $endpoint
                );

			    foreach( $section_classes as $section_class ) {
			        if( ! in_array( $section_class, $classes ) ){
                        $classes[] = $section_class;
                    }
                }
			}

			return $classes;
		}

		/**
		 * Get section slug and section name from DB
		 *
		 * @since    1.0.0
		 *
		 * @param      $type
		 * @param      $section
		 * @param bool $default
		 *
		 * @return string slug or name value
		 */
		public function get_option( $type, $section, $default = false ) {
			return get_option( "yith_wcfm_{$section}_section_{$type}", $default );
		}

		/**
		 * Create endpoints panel options
		 *
		 * @since    1.0.0
		 *
		 * @param    Array $options options args
		 *
		 * @return  Array options argument to YIT_Panel_WooCommerce
		 */
		public function add_endpoints_settings( $options ) {
			$section_id = $this->id;

			$section_settings = array(
				"{$section_id}_options_start" => array(
					'type'  => 'yith_wcfm_sectionstart',
					'value' => $section_id
				),

				"{$section_id}_options_title" => array(
					'type'  => 'title',
					'title' => sprintf( '%s %s', $this->_default_section_name, __( 'Endpoint', 'yith-frontend-manager-for-woocommerce' ) ),
					'id'    => 'yith_title'
				),

				"{$section_id}_options_disabled" => array(
					'type'  => 'yith_wcfm_section_disabled_message',
					'value' => $section_id
				),

				"{$section_id}_section_name" => array(
					'type'    => 'text',
					'id'      => "yith_wcfm_{$section_id}_section_name",
					'title'   => __( 'Section name', 'yith-frontend-manager-for-woocommerce' ),
					'default' => $this->_default_section_name,
					'desc'    => sprintf( '%s %s', __( 'Menu label on the frontend &rarr;', 'yith-frontend-manager-for-woocommerce' ),
                        $this->_default_section_name ),
				),

				"{$section_id}_section_slug" => array(
					'type'    => 'text',
					'id'      => "yith_wcfm_{$section_id}_section_slug",
					'title'   => __( 'Section slug', 'yith-frontend-manager-for-woocommerce' ),
					'default' => $this->id,
					'desc'    => sprintf( '%s %s %s', __( 'Endpoint for the frontend &rarr;', 'yith-frontend-manager-for-woocommerce' ),
                        $this->_default_section_name, _x( 'page', 'i.e.: Endpoint for the Frontend Manager Dashboard page',
                            'yith-frontend-manager-for-woocommerce' ) ),
				),
			);

			$subsection_settings = array();

			$section_settings_end = array(
				"{$section_id}_options_end" => array( 'type' => 'sectionend' ),

				"{$section_id}_options_yith_end" => array(
					'type'  => 'yith_wcfm_sectionend',
					'value' => $section_id
				),
			);

			if ( ! empty( $this->_subsections ) ) {
				foreach ( $this->_subsections as $subsection_id => $subsection ) {
					$subsection_settings["{$section_id}_{$subsection_id}_section_name"] = array(
						'type'    => 'text',
						'id'      => "yith_wcfm_{$section_id}_{$subsection_id}_section_name",
						'title'   => sprintf( '%s %s', $subsection['name'], __( 'sub-section name', 'yith-frontend-manager-for-woocommerce' ) ),
						'default' => $subsection['name'],
						'desc'    => sprintf( '%s &rarr; %s &rarr; %s',
							__( 'Menu label on the frontend', 'yith-frontend-manager-for-woocommerce' ),
							$this->_default_section_name,
							$subsection['name']
						)
					);

					$subsection_settings["{$section_id}_{$subsection_id}_section_slug"] = array(
						'type'    => 'text',
						'id'      => "yith_wcfm_{$section_id}_{$subsection_id}_section_slug",
						'title'   => sprintf( '%s %s', $subsection['name'], __( 'sub-section slug', 'yith-frontend-manager-for-woocommerce' ) ),
						'default' => $subsection_id,
						'desc'    => sprintf( '%s &rarr; %s &rarr; %s %s',
							__( 'Endpoint for the frontend', 'yith-frontend-manager-for-woocommerce' ),
							$this->_default_section_name,
							$subsection['name'],
							_x( 'page', 'i.e.: Endpoint for the Frontend Manager Dashboard page', 'yith-frontend-manager-for-woocommerce' )
						),
					);
				}
			}

			return array_merge( $options, $section_settings, $subsection_settings, $section_settings_end );

		}

		/**
		 * Replace a page title with the endpoint title.
		 *
		 * @since    1.0.0
		 *
		 * @param  string $title
		 * @return string
		 *
		 * @return string endpoint title
		 */
		public function page_endpoint_title( $title ) {
			global $wp_query;

			if ( ! is_null( $wp_query ) && ! is_admin() && is_main_query() && in_the_loop() && is_page() && $this->is_current() ) {
				$title = $this->get_name();
				remove_filter( 'the_title', array( $this, 'page_endpoint_title' ) );
			}

			return $title;
		}

		/**
		 * General styles and scripts
		 *
		 * @return void
		 * @since  1.0.0
		 */
		final public function enqueue_scripts(){
			//Section Scripts and Styles

            $section_object = ! empty( YITH_Frontend_Manager()->gui ) ? YITH_Frontend_Manager()->gui->get_current_section_obj() : null;

            $is_current         = $this->is_current();
            $is_default_section = ! empty( $section_object ) && $this->get_id() == $section_object->get_id();

            if( $is_current || ( $is_default_section && ! empty(  YITH_Frontend_Manager()->gui ) && YITH_Frontend_manager()->gui->is_main_page() ) ){
            	wp_dequeue_script( 'yith-proteo-themejs' ); //Support for YITH Proteo
				$this->enqueue_section_scripts();
			}
		}

		/**
		 * Section styles and scripts
		 *
		 * Override this method in section class to enqueue
		 * particular styles and scripts only in correct
		 * section
		 *
		 * @return false
		 * @since  1.0.0
		 */
		public function enqueue_section_scripts(){
			return false;
		}

		/**
		 * Create Section class alias to dynamic class extends
		 *
		 * @since 1.0
		 * @retun void
		 * @access public static
		 */
		public static function section_class_alias() {
			if( ! class_exists( 'YITH_WCFM_Section' ) ){
				class_alias( __CLASS__, 'YITH_WCFM_Section' );
			}
		}

        /**
         * Add body classes
         *
         * @return array body classes
         * @since 1.0.0
         */
        public function body_classes( $classes ){
	        if ( ! empty(  YITH_Frontend_Manager()->gui ) && YITH_Frontend_Manager()->gui->is_main_page() || $this->is_current() ) {
				/**
				 * APPLY_FILTERS: yith_wcfm_use_my_account_style
				 *
				 * Filter if use my account style
				 *
				 * @param bool $use_style True by default.
				 *
				 * @return bool
				 */
		        if ( apply_filters( 'yith_wcfm_use_my_account_style', true ) ) {
			        if ( ! in_array( 'woocommerce-account', $classes ) ) {
				        $classes[] = 'woocommerce-account';
			        }

			        if ( ! in_array( 'woocommerce', $classes ) ) {
				        $classes[] = 'woocommerce';
			        }

		        }

               $classes[] = 'yith-wcfm-endpoint';
               $classes[] = YITH_WCFM_SLUG;

	            if( ! YITH_Frontend_Manager()->current_user_can_manage_woocommerce_on_front() ){
		            $classes[] = 'yith-frontend-manager-restricted';
	            }

	            if( $this->is_current() ){
	            	$classes[] = 'yith-wcfm-section-' . $this->get_id();
	            }

               //Get Theme Name
               $wp_theme = wp_get_theme();
	           $theme_name = str_replace( ' ', '-', strtolower( $wp_theme->Name ) );

               if( is_child_theme() ){
                   $child_theme_name = str_replace( ' ', '-', $theme_name );
                   $parent_theme_name = str_replace( ' ', '-', strtolower( $wp_theme->Template ) );
                   $theme_classes = array( $parent_theme_name, $child_theme_name );
                   foreach( $theme_classes as $class ){
                       if( ! in_array( $class, $classes ) ){
                           $classes[] = $class;
                       }
                   }
               }

               else {
                   if( ! in_array( $theme_name, $classes ) ){
                       $classes[] = $theme_name;
                   }
               }

               /* === WooCommerce Version Check === */
	            $classes[] = 'woocommerce-' . substr( WC()->version, 0, 3 );
            }

            return $classes;
        }

		/**
		 * Get allowed query string id
		 *
		 * @return string
		 * @since  1.7.0
		 */
		public function get_allowed_query_string() {
			return $this->allowed_query_string;
		}

		/**
		 * Set allowed query string id
		 *
		 * @return string
		 * @since  1.7.0
		 */
		public function set_allowed_query_string( $args ) {
			$this->allowed_query_string = $args;
		}

		/**
		 * Set the allowed query string in general array
		 */
		public function allowed_args_on_frontend_manager_query_string( $args ){
			if( ! empty( $this->get_allowed_query_string() ) ){
				$args[ $this->get_slug() ] = $this->get_allowed_query_string();
			}
			return $args;
		}
	}
}

add_action( 'yith_wcfm_after_load_common_classes', 'YITH_Frontend_Manager_Section::section_class_alias', 20 );
