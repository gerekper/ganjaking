<?php
/**
 * WooCommerce Plugin Compatibility
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the plugin to newer
 * versions in the future. If you wish to customize the plugin for your
 * needs please refer to http://www.skyverge.com
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2014, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Warranty_Compatibility' ) ) :

    /**
     * WooCommerce Compatibility Utility Class
     *
     * The unfortunate purpose of this class is to provide a single point of
     * compatibility functions for dealing with supporting multiple versions
     * of WooCommerce.
     *
     * The recommended procedure is to rename this file/class, replacing "my plugin"
     * with the particular plugin name, so as to avoid clashes between plugins.
     * Over time we expect to remove methods from this class, using the current
     * ones directly, as support for older versions of WooCommerce is dropped.
     *
     * Current Compatibility: 2.5 - 3.0
     *
     * @version 2.0
     */
    class WC_Warranty_Compatibility {
        /**
         * Get order property with compatibility check on order getter introduced
         * in WC 3.0.
         *
         * @since 1.8.6
         *
         * @param WC_Order $order Order object.
         * @param string   $prop  Property name.
         *
         * @return mixed Property value
         */
        public static function get_order_prop( $order, $prop ) {
            $modifier = function ( $a ) {
                return $a;
            };

            switch ( $prop ) {
                case 'modified_date':
                    $getter = array( $order, 'get_date_modified' );

                    $modifier = function ( $date ) {
                        return $date ? $date->date( 'Y-m-d H:i:s' ) : '';
                    };

                    break;
                case 'order_date':
                    $getter = array( $order, 'get_date_created' );

                    $modifier = function ( $date ) {
                        return $date ? $date->date( 'Y-m-d H:i:s' ) : '';
                    };

                    break;
                case 'customer_user':
                    $getter = array( $order, 'get_customer_id' );
                    break;
                default:
                    $getter = array( $order, 'get_' . $prop );
                    break;
            }

            return is_callable( $getter ) ? $modifier( call_user_func( $getter ) ) : $order->{ $prop };
        }

    }

endif; // Class exists check
