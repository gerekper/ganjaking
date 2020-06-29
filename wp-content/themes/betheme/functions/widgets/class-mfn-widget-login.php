<?php
/**
 * Muffin Widget Login
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

if (! class_exists('Mfn_Widget_Login')) {
	class Mfn_Widget_Login extends WP_Widget
	{

		/**
		 * Constructor
		 */

		public function __construct()
		{
			$widget_ops = array(
				'classname' => 'widget_mfn_login',
				'description' => esc_html__('Displays Login Form.', 'mfn-opts')
			);

			parent::__construct('widget_mfn_login', esc_html__('Muffin Login', 'mfn-opts'), $widget_ops);

			$this->alt_option_name = 'widget_mfn_login';
		}

		/**
		 * Outputs the HTML for this widget.
		 */

		public function widget($args, $instance)
		{
			global $user_login;

			if (! isset($args['widget_id'])) {
				$args['widget_id'] = null;
			}
			extract($args, EXTR_SKIP);
			extract($instance);

			echo wp_kses_post($before_widget);

			$title = apply_filters('widget_title', $title, $instance, $this->id_base);

			if (is_user_logged_in()) {
				$user = get_user_by('login', $user_login);
				$title = esc_html__('Welcome', 'mfn-opts').' '.$user->data->display_name;
			}

			echo '<div class="mfn-login">';

			if ($title) {
				echo wp_kses($before_title, array('h3'=>array(),'h4'=>array()));
					echo wp_kses($title, mfn_allowed_html());
				echo wp_kses($after_title, array('h3'=>array(),'h4'=>array()));
			}

			// validation

			if (isset($_GET['login']) && $_GET['login'] == 'failed') {
				$errcode = $_GET['errcode'];

				if ($errcode == "empty_username" || $errcode == "empty_password") {
					$error = __('Please enter Username and Password', 'mfn-opts');
				} elseif ($errcode == 'invalid_username') {
					$error = __('Invalid Username', 'mfn-opts');
				} elseif ($errcode == 'incorrect_password') {
					$error = __('Incorrect Password', 'mfn-opts');
				}

				echo '<div class="alert alert_error">'. esc_html($error) .'</div>';
			}

			if (is_user_logged_in()) {

				echo '<div class="avatar-wrapper">'. get_avatar($user->ID, 64) .'</div>';

				echo '<div class="author">';

					esc_html_e('Logged in as ', 'mfn-opts');
					echo '<strong>'. esc_html(ucfirst(implode(', ', $user->roles))) .'</strong><br />';
					echo '<a href="'. esc_url(admin_url()) .'">'. esc_html__('Dashboard', 'mfn-opts') .'</a>';
					echo '<span class="sep">|</span>';
					echo '<a href="'. esc_url(admin_url()) .'profile.php">'. esc_html__('Profile', 'mfn-opts') .'</a>';
					echo '<span class="sep">|</span>';
					echo '<a href="'. esc_url(wp_logout_url(site_url())) .'">'. esc_html__('Logout', 'mfn-opts') .'</a>';

				echo '</div>';

			} else {

				wp_login_form(array( 'value_remember' => 0,
						'redirect' => site_url(),
						'remember'=> false
					));

				echo '<div class="links">';
					if ($show_register) {
						echo '<a href="'. esc_url(wp_registration_url() ).'">'. esc_html__('Register', 'mfn-opts') .'</a>';
					}
					if ($show_register && $show_forgotten_password) {
						echo '<span class="sep">|</span>';
					}
					if ($show_forgotten_password) {
						echo '<a href="'. esc_url(wp_registration_url()) .'">'. esc_html__('Lost your password?', 'mfn-opts') .'</a>';
					}
				echo '</div>';

			}

			echo '</div>'."\n";

			echo wp_kses_post($after_widget);
		}

		/**
		 * Deals with the settings when they are saved by the admin.
		 */

		public function update($new_instance, $old_instance)
		{
			$instance = $old_instance;

			$instance['title'] = strip_tags($new_instance['title']);
			$instance['show_register'] = (int) $new_instance['show_register'];
			$instance['show_forgotten_password'] = (int) $new_instance['show_forgotten_password'];

			return $instance;
		}

		/**
		 * Displays the form for this widget on the Widgets page of the WP Admin area.
		 */

		public function form($instance)
		{
			$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
			$show_register = isset($instance['show_register']) ? absint($instance['show_register']) : 0;
			$show_forgotten_password = isset($instance['show_forgotten_password']) ? absint($instance['show_forgotten_password']) : 0;
			?>

				<p>
					<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'mfn-opts'); ?></label>
					<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
				</p>

				<p>
					<input id="<?php echo esc_attr($this->get_field_id('show_register')); ?>" name="<?php echo esc_attr($this->get_field_name('show_register')); ?>" type="checkbox" value="1" <?php if (esc_attr($show_register)) {echo "checked='checked'";} ?> />
					<label for="<?php echo esc_attr($this->get_field_id('show_register')); ?>"><?php esc_html_e('Show Register link', 'mfn-opts'); ?></label>
					<br />
					<input id="<?php echo esc_attr($this->get_field_id('show_forgotten_password')); ?>" name="<?php echo esc_attr($this->get_field_name('show_forgotten_password')); ?>" type="checkbox" value="1" <?php if (esc_attr($show_forgotten_password)) {echo "checked='checked'";} ?> />
					<label for="<?php echo esc_attr($this->get_field_id('show_forgotten_password')); ?>"><?php esc_html_e('Show Forgotten Password link', 'mfn-opts'); ?></label>
				</p>

			<?php
		}

	}
}
