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
 * Memberships Shipping.
 *
 * This class is responsible for restricting free shipping to members.
 *
 * @since 1.10.0
 */
class WC_Memberships_Shipping {


	/**
	 * Memberships Shipping Handler constructor.
	 *
	 * @since 1.10.0
	 */
	public function __construct() {

		// modify free shipping settings
		add_filter( 'woocommerce_shipping_instance_form_fields_free_shipping', array( $this, 'modify_free_shipping_settings' ) );

		// adjust free shipping setting availability
		add_filter( 'woocommerce_shipping_free_shipping_is_available', array( $this, 'modify_free_shipping_availability' ), 10, 3 );

		// adjust free shipping settings styles / display
		if ( is_admin() && ! wp_doing_ajax() ) {
			add_action( 'admin_print_scripts', array( $this, 'add_admin_scripts' ) );
			add_action( 'admin_print_styles', array( $this, 'add_admin_styles' ) );
		}
	}


	/**
	 * Modifies free shipping settings to add the ability to require a membership.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param array $fields the shipping settings fields
	 * @return array updated fields
	 */
	public function modify_free_shipping_settings( $fields ) {

		if ( isset( $fields['requires']['options'] ) ) {

			$fields['requires']['options']['membership'] = __( 'An active membership', 'woocommerce-memberships' );

			$fields['allowed_membership_plans'] = array(
				'title'    => __( 'Allowed membership plans', 'woocommerce-memberships' ),
				'type'     => 'multiselect',
				'class'    => 'wc-enhanced-select',
				'css'      => 'min-width: 250px;',
				'default'  => '',
				'desc_tip' => __( 'Select plans whose members should be offered free shipping.', 'woocommerce-memberships' ),
				'options'  => $this->get_plan_list(),
				'custom_attributes' => array(
					'data-placeholder' => __( 'Choose plans', 'woocommerce-memberships' ),
				),
			);
		}

		$fields['disallowed_membership_plans'] = array(
			'title'    => __( 'Disallowed membership plans', 'woocommerce-memberships' ),
			'type'     => 'multiselect',
			'class'    => 'wc-enhanced-select',
			'css'      => 'min-width: 250px;',
			'default'  => '',
			'desc_tip' => __( 'Select plans whose members should not be offered this rate.', 'woocommerce-memberships' ),
			'options'  => $this->get_plan_list(),
			'custom_attributes' => array(
				'data-placeholder' => __( 'Choose plans', 'woocommerce-memberships' ),
			),
		);

		return $fields;
	}


	/**
	 * Determines if free shipping should be available or not based on membership criteria.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 *
	 * @param bool $available true if free shipping is available
	 * @param string[] $package shipping package data, unused
	 * @param \WC_Shipping_Method|bool $method the shipping method instance
	 * @return bool free shipping availability
	 */
	public function modify_free_shipping_availability( $available, $package, $method = false ) {

		// if we're here, it means some plugin isn't passing $method like it should since WC 3.2, log it and bail
		if ( ! $method ) {
			wc_memberships()->log( 'Cannot modify shipping availability for members since method is undefined. Backtrace: ' . print_r( wp_debug_backtrace_summary(), true ) );
		}

		$user_id = get_current_user_id();

		// ensure we're looking at a free shipping rate assigned to a zone (instance ID can't be 0) that requires a membership
		if (    $method instanceof \WC_Shipping_Free_Shipping
		     && 'yes' === $method->enabled
		     && $method->instance_id > 0 ) {

			$allowed    = $method->get_option( 'allowed_membership_plans' );
			$allowed    = ! empty( $allowed ) ? (array) $allowed : [];
			$disallowed = $method->get_option( 'disallowed_membership_plans' );
			$disallowed = ! empty( $disallowed ) ? (array) $disallowed : [];

			// first, if we need a membership, start with disabling this rate, maybe enable it
			if ( 'membership' === $method->requires ) {

				$available = false;

				foreach( $allowed as $plan_id ) {

					if ( wc_memberships_is_user_active_member( $user_id, $plan_id ) ) {
						$available = true;
						break;
					}
				}

			// otherwise, only modify availability if we should disallow some members
			} elseif ( ! empty( $disallowed ) ) {

				foreach ( $disallowed as $plan_id ) {

					if ( wc_memberships_is_user_active_member( $user_id, $plan_id ) ) {
						$available = false;
						break;
					}
				}
			}
		}

		return $available;
	}


	/**
	 * Adjusts enhanced select styles in shipping modal.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 */
	public function add_admin_styles() {

		if ( $this->is_shipping_settings() ) {

			wp_add_inline_style( 'woocommerce_admin_styles', '.wc-backbone-modal .select2-container{ max-width: 350px !important; }' );
		}
	}


	/**
	 * Adjusts free shipping settings based on whether memberships is selected or not.
	 *
	 * This is pretty ugly, but we can't hide the entire minimum amount field because the WooCommerce JS is forcing this to show,
	 * and we have no reasonable way to dequeue it since it's output with {@see wc_enqueue_js()}, so we hide the td / th as a workaround.
	 *
	 * @internal
	 *
	 * @since 1.10.0
	 */
	public function add_admin_scripts() {

		if ( $this->is_shipping_settings() ) {

			wc_enqueue_js( "
				function wcMembershipsFreeShippingHideShowFields( el ) {

					var form                 = $( el ).closest( 'form' ),
					    minAmountField       = $( '#woocommerce_free_shipping_min_amount', form ).closest( 'td' ),
					    allowedPlansField    = $( '#woocommerce_free_shipping_allowed_membership_plans', form ).closest( 'tr' ),
					    disallowedPlansField = $( '#woocommerce_free_shipping_disallowed_membership_plans', form ).closest( 'tr' );

					if ( 'membership' === $( el ).val() ) {
						allowedPlansField.show();
						disallowedPlansField.hide();
						minAmountField.hide();
						minAmountField.prev().hide();
					} else {
						allowedPlansField.hide();
						disallowedPlansField.show();
						minAmountField.show()
						minAmountField.prev().show();
					}
				}

				$( document.body ).on( 'change', '#woocommerce_free_shipping_requires', function() {

					wcMembershipsFreeShippingHideShowFields( this );
				} );

				// Change while load.
				$( '#woocommerce_free_shipping_requires' ).change();

				$( document.body ).on( 'wc_backbone_modal_loaded', function( evt, target ) {

					if ( 'wc-modal-shipping-method-settings' === target ) {

						$( document.body ).trigger( 'wc-enhanced-select-init' );
						wcMembershipsFreeShippingHideShowFields( $( '#wc-backbone-modal-dialog #woocommerce_free_shipping_requires', evt.currentTarget ) );
					}
				} );
			" );
		}
	}


	/** Helper methods ***************************************/


	/**
	 * Helper to return an array of membership plans.
	 *
	 * @since 1.10.0
	 *
	 * @return string[] array of plans as plan_id => plan_name
	 */
	private function get_plan_list() {

		$all_plans = wc_memberships_get_membership_plans();
		$plans     = array();

		foreach ( $all_plans as $id ) {

			$plan = wc_memberships_get_membership_plan( $id );

			if ( $plan ) {
				$plans[ $plan->get_id() ] = $plan->get_name();
			}
		}

		return $plans;
	}


	/**
	 * Helper to determine if we're on the shipping settings pages.
	 *
	 * @since 1.10.0
	 */
	private function is_shipping_settings() {

		$current_page = isset( $_GET['page'] ) ? $_GET['page'] : '';
		$current_tab  = isset( $_GET['tab'] )  ? $_GET['tab']  : '';

		return 'wc-settings' === $current_page && 'shipping' === $current_tab;
	}


}
