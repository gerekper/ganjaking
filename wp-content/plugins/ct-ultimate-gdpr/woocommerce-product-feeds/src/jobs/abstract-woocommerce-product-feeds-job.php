<?php

class AbstractWoocommerceProductFeedsJob {

	/**
	 * @var string  The hook used for this job.
	 */
	protected $action_hook = '';

	/**
	 * @var int The number of arguments our hooked function expects.
	 */
	protected $action_hook_arg_count = 1;

	/**
	 * Add our hook...
	 */
	public function __construct() {
		add_action( $this->action_hook, [ $this, 'task' ], 10, $this->action_hook_arg_count );
	}
}
