<?php
namespace WeDevs\PM_Pro\Core\Notifications\Emails;

/**
* Daily Digest Email notification
*/

use WeDevs\PM\Core\Notifications\Email;
use WeDevs\PM\User\Models\User;
use WeDevs\PM\User\Models\User_Role;

class Daily_Digest extends Email {

    function __construct() {
        $this->active_daily_digest();

        //add_filter( 'cron_schedules', array( $this, 'add_my_custom_time' ) );
        add_action( 'pm_daily_digest', array( $this, 'daily_digest' ) );
        //$this->daily_digest();
    }

    // function add_my_custom_time( $schedules ) {
    //      $schedules['sixty_seconds_pm'] = array(
    //          'interval' => 60,
    //          'display' => esc_html__( 'Every 60 Seconds for pm' ),
    //      );

    //     return $schedules;
    // }


    /**
     * Run actions on `plugins_loaded` hook
     *
     * @since 2.0.0
     *
     * @return void
     */
    public function active_daily_digest() {
        if ( ! wp_next_scheduled( 'pm_daily_digest' ) ) {
            wp_schedule_event( time(), 'daily', 'pm_daily_digest' );
        }
    }

    /**
     * Daily digest email notification function
     * @since 2.0.0
     *
     * @return void
     */
    public function daily_digest() {

        if ( !$this->is_daily_digest_enable() ) {
            return;
        }

        $user_ids = User_Role::get(['user_id'])->pluck('user_id')->unique()->values();
        $users    = User::with( 'projects', 'projects.task_lists', 'projects.task_lists.tasks' )->find( $user_ids );

        $subday        = \Carbon\Carbon::now()->subDay(1);
        $now           = \Carbon\Carbon::now()->addDay(3);
        $template_name = apply_filters( 'pm_daily_digest_email_template_path', $this->get_template_path( '/html/daily_digest.php' ) );
        $subject       = sprintf( __( '[%s] Daily digest -', 'pm' ), $this->get_blogname() );


        if ( ! $users->isEmpty() ) {
            foreach ( $users as $user ) {
                $task_ids    = [];
                $project_ids = [];

                if ( !$this->is_user_enable_daily_digest( $user->ID ) ) {
                    continue ;
                }

                $project_ids = $user->projects->where('status', 'incomplete')->pluck('id')->unique()->values();

                if ( empty( $project_ids ) ) {
                    continue;
                }

                $tasks = $user->tasks()
                    ->where( pm_tb_prefix() . 'pm_tasks.status', 0 )
                    ->whereIn( pm_tb_prefix() . 'pm_tasks.project_id', $project_ids)
                    ->where( function ( $query ) use ( $subday, $now ) {

                        $query->whereBetween('due_date', [$subday, $now])
                            ->orWhereBetween('start_at', [$subday, $now]);

                } )->get()->toArray();

                if ( empty( $tasks ) ) {
                    continue ;
                }
                //pmpr();
                $task_ids    = wp_list_pluck( $tasks, 'id' );
                $project_ids = wp_list_pluck( $tasks, 'project_id' );

                $message = $this->get_content_html( $template_name, [
                    'user_name'   => $user->display_name,
                    'user_id'     => $user->ID,
                    'projects'    => $user->projects,
                    'project_ids' => $project_ids,
                    'tasks_ids'   => $task_ids,
                ] );

                $this->send( $user->user_email, $subject, $message );

            }
        }
    }

    public function is_user_enable_daily_digest( $user_id ) {
        $status =  get_user_meta( $user_id, '_user_daily_digets_status', true );

        if ( empty( $status ) ){
            return true;
        }

        return $status === 'on';
    }

    public function is_daily_digest_enable () {

        if ( function_exists( 'pm_get_setting' ) ) {
            $digest = pm_get_setting( 'daily_digest' );

            if ( $digest == 'false' || $digest === false ) {
                return false;
            }

            if ( $digest == 'true' || $digest === true ) {
                return true;
            }

        }

        return true;
    }
}
