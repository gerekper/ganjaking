<?php
/**
 * Widget Muffin Recent Comments
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

if (! class_exists('Mfn_Widget_Comments')) {
	class Mfn_Widget_Comments extends WP_Widget
	{

		/**
		 * Constructor
		 */

		public function __construct()
		{
			$widget_ops = array(
				'classname' => 'widget_mfn_recent_comments',
				'description' => __('The most recent comments.', 'mfn-opts')
			);

			parent::__construct('widget_mfn_recent_comments', __('Muffin Recent Comments', 'mfn-opts'), $widget_ops);

			$this->alt_option_name = 'widget_mfn_recent_comments';
		}

		/**
		 * Outputs the HTML for this widget.
		 */

		public function widget($args, $instance)
		{
			$translate['translate-commented-on'] = mfn_opts_get('translate') ? mfn_opts_get('translate-commented-on', 'commented on') : __('commented on', 'betheme');

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

			$comments = get_comments(apply_filters('widget_comments_args', array( 'number' => intval($instance['count']), 'status' => 'approve', 'post_status' => 'publish', 'post_type' => 'post' )));

			if (is_array($comments)) {
				echo '<div class="Recent_comments">';
					echo '<ul>';
						foreach ($comments as $comment) {

							$url = get_permalink($comment->comment_post_ID) .'#comment-'. $comment->comment_ID;

							echo '<li>';
								echo '<span class="date_label">'. date_i18n(get_option('date_format'), strtotime($comment->comment_date)) .'</span>';
								echo '<p><i class="icon-user"></i> <strong>'. esc_attr($comment->comment_author) .'</strong> '. esc_html($translate['translate-commented-on']) .' <a href="'. esc_url($url) .'">'. wp_kses(get_the_title($comment->comment_post_ID), mfn_allowed_html()) .'</a></p>';
							echo '</li>';

						}
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

			$instance['title'] = strip_tags($new_instance['title']);
			$instance['count'] = (int) $new_instance['count'];

			return $instance;
		}

		/**
		 * Displays the form for this widget on the Widgets page of the WP Admin area.
		 */

		public function form($instance)
		{
			$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
			$count = isset($instance['count']) ? absint($instance['count']) : 2;
			?>

				<p>
					<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'mfn-opts'); ?></label>
					<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
				</p>

				<p>
					<label for="<?php echo esc_attr($this->get_field_id('count')); ?>"><?php _e('Number of comments:', 'mfn-opts'); ?></label>
					<input id="<?php echo esc_attr($this->get_field_id('count')); ?>" name="<?php echo esc_attr($this->get_field_name('count')); ?>" type="text" value="<?php echo esc_attr($count); ?>" size="3"/>
				</p>

			<?php
		}
	}
}
