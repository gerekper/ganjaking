<?php

namespace MailOptin\Core\Admin\Customizer\EmailCampaign;


use MailOptin\Core\EmailCampaigns\NewPublishPost\Templatify;
use MailOptin\Core\Repositories\EmailCampaignRepository;

class NewPublishPostTemplatePreview extends Templatify
{
    public function __construct($email_campaign_id)
    {
        $preview_post_id = EmailCampaignRepository::get_customizer_value($email_campaign_id, 'post_as_preview');

        $post = $this->postMockObject();

        if ( ! empty($preview_post_id)) {
            $post = $preview_post_id;
        }

        parent::__construct($email_campaign_id, $post);
    }

    public function postMockObject()
    {
        $mock_post                    = new \stdClass();
        $mock_post->ID                = false;
        $mock_post->post_title        = SolitaryDummyContent::title();
        $mock_post->post_content      = SolitaryDummyContent::content();
        $mock_post->post_excerpt      = SolitaryDummyContent::excerpt();
        $mock_post->post_url          = '#';
        $mock_post->post_date         = '2018-08-01 15:19:16';
        $mock_post->post_date_gmt     = '2018-08-01 14:19:16';
        $mock_post->post_author       = get_current_user_id();
        $mock_post->post_type         = 'post';
        $mock_post->post_status       = 'publish';
        $mock_post->post_modified     = '2018-08-01 15:19:16';
        $mock_post->post_modified_gmt = '2018-08-01 14:19:16';

        return $mock_post;
    }
}