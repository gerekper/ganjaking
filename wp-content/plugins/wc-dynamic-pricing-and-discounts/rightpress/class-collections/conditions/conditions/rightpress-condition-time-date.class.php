<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-time.class.php';

/**
 * Condition: Time - Date
 *
 * @class RightPress_Condition_Time_Date
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Time_Date extends RightPress_Condition_Time
{

    protected $key      = 'date';
    protected $method   = 'date';
    protected $fields   = array(
        'after' => array('date'),
    );
    protected $position = 10;

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

        return esc_html__('Date', 'rightpress');
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
        $date->setTime(0, 0, 0);
        return $date;
    }





}
