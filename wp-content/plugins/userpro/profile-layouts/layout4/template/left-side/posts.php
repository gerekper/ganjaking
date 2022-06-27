<?php
defined('ABSPATH') || exit;
global $up_user;
?>
<?php if (is_current_user_profile($up_user->getUserId())): ?>
    <div class="up-posts">
        <div class="title"><?php _e('My Posts', 'userpro') ?></div>
        <?php

        $user_posts = $up_user->user_posts->getPosts();

        if ($user_posts->have_posts()): ?>
            <div class="up-posts-container">
                <?php
                while ($user_posts->have_posts()):
                    $user_posts->the_post(); ?>
                    <article>
                        <a href="<?= get_permalink() ?>"><h2><?php the_title(); ?></h2></a>
                        <i class="up-status up-status--<?= get_post_status() ?>"></i>
                    </article>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
        <?php echo $up_user->user_posts->pagination($user_posts->max_num_pages); ?>
    </div>
<?php endif; ?>