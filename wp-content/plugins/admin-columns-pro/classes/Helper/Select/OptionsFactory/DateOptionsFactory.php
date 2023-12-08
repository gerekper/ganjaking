<?php

namespace ACP\Helper\Select\OptionsFactory;

use AC\Helper\Select\Options;

interface DateOptionsFactory {

	public function create_label( string $value ): string;

	public function create_options( string $db_column ): Options;

}