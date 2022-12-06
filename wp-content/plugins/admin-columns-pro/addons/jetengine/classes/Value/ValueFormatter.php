<?php

namespace ACA\JetEngine\Value;

interface ValueFormatter {

	public function format( $raw_value ): ?string;

}