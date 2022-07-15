<?php

namespace WPMailSMTP\Pro\Alerts;

use WPMailSMTP\Options;

/**
 * Abstract Class OptionsAbstract.
 *
 * @since 3.5.0
 */
abstract class AbstractOptions implements OptionsInterface {

	/**
	 * The provider slug.
	 *
	 * @since 3.5.0
	 *
	 * @var string
	 */
	private $slug = '';

	/**
	 * The provider title (or name).
	 *
	 * @since 3.5.0
	 *
	 * @var string
	 */
	private $title = '';

	/**
	 * The provider description.
	 *
	 * @since 3.5.0
	 *
	 * @var string
	 */
	private $description = '';

	/**
	 * The provider add connection button text.
	 *
	 * @since 3.5.0
	 *
	 * @var string
	 */
	private $add_connection_text = '';

	/**
	 * The provider maximum connections count. Unlimited by default.
	 *
	 * @since 3.5.0
	 *
	 * @var int
	 */
	private $max_connections_count = - 1;

	/**
	 * Plugin options object.
	 *
	 * @since 3.5.0
	 *
	 * @var Options
	 */
	protected $options;

	/**
	 * Constructor.
	 *
	 * @since 3.5.0
	 *
	 * @param array $params The options parameters.
	 */
	public function __construct( $params ) {

		if (
			empty( $params['slug'] ) ||
			empty( $params['title'] )
		) {
			return;
		}

		$this->slug  = sanitize_key( $params['slug'] );
		$this->title = $params['title'];

		if ( ! empty( $params['description'] ) ) {
			$this->description = $params['description'];
		}

		if ( ! empty( $params['add_connection_text'] ) ) {
			$this->add_connection_text = $params['add_connection_text'];
		}

		if ( ! empty( $params['max_connections_count'] ) ) {
			$this->max_connections_count = intval( $params['max_connections_count'] );
		}

		$this->options = Options::init();
	}

	/**
	 * Get the provider slug.
	 *
	 * @since 3.5.0
	 *
	 * @return string
	 */
	public function get_slug() {

		return $this->slug;
	}

	/**
	 * Get the provider title (or name).
	 *
	 * @since 3.5.0
	 *
	 * @return string
	 */
	public function get_title() {

		return $this->title;
	}

	/**
	 * Get the provider description.
	 *
	 * @since 3.5.0
	 *
	 * @return string
	 */
	public function get_description() {

		return $this->description;
	}

	/**
	 * Get the provider add connection button text.
	 *
	 * @since 3.5.0
	 *
	 * @return string
	 */
	public function get_add_connection_text() {

		return $this->add_connection_text;
	}

	/**
	 * Get the provider maximum connection count.
	 *
	 * @since 3.5.0
	 *
	 * @return int
	 */
	public function get_max_connections_count() {

		return $this->max_connections_count;
	}

	/**
	 * Get the provider options group.
	 *
	 * @since 3.5.0
	 *
	 * @return string
	 */
	public function get_group() {

		return 'alert_' . $this->get_slug();
	}
}
