/*jQuery(document).ready(function(){jQuery(".ult_exp_content").hide(),jQuery(".ult_exp_section").click(function(){var e=jQuery(this).data("effect");if(jQuery(this).toggleClass("ult_active_section"),jQuery(this).next(".ult_exp_content").toggleClass("ult_active_section"),"slideToggle"==e&&jQuery(this).next(".ult_exp_content").slideToggle(500),"fadeToggle"==e){var t=jQuery(this).next(".ult_exp_content");t.is(":visible")?t.fadeTo(500,0,function(){t.slideUp()}):t.slideDown(function(){t.fadeTo(500,1)})}jQuery(this).trigger("select")}),jQuery(".ult_exp_section").select(function(){var e=jQuery(this).data("title"),t=jQuery(this).data("newtitle"),i=jQuery(this).data("icon"),r=jQuery(this).data("newicon"),s=jQuery(this).data("img"),n=jQuery(this).data("newimg"),u=jQuery(this).data("activetitle"),a=jQuery(this).data("activebg"),c=jQuery(this).data("activeicon"),o=jQuery(this).data("activeiconbg"),l=jQuery(this).data("activeborder");jQuery(this).css({color:u}),jQuery(this).parent().find(".ult_exp_section").css({background:a}),jQuery(this).find(".ult_expsection_icon").css({color:c}),jQuery(this).find(".ult_expsection_icon").css({background:o}),jQuery(this).find(".ult_expsection_icon").css({"border-color":l}),jQuery(this).find(".ult_expheader").stop().css("opacity","0.1").html(function(i,r){return r==t?e:t}).animate({opacity:1},300),jQuery(this).hasClass("ult_active_section")?(jQuery(this).find(".ult_ex_icon").removeClass(i),jQuery(this).find(".ult_ex_icon").fadeOut(100).switchClass(i,r,1500,"easeInOutQuad").fadeIn(300),jQuery(this).find(".ult_exp_img").fadeOut(200).attr("src",n).fadeIn(500)):(jQuery(this).find(".ult_ex_icon").removeClass(r),jQuery(this).find(".ult_ex_icon").fadeOut(100).switchClass(r,i,1500,"easeInOutQuad").fadeIn(300),jQuery(this).find(".ult_exp_img").fadeOut(200).attr("src",s).fadeIn(500))})}),jQuery(document).ready(function(){function e(){jQuery(".ult_exp_section_layer").each(function(e,t){jQuery(t).css({"margin-left":0});var i=jQuery(t).find(".ult_exp_section").data("override");if(0!=i){var r="true";if(jQuery(t).parents(".wpb_row").length>0)var s=jQuery(t).parents(".wpb_column");else if(jQuery(t).parents(".wpb_column").length>0)var s=jQuery(t).parents(".wpb_row");else var s=jQuery(t).parent();if("full"==i&&(s=jQuery("body"),r="false"),"ex-full"==i&&(s=jQuery("html"),r="false"),!isNaN(i))for(var e=1;i>e&&"HTML"!=s.prop("tagName");e++)s=s.parent();if("false"==r)var n=s.outerWidth();else var n=s.width();var u=s.offset().left,a=jQuery(t).offset().left,c=u-a;jQuery(t).css("false"==r?{width:n,"margin-left":c}:{width:n})}})}jQuery(".ult_exp_section").hover(function(){var e=jQuery(this).data("texthover"),t=jQuery(this).data("ihover"),i=(jQuery(this).data("cnthvrbg"),jQuery(this).data("headerhover")),r=jQuery(this).data("icnhvrbg"),s=jQuery(this).data("icnhvrborder");jQuery(this).hasClass("ult_active_section")||(jQuery(this).css({color:e,background:i}),jQuery(this).find(".ult_expsection_icon").css({color:t}),jQuery(this).find(".ult_expsection_icon").css({background:r}),jQuery(this).find(".ult_expsection_icon").css({"border-color":s}))},function(){var e=jQuery(this).data("textcolor"),t=jQuery(this).data("icncolor"),i=(jQuery(this).data("cntbg"),jQuery(this).data("headerbg")),r=jQuery(this).data("icnbg"),s=jQuery(this).data("icnborder");jQuery(this).hasClass("ult_active_section")||(jQuery(this).css({color:e,background:i}),jQuery(this).find(".ult_expsection_icon").css({color:t}),jQuery(this).find(".ult_expsection_icon").css({background:r}),jQuery(this).find(".ult_expsection_icon").css({"border-color":s}))}),jQuery(".ult_exp_content").hover(function(){jQuery(this).parent().find(".ult_exp_section").data("cnthvrbg")},function(){jQuery(this).parent().find(".ult_exp_section").data("cntbg")}),jQuery(window).resize(function(){e()}),e()});*/

jQuery( document ).ready( function () {
	jQuery( '.ult_exp_content' ).hide();
	//toggle the componenet with class msg_body
	jQuery( '.ult_exp_section' ).click( function () {
		const effect = jQuery( this ).data( 'effect' );

		jQuery( this ).toggleClass( 'ult_active_section' );

		jQuery( this )
			.next( '.ult_exp_content' )
			.toggleClass( 'ult_active_section' );
		if ( effect == 'slideToggle' ) {
			jQuery( this ).next( '.ult_exp_content' ).slideToggle( 500 );
		}
		if ( effect == 'fadeToggle' ) {
			//jQuery(this).next(".ult_exp_content").fadeToggle(500);
			const child = jQuery( this ).next( '.ult_exp_content' );
			if ( child.is( ':visible' ) ) {
				child.fadeTo( 500, 0.0, function () {
					child.slideUp();
				} );
			} else {
				child.slideDown( function () {
					child.fadeTo( 500, 1.0 );
				} );
			}
		}
		/* bind trigger on click event*/
		jQuery( this ).trigger( 'select' );
		/* trigger on click event*/
		jQuery( document ).trigger(
			'ult-expandable',
			jQuery( this ).next( '.ult_exp_content' )
		);
	} );

	jQuery( '.ult_exp_section' ).select( function () {
		const title = jQuery( this ).data( 'title' );
		const title1 = jQuery( this ).data( 'newtitle' );
		const icn = jQuery( this ).data( 'icon' );
		const icn1 = jQuery( this ).data( 'newicon' );
		const img = jQuery( this ).data( 'img' );
		const img1 = jQuery( this ).data( 'newimg' );

		const activetitle = jQuery( this ).data( 'activetitle' );
		const activebg = jQuery( this ).data( 'activebg' );
		const acticon = jQuery( this ).data( 'activeicon' );
		const actiocnbg = jQuery( this ).data( 'activeiconbg' );
		const activeborder = jQuery( this ).data( 'activeborder' );

		jQuery( this ).css( { color: activetitle } );
		jQuery( this )
			.parent()
			.find( '.ult_exp_section' )
			.css( { background: activebg } );
		jQuery( this ).find( '.ult_expsection_icon' ).css( { color: acticon } );
		jQuery( this )
			.find( '.ult_expsection_icon' )
			.css( { background: actiocnbg } );
		jQuery( this )
			.find( '.ult_expsection_icon' )
			.css( { 'border-color': activeborder } );
		if ( title != title1 ) {
			jQuery( this )
				.find( '.ult_expheader' )
				.stop()
				.css( 'opacity', '0.1' )
				.html( function ( _, oldText ) {
					return oldText == title1 ? title : title1;
				} )
				.animate(
					{
						opacity: 1,
					},
					300
				);
		}
		/*----icon replace---------*/
		if ( jQuery( this ).hasClass( 'ult_active_section' ) ) {
			if ( icn !== icn1 ) {
				jQuery( this ).find( '.ult_ex_icon' ).removeClass( icn );
				jQuery( this )
					.find( '.ult_ex_icon' )
					.fadeOut( 100 )
					.switchClass( icn, icn1, 1500, 'easeInOutQuad' )
					.fadeIn( 300 );
			}

			if ( img !== img1 ) {
				jQuery( this )
					.find( '.ult_exp_img' )
					.fadeOut( 200 )
					.attr( 'src', img1 )
					.fadeIn( 500 );
			}
		} else {
			if ( icn !== icn1 ) {
				jQuery( this ).find( '.ult_ex_icon' ).removeClass( icn1 );
				jQuery( this )
					.find( '.ult_ex_icon' )
					.fadeOut( 100 )
					.switchClass( icn1, icn, 1500, 'easeInOutQuad' )
					.fadeIn( 300 );
			}

			if ( img !== img1 ) {
				jQuery( this )
					.find( '.ult_exp_img' )
					.fadeOut( 200 )
					.attr( 'src', img )
					.fadeIn( 500 );
			}
		}
	} );
} );

jQuery( document ).ready( function () {
	jQuery( '.ult_exp_section' ).hover(
		function () {
			const texthover = jQuery( this ).data( 'texthover' );
			const ihover = jQuery( this ).data( 'ihover' );
			const cnthvrbg = jQuery( this ).data( 'cnthvrbg' );
			const headerhover = jQuery( this ).data( 'headerhover' );
			const icnhvrbg = jQuery( this ).data( 'icnhvrbg' );
			const icnhvrborder = jQuery( this ).data( 'icnhvrborder' );
			if ( jQuery( this ).hasClass( 'ult_active_section' ) ) {
			} else {
				jQuery( this ).css( {
					color: texthover,
					background: headerhover,
				} );
				jQuery( this )
					.find( '.ult_expsection_icon' )
					.css( { color: ihover } );
				jQuery( this )
					.find( '.ult_expsection_icon' )
					.css( { background: icnhvrbg } );
				jQuery( this )
					.find( '.ult_expsection_icon' )
					.css( { 'border-color': icnhvrborder } );
			}
		},
		function () {
			const textcolor = jQuery( this ).data( 'textcolor' );
			const icncolor = jQuery( this ).data( 'icncolor' );
			const cntbg = jQuery( this ).data( 'cntbg' );
			const headerbg = jQuery( this ).data( 'headerbg' );
			const icnbg = jQuery( this ).data( 'icnbg' );
			const icnborder = jQuery( this ).data( 'icnborder' );

			if ( jQuery( this ).hasClass( 'ult_active_section' ) ) {
			} else {
				jQuery( this ).css( {
					color: textcolor,
					background: headerbg,
				} );

				jQuery( this )
					.find( '.ult_expsection_icon' )
					.css( { color: icncolor } );
				jQuery( this )
					.find( '.ult_expsection_icon' )
					.css( { background: icnbg } );
				jQuery( this )
					.find( '.ult_expsection_icon' )
					.css( { 'border-color': icnborder } );
			}
		}
	);

	jQuery( '.ult_exp_content' ).hover(
		function () {
			const bg = jQuery( this )
				.parent()
				.find( '.ult_exp_section' )
				.data( 'cnthvrbg' );
		},
		function () {
			const bg1 = jQuery( this )
				.parent()
				.find( '.ult_exp_section' )
				.data( 'cntbg' );
		}
	);

	function resize_ult_section() {
		jQuery( '.ult_exp_section_layer' ).each( function ( i, element ) {
			jQuery( element ).css( { 'margin-left': 0 } );
			const override = jQuery( element )
				.find( '.ult_exp_section' )
				.data( 'override' );

			if ( override != 0 ) {
				let is_relative = 'true';
				if ( jQuery( element ).parents( '.wpb_row' ).length > 0 )
					var ancenstor = jQuery( element ).parents( '.wpb_column' );
				else if (
					jQuery( element ).parents( '.wpb_column' ).length > 0
				)
					var ancenstor = jQuery( element ).parents( '.wpb_row' );
				else var ancenstor = jQuery( element ).parent();

				const parent = ancenstor;
				if ( override == 'full' ) {
					ancenstor = jQuery( 'body' );
					is_relative = 'false';
				}
				if ( override == 'ex-full' ) {
					ancenstor = jQuery( 'html' );
					is_relative = 'false';
				}
				if ( ! isNaN( override ) ) {
					for ( var i = 1; i < override; i++ ) {
						if ( ancenstor.prop( 'tagName' ) != 'HTML' ) {
							ancenstor = ancenstor.parent();
						} else {
							break;
						}
					}
				}
				if ( is_relative == 'false' ) {
					var w = ancenstor.outerWidth();
				} else {
					var w = ancenstor.width();
				}

				const a_left = ancenstor.offset().left;
				const left = jQuery( element ).offset().left;
				const calculate_left = a_left - left;

				if ( is_relative == 'false' ) {
					jQuery( element ).css( {
						width: w,
						'margin-left': calculate_left,
					} );
				} else {
					jQuery( element ).css( { width: w } );
				}
				//jQuery(element).css({'width':w, 'margin-left' : calculate_left });
			}
		} );
	}

	jQuery( window ).resize( function () {
		resize_ult_section();
	} );
	resize_ult_section();
} );

jQuery( document ).ready( function () {
	jQuery( '.ult_exp_section' ).select( function () {
		//console.log("jo");
		const ht = jQuery( this ).data( 'height' );

		if ( ht != 0 ) {
			const top = jQuery( this ).offset().top;
			const ntop = parseInt( top ) - ht;
			jQuery( 'html, body' ).animate( { scrollTop: ntop }, 1200 );
		}
	} );
} );
