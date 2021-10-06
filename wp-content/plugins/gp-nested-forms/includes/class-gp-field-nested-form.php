<?php

class GP_Field_Nested_Form extends GF_Field {

	public $type = 'form';

	public function get_form_editor_field_title() {
		return esc_attr__( 'Nested Form', 'gp-nested-forms' );
	}

	public function get_form_editor_button() {
		return array(
			'group' => 'advanced_fields',
			'text'  => $this->get_form_editor_field_title(),
		);
	}

	public function get_form_editor_field_settings() {
		return array(
			'conditional_logic_field_setting',
			'prepopulate_field_setting',
			'error_message_setting',
			'label_setting',
			'admin_label_setting',
			'visibility_setting',
			'description_setting',
			'label_placement_setting',
			'css_class_setting',
			'gpnf-setting',
			'gpnf-modal-header-color-setting',
			'gpnf-entry-limits-setting',
			'gpnf-feed-processing-setting',
		);
	}

	public function get_field_input( $form, $value = '', $entry = null ) {

		if ( $this->is_entry_detail_edit() ) {
			ob_start();
			?>
			<div class="ginput_container gpnf-no-edit">
				<?php esc_html_e( 'Nested Form fields cannot be edited. Edit the child entry to make changes.', 'gp-nested-forms' ); ?>
				<input name="input_<?php echo $this->id; ?>" type="hidden" value="<?php echo $value; ?>" />
			</div>
			<?php
			return ob_get_clean();
		}

		$nested_form_id = rgar( $this, 'gpnfForm' );

		$nested_form = $nested_form_id ? GFAPI::get_form( $nested_form_id ) : false;
		if ( $nested_form && GFForms::get_page() !== 'form_editor' && rgpost( 'action' ) !== 'rg_refresh_field_preview' ) {
			$nested_form = gf_apply_filters( array( 'gform_pre_render', $nested_form_id ), GFAPI::get_form( $nested_form_id ), false, null );
		}

		$nested_field_ids = rgar( $this, 'gpnfFields', array() );
		$column_count     = count( $nested_field_ids ) + 1; // + 1 for actions column

		// Show warning message when form/fields need to be configured
		if ( ! $nested_field_ids ) {
			// GF 2.5 border color
			$border_color = ( version_compare( GFForms::$version, '2.5.0', '>=' ) ) ? '#ddd' : '#D2E0EB';
			return sprintf(
				'<div class="gpnf-nested-entries-container ginput_container"><p style=" border: 1px dashed %s; border-radius: 3px; padding: 1rem; background: #fff; "><strong style="color: #ca4a1f;">%s</strong><br>%s</p></div>',
				$border_color,
				__( 'Configuration Required', 'gp-nested-forms' ),
				__( 'Use the Nested Form and Summary Fields settings to choose the form and fields to display in this Nested Form field.', 'gp-nested-forms' )
			);
		}

		// Sanitize value.
		$value = $this->santize_nested_form_field_value( $value );

		// Get existing entries.
		$entries = array();
		if ( ! empty( $value ) ) {
			$entries = gp_nested_forms()->get_entries( $value );
		}

		$tabindex         = GFCommon::get_tabindex();
		$add_button_label = sprintf( __( 'Add %s', 'gp-nested-forms' ), $this->get_item_label() );

		// return parsed template content
		$args = array(
			'template'           => 'nested-entries',
			'field'              => $this,
			'nested_form'        => $nested_form,
			'nested_fields'      => ! empty( $nested_form ) ? gp_nested_forms()->get_fields_by_ids( $nested_field_ids, $nested_form ) : array(),
			'nested_field_ids'   => $nested_field_ids,
			'column_count'       => $column_count,
			'value'              => $value,
			'entries'            => $entries,
			'add_button'         => $this->get_add_button( $form['id'], rgar( $nested_form, 'id' ), $tabindex, $add_button_label ),
			'add_button_message' => $this->get_add_button_max_message( $form['id'], rgar( $nested_form, 'id' ) ),
			'tabindex'           => $tabindex,
			'labels'             => array(
				'no_entries'      => sprintf( __( 'There are no %1$s%2$s.%3$s', 'gp-nested-forms' ), '<span>', $this->get_items_label(), '</span>' ),
				'add_entry'       => $add_button_label,
				'edit_entry'      => __( 'Edit', 'gp-nested-forms' ),
				'duplicate_entry' => __( 'Duplicate', 'gp-nested-forms' ),
				'delete_entry'    => __( 'Delete', 'gp-nested-forms' ),
			),
		);

		/**
		 * Filter the arguments that will be used to render the Nested Form field template.
		 *
		 * @since 1.0
		 *
		 * @param array $args {
		 *
		 *     @var string               $template             The template file to be rendered.
		 *     @var GP_Field_Nested_Form $field                The current Nested Form field.
		 *     @var array                $nested_form          The nested form object for the current Nested Form field.
		 *     @var array                $nested_fields        Any array of field objects on the nested form that will be displayed on the parent form.
		 *     @var array                $nested_field_ids     The field IDs from the nested form that will be displayed on the parent form.
		 *     @var string               $value                A comma-delimited list of entry IDs that have been submitted for this Nested Form field and session.
		 *     @var array                $entries              An array of entries that have been submitted for this Nested Form field and session.
		 *     @var string               $add_button           An HTML button that allows users to load the nested form modal and add new entries to the Nested Form field.
		 *     @var int                  $column_count         The number of columns (based on $nested_field_ids count plus one for the actions column).
		 *     @var string               $tabindex             The Gravity Forms tabindex string, if tabindex is enabled.
		 *     @var string               $related_entries_link An HTML link to the Entry List view, filtered by entries for the current Nested Form field and parent entry.
		 *     @var array                $labels               An array of miscellaneous UI labels that will be used when rendering the template ('no_entries', 'add_entry', 'edit_entry', 'delete_entry').
		 *
		 * }
		 *
		 * @param GP_Field_Nested_Form $field The current Nested Form field
		 */
		$args = gf_apply_filters( array( 'gpnf_template_args', $this->formId, $this->id ), $args, $this );

		// Update 'add_button' if 'add_entry' label is changed via filter.
		if ( $args['labels']['add_entry'] != $add_button_label ) {
			$args['add_button'] = $this->get_add_button( $form['id'], $nested_form['id'], $tabindex, $args['labels']['add_entry'] );
		}

		$template = new GP_Template( gp_nested_forms() );
		$markup   = $template->parse_template(
			gp_nested_forms()->get_template_names( $args['template'], $form['id'], $this->id ),
			true,
			false,
			$args
		);

		// Apppend the input that is actual used to interact with Gravity Forms.
		$markup .= sprintf(
			'<input type="hidden"
                name="input_%d"
                id="input_%d_%d"
                data-bind="value: entryIds"
                value="%s" />',
			$this->id,
			$this->formId,
			$this->id,
			$value
		);

		return $markup;
	}

	public function santize_nested_form_field_value( $value ) {
		return implode( ',', gp_nested_forms()->get_child_entry_ids_from_value( $value ) );
	}

	public function get_add_button( $form_id, $nested_form_id, $tabindex, $label ) {
		return sprintf(
			'
			<button type="button" class="gpnf-add-entry"
		        data-formid="%s"
		        data-nestedformid="%s"
				data-bind="attr: { disabled: isMaxed }, css: { \'gf-default-disabled\': isMaxed }"
				%s>
				%s
			</button>',
			$form_id,
			$nested_form_id,
			$tabindex,
			esc_html( $label )
		);
	}

	public function get_add_button_max_message( $form_id, $nested_form_id ) {
		$message = sprintf( __( 'Maximum number of %s reached.', 'gp-nested-forms' ), strtolower( $this->get_items_label() ) );

		if ( $this->is_form_editor() ) {
			return null;
		}

		return sprintf(
			'
			<p class="gpnf-add-entry-max" data-bind="if: isMaxed">
				%s
			</p>',
			gf_apply_filters( array( 'gpnf_add_button_max_message', $form_id, $nested_form_id ), $message )
		);
	}

	public function get_value_entry_list( $value, $entry, $field_id, $columns, $form ) {

		$field            = GFAPI::get_field( $form, $field_id );
		$template         = new GP_Template( gp_nested_forms() );
		$entries          = gp_nested_forms()->get_entries( $value );
		$nested_form_id   = rgar( $this, 'gpnfForm' );
		$nested_form      = gp_nested_forms()->get_nested_form( $nested_form_id );
		$nested_field_ids = rgar( $this, 'gpnfFields' );

		$args = array(
			'template'      => 'nested-entries-count',
			'entries'       => $entries,
			'entry_count'   => count( $entries ),
			'label_plural'  => $this->get_items_label(),
			'nested_fields' => gp_nested_forms()->get_fields_by_ids( $nested_field_ids, $nested_form ),
			'nested_form'   => $nested_form,
		);

		$markup = $template->parse_template(
			gp_nested_forms()->get_template_names( $args['template'], $field->formId, $field->id ),
			true,
			false,
			$args
		);

		return $markup;

	}

	public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {

		$entries = gp_nested_forms()->get_entries( $value );
		if ( empty( $entries ) ) {
			return $value;
		}

		$nested_form_id   = rgar( $this, 'gpnfForm' );
		$nested_form      = gp_nested_forms()->get_nested_form( $nested_form_id );
		$nested_field_ids = rgar( $this, 'gpnfFields' );
		$column_count     = count( $nested_field_ids ) + 1; // + 1 for actions column

		if ( ! $nested_field_ids ) {
			return '';
		}

		$template = 'nested-entries-detail';

		if ( $this->should_use_simple_detail_template() ) {
			$template = 'nested-entries-detail-simple';
		} elseif ( $this->should_use_count_template() ) {
			$template = 'nested-entries-count';
		}

		$related_entries_link = '';

		// Related entries requires login, hide them if this is a public view.
		if ( is_admin() ) {
			$related_entries_link = sprintf(
				'<a class="gpnf-related-entries-link" href="%s">%s</a>',
				add_query_arg(
					array(
						'page'                       => 'gf_entries',
						'id'                         => $nested_form['id'],
						GPNF_Entry::ENTRY_PARENT_KEY => rgget( 'lid' ),
						GPNF_Entry::ENTRY_NESTED_FORM_FIELD_KEY => $this->id,
					),
					admin_url( 'admin.php' )
				),
				sprintf( __( 'View Expanded %s List', 'gp-nested-forms' ), $this->get_item_label() )
			);
		}

		$actions = array(
			'related_entries' => $related_entries_link,
		);

		// return parsed template content
		$args = array(
			'template'             => $template,
			'field'                => $this,
			'nested_form'          => $nested_form,
			'nested_fields'        => gp_nested_forms()->get_fields_by_ids( $nested_field_ids, $nested_form ),
			'nested_field_ids'     => $nested_field_ids,
			'value'                => $value,
			'entries'              => $entries,
			'entry_count'          => count( $entries ),
			'label_plural'         => $this->get_items_label(),
			'column_count'         => $column_count,
			'related_entries_link' => $related_entries_link,
			'actions'              => $actions,
			'labels'               => array(
				'view_entry' => __( 'View Entry', 'gp-nested-forms' ),
			),
		);

		/**
		 * Filter the arguments that will be used to render the Nested Form field template.
		 *
		 * @since 1.0
		 *
		 * @param array $args {
		 *
		 *     @var string               $template             The template file to be rendered.
		 *     @var GP_Field_Nested_Form $field                The current Nested Form field.
		 *     @var array                $nested_form          The nested form object for the current Nested Form field.
		 *     @var array                $nested_fields        Any array of field objects on the nested form that will be displayed on the parent entry.
		 *     @var array                $nested_field_ids     The field IDs from the nested form that will be displayed on the parent entry.
		 *     @var string               $value                A comma-delimited list of entry IDs that have been submitted for this Nested Form field and parent entry.
		 *     @var array                $entries              An array of entries that have been submitted for this Nested Form field and parent entry.
		 *     @var int                  $column_count         The number of columns (based on $nested_field_ids count plus one for the actions column).
		 *     @var array                $actions              An array of HTML links to be output as action items for the child entry list.
		 *     @var array                $labels               An array of miscellaneous UI labels that will be used when rendering the template.
		 *
		 * }
		 *
		 * @param GP_Field_Nested_Form $field The current Nested Form field
		 */
		$args = gf_apply_filters( array( 'gpnf_template_args', $this->formId, $this->id ), $args, $this );

		if ( $actions['related_entries'] != $related_entries_link ) {
			$actions['related_entries'] = $related_entries_link;
		}

		$template = new GP_Template( gp_nested_forms() );
		$markup   = $template->parse_template(
			gp_nested_forms()->get_template_names( $args['template'], $this->formId, $this->id ),
			true,
			false,
			$args
		);

		// Apppend the input that is actual used to interact with Gravity Forms.
		$markup .= sprintf(
			'<input type="hidden"
                name="input_%d"
                id="input_%d_%d"
                value="%s" />',
			$this->id,
			$this->formId,
			$this->id,
			$value
		);

		return $markup;
	}

	public function should_use_simple_detail_template() {

		$is_woocommerce = is_callable( 'is_woocommerce' ) && ( is_cart() || is_checkout() || is_view_order_page() );
		$is_gravityview = $this->is_gravityview();
		$is_print_page  = rgget( 'gf_page' ) == 'print-entry';

		return $is_woocommerce || $is_print_page || $is_gravityview;

	}

	public function is_gravityview() {
		return function_exists( 'gravityview' ) && gravityview()->request->is_entry();
	}

	public function should_use_count_template() {

		$is_gravityview = function_exists( 'gravityview' ) && gravityview()->request->is_view();

		return $is_gravityview;

	}

	public function get_item_labels() {

		$item_labels = wp_parse_args(
			array_filter(
				array(
					'singular' => $this->gpnfEntryLabelSingular,
					'plural'   => $this->gpnfEntryLabelPlural,
				)
			),
			array(
				'singular' => __( 'Entry', 'gp-nested-forms' ),
				'plural'   => __( 'Entries', 'gp-nested-forms' ),
			)
		);

		/**
		 * Filter the label used to identify entries in a Nested Form field.
		 *
		 * @since 1.0
		 *
		 * @param $item_labels {
		 *
		 *     @var string $singular Label used to identify a single entry (e.g. Car).
		 *     @var string $plural   Label used to identify more than one entry (e.g. Cars).
		 *
		 * }
		 */
		$item_labels = gf_apply_filters( array( 'gpnf_item_labels', $this->formId, $this->id ), $item_labels );

		return $item_labels;
	}

	public function get_item_label() {
		return rgar( $this->get_item_labels(), 'singular' );
	}

	public function get_items_label() {
		return rgar( $this->get_item_labels(), 'plural' );
	}

	public function validate( $value, $form ) {

		if ( empty( $value ) ) {
			$entry_count = 0;
			$entry_ids   = array();
		} else {
			$entry_ids   = gp_nested_forms()->get_child_entry_ids_from_value( $value );
			$entry_count = count( $entry_ids );
		}

		/**
		 * Filter the minimum number of entries required by this Nested Form field.
		 *
		 * @param int                   $entry_limit_min The minimum entry limit.
		 * @param int                   $entry_count     The number of child entries submitted via this Nested Form field.
		 * @param array                 $entry_ids       An array of child entry IDs that have been submitted via this Nested Form field.
		 * @param \GP_Field_Nested_Form $this            The current GP_Nested_Form_Field object.
		 * @param array                 $form            The current form object.
		 *
		 * @since 1.0-beta-8
		 */
		$minimum = apply_filters( 'gpnf_entry_limit_min', $this->gpnfEntryLimitMin, $entry_count, $entry_ids, $this, $form );
		if ( ! rgblank( $minimum ) && $entry_count < $minimum ) {
			$this->failed_validation = true;
			// Translators: %1$d is replaced with the minimum. %2$s is replaced with the label for an individual entry.
			$this->validation_message = sprintf( __( 'Please enter a minimum of %1$d %2$s', 'gp-nested-forms' ), $minimum, $minimum > 1 ? $this->get_items_label() : $this->get_item_label() );
		}

		/**
		 * Filter the maximum number of entries required by this Nested Form field.
		 *
		 * @param int                   $entry_limit_max The maximum entry limit.
		 * @param int                   $entry_count     The number of child entries submitted via this Nested Form field.
		 * @param array                 $entry_ids       An array of child entry IDs that have been submitted via this Nested Form field.
		 * @param \GP_Field_Nested_Form $this            The current GP_Nested_Form_Field object.
		 * @param array                 $form            The current form object.
		 *
		 * @since 1.0-beta-8
		 */
		$maximum = apply_filters( 'gpnf_entry_limit_max', $this->gpnfEntryLimitMax, $entry_count, $entry_ids, $this, $form );
		if ( ! rgblank( $maximum ) && $entry_count > $maximum ) {
			$this->failed_validation = true;
			// Translators: %1$d is replaced with the maximum. %2$s is replaced with the label for an individual entry.
			$this->validation_message = sprintf( __( 'Please enter a maximum of %1$d %2$s', 'gp-nested-forms' ), $maximum, $maximum > 1 ? $this->get_items_label() : $this->get_item_label() );
		}

	}

}

class GP_Nested_Form_Field extends GP_Field_Nested_Form { }

GF_Fields::register( new GP_Field_Nested_Form() );
