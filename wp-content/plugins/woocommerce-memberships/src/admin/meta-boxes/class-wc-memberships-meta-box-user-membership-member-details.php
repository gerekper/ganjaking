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
 * @copyright Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * User Membership Member Details Meta Box
 *
 * @since 1.0.0
 */
class WC_Memberships_Meta_Box_User_Membership_Member_Details extends \WC_Memberships_Meta_Box {


	/**
	 * Constructor.
	 *
	 * @since 1.7.0
	 */
	public function __construct() {

		$this->id       = 'wc-memberships-user-membership-member-details';
		$this->context  = 'side';
		$this->priority = 'high';
		$this->screens  = array( 'wc_user_membership' );

		parent::__construct();
	}


	/**
	 * Returns the meta box title.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Member Details', 'woocommerce-memberships' );
	}


	/**
	 * Displays the member details meta box.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_Post $post
	 */
	public function output( \WP_Post $post ) {

		// prepare variables
		$this->post            = $post;
		$this->user_membership = $user_membership = wc_memberships_get_user_membership( $post->ID );
		$this->order           = $order           = $user_membership->get_order();
		$this->product         = $product         = $user_membership->get_product();
		$this->user            = $user            = $this->get_membership_user( $user_membership );

		// bail out if no user
		if ( ! $user ) {
			return;
		}

		/**
		 * Fires at the beginning of the member details meta box.
		 *
		 * @since 1.0.0
		 *
		 * @param int $user_id the member (user) ID
		 * @param int $user_membership_id the post id of the user membership post
		 */
		do_action( 'wc_memberships_before_user_membership_member_details', $user->ID, $user_membership->get_id() );

		echo get_avatar( $user->ID, 256 );

		?>
		<h2 class="member-name">
			<a class="edit-member" href="<?php echo esc_url( get_edit_user_link( $user->ID ) ); ?>">
				<span class="dashicons dashicons-edit"></span><?php echo esc_html( $user->display_name ); ?>
			</a>
		</h2>
		<p>
			<a href="mailto:<?php echo esc_attr( $user->user_email ); ?>" class="member-email"><?php echo esc_html( $user->user_email ); ?></a>
			<br><br>
			<?php if ( $member_since = wc_memberships()->get_user_memberships_instance()->get_user_member_since_local_date( $user->ID, 'timestamp' ) ) : ?>

				<span class="member-since">
					<?php printf(
						/* translators: Placeholder: %s - date */
						esc_html__( 'Member since %s', 'woocommerce-memberships' ),
						date_i18n( wc_date_format(), $member_since )
					); ?>
				</span>

			<?php endif; ?>
		</p>

		<address>
			<?php

			// prepare the address
			$address_parts = array(
				'first_name'  => get_user_meta( $user->ID, 'billing_first_name', true ),
				'last_name'   => get_user_meta( $user->ID, 'billing_last_name', true ),
				'company'     => get_user_meta( $user->ID, 'billing_company', true ),
				'address_1'   => get_user_meta( $user->ID, 'billing_address_1', true ),
				'address_2'   => get_user_meta( $user->ID, 'billing_address_2', true ),
				'city'        => get_user_meta( $user->ID, 'billing_city', true ),
				'state'       => get_user_meta( $user->ID, 'billing_state', true ),
				'postcode'    => get_user_meta( $user->ID, 'billing_postcode', true ),
				'country'     => get_user_meta( $user->ID, 'billing_country', true )
			);

			// format the address with WooCommerce
			$address           = apply_filters( 'woocommerce_my_account_my_address_formatted_address', $address_parts, $user->ID, 'billing' );
			$formatted_address = WC()->countries->get_formatted_address( $address );

			if ( ! $formatted_address ) {
				esc_html_e( 'User has not set up their billing address yet.', 'woocommerce-memberships' );
			} else {
				echo $formatted_address;
			}

			?>
		</address>
		<br>
		<?php

		$last_active = get_user_meta( $user->ID, 'wc_last_active', true );

		if ( is_numeric( $last_active ) ) :

			?>
			<span class="last-login">
				<?php printf(
					/* translators: Placeholder: %s last login since */
					esc_html__( 'Last login: %s ago', 'woocommerce-memberships' ),
					human_time_diff( (int) $last_active )
				); ?>
			</span>
			<?php

		endif;

		/**
		 * Fires at the end of the member detail meta box.
		 *
		 * @since 1.0.0
		 *
		 * @param int $user_id the member (user) ID
		 * @param int $user_membership_id the post id of the user membership post
		 */
		do_action( 'wc_memberships_after_user_membership_member_details', $user->ID, $user_membership->get_id() );
	}


}
