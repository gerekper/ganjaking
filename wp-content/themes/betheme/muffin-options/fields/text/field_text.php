<?php
class MFN_Options_text // extends MFN_Options
{

	protected $field = array();
	protected $value = '';
	protected $prefix = false;

	/**
	 * Constructor
	 */

	public function __construct($field = array(), $value = '', $prefix = false)
	{
		$this->field = $field;
		$this->value = $value;
		$this->prefix = $prefix;
	}

	/**
	 * Render
	 */

	public function render($meta = false)
	{

		// NOTICE builder uses field types: select, text, textarea, upload, tabs, icon

		// class

		if (isset($this->field['class'])) {
			$class = $this->field['class'];
		} else {
			$class = 'regular-text';
		}

		// title

		if (strpos($this->field['id'], 'title')) {
			$class .= ' mfn-item-title';
		}

		// name

		if ($meta == 'new') {

			// builder new
			$name_escaped = 'data-name="'. esc_attr($this->field['id']) .'"';

		} elseif ($meta) {

			// page mata & builder existing items
			$name_escaped = 'name="'. esc_attr($this->field['id']) .'"';

		} else {

			// theme options
			$name_escaped = 'name="'. esc_attr($this->prefix.'['.$this->field['id'].']') .'"';

		}

		// output -----

		// This variable has been safely escaped above in this function
		echo '<input type="text" '. $name_escaped .' value="'. esc_attr($this->value) .'" class="'. esc_attr($class) .'" />';

		if (isset($this->field['desc'])) {
			echo '<span class="description">'. wp_kses($this->field['desc'], mfn_allowed_html('desc')) .'</span>';
		}
	}
}
