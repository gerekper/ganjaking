<?php

abstract class UEExchangeRateAPIModel{

	private $attributes;

	/**
	 * Create a new class instance.
	 *
	 * @param array $attributes
	 *
	 * @return void
	 */
	private function __construct($attributes){

		$this->attributes = $attributes;
	}

	/**
	 * Transform list of items into models.
	 *
	 * @param array $items
	 *
	 * @return array
	 */
	public static function transformAll($items){

		$data = array();

		foreach($items as $attributes){
			$data[] = self::transform($attributes);
		}

		return $data;
	}

	/**
	 * Transform attributes into a model.
	 *
	 * @param array $attributes
	 *
	 * @return static
	 */
	public static function transform($attributes){

		$model = new static($attributes);

		return $model;
	}

	/**
	 * Get the attribute value.
	 *
	 * @param string $key
	 * @param mixed $fallback
	 *
	 * @return mixed
	 */
	protected function getAttribute($key, $fallback = null){

		$value = UniteFunctionsUC::getVal($this->attributes, $key, $fallback);

		return $value;
	}

}
