<?php

defined( 'ABSPATH' ) || exit;
// Add template parts to actions
add_action('up_profile_head', 'up_professional_before', 10 );

add_action('up_profile_head', 'up_professional_cover', 20 );

add_action('up_profile_left_side', 'up_professional_before_left', 10);

add_action('up_profile_left_side', 'up_professional_avatar', 20);

add_action('up_profile_left_side', 'up_professional_before_left_content', 30);

if(userpro_get_option('modstate_social') === '1')
{
    add_action('up_profile_left_side', 'up_professional_followers', 40);
}

if(userpro_get_option('enable_connect') === 'y')
{
    add_action('up_profile_left_side', 'up_professional_connections', 50);
}
if(userpro_get_option('enable_post_editor') === 'y')
{
    add_action('up_profile_left_side', 'up_professional_posts', 60);
}

add_action('up_profile_left_side', 'up_professional_after_left_content', 70);

add_action('up_profile_left_side', 'up_professional_after_left', 80);

// right side template parts
add_action('up_profile_right_side', 'up_professional_before_right', 10);

add_action('up_profile_right_side', 'up_professional_buttons', 20);

add_action('up_profile_right_side', 'up_professional_username', 30);

add_action('up_profile_right_side', 'up_professional_tabs', 40);

add_action('up_profile_right_side', 'up_professional_after_right', 50);

add_action('up_profile_footer', 'up_professional_after', 10 );


function up_professional_tabs()
{
    up_get_template_part('layout4/template/right-side/profile-tabs');
}
function up_professional_username()
{
    up_get_template_part('layout4/template/right-side/username');

}
function up_professional_buttons()
{
    up_get_template_part('layout4/template/right-side/buttons');

}

function up_professional_before_right()
{
    up_get_template_part('layout4/template/right-side/before-right-side');

}

function up_professional_after_right()
{
    up_get_template_part('layout4/template/right-side/after-right-side');

}

function up_professional_avatar()
{
    up_get_template_part('layout4/template/left-side/avatar');
}
function up_professional_before_left_content()
{
    up_get_template_part('layout4/template/left-side/before-left-content');
}
function up_professional_after_left_content()
{
    up_get_template_part('layout4/template/left-side/after-left-content');
}
function up_professional_followers()
{
    up_get_template_part('layout4/template/left-side/followers');
}
function up_professional_connections()
{
    up_get_template_part('layout4/template/left-side/connections');
}
function up_professional_posts()
{
    up_get_template_part('layout4/template/left-side/posts');
}
function up_professional_before_left()
{
    up_get_template_part('layout4/template/left-side/before-left-side');

}
function up_professional_after_left()
{
    up_get_template_part('layout4/template/left-side/after-left-side');

}
function up_professional_before()
{
    up_get_template_part('layout4/template/header/before-layout');

}

function up_professional_after()
{
    up_get_template_part('layout4/template/footer/after-layout');

}
// Professional layout
function up_professional_cover()
{
    up_get_template_part('layout4/template/cover/cover');
}

