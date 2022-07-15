<?php

namespace WPMailSMTP\Pro\Alerts;

/**
 * Class Alert.
 *
 * @since 3.5.0
 */
class Alert {

	/**
	 * Alert type.
	 *
	 * @since 3.5.0
	 *
	 * @var string
	 */
	private $type;

	/**
	 * Alert data.
	 *
	 * @since 3.5.0
	 *
	 * @var array
	 */
	private $data;

	/**
	 * Constructor.
	 *
	 * @since 3.5.0
	 *
	 * @param string $type Alert type.
	 * @param array  $data Alert data.
	 */
	public function __construct( $type, $data ) {

		$this->type = $type;
		$this->data = $data;
	}

	/**
	 * Get alert type.
	 *
	 * @since 3.5.0
	 *
	 * @return string
	 */
	public function get_type() {

		return $this->type;
	}

	/**
	 * Get alert data.
	 *
	 * @since 3.5.0
	 *
	 * @return array
	 */
	public function get_data() {

		return $this->data;
	}
}
