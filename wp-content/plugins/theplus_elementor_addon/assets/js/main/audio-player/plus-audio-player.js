(function ($) {
	"use strict";
	var audioMap = new Map();	
	var WidgetAudioPlayerHandler = function($scope, $) {
		var container = $scope.find('.tp-audio-player-wrapper');
		var id = container.data("id");
		var song,cutime;
		var tracker = $('.'+id+' .tracker');
		var volume = $('.'+id+' .volume');
		var durationtime = $('.'+id+' .durationtime');
		var currenttime = $('.'+id+' .currenttime');
		var style = container.data('style');
		var apvolume = container.data('apvolume');
        var eoautoplay = container.data('eoautoplay');
		
		function initAudio(elem,id) {
			var url = elem.attr('audiourl');

			var title = elem.attr('audioTitle');
			var artist = elem.attr('artist');
			var thumb = elem.attr('data-thumb');
			if(style=='style-3'){
				$('.'+id+' .tp-player .trackimage img').attr('src',thumb);
			}			
			if(style=='style-4'){
				$('.'+id+' .tp-player').css('background','url('+ thumb +')').css('transition', 'background 0.5s linear');				
			}
			if(style=='style-5'){
				$('.'+id+' .tp-player .ap-st5-img').css('background','url('+ thumb +')').css('transition', 'background 0.5s linear');				
			}
			if(style=='style-6'){
				$('.'+id+' .tp-player .ap-st5-content').css('background','url('+ thumb +')').css('transition', 'background 0.5s linear');				
			}
			if(style=='style-8'){
				$('.'+id+' .tp-player-bg-img').css('background','url('+ thumb +')');
				$('.'+id+' .tp-player .trackimage img').attr('src',thumb);
			}
			if(style=='style-9'){
				$('.'+id+' .tp-player-bg-img').css('background','url('+ thumb +')');				
			}
			
			$('.'+id+' .tp-player .title').text(title);
			$('.'+id+' .tp-player .artist').text(artist);

			// song = new Audio('media/'+url);
			song = new Audio(url);	
			audioMap.set(this, song);
			// timeupdate event listener
			song.addEventListener('timeupdate', function() {
				var curtime = parseInt(song.currentTime,10);
				tracker.slider('value', curtime);
				cutime = curtime;
				UpdateSeek(cutime);
				
				if(cutime === Math.round(song.duration)){
					var next = $('.'+id+' .playlist li.active').next();
					if (next.length === 0) {
						next = $('.'+id+' .playlist li:first-child');
					}

                    if(eoautoplay != 'disable'){
                        initAudio(next,id);
                        song.addEventListener('loadedmetadata', function() {
                            playAudio(id);
                        });
                    }					
				}
			});
			song.addEventListener('loadeddata', function playerLoadeddata(){
				durationtime.html(formatTime(song.duration));				
			}, true);
			
			$('.'+id+' .playlist li').removeClass('active');
			elem.addClass('active');
		}

		function playAudio(id) {
			stopAllAudio();
			song.play();

			tracker.slider("option", "max", song.duration);

			$('.'+id+' .play').addClass('hidden');
			$('.'+id+' .pause').addClass('visible');
		}
		
		function stopAllAudio(){
			audioMap.forEach(function(oldAudio){
				oldAudio.pause();
			});			
			$('.tp-audio-player-wrapper .play').removeClass('hidden');
			$('.tp-audio-player-wrapper .pause').removeClass('visible');
		}
		
		function stopAudio(id) {
			song.pause();

			$('.'+id+' .play').removeClass('hidden');
			$('.'+id+' .pause').removeClass('visible');
		}
		function UpdateSeek(a){
			currenttime.html(formatTime(a));
		}
		// play click
		$('.'+id+' .play').on('click',function(e) {
			e.preventDefault();
			// playAudio(id);
			audioMap.set(this,song);
			song.addEventListener('ended', function() {
				var next = $('.'+id+' .playlist li.active').next();
				if (next.length == 0) {
					next = $('.'+id+' .playlist li:first-child');
				}

                if(eoautoplay != 'disable'){                   
                    initAudio(next,id);
                    song.addEventListener('loadedmetadata', function() {
                        playAudio(id);					
                    });
                }

			}, false);
			
			tracker.slider("option", "max", song.duration);
            
			stopAllAudio();
            
            if( song.currentSrc ){
                song.play();
            }

			$('.'+id+' .play').addClass('hidden');
			$('.'+id+' .pause').addClass('visible');

		});

		// pause click
		$('.'+id+' .pause').on('click',function(e) {
			e.preventDefault();
			stopAudio(id);
		});

		// next track
		$('.'+id+' .fwd').on('click',function(e) {
			e.preventDefault();
			stopAudio(id);

			var next = $('.'+id+' .playlist li.active').next();
			if (next.length === 0) {
				next = $('.'+id+' .playlist li:first-child');
			}
			audioMap.set(this,song);
			initAudio(next,id);
			
			song.addEventListener('loadedmetadata', function() {
				stopAllAudio();
				playAudio(id);
			});
		});

		// prev track
		$('.'+id+' .rew').on('click',function(e) {
			e.preventDefault();
			stopAudio(id);

			var prev = $('.'+id+' .playlist li.active').prev();
			if (prev.length === 0) {
				prev = $('.'+id+' .playlist li:last-child');
			}
			audioMap.set(this,song);
			initAudio(prev,id);

			song.addEventListener('loadedmetadata', function() {
				stopAllAudio();
				playAudio(id);
			});
		});

		// show playlist
		$('.'+id+' .playlistIcon').on('click',function(e) {
			e.preventDefault();
			$('.'+id+' .playlist').toggleClass('show');
		});

		// playlist elements - click
		$('.'+id+' .playlist li').on('click',function() {
			stopAllAudio();
			audioMap.set(this,song);
			stopAudio(id);
			initAudio($(this),id);
			song.addEventListener('loadedmetadata', function() {
				playAudio(id);				
			});
		});

		// initialization - first element in playlist
		initAudio($('.'+id+' .playlist li:first-child'),id);

		//song.volume = 0.8;

		volume.slider({
			orientation: 'vertical',
			range: 'max',
			max: 100,
			min: 1,
			value: apvolume,
			start: function(event, ui) {},
			slide: function(event, ui) {
				song.volume = ui.value / 100;
			},
			stop: function(event, ui) {},
		});

		$('.'+id+' .vol-icon-toggle').on('click',function(e) {
			e.preventDefault();
			$('.'+id+' .tp-volume-bg').toggleClass('show');
		});

		// empty tracker slider
		tracker.slider({
			range: 'min',
			min: 0,
			max: 100,
			value: 0,
			start: function(event, ui) {},
			slide: function(event, ui) {
				song.currentTime = ui.value;
			},
			stop: function(event, ui) {}
		});
		
		function formatTime(val) {
			var h = 0, m = 0, s;
			val = parseInt(val, 10);
			if (val > 60 * 60) {
				h = parseInt(val / (60 * 60), 10);
				val -= h * 60 * 60;
			}
			if (val > 60) {
				m = parseInt(val / 60, 10);
				val -= m * 60;
			}
			s = val;
			val = (h > 0)? h + ':' : '';
			val += (m > 0)? ((m < 10 && h > 0)? '0' : '') + m + ':' : '0:';
			val += ((s < 10)? '0' : '') + s;
			return val;
		}
	};
	$(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/tp-audio-player.default', WidgetAudioPlayerHandler);
	});
})(jQuery);
function playAudio(id,song) {
	stopAllAudio();
	song.play();
	var tracker = jQuery('.'+id+' .tracker');
	tracker.slider("option", "max", song.duration);

	jQuery('.'+id+' .play').addClass('hidden');
	jQuery('.'+id+' .pause').addClass('visible');
}
function stopAllAudio(){
	audioMap.forEach(function(oldAudio){
		oldAudio.pause();
	});			
	$('.tp-audio-player-wrapper .play').removeClass('hidden');
	$('.tp-audio-player-wrapper .pause').removeClass('visible');
}
function stopAudio(id,song) {
	song.pause();

	jQuery('.'+id+' .play').removeClass('hidden');
	jQuery('.'+id+' .pause').removeClass('visible');
}
function UpdateSeek(id,a){
	var currenttime = jQuery('.'+id+' .currenttime');
	currenttime.html(loadformatTime(a));
}
function loadformatTime(val) {
	var h = 0, m = 0, s;
	val = parseInt(val, 10);
	if (val > 60 * 60) {
		h = parseInt(val / (60 * 60), 10);
		val -= h * 60 * 60;
	}
	if (val > 60) {
		m = parseInt(val / 60, 10);
		val -= m * 60;
	}
	s = val;
	val = (h > 0)? h + ':' : '';
	val += (m > 0)? ((m < 10 && h > 0)? '0' : '') + m + ':' : '0:';
	val += ((s < 10)? '0' : '') + s;
	return val;
}

function loadinitAudio(elem,id,style) {
	var $ = jQuery;
	var url = elem.attr('audiourl');
	var song,cutime;
	var title = elem.attr('audioTitle');
	var artist = elem.attr('artist');
	var thumb = elem.attr('data-thumb');
	var durationtime = $('.'+id+' .durationtime');
	if(style=='style-3'){
		$('.'+id+' .tp-player .trackimage img').attr('src',thumb);
	}			
	if(style=='style-4'){
		$('.'+id+' .tp-player').css('background','url('+ thumb +')').css('transition', 'background 0.5s linear');				
	}
	if(style=='style-5'){
		$('.'+id+' .tp-player .ap-st5-img').css('background','url('+ thumb +')').css('transition', 'background 0.5s linear');				
	}
	if(style=='style-6'){
		$('.'+id+' .tp-player .ap-st5-content').css('background','url('+ thumb +')').css('transition', 'background 0.5s linear');				
	}
	if(style=='style-8'){
		$('.'+id+' .tp-player-bg-img').css('background','url('+ thumb +')');
		$('.'+id+' .tp-player .trackimage img').attr('src',thumb);
	}
	if(style=='style-9'){
		$('.'+id+' .tp-player-bg-img').css('background','url('+ thumb +')');				
	}
	
	$('.'+id+' .tp-player .title').text(title);
	$('.'+id+' .tp-player .artist').text(artist);
	var tracker = $('.'+id+' .tracker');
	// song = new Audio('media/'+url);
	song = new Audio(url);
	// timeupdate event listener
	song.addEventListener('timeupdate', function() {
		var curtime = parseInt(song.currentTime,10);
		tracker.slider('value', curtime);
		cutime = curtime;
		UpdateSeek(id,cutime);
		if(cutime === Math.round(song.duration)){
			var next = $('.'+id+' .playlist li.active').next();
			if (next.length === 0) {
				next = $('.'+id+' .playlist li:first-child');
			}
			initAudio(next,id);
			song.addEventListener('loadedmetadata', function() {
				playAudio(id);
			});
		}
	});
	song.addEventListener('loadeddata', function playerLoadeddata(){
		durationtime.html(loadformatTime(song.duration));				
	}, true);
	
	$('.'+id+' .playlist li').removeClass('active');
	elem.addClass('active');
	
	$('.'+id+' .vol-icon-toggle').on('click',function(e) {
		e.preventDefault();
		$('.'+id+' .tp-volume-bg').toggleClass('show');
	});
	
	$('.'+id+' .play').on('click',function(e) {
		e.preventDefault();
		// playAudio(id);

		song.addEventListener('ended', function() {
			var next = $('.'+id+' .playlist li.active').next();
			if (next.length == 0) {
				next = $('.'+id+' .playlist li:first-child');
			}
			loadinitAudio(next,id);

			song.addEventListener('loadedmetadata', function() {
				playAudio(id,song);
				
			});

		}, false);
		
		tracker.slider("option", "max", song.duration);
		
		stopAllAudio();
		song.play();
		$('.'+id+' .play').addClass('hidden');
		$('.'+id+' .pause').addClass('visible');

	});

	// pause click
	$('.'+id+' .pause').on('click',function(e) {
		e.preventDefault();
		stopAudio(id,song);
	});

	// next track
	$('.'+id+' .fwd').on('click',function(e) {
		e.preventDefault();
		stopAudio(id,song);

		var next = $('.'+id+' .playlist li.active').next();
		if (next.length === 0) {
			next = $('.'+id+' .playlist li:first-child');
		}
		loadinitAudio(next,id);
		song.addEventListener('loadedmetadata', function() {
			stopAllAudio();
			playAudio(id,song);
		});
	});

	// prev track
	$('.'+id+' .rew').on('click',function(e) {
		e.preventDefault();
		stopAudio(id,song);

		var prev = $('.'+id+' .playlist li.active').prev();
		if (prev.length === 0) {
			prev = $('.'+id+' .playlist li:last-child');
		}
		loadinitAudio(prev,id);

		song.addEventListener('loadedmetadata', function() {
			stopAllAudio();
			playAudio(id,song);
		});
	});

	// show playlist
	$('.'+id+' .playlistIcon').on('click',function(e) {
		e.preventDefault();
		$('.'+id+' .playlist').toggleClass('show');
	});

	// playlist elements - click
	$('.'+id+' .playlist li').on('click',function() {
		stopAudio(id,song);
		loadinitAudio($(this),id);
		song.addEventListener('loadedmetadata', function() {
			stopAllAudio();
			playAudio(id,song);				
		});
	});
	var tracker = $('.'+id+' .tracker');
	var volume = $('.'+id+' .volume');
	var durationtime = $('.'+id+' .durationtime');
	var currenttime = $('.'+id+' .currenttime');
	var style = $(this).data('style');
	var apvolume = $(this).data('apvolume');
	volume.slider({
		orientation: 'vertical',
		range: 'max',
		max: 100,
		min: 1,
		value: apvolume,
		start: function(event, ui) {},
		slide: function(event, ui) {
			song.volume = ui.value / 100;
		},
		stop: function(event, ui) {},
	});
	tracker.slider({
		range: 'min',
		min: 0,
		max: 100,
		value: 0,
		start: function(event, ui) {},
		slide: function(event, ui) {
			song.currentTime = ui.value;
		},
		stop: function(event, ui) {}
	});
}