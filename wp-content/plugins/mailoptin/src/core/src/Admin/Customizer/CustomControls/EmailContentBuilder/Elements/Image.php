<?php

namespace MailOptin\Core\Admin\Customizer\CustomControls\EmailContentBuilder\Elements;


use MailOptin\Core\Admin\Customizer\CustomControls\EmailContentBuilder\AbstractElement;

class Image extends AbstractElement
{
    public function id()
    {
        return 'image';
    }

    public function icon()
    {
        return 'image.svg';
    }

    public function title()
    {
        return esc_html__('Image', 'mailoptin');
    }

    public function description()
    {
        return esc_html__('Adds an image.', 'mailoptin');
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
        return apply_filters('mo_email_content_elements_image_element', $this->element_block_settings() + [
                'image_url'       => [
                    'label' => esc_html__('Image URL', 'mailoptin'),
                    'type'  => 'select_image',
                    'tab'   => 'tab-content'
                ],
                'image_link'      => [
                    'label'       => esc_html__('Link', 'mailoptin'),
                    'placeholder' => 'https://',
                    'type'        => 'text',
                    'tab'         => 'tab-content'
                ],
                'image_width'     => [
                    'label' => esc_html__('Width (px)', 'mailoptin'),
                    'type'  => 'range',
                    'tab'   => 'tab-style',
                    'min'   => 10,
                    'max'   => 600,
                    'step'  => 1,
                ],
                'image_alignment' => [
                    'label'   => esc_html__('Alignment', 'mailoptin'),
                    'choices' => [
                        'left'   => esc_html__('Left', 'mailoptin'),
                        'right'  => esc_html__('Right', 'mailoptin'),
                        'center' => esc_html__('Center', 'mailoptin'),
                    ],
                    'type'    => 'select',
                    'tab'     => 'tab-style'
                ],
                'image_alt_text'  => [
                    'label' => esc_html__('Alternative Text (alt tag)', 'mailoptin'),
                    'type'  => 'text',
                    'tab'   => 'tab-style'
                ],
            ]
        );
    }
}