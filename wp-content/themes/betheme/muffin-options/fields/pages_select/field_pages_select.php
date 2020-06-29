<?php
class MFN_Options_pages_select extends MFN_Options
{

	/**
	 * Constructor
	 */

	public function __construct($field = array(), $value = '', $prefix = false)
	{
		$this->field = $field;
		$this->value = $value;

		// theme options 'opt_name'
		$this->prefix = $prefix;
	}

	/**
	 * Render
	 */

	public function render($meta = false)
	{

		// class

		if (isset($this->field['class'])) {
			$class = $this->field['class'];
		} else {
			$class = false;
		}

		// name

		if ($meta) {

			// page mata & builder existing items
			$name_escaped = 'name="'. esc_attr($this->field['id']) .'"';

		} else {

			// theme options
			$name_escaped = 'name="'. esc_attr($this->prefix) .'['. esc_attr($this->field['id']) .']"';

		}

		$pages = get_pages('sort_column=post_title&hierarchical=0');

		// output -----

		// This variable has been safely escaped above in this function
		echo '<select '. $name_escaped .' '. esc_attr($class) .' rows="6">';
			echo '<option value="">'. esc_html__('-- select --', 'mfn-opts') .'</option>';
			foreach ($pages as $page) {
				echo '<option value="'. esc_attr($page->ID) .'" '. selected($this->value, $page->ID, false). '>'. esc_html($page->post_title) .'</option>';
			}
		echo '</select>';

		if (isset($this->field['desc'])) {
			echo '<span class="description">'. wp_kses($this->field['desc'], mfn_allowed_html('desc')) .'</span>';
		}

	}
}
