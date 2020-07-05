<?php namespace Premmerce\WooCommercePinterest\Frontend\Analytics;

use Premmerce\PrimaryCategory\Model\Model;
use Premmerce\SDK\V2\FileManager\FileManager;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\PinterestIntegration;
use Premmerce\WooCommercePinterest\Frontend\Analytics\Event\AddToCartEvent;
use Premmerce\WooCommercePinterest\Frontend\Analytics\Event\EventCheckout;
use Premmerce\WooCommercePinterest\Frontend\Analytics\Event\EventInterface;
use Premmerce\WooCommercePinterest\Frontend\Analytics\Event\LeadEvent;
use Premmerce\WooCommercePinterest\Frontend\Analytics\Event\PageVisitEvent;
use Premmerce\WooCommercePinterest\Frontend\Analytics\Event\ProductCategoryEvent;
use Premmerce\WooCommercePinterest\Frontend\Analytics\Event\SearchEvent;
use Premmerce\WooCommercePinterest\PinterestPlugin;

/**
 * Class Analytics
 * Responsible for triggering Pinterest events via js api
 *
 * @package Premmerce\WooCommercePinterest\Frontend
 */
class Analytics {

	const DEFERRED_EVENTS_TRANSIENT_NAME = 'woocommerce_pinterest_deferred_events';

	const PINTEREST_ANALYTICS_INIT_SCRIPT_HANDLE = 'pinterest-analytics-init';

	/**
	 * FileManager instance
	 *
	 * @var FileManager
	 */
	private $fileManager;

	/**
	 * PinterestIntegration instance
	 *
	 * @var PinterestIntegration
	 */
	private $integration;

	/**
	 * Primary Category Model instance
	 *
	 * @var Model
	 */
	private $primaryCategoryModel;

	/**
	 * Array of registered Events
	 *
	 * @var EventInterface[]
	 */
	private $events = array();


	/**
	 * Analytics constructor.
	 *
	 * @param FileManager $fileManager
	 * @param PinterestIntegration $integration
	 * @param Model $primaryCategoryModel
	 */
	public function __construct( FileManager $fileManager, PinterestIntegration $integration, Model $primaryCategoryModel ) {
		$this->fileManager = $fileManager;
		$this->integration = $integration;

		$this->primaryCategoryModel = $primaryCategoryModel;
	}

	/**
	 * Init hooks
	 */
	public function init() {
		add_action( 'wp_enqueue_scripts', array( $this, 'initPinterestScript' ), 9 );
		add_action( 'wp_loaded', array( $this, 'registerEvents' ) );

		add_action( 'shutdown', array( $this, 'saveDeferredEventsData' ) );
	}

	/**
	 * Init pinterest analytics sdk
	 */
	public function initPinterestScript() {
		wp_enqueue_script(
			self::PINTEREST_ANALYTICS_INIT_SCRIPT_HANDLE,
			$this->fileManager->locateAsset( 'frontend/analytics/pinterest-analytics-init.js' ),
			array(),
			PinterestPlugin::$version,
			false
		);

		$tagId = $this->integration->get_option( 'tag_id' );

		wp_localize_script( self::PINTEREST_ANALYTICS_INIT_SCRIPT_HANDLE,
			'pinterestSettings',
			array( 'tagId' => $tagId ) );
	}

	/**
	 * Register events
	 */
	public function registerEvents() {
		$this->loadEvents();

		foreach ( $this->events as $event ) {

			if ( ! $event->enabled() ) {
				continue;
			}

			if ( $event->isDeferred() ) {
				$deferredEventData = $this->restoreDeferredEventData( $event->getName() );
				if ( $deferredEventData ) {
					$event->setData( $deferredEventData );
				}
			}

			add_action( 'wp_enqueue_scripts', function () use ( $event ) {
				if ( $event->fired() ) {
					$event->trigger();
				}
			}, 11 );

		}

		$this->clearDeferredEvents();
	}

	private function loadEvents() {
		$this->events = array(
			PageVisitEvent::class       => new PageVisitEvent( $this->integration ),


			// LeadEvent::class => new LeadEvent($this->integration), //todo: Check why this is commented. Delete if this not needed anymore.
			ProductCategoryEvent::class => new ProductCategoryEvent( $this->integration ),
			SearchEvent::class          => new SearchEvent( $this->integration ),
			AddToCartEvent::class       => new AddToCartEvent( $this->integration, $this->fileManager ),
			EventCheckout::class        => new EventCheckout( $this->integration, $this->primaryCategoryModel )
		);

		$this->events = apply_filters( 'woocommerce_pinterest_tracking_events', $this->events );
	}

	/**
	 * Restore default event from transient
	 *
	 * @param $eventName
	 *
	 * @return array|null
	 */
	private function restoreDeferredEventData( $eventName ) {
		$deferredEventsData = get_transient( self::DEFERRED_EVENTS_TRANSIENT_NAME );
		if ( isset( $deferredEventsData[ $eventName ] ) ) {
			return (array) $deferredEventsData[ $eventName ];
		}

		return null;
	}

	private function clearDeferredEvents() {
		delete_transient( self::DEFERRED_EVENTS_TRANSIENT_NAME );
	}

	public function saveDeferredEventsData() {
		$deferredEventsData = array();
		foreach ( $this->events as $event ) {
			if ( $event->isDeferred() && $event->getData() ) {
				$deferredEventsData[ $event->getName() ] = $event->getData();
			}
		}

		set_transient( self::DEFERRED_EVENTS_TRANSIENT_NAME, $deferredEventsData );
	}

	/**
	 * Is analytics enabled
	 *
	 * @return bool
	 */
	public function enabled() {
		return $this->integration->get_option( 'enable_track_conversion' ) === 'yes' && $this->integration->get_option( 'tag_id' );
	}
}
