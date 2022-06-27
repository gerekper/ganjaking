<?php

defined( 'ABSPATH' ) || exit;

/**
 * Check if user id is equal to current user id.
 *
 * @param int $user_id
 * @since 4.9.31
 * @return bool
 */
function is_current_user_profile($user_id = 0)
{
    if($user_id === get_current_user_id())
    {
        return TRUE;
    }
    return FALSE;
}

/**
 * Get user profile id.
 * @since 4.9.31
 * @return bool|mixed|string
 */

function up_get_profile_user_id()
{
    $user = get_query_var('up_username');
    if ($user) {
        $user = userpro_get_view_user($user);
        return $user;
    }
    return FALSE;
}