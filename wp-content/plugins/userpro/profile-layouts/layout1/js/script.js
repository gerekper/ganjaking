jQuery('document').ready(function(){
	/*jQuery('document').on('click','.main-content',function(){
		alert("ok");
		up_toggle_content(this);
	});*/
	
	jQuery('.up-layout-side li').click(function(){
		up_toggle_content(this);
	});
	
	userpro_collapse(jQuery('#up_profile_details'));
});

function up_toggle_content(elm){
	
	var content = jQuery(elm).data('id');
	jQuery('.up-layout-side li').removeClass('active');
	jQuery(elm).addClass('active');
	jQuery('.up_content').hide();
	jQuery('#'+content).show();
}