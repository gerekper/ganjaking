<?php
defined('WYSIJA') or die('Restricted access');
/*
Class WJ_FieldRender
It is able to render Custom Fields in a form.
*/

class WJ_FieldRender {

	// WJ_FieldUser Object.
	private $field_user;
	// The column, like 'cf_1'
	private $identifier;
	// Name of the field.
	private $name;
	// Value of the field.
	private $value;

	public function __construct( $field_user ) {
		$this->field_user = $field_user;
		$this->identifier = $field_user->column_name();
		$this->name = esc_attr( $field_user->field->name );
		$this->value = esc_attr( $field_user->value() );
	}

	public function validation() {
		$rules = array();
		$validation_class = '';

		// If there are any validations, add correct class names.
		if ( ! empty( $this->field_user->field->settings['validate'] ) ) {
			$validation_value = $this->field_user->field->settings['validate'];
			$rules[] = 'custom[' . $validation_value . ']';
		}

		// If there's any validation, join all classes.
		if ( ! empty( $rules ) ) {
			$validation_class = 'class="validate[' . join( ',', $rules ) . ']"';
		}

		return $validation_class;
	}

	/*
	Returns the HTML label for this field, to be used
	in a form.
	$field_render->label();
	# => '<label ...>Address</label>'
	*/
	public function label() {
		$form_field_name = $this->identifier;
		$label =
		'<label for="' . $form_field_name . '">' .
			$this->name .
			'</label>';
		return $label;
	}

	/*
	Returns the input related to the type of the field,
	to be used in a form.
	$field_render->input();
	# => '<input type=..../>
	*/
	public function input() {
		$input = '';

		switch ( $this->field_user->field->type ) {
			case 'input':
				$input =
					'<input type="text" size="40" id="'. $this->identifier . '"' .
					' value="' . $this->value .
					'" name="wysija[field][' . $this->identifier . ']" ' .
					$this->validation() .
					' />';
				break;
			case 'textarea':
				$input =
					'<textarea id="'. $this->identifier . '"' .
					'name="wysija[field][' . $this->identifier . ']" ' .
					$this->validation() .
					' />' .
					$this->value .
					'</textarea>';
				break;
			case 'checkbox':
				if ( empty( $this->field_user->field->settings['values'] ) ){
					$input = esc_attr__( 'This field contains no values', WYSIJA );
					break;
				}
				$check_value = '';
				$label = $this->field_user->field->settings['values'][0]['value'];
				if ( $this->value == 1 ) {
					$check_value = ' checked="checked"';
				}
				$input =
					'<label for="' . $this->identifier . '">' .
					'<input type="hidden" '.
					$check_value .
					' name="wysija[field][' . $this->identifier . ']"' .
					' value="0" ' .
					' />' .
					'<input type="checkbox" id="'. $this->identifier . '"' .
					$check_value .
					' name="wysija[field][' . $this->identifier . ']"' .
					' value="1" ' .
					$this->validation() .
					' />' . $label . '</label>';
				break;
			case 'radio':
				$field = $this->field_user->field;
				if ( empty( $this->field_user->field->settings['values'] ) ){
					$input = esc_attr__( 'This field contains no values', WYSIJA );
					break;
				}
				foreach ( $field->settings['values'] as $index => $content ) {
					$check_value = '';
					if ( $this->value == $content['value'] ) {
						$check_value = 'checked="checked"';
					}
					$local_identifier = $this->identifier . '_' . $content['value'];
					$input .=
						'<label for="' . $local_identifier . '">' .
						'<input type="radio" id="'. $local_identifier . '"' .
						$check_value .
						' name="wysija[field][' . $this->identifier . ']"' .
						' value="' . $content['value'] . '" />' .
						$content['value'] .
						'</label>';
				}
				break;
			case 'select':
				if ( empty( $this->field_user->field->settings['values'] ) ){
					$input = esc_attr__( 'This field contains no values', WYSIJA );
					break;
				}

				$input = '<select id="' . $this->identifier . '"' .
					' name="wysija[field][' . $this->identifier . ']" ' .
					$this->validation() .
					' />';
				$field = $this->field_user->field;
				foreach ( $field->settings['values'] as $index => $content ) {
					$check_value = '';
					if ( $this->value == $content['value'] ) {
						$check_value = 'selected="selected"';
					}
					$input .=
						'<option ' . $check_value .
						' value="' . $content['value'] . '" >' .
						$content['value'] .
						'</option>';
				}
				$input .= '</select>';
				break;

			case 'date':
				// get date format from field settings
				$field = $this->field_user->field;

				// get timestamp value
				$value = (int) $this->value;

				// get date type (defaults to year + month + day)
				$date_type = ( isset( $field->settings['date_type'] ) ) ? $field->settings['date_type'] : 'year_month_day';
				// get an array of all required date components (year, month, day)
				$display_date_fields = explode( '_', $date_type );
				// form engine to get date data
				$helper_form_engine = WYSIJA::get( 'form_engine', 'helper' );
				$date_order = explode('/', $field->settings['date_order']);

				foreach ($date_order as $date_element) {
					if (strpos($date_element, 'yy') !== false) {
						// year selection
						if ( in_array( 'year', $display_date_fields ) ) {
							$years = $helper_form_engine->get_years();

							$selected_year = null;
							if ( $value !== null ) {
								$selected_year = (int) strftime( '%Y', $value );
							}

							// select
							$input .= '<select name="wysija[field]['.$this->identifier.'][year]">';
							$input .= '<option value="">' . __( 'Year' ) .'</option>';
							foreach ( $years as $year ) {
								$is_selected = ((int)$year['year'] === $selected_year) ? ' selected="selected"' : '';
								$input .= '<option value="'.$year['year'].'"'.$is_selected.'>'.$year['year'].'</option>';
							}
							$input .= '</select>';
						}
					} elseif (strpos($date_element, 'mm') !== false) {
						// month selection
						if ( in_array( 'month', $display_date_fields ) ) {
							$months = $helper_form_engine->get_months();

							$selected_month = null;
							if ( $value !== null ) {
								$selected_month = (int) strftime( '%m', $value );
							}

							// select
							$input .= '<select name="wysija[field]['.$this->identifier.'][month]">';
							$input .= '<option value="">' . __( 'Month' ) . '</option>';
							foreach ( $months as $month ) {
								$is_selected = ((int)$month['month'] === $selected_month) ? ' selected="selected"' : '';
								$input .= '<option value="'.$month['month'].'"'.$is_selected.'>'.$month['month_name'].'</option>';
							}
							$input .= '</select>';
						}
					} elseif (strpos($date_element, 'dd') !== false) {
						// day selection
						if ( in_array( 'day', $display_date_fields ) ) {
							$days = $helper_form_engine->get_days();

							$selected_day = null;
							if ( $value !== null ) {
								$selected_day = (int) strftime( '%d', $value );
							}

							// select
							$input .= '<select name="wysija[field]['.$this->identifier.'][day]">';
							$input .= '<option value="">' . __( 'Day' ) . '</option>';
							foreach ( $days as $day ) {
								$is_selected = ((int)$day['day'] === $selected_day) ? ' selected="selected"' : '';
								$input .= '<option value="'.$day['day'].'"'.$is_selected.'>'.$day['day'].'</option>';
							}
							$input .= '</select>';
						}
					}
				}

			break;
			default:
				$input = '';
				break;
		}
		return $input;
	}

	/*
	Render all custom fields in a table, given the user id.
	$field_render::render_all(1);
	# => '<tr><th><label...<input...</td></tr>'
	*/
	public static function render_all( $user_id ) {
		$fields = WJ_FieldUser::get_all( $user_id );
		if ( isset( $fields ) ) {
			$output = '';
			foreach ( $fields as $field ) {
				$field_render = new self($field);
				$output .=
					'<tr>' .
					'<th scope="row">' .
					$field_render->label() .
					'</th><td>' .
					$field_render->input() .
					'</td></tr>';
			}
			return $output;
		} else {
			return null;
		}
	}


}

