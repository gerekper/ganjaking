<?php
class MFN_Options_select extends MFN_Options
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
			$class = '';
		}

		// name

		if ($meta == 'new') {

			// builder
			$name_escaped = 'data-name="'. esc_attr($this->field['id']) .'"';

		} elseif ($meta) {

			// page mata & builder existing items
			$name_escaped = 'name="'. esc_attr($this->field['id']) .'"';

		} else {

			// theme options
			$name_escaped = 'name="'. esc_attr($this->prefix .'['. $this->field['id'] .']') .'"';

		}

		// wpml

		if (isset($this->field['wpml']) && ! empty($this->field['wpml'])) {
			if ($this->value && function_exists('icl_object_id')) {
				$term = get_term_by('slug', $this->value, $this->field['wpml']);
				$term = apply_filters('wpml_object_id', $term->term_id, $this->field['wpml'], true);
				$this->value = get_term_by('term_id', $term, $this->field['wpml'])->slug;
			}
		}

		// output -----

		// This variable has been safely escaped above in this function
		echo '<select '. $name_escaped .' class="'. esc_attr($class) .'" rows="6" >';
			if (is_array($this->field['options'])) {
				foreach ($this->field['options'] as $k => $v) {
					echo '<option value="'. esc_attr($k) .'" '. selected($this->value, $k, false) .'>'. esc_html($v) .'</option>';
				}
			}
		echo '</select>';

		if (isset($this->field['desc'])) {
			echo '<span class="description '. esc_attr($class) .'">'. wp_kses($this->field['desc'], mfn_allowed_html('desc')) .'</span>';
		}
	}
}
