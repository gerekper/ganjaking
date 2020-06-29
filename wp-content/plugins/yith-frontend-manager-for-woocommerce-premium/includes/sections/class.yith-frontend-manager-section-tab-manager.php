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

if( ! class_exists( 'YITH_Frontend_Manager_Section_Tab_Manager' ) && defined( 'YWTM_PREMIUM' ) ) {

    class YITH_Frontend_Manager_Section_Tab_Manager extends YITH_WCFM_Section {

        public $WC_Admin_Post_Types;
        protected  $tab_manager_metaboxes ;

        /**
         * Constructor method
         *
         * @return \YITH_Frontend_Manager_Section
         * @since 1.0.0
         */
        public function __construct() {
            $this->id                   = 'tab_manager';
            $this->_default_section_name = _x( 'Tab Manager', '[Frontend]: Dashboard menu item', 'yith-frontend-manager-for-woocommerce' );

            $this->_subsections =  array(
                'tabs' => array(
                    'slug' => $this->get_option( 'slug', $this->id . '_list' , 'tab_manager' ),
                    'name' => __( 'All tabs', 'yith-frontend-manager-for-woocommerce' ),
                    'add_delete_script' => true
                ),

                'tab' => array(
                    'slug' => $this->get_option( 'slug', $this->id . '_tab', 'add_tab' ),
                    'name' => __( 'Add Tab', 'yith-frontend-manager-for-woocommerce' ),
                ),
            );

            $this->deps();

            add_action( 'yith_wcfm_tabs_show_metaboxes', array( $this, 'show_tab_metaboxes' ), 10, 1 );
           // add_action( 'init', array( $this, 'init_metaboxes'), 5 );

            parent::__construct();
        }

        /* === SECTION METHODS === */

        /**
         * Required files for this section
         *
         * @author Andrea Grillo    <andrea.grillo@yithemes.com>
         * @return void
         * @since 1.0.0
         */
        public function deps(){


            if( ! class_exists( 'WP_Posts_List_Table' ) ){
                require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
            }

            require_once( YITH_WCFM_LIB_PATH . 'class.yith-frontend-manager-tabs-list-table.php' );
            require_once( ABSPATH . 'wp-admin/includes/meta-boxes.php' );


        }

        /**
         * Section styles and scripts
         *
         * Override this method in section class to enqueue
         * particular styles and scripts only in correct
         * section
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @return void
         * @since  1.0.0
         */
        public function enqueue_section_scripts() {
            /* === Styles === */


	        YITH_Tab_Manager_Admin()->enqueue_admin_style();
	        YITH_Tab_Manager_Admin()->admin_premium_scripts();
	        YITH_Tab_Manager_Admin()->admin_premium_styles();

	        wp_enqueue_script( 'ywtm_admin_post_type' );
	        wp_enqueue_style( 'font-retina' );
	        wp_enqueue_style( 'yit-tab-style' );

	        do_action( 'yith_wcfm_tabs_enqueue_scripts' );


	        YIT_Assets::instance()->register_styles_and_scripts();
	        wp_enqueue_script( 'yith-plugin-fw-fields');
	        wp_enqueue_script( 'yit-metabox' );
	        wp_enqueue_script( 'yit-plugin-panel' );
	        wp_enqueue_script( 'codemirror' );
	        wp_enqueue_script( 'codemirror-javascript' );
	        wp_enqueue_script( 'colorbox' );
	        wp_enqueue_script( 'yith_how_to' );

	        wp_enqueue_style( 'codemirror' );
	        wp_enqueue_style( 'yit-plugin-style');
	        wp_enqueue_style( 'raleway-font');
	        wp_enqueue_style( 'yit-jquery-ui-style');
	        wp_enqueue_style( 'colorbox');
	        wp_enqueue_style( 'yit-upgrade-to-pro');
	        wp_enqueue_style( 'yit-plugin-metaboxes');
	        wp_enqueue_style( 'yith-plugin-fw-fields');

	        wp_enqueue_style( 'yith-frontend-manager-tabmanager-style', YITH_WCFM_ASSETS_URL.'css/tabs.css' );
        }

            /**
         * Print shortcode function
         *
         * @author Andrea Grillo    <andrea.grillo@yithemes.com>
         * @return void
         * @since 1.0.0
         */
        public function print_shortcode( $atts = array(), $content = '', $tag ) {
            $section = $subsection = '';
            if ( ! empty( $atts) ) {
                $section = ! empty( $atts['section'] ) ? $atts['section'] : $this->id;
                $subsection = $this->id;
                if( ! empty( $atts['subsection'] ) && 'tabs' != $atts['subsection'] && ! in_array( $atts['subsection'], $this->_subsections ) ){
                    $subsection = $atts['subsection'];
                }
            }

            $atts = array(
                'section_obj'    => $this,
                //'product_status' => YITH_Frontend_Manager_Section_Products::get_product_status(),
                'section'        => $section,
                'subsection'     => $subsection
            );

            if( apply_filters( 'yith_wcfm_print_tab_section', true, $subsection, $section, $atts ) ){
                $this->print_section( $subsection, $section, $atts );
            }

            else {
                do_action( 'yith_wcfm_print_section_unauthorized', $this->id );
            }
        }

        /**
         * get the edit tab link for frontend
         *
         * @author Salvatore Strano
         * @return string post link
         * @since 1.0.0
         */
        public static function get_edit_tab_link( $tab_id ){
            return add_query_arg( array( 'tab_id' => $tab_id, ), yith_wcfm_get_section_url( 'current', 'tab' ) );
        }



        /**
         * Delete tab post type
         *
         * @param int $tab_id
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @since 1.0.0
         */
        public static function delete( $tab_id ){

           $result = wp_delete_post( $tab_id, true );

	        if( $result ){
		        $message = _x( 'Tab deleted successfully', '[Frontend]: user message', 'yith-frontend-manager-for-woocommerce' );
		        $type    = 'success';
	        }

	        else {
		        $message = _x( 'Tab does not exist', '[Frontend]: user message', 'yith-frontend-manager-for-woocommerce' );
		        $type = 'error';
	        }

	        wc_add_notice( $message, $type );
        }

	    /**
	     * save the tab post meta and show the metaboxes
	     * @author Salvatore Strano<salvostrano@msn.com>
	     *
	     */
        public function show_tab_metaboxes(){

        	if( function_exists( 'YITH_Tab_Manager_Admin' ) ){


		        $tab_manager = YITH_Tab_Manager_Admin();
        		$tab_manager->add_tab_metabox();
        		$tab_manager->add_layout_tab_metabox();

        		$tab_metabox = YIT_Metabox('yit-tab-manager-setting');

		        $tab_metabox->enqueue();

		        if( isset( $_POST['tab_id'] ) && isset( $_POST['act'] ) && ( isset( $_POST['post_type'] )  && 'ywtm_tab' == $_POST['post_type'] ) ){
		        	$tab_id = $_POST['tab_id'];
		        	$tab_metabox->save_postdata( $tab_id );

		        }
		        $tab_metabox->show();

	        }
        }
    }
}