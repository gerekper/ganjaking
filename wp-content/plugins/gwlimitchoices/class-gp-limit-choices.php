<?php

/**
* TODO
* + add option to hide when limit reached or disable (code is in place, UI to follow)
*       add UI option that only appears when "enable limits" is checked
*/

class GP_Limit_Choices extends GWPerk {

	protected $min_gravity_perks_version = '2.2.3';
	protected $min_gravity_forms_version = '1.9.15';

	public $version = GP_LIMIT_CHOICES_VERSION;
	public $choiceless;

	public static $version_info;
	public static $allowed_field_types = array( 'radio', 'select', 'checkbox', 'multiselect' );
	public static $disabled_choices    = array(); // array( ['form_id'] => array( ['field_id'] => array( choice id, choice id ) ) )
	public static $current_form        = null;

	/**
	 * @var null | array Current submitted entry to pull selected choices when evaluating conditional logic in some
	 *   scenarios where the data is not posted.
	 */
	public static $current_entry = null;

	private static $instance = null;

	public static function get_instance( $perk_file ) {
		if ( null == self::$instance ) {
			self::$instance = new self( $perk_file );
		}
		return self::$instance;
	}

	public function init() {

		load_plugin_textdomain( 'gp-limit-choices', false, basename( dirname( __file__ ) ) . '/languages/' );

		// # Register Scripts

		$this->register_scripts();

		// # Form Rendering

		add_action( 'gform_enqueue_scripts', array( $this, 'enqueue_form_scripts' ) );
		add_action( 'gform_register_init_scripts', array( $this, 'register_init_script' ) );
		add_filter( 'gform_pre_render', array( $this, 'pre_render' ) );
		add_filter( 'gform_pre_render', array( $this, 'add_conditional_logic_support_rules' ) );
		add_filter( 'gform_field_input', array( $this, 'display_choiceless_message' ), 10, 5 );

		// # Form Validation & Submission

		add_filter( 'gform_pre_validation', array( $this, 'set_current_form' ) );
		add_filter( 'gform_validation', array( $this, 'validate' ) );
		add_filter( 'gform_is_value_match', array( $this, 'is_value_match' ), 10, 6 );
		add_filter( 'gform_pre_submission_filter', array( $this, 'add_conditional_logic_support_rules' ) );
		add_action( 'gform_entry_created', array( $this, 'flush_choice_count_cache_post_entry_creation' ), 10, 2 );
		add_filter( 'gform_after_submission', array( $this, 'unset_current_form' ), 20 );

		add_action( 'wp_ajax_nopriv_gfppcp_get_order_data', array( $this, 'set_current_form_gfppcp' ), 1 );
		add_action( 'wp_ajax_gfppcp_get_order_data', array( $this, 'set_current_form_gfppcp' ), 1 );

		add_action( 'gform_post_payment_action', array( $this, 'set_current_form_and_entry_post_payment_action' ), 1 );

		// # Admin

		if ( is_admin() ) {

			$this->enqueue_field_settings();

			add_filter( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		}

	}

	private function register_scripts() {

		$this->register_script( $this->key( 'admin' ), $this->get_base_url() . '/js/admin.js', array( 'jquery', 'gform_form_admin', 'gform_gravityforms' ), $this->version, true );
		wp_localize_script( $this->key( 'admin' ), 'GPLCAdminData', array(
			'allowedFieldTypes' => self::$allowed_field_types,
			'enableCheckbox'    => sprintf( '
				<div style="float:right;margin-right:10px;">
					<input type="checkbox" onclick="SetFieldProperty( \'%s\', this.checked ); GPLCAdmin.toggleEnableLimits();" id="field_choice_limits_enabled">
					<label class="inline gfield_value_label" for="field_choice_limits_enabled">%s</label>
				</div>',
				$this->key( 'enableLimits' ),
				__( 'enable limits', 'gp-limit-choices' )
			),
		) );

		wp_register_style( $this->key( 'admin' ), $this->get_base_url() . "/css/gp-limit-choices-admin.css", array(), $this->version );
		$this->register_noconflict_styles( $this->key( 'admin' ) );

		$this->register_script( $this->key( 'frontend' ), $this->get_base_url() . '/js/frontend.js', array( 'jquery', 'gform_gravityforms', 'gform_conditional_logic' ), $this->version, true );

	}

	public function enqueue_admin_scripts() {

		$is_applicable_page = in_array(
			GFForms::get_page(),
			array(
				'form_editor',
				'form_settings',
				'confirmation',
				'notification_edit',
				'export_entry',
			)
		);

		if ( $is_applicable_page ) {
			wp_enqueue_script( $this->key( 'admin' ) );
			wp_enqueue_style( $this->key( 'admin' ) );
		}

	}

	public function enqueue_form_scripts( $form ) {

		foreach ( $form['fields'] as $field ) {
			if ( $this->is_applicable_field( $field ) ) {
				wp_enqueue_script( $this->key( 'frontend' ) );
				break;
			}
		}

	}

	public function register_init_script( $form ) {

		$data = array();

		foreach ( $form['fields'] as $field ) {

			if ( ! $this->is_applicable_field( $field ) ) {
				continue;
			}

			$choices = $field['choices'];

			foreach ( $choices as &$choice ) {
				$choice['count'] = self::get_choice_count( $choice['value'], $field, $form['id'] );
			}

			$field['choices'] = $choices;

			$data[ $field['id'] ] = array(
				'choices'     => $field['choices'],
				'isExhausted' => in_array( $field['id'], (array) rgar( $this->choiceless, $form['id'] ) ),
			);

		}

		if ( empty( $data ) ) {
			return;
		}

		$args = array(
			'formId' => $form['id'],
			'data'   => $data,
		);

		$script = 'new GPLimitChoices( ' . json_encode( $args ) . ' );';

		GFFormDisplay::add_init_script( $form['id'], $this->slug, GFFormDisplay::ON_PAGE_RENDER, $script );

	}

	public function pre_render( $form ) {

		if ( ! is_array( $form ) ) {
			return $form;
		}

		foreach ( $form['fields'] as &$field ) {

			if ( ! $this->is_applicable_field( $field ) ) {
				continue;
			}

			// Only process the choices once. Some 3rd party plugins call gform_pre_render twice which can cause issues.
			if ( $field->gplcIsProcessed ) {
				break;
			} else {
				$field->gplcIsProcessed = true;
			}

			if ( ! isset( $this->choiceless[ $form['id'] ] ) ) {
				$this->choiceless[ $form['id'] ] = array();
			}

			$field->choices = $this->apply_choice_limits( $field->choices, $field, $form );

		}

		if ( $this->has_disabled_choice( $form ) ) {
			add_filter( 'gform_field_content', array( $this, 'disable_choice' ), 10, 2 );
			add_filter( 'gppa_hydrate_input_html', array( $this, 'disable_choice' ), 10, 2 );
		}

		return $form;
	}

	/**
	 * @param $choices
	 * @param GF_Field $field
	 * @param $form
	 */
	public function apply_choice_limits( $choices, $field, $form, $remove = true ) {

		$filtered_choices = array();
		$choice_counts    = self::get_choice_counts( $form['id'], $field );

		// allows to prevent the removal of choices, validation still occurs
		$remove_choices = apply_filters( "gwlc_remove_choices_{$form['id']}", apply_filters( 'gwlc_remove_choices', $remove, $form['id'], $field['id'] ), $form['id'], $field['id'] );
		$remove_choices = gf_apply_filters( 'gplc_remove_choices', array( $form['id'], $field->id ), $remove_choices, $form['id'], $field['id'] );

		// if choices are not removed, disable by default but allow override
		$disable_choices = gf_apply_filters( 'gplc_disable_choices', array( $form['id'], $field->id ), ! $remove_choices, $form['id'], $field['id'] );

		foreach ( $choices as $choice ) {

			$limit    = $this->get_choice_limit( $choice, $field->formId, $field->id );
			$no_limit = rgblank( $limit );

			if ( $no_limit ) {
				$filtered_choices[] = $choice;
				continue;
			}

			// if choice count is greater than or equal to choice limit, limit has been exceeded
			$value          = $field->sanitize_entry_value( $choice['value'], $form['id'] );
			$choice_count   = intval( rgar( $choice_counts, $value ) );
			$exceeded_limit = $choice_count >= $limit;

			if ( $this->is_edit_view() && in_array( $value, $this->get_selected_values( $field ) ) ) {
				$exceeded_limit = false;
			}

			// add $choice to $disabled_choices, will be used to disable choice via JS
			if ( $exceeded_limit && $disable_choices ) {
				$choice['isDisabled'] = $choice['is_disabled'] = true;
				$choice['isSelected'] = false;
			}

			// provide custom opportunity to modify choices (includes whether the choice has exceeded limit)
			$choice = apply_filters( 'gwlc_pre_render_choice', $choice, $exceeded_limit, $field, $form );
			$choice = gf_apply_filters( array( 'gplc_pre_render_choice', $form['id'], $field->id ), $choice, $exceeded_limit, $field, $form, $choice_count );

			if ( ! $exceeded_limit || ! $remove_choices ) {
				$filtered_choices[] = $choice;
			}
		}

		if ( empty( $filtered_choices ) ) {
			$this->choiceless[ $form['id'] ][] = $field['id'];
		}

		return $filtered_choices;
	}

	public function has_disabled_choice( $form ) {
		foreach ( $form['fields'] as $field ) {
			foreach ( (array) $field->choices as $choice ) {
				if ( rgar( $choice, 'isDisabled' ) ) {
					return true;
				}
			}
		}
		return false;
	}

	// If in an edit view, we don't want to disable the selected choice, providing the user the ability to select another choice - or - reselect the exhausted choice.
	public function is_edit_view() {
		return apply_filters( 'gwlc_is_edit_view', $this->is_gflow_edit_view() || $this->is_gview_edit_view() );
	}

	public function is_gflow_edit_view() {
		return is_callable( 'gravity_flow' ) && gravity_flow()->is_workflow_detail_page();

	}

	public function is_gview_edit_view() {
		return is_callable( 'gravityview_get_context' ) && gravityview_get_context() == 'edit';
	}

	public function get_gview_entry() {
		$gravityview_view = GravityView_View::getInstance();
		$entries          = $gravityview_view->getEntries();
		$entry            = reset( $entries );
		return $entry;
	}

	/**
	 * We need to make sure that when fields are changed that impact our custom conditional logic the conditional logic is triggered.
	 * To this end, we add "fake" rules which will always return true or false (depending on the logic type) to ensure that when
	 * GF creates the 'conditionalLogicFields' property for a trigger field, it will pick up our custom dependency.
	 *
	 * @param $form
	 *
	 * @return $form
	 */
	public function add_conditional_logic_support_rules( $form ) {

		if ( ! is_array( $form ) ) {
			return $form;
		}

		foreach ( $form['fields'] as &$field ) {

			if ( ! is_array( rgar( $field, 'conditionalLogic' ) ) ) {
				continue;
			}

			foreach ( $field['conditionalLogic']['rules'] as $rule ) {

				if ( strpos( $rule['fieldId'], 'gplc_count_remaining_' ) === false ) {
					continue;
				}

				$field_id_bits = explode( '_', $rule['fieldId'] );
				$field_id      = array_pop( $field_id_bits );

				// if ALL rules must match, create rule that will always be true, if ANY, create rule that will always be false
				$value = $field['conditionalLogic']['logicType'] == 'all' ? '__return_true' : '__return_false';

				$conditional_logic = $field['conditionalLogic'];

				$conditional_logic['rules'][] = array(
					'fieldId'  => $field_id,
					'operator' => 'is',
					'value'    => $value,
				);

				$field['conditionalLogic'] = $conditional_logic;

			}
		}

		return $form;
	}

	public function is_value_match( $is_match, $field_value, $target_value, $operation, $source_field, $rule ) {

		if ( $target_value == '__return_true' ) {
			return true;
		} elseif ( $target_value == '__return_false' ) {
			return false;
		}

		// $current_form is set on pre validation and nulled on after submission
		if ( ! self::$current_form ) {
			return $is_match;
		}

		$has_our_tag = strpos( $rule['fieldId'], 'gplc_count_remaining' ) !== false;
		if ( ! $has_our_tag ) {
			return $is_match;
		}

		$field_ids       = explode( '_', $rule['fieldId'] );
		$target_field_id = array_pop( $field_ids );
		$target_field    = GFFormsModel::get_field( self::$current_form, $target_field_id );

		if ( ! $this->is_applicable_field( $target_field ) ) {
			return $is_match;
		}

		$selected_choices = $this->get_selected_choices( $target_field, rgar( self::$current_entry, $target_field->id ) );
		$choice           = array_pop( $selected_choices );

		// Account for no choice being selected (including drop down placeholder option); only applies when comparing to
		// "0" for checking if field is exhausted otherwise we'll assume they're checking for the selected option, which
		// would be "0" anyways since no option is selected.
		if ( ! $choice && $target_value === '0' ) {

			$remaining = $this->is_field_exhausted( $target_field ) ? 0 : 1;

		} else {

			$limit     = intval( $this->get_choice_limit( $choice, $target_field->formId, $target_field->id ) );
			$count     = self::get_choice_count( $choice['value'], $target_field, $target_field['formId'] );
			$remaining = max( $limit - $count, 0 );

		}

		$is_match = GFFormsModel::matches_operation( $remaining, $target_value, $operation );

		return $is_match;
	}

	public function set_current_form( $form ) {
		self::$current_form = $form;
		return $form;
	}

	/**
	 * Set current form when entry is being created for Gravity Forms PayPal Commerce Platform.
	 *
	 * Without this, the remaining count conditional logic in Limit Choices will not be processed and can cause the
	 * PayPal pop-up to not open.
	 */
	public function set_current_form_gfppcp() {
		$this->set_current_form( GFAPI::get_form( absint( rgpost( 'form_id' ) ) ) );
	}

	/**
	 * Set current form for delayed payment feeds like Stripe.
	 *
	 * Without this, the remaining count conditional logic in Limit Choices will not be processed.
	 */
	public function set_current_form_and_entry_post_payment_action( $entry ) {
		self::$current_entry = $entry;
		$this->set_current_form( GFAPI::get_form( $entry['form_id'] ) );
	}

	public function unset_current_form( $return ) {
		self::$current_form = null;
		return $return;
	}

	/**
	 * Prevent synchronous submisssion which would exceed limit.
	 *
	 * @param mixed $validation_result
	 */
	public function validate( $validation_result ) {

		$form                 = $validation_result['form'];
		$has_validation_error = false;

		foreach ( $form['fields'] as &$field ) {

			if ( ! $this->should_validate_field( $field, $form ) ) {
				continue;
			}

			$choices = $this->get_selected_choices( $field );
			if ( empty( $choices ) ) {
				continue;
			}

			// confirm whether choices are removed and/or disabled for valdiation purposes
			$remove_choices  = apply_filters( "gplc_remove_choices_{$form['id']}", apply_filters( 'gplc_remove_choices', true, $form['id'], $field['id'] ), $form['id'], $field['id'] );
			$disable_choices = apply_filters( "gplc_disable_choices_{$form['id']}", apply_filters( 'gplc_disable_choices', ! $remove_choices, $form['id'], $field['id'] ), $form['id'], $field['id'] );

			// if choices are not disabled, bypass validation
			if ( ! $remove_choices && ! $disable_choices ) {
				continue;
			}

			$existing_count = 0;
			if ( $this->is_gview_edit_view() ) {
				$entry          = $this->get_gview_entry();
				$existing_count = $this->get_requested_count( $field, $entry );
			}

			$validation_messages = array();

			foreach ( $choices as $choice ) {

				$limit = $this->get_choice_limit( $choice, $field->formId, $field->id );
				if ( rgblank( $limit ) ) {
					continue;
				}

				$limit            = intval( $limit );
				$count            = self::get_choice_count( $choice['value'], $field, $form['id'] ) - $existing_count;
				$requested_count  = $this->get_requested_count( $field );
				$out_of_stock     = $limit <= $count;
				$not_enough_stock = $limit < $count + $requested_count;
				$remaining_count  = $limit - $count;

				if ( ! ( $out_of_stock && $requested_count > 0 ) && ! $not_enough_stock && $limit != 0 ) {
					continue;
				}

				// passed to the label hooks
				$inventory_data = array(
					'limit'     => $limit,
					'count'     => $count,
					'requested' => $requested_count,
					'remaining' => $remaining_count,
				);

				if ( $out_of_stock ) {

					$out_of_stock_message = __( 'The choice, "%s", which you have selected is no longer available.', 'gp-limit-choices' );
					/**
					 * Filter validation message when the item is out of stock.
					 *
					 * @since 1.6
					 *
					 * @param string $out_of_stock_message Validation message.
					 * @param array  $form                 Form Object
					 * @param array  $field                Field Object
					 * @param array  $inventory_data       Includes the limit, count, requested count and remaining count.
					 *
					 * @example url https://gist.github.com/spivurno/3dbf8bf204b46031f7ec
					 */
					$out_of_stock_message = gf_apply_filters( 'gplc_out_of_stock_message', array( $form['id'], $field->id ), $out_of_stock_message, $form, $field, $inventory_data );
					$message              = sprintf( $out_of_stock_message, $choice['text'] );

				} elseif ( $not_enough_stock ) {

					if ( $field->type == 'option' ) {
						$not_enough_stock_message = _n( 'You selected this option for %1$d items but only %2$d of this option is available.', 'You selected this option for %1$d items but only %2$d of this option are available.', $remaining_count, 'gp-limit-choices' );
					} else {
						$not_enough_stock_message = _n( 'You selected %1$d of this item but only %2$d is available.', 'You selected %1$d of this item but only %2$d are available.', $remaining_count, 'gp-limit-choices' );
					}
					/**
					 * Filter validation message when the item has stock available but not as many as the requested amount.
					 *
					 * @since 1.6
					 *
					 * @param string $not_enough_stock_message Validation message.
					 * @param array  $form                     Form Object
					 * @param array  $field                    Field Object
					 * @param array  $inventory_data           Includes the limit, count, requested count and remaining count.
					 *
					 * @example url https://gist.github.com/spivurno/a0b3bc833a1b7ced93eb
					 */
					$not_enough_stock_message = gf_apply_filters( 'gplc_not_enough_stock_message', array( $form['id'], $field->id ), $not_enough_stock_message, $form, $field, $inventory_data );
					$message                  = sprintf( $not_enough_stock_message, $requested_count, $remaining_count );

				}

				$validation_messages[] = $message;

			}

			if ( ! empty( $validation_messages ) ) {
				$has_validation_error        = true;
				$field['failed_validation']  = true;
				$field['validation_message'] = implode( '<br />', $validation_messages );
			}
		}

		$validation_result['form']     = $form;
		$validation_result['is_valid'] = $validation_result['is_valid'] && ! $has_validation_error;

		return $validation_result;
	}

	public function get_choice_limit( $choice, $form_id, $field_id ) {
		/**
		 * Filter the choice limit for a given choice.
		 *
		 * @since 1.1.2
		 *
		 * @param int   $limit    The current choice's limit.
		 * @param array $choice   The current choice array.
		 * @param int   $form_id  The current form ID to which the choice belongs.
		 * @param int   $field_id The current field ID to which the choice belongs.
		 */
		return gf_apply_filters( array( 'gplc_choice_limit', $form_id, $field_id ), rgar( $choice, 'limit' ), $choice, $form_id, $field_id );
	}

	public function disable_choice( $content, $field ) {

		$field_type = GFFormsModel::get_input_type( $field );
		if ( ! in_array( $field_type, self::$allowed_field_types ) ) {
			return $content;
		}

		foreach ( $field['choices'] as $choice_id => $choice ) {

			if ( ! rgar( $choice, 'isDisabled' ) ) {
				continue;
			}

			if ( is_array( $field['inputs'] ) ) {
				foreach ( $field['inputs'] as $input_index => $input ) {
					if ( $input_index == $choice_id ) {
						$pieces    = explode( '.', $input['id'] );
						$choice_id = $pieces[1];
						break;
					}
				}
			}

			switch ( $field_type ) {
				case 'multiselect':
				case 'select':
					if ( in_array( $field['type'], array( 'product', 'option' ) ) ) {
						$price = GFCommon::to_number( $choice['price'] ) === false ? 0 : GFCommon::to_number( $choice['price'] );
						$value = sprintf( '%s|%s', $choice['value'], $price );
					} else {
						$value = $choice['value'];
					}
					$value  = esc_attr( $value );
					$search = "<option value='{$value}'";
					break;
				default:
					if ( version_compare( GFCommon::$version, '1.8.20.7', '>=' ) ) {
						$choice_html_id = "choice_{$field['formId']}_{$field['id']}_{$choice_id}";
					} else {
						$choice_html_id = "choice_{$field['id']}_{$choice_id}";
					}
					$search = "id='{$choice_html_id}'";
					break;
			}

			$replace = "$search disabled='disabled' class='gwlc-disabled'";
			$content = str_replace( $search, $replace, $content );

		}

		return $content;
	}

	public function is_applicable_field( $field ) {

		$is_allowed_field_type = in_array( GFFormsModel::get_input_type( $field ), self::$allowed_field_types );
		$are_limits_enabled    = rgar( $field, $this->key( 'enableLimits' ) );

		return $is_allowed_field_type && $are_limits_enabled;
	}

	public function should_validate_field( $field, $form ) {
		$page_number  = GFFormDisplay::get_source_page( $field->formId );
		$is_last_page = GFFormDisplay::get_target_page( $form, $page_number, array() ) == '0';
		return $this->is_applicable_field( $field ) && ( $is_last_page || $page_number == $field->pageNumber );
	}

	public function display_choiceless_message( $input, $field, $value, $lead_id, $form_id ) {

		if ( GFForms::get_page() || ! isset( $this->choiceless[ $form_id ] ) || ! in_array( $field['id'], $this->choiceless[ $form_id ] ) ) {
			return $input;
		}

		$message = sprintf( '<p class="choiceless">%s<p>', __( 'There are no options available for this field.', 'gp-limit-choices' ) );

		/**
		 * Filter the message that is displayed when a field has no available choices.
		 *
		 * @param string    $message The markup to be displayed when a field had no available choices.
		 * @param \GF_Field $field   The current field.
		 * @param int       $form_id The current form ID.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'gplc_choiceless_message', $message, $field, $form_id );
	}

	public function get_selected_choices( $field, $values = false ) {

		if ( ! $values ) {
			$values = $this->get_selected_values( $field );
		} elseif ( ! is_array( $values ) ) {
			$values = array( $values );
		}

		$choices = array();

		foreach ( $field['choices'] as $choice ) {
			if ( in_array( $choice['value'], $values ) ) {
				$choices[] = $choice;
			}
		}

		return $choices;
	}

	public function get_selected_values( $field ) {

		// On the Gravity Flow detail page, we want to get the values from the $entry rather than the $_POST.
		if ( $this->is_gflow_edit_view() ) {
			$entry  = GFAPI::get_entry( rgget( 'lid' ) );
			$values = GFFormsModel::get_lead_field_value( $entry, $field );
		} elseif ( $this->is_gview_edit_view() ) {
			$entry  = $this->get_gview_entry();
			$values = GFFormsModel::get_lead_field_value( $entry, $field );
		} else {
			$values = apply_filters( 'gwlc_selected_values', GFFormsModel::get_field_value( $field ), $field );
		}

		if ( ! is_array( $values ) ) {
			$values = array( $values );
		}

		$values = array_filter( $values, array( $this, 'not_blank' ) );

		if ( $this->is_pricing_field( $field ) ) {
			foreach ( $values as &$value ) {
				$value = $this->remove_price( $value );
			}
		}

		return $values;
	}

	// Allow field values to include a vertical pipe "|" character
	// Example real-world value: 20-24lbs | $10
	private function remove_price( $value ) {
		if ( strlen( $value ) < 1 ) {
			return $value;
		}
		$value = explode( '|', $value );
		switch ( sizeof( $value ) ) {
			case 1:
				return $value;
				break;
			default:
				array_pop( $value );

				return join( '|', $value );
				break;
		}
	}

	public function not_blank( $value ) {
		return ! rgblank( $value );
	}

	public function is_pricing_field( $field ) {
		return GFCommon::is_pricing_field( $field['type'] );
	}

	public function is_field_exhausted( $field ) {
		return isset( $this->choiceless[ $field['formId'] ] ) && in_array( $field['id'], $this->choiceless[ $field['formId'] ] );
	}

	public function get_requested_count( $field, $entry = false ) {

		$requested_count = 1;

		if ( gp_limit_choices()->is_pricing_field( $field ) ) {
			$quantity_input_id = $this->get_product_quantity_input_id( $field );
			if ( $quantity_input_id ) {
				if ( $entry ) {
					$requested_count = rgar( $entry, $quantity_input_id );
				} else {
					$requested_count = rgpost( sprintf( 'input_%s', str_replace( '.', '_', $quantity_input_id ) ) );
				}
			}
		}

		return apply_filters( 'gplc_requested_count', intval( $requested_count ), $field );
	}

	/**
	 * Get the Quantity field or Product field where quantity ordered will be provided.
	 *
	 * @param $product_field
	 *
	 * @return bool
	 */
	public function get_product_quantity_field( $product_field ) {

		$form            = GFAPI::get_form( $product_field->formId );
		$product_field   = $product_field->type == 'product' ? $product_field : GFFormsModel::get_field( $form, $product_field->productField );
		$quantity_fields = GFCommon::get_product_fields_by_type( $form, array( 'quantity' ), $product_field->id );

		if ( isset( $quantity_fields[0] ) ) {
			$quantity_field = $quantity_fields[0];
		} else {
			// if no quantity field is found, the product field will have the quantity inline
			$quantity_field = $product_field;
		}

		return $quantity_field;
	}

	public function get_product_quantity_input_id( $product_field ) {

		$quantity_field = $this->get_product_quantity_field( $product_field );

		if ( $quantity_field->type == 'quantity' ) {
			$quantity_input_id = $quantity_field->id;
		} elseif ( in_array( GFFormsModel::get_input_type( $quantity_field ), array( 'singleproduct', 'calculation' ) ) ) {
			$quantity_input_id = "{$quantity_field->id}.3";
		} else {
			$quantity_input_id = false;
		}

		return $quantity_input_id;
	}

	public static function get_choice_count( $value, $field, $form_id ) {

		$counts = self::get_choice_counts( $form_id, $field );

		if ( gp_limit_choices()->is_pricing_field( $field ) ) {
			$value = rgar( explode( '|', $value ), 0 );
			$value = wp_kses( rgar( explode( '|', $value ), 0 ), wp_kses_allowed_html( 'post' ) );
		} elseif ( version_compare( GFForms::$version, '2.0', '>=' ) ) {
			$value = $field->sanitize_entry_value( $value, $form_id );
		}

		return intval( rgar( $counts, $value ) );
	}

	public static function get_choice_counts( $form_id, $field ) {
		global $wpdb;

		if ( version_compare( GFForms::$version, '2.3-beta-1', '<' ) ) {
			return self::get_choice_counts_pre_2_3( $form_id, $field );
		}

		if ( is_integer( $field ) ) {
			$form  = GFFormsModel::get_form_meta( $form_id );
			$field = GFFormsModel::get_field( $form, $field );
		}

		$cache_key = sprintf( 'gplc_choice_counts_%d_%d', $form_id, $field['id'] );
		$result    = GFCache::get( $cache_key );
		if ( $result !== false ) {
			return $result;
		}

		$is_pricing_field = gp_limit_choices()->is_pricing_field( $field );
		$counts           = array();

		$query = array(
			'select' => 'SELECT em.entry_id, em.meta_key, em.meta_value',
			'from'   => $wpdb->prepare( "FROM {$wpdb->prefix}gf_entry e INNER JOIN {$wpdb->prefix}gf_entry_meta em ON em.entry_id = e.id AND (em.meta_key = %s OR em.meta_key LIKE %s)", $field['id'], $wpdb->esc_like( $field['id'] ) . '.%' ),
			'join'   => '',
			'where'  => $wpdb->prepare( "
                WHERE e.status = 'active'
                AND em.form_id = %d",
				$form_id
			),
		);

		if ( $is_pricing_field ) {
			$quantity_input_id = gp_limit_choices()->get_product_quantity_input_id( $field );
			if ( $quantity_input_id ) {
				$query['select'] .= ', em2.meta_value as quantity';
				$query['join']   .= $wpdb->prepare( "LEFT OUTER JOIN {$wpdb->prefix}gf_entry_meta em2 ON em2.entry_id = e.id AND em2.meta_key = %s", $quantity_input_id );
			}
		}

		$approved_payments_only = apply_filters( "gwlc_approved_payments_only_{$form_id}", apply_filters( 'gwlc_approved_payments_only', false ) );
		$approved_payments_only = apply_filters( "gplc_completed_payments_only_{$form_id}", apply_filters( 'gplc_completed_payments_only', $approved_payments_only ) );

		if ( $approved_payments_only ) {
			$query['where'] .= " AND ( e.payment_status = 'Approved' OR e.payment_status = 'Paid' OR e.payment_status is null )";
		}

		$query   = apply_filters( "gwlc_choice_counts_query_{$form_id}", apply_filters( 'gwlc_choice_counts_query', $query, $field ), $field );
		$sql     = implode( ' ', $query );
		$results = $wpdb->get_results( $sql, ARRAY_A );

		foreach ( $results as $choice ) {

			if ( $is_pricing_field ) {
				$value            = $field->sanitize_entry_value( rgar( explode( '|', $choice['meta_value'] ), 0 ), $form_id );
				$quantity         = isset( $quantity_input_id ) && $quantity_input_id ? $choice['quantity'] : 1;
				$counts[ $value ] = isset( $counts[ $value ] ) ? $counts[ $value ] + $quantity : $quantity;
			} elseif ( GFFormsModel::get_input_type( $field ) == 'multiselect' ) {
				// New versions of GF store multiselect values as a JSON string.
				$values = json_decode( $choice['meta_value'] );
				// Older versions of Gravity Forms store the multiselect values as a comma-delimited list.
				if ( ! $values ) {
					$values = explode( ',', $choice['meta_value'] );
				}
				if ( ! is_array( $values ) ) {
					continue;
				}
				foreach ( $values as $value ) {
					$value            = $field->sanitize_entry_value( $value, $form_id );
					$counts[ $value ] = isset( $counts[ $value ] ) ? $counts[ $value ] + 1 : 1;
				}
			} else {
				$value            = $field->sanitize_entry_value( $choice['meta_value'], $form_id );
				$counts[ $value ] = isset( $counts[ $value ] ) ? $counts[ $value ] + 1 : 1;
			}
		}

		/**
		 * Filter the choice counts for the given field.
		 *
		 * @param array    $counts Associative array of the choice counts (i.e. array( 'First Choice' => 1 )).
		 * @param array    $form   Current form object.
		 * @param GF_Field $field  Current field object.
		 */
		$counts = gf_apply_filters( array( 'gplc_choice_counts', $form_id, $field->id ), $counts, $form_id, $field );

		GFCache::set( $cache_key, $counts );

		return $counts;
	}

	public static function get_choice_counts_pre_2_3( $form_id, $field ) {
		global $wpdb;

		if ( is_integer( $field ) ) {
			$form  = GFFormsModel::get_form_meta( $form_id );
			$field = GFFormsModel::get_field( $form, $field );
		}

		$cache_key = sprintf( 'gplc_choice_counts_%d_%d', $form_id, $field['id'] );
		$result    = GFCache::get( $cache_key );
		if ( $result !== false ) {
			return $result;
		}

		$is_pricing_field = gp_limit_choices()->is_pricing_field( $field );
		$counts           = array();

		$query = array(
			'select' => 'SELECT ld.lead_id, ld.field_number, ld.value',
			'from'   => $wpdb->prepare( "FROM {$wpdb->prefix}rg_lead l INNER JOIN {$wpdb->prefix}rg_lead_detail ld ON ld.lead_id = l.id AND floor( ld.field_number ) = %d", $field['id'] ),
			'join'   => '',
			'where'  => $wpdb->prepare( "
                WHERE l.status = 'active'
                AND ld.form_id = %d",
				$form_id
			),
		);

		if ( $is_pricing_field ) {
			$quantity_input_id = gp_limit_choices()->get_product_quantity_input_id( $field );
			if ( $quantity_input_id ) {
				$query['select'] .= ', ld2.value as quantity';
				$query['join']   .= $wpdb->prepare( "LEFT OUTER JOIN {$wpdb->prefix}rg_lead_detail ld2 ON ld2.lead_id = l.id AND CAST( ld2.field_number as CHAR ) = %s", $quantity_input_id );
			}
		}

		$approved_payments_only = apply_filters( "gwlc_approved_payments_only_{$form_id}", apply_filters( 'gwlc_approved_payments_only', false ) );
		$approved_payments_only = apply_filters( "gplc_completed_payments_only_{$form_id}", apply_filters( 'gplc_completed_payments_only', $approved_payments_only ) );

		if ( $approved_payments_only ) {
			$query['where'] .= " AND ( l.payment_status = 'Approved' OR l.payment_status = 'Paid' OR l.payment_status is null )";
		}

		$query   = apply_filters( "gwlc_choice_counts_query_{$form_id}", apply_filters( 'gwlc_choice_counts_query', $query, $field ), $field );
		$sql     = implode( ' ', $query );
		$results = $wpdb->get_results( $sql, ARRAY_A );

		foreach ( $results as $choice ) {

			/*
			 * It appears the long table has been deprecated.
			 *
			 * if( strlen( $choice['value'] ) >= GFORMS_MAX_FIELD_LENGTH - 10 ) {
				$entry = array( 'id' => $choice['lead_id'] );
				$long_value = GFFormsModel::get_field_value_long( $entry, $choice['field_number'], array(), false );
				$choice['value'] = ! empty( $long_value ) ? $long_value : $choice['value'];
			}*/

			if ( $is_pricing_field ) {
				//$value            = wp_kses( rgar( explode( '|', $choice['value'] ), 0 ), wp_kses_allowed_html( 'post' ) );
				$value            = $field->sanitize_entry_value( rgar( explode( '|', $choice['value'] ), 0 ), $form_id );
				$quantity         = isset( $quantity_input_id ) && $quantity_input_id ? $choice['quantity'] : 1;
				$counts[ $value ] = isset( $counts[ $value ] ) ? $counts[ $value ] + $quantity : $quantity;
			} elseif ( GFFormsModel::get_input_type( $field ) == 'multiselect' ) {
				// New versions of GF store multiselect values as a JSON string.
				$values = json_decode( $choice['value'] );
				// Older versions of Gravity Forms store the multiselect values as a comma-delimited list.
				if ( ! $values ) {
					$values = explode( ',', $choice['value'] );
				}
				foreach ( $values as $value ) {
					$value            = version_compare( GFForms::$version, '2.0', '>=' ) ? $field->sanitize_entry_value( $value, $form_id ) : $value; // changed with GF2.0? //wp_kses( $value, wp_kses_allowed_html( 'post' ) );
					$counts[ $value ] = isset( $counts[ $value ] ) ? $counts[ $value ] + 1 : 1;
				}
			} else {
				$value            = version_compare( GFForms::$version, '2.0', '>=' ) ? $field->sanitize_entry_value( $choice['value'], $form_id ) : $choice['value']; // changed with GF2.0? //wp_kses( $choice['value'], wp_kses_allowed_html( 'post' ) );
				$counts[ $value ] = isset( $counts[ $value ] ) ? $counts[ $value ] + 1 : 1;
			}
		}

		GFCache::set( $cache_key, $counts );

		return $counts;
	}

	public static function flush_choice_count_cache_post_entry_creation( $entry, $form ) {
		self::flush_choice_count_cache( $form );
	}

	public static function flush_choice_count_cache( $form ) {
		foreach ( $form['fields'] as $field ) {
			$cache_key = sprintf( 'gplc_choice_counts_%d_%d', $form['id'], $field['id'] );
			GFCache::delete( $cache_key );
		}
	}

	public static function get_disabled_choices( $form_id = false, $field_id = false ) {

		if ( ! $form_id && ! $field_id ) {
			return self::$disabled_choices;
		}

		if ( $form_id && $field_id ) {
			return isset( self::$disabled_choices[ $form_id ][ $field_id ] ) ? self::$disabled_choices[ $form_id ][ $field_id ] : array();
		}

		if ( $form_id ) {
			return isset( self::$disabled_choices[ $form_id ] ) ? self::$disabled_choices[ $form_id ] : array();
		}

		return array();
	}

	public static function set_disabled_choice( $choice, $form_id, $field_id ) {

		$choices   = self::get_disabled_choices( $form_id, $field_id );
		$choices[] = $choice;

		if ( ! isset( self::$disabled_choices[ $form_id ] ) ) {
			self::$disabled_choices[ $form_id ] = array();
		}

		if ( ! isset( self::$disabled_choices[ $form_id ][ $field_id ] ) ) {
			self::$disabled_choices[ $form_id ][ $field_id ] = array();
		}

		self::$disabled_choices[ $form_id ][ $field_id ] = $choices;

	}

	/**
	 * Get the number of entries left. Not used internally.
	 *
	 * @param $form_id
	 * @param $field_id
	 * @param $value     the value of the desired choice.
	 *
	 * @return int|string
	 */
	public static function get_entries_left( $form_id, $field_id, $value ) {

		$form    = GFFormsModel::get_form_meta( $form_id );
		$field   = GFFormsModel::get_field( $form, $field_id );
		$choices = gp_limit_choices()->get_selected_choices( $field, $value );
		$choice  = reset( $choices );
		$limit   = gp_limit_choices()->get_choice_limit( (array) $choice, $field->formId, $field->id );

		if ( ! $choice || ! $limit ) {
			return __( 'unlimited', 'gp-limit-choices' );
		}

		$count = self::get_choice_count( $value, $field, $form_id );

		if ( $limit > $count ) {
			return $limit - $count;
		}

		return 0;
	}


	/**
	 * Get the selected choice by field or field and value.
	 *
	 * @deprecated 1.3.1
	 * @deprecated Use get_selected_choices()
	 *
	 * @param       $field
	 * @param mixed $value
	 *
	 * @return bool
	 */
	public static function get_selected_choice( $field, $value = false ) {

		_deprecated_function( __FUNCTION__, '1.3.1', 'get_selected_choices()' );

		if ( ! $value ) {
			$value = GFFormsModel::get_field_value( $field );
			$value = gp_limit_choices()->is_pricing_field( $field ) ? rgar( explode( '|', $value ), 0 ) : $value;
		}

		foreach ( $field['choices'] as $choice ) {
			if ( $choice['value'] == $value ) {
				return $choice;
			}
		}

		return false;
	}

}

function gp_limit_choices() {
	return GWLimitChoices::get_instance( null );
}

class GWLimitChoices extends GP_Limit_Choices { }
