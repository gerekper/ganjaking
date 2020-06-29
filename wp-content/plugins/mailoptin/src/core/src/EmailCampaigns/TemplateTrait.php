<?php

namespace MailOptin\Core\EmailCampaigns;

use MailOptin\Core\PluginSettings\Settings;
use MailOptin\Core\Repositories\EmailCampaignRepository as ER;
use WP_Post;

trait TemplateTrait
{
    /**
     * @param int|WP_Post $post
     *
     * @return string
     */
    public function post_title($post)
    {
        if ( ! is_object($post) || ! ($post instanceof WP_Post)) {
            $post = get_post($post);
        }

        return $post->post_title;
    }

    /**
     * @param int|WP_Post $post
     *
     * @return false|string
     */
    public function post_url($post)
    {
        if ($post instanceof \stdClass) {
            return $post->post_url;
        }

        if ( ! is_object($post) || ! ($post instanceof WP_Post)) {
            $post = get_post($post);
        }

        return get_permalink($post);
    }

    /**
     * @param WP_Post $post
     *
     * @return string
     */
    public function post_meta($post, $post_metas = '')
    {
        if (empty($post_metas)) {
            $post_metas = ER::get_customizer_value($this->email_campaign_id, 'content_post_meta');

            if (empty($post_metas)) return '';

            $post_metas = array_map('sanitize_text_field', explode(',', $post_metas));
        }

        $post = get_post($post);

        if ( ! $post) {
            return false;
        }

        $bucket = [];

        foreach ($post_metas as $meta) {
            switch ($meta) {
                case 'author':
                    $bucket[] = sprintf('<span>%s</span>', strip_tags(get_the_author_meta('display_name', $post->post_author)));
                    break;
                case 'date':
                    $bucket[] = sprintf('<span>%s</span>', get_the_date(get_option('date_format'), $post));
                    break;
                case 'category':
                    $bucket[] = sprintf('<span>%s</span>', strip_tags(get_the_term_list($post->ID, 'category', '', ', ')));
                    break;
            }
        }

        $html = '<div class="mo-post-meta mo-content-text-color">';
        $html .= implode('<span>&nbsp;•&nbsp;</span>', $bucket);
        $html .= '</div>';

        return $html;
    }

    /**
     * @param int|WP_Post $post
     *
     * @return string
     */
    public function post_content($post, $post_content_length = null)
    {
        $is_remove_post_content = ER::get_merged_customizer_value(
            $this->email_campaign_id,
            'content_remove_post_body'
        );

        if ($is_remove_post_content) return '';

        if ( ! is_object($post) || ! ($post instanceof WP_Post)) {
            $post = get_post($post);
        }

        // shim for DIVI builder so conent are parsed.
        if (function_exists('et_builder_init_global_settings')) {
            if ( ! did_action('et_builder_ready')) {
                et_builder_init_global_settings();
                et_builder_add_main_elements();
            }

            $post->post_content = apply_filters('the_content', $post->post_content);
        }

        $post_content = $post->post_content;

        if (ER::get_merged_customizer_value($this->email_campaign_id, 'post_content_type') == 'post_excerpt') {
            if ( ! empty($post->post_excerpt)) {
                $post_content = $post->post_excerpt;
            }
        }

        $post_content = do_shortcode(
            apply_filters('mo_email_automation_post_content', $post_content, $post, $this->email_campaign_id)
        );

        $post_content_length = is_null($post_content_length) ? ER::get_merged_customizer_value($this->email_campaign_id, 'post_content_length') : $post_content_length;

        $post_content_length = absint($post_content_length);

        if (0 !== $post_content_length) {
            $post_content = \MailOptin\Core\limit_text(
                $post_content,
                $post_content_length
            );
        }

        return wpautop($post_content);
    }

    /**
     * @param int|WP_Post|\stdClass $post
     * @param string $email_campaign_id
     *
     * @return mixed|string
     */
    public function feature_image($post, $email_campaign_id = '', $default_feature_image = '')
    {
        $email_campaign_id = ! empty($email_campaign_id) ? $email_campaign_id : $this->email_campaign_id;

        $default_feature_image = ! empty($default_feature_image) ? $default_feature_image : ER::get_merged_customizer_value($email_campaign_id, 'default_image_url');

        $is_remove_featured_image = ER::get_merged_customizer_value(
            $email_campaign_id,
            'content_remove_feature_image'
        );

        if ($is_remove_featured_image) return '';

        if ($post instanceof \stdClass) {
            return $default_feature_image;
        }

        if (has_post_thumbnail($post)) {
            $image_data = wp_get_attachment_image_src(get_post_thumbnail_id($post), 'full');
            if ( ! empty($image_data[0])) {
                return $image_data[0];
            }
        }

        return $default_feature_image;
    }

    /**
     * Replace placeholders in email template's footer description with their contact details saved values.
     *
     * @param string $content
     *
     * @return mixed
     */
    public function replace_footer_placeholder_tags($content)
    {
        $search = [
            '{{company_name}}',
            '{{company_address}}',
            '{{company_address_2}}',
            '{{company_city}}',
            '{{company_state}}',
            '{{company_zip}}',
            '{{company_country}}'
        ];

        $replace = [
            Settings::instance()->company_name(),
            Settings::instance()->company_address(),
            Settings::instance()->company_address_2(),
            Settings::instance()->company_city(),
            Settings::instance()->company_state(),
            Settings::instance()->company_zip(),
            \MailOptin\Core\country_code_to_name(Settings::instance()->company_country()),
        ];

        foreach ($replace as $key => $item) {
            if (empty($item)) {
                unset($search[$key]);
                unset($replace[$key]);
            }
        }

        return str_replace($search, $replace, apply_filters('mailoptin_settings_replace_footer_placeholder_tags', $content));
    }
}