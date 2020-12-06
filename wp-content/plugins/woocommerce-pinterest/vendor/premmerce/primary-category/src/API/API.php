<?php

namespace Premmerce\PrimaryCategory\API;

use Premmerce\PrimaryCategory\Model\Model;

/**
 * Class API
 * @package Premmerce\PrimaryCategory\API
 *
 * This class is handling API calls
 */
class API
{
	/**
	 * @var Model
	 */
	private $model;

	/**
	 * API constructor.
	 * @param Model $model
	 */
	public function __construct(Model $model)
	{
		$this->model = $model;
	}


	/**
	 * @param $productId
	 * @return int|null
	 */
	public function getProductPrimaryCategory($productId){
		return $this->model->getPrimaryCategoryId($productId);
	}

	/**
	 * @param $productId
	 * @param $categoryId
	 */
	public function updateProductPrimaryCategory($productId, $categoryId)
	{
		$this->model->updatePrimaryCategory($productId, $categoryId);
	}
}
