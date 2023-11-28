<?php
namespace Essential_Addons_Elementor\Pro\Extensions;

if (!defined('ABSPATH')) {
    exit;
}

use Elementor\Controls_Manager;

class EAEL_Particle_Section
{

    public function __construct()
    {
        add_action('elementor/frontend/section/before_render', array($this, 'before_render'));
        add_action('elementor/element/section/section_layout/after_section_end', array($this, 'register_controls'), 10);
        add_action('elementor/element/container/section_layout/after_section_end', array($this, 'register_controls'), 10);
        add_action('elementor/frontend/section/after_render', array($this, 'after_render'));

        //Elementor Flexbox Container
        add_action('elementor/frontend/container/before_render', array($this, 'before_render'));
        add_action('elementor/element/container/section_layout/after_section_end', array($this, 'register_controls'), 10);
        add_action('elementor/frontend/container/after_render', array($this, 'after_render'));
    }

    public function register_controls($element)
    {

        $element->start_controls_section(
            'eael_particles_section',
            [
                'label' => __('<i class="eaicon-logo"></i> Particles', 'essential-addons-elementor'),
                'tab' => Controls_Manager::TAB_LAYOUT,
            ]
        );

        $element->add_control(
            'eael_particle_switch',
            [
                'label' => __('Enable Particles', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
            ]
        );

        $element->add_control(
            'eael_particle_area_zindex',
            [
                'label' => __('Z-index', 'essential-addons-elementor'),
                'type' => Controls_Manager::NUMBER,
                'default' => -1,
                'condition' => [
                    'eael_particle_switch' => 'yes',
                ],
            ]
        );

        $element->add_control(
            'eael_particle_theme_from',
            [
                'label' => __('Theme Source', 'essential-addons-elementor'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'presets' => [
                        'title' => __('Defaults', 'essential-addons-elementor'),
                        'icon' => 'fa fa-list',
                    ],
                    'custom' => [
                        'title' => __('Custom', 'essential-addons-elementor'),
                        'icon' => 'fa fa-edit',
                    ],
                ],
                'condition' => [
                    'eael_particle_switch' => 'yes',
                ],
                'default' => 'presets',
            ]
        );

        $element->add_control(
            'eael_particle_preset_themes',
            [
                'label' => esc_html__('Preset Themes', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'label_block' => true,
                'options' => [
                    'default' => __('Default', 'essential-addons-elementor'),
                    'nasa' => __('Nasa', 'essential-addons-elementor'),
                    'bubble' => __('Bubble', 'essential-addons-elementor'),
                    'snow' => __('Snow', 'essential-addons-elementor'),
                    'nyan_cat' => __('Nyan Cat', 'essential-addons-elementor'),
                ],
                'default' => 'default',
                'condition' => [
                    'eael_particle_theme_from' => 'presets',
                    'eael_particle_switch' => 'yes',
                ],
            ]
        );

        $element->add_control(
            'eael_particles_custom_style',
            [
                'label' => __('Custom Style', 'essential-addons-elementor'),
                'type' => Controls_Manager::TEXTAREA,
                'description' => __('You can generate custom particles JSON code from <a href="http://vincentgarreau.com/particles.js/#default" target="_blank">Here!</a>. Simply just past the JSON code above. For more queries <a href="https://essential-addons.com/elementor/docs/" target="_blank">Click Here!</a>', 'essential-addons-elementor'),
                'condition' => [
                    'eael_particle_theme_from' => 'custom',
                    'eael_particle_switch' => 'yes',
                ],
            ]
        );

        $element->add_control(
            'eael_particle_section_notice',
            [
                'raw' => __('You need to configure a <strong style="color:green">Background Type</strong> to see this in full effect. You can do this by switching to the <strong style="color:green">Style</strong> Tab.', 'essential-addons-elementor'),
                'type' => Controls_Manager::RAW_HTML,
                'condition' => [
                    'eael_particle_theme_from' => 'custom',
                    'eael_particle_switch' => 'yes',
                ],
            ]
        );

        $element->add_control(
            'eael_particle_on_mobile',
            [
                'label' => __('Particles on Mobile Devices?', 'essential-addons-elementor'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'return_value' => 'yes',
                'condition' => [
                    'eael_particle_switch'  => 'yes'
                ]
            ]
        );

        $element->end_controls_section();

    }

    public function before_render($element)
    {

        $settings = $element->get_settings();

        if ($settings['eael_particle_switch'] !== 'yes') {
            $element->add_render_attribute('_wrapper', 'data-particle_enable', 'false');
        }

        if ($settings['eael_particle_switch'] == 'yes') {
            $element->add_render_attribute(
                '_wrapper',
                [
                    'data-particle_enable' => 'true',
                    'class' => 'eael-section-particles-' . $element->get_id(),
                    'data-eael_ptheme_source' => $settings['eael_particle_theme_from'],
                    'data-preset_theme' => $settings['eael_particle_preset_themes'],
                    'data-custom_style' => $settings['eael_particles_custom_style'],
                ]
            );
        }

        $element->add_render_attribute( '_wrapper', 'data-particle-mobile-disabled', 'false' );

        if ($settings['eael_particle_on_mobile'] !== 'yes') {
            $element->add_render_attribute(
                '_wrapper',
                [
                    'data-particle-mobile-disabled' => 'true'
                ]
            );
        }

    }

    public function after_render($element)
    {
        $data = $element->get_data();
        $settings = $element->get_settings_for_display();
        $type = $data['elType'];
        $zindex = !empty($settings['eael_particle_area_zindex']) ? $settings['eael_particle_area_zindex'] : 0;
        if (('section' == $type || 'container' == $type ) && ($element->get_settings('eael_particle_switch') == 'yes')) { ?>
            <style>
                .elementor-element-<?php echo $element->get_id(); ?>.eael-particles-section > canvas {
                    z-index: <?php echo $zindex; ?>;
                    position: absolute;
                    top:0;
                }
            </style>
        <?php }
    }
}
