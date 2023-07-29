<?php
/**
 * Class to customize the 'My Account' page.
 *
 * @package WC_Newsletter_Subscription
 * @since   4.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Newsletter_Subscription_My_Account.
 */
class WC_Newsletter_Subscription_My_Account {

	/**
	 * Constructor.
	 *
	 * @since 4.0.0
	 */
	public function __construct() {
		add_filter( 'woocommerce_get_query_vars', array( $this, 'query_vars' ) );
		add_filter( 'woocommerce_account_menu_items', array( $this, 'menu_items' ) );
		add_filter( 'woocommerce_endpoint_newsletter_title', array( $this, 'newsletter_title' ) );
		add_action( 'woocommerce_account_newsletter_endpoint', array( $this, 'newsletter_content' ) );
		add_action( 'template_redirect', array( $this, 'save_newsletter_form' ) );
	}

	/**
	 * Registers custom query vars.
	 *
	 * @since 4.0.0
	 *
	 * @param array $query_vars The query vars.
	 * @return array
	 */
	public function query_vars( $query_vars ) {
		$query_vars['newsletter'] = 'newsletter';

		return $query_vars;
	}

	/**
	 * Can the customer manage its newsletter subscription?
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	protected function can_manage_subscription() {
		if ( 'yes' !== get_option( 'woocommerce_newsletter_my_account_manage_subscription', 'yes' ) ) {
			return false;
		}

		$provider = wc_newsletter_subscription_get_provider();

		if ( ! $provider || ! $provider->supports( 'manage_subscription' ) || ! wc_newsletter_subscription_provider_has_list( $provider ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Adds new items to the 'My Account' menu.
	 *
	 * @since 4.0.0
	 *
	 * @param array $menu_items Menu items.
	 * @return array
	 */
	public function menu_items( $menu_items ) {
		if ( ! $this->can_manage_subscription() ) {
			return $menu_items;
		}

		// Add the new item after the 'Edit account' item or before the 'Logout' item.
		$position = count( $menu_items ) - 1;

		/**
		 * Filters the position of the 'Newsletter Subscription' item in the 'My Account' menu.
		 *
		 * @since 4.0.0
		 *
		 * @param int   $position   The menu item position.
		 * @param array $menu_items The menu items.
		 */
		$position = apply_filters( 'wc_newsletter_subscription_my_account_menu_item_position', $position, $menu_items );

		return array_merge(
			array_slice( $menu_items, 0, $position ),
			array(
				'newsletter' => _x( 'Newsletter', 'my account: menu item', 'woocommerce-subscribe-to-newsletter' ),
			),
			array_slice( $menu_items, $position )
		);
	}

	/**
	 * Filters the 'newsletter' endpoint title.
	 *
	 * @since 4.0.0
	 *
	 * @return string.
	 */
	public function newsletter_title() {
		return _x( 'Newsletter', 'my account: page title', 'woocommerce-subscribe-to-newsletter' );
	}

	/**
	 * Outputs the 'newsletter' endpoint content.
	 *
	 * @since 4.0.0
	 */
	public function newsletter_content() {
		if ( ! $this->can_manage_subscription() || ! get_current_user_id() ) {
			return;
		}

		$user = wp_get_current_user();

		$fields = array(
			'subscribe_to_newsletter' => array(
				'type'        => 'checkbox',
				'label'       => wc_newsletter_subscription_get_checkbox_label(),
				'default'     => intval( 'checked' === get_option( 'woocommerce_newsletter_checkbox_status' ) ),
				'class'       => array( 'woocommerce-form-row', 'woocommerce-form-row--wide' ),
				'label_class' => array( 'woocommerce-form__label', 'woocommerce-form__label-for-checkbox' ),
				'input_class' => array( 'woocommerce-form__input', 'woocommerce-form__input-checkbox' ),
				'value'       => wc_newsletter_subscription_is_subscribed( $user->user_email ),
			),
		);

		wc_newsletter_subscription_get_template( 'myaccount/newsletter-subscription.php', compact( 'fields' ) );
	}

	/**
	 * Saves the newsletter form.
	 *
	 * @since 4.0.0
	 */
	public function save_newsletter_form() {
		if ( empty( $_POST['action'] ) || 'save_newsletter_subscription' !== $_POST['action'] || ! get_current_user_id() ) {
			return;
		}

		$nonce = ( isset( $_POST['save-newsletter-subscription-nonce'] ) ? wc_clean( wp_unslash( $_POST['save-newsletter-subscription-nonce'] ) ) : '' );

		if ( ! wp_verify_nonce( $nonce, 'save_newsletter_subscription' ) ) {
			return;
		}

		$user       = wp_get_current_user();
		$user_email = $user->user_email;

		$new_status     = ! empty( $_POST['subscribe_to_newsletter'] );
		$current_status = wc_newsletter_subscription_is_subscribed( $user_email );

		if ( $new_status !== $current_status ) {
			if ( $new_status ) {
				$result = wc_newsletter_subscription_subscribe(
					$user_email,
					array(
						'first_name' => $user->first_name,
						'last_name'  => $user->last_name,
					)
				);
			} else {
				$result = wc_newsletter_subscription_unsubscribe( $user_email );
			}

			if ( ! $result ) {
				wc_add_notice( __( 'There was an error changing your newsletter subscription preferences.', 'woocommerce-subscribe-to-newsletter' ), 'error' );
				return;
			}
		}

		wc_add_notice( __( 'Newsletter subscription preferences changed successfully.', 'woocommerce-subscribe-to-newsletter' ) );
	}

}
return new WC_Newsletter_Subscription_My_Account();
