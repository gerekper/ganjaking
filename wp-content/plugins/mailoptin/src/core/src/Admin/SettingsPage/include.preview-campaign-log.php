<?php

use MailOptin\Core\Logging\CampaignLogRepository;

$campaign      = CampaignLogRepository::instance()->getById(intval($_GET['id']));
$campaign_type = "content_" . sanitize_text_field($_GET['type']);

if (isset( $_GET['type'] ) && 'text' == $_GET['type']) {
    echo str_replace(['%5B', '%5D'], ['[', ']'], nl2br($campaign->$campaign_type));
} else {
    echo $campaign->$campaign_type;
}