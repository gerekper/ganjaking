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
 * Template function overrides
 *
 * @since 1.0.0
 */


if ( ! function_exists( 'wc_product_reviews_pro_contributions' ) ) {

	/**
	 * Output the Contributions comments template.
	 *
	 * @since 1.0.0
	 * @param \WP_Comment $comment
	 * @param array $args
	 * @param int $depth
	 */
	function wc_product_reviews_pro_contributions( $comment, $args, $depth ) {

		$contribution = wc_product_reviews_pro_get_contribution( $comment );

		// The default template path.
		$template = 'single-product/contributions/contribution';

		// If a type-specific template exists, add the type to the template string.
		if ( file_exists( wc_locate_template( $template . '-' . $contribution->get_type() . '.php' ) ) ) {
			$template .= '-' . $contribution->get_type();
		}

		$template .= '.php';

		wc_get_template( $template, array(
			'contribution' => $contribution,
			'comment'      => $comment,
			'args'         => $args,
			'depth'        => $depth,
		) );
	}

}

/**
 * Display the contribution karma markup.
 *
 * @since 1.2.0
 * @param \WC_Contribution $contribution
 */
function wc_product_reviews_pro_contribution_karma( $contribution ) {

	wc_get_template( 'single-product/contributions/karma.php', array(
		'contribution' => $contribution,
	) );
}

/**
 * Display the contribution meta markup.
 *
 * @since 1.2.0
 * @param \WC_Contribution $contribution
 */
function wc_product_reviews_pro_contribution_meta( $contribution ) {

	$template_file = 'single-product/contributions/meta.php';

	wc_get_template( $template_file, array(
		'contribution' => $contribution,
		'comment'      => $contribution->get_comment_data(),
	) );
}

/**
 * Display the contribution attachments.
 *
 *
 * @since 1.2.0
 * @param \WC_Contribution $contribution
 * @param bool $wrap_microdata Whether to add microdata to markup output (versions below WC 3.0).
 */
function wc_product_reviews_pro_contribution_attachments( $contribution, $wrap_microdata = true ) {

	$template_file = 'single-product/contributions/attachments.php';

	wc_get_template( $template_file, array(
		'contribution'   => $contribution,
		'wrap_microdata' => $wrap_microdata,
	) );
}

/**
 * Get the contribution attachment image.
 *
 * @since 1.2.0
 * @param \WC_Contribution $contribution
 * @return \WC_Contribution|false The contribution attachment image markup.
 */
function wc_product_reviews_pro_get_contribution_attachment_image( $contribution ) {

	$image = false;

	if ( $attachment_url = $contribution->get_attachment_url() ) {

		$image = '<img src="' . esc_url( $attachment_url ) . '" />';

    } elseif ( $contribution->has_attachment() ) {

		/**
		 * Filter the attached image size.
		 *
		 * Note that this only applies to images that were uploaded from the user's computer.
		 *
		 * @since 1.2.0
		 * @param string|array $size The desired image size. Default: large.
		 * @param \WC_Contribution $contribution The current contribution.
		 */
		$image_size = apply_filters( 'wc_product_reviews_pro_contribution_image_size', 'large', $contribution );

		$image = wp_get_attachment_image( $contribution->get_attachment_id(), $image_size, false );
	}

	/**
	 * Filter the attached image size.
	 *
	 * @since 1.2.0
	 * @param string $image The image markup.
	 * @param \WC_Contribution $contribution The current contribution.
	 */
	$image = apply_filters( 'wc_product_reviews_pro_contribution_image', $image, $contribution );

	return $image;
}


/**
 * Display the contribution actions markup.
 *
 * @since 1.2.0
 * @param \WC_Contribution $contribution
 */
function wc_product_reviews_pro_contribution_actions( $contribution ) {

	$action = '';

	// Display a subscription action only for top level contributions
	if ( isset( $contribution->comment->comment_parent ) && (int) $contribution->comment->comment_parent === 0 ) {

		if ( is_user_logged_in() && wc_product_reviews_pro_comment_notification_enabled() ) {

			$user = wp_get_current_user();

			if ( in_array( $user->ID, wc_product_reviews_pro_get_comment_notification_subscribers( $contribution ), false ) ) {
				$action = 'unsubscribe';
			} else {
				$action = 'subscribe';
			}
		}
	}

	$template_file = 'single-product/contributions/actions.php';

	wc_get_template( $template_file, array(
		'contribution'  => $contribution,
		'notifications' => $action,
	) );
}

/**
 * Determine if a contribution supports the "upvoteCount" & "downvoteCount" schema properties.
 *
 * @since 1.4.3
 * @param \WC_Contribution $contribution the contribution object
 * @return bool
 */
function wc_product_reviews_pro_contribution_supports_upvote_downvote_schema( WC_Contribution $contribution ) {

	$type = $contribution->get_type();

	return 'question' === $type || 'video' === $type || 'contribution_comment' === $type;
}

if ( ! function_exists( 'wc_product_reviews_pro_contribution_comment_form' ) ) {

	/**
	 * Output the contribution comment form template.
	 *
	 * @since 1.0.0
	 * @param \WP_Comment $comment
	 * @param array $args
	 * @param int $depth
	 */
	function wc_product_reviews_pro_contribution_comment_form( $comment, $args, $depth ) {

		$contribution_types = wc_product_reviews_pro_get_enabled_contribution_types();

		// if comments are disabled, bail
		if ( ! in_array( 'contribution_comment', $contribution_types, true ) ) {
			return;
		}

		if ( ! $comment->comment_parent ) {

			wc_get_template( 'single-product/form-contribution.php', array(
				'comment' => $comment,
				'args'    => $args,
				'depth'   => $depth,
				'type'    => 'contribution_comment',
			) );
		}
	}

}


if ( ! function_exists( 'wc_product_reviews_pro_contribution_flag_form' ) ) {

	/**
	 * Output the contribution flag form template.
	 *
	 * @since 1.0.0
	 * @param \WP_Comment $comment
	 */
	function wc_product_reviews_pro_contribution_flag_form( $comment ) {

		wc_get_template( 'single-product/form-flag-contribution.php', array(
			'comment' => $comment,
		) );
	}

}


if ( ! function_exists( 'wc_product_reviews_pro_review_qualifiers_form_controls' ) ) {

	/**
	 * Output the contribution flag form template.
	 *
	 * @since 1.0.0
	 */
	function wc_product_reviews_pro_review_qualifiers_form_controls() {

		wc_get_template( 'single-product/form-control-review-qualifiers.php' );
	}

}


if ( ! function_exists( 'wc_product_reviews_pro_review_qualifiers' ) ) {

	/**
	 * Output the contribution flag form template.
	 *
	 * @since 1.0.0
	 * @param \WC_Contribution $contribution
	 */
	function wc_product_reviews_pro_review_qualifiers( $contribution ) {

		wc_get_template( 'single-product/contributions/contribution-review-qualifiers.php', array(
			'contribution' => $contribution,
		) );
	}

}


if ( ! function_exists( 'wc_product_reviews_pro_contributions_list_title' ) ) {

	/**
	 * Output the contributions list title
	 *
	 * @param string $current_type Optional
	 * @param int $count Optional
	 * @param int $rating Optional
	 */
	function wc_product_reviews_pro_contributions_list_title( $current_type = '', $count = 0, $rating = null ) {

		if ( ! $current_type ) {
			esc_html_e( 'What others are saying', 'woocommerce-product-reviews-pro' );
		} else {
			$contribution_type = wc_product_reviews_pro_get_contribution_type( $current_type );
			echo $contribution_type->get_list_title( $count, $rating );
		}
	}

}


if ( ! function_exists( 'wc_product_reviews_pro_contributions_list_no_results_text' ) ) {

	/**
	 * Outputs the no results text, depending on current type context.
	 *
	 * If there are contributions for a given product, but the optional threshold is not met, a corresponding message will be displayed.
	 *
	 * @since 1.0.0
	 *
	 * @param string $current_type the contribution type
	 * @param int|\WP_Post|\WC_Product $product a product identifier (defaults to global post object)
	 */
	function wc_product_reviews_pro_contributions_list_no_results_text( $current_type = '', $product = null) {
		global $post;

		$default_text = esc_html__( 'There are no contributions yet.', 'woocommerce-product-reviews-pro' );
		$product_id   = null;

		if ( null === $product ) {
			$product = $post;
		}

		if ( $product instanceof \WP_Post ) {
			$product_id = $product->ID;
		} elseif ( $product instanceof \WC_Product ) {
			$product_id = $product->get_id();
		} elseif ( is_numeric( $product ) ) {
			$product_id = (int) $product;
		}

		if ( $product_id && wc_product_reviews_pro()->get_frontend_instance()->is_product_contributions_below_threshold( $product_id ) ) {

			$contribution        = null;
			$contributions_label = __( 'Contributions', 'woocommerce-product-reviews-pro' );

			if ( $current_type ) {

				$contribution        = wc_product_reviews_pro_get_contribution_type( $current_type );
				$contributions_label = $contribution ? $contribution->get_title( true ) : $contributions_label;

			} else {

				$enabled_contribution_types = wc_product_reviews_pro_get_enabled_contribution_types();

				unset( $enabled_contribution_types['contribution_comment'] );

				if ( 1 === count( $enabled_contribution_types ) ) {

					$contribution        = wc_product_reviews_pro_get_contribution_type( current( $enabled_contribution_types ) );
					$contributions_label = $contribution ? $contribution->get_title( true ) : $contributions_label;
				}
			}

			$below_threshold_text = esc_html(
				/* translators: Placeholder: %s - contribution type (plural) */
				sprintf( __( 'We are accepting %s for this product, and will display them when we get a few more!', 'woocommerce-product-reviews-pro' ), strtolower( $contributions_label ) )
			);

			/**
			 * Filters the message displayed when product contributions are below the threshold set.
			 *
			 * @since 1.10.0
			 *
			 * @param string $below_threshold_text the message text displayed in front end notice to customer
			 * @param string $contribution_type the contribution type (or empty string if undetermined)
			 */
			echo apply_filters( 'wc_product_reviews_pro_contribution_type_below_threshold_text', $below_threshold_text, $contribution ? $contribution->type : '' );

		} elseif ( ! $current_type ) {

			echo $default_text;

		} else {

			$contribution_type = wc_product_reviews_pro_get_contribution_type( $current_type );

			echo $contribution_type ? $contribution_type->get_no_results_text() : $default_text;
		}
	}

}


if ( ! function_exists( 'wc_product_reviews_pro_contribution_list_table' ) ) {

	/**
	 * List the current user's Contributions as a table
	 *
	 * @since 1.6.0
	 */
	function wc_product_reviews_pro_contribution_list_table() {

		$product_reviews_pro = wc_product_reviews_pro();

		// get the comments for the user
		$comments = get_comments( array( 'user_id' => get_current_user_id() ) );

		// we'll pass in post types the comments have been left on,
		// so we can display comments only on products
		$comments_on = array();

		foreach ( $comments as $comment ) {

			$comments_on[] = get_post_type( $comment->comment_post_ID );
		}

		// get enabled contribution types
		$enabled_contribution_types = wc_product_reviews_pro_get_enabled_contribution_types();

		if ( ! empty( $enabled_contribution_types ) ) {

			wc_get_template(
				'myaccount/contribution-list.php',
				array(
					'comments'                   => $comments,
					'enabled_contribution_types' => $enabled_contribution_types,
					'comments_on'                => $comments_on,
				),
				'',
				$product_reviews_pro->get_plugin_path() . '/templates/'
			);
		}
	}

}


if ( ! function_exists( 'wc_product_reviews_pro_admin_badge' ) ) {

	/**
	 * Outputs the badge for admin / shop manager comments if set.
	 *
	 * @since 1.2.0
	 *
	 * @param \WP_Comment $comment the contribution's comment object
	 */
	function wc_product_reviews_pro_author_badge( $comment ) {

		$badge_text = get_option( 'wc_product_reviews_pro_contribution_badge' );

		/**
		 * Filters the badge text.
		 *
		 * @since 1.2.0
		 *
		 * @param string $badge_text the badge text
		 * @param \WP_Comment $comment the current comment
		 */
		$badge_text = (string) apply_filters( 'wc_product_reviews_pro_contribution_badge_text', $badge_text, $comment );

		if ( ! empty ( $badge_text ) && is_object( $comment ) ) {

			$badge = '';

			if ( $comment->user_id ) {

				$userdata = get_userdata( $comment->user_id );
				$roles    = $userdata ? $userdata->roles : array();

				if ( in_array( 'administrator', $roles, true ) || in_array( 'shop_manager', $roles, true ) || user_can( $comment->user_id, 'manage_network' ) ) {

					$badge = '<span class="contribution-badge contribution-badge-admin">' . esc_html( $badge_text ) . '</span>';
				}
			}

			/**
			 * Filters the admin / shop manager badge markup.
			 *
			 * @since 1.2.0
			 *
			 * @param string $badge the badge markup (may contain HTML)
			 * @param \WP_Comment $comment the comment data
			 */
			echo apply_filters( 'wc_product_reviews_pro_author_badge', $badge, $comment );
		}
	}

}


if ( ! function_exists( 'wc_product_reviews_pro_get_enabled_types_name' ) ) {

	/**
	 * Get the name for the enabled contribution types
	 *
	 * @since 1.2.0
	 * @return string $type_title Title for enabled contributions
	 */
	function wc_product_reviews_pro_get_enabled_types_name() {

		$enabled_contribution_types = wc_product_reviews_pro_get_enabled_contribution_types();

		// Do not take contribution_comments into account
		if ( ( $key = array_search( 'contribution_comment', $enabled_contribution_types, false ) ) !== false ) {
			unset( $enabled_contribution_types[$key] );
		}

		// For single types, get their type-specific section title
		if ( count( $enabled_contribution_types ) === 1 ) {

			$type = $enabled_contribution_types[0];
			$contribution_type = wc_product_reviews_pro_get_contribution_type( $type );

			$type_title = strtolower( $contribution_type->get_title( true ) );
		} else {
			$type_title = __( 'contributions', 'woocommerce-product-reviews-pro' );
		}

		return apply_filters( 'wc_product_reviews_pro_enabled_types_name', $type_title, $enabled_contribution_types );
	}

}
