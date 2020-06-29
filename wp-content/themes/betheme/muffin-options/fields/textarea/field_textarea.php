<?php
class MFN_Options_textarea extends MFN_Options
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

		$param = $class_field = isset($this->field['param']) ? $this->field['param'] : '';

		// title

		if (strpos($this->field['id'], 'content')) {
			$class_field .= ' mfn-item-excerpt';
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

		echo '<div class="textarea-wrapper '. esc_attr($class) .'">';

			if (strpos($class, 'sc') !== false) {
				echo '<div class="mfn-textarea-header">';

					echo '<div class="mfn-sc-add">';
						echo '<a class="mfn-sc-add-btn" href="javascript:void(0);">Add Content Shortcode</a>';
						echo '<ul class="mfn-sc-add-list">';
							echo '<li><a href="javascript:void(0);" data-rel="alert">Alert</a></li>';
							echo '<li><a href="javascript:void(0);" data-rel="blockquote">Blockquote</a></li>';
							echo '<li><a href="javascript:void(0);" data-rel="button">Button</a></li>';
							echo '<li><a href="javascript:void(0);" data-rel="code">Code</a></li>';
							echo '<li><a href="javascript:void(0);" data-rel="content_link">Content Link</a></li>';
							echo '<li><a href="javascript:void(0);" data-rel="counter_inline">Counter Inline</a></li>';
							echo '<li><a href="javascript:void(0);" data-rel="dropcap">Dropcap</a></li>';
							echo '<li><a href="javascript:void(0);" data-rel="fancy_link">Fancy Link</a></li>';
							echo '<li><a href="javascript:void(0);" data-rel="google_font">Google Font</a></li>';
							echo '<li><a href="javascript:void(0);" data-rel="heading">Heading</a></li>';
							echo '<li><a href="javascript:void(0);" data-rel="highlight">Highlight</a></li>';
							echo '<li><a href="javascript:void(0);" data-rel="hr">Hr</a></li>';
							echo '<li><a href="javascript:void(0);" data-rel="icon">Icon</a></li>';
							echo '<li><a href="javascript:void(0);" data-rel="icon_bar">Icon Bar</a></li>';
							echo '<li><a href="javascript:void(0);" data-rel="icon_block">Icon Block</a></li>';
							echo '<li><a href="javascript:void(0);" data-rel="idea">Idea</a></li>';
							echo '<li><a href="javascript:void(0);" data-rel="image">Image</a></li>';
							echo '<li><a href="javascript:void(0);" data-rel="popup">Popup</a></li>';
							echo '<li><a href="javascript:void(0);" data-rel="progress_icons">Progress Icons</a></li>';
							echo '<li><a href="javascript:void(0);" data-rel="share_box">Share Box</a></li>';
							echo '<li><a href="javascript:void(0);" data-rel="table">Table</a></li>';
							echo '<li><a href="javascript:void(0);" data-rel="tooltip">Tooltip</a></li>';
							echo '<li><a href="javascript:void(0);" data-rel="tooltip_image">Tooltip Image</a></li>';
						echo '</ul>';
					echo '</div>';

					echo '<div class="mfn-sc-tools">';
						echo '<a class="dashicons dashicons-editor-bold" href="javascript:void(0);" data-open="strong" data-close="strong"></a>';
						echo '<a class="dashicons dashicons-editor-italic" href="javascript:void(0);" data-open="em" data-close="em"></a>';
						echo '<a class="dashicons dashicons-editor-underline" href="javascript:void(0);" data-open="u" data-close="u"></a>';
						echo '<a class="" href="javascript:void(0);" data-open="h1" data-close="h1">H1</a>';
						echo '<a class="" href="javascript:void(0);" data-open="h2" data-close="h2">H2</a>';
						echo '<a class="" href="javascript:void(0);" data-open="h3" data-close="h3">H3</a>';
						echo '<a class="" href="javascript:void(0);" data-open="h4" data-close="h4">H4</a>';
						echo '<a class="" href="javascript:void(0);" data-open="h5" data-close="h5">H5</a>';
						echo '<a class="" href="javascript:void(0);" data-open="h6" data-close="h6">H6</a>';
						echo '<a class="dashicons dashicons-editor-paragraph" href="javascript:void(0);" data-open="p" data-close="p"></a>';
						echo '<a class="dashicons dashicons-editor-break" href="javascript:void(0);" data-open="br"></a>';
						echo '<a class="dashicons dashicons-camera" href="javascript:void(0);" data-open="img class=Xscale-with-gridX src=X#X alt=XX"></a>';
						echo '<a class="dashicons dashicons-admin-links" href="javascript:void(0);" data-open="a href=X#X" data-close="a"></a>';
						echo '<a class="width-auto" href="javascript:void(0);" data-open="lipsum" title="Lorem ipsum dolor sit amet...">Lorem</a>';
					echo '</div>';

				echo '</div>';
			}

			// This variable has been safely escaped above in this function
			echo '<textarea '. $name_escaped .' class="'. esc_attr($class_field) .'" rows="8">'. esc_attr($this->value) .'</textarea>';

			if (isset($this->field['desc'])) {
				echo '<span class="description '. esc_attr($class) .'">'. wp_kses($this->field['desc'], mfn_allowed_html('desc')) .'</span>';
			}

		echo '</div>';
	}
}
