jQuery(function($) {

	var LS_GoogleFontsAPI = {

		results : 0,
		fontName : null,
		fontIndex : null,

		init : function() {

			// Prefetch fonts
			$('.ls-font-search input').focus(function() {
				LS_GoogleFontsAPI.getFonts();
			});

			// Search
			$('.ls-font-search > button').click(function(e) {
				e.preventDefault();
				var input = $(this).prev()[0];
				LS_GoogleFontsAPI.timeout = setTimeout(function() {
					LS_GoogleFontsAPI.search(input);
				}, 500);
			});

			$('.ls-font-search input').keydown(function(e) {
				if(e.which === 13) {
					e.preventDefault();
					var input = this;
					LS_GoogleFontsAPI.timeout = setTimeout(function() {
						LS_GoogleFontsAPI.search(input);
					}, 500);
				}
			});

			// Form save
			$('form.ls-google-fonts').submit(function() {
				$('ul.ls-font-list li', this).each(function(idx) {
					$('input', this).each(function() {
						$(this).attr('name', 'fontsData['+idx+']['+$(this).data('name')+']');
					});
				});

				return true;
			});

			// Select font
			$('.ls-google-fonts .fonts').on('click', 'li:not(.unselectable)', function() {
				LS_GoogleFontsAPI.showVariants(this);
			});

			// Add font event
			$('.ls-font-search').on('click', 'button.add-font', function(e) {
				e.preventDefault();
				LS_GoogleFontsAPI.addFonts(this);
			});

			// Back to results event
			$('.ls-google-fonts .variants').on('click', 'button:last', function(e) {
				e.preventDefault();
				LS_GoogleFontsAPI.showFonts(this);
			});

			// Close event
			$(document).on( 'click', '.ls-overlay', function() {

				if($(this).data('manualclose')) {
					return false;
				}

				if($('.ls-pointer').length) {
					$(this).remove();
					$('.ls-pointer').children('div.fonts').show().next().hide();
					$('.ls-pointer').animate({ marginTop : 40, opacity : 0 }, 150, function() {
						this.style.display = 'none';
					});
				}
			});

			// Remove font
			$('.ls-font-list').on('click', 'a.remove', function(e) {
				e.preventDefault();
				$(this).parent().animate({ height : 0, opacity : 0 }, 300, function() {

					// Add notice if needed
					if($(this).siblings().length < 2) {
						$(this).parent().append(
							$('<li>', { 'class' : 'ls-notice', 'text' : LS_l10n.GFEmptyList })
						);
					}

					$(this).remove();
				});
			});

			// Add script
			$('.ls-google-fonts .footer select').change(function() {

				// Prevent adding the placeholder option tag
				if($('option:selected', this).index() !== 0) {

					// Selected item
					var item = $('option:selected', this);
					var hasDuplicate = false;

					// Prevent adding duplicates
					$('.ls-google-font-scripts input').each(function() {
						if($(this).val() === item.val()) {
							hasDuplicate = true;
							return false;
						}
					});

					// Add item
					if(!hasDuplicate) {
						var clone = $('.ls-google-font-scripts li:first').clone();
							clone.find('span').text( item.text() );
							clone.find('input').val( item.val() );
							clone.removeClass('ls-hidden').appendTo('.ls-google-font-scripts');
					}

					// Show the placeholder option tag
					$('option:first', this).prop('selected', true);
				}
			});

			// Remove script
			$('.ls-google-font-scripts').on('click', 'li a', function(event) {
				event.preventDefault();

				if($('.ls-google-font-scripts li').length > 2) {
					$(this).closest('li').remove();
				} else {
					alert(LS_l10n.GFEmptyCharset);
				}
			});
		},

		getFonts : function() {

			if(LS_GoogleFontsAPI.results == 0) {
				var API_KEY = 'AIzaSyC_iL-1h1jz_StV_vMbVtVfh3h2QjVUZ8c';
				$.getJSON('https://www.googleapis.com/webfonts/v1/webfonts?key=' + API_KEY, function(data) {
					LS_GoogleFontsAPI.results = data;
				});
			}
		},

		search : function(input) {

			// Hide overlay if any
			$('.ls-overlay').remove();

			// Get search field
			var searchValue = $(input).val().toLowerCase();

			// Wait until fonts being fetched
			if(LS_GoogleFontsAPI.results != 0 && searchValue.length > 2 ) {

				// Search
				var indexes = [];
				var found = $.grep(LS_GoogleFontsAPI.results.items, function(obj, index) {
					if(obj.family.toLowerCase().indexOf(searchValue) !== -1) {
						indexes.push(index);
						return true;
					}
				});

				// Get list
				var list = $('.ls-font-search .ls-pointer .fonts ul');

				// Remove previous contents and append new ones
				list.empty();
				if(found.length) {
					for(c = 0; c < found.length; c++) {
						list.append( $('<li>', { 'data-key' : indexes[c], 'text' : found[c]['family'] }));
					}
				} else {
					list.append($('<li>', { 'class' : 'unselectable' })
						.append( $('<h4>', { 'text' : 'No results were found' }))
					);
				}

				// Show pointer and append overlay
				$('.ls-font-search .ls-pointer').show().animate({ marginTop : 15, opacity : 1 }, 150);
				$('<div>', { 'class' : 'ls-overlay dim'}).prependTo('body');
			}
		},

		showVariants : function(li) {

			// Get selected font
			var fontName = $(li).text();
			var fontIndex = $(li).data('key');
			var fontObject = LS_GoogleFontsAPI.results.items[fontIndex]['variants'];
			LS_GoogleFontsAPI.fontName = fontName;
			LS_GoogleFontsAPI.fontIndex = fontIndex;

			// Get and empty list
			var list = $(li).closest('div').next().children('ul');
				list.empty();


			// Change header
			var title = LS_l10n.GFFontVariant.replace('%s', fontName);
			$(li).closest('.ls-box').children('.header').text(title);

			// Append variants
			for(c = 0; c < fontObject.length; c++) {
				list.append( $('<li>', { 'class' : 'unselectable' })
					.append( $('<input>', { 'type' : 'checkbox'} ))
					.append( $('<span>', { 'text' : ucFirst(fontObject[c]) }))
				);
			}

			// Init checkboxes
			list.find(':checkbox').customCheckbox();

			// Show variants
			$(li).closest('.fonts').hide().next().show();
		},

		showFonts : function(button) {
			$(button).closest('.ls-box').children('.header').text(LS_l10n.GFFontFamily);
			$(button).closest('.variants').hide().prev().show();
		},

		addFonts: function(button) {

			// Get variants
			var variants = $(button).parent().prev().find('input:checked');

			var apiUrl = [];
			var urlVariants = [];
			apiUrl.push(LS_GoogleFontsAPI.fontName.replace(/ /g, '+'));

			if(variants.length) {
				apiUrl.push(':');
				variants.each(function() {
					urlVariants.push( $(this).siblings('span').text().toLowerCase() );
				});
				apiUrl.push(urlVariants.join(','));
			}

			LS_GoogleFontsAPI.appendToFontList( apiUrl.join('') );
		},

		appendToFontList : function(url) {

			// Empty notice if any
			$('ul.ls-font-list li.ls-notice').remove();

			var index = $('ul.ls-font-list li').length - 1;

			// Append list item
			var item = $('ul.ls-font-list li.ls-hidden').clone();
				item.children('input:text').val(url);
				item.appendTo('ul.ls-font-list').attr('class', '');

			// Reset search field
			$('.ls-font-search input').val('');

			// Close pointer
			$('.ls-overlay').click();
		}
	};


	kmUI.popover.init();

	// Tabs
	$('.km-tabs').kmTabs();

	// Checkboxes
	$('.ls-global-settings :checkbox').customCheckbox();
	$('.ls-google-fonts :checkbox').customCheckbox();


	// Google Fonts API
	LS_GoogleFontsAPI.init();


	// Close add slider window
	$(document).on( 'click', '.ls-overlay', function() {

		if($(this).data('manualclose')) {
			return false;
		}

		if($('.ls-pointer').length) {
			$('.ls-overlay').remove();
			$('.ls-pointer').animate({ marginTop : 40, opacity : 0 }, 150);
		}
	});


	// Permission form
	$('#ls-permission-form').submit(function(e) {
		e.preventDefault();
		if(confirm(LS_l10n.SLPermissions)) {
			this.submit();
		}
	});

	$('#ls-privacy-form .ls-checkbox').click(function(e) {

		var $this 		= $(this),
			warningText = $this.parent().data('warning');

		if( $this.hasClass('on') && warningText ) {
			if( ! confirm( warningText ) ) {
				e.preventDefault();
				return false;
			}
		}
	});


	// Google CDN version warning
	$('#ls_use_custom_jquery').on('click', '.ls-checkbox', function(e) {
		if( $(this).hasClass('off') ) {
			if( ! confirm(LS_l10n.SLJQueryConfirm) ) {
				e.preventDefault();
				return false;

			}

			alert(LS_l10n.SLJQueryReminder);
		}
	});
});