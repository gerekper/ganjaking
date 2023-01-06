<?php

namespace WCML\Utilities\Suspend;

interface Suspend {

	/**
	 * Manually resume the suspended logic.
	 *
	 * @return void
	 */
	public function resume();

	/**
	 * Run some function and automatically resume the suspended logic.
	 *
	 * @param callable $function
	 *
	 * @return mixed
	 */
	public function runAndResume( callable $function );
}
