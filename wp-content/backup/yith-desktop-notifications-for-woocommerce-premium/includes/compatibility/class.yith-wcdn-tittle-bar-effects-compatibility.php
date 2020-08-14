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

/**
 *
 *
 * @class      YITH_WCDN_Tittle_Bar_Effects_Compatibility
 * @package    Yithemes
 * @since      Version 1.0.1
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_WCDN_Tittle_Bar_Effects_Compatibility' ) ) {

    class YITH_WCDN_Tittle_Bar_Effects_Compatibility
    {

        public function __construct()
        {

            $this->_load();
        }

        private function _load()
        {

            if (class_exists('YITH_WTBE') ) {
                $yith_wtbe = YITH_WTBE::get_instance();
                if ( method_exists( $yith_wtbe, 'register_general_scripts' ) ) {
                    if('yes' == $yith_wtbe->get_option('yith-wtbe-enabled')) {
                        add_action('admin_enqueue_scripts', array($yith_wtbe, 'register_general_scripts'));
                        add_action('admin_enqueue_scripts', array($this, 'enqueue_general_scripts'));
                    }
                }
            }
        }

        public function enqueue_general_scripts(){
            wp_enqueue_script('yith_ywdpd_frontend');
        }
    }
}

return new YITH_WCDN_Tittle_Bar_Effects_Compatibility();