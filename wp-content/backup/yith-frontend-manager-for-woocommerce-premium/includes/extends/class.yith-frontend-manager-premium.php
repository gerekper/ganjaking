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

            parent::__construct();
        }
        
        /**
         * Plugin Initializzation
         *
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
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
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
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
         * @author Antonio La Rocca <antonio.larocca@yithemes.com>
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
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         * @return bool true for free, false otherwise
         *
         */
        public function is_free(){
            return false;
        }
    }
}