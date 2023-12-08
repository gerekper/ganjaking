(function ($) {
	/*Instagram Feed*/
	var InstagramFeedList = function( $scope, $ ) {

		var InstagramFeedList = $scope.find('.theplus-instagram-feed'),
			options = InstagramFeedList.find('.theplus-insta-grid'),
			instafeed_by =options.data("instafeed_by"),
			caption_count = options.data('caption-count'),
			caption  = (options.data('caption') === 'show-caption') ? '<span class="insta-caption">{{caption}}</span>' : '',
			popupimage = options.data('popup-image'),	
			loadmoretext = options.data('loadmore-text'),
			loadingtext = options.data('loading-text'),
			instaimage = (options.data('instaimage') === 'show-instaimage') ? '<div class="insta-image"><a href="{{image}}" class="insta-pop-image"><img src="' + popupimage + '" /></a></div>' : '',
			likes = (options.data('likes') === 'yes') ? '<span class="theplus-insta-post-likes"> <i class="far fa-heart" aria-hidden="true"></i> {{likes}}</span>' : '',
			comments = (options.data('comments') === 'yes') ? '<span class="theplus-insta-post-comments"><i class="far fa-comments" aria-hidden="true"></i> {{comments}}</span>' : '',
			link_target = (options.data('link-target') === 'yes') ? 'target="_blank"' : '',
			link  = (options.data('link') === 'yes') ? '<a href=\"{{link}}\" ' +link_target+ ' class="insta-link"></a>' : '',
			carousels = options.data('carousels'),		
			column = options.data('column'),		
			strager = options.data('strager'),		
			layoutstyle = options.data('layoutstyle');	
			if(layoutstyle=='style-2'){
				var style_template ='<div class="theplus-insta-feed grid-item theplus-insta-box '+ column +' '+ strager + '"><div class="theplus-insta-feed-inner"><div class="theplus-insta-feed-wrap"><div class="theplus-insta-img-wrap"><img src="{{image}}" /></div><div class="theplus-insta-info-wrap"><div class="theplus-insta-info-wrap-inner">' + caption + '</div><div class="insta-like-comment-img"><div class="theplus-insta-likes-comments">' + likes + comments + '</div>'+ instaimage +'</div></div>' + link + '</div></div></div>';
			}else{
				var style_template ='<div class="theplus-insta-feed grid-item theplus-insta-box  '+ column +' '+ strager + '"><div class="theplus-insta-feed-inner"><div class="theplus-insta-feed-wrap"><div class="theplus-insta-img-wrap"><img src="{{image}}" /></div><div class="theplus-insta-info-wrap"><div class="theplus-insta-info-wrap-inner"><div class="theplus-insta-likes-comments">'+ instaimage + likes + comments + '</div>' + caption + '</div>'+ link + '</div></div></div></div>';
			}
		if(InstagramFeedList.length > 0){
			
			if(instafeed_by=='username'){
				
				var username =options.data('username');
				
				if(username!='' && username!=undefined){
					
					//var url = "https://cors-anywhere.herokuapp.com/https://www.instagram.com/"+username;
					var url = "https://www.instagram.com/"+username;
					$.ajax({
						url: url,
						type: "GET",
						//beforeSend: function(xhr){xhr.setRequestHeader("x-requested-with", 'instagram.com');},
						success: function(data){
							data = data.split("window._sharedData = ");
							data = data[1].split("<\/script>");
							data = data[0];
							data = data.substr(0, data.length - 1);
							data = JSON.parse(data);
							data = data.entry_data.ProfilePage[0].graphql.user;
							
							var html = "";
							if(data.is_private){
								html += "<div class='plus-insta-private'><strong>This profile is private</strong></div>";
							} else {
								var imgs = data.edge_owner_to_timeline_media.edges;
									max = (imgs.length > options.data('limit')) ? options.data('limit') : imgs.length;
								
								for(var i = 0; i < max; i++) {
						
									likes = (options.data('likes') === 'yes') ? '<span class="theplus-insta-post-likes"> <i class="far fa-heart" aria-hidden="true"></i> '+imgs[i].node.edge_media_preview_like.count+'</span>' : '';
									
									comments = (options.data('comments') === 'yes') ? '<span class="theplus-insta-post-comments"><i class="far fa-comments" aria-hidden="true"></i> '+imgs[i].node.edge_media_to_comment.count+'</span>' : '';
									
									instaimage = (options.data('instaimage') === 'show-instaimage') ? '<div class="insta-image"><a href="'+imgs[i].node.display_url+'" class="insta-pop-image"><img src="' + popupimage + '" /></a></div>' : '';
									
									
									if(imgs[i].node.edge_media_to_caption.edges.length > 0){
										var caption_count = options.data('caption-count'),
										caption_text = imgs[i].node.edge_media_to_caption.edges[0].node.text,
										caption_display = caption_text.substring(0, caption_count);
										caption = (options.data('caption') === 'show-caption') ? '<span class="insta-caption">'+caption_display+'</span>' : '';
									}else{
										caption ='';
									}
									
									link = (options.data('link') === 'yes') ? '<a href="https://www.instagram.com/p/'+ imgs[i].node.shortcode+'" ' +link_target+ ' class="insta-link"></a>' : '';
									
									if(layoutstyle=='style-2'){
										html +='<div class="theplus-insta-feed grid-item theplus-insta-box '+ column +' '+ strager + '"><div class="theplus-insta-feed-inner"><div class="theplus-insta-feed-wrap"><div class="theplus-insta-img-wrap"><img src="'+imgs[i].node.display_url+'" /></div><div class="theplus-insta-info-wrap"><div class="theplus-insta-info-wrap-inner">' + caption + '</div><div class="insta-like-comment-img"><div class="theplus-insta-likes-comments">' + likes + comments + '</div>'+ instaimage +'</div></div>' + link + '</div></div></div>';
									}else{
										html +='<div class="theplus-insta-feed grid-item theplus-insta-box  '+ column +' '+ strager + '"><div class="theplus-insta-feed-inner"><div class="theplus-insta-feed-wrap"><div class="theplus-insta-img-wrap"><img src="'+imgs[i].node.display_url+'" /></div><div class="theplus-insta-info-wrap"><div class="theplus-insta-info-wrap-inner"><div class="theplus-insta-likes-comments">'+ instaimage + likes + comments + '</div>' + caption + '</div>'+ link + '</div></div></div></div>';
									}
								}
							}
							InstagramFeedList.find('.post-inner-loop').html(html);
							InstagramFeedList.find(".plus-insta-loading").addClass("loaded");
							var $grid = InstagramFeedList.find('.theplus-insta-grid');
							if(InstagramFeedList.find('.insta-masonry-layout').length > 0){
								$grid.isotope({					
									columnWidth: '.theplus-insta-feed',
									itemSelector: '.grid-item',
									percentPosition: true
								});
								$grid.imagesLoaded().progress( function() {
									$grid.isotope('layout');						
								});
							}
							if(InstagramFeedList.find('.list-carousel-slick').length>0){
								var carousel_elem = $scope.find('.list-carousel-slick').eq(0);
								$grid.imagesLoaded().progress( function() {
									if (carousel_elem.length > 0) {																				
										if(!carousel_elem.hasClass("done-carousel")){										
											theplus_carousel_list('instagram');
										}
									}
								});
							}
						}
					});
					
				}else{
					InstagramFeedList.find('.post-inner-loop').html("<div class='plus-insta-private'><strong>Enter Instagram Username</strong></div>");
					InstagramFeedList.find(".plus-insta-loading").addClass("loaded");
				}
				
			}else{
				
				var instaUserId = options.data('user-id'),				
				instaAccessToken = options.data('access-token'),
				instaClientId = options.data('client-id'),
				noOfLoadLimit = options.data('limit'),
				instaResolution = options.data('resolution'),
				target = options.data('target'),
				data_id = options.data('id'),
				instaSortBy = options.data('sort-by');
				
				if(((instaAccessToken!='' && instaAccessToken!=undefined) || (instaClientId!='' && instaClientId!=undefined)) && (instaUserId!='' && instaUserId!=undefined)){
					var instaloadButton = InstagramFeedList.find('.theplus-load-more-button');
					var feed = new Instafeed({
						get: 'user',
						userId: instaUserId,
						clientId: ''+instaClientId+'',
						accessToken: ''+instaAccessToken+'',
						limit: ''+noOfLoadLimit+'',
						resolution: ''+instaResolution+'',
						sortBy: ''+instaSortBy+'',
						target: ''+target+'',
						template: style_template,				
						after: function() {							
							
							var el = $(this);
							if (el.classList)
								el.classList.add('show');
							else
								el.className += ' ' + 'show';

							if (!this.hasNext()) {
								$(instaloadButton).parent().addClass( 'no-pagination' );
								instaloadButton.attr('disabled', 'disabled');
							}
							
							InstagramFeedList.find(".plus-insta-loading").addClass("loaded");
							instaInitMasonry(data_id);
							if(InstagramFeedList.find('.list-carousel-slick').length>0){
								var carousel_elem = $scope.find('.list-carousel-slick').eq(0);
								InstagramFeedList.find('.theplus-insta-grid').imagesLoaded().progress( function() {
									if (carousel_elem.length > 0) {										
										if(!carousel_elem.hasClass("done-carousel")){
											theplus_carousel_list('instagram');
										}
									}
								});								
							}
							if(InstagramFeedList.find('.post-inner-loop .grid-item').length){
								InstagramFeedList.find('.post-inner-loop .grid-item').each( function () {
									var caption = $(this).find('.insta-caption').text();
									if(caption){
										var caption1 = caption.substring(0,caption_count);										
										$('.insta-caption',this).text(caption1);
									}
								});
							}
							
						},
						success: function() {
							$(instaloadButton).removeClass( 'button--loading' );
							$(instaloadButton).find( 'span' ).html(loadmoretext);
							
						}
					});
					
					instaloadButton.on('click', function() {					
						feed.next();
						$(instaloadButton).addClass( 'button--loading' );
						$(instaloadButton).find( 'span' ).html(loadingtext);
					});
					
					feed.run();
									
					function instaInitMasonry(data_id) {
						var $grid = $('.insta-masonry-layout.'+data_id);

						$grid.isotope({					
							columnWidth: '.theplus-insta-feed',
							itemSelector: '.grid-item',
							percentPosition: true
						});
						$grid.imagesLoaded().progress( function() {
							$grid.isotope('layout');						
						});
					}
				}
			}
		}
	}
	/*Instagram Feed*/
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-instagram.default',InstagramFeedList);
	});
})(jQuery);