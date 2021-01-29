<?php

namespace WeDevs\PM_Pro\Core\WP;

use WeDevs\PM_Pro\Core\WP\Output;
use WeDevs\PM_Pro\Core\WP\Enqueue_Scripts;
use WeDevs\PM_Pro\User\Models\User;
use WeDevs\PM\User\Models\User_Role;
use WeDevs\PM_Pro\Core\Update\Update as License;

class Menu {

	private static $capability = 'read';

	public static function admin_menu( $home ) {

		global $submenu;

        $submenu['pm_projects']['calendar'] = [ __( 'Calendar', 'pm-pro' ), self::$capability, 'admin.php?page=pm_projects#/calendar' ];

        if ( pm_user_can_access( pm_manager_cap_slug() ) ) {
            $submenu['pm_projects'][] = [ __( 'Progress', 'pm-pro' ), self::$capability, 'admin.php?page=pm_projects#/progress' ];
        }

        if ( pm_user_can_access( pm_manager_cap_slug() ) ) {
            $submenu['pm_projects']['reports'] = [ __( 'Reports', 'pm-pro' ), self::$capability, 'admin.php?page=pm_projects#/reports' ];
        }

        if ( pm_user_can_access( pm_manager_cap_slug() ) ) {
            $submenu['pm_projects'][] = [ __( 'Modules', 'pm-pro' ), self::$capability, 'admin.php?page=pm_projects#/modules' ];
        }

		add_action( 'admin_print_styles-' . $home, array( 'WeDevs\\PM_Pro\\Core\WP\\Menu', 'scripts' ) );

		do_action( 'pm_pro_menu', $home );
	}

	public static function scripts() {
		Enqueue_Scripts::scripts();
		Enqueue_Scripts::styles();
	}

    public function create_frontend_menu($wp_admin_bar) {
        global $wp_admin_bar;

        /* Check that the admin bar is showing and user has permission... */
        if ( !is_admin_bar_showing() ) {
            return;
        }

        /* Add the main siteadmin menu item */
        global $wp_admin_bar;

        if ( get_option( 'permalink_structure' ) ) {
            $href = home_url( 'pm' );
        } else {
            $href = home_url( '?' . pm_register_query_var() . '=' . pm_frontend_slug() );
        }

        $wp_admin_bar->add_menu(
            [
                'parent' => 'site-name',
                'title'  => __('PM Frontend', 'pm-pro'),
                'id'     => 'pm-pro-frontend-menu',
                'href'   => $href,
                'meta'   => array('target' => '_blank')
            ]
        );
    }
}
