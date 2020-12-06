<?php namespace Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections;

use DateTime;
use Exception;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\PinterestIntegration;
use Premmerce\WooCommercePinterest\PinterestException;


/**
 * Class PinTimeSection
 *
 * @package Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections
 *
 * This class is a storage for Pin Time section fields on settings page
 */
class PinTimeSection implements IntegrationSectionInterface {

	const DEFAULT_DAY = - 1;

	const DEFAULT_TIME = '18:00';

	const DEFAULT_INTERVAL = 5;

	const DEFAULT_PINS_PER_INTERVAL = 10;

	const DEFAULT_PINS_TIME = 'right_away';

	/**
	 * PinterestIntegration instance
	 *
	 * @var PinterestIntegration
	 */
	private $integration;

	/**
	 * PinTimeSection constructor.
	 *
	 * @param PinterestIntegration $integration
	 *
	 * @throws PinterestException
	 */
	public function __construct( PinterestIntegration $integration ) {
		$this->integration = $integration;
		$this->getExecuteDate();
	}

	public function getTitle() {
		return __( 'Pinning time', 'woocommerce-pinterest' );
	}

	public function getSlug() {
		return 'pinning_time_section';
	}

	public function getFields() {
		return array(
			'pin_time' => array(
				'title'       => __( 'Create pins', 'woocommerce-pinterest' ),
				'type'        => 'select',
				'options'     => array(
					'right_away' => __( 'Right away', 'woocoommerce-pinterest' ),
					'defer'      => __( 'Schedule', 'woocommerce-pinterest' ),
				),
				'default'     => self::DEFAULT_PINS_TIME,
				'description' => __( 'Pinterest API has the limit - 100 requests per day (product pinning, pins update, pin removal, boards updates). Take this into account while planning the pinning of products.', 'woocommerce-pinterest' ),
				'desc_tip'    => __( 'When pin will be created. For defer option you able to choose day and time for pinning.',
					'woocommerce-pinterest' ),
			),

			'pin_defer_day' => array(
				'title'   => __( 'Scheduled posting: day', 'woocommerce-pinterest' ),
				'type'    => 'select',
				'options' => $this->getAvailableDays(),
				'default' => self::DEFAULT_DAY
			),

			'pin_defer_time' => array(
				'title'   => __( 'Scheduled posting: time', 'woocommerce-pinterest' ),
				'type'    => 'select',
				'options' => $this->getAvailableTimes(),
				'default' => self::DEFAULT_TIME
			),

			'pin_defer_interval' => array(
				'title'   => __( 'Interval', 'woocommerce-pinterest' ),
				'type'    => 'select',
				'options' => $this->getIntervals(),
				'default' => self::DEFAULT_INTERVAL
			),

			'pin_defer_pins_per_interval' => array(
				'title'   => __( 'Pins per interval', 'woocommerce-pinterest' ),
				'type'    => 'select',
				'options' => $this->getPinsPerInterval(),
				'default' => self::DEFAULT_PINS_PER_INTERVAL
			),
		);
	}

	/**
	 * Return list of available days
	 *
	 * @return array
	 */
	protected function getAvailableDays() {
		return apply_filters( 'woocommerce_pinterest_defer_pins_days', array(
			- 1 => __( 'Everyday', 'woocommerce-pinterest' ),
			7   => __( 'Sunday', 'woocommerce-pinterest' ),
			1   => __( 'Monday', 'woocommerce-pinterest' ),
			2   => __( 'Tuesday', 'woocommerce-pinterest' ),
			3   => __( 'Wednesday', 'woocommerce-pinterest' ),
			4   => __( 'Thursday', 'woocommerce-pinterest' ),
			5   => __( 'Friday', 'woocommerce-pinterest' ),
			6   => __( 'Saturday', 'woocommerce-pinterest' )
		) );
	}

	/**
	 * Return list of available intervals
	 *
	 * @return array
	 */
	protected function getIntervals() {
		return apply_filters( 'woocommerce_pinterest_defer_pins_intervals', array(
			- 1 => __( 'None', 'woocommerce-pinterest' ),
			1   => __( '1 minute', 'woocommerce-pinterest' ),
			2   => __( '2 minutes', 'woocommerce-pinterest' ),
			3   => __( '3 minutes', 'woocommerce-pinterest' ),
			5   => __( '5 minutes', 'woocommerce-pinterest' ),
			10  => __( '10 minutes', 'woocommerce-pinterest' ),
			20  => __( '20 minutes', 'woocommerce-pinterest' ),
			60  => __( 'One hour', 'woocommerce-pinterest' )
		) );
	}

	/**
	 * Return list of available times
	 *
	 * @return array
	 */
	protected function getAvailableTimes() {
		$times = array();
		$date  = DateTime::createFromFormat( 'H:i', '00:00' );

		for ( $i = 1; $i < 48; $i ++ ) {
			$times[ $date->format( 'H:i' ) ] = $date->format( 'H:i' );

			$date->modify( '+30 minutes' );
		}

		return apply_filters( 'woocommerce_pinterest_defer_pins_times', $times );
	}

	/**
	 * Return list of pins per interval
	 *
	 * @return array
	 */
	protected function getPinsPerInterval() {
		return apply_filters( 'woocommerce_pinterest_defer_pins_per_interval', array(
			1   => '1',
			5   => '5',
			10  => '10',
			20  => '20',
			50  => '50',
			100 => '100',
			300 => '300'
		) );
	}

	/**
	 * Return list of defer params
	 *
	 * @return array
	 */
	public function getDeferParams() {
		return array(
			'day'               => $this->integration->get_option( 'pin_defer_day' ),
			'time'              => $this->integration->get_option( 'pin_defer_time' ),
			'interval'          => $this->integration->get_option( 'pin_defer_interval' ),
			'pins_per_interval' => $this->integration->get_option( 'pin_defer_pins_per_interval' ),
		);
	}

	/**
	 * Return where to run task
	 *
	 * @return DateTime|null
	 *
	 * @throws PinterestException
	 * @todo: This needs refactoring
	 *
	 */
	public function getExecuteDate() {
		if ( $this->integration->get_option( 'pin_time' ) === 'right_away' ) {
			return null;
		}

		$dayNow = (int) ( new DateTime() )->format( 'N' );

		$defDay = (int) $this->integration->get_option( 'pin_defer_day' );
		$defDay = - 1 === $defDay ? $dayNow : $defDay;

		$defTime = $this->integration->get_option( 'pin_defer_time' );
		$defTime = $defTime ? $defTime : self::DEFAULT_TIME;
		$time    = explode( ':', $defTime );

		$day = $defDay % 8;

		$dayDifference = $dayNow > $defDay ? ( 7 - $dayNow ) + $defDay : $defDay - $dayNow;

		try {
			$hours   = (int) ( new DateTime() )->format( 'G' );
			$minutes = (int) ( new DateTime() )->format( 'i' );
		} catch ( Exception $e ) {
			throw new PinterestException( "Can't get pin date and time", 0, $e );
		}


		if ( $day === $dayNow && ( $hours + ( $minutes / 10 ) ) > ( $time[0] + ( $time[1] / 10 ) ) ) {
			$dayDifference ++;
		}

		$date = DateTime::createFromFormat( 'G:i', $time[0] . ':' . $time[1] );

		$date->modify( '+' . $dayDifference . ' days' );

		return $date;
	}
}
