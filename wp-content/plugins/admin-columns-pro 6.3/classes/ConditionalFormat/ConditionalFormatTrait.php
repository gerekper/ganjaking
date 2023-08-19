<?php

namespace ACP\ConditionalFormat;

trait ConditionalFormatTrait {

	public function conditional_format(): ?FormattableConfig {
		return new FormattableConfig();
	}

}