<?php

namespace MailOptin\Core\EmailCampaigns;

class Misc
{
    public function __construct()
    {
        $this->date_shortcode();
    }

    public function date_shortcode()
    {
        add_shortcode('mo_date', function ($atts) {

            if (isset($atts['format']) && ! empty($atts['format'])) {
                return date(sanitize_text_field($atts['format']));
            }

            return date('l jS');
        });
    }

    public static function parse_email_subject($subject)
    {
        $result = preg_replace('/{{(date.+)}}/', '[mo_$1]', $subject);
        if ($result) {
            $subject = $result;
        }

        return do_shortcode($subject);
    }

    /**
     * Singleton.
     *
     * @return self
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