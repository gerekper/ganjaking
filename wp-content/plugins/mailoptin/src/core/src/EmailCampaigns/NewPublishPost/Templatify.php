<?php

namespace MailOptin\Core\EmailCampaigns\NewPublishPost;

use MailOptin\Core\Admin\Customizer\EmailCampaign\EmailCampaignFactory;
use MailOptin\Core\EmailCampaigns\Shortcodes;
use MailOptin\Core\EmailCampaigns\TemplateTrait;
use MailOptin\Core\EmailCampaigns\TemplatifyInterface;
use MailOptin\Core\EmailCampaigns\VideoToImageLink;
use MailOptin\Core\Repositories\EmailCampaignRepository as ER;
use WP_Post;


class Templatify implements TemplatifyInterface
{
    use TemplateTrait;

    protected $post;
    protected $email_campaign_id;
    protected $template_class;
    protected $post_content_length;

    /**
     * @param null|int $email_campaign_id
     * @param mixed $post could be WP_Post object, post ID or stdClass for customizer preview
     */
    public function __construct($email_campaign_id, $post = null)
    {
        //used for sending test emails.
        if ($post instanceof \stdClass) {
            $this->post = $post;
        } else {
            $this->post = get_post($post);
        }

        $this->email_campaign_id   = $email_campaign_id;
        $this->template_class      = ER::get_template_class($email_campaign_id);
        $this->post_content_length = absint(ER::get_customizer_value($email_campaign_id, 'post_content_length'));
    }

    public function post_content_forge()
    {
        $preview_structure = EmailCampaignFactory::make($this->email_campaign_id)->get_preview_structure();

        $preview_structure = str_replace('{{post.feature.image}}', $this->feature_image($this->post), $preview_structure);

        $search = array(
            '{{post.title}}',
            '{{post.content}}',
            '{{post.url}}',
            '{{post.meta}}'
        );

        $replace = [
            $this->post->post_title,
            wpautop($this->post_content($this->post)),
            $this->post_url($this->post),
            $this->post_meta($this->post)
        ];

        return str_replace($search, $replace, $preview_structure);
    }

    /**
     * Turn {@see WP_Post} object to email campaign template.
     *
     * @return mixed
     */
    public function forge()
    {
        do_action('mailoptin_email_template_before_forge', $this->email_campaign_id, $this->template_class);

        if (ER::is_code_your_own_template($this->email_campaign_id)) {
            $content              = ER::get_customizer_value($this->email_campaign_id, 'code_your_own');
            $templatified_content = (new Shortcodes($this->email_campaign_id))->from($this->post)->parse($content);
        } else {
            $templatified_content = do_shortcode($this->post_content_forge());
        }

        $templatified_content = apply_filters('mo_new_publish_post_post_templatify_forge', $templatified_content, $this->post, $this);

        $content = (new VideoToImageLink($templatified_content))->forge();

        if ( ! is_customize_preview()) {
            $content = \MailOptin\Core\emogrify($content, true);
        }

        return $this->replace_footer_placeholder_tags(
        // we found out urlendcode was been done especially to the url part. previously we were doing
        // str_replace(['%5B', '%5D', '%7B', '%7D'], ['[', ']', '{', '}'], $content) going forward
        // urldecode is the best way.
            urldecode($content)
        );
    }
}