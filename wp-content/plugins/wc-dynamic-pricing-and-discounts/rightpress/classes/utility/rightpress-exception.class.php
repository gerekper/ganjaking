<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * RightPress Exception Class
 *
 * @class RightPress_Exception
 * @package RightPress
 * @author RightPress
 */
class RightPress_Exception extends Exception
{

    protected $error_code;
    protected $error_data;

    /**
     * Constructor
     *
     * @access public
     * @param string $code
     * @param string $message
     * @param array $data
     * @return void
     */
    public function __construct($code, $message, $data = array())
    {
        // Set error code and extra data
        $this->error_code = $code;
        $this->error_data = $data;

        // Get numeric code
        $numeric_code = is_int($code) ? $code : 0;

        // Call parent constructor
        parent::__construct($message, $numeric_code);
    }

    /**
     * Get error code
     *
     * @access public
     * @return string
     */
    public function get_error_code()
    {
        return $this->error_code;
    }

    /**
     * Check against error code
     *
     * @access public
     * @param string $value
     * @return bool
     */
    public function is_error_code($value)
    {
        return $this->error_code === $value;
    }

    /**
     * Get error data
     *
     * @access public
     * @return array
     */
    public function get_error_data()
    {
        return $this->error_data;
    }







}
