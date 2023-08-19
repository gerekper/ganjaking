<?php

namespace ACA\ACF\ConditionalFormatting;

use ACA\ACF\Column;
use ACP\ConditionalFormat\FormattableConfig;
use LogicException;

trait FormattableTrait {

	protected $formatting_factory;

	public function set_formattable_factory( FormattableFactory $factory ): void {
		$this->formatting_factory = $factory;
	}

	public function conditional_format(): ?FormattableConfig {
		if ( ! $this->formatting_factory instanceof FormattableFactory ) {
			throw new LogicException( 'No valid FormatterFactory set' );
		}

		if ( ! $this instanceof Column ) {
			throw new LogicException( 'Trait can only be used in a %s class', Column::class );
		}

		return $this->formatting_factory->create( $this->get_field() );
	}

}