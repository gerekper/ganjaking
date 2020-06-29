<?php

namespace MailOptin\Core\Admin\Customizer\EmailCampaign;

use WP_Error;

class EC_Customizer_Setting extends \WP_Customize_Setting
{

    public function validate($value)
    {
        $validations = [
            parent::validate($value),
            $this->validate_campaign_setting($value)
        ];

        foreach ($validations as $validation) {
            if (is_wp_error($validation)) {
                return $validation;
            }
        }
    }

    /**
     * Validation for campaign settings.
     *
     * This can also be used for data sanitation before saving to DB. but instead of validate, it is sanitize
     *
     * @param mixed $value
     *
     * @return WP_Error|null|bool
     */
    public function validate_campaign_setting($value)
    {
        $setting_id = $this->id;

        if (strpos($setting_id, 'schedule_digit') !== false) {
            if (!is_numeric($value)) {
                return new WP_Error('required', __('You must supply a valid numeric value.', 'mailoptin'));
            }
        }

        if (strpos($setting_id, 'post_content_length') !== false) {
            if (!is_numeric($value)) {
                return new WP_Error('required', __('You must supply a valid numeric value.', 'mailoptin'));
            }
        }
    }
}