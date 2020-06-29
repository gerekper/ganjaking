<?php
class MFN_Options_upload // extends MFN_Options
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

		// data

		$data = isset($this->field[ 'data' ]) ? $this->field[ 'data' ] : 'image';

		// name

		if ($meta == 'new') {

			// builder new
			$name_escaped = 'data-name="'. esc_attr($this->field['id']) .'"';

		} elseif ($meta) {

			// page mata & builder existing items
			$name_escaped = 'name="'. esc_attr($this->field['id']) .'"';

		} else {

			// theme options
			$name_escaped = 'name="'. esc_attr($this->prefix.'['.$this->field['id'].']') .'"';

		}

		// value is empty

		if ($this->value == '') {
			$remove_escaped = 'style="display:none;"';
			$upload_escaped = false;
		} else {
			$remove_escaped = '';
			$upload_escaped = 'style="display:none;"';
		}

		// output -----

		echo '<div class="mfn-upload-field">';

			// This variable has been safely escaped above in this function
			echo '<input type="text" '. $name_escaped .' value="'. esc_attr($this->value) .'" class="'. esc_attr($data) .'" />';

			echo '&nbsp;<a href="javascript:void(0);" data-choose="Choose a File" data-update="Select File" class="mfn-opts-upload" '. $upload_escaped .'><span></span>'. esc_html__('Browse', 'mfn-opts') .'</a>';
			echo ' <a href="javascript:void(0);" class="mfn-opts-upload-remove" '. $remove_escaped .'>'. esc_html__('Remove Upload', 'mfn-opts') .'</a>';

			if ('image' == $data) {
				echo '<img class="mfn-opts-screenshot '. esc_attr($data) .'" src="'. esc_url($this->value) .'" />';
			}

			if (isset($this->field['desc'])) {
				echo '<span class="description '. esc_attr($data) .'">'. wp_kses($this->field['desc'], mfn_allowed_html('desc')) .'</span>';
			}

		echo '</div>';

	}

	/**
	 * Enqueue
	 */

	public function enqueue()
	{
		wp_enqueue_media();
		wp_enqueue_script('mfn-opts-field-upload', MFN_OPTIONS_URI .'fields/upload/field_upload.js', array('jquery'), MFN_THEME_VERSION, true);
	}

}
