<?php
defined( 'ABSPATH' ) || exit;

global $up_user;
?>

<div class="user-profile-name"><p><?php echo $up_user->getUserDisplayName() ?></p>

    <?php if(userpro_get_option('show_badges_profile')=='1'): ?>
    <div class="user-profile-badges"><?php  echo $up_user->getUserBadges(); ?></div>
    <?php endif; ?>

</div>
