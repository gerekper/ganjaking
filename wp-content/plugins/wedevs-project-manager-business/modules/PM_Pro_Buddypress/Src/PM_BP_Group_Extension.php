<?php
namespace WeDevs\PM_Pro\Modules\PM_Pro_Buddypress\Src;

use BP_Group_Extension;
use WeDevs\PM\Core\WP\Enqueue_Scripts;
use WeDevs\PM\Core\WP\Register_Scripts;
use WeDevs\PM_Pro\Core\WP\Enqueue_Scripts as Pro_Enqueue_Scripts;
use WeDevs\PM_Pro\Core\WP\Register_Scripts as Pro_Register_Scripts;

if ( class_exists( 'BP_Group_Extension' ) ) {

    class PM_BP_Group_Extension extends BP_Group_Extension {

        /**
         * Constructor
         * @since 0.1
         */
        function __construct() {
            $args = array(
                'slug' => sanitize_title( __( pm_pro_bp_slug_name(), 'pm-pro' ) ),
                'name' => __( 'Projects', 'pm-pro' ),
            );
            parent::init( $args );

            //add_action( 'pm_load_shortcode_script', 'enqueue_scripts' );
        }

        /**
         * Loads the content of the tab
         *
         * This function does a few things. First, it loads the subnav, which is visible on every
         * CP BP subtab. Then, it decides which template should be loaded, based on the current
         * view (determined by the URL). It then checks to see whether the template in question
         * has been overridden in the active theme or its parent, using locate_template(). Finally,
         * the proper template is loaded.
         *
         * @package    CollabPress
         * @subpackage CP BP
         * @since      1.2
         */
        function display( $group_id = NULL ) {

            if ( ! is_user_logged_in() ) {
                return wp_login_form( array( 'echo' => false ) );
            }

            if ( ! groups_is_user_member( get_current_user_id(), $group_id ) ) {
                echo '<div id="message" class="info"><p>';
                _e( 'Only group members are authorized to access this page.', 'pm-pro' );
                echo '</p></div>';
                return;
            }

            $project_id = isset( $_GET['project_id'] ) ? intval( $_GET['project_id'] ) : 0;
            $this->enqueue_scripts();
            ?>

            <div id="wedevs-pm"></div>
            <?php

        }

        public function enqueue_scripts() {

             //pro scripts
            Pro_Register_Scripts::scripts();
            Pro_Register_Scripts::styles();

            // free scripts
            Register_Scripts::scripts();
            Register_Scripts::styles();

            if( pm_pro_is_module_active( 'Sub_Tasks/Sub_Tasks.php' ) ) {
                pm_pro_enqueue_sub_tasks_script();
            }

            if( pm_pro_is_module_active( 'Time_Tracker/Time_Tracker.php' ) ) {
                pm_pro_enqueue_time_tracker_script();
            }
            if( pm_pro_is_module_active( 'Kanboard/Kanboard.php' ) ) {
                pm_pro_enqueue_kanboard_script();
            }
            if( pm_pro_is_module_active( 'Gantt/Gantt.php' ) ) {
                pm_pro_gantt_script();
            }
            if( pm_pro_is_module_active( 'Invoice/Invoice.php' ) ) {
                pm_pro_invoice_scripts();
            }

            wp_enqueue_style( 'pm-frontend-style' );

            wp_enqueue_script( 'pm-bp', plugins_url( '../views/assets/js/pm-buddypress.js', __FILE__ ), array('pm-config'), config( 'app.version' ), true );
            // module script load
            wp_localize_script( 'pm-bp', 'PM_BP_Vars', [
                'group_id'       => $this->group_id,
                'show_role_form' => false
            ] );

            //pro scripts
            Pro_Enqueue_Scripts::scripts();
            Pro_Enqueue_Scripts::styles();

            // free scripts
            Enqueue_Scripts::scripts();
            Enqueue_Scripts::styles();


        }
    }
}

