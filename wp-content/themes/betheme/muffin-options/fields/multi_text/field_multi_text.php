<?php
class MFN_Options_multi_text extends MFN_Options
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

		// class

		if (isset($this->field['class'])) {
			$class = $this->field['class'];
		} else {
			$class = false;
		}

		// name

		$name = $this->prefix .'['. $this->field['id'] .'][]';

		// output -----

		echo '<div class="mfn-multi-text-field">';

			echo '<input type="text" class="multi-text-add small-text" placeholder="'. esc_html__('Type sidebar title here', 'mfn-opts') .'">';
			echo '<a href="javascript:void(0);" class="multi-text-btn btn-blue" rel-id="'. esc_attr($this->field['id']) .'-ul" rel-name="'. esc_attr($name) .'">'. esc_html__('Add sidebar', 'mfn-opts') .'</a>';

			if (isset($this->field['desc'])) {
				echo '<span class="description multi-text-desc">'. wp_kses($this->field['desc'], mfn_allowed_html('desc')) .'</span>';
			}

			echo '<ul class="multi-text-ul" id="'. esc_attr($this->field['id']) .'-ul">';

				if (isset($this->value) && is_array($this->value)) {
					foreach ($this->value as $k => $value) {
						if ($value != '') {
							echo '<li>';
								echo '<input type="hidden" id="'. esc_attr($this->field['id']) .'-'. esc_attr($k) .'" name="'. esc_attr($name) .'" value="'. esc_attr($value) .'" class="'. esc_attr($class) .'" />';
								echo '<span>'. esc_attr($value) .'</span>';
								echo '<a href="" class="multi-text-remove"><em>delete</em></a>';
							echo '</li>';
						}
					}
				}

				echo '<li class="multi-text-default">';
					echo '<input type="hidden" name="" value="" class="'. esc_attr($class) .'" />';
					echo '<span></span>';
					echo '<a href="" class="multi-text-remove"><em>delete</em></a>';
				echo '</li>';

			echo '</ul>';

		echo '</div>';
	}

	/**
	 * Enqueue Function.
	 */

	public function enqueue()
	{
		wp_enqueue_script('mfn-opts-field-multi-text', MFN_OPTIONS_URI .'fields/multi_text/field_multi_text.js', array('jquery'), MFN_THEME_VERSION, true);
	}

}
