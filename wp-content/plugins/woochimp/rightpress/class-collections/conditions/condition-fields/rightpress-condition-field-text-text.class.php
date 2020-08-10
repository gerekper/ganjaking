<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-field-text.class.php';

/**
 * Condition Field: Text - Text
 *
 * @class RightPress_Condition_Field_Text_Text
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Field_Text_Text extends RightPress_Condition_Field_Text
{

    protected $key = 'text';

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
     * Validate field value
     *
     * @access public
     * @param array $posted
     * @param object $condition
     * @param string $method_option_key
     * @return bool
     */
    public function validate($posted, $condition, $method_option_key)
    {
        // Value is not empty
        if (isset($posted[$this->key]) && !RightPress_Help::is_empty($posted[$this->key])) {
            return true;
        }

        // Special case for specific condition methods
        if (in_array($condition->get_method(), array('field', 'meta'), true) && in_array($method_option_key, array('is_empty', 'is_not_empty', 'is_checked', 'is_not_checked'), true)) {
            return true;
        }

        return false;
    }





}
