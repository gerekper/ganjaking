<?php
/**
 * Widget Muffin Menu
 *
 * @package Betheme
 * @author Muffin group
 * @link https://muffingroup.com
 */

if (! class_exists('Mfn_Widget_Menu')) {
	class Mfn_Widget_Menu extends WP_Widget
	{

		/**
		 * Constructor
		 */

		public function __construct()
		{
			$widget_ops = array( 'classname' => 'widget_mfn_menu', 'description' => esc_html__('Use this widget on pages to display aside menu with children or siblings of the current page', 'mfn-opts') );

			parent::__construct('widget_mfn_menu', esc_html__('Muffin Menu', 'mfn-opts'), $widget_ops);

			$this->alt_option_name = 'widget_mfn_menu';
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

			$title = "";
			if ($instance['use_page_title']) {
				$title = wp_title('', false);
			} elseif ($instance['title']) {
				$title = $instance['title'];
			}

			$title = apply_filters('widget_title', $title, $instance, $this->id_base);

			echo wp_kses_post($before_widget);

			if ($title) {
				echo wp_kses($before_title, array('h3'=>array(),'h4'=>array()));
					echo wp_kses($title, mfn_allowed_html());
				echo wp_kses($after_title, array('h3'=>array(),'h4'=>array()));
			}

			if (! $instance['nav_menu']) {

				// pages menu

				$parentID = false;
				if ($instance['use_page_sibling']==1) {

					// sibling
					$aPost = get_post(get_the_ID());
					if (is_array($aPost->ancestors) && key_exists(0, $aPost->ancestors)) {
						$parentID = $aPost->ancestors[0];
					}

				} else {

					// children
					$parentID = get_the_ID();

				}

				$aPages_attr = array(
					'title_li' => '',
					'depth' => $instance['depth'] ? intval($instance['depth']) : 1,
					'child_of' => $parentID,
					'link_before' => '',
					'echo' => 0,
				);

				$aPages = wp_list_pages($aPages_attr);

				// if there is no children

				if ((! $aPages) && ($instance['use_page_sibling'] == 2)) {
					$aPost = get_post(get_the_ID());
					$parentID = false;
					if (is_array($aPost->ancestors) && key_exists(0, $aPost->ancestors)) {
						$parentID = $aPost->ancestors[0];
					}

					$aPages_attr['child_of'] = $parentID;
					$aPages = wp_list_pages($aPages_attr);
				}

				// echo

				if ($aPages) {
					echo '<ul class="menu">';
						echo wp_kses_post($aPages);
					echo '</ul>';
				}

			} else {

				// custom menu

				$submenu = isset($instance['submenus']) ? $instance['submenus'] : 'show';
				$args = array(
					'menu_class' => 'menu submenus-'. $submenu,
					'menu' => $instance['nav_menu'],
					'depth' => $instance['depth'] ? intval($instance['depth']) : 1,
				);
				wp_nav_menu($args);
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
			$instance['nav_menu'] = (int) $new_instance['nav_menu'];

			$instance['depth'] = (int) $new_instance['depth'];
			$instance['submenus'] = strip_tags($new_instance['submenus']);

			$instance['use_page_title'] = (int) $new_instance['use_page_title'];
			$instance['use_page_sibling'] = (int) $new_instance['use_page_sibling'];

			return $instance;
		}

		/**
		 * Displays the form for this widget on the Widgets page of the WP Admin area.
		 */

		public function form($instance)
		{
			$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
			$nav_menu = isset($instance['nav_menu']) ? absint($instance['nav_menu']) : 0;

			$depth = isset($instance['depth']) ? absint($instance['depth']) : 1;
			$submenus = isset($instance['submenus']) ? esc_attr($instance['submenus']) : '';

			$use_page_title = isset($instance['use_page_title']) ? absint($instance['use_page_title']) : 0;
			$use_page_sibling = isset($instance['use_page_sibling']) ? absint($instance['use_page_sibling']) : 2;

			// get menus

			$menus = wp_get_nav_menus(array( 'orderby' => 'name' ));
			?>

				<p>
					<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'mfn-opts'); ?></label>
					<input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
				</p>

				<p>
					<input id="<?php echo esc_attr($this->get_field_id('use_page_title')); ?>" name="<?php echo esc_attr($this->get_field_name('use_page_title')); ?>" type="checkbox" value="1" <?php if ($use_page_title) {echo 'checked="checked"';} ?> />
					<label for="<?php echo esc_attr($this->get_field_id('use_page_title')); ?>"><?php esc_html_e('Use current page title', 'mfn-opts'); ?></label>
				</p>

				<p>
					<label for="<?php echo esc_attr($this->get_field_id('nav_menu')); ?>"><?php esc_html_e('Menu:', 'mfn-opts'); ?></label>
					<select id="<?php echo esc_attr($this->get_field_id('nav_menu')); ?>" name="<?php echo esc_attr($this->get_field_name('nav_menu')); ?>">
						<?php
							echo '<option value="0">'. esc_html__('- Pages Menu -', 'mfn-opts') .'</option>';
							foreach ($menus as $menu) {
								echo '<option value="'. esc_attr($menu->term_id) .'" '. selected($nav_menu, $menu->term_id, false). '>'. esc_attr($menu->name) . '</option>';
							} ?>
					</select>
				</p>

				<p>
					<label><b><?php esc_html_e('Options for Custom Menu', 'mfn-opts'); ?></b></label>
					<br />

					<label for="<?php echo esc_attr($this->get_field_id('depth')); ?>"><?php esc_html_e('Depth:', 'mfn-opts'); ?></label>
					<input id="<?php echo esc_attr($this->get_field_id('depth')); ?>" name="<?php echo esc_attr($this->get_field_name('depth')); ?>" type="text" value="<?php echo esc_attr($depth); ?>" size="3"/>
					<br />

					<label for="<?php echo esc_attr($this->get_field_id('submenus')); ?>"><?php esc_html_e('Submenus:', 'mfn-opts'); ?></label>
					<select id="<?php echo esc_attr($this->get_field_id('submenus')); ?>" name="<?php echo esc_attr($this->get_field_name('submenus')); ?>">
						<?php
							echo '<option value="show" '. selected($submenus, 'show', false). '>'. esc_html__('Always show', 'mfn-opts') .'</option>';
							echo '<option value="hover" '. selected($submenus, 'hover', false). '>'. esc_html__('Show on hover', 'mfn-opts') .'</option>';
							echo '<option value="hover submenu-active" '. selected($submenus, 'hover submenu-active', false). '>'. esc_html__('Show on hover, always show active', 'mfn-opts') .'</option>';
							echo '<option value="click" '. selected($submenus, 'click', false). '>'. esc_html__('Show on click', 'mfn-opts') .'</option>';
						?>
					</select>
				</p>

				<p>
					<label><b><?php esc_html_e('Options for Pages Menu', 'mfn-opts'); ?></b></label>
					<br />

					<input id="<?php echo esc_attr($this->get_field_id('use_page_sibling')); ?>" name="<?php echo esc_attr($this->get_field_name('use_page_sibling')); ?>" type="radio" value="1" <?php if ($use_page_sibling==1) {echo "checked='checked'";} ?> />
					<label for="<?php echo esc_attr($this->get_field_id('use_page_sibling')); ?>"><?php esc_html_e('Show page siblings', 'mfn-opts'); ?></label>
					<br/>

					<input id="<?php echo esc_attr($this->get_field_id('use_page_childrens')); ?>" name="<?php echo esc_attr($this->get_field_name('use_page_sibling')); ?>" type="radio" value="0" <?php if (! $use_page_sibling) {echo "checked='checked'";} ?> />
					<label for="<?php echo esc_attr($this->get_field_id('use_page_childrens')); ?>"><?php esc_html_e('Show page children', 'mfn-opts'); ?></label>
					<br/>

					<input id="<?php echo esc_attr($this->get_field_id('use_page_childrens2')); ?>" name="<?php echo esc_attr($this->get_field_name('use_page_sibling')); ?>" type="radio" value="2" <?php if ($use_page_sibling==2) {echo "checked='checked'";} ?> />
					<label for="<?php echo esc_attr($this->get_field_id('use_page_childrens2')); ?>"><?php esc_html_e('Show page children (if there is no children, show siblings)', 'mfn-opts'); ?></label>
				</p>

			<?php
		}
	}
}
