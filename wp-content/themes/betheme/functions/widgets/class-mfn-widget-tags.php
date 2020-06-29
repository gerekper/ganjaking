<?php
/**
 * Widget Muffin Tag Cloud
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

if (! class_exists('Mfn_Widget_Tags')) {
	class Mfn_Widget_Tags extends WP_Widget
	{

		/**
		 * Constructor
		 */

		public function __construct()
		{
			$widget_ops = array(
				'classname' => 'widget_mfn_tag_cloud',
				'description' => __('Your most used tags in cloud format .', 'mfn-opts')
			);

			parent::__construct('widget_mfn_tag_cloud', __('Muffin Tag Cloud', 'mfn-opts'), $widget_ops);

			$this->alt_option_name = 'widget_mfn_tag_cloud';
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

			echo '<div class="Tags">';

				$args = array(
					'number' => $instance['count'],
					'orderby' => $instance['orderby'],
					'order' => $instance['order'],
					'taxonomy' => $instance['taxonomy']
				);
				$tags = get_terms($args['taxonomy'], $args);

				if ($tags) {
					echo '<ul class="wp-tag-cloud">';
					foreach ($tags as $tag) {
						echo '<li><a href="'. esc_url(get_term_link(intval($tag->term_id), $tag->taxonomy)) .'"><span>'. esc_html($tag->name) .'</span></a></li>';
					}
					echo '</ul>';
				}

			echo '</div>';

			echo wp_kses_post($after_widget);
		}

		/**
		 * Deals with the settings when they are saved by the admin.
		 */

		public function update($new_instance, $old_instance)
		{
			$instance = $old_instance;

			$instance['title'] = strip_tags($new_instance['title']);
			$instance['count'] = (int) $new_instance['count'];
			$instance['taxonomy'] = strip_tags($new_instance['taxonomy']);
			$instance['orderby'] = strip_tags($new_instance['orderby']);
			$instance['order'] = strip_tags($new_instance['order']);

			return $instance;
		}

		/**
		 * Displays the form for this widget on the Widgets page of the WP Admin area.
		 */

		public function form($instance)
		{
			$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
			$count = isset($instance['count']) ? absint($instance['count']) : 10;
			$taxonomy = isset($instance['taxonomy']) ? esc_attr($instance['taxonomy']) : 'post_tag';
			$orderby = isset($instance['orderby']) ? esc_attr($instance['orderby']) : 'name';
			$order = isset($instance['order']) ? esc_attr($instance['order']) : 'ASC';
			?>

				<p>
					<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'mfn-opts'); ?></label>
					<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
				</p>

				<p>
					<label for="<?php echo esc_attr($this->get_field_id('count')); ?>"><?php _e('Number of tags:', 'mfn-opts'); ?></label>
					<input id="<?php echo esc_attr($this->get_field_id('count')); ?>" name="<?php echo esc_attr($this->get_field_name('count')); ?>" type="text" value="<?php echo esc_attr($count); ?>" size="3"/>
					[1-45]
				</p>

				<p>
					<label for="<?php echo esc_attr($this->get_field_id('taxonomy')); ?>"><?php _e('Taxonomy:', 'mfn-opts'); ?></label>
					<select id="<?php echo esc_attr($this->get_field_id('taxonomy')); ?>" name="<?php echo esc_attr($this->get_field_name('taxonomy')); ?>" class="widefat">
						<option value="category" <?php if ($taxonomy=='category') { echo 'selected="selected"'; } ?>>Categories</option>
						<option value="post_tag" <?php if ($taxonomy=='post_tag') { echo 'selected="selected"'; } ?>>Tags</option>
						<option value="portfolio-types" <?php if ($taxonomy=='portfolio-types') { echo 'selected="selected"'; } ?>>Portfolio categories</option>
					</select>
				</p>

				<p>
					<label for="<?php echo esc_attr($this->get_field_id('orderby')); ?>"><?php _e('Order by:', 'mfn-opts'); ?></label>
					<select id="<?php echo esc_attr($this->get_field_id('orderby')); ?>" name="<?php echo esc_attr($this->get_field_name('orderby')); ?>" class="widefat">
						<option value="count" <?php if ($orderby=='count') { echo 'selected="selected"';} ?>>Count</option>
						<option value="name" <?php if ($orderby=='name') { echo 'selected="selected"';} ?>>Name</option>
					</select>
				</p>

				<p>
					<label for="<?php echo esc_attr($this->get_field_id('order')); ?>"><?php _e('Order:', 'mfn-opts'); ?></label>
					<select id="<?php echo esc_attr($this->get_field_id('order')); ?>" name="<?php echo esc_attr($this->get_field_name('order')); ?>" class="widefat">
						<option value="ASC" <?php selected($order, 'ASC'); ?>>Ascending</option>
						<option value="DESC" <?php selected($order, 'DESC'); ?>>Descending</option>
					</select>
				</p>

			<?php
		}
	}
}
