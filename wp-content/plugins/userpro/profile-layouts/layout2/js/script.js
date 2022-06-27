jQuery('document').ready(function(){
        jQuery('.thumbnail_media').addClass('col-xs-6 col-md-4');
	jQuery('.up-layout-side li').click(function(){
		up_toggle_content(this);
	});
        jQuery('.col-xs-6.col-md-4').removeClass('thumbnail_media');
        jQuery('.col-xs-6.col-md-4').addClass('col-md-6');
        jQuery('.col-xs-6.col-md-4').removeClass('col-md-4');
	userpro_collapse(jQuery('#up_profile_details'));
        jQuery('.upl_follow .userpro-follow').removeClass('userpro-button');
});

function up_toggle_content(elm){
	
	var content = jQuery(elm).data('id');
	jQuery('.up-layout-side li').removeClass('active');
	jQuery(elm).addClass('active');
	jQuery('.up_content').hide();
	jQuery('#'+content).show();
}
