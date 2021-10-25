<?php

/**
 * initialize the plugin.
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('FP_WooCommerce_Log')) {

    /**
     * FP_WooCommerce_Log Class.
     */
    class FP_WooCommerce_Log {

        static $log = false;

        /**
         * Save Error Log On WooCommerce Log
         */
        public static function log($message, $source = 'fp-upgrade', $level = 'info', $context = array()) {
            if (empty(self::$log)) {
                self::$log = new WC_Logger();
            }
            if (empty($context)) {
                $context = array('source' => $source, '_legacy' => true);
            }

            $implements = class_implements('WC_Logger');

            if (is_array($implements) && in_array('WC_Logger_Interface', $implements)) {
                self::$log->log($level, $message, $context);
            } else {
                self::$log->add($source, $message);
            }
        }

    }

}