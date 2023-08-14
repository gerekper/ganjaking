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
	 * Page tag.
	 *
	 * @since 4.2.0
     *
	 * @var string
	 */
	public $page = '';

	/**
	 * Tab tag.
	 *
	 * @since 4.0
     *
	 * @var string
	 */
	public $tab = '';

	/**
	 * Additional URL arguments.
	 *
	 * @since 4.3.0
	 *
	 * @var array
	 */
	public $query_args = [];

	/**
	 * Tab link.
	 *
	 * @since 4.0
     *
	 * @var string
	 */
	public $link = '';

	/**
	 * Tab label.
	 *
	 * @since 4.0
     *
	 * @var string
	 */
	public $label = '';

	/**
	 * Tab icon(s) as HTML class(es).
	 *
	 * @since 4.0
     *
	 * @var string
	 */
	public $icon = '';

	/**
	 * Is it a default tab?
	 *
	 * @since 4.2.0
     *
	 * @var bool
	 */
	public $is_default = false;

	/**
	 * Tab CSS classes.
	 *
	 * @since 4.0
     *
	 * @var string[]
	 */
	public $classes = [];

	/**
	 * NavTab constructor.
	 *
	 * @since 4.0
     *
	 * @param array $args Tab settings.
	 */
	public function __construct( array $args = [] ) {

		$defaults = [
			'page'       => '',
			'tab'        => '',
			'query_args' => [],
			'label'      => __( 'Settings', 'searchwp' ),
			'classes'    => '',
			'icon'       => '',
		];

		$args = wp_parse_args( $args, $defaults );

		$this->link       = '#';
		$this->classes    = [ 'swp-nav-link' ];
		$this->page       = sanitize_title_with_dashes( $args['page'] );
		$this->tab        = sanitize_title_with_dashes( $args['tab'] );
		$this->query_args = array_map( 'sanitize_title_with_dashes', $args['query_args'] );
		$this->label      = sanitize_text_field( $args['label'] );
		$this->icon       = sanitize_text_field( $args['icon'] );

        if ( isset( $args['is_default'] ) ) {
	        $this->is_default = (bool) $args['is_default'];
        } else {
	        $this->is_default = $this->tab === 'default';
        }

		if ( ! empty( $args['classes'] ) ) {
			$this->classes = array_merge( $this->classes, (array) $args['classes'] );
		}

		if ( ! empty( $this->page ) || ! empty( $this->tab ) ) {
			$this->build_link();
		}

		$this->check_for_active_state();

		add_action( 'searchwp\settings\nav\tab', [ $this, 'render' ] );
	}

	/**
	 * Render this Navigation Tab.
	 *
	 * @since 4.0
     *
	 * @return void
	 */
	public function render() {
        ?>
        <li class="swp-nav-item <?php echo esc_attr( implode( '-wrapper ', $this->classes ) . '-wrapper' ); ?>">
            <a href="<?php echo esc_url( $this->link ); ?>" class="<?php echo esc_attr( implode( ' ', $this->classes ) ); ?>">
	            <?php echo esc_html( $this->label ); ?>
	            <?php if ( ! empty( $this->icon ) ) : ?>
                    <span class="<?php echo esc_attr( $this->icon ); ?>"></span>
	            <?php endif; ?>
            </a>
        </li>
		<?php
	}

	/**
	 * Build the link for this Nav Tab.
	 *
	 * @since 4.0
     *
	 * @return void
	 */
	private function build_link() {

		wp_create_nonce( 'searchwp-tab-' . $this->tab );

		$this->classes[] = 'swp-nav-link-' . sanitize_title_with_dashes( $this->tab );

		$this->link = add_query_arg( [] );

		if ( ! empty( $this->page ) ) {
			$this->link = add_query_arg(
				[ 'page' => 'searchwp-' . $this->page ],
				admin_url( 'admin.php' )
			);
		}

		if ( ! empty( $this->tab ) && ! $this->is_default ) {
			$this->link = add_query_arg(
				[ 'tab' => $this->tab ],
				$this->link
			);
		}

		if ( ! empty( $this->query_args ) ) {
			$this->link = add_query_arg(
				$this->query_args,
				$this->link
			);
		}
	}

	/**
	 * Check whether this Nav Tab is active and if so add CSS class.
	 *
	 * @since 4.0
     *
	 * @return void
	 */
	private function check_for_active_state() {

		if (
			( empty( $this->tab ) && ! isset( $_GET['tab'] ) ) ||
			( $this->is_default && ! isset( $_GET['tab'] ) ) ||
			( isset( $_GET['tab'] ) && ! isset( $this->query_args['extension'] ) && $this->tab === $_GET['tab'] ) ||
			( isset( $_GET['extension'] ) && isset( $this->query_args['extension'] ) && $this->query_args['extension'] === $_GET['extension'] )
		) {
			$this->classes[] = 'swp-nav-link--active';
		}
	}
}
