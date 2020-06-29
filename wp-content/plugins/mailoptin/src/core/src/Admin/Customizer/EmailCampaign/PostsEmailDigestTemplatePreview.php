<?php

namespace MailOptin\Core\Admin\Customizer\EmailCampaign;


use MailOptin\Core\EmailCampaigns\PostsEmailDigest\PostsEmailDigest;
use MailOptin\Core\EmailCampaigns\PostsEmailDigest\Templatify;
use MailOptin\Core\Repositories\EmailCampaignRepository as ER;

class PostsEmailDigestTemplatePreview extends Templatify
{
    public function __construct($email_campaign_id, array $posts = [])
    {
        parent::__construct($email_campaign_id, $this->post_collection($email_campaign_id));
    }

    public function post_collection($email_campaign_id)
    {
        $item_count = ER::get_merged_customizer_value($email_campaign_id, 'item_number');

        $default_params = [
            'posts_per_page' => $item_count,
            'post_status'    => 'publish',
            'post_type'      => 'post',
            'order'          => 'DESC',
            'orderby'        => 'post_date'
        ];

        $parameters = PostsEmailDigest::get_instance()->post_collect_query($email_campaign_id);

        $parameters = apply_filters('mo_post_digest_get_posts_args', $parameters, $email_campaign_id, 'customizer');

        $response = get_posts($parameters);

        if (empty($response)) {
            $response = get_posts($default_params);
        }

        return $response;
    }
}