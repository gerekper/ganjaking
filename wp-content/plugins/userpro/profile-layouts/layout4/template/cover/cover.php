<?php

defined( 'ABSPATH' ) || exit;
global $up_user; ?>

<div class="up-professional-layout__cover">
    <img src="<?= $up_user->getProfileCover() ?>" alt="profile_cover">

    <?php if (get_current_user_id()): ?>
        <div data-up-from-id="<?php echo get_current_user_id() ?>" data-up-user-id="<?php echo up_get_profile_user_id(); ?>"
             class="up-professional-layout__cover_buttons">

            <?php if (!is_current_user_profile($up_user->getUserId())):
                /**
                 * If social features enabled show follow button.
                 */
                if (userpro_get_option('modstate_social') === '1') {
                    echo $up_user->user_social->getFollowActionHtml(get_current_user_id());
                }
                /**
                 * If Connection enabled show Connect button.
                 */
                if (userpro_get_option('enable_connect') === 'y') {
                    echo $up_user->user_social->getConnectionsHtml(get_current_user_id());
                } ?>

            <?php else: ?>
                <a class="up-professional-btn up-professional-btn--small" href="<?php echo $up_user->getEditUrl() ?>"><span>
                    <i class="up-far up-fa-edit"></i><p><?php _e('Edit Profile', 'userpro') ?></p></span></a>
            <?php endif; ?>

            <?php do_action('up_after_professional_layout_buttons') ?>
        </div>
        <?php do_action('up_after_professional_layout_buttons_block') ?>

    <?php endif; ?>
</div>
<div class="up-professional-layout__body">