jQuery( document ).ready(function( $ ) {

	$('.ls-about-privacy :checkbox').customCheckbox();


	$('.ls-about-privacy').submit( function( event ) {

		event.preventDefault();
		$.post( ajaxurl, $( this ).serialize() );
	});


	$('.ls-about-privacy :checkbox').change( function() {
		$('.ls-about-privacy').submit();
	});
});