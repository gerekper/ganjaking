<?php
/**
 * Image Selector Control Class
 *
 * @package Happy_Addons_Pro
 */
namespace Happy_Addons_Pro\Controls;

use \Elementor\Base_Data_Control;

class Image_Selector extends Base_Data_Control {

    /**
     * Control identifier
     */
    const TYPE = 'ha-image-selector';

	/**
	 * Set control type.
	 */
	public function get_type() {
		return self::TYPE;
	}

	/**
	 * Enqueue control scripts and styles.
	 */
	public function enqueue() {
		wp_enqueue_style(
			'hapro-editor-css',
			HAPPY_ADDONS_PRO_ASSETS.'/admin/css/editor.min.css',
			[],
			HAPPY_ADDONS_PRO_VERSION
		);
	}

	/**
	 * Set default settings
	 */
	protected function get_default_settings() {
		return [
			'label_block' => true,
			'toggle' => true,
			'col' => 3,
			'options' => [],
		];
	}

	/**
	 * control field markup
	 */
	public function content_template() {
		$control_uid = $this->get_control_uid('{{ value }}');
		?>
		<div class="elementor-control-field">
			<label class="elementor-control-title">{{{ data.label }}}</label>
			<div class="ha-control-image-selector-wrapper">
				<# _.each( data.options, function( options, value ) { #>
				<input id="<?php echo $control_uid; ?>" type="radio" name="ha-image-selector-{{ data.name }}-{{ data._cid }}" value="{{ value }}" data-setting="{{ data.name }}">
				<label class="ha-image-selector-label tooltip-target col-{{{ data.col }}}" for="<?php echo $control_uid; ?>" data-tooltip="{{ options.title }}" title="{{ options.title }}">
					<img src="{{ options.url }}" alt="{{ options.title }}">
					<span class="elementor-screen-only">{{{ options.title }}}</span>
				</label>
				<# } ); #>
			</div>
		</div>
		<# if ( data.description ) { #>
		<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php
	}

}
