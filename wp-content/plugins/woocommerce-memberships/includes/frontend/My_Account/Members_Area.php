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
 * @copyright Copyright (c) 2014-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships\Frontend\My_Account;

use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * The Members Area handler.
 *
 * @since 1.6.0
 * @since 1.19.0 renamed from WC_Memberships_Members_Area
 */
class Members_Area {


	/** @var string the endpoint used by the Members Area */
	private $endpoint;

	/** @var bool whether the installation is using pretty permalinks (true) or query strings (false) as URL rewrite structure */
	private $using_permalinks;


	/**
	 * Members Area constructor.
	 *
	 * The Members Area lists the current customer's Memberships and content information for each Memberships they have access to on the WooCommerce My Account page.
	 *
	 * We add an endpoint to WooCommerce My Account through a 'members_area' query variable.
	 * This translates as a slug that can be customer defined, just like other slugs managed by WooCommerce core for the My Account area.
	 *
	 * Unlike other My Account endpoints, the Members Area expects more information coming from the URL (or via query strings if not using a permalink structure).
	 *
	 * Not just the User Membership needs to be passed (much like an Order ID when viewing orders) but also the Membership content to display (post content, products, discounts, membership notes are the default ones).
	 * Since the content a plan discloses access to might be very big to display in a single page we also paginate it, and we need to pass in the URL a paged number information.
	 *
	 * @see \wc_memberships_get_members_area_endpoint()
	 * @see \wc_memberships_get_members_area_query_var()
	 * @see \WC_Memberships::add_rewrite_endpoints()
	 * @see \WC_Memberships::add_query_vars()
	 *
	 * @since 1.6.0
	 */
	public function __construct() {

		$this->using_permalinks = get_option( 'permalink_structure' );
		$this->endpoint         = wc_memberships_get_members_area_endpoint();

		// add a menu item in My Account for the Memberships endpoint
		add_filter( 'woocommerce_account_menu_items',        array( $this, 'add_account_members_area_menu_item' ), 999 );
		add_filter( 'woocommerce_account_menu_item_classes', array( $this, 'adjust_account_members_area_menu_item_classes' ), 999, 2 );

		// filter the endpoint URL when customer has only a single membership
		add_filter( 'woocommerce_get_endpoint_url', array( $this, 'get_members_area_memberships_endpoint_url' ), 10, 4 );

		// renders the members area content
		add_filter( 'wc_get_template',                                array( $this, 'get_members_area_navigation_template' ), 1, 2 );
		add_action( "woocommerce_account_{$this->endpoint}_endpoint", array( $this, 'output_members_area' ) );

		// handles WordPress page title and content in Members Area sections
		add_filter( 'the_title',   array( $this, 'adjust_account_page_title' ), 40 );
		add_filter( 'the_content', array( $this, 'adjust_account_page_content' ), 40 );

		// filter the breadcrumbs in My Account area when viewing individual memberships
		add_filter( 'woocommerce_get_breadcrumb', array( $this, 'adjust_account_page_breadcrumbs' ), 100 );
	}


	/**
	 * Checks if we are on the members area endpoint.
	 *
	 * @since 1.7.4
	 *
	 * @return bool
	 */
	public function is_members_area() {
		global $wp_query;

		if ( $wp_query ) {
			if ( $this->using_permalinks ) {
				$is_endpoint_url = array_key_exists( $this->endpoint, $wp_query->query_vars ) || ! empty( $wp_query->query_vars[ $this->endpoint ] );
			} else {
				$is_endpoint_url = isset( $_GET[ $this->endpoint ] );
			}
		}

		return ! empty( $is_endpoint_url );
	}


	/**
	 * Checks if we are currently viewing a members area section.
	 *
	 * @since 1.9.0
	 *
	 * @param null|array|string $section optional: check against a specific section, an array of sections or any valid section (null)
	 * @return bool
	 */
	public function is_members_area_section( $section = null ) {

		$is_section = false;

		if ( $this->is_members_area() ) {

			$the_section = $this->get_members_area_section();

			if ( null !== $section ) {
				// check for specified section(s)
				$is_section = is_array( $section ) ? in_array( $the_section, $section, true  ) : $section === $the_section;
			} elseif ( $plan = $this->get_members_area_membership_plan() ) {
				// check if we have plan context
				$is_section = in_array( $the_section, $plan->get_members_area_sections(), true );
			} else {
				// check for more generic sections list
				$is_section = array_key_exists( $the_section, wc_memberships_get_members_area_sections() );
			}
		}

		return $is_section;
	}


	/**
	 * Returns the members area current section to display.
	 *
	 * @since 1.7.4
	 *
	 * @return string section name
	 */
	public function get_members_area_section() {

		$query_vars = $this->get_members_area_query_vars();

		return ! empty( $query_vars[1] ) ? $query_vars[1] : '';
	}


	/**
	 * Returns the members area current page.
	 *
	 * @since 1.7.4
	 *
	 * @return int page ID
	 */
	public function get_members_area_section_page() {

		$query_vars = $this->get_members_area_query_vars();

		return ! empty( $query_vars[2] ) ? max( 1, (int) $query_vars[2] ) : 1;
	}


	/**
	 * Returns the members area current membership plan ID.
	 *
	 * @since 1.7.4
	 *
	 * @return int plan ID
	 */
	public function get_members_area_membership_plan_id() {

		$query_vars = $this->get_members_area_query_vars();

		return isset( $query_vars[0] ) && is_numeric( $query_vars[0] ) ? $query_vars[0] : 0;
	}


	/**
	 * Get the members area current membership plan to display.
	 *
	 * @since 1.7.4
	 * @return false|\WC_Memberships_Membership_Plan related membership plan object
	 */
	public function get_members_area_membership_plan() {
		return wc_memberships_get_membership_plan( $this->get_members_area_membership_plan_id() );
	}


	/**
	 * Returns the user membership to display in members area.
	 *
	 * @since 1.7.4
	 *
	 * @return false|\WC_Memberships_User_Membership current user membership object
	 */
	public function get_members_area_user_membership() {
		return wc_memberships_get_user_membership( get_current_user_id(), $this->get_members_area_membership_plan_id() );
	}


	/**
	 * Returns the members area query vars.
	 *
	 * @since 1.7.4
	 *
	 * @return string[] array of members area query vars
	 */
	private function get_members_area_query_vars() {
		global $wp;

		$query_vars = array();

		if ( ! $this->using_permalinks ) {
			if ( isset( $_GET[ $this->endpoint ] ) && is_numeric( $_GET[ $this->endpoint ] ) ) {
				$query_vars[] = (int) $_GET[ $this->endpoint ];
			}
			if ( isset( $_GET['members_area_section'] ) ) {
				$query_vars[] = $_GET['members_area_section'];
			}
			if ( isset( $_GET['members_area_section_page'] ) && is_numeric( $_GET['members_area_section_page'] ) ) {
				$query_vars[] = $_GET['members_area_section_page'];
			}
		} else {
			$query_vars = ! empty( $wp->query_vars[ $this->endpoint ] ) ? explode( '/',  $wp->query_vars[ $this->endpoint ] ) : $query_vars;
		}

		return $query_vars;
	}


	/**
	 * Returns the Memberships endpoint title for the Members Area current view.
	 *
	 * @since 1.9.0
	 *
	 * @param \WC_Memberships_Membership_Plan|null $plan optional plan to form a breadcrumb when viewing an individual membership
	 * @return string unescaped label
	 */
	private function get_members_area_memberships_endpoint_title( $plan = null ) {

		if ( $this->redirect_to_single_membership() ) {
			$endpoint_title = __( 'My Membership', 'woocommerce-memberships' );
		} else {
			$endpoint_title = __( 'Memberships', 'woocommerce-memberships' );
		}

		// perhaps display the current plan name
		if ( $plan instanceof \WC_Memberships_Membership_Plan ) {
			if ( is_rtl() ) {
				$endpoint_title  = $plan->get_name() . ' &laquo; ' . $endpoint_title;
			} else {
				$endpoint_title .= ' &raquo; ' . $plan->get_name();
			}
		}

		/**
		 * Filters the "Memberships" members area title in My Account page.
		 *
		 * @since 1.9.0
		 *
		 * @param string $endpoint_title the endpoint title
		 * @param \WC_Memberships_Membership_Plan|null $plan the current membership plan or null if viewing the memberships list
		 */
		return (string) apply_filters( 'wc_memberships_my_account_memberships_title', $endpoint_title, $plan );
	}


	/**
	 * Changes the Members Area endpoint URL when redirecting to a single membership.
	 *
	 * @since 1.13.0
	 *
	 * @internal
	 *
	 * @param string $url a My Account URL
	 * @param string $endpoint a My Account endpoint
	 * @return string endpoint URL
	 */
	public function get_members_area_memberships_endpoint_url( $url, $endpoint ) {

		if ( $endpoint === wc_memberships_get_members_area_endpoint() ) {

			$user_memberships = wc_memberships_get_user_memberships();

			if ( $this->redirect_to_single_membership( $user_memberships ) ) {

				$user_membership = isset( $user_memberships[0] ) ? $user_memberships[0] : null;

				if ( $user_membership && $user_membership->is_active() ) {

					$members_area_sections = $user_membership->get_plan()->get_members_area_sections();

					// don't change the members area link if there are no sections to display
					if ( ! empty( $members_area_sections ) ) {
						$url = wc_memberships_get_members_area_url( $user_membership->get_plan() );
						$url = ! $this->using_permalinks && Framework\SV_WC_Helper::str_starts_with( $url, '&' ) ? ltrim( $url, '&' ) : untrailingslashit( $url );
					}
				}
			}
		}

		return $url;
	}


	/**
	 * Adds a My Account menu item for the Members Area.
	 *
	 * @see wc_get_account_menu_items()
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 *
	 * @param array $items associative array of custom endpoints and endpoint labels
	 * @return array
	 */
	public function add_account_members_area_menu_item( $items ) {

		if ( $this->is_members_area_section() ) {

			// if we are viewing a membership, then wipe out the My Account items to make room for the members area sections
			$items = array();

		} else {

			// we grab again the endpoint option even if not using permalinks, to check if it's emptied by the admin
			$members_area_endpoint = get_option( 'woocommerce_myaccount_members_area_endpoint', 'members-area' );

			// add new endpoint if there is at least 1 membership plan defined and the endpoint isn't blank
			if ( ! empty( $members_area_endpoint ) && wc_memberships()->get_plans_instance()->get_membership_plans_count() > 0 ) {

				$endpoint       = wc_memberships_get_members_area_endpoint();
				$endpoint_title = esc_html( $this->get_members_area_memberships_endpoint_title() );

				if ( array_key_exists( 'orders', $items ) ) {
					$items = Framework\SV_WC_Helper::array_insert_after( $items, 'orders', array( $endpoint => $endpoint_title ) );
				} else {
					$items[ $endpoint ] = $endpoint_title;
				}
			}
		}

		return $items;
	}


	/**
	 * Adjusts the CSS classes of the members area endpoints.
	 *
	 * @see wc_get_account_menu_item_classes()
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 *
	 * @param string[] $classes array of CSS classes
	 * @param string $endpoint the current endpoint
	 * @return string[] array of CSS classes (to be passed later in `sanitize_html_class()` by WC)
	 */
	public function adjust_account_members_area_menu_item_classes( $classes, $endpoint ) {

		if ( ! $this->is_members_area_section() ) {

			$members_area_endpoint = wc_memberships_get_members_area_endpoint();

			if ( $endpoint === $members_area_endpoint ) {

				$class_prefix       = 'woocommerce-MyAccount-navigation-link--';
				$members_area_class = $class_prefix . $members_area_endpoint;
				$new_classes        = array();

				foreach ( $classes as $class ) {

					if ( $class === $members_area_class ) {

						$current_section = $this->get_members_area_section();

						if ( ! empty( $current_section ) ) {
							$new_classes[] = $class_prefix . $current_section;
						} else {
							$new_classes[] = $class_prefix . 'members-area';
						}

					} else {

						$new_classes[] = $class;
					}
				}

				$classes = $new_classes;
			}
		}

		return $classes;
	}


	/**
	 * Returns the menu items for the members area currently viewed plan.
	 *
	 * @since 1.9.0
	 *
	 * @param \WC_Memberships_Membership_Plan $membership_plan
	 * @return array associative array
	 */
	public function get_members_area_navigation_items( $membership_plan ) {

		$user_memberships   = wc_memberships_get_user_memberships( get_current_user_id(), array( 'fields' => 'ids' ) );
		$memberships_count  = count( $user_memberships );
		$available_sections = wc_memberships_get_members_area_sections( $membership_plan );
		$plan_sections      = $membership_plan->get_members_area_sections();
		$menu_items         = array( 'back-to-memberships' => array(
			'url'   => 1 === $memberships_count && $this->redirect_to_single_membership( $user_memberships ) ? wc_get_account_endpoint_url( 'dashboard' ) : wc_get_account_endpoint_url( $this->endpoint ),
			/* translators: Placeholder: %s - "Back to Memberships" or "Back to Dashboard" label to return back to the memberships list or the My Account dashboard */
			'label' => sprintf( __( 'Back to %s', 'woocommerce-memberships' ), 1 === $memberships_count ? __( 'Dashboard', 'woocommerce-memberships' ) : $this->get_members_area_memberships_endpoint_title() ),
			'class' => '',
		) );

		foreach ( $available_sections as $section_id => $section_name ) {
			if ( in_array( $section_id, $plan_sections, true ) ) {

				// filters use underscores, section IDs use dashes
				$section = str_replace( '-', '_', $section_id );

				/**
				 * Filters the members area section name title.
				 *
				 * @since 1.4.0
				 *
				 * @param string $section_name the section name (e.g. "Content", "Products", "Discounts"...)
				 * @param \WC_Memberships_User_Membership $user_membership the current user membership displayed
				 */
				$section_name = apply_filters( "wc_memberships_members_area_{$section}_title", $section_name, $this->get_members_area_user_membership() );

				$menu_items[ $section_id ] = array(
					'url'   => wc_memberships_get_members_area_url( $membership_plan, $section_id ),
					'label' => $section_name,
					'class' => $this->is_members_area_section( $section_id ) ? ' is-active' : '',
				);
			}
		}

		/**
		 * Filters the members area menu items for the current plan.
		 *
		 * @since 1.9.0
		 *
		 * @param array $menu_items associative array of URLs and labels
		 * @param \WC_Memberships_Membership_Plan $membership_plan the plan the menu items are for
		 */
		return (array) apply_filters( 'wc_memberships_members_area_navigation_items', $menu_items, $membership_plan );
	}


	/**
	 * Adjusts WooCommerce template loading to replace the account navigation with members area menu items.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 *
	 * @param string $located the located template
	 * @param string $template_name the template name
	 * @return string template to load
	 */
	public function get_members_area_navigation_template( $located, $template_name ) {

		if ( 'myaccount/navigation.php' === $template_name && $this->is_members_area_section() ) {
			$located = wc_locate_template( 'myaccount/my-membership-navigation.php' );
		}

		return $located;
	}


	/**
	 * Sets the My Account page title when viewing the Members Area endpoint.
	 *
	 * If we are in "Memberships" it will display "Memberships" as the page title.
	 * If we are viewing an individual membership section, it will display "Memberships > {Section Name}" as the title.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 *
	 * @param string $title the page title
	 * @return string
	 */
	public function adjust_account_page_title( $title ) {

		if ( $this->is_members_area() && ( ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) ) {

			$title = esc_html( $this->get_members_area_memberships_endpoint_title( $this->is_members_area_section() ? $this->get_members_area_membership_plan() : null ) );

			// remember: the removal priority must match the priority when the filter was added in constructor
			remove_filter( 'the_title', array( $this, 'adjust_account_page_title' ), 40 );
		}

		return $title;
	}


	/**
	 * Filters WooCommerce My Account area breadcrumbs.
	 *
	 * @since 1.6.0
	 *
	 * @param array $crumbs WooCommerce My Account breadcrumbs
	 * @return array
	 */
	public function adjust_account_page_breadcrumbs( $crumbs ) {
		global $wp;

		// sanity check to see if we're at the right endpoint:
		if (    isset( $wp->query_vars[ $this->endpoint ] )
		     && is_array( $crumbs )
		     && is_account_page()
		     && ( count( $crumbs ) > 0 ) ) {

			// add the top-level "Memberships" endpoint link, if we're on the members area to begin with
			$crumbs[] = array( esc_html( $this->get_members_area_memberships_endpoint_title() ), wc_get_endpoint_url( $this->endpoint ) );

			// get membership data
			$current_user_id = get_current_user_id();
			$user_membership = wc_memberships_get_user_membership( $current_user_id, (int) $wp->query_vars[ $this->endpoint ] );

			// check if membership exists and the current logged in user is an active or delayed member
			if (    ( $user_membership && ( $current_user_id === $user_membership->get_user_id() ) )
			     && ( wc_memberships_is_user_active_member( $current_user_id, $user_membership->get_plan() ) || wc_memberships_is_user_delayed_member( $current_user_id, $user_membership->get_plan() ) ) ) {

				// add a link to the current membership being viewed
				$members_area_sections = wc_memberships_get_members_area_sections( $user_membership->get_plan() );
				$default_section       = in_array( 'my-membership-details', $members_area_sections, true ) ? 'my-membership-details' : current( $members_area_sections );

				$crumbs[] = array( $user_membership->get_plan()->get_name(), wc_memberships_get_members_area_url( $user_membership->get_plan(), $default_section ) );

				// add a link to the current section of the members area
				if ( $this->is_members_area_section() && ( $current_section = $this->get_members_area_section() ) && array_key_exists( $current_section, $members_area_sections ) ) {

					$crumbs[] = array( $members_area_sections[ $current_section ], wc_memberships_get_members_area_url( $user_membership->get_plan(), $current_section ) );
				}
			}
		}

		return $crumbs;
	}


	/**
	 * Returns a settings array for a membership to be used in members area output.
	 *
	 * @since 1.9.0
	 *
	 * @param \WC_Memberships_User_Membership $user_membership the membership to get settings for
	 * @return array
	 */
	private function get_members_area_user_membership_details( $user_membership ) {

		$details = array(
			'status' => array(
				'label'   => __( 'Status', 'woocommerce-memberships' ),
				'content' => wc_memberships_get_user_membership_status_name( $user_membership->get_status() ),
				'class'   => 'my-membership-detail-user-membership-status',
			),
			'start-date' => array(
				'label'   => __( 'Start Date', 'woocommerce-memberships' ),
				'content' => $user_membership->has_start_date() ? date_i18n( wc_date_format(), $user_membership->get_local_start_date( 'timestamp' ) ) : esc_html__( 'N/A', 'woocommerce-memberships' ),
				'class'   => 'my-membership-detail-user-membership-start-date',
			),
			'expires' => array(
				'label'   => __( 'Expires', 'woocommerce-memberships' ),
				'content' => $user_membership->has_end_date() ? date_i18n( wc_date_format(), $user_membership->get_local_end_date( 'timestamp' ) ) : esc_html__( 'N/A', 'woocommerce-memberships' ),
				'class'   => 'my-membership-detail-user-membership-expires',
			),
			'actions' => array(
				'label'   => __( 'Actions', 'woocommerce-memberships' ),
				'content' => wc_memberships_get_members_area_action_links( 'my-membership-details', $user_membership ),
				'class'   => 'my-membership-detail-user-membership-actions',
			),
		);

		/**
		 * Filters the members area current membership details.
		 *
		 * @since 1.9.0
		 *
		 * @param array $details associative array of settings labels and HTML content for each row
		 * @param \WC_Memberships_User_Membership $user_membership the user membership the details are for
		 */
		return apply_filters( 'wc_memberships_members_area_my_membership_details', $details, $user_membership );
	}


	/**
	 * Adjusts the HTML content of the My Account page and wraps it with a Members Area div.
	 *
	 * @internal
	 * @see \WC_Memberships_Members_Area::output_members_area()
	 *
	 * @since 1.9.0
	 *
	 * @param string $content post content HTML
	 * @return string the same HTML content wrapped in a new div to identify the members area container
	 */
	public function adjust_account_page_content( $content ) {

		if ( $this->is_members_area_section() && ( ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) ) {

			$member_id = get_current_user_id();
			$plan_id   = $this->get_members_area_membership_plan_id();

			ob_start();

			?>
			<div
				class="my-membership member-<?php echo esc_attr( $member_id ); ?>"
				id="wc-memberships-members-area"
				data-member="<?php echo esc_attr( $member_id ); ?>"
				data-membership="<?php echo esc_attr( $plan_id ); ?>">
				<?php echo $content; ?>
			</div>
			<?php

			$content = ob_get_clean();

			// remember: the removal priority must match the priority when the filter was added in constructor
			remove_filter( 'the_content', array( $this, 'adjust_account_page_content' ), 40 );
		}

		return $content;
	}


	/**
	 * Determines whether the Members Area should redirect directly to a membership plan's sections if it's the sole plan.
	 *
	 * @since 1.13.0
	 *
	 * @param null|int[]|\WC_Memberships_User_Membership[] array of memberships, otherwise it will try to get memberships count for the current user
	 * @return bool
	 */
	private function redirect_to_single_membership( $user_memberships = null ) {

		/**
		 * Filters whether to redirect directly to a membership plan's sections if the user only has one membership.
		 *
		 * @since 1.13.0
		 *
		 * @param bool $redirect default true
		 */
		$redirect = (bool) apply_filters( 'wc_memberships_my_account_redirect_to_single_membership', true );

		if ( $redirect && null === $user_memberships ) {
			$user_memberships = wc_memberships_get_user_memberships( get_current_user_id() );
		}

		return $redirect && is_array( $user_memberships ) && 1 === count( $user_memberships );
	}


	/**
	 * Renders the members area content.
	 *
	 * @internal
	 *
	 * @since 1.9.0
	 */
	public function output_members_area() {

		$the_content = '';

		if ( $this->is_members_area() ) :

			$user_id         = get_current_user_id();
			$user_membership = $this->get_members_area_user_membership();

			// check if membership exists and the current logged in user is an active or at least a delayed member.
			if (    ( $user_membership && ( $user_id === $user_membership->get_user_id() ) )
			     && ( wc_memberships_is_user_active_member( $user_id, $user_membership->get_plan() ) || wc_memberships_is_user_delayed_member( $user_id, $user_membership->get_plan() ) ) ) :

				// sections for this membership defined in admin
				$sections     = (array) $user_membership->get_plan()->get_members_area_sections();
				$members_area = array_intersect_key( wc_memberships_get_members_area_sections(), array_flip( $sections ) );

				// Members Area should have at least one section enabled
				if ( ! empty( $members_area ) ) :

					// Get the section to display, or use the first designated section as fallback:
					$section = $this->get_members_area_section();
					$section = ! empty( $section ) && array_key_exists( $section, $members_area ) ? $section : $sections[0];
					// Get a paged request for the given section:
					$paged   = $this->get_members_area_section_page();

					ob_start();

					?>
					<div
						class="my-membership-section <?php echo sanitize_html_class( $section ); ?>"
						id="wc-memberships-members-area-section"
						data-section="<?php echo esc_attr( $section ); ?>"
						data-page="<?php echo esc_attr( $paged ); ?>">
						<?php

						/**
						 * Fires before Members Area template output.
						 *
						 * @since 1.4.0
						 *
						 * @param string $section members area section
						 * @param \WC_Memberships_User_Membership $user_membership the customer membership
						 */
						do_action( 'wc_memberships_before_members_area', $section, $user_membership );

						$this->get_template( $section, array(
							'user_membership' => $user_membership,
							'user_id'         => $user_id,
							'paged'           => $paged,
						) );

						/**
						 * Fires after Members Area template output.
						 *
						 * @since 1.4.0
						 *
						 * @param string $section members area section
						 * @param \WC_Memberships_User_Membership $user_membership the customer membership
						 */
						do_action( 'wc_memberships_after_members_area', $section, $user_membership );

						?>
					</div>
					<?php

					// grab everything that was output above while processing any shortcode in between
					$the_content = do_shortcode( ob_get_clean() );

				endif;

			else :

				ob_start();

				?>
				<div class="woocommerce-account-my-memberships">
					<?php

					/**
					 * Fires before the Memberships table in My Account page.
					 *
					 * @since 1.4.0
					 */
					do_action( 'wc_memberships_before_my_memberships' );

					wc_get_template( 'myaccount/my-memberships.php', array(
						'customer_memberships' => wc_memberships_get_user_memberships(),
						'user_id'              => get_current_user_id(),
					) );

					/**
					 * Fires after the Memberships table in My Account page.
					 *
					 * @since 1.4.0
					 */
					do_action( 'wc_memberships_after_my_memberships' );

					?>
				</div>
				<?php

				$the_content = ob_get_clean();

			endif;

		endif;

		echo $the_content;
	}


	/**
	 * Returns query sorting arguments for members area content.
	 *
	 * @since 1.9.0
	 *
	 * @return array associative array compatible with `get_posts` and `WP_Query` sorting arguments
	 */
	public function get_members_area_sorting_args() {

		$args = array();

		if ( isset( $_GET['sort_by'] ) && in_array( $_GET['sort_by'], array( 'title', 'type' ), true ) ) {

			$args['orderby'] = $_GET['sort_by'];

			if ( isset( $_GET['sort_order'] ) && in_array( strtoupper( $_GET['sort_order'] ), array( 'ASC', 'DESC' ), true ) ) {
				$args['order'] = strtoupper( $_GET['sort_order'] );
			} else {
				$args['order'] = 'ASC';
			}
		}

		return $args;
	}


	/**
	 * Loads the Members Area templates.
	 *
	 * @internal
	 *
	 * @since 1.6.0
	 *
	 * @param string $section the members area section to display
	 * @param array $args array of arguments {
	 *      @type \WC_Memberships_User_Membership $user_membership user membership object (required)
	 *      @type int $user_id member ID (required)
	 *      @type int $paged optional pagination (optional)
	 * }
	 */
	public function get_template( $section, $args ) {

		// bail out: no args, no party
		if ( empty( $args['user_membership'] ) && empty( $args['user_id'] ) && ( ! $args['user_membership'] instanceof \WC_Memberships_User_Membership ) ) {
			return;
		}

		// handle optional pagination
		$paged = isset( $args['paged'] ) ? max( 1, (int) $args['paged'] ) : 1;

		// get any sorting args
		$sorting = $this->get_members_area_sorting_args();

		if ( 'my-membership-content' === $section ) {

			wc_get_template( 'myaccount/my-membership-content.php', array(
				/* @see \WC_Memberships_User_Membership */
				'customer_membership' => $args['user_membership'],
				/* @see \WC_Memberships_Membership_Plan::get_restricted_content() */
				'restricted_content'  => $args['user_membership']->get_plan()->get_restricted_content( $paged, $sorting ),
				'user_id'             => $args['user_id'],
			) );

		} elseif ( 'my-membership-products' === $section ) {

			wc_get_template( 'myaccount/my-membership-products.php', array(
				/* @see \WC_Memberships_User_Membership */
				'customer_membership' => $args['user_membership'],
				/* @see \WC_Memberships_Membership_Plan::get_restricted_products() */
				'restricted_products' => $args['user_membership']->get_plan()->get_restricted_products( $paged, $sorting ),
				'user_id'             => $args['user_id'],
			) );

		} elseif ( 'my-membership-discounts' === $section ) {

			wc_get_template( 'myaccount/my-membership-discounts.php', array(
				/* @see \WC_Memberships_User_Membership */
				'customer_membership' => $args['user_membership'],
				/* @see \WC_Memberships_Membership_Plan::get_discounted_products() */
				'discounted_products' => $args['user_membership']->get_plan()->get_discounted_products( $paged, $sorting ),
				'user_id'             => $args['user_id'],
			) );

		} elseif ( 'my-membership-notes' === $section ) {

			$dateTime = new \DateTime();
			$dateTime->setTimezone( new \DateTimeZone( wc_timezone_string() ) );
			$timezone = $dateTime->format( 'T' );

			wc_get_template( 'myaccount/my-membership-notes.php', array(
				/* @see \WC_Memberships_User_Membership */
				'customer_membership' => $args['user_membership'],
				/* @see \WC_Memberships_User_Membership::get_notes() */
				'customer_notes'      => $args['user_membership']->get_notes( 'customer', $paged ),
				'timezone'            => $timezone,
				'user_id'             => $args['user_id'],
			) );

		} elseif ( 'my-membership-details' === $section ) {

			wc_get_template( 'myaccount/my-membership-details.php', array(
				/* @see \WC_Memberships_User_Membership */
				'customer_membership' => $args['user_membership'],
				'membership_details'  => $this->get_members_area_user_membership_details( $args['user_membership'] ),
			) );

		} else {

			// allow custom sections if wc_membership_plan_members_area_sections is filtered
			$located = wc_locate_template( "myaccount/{$section}.php" );

			if ( is_readable( $located ) ) {
				wc_get_template( "myaccount/{$section}.php", $args );
			}
		}
	}


}
