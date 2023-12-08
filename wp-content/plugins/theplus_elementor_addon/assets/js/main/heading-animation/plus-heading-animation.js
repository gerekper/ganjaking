(function($) {
    'use strict';
	$(document).ready(function() {
		plus_heading_animation();
	});
})(jQuery);
function plus_heading_animation(){	
	jQuery(document).ready(function($){
		"use strict";
		
		var animateDelay = 2500,
		
		baranimateDelay = 3800,
		barWaitingDelay = baranimateDelay - 3000, 
		
		charaterDelay = 50,		
		typecharaterDelay = 150,
		selectDurationTime = 500,
		typeanimateDelay = selectDurationTime + 800,
		
		revealDurationTime = 600,
		revealanimateDelay = 1500;
		
		pt_plus_initHeadline();
		
		
		function pt_plus_initHeadline() {
			singleCharater(jQuery('.pt-plus-cd-headline.letters').find('b'));
			animateHeadlineWord(jQuery('.pt-plus-cd-headline'));
		}
		
		function singleCharater($words) {
			$words.each(function(){
				var i;
				var word = jQuery(this),
				letters = word.text().split(''),
				selected = word.hasClass('is-visible');
                var j = 1;
				for (i in letters) {
                    if(j==1){
                        
                    }else{
                        if(word.parents('.rotate-2').length > 0) letters[i] = '<em>' + letters[i] + '</em>';
					    letters[i] = (selected) ? '<i class="in">' + letters[i] + '</i>': '<i>' + letters[i] + '</i>';
                    }
                    j++;
				}
				var newLetters = letters.join('');
				word.html(newLetters).css('opacity', 1);
			});
		}
		
		function animateHeadlineWord($headlineText) {
			var duration = animateDelay;
			$headlineText.each(function(){
				var headline = jQuery(this);
				
				if(headline.hasClass('loading-bar')) {
					duration = baranimateDelay;
					setTimeout(function(){ headline.find('.cd-words-wrapper').addClass('is-loading') }, barWaitingDelay);
					} else if (headline.hasClass('clip')){
					var spanWrapper = headline.find('.cd-words-wrapper'),
					newWidth = spanWrapper.width() + 10
					spanWrapper.css('width', newWidth);
					} else if (!headline.hasClass('type') ) {
					//assign to .cd-words-wrapper the width of its longest word
					var words = headline.find('.cd-words-wrapper b'),
					width = 0;
					words.each(function(){
						var word_W = jQuery(this).width();
						if (word_W > width) width = word_W;
					});
					headline.find('.cd-words-wrapper').css('width', width+12);
				};
				
				//trigger animation
				setTimeout(function(){ headlinehideWord( headline.find('.is-visible').eq(0) ) }, duration);
			});
		}
		
		function headlinehideWord($word) {
			var next_Word = takeNextWord($word);
			
			if($word.parents('.pt-plus-cd-headline').hasClass('type')) {
				var currentSpan = $word.parent('.cd-words-wrapper');
				currentSpan.addClass('selected').removeClass('waiting');	
				
				setTimeout(function(){ 
					currentSpan.removeClass('selected'); 
					$word.removeClass('is-visible').addClass('is-hidden').children('i').removeClass('in').addClass('out');
				}, selectDurationTime);
				
				setTimeout(function(){ displyWord(next_Word, typecharaterDelay) }, typeanimateDelay);
				
				} else if($word.parents('.pt-plus-cd-headline').hasClass('letters')) {
				
				var bool = ($word.children('i').length >= next_Word.children('i').length) ? true : false;
				hideCharater($word.find('i').eq(0), $word, bool, charaterDelay);
				displayLetter(next_Word.find('i').eq(0), next_Word, bool, charaterDelay);
				
				}  else if($word.parents('.pt-plus-cd-headline').hasClass('clip')) {
				$word.parents('.cd-words-wrapper').animate({ width : '2px' }, revealDurationTime, function(){
					switchChangeWord($word, next_Word);
					displyWord(next_Word);
				});
				
				} else if ($word.parents('.pt-plus-cd-headline').hasClass('loading-bar')){
				$word.parents('.cd-words-wrapper').removeClass('is-loading');
				switchChangeWord($word, next_Word);
				setTimeout(function(){ headlinehideWord(next_Word) }, baranimateDelay);
				setTimeout(function(){ $word.parents('.cd-words-wrapper').addClass('is-loading') }, barWaitingDelay);
				
				} else {
				switchChangeWord($word, next_Word);
				setTimeout(function(){ headlinehideWord(next_Word) }, animateDelay);
			}
		}
		
		function displyWord($word, $durtime) {
			if($word.parents('.pt-plus-cd-headline').hasClass('type')) {
				displayLetter($word.find('i').eq(0), $word, false, $durtime);
				$word.addClass('is-visible').removeClass('is-hidden');
				
				}  else if($word.parents('.pt-plus-cd-headline').hasClass('clip')) {
				$word.parents('.cd-words-wrapper').animate({ 'width' : $word.width() + 10 }, revealDurationTime, function(){ 
					setTimeout(function(){ headlinehideWord($word) }, revealanimateDelay); 
				});
			}
		}
		
		function hideCharater($letter, $word, $bool, $durtime) {
			$letter.removeClass('in').addClass('out');
			
			if(!$letter.is(':last-child')) {
				setTimeout(function(){ hideCharater($letter.next(), $word, $bool, $durtime); }, $durtime);  
				} else if($bool) { 
				setTimeout(function(){ headlinehideWord(takeNextWord($word)) }, animateDelay);
			}
			
			if($letter.is(':last-child') && jQuery('html').hasClass('no-csstransitions')) {
				var next_Word = takeNextWord($word);
				switchChangeWord($word, next_Word);
			} 
		}
		
		function displayLetter($letter, $word, $bool, $durtime) {
			$letter.addClass('in').removeClass('out');
			
			if(!$letter.is(':last-child')) { 
				setTimeout(function(){ displayLetter($letter.next(), $word, $bool, $durtime); }, $durtime); 
				} else { 
				if($word.parents('.pt-plus-cd-headline').hasClass('type')) { setTimeout(function(){ $word.parents('.cd-words-wrapper').addClass('waiting'); }, 200);}
				if(!$bool) { setTimeout(function(){ headlinehideWord($word) }, animateDelay) }
			}
		}
		
		function takeNextWord($word) {
			return (!$word.is(':last-child')) ? $word.next() : $word.parent().children().eq(0);
		}
		
		function takePrevChild($word) {
			return (!$word.is(':first-child')) ? $word.prev() : $word.parent().children().last();
		}
		
		function switchChangeWord($oldWordText, $newWordText) {
			$oldWordText.removeClass('is-visible').addClass('is-hidden');
			$newWordText.removeClass('is-hidden').addClass('is-visible');
		}
	});
}