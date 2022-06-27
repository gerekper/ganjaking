<?php

if (!defined('ABSPATH')) {
    exit;
}

class UP_UserAjax extends UP_Ajax
{
    /**
     * User ajax events instance.
     *
     * @var null
     */
    protected static $instance = null;

    /**
     * Register ajax events. true|false for priv/nopriv ajax methods.
     *
     * @var array
     */
    protected $ajax_events = [
        'get_user_posts' => true,
        'connect_user' => true,
        'user_invite' => true,
    ];

    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->registerAjaxEvents();
    }

    public static function connect_user()
    {

        $output['modal_msg'] = __('Your request has been sent', 'userpro');
        $output['action'] = 'connect';

        $current_user    = wp_get_current_user();
        $current_user_id = $current_user->ID;

        $requested_userid = (int)$_POST['user_id'];
        userpro_mail($requested_userid, 'userpro_connect_request');

        $userrequest = (array)get_user_meta($requested_userid, '_userpro_users_request', TRUE);
        if(isset($userrequest['0'])) {
            unset($userrequest['0']);
        }
        $userrequest[$current_user_id] = 1;
        update_user_meta($requested_userid, '_userpro_users_request', $userrequest);

        wp_send_json_success($output);
    }

    /**
     * Get user posts with ajax.
     * @since 4.9.31
     */
    public static function get_user_posts()
    {
        try{
            $page = (int)$_POST['page'];
            $user_id = (int) $_POST['user_id'];

            $paged = ( isset($page) ) ? $page : 1;
            $args = array(
                'author'        =>  $user_id,
                'orderby'       =>  'post_date',
                'order'         =>  'ASC',
                'posts_per_page' => 5,
                'paged' => $paged,
                'post_status' => ['publish', 'pending']
            );
            $query = new WP_Query( $args );

            $posts = $query->posts;

            $html = '';

            foreach ($posts as $post){
                $html .= ' <article>
            <a href="'.get_permalink($post->ID).'"><h2>'.$post->post_title.'</h2></a>
            <i class="up-status up-status--'.$post->post_status.'"></i>
            </article>';
            }
            wp_send_json_success($html, 200);
        }catch (Exception $e){
            wp_send_json_error( array( 'error' => $e->getMessage() ) );
        }
    }
}