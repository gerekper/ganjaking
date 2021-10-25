<?php

namespace MasterAddons\Modules;

use \Elementor\Element_Base;
use \Elementor\Controls_Manager;

use \MasterAddons\Inc\Classes\JLTMA_Extension_Prototype;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
};

/**
 * Parallax Scrolling Effect - Rellax
 */

class Extension_Rellax extends JLTMA_Extension_Prototype
{

    private static $instance = null;
    public $name = 'Rellax';
    public $has_controls = true;
    public $common_sections_actions = array(
        array(
            'element' => 'common',
            'action' => '_section_style',
        ),

        array(
            'element' => 'column',
            'action' => 'section_advanced',
        ),
    );

    private function add_controls($element, $args)
    {

        $element_type = $element->get_type();

        $element->add_control(
            'enabled_rellax',
            [
                'label' => __('Enabled Rellax', MELA_TD),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => __('Yes', MELA_TD),
                'label_off' => __('No', MELA_TD),
                'return_value' => 'yes',
                'frontend_available' => true,
            ]
        );
        $element->add_responsive_control(
            'speed_rellax',
            [
                'label' => __('Speed', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'min' => -10,
                        'max' => 10,
                        'step' => 0.1,
                    ]
                ],
                'render_type' => 'template',
                'frontend_available' => true,
                'condition' => [
                    'enabled_rellax' => 'yes',
                ]
            ]
        );
        $element->add_responsive_control(
            'percentage_rellax',
            [
                'label' => __('Percentage', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0.5,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1,
                        'step' => 0.01,
                    ]
                ],
                'render_type' => 'template',
                'condition' => [
                    'enabled_rellax' => 'yes',
                ]
            ]
        );
        $element->add_control(
            'zindex_rellax',
            [
                'label' => __('Z-Index', MELA_TD),
                'type' => Controls_Manager::NUMBER,
                'default' => 0,
                'min' => -1,
                'max' => 50,
                'step' => 1,
                'condition' => [
                    'enabled_rellax' => 'yes',
                ]
            ]
        );


        /* $element->add_control(
          'vertical_rellax',
          [
                  'label' => __( 'Vertical', MELA_TD ),
                  'type' => Controls_Manager::SWITCHER,
                  'default' => 'yes',
                  'label_on' => __( 'Yes', MELA_TD ),
                  'label_off' => __( 'No', MELA_TD ),
                  'return_value' => 'yes',
                  'frontend_available' => true,
                  'condition'     => [
                  'enabled_rellax' => 'yes',
                ]
            ]
          );
          $element->add_control(
          'horizontal_rellax',
          [
          'label' => __( 'Horizontal', MELA_TD ),
          'type' => Controls_Manager::SWITCHER,
          'default' => '',
          'label_on' => __( 'Yes', MELA_TD ),
          'label_off' => __( 'No', MELA_TD ),
          'return_value' => 'yes',
          'frontend_available' => true,
          'condition'     => [
          'enabled_rellax' => 'yes',
          ]
          ]
          ); */
    }

    protected function add_actions()
    {

        // Activate controls for widgets
        add_action('elementor/element/common/jltma_section_rellax_advanced/before_section_end', function ($element, $args) {
            $this->add_controls($element, $args);
        }, 10, 2);

        add_filter('elementor/widget/print_template', array($this, 'rellax_print_template'), 11, 2);

        add_action('elementor/widget/render_content', array($this, 'rellax_render_template'), 11, 2);

        // add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'before_render'],10);
        // add_action( 'elementor/frontend/element/before_render', [ $this, 'before_render'],10,1);
        // add_action( 'elementor/frontend/column/before_render', [ $this, 'before_render'],10,1);
        // add_action( 'elementor/frontend/section/before_render', [ $this, 'before_render'],10,1);
        // add_action( 'elementor/frontend/widget/before_render', [ $this, 'before_render' ], 10,1 );

        add_action('elementor/preview/enqueue_scripts', [$this, 'jltma_add_relax_scripts']);

        // Activate controls for columns
        add_action('elementor/element/column/jltma_section_rellax_advanced/before_section_end', function ($element, $args) {
            $this->add_controls($element, $args);
        }, 10, 2);
    }


    public static function jltma_add_relax_scripts()
    {
        wp_enqueue_script('ma-el-rellaxjs-libs', MELA_PLUGIN_URL . '/assets/vendor/rellax/rellax.min.js', array('jquery'), MELA_VERSION, true);
    }

    public function rellax_print_template($content, $widget)
    {
        if (!$content)
            return '';

        $id_item = $widget->get_id();

        $content = "<# if ( '' !== settings.enabled_rellax ) { #><div id=\"rellax-{{id}}\" class=\"rellax\" data-rellax-percentage=\"{{ settings.percentage_rellax.size }}\" data-rellax-zindex=\"{{ settings.zindex_rellax }}\">" . $content . "</div><# } else { #>" . $content . "<# } #>";
        return $content;
    }



    public function rellax_render_template($content, $widget)
    {
        $settings = $widget->get_settings_for_display();


        if (isset($settings['enabled_rellax']) && $settings['enabled_rellax'] == 'yes') {

            $this->_enqueue_alles();

            self::jltma_add_relax_scripts();


            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) {
            }

            $id_item = $widget->get_id();

            $content = '<div id="rellax-' . $id_item . '" class="rellax" data-rellax-percentage="' . $settings['percentage_rellax']['size'] . '" data-rellax-zindex="' . $settings['zindex_rellax'] . '">' . $content . '</div>';
        }
        return $content;
    }

    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }
}

Extension_Rellax::get_instance();
