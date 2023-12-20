<?php

namespace Essential_Addons_Elementor\Pro\Traits;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

use \Elementor\Controls_Manager;
use \Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use \Elementor\Group_Control_Background;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography;
use \Elementor\Repeater;
use \Elementor\Utils;
use \Essential_Addons_Elementor\Elements\Login_Register;
use \Essential_Addons_Elementor\Pro\Classes\Helper;
use \Google_Client;
use \mysqli;

trait Extender
{
    public function add_progressbar_pro_layouts($options)
    {
        $options['layouts']['line_rainbow'] = __('Line Rainbow', 'essential-addons-elementor');
        $options['layouts']['circle_fill'] = __('Circle Fill', 'essential-addons-elementor');
        $options['layouts']['half_circle_fill'] = __('Half Circle Fill', 'essential-addons-elementor');
        $options['layouts']['box'] = __('Box', 'essential-addons-elementor');
        $options['conditions'] = [];

        return $options;
    }

    public function fancy_text_style_types($options)
    {
        $options['styles']['style-2'] = __('Style 2', 'essential-addons-elementor');
        $options['conditions'] = [];

        return $options;
    }

    public function ticker_options($options)
    {
        $options['options']['custom'] = __('Custom', 'essential-addons-elementor');
        $options['conditions'] = [];

        return $options;
    }

    public function data_table_sorting($obj)
    {
        $obj->add_control('eael_section_data_table_enabled', [
            'label' => __('Enable Table Sorting', 'essential-addons-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => esc_html__('Yes', 'essential-addons-elementor'),
            'label_off' => esc_html__('No', 'essential-addons-elementor'),
            'return_value' => 'true',
        ]);
    }

    public function ticker_custom_contents($obj)
    {
        /**
         * Content Ticker Custom Content Settings
         */
        $obj->start_controls_section('eael_section_ticker_custom_content_settings', [
            'label' => __('Custom Content Settings', 'essential-addons-elementor'),
            'condition' => [
                'eael_ticker_type' => 'custom',
            ],
        ]);

        $obj->add_control('eael_ticker_custom_contents', [
            'type' => Controls_Manager::REPEATER,
            'seperator' => 'before',
            'default' => [
                ['eael_ticker_custom_content' => 'Ticker Custom Content'],
            ],
            'fields' => [
                [
                    'name' => 'eael_ticker_custom_content',
                    'label' => esc_html__('Content', 'essential-addons-elementor'),
                    'type' => Controls_Manager::TEXT,
                    'dynamic' => [
                        'active' => true,
                    ],
                    'label_block' => true,
                    'default' => esc_html__('Ticker custom content', 'essential-addons-elementor'),
                ],
                [
                    'name' => 'eael_ticker_custom_content_link',
                    'label' => esc_html__('Button Link', 'essential-addons-elementor'),
                    'type' => Controls_Manager::URL,
                    'dynamic' => [
                        'active' => true,
                    ],
                    'label_block' => true,
                    'default' => [
                        'url' => '#',
                        'is_external' => '',
                    ],
                    'show_external' => true,
                ],
            ],
            'title_field' => '{{eael_ticker_custom_content}}',
        ]);

        $obj->end_controls_section();
    }

    public function content_ticker_custom_content($settings)
    {
        if ('custom' === $settings['eael_ticker_type']) {
            foreach ($settings['eael_ticker_custom_contents'] as $content):
                $target = $content['eael_ticker_custom_content_link']['is_external'] ? 'target="_blank"' : '';
                $nofollow = $content['eael_ticker_custom_content_link']['nofollow'] ? 'rel="nofollow"' : '';
                ?>
			                <div class="swiper-slide">
			                    <div class="ticker-content">
									<?php if (!empty($content['eael_ticker_custom_content_link']['url'])): ?>
			                            <a <?php echo $target; ?> <?php echo $nofollow; ?>
			                                    href="<?php echo esc_url($content['eael_ticker_custom_content_link']['url']); ?>"
			                                    class="ticker-content-link"><?php echo _e($content['eael_ticker_custom_content'], 'essential-addons-elementor') ?></a>
									<?php else: ?>
                            <p><?php echo _e($content['eael_ticker_custom_content'], 'essential-addons-elementor') ?></p>
						<?php endif;?>
                    </div>
                </div>
			<?php
endforeach;
        }
    }

    public function progress_bar_rainbow_class(array $wrap_classes, array $settings)
    {
        if ($settings['progress_bar_layout'] == 'line_rainbow') {
            $wrap_classes[] = 'eael-progressbar-line-rainbow';
        }

        return $wrap_classes;
    }

    public function progress_bar_circle_fill_class(array $wrap_classes, array $settings)
    {
        if ($settings['progress_bar_layout'] == 'circle_fill') {
            $wrap_classes[] = 'eael-progressbar-circle-fill';
        }

        return $wrap_classes;
    }

    public function progressbar_half_circle_wrap_class(array $wrap_classes, array $settings)
    {
        if ($settings['progress_bar_layout'] == 'half_circle_fill') {
            $wrap_classes[] = 'eael-progressbar-half-circle-fill';
        }

        return $wrap_classes;
    }

    public function progress_bar_box_control($obj)
    {
        /**
         * Style Tab: General(Box)
         */
        $obj->start_controls_section('progress_bar_section_style_general_box', [
            'label' => __('General', 'essential-addons-elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => [
                'progress_bar_layout' => 'box',
            ],
        ]);

        $obj->add_control('progress_bar_box_alignment', [
            'label' => __('Alignment', 'essential-addons-elementor'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'left' => [
                    'title' => __('Left', 'essential-addons-elementor'),
                    'icon' => 'eicon-text-align-left',
                ],
                'center' => [
                    'title' => __('Center', 'essential-addons-elementor'),
                    'icon' => 'eicon-text-align-center',
                ],
                'right' => [
                    'title' => __('Right', 'essential-addons-elementor'),
                    'icon' => 'eicon-text-align-right',
                ],
            ],
            'default' => 'center',
        ]);

        $obj->add_control('progress_bar_box_width', [
            'label' => __('Width', 'essential-addons-elementor'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => [
                'px' => [
                    'min' => 100,
                    'max' => 500,
                    'step' => 1,
                ],
            ],
            'default' => [
                'unit' => 'px',
                'size' => 140,
            ],
            'selectors' => [
                '{{WRAPPER}} .eael-progressbar-box' => 'width: {{SIZE}}{{UNIT}};',
            ],
            'separator' => 'before',
        ]);

        $obj->add_control('progress_bar_box_height', [
            'label' => __('Height', 'essential-addons-elementor'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => [
                'px' => [
                    'min' => 100,
                    'max' => 500,
                    'step' => 1,
                ],
            ],
            'default' => [
                'unit' => 'px',
                'size' => 200,
            ],
            'selectors' => [
                '{{WRAPPER}} .eael-progressbar-box' => 'height: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $obj->add_control('progress_bar_box_bg_color', [
            'label' => __('Background Color', 'essential-addons-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#fff',
            'selectors' => [
                '{{WRAPPER}} .eael-progressbar-box' => 'background-color: {{VALUE}}',
            ],
            'separator' => 'before',
        ]);

        $obj->add_control('progress_bar_box_fill_color', [
            'label' => __('Fill Color', 'essential-addons-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#000',
            'selectors' => [
                '{{WRAPPER}} .eael-progressbar-box-fill' => 'background-color: {{VALUE}}',
            ],
            'separator' => 'before',
        ]);

        $obj->add_control('progress_bar_box_stroke_width', [
            'label' => __('Stroke Width', 'essential-addons-elementor'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 100,
                    'step' => 1,
                ],
            ],
            'default' => [
                'unit' => 'px',
                'size' => 1,
            ],
            'selectors' => [
                '{{WRAPPER}} .eael-progressbar-box' => 'border-width: {{SIZE}}{{UNIT}}',
            ],
            'separator' => 'before',
        ]);

        $obj->add_control('progress_bar_box_stroke_color', [
            'label' => __('Stroke Color', 'essential-addons-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#eee',
            'selectors' => [
                '{{WRAPPER}} .eael-progressbar-box' => 'border-color: {{VALUE}}',
            ],
        ]);

        $obj->end_controls_section();
    }

    public function add_box_progress_bar_block(array $settings, $obj, array $wrap_classes)
    {
        if ($settings['progress_bar_layout'] == 'box') {
            $wrap_classes[] = 'eael-progressbar-box';

            $obj->add_render_attribute('eael-progressbar-box', [
                'class' => $wrap_classes,
                'data-layout' => $settings['progress_bar_layout'],
                'data-count' => $settings['progress_bar_value_type'] == 'static' ? $settings['progress_bar_value']['size'] : $settings['progress_bar_value_dynamic'],
                'data-duration' => $settings['progress_bar_animation_duration']['size'],
            ]);

            $obj->add_render_attribute('eael-progressbar-box-fill', [
                'class' => 'eael-progressbar-box-fill',
                'style' => '-webkit-transition-duration:' . $settings['progress_bar_animation_duration']['size'] . 'ms;-o-transition-duration:' . $settings['progress_bar_animation_duration']['size'] . 'ms;transition-duration:' . $settings['progress_bar_animation_duration']['size'] . 'ms;',
            ]);

            echo '<div class="eael-progressbar-box-container ' . $settings['progress_bar_box_alignment'] . '">
				<div ' . $obj->get_render_attribute_string('eael-progressbar-box') . '>
	                <div class="eael-progressbar-box-inner-content">
	                    ' . ($settings['progress_bar_title'] ? sprintf('<%1$s class="%2$s">', $settings['progress_bar_title_html_tag'], 'eael-progressbar-title') . $settings['progress_bar_title'] . sprintf('</%1$s>', $settings['progress_bar_title_html_tag']) : '') . '
	                    ' . ($settings['progress_bar_show_count'] === 'yes' ? '<span class="eael-progressbar-count-wrap"><span class="eael-progressbar-count">0</span><span class="postfix">' . __('%', 'essential-addons-for-elementor') . '</span></span>' : '') . '
	                </div>
	                <div ' . $obj->get_render_attribute_string('eael-progressbar-box-fill') . '></div>
	            </div>
            </div>';
        }
    }

    public function progressbar_general_style_condition($conditions)
    {
        return array_merge($conditions, [
            'line_rainbow',
        ]);
    }

    public function progressbar_line_fill_stripe_condition($conditions)
    {
        return array_merge($conditions, ['progress_bar_layout' => 'line']);
    }

    public function circle_style_general_condition($conditions)
    {
        return array_merge($conditions, [
            'circle_fill',
            'half_circle_fill',
        ]);
    }

    public function add_pricing_table_styles($options)
    {
        $options['styles']['style-3'] = esc_html__('Pricing Style 3', 'essential-addons-elementor');
        $options['styles']['style-4'] = esc_html__('Pricing Style 4', 'essential-addons-elementor');
        $options['styles']['style-5'] = esc_html__('Pricing Style 5', 'essential-addons-elementor');
        $options['conditions'] = [];

        return $options;
    }

    public function add_creative_button_controls($obj)
    {
        // Content Controls
        $obj->start_controls_section('eael_section_creative_button_content', [
            'label' => esc_html__('Button Content', 'essential-addons-elementor'),
        ]);

        $obj->start_controls_tabs('eael_creative_button_content_separation');

        $obj->start_controls_tab('button_primary_settings', [
            'label' => __('Primary', 'essential-addons-elementor'),
        ]);

        $obj->add_control('creative_button_text', [
            'label' => __('Button Text', 'essential-addons-elementor'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'label_block' => true,
            'default' => 'Click Me!',
            'placeholder' => __('Enter button text', 'essential-addons-elementor'),
            'title' => __('Enter button text here', 'essential-addons-elementor'),
        ]);

        $obj->add_control('eael_creative_button_icon_new', [
            'label' => esc_html__('Icon', 'essential-addons-elementor'),
            'type' => Controls_Manager::ICONS,
            'fa4compatibility' => 'eael_creative_button_icon',
            'condition' => [
                'creative_button_effect!' => ['eael-creative-button--tamaya'],
            ],
        ]);

        $obj->add_control('eael_creative_button_icon_alignment', [
            'label' => esc_html__('Icon Position', 'essential-addons-elementor'),
            'type' => Controls_Manager::SELECT,
            'default' => 'left',
            'options' => [
                'left' => esc_html__('Before', 'essential-addons-elementor'),
                'right' => esc_html__('After', 'essential-addons-elementor'),
            ],
            'condition' => [
                'creative_button_effect!' => ['eael-creative-button--tamaya'],
            ],
        ]);

        $obj->add_control('eael_creative_button_icon_indent', [
            'label' => esc_html__('Icon Spacing', 'essential-addons-elementor'),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .eael-creative-button-icon-right' => 'margin-left: {{SIZE}}px;',
                '{{WRAPPER}} .eael-creative-button-icon-left' => 'margin-right: {{SIZE}}px;',
                '{{WRAPPER}} .eael-creative-button--shikoba i' => 'left: {{SIZE}}%;',
            ],
            'condition' => [
                'creative_button_effect!' => ['eael-creative-button--tamaya'],
            ],
        ]);

        $obj->end_controls_tab();

        $obj->start_controls_tab('button_secondary_settings', [
            'label' => __('Secondary', 'essential-addons-elementor'),
        ]);

        $obj->add_control('creative_button_secondary_text', [
            'label' => __('Button Secondary Text', 'essential-addons-elementor'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'label_block' => true,
            'default' => 'Go!',
            'placeholder' => __('Enter button secondary text', 'essential-addons-elementor'),
            'title' => __('Enter button secondary text here', 'essential-addons-elementor'),
        ]);

        $obj->end_controls_tab();

        $obj->end_controls_tabs();

        $obj->add_control('creative_button_link_url', [
            'label' => esc_html__('Link URL', 'essential-addons-elementor'),
            'type' => Controls_Manager::URL,
            'dynamic' => [
                'active' => true,
            ],
            'label_block' => true,
            'default' => [
                'url' => '#',
                'is_external' => '',
            ],
            'show_external' => true,
        ]);

        $obj->end_controls_section();
    }

    public function add_creative_button_style_pro_controls($obj)
    {
        $obj->add_control('creative_button_effect', [
            'label' => esc_html__('Set Button Effect', 'essential-addons-elementor'),
            'type' => Controls_Manager::SELECT,
            'default' => 'eael-creative-button--default',
            'options' => [
                'eael-creative-button--default' => esc_html__('Default', 'essential-addons-elementor'),
                'eael-creative-button--winona' => esc_html__('Winona', 'essential-addons-elementor'),
                'eael-creative-button--ujarak' => esc_html__('Ujarak', 'essential-addons-elementor'),
                'eael-creative-button--wayra' => esc_html__('Wayra', 'essential-addons-elementor'),
                'eael-creative-button--tamaya' => esc_html__('Tamaya', 'essential-addons-elementor'),
                'eael-creative-button--rayen' => esc_html__('Rayen', 'essential-addons-elementor'),
                'eael-creative-button--pipaluk' => esc_html__('Pipaluk', 'essential-addons-elementor'),
                'eael-creative-button--moema' => esc_html__('Moema', 'essential-addons-elementor'),
                'eael-creative-button--wave' => esc_html__('Wave', 'essential-addons-elementor'),
                'eael-creative-button--aylen' => esc_html__('Aylen', 'essential-addons-elementor'),
                'eael-creative-button--saqui' => esc_html__('Saqui', 'essential-addons-elementor'),
                'eael-creative-button--wapasha' => esc_html__('Wapasha', 'essential-addons-elementor'),
                'eael-creative-button--nuka' => esc_html__('Nuka', 'essential-addons-elementor'),
                'eael-creative-button--antiman' => esc_html__('Antiman', 'essential-addons-elementor'),
                'eael-creative-button--quidel' => esc_html__('Quidel', 'essential-addons-elementor'),
                'eael-creative-button--shikoba' => esc_html__('Shikoba', 'essential-addons-elementor'),
            ],
        ]);

        $obj->start_controls_tabs('eael_creative_button_typography_separation');

        $obj->start_controls_tab('button_primary_typography', [
            'label' => __('Primary', 'essential-addons-elementor'),
        ]);

        $obj->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'eael_creative_button_typography',
            'global' => [
	            'default' => Global_Typography::TYPOGRAPHY_PRIMARY
            ],
            'selector' => '{{WRAPPER}} .eael-creative-button .cretive-button-text',
        ]);

        $obj->add_responsive_control('eael_creative_button_icon_size', [
            'label' => esc_html__('Icon Size', 'essential-addons-elementor'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [
                'px',
                '%',
            ],
            'default' => [
                'size' => 30,
                'unit' => 'px',
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 500,
                    'step' => 1,
                ],
                '%' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .eael-creative-button i' => 'font-size: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .eael-creative-button svg' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $obj->end_controls_tab();

        $obj->start_controls_tab('button_secondary_typography', [
            'label' => __('Secondary', 'essential-addons-elementor'),
        ]);

        $obj->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'eael_creative_button_secondary_typography',
            'global' => [
	            'default' => Global_Typography::TYPOGRAPHY_PRIMARY
            ],
            'selector' => '{{WRAPPER}} .eael-creative-button--rayen::before, 
                            {{WRAPPER}} .eael-creative-button--winona::after, 
                            {{WRAPPER}} .eael-creative-button--tamaya .eael-creative-button--tamaya-secondary span,
                            {{WRAPPER}} .eael-creative-button.eael-creative-button--saqui::after',
        ]);

        $obj->end_controls_tab();

        $obj->end_controls_tabs();

        $obj->add_responsive_control('eael_creative_button_alignment', [
            'label' => esc_html__('Button Alignment', 'essential-addons-elementor'),
            'type' => Controls_Manager::CHOOSE,
            'label_block' => true,
            'options' => [
                'flex-start' => [
                    'title' => esc_html__('Left', 'essential-addons-elementor'),
                    'icon' => 'eicon-text-align-left',
                ],
                'center' => [
                    'title' => esc_html__('Center', 'essential-addons-elementor'),
                    'icon' => 'eicon-text-align-center',
                ],
                'flex-end' => [
                    'title' => esc_html__('Right', 'essential-addons-elementor'),
                    'icon' => 'eicon-text-align-right',
                ],
            ],
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .eael-creative-button-wrapper' => 'justify-content: {{VALUE}};',
            ],
        ]);

        $obj->add_responsive_control('eael_creative_button_width', [
            'label' => esc_html__('Width', 'essential-addons-elementor'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [
                'px',
                '%',
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 500,
                    'step' => 1,
                ],
                '%' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .eael-creative-button' => 'width: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $obj->add_responsive_control('eael_creative_button_padding', [
            'label' => esc_html__('Button Padding', 'essential-addons-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [
                'px',
                'em',
                '%',
            ],
            'selectors' => [
                '{{WRAPPER}} .eael-creative-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--winona::after' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--winona > .creative-button-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--tamaya::before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--rayen::before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--rayen > .creative-button-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--saqui::after' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $obj->add_control('use_gradient_background', [
            'label' => __('Use Gradient Background', 'essential-addons-for-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __('Show', 'essential-addons-for-elementor'),
            'label_off' => __('Hide', 'essential-addons-for-elementor'),
            'return_value' => 'yes',
            'default' => '',
        ]);

        $obj->start_controls_tabs('eael_creative_button_tabs');

        $obj->start_controls_tab('normal', ['label' => esc_html__('Normal', 'essential-addons-elementor')]);

        $obj->add_control('eael_creative_button_icon_color', [
            'label' => esc_html__('Icon Color', 'essential-addons-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .eael-creative-button i' => 'color: {{VALUE}};',
                '{{WRAPPER}} .eael-creative-button .creative-button-inner svg' => 'fill: {{VALUE}};',
            ],
        ]);

        $obj->add_control('eael_creative_button_text_color', [
            'label' => esc_html__('Text Color', 'essential-addons-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .eael-creative-button' => 'color: {{VALUE}};',
                '{{WRAPPER}} .eael-creative-button svg' => 'fill: {{VALUE}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--tamaya::before' => 'color: {{VALUE}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--tamaya::after' => 'color: {{VALUE}};',
            ],
        ]);

        $obj->add_group_control(Group_Control_Background::get_type(), [
            'name' => 'eael_creative_button_gradient_background',
            'types' => [
                'gradient',
                'classic',
            ],
            'selector' => '
                    {{WRAPPER}} .eael-creative-button,
                    {{WRAPPER}} .eael-creative-button.eael-creative-button--ujarak:hover,
                    {{WRAPPER}} .eael-creative-button.eael-creative-button--wayra:hover,
                    {{WRAPPER}} .eael-creative-button.eael-creative-button--tamaya::before,
                    {{WRAPPER}} .eael-creative-button.eael-creative-button--tamaya::after,
                    {{WRAPPER}} .eael-creative-button.eael-creative-button--rayen:hover,
                    {{WRAPPER}} .eael-creative-button.eael-creative-button--pipaluk::after,
                    {{WRAPPER}} .eael-creative-button.eael-creative-button--wave:hover,
                    {{WRAPPER}} .eael-creative-button.eael-creative-button--nuka::before,
                    {{WRAPPER}} .eael-creative-button.eael-creative-button--nuka::after,
                    {{WRAPPER}} .eael-creative-button.eael-creative-button--antiman::after,
                    {{WRAPPER}} .eael-creative-button.eael-creative-button--quidel::after
                ',
            'condition' => [
                'use_gradient_background' => 'yes',
            ],
        ]);

        $obj->add_control('eael_creative_button_background_color', [
            'label' => esc_html__('Background Color', 'essential-addons-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#333333',
            'selectors' => [
                '{{WRAPPER}} .eael-creative-button' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--ujarak:hover' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--wayra:hover' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--tamaya::before' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--tamaya::after' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--rayen:hover' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--pipaluk::after' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--wave:hover' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--aylen::before' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--nuka::before' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--nuka::after' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--antiman::after' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--quidel::after' => 'background-color: {{VALUE}};',
            ],
            'condition' => [
                'use_gradient_background' => '',
            ],
        ]);

        $obj->add_group_control(Group_Control_Border::get_type(), [
            'name' => 'eael_creative_button_border',
            'selector' => '{{WRAPPER}} .eael-creative-button',
            'condition' => [
                    'creative_button_effect!' => 'eael-creative-button--nuka'
            ]
        ]);

        $obj->add_control('eael_creative_button_border_radius', [
            'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .eael-creative-button' => 'border-radius: {{SIZE}}px;',
                '{{WRAPPER}} .eael-creative-button::before' => 'border-radius: {{SIZE}}px;',
                '{{WRAPPER}} .eael-creative-button::after' => 'border-radius: {{SIZE}}px;',
            ],
        ]);

        $obj->end_controls_tab();

        $obj->start_controls_tab('eael_creative_button_hover', ['label' => esc_html__('Hover', 'essential-addons-elementor')]);

	    $obj->add_control('eael_creative_button_hover_icon_color', [
		    'label' => esc_html__('Icon Color', 'essential-addons-elementor'),
		    'type' => Controls_Manager::COLOR,
		    'default' => '#ffffff',
		    'selectors' => [
			    '{{WRAPPER}} .eael-creative-button:hover i' => 'color: {{VALUE}};',
			    '{{WRAPPER}} .eael-creative-button:hover .creative-button-inner svg' => 'fill: {{VALUE}};',
		    ],
	    ]);

        $obj->add_control('eael_creative_button_hover_text_color', [
            'label' => esc_html__('Text Color', 'essential-addons-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .eael-creative-button:hover' => 'color: {{VALUE}};',
                '{{WRAPPER}} .eael-creative-button:hover svg' => 'fill: {{VALUE}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--winona::after' => 'color: {{VALUE}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--saqui::after' => 'color: {{VALUE}};',
            ],
        ]);

        $obj->add_group_control(Group_Control_Background::get_type(), [
            'name' => 'eael_creative_button_hover_gradient_background',
            'types' => [
                'gradient',
                'classic',
            ],
            'selector' => '
                    {{WRAPPER}} .eael-creative-button:hover,
                    {{WRAPPER}} .eael-creative-button.eael-creative-button--ujarak::before,
                    {{WRAPPER}} .eael-creative-button.eael-creative-button--wayra:hover::before,
                    {{WRAPPER}} .eael-creative-button.eael-creative-button--tamaya:hover,
                    {{WRAPPER}} .eael-creative-button.eael-creative-button--rayen::before,
                    {{WRAPPER}} .eael-creative-button.eael-creative-button--wave::before,
                    {{WRAPPER}} .eael-creative-button.eael-creative-button--wave:hover::before,
                    {{WRAPPER}} .eael-creative-button.eael-creative-button--aylen::after,
                    {{WRAPPER}} .eael-creative-button.eael-creative-button--saqui:hover,
                    {{WRAPPER}} .eael-creative-button.eael-creative-button--nuka:hover::after,
                    {{WRAPPER}} .eael-creative-button.eael-creative-button--quidel:hover::after
                ',
            'condition' => [
                'use_gradient_background' => 'yes',
            ],
        ]);

        $obj->add_control('eael_creative_button_hover_background_color', [
            'label' => esc_html__('Background Color', 'essential-addons-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#f54',
            'selectors' => [
                '{{WRAPPER}} .eael-creative-button:hover' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--ujarak::before' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--wayra:hover::before' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--tamaya:hover' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--rayen::before' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--wave::before' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--wave:hover::before' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--aylen::after' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--saqui:hover' => 'color: {{VALUE}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--nuka:hover::after' => 'background-color: {{VALUE}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--quidel:hover::after' => 'background-color: {{VALUE}};',
            ],
        ]);

        $obj->add_control('eael_creative_button_hover_border_color', [
            'label' => esc_html__('Border Color', 'essential-addons-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .eael-creative-button:hover' => 'border-color: {{VALUE}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--wapasha::before' => 'border-color: {{VALUE}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--antiman::before' => 'border-color: {{VALUE}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--pipaluk::before' => 'border-color: {{VALUE}};',
                '{{WRAPPER}} .eael-creative-button.eael-creative-button--quidel::before' => 'background-color: {{VALUE}};',
            ],
        ]);

        $obj->end_controls_tab();

        $obj->end_controls_tabs();
    }

    public function pricing_table_subtitle_field($options)
    {
        return array_merge($options, [
            'style-3',
            'style-4',
            'style-5',
        ]);
    }

    public function pricing_table_icon_support($options)
    {
        return array_merge($options, ['style-5']);
    }

    public function pricing_table_header_radius_support($options)
    {
        return array_merge($options, ['style-5']);
    }

    public function pricing_table_header_background_support($options)
    {
        return array_merge($options, ['style-5']);
    }

    public function pricing_table_header_image_control($obj)
    {
        /**
         * Condition: 'eael_pricing_table_style' => 'style-4'
         */
        $obj->add_control('eael_pricing_table_style_4_image', [
            'label' => esc_html__('Header Image', 'essential-addons-elementor'),
            'type' => Controls_Manager::MEDIA,
            'default' => [
                'url' => Utils::get_placeholder_image_src(),
            ],
            'selectors' => [
                '{{WRAPPER}} .eael-pricing-image' => 'background-image: url({{URL}});',
            ],
            'condition' => [
                'eael_pricing_table_style' => 'style-4',
            ],
            'ai' => [
                'active' => false,
            ],
        ]);
    }

    public function pricing_table_style_2_currency_position($obj)
    {
        /**
         * Condition: 'eael_pricing_table_style' => 'style-3'
         */
        $obj->add_control('eael_pricing_table_style_3_price_position', [
            'label' => esc_html__('Pricing Position', 'essential-addons-elementor'),
            'type' => Controls_Manager::SELECT,
            'default' => 'bottom',
            'label_block' => false,
            'options' => [
                'top' => esc_html__('On Top', 'essential-addons-elementor'),
                'bottom' => esc_html__('At Bottom', 'essential-addons-elementor'),
            ],
            'condition' => [
                'eael_pricing_table_style' => 'style-3',
            ],
        ]);
    }

    public function pricing_table_style_five_settings_control($obj)
    {
        $obj->add_control('eael_pricing_table_style_five_icon_and_title_style', [
            'label' => esc_html__('Icon Beside Title', 'essential-addons-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __('Yes', 'essential-addons-elementor'),
            'label_off' => __('No', 'essential-addons-elementor'),
            'return_value' => 'yes',
            'condition' => [
                'eael_pricing_table_style' => 'style-5',
            ],
        ]);
        $obj->add_control('eael_pricing_table_style_five_header_layout', [
            'label' => esc_html__('Header Layout', 'essential-addons-elementor'),
            'type' => Controls_Manager::SELECT,
            'default' => 'one',
            'options' => [
                'one' => __('Layout 1', 'essential-addons-elementor'),
                'two' => __('Layout 2', 'essential-addons-elementor'),
            ],
            'condition' => [
                'eael_pricing_table_style' => 'style-5',
            ],
        ]);
    }

    public function pricing_table_style_header_layout_two($obj)
    {
        $obj->start_controls_section('eael_pricing_table_style_five_section', [
            'label' => __('Header Layout Two', 'essential-addons-elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => [
                'eael_pricing_table_style_five_header_layout' => 'two',
            ],
        ]);

        $obj->add_control('eael_pricing_table_style_five_price_style_padding', [
            'label' => __('Padding', 'essential-addons-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [
                'px',
                '%',
                'em',
            ],
            'selectors' => [
                '{{WRAPPER}} .eael-pricing.style-5 .eael-pricing-item .eael-pricing-image' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => [
                'eael_pricing_table_style' => 'style-5',
            ],
        ]);
        $obj->add_group_control(Group_Control_Background::get_type(), [
            'name' => 'eael_pricing_table_style_five_price_style_background',
            'label' => __('Background', 'essential-addons-elementor'),
            'types' => [
                'classic',
                'gradient',
            ],
            'selector' => '{{WRAPPER}} .eael-pricing.style-5 .eael-pricing-item .eael-pricing-image',
            'condition' => [
                'eael_pricing_table_style' => 'style-5',
            ],
        ]);
        $obj->end_controls_section();
    }

    public function add_pricing_table_pro_styles($settings, $obj, $pricing, $target, $nofollow, $featured_class)
    {
        $settings = $obj->get_settings();
        $button_text = $obj->get_settings_for_display('eael_pricing_table_btn');
        $inline_style = ($settings['eael_pricing_table_featured_styles'] === 'ribbon-4' && 'yes' === $settings['eael_pricing_table_featured'] ? ' style="overflow: hidden;"' : '');
        if ('style-3' === $settings['eael_pricing_table_style']): ?>
            <div class="eael-pricing style-3"<?php echo $inline_style; ?>>
                <div class="eael-pricing-item <?php echo esc_attr($featured_class); ?>">
					<?php if ('top' === $settings['eael_pricing_table_style_3_price_position']): ?>
                        <div class="eael-pricing-tag on-top">
                            <span class="price-tag"><?php echo $pricing; ?></span>
                            <span class="price-period"><?php echo $settings['eael_pricing_table_period_separator']; ?><?php echo $settings['eael_pricing_table_price_period']; ?></span>
                        </div>
					<?php endif;?>
                    <div class="header">
                        <h2 class="title"><?php echo $settings['eael_pricing_table_title']; ?></h2>
                        <span class="subtitle"><?php echo $settings['eael_pricing_table_sub_title']; ?></span>
                    </div>
                    <div class="body">
						<?php $obj->render_feature_list($settings, $obj);?>
                    </div>
					<?php if ('bottom' === $settings['eael_pricing_table_style_3_price_position']): ?>
                        <div class="eael-pricing-tag">
                            <span class="price-tag"><?php echo $pricing; ?></span>
                            <span class="price-period"><?php echo $settings['eael_pricing_table_period_separator']; ?><?php echo $settings['eael_pricing_table_price_period']; ?></span>
                        </div>
					<?php endif;?>
                    <div class="footer">
                        <a href="<?php echo esc_url($settings['eael_pricing_table_btn_link']['url']); ?>" <?php echo $target; ?> <?php echo $nofollow; ?>
                           class="eael-pricing-button">
							<?php if ('left' == $settings['eael_pricing_table_button_icon_alignment']): ?>
								<?php if (empty($settings['eael_pricing_table_button_icon']) || isset($settings['__fa4_migrated']['eael_pricing_table_button_icon_new'])) {?>
                                    <i class="<?php echo esc_attr($settings['eael_pricing_table_button_icon_new']['value']); ?> fa-icon-left"></i>
								<?php } else {?>
                                    <i class="<?php echo esc_attr($settings['eael_pricing_table_button_icon']); ?> fa-icon-left"></i>
								<?php }?>
								<?php echo $button_text; ?>
							<?php elseif ('right' == $settings['eael_pricing_table_button_icon_alignment']): ?>
								<?php echo $button_text; ?>
								<?php if (empty($settings['eael_pricing_table_button_icon']) || isset($settings['__fa4_migrated']['eael_pricing_table_button_icon_new'])) {?>
                                    <i class="<?php echo esc_attr($settings['eael_pricing_table_button_icon_new']['value']); ?> fa-icon-right"></i>
								<?php } else {?>
                                    <i class="<?php echo esc_attr($obj->get_settings('eael_pricing_table_button_icon')); ?> fa-icon-right"></i>
								<?php }?>
							<?php endif;?>
                        </a>
                    </div>
                </div>
            </div>
		<?php endif;
        if ('style-4' === $settings['eael_pricing_table_style']): ?>
            <div class="eael-pricing style-4"<?php echo $inline_style; ?>>
                <div class="eael-pricing-item <?php echo esc_attr($featured_class); ?>">
                    <div class="eael-pricing-image">
                        <div class="eael-pricing-tag">
                            <span class="price-tag"><?php echo $pricing; ?></span>
                            <span class="price-period"><?php echo $settings['eael_pricing_table_period_separator']; ?><?php echo $settings['eael_pricing_table_price_period']; ?></span>
                        </div>
                    </div>
                    <div class="header">
                        <h2 class="title"><?php echo $settings['eael_pricing_table_title']; ?></h2>
                        <span class="subtitle"><?php echo $settings['eael_pricing_table_sub_title']; ?></span>
                    </div>
                    <div class="body">
						<?php $obj->render_feature_list($settings, $obj);?>
                    </div>
                    <div class="footer">
                        <a href="<?php echo esc_url($settings['eael_pricing_table_btn_link']['url']); ?>" <?php echo $target; ?> <?php echo $nofollow; ?>
                           class="eael-pricing-button">
							<?php if ('left' == $settings['eael_pricing_table_button_icon_alignment']): ?>
								<?php if (empty($settings['eael_pricing_table_button_icon']) || isset($settings['__fa4_migrated']['eael_pricing_table_button_icon_new'])) {?>
                                    <i class="<?php echo esc_attr($settings['eael_pricing_table_button_icon_new']['value']); ?> fa-icon-left"></i>
								<?php } else {?>
                                    <i class="<?php echo esc_attr($settings['eael_pricing_table_button_icon']); ?> fa-icon-left"></i>
								<?php }?>
								<?php echo $button_text; ?>
							<?php elseif ('right' == $settings['eael_pricing_table_button_icon_alignment']): ?>
								<?php echo $button_text; ?>
								<?php if (empty($settings['eael_pricing_table_button_icon']) || isset($settings['__fa4_migrated']['eael_pricing_table_button_icon_new'])) {?>
                                    <i class="<?php echo esc_attr($settings['eael_pricing_table_button_icon_new']['value']); ?> fa-icon-right"></i>
								<?php } else {?>
                                    <i class="<?php echo esc_attr($settings['eael_pricing_table_button_icon']); ?> fa-icon-right"></i>
								<?php }?>
							<?php endif;?>
                        </a>
                    </div>
                </div>
            </div>
		<?php endif;
        if ('style-5' === $settings['eael_pricing_table_style']): ?>
            <div class="eael-pricing style-5"<?php echo $inline_style; ?>>
                <div class="eael-pricing-item <?php echo ($settings['eael_pricing_table_style_five_header_layout'] !== 'two' ? esc_attr($featured_class) : ''); ?>">
                    <div class="header">
						<?php
if (!empty($settings['eael_pricing_table_style_2_icon_new']['value'])):
        ?>
                            <div class="eael-pricing-icon<?php print($settings['eael_pricing_table_style_five_icon_and_title_style'] === 'yes' ? ' inline' : '');?>">
                            <span class="icon"
                                  style="background:<?php if ('yes' != $settings['eael_pricing_table_icon_bg_show']): echo 'none';endif;?>;">
                                <?php if (empty($settings['eael_pricing_table_style_2_icon']) || isset($settings['__fa4_migrated']['eael_pricing_table_style_2_icon_new'])) {?>
	                                <?php if (isset($settings['eael_pricing_table_style_2_icon_new']['value']['url'])): ?>
                                        <img src="<?php echo esc_attr($settings['eael_pricing_table_style_2_icon_new']['value']['url']); ?>"
                                             alt="<?php echo esc_attr(get_post_meta($settings['eael_pricing_table_style_2_icon_new']['value']['id'], '_wp_attachment_image_alt', true)); ?>"/>
	                                <?php else: ?>
                                        <i class="<?php echo esc_attr($settings['eael_pricing_table_style_2_icon_new']['value']); ?>"></i>
	                                <?php endif;?>
                                <?php } else {?>
                                    <i class="<?php echo esc_attr($settings['eael_pricing_table_style_2_icon']); ?>"></i>
                                <?php }?>
                            </span>
                            </div>
						<?php
endif; // icon
        if (!empty($settings['eael_pricing_table_title'])):
        ?>
                            <h2 class="title<?php print($settings['eael_pricing_table_style_five_icon_and_title_style'] === 'yes' ? ' inline' : '');?>"><?php echo $settings['eael_pricing_table_title']; ?></h2>
						<?php
endif; // title
        if (!empty($settings['eael_pricing_table_sub_title'])):
        ?>
                            <span class="subtitle"><?php echo $settings['eael_pricing_table_sub_title']; ?></span>
						<?php
endif;
        if ($settings['eael_pricing_table_style_five_header_layout'] == 'one'):
        ?>
                            <div class="eael-pricing-image">
                                <div class="eael-pricing-tag">
                                    <span class="price-tag"><?php echo $pricing; ?></span>
                                    <span class="price-period"><?php echo $settings['eael_pricing_table_period_separator']; ?><?php echo $settings['eael_pricing_table_price_period']; ?></span>
                                </div>
                            </div>
						<?php
endif;
        ?>
                    </div>
					<?php
if ($settings['eael_pricing_table_style_five_header_layout'] == 'two'):
        ?>
                        <div class="eael-pricing-image <?php echo esc_attr($featured_class); ?>"<?php echo $inline_style; ?>>
                            <div class="eael-pricing-tag">
                                <span class="price-tag"><?php echo $pricing; ?></span>
                                <span class="price-period"><?php echo $settings['eael_pricing_table_period_separator']; ?><?php echo $settings['eael_pricing_table_price_period']; ?></span>
                            </div>
                        </div>
					<?php
endif;
        ?>
                    <div class="body">
						<?php $obj->render_feature_list($settings, $obj);?>
                    </div>
                    <div class="footer">
                        <a href="<?php echo esc_url($settings['eael_pricing_table_btn_link']['url']); ?>" <?php echo $target; ?> <?php echo $nofollow; ?>
                           class="eael-pricing-button">
							<?php if ('left' == $settings['eael_pricing_table_button_icon_alignment']): ?>
								<?php if (empty($settings['eael_pricing_table_button_icon']) || isset($settings['__fa4_migrated']['eael_pricing_table_button_icon_new'])) {?>
                                    <i class="<?php echo esc_attr($settings['eael_pricing_table_button_icon_new']['value']); ?> fa-icon-left"></i>
								<?php } else {?>
                                    <i class="<?php echo esc_attr($settings['eael_pricing_table_button_icon']); ?> fa-icon-left"></i>
								<?php }?>
								<?php echo $button_text; ?>
							<?php elseif ('right' == $settings['eael_pricing_table_button_icon_alignment']): ?>
								<?php echo $button_text; ?>
								<?php if (empty($settings['eael_pricing_table_button_icon']) || isset($settings['__fa4_migrated']['eael_pricing_table_button_icon_new'])) {?>
                                    <i class="<?php echo esc_attr($settings['eael_pricing_table_button_icon_new']['value']); ?> fa-icon-right"></i>
								<?php } else {?>
                                    <i class="<?php echo esc_attr($settings['eael_pricing_table_button_icon']); ?> fa-icon-right"></i>
								<?php }?>
							<?php endif;?>
                        </a>
                    </div>
                </div>
            </div>
		<?php endif;
    }

    public function add_admin_licnes_markup_html()
    {
        do_action('eael_licensing');
    }

    public function add_eael_premium_support_link()
    {
        ?>
        <p><?php echo _e('Stuck with something? Get help from live chat or support ticket.', 'essential-addons-elementor'); ?></p>
        <a href="https://wpdeveloper.com"
           class="ea-button"
           target="_blank"><?php echo _e('Initiate a Chat', 'essential-addons-elementor'); ?></a>
		<?php
}

    public function add_eael_additional_support_links()
    {
        ?>
        <div class="eael-admin-block eael-admin-block-community">
            <header class="eael-admin-block-header">
                <div class="eael-admin-block-header-icon">
                    <img src="<?php echo EAEL_PRO_PLUGIN_URL . 'assets/admin/images/icon-join-community.svg'; ?>"
                         alt="join-essential-addons-community">
                </div>
                <h4 class="eael-admin-title">
                    Join the Community</h4>
            </header>
            <div class="eael-admin-block-content">
                <p><?php echo _e('Join the Facebook community and discuss with fellow developers and users. Best way to connect with people and get feedback on your projects.', 'essential-addons-elementor'); ?></p>

                <a href="https://www.facebook.com/groups/essentialaddons"
                   class="review-flexia ea-button"
                   target="_blank"><?php echo _e('Join Facebook Community', 'essential-addons-elementor'); ?></a>
            </div>
        </div>
		<?php
}

    public function add_manage_linces_action_link()
    {
        $link_text = __('Manage License', 'essential-addons-elementor');
        printf('<a class="eael-button button__themeColor" href="https://wpdeveloper.com/account" target="_blank">%s</a>', $link_text);
    }

    public function team_member_presets_condition($options)
    {
        return [];
    }

    public function add_team_member_circle_presets($obj)
    {
        $obj->add_responsive_control(
            'eael_team_members_image_height',
            [
                'label' => esc_html__('Image Height', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'em' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                ],
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .eael-team-item figure img' => 'height:{{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'eael_team_members_preset!' => 'eael-team-members-circle',
                ],
            ]
        );

        $obj->add_responsive_control(
            'eael_team_members_circle_image_width',
            [
                'label' => esc_html__('Image Width', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 150,
                    'unit' => 'px',
                ],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                ],
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .eael-team-item.eael-team-members-circle figure img' => 'width:{{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'eael_team_members_preset' => 'eael-team-members-circle',
                ],
            ]
        );

        $obj->add_responsive_control(
            'eael_team_members_circle_image_height',
            [
                'label' => esc_html__('Image Height', 'essential-addons-elementor'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 150,
                    'unit' => 'px',
                ],
                'range' => [
                    '%' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                ],
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .eael-team-item.eael-team-members-circle figure img' => 'height:{{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'eael_team_members_preset' => 'eael-team-members-circle',
                ],
            ]
        );
    }

    public function add_team_member_social_bottom_markup($settings)
    {
        ?>
        <p class="eael-team-text"><?php echo $settings['eael_team_member_description']; ?></p>
		<?php if (!empty($settings['eael_team_member_enable_social_profiles'])): ?>
            <ul class="eael-team-member-social-profiles">
				<?php foreach ($settings['eael_team_member_social_profile_links'] as $item): ?>
					<?php $icon_migrated = isset($item['__fa4_migrated']['social_new']);
        $icon_is_new = empty($item['social']);?>
					<?php if (!empty($item['social']) || !empty($item['social_new'])): ?>
						<?php $target = $item['link']['is_external'] ? ' target="_blank"' : '';?>
                        <li class="eael-team-member-social-link">
                            <a href="<?php echo esc_attr($item['link']['url']); ?>" <?php echo $target; ?>>
								<?php if ($icon_is_new || $icon_migrated) {?>
									<?php if (isset($item['social_new']['value']['url'])): ?>
                                        <img src="<?php echo esc_attr($item['social_new']['value']['url']); ?>"
                                             alt="<?php echo esc_attr(get_post_meta($item['social_new']['value']['id'], '_wp_attachment_image_alt', true)); ?>"/>
									<?php else: ?>
                                        <i class="<?php echo esc_attr($item['social_new']['value']); ?>"></i>
									<?php endif;?>
								<?php } else {?>
                                    <i class="<?php echo esc_attr($item['social']); ?>"></i>
								<?php }?>
                            </a>
                        </li>
					<?php endif;?>
				<?php endforeach;?>
            </ul>
		<?php endif;
    }

    public function add_team_member_social_right_markup($settings)
    {
        ?>
		<?php if (!empty($settings['eael_team_member_enable_social_profiles'])): ?>
            <ul class="eael-team-member-social-profiles">
				<?php foreach ($settings['eael_team_member_social_profile_links'] as $item): ?>
					<?php if (!empty($item['social_new'])): ?>
						<?php $target = $item['link']['is_external'] ? ' target="_blank"' : '';?>
                        <li class="eael-team-member-social-link">
                            <a href="<?php echo esc_attr($item['link']['url']); ?>"<?php echo $target; ?>><i
                                        class="<?php echo esc_attr($item['social_new']['value']); ?>"></i></a>
                        </li>
					<?php endif;?>
				<?php endforeach;?>
            </ul>
		<?php endif;
    }

    // Advanced Data Table
    public function advanced_data_table_source_control($wb)
    {
        // database
        $wb->add_control('ea_adv_data_table_source_database_query_type', [
            'label' => esc_html__('Select Query', 'essential-addons-elementor'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'table' => 'Table',
                'query' => 'MySQL Query',
            ],
            'default' => 'table',
            'condition' => [
                'ea_adv_data_table_source' => 'database',
            ],
        ]);

        $wb->add_control('ea_adv_data_table_source_database_table', [
            'label' => esc_html__('Select Table', 'essential-addons-elementor'),
            'type' => Controls_Manager::SELECT,
            'options' => Helper::list_db_tables(),
            'condition' => [
                'ea_adv_data_table_source' => 'database',
                'ea_adv_data_table_source_database_query_type' => 'table',
            ],
        ]);

        $wb->add_control('ea_adv_data_table_source_database_query', [
            'label' => esc_html__('MySQL Query', 'essential-addons-elementor'),
            'type' => Controls_Manager::TEXTAREA,
            'placeholder' => 'e.g. SELECT * FROM `table`',
            'condition' => [
                'ea_adv_data_table_source' => 'database',
                'ea_adv_data_table_source_database_query_type' => 'query',
            ],
        ]);

        // remote
        $wb->add_control('ea_adv_data_table_source_remote_host', [
            'label' => __('Host', 'essential-addons-elementor'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'condition' => [
                'ea_adv_data_table_source' => 'remote',
                'ea_adv_data_table_source_remote_connected' => false,
            ],
        ]);

        $wb->add_control('ea_adv_data_table_source_remote_username', [
            'label' => __('Username', 'essential-addons-elementor'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'condition' => [
                'ea_adv_data_table_source' => 'remote',
                'ea_adv_data_table_source_remote_connected' => false,
            ],
        ]);

        $wb->add_control('ea_adv_data_table_source_remote_password', [
            'label' => __('Password', 'essential-addons-elementor'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'input_type' => 'password',
            'condition' => [
                'ea_adv_data_table_source' => 'remote',
                'ea_adv_data_table_source_remote_connected' => false,
            ],
        ]);

        $wb->add_control('ea_adv_data_table_source_remote_database', [
            'label' => __('Database', 'essential-addons-elementor'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'condition' => [
                'ea_adv_data_table_source' => 'remote',
                'ea_adv_data_table_source_remote_connected' => false,
            ],
        ]);

        $wb->add_control('ea_adv_data_table_source_remote_connect', [
            'label' => __('Connect DB', 'essential-addons-for-elementor'),
            'type' => Controls_Manager::BUTTON,
            'text' => __('Connect', 'essential-addons-elementor'),
            'event' => 'ea:advTable:connect',
            'condition' => [
                'ea_adv_data_table_source' => 'remote',
                'ea_adv_data_table_source_remote_connected' => false,
            ],
        ]);

        $wb->add_control('ea_adv_data_table_source_remote_disconnect', [
            'label' => __('Disconnect DB', 'essential-addons-for-elementor'),
            'type' => Controls_Manager::BUTTON,
            'text' => __('Disconnect', 'essential-addons-elementor'),
            'event' => 'ea:advTable:disconnect',
            'condition' => [
                'ea_adv_data_table_source' => 'remote',
                'ea_adv_data_table_source_remote_connected' => true,
            ],
        ]);

        $wb->add_control('ea_adv_data_table_source_remote_connected', [
            'type' => Controls_Manager::HIDDEN,
            'default' => false,
        ]);

        $wb->add_control('ea_adv_data_table_source_remote_tables', [
            'type' => Controls_Manager::HIDDEN,
            'default' => [],
        ]);

        $wb->add_control('ea_adv_data_table_dynamic_th_width', [
            'type' => Controls_Manager::HIDDEN,
            'default' => [],
        ]);

        $wb->add_control('ea_adv_data_table_source_remote_query_type', [
            'label' => esc_html__('Select Query', 'essential-addons-elementor'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'table' => 'Table',
                'query' => 'MySQL Query',
            ],
            'default' => 'table',
            'condition' => [
                'ea_adv_data_table_source' => 'remote',
                'ea_adv_data_table_source_remote_connected' => true,
            ],
        ]);

        $wb->add_control('ea_adv_data_table_source_remote_table', [
            'label' => esc_html__('Select Table', 'essential-addons-elementor'),
            'type' => Controls_Manager::SELECT,
            'options' => [],
            'condition' => [
                'ea_adv_data_table_source' => 'remote',
                'ea_adv_data_table_source_remote_connected' => true,
                'ea_adv_data_table_source_remote_query_type' => 'table',
            ],
        ]);

        $wb->add_control('ea_adv_data_table_source_remote_query', [
            'label' => esc_html__('MySQL Query', 'essential-addons-elementor'),
            'type' => Controls_Manager::TEXTAREA,
            'placeholder' => 'e.g. SELECT * FROM `table`',
            'condition' => [
                'ea_adv_data_table_source' => 'remote',
                'ea_adv_data_table_source_remote_connected' => true,
                'ea_adv_data_table_source_remote_query_type' => 'query',
            ],
        ]);

        // google sheet
        $wb->add_control('ea_adv_data_table_source_google_api_key', [
            'label' => __('API Key', 'essential-addons-elementor'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'condition' => [
                'ea_adv_data_table_source' => 'google',
            ],
        ]);

        $wb->add_control('ea_adv_data_table_source_google_sheet_id', [
            'label' => __('Sheet ID', 'essential-addons-elementor'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'condition' => [
                'ea_adv_data_table_source' => 'google',
            ],
        ]);

        $wb->add_control('ea_adv_data_table_source_google_table_range', [
            'label' => __('Table Range', 'essential-addons-elementor'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'condition' => [
                'ea_adv_data_table_source' => 'google',
            ],
        ]);

        // tablepress
        if (apply_filters('eael/is_plugin_active', 'tablepress/tablepress.php')) {
            $wb->add_control('ea_adv_data_table_source_tablepress_table_id', [
                'label' => esc_html__('Table ID', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'options' => Helper::list_tablepress_tables(),
                'condition' => [
                    'ea_adv_data_table_source' => 'tablepress',
                ],
            ]);
        } else {
            $wb->add_control('ea_adv_data_table_tablepress_required', [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => __('<strong>TablePress</strong> is not installed/activated on your site. Please install and activate <a href="plugin-install.php?s=TablePress&tab=search&type=term" target="_blank">TablePress</a> first.', 'essential-addons-for-elementor'),
                'content_classes' => 'eael-warning',
                'condition' => [
                    'ea_adv_data_table_source' => 'tablepress',
                ],
            ]);
        }
    }

    public function event_calendar_source_control($obj)
    {
        if (apply_filters('eael/is_plugin_active', 'eventON/eventon.php')) {
            $obj->start_controls_section('eael_event_calendar_eventon_section', [
                'label' => __('EventON', 'essential-addons-for-elementor'),
                'condition' => [
                    'eael_event_calendar_type' => 'eventon',
                ],
            ]);

            $obj->add_control('eael_eventon_calendar_fetch', [
                'label' => __('Get Events', 'essential-addons-for-elementor'),
                'type' => Controls_Manager::SELECT,
                'label_block' => true,
                'default' => ['all'],
                'options' => [
                    'all' => __('All', 'essential-addons-for-elementor'),
                    'date_range' => __('Date Range', 'essential-addons-for-elementor'),
                ],
            ]);

            $obj->add_control('eael_eventon_calendar_start_date', [
                'label' => __('Start Date', 'essential-addons-for-elementor'),
                'type' => Controls_Manager::DATE_TIME,
                'default' => date('Y-m-d H:i', current_time('timestamp', 0)),
                'condition' => [
                    'eael_eventon_calendar_fetch' => 'date_range',
                ],
            ]);

            $obj->add_control('eael_eventon_calendar_end_date', [
                'label' => __('End Date', 'essential-addons-for-elementor'),
                'type' => Controls_Manager::DATE_TIME,
                'default' => date('Y-m-d H:i', strtotime("+6 months", current_time('timestamp', 0))),
                'condition' => [
                    'eael_eventon_calendar_fetch' => 'date_range',
                ],
            ]);

            $obj->add_control('eael_eventon_calendar_post_tag', [
                'label' => __('Event Tag', 'essential-addons-for-elementor'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'label_block' => true,
                'default' => [],
                'options' => Helper::get_tags_list([
                    'taxonomy' => 'post_tag',
                    'hide_empty' => false,
                ]),
            ]);

            $taxonomies = Helper::get_taxonomies_by_post(['object_type' => 'ajde_events']);

            unset($taxonomies['event_location'], $taxonomies['post_tag'], $taxonomies['event_organizer']);

            foreach ($taxonomies as $taxonomie) {
                $key = 'eael_eventon_calendar_' . $taxonomie;
                $obj->add_control($key, [
                    'label' => ucwords(str_replace('_', ' ', $taxonomie)),
                    'type' => Controls_Manager::SELECT2,
                    'multiple' => true,
                    'label_block' => true,
                    'default' => [],
                    'options' => Helper::get_tags_list([
                        'taxonomy' => $taxonomie,
                        'hide_empty' => false,
                    ]),
                ]);
            }

            $obj->add_control('eael_eventon_calendar_event_location', [
                'label' => __('Event Location', 'essential-addons-for-elementor'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'label_block' => true,
                'default' => [],
                'options' => Helper::get_tags_list([
                    'taxonomy' => 'event_location',
                    'hide_empty' => false,
                ]),
            ]);

            $obj->add_control('eael_eventon_calendar_event_organizer', [
                'label' => __('Event Organizer', 'essential-addons-for-elementor'),
                'type' => Controls_Manager::SELECT2,
                'multiple' => true,
                'label_block' => true,
                'default' => [],
                'options' => Helper::get_tags_list([
                    'taxonomy' => 'event_organizer',
                    'hide_empty' => false,
                ]),
            ]);

            $obj->add_control('eael_eventon_calendar_max_result', [
                'label' => __('Max Result', 'essential-addons-for-elementor'),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'default' => 100,
            ]);

            $obj->end_controls_section();
        }
    }

    public function event_calendar_activation_notice($obj)
    {
        if (!apply_filters('eael/is_plugin_active', 'eventON/eventon.php')) {
            $obj->add_control('eael_eventon_warning_text', [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => __('<strong>EventON</strong> is not installed/activated on your site. Please install and activate <a href="https://codecanyon.net/item/eventon-wordpress-event-calendar-plugin/1211017" target="_blank">EventON</a> first.', 'essential-addons-for-elementor'),
                'content_classes' => 'eael-warning',
                'condition' => [
                    'eael_event_calendar_type' => 'eventon',
                ],
            ]);
        }
    }

    public function advanced_data_table_database_integration($settings)
    {
        global $wpdb;

        $html = '';
        $results = [];

        // suppress error
        $wpdb->suppress_errors = true;

        // collect data
        if ($settings['ea_adv_data_table_source_database_query_type'] == 'table') {
            $table = $settings["ea_adv_data_table_source_database_table"];
            $results = $wpdb->get_results("SELECT * FROM $table", ARRAY_A);
        } else {
            if (empty($settings['ea_adv_data_table_source_database_query'])) {
                return;
            }

            if( !$this->eael_valid_select_query( $settings['ea_adv_data_table_source_database_query'] ) ){
                return $results;
            }

            $results = $wpdb->get_results($settings['ea_adv_data_table_source_database_query'], ARRAY_A);
        }

        if (is_wp_error($results)) {
            return $results->get_error_message();
        }

        if (!empty($results)) {
            $html .= '<thead><tr>';
            foreach (array_keys($results[0]) as $key => $th) {
                $style = isset($settings['ea_adv_data_table_dynamic_th_width']) && isset($settings['ea_adv_data_table_dynamic_th_width'][$key]) ? ' style="width:' . $settings['ea_adv_data_table_dynamic_th_width'][$key] . '"' : '';
                $html .= '<th' . $style . '>' . $th . '</th>';
            }
            $html .= '</tr></thead>';

            $html .= '<tbody>';
            foreach ($results as $tr) {
                $html .= '<tr>';
                foreach ($tr as $td) {
                    $html .= '<td>' . $td . '</td>';
                }
                $html .= '</tr>';
            }
            $html .= '</tbody>';
        }

        // enable error reporting
        $wpdb->suppress_errors = false;

        return $html;
    }

	/**
	 * advanced_data_table_remote_database_integration
     * Access remote database using settings info ,after that fetch table data and
     * Preview in Advance Data Table widget
     *
     *
	 * @param $settings array elementor settings data
	 *
     * @access public
	 * @return array|string|void
     * @since 3.1.0
	 */
	public function advanced_data_table_remote_database_integration( $settings ) {
		global $wpdb;

		$html    = '';
		$results = [];

		// suppress error
		$wpdb->suppress_errors = true;

		// collect data
		if ( $settings['ea_adv_data_table_source'] == 'remote' ) {
			if ( empty( $settings['ea_adv_data_table_source_remote_host'] ) || empty( $settings['ea_adv_data_table_source_remote_username'] ) || empty( $settings['ea_adv_data_table_source_remote_password'] ) || empty( $settings['ea_adv_data_table_source_remote_database'] ) ) {
				return;
			}

			if ( $settings['ea_adv_data_table_source_remote_connected'] == false ) {
				return;
			}

			$conn = new mysqli( $settings['ea_adv_data_table_source_remote_host'], $settings['ea_adv_data_table_source_remote_username'], $settings['ea_adv_data_table_source_remote_password'], $settings['ea_adv_data_table_source_remote_database'] );

			if ( $conn->connect_error ) {
				return "Failed to connect to MySQL: " . $conn->connect_error;
			} else {
				$conn->set_charset( "utf8" );

				if ( $settings['ea_adv_data_table_source_remote_query_type'] == 'table' ) {
					$table = $settings['ea_adv_data_table_source_remote_table'];
					$query = $conn->query( "SELECT * FROM $table" );

					if ( $query ) { //@todo we have to cache data for optimize site speed and mysql query request
						$results = $query->fetch_all( MYSQLI_ASSOC );
					}
				} else {

					if ( ! $this->eael_valid_select_query( $settings['ea_adv_data_table_source_remote_query'] ) ) {
						return $results;
					}

					if ( $settings['ea_adv_data_table_source_remote_query'] ) {
						$query = $conn->query( $settings['ea_adv_data_table_source_remote_query'] );

						if ( $query ) {
							$results = $query->fetch_all( MYSQLI_ASSOC );
						}
					}
				}

				$conn->close();
			}
		}

		if ( ! empty( $results ) ) {
			$html .= '<thead><tr>';
			foreach ( array_keys( $results[0] ) as $key => $th ) {
				$style = isset( $settings['ea_adv_data_table_dynamic_th_width'] ) && isset( $settings['ea_adv_data_table_dynamic_th_width'][ $key ] ) ? ' style="width:' . $settings['ea_adv_data_table_dynamic_th_width'][ $key ] . '"' : '';
				$html  .= '<th' . $style . '>' . $th . '</th>';
			}
			$html .= '</tr></thead>';

			$html .= '<tbody>';
			foreach ( $results as $tr ) {
				$html .= '<tr>';
				foreach ( $tr as $td ) {
					$html .= '<td>' . $td . '</td>';
				}
				$html .= '</tr>';
			}
			$html .= '</tbody>';
		}

		// enable error reporting
		$wpdb->suppress_errors = false;

		return $html;
	}

    public function advanced_data_table_google_sheets_integration($settings)
    {
        if (empty($settings['ea_adv_data_table_source_google_api_key']) || empty($settings['ea_adv_data_table_source_google_sheet_id']) || empty($settings['ea_adv_data_table_source_google_table_range'])) {
            return;
        }

        $arg = [
          'google_sheet_api_key' => $settings['ea_adv_data_table_source_google_api_key'],
          'google_sheet_id' => $settings['ea_adv_data_table_source_google_sheet_id'],
          'table_range' => $settings['ea_adv_data_table_source_google_table_range'],
          'cache_time' => $settings['ea_adv_data_table_data_cache_limit'],
        ];

        $thead = '';
        $tbody = '';

        $transient_key = 'ea_adv_data_table_source_google_sheet_' . md5(implode('', $arg));

        $results = get_transient( $transient_key );

	    if ( empty( $results ) || empty( $results['rowData'] ) ) {
		    $connection = wp_remote_get( "https://sheets.googleapis.com/v4/spreadsheets/{$settings['ea_adv_data_table_source_google_sheet_id']}/?key={$settings['ea_adv_data_table_source_google_api_key']}&ranges={$settings['ea_adv_data_table_source_google_table_range']}&includeGridData=true", [ 'timeout' => 70 ] );

		    if ( ! is_wp_error( $connection ) ) {
			    $connection = json_decode( wp_remote_retrieve_body( $connection ), true );
			    if ( isset( $connection['sheets'][0]['data'][0]['rowData'] ) ) {
				    $results                = [];
				    $results['rowData']     = $connection['sheets'][0]['data'][0]['rowData'];
				    $results['startRow']    = empty( $connection['sheets'][0]['data'][0]['startRow'] ) ? 0 : $connection['sheets'][0]['data'][0]['startRow'];
				    $results['startColumn'] = empty( $connection['sheets'][0]['data'][0]['startColumn'] ) ? 0 : $connection['sheets'][0]['data'][0]['startColumn'];

				    $table_range     = explode( ':', $arg['table_range'] );

				    if ( empty( $table_range[1] ) ) {
					    if ( empty( $connection['namedRanges'] ) ) {
						    $endRow    = $results['startRow'] + 1;
						    $endColumn = $results['startColumn'] + 1;
					    } elseif ( count( $connection['namedRanges'] ) === 1 ) {
						    $endRow    = $connection['namedRanges'][0]['range']['endRowIndex'];
						    $endColumn = $connection['namedRanges'][0]['range']['endColumnIndex'];
					    } else {
						    foreach ( $connection['namedRanges'] as $range ) {
							    if ( $range['name'] === $arg['table_range'] ) {
								    $endRow    = $range['range']['endRowIndex'];
								    $endColumn = $range['range']['endColumnIndex'];
							    }
						    }
					    }
				    } else {
					    $table_range     = strrev( $table_range[1] . '1' ); // Added extra 1 digit in last so that after flip the number pattern will ok. ex: 20 flip is 02 but 201 flip is 102
					    $table_range     = sscanf( $table_range, '%d%s' );
					    $table_range     = array_map( 'strrev', $table_range ); // Flip again to get exact value
					    $endRow          = substr( $table_range[0], 0, - 1 ); // Remove the extra last digit
					    $endColumn       = strtoupper( $table_range[1] );
					    $alphabet_to_int = array_flip( range( 'A', 'Z' ) );

					    if ( strlen( $endColumn ) === 1 ) {
						    $endColumn = $alphabet_to_int[ $endColumn ] + 1;
					    } else {
						    $sum = 0;
						    for ( $i = 0; $i < strlen( $endColumn ); $i ++ ) {
							    $sum = $sum * 26 + $alphabet_to_int[ $endColumn[ $i ] ] + 1;
						    }
						    $endColumn = $sum;
					    }
				    }

				    $results['rowCount']    = absint( $endRow ) - $results['startRow'];
				    $results['columnCount'] = absint( $endColumn ) - $results['startColumn'];

				    if ( isset( $connection['sheets'][0]['merges'] ) && is_array( $connection['sheets'][0]['merges'] ) ) {
					    $results['mergeData'] = $connection['sheets'][0]['merges'];
				    }

				    set_transient( $transient_key, $results, $settings['ea_adv_data_table_data_cache_limit'] * MINUTE_IN_SECONDS );
			    }
		    }
	    }

	    if ( ! empty( $results['rowData'] ) ) {
		    if ( ! empty( $results['mergeData'] ) && is_array( $results['mergeData'] ) ) {
			    $merge_data_cell = [];
			    foreach ( $results['mergeData'] as $merge_data ) {
				    $attrs = [
					    'rowSpan' => '',
					    'colSpan' => '',
				    ];

				    if ( ( $row_span = $merge_data['endRowIndex'] - $merge_data['startRowIndex'] ) !== 1 ) {
					    $attrs['rowSpan'] = "rowspan='{$row_span}'";
				    }

				    if ( ( $col_span = $merge_data['endColumnIndex'] - $merge_data['startColumnIndex'] ) !== 1 ) {
					    $attrs['colSpan'] = "colspan='{$col_span}'";
				    }

				    $startRowIndex            = $merge_data['startRowIndex'] - $results['startRow'];
				    $startColumnIndex         = $merge_data['startColumnIndex'] - $results['startColumn'];
				    $cell                     = "{$startRowIndex}-{$startColumnIndex}";
				    $merge_data_cell[ $cell ] = $attrs;

				    for ( $row = 0; $row < $row_span; $row ++ ) {
					    for ( $col = 0; $col < $col_span; $col ++ ) {
						    if ( $row == 0 && $col == 0 ) {
							    continue;
						    }
						    $rowindex = $startRowIndex + $row;
						    $colindex = $startColumnIndex + $col;

						    $merge_data_cell["{$rowindex}-{$colindex}"] = true;
					    }
				    }
			    }
		    }

		    foreach ( $results['rowData'] as $tr_key => $tr ) {

			    if ( isset( $tr['values'] ) ) {
				    $tr = $tr['values'];
			    } else {
				    if ( $tr_key == 0 ) {
					    $thead .= '<tr>' . str_repeat( '<th>&nbsp;</th>', $results['columnCount'] ) . '</tr>';
				    } else {
					    $tbody .= '<tr>' . str_repeat( '<td>&nbsp;</td>', $results['columnCount'] ) . '</tr>';
				    }
				    continue;
			    }

                if ($tr_key == 0) {
                    $thead .= '<tr>';
                    foreach ($tr as $key => $th) {
	                    $cell = "{$tr_key}-{$key}";
	                    if ( ! empty( $merge_data_cell[ $cell ] ) && $merge_data_cell[ $cell ] === true ) {
		                    goto empty_th_cell;
	                    }

	                    $style          = isset( $settings['ea_adv_data_table_dynamic_th_width'] ) && isset( $settings['ea_adv_data_table_dynamic_th_width'][ $key ] ) ? ' style="width:' . $settings['ea_adv_data_table_dynamic_th_width'][ $key ] . '"' : '';
	                    $formattedValue = empty( $th['formattedValue'] ) ? '' : $th['formattedValue'];
	                    $th             = isset( $th['hyperlink'] ) ? '<a href="' . $th['hyperlink'] . '" target="_blank">' . $formattedValue . '</a>' : $formattedValue;
	                    $row_span       = empty( $merge_data_cell[ $cell ]['rowSpan'] ) ? '' : $merge_data_cell[ $cell ]['rowSpan'];
	                    $col_span       = empty( $merge_data_cell[ $cell ]['colSpan'] ) ? '' : $merge_data_cell[ $cell ]['colSpan'];
	                    $merge_attr     = " {$row_span} {$col_span}";

	                    $thead .= '<th' . $style . $merge_attr . '>' . $th . '</th>';

	                    empty_th_cell:
	                    if ( count( $tr ) < $results['columnCount'] && count( $tr ) === $key + 1 ) {
		                    $thead .= str_repeat( '<th>&nbsp;</th>', $results['columnCount'] - count( $tr ) );
	                    }
                    }
                    $thead .= '</tr>';
                } else {
                    $tbody .= '<tr>';

                    foreach ($tr as $key => $td) {
	                    $cell = "{$tr_key}-{$key}";
	                    if ( ! empty( $merge_data_cell[ $cell ] ) && $merge_data_cell[ $cell ] === true ) {
		                    goto empty_td_cell;
	                    }

	                    $row_span       = empty( $merge_data_cell[ $cell ]['rowSpan'] ) ? '' : $merge_data_cell[ $cell ]['rowSpan'];
	                    $col_span       = empty( $merge_data_cell[ $cell ]['colSpan'] ) ? '' : $merge_data_cell[ $cell ]['colSpan'];
	                    $merge_attr     = " {$row_span} {$col_span}";
	                    $formattedValue = empty( $td['formattedValue'] ) ? '' : $td['formattedValue'];
	                    $formattedValue = isset( $td['formattedValue'] ) && '0' === $td['formattedValue'] ? esc_html( $td['formattedValue'] ) : $formattedValue; // Fix for 0 value
	                    $td             = isset( $td['hyperlink'] ) ? '<a href="' . $td['hyperlink'] . '" target="_blank">' . $formattedValue . '</a>' : $formattedValue;

	                    $tbody .= '<td' . $merge_attr . '>' . $td . '</td>';

	                    empty_td_cell:
	                    if ( count( $tr ) < $results['columnCount'] && count( $tr ) === $key + 1 ) {
		                    $tbody .= str_repeat( '<td>&nbsp;</td>', $results['columnCount'] - count( $tr ) );
	                    }
                    }

                    $tbody .= '</tr>';
                }

			    if ( count( $results['rowData'] ) < $results['rowCount'] && count( $results['rowData'] ) === $tr_key + 1 ) {
				    $tbody .= str_repeat( '<tr>' . str_repeat( '<td>&nbsp;</td>', $results['columnCount'] ) . '</tr>', $results['rowCount'] - count( $results['rowData'] ) );
			    }
            }

            return '<thead>' . $thead . '</thead><tbody>' . $tbody . '</tbody>';
        }

        return '';
    }

    public function advanced_data_table_tablepress_integration($settings)
    {
        if (empty($settings['ea_adv_data_table_source_tablepress_table_id'])) {
            return;
        }

        $html = '';
        $tables_opt = get_option('tablepress_tables', '{}');
        $tables_opt = json_decode($tables_opt, true);
        $tables = $tables_opt['table_post'];
        $table_id = $tables[$settings['ea_adv_data_table_source_tablepress_table_id']];
        $table_data = get_post_field('post_content', $table_id);
        $results = json_decode($table_data, true);
        $table_settings = get_post_meta($table_id, '_tablepress_table_options', true);
        $table_settings = json_decode($table_settings, true);

        //return do_shortcode('[table id=1 /]');
        if (!empty($results)) {
	        if ( ! empty( $table_settings ) && isset( $table_settings['table_head'] ) && $table_settings['table_head'] == true ) {
		        $html .= '<thead><tr>';
		        foreach ( $results[0] as $key => $th ) {
			        $style   = isset( $settings['ea_adv_data_table_dynamic_th_width'] ) && isset( $settings['ea_adv_data_table_dynamic_th_width'][ $key ] ) ? ' style="width:' . $settings['ea_adv_data_table_dynamic_th_width'][ $key ] . '"' : '';
			        $colspan = 1;
			        while ( ! empty( $results[0][ $key + $colspan ] ) && $results[0][ $key + $colspan ] == '#colspan#' ) {
				        $colspan ++;
			        }

			        if ( $th == '#colspan#' ) {
				        continue;
			        }
			        $combine_cells = $colspan > 1 ? " colspan={$colspan} " : '';
			        $html          .= '<th' . $combine_cells . $style . '>' . nl2br( $th ) . '</th>';
		        }
		        $html .= '</tr></thead>';

		        array_shift( $results );
	        }

            $html .= '<tbody>';
            foreach ($results as $index =>  $tr) {
                $html .= '<tr>';
	            $col = false;
                $row = [];

                // for rowspan support
	            if ( ! empty( $results[ $index + 1 ] ) ) {
		            foreach ( $results[ $index + 1 ] as $next_key => $previous ) {
			            if ( $previous == '#rowspan#' ) {
				            $row[] = $next_key;
			            }
		            }
	            }

	            foreach ( $tr as $key => $td ) {
		            if ( $col ) {
			            $col = ! empty( $tr[ $key + 1 ] ) && $tr[ $key + 1 ] == '#colspan#';
			            continue;
		            }

		            $colspan = 1;
		            while ( ! empty( $tr[ $key + $colspan ] ) && $tr[ $key + $colspan ] == '#colspan#' ) {
			            $colspan ++;
		            }
		            $combine_cells = $colspan > 1 ? " colspan={$colspan} " : '';
		            $col           = ! empty( $tr[ $key + 1 ] ) && $tr[ $key + 1 ] == '#colspan#';

		            if ( in_array( $key, $row ) ) {
			            $combine_cells = 'rowspan=2';
		            }

		            if ( $td == '#rowspan#' ) {
			            continue;
		            }

		            $html .= '<td ' . $combine_cells . '>' . nl2br( $td ) . '</td>';
                }
                $html .= '</tr>';

            }
            $html .= '</tbody>';
        }

        return $html;
    }

    public function event_calendar_eventon_integration($data, $settings)
    {
        if (!function_exists('EVO') || $settings['eael_event_calendar_type'] != 'eventon') {
            return $data;
        }

        $default_attr = EVO()->calendar->get_supported_shortcode_atts();
        $default_attr['event_count'] = $settings['eael_eventon_calendar_max_result'];

	    if ( $settings['eael_eventon_calendar_fetch'] == 'date_range' ) {
		    $default_attr['focus_start_date_range'] = strtotime( $settings['eael_eventon_calendar_start_date'] );
		    $default_attr['focus_end_date_range']   = strtotime( $settings['eael_eventon_calendar_end_date'] );
	    } else {
		    $default_attr['focus_start_date_range'] = strtotime( "-6 years", current_time( 'timestamp', 0 ) );
		    $default_attr['focus_end_date_range']   = strtotime( "+6 years", current_time( 'timestamp', 0 ) );
	    }

        $cat_arr = Helper::get_taxonomies_by_post(['object_type' => 'ajde_events']);

        foreach ($cat_arr as $key => $cat) {

            $cat_id = 'eael_eventon_calendar_' . $key;

            if (!empty($settings[$cat_id])) {
                if ($cat == 'post_tag') {
                    $cat = 'event_tag';
                }
                $default_attr[$cat] = implode(',', $settings[$cat_id]);
            }
        }

        EVO()->calendar->shortcode_args = $default_attr;
        $content = EVO()->evo_generator->_generate_events();
        $events = $content['data'];

        if (!empty($events)) {
            $data = [];
	        foreach ( $events as $key => $event ) {
		        $event_id    = $event['ID'];
		        $date_format = 'Y-m-d';
		        $all_day     = 'yes';
		        $featured    = get_post_meta( $event_id, '_featured', true );

		        $end = date( $date_format, ( $event['event_end_unix'] + 86400 ) );
		        if ( get_post_meta( $event_id, 'evcal_allday', true ) === 'no' ) {
			        $date_format .= ' H:i';
			        $all_day     = '';
			        $end         = date( $date_format, $event['event_end_unix'] );
		        }

		        $start = date( $date_format, $event['event_start_unix'] );

		        if ( ! empty( $settings['eael_old_events_hide'] ) && 'yes' === $settings['eael_old_events_hide'] ) {
			        $is_old_event = $this->is_old_event_pro( $start );
			        if ( $is_old_event ) {
				        continue;
			        }
		        }

		        $data[] = [
			        'id'               => $event_id,
			        'title'            => ! empty( $event['event_title'] ) ? html_entity_decode( $event['event_title'], ENT_QUOTES ) : __( 'No Title', 'essential-addons-for-elementor' ),
			        'description'      => $content = get_post_field( 'post_content', $event_id ),
			        'start'            => $start,
			        'end'              => $end,
			        'borderColor'      => '#6231FF',
			        'textColor'        => $settings['eael_event_global_text_color'],
			        'color'            => ( $featured == 'yes' ) ? $settings['eael_event_on_featured_color'] : $settings['eael_event_global_bg_color'],
			        'url'              => ( $settings['eael_event_details_link_hide'] !== 'yes' ) ? get_the_permalink( $event_id ) : '',
			        'allDay'           => $all_day,
			        'external'         => 'on',
			        'nofollow'         => 'on',
			        'eventHasComplete' => get_post_meta( $event_id, '_completed', true ),
			        'hideEndDate'      => get_post_meta( $event_id, 'evo_hide_endtime', true ),
		        ];
	        }
        }

        return $data;
    }

    /**
     * Event Calendar: EventOn
     * 
     * @since 5.1.2
     */
    public function is_old_event_pro($start_date){
	    $today                = strtotime( current_time( 'Y-m-d' ) );
	    $start_date_timestamp = strtotime( $start_date );

	    if ( $start_date_timestamp < $today ) {
		    return true;
	    }

	    return false;
    }

    /**
     * Woo Checkout Layout
     */
    public function eael_woo_checkout_layout($layout)
    {
        if (apply_filters('eael/pro_enabled', false)) {
            $layout['multi-steps'] = __('Multi Steps', 'essential-addons-elementor');
            $layout['split'] = __('Split', 'essential-addons-elementor');
        } else {
            $layout['multi-steps'] = __('Multi Steps', 'essential-addons-elementor');
            $layout['split'] = __('Split (Pro)', 'essential-addons-elementor');
        }

        return $layout;
    }

    /**
     * Woo Checkout Layout Template
     */
    public function add_woo_checkout_pro_layout($checkout, $settings)
    {
        if ($settings['ea_woo_checkout_layout'] == 'split') {
            echo self::woo_checkout_render_split_template_($checkout, $settings);
        } elseif ($settings['ea_woo_checkout_layout'] == 'multi-steps') {
            echo self::woo_checkout_render_multi_steps_template_($checkout, $settings);
        }
    }

    /**
     * Woo Checkout Tab Data Settings
     */
    public function add_woo_checkout_tabs_data($obj)
    {

        $obj->add_control('ea_woo_checkout_tabs_settings', [
            'label' => __('Tabs Label', 'essential-addons-elementor'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [
                'ea_woo_checkout_layout!' => 'default',
            ],
        ]);
        $obj->add_control('ea_woo_checkout_tab_login_text', [
            'label' => __('Login', 'essential-addons-elementor'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default' => __('Login', 'essential-addons-elementor'),
            'condition' => [
                'ea_woo_checkout_layout!' => 'default',
            ],
            'description' => 'To preview the changes in Login tab, turn on the Settings from \'Login\' section below.',
        ]);
        $obj->add_control('ea_woo_checkout_tab_coupon_text', [
            'label' => __('Coupon', 'essential-addons-elementor'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default' => __('Coupon', 'essential-addons-elementor'),
            'condition' => [
                'ea_woo_checkout_layout!' => 'default',
            ],
        ]);
        $obj->add_control('ea_woo_checkout_tab_billing_shipping_text', [
            'label' => __('Billing & Shipping', 'essential-addons-elementor'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default' => __('Billing & Shipping', 'essential-addons-elementor'),
            'condition' => [
                'ea_woo_checkout_layout!' => 'default',
            ],
        ]);
        $obj->add_control('ea_woo_checkout_tab_payment_text', [
            'label' => __('Payment', 'essential-addons-elementor'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default' => __('Payment', 'essential-addons-elementor'),
            'condition' => [
                'ea_woo_checkout_layout!' => 'default',
            ],
        ]);

        $obj->add_control('ea_woo_checkout_tabs_btn_settings', [
            'label' => __('Previous/Next Label', 'essential-addons-elementor'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [
                'ea_woo_checkout_layout!' => 'default',
            ],
        ]);
        $obj->add_control('ea_woo_checkout_tabs_btn_next_text', [
            'label' => __('Next', 'essential-addons-elementor'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default' => __('Next', 'essential-addons-elementor'),
            'condition' => [
                'ea_woo_checkout_layout!' => 'default',
            ],
        ]);
        $obj->add_control('ea_woo_checkout_tabs_btn_prev_text', [
            'label' => __('Previous', 'essential-addons-elementor'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default' => __('Previous', 'essential-addons-elementor'),
            'condition' => [
                'ea_woo_checkout_layout!' => 'default',
            ],
        ]);
    }

    /**
     * Woo Checkout Layout
     */
    public function add_woo_checkout_tabs_styles($obj)
    {

        $obj->start_controls_section('ea_section_woo_checkout_tabs_styles', [
            'label' => esc_html__('Tabs', 'essential-addons-elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => [
                'ea_woo_checkout_layout!' => 'default',
            ],
        ]);

        $obj->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'ea_section_woo_checkout_tabs_typo',
            'selector' => '{{WRAPPER}} .ea-woo-checkout.layout-split .layout-split-container .info-area .split-tabs li, {{WRAPPER}} .ea-woo-checkout.layout-multi-steps .layout-multi-steps-container .ms-tabs .ms-tab',
            'fields_options' => [
                'font_size' => [
                    'default' => [
                        'unit' => 'px',
                        'size' => 16,
                    ],
                ],
            ],
        ]);

        $obj->start_controls_tabs('ea_woo_checkout_tabs_tabs');
        $obj->start_controls_tab('ea_woo_checkout_tabs_tab_normal', ['label' => esc_html__('Normal', 'essential-addons-elementor')]);

        $obj->add_control('ea_woo_checkout_tabs_bg_color', [
            'label' => esc_html__('Background Color', 'essential-addons-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#f4f6fc',
            'selectors' => [
                '{{WRAPPER}} .ea-woo-checkout.layout-split .layout-split-container .info-area .split-tabs' => 'background-color: {{VALUE}};',
            ],
            'condition' => [
                'ea_woo_checkout_layout' => 'split',
            ],
        ]);
        $obj->add_control('ea_woo_checkout_tabs_color', [
            'label' => esc_html__('Color', 'essential-addons-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#404040',
            'selectors' => [
                '{{WRAPPER}} .split-tabs li, {{WRAPPER}} .ms-tabs li' => 'color: {{VALUE}};',
            ],
        ]);

        $obj->end_controls_tab();

        $obj->start_controls_tab('ea_woo_checkout_tabs_tab_active', ['label' => esc_html__('Active', 'essential-addons-elementor')]);

        $obj->add_control('ea_woo_checkout_tabs_bg_color_active', [
            'label' => esc_html__('Background Color', 'essential-addons-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#7866ff',
            'selectors' => [
                '{{WRAPPER}} .ea-woo-checkout.layout-split .layout-split-container .info-area .split-tabs li.active' => 'background-color: {{VALUE}};',
            ],
            'condition' => [
                'ea_woo_checkout_layout' => 'split',
            ],
        ]);
        $obj->add_control('ea_woo_checkout_tabs_color_active', [
            'label' => esc_html__('Color', 'essential-addons-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .ea-woo-checkout.layout-split .layout-split-container .info-area .split-tabs li.active' => 'color: {{VALUE}};',
            ],
            'condition' => [
                'ea_woo_checkout_layout' => 'split',
            ],
        ]);
        $obj->add_control('ea_woo_checkout_tabs_ms_color_active', [
            'label' => esc_html__('Color', 'essential-addons-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#7866ff',
            'selectors' => [
                '{{WRAPPER}} .ea-woo-checkout.layout-multi-steps .layout-multi-steps-container .ms-tabs li.completed' => 'color: {{VALUE}};',
            ],
            'condition' => [
                'ea_woo_checkout_layout' => 'multi-steps',
            ],
        ]);
        $obj->add_group_control(Group_Control_Box_Shadow::get_type(), [
            'name' => 'ea_woo_checkout_tabs_box_shadow',
            'separator' => 'before',
            'selector' => '{{WRAPPER}} .ea-woo-checkout.layout-split .layout-split-container .info-area .split-tabs li.active',
            'condition' => [
                'ea_woo_checkout_layout' => 'split',
            ],
        ]);

        $obj->end_controls_tab();
        $obj->end_controls_tabs();

        $obj->add_responsive_control('ea_woo_checkout_tabs_border_radius', [
            'label' => __('Border Radius', 'essential-addons-elementor'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => 05,
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 50,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .split-tabs, {{WRAPPER}} .split-tab li.active' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [
                'ea_woo_checkout_layout' => 'split',
            ],
        ]);
        $obj->add_responsive_control('ea_woo_checkout_tabs_padding', [
            'label' => esc_html__('Padding', 'essential-addons-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'default' => [
                'top' => '17',
                'right' => '17',
                'bottom' => '17',
                'left' => '17',
                'unit' => 'px',
                'isLinked' => true,
            ],
            'size_units' => [
                'px',
                'em',
                '%',
            ],
            'selectors' => [
                '{{WRAPPER}} .ea-woo-checkout.layout-split .layout-split-container .info-area .split-tabs li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'condition' => [
                'ea_woo_checkout_layout' => 'split',
            ],
        ]);

        $obj->add_responsive_control('ea_woo_checkout_tabs_bottom_gap', [
            'label' => esc_html__('Bottom Gap', 'essential-addons-for-elementor'),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'max' => 50,
                ],
            ],
            'default' => [
                'unit' => 'px',
                'size' => 30,
            ],
            'selectors' => [
                '{{WRAPPER}} .ea-woo-checkout.layout-multi-steps .layout-multi-steps-container .ms-tabs' => 'margin: 0 0 {{SIZE}}{{UNIT}} 0;',
            ],
            'condition' => [
                'ea_woo_checkout_layout' => 'multi-steps',
            ],
        ]);
        // multi steps
        $obj->add_control('ea_woo_checkout_tabs_steps', [
            'label' => __('Steps', 'essential-addons-elementor'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
            'condition' => [
                'ea_woo_checkout_layout' => 'multi-steps',
            ],
        ]);
        $obj->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'ea_section_woo_checkout_tabs_steps_typo',
            'selector' => '{{WRAPPER}} .ea-woo-checkout.layout-multi-steps .layout-multi-steps-container .ms-tabs li:before',
            'fields_options' => [
                'font_size' => [
                    'default' => [
                        'unit' => 'px',
                        'size' => 12,
                    ],
                ],
            ],
            'condition' => [
                'ea_woo_checkout_layout' => 'multi-steps',
            ],
        ]);
        $obj->start_controls_tabs('ea_woo_checkout_tabs_steps_tabs', [
            'condition' => [
                'ea_woo_checkout_layout' => 'multi-steps',
            ],
        ]);
        $obj->start_controls_tab('ea_woo_checkout_tabs_steps_tab_normal', ['label' => esc_html__('Normal', 'essential-addons-elementor')]);
        $obj->add_control('ea_woo_checkout_tabs_steps_bg_color', [
            'label' => esc_html__('Background Color', 'essential-addons-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#d3c9f7',
            'selectors' => [
                '{{WRAPPER}} .ea-woo-checkout.layout-multi-steps .layout-multi-steps-container .ms-tabs li:before' => 'background-color: {{VALUE}};',
            ],
        ]);
        $obj->add_control('ea_woo_checkout_tabs_steps_color', [
            'label' => esc_html__('Color', 'essential-addons-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#7866FF',
            'selectors' => [
                '{{WRAPPER}} .ea-woo-checkout.layout-multi-steps .layout-multi-steps-container .ms-tabs li:before' => 'color: {{VALUE}};',
            ],
        ]);
        $obj->add_group_control(Group_Control_Border::get_type(), [
            'name' => 'ea_woo_checkout_tabs_steps_border',
            'selector' => '{{WRAPPER}} .ea-woo-checkout.layout-multi-steps .layout-multi-steps-container .ms-tabs li:before',
        ]);
        $obj->add_control('ea_woo_checkout_tabs_steps_connector_color', [
            'label' => esc_html__('Connector Color', 'essential-addons-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#d3c9f7',
            'selectors' => [
                '{{WRAPPER}} .ea-woo-checkout.layout-multi-steps .layout-multi-steps-container .ms-tabs li:after' => 'background-color: {{VALUE}};',
            ],
        ]);
        $obj->end_controls_tab();
        $obj->start_controls_tab('ea_woo_checkout_tabs_steps_tab_active', ['label' => esc_html__('Active', 'essential-addons-elementor')]);
        $obj->add_control('ea_woo_checkout_tabs_steps_bg_color_active', [
            'label' => esc_html__('Background Color', 'essential-addons-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#7866ff',
            'selectors' => [
                '{{WRAPPER}} .ea-woo-checkout.layout-multi-steps .layout-multi-steps-container .ms-tabs li.completed:before' => 'background-color: {{VALUE}};',
            ],
        ]);
        $obj->add_control('ea_woo_checkout_tabs_steps_color_active', [
            'label' => esc_html__('Color', 'essential-addons-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .ea-woo-checkout.layout-multi-steps .layout-multi-steps-container .ms-tabs li.completed:before' => 'color: {{VALUE}};',
            ],
        ]);
        $obj->add_control('ea_woo_checkout_tabs_steps_connector_color_active', [
            'label' => esc_html__('Connector Color', 'essential-addons-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#7866ff',
            'selectors' => [
                '{{WRAPPER}} .ea-woo-checkout.layout-multi-steps .layout-multi-steps-container .ms-tabs li.completed:after' => 'background-color: {{VALUE}};',
            ],
        ]);
        $obj->end_controls_tab();
        $obj->end_controls_tabs();
        $obj->add_responsive_control('ea_woo_checkout_tabs_steps_size', [
            'label' => __('Size', 'essential-addons-elementor'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => 25,
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 50,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .ea-woo-checkout.layout-multi-steps .layout-multi-steps-container .ms-tabs li:before' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
                '{{WRAPPER}} .ea-woo-checkout.layout-multi-steps .layout-multi-steps-container .ms-tabs li:after' => 'top: calc(({{SIZE}}{{UNIT}}/2) - 2px);',
            ],
            'condition' => [
                'ea_woo_checkout_layout' => 'multi-steps',
            ],
        ]);
        $obj->add_responsive_control('ea_woo_checkout_tabs_steps_border_radius', [
            'label' => __('Border Radius', 'essential-addons-elementor'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => 50,
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 50,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .ea-woo-checkout.layout-multi-steps .layout-multi-steps-container .ms-tabs li:before' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
            'condition' => [
                'ea_woo_checkout_layout' => 'multi-steps',
            ],
        ]);

        $obj->end_controls_section();
    }

    /**
     * Woo Checkout section
     */
    public function add_woo_checkout_section_styles($obj)
    {

        $obj->start_controls_section('ea_section_woo_checkout_section_styles', [
            'label' => esc_html__('Section', 'essential-addons-elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => [
                'ea_woo_checkout_layout' => 'multi-steps',
            ],
        ]);
        $obj->add_control('ea_woo_checkout_section_bg_color', [
            'label' => esc_html__('Background', 'essential-addons-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .ms-tabs-content' => 'background-color: {{VALUE}};',
            ],
        ]);

        $obj->add_responsive_control('ea_woo_checkout_section_border_radius', [
            'label' => __('Border Radius', 'essential-addons-elementor'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => 05,
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 50,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .ms-tabs-content' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
        ]);
        $obj->add_responsive_control('ea_woo_checkout_section_padding', [
            'label' => esc_html__('Padding', 'essential-addons-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'default' => [
                'top' => '25',
                'right' => '25',
                'bottom' => '25',
                'left' => '25',
                'unit' => 'px',
                'isLinked' => true,
            ],
            'size_units' => [
                'px',
                'em',
                '%',
            ],
            'selectors' => [
                '{{WRAPPER}} .ms-tabs-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);
        $obj->add_group_control(Group_Control_Box_Shadow::get_type(), [
            'name' => 'ea_woo_checkout_section_box_shadow',
            'separator' => 'before',
            'selector' => '{{WRAPPER}} .ea-woo-checkout.layout-multi-steps .layout-multi-steps-container .ms-tabs-content-wrap .ms-tabs-content',
        ]);

        $obj->end_controls_section();
    }

    /**
     * Woo Checkout Tab Data Style
     */
    public function add_woo_checkout_steps_btn_styles($obj)
    {

        $obj->start_controls_section('ea_section_woo_checkout_steps_btn_styles', [
            'label' => esc_html__('Previous/Next Button', 'essential-addons-elementor'),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => [
                'ea_woo_checkout_layout!' => 'default',
            ],
        ]);

        $obj->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'ea_woo_checkout_steps_btn_typo',
            'selector' => '{{WRAPPER}} .steps-buttons button',
        ]);
        $obj->start_controls_tabs('ea_woo_checkout_steps_btn_tabs');
        $obj->start_controls_tab('ea_woo_checkout_steps_btn_tab_normal', ['label' => __('Normal', 'essential-addons-for-elementor')]);

        $obj->add_control('ea_woo_checkout_steps_btn_bg_color', [
            'label' => __('Background Color', 'essential-addons-for-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#7866ff',
            'selectors' => [
                '{{WRAPPER}} .ea-woo-checkout .steps-buttons button,
                {{WRAPPER}} .ea-woo-checkout .steps-buttons button#ea_place_order' => 'background-color: {{VALUE}};background: {{VALUE}};',
            ],
        ]);

        $obj->add_control('ea_woo_checkout_steps_btn_color', [
            'label' => __('Color', 'essential-addons-for-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .ea-woo-checkout .steps-buttons button,
                {{WRAPPER}} .ea-woo-checkout .steps-buttons button#ea_place_order' => 'color: {{VALUE}};',
            ],
        ]);

        $obj->add_group_control(Group_Control_Border::get_type(), [
            'name' => 'ea_woo_checkout_steps_btn_border',
            'selector' => '{{WRAPPER}} .ea-woo-checkout .steps-buttons button,
            {{WRAPPER}} .ea-woo-checkout .steps-buttons button#ea_place_order',
        ]);

        $obj->end_controls_tab();

        $obj->start_controls_tab('ea_woo_checkout_steps_btn_tab_hover', ['label' => __('Hover', 'essential-addons-for-elementor')]);

        $obj->add_control('ea_woo_checkout_steps_btn_bg_color_hover', [
            'label' => __('Background Color', 'essential-addons-for-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#7866ff',
            'selectors' => [
                '{{WRAPPER}} .ea-woo-checkout .steps-buttons button:hover,
                {{WRAPPER}} .ea-woo-checkout .steps-buttons button#ea_place_order:hover' => 'background-color: {{VALUE}};background: {{VALUE}};',
            ],
        ]);

        $obj->add_control('ea_woo_checkout_steps_btn_color_hover', [
            'label' => __('Color', 'essential-addons-for-elementor'),
            'type' => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .ea-woo-checkout .steps-buttons button:hover,
                {{WRAPPER}} .ea-woo-checkout .steps-buttons button#ea_place_order:hover' => 'color: {{VALUE}};',
            ],
        ]);

        $obj->add_control('ea_woo_checkout_steps_btn_border_color_hover', [
            'label' => __('Border Color', 'essential-addons-for-elementor'),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '{{WRAPPER}} .ea-woo-checkout .steps-buttons button:hover,
                {{WRAPPER}} .ea-woo-checkout .steps-buttons button#ea_place_order:hover' => 'border-color: {{VALUE}};',
            ],
            'condition' => [
                'ea_section_woo_checkout_steps_btn_border_border!' => '',
            ],
        ]);

        $obj->end_controls_tab();
        $obj->end_controls_tabs();

        $obj->add_control('ea_woo_checkout_steps_btn_border_radius', [
            'label' => __('Border Radius', 'essential-addons-for-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => [
                'px',
                '%',
            ],
            'default' => [
                'top' => '5',
                'right' => '5',
                'bottom' => '5',
                'left' => '5',
                'unit' => 'px',
                'isLinked' => true,
            ],
            'selectors' => [
                '{{WRAPPER}} .ea-woo-checkout .steps-buttons button,
                {{WRAPPER}} .ea-woo-checkout .steps-buttons button#ea_place_order' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);
        $obj->add_group_control(Group_Control_Box_Shadow::get_type(), [
            'name' => 'ea_woo_checkout_steps_btn_box_shadow',
            'selector' => '{{WRAPPER}} .ea-woo-checkout .steps-buttons button',
        ]);
        $obj->add_responsive_control('ea_woo_checkout_steps_btn_padding', [
            'label' => esc_html__('Padding', 'essential-addons-elementor'),
            'type' => Controls_Manager::DIMENSIONS,
            'default' => [
                'top' => '13',
                'right' => '20',
                'bottom' => '13',
                'left' => '20',
                'unit' => 'px',
                'isLinked' => true,
            ],
            'size_units' => [
                'px',
                'em',
                '%',
            ],
            'selectors' => [
                '{{WRAPPER}} .steps-buttons button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);
        $obj->add_responsive_control('ea_woo_checkout_steps_btn_align', [
            'label' => __('Alignment', 'elementor'),
            'type' => Controls_Manager::CHOOSE,
            'options' => [
                'flex-start' => [
                    'title' => __('Left', 'elementor'),
                    'icon' => 'eicon-text-align-left',
                ],
                'center' => [
                    'title' => __('Center', 'elementor'),
                    'icon' => 'eicon-text-align-center',
                ],
                'flex-end' => [
                    'title' => __('Right', 'elementor'),
                    'icon' => 'eicon-text-align-right',
                ],
                'space-between' => [
                    'title' => __('Justified', 'elementor'),
                    'icon' => 'eicon-text-align-justify',
                ],
            ],
            'default' => 'flex-start',
            'selectors' => [
                '{{WRAPPER}} .steps-buttons' => 'justify-content: {{VALUE}};',
            ],
        ]);
        $obj->add_responsive_control('ea_woo_checkout_steps_btn_gap', [
            'label' => __('Gap', 'essential-addons-elementor'),
            'type' => Controls_Manager::SLIDER,
            'default' => [
                'size' => 10,
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '{{WRAPPER}} .steps-buttons button:first-child' => 'margin-right: calc({{SIZE}}{{UNIT}} / 2);',
                '{{WRAPPER}} .steps-buttons button:last-child' => 'margin-left: calc({{SIZE}}{{UNIT}} / 2);',
            ],
        ]);

        $obj->end_controls_section();
    }

    /**
     * Add ajax control
     *
     * @param Login_Register $lr
     */
    public function lr_init_content_ajax_controls($lr)
    {
        $lr->add_control('enable_ajax', [
            'label' => __('Submit Form via AJAX', 'essential-addons-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);

    }

    /**
     * Add spinner control
     *
     * @param Login_Register $lr
     */
    public function lr_init_content_spinner_controls($lr, $button_type)
    {
        $lr->add_control( "{$button_type}_btn_show_spinner", [
			'label'        => esc_html__( 'Show Spinner', 'essential-addons-for-elementor-lite' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'true',
			'default'      => '',
			'label_on'     => __( 'Show', 'essential-addons-for-elementor-lite' ),
			'label_off'    => __( 'Hide', 'essential-addons-for-elementor-lite' ),
		] );

		$lr->add_control( "{$button_type}_btn_spinner_note", [
			'type'            => Controls_Manager::RAW_HTML,
			'content_classes' => 'elementor-control-raw-html elementor-panel-alert elementor-panel-alert-info',
			'raw'             => esc_html__( 'In preview, Spinner is only visible after clicking on the button.', 'essential-addons-for-elementor-lite' ),
			'condition'       => [
				"{$button_type}_btn_show_spinner" => 'true',
			],
		] );

		$lr->add_control( "{$button_type}_btn_spinner_position", [
			'label'     => __( 'Position', 'essential-addons-for-elementor-lite' ),
			'type'      => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'%',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 100,
					'step' => 1,
				],
				'%'  => [
					'min' => 0,
					'max' => 100,
				],
			],
			'condition' => [
				"{$button_type}_btn_show_spinner" => 'true',
			],
			'selectors' => [
				"{{WRAPPER}} .eael-lr-form-loader-wrapper .eael-lr-form-loader.eael-lr-{$button_type}-form-loader" => 'right: {{SIZE}}{{UNIT}};',
			],
		] );

		$lr->add_control( "{$button_type}_btn_spinner_color", [
			'label'     => __( 'Color', 'essential-addons-for-elementor-lite' ),
			'type'      => Controls_Manager::COLOR,
			'condition' => [
				"{$button_type}_btn_show_spinner" => 'true',
			],
			'selectors' => [
				"{{WRAPPER}} .eael-lr-form-loader-wrapper .eael-lr-form-loader.eael-lr-{$button_type}-form-loader" => 'color: {{VALUE}};',
			],
		] );
    }
    
    /**
     * Add login button spinner control
     *
     * @param Login_Register $lr
     */
    public function lr_init_content_login_spinner_controls($lr)
    {
        $button_type = 'login';

        $this->lr_init_content_spinner_controls($lr, $button_type);
    }

    /**
     * Add register button spinner control
     *
     * @param Login_Register $lr
     */
    public function lr_init_content_register_spinner_controls($lr)
    {
        $button_type = 'register';

        $this->lr_init_content_spinner_controls($lr, $button_type);
    }

    /**
     * Add Social Login related controls
     *
     * @param Login_Register $lr
     */
    public function lr_init_content_social_login_controls($lr)
    {
        $lr->start_controls_section('section_content_social_login', [
            'label' => __('Social Login', 'essential-addons-elementor'),
            'conditions' => $lr->get_form_controls_display_condition('login'),
        ]);
        $lr->add_control('enable_google_login', [
            'label' => __('Enable Login with Google', 'essential-addons-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'render_type' => 'template',
        ]);
        if (empty(get_option('eael_g_client_id'))) {
            $lr->add_control('eael_g_client_id_missing', [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => sprintf(__('Google Client ID is missing. Please add it from %sDashboard >> Essential Addons >> Elements >> Login | Register Form %sSettings', 'essential-addons-elementor'), '<strong>', '</strong>'),
                'content_classes' => 'eael-warning',
                'condition' => [
                    'enable_google_login' => 'yes',
                ],
            ]);
        }
        $lr->add_control('enable_fb_login', [
            'label' => __('Enable Login with Facebook', 'essential-addons-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'render_type' => 'template',
        ]);
        if (empty(get_option('eael_fb_app_id')) || empty(get_option('eael_fb_app_secret'))) {
            $lr->add_control('eael_fb_app_id_missing', [
                'type' => Controls_Manager::RAW_HTML,
                'raw' => sprintf(__('Facebook API keys are missing. Please add them from %sDashboard >> Essential Addons >> Elements >> Login | Register Form %sSettings', 'essential-addons-elementor'), '<strong>', '</strong>'),
                'content_classes' => 'eael-warning',
                'condition' => [
                    'enable_fb_login' => 'yes',
                ],
            ]);
        }
        $lr->add_control('fb_login_text', [
            'label' => __('Text for Facebook Button', 'essential-addons-elementor'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default' => __('Login with Facebook', 'essential-addons-elementor'),
            'placeholder' => __('Login with Facebook', 'essential-addons-elementor'),
            'condition' => [
                'enable_fb_login' => 'yes',
            ],
        ]);

        $lr->add_control('enable_social_login_on_register_form', [
            'label' => __('Show on Register Form', 'essential-addons-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'render_type' => 'template',
        ]);

        $lr->add_control('show_separator', [
            'label' => __('Show Separator', 'essential-addons-elementor'),
            'type' => Controls_Manager::SWITCHER,
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [
                        'name' => "enable_fb_login",
                        'value' => 'yes',
                    ],
                    [
                        'name' => 'enable_google_login',
                        'value' => 'yes',
                    ],
                ],
            ],
        ]);

        $lr->add_control('separator_type', [
            'label' => __('Separator Type', 'essential-addons-elementor'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'hr' => __('Line', 'essential-addons-elementor'),
                'text' => __('Text', 'essential-addons-elementor'),
            ],
            'default' => 'hr',
            'condition' => [
                'show_separator' => 'yes',
            ],
        ]);

        $lr->add_control('separator_text', [
            'label' => __('Separator Text', 'essential-addons-elementor'),
            'type' => Controls_Manager::TEXT,
            'dynamic' => [
                'active' => true,
            ],
            'default' => __('Or', 'essential-addons-elementor'),
            'placeholder' => __('Eg. Or', 'essential-addons-elementor'),
            'condition' => [
                'separator_type' => 'text',
            ],
        ]);

        $lr->end_controls_section();
    }

    /**
     * It prints Social Login related markup
     *
     * @param Login_Register $lr
     */
    public function lr_print_social_login($lr, $form_type = 'login')
    {
        $form_type = in_array($form_type, ['login', 'register']) ? $form_type : 'login';

        $should_print_google = $should_print_fb = false;
        $gclient = $app_id = $fbtn_text = '';
        if ('yes' === $lr->get_settings_for_display('enable_google_login')) {
            $gclient = get_option('eael_g_client_id');
            $should_print_google = true;
        }

        if ('yes' === $lr->get_settings_for_display('enable_fb_login')) {
            $app_id = get_option('eael_fb_app_id');
            $should_print_fb = true;

            $fbtn_text = apply_filters('eael/login-register/fb-button-text', $lr->get_settings_for_display('fb_login_text'));

        }
        $show_sep = $lr->get_settings_for_display('show_separator');
        $sep_type = $lr->get_settings_for_display('separator_type');
        $sep_text = $lr->get_settings_for_display('separator_text');

        if ($should_print_google || $should_print_fb) {?>
            <div class="lr-social-login-container"
                 data-widget-id="<?php echo esc_attr($lr->get_id()); ?>">
				<?php
if ('yes' === $show_sep) {?>
                    <div class="lr-separator">
						<?php if ('hr' === $sep_type) {
            echo '<hr>';
        } elseif ('text' === $sep_type) {
            printf("<p>%s</p>", esc_html($sep_text));
        }?>
                    </div>
				<?php }?>
                <div class="lr-social-buttons-container">
					<?php
if ($should_print_google) {
            $this->lr_print_google_button($gclient, $lr, $form_type);
        }
            if ($should_print_fb) {
                $this->lr_print_facebook_button($app_id, $lr, $fbtn_text, $form_type);
            }
            ?>
                </div>
            </div>
			<?php
}
    }

    /**
     * It prints Social Login related markup
     *
     * @param Login_Register $lr
     */
    public function lr_print_social_login_on_register($lr) {
        if ('yes' === $lr->get_settings_for_display('enable_social_login_on_register_form')) {
            $this->lr_print_social_login($lr, 'register');
        }
    }

    /**
     * It prints google login button
     *
     * @param string         $client_id
     * @param Login_Register $lr
     */
    public function lr_print_google_button($client_id, $lr, $form_type = 'login')
    {
	    $form_type      = in_array( $form_type, [ 'login', 'register' ] ) ? $form_type : 'login';
	    $type           = $lr->get_settings_for_display( 'eael_sl_gis_btn_type' );
	    $theme          = $lr->get_settings_for_display( 'eael_sl_gis_btn_theme' );
	    $size           = $lr->get_settings_for_display( 'eael_sl_gis_btn_size' );
	    $text           = $lr->get_settings_for_display( 'eael_sl_gis_btn_text' );
	    $shape          = $lr->get_settings_for_display( 'eael_sl_gis_btn_shape' );
	    $logo_alignment = $lr->get_settings_for_display( 'eael_sl_gis_btn_logo_alignment' );
	    $locale         = $lr->get_settings_for_display( 'eael_sl_gis_btn_locale' );
	    $width          = $lr->get_settings_for_display( 'eael_sl_gis_btn_width' );
	    $width          = empty( $width['size'] ) ? '185px' : $width['size'] . 'px';
	    ?>
        <div class="eael-social-button eael-gis" id="eael-google-<?php echo esc_attr( $form_type ); ?>-btn-<?php echo esc_attr( $lr->get_id() ); ?>"
             data-g-client-id="<?php echo esc_attr( $client_id ); ?>" data-type="<?php echo esc_attr( $type ); ?>" data-theme="<?php echo esc_attr( $theme ); ?>"
             data-size="<?php echo esc_attr( $size ); ?>" data-text="<?php echo esc_attr( $text ); ?>" data-shape="<?php echo esc_attr( $shape ); ?>"
             data-logo_alignment="<?php echo esc_attr( $logo_alignment ); ?>" data-width="<?php echo esc_attr( $width ); ?>" data-locale="<?php echo esc_attr( $locale ); ?>">
        </div>
		<?php
}

    /**
     * It prints facebook login button
     *
     * @param string         $app_id
     * @param Login_Register $lr
     * @param string         $btn_text
     */
    public function lr_print_facebook_button($app_id, $lr, $btn_text = '', $form_type = 'login')
    {
        $form_type = in_array($form_type, ['login', 'register']) ? $form_type : 'login';

        ?>
        <div class="eael-social-button eael-facebook"
             id="eael-fb-<?php echo esc_attr($form_type); ?>-btn-<?php echo esc_attr($lr->get_id()); ?>"
             data-fb-appid="<?php echo esc_attr($app_id); ?>">
            <svg xmlns="http://www.w3.org/2000/svg"
                 width="18px"
                 height="18px"
                 viewBox="0 0 216 216"
                 class="_5h0m"
                 fill="#4d6fa9">
                <path d="M204.1 0H11.9C5.3 0 0 5.3 0 11.9v192.2c0 6.6 5.3 11.9 11.9 11.9h103.5v-83.6H87.2V99.8h28.1v-24c0-27.9 17-43.1 41.9-43.1 11.9 0 22.2.9 25.2 1.3v29.2h-17.3c-13.5 0-16.2 6.4-16.2 15.9v20.8h32.3l-4.2 32.6h-28V216h55c6.6 0 11.9-5.3 11.9-11.9V11.9C216 5.3 210.7 0 204.1 0z"></path>
            </svg>
			<?php
if ($btn_text) {
            printf("<span class='eael-fbtn-text'>%s</span>", esc_html($btn_text));
        }
        ?>
        </div>
        <?php
    }

	public function lr_handle_social_login() {
		// verify security for social login
		if ( ! empty( $_POST['eael-google-submit'] ) || ! empty( $_POST['eael-facebook-submit'] ) ) {
			check_ajax_referer( 'essential-addons-elementor', 'nonce' );
			if ( is_user_logged_in() ) {
				wp_send_json_error( __( 'You are already logged in.', 'essential-addons-elementor' ) );
			}
		}


		if ( ! empty( $_POST['eael-google-submit'] ) ) {
			$client_id     = get_option( 'eael_g_client_id' );
			$id_token      = ! empty( $_POST['id_token'] ) ? sanitize_text_field( $_POST['id_token'] ) : '';
			$verified_data = $this->lr_verify_google_user_data( $id_token, $client_id );

			if ( empty( $verified_data ) ) {
				wp_send_json_error( __( 'User data was not verified by Google', 'essential-addons-elementor' ) );
			}
			// verified data
			$v_name      = isset( $verified_data['name'] ) ? $verified_data['name'] : '';
			$v_email     = isset( $verified_data['email'] ) ? $verified_data['email'] : '';
			$v_client_id = isset( $verified_data['aud'] ) ? $verified_data['aud'] : '';

			// Check if email is verified with Google.
			if ( ( $client_id !== $v_client_id ) ) {
				wp_send_json_error( __( 'User data was not verified by Google', 'essential-addons-elementor' ) );
			}

			$this->lr_log_user_using_social_data( $v_name, $v_email, 'google' );
		}

		if ( ! empty( $_POST['eael-facebook-submit'] ) ) {

			$app_id       = get_option( 'eael_fb_app_id' );
			$app_secret   = get_option( 'eael_fb_app_secret' );
			$access_token = ! empty( $_POST['access_token'] ) ? sanitize_text_field( $_POST['access_token'] ) : '';
			$user_id      = ! empty( $_POST['user_id'] ) ? sanitize_text_field( $_POST['user_id'] ) : 0;
			$name         = isset( $_POST['full_name'] ) ? sanitize_text_field( $_POST['full_name'] ) : '';
			$email        = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
			if ( empty( $user_id ) ) {
				wp_send_json_error( __( 'Facebook authorization failed', 'essential-addons-elementor' ) );
			}

			$fb_user_data = $this->lr_get_facebook_user_profile( $access_token, $app_id, $app_secret );


			if ( empty( $user_id ) || empty( $fb_user_data ) || empty( $app_id ) || empty( $app_secret ) || ( $user_id !== $fb_user_data['data']['user_id'] ) || ( $app_id !== $fb_user_data['data']['app_id'] ) || ( ! $fb_user_data['data']['is_valid'] ) ) {
				wp_send_json_error( __( 'Facebook authorization failed', 'essential-addons-elementor' ) );
			}


			$res = $this->lr_get_user_email_facebook( $fb_user_data['data']['user_id'], $access_token );
			//Some facebook user may not have $email as they might have used mobile number to open account
			if ( ! empty( $email ) && ( empty( $res['email'] ) || $res['email'] !== $email ) ) {
				//if js SDK sends email, then php api must return the same email.
				wp_send_json_error( __( 'Facebook email validation failed', 'essential-addons-elementor' ) );
			}

			$v_email = ! empty( $email ) && ! empty( $res['email'] ) ? sanitize_email( $res['email'] ) : $fb_user_data['data']['user_id'] . '@facebook.com';

			$this->lr_log_user_using_social_data( $name, $v_email, 'facebook' );

		}

	}

	/**
	 * @param string $name
	 * @param string $email
	 * @param string $login_source eg. Google, Facebook etc.
	 */
	public function lr_log_user_using_social_data( $name, $email, $login_source = '' ) {
		$response             = [];
		$username             = strtolower( preg_replace( '/\s+/', '', $name ) );
		$response['username'] = $username;
		$user_data            = get_user_by( 'email', $email ); // do we have user by this email already?

		if ( ! empty( $user_data ) ) {
			//user already registered using this email, so log him in.
			$user_ID = $user_data->ID;
			wp_set_auth_cookie( $user_ID );
			wp_set_current_user( $user_ID, $username );
			do_action( 'wp_login', $user_data->user_login, $user_data );

		} else {
			$users_can_register = get_option( 'users_can_register' );
			if ( ! $users_can_register ) {
				wp_send_json_error( __( 'User not registered', 'essential-addons-elementor' ) );
			}

			// user is new, so let's register him
			$password = wp_generate_password( 12, true, false );

			if ( username_exists( $username ) ) {
				// Generate something unique to append to the username in case of a conflict with another user.
				$suffix   = '-' . zeroise( wp_rand( 0, 9999 ), 4 );
				$username .= $suffix;
			}
			$user_array = [
				'user_login' => $username,
				'user_pass'  => $password,
				'user_email' => $email,
			];
			$_name      = explode( " ", $name, 2 );
			if ( isset( $_name[0] ) ) {
				$user_array['first_name'] = $_name[0];
			}
			if ( isset( $_name[1] ) ) {
				$user_array['last_name'] = $_name[1];
			}
			$result = wp_insert_user( $user_array );
			if ( is_wp_error( $result ) ) {
				wp_send_json_error( __( 'Logging user failed.', 'essential-addons-elementor' ) );
			}

			//@TODO; send email to user/admin later
			//do_action( 'edit_user_created_user', $result, $notify );

			$user_data = get_user_by( 'email', $email );

			if ( $user_data ) {
				$user_ID   = $user_data->ID;
				$user_meta = [
					'login_source' => $login_source,
				];

				update_user_meta( $user_ID, 'eael_login_form', $user_meta );

				if ( wp_check_password( $password, $user_data->user_pass, $user_data->ID ) ) {
					wp_set_auth_cookie( $user_ID );
					wp_set_current_user( $user_ID, $username );
					do_action( 'wp_login', $user_data->user_login, $user_data );
				} else {
					wp_send_json_error( __( 'Logging user failed.', 'essential-addons-elementor' ) );
				}
			}
		}
		$response ['message'] = __( 'You are logged in successfully', 'essential-addons-elementor' );
		if ( ! empty( $_POST['redirect_to'] ) ) {
			$response['redirect_to'] = esc_url( $_POST['redirect_to'] );
		}

		wp_send_json_success( $response );
	}

	/**
	 * It verifies id token generated or sent from google js sdk
	 *
	 * @param string $id_token  id token generated via google js sdk
	 * @param string $client_id the client api key generated in the google console
	 *
	 * @return array|false
	 */
	public function lr_verify_google_user_data( $id_token, $client_id ) {
		// load composer autoloader
		$composer_autoloader = EAEL_PRO_PLUGIN_PATH . 'vendor/autoload.php';
		if ( file_exists( $composer_autoloader ) ) {
			require_once $composer_autoloader;
		}

		if ( ! class_exists( '\Google_Client' ) ) {
			error_log( 'Google_client class was not loaded. did you run composer install?' );

			return false;
		}
		$client        = new Google_Client( [ 'client_id' => $client_id ] );
		$verified_data = $client->verifyIdToken( $id_token );
		if ( $verified_data ) {
			return $verified_data;
		}

		return false;
	}

	/**
	 * Get facebook user profile
	 *
	 * @param string $access_token Access Token.
	 * @param string $app_id       App ID.
	 * @param string $app_secret   Secret token.
	 *
	 * @return mixed
	 */
	public function lr_get_facebook_user_profile( $access_token, $app_id, $app_secret ) {

		$fb_url = 'https://graph.facebook.com/oauth/access_token';
		$fb_url = add_query_arg( [
			'client_id'     => $app_id,
			'client_secret' => $app_secret,
			'grant_type'    => 'client_credentials',
		], $fb_url );

		$fb_response = wp_remote_get( $fb_url );

		if ( is_wp_error( $fb_response ) ) {
			wp_send_json_error();
		}

		$fb_app_response = json_decode( wp_remote_retrieve_body( $fb_response ), true );

		$app_token = $fb_app_response['access_token'];

		$url = 'https://graph.facebook.com/debug_token';
		$url = add_query_arg( [
			'input_token'  => $access_token,
			'access_token' => $app_token,
		], $url );

		$response = wp_remote_get( $url );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		return json_decode( wp_remote_retrieve_body( $response ), true );
	}

	/**
	 * Get user email of authenticated facebook user
	 *
	 * @param string $user_id      User ID.
	 * @param string $access_token User Access Token.
	 *
	 * @return mixed
	 */
	public function lr_get_user_email_facebook( $user_id, $access_token ) {
		$fb_email_url = 'https://graph.facebook.com/' . $user_id;
		$fb_email_url = add_query_arg( [
			'fields'       => 'email',
			'access_token' => $access_token,
		], $fb_email_url );

		$email_response = wp_remote_get( $fb_email_url );

		if ( is_wp_error( $email_response ) ) {
			return false;
		}

		return json_decode( wp_remote_retrieve_body( $email_response ), true );
	}

	/**
	 * It adds styling controls for social login
	 *
	 * @param Login_Register $lr
	 */
	public function lr_init_style_social_controls( Login_Register $lr ) {
		$lr->start_controls_section( 'section_style_social_login', [
			'label'      => __( 'Social Login Style', 'essential-addons-for-elementor' ),
			'tab'        => Controls_Manager::TAB_STYLE,
			'conditions' => [
				'relation' => 'or',
				'terms'    => [
					[
						'name'  => "enable_fb_login",
						'value' => 'yes',
					],
					[
						'name'  => 'enable_google_login',
						'value' => 'yes',
					],
				],
			],
		] );
		$container = "{{WRAPPER}} .lr-social-login-container";
		$lr->add_control( 'eael_sl_pot', [
			'label'        => __( 'Social Container', 'essential-addons-for-elementor' ),
			'type'         => Controls_Manager::POPOVER_TOGGLE,
			'label_off'    => __( 'Default', 'essential-addons-for-elementor' ),
			'label_on'     => __( 'Custom', 'essential-addons-for-elementor' ),
			'return_value' => 'yes',
		] );
		$lr->start_popover();
		$lr->add_responsive_control( "eael_sl_wrap_width", [
			'label'      => esc_html__( 'Width', 'essential-addons-for-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'%',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 1000,
					'step' => 5,
				],
				'%'  => [
					'min' => 0,
					'max' => 100,
				],
			],
			'default'    => [
				'unit' => '%',
				'size' => 100,
			],
			'selectors'  => [
				$container => 'width: {{SIZE}}{{UNIT}};',
			],
			'condition'  => [
				'eael_sl_pot' => 'yes',
			],
		] );

		$lr->add_responsive_control( "eael_sl_wrap_height", [
			'label'      => esc_html__( 'Height', 'essential-addons-for-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'%',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 1000,
					'step' => 5,
				],
				'%'  => [
					'min' => 0,
					'max' => 100,
				],
			],
			'selectors'  => [
				$container => 'height: {{SIZE}}{{UNIT}};',
			],
			'condition'  => [
				'eael_sl_pot' => 'yes',
			],
		] );

		$lr->add_responsive_control( "eael_sl_wrap_margin", [
			'label'      => __( 'Margin', 'essential-addons-for-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				$container => $lr->apply_dim( 'margin' ),
			],
			'condition'  => [
				'eael_sl_pot' => 'yes',
			],
		] );
		$lr->add_responsive_control( "eael_sl_wrap_padding", [
			'label'      => __( 'Padding', 'essential-addons-for-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				$container => $lr->apply_dim( 'padding' ),
			],
			'condition'  => [
				'eael_sl_pot' => 'yes',
			],
		] );
		$lr->add_group_control( Group_Control_Border::get_type(), [
			'name'      => "eael_sl_wrap_border",
			'selector'  => $container,
			'condition' => [
				'eael_sl_pot' => 'yes',
			],
		] );
		$lr->add_control( "eael_sl_wrap_border_radius", [
			'label'      => __( 'Border Radius', 'essential-addons-for-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				$container => $lr->apply_dim( 'border-radius' ),
			],
			'condition'  => [
				'eael_sl_pot' => 'yes',
			],
			'separator'  => 'after',
		] );
		$lr->add_control( "eael_sl_wrap_text_color", [
			'label'     => __( 'Text Color', 'essential-addons-for-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$container => 'color: {{VALUE}};',
			],
			'condition' => [
				'eael_sl_pot' => 'yes',
			],
			'separator' => 'before',
		] );
		$lr->add_group_control( Group_Control_Background::get_type(), [
			'name'      => "eael_sl_wrap_bg_color",
			'label'     => __( 'Background Color', 'essential-addons-for-elementor' ),
			'types'     => [
				'classic',
				'gradient',
			],
			'selector'  => $container,
			'condition' => [
				'eael_sl_pot' => 'yes',
			],
		] );
		$lr->end_popover();

		$lr->add_responsive_control( "eael_sl_btn_display_type", [
			'label'     => __( 'Display Button as', 'essential-addons-for-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'options'   => [
				'row'    => __( 'Inline', 'essential-addons-for-elementor' ),
				'column' => __( 'Block', 'essential-addons-for-elementor' ),
			],
			'default'   => 'row',
			'selectors' => [
				"{$container} .lr-social-buttons-container" => 'flex-direction: {{VALUE}};',
			],
		] );
		//Social Buttons
		$this->lr_social_gis_btn_controlls( $lr );
		$this->lr_init_style_social_btn_controls( $lr, 'facebook' );
		// Separator
		$this->lr_init_style_social_separator_controls( $lr );
		$lr->end_controls_section();
	}
    
    /**
	 * It adds styling controls for mailchimp integration to Login | Register Form
	 *
	 * @param Login_Register $lr
	 */
	public function lr_init_mailchimp_integration_controls( Login_Register $lr ) {
		$lr->add_control( 'eael_register_mailchimp_integration_enable', [
            'label'        => __( 'Enable Mailchimp Integration', 'essential-addons-elementor' ),
            'description'  => __( 'Enable to create new Mailchimp audience contact on each user registration.', 'essential-addons-elementor' ),
            'type'         => Controls_Manager::SWITCHER,
            'label_on'     => __( 'Yes', 'essential-addons-elementor' ),
            'label_off'    => __( 'No', 'essential-addons-elementor' ),
            'return_value' => 'yes',
        ] );
        
        $lr->add_control(
            'eael_mailchimp_lists',
            [
                'label' => esc_html__('Mailchimp List', 'essential-addons-elementor'),
                'type' => Controls_Manager::SELECT,
                'label_block' => false,
                'description' => __( sprintf('Set your API Key from <a class="elementor-control-field-description" href="%s" target="_blank"><strong>EA Dashboard &raquo; Elements &raquo; Login | Register Form Settings</strong></a>', site_url('/wp-admin/admin.php?page=eael-settings') ), 'essential-addons-elementor' ),
                'options' => Helper::mailchimp_lists('login-register-form'),
                'condition' => [
                    'eael_register_mailchimp_integration_enable' => 'yes',
                ],
            ]
        );
	}

	/**
     * GIS button new controllers
     *
	 * @param Login_Register $lr
	 *
	 * @return void
	 */
	public function lr_social_gis_btn_controlls( Login_Register $lr ) {
		$lr->add_control( "eael_sl_gis_btn_pot", [
			'label'        => __( 'Google Button', 'essential-addons-elementor' ),
			'type'         => Controls_Manager::POPOVER_TOGGLE,
			'label_off'    => __( 'Default', 'essential-addons-elementor' ),
			'label_on'     => __( 'Custom', 'essential-addons-elementor' ),
			'return_value' => 'yes',
			'separator'    => 'before',
			'condition'    => [ 'enable_google_login' => 'yes' ],
		] );

		$lr->start_popover();

		$lr->add_control( "eael_sl_gis_btn_heading", [
			'label'     => __( 'Button Style', 'essential-addons-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );

		$lr->add_control( "eael_sl_gis_btn_type", [
			'label'   => __( 'Type', 'essential-addons-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'standard' => __( 'Standard', 'essential-addons-elementor' ),
				'icon'     => __( 'Icon', 'essential-addons-elementor' ),
			],
			'default' => 'standard',
		] );

		$lr->add_control( "eael_sl_gis_btn_theme", [
			'label'   => __( 'Theme', 'essential-addons-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'outline'      => __( 'Outline', 'essential-addons-elementor' ),
				'filled_blue'  => __( 'Filled Blue', 'essential-addons-elementor' ),
				'filled_black' => __( 'Filled Black', 'essential-addons-elementor' ),
			],
			'default' => 'outline',
		] );

		$lr->add_control( "eael_sl_gis_btn_size", [
			'label'   => __( 'Size', 'essential-addons-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'large'  => __( 'Large', 'essential-addons-elementor' ),
				'medium' => __( 'Medium', 'essential-addons-elementor' ),
				'small'  => __( 'Small', 'essential-addons-elementor' ),
			],
			'default' => 'large',
		] );

		$lr->add_control( "eael_sl_gis_btn_text", [
			'label'   => __( 'Text', 'essential-addons-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'signin_with'   => __( 'Sign in with Google', 'essential-addons-elementor' ),
				'signup_with'   => __( 'Sign up with Google', 'essential-addons-elementor' ),
				'continue_with' => __( 'Continue with Google', 'essential-addons-elementor' ),
				'signin'        => __( 'Sign in', 'essential-addons-elementor' ),
			],
			'default' => 'signin_with',
		] );

		$lr->add_control( "eael_sl_gis_btn_shape", [
			'label'   => __( 'Shape', 'essential-addons-elementor' ),
			'type'    => Controls_Manager::SELECT,
			'options' => [
				'rectangular' => __( 'Rectangular', 'essential-addons-elementor' ),
				'pill'        => __( 'Pill', 'essential-addons-elementor' ),
				'circle'      => __( 'Circle', 'essential-addons-elementor' ),
				'square'      => __( 'Square', 'essential-addons-elementor' ),
			],
			'default' => 'rectangular',
		] );

		$lr->add_control( "eael_sl_gis_btn_logo_alignment", [
			'label'     => __( 'Logo Alignment', 'essential-addons-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'options'   => [
				'left'   => __( 'Left', 'essential-addons-elementor' ),
				'center' => __( 'Center', 'essential-addons-elementor' ),
			],
			'default'   => 'left',
			'condition' => [ 'eael_sl_gis_btn_type' => 'standard' ],
		] );

		$lr->add_control( "eael_sl_gis_btn_width", [
			'label'      => esc_html__( 'Width', 'essential-addons-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 280,
					'step' => 5,
				],
			],
			'default'    => [
				'unit' => 'px',
				'size' => 185,
			],
		] );

		$lr->add_control( "eael_sl_gis_btn_locale", [
			'label'   => __( 'Locale', 'essential-addons-elementor' ),
			'type'    => Controls_Manager::SELECT2,
			'options' => [
				"am"    => "Amharic",
				"ar"    => "Arabic",
				"eu"    => "Basque",
				"bn"    => "Bengali",
				"en-GB" => "English (UK)",
				"pt-BR" => "Portuguese (Brazil)",
				"bg"    => "Bulgarian",
				"ca"    => "Catalan",
				"chr"   => "Cherokee",
				"hr"    => "Croatian",
				"cs"    => "Czech",
				"da"    => "Danish",
				"nl"    => "Dutch",
				"en"    => "English (US)",
				"et"    => "Estonian",
				"fil"   => "Filipino",
				"fi"    => "Finnish",
				"fr"    => "French",
				"de"    => "German",
				"el"    => "Greek",
				"gu"    => "Gujarati",
				"iw"    => "Hebrew",
				"hi"    => "Hindi",
				"hu"    => "Hungarian",
				"is"    => "Icelandic",
				"id"    => "Indonesian",
				"it"    => "Italian",
				"ja"    => "Japanese",
				"kn"    => "Kannada",
				"ko"    => "Korean",
				"lv"    => "Latvian",
				"lt"    => "Lithuanian",
				"ms"    => "Malay",
				"ml"    => "Malayalam",
				"mr"    => "Marathi",
				"no"    => "Norwegian",
				"pl"    => "Polish",
				"pt-PT" => "Portuguese (Portugal)",
				"ro"    => "Romanian",
				"ru"    => "Russian",
				"sr"    => "Serbian",
				"zh-CN" => "Chinese (PRC)",
				"sk"    => "Slovak",
				"sl"    => "Slovenian",
				"es"    => "Spanish",
				"sw"    => "Swahili",
				"sv"    => "Swedish",
				"ta"    => "Tamil",
				"te"    => "Telugu",
				"th"    => "Thai",
				"zh-TW" => "Chinese (Taiwan)",
				"tr"    => "Turkish",
				"ur"    => "Urdu",
				"uk"    => "Ukrainian",
				"vi"    => "Vietnamese",
				"cy"    => "Welsh",
			],
			'default' => 'en',
		] );

		$lr->end_popover();
	}

	/**
	 * @param Login_Register $lr
	 * @param string         $btn_type
	 */
	public function lr_init_style_social_btn_controls( Login_Register $lr, $btn_type = 'google' ) {
		$btn_class  = "{{WRAPPER}} .lr-social-login-container .eael-social-button.eael-{$btn_type}";
		$icon_class = "{$btn_class} svg";
		$width      = 'google' === $btn_type ? 175 : 190;

		$condition_name = 'facebook' === $btn_type ? 'enable_fb_login' : "enable_{$btn_type}_login";

		$lr->add_control( "eael_sl_{$btn_type}_btn_pot", [
			'label'        => sprintf( __( '%s Button', 'essential-addons-for-elementor' ), ucfirst( $btn_type ) ),
			'type'         => Controls_Manager::POPOVER_TOGGLE,
			'label_off'    => __( 'Default', 'essential-addons-for-elementor' ),
			'label_on'     => __( 'Custom', 'essential-addons-for-elementor' ),
			'return_value' => 'yes',
			'separator'    => 'before',
			'condition'    => [ $condition_name => 'yes' ],
		] );

		$lr->start_popover();
		$lr->add_control( "eael_sl_{$btn_type}_btn_heading", [
			'label'     => __( 'Button Style', 'essential-addons-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
			'condition' => [
				"eael_sl_{$btn_type}_btn_pot" => 'yes',
			],
		] );
		$lr->add_responsive_control( "eael_sl_{$btn_type}_btn_width", [
			'label'      => esc_html__( 'Button Width', 'essential-addons-for-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'%',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 1000,
					'step' => 5,
				],
				'%'  => [
					'min' => 0,
					'max' => 100,
				],
			],
			'default'    => [
				'unit' => 'px',
				'size' => $width,
			],
			'selectors'  => [
				$btn_class => 'width: {{SIZE}}{{UNIT}};',
			],
			'condition'  => [
				"eael_sl_{$btn_type}_btn_pot" => 'yes',
			],
		] );
		$lr->add_responsive_control( "eael_sl_{$btn_type}_btn_height", [
			'label'      => esc_html__( 'Button Height', 'essential-addons-for-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'%',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 1000,
					'step' => 5,
				],
				'%'  => [
					'min' => 0,
					'max' => 100,
				],
			],
			'selectors'  => [
				$btn_class => 'height: {{SIZE}}{{UNIT}};',
			],
			'condition'  => [
				"eael_sl_{$btn_type}_btn_pot" => 'yes',
			],
		] );
		$lr->add_responsive_control( "eael_sl_{$btn_type}_btn_margin", [
			'label'      => __( 'Margin', 'essential-addons-for-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				$btn_class => $lr->apply_dim( 'margin' ),
			],
			'condition'  => [
				"eael_sl_{$btn_type}_btn_pot" => 'yes',
			],
		] );
		$lr->add_responsive_control( "eael_sl_{$btn_type}_btn_padding", [
			'label'      => __( 'Padding', 'essential-addons-for-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				$btn_class => $lr->apply_dim( 'padding' ),
			],
			'condition'  => [
				"eael_sl_{$btn_type}_btn_pot" => 'yes',
			],
		] );
		$lr->add_group_control( Group_Control_Border::get_type(), [
			'name'      => "eael_sl_{$btn_type}_btn_border",
			'selector'  => $btn_class,
			'condition' => [
				"eael_sl_{$btn_type}_btn_pot" => 'yes',
			],
		] );
		$lr->add_control( "eael_sl_{$btn_type}_btn_border_radius", [
			'label'      => __( 'Border Radius', 'essential-addons-for-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				$btn_class => $lr->apply_dim( 'border-radius' ),
			],
			'condition'  => [
				"eael_sl_{$btn_type}_btn_pot" => 'yes',
			],
			'separator'  => 'after',
		] );
		$lr->add_control( "eael_sl_{$btn_type}_btn_text_color", [
			'label'     => __( 'Text Color', 'essential-addons-for-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$btn_class => 'color: {{VALUE}};',
			],
			'condition' => [
				"eael_sl_{$btn_type}_btn_pot" => 'yes',
			],
			'separator' => 'before',
		] );
		$lr->add_group_control( Group_Control_Background::get_type(), [
			'name'      => "eael_sl_{$btn_type}_btn_bg_color",
			'label'     => __( 'Background Color', 'essential-addons-for-elementor' ),
			'types'     => [
				'classic',
				'gradient',
			],
			'selector'  => $btn_class,
			'condition' => [
				"eael_sl_{$btn_type}_btn_pot" => 'yes',
			],
		] );

		$lr->end_popover();
		$lr->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => "eael_sl_{$btn_type}_btn_typo",
			'label'    => sprintf( __( '%s Button Typography', 'essential-addons-for-elementor' ), ucfirst( $btn_type ) ),
			'selector' => $btn_class,
		] );

		// Button icon
		$lr->add_control( "eael_sl_{$btn_type}_icon_pot", [
			'label'        => sprintf( __( '%s  Button Icon', 'essential-addons-for-elementor' ), ucfirst( $btn_type ) ),
			'type'         => Controls_Manager::POPOVER_TOGGLE,
			'label_off'    => __( 'Default', 'essential-addons-for-elementor' ),
			'label_on'     => __( 'Custom', 'essential-addons-for-elementor' ),
			'return_value' => 'yes',
		] );
		$lr->start_popover();
		$lr->add_responsive_control( "eael_sl_{$btn_type}_icon_width", [
			'label'      => esc_html__( 'Icon Width', 'essential-addons-for-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'%',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 150,
					'step' => 1,
				],
				'%'  => [
					'min' => 0,
					'max' => 100,
				],
			],
			'default'    => [
				'unit' => 'px',
				'size' => 18,
			],
			'selectors'  => [
				$icon_class => 'width: {{SIZE}}{{UNIT}};',
			],
			'condition'  => [
				"eael_sl_{$btn_type}_icon_pot" => 'yes',
			],
		] );
		$lr->add_responsive_control( "eael_sl_{$btn_type}_icon_height", [
			'label'      => esc_html__( 'Icon Height', 'essential-addons-for-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'%',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 150,
					'step' => 1,
				],
				'%'  => [
					'min' => 0,
					'max' => 100,
				],
			],
			'default'    => [
				'unit' => 'px',
				'size' => 18,
			],
			'selectors'  => [
				$icon_class => 'height: {{SIZE}}{{UNIT}};',
			],
			'condition'  => [
				"eael_sl_{$btn_type}_icon_pot" => 'yes',
			],
		] );
		$lr->add_responsive_control( "eael_sl_{$btn_type}_icon_margin", [
			'label'      => __( 'Margin', 'essential-addons-for-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				$icon_class => $lr->apply_dim( 'margin' ),
			],
			'condition'  => [
				"eael_sl_{$btn_type}_icon_pot" => 'yes',
			],
		] );
		$lr->add_responsive_control( "eael_sl_{$btn_type}_icon_padding", [
			'label'      => __( 'Padding', 'essential-addons-for-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				$icon_class => $lr->apply_dim( 'padding' ),
			],
			'condition'  => [
				"eael_sl_{$btn_type}_icon_pot" => 'yes',
			],
		] );
		$lr->add_group_control( Group_Control_Background::get_type(), [
			'name'      => "eael_sl_{$btn_type}_icon_bg_color",
			'label'     => __( 'Background Color', 'essential-addons-for-elementor' ),
			'types'     => [
				'classic',
				'gradient',
			],
			'selector'  => $icon_class,
			'condition' => [
				"eael_sl_{$btn_type}_icon_pot" => 'yes',
			],
		] );
		$lr->add_group_control( Group_Control_Border::get_type(), [
			'name'      => "eael_sl_{$btn_type}_icon_border",
			'selector'  => $icon_class,
			'condition' => [
				"eael_sl_{$btn_type}_btn_pot" => 'yes',
			],
		] );
		$lr->add_control( "eael_sl_{$btn_type}_icon_border_radius", [
			'label'      => __( 'Border Radius', 'essential-addons-for-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				$icon_class => $lr->apply_dim( 'border-radius' ),
			],
			'condition'  => [
				"eael_sl_{$btn_type}_btn_pot" => 'yes',
			],
			'separator'  => 'after',
		] );
		$lr->end_popover();
	}

	/**
	 * @param Login_Register $lr
	 */
	public function lr_init_style_social_separator_controls( Login_Register $lr ) {
		$sep_class = '{{WRAPPER}} .lr-social-login-container .lr-separator';
		$sep_text  = '{{WRAPPER}} .lr-social-login-container .lr-separator p';
		$sep_hr    = '{{WRAPPER}} .lr-social-login-container .lr-separator hr';
		$lr->add_control( 'eael_sl_sep_pot', [
			'label'        => __( 'Separator', 'essential-addons-for-elementor' ),
			'type'         => Controls_Manager::POPOVER_TOGGLE,
			'label_off'    => __( 'Default', 'essential-addons-for-elementor' ),
			'label_on'     => __( 'Custom', 'essential-addons-for-elementor' ),
			'return_value' => 'yes',
			'separator'    => 'before',
			'condition'    => [
				'show_separator' => 'yes',
			],
		] );
		$lr->start_popover();
		$lr->add_responsive_control( "eael_sl_sep_width", [
			'label'      => esc_html__( 'Divider Width', 'essential-addons-for-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'%',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 1000,
					'step' => 5,
				],
				'%'  => [
					'min' => 0,
					'max' => 100,
				],
			],
			'default'    => [
				'unit' => '%',
				'size' => 100,
			],
			'selectors'  => [
				$sep_hr => 'width: {{SIZE}}{{UNIT}};',
			],
			'condition'  => [
				'eael_sl_sep_pot' => 'yes',
				'separator_type'  => 'hr',
			],
		] );

		$lr->add_responsive_control( "eael_sl_sep_height", [
			'label'      => esc_html__( 'Divider Height', 'essential-addons-for-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'%',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 20,
					'step' => 5,
				],
				'%'  => [
					'min' => 0,
					'max' => 100,
				],
			],
			'selectors'  => [
				$sep_hr => 'height: {{SIZE}}{{UNIT}};',
			],
			'condition'  => [
				'eael_sl_sep_pot' => 'yes',
				'separator_type'  => 'hr',
			],
		] );

		$lr->add_responsive_control( "eael_sl_sep_margin", [
			'label'      => __( 'Margin', 'essential-addons-for-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				$sep_text => $lr->apply_dim( 'margin' ),
				$sep_hr   => $lr->apply_dim( 'margin' ),
			],
			'condition'  => [
				'eael_sl_sep_pot' => 'yes',
			],
		] );
		$lr->add_responsive_control( "eael_sl_sep_padding", [
			'label'      => __( 'Padding', 'essential-addons-for-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				$sep_text => $lr->apply_dim( 'padding' ),
				$sep_hr   => $lr->apply_dim( 'padding' ),
			],
			'condition'  => [
				'eael_sl_sep_pot' => 'yes',
			],
		] );
		$lr->add_group_control( Group_Control_Border::get_type(), [
			'name'      => "eael_sl_sep_border",
			'selector'  => $sep_text,
			'condition' => [
				'eael_sl_sep_pot' => 'yes',
				'separator_type'  => 'text',
			],
		] );
		$lr->add_control( "eael_sl_sep_border_radius", [
			'label'      => __( 'Border Radius', 'essential-addons-for-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				$sep_hr   => $lr->apply_dim( 'border-radius' ),
				$sep_text => $lr->apply_dim( 'border-radius' ),
			],
			'condition'  => [
				'eael_sl_sep_pot' => 'yes',
			],
			'separator'  => 'after',
		] );
		$lr->add_control( "eael_sl_sep_text_color", [
			'label'     => __( 'Color', 'essential-addons-for-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => [
				$sep_text => 'color: {{VALUE}};',
			],
			'condition' => [
				'eael_sl_sep_pot' => 'yes',
				'separator_type'  => 'text',
			],
			'separator' => 'before',
		] );
		$lr->add_group_control( Group_Control_Background::get_type(), [
			'name'      => "eael_sl_sep_bg_color",
			'label'     => __( 'Background Color', 'essential-addons-for-elementor' ),
			'types'     => [
				'classic',
				'gradient',
			],
			'selector'  => $sep_text . ', ' . $sep_hr,
			'condition' => [
				'eael_sl_sep_pot' => 'yes',
			],
		] );
		$lr->end_popover();
		$lr->add_group_control( Group_Control_Typography::get_type(), [
			'name'      => "eael_sl_sep_typo",
			'label'     => __( 'Separator Typography', 'essential-addons-for-elementor' ),
			'selector'  => $sep_text,
			'condition' => [
				'separator_type' => 'text',
			],
		] );
	}

	/**
	 * @param array $scripts
	 *
	 * @return array
	 */
	public function lr_load_pro_scripts( $scripts ) {
		array_push( $scripts, 'password-strength-meter' );

		return $scripts;
	}

	/**
	 * @param array $styles
	 *
	 * @return array
	 */
	public function lr_load_pro_styles( array $styles ) {
		return array_merge( $styles, [
			'font-awesome-5-all',
			'font-awesome-4-shim',
		] );
	}

	/**
	 * @param Login_Register $lr
	 */
	public function lr_init_content_pass_strength_controls( Login_Register $lr ) {
		$lr->add_control( 'show_register_icon', [
			'label' => __( 'Show Field Icons', 'essential-addons-elementor' ),
			'type'  => Controls_Manager::SWITCHER,
		] );
		$lr->add_control( 'show_ps_meter', [
			'label' => __( 'Show Password Strength Meter', 'essential-addons-elementor' ),
			'type'  => Controls_Manager::SWITCHER,
		] );
		$lr->add_control( 'show_pass_strength', [
			'label' => __( 'Show Password Strength Text', 'essential-addons-elementor' ),
			'type'  => Controls_Manager::SWITCHER,
		] );
		$lr->add_control( 'ps_text_type', [
			'label'     => __( 'Password Strength Text', 'essential-addons-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'default',
			'options'   => [
				'default' => __( 'Default', 'essential-addons-elementor' ),
				'custom'  => __( 'Custom', 'essential-addons-elementor' ),
			],
			'condition' => [
				'show_pass_strength' => 'yes',
			],
		] );
		$pass_type = [
			'short'  => __( 'Very Weak', 'essential-addons-elementor' ),
			'bad'    => __( 'Weak', 'essential-addons-elementor' ),
			'good'   => __( 'Medium', 'essential-addons-elementor' ),
			'strong' => __( 'Strong', 'essential-addons-elementor' ),
		];
		foreach ( $pass_type as $p_type => $label ) {
			$lr->add_control( "ps_text_{$p_type}", [
				/* translators: %s: Strength of the Password eg. Bad, Good etc. */
				'label'       => sprintf( __( '%s Password', 'essential-addons-elementor' ), $label ),
				'type'        => Controls_Manager::TEXT,
				/* translators: %s: Strength of the Password eg. Bad, Good etc. */
				'default'     => sprintf( __( '%s Password', 'essential-addons-elementor' ), $label ),
				'placeholder' => __( 'Eg. Weak or Good etc.', 'essential-addons-elementor' ),
				'condition'   => [
					'show_pass_strength' => 'yes',
					'ps_text_type'       => 'custom',
				],
			] );
		}

        /** Use Weak Password : Starts */
        $lr->add_control( 'use_weak_password', [
            'label' => __( 'Enable use of weak Password', 'essential-addons-elementor' ),
            'type'  => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ] );

        $lr->add_control( 'weak_pass_validation_text_type', [
			'label'     => __( 'Validation Text', 'essential-addons-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => '',
			'options'   => [
				'' => __( 'Default', 'essential-addons-elementor' ),
				'custom'  => __( 'Custom', 'essential-addons-elementor' ),
			],
            'condition' => [
                'use_weak_password' => '',
            ],
		] );

		$lr->add_control( "weak_pass_custom_validation_text", [
			'label'       => __( 'Custom Validation Text', 'essential-addons-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'placeholder' => __( 'Your custom validation text...', 'essential-addons-elementor' ),
			'condition'   => [
				'weak_pass_validation_text_type' => 'custom',
                'use_weak_password' => '',
			],
		] );

        $lr->add_control( 'weak_pass_min_char', [
            'label' => __( 'Minimum Password Length', 'essential-addons-elementor' ),
            'type'  => Controls_Manager::NUMBER,
            'default' => 12,
            'min' => 6,
            'max' => 30,
            'step' => 1,
            'condition' => [
                'use_weak_password' => '',
            ],
        ] );

        $lr->add_control( 'weak_pass_one_uppercase', [
            'label' => __( 'One Uppercase Letter', 'essential-addons-elementor' ),
            'type'  => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'condition' => [
                'use_weak_password' => '',
            ],
        ] );

        $lr->add_control( 'weak_pass_one_lowercase', [
            'label' => __( 'One Lowercase Letter', 'essential-addons-elementor' ),
            'type'  => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'condition' => [
                'use_weak_password' => '',
            ],
        ] );

        $lr->add_control( 'weak_pass_one_number', [
            'label' => __( 'One Number', 'essential-addons-elementor' ),
            'type'  => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'condition' => [
                'use_weak_password' => '',
            ],
        ] );

        $lr->add_control( 'weak_pass_one_special', [
            'label' => __( 'One Special Character', 'essential-addons-elementor' ),
            'type'  => Controls_Manager::SWITCHER,
            'default' => 'yes',
            'condition' => [
                'use_weak_password' => '',
            ],
        ] );

        /** Use Weak Password : Ends */

		$lr->add_control( 'ps_hint_type', [
			'label'     => __( 'Password Hint', 'essential-addons-elementor' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => '',
			'options'   => [
				''        => __( 'None', 'essential-addons-elementor' ),
				'default' => __( 'WordPress Default', 'essential-addons-elementor' ),
				'custom'  => __( 'Custom', 'essential-addons-elementor' ),
			],
            'condition' => [
                'use_weak_password!' => '',
            ],
            'separator' => 'before',
		] );
		$lr->add_control( "ps_hint", [
			'label'       => __( 'Custom Password Hint', 'essential-addons-elementor' ),
			'type'        => Controls_Manager::TEXT,
			'placeholder' => __( 'Your custom password hint...', 'essential-addons-elementor' ),
			'condition'   => [
				'ps_hint_type' => 'custom',
                'use_weak_password!' => '',
			],
		] );

	}

	/**
	 * @param Login_Register $lr
	 */
	public function lr_init_content_icon_controls( Login_Register $lr ) {
		$lr->add_control( 'show_login_icon', [
			'label' => __( 'Show Field Icons', 'essential-addons-elementor' ),
			'type'  => Controls_Manager::SWITCHER,
		] );
	}

	/**
	 * @param Repeater $repeater register field repeater object
	 */
	public function lr_add_register_fields_icons( $repeater ) {
		$repeater->add_control( 'icon', [
			'label'   => __( 'Icon', 'essential-addons-elementor' ),
			'type'    => Controls_Manager::ICONS,
			'default' => [
				'value'   => 'fas fa-user',
				'library' => 'fa-solid',
			],
		] );
	}

	/**
	 * @param array $fields register fields default fields array
	 *
	 * @return array $fields
	 */
	public function lr_add_register_fields_default_icons( $fields ) {
		return array_map( function ( $field ) {
			if ( ! isset( $field['field_type'] ) ) {
				return $field;
			}
			switch ( $field['field_type'] ) {
				case 'user_name':
				case 'first_name':
				case 'last_name':
					$field['icon'] = [
						'value'   => 'fas fa-user',
						'library' => 'fa-solid',
					];
					break;
				case 'email':
					$field['icon'] = [
						'value'   => 'fas fa-envelope',
						'library' => 'fa-solid',
					];
					break;
				case 'password':
				case 'confirm_pass':
					$field['icon'] = [
						'value'   => 'fas fa-lock',
						'library' => 'fa-solid',
					];
					break;
				case 'website':
					$field['icon'] = [
						'value'   => 'fas fa-globe',
						'library' => 'fa-solid',
					];
					break;

			}

			return $field;
		}, $fields );
	}

	/**
	 * It adds styling controls for password strength
	 *
	 * @param Login_Register $lr
	 */
	public function lr_init_style_pass_strength_controls( Login_Register $lr ) {
		$lr->start_controls_section( 'section_style_pass_strength', [
			'label' => __( 'Password Strength', 'essential-addons-for-elementor' ),
			'tab'   => Controls_Manager::TAB_STYLE,
			//'condition' => [
			//	'show_pass_strength' => 'yes',
			//], //@TODO; update to or condition later
		] );
		$container        = "{{WRAPPER}} .pass-meta-info";
		$notice_container = "{{WRAPPER}} .eael-pass-notice";
		$meter             = "{{WRAPPER}} .eael-pass-meter";
		$hint             = "{{WRAPPER}} .eael-pass-hint";
		$lr->add_responsive_control( "eael_ps_wrap_width", [
			'label'      => esc_html__( 'Width', 'essential-addons-for-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'%',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 1000,
					'step' => 5,
				],
				'%'  => [
					'min' => 0,
					'max' => 100,
				],
			],
			'default'    => [
				'unit' => '%',
				'size' => 100,
			],
			'selectors'  => [
				$container => 'width: {{SIZE}}{{UNIT}};',
			],
		] );
		$lr->add_responsive_control( "eael_ps_wrap_height", [
			'label'      => esc_html__( 'Height', 'essential-addons-for-elementor' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [
				'px',
				'%',
			],
			'range'      => [
				'px' => [
					'min'  => 0,
					'max'  => 1000,
					'step' => 5,
				],
				'%'  => [
					'min' => 0,
					'max' => 100,
				],
			],
			'selectors'  => [
				$container => 'height: {{SIZE}}{{UNIT}};',
			],
		] );
		$lr->add_responsive_control( "eael_ps_wrap_margin", [
			'label'      => __( 'Box Margin', 'essential-addons-for-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				$container => $lr->apply_dim( 'margin' ),
			],
		] );

		$lr->add_responsive_control( "eael_ps_wrap_padding", [
			'label'      => __( 'Box Padding', 'essential-addons-for-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				$container => $lr->apply_dim( 'padding' ),
			],
			'separator'  => 'after',
		] );
		$lr->add_responsive_control( "eael_ps_meter_margin", [
			'label'      => __( 'Meter Margin', 'essential-addons-for-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				$meter => $lr->apply_dim( 'margin' ),
			],
		] );
		$lr->add_responsive_control( "eael_ps_text_margin", [
			'label'      => __( 'Strength Text Margin', 'essential-addons-for-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				$notice_container => $lr->apply_dim( 'margin' ),
			],
		] );
		$lr->add_responsive_control( "eael_ps_hint_margin", [
			'label'      => __( 'Password Hint Margin', 'essential-addons-for-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'em',
				'%',
			],
			'selectors'  => [
				$hint => $lr->apply_dim( 'margin' ),
			],
			'condition' => [
				'ps_hint_type!' => '',
				'use_weak_password!' => '',
			],
		] );
		$lr->add_group_control( Group_Control_Typography::get_type(), [
			'label'     => __( 'Strength Text Typography', 'essential-addons-elementor' ),
			'name'     => 'eael_ps_text_typo',
			'selector' => $notice_container,
		] );
		$lr->add_group_control( Group_Control_Typography::get_type(), [
			'label'     => __( 'Password Hint Typography', 'essential-addons-elementor' ),
			'name'     => 'eael_ps_hint_typo',
			'selector' => $hint,
			'condition' => [
				'ps_hint_type!' => '',
                'use_weak_password!' => '',
			],
		] );
		$lr->add_group_control( Group_Control_Border::get_type(), [
			'name'     => "eael_ps_wrap_border",
			'selector' => $notice_container,
		] );
		$lr->add_control( "eael_ps_wrap_border_radius", [
			'label'      => __( 'Border Radius', 'essential-addons-for-elementor' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [
				'px',
				'%',
			],
			'selectors'  => [
				$notice_container => $lr->apply_dim( 'border-radius' ),
			],
			'separator'  => 'after',
		] );
		$lr->add_control( 'ps_text_color_heading', [
			'label'     => __( 'Colors', 'essential-addons-elementor' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );
		$pass_type = [
			'short'  => __( 'Very Weak', 'essential-addons-elementor' ),
			'bad'    => __( 'Weak', 'essential-addons-elementor' ),
			'good'   => __( 'Medium', 'essential-addons-elementor' ),
			'strong' => __( 'Strong', 'essential-addons-elementor' ),
		];
		foreach ( $pass_type as $p_type => $label ) {
			$ps_text_n_meter_selectors = [ "{$notice_container}.{$p_type}" => 'color: {{VALUE}};' ];
			switch ( $p_type ) {
				case 'short':
					$ps_text_n_meter_selectors['{{WRAPPER}} .eael-lr-form-wrapper meter[value=\'0\']::-webkit-meter-optimum-value'] = 'background: {{VALUE}};';
					$ps_text_n_meter_selectors['{{WRAPPER}} .eael-lr-form-wrapper meter[value=\'0\']::-moz-meter-bar, ']            = 'background: {{VALUE}};';
					$ps_text_n_meter_selectors['{{WRAPPER}} .eael-lr-form-wrapper meter[value=\'1\']::-webkit-meter-optimum-value'] = 'background: {{VALUE}};';
					$ps_text_n_meter_selectors['{{WRAPPER}} .eael-lr-form-wrapper meter[value=\'1\']::-moz-meter-bar']              = 'background: {{VALUE}};';
					break;
				case 'bad':
					$ps_text_n_meter_selectors['{{WRAPPER}} .eael-lr-form-wrapper meter[value=\'2\']::-webkit-meter-optimum-value'] = 'background: {{VALUE}};';
					$ps_text_n_meter_selectors['{{WRAPPER}} .eael-lr-form-wrapper meter[value=\'2\']::-moz-meter-bar']              = 'background: {{VALUE}};';
					break;
				case 'good':

					$ps_text_n_meter_selectors['{{WRAPPER}} .eael-lr-form-wrapper meter[value=\'3\']::-webkit-meter-optimum-value'] = 'background: {{VALUE}};';
					$ps_text_n_meter_selectors['{{WRAPPER}} .eael-lr-form-wrapper meter[value=\'3\']::-moz-meter-bar']              = 'background: {{VALUE}};';
					break;
				case 'strong':
					$ps_text_n_meter_selectors['{{WRAPPER}} .eael-lr-form-wrapper meter[value=\'4\']::-webkit-meter-optimum-value'] = 'background: {{VALUE}};';
					$ps_text_n_meter_selectors['{{WRAPPER}} .eael-lr-form-wrapper meter[value=\'4\']::-moz-meter-bar']              = 'background: {{VALUE}};';
					break;
			}

			$lr->add_control( "ps_text_{$p_type}_color", [
				/* translators: %s: Strength of the Password eg. Bad, Good etc. */
				'label'     => sprintf( __( '%s Password', 'essential-addons-elementor' ), $label ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => $ps_text_n_meter_selectors,
				'condition' => [
					'show_pass_strength' => 'yes',
				],
			] );
		}
		$lr->add_control( "ps_hint_color", [
			'label'     => __( 'Password Hint', 'essential-addons-elementor' ),
			'type'      => Controls_Manager::COLOR,
			'selectors' => $hint,
			'condition' => [
				'ps_hint_type!' => '',
                'use_weak_password!' => '',
			],
		] );
		$lr->add_group_control( Group_Control_Background::get_type(), [
			'name'     => "eael_ps_wrap_bg_color",
			'label'    => __( 'Background Color', 'essential-addons-for-elementor' ),
			'types'    => [
				'classic',
				'gradient',
			],
			'selector' => $notice_container,
		] );
		$lr->add_responsive_control( 'eael_ps_align', [
			'label'     => __( 'Alignment', 'essential-addons-for-elementor' ),
			'type'      => Controls_Manager::CHOOSE,
			'options'   => [
				'left'   => [
					'title' => __( 'Left', 'essential-addons-for-elementor' ),
					'icon'  => 'eicon-text-align-left',
				],
				'center' => [
					'title' => __( 'Center', 'essential-addons-for-elementor' ),
					'icon'  => 'eicon-text-align-center',
				],
				'right'  => [
					'title' => __( 'Right', 'essential-addons-for-elementor' ),
					'icon'  => 'eicon-text-align-right',
				],
			],
			'default'   => '',
			'selectors' => [
				$notice_container => 'text-align: {{VALUE}};',
			],
		] );
		$lr->end_controls_section();
	}

	/**
	 * It shows password strength
	 *
	 * @param Login_Register $lr
	 */
	public function lr_show_password_strength_meter( Login_Register $lr ) {
		$show_ps_meter      = $lr->get_settings_for_display( 'show_ps_meter' );
		$show_pass_strength = $lr->get_settings_for_display( 'show_pass_strength' );
		$hint_type          = $lr->get_settings_for_display( 'ps_hint_type' );

		if ( 'yes' !== $show_pass_strength && empty( $hint_type ) && 'yes' !== $show_ps_meter ) {
			return;// vail early if SPS, spsm and hint all off
		}

		$data = [
			'show_ps_meter'      => esc_attr( $show_ps_meter ),
			'show_pass_strength' => esc_attr( $show_pass_strength ),
			'ps_text_type'       => esc_attr( $lr->get_settings_for_display( 'ps_text_type' ) ),
		];

		if ( 'yes' === $show_pass_strength ) {
			$pass_types = [
				'short',
				'bad',
				'good',
				'strong',
			];
			foreach ( $pass_types as $pass_type ) {
				$data["ps_text_{$pass_type}"] = trim( $lr->get_settings_for_display( "ps_text_{$pass_type}" ) );
			}
		}

		$hint = '';
		if ( ! empty( $hint_type ) ) {
			$hint = 'custom' === $hint_type ? $lr->get_settings_for_display( 'ps_hint' ) : wp_get_password_hint();
		}
		?>
        <div class='pass-meta-info' data-strength-options="<?php echo esc_attr( json_encode( $data ) ); ?>">
			<?php
			if ( 'yes' === $show_ps_meter ) {
				echo '<meter max="4" class="eael-pass-meter" value="" style="display: none"></meter>';
			}
			if ( 'yes' === $show_pass_strength ) {
				echo '<p class="eael-pass-notice" style="display: none"></p>';
			}
			if ( ! empty( $hint ) ) {
				printf( "<p class='eael-pass-hint'>%s</p>", esc_html( $hint ) );
			}
			?>
        </div>
		<?php

	}
    
    /**
	 * It validates password
     * 
     * @param array $settings
     * @param string $password
     * @param array $errors
	 */
	public function lr_register_user_password_validation( $errors, $settings, $password ) {
        $use_weak_password = !empty($settings['use_weak_password']) ? $settings['use_weak_password'] : '';
        $validation_text_condition = [];

        if('yes' !== $use_weak_password) {
            $weak_pass_min_char = isset($settings['weak_pass_min_char']) ? intval($settings['weak_pass_min_char']) : 12;
            $weak_pass_one_uppercase = isset($settings['weak_pass_one_uppercase']) ? sanitize_text_field( $settings['weak_pass_one_uppercase'] ) : 'yes';
            $weak_pass_one_lowercase = isset($settings['weak_pass_one_lowercase']) ? sanitize_text_field( $settings['weak_pass_one_lowercase'] ) : 'yes';
            $weak_pass_one_number = isset($settings['weak_pass_one_number']) ? sanitize_text_field( $settings['weak_pass_one_number'] ) : 'yes';
            $weak_pass_one_special = isset($settings['weak_pass_one_special']) ? sanitize_text_field( $settings['weak_pass_one_special'] ) : 'yes';
            
            $pattern_init_uppercase = $pattern_init_lowercase = $pattern_init_number = $pattern_init_special = '';
            
            if( 'yes' === $weak_pass_one_uppercase ) {
                $pattern_init_uppercase = '(?=.*[A-Z])';
                $validation_text_condition[] = 'one uppercase letter';
            }
            if( 'yes' === $weak_pass_one_lowercase ) {
                $pattern_init_lowercase = '(?=.*[a-z])';
                $validation_text_condition[] = 'one lowercase letter';
            }
            if( 'yes' === $weak_pass_one_number ) {
                $pattern_init_number = '(?=.*[0-9])';
                $validation_text_condition[] = 'one number';
            }
            if( 'yes' === $weak_pass_one_special ) {
                $pattern_init_special = '(?=.*[!@#$%^&*-])';
                $validation_text_condition[] = 'one special character';
            }
            
            if($weak_pass_min_char){
                $pattern_init_min_char = '.{' . $weak_pass_min_char . ',}';
            }
            $pattern = '/^' . $pattern_init_special . $pattern_init_number . $pattern_init_uppercase . $pattern_init_lowercase . $pattern_init_min_char . '$/';
            // Regex Example: $pattern = '/^(?=.*[!@#$%^&*-])(?=.*[0-9])(?=.*[A-Z]).{12,}$/'; // at least 12 characters, one special character, one number, one uppercase letter and one lowercase letter

            if(!preg_match($pattern, $password)) {
                
                $validation_text_default = 'Password must be at least ' . intval( $weak_pass_min_char ) . ' characters long';
                
                if( count($validation_text_condition) ){
                    foreach($validation_text_condition as $key => $value) {
                        if($key === 0) {
                            $validation_text_default .= ' and contains at least ';
                        } else if ( $key === count($validation_text_condition) - 1 ) {
                            $validation_text_default .= ' and ';
                        } else {
                            $validation_text_default .= ', ';
                        }

                        $validation_text_default .= $value;
                    }
                }
                
                $errors['password'] = $validation_text_default;

                if ( !empty( $settings['weak_pass_validation_text_type'] ) && 'custom' === $settings['weak_pass_validation_text_type'] ) {
                    $errors['password'] = !empty( $settings['weak_pass_custom_validation_text'] ) ? sanitize_text_field( $settings['weak_pass_custom_validation_text'] ) : $errors['password'];
                }
            }
        }

        return $errors;
	}

}
