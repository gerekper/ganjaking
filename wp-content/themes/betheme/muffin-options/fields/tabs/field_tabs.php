<?php
class MFN_Options_tabs extends MFN_Options
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

		$name	= (! $meta) ? ($this->prefix .'['. $this->field['id'] .']') : $this->field['id'];

		if ($meta == 'new') {

			// builder new
			$field_prefix = 'data-';

		} else {

			// builder exist & theme options
			$field_prefix = '';
		}

		// output -----

		$count = ($this->value) ? count($this->value) : 0;

		echo '<input type="hidden" '. esc_attr($field_prefix) .'name="'. esc_attr($name) .'[count][]" class="mfn-tabs-count" value="'. esc_attr($count) .'" />';

		echo '<a href="javascript:void(0);" class="btn-blue mfn-add-tab" rel-name="'. esc_attr($name) .'">'. esc_html__('Add tab', 'mfn-opts') .'</a>';
		echo '<br style="clear:both;" />';

		echo '<ul class="tabs-ul">';

			if (isset($this->value) && is_array($this->value)) {
				foreach ($this->value as $k => $value) {
					echo '<li>';

						echo '<label>'. esc_html__('Title', 'mfn-opts') .'</label>';
						echo '<input type="text" name="'. esc_attr($name) .'[title][]" value="'. htmlspecialchars(stripslashes($value['title'])) .'" />';

						echo '<label>'. esc_html__('Content', 'mfn-opts') .'</label>';
						echo '<textarea name="'. esc_attr($name) .'[content][]" value="" >'. esc_textarea($value['content']) .'</textarea>';

						echo '<a href="" class="mfn-btn-close mfn-remove-tab"><em>delete</em></a>';

					echo '</li>';
				}
			}

			// default tab to clone

			echo '<li class="tabs-default">';

				echo '<label>'. esc_html__('Title', 'mfn-opts') .'</label>';
				echo '<input type="text" name="" value="" />';

				echo '<label>'. esc_html__('Content', 'mfn-opts') .'</label>';
				echo '<textarea name="" value=""></textarea>';

				echo '<a href="" class="mfn-btn-close mfn-remove-tab"><em>delete</em></a>';

			echo '</li>';

		echo '</ul>';

		if (isset($this->field['desc'])) {
			echo ' <span class="description tabs-desc">'. wp_kses($this->field['desc'], mfn_allowed_html('desc')) .'</span>';
		}
	}

	/**
	 * Enqueue
	*/

	public function enqueue()
	{
		wp_enqueue_script('mfn-opts-field-tabs', MFN_OPTIONS_URI .'fields/tabs/field_tabs.js', array('jquery'), MFN_THEME_VERSION, true);
	}
}
