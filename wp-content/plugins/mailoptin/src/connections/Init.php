<?php

namespace MailOptin\Connections;

class Init
{
    public static function select2_tag_connections()
    {
        return ['GetResponseConnect', 'OntraportConnect', 'ConvertKitConnect', 'InfusionsoftConnect', 'FluentCRMConnect'];
    }

    public static function text_tag_connections()
    {
        return ['AweberConnect', 'MailChimpConnect', 'ConvertFoxConnect', 'SendlaneConnect', 'DripConnect', 'ActiveCampaignConnect', 'ZohoCRMConnect', 'JiltConnect', 'WeMailConnect', 'CleverReachConnect'];
    }

    public static function no_name_mapping_connections()
    {
        return ['CleverReachConnect'];
    }

    public static function double_optin_support_connections($only_keys = false)
    {
        //True means double optin is enabled, and false means double optin is disabled by default
        $double_optin_connections = ['DripConnect' => false, 'FluentCRMConnect' => true, 'MailChimpConnect' => true, 'MailjetConnect' => false, 'MailsterConnect' => true, 'SendinblueConnect' => false];

        if($only_keys) {
            return array_keys($double_optin_connections);
        }

        return $double_optin_connections;
    }

    public static function return_name($name, $first_name, $last_name)
    {
        if (empty($name)) {

            if ( ! empty($first_name)) {

                if ( ! empty($last_name)) {
                    return $first_name . ' ' . $last_name;
                }

                return $first_name;
            }

            if ( ! empty($last_name)) {
                return $last_name;
            }
        }

        return $name;
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
        \MailOptin\SendFoxConnect\Connect::get_instance();
        \MailOptin\WeMailConnect\Connect::get_instance();
        \MailOptin\WordPressUserRegistrationConnect\Connect::get_instance();
        \MailOptin\EmmaConnect\Connect::get_instance();
        \MailOptin\OntraportConnect\Connect::get_instance();
        \MailOptin\ConvertKitConnect\Connect::get_instance();
        \MailOptin\ActiveCampaignConnect\Connect::get_instance();
        \MailOptin\CtctConnect\Connect::get_instance();
        \MailOptin\Ctctv3Connect\Connect::get_instance();
        \MailOptin\HubspotConnect\Connect::get_instance();
        \MailOptin\InfusionsoftConnect\Connect::get_instance();
        \MailOptin\CleverReachConnect\Connect::get_instance();
        \MailOptin\JiltConnect\Connect::get_instance();
        \MailOptin\MailerliteConnect\Connect::get_instance();
        \MailOptin\EmailOctopusConnect\Connect::get_instance();
        \MailOptin\FluentCRMConnect\Connect::get_instance();
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
        \MailOptin\LeadBankConnect\Connect::get_instance();
        \MailOptin\FacebookCustomAudienceConnect\Connect::get_instance();
        \MailOptin\FormidableFormConnect\Connect::get_instance();
        \MailOptin\ForminatorFormConnect\Connect::get_instance();
        GoogleAnalytics::get_instance();
    }
}