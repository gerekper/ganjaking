<?php namespace Premmerce\WooCommercePinterest\Task;

use Premmerce\WooCommercePinterest\Logger\Logger;
use Premmerce\WooCommercePinterest\Model\PinModel;
use Premmerce\WooCommercePinterest\Pinterest\Api\Api;
use Premmerce\WooCommercePinterest\Pinterest\Api\PinterestApiException;
use Premmerce\WooCommercePinterest\Pinterest\PinDataGenerator;
use Premmerce\WooCommercePinterest\PinterestException;
use Premmerce\WooCommercePinterest\ServiceContainer;
use WC_Background_Process;

/**
 * Class BackgroundProcess
 * Responsible for background processing create, update and delete tasks
 *
 * @package Premmerce\WooCommercePinterest\Task
 *
 */
class PinCreationBackgroundProcess extends WC_Background_Process {


	/**
	 * Action
	 *
	 * @var string
	 */
	protected $action = 'woocommerce_pinterest_synchronization';

	/**
	 * Api instance
	 *
	 * @var Api
	 */
	private $api;

	/**
	 * PinModel instance
	 *
	 * @var PinModel
	 */
	private $model;

	/**
	 * PinDataGenerator instance
	 *
	 * @var PinDataGenerator
	 */
	private $generator;

	/**
	 * Logger instance
	 *
	 * @var Logger
	 */
	private $logger;

	/**
	 * BackgroundProcess constructor.
	 *
	 * @param Api $api
	 * @param PinModel $model
	 * @param PinDataGenerator $generator
	 * @param Logger $logger
	 */
	public function __construct( Api $api, PinModel $model, PinDataGenerator $generator, Logger $logger) {
		$this->api       = $api;
		$this->model     = $model;
		$this->generator = $generator;
		$this->logger    = $logger;

		parent::__construct();
	}


	/**
	 * Run task
	 *
	 * @param array $task
	 *
	 * @return bool|array
	 */
	public function task( $task ) {
		if ($this->api->getState()->isWaiting()) {
			$this->kill_process();

			$this->api->getState()->scheduleBg();

			return $task;
		}

		$pin = $this->model->find($task['id']);

		if (! $pin) {
			return false;
		}

		if ('update' === $task['action'] && ! $pin['pin_id']) {
			$task['action'] = 'create';
		}

    if( intval($pin['attachment_id']) === 0 && intval($pin['carousel_ids']) !== 0 ) {
      switch ($task['action']) {
        case 'create':
          return $this->runCreateCarousel($pin);
        case 'update':
          return $this->runUpdateCarousel($pin);
        case 'delete':
          return $this->runDelete($pin);
      }
    } else {
      switch ($task['action']) {
        case 'create':
          return $this->runCreate($pin);
        case 'update':
          return $this->runUpdate($pin);
        case 'delete':
          return $this->runDelete($pin);
      }
    }
	}

  /**
   * Create carousel using api and mark as ready
   *
   * @param array $pin
   *
   * @return false
   */
  public function runCreateCarousel( $pin ) {
    try {
      $carouselImages = $this->generator->generateCarouselImagesData($pin);
      $imagesResponse = $this->api->uploadCarouselImages($carouselImages);

      if ( $imagesResponse->isFailed() ) {
        return false;
      }

      $body         = $imagesResponse->getBody();
      $carouselData = $this->generator->generateCarouselData($pin, $body['data']);
      $response     = $this->api->createCarouselPin($carouselData);
      $responseData = $response->getData();
      $pinId        = $responseData['id'];

      $this->model->setPinCreated($pin['id'], $pinId);
    } catch (PinterestApiException $e) {
      $this->handleTaskException($e, $pin);
    } catch (PinterestException $e) {
      $this->handlePinDataGenerationException($e, $pin['id']);
    }

    return false;
  }

  /**
   * Update carousel using api and mark as ready
   *
   * @param array $pin
   *
   * @return false
   */
  public function runUpdateCarousel( $pin ) {
    try {
      $carouselData = $this->generator->generateCarouselData($pin, array());

      $this->api->updateCarouselPin($pin['pin_id'], $carouselData);

      $this->model->setPinSynchronized($pin['id']);
    } catch (PinterestApiException $e) {
      $this->handleTaskException($e, $pin);
    } catch (PinterestException $e) {
      $this->handlePinDataGenerationException($e, $pin['id']);
    }

    return false;
  }

	/**
	 * Create pin using api and mark as ready
	 *
	 * @param array $pin
	 *
	 * @return false
	 */
	public function runCreate( $pin) {
		try {
			$pinData = $this->generator->generateData($pin);

			$response = $this->api->createPin($pinData);

			$responseData = $response->getData();

			$pinId = $responseData['id'];

			$this->model->setPinCreated($pin['id'], $pinId);
		} catch (PinterestApiException $e) {
			$this->handleTaskException($e, $pin);
		} catch (PinterestException $e) {
			$this->handlePinDataGenerationException($e, $pin['id']);
		}

		return false;
	}

	/**
	 * Delete pin using api and delete pin from database
	 *
	 * @param array $pin
	 *
	 * @return false
	 */
	public function runDelete( $pin) {
		try {
			if (! empty($pin['pin_id'])) {
				$this->api->deletePin($pin['pin_id']);
			}

			$this->model->deleteSingleById($pin['id']);
		} catch (PinterestApiException $e) {
			if ($e->getCode() === 404) {
				$this->model->deleteSingleById($pin['id']);
			} else {
				$this->handleTaskException($e, $pin);
			}
		}

		return false;
	}


	/**
	 * Update pin using api and mark pin as ready
	 *
	 * @param array $pin
	 *
	 * @return false
	 */
	public function runUpdate( $pin) {
		try {
			$data = $this->generator->generateData($pin);

			$this->api->updatePin($pin['pin_id'], $data);

			$this->model->setPinSynchronized($pin['id']);
		} catch (PinterestApiException $e) {
			$this->handleTaskException($e, $pin);
		} catch (PinterestException $e) {
			$this->handlePinDataGenerationException($e, $pin['id']);
		}

		return false;
	}

	/**
	 * Set failed status and error data
	 *
	 * @param PinterestApiException $e
	 * @param array $pin
	 */
	protected function handleTaskException( PinterestApiException $e, $pin) {
		$this->logger->logPinterestException($e);

		if ($e->getCode() === Api::CODE_AUTH_FAILED) {
			$this->api->getState()->setApiAuthFailed();
		}

		if ($e->getCode() === Api::CODE_TOO_MANY_REQUESTS) {
			//Exceeded rate limit, wait for 1 hour
			$this->api->getState()->wait();
		}

		if ($e->getCode() >= 500) {
			//Api error, wait for 10 minutes
			$this->api->getState()->wait(600);
		}

		$this->model->setPinFailed($pin['id'], array(
			'code' => $e->getCode(), 'message' => $e->getMessage()
		));
	}

	protected function handlePinDataGenerationException( PinterestException $e, $pinId) {
		$this->logger->logPinterestException($e);

		ServiceContainer::getInstance()->getPinModel()->setPinFailed($pinId, 'Failed pin data generation. See details in Woocommerce logs.');
	}
}
