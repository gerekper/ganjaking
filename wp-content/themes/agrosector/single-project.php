<?php
	if ( !post_password_required() ) {
		get_header();
		the_post();
		?>

		<?php
		$page_title_conditional = ((gt3_option('page_title_conditional') == '1' || gt3_option('page_title_conditional') == true)) ? 'yes' : 'no';
		$project_title_conditional = ((gt3_option('project_title_conditional') == '1' || gt3_option('project_title_conditional') == true)) ? 'yes' : 'no';
		if ($page_title_conditional == 'yes' && $project_title_conditional == 'no') {
            $page_title_conditional = 'no';
        }
        $id = gt3_get_queried_object_id();
        if (class_exists( 'RWMB_Loader' ) && $id !== 0) {
            $page_sub_title = rwmb_meta('mb_page_sub_title');
            $mb_page_title_conditional = rwmb_meta('mb_page_title_conditional');
            if ($mb_page_title_conditional == 'no') {
            	$page_title_conditional = 'no';
            }
        }


		$layout = gt3_option('project_single_sidebar_layout');
		$sidebar = gt3_option('project_single_sidebar_def');
		if (class_exists( 'RWMB_Loader' ) && gt3_get_queried_object_id() !== 0) {
			$mb_layout = rwmb_meta('mb_page_sidebar_layout');
			if (!empty($mb_layout) && $mb_layout != 'default') {
				$layout = $mb_layout;
				$sidebar = rwmb_meta('mb_page_sidebar_def');
			}
		}
		$column = 12;
		if ( ($layout == 'left' || $layout == 'right') && is_active_sidebar( $sidebar )  ) {
			$column = 9;
		}else{
			$sidebar = '';
		}
		$row_class = 'sidebar_'.esc_attr($layout);
		?>

		<div class="container container-<?php echo esc_attr($row_class); ?>">
			<div class="row <?php echo esc_attr($row_class); ?>">
				<div class="content-container span<?php echo (int)$column; ?>">
					<section id='main_content'>
						<?php
							if ($page_title_conditional == 'no') {
								echo "<h1 class='project_title_content'>";
									echo get_the_title();
								echo "</h1>";
							}

							the_content(esc_html__('Read more!', 'agrosector'));
							wp_link_pages(array('before' => '<div class="page-link">' . esc_html__('Pages', 'agrosector') . ': ', 'after' => '</div>'));
							if (gt3_option("page_comments") == "1") { ?>
								<div class="clear"></div>
								<?php comments_template(); ?>
							<?php } ?>
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
		<?php
		// prev next links
		$prev_post = get_previous_post();
		$next_post = get_next_post();
		if (($prev_post || $next_post)) { ?>
			<div class="single_prev_next_posts">
				<div class="container">
					<?php
					if (!empty($prev_post)) {
						previous_post_link('<div class="fleft">%link</div>', '<span class="gt3_post_navi" data-title="' . esc_attr($prev_post->post_title) . '">' . esc_html__('Prev', 'agrosector') . '</span>');
					}
					echo '<a href="'. esc_js("javascript:history.back()") .'" class="port_back2grid"><span class="port_back2grid_box1"></span><span class="port_back2grid_box2"></span><span class="port_back2grid_box3"></span><span class="port_back2grid_box4"></span></a>';
					if (!empty($next_post)) {
						next_post_link('<div class="fright">%link</div>', '<span class="gt3_post_navi" data-title="' . esc_attr($next_post->post_title) . '">' . esc_html__('Next', 'agrosector') . '</span>');
					}
					?>
				</div>
			</div>
		<?php }
		get_footer();
	} else {
		get_header();
		?>
		<div class="pp_block">
			<div class="container_vertical_wrapper">
				<div class="container a-center pp_container">
					<h1><?php echo esc_html__('Password Protected', 'agrosector'); ?></h1>
					<?php the_content(); ?>
				</div>
			</div>
		</div>
		<?php
		get_footer();
	} ?>
