<?php
/**
 * @package Polylang-WC
 */

/**
 * Associates a language to the user and to orders and manages the customer emails languages.
 *
 * @since 0.1
 */
class PLLWC_Emails {
	/**
	 * Product language data store.
	 *
	 * @var PLLWC_Order_Language_CPT
	 */
	protected $data_store;

	/**
	 * Stores previous language information each time it may be switched.
	 *
	 * @var array[] {
	 *   @type bool              $switched Has the WordPress locale been switched?
	 *   @type PLL_Language|null $language Previous current language.
	 * }
	 *
	 * @phpstan-var array<array{switched:bool, language:PLL_Language|null}>
	 */
	private $previous_languages = array();

	/**
	 * Constructor.
	 *
	 * Setups filters and actions.
	 *
	 * @since 0.1
	 */
	public function __construct() {
		$this->data_store = PLLWC_Data_Store::load( 'order_language' );

		add_action( 'change_locale', array( $this, 'change_locale' ), 1 ); // Soon to load the plugin_locale filter.

		// Deactivate the email locale switch from WooCommerce.
		add_filter( 'woocommerce_email_setup_locale', '__return_false' );
		add_filter( 'woocommerce_email_restore_locale', '__return_false' );

		// Define the customer preferred language.
		add_action( 'woocommerce_created_customer', array( $this, 'created_customer' ), 5 ); // Before WC sends the notification.
		add_action( 'woocommerce_new_order', array( $this, 'new_order' ) );

		// Manually sent order emails from order action metabox.
		add_action( 'woocommerce_before_resend_order_emails', array( $this, 'resend_order_email' ), 10, 2 );
		add_action( 'woocommerce_after_resend_order_email', array( $this, 'after_email' ) );

		// Translate site title.
		add_filter( 'woocommerce_email_format_string_replace', array( $this, 'format_string_replace' ), 10, 2 );

		// Delays the the addition of other actions after WC_Emails init.
		add_action( 'woocommerce_email', array( $this, 'mailer_init' ) );
	}

	/**
	 * Setups actions related to automatically sent emails.
	 *
	 * This is delayed after the first call to WC()->mailer() to avoid setting up
	 * emails sooner than expected.
	 *
	 * @since 1.6.3
	 *
	 * @param WC_Emails $mailer The WooCommerce emails controller.
	 * @return void
	 */
	public function mailer_init( $mailer ) {
		$this->user_emails_init();
		$this->customer_emails_init();

		/**
		 * Filters if the emails sent to shop managers must be sent in their own language.
		 *
		 * @since 1.7
		 *
		 * @param bool $enable True if emails are sent in shop manager language, false otherwise.
		 */
		if ( apply_filters( 'pllwc_enable_shop_manager_email_language', true ) ) {
			$this->shop_manager_emails_init( $mailer );
		}
	}

	/**
	 * Setups actions related to emails sent to a user.
	 *
	 * @since 1.7
	 *
	 * @return void
	 */
	protected function user_emails_init() {
		/**
		 * Filters the actions used to send translated emails to a user.
		 * The actions in the list must pass a `WP_User` or user id as first parameter.
		 *
		 * @since 1.6
		 *
		 * @param string[] $email_actions Array of actions used to send user emails.
		 */
		$actions = apply_filters(
			'pllwc_user_email_actions',
			array(
				'woocommerce_created_customer_notification', // Customer new account.
				'woocommerce_reset_password_notification', // Reset password.
			)
		);

		foreach ( $actions as $action ) {
			add_action( $action, array( $this, 'before_user_email' ), 1 ); // Switch the language for the email.
			add_action( $action, array( $this, 'after_email' ), 999 ); // Switch the language back after the email has been sent.
		}
	}

	/**
	 * Setups actions related to emails sent to a customer.
	 *
	 * @since 1.7
	 *
	 * @return void
	 */
	protected function customer_emails_init() {
		/**
		 * Filters the actions used to send translated customer emails related to an order.
		 * The actions in the list must pass a `WC_Order` or order id as first parameter.
		 *
		 * @since 1.6
		 *
		 * @param string[] $email_actions Array of actions used to send order emails.
		 */
		$actions = apply_filters(
			'pllwc_order_email_actions',
			array(
				// Completed order.
				'woocommerce_order_status_completed_notification',
				// Customer note.
				'woocommerce_new_customer_note_notification',
				// On hold.
				'woocommerce_order_status_failed_to_on-hold_notification',
				'woocommerce_order_status_pending_to_on-hold_notification',
				'woocommerce_order_status_cancelled_to_on-hold_notification',
				// Processing.
				'woocommerce_order_status_cancelled_to_processing_notification',
				'woocommerce_order_status_failed_to_processing_notification',
				'woocommerce_order_status_on-hold_to_processing_notification',
				'woocommerce_order_status_pending_to_processing_notification',
				// Refunded order.
				'woocommerce_order_fully_refunded_notification',
				'woocommerce_order_partially_refunded_notification',
			)
		);

		foreach ( $actions as $action ) {
			add_action( $action, array( $this, 'before_order_email' ), 1 ); // Switch the language for the email.
			add_action( $action, array( $this, 'after_email' ), 999 ); // Switch the language back after the email has been sent.
		}
	}

	/**
	 * Setups actions related to emails sent to shop managers.
	 *
	 * @since 1.7
	 *
	 * @param WC_Emails $mailer The WooCommerce emails controller.
	 * @return void
	 */
	protected function shop_manager_emails_init( $mailer ) {
		// Cancelled order emails sent to shop managers.
		$actions = array(
			'woocommerce_order_status_processing_to_cancelled_notification',
			'woocommerce_order_status_on-hold_to_cancelled_notification',
		);

		$email = $mailer->emails['WC_Email_Cancelled_Order'];
		foreach ( $actions as $action ) {
			remove_action( $action, array( $email, 'trigger' ) );
			add_action( $action, array( $this, 'send_cancelled_order_email' ) );
		}

		// Failed order emails sent to shop managers.
		$actions = array(
			'woocommerce_order_status_pending_to_failed_notification',
			'woocommerce_order_status_on-hold_to_failed_notification',
		);

		$email = $mailer->emails['WC_Email_Failed_Order'];
		foreach ( $actions as $action ) {
			remove_action( $action, array( $email, 'trigger' ) );
			add_action( $action, array( $this, 'send_failed_order_email' ) );
		}

		// New order emails sent to shop managers.
		$actions = array(
			'woocommerce_order_status_pending_to_processing_notification',
			'woocommerce_order_status_pending_to_completed_notification',
			'woocommerce_order_status_pending_to_on-hold_notification',
			'woocommerce_order_status_failed_to_processing_notification',
			'woocommerce_order_status_failed_to_completed_notification',
			'woocommerce_order_status_failed_to_on-hold_notification',
			'woocommerce_order_status_cancelled_to_processing_notification',
			'woocommerce_order_status_cancelled_to_completed_notification',
			'woocommerce_order_status_cancelled_to_on-hold_notification',
		);

		$email = $mailer->emails['WC_Email_New_Order'];
		foreach ( $actions as $action ) {
			remove_action( $action, array( $email, 'trigger' ) );
			add_action( $action, array( $this, 'send_new_order_email' ) );
		}
	}

	/**
	 * Set the preferred customer language at customer creation.
	 * Hooked to the action 'woocommerce_created_customer'.
	 *
	 * @since 0.1
	 *
	 * @param int $user_id User ID.
	 * @return void
	 */
	public function created_customer( $user_id ) {
		update_user_meta( $user_id, 'locale', get_locale() );
	}

	/**
	 * Maybe changes the customer language when he places a new order.
	 * The chosen language is the currently browsed language.
	 * Hooked to the action 'woocommerce_new_order'.
	 *
	 * @since 1.0
	 *
	 * @param int $order_id Order ID.
	 * @return void
	 */
	public function new_order( $order_id ) {
		if ( PLL() instanceof PLL_Frontend ) {
			$order = wc_get_order( $order_id );
			if ( $order instanceof WC_Order ) {
				$user_id = $order->get_user_id();
				if ( $user_id ) {
					$order_locale = $this->data_store->get_language( $order_id, 'locale' );
					$user_locale  = get_user_meta( $user_id, 'locale', true );
					if ( ! empty( $order_locale ) && $order_locale !== $user_locale ) {
						update_user_meta( $user_id, 'locale', $order_locale );
					}
				}
			}
		}
	}

	/**
	 * Loads the WooCommerce text domain when the locale is switched.
	 * Hooked to the action 'change_locale'.
	 *
	 * @since 1.0.2
	 *
	 * @return void
	 */
	public function change_locale() {
		if ( is_locale_switched() ) {
			if ( isset( PLL()->filters ) ) {
				remove_filter( 'locale', array( PLL()->filters, 'get_locale' ) );
				remove_filter( 'load_textdomain_mofile', array( PLL()->filters, 'load_textdomain_mofile' ) );
			}
			add_filter( 'get_user_metadata', array( $this, 'filter_user_locale' ), 10, 3 );
		} else {
			if ( PLL() instanceof PLL_Frontend && isset( PLL()->filters ) ) {
				add_filter( 'locale', array( PLL()->filters, 'get_locale' ) );
			}
			remove_filter( 'get_user_metadata', array( $this, 'filter_user_locale' ) );
		}
		WC()->load_plugin_textdomain();
	}

	/**
	 * Sets the email language.
	 *
	 * @since 0.1
	 *
	 * @param PLL_Language $language An instance of PLL_Language.
	 * @return void
	 */
	public function set_email_language( $language ) {
		$this->previous_languages[] = array(
			'switched' => switch_to_locale( $language->locale ),
			'language' => empty( PLL()->curlang ) ? null : PLL()->curlang,
		);

		PLL()->curlang = $language;

		// Translates pages ids (to translate urls if any).
		foreach ( array( 'myaccount', 'shop', 'cart', 'checkout', 'terms' ) as $page ) {
			add_filter( 'option_woocommerce_' . $page . '_page_id', 'pll_get_post' );
		}

		if ( ! is_locale_switched() ) {
			PLL()->load_strings_translations( $language->locale );
		}

		/**
		 * Fires just after the language of the email has been set.
		 *
		 * @since 0.1
		 */
		do_action( 'pllwc_email_language' );
	}

	/**
	 * Sets the email language depending on the order language.
	 * Hooked to order notifications.
	 *
	 * @since  0.1
	 *
	 * @param int|array|WC_Order $order Order or order ID.
	 * @return void
	 */
	public function before_order_email( $order ) {
		if ( is_numeric( $order ) ) {
			$order_id = $order;
		} elseif ( is_array( $order ) ) {
			$order_id = $order['order_id'];
		} elseif ( is_object( $order ) ) {
			$order_id = $order->get_id();
		}

		if ( ! empty( $order_id ) ) {
			$lang = $this->data_store->get_language( $order_id );
			$language = PLL()->model->get_language( $lang );
			if ( $language ) {
				$this->set_email_language( $language );
			}
		}
	}

	/**
	 * Sets the email language depending on the user language.
	 * Hooked to user notifications.
	 *
	 * @since 0.1
	 *
	 * @param int|string $user User ID or user login.
	 * @return void
	 */
	public function before_user_email( $user ) {
		if ( is_numeric( $user ) ) {
			$user_id = (int) $user;
		} else {
			$user = get_user_by( 'login', $user );
			if ( $user instanceof WP_User ) {
				$user_id = $user->ID;
			}
		}

		if ( isset( $user_id ) ) {
			$lang = get_user_meta( $user_id, 'locale', true );
			$lang = empty( $lang ) ? get_locale() : $lang;
			$language = PLL()->model->get_language( $lang );
			if ( $language ) {
				$this->set_email_language( $language );
			}
		}
	}

	/**
	 * Restores the current language after the email has been sent.
	 * Hooked to order and user notifications.
	 *
	 * @since 0.1
	 *
	 * @return void
	 */
	public function after_email() {
		if ( empty( $this->previous_languages ) ) {
			return;
		}

		$previous = array_pop( $this->previous_languages );

		if ( $previous['switched'] ) {
			restore_previous_locale();
		}

		PLL()->curlang = $previous['language'];

		if ( empty( PLL()->curlang ) ) {
			foreach ( array( 'myaccount', 'shop', 'cart', 'checkout', 'terms' ) as $page ) {
				remove_filter( 'option_woocommerce_' . $page . '_page_id', 'pll_get_post' );
			}
		}
	}

	/**
	 * Translate the site title which is filled before the email is triggered.
	 * Hooked to the filter 'woocommerce_email_format_string_replace'.
	 *
	 * @since 0.5
	 *
	 * @param string[] $replace Array of strings to replace placeholders in emails.
	 * @param WC_Email $email   Instance of WC_Email.
	 * @return string[]
	 */
	public function format_string_replace( $replace, $email ) {
		$replace['blogname']   = $email->get_blogname();
		$replace['site-title'] = $email->get_blogname();
		return $replace;
	}

	/**
	 * Filters the user locale. Needed when sending the email from admin.
	 *
	 * @since 1.0.3
	 *
	 * @param mixed  $value    The value get_metadata() should return.
	 * @param int    $user_id  User ID.
	 * @param string $meta_key Meta key.
	 * @return mixed The meta value.
	 */
	public function filter_user_locale( $value, $user_id, $meta_key ) {
		return 'locale' === $meta_key ? get_locale() : $value;
	}

	/**
	 * Get the user language by email.
	 *
	 * @nince 1.6
	 *
	 * @param string $email Email.
	 * @return PLL_Language The language of the user having this email, the default language if not found.
	 */
	protected function get_language_by_email( $email ) {
		$user = get_user_by( 'email', $email );

		if ( $user instanceof WP_User ) {
			$locale = get_user_meta( $user->ID, 'locale', true );
			$language = PLL()->model->get_language( $locale );
		}

		if ( empty( $language ) ) {
			// In the eventuality that the user locale is not part of a Polylang language.
			$language = pll_default_language( 'OBJECT' );
		}

		return $language;
	}

	/**
	 * Sends order email in the user's language.
	 *
	 * @since 1.6
	 *
	 * @param WC_Email $email    WooCommerce Email Class.
	 * @param int      $order_id Order id.
	 * @return void
	 */
	protected function send_order_email( $email, $order_id ) {
		if ( method_exists( $email, 'trigger' ) ) {
			$recipients = explode( ',', $email->get_recipient() );

			remove_all_filters( 'woocommerce_email_recipient_' . $email->id ); // Prevents multiple emails sent to recipients added in this filter.
			remove_filter( 'get_user_metadata', array( $this, 'filter_user_locale' ) );

			$emails_by_language = array();

			foreach ( $recipients as $recipient ) {
				$language = $this->get_language_by_email( $recipient );
				$emails_by_language[ $language->slug ]['language']     = $language;
				$emails_by_language[ $language->slug ]['recipients'][] = $recipient;
			}

			foreach ( $emails_by_language as $em ) {
				$this->set_email_language( $em['language'] );
				$email->recipient = implode( ',', $em['recipients'] );
				$email->trigger( $order_id );
				$this->after_email();
			}
		}
	}

	/**
	 * Sends cancelled order email in the user's language.
	 *
	 * @since 1.6
	 *
	 * @param int $order_id Order id.
	 * @return void
	 */
	public function send_cancelled_order_email( $order_id ) {
		$this->send_order_email( WC()->mailer()->emails['WC_Email_Cancelled_Order'], $order_id );
	}

	/**
	 * Sends failed order email in the user's language.
	 *
	 * @since 1.6
	 *
	 * @param int $order_id Order id.
	 * @return void
	 */
	public function send_failed_order_email( $order_id ) {
		$this->send_order_email( WC()->mailer()->emails['WC_Email_Failed_Order'], $order_id );
	}

	/**
	 * Sends new order email in the user's language.
	 *
	 * @since 1.6
	 *
	 * @param int $order_id Order id.
	 * @return void
	 */
	public function send_new_order_email( $order_id ) {
		add_filter( 'woocommerce_new_order_email_allows_resend', '__return_true' );
		$this->send_order_email( WC()->mailer()->emails['WC_Email_New_Order'], $order_id );
	}

	/**
	 * Handles emails sent from the order actions metabox.
	 *
	 * @since 1.6
	 *
	 * @param WC_Order $order  Order.
	 * @param string   $action Order action.
	 * @return void
	 */
	public function resend_order_email( $order, $action ) {
		if ( 'new_order' === $action ) {
			$this->send_new_order_email( $order->get_id() ); // Send email in the user's language.
			add_filter( 'woocommerce_email_enabled_new_order', '__return_false' ); // Prevents WC to resend the email.
		} else {
			$this->before_order_email( $order ); // Other emails are sent to the customer in the order's language.
		}
	}
}
