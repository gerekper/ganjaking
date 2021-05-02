<?php

namespace ACP\Filtering\Markup;

use AC\Form\Element\Select;

class Dropdown extends Select {

	const OPTION_EMPTY = 'cpac_empty';
	const OPTION_NON_EMPTY = 'cpac_nonempty';
	const OPTION_FILTER = 'acp_filter';

	/**
	 * @var string
	 */
	private $empty;

	/**
	 * @var string
	 */
	private $nonempty;

	/**
	 * @var string
	 */
	private $order;

	public function __construct( $name, array $options = [] ) {
		parent::__construct( $name, $options );

		$this->set_id( 'acp-filter-' . $name )
		     ->set_name( sprintf( '%s[%s]', self::OPTION_FILTER, $name ) )
		     ->set_class( 'postform acp-filter' );
	}

	/**
	 * @return string
	 */
	public function get_empty() {
		return $this->empty;
	}

	/**
	 * @param null|string $label
	 *
	 * @return $this
	 */
	public function set_empty( $label = null ) {
		if ( null === $label ) {
			$label = __( 'Empty', 'codepress-admin-columns' );
		}

		$this->empty = $label;

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_nonempty() {
		return $this->nonempty;
	}

	/**
	 * @param null|$label
	 *
	 * @return $this
	 */
	public function set_nonempty( $label = null ) {
		if ( null === $label ) {
			$label = __( 'Not empty', 'codepress-admin-columns' );
		}

		$this->nonempty = $label;

		return $this;
	}

	/**
	 * @param string $order ASC (default) or DESC
	 *
	 * @return $this
	 */
	public function set_order( $order ) {
		if ( true === $order ) {
			$order = 'ASC';
		}

		if ( $order === 'ASC' || $order === 'DESC' ) {
			$this->order = $order;
		}

		return $this;
	}

	/**
	 * @return string
	 */
	public function get_order() {
		return $this->order;
	}

	private function sanitize_options() {
		$sanitized = [];

		foreach ( $this->options as $value => $label ) {
			if ( ! is_scalar( $label ) ) {
				continue;
			}

			// Prevent slowing down the DOM with too large strings
			if ( strlen( $value ) > 6000 ) {
				continue;
			}

			// No HTML
			$label = strip_tags( $label );

			if ( ! $label ) {
				$label = $value;
			}

			// Crop label to 100 characters
			if ( strlen( str_replace( '&nbsp;', '', $label ) ) > 100 ) {
				$label = substr( $label, 0, 97 ) . '...';
			}

			$sanitized[ $value ] = $label;
		}

		if ( $this->get_order() ) {
			natcasesort( $sanitized );

			if ( 'DESC' === $this->get_order() ) {
				$sanitized = array_reverse( $sanitized );
			}
		}

		$this->options = $sanitized;
	}

	/**
	 * @return string
	 */
	public static function get_disabled_prefix() {
		return '__ac_disabled_';
	}

	/**
	 * @param string $key
	 *
	 * @return bool
	 */
	private function is_disabled_key( $key ) {
		return false !== strpos( $key, self::get_disabled_prefix() );
	}

	/**
	 * @return bool
	 */
	private function has_empty_option() {
		return $this->get_empty() || $this->get_nonempty();
	}

	public function render() {
		$this->sanitize_options();

		if ( $this->has_empty_option() ) {
			if ( count( $this->options ) > 0 ) {
				$this->options[ self::get_disabled_prefix() . 'empty_divider' ] = '───────────';
			}

			if ( $this->get_empty() ) {
				$this->options[ self::OPTION_EMPTY ] = $this->get_empty();
			}

			if ( $this->get_nonempty() ) {
				$this->options[ self::OPTION_NON_EMPTY ] = $this->get_nonempty();
			}
		}

		if ( $this->get_label() ) {
			$this->options = [ '' => esc_html( $this->get_label() ) ] + $this->options;
		}

		if ( empty( $this->options ) ) {
			return;
		}

		$this->set_attribute( 'data-current', md5( $this->get_value() ) );

		?>

		<label for=" <?php echo esc_attr( $this->get_id() ); ?>" class="screen-reader-text">
			<?php printf( __( 'Filter by %s', 'codepress-admin-columns' ), $this->get_label() ); ?>
		</label>

		<?php

		echo parent::render();
	}

	protected function get_option_attributes( $key ) {
		$attributes = parent::get_option_attributes( $key );

		$attributes['value'] = htmlentities( $key, ENT_QUOTES, "utf-8" );
		$attributes['data-value'] = md5( $key );

		if ( $this->is_disabled_key( $key ) ) {
			$attributes['disabled'] = 'disabled';
		}

		return $attributes;
	}

}