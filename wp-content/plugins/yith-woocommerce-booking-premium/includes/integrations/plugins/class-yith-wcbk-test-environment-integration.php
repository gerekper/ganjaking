<?php
/**
 * Class YITH_WCBK_Test_Environment_Integration
 * Test Environment integration
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking
 */

defined( 'YITH_WCBK' ) || exit;

/**
 * Class YITH_WCBK_Test_Environment_Integration
 *
 * @since   2.0.7
 */
class YITH_WCBK_Test_Environment_Integration extends YITH_WCBK_Integration {
	use YITH_WCBK_Singleton_Trait;

	/**
	 * Init
	 */
	protected function init() {
		if ( $this->is_enabled() ) {
			add_filter( 'ywtenv_run_replace_tables_list', array( $this, 'exclude_booking_tables' ), 10, 2 );
		}
	}

	/**
	 * Exclude booking tables for searching and replacing site URL.
	 *
	 * @param array  $tables_list   Table list.
	 * @param string $target_prefix Target DB prefix.
	 *
	 * @return array
	 */
	public function exclude_booking_tables( $tables_list, $target_prefix ) {
		$table_to_exclude = array(
			$target_prefix . YITH_WCBK_DB::BOOKING_NOTES_TABLE,
			$target_prefix . YITH_WCBK_DB::EXTERNAL_BOOKINGS_TABLE,
			$target_prefix . YITH_WCBK_DB::LOGS_TABLE,
		);

		foreach ( $tables_list as $key => $value ) {
			if ( is_array( $value ) ) {
				$table_name = current( $value );
				if ( is_string( $table_name ) && in_array( $table_name, $table_to_exclude, true ) ) {
					unset( $tables_list[ $key ] );
				}
			}
		}

		return $tables_list;
	}
}
