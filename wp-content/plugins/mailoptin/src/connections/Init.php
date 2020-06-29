<?php

namespace MailOptin\Connections;

class Init
{
    public static function select2_tag_connections()
    {
        return ['GetResponseConnect', 'OntraportConnect', 'ConvertKitConnect'];
    }

    public static function text_tag_connections()
    {
        return ['AweberConnect', 'MailChimpConnect', 'ConvertFoxConnect', 'SendlaneConnect', 'DripConnect', 'ActiveCampaignConnect', 'ZohoCRMConnect'];
    }

    public static function init()
    {
        \MailOptin\RegisteredUsersConnect\Connect::get_instance(); // should always come first before any connect.
        \MailOptin\MailChimpConnect\Connect::get_instance();
        \MailOptin\MailjetConnect\Connect::get_instance();
        \MailOptin\AweberConnect\Connect::get_instance();
        \MailOptin\CampaignMonitorConnect\Connect::get_instance();
        \MailOptin\VerticalResponseConnect\Connect::get_instance();
        \MailOptin\SendyConnect\Connect::get_instance();
        \MailOptin\DripConnect\Connect::get_instance();
        \MailOptin\SendlaneConnect\Connect::get_instance();
        \MailOptin\EmmaConnect\Connect::get_instance();
        \MailOptin\OntraportConnect\Connect::get_instance();
        \MailOptin\ConvertKitConnect\Connect::get_instance();
        \MailOptin\ActiveCampaignConnect\Connect::get_instance();
        \MailOptin\CtctConnect\Connect::get_instance();
        \MailOptin\HubspotConnect\Connect::get_instance();
        \MailOptin\MailerliteConnect\Connect::get_instance();
        \MailOptin\EmailOctopusConnect\Connect::get_instance();
        \MailOptin\GEMConnect\Connect::get_instance();
        \MailOptin\SendinblueConnect\Connect::get_instance();
        \MailOptin\SendGridConnect\Connect::get_instance();
        \MailOptin\MailPoetConnect\Connect::get_instance();
        \MailOptin\MailsterConnect\Connect::get_instance();
        \MailOptin\MoosendConnect\Connect::get_instance();
        \MailOptin\GetResponseConnect\Connect::get_instance();
        \MailOptin\KlaviyoConnect\Connect::get_instance();
        \MailOptin\ZohoCampaignsConnect\Connect::get_instance();
        \MailOptin\ZohoCRMConnect\Connect::get_instance();
        \MailOptin\ConvertFoxConnect\Connect::get_instance();
        \MailOptin\ElementorConnect\Connect::get_instance();
        \MailOptin\WPFormsConnect\Connect::get_instance();
        \MailOptin\NinjaFormsConnect\Connect::get_instance();
        \MailOptin\ContactForm7Connect\Connect::get_instance();
        \MailOptin\GravityFormsConnect\Connect::get_instance();
        \MailOptin\PolylangConnect\Connect::get_instance();
        \MailOptin\WPMLConnect\Connect::get_instance();
        \MailOptin\LeadBankConnect\Connect::get_instance();
        \MailOptin\FacebookCustomAudienceConnect\Connect::get_instance();
        GoogleAnalytics::get_instance();
    }
}