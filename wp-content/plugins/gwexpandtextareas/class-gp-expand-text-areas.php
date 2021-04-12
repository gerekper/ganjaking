<?php 

class GP_Expand_Text_Areas extends GWPerk {

    public static $version_info;

    public function init() {

        $this->enqueue_field_settings();

    }

    public function settings() {

        echo self::generate_checkbox($this, array(
            'id' => 'all_textareas',
            'label' => __('Add expand option for all textareas', 'gravityperks'),
            'description' => __('By default the "Expand Textarea" button is only added to a few much needed textareas.', 'gravityperks')
            ));

        echo self::generate_checkbox($this, array(
            'id' => 'expand_on_focus',
            'label' => __('Expand textarea when you click inside the textarea', 'gravityperks'),
            'description' => __('By default, you must click the "Expand Textarea" button to expand the textarea.', 'gravityperks')
            ));

    }

    public function register_settings($perk) {
        return array("all_textareas", "expand_on_focus");
    }

    public function field_settings_js() {

        $settings = self::get_perk_settings($this->slug);

        ?>

        <style type="text/css">
            .field_setting textarea { position: relative; z-index: 2; }
            .gwp-expand { margin: -4px 0 0 5px; position: relative; z-index: 1; }
            .gwp-expand a { background-color: #f7f7f7; font-size: 11px;
                color: #999 !important; cursor: pointer; padding: 5px 10px; border: 1px solid #efefef; border-top: 0;
                display: -moz-inline-stack; display: inline-block; zoom: 1; *display: inline; border-radius: 0 0 3px 3px; }
            .gwp-expand a:hover { color: #21759B !important; background-color: #efefef; border-color: #dedede; }
            .gwp-expand span.icon { background-image: url(https://s3.amazonaws.com/gwsimages/expand.png); width: 15px; height: 15px;
                display: -moz-inline-stack; display: inline-block; zoom: 1; *display: inline; margin: 0 0 -2px 8px; }
            #textarea_modal { margin: 15px 0 0; }
            #textarea_modal .gf_calculation_buttons { margin: 0; }
        </style>

        <script type="text/javascript">

        var gwpActiveTextarea = false;
        var gwpDefaultTextareas = '#field_calculation_formula, #field_post_content_template, #field_content';

        var gwpAllTextareas = '<?php echo  $this->get_setting('all_textareas', $settings) ? true : false; ?>';
        var gwpExpandOnFocus = '<?php echo $this->get_setting('expand_on_focus', $settings) ? true : false; ?>';

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
                        $( this ).next().after('<div class="gwp-expand"><a onclick="loadTextareaModal(\'' + $(this).attr('id') + '\');"><?php _e("Expand Textarea", "gravityforms") ?><span class="icon"></span></a></div>');
                    } else {
                        $( this ).after('<div class="gwp-expand"><a onclick="loadTextareaModal(\'' + $(this).attr('id') + '\');"><?php _e("Expand Textarea", "gravityforms") ?><span class="icon"></span></a></div>');
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
                <textarea id="modal_textarea" style="width:100%;height:315px;margin:0 0 15px;"></textarea>
                <div style="text-align: right;">
                    <button class="button" onclick="gwsTbRemove();">Cancel</button>
                    <button class="button button-primary" onclick="insertTextareaValue();">Insert</button>
                </div>

            </div>

        </div>

        <?php
    }

    public function documentation() {
        ob_start();
        ?>

# What does it do?

The **Expand Editor Textareas** perk makes working with textareas in the Gravity Forms Form Editor much easier by launching an
expanded textarea in a modal window. The expanded textarea is much larger, increasing the readability and ease-of-use
when working with larger amounts of content.

# How does it work?

By default, this perk adds an "Expand Textarea" button to specific set of field setting textareas that would
most benefit from having the expanded textarea (specifially the "HTML Content", the Calcuation "Formula", and
the Post Body "Content Template" textareas). Clicking the "Expand Textarea" button launches the Expand Textareas
modal for easy editing.

# How do I enable this functionality?

Enabling the default functionality is very easy. Simply install and activate the
**Expand Textareas** perk and "Expand Textarea" button will appear directly below the default textareas.
Clicking this button will launch the textarea in a modal window. Add or modify the textarea content and click
the "Insert" button to preserve your changes to the textarea.

![Expand Textareas Button](<?php echo $this->get_base_url(); ?>/images/expand-textareas.png)

<div class="notice"><b>Note</b>: Additional configuration options are available for this perk.</div>

## Additional Settings

There are a couple additional options that can be configured from this perk's "Settings" page. This perk's
global settings are available by navigating to the "[Manage Perks](<?php echo GW_MANAGE_PERKS_URL; ?>)" page,
finding the **GP Expand Editor Textareas** perk on this page, and click in the "Settings" link below the perk title.

### Add expand option for all textareas

By default the "Expand Textarea" button is only added to a few much needed textareas. Enable this option to
make the "Expand Textarea" button appear for all Form Editor textareas.

### Expand textarea when you click inside the textarea

By default, you must click the "Expand Textarea" button to expand the textarea. Enable this option to
automatically launch the expanded textarea modal when you click on the textarea to edit.

![Expand Textareas Settings](<?php echo $this->get_base_url(); ?>/images/expand-textarea-settings.png)

# Anything else I need to know?

Nope! That's pretty much it. If you have any questions on this functionality or just want to say how much you love it, make sure you
come back to [GravityWiz.com](<?php echo $this->data['AuthorURI'] ?>) and leave us a comment.

[Visit this Perk's Home Page](<?php echo $this->data['PluginURI'] ?>)
        <?php
        return ob_get_clean();
    }
}

class GWExpandTextareas extends GP_Expand_Text_Areas { }