<?php
defined( 'ABSPATH' ) || exit;
global $up_user;
global $userpro;
?>

    <div class="up-follow">
        <div class="up-followers">
            <h4><?php _e('Followers', 'userpro') ?></h4>
           <a href="<?php echo $userpro->permalink($up_user->getUserId(), 'followers', 'userpro_sc_pages'); ?>"><span><?php echo $up_user->user_social->getUserFollowersCount('followers'); ?></span></a>
        </div>
        <div class="up-following">
            <h4><?php _e('Following', 'userpro') ?></h4>

            <a href="<?php echo $userpro->permalink($up_user->getUserId(), 'following', 'userpro_sc_pages'); ?>"><span><?php echo $up_user->user_social->getUserFollowersCount('following'); ?></span></a>
        </div>
    </div>