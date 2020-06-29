<?php

namespace MailOptin\Core\Admin\Customizer\EmailCampaign;


use MailOptin\Core\EmailCampaigns\Newsletter\Templatify;

class NewsletterTemplatePreview extends Templatify
{
    public function newsletter_content()
    {
        $instance = EmailCampaignFactory::make($this->email_campaign_id);

        $preview_structure = $instance->get_preview_structure();

        $content = $this->builderHtml();

        $search = ['{{newsletter.content}}'];

        $replace = [$content];

        return str_replace($search, $replace, $preview_structure);
    }
}