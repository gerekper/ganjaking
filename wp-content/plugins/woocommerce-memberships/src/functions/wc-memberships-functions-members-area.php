<?php
/**
 * WooCommerce Memberships
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
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2024, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;


/**
 * Checks if we are on the Members Area content.
 *
 * @since 1.9.0
 *
 * @return bool
 */
function wc_memberships_is_members_area() {

	return wc_memberships()->get_frontend_instance()->get_my_account_instance()->get_members_area_instance()->is_members_area();
}


/**
 * Checks if we are currently viewing a Members Area section.
 *
 * @since 1.9.0
 *
 * @param null|array|string $section optional: check against a specific section, an array of sections or any valid section (null)
 * @return bool
 */
function wc_memberships_is_members_area_section( $section = null ) {

	return wc_memberships()->get_frontend_instance()->get_my_account_instance()->get_members_area_instance()->is_members_area_section( $section );
}


/**
 * Gets the Members Area query var.
 *
 * TODO by version 2.0.0 we could allow for customizing the query var like we do for the endpoint so it would be the same regardless of rewrite structure used {FN 2017-09-06}
 *
 * @since 1.13.0
 *
 * @return string
 */
function wc_memberships_get_members_area_query_var() {

	return 'members_area';
}


/**
 * Gets the Members Area endpoint, filtered.
 *
 * @since 1.13.0
 *
 * @return string
 */
function wc_memberships_get_members_area_endpoint() {

	if ( get_option( 'permalink_structure' ) ) {
		$endpoint = (string) get_option( 'woocommerce_myaccount_members_area_endpoint', 'members-area' );
	} else {
		$endpoint = wc_memberships_get_members_area_query_var();
	}

	/**
	 * Filters the members area endpoint for the account.
	 *
	 * @since 1.11.1
	 *
	 * @param string $endpoint the members area endpoint, which may be filtered by third parties
	 * @param \WC_Memberships_User_Membership[] $user_memberships the user memberships (kept for backwards compatibility reasons)
	 * @param string $endpoint the original endpoint used by the Members Area
	 */
	return (string) apply_filters( 'wc_memberships_members_area_endpoint', $endpoint, wc_memberships_get_user_memberships(), $endpoint );
}


/**
 * Returns the members area URL pointing to a specific plan and section.
 *
 * Leave arguments empty to return the base URL only.
 *
 * @since 1.4.0
 *
 * @param null|false|int|\WC_Memberships_Membership_Plan $membership_plan optional plan object or id
 * @param string $members_area_section optional, which section of the members area to point to
 * @param int|string $paged optional, for paged sections
 * @return string unescaped URL
 */
function wc_memberships_get_members_area_url( $membership_plan = null, $members_area_section = '', $paged = '' ) {

	$url                = '';
	$my_account_page_id = wc_get_page_id( 'myaccount' );
	$membership_plan_id = $membership_plan instanceof \WC_Memberships_Membership_Plan ? $membership_plan->get_id() : (int) $membership_plan;

	// bail out if something is wrong (wc_get_page_id() may return negative int)
	if ( $my_account_page_id > 0 ) {

		$using_permalinks = get_option( 'permalink_structure' );

		// grab base URL according to rewrite structure used
		if ( $using_permalinks ) {
			$my_account_url = wc_get_page_permalink( 'myaccount' );
		} else {
			$my_account_url = get_home_url();
		}

		// grab any query strings (sometimes set by translation plugins, e.g. ?lang=it)
		$url_pieces     = parse_url( $my_account_url );
		$query_strings  = ! empty( $url_pieces['query'] ) && is_string( $url_pieces['query'] ) ? explode( '&', $url_pieces['query'] ) : array();
		$my_account_url = preg_replace( '/\?.*/', '', $my_account_url );
		$endpoint       = wc_memberships_get_members_area_endpoint();

		if ( $using_permalinks ) {

			// using permalinks
			// e.g. /my-account/members-area/
			$url = trailingslashit( $my_account_url ) . $endpoint . '/';

		} else {

			// not using permalinks
			// e.g. /?page_id=123&members_area
			$url = add_query_arg(
				[
					'page_id' => $my_account_page_id,
					$endpoint => '',
				],
				trailingslashit( $my_account_url )
			);
		}

		// grab optional members area section and paged requests
		if ( is_numeric( $membership_plan_id ) && $membership_plan_id > 0 ) {

			if ( $using_permalinks ) {

				// using permalinks
				// e.g. /my-account/members-area/123/
				$url = trailingslashit( $url ) . $membership_plan_id . '/';

			} else {

				// not using permalinks
				// e.g. /?page_id=123&members_area=123
				$url = add_query_arg(
					[
						$endpoint => $membership_plan_id,
					],
					remove_query_arg( $endpoint, $url )
				);
			}

			// if unspecified, will get the first tab as set in membership plan in admin
			if ( empty( $members_area_section ) ) {

				if ( is_numeric( $membership_plan ) ) {
					$membership_plan = wc_memberships_get_membership_plan( $membership_plan_id );
				}

				if ( $membership_plan instanceof \WC_Memberships_Membership_Plan ) {

					$plan_sections        = (array) $membership_plan->get_members_area_sections();
					$available_sections   = array_intersect_key( wc_memberships_get_members_area_sections(), array_flip( $plan_sections ) );
					$members_area_section = (string) key( $available_sections );
				}
			}

			if ( ! empty( $members_area_section ) ) {

				$paged = ! empty( $paged ) ? max( absint( $paged ), 1 ) : '';

				if ( $using_permalinks ) {

					// append a trailing slash to the page number, if present
					$paged .= '' !== $paged ? '/' : '';

					// using permalinks:
					// e.g. /my-account/members-area/123/my-membership-content/2/
					$url = trailingslashit( $url ) . "{$members_area_section}/{$paged}";

				} else {

					$url_args = [ 'members_area_section' => $members_area_section ];

					if ( $paged > 0 )  {
						$url_args['members_area_section_page'] = $paged;
					}

					// not using permalinks:
					// e.g. /?page_id=123&members_area=456&members_area_section=my_membership_content&members_area_section_page=2
					$url = add_query_arg( $url_args, $url );
				}
			}
		}

		// puts back any query arg at the end of the Members Area URL
		if ( ! empty( $query_strings ) ) {

			foreach ( $query_strings as $query_string ) {

				$arg = explode( '=', $query_string );
				$url = add_query_arg( [ $arg[0] => isset( $arg[1] ) ? $arg[1] : '' ], $url );
			}
		}
	}

	return $url;
}


/**
 * Returns the Members Area action links.
 *
 * @since 1.4.0
 *
 * @param string $section members area section to display actions for
 * @param \WC_Memberships_User_Membership $user_membership the user membership the members area is for
 * @param \WC_Product|\WP_Post|object $object an object to pass to a filter hook (optional)
 * @return string action links HTML
 */
function wc_memberships_get_members_area_action_links( $section, $user_membership, $object = null ) {

	$default_actions = array();
	$object_id       = 0;

	if ( $object instanceof \WC_Product ) {
		$object_id = $object->get_id();
	} elseif ( $object instanceof \WP_Post || isset( $object->ID ) ) {
		$object_id = $object->ID;
	}

	switch ( $section ) {

		case 'my-memberships' :
		case 'my-membership-details' :

			$members_area = $user_membership->get_plan()->get_members_area_sections();

			// Renew: Show only for expired memberships that can be renewed
			if (    $user_membership->is_expired()
			     && $user_membership->can_be_renewed()
			     && current_user_can( 'wc_memberships_renew_membership', $user_membership->get_id() ) ) {

				$default_actions['renew'] = array(
					'url'  => $user_membership->get_renew_membership_url(),
					'name' => __( 'Renew', 'woocommerce-memberships' ),
				);
			}

			// Cancel: Show only for memberships that can be cancelled
			if (    $user_membership->can_be_cancelled()
			     && current_user_can( 'wc_memberships_cancel_membership', $user_membership->get_id() ) ) {

				$default_actions['cancel'] = array(
					'url'  => $user_membership->get_cancel_membership_url(),
					'name' => __( 'Cancel', 'woocommerce-memberships' ),
				);
			}

			// View: Do not show for cancelled, expired, paused memberships, or memberships without a Members Area
			if (    'my-membership-details' !== $section
			     && ( ! empty ( $members_area ) && is_array( $members_area ) )
			     && $user_membership->has_status( wc_memberships()->get_user_memberships_instance()->get_active_access_membership_statuses() ) ) {

				$sections = $user_membership->get_plan()->get_members_area_sections();

				// perhaps open the my content section, if available, or default to the first section in array
				if ( in_array( 'my-membership-content', $sections, true ) ) {
					$url_section = 'my-membership-content';
				} else {
					$url_section = current( $sections );
				}

				$default_actions['view'] = array(
					'url' => wc_memberships_get_members_area_url( $user_membership->get_plan_id(), $url_section ),
					'name' => __( 'View', 'woocommerce-memberships' ),
				);
			}

		break;

		case 'my-membership-content'   :

			if ( ! empty( $object_id ) && wc_memberships_user_can( $user_membership->get_user_id(), 'view', array( 'post' => $object_id ) ) ) {

				$default_actions['view'] = array(
					'url'  => get_permalink( $object_id ),
					'name' => __( 'View', 'woocommerce-memberships' ),
				);
			}

		break;

		case 'my-membership-products'  :
		case 'my-membership-discounts' :

			$can_view_product     = wc_memberships_user_can( $user_membership->get_user_id(), 'view',     array( 'product' => $object_id ) );
			$can_purchase_product = wc_memberships_user_can( $user_membership->get_user_id(), 'purchase', array( 'product' => $object_id ) );

			if ( $can_view_product ) {

				$default_actions['view'] = array(
					'url'  => get_permalink( $object_id ),
					'name' => __( 'View', 'woocommerce-memberships' ),
				);
			}

			if ( $can_view_product && $can_purchase_product && $object instanceof \WC_Product ) {

				$default_actions['add-to-cart'] = array(
					'url'	=> $object->add_to_cart_url(),
					'name'	=> $object->add_to_cart_text(),
				);
			}

		break;
	}

	/**
	 * Filter membership actions on My Account and Members Area pages.
	 *
	 * @since 1.4.0
	 *
	 * @param array $default_actions associative array of actions
	 * @param \WC_Memberships_User_Membership $user_membership User Membership object
	 * @param \WC_Product|\WP_Post|object $object current object where the action is run (optional)
	 */
	$actions = apply_filters( "wc_memberships_members_area_{$section}_actions", $default_actions, $user_membership, $object );

	$links = '';

	if ( ! empty( $actions ) ) {
		foreach ( $actions as $key => $action ) {
			$links .= '<a href="' . esc_url( $action['url'] ) . '" class="button ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a> ';
		}
	}

	return $links;
}


/**
 * Returns Members Area pagination links.
 *
 * @since 1.4.0
 *
 * @param false|int|\WC_Memberships_Membership_Plan $membership_plan membership plan object
 * @param string $section Members Area section
 * @param \WP_Query|\WP_Comment_Query $query current query
 * @return string HTML or empty output if query is not paged
 */
function wc_memberships_get_members_area_page_links( $membership_plan, $section, $query ) {

	$links     = '';
	$max_pages = (int) $query->max_num_pages;

	if ( $max_pages > 1 ) {

		$current_page = (int) $query->get( 'paged' );

		if ( is_numeric( $membership_plan ) ) {
			$membership_plan = wc_memberships_get_membership_plan( (int) $membership_plan );
		}

		if ( $membership_plan ) {

			$links .= '<span class="wc-memberships-members-area-pagination">';

			// page navigation entities
			$first         = '<span class="first">&#x25C4;</span>';
			$first_tooltip = __( 'First', 'woocommerce-memberships' );
			$prev          = '<span class="prev">&#x25C2;</span>';
			$prev_tooltip  = __( 'Previous', 'woocommerce-memberships' );
			$current       = ' &nbsp; <span class="current">' . $current_page . '</span> &nbsp; ';
			$next          = '<span class="next">&#x25B8;</span>';
			$next_tooltip  = __( 'Next', 'woocommerce-memberships' );
			$last          = '<span class="last">&#x25BA;</span>';
			$last_tooltip  = __( 'Last', 'woocommerce-memberships' );

			if ( 1 === $current_page ) {
				// first page, show next
				$links .= $current;
				$links .= ' <a title="' . esc_html( $next_tooltip )   . '" href="' . esc_url( wc_memberships_get_members_area_url( $membership_plan, $section, 2 ) )                . '" class="wc-memberships-members-area-page-link wc-memberships-members-area-pagination-next">' . $next . '</a> ';
				$links .= ' <a title="' . esc_html( $last_tooltip )   . '" href="' . esc_url( wc_memberships_get_members_area_url( $membership_plan, $section, $max_pages ) )             . '" class="wc-memberships-members-area-page-link wc-memberships-members-area-page-link wc-memberships-members-area-pagination-last">' . $last . '</a> ';
			} elseif ( $max_pages === $current_page ) {
				// last page, show prev
				$links .= ' <a title="' . esc_html( $first_tooltip ) . '" href="' . esc_url( wc_memberships_get_members_area_url( $membership_plan, $section, 1 ) )                 . '" class="wc-memberships-members-area-page-link wc-memberships-members-area-page-link wc-memberships-members-area-pagination-first">' . $first . '</a> ';
				$links .= ' <a title="' . esc_html( $prev_tooltip )  . '" href="' . esc_url( wc_memberships_get_members_area_url( $membership_plan, $section, $current_page - 1 ) ) . '" class="wc-memberships-members-area-page-link wc-memberships-members-area-page-link wc-memberships-members-area-pagination-prev">' . $prev . '</a> ';
				$links .= $current;
			} else {
				// in the middle of pages, show both
				$links .= ' <a title="' . esc_html( $first_tooltip ) . '" href="' . esc_url( wc_memberships_get_members_area_url( $membership_plan, $section, 1 ) )                 . '" class="wc-memberships-members-area-page-link wc-memberships-members-area-page-link wc-memberships-members-area-pagination-first">' . $first . '</a> ';
				$links .= ' <a title="' . esc_html( $prev_tooltip )  . '" href="' . esc_url( wc_memberships_get_members_area_url( $membership_plan, $section, $current_page - 1 ) ) . '" class="wc-memberships-members-area-page-link wc-memberships-members-area-page-link wc-memberships-members-area-pagination-prev">' . $prev . '</a> ';
				$links .= $current;
				$links .= ' <a title="' . esc_html( $next_tooltip )  . '" href="' . esc_url( wc_memberships_get_members_area_url( $membership_plan, $section, $current_page + 1 ) ) . '" class="wc-memberships-members-area-page-link wc-memberships-members-area-page-link wc-memberships-members-area-pagination-next">' . $next . '</a> ';
				$links .= ' <a title="' . esc_html( $last_tooltip )  . '" href="' . esc_url( wc_memberships_get_members_area_url( $membership_plan, $section, $max_pages ) )              . '" class="wc-memberships-members-area-page-linkwc-memberships-members-area-page-link wc-memberships-members-area-pagination-last">' . $last . '</a> ';
			}

			$links .= '</span>';
		}
	}

	/**
	 * Filters the members area pagination links.
	 *
	 * @since 1.9.0
	 *
	 * @param string $links HTML
	 * @param \WC_Memberships_Membership_Plan $membership_plan the plan the links are related to
	 * @param string $section the current section displayed
	 * @param \WP_Query|\WP_Comment_Query $query the current query (content or comments for notes)
	 */
	return (string) apply_filters( 'wc_memberships_members_area_pagination_links', $links, $membership_plan, $section, $query );
}


/**
 * Returns a members area sorting link.
 *
 * @since 1.9.0
 *
 * @param string $sort_key sort key (e.g. title, type for post type...)
 * @param string $sort_label label text to use
 * @return string HTML
 */
function wc_memberships_get_members_area_sorting_link( $sort_key, $sort_label ) {

	if ( $members_area = wc_memberships()->get_frontend_instance()->get_my_account_instance()->get_members_area_instance() ) {

		$sorting_link = '<span class="wc-memberships-members-area-sorting">';
		$sorting_args = $members_area->get_members_area_sorting_args();

		if ( empty( $sorting_args ) ) {

			$sorting_link .= '<span class="sort-status unsorted">';

			if ( 'title' === $sort_key ) {
				$sorting_link .= '<a class="sort-by-post-title" href="' . esc_url( add_query_arg( array( 'sort_by' => 'title', 'sort_order' => 'ASC' ) ) ) . '">' . esc_html( $sort_label ) . '</a>';
			} elseif ( 'type' === $sort_key ) {
				$sorting_link .= '<a class="sort-by-post-type" href="' . esc_url( add_query_arg( array( 'sort_by' => 'post_type', 'sort_order' => 'ASC' ) ) ) . '">' . esc_html( $sort_label ) . '</a>';
			}

			$sorting_link .= '<span class="sort-order-icon sort-asc"> &nbsp; &#x25B4;</span> ';
			$sorting_link .= '<span class="sort-order-icon sort-desc" style="display:none;"> &nbsp; &#x25BE;</span> ';
			$sorting_link .= '</span>';

		} else {

			$is_current = isset( $sorting_args['orderby'] ) && $sorting_args['orderby'] === $sort_key;
			$sort_order = isset( $sorting_args['order'] ) ? strtoupper( $sorting_args['order'] ) : 'ASC';

			if ( $is_current ) {
				$sort_url   = add_query_arg( array( 'sort_by' => $sort_key, 'sort_order' => $sort_order === 'ASC' ? 'DESC' : 'ASC' ) );
				$sort_order = strtolower( $sort_order );
				$sort_class = "sorted sort-{$sort_order}";
			} else {
				$sort_url   = add_query_arg( array( 'sort_by' => $sort_key, 'sort_order' => 'ASC' ) );
				$sort_class = 'unsorted';
			}

			$sorting_link .= '<span class="sort-status ' . $sort_class . '">';

			if ( 'title' === $sort_key ) {
				$sorting_link .= '<a class="sort-by-post-title" href="' . esc_url( $sort_url ) . '">' . esc_html( $sort_label ) . '</a>';
			} elseif ( 'type' === $sort_key ) {
				$sorting_link .= '<a class="sort-by-post-type" href="' . esc_url( $sort_url ) . '">' . esc_html( $sort_label ) . '</a>';
			}

			$sorting_link .= '<span class="sort-order-icon sort-asc"> &nbsp; &#x25B4;</span>';
			$sorting_link .= '<span class="sort-order-icon sort-desc"> &nbsp; &#x25BE;</span>';
			$sorting_link .= '</span>';
		}

		$sorting_link .= '</span>';

	} else {

		$sorting_link = $sort_label;
	}

	/**
	 * Filters a sorting link in the members area.
	 *
	 * @since 1.9.0
	 *
	 * @param string $sorting_link HTML
	 * @param string $sort_key the key to sort
	 * @param string $sort_label the label used for the sort element
	 */
	return (string) apply_filters( 'wc_memberships_members_area_sorting_link', $sorting_link, $sort_key, $sort_label );
}
