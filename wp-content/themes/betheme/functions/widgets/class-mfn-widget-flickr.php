<?php
/**
 * Muffin Widget Flickr
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

if (! class_exists('Mfn_Widget_Flickr')) {
	class Mfn_Widget_Flickr extends WP_Widget
	{

		/**
		 * Constructor
		 */

		public function __construct()
		{
			$widget_ops = array(
				'classname' => 'widget_mfn_flickr',
				'description' => esc_html__('Use this widget on pages to display photos from Flickr photostream.', 'mfn-opts')
			);

			parent::__construct('widget_mfn_flickr', esc_html__('Muffin Flickr', 'mfn-opts'), $widget_ops);

			$this->alt_option_name = 'widget_mfn_flickr';
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

			echo '<div class="Flickr">';
				echo'<script src="https://www.flickr.com/badge_code_v2.gne?count='. esc_attr($instance['count']) .'&amp;display='. esc_attr($instance['order']) .'&amp;size=s&amp;layout=x&amp;source=user&amp;user='. esc_attr($instance['userID']) .'"></script>';
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
			$instance['userID'] = strip_tags($new_instance['userID']);
			$instance['count'] = (int) $new_instance['count'];
			$instance['order'] = strip_tags($new_instance['order']);

			return $instance;
		}

		/**
		 * Displays the form for this widget on the Widgets page of the WP Admin area.
		 */

		public function form($instance)
		{
			$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
			$userID = isset($instance['userID']) ? esc_attr($instance['userID']) : '71865026@N00';
			$count = isset($instance['count']) ? absint($instance['count']) : 6;
			$order = isset($instance['order']) ? esc_attr($instance['order']) : 'latest';
			?>

				<p>
					<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'mfn-opts'); ?></label>
					<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
				</p>

				<p>
					<label for="<?php echo esc_attr($this->get_field_id('userID')); ?>"><?php _e('Flickr User ID:', 'mfn-opts'); ?></label>
					<input class="widefat" id="<?php echo esc_attr($this->get_field_id('userID')); ?>" name="<?php echo esc_attr($this->get_field_name('userID')); ?>" type="text" value="<?php echo esc_attr($userID); ?>" />
					<?php _e('Use <a href="http://idgettr.com/" target="_blank">this</a> tool to find your Flickr user ID', 'mfn-opts'); ?>
				</p>

				<p>
					<label for="<?php echo esc_attr($this->get_field_id('count')); ?>"><?php _e('Number of photos:', 'mfn-opts'); ?></label>
					<input id="<?php echo esc_attr($this->get_field_id('count')); ?>" name="<?php echo esc_attr($this->get_field_name('count')); ?>" type="text" value="<?php echo esc_attr($count); ?>" size="3"/>
					[1-10]
				</p>

				<p>
					<input id="<?php echo esc_attr($this->get_field_id('order_latest')); ?>" name="<?php echo esc_attr($this->get_field_name('order')); ?>" type="radio" value="latest" <?php if ($order=="latest") {echo "checked='checked'";} ?> />
					<label for="<?php echo esc_attr($this->get_field_id('order_latest')); ?>"><?php _e('Latest uploads', 'mfn-opts'); ?></label>
					<br/>
					<input id="<?php echo esc_attr($this->get_field_id('order_random')); ?>" name="<?php echo esc_attr($this->get_field_name('order')); ?>" type="radio" value="random" <?php if ($order=="random") {echo "checked='checked'";} ?> />
					<label for="<?php echo esc_attr($this->get_field_id('order_random')); ?>"><?php _e('Random photos', 'mfn-opts'); ?></label>
				</p>
			<?php
		}

	}
}
