<?php
/**
 * MailChimp for WooCommerce Memberships
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
 * Do not edit or add to this file if you wish to upgrade MailChimp for WooCommerce Memberships to newer
 * versions in the future. If you wish to customize MailChimp for WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/mailchimp-for-woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2017-2020, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships\MailChimp;

use SkyVerge\WooCommerce\PluginFramework\v5_5_0 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Main front end handler.
 *
 * @since 1.0.1
 */
class Frontend {


	/**
	 * Sets up the front end.
	 *
	 * @since 1.0.1
	 */
	public function __construct() {

		// add and process an opt in checkbox at checkout if user is a member or cart contains a product that grants access
		add_action( 'woocommerce_checkout_fields',          array( $this, 'add_checkout_fields_opt_in' ) );
		add_action( 'woocommerce_checkout_update_customer', array( $this, 'set_checkout_customer_opt_in_preference' ), 10, 2 );

		// show a notice when opt in preference is not set
		add_action( 'wc_memberships_before_members_area', array( $this, 'output_members_area_opt_in_notice'), 1, 2 );
	}


	/**
	 * Adds an opt in checkbox in checkout billing fields to subscribe to the members audience.
	 *
	 * @internal
	 *
	 * @since 1.0.1
	 *
	 * @param array $checkout_fields input fields data
	 * @return array
	 */
	public function add_checkout_fields_opt_in( $checkout_fields ) {

		if ( ! empty( $checkout_fields['billing'] ) && $this->show_opt_in( 'checkout' ) ) {

			$opt_in_text = $this->get_opt_in_prompt_text();

			if ( '' !== $opt_in_text ) {

				$checkout_fields['billing']['subscribe_members_list'] = array(
					'label'    => $opt_in_text,
					'type'     => 'checkbox',
					'priority' => 115,
					'class'    => array( 'form-row-wide' ),
				);
			}
		}

		return $checkout_fields;
	}


	/**
	 * Processes the opt in preference passed at checkout.
	 *
	 * @internal
	 *
	 * @since 1.0.1
	 *
	 * @param \WC_Customer $customer the customer
	 * @param array $posted_data data from checkout form
	 */
	public function set_checkout_customer_opt_in_preference( $customer, $posted_data ) {

		if ( ! empty( $posted_data['subscribe_members_list'] ) && in_array( $posted_data['subscribe_members_list'], array( 'yes', '1', 1 ), false ) ) {

			// a sync should be triggered as soon as the membership is created as the current order is placed
			$customer->add_meta_data( '_wc_memberships_mailchimp_sync_opt_in', 'yes', true );
		}
	}


	/**
	 * Displays a notice to let members sign up to the members audience if manual opt in is required.
	 *
	 * This is shown when a preference is not set yet.
	 *
	 * @internal
	 *
	 * @since 1.0.1
	 *
	 * @param string $section the current section displayed
	 * @param \WC_Memberships_User_Membership|null $user_membership the current membership displayed
	 */
	public function output_members_area_opt_in_notice( $section, $user_membership ) {

		if ( $user_membership instanceof \WC_Memberships_User_Membership && $this->show_opt_in( $section, $user_membership ) ) {

			echo $this->get_opt_in_prompt( $user_membership->get_user_id(), array( 'woocommerce', 'woocommerce-info' ) );
		}
	}


	/**
	 * Checks whether the opt in should be shown to a member.
	 *
	 * @since 1.0.1
	 *
	 * @param string $context members area section being displayed or other context (e.g. checkout)
	 * @param null|\WC_Memberships_User_Membership $user_membership related user membership or null if in checkout context
	 * @return bool
	 */
	private function show_opt_in( $context, $user_membership = null ) {

		$show = false;

		if (    wc_memberships_mailchimp()->is_members_opt_in_mode( 'manual' )
		     && wc_memberships_mailchimp()->is_memberships_version_gte( '1.10.3' ) ) {

			$key = '_wc_memberships_mailchimp_sync_opt_in';

			if ( null === $user_membership ) {

				$show_opt_in = $opt_in_value = false;

				if ( 'checkout' === $context ) {

					$user_id = get_current_user_id();

					if ( $user_id > 0 ) {
						$opt_in_value = get_user_meta( $user_id, $key, true );
					}

					if ( 'yes' !== $opt_in_value ) {

						$show_opt_in = $user_id > 0 ? wc_memberships_is_user_member( $user_id ) : false;

						if ( ! $show_opt_in ) {
							$show_opt_in = $this->cart_contains_product_that_grants_access();
						}
					}
				}

			} else {

				$show_opt_in = 'yes' !== get_user_meta( $user_membership->get_user_id(), $key, true );
			}

			/**
			 * Filters whether to show a members audience opt in prompt.
			 *
			 * @since 1.0.1
			 *
			 * @param bool $show_opt_in whether to show the user prompt
			 * @param string $context the context where the check is performed (typically a members area section or checkout)
			 * @param null|\WC_Memberships_User_Membership the current user membership the prompt is shown for (may be null depending on context)
			 */
			$show = (bool) apply_filters( 'wc_memberships_mailchimp_show_member_opt_in_prompt', $show_opt_in, $context, $user_membership );
		}

		return $show;
	}


	/**
	 * Checks whether the cart contains products that grant access to at least one membership plan.
	 *
	 * @since 1.0.1
	 *
	 * @return bool
	 */
	private function cart_contains_product_that_grants_access() {

		$cart        = WC()->cart;
		$has_product = false;

		if ( $cart && ! empty( $cart->cart_contents ) ) {

			$plans = wc_memberships_get_membership_plans();

			if ( ! empty( $plans ) ) {

				$products = array( array() );

				foreach ( $plans as $plan ) {

					if ( $plan->has_products() ) {

						$products[] = $plan->get_product_ids();
					}
				}

				$products = call_user_func_array( 'array_merge', $products );

				if ( ! empty( $products ) ) {

					foreach ( $cart->cart_contents as $cart_content ) {

						$product = isset( $cart_content['data'] ) ? $cart_content['data'] : null;

						if ( $product instanceof \WC_Product && in_array( $product->get_id(), $products, false ) ) {

							$has_product = true;
							break;
						}
					}
				}
			}
		}

		return $has_product;
	}


	/**
	 * Returns the custom opt in prompt text.
	 *
	 * @since 1.0.1
	 *
	 * @return string may contain HTML
	 */
	private function get_opt_in_prompt_text() {

		return trim( get_option( 'wc_memberships_mailchimp_sync_members_opt_in_prompt_text', '' ) );
	}


	/**
	 * Returns the custom opt in prompt button label.
	 *
	 * @since 1.0.1
	 *
	 * @return string
	 */
	private function get_opt_in_prompt_button_label() {

		return trim( get_option( 'wc_memberships_mailchimp_sync_members_opt_in_button_text', '' ) );
	}


	/**
	 * Returns the opt in prompt to subscribe a user to the members audience.
	 *
	 * @since 1.0.1
	 *
	 * @param int $user_id the ID of the user to set preference for
	 * @param array $classes CSS classes to add to the prompt container
	 * @return string HTML
	 */
	private function get_opt_in_prompt( $user_id, $classes = array() ) {

		ob_start();

		$text = $this->get_opt_in_prompt_text();

		if ( '' !== $text ) :

			?>
			<div class="<?php echo implode( ' ', array_map( 'sanitize_html_class', $classes ) ); ?>">
				<?php echo $this->get_opt_in_action_link( $user_id ); ?>
				<?php echo wp_kses_post( do_shortcode( $text ) ); ?>
				<?php wp_nonce_field( 'wc_memberships_mailchimp_sync_member_opt_in_nonce', 'wc-memberships-mailchimp-sync-member-opt-in' ); ?>
			</div>
			<?php

			$this->add_opt_in_script();

		endif;

		return ob_get_clean();
	}


	/**
	 * Returns the opt in action link to subscribe to the members audience.
	 *
	 * @since 1.0.1
	 *
	 * @param int $user_id the user ID to set the preference for
	 * @return string HTML
	 */
	private function get_opt_in_action_link( $user_id ) {

		$label = $this->get_opt_in_prompt_button_label();

		ob_start();

		if ( '' !== $label ) :

			?>
			<a
				href="#"
				id="wc-memberships-mailchimp-sync-opt-in"
				data-user-id="<?php echo esc_attr( $user_id ); ?>"
				class="button"><?php echo esc_html( $label ); ?></a>
			<?php

		endif;

		return ob_get_clean();
	}


	/**
	 * Adds some inline JavaScript to handle the opt in preference.
	 *
	 * TODO if the front end scripts grow in size, consider moving this from inline to a dedicated file to be loaded conditionally {FN 2018-05-21}
	 *
	 * @since 1.0.1
	 */
	private function add_opt_in_script() {

		wc_enqueue_js( "
			jQuery( document ).ready( function( $ ) {
				$( '#wc-memberships-mailchimp-sync-opt-in' ).click( function ( e ) {
					e.preventDefault();
					$.post( 
						'" . admin_url( 'admin-ajax.php' ) . "', 
						{ 
							action:   'wc_memberships_mailchimp_sync_member_opt_in',
							user_id:  $( this ).data( 'user-id' ),
							security: $( '#wc-memberships-mailchimp-sync-member-opt-in' ).val()
						} 
					).done( function( response ) {
						if ( ! response || ! response.success ) {
							console.log( response );
						} else {
							location.reload();
						}
					} );
				} );
			} );
		" );
	}


}
