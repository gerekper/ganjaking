<?php

namespace DynamicContentForElementor\Widgets;

use Elementor\Repeater;
use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use DynamicContentForElementor\Helper;
if (!\defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
class AnimatedText extends \DynamicContentForElementor\Widgets\WidgetPrototype
{
    public function get_script_depends()
    {
        return ['dce-gsap-lib', 'dce-splitText-lib', 'dce-animatetext'];
    }
    public function get_style_depends()
    {
        return ['dce-animatetext'];
    }
    /**
     * Register controls after check if this feature is only for admin
     *
     * @return void
     */
    protected function safe_register_controls()
    {
        $this->start_controls_section('section_animateText', ['label' => __('Animated Text', 'dynamic-content-for-elementor')]);
        $this->add_control('animatetext_splittype', ['label' => __('Type', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['chars' => __('Chars', 'dynamic-content-for-elementor'), 'words' => __('Words', 'dynamic-content-for-elementor'), 'lines' => __('Lines', 'dynamic-content-for-elementor')], 'frontend_available' => \true, 'default' => 'chars']);
        $this->add_control('animatetext_trigger', ['label' => __('Trigger', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['animation' => __('Animation', 'dynamic-content-for-elementor'), 'scroll' => __('Scroll', 'dynamic-content-for-elementor')], 'frontend_available' => \true, 'default' => 'animation', 'render_type' => 'template', 'prefix_class' => 'animatetext-trigger-']);
        $repeater = new Repeater();
        $repeater->start_controls_tabs('tabs_repeater');
        $repeater->start_controls_tab('tab_content', ['label' => __('Content', 'dynamic-content-for-elementor')]);
        $repeater->add_control('text_word', ['label' => __('Word', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::TEXTAREA, 'default' => '']);
        $repeater->end_controls_tab();
        $repeater->start_controls_tab('tab_style', ['label' => __('Style', 'dynamic-content-for-elementor')]);
        $repeater->add_control('color_item', ['label' => __('Text color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-animatetext{{CURRENT_ITEM}}' => 'color: {{VALUE}};']]);
        $repeater->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography_item', 'label' => 'Typography item', 'selector' => '{{WRAPPER}} .dce-animatetext{{CURRENT_ITEM}}']);
        $repeater->end_controls_tab();
        $repeater->end_controls_tabs();
        $this->add_control('words', ['label' => __('Text', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::REPEATER, 'default' => [['text_word' => __('Type any word', 'dynamic-content-for-elementor')]], 'separator' => 'after', 'frontend_available' => \true, 'fields' => $repeater->get_controls(), 'title_field' => '{{{ text_word }}}']);
        $this->add_control('animatetext_repeat', ['label' => __('Repeat', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::NUMBER, 'label_block' => \false, 'separator' => 'before', 'frontend_available' => \true, 'description' => __('Infinite: -1, repeat it once and hide it: 0', 'dynamic-content-for-elementor'), 'default' => -1, 'min' => -1, 'max' => 25, 'step' => 1]);
        $this->end_controls_section();
        $this->start_controls_section('section_animateText_in', ['label' => __('IN', 'dynamic-content-for-elementor')]);
        $this->add_control('animatetext_animationstyle_in', ['label' => __('Animation style', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['fading' => __('Fading', 'dynamic-content-for-elementor'), 'from_left' => __('From Left', 'dynamic-content-for-elementor'), 'from_right' => __('From Right', 'dynamic-content-for-elementor'), 'from_top' => __('From Top', 'dynamic-content-for-elementor'), 'from_bottom' => __('From Bottom', 'dynamic-content-for-elementor'), 'zoom_front' => __('Zoom Front', 'dynamic-content-for-elementor'), 'zoom_back' => __('Zoom Back', 'dynamic-content-for-elementor'), 'random_position' => __('Random position', 'dynamic-content-for-elementor')], 'frontend_available' => \true, 'default' => 'fading']);
        $this->add_control('animatetext_splitorigin_in', ['label' => __('Origin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['null' => ['title' => __('Start', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-center'], 'end' => ['title' => __('End', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right']], 'default' => 'null', 'frontend_available' => \true]);
        $this->add_control('speed_animation_in', ['label' => __('Speed', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0.7], 'range' => ['px' => ['min' => 0.2, 'max' => 5, 'step' => 0.1]], 'frontend_available' => \true]);
        $this->add_control('amount_speed_in', ['label' => __('Amount', 'dynamic-content-for-elementor'), 'description' => __('Negative values produce a contrary effect of origin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1], 'range' => ['px' => ['min' => -100, 'max' => 100, 'step' => 1]], 'frontend_available' => \true]);
        $this->add_control('delay_animation_in', ['label' => __('Delay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0], 'range' => ['px' => ['min' => 0, 'max' => 30, 'step' => 0.1]], 'frontend_available' => \true]);
        $this->add_control('animFrom_easing_in', ['label' => __('Easing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_gsap_ease(), 'default' => 'easeInOut', 'frontend_available' => \true, 'label_block' => \false]);
        $this->add_control('animFrom_easing_ease_in', ['label' => __('Equation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_gsap_timing_functions(), 'default' => 'Power3', 'frontend_available' => \true, 'label_block' => \false]);
        $this->end_controls_section();
        // ---------------------------------------------------- OUT
        $this->start_controls_section('section_animateText_out', ['label' => __('OUT', 'dynamic-content-for-elementor')]);
        $this->add_control('animatetext_animationstyle_out', ['label' => __('Animation style', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['fading' => __('Fading', 'dynamic-content-for-elementor'), 'to_left' => __('To Left', 'dynamic-content-for-elementor'), 'to_right' => __('To Right', 'dynamic-content-for-elementor'), 'to_top' => __('To Top', 'dynamic-content-for-elementor'), 'to_bottom' => __('To Bottom', 'dynamic-content-for-elementor'), 'zoom_front' => __('Zoom Front', 'dynamic-content-for-elementor'), 'zoom_back' => __('Zoom Back', 'dynamic-content-for-elementor'), 'random_position' => __('Random position', 'dynamic-content-for-elementor'), 'elastic' => __('Elastic', 'dynamic-content-for-elementor')], 'frontend_available' => \true, 'default' => 'fading']);
        $this->add_control('animatetext_splitorigin_out', ['label' => __('Origin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['null' => ['title' => __('Start', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-center'], 'end' => ['title' => __('End', 'dynamic-content-for-elementor'), 'icon' => 'eicon-h-align-right']], 'default' => 'null', 'frontend_available' => \true]);
        $this->add_control('speed_animation_out', ['label' => __('Speed', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 0.7], 'range' => ['px' => ['min' => 0.2, 'max' => 5, 'step' => 0.1]], 'frontend_available' => \true]);
        $this->add_control('amount_speed_out', ['label' => __('Amount', 'dynamic-content-for-elementor'), 'description' => __('Negative values produce a contrary effect of origin', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 1], 'range' => ['px' => ['min' => -100, 'max' => 100, 'step' => 1]], 'frontend_available' => \true]);
        $this->add_control('delay_animation_out', ['label' => __('Delay', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SLIDER, 'default' => ['size' => 3], 'range' => ['px' => ['min' => 0, 'max' => 30, 'step' => 0.1]], 'frontend_available' => \true]);
        $this->add_control('animFrom_easing_out', ['label' => __('Easing', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_gsap_ease(), 'default' => 'easeInOut', 'frontend_available' => \true, 'label_block' => \false]);
        $this->add_control('animFrom_easing_ease_out', ['label' => __('Equation', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => Helper::get_gsap_timing_functions(), 'default' => 'Power3', 'frontend_available' => \true, 'label_block' => \false]);
        $this->end_controls_section();
        $this->start_controls_section('section_style', ['label' => __('Animate Text', 'dynamic-content-for-elementor'), 'tab' => Controls_Manager::TAB_STYLE]);
        $this->add_responsive_control('animatetext_align', ['label' => __('Alignment', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::CHOOSE, 'options' => ['left' => ['title' => __('Left', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-left'], 'center' => ['title' => __('Center', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-center'], 'right' => ['title' => __('Right', 'dynamic-content-for-elementor'), 'icon' => 'fa fa-align-right']], 'render_type' => 'template', 'default' => 'left', 'selectors' => ['{{WRAPPER}} .dce-animatetext' => 'text-align: {{VALUE}};']]);
        $this->add_control('color', ['label' => __('Text Color', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::COLOR, 'selectors' => ['{{WRAPPER}} .dce-animatetext' => 'color: {{VALUE}};', '{{WRAPPER}} .dce-animatetext a' => 'color: {{VALUE}};']]);
        $this->add_group_control(Group_Control_Typography::get_type(), ['name' => 'typography', 'selector' => '{{WRAPPER}} .dce-animatetext']);
        $this->add_group_control(Group_Control_Text_Shadow::get_type(), ['name' => 'text_shadow', 'selector' => '{{WRAPPER}} .dce-animatetext']);
        $this->add_control('blend_mode', ['label' => __('Blend Mode', 'dynamic-content-for-elementor'), 'type' => Controls_Manager::SELECT, 'options' => ['' => __('Normal', 'dynamic-content-for-elementor'), 'multiply' => __('Multiply', 'dynamic-content-for-elementor'), 'screen' => __('Screen', 'dynamic-content-for-elementor'), 'overlay' => __('Overlay', 'dynamic-content-for-elementor'), 'darken' => __('Darken', 'dynamic-content-for-elementor'), 'lighten' => __('Lighten', 'dynamic-content-for-elementor'), 'color-dodge' => __('Color Dodge', 'dynamic-content-for-elementor'), 'saturation' => __('Saturation', 'dynamic-content-for-elementor'), 'color' => __('Color', 'dynamic-content-for-elementor'), 'difference' => __('Difference', 'dynamic-content-for-elementor'), 'exclusion' => __('Exclusion', 'dynamic-content-for-elementor'), 'hue' => __('Hue', 'dynamic-content-for-elementor'), 'luminosity' => __('Luminosity', 'dynamic-content-for-elementor')], 'selectors' => ['{{WRAPPER}} .dce-animatetext' => 'mix-blend-mode: {{VALUE}}'], 'separator' => 'before']);
        $this->end_controls_section();
    }
    protected function safe_render()
    {
        $settings = $this->get_settings_for_display();
        if (empty($settings)) {
            return;
        }
        $effect = $settings['animatetext_animationstyle_in'];
        if ($effect) {
            $effect = ' dce-animatetext-' . $effect;
        }
        $words = $settings['words'];
        echo '<div class="dce-animatetext' . $effect . '"></div>';
        echo '<div style="display:none;" class="testi-nascosti">';
        if (!empty($words)) {
            $counter_item = 0;
            foreach ($words as $key => $w) {
                echo '<div class="dce-animatetext-item dce-animatetext-item-' . $counter_item . $effect . '">';
                echo wp_kses_post($w['text_word']);
                $counter_item++;
                echo '</div>';
            }
        }
        echo '</div>';
    }
}
