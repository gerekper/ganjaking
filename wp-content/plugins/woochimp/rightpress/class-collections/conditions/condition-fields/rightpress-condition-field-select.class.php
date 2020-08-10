<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-field.class.php';

/**
 * Condition Field Group: Select
 *
 * @class RightPress_Condition_Field_Select
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Field_Select extends RightPress_Condition_Field
{

    protected $is_grouped = false;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        parent::__construct();
    }

    /**
     * Display field
     *
     * @access public
     * @param string $context
     * @param string $alias
     * @return void
     */
    public function display($context, $alias = 'condition')
    {
        RightPress_Forms::select($this->get_field_attributes($context, $alias), false, $this->is_grouped);
    }





}
