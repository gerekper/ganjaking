<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * RightPress WooCommerce Helper
 *
 * @class RightPress_WC
 * @package RightPress
 * @author RightPress
 */
final class RightPress_WC
{

    /**
     * Check if WooCommerce customer meta exists
     *
     * @access public
     * @param mixed $customer
     * @param string $key
     * @return bool
     */
    public static function customer_meta_exists($customer, $key)
    {
        return self::meta_exists($customer, $key, 'customer', 'user');
    }

    /**
     * Check if WooCommerce order meta exists
     *
     * @access public
     * @param mixed $order
     * @param string $key
     * @return bool
     */
    public static function order_meta_exists($order, $key)
    {
        return self::meta_exists($order, $key, 'order', 'post');
    }

    /**
     * Check if WooCommerce order item meta exists
     *
     * @access public
     * @param mixed $order_item
     * @param string $key
     * @return bool
     */
    public static function order_item_meta_exists($order_item, $key)
    {
        return self::meta_exists($order_item, $key, 'order_item', 'order_item');
    }

    /**
     * Check if WooCommerce product meta exists
     *
     * @access public
     * @param mixed $product
     * @param string $key
     * @return bool
     */
    public static function product_meta_exists($product, $key)
    {
        return self::meta_exists($product, $key, 'product', 'post');
    }

    /**
     * Check if WooCommerce meta exists
     *
     * @access private
     * @param object|int $object
     * @param string $key
     * @param string $store
     * @param string $legacy_store
     * @return bool
     */
    private static function meta_exists($object, $key, $store, $legacy_store)
    {
        // Load object
        if (!is_object($object)) {
            $object = self::load_object($object, $store);
        }

        // Internal meta is not supported
        if (RightPress_WC::is_internal_meta($object, $key)) {
            return false;
        }

        // Check if meta exists
        return $object ? $object->meta_exists($key) : false;
    }

    /**
     * Get WooCommerce customer meta
     *
     * @access public
     * @param mixed $customer
     * @param string $key
     * @param bool $single
     * @param string $context
     * @return mixed
     */
    public static function customer_get_meta($customer, $key, $single = true, $context = 'view')
    {
        return self::get_meta($customer, $key, $single, $context, 'customer', 'user');
    }

    /**
     * Get WooCommerce order meta
     *
     * @access public
     * @param mixed $order
     * @param string $key
     * @param bool $single
     * @param string $context
     * @return mixed
     */
    public static function order_get_meta($order, $key, $single = true, $context = 'view')
    {
        return self::get_meta($order, $key, $single, $context, 'order', 'post');
    }

    /**
     * Get WooCommerce order item meta
     *
     * @access public
     * @param mixed $order_item
     * @param string $key
     * @param bool $single
     * @param string $context
     * @return mixed
     */
    public static function order_item_get_meta($order_item, $key, $single = true, $context = 'view')
    {
        return self::get_meta($order_item, $key, $single, $context, 'order_item', 'order_item');
    }

    /**
     * Get WooCommerce product meta
     *
     * @access public
     * @param mixed $product
     * @param string $key
     * @param bool $single
     * @param string $context
     * @return mixed
     */
    public static function product_get_meta($product, $key, $single = true, $context = 'view')
    {
        return self::get_meta($product, $key, $single, $context, 'product', 'post');
    }

    /**
     * Get WooCommerce meta
     *
     * @access private
     * @param object|int $object
     * @param string $key
     * @param bool $single
     * @param string $context
     * @param string $store
     * @param string $legacy_store
     * @return mixed
     */
    private static function get_meta($object, $key, $single, $context, $store, $legacy_store)
    {
        // Load object
        if (!is_object($object)) {
            $object = self::load_object($object, $store);
        }

        // Internal meta is not supported
        if (RightPress_WC::is_internal_meta($object, $key)) {
            return $single ? '' : array();
        }

        // Get meta
        return $object ? $object->get_meta($key, $single, $context) : false;
    }

    /**
     * Add WooCommerce customer meta
     * Note: If object is passed in, we assume that the calling method will handle save()
     *
     * @access public
     * @param mixed $customer
     * @param string $key
     * @param mixed $value
     * @param bool $unique
     * @return void
     */
    public static function customer_add_meta_data($customer, $key, $value, $unique = false)
    {
        self::add_meta_data($customer, $key, $value, $unique, 'customer', 'user');
    }

    /**
     * Add WooCommerce order meta
     * Note: If object is passed in, we assume that the calling method will handle save()
     *
     * @access public
     * @param mixed $order
     * @param string $key
     * @param mixed $value
     * @param bool $unique
     * @return void
     */
    public static function order_add_meta_data($order, $key, $value, $unique = false)
    {
        self::add_meta_data($order, $key, $value, $unique, 'order', 'post');
    }

    /**
     * Add WooCommerce order item meta
     * Note: If object is passed in, we assume that the calling method will handle save()
     *
     * @access public
     * @param mixed $order_item
     * @param string $key
     * @param mixed $value
     * @param bool $unique
     * @return void
     */
    public static function order_item_add_meta_data($order_item, $key, $value, $unique = false)
    {
        self::add_meta_data($order_item, $key, $value, $unique, 'order_item', 'order_item');
    }

    /**
     * Add WooCommerce product meta
     * Note: If object is passed in, we assume that the calling method will handle save()
     *
     * @access public
     * @param mixed $product
     * @param string $key
     * @param mixed $value
     * @param bool $unique
     * @return void
     */
    public static function product_add_meta_data($product, $key, $value, $unique = false)
    {
        self::add_meta_data($product, $key, $value, $unique, 'product', 'post');
    }

    /**
     * Add WooCommerce meta
     * Note: If object is passed in, we assume that the calling method will handle save()
     *
     * @access private
     * @param object|int $object
     * @param string $key
     * @param mixed $value
     * @param bool $unique
     * @param string $store
     * @param string $legacy_store
     * @return void
     */
    private static function add_meta_data($object, $key, $value, $unique, $store, $legacy_store)
    {
        $do_save = !is_object($object);

        // Load object
        if ($do_save) {
            $object = self::load_object($object, $store);
        }

        // Check object
        if ($object) {

            // Internal meta is not supported
            if (RightPress_WC::is_internal_meta($object, $key)) {
                return;
            }

            // Add meta data
            $object->add_meta_data($key, $value, $unique);

            // Save object
            if ($do_save) {
                $object->save_meta_data();
            }
        }
    }

    /**
     * Update WooCommerce customer meta
     * Note: If object is passed in, we assume that the calling method will handle save()
     *
     * @access public
     * @param mixed $customer
     * @param string $key
     * @param mixed $value
     * @param int $meta_id
     * @return void
     */
    public static function customer_update_meta_data($customer, $key, $value, $meta_id = '')
    {
        self::update_meta_data($customer, $key, $value, $meta_id, 'customer', 'user');
    }

    /**
     * Update WooCommerce order meta
     * Note: If object is passed in, we assume that the calling method will handle save()
     *
     * @access public
     * @param mixed $order
     * @param string $key
     * @param mixed $value
     * @param int $meta_id
     * @return void
     */
    public static function order_update_meta_data($order, $key, $value, $meta_id = '')
    {
        self::update_meta_data($order, $key, $value, $meta_id, 'order', 'post');
    }

    /**
     * Update WooCommerce order item meta
     * Note: If object is passed in, we assume that the calling method will handle save()
     *
     * @access public
     * @param mixed $order_item
     * @param string $key
     * @param mixed $value
     * @param int $meta_id
     * @return void
     */
    public static function order_item_update_meta_data($order_item, $key, $value, $meta_id = '')
    {
        self::update_meta_data($order_item, $key, $value, $meta_id, 'order_item', 'order_item');
    }

    /**
     * Update WooCommerce product meta
     * Note: If object is passed in, we assume that the calling method will handle save()
     *
     * @access public
     * @param mixed $product
     * @param string $key
     * @param mixed $value
     * @param int $meta_id
     * @return void
     */
    public static function product_update_meta_data($product, $key, $value, $meta_id = '')
    {
        self::update_meta_data($product, $key, $value, $meta_id, 'product', 'post');
    }

    /**
     * Update WooCommerce meta
     * Note: If object is passed in, we assume that the calling method will handle save()
     *
     * @access private
     * @param object|int $object
     * @param string $key
     * @param mixed $value
     * @param int $meta_id
     * @param string $store
     * @param string $legacy_store
     * @return void
     */
    private static function update_meta_data($object, $key, $value, $meta_id, $store, $legacy_store)
    {
        $do_save = !is_object($object);

        // Load object
        if ($do_save) {
            $object = self::load_object($object, $store);
        }

        // Check object
        if ($object) {

            // Internal meta is not supported
            if (RightPress_WC::is_internal_meta($object, $key)) {
                return;
            }

            // Update meta data
            $object->update_meta_data($key, $value, $meta_id);

            // Save object
            if ($do_save) {
                $object->save_meta_data();
            }
        }
    }

    /**
     * Delete WooCommerce customer meta
     * Note: If object is passed in, we assume that the calling method will handle save()
     *
     * @access public
     * @param mixed $customer
     * @param string $key
     * @return void
     */
    public static function customer_delete_meta_data($customer, $key)
    {
        self::delete_meta_data($customer, $key, 'customer', 'user');
    }

    /**
     * Delete WooCommerce order meta
     * Note: If object is passed in, we assume that the calling method will handle save()
     *
     * @access public
     * @param mixed $order
     * @param string $key
     * @return void
     */
    public static function order_delete_meta_data($order, $key)
    {
        self::delete_meta_data($order, $key, 'order', 'post');
    }

    /**
     * Delete WooCommerce order item meta
     * Note: If object is passed in, we assume that the calling method will handle save()
     *
     * @access public
     * @param mixed $order_item
     * @param string $key
     * @return void
     */
    public static function order_item_delete_meta_data($order_item, $key)
    {
        self::delete_meta_data($order_item, $key, 'order_item', 'order_item');
    }

    /**
     * Delete WooCommerce product meta
     * Note: If object is passed in, we assume that the calling method will handle save()
     *
     * @access public
     * @param mixed $product
     * @param string $key
     * @return void
     */
    public static function product_delete_meta_data($product, $key)
    {
        self::delete_meta_data($product, $key, 'product', 'post');
    }

    /**
     * Delete WooCommerce meta
     * Note: If object is passed in, we assume that the calling method will handle save()
     *
     * @access private
     * @param object|int $object
     * @param string $key
     * @param string $store
     * @param string $legacy_store
     * @return void
     */
    private static function delete_meta_data($object, $key, $store, $legacy_store)
    {
        $do_save = !is_object($object);

        // Load object
        if ($do_save) {
            $object = self::load_object($object, $store);
        }

        // Check object
        if ($object) {

            // Internal meta is not supported
            if (RightPress_WC::is_internal_meta($object, $key)) {
                return;
            }

            // Delete meta data
            $object->delete_meta_data($key);

            // Save object
            if ($do_save) {
                $object->save_meta_data();
            }
        }
    }

    /**
     * Load object
     *
     * @access private
     * @param int $object_id
     * @param string $store
     * @return object|false
     */
    private static function load_object($object_id, $store)
    {
        $method = 'wc_get_' . $store;
        return RightPress_Help::$method($object_id);
    }

    /**
     * Normalize meta array
     *
     * Turns WC 3.0 style meta data (containing objects) to regular WP post meta format
     * Unwraps meta in all WC versions
     *
     * @access public
     * @param array $meta_data
     * @return array
     */
    public static function normalize_meta_data($meta_data)
    {
        $normalized = array();

        foreach ($meta_data as $meta) {
            $normalized[$meta->key][] = $meta->value;
        }

        return RightPress_Help::unwrap_post_meta($normalized);
    }

    /**
     * Check if meta is internal
     *
     * @access public
     * @param object $object
     * @param string $key
     * @param bool $suppress_warning
     * @return bool
     */
    public static function is_internal_meta($object, $key, $suppress_warning = false)
    {
        // Get data store
        if (is_callable(array($object, 'get_data_store'))) {
            if ($data_store = $object->get_data_store()) {

                // Get internal meta keys
                if (is_callable(array($data_store, 'get_internal_meta_keys'))) {
                    if ($internal_meta_keys = $data_store->get_internal_meta_keys()) {

                        // Key is internal meta key
                        if (in_array($key, $internal_meta_keys, true)) {

                            // Maybe add warning
                            if (!$suppress_warning) {
                                error_log('RightPress_WC methods must not be used to interact with WooCommerce internal meta (used key "' . $key . '").');
                            }

                            return true;
                        }
                        // Key is regular meta key
                        else {
                            return false;
                        }
                    }
                }
            }
        }

        return false;
    }




}
