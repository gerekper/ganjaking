<?php

namespace WPMailSMTP\Pro\Emails\Logs\Export;

use Generator;
use WPMailSMTP\Pro\Emails\Logs\Email;
use WPMailSMTP\Pro\Emails\Logs\EmailsCollection;

/**
 * Email logs export data in table format.
 *
 * @since 2.8.0
 */
class TableData extends AbstractData {

	/**
	 * Get columns.
	 *
	 * @since 2.8.0
	 *
	 * @return array columns (first row).
	 */
	public function get_columns() {

		$request_data = $this->request->get_data();

		$columns = [];

		if ( ! empty( $request_data['common_fields'] ) ) {
			foreach ( $request_data['common_fields'] as $key ) {
				$columns[ $key ] = Export::get_common_fields( $key );
			}
		}

		if ( ! empty( $request_data['additional_fields'] ) ) {
			foreach ( $request_data['additional_fields'] as $key ) {
				$columns[ $key ] = Export::get_additional_fields( $key );
			}
		}

		/**
		 * Filters export table data columns.
		 *
		 * @since 2.8.0
		 *
		 * @param array     $columns Columns.
		 * @param TableData $data    Data.
		 */
		return apply_filters( 'wp_mail_smtp_pro_emails_logs_export_table_data_get_columns', $columns, $this );
	}

	/**
	 * Get single email data row.
	 *
	 * @since 2.8.0
	 *
	 * @return Generator
	 */
	public function get_row() {

		$emails = new EmailsCollection( $this->request->get_data( 'db_args' ) );

		foreach ( $emails->get() as $email ) {

			$row = [];

			foreach ( $this->get_columns() as $col_id => $col_label ) {
				$value          = $this->get_field_value( $col_id, $email );
				$row[ $col_id ] = $this->escape_value( $value );
			}

			/**
			 * Filters export table data row.
			 *
			 * @since 2.8.0
			 *
			 * @param array     $row   Row.
			 * @param Email     $email Current email.
			 * @param TableData $data  Data.
			 */
			yield apply_filters( 'wp_mail_smtp_pro_emails_logs_export_table_data_get_row', $row, $email, $this );
		}
	}

	/**
	 * Escape string for table data.
	 *
	 * @since 3.6.0
	 *
	 * @param mixed $value Value to escape.
	 *
	 * @return string
	 */
	private function escape_value( $value ) {

		// Prevent formulas in spreadsheet applications.
		if ( in_array( substr( (string) $value, 0, 1 ), [ '=', '-', '+', '@', "\t", "\r" ], true ) ) {
			$value = "'" . $value;
		}

		return html_entity_decode( $value, ENT_QUOTES );
	}
}
