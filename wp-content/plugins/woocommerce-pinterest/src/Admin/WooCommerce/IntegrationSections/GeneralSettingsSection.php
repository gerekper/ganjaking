<?php


namespace Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections;

use Premmerce\SDK\V2\FileManager\FileManager;
use Premmerce\WooCommercePinterest\Admin\WooCommerce\PinterestIntegration;
use Premmerce\WooCommercePinterest\Pinterest\Api\ApiState;
use Premmerce\WooCommercePinterest\Pinterest\DescriptionPlaceholders;

/**
 * Class GeneralSettingsSection
 *
 * @package Premmerce\WooCommercePinterest\Admin\WooCommerce\IntegrationSections
 *
 * This class is a storage for General Settings section fields on settings page
 */
class GeneralSettingsSection implements IntegrationSectionInterface {

	const PINTEREST_DEFAULT_IMAGE_HEIGHT = 1500;

	const PINTEREST_DEFAULT_IMAGE_WIDTH = 1000;

	const PINTEREST_DEFAULT_IMAGE_CROP = false;

	/**
	 * PinterestIntegration instance
	 *
	 * @var PinterestIntegration
	 */
	private $integration;

	/**
	 * FileManager instance
	 *
	 * @var FileManager
	 */
	private $fileManager;

	/**
	 * ApiState instance
	 *
	 * @var ApiState
	 */
	private $apiState;

	/**
	 * DescriptionPlaceholders instance
	 *
	 * @var DescriptionPlaceholders
	 */
	private $descriptionPlaceholders;

	/**
	 * GeneralSettingsSection constructor.
	 *
	 * @param PinterestIntegration $integration
	 * @param FileManager $fileManager
	 * @param ApiState $apiState
	 * @param DescriptionPlaceholders $descriptionPlaceholders
	 */
	public function __construct(
		PinterestIntegration $integration,
		FileManager $fileManager,
		ApiState $apiState,
		DescriptionPlaceholders $descriptionPlaceholders
	) {

		$this->integration             = $integration;
		$this->fileManager             = $fileManager;
		$this->apiState                = $apiState;
		$this->descriptionPlaceholders = $descriptionPlaceholders;
	}

	public function getTitle() {
		return __( 'General settings', 'woocommerce-pinterest' );
	}

	public function getSlug() {
		return 'general_section';
	}

	public function getFields() {
		$boards = $this->getBoardsForOptionsSelect();

		return array(
			'pinterest_image_size_type' => array(
				'title'       => __( 'Pinned image size', 'woocommerce-pinterest' ),
				'type'        => 'select',
				'options'     => array(
					'pinterest_image' => __( 'Pinterest size', 'woocommerce-pinterest' ),
					'full'            => __( 'Original image size', 'woocommerce-pinterest' ),
				),
				'default'     => 'full',
				'description' => __( 'What size of images would be pinned.', 'woocommerce-pinterest' ),
			),

			'pinterest_image_size' => array(
				'title'             => __( 'Pinterest image size', 'woocommerce-pinterest' ),
				'type'              => 'pinterest_size',
				'default'           => $this->getPinterestImageSizeDefaults(),
				'sanitize_callback' => array( $this, 'sanitizePinterestImageSize' )
			),

			'board' => array(
				'title'       => __( 'Default board	', 'woocommerce-pinterest' ),
				'type'        => 'select',
				'options'     => $boards,
				'description' => $this->renderBoardDescription(),
				'desc_tip'    => __( 'The board where your product Pins will be placed if you don\'t select any other boards for your Pins or Category.',
					'woocommerce-pinterest' )
			),

			'pin_description' => array(
				'title'       => __( 'Pin description', 'woocommerce-pinterest' ),
				'type'        => 'textarea',
				'desc_tip'    => __( 'Description of the Pin', 'woocommerce-pinterest' ),
				'description' => $this->renderDescriptionVariables(),
			),

			'enable_richpins' => array(
				'type'        => 'checkbox',
				'title'       => __( 'Rich product pins', 'woocommerce-pinterest' ),
				'label'       => __( 'Enable Rich Pins', 'woocommerce-pinterest' ),
				'description' => $this->renderRichPinsDescription(),
				'desc_tip'    => __(
					'Product Pins add Open Graph markup and make it easier for Pinners to see 
                        information about things you sell and include pricing, availability and buy location.',
					'woocommerce-pinterest'
				),
			),

			'enable_richpins_advanced' => array(
				'type'              => 'richpins_advanced',
				'default'           => $this->getRichpinAdvancedDefaults(),
				'sanitize_callback' => array( $this, 'sanitizeRichpinAdvanced' )
			),

			'enable_yoast_compatibility' => array(
				'type'  => 'checkbox',
				'label' => __( 'Enable Rich Pins compatibility with Yoast SEO', 'woocommerce-pinterest' )
			)
		);
	}

	/**
	 * Sanitize richpin advanced
	 *
	 * @param mixed $value
	 *
	 * @return array
	 */
	public function sanitizeRichpinAdvanced( $value ) {

		$value = is_array( $value ) ? $value : array();

		$value = array_map( function ( $field ) {
			return (bool) $field ? 'yes' : 'no';
		}, $value );

		return $value;
	}

	public function getRichpinAdvancedDefaults() {
		return array(
			'brand'                  => 'yes',
			'url'                    => 'yes',
			'title'                  => 'yes',
			'site_name'              => 'yes',
			'description'            => 'yes',
			'product_price_amount'   => 'yes',
			'price_standard_amount'  => 'yes',
			'product_price_currency' => 'yes',
			'availability'           => 'yes',
			'type'                   => 'yes',
		);
	}

	/**
	 * Get boards for options select
	 *
	 * @return array
	 */
	private function getBoardsForOptionsSelect() {
		$boards = $this->getBoards();

		$boards = array_column( $boards, 'name', 'id' );

		$boards = array( '0' => __( 'None', 'woocommerce-pinterest' ) ) + $boards;

		$boards = apply_filters( 'woocommerce_pinterest_boards_options', $boards, $this->integration );

		return $boards;
	}

	/**
	 * Get boards
	 *
	 * @return array
	 */
	private function getBoards() {
		return (array) $this->integration->get_option( 'boards', array() );
	}

	/**
	 * Sanitize Pinterest Image Size
	 *
	 * @param mixed $value
	 *
	 * @return array
	 */
	public function sanitizePinterestImageSize( $value ) {
		$value = wp_parse_args( (array) $value, $this->getPinterestImageSizeDefaults() );

		$value['h']    = intval( $value['h'] );
		$value['w']    = intval( $value['w'] );
		$value['crop'] = (bool) $value['crop'];

		return $value;
	}

	/**
	 * Get Pinterest Image Size Defaults
	 *
	 * @return array
	 */
	private function getPinterestImageSizeDefaults() {
		return array(
			'h'    => self::PINTEREST_DEFAULT_IMAGE_HEIGHT,
			'w'    => self::PINTEREST_DEFAULT_IMAGE_WIDTH,
			'crop' => self::PINTEREST_DEFAULT_IMAGE_CROP,
		);
	}

	/**
	 * Render board description that depends from api state
	 *
	 * @return string
	 */
	private function renderBoardDescription() {
		return $this->fileManager->renderTemplate( 'admin/woocommerce/board-description.php', array(
			'state' => $this->apiState,
		) );
	}

	/**
	 * Render buttons for variables
	 *
	 * @return string
	 */
	private function renderDescriptionVariables() {
		$variables = apply_filters( 'woocommerce_pinterest_admin_description_variables', $this->descriptionPlaceholders->getPlaceholders() );

		return $this->fileManager->renderTemplate( 'admin/woocommerce/pin-description-variables.php', array(
			'variables' => $variables
		) );
	}

	/**
	 * Render Rich Pins description
	 *
	 * @return string
	 */
	private function renderRichPinsDescription() {
		/* translators: First '%s' is replaced with Pinterest docs link, the second is replaced with "validate" */
		$tag = sprintf(
			'<a href="%s" target="_blank">%s</a>',
			'https://developers.pinterest.com/tools/url-debugger/',
			__( 'validate', 'woocommerce-pinterest' )
		);

		/* translators: '%s' is replaced with html <a> tag */

		return sprintf( __(
			'After you turn on Rich Pins, you must %s the product page.',
			'woocommerce-pinterest'
		), $tag
		);
	}
}
