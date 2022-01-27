<?php namespace Premmerce\WooCommercePinterest\Pinterest;

use Premmerce\WooCommercePinterest\Admin\WooCommerce\PinterestIntegration;
use Premmerce\WooCommercePinterest\Pinterest\Api\PinterestApiException;
use Premmerce\WooCommercePinterest\PinterestException;
use Premmerce\WooCommercePinterest\Tags\PinterestTagsController;
use Premmerce\WooCommercePinterest\ServiceContainer;
use WC_Product;
use WC_Product_Variable;

/**
 * Class PinDataGenerator
 * Responsible for generating data for Pinterest API requests
 *
 * @package Premmerce\WooCommercePinterest\Pinterest
 */
class PinDataGenerator {

	/**
	 * PinterestTagsController instance
	 *
	 * @var PinterestTagsController
	 */
	private $pinterestTagsController;

	/**
	 * PinterestIntegration instance
	 *
	 * @var PinterestIntegration
	 */
	private $pinterestIntegration;


	/**
	 * PinDataGenerator constructor.
	 *
	 * @param PinterestTagsController $pinterestTagsController
	 * @param PinterestIntegration $pinterestIntegration
	 */
	public function __construct( PinterestTagsController $pinterestTagsController, PinterestIntegration $pinterestIntegration ) {
		$this->pinterestTagsController = $pinterestTagsController;
		$this->pinterestIntegration    = $pinterestIntegration;
	}

	/**
	 * Generate pin data
	 *
	 * @param array $pin
	 *
	 * @return array
	 * @throws PinterestApiException | PinterestException
	 */
	public function generateData( $pin ) {

		$product    = wc_get_product( $pin['post_id'] );
		$attachment = wp_get_attachment_image_url( $pin['attachment_id'] );

		$settings = get_option( 'woocommerce_pinterest_settings' );
		$board    = isset( $pin['board'] ) ? $pin['board'] : '';


		if ( ! $board ) {
			throw new PinterestApiException( __( 'Board not specified', 'woocommerce-pinterest' ) );
		}

		if ( ! $product instanceof WC_Product ) {
			throw new PinterestApiException( __( 'Product not found', 'woocommerce-pinterest' ) );
		}

		if ( $product->get_status() !== 'publish' ) {
			throw new PinterestApiException( __( 'Product not published', 'woocommerce-pinterest' ) );
		}

		if ( ! $attachment ) {
			throw new PinterestApiException( __( 'Attachment not found', 'woocommerce-pinterest' ) );
		}

		$size = isset( $settings['pinterest_image_size_type'] ) ? $settings['pinterest_image_size_type'] : 'full';

		$data = array(
			'board_id'    => $pin['board'],
			'title'       => $this->generateTitle( $pin, $product ),
			'description' => $this->generateNote( $pin, $product ),
			'alt_text'    => $this->generateAlt( $pin, $product ),
			'source_url'  => get_permalink( $pin['post_id'] ),
			'image_url'   => wp_get_attachment_image_url( $pin['attachment_id'], $size ),
		);

		$data = apply_filters( 'woocommerce_pinterest_pin_data', $data, $pin );

		return $data;
	}

  /**
   * Generate сarousel data
   *
   * @param array $pin
   * @param array $imagesResponse
   *
   * @return array
   * @throws PinterestApiException | PinterestException
   */
  public function generateCarouselData( $pin, $imagesResponse ) {

    $product              = wc_get_product( $pin['post_id'] );
    $attachments          = explode( ',', $pin['carousel_ids'] );
    $settings             = get_option( 'woocommerce_pinterest_settings' );
    $size                 = isset( $settings['pinterest_image_size_type'] ) ? $settings['pinterest_image_size_type'] : 'full';
    $pin['attachment_id'] = $attachments[0];

    $data = array(
      'board_id'           => sanitize_key($pin['board']),
      'image_url'          => esc_url_raw(wp_get_attachment_image_url( $pin['attachment_id'], $size )),
      'carousel_data_json' => json_encode(array(
        'carousel_slots' => $this->generateCarouselDataJson( $pin, $imagesResponse, $product ),
        'index'          => 0,
      )),
    );

    $data = apply_filters( 'woocommerce_pinterest_carousel_data', $data, $pin );

    return $data;
  }

  /**
   * Generate carousel data json
   *
   * @param array $pin
   * @param array $imagesResponse
   * @param WC_Product $product
   *
   * @return array
   * @throws PinterestException
   */
  public function generateCarouselDataJson( $pin, $imagesResponse, $product ) {
    $carouselDataJson = array();

    if( empty( $imagesResponse ) ) {

      $images = array_unique( array_map( 'intval', explode( ',', $pin['carousel_ids'] ) ) );

      foreach ($images as $image) {

        $sanitizedData = array(
          'link'            => esc_url_raw(get_site_url()),
          'title'           => esc_html($this->generateTitle( $pin, $product )),
          'description'     => esc_html($this->generateNote( $pin, $product )),
        );

        $carouselDataJson[] = $sanitizedData;
      }
    } else {
      foreach ($imagesResponse as $image) {

        $sanitizedData = array(
          'link'            => esc_url_raw(get_site_url()),
          'title'           => esc_html($this->generateTitle( $pin, $product )),
          'description'     => esc_html($this->generateNote( $pin, $product )),
          'image_signature' => sanitize_key($image['image_signature']),
          'tracking_id'     => sanitize_key($image['tracking_id']),
        );

        $carouselDataJson[] = $sanitizedData;
      }
    }

    return $carouselDataJson;
  }

	/**
	 * Generate carousel image urls
	 *
	 * @param array $pin
	 *
	 * @return array
	 * @throws PinterestApiException
	 */
	public function generateCarouselImagesData( $pin ) {

		$product       = wc_get_product( $pin['post_id'] );
		$attachments   = explode( ',', $pin['carousel_ids'] );
		$settings      = get_option( 'woocommerce_pinterest_settings' );
    $size          = isset( $settings['pinterest_image_size_type'] ) ? $settings['pinterest_image_size_type'] : 'full';
    $attachmentArr = array();

    foreach ( $attachments as $attachment ) {
      $imgUrl = wp_get_attachment_image_url( $attachment, $size );
      if( $imgUrl ) {
        $attachmentArr[] = $imgUrl;
      }
		}

    if ( ! $product instanceof WC_Product ) {
      throw new PinterestApiException( esc_html__( 'Product not found', 'woocommerce-pinterest' ) );
    }

    if ( empty( $attachmentArr ) ) {
      throw new PinterestApiException( esc_html__( 'Attachments not found', 'woocommerce-pinterest' ) );
    }

    $data = array(
      'image_urls' => implode( ',', $attachmentArr )
    );

    $data = apply_filters( 'woocommerce_pinterest_carousel_image_urls', $data, $pin );

    return $data;
	}

	/**
	 * Generate pin note string
	 *
	 * @param array $pin
	 *
	 * @param WC_Product $product
	 *
	 * @return string|null
	 *
	 * @throws PinterestException
	 */
	public function generateNote( $pin, $product ) {
		$placeholders = $this->getPlaceholders( $product, $pin['attachment_id'] );

		$product = $this->getActualProduct( $product, $pin['attachment_id'] );

		$template = $this->getPinDescriptionTemplate( $product );

		$template = apply_filters( 'woocommerce_pinterest_description_template', $template, $pin );

		$placeholders = apply_filters( 'woocommerce_pinterest_description_placeholders', $placeholders, $pin );

		$note = strtr( $template, $placeholders );

		$id = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();

		$tags = $this->pinterestTagsController->getTagsForProduct( $id );
		$tags = apply_filters( 'woocommerce_pinterest_description_tags', $tags, $pin );
		$note .= ' ' . $this->formatTagNamesToString( $tags );
		$note = strip_tags( $note );

		return apply_filters( 'woocommerce_pinterest_description', trim( $note ), $pin, $product );
	}

  /**
   * Generate pin alt text
   *
   * @param array $pin
   *
   * @param WC_Product $product
   *
   * @return string
   */
  public function generateAlt( $pin, $product )
  {
    $settings    = get_option( 'woocommerce_pinterest_settings' );
    $altSettings = isset( $settings['pin_alt'] ) ? $settings['pin_alt'] : false;

    switch ( $altSettings ) {
      case 'alt_title':
        $altText = $product->get_title();
        break;

      case 'alt_image':
        $altText = get_post_meta( $pin['attachment_id'], '_wp_attachment_image_alt', true );
        break;

      case 'alt_both':
        $altText = get_post_meta( $pin['attachment_id'], '_wp_attachment_image_alt', true );
        if ( empty( $altText ) ) {
          $altText = $product->get_title();
        }
        break;

      default:
        $altText = '';
        break;
    }

    $altText = strip_tags( $altText );
    if( mb_strlen( $altText ) > 500 ) {
      $altText = substr($altText, 0, 500);
    }

    return apply_filters( 'woocommerce_pinterest_alt_text', trim( $altText ), $pin, $product );
  }

  /**
   * Generate pin title string
   *
   * @param array $pin
   *
   * @param WC_Product $product
   *
   * @return string|null
   *
   * @throws PinterestException
   */
	public function generateTitle( $pin, $product )
  {
    $placeholders = $this->getPlaceholders( $product, $pin['attachment_id'] );

    $product = $this->getActualProduct( $product, $pin['attachment_id'] );

    $template = $this->getPinTitleTemplate( $product );

    $template = apply_filters( 'woocommerce_pinterest_title_template', $template, $pin );

    $placeholders = apply_filters( 'woocommerce_pinterest_title_placeholders', $placeholders, $pin );

    $note = strtr( $template, $placeholders );

    $id = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();

    $tags = $this->pinterestTagsController->getTagsForProduct( $id );
    $tags = apply_filters( 'woocommerce_pinterest_title_tags', $tags, $pin );
    $note .= ' ' . $this->formatTagNamesToString( $tags );
    $note = strip_tags( $note );
    if( mb_strlen( $note ) > 100 ) {
      $note = substr($note, 0, 100);
    }

    return apply_filters( 'woocommerce_pinterest_title', trim( $note ), $pin, $product );
  }

	/**
	 * Format array of term names to the string looks like "#tag1, #tag2, #tag3"
	 *
	 * @param string[] $tagsNames
	 *
	 * @return string
	 */
	private function formatTagNamesToString( array $tagsNames ) {

		if ( $tagsNames ) {
			$tagsString = '#' . implode( ', #', $tagsNames );
		}

		return isset( $tagsString ) ? $tagsString : '';
	}

  /**
   * Get Pin title template depend on product and global pin title
   *
   * @param WC_Product $product
   *
   * @return string|void
   */
  protected function getPinTitleTemplate( WC_Product $product ) {
    $container  = ServiceContainer::getInstance();
    $pinService = $container->getPinService();

    if ( $product->is_type( 'variation' ) && ! $pinService->isVariationHasPinDescription( $product->get_id() ) ) {
      $productId = $product->get_parent_id();
    } else {
      $productId = $product->get_id();
    }

    $settings       = get_option( 'woocommerce_pinterest_settings' );
    $globalTemplate = isset( $settings['pin_title'] ) ? $settings['pin_title'] : '';

    $pinTitle = $pinService->getProductPinTitleTemplate( $productId );

    return $pinTitle ? $pinTitle : $globalTemplate;
  }

	/**
	 * Get Pin description template depend on product and global pin description
	 *
	 * @param WC_Product $product
	 *
	 * @return string|void
	 */
	protected function getPinDescriptionTemplate( WC_Product $product ) {
		$container  = ServiceContainer::getInstance();
		$pinService = $container->getPinService();

		if ( $product->is_type( 'variation' ) && ! $pinService->isVariationHasPinDescription( $product->get_id() ) ) {
			$productId = $product->get_parent_id();
		} else {
			$productId = $product->get_id();
		}

		$settings       = get_option( 'woocommerce_pinterest_settings' );
		$globalTemplate = isset( $settings['pin_description'] ) ? $settings['pin_description'] : '';

		$pinDescription = $pinService->getProductPinDescriptionTemplate( $productId );

		return $pinDescription ? $pinDescription : $globalTemplate;
	}

	/**
	 * Set placeholders for pin description
	 *
	 * @param WC_Product $product
	 * @param int $attachmentId
	 *
	 * @return array
	 */
	public function getPlaceholders( WC_Product $product, $attachmentId ) {
		$formattedPrice = wp_strip_all_tags( wc_price( $product->get_price() ) );
		if ( $product instanceof WC_Product_Variable ) {
			$formattedPrice = wp_strip_all_tags( $product->get_price_html() );
		}

		$placeholders['{excerpt}']    = wp_strip_all_tags( $product->get_short_description() );
		$placeholders['{site_title}'] = get_bloginfo( 'name' );

		$product = $this->getActualProduct( $product, $attachmentId );

		$placeholders['{link}']        = get_permalink( $product->get_id() );
		$placeholders['{price}']       = html_entity_decode($formattedPrice);
		$placeholders['{title}']       = $product->get_name();
		$placeholders['{description}'] = wp_strip_all_tags( $product->get_description() );

		return $placeholders;
	}

	/**
	 * Get product tied to attachment.
	 *
	 * @param WC_Product $product
	 * @param int $attachmentId
	 *
	 * @return WC_Product
	 */
	protected function getActualProduct( WC_Product $product, $attachmentId ) {
		if ( $product instanceof WC_Product_Variable ) {

			foreach ( $product->get_children() as $variationId ) {
				$image     = get_post_thumbnail_id( $variationId );
				$variation = wc_get_product( $variationId );
				if ( $image === $attachmentId && $variation ) {
					return $variation;
				}
			}
		}

		return $product;
	}
}
