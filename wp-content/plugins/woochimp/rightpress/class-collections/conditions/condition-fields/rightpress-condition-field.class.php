<?php

// Exit if accessed directly
defined('ABSPATH') || exit;

/**
 * Parent condition field class
 *
 * @class RightPress_Condition_Field
 * @package RightPress
 * @author RightPress
 */
abstract class RightPress_Condition_Field extends RightPress_Item
{

    protected $item_key = 'condition_field';

    protected $accepts_multiple     = false;
    protected $supports_hierarchy   = false;

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
     * Get field attributes
     *
     * @access public
     * @param string $context
     * @param string $alias
     * @return array
     */
    public function get_field_attributes($context = null, $alias = 'condition')
    {

        $attributes = array();

        // Define validation rules key
        $validation_rules_key = 'data-' . str_replace('_', '-', $this->get_plugin_prefix()) . 'validation';

        // Define attribute keys and method names to retrieve values
        $attribute_keys = array(
            'id'                    => 'get_id',
            'name'                  => 'get_name',
            'class'                 => 'get_class',
            'options'               => 'get_options',
            'placeholder'           => 'get_placeholder',
            'disabled'              => 'get_disabled',
            'readonly'              => 'get_readonly',
            $validation_rules_key   => 'get_validation_rules'
        );

        // Iterate over attribute keys
        foreach ($attribute_keys as $attribute_key => $method) {

            // Get attribute value
            $value = $this->$method($context, $alias);

            // Add to main array if it is set
            if ($value !== null) {
                $attributes[$attribute_key] = $value;
            }
        }

        return $attributes;
    }

    /**
     * Get id
     *
     * @access public
     * @param string $context
     * @param string $alias
     * @return string
     */
    public function get_id($context = null, $alias = 'condition')
    {

        // Check if conditions are second level items
        $is_second_level = $this->get_controller()->is_second_level_item();

        // Format id
        return $this->get_plugin_prefix() . (($context && $is_second_level) ? ($context . '_') : '') . $alias . 's' . '_' . ($is_second_level ? '{i}_' : '') . $this->key . '_{' . ($is_second_level ? 'j' : 'i') . '}';
    }

    /**
     * Get name
     *
     * @access public
     * @param string $context
     * @param string $alias
     * @return string
     */
    public function get_name($context = null, $alias = 'condition')
    {

        // Check if conditions are second level items
        $is_second_level = $this->get_controller()->is_second_level_item();

        // Format name
        return $this->get_plugin_prefix() . 'settings' . (($context && $is_second_level) ? '[' . $context . ']' : '') . ($is_second_level ? '[{i}]' : '') . '[' . $alias . 's][{' . ($is_second_level ? 'j' : 'i') . '}][' . $this->key . ']' . ($this->accepts_multiple ? '[]' : '');
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

        $class = $this->get_plugin_prefix() . $alias . '_' . $this->key . ' ';

        if ($context) {
            $class .= $this->get_plugin_prefix() . $context . '_' . $alias . '_' . $this->key . ' ';
        }

        $class .= $this->get_plugin_prefix() . 'child_element_field ';

        return $class;
    }

    /**
     * Get options
     *
     * @access public
     * @return array
     */
    public function get_options()
    {

        return null;
    }

    /**
     * Get placeholder
     *
     * @access public
     * @return string
     */
    public function get_placeholder()
    {

        return null;
    }

    /**
     * Get disabled value
     *
     * @access public
     * @return string
     */
    public function get_disabled()
    {

        return null;
    }

    /**
     * Get readonly value
     *
     * @access public
     * @return string
     */
    public function get_readonly()
    {

        return null;
    }

    /**
     * Get validation rules
     *
     * @access public
     * @return string
     */
    public function get_validation_rules()
    {

        return 'required';
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

        return isset($posted[$this->key]) && !RightPress_Help::is_empty($posted[$this->key]);
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

        if (isset($posted[$this->key])) {
            return $posted[$this->key];
        }

        return null;
    }

    /**
     * Get child ids for fields that support hierarchy
     *
     * @access public
     * @param array $values
     * @return array
     */
    public function get_children($values)
    {

        $values_with_children = array();

        foreach ($values as $value) {
            $values_with_children[$value] = array($value);
        }

        return $values_with_children;
    }

    /**
     * Check if condition field supports hierarchy
     *
     * @access public
     * @return bool
     */
    public function supports_hierarchy()
    {

        return $this->supports_hierarchy;
    }





}
