<div class="send-type-course send-type-div">
	<select id="course_ids" name="course_ids[]" class="select2" multiple data-placeholder="<?php esc_attr_e('Search for a course&hellip;', 'follow_up_emails'); ?>" style="width: 600px;">
		<?php foreach ($courses as $course): ?>
			<option value="<?php esc_attr_e($course->ID); ?>"><?php echo esc_html( get_the_title( $course ) ); ?></option>
		<?php endforeach; ?>
	</select>
</div>