<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

// Load dependencies
require_once 'rightpress-condition-field.class.php';

/**
 * Condition Field Group: Multiselect
 *
 * @class RightPress_Condition_Field_Multiselect
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Field_Multiselect extends RightPress_Condition_Field
{

    protected $accepts_multiple = true;

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
        RightPress_Forms::multiselect($this->get_field_attributes($context, $alias));
    }

    /**
     * Get options
     *
     * @access public
     * @return array
     */
    public function get_options()
    {
        return array();
    }

    /**
     * Get multiselect options
     *
     * @access public
     * @param array $params
     * @return array
     */
    public function get_multiselect_options($params = array())
    {
        // Do not load options if params are not set
        if (empty($params)) {
            return array();
        }

        // Load multiselect field options
        $multiselect_options = $this->load_multiselect_options(array(), $params['query']);

        // Remove options that do not match criteria
        foreach ($multiselect_options as $option_key => $option) {
            if (!RightPress_Help::string_contains_phrase($option['text'], $params['query']) || (!empty($params['selected']) && in_array($option['id'], $params['selected'], true))) {
                unset($multiselect_options[$option_key]);
            }
        }

        // Return options
        return $multiselect_options;
    }

    /**
     * Get multiselect option labels by ids
     *
     * @access public
     * @param array $value
     * @return array
     */
    public function get_multiselect_option_labels($value)
    {
        // Load multiselect options
        $multiselect_options = $this->load_multiselect_options($value);

        // Get existing option ids
        $existing_ids = wp_list_pluck($multiselect_options, 'id');

        // Add missing options
        foreach ($value as $id) {
            if (!in_array($id, $existing_ids, true)) {
                $multiselect_options[] = array(
                    'id'    => $id,
                    'text'  => (is_numeric($id) ? '#' : '') . $id . ' ' . esc_html__('(DELETED)', 'rightpress'),
                );
            }
        }

        return $multiselect_options;
    }

    /**
     * Load multiselect options
     *
     * @access public
     * @param array $ids
     * @param string $query
     * @return array
     */
    public function load_multiselect_options($ids = array(), $query = '')
    {
        return array();
    }

    /**
     * Get class
     *
     * @access public
     * @param string $context
     * @param string $alias
     * @return string
     */
    public function get_class($context = null, $alias = 'condition')
    {

        return parent::get_class($context, $alias) . 'rightpress_select2 ' . $this->get_plugin_prefix() . 'select2 ' . $this->get_plugin_prefix() . 'select2_multiselect';
    }

    /**
     * Sanitize field value
     *
     * @access public
     * @param array $posted
     * @param object $condition
     * @param string $method_option_key
     * @return mixed
     */
    public function sanitize($posted, $condition, $method_option_key)
    {
        if (isset($posted[$this->key]) && !RightPress_Help::is_empty($posted[$this->key])) {
            return (array) $posted[$this->key];
        }

        return array();
    }

    /**
     * Get placeholder
     *
     * @access public
     * @return string
     */
    public function get_placeholder()
    {
        return esc_html__('Select values', 'rightpress');
    }





}
