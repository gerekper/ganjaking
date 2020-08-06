// Word Rotate
/*
Plugin Name: 	Animated Headlines
Written by: 	Codyhouse - (https://codyhouse.co/demo/animated-headlines/index.html)
*/
jQuery(document).ready(function($){
	'use strict';

	//set animation timing
	var animationDelay = 2500,
		//loading bar effect
		barAnimationDelay = 3800,
		barWaiting = barAnimationDelay - 3000, //3000 is the duration of the transition on the loading bar - set in the scss/css file
		//letters effect
		lettersDelay = 50,
		//type effect
		typeLettersDelay = 150,
		selectionDuration = 500,
		typeAnimationDelay = selectionDuration + 800,
		//clip effect 
		revealDuration = 600,
		revealAnimationDelay = 1500;

	initHeadline($('body'));

	$(document.body).on('porto_init_fancytext', function(e, $obj) {
		initHeadline( $obj );
	});

	function initHeadline($wrap) {
		//insert <i> element for each letter of a changing word
		singleLetters($wrap.find('.word-rotator.letters .word-rotator-items').find('b'));
		//initialise headline animation
		animateHeadline($wrap.find('.word-rotator'));

	}

	function singleLetters($words) {
		$words.each(function(){
			var word = $(this),
				letters = word.text().split(''),
				selected = word.hasClass('active');
			for (var i in letters) {
				if(word.parents('.rotate-2').length > 0) letters[i] = '<em>' + letters[i] + '</em>';
				letters[i] = (selected) ? '<i class="in">' + letters[i] + '</i>': '<i>' + letters[i] + '</i>';
			}
			var newLetters = letters.join('');
			word.html(newLetters).css('opacity', 1);
		});
	}

	function animateHeadline($headlines) {
		var duration = animationDelay;
		$headlines.each(function(){
			var headline = $(this),
				options = JSON.parse(headline.data('plugin-options').replace(/'/g,'"').replace(';',''));
			if (options && options.waittime) {
				duration = parseInt(options.waittime, 10);
			}

			if(headline.hasClass('loading-bar')) {
				duration = barAnimationDelay;
				setTimeout(function(){ headline.find('.word-rotator-items').addClass('is-loading') }, barWaiting);
			} else if (headline.hasClass('clip')){
				var spanWrapper = headline.find('.word-rotator-items'),
					newWidth = spanWrapper.outerWidth() + 10
				spanWrapper.css('width', newWidth);
			} else if (!headline.hasClass('type') ) {
				//assign to .word-rotator-items the width of its longest word
				var words = headline.find('.word-rotator-items b'),
					width = 0;
				words.each(function(){
					var wordWidth = $(this).outerWidth();
					if (wordWidth > width) width = wordWidth;
				});
				headline.find('.word-rotator-items').css('width', width);
			};

			//trigger animation
			setTimeout(function(){ hideWord( headline.find('.active').eq(0), duration ) }, duration);

			if (options.pauseOnHover) {
				headline.on('mouseenter', function() {
					headline.addClass('pause');
				}).on('mouseleave', function() {
					headline.removeClass('pause');
					setTimeout(function(){ hideWord( headline.find('.active').eq(0), duration ) }, duration);
				});
			}
		});
	}

	function hideWord($word, duration) {
		if ($word.closest('.word-rotator').hasClass('pause')) {
			return;
		}
		var nextWord = takeNext($word);

		if($word.parents('.word-rotator').hasClass('type')) {
			var parentSpan = $word.parent('.word-rotator-items');
			parentSpan.addClass('selected').removeClass('waiting');	
			setTimeout(function(){ 
				parentSpan.removeClass('selected'); 
				$word.removeClass('active').addClass('inactive').children('i').removeClass('in').addClass('out');
			}, selectionDuration);
			setTimeout(function(){ showWord(nextWord, typeLettersDelay, duration) }, typeAnimationDelay);
		} else if($word.parents('.word-rotator').hasClass('letters')) {
			var bool = ($word.children('i').length >= nextWord.children('i').length) ? true : false;
			hideLetter($word.find('i').eq(0), $word, bool, lettersDelay, duration);
			showLetter(nextWord.find('i').eq(0), nextWord, bool, lettersDelay, duration);

		} else if($word.parents('.word-rotator').hasClass('clip')) {
			$word.parents('.word-rotator-items').animate({ width : '2px' }, revealDuration, function(){
				switchWord($word, nextWord);
				showWord(nextWord, undefined, duration);
			});
		} else if ($word.parents('.word-rotator').hasClass('loading-bar')){
			$word.parents('.word-rotator-items').removeClass('is-loading');
			switchWord($word, nextWord);
			setTimeout(function(){ hideWord(nextWord, duration) }, barAnimationDelay);
			setTimeout(function(){ $word.parents('.word-rotator-items').addClass('is-loading') }, barWaiting);
		} else {
			switchWord($word, nextWord);
			setTimeout(function(){ hideWord(nextWord, duration) }, duration);
		}
	}

	function showWord($word, $duration, delay) {
		if($word.parents('.word-rotator').hasClass('type')) {
			showLetter($word.find('i').eq(0), $word, false, $duration, delay);
			$word.addClass('active').removeClass('inactive');

		}  else if($word.parents('.word-rotator').hasClass('clip')) {
			$word.parents('.word-rotator-items').animate({ 'width' : $word.outerWidth() + 10 }, revealDuration, function(){ 
				setTimeout(function(){ hideWord($word, delay) }, revealAnimationDelay); 
			});
		}
	}

	function hideLetter($letter, $word, $bool, $duration, delay) {
		$letter.removeClass('in').addClass('out');
		
		if(!$letter.is(':last-child')) {
			setTimeout(function(){ hideLetter($letter.next(), $word, $bool, $duration, delay); }, $duration);
		} else if($bool) { 
			setTimeout(function(){ hideWord(takeNext($word), delay) }, delay);
		}

		if($letter.is(':last-child') && $('html').hasClass('no-csstransitions')) {
			var nextWord = takeNext($word);
			switchWord($word, nextWord);
		} 
	}

	function showLetter($letter, $word, $bool, $duration, delay) {
		$letter.addClass('in').removeClass('out');
		
		if(!$letter.is(':last-child')) { 
			setTimeout(function(){ showLetter($letter.next(), $word, $bool, $duration, delay); }, $duration); 
		} else { 
			if($word.parents('.word-rotator').hasClass('type')) { setTimeout(function(){ $word.parents('.word-rotator-items').addClass('waiting'); }, 200);}
			if(!$bool) { setTimeout(function(){ hideWord($word, delay) }, delay) }

			if(!$word.closest('.word-rotator').hasClass('type')) {
				$word.closest('.word-rotator-items').animate({
					width: $word.outerWidth()
				});
			}
		}
	}

	function takeNext($word) {
		return (!$word.is(':last-child')) ? $word.next() : $word.parent().children().eq(0);
	}

	function takePrev($word) {
		return (!$word.is(':first-child')) ? $word.prev() : $word.parent().children().last();
	}

	function switchWord($oldWord, $newWord) {
		$oldWord.removeClass('active').addClass('inactive');
		$newWord.removeClass('inactive').addClass('active');

		if(!$newWord.closest('.word-rotator').hasClass('clip')) {
			var space = 0,
				delay = ( $newWord.outerWidth() > $oldWord.outerWidth() ) ? 0 : 600;

			if($newWord.closest('.word-rotator').hasClass('loading-bar') || $newWord.closest('.word-rotator').hasClass('slide')) {
				space = 3;
				delay = 0;
			}

			setTimeout(function(){
				$newWord.closest('.word-rotator-items').animate({
					width: $newWord.outerWidth() + space
				});
			}, delay);
		}
	}
});