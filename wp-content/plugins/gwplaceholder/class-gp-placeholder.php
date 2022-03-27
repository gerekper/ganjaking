<?php

class GP_Placeholder extends GWPerk {

    public $version = GP_PLACEHOLDER_VERSION;
    protected $min_perks_version = '1.2.8';
    protected $min_gravity_forms_version = '1.8';

    public $unsupported_fields = array();
    public $gf_unsupported_fields = array();
    public $gf_supports_placeholders = false;

    function init() {

        $this->unsupported_fields = apply_filters( 'gwplh_unsupported_fields',
            array( 'select', 'multiselect', 'checkbox', 'radio', 'hidden', 'html', 'section', 'page', 'fileupload', 'CAPTCHA', 'total' )
        );

        // fields that GP supports but GF does not
        $this->gf_unsupported_fields = apply_filters( 'gwplh_gf_unsupported_fields', array( 'list', 'password' ) );
        $this->gf_supports_placeholders = version_compare( GFForms::$version, '1.9.beta1.0', '>=' );

        $this->add_tooltip($this->key('placeholder'), __('<h6>Placeholder</h6> Add a short hint to aid the user to correctly fill out this field.', 'gravityperks'));

        if( $this->gf_supports_placeholders ) {
            add_filter( 'gform_field_appearance_settings_50', array( $this, 'field_settings_ui' ) );
        } else {
            add_filter( 'gform_field_standard_settings_25', array( $this, 'field_settings_ui' ) );
        }


        add_filter('gform_editor_js', array(&$this, 'field_settings_js'));

        add_filter('gform_field_input', array(&$this, 'input_html'), 10, 5);
        add_action('gform_enqueue_scripts', array(&$this, 'enqueue_scripts'));
        add_filter('gform_pre_render', array(&$this, 'pre_render'));

    }

    public function field_settings_ui() {
        ?>

        <li class="<?php echo $this->key('placeholder'); ?>_setting field_setting">

            <label for="<?php echo $this->key('placeholder'); ?>"><?php _e('Placeholder', 'gravityperks'); ?> <?php gform_tooltip($this->key('placeholder')); ?></label>

            <input type="text" id="<?php echo $this->key('placeholder'); ?>" onkeyup="SetFieldProperty('placeholder', this.value);" />

            <div id="<?php echo $this->key('inputs_container'); ?>" style="border-left:2px solid #eee;padding-left:10px;">
                <!-- inputs are generated via JS based on number of field inputs -->
            </div>

        </li>

        <?php
    }

    /**
    * Add the placeholder setting to all supported fields.
    * Load placeholder value from field into placeholder setting when the field settings are loaded.
    *
    * Note: The ID of the placeholder setting includes the perk slug; however, unlike most other perks, the value is stored
    * as simply 'placeholder'. This is because I suspect this will be the slug used by Gravity Forms when it is officially
    * implemented and then user's can simply deactivated this perk with their data already in place.
    *
    */
    public function field_settings_js() {
        
        $form_id = rgget( 'id' );
        
        ?>

        <script type="text/javascript">

        var gwplh = {
            unsupportedFields: <?php echo json_encode( $this->unsupported_fields ); ?>,
            gfUnsupportedFields: <?php echo json_encode( $this->gf_unsupported_fields ); ?>,
            doesGFSupportPlacehoders: <?php echo $this->gf_supports_placeholders ? 'true' : 'false'; ?>,
            displaySetting: false
        };

        for( i in fieldSettings ) {

            if( ! fieldSettings.hasOwnProperty( i ) ) {
                continue;
            }

            if( gwplh.doesGFSupportPlacehoders ) {
                gwplh.displaySetting = jQuery.inArray( i, gwplh.gfUnsupportedFields ) != -1;
            } else {
                gwplh.displaySetting = jQuery.inArray( i, gwplh.unsupportedFields ) == -1;
            }

            if( gwplh.displaySetting ) {
                fieldSettings[ i ] += ', .<?php echo $this->key('placeholder'); ?>_setting';
            }

        }

        jQuery( document ).ready( function( $ ) {

            // only run conversion if GF version supports placeholders
            if( ! gwplh.doesGFSupportPlacehoders ) {
                return;
            }

            $.each( form.fields, function( i, field ) {

                var inputType = GetInputType( field );

                switch( inputType ) {
                    case 'email':
                        if( field.inputs ) {
                            if( ! field.inputs[0].placeholder ) {
                                field.inputs[0].placeholder = field.placeholder;
                                field.placeholder = '';
                            }
                            if( ! field.inputs[1].placeholder ) {
                                field.inputs[1].placeholder = field.confirmationPlaceholder;
                                field.confirmationPlaceholder = '';
                            }
                        }
                        break;
                    default:
                        break;
                }

            } );

        } );

        jQuery(document).bind('gform_load_field_settings', function(event, field, form) {

            loadPlaceholderInputs();

            jQuery('select#field_address_type').change(function(){
                loadPlaceholderInputs();
            });

        });

        function loadPlaceholderInputs() {
            
            var html                   = '',
                baseKey                = '<?php echo $this->key('placeholder'); ?>',
                singleInput            = jQuery("#" + baseKey),
                inputsContainer        = jQuery("#<?php echo $this->key('inputs_container'); ?>"),
                inputType              = GetInputType( field ),
                isMultiColumnListField = inputType == 'list' && typeof field['choices'] != 'undefined' && field.choices != '';
                isMultiInputField      = jQuery.isArray( field.inputs ) || jQuery.inArray( inputType, [ 'email', 'password' ] ) != - 1 || isMultiColumnListField;

            if( isMultiInputField ) {

                singleInput.hide();
                inputsContainer.show();
                
                var inputs = [];
                
                // add custom inputs for fields that support a confirmation input (confirmation inputs are not stored in field.inputs array)
                switch( inputType ) {
                case 'password':
                    inputs.push( { 
                        id: field.id, 
                        label: '<?php echo apply_filters( "gform_password_{$form_id}", apply_filters( 'gform_password', __( 'Enter Password', 'gravityforms' ), $form_id ), $form_id ); ?>', 
                        placeholder: field.placeholder,
                        onkeyup: 'SetFieldProperty( \'placeholder\', this.value );'
                        } );
                    inputs.push( { 
                        id: field.id + '_2', 
                        label: '<?php echo apply_filters( "gform_password_confirm_{$form_id}", apply_filters( 'gform_password_confirm', __( 'Confirm Password', 'gravityforms' ), $form_id ), $form_id ); ?>', 
                        placeholder: field.confirmationPlaceholder,
                        onkeyup: 'SetFieldProperty( \'confirmationPlaceholder\', this.value);'
                        } );
                    break;
                case 'email':
                    inputs.push( { 
                        id: field.id, 
                        label: '<?php echo apply_filters( "gform_email_{$form_id}", apply_filters( 'gform_email', __( 'Enter Email', 'gravityforms' ), $form_id ), $form_id ); ?>', 
                        placeholder: field.placeholder,
                        onkeyup: 'SetFieldProperty( \'placeholder\', this.value );'
                        } );
                    inputs.push( { 
                        id: field.id + '_2', 
                        label: '<?php echo apply_filters( "gform_email_confirm_{$form_id}", apply_filters( 'gform_email_confirm', __( 'Confirm Email', 'gravityforms' ), $form_id ), $form_id ); ?>', 
                        placeholder: field.confirmationPlaceholder,
                        onkeyup: 'SetFieldProperty( \'confirmationPlaceholder\', this.value);'
                        } );
                    break;
                case 'list':
                    for( var i = 0; i < field.choices.length; i++ ) {
                        inputs.push( {
                            id: field.id + '_' + ( i + 1 ),
                            label: field.choices[i].text,
                            placeholder: field.choices[i].placeholder,
                            onkeyup: 'field.choices[' + i + '][\'placeholder\'] = this.value; console.log( field.choices[' + i + '][\'placeholder\'], this.value );'
                        } );
                    }
                    break;
                default:
                    inputs = field.inputs;
                }
                
                for( var i in inputs ) {
                    
                    var input       = inputs[i],
                        placeholder = gperk.isUndefined( input.placeholder ) ? '' : input.placeholder,
                        onkeyup     = gperk.isUndefined( input.onkeyup ) ? 'gperk.setInputProperty(' + input.id + ', \'placeholder\', this.value);' : input.onkeyup;
                        
                    html += '<label for="' + baseKey + '_' + input.id + '" style="width:140px;float:left;padding:2px;">' + input.label + '</label>';
                    html += '<input type="text" id="' + baseKey + '_' + input.id + '" value="' + placeholder + '" onkeyup="' + onkeyup + '" style="margin-top:0;" /><div style="clear:both;"></div>';
                }

                inputsContainer.html( html );

            } else {
                
                inputsContainer.hide();
                singleInput.show();

                singleInput.val( field['placeholder'] );
                
            }

        }

        </script>

        <?php
    }

    public function input_html( $input, $field, $value, $lead_id, $form_id ) {

        $input_type = RGFormsModel::get_input_type($field);

        if( $this->gf_supports_placeholders ) {
            $is_applicable_input_type = in_array( $input_type, $this->gf_unsupported_fields );
        } else {
            $is_applicable_input_type = ! in_array( $input_type, $this->unsupported_fields );
        }

        if( ! $is_applicable_input_type || ! self::has_placeholder( $field ) ) {
            return $input;
        }

        remove_filter( 'gform_field_input', array( $this, 'input_html' ) );

        $input_html = GFCommon::get_field_input($field, $value, $lead_id, $form_id, GFAPI::get_form( $form_id ) );
        $inputs = rgar( $field, 'inputs' );
        $has_confirm_field = in_array( $input_type, array( 'email', 'password' ) );

        $is_list_field = $input_type == 'list';
        $is_multi_column_list_field = $is_list_field && is_array( rgar( $field, 'choices' ) );

        if( is_array( $inputs ) || $has_confirm_field || $is_multi_column_list_field ) {
            
            // add custom inputs for fields that support a confirmation input (confirmation inputs are not stored in field.inputs array)
            if( $has_confirm_field ) {

                $inputs = array();

                $inputs[] = array(
                    'id' => $field['id'], 
                    'placeholder' => rgar( $field, 'placeholder' )
                    );
                $inputs[] = array(
                    'id' => $field['id'] . '_2',
                    'placeholder' => rgar( $field, 'confirmationPlaceholder' )
                    );

            } else if( $is_list_field ) {

                $inputs = array();

                foreach( $field['choices'] as $index => $choice ) {
                    $inputs[] = array(
                        'id' => $field['id'] . '_' . ( $index + 1 ),
                        'placeholder' => $choice['placeholder']
                    );
                }

            }

	        if( $is_list_field ) {

		        $search = "/(name='input_{$field['id']}\[\]')/";
		        preg_match_all( $search, $input_html, $matches, PREG_OFFSET_CAPTURE );

		        foreach( $matches[0] as $index => $match ) {
			        $input_html = preg_replace( $search, sprintf( 'GPPLACEHOLDER_%d', $index ), $input_html, 1 );
		        }

		        foreach( $matches[0] as $index => $match ) {
			        $replace = "$match[0] placeholder='{$inputs[ $index ]['placeholder']}'";
			        $input_html = preg_replace( sprintf( '/GPPLACEHOLDER_%d/', $index ), $replace, $input_html, 1 );
		        }

	        } else {

		        foreach( $inputs as $input ) {

			        $placeholder = esc_html( rgar( $input, 'placeholder' ) );
			        if( ! $placeholder )
				        continue;

			        $search = "name='input_{$input['id']}'";
			        $replace = $search . " placeholder='{$placeholder}'";
			        $input_html = str_replace( $search, $replace, $input_html );

		        }

	        }

        } else {
            
            $placeholder = esc_html( rgar( $field, 'placeholder' ) );

            if( $is_list_field ) {
                $search = "/(name='input_{$field['id']}\[\]')/";
                $replace = "$1 placeholder='{$placeholder}'";
                $input_html = preg_replace( $search, $replace, $input_html, 1 );
            } else {
                $search = "name='input_{$field['id']}'";
                $replace = $search . " placeholder='{$placeholder}'";
                $input_html = str_replace( $search, $replace, $input_html );
            }


        }

        add_filter('gform_field_input', array(&$this, 'input_html'), 10, 5);

        return $input_html;
    }

    public function enqueue_scripts($form) {

        foreach($form['fields'] as $field) {
            if(!in_array(RGFormsModel::get_input_type($field), $this->unsupported_fields) && rgar($field, 'placeholder')) {
                wp_enqueue_script('gperk_placeholder', $this->get_base_url() . '/scripts/jquery.placeholder.min.js', array('jquery'));
                GWPerk::register_noconflict_script('gperk_placeholder');
                GFFormDisplay::add_init_script($form['id'], 'gperk_placeholder', GFFormDisplay::ON_PAGE_RENDER, "jQuery('input[placeholder], textarea[placeholder]').placeholder();");
                break;
            }
        }

    }

    public function pre_render($form) {

        if(!wp_script_is('gperk_placeholder', 'queue'))
            return $form;

        ?>

        <style type="text/css">
        .gform_wrapper .placeholder { color: #aaa; }
        </style>

        <?php
        return $form;
    }

    public static function has_placeholder($field) {
        if( is_array( rgar( $field, 'inputs' ) ) ) {
            $inputs = rgar( $field, 'inputs' );
            foreach( $inputs as $input ) {
                if( rgar( $input, 'placeholder' ) )
                    return true;
            }
        } else if( GFFormsModel::get_input_type( $field ) == 'list' && is_array( rgar( $field, 'choices' ) ) ) {
            $choices = rgar( $field, 'choices' );
            foreach( $choices as $choice ) {
                if( rgar( $choice, 'placeholder' ) )
                    return true;
            }
        } else {
            return rgar( $field, 'placeholder' ) == true;
        }
    }

    function documentation() {
        ob_start();
        ?>

# What does it do?

The **Placeholder** perk allows you to add HTML5 placeholders to your Gravity Form fields. What are placeholders? They're short hints that
display inline in form input fields (and textareas) that help users fill out the fields with the correct data. They look like this:

<img src="<?php echo self::get_base_url(); ?>/images/front-end-example.png" alt="Placeholders" />

Once the user starts filling the fields out, it would look like this:

<img src="<?php echo self::get_base_url(); ?>/images/front-end-example-filled-out.png" alt="Placeholders: Partially Filled In" />

# How does it work?

This perk adds a new field setting for fields that support the placeholder property. More on that below. After you've specified the
placeholder for a field, the **Placeholder** perk handles updating the field's input HTML to include the placeholder attribute. For
browsers that support placeholders (like Firefix, Chrome and Safari), this functionality will work natively. That means no special scripts!

But alas, not all browsers support placeholders just yet. For these browsers (like Internet Explorer), the **Placeholder** perk loads
a little <a href="https://github.com/danielstocks/jQuery-Placeholder" target="_blank">jQuery Placeholder</a> script by
<a href="http://webcloud.se" target="_blank">Daniel Stocks</a>. This script adds support for the placeholder attribute to older browsers
so regardless of the browser, the user receives a uniform experience.

# How do I enable this functionality?

First step, as always, is to activate the **Placeholder** perk. Once activated, supported field types on all forms will automatically
display the "Placeholder" setting right below the "Field Label" setting.

<img src="<?php echo self::get_base_url(); ?>/images/placeholder-setting.png" alt="Placeholders: Settings" />

For single input fields (like the *Single Line Text* field and the *Paragraph Field*), there is only a single input for the Placeholder
setting. For multi-input fields (like the *Name* field and the *Adddress* field), there will be an input to specify the placeholder for
each input of that field type.

<img src="<?php echo self::get_base_url(); ?>/images/placeholder-setting-multi-input.png" alt="Placeholders: Settings Multi-input" />

Enter whatever text you would like to appear as the placeholder for each field on your form. Save your changes and you're good to go!

# Anything else I need to know?

Nope! That's pretty much it. If you have any questions on this functionality or just want to say how much you love it, make sure you
come back to <a href="<?php echo $this->data['AuthorURI'] ?>" target="_blank">GravityWiz.com</a> and leave us a comment.

<a href="<?php echo $this->data['PluginURI'] ?>" target="_blank">Visit this Perk's Home Page</a>

        <?php
        return ob_get_clean();
    }

}

class GWPlaceholder extends GP_Placeholder { }