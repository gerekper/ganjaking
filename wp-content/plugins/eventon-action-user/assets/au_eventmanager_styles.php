<?php
// styles for event manager thats printed on to the page

$preset_data = array(
    'evo_color_1' => '202124',
    'evo_color_2' => '656565',
    'evo_color_link' => '656565',
);
extract($preset_data);

?>

.eventon_actionuser_eventslist p subtitle a,
.eventon_actionuser_eventslist .evoau_manager_row .event_date_time,
.eventon_actionuser_eventslist .evoau_manager_row .event_date_time span,
.eventon_actionuser_eventslist a.editEvent, .eventon_actionuser_eventslist a.deleteEvent,
.evoau_manager_continer h4,
.evoau_back_btn i, .evoau_paginations i,
a.evoau_back_btn, .evoau_paginations,
#evoau_event_manager h3.evoau_subheader
{color:var(--evo_color_1)}

.eventon_actionuser_eventslist .evoau_manager_row a.evoauem_additional_buttons,
.eventon_actionuser_eventslist .editEvent:hover, .eventon_actionuser_eventslist .deleteEvent:hover,
.evoau_back_btn:hover i, .evoau_paginations:hover i
{background-color:var(--evo_color_1)}

.evoau_back_btn i, .evoau_paginations i
{border-color:var(--evo_color_1)}

.evoau_manager_event_section{
	width:100%;
	overflow:hidden;
    font-family:'open sans'
}
.eventon_actionuser_eventslist,
.evoau_manager_event{float:left; width:100%}
.evoau_delete_trigger{
	position: absolute;
    height: 100%;
    width: 100%;
    background-color: rgba(255, 255, 255, 0.79);
    text-align: center;
    vertical-align: middle;
    display: block;
    z-index: 5;
    display: flex;
    justify-content: center;
}
.evoau_delete_trigger .deletion_message{
    position:absolute; height:100px; display:block
}
.evoau_delete_trigger span{
	display: inline-block;
    border-radius: 20px;
    margin: 15px 5px;
    padding: 5px 20px;
    background-color: #59c9ff;
    color: #fff;
    cursor:pointer;
}
.evoau_delete_trigger span:hover{background-color:#32bbfd}
.evoau_manager_event_rows, .evoau_manager_continer{border:1px solid var(--evo_color_2);border-radius:12px;overflow:hidden}

.evoau_manager_continer h4{
    font-weight: 900; font-size:20px;
    font-family: 'roboto';
    padding: 10px 0;
}

.evoau_manager_continer .evoau_tile{
    background-color: #eaeaea;
    padding: 30px;
    border-radius: 10px; margin-bottom:10px;
}
.evoauem_section_subtitle b{margin-right:10px;}
.evoau_information_bubble{
    border: 1px solid #888;
    padding: 3px 15px;
    border-radius: 20px;
    font-size: 16px;
    display:inline-block;margin-right:10px; 
}
.evorsau_icon_button{margin-left:10px; cursor: pointer;opacity: 0.5;}
.evorsau_icon_button:hover{opacity: 1;}

.evcal_btn.evoau, .evoau_submission_form.loginneeded .evcal_btn{
	border-radius: 4px;
	border: none;
	color: #ffffff;
	background: #237ebd;
	text-transform: uppercase;
	text-decoration: none;
	border-radius: 4px;
	border-bottom: none;
	font: bold 14px arial;
	display: inline-block;
	padding: 8px 12px;
	margin-top: 4px;
	box-shadow:none; transition:none
}
.evcal_btn.evoau:hover, .evoau_submission_form.loginneeded .evcal_btn:hover{color: #fff; opacity: 0.6;box-shadow:none}
.eventon_actionuser_eventslist{
	overflow:hidden;
	position:relative
}
a.evoau_back_btn, .evoau_paginations{
	text-decoration:none!important;
	box-shadow:none;
	position:relative;
	padding:10px 0;
	cursor:pointer;
}
a.evoau_back_btn:hover, .evoau_paginations:hover{
	box-shadow:none;
	text-decoration:none
}
.evoau_back_btn i, .evoau_paginations i{
	border-radius: 50%;
    height: 30px;
    width: 30px;
    display: inline-block;
    border-width:1px;
    border-style:solid;
    text-align: center;
    font-size: 16px;
    position: relative;
    padding-top: 5px;
    margin-right:10px;
    box-sizing:border-box;
}
.evoau_back_btn:hover i:before, .evoau_paginations:hover i:before{color:#fff}
.evoau_back_btn i:before{
	margin-top:5px;
}
.eventon_actionuser_eventslist a, .eventon_actionuser_eventslist a:hover{
	box-shadow:none; -webkit-box-shadow:none;
}
.eventon_actionuser_eventslist .evoau_manager_row{
	padding: 10px 15px;
    margin: 0;
    position: relative;
    margin-bottom: 10px;
    border-radius: 10px;
    background-color: transparent;
    border: 1px solid var(--evo_color_2);
    margin: 10px;
}
.eventon_actionuser_eventslist .evoau_manager_row.past{background-color:#f7f7f7}
.eventon_actionuser_eventslist .evoau_manager_row p{padding:0; margin:0}
.eventon_actionuser_eventslist .evoau_manager_row p.event_name{}
.eventon_actionuser_eventslist .evoau_manager_row p.event_name i{
    color:#ffa86b; margin-right:5px;
}
.eventon_actionuser_eventslist .evoau_manager_row:hover{
	background-color: #FCF7F3;
}
#evoau_event_manager h2.title{
	padding: 10px 0;
    border-bottom: 1px solid #afafaf;
    border-top: 1px solid #afafaf;
}
#evoau_event_manager h3.evoau_subheader{margin:20px 0 10px 0}
#evoau_event_manager p.evoau_intro{margin:10px 0}
#evoau_event_manager h3 i.fa-search{
     font-size: 18px;
    margin-right: 10px;
    cursor: pointer;
    opacity: 0.5;
}
#evoau_event_manager h3 i.fa-search:hover{opacity:1}
.evoau_search_form{
    display:none;
    margin-bottom: 20px;
    background-color: #e8e8e8;
    border-radius: 25px;
    padding: 10px 20px;
}
.evoau_search_form input{
    width: 100%;
    background-color: transparent;
    box-shadow: none;
    border: none;
    font-size: 18px;
}
.evoau_search_form input:focus{border:none;outline:none}
.eventon_actionuser_eventslist .evoau_manager_row span{
	opacity: 0.7;
	display: block;
	font-size: 11px;
	text-transform: uppercase;		
}
.eventon_actionuser_eventslist .evoau_manager_row .event_date_time{font-size:13px}
.eventon_actionuser_eventslist .evoau_manager_row .event_date_time span{
   display: inline-block;margin-left: 10px;opacity: 1;font-size: 13px;
}
.eventon_actionuser_eventslist .evoau_manager_row span.event_date{}
.eventon_actionuser_eventslist .evoau_manager_row a.evoauem_additional_buttons{
	cursor:pointer;
	display:inline-block;
	border-radius:20px;
	margin-right:5px;
	padding:1px 15px;margin-top:5px;
	color:#fff;
	font-size:12px;
	text-transform:uppercase;
    text-decoration:none
}
.eventon_actionuser_eventslist .evoau_manager_row a.evoauem_additional_buttons:hover{
	text-decoration:none; opacity:0.6
}
.eventon_actionuser_eventslist p subtitle{
	font-weight:bold; text-transform:uppercase;
	font-family:roboto,oswald,'arial narrow';
	color:#808080;
	font-size:18px;
}
.eventon_actionuser_eventslist p subtitle a{text-decoration:none!important}
.eventon_actionuser_eventslist .evoau_manager_row span em,
.eventon_actionuser_eventslist .evoau_manager_row tags{
	padding:3px 10px; background-color:#cccccc; color:#fff;display:inline-block; border-radius:5px; margin-bottom:5px; font-size:12px; font-style:normal;line-height:1; margin-right:5px;
}
.eventon_actionuser_eventslist .evoau_manager_row span em.movedonline{background-color: #6edccd}
.eventon_actionuser_eventslist .evoau_manager_row span em.rescheduled{background-color: #67ef78}
.eventon_actionuser_eventslist .evoau_manager_row span em.postponed{background-color: #e3784b}
.eventon_actionuser_eventslist .evoau_manager_row span em.cancelled{background-color: #F79191}

.eventon_actionuser_eventslist .evoau_manager_row span em.event_poststatus.status_publish{background-color:#64ce76}
.eventon_actionuser_eventslist a.editEvent, .eventon_actionuser_eventslist a.deleteEvent{
	z-index: 1;
    text-align: center;
    position: absolute;
    right: 20px;
    top: 50%;
    height: 40px;
    width: 40px;
    padding-top: 12px;
    cursor: pointer;
    border-radius: 50%;
    border: 1px solid #3d3d3d;
    font-size: 14px;
    margin-top: -20px;
    opacity: 0.5;
    text-decoration:none!important;
    box-sizing:border-box;
}
.eventon_actionuser_eventslist a.deleteEvent.disa{opacity:0.2;cursor:default;}
.eventon_actionuser_eventslist a.deleteEvent.disa:hover{color: #333;background-color:transparent}
.eventon_actionuser_eventslist a.deleteEvent{
	right:70px;
}
.eventon_actionuser_eventslist .editEvent:hover, .eventon_actionuser_eventslist .deleteEvent:hover{
	text-decoration: none; opacity: 1;color:#fff; }

.eventon_actionuser_eventslist em{clear: both;}
h3.evoauem_del_msg{padding: 4px 12px; border-radius: 5px; text-transform: uppercase;}
p.evoau_outter_shell{
	padding:20px; border-radius:5px; border:1px solid #d6d6d6;
}
/*#eventon_form .evoau_table .evoau_sub_formfield .row{border:none; padding:0}*/
.evoau_table p.minor_notice{ font-size:14px; background-color:#f5dfdf; padding:5px 10px}

.evoau_table span.ajdeToolTip{
    padding-left: 0;
    margin-left: 4px;
    text-align: center;
    font-style: normal;
    position: absolute;
    width: 13px;
    height: 14px;
    line-height: 110%;
    opacity: 0.4;
    border-radius: 0px;
    color: #fff;
    padding: 0;
    font-style: normal;
    cursor: pointer;
    display: inline-block;
    margin-top:3px;
}

.evoau_table .ajdeToolTip em{
    visibility: hidden;
    font: 12px 'open sans';
    position: absolute;
    left: -1px;
    width: 200px;
    background-color: #6B6B6B;
    border-radius: 0px;
    color: #fff;
    padding: 6px 8px;
    bottom: 22px;
    z-index: 900;
    text-align: center;
    margin-left: 8px;
    opacity: 0;
    -webkit-transition: opacity 0.2s, -webkit-transform 0.2s;
    transition: opacity 0.2s, transform 0.2s;
    -webkit-transform: translateY(-15px);
    transform: translateY(-15px);
    pointer-event: none;
}
.evoau_table .ajdeToolTip em:before{
    content: "";
    width: 0px;
    height: 0px;
    border-style: solid;
    border-width: 9px 9px 0 0;
    border-color: #6B6B6B transparent transparent transparent;
    position: absolute;
    bottom: -9px;
    left: 0px;
}
.evoau_table .ajdeToolTip:hover { opacity: 1;}
.evoau_table .ajdeToolTip:hover em {
    opacity: 1;
    visibility: visible;
    -webkit-transform: translateY(0);
    transform: translateY(0);
}
.edit_special{background-color:#f7f7f7;}
#eventon_form .evoau_table .edit_special .row{border-color:#e8e8e8}
.evoauem_section_subtitle{
    padding:10px 0; margin:0px; font-size:18px;
}
/* Pagination */
.evoau_manager_pagination{  padding-top:10px;    display: flex;   justify-content: space-between;}
.evoau_manager_pagination .evoau_paginations.next i{margin-right:0px; margin-left:10px}

/* signin */
.evoau_manager_event_content i.signin{
    margin-left: 10px;
    background-color: #8bc34a;
    padding: 5px;
    border-radius: 20px;
    color: #fff;
    font-size: 12px;
}
.evoau_manager_event_content input{    border: none;box-shadow: 0px 0px 15px #e4e4e4;border-radius: 25px;padding: 4px 15px;background-color: #fff;}
.evoau_manager_event_content input:focus{outline:none}

@media (max-width: 480px){
	.eventon_actionuser_eventslist .editEvent, .eventon_actionuser_eventslist .deleteEvent{
		width:30px;
	}
	.eventon_actionuser_eventslist .deleteEvent{right:30px;}
}

<?php do_action('evoauem_styles');?>