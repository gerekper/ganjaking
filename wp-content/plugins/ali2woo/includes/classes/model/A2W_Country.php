<?php

/**
 * Description of A2W_Country
 *
 * @author Andrey
 */
if (!class_exists('A2W_Country')) {

    class A2W_Country
    {
        private static $countries = array();
        private static $states = array();
        private static $ali_states = array();

        public static function get_countries()
        {
            if (empty(self::$countries)) {
                unload_textdomain('woocommerce');
                self::$countries = apply_filters('woocommerce_countries', include WC()->plugin_path() . '/i18n/countries.php');
                if (apply_filters('woocommerce_sort_countries', true) && function_exists('wc_asort_by_locale')) {
                    wc_asort_by_locale(self::$countries);
                }
                $locale = determine_locale();
                $locale = apply_filters('plugin_locale', $locale, 'woocommerce');
                load_textdomain('woocommerce', WP_LANG_DIR . '/woocommerce/woocommerce-' . $locale . '.mo');
                load_plugin_textdomain('woocommerce', false, plugin_basename(dirname(WC_PLUGIN_FILE)) . '/i18n/languages');
            }

            return self::$countries;
        }

        public static function get_country($code)
        {
            $countries = self::get_countries();
            foreach ($countries as $c => $n) {
                if ($c === $code) {
                    return $n;
                    break;
                }
            }
            return '';
        }

        public static function get_states($cc)
        {
            if (empty(self::$states)) {
                unload_textdomain('woocommerce');
                self::$states = apply_filters('woocommerce_states', include WC()->plugin_path() . '/i18n/states.php');
                $locale = determine_locale();
                $locale = apply_filters('plugin_locale', $locale, 'woocommerce');
                load_textdomain('woocommerce', WP_LANG_DIR . '/woocommerce/woocommerce-' . $locale . '.mo');
                load_plugin_textdomain('woocommerce', false, plugin_basename(dirname(WC_PLUGIN_FILE)) . '/i18n/languages');
            }

            if (!is_null($cc)) {
                return isset(self::$states[$cc]) ? self::$states[$cc] : false;
            } else {
                return self::$states;
            }
        }

        public static function get_ali_states($cc)
        {
            if (empty(self::$ali_states)) {
                ini_set('memory_limit', -1);
                self::$ali_states = file_get_contents(A2W()->plugin_path() . '/assets/data/aliexpress_states.json');
                self::$ali_states = a2w_json_decode(self::$ali_states);
                // error_log(print_r(self::$ali_states['UK'], true));
            }

            return isset(self::$ali_states[$cc]) ? self::$ali_states[$cc] : array();
        }

        public static function is_support_other_city($cc)
        {
            return in_array($cc, array('BR', 'CL', 'FR', 'IN', 'ID', 'IT', 'KZ', 'KR', 'NL', 'NZ', 'PL', 'RU', 'SA', 'ES', 'TR', 'UA', 'UK', 'US'));
        }

    }

}
