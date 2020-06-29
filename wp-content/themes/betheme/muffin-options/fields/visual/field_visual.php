<?php
class MFN_Options_visual extends MFN_Options
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

		// output -----

		echo '<div class="mfnf-visual">';
			echo '<div class="wp-core-ui wp-editor-wrap tmce-active">';
				echo '<div class="wp-editor-tools hide-if-no-js">';

					echo '<div class="wp-media-buttons">';
						echo '<button type="button" class="button insert-media add_media" data-editor="mfn-editor"><span class="wp-media-buttons-icon"></span> Add Media</button>';
					echo '</div>';

					echo '<div class="wp-editor-tabs">';
						echo '<button type="button" class="wp-switch-editor switch-tmce" data-wp-editor-id="mfn-editor">Visual</button>';
						echo '<button type="button" class="wp-switch-editor switch-html" data-wp-editor-id="mfn-editor">Text</button>';
					echo '</div>';

				echo '</div>';

				echo '<div class="wp-editor-container">';

					// This variable has been safely escaped above in this function
					echo '<textarea '. $name_escaped .' class="editor wp-editor-area" rows="8">'. esc_textarea($this->value) .'</textarea>';

				echo '</div>';
			echo '</div>';
		echo '</div>';

	}

	/**
	 * Enqueue
	 */

	public function enqueue()
	{
		$localize = array(
			'mfnsc' => get_theme_file_uri('/functions/tinymce/plugin.js'),
		);

		wp_enqueue_media();
		wp_enqueue_script('mfn-opts-field-visual', MFN_OPTIONS_URI .'fields/visual/field_visual.js', array('jquery'), MFN_THEME_VERSION, true);
		wp_localize_script('mfn-opts-field-visual', 'fieldVisualJS', $localize);
	}

}
