<?php

namespace MailOptin\Core\OptinForms;

use MailOptin\Core\Admin\Customizer\OptinForm\OptinFormFactory;
use MailOptin\Core\Repositories\OptinCampaignsRepository as OCR;

class Shortcodes
{
    public function __construct()
    {
        add_shortcode('mo-optin-form', [$this, 'optin_shortcode']);

        add_action('mo_optin_form', [$this, 'template_tag']);

        add_shortcode('mo-click-launch', [$this, 'optin_click_launch']);
    }

    public function optin_click_launch($atts, $content = null)
    {
        if ( ! defined('MAILOPTIN_DETACH_LIBSODIUM')) return;

        $atts = shortcode_atts(
            [
                'id'    => '',
                'class' => '',
                'link'  => __('click here', 'mailoptin'),
            ],
            $atts
        );

        $id    = esc_attr($atts['id']);
        $class = esc_attr($atts['class']);
        if ( ! empty($class)) {
            $class = " $class";
        }

        $link = sanitize_text_field($atts['link']);

        if (empty($id)) return;

        $optin_campaign_uuid = is_numeric($id) ? OCR::get_optin_campaign_uuid($id) : $id;
        $optin_campaign_id   = OCR::get_optin_campaign_id_by_uuid($optin_campaign_uuid);

        $optin_type = OCR::get_optin_campaign_type($optin_campaign_id);

        if (in_array($optin_type, ['inpost', 'sidebar'])) {
            return sprintf(__('Click trigger does not support %s optin.', 'mailoptin'), $optin_type);
        }

        $anchor_text = ! empty($content) ? $content : $link;

        return sprintf(
            '<a href="#" class="mailoptin-click-trigger%s" data-optin-uuid="%s">%s</a>',
            $class,
            $optin_campaign_uuid,
            $anchor_text
        );
    }

    public function template_tag($id)
    {
        if ( ! defined('MAILOPTIN_DETACH_LIBSODIUM')) return;

        if ( ! isset($id) || empty($id)) return;

        $optin_campaign_id = is_numeric($id) ? $id : OCR::get_optin_campaign_id_by_uuid(sanitize_text_field($id));

        echo $this->get_optin($optin_campaign_id);
    }

    public function optin_shortcode($atts)
    {
        if ( ! isset($atts['id']) || empty($atts['id'])) return;

        $optin_campaign_id = is_numeric($atts['id']) ? $atts['id'] : OCR::get_optin_campaign_id_by_uuid(sanitize_text_field($atts['id']));

        return $this->get_optin($optin_campaign_id);
    }

    /**
     * @param $optin_campaign_id
     *
     * @return string
     */
    public function get_optin($optin_campaign_id)
    {
        if (\MailOptin\Core\is_mailoptin_customizer_preview()) return '';

        if (isset($_GET['mohide']) && $_GET['mohide'] == 'true') return '';

        // $optin_campaign_id could be null if invalid uuid is supplied.
        if ( ! $optin_campaign_id) return __('Invalid optin campaign ID', 'mailoptin');

        $optin_campaign_id = OCR::choose_split_test_variant($optin_campaign_id);

        $optin_type = OCR::get_optin_campaign_type($optin_campaign_id);

        if ( ! in_array($optin_type, ['inpost', 'sidebar'])) {
            return sprintf(__('Shortcode embed does not support %s optin.', 'mailoptin'), $optin_type);
        }

        return OptinFormFactory::build(absint($optin_campaign_id));
    }

    /**
     * Singleton poop
     *
     * @return Shortcodes
     */
    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}