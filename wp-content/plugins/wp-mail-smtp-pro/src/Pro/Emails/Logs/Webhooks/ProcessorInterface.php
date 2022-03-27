<?php

namespace WPMailSMTP\Pro\Emails\Logs\Webhooks;

/**
 * Interface ProcessorInterface.
 *
 * @since 3.3.0
 */
interface ProcessorInterface {

	/**
	 * Validate webhook incoming request.
	 *
	 * @since 3.3.0
	 *
	 * @param \WP_REST_Request $request Webhook request.
	 *
	 * @return bool
	 */
	public function validate( \WP_REST_Request $request );

	/**
	 * Handle webhook incoming request.
	 *
	 * @since 3.3.0
	 *
	 * @param \WP_REST_Request $request Webhook request.
	 *
	 * @return bool
	 */
	public function handle( \WP_REST_Request $request );
}
