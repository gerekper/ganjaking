<?php
/**
 * Email template for subscriber notification email to admin
 * 
 * You can edit this template by copying this file to 
 * ../wp-content/themes/yourtheme/eventon/subscriber/
 */
?>
<p>You have a new subscriber for your calendar!</p>
<?php
	if(!empty($args['subscriber_id'])){
		echo "<p>Subscriber: ".get_the_title($args['subscriber_id'])."</p>";
	}
?>

