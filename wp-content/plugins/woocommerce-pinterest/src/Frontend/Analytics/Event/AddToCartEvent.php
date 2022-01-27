<?php


namespace Premmerce\WooCommercePinterest\Frontend\Analytics\Event;

use Premmerce\SDK\V2\FileManager\FileManager;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\PinterestIntegration;
use Premmerce\WooCommercePinterest\PinterestPlugin;
use \WC_Product;

class AddToCartEvent extends AbstractEvent implements EventInterface {

	const AJAX_GET_PRODUCT_NONCE = 'woocommerce-pinterest-get-product-nonce';

	/**
	 * Product id
	 *
	 * @var int
	 */
	private $productId;

	/**
	 * Added to cart quantity
	 *
	 * @var int
	 */
	private $quantity;

	/**
	 * FileManager instance
	 *
	 * @var FileManager
	 */
	private $fileManager;

	/**
	 * Was event fired
	 *
	 * @var bool
	 */
	protected $fired = false;

	/**
	 * AddToCartEvent constructor.
	 *
	 * @param PinterestIntegration $integration
	 * @param FileManager $fileManager
	 */
	public function __construct( PinterestIntegration $integration, FileManager $fileManager ) {
		$this->fileManager = $fileManager;
		$this->init();

		parent::__construct( $integration );
	}

	public function init() {

		add_action( 'woocommerce_add_to_cart', array( $this, 'setAddToCartData' ), 9, 3 );
		add_action( 'wp_enqueue_scripts', array( $this, 'registerAddToCartScript' ) );
		add_action( 'wc_ajax_woocommerce_pinterest_get_product_data', array( $this, 'ajaxGetProductData' ) );
	}

	/**
	 * Set Add to cart data
	 *
	 * @param $cartHash
	 * @param $productId
	 * @param $quantity
	 */
	public function setAddToCartData( $cartHash, $productId, $quantity ) {
		if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) || ! wp_doing_ajax() ) {
			$this->productId = $productId;
			$this->quantity  = $quantity;
			$this->fired     = true;
		}
	}

	/**
	 * Return event status
	 *
	 * @return bool
	 */
	public function enabled() {
		return $this->isEnabledInOptions();
	}

	/**
	 * Return if event was fired
	 *
	 * @return bool
	 */
	public function fired() {
		return $this->fired;
	}

	/**
	 * Return event name
	 *
	 * @return string
	 */
	public function getName() {
		return 'AddToCart';
	}

	/**
	 * Return data to be sent with analytics event
	 *
	 * @return array
	 */
	public function getData() {
		if ( $this->data ) {
			return $this->data;
		}

		$product = wc_get_product( $this->productId );

		if ( $product && $product->is_purchasable() ) {
			return $this->fired() ? array(
				'product_id' => $this->productId,
				'value'      => $product->get_price(),
				'quantity'   => $this->quantity,
				'currency'   => get_woocommerce_currency()
			) : array();
		}

		return array();
	}

	/**
	 * Return deferred status
	 * Deferred if redirect after add to cart enabled
	 *
	 * @return bool
	 */
	public function isDeferred() {
		return true;
	}

	public function trigger() {
		parent::trigger();

		$this->reset();
	}

	private function reset() {
		$this->data      = array();
		$this->productId = null;
		$this->quantity  = null;
		$this->fired     = null;
	}

	/**
	 * Register add to cart script
	 * if enabled ajax  add to cart at archive page enqueue scripts to handle
	 */
	public function registerAddToCartScript() {
		$version = PinterestPlugin::$version;
		if ( 'yes' === get_option( 'woocommerce_enable_ajax_add_to_cart' ) ) {
			wp_enqueue_script(
				'pinterest-analytics-add-to-cart',
				$this->fileManager->locateAsset( 'frontend/analytics/track-add-to-cart' . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min' ) . '.js' ),
				array( 'jquery' ),
				$version,
				false //todo: check if it can be enqueued in footer
			);

			wp_localize_script( 'pinterest-analytics-add-to-cart', 'wooPinterestAnalyticsConfig', array(
				'ajaxNonce' => wp_create_nonce( self::AJAX_GET_PRODUCT_NONCE ),
			) );
		}
	}

	/**
	 * Wc ajax endpoint
	 * Send product data for AddToCart event
	 */
	public function ajaxGetProductData() {
		if ( check_ajax_referer( self::AJAX_GET_PRODUCT_NONCE ) && ! empty( $_REQUEST['id'] ) ) {
			$product = wc_get_product( intval( $_REQUEST['id'] ) );

			$data = array( 'status' => false );
			if ( $product instanceof WC_Product ) {

				$data = array(
					'price'                  => $product->get_price(),
					'currency'               => get_woocommerce_currency(),
					'status'                 => 1,
					'isEnhancedMatchEnabled' => $this->integration->get_option( 'enable_enhanced_match' ) === 'yes',
					'tagId'                  => $this->integration->get_option( 'tag_id' ),
				);

				if ( is_user_logged_in() ) {
					$user = new \WP_User( get_current_user_id() );

					$data['userEmail'] = $user->user_email;
				}
			}
			wp_send_json( $data );
		}
	}
}
