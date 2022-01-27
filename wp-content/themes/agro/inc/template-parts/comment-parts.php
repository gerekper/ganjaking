<?php

if (is_admin()) {
    return false;
}


/*************************************************
## Post Comment Customization
*************************************************/


// Theme custom comment list
function agro_custom_commentlist($comment, $args, $depth)
{
    $GLOBALS['comment'] = $comment; ?>

	<li <?php comment_class('nt-comment-item'); ?> id="li-comment-<?php comment_ID() ?>">

		<div id="comment-<?php comment_ID(); ?>">

			<div class="nt-comment-left">

                <div class="nt-comment-avatar">
					<?php echo get_avatar($comment, $size='48'); ?>
				</div>

                <?php if ($comment->comment_approved == '0') : ?>
					<em><?php esc_html_e('Your comment is awaiting moderation.', 'agro') ?></em>
					<br />
				<?php endif; ?>

			</div>

			<div class="nt-comment-right">

				<div class="nt-comment-author"><?php echo get_comment_author_link(); ?></div>

				<div class="nt-comment-date">
					<a href="<?php echo htmlspecialchars(get_comment_link($comment->comment_ID)) ?>"><?php printf(esc_html__('%1$s at %2$s', 'agro'), get_comment_date(), get_comment_time()) ?></a>
					<?php edit_comment_link(esc_html__('(Edit)', 'agro'), '  ', '') ?>
				</div>

				<div class="nt-comment-content nt-theme-content nt-clearfix"><?php comment_text() ?></div>

				<div class="nt-comment-reply-content"><?php comment_reply_link(array_merge($args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?></div>

			</div>

		</div>
<?php
}



// Unset URL from comment form
function agro_move_comment_form_below($fields)
{
    $comment_field = $fields['comment'];
    unset($fields['comment']);
    $fields['comment'] = $comment_field;

    return $fields;
}
add_filter('comment_form_fields', 'agro_move_comment_form_below');



// Add placeholder for Name and Email
function agro_move_modify_comment_form_fields($fields)
{
    $commenter     = wp_get_current_commenter();
    $user          = wp_get_current_user();
    $user_identity = $user->exists() ? $user->display_name : '';
    $req           = get_option('require_name_email');
    $aria_req      = ($req ? " aria-required='true'" : '');
    $html_req      = ($req ? " required='required'" : '');
    $html5         = current_theme_supports('html5', 'comment-form') ? 'html5' : false;

    $fields['author'] = '<div class="row"><div class="col-sm-4">' . '<input class="nt-form-input" id="author" placeholder="'.esc_attr__('Your Name (No Keywords)', 'agro').'" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30"' . $aria_req . ' required />'.'</div>';
    $fields['email'] = '<div class="col-sm-4">' . '<input class="nt-form-input" id="email" placeholder="'.esc_attr__('your-real-email@example.com', 'agro').'" name="email" type="text" value="' . esc_attr($commenter['comment_author_email']) .'" size="30"' . $aria_req . ' required/>'  .'</div>';
    $fields['url'] = '<div class="col-sm-4">' . '<input class="nt-form-input" id="url" name="url" placeholder="'.esc_attr__('Add your website URL', 'agro').'" type="text" value="' . esc_attr($commenter['comment_author_url']) . '" size="30" required/> ' .'</div></div>';

    return $fields;
}
add_filter('comment_form_default_fields', 'agro_move_modify_comment_form_fields');



// add class comment form button
function agro_addclass_form_button($arg)
{

  // $arg contains all the comment form defaults
    // simply redefine one of the existing array keys to change the comment form
    // see http://codex.wordpress.org/Function_Reference/comment_form for a list
    // of array keys
    // add Foundation classes to the button class
    $arg['class_submit'] = 'nt-theme-button nt-comment-form-button custom-btn custom-btn--medium custom-btn--style-1';
    // return the modified array

    return $arg;
}
// run the comment form defaults through the newly defined filter
add_filter('comment_form_defaults', 'agro_addclass_form_button');

