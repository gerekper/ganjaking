<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition.class.php';

/**
 * Condition Group: Custom Taxonomy
 *
 * @class RightPress_Condition_Custom_Taxonomy
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Custom_Taxonomy extends RightPress_Condition
{

    protected $group_key        = 'custom_taxonomy';
    protected $group_position   = 30;

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        parent::__construct();

        $this->hook_group();
    }

    /**
     * Get group label
     *
     * @access public
     * @return string
     */
    public function get_group_label()
    {

        return __('Custom Taxonomy', 'rightpress');
    }

    /**
     * Check if condition is enabled
     *
     * @access public
     * @return bool
     */
    public function is_enabled()
    {

        // Custom taxonomy conditions are always enabled -
        // they are not loaded at all when they are not used
        return true;
    }





}
