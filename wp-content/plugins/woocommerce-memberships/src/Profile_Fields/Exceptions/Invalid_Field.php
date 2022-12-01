<?php
/**
 * WooCommerce Memberships
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2022, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\Memberships\Profile_Fields\Exceptions;

use SkyVerge\WooCommerce\PluginFramework\v5_10_13 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * An exception used to report profile field validation errors.
 *
 * @since 1.19.0
 */
class Invalid_Field extends Framework\SV_WC_Plugin_Exception {


	/** @var string validation error used when the slug is already used by another profile field definition */
	const ERROR_EXISTING_SLUG = 'existing_slug';

	/** @var string validation error used when the profile field definition does not have a valid name */
	const ERROR_INVALID_NAME = 'invalid_name';

	/** @var string validation error used when the field is not available for the membership's plan */
	const ERROR_INVALID_PLAN = 'invalid_plan';

	/** @var string validation error used when the slug does not match an existing profile field definition */
	const ERROR_INVALID_SLUG = 'invalid_slug';

	/** @var string validation error used when the profile field definition does not have a valid type */
	const ERROR_INVALID_TYPE = 'invalid_type';

	/** @var string validation error used when the profile field does not have a valid user ID */
	const ERROR_INVALID_USER = 'invalid_user';

	/** @var string validation error used when the profile field definition does not have a valid editable by configuration */
	const ERROR_INVALID_USER_TYPE = 'invalid_user_type';

	/** @var string validation error used when the value is not valid for the field */
	const ERROR_INVALID_VALUE = 'invalid_value';

	/** @var string validation error used when the profile field does not have a profile field definition */
	const ERROR_NO_DEFINITION = 'no_definition';

	/** @var string validation error used when a multi-choice profile field definition does not have options set */
	const ERROR_NO_OPTIONS = 'no_options';

	/** @var string validation error used when a user editable profile field definition does not have valid visibility configuration */
	const ERROR_NO_VISIBILITY = 'no_visibility';

	/** @var string validation error used when trying to clear a required field */
	const ERROR_REQUIRED_VALUE = 'required_value';


	/** @var string validation error code */
	protected $code;


	/**
	 * Constructor.
	 *
	 * @since 1.19.0
	 *
	 * @param string $message validation error message
	 * @param string $code validation error code
	 * @param \Throwable $previous the previous exception used for exception chaining
	 */
	public function __construct( $message, $code = '', $previous = null ) {

		parent::__construct( $message, 0, $previous );

		$this->code = $code;
	}


}
