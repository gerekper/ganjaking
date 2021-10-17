/**
 * Javascript: Speaker & Schedule
 * @version  0.1
 */
jQuery(document).ready(function($){	

	// open the subscription form
		$('body').on('click','.evospk_img_box',function(e){
			OBJ = $(this);
			$('.evoss_lightbox').addClass('show');
			$('body').trigger('evolightbox_show');

			HTML = OBJ.siblings('.evospk_hidden').html();
			$('.evoss_lightbox').find('.evo_lightbox_body').html( HTML );
		});

	// switching the schedule dates
		$('body').on('click','.evosch_nav li',function(){
			OBJ = $(this);
			DAY = OBJ.attr('data-day');
			OBJ.parent().find('li').removeClass('evoss_show');
			OBJ.addClass('evoss_show');
			
			OBJ.closest('.evosch_blocks_list').find('.evosch_oneday_schedule').removeClass('evoss_show');
			OBJ.closest('.evosch_blocks_list').find('ul.evosch_date_'+DAY).addClass('evoss_show');
		});
	// expand schedule
		$('body').on('click','.evosch_oneday_schedule b',function(){
			if($(this).hasClass('evoss_hide')){
				$(this).attr('class','evoss_show');
				$(this).siblings('span').attr('class','evoss_show');
			}else{
				$(this).attr('class','evoss_hide');
				$(this).siblings('span').attr('class','evoss_hide');
			}
		});
});