<?php


namespace Premmerce\WooCommercePinterest\Frontend\Analytics\Event;

use Premmerce\WooCommercePinterest\Admin\WooCommerce\PinterestIntegration;
use Premmerce\WooCommercePinterest\Frontend\Analytics\Analytics;

abstract class AbstractEvent implements EventInterface {

	/**
	 * PinterestIntegration instance
	 *
	 * @var PinterestIntegration
	 *
	 */
	protected $integration;

	/**
	 * Event data set manually
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * User email for enhanced match
	 *
	 * @var static|null
	 */
	protected $userEmail;

	/**
	 * Was Event fired
	 *
	 * @var bool
	 */
	protected $fired = false;

	/**
	 * AbstractEvent constructor.
	 *
	 * @param PinterestIntegration $integration
	 */
	public function __construct( PinterestIntegration $integration ) {
		$this->integration = $integration;
	}

	/**
	 * Set event data manually
	 *
	 * @param array $data
	 */
	public function setData( array $data ) {
		$this->data = $data;
	}

	/**
	 * Set if event is fired
	 *
	 * @param $fired
	 */
	public function setFired( $fired ) {
		$this->fired = $fired;
	}

	/**
	 * Return is event enabled in options
	 *
	 * @return bool
	 */
	public function isEnabledInOptions() {
		$eventName = $this->getName();

		return $this->integration->get_option( 'event_' . $eventName ) === 'yes';
	}

	public function trigger() {

		$data   = wp_json_encode( $this->getData() );
		$script = "pintrk('track', '{$this->getName()}', {$data});";

		// Enhanced match
		if ( $this->integration->get_option( 'enable_enhanced_match' ) === 'yes' ) {
			$userEmail = false;

			if ( isset( $this->userEmail ) ) {
				$userEmail = $this->userEmail;
			} else {
				$user = wp_get_current_user();

				if ( $user ) {
					$userEmail = $user->user_email;
				}
			}

			if ( $userEmail ) {
				$script = sprintf( "pintrk('track', '%s', %s);", $this->getName(), $data );
        wp_localize_script(
          Analytics::PINTEREST_ANALYTICS_INIT_SCRIPT_HANDLE,
          'enhancedSettings',
          array(
            'email'  => $userEmail
          )
        );
			}
		}

		wp_add_inline_script( Analytics::PINTEREST_ANALYTICS_INIT_SCRIPT_HANDLE, $script );
	}
}
