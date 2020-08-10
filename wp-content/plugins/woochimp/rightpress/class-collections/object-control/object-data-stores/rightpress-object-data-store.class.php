<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'interfaces/rightpress-object-data-store-interface.php';

/**
 * RightPress_Object_Data_Store
 *  > RightPress_WP_Object_Data_Store
 *     > RightPress_WP_Custom_Object_Data_Store
 *     > RightPress_WP_Custom_Post_Object_Data_Store
 *        > RightPress_WP_Log_Entry_Data_Store
 *  > RightPress_WC_Object_Data_Store
 *     > RightPress_WC_Product_Object_Data_Store
 *     > RightPress_WC_Custom_Order_Object_Data_Store
 * RightPress_WC_Custom_Order_Data_Store
 */

/**
 * Object Data Store
 *
 * @class RightPress_Object_Data_Store
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Object_Data_Store implements RightPress_Object_Data_Store_Interface
{

    // TODO: Null/empty/default values are stored in post meta and possibly in other locations, need to filter out those values, unless there are existing values to override (in which case we should delete records instead)

    /**
     * Get data for database
     *
     * @access protected
     * @param object $object
     * @param array $changes
     * @return array
     */
    public function get_data_for_database(&$object, $changes)
    {

        $data = array();

        // Iterate over properties
        foreach ($object->get_data_keys() as $key) {

            // Update property only if it has changed
            if (array_key_exists($key, $changes)) {

                // Get value
                $getter = 'get_' . $key;
                $value = $object->{$getter}('store');

                // Add to data array
                $data[$key] = $value;
            }
        }

        // Set updated property
        if (!empty($data)) {
            $object->set_updated(time());
            $data['updated'] = $object->get_updated('store');
        }

        return $data;
    }

    /**
     * Prefix meta key
     *
     * All custom meta is prefixed with _x_ to ensure it does not clash with
     * regular object properties that can be saved as WordPress post meta
     *
     * @access protected
     * @param string $key
     * @param object $object
     * @return string
     */
    protected function prefix_meta_key($key, $object = null)
    {

        return $this->get_meta_key_prefix($object) . $key;
    }

    /**
     * Remove prefix from meta key
     *
     * @access protected
     * @param string $key
     * @param object $object
     * @return string
     */
    protected function unprefix_meta_key($key, $object = null)
    {

        if ($this->is_true_meta_key($key, $object)) {
            $key = substr($key, strlen($this->get_meta_key_prefix($object)));
        }

        return $key;
    }

    /**
     * Check if key is true meta key
     *
     * @access protected
     * @param string $key
     * @param object $object
     * @return bool
     */
    protected function is_true_meta_key($key, $object = null)
    {

        return RightPress_Help::string_begins_with_substring($key, $this->get_meta_key_prefix($object));
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

        return 'meta:';
    }





}
