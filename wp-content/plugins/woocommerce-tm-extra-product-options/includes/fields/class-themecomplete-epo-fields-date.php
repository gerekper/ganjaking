<?php
/**
 * Date Field class
 *
 * @package Extra Product Options/Fields
 * @version 6.0
 * phpcs:disable PEAR.NamingConventions.ValidClassName
 */

defined( 'ABSPATH' ) || exit;

/**
 * Date Field class
 *
 * @package Extra Product Options/Fields
 * @version 6.0
 */
class THEMECOMPLETE_EPO_FIELDS_date extends THEMECOMPLETE_EPO_FIELDS {

	/**
	 * Display field array
	 *
	 * @param array $element The element array.
	 * @param array $args Array of arguments.
	 * @since 1.0
	 */
	public function display_field( $element = [], $args = [] ) {

		$name = $args['posted_name'];
		$id   = $args['id'];

		$tm_epo_global_datepicker_theme    = ! empty( THEMECOMPLETE_EPO()->tm_epo_global_datepicker_theme ) ? THEMECOMPLETE_EPO()->tm_epo_global_datepicker_theme : ( isset( $element['theme'] ) ? $element['theme'] : 'epo' );
		$tm_epo_global_datepicker_size     = ! empty( THEMECOMPLETE_EPO()->tm_epo_global_datepicker_size ) ? THEMECOMPLETE_EPO()->tm_epo_global_datepicker_size : ( isset( $element['theme_size'] ) ? $element['theme_size'] : 'medium' );
		$tm_epo_global_datepicker_position = ! empty( THEMECOMPLETE_EPO()->tm_epo_global_datepicker_position ) ? THEMECOMPLETE_EPO()->tm_epo_global_datepicker_position : ( isset( $element['theme_position'] ) ? $element['theme_position'] : 'normal' );

		$tranlation_day   = ! empty( $element['tranlation_day'] ) ? $element['tranlation_day'] : '';
		$tranlation_month = ! empty( $element['tranlation_month'] ) ? $element['tranlation_month'] : '';
		$tranlation_year  = ! empty( $element['tranlation_year'] ) ? $element['tranlation_year'] : '';

		$style       = isset( $element['button_type'] ) ? $element['button_type'] : '';
		$defaultdate = isset( $element['default_value'] ) ? $element['default_value'] : '';
		$format      = ! empty( $element['format'] ) ? $element['format'] : 0;

		$end_year   = ! empty( $element['end_year'] ) ? $element['end_year'] : ( gmdate( 'Y' ) + 10 );
		$start_year = ! empty( $element['start_year'] ) ? $element['start_year'] : 1900;
		$end_year   = absint( $end_year );
		$start_year = absint( $start_year );

		if ( $end_year < $start_year ) {
			$end_year = $start_year;
		}

		$data             = THEMECOMPLETE_EPO()->get_date_format( $format );
		$date_format      = $data['element_date_format'];
		$date_placeholder = $data['date_placeholder'];
		$date_mask        = $data['date_mask'];

		if ( apply_filters( 'wc_epo_display_rtl', is_rtl() ) ) {
			$date_format      = strrev( $date_format );
			$date_placeholder = strrev( $date_placeholder );
			$date_mask        = strrev( $date_mask );
		}

		$input_type = 'text';
		$showon     = 'both';
		if ( '' === $style ) {
			$input_type       = 'hidden';
			$showon           = 'focus';
			$date_mask        = '';
			$date_placeholder = '';
		}

		$picker_html = '';
		if ( 'picker' !== $style ) {
			if ( isset( $_REQUEST[ $name ] ) && empty( $this->post_data ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$value = str_replace( '.', '-', wp_unslash( $_REQUEST[ $name ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$value = str_replace( '/', '-', $value );
				$value = explode( '-', $value );
				if ( ! isset( $value[0] ) ) {
					$value[0] = '';
				}
				if ( ! isset( $value[1] ) ) {
					$value[1] = '';
				}
				if ( ! isset( $value[2] ) ) {
					$value[2] = '';
				}
				switch ( $format ) {
					case '0':
					case '2':
					case '4':
						$_REQUEST[ $name . '_day' ]   = $value[0];
						$_REQUEST[ $name . '_month' ] = $value[1];
						$_REQUEST[ $name . '_year' ]  = $value[2];
						break;
					case '1':
					case '3':
					case '5':
						$_REQUEST[ $name . '_day' ]   = $value[1];
						$_REQUEST[ $name . '_month' ] = $value[0];
						$_REQUEST[ $name . '_year' ]  = $value[2];
						break;
				}
			}

			$select_array     = [
				'class' => 'tmcp-date-select tmcp-date-day',
				'id'    => $id . '_day',
				'name'  => $name . '_day' . ( ! empty( $args['repeater'] ) ? '[' . $args['get_posted_key'] . ']' : '' ),
				'atts'  => [ 'data-tm-date' => $id ],
			];
			$select_options   = [];
			$tranlation_day   = ( ! empty( $tranlation_day ) ) ? $tranlation_day : esc_html__( 'Day', 'woocommerce-tm-extra-product-options' );
			$select_options[] = [
				'text'  => $tranlation_day,
				'value' => '',
			];
			for ( $i = 1; 31 + 1 !== $i; ++$i ) {
				$select_options[] = [
					'text'  => $i,
					'value' => $i,
				];
			}
			$selectedvalue = isset( $_REQUEST[ $name . '_day' ] ) ? wp_unslash( $_REQUEST[ $name . '_day' ] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if ( is_array( $selectedvalue ) ) {
				if ( isset( $selectedvalue[ $args['get_posted_key'] ] ) ) {
					$selectedvalue = $selectedvalue[ $args['get_posted_key'] ];
				}
			}
			$day_html = THEMECOMPLETE_EPO_HTML()->create_dropdown( $select_array, $select_options, $selectedvalue, 1, 0 );

			$select_array     = [
				'class' => 'tmcp-date-select tmcp-date-month',
				'id'    => $id . '_month',
				'name'  => $name . '_month' . ( ! empty( $args['repeater'] ) ? '[' . $args['get_posted_key'] . ']' : '' ),
				'atts'  => [ 'data-tm-date' => $id ],
			];
			$select_options   = [];
			$tranlation_month = ( ! empty( $tranlation_month ) ) ? $tranlation_month : esc_html__( 'Month', 'woocommerce-tm-extra-product-options' );
			$select_options[] = [
				'text'  => $tranlation_month,
				'value' => '',
			];

			global $wp_locale;
			for ( $i = 1; 12 + 1 !== $i; ++ $i ) {
				$select_options[] = [
					'text'  => $wp_locale->get_month( $i ),
					'value' => $i,
				];
			}
			$selectedvalue = isset( $_REQUEST[ $name . '_month' ] ) ? wp_unslash( $_REQUEST[ $name . '_month' ] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if ( is_array( $selectedvalue ) ) {
				if ( isset( $selectedvalue[ $args['get_posted_key'] ] ) ) {
					$selectedvalue = $selectedvalue[ $args['get_posted_key'] ];
				}
			}
			$month_html = THEMECOMPLETE_EPO_HTML()->create_dropdown( $select_array, $select_options, $selectedvalue, 1, 0 );

			$select_array     = [
				'class' => 'tmcp-date-select tmcp-date-year',
				'id'    => $id . '_year',
				'name'  => $name . '_year' . ( ! empty( $args['repeater'] ) ? '[' . $args['get_posted_key'] . ']' : '' ),
				'atts'  => [ 'data-tm-date' => $id ],
			];
			$select_options   = [];
			$tranlation_year  = ( ! empty( $tranlation_year ) ) ? $tranlation_year : esc_html__( 'Year', 'woocommerce-tm-extra-product-options' );
			$select_options[] = [
				'text'  => $tranlation_year,
				'value' => '',
			];
			for ( $i = $end_year; $i !== $start_year - 1; -- $i ) {
				$select_options[] = [
					'text'  => $i,
					'value' => $i,
				];
			}
			$selectedvalue = isset( $_REQUEST[ $name . '_year' ] ) ? wp_unslash( $_REQUEST[ $name . '_year' ] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if ( is_array( $selectedvalue ) ) {
				if ( isset( $selectedvalue[ $args['get_posted_key'] ] ) ) {
					$selectedvalue = $selectedvalue[ $args['get_posted_key'] ];
				}
			}
			$year_html = THEMECOMPLETE_EPO_HTML()->create_dropdown( $select_array, $select_options, $selectedvalue, 1, 0 );

			switch ( $format ) {
				case '0':
				case '2':
				case '4':
					if ( apply_filters( 'wc_epo_display_rtl', is_rtl() ) ) {
						$picker_html = $year_html . $month_html . $day_html;
					} else {
						$picker_html = $day_html . $month_html . $year_html;
					}

					break;
				case '1':
				case '3':
				case '5':
					if ( apply_filters( 'wc_epo_display_rtl', is_rtl() ) ) {
						$picker_html = $year_html . $day_html . $month_html;
					} else {
						$picker_html = $month_html . $day_html . $year_html;
					}

					break;
			}
		}

		$get_default_value = '';
		if ( ! isset( $defaultdate ) ) {
			$defaultdate       = null;
			$get_default_value = $defaultdate;
		} else {
			if ( '' !== $defaultdate ) {
				$get_default_value = $defaultdate;
				if ( is_numeric( $defaultdate ) ) {
					$get_default_value = new DateTime( 'now' );
					if ( floatval( $defaultdate ) > 0 ) {
						$get_default_value->add( new DateInterval( 'P' . abs( floatval( $defaultdate ) ) . 'D' ) );
					} else {
						$get_default_value->sub( new DateInterval( 'P' . abs( floatval( $defaultdate ) ) . 'D' ) );
					}
					$get_default_value = $get_default_value->format( str_ireplace( 'dd', 'd', str_ireplace( 'mm', 'm', str_ireplace( 'yy', 'Y', $date_format ) ) ) );
					$date_errors       = DateTime::getLastErrors();
					if ( ! empty( $date_errors['error_count'] ) ) {
						$get_default_value = $defaultdate;
					}
				}
			}
		}

		$class_label = '';
		if ( THEMECOMPLETE_EPO()->tm_epo_select_fullwidth === 'yes' ) {
			$class_label = ' fullwidth';
		}

		return apply_filters(
			'wc_epo_display_field_date',
			[
				'date_format'         => $date_format,
				'input_type'          => $input_type,
				'showon'              => $showon,
				'date_mask'           => $date_mask,
				'date_placeholder'    => $date_placeholder,
				'picker_html'         => $picker_html,
				'textbeforeprice'     => $this->get_value( $element, 'text_before_price', '' ),
				'textafterprice'      => $this->get_value( $element, 'text_after_price', '' ),
				'hide_amount'         => $this->get_value( $element, 'hide_amount', '' ),
				'style'               => $style,
				'format'              => $format,
				'start_year'          => $start_year,
				'end_year'            => $end_year,
				'min_date'            => isset( $element['min_date'] ) ? $element['min_date'] : '',
				'max_date'            => isset( $element['max_date'] ) ? $element['max_date'] : '',
				'disabled_dates'      => ! empty( $element['disabled_dates'] ) ? $element['disabled_dates'] : '',
				'enabled_only_dates'  => ! empty( $element['enabled_only_dates'] ) ? $element['enabled_only_dates'] : '',
				'exlude_disabled'     => isset( $element['exlude_disabled'] ) ? $element['exlude_disabled'] : '',
				'disabled_weekdays'   => isset( $element['disabled_weekdays'] ) ? $element['disabled_weekdays'] : '',
				'disabled_months'     => isset( $element['disabled_months'] ) ? $element['disabled_months'] : '',
				'tranlation_day'      => $tranlation_day,
				'tranlation_month'    => $tranlation_month,
				'tranlation_year'     => $tranlation_year,
				'quantity'            => $this->get_value( $element, 'quantity', '' ),
				'defaultdate'         => $defaultdate,
				'get_default_value'   => $this->get_default_value( $element, $args ),
				'date_theme'          => $tm_epo_global_datepicker_theme,
				'date_theme_size'     => $tm_epo_global_datepicker_size,
				'date_theme_position' => $tm_epo_global_datepicker_position,
				'class_label'         => $class_label,
			],
			$this,
			$element,
			$args
		);
	}

	/**
	 * Field validation
	 *
	 * @since 1.0
	 */
	public function validate() {

		$format      = $this->element['format'];
		$data        = THEMECOMPLETE_EPO()->get_date_format( $format );
		$date_format = $data['date_format'] . ' H:i:s';
		$sep         = $data['sep'];

		$passed  = true;
		$message = [];

		$quantity_once = false;
		$min_quantity  = isset( $this->element['quantity_min'] ) ? (int) $this->element['quantity_min'] : 0;
		if ( apply_filters( 'wc_epo_field_min_quantity_greater_than_zero', true ) && $min_quantity < 0 ) {
			$min_quantity = 0;
		}
		foreach ( $this->field_names as $attribute ) {

			$attribute_quantity = $attribute . '_quantity';
			if ( ! $quantity_once && isset( $this->epo_post_fields[ $attribute ] ) && '' !== $this->epo_post_fields[ $attribute ] && isset( $this->epo_post_fields[ $attribute_quantity ] ) && ! ( (int) array_sum( (array) $this->epo_post_fields[ $attribute_quantity ] ) >= $min_quantity ) ) {
				$passed        = false;
				$quantity_once = true;
				/* translators: %1 element label %2 quantity value. */
				$message[] = sprintf( esc_html__( 'The quantity for "%1$s" must be greater than %2$s', 'woocommerce-tm-extra-product-options' ), $this->element['label'], $min_quantity );
			}

			if ( $this->element['required'] ) {
				if ( ! isset( $this->epo_post_fields[ $attribute ] ) || '' === $this->epo_post_fields[ $attribute ] ) {
					$passed    = false;
					$message[] = 'required';
					break;
				}
			}

			if ( ! empty( $this->epo_post_fields[ $attribute ] ) && class_exists( 'DateTime' ) && ( version_compare( phpversion(), '5.3', '>=' ) ) ) {
				$posted_date_value = $this->epo_post_fields[ $attribute ];
				$posted_date_value = (array) $posted_date_value;

				foreach ( $posted_date_value as $posted_date_id => $posted_date ) {

					if ( '' === $posted_date ) {
						continue;
					}
					if ( apply_filters( 'wc_epo_display_rtl', is_rtl() ) ) {
						$posted_date_arr = explode( $sep, $posted_date );
						if ( 3 === count( $posted_date_arr ) ) {
							$posted_date = $posted_date_arr[2] . $sep . $posted_date_arr[1] . $sep . $posted_date_arr[0];
						}
					}
					if ( ! empty( THEMECOMPLETE_EPO()->tm_epo_global_date_timezone ) ) {
						$date = DateTime::createFromFormat( $date_format, $posted_date . ' 00:00:00', new DateTimeZone( THEMECOMPLETE_EPO()->tm_epo_global_date_timezone ) );
					} else {
						$date = DateTime::createFromFormat( $date_format, $posted_date . ' 00:00:00' );
					}
					$date_errors = DateTime::getLastErrors();

					if ( ! empty( $date_errors['error_count'] ) ) {
						$passed    = false;
						$message[] = esc_html__( 'Invalid date entered!', 'woocommerce-tm-extra-product-options' );
						break 2;
					}

					$_year  = $date->format( 'Y' );
					$year   = $_year;
					$_month = $date->format( 'm' );
					$month  = $_month;
					$_day   = $date->format( 'd' );
					$day    = $_day;

					$posted_date_arr = explode( $sep, $posted_date );

					if ( 3 === count( $posted_date_arr ) ) {
						switch ( $format ) {
							case '0':
							case '2':
							case '4':
								$_year  = $posted_date_arr[2];
								$_month = $posted_date_arr[1];
								$_day   = $posted_date_arr[0];
								break;
							case '1':
							case '3':
							case '5':
								$_year  = $posted_date_arr[2];
								$_month = $posted_date_arr[0];
								$_day   = $posted_date_arr[1];
								break;
						}

						if ( $year !== $_year || $month !== $_month || $day !== $_day ) {
							$message[] = esc_html__( 'Invalid data submitted!', 'woocommerce-tm-extra-product-options' );
							$passed    = false;
							break 2;
						}
					}

					if ( checkdate( $_month, $_day, $_year ) ) {
						// valid date.
						$start_year         = (int) $this->element['start_year'] || 1900;
						$end_year           = (int) $this->element['end_year'] || ( gmdate( 'Y' ) + 10 );
						$min_date           = ( '' !== $this->element['min_date'] ) ? ( $this->element['min_date'] ) : false;
						$max_date           = ( '' !== $this->element['max_date'] ) ? ( $this->element['max_date'] ) : false;
						$exlude_disabled    = ( '' !== $this->element['exlude_disabled'] ) ? ( $this->element['exlude_disabled'] ) : false;
						$disabled_dates     = $this->element['disabled_dates'];
						$enabled_only_dates = $this->element['enabled_only_dates'];
						$disabled_weekdays  = $this->element['disabled_weekdays'];
						$disabled_months    = $this->element['disabled_months'];

						$now = new DateTime( '00:00:00' );
						if ( ! empty( THEMECOMPLETE_EPO()->tm_epo_global_date_timezone ) ) {
							$now = new DateTime( '00:00:00', new DateTimeZone( THEMECOMPLETE_EPO()->tm_epo_global_date_timezone ) );
						}
						$now_day   = $now->format( 'd' );
						$now_month = $now->format( 'm' );
						$now_year  = $now->format( 'Y' );

						if ( $enabled_only_dates ) {
							$enabled_only_dates = explode( ',', $enabled_only_dates );
							$_pass              = false;
							foreach ( $enabled_only_dates as $key => $value ) {
								$value = str_replace( '.', '-', $value );
								$value = str_replace( '/', '-', $value );
								$value = explode( '-', $value );
								if ( count( $value ) !== 3 ) {
									continue;
								}
								switch ( $format ) {
									case '0':
									case '2':
									case '4':
										$value = $value[2] . '-' . $value[1] . '-' . $value[0];
										break;
									case '1':
									case '3':
									case '5':
										$value = $value[2] . '-' . $value[0] . '-' . $value[1];
										break;
								}
								$value_to_date = date_create( $value );
								if ( ! $value_to_date ) {
									continue;
								}
								$value    = date_format( $value_to_date, $date_format );
								$temp     = DateTime::createFromFormat( $date_format, $value );
								$interval = $temp->diff( $date );
								$sign     = floatval( $interval->format( '%d%m%Y' ) );
								if ( empty( $sign ) ) {
									$_pass = true;
									break 2;
								}
							}
							$passed = $_pass;
							if ( ! $_pass ) {
								$message[] = esc_html__( 'Invalid date entered!', 'woocommerce-tm-extra-product-options' );
								break 2;
							}
						} else {
							// validate start,end year.
							if ( $_year < $start_year || $_year > $end_year ) {
								$passed    = false;
								$message[] = esc_html__( 'Invalid year date entered!', 'woocommerce-tm-extra-product-options' );
								break 2;
							}

							// validate disabled dates.
							if ( $disabled_dates ) {
								$disabled_dates = explode( ',', $disabled_dates );
								foreach ( $disabled_dates as $key => $value ) {
									$value = str_replace( '.', '-', $value );
									$value = str_replace( '/', '-', $value );
									$value = explode( '-', $value );
									if ( count( $value ) !== 3 ) {
										continue;
									}
									switch ( $format ) {
										case '0':
										case '2':
										case '4':
											$value = $value[2] . '-' . $value[1] . '-' . $value[0];
											break;
										case '1':
										case '3':
										case '5':
											$value = $value[2] . '-' . $value[0] . '-' . $value[1];
											break;
									}
									$value_to_date = date_create( $value );
									if ( ! $value_to_date ) {
										continue;
									}
									$value    = date_format( $value_to_date, $date_format );
									$temp     = DateTime::createFromFormat( $date_format, $value );
									$interval = $temp->diff( $date );
									$sign     = floatval( $interval->format( '%d%m%Y' ) );
									if ( empty( $sign ) ) {
										$passed    = false;
										$message[] = esc_html__( 'You cannot select that date!', 'woocommerce-tm-extra-product-options' );
										break 2;
									}
								}
							}

							// validate minimum date.
							if ( false !== $min_date ) {

								if ( is_numeric( $min_date ) ) {
									if ( $exlude_disabled ) {
										$min_date = $this->correct_date( $min_date );
									}
									$temp = clone $now;
									if ( $min_date > 0 ) {
										$temp->add( new DateInterval( 'P' . abs( $min_date ) . 'D' ) );
									} elseif ( $min_date < 0 ) {
										$temp->sub( new DateInterval( 'P' . abs( $min_date ) . 'D' ) );
									}
								} else {
									$temp = str_replace( '.', '-', $min_date );
									$temp = str_replace( '/', '-', $temp );
									$temp = explode( '-', $temp );
									if ( is_array( $temp ) && isset( $temp[0] ) && isset( $temp[1] ) && isset( $temp[2] ) ) {
										switch ( $format ) {
											case '0':
											case '2':
											case '4':
												$temp = $temp[2] . '-' . $temp[1] . '-' . $temp[0];
												break;
											case '1':
											case '3':
											case '5':
												$temp = $temp[2] . '-' . $temp[0] . '-' . $temp[1];
												break;
										}
										$temp = date_create( $temp );
									} else {
										$temp = false;
									}
									if ( ! $temp ) {
										// failsafe.
										$temp = clone $now;
									} else {
										$temp = date_format( $temp, $date_format );
										$temp = DateTime::createFromFormat( $date_format, $temp );
									}
								}

								$interval = $temp->diff( $date );
								$sign     = $interval->format( '%r' );

								if ( ! empty( $sign ) ) {
									$passed    = false;
									$message[] = esc_html__( 'You cannot select that date!', 'woocommerce-tm-extra-product-options' );
									break 2;
								}
							}

							// validate maximum date.
							if ( false !== $max_date ) {
								if ( is_numeric( $max_date ) ) {
									if ( $exlude_disabled ) {
										$max_date = $this->correct_date( $max_date );
									}
									$temp = clone $now;
									if ( $max_date > 0 ) {
										$temp->add( new DateInterval( 'P' . abs( $max_date ) . 'D' ) );
									} elseif ( $max_date < 0 ) {
										$temp->sub( new DateInterval( 'P' . abs( $max_date ) . 'D' ) );
									}
								} else {
									$temp = str_replace( '.', '-', $max_date );
									$temp = str_replace( '/', '-', $temp );
									$temp = explode( '-', $temp );
									switch ( $format ) {
										case '0':
										case '2':
										case '4':
											$temp = $temp[2] . '-' . $temp[1] . '-' . $temp[0];
											break;
										case '1':
										case '3':
										case '5':
											$temp = $temp[2] . '-' . $temp[0] . '-' . $temp[1];
											break;
									}
									$temp = date_create( $temp );
									if ( ! $temp ) {
										// failsafe todo:proper handling.
										$temp = clone $now;
									} else {
										$temp = date_format( $temp, $date_format );
										$temp = DateTime::createFromFormat( $date_format, $temp );
									}
								}

								$interval = $date->diff( $temp );
								$sign     = $interval->format( '%r' );
								if ( ! empty( $sign ) ) {
									$passed    = false;
									$message[] = esc_html__( 'You cannot select that date!', 'woocommerce-tm-extra-product-options' );
									break 2;
								}
							}
						}
					} else {
						// problem with dates ...
						$passed    = false;
						$message[] = esc_html__( 'Invalid date entered!', 'woocommerce-tm-extra-product-options' );
						break 2;
					}
				}
			}
		}

		return [
			'passed'  => $passed,
			'message' => $message,
		];
	}

	/**
	 * Correct days
	 *
	 * @param mixed $days The days to check.
	 * @return mixed
	 */
	public function correct_date( $days ) {
		if ( is_numeric( $days ) ) {
			$sign = 0 === $days ? $days : ( $days > 0 ? 1 : -1 );
			if ( 0 !== $sign ) {
				$now               = new DateTime( '00:00:00' );
				$test_date         = clone $now;
				$count             = 1;
				$added             = false;
				$no_of_days_to_add = abs( $days );
				while ( $count <= $no_of_days_to_add ) {
					if ( false === $added ) {
						$added = 0;
					}
					if ( $sign > 0 ) {
						$test_date->add( new DateInterval( 'P' . abs( $sign ) . 'D' ) );
					} elseif ( $sign < 0 ) {
						$test_date->sub( new DateInterval( 'P' . abs( $sign ) . 'D' ) );
					}
					$added++;
					$get_day = (int) $test_date->format( 'w' );
					if ( 0 !== $get_day && 6 !== $get_day ) {
						$count++;
					}
				}
				if ( false !== $added ) {
					$days = $added * $sign;
				}
			}
		}
		return $days;
	}

}
