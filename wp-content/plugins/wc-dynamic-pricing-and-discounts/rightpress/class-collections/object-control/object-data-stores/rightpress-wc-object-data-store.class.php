<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-object-data-store.class.php';

/**
 * WooCommerce Object Wrapper Data Store
 *
 * @class RightPress_WC_Object_Data_Store
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WC_Object_Data_Store extends RightPress_Object_Data_Store
{

    /**
     * Create object data in the database
     *
     * Note: This method does nothing since our WooCommerce object wrappers always piggyback on WooCommerce objects
     *
     * @access public
     * @param object $object
     * @param array $args
     * @return void
     */
    public function create(&$object, $args = array()) {}

    /**
     * Read object data from the database
     *
     * @access public
     * @param object $object
     * @param array $args
     * @return void
     */
    public function read(&$object, $args = array())
    {

        // Reference WooCommerce object
        $wc_object = $object->get_wc_object();

        // Set properties
        foreach ($object->get_data_keys() as $key) {

            // Prefix key
            $prefixed_key = $this->prefix_key($key, $object);

            // Check if meta exists
            if ($wc_object->meta_exists($prefixed_key)) {

                // Get meta value
                $value = $wc_object->get_meta($prefixed_key, true, 'edit');

                // Set property
                $method = 'set_' . $key;
                $object->{$method}($value);
            }
        }

        // Object data is ready
        $object->set_data_ready(true);
    }

    /**
     * Update object data in the database
     *
     * @access public
     * @param object $object
     * @param array $args
     * @return void
     */
    public function update(&$object, $args = array())
    {

        // Reference WooCommerce object
        $wc_object = $object->get_wc_object();

        // Reset plugin version
        $object->reset_plugin_version();

        // Get changes
        $changes = $object->get_changes();

        // Get data for database
        if ($data = $this->get_data_for_database($object, $changes)) {

            // TODO: what do we do with null values?

            // Iterate over data entries
            foreach ($data as $key => $value) {

                // Prefix key
                $prefixed_key = $this->prefix_key($key, $object);

                // Update value in WooCommerce object meta
                $wc_object->update_meta_data($prefixed_key, $value);
            }
        }

        // Save meta data
        $object->save_meta_data();

        // Apply changes
        $object->apply_changes();
    }

    /**
     * Delete object data from the database
     *
     * Note: this will delete corresponding WooCommerce objects
     *
     * @access public
     * @param object $object
     * @param array $args
     * @return void
     */
    public function delete(&$object, $args = array())
    {

        // Import arguments
        extract(RightPress_Help::filter_by_keys_with_defaults($args, array('permanently' => false)));

        // Delete WooCommerce object
        $object->get_wc_object()->delete($permanently);
    }

    /**
     * Clear object data from the database
     *
     * Note: This is intended to clear all own data from WooCommerce objects without deleting those objects
     *
     * @access public
     * @param object $object
     * @param array $args
     * @return void
     */
    public function clear(&$object, $args = array())
    {

        // Reference WooCommerce object
        $wc_object = $object->get_wc_object();

        // Iterate over all data keys
        foreach ($object->get_data_keys() as $key) {

            // Prefix key
            $prefixed_key = $this->prefix_key($key, $object);

            // Delete from WooCommerce object meta
            $wc_object->delete_meta_data($prefixed_key);
        }
    }

    /**
     * Add object meta data to the database
     *
     * @access public
     * @param object $object
     * @param object $meta
     * @param array $args
     * @return void
     */
    public function add_meta(&$object, $meta, $args = array())
    {

// TODO: Test this method

        // Prefix meta key
        $prefixed_key = $this->prefix_meta_key($meta->key, $object);

        // Add meta
        $object->get_wc_object()->add_meta_data($prefixed_key, $meta->value);
    }

    /**
     * Read object meta data from the database
     *
     * @access public
     * @param object $object
     * @param array $args
     * @return mixed
     */
    public function read_meta(&$object, $args = array())
    {

        $sanitized = array();

        // Get WooCommerce object meta data
        $meta_data = $object->get_wc_object()->get_meta_data();

        // Iterate over WooCommerce object meta data
        foreach ($meta_data as $meta) {

            // Check if entry is true meta
            if ($this->is_true_meta_key($meta->key, $object)) {

                // Sanitize meta value
                $sanitized[] = new RightPress_Meta(array(
                    'id'    => $meta->id,
                    'key'   => $this->unprefix_meta_key($meta->key, $object),
                    'value' => $meta->value,
                ));
            }
        }

        return $sanitized;
    }

    /**
     * Update object meta data in the database
     *
     * @access public
     * @param object $object
     * @param object $meta
     * @param array $args
     * @return void
     */
    public function update_meta(&$object, $meta, $args = array())
    {

// TODO: Test this method

        // Prefix meta key
        $prefixed_key = $this->prefix_meta_key($meta->key, $object);

        // Add meta
        $object->get_wc_object()->update_meta_data($prefixed_key, $meta->value, $meta->id);
    }

    /**
     * Delete object meta data from the database
     *
     * @access public
     * @param object $object
     * @param object $meta
     * @param array $args
     * @return void
     */
    public function delete_meta(&$object, $meta, $args = array())
    {

// TODO: Test this method

        // Prefix meta key
        $prefixed_key = $this->prefix_meta_key($meta->key, $object);

        // Delete meta
        $object->get_wc_object()->delete_meta_data($prefixed_key);
    }

    /**
     * Prefix key for storage as WooCommerce object meta
     *
     * @access public
     * @param string $key
     * @param object $object
     * @return string
     */
    public function prefix_key($key, &$object)
    {

        return $this->get_key_prefix($object) . $key;
    }

    /**
     * Get key prefix
     *
     * @access protected
     * @param object $object
     * @return string
     */
    protected function get_key_prefix($object = null)
    {

        return $object->get_controller()->get_database_key_prefix();
    }

    /**
     * Get meta key prefix
     *
     * @access protected
     * @param object $object
     * @return string
     */
    protected function get_meta_key_prefix($object = null)
    {

        return $this->get_key_prefix($object) . 'meta:';
    }





}
