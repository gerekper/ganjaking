<?php

namespace DynamicContentForElementor;

use Elementor\Controls_Manager;
class ForceDynamicTags extends Controls_Manager
{
    public static $types = [Controls_Manager::TEXT => \true, Controls_Manager::TEXTAREA => \true, Controls_Manager::WYSIWYG => \true, Controls_Manager::NUMBER => \true, Controls_Manager::URL => \true, Controls_Manager::COLOR => \true, Controls_Manager::SLIDER => \true, Controls_Manager::MEDIA => \true, Controls_Manager::GALLERY => \true];
    /**
     * Add control to stack.
     *
     * This method adds a new control to the stack.
     *
     * @since 1.0.0
     * @access public
     *
     * @param \Elementor\Controls_Stack $element      Element stack.
     * @param string         $control_id   Control ID.
     * @param array          $control_data Control data.
     * @param array          $options      Optional. Control additional options.
     *                                     Default is an empty array.
     *
     * @return bool True if control added, False otherwise.
     */
    public function add_control_to_stack(\Elementor\Controls_Stack $element, $control_id, $control_data, $options = [])
    {
        $element_name = $element->get_name();
        // avoid EPRO Popup condition issue
        if (!($element_name === 'popup_triggers' || $element_name === 'popup_timing')) {
            //add Dynamic Tags to $control_data
            $this->add_dynamic_tags($control_data);
        }
        return parent::add_control_to_stack($element, $control_id, $control_data, $options);
    }
    /**
     * Some controls have the ability to display Dynamic Tags (for example a
     * Text Control) but they are not always enabled. The idea of this function
     * is to force Dynamic Tags on all such controls. The actual controls that
     * are forced are the one listed in the property self::$types.
     */
    public function add_dynamic_tags(&$control_data)
    {
        if (!empty($control_data)) {
            foreach ($control_data as $key => $control) {
                if ($key !== 'dynamic' && \is_array($control)) {
                    self::add_dynamic_tags($control);
                }
            }
            if (isset($control_data['type']) && \is_string($control_data['type']) && isset(self::$types[$control_data['type']])) {
                $control_data['dynamic']['active'] = \true;
            }
        }
    }
}
