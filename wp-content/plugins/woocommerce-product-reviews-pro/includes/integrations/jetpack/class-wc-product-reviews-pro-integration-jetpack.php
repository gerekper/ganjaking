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
 * WooCommerce Jetpack integration.
 *
 * @since 1.12.0
 */
class WC_Product_Reviews_Pro_Integration_Jetpack {


	/** @var array enabled sharing services */
	private $sharing_services = array();

	/** @var bool true if sharing services are available */
	private $is_sharing_available;

	/** @var bool true if Jetpack sharing is enabled */
	private $is_sharing_enabled;


	/**
	 * Hooks into plugin.
	 *
	 * @since 1.10.0
	 */
	public function __construct() {

		// adds the sharing button HTML
		add_action( 'woocommerce_after_template_part', array( $this, 'render_review_sharing_links' ), 1, 4 );

		// add copy on click script
		add_action( 'wp_print_scripts', array( $this, 'add_copy_url_script' ) );

		if ( is_admin() ) {

			// add the review sharing option in Jetpack settings
			add_action( 'sharing_global_options', array( $this, 'add_review_sharing_option' ), 1 );

			// save our option
			add_action( 'sharing_admin_update' , array( $this, 'save_review_sharing_option' ) );
		}
	}


	/**
	 * Adjusts the Jetpack sharing settings to include product reviews.
	 *
	 * @internal
	 *
	 * @since 1.12.0
	 */
	public function add_review_sharing_option() {

		?>
		<tr valign="top">
			<th scope="row">
				<label><?php
					/* translators: Placeholder: %s - enabled contributions name, e.g., "contributions" or "reviews" */
					echo esc_html( sprintf( __( 'Show buttons on product %s', 'woocommerce-product-reviews-pro' ), wc_product_reviews_pro_get_enabled_types_name() ) ); ?></label>
			</th>
			<td>
				<label>
					<input
						type="checkbox"
						name="wc_product_reviews_pro_show_jetpack_sharing"
						<?php checked( $this->is_sharing_enabled() ); ?>
					/>
					<?php esc_html_e( 'Yes', 'woocommerce-product-reviews-pro' ); ?>
				</label>
			</td>
		</tr>
		<?php
	}


	/**
	 * Saves the custom product review sharing option.
	 *
	 * @internal
	 *
	 * @since 1.12.0
	 */
	public function save_review_sharing_option() {

		$value = isset( $_POST['wc_product_reviews_pro_show_jetpack_sharing'] ) && 'on' === $_POST['wc_product_reviews_pro_show_jetpack_sharing'] ? 'yes' : 'no';

		// we don't validate a nonce here as Jetpack only fires this action when it's already done so
		update_option( 'wc_product_reviews_pro_show_jetpack_sharing', $value );
	}


	/**
	 * Renders the sharing icons after the product review.
	 *
	 * @since 1.12.0
	 *
	 * @param string $template_name template name
	 * @param string $template_path template path, unused
	 * @param string $located template location, unused
	 * @param array $args template arguments
	 */
	public function render_review_sharing_links( $template_name, $template_path, $located, $args ) {

		// render sharing links after the contribution flagging target
		if (    'single-product/form-flag-contribution.php' === $template_name
			 && isset( $args['comment'] )
			 && $args['comment'] instanceof \WP_Comment ) {

			$this->maybe_render_review_sharing_html( $args['comment'] );
		}
	}


	/**
	 * Adds sharing link scripts when permalinks are being shown.
	 *
	 * @internal
	 *
	 * @since 1.12.0
	 */
	public function add_copy_url_script() {

		// only add this to product pages when review link sharing is shown
		if ( $this->is_sharing_shown() ) {

			$copied_alert = __( 'Copied URL - ready to share!', 'woocommerce-product-reviews-pro' );

			wc_enqueue_js( '
				$( document.body ).on( "click", ".sharedaddy.woocommerce-product-reviews .share-direct-link a", function( evt ) {
					evt.preventDefault();
					var temp = $( "<input>" );
					$( "body" ).append( temp );
					temp.val( $( this ).attr( "href" ) ).select();
					document.execCommand( "copy" );
					temp.remove();
					alert( "' . esc_js( $copied_alert ) . '\n" + $( this ).attr( "href" ) );
				} );
			' );
		}
	}


	/**
	 * Renders the sharing link html.
	 *
	 * @since 1.12.0
	 *
	 * @param \WP_Comment $comment the current comment object
	 */
	private function maybe_render_review_sharing_html( $comment ) {

		// exclude comment-type replies and be sure that sharing is enabled
		if ( class_exists( 'Sharing_Service' ) && $this->is_sharing_enabled( $comment ) ) {

			$sharer   = new Sharing_Service();
			/** this filter is documented in jetpack/modules/sharedaddy/sharing-service.php on line 292 */
			$services = apply_filters( 'sharing_enabled', $sharer->get_blog_services() );

			// only render HTML if we've got enabled services
			if ( count( $services['all'] ) > 0 ) {

				echo $this->build_sharing_html( $comment, $this->get_sharing_services() );
			}
		}
	}


	/**
	 * Builds the sharing HTML.
	 *
	 * We're forcing the "icon" style buttons currently to keep this block smaller, but may want to use the selected style in the future.
	 *
	 * @since 1.12.0
	 *
	 * @param \WP_Comment $comment the current comment object
	 * @param array $services the enabled sharing services
	 * @return string sharing HTML
	 */
	private function build_sharing_html( $comment, $services ) {

		ob_start();

		?>
		<div class="sharedaddy sd-sharing-enabled woocommerce-product-reviews wc-product-reviews-pro">
			<div class="robots-nocontent sd-block sd-social sd-social-icon sd-sharing">

				<span class="review-sharing-title"><strong><?php esc_html_e( _x( 'Share:', 'share on a social network', 'woocommerce-product-reviews-pro' ) ); ?></strong></span>

				<div class="sd-content">
					<ul>

						<?php foreach ( $services as $service ) : ?>

							<?php

							$sharing_url = $this->get_sharing_url( $service, $comment );

							if ( empty( $sharing_url ) ) {
								continue;
							}

							?>

							<li class="share-<?php echo esc_attr( $service->get_class() ); ?>">
								<a rel="nofollow"
								   data-shared="sharing-<?php echo esc_attr( $service->get_id() ); ?>-<?php echo esc_attr( $comment->comment_ID ); ?>"
								   class="share-<?php echo esc_attr( $service->get_class() ); ?> sd-button share-icon no-text"
								   href="<?php echo esc_url( $sharing_url ); ?>"
								   target="_blank"
								   title="<?php echo esc_html( sprintf( __( 'Click to share on %s', 'woocommerce-product-reviews-pro' ), $service->get_name() ) ); ?>">
									<span></span>
									<span class="sharing-screen-reader-text">
										<?php echo esc_html( sprintf( __( 'Click to share on %s', 'woocommerce-product-reviews-pro' ), $service->get_name() ) ); ?>
										<?php esc_html_e( ' (Opens in new window)', 'jetpack' ); // intentional use of different text domain as this is likely to be translated already in Jetpack ?>
									</span>
								</a>
							</li>

						<?php endforeach; ?>

						<li class="share-direct-link">
							<a rel="nofollow"
							   data-shared="sharing-direct-link-<?php echo esc_attr( $comment->comment_ID ); ?>"
							   class="share-direct-link sd-button share-icon no-text"
							   href="<?php echo esc_url( get_comment_link( $comment ) ); ?>"
							   target="_blank"
							   title="<?php echo esc_html( __( 'Click to copy link', 'woocommerce-product-reviews-pro' ) ); ?>">
								<span class="dashicons dashicons-admin-links"></span>
							</a>
						</li>

						<li class="share-end"></li>

					</ul>
				</div>
			</div>
		</div>
		<?php

		return ob_get_clean();
	}


	/**
	 * Returns a sharing URL for the service.
	 *
	 * This is mostly copied from Jetpack's Sharing_Source classes, as their $service->get_display()
	 *  method requires a WP_Post object and builds the link from the post.
	 * We're sharing a comment, so we want to use the comment URL instead and thus can't pass in the WP_Post.
	 *
	 * @since 1.12.0
	 *
	 * @param \Sharing_Source $service a Jetpack sharing service object
	 * @param \WP_Comment $comment the current comment
	 * @return string the sharing URL for the service
	 */
	private function get_sharing_url( $service, $comment ) {

		switch ( $service->get_id() ) {

			case 'twitter':

				$url = add_query_arg( array(
					'text' => rawurlencode( wp_trim_words( $comment->comment_content, 238 ) ),
					'via'  => rawurlencode( $service::sharing_twitter_via( $comment->comment_post_ID ) ),
					'url'  => rawurlencode( get_comment_link( $comment ) ),
				), 'https://twitter.com/intent/tweet' );

			break;

			case 'email':
				$url = 'mailto:?&subject=You%20may%20enjoy%20this&body=' . rawurlencode( wp_trim_words( $comment->comment_content, 238 ) ) . '%20' . rawurlencode( get_comment_link( $comment ) ) . '%0A%0Afrom%20' . Framework\SV_WC_Helper::get_site_name();
			break;

			case 'reddit':

				$url = add_query_arg( array(
					'url'   => rawurlencode( get_comment_link( $comment ) ),
					'title' => rawurlencode( wp_trim_words( $comment->comment_content, 238 ) ),
				), is_ssl() ? 'https' : 'http' . '://reddit.com/submit' );

			break;

			case 'linkedin':

				$url = add_query_arg( array(
					'token'    => '',
					'isFramed' => 'false',
					'url'      => rawurlencode( get_comment_link( $comment ) ),
				), 'https://www.linkedin.com/cws/share' );

			break;

			case 'facebook':

				$url = add_query_arg( array(
					'u' => rawurlencode( get_comment_link( $comment ) ),
					't' => rawurlencode( wp_trim_words( $comment->comment_content, 238 ) ),
				), is_ssl() ? 'https' : 'http' . '://www.facebook.com/sharer.php' );

			break;

			case 'print':
				$url = get_comment_link( $comment );
			break;

			case 'googleplus1':

				$url = add_query_arg( array(
					'url' => rawurlencode( get_comment_link( $comment ) ),
				), 'https://plus.google.com/share' );

			break;

			case 'tumblr':

				$url = add_query_arg( array(
					'v' => '3',
					'u' => rawurlencode( get_comment_link( $comment ) ),
					't' => rawurlencode( wp_trim_words( $comment->comment_content, 238 ) ),
				), 'http://www.tumblr.com/share' );

			break;

			case 'pinterest':

				$url = add_query_arg( array(
					'url'         => rawurlencode( get_comment_link( $comment ) ),
					'media'       => rawurlencode( get_the_post_thumbnail_url( $comment->comment_post_ID, 'medium' ) ? get_the_post_thumbnail_url( $comment->comment_post_ID, 'medium' ) : 'https://s0.wp.com/i/blank.jpg' ),
					'description' => rawurlencode( wp_trim_words( $comment->comment_content, 238 ) ),
				), 'https://www.pinterest.com/pin/create/button/' );

			break;

			case 'pocket':

				$url = add_query_arg( array(
					'url'   => rawurlencode( get_comment_link( $comment ) ),
					'title' => rawurlencode( wp_trim_words( $comment->comment_content, 238 ) ),
				), 'https://getpocket.com/save/' );

			break;

			case 'jetpack-whatsapp':

				$url = add_query_arg( array(
					'text' => rawurlencode( wp_trim_words( $comment->comment_content, 238 ) ) . ' ' . rawurlencode( get_comment_link( $comment ) ),
				), 'https://api.whatsapp.com/send' );

			break;

			case 'skype':

				$url = add_query_arg( array(
					'url'    => rawurlencode( get_comment_link( $comment ) ),
					'lang'   => 'en-US',
					'source' => 'jetpack',
				), 'https://web.skype.com/share' );

			break;

			// not going to support other sharing services for now
			default:
				$url = '';
			break;
		}

		/**
		 * Filters the sharing URL for a service.
		 *
		 * @since 1.12.0
		 *
		 * @param string the sharing URL
		 * @param \Sharing_Source $service a Jetpack sharing service object
		 * @param \WP_Comment $comment the comment object
		 */
		return (string) apply_filters( 'wc_product_reviews_pro_jetpack_comment_share_url', $url, $service, $comment );
	}


	/**
	 * Gets all enabled Jetpack sharing services.
	 *
	 * @since 1.12.0
	 *
	 * @return \Sharing_Source[] enabled Jetpack sharing services
	 */
	private function get_sharing_services() {

		// just double-check if we think there are no services
		if ( empty( $this->sharing_services ) && class_exists( 'Sharing_Service' ) ) {

			$sharer = new Sharing_Service();

			/** this filter is documented in jetpack/modules/sharedaddy/sharing-service.php on line 292 */
			$services = apply_filters( 'sharing_enabled', $sharer->get_blog_services() );

			// we just want to show all services, we don't care if they're hidden or not
			$this->sharing_services = array_merge( $services['visible'], $services['hidden'] );
		}

		$this->is_sharing_available = ! empty( $this->sharing_services );

		/**
		 * Filters which sharing services to show on contributions.
		 *
		 * @since 1.12.0
		 *
		 * @param \Sharing_Source[] sharing services
		 */
		return apply_filters( 'wc_product_reviews_pro_jetpack_sharing_services', $this->sharing_services );
	}


	/**
	 * Checks whether review link sharing buttons are shown.
	 *
	 * @since 1.12.0
	 *
	 * @return bool
	 */
	private function is_sharing_shown() {

		$show_jetpack_sharing = false;

		if ( function_exists( 'is_product' ) && is_product() && $this->is_sharing_available() ) {

			$show_jetpack_sharing = $this->is_sharing_enabled();
		}

		return $show_jetpack_sharing;
	}


	/**
	 * Determines if sharing services are enabled.
	 *
	 * @since 1.12.0
	 *
	 * @return bool true if some services are enabled
	 */
	private function is_sharing_available() {

		if ( ! $this->is_sharing_available ) {

			$available_sharing          = $this->get_sharing_services();
			$this->is_sharing_available = ! empty( $available_sharing );
		}

		return $this->is_sharing_available;
	}


	/**
	 * Checks whether Jetpack sharing is enabled.
	 *
	 * @since 1.12.0
	 *
	 * @param null|\WP_Comment optional comment
	 * @return bool
	 */
	private function is_sharing_enabled( $contribution = null ) {

		if ( null === $this->is_sharing_enabled ) {

			$this->is_sharing_enabled = 'yes' === get_option( 'wc_product_reviews_pro_show_jetpack_sharing', 'no' );
		}

		$enabled = $this->is_sharing_enabled;

		if ( $enabled && $contribution instanceof \WP_Comment ) {

			$enabled = ! in_array( $contribution->comment_type, array( '', 'contribution-comment' ), true );
		}

		return $enabled;
	}


}
