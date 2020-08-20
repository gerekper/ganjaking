<?php
/**
 * Date Field class
 *
 * @package Extra Product Options/Fields
 * @version 4.9
 */

defined( 'ABSPATH' ) || exit;

class THEMECOMPLETE_EPO_FIELDS_date extends THEMECOMPLETE_EPO_FIELDS {

	/**
	 * Display field array
	 *
	 * @since 1.0
	 */
	public function display_field( $element = array(), $args = array() ) {

		$name = $args['name'];
		$id   = $args['id'];

		$tm_epo_global_datepicker_theme    = ! empty( THEMECOMPLETE_EPO()->tm_epo_global_datepicker_theme ) ? THEMECOMPLETE_EPO()->tm_epo_global_datepicker_theme : ( isset( $element['theme'] ) ? $element['theme'] : "epo" );
		$tm_epo_global_datepicker_size     = ! empty( THEMECOMPLETE_EPO()->tm_epo_global_datepicker_size ) ? THEMECOMPLETE_EPO()->tm_epo_global_datepicker_size : ( isset( $element['theme_size'] ) ? $element['theme_size'] : "medium" );
		$tm_epo_global_datepicker_position = ! empty( THEMECOMPLETE_EPO()->tm_epo_global_datepicker_position ) ? THEMECOMPLETE_EPO()->tm_epo_global_datepicker_position : ( isset( $element['theme_position'] ) ? $element['theme_position'] : "normal" );

		$tranlation_day   = ! empty( $element['tranlation_day'] ) ? $element['tranlation_day'] : "";
		$tranlation_month = ! empty( $element['tranlation_month'] ) ? $element['tranlation_month'] : "";
		$tranlation_year  = ! empty( $element['tranlation_year'] ) ? $element['tranlation_year'] : "";


		$style       = isset( $element['button_type'] ) ? $element['button_type'] : "";
		$defaultdate = isset( $element['default_value'] ) ? $element['default_value'] : "";
		$format      = ! empty( $element['format'] ) ? $element['format'] : 0;

		$date_format      = 'dd/mm/yy';
		$date_placeholder = 'dd/mm/yyyy';
		$date_mask        = '00/00/0000';

		$end_year   = ! empty( $element['end_year'] ) ? $element['end_year'] : ( date( "Y" ) + 10 );
		$start_year = ! empty( $element['start_year'] ) ? $element['start_year'] : "1900";

		switch ( $format ) {
			case "0":
				$date_format      = 'dd/mm/yy';
				$date_placeholder = 'dd/mm/yyyy';
				$date_mask        = '00/00/0000';
				break;
			case "1":
				$date_format      = 'mm/dd/yy';
				$date_placeholder = 'mm/dd/yyyy';
				$date_mask        = '00/00/0000';
				break;
			case "2":
				$date_format      = 'dd.mm.yy';
				$date_placeholder = 'dd.mm.yyyy';
				$date_mask        = '00.00.0000';
				break;
			case "3":
				$date_format      = 'mm.dd.yy';
				$date_placeholder = 'mm.dd.yyyy';
				$date_mask        = '00.00.0000';
				break;
			case "4":
				$date_format      = 'dd-mm-yy';
				$date_placeholder = 'dd-mm-yyyy';
				$date_mask        = '00-00-0000';
				break;
			case "5":
				$date_format      = 'mm-dd-yy';
				$date_placeholder = 'mm-dd-yyyy';
				$date_mask        = '00-00-0000';
				break;

			case "6":
				$date_format      = 'yy/mm/dd';
				$date_placeholder = 'yyyy/mm/dd';
				$date_mask        = '0000/00/00';
				break;
			case "7":
				$date_format      = 'yy/dd/mm';
				$date_placeholder = 'yyyy/dd/mm';
				$date_mask        = '0000/00/00';
				break;
			case "8":
				$date_format      = 'yy.mm.dd';
				$date_placeholder = 'yyyy.mm.dd';
				$date_mask        = '0000.00.00';
				break;
			case "9":
				$date_format      = 'yy.dd.mm';
				$date_placeholder = 'yyyy.dd.mm';
				$date_mask        = '0000.00.00';
				break;
			case "10":
				$date_format      = 'yy-mm-dd';
				$date_placeholder = 'yyyyy-mm-dd';
				$date_mask        = '0000-00-00';
				break;
			case "11":
				$date_format      = 'yy-dd-mm';
				$date_placeholder = 'yyyy-dd-mm';
				$date_mask        = '0000-00-00';
				break;
		}

		if ( apply_filters( 'wc_epo_display_rtl', is_rtl() ) ) {
			$date_format      = strrev( $date_format );
			$date_placeholder = strrev( $date_placeholder );
			$date_mask        = strrev( $date_mask );
		}

		$input_type = "text";
		$showon     = "both";
		if ( $style == "" ) {
			$input_type       = "hidden";
			$showon           = "focus";
			$date_mask        = '';
			$date_placeholder = '';
		}

		$picker_html = "";
		if ( $style != "picker" ) {
			if ( isset( $_GET[ $name ] ) && empty( $_POST ) ) {
				$value = str_replace( ".", "-", $_GET[ $name ] );
				$value = str_replace( "/", "-", $value );
				$value = explode( "-", $value );
				switch ( $format ) {
					case "0":
					case "2":
					case "4":
						$_POST[ $name . "_day" ]   = $value[0];
						$_POST[ $name . "_month" ] = $value[1];
						$_POST[ $name . "_year" ]  = $value[2];
						break;
					case "1":
					case "3":
					case "5":
						$_POST[ $name . "_day" ]   = $value[1];
						$_POST[ $name . "_month" ] = $value[0];
						$_POST[ $name . "_year" ]  = $value[2];
						break;
				}
			}

			$selectArray      = array( "class" => "tmcp-date-select tmcp-date-day", "id" => $id . "_day", "name" => $name . "_day", "atts" => array( "data-tm-date" => $id ) );
			$select_options   = array();
			$tranlation_day   = ( ! empty( $tranlation_day ) ) ? $tranlation_day : esc_html__( 'Day', 'woocommerce-tm-extra-product-options' );
			$select_options[] = array( "text" => $tranlation_day, "value" => "" );
			for ( $i = 1; $i != 31 + 1; $i += 1 ) {
				$select_options[] = array( "text" => $i, "value" => $i );
			}
			$day_html = THEMECOMPLETE_EPO_HTML()->tm_make_select( $selectArray, $select_options, $selectedvalue = isset( $_POST[ $name . "_day" ] ) ? $_POST[ $name . "_day" ] : "", 1, 0 );

			$selectArray      = array( "class" => "tmcp-date-select tmcp-date-month", "id" => $id . "_month", "name" => $name . "_month", "atts" => array( "data-tm-date" => $id ) );
			$select_options   = array();
			$tranlation_month = ( ! empty( $tranlation_month ) ) ? $tranlation_month : esc_html__( 'Month', 'woocommerce-tm-extra-product-options' );
			$select_options[] = array( "text" => $tranlation_month, "value" => "" );

			global $wp_locale;
			for ( $i = 1; $i != 12 + 1; $i += 1 ) {
				$select_options[] = array( "text" => $wp_locale->get_month( $i ), "value" => $i );
			}
			$month_html = THEMECOMPLETE_EPO_HTML()->tm_make_select( $selectArray, $select_options, $selectedvalue = isset( $_POST[ $name . "_month" ] ) ? $_POST[ $name . "_month" ] : "", 1, 0 );

			$selectArray      = array( "class" => "tmcp-date-select tmcp-date-year", "id" => $id . "_year", "name" => $name . "_year", "atts" => array( "data-tm-date" => $id ) );
			$select_options   = array();
			$tranlation_year  = ( ! empty( $tranlation_year ) ) ? $tranlation_year : esc_html__( 'Year', 'woocommerce-tm-extra-product-options' );
			$select_options[] = array( "text" => $tranlation_year, "value" => "" );
			for ( $i = intval( $end_year ); $i != intval( $start_year ) - 1; $i -= 1 ) {
				$select_options[] = array( "text" => $i, "value" => $i );
			}
			$year_html = THEMECOMPLETE_EPO_HTML()->tm_make_select( $selectArray, $select_options, $selectedvalue = isset( $_POST[ $name . "_year" ] ) ? $_POST[ $name . "_year" ] : "", 1, 0 );

			switch ( $format ) {
				case "0":
				case "2":
				case "4":
					if ( apply_filters( 'wc_epo_display_rtl', is_rtl() ) ) {
						$picker_html = $year_html . $month_html . $day_html;
					} else {
						$picker_html = $day_html . $month_html . $year_html;
					}

					break;
				case "1":
				case "3":
				case "5":
					if ( apply_filters( 'wc_epo_display_rtl', is_rtl() ) ) {
						$picker_html = $year_html . $day_html . $month_html;
					} else {
						$picker_html = $month_html . $day_html . $year_html;
					}

					break;
			}
		}

		$get_default_value = "";
		if ( ! isset( $defaultdate ) ) {
			$defaultdate       = NULL;
			$get_default_value = $defaultdate;
		} else {
			if ( $defaultdate !== '' ) {
				$get_default_value = $defaultdate;
			}
		}

		if ( THEMECOMPLETE_EPO()->tm_epo_global_reset_options_after_add == "no" && isset( $_POST[ $name ] ) ) {
			$get_default_value = esc_attr( stripslashes( $_POST[ $name ] ) );
		} elseif ( isset( $_GET[ $name ] ) ) {
			$get_default_value = esc_attr( stripslashes( $_GET[ $name ] ) );
		}
		$get_default_value = apply_filters( 'wc_epo_default_value', $get_default_value, $element );

		return array(
			'date_format'         => $date_format,
			'input_type'          => $input_type,
			'showon'              => $showon,
			'date_mask'           => $date_mask,
			'date_placeholder'    => $date_placeholder,
			'picker_html'         => $picker_html,
			'textbeforeprice'     => isset( $element['text_before_price'] ) ? $element['text_before_price'] : "",
			'textafterprice'      => isset( $element['text_after_price'] ) ? $element['text_after_price'] : "",
			'hide_amount'         => isset( $element['hide_amount'] ) ? " " . $element['hide_amount'] : "",
			'style'               => $style,
			'format'              => $format,
			'start_year'          => $start_year,
			'end_year'            => $end_year,
			'min_date'            => isset( $element['min_date'] ) ? $element['min_date'] : "",
			'max_date'            => isset( $element['max_date'] ) ? $element['max_date'] : "",
			'disabled_dates'      => ! empty( $element['disabled_dates'] ) ? $element['disabled_dates'] : "",
			'enabled_only_dates'  => ! empty( $element['enabled_only_dates'] ) ? $element['enabled_only_dates'] : "",
			'exlude_disabled'     => isset( $element['exlude_disabled'] ) ? $element['exlude_disabled'] : "",
			'disabled_weekdays'   => isset( $element['disabled_weekdays'] ) ? $element['disabled_weekdays'] : "",
			'disabled_months'     => isset( $element['disabled_months'] ) ? $element['disabled_months'] : "",
			'tranlation_day'      => $tranlation_day,
			'tranlation_month'    => $tranlation_month,
			'tranlation_year'     => $tranlation_year,
			'quantity'            => isset( $element['quantity'] ) ? $element['quantity'] : "",
			'defaultdate'         => $defaultdate,
			'get_default_value'   => $get_default_value,
			'date_theme'          => $tm_epo_global_datepicker_theme,
			'date_theme_size'     => $tm_epo_global_datepicker_size,
			'date_theme_position' => $tm_epo_global_datepicker_position,
		);
	}

	/**
	 * Field validation
	 *
	 * @since 1.0
	 */
	public function validate() {

		$format = $this->element['format'];
		switch ( $format ) {
			case "0":
				$date_format = 'd/m/Y H:i:s';
				$sep         = "/";
				break;
			case "1":
				$date_format = 'm/d/Y H:i:s';
				$sep         = "/";
				break;
			case "2":
				$date_format = 'd.m.Y H:i:s';
				$sep         = ".";
				break;
			case "3":
				$date_format = 'm.d.Y H:i:s';
				$sep         = ".";
				break;
			case "4":
				$date_format = 'd-m-Y H:i:s';
				$sep         = "-";
				break;
			case "5":
				$date_format = 'm-d-Y H:i:s';
				$sep         = "-";
				break;

			case "6":
				$date_format = 'Y/m/d H:i:s';
				$sep         = "/";
				break;
			case "7":
				$date_format = 'Y/d/m H:i:s';
				$sep         = "/";
				break;
			case "8":
				$date_format = 'Y.m.d H:i:s';
				$sep         = ".";
				break;
			case "9":
				$date_format = 'Y.d.m H:i:s';
				$sep         = ".";
				break;
			case "10":
				$date_format = 'Y-m-d H:i:s';
				$sep         = "-";
				break;
			case "11":
				$date_format = 'Y-d-m H:i:s';
				$sep         = "-";
				break;
		}

		$passed  = TRUE;
		$message = array();

		$quantity_once = FALSE;
		$min_quantity  = isset( $this->element['quantity_min'] ) ? intval( $this->element['quantity_min'] ) : 0;
		if ( $min_quantity < 0 ) {
			$min_quantity = 0;
		}
		foreach ( $this->field_names as $attribute ) {

			if ( ! $quantity_once && isset( $this->epo_post_fields[ $attribute ] ) && $this->epo_post_fields[ $attribute ] !== "" && isset( $this->epo_post_fields[ $attribute . '_quantity' ] ) && ! ( intval( $this->epo_post_fields[ $attribute . '_quantity' ] ) >= $min_quantity ) ) {
				$passed        = FALSE;
				$quantity_once = TRUE;
				$message[]     = sprintf( esc_html__( 'The quantity for "%s" must be greater than %s', 'woocommerce-tm-extra-product-options' ), $this->element['label'], $min_quantity );
			}

			if ( $this->element['required'] ) {
				if ( ! isset( $this->epo_post_fields[ $attribute ] ) || $this->epo_post_fields[ $attribute ] == "" ) {
					$passed    = FALSE;
					$message[] = 'required';
					break;
				}
			}

			if ( ! empty( $this->epo_post_fields[ $attribute ] ) && class_exists( 'DateTime' ) && ( version_compare( phpversion(), '5.3', '>=' ) ) ) {
				$posted_date = $this->epo_post_fields[ $attribute ];
				if ( apply_filters( 'wc_epo_display_rtl', is_rtl() ) ) {
					$posted_date_arr = explode( $sep, $posted_date );
					if ( count( $posted_date_arr ) == 3 ) {
						$posted_date = $posted_date_arr[2] . $sep . $posted_date_arr[1] . $sep . $posted_date_arr[0];
					}
				}
				$date = DateTime::createFromFormat( $date_format, $posted_date . ' 00:00:00' );
				if (!empty(THEMECOMPLETE_EPO()->tm_epo_global_date_timezone)){
					$date = DateTime::createFromFormat( $date_format, $posted_date . ' 00:00:00', new DateTimeZone(THEMECOMPLETE_EPO()->tm_epo_global_date_timezone) );
				}
				$date_errors = DateTime::getLastErrors();

				if ( ! empty( $date_errors['error_count'] ) ) {
					$passed    = FALSE;
					$message[] = esc_html__( 'Invalid date entered!', 'woocommerce-tm-extra-product-options' );
					break;
				}

				$year  = $_year = $date->format( "Y" );
				$month = $_month = $date->format( "m" );
				$day   = $_day = $date->format( "d" );

				$posted_date_arr = explode( $sep, $posted_date );

				if ( count( $posted_date_arr ) == 3 ) {
					switch ( $format ) {
						case "0":
						case "2":
						case "4":
							$_year  = $posted_date_arr[2];
							$_month = $posted_date_arr[1];
							$_day   = $posted_date_arr[0];
							break;
						case "1":
						case "3":
						case "5":
							$_year  = $posted_date_arr[2];
							$_month = $posted_date_arr[0];
							$_day   = $posted_date_arr[1];
							break;
					}

					if ( $year != $_year || $month != $_month || $day != $_day ) {
						$message[] = esc_html__( 'Invalid data submitted!', 'woocommerce-tm-extra-product-options' );
						$passed    = FALSE;
						break;
					}
				}

				if ( checkdate( $_month, $_day, $_year ) ) {
					// valid date ...
					$start_year         = intval( $this->element['start_year'] ) || 1900;
					$end_year           = intval( $this->element['end_year'] ) || ( date( "Y" ) + 10 );
					$min_date           = ( $this->element['min_date'] !== '' ) ? ( $this->element['min_date'] ) : FALSE;
					$max_date           = ( $this->element['max_date'] !== '' ) ? ( $this->element['max_date'] ) : FALSE;
					$disabled_dates     = $this->element['disabled_dates'];
					$enabled_only_dates = $this->element['enabled_only_dates'];
					$disabled_weekdays  = $this->element['disabled_weekdays'];
					$disabled_months    = $this->element['disabled_months'];

					$now = new DateTime( '00:00:00' );
					if (!empty(THEMECOMPLETE_EPO()->tm_epo_global_date_timezone)){
						$now = new DateTime( '00:00:00', new DateTimeZone(THEMECOMPLETE_EPO()->tm_epo_global_date_timezone) );
					}
					$now_day   = $now->format( "d" );
					$now_month = $now->format( "m" );
					$now_year  = $now->format( "Y" );

					if ( $enabled_only_dates ) {
						$enabled_only_dates = explode( ",", $enabled_only_dates );
						$_pass              = FALSE;
						foreach ( $enabled_only_dates as $key => $value ) {
							$value = str_replace( ".", "-", $value );
							$value = str_replace( "/", "-", $value );
							$value = explode( "-", $value );
							if ( count( $value ) !== 3 ) {
								continue;
							}
							switch ( $format ) {
								case "0":
								case "2":
								case "4":
									$value = $value[2] . "-" . $value[1] . "-" . $value[0];
									break;
								case "1":
								case "3":
								case "5":
									$value = $value[2] . "-" . $value[0] . "-" . $value[1];
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
								$_pass = TRUE;
								break;
							}
						}
						$passed = $_pass;
						if ( ! $_pass ) {
							$message[] = esc_html__( 'Invalid date entered!', 'woocommerce-tm-extra-product-options' );
							break;
						}
					} else {
						// validate start,end year
						if ( $_year < $start_year || $_year > $end_year ) {
							$passed    = FALSE;
							$message[] = esc_html__( 'Invalid year date entered!', 'woocommerce-tm-extra-product-options' );
							break;
						}

						// validate disabled dates
						if ( $disabled_dates ) {
							$disabled_dates = explode( ",", $disabled_dates );
							foreach ( $disabled_dates as $key => $value ) {
								$value = str_replace( ".", "-", $value );
								$value = str_replace( "/", "-", $value );
								$value = explode( "-", $value );
								if ( count( $value ) !== 3 ) {
									continue;
								}
								switch ( $format ) {
									case "0":
									case "2":
									case "4":
										$value = $value[2] . "-" . $value[1] . "-" . $value[0];
										break;
									case "1":
									case "3":
									case "5":
										$value = $value[2] . "-" . $value[0] . "-" . $value[1];
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
									$passed    = FALSE;
									$message[] = esc_html__( 'You cannot select that date!', 'woocommerce-tm-extra-product-options' );
									break;
								}
							}

						}

						// validate minimum date
						if ( $min_date !== FALSE ) {

							if ( is_numeric( $min_date ) ) {
								$temp = clone $now;
								if ( $min_date > 0 ) {
									$temp->add( new DateInterval( 'P' . abs( $min_date ) . 'D' ) );
								} elseif ( $min_date < 0 ) {
									$temp->sub( new DateInterval( 'P' . abs( $min_date ) . 'D' ) );
								}
							} else {
								$temp = str_replace( ".", "-", $min_date );
								$temp = str_replace( "/", "-", $temp );
								$temp = explode( "-", $temp );
								if ( is_array( $temp ) && isset( $temp[0] ) && isset( $temp[1] ) && isset( $temp[2] ) ) {
									switch ( $format ) {
										case "0":
										case "2":
										case "4":
											$temp = $temp[2] . "-" . $temp[1] . "-" . $temp[0];
											break;
										case "1":
										case "3":
										case "5":
											$temp = $temp[2] . "-" . $temp[0] . "-" . $temp[1];
											break;
									}
									$temp = date_create( $temp );
								} else {
									$temp = FALSE;
								}
								if ( ! $temp ) {
									//failsafe
									$temp = clone $now;
								} else {
									$temp = date_format( $temp, $date_format );
									$temp = DateTime::createFromFormat( $date_format, $temp );
								}
							}

							$interval = $temp->diff( $date );
							$sign     = $interval->format( '%r' );

							if ( ! empty( $sign ) ) {
								$passed    = FALSE;
								$message[] = esc_html__( 'You cannot select that date!', 'woocommerce-tm-extra-product-options' );
								break;
							}
						}

						// validate maximum date
						if ( $max_date !== FALSE ) {
							if ( is_numeric( $max_date ) ) {
								$temp = clone $now;
								if ( $max_date > 0 ) {
									$temp->add( new DateInterval( 'P' . abs( $max_date ) . 'D' ) );
								} elseif ( $max_date < 0 ) {
									$temp->sub( new DateInterval( 'P' . abs( $max_date ) . 'D' ) );
								}
							} else {
								$temp = str_replace( ".", "-", $max_date );
								$temp = str_replace( "/", "-", $temp );
								$temp = explode( "-", $temp );
								switch ( $format ) {
									case "0":
									case "2":
									case "4":
										$temp = $temp[2] . "-" . $temp[1] . "-" . $temp[0];
										break;
									case "1":
									case "3":
									case "5":
										$temp = $temp[2] . "-" . $temp[0] . "-" . $temp[1];
										break;
								}
								$temp = date_create( $temp );
								if ( ! $temp ) {
									// failsafe todo:proper handling
									$temp = clone $now;
								} else {
									$temp = date_format( $temp, $date_format );
									$temp = DateTime::createFromFormat( $date_format, $temp );
								}
							}

							$interval = $date->diff( $temp );
							$sign     = $interval->format( '%r' );
							if ( ! empty( $sign ) ) {
								$passed    = FALSE;
								$message[] = esc_html__( 'You cannot select that date!', 'woocommerce-tm-extra-product-options' );
								break;
							}
						}
					}

				} else {
					// problem with dates ...
					$passed    = FALSE;
					$message[] = esc_html__( 'Invalid date entered!', 'woocommerce-tm-extra-product-options' );
					break;
				}
			}
		}

		return array( 'passed' => $passed, 'message' => $message );
	}

}
