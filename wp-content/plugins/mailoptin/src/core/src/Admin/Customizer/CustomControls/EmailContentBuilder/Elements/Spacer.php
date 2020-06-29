<?php

namespace MailOptin\Core\Admin\Customizer\CustomControls\EmailContentBuilder\Elements;


use MailOptin\Core\Admin\Customizer\CustomControls\EmailContentBuilder\AbstractElement;

class Spacer extends AbstractElement
{
    public function id()
    {
        return 'spacer';
    }

    public function icon()
    {
        return 'spacer.svg';
    }

    public function title()
    {
        return esc_html__('Spacer', 'mailoptin');
    }

    public function description()
    {
        return esc_html__('A white-space separator.', 'mailoptin');
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
        return apply_filters('mo_email_content_elements_spacer_element', $this->element_block_settings() + [
                'spacer_height' => [
                    'label' => esc_html__('Height (px)', 'mailoptin'),
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