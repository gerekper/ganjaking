<?php

namespace MailOptin\Core\Admin\Customizer\CustomControls\EmailContentBuilder\Elements;


use MailOptin\Core\Admin\Customizer\CustomControls\EmailContentBuilder\AbstractElement;

class Button extends AbstractElement
{
    public function id()
    {
        return 'button';
    }

    public function icon()
    {
        return 'button.svg';
    }

    public function title()
    {
        return esc_html__('Button', 'mailoptin');
    }

    public function description()
    {
        return esc_html__('A simple button.', 'mailoptin');
    }

    public function tabs()
    {
        return [
            'tab-content'        => esc_html__('Content', 'mailoptin'),
            'tab-style'          => esc_html__('Style', 'mailoptin'),
            'tab-block-settings' => esc_html__('Block Settings', 'mailoptin'),
        ];
    }

    public function settings()
    {
        return apply_filters('mo_email_content_elements_button_element', $this->element_block_settings() + [
                'button_text'             => [
                    'label' => esc_html__('Button Text', 'mailoptin'),
                    'type'  => 'text',
                    'tab'   => 'tab-content'
                ],
                'button_link'             => [
                    'label' => esc_html__('Button Link (URL)', 'mailoptin'),
                    'type'  => 'text',
                    'tab'   => 'tab-content'
                ],
                'button_width'            => [
                    'label' => esc_html__('Width (%)', 'mailoptin'),
                    'type'  => 'range',
                    'tab'   => 'tab-content',
                    'min'   => 20,
                    'max'   => 100,
                    'step'  => 1,
                ],
                'button_background_color' => [
                    'label' => esc_html__('Background Color', 'mailoptin'),
                    'type'  => 'color_picker',
                    'tab'   => 'tab-style'
                ],
                'button_color'            => [
                    'label' => esc_html__('Color', 'mailoptin'),
                    'type'  => 'color_picker',
                    'tab'   => 'tab-style'
                ],
                'button_font_size'        => [
                    'label' => esc_html__('Font Size (px)', 'mailoptin'),
                    'type'  => 'range',
                    'tab'   => 'tab-style',
                    'min'   => 5,
                    'max'   => 100,
                    'step'  => 1,
                ],
                'button_alignment'        => [
                    'label'   => esc_html__('Alignment', 'mailoptin'),
                    'choices' => [
                        'left'   => esc_html__('Left', 'mailoptin'),
                        'right'  => esc_html__('Right', 'mailoptin'),
                        'center' => esc_html__('Center', 'mailoptin'),
                    ],
                    'type'    => 'select',
                    'tab'     => 'tab-style'
                ],
                'button_padding'          => [
                    'label' => esc_html__('Padding', 'mailoptin'),
                    'type'  => 'dimension',
                    'tab'   => 'tab-style'
                ],
                'button_border_radius'    => [
                    'label' => esc_html__('Rounded Corner', 'mailoptin'),
                    'type'  => 'range',
                    'tab'   => 'tab-style',
                    'min'   => 0,
                    'max'   => 50,
                    'step'  => 1,
                ],
                'button_font_family'      => [
                    'label' => esc_html__('Font Family', 'mailoptin'),
                    'type'  => 'font_family',
                    'tab'   => 'tab-style'
                ],
                'button_font_weight'      => [
                    'label'   => esc_html__('Font Weight', 'mailoptin'),
                    'type'    => 'select',
                    'choices' => [
                        'normal'  => esc_html__('Normal', 'mailoptin'),
                        'bold'    => esc_html__('Bold', 'mailoptin'),
                        'bolder'  => esc_html__('Bolder', 'mailoptin'),
                        'lighter' => esc_html__('Lighter', 'mailoptin')
                    ],
                    'tab'     => 'tab-style'
                ],
            ]
        );
    }
}