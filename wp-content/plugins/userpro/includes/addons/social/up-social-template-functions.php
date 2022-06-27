<?php

/* Hook after profile head */
add_action('userpro_after_profile_head','userpro_social_bar', 99);

function userpro_social_bar( $args ){

    $user_id = $args['user_id'];

    if (!userpro_get_option('modstate_social') ) {
        userpro_set_option('enable_connect','n');
        return false;
    }

    // where to add the hook
    if ( in_array($args['template'], array('view','following','followers') )  && !isset($args['no_style']) ){

        ?>

        <div class="userpro-sc-bar">
            <?php do_action('userpro-social-bar', $args) ?>
        </div>
        <div class="userpro-sc-buttons">
            <?php do_action('userpro_social_buttons', $user_id); ?>
        </div>

        <?php
    }
}

/**
 * Insert Followers/Following Bar
 *
 * @since 4.9.33
 */

add_action('userpro-social-bar', 'social_bar_tabs', 10 );

function social_bar_tabs($args){

    global $userpro,$userpro_social;

    up_get_template_part('addons/social/template/follow/follow-bar', 'includes', ['user_id' => $args['user_id'],
        'userpro' => $userpro, 'userpro_social' => $userpro_social, 'template' => $args['template']]);

}
/**
 * Insert Bar button
 *
 * @since 4.9.33
 */
if(userpro_get_option('enable_connect') === 'y')
    add_action('userpro_follow_bar_before','userpro_connect_button', 10, 2);

function userpro_connect_button($user_id, $template)
{
    global $userpro;

    if (in_array($template,
        array('view', 'following', 'followers', 'connections'))) {
        up_get_template_part('addons/social/template/follow/connection-button', 'includes', ['user_id' => $user_id, 'userpro' => $userpro]);

    }
}
/**
 * Insert Follow button.
 *
 * @since 4.9.33
 */
add_action('userpro_social_buttons', 'social_bar_follow', 20 );

function social_bar_follow($user_id){
    global $userpro_social;

    up_get_template_part('addons/social/template/follow/follow-button', 'includes', ['user_id' => $user_id, 'userpro_social' => $userpro_social]);
}
