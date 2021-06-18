<?php

	// add custom message to a user profile
	add_action('userpro_before_form_submit', 'userpro_before_form_submit', 10);
	function userpro_before_form_submit($args){
		global $userpro;
		if ($args['template'] == 'register' && userpro_get_option('terms_agree') == 1 ) {
		
			?>
			
			<div class="userpro-column">
				<div class="userpro-field userpro-maxwidth" data-required="1" data-required_msg="<?php _e('You must accept our terms and conditions','userpro'); ?>">
					<div class="userpro-input">
					
						<div class='userpro-checkbox-wrap'>
							<label class='userpro-checkbox full'>
								<span></span>
								<input type='checkbox' name='terms' id="terms" /><?php echo html_entity_decode( userpro_get_option('terms_agree_text') ); ?>
							</label>
						</div>
						
					</div>
				</div>
			</div><div class="userpro-clear"></div>
			
			<?php
		
		}
	}