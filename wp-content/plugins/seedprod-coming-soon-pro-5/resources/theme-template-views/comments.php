<div id="comments" class="comments-area">

<?php
if ( have_comments() ) :
	?>
<h2 class="comments-title">
	Comments
</h2>

<ol class="comment-list">
	<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'avatar_size' => 48,
				)
			);
	?>
</ol><!-- .comment-list -->

	<?php
		// Are there comments to navigate through?
	if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
		?>
<nav class="navigation comment-navigation" role="navigation">
	<h1 class="screen-reader-text section-heading"><?php esc_html_e( 'Comment navigation', 'seedprod-pro' ); ?></h1>
	<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'seedprod-pro' ) ); ?></div>
	<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'seedprod-pro' ) ); ?></div>
</nav><!-- .comment-navigation -->
<?php endif; // Check for comment navigation ?>

	<?php if ( ! comments_open() && get_comments_number() ) : ?>
<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'seedprod-pro' ); ?></p>
	<?php endif; ?>

<?php endif; // end have_comments ?>

<?php
$commenter = wp_get_current_commenter();
$req       = get_option( 'require_name_email' );
$aria_req  = ( $req ? " aria-required='true'" : '' );
$fields    = array(
	'author' => '<p class="comment-form-author"><label for="author">' . __( 'Your Real Name' ) . '</label> ' .
		'<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . ' /></p>',
	'email'  => '<p class="comment-form-email"><label for="email">' . __( 'You Real Email' ) . '</label> ' .
		'<input id="email" name="email" type="text" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30"' . $aria_req . ' /></p>',
);

$comments_args = array(
	'title_reply'          => 'Add A Comment',
	'fields'               => $fields,
	'label_submit'         => 'Add Your Comment',
	'comment_notes_before' => '<p class="commentpolicy">We\'re glad you have chosen to leave a comment. Please keep in mind that all comments are moderated according to our <a href="/privacy/" title="Privacy Policy">privacy policy</a>, and all links are nofollow. Do NOT use keywords in the name field. Let\'s have a personal and meaningful conversation.</p>',
);
comment_form( $comments_args );
?>

</div><!-- #comments -->
