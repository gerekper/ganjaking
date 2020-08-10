<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-wp-object-data-store.class.php';
require_once 'interfaces/rightpress-wp-custom-object-data-store-interface.php';

/**
 * WordPress Custom Object Data Store
 *
 * @class RightPress_WP_Custom_Object_Data_Store
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WP_Custom_Object_Data_Store extends RightPress_WP_Object_Data_Store implements RightPress_WP_Custom_Object_Data_Store_Interface
{

    /**
     * Get meta type
     *
     * @access public
     * @param object $object
     * @return string
     */
    public function get_meta_type(&$object)
    {
        return $object->get_controller()->get_object_key();
    }

    /**
     * Get object id field name
     *
     * @access public
     * @param object $object
     * @return string
     */
    public function get_object_id_field_name(&$object)
    {
        return $this->get_meta_type($object) . '_id';
    }

    /**
     * Create object data in the database
     *
     * @access public
     * @param object $object
     * @param array $args
     * @return void
     */
    public function create(&$object, $args = array())
    {
        global $wpdb;

        // Reset plugin version
        $object->reset_plugin_version();

        // Set created datetime
        $object->set_created(time());

        // Run status getter so that default status is set if not set manually
        $object->get_status();

        // Get changes
        $changes = $object->get_changes();

        // Get data for database
        $data = $this->get_data_for_database($object, $changes);

        // Create database entry with all data
        if ($result = $wpdb->insert($this->get_table_name($object), $data)) {

            // Set object id
            $object->set_id($wpdb->insert_id);

            // Save meta data
            $object->save_meta_data();

            // Apply changes
            $object->apply_changes();
        }
    }

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
        global $wpdb;

        // Get database properties
        $table_name = $this->get_table_name($object);
        $object_id_field_name = $this->get_object_id_field_name($object);

        // Prepare query
        $query = $wpdb->prepare("SELECT * FROM {$table_name} WHERE {$object_id_field_name} = %d LIMIT 1;", $object->get_id());

        // Get data from database
        $data = $wpdb->get_row($query);

        // Unable to load data
        if (!$data) {
            throw new RightPress_Exception($object->get_controller()->prefix_error_code('data_read_failed'), ('RightPress: Data for object ' . get_class($object) . ' #' . $object->get_id() . ' could not be read from database.'));
        }

        // Set properties
        foreach ($object->get_data_keys() as $key) {

            // Get setter method name
            $method = 'set_' . $key;

            // Check if value is set and setter method exists
            if (isset($data->$key) && method_exists($object, $method)) {
                $object->{$method}($data->$key);
            }
        }

        // Read meta data
        $object->read_meta_data();

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
        global $wpdb;

        // Reset plugin version
        $object->reset_plugin_version();

        // Get changes
        $changes = $object->get_changes();

        // Get data for database
        if ($data = $this->get_data_for_database($object, $changes)) {

            // Get database properties
            $table_name = $this->get_table_name($object);
            $object_id_field_name = $this->get_object_id_field_name($object);

            // Update data in database
            $wpdb->update($table_name, $data, array(
                $object_id_field_name => $object->get_id(),
            ));

            // Apply changes
            $object->apply_changes();
        }

        // Save meta data
        $object->save_meta_data();
    }

    /**
     * Delete object data from the database
     *
     * @access public
     * @param object $object
     * @param array $args
     * @return void
     */
    public function delete(&$object, $args = array())
    {
        global $wpdb;

        // Get object id
        if ($id = $object->get_id()) {

            // Get database properties
            $table_name = $this->get_table_name($object);
            $object_id_field_name = $this->get_object_id_field_name($object);

            // Delete entry
            $wpdb->delete($table_name, array($object_id_field_name => $id));

            // Delete meta data
            if ($object->get_controller()->supports_metadata()) {
                $meta_table_name = $this->get_meta_table_name($object);
                $wpdb->delete($meta_table_name, array($object_id_field_name => $id));
            }

            // Run action
            do_action($object->get_controller()->prefix_public_hook('deleted'), $object, $id);
        }
    }




}
