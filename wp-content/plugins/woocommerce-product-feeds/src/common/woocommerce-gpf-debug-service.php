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
		$debug_key        = get_option( 'woocommerce_gpf_debug_key' );
		$this->wc_context = [ 'source' => 'woocommerce-product-feeds' ];

		$this->enabled = isset( $_REQUEST['debug_key'] ) &&
						 $_REQUEST['debug_key'] === $debug_key;

		$this->destination = 'wc-log';
		if ( isset( $_REQUEST['destination'] ) &&
			 in_array( $_REQUEST['destination'], [ 'xml' ], true )
		) {
			$this->destination = $_REQUEST['destination'];
		}

		if ( did_action( 'plugins_loaded' ) ) {
			$this->get_logger();
		} else {
			add_action( 'plugins_loaded', [ $this, 'get_logger' ] );
		}
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
			echo $log_msg;
			echo ' -->';
		}

	}
}
