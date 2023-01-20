<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Handles license input and validation
 */
class BetterDocs_Licensing {
	private $product_slug;
	private $text_domain;
	private $product_name;
	private $item_id;

	/**
	 * Initializes the license manager client.
	 */
	public function __construct( $product_slug, $product_name, $text_domain ) {
		// Store setup data
		$this->product_slug         = $product_slug;
		$this->text_domain          = $text_domain;
		$this->product_name         = $product_name;
		$this->item_id              = BETTERDOCS_PRO_SL_ITEM_ID;

		// Init
		$this->add_actions();
	}
	/**
	 * Adds actions required for class functionality
	 */
	public function add_actions() {
		if ( is_admin() ) {
			// Add the menu screen for inserting license information
			add_action( 'admin_init', array( $this, 'register_license_settings' ) );
			add_action( 'admin_init', array( $this, 'activate_license' ) );
			add_action( 'admin_init', array( $this, 'deactivate_license' ) );
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
			add_action( 'betterdocs_licensing', array( $this, 'render_licenses_page' ) );
		}
	}

	/**
	 * @return string   The slug id of the licenses settings page.
	 */
	protected function get_settings_page_slug() {
		return 'betterdocs-settings';
	}

	/**
	 * Creates the settings fields needed for the license settings menu.
	 */
	public function register_license_settings() {
		// creates our settings in the options table
		register_setting( $this->get_settings_page_slug(), $this->product_slug . '-license-key', 'sanitize_license' );
	}

	public function sanitize_license( $new ) {
		$old = get_option( $this->product_slug . '-license-key' );
		if ( $old && $old != $new ) {
			delete_option( $this->product_slug . '-license-status' ); // new license has been entered, so must reactivate
		}
		return $new;
	}

	/**
	* Handles admin notices for errors and license activation
	*
	* @since 0.1.0
	*/

	public function admin_notices() {
		$status = $this->get_license_status();
		$license_data = $this->get_license_data();

		if( isset( $license_data->license ) ) {
			$status = $license_data->license;
		}

		if( $status === 'http_error' ) {
			return;
		}

		if ( ( $status === false || $status !== 'valid' ) && $status !== 'expired' ) {
			$msg = __( 'Please %1$sactivate your license%2$s key to enable updates for %3$s.', $this->text_domain );
			$msg = sprintf( $msg, '<a href="' . admin_url( 'admin.php?page=' . $this->get_settings_page_slug() ) . '#go_license_tab">', '</a>',	'<strong>' . $this->product_name . '</strong>' );
			?>
			<div class="notice notice-error">
				<p><?php echo $msg; ?></p>
			</div>
		<?php
		}		   
		if ( $status === 'expired' ) {
			$msg = __( 'Your license has been expired. Please %1$srenew your license%2$s key to enable updates for %3$s.',	$this->text_domain );
			$msg = sprintf( $msg, '<a rel="nofollow" href="https://wpdeveloper.com/account">', '</a>', '<strong>' . $this->product_name . '</strong>' );
			?>
			<div class="notice notice-error">
				<p><?php echo $msg; ?></p>
			</div>
		<?php
		}
		if ( ( isset( $_GET['sl_activation'] ) || isset( $_GET['sl_deactivation'] ) ) && ! empty( $_GET['message'] ) ) {
			$target = isset( $_GET['sl_activation'] ) ? $_GET['sl_activation'] : null;
			$target = is_null( $target ) ? ( isset( $_GET['sl_deactivation'] ) ? $_GET['sl_deactivation'] : null ) : null;
			switch( $target ) {
				case 'false':
					$message = urldecode( $_GET['message'] );
					?>
					<div class="error">
						<p><?php echo $message; ?></p>
					</div>
					<?php
					break;
				case 'true':
				default:
				   // Developers can put a custom success message here for when activation is successful if they way.
					break;

			}
		}
	}

	/**
	 * Renders the settings page for entering license information.
	 */
	public function render_licenses_page() {
		$license_key 	= $this->get_license_key();
		$status 		= $this->get_license_status();
		$title 			= sprintf( __( '%s License', $this->text_domain ), $this->product_name );
		?>
		<div class="betterdocs-license-wrapper">
			<form method="post" action="options.php" id="betterdocs-license-form">

				<?php settings_fields( $this->get_settings_page_slug() ); ?>

      				<?php if ( $status == false || $status !== 'valid' ) : ?>
	      				<div class="betterdocs-lockscreen">
	      				<div class="betterdocs-lockscreen-icons">
							<svg height="64px" version="1.1" viewBox="0 0 32 32" width="64px" xmlns="http://www.w3.org/2000/svg" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns" xmlns:xlink="http://www.w3.org/1999/xlink"><title/><desc/><defs/><g fill="none" fill-rule="evenodd" id="Page-1" stroke="none" stroke-width="1"><g fill="#e74c3c" id="icon-114-lock"><path d="M16,21.9146472 L16,24.5089948 C16,24.7801695 16.2319336,25 16.5,25 C16.7761424,25 17,24.7721195 17,24.5089948 L17,21.9146472 C17.5825962,21.708729 18,21.1531095 18,20.5 C18,19.6715728 17.3284272,19 16.5,19 C15.6715728,19 15,19.6715728 15,20.5 C15,21.1531095 15.4174038,21.708729 16,21.9146472 L16,21.9146472 L16,21.9146472 Z M15,22.5001831 L15,24.4983244 C15,25.3276769 15.6657972,26 16.5,26 C17.3284271,26 18,25.3288106 18,24.4983244 L18,22.5001831 C18.6072234,22.04408 19,21.317909 19,20.5 C19,19.1192881 17.8807119,18 16.5,18 C15.1192881,18 14,19.1192881 14,20.5 C14,21.317909 14.3927766,22.04408 15,22.5001831 L15,22.5001831 L15,22.5001831 Z M9,14.0000125 L9,10.499235 C9,6.35670485 12.3578644,3 16.5,3 C20.6337072,3 24,6.35752188 24,10.499235 L24,14.0000125 C25.6591471,14.0047488 27,15.3503174 27,17.0094776 L27,26.9905224 C27,28.6633689 25.6529197,30 23.991212,30 L9.00878799,30 C7.34559019,30 6,28.652611 6,26.9905224 L6,17.0094776 C6,15.339581 7.34233349,14.0047152 9,14.0000125 L9,14.0000125 L9,14.0000125 Z M10,14 L10,10.4934269 C10,6.90817171 12.9101491,4 16.5,4 C20.0825462,4 23,6.90720623 23,10.4934269 L23,14 L22,14 L22,10.5090731 C22,7.46649603 19.5313853,5 16.5,5 C13.4624339,5 11,7.46140289 11,10.5090731 L11,14 L10,14 L10,14 Z M12,14 L12,10.5008537 C12,8.0092478 14.0147186,6 16.5,6 C18.9802243,6 21,8.01510082 21,10.5008537 L21,14 L12,14 L12,14 L12,14 Z M8.99742191,15 C7.89427625,15 7,15.8970601 7,17.0058587 L7,26.9941413 C7,28.1019465 7.89092539,29 8.99742191,29 L24.0025781,29 C25.1057238,29 26,28.1029399 26,26.9941413 L26,17.0058587 C26,15.8980535 25.1090746,15 24.0025781,15 L8.99742191,15 L8.99742191,15 Z" id="lock"/></g></g></svg>

							<svg enable-background="new 0 0 32 32" height="64px" id="arrow-right" version="1.1" viewBox="0 0 32 32" width="64px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="M1.06,29.897c0.011,0,0.023,0,0.034-0.001c0.506-0.017,0.825-0.409,0.868-0.913  c0.034-0.371,1.03-9.347,15.039-9.337l0.031,5.739c0,0.387,0.223,0.739,0.573,0.904c0.347,0.166,0.764,0.115,1.061-0.132  l12.968-10.743c0.232-0.19,0.366-0.475,0.365-0.774c-0.001-0.3-0.136-0.584-0.368-0.773L18.664,3.224  c-0.299-0.244-0.712-0.291-1.06-0.128c-0.349,0.166-0.571,0.518-0.571,0.903l-0.031,5.613c-5.812,0.185-10.312,2.054-13.23,5.468  c-4.748,5.556-3.688,13.63-3.639,13.966C0.207,29.536,0.566,29.897,1.06,29.897z M18.032,17.63c-0.001,0-0.002,0-0.002,0  C8.023,17.636,4.199,21.015,2.016,23.999c0.319-2.391,1.252-5.272,3.281-7.626c2.698-3.128,7.045-4.776,12.735-4.776  c0.553,0,1-0.447,1-1V6.104l10.389,8.542l-10.389,8.622V18.63c0-0.266-0.105-0.521-0.294-0.708  C18.551,17.735,18.297,17.63,18.032,17.63z" fill="#888" id="Arrow_Right_2_"/><g/><g/><g/><g/><g/><g/></svg>

							<svg height="64px" version="1.1" viewBox="0 0 32 32" width="64px" xmlns="http://www.w3.org/2000/svg" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns" xmlns:xlink="http://www.w3.org/1999/xlink"><title/><desc/><defs/><g fill="none" fill-rule="evenodd" id="Page-1" stroke="none" stroke-width="1"><g fill="#157EFB" id="icon-24-key"><path d="M18.5324038,19.4675962 L14,24 L11,24 L11,27 L8,27 L8,30 L3,30 L3,25 L13.5324038,14.4675962 C13.1881566,13.5437212 13,12.5438338 13,11.5 C13,6.80557939 16.8055794,3 21.5,3 C26.1944206,3 30,6.80557939 30,11.5 C30,16.1944206 26.1944206,20 21.5,20 C20.4561662,20 19.4562788,19.8118434 18.5324038,19.4675962 L18.5324038,19.4675962 L18.5324038,19.4675962 Z M13.9987625,15.5012375 L4,25.5 L4,29 L7,29 L7,26 L10,26 L10,23 L13.5,23 L17.4987625,19.0012375 C16.0139957,18.2075914 14.7924086,16.9860043 13.9987625,15.5012375 L13.9987625,15.5012375 L13.9987625,15.5012375 Z M29,11.5 C29,7.35786417 25.6421358,4 21.5,4 C17.3578642,4 14,7.35786417 14,11.5 C14,15.6421358 17.3578642,19 21.5,19 C25.6421358,19 29,15.6421358 29,11.5 L29,11.5 L29,11.5 Z M27,9 C27,7.34314567 25.6568543,6 24,6 C22.3431457,6 21,7.34314567 21,9 C21,10.6568543 22.3431457,12 24,12 C25.6568543,12 27,10.6568543 27,9 L27,9 L27,9 Z M26,9 C26,7.89543045 25.1045696,7 24,7 C22.8954304,7 22,7.89543045 22,9 C22,10.1045696 22.8954304,11 24,11 C25.1045696,11 26,10.1045696 26,9 L26,9 L26,9 Z" id="key"/></g></g></svg>

							<svg enable-background="new 0 0 32 32" height="64px" id="arrow-right" version="1.1" viewBox="0 0 32 32" width="64px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="M1.06,29.897c0.011,0,0.023,0,0.034-0.001c0.506-0.017,0.825-0.409,0.868-0.913  c0.034-0.371,1.03-9.347,15.039-9.337l0.031,5.739c0,0.387,0.223,0.739,0.573,0.904c0.347,0.166,0.764,0.115,1.061-0.132  l12.968-10.743c0.232-0.19,0.366-0.475,0.365-0.774c-0.001-0.3-0.136-0.584-0.368-0.773L18.664,3.224  c-0.299-0.244-0.712-0.291-1.06-0.128c-0.349,0.166-0.571,0.518-0.571,0.903l-0.031,5.613c-5.812,0.185-10.312,2.054-13.23,5.468  c-4.748,5.556-3.688,13.63-3.639,13.966C0.207,29.536,0.566,29.897,1.06,29.897z M18.032,17.63c-0.001,0-0.002,0-0.002,0  C8.023,17.636,4.199,21.015,2.016,23.999c0.319-2.391,1.252-5.272,3.281-7.626c2.698-3.128,7.045-4.776,12.735-4.776  c0.553,0,1-0.447,1-1V6.104l10.389,8.542l-10.389,8.622V18.63c0-0.266-0.105-0.521-0.294-0.708  C18.551,17.735,18.297,17.63,18.032,17.63z" fill="#888" id="Arrow_Right_2_"/><g/><g/><g/><g/><g/><g/></svg>

							<svg height="64px" version="1.1" viewBox="0 0 32 32" width="64px" xmlns="http://www.w3.org/2000/svg" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns" xmlns:xlink="http://www.w3.org/1999/xlink"><title/><desc/><defs/><g fill="none" fill-rule="evenodd" id="Page-1" stroke="none" stroke-width="1"><g fill="#2ecc71" id="icon-116-lock-open"><path d="M24,9.5 L24,8.499235 C24,4.35752188 20.6337072,1 16.5,1 C12.3578644,1 9,4.35670485 9,8.499235 L9,16.0000125 L9,16.0000125 C7.34233349,16.0047152 6,17.339581 6,19.0094776 L6,28.9905224 C6,30.652611 7.34559019,32 9.00878799,32 L23.991212,32 C25.6529197,32 27,30.6633689 27,28.9905224 L27,19.0094776 C27,17.3503174 25.6591471,16.0047488 24,16 L23.4863586,16 L12.0274777,16 C12.0093222,15.8360041 12,15.6693524 12,15.5005291 L12,8.49947095 C12,6.01021019 14.0147186,4 16.5,4 C18.9802243,4 21,6.01448176 21,8.49947095 L21,9.5 L21,12.4998351 C21,13.3283533 21.6657972,14 22.5,14 C23.3284271,14 24,13.3256778 24,12.4998351 L24,9.5 L24,9.5 L24,9.5 Z M23,8.49342686 C23,4.90720623 20.0825462,2 16.5,2 C12.9101491,2 10,4.90817171 10,8.49342686 L10,15.5065731 C10,15.6725774 10.0062513,15.8371266 10.0185304,16 L11,16 L11,8.50907306 C11,5.46140289 13.4624339,3 16.5,3 C19.5313853,3 22,5.46649603 22,8.50907306 L22,12.5022333 C22,12.7771423 22.2319336,13 22.5,13 L22.5,13 C22.7761424,13 23,12.7849426 23,12.5095215 L23,9 L23,8.49342686 L23,8.49342686 Z M16,23.9146472 L16,26.5089948 C16,26.7801695 16.2319336,27 16.5,27 C16.7761424,27 17,26.7721195 17,26.5089948 L17,23.9146472 C17.5825962,23.708729 18,23.1531095 18,22.5 C18,21.6715728 17.3284272,21 16.5,21 C15.6715728,21 15,21.6715728 15,22.5 C15,23.1531095 15.4174038,23.708729 16,23.9146472 L16,23.9146472 L16,23.9146472 Z M15,24.5001831 L15,26.4983244 C15,27.3276769 15.6657972,28 16.5,28 C17.3284271,28 18,27.3288106 18,26.4983244 L18,24.5001831 C18.6072234,24.04408 19,23.317909 19,22.5 C19,21.1192881 17.8807119,20 16.5,20 C15.1192881,20 14,21.1192881 14,22.5 C14,23.317909 14.3927766,24.04408 15,24.5001831 L15,24.5001831 L15,24.5001831 Z M8.99742191,17 C7.89427625,17 7,17.8970601 7,19.0058587 L7,28.9941413 C7,30.1019465 7.89092539,31 8.99742191,31 L24.0025781,31 C25.1057238,31 26,30.1029399 26,28.9941413 L26,19.0058587 C26,17.8980535 25.1090746,17 24.0025781,17 L8.99742191,17 L8.99742191,17 Z" id="-ock-open"/></g></g></svg>
	      				</div>	
      					<h1 class="betterdocs-validation-title"><?php esc_html_e('Just one more step to go!','betterdocs-pro') ?></h1>	
	      			</div>
      				<div class="betterdocs-license-instruction">
	                    <p><?php _e( 'Enter your license key here, to activate <strong>BetterDocs Pro</strong>, and get automatic updates and premium support.', $this->text_domain ); ?></p>
	                    <p><?php printf( __( 'Visit the <a rel="nofollow" href="%s" target="_blank">Validation Guide</a> for help.', $this->text_domain ), 'https://betterdocs.co/docs/betterdocs-license/' ); ?></p>

	                    <ol>
	                        <li><?php printf( __( 'Log in to <a rel="nofollow" href="%s" target="_blank">your account</a> to get your license key.', $this->text_domain ), 'https://wpdeveloper.com/account/' ); ?></li>
	                        <li><?php printf( __( 'If you don\'t yet have a license key, get <a rel="nofollow" href="%s" target="_blank">BetterDocs Pro now</a>.', $this->text_domain ), 'https://betterdocs.co/upgrade' ); ?></li>
	                        <li><?php _e( __( 'Copy the license key from your account and paste it below.', $this->text_domain ) ); ?></li>
	                        <li><?php _e( __( 'Click on <strong>"Activate License"</strong> button.', $this->text_domain ) ); ?></li>
	                    </ol>
                	</div>
      				<?php endif; ?>

      				<?php if( $status !== false && $status == 'valid' ) { ?>
      				<div class="validated-feature-list">
      					<div class="validated-feature-list-item">
  							<div class="validated-feature-list-icon">
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 64 64" version="1.1" fill="#2ecc71">
									<g id="surface1" fill="#2ecc71">
									<path style=" " d="M 31 2 C 15.011719 2 2 15.011719 2 31 C 2 46.988281 15.011719 60 31 60 C 46.988281 60 60 46.988281 60 31 C 60 15.011719 46.988281 2 31 2 Z M 31 4 C 45.886719 4 58 16.113281 58 31 C 58 45.886719 45.886719 58 31 58 C 16.113281 58 4 45.886719 4 31 C 4 16.113281 16.113281 4 31 4 Z M 30.5625 8.007813 C 24.671875 8.128906 18.960938 10.507813 14.734375 14.734375 C 5.769531 23.703125 5.769531 38.292969 14.734375 47.261719 C 14.929688 47.457031 15.1875 47.554688 15.441406 47.554688 C 15.699219 47.554688 15.957031 47.457031 16.152344 47.261719 C 16.542969 46.871094 16.542969 46.238281 16.152344 45.847656 C 7.960938 37.660156 7.964844 24.339844 16.152344 16.152344 C 20.898438 11.402344 27.699219 9.199219 34.339844 10.265625 C 34.882813 10.355469 35.394531 9.980469 35.484375 9.4375 C 35.574219 8.890625 35.199219 8.375 34.65625 8.289063 C 33.289063 8.070313 31.921875 7.980469 30.5625 8.007813 Z M 38.660156 9.386719 C 38.269531 9.402344 37.910156 9.648438 37.765625 10.035156 C 37.570313 10.554688 37.832031 11.128906 38.351563 11.324219 C 39.0625 11.589844 39.765625 11.894531 40.453125 12.238281 C 40.597656 12.3125 40.75 12.347656 40.902344 12.347656 C 41.265625 12.347656 41.617188 12.148438 41.796875 11.796875 C 42.042969 11.308594 41.84375 10.703125 41.351563 10.453125 C 40.605469 10.078125 39.828125 9.738281 39.050781 9.445313 C 38.921875 9.398438 38.789063 9.382813 38.660156 9.386719 Z M 44.433594 12.675781 C 44.179688 12.707031 43.9375 12.835938 43.765625 13.050781 C 43.425781 13.488281 43.5 14.113281 43.9375 14.453125 C 44.605469 14.976563 45.25 15.550781 45.847656 16.152344 C 54.039063 24.339844 54.039063 37.660156 45.847656 45.847656 C 45.457031 46.242188 45.457031 46.871094 45.847656 47.265625 C 46.042969 47.457031 46.300781 47.558594 46.558594 47.558594 C 46.8125 47.558594 47.070313 47.457031 47.265625 47.265625 C 56.230469 38.296875 56.230469 23.703125 47.265625 14.734375 C 46.605469 14.078125 45.902344 13.453125 45.171875 12.878906 C 44.953125 12.710938 44.683594 12.644531 44.433594 12.675781 Z M 43 22 C 42.746094 22 42.488281 22.097656 42.292969 22.292969 L 28 36.585938 L 20.707031 29.292969 C 20.316406 28.902344 19.683594 28.902344 19.292969 29.292969 C 18.902344 29.683594 18.902344 30.316406 19.292969 30.707031 L 27.292969 38.707031 C 27.488281 38.902344 27.742188 39 28 39 C 28.257813 39 28.511719 38.902344 28.707031 38.707031 L 43.707031 23.707031 C 44.097656 23.316406 44.097656 22.683594 43.707031 22.292969 C 43.511719 22.097656 43.253906 22 43 22 Z M 21.375 47.394531 C 20.984375 47.347656 20.589844 47.527344 20.386719 47.886719 L 19.386719 49.617188 C 19.109375 50.097656 19.273438 50.707031 19.75 50.984375 C 19.90625 51.074219 20.078125 51.117188 20.25 51.117188 C 20.59375 51.117188 20.929688 50.9375 21.113281 50.617188 L 22.113281 48.886719 C 22.390625 48.410156 22.230469 47.796875 21.75 47.519531 C 21.628906 47.453125 21.5 47.410156 21.375 47.394531 Z M 40.625 47.394531 C 40.496094 47.410156 40.367188 47.453125 40.25 47.519531 C 39.769531 47.796875 39.609375 48.410156 39.886719 48.890625 L 40.886719 50.621094 C 41.070313 50.941406 41.40625 51.121094 41.75 51.121094 C 41.921875 51.121094 42.09375 51.074219 42.25 50.984375 C 42.730469 50.707031 42.890625 50.097656 42.613281 49.621094 L 41.613281 47.890625 C 41.40625 47.53125 41.011719 47.347656 40.625 47.394531 Z M 25.816406 49.34375 C 25.429688 49.398438 25.09375 49.675781 24.984375 50.078125 L 24.46875 52.011719 C 24.324219 52.542969 24.644531 53.089844 25.175781 53.234375 C 25.261719 53.257813 25.347656 53.265625 25.4375 53.265625 C 25.875 53.265625 26.28125 52.972656 26.402344 52.527344 L 26.921875 50.59375 C 27.0625 50.0625 26.746094 49.511719 26.214844 49.371094 C 26.082031 49.332031 25.945313 49.328125 25.816406 49.34375 Z M 36.1875 49.34375 C 36.058594 49.328125 35.921875 49.332031 35.785156 49.371094 C 35.253906 49.511719 34.9375 50.0625 35.078125 50.59375 L 35.597656 52.527344 C 35.71875 52.972656 36.121094 53.265625 36.5625 53.265625 C 36.652344 53.265625 36.738281 53.257813 36.824219 53.234375 C 37.355469 53.089844 37.675781 52.542969 37.53125 52.011719 L 37.015625 50.078125 C 36.90625 49.675781 36.570313 49.398438 36.1875 49.34375 Z M 31 50 C 30.445313 50 30 50.445313 30 51 L 30 53 C 30 53.554688 30.445313 54 31 54 C 31.554688 54 32 53.554688 32 53 L 32 51 C 32 50.445313 31.554688 50 31 50 Z " fill="#2ecc71"/>
									</g>
								</svg>
  							</div>
  							<div class="validated-feature-list-content">
  								<h4><?php esc_html_e('Auto Update', 'betterdocs-pro') ?></h4>
  								<p><?php esc_html_e('Update the plugin right from your WordPress Dashboard.', 'betterdocs-pro') ?></p>
  							</div>
      					</div><!--./feature-list-item-->
      					<div class="validated-feature-list-item">
      						<div class="validated-feature-list-icon">
								<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 64 64" version="1.1" fill="#2ecc71">
									<g id="surface1" fill="#2ecc71">
									<path style=" " d="M 31 2 C 15.011719 2 2 15.011719 2 31 C 2 46.988281 15.011719 60 31 60 C 46.988281 60 60 46.988281 60 31 C 60 15.011719 46.988281 2 31 2 Z M 31 4 C 45.886719 4 58 16.113281 58 31 C 58 45.886719 45.886719 58 31 58 C 16.113281 58 4 45.886719 4 31 C 4 16.113281 16.113281 4 31 4 Z M 30.5625 8.007813 C 24.671875 8.128906 18.960938 10.507813 14.734375 14.734375 C 5.769531 23.703125 5.769531 38.292969 14.734375 47.261719 C 14.929688 47.457031 15.1875 47.554688 15.441406 47.554688 C 15.699219 47.554688 15.957031 47.457031 16.152344 47.261719 C 16.542969 46.871094 16.542969 46.238281 16.152344 45.847656 C 7.960938 37.660156 7.964844 24.339844 16.152344 16.152344 C 20.898438 11.402344 27.699219 9.199219 34.339844 10.265625 C 34.882813 10.355469 35.394531 9.980469 35.484375 9.4375 C 35.574219 8.890625 35.199219 8.375 34.65625 8.289063 C 33.289063 8.070313 31.921875 7.980469 30.5625 8.007813 Z M 38.660156 9.386719 C 38.269531 9.402344 37.910156 9.648438 37.765625 10.035156 C 37.570313 10.554688 37.832031 11.128906 38.351563 11.324219 C 39.0625 11.589844 39.765625 11.894531 40.453125 12.238281 C 40.597656 12.3125 40.75 12.347656 40.902344 12.347656 C 41.265625 12.347656 41.617188 12.148438 41.796875 11.796875 C 42.042969 11.308594 41.84375 10.703125 41.351563 10.453125 C 40.605469 10.078125 39.828125 9.738281 39.050781 9.445313 C 38.921875 9.398438 38.789063 9.382813 38.660156 9.386719 Z M 44.433594 12.675781 C 44.179688 12.707031 43.9375 12.835938 43.765625 13.050781 C 43.425781 13.488281 43.5 14.113281 43.9375 14.453125 C 44.605469 14.976563 45.25 15.550781 45.847656 16.152344 C 54.039063 24.339844 54.039063 37.660156 45.847656 45.847656 C 45.457031 46.242188 45.457031 46.871094 45.847656 47.265625 C 46.042969 47.457031 46.300781 47.558594 46.558594 47.558594 C 46.8125 47.558594 47.070313 47.457031 47.265625 47.265625 C 56.230469 38.296875 56.230469 23.703125 47.265625 14.734375 C 46.605469 14.078125 45.902344 13.453125 45.171875 12.878906 C 44.953125 12.710938 44.683594 12.644531 44.433594 12.675781 Z M 43 22 C 42.746094 22 42.488281 22.097656 42.292969 22.292969 L 28 36.585938 L 20.707031 29.292969 C 20.316406 28.902344 19.683594 28.902344 19.292969 29.292969 C 18.902344 29.683594 18.902344 30.316406 19.292969 30.707031 L 27.292969 38.707031 C 27.488281 38.902344 27.742188 39 28 39 C 28.257813 39 28.511719 38.902344 28.707031 38.707031 L 43.707031 23.707031 C 44.097656 23.316406 44.097656 22.683594 43.707031 22.292969 C 43.511719 22.097656 43.253906 22 43 22 Z M 21.375 47.394531 C 20.984375 47.347656 20.589844 47.527344 20.386719 47.886719 L 19.386719 49.617188 C 19.109375 50.097656 19.273438 50.707031 19.75 50.984375 C 19.90625 51.074219 20.078125 51.117188 20.25 51.117188 C 20.59375 51.117188 20.929688 50.9375 21.113281 50.617188 L 22.113281 48.886719 C 22.390625 48.410156 22.230469 47.796875 21.75 47.519531 C 21.628906 47.453125 21.5 47.410156 21.375 47.394531 Z M 40.625 47.394531 C 40.496094 47.410156 40.367188 47.453125 40.25 47.519531 C 39.769531 47.796875 39.609375 48.410156 39.886719 48.890625 L 40.886719 50.621094 C 41.070313 50.941406 41.40625 51.121094 41.75 51.121094 C 41.921875 51.121094 42.09375 51.074219 42.25 50.984375 C 42.730469 50.707031 42.890625 50.097656 42.613281 49.621094 L 41.613281 47.890625 C 41.40625 47.53125 41.011719 47.347656 40.625 47.394531 Z M 25.816406 49.34375 C 25.429688 49.398438 25.09375 49.675781 24.984375 50.078125 L 24.46875 52.011719 C 24.324219 52.542969 24.644531 53.089844 25.175781 53.234375 C 25.261719 53.257813 25.347656 53.265625 25.4375 53.265625 C 25.875 53.265625 26.28125 52.972656 26.402344 52.527344 L 26.921875 50.59375 C 27.0625 50.0625 26.746094 49.511719 26.214844 49.371094 C 26.082031 49.332031 25.945313 49.328125 25.816406 49.34375 Z M 36.1875 49.34375 C 36.058594 49.328125 35.921875 49.332031 35.785156 49.371094 C 35.253906 49.511719 34.9375 50.0625 35.078125 50.59375 L 35.597656 52.527344 C 35.71875 52.972656 36.121094 53.265625 36.5625 53.265625 C 36.652344 53.265625 36.738281 53.257813 36.824219 53.234375 C 37.355469 53.089844 37.675781 52.542969 37.53125 52.011719 L 37.015625 50.078125 C 36.90625 49.675781 36.570313 49.398438 36.1875 49.34375 Z M 31 50 C 30.445313 50 30 50.445313 30 51 L 30 53 C 30 53.554688 30.445313 54 31 54 C 31.554688 54 32 53.554688 32 53 L 32 51 C 32 50.445313 31.554688 50 31 50 Z " fill="#2ecc71"/>
									</g>
								</svg> 
      						</div>
  							<div class="validated-feature-list-content">
  								<h4><?php esc_html_e('Premium Support', 'betterdocs-pro') ?></h4>
  								<p><?php esc_html_e('Supported by professional and courteous staff.', 'betterdocs-pro') ?></p>
  							</div>
      					</div><!--./feature-list-item-->
      				</div><!--./feature-list-->
      				<?php } ?>

      				<div class="betterdocs-license-container">
						<div class="betterdocs-license-icon">
							<?php if( $status == false && $status !== 'valid' ) { ?>
								<svg height="32px" version="1.1" viewBox="0 0 32 32" width="32px" xmlns="http://www.w3.org/2000/svg" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns" xmlns:xlink="http://www.w3.org/1999/xlink"><title/><desc/><defs/><g fill="none" fill-rule="evenodd" id="Page-1" stroke="none" stroke-width="1"><g fill="#26d396" id="icon-114-lock"><path d="M16,21.9146472 L16,24.5089948 C16,24.7801695 16.2319336,25 16.5,25 C16.7761424,25 17,24.7721195 17,24.5089948 L17,21.9146472 C17.5825962,21.708729 18,21.1531095 18,20.5 C18,19.6715728 17.3284272,19 16.5,19 C15.6715728,19 15,19.6715728 15,20.5 C15,21.1531095 15.4174038,21.708729 16,21.9146472 L16,21.9146472 Z M9,14.0000125 L9,10.499235 C9,6.35670485 12.3578644,3 16.5,3 C20.6337072,3 24,6.35752188 24,10.499235 L24,14.0000125 C25.6591471,14.0047488 27,15.3503174 27,17.0094776 L27,26.9905224 C27,28.6633689 25.6529197,30 23.991212,30 L9.00878799,30 C7.34559019,30 6,28.652611 6,26.9905224 L6,17.0094776 C6,15.339581 7.34233349,14.0047152 9,14.0000125 L9,14.0000125 L9,14.0000125 Z M12,14 L12,10.5008537 C12,8.0092478 14.0147186,6 16.5,6 C18.9802243,6 21,8.01510082 21,10.5008537 L21,14 L12,14 L12,14 L12,14 Z" id="lock"/></g></g></svg>
							<?php } ?>
							<?php if( $status !== false && $status == 'valid' ) { ?>
								<svg height="30px" version="1.1" viewBox="0 0 32 32" width="30px" xmlns="http://www.w3.org/2000/svg" xmlns:sketch="http://www.bohemiancoding.com/sketch/ns" xmlns:xlink="http://www.w3.org/1999/xlink"><title/><desc/><defs/><g fill="none" fill-rule="evenodd" id="Page-1" stroke="none" stroke-width="1"><g fill="rgba(20,216,161, .75)" id="icon-116-lock-open"><path d="M16,23.9146472 L16,26.5089948 C16,26.7801695 16.2319336,27 16.5,27 C16.7761424,27 17,26.7721195 17,26.5089948 L17,23.9146472 C17.5825962,23.708729 18,23.1531095 18,22.5 C18,21.6715728 17.3284272,21 16.5,21 C15.6715728,21 15,21.6715728 15,22.5 C15,23.1531095 15.4174038,23.708729 16,23.9146472 L16,23.9146472 L16,23.9146472 Z M24,9.5 L24,8.499235 C24,4.35752188 20.6337072,1 16.5,1 C12.3578644,1 9,4.35670485 9,8.499235 L9,16.0000125 L9,16.0000125 C7.34233349,16.0047152 6,17.339581 6,19.0094776 L6,28.9905224 C6,30.652611 7.34559019,32 9.00878799,32 L23.991212,32 C25.6529197,32 27,30.6633689 27,28.9905224 L27,19.0094776 C27,17.3503174 25.6591471,16.0047488 24,16 L22.4819415,16 L12.0274777,16 C12.0093222,15.8360041 12,15.6693524 12,15.5005291 L12,8.49947095 C12,6.01021019 14.0147186,4 16.5,4 C18.9802243,4 21,6.01448176 21,8.49947095 L21,9.5 L21,12.1239591 C21,13.1600679 21.6657972,14 22.5,14 C23.3284271,14 24,13.1518182 24,12.1239591 L24,9.5 L24,9.5 L24,9.5 Z" id="lock-open"/></g></g></svg>
							<?php } ?>

						</div>
						<div class="betterdocs-license-input">
							<input <?php echo ( $status !== false && $status == 'valid' ) ? 'disabled' : ''; ?> id="<?php echo $this->product_slug; ?>-license-key" name="<?php echo $this->product_slug; ?>-license-key" type="text" class="regular-text" value="<?php echo esc_attr( self::get_hidden_license_key() ); ?>"" placeholder="Place Your License Key and Activate" />
						</div>
						<div class="betterdocs-license-buttons">
							<?php wp_nonce_field( $this->product_slug . '_license_nonce', $this->product_slug . '_license_nonce' ); ?>
							<?php if( $status !== false && $status == 'valid' ) { ?>
								<input type="hidden" name="<?php echo $this->product_slug; ?>_license_deactivate" />
								<?php submit_button( __( 'Deactivate License', 'betterdocs-pro' ), 'betterdocs-license-deactivation-btn', 'submit', false, array( 'class' => 'button button-primary' ) ); ?>
							<?php } else { ?>
								<input type="hidden" name="<?php echo $this->product_slug; ?>_license_activate" />
								<?php submit_button( __( 'Activate License', 'betterdocs-pro' ), 'betterdocs-license-activation-btn', 'submit', false, array( 'class' => 'button button-primary' ) ); ?>
							<?php } ?>
						</div>
					</div>
			</form>
		</div>
	<?php
	}

	/**
	 * Gets the current license status
	 *
	 * @return bool|string   The product license key, or false if not set
	 */
	public function get_license_status() {
		$status = get_option( $this->product_slug . '-license-status' );
		if ( ! $status ) {
			// User hasn't saved the license to settings yet. No use making the call.
			return false;
		}
		return trim( $status );
	}

	/**
	 * Gets the currently set license key
	 *
	 * @return bool|string   The product license key, or false if not set
	 */
	public function get_license_key() {
		$license = get_option( $this->product_slug . '-license-key' );
		if ( ! $license ) {
			// User hasn't saved the license to settings yet. No use making the call.
			return false;
		}
		return trim( $license );
	}


	/**
	 * Updates the license key option
	 *
	 * @return bool|string   The product license key, or false if not set
	 */
	public function set_license_key( $license_key ) {
		return update_option( $this->product_slug . '-license-key', $license_key );
	}

	private function get_hidden_license_key() {
		$input_string = $this->get_license_key();

		$start = 5;
		$length = mb_strlen( $input_string ) - $start - 5;

		$mask_string = preg_replace( '/\S/', '*', $input_string );
		$mask_string = mb_substr( $mask_string, $start, $length );
		$input_string = substr_replace( $input_string, $mask_string, $start, $length );

		return $input_string;
	}

	/**
	 * @param array $body_args
	 *
	 * @return \stdClass|\WP_Error
	 */
	private function remote_post( $body_args = [] ) {
		$api_params = wp_parse_args(
			$body_args,
			[
				'item_id' => urlencode( $this->item_id ),
				'url'     => home_url(),
			]
		);

		$response = wp_remote_post( BETTERDOCS_PRO_STORE_URL, [
			'sslverify' => false,
			'timeout' => 40,
			'body' => $api_params,
		] );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== (int) $response_code ) {
			return new \WP_Error( $response_code, __( 'HTTP Error', 'essential-addons-elementor' ) );
		}

		$data = json_decode( wp_remote_retrieve_body( $response ) );
		if ( empty( $data ) || ! is_object( $data ) ) {
			return new \WP_Error( 'no_json', __( 'An error occurred, please try again', 'essential-addons-elementor' ) );
		}

		return $data;
	}

	public function activate_license(){
		if( ! isset( $_POST[ $this->product_slug . '_license_activate' ] ) ) { 
			return;
		}
		// run a quick security check
		if( ! check_admin_referer( $this->product_slug . '_license_nonce', $this->product_slug . '_license_nonce' ) ) {
			return;
		}

		$license_data = new \stdClass();
				$license_data->license = 'valid';
				$license_data->success = true;

				$license_data->payment_id = 0;
				$license_data->license_limit = 0;

				$license_data->site_count = 0;

				$license_data->activations_left = 0;

		if ( isset( $license_data->success ) && false === boolval( $license_data->success ) ) {

			switch( $license_data->error ) {

				case 'expired' :

					$message = sprintf(
						__( 'Your license key expired on %s.' ),
						date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
					);
					break;

				case 'revoked' :

					$message = __( 'Your license key has been disabled.' );
					break;

				case 'missing' :

					$message = __( 'Invalid license.' );
					break;

				case 'invalid' :
				case 'site_inactive' :

					$message = __( 'Your license is not active for this URL.' );
					break;

				case 'item_name_mismatch' :

					$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), BETTERDOCS_PRO_SL_ITEM_NAME );
					break;

				case 'no_activations_left':

					$message = __( 'Your license key has reached its activation limit.' );
					break;

				default :

					$message = __( 'An error occurred, please try again.' );
					break;
			}

		}


		// Check if anything passed on a message constituting a failure
		if ( ! empty( $message ) ) {
			$base_url = admin_url( 'admin.php?page=' . $this->get_settings_page_slug() );
			$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );
			wp_redirect( $redirect );
			exit();
		}

		// $license_data->license will be either "valid" or "invalid"
		$this->set_license_key( $license );
		$this->set_license_data( $license_data );
		$this->set_license_status( $license_data->license );
		wp_redirect( admin_url( 'admin.php?page=' . $this->get_settings_page_slug() ) );
		exit();

	}

	public function set_license_data( $license_data, $expiration = null ) {
		if ( null === $expiration ) {
			$expiration = 12 * HOUR_IN_SECONDS;
		}
		set_transient( $this->product_slug . '-license_data', $license_data, $expiration );
	}

	public function get_license_data( $force_request = false ) {
		$license_data = get_transient( $this->product_slug . '-license_data' );

		if ( false === $license_data || $force_request ) {

			$license = $this->get_license_key();

			if( empty( $license ) ) {
				return false;
			}



				$license_data = new \stdClass();
				$license_data->license = 'valid';
				$license_data->payment_id = 0;
				$license_data->license_limit = 0;
				$license_data->site_count = 0;
				$license_data->activations_left = 0;
				$this->set_license_data( $license_data );
				$this->set_license_status( $license_data->license );
		}

		return $license_data;
	}

	public function deactivate_license(){
		if( ! isset( $_POST[ $this->product_slug . '_license_deactivate' ] ) ) {
			return;
		}
		if( ! check_admin_referer( $this->product_slug . '_license_nonce', $this->product_slug . '_license_nonce' ) ) {
			return;
		}

		// retrieve the license from the database
		$license = $this->get_license_key();
		$transient = get_transient( $this->product_slug . '-license_data' );
		if( $transient !== false ) {
			$option = delete_option( '_transient_' . $this->product_slug . '-license_data' );
			if( $option ) {
				delete_option( '_transient_timeout_' . $this->product_slug . '-license_data' );
			}
		}

		$license_data = new \stdClass();
				$license_data->license = 'deactivated';


		if( is_wp_error( $license_data ) ) {
			$message = $license_data->get_error_message();
		}
		
		if( ! empty( $message ) ) {
			$base_url = admin_url( 'admin.php?page=' . $this->get_settings_page_slug() );
			$redirect = add_query_arg( array( 'sl_activation' => 'false', 'message' => urlencode( $message ) ), $base_url );
			wp_redirect( $redirect );
			exit();
		}

		if( $license_data->license != 'deactivated' ) {
			$message = __( 'An error occurred, please try again', 'essential-addons-elementor' );
			$base_url = admin_url( 'admin.php?page=' . $this->get_settings_page_slug() );
			$redirect = add_query_arg( array( 'sl_deactivation' => 'false', 'message' => urlencode( $message ) ), $base_url );
			wp_redirect( $redirect );
			exit();
		}

		if( $license_data->license == 'deactivated' ) {
			delete_option( $this->product_slug . '-license-status' );
			delete_option( $this->product_slug . '-license-key' );
		}

		
		wp_redirect( admin_url( 'admin.php?page=' . $this->get_settings_page_slug() ) );
		exit();
	}

	/**
	 * Updates the license status option
	 *
	 * @return bool|string   The product license key, or false if not set
	 */
	public function set_license_status( $license_status ) {
		return update_option( $this->product_slug . '-license-status', $license_status );
	}
}