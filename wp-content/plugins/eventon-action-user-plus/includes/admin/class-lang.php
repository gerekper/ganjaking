<?php
/**
* Language Data
*/

class EVOAUP_Lang{
	public function __construct(){
		add_filter('eventonau_language_fields', array($this,'language_additions'), 10, 1);
	}

	// Language
		function language_additions($array){
			$newarray = array(
					array('label'=>'ActionUser Plus','type'=>'subheader'),
					array('label'=>'Login to Submit or Purchase Submissions','var'=>1),
					array('label'=>'Purchase Submissions or Login to use your already purchased submission','var'=>1),
					array('label'=>'Login to use purchased submissions','var'=>1),
					array('label'=>'You do not have any paid submissions left','var'=>1),
					array('label'=>'You have used all your paid submissions','var'=>1),
					array('label'=>'Purchase event submissions using the below section. After purchased, please revisit this page to submit your purchased events.','var'=>1),
					array('label'=>'Price Per Event Submission','var'=>1),
					array('label'=>'Price','var'=>1),
					array('label'=>'Total Price','var'=>1),
					array('label'=>'Number of Event Submissions included','var'=>1),
					array('label'=>'Unlimited','var'=>1),
					array('label'=>'Event','var'=>1),
					array('label'=>'Events','var'=>1),
					array('label'=>'Included Event Fields','var'=>1),
					array('label'=>'General Event Submissions','var'=>1),
					array('label'=>'Quantity','var'=>1),
					array('label'=>'Add to Cart','var'=>1),
					array('label'=>'Successfully Added to cart','var'=>1),
					array('label'=>'View Cart','var'=>1),
					array('label'=>'Checkout','var'=>1),
					array('label'=>'Regular Submission','var'=>1),
					array('label'=>'Event Submission Level','var'=>1),
					array('label'=>'Event Submissions Qty','var'=>1),
					array('label'=>'Event Submissions Remaining','var'=>1),
					array('label'=>'Select Event Submission Type','var'=>1),
					array('label'=>'Submission Level Name','var'=>1),
					array('label'=>'In order to submit an event please select your purchased event submission level type','var'=>1),
					array('label'=>'Purchase additional event submission packages','var'=>1),
					array('label'=>'You can also purchase additional event submission packages from below list.','var'=>1),
					array('label'=>'Once order is placed and processed, please revisit this page to submit your purchased event.','var'=>1),
					array('label'=>'Your Event Submissions','var'=>1),
					array('label'=>'You can submit your purchased event submissions from below link.','var'=>1),
					array('label'=>'Submit Events','var'=>1),
					array('label'=>'Your order is still in process, once the order is completed you can submit your purchased event submissions','var'=>1),
					array('label'=>'Event date and time editing is disabled! Please contact us regarding date/time changes!','var'=>1),
			);

			// fields
			$FIELDS = EVOAU()->frontend->au_form_fields('additional');
			$newarray[] = array('label'=>'ActionUser Plus Fields','type'=>'subheader');
			foreach($FIELDS as $fieldkey=>$val){
				$newarray[] = array('label'=> $val[0],'var'=>1);
			}
			$newarray[]= array('type'=>'togend');

			$newarray[]= array('type'=>'togend');

			return array_merge($array, $newarray);
		}
}
new EVOAUP_Lang();