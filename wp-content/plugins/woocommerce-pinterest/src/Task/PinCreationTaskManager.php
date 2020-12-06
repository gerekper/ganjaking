<?php namespace Premmerce\WooCommercePinterest\Task;

use Premmerce\WooCommercePinterest\Model\PinModel;
use Premmerce\WooCommercePinterest\Pinterest\Api\ApiState;
use Premmerce\WooCommercePinterest\ServiceContainer;

/**
 * Class Scheduler
 * Responsible for scheduling queue processing in background
 *
 * @package Premmerce\WooCommercePinterest\Pinterest\Queue
 */
class PinCreationTaskManager extends AbstractTaskManager {

	/**
	 * ApiState instance
	 *
	 * @var ApiState
	 */
	private $apiState;

	/**
	 * Background process
	 *
	 * @var PinCreationBackgroundProcess
	 */
	private $backgroundProcess;

	/**
	 * PinModel
	 *
	 * @var PinModel
	 */
	private $pinModel;

	/**
	 * TaskManager constructor.
	 *
	 * @param ApiState $apiState
	 * @param PinCreationBackgroundProcess $backgroundProcess
	 * @param PinModel $pinModel
	 *
	 * @todo: check whether we need to instantiate and init this this on every query
	 */
	public function __construct( ApiState $apiState, PinCreationBackgroundProcess $backgroundProcess, PinModel $pinModel) {
		$this->apiState          = $apiState;
		$this->backgroundProcess = $backgroundProcess;
		$this->pinModel          = $pinModel;
	}

	/**
	 * Schedule background processing
	 */
	public function init() {
		add_action('init', array($this, 'schedule'));
		add_action('init', array($this, 'checkForDefer'));
	}

	public function schedule() {
		if ($this->apiState->canStartProcessing()) {
			$this->apiState->unScheduleBg();

			$pins = $this->pinModel
				->filterByCurrentUser()
				->filterPending()
				->orderBy('updated_at', 'ASC')
				->get(array('id', 'action'));

			if (!empty($pins)) {
				$this->backgroundProcess->kill_process();
				$this->backgroundProcess->delete_all_batches();

				foreach ($pins as $pin) {
					$this->backgroundProcess->push_to_queue($pin);
				}

				$this->backgroundProcess->save()->dispatch();
			}
		}
	}

	public function checkForDefer() {
		if ($this->pinModel->hasDeferPinning()) {

			$deferParams = ServiceContainer::getInstance()->getPinterestIntegration()->getDeferParams();

			$deferPins = $this->pinModel
				->filterByCurrentUser()
				->filterWaiting()
				->filterByProduceNow()
				->orderBy('produce_at', 'ASC');

			if ($deferParams['interval'] > 0) {

				$deferPins->limit((int) $deferParams['pins_per_interval']);

				$deferPins = $deferPins->get();

				$ids = array_column($deferPins, 'id');

				if (!empty($ids)) {
					// Update the rest pins. Add interval
					$this->pinModel->addIntervalToPins($ids, $deferParams['interval']);
				}

			} else {

				$deferPins = $deferPins->get();

				$ids = array_column($deferPins, 'id');
			}

			if (!empty($ids)) {
				// Update pin status to PENDING
				$this->pinModel->switchToPending($ids, PinModel::STATUS_PENDING, false);
			}

			$this->apiState->scheduleBg();
		}
	}
}
