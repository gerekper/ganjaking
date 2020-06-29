<?php
class MFN_Options_sliderbar extends MFN_Options
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

	public function render()
	{

		// parameters

		if (isset($this->field['param'])) {
			$param = $this->field['param'];
		} else {
			$param = false;
		}

		$min = isset($param['min']) ? $param['min'] : 1;
		$max = isset($param['max']) ? $param['max'] : 100;

		// output -----

		echo '<div class="mfn-slider-field clearfix">';

			echo '<div id="'. esc_attr($this->field['id']) .'_sliderbar" class="sliderbar" rel="'. esc_attr($this->field['id']) .'" data-min="'. esc_attr($min) .'" data-max="'. esc_attr($max) .'"></div>';

			echo '<input type="number" class="sliderbar_input" min="'. esc_attr($min) .'" max="'. esc_attr($max) .'" id="'. esc_attr($this->field['id']) .'" name="'. esc_attr($this->prefix.'['.$this->field['id'].']') .'" value="'. esc_attr($this->value) .'"/>';

			echo '<div class="range">'. esc_attr($min) .' - '. esc_attr($max) .'</div>';

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
		wp_enqueue_style('mfn-opts-jquery-ui-css');
		wp_enqueue_script('mfn-opts-field-sliderbar', MFN_OPTIONS_URI.'fields/sliderbar/field_sliderbar.js', array('jquery', 'jquery-ui-core', 'jquery-ui-slider'), MFN_THEME_VERSION, true);
	}
}
