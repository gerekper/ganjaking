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

if( ! class_exists( 'YITH_Frontend_Manager_Premium' ) ){

    class YITH_Frontend_Manager_Premium extends YITH_Frontend_Manager{

        /**
         * Construct
         */
        public function __construct() {
            add_filter( 'yith_wcfm_required_classes', array( $this, 'premium_required_classes' ) );

	        /* Register plugin to licence/update system */
	        add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
	        add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

            parent::__construct();
        }

        /**
         * Plugin Initializzation
         *
         * @author YITH <plugins@yithemes.com>
         * @since  1.0
         * @return void
         */
        public function init(){
            $this->_install_sections();

            if ( $this->is_admin ) {
                $this->backend = new YITH_Frontend_Manager_Admin_Premium();
            }

            else {
                $this->gui = new YITH_Frontend_Manager_GUI_Premium();
            }
        }


        /**
         * Premium class files
         *
         * @since  1.0
         *
         * @param $classes
         *
         * @return mixed
         */
        public function premium_required_classes( $classes ){
            /* GUI Premium Classes */
            $classes['gui'][] = YITH_WCFM_PREMIUM_CLASS_PATH . 'class.yith-frontend-manager-gui-premium.php';

            /* Backend Premium Classes */
            $classes['backend'][] = YITH_WCFM_PREMIUM_CLASS_PATH . 'class.yith-frontend-manager-admin-premium.php';

            /* Frontend and Backend classes */
            $classes['common'][] = YITH_WCFM_PREMIUM_CLASS_PATH . 'class.yith-frontend-manager-section-premium.php';
            return $classes;
        }

        /**
         * Install sections
         *
         * @return void
         * @since 1.0.0
         */
        protected function _install_sections(){
            $sections = array_keys( YITH_Frontend_Manager()->available_sections );

            if( $sections ){
                foreach( $sections as $section ){
                    /**
                     * Skip to create new object from base class
                     * if a premium class exists
                     */
                    $class_name = strtolower( $section );
                    $is_premium = strpos( '_premium', $class_name );
                    if( ! $is_premium && class_exists( $section . '_Premium' ) ){
                        continue;
                    }

                    if( class_exists( $section ) ){
                        $section_obj = new $section();
                        $this->_sections[ $section_obj->get_id() ] = $section_obj;
                    }
                }
            }
        }

        /**
         * check if this is free or premium version of YITH WCFM
         *
         * @since 1.0
         * @return bool true for free, false otherwise
         *
         */
        public function is_free(){
            return false;
        }

	    /**
	     * Register plugins for activation tab
	     *
	     * @return void
	     * @since    2.0.0
	     */
	    public function register_plugin_for_activation() {
		    if( ! class_exists( 'YIT_Plugin_Licence' ) ){
			    require_once YITH_WCFM_PATH . 'plugin-fw/lib/yit-plugin-licence.php';
		    }
		    YIT_Plugin_Licence()->register( YITH_WCFM_INIT, YITH_WCFM_SECRET_KEY, YITH_WCFM_SLUG );
	    }

	    /**
	     * Register plugins for update tab
	     *
	     * @return void
	     * @since    2.0.0
	     */
	    public function register_plugin_for_updates() {
		    if( class_exists( 'YIT_Upgrade' ) ){
			    require_once YITH_WCFM_PATH . 'plugin-fw/lib/yit-upgrade.php';
		    }
		    YIT_Upgrade()->register( YITH_WCFM_SLUG, YITH_WCFM_INIT );
	    }
    }
}
