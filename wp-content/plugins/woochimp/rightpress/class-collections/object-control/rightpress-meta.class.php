<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * Meta Class
 *
 * @class RightPress_Meta
 * @package RightPress
 * @author RightPress
 */
class RightPress_Meta
{

    // Define meta data structure
    protected $data = array(
        'id'    => null,
        'key'   => null,
        'value' => null,
    );

    // Store changes
    protected $changes;

    /**
     * Constructor
     *
     * @access public
     * @param array $meta
     * @return void
     */
    public function __construct($meta = array())
    {
        $this->changes = $meta;
        $this->apply_changes();
    }

    /**
     * Set property
     *
     * @access public
     * @param string $prop
     * @param mixed $prop_value
     * @return void
     */
    public function __set($prop, $prop_value)
    {
        if (array_key_exists($prop, $this->data)) {
            $this->changes[$prop] = $prop_value;
        }
    }

    /**
     * Get property
     *
     * @access public
     * @param string $prop
     * @return mixed
     */
    public function __get($prop)
    {
        if (array_key_exists($prop, $this->changes)) {
            return $this->changes[$prop];
        }

        return null;
    }

    /**
     * Check if property is set
     *
     * @access public
     * @param string $prop
     * @return bool
     */
    public function __isset($prop)
    {
        return array_key_exists($prop, $this->changes);
    }

    /**
     * Apply changes
     *
     * @access public
     * @return void
     */
    public function apply_changes()
    {
        $this->data = array_replace_recursive($this->data, $this->changes);
    }

    /**
     * Get changes
     *
     * @access public
     * @return array
     */
    public function get_changes()
    {
        $changes = array();

        foreach ($this->changes as $prop => $prop_value) {
            if (!array_key_exists($prop, $this->data) || $prop_value !== $this->data[$prop]) {
                $changes[$prop] = $prop_value;
            }
        }

        return $changes;
    }







}
