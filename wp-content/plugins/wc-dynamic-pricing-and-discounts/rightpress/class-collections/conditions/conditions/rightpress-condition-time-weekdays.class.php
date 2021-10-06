<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-time.class.php';

/**
 * Condition: Time - Weekdays
 *
 * @class RightPress_Condition_Time_Weekdays
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Time_Weekdays extends RightPress_Condition_Time
{

    protected $key      = 'weekdays';
    protected $method   = 'list';
    protected $fields   = array(
        'after' => array('weekdays'),
    );
    protected $position = 40;

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

        return esc_html__('Days of week', 'rightpress');
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

        $date = RightPress_Help::get_datetime_object();
        return $date->format('w');
    }





}
