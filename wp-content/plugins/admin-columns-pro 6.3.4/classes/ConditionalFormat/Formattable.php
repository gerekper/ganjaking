<?php declare( strict_types=1 );

namespace ACP\ConditionalFormat;

interface Formattable {

	public function conditional_format(): ?FormattableConfig;

}