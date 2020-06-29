<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCGPF_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_WCGPF_Compatibility
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_WCGPF_Compatibility' ) ) {

    class YITH_WCGPF_Compatibility
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
                'brands' => 'Brands',
                'wpml'   => 'WPML',
                'eu-energy-label' => 'EU_Energy_Label'
            );
            $this->_load();
        }

        private function _load()
        {
            foreach ($this->_plugins as $slug => $class_slug) {
                $filename = YITH_WCGPF_PATH . 'includes/compatibility/class.yith-wcgpf-' . $slug . '-compatibility.php';
                $classname = 'YITH_WCGPF_' . $class_slug . '_Compatibility';
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
                case 'brands':
                    return defined('YITH_WCBR_PREMIUM_INIT') && YITH_WCBR_PREMIUM_INIT && defined('YITH_WCBR_VERSION') && version_compare(YITH_WCBR_VERSION, apply_filters('yith_wcgpf_brands_min_version', '1.0.9'), '>');
                    break;
                case 'wpml':
                    global $sitepress;
                    return !empty( $sitepress );
                    break;
                case 'eu-energy-label':
                    return defined('YITH_WCEUE_PREMIUM') && YITH_WCEUE_PREMIUM && defined('YITH_WCEUE_VERSION') && version_compare(YITH_WCEUE_VERSION, apply_filters('yith_wcgpf_eu_energy_label_min_version', '1.1.2'), '>');
                    break;
                default:
                    return false;
            }
        }
    }
}