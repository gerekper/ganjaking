<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCDN_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_WCDN_Compatibility
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_WCDN_Compatibility' ) ) {

    class YITH_WCDN_Compatibility
    {

        protected static $instance;

        protected $_plugins = array();

        public static function get_instance()
        {
            if (is_null(self::$instance)) {
                self::$instance = new self;
            }

            return self::$instance;
        }

        public function __construct()
        {
            $this->_plugins = array(
                'multivendor'           => 'Multivendor',
                'request-a-quote'       => 'RequestaQuote',
                'tittle-bar-effects'    => 'Tittle_Bar_Effects',
                'bookings'              => 'Bookings',
            );
            $this->_load();
        }

        private function _load()
        {
            foreach ($this->_plugins as $slug => $class_slug) {
                $filename = YITH_WCDN_PATH . 'includes/compatibility/class.yith-wcdn-' . $slug . '-compatibility.php';
                $classname = 'YITH_WCDN_' . $class_slug . '_Compatibility';


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
                case 'multivendor':
                    return defined('YITH_WPV_PREMIUM') && YITH_WPV_PREMIUM && defined('YITH_WPV_VERSION') && version_compare(YITH_WPV_VERSION, apply_filters('yith_wcdn_multivendor_min_version', '1.5.0'), '>');
                    break;
                case 'request-a-quote':
                    return defined('YITH_YWRAQ_PREMIUM') && YITH_YWRAQ_PREMIUM && defined('YITH_YWRAQ_VERSION') && version_compare(YITH_YWRAQ_VERSION, apply_filters('yith_wcdn_request_a_quote_min_version', '1.5.0'), '>');
                    break;
                case 'tittle-bar-effects':
                    return defined('YITH_WTBE_PREMIUM') && YITH_WTBE_PREMIUM && defined('YITH_WTBE_VERSION') && version_compare(YITH_WTBE_VERSION, apply_filters('yith_wcdn_tittle_bar_effects_min_version', '0.1.0'), '>');
                    break;
                case 'bookings':
                    return defined('YITH_WCBK_PREMIUM') && YITH_WCBK_PREMIUM && defined('YITH_WCBK_VERSION') && version_compare(YITH_WCBK_VERSION, apply_filters('yith_wcdn_bookings_min_version', '2.1.2'), '>');
                    break;
                default:
                    return false;
            }
        }
    }
}