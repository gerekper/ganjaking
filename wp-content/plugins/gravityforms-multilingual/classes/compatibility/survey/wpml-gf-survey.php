<?php

/**
 * Class WPML_GF_Survey
 *
 * Compatibility class for Gravity Forms Survey Add-On
 */
class WPML_GF_Survey implements \IWPML_Backend_Action, \IWPML_Frontend_Action, \IWPML_DIC_Action {

	const FIELD_TYPE              = 'survey';
	const LIKERT_ROWS_KEY         = 'gsurveyLikertRows';
	const LIKERT_ROWS_NAME_PREFIX = 'survey-likert-rows-';

	/** @var GFML_TM_API $gfml_tm_api */
	private $gfml_tm_api;

	/** @var GFML_String_Name_Helper $string_name_helper */
	private $string_name_helper;

	public function __construct( GFML_TM_API $gfml_tm_api, GFML_String_Name_Helper $string_name_helper ) {
		$this->gfml_tm_api        = $gfml_tm_api;
		$this->string_name_helper = $string_name_helper;
	}

	public function add_hooks() {
		add_action( 'wpml_gf_register_strings_field_survey', [ $this, 'register_field_strings' ], 10, 3 );

		if ( ! is_admin() ) {
			add_filter( 'gform_pre_render', [ $this, 'gform_pre_render' ], 10 );
		}
	}

	/**
	 * @param array $form
	 *
	 * @return array
	 */
	public function gform_pre_render( $form ) {
		if ( $this->has_survey_field( $form ) ) {
			$form_package = $this->gfml_tm_api->get_form_package( $form );
			$form         = $this->translate_strings_for_likert_rows( $form, $form_package );
		}

		return $form;
	}

	private function translate_strings_for_likert_rows( $form, $form_package ) {
		foreach ( $form['fields'] as &$field ) {
			if ( $this->is_likert_rows_type( $field ) ) {
				foreach ( $field->{self::LIKERT_ROWS_KEY} as &$likert_row ) {
					$likert_row['text'] = apply_filters(
						'wpml_translate_string',
						$likert_row['text'],
						$this->get_likert_row_string_name( $likert_row['text'] ),
						$form_package
					);
				}
			}
		}

		return $form;
	}

	/**
	 * @param array    $form
	 * @param stdClass $form_package
	 * @param object   $form_field
	 */
	public function register_field_strings( $form, $form_package, $form_field ) {
		if ( ! $this->is_survey_field( $form_field ) ) {
			return;
		}

		$this->gfml_tm_api->register_strings_field_option( $form_package, $form_field );
		$this->register_strings_for_likert_rows( $form_package, $form_field );
	}

	private function register_strings_for_likert_rows( $form_package, $form_field ) {
		if ( $this->is_likert_rows_type( $form_field ) ) {
			foreach ( $form_field->{self::LIKERT_ROWS_KEY} as $likert_row ) {
				$value = $likert_row['text'];
				$name  = $this->get_likert_row_string_name( $value );
				$this->gfml_tm_api->register_gf_string( $value, $name, $form_package, $name );
			}
		}
	}

	/**
	 * @param string $value
	 *
	 * @return string
	 */
	private function get_likert_row_string_name( $value ) {
		return $this->string_name_helper->sanitize_string( self::LIKERT_ROWS_NAME_PREFIX . $value );
	}

	/**
	 * @param stdClass|GF_Field $form_field
	 *
	 * @return bool
	 */
	private function is_likert_rows_type( $form_field ) {
		return isset( $form_field->{self::LIKERT_ROWS_KEY} );
	}

	/**
	 * @param stdClass|GF_Field $form_field
	 *
	 * @return bool
	 */
	private function is_survey_field( $form_field ) {
		return isset( $form_field->type ) && self::FIELD_TYPE === $form_field->type;
	}

	/**
	 * @param array $form
	 *
	 * @return bool
	 */
	private function has_survey_field( $form ) {
		foreach ( $form['fields'] as $field ) {
			if ( $this->is_survey_field( $field ) ) {
				return true;
			}
		}

		return false;
	}
}
