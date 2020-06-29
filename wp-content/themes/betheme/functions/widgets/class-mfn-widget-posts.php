<?php
/**
 * Widget Muffin Recent Posts
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

if (! class_exists('Mfn_Widget_Posts')) {
	class Mfn_Widget_Posts extends WP_Widget
	{

		/**
		 * Constructor
		 */

		public function __construct()
		{
			$widget_ops = array(
				'classname' => 'widget_mfn_recent_posts',
				'description' => __('The most recent posts on your site.', 'mfn-opts')
			);

			parent::__construct('widget_mfn_recent_posts', __('Muffin Recent Posts', 'mfn-opts'), $widget_ops);

			$this->alt_option_name = 'widget_mfn_recent_posts';
		}

		/**
		 * Outputs the HTML for this widget.
		 */

		public function widget($args, $instance)
		{
			if (! isset($args['widget_id'])) {
				$args['widget_id'] = null;
			}
			extract($args, EXTR_SKIP);

			echo wp_kses_post($before_widget);

			$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
			if ($title) {
				echo wp_kses($before_title, array('h3'=>array(),'h4'=>array()));
					echo wp_kses($title, mfn_allowed_html());
				echo wp_kses($after_title, array('h3'=>array(),'h4'=>array()));
			}

			$args = array(
				'posts_per_page' => $instance['count'] ? intval($instance['count']) : 0,
				'no_found_rows' => true,
				'post_status' => 'publish',
				'ignore_sticky_posts'	=> true
			);

			if ($instance['category']) {
				$args['category_name'] = $instance['category'];
			}

			if (! isset($instance['style'])) {
				$instance['style'] = '';
			}

			$query_recent_posts = new WP_Query(apply_filters('widget_posts_args', $args));

			if ($query_recent_posts->have_posts()) {

				echo '<div class="Recent_posts '. esc_attr($instance['style']) .'">';
					echo '<ul>';

						$thumbnail_size = 'blog-navi';
						if ($instance['style'] == 'blog_news') {
							$thumbnail_size = 'blog-portfolio';
						}

						while ($query_recent_posts->have_posts()) {
							$query_recent_posts->the_post();

							$class = ' format-'. get_post_format();
							if (! has_post_thumbnail(get_the_ID())) {
								if (($instance['style'] == 'blog_news') || (! in_array(get_post_format(), array( 'quote', 'link' )))) {
									$class .= ' no-img';
								}
							}

							echo '<li class="post'. esc_attr($class) .'">';
								echo '<a href="'. esc_url(get_permalink()) .'">';

									echo '<div class="photo">';
										if (has_post_thumbnail()) {
											the_post_thumbnail($thumbnail_size, array('class'=>'scale-with-grid'));
										}
										if (comments_open()) {
											echo '<span class="c">'. esc_html(get_comments_number()) .'</span>';
										}
									echo '</div>';

									echo '<div class="desc">';
										echo '<h6>'. wp_kses(get_the_title(), mfn_allowed_html()) .'</h6>';
										echo '<span class="date"><i class="icon-clock"></i>'. esc_html(get_the_date()) .'</span>';
									echo '</div>';

								echo '</a>';
							echo '</li>';
						}

						wp_reset_postdata();

					echo '</ul>';
				echo '</div>'."\n";
			}

			echo wp_kses_post($after_widget);
		}

		/**
		 * Deals with the settings when they are saved by the admin.
		 */

		public function update($new_instance, $old_instance)
		{
			$instance = $old_instance;

			$instance[ 'title' ] = strip_tags($new_instance['title']);
			$instance[ 'count' ] = (int) $new_instance['count'];
			$instance[ 'category' ] = strip_tags($new_instance['category']);
			$instance[ 'style' ] = strip_tags($new_instance['style']);

			return $instance;
		}

		/**
		 * Displays the form for this widget on the Widgets page of the WP Admin area.
		 */

		public function form($instance)
		{
			$title = isset($instance['title']) ? esc_attr($instance['title']) 	: '';
			$count = isset($instance['count']) ? absint($instance['count']) 		: 5;
			$category	= isset($instance['category']) ? esc_attr($instance['category']) : '';
			$style = isset($instance['style']) ? esc_attr($instance['style']) 	: '';
			?>

				<p>
					<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'mfn-opts'); ?></label>
					<input id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" class="widefat" />
				</p>

				<p>
					<label for="<?php echo esc_attr($this->get_field_id('count')); ?>"><?php esc_html_e('Number of posts:', 'mfn-opts'); ?></label>
					<input id="<?php echo esc_attr($this->get_field_id('count')); ?>" name="<?php echo esc_attr($this->get_field_name('count')); ?>" value="<?php echo esc_attr($count); ?>" type="number" step="1" min="1" class="widefat" />
				</p>

				<p>
					<label for="<?php echo esc_attr($this->get_field_id('category')); ?>"><?php esc_html_e('Category:', 'mfn-opts'); ?></label>
					<select id="<?php echo esc_attr($this->get_field_id('category')); ?>" name="<?php echo esc_attr($this->get_field_name('category')); ?>" class="widefat">
						<?php
							$categories = mfn_get_categories('category');
							foreach ($categories as $k=>$v) {
								$selected = ($category == $k) ? 'selected="selected"' : false;
								echo '<option value="'. esc_attr($k) .'" '. $selected .'>'. esc_attr($v) .'</option>';
							}
						?>
					</select>
				</p>

				<p>
					<label for="<?php echo esc_attr($this->get_field_id('style')); ?>"><?php esc_html_e('Style:', 'mfn-opts'); ?></label>
					<select id="<?php echo esc_attr($this->get_field_id('style')); ?>" name="<?php echo esc_attr($this->get_field_name('style')); ?>" class="widefat">
						<option value="classic" <?php selected($style, 'classic'); ?>><?php esc_html_e('Classic', 'mfn-opts'); ?></option>
						<option value="blog_news" <?php selected($style, 'blog_news'); ?>><?php esc_html_e('Blog News', 'mfn-opts'); ?></option>
					</select>
				</p>

			<?php
		}
	}
}
