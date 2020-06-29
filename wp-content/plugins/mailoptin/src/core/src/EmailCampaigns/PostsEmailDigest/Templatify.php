<?php

namespace MailOptin\Core\EmailCampaigns\PostsEmailDigest;

use MailOptin\Core\Admin\Customizer\EmailCampaign\EmailCampaignFactory;
use MailOptin\Core\EmailCampaigns\Shortcodes;
use MailOptin\Core\EmailCampaigns\TemplateTrait;
use MailOptin\Core\EmailCampaigns\TemplatifyInterface;
use MailOptin\Core\EmailCampaigns\VideoToImageLink;
use MailOptin\Core\Repositories\EmailCampaignRepository as ER;


class Templatify implements TemplatifyInterface
{
    use TemplateTrait;

    protected $email_campaign_id;
    protected $posts;

    /**
     * @param int $email_campaign_id
     * @param array $posts
     */
    public function __construct($email_campaign_id, $posts = [])
    {
        $this->email_campaign_id = $email_campaign_id;
        $this->posts             = $posts;
    }

    public function forge()
    {
        do_action('mailoptin_email_template_before_forge', $this->email_campaign_id);

        if (ER::is_code_your_own_template($this->email_campaign_id)) {
            $content              = ER::get_customizer_value($this->email_campaign_id, 'code_your_own');
            $templatified_content = (new Shortcodes($this->email_campaign_id))->fromCollection($this->posts)->parse($content);
        } else {
            $templatified_content = do_shortcode(EmailCampaignFactory::make($this->email_campaign_id, $this->posts)->get_preview_structure());
        }

        $content = (new VideoToImageLink($templatified_content))->forge();

        if ( ! is_customize_preview()) {
            $content = \MailOptin\Core\emogrify($content);
        }

        return $this->replace_footer_placeholder_tags(
            str_replace(['%5B', '%5D', '%7B', '%7D'], ['[', ']', '{', '}'], $content)
        );
    }
}