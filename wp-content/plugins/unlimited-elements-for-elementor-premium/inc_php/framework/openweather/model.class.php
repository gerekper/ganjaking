<?php

abstract class UEOpenWeatherAPIModel{

	private $attributes;
	private $parameters;

	/**
	 * Create a new class instance.
	 *
	 * @param array $attributes
	 * @param array $parameters
	 *
	 * @return void
	 */
	private function __construct($attributes, $parameters = array()){

		$this->attributes = $attributes;
		$this->parameters = $parameters;
	}

	/**
	 * Transform list of items into models.
	 *
	 * @param array $items
	 * @param array $parameters
	 *
	 * @return array
	 */
	public static function transformAll($items, $parameters = array()){

		$data = array();

		foreach($items as $attributes){
			$data[] = self::transform($attributes, $parameters);
		}

		return $data;
	}

	/**
	 * Transform attributes into a model.
	 *
	 * @param array $attributes
	 * @param array $parameters
	 *
	 * @return static
	 */
	public static function transform($attributes, $parameters = array()){

		$model = new static($attributes, $parameters);

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

	/**
	 * Get the parameter value.
	 *
	 * @param string $key
	 * @param mixed $fallback
	 *
	 * @return mixed
	 */
	protected function getParameter($key, $fallback = null){

		$value = UniteFunctionsUC::getVal($this->parameters, $key, $fallback);

		return $value;
	}

}
