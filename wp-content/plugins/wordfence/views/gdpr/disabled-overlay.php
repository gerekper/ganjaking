<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
?>
<div id="wf-toupp-required-overlay"></div>
<div id="wf-toupp-required-message">
	<div id="wf-toupp-required-message-inner">
		<p><?php esc_html_e('We have updated our policies. To continue using Wordfence, you will need to read and agree to the Wordfence License Terms and Conditions, the Services Subscription Agreement, and Terms of Service, and read and acknowledge the Wordfence Privacy Policy by clicking Review.', 'wordfence'); ?></p>
		<p><a href="#" class="wf-btn wf-btn-default" onclick="jQuery('#wf-gdpr-review').trigger('click'); return false;" role="button"><?php esc_html_e('Review', 'wordfence'); ?></a></p>
	</div>
</div>