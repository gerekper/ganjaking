<?php
/**
 * @license MIT
 *
 * Modified by woocommerce on 20-November-2023 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

declare(strict_types=1);

namespace Automattic\WooCommerce\Bookings\Vendor\GuzzleHttp\Promise;

/**
 * Exception that is set as the reason for a promise that has been cancelled.
 */
class CancellationException extends RejectionException
{
}
