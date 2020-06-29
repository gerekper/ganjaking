<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * Condition fields controller
 *
 * @class RightPress_Controller_Condition_Fields
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Controller_Condition_Fields extends RightPress_Item_Controller
{

    protected $item_key = 'condition_field';

    protected $is_second_level_item = false;

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
     * Check if conditions are second level items
     *
     * @access public
     * @return bool
     */
    public function is_second_level_item()
    {

        return $this->is_second_level_item;
    }




}
