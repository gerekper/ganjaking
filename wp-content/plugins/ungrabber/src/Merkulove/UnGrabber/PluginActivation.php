<?php
/**
 * A most effective way to protect your online content from being copied or grabbed
 * Exclusively on Envato Market: https://1.envato.market/ungrabber
 *
 * @encoding        UTF-8
 * @version         2.0.1
 * @copyright       Copyright (C) 2018 - 2020 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license         Commercial Software
 * @contributors    Alexander Khmelnitskiy (info@alexander.khmelnitskiy.ua), Dmitry Merkulov (dmitry@merkulov.design)
 * @support         help@merkulov.design
 **/

namespace Merkulove\UnGrabber;

/** Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

/**
 * SINGLETON: Class used to implement Activation tab on plugin settings page.
 *
 * @since 1.0.0
 * @author Alexandr Khmelnytsky (info@alexander.khmelnitskiy.ua)
 */
final class PluginActivation {

	/**
	 * The one true PluginActivation.
	 *
	 * @var PluginActivation
	 * @since 1.0.0
	 **/
	private static $instance;

	/**
	 * Sets up a new PluginActivation instance.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	private function __construct() {

		add_action( 'update_option_envato_purchase_code_' . EnvatoItem::get_instance()->get_id(), [$this, 'reset_temporary_activation'], 10, 2);

	}

	/**
	 * Reset temporary activation on every pid change.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function reset_temporary_activation() {

		delete_transient ( 'activated_' . EnvatoItem::get_instance()->get_id() );

		/** Reset updates cache. */
		PluginUpdater::get_instance()->reset_cache();

	}

	/**
	 * Display Activation Status.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function display_status() {

		$activation_tab = admin_url( 'admin.php?page=mdp_ungrabber_settings&tab=activation' );
		?>

        <hr class="mdc-list-divider">
        <h6 class="mdc-list-group__subheader"><?php esc_html_e( 'CodeCanyon License', 'ungrabber' ); ?></h6>

        <?php if ( $this->is_activated() ) : ?>
            <a class="mdc-list-item mdc-activation-status activated" href="<?php echo esc_url( $activation_tab ); ?>">
                <i class='material-icons mdc-list-item__graphic' aria-hidden='true'>check_circle</i>
                <span class="mdc-list-item__text"><?php esc_html_e( 'Activated', 'ungrabber' ); ?></span>
            </a>
        <?php else : ?>
            <a class=" mdc-list-item mdc-activation-status not-activated" href="<?php echo esc_url( $activation_tab ); ?>">
                <i class='material-icons mdc-list-item__graphic' aria-hidden='true'>remove_circle</i>
                <span class="mdc-list-item__text"><?php esc_html_e( 'Not Activated', 'ungrabber' ); ?></span>
            </a>
        <?php endif;

	}

	/**
	 * Return Activation Status.
	 *
	 * @return boolean True if activated.
	 * @since 1.0.0
	 * @access public
	 */
	public function is_activated() {

	    /** Not activated if plugin don't have Envato ID. */
	    $plugin_id = EnvatoItem::get_instance()->get_id();
        if ( (int)$plugin_id === 0 ) { return false; }

		/** Get fresh PID from form. */
		$item_id = EnvatoItem::get_instance()->get_id();
		if ( isset( $_POST['envato_purchase_code_' . $item_id ] ) ) {
			$purchase_code = filter_input( INPUT_POST, 'envato_purchase_code_' . $item_id );

        /** Or get PID from option. */
        } else {
			$purchase_code = get_option( 'envato_purchase_code_' . $item_id );
        }

		/** If we do not have $purchase_code then nothing to check. */
		if ( ! $purchase_code ) {
			return false; // No key. Not activated.
        }

		/** Clean and validate purchase code. */
		/** Remove spaces. */
		$purchase_code = trim( $purchase_code );

		/** Make sure the code is valid before sending it to Envato. */
		if ( ! preg_match( "/^(\w{8})-((\w{4})-){3}(\w{12})$/", $purchase_code ) ) {
			return false; // Wrong key format. Not activated.
		}

	    /** Check temporary activation */
        $local_activation = $this->local_validation(); // 0 - if no local value, go to download.

		if ( $local_activation === 0 ) {

		    /** Need Remote validation. */
			$remote_activation = $this->remote_validation( $purchase_code );
			if ( $remote_activation ) {

                $this->temporary_activation( $remote_activation );
                return filter_var( $remote_activation, FILTER_VALIDATE_BOOLEAN );

            } else {

				/** Not activated. */
				$this->temporary_activation( false );
                return false;
            }

        } else {

			/** Use local activation. */
			$this->temporary_activation( $local_activation );
			return filter_var( $local_activation, FILTER_VALIDATE_BOOLEAN );

        }

	}

	/**
	 * Set temporary activation.
	 *
	 * @param bool $activate - Temporary Activate/Deactivate.
	 * @param int $hours - Timeout for temporary activation.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function temporary_activation( $activate, $hours = 12 ) {

	    /** Get envato item id. */
		$item_id = EnvatoItem::get_instance()->get_id();

	    if ( filter_var( $activate, FILTER_VALIDATE_BOOLEAN ) ) {
		    set_transient( 'activated_' . $item_id, '1', $hours * HOUR_IN_SECONDS );
        } else {
		    set_transient( 'activated_' . $item_id, '0', $hours * HOUR_IN_SECONDS );
        }

    }

	/**
	 * Validate PID on local server.
	 *
	 * @return int|true|false - status of temporary activation status.
     *         0 - if we dont have local activation status and need remove validation.
	 * @since 1.0.0
	 * @access public
	 */
	public function local_validation() {

		/** Get temporary activation status. */
		$tmp_activation_status = get_transient( 'activated_' . EnvatoItem::get_instance()->get_id() );

		/** Send query to server if we dont have temporary option. */
		if ( false === $tmp_activation_status ) {
			return 0;
        } else {
			return filter_var( $tmp_activation_status, FILTER_VALIDATE_BOOLEAN );
        }

    }

	/**
	 * Validate PID on our server.
	 *
	 * @param $purchase_code - Envato Purchase Code.
	 *
	 * @return array|mixed|object
	 * @since 1.0.0
	 * @access public
	 */
	public function remote_validation( $purchase_code ) {

		$curl = curl_init();

		/** Prepare URL. */
		$url = 'https://merkulove.host/wp-content/plugins/mdp-purchase-validator/src/Merkulove/PurchaseValidator/Validate.php?';
		$url .= 'action=validate&'; // Action.
		$url .= 'plugin=ungrabber&'; // Plugin Name.
		$url .= 'domain=' . parse_url( site_url(), PHP_URL_HOST ) . '&'; // Domain Name.
		$url .= 'version=2.0.1&'; // Plugin version.
		$url .= 'pid=' . $purchase_code . '&'; // Purchase Code.
		$url .= 'admin_e=' . base64_encode( get_option( 'admin_email' ) );

		curl_setopt( $curl, CURLOPT_URL, $url );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_HEADER, false );
		$json = curl_exec( $curl );

		/**
		 * Handle connection errors.
		 * Show users an appropriate message asking to try again later.
		 **/
		if ( curl_errno( $curl ) > 0 ) {
			echo esc_html( 'Error connecting to: ' . $url . PHP_EOL . 'Please check your security plugins and add this url to white list.' );
			return false;
		}

		/**
		 * If we reach this point, we have a proper response.
		 * Get the response code to check if the content was found.
		 **/
		$responseCode = curl_getinfo( $curl, CURLINFO_HTTP_CODE );

		/**
		 * Anything other than HTTP 200 indicates a request error.
		 * In this case, we again ask the user to try again later.
		 **/
		if ( $responseCode !== 200 ) {
			echo esc_html('Failed to get content due to an error: HTTP ' . $responseCode . PHP_EOL . 'URL: ' . $url );
			return false;
		}

		curl_close( $curl );

		$res = json_decode( $json, true );

		if ( true === $res ) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Generate Activation Tab.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function add_settings() {

		/** Not show if plugin don't have Envato ID. */
		$plugin_id = EnvatoItem::get_instance()->get_id();
		if ( (int)$plugin_id === 0 ) { return; }

		/** Activation Tab. */
		register_setting( 'UnGrabberActivationOptionsGroup', 'envato_purchase_code_' . EnvatoItem::get_instance()->get_id() );
		add_settings_section( 'mdp_ungrabber_settings_page_activation_section', '', null, 'UnGrabberActivationOptionsGroup' );

	}

	/**
	 * Render Purchase Code field.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function render_pid() {

		/** Not show if plugin don't have Envato ID. */
		$plugin_id = EnvatoItem::get_instance()->get_id();
		if ( (int)$plugin_id === 0 ) { return; }

	    /** Get envato item ID. */
	    $item_id = EnvatoItem::get_instance()->get_id();

		/** Get activation settings. */
		$purchase_code = get_option( 'envato_purchase_code_' . $item_id );

		?>

        <div class="mdp-activation">

            <div class="mdp-activation-form">

                <h3><?php esc_html_e( 'Plugin Activation', 'ungrabber' ); ?></h3>

                <?php
                /** Render input. */
                UI::get_instance()->render_input(
                    $purchase_code,
                    esc_html__( 'CodeCanyon purchase code', 'ungrabber'),
                    esc_html__( 'Enter your CodeCanyon purchase code. Allowed only one Purchase Code per website.', 'ungrabber' ),
                    [
                    	'name' => 'envato_purchase_code_' . $item_id,
	                    'id' => 'mdp_envato_purchase_code'
                    ]
                );
                ?>

            </div>

            <div class="mdp-activation-faq">
                <?php $this->render_FAQ(); // Render FAQ block. ?>
            </div>

        </div>

		<?php
	}

	/**
	 * Render FAQ block.
	 *
	 * @since 1.0.0
	 * @access public
	 **/
	public function render_FAQ() {
	    ?>
        <div class="mdc-accordion" data-mdc-accordion="showfirst: true">

            <h3><?php esc_html_e( 'Activation FAQ\'S', 'ungrabber' ); ?></h3>

            <div class="mdc-accordion-title">
                <i class="material-icons">help</i>
                <span class="mdc-list-item__text"><?php esc_html_e( 'Where is my Purchase Code?', 'ungrabber' ); ?></span>
            </div>
            <div class="mdc-accordion-content">
                <p><?php esc_html_e( 'The purchase code is a unique combination of characters that confirms that you bought the plugin. You can find your purchase code in ', 'ungrabber' ); ?>
                    <a href="https://1.envato.market/cc-downloads" target="_blank"><?php esc_html_e( 'your account', 'ungrabber' );?></a>
			        <?php esc_html_e( ' on the CodeCanyon. Learn more about ', 'ungrabber' ); ?>
                    <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank"><?php esc_html_e( 'How to find your purchase code', 'ungrabber' );?></a>
			        <?php esc_html_e( ' .', 'ungrabber');?>
                </p>
            </div>

            <div class="mdc-accordion-title">
                <i class="material-icons">help</i>
                <span class="mdc-list-item__text"><?php esc_html_e( 'Can I use one Purchase Code on multiple sites?', 'ungrabber' ); ?></span>
            </div>
            <div class="mdc-accordion-content">
                <p>
                    <?php esc_html_e( 'No, this is prohibited by license terms. You can use the purchase code on only one website at a time. Learn more about ', 'ungrabber' ); ?>
                    <a href="https://1.envato.market/KYbje" target="_blank"><?php esc_html_e( 'Envato License', 'ungrabber' );?></a>
	                <?php esc_html_e( ' terms. ', 'ungrabber' ); ?>
                </p>
            </div>

            <div class="mdc-accordion-title">
                <i class="material-icons">help</i>
                <span class="mdc-list-item__text"><?php esc_html_e( 'What are the benefits of plugin activation?', 'ungrabber' ); ?></span>
            </div>
            <div class="mdc-accordion-content">
                <p>
			        <?php esc_html_e( 'Activation of the plugin allows you to use all the functionality of the plugin on your site. In addition, in some cases, activating the plugin allows you to access additional features and capabilities of the plugin. Also, using an authored version of the plugin, you can be sure that you will not violate the license.', 'ungrabber' ); ?>
                </p>
            </div>

            <div class="mdc-accordion-title">
                <i class="material-icons">help</i>
                <span class="mdc-list-item__text"><?php esc_html_e( 'What should I do if my Purchase Code does not work?', 'ungrabber' ); ?></span>
            </div>
            <div class="mdc-accordion-content">
                <p>
                    <?php esc_html_e( 'There are several reasons why the purchase code may not work on your site. Learn more why your ', 'ungrabber' ); ?>
                    <a href="https://help.market.envato.com/hc/en-us/articles/204451834-My-Purchase-Code-is-Not-Working" target="_blank"><?php esc_html_e( 'Purchase Code is Not Working', 'ungrabber' );?></a>
	                <?php esc_html_e( ' .', 'ungrabber');?>
                </p>
            </div>

        </div>
        <?php
    }

	/**
	 * Main PluginActivation Instance.
	 *
	 * Insures that only one instance of PluginActivation exists in memory at any one time.
	 *
	 * @static
	 * @return PluginActivation
	 * @since 1.0.0
	 **/
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof PluginActivation ) ) {
			self::$instance = new PluginActivation;
		}

		return self::$instance;
	}

} // End Class PluginActivation.
