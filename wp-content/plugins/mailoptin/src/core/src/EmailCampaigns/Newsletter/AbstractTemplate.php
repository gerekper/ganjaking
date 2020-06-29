<?php

namespace MailOptin\Core\EmailCampaigns\Newsletter;

use MailOptin\Core\EmailCampaigns\TemplateTrait;

abstract class AbstractTemplate extends \MailOptin\Core\EmailCampaigns\AbstractTemplate
{
    use TemplateTrait;

    public function __construct($email_campaign_id)
    {
        parent::__construct($email_campaign_id);

        add_filter('mo_ecb_elements_default_values', [$this, 'email_content_builder_element_defaults']);

        add_action('mo_get_preview_structure_before_closing_head', [$this, 'google_fonts']);
    }

    public function get_font_family_stack($font_family)
    {
        switch ($font_family) {
            case 'Arial':
                return "Arial, 'Helvetica Neue', Helvetica, sans-serif";
            case 'Comic Sans MS':
                return "'Comic Sans MS', 'Marker Felt-Thin', Arial, sans-serif";
            case 'Courier New':
                return "'Courier New', Courier, 'Lucida Sans Typewriter', 'Lucida Typewriter', monospace";
            case 'Georgia':
                return "Georgia, Times, 'Times New Roman', serif";
            case 'Helvetica':
                return "'Helvetica Neue', Helvetica, Arial, Verdana, sans-serif";
            case 'Lucida':
                return "'Lucida Sans Unicode', 'Lucida Grande', sans-serif";
            case 'Tahoma':
                return "Tahoma, Verdana, Segoe, sans-serif";
            case 'Times New Roman':
                return "'Times New Roman', Times, Baskerville, Georgia, serif";
            case 'Trebuchet MS':
                return "'Trebuchet MS', 'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', Tahoma, sans-serif";
            case 'Verdana':
                return "Verdana, Geneva, sans-serif";
            case 'Arvo':
                return "Arvo, Courier, Georgia, serif";
            case 'Lato':
                return "Lato, 'Helvetica Neue', Helvetica, Arial, sans-serif";
            case 'Lora':
                return "Lora, Georgia, 'Times New Roman', serif";
            case 'Merriweather':
                return "Merriweather, Georgia, 'Times New Roman', serif";
            case 'Merriweather Sans':
                return "'Merriweather Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif";
            case 'Noticia Text':
                return "'Noticia Text', Georgia, 'Times New Roman', serif";
            case 'Open Sans':
                return "'Open Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif";
            case 'Playfair Display':
                return "'Playfair Display', Georgia, 'Times New Roman', serif";
            case 'Roboto':
                return "Roboto, 'Helvetica Neue', Helvetica, Arial, sans-serif";
            case 'Source Sans Pro':
                return "'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif";
            case 'Oswald':
                return "Oswald, 'Trebuchet MS', 'Lucida Grande', 'Lucida Sans Unicode', 'Lucida Sans', Tahoma, sans-serif";
            case 'Raleway':
                return "Raleway, 'Century Gothic', CenturyGothic, AppleGothic, sans-serif";
            case 'Permanent Marker':
                return "'Permanent Marker', Tahoma, Verdana, Segoe, sans-serif";
            case 'Pacifico':
                return "Pacifico, 'Arial Narrow', Arial, sans-serif";
        }

        return $font_family;
    }

    public function google_fonts()
    {
        echo <<<HTML
<!--[if !mso]><!-->
<link href="https://fonts.googleapis.com/css?family=Arvo:400,400i,700,700i|Lato:400,400i,700,700i|Lora:400,400i,700,700i|Merriweather:400,400i,700,700i|Merriweather+Sans:400,400i,700,700i|Noticia+Text:400,400i,700,700i|Open+Sans:400,400i,700,700i|Playfair+Display:400,400i,700,700i|Roboto:400,400i,700,700i|Source+Sans+Pro:400,400i,700,700i|Oswald:400,400i,700,700i|Raleway:400,400i,700,700i|Permanent+Marker:400,400i,700,700i|Pacifico:400,400i,700,700i" rel="stylesheet">
<!--<![endif]-->
HTML;

    }

    abstract function email_content_builder_element_defaults($defaults);

    abstract function text_block($id, $settings);

    abstract function button_block($id, $settings);

    abstract function divider_block($id, $settings);

    abstract function image_block($id, $settings);

    abstract function spacer_block($id, $settings);
}