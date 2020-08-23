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
 * Frontend class
 *
 * @since 1.0.0
 */
class WC_Product_Reviews_Pro_Frontend {


	/** @var bool indicator, if we are inserting a new contribution **/
	private $_inserting_contribution = false;

	/** @var int minimum amount of contributions to display contributions on a product */
	private $contribution_threshold;

	/** @var int[] array of product IDs that should have their contributions disabled because the threshold is unmet */
	private $disabled_contributions_by_product_ids = array();

	/** @var \WC_Product_Reviews_Pro_My_Account_Contributions instance */
	protected $my_account_contributions;


	/**
	 * Add hooks
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// load frontend styles and scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'load_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );

		// load contributions comments template
		add_filter( 'comments_template', array( $this, 'comments_template_loader' ), 20 );
		// try to load WooCommerce templates from our plugin first
		add_filter( 'woocommerce_locate_template', array( $this, 'locate_template' ), 20, 3 );
		// add file type support to woocommerce_form_field
		add_filter( 'woocommerce_form_field_wc_product_reviews_pro_file',   array( $this, 'form_field' ), 10, 4 );
		add_filter( 'woocommerce_form_field_wc_product_reviews_pro_radio',  array( $this, 'form_field' ), 10, 4 );
		add_filter( 'woocommerce_form_field_wc_product_reviews_pro_hidden', array( $this, 'form_field' ), 10, 4 );

		// process posted comment data
		add_action( 'pre_comment_on_post', array( $this, 'process_posted_comment_data' ), 0 );
		// filter comment_moderation option for contributions
		add_filter( 'pre_option_comment_moderation', array( $this, 'contribution_moderation' ) );
		// set comment type based on contribution type
		add_filter( 'preprocess_comment', array( $this, 'preprocess_comment_data' ), -1 );
		// save contribution data
		add_action( 'comment_post', array( $this, 'add_contribution_data' ), 1 );

		// add contribution types as avatar comment types
		add_filter( 'get_avatar_comment_types', array( $this, 'add_contribution_avatar_types' ) );

		// support flagging without AJAX
		add_action( 'woocommerce_init', array( $this, 'flag_contribution' ) );
		// support voting without AJAX
		add_action( 'woocommerce_init', array( $this, 'vote_for_contribution' ) );

		// Filter & order contributions on frontend.
		add_filter( 'comments_array', array( $this, 'order_comments' ), 10 );
		add_filter( 'comments_array', array( $this, 'filter_comments' ), 10 );
		// WP 4.5+ only: filter query args to fix borked reviews sort order according to some WordPress comment settings.
		add_filter( 'comments_template_query_args', array( $this, 'filter_comments_template_query_args' ), 10 );

		// Customize the review tab.
		add_filter( 'woocommerce_product_tabs', array( $this, 'customize_review_tab' ) );

		// Handle JSON LD structured data for contributions on product pages.
		add_filter( 'woocommerce_structured_data_type_for_page', array( $this, 'add_structured_data_types' ) );
		add_filter( 'woocommerce_structured_data_product_offer', array( $this, 'improve_offer_structured_data' ), 10, 2 );
		add_filter( 'woocommerce_structured_data_review',        array( $this, 'add_structured_data' ), 40, 2 );
		add_filter( 'woocommerce_structured_data_product',       array( $this, 'additional_product_structured_data' ) );
		// Display the admin author badge.
		add_filter( 'comment_author', array( $this, 'add_author_badge' ), 20, 2 );
		// Customize the front-end product review count.
		add_filter( 'woocommerce_product_get_review_count', array( $this, 'customize_review_count' ), 10, 2 );

		// Handle login and redirect.
		add_filter( 'login_message',          array( $this, 'login_message' ) );
		add_action( 'woocommerce_login_form', array( $this, 'add_redirect_to_field' ) );

		// Handle posted comment/contribution data from session.
		add_action( 'woocommerce_init', array( $this, 'handle_postdata_from_session' ) );

		// list contributions in My Account page as a tab
		add_action( 'init', array( $this, 'load_my_account_contributions' ) );

		// disables contributions from display if an optional threshold is not met
		add_action( 'pre_get_comments', array( $this, 'handle_contributions_threshold' ), -1 );
		add_action( 'the_post',         array( $this, 'handle_product_contributions_count' ), -1 );
		// removes the ratings for products whose contributions are below the threshold
		add_filter( 'wc_product_reviews_pro_product_rating_average', array( $this, 'handle_product_ratings' ), -1, 2 );
		add_filter( 'wc_product_reviews_pro_product_rating_count',   array( $this, 'handle_product_ratings' ), -1, 2 );
		add_filter( 'woocommerce_product_get_rating_html',           array( $this, 'adjust_product_rating_html_below_threshold' ), -1, 2 );

		// Filter comments count
		add_filter( 'wp_count_comments', array( $this, 'queue_count_comments_modifier' ), 11, 2 );
	}


	/**
	 * Returns the contribution threshold.
	 *
	 * @since 1.10.0
	 *
	 * @return int default 1 (no threshold)
	 */
	private function get_contribution_threshold() {

		if ( null === $this->contribution_threshold ) {
			$this->contribution_threshold = (int) get_option( 'wc_product_reviews_pro_contribution_threshold', 1 );
		}

		return max( 1, $this->contribution_threshold );
	}


	/**
	 * Returns an array of product IDs and their contributions where these are less than the set threshold.
	 *
	 * @since 1.10.0
	 *
	 * @return array associative array of contribution IDs indexed by product IDs
	 */
	private function get_products_disabled_contributions() {
		global $wpdb;

		if ( empty( $this->disabled_contributions_by_product_ids ) ) {

			$threshold = $this->get_contribution_threshold();

			if ( $threshold > 1 ) {

				$posts_table    = $wpdb->prefix . 'posts';
				$comments_table = $wpdb->prefix . 'comments';
				$results        = $wpdb->get_results( "
					SELECT comment_ID AS contribution_id, comment_post_ID AS product_id, comment_approved as comment_moderation
					FROM {$comments_table}
					WHERE comment_post_ID IN (
						SELECT ID
						FROM {$posts_table}
						WHERE post_type = 'product'
						AND post_status = 'publish'
					)
					AND comment_parent = 0
				" );

				if ( ! empty( $results ) ) {

					$comment_ids_by_product_ids = array();

					foreach ( $results as $result ) {
						$comment_ids_by_product_ids[ (int) $result->product_id ][ (int) $result->contribution_id ] = $result->comment_moderation;
					}


					foreach ( $comment_ids_by_product_ids as $product_id => $contributions ) {

						$approved = 0;

						foreach ( (array) $contributions as $contribution_id => $moderation_status ) {

							if ( is_numeric( $moderation_status ) && 1 === (int) $moderation_status ) {
								$approved++;
							}
						}

						if ( $approved < $threshold ) {
							$this->disabled_contributions_by_product_ids[ $product_id ] = array_keys( $contributions );
						}
					}
				}
			}
		}

		return $this->disabled_contributions_by_product_ids;
	}


	/**
	 * Returns disabled contribution IDs.
	 *
	 * @since 1.10.0
	 *
	 * @return int[] array of contribution IDs
	 */
	private function get_disabled_contributions() {

		$disabled = array_values( $this->get_products_disabled_contributions() );

		return ! empty( $disabled ) && is_array( $disabled ) ? array_unique( call_user_func_array( 'array_merge', $disabled ) ) : array();
	}


	/**
	 * Checks if a product has too few contributions to meet threshold.
	 *
	 * @since 1.10.0
	 *
	 * @param int|\WP_Post|\WC_Product $product a product object, post or ID
	 * @return bool
	 */
	public function is_product_contributions_below_threshold( $product ) {

		$below_threshold = $product_id = false;
		$excluded        = $this->get_products_disabled_contributions();

		if ( ! empty( $excluded ) ) {

			if ( $product instanceof \WP_Post ) {
				$product_id = $product->ID;
			} elseif ( $product instanceof \WC_Product ) {
				$product_id = $product->get_id();
			} elseif ( is_numeric( $product ) ) {
				$product_id = (int) $product;
			}

			$below_threshold = $product_id ? array_key_exists( $product_id, $excluded ) : false;
		}

		return $below_threshold;
	}


	/**
	 * Filters the comment query to exclude products whose contributions don't meet the set threshold.
	 *
	 * By default (threshold of 1) this is ignored and any amount of contributions is displayed.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param \WP_Comment_Query $wp_comment_query the comment query instance passed by reference
	 */
	public function handle_contributions_threshold( $wp_comment_query ) {

		// if the threshold is above 1, we need to toggle contribution display for certain products
		if ( isset( $wp_comment_query->query_vars ) && $this->get_contribution_threshold() > 1 ) {

			$excluded_contribution_ids = $this->get_disabled_contributions();

			if ( ! empty( $excluded_contribution_ids ) ) {

				$existing_exclusions       = ! empty( $wp_comment_query->query_vars['comment__not_in'] ) ? (array) $wp_comment_query->query_vars['comment__not_in'] : array();
				$excluded_contribution_ids = array_unique( array_merge( $excluded_contribution_ids, $existing_exclusions ) );

				$wp_comment_query->query_vars['comment__not_in'] = $excluded_contribution_ids;
			}
		}
	}


	/**
	 * Sets the comment count of a product to 0 if the number of contributions are below threshold.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param \WP_Post $post the post object, passed by reference
	 */
	public function handle_product_contributions_count( $post) {

		if (    'product' === get_post_type( $post )
		     && $this->is_product_contributions_below_threshold( $post ) ) {

			$post->comment_count = 0;
		}
	}


	/**
	 * Returns 0 if the product contributions do not meet the threshold.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param int|float $rating a rating count or average
	 * @param \WC_Product $product a product object
	 * @return int|float
	 */
	public function handle_product_ratings( $rating, $product ) {

		if ( $this->is_product_contributions_below_threshold( $product ) ) {
			$rating = 0;
		}

		return $rating;
	}


	/**
	 * Adjusts the product rating HTML for products with contributions below the optional threshold.
	 *
	 * @see \wc_get_rating_html()
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param string $rating_html star rating HTML
	 * @param int|float $rating rating value
	 * @return string empty string or HTML
	 */
	public function adjust_product_rating_html_below_threshold( $rating_html, $rating ) {
		global $post, $product;

		// bother only if above 0
		if ( $rating > 0 ) {

			$the_product = null;

			if ( $product instanceof \WC_Product ) {
				$the_product = $product;
			} elseif ( $post instanceof \WP_Post ) {
				$the_product = wc_get_product( $post );
			}

			if ( $the_product instanceof \WC_Product && $this->is_product_contributions_below_threshold( $product ) ) {

				$rating_html = '';
			}
		}

		return $rating_html;
	}


	/**
	 * Load My Contributions in My Account page
	 *
	 * @since 1.6.0
	 */
	public function load_my_account_contributions() {

		$this->my_account_contributions = wc_product_reviews_pro()->load_class( '/includes/frontend/class-wc-product-reviews-pro-my-account-contributions.php', 'WC_Product_Reviews_Pro_My_Account_Contributions' );
	}


	/**
	 * Get My Account Contributions instance
	 *
	 * @since 1.6.0
	 * @return \WC_Product_Reviews_Pro_My_Account_Contributions
	 */
	public function get_my_account_contributions_instance() {
		return $this->my_account_contributions;
	}


	/**
	 * Loads the product contributions comments template
	 *
	 * @since 1.0.0
	 * @param mixed $template
	 * @return string
	 */
	public function comments_template_loader( $template ) {

		// Bail if not viewing a product
		if ( get_post_type() !== 'product' ) {
			return $template;
		}

		// The WooCommerce template path within the theme
		$template_path = WC()->template_path();

		// Our custom comments template name
		$template_name = 'single-product/contributions.php';

		// Look within the theme first
		$template = locate_template(
			array(
				trailingslashit( $template_path ) . $template_name,
				$template_name,
			)
		);

		// If nothing was found in the theme, look in the plugin
 		if ( ! $template ) {

 			// Set the path to our templates directory
 			$plugin_path = wc_product_reviews_pro()->get_plugin_path() . '/templates/';

 			// If a template is found, make it so
 			if ( is_readable( $plugin_path . $template_name ) ) {
 				$template = $plugin_path . $template_name;
 			}
 		}

		return $template;
	}


	/**
	 * Locates the WooCommerce template files from our templates directory
	 *
	 * @since 1.0.0
	 * @param string $template Already found template
	 * @param string $template_name Searchable template name
	 * @param string $template_path Template path
	 * @return string Search result for the template
	 */
	public function locate_template( $template, $template_name, $template_path ) {

		// only keep looking if no custom theme template was found
		// or if a default WooCommerce template was found
 		if ( ! $template || Framework\SV_WC_Helper::str_starts_with( $template, WC()->plugin_path() ) ) {

 			// set the path to our templates directory
 			$plugin_path = wc_product_reviews_pro()->get_plugin_path() . '/templates/';

 			// if a template is found, make it so
 			if ( is_readable( $plugin_path . $template_name ) ) {
 				$template = $plugin_path . $template_name;
 			}
 		}

		return $template;
	}


	/**
	 * Checks if a product is being rendered, either on a product page or via [product_page] shortcode
	 *
	 * @since 1.5.0
	 * @param \WP_Post $post the post object
	 * @return bool true if the post displays a product page
	 */
	protected function is_product_rendered( $post ) {
		return is_product() || ( is_object( $post ) && isset( $post->post_content ) && has_shortcode( $post->post_content, 'product_page' ) );
	}


	/**
	 * Loads frontend styles for widgets, account section, and on product pages
	 *
	 * @since 1.5.0
	 */
	public function load_styles() {
		global $post;

		// check if any 'recent contributions' widget is active
		$widget_active = false;
		$widget_types  = array( 'photo', 'video', 'review', 'question' );

		foreach ( $widget_types as $widget_name ) {

			$widget_id = 'wc_product_reviews_pro_recent_' . $widget_name . 's';

			// if any widget is active, load the CSS;
			// break as we only need one "true"
			if ( is_active_widget( false, false, $widget_id ) ) {
				$widget_active = true;
				break;
			}
		}

		// load CSS if the widget is active, account is viewed, or a product page is rendered
		if ( $widget_active || $this->is_product_rendered( $post ) || is_account_page() ) {
			// frontend CSS
			wp_enqueue_style( 'wc-product-reviews-pro-frontend', wc_product_reviews_pro()->get_plugin_url() . '/assets/css/frontend/wc-product-reviews-pro-frontend.min.css', array( 'dashicons' ), \WC_Product_Reviews_Pro::VERSION );
		}
	}


	/**
	 * Loads frontend styles and scripts on product page
	 *
	 * @since 1.5.0
	 */
	public function load_scripts() {
		global $post;

		// Bail out if we aren't on a product page or the product page shortcode isn't being used
		if ( ! $this->is_product_rendered( $post ) ) {
			return;
		}

		// jQuery tipTip from WC
		if ( ! wp_script_is( 'jquery-tiptip', 'registered' ) ) {
			wp_register_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.min.js', array( 'jquery' ), WC()->version, true );
		}

		// frontend scripts
		wp_enqueue_script( 'wc-product-reviews-pro-frontend', wc_product_reviews_pro()->get_plugin_url() . '/assets/js/frontend/wc-product-reviews-pro-frontend.min.js', array( 'jquery', 'jquery-tiptip' ), \WC_Product_Reviews_Pro::VERSION );

		$max_upload_size = wp_max_upload_size();

		wp_localize_script( 'wc-product-reviews-pro-frontend', 'wc_product_reviews_pro', array(
			'is_user_logged_in'    => is_user_logged_in(),
			'user_id'              => get_current_user_id(),
			'comment_registration' => 1 == get_option( 'comment_registration' ), // in js 0 is not falsy and not empty
			'product_id'    => $post->ID,
			'ajax_url'      => is_ssl() ? admin_url( 'admin-ajax.php', 'https' ) : admin_url( 'admin-ajax.php', 'http' ),
			'nonce'         => wp_create_nonce( 'wc-product-reviews-pro' ),
			'comment_type'  => isset( $_POST['comment_type'] ) ? $_POST['comment_type'] : null,
			'file_size_max'	=> $max_upload_size,
			'i18n' => array(
				'loading'           => __( 'Loading...', 'woocommerce-product-reviews-pro' ),
				'attach_a_photo'    => __( 'Attach a photo', 'woocommerce-product-reviews-pro' ),
				'attach_a_video'    => __( 'Attach a video', 'woocommerce-product-reviews-pro' ),
				'attach_photo_url'  => __( 'Rather attach photo from another website?', 'woocommerce-product-reviews-pro' ),
				'attach_photo_file' => __( 'Rather attach photo from your computer?', 'woocommerce-product-reviews-pro' ),
				'attach_video_url'  => __( 'Rather attach video from another website?', 'woocommerce-product-reviews-pro' ),
				'attach_video_file' => __( 'Rather attach video from your computer?', 'woocommerce-product-reviews-pro' ),
				'flag_failed'       => __( 'Could not flag contribution. Please try again later.', 'woocommerce-product-reviews-pro' ),
				'subscribe_failed'  => __( 'An error occurred. Your request could not be processed.', 'woocommerce-product-reviews-pro' ),
				'vote_failed'       => __( 'Could not cast your vote. Please try again later.', 'woocommerce-product-reviews-pro' ),
				'comment_karma'     => __( '%1$d out of %2$d people found this helpful', 'woocommerce-product-reviews-pro' ),
				// Errors:
				'error_attach_file'      => __( 'Please attach a file.', 'woocommerce-product-reviews-pro' ),
				'error_required'         => __( 'This is a required field.', 'woocommerce-product-reviews-pro' ),
				'error_too_short'        => __( 'Please enter at least %d words.', 'woocommerce-product-reviews-pro' ),
				'error_too_long'         => __( 'Please enter less than %d words.', 'woocommerce-product-reviews-pro' ),
				'error_file_not_allowed' => __( 'Only jpg, png, gif, bmp and tiff files, please', 'woocommerce-product-reviews-pro' ),
				/* translators: Placeholders: %s Size of file in human readable format (e.g. 2M, 200Kb, etc.) */
				'error_file_size_max'    => sprintf( __( 'File is too large. Size must be less than %s.', 'woocommerce-product-reviews-pro' ), size_format( $max_upload_size ) ),
				'error_login_signup'     => __( 'An error occurred, please try again.', 'woocommerce-product-reviews-pro' ),
				'remove_attachment'      => __( 'Remove Attachment', 'woocommerce-product-reviews-pro' ),
				// confirmation when Logged-out/Guest user tries to leave a review with an email tied to a registered user
				'review_update_confirmation' => __( "Hold up -- it looks like you've already left a review for this product. You can update your existing review instead.", 'woocommerce-product-reviews-pro' ) . "\n\n" . __( 'Please click "OK" to send a confirmation email to update your existing review, or "Cancel" to go back.', 'woocommerce-product-reviews-pro' ),
			),
		) );
	}


	/**
	 * Add support for extra field types to woocommerce_form_field
	 *
	 * Adds support for radio, file
	 *
	 * @since 1.0.0
	 * @param array $field
	 * @param string $key
	 * @param array $args
	 * @param mixed $value
	 * @return string $field HTML
	 */
	public function form_field( $field, $key, $args, $value ) {

		if ( ! empty( $args['clear'] ) ) {
			$after = '<div class="clear"></div>';
		} else {
			$after = '';
		}

		if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$required = ' <abbr class="required" title="' . esc_attr__( 'required', 'woocommerce-product-reviews-pro'  ) . '">*</abbr>';
		} else {
			$required = '';
		}

		// Custom attribute handling
		$custom_attributes = array();

		if ( ! empty( $args['custom_attributes'] ) && is_array( $args['custom_attributes'] ) ) {
			foreach ( $args['custom_attributes'] as $attribute => $attribute_value ) {
				$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
			}
		}

		switch ( $args['type'] ) {

			case 'wc_product_reviews_pro_radio' :

				if ( ! empty( $args['options'] ) ) {
					$field .= '<div class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $key ) . '_field">';

					if ( $args['label'] ) {
						$field .= '<label for="' . esc_attr( $key ) . '_' . esc_attr( current( array_keys( $args['options'] ) ) ) . '" class="' . implode( ' ', $args['label_class'] ) .'">' . $args['label']. $required  . '</label>';
					}

					$field .= '<fieldset>';

					foreach ( $args['options'] as $option_key => $option_text ) {

						$field .= '<input type="radio" class="input-checkbox" value="' . esc_attr( $option_key ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '_' . esc_attr( $option_key ) . '"' . checked( $value, $option_key, false ) . ' />';

						$field .= '<label for="' . esc_attr( $key ) . '_' . esc_attr( $option_key ) . '" class="checkbox ' . implode( ' ', $args['label_class'] ) .'">' . $option_text . '</label> ';

					}

					$field .= '</fieldset>';
					$field .= '</div>' . $after;
				}

			break;

			case 'wc_product_reviews_pro_hidden' :

				$field .= '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $key ) . '_field" style="display:none;">';

					$field .= '<input type="hidden" class="input-hidden ' . implode( ' ', $args['input_class'] ) .'" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" ' . implode( ' ', $custom_attributes ) . ' />';

				$field .= '</p>' . $after;

			break;

			case 'wc_product_reviews_pro_file' :

				$field = '<p class="form-row ' . esc_attr( implode( ' ', $args['class'] ) ) .'" id="' . esc_attr( $key ) . '_field">';

				if ( $args['label'] ) {
					$field .= '<label for="' . esc_attr( $key ) . '" class="' . implode( ' ', $args['label_class'] ) . '">' . $args['label'] . $required . '</label>';
				}

				$field .= '<input type="file" class="input-file ' . implode( ' ', $args['input_class'] ) .'" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" ' . implode( ' ', $custom_attributes ) . ' />';

				$field .= '</p>' . $after;

				break;

			break;
		}

		return $field;
	}


	/**
	 * Maybe force enable My Account registration on the product page so
	 * the registration form is rendered properly in the Ajax modal window
	 *
	 * TODO: remove this method by version 2.0.0 or by 2021-01-09 {WV 2020-01-09}
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 * @deprecated 1.15.2
	 *
	 * @param $enabled
	 * @return string
	 */
	public function maybe_force_enable_myaccount_registration( $enabled ) {

		wc_deprecated_function( __METHOD__, '1.15.2' );

		return $enabled;
	}


	/**
	 * Remove contribution type prefix from posted keys
	 *
	 * @since 1.0.0
	 */
	public function process_posted_comment_data() {

		$type = isset( $_POST['comment_type'] ) ? $_POST['comment_type'] : null;

		// Bail out if not contribution type is set. This probably means
		// that this wasn't a contribution form anyway.
		if ( ! $type ) {
			return;
		}

		// Loop over POST data and remove type prefix
		foreach ( $_POST as $key => $value ) {

			// Check if the key is prefixed with type
			if ( strpos( $key, $type . '_' ) === 0 ) {

				// Add posted value under cleaned (unprefixed) key
				$clean_key = substr( $key, strlen( $type ) + 1 );
				$_POST[ $clean_key ] = $value;

			}
		}

		// Process fields
		$contribution_type = wc_product_reviews_pro_get_contribution_type( $type );
		foreach ( $contribution_type->get_fields() as $key => $field ) {

			// Get Value
			switch ( $field['type'] ) {
				case 'checkbox' :
					$_POST[ $key ] = isset( $_POST[ $key ] ) ? 1 : 0;
				break;
				default :
					$_POST[ $key ] = isset( $_POST[ $key ] ) ? wc_clean( $_POST[ $key ] ) : '';
				break;
			}

			/**
			 * Filter the POST value for $key.
			 *
			 * @since 1.0.0
			 * @param mixed $value The POST value for $key.
			 */
			$_POST[ $key ] = apply_filters( 'wc_product_reviews_pro_process_contribution_form_field_' . $key, $_POST[ $key ] );

			// Validation: Required fields
			if ( ! empty( $field['required'] ) && empty( $_POST[ $key ] ) ) {
				wc_add_notice( sprintf( __( '%s is a required field.', 'woocommerce-product-reviews-pro' ), $field['label'] ), 'error' );
			}

			// Validation rules
			if ( ! empty( $field['validate'] ) && is_array( $field['validate'] ) ) {
				foreach ( $field['validate'] as $rule ) {
					switch ( $rule ) {
						case 'email' :
							$_POST[ $key ] = strtolower( $_POST[ $key ] );

							if ( ! is_email( $_POST[ $key ] ) ) {
								wc_add_notice( '<strong>' . $field['label'] . '</strong> ' . __( 'is not a valid email address.', 'woocommerce-product-reviews-pro' ), 'error' );
							}
						break;
					}
				}
			}
		}

		// check if rating is required
		if ( 'review' === $type && isset( $_POST[ $type . '_rating'] ) && empty( $_POST[ $type . '_rating'] ) && wc_review_ratings_required() ) {
			wc_add_notice( __( 'Please rate the product.', 'woocommerce-product-reviews-pro' ), 'error' );
		}

		// save/handle attachments (photos, videos)
		$attachment_type = isset( $_POST[ 'attachment_type' ] ) ? $_POST[ 'attachment_type' ] : null;

		// skip this if attachments are disabled for this contribution type
		if ( ('review' === $type || 'question' === $type) && ('yes' !== get_option( 'wc_product_reviews_pro_contribution_allow_attachments' ) ) ) {
			$attachment_type = null;
		}

		if ( $attachment_type ) {

			$key = $type . '_attachment_file';

			if ( isset( $_FILES[ $key ] ) && $_FILES[ $key ][ 'size' ] > 0 ) {

				// Only photo uploads are supported at the moment
				if ( 'photo' === $attachment_type ) {

					// These files need to be included as dependencies when on the front end.
					require_once( ABSPATH . 'wp-admin/includes/image.php' );
					require_once( ABSPATH . 'wp-admin/includes/file.php' );
					require_once( ABSPATH . 'wp-admin/includes/media.php' );

					$attachment_id = media_handle_upload( $key, 0, array(), array(
						'test_form' => false,
						'mimes' => array(
							'jpg|jpeg|jpe' => 'image/jpeg',
							'gif'          => 'image/gif',
							'png'          => 'image/png',
							'bmp'          => 'image/bmp',
							'tif|tiff'     => 'image/tiff',
						),
					) );

					// Bail out if file upload did not succeed
					if ( is_wp_error( $attachment_id ) ) {

						/* translators: Placeholders: %s - error description */
						wc_add_notice( sprintf( __( 'Unable to upload file: %s', 'woocommerce-product-reviews-pro' ), $attachment_id->get_error_message() ), 'error' );

					} else {

						// Keep a reference to attachment_id and type
						$this->_uploaded_attachment_id   = $attachment_id;
						$this->_uploaded_attachment_type = $attachment_type;
					}

				} else {

					wc_add_notice( __( 'Only photo uploads are supported at the moment', 'woocommerce-product-reviews-pro' ), 'error' );
				}

			}

			// Make sure that at least one of file or url is submitted
			if ( 'photo' === $type
			     && ! ( isset( $_FILES[ $key ] ) && $_FILES[ $key ][ 'size' ] > 0 )
			     && ! ( isset( $_POST[ $type . '_attachment_url' ] ) && $_POST[ $type . '_attachment_url'] ) ) {

				wc_add_notice( __( 'Please attach a photo.', 'woocommerce-product-reviews-pro' ), 'error' );
			}
		}


		// Redirect back to product page if there are errors
		if ( wc_notice_count( 'error' ) > 0 ) {

			WC()->session->wc_product_reviews_pro_posted_data = $_POST;

			// Provide a hash so that page scrolls to form on load
			$hash = 'contribution_comment' === $type ? '#comment-' . $_POST['comment_parent'] : '#reviews';

			wp_safe_redirect( wp_get_referer() . $hash );
			exit;
		} else {

			// Force setting cookies, otherwise notice is not stored to session
			if ( ! is_user_logged_in() && WC()->cart->is_empty() ) {
				WC()->session->set_customer_session_cookie( true );
			}

			if ( 'yes' === get_option( 'wc_product_reviews_pro_contribution_moderation' ) ) {
				wc_add_notice( __( 'Thank you! Your contribution has been received and is being reviewed.', 'woocommerce-product-reviews-pro' ), 'success' );
			} else {
				wc_add_notice( __( 'Thank you! Your contribution has been received.', 'woocommerce-product-reviews-pro' ), 'success' );
			}
		}
	}


	/**
	 * Preprocess comment data
	 *
	 * @since 1.0.0
	 * @param array $commentdata
	 * @return array $commentdata
	 */
	public function preprocess_comment_data( $commentdata ) {

		// Set comment_type in commentdata so that the comment is saved with
		// the correct comment type. WP itself does not read it from $_POST,
		// so we need to set it manually.
		if ( isset( $_POST['comment_type'] ) ) {
			$commentdata['comment_type'] = $_POST['comment_type'];
		}

		// Indicate that we are in the process of inserting a new contribution.
		// This flag will be used by the pre_option_comment_moderation filter later
		if ( $commentdata['comment_type'] ) {
			$this->_inserting_contribution = true;
		}

		// If there is an attachment_url present, set it as the
		// comment_author_url so that Akismet can check it
		if ( isset( $_POST['attachment_url'] ) && $_POST['attachment_url'] ) {
			$commentdata['comment_author_url'] = $_POST['attachment_url'];
		}

		// This is necessary as of WordPress 4.4
		// because process_posted_comment_data() method is too late
		// and $commentdata is already set in wp_handle_comment_submission( $_POST )
		if ( isset( $_POST['comment'] ) ) {
			$commentdata['comment_content'] = trim( $_POST['comment'] );
		}

		return $commentdata;
	}


	/**
	 * Saves contribution data.
	 *
	 * @since 1.0.0
	 *
	 * @param int $comment_id
	 */
	public function add_contribution_data( $comment_id ) {

		$comment_type      = isset( $_POST['comment_type'] )    ? $_POST['comment_type']    : null;
		$attachment_type   = isset( $_POST['attachment_type'] ) ? $_POST['attachment_type'] : null;

		if ( $comment_type && ( 'review' === $comment_type || 'question' === $comment_type ) ) {
			$allow_attachments = 'yes' === get_option( 'wc_product_reviews_pro_contribution_allow_attachments' );
		} else {
			$allow_attachments = true;
		}

		// save title
		if ( isset( $_POST['title'] ) && $_POST['title'] ) {
			add_comment_meta( $comment_id, 'title', $_POST['title'], true );
		}

		// save/handle attachments (photos, videos)
		if ( $allow_attachments && $attachment_type ) {

			if ( isset ( $_POST['attachment_url'] ) && $_POST['attachment_url'] ) {

				add_comment_meta( $comment_id, 'attachment_type', $attachment_type );
				add_comment_meta( $comment_id, 'attachment_url', esc_url_raw( $_POST[ 'attachment_url' ], array( 'http', 'https' ) ) );

			} elseif ( isset ( $this->_uploaded_attachment_type ) && $attachment_type === $this->_uploaded_attachment_type ) {

				add_comment_meta( $comment_id, 'attachment_type', $this->_uploaded_attachment_type );
				add_comment_meta( $comment_id, 'attachment_id',   $this->_uploaded_attachment_id );
			}
		}

		// subscribe user to contribution comment replies
		if ( ! empty( $_POST['subscribe_to_replies'] ) ) {

			$user_id = $_POST['comment_author_ID'];
			$comment = wc_product_reviews_pro_get_contribution( $comment_id );

			wc_product_reviews_pro_add_comment_notification_subscriber( 'subscribe', $user_id, $comment );
		}

		// maybe clear transients set on the product
		if ( $comment_type ) {

			$comment = get_comment( $comment_id );

			if ( $comment && ! empty( $comment->comment_post_ID ) ) {

				\WC_Product_Reviews_Pro_Products::clear_transients( absint( $comment->comment_post_ID ) );
			}
		}
	}


	/**
	 * Filter comment_moderation option for contributions
	 *
	 * @since 1.0.0
	 * @param int|string $moderation
	 * @return int|string Return 1 (truthy) if manual contribution moderation is on,
	 *                     empty (falsy) otherwise
	 */
	public function contribution_moderation( $moderation ) {

		if ( $this->_inserting_contribution ) {
			$moderation = ( 'yes' === get_option('wc_product_reviews_pro_contribution_moderation') ) ? 1 : '';
		}

		return $moderation;
	}


	/**
	 * Add contribution types as allowed comment types for avatars
	 *
	 * @since 1.0.0
	 * @param array $allowed_types
	 * @return array
	 */
	public function add_contribution_avatar_types( $allowed_types ) {

		$contribution_types = array_keys( wc_product_reviews_pro_get_contribution_types() );

		return array_unique( array_merge( $allowed_types, $contribution_types ) );
	}


	/**
	 * Flag a contribution (non-AJAX)
	 *
	 * @since 1.0.0
	 */
	public function flag_contribution() {

		// Ensure we are actually flagging a contribution
		if ( 'POST' !== $_SERVER['REQUEST_METHOD'] || ! isset( $_POST['action'] ) || 'flag_contribution' !== $_POST['action'] ) {
			return;
		}

		// Bail out if no comment ID was provided
		if ( ! isset( $_POST['comment_id'] ) || ! $_POST['comment_id'] ) {
			return;
		}

		$contribution = wc_product_reviews_pro_get_contribution( $_POST['comment_id'] );
		$reason = isset( $_POST['flag_reason'] ) ? $_POST['flag_reason'] : null;

		if ( ! $contribution ) {
			return;
		}

		// Flag contribution
		if ( $contribution->flag( $reason ) ) {

			wc_add_notice( __( 'Contribution was flagged. Thanks!', 'woocommerce-product-reviews-pro' ) );

		} else {

			$message = $contribution->get_failure_message();
			wc_add_notice( $message ? $message : __( 'Could not flag contribution. Please try again later.', 'woocommerce-product-reviews-pro' ), 'error' );
		}

		wp_safe_redirect( wp_get_referer() );
		exit;
	}


	/**
	 * Vote for a contribution (non-AJAX)
	 *
	 * @since 1.0.0
	 */
	public function vote_for_contribution() {

		// Ensure we are actually voting for a contribution
		if ( 'GET' !== $_SERVER['REQUEST_METHOD'] || ! isset( $_GET['action'] ) || 'vote_for_contribution' !== $_GET['action'] ) {
			return;
		}

		// Bail out if no comment ID was provided
		if ( ! isset( $_GET['comment_id'] ) || ! $_GET['comment_id'] ) {
			return;
		}

		$contribution = wc_product_reviews_pro_get_contribution( $_GET['comment_id'] );

		if ( ! $contribution ) {
			return;
		}

		$type = isset( $_GET['type'] ) ? $_GET['type'] : null;

		// Check if user is logged in
		if ( ! is_user_logged_in() ) {
			$redirect_to = add_query_arg( 'redirect_to', urlencode( $_SERVER['REQUEST_URI'] ), wc_get_page_permalink( 'myaccount' ) );
			wp_redirect( $redirect_to );
			exit;
		}

		// Cas the vote for contribution
		if ( $contribution->cast_vote( $type ) ) {

			wc_add_notice( __( 'Vote has been cast. Thanks!', 'woocommerce-product-reviews-pro' ) );

		} else {

			$message = $contribution->get_failure_message();
			wc_add_notice( $message ? $message : __( 'Could not cast your vote. Please try again later.', 'woocommerce-product-reviews-pro' ), 'error' );
		}

		wp_safe_redirect( wp_get_referer() );
		exit;
	}


	/**
	 * Filter comments (contributions) on frontend.
	 *
	 * Filters contributions by specified type and/or rating in query args.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 * @param \WP_Comment[] $comments Array of comments.
	 * @return \WP_Comment[]
	 */
	public function filter_comments( $comments ) {
		global $post;

		if ( 'product' === $post->post_type ) {

			$filters = wc_product_reviews_pro_get_current_comment_filters();

			if ( $filters && ! empty( $filters ) ) {
				foreach ( $filters as $filter => $value ) {

					switch ( $filter ) {


						# Filter by comment type
						case 'comment_type':
							$_comments = array();

							foreach ( $comments as $comment ) {

								switch ( $comment->comment_type ) {

									case $value:
										$_comments[] = $comment;
									break;

									case 'contribution_comment':
										foreach ( $comments as $parent ) {
											if ( $parent->comment_ID === $comment->comment_parent && $value == $parent->comment_type ) {
												$_comments[] = $comment;
											}
										}
									break;
								}

							}

							$comments = $_comments;

						break;


						// filter for logged in user
						case 'user_type':

							// checking if user is logged in and filter's value is `me`
							if ( is_user_logged_in() && ( 'me' === $value ) ) {

								$_comments		 = array();
								$current_user_id = get_current_user_id();

								foreach ( $comments as $comment ) {

									// checking if comment author ID and current user ID are same
									if ( (int) $comment->user_id === (int) $current_user_id ) {
										$_comments[] = $comment;
									}
								}

								$comments = $_comments;
							}

						break;


						// filter by review rating
						case 'rating':

							$_comments = array();

							foreach ( $comments as $comment ) {

								switch ( $comment->comment_type ) {

									// Include reviews with matching rating
									case 'review':
										$rating = get_comment_meta( $comment->comment_ID, 'rating', true );

										if ( $rating === $value ) {
											$_comments[] = $comment;
										}

									break;

									// Include comments that have a parent with matching rating
									case 'contribution_comment':

										foreach ( $_comments as $parent ) {
											if ( $parent->comment_ID === $comment->comment_parent ) {
												$_comments[] = $comment;
											}
										}

									break;
								}
							}

							$comments = $_comments;

						break;


						# Filter by review qualifier
						case 'review_qualifier':

							$_comments = array();

							$parts = explode( ':', $value );

							// Make sure we actually have a qualifier value
							if ( ! isset( $parts[1] ) ) {
								break;
							}

							$filter_qualifier_value  = $parts[1];

							foreach ( $comments as $comment ) {

								$qualifier_value = get_comment_meta( $comment->comment_ID, 'wc_product_reviews_pro_review_qualifier_' . $parts[0], true );

								if ( $qualifier_value === $filter_qualifier_value ) {
									$_comments[] = $comment;
								}
							}

							$comments = $_comments;

						break;


						# Filter by unanswered
						case 'unanswered':
							global $wpdb;

							$_comments = array();

							foreach ( $comments as $comment ) {

								if ( ! $comment->comment_parent ) {

									$answers_count = $wpdb->get_var( $wpdb->prepare( "
										SELECT COUNT(comment_ID) FROM $wpdb->comments
										WHERE comment_parent = %d
									", $comment->comment_ID ) );

									if ( ! $answers_count ) {
										$_comments[] = $comment;
									}
								}
							}

							$comments = $_comments;

						break;


						# Filter by classification (positive/negative)
						case 'classification':
							global $wpdb;

							$_comments = array();

							foreach ( $comments as $comment ) {

								$rating = get_comment_meta( $comment->comment_ID, 'rating', true );

								if ( $value === 'positive' && $rating >= 3 ) {
									$_comments[] = $comment;
								}

								if ( $value === 'negative' && $rating < 3 ) {
									$_comments[] = $comment;
								}
							}

							$comments = $_comments;

						break;


						# Filter by helpfulness
						case 'helpful':

							$_comments = array();

							foreach ( $comments as $comment ) {

								$contribution = wc_product_reviews_pro_get_contribution( $comment );
								$ratio = $contribution->get_helpfulness_ratio();

								if ( $ratio >= 0.66 ) {
									$_comments[] = $comment;
								}
							}

							$comments = $_comments;

						break;


						# Apply filters if this is an unknown filter
						default:

							/**
							 * Allow plugins to filter comments using a custom filter
							 *
							 * @since 1.0.0
							 * @param \WP_Comment[] $comments The comments array.
							 * @param array $args Associative array of arguments including the filter and value.
							 */
							$comments = apply_filters( 'wc_product_reviews_pro_filter_comments', $comments, array( 'filter' => $filter, 'value' => $value ) );

						break;
					}
				}
			}
		}

		return $comments;
	}


	/**
	 * Order comments (contributions) on frontend.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 * @param \WP_Comment[] $comments Array of comments.
	 * @return \WP_Comment[] $comments
	 */
	public function order_comments( $comments ) {
		global $post;

		if ( 'product' === $post->post_type ) {

			$orderby = get_option( 'wc_product_reviews_pro_contributions_orderby' );

			switch ( $orderby ) {

				// Order contributions by most helpful ratio
				// TODO: implement a better algorithm for determining usefulness
				case 'most_helpful':

					foreach ( $comments as $key => $comment ) {

						$contribution = wc_product_reviews_pro_get_contribution( $comment );
						$comment->helpfulness_ratio = $contribution->get_helpfulness_ratio();

						$comments[ $key ] = $comment;
					}

					usort( $comments, array( $this, 'compare_helpfulness_ratio' ) );

					if ( has_filter( 'comments_array', array( $this, 'reverse_reviews_order' ) ) && 'desc' === get_option( 'comment_order' ) ) {

						$comments = array_reverse( $comments );
					}

				break;

				// The comments template defaults to oldest first
				case 'newest':

					// reverse the order if WP comments are set to show older first, do nothing otherwise
					if ( 'asc' === get_option( 'comment_order' ) ) {

						$comments = array_reverse( $comments );
					}

				break;
			}
		}

		return $comments;
	}


	/**
	 * Adjust sort order of reviews according to WordPress Discussion comment sorting settings.
	 *
	 * Note: this only runs from WordPress 4.5 onwards.
	 *
	 * @internal
	 *
	 * @since 1.6.5
	 * @param array $query_args Comment template query args.
	 * @return array
	 */
	public function filter_comments_template_query_args( $query_args ) {

		if ( isset( $query_args['post_id'] ) && 'product' === get_post_type( $query_args['post_id'] ) ) {

			if ( 1 !== (int) get_option( 'page_comments' ) || 'newest' === get_option( 'wc_product_reviews_pro_contributions_orderby' ) ) {
				$query_args['order'] = 'DESC';
			}

			add_filter( 'comments_array', array( $this, 'reverse_reviews_order' ), 9 );
		}

		return $query_args;
	}


	/**
	 * Reverse the order of reviews.
	 *
	 * @internal
	 * @see WC_Product_Reviews_Pro_Frontend::filter_comments_template_query_args()
	 *
	 * @since 1.6.5
	 * @param \WP_Comment[] $reviews Array of comments (reviews) to sort.
	 * @return \WP_Comment[]
	 */
	public function reverse_reviews_order( $reviews ) {
		return is_array( $reviews ) && ! empty( $reviews ) ? array_reverse( $reviews ) : $reviews;
	}


	/**
	 * Compare contributions based on helpfulness ratios
	 *
	 * `usort()` function callback, returns any of the following:
	 *
	 * -1 - $comment_a is below $comment_b
	 *  0 - $comment_a is equal to $comment_b
	 *  1 - $comment_a is above $comment_b
	 *
	 * @since 1.0.0
	 * @param \WP_Comment $comment_a First comment to compare
	 * @param \WP_Comment $comment_b Second comment to compare
	 * @return int
	 */
	private function compare_helpfulness_ratio( $comment_a, $comment_b ) {

		// disregard child comments, keep default sorting by date
		if ( $comment_a->comment_parent > 0 || $comment_b->comment_parent > 0 ) {
			return 0;
		}

		return strcmp( $comment_b->helpfulness_ratio, $comment_a->helpfulness_ratio );
	}


	/**
	 * Adjust the login message
	 *
	 * @param string $message
	 * @return string
	 */
	public function login_message( $message ) {

		$redirect_to = isset( $_GET['redirect_to'] ) ? urldecode( $_GET['redirect_to'] ) : '';

		// Display a message when trying to vote for a contribution
		if ( $redirect_to ) {

			$params = array();
			parse_str( parse_url( $redirect_to, PHP_URL_QUERY ), $params );

			if ( isset( $params['action'] ) && 'vote_for_contribution' == $params['action'] ) {
				$message = '<p class="message">' . __( 'You must be logged in to vote' ) . '</p>';
			}
		}

		return $message;
	}


	/**
	 * Customize the review product tab
	 *
	 * Will replace the review tab title with a more generic
	 * one if multiple contribution types are enabled, or
	 * with a specific title, if only one type is enabled.
	 *
	 * @since 1.0.0
	 * @param array $tabs
	 * @return array
	 */
	public function customize_review_tab( $tabs ) {
		global $post;

		if ( isset( $tabs['reviews'] ) ) {

			if ( $reviews_tab_title = $this->get_reviews_tab_title( $post->ID ) ) {
				$tabs['reviews']['title'] = $reviews_tab_title;
			} else {
				// hide reviews tab if there is no title, i.e. no contribution types are enabled
				unset( $tabs['reviews'] );
			}
		}

		return $tabs;
	}

	/**
	 * Get the reviews product tab title based on enabled contribution types
	 *
	 * @since 1.10.0
	 *
	 * @param int $product_id The product ID
	 * @return string The reviews tab title based on enabled contribution types
	 */
	private function get_reviews_tab_title( $product_id ) {

		$title = '';

		$enabled_contribution_types = wc_product_reviews_pro_get_enabled_contribution_types();

		// do not take contribution_comments into account
		if ( ( $key = array_search( 'contribution_comment', $enabled_contribution_types, true ) ) !== false ) {
			unset( $enabled_contribution_types[ $key ] );
		}

		// bail with empty title if none of the types are enabled
		if ( empty( $enabled_contribution_types ) ) {
			return $title;
		}

		// for single types, get their type-specific tab title
		elseif ( 1 === count( $enabled_contribution_types ) ) {

			$type              = $enabled_contribution_types[0];
			$contribution_type = wc_product_reviews_pro_get_contribution_type( $type );
			$count             = wc_product_reviews_pro_get_contributions_count( $product_id, $type );
			$title             = $contribution_type->get_tab_title( $count );
		}

		// otherwise, get the Discussions title and correct number of contributions
		else {

			$count             = wc_product_reviews_pro_get_contributions_count( $product_id, $enabled_contribution_types );
			$contribution_type = wc_product_reviews_pro_get_contribution_type( null );
			$title             = $contribution_type->get_tab_title( $count );
		}

		return $title;
	}


	/**
	 * Add an author badge before the author name in review meta.
	 *
	 * @internal
	 *
	 * @since 1.7.0
	 * @param string $author The author name string.
	 * @param int $comment_id The comment ID where the author name appears.
	 * @return string HTML.
	 */
	public function add_author_badge( $author, $comment_id ) {

		$contribution = wc_product_reviews_pro_get_contribution( $comment_id );

		if ( $contribution && $contribution->is_type( wc_product_reviews_pro_get_enabled_contribution_types() ) ) {

			ob_start();

			wc_product_reviews_pro_author_badge( $contribution->comment );

			$badge = ob_get_clean();

			if ( ! empty( $badge ) ) {

				$author = $badge . ' ' . $author;
			}
		}

		return $author;
	}



	/**
	 * Customize the front-end product review count.
	 *
	 * By default, $product->get_review_count() will return all
	 * contribution types. We only want the number of actual reviews
	 * for the "x customer reviews" link, for example.
	 *
	 * @since 1.2.0
	 * @param int $count The number of contributions of any type.
	 * @param \WC_Product $product The current product.
	 * @return int The actual number of reviews.
	 */
	public function customize_review_count( $count, $product ) {

		if ( is_singular( 'product' ) ) {
			$count = wc_product_reviews_pro_get_contributions_count( $product->get_id(), 'review' );
		}

		return $count;
	}


	/**
	 * Add redirect field to my-account/form-login.php
	 *
	 * Allows specifying the page to redirect to after logging in
	 *
	 * @since 1.0.0
	 */
	public function add_redirect_to_field() {

		if ( is_account_page() && isset( $_REQUEST['redirect_to'] ) ) {

			?><input type="hidden" name="redirect" value="<?php echo esc_attr( $_REQUEST['redirect_to'] ); ?>" /><?php
		}
	}


	/**
	 * Handle posted comment/contribution data from session.
	 *
	 * @since 1.0.0
	 */
	public function handle_postdata_from_session() {

		if ( empty( $_POST ) && isset( WC()->session->wc_product_reviews_pro_posted_data ) ) {

			// Mimick $_POST data by getting the post data from WC session
			$_POST = WC()->session->wc_product_reviews_pro_posted_data;

			// Unset data from session, because we only need it once
			WC()->session->wc_product_reviews_pro_posted_data = null;

			// Handle displaying errors
			$type = isset( $_POST['comment_type'] ) ? $_POST['comment_type'] : null;

			if ( $type ) {

				// Unhook wc_print_notices from product page top
				remove_action( 'woocommerce_before_single_product', 'wc_print_notices' );

				// Print notices just before the contributions form
				if ( 'contribution_comment' === $type ) {

					add_action( 'wc_product_reviews_pro_before_' . $type .'_' . $_POST['comment_parent'] . '_form', 'wc_print_notices', 10 );

				} else {

					add_action( 'wc_product_reviews_pro_before_' . $type .'_form', 'wc_print_notices', 10 );
				}
			}
		}
	}


	/**
	 * Add new structured data types for pages with contributions.
	 *
	 * @internal
	 *
	 * @since 1.7.0
	 * @param array $types Structured data types (e.g. "review").
	 * @return array
	 */
	public function add_structured_data_types( $types ) {

		// {BR} do not capitalize these
		if ( is_product() ) {
			$types = array_merge( $types, array(
				'question',
				'imageobject',
				'comment',
			) );
		}

		return $types;
	}


	/**
	 * Improves the "offer" structured data markup.
	 *
	 * @internal
	 *
	 * @since 1.7.0
	 * @param array $offer_markup the markup for the product offer
	 * @return array improved markup
	 */
	public function improve_offer_structured_data( $offer_markup, $product ) {

		$offer_markup['category']       = wc_get_product_category_list( $product->get_id() );
		$offer_markup['seller']['name'] = Framework\SV_WC_Helper::get_site_name();

		return $offer_markup;
	}


	/**
	 * Adds additional structured data for the product to improve aggregateRating.
	 *
	 * TODO: We want to add the 'review' property here in the future to nest product reviews under the product object,
	 *  but likely this will need a lazy / background load in the case of hundreds of reviews {BR 2017-02-25}
	 *
	 * @internal
	 *
	 * @since 1.7.0
	 *
	 * @param array $product_markup the product schema markup
	 * @return array updated markup
	 */
	public function additional_product_structured_data( $product_markup ) {

		if ( isset( $product_markup['aggregateRating'] ) ) {
			$product_markup['aggregateRating']['bestRating'] = 5;
		}

		return $product_markup;
	}


	/**
	 * Filter reviews structured data with more attributes depending on contribution.
	 *
	 * TODO: When reviews are nested in the product object, the included data could likely
	 *  be simplified a bit (ie no publisher needed). {BR 2017-02-25}
	 *
	 * @internal
	 *
	 * @since 1.7.0
	 * @param array $schema Structured data.
	 * @param \WP_Comment $comment The comment (WC review) object.
	 * @return array Filtered markup.
	 */
	public function add_structured_data( $schema, $comment = null ) {

		if ( ! $comment instanceof \WP_Comment ) {
			global $comment;
		}

		$contribution = wc_product_reviews_pro_get_contribution( $comment );

		if ( $comment && $contribution ) {

			if ( 'review' !== $contribution->get_type() ) {
				$schema['dateCreated'] = get_comment_date( 'c', $comment );
				unset( $schema['itemReviewed'], $schema['reviewRating'], $schema['datePublished'] );
			}

			if ( wc_product_reviews_pro_contribution_supports_upvote_downvote_schema( $contribution ) ) {

				$schema['upvoteCount']   = (int) $contribution->get_positive_votes();
				$schema['downvoteCount'] = (int) $contribution->get_negative_votes();
			}

			switch ( $contribution->get_type() ) {

				case 'comment' :

					$schema['@type'] = 'Comment';
					$schema['text']  = get_comment_text( $comment );

					unset( $schema['description'] );

				break;

				case 'photo' :

					$schema['@type']      = 'ImageObject';
					$schema['contentUrl'] = $contribution->get_attachment_url();

					if ( $title = $contribution->get_title() ) {
						$schema['caption'] = $title;
					}

				break;

				case 'question' :

					$schema['@type'] = 'Question';
					$schema['text']  = get_comment_text( $comment );

					unset( $schema['description'] );

				break;

				case 'review' :

					if ( $title = $contribution->get_title() ) {
						$schema['name'] = $title;
					}

					// ensure this review is tied back to the product
					$schema['itemReviewed'] = array(
						'@type'  => 'Product',
						'name'   => get_the_title( $comment->comment_post_ID ),
						'sameAs' => get_permalink( $comment->comment_post_ID ),
					);

					// docs have mixed info on whether this is required or not
					// so let's add it just in case ¯\_(ツ)_/¯
					$schema['publisher'] = array(
						'@type' => 'Organization',
						'name'  => Framework\SV_WC_Helper::get_site_name(),
					);

					// description *may* be required since this isn't nested, < 200 chars
					$schema['reviewBody']  = get_comment_text( $comment );
					$schema['description'] = Framework\SV_WC_Helper::str_truncate( $schema['reviewBody'], 200 );

				break;

				case 'video' :

					$schema['@type'] = 'Comment';
					$schema['text']  = get_comment_text( $comment );

					if ( $title = $contribution->get_title() ) {
						$schema['name'] = $title;
					}

				break;
			}

			if ( 'photo' === $contribution->get_attachment_type() && 'photo' !== $contribution->get_type() ) {

				$schema['associatedMedia'] = array(
					'@type'      => 'ImageObject',
					'contentUrl' => $contribution->get_attachment_url(),
				);
			}

			if ( ! wc_review_ratings_enabled() ) {

				unset( $schema['reviewRating'] );
			}
		}

		return $schema;
	}

	/**
	 * Queue count_comments modifier
	 *
	 * @since 1.12.1
	 *
	 * @param array $stats An empty array.
	 * @param int $post_id Optional. The post ID.
	 * @return array
	 */
	public function queue_count_comments_modifier( $stats, $post_id ) {

		if ( ! $post_id && ! empty( $stats ) && $stats->moderated > 0 ) {
			$reviews_count = wc_product_reviews_pro_get_reviews_count( 'all', '0' );

			$stats->moderated -= $reviews_count;
		}

		return $stats;
	}
}
