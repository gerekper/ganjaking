// Word Rotate
/*
Plugin Name: 	Animated Headlines
Written by: 	Codyhouse - (https://codyhouse.co/demo/animated-headlines/index.html)
*/
(function ($) {

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

	function initHeadline($wrap) {
		//insert <i> element for each letter of a changing word
		singleLetters($wrap.find('.word-rotator.letters .word-rotator-items > b'));
		//initialise headline animation
		animateHeadline($wrap.find('.word-rotator'));

	}

	function destroyHeadline($wrap) {
		$wrap.find('.word-rotator .word-rotator-items > b').each(function(index) {
			this.innerHTML = $(this).text();
			$(this).removeAttr('style').removeClass('active').removeClass('inactive');
			if (0 === index) {
				$(this).addClass('active');
			}
		});
		$wrap.find('.word-rotator-items').css('width', '');
		$wrap.find('.word-rotator').removeClass('fancy-init');
		$wrap.find('.word-rotator').each(function() {
			var data = $(this).data('__fancytext');
			if (data) {
				if (data.hideWordTimer) {
					clearTimeout(data.hideWordTimer);
				}
				if (data.hideWordTimer1) {
					clearTimeout(data.hideWordTimer1);
				}
				if (data.hideWordTimer2) {
					clearTimeout(data.hideWordTimer2);
				}
				if (data.hideWordTimer3) {
					clearTimeout(data.hideWordTimer3);
				}
				if (data.hideWordTimer4) {
					clearTimeout(data.hideWordTimer4);
				}
				if (data.hideWordTimer5) {
					clearTimeout(data.hideWordTimer5);
				}
				if (data.hideWordTimer6) {
					clearTimeout(data.hideWordTimer6);
				}
				if (data.showWordTimer) {
					clearTimeout(data.showWordTimer);
				}
				if (data.hideLetterTimer) {
					clearTimeout(data.hideLetterTimer);
				}
				if (data.showLetterTimer) {
					clearTimeout(data.showLetterTimer);
				}
			}
			$(this).data('__fancytext', {});
		});
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
				options = JSON.parse(headline.attr('data-plugin-options').replace(/'/g,'"').replace(';','')),
				data = headline.data('__fancytext');
			if (typeof data == 'undefined') {
				data = {};
			}
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
			data.hideWordTimer = setTimeout(function(){ hideWord( headline.find('.active').eq(0), duration ) }, duration);

			if (options.pauseOnHover) {
				headline.on('mouseenter', function() {
					headline.addClass('pause');
				}).on('mouseleave', function() {
					headline.removeClass('pause');
					if (data.hideWordTimer1) {
						clearTimeout(data.hideWordTimer1);
					}
					data.hideWordTimer1 = setTimeout(function(){ hideWord( headline.find('.active').eq(0), duration ) }, duration);
				});
			}

			headline.addClass('fancy-init').data('__fancytext', data);
		});
	}

	function hideWord($word, duration) {
		var $parents = $word.closest('.word-rotator');
		if (!$word.length || $parents.hasClass('pause')) {
			return;
		}
		var nextWord = takeNext($word);
		$parents = $word.parents('.word-rotator');
		var data = $parents.data('__fancytext');
		if (typeof data == 'undefined') {
			data = {};
		}
		if($parents.hasClass('type')) {
			var parentSpan = $word.parent('.word-rotator-items');
			parentSpan.addClass('selected').removeClass('waiting');
			setTimeout(function(){ 
				parentSpan.removeClass('selected'); 
				$word.removeClass('active').addClass('inactive').children('i').removeClass('in').addClass('out');
			}, selectionDuration);
			data.showWordTimer = setTimeout(function(){ showWord(nextWord, typeLettersDelay, duration) }, typeAnimationDelay);
		} else if($parents.hasClass('letters')) {
			var bool = ($word.children('i').length >= nextWord.children('i').length) ? true : false;
			hideLetter($word.find('i').eq(0), $word, bool, lettersDelay, duration);
			showLetter(nextWord.find('i').eq(0), nextWord, bool, lettersDelay, duration);

		} else if($parents.hasClass('clip')) {
			$word.parents('.word-rotator-items').animate({ width : '2px' }, revealDuration, function(){
				switchWord($word, nextWord);
				showWord(nextWord, undefined, duration);
			});
		} else if ($parents.hasClass('loading-bar')){
			$word.parents('.word-rotator-items').removeClass('is-loading');
			switchWord($word, nextWord);
			data.hideWordTimer2 = setTimeout(function(){ hideWord(nextWord, duration) }, barAnimationDelay);
			setTimeout(function(){ $word.parents('.word-rotator-items').addClass('is-loading') }, barWaiting);
		} else {
			switchWord($word, nextWord);
			data.hideWordTimer3 = setTimeout(function(){ hideWord(nextWord, duration) }, duration);
		}
		$parents.data('__fancytext', data);
	}

	function showWord($word, $duration, delay) {
		if (!$word.length) {
			return;
		}
		var $parents = $word.parents('.word-rotator');
		if($parents.hasClass('type')) {
			showLetter($word.find('i').eq(0), $word, false, $duration, delay);
			$word.addClass('active').removeClass('inactive');

		}  else if($parents.hasClass('clip')) {
			$word.parents('.word-rotator-items').animate({ 'width' : $word.outerWidth() + 10 }, revealDuration, function() {
				var $parent = $(this).closest('.word-rotator'),
					data = $parent.data('__fancytext');
				if (typeof data == 'undefined') {
					data = {};
				}
				data.hideWordTimer4 = setTimeout(function(){ hideWord($word, delay) }, revealAnimationDelay); 
				$parent.data('__fancytext', data);
			});
		}
	}

	function hideLetter($letter, $word, $bool, $duration, delay) {
		$letter.removeClass('in').addClass('out');

		var $parent = $word.closest('.word-rotator'),
			data = $parent.data('__fancytext');
		if (typeof data == 'undefined') {
			data = {};
		}
		if(!$letter.is(':last-child')) {
			data.hideLetterTimer = setTimeout(function(){ hideLetter($letter.next(), $word, $bool, $duration, delay); }, $duration);
		} else if($bool) {
			data.hideWordTimer5 = setTimeout(function(){ hideWord(takeNext($word), delay) }, delay);
		}
		$parent.data('__fancytext', data);

		if($letter.is(':last-child') && $('html').hasClass('no-csstransitions')) {
			var nextWord = takeNext($word);
			switchWord($word, nextWord);
		} 
	}

	function showLetter($letter, $word, $bool, $duration, delay) {
		$letter.addClass('in').removeClass('out');

		var $parent = $word.closest('.word-rotator'),
			data = $parent.data('__fancytext');
		if (typeof data == 'undefined') {
			data = {};
		}

		if(!$letter.is(':last-child')) { 
			data.showLetterTimer = setTimeout(function(){ showLetter($letter.next(), $word, $bool, $duration, delay); }, $duration);
		} else { 
			if($word.parents('.word-rotator').hasClass('type')) { setTimeout(function(){ $word.parents('.word-rotator-items').addClass('waiting'); }, 200);}
			if(!$bool) {
				data.hideWordTimer6 = setTimeout(function(){ hideWord($word, delay) }, delay);
			}

			if(!$word.closest('.word-rotator').hasClass('type')) {
				$word.closest('.word-rotator-items').animate({
					width: $word.outerWidth()
				});
			}
		}
		$parent.data('__fancytext', data);
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
	vcv.on('ready', function (action, id, options, tag) {
		var updateAttrs = ['fancytext_strings', 'fancytext_align', 'ticker_wait_time', 'animation_effect', 'ticker_hover_pause'],
			skipCounter = (tag && tag !== 'portoFancyText') || (action === 'merge') || (options && options.changedAttribute && updateAttrs.indexOf(options.changedAttribute) === -1),
			fancyTextTimer = null;

		if (!skipCounter) {
			if (fancyTextTimer) {
				clearTimeout(fancyTextTimer);
			}
			fancyTextTimer = setTimeout(function() {
				if (id) {
					var $obj = $('#el-' + id);
					if ($obj.hasClass('fancy-init')) {
						destroyHeadline($obj.parent());
						initHeadline($obj.parent());
					} else {
						initHeadline($obj.parent());
					}
				} else {
					destroyHeadline($(document.body));
					initHeadline($(document.body));
				}
			}, action ? 100 : 10);
		}
	})
})(window.jQuery);