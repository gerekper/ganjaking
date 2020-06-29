<?php

namespace WBCR\Factory_Freemius_111\Entities;

use stdClass;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @author Webcraftic <wordpress.webraftic@gmail.com>, Alex Kovalev <alex.kovalevv@gmail.com>
 * @link https://webcraftic.com
 * @copyright (c) 2018 Webraftic Ltd, Freemius, Inc.
 * @version 1.0
 */
class Scope extends Entity {
	
	/**
	 * @var string
	 */
	public $public_key;
	/**
	 * @var string
	 */
	public $secret_key;
	
	/**
	 * @param bool|stdClass $scope_entity
	 */
	function __construct( $scope_entity = false ) {
		parent::__construct( $scope_entity );
	}
}
