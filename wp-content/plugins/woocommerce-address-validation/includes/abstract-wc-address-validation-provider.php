<?php
/**
 * WooCommerce Address Validation
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Address Validation to newer
 * versions in the future. If you wish to customize WooCommerce Address Validation for your
 * needs please refer to http://docs.woocommerce.com/document/address-validation/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * Address Validation Validator class
 *
 * Extended by address providers to handle address/postcode validation and lookup
 *
 * @since 1.0
 */
abstract class WC_Address_Validation_Provider extends \WC_Settings_API {


	/** @var string unique prefix for saving settings */
	public $plugin_id = 'wc_address_validation_';

	/** @var string unique ID for the validator, required */
	public $id;

	/** @var null|bool whether the provider is configured */
	protected $is_configured;

	/** @var string title used on settings page */
	protected $title;

	/** @var string description used on settings page */
	protected $description;

	/** @var array array of countries this validator is valid for */
	protected $countries = array();

	/** @var array features this validator supports (e.g. post code lookup) */
	protected $supports = array();


	/**
	 * Return the provider's title
	 *
	 * @since 1.0
	 * @return string the provider title
	 */
	public function get_title() {

		$title = empty( $this->title ) ? ucwords( str_replace( array( '_', '-' ), '', $this->id ) ) : $this->title;

		/**
		 * Filter the address provider title
		 *
		 * @since 2.0.0
		 * @param string $title
		 * @param \WC_Address_Validation_Provider $this
		 */
		return apply_filters( 'wc_address_validation_provider_title', $title, $this );
	}


	/**
	 * Return the description for admin screens
	 *
	 * @since 2.0.0
	 * @return string
	 */
	public function get_description() {

		/**
		 * Filter the address provider description
		 *
		 * @since 2.0.0
		 * @param string $description
		 * @param \WC_Address_Validation_Provider $this
		 */
		return apply_filters( 'wc_address_validation_provider_description', $this->description, $this );
	}


	/**
	 * Return an array of supported features
	 *
	 * Since 2.0.0 returns an array of non-formatted
	 * features. To get formatted feature labels, use
	 * get_supported_features_formatted() instead.
	 *
	 * @since 1.0
	 * @return array supported features
	 */
	public function get_supported_features() {
		return $this->supports;
	}


	/**
	 * Return an array of formatted supported features
	 *
	 * @since 2.0.0
	 * @return array supported features, formatted
	 */
	public function get_supported_features_formatted() {
		return array_map( array( $this, 'get_feature_label' ), $this->supports );
	}


	/**
	 * Get feature label
	 *
	 * @since 2.0.0
	 * @param string $feature
	 * @return string localized feature label
	 */
	public function get_feature_label( $feature ) {

		$labels = array(
			'address_validation'     => __( 'Address Validation', 'woocommerce-address-validation' ),
			'address_classification' => __( 'Address Classification', 'woocommerce-address-validation' ),
			'geocoding'              => __( 'Geocoding', 'woocommerce-address-validation' ),
			'postcode_lookup'        => __( 'Postcode Lookup', 'woocommerce-address-validation' ),
		);

		$label = ! empty( $labels[ $feature ] )	? $labels[ $feature ] : ucwords( str_replace( '_', ' ', $feature ) );

		/**
		 * Filter the feature label
		 *
		 * Allows third parties to supply their own labels to any custom features
		 *
		 * @since 2.0.0
		 * @param string $label
		 * @param string $feature
		 */
		return apply_filters( 'wc_address_validation_feature_label', $label, $feature );
	}


	/**
	 * Return an array of supported countries
	 *
	 * @since 1.2.0
	 * @return array supported countries
	 */
	public function get_supported_countries() {

		/**
		 * Filters the provider's supported countries.
		 *
		 * Note: you normally can't add countries that aren't supported by the provider by default.
		 *
		 * @since 2.3.3
		 *
		 * @param string[] $supported_countries list of supported countries (as country codes)
		 * @param \WC_Address_Validation_Provider $provider provider object
		 */
		return (array) apply_filters( "{$this->plugin_id}{$this->id}_supported_countries", $this->countries, $this );
	}


	/**
	 * Checks if provider is configured correctly, overridden by child providers.
	 *
	 * @since 1.0
	 *
	 * @return bool true if configured, false otherwise
	 */
	public function is_configured() {

		/**
		 * Filters whether a provider is configured and available.
		 *
		 * @since 2.3.3
		 *
		 * @param bool $is_configured whether the provider is configured
		 * @param \WC_Address_Validation_Provider $provider provider object
		 */
		return (bool) apply_filters( "{$this->plugin_id}{$this->id}_is_configured", $this->is_configured, $this );
	}


	/**
	 * Postcode lookup function stub to be overridden by child providers
	 *
	 * This method only needs to be overridden for providers that support postcode lookup
	 *
	 * @since 1.0
	 * @param string $postcode
	 * @param string $house_number Optional. Used by Postcode.nl.
	 */
	public function lookup_postcode( $postcode, $house_number = '' ) { }


	/**
	 * Checks if a provider supports a given feature.
	 *
	 * Options = 'postcode_lookup', 'address_validation', 'geocoding', 'address_classification'
	 *
	 * @since 1.0
	 *
	 * @param string $feature the name of a feature to test support for
	 * @return bool
	 */
	public function supports( $feature ) {

		/**
		 * Filters whether the provider has support for a feature.
		 *
		 * @since 1.0
		 *
		 * @param bool $supported whether the feature is supported
		 * @param string $feature the feature to check
		 * @param \WC_Address_Validation_Provider the provider object
		 */
		return (bool) apply_filters( 'wc_address_validation_provider_supports', in_array( $feature, $this->supports,false ), $feature, $this );
	}


	/**
	 * Show the title / description and settings for provider
	 *
	 * @since 1.0
	 */
	public function admin_options() {

		echo '<h2>' . esc_html( $this->get_title() ) . '</h2>';

		if ( $this->id !== wc_address_validation()->get_handler_instance()->get_active_provider()->id ) {

			/* translators: %1$s - provider name, %2$s - opening <a> tag, %3$s - closing </a> tag */
			$message = __( '%1$s is not selected as your active provider. Please change your active provider under the %2$sGeneral Options%3$s and save your settings to enable %1$s.', 'woocommerce-address-validation' );
			$message = sprintf( $message, $this->get_title(), '<a href="' . wc_address_validation()->get_settings_url() . '">', '</a>' );

			printf( '<div class="notice notice-warning below-h2"><p>%s</p></div>', $message );
		}

		echo wp_kses_post( wpautop( $this->get_description() ) );

		parent::admin_options();

		// Display supported features
		?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $this->id . '_supported_features' ); ?>">
						<?php _e( 'Supported Features', 'woocommerce-address-validation' ); ?>
					</label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text">
							<span><?php _e( 'Supported Features', 'woocommerce-address-validation' ); ?></span>
						</legend>
						<p><?php echo esc_html( implode( ', ', $this->get_supported_features_formatted() ) ); ?></p>
					</fieldset>
				</td>
			</tr>
		</table>
		<?php
	}


	/**
	 * Log API Request data if plugin debug mode is enabled
	 *
	 * @param string $url Request URL
	 * @param array  $args Request arguments
	 */
	public function maybe_log_request( $url, $args ) {

		if ( wc_address_validation()->is_debug_mode_enabled() ) {
			wc_address_validation()->log( print_r( compact( 'url', 'args' ), true ) );
		}
	}


	/**
	 * Prepares postcode lookup results.
	 *
	 * @since 2.7.2
	 *
	 * @param array $locations locations found
	 * @param string $postcode the postcode the user entered
	 * @param array $args the lookup API arguments
	 * @return array
	 */
	protected function prepare_lookup_data( $locations, $postcode, $args ) {

		/**
		 * Filters the postcode lookup results.
		 *
		 * @since 2.7.2
		 *
		 * @param array $locations locations found
		 * @param string $postcode the postcode the user entered
		 * @param string $args the lookup API arguments
		 * @param \WC_Address_Validation_Provider $provider the provider instance
		 */
		return (array) apply_filters_ref_array( 'wc_address_validation_postcode_lookup', [ $locations, $postcode, $args, $this ] );
	}


	/**
	 * Gets the lookup error message filtered.
	 *
	 * @since 2.7.2
	 *
	 * @param array|WP_Error $api_response
	 * @return string
	 */
	protected function get_lookup_provider_error_message( $api_response ) {

		/**
		 * Changes the message displayed when a postcode lookup API returns an error.
		 *
		 * @since 2.7.2
		 *
		 * @param string $message the message to display
		 * @param array|WP_Error $api_response the API response object or error object
		 * @param \WC_Address_Validation_Provider $provider the provider instance
		 */
		return (string) apply_filters_ref_array( 'wc_address_validation_postcode_lookup_provider_error_message', [
			__( 'No addresses found, please check your postcode and try again.', 'woocommerce-address-validation' ),
			$api_response,
			$this,
		] );
	}


}
