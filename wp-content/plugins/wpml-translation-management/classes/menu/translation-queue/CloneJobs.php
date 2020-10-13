<?php

namespace WPML\TM\Menu\TranslationQueue;

use WPML_Element_Translation_Job;
use WPML_TM_Editors;
use WPML_TM_ATE_Jobs;
use WPML\TM\ATE\JobRecords;
use WPML_TM_ATE_API;

class CloneJobs {
	/**
	 * @var WPML_TM_ATE_Jobs
	 */
	private $ateJobs;

	/**
	 * @var WPML_TM_ATE_API
	 */
	private $apiClient;

	/**
	 * @param WPML_TM_ATE_Jobs $ateJobs
	 * @param WPML_TM_ATE_API $apiClient
	 */
	public function __construct( WPML_TM_ATE_Jobs $ateJobs, WPML_TM_ATE_API $apiClient ) {
		$this->ateJobs   = $ateJobs;
		$this->apiClient = $apiClient;
	}

	/**
	 * @param int $wpmlJobId
	 * @param WPML_Element_Translation_Job $jobObject
	 */
	public function cloneCompletedJob( $wpmlJobId, WPML_Element_Translation_Job $jobObject ) {
		if (
			wpml_tm_load_old_jobs_editor()->get( $wpmlJobId ) === WPML_TM_Editors::ATE
			&& (int) $jobObject->get_status_value() === ICL_TM_COMPLETE
		) {
			$ateJobId = $this->ateJobs->get_ate_job_id( $jobObject->get_id() );
			$result   = $this->apiClient->clone_job( $ateJobId, $jobObject );
			if ( $result ) {
				$this->ateJobs->store( $wpmlJobId, [ JobRecords::FIELD_ATE_JOB_ID => $result['id'] ] );
				$this->ateJobs->set_wpml_status_from_ate( $wpmlJobId, $result['ate_status'] );
			}
		}
	}
}