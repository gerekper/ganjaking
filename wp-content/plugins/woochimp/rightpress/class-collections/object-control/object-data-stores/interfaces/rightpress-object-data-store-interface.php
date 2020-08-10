<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * Object Data Store Interface
 *
 * @package RightPress
 * @author RightPress
 */
interface RightPress_Object_Data_Store_Interface
{

    /**
     * Create object data in the database
     *
     * @access public
     * @param object $object
     * @param array $args
     * @return void
     */
    public function create(&$object, $args = array());

    /**
     * Read object data from the database
     *
     * @access public
     * @param object $object
     * @param array $args
     * @return void
     */
    public function read(&$object, $args = array());

    /**
     * Update object data in the database
     *
     * @access public
     * @param object $object
     * @param array $args
     * @return void
     */
    public function update(&$object, $args = array());

    /**
     * Delete object data from the database
     *
     * @access public
     * @param object $object
     * @param array $args
     * @return void
     */
    public function delete(&$object, $args = array());

    /**
     * Add object meta data to the database
     *
     * @access public
     * @param object $object
     * @param object $meta
     * @param array $args
     * @return void
     */
    public function add_meta(&$object, $meta, $args = array());

    /**
     * Read object meta data from the database
     *
     * @access public
     * @param object $object
     * @param array $args
     * @return mixed
     */
    public function read_meta(&$object, $args = array());

    /**
     * Update object meta data in the database
     *
     * @access public
     * @param object $object
     * @param object $meta
     * @param array $args
     * @return void
     */
    public function update_meta(&$object, $meta, $args = array());

    /**
     * Delete object meta data from the database
     *
     * @access public
     * @param object $object
     * @param object $meta
     * @param array $args
     * @return void
     */
    public function delete_meta(&$object, $meta, $args = array());





}
