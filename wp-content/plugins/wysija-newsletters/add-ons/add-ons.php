<?php
if ( ! defined( 'ABSPATH' ) ){
	exit; // Exit if accessed directly
}

class MailPoet_Add_ons {
	/**
	 * Constructor
	 */
	public function __construct(){
		$this->plugin_path = WYSIJA_DIR;
		$this->wp_plugin_path = str_replace( 'wysija-newsletters', '', $this->plugin_path );
		$this->plugin_url = WYSIJA_URL;
		$this->image_url = '//ps.w.org/wysija-newsletters/assets/add-ons/';

		$this->mailpoet_add_on_activated_notice();
		$this->mailpoet_add_on_deactivated_notice();
	}

	/**
	 * Runs when the plugin is initialized.
	 */
	public function init_mail_poet_add_ons(){
		// Load JavaScript and stylesheets.
		$this->register_scripts_and_styles();
	}

	/**
	 * Registers and enqueues stylesheets for the
	 * administration panel and the public facing site.
	 */
	public function register_scripts_and_styles(){
		if ( is_admin() ) {
			wp_register_style( 'mail_poet_add_ons', WYSIJA_URL . 'css/add-ons.css' );
			wp_enqueue_style( 'mail_poet_add_ons' );
		} // end if
	} // end register_scripts_and_styles

	/**
	 * This notifies the user that the add-on plugin
	 * is now activated and returns them back to the
	 * add-ons page.
	 */
	public function mailpoet_add_on_activated_notice(){
		global $current_screen;

		require_once(ABSPATH.'/wp-admin/includes/plugin.php');

		if ( isset($_GET['action'] ) && $_GET['action'] == 'activate' && isset( $_GET['module'] ) ) {

			$plugin = plugin_basename( $_GET['module'] );
			$plugin_data = get_plugin_data( $this->wp_plugin_path . $plugin );

			$plugin_name = esc_attr( str_replace( ' ', '_', $plugin_data['Name'] ) );
			$plugin_name = esc_attr( str_replace( '&#039;', '_', $plugin_name ) );

			if ( isset( $_GET['requires'] ) ) {
				if ( file_exists( $this->wp_plugin_path . plugin_basename( $_GET['requires'] ) ) ) {
					if ( ! WYSIJA::is_plugin_active( $_GET['requires'] ) ) {
						$location = admin_url( 'admin.php?page=wysija_config&status=not-activated&add-on=' . $plugin_name . '&requires=' . esc_attr( str_replace( ' ', '_', $_GET['requires_name'] ) ) . '#tab-add-ons' );
						wp_safe_redirect( $location );
						exit;
					}
				} else {
					$location = admin_url( 'admin.php?page=wysija_config&status=not-installed&add-on=' . $plugin_name . '&requires=' . esc_attr( str_replace( ' ', '_', $_GET['requires_name'] ) ) . '#tab-add-ons' );
					wp_safe_redirect( $location );
					exit;
				}
			}

			// Activate the add-on plugin.
			activate_plugin( $plugin );

			// Return back to add-on page.
			$location = admin_url( 'admin.php?page=wysija_config&status=activated&add-on=' . $plugin_name . '#tab-add-ons' );
			wp_safe_redirect( $location );
			exit;
		}

		/**
		 * Display message if the plugin was not able to activate due
		 * to a required plugin is not active first.
		 */
		if ( $current_screen->parent_base == 'wysija_campaigns' && isset( $_GET['status'] ) && $_GET['status'] == 'not-activated' || isset( $_GET['status'] ) && $_GET['status'] == 'not-installed' ){
			echo
				'<div id="message" class="error fade" style="display:block !important;">' .
					'<p>' .
						'<strong>' . esc_attr( str_replace( '_', ' ', $_GET['add-on'] ) ) . '</strong> ' .
						wp_kses( sprintf(
							__( 'was not activated as it requires <strong><a href="%s">%s</a></strong> to be installed and active first.', WYSIJA ),
							esc_url( admin_url( 'plugin-install.php?tab=search&type=term&s=' . esc_attr( strtolower( str_replace( ' ', '+', $_GET['requires'] ) ) ) ) ),
							str_replace( '_', ' ', $_GET['requires'] )
						), array( 'a' => array( 'href' => array() ), 'strong' => array(), 'b' => array(), 'em' => array() ) ) .
						' <input type="button" class="button" value="' . esc_attr__( 'Hide this message', WYSIJA ) . '" onclick="document.location.href=\'' . esc_url( admin_url( 'admin.php?page=wysija_config#tab-add_ons' ) ) . '\';">' .
					'</p>' .
				'</div>';
		}

		// Display message once the add-on has been activated.
		if ( $current_screen->parent_base == 'wysija_campaigns' && isset( $_GET['status'] ) && $_GET['status'] == 'activated' ){
			echo '<div id="message" class="updated fade" style="display:block !important;"><p><strong>' . esc_attr( str_replace( '_', ' ', $_GET['add-on'] ) ) . '</strong> ' . esc_attr__( 'has been activated.', WYSIJA ) . '</p></div>';
		}
	}

	/**
	 * This notifies the user that the add-on plugin
	 * is now deactivated and returns them back to the
	 * add-ons page.
	 */
	public function mailpoet_add_on_deactivated_notice(){
		global $current_screen;

		require_once ABSPATH . '/wp-admin/includes/plugin.php';

		if ( isset( $_GET['action'] ) && $_GET['action'] == 'deactivate' && isset( $_GET['module'] ) ) {
			$plugin = plugin_basename( $_GET['module'] );
			$plugin_data = get_plugin_data( $this->wp_plugin_path . $plugin );

			// Deactivate the add-on plugin.
			deactivate_plugins( $plugin );

			// Return back to add-on page.
			$location = admin_url( 'admin.php?page=wysija_config&status=deactivated&add-on=' . esc_html( str_replace( ' ', '_', $plugin_data['Name'] ) ) . '#tab-add-ons' );
			wp_safe_redirect( $location );
			exit;
		}

		// Display message once the add-on has been deactivated.
		if ( $current_screen->parent_base == 'wysija_campaigns' && isset( $_GET['status'] ) && $_GET['status'] == 'deactivated' ) {
			echo '<div id="message" class="updated fade" style="display:block !important;"><p><strong>' . esc_attr( str_replace( '_', ' ', $_GET['add-on'] ) ) . '</strong> ' . esc_attr__( 'has been de-activated.', WYSIJA ) . '</p></div>';
		}

	}

	/**
	 * Displays the add ons page and lists
	 * the plugins and services available.
	 */
	public function add_ons_page(){
		require_once WYSIJA_DIR . '/add-ons/add-ons-list.php';

		echo '<div class="module-container">';
		foreach ( add_ons_list() as $plugin => $product ){
			$status = ''; // Status class.

			/**
			 * Queries if the plugin is installed,
			 * active and meets the requirements
			 * it requires if any.
			 */
			if ( file_exists( $this->wp_plugin_path . plugin_basename( $product['plugin_url'] ) ) ) {
				$status .= ' installed';
			} else {
				$status .= ' not-installed';
			}

			if ( WYSIJA::is_plugin_active( $product['plugin_url'] ) ) {
				$status .= ' active';
			} else {
				$status .= ' inactive';
			}

			if ( empty( $product['requires'] ) ) {
				$status .= ' ready';
			} elseif ( ! empty( $product['requires'] ) && file_exists( $this->wp_plugin_path . plugin_basename( $product['requires'] ) ) ) {
				$status .= ' ready';
				if ( WYSIJA::is_plugin_active( $product['requires'] ) ) {
					$status .= ' ready';
				} else {
					$status .= ' not-ready';
				}
			} elseif ( ! empty( $product['requires'] ) && ! file_exists( $this->wp_plugin_path . plugin_basename( $product['requires'] ) ) ) {
				$status .= ' not-ready';
			}

			if ( WYSIJA::is_plugin_active( 'wysija-newsletters-premium/index.php' ) ) {
				$status .= ' premium-active';
			}

			echo
			'<div class="mailpoet-module' . esc_attr( $status ) . '" id="product">' .
				'<h3>' . esc_attr( $product['name'] ) . '</h3>';

			if ( ! empty( $product['thumbnail'] ) ) {
				echo '<div class="mailpoet-module-image"><img src="' . esc_url( $this->image_url . $product['thumbnail'] ) . '" width="100%" title="' . esc_attr( $product['name'] ) . '" alt=""></div>';
			}

			echo
				'<div class="mailpoet-module-content">' .
					'<div class="mailpoet-module-description">' .
						'<p>' . wp_kses( $product['description'], array() ) . '</p>';

			if ( ! empty( $product['review'] ) ) {
				echo '<p><strong>' . esc_attr__( 'MailPoet says: ', WYSIJA ) . '<em>' . esc_attr( $product['review'] ) . '</em>' . '</strong></p>';
			}

			if ( WYSIJA::is_plugin_active( 'wysija-newsletters-premium/index.php' ) && ! empty( $product['premium_offer'] ) ) {
				echo '<p><strong>' . esc_attr( $product['premium_offer'] ) . '</strong></p>';
			}
			echo
					'</div>' .
				'</div>' .

				'<div class="mailpoet-module-actions">';

			if ( ! empty( $product['author_url'] ) ) {
				echo '<a href="' . esc_url( $product['author_url'] ) . '" target="_blank" rel="external" class="button-primary website">' . esc_attr__( 'Website', WYSIJA ) . '</a>&nbsp;';
			}

			if ( $product['free'] == false && ! empty( $product['purchase_url'] ) ) {
				if ( ! empty( $product['plugin_url'] ) && ! file_exists( $this->wp_plugin_path . plugin_basename( $product['plugin_url'] ) ) ) {
					echo '<a href="' . esc_url( $product['purchase_url'] ) . '" target="_blank" rel="external" class="button-primary purchase">' . esc_attr__( 'Purchase', WYSIJA ) . '</a>&nbsp;';
				} // end if plugin is installed, don't show purchase button.
			} // end if product is not free.

			if ( $product['service'] == false ){
				if ( $product['on_wordpress.org'] == true ){
					if ( ! file_exists( $this->wp_plugin_path . plugin_basename( $product['plugin_url'] ) ) ) {
						echo '<a href="' . esc_url( admin_url( 'plugin-install.php?tab=search&type=term&s=' . strtolower( str_replace( ' ', '+', $product['search'] ) ) ) ) . '" class="button-primary install">' . esc_attr__( 'Install from WordPress.org', WYSIJA ) . '</a>&nbsp;';
					}
				} // end if $product['on_wordpress.org'];

				if ( ! empty( $product['plugin_url'] ) && file_exists( $this->wp_plugin_path . plugin_basename( $product['plugin_url'] ) ) ) {
					if ( ! WYSIJA::is_plugin_active( $product['plugin_url'] ) ) {
						if ( ! empty( $product['requires'] ) ) {
							$requires = '&amp;requires=' . $product['requires'] . '&amp;requires_name=' . $product['requires_name'];
						} else {
							$requires = '';
						}
						echo '<a href="' . esc_url( admin_url( 'admin.php?page=wysija_config&amp;action=activate&amp;module=' . $product['plugin_url'] . $requires ) ) . '" class="button-primary activate">' . esc_attr__( 'Activate', WYSIJA ) . '</a>&nbsp;';
					} else {
						if ( ! empty( $product['config_url'] ) ) {
							echo '<a href="' . esc_url( $product['config_url'] ) . '" class="mailpoet-configure-button button-secondary">' . esc_attr__( 'Configure', WYSIJA ) . '</a>';
						}
					}
				}
			}

			echo
				'</div>' .
			'</div>';
		} // end if local is yes.

		echo
			'<div class="submit-idea">' .
				'<p>' . wp_kses( sprintf( __( 'Don\'t see the add-on you\'re looking for? <a href="%s">Submit it</a> in our contact form.', WYSIJA ), 'http://www.mailpoet.com/contact/" target="blank' ), array( 'a' => array( 'href' => array() ) ) ) . '</p>' .
			'</div>' .
		'</div>';
	}
} // end class

/**
 * This loads the add ons class and displays the page.
 *
 * @init_mail_poet_add_ons();
 * @add_ons_page();
 */
function load_add_ons_manager(){
	$mailpoet_add_ons = new MailPoet_Add_ons();
	$mailpoet_add_ons->init_mail_poet_add_ons();
	$mailpoet_add_ons->add_ons_page();
}
load_add_ons_manager();