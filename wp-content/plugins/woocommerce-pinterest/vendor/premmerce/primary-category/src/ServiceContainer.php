<?php


namespace Premmerce\PrimaryCategory;

use Premmerce\PrimaryCategory\Admin\Admin;
use Premmerce\PrimaryCategory\API\API;
use Premmerce\PrimaryCategory\Model\Model;

/**
 * Class ServiceContainer
 * @package Premmerce\PrimaryCategory
 *
 * Responsible for plugin's services instantiating and storing
 */
class ServiceContainer
{
	/**
	 * @var ServiceContainer
	 */
	protected static $instance;

	/**
	 * @var object[]
	 */
	protected $services = [];


	/**
	 * @return ServiceContainer
	 */
	public static function getInstance()
	{
		return static::$instance ? static::$instance : static::$instance = new static();
	}


	/**
	 * @param $mainFilePath
	 */
	public function initPrimaryCategory($mainFilePath)
	{
		if(! $this->serviceExists(PrimaryCategory::class))
		{
			$this->addService(PrimaryCategory::class, new PrimaryCategory($mainFilePath, $this));
		}
	}

	/**
	 * @return Admin
	 */
	public function getAdmin()
	{
		if(! $this->serviceExists(Admin::class)){
			$this->addService(Admin::class, new Admin($this->getModel()));
		}

		return $this->getService(Admin::class);
	}

	/**
	 * @return Model
	 */
	public function getModel()
	{
		if(! $this->serviceExists(Model::class)){
			$this->addService(Model::class, new Model());
		}

		return $this->getService(Model::class);
	}

    /**
     * @return EventsTracker
     */
	public function getEventsTracker()
    {
        if(! $this->serviceExists(EventsTracker::class)){
            $this->addService(EventsTracker::class, new EventsTracker($this->getModel()) );
        }

        return $this->getService(EventsTracker::class);
    }


	/**
	 * @return API
	 */
	public function getApi()
	{
		if(! $this->serviceExists(API::class)){
			$this->addService(API::class, new API($this->getModel()));
		}

		return $this->getService(API::class);
	}

	/**
	 * @param string $id
	 *
	 * @return mixed
	 */
	public function getService($id)
	{
		if ($this->serviceExists($id)) {
			return $this->services[$id];
		}
	}

	/**
	 * @param string $id
	 *
	 * @return bool
	 */
	public function serviceExists($id)
	{
		return isset($this->services[$id]);
	}

	/**
	 * @param string $id
	 * @param $service
	 */
	public function addService($id, $service)
	{
		$this->services[$id] = $service;
	}
}
