<?php

namespace ACP\Type;

interface ActivationToken {

	/**
	 * @return string
	 */
	public function get_token();

	/**
	 * @return string
	 */
	public function get_type();

}