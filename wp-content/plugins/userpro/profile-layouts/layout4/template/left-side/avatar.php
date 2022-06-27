<?php

defined( 'ABSPATH' ) || exit; ?>

<div class="up-professional-layout__left_avatar">
    <?php
    global $up_user;
        echo $up_user->getProfileAvatar();
    ?>
</div>