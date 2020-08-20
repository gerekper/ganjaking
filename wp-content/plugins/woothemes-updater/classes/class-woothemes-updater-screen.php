<?php
// File Security Check.
if ( ! defined( 'ABSPATH' ) ) exit;

class Woothemes_Updater_Screen {
	/**
	 * Generate header HTML.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public static function get_header ( $token = 'woothemes-updater', $screen_icon = 'tools' ) {
		do_action( 'woothemes_updater_screen_before', $token, $screen_icon );
		$html = '<div class="wrap woothemes-updater-wrap">' . "\n";
		$html .= get_screen_icon( $screen_icon );
		$html .= '<h2 class="nav-tab-wrapper">' . "\n";
		$html .= self::get_navigation_tabs();
		$html .= '</h2>' . "\n";
		echo $html;
		do_action( 'woothemes_updater_screen_header_before_content', $token, $screen_icon );
	} // End get_header()

	/**
	 * Generate footer HTML.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public static function get_footer ( $token = 'woothemes-updater', $screen_icon = 'tools' ) {
		do_action( 'woothemes_updater_screen_footer_after_content', $token, $screen_icon );
		$html = '</div><!--/.wrap woothemes-updater-wrap-->' . "\n";
		echo $html;
		do_action( 'woothemes_updater_screen_after', $token, $screen_icon );
	} // End get_footer()

	/**
	 * Generate navigation tabs HTML, based on a specific admin menu.
	 * @access  public
	 * @since   1.0.0
	 * @return  string/WP_Error
	 */
	public static function get_navigation_tabs ( $menu_key = 'woothemes' ) {
		$html = '';

		$screens = Woothemes_Updater_Screen::get_available_screens();

		$current_tab = self::get_current_screen();
		if ( 0 < count( $screens ) ) {
			foreach ( $screens as $k => $v ) {
				$class = 'nav-tab';
				if ( $current_tab == $k ) {
					$class .= ' nav-tab-active';
				}

				$url = add_query_arg( 'page', 'woothemes-helper', network_admin_url( 'index.php' ) );
				$url = add_query_arg( 'screen', $k, $url );
				$html .= '<a href="' . esc_url( $url ) . '" class="' . esc_attr( $class ) . '">' . esc_html( $v ) . '</a>';
			}
		}

		return $html;
	} // End get_navigation_tabs()

	/**
	 * Return the token for the current screen.
	 * @access  public
	 * @since   1.2.0
	 * @return  string The token for the current screen.
	 */
	public static function get_current_screen () {
		$screen = 'subscriptions'; // Default.
		if ( isset( $_GET['screen'] ) && '' != $_GET['screen'] ) $screen = esc_attr( $_GET['screen'] );
		return $screen;
	} // End get_current_screen()

	/**
	 * Return an array of available admin screens.
	 * @access  public
	 * @since   1.2.0
	 * @return  array Available admin screens.
	 */
	public static function get_available_screens () {
		return array(
			'subscriptions' => __( 'Subscriptions', 'woothemes-updater' ),
			'help' => __( 'Help', 'woothemes-updater' )
			);
	} // End get_available_screens()
} // End Class
?>