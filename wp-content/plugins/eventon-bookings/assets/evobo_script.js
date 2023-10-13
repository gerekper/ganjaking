/**
 * frontend script 
 * @version 1.4
 */
jQuery(document).ready(function($){

const BB = $('body');


// load booking cal for ajax loaded events 
    $( document ).ajaxComplete(function(event, xhr, settings) {
        
        if( !( 'data' in settings) ) return false;
        var data = settings.data;

        if( typeof data ==='object') return false;

        if( data !== undefined && data && data != ''){     
            
            if(data.indexOf('action=eventon_init_load') != -1)   load_evobo_calendar();
            if(data.indexOf('action=the_ajax_hook') != -1)   load_evobo_calendar();
            if(data.indexOf('action=new_week') != -1)   load_evobo_calendar();
            if(data.indexOf('action=eventon_load_single_eventcard_content') != -1)   
                load_evobo_calendar();
        }
    });

// primary function
    $.fn.bookify_event = function (options) {
        var works = {},
        $el = this;

        defaults = {
            action:'init'
        };
        var settings = $.extend({}, defaults, options);

        var evobo_section = $el;
        var eventRow = evobo_section.closest('.evorow');
        var C = evobo_section.find('.evobo_calendar');
        var TS = evobo_section.find('.evobo_selections');
        var mainSEL = evobo_section.find('.evobo_main_selection');
        
        const tx_data = $el.evotx_get_custom_data('evobo_data');

        var CalDATA =   tx_data.evobo_dataset;
        var slotsDATA = tx_data.evobo_json;
        var selectDate = {};
        var _today = new Date();
        var __today = moment();        

        var style = evobo_section.data('s');

        $( window ).resize(function() {
            var w = mainSEL.width();
            //console.log(w);
            if(w< 650){
                mainSEL.addClass('trim');
            }else{
                mainSEL.removeClass('trim');
            }
        });
        
        works = {
            drawCal: function(){              
                
                var HTML = '';
                
                // calendar view
                if(style == 'def'){
                    var y = parseInt(CalDATA.cty);
                    var mm = parseInt(CalDATA.ctm);
                    m = mm -1; // 0-11
                    sow = CalDATA.sow; // start of the week 0-6

                    HTML += "<div class='evoGC'>";
                    
                    // calendar header
                    HTML += works.getCalHeader( y, mm );

                    // Day of week
                        HTML += '<span class="evoGC_days">';
                        for(i=0; i<7; i++){
                            sow_ = parseInt(sow)+i;
                            sow_ = sow_>6? sow_-7: sow_;

                            HTML += "<span class='"+ sow_+"'>"+ CalDATA.d1[ sow_ ] +"</span>";
                        }
                        HTML += '</span>';
                    
                    HTML += '<span class="evoGC_dates">';                   

                        HTML += works.getCalDates( y, mm );
                        
                    HTML += '</span>';
                    HTML += '</div>';

                    // set select data at start
                    selectDate['y'] = _today.getFullYear();
                    selectDate['m'] = _today.getMonth();
                    selectDate['d'] = _today.getMonth();
                    
                     // header
                    HTML = '<span class="evobo_section_header">'+CalDATA.t1+'</span>'+ HTML;
                   
                    C.html( HTML );

                    // resize
                    var w = mainSEL.width();
                    if( mainSEL.is(':visible') && w<650 ) mainSEL.addClass('trim');

                // time slot view
                }else{

                    if( slotsDATA != '') HTML += '<span class="evobo_section_header">'+ CalDATA.t2+'</span>';
                    HTML += "<div class='evobo_slot_selection evobo_selection_row'>";

                    if( slotsDATA == ''){
                        HTML += "<p>" + CalDATA.t3n + '</p>';
                    }else{
                        $.each(slotsDATA, function(year, months){
                            $.each(months, function(month, days){
                                
                                month_name = days['name'];
                                HTML += '<span class="month">'+month_name+ ', '+ year +'</span>';
                                
                                $.each(days, function(date, slots){
                                    IND = 1;
                                    if( date == 'name') return true;

                                    HTML += '<div class="date">';
                                    HTML += '<b class="day">' + date + " <i>"+ slots['day']+"</i></b>";
                                    HTML += "<span class='line_slots'>";

                                    $.each(slots, function(booking_index, BD){
                                        if( booking_index == 'day') return true;
                                        var a_class = ('a' in BD && Object.keys(BD.a).length>0)? 'has':'';

                                        // show/hide end time for booking blocks
                                        var time__ = BD.times.split(' - ');
                                        var times__ = CalDATA.hide_end ? time__[0]: BD.times;

                                        HTML += '<span class="slot '+a_class+'" data-val="'+ BD.index +'">'+ times__ +'</span>';
                                        IND++;
                                    });
                                    HTML += "</span>";
                                    HTML += "</div>";
                                });
                            });
                        });
                    }

                    HTML += '</div>';

                    TS.html( HTML );

                }

                // hide loading
                evobo_section.find('.evobo_loading_section').hide();

                $el.evotx_hide_loading();

            },
            getCalHeader:function(y, mm){
                var HTML = '';
                m = mm-1;
                var next = {};
                next['m'] = (mm+1>12? 1: mm+1);
                next['y'] = (mm+1>12? y+1: y); 

                var prev = {};
                prev['m'] = (mm-1<1? 12: mm-1);
                prev['y'] = (mm-1<1? y-1: y);

                HTML += "<span class='evoGC_header'>";
                    HTML += "<span class='evoGC_monthyear'>";
                        HTML += "<span class='evoGC_month'>"+ CalDATA.m[mm] +"</span>";
                        HTML += "<span class='evoGC_year'>"+ y +"</span>";
                    HTML += '</span>';

                    HTML += "<span class='evoGC_nav'>";
                        today_vis = (_today.getMonth()+1) == mm && _today.getFullYear() == y? 'none':'block';
                        HTML += '<span class="evoGC_today" style="display:'+today_vis+'" >'+CalDATA.t4+'</span>';
                        HTML += "<span class='evoGC_prev evoGC_ar'><i class='fa fa-angle-left'></i></span>";
                        HTML += "<span class='evoGC_next evoGC_ar'><i class='fa fa-angle-right'></i></span>";
                    HTML += "</span>";
                HTML += '</span>';

                return HTML;
            },
            getCalDates: function(y, mm){
                HTML = '';
                week = 1;
                week_break = [8,15,22,29,36];
                HTML += '<span class="evoGC_week">';
                sow = CalDATA.sow;
                m = mm-1;

                var dim = works.dayInMonth(y, mm);

                for( i=1; i<= dim; i++){
                    Dthis = new Date(y,m,i);
                    __d = Dthis.getDay();

                    if( i == 1){
                        dof_1st =  __d; // start of the week for 1st of this month
                        ifd = ( dof_1st < sow)? (( 7-sow )+ dof_1st): ( dof_1st - sow);

                        if(dof_1st != sow && ifd != 7 ){
                            for(b=1; b<= ifd; b++){
                                HTML += '<span class="evoGC_date blank" '+week+'></span>';
                                week++;
                            }
                        }
                    }

                    var ADDCLASS = '';
                    $.each(slotsDATA, function(year, OD){
                        $.each( OD, function( month, ODD){
                            $.each( ODD, function( date, ODDD){
                                if( year == y && month == mm && date == i) ADDCLASS += ' hasslots'+' ';
                            });
                        });
                    });
                    
                    // today
                    if( CalDATA.cty == _today.getFullYear() && CalDATA.ctm == (_today.getMonth() +1) && CalDATA.ctd == i )
                        ADDCLASS += ' today';

                    // select day
                    if(selectDate['y'] == y && selectDate['m'] == mm && selectDate['d'] == i) ADDCLASS += ' select';

                    //hasSlot = ( i in thismonthSlots)? 'hasslot':'';
                    HTML += '<span class="evoGC_date'+ADDCLASS+'" data-d="'+ i +'" data-m="'+ mm +'" data-y="'+ Dthis.getFullYear()+'"><i><em>'+i+ '</em></i></span>';

                    week++;

                    if( $.inArray(week,week_break) >= 0 ) HTML += '</span><span class="evoGC_week">';
                }
                HTML += '</span>';

                return HTML;
            },
            goToMonth: function(y,mm){
                var O = $(this);                    
                
                CalDATA.cty = y;
                CalDATA.ctm = mm;

                C.data( 'dataset', CalDATA);               

                header_HTML = works.getCalHeader( y, mm );
                HTML = works.getCalDates( y, mm );

                C.find('.evoGC_dates').html( HTML );
                C.find('.evoGC_header').replaceWith( header_HTML );
            },
            dayInMonth: function(y, mm){
                var D = moment();
                    D.date(1).month( mm-1 ).year( y );
                return D.daysInMonth();
            },
            interaction: function(){
                // click on a date
                C.on('click','.evoGC_date',function(event){
                    
                    C.evotx_hide_msg();

                    _click_y = selectDate['y'] = $(this).data('y');
                    _click_m = selectDate['m'] = $(this).data('m');
                    _click_d = selectDate['d'] = $(this).data('d');

                    //reset                     
                    eventRow.find('.evobo_price_values').hide();
                    C.find('.evoGC_date').removeClass('select');
                    C.find('.evoGC_date[data-d="'+_click_d+'"]').addClass('select');

                    HTML = '';
                    if( __hasVal(slotsDATA, _click_y ) ){
                        if( __hasVal(slotsDATA[ _click_y ], _click_m )){

                            if( __hasVal(slotsDATA[ _click_y ][ _click_m ], _click_d )){

                                HTML += '<span class="evobo_section_header">'+ CalDATA.t2+'</span>';
                                HTML += "<div class='evobo_slot_selection evobo_selection_row'>";
                                $.each( slotsDATA[ _click_y ][ _click_m ][ _click_d ] , function(booking_index, times){
                                    if(booking_index == 'day') return true;

                                    time__ = times.times.split(' - ');

                                    end_time = CalDATA.hide_end ? '' :
                                        '<em class="end"> - '+ time__[1] +'</em>';

                                    HTML += '<span class="slot" data-val="'+times.index+'"><em class="start">'+ time__[0]+'</em>'+ end_time +'</span>';
                                });
                                HTML += '</div>';
                            }
                        }
                    }

                    if( HTML == ''){
                        HTML += '<span class="evobo_section_header">'+ CalDATA.t2+'</span>';
                        HTML += "<div class='evobo_slot_selection evobo_selection_row'><p>";
                        HTML += CalDATA.t3;
                        HTML += '</p></div>';
                    }

                    eventRow.find('.evobo_selections').html( HTML );
                });

                // go to today
                C.on('click', '.evoGC_today',function(){
                    works.goToMonth( _today.getFullYear() , _today.getMonth()+1);
                }),
                // month nav arrow
                C.on('click','.evoGC_ar',function(event, ){
                    var D = moment();
                    D.date( 1).month( parseInt(CalDATA.ctm)-1 ).year( parseInt(CalDATA.cty) );
                                                           
                    if($(this).hasClass('evoGC_prev')){
                        D.subtract(1, 'month'); 
                    }else{    D.add(1, 'month');    }

                    works.goToMonth( D.year(), D.month()+1);                  
                });

                // click on a timeslot
                eventRow.on('click','.evobo_slot_selection span.slot',function(){
                    var SPAN = $(this);
                    
                    TS.find('.slot').removeClass('select');
                    SPAN.addClass('select');

                    // conditional wait
                        if( evobo_section.hasClass('wait')){

                            BB.trigger('evobo_click_on_block', [ eventRow, $(this) ]);
                            return;
                        }

                    // set the new block id for other_data
                    SPAN.evotx_set_select_data('evobo', { 'block_id':SPAN.data('val') });
                    
                    // proceed
                    var ajaxdataa = { };
                        ajaxdataa['action']='evobo_get_prices';
                        ajaxdataa['block_id']=  SPAN.data('val');
                        ajaxdataa['event_data']=  $el.evotx_get_event_data();

                    $(this).evo_admin_get_ajax({
                        'ajaxdata':ajaxdataa,
                        'uid':'evobo_get_prices',
                        'end':'client',
                        'loader_el': eventRow
                    }); 
                });

                $('body')
                .on('evo_ajax_success_evobo_get_prices', function(event, OO, data){
                    if(data.status=='good'){    

                        eventRow.find('.evobo_price_values').html( data.content).show();
                        $('body').trigger('evobo_block_prices_loaded', [eventRow, data, OO.ajaxdata ]);

                    }else{
                        $el.evotx_show_msg({ 
                            'status': data.status, 'msg': data.msg, 
                            'hide_hidables':false });
                    }
                })
                // after ticket added to cart
                .on('evotx_added_to_cart', function(event, data, SECTION, ajaxdata){
                    if( 'other_data' in ajaxdata && 'block_id' in ajaxdata.other_data){
                        $( SECTION ).find('.evobo_price_values').hide();
                    }
                })
                ;

            }
        };


        // Reload interactions if loaded via lightbox
        if(settings.action == 'lightbox')   works.interaction();

        if($el.hasClass('active')) return;
        $el.addClass('active');
        
        works.drawCal();
        works.interaction();
    };

// RUN
    load_evobo_calendar();
    function load_evobo_calendar(){
        if($('body').find('.evobo_booking_section').length>0){
            $('body').find('.evobo_booking_section').each(function(){
                $(this).bookify_event();                
            });
        }
    }

    // load booking cal after the event is slide down
    $('body').on('evo_slidedown_eventcard_complete', function(){
        load_evobo_calendar();
    });

    // load booking cal after ticket standalone button -- @since 1.4
    $('body').on('evotx_standlone_loaded',function(event, LB){
        load_evobo_calendar();
    });


    $('body').on('evolightbox_end', function(){
        if($('body').find('.evobo_booking_section').length>0){
            $('body').find('.evobo_booking_section').each(function(){
                $(this).bookify_event({action:'lightbox'});             
            });
        }
    });

    function __hasVal(obj, key){
        return obj.hasOwnProperty(key);
    }


});