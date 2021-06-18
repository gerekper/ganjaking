<?php

if (!defined('ABSPATH')) {
    exit;
}
class UP_SocialAjax extends UP_Ajax{

    /**
     * Social Ajax instance
     *
     * @since 4.9.33
     * @var null
     */
    protected static $instance = null;


    protected $ajax_events = [
        'followAction' => false
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


    /**
     * userpro_sc_follow
     */
    public static function followAction(){

            $to = $_POST['to'];
            $from = get_current_user_id();
            $security = $_POST['security'];

            $action = $_POST['follow_action'];

            if(wp_verify_nonce( $security, 'userpro_followAction' ) ) {

                // User
                $user = new UP_User($to);

                if ($action === 'follow') {
                    $user->user_social->follow($from);
                }
                if ($action === 'unfollow') {
                    $user->user_social->unfollow($from);
                }

                $output = $user->user_social->getFollowActionPlain($from, $action);

                $output['count'] = $user->user_social->getUserFollowersCount('followers');

                wp_send_json_success($output);
            }else{
                wp_send_json_error(__('Something went wrong, sorry', 'userpro'));
            }

    }
}