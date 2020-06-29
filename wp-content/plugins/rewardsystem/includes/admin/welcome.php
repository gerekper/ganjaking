<?php
add_action('admin_init', 'sumo_reward_points_welcome_screen_do_activation_redirect');

function sumo_reward_points_welcome_screen_do_activation_redirect() {
    if (!get_transient('_welcome_screen_activation_redirect_reward_points')) {
        return;
    }
    
    delete_transient( '_welcome_screen_activation_redirect_reward_points' );
    
    wp_safe_redirect(add_query_arg( array( 'page' => 'sumo-reward-points-welcome-page' ), SRP_ADMIN_URL ));
}

add_action('admin_menu', 'sumo_reward_points_welcome_screen_pages');

function sumo_reward_points_welcome_screen_pages() {
    add_dashboard_page(
            'Welcome To Sumo Reward Points', 'Welcome To Reward Points', 'read', 'sumo-reward-points-welcome-page', 'sumo_reward_points_welcome_screen_content'
    );
}

function sumo_reward_points_welcome_screen_content() {

    include 'reward_points_welcome_page.php';
    }

    add_action('admin_head', 'sumo_reward_points_welcome_screen_remove_menus');

    function sumo_reward_points_welcome_screen_remove_menus() {
        remove_submenu_page('index.php', 'sumo-reward-points-welcome-page');
    }
    
