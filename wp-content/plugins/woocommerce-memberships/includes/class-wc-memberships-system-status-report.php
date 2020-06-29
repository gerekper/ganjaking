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

namespace SkyVerge\WooCommerce\Memberships;

use SkyVerge\WooCommerce\PluginFramework\v5_7_1 as Framework;

defined( 'ABSPATH' ) or exit;


/**
 * Memberships System Status Report.
 *
 * @since 1.12.0
 */
class System_Status_Report {


	/**
	 * Gets the system status report data for the system status page and API response.
	 *
	 * @see \WC_Memberships_Admin::add_system_status_report_block()
	 * @see \SkyVerge\WooCommerce\Memberships\REST_API::get_system_status_data()
	 *
	 * @since 1.12.0
	 *
	 * @return array
	 */
	public static function get_system_status_report_data() {

		$data = array(
			'Restriction Mode'    => self::get_restriction_data(),
			'Public Content'      => self::get_public_content_data(),
			'Discount Exclusions' => self::get_products_excluded_from_discounts(),
		);

		$plans = wc_memberships_get_membership_plans( array( 'post_status' => 'any' ) );
		$count = count( $plans );
		$html  = '';

		$data['Membership Plans'] = array(
			'label' => __( 'Membership plans', 'woocommerce-memberships' ),
			'help'  => __( 'Membership plans limit access to content or products and apply perks such as discounts to members.', 'woocommerce-memberships' ),
			'value' => array(
				'count' => array(),
				'info'  => array(),
			),
		);

		if ( $count > 0 ) {

			$is_admin = is_admin();
			$statuses = array();

			foreach ( $plans as $plan ) {

				if ( ! isset( $statuses[ $plan->post->post_status ] ) ) {
					$statuses[ $plan->post->post_status ] = 0;
				}

				$statuses[ $plan->post->post_status ]++;

				$plan_info = self::get_membership_plan_data( $plan );

				// data condensed for the admin page:
				if ( $is_admin ) {

					// this is the export key: does not need to be translatable
					$data[ sprintf( 'Membership Plan: %s;', esc_html( $plan->get_slug() ) ) ] = $plan_info;

				// data formatted for the API response:
				} else {

					$data['Membership Plans']['value']['info'][ $plan->get_id() ] = array(
						'name'          => $plan->get_name(),
						'members_count' => $plan_info['value']['members_count']['value'],
						'access_method' => $plan_info['value']['access_method']['value'],
						'access_length' => $plan_info['value']['access_length']['value'],
						'warnings'      => $plan_info['value']['warnings'],
						'errors'        => $plan_info['value']['errors'],
					);
				}
			}

			$published = isset( $statuses['publish'] ) ? $statuses['publish'] : 0;
			$html     .= $published === $count ? $count : sprintf( _n( '%1$d (%2$d published)', '%1$d (%2$d published)', 'woocommerce-memberships', $published ), $count, $published );

			if ( 0 === $published ) {

				ob_start();

				?>
				<mark class="error"><span class="dashicons dashicons-warning"></span> <?php esc_html_e( 'No published plans', 'woocommerce-memberships' ); ?></mark>
				<?php

				$html .= ob_get_clean();

			}

			$data['Membership Plans']['value']['count'] = $statuses;

		} else {

			ob_start();

			?>
			<mark class="error"><span class="dashicons dashicons-warning"></span> <?php esc_html_e( 'No published plans', 'woocommerce-memberships' ); ?></mark>
			<?php

			$html .= ob_get_clean();
		}

		$data['Membership Plans']['html'] = $html;

		/**
		 * Filters the system status report data.
		 *
		 * @since 1.12.0
		 *
		 * @param array $data associative array of data used in API responses and status page
		 */
		return (array) apply_filters( 'wc_memberships_get_system_status_data', $data );
	}


	/**
	 * Gets the restriction mode information to be displayed in the status report.
	 *
	 * @since 1.12.0
	 *
	 * @return array
	 */
	private static function get_restriction_data() {

		$handler = wc_memberships()->get_restrictions_instance();
		$modes   = $handler->get_restriction_modes( true );
		$mode    = $handler->get_restriction_mode();

		ob_start();

		?>
		<?php echo esc_html( $modes[ $mode ] ); ?>
		<?php

		$html = ob_get_clean();

		return array(
			'label' => __( 'Restriction mode', 'woocommerce-memberships' ),
			'help'  => __( 'Determines how restricted content is presented to non-members.', 'woocommerce-memberships' ),
			'value' => $mode,
			'html'  => $html,
		);
	}


	/**
	 * Gets Membership Plan data.
	 *
	 * @since 1.12.0
	 *
	 * @param \WC_Memberships_Membership_Plan $plan membership plan object
	 * @return array
	 */
	private static function get_membership_plan_data( \WC_Memberships_Membership_Plan $plan ) {

		$html = '';
		$data = $errors = $warnings = array();

		// special keys used in API response only to rationalize errors and warnings
		$data['warnings'] = array();
		$data['errors']   = array();

		// members count
		$members_count         = $plan->get_memberships_count();
		$data['members_count'] = array(
			'label' => __( 'Members', 'woocommerce-memberships' ),
			'value' => $members_count,
			'html'  => $members_count,
		);

		// access method
		$methods       = wc_memberships()->get_plans_instance()->get_membership_plans_access_methods( true );
		$method_slug   = $plan->get_access_method();
		$access_method = isset( $methods[ $method_slug ] ) ? $methods[ $method_slug ] : $methods['manual-only'];

		$data['access_method'] = array(
			'label' => __( 'Access method', 'woocommerce-memberships' ),
			'value' => $method_slug,
			'html'  => $access_method,
		);

		// verify that there are products to grant access and they are purchasable
		if ( $plan->is_access_method( 'purchase' ) ) {

			$purchasable = false;
			$products    = $plan->get_products();

			foreach ( $products as $product ) {

				if ( $product->is_purchasable() && ! wc_memberships_is_product_purchasing_restricted( $product ) ) {

					$purchasable = true;
					break;
				}
			}

			// technically the membership can still be assigned manually, but this could be an oversight
			if ( ! $purchasable ) {

				$errors[] = __( 'No access-granting products are purchasable', 'woocommerce-memberships' );

				$data['errors'][] = 'no_purchasable_access_granting_product';
			}
		}

		// access length
		$lengths       = wc_memberships()->get_plans_instance()->get_membership_plans_access_length_types( true );
		$length_slug   = $plan->get_access_length_type();
		$access_length = sprintf( '%1$s %2$s', isset( $lengths[ $length_slug ] ) ? $lengths[ $length_slug ] : $lengths['unlimited'], 'unlimited' !== $length_slug ? '(' . $plan->get_access_length() . ')' : '' );

		$data['access_length'] = array(
			'label' => __( 'Access length', 'woocommerce-memberships' ),
			'value' => $length_slug,
			'html'  => $access_length,
		);

		/**
		 * Filters Membership Plan data for the system status report block.
		 *
		 * @since 1.12.0
		 *
		 * @param array $data associative array of labels (keys) and membership plan data (values)
		 * @param $plan \WC_Memberships_Membership_Plan $plan membership plan object
		 */
		$data = (array) apply_filters( 'wc_memberships_get_system_status_membership_plan_data', $data, $plan );

		if ( ! empty( $data ) ) {

			$rules = $plan->get_rules();

			$total_discounts = $inactive_discounts = 0;

			foreach ( $rules as $rule ) {

				// if a restriction rule targets all posts or products, we will highlight this item in the report
				if ( ! $rule->is_type( 'purchasing_discount' ) ) {

					if (      $rule->is_content_type( 'post_type' )
				         && ! $rule->has_object_ids()
				         &&   ( $post_type = $rule->get_content_type_labels() ) ) {

						$warnings[] = sprintf(
							/* translators: Placeholder: %s - post type name (plural, e.g. "posts") */
							__( 'The plan contains a rule to restrict all %s to non-members', 'woocommerce-memberships' ),
							$post_type->name
						);

						$data['warnings'][] = 'has_blanket_post_type_restriction';
					}

				} else {

					$total_discounts++;

					if ( ! $rule->is_active() ) {
						$inactive_discounts++;
					}
				}
			}

			// if the plan contains discount rules, but they're all inactive, highlight this item in the report
			if ( $total_discounts > 0 && $inactive_discounts === $total_discounts ) {

				$warnings[] = __( 'The plan has purchasing discounts, but none are active', 'woocommerce-memberships' );

				$data['warnings'][] = 'has_only_inactive_discounts';
			}

			// format HTML data when set
			foreach ( $data as $item ) {

				if ( isset( $item['label'], $item['html'] ) ) {

					$html .= sprintf( '%1$s: %2$s<br/>', $item['label'], $item['html'] );
				}
			}

			// append errors in HTML only
			foreach ( $errors as $error ) {
				$html .= '<mark class="error"><span class="dashicons dashicons-no"></span> ' . esc_html( $error ) . '</mark><br/>';
			}

			// append warnings in HTML only
			foreach ( $warnings as $warning ) {
				$html .= '<mark class="warning"><span class="dashicons dashicons-warning"></span> ' . esc_html( $warning ) . '</mark><br/>';
			}
		}

		// format the label
		$post_status_label = ''; // omit for published plans

		if ( 'publish' !== $plan->post->post_status ) {
			$post_status       = get_post_status_object( $plan->post->post_status );
			$post_status_label = ' (' . ( $post_status && ! empty( $post_status->label ) ? $post_status->label : __( 'Not published', 'woocommerce-memberships' ) ) . ')';
		}

		$label = sprintf( '%1$s%2$s', esc_html( $plan->get_name() ), strtolower( $post_status_label ) );

		return array(
			'label' => $label,
 			'value' => $data,
			'html'  => $html,
		);
	}


	/**
	 * Gets the items count per post type of content marked to be public regardless of plan rules.
	 *
	 * @since 1.12.0
	 *
	 * @return array data
	 */
	private static function get_public_content_data() {

		$public_content = wc_memberships()->get_restrictions_instance()->get_public_posts();
		$html           = '';
		$data           = array(
			'total'      => 0,
			'post_types' => array(),
		);

		if ( ! empty( $public_content ) ) {

			foreach ( $public_content as $post_type => $post_ids ) {

				if ( $post_type = get_post_type_object( $post_type ) ) {

					$count = count( $post_ids );

					// data for API response
					$data['total'] += $count;
					$data['post_types'][ $post_type->name ] =  $count;

					/* translator: Placeholder: %d - posts count */
					$items = sprintf( _n( '%d item', '%d items', $count, 'woocommerce-memberships' ), $count );
					$html .= sprintf( '%1$s: %2$s<br />', esc_html( ucfirst( $post_type->label ) ), $items );
				}
			}

		} else {

			$html .= '&ndash;';
		}

		return array(
			'label' => __( 'Public content', 'woocommerce-memberships' ),
			'help'  => __( 'Content (posts, pages, products) may be excluded from restrictions and will always be public.', 'woocommerce-memberships' ),
			'value' => $data,
			'html'  => $html,
		);
	}


	/**
	 * Gets the count for products marked to be excluded from discounts regardless of plan rules.
	 *
	 * @since 1.12.0
	 *
	 * @return array data
	 */
	private static function get_products_excluded_from_discounts() {

		$products = wc_memberships()->get_member_discounts_instance()->get_products_excluded_from_member_discounts();
		$items    = count( $products );

		if ( $items > 0 ) {
			/* translator: Placeholder: %d - products count */
			$html = sprintf( _n( '%d product', '%d products', $items, 'woocommerce-memberships' ), $items );
		} else {
			$html = '&ndash;';
		}

		return array(
			'label' => __( 'Excluded from discounts', 'woocommerce-memberships' ),
			'help'  => __( 'Products may be excluded from member discounts.', 'woocommerce-memberships' ),
			'value' => array( 'total' => $items ),
			'html'  => $html,
		);
	}


}
