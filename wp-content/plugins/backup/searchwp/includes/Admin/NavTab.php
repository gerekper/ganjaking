<?php

/**
 * SearchWP NavTab.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP\Admin;

/**
 * Class NavTab is responsible for modeling an OptionsView nav tab.
 *
 * @since 4.0
 */
class NavTab {

	/**
	 * Tab tag.
	 *
	 * @since 4.0
	 * @var string
	 */
	public $tab = '';

	/**
	 * Tab link.
	 *
	 * @since 4.0
	 * @var string
	 */
	public $link = '';

	/**
	 * Tab label.
	 *
	 * @since 4.0
	 * @var string
	 */
	public $label = '';

	/**
	 * Tab icon(s) as HTML class(es).
	 *
	 * @since 4.0
	 * @var string
	 */
	public $icon = '';

	/**
	 * Tab CSS classes.
	 *
	 * @since 4.0
	 * @var string[]
	 */
	public $classes = '';

	/**
	 * NavTab constructor.
	 *
	 * @since 4.0
	 * @param mixed  $value The value to store.
	 * @param string $label The label to use.
	 */
	function __construct( array $args = [] ) {
		$defaults = [
			'tab'     => '',
			'label'   => __( 'Settings', 'searchwp' ),
			'classes' => '',
			'icon'    => '',
		];

		$args = wp_parse_args( $args, $defaults );

		$this->link    = '#';
		$this->classes = [ 'searchwp-settings-nav-tab' ];
		$this->tab     = sanitize_title_with_dashes( $args['tab'] );
		$this->label   = sanitize_text_field( $args['label'] );
		$this->icon    = sanitize_text_field( $args['icon'] );

		if ( ! empty( $args['classes'] ) ) {
			$this->classes = array_merge( $this->classes, (array) $args['classes'] );
		}

		$this->check_for_active_state();

		if ( ! empty( $this->tab ) ) {
			$this->build_link();
		}

		add_action( 'searchwp\settings\nav\tab', [ $this, 'render' ] );
	}

	/**
	 * Render this Navigation Tab.
	 *
	 * @since 4.0
	 * @return void
	 */
	public function render() {
		?>
		<li class="<?php echo esc_attr( implode( '-wrapper ', $this->classes ) . '-wrapper' ); ?>">
			<a href="<?php echo esc_url( $this->link ); ?>" class="<?php echo esc_attr( implode( ' ', $this->classes ) ); ?>">
				<span>
					<?php echo esc_html( $this->label ); ?>
					<?php if ( ! empty( $this->icon ) ) : ?>
						<span class="<?php echo esc_attr( $this->icon ); ?>"></span>
					<?php endif; ?>
				</span>
			</a>
		</li>
		<?php
	}

	/**
	 * Build the link for this Nav Tab.
	 *
	 * @since 4.0
	 * @return void
	 */
	private function build_link() {
		wp_create_nonce( 'searchwp-tab-' . $this->tab );

		$this->classes[] = 'searchwp-settings-nav-tab-' . sanitize_title_with_dashes( $this->tab );

		$this->link = add_query_arg(
			[ 'tab' => $this->tab, ],
			admin_url( 'options-general.php?page=searchwp' )
		);
	}

	/**
	 * Check whether this Nav Tab is active and if so add CSS class.
	 *
	 * @since 4.0
	 * @return void
	 */
	private function check_for_active_state() {
		if (
			( empty( $this->tab ) && ! isset( $_GET['tab'] ) ) ||
			( 'default' === $this->tab && ! isset( $_GET['tab'] ) ) ||
			( isset( $_GET['tab'] ) && $this->tab === $_GET['tab'] )
		) {
			$this->classes[] = 'searchwp-settings-nav-tab-active postbox';
		}
	}
}
