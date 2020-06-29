<?php

namespace MailOptin\Core\Admin\Customizer\OptinForm;

use MailOptin\Core\OptinForms\AbstractOptinForm;
use MailOptin\Core\Repositories\OptinCampaignsRepository;

/**
 * Create a optin form class instance from its ID or class name.
 */
class OptinFormFactory
{
    /**
     * @param int $optin_campaign_id
     *
     * @return false|AbstractOptinForm
     */
    public static function make($optin_campaign_id)
    {
        $db_optin_class = OptinCampaignsRepository::get_optin_campaign_class($optin_campaign_id);
        $optin_type = ucfirst(OptinCampaignsRepository::get_optin_campaign_type($optin_campaign_id));

        // first $db_optin_class is the template class namespace.
        $optin_class = apply_filters(
            'mailoptin_register_optin_class',
            "\\MailOptin\\Core\\OptinForms\\$optin_type\\$db_optin_class",
            $optin_campaign_id,
            $db_optin_class,
            $optin_type
        );

        if (!class_exists($optin_class)) {
            return false;
        }

        return new $optin_class($optin_campaign_id);
    }

    /**
     * Build or make optin form (HTML) structure.
     *
     * @param int $optin_campaign_id
     *
     * @return string
     */
    public static function build($optin_campaign_id)
    {
        $optinInstance = self::make($optin_campaign_id);

        if (!$optinInstance) return '';

        $optin_form = $optinInstance->get_optin_form_structure();
        $optin_form .= $optinInstance->webfont_loader_js_script();
        return $optin_form;
    }

}