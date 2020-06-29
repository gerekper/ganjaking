<?php
class MFN_Options_radio_img extends MFN_Options
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

		// class

		if (isset($this->field['class'])) {
			$class = $this->field['class'];
		} else {
			$class = 'regular-text';
		}

		// name

		if ($meta) {

			// page mata & builder existing items
			$name_escaped = 'name="'. $this->field['id'] .'"';

		} else {

			// theme options
			$name_escaped = 'name="'. $this->prefix .'['. $this->field['id'] .']"';

		}

		// output -----

		echo '<fieldset '. esc_attr($class) .'>';

			foreach ($this->field['options'] as $k => $v) {
				echo '<div class="mfn-radio-item">';

					$selected_escaped = (checked($this->value, $k, false) != '') ? ' mfn-radio-img-selected' : '';

					echo '<label class="mfn-radio-img'. $selected_escaped .'" for="'. esc_attr($this->field['id'] .'_'. $k) .'">';
						// This variable has been safely escaped above in this function
						echo '<input type="radio" id="'. esc_attr($this->field['id'] .'_'. $k) .'" '. $name_escaped . ' value="'. esc_attr($k) .'" '. checked($this->value, $k, false) .'/>';
						echo '<img src="'. esc_url($v['img']) .'" alt="'. esc_attr($v['title']) .'" />';
					echo '</label>';

					echo '<span class="description">'. wp_kses($v['title'], mfn_allowed_html('desc')) .'</span>';

				echo '</div>';
			}

			if (isset($this->field['desc'])) {
				echo '<br style="clear:both;"/>';
				echo '<span class="description">'. wp_kses($this->field['desc'], mfn_allowed_html('desc')) .'</span>';
			}

		echo '</fieldset>';
	}

	/**
	 * Enqueue
	 */

	public function enqueue()
	{
		wp_enqueue_script('mfn-opts-field-radio_img', MFN_OPTIONS_URI .'fields/radio_img/field_radio_img.js', array('jquery'), MFN_THEME_VERSION, true);
	}

}
