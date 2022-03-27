<?php

class GF_Field_Terms_Of_Service extends GF_Field_Checkbox {

	public $type                = 'tos';
	public $default_field_props = array();

	private static $initialized = false;

	public function __construct( $data = array() ) {

		parent::__construct( $data );

		$this->default_field_props = array(
			'label'          => __( 'Terms of Service', 'gp-terms-of-service' ),
			'inputType'      => 'checkbox',
			'size'           => 'large',
			'choices'        => array(
				array(
					'text'  => __( 'I agree to the Terms of Service', 'gp-terms-of-service' ),
					'value' => __( 'I agree to the Terms of Service', 'gp-terms-of-service' ),
				),
			),
			'tosHTMLEnabled' => true,
		);

		if ( ! self::$initialized ) {
			$this->init();
		}

	}

	public function init() {

		add_filter( 'gform_gf_field_create', array( $this, 'create_as_field_type_class' ), 10, 2 );

		add_action( 'gform_field_standard_settings_25', array( $this, 'advanced_settings_html' ) );
		add_action( 'gform_editor_js', array( $this, 'editor_js' ) );
		add_action( 'gform_editor_js', array( $this, 'field_default_properties_js' ) );

		add_action( 'gform_multilingual_field_keys', array( $this, 'add_wpml_field_keys' ) );
		add_action( 'wpml_gf_register_strings_field_tos', array( $this, 'add_wpml_support' ), 10, 3 );
		add_filter( 'gform_merge_tag_filter', array( $this, 'include_terms_in_merge_tags' ), 11, 4 ); // Priority 11 to avoid GP Preview Submission overwriting modified value.

		self::$initialized = true;

	}

	/**
	 * Supported by Gravity Forms 2.5 and newer
	 *
	 * @return string Field's form editor description.
	 */
	public function get_form_editor_field_description() {
		return esc_attr__( 'Require users to scroll through terms and check a checkbox.', 'gp-terms-of-service' );
	}

	public function create_as_field_type_class( $field, $properties ) {
		if ( $field->type == 'tos' ) {
			$field = new GF_Field_Terms_Of_Service( $properties );
		}
		return $field;
	}

	public function get_form_editor_field_title() {
		return esc_attr__( 'Terms of Service', 'gp-terms-of-service' );
	}

	public function get_form_editor_button() {
		return array(
			'group' => 'advanced_fields',
			'text'  => $this->get_form_editor_field_title(),
		);
	}

	public function get_form_editor_field_settings() {
		return array(
			'label_setting',
			'description_setting',
			'admin_label_setting',
			'size_setting',
			'default_value_textarea_setting',
			'error_message_setting',
			'css_class_setting',
			'visibility_setting',
			'tos_setting',
			'choices_setting',
		);
	}

	public function is_conditional_logic_supported() {
		return true;
	}

	public function editor_js() {
		?>

		<style type="text/css">
			.gwtos-hide { }
			.gwtos-hide .gf_insert_field_choice,
			.gwtos-hide .gf_delete_field_choice,
			.gwtos-hide #gfield_settings_choices_container + input.button,
			.gwtos-hide input#field_choice_values_enabled,
			.gwtos-hide input#field_choice_values_enabled + label,
			.gwtos-hide .field-choice-handle,
			.gwtos-hide .gfield_choice_header_label,
			.gwtos-hide #gfield_settings_choices_container .gfield_choice_header_label {
				display: none !important;
			}

			.gwtos-hide #checkbox_choice_text_0 {
				width: 300px !important;
			}

			/**
			 * GF <2.5 backwards compatibility styling
			 */
			.gfield .gwtos-hide #checkbox_choice_text_0 {
				width: 388px !important;
			}
		</style>

		<script type='text/javascript'>

			var gwtos = {
				modifyChoiceEditor: function() {
					jQuery( 'li.choices_setting' ).addClass( 'gwtos-hide' );
				},
				resetChoiceEditor: function() {
					jQuery( 'li.choices_setting' ).removeClass( 'gwtos-hide' );
				}
			};

			jQuery(document).bind("gform_load_field_settings", function(event, field, form) {

				if( field.type == '<?php echo $this->type; ?>' ) {
					gwtos.modifyChoiceEditor();
					jQuery( 'li.select_all_choices_setting' ).hide();
				} else {
					gwtos.resetChoiceEditor();
				}

				jQuery("#<?php echo $this->key( 'require_scroll' ); ?>").attr("checked", field['<?php echo $this->key( 'require_scroll' ); ?>'] == true);
				jQuery("#<?php echo $this->key( 'terms' ); ?>").val(field['<?php echo $this->key( 'terms' ); ?>']);

			});

			jQuery(document).ready(function($){

				$('#<?php echo $this->key( 'terms' ); ?>').keyup(function(){
					var field = GetSelectedField();
					$('#gw_terms_' + field['id']).val($(this).val());
				});

			});

		</script>

		<?php
	}

	public function advanced_settings_html() {
		?>

		<li class="tos_setting field_setting">

			<label for="<?php echo $this->key( 'terms' ); ?>" class="section_label">
				<?php _e( 'The Terms', 'gp-terms-of-service' ); ?>
				<?php gform_tooltip( $this->key( 'terms' ) ); ?>
			</label>
			<textarea id="<?php echo $this->key( 'terms' ); ?>"
					  class="fieldwidth-3 fieldheight-2"
					  onkeyup="SetFieldProperty('<?php echo $this->key( 'terms' ); ?>', this.value);"
					  onchange="SetFieldProperty('<?php echo $this->key( 'terms' ); ?>', this.value);"></textarea>

			<div class="clear" style="margin:0 0 5px;"></div>

			<input type="checkbox" id="<?php echo $this->key( 'require_scroll' ); ?>" onclick="SetFieldProperty('<?php echo $this->key( 'require_scroll' ); ?>', this.checked);" />
			<label for="<?php echo $this->key( 'require_scroll' ); ?>" class="inline">
				<?php _e( 'Require Full Scroll', 'gp-terms-of-service' ); ?>
				<?php gform_tooltip( $this->key( 'require_scroll' ) ); ?>
			</label>

		</li>

		<?php
	}

	public function get_style_block( $form, $entry, $value ) {

		$entry_id = rgar( $entry, 'id' );

		/**
		 * Filter whether or not to output the Terms of Service Field's CSS.
		 *
		 * @since 1.3.4
		 *
		 * @param boolean $disable_css Whether or not to disable the output of the Terms of Service Field's CSS.
		 * @param \GF_Field $tos_field The current field object.
		 * @param integer $form_id The current field ID.
		 * @param integer $entry_id The current entry ID.
		 * @param mixed $value The current field value.
		 */
		$disable_css = gf_apply_filters( array( 'gptos_disable_css', $form['id'] ), false, $this, $form['id'], $entry_id, $value );

		if ( $disable_css ) {
			return '';
		}

		ob_start();
		?>

		<style type="text/css">

			/* Frontend Styles */
			.gptos_terms_container { height: 11.250em; width: 97.5%; background-color: #fff; overflow: auto; border: 1px solid #ccc; }
			.gptos_terms_container.small { width: 25%; }
			.gptos_terms_container.medium { width: 47.5%; }
			.gptos_terms_container.large { /* default width */ }
			.left_label .gptos_terms_container,
			.right_label .gptos_terms_container { margin-left: 30% !important; width: auto !important; }
			.gform_wrapper .gptos_terms_container > div { margin: 1rem !important; }
			.gform_wrapper .gptos_terms_container ul,
			.gform_wrapper .gptos_terms_container ol { margin: 0 0 1rem 1.5rem !important; }
			.gform_wrapper .gptos_terms_container ul li { list-style: disc !important; }
			.gform_wrapper .gptos_terms_container ol li { list-style: decimal !important; }
			.gform_wrapper .gptos_terms_container p { margin: 0 0 1rem; }
			.gform_wrapper .gptos_terms_container *:last-child { margin-bottom: 0; }

			.gptos_input_container { margin-top: 12px; }
			.gptos_input_container ul { padding: 0; }

			/* Admin Styles */
			#gform_fields .gptos_terms_container { background-color: rgba( 255, 255, 255, 0.5 ); border-color: rgba( 222, 222, 222, 0.75 ); }
			#gform_fields .gptos_terms_container > div { margin: 1rem !important; }
			#gform_fields .gptos_terms_container p { margin: 0 0 1rem; }
			#gform_fields .gptos_terms_container *:last-child { margin-bottom: 0; }

		</style>

		<?php
		return ob_get_clean();

	}

	public function field_default_properties_js() {
		?>

		<script type="text/javascript">

			function SetDefaultValues_<?php echo $this->type; ?>(field) {

				var defaultFieldProps = <?php echo json_encode( $this->default_field_props ); ?>;
				for(var key in defaultFieldProps) {
					if( defaultFieldProps.hasOwnProperty( key ) ) {
						field[ key ] = defaultFieldProps[ key ];
					}
				}

				field.inputs = [];
				var skip = 0;

				// populate 'inputs' property for checkboxes
				for(var i = 0; i < field.choices.length; i++) {

					// skipping ids that are multiple of ten to avoid conflicts with other fields (i.e. 5.1 and 5.10)
					if((i + 1 + skip) % 10 == 0)
						skip++;

					var field_number = field.id + '.' + (i + 1 + skip);
					field.inputs.push(new Input(field_number, field.choices[i].text));

				}

				return field;
			}

		</script>

		<?php
	}

	public function get_field_input( $form, $value = '', $entry = null ) {

		$terms_container = $this->get_terms_container_tag( $this );
		$disabled        = $this->is_form_editor() || GP_Perk::doing_ajax( 'rg_add_field' ) ? "disabled='disabled'" : '';

		if ( $this->is_entry_detail() ) {

			$terms = '';

		} else {

			$classes = array( rgar( $this, 'cssClass' ), $this->size );
			$terms   = $this->get_terms( $form );

			if ( ! $this->is_html_enabled( $this ) ) {

				$tabindex  = GFCommon::get_tabindex();
				$classes[] = 'textarea';
				$readonly  = "readonly='readonly'";

				$terms = sprintf(
					"<div class='ginput_container'><textarea $disabled $readonly $tabindex id='gw_terms_%d' class='%s' rows='10' cols='50'>%s</$terms_container></div>",
					$this['id'],
					esc_attr( implode( ' ', $classes ) ),
					$terms
				);

			} else {

				if ( $this->is_form_editor() ) {
					$terms  = sprintf( '<p>%s</p>', __( 'Terms will appear here when you preview the form.', 'gp-terms-of-service' ) );
					$terms .= '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc quis lorem sed justo aliquam maximus at ac lectus. Phasellus feugiat dictum tellus a consequat. Vivamus ultricies elementum auctor. Quisque ac molestie elit. Praesent quis pulvinar diam. Donec lorem tortor, vulputate et erat eu, convallis sodales est. Maecenas a urna sagittis, pretium urna ac, dignissim ligula. Morbi nec nibh ac ante interdum vulputate quis sed est. Vivamus nec sagittis nulla.</p><p>Donec aliquet vulputate hendrerit. Curabitur mattis, tellus quis iaculis consectetur, massa quam accumsan nibh, sit amet vestibulum ligula eros ut orci. Sed gravida accumsan quam, ac viverra lacus venenatis id. Phasellus laoreet id ante vitae luctus. Praesent sed leo lectus. Integer vel eros auctor, vestibulum mauris et, sollicitudin ligula. Praesent faucibus dictum purus, at vestibulum nibh sollicitudin ullamcorper. Mauris nibh est, placerat non odio eget, imperdiet tempus est. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Aenean facilisis nibh at urna efficitur faucibus. Praesent lacinia erat ipsum, a pharetra tellus rhoncus ut. Suspendisse sollicitudin vel ligula ac egestas. Ut porttitor a enim id condimentum. In dolor nulla, consequat sit amet finibus vitae, fermentum sed sem.</p>';
				}

				$entry_id                = rgar( $entry, 'id' );
				$disable_auto_formatting = gf_apply_filters( 'gptos_disable_auto_formatting', array( $form['id'], sprintf( '%s_%s', $form['id'], $this->id ) ), false, $this, $form['id'], $entry_id, $value );
				if ( ! $disable_auto_formatting ) {
					$terms = wpautop( $terms );
				}

				array_push( $classes, 'gptos_terms_container', 'gwtos_terms_container' );

				$tabindex = GFCommon::$tab_index ? GFCommon::$tab_index++ : 0;

				$terms = sprintf(
					"<div id='gw_terms_%d' class='%s' tabindex='%d'>" .
					"<div class='gptos_the_terms'>%s</div>" .
					'</div>',
					$this->id,
					esc_attr( implode( ' ', array_filter( $classes ) ) ),
					$tabindex,
					$terms
				);

				$terms .= $this->get_style_block( $form, $entry_id, $value );

			}
		}

		$tos_visible = ! GFFormsModel::is_field_hidden( $form, $this, array(), $entry ) && (int) GFFormDisplay::get_current_page( $this->formId ) === (int) $this->pageNumber;

		if ( ( ! is_admin() || GP_Perk::doing_ajax( 'gpnf_edit_entry' ) ) && $tos_visible ) {
			$disabled = rgar( $this, $this->key( 'require_scroll' ) ) && ! rgpost( sprintf( 'input_%s_1', $this->id ) ) ? "disabled='disabled'" : '';
		}

		$html_id = sprintf( 'input_%s_%s', $form['id'], $this->id );
		$input   = sprintf(
			'<div class="ginput_container gptos_input_container"><%1$s class="gfield_checkbox" id="%2$s">%3$s</%1$s></div>',
			is_callable( array( 'GFCommon', 'is_legacy_markup_enabled' ) ) && ! GFCommon::is_legacy_markup_enabled( $form ) ? 'div' : 'ul',
			$html_id,
			$this->get_checkbox_choices( $value, $disabled, $form['id'] )
		);

		return $terms . $input;
	}

	public function get_form_inline_script_on_page_render( $form ) {

		$script = '';

		foreach ( $form['fields'] as $field ) {

			if ( ! rgar( $field, $this->key( 'require_scroll' ) ) ) {
				continue;
			}

			$script .= 'gwTosScroll( jQuery( "#gw_terms_' . $field['id'] . '"), ' . $field['id'] . ', ' . $form['id'] . ' ); jQuery("#gw_terms_' . $field['id'] . '").scroll(function(){ gwTosScroll( jQuery( this ), ' . $field['id'] . ', ' . $form['id'] . ' ); });';

		}

		if ( $script ) {

			// include generic function once
			$script = '' .
					  'function gwTosScroll( $elem, fieldId, formId ) {' .
					  'if( $elem.length <= 0 ) { return; }' .
					  'var isFullScroll = $elem.scrollTop() + $elem.height() >= $elem[0].scrollHeight - 20;' .
					  'if( $elem.is( ":visible" ) && isFullScroll ) {' .
					  '    jQuery( "input#choice_" + formId + "_" + fieldId + "_1" ).prop( "disabled", false );' .
					  '}' .
					  '}' . $script;

			$script_event = $this->has_conditional_logic( $form ) ? GFFormDisplay::ON_CONDITIONAL_LOGIC : GFFormDisplay::ON_PAGE_RENDER;
			GFFormDisplay::add_init_script( $form['id'], $this->key( 'init_script' ), $script_event, $script );

		}

	}

	public function get_terms_container_tag( $field ) {
		$form_id = rgar( $field, 'formId' ) ? rgar( $field, 'formId' ) : rgget( 'id' );
		return apply_filters( 'gptos_terms_container_tag_' . $form_id, apply_filters( 'gptos_terms_container_tag', 'textarea', $field ), $field );
	}

	public function is_html_enabled( $field ) {
		return $this->get_terms_container_tag( $field ) == 'div' || rgar( $field, 'tosHTMLEnabled' );
	}

	public function has_conditional_logic( $form ) {

		// has_conditional_logic is changed to public with GF 1.8.5.18, see if the version of GF running has the public version of this method
		$func = array( 'GFFormDisplay', 'has_conditional_logic' );
		if ( is_callable( $func ) ) {
			$has_conditional_logic = call_user_func( $func, $form );
		} else {
			$has_conditional_logic = $this->has_conditional_logic_legwork( $form );
			$has_conditional_logic = apply_filters( 'gform_has_conditional_logic', $has_conditional_logic, $form );
		}

		return $has_conditional_logic;
	}

	public function has_conditional_logic_legwork( $form ) {

		if ( empty( $form ) ) {
			return false;
		}

		if ( isset( $form['button']['conditionalLogic'] ) ) {
			return true;
		}

		if ( is_array( rgar( $form, 'fields' ) ) ) {
			foreach ( $form['fields'] as $field ) {
				if ( ! empty( $field['conditionalLogic'] ) ) {
					return true;
				} elseif ( isset( $field['nextButton'] ) && ! empty( $field['nextButton']['conditionalLogic'] ) ) {
					return true;
				}
			}
		}

		return false;
	}

	public function add_wpml_field_keys( $keys ) {

		array_push( $keys, $this->key( 'terms' ) );

		return $keys;
	}

	public function add_wpml_support( $form, $form_package, $form_field ) {
		$GLOBALS['wpml_gfml_tm_api']->register_strings_field_option( $form_package, $form_field );
	}

	public function include_terms_in_merge_tags( $value, $merge_tag, $options, $field ) {

		if ( $field['type'] != 'tos' ) {
			return $value;
		}

		$options = explode( ',', $options );
		if ( ! in_array( 'include_terms', $options ) ) {
			return $value;
		}

		if ( $merge_tag != 'all_fields' ) {
			$value = '<ul><li>' . $value . '</li></ul>';
		}

		$value = wpautop( $field->get_terms( GFAPI::get_form( $field->formId ) ) ) . $value;

		return $value;

	}

	public function get_terms( $form ) {

		$terms = rgar( $this, $this->key( 'terms' ) );

		if ( ! $this->is_form_editor() ) {
			$terms = do_shortcode( $terms );
		}

		// replace merge tags if Preview Confirmation is available
		if ( is_callable( array( 'GWPreviewConfirmation', 'preview_replace_variables' ) ) ) {
			$terms = GWPreviewConfirmation::preview_replace_variables( $terms, $form );
		}

		return $terms;
	}

	public function key( $key ) {
		return gp_terms_of_service()->key( $key );
	}

}

GF_Fields::register( new GF_Field_Terms_Of_Service() );
