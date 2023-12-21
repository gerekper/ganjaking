<?php
namespace Happy_Addons_Pro\Traits;

use Elementor\Icons_Manager;

defined('ABSPATH') || die();

trait Smart_Post_List_Markup {

	public static function render_spl_markup( $settings, $posts, $class_array,$list_column,$per_page, $loop = 1 ) {

		?>
		<?php if('yes' === $settings['make_featured_post'] && $loop===1): ?>
			<?php foreach ( $posts as $post ): ?>
			<!-- featured post -->
			<div class="<?php echo $class_array['featured']; ?>">
				<div class="<?php echo $class_array['featured_inner']; ?>">
					<?php if ( has_post_thumbnail($post->ID) ): ?>
						<a class="ha-spl-featured-thumb">
							<?php
							echo get_the_post_thumbnail( $post->ID, $settings['featured_image_size'] );
							?>
						</a>
					<?php endif; ?>
					<div class="ha-spl-content">
						<?php if ( 'yes' === $settings['show_badge'] && ha_pro_the_first_taxonomy( $post->ID, $settings['taxonomy_badge'] ) ): ?>
							<div class="ha-spl-badge">
								<?php echo ha_pro_the_first_taxonomy( $post->ID, $settings['taxonomy_badge'], ['class' => 'ha-spl-badge-inner'] ); ?>
							</div>
						<?php endif; ?>
						<?php
							if ( 'yes' === $settings['featured_post_title'] && $post->post_title ) {
								printf( '<%1$s %2$s><a href="%3$s">%4$s</a></%1$s>',
									tag_escape( $settings['featured_post_title_tag'] ),
									'class="ha-spl-title"',
									esc_url( get_the_permalink( $post->ID ) ),
									esc_html( $post->post_title )
								);
							}
						?>
						<?php if ( !empty( $settings['featured_meta_active'] ) ): ?>
							<div class="ha-spl-meta">
								<ul>
									<?php if ( in_array( 'author', $settings['featured_meta_active'] ) ): ?>
										<li class="ha-spl-meta-author">
											<a href="<?php echo esc_url(get_author_posts_url($post->post_author));?>">
												<?php if ( $settings['featured_post_author_icon'] ):
														echo '<span class="ha-spl-meta-icon">';
															Icons_Manager::render_icon( $settings['featured_post_author_icon'], [ 'aria-hidden' => 'true' ] );
														echo '</span>';
													endif;
													echo '<span class="ha-spl-meta-text">';
														echo esc_html( get_the_author_meta( 'display_name', $post->post_author ) );
													echo '</span>';
												?>
											</a>
										</li>
									<?php endif;?>
									<?php if ( in_array( 'date', $settings['featured_meta_active'] ) ): ?>
										<li class="ha-spl-meta-date">
											<?php
												$year = get_the_date('Y', $post->ID );
												$month = get_the_time('m', $post->ID );
												$day = get_the_time('d', $post->ID );
												$url = get_day_link($year, $month, $day);
											?>
											<a href="<?php echo esc_url($url);?>">
												<?php if ( $settings['featured_post_date_icon'] ):
															echo '<span class="ha-spl-meta-icon">';
																Icons_Manager::render_icon( $settings['featured_post_date_icon'], [ 'aria-hidden' => 'true' ] );
															echo '</span>';
														endif;
														echo '<span class="ha-spl-meta-text">';
															echo get_the_date( get_option( 'date_format' ), $post->ID );
														echo '</span>';
												?>
											</a>
										</li>
									<?php endif;?>
									<?php if ( in_array( 'comments', $settings['featured_meta_active'] ) ): ?>
										<li class="ha-spl-meta-comment">
											<span>
												<?php if ( $settings['featured_post_comment_icon'] ):
														echo '<span class="ha-spl-meta-icon">';
															Icons_Manager::render_icon( $settings['featured_post_comment_icon'], [ 'aria-hidden' => 'true' ] );
														echo '</span>';
													endif;
													echo '<span class="ha-spl-meta-text">';
														echo get_comments_number($post->ID);
													echo '</span>';
												?>
											</span>
										</li>
									<?php endif;?>
								</ul>
							</div>
						<?php endif;?>
						<?php if ( !empty($settings['featured_excerpt_length']) ): ?>
							<div class="ha-spl-desc">
								<?php printf( '<p>%1$s</p>', ha_pro_get_excerpt( $post->ID, $settings['featured_excerpt_length'] ) ); ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<!-- featured post -->

			<?php break; endforeach;?>
		<?php endif;?>


		<!-- blog list -->
		<?php
			if( 'yes' === $settings['make_featured_post'] && !empty($list_column) ){
				echo '<div class="ha-spl-column ha-spl-list-wrap '.esc_attr($list_column).'">';
			}
		?>
			<?php if( !empty($list_column) ): ?>
			<?php foreach ( $posts as $post ): ?>
				<?php if( 'yes' === $settings['make_featured_post'] && $loop === 1){ $loop++; continue;} ?>
				<div class="ha-spl-list">
					<?php if ( 'yes' === $settings['list_post_image'] && has_post_thumbnail($post->ID) ): ?>
						<div class="ha-spl-list-thumb">
							<a href="<?php echo esc_url( get_the_permalink( $post->ID ) );?>">
							<?php
								echo get_the_post_thumbnail( $post->ID, $settings['list_post_image_size'] );
							?>
							</a>
						</div>
					<?php endif; ?>
					<div class="ha-spl-list-content">
						<?php
							if (  $post->post_title ) {
								printf( '<%1$s %2$s><a href="%3$s">%4$s</a></%1$s>',
									tag_escape( $settings['list_post_title_tag'] ),
									'class="ha-spl-list-title"',
									esc_url( get_the_permalink( $post->ID ) ),
									esc_html( $post->post_title )
								);
							}
						?>

						<?php if ( !empty( $settings['list_meta_active'] ) ): ?>
							<div class="ha-spl-meta">
								<ul>
									<?php if ( in_array( 'author', $settings['list_meta_active'] ) ): ?>
										<li class="ha-spl-meta-author">
											<a href="<?php echo esc_url(get_author_posts_url($post->post_author));?>">
												<?php if ( $settings['list_post_author_icon'] ):
													echo '<span class="ha-spl-meta-icon">';
														Icons_Manager::render_icon( $settings['list_post_author_icon'], [ 'aria-hidden' => 'true' ] );
													echo '</span>';
												endif;
												echo '<span class="ha-spl-meta-text">';
													echo esc_html( get_the_author_meta( 'display_name', $post->post_author ) );
												echo '</span>';
												?>
											</a>
										</li>
									<?php endif;?>
									<?php if ( in_array( 'date', $settings['list_meta_active'] ) ): ?>
										<li class="ha-spl-meta-date">
											<?php
												$year = get_the_date('Y', $post->ID );
												$month = get_the_time('m', $post->ID );
												$day = get_the_time('d', $post->ID );
												$url = get_day_link($year, $month, $day);
											?>
											<a href="<?php echo esc_url($url);?>">
												<?php if ( $settings['list_post_date_icon'] ):
														echo '<span class="ha-spl-meta-icon">';
															Icons_Manager::render_icon( $settings['list_post_date_icon'], [ 'aria-hidden' => 'true' ] );
														echo '</span>';
													endif;
													echo '<span class="ha-spl-meta-text">';
														echo get_the_date( get_option( 'date_format' ), $post->ID );
													echo '</span>';
												?>
											</a>
										</li>
									<?php endif;?>
									<?php if ( in_array( 'comments', $settings['list_meta_active'] ) ): ?>
										<li class="ha-spl-meta-comment">
											<span>
												<?php if ( $settings['list_post_comment_icon'] ):
														echo '<span class="ha-spl-meta-icon">';
															Icons_Manager::render_icon( $settings['list_post_comment_icon'], [ 'aria-hidden' => 'true' ] );
														echo '</span>';
													endif;
													echo '<span class="ha-spl-meta-text">';
														echo get_comments_number($post->ID);
													echo '</span>';
												?>
											</span>
										</li>
									<?php endif;?>
								</ul>
							</div>
						<?php endif;?>
					</div>
				</div>
				<?php if($loop == $per_page){break;}?>
			<?php $loop++; endforeach;?>
			<?php endif;?>

		<?php if( 'yes' === $settings['make_featured_post'] && !empty($list_column) ){ echo '</div>';} ?>
	<?php
	}
}
