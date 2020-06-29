<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-field-number.class.php';

/**
 * Condition Field: Number - Number
 *
 * @class RightPress_Condition_Field_Number_Number
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Field_Number_Number extends RightPress_Condition_Field_Number
{

    protected $key = 'number';

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
