<?php

namespace ElementPack\Modules\ImageParallax;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use ElementPack;
use ElementPack\Base\Element_Pack_Module_Base;
use ElementPack\Element_Pack_Loader;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

    public function __construct() {
        parent::__construct();
        $this->add_actions();
    }

    public function get_name() {
        return 'bdt-image-parallax';
    }

    public function register_section($element) {
        $element->start_controls_section(
            'element_pack_image_parallax_section',
            [
                'tab' => Controls_Manager::TAB_ADVANCED,
                'label' => BDTEP_CP . esc_html__('Image Parallax', 'visibility-logic-elementor'),
            ]
        );
        $element->end_controls_section();
    }

    public function register_controls($section, $args) {

        $section->start_controls_tabs('element_pack_section_parallax_tabs');

        $section->start_controls_tab(
            'element_pack_section_image_parallax_tab',
            [
                'label' => __('Image', 'bdthemes-element-pack'),
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'section_parallax_title',
            [
                'label' => __('Title', 'bdthemes-element-pack'),
                'type' => Controls_Manager::TEXT,
                'default' => __('Parallax 1', 'bdthemes-element-pack'),
                'label_block' => true,
                'render_type' => 'ui',
            ]
        );

        $repeater->add_control(
            'section_parallax_image',
            [
                'label' => esc_html__('Image', 'bdthemes-element-pack'),
                'type' => Controls_Manager::MEDIA,
                //'condition' => [ 'parallax_content' => 'parallax_image' ],
            ]
        );

        $repeater->add_control(
            'section_parallax_depth',
            [
                'label' => __('Depth', 'bdthemes-element-pack'),
                'type' => Controls_Manager::NUMBER,
                'default' => 0.1,
                'min' => 0,
                'max' => 1,
                'step' => 0.1,
            ]
        );

        $repeater->add_control(
            'section_parallax_bgp_x',
            [
                'label' => __('Image X Position', 'bdthemes-element-pack'),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 100,
                'default' => 50,
            ]
        );

        $repeater->add_control(
            'section_parallax_bgp_y',
            [
                'label' => __('Image Y Position', 'bdthemes-element-pack'),
                'type' => Controls_Manager::NUMBER,
                'min' => 0,
                'max' => 100,
                'default' => 50,
            ]
        );

        $repeater->add_control(
            'section_parallax_bg_size',
            [
                'label' => __('Image Size', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => 'cover',
                'options' => [
                    'auto' => __('Auto', 'bdthemes-element-pack'),
                    'cover' => __('Cover', 'bdthemes-element-pack'),
                    'contain' => __('Contain', 'bdthemes-element-pack'),
                ],
            ]
        );


        $section->add_control(
            'section_parallax_elements',
            [
                'label' => __('Parallax Items', 'bdthemes-element-pack'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'prevent_empty' => false,
                'title_field' => '{{{ section_parallax_title }}}',
            ]
        );


        $section->add_control(
            'section_parallax_mode',
            [
                'label' => esc_html__('Parallax Mode', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    '' => esc_html__('Relative', 'bdthemes-element-pack'),
                    'clip' => esc_html__('Clip', 'bdthemes-element-pack'),
                    'hover' => esc_html__('Hovar (Mobile also turn off)', 'bdthemes-element-pack'),
                ],
            ]
        );


        $section->end_controls_tab();

        $section->start_controls_tab(
            'element_pack_section_color_parallax_tab',
            [
                'label' => __('Color', 'bdthemes-element-pack'),
            ]
        );


        $section->add_control(
            'element_pack_sbgc_parallax_show',
            [
                'label' => __('Background Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'return_value' => 'yes',
            ]
        );

        $section->add_control(
            'element_pack_sbgc_parallax_sc',
            [
                'label' => esc_html__('Start Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'element_pack_sbgc_parallax_show' => 'yes',
                ],
            ]
        );

        $section->add_control(
            'element_pack_sbgc_parallax_ec',
            [
                'label' => esc_html__('End Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'element_pack_sbgc_parallax_show' => 'yes',
                ],

            ]
        );

        $section->add_control(
            'element_pack_sbc_parallax_show',
            [
                'label' => __('Border Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'return_value' => 'yes',
                'separator' => 'before',
            ]
        );

        $section->add_control(
            'element_pack_sbc_parallax_sc',
            [
                'label' => esc_html__('Start Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'element_pack_sbc_parallax_show' => 'yes',
                ],
            ]
        );

        $section->add_control(
            'element_pack_sbc_parallax_ec',
            [
                'label' => esc_html__('End Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'condition' => [
                    'element_pack_sbc_parallax_show' => 'yes',
                ],

            ]
        );


        $section->end_controls_tab();

        $section->end_controls_tabs();
    }

    public function enqueue_scripts() {
        $suffix       = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
        wp_enqueue_script('bdt-parallax', BDTEP_ASSETS_URL . 'vendor/js/parallax' . $suffix . '.js', null, true);
    }

    public function section_parallax_before_render($section) {
        $parallax_elements = $section->get_settings('section_parallax_elements');
        $settings = $section->get_settings();


        if ('yes' === $settings['element_pack_sbgc_parallax_show']) {

            $color1 = ($settings['element_pack_sbgc_parallax_sc']) ? $settings['element_pack_sbgc_parallax_sc'] : '#fff';
            $color2 = ($settings['element_pack_sbgc_parallax_ec']) ? $settings['element_pack_sbgc_parallax_ec'] : '#fff';

            $section->add_render_attribute('_wrapper', 'data-bdt-parallax', 'background-color: ' . $color1 . ',' . $color2 . ';');
        }


        if ('yes' === $settings['element_pack_sbc_parallax_show']) {

            $color1 = ($settings['element_pack_sbc_parallax_sc']) ? $settings['element_pack_sbc_parallax_sc'] : '#fff';
            $color2 = ($settings['element_pack_sbc_parallax_ec']) ? $settings['element_pack_sbc_parallax_ec'] : '#fff';

            $section->add_render_attribute('_wrapper', 'data-bdt-parallax', 'border-color: ' . $color1 . ',' . $color2 . ';');
        }


        if (!empty($parallax_elements)) {
            $this->enqueue_scripts();
            wp_enqueue_style('ep-image-parallax');

            $id = $section->get_id();
            $section->add_render_attribute('scene', 'class', 'parallax-scene');
            $section->add_render_attribute('_wrapper', 'class', 'has-bdt-parallax');

            if ('relative' === $settings['section_parallax_mode']) {
                $section->add_render_attribute('scene', 'data-relative-input', 'true');
            } elseif ('clip' === $settings['section_parallax_mode']) {
                $section->add_render_attribute('scene', 'data-clip-relative-input', 'true');
            } elseif ('hover' === $settings['section_parallax_mode']) {
                $section->add_render_attribute('scene', 'data-hover-only', 'true');
            }

?>
            <div data-parallax-id="bdt_scene<?php echo esc_attr($id); ?>" id="bdt_scene<?php echo esc_attr($id); ?>" <?php echo $section->get_render_attribute_string('scene'); ?>>
                <?php foreach ($parallax_elements as $index => $item) : ?>

                    <?php

                    $image_src = wp_get_attachment_image_src($item['section_parallax_image']['id'], 'full');

                    if ($item['section_parallax_bgp_x']) {
                        $section->add_render_attribute('item', 'style', 'background-position-x: ' . $item['section_parallax_bgp_x'] . '%;', true);
                    }
                    if ($item['section_parallax_bgp_y']) {
                        $section->add_render_attribute('item', 'style', 'background-position-y: ' . $item['section_parallax_bgp_y'] . '%;');
                    }
                    if ($item['section_parallax_bg_size']) {
                        $section->add_render_attribute('item', 'style', 'background-size: ' . $item['section_parallax_bg_size'] . ';');
                    }

                    if (isset($image_src[0])) {
                        $section->add_render_attribute('item', 'style', 'background-image: url(' . esc_url($image_src[0]) . ');');
                    }

                    ?>

                    <div data-depth="<?php echo esc_attr($item['section_parallax_depth']); ?>" class="bdt-scene-item" <?php echo $section->get_render_attribute_string('item'); ?>></div>

                <?php endforeach; ?>
            </div>

<?php
        }
    }

    protected function add_actions() {

        add_action('elementor/element/container/section_layout/after_section_end', [$this, 'register_section']);
        add_action('elementor/element/container/element_pack_image_parallax_section/before_section_end', [$this, 'register_controls'], 10, 2);
        add_action('elementor/frontend/container/before_render', [$this, 'section_parallax_before_render'], 10, 1);

        // Add section for settings
        add_action('elementor/element/section/section_advanced/after_section_end', [$this, 'register_section']);

        add_action('elementor/element/section/element_pack_image_parallax_section/before_section_end', [$this, 'register_controls'], 10, 2);

        //add_action('elementor/element/after_section_end', [$this, 'register_controls_parallax'], 10, 3);
        add_action('elementor/frontend/section/before_render', [$this, 'section_parallax_before_render'], 10, 1);
        add_action('elementor/preview/enqueue_scripts', [$this, 'enqueue_scripts']);
    }
}
