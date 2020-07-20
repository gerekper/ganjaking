<?php
$course = (!empty( $conditions[ $idx ]['courses'] ) ) ? $conditions[ $idx ]['courses'] : '';
$lesson = (!empty( $conditions[ $idx ]['lessons'] ) ) ? $conditions[ $idx ]['lessons'] : '';
?>
<div class="value-courses" style="display: none; margin: 5px 0 0 45px;">
	<select
		class="ajax-select2-init"
		name="conditions[<?php echo esc_attr( $idx ); ?>][courses][]"
		id="conditions_<?php echo esc_attr( $idx ); ?>_courses"
		multiple
		data-placeholder="<?php esc_attr_e( 'Any course', 'follow_up_emails' ); ?>"
		data-nonce="<?php echo esc_attr( wp_create_nonce( 'search-courses' ) ); ?>"
	>
	<?php
		if ( ! is_array( $course ) ) {
			$course = explode( ',', $course );
		}
		$course_ids = array_filter( array_map( 'absint', $course ) );

		foreach ( $course_ids as $course_id ) {
			$course_name = htmlspecialchars( wp_kses_post( get_the_title( $course_id ) ) );
	?>
		<option value="<?php echo esc_attr( $course_id ); ?>" selected><?php echo esc_html( $course_name ); ?></option>
	<?php
		}
	?>
	</select>
</div>
<div class="value-lessons" style="display: none; margin: 5px 0 0 45px;">
	<select
		class="ajax-select2-init"
		name="conditions[<?php echo esc_attr( $idx ); ?>][lessons][]"
		id="conditions_<?php echo esc_attr( $idx ); ?>_lessons"
		multiple
		data-placeholder="<?php esc_attr_e( 'Search for lessons&hellip;', 'follow_up_emails' ); ?>"
		data-nonce="<?php echo esc_attr( wp_create_nonce( 'search-lessons' ) ); ?>"
	>
	<?php
		if ( ! is_array( $lesson ) ) {
			$lesson = explode( ',', $lesson );
		}
		$lesson_ids = array_filter( array_map( 'absint', $lesson ) );

		foreach ( $lesson_ids as $lesson_id ) {
			$lesson_name = htmlspecialchars( wp_kses_post( get_the_title( $lesson_id ) ) );
	?>
		<option value="<?php echo esc_attr( $lesson_id ); ?>" selected><?php echo esc_html( $lesson_name ); ?></option>
	<?php
		}
	?>
	</select>
</div>
