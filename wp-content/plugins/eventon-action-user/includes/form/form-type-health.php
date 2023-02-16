<?php 
/**
 * Health Guidelines fields
 */

echo $this->get_form_html(
	'_health',
	array(
		'type'=>'yesno',
		'yesno_args'=>array(
			'id'=>'_health',
			'input'=>true,
			'label'=>evo_lang('Add Health Guidelines for this Event'),
			'var'=> ($this->EVENT && $this->EVENT->check_yn('_health')?'yes':'no'),
			'afterstatement'=>'evo_health_fields'
		)
	)
);

echo "<div id='evo_health_fields' class='row evoau_sub_formfield' style='display:".($this->EVENT && $this->EVENT->check_yn('_health')?'':'none')."'>";


// fields
	echo EVO()->elements->process_multiple_elements(
		array(	
		array(
			'type'=>'yesno_btn',
			'label'=> evo_lang('Face masks required'),
			'id'=> '_edata[_health_mask]',
			'value'=> ($this->EVENT? $this->EVENT->get_eprop("_health_mask"):'no'),
			'nesting'=>'row',		
		),array(
			'type'=>'yesno_btn',
			'label'=> evo_lang('Temperate will be checked at entrance'),
			'id'=> '_edata[_health_temp]',
			'value'=> ($this->EVENT? $this->EVENT->get_eprop("_health_temp"):'no'),
			'nesting'=>'row',		
		),array(
			'type'=>'yesno_btn',
			'label'=> evo_lang('Physical distance maintained event'),
			'id'=> '_edata[_health_pdis]',
			'value'=> ($this->EVENT? $this->EVENT->get_eprop("_health_pdis"):'no'),
			'nesting'=>'row',		
		),array(
			'type'=>'yesno_btn',
			'label'=> evo_lang('Event area sanitized before event'),
			'id'=> '_edata[_health_san]',
			'value'=> ($this->EVENT? $this->EVENT->get_eprop("_health_san"):'no'),
			'nesting'=>'row',		
		),array(
			'type'=>'yesno_btn',
			'label'=> evo_lang('Event is held outside'),
			'id'=> '_edata[_health_out]',
			'value'=> ($this->EVENT? $this->EVENT->get_eprop("_health_out"):'no'),
			'nesting'=>'row',		
		),array(
			'type'=>'textarea',
			'name'=> evo_lang('Other additional health guidelines'),
			'id'=> '_edata[_health_other]',
			'value'=> ($this->EVENT? $this->EVENT->get_eprop("_health_other"):''),
			'nesting'=>'row',		
		)
		)
	);



echo "</div>";
