<?php
class MFN_Options_font_select extends MFN_Options
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

		$fonts = mfn_fonts();

		// output -----

		// This variable has been safely escaped above in this function
		echo '<select '. $name_escaped .' '. esc_attr($class) .' rows="6" >';

			// system fonts

			echo '<optgroup label="'. esc_html__('System', 'mfn-opts') .'">';
				foreach ($fonts['system'] as $font) {
					echo '<option value="'. esc_attr($font) .'" '. selected($this->value, $font, false).'>'. esc_html($font) .'</option>';
				}
			echo '</optgroup>';

			// custom font | uploaded in theme options

			if (key_exists('custom', $fonts)) {
				echo '<optgroup label="'. esc_html__('Custom Fonts', 'mfn-opts') .'">';
					foreach ($fonts['custom'] as $font) {
						echo '<option value="'. esc_attr($font) .'" '. selected($this->value, $font, false).'>'. esc_html(str_replace('#', '', $font)) .'</option>';
					}
				echo '</optgroup>';
			}

			// google fonts | all

			echo '<optgroup label="'. esc_html__('Google Fonts', 'mfn-opts') .'">';
				foreach ($fonts['all'] as $font) {
					echo '<option value="'. esc_attr($font) .'" '. selected($this->value, $font, false) .'>'. esc_html($font) .'</option>';
				}
			echo '</optgroup>';

		echo '</select>';

		if (isset($this->field['desc'])) {
			echo '<span class="description">'. wp_kses($this->field['desc'], mfn_allowed_html('desc')) .'</span>';
		}
	}
}
