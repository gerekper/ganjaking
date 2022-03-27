<?php

if ( ! class_exists( 'GWConditionalLogicDateFields' ) ) {

	class GWConditionalLogicDateFields {

		public $perk = null;

		static $script_output     = false;
		static $applicable_fields = array();

		function __construct( $perk ) {

			$this->perk = $perk;

			// handles converting dates to timestamps any time form meta is retrieved (except on form editor view)
			//add_filter( 'gform_form_post_get_meta', array( $this, 'maybe_modify_form_object' ) );

			// handles evaluating date-based conditional logic
			add_filter( 'gform_is_value_match', array( $this, 'is_value_match' ), 10, 6 );

			add_filter( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ), 11 );
			add_filter( 'gform_admin_pre_render', array( $this, 'enqueue_admin_inline_script' ) );
			add_filter( 'gform_routing_field_types', array( $this, 'set_routing_field_types' ), 10, 5 );

			//add_filter( 'gform_field_content',         array( $this, 'add_logic_event_to_input' ), 10, 2 );
			add_filter( 'gform_enqueue_scripts', array( $this, 'enqueue_form_scripts' ) );
			add_filter( 'gform_pre_render', array( $this, 'modify_frontend_form_object' ), 10 );
			add_filter( 'gform_pre_process', array( $this, 'modify_submitted_form_object' ), 9 );
			add_filter( 'gform_pre_submission_filter', array( $this, 'modify_submitted_form_object' ), 9 );
			add_filter( 'gform_admin_pre_render', array( $this, 'maybe_modify_admin_form_object' ), 9 );
			add_filter( 'gform_before_resend_notifications', array( $this, 'modify_submitted_form_object' ), 9 );

			add_filter( 'gform_addon_pre_process_feeds', array( $this, 'modify_feeds' ), 10, 3 );

			add_action( 'gform_action_pre_payment_callback', array( $this, 'modify_form_object_for_payment_callbacks' ) );

		}

		public function enqueue_admin_scripts() {

			if ( GFForms::get_page() || rgget( 'page' ) === 'gf_edit_forms' ) {

				wp_enqueue_script( 'gform_masked_input' );
				GWPerk::register_noconflict_script( 'gform_masked_input' );

				wp_enqueue_script( 'jquery-ui-datepicker' );
				GWPerk::register_noconflict_script( 'jquery-ui-datepicker' );

			}

		}

		public function enqueue_admin_inline_script( $form ) {
			add_filter( 'admin_footer', array( $this, 'admin_inline_script' ) );

			return $form;
		}

		/**
		 * Allow Date fields to be selected in the field select for conditional logic UI.
		 *
		 * @param mixed $form
		 */
		function admin_inline_script() {
			?>

			<script type="text/javascript">
				if (window.gform) {

					// allow Date fields in conditional logic field select
					gform.addFilter('gform_is_conditional_logic_field', function (isConditionalLogicField, field) {
						// if GF already considers this a conditional field OR if the field type is 'date'
						return isConditionalLogicField || GetInputType(field) == 'date' || GetInputType(field) == 'time';
					});

					// add Time fields, "Current Time", and "Current Date" to conditional logic field select
					gform.addFilter('gform_conditional_logic_fields', function (options, form, selectedFieldId) {

						for (var i = 0; i < form.fields.length; i++) {

							var field = form.fields[i];

							if (GetInputType(field) != 'time' && GetInputType(field) != 'date') {
								continue;
							}

							for (var j = options.length - 1; j >= 0; j--) {
								if (parseInt(options[j].value) == field.id) {
									options.splice(j, 1);
								}
							}

							options.push({
								label: GetLabel(field),
								value: field.id,
								selected: field.id == selectedFieldId ? 'selected="selected"' : ''
							});
						}

						var currentTimeValue = '_gpcld_current_time';
						options.push({
							label: '<?php esc_html_e( 'Δ Current Time' ); ?>',
							value: currentTimeValue,
							selected: currentTimeValue == selectedFieldId ? 'selected="selected"' : ''
						});

						/*options.push( {
							label: '<!php esc_html_e( 'Δ Current Date' ); !>',
							value: '_gpcld_current_date'
						} );*/

						return options;
					});

					gform.addFilter('gform_conditional_logic_operators', function (operators, objectType, fieldId) {

						var field = GetFieldById(fieldId);
						if (!field || (GetInputType(field) != 'date' && GetInputType(field) != 'time')) {
							return operators;
						}

						var allowedOperators = ['<', '>', 'is', 'isnot'],
							filteredOperators = {};

						for (var key in operators) {
							if (operators.hasOwnProperty(key) && jQuery.inArray(key, allowedOperators) !== -1) {
								filteredOperators[key] = operators[key];
							}
						}

						return filteredOperators;
					});

					jQuery(document).ready(function ($) {

						var refreshInputsTimeout = false;

						$.mask.definitions['p'] = '[APap]';
						$.mask.definitions['m'] = '[Mm]';

						var $doc           = $(document),
							baseSelector   = '.gf_conditional_logic_rules_container',
							editorSelector = '.conditional-flyout__main-fields';

						$doc.on('change', baseSelector + ' select[id*="_rule_field_"], ' + editorSelector + ' select[id*="_rule_field_"]', function () {
							initializeConditionalLogicValueInput($(this));
						});

						$doc.on('change', baseSelector + ' select[id*="_rule_operator_"], ' + editorSelector + ' select[id*="_rule_operator_"]', function () {
							var index = $(this).attr('id').split('_')[3],
								$select = $('#field_rule_field_' + index);
							initializeConditionalLogicValueInput($select);
						});

						$doc.on('change', baseSelector + ' .add_field_choice, ' + editorSelector + ' .add_field_choice', function () {
							delayedRefreshInputs();
						});

						$doc.bind('gform_load_field_settings', function (event, field) {
							$doc.find(baseSelector + ' select[id*="_rule_field_"]').each(function () {
								initializeConditionalLogicValueInput($(this));
							});
						});

						// Re-init our custom input functionality anytime the CL inputs are refreshed.
						gform.addFilter('gform_conditional_logic_values_input', function (fields) {
							delayedRefreshInputs();
							return fields;
						});

						function delayedRefreshInputs() {
							refreshInputsTimeout = setTimeout(function () {
								$doc.find(baseSelector + ' select[id*="_rule_field_"], ' + editorSelector + ' select[id*="_rule_field_"]' ).each(function () {
									initializeConditionalLogicValueInput($(this));
								});
							}, 10);
						}

						function initializeConditionalLogicValueInput($select) {

							var value          = $select.val(),
								bits           = $select.attr('id').split('_'),
								index          = bits[bits.length - 1],
								prep           = bits.indexOf('rule'),
								prefix         = bits.slice(0, prep).join('_'),
								$input         = $('#{0}_rule_value_{1}'.format(prefix, index)),
								needsSaveEvent = false;

							$input.attr('placeholder', gf_vars.enterValue);

							if (value == '_gpcld_current_time') {
								$input.attr('placeholder', '<?php _e( 'e.g. 12:30am' ); ?>').mask('99:99?pm');
								needsSaveEvent = true;
							} else if (value == '_gpcld_current_date') {
								$input.attr('placeholder', 'mm/dd/yyyy').mask('99/99/9999').datepicker();
								needsSaveEvent = true;
							} else {

								for (var i = 0; i < form.fields.length; i++) {
									var field = form.fields[i];
									if (value != field.id) {
										continue;
									}
									switch (GetInputType(field)) {
										case 'time':
											$input.attr('placeholder', '<?php _e( 'e.g. 12:30am' ); ?>').mask('99:99?pm');
											needsSaveEvent = true;
											break;
										case 'date':
											$input
												.attr('placeholder', 'mm/dd/yyyy')
												.attr( 'autocomplete', 'off' )
												.datepicker({
													dateFormat    : 'mm/dd/yy',
													constrainInput: false,
													// The datepicker's default index is oddly set to `-9` in GF 2.5. Let's force our own z-index.
													beforeShow: function() {
														window.requestAnimationFrame( function() {
															$('.ui-datepicker').css( 'z-index', 1 );
														} );
													},
													onSelect: function() {
														$( this ).change();
													}
											});
											needsSaveEvent = true;
											break;
									}
								}

							}

							if ( needsSaveEvent && window.GF_CONDITIONAL_INSTANCE ) {
								$input.on( 'change', function( e ) {
									saveConditionalLogicValue( e );
								} );
							}

						}

						/**
						 * Get the current conditional logic instance. GF_CONDITIONAL_INSTANCE isn't always reliable as it'll be the
						 * last generated conditional logic instance rather than the one that's open.
						 *
						 * @return {GFConditionalLogic}
						 */
						function getConditionalInstance() {
							var $flyout = $('.conditional_logic_flyout_container:visible:not(:empty)');

							if ($flyout.length) {
								var conditionalLogicObjectTypeMatch = $flyout.prop('id').match(/conditional_logic_(.*?)_flyout_container/);

								if (conditionalLogicObjectTypeMatch) {
									var conditionalLogicObjectType = conditionalLogicObjectTypeMatch[1];
									var conditionalInstance = null;

									window.GF_CONDITIONAL_INSTANCES_COLLECTION.forEach( function( instance, instanceIndex ) {
										if (instance.objectType === conditionalLogicObjectType) {
											conditionalInstance = instance;
											return false; // break
										}
									});

									if (conditionalInstance) {
										return conditionalInstance;
									}
								}
							}

							return window.GF_CONDITIONAL_INSTANCE;
						}

						function saveConditionalLogicValue( e ) {
							var instance = getConditionalInstance();

							if ( instance ) {
								var parent = e.target.parentNode;
								var key    = e.target.dataset.jsRuleInput;
								var val    = e.target.value;

								instance.updateRule( key, val, parent.dataset.jsRuleIdx );
							}
						}

						// Try and catch any places where GF renders the markup on page load via PHP for existing
						// conditional logic; rather than generating from JS.
						delayedRefreshInputs();

					});

				}
			</script>

			<?php
		}

		function modify_form_object_for_payment_callbacks() {
			GFFormsModel::flush_current_forms();
			add_filter( 'gform_form_post_get_meta', array( $this, 'modify_frontend_form_object' ) );
		}

		/**
		 * Modify the front-end form object by:
		 *  1 - Converting any date-based conditional logic values from date strings (ie '05/04/2013') to a timestamp (ie '1234567900')
		 *  2 - Adding the 'gcldf-field' class to all date fields upon which conditional logic is dependent
		 *
		 * @param mixed $form
		 */
		function modify_frontend_form_object( $form ) {

			if ( ! is_array( $form ) ) {
				return $form;
			}

			 $applicable_fields = array_filter( self::get_applicable_date_fields( $form ) );
			if ( empty( $applicable_fields ) ) {
				return $form;
			}

			// NOTE: will be handled in via 'gform_form_post_get_meta' filter
			// don't convert date values if the form has been submitted since it will already have been converted via the "gform_pre_validation" hook
			$form = self::convert_conditional_logic_date_field_values( $form );

			// loop through fields an apply 'gcldf-field' class to applicable date fields
			foreach ( $form['fields'] as &$field ) {
				$applicable_field_ids = wp_list_pluck( $applicable_fields, 'id' );
				if ( in_array( $field['id'], $applicable_field_ids ) ) {
					$date_format        = $field->get_input_type() == 'date' ? rgar( $field, 'dateFormat', 'mdy' ) : false;
					$date_format_class  = $date_format ? sprintf( 'gcldf-date-format-%s', $date_format ) : '';
					$field['cssClass'] .= sprintf( ' gcldf-field gcldf-field-%s %s gfield_trigger_change', $field->get_input_type(), $date_format_class );
				}
			}

			return $form;
		}

		/**
		 * Modify the form object on the Entry Detail page; the order summary is processed w/ admin labels the first
		 * time the entry detail view is accessed for an entry. Since products/discounts/taxes can have date-based
		 * conditional logic, we need to make sure that we handle that in the admin as well.
		 *
		 * This was added specifically for the use case where a user had date-based conditional logic configured for a
		 * Discount field.
		 *
		 * @param $form
		 *
		 * @return mixed
		 */
		function maybe_modify_admin_form_object( $form ) {
			if ( GFForms::get_page() == 'entry_detail' ) {
				$form = $this->modify_submitted_form_object( $form );
			}

			return $form;
		}

		function modify_feeds( $feeds, $entry, $form ) {
			if ( is_array( $feeds ) ) {
				$feeds = self::convert_conditional_logic_date_field_values( $feeds, $form );
			}

			return $feeds;
		}

		function enqueue_form_scripts( $form ) {
			if ( self::has_applicable_date_fields( $form ) ) {
				$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';
				wp_enqueue_script( 'gp-conditional-logic-dates', $this->perk->get_base_url() . "/js/gp-conditional-logic-dates{$min}.js", array( 'gform_gravityforms', 'gform_conditional_logic' ) );

				wp_localize_script( 'gp-conditional-logic-dates', 'GPConditionalLogicDates', array(
					'serverTzOffsetHours' => wp_timezone()->getOffset( new DateTime ) / HOUR_IN_SECONDS,
				) );
			}
		}

		function enqueue_inline_script( $form ) {

			if ( ! self::has_applicable_date_fields( $form ) || has_filter( 'wp_footer', array( $this, 'output_inline_script' ) ) ) {
				return $form;
			}

			add_filter( 'wp_footer', array( $this, 'output_inline_script' ), 99 );
			add_filter( 'gform_preview_footer', array( $this, 'output_inline_script' ) );

			return $form;
		}

		function modify_submitted_form_object( $form ) {

			if ( self::has_applicable_date_fields( $form ) ) {
				/**
				 * Some plugins (like WC GF Product Add-ons) initiate some of Gravity Forms caching
				 * (like the GFFormsModel::is_field_hidden()) prematurely. Flush the cache to make sure we're running
				 * on a clean slate.
				 */
				GFCache::flush();
				$form = self::convert_conditional_logic_date_field_values( $form );
			}

			return $form;
		}

		function maybe_modify_form_object( $form ) {

			if ( ! in_array( GFForms::get_page(), array( 'form_editor' ) ) ) {
				$form = $this->modify_submitted_form_object( $form );
			}

			return $form;
		}

		function is_value_match( $is_match, $field_value, $target_value, $operator, $source_field, $rule ) {

			if ( $rule['fieldId'] == '_gpcld_current_time' ) {

				$value        = current_time( 'timestamp' );
				$target_value = strtotime( $rule['value'], $value );

			} else {

				switch ( GFFormsModel::get_input_type( $source_field ) ) {
					case 'date':
						$format      = $source_field['dateFormat'] ? $source_field['dateFormat'] : 'mdy';
						$parsed_date = GFCommon::parse_date( $field_value, $format );
						$value       = false;
						break;
					case 'time':
						$field_value  = ! is_array( $field_value ) && $field_value ? preg_split( '/[: ]/', $field_value ) : $field_value; // will either be an array or string (i.e. 04:00 am)
						$time_string  = sprintf( '%02d:%02d%s', rgar( $field_value, 0 ), rgar( $field_value, 1 ), rgar( $field_value, 2 ) );
						$value        = strtotime( $time_string );
						$target_value = strtotime( $rule['value'], $value );
						break;
					default:
						return $is_match;
				}
			}

			/*
			 * Allows use of asterisks (wildcards) when specifying dates in rule values. Will be replaced with
			 * the corresponding value from the compared date value.
			 *
			 * Selected Date: 9/20/2016
			 * Wildcard Rule: 9/15/*
			 * Replaced Rule: 9/15/2016
			 */
			if ( strpos( $rule['value'], '*' ) !== false ) {

				$format      = $source_field['dateFormat'] ? $source_field['dateFormat'] : 'mdy';
				$parsed_date = GFCommon::parse_date( $field_value, $format );
				$rule_date   = array_combine( array( 'month', 'day', 'year' ), explode( '/', $rule['value'] ) );

				foreach ( $rule_date as $key => &$_value ) {
					if ( $_value == '*' ) {
						$_value = $parsed_date[ $key ];
					}
				}

				$target_value = date( 'U', strtotime( implode( '/', $rule_date ) ) );

			}

			if ( ! empty( $parsed_date ) ) {

				$timestamp = strtotime( implode( '/', array( $parsed_date['month'], $parsed_date['day'], $parsed_date['year'] ) ) );

				/*
				 * by default $target_value should already be converted to timestamp; some tags do not resolve to timestamps
				 * like {monday} and need to be handled here
				 */
				preg_match_all( '/{([a-z]*)(?::(.+))?}/', $target_value, $matches, PREG_SET_ORDER );
				if ( $matches ) {
					foreach ( $matches as $match ) {

						list( $full_value, $tag, $modifier ) = array_pad( $match, 3, '' );

						$tag = strtolower( $tag );

						switch ( $tag ) {
							case 'monday':
							case 'tuesday':
							case 'wednesday':
							case 'thursday':
							case 'friday':
							case 'saturday':
							case 'sunday':
								$value        = date( 'N', $timestamp );
								$target_value = array_search( $tag, array( '', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday' ) );
								break;
						}
					}
				} else {
					// if no tags, assume tags have already been processed
					$value = date( 'U', $timestamp );
				}
			}

			/**
			 * Conditional logic can be evaluated for a form object that has not had its conditional logic date values
			 * converted. As a short-term solution, let's account for that here. In the future, we will convert dates on
			 * whenever the form is retrieved from the database.
			 * @see https://secure.helpscout.net/conversation/964227500/13854?folderId=14965
			 */
			if ( ! self::is_valid_timestamp( $target_value ) ) {
				$temp_rules   = self::convert_conditional_logic_rules( array( $rule ), $source_field->formId );
				$target_value = $temp_rules[0]['value'];

				if ( ! self::is_valid_timestamp( $value ) ) {
					$value = date( 'U', $timestamp );
				}
			}

			/**
			 * By default, GPCLD will require a Date field targeted by a conditional logic rule to have a value before
			 * that rule is evaluated. Otherwise, it will always return false for that condition.
			 *
			 * This is problematic when checking against multiple Date fields which themselves have conditional logic
			 * dictating whether they are shown or hidden. A hidden Date field will always have an empty value so it
			 * would always return false. If ALL conditions must be true, this would prevent the conditional logic from
			 * evaluating as expected.
			 *
			 * See: https://secure.helpscout.net/conversation/676756704/9885/
			 */
			/**
			 * Filter whether a date must be selected for the conditional logic rule to be evaluated.
			 *
			 * @param bool  $is_date_selection_required Is a date required to evaluate the conditional logic rule?
			 * @param int   $form_id                    The form ID of the targeted Date field.
			 * @param array $rule                       The current conditional logic rule.
			 *
			 * @since 1.0.6
			 */
			if ( $value === false && apply_filters( 'gpcld_require_date_selection', true, $source_field['formId'], $rule ) ) {
				if ( $operator == 'isnot' ) {
					$value = $target_value;
				} elseif ( $operator == '>' ) {
					$value = $target_value - 1;
				} elseif ( $operator != 'is' ) {
					$target_value = '';
				}
			}

			remove_filter( 'gform_is_value_match', array( $this, 'is_value_match' ) );
			$is_match = GFFormsModel::is_value_match( $value, $target_value, $operator, $source_field );
			add_filter( 'gform_is_value_match', array( $this, 'is_value_match' ), 10, 6 );

			return $is_match;
		}

		function set_routing_field_types( $field_types ) {
			$field_types[] = 'date';

			return $field_types;
		}



		// HELPERS

		/**
		 * Search through each fields conditional logic and look for date fields.
		 *
		 * @param mixed $form
		 */
		public static function get_applicable_date_fields( $form ) {

			$form_id = is_array( $form ) ? $form['id'] : $form;

			if ( isset( self::$applicable_fields[ $form_id ] ) ) {
				return self::$applicable_fields[ $form_id ];
			}

			if ( ! is_array( $form ) ) {
				$form = GFFormsModel::get_form_meta( $form_id );
			}

			self::$applicable_fields[ $form_id ] = self::get_applicable_fields_recursive( $form );

			return self::$applicable_fields[ $form_id ];
		}

		public static function get_applicable_fields_recursive( $object, $form = false, $applicable_fields = array() ) {

			// if no $form is provided, assume that the $object is the form object
			if ( ! $form ) {
				$form = $object;
			}

			foreach ( $object as $prop => $value ) {

				if ( $prop && $prop == 'conditionalLogic' && ! empty( $value ) ) {
					foreach ( $object[ $prop ]['rules'] as $rule ) {
						$ruleField = RGFormsModel::get_field( $form, $rule['fieldId'] );
						if ( in_array( $rule['fieldId'], array( '_gpcld_current_time', '_gpcld_current_date' ) ) || in_array( GFFormsModel::get_input_type( $ruleField ), array( 'date', 'time' ) ) ) {
							$applicable_fields[] = $ruleField;
						}
					}
				} elseif ( is_array( $value ) || is_a( $value, 'GF_Field' ) ) {
					$applicable_fields = self::get_applicable_fields_recursive( $value, $form, $applicable_fields );
				}
			}

			return $applicable_fields;
		}

		public static function has_applicable_date_fields( $form ) {
			$applicable_fields = self::get_applicable_date_fields( $form );

			return ! empty( $applicable_fields );
		}

		public static function convert_conditional_logic_date_field_values( $object, $form = false ) {

			// if no $form is provided, assume that the $object is the form object
			if ( ! $form ) {
				$form = $object;
			}

			foreach ( $object as $prop => $value ) {

				if ( $prop && $prop == 'conditionalLogic' && ! empty( $value ) ) {
					$logic           = $object[ $prop ];
					$logic['rules']  = self::convert_conditional_logic_rules( $value['rules'], $form );
					$object[ $prop ] = $logic;
				} elseif ( is_array( $value ) || is_a( $value, 'GF_Field' ) ) {
					$object[ $prop ] = self::convert_conditional_logic_date_field_values( $value, $form );
				}
			}

			return $object;
		}

		public static function convert_conditional_logic_rules( $rules, $form ) {

			foreach ( $rules as &$rule ) {

				$rule_field = GFFormsModel::get_field( $form, $rule['fieldId'] );

				// if this rule is not based on a date field - or - if value is already a valid timestamp, don't convert
				if ( ! $rule_field
					|| GFFormsModel::get_input_type( $rule_field ) !== 'date'
					|| self::is_valid_timestamp( $rule['value'] )
					|| strpos( $rule['value'], '*' ) !== false
				) {
					continue;
				}

				$matches   = self::parse_merge_tags( $rule['value'] );
				$value     = $rule['value'];
				$raw_value = false;

				foreach ( $matches as $match ) {

					list( $full_value, $tag, $modifier ) = array_pad( $match, 3, '' );

					// Check for field merge tags.
					if ( is_numeric( $modifier ) ) {
						$raw_value = $full_value;
						continue;
					}

					$tag = strtolower( $tag );

					switch ( $tag ) {
						case 'today':
							// supports modifier (i.e. '+30 days'), modify time retrieved for {today} by the modifier
							$time  = ! $modifier ? current_time( 'timestamp' ) : strtotime( $modifier, current_time( 'timestamp' ) );
							$value = date( 'Y-m-d', $time );
							break;
						case 'year':
							$time  = ! $modifier ? time() : strtotime( $modifier );
							$year  = date( 'Y', $time );
							$value = str_replace( $full_value, $year, $value );
							break;
						case 'month':
							$time  = ! $modifier ? time() : strtotime( $modifier );
							$month = date( 'n', $time );
							$value = str_replace( $full_value, $month, $value );
							break;
						case 'day':
							$time  = ! $modifier ? time() : strtotime( $modifier );
							$day   = date( 'j', $time );
							$value = str_replace( $full_value, $day, $value );
							break;
						case 'monday':
						case 'tuesday':
						case 'wednesday':
						case 'thursday':
						case 'friday':
						case 'saturday':
						case 'sunday':
							$raw_value = $value;
							break;
						default:
							$value = $rule['value'];
					}
				}

				// some values (like day of the week) should not be converted to dates
				if ( $raw_value ) {
					$rule['value'] = $raw_value;
				} else {
					$rule['value'] = date( 'U', strtotime( $value ) );
				}
			}

			return $rules;
		}

		/**
		 * Thank you @stackoverflow:
		 * http://stackoverflow.com/questions/2524680/check-whether-the-string-is-a-unix-timestamp
		 *
		 * @param mixed $timestamp
		 */
		public static function is_valid_timestamp( $timestamp ) {
			return ( (string) (int) $timestamp === (string) $timestamp )
				&& ( $timestamp <= PHP_INT_MAX )
				&& ( $timestamp >= ~PHP_INT_MAX );
		}

		public static function parse_merge_tags( $value ) {
			preg_match_all( '/{(.*?)(?::(.+?))?}/', $value, $matches, PREG_SET_ORDER );
			return $matches;
		}

	}

}
