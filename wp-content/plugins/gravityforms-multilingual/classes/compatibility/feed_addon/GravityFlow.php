<?php

namespace GFML\Compatibility\FeedAddon;

use GFML_TM_API;
use Gravity_Flow;

class GravityFlow extends FeedAddon implements \IWPML_Action {

	/** @var \Gravity_Flow */
	private $gravityFlow;

	public function __construct( GFML_TM_API $gfml_tm_api, Gravity_Flow $gravityFlow ) {
		parent::__construct( $gfml_tm_api, 'flow' );
		$this->gravityFlow = $gravityFlow;
	}

	protected function getImportedFeeds( $form_id ) {
		return $this->gravityFlow->get_feeds( $form_id );
	}

	protected function getTranslatableKeys() {
		return [
			TranslatableKey::create( 'step_name', 'name' ),
			TranslatableKey::create( 'description' ),
			TranslatableKey::create( 'instructionsValue', 'instructions' ),
			TranslatableKey::create( 'rejection_notification_subject' ),
			TranslatableKey::create( 'rejection_notification_message' ),
			TranslatableKey::create( 'revert_notification_subject' ),
			TranslatableKey::create( 'revert_notification_message' ),
			TranslatableKey::create( 'assignee_notification_subject' ),
			TranslatableKey::create( 'assignee_notification_message' ),
			TranslatableKey::create( 'approval_notification_subject' ),
			TranslatableKey::create( 'approval_notification_message' ),
			TranslatableKey::create( 'workflow_notification_subject' ),
			TranslatableKey::create( 'workflow_notification_message' ),
			TranslatableKey::create( 'complete_notification_subject' ),
			TranslatableKey::create( 'complete_notification_message' ),
			TranslatableKey::create( 'in_progress_notification_subject' ),
			TranslatableKey::create( 'in_progress_notification_message' ),
			TranslatableKey::create( 'confirmation_messageValue', 'confirmation_message' ),
			// The conditional routing rules.
			TranslatableKey::create( [ 'routing', '[]', 'value' ], 'routing-[]' ),
			TranslatableKey::create( [ 'rejection_notification_routing', '[]', 'value' ], 'routing-rejection-[]' ),
			TranslatableKey::create( [ 'revert_notification_routing', '[]', 'value' ], 'routing-revert-[]' ),
			TranslatableKey::create( [ 'approval_notification_routing', '[]', 'value' ], 'routing-approval-[]' ),
			TranslatableKey::create( [ 'workflow_notification_routing', '[]', 'value' ], 'routing-workflow-[]' ),
			TranslatableKey::create( [ 'complete_notification_routing', '[]', 'value' ], 'routing-complete-[]' ),
			TranslatableKey::create( [ 'in_progress_notification_routing', '[]', 'value' ], 'routing-in_progress-[]' ),
		];
	}
}
