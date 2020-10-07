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

if ( ! class_exists( 'WC_MS_Compatibility' ) ) :

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
     * Current Compatibility: 2.1.x - 2.2
     *
     * @version 2.0
     */
    class WC_MS_Compatibility {

		/**
		 * Gets and formats a list of cart item data + variations for display on the frontend.
		 *
		 * @param array $cart_item Cart item object.
		 * @param bool  $flat Should the data be returned flat or in a list.
		 * @return string
		 */
		public static function get_item_data( $cart_item, $flat = false ) {
			if ( self::is_wc_version_gte( '3.3' ) ) {
				return wc_get_formatted_cart_item_data( $cart_item, $flat );
			}

			return WC()->cart->get_item_data( $cart_item );
		}

        /**
         * Get the WC Order instance for a given order ID or order post
         *
         * Introduced in WC 2.2 as part of the Order Factory so the 2.1 version is
         * not an exact replacement.
         *
         * If no param is passed, it will use the global post. Otherwise pass an
         * the order post ID or post object.
         *
         * @since 2.0.0
         * @param bool|int|string|\WP_Post $the_order
         * @return bool|\WC_Order
         */
        public static function wc_get_order( $the_order = false ) {

            if ( self::is_wc_version_gte_2_2() ) {

                return wc_get_order( $the_order );

            } else {

                global $post;

                if ( false === $the_order ) {

                    $order_id = $post->ID;

                } elseif ( $the_order instanceof WP_Post ) {

                    $order_id = $the_order->ID;

                } elseif ( is_numeric( $the_order ) ) {

                    $order_id = $the_order;
                }

                return new WC_Order( $order_id );
            }
        }


        /**
         * Transparently backport the `post_status` WP Query arg used by WC 2.2
         * for order statuses to the `shop_order_status` taxonomy query arg used by
         * WC 2.1
         *
         * @since 2.0.0
         * @param array $args WP_Query args
         * @return array
         */
        public static function backport_order_status_query_args( $args ) {

            if ( ! self::is_wc_version_gte_2_2() ) {

                // convert post status arg to taxonomy query compatible with WC 2.1
                if ( ! empty( $args['post_status'] ) ) {

                    $order_statuses = array();

                    foreach ( (array) $args['post_status'] as $order_status ) {

                        $order_statuses[] = str_replace( 'wc-', '', $order_status );
                    }

                    $args['post_status'] = 'publish';

                    $tax_query = array(
                        'taxonomy' => 'shop_order_status',
                        'field'    => 'slug',
                        'terms'    => $order_statuses,
                        'operator' => 'IN',
                    );

                    $args['tax_query'] = array_merge( isset( $args['tax_query'] ) ? $args['tax_query'] : array(), $tax_query );
                }
            }

            return $args;
        }


        /**
         * Get the user ID for an order
         *
         * @since 2.0.0
         * @param \WC_Order $order
         * @return int
         */
        public static function get_order_user_id( WC_Order $order ) {

            if ( self::is_wc_version_gte_2_2() ) {

                return $order->get_user_id();

            } else {

                return $order->customer_user ? $order->customer_user : 0;
            }
        }


        /**
         * Get the user for an order
         *
         * @since 2.0.0
         * @param \WC_Order $order
         * @return bool|WP_User
         */
        public static function get_order_user( WC_Order $order ) {

            if ( self::is_wc_version_gte_2_2() ) {

                return $order->get_user();

            } else {

                return self::get_order_user_id( $order ) ? get_user_by( 'id', self::get_order_user_id( $order ) ) : false;
            }
        }

        /**
         * The the Order's status
         * @param WC_Order $order
         * @return string
         */
        public static function get_order_status( WC_Order $order ) {
            if ( self::is_wc_version_gte_2_2() ) {
                return $order->get_status();
            } else {
                return $order->status;
            }
        }

        /**
         * Return an array of formatted item meta in format:
         *
         * array(
         *   $meta_key => array(
         *     'label' => $label,
         *     'value' => $value
         *   )
         * )
         *
         * e.g.
         *
         * array(
         *   'pa_size' => array(
         *     'label' => 'Size',
         *     'value' => 'Medium',
         *   )
         * )
         *
         * Backports the get_formatted() method to WC 2.1
         *
         * @since 2.0.0
         * @see WC_Order_Item_Meta::get_formatted()
         * @param \WC_Order_Item_Meta $item_meta order item meta class instance
         * @param string $hide_prefix exclude meta when key is prefixed with this, defaults to `_`
         * @return array
         */
        public static function get_formatted_item_meta( WC_Order_Item_Meta $item_meta, $hide_prefix = '_' ) {

            if ( self::is_wc_version_gte_2_2() ) {

                return $item_meta->get_formatted( $hide_prefix );

            } else {

                if ( empty( $item_meta->meta ) ) {
                    return array();
                }

                $formatted_meta = array();

                foreach ( (array) $item_meta->meta as $meta_key => $meta_values ) {

                    if ( empty( $meta_values ) || ! is_array( $meta_values ) || ( ! empty( $hide_prefix ) && substr( $meta_key, 0, 1 ) == $hide_prefix ) ) {
                        continue;
                    }

                    foreach ( $meta_values as $meta_value ) {

                        // Skip serialised meta
                        if ( is_serialized( $meta_value ) ) {
                            continue;
                        }

                        $attribute_key = urldecode( str_replace( 'attribute_', '', $meta_key ) );

                        // If this is a term slug, get the term's nice name
                        if ( taxonomy_exists( $attribute_key ) ) {
                            $term = get_term_by( 'slug', $meta_value, $attribute_key );

                            if ( ! is_wp_error( $term ) && is_object( $term ) && $term->name ) {
                                $meta_value = $term->name;
                            }

                            // If we have a product, and its not a term, try to find its non-sanitized name
                        } elseif ( $item_meta->product ) {
                            $product_attributes = $item_meta->product->get_attributes();

                            if ( isset( $product_attributes[ $attribute_key ] ) ) {
                                $meta_key = wc_attribute_label( $product_attributes[ $attribute_key ]['name'] );
                            }
                        }

                        $formatted_meta[ $meta_key ] = array(
                            'label'     => wc_attribute_label( $attribute_key ),
                            'value'     => apply_filters( 'woocommerce_order_item_display_meta_value', $meta_value ),
                        );
                    }
                }

                return $formatted_meta;
            }
        }


        /**
         * Get the full path to the log file for a given $handle
         *
         * @since 2.0.0
         * @param string $handle log handle
         * @return string
         */
        public static function wc_get_log_file_path( $handle ) {

            if ( self::is_wc_version_gte_2_2() ) {

                return wc_get_log_file_path( $handle );

            } else {

                return sprintf( '%s/plugins/woocommerce/logs/%s-%s.txt', WP_CONTENT_DIR, $handle, sanitize_file_name( wp_hash( $handle ) ) );
            }
        }

        /**
         * Get the tax rates
         *
         * @param string $tax_class
         * @param WC_Cart $cart
         * @return array
         */
        public static function get_tax_rates( $tax_class = '' ) {
            if ( self::is_wc_version_gte_2_2() ) {
                return WC_Tax::get_rates( $tax_class );
            } else {
                return WC()->cart->tax->get_rates( $tax_class );
            }
        }

        /**
         * Calculate the tax of the given $price
         * @param float $price
         * @param array $rate
         * @param bool $price_includes_tax
         * @param bool $suppress_rounding
         * @return array
         */
        public static function calc_tax( $price, $rate, $price_includes_tax = false, $suppress_rounding = false ) {
            if ( self::is_wc_version_gte_2_2() ) {
                return WC_Tax::calc_tax( $price, $rate, $price_includes_tax, $suppress_rounding );
            } else {
                return WC()->cart->tax->calc_tax( $price, $rate, $price_includes_tax, $suppress_rounding );
            }
        }

        /**
         * Calculate the total tax amount
         * @param array $taxes
         * @return float
         */
        public static function get_tax_total( $taxes ) {
            if ( self::is_wc_version_gte_2_2() ) {
                return WC_Tax::get_tax_total( $taxes );
            } else {
                return WC()->cart->tax->get_tax_total( $taxes );
            }
        }


        /**
         * Helper method to get the version of the currently installed WooCommerce
         *
         * @since 1.0.0
         * @return string woocommerce version number or null
         */
        private static function get_wc_version() {

            return defined( 'WC_VERSION' ) && WC_VERSION ? WC_VERSION : null;
        }


        /**
         * Returns true if the installed version of WooCommerce is 2.2 or greater
         *
         * @since 2.0.0
         * @return boolean true if the installed version of WooCommerce is 2.2 or greater
         */
        public static function is_wc_version_gte_2_2() {
            return self::get_wc_version() && version_compare( self::get_wc_version(), '2.2', '>=' );
        }


        /**
         * Returns true if the installed version of WooCommerce is greater than $version
         *
         * @since 1.0.0
         * @param string $version the version to compare
         * @return boolean true if the installed version of WooCommerce is > $version
         */
        public static function is_wc_version_gt( $version ) {
            return self::get_wc_version() && version_compare( self::get_wc_version(), $version, '>' );
        }

        public static function is_wc_version_gte( $version ) {
            return self::get_wc_version() && version_compare( self::get_wc_version(), $version, '>=' );
        }

        /**
         * Get order property with compatibility check on order getter introduced
         * in WC 3.0.
         *
         * @since 3.3.23
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
