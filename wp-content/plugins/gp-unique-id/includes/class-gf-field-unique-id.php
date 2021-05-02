<?php

class GF_Field_Unique_ID extends GF_Field {

	public $type = 'uid';

	public static $instance = null;

	public function __construct( $data = array() ) {

		parent::__construct( $data );

		// init on first run
		if ( self::$instance === null ) {
			self::$instance = $this->init();
		}

	}

	public function init() {

		add_action( 'gform_field_css_class', array( $this, 'add_editor_field_class' ), 10, 2 );
		add_action( 'gform_field_standard_settings_25', array( $this, 'field_settings_ui' ) );
		add_action( 'gform_field_advanced_settings_50', array( $this, 'advanced_field_settings_ui' ) );
		add_action( 'gform_editor_js', array( $this, 'editor_js' ) );
		add_action( 'gform_editor_js', array( $this, 'field_default_properties_js' ) );
		add_filter( 'gform_routing_field_types', array( $this, 'add_routing_field_type' ) );

		// This is here for backwards compatibility. GF introduced the "hidden" visibility setting a while back. In order
		// to use it, we must make sure old fields have it set as well.
		add_action( 'gform_form_post_get_meta', array( $this, 'set_field_visibility' ) );

		// Priority 8 so ID is generated before GF Feeds are processed (10) and gives other plugins a chance to do something
		// with the generated ID before the GF Feeds are processed as well (9).
		add_filter( 'gform_entry_post_save', array( $this, 'populate_field_value' ), 8, 2 );
		add_action( 'gform_post_add_entry', array( $this, 'populate_field_value' ), 8, 2 );
		add_action( 'gform_paypal_fulfillment', array( $this, 'delayed_populate_field_value' ), 8 );
		// Handle Mollie transactions
		add_action( 'gform_trigger_payment_delayed_feeds', array( $this, 'delayed_mollie_populate_field_value' ), 8, 4 );

		add_action( 'wp_ajax_gpui_reset_starting_number', array( $this, 'ajax_reset_starting_number' ) );

		return $this;
	}

	public function get_form_editor_field_title() {
		return esc_attr__( 'Unique ID', 'gp-unique-id' );
	}

	public function get_form_editor_button() {
		return array(
			'group' => 'advanced_fields',
			'text'  => $this->get_form_editor_field_title(),
		);
	}

	public function get_form_editor_field_settings() {
		/**
		 * Filter the field settings that appear in the Form Editor for Unique ID fields.
		 *
		 * @since 1.3.13
		 *
		 * @param array $settings The default field settings available for the Unique ID field type.
		 */
		return apply_filters(
			'gpui_form_editor_field_settings',
			array(
				'label_setting',
				'uid_setting',
				'conditional_logic_field_setting',
				'prepopulate_field_setting',
				'admin_label_setting',
				'css_class_setting',
			)
		);
	}

	public function field_default_properties_js() {
		?>

		<script type="text/javascript">

			function SetDefaultValues_<?php echo $this->type; ?>( field ) {
				field.label = '<?php esc_html_e( 'Unique ID', 'gp-unique-id' ); ?>';
				field['<?php echo gp_unique_id()->perk->key( 'type' ); ?>'] = 'alphanumeric';
				field.visibility = 'hidden';
				return field;
			}

		</script>

		<?php
	}

	/**
	 * Add `gform_hidden` class to field container to tap into GF's default styling for hidden-type inputs in the form editor.
	 *
	 * @param $css_class
	 * @param $field
	 *
	 * @return string
	 */
	public function add_editor_field_class( $css_class, $field ) {
		if ( $this->is_form_editor() && $field->get_input_type() === $this->type ) {
			$css_class .= ' gform_hidden';
		}
		return $css_class;
	}

	public function field_settings_ui() {
		?>

		<li class="uid_setting gwp_field_setting field_setting">

			<div>
				<label for="<?php echo gp_unique_id()->perk->key( 'type' ); ?>" class="section_label">
					<?php _e( 'Type', 'gp-unique-id' ); ?>
					<?php gform_tooltip( gp_unique_id()->perk->key( 'type' ) ); ?>
				</label>
				<select name="<?php echo gp_unique_id()->perk->key( 'type' ); ?>" id="<?php echo gp_unique_id()->perk->key( 'type' ); ?>"
						onchange="SetFieldProperty( '<?php echo gp_unique_id()->perk->key( 'type' ); ?>', this.value ); gpui.toggleByType( this.value );">
					<?php foreach ( gp_unique_id()->get_unique_id_types() as $value => $type ) : ?>
						<?php printf( '<option value="%s">%s</option>', $value, $type['label'] ); ?>
					<?php endforeach; ?>
				</select>
			</div>

		</li>

		<?php
	}

	public function advanced_field_settings_ui() {
		?>

		<li class="uid_setting gwp_field_setting field_setting gp-field-setting">

			<div class="gp-row">
				<label for="<?php echo gp_unique_id()->perk->key( 'starting_number' ); ?>" class="section_label">
					<?php _e( 'Starting Number', 'gp-unique-id' ); ?>
					<?php gform_tooltip( gp_unique_id()->perk->key( 'starting_number' ) ); ?>
				</label>
				<input type="number" name="<?php echo gp_unique_id()->perk->key( 'starting_number' ); ?>" id="<?php echo gp_unique_id()->perk->key( 'starting_number' ); ?>"
					   onkeyup="SetFieldProperty( '<?php echo gp_unique_id()->perk->key( 'starting_number' ); ?>', this.value );"
					   onchange="SetFieldProperty( '<?php echo gp_unique_id()->perk->key( 'starting_number' ); ?>', this.value );"
					   style="width:25%;" />

				<a href="#" style="margin-left:10px;" onclick="gpui.resetStartingNumber( this )"><?php _e( 'reset', 'gp-unique-id' ); ?></a>
				<?php gform_tooltip( gp_unique_id()->perk->key( 'reset' ) ); ?>

			</div>

			<div class="gp-row">
				<label for="<?php echo gp_unique_id()->perk->key( 'length' ); ?>" class="section_label">
					<?php _e( 'Length', 'gp-unique-id' ); ?>
					<?php gform_tooltip( gp_unique_id()->perk->key( 'length' ) ); ?>
				</label>
				<input type="number" name="<?php echo gp_unique_id()->perk->key( 'length' ); ?>" id="<?php echo gp_unique_id()->perk->key( 'length' ); ?>"
					   onkeyup="gpui.setLengthFieldProperty( this.value );"
					   onchange="gpui.setLengthFieldProperty( this.value );"
					   onblur="gpui.setLengthFieldProperty( this.value, true );"
					   style="width:25%;" />
			</div>

			<div class="gp-row">
				<label for="<?php echo gp_unique_id()->perk->key( 'prefix' ); ?>" class="section_label">
					<?php _e( 'Prefix', 'gp-unique-id' ); ?>
					<?php gform_tooltip( gp_unique_id()->perk->key( 'prefix' ) ); ?>
				</label>
				<input type="text" class="merge-tag-support mt-position-right" name="<?php echo gp_unique_id()->perk->key( 'prefix' ); ?>" id="<?php echo gp_unique_id()->perk->key( 'prefix' ); ?>"
						onkeyup="SetFieldProperty( '<?php echo gp_unique_id()->perk->key( 'prefix' ); ?>', this.value );"
						onchange="SetFieldProperty( '<?php echo gp_unique_id()->perk->key( 'prefix' ); ?>', this.value );"
						oninput="SetFieldProperty( '<?php echo gp_unique_id()->perk->key( 'prefix' ); ?>', this.value );" />
			</div>

			<div>
				<label for="<?php echo gp_unique_id()->perk->key( 'suffix' ); ?>" class="section_label">
					<?php _e( 'Suffix', 'gp-unique-id' ); ?>
					<?php gform_tooltip( gp_unique_id()->perk->key( 'suffix' ) ); ?>
				</label>
				<input type="text" class="merge-tag-support mt-position-right" name="<?php echo gp_unique_id()->perk->key( 'suffix' ); ?>" id="<?php echo gp_unique_id()->perk->key( 'suffix' ); ?>"
						onkeyup="SetFieldProperty( '<?php echo gp_unique_id()->perk->key( 'suffix' ); ?>', this.value );"
						onchange="SetFieldProperty( '<?php echo gp_unique_id()->perk->key( 'suffix' ); ?>', this.value );"
						oninput="SetFieldProperty( '<?php echo gp_unique_id()->perk->key( 'suffix' ); ?>', this.value );" />
			</div>

		</li>

		<?php
	}

	public function editor_js() {
		?>

		<script type='text/javascript'>

			jQuery( document ).ready(function( $ ) {

				$( document).bind( 'gform_load_field_settings', function( event, field, form ) {

					var $type       = $( '#' + gpui.key( 'type' ) ),
						$prefix     = $( '#' + gpui.key( 'prefix' ) ),
						$suffix     = $( '#' + gpui.key( 'suffix' ) ),
						$length     = $( '#' + gpui.key( 'length' ) ),
						$start      = $( '#' + gpui.key( 'starting_number' ) ),
						$reset = $( '#' + gpui.key( 'reset' ) ),
						type        = field[gpui.key( 'type' )];

					$type.val( type );
					$prefix.val( field[gpui.key( 'prefix' )] );
					$suffix.val( field[gpui.key( 'suffix' )] );
					$length.val( field[gpui.key( 'length' )] );
					$start.val( field[gpui.key( 'starting_number' )] );
					$reset.prop( 'checked', field[gpui.key( 'reset' )] == true );

					gpui.toggleByType( type );

				} );

			} );

			var gpui;

			( function( $ ) {

				gpui = {

					key: function( key ) {
						return '<?php echo gp_unique_id()->perk->key( '' ); ?>' + key;
					},

					setLengthFieldProperty: function( length, enforce ) {

						var type    = $( '#' + gpui.key( 'type' ) ).val(),
							length  = parseInt( length ),
							enforce = typeof enforce != 'undefined' && enforce === true;

						if( isNaN( length ) ) {
							length = '';
						} else {
							switch( type ) {
								case 'alphanumeric':
									length = Math.max( length, 4 );
									break;
								case 'numeric':
									length = Math.max( length, <?php echo apply_filters( 'gpui_numeric_minimum_length', 6 ); ?> );
									length = Math.min( length, 19 );
									break;
							}
						}

						SetFieldProperty( gpui.key( 'length' ), length );

						if( enforce ) {
							$( '#' + gpui.key( 'length' ) ).val( length );
						}

					},

					toggleByType: function( type ) {

						var $start = $( '#' + gpui.key( 'starting_number' ) );

						switch( type ) {
							case 'sequential':
								$start.parent().show();
								break;
							default:
								$start.parent().hide();
								$start.val( '' ).change();
						}

					},

					resetStartingNumber: function( elem ) {

						var starting_number = parseInt( $( '#' + gpui.key( 'starting_number' ) ).val() );
						if ( ! starting_number ) {
							return alert( '<?php _e( 'Please enter a starting number to reset the sequential ID', 'gp-unique-id' ); ?>' );
						}
						var $elem         = $( elem ),
							field         = GetSelectedField(),
							resettingText = '<?php _e( 'resetting', 'gp-unique-id' ); ?>',
							$response     = $( '<span />' ).text( resettingText ).css( 'margin-left', '10px' );


						$elem.hide();
						$response.insertAfter( $elem );

						var loadingInterval = setInterval( function() {
							$response.text( $response.text() + '.' );
						}, 500 );

						$.post( ajaxurl, {
							action:          'gpui_reset_starting_number',
							starting_number: $( '#' + gpui.key( 'starting_number' ) ).val(),
							form_id:         field.formId,
							field_id:        field.id,
							gpui_reset_starting_number: '<?php echo wp_create_nonce( 'gpui_reset_starting_number' ); ?>'
						}, function( response ) {

							clearInterval( loadingInterval );

							if( response ) {
								response = $.parseJSON( response );
								$response.text( response.message );
							}

							setTimeout( function() {
								$response.remove();
								$elem.show();
							}, 4000 );

						} );

					}

				}

			} )( jQuery );

		</script>

		<?php
	}

	public function set_field_visibility( $form ) {
		foreach ( $form['fields'] as &$field ) {
			if ( $field->get_input_type() == $this->get_input_type() ) {
				if ( version_compare( GFCommon::$version, '2.1', '<=' ) ) {
					$field->cssClass .= ' gf_hidden';
				}
				$field->visibility = 'hidden';
			}
		}
		return $form;
	}

	public function get_field_input( $form, $value = '', $entry = null ) {

		if ( $this->is_form_editor() ) {
			return $this->get_field_input_form_editor();
		}

		$input_type = $this->is_form_editor() || $this->is_entry_detail() ? 'text' : 'hidden';
		$html_id    = $this->is_entry_detail() ? "input_{$this->id}" : "input_{$form['id']}_{$this->id}";
		$disabled   = $this->is_form_editor() ? "disabled='disabled'" : '';

		extract( gf_apply_filters( 'gpui_input_html_options', array( $form['id'], $this->id ), compact( 'input_type', 'disabled' ) ) );

		$input_html = sprintf( "<input name='input_%d' id='%s' type='%s' value='%s' %s />", $this->id, $html_id, $input_type, esc_attr( $value ), $disabled );
		$input_html = sprintf( "<div class='ginput_container ginput_container_%s'>%s</div>", $input_type, $input_html );

		return $input_html;
	}

	public function get_field_input_form_editor() {
		$style = 'border:1px dashed #ccc;background-color:transparent;text-transform:lowercase;width: 100%;text-align:center;font-size: 0.9375rem;padding: 0.5rem;line-height: 2;border-radius: 4px;';
		if ( GravityPerks::is_gf_version_lte( '2.5-beta-1' ) ) {
			$style = 'border:1px dashed #ccc;background-color:transparent;padding:5px;color:#bbb;letter-spacing:.05em;text-transform:lowercase;width:330px;text-align:center;font-family:\'Open Sans\', sans-serif;';
		}
		$input_html = sprintf( '<input
            style="%s"
            value="hidden field, populated on submission"
            disabled="disabled" />',
			$style
		);
		$input_html = sprintf( "<div class='ginput_container ginput_container_hidden'>%s</div>", $input_html );
		return $input_html;
	}

	/**
	 * GF 2.5 adds an ugly "Hidden" label and icon about field's with a hidden visibility. Let's disable this.
	 * @return string
	 */
	public function get_hidden_admin_markup() {
		return '';
	}

	public function populate_field_value( $entry, $form, $fulfilled = false ) {

		$feed = null;

		if ( rgar( $entry, 'partial_entry_id' ) ) {
			return $entry;
		}

		foreach ( $form['fields'] as $field ) {

			if ( $field->get_input_type() != $this->get_input_type() || GFFormsModel::is_field_hidden( $form, $field, array(), $entry ) ) {
				continue;
			}

			if ( $feed === null ) {
				$feed = $this->get_paypal_standard_feed( $form, $entry );
				// Look for Mollie's feed if PayPal isn't present
				if ( ! $feed ) {
					$feed = gp_unique_id_field()->get_mollie_standard_feed( $form, $entry );
				}
				/**
				 * Modify the feed that indicates a payment gateway is configured that
				 * accepts delayed payments (i.e. PayPal Standard).
				 *
				 * This filter allows 3rd party payment add-ons to add support for delaying unique ID generation when
				 * one of their feeds is present.
				 *
				 * @since 1.3.1
				 *
				 * @param $feed  array The payment feed.
				 * @param $form  array The current form object.
				 * @param $entry array The current entry object.
				 */
				$feed = gf_apply_filters( array( 'gpui_wait_for_payment_feed', $form['id'], $field->id ), $feed, $form, $entry );
			}

			/**
			 * Indicate whether the unique ID generation should wait for a completed payment.
			 *
			 * Only applies to payment gateways that accept delayed payments (i.e. PayPal Standard).
			 *
			 * @since 1.3.0
			 *
			 * @param $wait_for_payment bool  Whether or not to wait for payment. Defaults to false.
			 * @param $form             array The current form object.
			 * @param $entry            array The current entry object.
			 */
			$wait_for_payment = $feed && gf_apply_filters( array( 'gpui_wait_for_payment', $form['id'], $field->id ), false, $feed, $form, $entry );
			if ( $wait_for_payment && ! $fulfilled ) {
				continue;
			}

			// Check entry for value first (bullet-proofing PayPal delayed generation) and then check the $_POST.
			$default_value = $entry[ $field->id ] ? $entry[ $field->id ] : rgpost( "input_{$field->id}" );
			$value         = $this->save_value( $entry, $field, $default_value );

			$entry[ $field['id'] ] = $value;

		}

		return $entry;
	}

	/**
	 * When a PayPal order is fulfilled, loop through fields and populate any there were configured to wait for payment.
	 *
	 * @param $entry
	 */
	public function delayed_populate_field_value( $entry ) {
		$form = GFAPI::get_form( $entry['form_id'] );
		$this->populate_field_value( $entry, $form, true );
	}

	public function get_paypal_standard_feed( $form, $entry ) {

		$feed = false;

		if ( is_callable( 'gf_paypal' ) ) {
			$entry['id'] = null;
			$feed        = gf_paypal()->get_payment_feed( $entry, $form );
		}

		return $feed;
	}

	public function get_mollie_standard_feed( $form, $entry ) {

		$feed = false;

		if ( is_callable( 'gf_mollie' ) ) {
			$entry['id'] = null;
			$feed        = gf_mollie()->get_payment_feed( $entry, $form );
		}

		return $feed;
	}

	public function delayed_mollie_populate_field_value( $transaction_id, $payment_feed, $entry, $form ) {
		if ( rgar( $payment_feed, 'addon_slug' ) === 'gravityformsmollie' ) {
			$this->populate_field_value( $entry, $form, true );
		}
	}

	public function save_value( $entry, $field, $value ) {

		if ( ! $value ) {
			gp_unique_id()->log( sprintf( 'Generating a unique ID for field %d', $field->id ) );
			$value = gp_unique_id()->get_unique( $entry['form_id'], $field, 5, array(), $entry );
		}

		gp_unique_id()->log( sprintf( 'Saving unique ID for field %d: %s', $field->id, $value ) );

		$result = GFAPI::update_entry_field( $entry['id'], $field->id, $value );

		return $result ? $value : false;
	}

	public function save_value_to_entry( $entry_id, $form_id, $field, $value = false ) {
		global $wpdb;

		if ( ! $value ) {
			$value = gp_unique_id()->get_unique( $form_id, $field );
		}

		$result = GFAPI::update_entry_field( $entry_id, $field['id'], $value );

		return $result ? $value : false;
	}

	public function ajax_reset_starting_number() {

		$form_id         = rgpost( 'form_id' );
		$field_id        = rgpost( 'field_id' );
		$starting_number = rgpost( 'starting_number' );
		$starting_number = is_numeric( $starting_number ) ? $starting_number : 1; // Default to 1 if starting number is missing

		if ( ! check_admin_referer( 'gpui_reset_starting_number', 'gpui_reset_starting_number' ) || ! $form_id || ! $field_id || ! $starting_number ) {
			die( __( 'Oops! There was an error resetting the starting number.', 'gp-unique-id' ) );
		}

		$result = gp_unique_id()->set_sequential_starting_number( $form_id, $field_id, $starting_number - 1 );

		if ( $result == true ) {
			$response = array(
				'success' => true,
				'message' => __( 'Reset successfully!', 'gp-unique-id' ),
			);
		} elseif ( $result === 0 ) {
			$response = array(
				'success' => false,
				'message' => __( 'Already reset.', 'gp-unique-id' ),
			);
		} else {
			$response = array(
				'success' => false,
				'message' => __( 'Error resetting.', 'gp-unique-id' ),
			);
		}

		die( json_encode( $response ) );
	}

	function add_routing_field_type( $field_types ) {
		$field_types[] = 'uid';
		return $field_types;
	}

	/**
	 * Temporary solution to issue where Unique ID values are overwritten when editing an entry via Gravity View.
	 *
	 * @param $form
	 *
	 * @return mixed
	 */
	public function enable_dynamic_population( $form ) {
		foreach ( $form['fields'] as &$field ) {
			if ( $this->is_this_field_type( $field ) ) {
				$field['allowsPrepopulate'] = true;
				$field['inputName']         = $field['id'];
			}
		}
		return $form;
	}

}

class GP_Unique_ID_Field extends GF_Field_Unique_ID { }

GF_Fields::register( new GF_Field_Unique_ID() );

function gp_unique_id_field() {
	return GF_Field_Unique_ID::$instance;
}
