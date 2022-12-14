<?php
if (!defined('WORDFENCE_VERSION')) { exit; }
?>
<div id="wf-toupp-required-overlay"></div>
<div id="wf-toupp-required-message">
	<div id="wf-toupp-required-message-inner">
		<p><?php esc_html_e('You must install a license to continue using Wordfence.', 'wordfence'); ?></p>
		<p><a href="#" class="wf-btn wf-btn-default" onclick="window.location = jQuery('#wf-onboarding-resume').attr('href'); return false;" role="button"><?php esc_html_e('Resume Installation', 'wordfence'); ?></a></p>
	</div>
</div>