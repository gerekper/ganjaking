<?php
if (post_password_required()) {
    ?>
    <p class="nocomments"><?php esc_html_e('This post is password protected. Enter the password to view comments.', 'agrosector'); ?></p>
    <?php return;
}
?>

<div id="comments"><?php
#Required for nested reply function that moves reply inline with JS
if (is_singular()) wp_enqueue_script('comment-reply');
$comments_num = get_comments_number(get_the_ID());

if ($comments_num == 1) {
  $comments_text = esc_html__('Comment', 'agrosector');
} else {
  $comments_text = esc_html__('Comments', 'agrosector');
}
if (have_comments()) : ?>
    <h2><?php echo comments_number('0', '1', '%'); ?> <?php echo esc_html($comments_text); ?></h2>
    <ol class="commentlist">
    <?php
        if (gt3_option("post_pingbacks") == "1") {
            wp_list_comments('type=all&callback=gt3_theme_comment');
        } else {
            wp_list_comments('type=comment&callback=gt3_theme_comment');
        }
    ?>
    </ol>
    <div class="gt3_comments_pagination navigation"><?php
        paginate_comments_links(array(
            'prev_text' => '«',
            'next_text' => '»'
        )); ?></div>
    <?php if ('open' == $post->comment_status) : ?>
    <?php else : // comments are closed ?>
    <?php endif; ?>
<?php endif; ?>
<?php if ('open' == $post->comment_status) :

    $comment_form = array(
        'fields'                => apply_filters('comment_form_default_fields', array(
            'author'=> '<div class="span6"><label class="label-name label">' . esc_html__('Name*', 'agrosector') . '</label><input type="text" title="' . esc_attr__('Your Name*', 'agrosector') . '" id="author" name="author" class="form_field"></div>',
            'email' => '<div class="span6"><label class="label-email label">' . esc_html__('Email*', 'agrosector') . '</label><input type="text" title="' . esc_attr__('Email*', 'agrosector') . '" id="email" name="email" class="form_field"></div>',
            'url'   => ''
        )),
        'class_form'            => 'comment-form gt3_form',
        'comment_field'         => '<div class="span12"><label class="label-message label">' . esc_html__('Comment', 'agrosector') . '</label><textarea name="comment" cols="45" rows="5" id="comment-message" class="form_field"></textarea></div>',
        'comment_form_before'   => '',
        'comment_form_after'    => '',
        'must_log_in'           => esc_html__('You must be logged in to post a comment.', 'agrosector'),
        'title_reply_before'    => '<h2 id="reply-title" class="comment-reply-title">',
        'title_reply_after'     => '</h2>',
        'title_reply'           => esc_html__('Leave a Reply', 'agrosector'),
        'label_submit'          => esc_html__('Post a comment', 'agrosector'),
        'logged_in_as'          => '<p class="logged-in-as">' . esc_html__('Logged in as', 'agrosector') . ' <a href="' . esc_url(admin_url('profile.php')) . '">' . $user_identity . '</a>. <a href="' . esc_url(wp_logout_url(apply_filters('the_permalink', get_permalink()))) . '">' . esc_html__('Log out?', 'agrosector') . '</a></p>',
        'submit_button'         => '<button name="%1$s" type="submit" id="%2$s" class="%3$s">%4$s</button>',
    );

    add_filter('comment_form_fields', 'gt3_reorder_comment_fields');

    if (!function_exists('gt3_reorder_comment_fields')) {
        function gt3_reorder_comment_fields($fields ) {
            $new_fields = array();

            $myorder = array('author', 'email', 'url', 'comment');

            foreach( $myorder as $key ){
                $new_fields[ $key ] = $fields[ $key ];
                unset( $fields[ $key ] );
            }

            if( $fields ) {
                foreach( $fields as $key => $val ) {
                    $new_fields[ $key ] = $val;
                }
            }

            return $new_fields;
        }
    }


    comment_form($comment_form, $post->ID);

else : // Comments are closed ?>
<?php endif; ?></div>