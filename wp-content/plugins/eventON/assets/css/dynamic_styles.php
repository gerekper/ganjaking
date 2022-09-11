<?php
/**
 * dynamic styles for front end
 *
 * @updated 	4.0.3
 * @package		eventon/Styles
 * @author 		AJDE
 */


	// Load variables
	$opt= get_option('evcal_options_evcal_1');
	

	// complete styles array
	$style_array = apply_filters('eventon_inline_styles_array', array(
		array(
			'item'=>'.eventon_events_list .eventon_list_event .desc_trig, .evopop_top',
			'css'=>'background-color:#$', 'var'=>'evcal__bgc4',	'default'=>'f1f1f1'
		),array(
			'item'=>'.eventon_events_list .eventon_list_event .desc_trig:hover',
			'css'=>'background-color:#$', 'var'=>'evcal__bgc4h',	'default'=>'fbfbfb'
		),

		array(
			'item'=>apply_filters('evo_styles_primary_font',
				'.ajde_evcal_calendar .calendar_header p,
				.ajde_evcal_calendar .evcal_evdata_row .evcal_evdata_cell h3.evo_h3,
				.evo_lightbox_content h3.evo_h3,
				body .ajde_evcal_calendar h4.evo_h4, 
				.evo_content_in h4.evo_h4,
				.evo_metarow_ICS .evcal_evdata_cell p a,
				.eventon_events_list .eventon_list_event .evoet_cx span.evcal_desc2, 
				.eventon_list_event .evoet_cx span.evcal_event_title,
				.evoet_cx span.evcal_desc2,	
				.evo_metarow_ICS .evcal_evdata_cell p a, 
				.evo_metarow_learnMICS .evcal_col50 .evcal_evdata_cell p a,
				.eventon_list_event .evo_metarow_locImg p.evoLOCtxt .evo_loc_text_title,		
				.evo_clik_row .evo_h3,
				.evotax_term_card .evotax_term_details h2, 
				.evotax_term_card h3.evotax_term_subtitle,
				.ajde_evcal_calendar .evo_sort_btn,
				.eventon_main_section  #evcal_cur,	
				.ajde_evcal_calendar .calendar_header p.evo_month_title,		
				.ajde_evcal_calendar .eventon_events_list .evcal_month_line p,
				.eventon_events_list .eventon_list_event .evcal_cblock,			
				.ajde_evcal_calendar .evcal_month_line,
				.eventon_event .event_excerpt_in h3,
				.ajde_evcal_calendar .evo_footer_nav p.evo_month_title,
				.evo_eventon_live_now_section h3,
				.evo_tab_view .evo_tabs p.evo_tab,
				.evo_metarow_virtual .evo_live_now_tag'),
			'css'=>'font-family:$', 
			'type'=>'font_name',
			'var'=>'evcal_font_fam',	
			'default'=>"roboto, 'arial narrow'"
		),
		array(
			'item'=>apply_filters('evo_styles_secondary_font',
				'.ajde_evcal_calendar .eventon_events_list p,
				.eventon_events_list .eventon_list_event .evoet_cx span, 
				.evo_pop_body .evoet_cx span,
				.eventon_events_list .eventon_list_event .evoet_cx span.evcal_event_subtitle, 
				.evo_pop_body .evoet_cx span.evcal_event_subtitle,
				.ajde_evcal_calendar .eventon_list_event .event_description .evcal_btn, 
				.evo_pop_body .evcal_btn, .evcal_btn,
				.eventon_events_list .eventon_list_event .cancel_event .evo_event_headers, 
				.evo_pop_body .evo_event_headers.canceled,
				.eventon_events_list .eventon_list_event .evcal_list_a .evo_above_title span,
				.evo_pop_body .evcal_list_a .evo_above_title span,
				.evcal_evdata_row.evcal_event_details .evcal_evdata_cell p,
				#evcal_list .eventon_list_event .evoInput, .evo_pop_body .evoInput,
				.evcal_evdata_row .evcal_evdata_cell p, 
				#evcal_list .eventon_list_event p.no_events,
				.ajde_evcal_calendar .eventon_events_list .eventon_desc_in ul,
				.evo_elms em.evo_tooltip_box,
				.evo_cal_above span,
				.ajde_evcal_calendar .calendar_header .evo_j_dates .legend,
				.eventon_sort_line p, .eventon_filter_line p, .evcal_evdata_row'),
			'css'=>'font-family:$', 
			'var'=>'evcal_font_fam_secondary',	
			'default'=>"'open sans', 'arial narrow'"
		),
		array(
			'item'=>'.ajde_evcal_calendar .evo_sort_btn, .eventon_sf_field p, .evo_srt_sel p.fa',
			'css'=>'color:#$', 'var'=>'evcal__sot',	'default'=>'B8B8B8'
		),array(
			'item'=>'.ajde_evcal_calendar .evo_sort_btn:hover',
			'css'=>'color:#$', 'var'=>'evcal__sotH',	'default'=>'d8d8d8'
		),array(
			'item'=>'#evcal_list .eventon_list_event .evoet_cx em,  .evo_lightboxes .evoet_cx em',
			'css'=>'color:#$', 'var'=>'evcal__fc6',	'default'=>'8c8c8c'
		),array(
			'item'=>'#evcal_list .eventon_list_event .evoet_cx em a',
			'css'=>'color:#$', 'var'=>'evcal__fc7',	'default'=>'c8c8c8'
		),

		// buttons
		array(
			'item'=> apply_filters('evo_appearance_button_elms','
				#evcal_list .eventon_list_event .event_description .evcal_btn, 
				body .evo_lightboxes .evo_lightbox_body.evo_pop_body .evcal_btn,
				.ajde_evcal_calendar .eventon_list_event .event_description .evcal_btn, 
				.evo_lightbox .evcal_btn, body .evcal_btn,
				.evo_lightbox.eventon_events_list .eventon_list_event a.evcal_btn,
				.evcal_btn'
			),
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'evcal_gen_btn_fc',	'default'=>'ffffff'),
				array('css'=>'background:#$', 'var'=>'evcal_gen_btn_bgc',	'default'=>'237ebd')
			)	
		),
		array(
			'item'=>apply_filters('evo_appearance_button_elms_hover',
				'#evcal_list .eventon_list_event .event_description .evcal_btn:hover, 
				body .evo_lightboxes .evo_lightbox_body.evo_pop_body .evcal_btn:hover,
				.ajde_evcal_calendar .eventon_list_event .event_description .evcal_btn:hover, 
				.evo_pop_body .evcal_btn:hover, .evcal_btn:hover,.evcal_evdata_row.evo_clik_row:hover'
			),
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'evcal_gen_btn_fcx',	'default'=>'fff'),
				array('css'=>'background:#$', 'var'=>'evcal_gen_btn_bgcx',	'default'=>'237ebd')
			)	
		),
		array(
			'item'=>apply_filters('evo_appearance_button_elms_color_hover',
				'.evcal_evdata_row.evo_clik_row:hover > *, .evcal_evdata_row.evo_clik_row:hover i'
			),
			'css'=>'color:#$', 'var'=>'evcal_gen_btn_fcx',	'default'=>'fff'
		),
		

		array(
			'item'=> '.eventon_list_event .evo_btn_secondary, 
				.ajde_evcal_calendar .eventon_list_event .event_description .evcal_btn.evo_btn_secondary,
				.evo_lightbox .evcal_btn.evo_btn_secondary, 
				body .evcal_btn.evo_btn_secondary,
				#evcal_list .eventon_list_event .event_description .evcal_btn.evo_btn_secondary,
				.evcal_btn.evo_btn_secondary,
				.evo_btn_secondary',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'evo_btn_2nd_c',	'default'=>'ffffff'),
				array('css'=>'background:#$', 'var'=>'evo_btn_2nd_bgc',	'default'=>'d2d2d2')
			)	
		),array(
			'item'=> '.eventon_list_event .evo_btn_secondary:hover, 
				.ajde_evcal_calendar .eventon_list_event .event_description .evcal_btn.evo_btn_secondary:hover,
				.evo_lightbox .evcal_btn.evo_btn_secondary:hover, 
				body .evcal_btn.evo_btn_secondary:hover,
				#evcal_list .eventon_list_event .event_description .evcal_btn.evo_btn_secondary:hover,
				.eventon_list_event .evo_btn_secondary:hover, 
				.evcal_btn.evo_btn_secondary:hover,
				.evo_btn_secondary:hover',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'evo_btn_2nd_ch',	'default'=>'ffffff'),
				array('css'=>'background:#$', 'var'=>'evo_btn_2nd_bgch',	'default'=>'bebebe')
			)	
		),

		array(
			'item'=>'.evcal_evdata_row .evcal_evdata_icons i, .evcal_evdata_row .evcal_evdata_custometa_icons i',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'evcal__ecI',	'default'=>'6B6B6B'),
				array('css'=>'font-size:$', 'var'=>'evcal__ecIz',	'default'=>'18px')
			)	
		),array(
			'item'=>'.evcal_evdata_row .evcal_evdata_cell h3, .evo_clik_row .evo_h3',
			'css'=>'font-size:$', 'var'=>'evcal_fs_001',	'default'=>'18px'
		),array(
			'item'=>'#evcal_list .eventon_list_event .evcal_cblock, .evo_lightboxes .evo_pop_body .evcal_cblock',
			'css'=>'color:#$', 'var'=>'evcal__fc2',	'default'=>'737373'
		),array(
			'item'=>'.evcal_evdata_row .evcal_evdata_cell h2, .evcal_evdata_row .evcal_evdata_cell h3, .evorow .evo_clik_row h3, 
			.evcal_evdata_row .evcal_evdata_cell h3 a',
			'css'=>'color:#$', 'var'=>'evcal__fc4',	'default'=>'6B6B6B'
		),array(
			'item'=>'#evcal_list .eventon_list_event .evcal_eventcard p, 
				.ajde_evcal_calendar .eventon_events_list .eventon_desc_in ul,
				.evo_lightboxes .evo_pop_body .evcal_evdata_row .evcal_evdata_cell p,
				.evo_lightboxes .evcal_evdata_cell p a' ,
			'css'=>'color:#$', 'var'=>'evcal__fc5',	'default'=>'656565'
		),
		array(
			'item'=>'.ajde_evcal_calendar #evcal_head.calendar_header #evcal_cur, .ajde_evcal_calendar .evcal_month_line p, .ajde_evcal_calendar .evo_footer_nav p.evo_month_title',
			'css'=>'color:#$', 'var'=>'evcal_header1_fc',	'default'=>'737373'
		),
		array(
			'name'=>'Event Card color',
			'item'=>'.eventon_events_list .eventon_list_event .event_description, .evo_lightbox.eventcard .evo_lightbox_body',
			'css'=>'background-color:#$', 'var'=>'evcal__bc1',	'default'=>'fdfdfd'
		),
		array(
			'item'=>'.evocard_box',
			'css'=>'background-color:#$', 'var'=>'evcal__bc1in',	'default'=>'f3f3f3'
		),array(
			'item'=>'.evcal_event_details .evcal_evdata_cell.shorter_desc .eventon_desc_in:after',
			'css'=>'background:linear-gradient(to top,	#$ 20%, #$00 80%)', 'var'=>'evcal__bc1in',	'default'=>'f3f3f3'
		)

		//border color for event card
			,array(
				'item'=>'.event_description .bordb, #evcal_list .bordb, .eventon_events_list .eventon_list_event .event_description, .bordr, #evcal_list,
					#evcal_list p.desc_trig_outter, 
					.evopop_top,
					.evo_pop_body .bordb',
				'css'=>'border-color:#$', 'var'=>'evcal__evcbrb',	'default'=>'d5d5d5'
			)
		//eventtop
		,array(
			'item'=>'.eventon_events_list .eventon_list_event .evcal_list_a.featured_event',
			'css'=>'background-color:#$', 'var'=>'evcal__bgc5',	'default'=>'fff6e2'
		),array(
			'item'=>'.eventon_events_list .eventon_list_event .evcal_list_a.featured_event:hover',
			'css'=>'background-color:#$', 'var'=>'evcal__bgc5h',	'default'=>'ffecc5'
		)
			/* featured events tag */
			,array(
				'item'=>'.eventon_events_list .eventon_list_event .evoet_cx span.evo_above_title span.featured, .evo_pop_body .evoet_cx span.evo_above_title span.featured',
				'multicss'=>array(
					array('css'=>'color:#$', 'var'=>'fs_eventtop_featured_2',	'default'=>'ffffff'),
					array('css'=>'background-color:#$', 'var'=>'fs_eventtop_featured_1',	'default'=>'ffcb55')
				)			
			)

			// live event progress
			,array(
				'item'=>'.evcal_desc3 .evo_ep_bar b, .evcal_desc3 .evo_ep_bar b:before',
				'css'=>'background-color:#$', 'var'=>'evoeventtop_live1',	'default'=>'f79191'
			),array(
				'item'=>'.evcal_desc3 .evo_ep_bar.evo_completed b',
				'css'=>'background-color:#$', 'var'=>'evoeventtop_live2',	'default'=>'9a9a9a'
			)

		// colorful eventtop text color			
			,array(
				'item'=>'.ajde_evcal_calendar.color #evcal_list .eventon_list_event .evcal_cblock, .ajde_evcal_calendar.color #evcal_list .eventon_list_event .evoet_cx span.evcal_event_title, 
				.ajde_evcal_calendar.color #evcal_list .eventon_list_event .evoet_cx span.evcal_event_subtitle, 
				.ajde_evcal_calendar.color #evcal_list .eventon_list_event .evoet_cx em, 
				.ajde_evcal_calendar.color #evcal_list .eventon_list_event .eventon_list_event .evoet_cx .evcal_desc_info, 
				.ajde_evcal_calendar.color .eventon_events_list .eventon_list_event .evcal_cblock em.evo_day, 
				.ajde_evcal_calendar.color .evoet_cx .evo_below_title .status_reason, 
				.ajde_evcal_calendar.color .evoet_cx .evo_tz_time .evo_tz, 
				.ajde_evcal_calendar.color .evoet_cx .evo_mytime.tzo_trig i, 
				.ajde_evcal_calendar.color .evoet_cx .evo_mytime.tzo_trig b, 
				.ajde_evcal_calendar.color .evoet_cx .evcal_desc3 em i, 
				.ajde_evcal_calendar.color .evoet_cx .evcal_desc3 .evo_ep_pre, 
				.ajde_evcal_calendar.color .evoet_cx .evcal_desc3 .evo_ep_time, 
				.ajde_evcal_calendar.color .evoet_cx .evo_mytime.tzo_trig:hover i, 
				.ajde_evcal_calendar.color .evoet_cx .evo_mytime.tzo_trig:hover b, 
				.evo_lightboxes .color.eventon_list_event .evoet_cx .evo_below_title .status_reason, 
				.evo_lightboxes .color.eventon_list_event .evcal_cblock, 
				.evo_lightboxes .color.eventon_list_event .evoet_cx span.evcal_event_title,
				 .evo_lightboxes .color.eventon_list_event .evoet_cx span.evcal_event_subtitle, 
				 .evo_lightboxes .color.eventon_list_event .evoet_cx em, 
				 .evo_lightboxes .color.eventon_list_event .evoet_cx .evcal_desc_info, 
				 .evo_lightboxes .color.eventon_list_event .evcal_cblock em.evo_day, 
				 .evo_lightboxes .color.eventon_list_event .evoet_cx .evo_tz_time > *,
				  .evo_lightboxes .color.eventon_list_event .evoet_cx .evo_mytime.tzo_trig i,
				  .evo_lightboxes .color.eventon_list_event .evoet_cx .evo_mytime.tzo_trig b',
				'css'=>'color:#$', 'var'=>'evcal__colorful_text',	'default'=>'ffffff',
			)

		// close button for eventcard
		,array(
			'item'=>'.event_description .evcal_close',
			'css'=>'background-color:#$', 'var'=>'evcal_closebtn',	'default'=>'f7f7f7'
		),array(
			'item'=>'.event_description .evcal_close:hover',
			'css'=>'background-color:#$', 'var'=>'evcal_closebtnx',	'default'=>'f1f1f1'
		)

		// close button on the lightbox
			,array(
				'item'=>'.evo_lightboxes .evopopclose, .evo_lightboxes .evolbclose',
				'css'=>'background-color:#$', 'var'=>'evo_color_lb_1',	'default'=>'000000'
			),array(
				'item'=>'.evo_lightboxes .evopopclose:hover, .evo_lightboxes .evolbclose:hover',
				'css'=>'background-color:#$', 'var'=>'evo_color_lb_3',	'default'=>'cfcfcf'
			),array(
				'item'=>'.evo_lightboxes .evolbclose:before, .evo_lightboxes .evolbclose:after',
				'css'=>'background-color:#$', 'var'=>'evo_color_lb_2',	'default'=>'666666'
			),array(
				'item'=>'.evo_lightboxes .evolbclose:hover:before, .evo_lightboxes .evolbclose:hover:after',
				'css'=>'background-color:#$', 'var'=>'evo_color_lb_4',	'default'=>'666666'
			)

		// get directions section
			,array(
				'item'=>'#evcal_list .evorow.getdirections, .evo_pop_body .evorow.getdirections',
				'css'=>'background-color:#$', 'var'=>'evcal_getdir_001',	'default'=>'ffffff'
			),array(
				'item'=>'#evcal_list .evorow.getdirections .evoInput, .evo_pop_body .evorow.getdirections .evoInput',
				'css'=>'color:#$', 'var'=>'evcal_getdir_002',	'default'=>'888888'
			),array(
				'item'=>'#evcal_list .evorow.getdirections .evcalicon_9 i, .evo_pop_body .evorow.getdirections .evcalicon_9 i',
				'css'=>'color:#$', 'var'=>'evcal_getdir_003',	'default'=>'858585'
			)

		,array(
			'name'=>'Event title color',
			'item'=>'.ajde_evcal_calendar.clean #evcal_list.eventon_events_list .eventon_list_event p .evoet_cx span.evcal_event_title,
				.evo_lightboxes .evo_pop_body .evoet_cx span.evcal_desc2',
			'css'=>'color:#$', 'var'=>'evcal__fc3',	'default'=>'6B6B6B'
		),array(
			'name'=>'Event sub title color',
			'item'=>'.eventon_events_list .eventon_list_event .evoet_cx span.evcal_event_subtitle, 
				.evo_lightboxes .evo_pop_body .evoet_cx span.evcal_event_subtitle',
			'css'=>'color:#$', 'var'=>'evcal__fc3st',	'default'=>'6B6B6B'
		),array(
			'item'=>'.fp_popup_option i',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'fp__f1',	'default'=>'999'),
				array('css'=>'font-size:$', 'var'=>'fp__f1b',	'default'=>'22px')
			)			
		),array(
			'item'=>'.evo_cal_above span',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'evcal__jm001',	'default'=>'ffffff'),
				array('css'=>'background-color:#$', 'var'=>'evcal__jm002',	'default'=>'ADADAD')
			)			
		),array(
			'item'=>'.evo_cal_above span:hover',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'evcal__jm001H','default'=>'ffffff'),
				array('css'=>'background-color:#$', 'var'=>'evcal__jm002H',	'default'=>'C8C8C8')
			)			
		),
		// this month button
			array(
				'item'=>'.evo_cal_above span.evo-gototoday-btn',
				'multicss'=>array(
					array('css'=>'color:#$', 'var'=>'evcal__thm001',	'default'=>'ffffff'),
					array('css'=>'background-color:#$', 'var'=>'evcal__thm002',	'default'=>'ADADAD')
				)			
			),array(
				'item'=>'.evo_cal_above span.evo-gototoday-btn:hover',
				'multicss'=>array(
					array('css'=>'color:#$', 'var'=>'evcal__thm001H','default'=>'ffffff'),
					array('css'=>'background-color:#$', 'var'=>'evcal__thm002H',	'default'=>'d3d3d3')
				)			
			),
		array(
			'item'=>'.ajde_evcal_calendar .calendar_header .evo_j_dates .legend a',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'evcal__jm003','default'=>'a0a09f'),
				array('css'=>'background-color:#$', 'var'=>'evcal__jm004',	'default'=>'f5f5f5')
			)			
		)
		,array(
			'item'=>'.ajde_evcal_calendar .calendar_header .evo_j_dates .legend a:hover',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'evcal__jm003H','default'=>'a0a09f'),
				array('css'=>'background-color:#$', 'var'=>'evcal__jm004H',	'default'=>'e6e6e6')
			)			
		),array(
			'item'=>'.ajde_evcal_calendar .calendar_header .evo_j_dates .legend a.current',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'evcal__jm006','default'=>'ffffff'),
				array('css'=>'background-color:#$', 'var'=>'evcal__jm007',	'default'=>'CFCFCF')
			)			
		),array(
			'item'=>'.ajde_evcal_calendar .calendar_header .evo_j_dates .legend a.set',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'evcal__jm008','default'=>'ffffff'),
				array('css'=>'background-color:#$', 'var'=>'evcal__jm009',	'default'=>'f79191')
			)			
		),array(
			'item'=>'.ajde_evcal_calendar .calendar_header .evcal_arrows, .evo_footer_nav .evcal_arrows',
			'multicss'=>array(
				array('css'=>'border-color:#$', 'var'=>'evcal__jm010','default'=>'737373'),
				array('css'=>'background-color:#$', 'var'=>'evcal__jm011','default'=>'ffffff'),				
			)			
		),array(
			'item'=>'.ajde_evcal_calendar .calendar_header .evcal_arrows:hover, .evo_footer_nav .evcal_arrows:hover',
			'multicss'=>array(
				array('css'=>'border-color:#$', 'var'=>'evcal__jm010H','default'=>'e2e2e2'),
				array('css'=>'background-color:#$', 'var'=>'evcal__jm011H','default'=>'e2e2e2'),			
			)			
		),array(
			'item'=>'.ajde_evcal_calendar .calendar_header .evcal_arrows:before,
	.evo_footer_nav .evcal_arrows:before',
			'css'=>'border-color:#$', 'var'=>'evcal__jm01A','default'=>'737373'
		),array(
			'item'=>'.ajde_evcal_calendar .calendar_header .evcal_arrows.evcal_btn_next:hover:before,
.ajde_evcal_calendar .calendar_header .evcal_arrows.evcal_btn_prev:hover:before,
	.evo_footer_nav .evcal_arrows.evcal_btn_prev:hover:before,
	.evo_footer_nav .evcal_arrows.evcal_btn_next:hover:before',
			'css'=>'border-color:#$', 'var'=>'evcal__jm01AH','default'=>'e2e2e2'
		)
		,array(
			'item'=>'.eventon_events_list .eventon_list_event .evoet_cx span.evo_above_title span, .evo_pop_body .evoet_cx span.evo_above_title span',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'fs_eventtop_tag_2','default'=>'ffffff'),
				array('css'=>'background-color:#$', 'var'=>'fs_eventtop_tag_1','default'=>'F79191'),			
			)			
		),
		array(
			'item'=>'.eventon_events_list .eventon_list_event .evoet_cx span.evo_above_title span.canceled, .evo_pop_body .evoet_cx span.evo_above_title span.canceled',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'evcal__cancel_event_2','default'=>'ffffff'),
				array('css'=>'background-color:#$', 'var'=>'evcal__cancel_event_1','default'=>'F79191'),
			)			
		),
		// postponed
		array(
			'item'=>'.eventon_events_list .eventon_list_event .evoet_cx span.evo_above_title span.postponed, .evo_pop_body .evoet_cx span.evo_above_title span.postponed',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'fs_eventtop_est_1b','default'=>'ffffff'),
				array('css'=>'background-color:#$', 'var'=>'fs_eventtop_est_1a','default'=>'e3784b'),
			)			
		),		
		// moved online
		array(
			'item'=>'.eventon_events_list .eventon_list_event .evoet_cx span.evo_above_title span.movedonline, .evo_pop_body .evoet_cx span.evo_above_title span.movedonline',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'fs_eventtop_est_2b','default'=>'ffffff'),
				array('css'=>'background-color:#$', 'var'=>'fs_eventtop_est_2a','default'=>'6edccd'),
			)			
		),
		// rescheduled
		array(
			'item'=>'.eventon_events_list .eventon_list_event .evoet_cx span.evo_above_title span.rescheduled, .evo_pop_body .evoet_cx span.evo_above_title span.rescheduled',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'fs_eventtop_est_3b','default'=>'ffffff'),
				array('css'=>'background-color:#$', 'var'=>'fs_eventtop_est_3a','default'=>'67ef78'),
			)			
		),

		// cancel event eventtop lines		
		array(
			'item'=>'.ajde_evcal_calendar .eventon_events_list .eventon_list_event .cancel_event.evcal_list_a, .evo_lightbox_body.eventon_list_event.cancel_event .evopop_top',
			'css'=>'background-color:#$', 'var'=>'evcal__cancel_event_4x','default'=>'464646',
		),array(
			'item'=>'.eventon_events_list .eventon_list_event .cancel_event.evcal_list_a:before',
			'css'=>'color:#$', 'var'=>'evcal__cancel_event_4x','default'=>'333333',
		),
		/* loader */
		array(
			'item'=>'#eventon_loadbar',
			'css'=>'background-color:#$', 'var'=>'evcal_loader_001','default'=>'efefef',
		),array(
			'item'=>'#eventon_loadbar:before',
			'css'=>'background-color:#$', 'var'=>'evcal_loader_002','default'=>'f5b87a',
		),
		/* event top */
		array(
			'item'=>'.evoet_cx .evcal_desc3 em.evocmd_button, #evcal_list .evoet_cx .evcal_desc3 em.evocmd_button',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'evoeventtop_cmd_btnA','default'=>'ffffff'),
				array('css'=>'background-color:#$', 'var'=>'evoeventtop_cmd_btn','default'=>'237dbd'),			
			)			
		),

		// repeat instance button
		array(
			'item'=>'.evo_repeat_series_dates span',
			'multicss'=>array(
				array('css'=>'color:#$', 'var'=>'evcal_repinst_btn_txt','default'=>'656565'),
				array('css'=>'background-color:#$', 'var'=>'evcal_repinst_btn','default'=>'dedede'),			
			)			
		),

		// single events
			array(
				'item'=>'.evo_metarow_socialmedia a.evo_ss:hover',
				'multicss'=>array(
					array('css'=>'background-color:#$', 'var'=>'evose_4','default'=>'9d9d9d'),
				)						
			),array(
				'item'=>'.evo_metarow_socialmedia a.evo_ss i',
				'multicss'=>array(
					array('css'=>'color:#$', 'var'=>'evose_1','default'=>'858585')
				)						
			),array(
				'item'=>'.evo_metarow_socialmedia a.evo_ss:hover i',
				'multicss'=>array(
					array('css'=>'color:#$', 'var'=>'evose_2','default'=>'ffffff')
				)						
			),array(
				'item'=>'.evo_metarow_socialmedia .evo_sm',
				'css'=>'border-color:#$', 'var'=>'evose_5','default'=>'cdcdcd'
			),

		// load more
			array(
				'item'=>'.eventon_events_list .evoShow_more_events span','multicss'=>array(
					array('css'=>'background-color:#$', 'var'=>'evo_001a','default'=>'b4b4b4'),
					array('css'=>'color:#$', 'var'=>'evo_001b','default'=>'ffffff')
				)	
			),

		// health guidelines
			array(
				'item'=>'.evo_card_health_boxes .evo_health_b','multicss'=>array(
					array('css'=>'background-color:#$', 'var'=>'evo_health_1','default'=>'ececec'),
					array('css'=>'color:#$', 'var'=>'evo_health_2','default'=>'8d8d8d')
				)	
			),
			array(
				'item'=>'.evo_card_health_boxes .evo_health_b svg, .evo_card_health_boxes .evo_health_b i.fa',
				'multicss'=>array(
					array('css'=>'fill:#$', 'var'=>'evo_health_3','default'=>'8d8d8d'),
					array('css'=>'color:#$', 'var'=>'evo_health_3','default'=>'8d8d8d')
				)	
			),
			array(
				'item'=>'.evo_health_b.ehb_other',
				'css'=>'border-color:#$', 'var'=>'evo_health_4','default'=>'e8e8e8'
			),

		// timezone
			array(
				'item'=>'.eventon_list_event .evoet_cx em.evo_mytime.tzo_trig i
				',
				'css'=>'color:#$', 'var'=>'evo_tzoa','default'=>'2eb4dc'
			),
			array(
				'item'=>'.eventon_list_event .evoet_cx em.evo_mytime,
				.eventon_list_event .evoet_cx em.evo_mytime.tzo_trig:hover,
				.eventon_list_event .evoet_cx em.evo_mytime,
				.eventon_list_event .evcal_evdata_cell .evo_mytime,
				.eventon_list_event .evcal_evdata_cell .evo_mytime.tzo_trig',
				'css'=>'background-color:#$', 'var'=>'evo_tzoa','default'=>'2eb4dc'
			),
			array(
				'item'=>'.eventon_list_event .evoet_cx em.evo_mytime >*, 
				.eventon_list_event .evcal_evdata_cell .evo_mytime >*,
				.eventon_list_event .evoet_cx em.evo_mytime.tzo_trig:hover >*',
				'css'=>'color:#$', 'var'=>'evo_tzob','default'=>'ffffff'
			),
		// repeat header
			array(
				'item'=>'.eventon_events_list .evose_repeat_header span.title',
				'multicss'=>array(
					array('css'=>'background-color:#$', 'var'=>'evo_rep_1','default'=>'fed584'),
					array('css'=>'color:#$', 'var'=>'evo_rep_1c','default'=>'808080')
				)				
			),
			array(
				'item'=>'.eventon_events_list .evose_repeat_header p',
				'css'=>'background-color:#$', 'var'=>'evo_rep_2','default'=>'ffe3ad'
			),
			array(
				'item'=>'.eventon_events_list .evose_repeat_header .ri_nav a, 
				.eventon_events_list .evose_repeat_header .ri_nav a:visited, 
				.eventon_events_list .evose_repeat_header .ri_nav a:hover',
				'css'=>'color:#$', 'var'=>'evo_rep_2c','default'=>'808080'
			),
			array(
				'item'=>'.eventon_events_list .evose_repeat_header .ri_nav b',
				'css'=>'border-color:#$', 'var'=>'evo_rep_2c','default'=>'808080'
			),
		// search
			array(
				'item'=>'body .EVOSR_section a.evo_do_search, body a.evosr_search_btn, .evo_search_bar_in a.evosr_search_btn',
				'css'=>'color:#$', 'var'=>'evosr_4',	'default'=>'3d3d3d'
			),array(
				'item'=>'body .EVOSR_section a.evo_do_search:hover, body a.evosr_search_btn:hover, .evo_search_bar_in a.evosr_search_btn:hover',
				'css'=>'color:#$', 'var'=>'evosr_5',	'default'=>'bbbbbb'
			),array(
				'item'=>'.EVOSR_section input, .evo_search_bar input','multicss'=>array(
					array('css'=>'border-color:#$', 'var'=>'evosr_1','default'=>'ededed'),
					array('css'=>'background-color:#$', 'var'=>'evosr_2','default'=>'ffffff')
				)	
			),
			array(
				'item'=>'.evosr_blur','multicss'=>array(
					array('css'=>'background-color:#$', 'var'=>'evosr_6','default'=>'f9d789'),
					array('css'=>'color:#$', 'var'=>'evosr_7','default'=>'14141E')
				)	
			),
			array(
				'item'=>'.evosr_blur','multicss'=>array(
					array('css'=>'background-color:#$', 'var'=>'evo_001a','default'=>'e6e6e6'),
					array('css'=>'color:#$', 'var'=>'evo_001b','default'=>'ffffff')
				)	
			),
			array(
				'item'=>'.evo_search_results_count span','multicss'=>array(
					array('css'=>'background-color:#$', 'var'=>'evosr_9','default'=>'d2d2d2'),
					array('css'=>'color:#$', 'var'=>'evosr_10','default'=>'f9d789')
				)	
			),array(
				'item'=>'.EVOSR_section input:hover, .evo_search_bar input:hover',
				'css'=>'color:#$', 'var'=>'evosr_3',	'default'=>'c5c5c5'
			),array(
				'item'=>'.evo_search_results_count',
				'css'=>'color:#$', 'var'=>'evosr_8',	'default'=>'14141E'
			),

		// Live Now calendar
			array(
				'item'=>'.evo_eventon_live_now_section h3','css'=>'color:#$', 'var'=>'evo_live1b',	'default'=>'8e8e8e'
			),array(
				'item'=>'.evo_eventon_live_now_section .evo_eventon_now_next','css'=>'background-color:#$', 'var'=>'evo_live2',	'default'=>'ececec'
			),array(
				'item'=>'.evo_eventon_live_now_section .evo_eventon_now_next h3','css'=>'color:#$', 'var'=>'evo_live3',	'default'=>'8e8e8e'
			),array(
				'item'=>'.evo_eventon_live_now_section .evo_eventon_now_next h3 .evo_countdowner','multicss'=>array(
					array('css'=>'background-color:#$', 'var'=>'evo_live4b','default'=>'a5a5a5'),
					array('css'=>'color:#$', 'var'=>'evo_live4a','default'=>'ffffff')
				)	
			),array(
				'item'=>'.evo_eventon_live_now_section p.evo_eventon_no_events_now','multicss'=>array(
					array('css'=>'background-color:#$', 'var'=>'evo_live5b','default'=>'d6f5d2'),
					array('css'=>'color:#$', 'var'=>'evo_live5a','default'=>'888888')
				)	
			)
			
	));

	
	if(sizeof($style_array)>0){
		foreach($style_array as $sa){
			if(!empty($sa['multicss']) && is_array($sa['multicss'])){

				echo $sa['item'].'{';

				foreach($sa['multicss'] as $sin_CSS){
					if(!empty($sin_CSS['replace'])){
						$css = $sin_CSS['replace'];
						foreach($sin_CSS['var'] as $index=>$var){
							$css_val = (!empty($opt[ $var] ))? 
								$opt[ $var ] : $sin_CSS['default'][$index];

							$css = str_replace('$'.$index ,$css_val, $css );
						}
						
						echo $css.';';
					}else{
						$css_val  = (!empty($opt[ $sin_CSS['var'] ] ))? $opt[ $sin_CSS['var'] ] : $sin_CSS['default'];						
						$css = str_replace('$',$css_val,$sin_CSS['css'] );
						echo $css.';';
					}
					
				}
				echo '}';
			}else{
				$css_val  = (!empty($opt[ $sa['var'] ] ))? $opt[ $sa['var'] ] : $sa['default'];
				$css_val = html_entity_decode($css_val);
				$css_val = str_replace('&#039;',"'",$css_val );
				$css = str_replace('$',$css_val,$sa['css'] );
				echo $sa['item'].'{'.$css.'}';
			}
		}
	}
	
	
	// STYLES
	echo (!empty($opt['evo_ftimgheight']))?
			".evcal_evdata_img{height:".$opt['evo_ftimgheight']."px}":null ;
		
		
		// featured event styles
		if(!empty($opt['evo_fte_override']) && $opt['evo_fte_override']=='yes'){
			echo "#evcal_list .eventon_list_event .evcal_list_a.featured_event{border-left-color:#".eventon_styles('ca594a','evcal__ftec', $opt)."!important;}";
		}


	// (---) Hook for addons
	do_action('eventon_inline_styles');
	
	echo get_option('evcal_styles');