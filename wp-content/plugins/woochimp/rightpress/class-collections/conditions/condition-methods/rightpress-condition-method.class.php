<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * Parent condition method class
 *
 * @class RightPress_Condition_Method
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Method extends RightPress_Item
{

    protected $item_key = 'condition_method';

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





}
