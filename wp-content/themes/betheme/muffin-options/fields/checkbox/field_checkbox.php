<?php
class MFN_Options_checkbox extends MFN_Options
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

		// class

		if (isset($this->field['class'])) {
			$class = $this->field['class'];
		} else {
			$class = '';
		}

		// name

		if ($meta) {

			// page mata & builder existing items
			$name = $this->field['id'];

		} else {

			// theme options
			$name = $this->prefix .'['. $this->field['id'] .']';

		}

		// output -----

		if (is_array($this->field[ 'options' ])) {

			// multiple checkboxes

			if (! isset($this->value)) {
				$this->value = array();
			}

			if (! is_array($this->value)) {
				$this->value = array();
			}

			echo '<div class="mfnf-checkbox multi '. esc_attr($class) .'">';

				// FIX | Post Meta Save | All values unchecked

				echo '<input type="hidden" name="'. esc_attr($name). '[post-meta]" value="1" checked="checked" />';

				echo '<ul>';
					foreach ($this->field['options'] as $k => $v) {
						if (! key_exists($k, $this->value)) {
							$this->value[$k] = '';
						}

						echo '<li>';
							echo '<label>';
								echo '<input type="checkbox" name="'. esc_attr($name) . '['.esc_attr($k).']" value="'. esc_attr($k) .'" '. checked($this->value[$k], $k, false) .' />';
								echo '<span class="label">'. wp_kses($v, mfn_allowed_html('desc')) .'</span>';
							echo '</label>';
						echo '</li>';
					}
				echo '</ul>';

				if (isset($this->field['desc']) && ! empty($this->field['desc'])) {
					echo '<span class="description">'. wp_kses($this->field['desc'], mfn_allowed_html('desc')) .'</span>';
				}

			echo '</div>';

		} else {

			// single checkbox
			echo 'please use "switch" field for single checkbox';

		}
	}
}
