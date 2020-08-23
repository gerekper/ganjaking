jQuery(document).ready(function($){

	// Tooltips
	$(".tips, .help_tip").tipTip({
			'attribute' : 'data-tip',
			'fadeIn' : 50,
			'fadeOut' : 50,
			'delay' : 200
	});
	
	function showType() {
		var csv_type = $("#csv_type").val();
		$(".csv_types").hide();
		if(csv_type === "quantity")
		{
			$(".csv_quantity").show();
		}
		else if(csv_type === "indicator")
		{
			$(".csv_indicator").show();
		}
	}
	
	$("#csv_type").change(function(){showType()});
	showType();

	$(document).on('click', '.csvupload_submit_btn',function()
	{
		$('.csvupload_form').hide()
		$('.csv_upload_iframe').show();
	});

    $(document).ready(function(){

        $('.fullinfo').click(function(){

            if($(this).prop("checked") == true){

                $('.slidesection').slideDown();

            }

            else if($(this).prop("checked") == false){

              $('.slidesection').slideUp();

            }

        });

    });

  


});