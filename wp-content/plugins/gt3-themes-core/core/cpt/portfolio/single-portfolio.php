<?php
	if ( !post_password_required() ) {
		get_header();
		the_post();
		?>

		<?php
		$layout = gt3_option('portfolio_single_sidebar_layout');
		$sidebar = gt3_option('portfolio_single_sidebar_def');
		if (class_exists( 'RWMB_Loader' ) && get_queried_object_id() !== 0) {
			$mb_layout = rwmb_meta('mb_page_sidebar_layout');
			if (!empty($mb_layout) && $mb_layout != 'default') {
				$layout = $mb_layout;
				$sidebar = rwmb_meta('mb_page_sidebar_def');
			}
		}
		$column = 12;
		if ( $layout == 'left' || $layout == 'right' ) {
			$column = apply_filters( 'gt3_column_width', 9 );
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
							the_content(esc_html__('Read more!', 'gt3_themes_core'));
							wp_link_pages(array('before' => '<div class="page-link">' . esc_html__('Pages', 'gt3_themes_core') . ': ', 'after' => '</div>'));
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
		<!-- prev next links -->
		<div class="single_prev_next_posts">
			<div class="container">
				<?php
				$prev_post = get_previous_post();
				$next_post = get_next_post();
				if (!empty($prev_post)) {
					previous_post_link('<div class="fleft">%link</div>', '<span class="big_arrow_prev"><i class="fa fa-angle-left"></i></span><span class="gt3_mobile_visible">' . esc_html__('Prev', 'gt3_themes_core') . '</span><span class="gt3_mobile_hidden">' . esc_html__('Previous Project', 'gt3_themes_core') . '</span>');
				}
				echo '<a href="'. esc_js("javascript:history.back()") .'" class="port_back2grid"><span class="port_back2grid_box1"></span><span class="port_back2grid_box2"></span><span class="port_back2grid_box3"></span><span class="port_back2grid_box4"></span></a>';
				if (!empty($next_post)) {
					next_post_link('<div class="fright">%link</div>', '<span class="gt3_mobile_visible">' . esc_html__('Next', 'gt3_themes_core') . '</span><span class="gt3_mobile_hidden">' . esc_html__('Next Project', 'gt3_themes_core') . '</span>' . '<span class="big_arrow_next"><i class="fa fa-angle-right"></i></span>');
				}
				?>
			</div>
		</div>
		<!-- //prev next links -->
		<?php

		get_footer();

	} else {
		get_header();
		?>
		<div class="pp_block">
			<div class="container_vertical_wrapper">
				<div class="container a-center pp_container">
					<h1><?php echo esc_html__('Password Protected', 'gt3_themes_core'); ?></h1>
					<?php the_content(); ?>
				</div>
			</div>
		</div>
		<?php
		get_footer();
	} ?>