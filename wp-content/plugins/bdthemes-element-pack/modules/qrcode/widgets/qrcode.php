<?php

namespace ElementPack\Modules\Qrcode\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use ElementPack\Base\Module_Base;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Qrcode extends Module_Base
{

    public function get_name()
    {
        return 'bdt-qrcode';
    }

    public function get_title()
    {
        return BDTEP . esc_html__('QR Code', 'bdthemes-element-pack');
    }

    public function get_icon()
    {
        return 'bdt-wi-qrcode';
    }

    public function get_categories()
    {
        return ['element-pack'];
    }

    public function get_keywords()
    {
        return ['qr', 'code'];
    }

    public function get_style_depends()
    {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-qrcode'];
        }
    }

    public function get_script_depends()
    {
        if ($this->ep_is_edit_mode()) {
            return ['qrcode', 'ep-scripts'];
        } else {
            return ['qrcode', 'ep-qrcode'];
        }
    }

    public function get_custom_help_url()
    {
        return 'https://youtu.be/3ofLAjpnmO8';
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'section_content_qrcode',
            [
                'label' => esc_html__('QR Code', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'text',
            [
                'label' => esc_html__('Content', 'bdthemes-element-pack'),
                'type' => Controls_Manager::TEXTAREA,
                'placeholder' => 'http://bdthemes.com',
                'default' => 'http://bdthemes.com',
                'condition' => ['site_link!' => 'yes'],
                'dynamic' => ['active' => true],
            ]
        );

        $this->add_control(
            'site_link',
            [
                'label' => esc_html__('This Page Link', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'label_type',
            [
                'label' => esc_html__('Label Type', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => 'text',
                'options' => [
                    'none' => esc_html__('None', 'bdthemes-element-pack'),
                    'text' => esc_html__('Text', 'bdthemes-element-pack'),
                    'image' => esc_html__('Image', 'bdthemes-element-pack'),
                ],
            ]
        );

        $this->add_control(
            'label',
            [
                'label' => esc_html__('Text', 'bdthemes-element-pack'),
                'type' => Controls_Manager::TEXT,
                'dynamic' => ['active' => true],
                'placeholder' => 'BDTHEMES',
                'default' => 'BDTHEMES',
                'condition' => [
                    'label_type' => 'text',
                ],
            ]
        );

        $this->add_control(
            'image',
            [
                'label' => __('Choose Image', 'bdthemes-element-pack'),
                'type' => Controls_Manager::MEDIA,
                'condition' => [
                    'label_type' => 'image',
                ],
                'default' => [
                    'url' => BDTEP_ASSETS_URL . 'images/no-image.jpg',
                ],
                'dynamic' => ['active' => true],
            ]
        );

        $this->add_control(
            'mode',
            [
                'label' => esc_html__('Mode', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => 2,
                'options' => [
                    1 => esc_html__('Strip', 'bdthemes-element-pack'),
                    2 => esc_html__('Box', 'bdthemes-element-pack'),
                ],
                'condition' => [
                    'label_type!' => 'none',
                ],
            ]
        );

        $this->add_responsive_control(
            'align',
            [
                'label' => esc_html__('Alignment', 'bdthemes-element-pack'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'bdthemes-element-pack'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'bdthemes-element-pack'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'bdthemes-element-pack'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'center',
                // 'prefix_class' => 'elementor-align%s-',
                'selectors' => [
                    '{{WRAPPER}}.elementor-widget-bdt-qrcode' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_qr_code_additional',
            [
                'label' => esc_html__('Additional', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'size',
            [
                'label' => esc_html__('Size', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 400,
                ],
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 1000,
                        'step' => 50,
                    ],
                ],
            ]
        );

        $this->add_control(
            'mSize',
            [
                'label' => esc_html__('Label Size', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 11,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 40,
                        'step' => 1,
                    ],
                ],
                'condition' => [
                    'label_type!' => 'none',
                ],
            ]
        );

        $this->add_control(
            'mPosX',
            [
                'label' => esc_html__('Label POS X:', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 50,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'condition' => [
                    'label_type!' => 'none',
                ],
            ]
        );

        $this->add_control(
            'mPosY',
            [
                'label' => esc_html__('Label POS Y:', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 50,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'condition' => [
                    'label_type!' => 'none',
                ],
            ]
        );

        $this->add_control(
            'minVersion',
            [
                'label' => esc_html__('Min Version', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 6,
                ],
                'range' => [
                    'px' => [
                        'min' => 1,
                        'max' => 10,
                        'step' => 1,
                    ],
                ],
            ]
        );

        $this->add_control(
            'ecLevel',
            [
                'label' => esc_html__('Error Correction Level', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => 'H',
                'options' => [
                    'L' => esc_html__('Low (7%)', 'bdthemes-element-pack'),
                    'M' => esc_html__('Medium (15%)', 'bdthemes-element-pack'),
                    'Q' => esc_html__('Quartile (25%)', 'bdthemes-element-pack'),
                    'H' => esc_html__('High (30%)', 'bdthemes-element-pack'),
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_qrcode',
            [
                'label' => esc_html__('QR Code', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'fill',
            [
                'label' => esc_html__('Code Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'default' => '#333333',
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'background_color',
                'selector' => '{{WRAPPER}} .bdt-qrcode > *',
                'render_type' => 'template',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'border_radius',
            [
                'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-qrcode > *' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'padding',
            [
                'label' => esc_html__('Padding', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-qrcode > *' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'fontcolor',
            [
                'label' => esc_html__('Label Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ff9818',
                'condition' => [
                    'label_type' => 'text',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'radius',
            [
                'label' => esc_html__('Code Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 0,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 10,
                    ],
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $id = 'bdt-qrcode' . $this->get_id();

        if ($settings['label_type'] == 'image') {
            $image_src = wp_get_attachment_image_src($settings['image']['id'], 'full');
            $image = ($image_src) ? $image_src[0] : BDTEP_ASSETS_URL . 'images/no-image.jpg';
        }

        $qr_content = $settings['text'];

        if ($settings['site_link']) {
            $qr_content = get_permalink();
        }

        if ('none' == $settings['label_type']) {
            $mode = 0;
        } elseif ('text' == $settings['label_type']) {
            $mode = $settings['mode'];
        } elseif ('' != $settings['image']) {
            $mode = $settings['mode'] + 2;
        } else {
            $mode = 0;
        }

        $this->add_render_attribute(
            [
                'qrcode' => [
                    'data-settings' => [
                        wp_json_encode(array_filter([
                            "render" => "canvas",
                            "ecLevel" => $settings["ecLevel"],
                            "minVersion" => $settings["minVersion"]["size"],
                            "fill" => $settings["fill"],
                            "text" => $qr_content,
                            "size" => $settings["size"]["size"],
                            "radius" => $settings["radius"]["size"] * 0.01,
                            "mode" => (int) $mode,
                            "mSize" => (isset($settings["mSize"]["size"]) ? $settings["mSize"]["size"] * 0.01 : 11),
                            "mPosX" => (isset($settings["mPosX"]["size"]) ? $settings["mPosX"]["size"] * 0.01 : 50),
                            "mPosY" => (isset($settings["mPosY"]["size"]) ? $settings["mPosY"]["size"] * 0.01 : 50),
                            "label" => $settings["label"],
                            "fontcolor" => $settings["fontcolor"],
                            "background" => "transparent",
                        ])),
                    ],
                ],
            ]
        );

        ?>
		<div class="bdt-qrcode" <?php echo $this->get_render_attribute_string('qrcode'); ?>></div>

		<?php if ('image' === $settings['label_type'] and !empty($image)): ?>
			<img src="<?php echo esc_url($image); ?>" class="bdt-hidden bdt-qrcode-image" alt="<?php echo esc_html($settings["label"]); ?>">
<?php endif;
    }
}
