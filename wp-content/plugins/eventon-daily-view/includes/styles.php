/**
 * EventON DV Styles
 * @version 1.0.4
 */
.ui-loader { display: none; position: absolute; opacity: .85; z-index: 100; left: 50%; width: 200px; margin-left: -130px; margin-top: -35px; padding: 10px 30px; }
.evodv_current_day p.evodv_daynum span, .evodv_current_day p.evodv_events{
	-webkit-transition: all .2s ease;
	-moz-transition: all .2s ease;
	-ms-transition: all .2s ease;
	-o-transition: all .2s ease;
	transition: all .2s ease;
}
/* fonts */
	.evo_day span,
	.evodv_tooltip,
	.evodv_current_day p.evodv_daynum
	{
		font-family:'roboto', 'oswald','arial narrow';
	}
	.evodv_current_day{
		font-family: 'open sans','arial'
	}
	.eventon_daily_in .evo_day{
		display:inline-block;
		float:left;
		margin:0;
		cursor:pointer;
		min-width:20px;
		width: auto;
		text-align: center;
		width: 60px;
		-webkit-box-sizing: border-box; /* Safari/Chrome, other WebKit */
		-moz-box-sizing: border-box;    /* Firefox, other Gecko */
		box-sizing: border-box;
		-webkit-touch-callout: none; /* iOS Safari */
		  -webkit-user-select: none;   /* Chrome/Safari/Opera */
		  -khtml-user-select: none;    /* Konqueror */
		  -moz-user-select: none;      /* Firefox */
		  -ms-user-select: none;       /* Internet Explorer/Edge */
		  user-select: none;
		outline:none;
	}
	
	/* focused */
		.eventon_daily_in .evo_day.on_focus{
			background-color:#636363;
			color:#fff;
		}
	.evo_day.has_events{color:#d5c3ac}
	.evo_day.on_focus{	color:#a4a4a4}
	.evo_day span{
		display:block; text-align:center;		
		line-height:100%;		
	}
	.evo_day{color:#e8e8e8}
	.evo_day:hover, .evodv_action:hover{color:#d4d4d4; background-color: #f3f3f3;}
	.evo_day span.evo_day_num{
		font-weight:bold;
		font-size:24px;
		padding-bottom: 5px;
	}
	.evo_day span.evo_day_name{
		text-transform:uppercase;
		font-size:12px;	
		padding-bottom:4px;
		padding-top: 15px;
	}
	.eventon_daily_in .evo_day.today{color: #ffb677}

/*-- container --*/
	.ajde_evcal_calendar .eventon_daily_list{
		position:relative;
		padding:0px;
		/*border-top:1px solid #e5e5e5;*/
		border-bottom:1px solid #e5e5e5;
		margin-bottom:10px;
		overflow: hidden;
		height: 90px;
	}
	.ajde_evcal_calendar.evoDV .eventon_events_list{border-top:1px solid #e5e5e5;}
	.eventon_dv_outter{
		overflow:hidden;
		width:100%; 
		padding: 0px;
		position: relative;
	}
	.eventon_daily_in{
		overflow:hidden; 
		position: relative;
		-webkit-transform: translate3d(0px, 0px, 0px);
		padding-left: 60px;
		padding-right: 60px;
	}
	.evodv_carousel{
		position: relative;
	    display: block;
	    overflow: hidden;
	    margin: 0;
	    padding: 0;
	    overflow-x:scroll; overflow-y:hidden;
	}
	.evodv_carousel .inner{width: 1980px;}
	/* events per day */
		.eventon_daily_list .evo_day .evoday_events{padding-bottom: 15px;height: 31px;}
		.eventon_daily_list .evo_day .evoday_events em{
			display: inline-block;
			margin: 0 1px;
			height: 7px; width: 9px;
			border-radius: 60px;
			background-color: #d8d8d8;
		}
		.eventon_daily_list .evo_day .evoday_events em.more{
			width: 15px;
			color: #3d3d3d;
		}
		.evodv_tooltip{
			position: absolute;
			background-color: #fafafa;
			padding: 5px 10px;
			font-size: 14px; font-style: normal;
			color:#868686;
			text-transform: uppercase;
			top: 0;
			border:1px solid #e8e8e8;
		}
		.evodv_tooltip:before{
			content:"";
			border-style: solid;
			border-width: 6.5px 10px 6.5px 0;
			border-color: transparent #b9b9b9 transparent transparent;
			height: 0;width: 0;position: absolute;
			left: -11px; top: 10px;
		}
		.evodv_tooltip.left:before{
			border-width: 6.5px 0 6.5px 10px;
			border-color: transparent transparent transparent #b9b9b9;
			right: -11px; left:auto;
		}

/* times only for dailyview */
	.ajde_evcal_calendar.evoDV #evcal_list .evcal_cblock .evo_date{display: none;}
	.ajde_evcal_calendar.evoDV #evcal_list .evcal_cblock .evo_time{display: block;}
	.ajde_evcal_calendar.evoDV .eventon_events_list .eventon_list_event .evcal_desc .evcal_time{display: none;}
	.ajde_evcal_calendar.evoDV .eventon_events_list .eventon_list_event .evcal_list_a{min-height: 75px}

/* current day section */
	.evoDV #eventon_loadbar_section{border-bottom:none;}
	.evodv_current_day{
		width: 100%;
		padding: 40px 0;
		background-color: #f5b87a;
		color: #fff;
		text-align: center;
		border-radius: 5px;
	}
	.ajde_evcal_calendar .evodv_current_day p{
		margin: 0; padding: 0;
	}
	.evodv_current_day p.evodv_dayname{
		text-transform: uppercase;
		font-size: 20px;
	}
	.evodv_current_day p.evodv_daynum{
		font-size: 120px;
		line-height: 110%;
		margin-top: -10px;
		position: relative;		
	}
	.evodv_current_day p.evodv_daynum b{font-weight: normal; font-size: 100%; color: #fff}
	.evodv_current_day p.evodv_daynum span i{
		font-size: 36px;
		margin-top: 4px;
		position: absolute;
		margin-left: -7px;
	}
	.evodv_current_day p.evodv_daynum span.disable:hover{opacity: 0.3}
	.evodv_current_day p.evodv_daynum span:hover{opacity: 1}
	.evodv_current_day p.evodv_daynum span{
		text-align: center;
		height: 50px;
		width: 50px;
		display: inline-block;
		cursor: pointer;
		border: 1px solid #fff;
		border-radius: 50%;
		color: #fff;
		position: relative;
		vertical-align: middle;
		margin-top: -20px;
		opacity: 0.3
	}
	.evodv_current_day p.evodv_daynum span.prev{margin-right: 30px;}
	.evodv_current_day p.evodv_daynum span.next{margin-left: 30px;}
	.evodv_current_day p.evodv_events:hover{opacity: 1}
	.evodv_current_day p.evodv_events{
		font-size: 14px;
		text-transform: uppercase;
		opacity: 0.5
	}
	.evodv_current_day p.evodv_events span{
		border-radius: 50%;
		background-color: #ffffff; color: #3d3d3d;
		width: 20px; height: 20px;
		text-align: center;
		display: inline-block;
		margin-right: 8px;
		font-size: 13px;
	}
/* arrows */
	.eventon_dv_outter .evodv_action{
		display: inline-block;
	    float: left;
	    margin: 0;
	    cursor: pointer;
	    min-width: 20px;
	    text-align: center;
	    width: 60px; height: 91px;
	    padding-top: 30px;
	    background-color: #f7f7f7; color: #c3bebe;
	    -webkit-box-sizing: border-box;
	    -moz-box-sizing: border-box;
	    box-sizing: border-box;
	    position: absolute;
	    top: 0;
	    z-index: 1;
	}
	.eventon_dv_outter .evodv_action.next{right: 0;}
	.eventon_dv_outter .evodv_action.prev{left: 0;}
	.eventon_dv_outter .evodv_action:hover{background-color: #e0e0e0; color:#fff;}
	.eventon_dv_outter .evodv_action.next:before,
	.eventon_dv_outter .evodv_action.prev:before
	{
		content:"\f105";
		display: inline-block;
	    font: normal normal normal 14px/1 evo_FontAwesome;
	    font-size: inherit;
	    text-rendering: auto;font-size: 30px;
	    -webkit-font-smoothing: antialiased;
	    -moz-osx-font-smoothing: grayscale;
	}
	.eventon_dv_outter .evodv_action.prev:before, .evodv_carousel .evodv_action.prev:before{
		content:"\f104";
	}

	/* widget */
		#evcal_widget .evoDV .evodv_current_day p.evodv_daynum{font-size: 70px;}
		#evcal_widget .evoDV .evodv_current_day p.evodv_daynum span{height: 35px; width: 35px; }
		#evcal_widget .evoDV .evodv_current_day p.evodv_daynum span i{font-size: 24px;margin-left: -4px}

	@media (max-width: 480px){
		.evoDV .evodv_current_day p.evodv_daynum{font-size: 70px;}
		.evoDV .evodv_current_day p.evodv_daynum span{height: 35px; width: 35px; }
		.evoDV .evodv_current_day p.evodv_daynum span i{font-size: 24px;margin-left: -4px}
	}	