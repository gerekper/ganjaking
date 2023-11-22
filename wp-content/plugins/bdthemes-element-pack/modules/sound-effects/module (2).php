<?php

namespace ElementPack\Modules\SoundEffects;

use Elementor\Controls_Manager;
use ElementPack\Base\Element_Pack_Module_Base;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

    public function __construct() {
        parent::__construct();
        $this->add_actions();
    }

    public function get_name() {
        return 'bdt-sound-effects';
    }

    public function register_section($element) {
        $element->start_controls_section(
            'section_element_pack_sound_effects_controls',
            [
                'tab'   => Controls_Manager::TAB_CONTENT,
                'label' => BDTEP_CP . esc_html__('Sound Effects', 'bdthemes-element-pack') . BDTEP_NC,
            ]
        );
        $element->end_controls_section();
    }


    public function register_controls($widget, $args) {

        $widget->add_control(
            'ep_sound_effects_active',
            [
                'label'              => esc_html__('Sound Effects', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::SWITCHER,
                'render_type'        => 'template',
                'frontend_available' => true,
            ]
        );

        $widget->add_control(
            'ep_sound_effects_select_type',
            [
                'label'              => esc_html__('Element Type', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::SELECT,
                'options'            => [
                    'widget'     => esc_html__('Widget', 'bdthemes-element-pack'),
                    'anchor_tag' => esc_html__('Widget > Inner Anchor Tags', 'bdthemes-element-pack'),
                    'custom'     => esc_html__('Widget > Custom Selector', 'bdthemes-element-pack'),
                ],
                'default'            => 'widget',
                'render_type'        => 'template',
                'frontend_available' => true,
                'condition'          => [
                    'ep_sound_effects_active' => 'yes',
                ],
            ]
        );

        $widget->add_control(
            'ep_sound_effects_element_selector',
            [
                'label'              => esc_html__('Element Selector', 'bdthemes-prime-slider'),
                'type'               => Controls_Manager::TEXT,
                'default'            => 'my-header',
                'description'        => esc_html__("By clicking this scroll button, to which section in your page you want to go? Just write that's section ID here such 'my-header'.", 'bdthemes-prime-slider'),
                'frontend_available' => true,
                'condition'          => [
                    'ep_sound_effects_active'      => 'yes',
                    'ep_sound_effects_select_type' => 'custom',
                ],
            ]
        );

        $widget->add_control(
            'ep_sound_effects_event',
            [
                'label'              => esc_html__('Event', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::SELECT,
                'options'            => [
                    'hover' => esc_html__('Hover', 'bdthemes-element-pack'),
                    'click' => esc_html__('Click', 'bdthemes-element-pack'),
                ],
                'default'            => 'click',
                'render_type'        => 'template',
                'frontend_available' => true,
                'condition'          => [
                    'ep_sound_effects_active' => 'yes',
                ],
            ]
        );

        $widget->add_control(
            'ep_sound_effects_source',
            [
                'label'              => esc_html__('Source', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::SELECT,
                'default'            => 'bubble',
                'options'            => [
                    'bubble'       => esc_html__('Bubble', 'bdthemes-element-pack'),
                    'button-click' => esc_html__('Button Click', 'bdthemes-element-pack'),
                    'mouse-click'  => esc_html__('Mouse Click', 'bdthemes-element-pack'),
                    'mountain'     => esc_html__('Bell Ring', 'bdthemes-element-pack'),
                    'hosted_url'   => esc_html__('Local Audio (.ogg only)', 'bdthemes-element-pack'),
                ],
                'frontend_available' => true,
                'condition'          => [
                    'ep_sound_effects_active' => 'yes',
                ],
            ]
        );

        $widget->add_control(
            'ep_sound_effects_source_local_link',
            [
                'type'               => Controls_Manager::HIDDEN,
                'default'            => BDTEP_ASSETS_URL . 'sounds/',
                'frontend_available' => true,
                'condition'          => [
                    'ep_sound_effects_active' => 'yes',
                ],
            ]
        );

        $widget->add_control(
            'ep_sound_effects_hosted_url',
            [
                'label'              => esc_html__('Local Audio', 'bdthemes-element-pack'),
                'type'               => Controls_Manager::MEDIA,
                'dynamic'            => [
                    'active'     => true,
                    'categories' => [
                        TagsModule::POST_META_CATEGORY,
                        TagsModule::MEDIA_CATEGORY,
                    ],
                ],
                'media_type'         => 'audio/ogg',
                'default'            => [
                    'url' => BDTEP_ASSETS_URL . 'sounds/mouse-double-click.ogg',
                ],
                'frontend_available' => true,
                'description'        => 'Right now you can use only OGG sound file.',
                'condition'          => [
                    'ep_sound_effects_active' => 'yes',
                    'ep_sound_effects_source' => 'hosted_url'
                ]
            ]
        );

        $widget->add_control(
            'ep_sound_effects_hosted_url_mp3',
            [
                'label'              => esc_html__('Local Audio MP3 (Fallback)', 'bdthemes-element-pack') . BDTEP_NC,
                'type'               => Controls_Manager::MEDIA,
                'dynamic'            => [
                    'active'     => true,
                    'categories' => [
                        TagsModule::POST_META_CATEGORY,
                        TagsModule::MEDIA_CATEGORY,
                    ],
                ],
                'media_type'         => 'audio',
                'frontend_available' => true,
                'description'        => 'Please upload MP3 file for fallback.',
                'condition'          => [
                    'ep_sound_effects_active' => 'yes',
                    'ep_sound_effects_source' => 'hosted_url'
                ]
            ]
        );
    }

    public function should_script_enqueue($widget) {
        if ('yes' === $widget->get_settings_for_display('ep_sound_effects_active')) {
            wp_enqueue_script('ep-sound-effects');
        }
    }

    protected function add_actions() {
        add_action('elementor/element/common/_section_style/after_section_end', [$this, 'register_section']);
        add_action('elementor/element/common/section_element_pack_sound_effects_controls/before_section_end', [$this, 'register_controls'], 10, 2);

        // render scripts
        add_action('elementor/frontend/widget/before_render', [$this, 'should_script_enqueue']);
    }
}
