<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-method-field.class.php';

/**
 * Condition Method: Meta
 *
 * Based on condition method Field
 *
 * @class RightPress_Condition_Method_Meta
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Method_Meta extends RightPress_Condition_Method_Field
{

    protected $key = 'meta';

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
     * Check against condition method
     *
     * This only checks first meta value (designed to work with unique meta entries)
     *
     * Note: If "contains" option is selected and a list of values is found,
     * it will check each individual item to check if it "contains" specific text,
     * as oposed to checking if the whole list "contains" an equal entry
     *
     * @access public
     * @param string $option_key
     * @param mixed $value
     * @param mixed $condition_value
     * @return bool
     */
    public function check($option_key, $value, $condition_value)
    {

        // Prepare single meta value to be checked
        if (is_array($value)) {

            // Get first element
            if (!empty($value)) {
                $value = array_shift($value);
            }
            // Value is not set
            else {
                $value = null;
            }
        }

        // Check value
        return parent::check($option_key, $value, $condition_value);
    }





}
