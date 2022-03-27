<?php

class GP_Expand_Text_Areas extends GWPerk {

	public static $version_info;

	protected $min_gravity_perks_version = '2.2.4';

	public function init() {

		$this->enqueue_field_settings();

	}

	public function settings() {

		echo self::generate_checkbox($this, array(
			'id'          => 'all_textareas',
			'label'       => __( 'Add expand option for all textareas', 'gravityperks' ),
			'description' => __( 'By default the "Expand Textarea" button is only added to a few much needed textareas.', 'gravityperks' ),
		));

		echo self::generate_checkbox($this, array(
			'id'          => 'expand_on_focus',
			'label'       => __( 'Expand textarea when you click inside the textarea', 'gravityperks' ),
			'description' => __( 'By default, you must click the "Expand Textarea" button to expand the textarea.', 'gravityperks' ),
		));

	}

	public function register_settings( $perk ) {
		return array( 'all_textareas', 'expand_on_focus' );
	}

	public function field_settings_js() {

		$settings = self::get_perk_settings( $this->slug );

		?>

		<style type="text/css">
			.field_setting textarea { position: relative; z-index: 2; }
			.gwp-expand { margin: -4px 0 0 5px; position: relative; z-index: 1; }
			.gwp-expand a { background-color: #f7f7f7; font-size: 12px;
				color: #555 !important; cursor: pointer; padding: 5px 10px; border: 1px solid #ecedf8; border-top: 0;
				display: -moz-inline-stack; display: inline-block; border-radius: 0 0 3px 3px; }
			.gwp-expand a:hover { background-color: #f6f9fc; }
			.gwp-expand span.dashicons { width: 15px; height: 15px; font-size: 14px; display: inline-block; margin: 0 0 0 4px; }

            #textarea_modal .gf_calculation_buttons { margin: 0; }

            body:not(.gf-legacy-ui) #textarea_modal .gf_calculation_buttons {
                margin: 0 0 5px;
                width: 230px;
                float: right;
                border-radius: 5px;
            }

            body:not(.gf-legacy-ui) #textarea_modal #textarea_controls:not(:empty) + textarea {
                height: 250px !important;
            }

            .gf-legacy-ui #textarea_modal { margin: 15px 0 0; }
            .gf-legacy-ui #textarea_modal textarea { height: 300px !important }
		</style>

		<script type="text/javascript">

		var gwpActiveTextarea = false;
		var gwpDefaultTextareas = '#field_calculation_formula, #field_post_content_template, #field_content';

		var gwpAllTextareas = '<?php echo  $this->get_setting( 'all_textareas', $settings ) ? true : false; ?>';
		var gwpExpandOnFocus = '<?php echo $this->get_setting( 'expand_on_focus', $settings ) ? true : false; ?>';

		jQuery(document).ready(function($){
			var applyTo = gwpAllTextareas ? 'li.field_setting textarea:not( .mm_tooltip_text textarea )' : gwpDefaultTextareas;
			$(applyTo).each(function() {
				if(gwpExpandOnFocus) {
					$(this).focus(function(){
						loadTextareaModal($(this).attr('id'));
					});
				}
				else {
					if( $( this ).hasClass( 'merge-tag-support' ) ) {
						$( this ).next().after('<div class="gwp-expand"><a onclick="loadTextareaModal(\'' + $(this).attr('id') + '\');"><?php _e( 'Expand Textarea', 'gravityforms' ); ?><span class="dashicons dashicons-external"></span></a></div>');
					} else {
						$( this ).after('<div class="gwp-expand"><a onclick="loadTextareaModal(\'' + $(this).attr('id') + '\');"><?php _e( 'Expand Textarea', 'gravityforms' ); ?><span class="dashicons dashicons-external"></span></a></div>');
					}
				}
			});
		});

		function loadTextareaModal(textareaId) {

			var textarea = jQuery('#' + textareaId);
			var labelText = jQuery('label[for="' + textarea.attr('id') + '"]').text().replace('(?)', '').trim();
			var field = GetSelectedField();

			gwpActiveTextarea = textarea;

			// only add controls for supported textareas
			if(jQuery.inArray(textareaId, gwpDefaultTextareas.split(', '))) {

				// get control elements
				switch(textareaId) {
				case 'field_content':
					var controls = textarea.prev('select').wrap('<div></div>');
				default:
					var controls = textarea.prev('div').clone();
				}

				// loop through control selects/inputs and update by ID
				controls.find('select, input').each(function() {
					var elem = jQuery(this);
					switch(elem.attr('id')) {

					// post content field
					case 'field_post_content_template_variable_select':
						elem.attr('id', 'modal_textarea_variable_select');
						elem.attr('onchange', "InsertPostContentVariable('modal_textarea', '');");
						break;

					// post content field (images)
					case 'field_post_content_template_image_size_select':
						elem.attr('id', 'modal_textarea_image_size_select');
						elem.attr('onchange', "InsertPostImageVariable('modal_textarea', '');");
						break;

					// html field, calc field
					case 'field_content_variable_select':
					case 'field_calculation_formula_variable_select':
						elem.attr('id', 'modal_textarea_variable_select');
						elem.attr('onchange', "InsertVariable('modal_textarea', '');");
						break;

					default:
						elem.attr('onclick', "InsertVariable('modal_textarea', '', this.value);");
					}
				});

				jQuery('#textarea_controls').html(controls);

			}

			jQuery('#modal_textarea').val(textarea.val());

			tb_show(field.label + ' : Field ID ' + field.id + ' : ' + labelText, '#TB_inline?inlineId=textarea_modal_container', '');

			setTimeout('jQuery("#modal_textarea").focus();', 1);

		}

		function insertTextareaValue() {
			var value = jQuery('#modal_textarea').val();
			gwpActiveTextarea.val( value )
				.trigger( 'blur' )
				.trigger( 'change' )
				.trigger( 'propertychange' )
				.trigger( 'keyup' );
			gwsTbRemove();
		}

		function gwsTbRemove() {
			tb_remove();
		}

		// GF's InsertVariable() will attempt to pull value from dummy select, populate value into our dummy select for GF
		function gwsSetModalSelect(elem) {
			elem = jQuery(elem);
			jQuery('#modal_textarea_variable_select').val(elem.val());
			elem.val(elem.find('option:first').val());
		}

		</script>

		<div id="textarea_modal_container" style="display:none;">

			<div id="textarea_modal">
				<div id="textarea_controls" style="margin: 0 0 2px;"></div>
				<textarea id="modal_textarea" style="width:100%;height:275px;"></textarea>

                <div class="modal_footer">
                    <div class="panel-buttons" style="">
                        <button class="button button-primary" onclick="insertTextareaValue();">Insert</button>
                        <button class="button" onclick="gwsTbRemove();">Cancel</button>&nbsp;
                    </div>
                </div>
			</div>

		</div>

		<?php
	}

}

class GWExpandTextareas extends GP_Expand_Text_Areas { }
