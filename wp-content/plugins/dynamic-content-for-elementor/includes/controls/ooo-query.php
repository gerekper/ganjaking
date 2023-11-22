<?php

namespace DynamicContentForElementor\Controls;

use Elementor\Control_Select2;
use Elementor\Modules\DynamicTags\Module as TagsModule;
if (!\defined('ABSPATH')) {
    exit;
    // Exit if accessed directly.
}
/**
 * Elementor Control Query
 */
class Control_OOO_Query extends Control_Select2
{
    /**
     * Get control type.
     *
     * @since 1.6.0
     * @access public
     *
     * @return string Control type.
     */
    public function get_type()
    {
        return 'ooo_query';
    }
    /**
     * Get ooo control default settings.
     *
     * Retrieve the default settings of the text control. Used to return the
     * default settings while initializing the text control.
     *
     * @since 1.0.0
     * @access protected
     *
     * @return array Control default settings.
     */
    protected function get_default_settings()
    {
        return ['dynamic' => ['active' => \true, 'categories' => [TagsModule::BASE_GROUP, TagsModule::TEXT_CATEGORY, TagsModule::NUMBER_CATEGORY]]];
    }
    /**
     * Render select2 control output in the editor.
     *
     * Used to generate the control HTML in the editor using Underscore JS
     * template. The variables for the class are available using `data` JS
     * object.
     *
     * @since 1.0.0
     * @access public
     */
    public function content_template()
    {
        $control_uid = $this->get_control_uid();
        ?>
		<div class="elementor-control-field">
			<# if ( data.label ) {#>
				<label for="<?php 
        echo $control_uid;
        ?>" class="elementor-control-title">{{{ data.label }}}</label>
			<# } #>
			<div class="elementor-control-input-wrapper elementor-control-dynamic-switcher-wrapper">
				<# var multiple = ( data.multiple ) ? 'multiple' : ''; #>
				<select id="<?php 
        echo $control_uid;
        ?>" class="elementor-select2 elementor-control-tag-area" type="select2" {{ multiple }} data-setting="{{ data.name }}">
					<# _.each( data.options, function( option_title, option_value ) {
						var value = data.controlValue;
						if ( typeof value == 'string' ) {
							var selected = ( option_value === value ) ? 'selected' : '';
						} else if ( null !== value ) {
							var value = _.values( value );
							var selected = ( -1 !== value.indexOf( option_value ) ) ? 'selected' : '';
						}
						#>
					<option {{ selected }} value="{{ option_value }}">{{{ option_title }}}</option>
					<# } ); #>
				</select>
			</div>
		</div>
		<# if ( data.description ) { #>
			<div class="elementor-control-field-description">{{{ data.description }}}</div>
		<# } #>
		<?php 
    }
}
