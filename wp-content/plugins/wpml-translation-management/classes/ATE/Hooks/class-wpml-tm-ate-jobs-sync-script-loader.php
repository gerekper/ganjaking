<?php

use WPML\TM\ATE\Download\Queue;
use WPML\TM\ATE\Sync\Trigger;
use WPML\TM\ATE\ReturnedJobsQueue;
use function WPML\FP\pipe;
use WPML\FP\Relation;
use WPML\FP\Fns;

class WPML_TM_ATE_Jobs_Sync_Script_Loader {

	const JS_HANDLER  = 'wpml-tm-ate-jobs-sync';
	const JS_VARIABLE = 'WPML_ATE_JOBS_SYNC';

	/** @var WPML_TM_Scripts_Factory */
	private $script_factory;

	/** @var WPML_TM_ATE_Job_Repository */
	private $ate_jobs_repository;

	/** @var Trigger $syncTrigger */
	private $syncTrigger;

	/** @var Queue $downloadQueue */
	private $downloadQueue;

	public function __construct(
		WPML_TM_Scripts_Factory $script_factory,
		WPML_TM_ATE_Job_Repository $ate_jobs_repository,
		Trigger $syncTrigger,
		Queue $downloadQueue
	) {
		$this->script_factory      = $script_factory;
		$this->ate_jobs_repository = $ate_jobs_repository;
		$this->syncTrigger         = $syncTrigger;
		$this->downloadQueue       = $downloadQueue;
	}


	public function load() {
		$jobsToSync = $this->ate_jobs_repository->get_jobs_to_sync();

		if (
			$jobsToSync->count()
			|| $this->syncTrigger->isSyncRequired()
			|| $this->downloadQueue->count()
		) {
			wp_register_script(
				self::JS_HANDLER,
				WPML_TM_URL . '/dist/js/ate/jobs-sync-app.js',
				[],
				WPML_TM_VERSION
			);

			$jobIds = $jobsToSync->map_to_property( 'translate_job_id' );

			// $isCompletedButNotDownloaded :: int->bool
			$isCompletedButNotDownloaded = pipe(
				[ ReturnedJobsQueue::class, 'getStatus' ],
				Relation::equals( ReturnedJobsQueue::STATUS_COMPLETED )
			);

			wp_localize_script( self::JS_HANDLER, self::JS_VARIABLE, [
				'jobIds'         => $jobIds,
				'completedInATE' => Fns::filter( $isCompletedButNotDownloaded, $jobIds ),
				'strings'        => [
					'tooltip' => __( 'Processing translation (could take a few minutes)',
						'wpml-translation-management' ),
					'status'  => __( 'Processing translation', 'wpml-translation-management' ),
				],
			] );

			wp_enqueue_script( self::JS_HANDLER );

			$this->script_factory->localize_script( self::JS_HANDLER );
		}
	}
}