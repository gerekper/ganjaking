<?php do_action('userpro_follow_bar_before', $user_id, $template) ?>
<a href="<?php echo $userpro->permalink($user_id, 'following', 'userpro_sc_pages'); ?>" class="up-social-tab up-following"><?php echo $userpro_social->following_count( $user_id ); ?></a>
<a href="<?php echo $userpro->permalink($user_id, 'followers', 'userpro_sc_pages'); ?>" class="up-social-tab up-followers"><?php echo $userpro_social->followers_count( $user_id ); ?></a>
<?php do_action('userpro_follow_bar_after') ?>