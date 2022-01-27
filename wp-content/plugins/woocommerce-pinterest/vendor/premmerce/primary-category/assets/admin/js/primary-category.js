jQuery(function ($) {
	const $categoryMetaBox = $('#product_catdiv');
	const $primaryCategoryHiddenField = $('#' + premmerceSettings.categoryIdFieldName);
	const $categoryCheckboxes = $categoryMetaBox.find('input');
	const makePrimaryClassName = 'premmerce-make-primary';
	const makePrimaryAnchorClassName = 'premmerce-make-primary-button';
	const primaryLabelClassName = 'premmerce-primary-category';
	const primarySpanClassName = 'premmerce-primary-mark';
	const $makePrimaryButtonPrototype = $('<a href="#" class="' + makePrimaryAnchorClassName + '" >' + premmerceSettings.makePrimaryText + '</a>');
	const $primarySpanPrototype = $('<span class="' + primarySpanClassName +'">' + premmerceSettings.primarySpanText + '</span>');
	
	let primaryCategoryId = premmerceSettings.mainCategoryId;

	$categoryCheckboxes.each(function (index, checkbox) {
		const $checkbox = $(checkbox);
		$checkbox.parent('label').append($makePrimaryButtonPrototype.clone().hide()).append($primarySpanPrototype.clone('hide'));
	});

	$('.' + makePrimaryAnchorClassName).on('click', function(e){
		e.preventDefault();
		updatePrimaryCategoryIdByClick(e);
		updatePrimaryCategoryField();
		updateCheckboxes();

	});

	updateCheckboxes();

	$categoryCheckboxes.on('change', function () {
		updateCheckboxes();
	});

	function updateCheckboxes()
	{
		$categoryCheckboxes.each(function(index, checkbox){
			const $checkbox = $(checkbox);
			const $label  = $checkbox.parent('label');
			const $makePrimaryAnchor = $label.find('.' + makePrimaryAnchorClassName);
			const $primaryMarkSpan = $label.find('.' + primarySpanClassName);


			if($checkbox.val() === primaryCategoryId){
				$label.addClass(primaryLabelClassName + " " + primaryLabelClassName);
				$makePrimaryAnchor.hide();
				$primaryMarkSpan.show();
			}
			else if($checkbox.is(':checked')){
				$label.addClass(makePrimaryClassName).removeClass(primaryLabelClassName);
				$makePrimaryAnchor.show();
				$primaryMarkSpan.hide();
			}
			else {
				$label.removeClass(makePrimaryClassName).removeClass(primaryLabelClassName);
				$makePrimaryAnchor.hide();
				$primaryMarkSpan.hide();
			}

		});
	}

	function updatePrimaryCategoryIdByClick(clickEvent)
	{
		const $anchor = $(clickEvent.target);
		const $label = $anchor.parent('label');

		primaryCategoryId = $label.find('input[type=checkbox]').val();
	}

	function updatePrimaryCategoryField(){
		$primaryCategoryHiddenField.val(primaryCategoryId);
	}

});
