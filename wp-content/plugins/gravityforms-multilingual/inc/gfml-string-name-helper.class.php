<?php

class GFML_String_Name_Helper {

	const SANITIZE_STRING_MAX_LENGTH = 128;
	const CHOICE_VALUE_SUFFIX        = '-value';

	/** @var array */
	public $field_choice;

	/** @var int */
	public $field_choice_index;

	/** @var array */
	public $confirmation;

	/** @var \GF_Field */
	public $field;

	/** @var array */
	public $field_input;

	/** @var string */
	public $field_key;

	/** @var array */
	public $notification;

	/** @var int|string */
	public $page_index;

	private function get_field_placeholder_parts() {
		$string_name_parts   = [];
		$string_name_parts[] = $this->field->type;
		$string_name_parts[] = $this->field->id;
		$string_name_parts[] = 'placeholder';

		return $string_name_parts;
	}

	private function get_field_customLabel_parts() {
		$string_name_parts   = [];
		$string_name_parts[] = $this->field->type;
		$string_name_parts[] = $this->field->id;
		$string_name_parts[] = 'customLabel';

		return $string_name_parts;
	}

	private function get_field_input_placeholder_parts() {
		$string_name_parts   = $this->get_field_placeholder_parts();
		$string_name_parts[] = 'input';
		$string_name_parts[] = $this->field_input['id'];

		return $string_name_parts;
	}

	private function get_field_input_customLabel_parts() {
		$string_name_parts   = $this->get_field_customLabel_parts();
		$string_name_parts[] = 'input';
		$string_name_parts[] = $this->field_input['id'];

		return $string_name_parts;
	}

	public function sanitize_string( $string_name, $max_length = self::SANITIZE_STRING_MAX_LENGTH ) {
		$string_name = preg_replace( '/[ \[\]]+/', '-', $string_name );
		if ( mb_strlen( $string_name ) > $max_length ) {
			$string_name = mb_substr( $string_name, 0, $max_length );
		}

		return $string_name;
	}

	public function get_field_post_category() {
		return $this->sanitize_string( "field-{$this->field->id}-categoryInitialItem" );
	}

	public function get_field_address_copy_values_option() {
		return $this->sanitize_string( "field-{$this->field->id}-copyValuesOptionLabel" );
	}

	public function get_field_post_custom_field() {
		return $this->sanitize_string( "field-{$this->field->id}-customFieldTemplate" );
	}

	public function get_field_page_previousButton() {
		return $this->sanitize_string( 'page-' . ( intval( $this->field->pageNumber ) - 1 ) . "-previousButton-{$this->field_key}" );
	}

	public function get_field_page_nextButton() {
		return $this->sanitize_string( 'page-' . ( intval( $this->field->pageNumber ) - 1 ) . "-nextButton-{$this->field_key}" );
	}

	public function get_field_html() {
		return $this->sanitize_string( "field-{$this->field->id}-content" );
	}

	public function get_field_multi_input_choice_text() {
		$sanitized_value = sanitize_text_field( $this->field_choice['text'] );
		return $this->sanitize_string(
			"{$this->field->type}-{$this->field->id}-choice-{$this->field_choice_index}-{$sanitized_value}",
			self::SANITIZE_STRING_MAX_LENGTH - mb_strlen( self::CHOICE_VALUE_SUFFIX )
		);
	}

	public function get_field_multi_input_choice_value() {
		return $this->get_field_multi_input_choice_text() . self::CHOICE_VALUE_SUFFIX;
	}

	public function get_form_confirmation_message() {
		return $this->sanitize_string( 'field-confirmation-message_' . $this->confirmation['name'] );
	}

	public function get_form_confirmation_page_id() {
		return $this->sanitize_string( 'confirmation-page_' . $this->confirmation['name'] );
	}

	public function get_form_notification_subject() {
		return $this->sanitize_string( 'notification-subject_' . $this->notification['name'] );
	}

	public function get_form_notification_message() {
		return $this->sanitize_string( 'field-notification-message_' . $this->notification['name'] );
	}

	public function get_field_validation_message() {
		return $this->sanitize_string( 'field-' . $this->field['id'] . '-errorMessage' );
	}

	public function get_field_common() {
		return $this->sanitize_string( "field-{$this->field->id}-{$this->field_key}" );
	}

	public function get_form_pagination_page_title() {
		return $this->sanitize_string( 'page-' . ( intval( $this->page_index ) + 1 ) . '-title' );
	}

	public function get_form_pagination_completion_text() {
		return $this->sanitize_string( 'progressbar_completion_text' );
	}

	public function get_form_pagination_last_page_button_text() {
		return $this->sanitize_string( 'lastPageButton' );
	}

	public function get_form_confirmation_redirect_url() {
		return $this->sanitize_string( 'confirmation-redirect_' . $this->confirmation['name'] );
	}

	public function get_field_placeholder() {
		$string_name_parts = $this->get_field_placeholder_parts();
		$string_name       = implode( '-', $string_name_parts );
		$string_name       = $this->sanitize_string( $string_name );

		return $string_name;
	}

	public function get_field_input_placeholder() {
		$string_name_parts = $this->get_field_input_placeholder_parts();
		$string_name       = implode( '-', $string_name_parts );
		$string_name       = $this->sanitize_string( $string_name );

		return $string_name;
	}

	public function get_field_input_customLabel() {
		$string_name_parts = $this->get_field_input_customLabel_parts();
		$string_name       = implode( '-', $string_name_parts );
		$string_name       = $this->sanitize_string( $string_name );

		return $string_name;
	}

	public function get_form_submit_button() {
		return $this->sanitize_string( 'form_submit_button' );
	}

	public function get_form_title() {
		return $this->sanitize_string( 'form_title' );
	}

	public function get_form_description() {
		return $this->sanitize_string( 'form_description' );
	}

	public function get_form_save_and_continue_later_text() {
		return $this->sanitize_string( 'form_save_and_continue_later_text' );
	}

	public function get_conditional_rule( $index, $rule ) {
		return $this->sanitize_string( $this->field->id . '-rule-' . $index . '-' . $rule['operator'] );
	}

}
