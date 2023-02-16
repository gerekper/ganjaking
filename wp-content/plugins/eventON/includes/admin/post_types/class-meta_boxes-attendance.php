<?php
/**
 * Event Edit Meta box Attendance Mode
 * @4.2.3
 */

?>
<div class='evcal_data_block_style1 event_attendance_settings'>
	<div class='evcal_db_data'>
		<?php
		
		echo EVO()->elements->get_element( array(
			'type'=>'select_row',
			'row_class'=>'eatt_values',
			'name'=>'_attendance_mode',
			'value'=>	$EVENT->get_attendance_mode(),
			'options'=>	EVO()->cal->get_attendance_modes()
		));
		?>		
	</div>
</div>