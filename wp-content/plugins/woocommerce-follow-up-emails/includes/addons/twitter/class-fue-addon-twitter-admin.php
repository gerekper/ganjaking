<?php

/**
 * FUE_Addon_Twitter_Admin class
 */
class FUE_Addon_Twitter_Admin {

	/**
	 * @var FUE_Addon_Twitter
	 */
	private $fue_twitter;

	/**
	 * Class constructor
	 *
	 * @param FUE_Addon_Twitter $fue_twitter
	 */
	public function __construct( FUE_Addon_Twitter $fue_twitter ) {
		$this->fue_twitter  = $fue_twitter;

		$this->register_hooks();
	}

	/**
	 * Register hooks
	 */
	public function register_hooks() {
		// settings
		add_action( 'fue_settings_integration', array( $this, 'settings_form' ) );
		add_action( 'fue_settings_saved', array( $this, 'save_settings' ) );

		// reset data
		add_action( 'admin_post_fue_reset_twitter', array( $this, 'reset_data' ) );

		// OAuth Callback
		add_action( 'woocommerce_api_wc_fue_twitter', array( $this, 'oauth_callback' ) );

		// Variables
		add_action( 'fue_email_variables_list', array( $this, 'twitter_variables' ) );

		// Email Form
		add_action( 'fue_email_form_scripts', array( $this, 'email_form_scripts' ) );
		add_action( 'edit_form_after_title', array( $this, 'add_twitter_content' ), 101 );
		add_filter( 'fue_wc_form_products_selector_email_types', array($this, 'register_product_selector'), 9, 3 );
		add_filter( 'fue_save_email_data', array( $this, 'save_email' ) );

		// User Profile
		add_action( 'edit_user_profile', array( $this, 'twitter_contact_method' ) );
		add_action( 'show_user_profile', array( $this, 'twitter_contact_method' ) );
		add_action( 'edit_user_profile_update', array( $this, 'format_twitter_handle' ) );
		add_action( 'personal_options_update', array( $this, 'format_twitter_handle' ) );
	}

	/**
	 * Content for the Twitter settings page
	 * @hook fue_settings_integration
	 */
	public function settings_form() {
		include FUE_TEMPLATES_DIR .'/add-ons/twitter/settings.php';
	}

	/**
	 * Save data from the settings page
	 *
	 * @hook fue_settings_save
	 * @param array $data
	 */
	public function save_settings( $data ) {
		$settings   = $this->fue_twitter->get_settings();
		$changed    = false;

		if ( $data['section'] == 'integration' ) {
			$settings['consumer_key']       = sanitize_text_field( $data['twitter_consumer_key'] );
			$settings['consumer_secret']    = sanitize_text_field( $data['twitter_consumer_secret'] );
			$settings['checkout_fields']    = empty( $data['twitter_checkout_fields'] ) ? 0 : 1;
			$settings['account_fields']     = empty( $data['twitter_account_fields'] ) ? 0 : 1;

			$changed = true;
		}

		if ( $changed ) {
			update_option( 'fue_twitter', $settings );
		}

	}

	/**
	 * Reset twitter data
	 */
	public function reset_data() {
		delete_option( 'fue_twitter' );

		wp_safe_redirect( 'admin.php?page=followup-emails-settings&tab=integration&message='. urlencode(__('Twitter data removed', 'follow_up_emails')) );
		exit;
	}

	/**
	 * Store OAuth tokens after Twitter App Authorization
	 *
	 * @throws \Abraham\TwitterOAuth\TwitterOAuthException
	 */
	public function oauth_callback() {
		$this->fue_twitter->include_files();

		$request_token  = get_transient( 'fue_twitter_request_token' );

		if ( isset($_REQUEST['oauth_token']) && $request_token['oauth_token'] !== $_REQUEST['oauth_token']) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			// Abort! Something is wrong.
			wp_die( esc_html__('The tokens do not match!', 'follow_up_emails') );
		}

		$connection = new \Abraham\TwitterOAuth\TwitterOAuth(
			$this->fue_twitter->settings['consumer_key'],
			$this->fue_twitter->settings['consumer_secret'],
			$request_token['oauth_token'],
			$request_token['oauth_token_secret']
		);

		if ( ! isset( $_REQUEST['oauth_verifier'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			wp_die( esc_html__( 'Invalid request!', 'follow_up_emails' ) );
		}

		$access_token = $connection->oauth("oauth/access_token", array("oauth_verifier" => sanitize_text_field( wp_unslash( $_REQUEST['oauth_verifier'] ) ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$this->fue_twitter->settings['access_token'] = $access_token;
		update_option( 'fue_twitter', $this->fue_twitter->settings );

		$message = urlencode( __('Successfully connected your Twitter account!', 'follow_up_emails') );
		wp_safe_redirect( admin_url('admin.php?page=followup-emails-settings&tab=integration&message='. $message) );
		exit;

	}

	/**
	 * Add twitter variables to the email form
	 *
	 * @param FUE_Email $email
	 */
	public function twitter_variables( $email ) {
		if ($email->type !== 'twitter') {
			return;
		}
		?>
		<li class="var var_twitter var_twitter_handle"><strong>{twitter_handle}</strong> <img class="help_tip" title="<?php esc_attr_e('The customer\'s Twitter handle (e.g. @johndoe)', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<li class="var var_twitter var_order_items"><strong>{order_items}</strong> <img class="help_tip" title="<?php esc_attr_e('Displays a list of purchased items.', 'follow_up_emails'); ?>" src="<?php echo esc_url( FUE_TEMPLATES_URL ); ?>/images/help.png" width="16" height="16" /></li>
		<?php
	}

	/**
	 * Enqueue JS for the email form
	 */
	public function email_form_scripts() {
		wp_enqueue_script( 'fue-form-twitter', FUE_TEMPLATES_URL .'/js/email-form-twitter.js', array('jquery'), FUE_Addon_Twitter::VERSION );
		wp_enqueue_style( 'fue-form-twitter', FUE_TEMPLATES_URL .'/email-form-twitter.css' );
	}

	/**
	 * Content for the Twitter content box
	 */
	public static function add_twitter_content() {
		global $post;

		if ( $post->post_type != 'follow_up_email' ) {
			return;
		}

		?>
		<div id="fue-twitter-content" style="display: none; margin-top: 20px;">
			<label for="twitter_content" class="fue-label"><?php esc_html_e('Twitter Message', 'follow_up_emails'); ?></label>
			<textarea name="twitter_content" id="twitter_content" rows="5" cols="80"><?php echo esc_attr( $post->post_content ); ?></textarea>
			<div id="fue-twitter-content-character-count-container">
				<div id="fue-twitter-content-character-count">
					<?php esc_html_e('Character Count:', 'follow_up_emails'); ?> <span id="fue-twitter-count">0</span> <?php esc_html_e('of 280', 'follow_up_emails'); ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Register this email type to support products and categories selector
	 *
	 * @param array $types
	 * @return array
	 */
	public function register_product_selector( $types ) {
		$types[] = 'twitter';

		return $types;
	}

	/**
	 * Save the twitter_content field as the post_content if the email type is twitter
	 *
	 * @param array $data
	 * @return array
	 */
	public function save_email( $data ) {
		if ( $data['type'] == 'twitter' && isset( $_POST['twitter_content'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Already handled before action.
			$data['message'] = sanitize_text_field( wp_unslash( $_POST['twitter_content'] ) );  // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Already handled before action.
		}

		return $data;
	}

	/**
	 * Add the Twitter handle as a user contact method
	 * that gets displayed in the user-edit/profile screen
	 *
	 * @param WP_User $user
	 */
	public function twitter_contact_method( $user ) {
		$handle = get_user_meta( $user->ID, 'twitter_handle', true );
		?>
		<h3><?php esc_html_e( 'Twitter' ); ?></h3>
		<table class="form-table">
			<tr>
				<th scope="row"><?php esc_html_e( 'Twitter Handle' ); ?></th>
				<td>
					@<input type="text" name="twitter_handle" value="<?php echo esc_attr($handle); ?>" />
				</td>
			</tr>
		</table>
		<?php
	}

	public function format_twitter_handle( $user_id ) {
		if ( ! isset( $_POST['twitter_handle'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Already handled before action.
			return;
		}

		$handle = ltrim( sanitize_text_field( wp_unslash( $_POST['twitter_handle'] ) ), '@' ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Already handled before action.
		update_user_meta( $user_id, 'twitter_handle', $handle );
	}

}
