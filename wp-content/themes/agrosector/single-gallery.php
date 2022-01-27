<?php
	if(!post_password_required()) {
		get_header();
		the_post();
		if(class_exists('\ElementorModal\Widgets\GT3_Elementor_Gallery')) {
			$gallery_type          = gt3_option('gallery_type');
			$gt3_theme_pagebuilder = gt3pb_get_gallery_ctp_gallery(get_the_ID());

			switch($gallery_type) {
				case 'grid':
					$gallery = new \ElementorModal\Widgets\GT3_Elementor_Grid();
					$settings      = array(
						'select_source' => 'gallery',
						'gallery'       => get_the_ID(),
						'grid_type'     => gt3_option('grid_grid_type'),
						'cols'          => gt3_option('grid_cols'),
						'grid_gap'      => gt3_option('grid_grid_gap'),
						'hover'         => gt3_option('grid_hover'),
						'lightbox'      => gt3_option('grid_lightbox'),
						'show_title'    => gt3_option('grid_show_title'),
						'post_per_load' => gt3_option('grid_post_per_load'),
						'show_view_all' => gt3_option('grid_show_view_all'),
						'load_items'    => gt3_option('grid_load_items'),
						'button_type'   => gt3_option('grid_button_type'),
						'button_border' => gt3_option('grid_button_border'),
						'button_title'  => gt3_option('grid_button_title'),

						'_background_background' => '',
						'_border_border'         => '',
						'from_elementor'         => false,
					);

					$gallery->set_settings($settings);
					?>
					<div class="elementor">
						<div class="elementor-inner">
							<div class="elementor-section-wrap">
								<?php
									$gallery->print_element();
								?>
							</div>
						</div>
					</div>
					<?php
					break;
				case 'packery':
					$gallery = new \ElementorModal\Widgets\GT3_Elementor_Packery();
					$settings      = array(
						'select_source' => 'gallery',
						'gallery'       => get_the_ID(),
						'type'          => gt3_option('packery_type'),
						'hover'         => gt3_option('packery_hover'),
						'grid_gap'      => gt3_option('packery_grid_gap'),
						'lightbox'      => gt3_option('packery_lightbox'),
						'show_title'    => gt3_option('packery_show_title'),
						'post_per_load' => gt3_option('packery_post_per_load'),
						'show_view_all' => gt3_option('packery_show_view_all'),
						'load_items'    => gt3_option('packery_load_items'),
						'button_type'   => gt3_option('packery_button_type'),
						'button_border' => gt3_option('packery_button_border'),
						'button_title'  => gt3_option('packery_button_title'),

						'_background_background' => '',
						'_border_border'         => '',
						'from_elementor'         => false,
					);

					$gallery->set_settings($settings);
					?>
					<div class="elementor">
						<div class="elementor-inner">
							<div class="elementor-section-wrap">
								<?php
									$gallery->print_element();
								?>
							</div>
						</div>
					</div>
					<?php
					break;
				case 'fs_slider':
					$gallery = new \ElementorModal\Widgets\GT3_Elementor_FSSlider();
					$settings      = array(
						'select_source'   => 'gallery',
						'gallery'         => get_the_ID(),
						'controls'        => gt3_option('fs_controls'),
						'autoplay'        => gt3_option('fs_autoplay'),
						'thumbs'          => gt3_option('fs_thumbs'),
						'interval'        => gt3_option('fs_interval'),
						'transition_time' => gt3_option('fs_transition_time'),
						'panel_color'     => gt3_option('fs_panel_color'),
						'text_overflow'   => gt3_option('fs_text_overflow'),
						'anim_style'      => gt3_option('fs_anim_style'),
						'fit_style'       => gt3_option('fs_fit_style'),
						'module_height'   => gt3_option('fs_module_height'),

						'_background_background' => '',
						'_border_border'         => '',
						'from_elementor'         => false,
					);

					$gallery->set_settings($settings);
					?>
					<div class="elementor">
						<div class="elementor-inner">
							<div class="elementor-section-wrap">
								<?php
									$gallery->print_element();
								?>
							</div>
						</div>
					</div>
					<?php
					break;
				case 'shift_slider':
					$gallery = new \ElementorModal\Widgets\GT3_Elementor_Shift();
					$settings      = array(
						'select_source'   => 'gallery',
						'gallery'         => get_the_ID(),
						'controls'        => gt3_option('shift_controls'),
						'infinity_scroll' => gt3_option('shift_infinity_scroll'),
						'autoplay'        => gt3_option('shift_autoplay'),
						'thumbs'          => gt3_option('shift_thumbs'),
						'interval'        => gt3_option('shift_interval'),
						'transition_time' => gt3_option('shift_transition_time'),
						'descr_type'      => gt3_option('shift_descr_type'),
						'expandeble'      => gt3_option('shift_expandeble'),
						'hover_effect'    => gt3_option('shift_hover_effect'),
						'module_height'   => gt3_option('shift_module_height'),

						'_background_background' => '',
						'_border_border'         => '',
						'from_elementor'         => false,
					);

					$gallery->set_settings($settings);
					?>
					<div class="elementor">
						<div class="elementor-inner">
							<div class="elementor-section-wrap">
								<?php
									$gallery->print_element();
								?>
							</div>
						</div>
					</div>
					<?php
					break;
				case 'masonry':
					$gallery = new \ElementorModal\Widgets\GT3_Elementor_Masonry();
					$settings      = array(
						'select_source' => 'gallery',
						'gallery'       => get_the_ID(),
						'cols'          => gt3_option('masonry_cols'),
						'grid_gap'      => gt3_option('masonry_grid_gap'),
						'hover'         => gt3_option('masonry_hover'),
						'lightbox'      => gt3_option('masonry_lightbox'),
						'show_title'    => gt3_option('masonry_show_title'),
						'post_per_load' => gt3_option('masonry_post_per_load'),
						'show_view_all' => gt3_option('masonry_show_view_all'),
						'load_items'    => gt3_option('masonry_load_items'),
						'button_type'   => gt3_option('masonry_button_type'),
						'button_border' => gt3_option('masonry_button_border'),
						'button_title'  => gt3_option('masonry_button_title'),

						'_background_background' => '',
						'_border_border'         => '',
						'from_elementor'         => false,
					);

					$gallery->set_settings($settings);
					?>
					<div class="elementor">
						<div class="elementor-inner">
							<div class="elementor-section-wrap">
								<?php
									$gallery->print_element();
								?>
							</div>
						</div>
					</div>
					<?php
					break;
				case 'kenburn':
					$gallery = new \ElementorModal\Widgets\GT3_Elementor_Kenburns();
					$settings      = array(
						'select_source'   => 'gallery',
						'gallery'         => get_the_ID(),
						'interval'        => gt3_option('kenburn_interval'),
						'transition_time' => gt3_option('kenburn_transition_time'),
						'module_height'   => gt3_option('kenburn_module_height'),
						'overlay_state'   => gt3_option('kenburn_overlay_state'),
						'overlay_bg'      => gt3_option('kenburn_overlay_bg'),

						'_background_background' => '',
						'_border_border'         => '',
						'from_elementor'         => false,
					);

					$gallery->set_settings($settings);
					?>
					<div class="elementor">
						<div class="elementor-inner">
							<div class="elementor-section-wrap">
								<?php
									$gallery->print_element();
								?>
							</div>
						</div>
					</div>
					<?php
					break;
				case 'ribbon':
					$gallery = new \ElementorModal\Widgets\GT3_Elementor_Ribbon();
					$settings      = array(
						'select_source'   => 'gallery',
						'gallery'         => get_the_ID(),
						'show_title'      => gt3_option('ribbon_show_title'),
						'show_descr'      => gt3_option('ribbon_descr'),
						'items_padding'   => gt3_option('ribbon_items_padding'),
						'controls'        => gt3_option('ribbon_controls'),
						'autoplay'        => gt3_option('ribbon_autoplay'),
						'interval'        => gt3_option('ribbon_interval'),
						'transition_time' => gt3_option('ribbon_transition_time'),
						'module_height'   => gt3_option('ribbon_module_height'),

						'_background_background' => '',
						'_border_border'         => '',
						'from_elementor'         => false,
					);

					$gallery->set_settings($settings);
					?>
					<div class="elementor">
						<div class="elementor-inner">
							<div class="elementor-section-wrap">
								<?php
									$gallery->print_element();
								?>
							</div>
						</div>
					</div>
					<?php
					break;
				case 'flow':
					$gallery = new \ElementorModal\Widgets\GT3_Elementor_Flow();
					$settings      = array(
						'select_source'   => 'gallery',
						'gallery'         => get_the_ID(),
						'img_width'       => gt3_option('flow_img_width'),
						'img_height'      => gt3_option('flow_img_height'),
						'lightbox'        => gt3_option('flow_lightbox'),
						'title_state'     => gt3_option('flow_title_state'),
						'autoplay'        => gt3_option('flow_autoplay'),
						'interval'        => gt3_option('flow_interval'),
						'transition_time' => gt3_option('flow_transition_time'),
						'module_height'   => gt3_option('flow_module_height'),

						'_background_background' => '',
						'_border_border'         => '',
						'from_elementor'         => false,
					);

					$gallery->set_settings($settings);
					?>
					<div class="elementor">
						<div class="elementor-inner">
							<div class="elementor-section-wrap">
								<?php
									$gallery->print_element();
								?>
							</div>
						</div>
					</div>
					<?php
					break;
			}
		}

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
	}