<?php

namespace MailOptin\Core\OptinForms;

use MailOptin\Core\Repositories\OptinCampaignsRepository as OCR;

trait UserTargetingRuleTrait
{
    /**
     * Determine if the optin passes user targeting rules.
     *
     * @param int $id
     * @return bool
     */
    public function user_targeting_rule_checker($id)
    {
        switch (OCR::get_customizer_value($id, 'who_see_optin')) {
            case 'show_to_roles':
            
                //If user is logged out abort early
                if (!is_user_logged_in()) return false;

                //Fetch roles and abort if none is specified
                $roles = OCR::get_customizer_value($id, 'show_to_roles');
                if (empty($roles) || !is_array($roles)) return false;

                //Check if user has any of the allowed user roles
                $user = wp_get_current_user();
                return 0 !== count(array_intersect($roles, (array) $user->roles));
                break;
            case 'show_logged_in':
                if (!is_user_logged_in()) return false;
                break;
            case 'show_non_logged_in':
                if (is_user_logged_in()) return false;
                break;
            case 'show_all':
                return true;
                break;
        }

        return true;
    }
}