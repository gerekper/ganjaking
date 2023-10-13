<?php

class WoocommerceGpfDebugService {

	/**
	 * @var bool  Whether debug is enabled or not.
	 */
	private $enabled = false;

	/**
	 * @var WC_Logger
	 */
	private $wc_logger;

	/**
	 * @var string[]
	 */
	private $wc_context;

	/**
	 * @var mixed|string
	 */
	private $destination;

	/**
	 * @var bool
	 */
	private $ready = false;

	/**
	 * WoocommerceGpfDebugService constructor.
	 */
	public function __construct() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$debug_key         = get_option( 'woocommerce_gpf_debug_key' );
		$this->wc_context  = [ 'source' => 'woocommerce-product-feeds' ];
		$this->enabled     = isset( $_REQUEST['debug_key'] ) &&
							$_REQUEST['debug_key'] === $debug_key;
		$this->destination = 'wc-log';

		if ( isset( $_REQUEST['destination'] ) &&
			$_REQUEST['destination'] === 'xml'
		) {
			$this->destination = 'xml';
		}

		if ( did_action( 'plugins_loaded' ) ) {
			$this->get_logger();
		} else {
			add_action( 'plugins_loaded', [ $this, 'get_logger' ] );
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Grab a WC_Logger instance.
	 */
	public function get_logger() {
		if ( is_callable( 'wc_get_logger' ) ) {
			$this->wc_logger = wc_get_logger();
			$this->ready     = true;
		}
	}

	/**
	 * Whether debug is active.
	 *
	 * @return bool
	 */
	public function debug_active() {
		return $this->enabled;
	}

	/**
	 * Log a message with optional sprintf replacements.
	 *
	 * @param string $message The message.
	 * @param array $args Array of replacements to be sprintf'd in.
	 */
	public function log( $message, $args = [] ) {
		if ( ! $this->enabled ) {
			return;
		}
		$log_msg = sprintf( $message, ...$args );
		if ( 'wc-log' === $this->destination ) {
			$this->ready && $this->wc_logger->debug(
				$log_msg,
				$this->wc_context
			);
		} elseif ( 'xml' === $this->destination ) {
			echo '<!-- ';
			// @TODO - can we just ignore as per below?
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $log_msg;
			echo ' -->' . PHP_EOL;
		}
	}
}
