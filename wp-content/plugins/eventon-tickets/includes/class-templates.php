<?php
/**
 * Handlebars templates
 * @version
 */

class EVOTX_Temp{
	public function __construct(){
		add_action('evo_temp_evotx_view_attendees', array($this, 'view_attendees'));
	}

	function view_attendees(){
		?>			
		<div class='evotx'>
			<div class='evotx_filter'></div>

			<div class='eventedit_tix_attendee_list'>
			{{#each tickets}}
				<span class='evotxVA_ticket evotix_{{@key}} {{s}}' data-tn="{{@key}}">
					<span class='evotxVA_tn'>{{@key}}</span>
					<span class='evotxVA_data'>
						<span class='etxva_main'>
							<b>{{n}}</b>
							{{#ifCond oS "==" "completed"}}
							<span class='etxva_tag {{s}} evotx_status {{gCC}}' data-gc='{{gC}}' data-status='{{s}}' data-tid='{{@key}}' data-tiid='{{id}}'>{{s}}</span>
							{{/ifCond}}
							{{#ifCond ../source "==" "backend"}}{{#if payment_method}}<span class='etxva_tag' style='background-color:#f7f7f7'>{{payment_method}}</span>{{/if}}{{/ifCond}}
							<a href='{{urlE eU}}'  target='_blank' class='etxva_tag evotx_wcorderstatus {{oS}}'>{{oS}}</a>
						</span>
						<span class='etxva_other'>
						{{#each oD}}
							<span><em>{{noDash @key}}</em>: {{this}}</span>
						{{/each}}
						</span>
					</span>
				</span>
			{{/each}}
			</div>
		</div>
		<?php
	}
}
new EVOTX_Temp();