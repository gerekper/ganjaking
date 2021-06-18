<?php

    if (!defined('ABSPATH')) {
        exit;
    }

    UP_ProfessionalLayout::instance();
    global $up_user;
    /**
     * @hooked : up_professional_before priority 10
     * @hooked : up_professional_cover priority 20
     */
    do_action('up_profile_head');

    /**
     * @hooked : up_professional_before_left priority 10
     * @hooked : up_professional_avatar priority 20
     * @hooked : up_professional_followers priority 30
     * @hooked : up_professional_after_left priority 40
     * @hooked : up_professional_connections priority 50
     * @hooked : up_professional_posts priority 60
     */
    do_action('up_profile_left_side');

    /**
     * @hooked : up_professional_before_right priority 10
     * @hooked : up_professional_buttons priority 20
     * @hooked : up_professional_username priority 30
     * @hooked : up_professional_nav priority 40
     * @hooked : up_professional_profile_info priority 50
     * @hooked : up_professional_after_right priority 60
     */
    do_action('up_profile_right_side', $up_user->getUserId());

    /**
     * @hooked : up_professional_after priority 10
     */
    do_action('up_profile_footer');



