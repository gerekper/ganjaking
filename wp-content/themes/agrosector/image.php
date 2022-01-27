<?php get_header();
the_post();

$layout = gt3_option('page_sidebar_layout');
$sidebar = gt3_option('page_sidebar_def');
$column = 12;

if ( ($layout == 'left' || $layout == 'right') && is_active_sidebar( $sidebar )  ) {
	$column = 9;
}else{
	$sidebar = '';
}
$row_class = ' sidebar_'.$layout;
?>

	<div class="container">
        <div class="row<?php echo esc_attr($row_class); ?>">
            <div class="content-container span<?php echo (int)$column; ?>">
                <section id='main_content'>
					<?php
						$post = get_post();
						$attachment_size = array(1170, 725);
						$next_attachment_url = wp_get_attachment_url();

						$attachment_ids = get_posts(array(
							'post_parent' => $post->post_parent,
							'fields' => 'ids',
							'numberposts' => -1,
							'post_status' => 'inherit',
							'post_type' => 'attachment',
							'post_mime_type' => 'image',
							'order' => 'ASC',
							'orderby' => 'menu_order ID'
						));

						if (count($attachment_ids) > 1) {
							foreach ($attachment_ids as $attachment_id) {
								if ($attachment_id == $post->ID) {
									$next_id = current($attachment_ids);
									break;
								}
							}

							if ($next_id) {
								$next_attachment_url = get_attachment_link($next_id);
							} else {
								$next_attachment_url = get_attachment_link(array_shift($attachment_ids));
							}
						}
					?>
					<div class="blog_post_preview">
						<div class="blog_post_media">
							<?php
								printf('%1$s',
									wp_get_attachment_image($post->ID, $attachment_size)
								);
							?>
						</div>
						<div class="blog_content">
							<div class="listing_meta">
								<?php
								$published_text = '<span class="attachment-meta">' . esc_html__('Published on', 'agrosector') . ' <time class="entry-date" datetime="%1$s">%2$s</time></span><span>' . esc_html__('in', 'agrosector') . ' <a href="%3$s" rel="gallery">%5$s</a></span>';
								$post_title = get_the_title($post->post_parent);
								if (empty($post_title) || 0 == $post->post_parent) {
									$published_text = '<span class="attachment-meta"><time class="entry-date" datetime="%1$s">%2$s</time></span>';
								}

								printf($published_text,
									esc_attr(get_the_date('c')),
									esc_html(get_the_date()),
									esc_url(get_permalink($post->post_parent)),
									esc_attr(strip_tags($post_title)),
									$post_title
								);

								$metadata = wp_get_attachment_metadata();
								printf('<span class="attachment-meta full-size-link"><a href="%1$s" title="%2$s">%3$s (%4$s &times; %5$s)</a></span>',
									esc_url(wp_get_attachment_url()),
									esc_html__('Link to full-size image', 'agrosector'),
									esc_html__('Full resolution', 'agrosector'),
									$metadata['width'],
									$metadata['height']
								);

								edit_post_link(esc_html__('Edit', 'agrosector'), '<span class="edit-link">', '</span>');
								?>
							</div>
							<?php
								$post_title = get_the_title();
								if (strlen($post_title) > 0) {
									echo '<h3 class="blogpost_title"><i class="fa fa-camera"></i>' . esc_html($post_title) . '</h3>';
								}
							?>
							<?php if (!empty($post->post_content)) { ?>
								<?php the_content(); ?>
								<?php wp_link_pages(array(
			                        'before' => '<div class="page-link"><span>' . esc_html__('Pages', 'agrosector') . '</span>: ', 
			                        'link_before'      => '<span class="page-number">',
			                        'link_after'       => '</span>',
			                        'pagelink'         => '%',
			                        'after' => '</div>')); ?>
							<?php } ?>
							<a class="learn_more" href="<?php echo esc_js("javascript:history.back()");?>"><?php echo esc_html__('Back', 'agrosector'); ?><span></span></a>
						</div>
					</div>
				</section>
			</div>
			<?php
			if ($layout == 'left' || $layout == 'right') {
				echo '<div class="sidebar-container span'.(12 - (int)$column).'">';
				if (is_active_sidebar( $sidebar )) {
					echo "<aside class='sidebar'>";
					dynamic_sidebar( $sidebar );
					echo "</aside>";
				}
				echo "</div>";
			}
			?>
		</div>

	</div>

<?php get_footer(); ?>