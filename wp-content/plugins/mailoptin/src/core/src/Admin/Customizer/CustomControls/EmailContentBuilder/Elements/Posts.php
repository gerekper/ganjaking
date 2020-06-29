<?php

namespace MailOptin\Core\Admin\Customizer\CustomControls\EmailContentBuilder\Elements;


use MailOptin\Core\Admin\Customizer\CustomControls\ControlsHelpers;
use MailOptin\Core\Admin\Customizer\CustomControls\EmailContentBuilder\AbstractElement;

class Posts extends AbstractElement
{
    public function id()
    {
        return 'posts';
    }

    public function icon()
    {
        return '<span class="dashicons dashicons-admin-post mo-email-content-element-img"></span>';
    }

    public function title()
    {
        return esc_html__('Posts', 'mailoptin');
    }

    public function is_premium_element()
    {
        return true;
    }

    public function description()
    {
        return esc_html__('Embed a list of posts.', 'mailoptin');
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
        return apply_filters('mo_email_content_elements_posts_element', $this->element_block_settings() + [
                'posts_post_type'       => [
                    'label'   => esc_html__('Select Post Type', 'mailoptin'),
                    'choices' => ['post' => esc_html__('Posts', 'mailoptin')] + ControlsHelpers::custom_post_types(),
                    'type'    => 'select',
                    'tab'     => 'tab-content'
                ],
                'post_list'             => [
                    'label'           => esc_html__('Select Posts', 'mailoptin'),
                    'type'            => 'select',
                    'choices'         => [],
                    'multiple'        => true,
                    'tab'             => 'tab-content',
                    'select2_options' => [
                        'placeholder'        => esc_html__('Search for posts', 'mailoptin'),
                        'minimumInputLength' => 2,
                        'ajax'               => [
                            'url'      => admin_url('admin-ajax.php'),
                            'method'   => 'POST',
                            'dataType' => 'json',
                        ]
                    ]
                ],
                'post_content_length'   => [
                    'label'       => esc_html__('Post Content Length', 'mailoptin'),
                    'description' => esc_html__('Number of words to limit the post content to. Set to "0" for full post content.', 'mailoptin'),
                    'type'        => 'range',
                    'tab'         => 'tab-content',
                    'min'         => 0,
                    'max'         => 1000,
                    'step'        => 1,
                ],
                'post_metas'            => [
                    'label'    => esc_html__('Post Meta Data', 'mailoptin'),
                    'type'     => 'select',
                    'choices'  => [
                        'author'   => esc_html__('Author', 'mailoptin'),
                        'date'     => esc_html__('Date', 'mailoptin'),
                        'category' => esc_html__('Categories', 'mailoptin'),
                    ],
                    'multiple' => true,
                    'tab'      => 'tab-content'
                ],
                'read_more_text'        => [
                    'label' => esc_html__('Read More Link Text', 'mailoptin'),
                    'type'  => 'text',
                    'tab'   => 'tab-content'
                ],
                'remove_feature_image'  => [
                    'checkbox_label' => esc_html__('Remove Featured Image', 'mailoptin'),
                    'type'           => 'checkbox',
                    'tab'            => 'tab-content'
                ],
                'remove_post_content'   => [
                    'checkbox_label' => esc_html__('Remove Post Content', 'mailoptin'),
                    'type'           => 'checkbox',
                    'tab'            => 'tab-content'
                ],
                'remove_read_more_link' => [
                    'checkbox_label' => esc_html__('Remove Read More Link', 'mailoptin'),
                    'type'           => 'checkbox',
                    'tab'            => 'tab-content'
                ],
                'default_image_url'     => [
                    'label' => esc_html__('Fallback Featured Image', 'mailoptin'),
                    'type'  => 'select_image',
                    'tab'   => 'tab-content'
                ],
                'post_title_color'      => [
                    'label' => esc_html__('Post Title Color', 'mailoptin'),
                    'type'  => 'color_picker',
                    'tab'   => 'tab-style'
                ],
                'read_more_color'       => [
                    'label' => esc_html__('Read More Link Color', 'mailoptin'),
                    'type'  => 'color_picker',
                    'tab'   => 'tab-style'
                ],
                'post_font_family'      => [
                    'label' => esc_html__('Font Family', 'mailoptin'),
                    'type'  => 'font_family',
                    'tab'   => 'tab-style'
                ],
            ]
        );
    }
}