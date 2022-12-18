<?php

global $porto_settings;

defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) : ?>
	<p class="no-comments"><?php esc_html_e( 'This post is password protected. Enter the password to view comments.', 'porto' ); ?></p>
	<?php
	return;
endif;

if ( have_comments() ) :
	?>
	<div class="post-block post-comments clearfix" id="comments">
		<?php if ( isset( $porto_settings['post-title-style'] ) && 'without-icon' == $porto_settings['post-title-style'] ) : ?>
			<h4>
			<?php
				printf(
					_nx( 'Comment (1)', 'Comments (%1$s)', get_comments_number(), 'comments title', 'porto' ),
					number_format_i18n( get_comments_number() ),
					'<span>' . get_the_title() . '</span>'
				);
			?>
			</h4>
		<?php else : ?>
			<h3><i class="far fa-comments"></i>
			<?php
				printf(
					_nx( 'Comment (1)', 'Comments (%1$s)', get_comments_number(), 'comments title', 'porto' ),
					number_format_i18n( get_comments_number() ),
					'<span>' . get_the_title() . '</span>'
				);
			?>
			</h3>
		<?php endif; ?>

		<ul class="comments">
			<?php
				// Comments list
				wp_list_comments(
					array(
						'short_ping'  => true,
						'avatar_size' => 80,
						'callback'    => 'porto_comment',
					)
				);
			?>
		</ul>

		<?php
		// Are there comments to navigate through?
		if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
			?>
			<div class="clearfix">
				<div class="pagination" role="navigation">
					<?php paginate_comments_links(); ?>
				</div>
			</div>
		<?php endif; // Check for comment navigation ?>

		<?php if ( ! comments_open() && get_comments_number() ) : ?>
			<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'porto' ); ?></p>
		<?php endif; ?>
	</div>
<?php endif; // have_comments() ?>

<?php comment_form(); ?>
