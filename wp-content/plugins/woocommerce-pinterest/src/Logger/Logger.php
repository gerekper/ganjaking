<?php namespace Premmerce\WooCommercePinterest\Logger;

use Premmerce\WooCommercePinterest\Admin\WooCommerce\PinterestIntegration;
use Premmerce\WooCommercePinterest\PinterestException;

/**
 * Class Logger
 *
 * @package Premmerce\WooCommercePinterest\Logger
 *
 * This class is responsible for logging
 */
class Logger {

	/**
	 * Is debug enabled
	 *
	 * @var bool
	 */
	private $debugEnabled;

	/**
	 * Logger constructor.
	 *
	 * @param bool $debugEnabled
	 */
	public function __construct( $debugEnabled ) {
		$this->debugEnabled = $debugEnabled;
	}

	/**
	 * Add string to log
	 *
	 * @param $message
	 * @param null $logLevel
	 */
	public function log( $message, $logLevel = null ) {

		if ( $this->debugEnabled ) {
			if ( function_exists( 'wc_get_logger' ) ) {
				$logger   = wc_get_logger();
				$logLevel = $logLevel ? $logLevel : \WC_Log_Levels::NOTICE;
				$logger->log( $logLevel, $message, array( 'source' => 'Pinterest for Woocommerce' ) );
			} else {
				error_log( $message );
			}
		}

	}

	/**
	 * Add PinterestException to log
	 *
	 * @param PinterestException $e
	 */
	public function logPinterestException( PinterestException $e ) {
		$message = 'Caught exception: ' . $e;

		if ( $e->getPrevious() ) {
			$message .= PHP_EOL . 'Previous exception: ' . PHP_EOL . $e;
		}

		$logLevel = null;
		if ( class_exists( \WC_Log_Levels::class ) ) {
			$logLevel = \WC_Log_Levels::WARNING;
		}
		$this->log( $message, $logLevel );
	}
}
