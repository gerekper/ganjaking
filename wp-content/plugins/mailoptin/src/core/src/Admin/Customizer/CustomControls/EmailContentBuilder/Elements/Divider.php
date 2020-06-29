<?php

namespace MailOptin\Core\Admin\Customizer\CustomControls\EmailContentBuilder\Elements;


use MailOptin\Core\Admin\Customizer\CustomControls\EmailContentBuilder\AbstractElement;

class Divider extends AbstractElement
{
    public function id()
    {
        return 'divider';
    }

    public function icon()
    {
        return 'divider.svg';
    }

    public function title()
    {
        return esc_html__('Divider', 'mailoptin');
    }

    public function description()
    {
        return esc_html__('A line separator.', 'mailoptin');
    }

    public function tabs()
    {
        return [
            'tab-style'          => esc_html__('Style', 'mailoptin'),
            'tab-block-settings' => esc_html__('Block Settings', 'mailoptin'),
        ];
    }

    public function settings()
    {
        return apply_filters('mo_email_content_elements_divider_element', $this->element_block_settings() + [
                'divider_width'     => [
                    'label' => esc_html__('Width (%)', 'mailoptin'),
                    'type'  => 'range',
                    'tab'   => 'tab-style',
                    'min'   => 5,
                    'max'   => 100,
                    'step'  => 1,
                ],
                'divider_style'     => [
                    'label'   => esc_html__('Border Style', 'mailoptin'),
                    'type'    => 'select',
                    'choices' => [
                        'none'   => esc_html__('None', 'mailoptin'),
                        'solid'  => esc_html__('Solid', 'mailoptin'),
                        'dotted' => esc_html__('Dotted', 'mailoptin'),
                        'dashed' => esc_html__('Dashed', 'mailoptin'),
                        'double' => esc_html__('Double', 'mailoptin'),
                        'groove' => esc_html__('Groove', 'mailoptin'),
                        'ridge'  => esc_html__('Ridge', 'mailoptin'),
                        'inset'  => esc_html__('Inset', 'mailoptin'),
                        'outset' => esc_html__('Outset', 'mailoptin'),
                    ],
                    'tab'     => 'tab-style'
                ],
                'divider_color'     => [
                    'label' => esc_html__('Color', 'mailoptin'),
                    'type'  => 'color_picker',
                    'tab'   => 'tab-style'
                ],
                'divider_height'    => [
                    'label' => esc_html__('Height (px)', 'mailoptin'),
                    'type'  => 'range',
                    'tab'   => 'tab-style',
                    'min'   => 0,
                    'max'   => 100,
                    'step'  => 1,
                ]
            ]
        );
    }
}