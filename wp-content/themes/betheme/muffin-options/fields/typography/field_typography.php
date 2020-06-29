<?php
class MFN_Options_typography extends MFN_Options
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

	public function render()
	{
		$name = $this->prefix .'['. $this->field['id'] .']';

		if (isset($this->field['class'])) {
			$class = $this->field['class'];
		} else {
			$class = false;
		}

		$disable = isset($this->field['disable']) ? $this->field['disable'] : '';

		$value = $this->value;

		if (! $value) {

			$value = $this->field['std'];

		} elseif ( ! is_array($value)) {

			// compatibility with Betheme < 13.5
			$value = array(
				'size' => $value,
				'line_height' => $this->field['std']['line_height'],
				'weight_style' => $this->field['std']['weight_style'],
				'letter_spacing' => $this->field['std']['letter_spacing'],
			);

		}

		// output -----

		echo '<div class="mfn-field-typography '. esc_attr($class) .'">';

			// font size

			echo '<div class="typography-wrapper typography-size">';
				echo '<label>'. esc_html__('Font size', 'mfn-opts') .'</label>';
				echo '<input type="number" name="'. esc_attr($name) .'[size]" value="'. esc_attr($value['size']) .'" class="'. esc_attr($class) .'" />';
				echo '<div class="desc-right">px</div>';
			echo '</div>';

			// line height

			if ($disable != 'line_height') {
				echo '<div class="typography-wrapper typography-line">';
					echo '<label>'. esc_html__('Line height', 'mfn-opts') .'</label>';
					echo '<input type="number" name="'. esc_attr($name) .'[line_height]" value="'. esc_attr($value['line_height']) .'" class="'. esc_attr($class) .'" />';
					echo '<div class="desc-right">px</div>';
				echo '</div>';
			}

			// weight

			echo '<div class="typography-wrapper typography-weight">';

				echo '<label>'. esc_html__('Font weight & style', 'mfn-opts') .'</label>';

				echo '<select name="'. esc_attr($name) .'[weight_style]">';
					echo '<option value="100" '. selected($value['weight_style'], '100', false) .'>100 Thin</option>';
					echo '<option value="100italic" '. selected($value['weight_style'], '100italic', false) .'>100 Thin Italic</option>';
					echo '<option value="200" '. selected($value['weight_style'], '200', false) .'>200 Extra-Light</option>';
					echo '<option value="200italic" '. selected($value['weight_style'], '200italic', false) .'>200 Extra-Light Italic</option>';
					echo '<option value="300" '. selected($value['weight_style'], '300', false) .'>300 Light</option>';
					echo '<option value="300italic" '. selected($value['weight_style'], '300italic', false) .'>300 Light Italic</option>';
					echo '<option value="400" '. selected($value['weight_style'], '400', false) .'>400 Regular</option>';
					echo '<option value="400italic" '. selected($value['weight_style'], '400italic', false) .'>400 Regular Italic</option>';
					echo '<option value="500" '. selected($value['weight_style'], '500', false) .'>500 Medium</option>';
					echo '<option value="500italic" '. selected($value['weight_style'], '500italic', false) .'>500 Medium Italic</option>';
					echo '<option value="600" '. selected($value['weight_style'], '600', false) .'>600 Semi-Bold</option>';
					echo '<option value="600italic" '. selected($value['weight_style'], '600italic', false) .'>600 Semi-Bold Italic</option>';
					echo '<option value="700" '. selected($value['weight_style'], '700', false) .'>700 Bold</option>';
					echo '<option value="700italic" '. selected($value['weight_style'], '700italic', false) .'>700 Bold Italic</option>';
					echo '<option value="800" '. selected($value['weight_style'], '800', false) .'>800 Extra-Bold</option>';
					echo '<option value="800italic" '. selected($value['weight_style'], '800italic', false) .'>800 Extra-Bold Italic</option>';
					echo '<option value="900" '. selected($value['weight_style'], '900', false) .'>900 Black</option>';
					echo '<option value="900italic" '. selected($value['weight_style'], '900italic', false) .'>900 Black Italic</option>';
				echo '</select>';

			echo '</div>';

			// letter spacing

			echo '<div class="typography-wrapper typography-spacing">';
				echo '<label>'. esc_html__('Letter spacing', 'mfn-opts') .'</label>';
				echo '<input type="number" name="'. esc_attr($name) .'[letter_spacing]" value="'. esc_attr($value['letter_spacing']) .'" class="'. esc_attr($class) .'" />';
				echo '<div class="desc-right">px</div>';
			echo '</div>';

			echo '<div class="clearfix"></div>';

			// description

			if (isset($this->field['desc']) && ! empty($this->field['desc'])) {
				echo '<span class="description">'. wp_kses($this->field['desc'], mfn_allowed_html('desc')) .'</span>';
			}

		echo '</div>';
	}
}
