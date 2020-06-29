<?php

namespace MailOptin\Core\Admin\Customizer\CustomControls\EmailContentBuilder\Elements;


use MailOptin\Core\Admin\Customizer\CustomControls\EmailContentBuilder\AbstractElement;

class Text extends AbstractElement
{
    public function id()
    {
        return 'text';
    }

    public function icon()
    {
        return 'text.svg';
    }

    public function title()
    {
        return esc_html__('Text', 'mailoptin');
    }

    public function description()
    {
        return esc_html__('Text, HTML and multimedia content.', 'mailoptin');
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
        return apply_filters('mo_email_content_elements_text_element', $this->element_block_settings() + [
                'text_content'     => [
                    'type' => 'tinymce',
                    'tab'  => 'tab-content'
                ],
                'text_font_family' => [
                    'label' => esc_html__('Font Family', 'mailoptin'),
                    'type'  => 'font_family',
                    'tab'   => 'tab-style'
                ],
                'text_font_size'   => [
                    'label' => esc_html__('Font Size (px)', 'mailoptin'),
                    'type'  => 'range',
                    'tab'   => 'tab-style',
                    'min'   => 5,
                    'max'   => 200,
                    'step'  => 1,
                ]
            ]
        );
    }
}