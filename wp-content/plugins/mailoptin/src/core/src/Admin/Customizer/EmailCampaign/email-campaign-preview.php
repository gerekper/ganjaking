<?php

namespace MailOptin\Core\Admin\Customizer\EmailCampaign;

use MailOptin\Core\Repositories\EmailCampaignRepository;

$email_campaign_id = absint($_REQUEST['mailoptin_email_campaign_id']);

$email_campaign_type = EmailCampaignRepository::get_email_campaign_type($email_campaign_id);

$email_campaign_type_namespace = EmailCampaignFactory::get_campaign_type_namespace($email_campaign_type);

$template_preview_class = "MailOptin\\Core\\Admin\\Customizer\\EmailCampaign\\{$email_campaign_type_namespace}TemplatePreview";

switch ($email_campaign_type) {
    case EmailCampaignRepository::NEW_PUBLISH_POST:
        /** @var NewPublishPostTemplatePreview $template_preview_instance */
        $template_preview_instance = new $template_preview_class($email_campaign_id, null);
        break;
    case EmailCampaignRepository::POSTS_EMAIL_DIGEST:
        /** @var PostsEmailDigestTemplatePreview $template_preview_instance */
        $template_preview_instance = new $template_preview_class($email_campaign_id);
        break;
    case EmailCampaignRepository::NEWSLETTER:
        /** @var NewsletterTemplatePreview $template_preview_instance */
        $template_preview_instance = new $template_preview_class($email_campaign_id);
        break;
}

echo $template_preview_instance->forge();

// this is not in AbstractTemplate as in AbstractOptinForm so it doesn't get templatified/emogrified along with the email template
// on customizer preview.
// hide any element that might have been injected to footer by any plugin.
echo '<div style="display:none">';
wp_footer();
echo '</div>';