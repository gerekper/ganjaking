<?php
class MFN_Options_info extends MFN_Options
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
		if (isset($this->field['desc'])) {
			echo '<p class="mfn-field-info">'. wp_kses($this->field['desc'], mfn_allowed_html('desc')) .'</p>';
		}
	}
}
