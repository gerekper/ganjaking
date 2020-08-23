<?php
/**
 * WooCommerce Product Reviews Pro
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Product Reviews Pro to newer
 * versions in the future. If you wish to customize WooCommerce Product Reviews Pro for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-product-reviews-pro/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2015-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

/**
 * WC Product Reviews Pro Contribution Type class
 *
 * Handles contribution type specifics, such as title, call to action, fields, etc.
 *
 * @since 1.0.0
 */
class WC_Product_Reviews_Pro_Contribution_Type {


	/** @public string contribution type */
	public $type;


	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @param string $type
	 */
	public function __construct( $type ) {

		$this->type = $type;
	}


	/**
	 * Get the title for the contribution type
	 *
	 * @param bool $plural
	 * @return string
	 */
	public function get_title( $plural = false ) {

		switch ( $this->type ) {

			case 'review':
				$title = $plural ? __( 'Reviews', 'woocommerce-product-reviews-pro' ) : __( 'Review', 'woocommerce-product-reviews-pro' );
			break;

			case 'question':
				$title = $plural ? __( 'Questions', 'woocommerce-product-reviews-pro' ) : __( 'Question', 'woocommerce-product-reviews-pro' );
			break;

			case 'photo':
				$title = $plural ? __( 'Photos', 'woocommerce-product-reviews-pro' ) : __( 'Photo', 'woocommerce-product-reviews-pro' );
			break;

			case 'video':
				$title = $plural ? __( 'Videos', 'woocommerce-product-reviews-pro' ) : __( 'Video', 'woocommerce-product-reviews-pro' );
			break;

			case 'contribution_comment':

				$title = $plural ? __( 'Comments', 'woocommerce-product-reviews-pro' ) : __( 'Comment', 'woocommerce-product-reviews-pro' );
			break;

			// default behaviour is to capitalize the first letter of the type
			default:
				$title = ucfirst( $this->type );
			break;

		}

		/**
		 * Filter contribution type title
		 *
		 * @since 1.0.0
		 * @param string $title The title
		 * @param string $type The contribution type
		 */
		return apply_filters( 'wc_product_reviews_pro_contribution_type_title', $title, $this->type );
	}


	/**
	 * Get the call to action for the contribution type
	 *
	 * @return string
	 */
	public function get_call_to_action() {

		$cta = '';

		switch ( $this->type ) {

			case 'review':

				if ( is_user_logged_in() && wc_product_reviews_pro_get_user_review_count( get_current_user_id(), get_the_ID() ) > 0 ) {
					$cta = __( 'Update my Review', 'woocommerce-product-reviews-pro' );
				} else {
					$cta = __( 'Leave a Review', 'woocommerce-product-reviews-pro' );
				}

			break;

			case 'question':
				$cta = __( 'Ask a Question', 'woocommerce-product-reviews-pro' );
			break;

			case 'photo':
				$cta = __( 'Post a Photo', 'woocommerce-product-reviews-pro' );
			break;

			case 'video':
				$cta = __( 'Post a Video', 'woocommerce-product-reviews-pro' );
			break;

		}

		/**
		 * Filter contribution type call to action
		 *
		 * @since 1.0.0
		 * @param string $cta The call to action
		 * @param string $type The contribution type
		 */
		return apply_filters( 'wc_product_reviews_pro_contribution_type_call_to_action', $cta, $this->type );
	}


	/**
	 * Get the contribution list title for the contribution type
	 *
	 * @param int $count Number of contributions
	 * @param int|null $rating Optional. Review rating (applies only to reviews)
	 * @return string
	 */
	public function get_list_title( $count, $rating = null ) {

		$list_title = '';

		switch ( $this->type ) {

			case 'review':

				if ( $rating > 0 ) {
					$list_title = sprintf( _n( 'One review with a %2$d-star rating', '%1$d reviews with a %2$d-star rating', $count, 'woocommerce-product-reviews-pro' ), $count, $rating );
				} else {
					$list_title = sprintf( _n( 'One review', '%d reviews', $count, 'woocommerce-product-reviews-pro' ), $count );
				}

			break;

			case 'question':
				$list_title = sprintf( _n( 'One question', '%d questions', $count, 'woocommerce-product-reviews-pro' ), $count );
			break;

			case 'photo':
				$list_title = sprintf( _n( 'One photo', '%d photos', $count, 'woocommerce-product-reviews-pro' ), $count );
			break;

			case 'video':
				$list_title = sprintf( _n( 'One video', '%d videos', $count, 'woocommerce-product-reviews-pro' ), $count );
			break;

		}

		/**
		 * Filter the list title for the contribution type
		 *
		 * @since 1.0.0
		 * @param string $list_title The list title for the contribution type
		 * @param string $type The contribution type
		 * @param int $count The number of contributions
		 * @param int $rating Review rating (applies only to reviews)
		 */
		return apply_filters( 'wc_product_reviews_pro_contribution_type_list_title', $list_title, $this->type, $count, $rating );
	}


	/**
	 * Get the tab title for the contribution type
	 *
	 * @param int $count  Number of contributions
	 * @return string
	 */
	public function get_tab_title( $count ) {

		switch ( $this->type ) {

			case 'review':
				$tab_title = sprintf( __( 'Reviews (%d)', 'woocommerce-product-reviews-pro' ), $count );
			break;

			case 'question':
				$tab_title = sprintf( __( 'Questions (%d)', 'woocommerce-product-reviews-pro' ), $count );
			break;

			case 'photo':
				$tab_title = sprintf( __( 'Photos (%d)', 'woocommerce-product-reviews-pro' ), $count );
			break;

			case 'video':
				$tab_title = sprintf( __( 'Videos (%d)', 'woocommerce-product-reviews-pro' ), $count );
			break;

			default:
				$tab_title = sprintf( __( 'Discussion (%d)', 'woocommerce-product-reviews-pro' ), $count );
			break;

		}

		/**
		 * Filter the tab title for the contribution type
		 *
		 * @since 1.0.0
		 * @param string $tab_title The tab title
		 * @param string $type The contribution type
		 * @param int $count The number of contributions
		 */
		return apply_filters( 'wc_product_reviews_pro_contribution_type_tab_title', $tab_title, $this->type, $count );
	}


	/**
	 * Get the frontend filter title for the contribution type
	 *
	 * @return string
	 */
	public function get_filter_title() {

		$filter_title = '';

		switch ( $this->type ) {

			case 'review':
				$filter_title = __( 'Show all reviews', 'woocommerce-product-reviews-pro' );
			break;

			case 'question':
				$filter_title = __( 'Show all questions', 'woocommerce-product-reviews-pro' );
			break;

			case 'photo':
				$filter_title = __( 'Show all photos', 'woocommerce-product-reviews-pro' );
			break;

			case 'video':
				$filter_title = __( 'Show all videos', 'woocommerce-product-reviews-pro' );
			break;

		}

		/**
		 * Filter contribution type filter title
		 *
		 * @since 1.0.0
		 * @param string $filter_title The frontend filter title for the contribution type
		 * @param string $type The contribution type
		 */
		return apply_filters( 'wc_product_reviews_pro_contribution_type_filter_title', $filter_title, $this->type );
	}


	/**
	 * Get the frontend filter title for the contribution type for logged in users.
	 *
	 * @since 1.8.0
	 * @return string
	 */
	public function get_user_filter_title() {

		$filter_title = '';

		switch ( $this->type ) {

			case 'review':
				$filter_title = __( 'Show my reviews', 'woocommerce-product-reviews-pro' );
			break;

			case 'question':
				$filter_title = __( 'Show my questions', 'woocommerce-product-reviews-pro' );
			break;

			case 'photo':
				$filter_title = __( 'Show my photos', 'woocommerce-product-reviews-pro' );
			break;

			case 'video':
				$filter_title = __( 'Show my videos', 'woocommerce-product-reviews-pro' );
			break;

		}

		/**
		 * Filter contribution type filter title for logged in users
		 *
		 * @since 1.8.0
		 * @param string $filter_title The frontend filter title for the contribution type for logged in users
		 * @param string $type The contribution type
		 */
		return apply_filters( 'wc_product_reviews_pro_contribution_type_user_filter_title', $filter_title, $this->type );
	}


	/**
	 * Get the button text for the contribution type
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_button_text() {

		switch ( $this->type ) {

			case 'review':
				$button_text = __( 'Save Review', 'woocommerce-product-reviews-pro' );
			break;

			case 'question':
				$button_text = __( 'Save Question', 'woocommerce-product-reviews-pro' );
			break;

			case 'photo':
				$button_text = __( 'Save Photo', 'woocommerce-product-reviews-pro' );
			break;

			case 'video':
				$button_text = __( 'Save Video', 'woocommerce-product-reviews-pro' );
			break;

			default:
				$button_text = sprintf( __( 'Save %s', 'woocommerce-product-reviews-pro' ), $this->get_title() );
			break;

		}

		/**
		 * Filter contribution type button text
		 *
		 * @since 1.0.0
		 * @param string $button_text The button text
		 * @param string $type The contribution type
		 */
		return apply_filters( 'wc_product_reviews_pro_contribution_type_button_text', $button_text, $this->type );
	}


	/**
	 * Get the no results text for the contribution type
	 *
	 * @return string
	 */
	public function get_no_results_text() {

		$text = __( 'There are no reviews yet', 'woocommerce-product-reviews-pro' );

		$filters = wc_product_reviews_pro_get_current_comment_filters();

		switch ( $this->type ) {

			case 'review':

				if ( ! empty( $filters ) && isset( $filters['helpful'] ) && $filters['helpful'] ) {

					$text = __( 'There are no helpful reviews yet', 'woocommerce-product-reviews-pro' );

					if ( isset( $filters['classification'] ) && 'positive' == $filters['classification'] ) {
						$text = __( 'There are no helpful positive reviews yet', 'woocommerce-product-reviews-pro' );
					} elseif ( isset( $filters['classification'] ) && 'negative' == $filters['classification'] ) {
						$text = __( 'There are no helpful negative reviews yet', 'woocommerce-product-reviews-pro' );
					}

				} elseif ( ! empty( $filters ) && isset( $filters['rating'] ) && $filters['rating'] ) {
					$text = sprintf( __(' There are no reviews with a %d-star rating yet', 'woocommerce-product-reviews-pro' ), $filters['rating'] );
				} else {
					$text = __( 'There are no reviews yet', 'woocommerce-product-reviews-pro' );
				}

			break;

			case 'question':

				if ( ! empty( $filters ) && isset( $filters['unanswered'] ) && $filters['unanswered'] ) {
					$text = __( 'There are no unanswered questions', 'woocommerce-product-reviews-pro' );
				} else {
					$text = __( 'There are no questions yet', 'woocommerce-product-reviews-pro' );
				}

			break;

			case 'photo':
				$text = __( 'There are no photos yet', 'woocommerce-product-reviews-pro' );
			break;

			case 'video':
				$text = __( 'There are no videos yet', 'woocommerce-product-reviews-pro' );
			break;

		}

		/**
		 * Filter contribution type no results text
		 *
		 * @since 1.0.0
		 * @param string $text The text
		 * @param string $type The contribution type
		 */
		return apply_filters( 'wc_product_reviews_pro_contribution_type_no_results_text', $text, $this->type );
	}


	/**
	 * Get the edit text for the contribution type
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_edit_text() {

		switch ( $this->type ) {

			case 'review':
				$text = __( 'Edit Review', 'woocommerce-product-reviews-pro' );
			break;

			case 'question':
				$text = __( 'Edit Question', 'woocommerce-product-reviews-pro' );
			break;

			case 'photo':
				$text = __( 'Edit Photo', 'woocommerce-product-reviews-pro' );
			break;

			case 'video':
				$text = __( 'Edit Video', 'woocommerce-product-reviews-pro' );
			break;

			default:
				$text = sprintf( __( 'Edit %s', 'woocommerce-product-reviews-pro' ), $this->get_title() );
			break;

		}

		/**
		 * Filter contribution type edit text
		 *
		 * @since 1.0.0
		 * @param string $edit The edit text
		 * @param string $type The contribution type
		 */
		return apply_filters( 'wc_product_reviews_pro_contribution_type_edit_text', $text, $this->type );
	}


	/**
	 * Get the moderate text for the contribution type
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function get_moderate_text() {

		switch ( $this->type ) {

			case 'review':
				$text = __( 'Moderate Review', 'woocommerce-product-reviews-pro' );
			break;

			case 'question':
				$text = __( 'Moderate Question', 'woocommerce-product-reviews-pro' );
			break;

			case 'photo':
				$text = __( 'Moderate Photo', 'woocommerce-product-reviews-pro' );
			break;

			case 'video':
				$text = __( 'Moderate Video', 'woocommerce-product-reviews-pro' );
			break;

			default:
				$text = sprintf( __( 'Moderate %s', 'woocommerce-product-reviews-pro' ), $this->get_title() );
			break;

		}

		/**
		 * Filter contribution type moderate text
		 *
		 * @since 1.0.0
		 * @param string $edit The moderate text
		 * @param string $type The contribution type
		 */
		return apply_filters( 'wc_product_reviews_pro_contribution_type_moderate_text', $text, $this->type );
	}


	/**
	 * Returns form fields for the given contribution type
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_fields() {

		// Get default contribution fields
		$fields = $this->get_default_fields();

		// Add type-specific fields
		switch ( $this->type ) {

			case 'review' :

				if ( wc_review_ratings_enabled() ) {

					if ( is_user_logged_in() && wc_product_reviews_pro_get_user_review_count( get_current_user_id(), get_the_ID() ) > 0 ) {

						$rating_label = __( 'Change your recent rating', 'woocommerce-product-reviews-pro' );
						$classes	  = array( 'star-rating-selector', 'change-rating' );
					} else {

						$rating_label = __( 'How would you rate this product?', 'woocommerce-product-reviews-pro' );
						$classes	  = array( 'star-rating-selector' );
					}

					// Add rating field to beginning of fields
					$fields = array_merge( array(
						'rating' => array(
							'type'    => 'wc_product_reviews_pro_radio',
							'label'   => $rating_label,
							'class'   => $classes,
							'options' => array(
								'5' => __( 'Perfect', 'woocommerce-product-reviews-pro' ),
								'4' => __( 'Good', 'woocommerce-product-reviews-pro' ),
								'3' => __( 'Average', 'woocommerce-product-reviews-pro' ),
								'2' => __( 'Mediocre', 'woocommerce-product-reviews-pro' ),
								'1' => __( 'Poor', 'woocommerce-product-reviews-pro' ),
							),
							'required' => wc_review_ratings_required(),
						),
					), $fields );
				}

				// remove attachments if disallowed in settings
				if ( 'yes' !== get_option( 'wc_product_reviews_pro_contribution_allow_attachments' ) ) {
					unset( $fields['attachment_type'], $fields['attachment_url'], $fields['attachment_file'] );
				}

				// Review title placeholder
				$fields['title']['placeholder'] = __( 'What is the title of your review?', 'woocommerce-product-reviews-pro' );

				// Review content label
				$fields['comment']['label'] = __( 'Review', 'woocommerce-product-reviews-pro' );

			break;

			case 'question' :

				// Remove title from question fields
				unset( $fields['title'] );

				// remove attachments if disallowed in settings
				if ( 'yes' !== get_option( 'wc_product_reviews_pro_contribution_allow_attachments' ) ) {
					unset( $fields['attachment_type'], $fields['attachment_url'], $fields['attachment_file'] );
				}

				// Question content label
				$fields['comment']['label'] = __( 'Question', 'woocommerce-product-reviews-pro' );

				// Question content placeholder
				$fields['comment']['placeholder'] = __( 'What is your question?', 'woocommerce-product-reviews-pro' );

			break;

			case 'photo' :

				// Photo title placeholder
				$fields['title']['placeholder'] = __( 'What is the title of your photo?', 'woocommerce-product-reviews-pro' );

				// Photo content label
				$fields['comment']['label'] = __( 'Description', 'woocommerce-product-reviews-pro' );

				// Photo content placeholder
				$fields['comment']['placeholder'] = __( 'Your photo\'s description', 'woocommerce-product-reviews-pro' );

				// Set attachment type explicitly
				$fields['attachment_type'] = array(
					'type'    => 'wc_product_reviews_pro_hidden',
					'default' => 'photo',
					'class'   => array( 'attachment-type' ),
				);

				if ( isset( $fields['comment']['custom_attributes']['data-min-word-count'] ) ) {
					unset( $fields['comment']['custom_attributes']['data-min-word-count'] );
				}

				if ( isset( $fields['comment']['custom_attributes']['data-max-word-count'] ) ) {
					unset( $fields['comment']['custom_attributes']['data-max-word-count'] );
				}

			break;

			case 'video' :

				// Video title placeholder
				$fields['title']['placeholder'] = __( 'What is the title of your video?', 'woocommerce-product-reviews-pro' );

				// Video content label
				$fields['comment']['label'] = __( 'Description', 'woocommerce-product-reviews-pro' );

				// Video content placeholder
				$fields['comment']['placeholder'] = __( 'Your video\'s description', 'woocommerce-product-reviews-pro' );

				// Set attachment type explicitly
				$fields['attachment_type'] = array(
					'type'    => 'wc_product_reviews_pro_hidden',
					'default' => 'video',
					'class'   => array( 'attachment-type' ),
				);

				$fields['attachment_url']['required'] = true;

				if ( isset( $fields['comment']['custom_attributes']['data-min-word-count'] ) ) {
					unset( $fields['comment']['custom_attributes']['data-min-word-count'] );
				}

				if ( isset( $fields['comment']['custom_attributes']['data-max-word-count'] ) ) {
					unset( $fields['comment']['custom_attributes']['data-max-word-count'] );
				}

				unset( $fields['attachment_file'] );

			break;

			case 'contribution_comment' :

				// Comment content placeholder
				$fields['comment']['placeholder'] = __( 'What is your comment?', 'woocommerce-product-reviews-pro' );

				// Unset unnecessary fields
				unset( $fields['title'] );
				unset( $fields['attachment_type'] );
				unset( $fields['attachment_file'] );
				unset( $fields['attachment_url'] );

				if ( isset( $fields['comment']['custom_attributes']['data-min-word-count'] ) ) {
					unset( $fields['comment']['custom_attributes']['data-min-word-count'] );
				}

				if ( isset( $fields['comment']['custom_attributes']['data-max-word-count'] ) ) {
					unset( $fields['comment']['custom_attributes']['data-max-word-count'] );
				}

			break;

		}

		/**
		 * Filter contribution form fields
		 *
		 * @since 1.0.0
		 * @param array $fields Associative array of contribution form fields
		 * @param string $type The contribution type
		 */
		$fields = apply_filters( 'wc_product_reviews_pro_contribution_type_fields', $fields, $this->type );

		$contribution_fields = array();

		// Prefix field keys with contribution type to avoid duplicate IDs
		// when using woocommerce_form_field
		$prefix = $this->type . '_';

		foreach ( $fields as $key => $value ) {

			$contribution_fields[ $prefix . $key ] = $value;
		}

		return $contribution_fields;
	}


	/**
	 * Returns the default contribution fields, can be filtered
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_default_fields() {

		$fields = array(

			'title' => array(
				'type'        => 'text',
				'label'       => __( 'Title', 'woocommerce-product-reviews-pro' ),
			),

			'comment' => array(
				'type'              => 'textarea',
				'label'             => __( 'Comment', 'woocommerce-product-reviews-pro' ),
				'placeholder'       => __( 'Tell us what you think of this product...', 'woocommerce-product-reviews-pro' ),
				'required'          => true,
				'custom_attributes' => array(
					'data-min-word-count' => get_option( 'wc_product_reviews_pro_min_word_count' ),
					'data-max-word-count' => get_option( 'wc_product_reviews_pro_max_word_count' ),
				),
			),

			'attachment_type' => array(
				'type'       => 'wc_product_reviews_pro_radio',
				'label'      => __( 'Attach a photo or video', 'woocommerce-product-reviews-pro' ),
				'class'      => array( 'attachment-type' ),
				'options'    => array(
					'photo' => __( 'Photo', 'woocommerce-product-reviews-pro' ),
					'video' => __( 'Video', 'woocommerce-product-reviews-pro' ),
				),
			),

			'attachment_url' => array(
				'type'        => 'text',
				'label'       => __( 'Enter a URL', 'woocommerce-product-reviews-pro' ),
				'placeholder' => 'http://',
				'class'       => array( 'attachment-url', 'attachment-source' ),
			),

			'attachment_file' => array(
				'type'       => 'wc_product_reviews_pro_file',
				'label'      => __( 'Choose a file', 'woocommerce-product-reviews-pro' ),
				'class'      => array( 'attachment-file', 'attachment-source' ),
				'custom_attributes' => array(
					'accept' => 'image/*'
				),
			),

		);

		/**
		 * Filter the default contribution fields.
		 *
		 * @since 1.0.0
		 * @param array $fields The default contribution fields.
		 */
		return apply_filters( 'wc_product_reviews_pro_default_fields', $fields );
	}


}
