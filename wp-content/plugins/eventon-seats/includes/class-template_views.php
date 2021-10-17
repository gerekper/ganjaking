<?php
/**
 * Templates for seat
 */

class EVOST_Temp{

	public function __construct(){
		add_action('evo_temp_evost_seat_map', array($this, 'seat_map'), 10);
		add_action('evo_temp_evost_cart_seats', array($this, 'cart_seats'), 10);
		add_action('evo_temp_evost_tooltips', array($this, 'tooltips'), 10);
			
		add_filter('evo_init_templates', array($this,'init_temps'),10,1);

	}

	// templates load into page on initial eventon ajax call
	function init_temps($A){
		ob_start();
		$this->cart_seats();
		$A['evost_cart_seats'] = ob_get_clean();

		ob_start();
		$this->seat_map();
		$A['evost_seat_map'] = ob_get_clean();

		ob_start();
		$this->tooltips();
		$A['evost_tooltips'] = ob_get_clean();

		return $A;
	}


	// seat map
	function seat_map($is_admin= true){
		?>
			{{#each sections}}
			<span id='evost_section_{{@key}}' class='evost_section turn{{ang}} align_{{align}} type_{{type}} {{avail type capacity}} {{#ifE shape}}shape_{{shape}}{{/ifE}} <?php echo $is_admin? 'editable':'';?>' data-id='{{@key}}' data-ang='{{ang}}' data-index='{{section_index}}' data-name='{{section_name}}' tip='{{section_name}}' style='top:{{top}}px; left:{{left}}px; background-color:#{{bgc}}; {{#ifCOND bgcA '==' 'yes'}}background-color:transparent;{{/ifCOND}} {{#ifCOND brd '==' 'yes'}}border:none;{{/ifCOND}} {{#ifE h}}height:{{h}}px;{{/ifE}} {{#ifE w}}width:{{w}}px{{/ifE}}'>
				<u style='color:#{{fc}}'>{{section_name}}
					{{#ifCOND type "==" "aoi"}}
						{{#ifE icon}}
						<i class='fa {{icon}}'></i>
						{{/ifE}}
					{{/ifCOND}}
				</u>
				
				{{#ifCOND type "==" "def"}}
				{{#each rows}}
					<span class='evost_row' data-id='{{@key}}' data-index='{{row_index}}'>
						{{#each seats}}
							<span class='evost_seat seat {{status}} {{#ifCOND handicap "==" "yes"}}hand{{/ifCOND}}' data-id='{{id}}' data-sid='{{Par ../../this ../this @key}}' data-number='{{number}}'></span>
						{{/each}}
					</span>
				{{/each}}
				{{/ifCOND}}
			</span>
			{{/each}}
		<?php
	}

	// seats in cart on eventcard
	function cart_seats(){
		?>
		{{#ifCOND total_seats '>=' 1}}
		<p class="evost_tix_title"><?php evo_lang_e('Your Tickets In Cart');?></p>				
		<ul>
		{{#each seat}}
			<li id='{{@key}}' data-seat_slug='{{seat_slug}}' data-qty='{{seat_qty}}'>
				<span class="evost_remove_tix">x</span>
				<div class="evost_tix_stub_content" style="display:block">
					<div class="evost_tt_content">
						<div class="evost_ttc_data section">
							<span class="label"><?php evo_lang_e('SEC');?></span>
							<span class="value sectionvalue">{{section}}</span></div>
						{{#ifCOND seat_type "==" "seat"}}
							<div class="evost_ttc_data row">
								<span class="label"><?php evo_lang_e('ROW');?></span>
								<span class="value rowvalue">{{row}}</span></div>
							<div class="evost_ttc_data seat">
								<span class="label"><?php evo_lang_e('SEAT');?></span>
								<span class="value seatvalue">{{seat_number}}</span>
							</div>
						{{/ifCOND}}
					</div>
					{{#ifCOND seat_type "==" "unaseat"}}
						<div class="evost_tt_content unaseat_qty">
							<span><?php evo_lang_e('Number of Seats');?></span>
							<span>x {{seat_qty}}</span>
						</div>	
					{{/ifCOND}}
					{{#each otherdata}}
						<div class="evost_tt_data otherdata {{@key}}">
							<span class="label">{{label}}</span><span class="price">{{price}}</span>
						</div>
					{{/each}}
					<div class="evost_tt_data {{#ifE totalprice}}hastotal{{/ifE}}">
						<span class="label"><?php evo_lang_e('Ticket Price');?></span><span class="price">{{price}}</span>
					</div>
					{{#ifE totalprice}}
					<div class="evost_tt_data totalprice">
						<span class="label"><?php evo_lang_e('Total Price');?></span><span class="price">{{totalprice}}</span>
					</div>
					{{/ifE}}
				</div>
			</li>
		{{/each}}
		</ul>
		<div class='evost_cart_expirations'>
			<span data-s='{{exp_time_s}}'><?php evo_lang_e('Your seats will expire in');?> <b>{{exp_time}}</b></span>
		</div>		
		<div class="evost_stub_action">
			<span class='count'>{{total_seats}} <?php echo evo_lang('Seats');?></span>
			<span class="action"><a class="evcal_btn" href='<?php echo wc_get_cart_url() ;?>'><?php evo_lang_e('Buy Now');?></a></span>
		</div>
		{{/ifCOND}}
		<?php
	}

	// tool tips
	function tooltips(){
		?>
		<div class="evost_tt_content">
			<div class="evost_ttc_data section"><span class="label"><?php evo_lang_e('SEC');?></span><span class="value sectionvalue">{{section}}</span></div>
			<div class="evost_ttc_data row"><span class="label"><?php evo_lang_e('ROW');?></span><span class="value rowvalue">{{row}}</span></div>
			<div class="evost_ttc_data seat"><span class="label"><?php evo_lang_e('SEAT');?></span><span class="value seatvalue">{{seat}}</span></div>
		</div>
		{{#ifCOND type '==' 'seat'}}
		<div class="evost_section_information">
			<span>{{section_name}}</span>
			{{#ifCOND hand '==' true}}
			<span class="icon"><i class="fa fa-wheelchair"></i></span>
			{{/ifCOND}}
		</div>
		{{/ifCOND}}

		{{#ifCOND type '==' 'unaseat'}}
		<div class="evost_section_information_2"><span><?php evo_lang_e('Seats available');?></span><span>{{available}}</span></div>
		{{/ifCOND}}

		{{#ifCOND canbuy '==' true}}
		<div class="evost_tt_data">
			<span class="label"><?php evo_lang_e('Ticket Price');?></span><span class="price">{{price}}</span>
		</div>
		{{/ifCOND}}
		<?php
	}
}
new EVOST_Temp();