<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-field-select-timeframe.class.php';

/**
 * Condition Field: Select - Timeframe Event
 *
 * @class RightPress_Condition_Field_Select_Timeframe_Event
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Field_Select_Timeframe_Event extends RightPress_Condition_Field_Select_Timeframe
{

    protected $key = 'timeframe_event';

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





}
