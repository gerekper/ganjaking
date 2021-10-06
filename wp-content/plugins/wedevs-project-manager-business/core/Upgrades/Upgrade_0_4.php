<?php
namespace WeDevs\PM_Pro\Core\Upgrades;

/**
*   Upgrade project manager 3.0
*/
class Upgrade_0_4 {
    /*initialize */
    public function upgrade_init() {
        $this->update_module_path();
    }

    function update_module_path() {
        if ( is_multisite() ) {
            $modules = get_site_option( 'pm_pro_active_modules', '' );
        } else {
            $modules = get_option( 'pm_pro_active_modules', '' );
        }

        if ( empty( $modules ) ) {
            return;
        }

        $new_paths = array();

        foreach ( $modules as $key => $module ) {
            $new_paths[] = $this->module_path( $module );
        }

        if ( ! empty( $new_paths ) ) {
            if ( is_multisite() ) {
                update_site_option( 'pm_pro_active_modules', $new_paths );
            } else {
                update_option( 'pm_pro_active_modules', $new_paths );
            }
        }
    }

    function module_path( $path ) {
        $module = strtolower( $path );

        $updated = array (
            'custom_fields/custom_fields.php'   => 'Custom_Fields/Custom_Fields.php',
            'gantt/gantt.php'                   => 'Gantt/Gantt.php',
            'invoice/invoice.php'               => 'Invoice/Invoice.php',
            'kanboard/kanboard.php'             => 'Kanboard/Kanboard.php',
            'pm_buddypress/pm_buddypress.php'   => 'PM_Pro_Buddypress/PM_Pro_Buddypress.php',
            'sprint/sprint.php'                 => 'Sprint/Sprint.php',
            'stripe/stripe.php'                 => 'Stripe/Stripe.php',
            'sub_tasks/sub_tasks.php'           => 'Sub_Tasks/Sub_Tasks.php',
            'task_recurring/task_recurring.php' => 'Task_Recurring/Task_Recurring.php',
            'time_tracker/time_tracker.php'     => 'Time_Tracker/Time_Tracker.php',
            'woo_project/woo_project.php'       => 'Woo_Project/Woo_Project.php'
        );

        return empty( $updated[$path] ) ? $path : $updated[$path];
    }
}
