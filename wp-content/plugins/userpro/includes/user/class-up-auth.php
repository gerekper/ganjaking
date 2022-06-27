<?php

if (!defined('ABSPATH')) {
    exit;
}

class UP_Auth extends UP_Data
{
    public function __construct()
    {
        // Silence is golden
    }

    /**
     * Old UserPro pending activation code
     *
     * @todo : Refactor this code block.
     */
    public function pendingActivation()
    {
        $uppayment = get_option('userpro_payment');
        if (userpro_get_option('users_approve') === '2') {

            if ($uppayment['userpro_payment_option'] == 'y') {
                $output = '<div class="userpro-message userpro-message-ajax"><p>' . __('Your email is pending verification/Payment is Pending. Please activate your account.',
                        'userpro') . '</p></div>';
            } else {

                $output = '<div class="userpro-message userpro-message-ajax"><p>' . __('Your email is pending verification. Please activate your account.',
                        'userpro') . '</p></div>';

            }
        } else {

            if ($uppayment['userpro_payment_option'] == 'y') {
                $output = '<div class="userpro-message userpro-message-ajax"><p>' . __('Your account is currently being reviewed/Payment is Pending. Thanks for your patience.',
                        'userpro') . '</p></div>';
            } else {
                $output = '<div class="userpro-message userpro-message-ajax"><p>' . __('Your account is currently being reviewed. Thanks for your patience.',
                        'userpro') . '</p></div>';
            }
        }
        wp_logout();
        return $output;
    }

}
