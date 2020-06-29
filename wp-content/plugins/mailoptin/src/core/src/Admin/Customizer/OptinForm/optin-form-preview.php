<?php

use MailOptin\Core\Admin\Customizer\OptinForm\OptinFormFactory;

$optin_campaign_id = absint($_REQUEST['mailoptin_optin_campaign_id']);

echo OptinFormFactory::make($optin_campaign_id)->get_preview_structure();