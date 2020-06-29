<div class="options_group">

	<p class="form-field">
		<label><?php esc_html_e('Sending Delay', 'follow_up_emails'); ?></label>

		<span class="hide-if-date interval_span hideable">
			<?php
			$interval = ( $email->interval <= 0 ) ? 1 : $email->interval;
			?>
			<input type="number" min="1" step="1" name="interval" id="interval" value="<?php echo esc_attr( $interval ); ?>" size="2" style="vertical-align: top; width: 50px;" />
		</span>

		<select name="interval_duration" id="interval_duration" class="interval_duration hideable">
			<?php
			/* @var FUE_Email $email */
			$email_type = $email->get_email_type();
			$durations  = array();
			$triggers   = array();

			if ( $email_type ) {
				$durations  = $email_type->durations;
				$triggers   = $email_type->triggers;
			}

			foreach ( $durations as $key => $duration ):
				$selected = ($email->duration == $key) ? 'selected' : '';
				?>
				<option class="interval_duration_<?php echo esc_attr( $key ); ?> hideable" value="<?php echo esc_attr( $key ); ?>" <?php echo esc_attr( $selected ); ?>><?php echo esc_html( Follow_Up_Emails::get_duration( $key, $email->interval ) ); ?></option>
			<?php endforeach; ?>
		</select>

	</p>

	<p class="form-field">
		<label><?php esc_html_e('Trigger', 'follow_up_emails'); ?></label>

		<span class="hide-if-date interval_type_span hideable">
			&nbsp;
			<select name="interval_type" id="interval_type" class="interval_type hideable">
				<?php
				foreach ( $triggers as $key => $value ):
					$selected = ($email->trigger == $key) ? 'selected' : '';
					?>
					<option
						class="interval_type_option interval_type_<?php echo esc_attr( $email_type->id ); ?> interval_type_<?php echo esc_attr( $key ); ?> <?php if ( $key != 'purchase' && $key != 'completed' ) echo 'non-reminder'; ?> hideable <?php do_action('fue_form_interval_type', $key); ?>"
						value="<?php echo esc_attr($key); ?>"
						<?php echo esc_attr( $selected ); ?>
						>
						<?php echo esc_html( $value ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</span>
		<span class="show-if-date interval_date_span hideable">
			<input type="text" name="send_date" class="date" value="<?php echo esc_attr($email->send_date); ?>" readonly style="width: 100px;" />

			<select name="send_date_hour">
				<option value=""><?php esc_html_e('Hour', 'follow_up_emails'); ?></option>
				<?php
				for ( $x = 1; $x <= 12; $x++ ):
					$sel = ($email->send_date_hour == $x) ? 'selected' : '';
					?>
					<option value="<?php echo esc_attr( $x ); ?>" <?php echo esc_attr( $sel ); ?>><?php echo esc_html( $x ); ?></option>
				<?php endfor; ?>
			</select>

			<select name="send_date_minute">
				<option value=""><?php esc_html_e('Minute', 'follow_up_emails'); ?></option>
				<?php
				for ( $x = 0; $x <= 55; $x+=5 ):
					?>
					<option value="<?php echo esc_attr( $x ); ?>" <?php selected( $email->send_date_minute, $x ) ?>><?php echo esc_html( $x ); ?></option>
				<?php endfor; ?>
			</select>
			<?php
			$ampm = (isset($email->meta['send_date_ampm'])) ? $email->meta['send_date_ampm'] : 'am';
			?>
			<select name="meta[send_date_ampm]">
				<option value="am" <?php selected( $ampm, 'am' ); ?>>AM</option>
				<option value="pm" <?php selected( $ampm, 'pm' ); ?>>PM</option>
			</select>
		</span>

		<?php do_action('fue_email_form_interval_meta', $email); ?>
	</p>

	<p class="form-field hideable show-if-list_signup">
		<label><?php esc_html_e('List', 'follow_up_emails'); ?></label>

		<?php
		$selected_list = (empty( $email->meta['list'] ) ) ? 'any' : $email->meta['list'];
		?>
		<select name="meta[list]">
			<option value="any" <?php selected( $selected_list, 'any' ); ?>><?php esc_html_e('Any list', 'follow_up_emails'); ?></option>
			<?php foreach ( Follow_Up_Emails::instance()->newsletter->get_lists() as $list ): ?>
				<option value="<?php echo esc_attr( $list['id'] ); ?>" <?php selected( $selected_list, $list['id'] ); ?>><?php echo esc_html( $list['list_name'] ); ?></option>
			<?php endforeach; ?>
		</select>
	</p>

	<?php do_action('fue_email_form_trigger_fields', $email); ?>

	<?php

	if ( $email->supports( 'conditions' ) ):
		$conditions = fue_get_trigger_conditions( $email );

		if ( !empty( $conditions ) ):
	?>

	<div id="trigger_conditions" class="hide-if-date non-signup">
	     
		<?php
		$email_conditions = $email->conditions;

		if ( $email_conditions ):
			$i = 0;
			foreach ( $email_conditions as $idx => $email_condition ):
				if ( $idx === '_idx_' ) {
					continue;
				}
				?>
				<fieldset id="condition_<?php echo esc_attr( $idx ); ?>">

					<?php esc_html_e('AND', 'follow_up_emails'); ?>

					<select name="conditions[<?php echo esc_attr( $idx ); ?>][condition]" class="condition">
						<?php
						foreach ( $conditions as $key => $value ):
							?>
							<option
								value="<?php echo esc_attr( $key ); ?>"
								class="condition-<?php echo esc_attr( $key ); ?>"
								<?php selected( $email_condition['condition'], $key ); ?>
								>
								<?php echo esc_html( $value ); ?>
							</option>
						<?php endforeach; ?>
					</select>

					<?php
					do_action('fue_email_form_conditions_meta', $email, $idx);
					?>

					<a href="#" class="button btn-remove-condition">Delete</a>

				</fieldset>
				<?php
			endforeach;
		endif;
		?>

		<p>
			<a class="button button-primary btn-add-condition"><?php esc_html_e('Add Additional Rule', 'follow_up_emails'); ?></a>
		</p>
	</div>

	<div id="conditions_tpl" style="display: none;">
		<fieldset id="condition__idx_">

			<?php esc_html_e('AND', 'follow_up_emails'); ?>

			<select name="conditions[_idx_][condition]" class="condition" disabled>
				<?php
				foreach ( $conditions as $key => $value ):
					?>
					<option value="<?php echo esc_attr($key); ?>">
						<?php echo esc_html( $value ); ?>
					</option>
				<?php endforeach; ?>
			</select>

			<?php
			// addons adding their own input elements must set the 'disabled'
			// attribute to make sure that it doesn't get included in the POSTed data.
			// FUE automatically enables the elements once they are added to the form.
			do_action('fue_email_form_conditions_meta', $email, '_idx_');
			?>

			<a href="#" class="button btn-remove-condition">Delete</a>

		</fieldset>
	</div>
	<?php
		endif; // if ( !empty( $conditions ) )
	endif; // if ( $email->type == 'storewide' )
	?>

</div>
<script>jQuery(".date").datepicker();</script>