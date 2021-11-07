<?php defined( 'ABSPATH' ) || die( 'This script cannot be accessed directly.' );


/**
 * Class GM_DebugPage
 */
class GM_DebugPage {
	/**
	 * Self object instance
	 *
	 * @var null|object
	 */
	private static $instance = null;


	/**
	 * Singleton self instance
	 *
	 * @return GM_DebugPage
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __clone() {
	}

	private function __construct() {
		add_action( 'gm_inside_debug_page_section', array( $this, 'infoVersions' ), 10 );
	}

	public function createPage() {
		if ( isset( $_GET['page'] ) && 'groovy_menu_debug_page' === $_GET['page'] ) { // @codingStandardsIgnoreLine
			add_action( 'admin_menu', array( $this, 'addDebugPage' ), 100 );
		}
	}

	public function addDebugPage() {

		// Add admin subpage.
		add_submenu_page(
			'tools.php',
			__( 'Groovy Menu debug page', 'groovy-menu' ),
			__( 'Groovy Menu debug page', 'groovy-menu' ),
			'edit_theme_options',
			'groovy_menu_debug_page',
			array( $this, 'debugPage' )
		);

	}

	public function addSection( $title, $decription, $content ) {
		?>
		<div class="gm-debug-section">
			<?php if ( ! empty( $title ) ): ?>
				<h3 class="gm-debug-section-title"><?php echo sprintf( '%s', $title ); ?></h3>
			<?php endif; ?>
			<?php if ( ! empty( $decription ) ): ?>
				<div class="gm-debug-section-desc"><?php echo sprintf( '%s', $decription ); ?></div>
			<?php endif; ?>
			<?php if ( ! empty( $content ) ): ?>
				<div class="gm-debug-section-content"><?php echo sprintf( '%s', $content ); ?></div>
			<?php endif; ?>
		</div>
		<?php
	}

	public function addList( $list ) {

		$output = '';

		if ( ! is_array( $list ) ) {
			return $output;
		}

		$output = '<div class="gm-debug-section-list">';
		foreach ( $list as $index => $value ) {

			if ( is_array( $value ) ) {
				$value = implode( ' ; ', $value );
			}

			$output .= '<div class="gm-debug-section-list-item gm-elem-index">' . sprintf( '%s', $index ) . '</div>';
			$output .= '<div class="gm-debug-section-list-item gm-elem-value">' . sprintf( '%s', $value ) . '</div>';
		}
		$output .= '</div>';

		return $output;

	}

	public function addListWithActions( $list ) {

		$output = '';

		if ( ! is_array( $list ) ) {
			return $output;
		}

		$output = '<div class="gm-debug-section-list">';
		foreach ( $list as $index => $value ) {

			if ( is_array( $value ) ) {
				$value = implode( ' ; ', $value );
			}

			$output .= '<div class="gm-debug-section-list-item gm-elem-index">' . sprintf( '%s', $index ) . '</div>';
			$output .= '<div class="gm-debug-section-list-item gm-elem-value">' . sprintf( '%s', $value ) . '</div>';
		}
		$output .= '</div>';

		return $output;

	}

	public function infoVersions() {

		global $wp_version;
		global $wp_db_version;

		$versions = array(
			'Current WordPress version'          => $wp_version,
			'Current WordPress DataBase version' => $wp_db_version,
			'Current plugin version'             => GROOVY_MENU_VERSION,
			'Current plugin DataBase version'    => get_option( GROOVY_MENU_DB_VER_OPTION ),
		);

		$content = $this->addList( $versions );

		$this->addSection(
			esc_html__( 'Versions info', 'groovy-menu' ),
			'',
			$content
		);

	}

	public function debugPage() {
		/**
		 * Fires before the groovy menu debug page output.
		 *
		 * @since 1.6.3
		 */
		do_action( 'gm_before_debug_page_output' );

		?>

		<div id="gm-debug-page" class="gm-debug-container">
			<div class="gm-debug-body">
				<h2><?php esc_html_e( 'Groovy Menu debug page', 'groovy-menu' ); ?></h2>
				<div class="gm-debug-body_inner">


					<?php

					/**
					 * Fires inside the groovy menu debug page output.
					 *
					 * @since 1.6.3
					 */
					do_action( 'gm_inside_debug_page_section' );

					?>


					<?php
					$this->addSection(
						esc_html__( 'Support', 'groovy-menu' ),
						'',
						sprintf( esc_html__( 'If you encounter migration problems or find any bugs, please create a ticket on our %s', 'groovy-menu' ),
							sprintf( '<a href="https://grooni.ticksy.com/" target="_blank">%s</a>', esc_html__( 'Support Portal', 'groovy-menu' ) )
						)
					);
					?>


				</div>
			</div>
		</div>


		<?php
		/**
		 * Fires after the groovy menu debug page output.
		 *
		 * @since 1.6.3
		 */
		do_action( 'gm_after_debug_page_output' );

	}


}
