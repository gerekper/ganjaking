<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * List table
 *
 * @class RightPress_WP_List_Table
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_WP_List_Table extends WP_List_Table
{

    private $related_object = null;

    private $singular_name  = '';
    private $plural_name    = '';

    /**
     * Constructor
     *
     * @access public
     * @return void
     */
    public function __construct()
    {

        // Call parent constructor
        parent::__construct(array(
            'singular'  => $this->singular_name,
            'plural'    => $this->plural_name,
            'ajax'      => false,
        ));
    }

    /**
     * Display the table
     *
     * Overriding to hide tablenav and column headers
     *
     * @access public
     * @return void
     */
    public function display()
    {

        $singular = $this->_args['singular'];

        echo '<table class="wp-list-table ' . implode(' ', $this->get_table_classes()) . '"><tbody id="the-list" data-wp-lists=\'list:' . $this->_args['singular'] . '\'>';

            $this->display_rows_or_placeholder();

        echo '</tbody></table>';
    }

    /**
     * Set related object
     *
     * @access public
     * @param object $object
     * @return void
     */
    public function set_related_object($object)
    {

        $this->related_object = $object;
    }

    /**
     * Get related object
     *
     * @access public
     * @return object
     */
    public function get_related_object()
    {

        return $this->related_object;
    }






}
