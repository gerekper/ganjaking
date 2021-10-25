<?php
/**
 * @package     ReduxFramework
 * @version     1.0.0
 */
// Exit if accessed directly
if (!defined('ABSPATH'))
	exit;

// Don't duplicate me!
if (!class_exists('ReduxFramework_custom_font')) {

	/**
	 * Main ReduxFramework_custom_field class
	 *
	 * @since       1.0.0
	 */
	class ReduxFramework_custom_font extends ReduxFramework {

		/**
		 * Field Constructor.
		 *
		 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
		 *
		 * @since       1.0.0
		 * @access      public
		 * @return      void
		 */
		function __construct($field = array (), $value = '', $parent) {

			$this->parent = $parent;
			$this->field = $field;
			$this->value = $value;

			if (empty($this->extension_dir)) {
				$this->extension_dir = trailingslashit(str_replace('\\', '/', dirname(__FILE__)));
				$this->extension_url = site_url(str_replace(trailingslashit(str_replace('\\', '/', ABSPATH)), '', $this->extension_dir));
				$this->extension_url = plugin_dir_url(__FILE__);
			}

			// Set default args for this field to avoid bad indexes. Change this to anything you use.
			$defaults = array (
				'options' => array (""),
				'stylesheet' => 'fff',
				'output' => true,
				'enqueue' => true,
				'enqueue_frontend' => true
			);
			$this->field = wp_parse_args($this->field, $defaults);

		}

		/**
		 * Field Render Function.
		 *
		 * Takes the vars and outputs the HTML for the field in the settings
		 *
		 * @since       1.0.0
		 * @access      public
		 * @return      void
		 */
		public function render() {
			$def_values = Custom_Font_Validator::generateDefaultArray($this->field);
//			print_r($def_values);
			$this->value = Custom_Font_Validator::addDefaultFontToArray($def_values, $this->value);
//			$this->value = array_merge($this->value, $def_values);
//			print_r($def_values);
			Custom_Font_Validator::instance()->normalizeFolderFont($this->value);
			$defaults = array (
					'show' => array (
						'title' => true,
						'description' => true,
						'url' => true,
					),
					'content_title' => esc_html__('Font', 'wpdaddy_core')
			);

			$this->field = wp_parse_args($this->field, $defaults);
			echo '<div class="redux-slides-accordion2" data-new-content-title="' . esc_attr(sprintf(__('Adding new %s', 'wpdaddy_core'), $this->field['content_title'])) . '">';

			$x = 0;
			$multi = ( isset($this->field['multi']) && $this->field['multi'] ) ? ' multiple="multiple"' : "";
			$jsonArr = array (
					"[zip]" => "application/zip"
			);
			$libFilter = urlencode(json_encode($jsonArr));
			$slides = $this->value;

			$font_list = Custom_Font_Validator::font_list();
			$all_fonts = Custom_Font_Validator::instance()->getLocalFonts();
			if (empty($font_list)) {
				$font_list = array ();
			}
			?>
			<style>
			<?php
// create css for previews
			foreach ($font_list as $f_name => $f_path) {

				$demo_font_preview = Custom_Font_Validator::fontface_css_creator($f_path["name"], $f_path["id"]);

				echo  $demo_font_preview['css']. '

#preview_' . $f_path["name"] . ' {	
font-family: "' . $f_name . '";
}';
			}
			?>
			</style>
			<?php
			if (empty($slides))
				$slides = array ("");

			$atachments = array();

			foreach ($slides as $slide) {

				if (!empty($slide['attachment_id']) && in_array($slide['attachment_id'], $atachments)) {
					continue;
				}
				if (!empty($slide['attachment_id'])) {
					$atachments[] = $slide['attachment_id'];
				}

				//$atachments;

				$defaults = array (
						'title' => '',
						'name' => esc_html__('Add new font', 'wpdaddy_core'),
						'description' => '',
						'sort' => '',
						'url' => '',
						'image' => '',
						'thumb' => '',
						'attachment_id' => '',
						'height' => '',
						'width' => '',
						'select' => array (),
				);
				$slide = wp_parse_args($slide, $defaults);

				if (empty($slide['thumb']) && !empty($slide['attachment_id'])) {
					$img = wp_get_attachment_image_src($slide['attachment_id'], 'full');
					$slide['image'] = $img[0];
					$slide['width'] = $img[1];
					$slide['height'] = $img[2];
				}
				$hide = '';
				if (empty($slide['image'])) {
					$hide = ' hide';
				}
				$font_face = $slide["attachment_id"] ? "Unknown_font" : "";
				$font_face_name = empty($slide["attachment_id"]) ? $slide['name'] : "";
				$preview_text = $slide["attachment_id"] ? "1 2 3 4 5 6 7 8 9 0 A B C D E F G H I J K L M N O P Q R S T U V W X Y Z A B C D E F G H I J K L M N O P Q R S T U V W X Y Z" : "";



				foreach ($font_list as $font_key => $font) {
					if ($font["id"] == $slide["attachment_id"]) {
						$font_face = $font["name"];
						$font_face_name = $font_key;
					}
				}

				$demo_font_preview = Custom_Font_Validator::fontface_css_creator($font_face, $slide['attachment_id']);

				echo '<div class="redux-slides-accordion-group"><fieldset class="redux-field" data-id="' . $this->field['id'] . '"><h3><span class="redux-slides-header">' . $font_face_name . '</span><span  class="redux_upload_file_name"></span></h3><div>';



				echo '<div class="screenshot' . $hide . '" '.(!empty($slide["attachment_id"]) ? 'style="
    vertical-align: middle;
    display: table;
"' : '').'>';
				echo '<a class="of-uploaded-image" href="' . $slide['image'] . '">';
				echo '<img class="redux-slides-image" id="image_image_id_' . $x . '" src="' . $slide['thumb'] . '" alt="" target="_blank" rel="external" />';
				echo '</a>';
				echo '<div id="preview_' . $font_face . '" style="
    vertical-align: middle;
    display: table-cell;
    padding-left: 22px;
    font-size: 25px;
">' . $demo_font_preview['demo_text'] . '</div>';
				echo '</div>';

				echo '<div class="redux_slides_add_remove">';

				echo '<span class="button media_upload_font_button" id="add_' . $x . '">' . esc_html__('Upload', 'wpdaddy_core') . '</span>';

				$hide = '';
				if (empty($slide['image']) || $slide['image'] == '') {
					$hide = ' hide';
				}

				echo '<span class="button remove-image' . $hide . '" id="reset_' . $x . '" rel="' . $slide['attachment_id'] . '">' . esc_html__('Remove', 'wpdaddy_core') . '</span>';

				echo '</div>' . "\n";

				echo '<ul id="' . $this->field['id'] . '-ul" class="redux-slides-list">';

				echo '<li><input type="hidden" class="slide-sort" name="' . $this->field['name'] . '[' . $x . '][sort]' . $this->field['name_suffix'] . '" id="' . $this->field['id'] . '-sort_' . $x . '" value="' . $slide['sort'] . '" />';
				echo '<li><input type="hidden" class="upload-id" name="' . $this->field['name'] . '[' . $x . '][attachment_id]' . $this->field['name_suffix'] . '" id="' . $this->field['id'] . '-image_id_' . $x . '" value="' . $slide['attachment_id'] . '" />';
				echo '<input type="hidden" class="upload-thumbnail" name="' . $this->field['name'] . '[' . $x . '][thumb]' . $this->field['name_suffix'] . '" id="' . $this->field['id'] . '-thumb_url_' . $x . '" value="' . $slide['thumb'] . '" readonly="readonly" />';
				echo '<input type="hidden" class="library-filter" data-lib-filter="' . $libFilter . '" />';

				echo '<input type="hidden" class="upload" name="' . $this->field['name'] . '[' . $x . '][image]' . $this->field['name_suffix'] . '" id="' . $this->field['id'] . '-image_url_' . $x . '" value="' . $slide['image'] . '" readonly="readonly" />';
				echo '<input type="hidden" class="upload-height" name="' . $this->field['name'] . '[' . $x . '][height]' . $this->field['name_suffix'] . '" id="' . $this->field['id'] . '-image_height_' . $x . '" value="' . $slide['height'] . '" />';
				echo '<input type="hidden" class="upload-width" name="' . $this->field['name'] . '[' . $x . '][width]' . $this->field['name_suffix'] . '" id="' . $this->field['id'] . '-image_width_' . $x . '" value="' . $slide['width'] . '" /></li>';
				echo '<li><a href="javascript:void(0);" class="button deletion redux-slides-remove">' . esc_html__('Delete', 'wpdaddy_core') . '</a></li>';
				echo '</ul></div></fieldset></div>';
				$x ++;
			}
			echo '</div><a href="javascript:void(0);" class="button redux-slides-add button-primary" rel-id="' . $this->field['id'] . '-ul" rel-name="' . $this->field['name'] . '[title][]' . $this->field['name_suffix'] . '">' . sprintf(__('Add %s', 'wpdaddy_core'), $this->field['content_title']) . '</a><br/><br/>';
		}

		/**
		 * Enqueue Function.
		 *
		 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
		 *
		 * @since       1.0.0
		 * @access      public
		 * @return      void
		 */
		public function enqueue() {
			if (function_exists('wp_enqueue_media')) {
				wp_enqueue_media();
			} else {
				wp_enqueue_script('media-upload');
			}

			if ($this->parent->args['dev_mode']) {
				wp_enqueue_style('redux-field-media-css');

				wp_enqueue_style(
						  'redux-field-slides-css', ReduxFramework::$_url . 'inc/fields/slides/field_slides.css', array (), time(), 'all'
				);
			}
			wp_enqueue_style(
					  'redux-field-slides-css1', $this->extension_url . 'field_custom_field.css', array (), time(), 'all'
			);

			wp_enqueue_script(
					  'redux-field-media-js1', $this->extension_url . 'media.js', array ('jquery', 'redux-js'), time(), true
			);

			wp_enqueue_script(
					  'redux-field-slides-js2', $this->extension_url . 'field_slides.js', array ('jquery', 'jquery-ui-core', 'jquery-ui-accordion', 'jquery-ui-sortable', 'redux-field-media-js'), time(), true
			);
		}

		/**
		 * Output Function.
		 *
		 * Used to enqueue to the front-end
		 *
		 * @since       1.0.0
		 * @access      public
		 * @return      void
		 */
		public function output() {

			if ($this->field['enqueue_frontend']) {

			}
		}

	}

}
