<?php

/**
 * SearchWP Notice.
 *
 * @package SearchWP
 * @author  Jon Christopher
 */

namespace SearchWP;

/**
 * Class Notice is responsible for storing a notice to be shown in the UI.
 *
 * @since 4.0
 */
class Notice implements \JsonSerializable {

	/**
	 * The message for this Notice.
	 *
	 * @since 4.0
	 * @var   string
	 */
	private $message = '';

	/**
	 * The tooltip for this Notice, shown after the message.
	 *
	 * @since 4.0
	 * @var   string
	 */
	private $tooltip = '';

	/**
	 * The type of this Notice. Accepted values include:
	 *     - ''
	 *     - 'success'
	 *     - 'error'
	 *     - 'warning'
	 *     - 'info'
	 *     - 'notice'
	 *
	 * @since 4.0
	 * @var   string
	 */
	private $type = '';

	/**
	 * The placement of this Notice in the UI. Accepted values include:
	 *     - ''
	 *     - 'details'
	 *
	 * @since 4.1
	 * @var   string
	 */
	public $placement = '';

	/**
	 * The icon of this Notice (i.e. HTML class).
	 *
	 * @since 4.0
	 * @var   string
	 */
	private $icon;

	/**
	 * Whether this notice can be dismissed. Can be FALSE or a callback to be fired when dismissed.
	 *
	 * @since 4.0
	 * @var   mixed
	 */
	private $dismissable = false;

	/**
	 * "More Info" link to find out more information about this Notice.
	 *
	 * @since 4.0
	 * @var array
	 */
	private $more;

	/**
	 * Option constructor.
	 *
	 * @since 4.0
	 * @param mixed  $value The value to store.
	 * @param string $label The label to use.
	 */
	function __construct( string $message = '', array $args = [] ) {
		$this->message = sanitize_text_field( $message );

		$defaults = array(
			'type'        => $this->type,
			'placement'   => $this->placement,
			'icon'        => $this->icon,
			'tooltip'     => $this->tooltip,
			'dismissable' => $this->dismissable,
			'more'        => $this->more,
		);

		$args = wp_parse_args( $args, $defaults );

		if ( ! empty( $args['type'] ) && in_array( $args['type'], [ 'success', 'error', 'warning', 'info', 'notice' ] ) ) {
			$this->type = $args['type'];
		}

		if ( ! empty( $args['placement'] ) && in_array( $args['placement'], [ 'details' ] ) ) {
			$this->placement = $args['placement'];
		}

		$this->icon    = sanitize_text_field( $args['icon'] );
		$this->tooltip = sanitize_text_field( $args['tooltip'] );

		if ( false !== $args['dismissable'] && is_callable( $args['dismissable'] ) ) {
			// FUTURE: Add AJAX endpoint that calls this function when a dismissable Notice is needed.
			$this->dismissable = $args['dismissable'];
		}

		if (
			! empty( $args['more'] )
			&& is_array( $args['more'] )
			&& isset( $args['more']['target'] )
			&& isset( $args['more']['text'] )
		) {
			$this->more = [
				'target' => $args['more']['target'],
				'text'   => $args['more']['text'],
			];
		}
	}

	/**
	 * Getter for message.
	 *
	 * @since 4.0
	 * @return string The message.
	 */
	public function get_message() {
		return $this->message;
	}

	/**
	 * Getter for type.
	 *
	 * @since 4.0
	 * @return mixed The type.
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Getter for icon.
	 *
	 * @since 4.0
	 * @return string The icon.
	 */
	public function get_icon() {
		return $this->icon;
	}

	/**
	 * Getter for tooltip.
	 *
	 * @since 4.0
	 * @return string The tooltip.
	 */
	public function get_tooltip() {
		return $this->tooltip;
	}

	/**
	 * Getter for more info array.
	 *
	 * @since 4.0
	 * @return string The more info array.
	 */
	public function get_more() {
		return $this->more;
	}

	/**
	 * Provides the model to use when representing this Notice as JSON.
	 *
	 * @since 4.0
	 * @return array
	 */
	public function jsonSerialize(): array {
		return [
			'message'   => $this->get_message(),
			'type'      => $this->get_type(),
			'placement' => $this->placement,
			'icon'      => $this->get_icon(),
			'tooltip'   => $this->get_tooltip(),
			'more'      => $this->get_more(),
		];
	}
}
