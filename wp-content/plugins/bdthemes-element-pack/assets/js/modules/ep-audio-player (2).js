/**
 * Start audio player widget script
 */

( function( $, elementor ) {

	'use strict';

	//Audio Player
	var widgetAudioPlayer = function( $scope, $ ) {

		var $audioPlayer         = $scope.find( '.bdt-audio-player .jp-jplayer' ),
			$container 			 = $audioPlayer.next('.jp-audio').attr('id'),
			$settings 		 	 = $audioPlayer.data('settings');
			

		if ( ! $audioPlayer.length ) {
			return;
		}

		$($audioPlayer).jPlayer({
			ready: function (event) {
				$(this).jPlayer('setMedia', {
					title : $settings.audio_title,
					mp3   : $settings.audio_source
				});
				if($settings.autoplay) {
					$(this).jPlayer('play', 1);
				}
			},
			play: function() {
				$(this).next('.jp-audio').removeClass('bdt-player-played');
				$(this).jPlayer('pauseOthers');
			},
			ended: function() {
		    	$(this).next('.jp-audio').addClass('bdt-player-played');
		  	},
			timeupdate: function(event) {
				if($settings.time_restrict) {
					if ( event.jPlayer.status.currentTime > $settings.restrict_duration ) {
						$(this).jPlayer('stop');
					}
				}
			},

			cssSelectorAncestor : '#' + $container,
			useStateClassSkin   : true,
			autoBlur            : $settings.smooth_show,
			smoothPlayBar       : true,
			keyEnabled          : $settings.keyboard_enable,
			remainingDuration   : true,
			toggleDuration      : true,
			volume              : $settings.volume_level,
			loop                : $settings.loop
			
		});

	};


	jQuery(window).on('elementor/frontend/init', function() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-audio-player.default', widgetAudioPlayer );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/bdt-audio-player.bdt-poster', widgetAudioPlayer );
	});

}( jQuery, window.elementorFrontend ) );

/**
 * Start audio player widget script
 */

