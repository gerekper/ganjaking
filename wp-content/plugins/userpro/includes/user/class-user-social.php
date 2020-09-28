<?php

if (!defined('ABSPATH')) {
    exit;
}

class UP_UserSocial
{

    /**
     * @var UP_User|null
     */
    protected $user = null;

    private $user_id = 0;

    /**
     * UP_UserSocial constructor.
     * @param UP_User $user
     */
    public function __construct(UP_User $user)
    {
        $this->user = $user;

        $this->user_id = $this->user->getUserId();
    }

    /**
     * Get User Followers ID's
     *
     * @since 4.9.31
     * @return mixed
     */
    public function getUserFollowers()
    {
        $followers = $this->user->getUserMeta('_userpro_followers_ids');

        return $followers ?? [];
    }

    /**
     * Get User Following ID's
     *
     * @since 4.9.31
     * @return mixed
     */
    public function getUserFollowing()
    {
        return $this->user->getUserMeta('_userpro_following_ids');
    }

    /**
     * Get all User Connections
     *
     * @since 4.9.31
     * @return array
     */
    public function getConnections()
    {
        $userRequest = $this->user->getUserMeta('_userpro_users_request');
        return  $userRequest ?? [];
    }

    /**
     * Get connections count
     *
     * @since 4.9.31
     * @return array|int
     */
    public function getConnectionsCount()
    {
        $connections = $this->getConnections();
        if (!empty($connections)) {
            $connections = count($connections);
        } else {
            $connections = 0;
        }

        return $connections;
    }

    /**
     * Get User Connections Data
     *
     * @since 4.9.31
     * @return array
     */
    public function getConnectionsPlain()
    {
        $connections = $this->getConnections();

        $details = array();

        foreach ($connections as $key => $value) {
            $details[] = [
                'user_id' => $key,
                'username' => $this->user->getMetaData('nickname', $key),
                'profile_picture' => get_avatar($key)
            ];
        }

        return $details;
    }

    /**
     * Connection button for profile page.
     *
     * @param $user_id
     * @since 4.9.31
     * @return string
     */
    public function getConnectionsHtml($user_id)
    {
        if (!array_key_exists($user_id, $this->getConnections())) {
            $connectionsHtml = '<a class="up-professional-btn up-professional-btn--small up-ajax-btn" href="#" 
                  data-profile-action="connect"><span><i class="fas fa-link"></i><p>' . __('Connect',
                    'userpro') . '</p></span></a>';

            return $connectionsHtml;
        }

        return null;
    }

    /**
     * Get Followers/Following count
     *
     * @param $type
     * @since 4.9.31
     * @return int
     */
    public function getUserFollowersCount($type)
    {
        switch ($type) {

            case 'followers':
                $followers = $this->getUserFollowers();

                break;

            case 'following':
                $followers = $this->getUserFollowing();

                break;
        }

        if (empty($followers)) {
            $followers = 0;
        } else {
            $followers = count($followers);
        }

        return $followers;
    }

    /**
     * Get Follow/Unfollow html template.
     *
     * @param $user_id
     * @since 4.9.31
     * @return string
     */
    public function getFollowActionHtml($user_id)
    {  
        if (array_key_exists($user_id, is_array($this->getUserFollowers()[0]) ? $this->getUserFollowers()[0] : $this->getUserFollowers() )    ) {
            $followText = __('Unfollow', 'userpro');
            $followAction = 'unfollow';
            $icon = 'fas fa-user-minus';
        } else {
            $followText = __('Follow', 'userpro');
            $followAction = 'follow';
            $icon = 'fas fa-user-plus';
        }

        $followHtml = '<a class="up-professional-btn up-professional-btn--small up-ajax-btn" href="#" data-profile-action="'
            . $followAction . '"><span><i class="' . $icon . '"></i><p>' . $followText . '</p></span></a>';

        return $followHtml;
    }

    /**
     * Get Plain data for ajax follow.
     *
     * @param $user_id
     * @since 4.9.31
     * @return array
     */
    public function getFollowActionPlain($user_id, $action)
    {
       // $followers = $this->getUserFollowers();
        if ($action === 'follow') {
            $followPlain['text'] = __('Unfollow', 'userpro');
            $followPlain['action'] = 'unfollow';
            $followPlain['icon'] = 'up-fas up-fa-minus';
        } else {
            $followPlain['text'] = __('Follow', 'userpro');
            $followPlain['action'] = 'follow';
            $followPlain['icon'] = 'up-fas up-fa-plus';
        }

        return $followPlain;
    }

    /**
     * Follow user
     *
     * @since 4.9.33
     * @param $from
     */
    public function follow($from) {

        $followers_ids = get_user_meta($this->user_id, '_userpro_followers_ids', true);

        if(empty($followers_ids)){
            $followers_ids = array();
        }
        $followers_ids[$from] = 1;

        update_user_meta($this->user_id, '_userpro_followers_ids', $followers_ids);

        $following_ids = get_user_meta($from, '_userpro_following_ids', true);
        if(empty($following_ids)){
            $following_ids = array();
        }
        $following_ids[$this->user_id] = 1;

        update_user_meta($from, '_userpro_following_ids', $following_ids);

        $array = array( 'to' => $this->user_id, 'from' => $from );
        do_action('userpro_sc_after_follow', $array);

    }

    /**
     * Unfollow user
     *
     * @since 4.9.33
     * @param $from
     */
    public function unfollow($from) {

        $followers = $this->getUserFollowers();


        if(empty($followers)){
            $followers_ids = array();
        }
        else{
            $followers_ids = $this->getUserFollowers();
        }

        if (!empty($followers_ids[$from]))
            unset($followers_ids[$from]);

        update_user_meta($this->user_id, '_userpro_followers_ids', $followers_ids);

        $following_ids = get_user_meta($from, '_userpro_following_ids', true);
        if(empty($following_ids)){
            $following_ids = array();
        }

        if (isset($following_ids[$this->user_id])) unset($following_ids[$this->user_id]);
        update_user_meta($from, '_userpro_following_ids', $following_ids);

        $array = array( 'to' => $this->user_id, 'from' => $from );
        do_action('userpro_sc_after_unfollow', $array);

    }
}
