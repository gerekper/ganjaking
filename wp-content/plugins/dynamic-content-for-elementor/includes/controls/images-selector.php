<?php

namespace DynamicContentForElementor\Controls;

use Elementor\Base_Data_Control;
use Elementor\Controls_Manager;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
/**
 * Elementor choose control.
 *
 * A base control for creating choose control. Displays radio buttons styled as
 * groups of buttons with icons for each option.
 *
 * @since 1.0.0
 */
class Control_Images_Selector extends Base_Data_Control
{
    public function get_type()
    {
        return 'images_selector';
    }
    public function enqueue()
    {
        wp_register_script('images-selector-control', plugins_url('/assets/js/images-selector-control.js', DCE__FILE__), ['jquery'], DCE_VERSION);
        wp_enqueue_script('images-selector-control');
    }
    public function content_template()
    {
        $control_uid = $this->get_control_uid('{{value}}');
        ?>
		<div class="elementor-control-field">
			<label class="elementor-control-title">{{{ data.label }}}</label>

			<# if ( data.description ) { #>
			<div class="elementor-control-field-description">{{{ data.description }}}</div>
			<# } #>

			<div class="elementor-control-input-wrapper">
				<div class="elementor-imageselector elementor-imageselector-type-{{{ data.type_selector }}}">
					<# _.each( data.options, function( options, value ) {
					var valueItem = value;

					if( (data.return_val == 'image' && options.return_val != 'val') ||  options.return_val == 'image'){
						valueItem = options.image;
					}else if( (data.return_val == 'icon' && options.return_val != 'val') || options.return_val == 'icon' ){
						valueItem = options.icon;
					}
					imageItem = options.image;

					if(options.image_preview){
						imageItem = options.image_preview;
					}
					#>
					<div class="elementor-imageselector-item elementor-imageselector-column-{{{ columns_grid }}}" data-column-grid="{{{ columns_grid }}}">
						<input id="<?php 
        echo $control_uid;
        ?>" type="radio" name="elementor-imageselector-{{ data.name }}-{{ data._cid }}" value="{{ valueItem }}">
						<label class="elementor-imageselector-label elementor-control-unit-1 tooltip-target" for="<?php 
        echo $control_uid;
        ?>" data-tooltip="{{ options.title }}" title="{{ options.title }}">
							<# if( data.type_selector == 'icon' ){ #>
							<i class="{{ options.icon }}" aria-hidden="true"></i>
							<# }else if( data.type_selector == 'image' ){ #>
							<img src="{{ imageItem }}" />
							<# }else if( data.type_selector == 'bgimage' ){ #>
							<div class="elementor-imageselector-bgimage" style="background-image:url({{ imageItem }};" />
							<# } #>
							<span class="elementor-screen-only">{{{ options.title }}}</span>
						</label>
					</div>
					<# } ); #>
				</div>
			</div>
		</div>
		<?php 
    }
    protected function get_default_settings()
    {
        return ['label_block' => \true, 'columns_grid' => 3, 'type_selector' => 'image', 'return_val' => 'image', 'options' => [], 'toggle' => \true];
    }
}
