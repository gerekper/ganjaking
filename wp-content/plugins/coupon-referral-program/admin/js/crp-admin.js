/* ADMIN JS */
( function() {
	function crp_init() {
		if(document.getElementById("mwb_crp_referral_link")) {

			document.getElementById("mwb_crp_referral_link").setAttribute("readonly", true);

		}

		if(document.querySelector('#referral_button_text')) {

			document.querySelector("#referral_button_text").onkeyup = function (e) {

				var html = this.value;
				if( html == '' ){
					html = 'Referral Program';
				}
				document.querySelector(".mwb_crp_preview_div").innerHTML = html;
			}
		}

		if(document.querySelector('.mwb_crp_image_button')) { 

			document.querySelector('.mwb_crp_image_button').onclick=function(e)
			{
				e.preventDefault();
				var id=document.querySelector('#mwb_cpr_image').innerHTML;
				var image = wp.media({ 
					title: 'Upload Image',
				multiple: false     // false for single image
			}).open()
				.on('select', function(e){

					var uploaded_image = image.state().get('selection').first();

					console.log(uploaded_image);
					var image_url = uploaded_image.toJSON().url;
					console.log(image_url);
					document.querySelector('#mwb_cpr_image_display').setAttribute("src",image_url);
					document.querySelector('#mwb_cpr_image').value=image_url;
				});
			}
		}

		if(document.querySelector('.mwb_crp_image_resetbtn')) {

			document.querySelector('.mwb_crp_image_resetbtn').onclick=function(e)
			{
				e.preventDefault();
				var imgurl =woocommerce_img.img_url;
				document.querySelector('#mwb_cpr_image_display').setAttribute("src",imgurl);
				document.querySelector('#mwb_cpr_image').value=imgurl;
			}
		}
		if(document.querySelector('#mwb_crp_reffral_signup_enable')) {
			document.querySelector('#mwb_crp_reffral_signup_enable').onclick=function(e) {
				var mwb_crp_refral_signup = document.getElementById("mwb_crp_reffral_signup_enable"); 
				var mwb_crp_signup = document.getElementById("mwb_crp_signup_enable"); 
				if(mwb_crp_refral_signup.checked == true){
					mwb_crp_signup.checked = false;
                	
            	}
			}
		}
		if(document.querySelector('#mwb_crp_signup_enable')) {
			document.querySelector('#mwb_crp_signup_enable').onclick=function(e) {
  				var mwb_crp_signup = document.getElementById("mwb_crp_signup_enable"); 
  				var mwb_crp_refral_signup = document.getElementById("mwb_crp_reffral_signup_enable"); 
				if(mwb_crp_signup.checked == true){
					mwb_crp_refral_signup.checked = false;
            	}
			}
		}
		if(document.querySelector('#referral_discount_type')) {
			document.querySelector('#referral_discount_type').onchange=function() {
				var e = document.getElementById("referral_discount_type");
				var value = e.options[e.selectedIndex].value;
				var text = e.options[e.selectedIndex].text;
				if(value == 'mwb_cpr_referral_fixed') {
					var rows = document.querySelector('#referral_discount_upto').closest("tr");
					rows.style.display="none";
				}
				else {
					var rows = document.querySelector('#referral_discount_upto').closest("tr");
					rows.style.display="";
				}
			}

			var e = document.getElementById("referral_discount_type");
			var value = e.options[e.selectedIndex].value;
			var text = e.options[e.selectedIndex].text;
			if(value == 'mwb_cpr_referral_fixed') {
				var rows = document.querySelector('#referral_discount_upto').closest("tr");
				rows.style.display="none";
			}
			else {
				var rows = document.querySelector('#referral_discount_upto').closest("tr");
				rows.style.display="";
			}
			
		}
		
   	}
   	document.addEventListener( 'DOMContentLoaded', crp_init );
   }() );