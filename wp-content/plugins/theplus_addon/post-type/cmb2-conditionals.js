jQuery( document ).ready( function( $ ) {
	'use strict';
	$( 'input[data-conditional-id]' ).each(function () {
	var $this=$(this);
	var ids=$(this).data('conditional-id');
	var values=$(this).data('conditional-value');
		$( "#"+ids ).change(function () {
		
		$( "#"+ids+" option:selected" ).each(function() {
		  var value = $( this ).val();
			if(value==values){
				$this.parent('td').parent('tr').removeClass("hidden").addClass("show");
			}else{
				$this.parent('td').parent('tr').removeClass("show").addClass("hidden");
			}
		});
		
	  })
	  .change();
	});
});
