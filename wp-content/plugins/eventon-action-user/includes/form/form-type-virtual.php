<?php 
/**
 *
 */

echo $this->get_form_html(
	'_virtual',
	array(
		'type'=>'yesno',
		'yesno_args'=>array(
			'id'=>'_virtual',
			'input'=>true,
			'label'=>evo_lang('This is a virtual (online) event'),
			'var'=> ($this->EVENT && $this->EVENT->check_yn('_virtual')?'yes':'no'),
			'afterstatement'=>'evo_vir_fields'
		)
	)
);


echo "<div id='evo_vir_fields' class='row evoau_sub_formfield' style='display:".($this->EVENT && $this->EVENT->is_virtual()?'':'none')."'>";


// using evo elements

	echo EVO()->elements->get_element(
		array(
			'type'=>'text',
			'id'=> '_vir_url',
			'value'=> ($this->EVENT ? $this->EVENT->get_prop('_vir_url'):''),
			'name'=> evo_lang('Virtual Event URL'),
			'nesting'=>'row',			
		)
	);

	echo EVO()->elements->get_element(
		array(
			'type'=>'text',
			'id'=> '_vir_pass',
			'value'=> ($this->EVENT ? $this->EVENT->get_virtual_pass():''),
			'name'=> evo_lang('Event access Pass Information'),
			'nesting'=>'row',			
		)
	);

	echo EVO()->elements->get_element(
		array(
			'type'=>'dropdown',
			'id'=> '_vir_show',
			'name'=> evo_lang('When to show the above virtual event information on event card'),
			'tooltip'=> evo_lang('This will set when to show the virtual event link and access information on the event card'),
			'nesting'=>'row',
			'value'=> ($this->EVENT ? $this->EVENT->get_prop('_vir_show'):''),
			'options'=> apply_filters('evo_vir_show', array(
				'always'=> evo_lang('Always'),
				'10800'=> evo_lang('3 Hours before the event start'),	
				'7200'=> evo_lang('2 Hours before the event start'),	
				'3600'=> evo_lang('1 Hour before the event start'),	
				'1800'=> evo_lang('30 Minutes before the event start'),	
			)) 
		)
	);

	echo EVO()->elements->get_element(
		array(
			'type'=>'yesno_btn',
			'id'=> '_vir_hide',
			'value'=> ($this->EVENT && $this->EVENT->check_yn('_vir_hide')?'yes':'no'),
			'label'=> evo_lang('Hide above access information when the event is live'),
			'nesting'=>'row',			
		)
	);

	echo EVO()->elements->get_element(
		array(
			'type'=>'yesno_btn',
			'id'=> '_vir_nohiding',
			'value'=> ($this->EVENT && $this->EVENT->check_yn('_vir_nohiding')?'yes':'no'),
			'label'=> evo_lang('Disable redirecting and hiding virtual event link'),
			'tooltip'=> evo_lang('Enabling this will show virtual event link without hiding it behind a redirect url'),
			'nesting'=>'row',			
		)
	);

?>
	<div class='row evoau_sub_child_formfield'>
		<p class='sub_child_title'><b><?php evo_lang_e('Optional After Event Information');?></b></p>
<?php
	echo EVO()->elements->get_element(
		array(
			'type'=>'textarea',
			'id'=> '_vir_after_content',
			'value'=> ($this->EVENT ? $this->EVENT->get_prop('_vir_after_content'):''),
			'name'=> evo_lang('Content to show after event has taken place'),
			'nesting'=>'row',			
		)
	);

	echo EVO()->elements->get_element(
		array(
			'type'=>'dropdown',
			'id'=> '_vir_after_content_when',
			'name'=> evo_lang('When to show the above content on eventcard'),
			'value'=> ($this->EVENT ? $this->EVENT->get_prop('_vir_after_content_when'):''),
			'nesting'=>'row',
			'options'=> apply_filters('evo_vir_after_content_show',array(					
				'event_end'=> evo_lang('After event end time is passed'),
				'3600'=> evo_lang('1 Hour after the event has ended'),	
				'86400'=> evo_lang('1 Day after the event has ended'),	
			))
		)
	);
?>
	</div>

<input type='hidden' name='_virtual_type' value='other_live'/><?php 

echo "</div>";