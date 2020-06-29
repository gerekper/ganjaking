<?php
class MFN_Options_switch // extends MFN_Options
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

		$this->enqueue();
	}

	/**
	 * Render
	 */

	public function render($meta = false)
	{

		// name

		if ($meta) {

			// page mata & builder existing items
			$name_escaped = 'name="'. esc_attr($this->field['id']) .'"';

		} else {

			// theme options
			$name_escaped = 'name="'. esc_attr($this->prefix.'['.$this->field['id'].']') .'"';

		}

		// output -----

		echo '<div class="mfn-switch-field">';

			// fix for value "off == 0"

			if (! $this->value) {
				$this->value = 0;
			}

			// fix for WordPress 3.6 meta options

			if (strpos($this->field['id'], '[]') === false) {
				// This variable has been safely escaped above in this function
				echo '<input type="hidden" '. $name_escaped .' value="0" />';
			}

			// This variable has been safely escaped above in this function
			echo '<input type="checkbox" data-toggle="switch" id="'. esc_attr($this->field['id']) .'" '. $name_escaped .' value="1" '. checked($this->value, 1, false) .' />';

			if (isset($this->field['desc'])) {
				echo '<span class="description btn-desc">'. wp_kses($this->field['desc'], mfn_allowed_html('desc')) .'</span>';
			}

		echo '</div>';
	}

	/**
	 * Enqueue
	 */

	public function enqueue()
	{
		wp_enqueue_script('mfn-opts-field-switch', MFN_OPTIONS_URI .'fields/switch/field_switch.js', array('jquery'), MFN_THEME_VERSION, true);
	}
}
