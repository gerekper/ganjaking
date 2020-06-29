<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_YWQA_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_YWQA_Compatibility_Premium
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_YWQA_Compatibility_Premium' ) ) {

    class YITH_YWQA_Compatibility_Premium extends YITH_YWQA_Compatibility
    {

        protected static $instance;

        protected $_plugins = array();

        public static function get_instance()
        {
            $self = __CLASS__ . ( class_exists( __CLASS__ . '_Premium' ) ? '_Premium' : '' );

            if ( is_null( $self::$instance ) ) {
                $self::$instance = new $self;
            }

            return $self::$instance;
        }

        public function __construct()
        {
            $this->_plugins = array(
                'elementor' => 'Elementor'
            );
            $this->_load();
        }

        private function _load()
        {
            foreach ($this->_plugins as $slug => $class_slug) {
                $filename = YITH_YWQA_LIB_DIR . 'compatibility/class.yith-ywqa-' . $slug . '-compatibility.php';
                $classname = 'YITH_YWQA_' . $class_slug . '_Compatibility';

                $var = str_replace('-', '_', $slug);
                if ($this::has_plugin($slug) && file_exists($filename) && !function_exists($classname)) {
                    require_once($filename);
                }

                if (function_exists($classname)) {
                    $this->$var = $classname();
                }
            }
        }

        public static function has_plugin($slug)
        {
            switch ($slug) {

                case 'elementor':
                    return defined('ELEMENTOR_VERSION') && ELEMENTOR_VERSION;

                default:
                    return false;
            }
        }
    }
}
YITH_YWQA_Compatibility_Premium::get_instance();