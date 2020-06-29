<?php
class MFN_Options_checkbox_pseudo extends MFN_Options
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

		if ($meta == 'new') {

			// builder new
			$name_escaped = 'data-name="'. esc_attr($this->field['id']) .'"';
		} else {

			// page mata & builder existing items
			$name_escaped = 'name="'. esc_attr($this->field['id']) .'"';
		}

		// prepare values array

		$this->value = preg_replace('/\s+/', ' ', $this->value);
		$values = explode(' ', $this->value);

		if (is_array($this->field[ 'options' ])) {

			// Multi Checkboxes

			echo '<div class="mfnf-checkbox pseudo multi">';

				// This variable has been safely escaped above in this function
				echo '<input class="value" type="text" '. $name_escaped .' value="'. esc_attr($this->value) .'"/>';

				echo '<ul>';
					foreach ($this->field[ 'options' ] as $key => $val) {
						if (in_array($key, $values)) {
							$check = $key;
						} else {
							$check = false;
						}

						echo '<li>';
							echo '<label>';
								echo '<input type="checkbox" value="'. esc_attr($key) .'" '. checked($check, $key, false) .' />';
								echo '<span class="label">'. wp_kses($val, mfn_allowed_html('desc')) .'</span>';
							echo '</label>';
						echo '</li>';
					}
				echo '</ul>';

				if (isset($this->field['desc'])) {
					echo '<span class="description">'. wp_kses($this->field['desc'], mfn_allowed_html('desc')) .'</span>';
				}

			echo '</div>';

		} else {

			// Single Checkbox

			echo 'please use "switch" field for single checkbox';
		}

	}

	/**
	 * Enqueue Function.
	 */

	public function enqueue()
	{
		wp_enqueue_script('mfn-opts-field-checkbox-pseudo', MFN_OPTIONS_URI .'fields/checkbox_pseudo/field_checkbox_pseudo.js', array('jquery'), MFN_THEME_VERSION, true);
	}

}
