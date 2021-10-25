<?php

namespace MasterAddons\Modules;

use \Elementor\Element_Base;
use \Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Parallax extenstion
 *
 * Adds parallax on widgets and columns
 *
 * @since 1.2.0
 */
class Extension_Parallax_Background extends Element_Base
{

    /**
     * A list of scripts that the widgets is depended in
     *
     * @since 1.8.0
     **/
    public function get_script_depends()
    {
        return [
            'parallax-background',
            'jquery-resize-ee',
            'jquery-visible',
        ];
    }

    /**
     * The description of the current extension
     *
     * @since 1.8.0
     **/
    public static function get_description()
    {
        return __('Adds parallax options for the background image of a section. Can be found under Style &rarr; Background &rarr; Extras if a background image is selected.', 'elementor-extras');
    }

    /**
     * Is disabled by default
     *
     * Return wether or not the extension should be disabled by default,
     * prior to user actually saving a value in the admin page.
     * Checks if Elementor Pro is enabled to allow for use of Scrolling Effects
     *
     * @access public
     * @since 2.1.0
     * @return bool
     */
    public static function is_default_disabled()
    {
        if (is_elementor_pro_active()) {
            return true;
        }
        return false;
    }

    /**
     * Add Actions
     *
     * @since 1.2.0
     *
     * @access private
     */
    private function add_controls($element, $args)
    {

        $element_type = $element->get_type();
        $elementor_pro_condition = [];

        if (is_elementor_pro_active()) {
            $elementor_pro_condition = [
                'background_motion_fx_motion_fx_scrolling' => '', // Elementor Pro Scrolling Effect should be off
            ];
        }

        $element->add_control(
            'parallax_heading',
            [
                'type'        => Controls_Manager::HEADING,
                'label'     => __('Extras', 'elementor-extras'),
                'separator' => 'before',
                'condition'    => array_merge(
                    [
                        'background_background'     => ['classic'],
                        'background_image[url]!'     => '',
                    ],
                    $elementor_pro_condition
                ),
            ]
        );

        $element->add_control(
            'parallax_background_enable',
            [
                'label'                    => _x('Parallax Background', 'Parallax Background', 'elementor-extras'),
                'type'                     => Controls_Manager::SWITCHER,
                'default'                 => '',
                'label_on'                 => __('Yes', 'elementor-extras'),
                'label_off'             => __('No', 'elementor-extras'),
                'return_value'             => 'yes',
                'frontend_available'     => true,
                'condition'                => array_merge(
                    [
                        'background_background'     => ['classic'],
                        'background_image[url]!'     => '',
                    ],
                    $elementor_pro_condition
                ),
            ]
        );

        $element->add_responsive_control(
            'parallax_background_speed',
            [
                'label'         => _x('Parallax Speed', 'Parallax Control', 'elementor-extras'),
                'type'             => Controls_Manager::SLIDER,
                'default'        => [
                    'size'            => 0.5,
                ],
                'range'         => [
                    'px'         => [
                        'min'    => 0,
                        'max'     => 1,
                        'step'    => 0.01,
                    ],
                ],
                'condition'                => array_merge(
                    [
                        'parallax_background_enable!' => '',
                        'background_background' => ['classic'],
                    ],
                    $elementor_pro_condition
                ),
                'frontend_available' => true,
            ]
        );

        $element->add_control(
            'parallax_background_direction',
            [
                'label'     => _x('Parallax Direction', 'Parallax Control', 'elementor-extras'),
                'type'         => Controls_Manager::SELECT,
                'default'     => 'down',
                'options'     => [
                    'up'     => __('Up', 'elementor-extras'),
                    'down'     => __('Down', 'elementor-extras'),
                    'left'     => __('Left', 'elementor-extras'),
                    'right' => __('Right', 'elementor-extras'),
                ],
                'condition'                => array_merge(
                    [
                        'parallax_background_enable!' => '',
                        'background_background'     => ['classic'],
                        'background_image[url]!'     => '',
                    ],
                    $elementor_pro_condition
                ),
                'frontend_available' => true,
            ]
        );
    }

    /**
     * Update existing background controls
     *
     * @since 2.1.0
     *
     * @access private
     */
    protected function update_background_controls($element)
    {

        // This is the selector for our transform element
        $parallax_inner_selector = '{{WRAPPER}}:not(.elementor-motion-effects-element-type-background) > .ee-parallax > .ee-parallax__inner';

        // Make the background image url available in the frontend
        $element->update_responsive_control('background_image', array(
            'selectors' => array_merge(
                [$parallax_inner_selector => 'background-image: url("{{URL}}");'],
                $element->get_controls('background_image')['selectors']
            ),
            'frontend_available' => true,
        ));

        $element->update_responsive_control('background_position', array(
            'selectors' => array_merge(
                [$parallax_inner_selector => 'background-position: {{VALUE}};'],
                $element->get_controls('background_position')['selectors']
            ),
        ));

        $element->update_control('background_attachment', array(
            'selectors' => array_merge(
                [$parallax_inner_selector => 'background-attachment: {{VALUE}};'],
                $element->get_controls('background_attachment')['selectors']
            ),
        ));

        $element->update_responsive_control('background_repeat', array(
            'selectors' => array_merge(
                [$parallax_inner_selector => 'background-repeat: {{VALUE}};'],
                $element->get_controls('background_repeat')['selectors']
            ),
        ));

        $element->update_responsive_control('background_size', array(
            'selectors' => array_merge(
                [$parallax_inner_selector => 'background-size: {{VALUE}};'],
                $element->get_controls('background_size')['selectors']
            ),
        ));

        $element->update_responsive_control('background_bg_width', array(
            'selectors' => array_merge(
                [$parallax_inner_selector => 'background-size: {{SIZE}}{{UNIT}} auto'],
                $element->get_controls('background_bg_width')['selectors']
            ),
        ));
    }

    /**
     * Add Actions
     *
     * @since 1.2.0
     *
     * @access private
     */
    protected function add_actions()
    {

        // Activate for widgets
        add_action('elementor/element/section/section_background/before_section_end', function ($element, $args) {

            $this->update_background_controls($element);

            $this->add_controls($element, $args);
        }, 10, 2);

        // Activate for columns

    }
}
