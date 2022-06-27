<?php
//documentation
echo '<div class="perfmatters-settings-section">';
	echo '<h2>' . __('Documentation', 'perfmatters') . '</h2>';
	echo '<div class="form-table">';
		echo '<div style="margin: 1em auto;">' . __('Need help? Check out our in-depth documentation. Every feature has a step-by-step walkthrough.', 'perfmatters') . '</div>';
		echo '<a class="button-secondary" href="https://perfmatters.io/docs/?utm_source=perfmatters&utm_medium=support-page&utm_campaign=documentation-cta" target="_blank">' . __('Documentation', 'perfmatters') . '</a>';
	echo '</div>';
echo '</div>';

//contact us
echo '<div class="perfmatters-settings-section">';
	echo '<h2>' . __('Contact Us', 'perfmatters') . '</h2>';
	echo '<div class="form-table">';
		echo '<div style="margin: 1em auto;">' . __('If you have questions or problems, please send us a message. We’ll get back to you as soon as possible.', 'perfmatters') . '</div>';
		echo '<a class="button-secondary" href="https://perfmatters.io/contact/?utm_source=perfmatters&utm_medium=support-page&utm_campaign=contact-us-cta" target="_blank">' . __('Contact Us', 'perfmatters') . '</a>';
	echo '</div>';
echo '</div>';

//faq
echo '<div class="perfmatters-settings-section">';
	echo '<h2>' . __('Frequently Asked Questions', 'perfmatters') . '</h2>';
	echo '<div class="form-table" style="display: inline-flex; flex-wrap: wrap;">';
		$faq_utm = '?utm_source=perfmatters&utm_medium=support-page&utm_campaign=faq';
		echo '<ul style="margin-right: 40px;">';	
			echo '<li><a href="https://perfmatters.io/docs/how-to-install-perfmatters/' . $faq_utm . '" target="_blank">' . __('How do I license activate the plugin?', 'perfmatters') . '</a></li>';
			echo '<li><a href="https://perfmatters.io/docs/update-perfmatters-plugin/' . $faq_utm . '" target="_blank">' . __('How do I update the plugin?', 'perfmatters') . '</a></li>';
			echo '<li><a href="https://perfmatters.io/docs/upgrade-license/' . $faq_utm . '" target="_blank">' . __('How do I upgrade my license?', 'perfmatters') . '</a></li>';
			echo '<li><a href="https://perfmatters.io/docs/changelog/' . $faq_utm . '" target="_blank">' . __('Where can I view the changelog?', 'perfmatters') . '</a></li>';
			echo '<li><a href="https://perfmatters.io/affiliate-program/' . $faq_utm . '" target="_blank">' . __('Where can I sign up for the affiliate program?', 'perfmatters') . '</a></li>';
		echo '</ul>';
		echo '<ul>';
			echo '<li><a href="https://perfmatters.io/docs/disable-scripts-per-post-page/' . $faq_utm . '" target="_blank">' . __('How do I disable scripts on a per post/page basis?', 'perfmatters') . '</a></li>';
			echo '<li><a href="https://perfmatters.io/docs/delay-javascript/' . $faq_utm . '" target="_blank">' . __('How do I delay JavaScript?', 'perfmatters') . '</a></li>';
			echo '<li><a href="https://perfmatters.io/docs/remove-unused-css/' . $faq_utm . '" target="_blank">' . __('How do I remove unused CSS?', 'perfmatters') . '</a></li>';
			echo '<li><a href="https://perfmatters.io/docs/lazy-load-wordpress/' . $faq_utm . '" target="_blank">' . __('How do I lazy load images and videos?', 'perfmatters') . '</a></li>';
			echo '<li><a href="https://perfmatters.io/docs/local-analytics/' . $faq_utm . '" target="_blank">' . __('How do I host Google Analytics locally?', 'perfmatters') . '</a></li>';
		echo '</ul>';
	echo '</div>';
echo '</div>';