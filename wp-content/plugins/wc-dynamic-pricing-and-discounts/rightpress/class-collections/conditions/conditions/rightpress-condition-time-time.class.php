<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-time.class.php';

/**
 * Condition: Time - Time
 *
 * @class RightPress_Condition_Time_Time
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Time_Time extends RightPress_Condition_Time
{

    protected $key      = 'time';
    protected $method   = 'time';
    protected $fields   = array(
        'after' => array('time'),
    );
    protected $position = 20;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        parent::__construct();

        $this->hook();
    }

    /**
     * Get label
     *
     * @access public
     * @return string
     */
    public function get_label()
    {

        return esc_html__('Time', 'rightpress');
    }

    /**
     * Get value to compare against condition
     *
     * @access public
     * @param array $params
     * @return mixed
     */
    public function get_value($params)
    {

        return RightPress_Help::get_datetime_object();
    }





}
