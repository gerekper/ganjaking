<?php

namespace ACA\JetEngine;

final class FieldFactory {

	public function create( array $settings ): ?Field\Field {
		$mapping = $this->get_mapping();

		return array_key_exists( $settings['type'], $mapping )
			? new $mapping[ $settings['type'] ]( $settings )
			: null;
	}

	private function get_mapping(): array {
		return [
			Field\Type\Checkbox::TYPE    => Field\Type\Checkbox::class,
			Field\Type\ColorPicker::TYPE => Field\Type\ColorPicker::class,
			Field\Type\Date::TYPE        => Field\Type\Date::class,
			Field\Type\DateTime::TYPE    => Field\Type\DateTime::class,
			Field\Type\Gallery::TYPE     => Field\Type\Gallery::class,
			Field\Type\IconPicker::TYPE  => Field\Type\IconPicker::class,
			Field\Type\Posts::TYPE       => Field\Type\Posts::class,
			Field\Type\Media::TYPE       => Field\Type\Media::class,
			Field\Type\Number::TYPE      => Field\Type\Number::class,
			Field\Type\Repeater::TYPE    => Field\Type\Repeater::class,
			Field\Type\Radio::TYPE       => Field\Type\Radio::class,
			Field\Type\Select::TYPE      => Field\Type\Select::class,
			Field\Type\Switcher::TYPE    => Field\Type\Switcher::class,
			Field\Type\Textarea::TYPE    => Field\Type\Textarea::class,
			Field\Type\Time::TYPE        => Field\Type\Time::class,
			Field\Type\Text::TYPE        => Field\Type\Text::class,
			Field\Type\Wysiwyg::TYPE     => Field\Type\Wysiwyg::class,
		];
	}

}