<?php
/**
 * Implements the provider stats feature.
 *
 * @package WC_Newsletter_Subscription/Traits
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Trait WC_Newsletter_Subscription_Provider_Stats.
 */
trait WC_Newsletter_Subscription_Provider_Stats {

	/**
	 * Gets the stats for the specified list.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $list The provider list.
	 * @return array
	 */
	public function get_stats( $list ) {
		$transient = $this->generate_transient_name( 'list_stats_' . $list );
		$stats     = get_transient( $transient );

		if ( ! $stats ) {
			$stats = $this->fetch_stats( $list );

			if ( ! empty( $stats ) ) {
				set_transient( $transient, $stats, HOUR_IN_SECONDS );
			}
		}

		return $stats;
	}

	/**
	 * Clear cached stats for the specified lists.
	 *
	 * @since 3.1.0
	 *
	 * @param mixed $list The provider list.
	 */
	public function clear_stats( $list ) {
		$transient = $this->generate_transient_name( 'list_stats_' . $list );
		delete_transient( $transient );
	}

	/**
	 * Gets the last sync datetime.
	 *
	 * @since 3.1.0
	 *
	 * @param mixed $list The provider list.
	 * @return WC_DateTime|false
	 */
	public function get_last_sync( $list ) {
		$transient = $this->generate_transient_name( 'list_stats_' . $list );
		$timeout   = get_option( '_transient_timeout_' . $transient );

		if ( ! $timeout ) {
			return false;
		}

		try {
			$last_sync = new WC_DateTime( '@' . $timeout );
		} catch ( Exception $e ) {
			return false;
		}

		$timezone = wc_timezone_string();
		$last_sync->setTimezone( new DateTimeZone( $timezone ) );

		// Subtract the period assigned to the transient.
		$last_sync->sub( new DateInterval( 'PT1H' ) );

		return $last_sync;
	}

	/**
	 * Fetches the stats for the specified list.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $list The list to fetch the stats.
	 * @return array
	 */
	abstract protected function fetch_stats( $list );

	/**
	 * Gets the formatted stats for the specified list.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $list The list to fetch the stats.
	 * @return array
	 */
	public function get_formatted_stats( $list ) {
		$stats     = $this->get_stats( $list );
		$formatted = $this->format_stats( $stats );

		/**
		 * Filters the formatted stats.
		 *
		 * The dynamic portion of the hook name, $this->id, refers to the provider ID.
		 *
		 * @since 3.0.0
		 *
		 * @param array $formatted Formatted stats.
		 * @param array $stats     Raw stats.
		 * @param mixed $list      The provider list.
		 */
		return apply_filters( "wc_newsletter_subscription_{$this->id}_formatted_stats", $formatted, $stats, $list );
	}

	/**
	 * Formats the stats.
	 *
	 * @since 3.0.0
	 *
	 * @param mixed $stats The stats to format.
	 * @return array
	 */
	abstract protected function format_stats( $stats );
}
