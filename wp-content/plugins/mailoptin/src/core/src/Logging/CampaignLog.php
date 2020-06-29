<?php

namespace MailOptin\Core\Logging;

class CampaignLog implements CampaignLogInterface
{
    /** @var mixed campaign data */
    private $data;

    /**
     * array(
     * 'title'        => sanitize_text_field($post->post_title),
     * 'content_html' => $post->content_html
     * )
     *
     * @param array $data campaign data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function email_campaign_id()
    {
        return $this->data['email_campaign_id'];
    }

    public function title()
    {
        return $this->data['title'];
    }


    public function content_html()
    {
        return $this->data['content_html'];
    }

    public function content_text()
    {
        return $this->data['content_text'];
    }

    // campaign status is draft by default as set in database table schema
}