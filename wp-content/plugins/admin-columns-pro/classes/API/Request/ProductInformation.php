<?php

namespace ACP\API\Request;

use ACP\API\Request;

/**
 * Used for displaying changelog information when clicking "view details" on the plugins page.
 */
class ProductInformation extends Request {

	/**
	 * @param string $plugin_name e.g. 'plugin-name'
	 */
	public function __construct( $plugin_name ) {
		parent::__construct( [
			'command'     => 'product_information',
			'plugin_name' => $plugin_name,
		] );
	}

}