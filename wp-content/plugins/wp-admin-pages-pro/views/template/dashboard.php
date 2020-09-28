<?php

remove_action('all_admin_notices', array( WU_Admin_Pages(), 'replace_dashboard'), 9999);

do_action('all_admin_notices');

// wp_dashboard_setup();
?>

<!-- New Wrap with custom welcome screen-->
<div class="wrap wuapc-dashboard">
    <h2><?php _e('Dashboard'); ?></h2>

    <?php

    $classes = 'welcome-panel';

    $option = get_user_meta( get_current_user_id(), 'show_welcome_panel', true );

	// 0 = hide, 1 = toggled to show or single site creator, 2 = multisite site owner
    $hide = 0 == $option;

	if ( $hide ) {

        $classes .= ' hidden';

    } // end if;

	?>
    <div id="welcome-panel" class=" <?php echo esc_attr( $classes ); ?>">
        <?php wp_nonce_field( 'welcome-panel-nonce', 'welcomepanelnonce', false ); ?>
        <a class="welcome-panel-close" href="<?php echo esc_url( admin_url( '?welcome=0' ) ); ?>" aria-label="<?php esc_attr_e( 'Dismiss the welcome panel' ); ?>"><?php _e( 'Dismiss' ); ?></a>
        <?php do_action( 'welcome_panel' ); ?>
    </div>


    <div id="dashboard-widgets-wrap">

    <?php wp_dashboard(); ?>

    <div class="clear"></div>
    </div><!-- dashboard-widgets-wrap -->

</div><!-- wrap -->

<?php

wp_print_community_events_templates();

require( ABSPATH . 'wp-admin/admin-footer.php' );

exit;
