( function( $ ) {
	/**
	 * @credit http://indiegamr.com/generate-repeatable-random-numbers-in-js/
	 *
	 * @constructor
	 */
	SeededRandom = function(seed, max, min) {
		this.seed = seed;
		this.max = max;
		this.min = min;
	}

	SeededRandom.prototype.constructor = SeededRandom;
	SeededRandom.prototype.generate = function() {
		max = this.max || 1;
		min = this.min || 0;

		this.seed = (this.seed * 9301 + 49297) % 233280;
		var rnd = this.seed / 233280;

		return min + rnd * (max - min);
	}

	/**
	 * @credit https://css-tricks.com/snippets/jquery/shuffle-dom-elements/
	 * @returns {*|jQuery|HTMLElement}
	 */
	$.fn.shuffleWithSeed = function (seed) {
		var seedRandom = new SeededRandom(seed);

		var allElems = this.get(),
			getRandom = function(max) {
				return Math.floor(seedRandom.generate() * max);
			},
			shuffled = $.map(allElems, function(){
				var random = getRandom(allElems.length),
					randEl = $(allElems[random]).clone(true)[0];
				allElems.splice(random, 1);
				return randEl;
			});

		this.each(function(i){
			$(this).replaceWith($(shuffled[i]));
		});

		return $(shuffled);
	};

	/**
	 * Convert field ID string to number to aide with seeding.
	 *
	 * @param string
	 * @returns number
	 */
	var fieldIDToNumber = function(string) {
		return string
			.split('')
			.map(function(char) {
				return char.charCodeAt(0);
			})
			.reduce(function(accumulator, currentValue) {
				return accumulator + currentValue;
			})
	}

	$.fn.gprRandomizeOrder = function() {
		var seed = $(this).parents('form').find('[name="gpr_seed"]').val();

		if (!seed) {
			console.warn('GP Randomizer: seed not available');
			return;
		}

		this.each(function() {
			/* Do not re-shuffle already shuffled choices. */
			if ($(this).find('.ginput_container').data('gprShuffledOrder')) {
				return;
			}

			/* Add field ID into seed to  */
			seed = seed + fieldIDToNumber($(this).attr('id'));

			/* Drop Downs */
			$(this).find('select option').shuffleWithSeed(seed);

			/* Checkboxes / Radios */
			$(this).find('[class*="gchoice_"]').filter(function (index, el) {
				/**
				 * Do not randomize position of the other choice.
				 */
				return $(el).find('[value="gf_other_choice"]').length === 0;
			}).shuffleWithSeed(seed);

			/* Survey Add-On Rank Fields */
			var $surveyRankUl = $(this).find('ul.gsurvey-rank');

			if ($surveyRankUl.length) {
				var $surveyRankChoices = $surveyRankUl.find('li.gsurvey-rank-choice');
				var surveyItemHiddenInput = $('#' + $surveyRankUl.attr('id') + '-hidden');
				var defaultOrder = $surveyRankUl.closest('.gsurvey-survey-field').data('default-order');

				if (defaultOrder.join(',') === surveyItemHiddenInput.val()) {
					$surveyRankChoices.shuffleWithSeed(seed);

					if (typeof gsurveyRankUpdateRank === 'function') {
						gsurveyRankUpdateRank($surveyRankUl[0]);
					}
				}
			}

			/* Set data to prevent reshuffle later */
			$(this).find('.ginput_container')
				.addClass('gpr-shuffled')
				.data('gprShuffledOrder', true);
		});
	};

	window.GPRandomizer = function(formId) {
		var self = this;
		this.formId = formId;

		this.randomizeChoiceOrder = function() {
			$( '#gform_wrapper_' + self.formId ).find('.gpr_randomize_field').gprRandomizeOrder();
		}

		this.bind = function() {
			/**
			 * Immediately randomize choice order.
			 */
			self.randomizeChoiceOrder();

			/**
			 * Listen for when Populate Anything updates fields and re-randomize choice order.
			 */
			$(document).on('gppa_updated_batch_fields', self.randomizeChoiceOrder);
		}

		this.bind();
	}
})(jQuery);
