<?php
$course = (!empty( $conditions[ $idx ]['courses'] ) ) ? $conditions[ $idx ]['courses'] : '';
$lesson = (!empty( $conditions[ $idx ]['lessons'] ) ) ? $conditions[ $idx ]['lessons'] : '';
?>
<div class="value-courses" style="display: none; margin: 5px 0 0 45px;">
	<?php
	$course_ids = array_filter( array_map( 'absint', explode( ',', $course ) ) );
	$json_ids   = array();

	foreach ( $course_ids as $course_id ) {
		$json_ids[ $course_id ] = wp_kses_post( get_the_title( $course_id ) );
	}
	?>
	<input
		type="hidden"
		class="ajax-select2-init"
		name="conditions[<?php echo esc_attr( $idx ); ?>][courses]"
		id="conditions_<?php echo esc_attr( $idx ); ?>_courses"
		data-multiple="true"
		data-placeholder="<?php esc_attr_e('Any course', 'follow_up_emails'); ?>"
		style="width: 500px;"
		value="<?php echo esc_attr( implode( ',', array_keys( $json_ids ) ) ); ?>"
		data-selected="<?php echo wc_esc_json( wp_json_encode( $json_ids ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>"
		data-nonce="<?php echo esc_attr( wp_create_nonce("search-courses") ); ?>"
		>
</div>
<div class="value-lessons" style="display: none; margin: 5px 0 0 45px;">
	<?php
	$lesson_ids = array_filter( array_map( 'absint', explode( ',', $lesson ) ) );
	$json_ids   = array();

	foreach ( $lesson_ids as $lesson_id ) {
		$json_ids[ $lesson_id ] = wp_kses_post( get_the_title( $lesson_id ) );
	}
	?>
	<input
		type="hidden"
		class="ajax-select2-init"
		name="conditions[<?php echo esc_attr( $idx ); ?>][lessons]"
		id="conditions_<?php echo esc_attr( $idx ); ?>_lessons"
		data-multiple="true"
		data-placeholder="<?php esc_attr_e('Search for lessons...', 'follow_up_emails'); ?>"
		style="width: 500px;"
		value="<?php echo esc_attr( implode( ',', array_keys( $json_ids ) ) ); ?>"
		data-selected="<?php echo wc_esc_json( wp_json_encode( $json_ids ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>"
		data-nonce="<?php echo esc_attr( wp_create_nonce("search-lessons") ); ?>"
		>
</div>
