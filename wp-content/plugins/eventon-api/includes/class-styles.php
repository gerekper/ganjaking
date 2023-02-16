<?php
/**
 * API Styles
 * @version 1.0.1
 */

class evosy_styles{
	function get_styles(){
	ob_start();
?>
	/* EVOAPI v.1.0.1 */
	.ajde_evcal_calendar.evoapi #evcal_list{
		border-color:#cdcdcd; 
		/*border-top:1px solid #e5e5e5;*/ 
		border-bottom:1px solid #e5e5e5; 
		overflow:hidden;
		border-radius:5px;
	}
	
	.ajde_evcal_calendar.evoapi .eventon_list_event:first-child{border-top:1px solid #e5e5e5;}
	.ajde_evcal_calendar.evoapi .eventon_list_event{margin:0; padding:0; overflow:hidden; position:relative;list-style-type:none;
		border-right: 1px solid #e5e5e5;
	}
	.ajde_evcal_calendar.evoapi .eventon_events_list .evcal_month_line{
		border-bottom: 1px solid #e5e5e5;
	}
	.ajde_evcal_calendar.evoapi .eventon_events_list .evcal_month_line p{
		text-transform: uppercase;
   		font-size: 18px;
   		font-weight:bold;
   		color:#808080;
   		margin:0; padding:10px 0;
	}
	.ajde_evcal_calendar.evoapi .desc_trig_outter{font-family:'open sans',arial; margin:0; border-bottom:1px solid #e5e5e5;}
	.ajde_evcal_calendar.evoapi .evcal_list_a{
		background-color:#fafafa;display:block; width:100%; position:relative;padding:10px 0 15px; overflow:hidden;border-left-width:3px; border-left-style:solid; min-height:85px;border-bottom:none;
		color:#6B6B6B;
		cursor:pointer;
		-webkit-transition: all .2s ease;
	    -moz-transition: all .2s ease;
	    -ms-transition: all .2s ease;
	    -o-transition: all .2s ease;
	    transition: all .2s ease;
	}
	.ajde_evcal_calendar.evoapi .eventon_list_event .evcal_list_a:hover{border-left-width:7px; background-color: #f4f4f4;}

	.ajde_evcal_calendar.evoapi #evcal_list .eventon_list_event .evcal_desc{padding:3px 15px 0 80px; margin-left:5px;margin-bottom: 0px; top: 0;display: block;}
	.ajde_evcal_calendar.evoapi #evcal_list .eventon_list_event .no_val .evcal_desc{padding-left:10px; color:#6B6B6B;}
	.ajde_evcal_calendar.evoapi #evcal_list .eventon_list_event .evcal_desc span.evcal_event_title{
		line-height:120%; padding-bottom:3px; font-weight:700;
	}
	.ajde_evcal_calendar.evoapi .eventon_events_list .eventon_list_event .evcal_desc span{display:block;}
	.ajde_evcal_calendar.evoapi #evcal_list .eventon_list_event .evcal_event_title{text-transform:uppercase; font-size:18px;}
	.ajde_evcal_calendar.evoapi #evcal_list .eventon_list_event .evcal_desc_info{font-size: 11px; line-height: 120%; padding-bottom: 2px; opacity: 0.7;}
	.ajde_evcal_calendar.evoapi .eventon_events_list .eventon_list_event .no_val .evcal_cblock { display: none;}
	.ajde_evcal_calendar.evoapi .evcal_desc .evcal_desc3 em { font-size: 10px;line-height: 110%;color: #797979;float: left;display: block;padding-right: 6px;}


	.ajde_evcal_calendar.evoapi .evcal_cblock .evo_start{
		float: left;
		font-size: 30px;
		clear:both;
	}
	.ajde_evcal_calendar.evoapi .evcal_cblock .evo_end{
		float: left;
		padding-left: 8px;
		font-size: 14px;
		position: relative;
	}
	.ajde_evcal_calendar.evoapi .evcal_cblock .evo_end:before{
		background-color: #ABABAB;
		height: 2px;
		width: 4px;
		content:"";
		position: absolute;
		display: block;
		left: 0px; top: 50%;
	}
	.ajde_evcal_calendar.evoapi .evcal_cblock .evo_start em.month, .evcal_cblock .evo_end em.month {
		font-size: 11px; font-weight: normal;display: block;
	}
	.ajde_evcal_calendar.evoapi .evcal_cblock .evo_end em.month{font-size: 8px;}
	.ajde_evcal_calendar.evoapi .evcal_cblock .evo_end em.year{font-size: 8px;}
	.ajde_evcal_calendar.evoapi .evcal_cblock em.time{font-size: 10px;}
	.ajde_evcal_calendar.evoapi .evcal_cblock .year{font-size: 10px; display: block;line-height: 10px; opacity: 0.7;}
	.ajde_evcal_calendar.evoapi .evcal_cblock .time, .ajde_evcal_calendar.evoapi .evcal_cblock .evo_end.only_time{display: none;}

	.ajde_evcal_calendar.evoapi .eventon_events_list .eventon_list_event .evcal_cblock em {font-style: normal;text-transform: uppercase;line-height: 110%;}
	.ajde_evcal_calendar.evoapi .eventon_events_list .eventon_list_event .evcal_cblock .evo_start .day{font-size:11px;display:block; font-weight:normal;}
	.ajde_evcal_calendar.evoapi .eventon_events_list .eventon_list_event .evcal_cblock .evo_end .day{display:block;font-size:8px;font-weight:normal;}

	.ajde_evcal_calendar.evoapi .eventon_events_list .eventon_list_event .evcal_cblock span {line-height: 85%; vertical-align: super;}


	.ajde_evcal_calendar.evoapi .eventon_events_list .eventon_list_event .evcal_desc span.evo_above_title span{    display: inline-block;color: #fff; background-color: #F79191;border-radius: 5px;padding: 3px 9px;margin-bottom: 4px;text-transform: uppercase;font-size: 12px; font-weight:700}

	.ajde_evcal_calendar.evoapi #evcal_list .evcal_cblock .evo_time, 
	.ajde_evcal_calendar.evoapi .eventon_events_list span.evcal_desc3 span.evocd_timer,
	.ajde_evcal_calendar.evoapi .eventon_events_list .evcal_desc .evcal_desc3 .evcal_desc3_rsvp
	{ display: none;}
	.ajde_evcal_calendar.evoapi .eventon_events_list .eventon_list_event .evcal_cblock {
	    background-color: transparent !important;color: #ababab;
	    font-size: 30px;
	    padding: 0px 8px 2px 13px;
	    font-weight: bold;
	    position: absolute; line-height: 110%; min-height: 30px;margin: 0 6px 0 0;left: 0;
	}
	.ajde_evcal_calendar.evoapi .evcal_cblock .evo_date .end {
	    float: left;
	    margin-left: 4px;
	    font-size: 14px;
	}
	.ajde_evcal_calendar.evoapi .evcal_cblock .evo_date .start em {
	    margin-left: 0; margin-top: 3px;
	}
	.ajde_evcal_calendar.evoapi .evcal_cblock .evo_date .end em {
	    font-size: 8px;margin-top: 3px;
	}
	.ajde_evcal_calendar.evoapi .evcal_desc .evcal_desc3 em i {   color: #c8c8c8;}
	.ajde_evcal_calendar.evoapi .eventon_events_list .eventon_list_event .ev_ftImg {
	    background-repeat: no-repeat;
	    width: 75px;
	    height: 65%;
	    max-height: 75px;
	    position: absolute;
	    margin: auto 0 auto 10px;
	    -webkit-background-size: cover;
	    -moz-background-size: cover;
	    -o-background-size: cover;
	    background-size: cover;
	    background-position: top center;
	    display: block;
	    border-radius: 5px;
	}
	.ajde_evcal_calendar.evoapi #evcal_list .eventon_list_event .hasFtIMG .evcal_desc {
	    padding-left: 170px;
	}
	.ajde_evcal_calendar.evoapi .eventon_events_list .eventon_list_event .hasFtIMG .evcal_cblock{
	    left: 90px;
	}
<?php
			return ob_get_clean();
		
	}
}