<?php
class MFN_Options_upload_multi
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
			$class = 'image';
		}

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

		if (! $this->value) {
			$remove_escaped = 'style="display:none;"';
		} else {
			$remove_escaped = false;
		}

		// output -----

		echo '<div class="mfnf-upload multi">';

			// This variable has been safely escaped above in this function
			echo '<input type="text" class="upload-input" '. $name_escaped .' value="'. esc_attr($this->value) .'" autocomplete=off />';

			echo ' <a href="javascript:void(0);" class="upload-add btn-blue" data-button="'. esc_html__('Add Images', 'mfn-opts') .'"  ><span></span>'. esc_html__('Browse', 'mfn-opts') .'</a>';
			echo ' <a href="javascript:void(0);" class="upload-remove all" '. $remove_escaped .'>'. esc_html__('Remove All Uploads', 'mfn-opts') .'</a>';

			echo '<section class="gallery-container clearfix">';
				$this->loop_over_the_images();
			echo '</section>';

			if (isset($this->field['desc']) && ! empty($this->field['desc'])) {
				echo '<span class="description">'. wp_kses($this->field['desc'], mfn_allowed_html('desc')) .'</span>';
			}

		echo '</div>';
	}

	private function loop_over_the_images()
	{
		$unsplited_string  = $this->value;

		if ($unsplited_string === '') {
			return;
		}

		$array_of_img_ids = explode(",", $unsplited_string);

		// escaped output ----

		$output_escaped = '';

		foreach ($array_of_img_ids as $img_id) {
			$img_src = wp_get_attachment_image_src($img_id, 'thumbnail');
			$img_src = $img_src[0];

			echo '<div class="image-container">';
				echo '<img class="screenshot image" data-pic-id="'. esc_attr($img_id) .'" src="'. esc_url($img_src) .'" />';
				echo '<a href="#" class="upload-remove single dashicons dashicons-no"></a>';
			echo '</div>';
		}
	}

	/**
	 * Enqueue
	 */

	public function enqueue()
	{
		wp_enqueue_media();
		wp_enqueue_script('mfn-opts-field-upload-multi', MFN_OPTIONS_URI .'fields/upload_multi/field_upload_multi.js', array('jquery'), MFN_THEME_VERSION, true);
	}

}
