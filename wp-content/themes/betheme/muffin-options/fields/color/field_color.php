<?php
class MFN_Options_color extends MFN_Options
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

		} elseif ($meta) {

			// page mata & builder existing items
			$name_escaped = 'name="'. esc_attr($this->field['id']) .'"';

		} else {

			// theme options
			$name_escaped = 'name="'. esc_attr($this->prefix) .'['. esc_attr($this->field['id']) .']"';

		}

		// value

		if ($this->value) {
			$value = $this->value;
		} else {
			$value = isset($this->field['std']) ? $this->field['std'] : '';
		}

		// alpha

		if (isset($this->field[ 'alpha' ])) {
			$alpha_escaped = ' data-alpha="true"';
		} else {
			$alpha_escaped = false;
		}

		echo '<div class="mfn-field-color">';

			// This variable has been safely escaped above in this function
			echo '<input type="text" id="'. esc_attr($this->field['id']) .'" '. $name_escaped .' value="'. esc_attr($value) .'" class="has-colorpicker"'. $alpha_escaped .'/>';

			if (isset($this->field['desc'])) {
				echo '<span class="description">'. wp_kses($this->field['desc'], mfn_allowed_html('desc')) .'</span>';
			}

		echo '</div>';
	}

	/**
	 * Enqueue
	 */
	public function enqueue()
	{

		// Add the color picker css file
		wp_enqueue_style('wp-color-picker');

		// Include our custom jQuery file with WordPress Color Picker dependency
		wp_enqueue_script('mfn-opts-field-color', MFN_OPTIONS_URI .'fields/color/field_color.js', array('wp-color-picker'), MFN_THEME_VERSION, true);
	}
}
