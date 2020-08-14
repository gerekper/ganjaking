<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined ( 'ABSPATH' ) ) {
    exit( 'Direct access forbidden.' );
}

if( ! class_exists( 'YITH_Frontend_Manager_GUI' ) ){

    class YITH_Frontend_Manager_GUI{

        /**
         * Sections array
         * 
         * @var array|mixed|void
         * @since 1.0.0.
         */
        protected $_sections = array();

        /**
         * Main Page ID
         *
         * @var string
         * @since 1.0.0
         */
        public $main_page_id = '';

        /**
         * YITH_Frontend_Manager_GUI constructor.
         */
        public function __construct(){
	        /**
	         * WPML Support
	         */
            $this->main_page_id = apply_filters( 'wpml_object_id', get_option( 'yith_wcfm_main_page_id' ) );

            add_action( 'init', array( $this, 'install' ), 20 );
            add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );

            /* Redirect after login */
            add_action( 'woocommerce_login_form_end', array( $this, 'login_redirect' ) );

            /* Register Style */
            add_action( 'wp_enqueue_scripts', array( $this, 'register_style' ), 5 );

            /* === Unauthorized Access === */
            add_action( 'yith_wcfm_print_section_unauthorized', array( $this, 'unauthorized' ), 10, 1 );

            /* Add Support to YITH Live Chat */
            add_filter( 'ylc_can_show_chat', array( $this, 'remove_chat_on_frontend_page' ), 15 );

            /* My Account Style Fix for not logged in users */
	       add_action( 'body_class', array( $this, 'add_body_class' ), 20 );

            /* Remove dynamics.css file from YITH 2.0 theme options from Skins */
	        if( function_exists( 'YIT_Css' ) ){
		        $option_not_in = array(
		        	'none',
			        'default'
		        );

	        	$current_skin = get_option( 'yith_wcfm_skin', 'default' );

		        if( ! in_array( $current_skin, $option_not_in ) && $this->is_main_page() ){
			        remove_action('wp_enqueue_scripts', array( YIT_Css(), 'enqueue_dynamics_css' ), 16);
		        }
	        }

	        /* Add support to TM Extra Product Options plugin */
	        if( function_exists( 'TM_EPO_ADMIN' ) ){
		        TM_EPO_ADMIN();
	        }
        }

        /**
         * GUI Install
         *
         * @since 1.0
         * @return void
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function install(){
            /* Main frontend manager shortcode */
            add_shortcode( yith_wcfm_get_main_shortcode_name(), array( $this, 'print_main_shortcode' ) );

            /* Sections */
            $this->_sections = YITH_Frontend_Manager()->get_section();

            /* Endpoints */
            $this->_install_endpoints();

            /* Shortcodes */
            $this->_add_schortcodes();
        }

        /**
         * Add shortcodes
         *
         * @since 1.0
         * @return void
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        protected function _add_schortcodes(){
            if( $this->_sections ){
                foreach( $this->_sections as $id => $section ){
                    $is_section_enabled = yith_wcfm_is_section_enabled( $section );
                    if( $is_section_enabled ){
                        $shortcode_name = 'yith_woocommerce_frontend_manager_' . str_replace( '-', '_', $id );
                        add_shortcode( $shortcode_name, array( $section, 'print_shortcode' ) );
                        if( $section->has_subsections() ){
                            $subsections = $section->get_subsections();
                            if( $subsections ){
                                foreach ( $subsections as $subsection_id => $subsection ) {
                                    $sub_shortcode_name = $shortcode_name . '_' . $subsection_id;
                                    add_shortcode( $sub_shortcode_name, array( $section, 'print_shortcode' ) );
                                }
                            }
                        }
                    }
                }
            }
        }

        /**
         * Print the frontend manager navigation menu
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @return string HTML navigation
         * @since 1.0.0
         */
        public function print_navigation(){
            $navigation_wrapper_args = apply_filters( 'yith_wcfm_navigation_template_args', array(
                'navigation_wrapper_classes' => 'yith-wcfm-navigation woocommerce-MyAccount-navigation'
            ) );

            return yith_wcfm_get_template( 'frontend-manager-navigation', $navigation_wrapper_args, false, false );
        }

        /**
         * Print the frontend manager current endpoint content
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @return string HTML content
         * @since 1.0.0
         */
        public function print_content(){
            global $wp_query;

            $default_endpoint            = apply_filters( 'yith_wcfm_default_endpoint', $this->get_default_endpoint() );
            $default_endpoint_subsection = apply_filters( 'yith_wcfm_default_endpoint_subsection', '' );
            $query_vars                  = $wp_query->query_vars;
            $sections                    = $this->get_sections();

            //$current_endpoint_id[ section_id ] = query_vars_key
            $current_endpoint_id    = array_intersect( wp_list_pluck( $sections, 'slug' ), array_keys( $query_vars ) );
            $current_sections_ids   = array_keys( $current_endpoint_id );
            $current_section_id     = ! empty( $current_sections_ids ) ? array_pop( $current_sections_ids ) : $default_endpoint;
            $current_section        = empty( $current_section_id ) ? $sections[ $default_endpoint ] : $sections[ $current_section_id ];

            $current_query_vars_ids   = array_values( $current_endpoint_id );
            $current_query_var_id     = array_pop( $current_query_vars_ids );

            $current_query_var_value = isset( $query_vars[ $current_query_var_id ] ) ? $query_vars[ $current_query_var_id ] : '';
            $current_subsection_id = array_search( $current_query_var_value, wp_list_pluck( $current_section->get_subsections(), 'slug' ) );

            $endpoint_shortcode = yith_wcfm_get_main_shortcode_name() . "_{$current_section_id}";

            if( $current_query_var_value ){
                $endpoint_shortcode .= "_{$current_subsection_id}";
            }

            $content_args = apply_filters( 'yith_wcfm_content_template_args', array(
                'atts' => array(
                    'section'    => ! empty( $current_section_id ) ? $current_section_id : $default_endpoint,
                    'subsection' => ! empty( $current_subsection_id ) ? $current_subsection_id : ''
                ),
                'content'                   => '',
                'endpoint_shortcode'        => $endpoint_shortcode,
                'obj'                       => $current_section,
                'content_wrapper_classes'   => 'yith-wcfm-content woocommerce-MyAccount-content'
            ),  $current_section_id,
                $current_query_var_id,
                $current_section,
                $query_vars
            );

            return yith_wcfm_get_template( 'frontend-manager-content', $content_args, '', false );
        }

        /***
         * Get default endpoint url
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function get_default_endpoint(){
            $default = 'dashboard';
            $default_section = YITH_Frontend_Manager()->gui->get_section( $default );
            if( ! empty( $default_section ) && ! $default_section->is_enabled() ){
                $sections = apply_filters( 'yith_wcfm_get_sections_before_print_navigation', YITH_Frontend_Manager()->gui->get_sections() );
                foreach( $sections as $endpoint => $section ){
                    $is_section_enabled = yith_wcfm_is_section_enabled( $section );
                    if( $is_section_enabled ){
                        $default = $section->get_id();
                        break;
                    }
                }
            }
            return $default;
        }

        /**
         * Install sections
         *
         * @author Antonio La Rocca <antonio.larocca@yithemes.com>
         * @return void
         * @since 1.0.0
         */
        protected function _install_sections(){
            $sections = array_keys( YITH_Frontend_Manager()->available_sections );

            if( $sections ){
                foreach( $sections as $section ){
                    $section_obj = new $section();
                    $this->_sections[ $section_obj->get_id() ] = $section_obj;
                }
            }
        }

        /**
         * Register plugin endpoints
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @return void
         * @since 1.0.0
         */
        protected function _install_endpoints(){
            /** @var YITH_Frontend_Manager_Section $obj  */
            $current_db_version = get_option( 'yith_wcfm_db_version', '1.0.0' );
            $wc_endpoints = WC()->query->query_vars;
            foreach ( $this->_sections as $endpoint => $obj ) {
                $slug            = $obj->get_slug();
                $is_section_enabled = apply_filters( 'yith_wcfm_is_section_enabled', true, $obj, $endpoint );
                $is_private_endpoint = in_array( $slug, $wc_endpoints );
                if( ! $is_private_endpoint ){
                    if( $is_section_enabled ) {
                        add_rewrite_endpoint( $slug, EP_ROOT | EP_PAGES | EP_PERMALINK );
                    }
                }

                else {
                    trigger_error( sprintf( 'The <b>%s</b> endpoint is already registered by WooCommerce', $obj->get_slug() ), E_USER_WARNING );
                }

                if( $is_section_enabled ){
                    $subsections = $obj->get_subsections();
                    foreach( $subsections as $subsection_id => $subsection ){
                        if( in_array( $subsection['slug'], $wc_endpoints ) ){
                            trigger_error( sprintf( 'The <b>%s</b> value of <i>%s</i> endpoint is already registered by WooCommerce', $subsection['slug'], $endpoint ), E_USER_WARNING );
                        }
                    }
                }
            }

            // flush rewrite rule only on db changes (flush can be very expensive operation, and it is really unlikely to change endpoints without changing db)
            $option_updated  = get_site_transient( YITH_Frontend_Manager()->get_rewrite_rules_transient() );
            $develpment_mode = ( defined( 'YITH_WCFM_DEVELOPMENT' ) && YITH_WCFM_DEVELOPMENT );

            if( $current_db_version != YITH_WCFM_DB_VERSION || isset( $_GET['yith_wcfm_force_install_endpoints'] ) || $develpment_mode || $option_updated ) {
            	set_site_transient( YITH_Frontend_Manager()->get_rewrite_rules_transient(), false );
	            flush_rewrite_rules();
            }
        }

        /**
         * Register plugins query vars
         *
         * @param $vars mixed Available query vars
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @return mixed Filtered query vars
         * @since 1.0.0
         */
        public function add_query_vars( $vars ){
            foreach ( $this->_sections as $endpoint => $obj ) {
                $vars[] = $obj->get_id();
            }

            return $vars;
        }

        /**
         * Get sections array
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @return mixed Array The sections list
         * @since 1.0.0
         */
        public function get_sections(){
            return $this->_sections;
        }

        /**
         * Get section
         *
         * @param $section section_id
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @return object section object
         * @since 1.0.0
         */
        public function get_section( $section = '' ){
            $sections = $this->get_sections();
            $return = false;

            if( ! empty( $section ) && ! empty( $sections[ $section ] ) ){
                $return = $sections[ $section ];
            }
            return $return;
        }

        /**
         * Get endpoints array
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @return mixed Array The endpoint list
         * @since 1.0.0
         */
        public function get_endpoints(){
            return array_keys( $this->_sections );
        }

        /**
         * Print the main shortcode content
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @return string HTML content
         * @since 1.0.0
         */
        public function print_main_shortcode(){
        	$sc_content = '';
            if( is_user_logged_in() ){
                if( YITH_Frontend_Manager()->current_user_can_manage_woocommerce_on_front() ){
                    $navigation = $this->print_navigation();
                    $content = $this->print_content();

	                $sc_content = ($navigation . $content );
                }

                else{
	                $sc_content = $this->unauthorized();
                }
            }

            else {
	            $sc_content = wc_get_template_html( 'myaccount/form-login.php' );
            }
            return $sc_content;
        }

        /**
         * Print unahthorized message
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @return string HTML content
         * @since 1.0.0
         */
        public function unauthorized( $post_type = '', $args = array() ){
            $default_args = apply_filters( 'yith_wcfm_restricted_area_args', array(
                    'alert_title'       => get_option( 'yith_wcfm_not_authorized_title' ),
                    'alert_title_class' => 'yith-wcfm-unauthorized-title woocommerce-error',
                    'alert_message'     => get_option( 'yith_wcfm_not_authorized_message' ),
                )
            );

            $hook = ! empty( $post_type ) ? "yith_wcfm_restricted_{$post_type}_section_args" : "yith_wcfm_restricted_section_args";

            $args = apply_filters( $hook, wp_parse_args( $args, $default_args ) );

            return yith_wcfm_get_template( 'frontend-manager-unauthorized', $args, '', false );
        }

        /**
         * check if the current page is the main page
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @return bool TRUE if the current page is frontend manager main page, false otherwise
         * @since 1.0.0
         */
        public function is_main_page(){
            return is_page( $this->main_page_id );
        }

        /**
         * Check if the login form are display in a frontend manager endpoint
         *
         * @since 1.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @return void
         */
        public function login_redirect() {
            global $wp;
            $redirect = false;

            $current_section_obj = $this->get_current_section_obj();
            
            if ( $this->is_main_page() ) {
                //IS Frontend Manager Page ?
                if ( $current_section_obj ) {
                    $current_subsection = $current_section_obj->get_current_subsection();
                    $subsection_slug    = '';

                    if ( is_array( $current_subsection ) ) {
                        $current_subsection = array_pop( $current_subsection );
                        $subsection_slug    = $current_subsection['slug'];
                    }

                    $redirect = $current_section_obj->get_url( $subsection_slug );
                } else {
                    //If not redirect to Frontend Manager Main Page
                    $redirect = yith_wcfm_get_main_page_url();
                }

                if ( $redirect ) {
                    printf( '<input type="hidden" name="redirect" value="%s" />', $redirect );
                }
            }
        }

        /**
         * Get current section obj
         * 
         * @since 1.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @return section obj if the user can see a frontend manager section, false otherwise
         */
        public function get_current_section_obj(){
            global $wp;
            $section = false;

            //IS Frontend Manager Page ?
            if ( $this->is_main_page() ) {
                foreach( $this->get_sections() as $is => $obj ){
                    if( $obj->is_current() ){
                        $section = $obj;
                        break;
                    }
                }
            }

            return $section ? $section : $this->get_section( $this->get_default_endpoint() );
        }

        /**
         * Wrap for current_user_can( 'manage_woocommerce' )
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 1.0
         * @deprecated since 1.2.2 in favour of YITH_Frontend_Manager()->current_user_can_manage_woocommerce_on_front()
         *
         * @return bool
         */
        public function current_user_can_manage_woocommerce_on_front(){
	        wc_deprecated_function( 'YITH_Frontend_Manager()->gui->current_user_can_manage_woocommerce_on_front()', '1.2.2', 'YITH_Frontend_Manager()->current_user_can_manage_woocommerce_on_front()' );
	        return YITH_Frontend_Manager()->current_user_can_manage_woocommerce_on_front();
        }

        /**
         * Register styles and scripts
         *
         * @since 1.0.0
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @return void
         */
        public function register_style(){
            global $wp_scripts, $wp_version;
            $jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.11.4';
            $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

            /* === Styles === */
            wp_register_style( 'jquery-ui-style', '//code.jquery.com/ui/' . $jquery_version . '/themes/smoothness/jquery-ui.min.css', array(), $jquery_version );
            wp_register_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );
            wp_register_style( 'woocommerce_admin_menu_styles', WC()->plugin_url() . '/assets/css/menu.css', array(), WC_VERSION );
            wp_register_style( 'woocommerce_admin_dashboard_styles', WC()->plugin_url() . '/assets/css/dashboard.css', array(), WC_VERSION );
            wp_register_style( 'woocommerce_admin_print_reports_styles', WC()->plugin_url() . '/assets/css/reports-print.css', array(), WC_VERSION, 'print' );
            wp_register_style( 'select2', WC()->plugin_url() . '/assets/css/select2.css', array(), WC_VERSION );
	        wp_register_style( 'wp-color-picker', admin_url( "css/color-picker$suffix.css" ), array(), $jquery_version );

	        if( ! wp_style_is( 'wp-admin' ) ){
		        wp_register_style( 'wp-admin', admin_url( 'css/wp-admin.css' ), array( 'dashicons', 'common', 'forms', 'admin-menu', 'dashboard', 'list-tables', 'edit', 'revisions', 'media', 'themes', 'about', 'nav-menus', 'widgets', 'site-icon', 'l10n' ) );
	        }

	        /* === Main CSS to include in all pages ===*/
	        if ( $this->is_main_page() ){
		        wp_enqueue_style( 'yith-wcfm-general', YITH_WCFM_URL . 'assets/css/general.css', array(), YITH_WCFM_VERSION );
	        }

            /* === Scripts === */
	        if ( ! wp_script_is( 'wp-color-picker', 'registered' ) ) {
		        wp_register_script( 'iris',  admin_url( 'js/iris.min.js' ), array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), false, true );
		        wp_register_script( 'wp-color-picker', admin_url( 'js/color-picker.min.js' ), array( 'jquery', 'iris' ), false, true );
	        }

	        wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', array(
			        'clear'            => __( 'Clear' ),
			        'clearAriaLabel'   => __( 'Clear color' ),
			        'defaultString'    => __( 'Default' ),
			        'defaultAriaLabel' => __( 'Select default color' ),
			        'pick'             => __( 'Select Color' ),
			        'defaultLabel'     => __( 'Color value' ),
		        )
	        );


	       if( ! wp_script_is( 'wp-post', 'registered' ) ){
		       wp_register_script( 'wp-ajax-response',  ABSPATH . '/wp-includes/js/wp-ajax-response.js', array( 'jquery' ), false, true );
		       wp_register_script( 'wp-lists', ABSPATH . '/wp-includes/js/wp-lists.js', array( 'jquery', 'wp-ajax-response' ), false, true );
		       wp_register_script( 'wp-util', ABSPATH . '/wp-includes/js/wp-util.js', array( 'jquery' ), false, true );
		       wp_register_script( 'wp-word-count', admin_url( 'js/word-count.js' ), array( 'jquery', 'wp-util' ), false, true );
		       wp_register_script( 'wp-postbox', admin_url( 'js/postbox.js' ), array( 'jquery', 'wp-lists' ), false, true );
		       wp_register_script( 'wp-editcomments', admin_url( 'js/edit-comments.js') , array( 'jquery', 'wp-lists' ), false, true );
		       wp_register_script( 'wp-post', admin_url( 'js/post.js' ), array( 'jquery', 'wp-lists', 'wp-util', 'wp-postbox', 'wp-editcomments', 'wp-word-count' ), false, true );

		       wp_localize_script( 'wp-lists', 'ajaxurl', admin_url( 'admin-ajax.php' ) );
		       wp_localize_script( 'wp-post', 'pagenow', 'shop_order' );
	       }

	        wp_register_script( 'jquery-ui-core', ABSPATH . "/wp-includes/js/jquery/ui/core$suffix.js", array('jquery'), $jquery_version, true );
	        wp_register_script( 'jquery-ui-widget', ABSPATH . "/wp-includes/js/jquery/ui/widget$suffix.js", array('jquery'), $jquery_version, true );
	        wp_register_script( 'jquery-ui-mouse', ABSPATH . "/wp-includes/js/jquery/ui/mouse$suffix.js", array( 'jquery-ui-core', 'jquery-ui-widget' ), $jquery_version, true );
	        wp_register_script( 'jquery-ui-resizable', ABSPATH . "/wp-includes/js/jquery/ui/resizable$suffix.js", array('jquery-ui-mouse'), $jquery_version, true );
	        wp_register_script( 'jquery-ui-draggable', ABSPATH . "/wp-includes/js/jquery/ui/draggable$suffix.js", array('jquery-ui-mouse'), $jquery_version, true );
	        wp_register_script( 'jquery-ui-button', ABSPATH . "/wp-includes/js/jquery/ui/button$suffix.js", array('jquery-ui-core', 'jquery-ui-widget'), $jquery_version, true );
	        wp_register_script( 'jquery-ui-position', ABSPATH . "/wp-includes/js/jquery/ui/position$suffix.js", array('jquery'), $jquery_version, true );
	        wp_register_script( 'jquery-ui-dialog', ABSPATH . "wp-includes/js/jquery/ui/dialog$suffix.js", array( 'jquery-ui-resizable', 'jquery-ui-draggable', 'jquery-ui-button', 'jquery-ui-position' ), $jquery_version, true );
            wp_register_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip' . $suffix . '.js', array( 'jquery' ), WC_VERSION, true );
            wp_register_script( 'jquery-blockui', WC()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), '2.70', true );
            wp_register_script( 'woocommerce_admin', WC()->plugin_url() . '/assets/js/admin/woocommerce_admin' . $suffix . '.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip' ), WC_VERSION, true );
            wp_register_script( 'jquery-blockui', WC()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), '2.70', true );
            wp_register_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip' . $suffix . '.js', array( 'jquery' ), WC_VERSION, true );
            wp_register_script( 'accounting', WC()->plugin_url() . '/assets/js/accounting/accounting' . $suffix . '.js', array( 'jquery' ), '0.4.2', true );
            wp_register_script( 'round', WC()->plugin_url() . '/assets/js/round/round' . $suffix . '.js', array( 'jquery' ), WC_VERSION, true );
            wp_register_script( 'wc-admin-meta-boxes', WC()->plugin_url() . '/assets/js/admin/meta-boxes' . $suffix . '.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable', 'accounting', 'round', 'wc-enhanced-select', 'plupload-all', 'stupidtable', 'jquery-tiptip' ), WC_VERSION, true );
            wp_register_script( 'zeroclipboard', WC()->plugin_url() . '/assets/js/zeroclipboard/jquery.zeroclipboard' . $suffix . '.js', array( 'jquery' ), WC_VERSION, true );
            wp_register_script( 'qrcode', WC()->plugin_url() . '/assets/js/jquery-qrcode/jquery.qrcode' . $suffix . '.js', array( 'jquery' ), WC_VERSION, true );
            wp_register_script( 'stupidtable', WC()->plugin_url() . '/assets/js/stupidtable/stupidtable' . $suffix . '.js', array( 'jquery' ), WC_VERSION, true );
            wp_register_script( 'serializejson', WC()->plugin_url() . '/assets/js/jquery-serializejson/jquery.serializejson' . $suffix . '.js', array( 'jquery' ), '2.6.1', true );
            wp_register_script( 'flot', WC()->plugin_url() . '/assets/js/jquery-flot/jquery.flot' . $suffix . '.js', array( 'jquery' ), WC_VERSION, true );
            wp_register_script( 'flot-resize', WC()->plugin_url() . '/assets/js/jquery-flot/jquery.flot.resize' . $suffix . '.js', array( 'jquery', 'flot' ), WC_VERSION, true );
            wp_register_script( 'flot-time', WC()->plugin_url() . '/assets/js/jquery-flot/jquery.flot.time' . $suffix . '.js', array( 'jquery', 'flot' ), WC_VERSION, true );
            wp_register_script( 'flot-pie', WC()->plugin_url() . '/assets/js/jquery-flot/jquery.flot.pie' . $suffix . '.js', array( 'jquery', 'flot' ), WC_VERSION, true );
            wp_register_script( 'flot-stack', WC()->plugin_url() . '/assets/js/jquery-flot/jquery.flot.stack' . $suffix . '.js', array( 'jquery', 'flot' ), WC_VERSION, true );
            wp_register_script( 'wc-settings-tax', WC()->plugin_url() . '/assets/js/admin/settings-views-html-settings-tax' . $suffix . '.js', array( 'jquery', 'wp-util', 'underscore', 'backbone', 'jquery-blockui' ), WC_VERSION, true );
            wp_register_script( 'wc-backbone-modal', WC()->plugin_url() . '/assets/js/admin/backbone-modal' . $suffix . '.js', array( 'underscore', 'backbone', 'wp-util' ), WC_VERSION, true );
            wp_register_script( 'wc-shipping-zones', WC()->plugin_url() . '/assets/js/admin/wc-shipping-zones' . $suffix . '.js', array( 'jquery', 'wp-util', 'underscore', 'backbone', 'jquery-ui-sortable', 'wc-enhanced-select', 'wc-backbone-modal' ), WC_VERSION, true );
            wp_register_script( 'wc-shipping-zone-methods', WC()->plugin_url() . '/assets/js/admin/wc-shipping-zone-methods' . $suffix . '.js', array( 'jquery', 'wp-util', 'underscore', 'backbone', 'jquery-ui-sortable', 'wc-backbone-modal' ), WC_VERSION, true );
            wp_register_script( 'wc-shipping-classes', WC()->plugin_url() . '/assets/js/admin/wc-shipping-classes' . $suffix . '.js', array( 'jquery', 'wp-util', 'underscore', 'backbone' ), WC_VERSION, true );
	        wp_register_script( 'select2', WC()->plugin_url() . '/assets/js/select2/select2.full' . $suffix . '.js', array( 'jquery' ), '4.0.3' );

	        /**
	         * Check if website running WooCommerce 3.2
	         */
	        $wc_enhanced_select_script_deps = array( 'jquery', 'selectYITH' );
	        $selectYITH_deps = array( 'jquery' );

	        if( YITH_Frontend_Manager()->is_wc_3_2_or_greather ){
		        wp_register_script( 'selectWoo', WC()->plugin_url() . '/assets/js/selectWoo/selectWoo.full' . $suffix . '.js', array( 'jquery' ), '1.0.0', true );
		        wp_register_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip' . $suffix . '.js', array( 'jquery' ), WC_VERSION, true );
		        $selectYITH_deps[] = 'selectWoo';
	        }

	        else {
		        wp_register_script( 'wc-tooltip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.min.js', array( 'jquery', 'select2' ), WC_VERSION, true );
		        $selectYITH_deps[] = 'select2';
	        }

	        if( ! wp_script_is( 'selectYITH', 'registered' ) ) {
		        wp_register_script( 'selectYITH', YITH_WCFM_URL . 'assets/js/selectYITH' . $suffix . '.js', $selectYITH_deps, YITH_WCFM_VERSION, true );
	        }

	        wp_register_script( 'wc-enhanced-select', WC()->plugin_url() . '/assets/js/admin/wc-enhanced-select' . $suffix . '.js', $wc_enhanced_select_script_deps, WC_VERSION, true );
            wp_register_script( 'wc-admin-order-meta-boxes', WC()->plugin_url() . '/assets/js/admin/meta-boxes-order' . $suffix . '.js', array( 'wc-admin-meta-boxes', 'wc-backbone-modal' ), WC_VERSION, false );
            wp_register_script( 'wc-reports', WC()->plugin_url() . '/assets/js/admin/reports' . $suffix . '.js', array( 'jquery', 'jquery-ui-datepicker', 'wc-enhanced-select' ), WC_VERSION, true );

            /* === Localize === */
            $wc_enhanced_select_params = array(
                'i18n_matches_1'            => _x( 'One result is available, press enter to select.', 'enhanced select', 'woocommerce' ),
                'i18n_matches_n'            => _x( '%qty% results are available, use up and down arrow keys to navigate.', 'enhanced select', 'woocommerce' ),
                'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'woocommerce' ),
                'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'woocommerce' ),
                'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'woocommerce' ),
                'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'woocommerce' ),
                'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'woocommerce' ),
                'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'woocommerce' ),
                'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'woocommerce' ),
                'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'woocommerce' ),
                'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'woocommerce' ),
                'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'woocommerce' ),
                'ajax_url'                  => admin_url( 'admin-ajax.php' ),
                'search_products_nonce'     => wp_create_nonce( 'search-products' ),
                'search_customers_nonce'    => wp_create_nonce( 'search-customers' )
            );

            wp_localize_script( 'wc-enhanced-select', 'wc_enhanced_select_params', $wc_enhanced_select_params );

            $locale  = localeconv();
            $decimal = isset( $locale['decimal_point'] ) ? $locale['decimal_point'] : '.';

            $wc_admins_params = array(
                /* translators: %s: decimal */
                'i18n_decimal_error'                => sprintf( __( 'Please enter in decimal (%s) format without thousand separators.', 'woocommerce' ), $decimal ),
                /* translators: %s: price decimal separator */
                'i18n_mon_decimal_error'            => sprintf( __( 'Please enter in monetary decimal (%s) format without thousand separators and currency symbols.', 'woocommerce' ), wc_get_price_decimal_separator() ),
                'i18n_country_iso_error'            => __( 'Please enter in country code with two capital letters.', 'woocommerce' ),
                'i18_sale_less_than_regular_error'  => __( 'Please enter in a value less than the regular price.', 'woocommerce' ),
                'decimal_point'                     => $decimal,
                'mon_decimal_point'                 => wc_get_price_decimal_separator(),
                'strings' => array(
                    'import_products' => __( 'Import', 'woocommerce' ),
                    'export_products' => __( 'Export', 'woocommerce' ),
                ),
                'urls' => array(
                    'import_products' => esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_importer' ) ),
                    'export_products' => esc_url_raw( admin_url( 'edit.php?post_type=product&page=product_exporter' ) ),
                ),
            );

            wp_localize_script( 'woocommerce_admin', 'woocommerce_admin', $wc_admins_params );

            $wc_admin_order_meta_boxes_params = array(
                'countries'              => json_encode( array_merge( WC()->countries->get_allowed_country_states(), WC()->countries->get_shipping_country_states() ) ),
                'i18n_select_state_text' => esc_attr__( 'Select an option&hellip;', 'woocommerce' )
            );

            wp_localize_script( 'wc-admin-order-meta-boxes', 'woocommerce_admin_meta_boxes_order', $wc_admin_order_meta_boxes_params );

            $post_id = isset( $_GET['id'] ) && $_GET['id'] > 0 ?  $_GET['id'] : '';
            $currency = '';

            if ( $post_id && in_array( get_post_type( $post_id ), wc_get_order_types( 'order-meta-boxes' ) ) ) {
                $order    = wc_get_order( $post_id );
                $get_currency = YITH_Frontend_Manager()->is_wc_3_0_or_greather ? 'get_currency' : 'get_order_currency';
                $currency = $order->$get_currency();
            }

            $admin_meta_boxes_params = array(
                'remove_item_notice'            => __( 'Are you sure you want to remove the selected items? If you have previously reduced this item\'s stock, or this order was submitted by a customer, you will need to manually restore the item\'s stock.', 'woocommerce' ),
                'i18n_select_items'             => __( 'Please select some items.', 'woocommerce' ),
                'i18n_do_refund'                => __( 'Are you sure you wish to process this refund? This action cannot be undone.', 'woocommerce' ),
                'i18n_delete_refund'            => __( 'Are you sure you wish to delete this refund? This action cannot be undone.', 'woocommerce' ),
                'i18n_delete_tax'               => __( 'Are you sure you wish to delete this tax column? This action cannot be undone.', 'woocommerce' ),
                'remove_item_meta'              => __( 'Remove this item meta?', 'woocommerce' ),
                'remove_attribute'              => __( 'Remove this attribute?', 'woocommerce' ),
                'name_label'                    => __( 'Name', 'woocommerce' ),
                'remove_label'                  => __( 'Remove', 'woocommerce' ),
                'click_to_toggle'               => __( 'Click to toggle', 'woocommerce' ),
                'values_label'                  => __( 'Value(s)', 'woocommerce' ),
                'text_attribute_tip'            => __( 'Enter some text, or some attributes by pipe (|) separating values.', 'woocommerce' ),
                'visible_label'                 => __( 'Visible on the product page', 'woocommerce' ),
                'used_for_variations_label'     => __( 'Used for variations', 'woocommerce' ),
                'new_attribute_prompt'          => __( 'Enter a name for the new attribute term:', 'woocommerce' ),
                'calc_totals'                   => __( 'Calculate totals based on order items, discounts, and shipping?', 'woocommerce' ),
                'calc_line_taxes'               => __( 'Calculate line taxes? This will calculate taxes based on the customers country. If no billing/shipping is set it will use the store base country.', 'woocommerce' ),
                'copy_billing'                  => __( 'Copy billing information to shipping information? This will remove any currently entered shipping information.', 'woocommerce' ),
                'load_billing'                  => __( 'Load the customer\'s billing information? This will remove any currently entered billing information.', 'woocommerce' ),
                'load_shipping'                 => __( 'Load the customer\'s shipping information? This will remove any currently entered shipping information.', 'woocommerce' ),
                'featured_label'                => __( 'Featured', 'woocommerce' ),
                'prices_include_tax'            => esc_attr( get_option( 'woocommerce_prices_include_tax' ) ),
                'tax_based_on'                  => esc_attr( get_option( 'woocommerce_tax_based_on' ) ),
                'round_at_subtotal'             => esc_attr( get_option( 'woocommerce_tax_round_at_subtotal' ) ),
                'no_customer_selected'          => __( 'No customer selected', 'woocommerce' ),
                'plugin_url'                    => WC()->plugin_url(),
                'ajax_url'                      => admin_url( 'admin-ajax.php' ),
                'order_item_nonce'              => wp_create_nonce( 'order-item' ),
                'add_attribute_nonce'           => wp_create_nonce( 'add-attribute' ),
                'save_attributes_nonce'         => wp_create_nonce( 'save-attributes' ),
                'calc_totals_nonce'             => wp_create_nonce( 'calc-totals' ),
                'get_customer_details_nonce'    => wp_create_nonce( 'get-customer-details' ),
                'search_products_nonce'         => wp_create_nonce( 'search-products' ),
                'grant_access_nonce'            => wp_create_nonce( 'grant-access' ),
                'revoke_access_nonce'           => wp_create_nonce( 'revoke-access' ),
                'add_order_note_nonce'          => wp_create_nonce( 'add-order-note' ),
                'delete_order_note_nonce'       => wp_create_nonce( 'delete-order-note' ),
                'calendar_image'                => WC()->plugin_url().'/assets/images/calendar.png',
                'post_id'                       => $post_id,
                'base_country'                  => WC()->countries->get_base_country(),
                'currency_format_num_decimals'  => wc_get_price_decimals(),
                'currency_format_symbol'        => get_woocommerce_currency_symbol( $currency ),
                'currency_format_decimal_sep'   => esc_attr( wc_get_price_decimal_separator() ),
                'currency_format_thousand_sep'  => esc_attr( wc_get_price_thousand_separator() ),
                'currency_format'               => esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ), // For accounting JS
                'rounding_precision'            => wc_get_rounding_precision(),
                'tax_rounding_mode'             => WC_TAX_ROUNDING_MODE,
                'product_types'                 => array_unique( array_merge( array( 'simple', 'grouped', 'variable', 'external' ), array_keys( wc_get_product_types() ) ) ),
                'i18n_download_permission_fail' => __( 'Could not grant access - the user may already have permission for this file or billing email is not set. Ensure the billing email is set, and the order has been saved.', 'woocommerce' ),
                'i18n_permission_revoke'        => __( 'Are you sure you want to revoke access to this download?', 'woocommerce' ),
                'i18n_tax_rate_already_exists'  => __( 'You cannot add the same tax rate twice!', 'woocommerce' ),
                'i18n_product_type_alert'       => __( 'Your product has variations! Before changing the product type, it is a good idea to delete the variations to avoid errors in the stock reports.', 'woocommerce' ),
                'i18n_delete_note'              => __( 'Are you sure you wish to delete this note? This action cannot be undone.', 'woocommerce' )
            );

            wp_localize_script( 'wc-admin-meta-boxes', 'woocommerce_admin_meta_boxes', $admin_meta_boxes_params );

            // Products
            wp_register_script( 'woocommerce_quick-edit', WC()->plugin_url() . '/assets/js/admin/quick-edit' . $suffix . '.js', array( 'jquery', 'woocommerce_admin' ), WC_VERSION, true );
            wp_register_script( 'wc-admin-product-meta-boxes', WC()->plugin_url() . '/assets/js/admin/meta-boxes-product' . $suffix . '.js', array( 'wc-admin-meta-boxes', 'media-models' ), WC_VERSION, true );
            wp_register_script( 'wc-admin-variation-meta-boxes', WC()->plugin_url() . '/assets/js/admin/meta-boxes-product-variation' . $suffix . '.js', array( 'wc-admin-meta-boxes', 'serializejson', 'media-models' ), WC_VERSION, false );

            $params = array(
                'post_id'                             => isset( $post ) && isset( $post->ID ) ? $post->ID : '',
                'plugin_url'                          => WC()->plugin_url(),
                'ajax_url'                            => admin_url( 'admin-ajax.php' ),
                'woocommerce_placeholder_img_src'     => wc_placeholder_img_src(),
                'add_variation_nonce'                 => wp_create_nonce( 'add-variation' ),
                'link_variation_nonce'                => wp_create_nonce( 'link-variations' ),
                'delete_variations_nonce'             => wp_create_nonce( 'delete-variations' ),
                'load_variations_nonce'               => wp_create_nonce( 'load-variations' ),
                'save_variations_nonce'               => wp_create_nonce( 'save-variations' ),
                'bulk_edit_variations_nonce'          => wp_create_nonce( 'bulk-edit-variations' ),
                'i18n_link_all_variations'            => esc_js( sprintf( __( 'Are you sure you want to link all variations? This will create a new variation for each and every possible combination of variation attributes (max %d per run).', 'woocommerce' ), defined( 'WC_MAX_LINKED_VARIATIONS' ) ? WC_MAX_LINKED_VARIATIONS : 50 ) ),
                'i18n_enter_a_value'                  => esc_js( __( 'Enter a value', 'woocommerce' ) ),
                'i18n_enter_menu_order'               => esc_js( __( 'Variation menu order (determines position in the list of variations)', 'woocommerce' ) ),
                'i18n_enter_a_value_fixed_or_percent' => esc_js( __( 'Enter a value (fixed or %)', 'woocommerce' ) ),
                'i18n_delete_all_variations'          => esc_js( __( 'Are you sure you want to delete all variations? This cannot be undone.', 'woocommerce' ) ),
                'i18n_last_warning'                   => esc_js( __( 'Last warning, are you sure?', 'woocommerce' ) ),
                'i18n_choose_image'                   => esc_js( __( 'Choose an image', 'woocommerce' ) ),
                'i18n_set_image'                      => esc_js( __( 'Set variation image', 'woocommerce' ) ),
                'i18n_variation_added'                => esc_js( __( "variation added", 'woocommerce' ) ),
                'i18n_variations_added'               => esc_js( __( "variations added", 'woocommerce' ) ),
                'i18n_no_variations_added'            => esc_js( __( "No variations added", 'woocommerce' ) ),
                'i18n_remove_variation'               => esc_js( __( 'Are you sure you want to remove this variation?', 'woocommerce' ) ),
                'i18n_scheduled_sale_start'           => esc_js( __( 'Sale start date (YYYY-MM-DD format or leave blank)', 'woocommerce' ) ),
                'i18n_scheduled_sale_end'             => esc_js( __( 'Sale end date (YYYY-MM-DD format or leave blank)', 'woocommerce' ) ),
                'i18n_edited_variations'              => esc_js( __( 'Save changes before changing page?', 'woocommerce' ) ),
                'i18n_variation_count_single'         => esc_js( __( '%qty% variation', 'woocommerce' ) ),
                'i18n_variation_count_plural'         => esc_js( __( '%qty% variations', 'woocommerce' ) ),
                'variations_per_page'                 => absint( apply_filters( 'woocommerce_admin_meta_boxes_variations_per_page', 15 ) )
            );

            wp_localize_script( 'wc-admin-variation-meta-boxes', 'woocommerce_admin_meta_boxes_variations', $params );


            /* Shipping Script */
            $shippingZonesLocalizeScript = array(
                'zones'         => WC_Shipping_Zones::get_zones(),
                'default_zone'  => array(
                    'zone_id'    => 0,
                    'zone_name'  => '',
                    'zone_order' => null,
                ),
                'wc_shipping_zones_nonce'  => wp_create_nonce( 'wc_shipping_zones_nonce' ),
                'strings'       => array(
                    'unload_confirmation_msg'     => __( 'Your changed data will be lost if you leave this page without saving.', 'woocommerce' ),
                    'delete_confirmation_msg'     => __( 'Are you sure you want to delete this zone? This action cannot be undone.', 'woocommerce' ),
                    'save_failed'                 => __( 'Your changes were not saved. Please retry.', 'woocommerce' ),
                    'no_shipping_methods_offered' => __( 'No shipping methods offered to this zone.', 'woocommerce' ),
                ),
            );

            wp_localize_script( 'wc-shipping-zones', 'shippingZonesLocalizeScript', $shippingZonesLocalizeScript );

            $zone_id = ! empty( $_REQUEST['zone_id'] ) ? wc_clean( $_REQUEST['zone_id'] ) : 0;

            if ( 'new' === $zone_id ) {
                $zone = new WC_Shipping_Zone();
            } else {
                $zone = WC_Shipping_Zones::get_zone( absint( $zone_id ) );
            }

            if ( ! $zone ) {
                wp_die( __( 'Zone does not exist!', 'woocommerce' ) );
            }

            // Prepare locations
            $locations = array();
            $postcodes = array();

            foreach ( $zone->get_zone_locations() as $location ) {
                if ( 'postcode' === $location->type ) {
                    $postcodes[] = $location->code;
                } else {
                    $locations[] = $location->type . ':' . $location->code;
                }
            }

           $shippingZoneMethodsLocalizeScript = array(
                'methods'                 => $zone->get_shipping_methods(),
                'zone_name'               => $zone->get_zone_name(),
                'zone_id'                 => $zone->get_id(),
                'wc_shipping_zones_nonce' => wp_create_nonce( 'wc_shipping_zones_nonce' ),
                'strings'                 => array(
                    'unload_confirmation_msg' => __( 'Your changed data will be lost if you leave this page without saving.', 'woocommerce' ),
                    'save_changes_prompt' => __( 'Do you wish to save your changes first? Your changed data will be discarded if you choose to cancel.', 'woocommerce' ),
                    'save_failed'         => __( 'Your changes were not saved. Please retry.', 'woocommerce' ),
                    'add_method_failed'   => __( 'Shipping method could not be added. Please retry.', 'woocommerce' ),
                    'yes'                 => __( 'Yes', 'woocommerce' ),
                    'no'                  => __( 'No', 'woocommerce' ),
                    'default_zone_name'   => __( 'Zone', 'woocommerce' ),
                ),
            );

            wp_localize_script( 'wc-shipping-zone-methods', 'shippingZoneMethodsLocalizeScript', $shippingZoneMethodsLocalizeScript );

            $wc_shipping = WC_Shipping::instance();
            $shippingClassesLocalizeScript = array(
                'classes'         => $wc_shipping->get_shipping_classes(),
                'default_shipping_class'  => array(
                    'term_id'     => 0,
                    'name'        => '',
                    'description' => '',
                ),
                'wc_shipping_classes_nonce' => wp_create_nonce( 'wc_shipping_classes_nonce' ),
                'strings'       => array(
                    'unload_confirmation_msg' => __( 'Your changed data will be lost if you leave this page without saving.', 'woocommerce' ),
                    'save_failed'             => __( 'Your changes were not saved. Please retry.', 'woocommerce' ),
                ),
            );

            wp_localize_script( 'wc-shipping-classes', 'shippingClassesLocalizeScript', $shippingClassesLocalizeScript );
        }

        /**
         * Add support to YITH Live Chat plugin
         * if the user are in the fronten manager page
         * the chat will be hidden
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 1.0
         * @return bool false if you are in the main page of frontendmanager page, the original value otherwise
         *
         */
        public function remove_chat_on_frontend_page( $show ){
            return $this->is_main_page() ? false : $show;
        }

	    /**
	     * Add body class to main page
	     *
	     * @param $classes array body classes
	     *
	     * @return array $classes
	     */
        public function add_body_class( $classes ){
        	if( $this->is_main_page() && ! is_user_logged_in() ){
        		$classes[] = 'woocommerce';
	        }
            return $classes;
        }
    }
}