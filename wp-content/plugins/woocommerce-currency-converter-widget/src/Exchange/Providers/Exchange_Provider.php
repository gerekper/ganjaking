<?php
/**
 * Exchange provider interface.
 *
 * @since 2.1.0
 */

namespace KoiLab\WC_Currency_Converter\Exchange\Providers;

defined( 'ABSPATH' ) || exit;

/**
 * Exchange Provider Interface.
 */
interface Exchange_Provider {

	/**
	 * Gets the provider ID.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	public function get_id(): string;

	/**
	 * Gets the provider name.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	public function get_name(): string;

	/**
	 * Gets the provider privacy URL.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	public function get_privacy_url(): string;

	/**
	 * Validates the given API credentials.
	 *
	 * @since 2.1.0
	 *
	 * @return bool
	 */
	public function validate_credentials(): bool;

	/**
	 * Gets the exchange rates.
	 *
	 * @since 2.1.0
	 *
	 * @param array $args Optional. Additional arguments. Default empty.
	 * @return array
	 */
	public function get_rates( array $args = array() ): array;

	/**
	 * Gets the rates refresh period in hours.
	 *
	 * @since 2.1.0
	 *
	 * @return int
	 */
	public function get_refresh_period(): int;
}
